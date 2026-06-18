<?php


class TicketParticipanteListAdm extends TPage
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
        $this->form->setFormTitle('Meus Tickets');

        TTransaction::open('Felabs_DB');

        $usuarios = SystemUser::getObjects();
        $loggedUnit = TSession::getValue('userunitid');

        $unit = new SystemUnit($loggedUnit);

        TTransaction::close();


        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_id', '=', $loggedUnit));
        

        // create the form fields
        $id = new TEntry('id');
        $ticket_id = new TEntry('ticket_id');
        $system_user_id = new TEntry('system_user_id');




        // add the fields
        //$this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Id do Ticket') ], [ $ticket_id ] );
        $this->form->addFields( [ new TLabel('Aluno(a):') ], [ $system_user_id ] );


        // set sizes
        $id->setSize('100%');
        $ticket_id->setSize('100%');
        $system_user_id->setSize('100%');

        // $items = [];

        // foreach($colaboradores as $colaborador)
        // {
        //     $items[$colaborador->id] = $colaborador->name;
        // }

        // $system_user_id->addItems($items);

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('TicketParticipante_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar'), new TAction([$this, 'onSearch']), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        //this->form->addActionLink(_t('New'), new TAction(['TicketParticipanteForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_ticket_id = new TDataGridColumn('ticket_id', 'Id do Ticket ', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Solicitante', 'left');
        $column_solicitante = new TDataGridColumn('solicitante', 'Solicitante', 'left');
        $column_categoria = new TDataGridColumn('categoria', 'Categoria', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_criado = new TDataGridColumn('criado', 'Criado em', 'left');
        $column_editado = new TDataGridColumn('editado', 'Última Alteração', 'left');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_ticket_id);
        $this->datagrid->addColumn($column_ticket_id);

        $this->datagrid->addColumn($column_solicitante);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_criado);
        $this->datagrid->addColumn($column_editado);


        // create abrir action
        $action_abrir = new TDataGridAction(array($this, 'goTicketView'),$param);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_abrir->setLabel('Abrir Ticket');
        $action_abrir->setImage('fas:ticket-alt green fa-lg');
        $action_abrir->setField('ticket_id');
        $this->datagrid->addAction($action_abrir);

        
        // create EDIT action
        /*$action_edit = new TDataGridAction(['TicketParticipanteForm', 'onEdit']);
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
        $this->datagrid->addAction($action_del);*/


        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('Tickets dos quais participo', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public function goTicketView($param)
    {
        $idDoTicket = $param['key'];

        $parametros = [];
        $parametros['key'] = $param['key'];
        $parametros['id'] = $param['key'];

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

        TSession::setValue('TicketParticipanteList_filter_id', NULL);
        TSession::setValue('TicketParticipanteList_filter_ticket_id', NULL);
        TSession::setValue('TicketParticipanteList_filter_system_user_id', NULL);


        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); 
            TSession::setValue('TicketParticipanteList_filter_id', $filter); 
        }


        if (isset($data->ticket_id) AND ($data->ticket_id)) {
            $filter = new TFilter('ticket_id', '=', "$data->ticket_id"); 
            TSession::setValue('TicketParticipanteList_filter_ticket_id', $filter); 
        }

        if (isset($data->system_user_id) && ($data->system_user_id)) {
            TTransaction::open('Felabs_DB');
        
            $repository = new TRepository('SystemUser');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('name', 'LIKE', "%{$data->system_user_id}%"));
            echo $criteria->dump();
            
            $users = $repository->load($criteria);
            TTransaction::close();
        
            if ($users) {
                $userIds = [];
                foreach ($users as $user) {
                    $userIds[] = $user->id;
                }
        
                if (!empty($userIds)) {
                    // Passa o array corretamente para o TFilter
                    $filter = new TFilter('system_user_id', 'IN', $userIds); 
                    TSession::setValue('TicketParticipanteList_filter_system_user_id', $filter);
                    var_dump($filter->dump());
                }
            }
        
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
            
            $loggedUnit = TSession::getValue('userunitid');
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
        
            // $sessionFilter = TSession::getValue('TicketParticipanteList_filter_system_user_id');
            // var_dump($sessionFilter ? $sessionFilter->dump() : 'Filtro de usuário não encontrado');

            $repository = new TRepository('TicketParticipante');
            $limit = 20;



            $criteria = new TCriteria;

            $criteria->setProperty('order', "(SELECT data_reg FROM ticket WHERE ticket.id = ticket_participante.ticket_id) DESC, 
                                             (SELECT status FROM ticket WHERE ticket.id = ticket_participante.ticket_id) DESC");
            

            // Restrição padrão: só carrega tickets que o usuário participa
            $sessionFilterUser = TSession::getValue('TicketParticipanteList_filter_system_user_id');

            if ($sessionFilterUser) {
                // Se houver filtro de busca, combinamos com a restrição do usuário logado
                $criteria->add($sessionFilterUser);
            } else {
                // Se não há filtro de busca, aplica somente a restrição do usuário logado
                $criteria->add(new TFilter('system_user_id', '=', $user->id));
            }

            // Adiciona os outros filtros da sessão
            if (TSession::getValue('TicketParticipanteList_filter_id')) {
                $criteria->add(TSession::getValue('TicketParticipanteList_filter_id'));
            }

            if (TSession::getValue('TicketParticipanteList_filter_ticket_id')) {
                $criteria->add(TSession::getValue('TicketParticipanteList_filter_ticket_id'));
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
                    $ticketInfo = new Ticket($object->ticket_id);


                    if($ticketInfo->status == 'A')
                    {
                        $object->status = '<span class="label label-danger">Aberto</span>';
                    }
                    elseif($ticketInfo->status == 'E')
                    {
                        $object->status = '<span class="label label-warning">Em Progresso</span>';
                    }
                    elseif($ticketInfo->status == 'F')
                    {
                        $object->status = '<span class="label label-primary">Finalizado</span>';
                    }   


                    //$object->status = $ticketInfo->status;

                    $sol = new SystemUser($ticketInfo->system_user_id);

                    $object->criado = date('d/m/Y H:i',strtotime($ticketInfo->data_reg));
                    $object->editado = date('d/m/Y H:i',strtotime($ticketInfo->ultima_edicao));
                    $object->solicitante = $sol->name;

                    $cat = new TicketCategoria($ticketInfo->categoria);

                    $object->categoria = $cat->nome;


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
