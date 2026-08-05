<?php
class EquivalenciaList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_Equivalencia');
        $this->form->setFormTitle('<h4>Consultar Equivalências Realizadas</h4>');
    
        // Campo alterado para TEntry
        $nome_aluno = new TEntry('nome_aluno');
        $nome_aluno->setSize('80%');

        $grade_id = new TDBCombo('grade_id', 'Felabs_DB', 'CurriculoDigital', 'cod_grade', 'Grade: ({cod_grade}) - {fi_grade_curso_descricao->Descricao} - Curso: {diploma_digital_curso->nome_curso_sistema}');
        $grade_id->setSize('80%');

        $this->form->addFields( [ new TLabel('Nome do Aluno') ], [ $nome_aluno ] );
        $this->form->addFields( [ new TLabel('Grade / Currículo') ], [ $grade_id ] );

        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // Ações da listagem
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Novo Registro', new TAction([$this, 'onNovoRegistro']), 'fa:plus green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $this->datagrid->addColumn(new TDataGridColumn('nome_aluno', 'Aluno', 'left', '25%'));
        $this->datagrid->addColumn(new TDataGridColumn('grade_id', 'Grade', 'center', '20%'));
        $this->datagrid->addColumn(new TDataGridColumn('nome_curso', 'Curso', 'left', '25%'));
        $this->datagrid->addColumn(new TDataGridColumn('total_disciplinas_aproveitadas', 'Matérias Aproveitadas', 'center', '15%'));
        $this->datagrid->addColumn(new TDataGridColumn('nome_ultimo_usuario', 'Responsável', 'left', '15%'));

        // Ação para abrir o Formulário do respectivo Aluno e Grade
        $action_edit = new TDataGridAction(['EquivalenciaForm', 'onCarregaDisciplinas']);
        $action_edit->setLabel('Gerenciar Matérias');
        $action_edit->setImage('fa:edit blue');
        $action_edit->setFields(['nome_aluno', 'grade_id']);
        $this->datagrid->addAction($action_edit);

        // Ação para imprimir o relatório
        $action_print = new TDataGridAction(['EquivalenciaReport', 'onGenerate']);
        $action_print->setLabel('Imprimir Relatório');
        $action_print->setImage('far:file-pdf red');
        $action_print->setFields(['nome_aluno', 'grade_id', 'nome_curso']);
        $this->datagrid->addAction($action_print);

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        parent::add($vbox);
        $this->onReload();
    }

    public function onSearch($param = null)
    {
        $data = $this->form->getData();

        $this->form->setData($data);

        TSession::setValue(__CLASS__.'_filter_data', $data);

        $this->onReload(['offset' => 0]);
    }

    public function onNovoRegistro($param)
    {
        $data = $this->form->getData();
        if (empty($data->grade_id) || empty($data->nome_aluno)) {
            new TMessage('warning', 'Preencha o Nome do Aluno e selecione uma Grade Curricular antes de iniciar um Novo Registro.');
            return;
        }

        TApplication::loadPage('EquivalenciaForm', 'onCarregaDisciplinas', [
            'nome_aluno' => $data->nome_aluno,
            'grade_id'  => $data->grade_id
        ]);
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $limit = 10;
            $offset = isset($param['offset']) ? $param['offset'] : 0;

            $repository = new TRepository('ViewEquivalencia');
            $criteria = new TCriteria;

            $loggedUserId = TSession::getValue('userid');
            $criteria->add(new TFilter('ultimo_system_user_id', '=', $loggedUserId));
            
            // Filtros atualizados
            $data = TSession::getValue(__CLASS__.'_filter_data');
            if ($data) {
                if (!empty($data->nome_aluno)) {
                    $criteria->add(new TFilter('nome_aluno', 'like', "%{$data->nome_aluno}%"));
                }
                if (!empty($data->grade_id)) {
                    $criteria->add(new TFilter('grade_id', '=', $data->grade_id));
                }
            }

            if (empty($param['order'])) {
                $criteria->setProperty('order', 'ultima_atualizacao');
                $criteria->setProperty('direction', 'desc');
            }

            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('offset', $offset);

            $objects = $repository->load($criteria, FALSE);
            $this->datagrid->clear();
            
            if ($objects) {
                foreach ($objects as $object) {
                    $item = new StdClass;
                    $item->nome_aluno = $object->nome_aluno ?? 'Não identificado';
                    $item->grade_id = $object->grade_id;
                    $item->nome_curso = !empty($object->nome_curso) ? $object->nome_curso : "Curso (Grade {$object->grade_id})";
                    $item->total_disciplinas_aproveitadas = $object->total_disciplinas_aproveitadas;
                    $item->nome_ultimo_usuario = $object->nome_ultimo_usuario ?? 'Não identificado';
                    
                    $this->datagrid->addItem($item);
                }
            }

            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = TRUE;
        }
        catch (Exception $e)
        {
            new TMessage('error', 'Erro ao ler dados agrupados: ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}