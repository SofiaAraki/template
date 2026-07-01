<?php

class AgendamentoEquipamentoList extends TPage
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
        $this->form = new TQuickForm('form_search_AgendamentoEquipamento');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('AgendamentoEquipamento');
        

        // create the form fields
        $id = new TEntry('id');
        $local = new TEntry('local');
        $equipamento_id = new TEntry('equipamento_id');
        $data_evento = new TDate('data_evento');
        $inicio = new TEntry('inicio');
        $termino = new TEntry('termino');
        $observacoes = new TEntry('observacoes');
        $usuario = new TEntry('usuario');
        $unidade = new TEntry('unidade');
        $data_reg = new TEntry('data_reg');


        // add the fields
        //$this->form->addQuickField('Id', $id, '100%');
        //$this->form->addQuickField('Local', $local, '100%');
        //$this->form->addQuickField('Equipamento Id', $equipamento_id, '100%');
        $this->form->addQuickField('Data', $data_evento, '100%');
        //$this->form->addQuickField('Inicio', $inicio, '100%');
        //$this->form->addQuickField('Termino', $termino, '100%');
        //$this->form->addQuickField('Observacoes', $observacoes, '100%');
        //$this->form->addQuickField('Usuario', $usuario, '100%');
        //$this->form->addQuickField('Unidade', $unidade, '100%');
        //$this->form->addQuickField('Data Reg', $data_reg, '100%');


        $data_evento->setSize('40%');
        $data_evento->setMask('dd/mm/yyyy'); 

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('AgendamentoEquipamento_filter_data') );
        
        
        // add the search form actions
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fas:search blue');
        $this->form->addQuickAction('Agendar Equipamento',  new TAction(array('AgendamentoEquipamentoForm', 'onEdit')), 'fas:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_local = new TDataGridColumn('local', 'Local', 'left');
        $column_equipamento_id = new TDataGridColumn('agendamento_equipamento_item->equipamento', 'Equipamento', 'left');
        $column_data_evento = new TDataGridColumn('data_evento', 'Data', 'left');
        $column_inicio = new TDataGridColumn('inicio', 'Início', 'left');
        $column_termino = new TDataGridColumn('termino', 'Término', 'left');
        $column_observacoes = new TDataGridColumn('observacoes', 'Observações', 'left');
        $column_usuario = new TDataGridColumn('system_user->name', 'Usuário', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_local);
        $this->datagrid->addColumn($column_equipamento_id);
        $this->datagrid->addColumn($column_data_evento);
        $this->datagrid->addColumn($column_inicio);
        $this->datagrid->addColumn($column_termino);
        $this->datagrid->addColumn($column_observacoes);
        $this->datagrid->addColumn($column_usuario);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_data_reg);


        // creates the datagrid column actions
        $order_data_evento = new TAction(array($this, 'onReload'));
        $order_data_evento->setParameter('order', 'data_evento');
        $column_data_evento->setAction($order_data_evento);
        
        $order_unidade = new TAction(array($this, 'onReload'));
        $order_unidade->setParameter('order', 'unidade');
        $column_unidade->setAction($order_unidade);
        
        
        // create EDIT action
        //$action_edit = new TDataGridAction(array('AgendamentoEquipamentoForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        //$action_edit->setLabel(_t('Edit'));
        //$action_edit->setImage('far:edit blue fa-lg');
        //$action_edit->setField('id');
       //$this->datagrid->addAction($action_edit);
        
        
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
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Lista de Agendamentos - Equipamentos', $this->form));
        $container->add(TPanelGroup::pack('Meus Agendamentos', $this->datagrid, $this->pageNavigation));
        
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
            
            $object = new AgendamentoEquipamento($key);
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

        $data->data_evento = TDate::date2us($data->data_evento);
        
        // clear session filters
        TSession::setValue('AgendamentoEquipamentoList_filter_id', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_local', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_equipamento_id', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_data_evento', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_inicio', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_termino', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_observacoes', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_usuario', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_unidade', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_data_reg', NULL);


        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_id', $filter); 
        }


        if (isset($data->local) AND ($data->local)) {
            $filter = new TFilter('local', 'like', "%{$data->local}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_local', $filter); 
        }


        if (isset($data->equipamento_id) AND ($data->equipamento_id)) {
            $filter = new TFilter('equipamento_id', 'like', "%{$data->equipamento_id}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_equipamento_id', $filter); 
        }


        if (isset($data->data_evento) AND ($data->data_evento)) {
            $filter = new TFilter('data_evento', 'like', "%{$data->data_evento}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_data_evento', $filter);
        }


        if (isset($data->inicio) AND ($data->inicio)) {
            $filter = new TFilter('inicio', 'like', "%{$data->inicio}%");
            TSession::setValue('AgendamentoEquipamentoList_filter_inicio', $filter);
        }


        if (isset($data->termino) AND ($data->termino)) {
            $filter = new TFilter('termino', 'like', "%{$data->termino}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_termino', $filter); 
        }


        if (isset($data->observacoes) AND ($data->observacoes)) {
            $filter = new TFilter('observacoes', 'like', "%{$data->observacoes}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_observacoes', $filter); 
        }


        if (isset($data->usuario) AND ($data->usuario)) {
            $filter = new TFilter('usuario', 'like', "%{$data->usuario}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_usuario', $filter); 
        }


        if (isset($data->unidade) AND ($data->unidade)) {
            $filter = new TFilter('unidade', 'like', "%{$data->unidade}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_unidade', $filter);
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('AgendamentoEquipamentoList_filter_data_reg', $filter); 
        }

        $data->data_evento = TDate::date2br($data->data_evento);


        $this->form->setData($data);
        
        TSession::setValue('AgendamentoEquipamento_filter_data', $data);
        
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
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            
            $repository = new TRepository('AgendamentoEquipamento');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            $criteria->add(new TFilter('usuario', '=', $user->id));
            

            if (empty($param['order']))
            {
                $param['order'] = 'data_evento';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AgendamentoEquipamentoList_filter_id')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_id')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_local')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_local')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_equipamento_id')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_equipamento_id')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_data_evento')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_data_evento')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_inicio')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_inicio')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_termino')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_termino')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_observacoes')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_observacoes')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_usuario')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_usuario')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_unidade')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_unidade')); 
            }


            if (TSession::getValue('AgendamentoEquipamentoList_filter_data_reg')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_data_reg')); 
            }


            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }

            $hojeData = date('Y-m-d');
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    if($object->unidade == '0')
                    {
                        $object->unidade = '<span class="label label-primary">FE</span>';                        
                    }
                    elseif($object->unidade == '12')
                    {
                        $object->unidade = '<span class="label label-success">CONNEXT</span>';                        
                    }
                    elseif($object->unidade == '2')
                    {
                        $object->unidade = '<span class="label label-warning">FFCL</span>';                        
                    }
                    elseif($object->unidade == '3')
                    {
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    }

                    //MOSTRA APENAS OS EVENTOS DE HOJE PARA FRENTE
                    //if($object->data_evento >= $hojeData)
                    //{ 
                        $object->inicio = substr($object->inicio, 11, -7);
                        $object->termino = substr($object->termino, 11, -7);
    
                        $object->data_evento = TDate::date2br($object->data_evento);
                        $object->data_reg = TDate::date2br($object->data_reg);
    
                        $this->datagrid->addItem($object);
                    //}
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
            
            $object = new AgendamentoEquipamento($key, FALSE);
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
