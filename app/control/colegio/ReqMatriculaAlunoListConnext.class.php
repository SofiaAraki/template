<?php

class ReqMatriculaAlunoListConnext extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;


    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_FiAluno');
        $this->form->setFormTitle('Requerimento de Matrícula - Connext');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $CodResponsavel = new TEntry('CodResponsavel');


        // add the fields
        $this->form->addFields( [ new TLabel('Cód. Aluno') ], [ $Codaluno ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $Nome ] );


        // set sizes
        $Codaluno->setSize('30%');
        $Nome->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('FiAluno_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar Aluno'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cod. Aluno', 'right');
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
        $column_CodResponsavel = new TDataGridColumn('responsavel->Nome', 'Responsável', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_CodResponsavel);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['RequerimentoMatriculaConnext', 'onEdit']);
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
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

    public function onSearch()
    {
        $data = $this->form->getData();
        
        
        TSession::setValue('FiAlunoList_filter_Codaluno',   NULL);
        TSession::setValue('FiAlunoList_filter_Nome',   NULL);
        TSession::setValue('FiAlunoList_filter_CodResponsavel',   NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', '=', "{$data->Codaluno}");
            TSession::setValue('FiAlunoList_filter_Codaluno',   $filter);
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%");
            TSession::setValue('FiAlunoList_filter_Nome',   $filter);
        }


        if (isset($data->CodResponsavel) AND ($data->CodResponsavel)) {
            $filter = new TFilter('CodResponsavel', 'like', "%{$data->CodResponsavel}%");
            TSession::setValue('FiAlunoList_filter_CodResponsavel',   $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue('FiAluno_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
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

            $repository = new TRepository('FiAluno');
            $limit = 10;

            $criteria = new TCriteria;
            

            if (empty($param['order']))
            {
                $param['order'] = 'Codaluno';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('FiAlunoList_filter_Codaluno')) {
                $criteria->add(TSession::getValue('FiAlunoList_filter_Codaluno'));
            }


            if (TSession::getValue('FiAlunoList_filter_Nome')) {
                $criteria->add(TSession::getValue('FiAlunoList_filter_Nome'));
            }


            if (TSession::getValue('FiAlunoList_filter_CodResponsavel')) {
                $criteria->add(TSession::getValue('FiAlunoList_filter_CodResponsavel'));
            }

            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('dados_fei');
            
            $object = new FiAluno($key, FALSE);
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function show()
    {
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


