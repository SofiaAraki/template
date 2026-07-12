<?php
class ConteudoProgramaticoListAll extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_ConteudoProgramatico');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('Conteudo Programático');

        // create the form fields
        $id = new TEntry('id');
        $curso = new TEntry('curso');
        $disciplina = new TEntry('disciplina');
        $etapa = new TEntry('etapa');
        $turma = new TEntry('turma');
        $data_reg = new TEntry('data_reg');
        $system_user_id = new TEntry('system_user_id');

        // add the fields
        //$this->form->addQuickField('Id', $id, '100%');
        $this->form->addQuickField('Curso', $curso, '100%');
        //$this->form->addQuickField('Disciplina', $disciplina, '100%');
        $this->form->addQuickField('Etapa', $etapa,  '100%');
        //$this->form->addQuickField('Turma', $turma, '100%');
        //$this->form->addQuickField('Data Reg', $data_reg, '100%');
        //$this->form->addQuickField('System User Id', $system_user_id, '100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ConteudoProgramatico_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fas:search blue');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Lançado por', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_system_user_id);
        
        // create EDIT action
        $action_view = new TDataGridAction(array('ConteudoProgramaticoFormView', 'onShow'));
        $action_view->setLabel(('Editar'));
        $action_view->setImage('fa:search');
        $action_view->setField('id');
        $this->datagrid->addAction($action_view);

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Conteúdo Programático - Listagem Geral', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('ConteudoProgramaticoList_filter_id', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_curso', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_disciplina', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_etapa', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_turma', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_status', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_data_reg', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_system_user_id', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%");
            TSession::setValue('ConteudoProgramaticoList_filter_id', $filter); 
        }


        if (isset($data->curso) AND ($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_curso', $filter);
        }


        if (isset($data->disciplina) AND ($data->disciplina)) {
            $filter = new TFilter('disciplina', 'like', "%{$data->disciplina}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_disciplina', $filter); 
        }


        if (isset($data->etapa) AND ($data->etapa)) {
            $filter = new TFilter('etapa', 'like', "%{$data->etapa}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_etapa', $filter); 
        }


        if (isset($data->turma) AND ($data->turma)) {
            $filter = new TFilter('turma', 'like', "%{$data->turma}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_turma', $filter);
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_status', $filter);
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_data_reg', $filter);
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', 'like', "%{$data->system_user_id}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_system_user_id', $filter); 
        }

       
        $this->form->setData($data);
        
        TSession::setValue('ConteudoProgramatico_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $loggedUnit = TSession::getValue('userunitid');

            $repository = new TRepository('ConteudoProgramatico');
            $limit = 10;

            $criteria = new TCriteria;
            

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ConteudoProgramaticoList_filter_id')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_id')); 
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_curso')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_curso')); 
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_disciplina')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_disciplina'));
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_etapa')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_etapa'));
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_turma')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_turma'));
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_status')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_status'));
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_data_reg')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_data_reg'));
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_system_user_id'));
            }


            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->data_reg = TDate::date2br($object->data_reg);


                    TTransaction::open('dados_fei');

                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));

                    $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria2);
                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;

                    TTransaction::close();
                    

                    if($disciplinaNome[0]->CodEntidade == $loggedUnit) //SE REGISTRO PERTENCE A UNIDADE DO USUÁRIO LOGADO
                    {
                        $this->datagrid->addItem($object);
                    }
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 
            
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
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        
        parent::show();
    }
}
