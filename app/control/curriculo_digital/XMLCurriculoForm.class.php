<?php

class XMLCurriculoForm extends TPage
{

    function __construct($param)
    {
        parent::__construct();

    }
 
  
    public function onVerificarXMLCurriculo($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');


            $curriculo_id = $param['curriculo_id'];                            
            
            $curriculo_digital = new CurriculoDigital($curriculo_id);
            

            //Se não existir diretório, cria
            $target_path = 'secretaria/curriculo_xmls/' . 'curso_' . $curriculo_digital->cod_curso;
                        
            if (!file_exists($target_path))
            {
                if (!@mkdir($target_path, 0777, true))
                {
                    throw new Exception(_t('Permission denied'). ': '. $target_path);
                }
            }

            //Se existir diretório
            if (file_exists($target_path))
            { 
                //Verifica se já existe arquivo XML. Se existir, questiona usuário
                if($curriculo_digital->status_xml == 1 OR $curriculo_digital->arquivo <> NULL)
                {
                    $action_gerar = new TAction([$this, 'onGerarCodigoValidacao']);
                    $action_gerar->setParameters(['id_curriculo_digital' => $curriculo_id, 'caminho_diretorio' => $target_path]);                   
                    
                    $action_voltar = new TAction(['CurriculoList', 'onReload']);                  
                    
                    new TQuestion('Um arquivo XML referente a este currículo já foi gerado. Deseja realmente gerar um novo arquivo e substituir o existente?', $action_gerar, $action_voltar);
                }            
                else
                {
                    $param = ['id_curriculo_digital' => $curriculo_id, 'caminho_diretorio' => $target_path];
                    $this->onGerarCodigoValidacao($param);      
                }
            }
            else
            {
                throw new Exception('Erro ao criar diretório em que o arquivo seria salvo');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    } 
           
    
    public function onGerarCodigoValidacao($param)
    {
        try
        {
            $curriculo_id = $param['id_curriculo_digital'];
            $target_path = $param['caminho_diretorio'];
            

            TTransaction::open('Felabs_DB');

            $curriculo_digital = new CurriculoDigital($curriculo_id);          
            $curso = new DiplomaDigitalCurso($curriculo_digital->dados_curso_id);           
            $emissora = new DiplomaDigitalEmissora($curso->dados_emissora_id);   
            
            
            //Formado pelo Cód. IES + Cód. de localização do currículo (Codigo|CodigoCursoEMEC|IesEmissora_CNPJ|DataCurriculo)
            if($emissora->codigo_mec)
            {
                $cod_emissora = trim($emissora->codigo_mec);
            }
            else
            {
                $action1 = new TAction(array('CurriculoList', 'onReload'));                       
                new TMessage('error', 'Verifique o código e-mec da emissora', $action1);    
                die;
            }
            
            
            //Se o curso não possuir código EMEC deve ser utilizado o nº do processo de tramitação
            if($curso->opcao_codigo_emec == "Possui código EMEC")
            {
                $emec_curso = trim($curso->codigo_curso_emec);
            }
            elseif($curso->opcao_codigo_emec == "Não possui código EMEC")
            {
                $emec_curso = trim($curso->sem_codigo_emec_numero_processo);
            }
            else
            {
                $action2 = new TAction(array('CurriculoList', 'onReload'));                       
                new TMessage('error', 'Verifique as informações sobre o e-mec do curso', $action2);    
                die; 
            }
            
   
            if($emissora->cnpj)
            {
                $validator = new TCNPJValidator;
                $validator->validate('CNPJ', $emissora->cnpj);

                $cnpj_emissora = trim($emissora->cnpj);
            }
            else
            {
                $action3 = new TAction(array('CurriculoList', 'onReload'));                       
                new TMessage('error', 'Verifique o CNPJ da emissora', $action3);    
                die;
            }
            

            //Converte data do currículo para formato brasileiro e retira caracteres
            $data_curriculo = TDate::date2br($curriculo_digital->data_curriculo);
            $data_curriculo = str_replace('/', '', $data_curriculo);            
             
                              
            $codigo_localizacao = trim((utf8_encode($curriculo_digital->codigo_curriculo)) . (utf8_encode($emec_curso)) . (utf8_encode($cnpj_emissora)) . (utf8_encode($data_curriculo)));
           
                       
            //Gera o hash com algoritmo SHA256 do código de localização (deve-se utilizar, no mínimo, 12 caracteres - coloquei 25)
            $hash = hash('sha256', $codigo_localizacao);
            $cod_localizacao = substr($hash, 0, 25);
            
               
            //Concatena o código da IES para gerar o código de validação do currículo
            $codigo_validacao = trim($cod_emissora . '.' . $cod_localizacao);
                
                
            TTransaction::close();             
           
            $param = ['id_curriculo_digital' => $curriculo_id, 'codigo_validacao' => $codigo_validacao, 'caminho_diretorio' => $target_path];
            self::onGerarXMLCurriculo($param);  
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onGerarXMLCurriculo($param)
    {
        try
        {                 
            $curriculo_id = $param['id_curriculo_digital'];
            $codigo_validacao = $param['codigo_validacao'];
            $target_path = $param['caminho_diretorio'];        
      
                     
            TTransaction::open('Felabs_DB');
            
            //Objetos que serão usados na construção do XML
            $curriculo_digital = new CurriculoDigital($curriculo_id);
            $curso = new DiplomaDigitalCurso($curriculo_digital->dados_curso_id);            
            $emissora = new DiplomaDigitalEmissora($curso->dados_emissora_id);
            $mantenedora = new DiplomaDigitalMantenedora($emissora->dados_mantenedora_id);                

  
            $target_file = $target_path . '/' . 'curriculo-' . $curriculo_digital->codigo_curriculo . '.xml';
                
            if((!file_exists($target_file) && is_writable(dirname($target_file))) OR is_writable($target_file))
            {                                
                //Versão XSD que está sendo utilizada
                $versao = DiplomaDigitalVersao::last();
  
                $data_atual = date('Y-m-d');
                

                //Compara as datas e verifica se a versão do XSD é válida
                if($data_atual >= $versao->versao_curriculo_inicio AND $data_atual <= $versao->versao_curriculo_termino)
                {
                    $versao_xsd = $versao->versao_curriculo;
                }
                else
                {
                    $action1 = new TAction(array('CurriculoList', 'onReload'));                       
                    new TMessage('error', 'Contate o setor de TI para verificar se a versão do XSD utilizada é válida', $action1);    
                    die;
                }
                
                    
                $document = new DOMDocument('1.0', 'UTF-8');
                $document->{'formatOutput'} = true;
                  
                  
                //NÓ CURRÍCULO ESCOLAR
                $noCurriculoEscolar = $document->createElement('CurriculoEscolar');
                $document->appendChild($noCurriculoEscolar);
                                
                                
                $xmlns_ns = $document->createAttribute('xmlns');
                $noCurriculoEscolar->appendChild($xmlns_ns);
                                  
                                                              
                //Em ambiente de teste o valor será "Homologação" e em ambiente real "Produção"
                /*$tipo_conexao = $_SERVER['HTTP_HOST'];
                
                if(($tipo_conexao == 'localhost') || ($tipo_conexao == '127.0.0.1'))
                {
                    $value_ns = $document->createTextNode("http://dev.feituverava.com.br/xsd");
                    $xmlns_ns->appendChild($value_ns);
                    
                    $ambiente = "Homologação";
                }
                else
                {*/
                    $value_ns = $document->createTextNode("http://portal.mec.gov.br/diplomadigital/arquivos-em-xsd");
                    $xmlns_ns->appendChild($value_ns);
                
                    $ambiente = "Produção";
                //}
                                  
                
                //NÓ INFCURRICULOESCOLAR
                $noInfCurriculoEscolar = $document->createElement('infCurriculoEscolar');
                $noInfCurriculoEscolar->setAttribute("ambiente", $ambiente); 
                $noInfCurriculoEscolar->setAttribute("versao", $versao_xsd);                          
                $noCurriculoEscolar->appendChild($noInfCurriculoEscolar);    
                             
                   
                //INFORMAÇÕES DO CURRÍCULO
                $codigo_curriculo          = $document->createElement("CodigoCurriculo", trim($curriculo_digital->codigo_curriculo));
                $data_curriculo            = $document->createElement("DataCurriculo", trim($curriculo_digital->data_curriculo));
                $minutos_relogio_hora_aula = $document->createElement("MinutosRelogioDaHoraAula", trim($curriculo_digital->duracao_aula));
                $nome_para_areas           = $document->createElement("NomeParaAreas", trim($curriculo_digital->nome_areas));
                                 
      
                $noInfCurriculoEscolar->appendChild($codigo_curriculo);
                $noInfCurriculoEscolar->appendChild($data_curriculo);
                $noInfCurriculoEscolar->appendChild($minutos_relogio_hora_aula);
                
                if($curriculo_digital->nome_areas <> NULL)
                {
                    $noInfCurriculoEscolar->appendChild($nome_para_areas);                            
                }
                

                //INFORMAÇÕES DO CURSO                  
                $noCurso = $document->createElement('DadosCurso');
                $noInfCurriculoEscolar->appendChild($noCurso);
   
                $curso_nome                            = $document->createElement("NomeCurso", trim($curso->nome_curso_diploma));
                $curso_codigo_emec                     = $document->createElement("CodigoCursoEMEC", trim($curso->codigo_curso_emec));
                $curso_sem_codigo_emec_numero_processo = $document->createElement("NumeroProcesso", trim($curso->sem_codigo_emec_numero_processo));
                $curso_sem_codigo_emec_tipo_processo   = $document->createElement("TipoProcesso", trim($curso->sem_codigo_emec_tipo_processo));
                $curso_sem_codigo_emec_data_cadastro   = $document->createElement("DataCadastro", trim($curso->sem_codigo_emec_data_cadastro));
                $curso_sem_codigo_emec_data_protocolo  = $document->createElement("DataProtocolo", trim($curso->sem_codigo_emec_data_protocolo));    
                
                $curso_autorizacao_tipo               = $document->createElement("Tipo", trim($curso->autorizacao_tipo));
                $curso_autorizacao_numero             = $document->createElement("Numero", trim($curso->autorizacao_numero));
                $curso_autorizacao_data               = $document->createElement("Data", trim($curso->autorizacao_data));                
                $curso_autorizacao_veiculo_publicacao = $document->createElement("VeiculoPublicacao", trim($curso->autorizacao_veiculo_publicacao));
                $curso_autorizacao_data_publicacao    = $document->createElement("DataPublicacao", trim($curso->autorizacao_data_publicacao));
                $curso_autorizacao_secao_publicacao   = $document->createElement("SecaoPublicacao", trim($curso->autorizacao_secao_publicacao));
                $curso_autorizacao_pagina_publicacao  = $document->createElement("PaginaPublicacao", trim($curso->autorizacao_pag_publicacao));
                $curso_autorizacao_numero_dou         = $document->createElement("NumeroDOU", trim($curso->autorizacao_numero_DOU));
                $curso_autorizacao_numero_processo    = $document->createElement("NumeroProcesso", trim($curso->autorizacao_numero_processo));
                $curso_autorizacao_tipo_processo      = $document->createElement("TipoProcesso", trim($curso->autorizacao_tipo_processo));
                $curso_autorizacao_data_cadastro      = $document->createElement("DataCadastro", trim($curso->autorizacao_data_cadastro));
                $curso_autorizacao_data_protocolo     = $document->createElement("DataProtocolo", trim($curso->autorizacao_data_protocolo));
                
                $curso_reconhecimento_tipo               = $document->createElement("Tipo", trim($curso->reconhecimento_tipo));
                $curso_reconhecimento_numero             = $document->createElement("Numero", trim($curso->reconhecimento_numero));
                $curso_reconhecimento_data               = $document->createElement("Data", trim($curso->reconhecimento_data));
                $curso_reconhecimento_veiculo_publicacao = $document->createElement("VeiculoPublicacao", trim($curso->reconhecimento_veiculo_publicacao));
                $curso_reconhecimento_data_publicacao    = $document->createElement("DataPublicacao", trim($curso->reconhecimento_data_publicacao));
                $curso_reconhecimento_secao_publicacao   = $document->createElement("SecaoPublicacao", trim($curso->reconhecimento_secao_publicacao));
                $curso_reconhecimento_pagina_publicacao  = $document->createElement("PaginaPublicacao", trim($curso->reconhecimento_pag_publicacao));
                $curso_reconhecimento_numero_dou         = $document->createElement("NumeroDOU", trim($curso->reconhecimento_numero_DOU));
                $curso_reconhecimento_numero_processo    = $document->createElement("NumeroProcesso", trim($curso->reconhecimento_numero_processo));
                $curso_reconhecimento_tipo_processo      = $document->createElement("TipoProcesso", trim($curso->reconhecimento_tipo_processo));
                $curso_reconhecimento_data_cadastro      = $document->createElement("DataCadastro", trim($curso->reconhecimento_data_cadastro));
                $curso_reconhecimento_data_protocolo     = $document->createElement("DataProtocolo", trim($curso->reconhecimento_data_protocolo));

                $curso_renovacao_tipo              = $document->createElement("Tipo", trim($curso->renovacao_reconhecimento_tipo));
                $curso_renovacao_numero            = $document->createElement("Numero", trim($curso->renovacao_reconhecimento_numero));
                $curso_renovacao_data              = $document->createElement("Data", trim($curso->renovacao_reconhecimento_data));
                $curso_renovacao_veic_publicacao   = $document->createElement("VeiculoPublicacao", trim($curso->renovacao_reconhecimento_veic_publ));
                $curso_renovacao_data_publicacao   = $document->createElement("DataPublicacao", trim($curso->renovacao_reconhecimento_data_publ));
                $curso_renovacao_secao_publicacao  = $document->createElement("SecaoPublicacao", trim($curso->renovacao_reconhecimento_secao_publ));
                $curso_renovacao_pagina_publicacao = $document->createElement("PaginaPublicacao", trim($curso->renovacao_reconhecimento_pag_publ));
                $curso_renovacao_numero_dou        = $document->createElement("NumeroDOU", trim($curso->renovacao_reconhecimento_numero_DOU));
                $curso_renovacao_numero_processo   = $document->createElement("NumeroProcesso", trim($curso->renovacao_reconhecimento_numero_processo));
                $curso_renovacao_tipo_processo     = $document->createElement("TipoProcesso", trim($curso->renovacao_reconhecimento_tipo_processo));
                $curso_renovacao_data_cadastro     = $document->createElement("DataCadastro", trim($curso->renovacao_reconhecimento_data_cadastro));
                $curso_renovacao_data_protocolo    = $document->createElement("DataProtocolo", trim($curso->renovacao_reconhecimento_data_protocolo));

                   
                $noCurso->appendChild($curso_nome);                
                
                //Se possui ou não código EMEC
                if($curso->opcao_codigo_emec == "Possui código EMEC")
                {
                    $noCurso->appendChild($curso_codigo_emec);
                }
                else
                {
                    $noCursoSemCodigoEMEC = $document->createElement('SemCodigoCursoEMEC');
                    $noCurso->appendChild($noCursoSemCodigoEMEC);
                    
                    $noCursoSemCodigoEMEC->appendChild($curso_sem_codigo_emec_numero_processo);
                    $noCursoSemCodigoEMEC->appendChild($curso_sem_codigo_emec_tipo_processo);
                    $noCursoSemCodigoEMEC->appendChild($curso_sem_codigo_emec_data_cadastro);
                    $noCursoSemCodigoEMEC->appendChild($curso_sem_codigo_emec_data_protocolo);    
                }
                
                //Autorização   
                $noAutorizacaoCurso = $document->createElement('Autorizacao');
                $noCurso->appendChild($noAutorizacaoCurso);
                
                if($curso->opcao_autorizacao_emec == "Utilizar informações sobre ato regulatório")
                {    
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_tipo);
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_numero);
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_data);
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_veiculo_publicacao);
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_data_publicacao);
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_secao_publicacao);
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_pagina_publicacao);
                    $noAutorizacaoCurso->appendChild($curso_autorizacao_numero_dou);
                }
                elseif($curso->opcao_autorizacao_emec == "Utilizar informações sobre tramitação do processo")
                {
                    $noTramitacaoAutorizacaoCurso = $document->createElement('InformacoesTramitacaoEMEC');
                    $noAutorizacaoCurso->appendChild($noTramitacaoAutorizacaoCurso);
                
                    $noTramitacaoAutorizacaoCurso->appendChild($curso_autorizacao_numero_processo);
                    $noTramitacaoAutorizacaoCurso->appendChild($curso_autorizacao_tipo_processo);
                    $noTramitacaoAutorizacaoCurso->appendChild($curso_autorizacao_data_cadastro);
                    $noTramitacaoAutorizacaoCurso->appendChild($curso_autorizacao_data_protocolo);
                }
                else
                {
                    $action2 = new TAction(array('CurriculoList', 'onReload'));                       
                    new TMessage('error', 'Verifique se os dados de autorização do curso foram lançados corretamente em seu cadastro', $action2);    
                    die;
                }
                
                //Reconhecimento   
                $noReconhecimentoCurso = $document->createElement('Reconhecimento');
                $noCurso->appendChild($noReconhecimentoCurso);
    
                if($curso->opcao_reconhecimento_emec == "Utilizar informações sobre ato regulatório")
                {
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_tipo);
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_numero);
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_data);
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_veiculo_publicacao);
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_data_publicacao);
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_secao_publicacao);
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_pagina_publicacao);
                    $noReconhecimentoCurso->appendChild($curso_reconhecimento_numero_dou);
                }
                elseif($curso->opcao_reconhecimento_emec == "Utilizar informações sobre tramitação do processo")
                {
                    $noTramitacaoReconhecimentoCurso = $document->createElement('InformacoesTramitacaoEMEC');
                    $noReconhecimentoCurso->appendChild($noTramitacaoReconhecimentoCurso);
                    
                    $noTramitacaoReconhecimentoCurso->appendChild($curso_reconhecimento_numero_processo);
                    $noTramitacaoReconhecimentoCurso->appendChild($curso_reconhecimento_tipo_processo);
                    $noTramitacaoReconhecimentoCurso->appendChild($curso_reconhecimento_data_cadastro);
                    $noTramitacaoReconhecimentoCurso->appendChild($curso_reconhecimento_data_protocolo);
                }
                else
                {
                    $action3 = new TAction(array('CurriculoList', 'onReload'));                     
                    new TMessage('error', 'Verifique se os dados de reconhecimento do curso foram lançados corretamente em seu cadastro', $action3);    
                    die;
                }
                
                //Renovação de reconhecimento pode não ter ocorrência, portanto não é obrigatório
                if($curso->opcao_renovacao_emec <> NULL)
                {   
                    $noRenovacaoCurso = $document->createElement('RenovacaoReconhecimento');
                    $noCurso->appendChild($noRenovacaoCurso);
        
                    if($curso->opcao_renovacao_emec == "Utilizar informações sobre ato regulatório")
                    {
                        $noRenovacaoCurso->appendChild($curso_renovacao_tipo);
                        $noRenovacaoCurso->appendChild($curso_renovacao_numero);
                        $noRenovacaoCurso->appendChild($curso_renovacao_data);
                        $noRenovacaoCurso->appendChild($curso_renovacao_veic_publicacao); 
                        $noRenovacaoCurso->appendChild($curso_renovacao_data_publicacao); 
                        $noRenovacaoCurso->appendChild($curso_renovacao_secao_publicacao); 
                        $noRenovacaoCurso->appendChild($curso_renovacao_pagina_publicacao); 
                        $noRenovacaoCurso->appendChild($curso_renovacao_numero_dou); 
                    }
                    elseif($curso->opcao_renovacao_emec == "Utilizar informações sobre tramitação do processo")
                    {
                        $noTramitacaoRenovacaoCurso = $document->createElement('InformacoesTramitacaoEMEC');
                        $noRenovacaoCurso->appendChild($noTramitacaoRenovacaoCurso);
                        
                        $noTramitacaoRenovacaoCurso->appendChild($curso_renovacao_numero_processo);
                        $noTramitacaoRenovacaoCurso->appendChild($curso_renovacao_tipo_processo);
                        $noTramitacaoRenovacaoCurso->appendChild($curso_renovacao_data_cadastro);
                        $noTramitacaoRenovacaoCurso->appendChild($curso_renovacao_data_protocolo);
                    } 
                    else
                    {
                        $action4 = new TAction(array('CurriculoList', 'onReload'));                      
                        new TMessage('error', 'Verifique se os dados de renovação de reconhecimento do curso foram lançados corretamente em seu cadastro', $action4);    
                        die;
                    }
                }


                //INÍCIO - EMISSORA                
                $noEmissora = $document->createElement('IesEmissora');
                $noInfCurriculoEscolar->appendChild($noEmissora);
   
                $emissora_nome             = $document->createElement("Nome", trim($emissora->nome));
                $emissora_codigo_mec       = $document->createElement("CodigoMEC", trim($emissora->codigo_mec));
                $emissora_cnpj             = $document->createElement("CNPJ", trim($emissora->cnpj));
                $emissora_logradouro       = $document->createElement("Logradouro", trim($emissora->logradouro));
                $emissora_numero           = $document->createElement("Numero", trim($emissora->numero));
                $emissora_complemento      = $document->createElement("Complemento", trim($emissora->complemento));
                $emissora_bairro           = $document->createElement("Bairro", trim($emissora->bairro));
                $emissora_codigo_municipio = $document->createElement("CodigoMunicipio", trim($emissora->codigo_municipio));
                $emissora_nome_municipio   = $document->createElement("NomeMunicipio", trim($emissora->nome_municipio));
                $emissora_uf               = $document->createElement("UF", trim($emissora->uf));
                $emissora_cep              = $document->createElement("CEP", trim($emissora->cep));
                
                $emissora_credenciamento_tipo            = $document->createElement("Tipo", trim($emissora->credenciamento_tipo));
                $emissora_credenciamento_numero          = $document->createElement("Numero", trim($emissora->credenciamento_numero));
                $emissora_credenciamento_data            = $document->createElement("Data", trim($emissora->credenciamento_data));
                $emissora_credenciamento_veic_publ       = $document->createElement("VeiculoPublicacao", trim($emissora->credenciamento_veiculo_publicacao));
                $emissora_credenciamento_data_publ       = $document->createElement("DataPublicacao", trim($emissora->credenciamento_data_publicacao));
                $emissora_credenciamento_secao_publ      = $document->createElement("SecaoPublicacao", trim($emissora->credenciamento_secao_publicacao));
                $emissora_credenciamento_pagina_publ     = $document->createElement("PaginaPublicacao", trim($emissora->credenciamento_pag_publicacao));
                $emissora_credenciamento_numero_DOU      = $document->createElement("NumeroDOU", trim($emissora->credenciamento_numero_DOU));
                $emissora_credenciamento_numero_processo = $document->createElement("NumeroProcesso", trim($emissora->credenciamento_numero_processo));
                $emissora_credenciamento_tipo_processo   = $document->createElement("TipoProcesso", trim($emissora->credenciamento_tipo_processo));
                $emissora_credenciamento_data_cadastro   = $document->createElement("DataCadastro", trim($emissora->credenciamento_data_cadastro));
                $emissora_credenciamento_data_protocolo  = $document->createElement("DataProtocolo", trim($emissora->credenciamento_data_protocolo));
                
                $emissora_recredenciamento_tipo            = $document->createElement("Tipo", trim($emissora->recredenciamento_tipo));
                $emissora_recredenciamento_numero          = $document->createElement("Numero", trim($emissora->recredenciamento_numero));
                $emissora_recredenciamento_data            = $document->createElement("Data", trim($emissora->recredenciamento_data));
                $emissora_recredenciamento_veic_publ       = $document->createElement("VeiculoPublicacao", trim($emissora->recredenciamento_veiculo_publicacao));
                $emissora_recredenciamento_data_publ       = $document->createElement("DataPublicacao", trim($emissora->recredenciamento_data_publicacao));
                $emissora_recredenciamento_secao_publ      = $document->createElement("SecaoPublicacao", trim($emissora->recredenciamento_secao_publicacao));
                $emissora_recredenciamento_pag_publ        = $document->createElement("PaginaPublicacao", trim($emissora->recredenciamento_pag_publicacao));
                $emissora_recredenciamento_numero_DOU      = $document->createElement("NumeroDOU", trim($emissora->recredenciamento_numero_DOU));
                $emissora_recredenciamento_numero_processo = $document->createElement("NumeroProcesso", trim($emissora->recredenciamento_numero_processo));
                $emissora_recredenciamento_tipo_processo   = $document->createElement("TipoProcesso", trim($emissora->recredenciamento_tipo_processo));
                $emissora_recredenciamento_data_cadastro   = $document->createElement("DataCadastro", trim($emissora->recredenciamento_data_cadastro));
                $emissora_recredenciamento_data_protocolo  = $document->createElement("DataProtocolo", trim($emissora->recredenciamento_data_protocolo));

                $emissora_renovacao_tipo            = $document->createElement("Tipo", trim($emissora->renovacao_recredenciamento_tipo));
                $emissora_renovacao_numero          = $document->createElement("Numero", trim($emissora->renovacao_recredenciamento_numero));
                $emissora_renovacao_data            = $document->createElement("Data", trim($emissora->renovacao_recredenciamento_data));
                $emissora_renovacao_veic_publ       = $document->createElement("VeiculoPublicacao", trim($emissora->renovacao_recredenciamento_veic_publ));
                $emissora_renovacao_data_publ       = $document->createElement("DataPublicacao", trim($emissora->renovacao_recredenciamento_data_publ));
                $emissora_renovacao_secao_publ      = $document->createElement("SecaoPublicacao", trim($emissora->renovacao_recredenciamento_secao_publ));
                $emissora_renovacao_pag_publ        = $document->createElement("PaginaPublicacao", trim($emissora->renovacao_recredenciamento_pag_publ));
                $emissora_renovacao_numero_DOU      = $document->createElement("NumeroDOU", trim($emissora->renovacao_recredenciamento_numero_DOU));
                $emissora_renovacao_numero_processo = $document->createElement("NumeroProcesso", trim($emissora->renovacao_recredenciamento_numero_processo));
                $emissora_renovacao_tipo_processo   = $document->createElement("TipoProcesso", trim($emissora->renovacao_recredenciamento_tipo_processo));
                $emissora_renovacao_data_cadastro   = $document->createElement("DataCadastro", trim($emissora->renovacao_recredenciamento_data_cadastro));
                $emissora_renovacao_data_protocolo  = $document->createElement("DataProtocolo", trim($emissora->renovacao_recredenciamento_data_protocolo));

                        
                $noEmissora->appendChild($emissora_nome);
                $noEmissora->appendChild($emissora_codigo_mec);
                $noEmissora->appendChild($emissora_cnpj);
                    
                $noEnderecoEmissora = $document->createElement('Endereco');
                $noEmissora->appendChild($noEnderecoEmissora);
                                    
                $noEnderecoEmissora->appendChild($emissora_logradouro);
                $noEnderecoEmissora->appendChild($emissora_numero);
                
                if($emissora->complemento <> NULL)
                {
                    $noEnderecoEmissora->appendChild($emissora_complemento);    
                }
                
                $noEnderecoEmissora->appendChild($emissora_bairro);
                $noEnderecoEmissora->appendChild($emissora_codigo_municipio);
                $noEnderecoEmissora->appendChild($emissora_nome_municipio);
                $noEnderecoEmissora->appendChild($emissora_uf);
                $noEnderecoEmissora->appendChild($emissora_cep);
                                  
                //Credenciamento   
                $noCredenciamentoEmissora = $document->createElement('Credenciamento');
                $noEmissora->appendChild($noCredenciamentoEmissora);
                
                if($emissora->opcao_credenciamento_emec == "Utilizar informações sobre ato regulatório")
                {   
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_tipo);
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_numero);
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_data);    
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_veic_publ);                    
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_data_publ);
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_secao_publ);
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_pagina_publ);
                    $noCredenciamentoEmissora->appendChild($emissora_credenciamento_numero_DOU);
                }  
                elseif($emissora->opcao_credenciamento_emec == "Utilizar informações sobre tramitação do processo")
                {
                    $noTramitacaoCredenciamentoEmissora = $document->createElement('InformacoesTramitacaoEMEC');
                    $noCredenciamentoEmissora->appendChild($noTramitacaoCredenciamentoEmissora);
                        
                    $noTramitacaoCredenciamentoEmissora->appendChild($emissora_credenciamento_numero_processo);
                    $noTramitacaoCredenciamentoEmissora->appendChild($emissora_credenciamento_tipo_processo);
                    $noTramitacaoCredenciamentoEmissora->appendChild($emissora_credenciamento_data_cadastro);
                    $noTramitacaoCredenciamentoEmissora->appendChild($emissora_credenciamento_data_protocolo);
                }
                else
                { 
                    $action5 = new TAction(array('CurriculoList', 'onReload'));                     
                    new TMessage('error', 'Verifique se os dados de credenciamento da emissora foram lançados corretamente em seu cadastro', $action5);    
                    die;
                }  
                                    
                //Recredenciamento pode não ter ocorrência, portanto não é obrigatório 
                if($emissora->opcao_recredenciamento_emec <> NULL)
                {   
                    $noRecredenciamentoEmissora = $document->createElement('Recredenciamento');
                    $noEmissora->appendChild($noRecredenciamentoEmissora);
        
                    if($emissora->opcao_recredenciamento_emec == "Utilizar informações sobre ato regulatório")
                    {
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_tipo);
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_numero);
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_data);
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_veic_publ);
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_data_publ);
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_secao_publ);
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_pag_publ);
                        $noRecredenciamentoEmissora->appendChild($emissora_recredenciamento_numero_DOU);
                    }
                    elseif($emissora->opcao_recredenciamento_emec == "Utilizar informações sobre tramitação do processo")
                    {
                        $noTramitacaoRecredenciamentoEmissora = $document->createElement('InformacoesTramitacaoEMEC');
                        $noRecredenciamentoEmissora->appendChild($noTramitacaoRecredenciamentoEmissora);
                        
                        $noTramitacaoRecredenciamentoEmissora->appendChild($emissora_recredenciamento_numero_processo);
                        $noTramitacaoRecredenciamentoEmissora->appendChild($emissora_recredenciamento_tipo_processo);
                        $noTramitacaoRecredenciamentoEmissora->appendChild($emissora_recredenciamento_data_cadastro);
                        $noTramitacaoRecredenciamentoEmissora->appendChild($emissora_recredenciamento_data_protocolo);
                    } 
                    else
                    { 
                        $action6 = new TAction(array('CurriculoList', 'onReload'));                       
                        new TMessage('error', 'Verifique se os dados de recredenciamento da emissora foram lançados corretamente em seu cadastro', $action6);    
                        die;
                    } 
                }
                
                //Renovação de recredenciamento pode não ter ocorrência, portanto não é obrigatório
                if($emissora->opcao_renovacao_emec <> NULL)
                {   
                    $noRenovacaoEmissora = $document->createElement('RenovacaoDeRecredenciamento');
                    $noEmissora->appendChild($noRenovacaoEmissora);
        
                    if($emissora->opcao_renovacao_emec == "Utilizar informações sobre ato regulatório")
                    {
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_tipo);
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_numero);
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_data);
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_veic_publ);
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_data_publ);
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_secao_publ);
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_pag_publ);
                        $noRenovacaoEmissora->appendChild($emissora_renovacao_numero_DOU); 
                    }
                    elseif($emissora->opcao_renovacao_emec == "Utilizar informações sobre tramitação do processo")
                    {
                        $noTramitacaoRenovacaoEmissora = $document->createElement('InformacoesTramitacaoEMEC');
                        $noRenovacaoEmissora->appendChild($noTramitacaoRenovacaoEmissora);
                        
                        $noTramitacaoRenovacaoEmissora->appendChild($emissora_renovacao_numero_processo);
                        $noTramitacaoRenovacaoEmissora->appendChild($emissora_renovacao_tipo_processo);
                        $noTramitacaoRenovacaoEmissora->appendChild($emissora_renovacao_data_cadastro);
                        $noTramitacaoRenovacaoEmissora->appendChild($emissora_renovacao_data_protocolo);
                    } 
                    else
                    { 
                        $action7 = new TAction(array('CurriculoList', 'onReload'));                         
                        new TMessage('error', 'Verifique se os dados de renovação de recredenciamento da emissora foram lançados corretamente em seu cadastro', $action7);    
                        die;
                    } 
                }            


                //INÍCIO - MANTENEDORA
                $noMantenedora = $document->createElement('Mantenedora');
                $noEmissora->appendChild($noMantenedora);
   
                $mantenedora_razao_social     = $document->createElement("RazaoSocial", trim($mantenedora->razao_social));
                $mantenedora_cnpj             = $document->createElement("CNPJ", trim($mantenedora->cnpj));
                $mantenedora_logradouro       = $document->createElement("Logradouro", trim($mantenedora->logradouro));
                $mantenedora_numero           = $document->createElement("Numero", trim($mantenedora->numero));
                $mantenedora_complemento      = $document->createElement("Complemento", trim($mantenedora->complemento));
                $mantenedora_bairro           = $document->createElement("Bairro", trim($mantenedora->bairro));
                $mantenedora_codigo_municipio = $document->createElement("CodigoMunicipio", trim($mantenedora->codigo_municipio));
                $mantenedora_nome_municipio   = $document->createElement("NomeMunicipio", trim($mantenedora->nome_municipio));
                $mantenedora_uf               = $document->createElement("UF", trim($mantenedora->uf));
                $mantenedora_cep              = $document->createElement("CEP", trim($mantenedora->cep));
    
                $noMantenedora->appendChild($mantenedora_razao_social);
                $noMantenedora->appendChild($mantenedora_cnpj);
                   
                $noEnderecoMantenedora = $document->createElement('Endereco');
                $noMantenedora->appendChild($noEnderecoMantenedora);
                                    
                $noEnderecoMantenedora->appendChild($mantenedora_logradouro);
                $noEnderecoMantenedora->appendChild($mantenedora_numero);
                
                if($mantenedora->complemento <> NULL)
                {
                    $noEnderecoMantenedora->appendChild($mantenedora_complemento);    
                }
                
                $noEnderecoMantenedora->appendChild($mantenedora_bairro);
                $noEnderecoMantenedora->appendChild($mantenedora_codigo_municipio);
                $noEnderecoMantenedora->appendChild($mantenedora_nome_municipio);
                $noEnderecoMantenedora->appendChild($mantenedora_uf);
                $noEnderecoMantenedora->appendChild($mantenedora_cep);                                



                //Unidades
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('curriculo_id', '=', $curriculo_digital->id));  
                $criteria1->setProperty('order', 'opcao_disciplina, etapa, nome', 'desc');
                        
                $unidades_curriculares = CurriculoDisciplina::getObjects($criteria1); 
    
    
                //Categorias
                $criteria2 = new TCriteria;
                $criteria2->add(new TFilter('curriculo_id', '=', $curriculo_digital->id));  
                $criteria2->setProperty('order', 'atividade_complementar_categoria_id', 'asc');
                
                $curriculo_categorias = CurriculoAtividadeCategoria::getObjects($criteria2); 
                
                
                //Atividades
                if($curriculo_categorias)
                {
                    foreach($curriculo_categorias as $curriculo_categoria)
                    {
                        $ids_curriculo_categorias[] = $curriculo_categoria->id;    
                    }
                        
                    $curriculo_atividades = CurriculoAtividadeCadastro::where('curriculo_atividade_categoria_id', 'IN', $ids_curriculo_categorias)->load();
                }
                    
                
                //Critérios
                $criteria3 = new TCriteria;
                $criteria3->add(new TFilter('curriculo_id', '=', $curriculo_digital->id)); 
                //$criteria3->add(new TFilter('participacao_total', '=', 'Sim'));  
                $criteria3->setProperty('order', 'id', 'asc');
                
                $criterios_integralizacao = CurriculoCriterioIntegralizacao::getObjects($criteria3);        


                //INÍCIO - ETIQUETAS 
                $noInfEtiquetas = $document->createElement('infEtiquetas');                   
                $noInfCurriculoEscolar->appendChild($noInfEtiquetas);                


                //1º Pega o ID de todas as disciplinas/unidades do currículo                
                foreach($unidades_curriculares as $unidade_curricular)
                {
                    $ids_unidades[] = $unidade_curricular->id;
                }


                //2º Percorre a tabela curriculo_disciplina_etiqueta onde estão salvas as etiquetas utilizadas em cada unidade/disciplina
                $curriculo_disciplinas_etiquetas = CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', 'IN', $ids_unidades)
                                                                              ->orderBy('id', 'desc')
                                                                              ->load();

 
                //3º Unifica etiquetas repetidas para aparecerem uma única vez
                foreach($curriculo_disciplinas_etiquetas as $curriculo_disciplina_etiqueta)
                {
                    $etiqueta = new Etiqueta($curriculo_disciplina_etiqueta->dados_etiqueta_id);
                                                
                    $etiquetas_disciplinas[$etiqueta->id]['codigo'] = $etiqueta->codigo;
                    $etiquetas_disciplinas[$etiqueta->id]['nome'] = $etiqueta->nome;
                    $etiquetas_disciplinas[$etiqueta->id]['aplicada_automaticamente'] = $etiqueta->aplicada_automaticamente;
                } 
                
                
                //Antes de incluir obrigatoriamente a etiqueta de extensão, recebe as etiquetas originais do currículo
                $etiquetas_originais_curriculo = [];
                $etiquetas_originais_curriculo = $etiquetas_disciplinas;
              
                
                //4º Se não tiver etiqueta de Extensão (ext), inclui, pois é obrigatório 
                $etiquetas_originais = serialize($etiquetas_originais_curriculo);
   
                if(strpos($etiquetas_originais,'"ext"') === false)
                {
                    $etq_extensao = Etiqueta::where('codigo', '=', 'ext')->load();

                    $etiquetas_disciplinas[$etq_extensao[0]->id]['codigo'] = $etq_extensao[0]->codigo;
                    $etiquetas_disciplinas[$etq_extensao[0]->id]['nome'] = $etq_extensao[0]->nome;
                    $etiquetas_disciplinas[$etq_extensao[0]->id]['aplicada_automaticamente'] = $etq_extensao[0]->aplicada_automaticamente;
                }
                

                if($etiquetas_disciplinas)
                {
                    foreach($etiquetas_disciplinas as $etiqueta_disciplina)
                    {
                        $etiqueta = (object) $etiqueta_disciplina;

                        if($etiqueta)
                        {
                            $noEtiqueta = $document->createElement('Etiqueta');                   
                            $noInfEtiquetas->appendChild($noEtiqueta);
                            
                            $noEtiquetaCodigo = $document->createElement("Codigo", trim($etiqueta->codigo));                   
                            $noEtiqueta->appendChild($noEtiquetaCodigo);
                            
                            $noEtiquetaNome = $document->createElement("Nome", trim($etiqueta->nome));                   
                            $noEtiqueta->appendChild($noEtiquetaNome);
    
                            $noAplicadaAutomaticamente = $document->createElement("AplicadoAutomaticamenteUnidadesNaoPertencentesAoCurriculo", trim($etiqueta->aplicada_automaticamente));                   
                            $noEtiqueta->appendChild($noAplicadaAutomaticamente);    
                        }    
                    }
                }                
                else
                {
                    $action8 = new TAction(array('CurriculoList', 'onReload'));                      
                    new TMessage('error', 'Verifique se etiquetas foram atribuídas às unidades presentes no currículo', $action8);    
                    die;    
                }
                
                
                //INÍCIO - ÁREAS
                $noInfAreas = $document->createElement('infAreas');                   
                $noInfCurriculoEscolar->appendChild($noInfAreas);                


                //1º Pega o ID de todas as disciplinas/unidades do currículo                
                foreach($unidades_curriculares as $unidade_curricular)
                {
                    $ids_unidades[] = $unidade_curricular->id;
                }


                //2º Percorre a tabela curriculo_disciplina_area onde estão salvas as áreas utilizadas
                $curriculo_disciplinas_areas = CurriculoDisciplinaArea::where('curriculo_disciplina_id', 'IN', $ids_unidades)
                                                                      ->orderBy('id', 'asc')
                                                                      ->load();

                
                //3º Unifica áreas repetidas para aparecerem uma única vez
                foreach($curriculo_disciplinas_areas as $curriculo_disciplina_area)
                {
                    $area = new AreaFormacao($curriculo_disciplina_area->dados_area_formacao_id);
                        
                    $areas_disciplinas[$area->id]['codigo'] = $area->codigo;
                    $areas_disciplinas[$area->id]['nome'] = $area->nome;
                } 
               

                //Se o curso tiver áreas de formação, traz os dados. Se não, a tag obrigatória "infAreas" fica vazia
                if($areas_disciplinas)
                {
                    foreach($areas_disciplinas as $area_disciplina)
                    {
                        $area = (object) $area_disciplina;
                        
                        if($area)
                        {
                            $noArea = $document->createElement('Area');                   
                            $noInfAreas->appendChild($noArea);
                            
                            $noAreaCodigo = $document->createElement("Codigo", trim($area->codigo));                   
                            $noArea->appendChild($noAreaCodigo);
                            
                            $noAreaNome = $document->createElement("Nome", trim($area->nome));                   
                            $noArea->appendChild($noAreaNome);
                        }    
                    }
                }
                
                
                //INÍCIO - ESTRUTURA CURRICULAR
                $noInfEstruturaCurricular = $document->createElement('infEstruturaCurricular');                   
                $noInfCurriculoEscolar->appendChild($noInfEstruturaCurricular);       

                if($unidades_curriculares)
                {
                    foreach($unidades_curriculares as $unidade_curricular)
                    {
                        $unidade = (object) $unidade_curricular;
                        
                        if($unidade)
                        {
                            $noUnidadeCurricular = $document->createElement('UnidadeCurricular');                   
                            $noInfEstruturaCurricular->appendChild($noUnidadeCurricular);
                            
                            //Tipo
                            $noTipoUnidade = $document->createElement("Tipo", trim($unidade->tipo));                   
                            $noUnidadeCurricular->appendChild($noTipoUnidade);
                            
                            //Código
                            $noCodigoUnidade = $document->createElement("Codigo", trim($unidade->cod_disciplina_curriculo));                   
                            $noUnidadeCurricular->appendChild($noCodigoUnidade);
                            
                            //Nome
                            $noNomeUnidade = $document->createElement("Nome", trim($unidade->nome));                   
                            $noUnidadeCurricular->appendChild($noNomeUnidade);    
                            
                            //Carga Horária (hora/aula)
                            //$noChAulaUnidade = $document->createElement("CargaHorariaEmHoraAula", trim($unidade->ch_hora_aula));                   
                            //$noUnidadeCurricular->appendChild($noChAulaUnidade);
                            
                            //Carga Horária (hora/relógio)
                            $noChRelogioUnidade = $document->createElement("CargaHorariaEmHoraRelogio", trim($unidade->ch_hora_relogio));                   
                            $noUnidadeCurricular->appendChild($noChRelogioUnidade);
                            
                            //Ementa (sem quebra de linhas)
                            $noEmentaUnidade = $document->createElement("Ementa");                   
                            $noUnidadeCurricular->appendChild($noEmentaUnidade);
                        
                            $noItemEmentaUnidade = $document->createElement("ItemEmenta", trim(preg_replace("/\r|\n|&nbsp/", "", $unidade->ementa)));                   
                            $noEmentaUnidade->appendChild($noItemEmentaUnidade);
                            
                            //Fase (as disciplinas lançadas como opções de optativa não estão vinculadas a nenhuma fase/etapa)
                            if($unidade->etapa <> NULL)
                            {
                                $noFaseUnidade = $document->createElement("Fase", trim($unidade->etapa));                   
                                $noUnidadeCurricular->appendChild($noFaseUnidade);
                            }
                                                    
                            //Pré-Requisitos 
                            $curriculo_disciplinas_requisitadas = CurriculoDisciplinaRequisitada::where('curriculo_disciplina_dependente_id', '=', $unidade->id)
                                                                                                ->orderBy('id', 'desc')
                                                                                                ->load();
            
                            //Se tiver pré-requisitos, adiciona tag ao XML
                            if($curriculo_disciplinas_requisitadas)
                            {
                                $noPreRequisitosUnidade = $document->createElement('PreRequisitos');                   
                                $noUnidadeCurricular->appendChild($noPreRequisitosUnidade);                                
                                
                                foreach($curriculo_disciplinas_requisitadas as $curriculo_disciplina_requisitada)
                                {
                                    $disciplina_requisitada = new CurriculoDisciplina($curriculo_disciplina_requisitada->curriculo_disciplina_requisitada_id);
                                    
                                    $noCodigoDependencia = $document->createElement("CodigoDependencia", trim($disciplina_requisitada->cod_disciplina_curriculo));                   
                                    $noPreRequisitosUnidade->appendChild($noCodigoDependencia);
                                }   
                            }
                                                
                            /*Etiquetas - as unidades só serão etiquetadas em currículos cujo ano inicial seja igual ou superior a 2023 (quando a discriminação da carga
                            de extensão do curso se tornou obrigatória) - CANCELADO (serão etiquetadas de qualquer forma)*/
                            //TTransaction::open('dados_fei');
                            
                            //$grade = new FiGradeCurso($curriculo_digital->cod_grade);
                            
                            //TTransaction::close();
                            
                            
                            //if($grade->AnoInicial >= 2023)
                            //{   
                                //$etiquetas_originais =  serialize($etiquetas_originais_curriculo);
                                
                                //Se foram aplicadas as etiquetas de extensão                                                                          
                                //if(strpos($etiquetas_originais,'"ext"') !== false)
                                //{                                      
                                    $noEtiquetasUnidade = $document->createElement('Etiquetas');                   
                                    $noUnidadeCurricular->appendChild($noEtiquetasUnidade);                                
                                    
                                    foreach($curriculo_disciplinas_etiquetas as $curriculo_disciplina_etiqueta)
                                    {
                                        if($curriculo_disciplina_etiqueta->curriculo_disciplina_id == $unidade->id)
                                        {
                                            $etiqueta = new Etiqueta($curriculo_disciplina_etiqueta->dados_etiqueta_id);
                                            
                                            $noEtiquetaUnidade = $document->createElement('Etiqueta');                   
                                            $noEtiquetasUnidade->appendChild($noEtiquetaUnidade); 
                                        
                                            $noCodigoEtiquetaUnidade = $document->createElement("Codigo", trim($etiqueta->codigo));                   
                                            $noEtiquetaUnidade->appendChild($noCodigoEtiquetaUnidade);
                                            
                                            //Etiqueta de extensão é a única que recebe carga horária
                                            if($etiqueta->codigo == "ext")
                                            {
                                                //Carga Horária (hora/aula)
                                                /*if($curriculo_disciplina_etiqueta->ch_hora_aula <> NULL)
                                                {
                                                    $noChAulaEtiquetaExtensaoUnidade = $document->createElement("CargaHorariaEmHoraAula", trim($curriculo_disciplina_etiqueta->ch_hora_aula));                   
                                                    $noEtiquetaUnidade->appendChild($noChAulaEtiquetaExtensaoUnidade);    
                                                }
                                                else
                                                {
                                                    $action9 = new TAction(array('CurriculoList', 'onReload'));                       
                                                    new TMessage('error', 'Verifique se foi atribuída carga horária às etiquetas de extensão presentes no currículo', $action9);    
                                                    die;
                                                }*/
                                                
                                                //Carga Horária (hora/relógio)
                                                if($curriculo_disciplina_etiqueta->ch_hora_relogio <> NULL)
                                                {
                                                    $noChRelogioEtiquetaExtensaoUnidade = $document->createElement("CargaHorariaEmHoraRelogio", trim($curriculo_disciplina_etiqueta->ch_hora_relogio));                   
                                                    $noEtiquetaUnidade->appendChild($noChRelogioEtiquetaExtensaoUnidade);    
                                                }
                                                else
                                                {
                                                    $action10 = new TAction(array('CurriculoList', 'onReload'));                       
                                                    new TMessage('error', 'Verifique se foi atribuída carga horária às etiquetas de extensão presentes no currículo', $action10);    
                                                    die;
                                                }
                                            }
                                        }    
                                    }         
                                //}
                                //else
                                //{
                                    //$action11 = new TAction(array('CurriculoList', 'onReload'));                       
                                    //new TMessage('error', 'É necessário etiquetar as unidades curriculares cuja carga horária, total ou parcial, seja utilizada para cômputo da carga de extensão do curso', $action11);    
                                    //die;  
                                //}    
                            //}
                                                                                     
                            //Áreas
                            $curriculo_disciplinas_areas = CurriculoDisciplinaArea::where('curriculo_disciplina_id', '=', $unidade->id)
                                                                                  ->orderBy('id', 'desc')
                                                                                  ->load();
            
                            //Se tiver áreas, adiciona tag ao XML
                            if($curriculo_disciplinas_areas)
                            {
                                $noAreasUnidade = $document->createElement('Areas');                   
                                $noUnidadeCurricular->appendChild($noAreasUnidade);                                
                                
                                foreach($curriculo_disciplinas_areas as $curriculo_disciplina_area)
                                {
                                    $area = new AreaFormacao($curriculo_disciplina_area->dados_area_formacao_id);
    
                                    $noAreaUnidade = $document->createElement('Area');                   
                                    $noAreasUnidade->appendChild($noAreaUnidade);
                                    
                                    $noCodigoAreaUnidade = $document->createElement("Codigo", trim($area->codigo));                   
                                    $noAreaUnidade->appendChild($noCodigoAreaUnidade); 
                                }   
                            } 
                        }                                                 
                    }
                }
                else
                {
                    $action12 = new TAction(array('CurriculoList', 'onReload'));                       
                    new TMessage('error', 'Verifique se as unidades curriculares foram lançadas no currículo', $action12);    
                    die;
                }
                
                
                //INÍCIO - ATIVIDADES COMPLEMENTARES               
                if($curriculo_categorias AND $curriculo_atividades)
                {
                    $noInfEstruturaAtividadesComplementares = $document->createElement('infEstruturaAtividadesComplementares');                   
                    $noInfCurriculoEscolar->appendChild($noInfEstruturaAtividadesComplementares);  
                    
                    foreach($curriculo_categorias as $curriculo_categoria)
                    {
                        $categoria = new AtividadeComplementarCategoria($curriculo_categoria->atividade_complementar_categoria_id);
                        
                        if($categoria)
                        {        
                            //Categoria      
                            $noCategoriaAtividade = $document->createElement('Categoria');                   
                            $noInfEstruturaAtividadesComplementares->appendChild($noCategoriaAtividade);
                            
                            //Código da categoria no currículo
                            $noCodigoCategoriaAtividade = $document->createElement("Codigo", trim($curriculo_categoria->cod_categoria_curriculo));                   
                            $noCategoriaAtividade->appendChild($noCodigoCategoriaAtividade);
                            
                            //Nome
                            $noNomeCategoriaAtividade = $document->createElement("Nome", trim($categoria->nome));                   
                            $noCategoriaAtividade->appendChild($noNomeCategoriaAtividade);  
                            
                            //Se foi definido um limite de carga horária para a categoria, adiciona tag
                            if($curriculo_categoria->ch_categoria_hora_relogio <> NULL)
                            {
                                $noLimiteChCategoriaAtividade = $document->createElement("LimiteCargaHorariaEmHoraRelogio", trim($curriculo_categoria->ch_categoria_hora_relogio));                   
                                $noCategoriaAtividade->appendChild($noLimiteChCategoriaAtividade);     
                            }
                        
                            //Atividades
                            $noAtividades = $document->createElement('Atividades');                   
                            $noCategoriaAtividade->appendChild($noAtividades);  
                            
                            foreach($curriculo_atividades as $curriculo_atividade)
                            {
                                if($curriculo_atividade->curriculo_atividade_categoria_id == $curriculo_categoria->id)
                                {
                                    $atividade = new AtividadeComplementarCadastro($curriculo_atividade->atividade_complementar_cadastro_id);
    
                                    if($atividade)
                                    {                                        
                                        //Atividade
                                        $noAtividade = $document->createElement("Atividade");                   
                                        $noAtividades->appendChild($noAtividade);
                                            
                                        //Código
                                        $noCodigoAtividade = $document->createElement("Codigo", trim($curriculo_atividade->cod_atividade_curriculo));                   
                                        $noAtividade->appendChild($noCodigoAtividade);
                                            
                                        //Nome
                                        $noNomeAtividade = $document->createElement("Nome", trim($atividade->nome));                   
                                        $noAtividade->appendChild($noNomeAtividade);
                                            
                                        //Limite de carga horária
                                        $noLimiteChAtividade = $document->createElement("LimiteCargaHorariaEmHoraRelogio", trim($curriculo_atividade->ch_atividade_hora_relogio));                   
                                        $noAtividade->appendChild($noLimiteChAtividade);           
                                    }
                                    else
                                    {
                                        $action13 = new TAction(array('CurriculoList', 'onReload'));                       
                                        new TMessage('error', 'Verifique a estruturação das atividades complementares lançadas no currículo', $action13);    
                                        die;
                                    }    
                                } 
                            }    
                        }  
                    }
                }    
                
                
                //INÍCIO - CRITÉRIOS DE INTEGRALIZAÇÃO
                $noInfCriteriosIntegralizacao = $document->createElement('infCriteriosIntegralizacao');                   
                $noInfCurriculoEscolar->appendChild($noInfCriteriosIntegralizacao); 
               
                foreach($criterios_integralizacao as $criterio_integralizacao)
                {
                    $criterio = (object) $criterio_integralizacao;
                        
                    $noCriterioIntegralizacaoRotulos = $document->createElement('CriterioIntegralizacaoRotulos');                   
                    $noInfCriteriosIntegralizacao->appendChild($noCriterioIntegralizacaoRotulos);
                       
                    //Código
                    $noCodigoCriterio = $document->createElement("Codigo", trim($criterio->codigo));                   
                    $noCriterioIntegralizacaoRotulos->appendChild($noCodigoCriterio);
                    
                    //Unidade
                    $noUnidadeCriterio = $document->createElement("UnidadeCurricular", trim($criterio->tipo_unidade));                   
                    $noCriterioIntegralizacaoRotulos->appendChild($noUnidadeCriterio);
                    
                    //Etiqueta
                    $etiquetas = explode(',', $criterio->etiquetas_nome);
                    
                    foreach($etiquetas as $etiqueta)
                    {
                        $noEtiquetasCriterio = $document->createElement("Etiqueta", trim($etiqueta));                   
                        $noCriterioIntegralizacaoRotulos->appendChild($noEtiquetasCriterio);    
                    }
                    
                    //Carga horária
                    $noChCriterio = $document->createElement("CargasHorariasCriterio");                   
                    $noCriterioIntegralizacaoRotulos->appendChild($noChCriterio);
                    
                    //Carga horária mínima
                    $noChMinimaCriterio = $document->createElement("CargaHorariaMinima", $criterio->ch_minima_hora_relogio);                   
                    $noChCriterio->appendChild($noChMinimaCriterio);
                    
                    //Carga horária máxima
                    $noChMaximaCriterio = $document->createElement("CargaHorariaMaxima", $criterio->ch_maxima_hora_relogio);                   
                    $noChCriterio->appendChild($noChMaximaCriterio);
                    
                    /*Carga horária para total (indica o quanto de ch do critério contribui para a ch do curso. Caso omitido, considera-se que este 
                    critério é utilizado exclusivamente para definir critérios de integralização e não contribui com a ch total do curso)*/
                    if($criterio->participacao_total == "Sim")
                    {
                        $noChParaTotalCriterio = $document->createElement("CargaHorariaParaTotal", $criterio->ch_maxima_hora_relogio);                   
                        $noChCriterio->appendChild($noChParaTotalCriterio);
                    }
                }
                
                
                //INÍCIO - SEGURANÇA CURRÍCULO
                $noSegurancaCurriculo = $document->createElement('SegurancaCurriculo');
                $noInfCurriculoEscolar->appendChild($noSegurancaCurriculo);

                if($codigo_validacao <> NULL)
                {
                    $noCodigoValidacao = $document->createElement('CodigoValidacao', trim($codigo_validacao));
                    $noSegurancaCurriculo->appendChild($noCodigoValidacao);
                }
                else
                {
                    $action14 = new TAction(array('CurriculoList', 'onReload'));                       
                    new TMessage('error', 'O código de validação do currículo não foi gerado corretamente. Por favor, repita o processo', $action14);    
                    die;    
                } 
                
                
                //INÍCIO - INFORMAÇÕES ADICIONAIS
                if($curriculo_digital->informacoes_adicionais <> NULL)
                {
                    $noInformacoesAdicionais = $document->createElement('InformacoesAdicionais', trim(strip_tags(preg_replace("/\r|\n|&nbsp/", " ", $curriculo_digital->informacoes_adicionais))));
                    $noInfCurriculoEscolar->appendChild($noInformacoesAdicionais);
                }


                //INÍCIO - CONFRONTA O XML COM O XSD
                libxml_use_internal_errors(true);
                              
                $document->loadXML($document->saveXML());
                //$document->schemaValidate("http://dev.feituverava.com.br/mec/CurriculoEscolarDigital_v" . $versao->versao_curriculo . ".xsd");
                
                //Alteração do caminho dos XSDs para a pasta do acadêmico por erro ao tentar acesso externo
                $document->schemaValidate("./public/mec/CurriculoEscolarDigital_v" . $versao->versao_curriculo . ".xsd");
                
                $errors = libxml_get_errors();
                              
                if($errors)
                {
                    foreach($errors as $error)
                    {
                        if(!preg_match("/(Signature)/", $error->message))
                        {
                            $message_original = str_replace('{http://portal.mec.gov.br/diplomadigital/arquivos-em-xsd}', '', $error->message);
                            $termos = array("Element" => "Elemento ", 
                                            "This element is not expected. Expected is" => "Este elemento não é esperado. O esperado é ", 
                                            "This element is not expected. Expected is one of" => "Este elemento não é esperado. O esperado é um dos ",
                                            "Missing child element(s). Expected is" => "Elemento(s) filho(s) ausente(s). Esperado é ",
                                            "Missing child element(s). Expected is one of" => "Elemento(s) filho(s) ausente(s). Esperado é um dos");
                            $message_translate = strtr($message_original, $termos);
                            
                            $pos_action = new TAction(array('CurriculoList', 'onReload'));
                            new TMessage('error', $message_translate, $pos_action); 
                            die;
                        }
                    }	
                }
                //FIM - CONFRONTA O XML COM O XSD
                
               
                $document->save($target_file); 
               
                
                $curriculo_digital->dados_versao_id = $versao->id;
                $curriculo_digital->status_xml = 1; //1 - Gerado
                $curriculo_digital->status_assinatura_coordenador = 0; //0 - Não preechida / 1 - Preenchida
                $curriculo_digital->data_exp_certificado_coordenador = '';
                $curriculo_digital->status_assinatura_emissora = 0; //0 - Não preechida / 1 - Preenchida
                $curriculo_digital->data_exp_certificado_emissora = '';
                $curriculo_digital->codigo_validacao = $codigo_validacao;
                $curriculo_digital->url_curriculo = '';
                $curriculo_digital->qrcode = '';
                $curriculo_digital->caminho_qrcode = '';
                $curriculo_digital->arquivo = 'curriculo-' . $curriculo_digital->codigo_curriculo . '.xml';
                $curriculo_digital->caminho_arquivo = $target_path;
                $curriculo_digital->arquivo_pdf = '';
                $curriculo_digital->caminho_pdf = '';
                $curriculo_digital->status_assinatura_pdf = 0;
                $curriculo_digital->status_publicacao = 0;
                $curriculo_digital->data_publicacao = '';
                $curriculo_digital->system_user_id = TSession::getValue('userid');
                $curriculo_digital->data_reg = date('Y-m-d H:i:s'); 
 
                $curriculo_digital->store();                               
                                                                               
                TTransaction::close();
                   
                new TMessage('info', 'XML gerado com sucesso');
                                   
                TApplication::loadPage('CurriculoList', 'onReload');
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $target_file);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());            
            TTransaction::rollback();
        }
    }
}


