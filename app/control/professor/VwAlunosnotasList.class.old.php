<?php
/**
 * VwAlunosnotasList Listing
 * @author  <your name here>
 */
class VwAlunosnotasList extends TPage
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

       // parent::setDatabase('dados_fei_t');
       // parent::setActiveRecord('VwAlunosnotas');
       // parent::setDefaultOrder('Codaluno', 'asc');
        // add the filter (filter field, operator, form field)
      //  parent::addFilterField('Codaluno', '=', 'Codaluno');
        
        // creates the form
        $this->form = new TQuickForm('form_search_VwAlunosnotas');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('VwAlunosnotas');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $CodTurmaetapa = new TEntry('CodTurmaetapa');
        $CodDisciplina = new TEntry('CodDisciplina');
        $TipoDis = new TEntry('TipoDis');
        $Resultado = new TEntry('Resultado');


        // add the fields
        $this->form->addQuickField('Codaluno', $Codaluno,  '100%' );
        $this->form->addQuickField('Nome', $Nome,  '100%' );
        $this->form->addQuickField('Codturmaetapa', $CodTurmaetapa,  '100%' );
        $this->form->addQuickField('Coddisciplina', $CodDisciplina,  '100%' );
        $this->form->addQuickField('Tipodis', $TipoDis,  '100%' );
        $this->form->addQuickField('Resultado', $Resultado,  '100%' );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwAlunosnotas_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addQuickAction(_t('New'),  new TAction(array('', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Codaluno', 'right');
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
        $column_CodTurmaetapa = new TDataGridColumn('fi_turma_etapa->Identificacao', 'Codturmaetapa', 'right');
        $column_CodDisciplina = new TDataGridColumn('fi_disciplina->Nomeusual', 'Coddisciplina', 'right');
        $column_TipoDis = new TDataGridColumn('TipoDis', 'Tipodis', 'left');
        $column_Resultado = new TDataGridColumn('Resultado', 'Resultado', 'left');
        $column_Ordem = new TDataGridColumn('Ordem', 'Ordem', 'left');
        $column_nota = new TDataGridColumn('nota_widget', 'Nota', 'right');

        $column_nota->setTransformer( function($value, $object, $row) {
            $widget = new TEntry('nota' . '_' . $object->Codaluno);
            $widget->setValue( $object->nota );
            $widget->setNumericMask(1,'.',',');
            $widget->setSize(120);
            $widget->setFormName('form_search_VwAlunosnotas');
            
            $action = new TAction( [$this, 'onSaveInline'] );
            $action->setParameter('column', 'nota');
            $widget->setExitAction( $action );
            return $widget;
        });



        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_CodTurmaetapa);
        $this->datagrid->addColumn($column_CodDisciplina);
        $this->datagrid->addColumn($column_TipoDis);
        $this->datagrid->addColumn($column_Resultado);
        $this->datagrid->addColumn($column_Ordem);
        $this->datagrid->addColumn($column_nota);

        
//         // create EDIT action
//         $action_edit = new TDataGridAction(array('', 'onEdit'));
//         //$action_edit->setUseButton(TRUE);
//         //$action_edit->setButtonClass('btn btn-default');
//         $action_edit->setLabel(_t('Edit'));
//         $action_edit->setImage('far:edit blue fa-lg');
//         $action_edit->setField('Codaluno');
//         $this->datagrid->addAction($action_edit);
//         
//         // create DELETE action
//         $action_del = new TDataGridAction(array($this, 'onDelete'));
//         //$action_del->setUseButton(TRUE);
//         //$action_del->setButtonClass('btn btn-default');
//         $action_del->setLabel(_t('Delete'));
//         $action_del->setImage('far:trash-alt red fa-lg');
//         $action_del->setField('Codaluno');
//         $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $this->datagrid->disableDefaultClick();
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Title', $this->form));
      //  $container->add($this->datagrid);
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
//     public function onInlineEdit($param)
//     {
//         try
//         {
//             // get the parameter $key
//             $field = $param['field'];
//             $key   = $param['key'];
//             $value = $param['value'];
//             
//             TTransaction::open('dados_fei_t'); // open a transaction with database
//             $object = new VwAlunosnotas($key); // instantiates the Active Record
//             $object->{$field} = $value;
//             $object->store(); // update the object in the database
//             TTransaction::close(); // close the transaction
//             
//             $this->onReload($param); // reload the listing
//             new TMessage('info', "Record Updated");
//         }
//         catch (Exception $e) // in case of exception
//         {
//             new TMessage('error', $e->getMessage()); // shows the exception error message
//             TTransaction::rollback(); // undo all pending operations
//         }
//     }
//  

/**
     * Save the datagrid objects
     */
    public static function onSaveInline($param)
    {
        $name   = $param['_field_name'];
        $value  = $param['_field_value'];
        $column = $param['column'];
        $parts  = explode('_', $name);
        $id     = end($parts);
        
        try
        {
            // open transaction
            TTransaction::open('dados_fei_t');
            
            $object = VwAlunosnotas::find($Codaluno);
            if ($object)
            {
                $object->$column = $value;
                $object->store();
            }
            
            // close transaction
            TTransaction::close();
        }
        catch (Exception $e)
        {
            // show the exception message
            new TMessage('error', $e->getMessage());
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
        TSession::setValue('VwAlunosnotasList_filter_Codaluno',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_Nome',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_CodTurmaetapa',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_CodDisciplina',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_TipoDis',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_Resultado',   NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', 'like', "%{$data->Codaluno}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_Codaluno',   $filter); // stores the filter in the session
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_Nome',   $filter); // stores the filter in the session
        }


        if (isset($data->CodTurmaetapa) AND ($data->CodTurmaetapa)) {
            $filter = new TFilter('CodTurmaetapa', 'like', "%{$data->CodTurmaetapa}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_CodTurmaetapa',   $filter); // stores the filter in the session
        }


        if (isset($data->CodDisciplina) AND ($data->CodDisciplina)) {
            $filter = new TFilter('CodDisciplina', 'like', "%{$data->CodDisciplina}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_CodDisciplina',   $filter); // stores the filter in the session
        }


        if (isset($data->TipoDis) AND ($data->TipoDis)) {
            $filter = new TFilter('TipoDis', 'like', "%{$data->TipoDis}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_TipoDis',   $filter); // stores the filter in the session
        }


        if (isset($data->Resultado) AND ($data->Resultado)) {
            $filter = new TFilter('Resultado', 'like', "%{$data->Resultado}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_Resultado',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('VwAlunosnotas_filter_data', $data);
        
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
            // open a transaction with database 'dados_fei_t'
            TTransaction::open('dados_fei_t');
            
            // creates a repository for VwAlunosnotas
            $repository = new TRepository('VwAlunosnotas');
            $limit = 100;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'Codaluno';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('VwAlunosnotasList_filter_Codaluno')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Codaluno')); // add the session filter
            }


            if (TSession::getValue('VwAlunosnotasList_filter_Nome')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Nome')); // add the session filter
            }


            if (TSession::getValue('VwAlunosnotasList_filter_CodTurmaetapa')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_CodTurmaetapa')); // add the session filter
            }


            if (TSession::getValue('VwAlunosnotasList_filter_CodDisciplina')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_CodDisciplina')); // add the session filter
            }


            if (TSession::getValue('VwAlunosnotasList_filter_TipoDis')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_TipoDis')); // add the session filter
            }


            if (TSession::getValue('VwAlunosnotasList_filter_Resultado')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Resultado')); // add the session filter
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
//     public function onDelete($param)
//     {
//         // define the delete action
//         $action = new TAction(array($this, 'Delete'));
//         $action->setParameters($param); // pass the key parameter ahead
//         
//         // shows a dialog to the user
//         new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
//     }
//     
//     /**
//      * Delete a record
//      */
//     public function Delete($param)
//     {
//         try
//         {
//             $key=$param['key']; // get the parameter $key
//             TTransaction::open('dados_fei_t'); // open a transaction with database
//             $object = new VwAlunosnotas($key, FALSE); // instantiates the Active Record
//             $object->delete(); // deletes the object from the database
//             TTransaction::close(); // close the transaction
//             $this->onReload( $param ); // reload the listing
//             new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); // success message
//         }
//         catch (Exception $e) // in case of exception
//         {
//             new TMessage('error', $e->getMessage()); // shows the exception error message
//             TTransaction::rollback(); // undo all pending operations
//         }
//     }
    


    
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
