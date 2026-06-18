<?php
/**
 * AtendimentoPsicopedagogicoDatasFormList Form List
 * @author  <your name here>
 */
class AtendimentoPsicopedagogicoDatasFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_AtendimentoPsicopedagogicoDatas');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('AtendimentoPsicopedagogicoDatas');
        


        // create the form fields
        $id = new THidden('id');
        $data_evento = new TDate('data_evento');
        $unidade = new TEntry('unidade');
        $status = new THidden('status');

        $entrada_hora = new TEntry('entrada_hora');
        $saida_hora = new TEntry('saida_hora');
        $system_user_reg = new THidden('system_user_reg');


        TTransaction::open('Felabs_DB');
        $loggedProfUnit = TSession::getValue('userunitid'); //PEGA A ID DA UNIDADE DO USUARIO LOGADO
        $unitName = new SystemUnit($loggedProfUnit);

        TTransaction::close();

        $unidade->setValue($unitName->name);
        $unidade->setEditable(FALSE);

        $id_psico = new TCombo('id_psico');

        $itens_psico = array();
        $itens_psico['1798'] ='PRISCILA CRISTINA BARBOSA FIDELIS';
        $itens_psico['1820'] ='EURIDICE BERGAMASCHI VICENTE';
        $itens_psico['1821'] ='RENATA MARÇAL MAEDA MATSUBARA';

        $id_psico->addItems($itens_psico);
        
  
        $entrada_hora->setMask('99:99');
        $saida_hora->setMask('99:99');

        $entrada_hora->placeholder = "hh:mm (0 a 24 horas)";
        $saida_hora->placeholder = "hh:mm (0 a 24 horas)";
        
        $data_evento->setMask('dd/mm/yyyy');
        $data_evento->setDatabaseMask('yyyy-mm-dd');

        $data_evento->addValidation('"Data"', new TRequiredValidator());
        $id_psico->addValidation('"Psicólogo(a)"', new TRequiredValidator());
        $entrada_hora->addValidation('"Entrada"', new TRequiredValidator());
        $saida_hora->addValidation('"Saida"', new TRequiredValidator());
        


        // add the fields
        $this->form->addQuickField('Id', $id,  '50%' );
        $this->form->addQuickField('Data', $data_evento,  '50%' );
        $this->form->addQuickField('Psicólogo(a)', $id_psico,  '50%' );
        $this->form->addQuickField('Entrada', $entrada_hora,  '50%' );
        $this->form->addQuickField('Saida', $saida_hora,  '50%' );
        $this->form->addQuickField('Unidade', $unidade,  '50%' );
        $this->form->addQuickField('Cadastrado ', $system_user_reg,  '50%' );
        $this->form->addQuickField('Status', $status,  '50%' );




        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addQuickAction(('Salvar'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(('Novo'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_data_evento = new TDataGridColumn('data_evento', 'Data ', 'left');
        $column_id_psico = new TDataGridColumn('system_user_psico->name', 'Psicólogo(a) ', 'left');
        $column_entrada_hora = new TDataGridColumn('entrada_hora', 'Entrada ', 'left');
        $column_saida_hora = new TDataGridColumn('saida_hora', 'Saida ', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_system_user_reg = new TDataGridColumn('system_user_operador->name', 'Operador', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_evento);
        $this->datagrid->addColumn($column_id_psico);
        $this->datagrid->addColumn($column_entrada_hora);
        $this->datagrid->addColumn($column_saida_hora);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_system_user_reg);
        $this->datagrid->addColumn($column_status);
        
        
        // creates two datagrid actions
    /** $action1 = new TDataGridAction(array($this, 'onEdit'));
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');*/
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel('Excluir');
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        
        // add the actions to the datagrid
        //$this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
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
        $container->add(TPanelGroup::pack('Cadastro de Datas - Núcleo de Apoio Psicopedagógico', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'Felabs_DB'
            TTransaction::open('Felabs_DB');
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $loggedUnit = TSession::getValue('userunitid');
            
            // creates a repository for AtendimentoPsicopedagogicoDatas
            $repository = new TRepository('AtendimentoPsicopedagogicoDatas');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;

            //$criteria->add(new TFilter('system_user_id', '=', $logged->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_evento';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('AtendimentoPsicopedagogicoDatas_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AtendimentoPsicopedagogicoDatas_filter'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    if($object->unidade == '0'){
                        $object->unidade = '<span class="label label-primary">FE</span>';
                        
                    }
                    elseif($object->unidade == '1'){
                        $object->unidade = '<span class="label label-success">CNSC</span>';
                        
                    }
                    elseif($object->unidade == '2'){
                        $object->unidade = '<span class="label label-warning">FFCL</span>';
                        
                    }
                    elseif($object->unidade == '3'){
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    }
                    if($object->status == 'Disponível'){
                        $object->status = '<span class="label label-success">Disponível</span>';
                    }
                    elseif($object->status == 'Reservado'){
                        $object->status = '<span class="label label-danger">Reservado</span>';
                    }

                    $object->data_evento = TDate::date2br($object->data_evento);
                    $horario=substr($object-> entrada_hora,0,5);                   
                    $object-> entrada_hora = "$horario". "hrs";
                    $horarios=substr($object-> saida_hora,0,5);                   
                    $object-> saida_hora = "$horarios". "hrs";
                    
                    $object->data_reg = TDate::date2br($object->data_reg);
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new AtendimentoPsicopedagogicoDatas($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $this->onReload( $param ); // reload the listing
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            $loggedProfUnit = TSession::getValue('userunitid');
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new AtendimentoPsicopedagogicoDatas;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            //here
            /**$e = $data->data_evento.' '.$data->entrada_hora.':00'; //FORMATO DATA-HORA
            $s = $data->data_evento.' '.$data->saida_hora.':00'; //FORMATO DATA-HORA

            $data->entrada_hora = $e;
            $data->saida_hora = $s;**/

            //here
            $hoje = date('Y-m-d');
            
            //if ($data->data_evento < $hoje) {
            //	new TMessage('error', 'A data do agendamento não pode ser menor que a data atual');
            //}

            if( ($data->data_evento < $hoje))
            {
               throw new Exception(('A data do agendamento não pode ser menor que a data atual.' ));
            }

            if ($data->entrada_hora > $data->saida_hora) {
            	throw new Exception('O horário de início não pode ser maior que o horário de término.' );
            	
            }

            $object->fromArray( (array) $data); // load the object with data
            $object->system_user_reg = TSession:: getValue ('userid');//pega o usuário que esta logado
            $object->unidade = $loggedProfUnit;
            $object->status = "Disponível";
            
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved')); // success message
            $this->onReload(); // reload the listing
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
        $object = new StdClass;
        $object->unidade = $param['unidade'];
        $this->form->setData($object);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new AtendimentoPsicopedagogicoDatas($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
