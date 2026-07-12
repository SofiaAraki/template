<?php
/**
 * QuestionarioQuestaoList Listing
 * @author  <your name here>
 */
class QuestionarioQuestaoList extends TPage
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
        $this->form = new TQuickForm('form_search_QuestionarioQuestao');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('QuestionarioQuestao');
        

        // create the form fields
        $id = new TEntry('id');
        $questionario_id = new TEntry('questionario_id');
        $tipo = new TCombo('tipo');
        $conteudo = new TEntry('conteudo');

        $items = [];
        $items['U'] = 'Única escolha';

        $tipo->addItems($items);

        // add the fields
        $this->form->addQuickField('Id', $id,  '100%' );
     //   $this->form->addQuickField('Questionario Id', $questionario_id,  '100%' );
        $this->form->addQuickField('Tipo', $tipo,  '100%' );
        $this->form->addQuickField('Conteúdo', $conteudo,  '100%' );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('QuestionarioQuestao_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        $this->form->addQuickAction('Adicionar Questão',  new TAction(array('QuestionarioQuestaoForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_questionario_id = new TDataGridColumn('questionario_id', 'Questionario Id', 'right');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        $column_conteudo = new TDataGridColumn('conteudo', 'Conteudo', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
    //    $this->datagrid->addColumn($column_questionario_id);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_conteudo);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('QuestionarioQuestaoForm', 'onEdit'));
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

        TTransaction::open('Felabs_DB');

        $questionario = new Questionario(TSession::getValue('questionarioid'));
        TTransaction::close();


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Buscar Questão', $this->form));
        $container->add(TPanelGroup::pack("Questões em $questionario->titulo", $this->datagrid, $this->pageNavigation));
        
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
            $object = new QuestionarioQuestao($key); // instantiates the Active Record
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
        TSession::setValue('QuestionarioQuestaoList_filter_id',   NULL);
        TSession::setValue('QuestionarioQuestaoList_filter_questionario_id',   NULL);
        TSession::setValue('QuestionarioQuestaoList_filter_tipo',   NULL);
        TSession::setValue('QuestionarioQuestaoList_filter_conteudo',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('QuestionarioQuestaoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->questionario_id) AND ($data->questionario_id)) {
            $filter = new TFilter('questionario_id', 'like', "%{$data->questionario_id}%"); // create the filter
            TSession::setValue('QuestionarioQuestaoList_filter_questionario_id',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', 'like', "%{$data->tipo}%"); // create the filter
            TSession::setValue('QuestionarioQuestaoList_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->conteudo) AND ($data->conteudo)) {
            $filter = new TFilter('conteudo', 'like', "%{$data->conteudo}%"); // create the filter
            TSession::setValue('QuestionarioQuestaoList_filter_conteudo',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('QuestionarioQuestao_filter_data', $data);
        
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
            
            // creates a repository for QuestionarioQuestao
            $repository = new TRepository('QuestionarioQuestao');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('questionario_id', '=', TSession::getValue('questionarioid')));

            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('QuestionarioQuestaoList_filter_id')) {
                $criteria->add(TSession::getValue('QuestionarioQuestaoList_filter_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioQuestaoList_filter_questionario_id')) {
                $criteria->add(TSession::getValue('QuestionarioQuestaoList_filter_questionario_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioQuestaoList_filter_tipo')) {
                $criteria->add(TSession::getValue('QuestionarioQuestaoList_filter_tipo')); // add the session filter
            }


            if (TSession::getValue('QuestionarioQuestaoList_filter_conteudo')) {
                $criteria->add(TSession::getValue('QuestionarioQuestaoList_filter_conteudo')); // add the session filter
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
                    if($object->tipo == 'U')
                    {
                        $object->tipo = 'Única Escolha';
                    }
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
            $object = new QuestionarioQuestao($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database

            $criteria = new TCriteria;
            $criteria->add(new TFilter('questao_id', '=', $key));

            $alternativas = QuestionarioAlternativa::getObjects($criteria);

            if(!empty($alternativas))
            {
                foreach($alternativas as $alternativa)
                {
                    $objAlternativa = new QuestionarioAlternativa($alternativa->id);
                    $objAlternativa->delete();
                  //  var_dump($alternativa);
                   // die;
                }
            }







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
