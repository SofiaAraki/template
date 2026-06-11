<?php

class HistoricoVisualizacaoAlunoList extends TPage
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
        

        $unit_id = TSession::getValue('userunitid');
            
        if($unit_id == 1 OR $unit_id == 8 OR $unit_id == 12)
        {
            new TMessage('error', 'Funcionalidade não disponível para esta unidade');
            die;
        }
        
                    
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_tipo_historico = new TDataGridColumn('tipo_historico', 'Histórico', 'center');
        $column_dados_diplomado_id = new TDataGridColumn('diploma_digital_diplomado->nome', 'Aluno', 'left');
        $column_dados_curso_id = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'left');
        $column_codigo_validacao = new TDataGridColumn('codigo_validacao', 'Código de validação', 'center');
        $column_data_publicacao = new TDataGridColumn('data_publicacao', 'Data de publicação', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_tipo_historico);
        $this->datagrid->addColumn($column_dados_diplomado_id);
        $this->datagrid->addColumn($column_dados_curso_id);
        $this->datagrid->addColumn($column_codigo_validacao);
        $this->datagrid->addColumn($column_data_publicacao);


        $column_data_publicacao->setTransformer( function($value, $object, $row) {
            if ($value)
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
            return $value;
        });


        $action_download_xml = new TDataGridAction([$this, 'onDownloadXML']);
        $action_download_xml->setUseButton(TRUE);
        $action_download_xml->setButtonClass('btn btn-default');
        $action_download_xml->setLabel('Download XML');
        $action_download_xml->setImage('fas:cloud-download-alt blue');
        $action_download_xml->setField('id');
        
        
        $action_download_representacao = new TDataGridAction([$this, 'onLerDadosXml']);
        $action_download_representacao->setUseButton(TRUE);
        $action_download_representacao->setButtonClass('btn btn-default');
        $action_download_representacao->setLabel('Download RVHE');
        $action_download_representacao->setImage('fas:cloud-download-alt blue');
        $action_download_representacao->setField('id');

        
        $this->datagrid->addAction($action_download_xml);
        $this->datagrid->addAction($action_download_representacao);
        
        
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
        
        
        $aviso = new TLabel("<i>Caro(a) aluno(a), o histórico escolar será emitido mediante solicitação prévia à secretaria acadêmica e análise do pedido pela mesma.</i>");
        
        parent::add($container);
        parent::add($aviso);
    }
    
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');  
            
            $unit_id = TSession::getValue('userunitid');            
            $user_id = TSession::getValue('userid');                    
            $user = new SystemUser($user_id);
                
            TTransaction::close();
            
            
            //Filtra os cursos da unidade logada            
            TTransaction::open('dados_fei');
            
            $repository_curso = new TRepository('FiCurso');
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $cursos = $repository_curso->load($criteria_curso);

            foreach($cursos as $curso)
            {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            
            $aluno = new FiAluno($user->systemuser_codlegado);
            
            TTransaction::close();
            
            
            TTransaction::open('Felabs_DB');

            //Exibe só o histórico do aluno logado na unidade correspondente e que tenha sido publicado
            $repository = new TRepository('HistoricoDigital');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $aluno->Codaluno));
            $criteria->add(new TFilter('cod_curso', 'IN', $items));
            $criteria->add(new TFilter('status_publicacao', '=', '1'));
            

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);            

            
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
    
    
    public function onDownloadXML($param)
    {
        try
        {
            $id_historico = $param['id'];            
            
            TTransaction::open('Felabs_DB');
            
            $historico = new HistoricoDigital($id_historico);

            if($historico->arquivo <> NULL AND $historico->caminho_arquivo <> NULL)
            {
                $caminho_arquivo = $historico->caminho_arquivo . '/' . $historico->arquivo;

                if (file_exists($caminho_arquivo))
                {
                    TPage::openFile($caminho_arquivo);
                }
            }
            else
            {
                new TMessage('info', 'Não foi possível fazer o download do arquivo');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onLerDadosXml($param)
    {
        try
        {
            $id_historico = $param['id'];            
            
            TTransaction::open('Felabs_DB');

            $historico = new HistoricoDigital($id_historico);
            
            //Passo 1: Lê o xml (para garantir que os dados estejam iguais) e salva em uma variável todas as informações que compõem a representação visual
            $target_file = $historico->caminho_arquivo . '/' . $historico->arquivo;
            
            $xml_historico = simplexml_load_file($target_file);             

            $disciplinas = [];
            $d = 0;
            $atividades = [];
            $a = 0;
            $estagios = [];
            $e = 0;
            $situacoes = []; 
            $s = 0;       
            $enades = [];
            $n = 0;
            
            
            $dados_representacao = new StdClass();
                                    
            foreach($xml_historico->infHistoricoEscolar as $tags_dados_historico)
            {
                //ALUNO
                foreach($tags_dados_historico->Aluno as $dados_aluno)
                {
                    $dados_representacao->NomeSocialAluno = (string) $dados_aluno->NomeSocial;
                    $dados_representacao->NomeCivilAluno = (string) $dados_aluno->Nome;
                    $dados_representacao->Nacionalidade = (string) $dados_aluno->Nacionalidade;  
                    $dados_representacao->CpfAluno = (string) $dados_aluno->CPF;
                    $dados_representacao->DataNascimento = (string) $dados_aluno->DataNascimento;
                                        
                    if($dados_aluno->RG)
                    {
                        foreach($dados_aluno->RG as $dados_documento_identificacao)
                        {
                            $dados_representacao->RgNumero = (string) $dados_documento_identificacao->Numero;
                            $dados_representacao->RgOrgaoExpedidor = (string) $dados_documento_identificacao->OrgaoExpedidor;
                            $dados_representacao->RgUf = (string) $dados_documento_identificacao->UF;
                        }
                    }
                    else
                    {
                        foreach($dados_aluno->OutroDocumentoIdentificacao as $dados_documento_identificacao)
                        {
                            $dados_representacao->DocTipo = (string) $dados_documento_identificacao->TipoDocumento;
                            $dados_representacao->DocIdentificador = (string) $dados_documento_identificacao->Identificador;
                        }
                    }
                    
                    foreach($dados_aluno->Naturalidade as $dados_naturalidade)
                    {
                        if($dados_naturalidade->CodigoMunicipio)
                        {
                            $dados_representacao->NaturalidadeMunicipio = (string) $dados_naturalidade->NomeMunicipio;
                            $dados_representacao->NaturalidadeUf = (string) $dados_naturalidade->UF;
                        }
                        else
                        {
                            $dados_representacao->NaturalidadeMunicipio = (string) $dados_naturalidade->NomeMunicipioEstrangeiro;
                        }
                    }                     
                } 


                //CURSO
                foreach($tags_dados_historico->DadosCurso as $dados_curso)
                {
                    $dados_representacao->NomeCurso = (string) $dados_curso->NomeCurso;     
                    
                    if($dados_curso->CodigoCursoEMEC)
                    {
                        $dados_representacao->CodigoEmecCurso = (string) $dados_curso->CodigoCursoEMEC;       
                    }
                    else
                    {
                        foreach($dados_curso->SemCodigoCursoEMEC as $dados_tramitacao_curso)
                        {
                            $dados_representacao->EmecCursoNumeroProcesso = (string) $dados_tramitacao_curso->NumeroProcesso;  
                            $dados_representacao->EmecCursoTipoProcesso = (string) $dados_tramitacao_curso->TipoProcesso;  
                            $dados_representacao->EmecCursoDataCadastro = (string) $dados_tramitacao_curso->DataCadastro;  
                            $dados_representacao->EmecCursoDataProtocolo = (string) $dados_tramitacao_curso->DataProtocolo;      
                        }
                    }
                    
                    foreach($dados_curso->Habilitacao as $dados_habilitacao)
                    {
                        $dados_representacao->NomeHabilitacao = (string) $dados_habilitacao->NomeHabilitacao;  
                        $dados_representacao->DataHabilitacao = (string) 'DATA DA HABILITAÇÃO: ' . TDate::date2br($dados_habilitacao->DataHabilitacao);      
                    }
                    
                    if($dados_curso->RenovacaoReconhecimento)
                    {
                        $dados_representacao->EmecCurso = "Renovação de reconhecimento";
                          
                        foreach($dados_curso->RenovacaoReconhecimento as $dados_renovacao_curso)
                        {
                            if($dados_renovacao_curso->InformacoesTramitacaoEMEC)
                            {
                                $dados_representacao->EmecCursoTramitacao = "Tramitação do processo";
                                    
                                foreach($dados_renovacao_curso->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao)
                                {    
                                    $dados_representacao->RenovacaoCursoNumeroProcesso = (string) $dados_tramitacao_renovacao->NumeroProcesso;
                                    $dados_representacao->RenovacaoCursoTipoProcesso = (string) $dados_tramitacao_renovacao->TipoProcesso;
                                    $dados_representacao->RenovacaoCursoDataCadastro = (string) $dados_tramitacao_renovacao->DataCadastro;
                                    $dados_representacao->RenovacaoCursoDataProtocolo = (string) $dados_tramitacao_renovacao->DataProtocolo; 
                                } 
                            }
                            else
                            {
                                $dados_representacao->EmecCursoAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->RenovacaoCursoTipo = (string) $dados_renovacao_curso->Tipo;
                                $dados_representacao->RenovacaoCursoNumero = (string) $dados_renovacao_curso->Numero;
                                $dados_representacao->RenovacaoCursoData = (string) $dados_renovacao_curso->Data;
                                $dados_representacao->RenovacaoCursoVeiculoPublicacao = (string) $dados_renovacao_curso->VeiculoPublicacao;
                                $dados_representacao->RenovacaoCursoDataPublicacao = (string) $dados_renovacao_curso->DataPublicacao;
                                $dados_representacao->RenovacaoCursoSecaoPublicacao = (string) $dados_renovacao_curso->SecaoPublicacao;
                                $dados_representacao->RenovacaoCursoPaginaPublicacao = (string) $dados_renovacao_curso->PaginaPublicacao;
                                $dados_representacao->RenovacaoCursoNumeroDOU = (string) $dados_renovacao_curso->NumeroDOU;
                            }
                        }
                    }
                    else
                    {
                        $dados_representacao->EmecCurso = "Reconhecimento";
                         
                        foreach($dados_curso->Reconhecimento as $dados_reconhecimento_curso)
                        {
                            if($dados_reconhecimento_curso->InformacoesTramitacaoEMEC)
                            {
                                $dados_representacao->EmecCursoTramitacao = "Tramitação do processo";
                                  
                                foreach($dados_reconhecimento_curso->InformacoesTramitacaoEMEC as $dados_tramitacao_reconhecimento)
                                {  
                                    $dados_representacao->ReconhecimentoCursoNumeroProcesso = (string) $dados_tramitacao_reconhecimento->NumeroProcesso;
                                    $dados_representacao->ReconhecimentoCursoTipoProcesso = (string) $dados_tramitacao_reconhecimento->TipoProcesso;
                                    $dados_representacao->ReconhecimentoCursoDataCadastro = (string) $dados_tramitacao_reconhecimento->DataCadastro;
                                    $dados_representacao->ReconhecimentoCursoDataProtocolo = (string) $dados_tramitacao_reconhecimento->DataProtocolo; 
                                } 
                            }
                            else
                            {
                                $dados_representacao->EmecCursoAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->ReconhecimentoCursoTipo = (string) $dados_reconhecimento_curso->Tipo;
                                $dados_representacao->ReconhecimentoCursoNumero = (string) $dados_reconhecimento_curso->Numero;
                                $dados_representacao->ReconhecimentoCursoData = (string) $dados_reconhecimento_curso->Data;
                                $dados_representacao->ReconhecimentoCursoVeiculoPublicacao = (string) $dados_reconhecimento_curso->VeiculoPublicacao;
                                $dados_representacao->ReconhecimentoCursoDataPublicacao = (string) $dados_reconhecimento_curso->DataPublicacao;
                                $dados_representacao->ReconhecimentoCursoSecaoPublicacao = (string) $dados_reconhecimento_curso->SecaoPublicacao;
                                $dados_representacao->ReconhecimentoCursoPaginaPublicacao = (string) $dados_reconhecimento_curso->PaginaPublicacao;
                                $dados_representacao->ReconhecimentoCursoNumeroDOU = (string) $dados_reconhecimento_curso->NumeroDOU;
                            }
                        }
                    }
                }
                
                
                //EMISSORA
                foreach($tags_dados_historico->IesEmissora as $dados_emissora)
                {
                    $dados_representacao->NomeEmissora = (string) $dados_emissora->Nome;
                    
                    foreach($dados_emissora->Endereco as $dados_endereco)
                    {
                        $dados_representacao->EnderecoEmissoraLogradouro = (string) $dados_endereco->Logradouro;
                        $dados_representacao->EnderecoEmissoraNumero = (string) $dados_endereco->Numero;
                        $dados_representacao->EnderecoEmissoraBairro = (string) $dados_endereco->Bairro;
                        $dados_representacao->EnderecoEmissoraMunicipio = (string) $dados_endereco->NomeMunicipio;
                        $dados_representacao->EnderecoEmissoraUf = (string) $dados_endereco->UF;   
                        $dados_representacao->EnderecoEmissoraCep = (string) $dados_endereco->CEP;       
                    }
                    
                    if($dados_emissora->RenovacaoDeRecredenciamento)
                    {
                        $dados_representacao->EmecEmissora = "Renovação de recredenciamento";
                            
                        foreach($dados_emissora->RenovacaoDeRecredenciamento as $dados_renovacao_emissora)
                        {
                            if($dados_renovacao_emissora->InformacoesTramitacaoEMEC)
                            {
                                $dados_representacao->EmecEmissoraTramitacao = "Tramitação do processo";
                                    
                                foreach($dados_renovacao_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao)
                                {    
                                    $dados_representacao->RenovacaoEmissoraNumeroProcesso = (string) $dados_tramitacao_renovacao->NumeroProcesso;
                                    $dados_representacao->RenovacaoEmissoraTipoProcesso = (string) $dados_tramitacao_renovacao->TipoProcesso;
                                    $dados_representacao->RenovacaoEmissoraDataCadastro = (string) $dados_tramitacao_renovacao->DataCadastro;
                                    $dados_representacao->RenovacaoEmissoraDataProtocolo = (string) $dados_tramitacao_renovacao->DataProtocolo; 
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecEmissoraAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->RenovacaoEmissoraTipo = (string) $dados_renovacao_emissora->Tipo;
                                $dados_representacao->RenovacaoEmissoraNumero = (string) $dados_renovacao_emissora->Numero;
                                $dados_representacao->RenovacaoEmissoraData = (string) $dados_renovacao_emissora->Data;
                                $dados_representacao->RenovacaoEmissoraVeiculoPublicacao = (string) $dados_renovacao_emissora->VeiculoPublicacao;
                                $dados_representacao->RenovacaoEmissoraDataPublicacao = (string) $dados_renovacao_emissora->DataPublicacao;
                                $dados_representacao->RenovacaoEmissoraSecaoPublicacao = (string) $dados_renovacao_emissora->SecaoPublicacao;
                                $dados_representacao->RenovacaoEmissoraPaginaPublicacao = (string) $dados_renovacao_emissora->PaginaPublicacao;
                                $dados_representacao->RenovacaoEmissoraNumeroDOU = (string) $dados_renovacao_emissora->NumeroDOU;
                            }
                        }
                    }
                    elseif($dados_emissora->Recredenciamento)
                    {
                        $dados_representacao->EmecEmissora = "Recredenciamento";
                            
                        foreach($dados_emissora->Recredenciamento as $dados_recredenciamento_emissora)
                        {
                            if($dados_recredenciamento_emissora->InformacoesTramitacaoEMEC)
                            {
                                $dados_representacao->EmecEmissoraTramitacao = "Tramitação do processo";
                                    
                                foreach($dados_recredenciamento_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_recredenciamento)
                                {    
                                    $dados_representacao->RecredenciamentoEmissoraNumeroProcesso = (string) $dados_tramitacao_recredenciamento->NumeroProcesso;
                                    $dados_representacao->RecredenciamentoEmissoraTipoProcesso = (string) $dados_tramitacao_recredenciamento->TipoProcesso;
                                    $dados_representacao->RecredenciamentoEmissoraDataCadastro = (string) $dados_tramitacao_recredenciamento->DataCadastro;
                                    $dados_representacao->RecredenciamentoEmissoraDataProtocolo = (string) $dados_tramitacao_recredenciamento->DataProtocolo; 
                                } 
                            }
                            else
                            {
                                $dados_representacao->EmecEmissoraAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->RecredenciamentoEmissoraTipo = (string) $dados_recredenciamento_emissora->Tipo;
                                $dados_representacao->RecredenciamentoEmissoraNumero = (string) $dados_recredenciamento_emissora->Numero;
                                $dados_representacao->RecredenciamentoEmissoraData = (string) $dados_recredenciamento_emissora->Data;
                                $dados_representacao->RecredenciamentoEmissoraVeiculoPublicacao = (string) $dados_recredenciamento_emissora->VeiculoPublicacao;
                                $dados_representacao->RecredenciamentoEmissoraDataPublicacao = (string) $dados_recredenciamento_emissora->DataPublicacao;
                                $dados_representacao->RecredenciamentoEmissoraSecaoPublicacao = (string) $dados_recredenciamento_emissora->SecaoPublicacao;
                                $dados_representacao->RecredenciamentoEmissoraPaginaPublicacao = (string) $dados_recredenciamento_emissora->PaginaPublicacao;
                                $dados_representacao->RecredenciamentoEmissoraNumeroDOU = (string) $dados_recredenciamento_emissora->NumeroDOU;
                            }
                        }
                    }
                    else
                    {
                        $dados_representacao->EmecEmissora = "Credenciamento";
                            
                        foreach($dados_emissora->Credenciamento as $dados_credenciamento_emissora)
                        {
                            if($dados_credenciamento_emissora->InformacoesTramitacaoEMEC)
                            {
                                $dados_representacao->EmecEmissoraTramitacao = "Tramitação do processo";
                                    
                                foreach($dados_credenciamento_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_credenciamento)
                                {    
                                    $dados_representacao->CredenciamentoEmissoraNumeroProcesso = (string) $dados_tramitacao_credenciamento->NumeroProcesso;
                                    $dados_representacao->CredenciamentoEmissoraTipoProcesso = (string) $dados_tramitacao_credenciamento->TipoProcesso;
                                    $dados_representacao->CredenciamentoEmissoraDataCadastro = (string) $dados_tramitacao_credenciamento->DataCadastro;
                                    $dados_representacao->CredenciamentoEmissoraDataProtocolo = (string) $dados_tramitacao_credenciamento->DataProtocolo; 
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecEmissoraAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->CredenciamentoEmissoraTipo = (string) $dados_credenciamento_emissora->Tipo;
                                $dados_representacao->CredenciamentoEmissoraNumero = (string) $dados_credenciamento_emissora->Numero;
                                $dados_representacao->CredenciamentoEmissoraData = (string) $dados_credenciamento_emissora->Data;
                                $dados_representacao->CredenciamentoEmissoraVeiculoPublicacao = (string) $dados_credenciamento_emissora->VeiculoPublicacao;
                                $dados_representacao->CredenciamentoEmissoraDataPublicacao = (string) $dados_credenciamento_emissora->DataPublicacao;
                                $dados_representacao->CredenciamentoEmissoraSecaoPublicacao = (string) $dados_credenciamento_emissora->SecaoPublicacao;
                                $dados_representacao->CredenciamentoEmissoraPaginaPublicacao = (string) $dados_credenciamento_emissora->PaginaPublicacao;
                                $dados_representacao->CredenciamentoEmissoraNumeroDOU = (string) $dados_credenciamento_emissora->NumeroDOU;
                            }
                        }
                    }
                }
                
                
                //COMPONENTES CURRICULARES
                foreach($tags_dados_historico->HistoricoEscolar as $tag_historico_escolar)
                {
                    foreach($tag_historico_escolar->CodigoCurriculo as $dados_curriculo)
                    {
                        $dados_representacao->CodigoCurriculo = (string) $dados_curriculo;
                    }
                    
                    foreach($tag_historico_escolar->ElementosHistorico as $dados_elemento_historico)
                    {
                        //DISCIPLINAS
                        if($dados_elemento_historico->Disciplina)
                        {   
                            foreach($dados_elemento_historico->Disciplina as $dados_disciplina)
                            {                                   
                                //NOME DISCIPLINA E PERÍODO LETIVO                                                  
                                $disciplinas[$d]['nome_disciplina'] = (string) $dados_disciplina->NomeDisciplina;
                                $disciplinas[$d]['periodo_letivo'] = (string) $dados_disciplina->PeriodoLetivo;
                                
                                
                                //CARGA HORÁRIA
                                foreach($dados_disciplina->CargaHoraria as $dados_carga_horaria)
                                {
                                    $disciplinas[$d]['carga_horaria'] = (string) $dados_carga_horaria->HoraRelogio;
                                }
                                
                                
                                //NOTA
                                $disciplinas[$d]['nota'] = (string) $dados_disciplina->Nota;
                                
                                
                                //SITUAÇÃO
                                if($dados_disciplina->Aprovado)
                                {
                                    $disciplinas[$d]['situacao'] = "APROVADO(A)";
                                    
                                    foreach($dados_disciplina->Aprovado as $dados_forma_integralizacao)
                                    {
                                        $disciplinas[$d]['forma_integralizacao'] = (string) $dados_forma_integralizacao->FormaIntegralizacao;
                                    }
                                }
                                
                                if($dados_disciplina->Reprovado)
                                {
                                    $disciplinas[$d]['situacao'] = "REPROVADO(A)";
                                }
                                
                                if($dados_disciplina->Pendente)
                                {
                                    $disciplinas[$d]['situacao'] = "PENDENTE";
                                }
                                 
                                 
                                //DOCENTES  
                                $p = 1; 
                                                             
                                foreach($dados_disciplina->Docentes as $dados_docentes)
                                { 
                                    foreach($dados_docentes->Docente as $dados_docente)                                     
                                    {
                                        if($dados_docente)
                                        {
                                            $disciplinas[$d]['nome_professor'.$p] = (string) "Prof. " . mb_strtoupper($dados_docente->Nome);
                                            $disciplinas[$d]['titulacao_professor'.$p] = (string) " - " . mb_strtoupper($dados_docente->Titulacao);    
                                        }    
                                        
                                        $p++;
                                    }   
                                }
                                
                                $d++;
                            }   
                        }
                        
                        
                        //ATIVIDADES COMPLEMENTARES
                        if($dados_elemento_historico->AtividadeComplementar)
                        {
                            foreach($dados_elemento_historico->AtividadeComplementar as $dados_atividade)
                            {
                                $atividades[$a]['data_inicio'] = (string) $dados_atividade->DataInicio;
                                $atividades[$a]['data_termino'] = (string) $dados_atividade->DataFim;
                                $atividades[$a]['tipo_atividade'] = (string) mb_strtoupper($dados_atividade->TipoAtividadeComplementar);
                                $atividades[$a]['descricao'] = (string) $dados_atividade->Descricao;
                                $atividades[$a]['carga_horaria'] = (string) substr($dados_atividade->CargaHorariaEmHoraRelogio,0,-3);
                                
                                
                                //DOCENTES (no nosso caso, só teremos um responsável)                           
                                foreach($dados_atividade->DocentesResponsaveisPelaValidacao as $dados_docentes)
                                { 
                                    foreach($dados_docentes->Docente as $dados_docente)                                     
                                    {
                                        $atividades[$a]['nome_professor'] = (string) "Prof. responsável " . mb_strtoupper($dados_docente->Nome);
                                        $atividades[$a]['titulacao_professor'] = (string) " - " . mb_strtoupper($dados_docente->Titulacao);
                                    }   
                                }
                                
                                $a++;
                            }
                        }
                      
                      
                        //ESTÁGIOS
                        if($dados_elemento_historico->Estagio)
                        {
                            foreach($dados_elemento_historico->Estagio as $dados_estagio)
                            {
                                $estagios[$e]['data_inicio'] = (string) $dados_estagio->DataInicio;
                                $estagios[$e]['data_termino'] = (string) $dados_estagio->DataFim;
                                $estagios[$e]['descricao'] = (string) $dados_estagio->Descricao;
                                
                                
                                //CONCEDENTE
                                if($dados_estagio->Concedente)
                                {
                                    foreach($dados_estagio->Concedente as $dados_concedente)
                                    {
                                        if($dados_concedente->RazaoSocial)
                                        {
                                            $estagios[$e]['concedente'] = (string) "Concedente: " . mb_strtoupper($dados_concedente->RazaoSocial);
                                            $estagios[$e]['cnpj_concedente'] = (string) " - CNPJ: " . preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $dados_concedente->CNPJ);
                                        }
                                        
                                        if($dados_concedente->Nome)
                                        {
                                            $estagios[$e]['concedente'] = (string) "Concedente: " . mb_strtoupper($dados_concedente->Nome);
                                            $estagios[$e]['cpf_concedente'] = (string) " - CPF: " . preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $dados_concedente->CPF);
                                        }
                                    }
                                }
                                
                                
                                $estagios[$e]['carga_horaria'] = (string) substr($dados_estagio->CargaHorariaEmHorasRelogio,0,-3);
                                
                                
                                //DOCENTES (no nosso caso, só teremos um responsável)                           
                                foreach($dados_estagio->DocentesOrientadores as $dados_docentes)
                                { 
                                    foreach($dados_docentes->Docente as $dados_docente)                                     
                                    {
                                        $estagios[$e]['nome_professor'] = (string) "Prof. responsável " . mb_strtoupper($dados_docente->Nome);
                                        $estagios[$e]['titulacao_professor'] = (string) " - " . mb_strtoupper($dados_docente->Titulacao);
                                    }   
                                }
                                
                                $e++;
                            }
                        }

                        
                        //SITUAÇÕES
                        if($dados_elemento_historico->SituacaoDiscente)
                        {
                            foreach($dados_elemento_historico->SituacaoDiscente as $dados_situacao)
                            {   
                                if($dados_situacao->Trancamento)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Trancamento";
                                }
                                
                                if($dados_situacao->MatriculadoEmDisciplina)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Matriculado em disciplina";
                                }
                                
                                if($dados_situacao->Licenca)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Licença";
                                }         
                                
                                if($dados_situacao->IntercambioInternacional)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Intercâmbio internacional";                                        
                                        
                                    foreach($dados_situacao->IntercambioInternacional as $dados_intercambio)
                                    {
                                        $situacoes[$s]['intercambio_instituicao'] = (string) "Instituição: " . mb_strtoupper($dados_intercambio->Instituicao);
                                        $situacoes[$s]['intercambio_pais'] = (string) " - País: " . mb_strtoupper($dados_intercambio->Pais);
                                        $situacoes[$s]['intercambio_programa'] = (string) " - Programa: " . mb_strtoupper($dados_intercambio->NomeProgramaIntercambio);
                                    }
                                }        
                                
                                if($dados_situacao->IntercambioNacional)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Intercâmbio nacional";
                                        
                                    foreach($dados_situacao->IntercambioNacional as $dados_intercambio)
                                    {
                                        $situacoes[$s]['intercambio_instituicao'] = (string) "Instituição: " . mb_strtoupper($dados_intercambio->Instituicao);
                                        $situacoes[$s]['intercambio_pais'] = (string) " - País: " . mb_strtoupper($dados_intercambio->Pais);
                                        $situacoes[$s]['intercambio_programa'] = (string) " - Programa: " . mb_strtoupper($dados_intercambio->NomeProgramaIntercambio);
                                    }
                                }         
                                
                                if($dados_situacao->Desistencia)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Desistência";
                                }
                                
                                if($dados_situacao->Abandono)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Abandono";
                                }
                                
                                if($dados_situacao->Jubilado)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Jubilado";
                                }
                                
                                if($dados_situacao->Formado)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;                                                                              
                                    $situacoes[$s]['situacao_discente'] = "Formado";
                                        
                                    foreach($dados_situacao->Formado as $dados_formado)
                                    { 
                                        $situacoes[$s]['formado_data_conclusao'] = (string) "Data de conclusão: " . TDate::date2br($dados_formado->DataConclusaoCurso);
                                        $situacoes[$s]['formado_data_colacao'] = (string) " - Data da colação: " . TDate::date2br($dados_formado->DataColacaoGrau);
                                        $situacoes[$s]['formado_data_exp_diploma'] = (string) " - Data de expedição do diploma: " . TDate::date2br($dados_formado->DataExpedicaoDiploma);
                                    }
                                }
                                
                                if($dados_situacao->OutraSituacao)
                                {
                                    $situacoes[$s]['periodo_letivo'] = (string) $dados_situacao->PeriodoLetivo;
                                    $situacoes[$s]['situacao_discente'] = "Outra";                                                                              
                                    $situacoes[$s]['outra_situacao_descricao'] = (string) $dados_situacao->OutraSituacao;
                                }
                                
                                $s++;
                            }       
                        }
                    }
                }
                
                
                //DATA EMISSÃO HISTÓRICO
                foreach($tags_dados_historico->HistoricoEscolar as $tag_historico_escolar)
                {
                    $dados_representacao->DataEmissaoHistorico = (string) TDate::date2br($tag_historico_escolar->DataEmissaoHistorico);
                }
                
                
                //SITUAÇÃO ATUAL DISCENTE
                foreach($tags_dados_historico->HistoricoEscolar as $tag_historico_escolar)
                {
                    foreach($tag_historico_escolar->SituacaoAtualDiscente as $dados_situacao_atual)
                    {
                        if($dados_situacao_atual->Trancamento)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "TRANCAMENTO";     
                        }
                        
                        if($dados_situacao_atual->MatriculadoEmDisciplina)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "MATRICULADO EM DISCIPLINA";     
                        }
                        
                        if($dados_situacao_atual->Licenca)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "LICENÇA";     
                        }
                        
                        if($dados_situacao_atual->IntercambioInternacional)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "INTERCÂMBIO INTERNACIONAL";     
                        }
                        
                        if($dados_situacao_atual->IntercambioNacional)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "INTERCÂMBIO NACIONAL";     
                        }
                        
                        if($dados_situacao_atual->Desistencia)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "DESISTÊNCIA";     
                        }
                        
                        if($dados_situacao_atual->Abandono)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "ABANDONO";     
                        }
                        
                        if($dados_situacao_atual->Jubilado)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "JUBILADO";     
                        }
                        
                        if($dados_situacao_atual->Formado)
                        {
                            $dados_representacao->SituacaoAtualDiscente = "FORMADO"; 
                            
                            foreach($dados_situacao_atual->Formado as $dados_formado)
                            {
                                $dados_representacao->DataConclusaoCurso = (string) TDate::date2br($dados_formado->DataConclusaoCurso); 
                                $dados_representacao->DataColacaoGrau = (string) TDate::date2br($dados_formado->DataColacaoGrau); 
                                $dados_representacao->DataExpedicaoDiploma = (string) TDate::date2br($dados_formado->DataExpedicaoDiploma);     
                            }
                        }
                        
                        if($dados_situacao_atual->OutraSituacao)
                        {
                            $dados_representacao->SituacaoAtualDiscente = (string) mb_strtoupper($dados_situacao_atual->OutraSituacao);     
                        }
                    }    
                }
                
                
                //ENADE (o aluno pode ter prestado mais de um)
                foreach($tags_dados_historico->HistoricoEscolar as $tag_historico_escolar)
                {
                    foreach($tag_historico_escolar->ENADE as $dados_enade)
                    {
                        if($dados_enade->Habilitado)
                        {
                            foreach($dados_enade->Habilitado as $dados_habilitado)
                            {     
                                $enades[$n]['situacao_enade'] = "Habilitado";                           
                                $enades[$n]['condicao_enade'] = (string) $dados_habilitado->Condicao;      
                                $enades[$n]['edicao_enade'] = (string) $dados_habilitado->Edicao;   
                            
                                $n++;
                            }   
                        }
                            
                        if($dados_enade->Irregular)
                        {
                            foreach($dados_enade->Irregular as $dados_irregular)
                            {     
                                $enades[$n]['situacao_enade'] = "Irregular";                           
                                $enades[$n]['condicao_enade'] = (string) $dados_irregular->Condicao;
                                $enades[$n]['edicao_enade'] = (string) $dados_irregular->Edicao;
                            
                                $n++;
                            }    
                        }
                            
                        if($dados_enade->NaoHabilitado)
                        {
                            foreach($dados_enade->NaoHabilitado as $dados_nao_habilitado)
                            {         
                                $enades[$n]['situacao_enade'] = "Não habilitado";                       
                                $enades[$n]['condicao_enade'] = (string) $dados_nao_habilitado->Condicao;
                                $enades[$n]['edicao_enade'] = (string) $dados_nao_habilitado->Edicao;
                            
                                if($dados_nao_habilitado->Motivo)
                                {
                                    $enades[$n]['motivo_enade'] = (string) $dados_nao_habilitado->Motivo;
                                }
                                else
                                {
                                    $enades[$n]['motivo_enade'] = (string) $dados_nao_habilitado->OutroMotivo;
                                }
                                
                                $n++;
                            }   
                        }                       
                    }    
                }
                
                 
                //CH INTEGRALIZADA
                foreach($tags_dados_historico->HistoricoEscolar as $tag_historico_escolar)
                {
                    foreach($tag_historico_escolar->CargaHorariaCursoIntegralizada as $dados_ch_integralizada)
                    {
                        $dados_representacao->CargaHorariaIntegralizada = (string) $dados_ch_integralizada->HoraRelogio;
                    }
                }
                
                    
                //CH CURSO
                foreach($tags_dados_historico->HistoricoEscolar as $tag_historico_escolar)
                {
                    foreach($tag_historico_escolar->CargaHorariaCurso as $dados_ch_curso)
                    {
                        $dados_representacao->CargaHorariaCurso = (string) $dados_ch_curso->HoraRelogio;
                    }
                }
                
                
                //FORMA DE INGRESSO
                foreach($tags_dados_historico->HistoricoEscolar as $tag_historico_escolar)
                {
                    foreach($tag_historico_escolar->IngressoCurso as $dados_ingresso)
                    {
                        $dados_representacao->DataIngresso = (string) $dados_ingresso->Data; 
                        $dados_representacao->FormaAcesso = (string) $dados_ingresso->FormaAcesso;     
                    }    
                }
                
                
                //CÓDIGO DE VALIDAÇÃO HISTÓRICO
                foreach($tags_dados_historico->SegurancaHistorico as $dados_seguranca)
                {
                    $dados_representacao->CodigoValidacaoHistorico = (string) $dados_seguranca->CodigoValidacao;
                }
                
                
                //INFORMAÇÕES ADICIONAIS
                foreach($tags_dados_historico->InformacoesAdicionais as $dados_adicionais)
                {
                    $dados_representacao->InformacoesAdicionais = (string) $dados_adicionais;
                }
            }         
                                        

            //Limpa variável para garantir integridade
            TSession::setValue('dados_representacao', NULL);
            TSession::setValue('dados_representacao', $dados_representacao);
            
            TSession::setValue('dados_disciplinas', NULL);
            TSession::setValue('dados_disciplinas', $disciplinas);
            
            TSession::setValue('dados_atividades', NULL);
            TSession::setValue('dados_atividades', $atividades);
            
            TSession::setValue('dados_estagios', NULL);
            TSession::setValue('dados_estagios', $estagios);
            
            TSession::setValue('dados_situacoes', NULL);
            TSession::setValue('dados_situacoes', $situacoes);
            
            TSession::setValue('dados_enades', NULL);
            TSession::setValue('dados_enades', $enades);
            
            
            TTransaction::close();            

            $this->onFormatarDados($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
   
    public function onFormatarDados($param)
    {
        try
        {                       
            TTransaction::open('Felabs_DB');            
            
            $id_historico = $param['id'];           
            
            $historico = new HistoricoDigital($id_historico);
            
            $emissora = new DiplomaDigitalEmissora($historico->dados_emissora_id);
            
            
            //Pega os dados do xml que vão compor a parte superior do histórico 
            $dados_representacao = TSession::getValue('dados_representacao');
                        
                        
            //Emissora - dados EMEC
            if($dados_representacao->EmecEmissora == "Renovação de recredenciamento") 
            {
                if($dados_representacao->EmecEmissoraAtoRegulatorio)
                {
                    $texto_emec_emissora = $dados_representacao->EmecEmissora . " pelo(a) " . 
                                           $dados_representacao->RenovacaoEmissoraTipo . " nº " .
                                           $dados_representacao->RenovacaoEmissoraNumero . ", de " .
                                           TDate::date2br($dados_representacao->RenovacaoEmissoraData) . ", publicado(a) no " .
                                           $dados_representacao->RenovacaoEmissoraVeiculoPublicacao . " nº " .
                                           $dados_representacao->RenovacaoEmissoraNumeroDOU . ", seção " . 
                                           $dados_representacao->RenovacaoEmissoraSecaoPublicacao . ", pág. " .
                                           $dados_representacao->RenovacaoEmissoraPaginaPublicacao . " em " .
                                           TDate::date2br($dados_representacao->RenovacaoEmissoraDataPublicacao);
                }
                else
                {
                    $texto_emec_emissora = "Processo de " . $dados_representacao->RenovacaoEmissoraTipoProcesso .
                                           " cadastrado em " . TDate::date2br($dados_representacao->RenovacaoEmissoraDataCadastro) .
                                           " e protocolado em " . TDate::date2br($dados_representacao->RenovacaoEmissoraDataProtocolo) .
                                           " sob o nº " . $dados_representacao->RenovacaoEmissoraNumeroProcesso .
                                           " junto ao e-mec";
                    
                                           /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecEmissora . 
                                           " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->RenovacaoEmissoraNumeroProcesso .
                                           ", " . $dados_representacao->RenovacaoEmissoraTipoProcesso . " cadastrado em " .
                                           TDate::date2br($dados_representacao->RenovacaoEmissoraDataCadastro) . " e protocolado em " .
                                           TDate::date2br($dados_representacao->RenovacaoEmissoraDataProtocolo);*/
                }
            }                         
            elseif($dados_representacao->EmecEmissora == "Recredenciamento")
            {
                if($dados_representacao->EmecEmissoraAtoRegulatorio)
                {
                    $texto_emec_emissora = "Recredenciada pelo(a) " . $dados_representacao->RecredenciamentoEmissoraTipo . 
                                           " nº " . $dados_representacao->RecredenciamentoEmissoraNumero . ", de " .
                                           TDate::date2br($dados_representacao->RecredenciamentoEmissoraData) . ", publicado(a) no " .
                                           $dados_representacao->RecredenciamentoEmissoraVeiculoPublicacao . " nº " .
                                           $dados_representacao->RecredenciamentoEmissoraNumeroDOU . ", seção " . 
                                           $dados_representacao->RecredenciamentoEmissoraSecaoPublicacao . ", pág. " .
                                           $dados_representacao->RecredenciamentoEmissoraPaginaPublicacao . " em " .
                                           TDate::date2br($dados_representacao->RecredenciamentoEmissoraDataPublicacao);
                }
                else
                {
                    $texto_emec_emissora = "Processo de " . $dados_representacao->RecredenciamentoEmissoraTipoProcesso .
                                           " cadastrado em " . TDate::date2br($dados_representacao->RecredenciamentoEmissoraDataCadastro) .
                                           " e protocolado em " . TDate::date2br($dados_representacao->RecredenciamentoEmissoraDataProtocolo) .
                                           " sob o nº " . $dados_representacao->RecredenciamentoEmissoraNumeroProcesso .
                                           " junto ao e-mec";
                    
                                           /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecEmissora . 
                                           " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->RecredenciamentoEmissoraNumeroProcesso .
                                           ", " . $dados_representacao->RecredenciamentoEmissoraTipoProcesso . " cadastrado em " .
                                           TDate::date2br($dados_representacao->RecredenciamentoEmissoraDataCadastro) . " e protocolado em " .
                                           TDate::date2br($dados_representacao->RecredenciamentoEmissoraDataProtocolo);*/
                }
            }                       
            else
            {
                if($dados_representacao->EmecEmissoraAtoRegulatorio)
                {
                    $texto_emec_emissora = "Credenciada pelo(a) " . $dados_representacao->CredenciamentoEmissoraTipo . 
                                           " nº " . $dados_representacao->CredenciamentoEmissoraNumero . ", de " .
                                           TDate::date2br($dados_representacao->CredenciamentoEmissoraData) . ", publicado(a) no " .
                                           $dados_representacao->CredenciamentoEmissoraVeiculoPublicacao . " nº " .
                                           $dados_representacao->CredenciamentoEmissoraNumeroDOU . ", seção " . 
                                           $dados_representacao->CredenciamentoEmissoraSecaoPublicacao . ", pág. " .
                                           $dados_representacao->CredenciamentoEmissoraPaginaPublicacao . " em " .
                                           TDate::date2br($dados_representacao->CredenciamentoEmissoraDataPublicacao);
                }
                else
                {
                    $texto_emec_emissora = "Processo de " . $dados_representacao->CredenciamentoEmissoraTipoProcesso .
                                           " cadastrado em " . TDate::date2br($dados_representacao->CredenciamentoEmissoraDataCadastro) .
                                           " e protocolado em " . TDate::date2br($dados_representacao->CredenciamentoEmissoraDataProtocolo) .
                                           " sob o nº " . $dados_representacao->CredenciamentoEmissoraNumeroProcesso .
                                           " junto ao e-mec";
                    
                                           /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecEmissora . 
                                           " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->CredenciamentoEmissoraNumeroProcesso .
                                           ", " . $dados_representacao->CredenciamentoEmissoraTipoProcesso . " cadastrado em " .
                                           TDate::date2br($dados_representacao->CredenciamentoEmissoraDataCadastro) . " e protocolado em " .
                                           TDate::date2br($dados_representacao->CredenciamentoEmissoraDataProtocolo);*/
                }
            }
            
            
            //Endereço e contato emissora
            if($emissora->system_unit_id == 2 OR $emissora->system_unit_id == 6)
            {
                $dados1_emissora = "Rua Cel. Flauzino Barbosa Sandoval, 1259 - Ituverava -SP  CEP: 14500-000  Fone/Fax:(16) 3729-9000";
            }
            
            if($emissora->system_unit_id == 3)
            {
                $dados1_emissora = "Rod. Jerônimo Nunes Macedo, Km 01 - Ituverava - SP - CEP: 14500-000";
                $dados2_emissora = "(16) 3729-9060 - email: fafram@feituverava.com.br";
            }
            
            if($emissora->system_unit_id == 10)
            {
                $dados1_emissora = "Rua Rio Grande do Norte, 1470 - São Joaquim da Barra - SP CEP: 14600-000 - Fone/Fax:(16) 3810-3900";
            }
                       

            //Nome social
            $nome = mb_strtolower($dados_representacao->NomeSocialAluno); // Converter o nome todo para minúsculo sem desconfigurar os acentos

            $nome = explode(" ", $nome); // Separa o nome por espaços

            for ($i=0; $i < count($nome); $i++) 
            { 
                //Tratar cada palavra do nome
                if ($nome[$i] == "de" or $nome[$i] == "da" or $nome[$i] == "e" or $nome[$i] == "dos" or $nome[$i] == "do")
                {
                    $nome_social_aluno .= $nome[$i].' '; // Se a palavra estiver dentro das complementares mostrar toda em minúsculo
                }
                else
                {
                    $nome_social_aluno .= ucfirst($nome[$i]).' '; //Se for um nome, mostrar a primeira letra maiúscula
                }
            }
            
            
            //Nome civil
            $nome = mb_strtolower($dados_representacao->NomeCivilAluno); // Converter o nome todo para minúsculo sem desconfigurar os acentos

            $nome = explode(" ", $nome); // Separa o nome por espaços

            for ($i=0; $i < count($nome); $i++) 
            { 
                //Tratar cada palavra do nome
                if ($nome[$i] == "de" or $nome[$i] == "da" or $nome[$i] == "e" or $nome[$i] == "dos" or $nome[$i] == "do")
                {
                    $nome_civil_aluno .= $nome[$i].' '; // Se a palavra estiver dentro das complementares mostrar toda em minúsculo
                }
                else
                {
                    $nome_civil_aluno .= ucfirst($nome[$i]).' '; //Se for um nome, mostrar a primeira letra maiúscula
                }
            }
            
            
            //Documento de identificação
            if($dados_representacao->RgNumero)
            {
                $rg_numero = $dados_representacao->RgNumero;
                $rg_orgao_expedidor = mb_strtoupper($dados_representacao->RgOrgaoExpedidor);
                $rg_uf = $dados_representacao->RgUf;
                
    
                $primeiro_elemento = substr($rg_numero, 0, 1);
        
                if(is_numeric($primeiro_elemento)) //Ex: Maioria dos estados
                {
                    //1º - coloca no "padrão"
                    $rg_formatado = preg_replace('/^([0-9]{1,2})([0-9]{3})([0-9]{3})([A-Za-z0-9]{0,1})/', '$1.$2.$3-$4', $rg_numero);
        
                    //2º - verifica se o RG possui dígito verificador
                    preg_match('/^[0-9]{1,2}\.[0-9]{3}\.[0-9]{3}\-([A-Za-z0-9]{0,1})$/', $rg_formatado, $partes);
                     
                    $digito_verificador = $partes[1];
                    
                    //Formatação de RG com dígito verificador    
                    if($digito_verificador <> NULL)
                    {
                        $documento_identificacao = $rg_formatado . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;                   
                    }
                    
                    //Formatação de RG sem dígito verificador
                    else
                    {
                        $rg_sem_digito = preg_replace('/^([0-9]{1,2})([0-9]{3})([0-9]{3})/', '$1.$2.$3', $rg_numero);
                            
                        $documento_identificacao = $rg_sem_digito . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;                   
                    }
                }
                else //Ex: RGs de Minas Gerais (começa com MG)
                {
                    $rg = preg_replace('/^([A-Za-z]{1,2})([0-9]{1,2})([0-9]{3})([0-9]{3})$/', '$1-$2.$3.$4', $rg_numero);
                        
                    $documento_identificacao = $rg . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;
                }
            }
            else
            {
                $documento_identificacao = $dados_representacao->DocTipo . ' - ' . $dados_representacao->DocIdentificador;
            }
            
            
            //CPF aluno
            $cpf_aluno = preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/', '$1.$2.$3-$4', $dados_representacao->CpfAluno);
            
            
            //Naturalidade
            $nome_cidade = mb_strtolower($dados_representacao->NaturalidadeMunicipio); // Converter o nome todo para minúsculo sem desconfigurar os acentos

            $nome_cidade = str_replace(array("-", "(", ")"), array(" ", " ", " "), $nome_cidade); //Retira caracteres especiais

            $nome_cidade = explode(" ", $nome_cidade); // Separa o nome por espaços

            for ($i=0; $i < count($nome_cidade); $i++) 
            {     
                //Tratar cada palavra do nome
                if ($nome_cidade[$i] == "de" or $nome_cidade[$i] == "da" or $nome_cidade[$i] == "do" or $nome_cidade[$i] == "dos" or $nome_cidade[$i] == "das")
                {
                    $cidade .= $nome_cidade[$i].' '; // Se a palavra estiver dentro das complementares mostrar toda em minúsculo
                }
                elseif($nome_cidade[$i] == "d'oeste")
                {
                    $do_oeste = "D'Oeste";
                    
                    $cidade .= $do_oeste.' '; //Se tiver apóstrofo, mostrar as letras envolvidas em maiúsculo
                }
                else
                {
                    $cidade .= ucfirst($nome_cidade[$i]).' '; //Se for um nome, mostrar a primeira letra maiúscula
                }
            }
            
            if($dados_representacao->Nacionalidade == "Brasileira" OR $dados_representacao->Nacionalidade == "Brasileiro")
            {       
                $naturalidade = $cidade . ' - ' . $dados_representacao->NaturalidadeUf;
            }
            else
            {
                $naturalidade = $cidade;
            }
           

            //Código e-mec curso ou dados de tramitação
            if($dados_representacao->CodigoEmecCurso)
            {
                $com_ou_sem_codigo_emec_curso = "CÓDIGO E-MEC: " . $dados_representacao->CodigoEmecCurso;    
            }
            else
            {
                $com_ou_sem_codigo_emec_curso = "Processo de " . $dados_representacao->EmecCursoTipoProcesso .
                                                " cadastrado em " . TDate::date2br($dados_representacao->EmecCursoDataCadastro) .
                                                " e protocolado em " . TDate::date2br($dados_representacao->EmecCursoDataProtocolo) .
                                                " sob o nº " . $dados_representacao->EmecCursoNumeroProcesso .
                                                " junto ao e-mec";
                
                                                /*"PROCESSO DE TRAMITAÇÃO DO CURSO JUNTO AO E-MEC: PROCESSO Nº " . $dados_representacao->EmecCursoNumeroProcesso .  
                                                ", " . $dados_representacao->EmecCursoTipoProcesso .
                                                " CADASTRADO EM " . TDate::date2br($dados_representacao->EmecCursoDataCadastro) .
                                                " E PROTOCOLADO EM " . TDate::date2br($dados_representacao->EmecCursoDataProtocolo);*/   
            }
            
            
            //Curso - EMEC
            if($dados_representacao->EmecCurso == "Renovação de reconhecimento") 
            {
                if($dados_representacao->EmecCursoAtoRegulatorio)
                {
                    $texto_emec_curso = $dados_representacao->EmecCurso . " pelo(a) " . 
                                        $dados_representacao->RenovacaoCursoTipo . " nº " .
                                        $dados_representacao->RenovacaoCursoNumero . ", de " .
                                        TDate::date2br($dados_representacao->RenovacaoCursoData) . ", publicado(a) no " .
                                        $dados_representacao->RenovacaoCursoVeiculoPublicacao . " nº " .
                                        $dados_representacao->RenovacaoCursoNumeroDOU . ", seção " . 
                                        $dados_representacao->RenovacaoCursoSecaoPublicacao . ", pág. " .
                                        $dados_representacao->RenovacaoCursoPaginaPublicacao . " em " .
                                        TDate::date2br($dados_representacao->RenovacaoCursoDataPublicacao);
                }
                else
                {
                    $texto_emec_curso = "Processo de " . $dados_representacao->RenovacaoCursoTipoProcesso .
                                        " cadastrado em " . TDate::date2br($dados_representacao->RenovacaoCursoDataCadastro) .
                                        " e protocolado em " . TDate::date2br($dados_representacao->RenovacaoCursoDataProtocolo) .
                                        " sob o nº " . $dados_representacao->RenovacaoCursoNumeroProcesso .
                                        " junto ao e-mec";
                    
                                        /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecCurso . 
                                        " do curso junto ao E-MEC: Processo de nº " . $dados_representacao->RenovacaoCursoNumeroProcesso .
                                        ", " . $dados_representacao->RenovacaoCursoTipoProcesso . " cadastrado em " .
                                        TDate::date2br($dados_representacao->RenovacaoCursoDataCadastro) . " e protocolado em " .
                                        TDate::date2br($dados_representacao->RenovacaoCursoDataProtocolo);*/
                }    
            }            
            else
            {
                if($dados_representacao->EmecCursoAtoRegulatorio)
                {
                    $texto_emec_curso = "Reconhecido pelo(a) " . $dados_representacao->ReconhecimentoCursoTipo . 
                                        " nº " . $dados_representacao->ReconhecimentoCursoNumero . ", de " .
                                        TDate::date2br($dados_representacao->ReconhecimentoCursoData) . ", publicado(a) no " .
                                        $dados_representacao->ReconhecimentoCursoVeiculoPublicacao . " nº " .
                                        $dados_representacao->ReconhecimentoCursoNumeroDOU . ", seção " . 
                                        $dados_representacao->ReconhecimentoCursoSecaoPublicacao . ", pág. " .
                                        $dados_representacao->ReconhecimentoCursoPaginaPublicacao . " em " .
                                        TDate::date2br($dados_representacao->ReconhecimentoCursoDataPublicacao);
                }
                else
                {
                    $texto_emec_curso = "Processo de " . $dados_representacao->ReconhecimentoCursoTipoProcesso .
                                        " cadastrado em " . TDate::date2br($dados_representacao->ReconhecimentoCursoDataCadastro) .
                                        " e protocolado em " . TDate::date2br($dados_representacao->ReconhecimentoCursoDataProtocolo) .
                                        " sob o nº " . $dados_representacao->ReconhecimentoCursoNumeroProcesso .
                                        " junto ao e-mec";
                    
                                        /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecCurso . 
                                        " do curso junto ao E-MEC: Processo de nº " . $dados_representacao->ReconhecimentoCursoNumeroProcesso .
                                        ", " . $dados_representacao->ReconhecimentoCursoTipoProcesso . " cadastrado em " .
                                        TDate::date2br($dados_representacao->ReconhecimentoCursoDataCadastro) . " e protocolado em " .
                                        TDate::date2br($dados_representacao->ReconhecimentoCursoDataProtocolo);*/
                }    
            }
            
                                   
            //Limpa variável para garantir integridade
            TSession::setValue('dados_gerais', NULL);        
        
            //Passa os dados para a representação do histórico
            TSession::setValue('dados_gerais', array('IdHistorico' => $historico->id,
                                                     'NomeEmissora' => mb_strtoupper($dados_representacao->NomeEmissora),
                                                     'TextoEmecEmissora' => $texto_emec_emissora,
                                                     'Dados1Emissora' => $dados1_emissora,
                                                     'Dados2Emissora' => $dados2_emissora,
                                                     'NomeSocialAluno' => mb_strtoupper($nome_social_aluno),
                                                     'NomeCivilAluno' => mb_strtoupper($nome_civil_aluno),
                                                     'DocumentoIdentificacao' => $documento_identificacao,
                                                     'CpfAluno' => $cpf_aluno,
                                                     'DataNascimentoAluno' => TDate::date2br($dados_representacao->DataNascimento),
                                                     'NaturalidadeAluno' => mb_strtoupper($naturalidade),
                                                     'NacionalidadeAluno' => mb_strtoupper($dados_representacao->Nacionalidade),
                                                     'FormaAcesso' => mb_strtoupper($dados_representacao->FormaAcesso),
                                                     'DataIngresso' => TDate::date2br($dados_representacao->DataIngresso),
                                                     'NomeCurso' => mb_strtoupper($dados_representacao->NomeCurso),
                                                     'CodigoEmecCurso' => $com_ou_sem_codigo_emec_curso,   
                                                     'HabilitacaoCurso' => mb_strtoupper($dados_representacao->NomeHabilitacao),
                                                     'DataHabilitacao' => $dados_representacao->DataHabilitacao,
                                                     'TextoEmecCurso' => mb_strtoupper($texto_emec_curso),
                                                     'CargaHorariaCurso' => (int) $dados_representacao->CargaHorariaCurso,
                                                     'CargaHorariaIntegralizada' => (int) $dados_representacao->CargaHorariaIntegralizada,
                                                     'SituacaoAtualDiscente' => $dados_representacao->SituacaoAtualDiscente,
                                                     'DataConclusaoCurso' => $dados_representacao->DataConclusaoCurso,
                                                     'DataColacaoGrau' => $dados_representacao->DataColacaoGrau,
                                                     'DataExpedicaoDiploma' => $dados_representacao->DataExpedicaoDiploma,
                                                     'DataEmissaoHistorico' => $dados_representacao->DataEmissaoHistorico,
                                                     'CodigoValidacao' => $dados_representacao->CodigoValidacaoHistorico,
                                                     'CaminhoQrCode' => $historico->caminho_qrcode . '/' . $historico->qrcode 
                                                    )      
                              );
            
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Disciplinas
            $dados_disciplinas = TSession::getValue('dados_disciplinas'); 
            
            foreach($dados_disciplinas as $dados_disciplina)
            {
                //Separa período letivo
                $parts_periodo_letivo  = explode('-', $dados_disciplina['periodo_letivo']);
                $ciclo = $parts_periodo_letivo[0];
                $ano_semestre = $parts_periodo_letivo[1];                
                $parts_ciclo = explode('º', $ciclo);
                $etapa = $parts_ciclo[0];

                if($dados_disciplina['situacao'] == 'PENDENTE')
                {
                    $nota = "-";
                }
                else
                {
                    $nota = $dados_disciplina['nota'];
                }
                
                if($dados_disciplina['situacao'] == 'APROVADO(A)')
                {
                    $forma_integralizacao = mb_strtoupper($dados_disciplina['forma_integralizacao']);
                }
                else
                {
                    $forma_integralizacao = "-";
                }
                
                
                $disciplinas[] = array('etapa' => $etapa,
                                       'ano_semestre' => $ano_semestre, 
                                       'nome_disciplina' => $dados_disciplina['nome_disciplina'],                                       
                                       'nome_professor1' => $dados_disciplina['nome_professor1'],
                                       'titulacao_professor1' => $dados_disciplina['titulacao_professor1'],
                                       'nome_professor2' => $dados_disciplina['nome_professor2'],
                                       'titulacao_professor2' => $dados_disciplina['titulacao_professor2'],
                                       'nota' => $nota,
                                       'carga_horaria' => (int) $dados_disciplina['carga_horaria'],
                                       'situacao' => $dados_disciplina['situacao'],
                                       'forma_integralizacao' => $forma_integralizacao); 
                                       
                $ch_disciplinas += (int) $dados_disciplina['carga_horaria'];                               
            } 
            
            $disciplinas_ch_integralizada[] = array('disciplinas_ch_integralizada' => $ch_disciplinas);
            
            //Limpa variável para garantir integridade
            TSession::setValue('disciplinas', NULL); 
            TSession::setValue('disciplinas_ch_integralizada', NULL);
            
            //Passa os dados para a representação do histórico
            TSession::setValue('disciplinas', $disciplinas); 
            TSession::setValue('disciplinas_ch_integralizada', $disciplinas_ch_integralizada);
            
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Atividades
                        
            //Se constar atividade complementar na grade, pega a CH do BD para preencher a CH mínima obrigatória na tabela Atividades
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('historico_digital_id', '=', $historico->id)); 
            $criteria1->add(new TFilter('tipo_entrada', '=', 'Atividade Complementar'));  
                    
            $historico_atividades = HistoricoDigitalDisciplinas::getObjects($criteria1); 
            
            if($historico_atividades)
            {
                foreach($historico_atividades as $historico_atividade)
                {    
                    $codigos_atividade_historico[] = $historico_atividade->cod_disciplina;                    
                }
                
                TTransaction::open('dados_fei');
                
                $view_historico = new VwDadoshistoricoaluno($historico->historico_genesi_id);
                $grade_cursada = new FiGradeCurso($view_historico->CodGradecurso);
                
                $criteria2 = new TCriteria;
                $criteria2->add(new TFilter('CodGradecurso', '=', $grade_cursada->CodGradecurso)); 
                $criteria2->add(new TFilter('CodDisciplina', 'IN', $codigos_atividade_historico));  
                                        
                $grade_atividades = FiGradeDisciplina::getObjects($criteria2);
                
                TTransaction::close();
                
                foreach($grade_atividades as $grade_atividade)
                {
                    $ch_obrig_atividades += (int) $grade_atividade->CargaHorariaTotal;
                }
                
                $ch_obrigatoria_atividades[] = array('ch_obrigatoria_atividades' => $ch_obrig_atividades); 
            }

            //Se não constar, verifica se a CH mínima obrigatória das atividades consta na coluna AtivCom_CH da Vw_DadosHistoricoAluno
            else
            {
                TTransaction::open('dados_fei');
                
                $view_historico = new VwDadoshistoricoaluno($historico->historico_genesi_id);
                
                TTransaction::close();

                if($view_historico->AtivCom_CH)
                {
                    $ch_obrig_atividades += (int) $view_historico->AtivCom_CH;  
                    
                    $ch_obrigatoria_atividades[] = array('ch_obrigatoria_atividades' => $ch_obrig_atividades);  
                }                                 
            }            
            

            //Vem do XML
            $dados_atividades = TSession::getValue('dados_atividades');           
                        
            $i = 0;
            
            foreach($dados_atividades as $dados_atividade)
            {
                $dado_atividade = (object) $dados_atividade;

                //Preenche todas as informações da atividade com dados vindos do XML e apenas a etapa e a CH mínima obrigatória com o valor do BD
                $atividades[$i] = array('data_inicio' => TDate::date2br($dado_atividade->data_inicio),
                                        'data_termino' => TDate::date2br($dado_atividade->data_termino),                                       
                                        'tipo_atividade' => $dado_atividade->tipo_atividade,
                                        'descricao' => "Descrição: " . mb_strtoupper($dado_atividade->descricao),
                                        'carga_horaria' => $dado_atividade->carga_horaria,
                                        'nome_professor' => $dado_atividade->nome_professor,
                                        'titulacao_professor' => $dado_atividade->titulacao_professor);
                                          
                $ch_atividades += $dado_atividade->carga_horaria;
                                          
                $entrada_atividade = AtividadeComplementar::where('cod_aluno', '=', $historico->cod_aluno)
                                                          ->where('cod_curso', '=', $historico->cod_curso)
                                                          ->where('status_atividade', '=', 'Aprovado')
                                                          ->where('data_inicio', '=', $dado_atividade->data_inicio)
                                                          ->where('data_termino', '=', $dado_atividade->data_termino)
                                                          ->where('descricao', '=', $dado_atividade->descricao)
                                                          ->load();
                
                if($entrada_atividade)  
                {
                    $atividades[$i]['etapa'] = $entrada_atividade[0]->etapa;
                }
                
                $i++;                                     
            }

            $atividades_ch_integralizada[] = array('atividades_ch_integralizada' => $ch_atividades);
            
            //Limpa variável para garantir integridade
            TSession::setValue('ch_obrigatoria_atividades', NULL); 
            TSession::setValue('atividades', NULL); 
            TSession::setValue('atividades_ch_integralizada', NULL);
            
            //Passa os dados para a representação do histórico
            TSession::setValue('ch_obrigatoria_atividades', $ch_obrigatoria_atividades); 
            TSession::setValue('atividades', $atividades);
            TSession::setValue('atividades_ch_integralizada', $atividades_ch_integralizada);
            
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Estágios
            
            /*Se constar estágios na grade e o aluno estiver na última etapa, pega a CH do BD para preencher a CH mínima obrigatória na tabela Estágios
            (Enfermagem, por exemplo, tem estágio nos 2 últimos ciclos, então só exibe a CH mínima quando a grade estiver completa)*/
            
            TTransaction::open('dados_fei');
            
            $view_historico = new VwDadoshistoricoaluno($historico->historico_genesi_id);
            
            $qtde_etapas = $view_historico->QtdeEtapas;
            
            TTransaction::close();
 
 
            $criteria3 = new TCriteria;
            $criteria3->add(new TFilter('historico_digital_id', '=', $historico->id)); 
            $criteria3->add(new TFilter('tipo_entrada', '=', 'Estágio'));  
                                
            $historico_estagios = HistoricoDigitalDisciplinas::getObjects($criteria3); 
            
            if($historico_estagios)
            {
                $i = 0;
                
                foreach($historico_estagios as $historico_estagio)
                {
                    $info_estagios[$i]['etapa'] = $historico_estagio->etapa;                    
                    $codigos_estagio_historico[] = $historico_estagio->cod_disciplina; 
                                        
                    $i++;                
                }
                
                $ultima_etapa =  serialize($info_estagios);
                
                //Se a última etapa foi lançada no histórico, soma a CH dos estágios 
                if(strpos($ultima_etapa, $qtde_etapas) !== false)
                {    
                    TTransaction::open('dados_fei');
                
                    $view_historico = new VwDadoshistoricoaluno($historico->historico_genesi_id);
                    $grade_cursada = new FiGradeCurso($view_historico->CodGradecurso);
                    
                    $criteria4 = new TCriteria;
                    $criteria4->add(new TFilter('CodGradecurso', '=', $grade_cursada->CodGradecurso)); 
                    $criteria4->add(new TFilter('CodDisciplina', 'IN', $codigos_estagio_historico));  
                                            
                    $grade_estagios = FiGradeDisciplina::getObjects($criteria4);
                    
                    TTransaction::close();
                    
                    foreach($grade_estagios as $grade_estagio)
                    {
                        $ch_obrig_estagios += (int) $grade_estagio->CargaHorariaTotal;
                    }
                    
                    $ch_obrigatoria_estagios[] = array('ch_obrigatoria_estagios' => $ch_obrig_estagios);
                } 
            }
            
            //Se não constar, verifica se a CH mínima obrigatória dos estágios consta na coluna Estagio_CH da Vw_DadosHistoricoAluno
            else
            {
                if($view_historico->Estagio_CH)
                {
                    $ch_obrig_estagios += (int) $view_historico->Estagio_CH;
                    
                    $ch_obrigatoria_estagios[] = array('ch_obrigatoria_estagios' => $ch_obrig_estagios);  
                }                                 
            }                
                 
                            
            //Vem do XML            
            $dados_estagios = TSession::getValue('dados_estagios');
            
            $i = 0;
            
            foreach($dados_estagios as $dados_estagio)
            {
                $dado_estagio = (object) $dados_estagio;


                //Preenche todas as informações do estágio com dados vindos do XML e apenas a etapa e a CH mínima obrigatória com o valor do BD
                $estagios[$i] = array('data_inicio' => TDate::date2br($dado_estagio->data_inicio),
                                      'data_termino' => TDate::date2br($dado_estagio->data_termino),                                       
                                      'concedente_estagio' => $dado_estagio->concedente,
                                      'cnpj_concedente' => $dado_estagio->cnpj_concedente,
                                      'cpf_concedente' => $dado_estagio->cpf_concedente,
                                      'descricao' => mb_strtoupper($dado_estagio->descricao), 
                                      'carga_horaria' => $dado_estagio->carga_horaria,
                                      'nome_professor' => $dado_estagio->nome_professor,
                                      'titulacao_professor' => $dado_estagio->titulacao_professor);      
            
                $ch_estagios += $dado_estagio->carga_horaria;
                
                $entrada_estagio = Estagio::where('cod_aluno', '=', $historico->cod_aluno)
                                          ->where('cod_curso', '=', $historico->cod_curso)
                                          ->where('status_estagio', '=', 'Aprovado')
                                          ->where('data_inicio', '=', $dado_estagio->data_inicio)
                                          ->where('data_termino', '=', $dado_estagio->data_termino)  
                                          ->where('descricao', '=', $dado_estagio->descricao)       
                                          ->load();
                                           
                if($entrada_estagio)  
                {
                    $estagios[$i]['etapa'] = $entrada_estagio[0]->etapa;
                }
                
                $i++;                             
            } 

            $estagios_ch_integralizada[] = array('estagios_ch_integralizada' => $ch_estagios);

            //Limpa variável para garantir integridade
            TSession::setValue('ch_obrigatoria_estagios', NULL); 
            TSession::setValue('estagios', NULL); 
            TSession::setValue('estagios_ch_integralizada', NULL);
            
            //Passa os dados para a representação do histórico
            TSession::setValue('ch_obrigatoria_estagios', $ch_obrigatoria_estagios); 
            TSession::setValue('estagios', $estagios);  
            TSession::setValue('estagios_ch_integralizada', $estagios_ch_integralizada);        
            
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Situações
            $dados_situacoes = TSession::getValue('dados_situacoes');

            foreach($dados_situacoes as $dados_situacao)
            {
                if($dados_situacao['situacao_discente'] == "Outra")
                {
                    $dados_situacao['situacao_discente'] = $dados_situacao['outra_situacao_descricao'];
                }
                                
                $situacoes[] = array('periodo_letivo' => $dados_situacao['periodo_letivo'],
                                     'situacao_discente' => mb_strtoupper($dados_situacao['situacao_discente']),
                                     'intercambio_instituicao' => $dados_situacao['intercambio_instituicao'],
                                     'intercambio_programa' => $dados_situacao['intercambio_programa'],
                                     'intercambio_pais' => $dados_situacao['intercambio_pais'],
                                     'formado_data_conclusao' => $dados_situacao['formado_data_conclusao'],
                                     'formado_data_colacao' => $dados_situacao['formado_data_colacao'],
                                     'formado_data_exp_diploma' => $dados_situacao['formado_data_exp_diploma']);    
            }           
            
            //Limpa variável para garantir integridade
            TSession::setValue('situacoes', NULL); 
            
            //Passa os dados para a representação do histórico
            TSession::setValue('situacoes', $situacoes);
                       
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Observações (Enade - ordenado por ano de participação; informações adicionais; currículo, etc)
            $dados_enades = TSession::getValue('dados_enades');
            
            foreach($dados_enades as $key => $value)
            {
                $obs_enades[$key] = $value['edicao_enade'];
            }
            
            array_multisort($obs_enades, SORT_ASC, $dados_enades);
            
            $enade1 = $dados_enades[0];
            $enade2 = $dados_enades[1];
            

            if($enade1)
            {
                if($enade1['situacao_enade'] == "Habilitado")
                {
                    $texto_enade_1a = 'Estudante HABILITADO ao ENADE como ' . $enade1['condicao_enade'] . ' na edição de ' . $enade1['edicao_enade'];
                }
                    
                if($enade1['situacao_enade'] == "Irregular")
                {
                    $texto_enade_1a = 'Estudante IRREGULAR junto ao ENADE como ' . $enade1['condicao_enade'] . ' na edição de ' . $enade1['edicao_enade'];
                }
                    
                if($enade1['situacao_enade'] == "Não habilitado")
                {
                    $texto_enade_1a = 'Estudante NÃO HABILITADO ao ENADE como ' . $enade1['condicao_enade'] . ' na edição de ' . $enade1['edicao_enade'];
                    $texto_enade_1b = 'Motivo: ' . $enade1['motivo_enade']; 
                }
            }
            
            if($enade2)
            {
                if($enade2['situacao_enade'] == "Habilitado")
                {
                    $texto_enade_2a = 'Estudante HABILITADO ao ENADE como ' . $enade2['condicao_enade'] . ' na edição de ' . $enade2['edicao_enade'];
                }
                
                if($enade2['situacao_enade'] == "Irregular")
                {
                    $texto_enade_2a = 'Estudante IRREGULAR junto ao ENADE como ' . $enade2['condicao_enade'] . ' na edição de ' . $enade2['edicao_enade'];
                }
                
                if($enade2['situacao_enade'] == "Não habilitado")
                {
                    $texto_enade_2a = 'Estudante NÃO HABILITADO ao ENADE como ' . $enade2['condicao_enade'] . ' na edição de ' . $enade2['edicao_enade'];
                    $texto_enade_2b = 'Motivo: ' . $enade2['motivo_enade']; 
                }
            }
           
           
            if($dados_representacao->CodigoCurriculo)
            {
                $curriculo = "Código do currículo integralizado pelo aluno: " . $dados_representacao->CodigoCurriculo;
            }
            else
            {
                $curriculo = "";
            }
            
            
            $infos_adicionais[] = array('TextoEnade1A' => $texto_enade_1a,
                                        'TextoEnade1B' => $texto_enade_1b,
                                        'TextoEnade2A' => $texto_enade_2a,
                                        'TextoEnade2B' => $texto_enade_2b,
                                        'InformacoesAdicionais' => $dados_representacao->InformacoesAdicionais,
                                        'CodigoCurriculo' => $curriculo);
            
            //Limpa variável para garantir integridade
            TSession::setValue('infos_adicionais', NULL); 
            
            //Passa os dados para a representação do histórico
            TSession::setValue('infos_adicionais', $infos_adicionais);
            
                       
            $this->onDownloadRepresentacao($param);
          
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public static function onDownloadRepresentacao($param)
    {
        $html_impressao = new THtmlRenderer('app/resources/HistoricoDigital.html');
        
        
        //Informações gerais 
        $dados_gerais = TSession::getValue('dados_gerais');
        $html_impressao->enableSection('main', $dados_gerais);
        
        
        //Disciplinas
        $dados_disciplinas = TSession::getValue('disciplinas');             
        $html_impressao->enableSection('disciplinas', $dados_disciplinas, TRUE);
        
        $disciplinas_ch_integralizada = TSession::getValue('disciplinas_ch_integralizada'); 
        $html_impressao->enableSection('disciplinas_ch_integralizada', $disciplinas_ch_integralizada, TRUE);
        
        
        //Atividades
        $dados_atividades = TSession::getValue('atividades');
        
        if($dados_atividades)
        {
            $html_impressao->enableSection('tabela-atividades');
            $html_impressao->enableSection('atividades', $dados_atividades, TRUE);
            
            
            //Carga horária total das atividades
            $ch_obrigatoria_atividades = TSession::getValue('ch_obrigatoria_atividades');
    
            if($ch_obrigatoria_atividades)
            {
                $html_impressao->enableSection('ch-obrigatoria-atividades', $ch_obrigatoria_atividades, TRUE);
            }
        }
        
        $atividades_ch_integralizada = TSession::getValue('atividades_ch_integralizada'); 
        $html_impressao->enableSection('atividades_ch_integralizada', $atividades_ch_integralizada, TRUE);
          
            
        //Estágios
        $dados_estagios = TSession::getValue('estagios');
        
        if($dados_estagios)
        {
            $html_impressao->enableSection('tabela-estagios');
            $html_impressao->enableSection('estagios', $dados_estagios, TRUE);
            
            
            //Carga horária total dos estágios
            $ch_obrigatoria_estagios = TSession::getValue('ch_obrigatoria_estagios');
    
            if($ch_obrigatoria_estagios)
            {
                $html_impressao->enableSection('ch-obrigatoria-estagios', $ch_obrigatoria_estagios, TRUE);
            } 
        } 
        
        $estagios_ch_integralizada = TSession::getValue('estagios_ch_integralizada'); 
        $html_impressao->enableSection('estagios_ch_integralizada', $estagios_ch_integralizada, TRUE);
            
            
        //Situações
        $dados_situacoes = TSession::getValue('situacoes');
        $html_impressao->enableSection('situacoes', $dados_situacoes, TRUE);
            
            
        //Observações 
        $dados_adicionais = TSession::getValue('infos_adicionais');
        $html_impressao->enableSection('observacoes', $dados_adicionais, TRUE); 
        
                       
        $contents = $html_impressao->getContents();
            
        $options = new \Dompdf\Options();
        $options->setChroot(getcwd());
            
        // converts the HTML template into PDF
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($contents);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
                
        $file = 'app/output/historico-' . $dados_gerais['CodigoValidacao'] . '.pdf';
                
        file_put_contents($file, $dompdf->output());
                  
        $window = TWindow::create('Representação Visual do Histórico', 0.8, 0.8);
        $object = new TElement('object');
        $object->data  = $file.'?rndval='.uniqid();
        $object->type  = 'application/pdf';
        $object->style = "width: 100%; height:calc(100% - 10px)";
        $window->add($object);
        $window->show();
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