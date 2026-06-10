<?php
/**
 * VwProfessordisciplinassemestreList Listing
 * @author  <your name here>
 */
class VwProfessordisciplinassemestreList extends TPage
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
        $this->form = new TQuickForm('form_search_VwProfessordisciplinassemestre');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('VwProfessordisciplinassemestre');
        

        
        // create the form fields
        $NomeProfessor = new TEntry('NomeProfessor');
        $NomeCurso = new TEntry('NomeCurso');
        $Etapa = new TEntry('Etapa');
        $NomeDisciplina = new TEntry('NomeDisciplina');
        $Identificacao = new TEntry('Identificacao');
        $Ano = new TEntry('Ano');
        $Semestre = new TEntry('Semestre');
        $NomeEntidade = new TEntry('NomeEntidade');


        // add the fields
        $this->form->addQuickField('Professor', $NomeProfessor,  '100%' );
        $this->form->addQuickField('Curso', $NomeCurso,  '100%' );
 //       $this->form->addQuickField('Etapa', $Etapa,  '100%' );
        $this->form->addQuickField('Disciplina', $NomeDisciplina,  '100%' );
        /*$this->form->addQuickField('Identificacao', $Identificacao,  '100%' );
        $this->form->addQuickField('Ano', $Ano,  '100%' );
        $this->form->addQuickField('Semestre', $Semestre,  '100%' );
        $this->form->addQuickField('Mantida', $NomeEntidade,  '100%' );*/


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwProfessordisciplinassemestre_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction( 'Show results', new TAction(array($this, 'showResults')), 'far:check-circle green' );
        
      //  $this->form->addQuickAction(_t('New'),  new TAction(array('', 'onEdit')), 'bs:plus-sign green');
        












        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns
        $column_NomeProfessor = new TDataGridColumn('NomeProfessor', 'Professor', 'left');
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left');
        $column_Etapa = new TDataGridColumn('Etapa', 'Etapa', 'left');
        $column_Identificacao = new TDataGridColumn('Identificacao', 'Identificacao', 'left');
        //$column_Ano = new TDataGridColumn('Ano', 'Ano', 'left');
        //$column_Semestre = new TDataGridColumn('Semestre', 'Semestre', 'left');
        //$column_NomeEntidade = new TDataGridColumn('NomeEntidade', 'Nomeentidade', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_NomeProfessor);
        $this->datagrid->addColumn($column_NomeCurso);
        $this->datagrid->addColumn($column_NomeDisciplina);
        $this->datagrid->addColumn($column_Etapa);
        $this->datagrid->addColumn($column_Identificacao);
        //$this->datagrid->addColumn($column_Ano);
        //$this->datagrid->addColumn($column_Semestre);
