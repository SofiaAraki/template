<?php

class TicketItemList extends TPage
{
    private $form;
    private $datagrid; 
    private $pageNavigation;
    private $loaded;   

    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new TQuickForm('form_search_TicketItem');
        $this->form->class = 'tform';
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%';
        $this->form->setFormTitle('TicketItem');
        

        // create the form fields
        $id = new TEntry('id');
        $ticket_id = new TEntry('ticket_id');
        $system_user_id = new TEntry('system_user_id');
        $destino_user_id = new TEntry('destino_user_id');
        $descricao = new TEntry('descricao');
        $anexo = new TEntry('anexo');
        $data_reg = new TEntry('data_reg');


        // add the fields
        $this->form->addQuickField('Id', $id, '100%');
        $this->form->addQuickField('Ticket Id', $ticket_id, '100%');
        $this->form->addQuickField('System User Id', $system_user_id, '100%');
        //$this->form->addQuickField('Destino User Id', $destino_user_id, '100%');
        //$this->form->addQuickField('Descricao', $descricao, '100%');
        //$this->form->addQuickField('Anexo', $anexo, '100%');
        //$this->form->addQuickField('Data Reg', $data_reg, '100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('TicketItem_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction(_t('New'),  new TAction(array('TicketItemForm', 'onEdit')), 'bs:plus-sign green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_ticket_id = new TDataGridColumn('ticket_id', 'Ticket Id', 'right');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'System User Id', 'right');
        $column_destino_user_id = new TDataGridColumn('destino_user_id', 'Destino User Id', 'right');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_anexo = new TDataGridColumn('anexo', 'Anexo', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data Reg', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_ticket_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_destino_user_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_anexo);
        $this->datagrid->addColumn($column_data_reg);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Listar Itens', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public function onSearch()
    {
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('TicketItemList_filter_id', NULL);
        TSession::setValue('TicketItemList_filter_ticket_id', NULL);
        TSession::setValue('TicketItemList_filter_system_user_id', NULL);
        TSession::setValue('TicketItemList_filter_destino_user_id', NULL);
        TSession::setValue('TicketItemList_filter_descricao', NULL);
        TSession::setValue('TicketItemList_filter_anexo', NULL);
        TSession::setValue('TicketItemList_filter_data_reg', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%");
            TSession::setValue('TicketItemList_filter_id', $filter);
        }


        if (isset($data->ticket_id) AND ($data->ticket_id)) {
            $filter = new TFilter('ticket_id', 'like', "%{$data->ticket_id}%");
            TSession::setValue('TicketItemList_filter_ticket_id', $filter);
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', 'like', "%{$data->system_user_id}%");
            TSession::setValue('TicketItemList_filter_system_user_id', $filter);
        }


        if (isset($data->destino_user_id) AND ($data->destino_user_id)) {
            $filter = new TFilter('destino_user_id', 'like', "%{$data->destino_user_id}%"); 
            TSession::setValue('TicketItemList_filter_destino_user_id', $filter); 
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); 
            TSession::setValue('TicketItemList_filter_descricao', $filter); 
        }


        if (isset($data->anexo) AND ($data->anexo)) {
            $filter = new TFilter('anexo', 'like', "%{$data->anexo}%"); 
            TSession::setValue('TicketItemList_filter_anexo', $filter); 
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('TicketItemList_filter_data_reg', $filter); 
        }


        $this->form->setData($data);
        
        TSession::setValue('TicketItem_filter_data', $data);
        
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
            
            $repository = new TRepository('TicketItem');
            $limit = 10;

            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('TicketItemList_filter_id')) {
                $criteria->add(TSession::getValue('TicketItemList_filter_id')); 
            }


            if (TSession::getValue('TicketItemList_filter_ticket_id')) {
                $criteria->add(TSession::getValue('TicketItemList_filter_ticket_id')); 
            }


            if (TSession::getValue('TicketItemList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('TicketItemList_filter_system_user_id')); 
            }


            if (TSession::getValue('TicketItemList_filter_destino_user_id')) {
                $criteria->add(TSession::getValue('TicketItemList_filter_destino_user_id')); 
            }


            if (TSession::getValue('TicketItemList_filter_descricao')) {
                $criteria->add(TSession::getValue('TicketItemList_filter_descricao')); 
            }


            if (TSession::getValue('TicketItemList_filter_anexo')) {
                $criteria->add(TSession::getValue('TicketItemList_filter_anexo'));
            }


            if (TSession::getValue('TicketItemList_filter_data_reg')) {
                $criteria->add(TSession::getValue('TicketItemList_filter_data_reg'));
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
