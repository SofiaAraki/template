<?php
/**
 * TarefaColegioList Listing
 * @author  <your name here>
 */
class TarefaColegioList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_TarefaColegio');
        $this->form->setFormTitle('Tarefas CNSC');
        

        // create the form fields
        $turma_tarefa = new TCombo('turma_tarefa');
            TTransaction::open('Felabs_DB');
                $loggedUnit = TSession::getValue('userunitid');
                $loggedUser = TSession::getValue('systemuser_codlegado');
            TTransaction::close();

            TTransaction::open('dados_fei');
            
            $criteria1 = new TCriteria;
            //$criteria1->add(new TFilter('Codaluno', '=', $loggedUser-> systemuser_codlegado), TExpression::AND_OPERATOR);
            $criteria1->add(new TFilter('CodEntidade', '=', '1'), TExpression::AND_OPERATOR);
            $criteria1->add(new TFilter('AnoMatricula', '=', '2018'), TExpression::AND_OPERATOR);
            $criteria1->add(new TFilter('SemestreMatricula', '=', '1'), TExpression::AND_OPERATOR);
            $criteria1->setProperty('order', 'IdentificacaoMatricula asc');


            $turmas = VwAlunoMatriculaEtapa::getObjects($criteria1);
            $options = [];
            if ($turmas)
            {
                foreach ($turmas as $turma)
                {
                    $options[ $turma-> IdentificacaoMatricula ] = $turma-> IdentificacaoMatricula;                               
                }            
            }
            
            $turma_tarefa->addItems($options);
            TTransaction::close();
        $data_tarefa = new TDate('data_tarefa');

        $data_tarefa->setMask('dd/mm/yyyy');
        $data_tarefa->setDatabaseMask('yyyy-mm-dd');


        // add the fields
        $this->form->addFields( [ new TLabel('Escolha a Turma:') ], [ $turma_tarefa ] );
        $this->form->addFields( [ new TLabel('Escolha a Data da Tarefa:') ], [ $data_tarefa ] );


        // set sizes
        $turma_tarefa->setSize('30%');
        $data_tarefa->setSize('30%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('TarefaColegio_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['TarefaColegioFormList', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id_tarefa = new TDataGridColumn('id_tarefa', 'ID', 'right');
        $column_disciplina_tarefa = new TDataGridColumn('disciplina_tarefa', 'Disciplina', 'left');
        $column_turma_tarefa = new TDataGridColumn('turma_tarefa', 'Turma', 'left');
        $column_descricao_tarefa = new TDataGridColumn('descricao_tarefa', 'Descrição', 'left', '50%');
        $column_data_tarefa = new TDataGridColumn('data_tarefa', 'Data da Tarefa', 'center');
        $column_dataentrega_tarefa = new TDataGridColumn('dataentrega_tarefa', 'Data de Entrega', 'center');
        
        //$column_data_tarefa->setTransformer(array($this, 'formatDate'));


        // add the columns to the DataGrid
       // $this->datagrid->addColumn($column_id_tarefa);
        $this->datagrid->addColumn($column_disciplina_tarefa);
        $this->datagrid->addColumn($column_turma_tarefa);
        $this->datagrid->addColumn($column_descricao_tarefa);
        $this->datagrid->addColumn($column_data_tarefa);
        $this->datagrid->addColumn($column_dataentrega_tarefa);

        
        // create EDIT action
        //$action_edit = new TDataGridAction(['TarefaColegioFormList', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        //$action_edit->setLabel(_t('Edit'));
        //$action_edit->setImage('far:edit blue fa-lg');
        //$action_edit->setField('id_tarefa');
        //$this->datagrid->addAction($action_edit);
        

        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
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
            $object = new TarefaColegio($key); // instantiates the Active Record
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
        TSession::setValue('TarefaColegioList_filter_turma_tarefa',   NULL);
        TSession::setValue('TarefaColegioList_filter_data_tarefa',   NULL);

        if (isset($data->turma_tarefa) AND ($data->turma_tarefa)) {
            $filter = new TFilter('turma_tarefa', 'like', "%{$data->turma_tarefa}%"); // create the filter
            TSession::setValue('TarefaColegioList_filter_turma_tarefa',   $filter); // stores the filter in the session
        }


        if (isset($data->data_tarefa) AND ($data->data_tarefa)) {
            $filter = new TFilter('data_tarefa', 'like', "%{$data->data_tarefa}%"); // create the filter
            TSession::setValue('TarefaColegioList_filter_data_tarefa',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('TarefaColegio_filter_data', $data);
        
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

            $loggedUnit = TSession::getValue('userunitid');

            if ($loggedUnit <> 1) {

                 new TMessage('error', "Você não pertece a Unidade CNSC!");
            }
            

            //var_dump($loggedUnit);
            //die();
            
            
            // creates a repository for TarefaColegio
            $repository = new TRepository('TarefaColegio');
            $limit = 10;
            // creates a criteria
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('id_entidade', '=', $loggedUnit));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_tarefa';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('TarefaColegioList_filter_turma_tarefa')) {
                $criteria->add(TSession::getValue('TarefaColegioList_filter_turma_tarefa')); // add the session filter
            }


            if (TSession::getValue('TarefaColegioList_filter_data_tarefa')) {
                $criteria->add(TSession::getValue('TarefaColegioList_filter_data_tarefa')); // add the session filter
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
                    $object->data_tarefa = TDate::date2br($object->data_tarefa);
                    $object->dataentrega_tarefa = TDate::date2br($object->dataentrega_tarefa);
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
            $this->form->clear(); 
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
            $key=$param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new TarefaColegio($key, FALSE); // instantiates the Active Record
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
     * method show()
     * Shows the page
     */
    public function show()
    {
         

        parent::show();
    }
}
