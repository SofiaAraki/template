<?php
/**
 * TrechoProfessorFormList Form List
 * @author  <your name here>
 */
class TrechoProfessorFormList extends TPage
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
        
        $this->form = new BootstrapFormBuilder('form_TrechoProfessor');
        $this->form->setFormTitle('Trecho');
        

        // create the form fields
        $id = new THidden('id');
        $nome_trecho = new TEntry('nome_trecho');
        //$distancia = new TEntry('distancia');
        $distancia = new TNumeric('distancia', '2', ',', '.' );
        //$qtd_litro_diesel = new TEntry('qtd_litro_diesel');
        $qtd_litro_diesel = new TNumeric('qtd_litro_diesel', '2', ',', '.' );
        $qtd_litro_etanol = new TNumeric('qtd_litro_etanol', '2', ',', '.' );
        //$qtd_litro_etanol = new TEntry('qtd_litro_etanol');
        $qtd_litro_gasolina = new TNumeric('qtd_litro_gasolina', '2', ',', '.' );
        //$qtd_litro_gasolina = new TEntry('qtd_litro_gasolina');

        $nome_trecho->addValidation('"Trecho"', new TRequiredValidator());
        $distancia->addValidation('"Distância (km)"', new TRequiredValidator());
        $qtd_litro_diesel->addValidation('"Diesel (litros)"', new TRequiredValidator());
        $qtd_litro_etanol->addValidation('"Etanol (litros)"', new TRequiredValidator());
        $qtd_litro_gasolina->addValidation('"Gasolina (litros)"', new TRequiredValidator());


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ new TLabel('Trecho') ], [ $nome_trecho ] );
        $this->form->addFields( [ new TLabel('Distância (km)') ], [ $distancia ] );
        $this->form->addFields( [ new TLabel('Diesel (litros)') ], [ $qtd_litro_diesel ] );
        $this->form->addFields( [ new TLabel('Etanol (litros)') ], [ $qtd_litro_etanol ] );
        $this->form->addFields( [ new TLabel('Gasolina (litros)') ], [ $qtd_litro_gasolina ] );



        // set sizes
        //$id->setSize('100%');
        $nome_trecho->setSize('50%');
        $distancia->setSize('50%');
        $qtd_litro_diesel->setSize('50%');
        $qtd_litro_etanol->setSize('50%');
        $qtd_litro_gasolina->setSize('50%');



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_nome_trecho = new TDataGridColumn('nome_trecho', 'Trecho', 'left');
        $column_distancia = new TDataGridColumn('distancia', 'Distância (km)', 'left');
        $column_qtd_litro_diesel = new TDataGridColumn('qtd_litro_diesel', 'Diesel (litros)', 'left');
        $column_qtd_litro_etanol = new TDataGridColumn('qtd_litro_etanol', 'Etanol (litros)', 'left');
        $column_qtd_litro_gasolina = new TDataGridColumn('qtd_litro_gasolina', 'Gasolina (litros)', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome_trecho);
        $this->datagrid->addColumn($column_distancia);
        $this->datagrid->addColumn($column_qtd_litro_diesel);
        $this->datagrid->addColumn($column_qtd_litro_etanol);
        $this->datagrid->addColumn($column_qtd_litro_gasolina);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
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
            
            // creates a repository for TrechoProfessor
            $repository = new TRepository('TrechoProfessor');
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
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                	/**
                	$gasolina=number_format($object-> qtd_litro_gasolina,2);
                	$object->qtd_litro_gasolina = "$gasolina";

                	$diesel=number_format($object-> qtd_litro_diesel,2);
                	$object->qtd_litro_diesel = "$diesel";

                	$etanol=number_format($object-> qtd_litro_etanol,2);
                	$object->qtd_litro_etanol = "$etanol";

                	$distancia_formato=number_format($object-> distancia,1);
                	$object->distancia = "$distancia_formato"; 
                	*/           	
                	
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
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new TrechoProfessor($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
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
            $data = $this->form->getData(); // get form data as array
            
            $object = new TrechoProfessor;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
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
                $object = new TrechoProfessor($key); // instantiates the Active Record
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
