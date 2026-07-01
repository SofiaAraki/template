<?php
/**
 * FiAlunoList Listing
 * @author  <your name here>
 */
class ReqMatriculaAlunoListAnglo extends TPage
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
        $this->form = new BootstrapFormBuilder('form_FiAluno');
        $this->form->setFormTitle('Requerimento de Matrícula - Anglo');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $CodResponsavel = new TEntry('CodResponsavel');


        // add the fields
        $this->form->addFields( [ new TLabel('Cód. Aluno') ], [ $Codaluno ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $Nome ] );
//        $this->form->addFields( [ new TLabel('Codresponsavel') ], [ $CodResponsavel ] );


        // set sizes
        $Codaluno->setSize('30%');
        $Nome->setSize('100%');
 //       $CodResponsavel->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('FiAluno_filter_data') );
        
        // add the search form actions
        $this->form->addAction('Buscar Aluno', new TAction([$this, 'onSearch']), 'fa:search blue');
        //$this->form->addAction(_t('New'), new TAction(['RequerimentoMatriculaNSC', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cod. Aluno', 'right');
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
        $column_CodResponsavel = new TDataGridColumn('responsavel->Nome', 'Responsável', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_CodResponsavel);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['RequerimentoMatriculaAnglo', 'onEdit']);
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Selecionar');
        $action_edit->setImage('fa:check-circle green');
        $action_edit->setField('Codaluno');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        //$action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        //$action_del->setLabel(_t('Delete'));
        //$action_del->setImage('far:trash-alt red fa-lg');
        //$action_del->setField('Codaluno');
        //$this->datagrid->addAction($action_del);
        
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
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('dados_fei'); // open a transaction with database
            $object = new FiAluno($key); // instantiates the Active Record
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
        TSession::setValue('FiAlunoList_filter_Codaluno',   NULL);
        TSession::setValue('FiAlunoList_filter_Nome',   NULL);
        TSession::setValue('FiAlunoList_filter_CodResponsavel',   NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', '=', "{$data->Codaluno}"); // create the filter
            TSession::setValue('FiAlunoList_filter_Codaluno',   $filter); // stores the filter in the session
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%"); // create the filter
            TSession::setValue('FiAlunoList_filter_Nome',   $filter); // stores the filter in the session
        }


        if (isset($data->CodResponsavel) AND ($data->CodResponsavel)) {
            $filter = new TFilter('CodResponsavel', 'like', "%{$data->CodResponsavel}%"); // create the filter
            TSession::setValue('FiAlunoList_filter_CodResponsavel',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('FiAluno_filter_data', $data);
        
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
            TTransaction::open('dados_fei');

/*
            TSession::setValue('sessao_resp', array('NomeResponsavel' => $object->responsavel->Nome,
                                                        'RgResponsavel'   =>$object->responsavel->Rg,
                                                        'CPFResponsavel'            => $object->responsavel->CPF,
                                                        'EnderecoResp'          => $object->responsavel->Endereco,
                                                        'EnderecoNumeroResp'  =>$object->responsavel->EnderecoNumero,
                                                        'BairroResp'  => $object->responsavel->Bairro,
                                                        'emailResp'  => $object->responsavel->email,
                                                        'CodCidadeResp'  => $object->responsavel->CodCidade,
                                                        'CepResp'  => $object->responsavel->Cep,
                                                        'Telefone1Resp'  => $object->responsavel->Telefone1

                                                        )
                                   );
            var_dump(TSession::getValue('sessao_resp'));
     */       
            // creates a repository for FiAluno
            $repository = new TRepository('FiAluno');
            $limit = 10;
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
            

            if (TSession::getValue('FiAlunoList_filter_Codaluno')) {
                $criteria->add(TSession::getValue('FiAlunoList_filter_Codaluno')); // add the session filter
            }


            if (TSession::getValue('FiAlunoList_filter_Nome')) {
                $criteria->add(TSession::getValue('FiAlunoList_filter_Nome')); // add the session filter
            }


            if (TSession::getValue('FiAlunoList_filter_CodResponsavel')) {
                $criteria->add(TSession::getValue('FiAlunoList_filter_CodResponsavel')); // add the session filter
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
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('dados_fei'); // open a transaction with database
            $object = new FiAluno($key, FALSE); // instantiates the Active Record
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
