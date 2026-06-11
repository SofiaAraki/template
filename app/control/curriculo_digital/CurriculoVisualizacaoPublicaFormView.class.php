<?php

class CurriculoVisualizacaoPublicaFormView extends TPage
{
    private $panel;
    private $notebook;
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    /*Se digitar a URL única direto ou tentar acessar pelo QrCode, vai para 'onSetDadosCurriculo'. 
    Se preencher o código e pedir para verificar, o JS força a verificação do código*/
    
    
    public function onVerificaCodigo($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $url_amigavel = $param['url_amigavel'];
            $codigo_mec = $param['codigo_mec'];            
            $codigo = $param['codigo_validacao'];

    
            if(($codigo == NULL) OR (empty($codigo)))
            {
                $action_recarregar1 = new TAction([$this, 'onDirecionaRecaptcha'], ['url_amigavel' => $url_amigavel]);            
                new TMessage('error', "Não foi possível localizar o currículo. Verifique o código de validação e tente novamente.", $action_recarregar1);
                die;
            }
                
            $token = $param['token'];
            $action = $param['action'];    
            
            //Chamar curl para solicitação POST
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => '6LcDilIeAAAAAEFxeU9YQb4omkvihSfRKHpatzIb', 'response' => $token)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
    
            curl_close($ch);
            
