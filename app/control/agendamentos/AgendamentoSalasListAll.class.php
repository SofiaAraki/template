<?php
/**
 * AgendamentoSalasListAll Listing
 * @author  <your name here>
 */
class AgendamentoSalasListAll extends TPage
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
        $this->form = new TQuickForm('form_search_AgendamentoSalas');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('AgendamentoSalas');
        

        // create the form fields
        $usuario = new TEntry('usuario');
        $data_evento = new TEntry('data_evento');
        $inicio = new TEntry('inicio');
        $termino = new TEntry('termino');
        $descricao = new TEntry('descricao');
        $id = new TEntry('id');
        $data_reg = new TEntry('data_reg');
        $sala_id = new TEntry('sala_id');


        // add the fields
        $this->form->addQuickField('Usuario', $usuario,  '100%' );
        $this->form->addQuickField('Data Evento', $data_evento,  '100%' );
        $this->form->addQuickField('Inicio', $inicio,  '100%' );
        $this->form->addQuickField('Termino', $termino,  '100%' );
        $this->form->addQuickField('Descricao', $descricao,  '100%' );
        $this->form->addQuickField('Id', $id,  '100%' );
        $this->form->addQuickField('Data Reg', $data_reg,  '100%' );
        $this->form->addQuickField('Sala Id', $sala_id,  '100%' );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('AgendamentoSalas_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search blue');
        $this->form->addQuickAction('Novo',  new TAction(array('AgendamentoSalasForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_usuario = new TDataGridColumn('usuario', 'Usuario', 'left');
        $column_data_evento = new TDataGridColumn('data_evento', 'Data Evento', 'left');
        $column_inicio = new TDataGridColumn('inicio', 'Inicio', 'left');
        $column_termino = new TDataGridColumn('termino', 'Termino', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data Reg', 'left');
        $column_sala_id = new TDataGridColumn('sala_id', 'Sala Id', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_usuario);
        $this->datagrid->addColumn($column_data_evento);
        $this->datagrid->addColumn($column_inicio);
        $this->datagrid->addColumn($column_termino);
        $this->datagrid->addColumn($column_descricao);
        
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_sala_id);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('AgendamentoSalasForm', 'onEdit'));
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
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Title', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
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
            $object = new AgendamentoSalas($key); // instantiates the Active Record
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
        TSession::setValue('AgendamentoSalasListAll_filter_usuario',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_data_evento',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_inicio',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_termino',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_descricao',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_descricao',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_id',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_data_reg',   NULL);
        TSession::setValue('AgendamentoSalasListAll_filter_sala_id',   NULL);

        if (isset($data->usuario) AND ($data->usuario)) {
            $filter = new TFilter('usuario', 'like', "%{$data->usuario}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_usuario',   $filter); // stores the filter in the session
        }


        if (isset($data->data_evento) AND ($data->data_evento)) {
            $filter = new TFilter('data_evento', 'like', "%{$data->data_evento}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_data_evento',   $filter); // stores the filter in the session
        }


        if (isset($data->inicio) AND ($data->inicio)) {
            $filter = new TFilter('inicio', 'like', "%{$data->inicio}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_inicio',   $filter); // stores the filter in the session
        }


        if (isset($data->termino) AND ($data->termino)) {
            $filter = new TFilter('termino', 'like', "%{$data->termino}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_termino',   $filter); // stores the filter in the session
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_data_reg',   $filter); // stores the filter in the session
        }


        if (isset($data->sala_id) AND ($data->sala_id)) {
            $filter = new TFilter('sala_id', 'like', "%{$data->sala_id}%"); // create the filter
            TSession::setValue('AgendamentoSalasListAll_filter_sala_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('AgendamentoSalas_filter_data', $data);
        
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
            
            // creates a repository for AgendamentoSalas
            $repository = new TRepository('AgendamentoSalas');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AgendamentoSalasListAll_filter_usuario')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_usuario')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_data_evento')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_data_evento')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_inicio')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_inicio')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_termino')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_termino')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_descricao')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_descricao')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_id')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_id')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_data_reg')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_data_reg')); // add the session filter
            }


            if (TSession::getValue('AgendamentoSalasListAll_filter_sala_id')) {
                $criteria->add(TSession::getValue('AgendamentoSalasListAll_filter_sala_id')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
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
            $object = new AgendamentoSalas($key, FALSE); // instantiates the Active Record
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
