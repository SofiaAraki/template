<?php
/**
 * ReqBolsaAlunoPeriodoFormList Form List
 * @author  <your name here>
 */
class ReqBolsaAlunoPeriodoFormList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ReqBolsaAlunoPeriodo');
        $this->form->setFormTitle('Período - Requerimento de bolsas');

        // create the form fields
        $id = new TEntry('id');
        $data_inicio = new TDate('data_inicio');
        $data_fim = new TDate('data_fim');

        $data_inicio->addValidation( ('Início'), new TRequiredValidator );
        $data_fim->addValidation( ('Término'), new TRequiredValidator );

        // add the fields
        $this->form->addFields( [new TLabel('ID')], [$id] );
        $this->form->addFields( [$ld=new TLabel(('Início'))], [$data_inicio] );
        $this->form->addFields( [$lf=new TLabel(('Término'))], [$data_fim] );

        $id->setEditable(FALSE);
        $id->setSize('20%');
        $data_inicio->setSize('20%');
        $ld->setFontColor('red');
        $data_fim->setSize('20%');
        $lf->setFontColor('red');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_fim->setDatabaseMask('yyyy-mm-dd');
        $data_inicio->setMask('dd/mm/yyyy');
        $data_fim->setMask('dd/mm/yyyy');

        $this->form->addAction(_t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->width = '100%';
        $this->datagrid->setHeight(320);

        $this->datagrid->addQuickColumn('ID', 'id', 'center', 50, new TAction(array($this, 'onReload')), array('order', 'id'));
        $col_data_inicio = $this->datagrid->addQuickColumn(('Início'), 'data_inicio', 'left', NULL, new TAction(array($this, 'onReload')), array('order', 'inicio'));
        $col_data_fim = $this->datagrid->addQuickColumn(('Término'), 'data_fim', 'left', NULL, new TAction(array($this, 'onReload')), array('order', 'fim'));

        $col_data_inicio->setTransformer(array($this, 'formatDate'));
        $col_data_fim->setTransformer(array($this, 'formatDate'));

        // add the actions to the datagrid
        $this->datagrid->addQuickAction(('Editar'), new TDataGridAction(array($this, 'onEdit')), 'id', 'far:edit blue fa-lg');
        $this->datagrid->addQuickAction(('Excluir'), new TDataGridAction(array($this, 'onDelete')), 'id', 'far:trash-alt red fa-lg');

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // creates the page structure using a vbox
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        // add the container inside the page
        parent::add($container);

    }

    public function formatDate($date, $object)
        {
            $dt = new DateTime($date);
            return $dt->format('d/m/Y');
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
            
            // creates a repository for ReqBolsaAlunoPeriodo
            $repository = new TRepository('ReqBolsaAlunoPeriodo');
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
            
            if (TSession::getValue('ReqBolsaAlunoPeriodo_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('ReqBolsaAlunoPeriodo_filter'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
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
            $object = new ReqBolsaAlunoPeriodo($key, FALSE); // instantiates the Active Record
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
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new ReqBolsaAlunoPeriodo;  // create an empty object
            $data = $this->form->getData(); // get form data as array

            //here
            if($data->data_fim < $data->data_inicio){
                new TMessage('error','A data de término não pode ser menor que a data de início!');
            }
            //var_dump($data_fim);
            //die;
            else{

            //$data->data_fim = '01/01/2018';
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved')); // success message
            $this->onReload(); // reload the listing
                }
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
                $object = new ReqBolsaAlunoPeriodo($key); // instantiates the Active Record
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
