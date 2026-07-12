<?php
/**
 * ContratoDadosAlunoList Listing
 * @author   Pamella Scapim
 */
class BoletimNovoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_BoletimNovoList');
        $this->form->setFormTitle('Boletim Acadêmico');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cod. Aluno', 'left');
        $column_NomeAluno = new TDataGridColumn('NomeAluno', 'Nome', 'left');
        $column_IdentificacaoMatricula = new TDataGridColumn('IdentificacaoMatricula', 'Turma', 'left');
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_AnoMatricula = new TDataGridColumn('AnoMatricula', 'Ano Matrícula', 'left');
        $column_SemestreMatricula = new TDataGridColumn('SemestreMatricula', 'Semestre', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_NomeAluno);
        $this->datagrid->addColumn($column_IdentificacaoMatricula);
        $this->datagrid->addColumn($column_NomeCurso);
        $this->datagrid->addColumn($column_AnoMatricula);
        $this->datagrid->addColumn($column_SemestreMatricula);

        $action1 = new TDataGridAction([$this, 'onSelect'], ['CodMatriculaEtapa'=>'{CodMatriculaEtapa}']);
        $this->datagrid->addAction($action1, 'Ver Boletim', 'far:check-circle fa-fw fa-lg green');
        $action1->setUseButton(TRUE);
               
        // create the datagrid model
        $this->datagrid->createModel();
      
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', 'BoletimNovoList'));
        $vbox->add(TPanelGroup::pack('', $this->datagrid));
        
        // wrap the page content
        parent::add($vbox);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $loggedUnit = TSession::getValue('userunitid'); // UNIDADE ESCOLHIDA NO MOMENTO DO LOGIN
            $user_id = TSession::getValue('userid');        
            $user = new SystemUser($user_id);
            
            TTransaction::close();
            
            // Verifica se preencheu campo contato whatsapp no cadastro - somente para graduação (Amanda)
            TTransaction::open('dados_fei');
            
            $ano_atual = date('Y');
            $array_colegio = ['118' => '118', '119' => '119', '120' => '120'];

            $matriculas = new TRepository('VwAlunoMatriculaEtapa');
            
            $criteria_cadastro = new TCriteria;
            $criteria_cadastro->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
            $criteria_cadastro->add(new TFilter('AnoMatricula', '=', 2026)); // Tira as matrículas antigas do NSC e ANGLO
            $criteria_cadastro->add(new TFilter('CodCurso', 'NOT IN', $array_colegio));
                                    
            $aluno = $matriculas->load($criteria_cadastro);
    
            TTransaction::close();
            
            TTransaction::open('Felabs_DB');

            if($aluno)
            {
                $criteria_whats = new TCriteria;
                $criteria_whats->add(new TFilter('cod_aluno', '=', $aluno[0]->Codaluno));
                    
                $contato_aluno = ContatoAluno::getObjects($criteria_whats);
                        
                if(!$contato_aluno)
                {
                    $action_cadastro = new TAction(['DadosCadastraisView', 'onLoad']);                
                    new TMessage('info', 'Antes de prosseguir, solicitamos que atualize seus dados de contato. <br> Você será redirecionado para o formulário de cadastro', $action_cadastro);   
                    die;              
                }  
            }             
            // Encerra Verifica se preencheu campo contato whatsapp no cadastro

            // Verifica se existe contrato pendente de assinatura (Amanda)    
            $criteria2 = new TCriteria;
            $criteria2->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
            
            $criteria3 = new TCriteria;
            $criteria3->add(new TFilter('StatusContrato', '=', 'Pendente de Validação Pelo Aluno'), TExpression::OR_OPERATOR);
            $criteria3->add(new TFilter('StatusContrato', '=', 'Assinado Pelo Aluno / Envio de Documento Pendente'), TExpression::OR_OPERATOR);
            $criteria3->add(new TFilter('AnoMatricula', '=', 2026), TExpression::AND_OPERATOR);
            
            $criteria4 = new TCriteria;
            $criteria4->add($criteria2);
            $criteria4->add($criteria3);
                    
            $contratos_pendentes = ContratoDadosAluno::getObjects($criteria4);

            if(!empty($contratos_pendentes))
            {
                $action2 = new TAction(['ContratoDadosAlunoList', 'onReload']);                
                new TMessage('info', 'Antes de prosseguir, é necessário assinar digitalmente o(s) contrato(s) de prestação de serviços pendente(s)', $action2);                 
                die;
            }          
            // Encerra Verifica se existe contrato pendente de assinatura
            else
            {
                TTransaction::open('dados_fei');
            
                $repository = new TRepository('VwAlunoMatriculaEtapa');
                $limit = 10;
                
                // creates a criteria
                $criteria5 = new TCriteria;
                $criteria5->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
                $criteria5->add(new TFilter('CodEntidade', '=', $loggedUnit));
                //$criteria5->add(new TFilter('AnoMatricula', '=', 2026));

                $criteria6 = new TCriteria;
                $criteria6->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
                $criteria6->add(new TFilter('CodEntidade', '=', 12));
                $criteria6->add(new TFilter('CodCurso', '=', 139));
                                        
                $criteria = new TCriteria;
                $criteria->add($criteria5, TExpression::OR_OPERATOR);
                $criteria->add($criteria6, TExpression::OR_OPERATOR);
               
                // default order
                if (empty($param['order']))
                {
                    $param['order'] = 'Codaluno';
                    $param['direction'] = 'asc';
                }
                $criteria->setProperties($param); // order, offset
                $criteria->setProperty('limit', $limit);
                
                // load the objects according to criteria
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
                        $this->datagrid->addItem($object);
                    }
                }
                
                // reset the criteria for record count
                $criteria->resetProperties();
                $count = $repository->count($criteria);
                
                $this->pageNavigation->setCount($count); // count of records
                $this->pageNavigation->setProperties($param); // order, page
                $this->pageNavigation->setLimit($limit); // limit
                
                // close the transaction
                TTransaction::close();
                $this->loaded = true;
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public function onSelect($param)
    {
        $key = $param['CodMatriculaEtapa'];

        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->CodMatriculaEtapa)
            {
                TSession::setValue('sessao_boletim', array(
                    'Codaluno'               => $object->Codaluno,
                    'AnoMatricula'           => $object->AnoMatricula,
                    'CodTurmaetapa'          => $object->CodTurmaetapa,
                    'CodMatriculaEtapa'      => $object->CodMatriculaEtapa,
                    'IdentificacaoMatricula' => $object->IdentificacaoMatricula,
                    'MediaPI'                => $object->MediaPI,
                    'NotaNI'                 => $object->NotaNI,
                    'SituacaoMatricula'      => $object->SituacaoMatricula,
                    'ConfirmacaoMatricula'   => $object->ConfirmacaoMatricula,
                    'CodEntidade'            => $object->CodEntidade
                ));
            }
        }

        TApplication::loadPage('BoletimNovoView');
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'], array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload(func_get_arg(0));
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}