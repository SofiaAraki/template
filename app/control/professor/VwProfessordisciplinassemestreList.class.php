<?php
class VwProfessordisciplinassemestreList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct($param)
    {
        parent::__construct();         

        // Restaurado para TQuickForm compatível com a versão 8.5
        $this->form = new TQuickForm('form_search_VwProfessordisciplinassemestre');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('VwProfessordisciplinassemestre');
        
        // fields
        $NomeCurso = new TEntry('NomeCurso');
        $NomeDisciplina = new TEntry('NomeDisciplina');

        // add fields
        $this->form->addQuickField('Nome do Curso', $NomeCurso, '100%');
        $this->form->addQuickField('Nome da Disciplina', $NomeDisciplina, '100%');

        // keep form filled
        $this->form->setData( TSession::getValue('VwProfessordisciplinassemestre_filter_data') );

        // actions
        $btn = $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // Columns
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left');
        $column_Etapa = new TDataGridColumn('Etapa', 'Etapa', 'center');
        $column_Identificacao = new TDataGridColumn('Identificacao', 'Identificacao', 'center');
        $column_Periodo = new TDataGridColumn('Periodo', 'Período', 'center');

        // Add columns
        $this->datagrid->addColumn($column_NomeCurso);
        $this->datagrid->addColumn($column_NomeDisciplina);
        $this->datagrid->addColumn($column_Etapa);
        $this->datagrid->addColumn($column_Identificacao);
        $this->datagrid->addColumn($column_Periodo);
        
        // Actions
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(FALSE);
        $action_select->setButtonClass('btn btn-default');
        $action_select->setLabel('Selecionar');
        $action_select->setImage('fas:check-circle blue');
        $action_select->setField('CodComposto');
        $this->datagrid->addAction($action_select);

        $action_papeleta = new TDataGridAction(array($this, 'onPapeleta'));
        $action_papeleta->setUseButton(FALSE);
        $action_papeleta->setButtonClass('btn btn-default');
        $action_papeleta->setLabel('Papeleta');
        $action_papeleta->setImage('fas:file-pdf red');
        $action_papeleta->setField('CodComposto');

        $this->datagrid->addAction($action_papeleta);

        $this->datagrid->createModel();

        // Container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ApontamentoBimestral'));
        $container->add(TPanelGroup::pack('Buscar Disciplina Por:', $this->form));
        $container->add(TPanelGroup::pack('Minhas Disciplinas', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeProfessor', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Etapa', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina', NULL);

        if (isset($data->NomeCurso) AND ($data->NomeCurso)) {
            $filter = new TFilter('NomeCurso', 'like', "%{$data->NomeCurso}%"); 
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso', $filter);
        }

        if (isset($data->NomeDisciplina) AND ($data->NomeDisciplina)) {
            $filter = new TFilter('NomeDisciplina', 'like', "%{$data->NomeDisciplina}%"); 
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina', $filter); 
        }

        $this->form->setData($data);
        TSession::setValue('VwProfessordisciplinassemestre_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }

    public function onSelect($param)
    {
        $key = $param['key'];
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->CodComposto)
            {
                $dados = array(
                    'CodTurmaetapa'                  => $object->CodTurmaetapa,
                    'CodDisciplina'                  => $object->CodDisciplina,
                    'CodGradeDisciplinaEtapa_Frente' => $object->CodGradeDisciplinaEtapaFrente,
                    'NomeDisciplina'                 => $object->NomeDisciplina,
                    'Periodoturma'                   => $object->Periodo,
                    'NomeCurso'                      => $object->NomeCurso,
                    'Etapa'                          => $object->Etapa,
                    'Identificacao'                  => $object->Identificacao,
                    'NomeEntidade'                   => $object->NomeEntidade ?? '',
                    'NomeProfessor'                  => $object->NomeProfessor ?? '',
                    'CodProfessor'                   => $object->CodProfessor ?? $object->Codprofessor
                );

                TSession::setValue('sessao_papeleta_unificada', $dados);
            }
        }
        TApplication::loadPage('VwAlunosnotasList');
    }

    public function onPapeleta($param)
    {
        $key = $param['key'];
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->CodComposto)
            {
                $dados = array(
                    'CodTurmaetapa'                  => $object->CodTurmaetapa,
                    'CodDisciplina'                  => $object->CodDisciplina,
                    'CodGradeDisciplinaEtapa_Frente' => $object->CodGradeDisciplinaEtapaFrente,
                    'NomeDisciplina'                 => $object->NomeDisciplina,
                    'Periodoturma'                   => $object->Periodo,
                    'NomeCurso'                      => $object->NomeCurso,
                    'Etapa'                          => $object->Etapa,
                    'Identificacao'                  => $object->Identificacao,
                    'NomeEntidade'                   => $object->NomeEntidade ?? '',
                    'NomeProfessor'                  => $object->NomeProfessor ?? '',
                    'CodProfessor'                   => $object->CodProfessor ?? $object->Codprofessor
                );

                TSession::setValue('sessao_papeleta_unificada', $dados);
            }
        }     
        TApplication::loadPage('VwPapeletaReport');
    }

    public function onReload($param = NULL)
    {
        try
        {   
            $sessao_bimestre = TSession::getValue('sessao_bimestre');

            $Ano = $sessao_bimestre["Ano"];
            $Semestre = $sessao_bimestre["Semestre"];
            $Entidade = $sessao_bimestre["Entidade"];

            TTransaction::open('Felabs_DB');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            TTransaction::close();

            TTransaction::open('dados_fei');
            
            $repository = new TRepository('VwProfessordisciplinassemestre');
            $limit = 50;
            
            // AJUSTE: Removida a chamada à classe Util que causava a primeira Exceção

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodProfessor', '=', $user->systemuser_codlegado));
            $criteria->add(new TFilter('Ano', '=', $Ano), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Semestre', '=', $Semestre), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('CodEntidade', '=',$Entidade), TExpression::AND_OPERATOR);
            
            if (empty($param['order']))
            {
                $param['order'] = 'NomeDisciplina';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso'));
            }

            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')); 
            }

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'], array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0) {
                $this->onReload( func_get_arg(0) );
            } else {
                $this->onReload();
            }
        }
        parent::show();
    }
}