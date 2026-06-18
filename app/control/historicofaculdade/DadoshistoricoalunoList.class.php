<?php
/**
 * VwDadoshistoricoalunoList Listing
 * @author  <your name here>
 */
class DadoshistoricoalunoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_VwDadoshistoricoaluno');
        $this->form->setFormTitle('Buscar Histórico');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $CPF = new TEntry('CPF');


        // add the fields
        $this->form->addFields( [ new TLabel('Cod. Aluno:') ], [ $Codaluno ] );
        $this->form->addFields( [ new TLabel('Nome:') ], [ $Nome ] );
        $this->form->addFields( [ new TLabel('CPF:') ], [ $CPF ] );


        // set sizes
        $Codaluno->setSize('20%');
        $Nome->setSize('70%');
        $CPF->setSize('30%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwDadoshistoricoaluno_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar Histórico'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['VwDadoshistoricoalunoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        //$column_codhistorico = new TDataGridColumn('codhistorico', 'Historico', 'right');
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cod.', 'right');
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
        $column_Datanascimento = new TDataGridColumn('Datanascimento', 'Dt. Nasc.', 'left');
        $column_CPF = new TDataGridColumn('CPF', 'CPF', 'left');
        //$column_CodEntidade = new TDataGridColumn('fi_entidade->NomeFantasia', 'Entidade', 'center');
        $column_Nomehistorico = new TDataGridColumn('Nomehistorico', 'Curso', 'left');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_codhistorico);
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_CPF);
        $this->datagrid->addColumn($column_Nomehistorico);
        //$this->datagrid->addColumn($column_CodEntidade);
        

        
        // create EDIT action
        $action_dados = new TDataGridAction(array('CompletaHistorico', 'onEdit'));
        $action_dados->setUseButton(TRUE);
        $action_dados->setButtonClass('btn btn-default');
        $action_dados->setLabel('Editar Aluno');
        $action_dados->setImage('fa:user green');
        $action_dados->setField('codhistorico');
        $this->datagrid->addAction($action_dados);

        // create EDIT action
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(TRUE);
        $action_select->setButtonClass('btn btn-default');
        $action_select->setLabel('Editar Prof.');
        $action_select->setImage('fas:pencil-alt orange');
        $action_select->setField('codhistorico');
        $this->datagrid->addAction($action_select);
                
        // create EDIT action
        $action_pdf = new TDataGridAction(array('HistoricoFinalFormView', 'onPrint'));
        //$action_pdf = new TDataGridAction(array($this, 'onPrint'));
        $action_pdf->setUseButton(TRUE);
        $action_pdf->setButtonClass('btn btn-default');
        $action_pdf->setLabel('Gerar PDF');
        $action_pdf->setImage('far:file-pdf red');
        $action_pdf->setField('codhistorico');
        $this->datagrid->addAction($action_pdf);
        

        
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
  /*  public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('dados_fei_t'); // open a transaction with database
            $object = new VwDadoshistoricoaluno($key); // instantiates the Active Record
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
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('VwDadoshistoricoalunoList_filter_Codaluno',   NULL);
        TSession::setValue('VwDadoshistoricoalunoList_filter_Nome',   NULL);
        TSession::setValue('VwDadoshistoricoalunoList_filter_CPF',   NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', '=', "{$data->Codaluno}"); // create the filter
            TSession::setValue('VwDadoshistoricoalunoList_filter_Codaluno',   $filter); // stores the filter in the session
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%"); // create the filter
            TSession::setValue('VwDadoshistoricoalunoList_filter_Nome',   $filter); // stores the filter in the session
        }


        if (isset($data->CPF) AND ($data->CPF)) {
            $filter = new TFilter('CPF', '=', "{$data->CPF}"); // create the filter
            TSession::setValue('VwDadoshistoricoalunoList_filter_CPF',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('VwDadoshistoricoaluno_filter_data', $data);
        
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
            // open a transaction with database 'dados_fei_t'
            TTransaction::open('Felabs_DB');
        
            $loggedProf = SystemUser::newFromLogin(TSession::getValue('login'));
            $loggedUnit = TSession::getValue('userunitid');
        
            TTransaction::close();

            TTransaction::open('dados_fei');
            
            // creates a repository for VwDadoshistoricoaluno
            $repository = new TRepository('VwDadoshistoricoaluno');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit));
            //$criteria->add(new TFilter('CodEntidade', '=', $CodEntidade), TExpression::AND_OPERATOR);
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'Nome';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('VwDadoshistoricoalunoList_filter_Codaluno')) {
                $criteria->add(TSession::getValue('VwDadoshistoricoalunoList_filter_Codaluno')); // add the session filter
            }


            if (TSession::getValue('VwDadoshistoricoalunoList_filter_Nome')) {
                $criteria->add(TSession::getValue('VwDadoshistoricoalunoList_filter_Nome')); // add the session filter
            }


            if (TSession::getValue('VwDadoshistoricoalunoList_filter_CPF')) {
                $criteria->add(TSession::getValue('VwDadoshistoricoalunoList_filter_CPF')); // add the session filter
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
  /*  public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('dados_fei_t'); // open a transaction with database
            $object = new VwDadoshistoricoaluno($key, FALSE); // instantiates the Active Record
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
    }*/

    public function onSelect($param)
    {
        // get the parameter and shows the message
       
        $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($key);
        //die();
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->codhistorico)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

               //var_dump($object);
               //die();

                TSession::setValue('sessao_historico', array('Codaluno'     => $object->Codaluno,
                                                             'CodCurso'     => $object->CodCurso,
                                                             'key'          => $object->codhistorico,
                                                             'Edita'        => $object->Edita,
                                                             'Nome'         => $object->Nome
                                                        )
                                   );

               //var_dump(TSession::getValue('sessao_historico'));
               //die();
        
            }
        }

        TTransaction::open('Felabs_DB');
            $loggedUnit = TSession::getValue('userunitid');
        

        if ($loggedUnit == 3) {
            TApplication::loadPage('CompletaDisciplinaHistoricoFAFRAM');
        }
        else {
            TApplication::loadPage('CompletaDisciplinaHistorico');
        }
        
        TTransaction::close();
    }

    public function onSelectDados($param)
    {
        // get the parameter and shows the message
       $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($key);
        //die();
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->codhistorico)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

               //var_dump($object);
               //die();

                TSession::setValue('sessao_dadoshistorico', array(  'Codaluno'  => $object->Codaluno,
                                                                    'CodCurso'  => $object->CodCurso,
                                                                    'key'       => $object->codhistorico,
                                                                    'Nome'      => $object->Nome
                                                                )
                                   );

               //var_dump(TSession::getValue('sessao_dadoshistorico'));
               //die();
        
            }
        }
        TApplication::loadPage('CompletaHistorico');
    }

    /*public function onPrint($param)
    {
        // get the parameter and shows the message
       $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($key);
        //die();
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->codhistorico)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

               //var_dump($object);
               //die();

                TSession::setValue('sessao_printhistorico', array(  'Codaluno'  => $object->Codaluno,
                                                                    'CodCurso'  => $object->CodCurso,
                                                                    'key'       => $object->codhistorico,
                                                                    'Nome'      => $object->Nome
                                                                )
                                   );

               //var_dump(TSession::getValue('sessao_dadoshistorico'));
               //die();
        
            }
        }
        TApplication::loadPage('HistoricoFinalFormView');
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
