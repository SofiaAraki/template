<?php
/**
 * MarketingBriefingList Listing
 * @author  <your name here>
 */
class MarketingBriefingList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_MarketingBriefing');
        $this->form->setFormTitle('MarketingBriefing');
        

        // create the form fields
        $solicitante = new TEntry('solicitante');
        $departamento = new TEntry('departamento');
        $mantida = new TEntry('mantida');


        // add the fields
        $this->form->addFields( [ new TLabel('Solicitante') ], [ $solicitante ] );
        $this->form->addFields( [ new TLabel('Departamento') ], [ $departamento ] );
        $this->form->addFields( [ new TLabel('Mantida') ], [ $mantida ] );


        // set sizes
        $solicitante->setSize('100%');
        $departamento->setSize('100%');
        $mantida->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['MarketingBriefingFormList_MKT', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_solicitante = new TDataGridColumn('system_user->name', 'Solicitante', 'left');
        $column_departamento = new TDataGridColumn('departamento', 'Departamento', 'left');
        $column_mantida = new TDataGridColumn('mantida', 'Mantida', 'left');
        //$column_objetivo_campanha = new TDataGridColumn('objetivo_campanha', 'Objetivo Campanha', 'left');
        //column_comunicacao_sugerida = new TDataGridColumn('comunicacao_sugerida', 'Comunicacao Sugerida', 'left');
        //$column_titulo_evento = new TDataGridColumn('titulo_evento', 'Titulo Evento', 'left');
        //$column_data_evento = new TDataGridColumn('data_evento', 'Data Evento', 'left');
        //$column_local_evento = new TDataGridColumn('local_evento', 'Local Evento', 'left');
        //$column_tipo_inscricoes = new TDataGridColumn('tipo_inscricoes', 'Tipo Inscricoes', 'left');
        //$column_descritivo_evento = new TDataGridColumn('descritivo_evento', 'Descritivo Evento', 'left');
        //$column_contato_principal = new TDataGridColumn('contato_principal', 'Contato Principal', 'left');
        //$column_locais_divulgacao = new TDataGridColumn('locais_divulgacao', 'Locais Divulgacao', 'left');
        //$column_publico_alvo = new TDataGridColumn('publico_alvo', 'Publico Alvo', 'left');
        //$column_outras_info = new TDataGridColumn('outras_info', 'Outras Info', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        //$column_declarar_ciencia = new TDataGridColumn('declarar_ciencia', 'Declarar Ciencia', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data Reg', 'left');
        //$column_autorizado_por = new TDataGridColumn('autorizado_por', 'Autorizado Por', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_solicitante);
        $this->datagrid->addColumn($column_departamento);
        $this->datagrid->addColumn($column_mantida);
       /* $this->datagrid->addColumn($column_objetivo_campanha);
        $this->datagrid->addColumn($column_comunicacao_sugerida);
        $this->datagrid->addColumn($column_titulo_evento);
        $this->datagrid->addColumn($column_data_evento);
        $this->datagrid->addColumn($column_local_evento);
        $this->datagrid->addColumn($column_tipo_inscricoes);
        $this->datagrid->addColumn($column_descritivo_evento);
        $this->datagrid->addColumn($column_contato_principal);
        $this->datagrid->addColumn($column_locais_divulgacao);
        $this->datagrid->addColumn($column_publico_alvo);
        $this->datagrid->addColumn($column_outras_info);*/
        $this->datagrid->addColumn($column_status);
        //$this->datagrid->addColumn($column_declarar_ciencia);
        $this->datagrid->addColumn($column_data_reg);
        //$this->datagrid->addColumn($column_autorizado_por);


        $action1 = new TDataGridAction(['MarketingBriefingFormList_MKT', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
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
            $object = new MarketingBriefing($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_solicitante',   NULL);
        TSession::setValue(__CLASS__.'_filter_departamento',   NULL);
        TSession::setValue(__CLASS__.'_filter_mantida',   NULL);

        if (isset($data->solicitante) AND ($data->solicitante)) {
            $filter = new TFilter('solicitante', 'like', "%{$data->solicitante}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_solicitante',   $filter); // stores the filter in the session
        }


        if (isset($data->departamento) AND ($data->departamento)) {
            $filter = new TFilter('departamento', 'like', "%{$data->departamento}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_departamento',   $filter); // stores the filter in the session
        }


        if (isset($data->mantida) AND ($data->mantida)) {
            $filter = new TFilter('mantida', 'like', "%{$data->mantida}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_mantida',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
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
            
            // creates a repository for MarketingBriefing
            $repository = new TRepository('MarketingBriefing');
            $limit = 30;
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
            

            if (TSession::getValue(__CLASS__.'_filter_solicitante')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_solicitante')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_departamento')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_departamento')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_mantida')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_mantida')); // add the session filter
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
                    if($object->status == "EM ANÁLISE")
                    {
                        $object->status = '<span class="label label-warning">EM ANÁLISE</span>';
                    }
                    elseif($object->status == "EM PROGRESSO")
                    {
                        $object->status = '<span class="label label-primary">EM PROGRESSO</span>';
                    }
                    elseif($object->status == "CONCLUÍDO")
                    {
                        $object->status = '<span class="label label-success">CONCLUÍDO</span>';
                    }
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
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
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
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new MarketingBriefing($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
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
