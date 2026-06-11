<?php


class TicketListProf extends TPage
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
        $this->form = new BootstrapFormBuilder('form_TicketParticipante');
        $this->form->setFormTitle('Buscar Ticket');
        

        // create the form fields
        $id = new THidden('id');
        $ticket_id = new TEntry('ticket_id');
        $system_user_id = new THidden('system_user_id');


        // add the fields
        $this->form->addFields(  [ $id ] );
        $this->form->addFields( [ new TLabel('Id do Ticket') ], [ $ticket_id ] );
        $this->form->addFields( [ $system_user_id ] );


        // set sizes
        $id->setSize('100%');
        $ticket_id->setSize('100%');
        $system_user_id->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('TicketParticipante_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar'), new TAction([$this, 'onSearch']), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['TicketParticipanteForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableHtmlConversion();


        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_ticket_id = new TDataGridColumn('ticket_id', 'Ticket Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'Solicitante', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_categoria = new TDataGridColumn('categoria', 'Categoria', 'left');
        $column_ultima_edicao = new TDataGridColumn('ultima_edicao', 'Última Edição', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Criado em', 'left');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_ticket_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_ultima_edicao);
        $this->datagrid->addColumn($column_data_reg);

        
        // create abrir action
        $action_abrir = new TDataGridAction(array($this, 'goTicketView'),$param);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_abrir->setLabel('Abrir Ticket');
        $action_abrir->setImage('fas:ticket-alt green fa-lg');
        $action_abrir->setField('ticket_id');
        $this->datagrid->addAction($action_abrir);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('Tickets dos quais participo', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public function goTicketView($param)
    {
        $idDoTicket = $param['ticket_id'];

        $parametros = [];
        $parametros['key'] = $param['ticket_id'];
        $parametros['id'] = $param['ticket_id'];

        TSession::setValue('ticketid',$idDoTicket); //FAZER FILTROS/BUSCA FUNCIONAR NA OUTRA CLASSE

        TApplication::loadPage('TicketView','onReload', $parametros);        
    }
    

    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new TicketParticipante($key); 
            
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

        TSession::setValue('TicketProfList_filter_id', NULL);
        TSession::setValue('TicketProfList_filter_ticket_id', NULL);
        TSession::setValue('TicketProfList_filter_system_user_id', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id");
            TSession::setValue('TicketProfList_filter_id', $filter);
        }


        if (isset($data->ticket_id) AND ($data->ticket_id)) {
            $filter = new TFilter('ticket_id', '=', "$data->ticket_id");
            TSession::setValue('TicketProfList_filter_ticket_id', $filter);
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', '=', "$data->system_user_id");
            TSession::setValue('TicketProfList_filter_system_user_id', $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue('TicketParticipante_filter_data', $data);
        
        $param = array();
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
            

            $repository = new TRepository('TicketParticipante');
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
            

            if (TSession::getValue('TicketProfList_filter_id')) {
                $criteria->add(TSession::getValue('TicketProfList_filter_id')); 
            }


            if (TSession::getValue('TicketProfList_filter_ticket_id')) {
                $criteria->add(TSession::getValue('TicketProfList_filter_ticket_id'));
            }


            if (TSession::getValue('TicketProfList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('TicketProfList_filter_system_user_id'));
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
                    $itemObj = new Ticket($object->ticket_id);


                    $object->data_reg = date('d/m/Y H:i',strtotime($itemObj->data_reg));
                    $object->ultima_edicao = date('d/m/Y H:i',strtotime($itemObj->ultima_edicao));
 

                    $user = new SystemUser($itemObj->system_user_id);
                    $cat = new TicketCategoria($itemObj->categoria);

                    $object->categoria = $cat->nome;


                    $object->system_user_id = $user->name;

                    
                    if($itemObj->status == 'A')
                    {
                        $object->status = '<span class="label label-danger">Aberto</span>';
                    }
                    elseif($itemObj->status == 'E')
                    {
                        $object->status = '<span class="label label-warning">Em Progresso</span>';
                    }
                    elseif($itemObj->status == 'F')
                    {
                        $object->status = '<span class="label label-primary">Finalizado</span>';
                    } 

                    $this->datagrid->addItem($object);
                    $this->datagrid->disableHtmlConversion();
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


    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new TicketParticipante($key, FALSE);
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action);
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