//        $this->datagrid->addColumn($column_NomeEntidade);

        
        // create EDIT action
 /*       $action_edit = new TDataGridAction(array('', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('CodProfessor');
        $this->datagrid->addAction($action_edit);*/
        
        // create DELETE action
        //$action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        //$action_del->setLabel(_t('Delete'));
        //$action_del->setImage('far:trash-alt red fa-lg');
        //$action_del->setField('CodProfessor');
        //$this->datagrid->addAction($action_del);
        
        


         // creates the datagrid actions
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(TRUE);
        $action_select->setButtonClass('btn btn-default');
        $action_select->setLabel(AdiantiCoreTranslator::translate('Select'));
        $action_select->setImage('far:check-circle blue');
        $action_select->setField('CodDisciplina');
        
        $this->datagrid->addAction($action_select);

        //$this->datagrid->disableDefaultClick();



        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Atribuição de Disciplinas', $this->form));
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
/*    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Dados_Fei_T'); // open a transaction with database
            $object = new VwProfessordisciplinassemestre($key); // instantiates the Active Record
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
    }*/
     
     public function onSelect($param)
    {
{
        
        var_dump($param);
        
        die();
        // get the selected objects from session 
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');

        TTransaction::open('Dados_Fei_T');
        $object = new VwProfessordisciplinassemestre($param['CodProfessor']); // load the object



        if (isset($selected_objects[$object->id]))
        {
            unset($selected_objects[$object->id]);
        }
        else
        {
            $selected_objects[$object->id] = $object->toArray(); // add the object inside the array
        }
        TSession::setValue(__CLASS__.'_selected_objects', $selected_objects); // put the array back to the session
        
        TTransaction::close();

        
        
        // reload datagrids
        $this->onReload( func_get_arg(0) );
    }
           }
    
     public function showResults()
    {
        $datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        
        $datagrid->addQuickColumn('CodProfessor', 'CodProfessor', 'left');
        $datagrid->addQuickColumn('NomeProfessor', 'NomeProfessor', 'left');
        $datagrid->addQuickColumn('CodDisciplina', 'CodDisciplina', 'left');
        
        // create the datagrid model
        $datagrid->createModel();
        
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        ksort($selected_objects);
        if ($selected_objects)
        {
            $datagrid->clear();
            foreach ($selected_objects as $selected_object)
            {
                $datagrid->addItem( (object) $selected_object );
            }
        }
        
        $win = TWindow::create('Results', 0.6, 0.6);
        $win->add($datagrid);
        $win->show();
    }
    
   /* public function Select($param)
        {
            try
            {
                $key=$param['key']; // get the parameter $key
                TTransaction::open('Dados_Fei_T'); // open a transaction with database

                $object = new VwProfessordisciplinassemestre($key, FALSE); // instantiates the Active Record

                $disciplina_prof = $object->CodDisciplina;
               
                var_dump($disciplina_prof);
                die();


               $usuarios = new SystemUser($usuario, FALSE);
                $emailusuario = $usuarios->email;

                $object->situacao = "ABONO DE AUSÊNCIA";
                $object->store(); // deletes the object from the database
               
 
                TTransaction::close(); // close the transaction
                $this->onReload( $param ); // reload the listing
                new TMessage('info', 'Apontamento APROVADO COM ABONO DE AUSÊNCIA foi realizado com sucesso!'); // success message

                
            }
            catch (Exception $e) // in case of exception
            {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations
            }
        }*/

    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeProfessor',   NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso',   NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Etapa',   NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina',   NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Identificacao',   NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Ano',   NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Semestre',   NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeEntidade',   NULL);

        if (isset($data->NomeProfessor) AND ($data->NomeProfessor)) {
            $filter = new TFilter('NomeProfessor', 'like', "%{$data->NomeProfessor}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeProfessor',   $filter); // stores the filter in the session
        }


        if (isset($data->NomeCurso) AND ($data->NomeCurso)) {
            $filter = new TFilter('NomeCurso', 'like', "%{$data->NomeCurso}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso',   $filter); // stores the filter in the session
        }


        if (isset($data->Etapa) AND ($data->Etapa)) {
            $filter = new TFilter('Etapa', 'like', "%{$data->Etapa}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_Etapa',   $filter); // stores the filter in the session
        }


        if (isset($data->NomeDisciplina) AND ($data->NomeDisciplina)) {
            $filter = new TFilter('NomeDisciplina', 'like', "%{$data->NomeDisciplina}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina',   $filter); // stores the filter in the session
        }


        if (isset($data->Identificacao) AND ($data->Identificacao)) {
            $filter = new TFilter('Identificacao', 'like', "%{$data->Identificacao}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_Identificacao',   $filter); // stores the filter in the session
        }


        if (isset($data->Ano) AND ($data->Ano)) {
            $filter = new TFilter('Ano', 'like', "%{$data->Ano}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_Ano',   $filter); // stores the filter in the session
        }


        if (isset($data->Semestre) AND ($data->Semestre)) {
            $filter = new TFilter('Semestre', 'like', "%{$data->Semestre}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_Semestre',   $filter); // stores the filter in the session
        }


        if (isset($data->NomeEntidade) AND ($data->NomeEntidade)) {
            $filter = new TFilter('NomeEntidade', 'like', "%{$data->NomeEntidade}%"); // create the filter
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeEntidade',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('VwProfessordisciplinassemestre_filter_data', $data);
        
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
            // open a transaction with database 'Dados_Fei_T'
            TTransaction::open('Felabs_DB');
            
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            // creates a repository for VwProfessordisciplinassemestre
            
            $professor = $logged-> systemuser_codlegado;
            TTransaction::close();

            TTransaction::open('Dados_Fei_T');
         
            $repository = new TRepository('VwProfessordisciplinassemestre');
            $limit = 50;
            
            $util = new Util();
            $message = $util->semestre."-".$util->ano;


            // creates a criteria
            $criteria = new TCriteria;
            
            $criteria->add(new TFilter('CodProfessor', '=', $logged-> systemuser_codlegado));
            $criteria->add(new TFilter('Ano', '=', '2017'), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Semestre', '=', '2'), TExpression::AND_OPERATOR);

            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'CodProfessor';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeProfessor')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeProfessor')); // add the session filter
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')); // add the session filter
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_Etapa')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_Etapa')); // add the session filter
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')); // add the session filter
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_Identificacao')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_Identificacao')); // add the session filter
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_Ano')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_Ano')); // add the session filter
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_Semestre')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_Semestre')); // add the session filter
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeEntidade')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeEntidade')); // add the session filter
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
/*    public function onDelete($param)
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
/*    public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('Dados_Fei_T'); // open a transaction with database
            $object = new VwProfessordisciplinassemestre($key, FALSE); // instantiates the Active Record
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
    }*/
    



    
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
