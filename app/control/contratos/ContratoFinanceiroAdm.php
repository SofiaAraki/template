<?php

class ContratoFinanceiroAdm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_ContratoDadosAluno');
        $this->form->setFormTitle('Busca de Contratos Preenchidos pelo Financeiro');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $NomeAluno = new TEntry('NomeAluno');
        $CPF = new TEntry('CPF');
        $CodCurso = new TEntry('CodCurso');
        $AnoMatricula = new TEntry('AnoMatricula');
        $SemestreMatricula = new TEntry('SemestreMatricula');


        // add the fields
        $this->form->addFields( [ new TLabel('Cod. Aluno') , $Codaluno ] );
        $this->form->addFields( [ new TLabel('Aluno(a)') , $NomeAluno ] );
        $this->form->addFields( [ new TLabel('CPF') , $CPF ] );
        $this->form->addFields( [ new TLabel('Cod. Curso') , $CodCurso ] );
        $this->form->addFields( [ new TLabel('Ano Matrícula') , $AnoMatricula ] );
        $this->form->addFields( [ new TLabel('Semestre Matrícula') , $SemestreMatricula ] );


        // set sizes
        $Codaluno->setSize('100%');
        $NomeAluno->setSize('100%');
        $CPF->setSize('100%');
        $CodCurso->setSize('100%');
        $AnoMatricula->setSize('100%');
        $SemestreMatricula->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(('Cadastrar Contrato'), new TAction(['ContratoFinanceiroListMatricula', 'loadPage']), 'fa:plus-square green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cod. Aluno', 'right');
        $column_NomeAluno = new TDataGridColumn('NomeAluno', 'Aluno(a)', 'left');
        //$column_Datanascimento = new TDataGridColumn('Datanascimento', 'Datanascimento', 'left');
        $column_CPF = new TDataGridColumn('CPF', 'CPF', 'center');
        //$column_Rg = new TDataGridColumn('Rg', 'Rg', 'left');
        //$column_RgOrgaoExpedidor = new TDataGridColumn('RgOrgaoExpedidor', 'Rgorgaoexpedidor', 'left');
        //$column_Naturalidade = new TDataGridColumn('Naturalidade', 'Naturalidade', 'left');
        //$column_Endereco = new TDataGridColumn('Endereco', 'Endereco', 'left');
        //$column_EnderecoNumero = new TDataGridColumn('EnderecoNumero', 'Endereconumero', 'left');
        //$column_Bairro = new TDataGridColumn('Bairro', 'Bairro', 'left');
        //$column_CodCidade = new TDataGridColumn('CodCidade', 'Codcidade', 'left');
        //$column_Nacionalidade = new TDataGridColumn('Nacionalidade', 'Nacionalidade', 'left');
        //$column_Cep = new TDataGridColumn('Cep', 'Cep', 'left');
       // $column_Telefone = new TDataGridColumn('Telefone', 'Telefone', 'left');
        //$column_CodResponsavel = new TDataGridColumn('CodResponsavel', 'Codresponsavel', 'left');
       // $column_NomeResponsavel = new TDataGridColumn('NomeResponsavel', 'Nomeresponsavel', 'left');
        //$column_CPFResponsavel = new TDataGridColumn('CPFResponsavel', 'Cpfresponsavel', 'left');
        //$column_CodCurso = new TDataGridColumn('CodCurso', 'Codcurso', 'right');
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_Periodo = new TDataGridColumn('Periodo', 'Periodo', 'left');
        $column_AnoMatricula = new TDataGridColumn('AnoMatricula', 'Ano Matricula', 'center');
        $column_SemestreMatricula = new TDataGridColumn('SemestreMatricula', 'Semestre Matricula', 'center');
        $column_EtapaMatricula = new TDataGridColumn('EtapaMatricula', 'Etapa matricula', 'center');
        //$column_CodEntidade = new TDataGridColumn('CodEntidade', 'IES', 'center');
        //$column_ValorAnoSem = new TDataGridColumn('ValorAnoSem', 'Valoranosem', 'left');
        //$column_ValorAnoSemExt = new TDataGridColumn('ValorAnoSemExt', 'Valoranosemext', 'left');
        //$column_ValorParc1 = new TDataGridColumn('ValorParc1', 'Valorparc1', 'left');
        //$column_ValorParc1Ext = new TDataGridColumn('ValorParc1Ext', 'Valorparc1ext', 'left');
        //$column_ValorDmsParc = new TDataGridColumn('ValorDmsParc', 'Valordmsparc', 'left');
        //$column_ValorDmsParcExt = new TDataGridColumn('ValorDmsParcExt', 'Valordmsparcext', 'left');
        $column_DescontoComercial = new TDataGridColumn('DescontoComercial', '% Desconto', 'center');
        $column_DataRegistro = new TDataGridColumn('DataRegistro', 'Registrado em', 'center');
        //$column_system_user_id = new TDataGridColumn('system_user_id', 'System User Id', 'left');
        $column_StatusContrato = new TDataGridColumn('StatusContrato', 'Status', 'left');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_NomeAluno);
        //$this->datagrid->addColumn($column_Datanascimento);
        $this->datagrid->addColumn($column_CPF);
        /*$this->datagrid->addColumn($column_Rg);
        $this->datagrid->addColumn($column_RgOrgaoExpedidor);
        $this->datagrid->addColumn($column_Naturalidade);
        $this->datagrid->addColumn($column_Endereco);
        $this->datagrid->addColumn($column_EnderecoNumero);
        $this->datagrid->addColumn($column_Bairro);
        $this->datagrid->addColumn($column_CodCidade);
        $this->datagrid->addColumn($column_Nacionalidade);
        $this->datagrid->addColumn($column_Cep);
        $this->datagrid->addColumn($column_Telefone);
        $this->datagrid->addColumn($column_CodResponsavel);
        $this->datagrid->addColumn($column_NomeResponsavel);
        $this->datagrid->addColumn($column_CPFResponsavel);*/
        //$this->datagrid->addColumn($column_CodCurso);
        $this->datagrid->addColumn($column_NomeCurso);
        $this->datagrid->addColumn($column_Periodo);
        $this->datagrid->addColumn($column_AnoMatricula);
        $this->datagrid->addColumn($column_SemestreMatricula);
        //
        $this->datagrid->addColumn($column_EtapaMatricula);
        //$this->datagrid->addColumn($column_CodEntidade);
        /*$this->datagrid->addColumn($column_ValorAnoSem);
        $this->datagrid->addColumn($column_ValorAnoSemExt);
        $this->datagrid->addColumn($column_ValorParc1);
        $this->datagrid->addColumn($column_ValorParc1Ext);
        $this->datagrid->addColumn($column_ValorDmsParc);
        $this->datagrid->addColumn($column_ValorDmsParcExt);*/
        $this->datagrid->addColumn($column_DescontoComercial);
        $this->datagrid->addColumn($column_DataRegistro);
        $this->datagrid->addColumn($column_StatusContrato);


        //$action1 = new TDataGridAction(['ContratoFinanceiroListMatricula', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        //$this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        $action2->setDisplayCondition( array($this, 'displayColumnDelete') );
        
        
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
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB');
            $object = new ContratoDadosAluno($key);
            $object->{$field} = $value;
            $object->store();
            TTransaction::close();
            
            $this->onReload($param);
            new TMessage('info', "Record Updated");
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
        TSession::setValue(__CLASS__.'_filter_NomeAluno',   NULL);
        TSession::setValue(__CLASS__.'_filter_CPF',   NULL);
        TSession::setValue(__CLASS__.'_filter_CodCurso',   NULL);
        TSession::setValue(__CLASS__.'_filter_AnoMatricula',   NULL);
        TSession::setValue(__CLASS__.'_filter_SemestreMatricula',   NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', 'like', "%{$data->Codaluno}%");
            TSession::setValue(__CLASS__.'_filter_Codaluno',   $filter);
        }


        if (isset($data->NomeAluno) AND ($data->NomeAluno)) {
            $filter = new TFilter('NomeAluno', 'like', "%{$data->NomeAluno}%");
            TSession::setValue(__CLASS__.'_filter_NomeAluno',   $filter);
        }


        if (isset($data->CPF) AND ($data->CPF)) {
            $filter = new TFilter('CPF', 'like', "%{$data->CPF}%");
            TSession::setValue(__CLASS__.'_filter_CPF',   $filter);
        }


        if (isset($data->CodCurso) AND ($data->CodCurso)) {
            $filter = new TFilter('CodCurso', 'like', "%{$data->CodCurso}%");
            TSession::setValue(__CLASS__.'_filter_CodCurso',   $filter);
        }


        if (isset($data->AnoMatricula) AND ($data->AnoMatricula)) {
            $filter = new TFilter('AnoMatricula', 'like', "%{$data->AnoMatricula}%");
            TSession::setValue(__CLASS__.'_filter_AnoMatricula',   $filter);
        }


        if (isset($data->SemestreMatricula) AND ($data->SemestreMatricula)) {
            $filter = new TFilter('SemestreMatricula', 'like', "%{$data->SemestreMatricula}%");
            TSession::setValue(__CLASS__.'_filter_SemestreMatricula',   $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            // Filtra de acordo com a unidade logada
            $unit_id = TSession::getValue('userunitid');
            
            $repository = new TRepository('ContratoDadosAluno');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodEntidade', '=', $unit_id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_Codaluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Codaluno'));
            }


            if (TSession::getValue(__CLASS__.'_filter_NomeAluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_NomeAluno'));
            }


            if (TSession::getValue(__CLASS__.'_filter_CPF')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_CPF'));
            }


            if (TSession::getValue(__CLASS__.'_filter_CodCurso')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_CodCurso'));
            }


            if (TSession::getValue(__CLASS__.'_filter_AnoMatricula')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_AnoMatricula'));
            }


            if (TSession::getValue(__CLASS__.'_filter_SemestreMatricula')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_SemestreMatricula'));
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

                    $object->DataRegistro = TDate::date2br($object->DataRegistro);
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
    
    
    public function displayColumnDelete($param)
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');
        $user_id = TSession::getValue('userid');
        
        
        if( in_array($grupo_admin, $user_groups))
        {
            return TRUE;
        }
            return FALSE;
    }


    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    //Apaga o registro no banco e exclui o arquivo do diretório
    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new ContratoDadosAluno($key, FALSE);
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('contrato_aluno_id', '=', $object->id));
            
            $dados_doc = ContratoDadosAlunoDoc::getObjects($criteria);
            
            if($dados_doc)
            {
                foreach($dados_doc as $dado_doc)
                {
                    $arquivos_anexados = $dado_doc->image;
                    $contrato_assinado = $dado_doc->contrato_assinado_ies;    
                }

                //Apaga zip arquivos anexados
                if(file_exists($arquivos_anexados))
                {
                    unlink($arquivos_anexados);
                }
            
                //Apaga zip contrato assinado
                if(file_exists($contrato_assinado))
                {
                    unlink($contrato_assinado);
                }
            }
            
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
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
