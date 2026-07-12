<?php

class ContratoAlunoListDigitalizacao extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_ContratoAlunoListDigitalizacao');
        $this->form->setFormTitle('Lista de Contratos Finalizados por Alunos');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $CPF = new TEntry('CPF');
        $StatusContrato = new TEntry('StatusContrato');


        // add the fields
        $this->form->addFields( [ new TLabel('Cod. Aluno') ], [ $Codaluno ] );
        $this->form->addFields( [ new TLabel('CPF') ], [ $CPF ] );
        $this->form->addFields( [ new TLabel('Status') ], [ $StatusContrato ] );


        // set sizes
        $Codaluno->setSize('100%');
        $CPF->setSize('100%');
        $StatusContrato->setSize('100%');
        
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        //$this->form->addActionLink('Novo', new TAction(['', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cod. Aluno', 'left');
        $column_NomeAluno = new TDataGridColumn('NomeAluno', 'Nome', 'left');
        $column_CPF = new TDataGridColumn('CPF', 'CPF', 'left');
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_AnoMatricula = new TDataGridColumn('AnoMatricula', 'Ano Matrícula', 'left');
        $column_SemestreMatricula = new TDataGridColumn('SemestreMatricula', 'Semestre', 'left');
        $column_EtapaMatricula = new TDataGridColumn('EtapaMatricula', 'Etapa', 'left');
        //$column_CodEntidade = new TDataGridColumn('CodEntidade', 'IES', 'right');
        //$column_DataRegistro = new TDataGridColumn('DataRegistro', 'Registrado em', 'left');
        $column_Status = new TDataGridColumn('StatusContrato', 'Status', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_NomeAluno);
        $this->datagrid->addColumn($column_CPF);
        $this->datagrid->addColumn($column_NomeCurso);
        $this->datagrid->addColumn($column_AnoMatricula);
        $this->datagrid->addColumn($column_SemestreMatricula);
        $this->datagrid->addColumn($column_EtapaMatricula);
        //$this->datagrid->addColumn($column_CodEntidade);
        //$this->datagrid->addColumn($column_DataRegistro);
        $this->datagrid->addColumn($column_Status);


        //$action1 = new TDataGridAction([$this, 'onSelect'], ['id'=>'{id}']);
        //$this->datagrid->addAction($action1, 'Selecionar', 'fa:check-circle fa-fw fa-lg green');
        $action2 = new TDataGridAction([$this, 'onDownloadContrato'], ['id'=>'{id}']);
        $this->datagrid->addAction($action2, 'Download', 'fa:download  fa-fw fa-lg green');
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', 'ContratoAlunoListDigitalizacao'));
        $vbox->add($this->form);
        $vbox->add( TPanelGroup::pack('', $this->datagrid, $this->pageNavigation ) );
        
        // wrap the page content
        parent::add($vbox);
    }

    
    public function onDownloadContrato($param)
    {
        try
        {
            $idContrato = $param['key']; 

            //Faz download do contrato assinado pela IES
            TTransaction::open('Felabs_DB');
            
            $object = new ContratoDadosAlunoDoc($idContrato);
            
            TTransaction::close();
            
            if($object->contrato_assinado_ies <> NULL)
            {
                if (strtolower(substr($object->contrato_assinado_ies, -4)) == 'html')
                {
                    $win = TWindow::create( $object->contrato_assinado_ies, 0.8, 0.8 );
                    $win->add( file_get_contents( "contratos/".$object->contrato_assinado_ies ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile($object->contrato_assinado_ies);                        
                }
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

    }


    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));


            $unit_id = TSession::getValue('userunitid');

            $repository = new TRepository('ContratoDadosAluno');

            $limit = 10;


            $criteria1 = new TCriteria;
            //$criteria1->add( new TFilter('StatusContrato', 'like', 'Finalizado pelo aluno / Pendente de Assinatura Eletrônica da IES'), TExpression::OR_OPERATOR);
            $criteria1->add( new TFilter('StatusContrato', 'like', 'Assinado pela IES'), TExpression::OR_OPERATOR);

            $criteria2 = new TCriteria;
            $criteria2->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $criteria = new TCriteria;
            $criteria->add($criteria1, TExpression::AND_OPERATOR);
            $criteria->add($criteria2, TExpression::AND_OPERATOR);                
            
            
            if (empty($param['order']))
            {
                $param['order'] = 'StatusContrato';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_Codaluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Codaluno'));
            }


            if (TSession::getValue(__CLASS__.'_filter_CPF')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_CPF'));
            }
        
            if (TSession::getValue(__CLASS__.'_filter_StatusContrato')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_StatusContrato'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    if($object->StatusContrato == 'Pendente de Validação Pelo Aluno')
                    {
                        $object->StatusContrato = '<span class="label label-danger">Pendente de Validação Pelo Aluno</span>';
                    }
                    elseif($object->StatusContrato == 'Assinado Pelo Aluno / Envio de Documento Pendente')
                    {
                        $object->StatusContrato = '<span class="label label-warning">Assinado Pelo Aluno / Envio de Documento Pendente</span>';
                    }
                    elseif($object->StatusContrato == 'Finalizado pelo aluno / Pendente de Assinatura Eletrônica da IES')
                    {
                        $object->StatusContrato = '<span class="label label-primary">Finalizado pelo aluno / Pendente de Assinatura Eletrônica da IES</span>';
                    }
                    elseif($object->StatusContrato == 'Assinado pela IES')
                    {
                        $object->StatusContrato = '<span class="label label-success">Assinado pela IES</span>';
                    }


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
        

    public function onSearch()
    {
        $data = $this->form->getData();
        

        TSession::setValue(__CLASS__.'_filter_Codaluno',   NULL);
        TSession::setValue(__CLASS__.'_filter_CPF',   NULL);
        TSession::setValue(__CLASS__.'_filter_StatusContrato',   NULL);
        

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', 'like', "%{$data->Codaluno}%");
            TSession::setValue(__CLASS__.'_filter_Codaluno',   $filter);
        }


        if (isset($data->CPF) AND ($data->CPF)) {
            $filter = new TFilter('CPF', 'like', "%{$data->CPF}%");
            TSession::setValue(__CLASS__.'_filter_CPF',   $filter);
        }
        
        if (isset($data->StatusContrato) AND ($data->StatusContrato)) {
            $filter = new TFilter('StatusContrato', 'like', "%{$data->StatusContrato}%");
            TSession::setValue(__CLASS__.'_filter_StatusContrato',   $filter);
        }

        
        $this->form->setData($data);
        
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
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