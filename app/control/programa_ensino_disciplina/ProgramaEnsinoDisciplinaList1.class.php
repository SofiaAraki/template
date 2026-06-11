<?php

class ProgramaEnsinoDisciplinaList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new TQuickForm('form_search_ProgramaEnsinoDisciplina');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('Programa de Ensino da Disciplina');
        

        // create the form fields
        $curso = new TEntry('curso');
        $disciplina = new TEntry('disciplina');


        // add the fields
        //$this->form->addQuickField('Curso', $curso, '50%');
        //$this->form->addQuickField('Disciplina', $disciplina, '50%');
        
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ProgramaEnsinoDisciplina_filter_data') );
        
        
        // add the search form actions
        //$btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(('Cadastrar Plano'),  new TAction(array('ProgramaEnsinoDisciplinaForm', 'onEdit')), 'bs:plus-sign green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Professor', 'left');
        //$column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        //$column_codigo = new TDataGridColumn('codigo', 'Codigo', 'left');
        //$column_obg_optativa = new TDataGridColumn('obg_optativa', 'Obg Optativa', 'left');
        //$column_periodo = new TDataGridColumn('periodo', 'Periodo', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        //$this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        //$this->datagrid->addColumn($column_codigo);
        //$this->datagrid->addColumn($column_obg_optativa);
        //$this->datagrid->addColumn($column_periodo);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_reg);


        // create EDIT action
        $action_pdf = new TDataGridAction(array('ProgramaEnsinoDisciplinaFormView', 'onShow'));
        $action_pdf->setButtonClass('btn btn-default btn-sm');
        $action_pdf->setLabel('Visualizar');
        $action_pdf->setImage('fa:search #478fca');
        $action_pdf->setField('id');
        $this->datagrid->addAction($action_pdf);
        
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ProgramaEnsinoDisciplinaForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Listagem - Programa de Ensino da Disciplina', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new ProgramaEnsinoDisciplina($key); 
            $object->{$field} = $value;
            $object->store(); 
            
            TTransaction::close(); 
            
            $this->onReload($param); 
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    

    public function onSearch()
    {
        $data = $this->form->getData();
        

        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_curso', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_disciplina', NULL);
        //TSession::setValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_periodo', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_data_reg', NULL);

        if (isset($data->curso) AND ($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_curso', $filter);
        }


        if (isset($data->disciplina) AND ($data->disciplina)) {
            $filter = new TFilter('disciplina', 'like', "%{$data->disciplina}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_disciplina', $filter); 
        }

        /**
        if (isset($data->obg_optativa) AND ($data->obg_optativa)) {
            $filter = new TFilter('obg_optativa', 'like', "%{$data->obg_optativa}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa', $filter); 
        }
        */

        if (isset($data->periodo) AND ($data->periodo)) {
            $filter = new TFilter('periodo', 'like', "%{$data->periodo}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_periodo', $filter);
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_data_reg', $filter);
        }

        
        $this->form->setData($data);
        
        
        TSession::setValue('ProgramaEnsinoDisciplina_filter_data', $data);
        
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
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $loggedUnit = TSession::getValue('userunitid');
            

            $repository = new TRepository('ProgramaEnsinoDisciplina');
            $limit = 10;
           
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            
           
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_curso')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_curso')); 
            }


            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_disciplina')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_disciplina')); 
            }


            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa')); 
            }


            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_periodo')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_periodo')); 
            }


            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_data_reg')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_data_reg')); 
            }

            
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    TTransaction::open('dados_fei');

                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
                    //var_dump($object->disciplina);
                    //die;

                    $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria2);
                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;

                    TTransaction::close();

                    $object->data_reg = TDate::date2br($object->data_reg);
                    
                    $this->datagrid->addItem($object);
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


    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new ProgramaEnsinoDisciplina($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            
            $this->onReload( $param ); 
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); 
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
