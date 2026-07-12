<?php

class HorarioCoordenadorList extends TPage
{
    protected $form;     
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_search_HorarioCoordenador');
        $this->form->setFormTitle('Painel de Controle de Horários Semestrais');
        
        $nome_horario = new TEntry('nome_horario');
        $nome_horario->placeholder = 'Digite o nome do horario para filtrar...';
        $this->form->addFields( [new TLabel('Nome do Horário:')], [$nome_horario] );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Limpar Filtros',  new TAction([$this, 'clear']), 'fa:eraser red');
        $this->form->addActionLink('Novo Horário',   new TAction(['HorarioCoordenadorForm', '__construct']), 'fa:calendar-plus green');
        
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = "100%";
        
        $col_horario = new TDataGridColumn('nome_horario', 'Nome do Horário', 'left', '20%');
        $col_curso    = new TDataGridColumn('curso', 'Curso', 'center', '20%');
        $col_etapa    = new TDataGridColumn('etapa', 'Etapa', 'center', '5%');
        $col_periodo = new TDataGridColumn('periodo', 'Período', 'center', '10%');
        $col_ano_semestre = new TDataGridColumn('ano_semestre', 'Ano/Semestre', 'center', '5%');
        $col_user    = new TDataGridColumn('ultimo_usuario', 'Modificado por', 'center', '20%');
        $col_data    = new TDataGridColumn('ultima_alteracao', 'Data Modificação', 'center', '10%');
        
        // Formata data nativamente no Grid
        $col_data->setTransformer(function($value){
            return !empty($value) ? date('d/m/Y H:i', strtotime($value)) : '-';
        });

        $this->datagrid->addColumn($col_horario);
        $this->datagrid->addColumn($col_curso);
        $this->datagrid->addColumn($col_etapa);
        $this->datagrid->addColumn($col_periodo);
        $this->datagrid->addColumn($col_ano_semestre);
        $this->datagrid->addColumn($col_user);
        $this->datagrid->addColumn($col_data);
        
        $action_edit   = new TDataGridAction(['HorarioCoordenadorForm', 'onEdit']);
        $action_print  = new TDataGridAction(['HorarioCoordenadorReport', 'onGenerate']);
        $action_delete = new TDataGridAction([$this, 'onDeletarGradeCompleta']); 
        
        $action_edit->setField('nome_horario');
        $action_print->setField('nome_horario');
        $action_delete->setField('nome_horario');
        
        $action_edit->setParameter('nome_horario', '{nome_horario}');
        $action_print->setParameter('nome_horario', '{nome_horario}');
        $action_delete->setParameter('nome_horario', '{nome_horario}');
        
        $this->datagrid->addAction($action_edit, 'Editar Horário', 'far:edit blue');
        $this->datagrid->addAction($action_print, 'Imprimir Horário Oficial (HTML)', 'fa:print purple');
        $this->datagrid->addAction($action_delete, 'Excluir Horário', 'far:trash-alt red');
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($vbox);
    }
    
    public function onSearch()
    {
        $data = $this->form->getData();
        TSession::setValue(__CLASS__.'_filter_data', $data);
        $this->onReload();
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('ViewHorarioCoordenador');
            $criteria   = new TCriteria;
            
            $limit = isset($param['limit']) ? $param['limit'] : 10;
            $offset = isset($param['offset']) ? $param['offset'] : 0;
            
            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('offset', $offset);
            
            $current_user = TSession::getValue('username') ?? null;
            $criteria->add(new TFilter('ultimo_usuario', '=', $current_user));

            $filter_data = TSession::getValue(__CLASS__.'_filter_data');
            if (!empty($filter_data->nome_horario))
            {
                $criteria->add(new TFilter('nome_horario', 'like', "%{$filter_data->nome_horario}%"));
            }
            
            $criteria->setProperty('order', 'nome_horario');
            
            $objects = $repository->load($criteria, FALSE);
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $item = new StdClass;
                    $item->nome_horario     = $object->nome_horario;
                    $item->curso             = $object->curso;
                    $item->etapa             = $object->etapa;
                    $item->periodo          = $object->periodo;
                    $item->ano_semestre     = $object->ano_semestre;
                    $item->ultimo_usuario   = $object->ultimo_usuario;
                    $item->ultima_alteracao = $object->ultima_alteracao;
                    
                    $this->datagrid->addItem($item);
                }
            }
            
            $criteria_count = clone $criteria;
            $criteria_count->setProperty('limit', null);
            $criteria_count->setProperty('offset', null);
            $criteria_count->setProperty('order', null); 
            
            $count = $repository->count($criteria_count);
            
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            $this->pageNavigation->setCount($count);
            
            TTransaction::close();
            $this->loaded = TRUE;
        }
        catch (Exception $e)
        {
            new TMessage('error', 'Erro ao carregar listagem: ' . $e->getMessage());
            try { TTransaction::rollback(); } catch(Exception $ex){}
        }
    }
    
    public function clear()
    {
        TSession::setValue(__CLASS__.'_filter_data', null);
        $this->form->clear();
        $this->onReload();
    }

    public function onDeletarGradeCompleta($param)
    {
        if (empty($param['nome_horario'])) return;

        $action_confirma = new TAction([$this, 'onDeleteConfirmado'], $param);
        new TQuestion("Deseja realmente excluir TODO o horário da turma '{$param['nome_horario']}'?", $action_confirma);
    }

    public function onDeleteConfirmado($param)
    {
        try {
            $conn = TTransaction::open('Felabs_DB');
            $statement = $conn->prepare("DELETE FROM horario_coordenador WHERE nome_horario = :cod_turma AND usuario_horario_coordenador = :username");
            $statement->execute([
                ':cod_turma' => $param['nome_horario'],
                ':username' => TSession::getValue('username')
            ]);
            TTransaction::close();
            
            new TMessage('info', 'Grade horária excluída com sucesso.');
            $this->onReload(); 
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            try { TTransaction::rollback(); } catch(Exception $ex){}
        }
    }

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload'))
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}