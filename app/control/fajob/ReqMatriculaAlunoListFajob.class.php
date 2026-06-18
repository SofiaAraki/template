<?php
/**
 * FiAlunoList Listing
 * @author  <your name here>
 */
class ReqMatriculaAlunoListFajob extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_VwFiVestibularRequerimento');
        $this->form->setFormTitle('Requerimento de Matrícula - FAJOB');
        

        // create the form fields
        $COD_INSCRICAO_VESTTIBULAR = new TEntry('COD_INSCRICAO_VESTTIBULAR');
        $Nome = new TEntry('Nome');
        //$CodResponsavel = new TEntry('CodResponsavel');


        // add the fields
        $this->form->addFields( [ new TLabel('Cód. Inscrição') ], [ $COD_INSCRICAO_VESTTIBULAR ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $Nome ] );
//        $this->form->addFields( [ new TLabel('Codresponsavel') ], [ $CodResponsavel ] );


        // set sizes
        $COD_INSCRICAO_VESTTIBULAR->setSize('30%');
        $Nome->setSize('100%');
 //       $CodResponsavel->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwFiVestibularRequerimento_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar Aluno'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction(_t('New'), new TAction(['RequerimentoMatriculaNSC', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_COD_INSCRICAO_VESTTIBULAR = new TDataGridColumn('COD_INSCRICAO_VESTTIBULAR', 'Cod. Inscrição', 'right');
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
       // $column_CodResponsavel = new TDataGridColumn('responsavel->Nome', 'Responsável', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_COD_INSCRICAO_VESTTIBULAR);
        $this->datagrid->addColumn($column_Nome);
        //$this->datagrid->addColumn($column_CodResponsavel);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['RequerimentoMatriculaFajob', 'onEdit']);
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Selecionar');
        $action_edit->setImage('fa:check-circle green');
        $action_edit->setField('COD_INSCRICAO_VESTTIBULAR');
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
            $object = new VwFiVestibularRequerimento($key); // instantiates the Active Record
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
        TSession::setValue('ReqMatriculaAlunoListFajob_filter_COD_INSCRICAO_VESTTIBULAR',   NULL);
        TSession::setValue('ReqMatriculaAlunoListFajob_filter_Nome',   NULL);
        //TSession::setValue('FiVestibularInscricaoList_filter_CodResponsavel',   NULL);

        if (isset($data->COD_INSCRICAO_VESTTIBULAR) AND ($data->COD_INSCRICAO_VESTTIBULAR)) {
            $filter = new TFilter('COD_INSCRICAO_VESTTIBULAR', '=', "%{$data->COD_INSCRICAO_VESTTIBULAR}%"); // create the filter
            TSession::setValue('ReqMatriculaAlunoListFajob_filter_COD_INSCRICAO_VESTTIBULAR',   $filter); // stores the filter in the session
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%"); // create the filter
            TSession::setValue('ReqMatriculaAlunoListFajob_filter_Nome',   $filter); // stores the filter in the session
        }


        //if (isset($data->CodResponsavel) AND ($data->CodResponsavel)) {
        //    $filter = new TFilter('CodResponsavel', 'like', "%{$data->CodResponsavel}%"); // create the filter
        //    TSession::setValue('FiAlunoList_filter_CodResponsavel',   $filter); // stores the filter in the session
        //}

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('VwFiVestibularRequerimento_filter_data', $data);
        
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
            $repository = new TRepository('VwFiVestibularRequerimento');
            $limit = 100;
            // creates a criteria
            $criteria = new TCriteria;

            $criteria->add(new TFilter('COD_VESTIBULAR', '=', 54));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'Nome';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ReqMatriculaAlunoListFajob_filter_COD_INSCRICAO_VESTTIBULAR')) {
                $criteria->add(TSession::getValue('ReqMatriculaAlunoListFajob_filter_COD_INSCRICAO_VESTTIBULAR')); // add the session filter
            }


            if (TSession::getValue('ReqMatriculaAlunoListFajob_filter_Nome')) {
                $criteria->add(TSession::getValue('ReqMatriculaAlunoListFajob_filter_Nome')); // add the session filter
            }


            //if (TSession::getValue('FiAlunoList_filter_CodResponsavel')) {
            //    $criteria->add(TSession::getValue('FiAlunoList_filter_CodResponsavel')); // add the session filter
            //}

            
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
