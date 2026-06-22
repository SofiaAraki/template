<?php

class TicketFormListProf extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_Ticket');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('Ticket');

        TTransaction::open('Felabs_DB');

        $loggedUnit = TSession::getValue('userunitid');

        $criteria = new TCriteria;
        $criteria->add( new TFilter('departamento_id', '=', 13));
        
        //$categorias = TicketCategoria::getObjects($criteria);
        $colaboradores = SystemUser::getObjects();

        TTransaction::close();


        // create the form fields
        $id = new THidden('id');
        //$titulo = new THidden('titulo');
        $descricao = new TText('descricao');
        //public function __construct($name, $database, $model, $key, $value, $ordercolumn = NULL, TCriteria $criteria = NULL)
        //$system_user_id = new TDBUniqueSearch('system_user_id','dados_fei_t','FiAluno','Codaluno','NomeSemAcento');
        $system_user_id = new THidden('system_user_id');
        $destino_user_id = new THidden('destino_user_id'); //CRIADO MANUAL
        $status = new THidden('status');
        $departamento = new THidden('departamento');
        $categoria = new TDBCombo('categoria', 'Felabs_DB', 'TicketCategoria', 'id', 'nome', 'nome', $criteria);
        $data_reg = new THidden('data_reg');
        $anexo = new TMultiFile('anexo');

        //$system_user_id->setMinLength(5);
        $descricao->setSize('100%',100);
        $id->setEditable(FALSE);

        //$deptoItems = [];
        //$deptoItems[1] = 'Secretaria FE';


        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Departamento', $departamento, '50%');
        $this->form->addQuickField('Solicitante (Prof)', $system_user_id, '50%');
        $this->form->addQuickField('Categoria', $categoria, '50%', new TRequiredValidator);
        $this->form->addQuickField('Descrição', $descricao, '50%', new TRequiredValidator);
        $this->form->addQuickField('Adicionar participante', $destino_user_id, '50%');
        $this->form->addQuickField('Anexar arquivo(s)', $anexo, '50%');
        $this->form->addQuickField('Status', $status, '100%');        
        $this->form->addQuickField('Data Reg', $data_reg, '100%');

        // set exit action for input_exit
        $change_action = new TAction(array($this, 'onChangeAction'));
        $categoria->setChangeAction($change_action);
         
        // create the form actions
        $this->form->addQuickAction(('Salvar'), new TAction(array($this, 'onSave')), 'fa:save green');
        
        //creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Solicitante', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_departamento = new TDataGridColumn('departamento', 'Departamento', 'left');
        $column_categoria = new TDataGridColumn('ticket_categoria->nome', 'Categoria', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Criado em', 'left');
        $column_ultima_edicao = new TDataGridColumn('ultima_edicao', 'Última edição', 'left');
        $column_quem_abriu = new TDataGridColumn('gestor->name', 'Quem abriu', 'left');

        $column_ultima_edicao->setTransformer(function($value, $object, $row) {
            if ($value) {
                $horario = substr($value, 11, 8);
                $dataUs = TDate::date2br($value);
                return "$dataUs $horario";
            }
            return '';
        });

        //add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_departamento);
        $this->datagrid->addColumn($column_categoria);
        //$this->datagrid->addColumn($column_quem_abriu);
        $this->datagrid->addColumn($column_ultima_edicao);
        $this->datagrid->addColumn($column_data_reg);

        // create abrir action
        $action_abrir = new TDataGridAction(array($this, 'goTicketView'), ['key' => '{ticket_id']);
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
        $container->add(TPanelGroup::pack('Novo Ticket', $this->form));
        $container->add(TPanelGroup::pack('Meus Tickets', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public static function onChangeAction($param)
    {
        TTransaction::open('Felabs_DB');

        $categoriaInfo = new TicketCategoria($param['categoria']);

        TTransaction::close();


        if($categoriaInfo->exemplo_msg)
        {
            $obj = new StdClass;
            $obj->descricao = $categoriaInfo->exemplo_msg;
        
            TForm::sendData('form_Ticket', $obj);
        }
    }

    public function goTicketView($param)
    {
        $parametros = [];
        $parametros['key'] = $param['key'];
        $parametros['id'] = $param['key'];

        TSession::setValue('ticketid',$param['key']); //FAZER FILTROS/BUSCA FUNCIONAR NA OUTRA CLASSE

        TApplication::loadPage('TicketView','onReload', $parametros);        
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
        

            $repository = new TRepository('Ticket');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));
            

            if (empty($param['order']))
            {
                $param['order'] = 'status';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('Ticket_filter'))
            {
                $criteria->add(TSession::getValue('Ticket_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $horario = substr($object-> data_reg,11,8);
                    $dataUs = TDate::date2br($object->data_reg);
                    $object->data_reg = "$dataUs"." "."$horario";

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
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            TTransaction::rollback();
        }
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $loggedUnit = TSession::getValue('userunitid');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            $this->form->validate();
            
            $object = new Ticket;  
            
            $data = $this->form->getData();          
            $object->fromArray( (array) $data);


            if(isset($data->anexo))
            {
                $zip = new ZipArchive();
                $today = date("Ymd");
                $nomeArquivo = "arquivos/"."arquivo"."_$today_".time().'.zip';
                $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                
                foreach ($data-> anexo as $arq)
                {
                    $source_file = 'tmp/'.$arq;
                    
                    if (file_exists($source_file))
                    {    
                        $zip->addFile(  'tmp/'.$arq , "$arq" );                        
                    }
                }
                
                $zip->close();
    
                $object->anexo = $nomeArquivo;
            }

            $numeroId = $user->systemuser_codlegado;

            $object->data_reg = date('Y-m-d H:i:s');
            $object->status = 'A';
            $object->departamento = $loggedUnit;
            $object->system_user_id = $user->id;
            $object->quem_abriu = $user->id;
            $object-> matricula_aluno = 'PROFESSOR';

            $object->store();

            $ticketItem = new TicketItem; //CRIA O PRIMEIRO ITEM COM INFORMAÇÕES DO TICKET
            $ticketItem->ticket_id = $object->id;
            $ticketItem->system_user_id = $user->id; //A PESSOA QUE ABRIU O TICKET
            $ticketItem->descricao = $object->descricao;

            if($object->anexo)
            {
                $ticketItem->anexo = $object->anexo;
            }
    
            $ticketItem->data_reg = $object->data_reg;
            $ticketItem->store();

            $ticketPart = new TicketParticipante; //ADICIONA ALUNO SOLICITANTE DO TICKET COMO PARTICIPANTE
            $ticketPart->ticket_id = $object->id;
            $ticketPart->system_user_id = $object->system_user_id;
            $ticketPart->store();

            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            $this->onReload();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
