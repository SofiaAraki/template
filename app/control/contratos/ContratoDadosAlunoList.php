<?php
/**
 * ContratoDadosAlunoList Listing
 * @author  Pamella Scapim
 */
class ContratoDadosAlunoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_ContratoDadosAluno');
        $this->form->setFormTitle('Lista de Contratos Disponíveis para Assinatura Digital');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $CPF = new TEntry('CPF');


        // add the fields
        $this->form->addFields( [ new TLabel('Cod. Aluno') ], [ $Codaluno ] );
        $this->form->addFields( [ new TLabel('CPF') ], [ $CPF ] );

        //echo $_SERVER['REMOTE_ADDR'];


        // set sizes
        $Codaluno->setSize('100%');
        $CPF->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        //$btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        //$btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cod. Aluno', 'right');
        $column_NomeAluno = new TDataGridColumn('NomeAluno', 'Nome', 'left');
        $column_CPF = new TDataGridColumn('CPF', 'Cpf', 'left');
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


        $action1 = new TDataGridAction([$this, 'onSelect'], ['id'=>'{id}']);
        $action1->setDisplayCondition( array($this, 'displayColumn') );        
        $this->datagrid->addAction($action1, 'Selecionar', 'far:check-circle fa-fw fa-lg green');
        $action2 = new TDataGridAction([$this, 'downloadArquivo'], ['id'=>'{id}']);
        $action2->setDisplayCondition( array($this, 'displayColumnDownload') );      
        $this->datagrid->addAction($action2, 'Download Documentos', 'fa:download  fa-fw fa-lg green');
        
        
        // create the datagrid model
        $this->datagrid->createModel();

        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $pagestep = new TPageStep;
        $pagestep->addItem('Selecionar');
        $pagestep->addItem('Assinatura Digital');
        $pagestep->addItem('Enviar Documento de Indentificação');
        $pagestep->select('Selecionar');
        
        //$back_action = new TAction(array('MultiStepRegistration1View', 'loadPage'));
        //$back = new TActionLink('Back', $back_action, 'black', null, null, 'far:arrow-alt-circle-left red');
        //$back->addStyleClass('btn btn-default btn-sm');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', 'ContratoDadosAlunoList'));
        $vbox->add( $pagestep );
        $vbox->add('<br>');
        $vbox->add( TPanelGroup::pack('', $this->datagrid, $this->pageNavigation ) );
        
        // wrap the page content
        parent::add($vbox);
    }


    public function displayColumn( $object )
    {
        if (($object->StatusContrato == '<span class="label label-primary">Finalizado pelo aluno / Pendente de Assinatura Eletrônica da IES</span>') || ($object->StatusContrato == '<span class="label label-success">Assinado pela IES</span>'))
        {
            return FALSE;
        }
        return TRUE;       
    }


    public function displayColumnDownload( $object )
    {
        if ($object->StatusContrato == '<span class="label label-success">Assinado pela IES</span>')
        {
            return TRUE;
        }
        return FALSE;        
    }


    public function downloadArquivo($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $id = $param['key'];  // get the parameter $key
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new ContratoDadosAlunoDoc($id); // instantiates the Active Record


                
               // if ($object->system_user_id == TSession::getValue('userid') OR TSession::getValue('login') === 'admin')
               // {
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
            else
            {
                $this->form->clear();
                //new TMessage('info', "Arquivo não localizado");
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
   
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'Felabs_DB'
            TTransaction::open('Felabs_DB');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            $loggedUnit = TSession::getValue('userunitid'); 

           // var_dump($loggedUnit);

            // creates a repository for ContratoDadosAluno
            $repository = new TRepository('ContratoDadosAluno');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add( new TFilter(Codaluno, '=', $logged->systemuser_codlegado));
            $criteria->add( new TFilter(CodEntidade, '=', $loggedUnit));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_Codaluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Codaluno')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_CPF')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_CPF')); // add the session filter
            }

            
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
                // iterate the collection of active records
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
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onSelect($param)
    {
        // get the parameter and shows the message
       $key = $param['key'];
        //var_dump($param);
        //die();
        // get the course description
        //var_dump($this->datagrid->getItems());
        //die();
        
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->id)
            {
                //Limpa variável para garantir integridade
                TSession::setValue('sessao_contrato', NULL);
                
                TSession::setValue('sessao_contrato', array('NomeAluno'           => $object->NomeAluno,
                                                            'CPF'                 => $object->CPF,
                                                            'key'                 => $object->id,
                                                            'Rg'                  => $object->Rg,
                                                            'RgOrgaoExpedidor'    => $object->RgOrgaoExpedidor,
                                                            'Datanascimento'      => $object->Datanascimento,
                                                            'Nacionalidade'       => $object->Nacionalidade,
                                                            'Endereco'            => $object->Endereco,
                                                            'EnderecoNumero'      => $object->EnderecoNumero,
                                                            'Bairro'              => $object->Bairro,
                                                            'CodCidade'           => $object->CodCidade,
                                                            'Uf'                  => $object->Uf,
                                                            'EstadoCivil'         => $object->EstadoCivil,
                                                            'Profissao'           => $object->Profissao,
                                                            'Cep'                 => $object->Cep,
                                                            'Telefone'            => $object->Telefone,
                                                            'NomeCurso'           => $object->NomeCurso,
                                                            'Periodo'             => $object->Periodo,
                                                            'AnoMatricula'        => $object->AnoMatricula,
                                                            'EtapaMatricula'      => $object->EtapaMatricula,
                                                            'NomeResponsavel'     => $object->NomeResponsavel,
                                                            'CPFResponsavel'      => $object->CPFResponsavel,
                                                            'RgResponsavel'       => $object->RgResponsavel,
                                                            'RuaResponsavel'      => $object->RuaResponsavel,
                                                            'NumResponsavel'      => $object->NumResponsavel,
                                                            'BairroResponsavel'   => $object->BairroResponsavel,
                                                            'EmailResponsavel'    => $object->EmailResponsavel,
                                                            'CidadeResponsavel'   => $object->CidadeResponsavel,
                                                            'CEPResponsavel'      => $object->CEPResponsavel,
                                                            'TelResponsavel'      => $object->TelResponsavel,
                                                            'ValorAnoSem'         => $object->ValorAnoSem,
                                                            'ValorAnoSemExt'      => $object->ValorAnoSemExt,
                                                            'ValorParc1'          => $object->ValorParc1,
                                                            'ValorParc1Ext'       => $object->ValorParc1Ext,
                                                            'ValorDmsParc'        => $object->ValorDmsParc,
                                                            'ValorDmsParcExt'     => $object->ValorDmsParcExt,
                                                            'DescontoComercial'   => $object->DescontoComercial,
                                                            'EstadoCivil'         => $object->EstadoCivil,
                                                            'Profissao'           => $object->Profissao,
                                                            'RazaoSocial'         => $object->RazaoSocial,
                                                            'NomeFantasia'        => $object->NomeFantasia,
                                                            'Profissao'           => $object->Profissao,
                                                            'EstadoCivil'         => $object->EstadoCivil,
                                                            'Codaluno'            => $object->Codaluno,
                                                            'InicioPrestServico'  => $object->InicioPrestServico,
                                                            'TerminoPrestServico' => $object->TerminoPrestServico,
                                                            'DataPrimeiraParcela' => TDate::date2br($object->DataPrimeiraParcela),
                                                            'UfResponsavel'       => $object->UfResponsavel,
                                                            'DataFinalContrato'   => $object->DataFinalContrato
                                                        )
                                   );
        
            }
        }
        
       //var_dump(TSession::getValue('sessao_contrato'));
       //die();

        TApplication::loadPage('MultiStepRegistration1View');
    }


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
