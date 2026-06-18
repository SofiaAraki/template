<?php
/**
 * TarefaColegioFormList Form List
 * @author  <your name here>
 */
class TarefaColegioFormList extends TPage
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
        
        $this->form = new BootstrapFormBuilder('form_TarefaColegio');
        $this->form->setFormTitle('Tarefas CNSC');
        

        // create the form fields
        $id_tarefa = new THidden('id_tarefa');
        $disciplina_tarefa = new TCombo('disciplina_tarefa');
            $disciplinas = array( 
                              'Arte' => 'Arte', 
                              'Ciências Físicas e Biológicas' => 'Ciências Físicas e Biológicas', 
                              'Ensino Religioso' => 'Ensino Religioso', 
                              'Geometria' => 'Geometria', 
                              'História' => 'História', 
                              'Inglês' => 'Inglês', 
                              'Língua Portuguesa' => 'Língua Portuguesa', 
                              'Matemática' => 'Matemática', 
                              'Redação' => 'Redação'
                        );
        
        $turma_tarefa = new TCombo('turma_tarefa');
            TTransaction::open('Felabs_DB');
                $loggedUnit = TSession::getValue('userunitid');
            TTransaction::close();

            TTransaction::open('dados_fei');
            
            $criteria1 = new TCriteria;
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

        
        


        $descricao_tarefa = new TText('descricao_tarefa');
        $data_tarefa = new TDate('data_tarefa');
        $dataentrega_tarefa = new TDate('dataentrega_tarefa');


        // add the fields
        $this->form->addFields( [ new TLabel('ID') ], [ $id_tarefa ] );
        $this->form->addFields( [ new TLabel('Disciplina:') ], [ $disciplina_tarefa ] );
        $this->form->addFields( [ new TLabel('Turma:') ], [ $turma_tarefa ] );
        $this->form->addFields( [ new TLabel('Descrição:') ], [ $descricao_tarefa ] );
        $this->form->addFields( [ new TLabel('Data da Tarefa:') ], [ $data_tarefa ] );
        $this->form->addFields( [ new TLabel('Data de Entrega:') ], [ $dataentrega_tarefa ] );

        $disciplina_tarefa->addValidation('Disciplina:', new TRequiredValidator);
        //$turma_tarefa->addValidation('Turma:', new TRequiredValidator);
        $data_tarefa->addValidation('Data Tarefa', new TRequiredValidator);

        $disciplina_tarefa->addItems($disciplinas);
        $data_tarefa->setMask('dd/mm/yyyy');
        $data_tarefa->setDatabaseMask('yyyy-mm-dd');

        $dataentrega_tarefa->setMask('dd/mm/yyyy');
        $dataentrega_tarefa->setDatabaseMask('yyyy-mm-dd');


        // set sizes
        $id_tarefa->setSize('100%');
        $disciplina_tarefa->setSize('100%');
        //$turma_tarefa->setSize('100%');
        $descricao_tarefa->setSize('100%');
        $data_tarefa->setSize('30%');
        $dataentrega_tarefa->setSize('30%');





        if (!empty($id_tarefa))
        {
            $id_tarefa->setEditable(FALSE);
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
        $column_id_tarefa = new TDataGridColumn('id_tarefa', 'ID', 'left');
        $column_disciplina_tarefa = new TDataGridColumn('disciplina_tarefa', 'Disciplina', 'left');
        $column_turma_tarefa = new TDataGridColumn('turma_tarefa', 'Turma', 'left');
        $column_descricao_tarefa = new TDataGridColumn('descricao_tarefa', 'Descrição', 'left','50%');
        $column_data_tarefa = new TDataGridColumn('data_tarefa', 'Data da Tarefa', 'center');
        $column_dataentrega_tarefa = new TDataGridColumn('dataentrega_tarefa', 'Data de Entrega', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id_tarefa);
        $this->datagrid->addColumn($column_disciplina_tarefa);
        $this->datagrid->addColumn($column_turma_tarefa);
        $this->datagrid->addColumn($column_descricao_tarefa);
        $this->datagrid->addColumn($column_data_tarefa);
        $this->datagrid->addColumn($column_dataentrega_tarefa);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id_tarefa');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id_tarefa');
        
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
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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
            
            // creates a repository for TarefaColegio
            $repository = new TRepository('TarefaColegio');
            $limit = 100;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_tarefa';
                $param['direction'] = 'desc';
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
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
         
            $loggedUnit = TSession::getValue('userunitid');
            

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new TarefaColegio;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            
            $object-> id_entidade = $loggedUnit;
            
            //var_dump($object);
            //die();


            $object->store(); // save the object
            
            // get the generated id_tarefa
            $data->id_tarefa = $object->id_tarefa;
            
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
                $object = new TarefaColegio($key); // instantiates the Active Record
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
