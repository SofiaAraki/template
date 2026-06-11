<?php

class XMLHistoricoForm extends TPage
{

    function __construct($param)
    {
        parent::__construct();

    }
 
  
    public function onVerificarXMLHistorico($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
 
            $id_historico_digital = $param['id_historico_digital'];                
            $tipo_historico = $param['tipo_historico'];
            
            //Para redirecionar à listagem que chamou a ação em caso de exceção (histórico automático ou manual)
            $formulario_origem = $param['formulario_origem'];

            
            $historico_digital = new HistoricoDigital($id_historico_digital);
            

            //Se não existir diretório, cria
            $target_path = 'secretaria/historico_xmls/' . 'historico_' . $historico_digital->id;
                        
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
                if($historico_digital->status_xml == 1 OR $historico_digital->arquivo <> NULL)
                {
                    $action_gerar = new TAction([$this, 'onGerarCodigoValidacao']);
                    $action_gerar->setParameters(['id_historico_digital' => $id_historico_digital, 'tipo_historico' => $tipo_historico, 'caminho_diretorio' => $target_path, 'formulario_origem' => $formulario_origem]);                   
                    
                    $action_voltar = new TAction([$formulario_origem, 'onReload']);                  
                    
                    new TQuestion('Um arquivo XML referente a este histórico já foi gerado. Deseja realmente gerar um novo arquivo e substituir o existente?', $action_gerar, $action_voltar);
                }            
                else
                {
                    $param = ['id_historico_digital' => $id_historico_digital, 'tipo_historico' => $tipo_historico, 'caminho_diretorio' => $target_path, 'formulario_origem' => $formulario_origem];
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
            $id_historico_digital = $param['id_historico_digital'];
            $tipo_historico = $param['tipo_historico'];
            $target_path = $param['caminho_diretorio'];
            $formulario_origem = $param['formulario_origem'];
            

            TTransaction::open('Felabs_DB');

            $historico_digital = new HistoricoDigital($id_historico_digital);
            $aluno = new DiplomaDigitalDiplomado($historico_digital->dados_diplomado_id);
            $curso = new DiplomaDigitalCurso($historico_digital->dados_curso_id);            
            $emissora = new DiplomaDigitalEmissora($historico_digital->dados_emissora_id);            
                   
            
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
                $action1 = new TAction(array($formulario_origem, 'onReload'));                       
                new TMessage('error', 'Verifique as informações sobre o código e-mec do curso', $action1);    
                die; 
            }
            
            
            if($emissora->codigo_mec)
            {
                $cod_emissora = trim(utf8_encode($emissora->codigo_mec));
            }
            else
            {
                $action2 = new TAction(array($formulario_origem, 'onReload'));                       
                new TMessage('error', 'Verifique o código e-mec da emissora', $action2);    
                die;
            }
            
            
            //A data de emissão vai ser atualizada após o XML ser gerado com sucesso
            $emissao = date('Y-m-d H:i:s');

            $data_emissao = substr($emissao, 0, 10);
            $hora_emissao = substr($emissao, 11, 5);
            

            //Converte para formato brasileiro
            $data_emissao = TDate::date2br($data_emissao);
                
                
            //Retira os caracteres deixando só os números
            $data_emissao = str_replace('/', '', $data_emissao);
            $hora_emissao = str_replace(':', '', $hora_emissao);
            
                
            //Formata a data de emissão do histórico para DDMMAAAAHHMM
            $emissao_historico = trim($data_emissao) . trim($hora_emissao);


            $codigo_localizacao = trim(trim((utf8_encode($aluno->cod_aluno))) . trim((utf8_encode($aluno->cpf))) . trim((utf8_encode($emec_curso))) . trim((utf8_encode($emissora->cnpj))) . trim((utf8_encode($emissao_historico))));
                       
                       
            //Gera o hash com algoritmo SHA256 do código de localização (deve-se utilizar, no mínimo, 12 caracteres - coloquei 25)
            $hash = hash('sha256', $codigo_localizacao);
            $cod_localizacao = substr($hash, 0, 25);

               
            //Concatena o código da IES para gerar o código de validação do histórico
            $codigo_validacao = trim($cod_emissora . '.' . $cod_localizacao);
                
                
            TTransaction::close();             
           
            $param = ['id_historico_digital' => $id_historico_digital, 'tipo_historico' => $tipo_historico, 'codigo_validacao' => $codigo_validacao, 'data_expedicao_historico' => $emissao, 'caminho_diretorio' => $target_path, 'formulario_origem' => $formulario_origem];
            self::onGerarXMLHistorico($param);  
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onGerarXMLHistorico($param)
    {
        try
        {                 
            $id_historico_digital = $param['id_historico_digital'];
            $tipo_historico = $param['tipo_historico'];
            $codigo_validacao = $param['codigo_validacao'];
            $data_expedicao_historico = $param['data_expedicao_historico'];
            $target_path = $param['caminho_diretorio'];
            $formulario_origem = $param['formulario_origem'];         
      
                     
            TTransaction::open('Felabs_DB');
            
            //Objetos que serão usados na construção do XML
            $historico_digital = new HistoricoDigital($id_historico_digital);
            $aluno = new DiplomaDigitalDiplomado($historico_digital->dados_diplomado_id);
            $curso = new DiplomaDigitalCurso($historico_digital->dados_curso_id);            
            $emissora = new DiplomaDigitalEmissora($historico_digital->dados_emissora_id);                
            $mantenedora = new DiplomaDigitalMantenedora($emissora->dados_mantenedora_id);
            $curriculo_digital = new CurriculoDigital($historico_digital->curriculo_id);                               
            
               
            $target_file = $target_path . '/' . 'historico-' . $codigo_validacao . '.xml';

                
            if((!file_exists($target_file) && is_writable(dirname($target_file))) OR is_writable($target_file))
            {                                
                //Versão XSD que está sendo utilizada
                $versao = DiplomaDigitalVersao::last();
  
                $data_atual = date('Y-m-d');
                
                //Compara as datas e verifica se a versão do XSD é válida
                if($data_atual >= $versao->versao_historico_inicio AND $data_atual <= $versao->versao_historico_termino)
                {
                    $versao_xsd = $versao->versao_historico;
                }
                else
                {
                    $action1 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'Contate o setor de TI para verificar se a versão do XSD utilizada é válida', $action1);    
                    die;
                }
                
                    
                $document = new DOMDocument('1.0', 'UTF-8');
                $document->{'formatOutput'} = true;
                  
                  
                //NÓ DOCUMENTO HISTÓRICO ESCOLAR
                if($tipo_historico == "Parcial")
                {
                    $noDocumentoHistoricoEscolar = $document->createElement('DocumentoHistoricoEscolarParcial');
                    $document->appendChild($noDocumentoHistoricoEscolar);
                }
                elseif($tipo_historico == "Final" OR $tipo_historico == "Transferência")
                {
                    $noDocumentoHistoricoEscolar = $document->createElement('DocumentoHistoricoEscolarFinal');
                    $document->appendChild($noDocumentoHistoricoEscolar);
                }
                //2ª via parcial e 2ª via transferência foram retirados do formulário de escolha, pois 2ª via de um nato físico seria apenas de um histórico completo
                elseif($tipo_historico == "2ª via parcial" OR $tipo_historico == "2ª via transferência" OR $tipo_historico == "2ª via final")
                {
                    $noDocumentoHistoricoEscolar = $document->createElement('DocumentoHistoricoEscolarSegundaViaNatoFisico');
                    $document->appendChild($noDocumentoHistoricoEscolar);
                }
                else
                {
                    $action2 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'Verifique o tipo de histórico selecionado para emissão do XML', $action2);    
                    die;
                }
                                
                                
                $xmlns_ns = $document->createAttribute('xmlns');
                $noDocumentoHistoricoEscolar->appendChild($xmlns_ns);
                                  
                                                              
                //Em ambiente de teste o valor será "Homologação" e em ambiente real "Produção"
                $tipo_conexao = $_SERVER['HTTP_HOST'];
                
                /*if(($tipo_conexao == 'localhost') || ($tipo_conexao == '127.0.0.1'))
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
                                  
                
                //NÓ INFHISTORICOESCOLAR
                $noInfHistoricoEscolar = $document->createElement('infHistoricoEscolar');
                $noInfHistoricoEscolar->setAttribute("versao", $versao_xsd); 
                $noInfHistoricoEscolar->setAttribute("ambiente", $ambiente);                              
                $noDocumentoHistoricoEscolar->appendChild($noInfHistoricoEscolar);    
                             
                   
                //INÍCIO - ALUNO
                $noAluno = $document->createElement('Aluno');
                $noInfHistoricoEscolar->appendChild($noAluno);
       
                $aluno_id                         = $document->createElement("ID", trim($aluno->cod_aluno));
                $aluno_nome                       = $document->createElement("Nome", trim($aluno->nome));
                //$aluno_nome_social                = $document->createElement("NomeSocial", trim($aluno->nome_social)); Desabilitado a pedido da UFSCar
                $aluno_sexo                       = $document->createElement("Sexo", trim($aluno->sexo));
                $aluno_nacionalidade              = $document->createElement("Nacionalidade", trim($aluno->nacionalidade));
                $aluno_codigo_municipio           = $document->createElement("CodigoMunicipio", trim($aluno->naturalidade_cod_municipio));
                $aluno_nome_municipio             = $document->createElement("NomeMunicipio", trim($aluno->naturalidade_nome_municipio));
                $aluno_naturalidade_uf            = $document->createElement("UF", trim($aluno->naturalidade_uf));
                $aluno_nome_municipio_estrangeiro = $document->createElement("NomeMunicipioEstrangeiro", trim($aluno->naturalidade_nome_municipio));
                $aluno_cpf                        = $document->createElement("CPF", trim($aluno->cpf));
                $aluno_rg_numero                  = $document->createElement("Numero", trim($aluno->rg_numero));
                $aluno_rg_orgao_expedidor         = $document->createElement("OrgaoExpedidor", trim($aluno->rg_orgao_expedidor));
                $aluno_rg_uf                      = $document->createElement("UF", trim($aluno->rg_uf));
                $aluno_outro_doc_tipo             = $document->createElement("TipoDocumento", trim($aluno->outro_doc_tipo));
                $aluno_outro_doc_identificador    = $document->createElement("Identificador", trim($aluno->outro_doc_identificador)); 
                $aluno_data_nascimento            = $document->createElement("DataNascimento", trim($aluno->data_nascimento));
                 
      
                $noAluno->appendChild($aluno_id);
                $noAluno->appendChild($aluno_nome);
                //$noAluno->appendChild($aluno_nome_social);
                $noAluno->appendChild($aluno_sexo);
                $noAluno->appendChild($aluno_nacionalidade);
                  
                $noNaturalidade = $document->createElement('Naturalidade');
                $noAluno->appendChild($noNaturalidade);
                      
                if($aluno->opcao_nacionalidade == "Brasileiro" OR $aluno->opcao_nacionalidade == "Brasileira")
                {
                    $noNaturalidade->appendChild($aluno_codigo_municipio);
                    $noNaturalidade->appendChild($aluno_nome_municipio);
                    $noNaturalidade->appendChild($aluno_naturalidade_uf);        
                }
                else
                {
                    $noNaturalidade->appendChild($aluno_nome_municipio_estrangeiro);
                }
                                      
                $noAluno->appendChild($aluno_cpf);
                      
                if($aluno->documento_identificacao == "RG")
                {
                    $noRG = $document->createElement('RG');
                    $noAluno->appendChild($noRG);
                    
                    $noRG->appendChild($aluno_rg_numero);
                    $noRG->appendChild($aluno_rg_orgao_expedidor);
                    $noRG->appendChild($aluno_rg_uf);
                }
                else
                {
                    $noOutroDocumento = $document->createElement('OutroDocumentoIdentificacao');
                    $noAluno->appendChild($noOutroDocumento);
                    
                    $noOutroDocumento->appendChild($aluno_outro_doc_tipo);
                    $noOutroDocumento->appendChild($aluno_outro_doc_identificador);                  
                }
                  
                $noAluno->appendChild($aluno_data_nascimento);                              
                //FIM - ALUNO



                //INÍCIO - CURSO                  
                $noCurso = $document->createElement('DadosCurso');
                $noInfHistoricoEscolar->appendChild($noCurso);
   
                $curso_nome                            = $document->createElement("NomeCurso", trim($curso->nome_curso_diploma));
                $curso_codigo_emec                     = $document->createElement("CodigoCursoEMEC", trim($curso->codigo_curso_emec));
                $curso_sem_codigo_emec_numero_processo = $document->createElement("NumeroProcesso", trim($curso->sem_codigo_emec_numero_processo));
                $curso_sem_codigo_emec_tipo_processo   = $document->createElement("TipoProcesso", trim($curso->sem_codigo_emec_tipo_processo));
                $curso_sem_codigo_emec_data_cadastro   = $document->createElement("DataCadastro", trim($curso->sem_codigo_emec_data_cadastro));
                $curso_sem_codigo_emec_data_protocolo  = $document->createElement("DataProtocolo", trim($curso->sem_codigo_emec_data_protocolo));    
                //$curso_habilitacao                     = $document->createElement("NomeHabilitacao", trim($curso->nome_habilitacao));
                //$curso_data_habilitacao                = $document->createElement("DataHabilitacao", trim($historico_digital->data_colacao_grau));// data habilitação
   
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
                
                //Só acrescenta os dados da habilitação se o aluno formou, pois a data da habilitação corresponde a data da colação de grau
                if($tipo_historico == "Final" OR $tipo_historico == "2ª via final")
                {     
                    if(!empty($historico_digital->data_colacao_grau))
                    {           
                       // $noHabilitacao = $document->createElement('Habilitacao');
                       // $noCurso->appendChild($noHabilitacao);
                                            
                       // $noHabilitacao->appendChild($curso_habilitacao);
                       // $noHabilitacao->appendChild($curso_data_habilitacao); 
                    }
                    else
                    {
                        $action3 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se a data de colação de grau foi lançada no histórico', $action3);    
                        die;
                    }    
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
                    $action4 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'Verifique se os dados de autorização do curso foram lançados corretamente em seu cadastro', $action4);    
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
                    $action5 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'Verifique se os dados de reconhecimento do curso foram lançados corretamente em seu cadastro', $action5);    
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
                        $action6 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se os dados de renovação de reconhecimento do curso foram lançados corretamente em seu cadastro', $action6);    
                        die;
                    } 
                }                  
                //FIM - CURSO



                //INÍCIO - EMISSORA                
                $noEmissora = $document->createElement('IesEmissora');
                $noInfHistoricoEscolar->appendChild($noEmissora);
   
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
                    $action7 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'Verifique se os dados de credenciamento da emissora foram lançados corretamente em seu cadastro', $action7);    
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
                        $action8 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se os dados de recredenciamento da emissora foram lançados corretamente em seu cadastro', $action8);    
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
                        $action9 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se os dados de renovação de recredenciamento da emissora foram lançados corretamente em seu cadastro', $action9);    
                        die;
                    } 
                } 
                
                                
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
                //FIM - EMISSORA
                
                

                //NÓ - HISTÓRICO ESCOLAR
                $noHistoricoEscolar = $document->createElement('HistoricoEscolar');
                $noInfHistoricoEscolar->appendChild($noHistoricoEscolar);
                
                
                //INÍCIO - CÓDIGO CURRÍCULO (1ª via é obrigatório associar o histórico a um currículo)
                if($tipo_historico == "Parcial" OR $tipo_historico == "Final" OR $tipo_historico == "Transferência")
                {
                    if($historico_digital->curriculo_id <> NULL)
                    {
                        $noCodigoCurriculo = $document->createElement("CodigoCurriculo", trim($curriculo_digital->codigo_curriculo));                   
                        $noHistoricoEscolar->appendChild($noCodigoCurriculo);    
                    }
                    else
                    {
                        $action10 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'É necessário vincular o histórico a um currículo', $action10);    
                        die; 
                    }
                }
                //FIM - CÓDIGO CURRÍCULO
                    

                //INÍCIO - ELEMENTOS HISTÓRICO
                $noElementosHistorico = $document->createElement('ElementosHistorico');                   
                $noHistoricoEscolar->appendChild($noElementosHistorico);
                
                
                //LANÇAMENTO DAS DISCIPLINAS
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('historico_digital_id', '=', $historico_digital->id));  
                $criteria1->add(new TFilter('tipo_entrada', '=', 'Disciplina'));  
                $criteria1->setProperty('order', 'etapa, carga_horaria', 'asc');
                    
                $historico_disciplinas = HistoricoDigitalDisciplinas::getObjects($criteria1); 
            
            
                //Unifica informações comuns de disciplinas que tem divisão de frente para aparecerem uma única vez
                foreach($historico_disciplinas as $historico_disciplina)
                {                
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['tipo_entrada'] = $historico_disciplina->tipo_entrada;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['cod_disciplina'] = $historico_disciplina->cod_disciplina;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['nome_disciplina'] = $historico_disciplina->nome_disciplina;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['etapa'] = $historico_disciplina->etapa;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['carga_horaria'] = $historico_disciplina->carga_horaria;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['nota'] = $historico_disciplina->nota;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['situacao'] = $historico_disciplina->situacao;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['forma_integralizacao'] = $historico_disciplina->forma_integralizacao;
                    $dados_disciplinas_historico[$historico_disciplina->cod_disciplina]['periodo_letivo'] = $historico_disciplina->etapa . 'º ciclo - ' . $historico_disciplina->ano . '/' . $historico_disciplina->semestre;                   
                }
            
            
                //Se o histórico estiver vinculado a um currículo, pega os dados das disciplinas para comparação
                if($historico_digital->curriculo_id <> NULL)
                {
                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('curriculo_id', '=', $curriculo_digital->id));    
                    $criteria2->setProperty('order', 'id');
                            
                    $curriculo_disciplinas = CurriculoDisciplina::getObjects($criteria2);                                   
                    
                    if($curriculo_disciplinas)
                    {
                        $i = 0;
                        
                        foreach($curriculo_disciplinas as $curriculo_disciplina)
                        {
                            $parts = explode('-', $curriculo_disciplina->cod_disciplina_curriculo);
                            $cod_disciplina = end($parts);
                            
                            $codigos_disciplinas_curriculo[$i]['cod_disciplina'] = $cod_disciplina;
                            $codigos_disciplinas_curriculo[$i]['cod_disciplina_curriculo'] = $curriculo_disciplina->cod_disciplina_curriculo;
                            
                            $dados_disciplinas_curriculo[$i]['cod_disciplina'] = $cod_disciplina;
                            $dados_disciplinas_curriculo[$i]['cod_disciplina_curriculo'] = $curriculo_disciplina->cod_disciplina_curriculo;
                            $dados_disciplinas_curriculo[$i]['etapa'] = $curriculo_disciplina->etapa;
                            $dados_disciplinas_curriculo[$i]['ch_hora_relogio'] = $curriculo_disciplina->ch_hora_relogio;
                            
                            $i++;
                        }  
                    } 
                    else
                    {
                        $action11 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se no currículo ao qual o histórico está vinculado foram lançadas disciplinas', $action11);    
                        die;
                    }   
                }
                
                
                if($dados_disciplinas_historico)
                {
                    foreach($dados_disciplinas_historico as $dados_disciplina_historico)
                    {    
                        $disciplina = (object) $dados_disciplina_historico;
                                 
                                            
                        //Tipo de entrada
                        $noEntradaDisciplina = $document->createElement('Disciplina');
                        $noElementosHistorico->appendChild($noEntradaDisciplina);                            
                        
                        
                        //Código da disciplina (Adicionado somente em casos de 1ª via em que é obrigatório associar as disciplinas do histórico às do currículo correspondente)
                        if($tipo_historico == "Parcial" OR $tipo_historico == "Final" OR $tipo_historico == "Transferência")
                        {                          
                            if($disciplina->cod_disciplina <> NULL)
                            {
                                $verificacao_codigos_disciplinas = serialize($codigos_disciplinas_curriculo);
                                    
                                //Verifica se o código está presente no currículo vinculado
                                if(strpos($verificacao_codigos_disciplinas, $disciplina->cod_disciplina) !== false)
                                {
                                    foreach($codigos_disciplinas_curriculo as $codigo_disciplina_curriculo)
                                    {
                                        //Adiciona o código que a disciplina recebeu no currículo    
                                        if($disciplina->cod_disciplina == $codigo_disciplina_curriculo['cod_disciplina'])
                                        {
                                            //1º Salva no BD
                                            TTransaction::open('Felabs_DB');
                                                
                                            $disciplinas_historico = HistoricoDigitalDisciplinas::where('historico_digital_id', '=', $historico_digital->id)
                                                                                                ->where('cod_disciplina', '=', $disciplina->cod_disciplina)
                                                                                                ->load();
                                                
                                            //Salva o mesmo código para disciplinas que possuem divisão de frente
                                            foreach($disciplinas_historico as $disciplina_historico)
                                            {
                                                $disciplina_historico->cod_disciplina_historico = $codigo_disciplina_curriculo['cod_disciplina_curriculo'];
                                                $disciplina_historico->store();
                                            }
                                                
                                            TTransaction::close();
                                                
                                            //Se o registro no BD não estiver nulo, significa que foi salvo. Pega o código do 1º registro (caso haja divisão de frente os códigos serão iguais)    
                                            if($disciplinas_historico[0]->cod_disciplina_historico <> NULL)
                                            {
                                                $noCodigoDisciplina = $document->createElement('CodigoDisciplina', trim($disciplinas_historico[0]->cod_disciplina_historico));
                                                $noEntradaDisciplina->appendChild($noCodigoDisciplina);
                                            }
                                            else
                                            {
                                                $action12 = new TAction(array($formulario_origem, 'onReload'));                       
                                                new TMessage('error', "O código da disciplina '$disciplina->nome_disciplina' presente no currículo não foi vinculado corretamente ao histórico", $action12);    
                                                die;    
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $action13 = new TAction(array($formulario_origem, 'onReload'));                       
                                    new TMessage('error', "O código da disciplina '$disciplina->nome_disciplina' lançada no histórico não está presente no currículo ao qual ele está vinculado", $action13);
                                    die;
                                }     
                            }
                            else
                            {
                                $action14 = new TAction(array($formulario_origem, 'onReload'));                       
                                new TMessage('error', "Verifique se o código da disciplina '$disciplina->nome_disciplina' foi adicionado ao histórico", $action14);    
                                die;
                            }
                        }    
                            
                            
                        //Nome da disciplina
                        if($disciplina->nome_disciplina <> NULL)
                        {
                            $noNomeDisciplina = $document->createElement('NomeDisciplina', trim(mb_strtoupper($disciplina->nome_disciplina)));
                            $noEntradaDisciplina->appendChild($noNomeDisciplina);                            
                        }
                        else
                        {
                            $action15 = new TAction(array($formulario_origem, 'onReload'));                       
                            new TMessage('error', 'Verifique se o nome de todas as disciplinas foi adicionado ao histórico', $action15);    
                            die;    
                        }
                            
                            
                        //Período letivo (Se o histórico estiver vinculado a um currículo, compara se os períodos letivos/etapas estão iguais. Se não estiver vinculado, adiciona normalmente)
                        if($historico_digital->curriculo_id <> NULL)
                        {
                            $parts = explode('º', $disciplina->periodo_letivo);
                            $etapa = $parts[0];                            
                                
                            foreach($dados_disciplinas_curriculo as $dados_disciplina_curriculo)
                            {   
                                if($disciplina->cod_disciplina == $dados_disciplina_curriculo['cod_disciplina'])
                                {
                                    //Se forem iguais ou no currículo não constar etapa (optativa), mas no histórico sim, adiciona
                                    if(($etapa == $dados_disciplina_curriculo['etapa']) OR ($disciplina->periodo_letivo <> NULL AND $dados_disciplina_curriculo['etapa'] == NULL))
                                    {
                                        $noPeriodo = $document->createElement('PeriodoLetivo', trim($disciplina->periodo_letivo));
                                        $noEntradaDisciplina->appendChild($noPeriodo);
                                    }
                                    else
                                    {
                                        $action16 = new TAction(array($formulario_origem, 'onReload'));                       
                                        new TMessage('error', "Verifique se a etapa da disciplina '$disciplina->nome_disciplina' é a mesma no histórico e no currículo ao qual ele foi vinculado", $action16);    
                                        die; 
                                    }
                                }
                            }
                        }
                        else
                        {
                            if($disciplina->periodo_letivo <> NULL)
                            {
                                $noPeriodo = $document->createElement('PeriodoLetivo', trim($disciplina->periodo_letivo));
                                $noEntradaDisciplina->appendChild($noPeriodo);
                            }
                            else
                            {
                                $action17 = new TAction(array($formulario_origem, 'onReload'));                       
                                new TMessage('error', 'Verifique se todos os períodos letivos em que as disciplinas foram cursadas foram adicionados ao histórico', $action17);    
                                die;    
                            }
                        }    
                            
                            
                        //CH (Se o histórico estiver vinculado a um currículo, compara se as cargas horárias estão iguais. Se não estiver vinculado, adiciona normalmente)                 
                        if($disciplina->carga_horaria <> NULL)
                        {
                            if($historico_digital->curriculo_id <> NULL)
                            {
                                foreach($dados_disciplinas_curriculo as $dados_disciplina_curriculo)
                                {   
                                    if($disciplina->cod_disciplina == $dados_disciplina_curriculo['cod_disciplina'])
                                    {
                                        //Verifica se tem divisão de frente e calcula o valor total antes de comparar com a ch do currículo
                                        TTransaction::open('Felabs_DB');
                                                
                                        $count_disciplinas_historico = HistoricoDigitalDisciplinas::where('historico_digital_id', '=', $historico_digital->id)
                                                                                                  ->where('cod_disciplina', '=', $disciplina->cod_disciplina)
                                                                                                  ->count();
                                                 
                                        TTransaction::close();     
                                        
                                        //Se a disciplina aparecer uma vez só no histórico, não há divisão de frente
                                        if($count_disciplinas_historico == 1)
                                        {
                                            $ch = $disciplina->carga_horaria;  
                                        }
                                        //Se aparecer mais de uma, há divisão de frente, então multiplica a carga horária pelo número de vezes que ela aparece
                                        else
                                        {
                                            $ch = number_format($disciplina->carga_horaria * $count_disciplinas_historico, 2, '.', '');
                                        }
                                                                                                      
                                        /*$verifica_digito = substr($disciplina->carga_horaria, -2);
                                       
                                        //Se terminar em .00 não há divisão na frente de disciplina
                                        if($verifica_digito == 00)
                                        {
                                            $ch = $disciplina->carga_horaria;
                                        }
                                        else
                                        {
                                            $ch = $disciplina->carga_horaria * 2;
                                        }*/        
                                                    
                                        //Se forem iguais, adiciona
                                        if($ch == $dados_disciplina_curriculo['ch_hora_relogio'])
                                        {     
                                            $noCargaHoraria = $document->createElement('CargaHoraria');
                                            $noEntradaDisciplina->appendChild($noCargaHoraria);
                                                 
                                            $noHoraRelogio = $document->createElement('HoraRelogio', trim($ch));
                                            $noCargaHoraria->appendChild($noHoraRelogio);
                                                
                                            //A carga horária integralizada só é contabilizada se o aluno for Aprovado na disciplina
                                            if($disciplina->situacao == "Aprovado")
                                            {
                                                $CH_integralizada += $ch;
                                                $ch_integralizada_disciplinas += $ch; 
                                            }    
                                        }
                                        else
                                        {
                                            $action18 = new TAction(array($formulario_origem, 'onReload'));                       
                                            new TMessage('error', "Verifique se a carga horária da disciplina '$disciplina->nome_disciplina' é a mesma no histórico e no currículo ao qual ele foi vinculado", $action18);    
                                            die; 
                                        }
                                    }
                                }    
                            }
                            else
                            {
                                //Verifica se tem divisão de frente e calcula o valor total
                                TTransaction::open('Felabs_DB');
                                                
                                $count_disciplinas_historico = HistoricoDigitalDisciplinas::where('historico_digital_id', '=', $historico_digital->id)
                                                                                          ->where('cod_disciplina', '=', $disciplina->cod_disciplina)
                                                                                          ->count();
                                                 
                                TTransaction::close();     
                                        
                                //Se a disciplina aparecer uma vez só no histórico, não há divisão de frente
                                if($count_disciplinas_historico == 1)
                                {
                                    $ch = $disciplina->carga_horaria;  
                                }
                                //Se aparecer mais de uma, há divisão de frente, então multiplica a carga horária pelo número de vezes que ela aparece
                                else
                                {
                                    $ch = number_format($disciplina->carga_horaria * $count_disciplinas_historico, 2, '.', '');
                                }
                                        
                                /*$verifica_digito = substr($disciplina->carga_horaria, -2);
                                      
                                //Se terminar em .00 não há divisão de frente de disciplina
                                if($verifica_digito == 00)
                                {
                                    $ch = $disciplina->carga_horaria;
                                }         
                                else
                                {
                                    $ch = $disciplina->carga_horaria * 2;
                                }*/         
                                    
                                $noCargaHoraria = $document->createElement('CargaHoraria');
                                $noEntradaDisciplina->appendChild($noCargaHoraria);
                                     
                                $noHoraRelogio = $document->createElement('HoraRelogio', trim($ch));
                                $noCargaHoraria->appendChild($noHoraRelogio);
                                        
                                //A carga horária integralizada só é contabilizada se o aluno for Aprovado na disciplina
                                if($disciplina->situacao == "Aprovado")
                                {
                                    $CH_integralizada += $ch;
                                    $ch_integralizada_disciplinas += $ch; 
                                }            
                            }    
                        }                    
                        else
                        {
                            $action19 = new TAction(array($formulario_origem, 'onReload'));                       
                            new TMessage('error', 'Verifique se todas as cargas horárias das disciplinas foram adicionadas ao histórico', $action19);    
                            die;
                        }
                           
                           
                        //Nota (só acrescenta nota se aluno foi aprovado ou reprovado)
                        if($disciplina->situacao == "Aprovado" OR $disciplina->situacao == "Reprovado")
                        {
                            if($disciplina->nota <> NULL)
                            {
                                $noNota = $document->createElement('Nota', trim($disciplina->nota));
                                $noEntradaDisciplina->appendChild($noNota);
                            }
                            else
                            {                                
                                $action20 = new TAction(array($formulario_origem, 'onReload'));                       
                                new TMessage('error', 'Verifique se todas as notas foram adicionadas ao histórico', $action20);    
                                die;    
                            }
                        }
                                                       
                           
                        //Situação
                        if($disciplina->situacao == "Aprovado")
                        {
                            $noAprovado = $document->createElement('Aprovado');
                            $noEntradaDisciplina->appendChild($noAprovado);                   
                               
                            if($disciplina->forma_integralizacao <> NULL)
                            {
                                $noFormaIntegralizacao = $document->createElement('FormaIntegralizacao', trim($disciplina->forma_integralizacao));
                                $noAprovado->appendChild($noFormaIntegralizacao);    
                            }
                            else
                            {
                                $action21 = new TAction(array($formulario_origem, 'onReload'));                       
                                new TMessage('error', 'Em disciplinas em que o aluno foi aprovado a forma de integralização deve ser compatível com uma das listadas a seguir: <br>
                                                       Cursado, Validado ou Aproveitado', $action21);    
                                die;
                            }
                        }
                        elseif($disciplina->situacao == "Reprovado")
                        {
                            $noReprovado = $document->createElement('Reprovado');
                            $noEntradaDisciplina->appendChild($noReprovado);
                        }
                        elseif($disciplina->situacao == "Pendente")
                        {
                            if($tipo_historico == "Transferência" OR $tipo_historico == "2ª via transferência" OR $tipo_historico == "Final" OR $tipo_historico == "2ª via final")
                            {
                                $action22 = new TAction(array($formulario_origem, 'onReload'));                       
                                new TMessage('error', 'Em um histórico de transferência ou final não deve constar disciplinas em que a situação do aluno esteja Pendente', $action22);    
                                die;
                            }
                            else
                            {
                                $noPendente = $document->createElement('Pendente');
                                $noEntradaDisciplina->appendChild($noPendente);    
                            }    
                        }
                        else
                        {
                            $action23 = new TAction(array($formulario_origem, 'onReload'));                       
                            new TMessage('error', 'Verifique se a situação do aluno nas disciplinas é compatível com um das listadas a seguir: <br>
                                                   Aprovado, Reprovado ou Pendente', $action23);    
                            die;
                        }
                            
                               
                        /*Docentes (Vai adicionar se:
                                    - 1ª via (obrigatório)
                                    - 2ª via e ano cursado >= 2019 (obrigatório)
                                    - 2ª via, ano cursado < 2019 e conste nome e titulação do professor)*/
                            
                        $parts_1 = explode('-', $disciplina->periodo_letivo);
                        $parts_2 = explode('/', $parts_1[1]);
                        $ano_cursado = $parts_2[0];
                           
                            
                        //Verifica se o nome e a titulação do professor foram inseridos para o caso do histórico cair na 3ª opção
                        foreach($historico_disciplinas as $historico_disciplina)
                        {
                            if($disciplina->cod_disciplina == $historico_disciplina->cod_disciplina)
                            {
                                $verifica_nome_professor = $historico_disciplina->nome_professor;
                                $verifica_titulacao_professor = $historico_disciplina->titulacao_professor;
                            }
                        }
                                                        
           /*1ª opção*/ if(($tipo_historico == "Parcial" OR $tipo_historico == "Transferência" OR $tipo_historico == "Final") OR
           /*2ª opção*/   (($tipo_historico == "2ª via parcial" OR $tipo_historico == "2ª via transferência" OR $tipo_historico == "2ª via final") AND ($ano_cursado >= 2019)) OR
           /*3ª opção*/   (($tipo_historico == "2ª via parcial" OR $tipo_historico == "2ª via transferência" OR $tipo_historico == "2ª via final") AND ($ano_cursado < 2019) AND ($verifica_nome_professor <> NULL AND $verifica_titulacao_professor <> NULL)))   
                        {
                            //Esta tag é inserida uma única vez em cada disciplina
                            $noDocentes = $document->createElement('Docentes');
                            $noEntradaDisciplina->appendChild($noDocentes);                                
                                     
                            foreach($historico_disciplinas as $historico_disciplina)
                            {
                                if($disciplina->cod_disciplina == $historico_disciplina->cod_disciplina)
                                {
                                    $noDocente = $document->createElement('Docente');
                                    $noDocentes->appendChild($noDocente);                                
                                                                                      
                                    //Nome
                                    if($historico_disciplina->nome_professor <> NULL)
                                    {
                                        $noNomeDocente = $document->createElement('Nome', trim($historico_disciplina->nome_professor));
                                        $noDocente->appendChild($noNomeDocente);
                                    }
                                    else
                                    {
                                        $action24 = new TAction(array($formulario_origem, 'onReload'));                                
                                        new TMessage('error', 'Verifique se o nome do docente foi registrado em todas as disciplinas lançadas no histórico', $action24);    
                                        die;
                                    } 
                                                                                   
                                    //Titulação
                                    if($historico_disciplina->titulacao_professor <> NULL)
                                    {
                                        $noTitulacaoDocente = $document->createElement('Titulacao', trim($historico_disciplina->titulacao_professor));
                                        $noDocente->appendChild($noTitulacaoDocente);
                                    } 
                                    else
                                    {
                                        $action25 = new TAction(array($formulario_origem, 'onReload'));                                        
                                        new TMessage('error', 'A habilitação dos professores no histórico deve ser compatível com uma das listadas a seguir: <br>
                                                      Tecnólogo, Graduação, Especialização, Mestrado ou Doutorado', $action25);    
                                        die;    
                                    }   
                                }
                            }
                        }                    
                    }
                }
                else
                {
                    $action26 = new TAction(array($formulario_origem, 'onReload'));                                        
                    new TMessage('error', 'Verifique se as disciplinas foram lançadas no histórico', $action26);    
                    die;    
                }



                //LANÇAMENTO DAS ATIVIDADES
                $criteria3 = new TCriteria;
                $criteria3->add(new TFilter('cod_aluno', '=', $historico_digital->cod_aluno)); 
                $criteria3->add(new TFilter('cod_curso', '=', $historico_digital->cod_curso)); 
                $criteria3->add(new TFilter('status_atividade', '=', 'Aprovado'));  
                $criteria3->setProperty('order', 'data_inicio', 'asc');
                
                $lancamentos_atividades = AtividadeComplementar::getObjects($criteria3);
                

                //Se o histórico estiver vinculado a um currículo, pega os dados das atividades para comparação
                if($historico_digital->curriculo_id <> NULL)
                {
                    $criteria4 = new TCriteria;
                    $criteria4->add(new TFilter('curriculo_id', '=', $curriculo_digital->id));  
                    $criteria4->setProperty('order', 'id');
                        
                    $curriculo_atividades = CurriculoAtividadeCadastro::getObjects($criteria4);
                                    
                    if($curriculo_atividades)
                    {
                        $i = 0;
                        
                        //Pega o ID da atividade e o código que ela tem no currículo
                        foreach($curriculo_atividades as $curriculo_atividade)
                        {
                            $codigos_atividades_curriculo[$i]['atividade_complementar_cadastro_id'] = $curriculo_atividade->atividade_complementar_cadastro_id;
                            $codigos_atividades_curriculo[$i]['cod_atividade_curriculo'] = $curriculo_atividade->cod_atividade_curriculo;

                            $i++;
                        }  
                    }
                    else
                    {
                        $action27 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se no currículo ao qual o histórico está vinculado foram estruturadas todas as atividades complementares', $action27);    
                        die;
                    }   
                }
                
                
                if($lancamentos_atividades)
                {
                    foreach($lancamentos_atividades as $lancamento_atividade)
                    {
                        $atividade_complementar = (object) $lancamento_atividade;
                         
                        if($atividade_complementar)
                        {
                            //Tipo de entrada
                            $noEntradaAtividade = $document->createElement('AtividadeComplementar');
                            $noElementosHistorico->appendChild($noEntradaAtividade);                            
                       
                       
                            //Código da atividade (Adicionado somente em casos de 1ª via em que é obrigatório associar as atividades do histórico às do currículo correspondente)
                            if($tipo_historico == "Parcial" OR $tipo_historico == "Final" OR $tipo_historico == "Transferência")
                            {
                                if($atividade_complementar->cadastro_atividade_id <> NULL)
                                {                                   
                                    $verificacao_codigos_atividades = serialize($codigos_atividades_curriculo); 
                                    
                                    //Verifica se o código está presente no currículo vinculado
                                    if(strpos($verificacao_codigos_atividades, $atividade_complementar->cadastro_atividade_id) !== false)
                                    {
                                        foreach($codigos_atividades_curriculo as $codigo_atividade_curriculo)
                                        {
                                            //Adiciona o código que a atividade recebeu no currículo    
                                            if($atividade_complementar->cadastro_atividade_id == $codigo_atividade_curriculo['atividade_complementar_cadastro_id'])
                                            {
                                                //1º Salva no BD (na tabela de lançamentos)
                                                TTransaction::open('Felabs_DB');

                                                $atividade_complementar->cod_atividade_historico = $codigo_atividade_curriculo['cod_atividade_curriculo'];
                                                $atividade_complementar->store();

                                                TTransaction::close();
                                                                                      
                                                if($atividade_complementar->cod_atividade_historico <> NULL)
                                                {
                                                    $noCodigoAtividade = $document->createElement('CodigoAtividadeComplementar', trim($atividade_complementar->cod_atividade_historico));
                                                    $noEntradaAtividade->appendChild($noCodigoAtividade);
                                                }
                                                else
                                                {
                                                    $action28 = new TAction(array($formulario_origem, 'onReload'));                       
                                                    new TMessage('error', "O código da atividade '$atividade_complementar->descricao' presente no currículo não foi vinculado corretamente ao histórico", $action28);    
                                                    die;    
                                                }
                                            }    
                                        }     
                                    }
                                    else
                                    {
                                        $action29 = new TAction(array($formulario_origem, 'onReload'));                       
                                        new TMessage('error', "O código da atividade '$atividade_complementar->descricao' lançada no histórico não está presente no currículo ao qual ele está vinculado", $action29);
                                        die;
                                    }  
                                }
                                else
                                {
                                    $action30 = new TAction(array($formulario_origem, 'onReload'));                       
                                    new TMessage('error', "Verifique se o ID da atividade '$atividade_complementar->descricao' foi registrado ao fazer o lançamento da mesma", $action30);    
                                    die;
                                }        
                            }
                           
                       
                            //Data de início
                            $noDataInicioAtividade = $document->createElement('DataInicio', trim($atividade_complementar->data_inicio));
                            $noEntradaAtividade->appendChild($noDataInicioAtividade);                        
                           
                           
                            //Data de término
                            $noDataFimAtividade = $document->createElement('DataFim', trim($atividade_complementar->data_termino));
                            $noEntradaAtividade->appendChild($noDataFimAtividade);                           
                           
                           
                            //Tipo de atividade
                            $noTipoAtividade = $document->createElement('TipoAtividadeComplementar', trim($atividade_complementar->tipo_atividade));
                            $noEntradaAtividade->appendChild($noTipoAtividade);                          
                           
                           
                            //Descrição
                            $noDescricaoAtividade = $document->createElement('Descricao', trim($atividade_complementar->descricao));
                            $noEntradaAtividade->appendChild($noDescricaoAtividade);    
                            
                           
                            //Carga horária
                            $noChAtividade = $document->createElement('CargaHorariaEmHoraRelogio', trim($atividade_complementar->carga_horaria));
                            $noEntradaAtividade->appendChild($noChAtividade);
                               
                            $CH_integralizada += $atividade_complementar->carga_horaria;                            
                            $ch_integralizada_atividades += $atividade_complementar->carga_horaria;
                           
                           
                            //Docente responsável
                            TTransaction::open('dados_fei');
                                
                            $prof = new FiProfessor($atividade_complementar->cod_prof_responsavel);
                               
                            TTransaction::close();
                                                       
                            $noResponsaveisAtividade = $document->createElement('DocentesResponsaveisPelaValidacao');
                            $noEntradaAtividade->appendChild($noResponsaveisAtividade);
                               
                            $noResponsavelAtividade = $document->createElement('Docente');
                            $noResponsaveisAtividade->appendChild($noResponsavelAtividade); 
                           
                           
                            //Nome                              
                            $noNomeDocenteResponsavelAtividade = $document->createElement('Nome', trim($prof->Nome));
                            $noResponsavelAtividade->appendChild($noNomeDocenteResponsavelAtividade);                                                 
                           
                           
                            //Titulação
                            $noTitulacaoDocenteResponsavelAtividade = $document->createElement('Titulacao', trim($atividade_complementar->titulacao_prof_responsavel));
                            $noResponsavelAtividade->appendChild($noTitulacaoDocenteResponsavelAtividade); 
                        }     
                    }
                }
                
                
                
                //LANÇAMENTO DOS ESTÁGIOS 
                $criteria5 = new TCriteria;
                $criteria5->add(new TFilter('historico_digital_id', '=', $historico_digital->id));  
                $criteria5->add(new TFilter('tipo_entrada', '=', 'Estágio'));   
                $criteria5->setProperty('order', 'etapa, nome_disciplina', 'asc');
                       
                $historico_estagios = HistoricoDigitalDisciplinas::getObjects($criteria5);
               
                
                $criteria6 = new TCriteria;
                $criteria6->add(new TFilter('cod_aluno', '=', $historico_digital->cod_aluno)); 
                $criteria6->add(new TFilter('cod_curso', '=', $historico_digital->cod_curso)); 
                $criteria6->add(new TFilter('status_estagio', '=', 'Aprovado'));  
                $criteria6->setProperty('order', 'etapa, data_inicio', 'asc');
                
                $lancamentos_estagios = Estagio::getObjects($criteria6);
                

                //Se o histórico estiver vinculado a um currículo, pega os dados dos estágios para comparação (Enfermagem tem dois lançamentos)
                if($historico_digital->curriculo_id <> NULL)
                {
                    $criteria7 = new TCriteria;
                    $criteria7->add(new TFilter('curriculo_id', '=', $curriculo_digital->id));   
                    $criteria7->add(new TFilter('tipo', '=', 'Estágio'));    
                    $criteria7->setProperty('order', 'etapa');
                            
                    $curriculo_estagios = CurriculoDisciplina::getObjects($criteria7);                                   
                     
                    if($curriculo_estagios)
                    {
                        $i = 0;
                        
                        foreach($curriculo_estagios as $curriculo_estagio)
                        {
                            $parts = explode('-', $curriculo_estagio->cod_disciplina_curriculo);
                            $cod_estagio = end($parts);
                             
                            $codigos_estagios_curriculo[$i]['cod_disciplina'] = $cod_estagio;
                            $codigos_estagios_curriculo[$i]['cod_disciplina_curriculo'] = $curriculo_estagio->cod_disciplina_curriculo;
                            
 
                            $dados_estagios_curriculo[$i]['cod_disciplina'] = $cod_estagio;
                            $dados_estagios_curriculo[$i]['cod_disciplina_curriculo'] = $curriculo_estagio->cod_disciplina_curriculo;
                            $dados_estagios_curriculo[$i]['etapa'] = $curriculo_estagio->etapa;
                            $dados_estagios_curriculo[$i]['ch_hora_relogio'] = $curriculo_estagio->ch_hora_relogio;
                            
                            $i++;
                        }  
                    }    
                }
                
                
                /*Só vai aparecer no XML se estiver inserido na grade, pois o código é obrigatório. Caso contrário, deve ser lançado nas informações adicionais. 
                  Na 1ª via insere o código correspondente presente no currículo e na 2ª via, insere o código da disciplina presente no histórico)*/
                if($historico_estagios)
                {
                    if($lancamentos_estagios)
                    {
                        foreach($lancamentos_estagios as $lancamento_estagio)
                        {
                            $estagio = (object) $lancamento_estagio;    
                                
                            if($estagio)
                            {
                                //Tipo de entrada
                                $noEntradaEstagio = $document->createElement('Estagio');
                                $noElementosHistorico->appendChild($noEntradaEstagio);                            
                                
                                
                                //Código do estágio 1ª via puxa do currículo (Seja histórico automático ou manual (antigo), 1ª via tem que ter currículo)
                                if($tipo_historico == "Parcial" OR $tipo_historico == "Final" OR $tipo_historico == "Transferência")
                                {                                   
                                    $count_etapas = count($curriculo_estagios);
                                    
                                    if($count_etapas == 1)
                                    {
                                        //1º Salva no BD
                                        TTransaction::open('Felabs_DB');
                                                      
                                        $estagio->cod_estagio_historico = $codigos_estagios_curriculo[0]['cod_disciplina_curriculo'];
                                        $estagio->store();
                   
                                        TTransaction::close(); 
                                        
                                        //Se o registro no BD não estiver nulo, significa que foi salvo    
                                        if($estagio->cod_estagio_historico <> NULL)
                                        {
                                            $noCodigoEstagio = $document->createElement('CodigoUnidadeCurricular', trim($estagio->cod_estagio_historico));
                                            $noEntradaEstagio->appendChild($noCodigoEstagio);
                                        }
                                        else
                                        {
                                            $action31 = new TAction(array($formulario_origem, 'onReload'));                       
                                            new TMessage('error', "O código do estágio '$estagio->descricao' presente no currículo não foi vinculado corretamente ao histórico", $action31);    
                                            die;    
                                        }           
                                    }                                   
                                    elseif($count_etapas > 1)
                                    {
                                        foreach($dados_estagios_curriculo as $dado_estagio_curriculo)
                                        {  
                                            //Adiciona o código do estágio presente no currículo que corresponda a mesma etapa em que o aluno estava quando realizou o estágio                                     
                                            if($estagio->etapa == $dado_estagio_curriculo['etapa'])
                                            {
                                                //1º Salva no BD 
                                                TTransaction::open('Felabs_DB');
                                                         
                                                $estagio->cod_estagio_historico = $dado_estagio_curriculo['cod_disciplina_curriculo'];
                                                $estagio->store();
                                                         
                                                TTransaction::close();
                                            }
                                        }        
                                        
                                        //Se o registro no BD não estiver nulo, significa que foi salvo    
                                        if($estagio->cod_estagio_historico <> NULL)
                                        {
                                            $noCodigoEstagio = $document->createElement('CodigoUnidadeCurricular', trim($estagio->cod_estagio_historico));
                                            $noEntradaEstagio->appendChild($noCodigoEstagio);
                                        }
                                        else
                                        {
                                            $action32 = new TAction(array($formulario_origem, 'onReload'));                       
                                            new TMessage('error', "O código do estágio '$estagio->descricao' presente no currículo não foi vinculado corretamente ao histórico", $action32);    
                                            die;    
                                        } 
                                    }                                   
                                    else
                                    {
                                        $action33 = new TAction(array($formulario_origem, 'onReload'));                       
                                        new TMessage('error', "Verifique se alguma unidade do currículo vinculado corresponde ao tipo 'Estágio'", $action33);    
                                        die;
                                    }    
                                }
                               
                               
                                /*Código do estágio 2ª via puxa do histórico (Se o histórico for automático, significa que tem registro da grade no Genesi - gera o mesmo código composto que origina o da 1ª via 
                                  para que sejam sempre iguais independentemente da via emitida. Se for manual, significa que não tem registro da grade no Genesi - neste caso insere só o código da disciplina*/
                                else
                                {
                                    $count_etapas = count($historico_estagios);  
                                    
                                    
                                    if($historico_digital->historico_gerado == "Automático")
                                    {
                                        TTransaction::open('dados_fei');
                                           
                                        $historico_genesi = VwDadoshistoricoaluno::where('codhistorico', '=', $historico_digital->historico_genesi_id)->load();                                           
                                        $cod_curso_grade = $historico_genesi[0]->CodCurso . '.' . $historico_genesi[0]->CodGradecurso;  
                                           
                                        TTransaction::close();                                            


                                        if($count_etapas == 1)
                                        {
                                            //1º Salva no BD
                                            TTransaction::open('Felabs_DB');
                                                         
                                            $estagio->cod_estagio_historico = $cod_curso_grade . '-' . $historico_estagios[0]->cod_disciplina;
                                            $estagio->store();
                       
                                            TTransaction::close();
                                            
                                            //Se o registro no BD não estiver nulo, significa que foi salvo    
                                            if($estagio->cod_estagio_historico <> NULL)
                                            {
                                                $noCodigoEstagio = $document->createElement('CodigoUnidadeCurricular', trim($estagio->cod_estagio_historico));
                                                $noEntradaEstagio->appendChild($noCodigoEstagio);
                                            }
                                            else
                                            {
                                                $action34 = new TAction(array($formulario_origem, 'onReload'));                       
                                                new TMessage('error', "O código do estágio '$estagio->descricao' não foi vinculado corretamente ao histórico", $action34);    
                                                die;    
                                            }
                                        }                                       
                                        elseif($count_etapas > 1)
                                        {
                                            foreach($historico_estagios as $historico_estagio)
                                            {                                                                                 
                                                if($estagio->etapa == $historico_estagio->etapa)
                                                {
                                                    //1º Salva no BD
                                                    TTransaction::open('Felabs_DB');
                                                                 
                                                    $estagio->cod_estagio_historico = $cod_curso_grade . '-' . $historico_estagio->cod_disciplina;
                                                    $estagio->store();
                               
                                                    TTransaction::close();
                                                }
                                            }
                                                    
                                            //Se o registro no BD não estiver nulo, significa que foi salvo    
                                            if($estagio->cod_estagio_historico <> NULL)
                                            {
                                                $noCodigoEstagio = $document->createElement('CodigoUnidadeCurricular', trim($estagio->cod_estagio_historico));
                                                $noEntradaEstagio->appendChild($noCodigoEstagio);
                                            }
                                            else
                                            {
                                                $action35 = new TAction(array($formulario_origem, 'onReload'));                       
                                                new TMessage('error', "O código do estágio '$estagio->descricao' não foi vinculado corretamente ao histórico", $action35);    
                                                die;    
                                            }       
                                        }                                       
                                        else
                                        {
                                            $action36 = new TAction(array($formulario_origem, 'onReload'));                       
                                            new TMessage('error', "Verifique se alguma unidade do histórico corresponde ao tipo 'Estágio'", $action36);    
                                            die;
                                        }  
                                    }
                                    else
                                    {
                                        if($count_etapas == 1)
                                        {
                                            //1º Salva no BD
                                            TTransaction::open('Felabs_DB');
                                                         
                                            $estagio->cod_estagio_historico = $historico_estagios[0]->cod_disciplina;
                                            $estagio->store();
                       
                                            TTransaction::close();
                                            
                                            //Se o registro no BD não estiver nulo, significa que foi salvo    
                                            if($estagio->cod_estagio_historico <> NULL)
                                            {
                                                $noCodigoEstagio = $document->createElement('CodigoUnidadeCurricular', trim($estagio->cod_estagio_historico));
                                                $noEntradaEstagio->appendChild($noCodigoEstagio);
                                            }
                                            else
                                            {
                                                $action37 = new TAction(array($formulario_origem, 'onReload'));                       
                                                new TMessage('error', "O código do estágio '$estagio->descricao' não foi vinculado corretamente ao histórico", $action37);    
                                                die;    
                                            }
                                        }                                       
                                        elseif($count_etapas > 1)
                                        {
                                            foreach($historico_estagios as $historico_estagio)
                                            {                                                                                 
                                                if($estagio->etapa == $historico_estagio->etapa)
                                                {
                                                    //1º Salva no BD
                                                    TTransaction::open('Felabs_DB');
                                                                 
                                                    $estagio->cod_estagio_historico = $historico_estagio->cod_disciplina;
                                                    $estagio->store();
                               
                                                    TTransaction::close();
                                                }
                                            }        
                                            
                                            //Se o registro no BD não estiver nulo, significa que foi salvo    
                                            if($estagio->cod_estagio_historico <> NULL)
                                            {
                                                $noCodigoEstagio = $document->createElement('CodigoUnidadeCurricular', trim($estagio->cod_estagio_historico));
                                                $noEntradaEstagio->appendChild($noCodigoEstagio);
                                            }
                                            else
                                            {
                                                $action38 = new TAction(array($formulario_origem, 'onReload'));                       
                                                new TMessage('error', "O código do estágio '$estagio->descricao' não foi vinculado corretamente ao histórico", $action38);    
                                                die;    
                                            }  
                                        }                                       
                                        else
                                        {
                                            $action39 = new TAction(array($formulario_origem, 'onReload'));                       
                                            new TMessage('error', "Verifique se alguma unidade do histórico corresponde ao tipo 'Estágio'", $action39);    
                                            die;
                                        }
                                    }                                                                       
                                }
                                                               
                               
                                //Data de início
                                $noDataInicioEstagio = $document->createElement('DataInicio', trim($estagio->data_inicio));
                                $noEntradaEstagio->appendChild($noDataInicioEstagio);                            
                                
                                
                                //Data de término
                                $noDataFimEstagio = $document->createElement('DataFim', trim($estagio->data_termino));
                                $noEntradaEstagio->appendChild($noDataFimEstagio);                            
                                
                                
                                //Concedente
                                if($estagio->opcao_estagio <> NULL)
                                {
                                    if($estagio->opcao_estagio == "Pessoa física")
                                    {
                                        if(($estagio->nome_pessoa_fisica <> NULL) AND ($estagio->cpf_pessoa_fisica <> NULL))
                                        {
                                            $noConcedenteEstagio = $document->createElement('Concedente');
                                            $noEntradaEstagio->appendChild($noConcedenteEstagio);
                                                
                                            $noNomePessoaEstagio = $document->createElement('Nome', trim($estagio->nome_pessoa_fisica));
                                            $noConcedenteEstagio->appendChild($noNomePessoaEstagio); 
                                           
                                            $noCpfPessoaEstagio = $document->createElement('CPF', trim($estagio->cpf_pessoa_fisica));
                                            $noConcedenteEstagio->appendChild($noCpfPessoaEstagio); 
                                        }
                                    }
                                    
                                    if($estagio->opcao_estagio == "Pessoa jurídica")
                                    {
                                        if(($estagio->razao_social_empresa <> NULL) AND ($estagio->cnpj_empresa <> NULL))
                                        {
                                            $noConcedenteEstagio = $document->createElement('Concedente');
                                            $noEntradaEstagio->appendChild($noConcedenteEstagio);
                                                
                                            $noRazaoSocialEstagio = $document->createElement('RazaoSocial', trim($estagio->razao_social_empresa));
                                            $noConcedenteEstagio->appendChild($noRazaoSocialEstagio);
                                            
                                            $noCnpjEmpresaEstagio = $document->createElement('CNPJ', trim($estagio->cnpj_empresa));
                                            $noConcedenteEstagio->appendChild($noCnpjEmpresaEstagio); 
                                        }    
                                    }                                   
                                }
                                
                               
                                //Descrição
                                $noDescricaoEstagio = $document->createElement('Descricao', trim($estagio->descricao));
                                $noEntradaEstagio->appendChild($noDescricaoEstagio);                              
     
                                
                                //Carga horária
                                $noChEstagio = $document->createElement('CargaHorariaEmHorasRelogio', trim($estagio->carga_horaria));
                                $noEntradaEstagio->appendChild($noChEstagio);  
                                    
                                $CH_integralizada += $estagio->carga_horaria;                          
                                $ch_integralizada_estagios += $estagio->carga_horaria; 
                                
                            
                                //Docente responsável
                                TTransaction::open('dados_fei');
                                    
                                $prof = new FiProfessor($estagio->cod_prof_responsavel);
                                   
                                TTransaction::close();
                                    
                                    
                                $noResponsaveisEstagio = $document->createElement('DocentesOrientadores');
                                $noEntradaEstagio->appendChild($noResponsaveisEstagio);
                                    
                                $noResponsavelEstagio = $document->createElement('Docente');
                                $noResponsaveisEstagio->appendChild($noResponsavelEstagio); 
                                
                               
                                //Nome                              
                                $noNomeDocenteResponsavelEstagio = $document->createElement('Nome', trim($prof->Nome));
                                $noResponsavelEstagio->appendChild($noNomeDocenteResponsavelEstagio);                                                 
     
                                
                                //Titulação
                                $noTitulacaoDocenteResponsavelEstagio = $document->createElement('Titulacao', trim($estagio->titulacao_prof_responsavel));
                                $noResponsavelEstagio->appendChild($noTitulacaoDocenteResponsavelEstagio);                             
                            } 
                        }          
                    }
                }        


                            
                /*LANÇAMENTO DAS SITUAÇÕES
                Pega todas as situações do aluno no decorrer do curso (dos alunos do Genesi puxa da Vw_AlunoMatriculaEtapa e dos antigos da tabela historico_situacao_discente)*/
                if($historico_digital->historico_gerado == "Automático")
                {
                    TTransaction::open('dados_fei');
                    
                    $criteria8 = new TCriteria;
                    $criteria8->add(new TFilter('Codaluno', '=', $historico_digital->cod_aluno));
                    $criteria8->add(new TFilter('CodCurso', '=', $historico_digital->cod_curso));
                    $criteria8->setProperty('order', 'AnoMatricula, SemestreMatricula, EtapaMatricula', 'asc');
                                        
                    $situacoes_discente = VwAlunoMatriculaEtapa::getObjects($criteria8);
             
                    TTransaction::close();
                    
                    $i = 0;
                    
                    foreach($situacoes_discente as $situacao_discente)
                    {
                        //Faz as correspondências aceitas pelo MEC
                        switch($situacao_discente->SituacaoMatricula)
                        {
                            case "FR":
                                $lancamento_situacoes[$i]['situacao_discente'] = "MatriculadoEmDisciplina";
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;
                                break;
                                
                            case "DS":
                                $lancamento_situacoes[$i]['situacao_discente'] = "Desistencia";
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;
                                break; 
                            
                            case "TE":
                                $lancamento_situacoes[$i]['situacao_discente'] = "OutraSituacao";
                                $lancamento_situacoes[$i]['descricao_situacao'] = "Transferência expedida";
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;
                                break;
                                
                            case "AB":
                                $lancamento_situacoes[$i]['situacao_discente'] = "Abandono";
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;
                                break; 
                                
                            case "CL":
                                $lancamento_situacoes[$i]['situacao_discente'] = "Formado";
                                $lancamento_situacoes[$i]['formado_data_conclusao'] = $historico_digital->data_conclusao_curso;
                                $lancamento_situacoes[$i]['formado_data_colacao'] = $historico_digital->data_colacao_grau;
                                $lancamento_situacoes[$i]['formado_data_exp_diploma'] = $historico_digital->data_expedicao_diploma;
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;                                
                                break;
                                
                            case "TR":
                                $lancamento_situacoes[$i]['situacao_discente'] = "Trancamento";
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;
                                break;
                                
                            case "RC":
                                $lancamento_situacoes[$i]['situacao_discente'] = "OutraSituacao";
                                $lancamento_situacoes[$i]['descricao_situacao'] = "Reclassificado";
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;
                                break;
                                
                            case "RM":
                                $lancamento_situacoes[$i]['situacao_discente'] = "OutraSituacao";
                                $lancamento_situacoes[$i]['descricao_situacao'] = "Remanejado";
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->AnoMatricula . '/' . $situacao_discente->SemestreMatricula;
                                break;                                
                        }
                        
                        $i++;
                    }
                }   
                else
                {
                    $criteria9 = new TCriteria;
                    $criteria9->add(new TFilter('historico_digital_id', '=', $historico_digital->id));
                    $criteria9->setProperty('order', 'situacao_ano, situacao_semestre, situacao_etapa', 'asc');
                                       
                    $situacoes_discente = HistoricoDigitalSituacaoDiscente::getObjects($criteria9);                                       
                   
                    if(!$situacoes_discente)
                    {
                        $action40 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'É necessário preencher as situações do aluno ao longo do curso no histórico', $action40);    
                        die;    
                    }
                    else
                    {
                        $i = 0;
                       
                        foreach($situacoes_discente as $situacao_discente)
                        {
                            //São tags "vazias", sem campos dependentes
                            if(($situacao_discente->situacao_discente == "Trancamento") OR ($situacao_discente->situacao_discente == "MatriculadoEmDisciplina") OR 
                               ($situacao_discente->situacao_discente == "Licenca") OR ($situacao_discente->situacao_discente == "Desistencia") OR
                               ($situacao_discente->situacao_discente == "Abandono") OR ($situacao_discente->situacao_discente == "Jubilado"))
                            {
                                $lancamento_situacoes[$i]['situacao_discente'] = $situacao_discente->situacao_discente;
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->situacao_ano . '/' . $situacao_discente->situacao_semestre;
                            }                                                            
                            
                            elseif(($situacao_discente->situacao_discente == "IntercambioInternacional") OR ($situacao_discente->situacao_discente == "IntercambioNacional"))
                            {
                                $lancamento_situacoes[$i]['situacao_discente'] = $situacao_discente->situacao_discente;
                                $lancamento_situacoes[$i]['intercambio_instituicao'] = $situacao_discente->situacao_intercambio_instituicao;
                                $lancamento_situacoes[$i]['intercambio_programa'] = $situacao_discente->situacao_intercambio_programa;
                                $lancamento_situacoes[$i]['intercambio_pais'] = $situacao_discente->situacao_intercambio_pais;
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->situacao_ano . '/' . $situacao_discente->situacao_semestre;
                            }
                           
                            elseif($situacao_discente->situacao_discente == "Formado")
                            {
                                $lancamento_situacoes[$i]['situacao_discente'] = $situacao_discente->situacao_discente;
                                $lancamento_situacoes[$i]['formado_data_conclusao'] = $historico_digital->data_conclusao_curso;
                                $lancamento_situacoes[$i]['formado_data_colacao'] = $historico_digital->data_colacao_grau;
                                $lancamento_situacoes[$i]['formado_data_exp_diploma'] = $historico_digital->data_expedicao_diploma;
                                $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->situacao_ano . '/' . $situacao_discente->situacao_semestre;
                            }
                           
                            elseif($situacao_discente->situacao_discente == "OutraSituacao")
                            {
                               $lancamento_situacoes[$i]['situacao_discente'] = $situacao_discente->situacao_discente;
                               $lancamento_situacoes[$i]['descricao_situacao'] = $situacao_discente->situacao_outra;
                               $lancamento_situacoes[$i]['periodo_letivo'] = $situacao_discente->situacao_ano . '/' . $situacao_discente->situacao_semestre;
                            }
                           
                            else
                            {
                                $action41 = new TAction(array($formulario_origem, 'onReload'));                       
                                new TMessage('error', 'Verifique se todas as situações do aluno ao longo do curso foram preenchidas corretamente no histórico', $action41);    
                                die;
                            }
                           
                            $i++;                            
                        }
                    }   
                }
               
 
                if($lancamento_situacoes)
                {
                    foreach($lancamento_situacoes as $lancamento_situacao)
                    {
                        $situacao = (object) $lancamento_situacao;    
                       
                        if($situacao)
                        {
                            //Tipo de entrada
                            $noEntradaSituacao = $document->createElement('SituacaoDiscente');
                            $noElementosHistorico->appendChild($noEntradaSituacao);                            
                       
                       
                            //Período letivo
                            $noPeriodoLetivoSituacao = $document->createElement('PeriodoLetivo', trim($situacao->periodo_letivo));
                            $noEntradaSituacao->appendChild($noPeriodoLetivoSituacao);      
                        
                       
                            //Situação
                            //São tags "vazias", sem campos dependentes
                            if(($situacao->situacao_discente == "Trancamento") OR ($situacao->situacao_discente == "MatriculadoEmDisciplina") OR 
                               ($situacao->situacao_discente == "Licenca") OR ($situacao->situacao_discente == "Desistencia") OR
                               ($situacao->situacao_discente == "Abandono") OR ($situacao->situacao_discente == "Jubilado"))
                            {
                                $noSituacaoDiscente = $document->createElement(trim($situacao->situacao_discente));
                                $noEntradaSituacao->appendChild($noSituacaoDiscente);      
                            }
                             
                            elseif(($situacao->situacao_discente == 'IntercambioInternacional') OR ($situacao->situacao_discente == 'IntercambioNacional'))
                            {
                                //Verifica se a instituição, o programa e o país foram preenchidos
                                if((! $situacao->intercambio_instituicao) OR (! $situacao->intercambio_pais) OR (! $situacao->intercambio_programa))
                                {
                                   $action42 = new TAction(array($formulario_origem, 'onReload'));                       
                                   new TMessage('error', 'É necessário preencher todos os dados sobre o intercâmbio no histórico', $action42);    
                                   die;    
                                }
                                else
                                {
                                    $noSituacaoDiscente = $document->createElement(trim($situacao->situacao_discente));
                                    $noEntradaSituacao->appendChild($noSituacaoDiscente);     
                               
                                    $noIntercambioInstituicao = $document->createElement('Instituicao', trim($situacao->intercambio_instituicao));
                                    $noSituacaoDiscente->appendChild($noIntercambioInstituicao); 
                                    
                                    $noIntercambioPais = $document->createElement('Pais', trim($situacao->intercambio_pais));
                                    $noSituacaoDiscente->appendChild($noIntercambioPais); 
                                   
                                    $noIntercambioPrograma = $document->createElement('NomeProgramaIntercambio', trim($situacao->intercambio_programa));
                                    $noSituacaoDiscente->appendChild($noIntercambioPrograma); 
                                }                                   
                            }
                           
                            elseif($situacao->situacao_discente == 'Formado')
                            {
                                //Verifica se as datas de conclusão, colação e expedição do diploma foram lançadas
                                if((! $situacao->formado_data_conclusao) OR (! $situacao->formado_data_colacao) OR (! $situacao->formado_data_exp_diploma))
                                {
                                   $action43 = new TAction(array($formulario_origem, 'onReload'));                       
                                   new TMessage('error', 'É necessário preencher as datas de conclusão, colação e expedição do diploma no histórico', $action43);    
                                   die;    
                                }
                                else
                                { 
                                    $noSituacaoDiscente = $document->createElement(trim($situacao->situacao_discente));
                                    $noEntradaSituacao->appendChild($noSituacaoDiscente);     
                             
                                    $noDataConclusaoCurso = $document->createElement('DataConclusaoCurso', trim($situacao->formado_data_conclusao));
                                    $noSituacaoDiscente->appendChild($noDataConclusaoCurso); 
                                    
                                    $noDataColacaoGrau = $document->createElement('DataColacaoGrau', trim($situacao->formado_data_colacao));
                                    $noSituacaoDiscente->appendChild($noDataColacaoGrau); 
                                    
                                    $noDataExpedicaoDiploma = $document->createElement('DataExpedicaoDiploma', trim($situacao->formado_data_exp_diploma));
                                    $noSituacaoDiscente->appendChild($noDataExpedicaoDiploma);
                                }    
                            }
                            
                            elseif($situacao->situacao_discente == 'OutraSituacao')
                            {
                                $noSituacaoDiscente = $document->createElement('OutraSituacao', trim($situacao->descricao_situacao));
                                $noEntradaSituacao->appendChild($noSituacaoDiscente);     
                            }
                           
                            else
                            {
                                $action44 = new TAction(array($formulario_origem, 'onReload'));                       
                                new TMessage('error', 'Verifique se todas as situações do aluno ao longo do curso foram preenchidas corretamente no histórico', $action44);    
                                die;
                            }
                        }       
                    }
                } 
                //FIM - ELEMENTOS HISTÓRICO

           
             
                //INÍCIO - NOME PARA ÁREAS E ÁREAS
                if($curso->termo_referencia_area <> NULL)
                {
                    $noNomeParaAreas = $document->createElement('NomeParaAreas', trim($curso->termo_referencia_area));
                    $noHistoricoEscolar->appendChild($noNomeParaAreas);
             
                   
                    //Se o curso tiver áreas de formação, verifica se o aluno está vinculado a alguma
                    $areas_integralizadas = $historico_digital->areas_integralizadas_id;
                   
                    if($areas_integralizadas <> NULL)
                    {  
                        //Se o histórico estiver vinculado a um currículo
                        if($historico_digital->curriculo_id <> NULL)
                        {
                            /*Verifica se a(s) área(s) que será(ão) lançada(s) no histórico está(ão) presente(s) no currículo e certifica-se de usar o mesmo código.
                            Compara diretamente com o XML, pois pode ter sido incluído no sistema posteriormente*/
                            $caminho_curriculo = $curriculo_digital->caminho_arquivo . '/' . $curriculo_digital->arquivo;            
                            $xml_curriculo = simplexml_load_file($caminho_curriculo); 
                            
                            foreach($xml_curriculo->infCurriculoEscolar as $tags_curriculo) 
                            {
                                if($tags_curriculo->infAreas->Area)
                                {                                   
                                    $i = 0;
                                     
                                    //Áreas do currículo
                                    foreach($tags_curriculo->infAreas->Area as $tags_area)
                                    {
                                        $areas_curriculo[$i]['codigo'] = (string) $tags_area->Codigo;
                                        $areas_curriculo[$i]['nome'] = (string) $tags_area->Nome;
                                        
                                        $i++;
                                    } 
                                }
                                else
                                {
                                    $action45 = new TAction(array($formulario_origem, 'onReload'));                       
                                    new TMessage('error', 'O aluno integralizou uma área de formação que não está presente no currículo vinculado ao histórico', $action45);    
                                    die; 
                                }
                            }
                          
                          
                            //Áreas do histórico
                            $areas = explode(',', $areas_integralizadas);
                                   
                            foreach($areas as $id_area)
                            {
                                $area_formacao = new AreaFormacao($id_area);
                                       
                                $codigos_areas_historico[] = $area_formacao->codigo;
                            } 
                            
                           
                            $noAreas = $document->createElement('Areas');
                            $noHistoricoEscolar->appendChild($noAreas);
                           
                            $verificacao_codigos_curriculo = serialize($areas_curriculo);
                                 
                                       
                            foreach($codigos_areas_historico as $codigo_area_historico)
                            {
                                //Se o código da área integralizada pelo aluno estiver no currículo, adiciona
                                if(strpos($verificacao_codigos_curriculo, $codigo_area_historico) !== false)
                                {
                                    foreach($areas_curriculo as $area_curriculo)
                                    {
                                        if($codigo_area_historico == $area_curriculo['codigo'])
                                        {                                                   
                                            $noArea = $document->createElement('Area');
                                            $noAreas->appendChild($noArea);
                                                       
                                            $noCodigoArea = $document->createElement('Codigo', trim($area_curriculo['codigo']));
                                            $noArea->appendChild($noCodigoArea);
                                                       
                                            $noNomeArea = $document->createElement('Nome', trim($area_curriculo['nome']));
                                            $noArea->appendChild($noNomeArea);
                                        }       
                                    }        
                                }
                                else
                                {
                                    $action46 = new TAction(array($formulario_origem, 'onReload'));                       
                                    new TMessage('error', 'O aluno integralizou uma área de formação que não está presente no currículo vinculado ao histórico', $action46);    
                                    die;
                                }
                            }    
                        }
                       
                        //Se não estiver vinculado a um currículo, adiciona as áreas normalmente
                        else
                        {
                            $noAreas = $document->createElement('Areas');
                            $noHistoricoEscolar->appendChild($noAreas);
                        
                            $areas = explode(',', $areas_integralizadas);
                            
                            foreach($areas as $id_area)
                            {
                                $area_formacao = new AreaFormacao($id_area);
                                
                                if($area_formacao)
                                {          
                                    $noArea = $document->createElement('Area');
                                    $noAreas->appendChild($noArea);
                                      
                                    $noCodigoArea = $document->createElement('Codigo', trim($area_formacao->codigo));
                                    $noArea->appendChild($noCodigoArea);
                                        
                                    $noNomeArea = $document->createElement('Nome', trim($area_formacao->nome));
                                    $noArea->appendChild($noNomeArea);   
                                }     
                            }
                        } 
                    }
                    else
                    {
                        $action47 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'É necessário informar no histórico as áreas integralizadas pelo aluno ao longo do curso', $action47);    
                        die;    
                    }                  
                }
                //FIM - NOME PARA ÁREAS E ÁREAS
        
               

                //INÍCIO - DATA E HORA EMISSÃO HISTÓRICO               
                $data_exp_historico = substr($data_expedicao_historico, 0, 10);
                $hora_exp_historico = substr($data_expedicao_historico, 11, 8);
               
                //Verifica se a data de emissão do histórico "bate" com a versão do XSD vigente               
                 if(($data_exp_historico < $versao->versao_historico_inicio) OR ($data_exp_historico > $versao->versao_historico_termino))
                 {
                     $action48 = new TAction(array($formulario_origem, 'onReload'));                       
                     new TMessage('error', 'Verifique com o setor de TI a versão vigente do XSD para a data em que o histórico está sendo expedido', $action48);    
                     die;                    
                 }
                
                 if(($data_exp_historico <> NULL) AND ($hora_exp_historico <> NULL))
                 {                      
                    $noDataEmissaoHistorico = $document->createElement('DataEmissaoHistorico', trim($data_exp_historico));
                    $noHistoricoEscolar->appendChild($noDataEmissaoHistorico);
                    
                    $noHoraEmissaoHistorico = $document->createElement('HoraEmissaoHistorico', trim($hora_exp_historico));
                    $noHistoricoEscolar->appendChild($noHoraEmissaoHistorico);    
                 }
                else
                {
                    $action49 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'A data e a hora de expedição do histórico não foram gerados corretamente', $action49);    
                    die;
                }              
                //FIM - DATA E HORA EMISSÃO HISTÓRICO
              
     
                               
                //INÍCIO - SITUAÇÃO ATUAL DISCENTE (no momento da emissão do histórico)
                $noSituacaoAtualDiscente = $document->createElement('SituacaoAtualDiscente');
                $noHistoricoEscolar->appendChild($noSituacaoAtualDiscente);
               
               
                //Período letivo (em que ocorreu a emissão do histórico)    
                $emissao = new DateTime($data_expedicao_historico);
                $ano_emissao = $emissao->format('Y');
                $mes_emissao = $emissao->format('m');
                           
                if($mes_emissao < 7)
                {
                    $semestre_emissao = 1;
                }
                 elseif($mes_emissao >= 7)
                {
                    $semestre_emissao = 2;
                }
                
                
                //Ordenada por ano/semestre/etapa
                $ultima_situacao = (object) end($lancamento_situacoes);
                
                
                //Em um histórico final (quando o aluno formou e não o de transferência) a última situação do aluno deve ser Formado
                if($tipo_historico == "Final" OR $tipo_historico == "2ª via final")
                {                        
                    if($ultima_situacao->situacao_discente == 'Formado')
                    { 
                        $noSituacaoAtualDiscentePeriodoLetivo = $document->createElement('PeriodoLetivo', trim($ano_emissao . '/' . $semestre_emissao));
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscentePeriodoLetivo);
                        
                        $noSituacaoAtualDiscenteFormado = $document->createElement('Formado');
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscenteFormado); 
                                    
                        $noSituacaoAtualDiscenteDataConclusao = $document->createElement('DataConclusaoCurso', trim($historico_digital->data_conclusao_curso));
                        $noSituacaoAtualDiscenteFormado->appendChild($noSituacaoAtualDiscenteDataConclusao);
                        
                        $noSituacaoAtualDiscenteDataColacao = $document->createElement('DataColacaoGrau', trim($historico_digital->data_colacao_grau));
                        $noSituacaoAtualDiscenteFormado->appendChild($noSituacaoAtualDiscenteDataColacao); 
                        
                        $noSituacaoAtualDiscenteDataExpedicaoDiploma = $document->createElement('DataExpedicaoDiploma', trim($historico_digital->data_expedicao_diploma));
                        $noSituacaoAtualDiscenteFormado->appendChild($noSituacaoAtualDiscenteDataExpedicaoDiploma);
                    }
                    else
                    {
                        $action50 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se a última situação do aluno no histórico corresponde a Formado ou Concluída', $action50);    
                        die;
                    }
                } 
                else
                {
                    if(($ultima_situacao->situacao_discente == "Trancamento") OR ($ultima_situacao->situacao_discente == "MatriculadoEmDisciplina") OR 
                       ($ultima_situacao->situacao_discente == "Licenca") OR ($ultima_situacao->situacao_discente == "Desistencia") OR
                       ($ultima_situacao->situacao_discente == "Abandono") OR ($ultima_situacao->situacao_discente == "Jubilado"))
                    {
                        $noSituacaoAtualDiscentePeriodoLetivo = $document->createElement('PeriodoLetivo', trim($ano_emissao . '/' . $semestre_emissao));
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscentePeriodoLetivo);
                       
                        $noUltimaSituacaoDiscente = $document->createElement(trim($ultima_situacao->situacao_discente));
                        $noSituacaoAtualDiscente->appendChild($noUltimaSituacaoDiscente);      
                    }
                             
                    elseif(($ultima_situacao->situacao_discente == 'IntercambioInternacional') OR ($ultima_situacao->situacao_discente == 'IntercambioNacional'))
                    {
                        $noSituacaoAtualDiscentePeriodoLetivo = $document->createElement('PeriodoLetivo', trim($ano_emissao . '/' . $semestre_emissao));
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscentePeriodoLetivo);
                       
                        $noSituacaoAtualDiscenteIntercambio = $document->createElement(trim($ultima_situacao->situacao_discente));
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscenteIntercambio);     
                           
                        $noIntercambioInstituicao = $document->createElement('Instituicao', trim($ultima_situacao->intercambio_instituicao));
                        $noSituacaoAtualDiscenteIntercambio->appendChild($noIntercambioInstituicao); 
                                
                        $noIntercambioPais = $document->createElement('Pais', trim($ultima_situacao->intercambio_pais));
                        $noSituacaoAtualDiscenteIntercambio->appendChild($noIntercambioPais); 
                                
                        $noIntercambioPrograma = $document->createElement('NomeProgramaIntercambio', trim($ultima_situacao->intercambio_programa));
                        $noSituacaoAtualDiscenteIntercambio->appendChild($noIntercambioPrograma);                                
                    }
                            
                    elseif($ultima_situacao->situacao_discente == 'Formado')
                    {
                        $noSituacaoAtualDiscentePeriodoLetivo = $document->createElement('PeriodoLetivo', trim($ano_emissao . '/' . $semestre_emissao));
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscentePeriodoLetivo);
                        
                        $noSituacaoAtualDiscenteFormado = $document->createElement('Formado');
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscenteFormado); 
                                    
                        $noSituacaoAtualDiscenteDataConclusao = $document->createElement('DataConclusaoCurso', trim($historico_digital->data_conclusao_curso));
                        $noSituacaoAtualDiscenteFormado->appendChild($noSituacaoAtualDiscenteDataConclusao);
                        
                        $noSituacaoAtualDiscenteDataColacao = $document->createElement('DataColacaoGrau', trim($historico_digital->data_colacao_grau));
                        $noSituacaoAtualDiscenteFormado->appendChild($noSituacaoAtualDiscenteDataColacao); 
                        
                        $noSituacaoAtualDiscenteDataExpedicaoDiploma = $document->createElement('DataExpedicaoDiploma', trim($historico_digital->data_expedicao_diploma));
                        $noSituacaoAtualDiscenteFormado->appendChild($noSituacaoAtualDiscenteDataExpedicaoDiploma);
                    }
                            
                    elseif($ultima_situacao->situacao_discente == 'OutraSituacao')
                    {
                        $noSituacaoAtualDiscentePeriodoLetivo = $document->createElement('PeriodoLetivo', trim($ano_emissao . '/' . $semestre_emissao));
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscentePeriodoLetivo);
                        
                        $noSituacaoAtualDiscenteOutra = $document->createElement('OutraSituacao', trim($ultima_situacao->descricao_situacao));
                        $noSituacaoAtualDiscente->appendChild($noSituacaoAtualDiscenteOutra);     
                    }
                    
                    else
                    {
                        $action51 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique se todas as situações do aluno ao longo do curso foram preenchidas corretamente no histórico', $action51);    
                        die;
                    }        
                }   
                //FIM - SITUAÇÃO ATUAL DISCENTE
                          
                               
                    
                //INÍCIO - ENADE (Se for 2ª via e não tiver dados, não precisa incluir tag. Caso contrário, é preciso incluir mesmo que fique vazia) 
                if(($tipo_historico == "Parcial" OR $tipo_historico == "Transferência" OR $tipo_historico == "Final") OR
                  (($tipo_historico == "2ª via parcial" OR $tipo_historico == "2ª via transferência" OR $tipo_historico == "2ª via final") AND ($historico_digital->situacao_enade1 <> NULL OR $historico_digital->situacao_enade2 <> NULL)))
                {
                    $noEnade = $document->createElement('ENADE');
                    $noHistoricoEscolar->appendChild($noEnade);
                }
                
                if($noEnade <> NULL)
                {
                    //Enade 1 (a obrigatoriedade dos campos já foi testada no formulário de origem)
                    if($historico_digital->situacao_enade1 == "Habilitado")
                    {               
                        $noHabilitado = $document->createElement('Habilitado');
                        $noEnade->appendChild($noHabilitado);
                        
                        $noCondicao = $document->createElement('Condicao', trim($historico_digital->situacao_enade1_condicao));
                        $noEdicao = $document->createElement('Edicao', trim($historico_digital->situacao_enade1_edicao));
                       
                        $noHabilitado->appendChild($noCondicao);
                        $noHabilitado->appendChild($noEdicao);                        
                    }
                   
                    if($historico_digital->situacao_enade1 == "Irregular")
                    {
                        $noIrregular = $document->createElement('Irregular');
                        $noEnade->appendChild($noIrregular);
                       
                        $noCondicao = $document->createElement('Condicao', trim($historico_digital->situacao_enade1_condicao));
                        $noEdicao = $document->createElement('Edicao', trim($historico_digital->situacao_enade1_edicao));
                        
                        $noIrregular->appendChild($noCondicao);
                        $noIrregular->appendChild($noEdicao);  
                    }
                   
                    if($historico_digital->situacao_enade1 == "NaoHabilitado")
                    {
                        $noNaoHabilitado = $document->createElement('NaoHabilitado');
                        $noEnade->appendChild($noNaoHabilitado);
                        
                        $noCondicao = $document->createElement('Condicao', trim($historico_digital->situacao_enade1_condicao));
                        $noEdicao = $document->createElement('Edicao', trim($historico_digital->situacao_enade1_edicao));
                        
                        $noNaoHabilitado->appendChild($noCondicao);
                        $noNaoHabilitado->appendChild($noEdicao);
                        
                        if($historico_digital->situacao_enade1_opcao_motivo == "Utiliza motivo listado pelo MEC")
                        {
                            $noMotivo = $document->createElement('Motivo', trim($historico_digital->situacao_enade1_motivo));
                            $noNaoHabilitado->appendChild($noMotivo);
                        }
                        
                        if($historico_digital->situacao_enade1_opcao_motivo == "Utiliza motivo não listado pelo MEC")
                        {
                            $noOutroMotivo = $document->createElement('OutroMotivo', trim($historico_digital->situacao_enade1_outro_motivo));
                            $noNaoHabilitado->appendChild($noOutroMotivo);
                        }                             
                    }
                   
                   
                    //Enade 2 (a obrigatoriedade dos campos já foi testada no formulário de origem)
                    if($historico_digital->situacao_enade2 == "Habilitado")
                    {               
                        $noHabilitado = $document->createElement('Habilitado');
                        $noEnade->appendChild($noHabilitado);
                       
                        $noCondicao = $document->createElement('Condicao', trim($historico_digital->situacao_enade2_condicao));
                        $noEdicao = $document->createElement('Edicao', trim($historico_digital->situacao_enade2_edicao));
                       
                        $noHabilitado->appendChild($noCondicao);
                        $noHabilitado->appendChild($noEdicao);                        
                    }
                   
                    if($historico_digital->situacao_enade2 == "Irregular")
                    {
                        $noIrregular = $document->createElement('Irregular');
                        $noEnade->appendChild($noIrregular);
                        
                        $noCondicao = $document->createElement('Condicao', trim($historico_digital->situacao_enade2_condicao));
                        $noEdicao = $document->createElement('Edicao', trim($historico_digital->situacao_enade2_edicao));
                        
                        $noIrregular->appendChild($noCondicao);
                        $noIrregular->appendChild($noEdicao);  
                    }
                    
                    if($historico_digital->situacao_enade2 == "NaoHabilitado")
                    {
                        $noNaoHabilitado = $document->createElement('NaoHabilitado');
                        $noEnade->appendChild($noNaoHabilitado);
                       
                        $noCondicao = $document->createElement('Condicao', trim($historico_digital->situacao_enade2_condicao));
                        $noEdicao = $document->createElement('Edicao', trim($historico_digital->situacao_enade2_edicao));
                        
                        $noNaoHabilitado->appendChild($noCondicao);
                        $noNaoHabilitado->appendChild($noEdicao);
                        
                        if($historico_digital->situacao_enade2_opcao_motivo == "Utiliza motivo listado pelo MEC")
                        {
                            $noMotivo = $document->createElement('Motivo', trim($historico_digital->situacao_enade2_motivo));
                            $noNaoHabilitado->appendChild($noMotivo);
                        }
                       
                        if($historico_digital->situacao_enade2_opcao_motivo == "Utiliza motivo não listado pelo MEC")
                        {
                            $noOutroMotivo = $document->createElement('OutroMotivo', trim($historico_digital->situacao_enade2_outro_motivo));
                            $noNaoHabilitado->appendChild($noOutroMotivo);
                        }                             
                    }
                }   
                //FIM - ENADE
              
              

                //INÍCIO - CH INTEGRALIZADA
                if($tipo_historico == "Final" OR $tipo_historico == "2ª via final")
                {
                    //Se vinculado a um currículo (1ª via final), verifica se cumpriu os critérios de integralização
                    if($historico_digital->curriculo_id <> NULL)
                    {
                        $criterios = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $historico_digital->curriculo_id)->load();
                        
                        foreach($criterios as $criterio)
                        {
                            if($criterio->tipo_unidade == "Atividade Complementar")
                            {
                                $ch_criterio_atividades += $criterio->ch_minima_hora_relogio;
                            }                            
                            elseif($criterio->tipo_unidade == "Estágio")
                            {
                                $ch_criterio_estagios += $criterio->ch_minima_hora_relogio;
                            }  
                            else
                            {
                                //Disciplina, TCC (entra como disciplina)
                                $ch_criterio_outros += $criterio->ch_minima_hora_relogio;    
                            }  
                        }
                        
                        if(($ch_integralizada_atividades < $ch_criterio_atividades)OR ($ch_integralizada_estagios < $ch_criterio_estagios) OR ($ch_integralizada_disciplinas < $ch_criterio_outros))
                        {
                            $action52 = new TAction(array($formulario_origem, 'onReload'));                       
                            new TMessage('error', 'Verifique se o aluno integralizou o mínimo de horas exigidas para disciplinas, atividades complementares e estágios', $action52);    
                            die;
                        }
                        else
                        {
                            $noCHintegralizada = $document->createElement('CargaHorariaCursoIntegralizada');
                            $noHistoricoEscolar->appendChild($noCHintegralizada);
                                    
                            $noCHintegralizada_HoraRelogio = $document->createElement('HoraRelogio', trim(number_format($CH_integralizada, 2, '.', '')));
                            $noCHintegralizada->appendChild($noCHintegralizada_HoraRelogio);
                        } 
                    }
                    
                    //Se não estiver vinculado a um currículo (2ª via final), verifica somente se atingiu a carga horária do curso 
                    else
                    {
                        if($CH_integralizada >= $historico_digital->ch_total_curso)
                        {
                            $noCHintegralizada = $document->createElement('CargaHorariaCursoIntegralizada');
                            $noHistoricoEscolar->appendChild($noCHintegralizada);
                                    
                            $noCHintegralizada_HoraRelogio = $document->createElement('HoraRelogio', trim(number_format($CH_integralizada, 2, '.', '')));
                            $noCHintegralizada->appendChild($noCHintegralizada_HoraRelogio);
                        }
                        else
                        {
                            $action53 = new TAction(array($formulario_origem, 'onReload'));                       
                            new TMessage('error', 'A carga horária integralizada pelo aluno não pode ser menor que a carga horária do curso', $action53);    
                            die;
                        }
                    }    
                } 
                else
                {
                    if($CH_integralizada <> NULL)
                    {
                        $noCHintegralizada = $document->createElement('CargaHorariaCursoIntegralizada');
                        $noHistoricoEscolar->appendChild($noCHintegralizada);
                                
                        $noCHintegralizada_HoraRelogio = $document->createElement('HoraRelogio', trim(number_format($CH_integralizada, 2, '.', '')));
                        $noCHintegralizada->appendChild($noCHintegralizada_HoraRelogio);
                    }
                    else
                    {
                        $action54 = new TAction(array($formulario_origem, 'onReload'));                       
                        new TMessage('error', 'Verifique a carga horária integralizada pelo aluno', $action54);    
                        die;
                    }
                }  
                //FIM - CARGA HORÁRIA INTEGRALIZADA
                                        
 
 
                //INÍCIO - CARGA HORÁRIA CURSO
                if($historico_digital->ch_total_curso <> NULL)
                {
                    $noChCurso = $document->createElement('CargaHorariaCurso');
                    $noHistoricoEscolar->appendChild($noChCurso);
                               
                    $noChCurso_HoraRelogio = $document->createElement('HoraRelogio', trim(number_format($historico_digital->ch_total_curso, 2, '.', '')));
                    $noChCurso->appendChild($noChCurso_HoraRelogio);
                }
                else
                {
                    $action55 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'Verifique se a carga horária total do curso foi inserida no histórico', $action55);    
                    die;
                }
                //FIM - CARGA HORÁRIA CURSO
                
                
                
                //INÍCIO - INGRESSO CURSO  
                $noIngressoCurso = $document->createElement('IngressoCurso');
                $noHistoricoEscolar->appendChild($noIngressoCurso);
                
                if($historico_digital->data_ingresso <> NULL)
                {
                    $noDataIngresso = $document->createElement('Data', trim($historico_digital->data_ingresso));
                    $noIngressoCurso->appendChild($noDataIngresso);
                }
                else
                {
                    $action56 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'É necessário preencher a data de ingresso do aluno na instituição', $action56);    
                    die;
                }
                 
                if($historico_digital->forma_acesso <> NULL)
                {
                    $noFormaAcesso = $document->createElement('FormaAcesso', trim($historico_digital->forma_acesso));
                    $noIngressoCurso->appendChild($noFormaAcesso);
                }
                else
                {
                    $action57 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'É necessário preencher a forma de ingresso do aluno na instituição', $action57);    
                    die;
                }     
                
                //Somente em caso de 2ª via e se forem preenchidos, adiciona o mês e ano do processo seletivo
                if($tipo_historico == "2ª via parcial" OR $tipo_historico == "2ª via transferência" OR $tipo_historico == "2ª via final")
                {
                    if(($historico_digital->ano_processo_seletivo <> NULL) AND ($historico_digital->mes_processo_seletivo <> NULL))
                    {
                        $ano_mes = $historico_digital->ano_processo_seletivo . "-" . $historico_digital->mes_processo_seletivo;
                        
                        $noAnoMesProcessoSeletivo = $document->createElement('AnoMesProcessoSeletivo', trim($ano_mes));
                        $noIngressoCurso->appendChild($noAnoMesProcessoSeletivo);    
                    }
                }       
                //FIM - INGRESSO CURSO
    
                //FIM - HISTÓRICO ESCOLAR                    



                //INÍCIO - SEGURANÇA HISTÓRICO
                $noSegurancaHistorico = $document->createElement('SegurancaHistorico');
                $noInfHistoricoEscolar->appendChild($noSegurancaHistorico);
 
                if($codigo_validacao <> NULL)
                {
                    $noCodigoValidacao = $document->createElement('CodigoValidacao', trim($codigo_validacao));
                    $noSegurancaHistorico->appendChild($noCodigoValidacao);
                }
                else
                {
                    $action58 = new TAction(array($formulario_origem, 'onReload'));                       
                    new TMessage('error', 'O código de validação do histórico não foi gerado corretamente. Por favor, repita o processo', $action58);    
                    die;    
                }
                //FIM - SEGURANÇA HISTÓRICO
               
               
                //ÍNICIO - INFORMAÇÕES ADICIONAIS
                if($historico_digital->informacoes_adicionais <> NULL)
                {
                    $noInformacoesAdicionais = $document->createElement('InformacoesAdicionais', trim(preg_replace("/\r|\n|&nbsp/", " ", $historico_digital->informacoes_adicionais)));
                    $noInfHistoricoEscolar->appendChild($noInformacoesAdicionais);    
                }
                //FIM - INFORMAÇÕES ADICIONAIS


                //INÍCIO - CONFRONTA O XML COM O XSD
                libxml_use_internal_errors(true);
                        
                $document->loadXML($document->saveXML());
                //$document->schemaValidate("http://dev.feituverava.com.br/mec/HistoricoEscolarDigital_v" . $versao->versao_historico . ".xsd");
                
                //Alteração do caminho dos XSDs para a pasta do acadêmico por erro ao tentar acesso externo
                $document->schemaValidate("./public/mec/HistoricoEscolarDigital_v" . $versao->versao_historico . ".xsd");
                
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
                            
                            $pos_action = new TAction(array($formulario_origem, 'onReload'));
                            new TMessage('error', $message_translate, $pos_action); 
                            die;
                        }
                    }	
                }
                //FIM - CONFRONTA O XML COM O XSD
                

               
                $document->save($target_file); 
               

                $historico_digital->tipo_historico = $tipo_historico;                 
                $historico_digital->dados_versao_id = $versao->id;
                $historico_digital->data_expedicao_historico = $data_expedicao_historico;
                $historico_digital->status_xml = 1; //1 - Gerado
                $historico_digital->status_assinatura_secretaria = 0; //0 - Não preechida / 1 - Preenchida
                $historico_digital->data_exp_certificado_secretaria = '';
                $historico_digital->status_assinatura_diretor = 0; //0 - Não preechida / 1 - Preenchida
                $historico_digital->data_exp_certificado_diretor = '';
                $historico_digital->status_assinatura_emissora = 0; //0 - Não preechida / 1 - Preenchida
                $historico_digital->data_exp_certificado_emissora = '';
                $historico_digital->codigo_validacao = $codigo_validacao;
                $historico_digital->url_historico = '';
                $historico_digital->qrcode = '';
                $historico_digital->caminho_qrcode = '';
                $historico_digital->arquivo = 'historico-' . $codigo_validacao . '.xml';
                $historico_digital->caminho_arquivo = $target_path;
                $historico_digital->arquivo_pdf = '';
                $historico_digital->caminho_pdf = '';
                $historico_digital->status_assinatura_pdf = 0;
                $historico_digital->status_publicacao = 0;
                $historico_digital->data_publicacao = '';
                $historico_digital->system_user_id = TSession::getValue('userid');
                $historico_digital->data_reg = date('Y-m-d H:i:s'); 
 
                $historico_digital->store();                               
                                                                               
                TTransaction::close();
                   
                new TMessage('info', 'XML gerado com sucesso');
                                   
                TApplication::loadPage($formulario_origem, 'onReload');
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