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
        
        // Inicialização do Formulário de Busca
        $this->form = new BootstrapFormBuilder('form_search_ContratoDadosAluno');
        $this->form->setFormTitle('Busca de Contratos Preenchidos pelo Financeiro');

        // Criação dos campos de busca
        $Codaluno          = new TEntry('Codaluno');
        $NomeAluno         = new TEntry('NomeAluno');
        $CPF               = new TEntry('CPF');
        $CodCurso          = new TEntry('CodCurso');
        $AnoMatricula      = new TEntry('AnoMatricula');
        $SemestreMatricula = new TEntry('SemestreMatricula');

        // Definição de tamanhos padrão
        $Codaluno->setSize('100%');
        $NomeAluno->setSize('100%');
        $CPF->setSize('100%');
        $CodCurso->setSize('100%');
        $AnoMatricula->setSize('100%');
        $SemestreMatricula->setSize('100%');

        // Agrupamento dos campos em linhas estruturadas (Grid Bootstrap)
        $row1 = $this->form->addFields(
            [ new TLabel('Cod. Aluno'), $Codaluno ],
            [ new TLabel('Aluno(a)'), $NomeAluno ],
            [ new TLabel('CPF'), $CPF ]
        );
        $row1->layout = ['col-sm-3', 'col-sm-5', 'col-sm-4'];

        $row2 = $this->form->addFields(
            [ new TLabel('Cod. Curso'), $CodCurso ],
            [ new TLabel('Ano Matrícula'), $AnoMatricula ],
            [ new TLabel('Semestre Matrícula'), $SemestreMatricula ]
        );
        $row2->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        // Mantém o formulário preenchido durante a navegação
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));
        
        // Ações do formulário
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Cadastrar Contrato', new TAction(['ContratoFinanceiroListMatricula', 'onShow']), 'fa:plus-square green');
        
        // Inicialização da Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // Definição das colunas da Datagrid
        $column_Codaluno          = new TDataGridColumn('Codaluno', 'Cod. Aluno', 'right');
        $column_NomeAluno         = new TDataGridColumn('NomeAluno', 'Aluno(a)', 'left');
        $column_CPF               = new TDataGridColumn('CPF', 'CPF', 'center');
        $column_NomeCurso         = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_Periodo           = new TDataGridColumn('Periodo', 'Periodo', 'left');
        $column_AnoMatricula      = new TDataGridColumn('AnoMatricula', 'Ano Matricula', 'center');
        $column_SemestreMatricula = new TDataGridColumn('SemestreMatricula', 'Semestre Matricula', 'center');
        $column_EtapaMatricula    = new TDataGridColumn('EtapaMatricula', 'Etapa matricula', 'center');
        $column_DescontoComercial = new TDataGridColumn('DescontoComercial', '% Desconto', 'center');
        $column_DataRegistro      = new TDataGridColumn('DataRegistro', 'Registrado em', 'center');
        $column_StatusContrato    = new TDataGridColumn('StatusContrato', 'Status', 'left');

        // Adicionando colunas na Datagrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_NomeAluno);
        $this->datagrid->addColumn($column_CPF);
        $this->datagrid->addColumn($column_NomeCurso);
        $this->datagrid->addColumn($column_Periodo);
        $this->datagrid->addColumn($column_AnoMatricula);
        $this->datagrid->addColumn($column_SemestreMatricula);
        $this->datagrid->addColumn($column_EtapaMatricula);
        $this->datagrid->addColumn($column_DescontoComercial);
        $this->datagrid->addColumn($column_DataRegistro);
        $this->datagrid->addColumn($column_StatusContrato);

        // Ações de linha na Datagrid
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);
        $this->datagrid->addAction($action2, _t('Delete'), 'far:trash-alt red');
        $action2->setDisplayCondition([$this, 'displayColumnDelete']);
        
        $this->datagrid->createModel();
        
        // Paginação
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // Container de Layout (VBox)
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue(__CLASS__.'_filter_Codaluno',          NULL);
        TSession::setValue(__CLASS__.'_filter_NomeAluno',         NULL);
        TSession::setValue(__CLASS__.'_filter_CPF',               NULL);
        TSession::setValue(__CLASS__.'_filter_CodCurso',          NULL);
        TSession::setValue(__CLASS__.'_filter_AnoMatricula',      NULL);
        TSession::setValue(__CLASS__.'_filter_SemestreMatricula', NULL);

        if (isset($data->Codaluno) && ($data->Codaluno)) {
            TSession::setValue(__CLASS__.'_filter_Codaluno', new TFilter('Codaluno', 'like', "%{$data->Codaluno}%"));
        }

        if (isset($data->NomeAluno) && ($data->NomeAluno)) {
            TSession::setValue(__CLASS__.'_filter_NomeAluno', new TFilter('NomeAluno', 'like', "%{$data->NomeAluno}%"));
        }

        if (isset($data->CPF) && ($data->CPF)) {
            TSession::setValue(__CLASS__.'_filter_CPF', new TFilter('CPF', 'like', "%{$data->CPF}%"));
        }

        if (isset($data->CodCurso) && ($data->CodCurso)) {
            TSession::setValue(__CLASS__.'_filter_CodCurso', new TFilter('CodCurso', 'like', "%{$data->CodCurso}%"));
        }

        if (isset($data->AnoMatricula) && ($data->AnoMatricula)) {
            TSession::setValue(__CLASS__.'_filter_AnoMatricula', new TFilter('AnoMatricula', 'like', "%{$data->AnoMatricula}%"));
        }

        if (isset($data->SemestreMatricula) && ($data->SemestreMatricula)) {
            TSession::setValue(__CLASS__.'_filter_SemestreMatricula', new TFilter('SemestreMatricula', 'like', "%{$data->SemestreMatricula}%"));
        }

        $this->form->setData($data);
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = [];
        $param['offset']     = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
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
            
            // Aplicação dos filtros da sessão na consulta
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
                    // Renderização visual dos Status em Badges nativas do Bootstrap
                    if ($object->StatusContrato == 'Pendente de Validação Pelo Aluno') {
                        $object->StatusContrato = '<span class="badge bg-danger text-white p-2">Pendente de Validação Pelo Aluno</span>';
                    } 
                    elseif ($object->StatusContrato == 'Assinado Pelo Aluno / Envio de Documento Pendente') {
                        $object->StatusContrato = '<span class="badge bg-warning text-dark p-2">Assinado Pelo Aluno / Envio de Documento Pendente</span>';
                    } 
                    elseif ($object->StatusContrato == 'Finalizado pelo aluno / Pendente de Assinatura Eletrônica da IES') {
                        $object->StatusContrato = '<span class="badge bg-primary text-white p-2">Finalizado pelo aluno / Pendente de Assinatura Eletrônica da IES</span>';
                    } 
                    elseif ($object->StatusContrato == 'Assinado pela IES') {
                        $object->StatusContrato = '<span class="badge bg-success text-white p-2">Assinado pela IES</span>';
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
        
        if ($user_groups && in_array($grupo_admin, $user_groups))
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
            
            if ($dados_doc)
            {
                foreach ($dados_doc as $dado_doc)
                {
                    $arquivos_anexados = $dado_doc->image;
                    $contrato_assinado = $dado_doc->contrato_assinado_ies;    
                }

                // Apaga arquivo zip de anexos se existir físico no disco
                if (!empty($arquivos_anexados) && file_exists($arquivos_anexados)) {
                    unlink($arquivos_anexados);
                }
            
                // Apaga arquivo zip do contrato assinado se existir físico no disco
                if (!empty($contrato_assinado) && file_exists($contrato_assinado)) {
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
        if (!$this->loaded && (!isset($_GET['method']) || !(in_array($_GET['method'], ['onReload', 'onSearch']))))
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