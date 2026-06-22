<?php
class TicketList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_Ticket');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('Ticket');

        TTransaction::open('Felabs_DB');

        $colaboradores = SystemUser::getObjects();
        $loggedUnit = TSession::getValue('userunitid');
        $unit = new SystemUnit($loggedUnit);

        TTransaction::close();

        $criteria = new TCriteria;
        $criteria->add(new TFilter('departamento_id', '=', $loggedUnit));

        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $system_user_id = new TUniqueSearch('system_user_id');
        $status = new THidden('status');
        $categoria = new TDBCombo('categoria','Felabs_DB','TicketCategoria','id','nome','nome',$criteria);
        $data_reg = new THidden('data_reg');

        $items = [];
        foreach($colaboradores as $colaborador)
        {
            $items[$colaborador->id] = $colaborador->name;
        }
        $system_user_id->addItems($items);

        // add the fields
        $this->form->addQuickField('Id do Ticket', $id, '80%');
        $this->form->addQuickField('Descrição', $descricao, '80%');
        $this->form->addQuickField('Solicitante', $system_user_id, '80%');
        $this->form->addQuickField('Status', $status, '80%');
        $this->form->addQuickField('Categoria', $categoria, '80%');
        $this->form->addQuickField('Data Reg', $data_reg, '80%');

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Ticket_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fas:search');
        $this->form->addQuickAction('Novo Ticket',  new TAction(array('TicketFormList', 'onShow')), 'fas:plus green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Aluno', 'left');
        $column_matricula_aluno = new TDataGridColumn('matricula_aluno', 'Matrícula', 'left');
        $column_categoria = new TDataGridColumn('ticket_categoria->nome', 'Categoria', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_edicao_user_id = new TDataGridColumn('edicao_user->name', 'Responsável', 'left');
        $column_ultima_edicao = new TDataGridColumn('ultima_edicao', 'Última Edição', 'left');
        $column_quem_abriu = new TDataGridColumn('gestor->name', 'Quem abriu', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Criado em', 'left');

        $column_ultima_edicao->setTransformer(function($value)
        {
            return $value ? date('d/m/Y H:i', strtotime($value)) : '';
        });

        $column_data_reg->setTransformer(function($value)
        {
            return $value ? date('d/m/Y H:i', strtotime($value)) : '';
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_matricula_aluno);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_edicao_user_id);
        $this->datagrid->addColumn($column_ultima_edicao);
        $this->datagrid->addColumn($column_quem_abriu);
        $this->datagrid->addColumn($column_data_reg);
        
        // create abrir action
        $action_abrir = new TDataGridAction(array($this, 'goTicketView'));
        $action_abrir->setLabel('Abrir Ticket');
        $action_abrir->setImage('fas:ticket-alt green fa-lg');
        $action_abrir->setField('id');
        $this->datagrid->addAction($action_abrir);        

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
        $container->add(TPanelGroup::pack('Buscar Tickets', $this->form));
        $container->add(TPanelGroup::pack("Listagem Geral {$unit->name}", $this->datagrid, $this->pageNavigation));
        
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
    
    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('TicketList_filter_id', NULL);
        TSession::setValue('TicketList_filter_titulo', NULL);
        TSession::setValue('TicketList_filter_descricao', NULL);
        TSession::setValue('TicketList_filter_system_user_id', NULL);
        TSession::setValue('TicketList_filter_status', NULL);
        TSession::setValue('TicketList_filter_departamento', NULL);
        TSession::setValue('TicketList_filter_matricula_aluno', NULL);
        TSession::setValue('TicketList_filter_categoria', NULL);
        TSession::setValue('TicketList_filter_data_reg', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); 
            TSession::setValue('TicketList_filter_id',   $filter); 
        }


        if (isset($data->titulo) AND ($data->titulo)) {
            $filter = new TFilter('titulo', 'like', "%{$data->titulo}%"); 
            TSession::setValue('TicketList_filter_titulo',   $filter); 
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); 
            TSession::setValue('TicketList_filter_descricao',   $filter); 
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', 'like', "%{$data->system_user_id}%"); 
            TSession::setValue('TicketList_filter_system_user_id',   $filter); 
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); 
            TSession::setValue('TicketList_filter_status',   $filter); 
        }


        if (isset($data->departamento) AND ($data->departamento)) {
            $filter = new TFilter('departamento', 'like', "%{$data->departamento}%"); 
            TSession::setValue('TicketList_filter_departamento',   $filter); 
        }

        if (isset($data->matricula_aluno) AND ($data->matricula_aluno)) {
            $filter = new TFilter('matricula_aluno', 'like', "%{$data->matricula_aluno}%"); 
            TSession::setValue('TicketList_filter_matricula_aluno',   $filter); 
        }


        if (isset($data->categoria) AND ($data->categoria)) {
            $filter = new TFilter('categoria', 'like', "%{$data->categoria}%"); 
            TSession::setValue('TicketList_filter_categoria',   $filter); 
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('TicketList_filter_data_reg',   $filter); 
        }

        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $data->{$key} = '';
            }
        }

        $this->form->setData($data);
        TSession::setValue('Ticket_filter_data', $data);
        
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
            
            $repository = new TRepository('Ticket');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('departamento', '=', $loggedUnit));
            

            if (empty($param['order']))
            {
                $param['order'] = 'status';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('TicketList_filter_id')) {
                $criteria->add(TSession::getValue('TicketList_filter_id')); 
            }


            if (TSession::getValue('TicketList_filter_titulo')) {
                $criteria->add(TSession::getValue('TicketList_filter_titulo')); 
            }


            if (TSession::getValue('TicketList_filter_descricao')) {
                $criteria->add(TSession::getValue('TicketList_filter_descricao')); 
            }


            if (TSession::getValue('TicketList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('TicketList_filter_system_user_id')); 
            }


            if (TSession::getValue('TicketList_filter_status')) {
                $criteria->add(TSession::getValue('TicketList_filter_status')); 
            }


            if (TSession::getValue('TicketList_filter_departamento')) {
                $criteria->add(TSession::getValue('TicketList_filter_departamento')); 
            }

            if (TSession::getValue('TicketList_filter_matricula_aluno')) {
                $criteria->add(TSession::getValue('TicketList_filter_matricula_aluno')); 
            }


            if (TSession::getValue('TicketList_filter_categoria')) {
                $criteria->add(TSession::getValue('TicketList_filter_categoria')); 
            }


            if (TSession::getValue('TicketList_filter_data_reg')) {
                $criteria->add(TSession::getValue('TicketList_filter_data_reg')); 
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
                    if($object->status == 'A')
                    {
                        $object->status = '<span class="label label-danger">Aberto</span>';
                    }
                    elseif($object->status == 'E')
                    {
                        $object->status = '<span class="label label-warning">Em Progresso</span>';
                    }
                    elseif($object->status == 'F')
                    {
                        $object->status = '<span class="label label-primary">Finalizado</span>';
                    }              

                    $unidade = new SystemUnit($object->departamento);
                    $object->departamento = $unidade->name;

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
