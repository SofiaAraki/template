<?php
/**
 * QuestionarioRespostaList Listing
 * @author  <your name here>
 */
class QuestionarioRespostaList extends TPage
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
        $this->form = new TQuickForm('form_search_QuestionarioResposta');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('QuestionarioResposta');
        

        // create the form fields
        $id = new TEntry('id');
        $questionario_periodo_id = new TEntry('questionario_periodo_id');
        $questionario_id = new TEntry('questionario_id');
        $questao_id = new TEntry('questao_id');
        $alternativa_id = new TEntry('alternativa_id');
        $system_user_id = new TEntry('system_user_id');
        $system_unit_id = new TEntry('system_unit_id');
        $cod_disciplina = new TEntry('cod_disciplina');
        $cod_professor = new TEntry('cod_professor');
        $ano = new TEntry('ano');
        $semestre = new TEntry('semestre');
        $conteudo_alternativa = new TEntry('conteudo_alternativa');
        $num_questao = new TEntry('num_questao');
        $cod_curso = new TEntry('cod_curso');


        // add the fields
        $this->form->addQuickField('Id', $id,  '100%' );
        $this->form->addQuickField('Questionario Periodo Id', $questionario_periodo_id,  '100%' );
        $this->form->addQuickField('Questionario Id', $questionario_id,  '100%' );
        $this->form->addQuickField('Questao Id', $questao_id,  '100%' );
        $this->form->addQuickField('Alternativa Id', $alternativa_id,  '100%' );
        $this->form->addQuickField('System User Id', $system_user_id,  '100%' );
        $this->form->addQuickField('System Unit Id', $system_unit_id,  '100%' );
        $this->form->addQuickField('Cod Disciplina', $cod_disciplina,  '100%' );
        $this->form->addQuickField('Cod Professor', $cod_professor,  '100%' );
        $this->form->addQuickField('Ano', $ano,  '100%' );
        $this->form->addQuickField('Semestre', $semestre,  '100%' );
        $this->form->addQuickField('Conteudo Alternativa', $conteudo_alternativa,  '100%' );
        $this->form->addQuickField('Num Questao', $num_questao,  '100%' );
        $this->form->addQuickField('Cod Curso', $cod_curso,  '100%' );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('QuestionarioResposta_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
     //   $this->form->addQuickAction(_t('New'),  new TAction(array('QuestionarioRespostaForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_questionario_periodo_id = new TDataGridColumn('questionario_periodo_id', 'Questionario Periodo Id', 'right');
        $column_questionario_id = new TDataGridColumn('questionario_id', 'Questionario Id', 'right');
        $column_questao_id = new TDataGridColumn('questao_id', 'Questao Id', 'right');
        $column_alternativa_id = new TDataGridColumn('alternativa_id', 'Alternativa Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'System User Id', 'right');
        $column_system_unit_id = new TDataGridColumn('system_unit_id', 'System Unit Id', 'left');
        $column_cod_disciplina = new TDataGridColumn('cod_disciplina', 'Cod Disciplina', 'left');
        $column_cod_professor = new TDataGridColumn('cod_professor', 'Cod Professor', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'right');
        $column_semestre = new TDataGridColumn('semestre', 'Semestre', 'right');
        $column_conteudo_alternativa = new TDataGridColumn('conteudo_alternativa', 'Conteudo Alternativa', 'left');
        $column_num_questao = new TDataGridColumn('num_questao', 'Num Questao', 'left');
        $column_cod_curso = new TDataGridColumn('cod_curso', 'Cod Curso', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_questionario_periodo_id);
        $this->datagrid->addColumn($column_questionario_id);
        $this->datagrid->addColumn($column_questao_id);
        $this->datagrid->addColumn($column_alternativa_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_system_unit_id);
        $this->datagrid->addColumn($column_cod_disciplina);
        $this->datagrid->addColumn($column_cod_professor);
        $this->datagrid->addColumn($column_ano);
        $this->datagrid->addColumn($column_semestre);
        $this->datagrid->addColumn($column_conteudo_alternativa);
        $this->datagrid->addColumn($column_num_questao);
        $this->datagrid->addColumn($column_cod_curso);

    /*    
        // create EDIT action
        $action_edit = new TDataGridAction(array('QuestionarioRespostaForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        */
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
            $object = new QuestionarioResposta($key); // instantiates the Active Record
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
        TSession::setValue('QuestionarioRespostaList_filter_id',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_questionario_periodo_id',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_questionario_id',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_questao_id',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_alternativa_id',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_system_user_id',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_system_unit_id',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_cod_disciplina',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_cod_professor',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_ano',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_semestre',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_conteudo_alternativa',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_num_questao',   NULL);
        TSession::setValue('QuestionarioRespostaList_filter_cod_curso',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->questionario_periodo_id) AND ($data->questionario_periodo_id)) {
            $filter = new TFilter('questionario_periodo_id', 'like', "%{$data->questionario_periodo_id}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_questionario_periodo_id',   $filter); // stores the filter in the session
        }


        if (isset($data->questionario_id) AND ($data->questionario_id)) {
            $filter = new TFilter('questionario_id', 'like', "%{$data->questionario_id}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_questionario_id',   $filter); // stores the filter in the session
        }


        if (isset($data->questao_id) AND ($data->questao_id)) {
            $filter = new TFilter('questao_id', 'like', "%{$data->questao_id}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_questao_id',   $filter); // stores the filter in the session
        }


        if (isset($data->alternativa_id) AND ($data->alternativa_id)) {
            $filter = new TFilter('alternativa_id', 'like', "%{$data->alternativa_id}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_alternativa_id',   $filter); // stores the filter in the session
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', 'like', "%{$data->system_user_id}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_system_user_id',   $filter); // stores the filter in the session
        }


        if (isset($data->system_unit_id) AND ($data->system_unit_id)) {
            $filter = new TFilter('system_unit_id', 'like', "%{$data->system_unit_id}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_system_unit_id',   $filter); // stores the filter in the session
        }


        if (isset($data->cod_disciplina) AND ($data->cod_disciplina)) {
            $filter = new TFilter('cod_disciplina', 'like', "%{$data->cod_disciplina}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_cod_disciplina',   $filter); // stores the filter in the session
        }


        if (isset($data->cod_professor) AND ($data->cod_professor)) {
            $filter = new TFilter('cod_professor', 'like', "%{$data->cod_professor}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_cod_professor',   $filter); // stores the filter in the session
        }


        if (isset($data->ano) AND ($data->ano)) {
            $filter = new TFilter('ano', 'like', "%{$data->ano}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_ano',   $filter); // stores the filter in the session
        }


        if (isset($data->semestre) AND ($data->semestre)) {
            $filter = new TFilter('semestre', 'like', "%{$data->semestre}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_semestre',   $filter); // stores the filter in the session
        }


        if (isset($data->conteudo_alternativa) AND ($data->conteudo_alternativa)) {
            $filter = new TFilter('conteudo_alternativa', 'like', "%{$data->conteudo_alternativa}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_conteudo_alternativa',   $filter); // stores the filter in the session
        }


        if (isset($data->num_questao) AND ($data->num_questao)) {
            $filter = new TFilter('num_questao', 'like', "%{$data->num_questao}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_num_questao',   $filter); // stores the filter in the session
        }


        if (isset($data->cod_curso) AND ($data->cod_curso)) {
            $filter = new TFilter('cod_curso', 'like', "%{$data->cod_curso}%"); // create the filter
            TSession::setValue('QuestionarioRespostaList_filter_cod_curso',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('QuestionarioResposta_filter_data', $data);
        
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
            
            // creates a repository for QuestionarioResposta
            $repository = new TRepository('QuestionarioResposta');
            $limit = 30;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('QuestionarioRespostaList_filter_id')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_questionario_periodo_id')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_questionario_periodo_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_questionario_id')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_questionario_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_questao_id')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_questao_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_alternativa_id')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_alternativa_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_system_user_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_system_unit_id')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_system_unit_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_cod_disciplina')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_cod_disciplina')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_cod_professor')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_cod_professor')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_ano')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_ano')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_semestre')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_semestre')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_conteudo_alternativa')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_conteudo_alternativa')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_num_questao')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_num_questao')); // add the session filter
            }


            if (TSession::getValue('QuestionarioRespostaList_filter_cod_curso')) {
                $criteria->add(TSession::getValue('QuestionarioRespostaList_filter_cod_curso')); // add the session filter
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
            $object = new QuestionarioResposta($key, FALSE); // instantiates the Active Record
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
