<?php

class ConteudoProgramaticoList extends TPage
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


        if(TSession::getValue('userunitid') == 3)
        {
            new TMessage('error','Funcionalidade não disponível para FAFRAM');
            die;
        }
        
        
        // creates the form
        $this->form = new TQuickForm('form_search_ConteudoProgramatico');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('ConteudoProgramatico');
        

        // create the form fields
        $usuario = new TEntry('usuario');
        $curso = new TEntry('curso');
        $disciplina = new TEntry('disciplina');
        $etapa = new TEntry('etapa');
        $turma = new TEntry('turma');
        $data_reg = new TEntry('data_reg');
        $id = new TEntry('id');
        $status = new TEntry('status');


        // add the fields
        //$this->form->addQuickField('Usuario', $usuario,'100%');
        $this->form->addQuickField('Curso', $curso, '100%');
        //$this->form->addQuickField('Disciplina', $disciplina, '100%');
        $this->form->addQuickField('Etapa', $etapa, '100%');
        //$this->form->addQuickField('Turma', $turma, '100%');
        //$this->form->addQuickField('Data Reg', $data_reg, '100%');
        //$this->form->addQuickField('Id', $id, '100%');
        //$this->form->addQuickField('Status', $status, '100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ConteudoProgramatico_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addQuickAction('Filtrar', new TAction(array($this, 'onSearch')), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(('Novo'),  new TAction(array('ConteudoProgramaticoForm', 'onEdit')), 'fas:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_usuario = new TDataGridColumn('system_user->name', 'Professor', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_status = new TDataGridColumn('status', 'Status', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_usuario);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_reg);
        //$this->datagrid->addColumn($column_status);


        // creates the datagrid column actions
        $order_usuario = new TAction(array($this, 'onReload'));
        $order_usuario->setParameter('order', 'usuario');
        $column_usuario->setAction($order_usuario);
        
        $order_curso = new TAction(array($this, 'onReload'));
        $order_curso->setParameter('order', 'curso');
        $column_curso->setAction($order_curso);
        
        $order_turma = new TAction(array($this, 'onReload'));
        $order_turma->setParameter('order', 'turma');
        $column_turma->setAction($order_turma);
        
        $order_data_reg = new TAction(array($this, 'onReload'));
        $order_data_reg->setParameter('order', 'data_reg');
        $column_data_reg->setAction($order_data_reg);
        
        $order_status = new TAction(array($this, 'onReload'));
        $order_status->setParameter('order', 'status');
        $column_status->setAction($order_status);
        
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ConteudoProgramaticoFormEdit', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);


        // create EDIT action
        $action_pdf = new TDataGridAction(array('ConteudoProgramaticoFormView', 'onPrint'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_pdf->setLabel('Gerar PDF');
        $action_pdf->setImage('far:file-pdf red fa-lg');
        $action_pdf->setField('id');
        $this->datagrid->addAction($action_pdf);


        // create delete action
        $action_delete = new TDataGridAction(array($this, 'onDelete'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_delete->setLabel(_t('Delete'));
        $action_delete->setImage('far:trash-alt red fa-lg');
        $action_delete->setField('id');
        $this->datagrid->addAction($action_delete);
        
        
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
        $container->add(TPanelGroup::pack('Listagem - Conteúdo Programático', $this->form));
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
            
            $object = new ConteudoProgramatico($key); 
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
        

        TSession::setValue('ConteudoProgramaticoList_filter_usuario', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_curso', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_disciplina', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_etapa', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_turma',NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_data_reg', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_id', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_status', NULL);

        if (isset($data->usuario) AND ($data->usuario)) {
            $filter = new TFilter('usuario', 'like', "%{$data->usuario}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_usuario', $filter); 
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


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_data_reg', $filter); 
        }


        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_id', $filter); 
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_status', $filter);
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
            
            $unitid = TSession::getValue('userunitid');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
        
            $repository = new TRepository('ConteudoProgramatico');
            $limit = 30;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));
            
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ConteudoProgramaticoList_filter_usuario')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_usuario')); 
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


            if (TSession::getValue('ConteudoProgramaticoList_filter_data_reg')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_data_reg')); 
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_id')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_id')); 
            }


            if (TSession::getValue('ConteudoProgramaticoList_filter_status')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_status')); 
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
                    TTransaction::open('dados_fei');

                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));

                    $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria2);
                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;

                    TTransaction::close();

                    $object->data_reg = TDate::date2br($object->data_reg);

                    //Só adiciona na grid o conteúdo programático dos cursos da unidade logada
                    if($disciplinaNome[0]->CodEntidade == $unitid)
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

            $criteria = new TCriteria;
            $criteria->add(new TFilter('conteudo_programatico_id', '=', $param['key'])); //OR EXPRESSION
            //$criteria->add(new TFilter('user_to', '=', $param['key']));


            $cpitens = ConteudoProgramaticoItem::getObjects($criteria);

          
            if(!empty($cpitens))
            {
                foreach($cpitens as $cpitem)
                {
                    $cpitem->delete();
                }
            }

            $object = new ConteudoProgramatico($key, FALSE); 
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