            $array_response = json_decode($response, true);
    
     
            if($array_response["success"] == '1' && $array_response["action"] == $action && $array_response["score"] >= 0.5)
            {
                //Traz o currículo que corresponde ao código de validação (deve ser único)
                $count = CurriculoDigital::where('codigo_validacao', "=", $codigo)
                                         ->where('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE codigo_mec = ' . $codigo_mec . ')')
                                         ->count();
                
                if($count == 1)
                {
                    $dados_curriculo = CurriculoDigital::where('codigo_validacao', "=", $codigo)
                                                       ->where('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE codigo_mec = ' . $codigo_mec . ')')
                                                       ->load();
                    
                    $param['codigo_validacao'] = $dados_curriculo[0]->codigo_validacao;
                    
                    $this->onSetDadosCurriculo($param);
                }
                else
                {
                    $action_recarregar2 = new TAction([$this, 'onDirecionaRecaptcha'], ['url_amigavel' => $url_amigavel]);            
                    new TMessage('error', "Não foi possível localizar o currículo. Verifique o código de validação e tente novamente.", $action_recarregar2);
                    die;
                } 
            }
            else
            {
                $action_recarregar3 = new TAction([$this, 'onDirecionaRecaptcha'], ['url_amigavel' => $url_amigavel]);           
                new TMessage('error', "Não foi possível localizar o currículo. Verifique o código de validação e tente novamente.", $action_recarregar3);
                die;
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }     
    }
    
    
    public function onDirecionaRecaptcha($param)
    {
        //Chama o formulário ConsultaCurriculoDigitalForm usando a rota    
        TScript::create("window.location.href = 'consultacurriculo';");
    }
    
    
    public function onSetDadosCurriculo($param)
    {
        try
        {
            $url_amigavel = $param['url_amigavel'];
            $codigo = $param['codigo_validacao'];            

            
            TTransaction::open('Felabs_DB');                                    

            //Traz o currículo que corresponde ao código de validação (deve ser único)
            $count = CurriculoDigital::where('codigo_validacao', '=', $codigo)->count();
            
            if($count == 1)
            {
                $dados_curriculo = CurriculoDigital::where('codigo_validacao', '=', $codigo)->load();
                    
                $curriculo = CurriculoDigital::find($dados_curriculo[0]->id);
                
                if($curriculo->status_publicacao == 0)
                {  
                    $action_recarregar1 = new TAction([$this, 'onDirecionaRecaptcha'], ['url_amigavel' => $url_amigavel]);          
                    new TMessage('error', "Não foi possível localizar o currículo. Verifique o código de validação e tente novamente.", $action_recarregar1);
                    die;
                }
                else
                { 
                    $param['id_curriculo'] = $curriculo->id;
                        
                    $this->onLerDadosXml($param);
                }    
            }                
            else
            {
                $action_recarregar2 = new TAction([$this, 'onDirecionaRecaptcha'], ['url_amigavel' => $url_amigavel]);           
                new TMessage('error', "Não foi possível localizar o currículo. Verifique o código de validação e tente novamente.", $action_recarregar2);
                die;
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
            $id_curriculo = $param['id_curriculo'];
            
            TTransaction::open('Felabs_DB');
            
            $curriculo_digital = new CurriculoDigital($id_curriculo);
            
            //Passo 1: Lê o xml (para garantir que os dados estejam iguais) e salva em uma variável todas as informações que compõem a representação visual
            $target_file = $curriculo_digital->caminho_arquivo . '/' . $curriculo_digital->arquivo;
            
            $xml_curriculo = simplexml_load_file($target_file);                       
            
            $unidades = [];
            $u = 0;            
            $atividades = [];
            $ac = 0;
            $criterios = [];
            $c = 0;
            
            $dados_representacao = new StdClass();
                                             
            foreach($xml_curriculo->infCurriculoEscolar as $tags_dados_curriculo)
            {
                //CURRÍCULO
                $dados_representacao->CodigoCurriculo = (string) $tags_dados_curriculo->CodigoCurriculo;
                $dados_representacao->DataCurriculo = (string) TDate::date2br($tags_dados_curriculo->DataCurriculo);
                $dados_representacao->MinutosRelogioDaHoraAula = (string) $tags_dados_curriculo->MinutosRelogioDaHoraAula;
                $dados_representacao->NomeParaAreas = (string) $tags_dados_curriculo->NomeParaAreas;
                
                
                //CURSO
                foreach($tags_dados_curriculo->DadosCurso as $dados_curso)
                {
                    $dados_representacao->NomeCurso = (string) $dados_curso->NomeCurso;     
                    
                    if($dados_curso->CodigoCursoEMEC)
                    {
                        $dados_representacao->CodigoCursoEMEC = (string) $dados_curso->CodigoCursoEMEC;       
                    }
                    else
                    {
                        foreach($dados_curso->SemCodigoCursoEMEC as $dados_tramitacao_curso)
                        {
                            $dados_representacao->EmecCursoNumeroProcesso = (string) $dados_tramitacao_curso->NumeroProcesso;  
                            $dados_representacao->EmecCursoTipoProcesso = (string) $dados_tramitacao_curso->TipoProcesso;  
                            $dados_representacao->EmecCursoDataCadastro = (string) TDate::date2br($dados_tramitacao_curso->DataCadastro);  
                            $dados_representacao->EmecCursoDataProtocolo = (string) TDate::date2br($dados_tramitacao_curso->DataProtocolo);      
                        }
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
                                    $dados_representacao->RenovacaoCursoDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao->DataCadastro);
                                    $dados_representacao->RenovacaoCursoDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao->DataProtocolo); 
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecCursoAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->RenovacaoCursoTipo = (string) $dados_renovacao_curso->Tipo;
                                $dados_representacao->RenovacaoCursoNumero = (string) $dados_renovacao_curso->Numero;
                                $dados_representacao->RenovacaoCursoData = (string) TDate::date2br($dados_renovacao_curso->Data);
                                $dados_representacao->RenovacaoCursoVeiculoPublicacao = (string) $dados_renovacao_curso->VeiculoPublicacao;
                                $dados_representacao->RenovacaoCursoDataPublicacao = (string) TDate::date2br($dados_renovacao_curso->DataPublicacao);
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
                                    $dados_representacao->ReconhecimentoCursoDataCadastro = (string) TDate::date2br($dados_tramitacao_reconhecimento->DataCadastro);
                                    $dados_representacao->ReconhecimentoCursoDataProtocolo = (string) TDate::date2br($dados_tramitacao_reconhecimento->DataProtocolo); 
                                }    
                            }
                            else
                            {
                                $dados_representacao->EmecCursoAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->ReconhecimentoCursoTipo = (string) $dados_reconhecimento_curso->Tipo;
                                $dados_representacao->ReconhecimentoCursoNumero = (string) $dados_reconhecimento_curso->Numero;
                                $dados_representacao->ReconhecimentoCursoData = (string) TDate::date2br($dados_reconhecimento_curso->Data);
                                $dados_representacao->ReconhecimentoCursoVeiculoPublicacao = (string) $dados_reconhecimento_curso->VeiculoPublicacao;
                                $dados_representacao->ReconhecimentoCursoDataPublicacao = (string) TDate::date2br($dados_reconhecimento_curso->DataPublicacao);
                                $dados_representacao->ReconhecimentoCursoSecaoPublicacao = (string) $dados_reconhecimento_curso->SecaoPublicacao;
                                $dados_representacao->ReconhecimentoCursoPaginaPublicacao = (string) $dados_reconhecimento_curso->PaginaPublicacao;
                                $dados_representacao->ReconhecimentoCursoNumeroDOU = (string) $dados_reconhecimento_curso->NumeroDOU;
                            }
                        }
                    }  
                }

                
                //EMISSORA
                foreach($tags_dados_curriculo->IesEmissora as $dados_emissora)
                {
                    $dados_representacao->NomeEmissora = (string) $dados_emissora->Nome;
                    $dados_representacao->CodigoMecEmissora = (string) $dados_emissora->CodigoMEC;
                    $dados_representacao->CnpjEmissora = (string) $dados_emissora->CNPJ;
                    
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
                                    $dados_representacao->RenovacaoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao->DataCadastro);
                                    $dados_representacao->RenovacaoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao->DataProtocolo); 
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecEmissoraAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->RenovacaoEmissoraTipo = (string) $dados_renovacao_emissora->Tipo;
                                $dados_representacao->RenovacaoEmissoraNumero = (string) $dados_renovacao_emissora->Numero;
                                $dados_representacao->RenovacaoEmissoraData = (string) TDate::date2br($dados_renovacao_emissora->Data);
                                $dados_representacao->RenovacaoEmissoraVeiculoPublicacao = (string) $dados_renovacao_emissora->VeiculoPublicacao;
                                $dados_representacao->RenovacaoEmissoraDataPublicacao = (string) TDate::date2br($dados_renovacao_emissora->DataPublicacao);
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
                                    $dados_representacao->RecredenciamentoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_recredenciamento->DataCadastro);
                                    $dados_representacao->RecredenciamentoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_recredenciamento->DataProtocolo); 
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecEmissoraAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->RecredenciamentoEmissoraTipo = (string) $dados_recredenciamento_emissora->Tipo;
                                $dados_representacao->RecredenciamentoEmissoraNumero = (string) $dados_recredenciamento_emissora->Numero;
                                $dados_representacao->RecredenciamentoEmissoraData = (string) TDate::date2br($dados_recredenciamento_emissora->Data);
                                $dados_representacao->RecredenciamentoEmissoraVeiculoPublicacao = (string) $dados_recredenciamento_emissora->VeiculoPublicacao;
                                $dados_representacao->RecredenciamentoEmissoraDataPublicacao = (string) TDate::date2br($dados_recredenciamento_emissora->DataPublicacao);
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
                                    $dados_representacao->CredenciamentoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_credenciamento->DataCadastro);
                                    $dados_representacao->CredenciamentoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_credenciamento->DataProtocolo); 
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecEmissoraAtoRegulatorio = "Ato regulatório";
                                    
                                $dados_representacao->CredenciamentoEmissoraTipo = (string) $dados_credenciamento_emissora->Tipo;
                                $dados_representacao->CredenciamentoEmissoraNumero = (string) $dados_credenciamento_emissora->Numero;
                                $dados_representacao->CredenciamentoEmissoraData = (string) TDate::date2br($dados_credenciamento_emissora->Data);
                                $dados_representacao->CredenciamentoEmissoraVeiculoPublicacao = (string) $dados_credenciamento_emissora->VeiculoPublicacao;
                                $dados_representacao->CredenciamentoEmissoraDataPublicacao = (string) TDate::date2br($dados_credenciamento_emissora->DataPublicacao);
                                $dados_representacao->CredenciamentoEmissoraSecaoPublicacao = (string) $dados_credenciamento_emissora->SecaoPublicacao;
                                $dados_representacao->CredenciamentoEmissoraPaginaPublicacao = (string) $dados_credenciamento_emissora->PaginaPublicacao;
                                $dados_representacao->CredenciamentoEmissoraNumeroDOU = (string) $dados_credenciamento_emissora->NumeroDOU;
                            }
                        }
                    }
                }

                
                //ESTRUTURA CURRICULAR
                foreach($tags_dados_curriculo->infEstruturaCurricular as $tag_estrutura_curricular)
                {     
                    //ESTRUTURA UNIDADES CURRICULARES           
                    foreach($tag_estrutura_curricular->UnidadeCurricular as $dados_unidade)
                    {                               
                        $unidades[$u]['tipo_unidade'] = (string) $dados_unidade->Tipo;
                        $unidades[$u]['codigo_unidade'] = (string) $dados_unidade->Codigo;
                        $unidades[$u]['nome_unidade'] = (string) $dados_unidade->Nome;
                        $unidades[$u]['ch_hora_aula_unidade'] = (string) $dados_unidade->CargaHorariaEmHoraAula;
                        $unidades[$u]['ch_hora_relogio_unidade'] = (string) $dados_unidade->CargaHorariaEmHoraRelogio;
                                                                                   
                        foreach($dados_unidade->Ementa as $dados_ementa)
                        {
                            $unidades[$u]['ementa_unidade'] = (string) $dados_ementa->ItemEmenta;
                        }
                                
                        $unidades[$u]['etapa_unidade'] = (string) $dados_unidade->Fase;        
                        
                        $p = 1;
                        
                        foreach($dados_unidade->PreRequisitos as $dados_pre_requisitos)
                        { 
                            foreach($dados_pre_requisitos->CodigoDependencia as $dados_codigo_dependencia)                                     
                            {
                                if($dados_codigo_dependencia)
                                {
                                    $unidades[$u]['disciplina_pre_requisitada'][$p] = (string) $dados_codigo_dependencia;
                                }      
                                     
                                $p++; 
                            }      
                        }
                        
                        $e = 1;
                                                          
                        foreach($dados_unidade->Etiquetas as $dados_etiquetas)
                        { 
                             foreach($dados_etiquetas->Etiqueta as $dados_etiqueta)                                     
                             {
                                 if($dados_etiqueta)
                                 {
                                     $unidades[$u]['codigo_etiqueta'][$e] = (string) $dados_etiqueta->Codigo;
                                 }      
                                 
                                 $e++;
                             }   
                        }
                        
                        $a = 1;
                                                          
                        foreach($dados_unidade->Areas as $dados_areas)
                        { 
                             foreach($dados_areas->Area as $dados_area)                                     
                             {
                                 if($dados_area)
                                 {
                                     $unidades[$u]['codigo_area'][$a] = (string) $dados_area->Codigo;
                                 }      
                                 
                                 $a++;
                             }   
                        }
                        
                        $u++; 
                    }                                                                                                   
                }
                
                
                //ESTRUTURA ATIVIDADES COMPLEMENTARES
                $at = 1;
                
                foreach($tags_dados_curriculo->infEstruturaAtividadesComplementares as $tag_estrutura_atividades)
                {     
                    foreach($tag_estrutura_atividades->Categoria as $dados_categoria)
                    {
                        $atividades[$ac]['categoria_codigo'] = (string) $dados_categoria->Codigo;
                        $atividades[$ac]['categoria_nome'] = (string) $dados_categoria->Nome;
                        $atividades[$ac]['categoria_limite_ch_hora_relogio'] = (string) $dados_categoria->LimiteCargaHorariaEmHoraRelogio;
                        
                        foreach($dados_categoria->Atividades as $dados_atividades)
                        {
                            foreach($dados_atividades as $dados_atividade)
                            {
                                if($dados_atividade)
                                {
                                    $atividades[$ac]['atividade_codigo'][$at] = (string) $dados_atividade->Codigo;
                                    $atividades[$ac]['atividade_nome'][$at] = (string) $dados_atividade->Nome;
                                    $atividades[$ac]['atividade_limite_ch_hora_relogio'][$at] = (string) $dados_atividade->LimiteCargaHorariaEmHoraRelogio;

                                }
                                
                                $at++;
                            }
                        }
                        
                        $ac++;   
                    } 
                }
                 
                
                //ESTRUTURA CRITÉRIOS INTEGRALIZAÇÃO
                $ci = 1;
                
                foreach($tags_dados_curriculo->infCriteriosIntegralizacao as $tag_estrutura_criterios)
                {     
                    foreach($tag_estrutura_criterios->CriterioIntegralizacaoRotulos as $dados_criterio)
                    {
                        //Se o critério fizer parte da carga horária total do curso
                        if(!empty($dados_criterio->CargasHorariasCriterio->CargaHorariaParaTotal))
                        {
                            $criterios[$c]['criterio_codigo'] = (string) $dados_criterio->Codigo;
                            $criterios[$c]['criterio_unidade'] = (string) $dados_criterio->UnidadeCurricular;
                            
                            foreach($dados_criterio->Etiqueta as $dados_etiqueta)
                            {
                                if($dados_etiqueta)
                                {
                                    $criterios[$c]['criterio_etiqueta'][$ci] = (string) $dados_etiqueta;                        
                                }
                                
                                $ci++;
                            }
                                                        
                            foreach($dados_criterio->CargasHorariasCriterio as $dados_ch_criterio)
                            {
                                if($dados_ch_criterio)
                                {
                                    $criterios[$c]['criterio_ch_minima'] = (string) $dados_ch_criterio->CargaHorariaMinima;
                                    $criterios[$c]['criterio_ch_maxima'] = (string) $dados_ch_criterio->CargaHorariaMaxima;
                                    $criterios[$c]['criterio_ch_total'] = (string) $dados_ch_criterio->CargaHorariaParaTotal;
                                }   
                            }
                            
                            $c++;  
                        }     
                    } 
                }
                
                
                //CÓDIGO DE VALIDAÇÃO CURRÍCULO
                foreach($tags_dados_curriculo->SegurancaCurriculo as $dados_seguranca)
                {
                    $dados_representacao->CodigoValidacaoCurriculo = (string) $dados_seguranca->CodigoValidacao;
                }
                
                
                //INFORMAÇÕES ADICIONAIS
                foreach($tags_dados_curriculo->InformacoesAdicionais as $dados_adicionais)
                {
                    $dados_representacao->InformacoesAdicionais = (string) $dados_adicionais;
                }
            }         
                           

            //Limpa variável para garantir integridade
            TSession::setValue('dados_representacao', NULL);
            TSession::setValue('dados_representacao', $dados_representacao);
            
            TSession::setValue('dados_unidades', NULL);
            TSession::setValue('dados_unidades', $unidades);
            
            TSession::setValue('dados_atividades', NULL);
            TSession::setValue('dados_atividades', $atividades);
            
            TSession::setValue('dados_criterios', NULL);
            TSession::setValue('dados_criterios', $criterios);            
            
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
            
            $id_curriculo = $param['id_curriculo'];
            
            $curriculo_digital = new CurriculoDigital($id_curriculo);
            
            $emissora = new DiplomaDigitalEmissora($curriculo_digital->dados_emissora_id);
            
            
            //Pega os dados do xml que vão compor o cabeçalho do currículo
            $dados_representacao = TSession::getValue('dados_representacao');           
            
            
            //Emissora - dados EMEC
            if($dados_representacao->EmecEmissora == "Renovação de recredenciamento") 
            {
                if($dados_representacao->EmecEmissoraAtoRegulatorio)
                {
                    $texto_emec_emissora = $dados_representacao->EmecEmissora . " pelo(a) " . 
                                           $dados_representacao->RenovacaoEmissoraTipo . " nº " .
                                           $dados_representacao->RenovacaoEmissoraNumero . ", de " .
                                           $dados_representacao->RenovacaoEmissoraData . ", publicado(a) no " .
                                           $dados_representacao->RenovacaoEmissoraVeiculoPublicacao . " nº " .
                                           $dados_representacao->RenovacaoEmissoraNumeroDOU . ", seção " . 
                                           $dados_representacao->RenovacaoEmissoraSecaoPublicacao . ", pág. " .
                                           $dados_representacao->RenovacaoEmissoraPaginaPublicacao . " em " .
                                           $dados_representacao->RenovacaoEmissoraDataPublicacao;
                }
                else
                {
                    $texto_emec_emissora = "Processo de " . $dados_representacao->RenovacaoEmissoraTipoProcesso .
                                           " cadastrado em " . $dados_representacao->RenovacaoEmissoraDataCadastro .
                                           " e protocolado em " . $dados_representacao->RenovacaoEmissoraDataProtocolo .
                                           " sob o nº " . $dados_representacao->RenovacaoEmissoraNumeroProcesso .
                                           " junto ao e-mec";
                    
                                           /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecEmissora . 
                                           " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->RenovacaoEmissoraNumeroProcesso .
                                           ", " . $dados_representacao->RenovacaoEmissoraTipoProcesso . " cadastrado em " .
                                           $dados_representacao->RenovacaoEmissoraDataCadastro . " e protocolado em " .
                                           $dados_representacao->RenovacaoEmissoraDataProtocolo;*/
                }
            }                
            elseif($dados_representacao->EmecEmissora == "Recredenciamento")
            {
                if($dados_representacao->EmecEmissoraAtoRegulatorio)
                {
                    $texto_emec_emissora = "Recredenciada pelo(a) " . $dados_representacao->RecredenciamentoEmissoraTipo . 
                                           " nº " . $dados_representacao->RecredenciamentoEmissoraNumero . ", de " .
                                           $dados_representacao->RecredenciamentoEmissoraData . ", publicado(a) no " .
                                           $dados_representacao->RecredenciamentoEmissoraVeiculoPublicacao . " nº " .
                                           $dados_representacao->RecredenciamentoEmissoraNumeroDOU . ", seção " . 
                                           $dados_representacao->RecredenciamentoEmissoraSecaoPublicacao . ", pág. " .
                                           $dados_representacao->RecredenciamentoEmissoraPaginaPublicacao . " em " .
                                           $dados_representacao->RecredenciamentoEmissoraDataPublicacao;
                }
                else
                {
                    $texto_emec_emissora = "Processo de " . $dados_representacao->RecredenciamentoEmissoraTipoProcesso .
                                           " cadastrado em " . $dados_representacao->RecredenciamentoEmissoraDataCadastro .
                                           " e protocolado em " . $dados_representacao->RecredenciamentoEmissoraDataProtocolo .
                                           " sob o nº " . $dados_representacao->RecredenciamentoEmissoraNumeroProcesso .
                                           " junto ao e-mec";
                    
                                           /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecEmissora . 
                                           " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->RecredenciamentoEmissoraNumeroProcesso .
                                           ", " . $dados_representacao->RecredenciamentoEmissoraTipoProcesso . " cadastrado em " .
                                           $dados_representacao->RecredenciamentoEmissoraDataCadastro . " e protocolado em " .
                                           $dados_representacao->RecredenciamentoEmissoraDataProtocolo;*/
                }
            }            
            else
            {
                if($dados_representacao->EmecEmissoraAtoRegulatorio)
                {
                    $texto_emec_emissora = "Credenciada pelo(a) " . $dados_representacao->CredenciamentoEmissoraTipo . 
                                           " nº " . $dados_representacao->CredenciamentoEmissoraNumero . ", de " .
                                           $dados_representacao->CredenciamentoEmissoraData . ", publicado(a) no " .
                                           $dados_representacao->CredenciamentoEmissoraVeiculoPublicacao . " nº " .
                                           $dados_representacao->CredenciamentoEmissoraNumeroDOU . ", seção " . 
                                           $dados_representacao->CredenciamentoEmissoraSecaoPublicacao . ", pág. " .
                                           $dados_representacao->CredenciamentoEmissoraPaginaPublicacao . " em " .
                                           $dados_representacao->CredenciamentoEmissoraDataPublicacao;
                }
                else
                {
                    $texto_emec_emissora = "Processo de " . $dados_representacao->CredenciamentoEmissoraTipoProcesso .
                                           " cadastrado em " . $dados_representacao->CredenciamentoEmissoraDataCadastro .
                                           " e protocolado em " . $dados_representacao->CredenciamentoEmissoraDataProtocolo .
                                           " sob o nº " . $dados_representacao->CredenciamentoEmissoraNumeroProcesso .
                                           " junto ao e-mec";
                    
                                           /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecEmissora . 
                                           " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->CredenciamentoEmissoraNumeroProcesso .
                                           ", " . $dados_representacao->CredenciamentoEmissoraTipoProcesso . " cadastrado em " .
                                           $dados_representacao->CredenciamentoEmissoraDataCadastro . " e protocolado em " .
                                           $dados_representacao->CredenciamentoEmissoraDataProtocolo;*/
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
            
            
            //Código e-mec curso ou dados de tramitação
            if($dados_representacao->CodigoCursoEMEC)
            {
                $com_ou_sem_codigo_emec_curso = "Código E-MEC: " . $dados_representacao->CodigoCursoEMEC;    
            }
            else
            {
                $com_ou_sem_codigo_emec_curso = "Processo de " . $dados_representacao->EmecCursoTipoProcesso .
                                                " cadastrado em " . $dados_representacao->EmecCursoDataCadastro .
                                                " e protocolado em " . $dados_representacao->EmecCursoDataProtocolo .
                                                " sob o nº " . $dados_representacao->EmecCursoNumeroProcesso .
                                                " junto ao e-mec";
                
                                                /*"Processo de tramitação junto ao E-MEC: Processo nº " . 
                                                $dados_representacao->EmecCursoNumeroProcesso . ", " . 
                                                $dados_representacao->EmecCursoTipoProcesso . " cadastrado em " . 
                                                $dados_representacao->EmecCursoDataCadastro . " e protocolado em " . 
                                                $dados_representacao->EmecCursoDataProtocolo;*/   
            }
            

            //Curso - EMEC
            if($dados_representacao->EmecCurso == "Renovação de reconhecimento") 
            {
                if($dados_representacao->EmecCursoAtoRegulatorio)
                {
                    $texto_emec_curso = $dados_representacao->EmecCurso . " pelo(a) " . 
                                        $dados_representacao->RenovacaoCursoTipo . " nº " .
                                        $dados_representacao->RenovacaoCursoNumero . ", de " .
                                        $dados_representacao->RenovacaoCursoData . ", publicado(a) no " .
                                        $dados_representacao->RenovacaoCursoVeiculoPublicacao . " nº " .
                                        $dados_representacao->RenovacaoCursoNumeroDOU . ", seção " . 
                                        $dados_representacao->RenovacaoCursoSecaoPublicacao . ", pág. " .
                                        $dados_representacao->RenovacaoCursoPaginaPublicacao . " em " .
                                        $dados_representacao->RenovacaoCursoDataPublicacao;
                }
                else
                {
                    $texto_emec_curso = "Processo de " . $dados_representacao->RenovacaoCursoTipoProcesso .
                                        " cadastrado em " . $dados_representacao->RenovacaoCursoDataCadastro .
                                        " e protocolado em " . $dados_representacao->RenovacaoCursoDataProtocolo .
                                        " sob o nº " . $dados_representacao->RenovacaoCursoNumeroProcesso .
                                        " junto ao e-mec";
                    
                                        /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecCurso . 
                                        " do curso junto ao E-MEC: Processo de nº " . $dados_representacao->RenovacaoCursoNumeroProcesso .
                                        ", " . $dados_representacao->RenovacaoCursoTipoProcesso . " cadastrado em " .
                                        $dados_representacao->RenovacaoCursoDataCadastro . " e protocolado em " .
                                        $dados_representacao->RenovacaoCursoDataProtocolo;*/
                }    
            }            
            else
            {
                if($dados_representacao->EmecCursoAtoRegulatorio)
                {
                    $texto_emec_curso = "Reconhecido pelo(a) " . $dados_representacao->ReconhecimentoCursoTipo . 
                                        " nº " . $dados_representacao->ReconhecimentoCursoNumero . ", de " .
                                        $dados_representacao->ReconhecimentoCursoData . ", publicado(a) no " .
                                        $dados_representacao->ReconhecimentoCursoVeiculoPublicacao . " nº " .
                                        $dados_representacao->ReconhecimentoCursoNumeroDOU . ", seção " . 
                                        $dados_representacao->ReconhecimentoCursoSecaoPublicacao . ", pág. " .
                                        $dados_representacao->ReconhecimentoCursoPaginaPublicacao . " em " .
                                        $dados_representacao->ReconhecimentoCursoDataPublicacao;
                }
                else
                {
                    $texto_emec_curso = "Processo de " . $dados_representacao->ReconhecimentoCursoTipoProcesso .
                                        " cadastrado em " . $dados_representacao->ReconhecimentoCursoDataCadastro .
                                        " e protocolado em " . $dados_representacao->ReconhecimentoCursoDataProtocolo .
                                        " sob o nº " . $dados_representacao->ReconhecimentoCursoNumeroProcesso .
                                        " junto ao e-mec";
                    
                                        /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecCurso . 
                                        " do curso junto ao E-MEC: Processo de nº " . $dados_representacao->ReconhecimentoCursoNumeroProcesso .
                                        ", " . $dados_representacao->ReconhecimentoCursoTipoProcesso . " cadastrado em " .
                                        $dados_representacao->ReconhecimentoCursoDataCadastro . " e protocolado em " .
                                        $dados_representacao->ReconhecimentoCursoDataProtocolo;*/
                }    
            }
            
            
            $dados_curriculo = new StdClass;
            $dados_curriculo->IdCurriculo = $curriculo_digital->id;
            $dados_curriculo->CodigoCurriculo = $dados_representacao->CodigoCurriculo;
            $dados_curriculo->NomeEmissora = mb_strtoupper($dados_representacao->NomeEmissora);
            $dados_curriculo->TextoEmecEmissora = $texto_emec_emissora;
            $dados_curriculo->Dados1Emissora = $dados1_emissora;
            
            if($dados2_emissora <> NULL)
            {
                $dados_curriculo->Dados2Emissora = $dados2_emissora;
            }
            else
            {
                $dados_curriculo->Dados2Emissora = '';    
            }
            
            $dados_curriculo->NomeCurso = mb_strtoupper($dados_representacao->NomeCurso);
            $dados_curriculo->CodigoEmecCurso = $com_ou_sem_codigo_emec_curso;
            $dados_curriculo->TextoEmecCurso = $texto_emec_curso;
            $dados_curriculo->CodigoValidacao = $dados_representacao->CodigoValidacaoCurriculo;
            $dados_curriculo->CaminhoQrCode = $curriculo_digital->caminho_qrcode . '/' . $curriculo_digital->qrcode;
            

            if($dados_representacao->InformacoesAdicionais)
            {
                $dados_curriculo->InformacoesAdicionais = $dados_representacao->InformacoesAdicionais;
            }
            else
            {
                $dados_curriculo->InformacoesAdicionais = "-";
            }
            
            $replace = [];
            $replace['object'] = $dados_curriculo;
                                
                                 
            //Limpa variável para garantir integridade
            TSession::setValue('informacoes_curriculo', NULL);
            
            //Passa os dados para a representação do currículo
            TSession::setValue('informacoes_curriculo', $replace);      


            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Unidades
            $dados_unidades = TSession::getValue('dados_unidades');
            
            //Agrupa por etapa
            $unidades_agrupadas = array_reduce($dados_unidades, function($result, $item) {
            
                //Etiquetas
                $etiquetas = implode(', ', $item['codigo_etiqueta']);
                $etiquetas = mb_strtoupper($etiquetas);
                $item['etiquetas'] = $etiquetas;                
                        
                //Pré-requisitos
                $pre_requisitos = implode(', ', $item['disciplina_pre_requisitada']);
                $pre_requisitos = mb_strtoupper($pre_requisitos);
                $item['pre_requisitos'] = $pre_requisitos;
                       
                if($item['etapa_unidade']) 
                {          
                    $result[$item['etapa_unidade']][] = $item;
                }
                else
                {
                    $result['0'][] = $item;
                }
                
                return $result;    
            }, array());
            
            
            foreach($unidades_agrupadas as $etapa => $unidades_etapa)
            {
                //Unidades da grade
                if($etapa <> 0)
                {
                    foreach($unidades_etapa as $key => $unidade) 
                    {
                        $info_unidades_grade[] = ['codigo_unidade' => $unidade['codigo_unidade'],
                                                  'nome_unidade' => $unidade['nome_unidade'],
                                                  'etiquetas' => $unidade['etiquetas'],
                                                  'ch_hora_aula_unidade' => $unidade['ch_hora_aula_unidade'],
                                                  'ch_hora_relogio_unidade' => (int) $unidade['ch_hora_relogio_unidade'],
                                                  'pre_requisitos' => $unidade['pre_requisitos'],
                                                  'ementa_unidade' => $unidade['ementa_unidade'] ];
                    }
        
                    $replace['grade'][] =  [ 'etapa_unidade' => (string) $etapa . "º CICLO",
                                             'unidades_grade' => $info_unidades_grade ] ;                                                                                                                                 
            
                    //Limpa o array para a próxima iteração
                    $info_unidades_grade = [];
                }
                
                //Optativas
                else
                {
                    foreach($unidades_etapa as $key => $unidade) 
                    {
                        $info_unidades_optativa[] = ['codigo_unidade' => $unidade['codigo_unidade'],
                                                     'nome_unidade' => $unidade['nome_unidade'],
                                                     'etiquetas' => $unidade['etiquetas'],
                                                     'ch_hora_aula_unidade' => $unidade['ch_hora_aula_unidade'],
                                                     'ch_hora_relogio_unidade' => (int) $unidade['ch_hora_relogio_unidade'],
                                                     'pre_requisitos' => $unidade['pre_requisitos'],
                                                     'ementa_unidade' => $unidade['ementa_unidade'] ];
                    }
        
                    $replace['optativa'][] =  [ 'etapa_unidade' => "OPTATIVAS",
                                                'unidades_optativa' => $info_unidades_optativa ] ;                                                                                                                                 
                
                    //Limpa o array para a próxima iteração
                    $info_unidades_optativa = [];
                }    
            } 
         

            //Acrescenta os dados das unidades na representação do currículo
            TSession::setValue('informacoes_curriculo', $replace);                      


            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Atividades
            $dados_atividades = TSession::getValue('dados_atividades');
            
            foreach($dados_atividades as $dados_atividade)
            {
                if(!$dados_atividade['categoria_limite_ch_hora_relogio'])
                {
                    $categoria_limite_ch_hora_relogio = "-";    
                }
                else
                {
                    $categoria_limite_ch_hora_relogio = (int) $dados_atividade['categoria_limite_ch_hora_relogio'];
                }
                
                
                foreach ($dados_atividade['atividade_nome'] as $key => $nome_atividade) 
                {
                    $info_atividades[] = ['atividade_codigo' => $dados_atividade['atividade_codigo'][$key],
                                          'atividade_nome' => $nome_atividade,
                                          'atividade_limite_ch' => (int) $dados_atividade['atividade_limite_ch_hora_relogio'][$key]];
                }
                
                $info_categorias[] = ['categoria_codigo' => $dados_atividade['categoria_codigo'],
                                      'categoria_nome' => mb_strtoupper($dados_atividade['categoria_nome']),
                                      'categoria_limite_ch' => $categoria_limite_ch_hora_relogio,
                                      'atividades' => $info_atividades];
            
                //Limpa o array para a próxima iteração
                $info_atividades = [];
            }                                             
            
            $replace['atividades_complementares'][] = [ 'tipo_unidade' => "ATIVIDADES COMPLEMENTARES",
                                                        'categorias' => $info_categorias] ;
            

            //Acrescenta os dados das atividades na representação do currículo
            TSession::setValue('informacoes_curriculo', $replace);
            
            
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            //Critérios 
            $dados_criterios = TSession::getValue('dados_criterios');      
            
            foreach($dados_criterios as $dados_criterio)
            {
                $ch_total += $dados_criterio['criterio_ch_total'];
                
                //Etiquetas
                $etiquetas = implode(' ', $dados_criterio['criterio_etiqueta']);
                
                $info_criterios[] = [ 'tipo_unidade' => $dados_criterio['criterio_unidade'],
                                      'etiqueta' => $etiquetas,
                                      'ch_integralizacao' => (int) $dados_criterio['criterio_ch_total'] ];   
            }
            
            $replace['criterios_integralizacao'][] = [ 'ch_total_curso' => (int) $ch_total,
                                                       'criterios' => $info_criterios ];
            
            
            //Acrescenta os dados dos critérios na representação do currículo
            TSession::setValue('informacoes_curriculo', $replace);
            
            $this->onShow($param);
          
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onVisualizarPDF($param)
    {
        $informacoes_curriculo = TSession::getValue('informacoes_curriculo');
        
        $html = new THtmlRenderer('app/resources/CurriculoDigital.html');      
        $html->enableSection('main', $informacoes_curriculo);         
      
        $contents = $html->getContents();
          
        $options = new \Dompdf\Options();
        $options->setChroot(getcwd());
               
        // converts the HTML template into PDF
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($contents);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
                    
        $file = 'app/output/curriculo-' . $informacoes_curriculo['object']->CodigoValidacao . '.pdf';
                   
        file_put_contents($file, $dompdf->output());
                    
        $object = new TElement('object');
        $object->data  = $file;
        $object->type  = 'application/pdf';
        $object->style = "width: 100%; min-height:600px";
           
        $this->notebook->appendPage('RVCE', $object);   
    }
    
    
    public function onVisualizarXML($param)
    {
        try
        {
            $id_curriculo = $param['id_curriculo'];            
            
            TTransaction::open('Felabs_DB');

            $curriculo = new CurriculoDigital($id_curriculo);

            $file = file_get_contents("$curriculo->caminho_arquivo/$curriculo->arquivo");

            $panel_xml = new TPanelGroup('');
            $panel_xml->add("<pre>");
            $panel_xml->add(htmlentities( $file ));
            $panel_xml->add("</pre>");
            
            $this->notebook->appendPage('XML', $panel_xml);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } 
    }
            
    
    public static function onDownloadXML($param)
    {
        try
        {
            $id_curriculo = $param['id_curriculo'];            
            
            TTransaction::open('Felabs_DB');
            
            $curriculo = new CurriculoDigital($id_curriculo);

            if($curriculo->arquivo <> NULL AND $curriculo->caminho_arquivo <> NULL)
            {
                $caminho_arquivo = $curriculo->caminho_arquivo . '/' . $curriculo->arquivo;

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
    
    
    public static function onDownloadPDF($param)
    {
        $informacoes_curriculo = TSession::getValue('informacoes_curriculo');
        
        $html = new THtmlRenderer('app/resources/CurriculoDigital.html');      
        $html->enableSection('main', $informacoes_curriculo);          
                       
        $contents = $html->getContents();
            
        $options = new \Dompdf\Options();
        $options->setChroot(getcwd());
            
        // converts the HTML template into PDF
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($contents);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
                
        $file = 'app/output/curriculo-' . $informacoes_curriculo['object']->CodigoValidacao . '.pdf';
                
        file_put_contents($file, $dompdf->output());
                  
        $window = TWindow::create('Representação Visual do Currículo', 0.8, 0.8);
        $object = new TElement('object');
        $object->data  = $file;
        $object->type  = 'application/pdf';
        $object->style = "width: 100%; height:calc(100% - 10px)";
        $window->add($object);
        $window->show();
    }
    
    
    public function onShow($param)
    {
        try
        {
            $id_curriculo = $param['id_curriculo'];            

            TTransaction::open('Felabs_DB');
            
            $curriculo = new CurriculoDigital($id_curriculo);
            
            if($curriculo)
            {
                $this->panel = new TPanelGroup('CURRÍCULO DIGITAL');
                $this->panel->addHeaderActionLink('Download XML', new TAction([$this, 'onDownloadXML'], ['id_curriculo' => $curriculo->id]), 'fas:cloud-download-alt blue');
                $this->panel->addHeaderActionLink('Download Representação Visual', new TAction([$this, 'onDownloadPDF'], ['id_curriculo' => $curriculo->id]), 'fas:cloud-download-alt blue');
                $this->panel->style = 'position: absolute; top: 0; width: 100%;';    
                
                $this->notebook = new TNotebook('notebook_historico');  
                $this->panel->add($this->notebook); 
                
                parent::add($this->panel);
            }    
            
            TTransaction::close();
                        
            $this->onVisualizarXML($param);
            $this->onVisualizarPDF($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
}    