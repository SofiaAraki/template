<?php
/**
 * AtendimentoPsicopedagogicoDatasList Listing
 * @author  <your name here>
 */
class AtendimentoPsicopedagogicoDatasListAll extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_AtendimentoPsicopedagogicoDatas');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('AtendimentoPsicopedagogicoDatas');
        

        // create the form fields
        $data_evento = new TDate('data_evento');
        //$entrada_hora = new TEntry('entrada_hora');
        //$saida_hora = new TEntry('saida_hora');

        $data_evento->setMask('dd/mm/yyyy');
        $data_evento->setDatabaseMask('yyyy-mm-dd');


        // add the fields
        $this->form->addQuickField('Data', $data_evento,  '25%' );
        //$this->form->addQuickField('Entrada Hora', $entrada_hora,  '100%' );
        //$this->form->addQuickField('Saida Hora', $saida_hora,  '100%' );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('AtendimentoPsicopedagogicoDatas_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction(('Novo Agendamento'),  new TAction(array('AtendimentoPsicopedagogicoAgendamentoForm', 'onEdit')), 'fa:plus #69aa46');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_data_evento = new TDataGridColumn('data_evento', 'Data', 'left');
        $column_id_psico = new TDataGridColumn('system_user_psico->name', 'Psicólogo(a) ', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_entrada_hora = new TDataGridColumn('entrada_hora', 'Entrada', 'left');
        $column_saida_hora = new TDataGridColumn('saida_hora', 'Saida', 'left');
        $column_email = new TDataGridColumn('email', 'Email', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Aluno', 'left');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_evento);
        $this->datagrid->addColumn($column_id_psico);
        $this->datagrid->addColumn($column_entrada_hora);
        $this->datagrid->addColumn($column_saida_hora);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_status);

        
        // create EDIT action
    /** $action_edit = new TDataGridAction(array('AtendimentoPsicopedagogicoDatasFormList', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);*/
        

        
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
        $container->add(TPanelGroup::pack('Agendamento Psicopedagógico', $this->form));
        $container->add(TPanelGroup::pack('Todos Agendamentos', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new AtendimentoPsicopedagogicoDatas($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('AtendimentoPsicopedagogicoDatasList_filter_data_evento',   NULL);
        TSession::setValue('AtendimentoPsicopedagogicoDatasList_filter_entrada_hora',   NULL);
        TSession::setValue('AtendimentoPsicopedagogicoDatasList_filter_saida_hora',   NULL);

        if (isset($data->data_evento) AND ($data->data_evento)) {
            $filter = new TFilter('data_evento', 'like', "%{$data->data_evento}%"); // create the filter
            TSession::setValue('AtendimentoPsicopedagogicoDatasList_filter_data_evento',   $filter); // stores the filter in the session
        }


        if (isset($data->entrada_hora) AND ($data->entrada_hora)) {
            $filter = new TFilter('entrada_hora', 'like', "%{$data->entrada_hora}%"); // create the filter
            TSession::setValue('AtendimentoPsicopedagogicoDatasList_filter_entrada_hora',   $filter); // stores the filter in the session
        }


        if (isset($data->saida_hora) AND ($data->saida_hora)) {
            $filter = new TFilter('saida_hora', 'like', "%{$data->saida_hora}%"); // create the filter
            TSession::setValue('AtendimentoPsicopedagogicoDatasList_filter_saida_hora',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('AtendimentoPsicopedagogicoDatas_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
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
            //$criteria->add(new TFilter('status', '=', 'Reservado'));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_evento';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AtendimentoPsicopedagogicoDatasList_filter_data_evento')) {
                $criteria->add(TSession::getValue('AtendimentoPsicopedagogicoDatasList_filter_data_evento')); // add the session filter
            }


            if (TSession::getValue('AtendimentoPsicopedagogicoDatasList_filter_entrada_hora')) {
                $criteria->add(TSession::getValue('AtendimentoPsicopedagogicoDatasList_filter_entrada_hora')); // add the session filter
            }


            if (TSession::getValue('AtendimentoPsicopedagogicoDatasList_filter_saida_hora')) {
                $criteria->add(TSession::getValue('AtendimentoPsicopedagogicoDatasList_filter_saida_hora')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
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
            new TMessage('error', $e->getMessage());
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
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
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
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); // success message
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
