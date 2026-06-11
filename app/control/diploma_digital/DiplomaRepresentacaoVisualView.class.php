<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;


class DiplomaRepresentacaoVisualView extends TPage
{
    private $html;    
    
    public function __construct($param)
    {
        parent::__construct();
        
        try
        {
            TTransaction::open('Felabs_DB');
         
            $id_diploma = $param['id_diploma'];
                
            $diploma = new DiplomaDigitalDiploma($id_diploma);
            $emissora = new DiplomaDigitalEmissora($diploma->dados_emissora_id);
            
            TTransaction::close();


            //FFCL E NEAD
            if($emissora->system_unit_id == 2 OR $emissora->system_unit_id == 6) 
            {
                $this->html = new THtmlRenderer('app/resources/RepresentacaoDiplomaFFCL.html');
                
                $panel = new TPanelGroup('Representação Visual do Diploma - FFCL');
            }
            
            //FAFRAM
            if($emissora->system_unit_id == 3)
            {
                $this->html = new THtmlRenderer('app/resources/RepresentacaoDiplomaFAFRAM.html');
                
                $panel = new TPanelGroup('Representação Visual do Diploma - FAFRAM');
            }
            
            //FAJOB - falta imagem
                        

            $panel->addHeaderActionLink('Voltar', new TAction([$this, 'onBackList']), 'fa:arrow-left black');
            $panel->addHeaderActionLink('Salvar PDF', new TAction([$this, 'onDownload'], ['id_diploma' => $diploma->id]), 'far:file-pdf red');
            $panel->addHeaderActionLink('Publicar diploma e notificar aluno', new TAction([$this, 'onQuestionPublicarDiploma'], ['id_diploma' => $diploma->id]), 'fa:check green');
            
            $panel->getBody()->style = "overflow-x:auto;"; //ativa scroll horizontal
            $panel->style = 'width: 1280px;max-width: 1280px';
            
            $panel->add($this->html);
            

            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add($panel);
            
            
            parent::add($vbox);
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
            TTransaction::open('Felabs_DB');
            
            $id_diploma = $param['id_diploma'];

            $dados_diploma = new DiplomaDigitalDiploma($id_diploma);

            //Passo 1: Lê o xml (para garantir que os dados estejam iguais) e salva em uma variável todas as informações que compõem a representação visual
            $target_file = $dados_diploma->caminho_arquivo_registrado . '/' . $dados_diploma->arquivo_registrado;
            
            $xml_diplomado = simplexml_load_file($target_file);                


            $dados_representacao = new StdClass();
                        
            foreach($xml_diplomado->infDiploma as $tags_dados_diploma)
            {   
                if($tags_dados_diploma->DadosDiploma)
                {
                    //Percorre a tag DadosDiploma (em casos de 1ª ou 2ª via)
                    foreach($tags_dados_diploma->DadosDiploma as $tag_dados_diploma)
                    {
                        //DIPLOMADO
                        foreach($tag_dados_diploma->Diplomado as $dados_diplomado)
                        {
                            $dados_representacao->NomeDiplomado = (string) $dados_diplomado->Nome;
                            $dados_representacao->Sexo = (string) $dados_diplomado->Sexo;
                            $dados_representacao->Nacionalidade = (string) $dados_diplomado->Nacionalidade;
                            
                            foreach($dados_diplomado->Naturalidade as $dados_naturalidade)
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
                           
                            if($dados_diplomado->RG)
                            {
                                foreach($dados_diplomado->RG as $dados_documento_identificacao)
                                {
                                    $dados_representacao->RgNumero = (string) $dados_documento_identificacao->Numero;
                                    $dados_representacao->RgOrgaoExpedidor = (string) $dados_documento_identificacao->OrgaoExpedidor;
                                    $dados_representacao->RgUf = (string) $dados_documento_identificacao->UF;
                                }
                            }
                            else
                            {
                                foreach($dados_diplomado->OutroDocumentoIdentificacao as $dados_documento_identificacao)
                                {
                                    $dados_representacao->DocTipo = (string) $dados_documento_identificacao->TipoDocumento;
                                    $dados_representacao->DocIdentificador = (string) $dados_documento_identificacao->Identificador;
                                }
                            }
                            
                            $dados_representacao->DataNascimento = (string) $dados_diplomado->DataNascimento;
                        }                        
                                            
                        //DATA DE CONCLUSÃO
                        $dados_representacao->DataConclusaoCurso = (string) $tag_dados_diploma->DataConclusao;
        
                        //CURSO
                        foreach($tag_dados_diploma->DadosCurso as $dados_curso)
                        {
                            $dados_representacao->NomeCurso = (string) $dados_curso->NomeCurso;  
                            
                            foreach($dados_curso->TituloConferido as $dados_titulo)
                            {
                                if($dados_titulo->Titulo)
                                {
                                    $dados_representacao->TituloConferido = (string) $dados_titulo->Titulo;
                                }
                                else
                                {
                                    $dados_representacao->TituloConferido = (string) $dados_titulo->OutroTitulo;
                                }
                            }
                            
                            $dados_representacao->GrauConferido = (string) $dados_curso->GrauConferido;
                            
                            if($dados_curso->RenovacaoReconhecimento)
                            {
                                $dados_representacao->EmecCurso = "Renovação de reconhecimento";
                                
                                foreach($dados_curso->RenovacaoReconhecimento as $dados_renovacao_curso)
                                {
                                    if($dados_renovacao_curso->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecCursoTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_renovacao_curso->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao_curso)
                                        {                                                                                           
                                            $dados_representacao->RenovacaoCursoNumeroProcesso = (string) $dados_tramitacao_renovacao_curso->NumeroProcesso;
                                            $dados_representacao->RenovacaoCursoTipoProcesso = (string) $dados_tramitacao_renovacao_curso->TipoProcesso;
                                            $dados_representacao->RenovacaoCursoDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao_curso->DataCadastro);
                                            $dados_representacao->RenovacaoCursoDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao_curso->DataProtocolo); 
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
                                        
                                        foreach($dados_reconhecimento_curso->InformacoesTramitacaoEMEC as $dados_tramitacao_reconhecimento_curso)
                                        {                                                                                        
                                            $dados_representacao->ReconhecimentoCursoNumeroProcesso = (string) $dados_tramitacao_reconhecimento_curso->NumeroProcesso;
                                            $dados_representacao->ReconhecimentoCursoTipoProcesso = (string) $dados_tramitacao_reconhecimento_curso->TipoProcesso;
                                            $dados_representacao->ReconhecimentoCursoDataCadastro = (string) TDate::date2br($dados_tramitacao_reconhecimento_curso->DataCadastro);
                                            $dados_representacao->ReconhecimentoCursoDataProtocolo = (string) TDate::date2br($dados_tramitacao_reconhecimento_curso->DataProtocolo); 
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
                        foreach($tag_dados_diploma->IesEmissora as $dados_emissora)
                        {
                            $dados_representacao->NomeEmissora = (string) $dados_emissora->Nome;  
                            
                            if($dados_emissora->RenovacaoDeRecredenciamento)
                            {
                                $dados_representacao->EmecEmissora = "Renovação de recredenciamento";
                                
                                foreach($dados_emissora->RenovacaoDeRecredenciamento as $dados_renovacao_emissora)
                                {
                                    if($dados_renovacao_emissora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecEmissoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_renovacao_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao_emissora)
                                        {                                                                                       
                                            $dados_representacao->RenovacaoEmissoraNumeroProcesso = (string) $dados_tramitacao_renovacao_emissora->NumeroProcesso;
                                            $dados_representacao->RenovacaoEmissoraTipoProcesso = (string) $dados_tramitacao_renovacao_emissora->TipoProcesso;
                                            $dados_representacao->RenovacaoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao_emissora->DataCadastro);
                                            $dados_representacao->RenovacaoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao_emissora->DataProtocolo); 
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
                                        
                                        foreach($dados_recredenciamento_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_recredenciamento_emissora)
                                        {
                                            $dados_representacao->RecredenciamentoEmissoraNumeroProcesso = (string) $dados_tramitacao_recredenciamento_emissora->NumeroProcesso;
                                            $dados_representacao->RecredenciamentoEmissoraTipoProcesso = (string) $dados_tramitacao_recredenciamento_emissora->TipoProcesso;
                                            $dados_representacao->RecredenciamentoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_recredenciamento_emissora->DataCadastro);
                                            $dados_representacao->RecredenciamentoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_recredenciamento_emissora->DataProtocolo); 
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
                                        
                                        foreach($dados_credenciamento_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_credenciamento_emissora)
                                        {                                           
                                            $dados_representacao->CredenciamentoEmissoraNumeroProcesso = (string) $dados_tramitacao_credenciamento_emissora->NumeroProcesso;
                                            $dados_representacao->CredenciamentoEmissoraTipoProcesso = (string) $dados_tramitacao_credenciamento_emissora->TipoProcesso;
                                            $dados_representacao->CredenciamentoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_credenciamento_emissora->DataCadastro);
                                            $dados_representacao->CredenciamentoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_credenciamento_emissora->DataProtocolo); 
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
    
                            //MANTENEDORA
                            foreach($dados_emissora->Mantenedora as $dados_mantenedora)
                            {
                                $dados_representacao->NomeMantenedora = (string) $dados_mantenedora->RazaoSocial;  
                                $dados_representacao->CnpjMantenedora = (string) $dados_mantenedora->CNPJ;    
                            }   
                        }
                    }
                }
                else
                {
                    //Percorre a tag DadosDiplomaPorDecisaoJudicial (em casos de emissão por decisão judicial)
                    foreach($tags_dados_diploma->DadosDiplomaPorDecisaoJudicial as $tag_dados_diploma)
                    {
                        //DIPLOMADO
                        foreach($tag_dados_diploma->Diplomado as $dados_diplomado)
                        {
                            $dados_representacao->NomeDiplomado = (string) $dados_diplomado->Nome;
                            $dados_representacao->Sexo = (string) $dados_diplomado->Sexo;
                            $dados_representacao->Nacionalidade = (string) $dados_diplomado->Nacionalidade;
                            
                            foreach($dados_diplomado->Naturalidade as $dados_naturalidade)
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
                           
                            if($dados_diplomado->RG)
                            {
                                foreach($dados_diplomado->RG as $dados_documento_identificacao)
                                {
                                    $dados_representacao->RgNumero = (string) $dados_documento_identificacao->Numero;
                                    $dados_representacao->RgOrgaoExpedidor = (string) $dados_documento_identificacao->OrgaoExpedidor;
                                    $dados_representacao->RgUf = (string) $dados_documento_identificacao->UF;
                                }
                            }
                            else
                            {
                                foreach($dados_diplomado->OutroDocumentoIdentificacao as $dados_documento_identificacao)
                                {
                                    $dados_representacao->DocTipo = (string) $dados_documento_identificacao->TipoDocumento;
                                    $dados_representacao->DocIdentificador = (string) $dados_documento_identificacao->Identificador;
                                }
                            }
                            
                            $dados_representacao->DataNascimento = (string) $dados_diplomado->DataNascimento;
                        }                        
                                            
                        //DATA DE CONCLUSÃO
                        $dados_representacao->DataConclusaoCurso = (string) $tag_dados_diploma->DataConclusao;
        
                        //CURSO
                        foreach($tag_dados_diploma->DadosCurso as $dados_curso)
                        {
                            $dados_representacao->NomeCurso = (string) $dados_curso->NomeCurso;  
                            
                            foreach($dados_curso->TituloConferido as $dados_titulo)
                            {
                                if($dados_titulo->Titulo)
                                {
                                    $dados_representacao->TituloConferido = (string) $dados_titulo->Titulo;
                                }
                                else
                                {
                                    $dados_representacao->TituloConferido = (string) $dados_titulo->OutroTitulo;
                                }
                            }
                            
                            $dados_representacao->GrauConferido = (string) $dados_curso->GrauConferido;
                            
                            if($dados_curso->RenovacaoReconhecimento)
                            {
                                $dados_representacao->EmecCurso = "Renovação de reconhecimento";
                                
                                foreach($dados_curso->RenovacaoReconhecimento as $dados_renovacao_curso)
                                {
                                    if($dados_renovacao_curso->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecCursoTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_renovacao_curso->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao_curso)
                                        {                                                                                           
                                            $dados_representacao->RenovacaoCursoNumeroProcesso = (string) $dados_tramitacao_renovacao_curso->NumeroProcesso;
                                            $dados_representacao->RenovacaoCursoTipoProcesso = (string) $dados_tramitacao_renovacao_curso->TipoProcesso;
                                            $dados_representacao->RenovacaoCursoDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao_curso->DataCadastro);
                                            $dados_representacao->RenovacaoCursoDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao_curso->DataProtocolo); 
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
                                        
                                        foreach($dados_reconhecimento_curso->InformacoesTramitacaoEMEC as $dados_tramitacao_reconhecimento_curso)
                                        {                                                                                        
                                            $dados_representacao->ReconhecimentoCursoNumeroProcesso = (string) $dados_tramitacao_reconhecimento_curso->NumeroProcesso;
                                            $dados_representacao->ReconhecimentoCursoTipoProcesso = (string) $dados_tramitacao_reconhecimento_curso->TipoProcesso;
                                            $dados_representacao->ReconhecimentoCursoDataCadastro = (string) TDate::date2br($dados_tramitacao_reconhecimento_curso->DataCadastro);
                                            $dados_representacao->ReconhecimentoCursoDataProtocolo = (string) TDate::date2br($dados_tramitacao_reconhecimento_curso->DataProtocolo); 
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
                        foreach($tag_dados_diploma->IesEmissora as $dados_emissora)
                        {
                            $dados_representacao->NomeEmissora = (string) $dados_emissora->Nome;  
                            
                            if($dados_emissora->RenovacaoDeRecredenciamento)
                            {
                                $dados_representacao->EmecEmissora = "Renovação de recredenciamento";
                                
                                foreach($dados_emissora->RenovacaoDeRecredenciamento as $dados_renovacao_emissora)
                                {
                                    if($dados_renovacao_emissora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecEmissoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_renovacao_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao_emissora)
                                        {                                                                                       
                                            $dados_representacao->RenovacaoEmissoraNumeroProcesso = (string) $dados_tramitacao_renovacao_emissora->NumeroProcesso;
                                            $dados_representacao->RenovacaoEmissoraTipoProcesso = (string) $dados_tramitacao_renovacao_emissora->TipoProcesso;
                                            $dados_representacao->RenovacaoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao_emissora->DataCadastro);
                                            $dados_representacao->RenovacaoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao_emissora->DataProtocolo); 
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
                                        
                                        foreach($dados_recredenciamento_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_recredenciamento_emissora)
                                        {
                                            $dados_representacao->RecredenciamentoEmissoraNumeroProcesso = (string) $dados_tramitacao_recredenciamento_emissora->NumeroProcesso;
                                            $dados_representacao->RecredenciamentoEmissoraTipoProcesso = (string) $dados_tramitacao_recredenciamento_emissora->TipoProcesso;
                                            $dados_representacao->RecredenciamentoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_recredenciamento_emissora->DataCadastro);
                                            $dados_representacao->RecredenciamentoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_recredenciamento_emissora->DataProtocolo); 
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
                                        
                                        foreach($dados_credenciamento_emissora->InformacoesTramitacaoEMEC as $dados_tramitacao_credenciamento_emissora)
                                        {                                           
                                            $dados_representacao->CredenciamentoEmissoraNumeroProcesso = (string) $dados_tramitacao_credenciamento_emissora->NumeroProcesso;
                                            $dados_representacao->CredenciamentoEmissoraTipoProcesso = (string) $dados_tramitacao_credenciamento_emissora->TipoProcesso;
                                            $dados_representacao->CredenciamentoEmissoraDataCadastro = (string) TDate::date2br($dados_tramitacao_credenciamento_emissora->DataCadastro);
                                            $dados_representacao->CredenciamentoEmissoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_credenciamento_emissora->DataProtocolo); 
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
    
                            //MANTENEDORA
                            foreach($dados_emissora->Mantenedora as $dados_mantenedora)
                            {
                                $dados_representacao->NomeMantenedora = (string) $dados_mantenedora->RazaoSocial;  
                                $dados_representacao->CnpjMantenedora = (string) $dados_mantenedora->CNPJ;    
                            }   
                        }
                    }
                }
                
                
                if($tags_dados_diploma->DadosRegistro)
                {
                    //Percorre a tag DadosRegistro
                    foreach($tags_dados_diploma->DadosRegistro as $tag_dados_registro)
                    {
                        foreach($tag_dados_registro->IesRegistradora as $dados_registradora)
                        {
                            $dados_representacao->NomeRegistradora = (string) $dados_registradora->Nome;
                            
                            if($dados_registradora->RenovacaoDeRecredenciamento)
                            {
                                $dados_representacao->EmecRegistradora = "Renovação de recredenciamento";
                                
                                foreach($dados_registradora->RenovacaoDeRecredenciamento as $dados_renovacao_registradora)
                                {
                                    if($dados_renovacao_registradora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecRegistradoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_renovacao_registradora->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao_registradora)
                                        {
                                            $dados_representacao->RenovacaoRegistradoraNumeroProcesso = (string) $dados_tramitacao_renovacao_registradora->NumeroProcesso;
                                            $dados_representacao->RenovacaoRegistradoraTipoProcesso = (string) $dados_tramitacao_renovacao_registradora->TipoProcesso;
                                            $dados_representacao->RenovacaoRegistradoraDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao_registradora->DataCadastro);
                                            $dados_representacao->RenovacaoRegistradoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao_registradora->DataProtocolo); 
                                        }
                                    }
                                    else
                                    {
                                        $dados_representacao->EmecRegistradoraAtoRegulatorio = "Ato regulatório";
                                        
                                        $dados_representacao->RenovacaoRegistradoraTipo = (string) $dados_renovacao_registradora->Tipo;
                                        $dados_representacao->RenovacaoRegistradoraNumero = (string) $dados_renovacao_registradora->Numero;
                                        $dados_representacao->RenovacaoRegistradoraData = (string) TDate::date2br($dados_renovacao_registradora->Data);
                                        $dados_representacao->RenovacaoRegistradoraVeiculoPublicacao = (string) $dados_renovacao_registradora->VeiculoPublicacao;
                                        $dados_representacao->RenovacaoRegistradoraDataPublicacao = (string) TDate::date2br($dados_renovacao_registradora->DataPublicacao);
                                        $dados_representacao->RenovacaoRegistradoraSecaoPublicacao = (string) $dados_renovacao_registradora->SecaoPublicacao;
                                        $dados_representacao->RenovacaoRegistradoraPaginaPublicacao = (string) $dados_renovacao_registradora->PaginaPublicacao;
                                        $dados_representacao->RenovacaoRegistradoraNumeroDOU = (string) $dados_renovacao_registradora->NumeroDOU;
                                    }
                                }
                            }
                            elseif($dados_registradora->Recredenciamento)
                            {
                                $dados_representacao->EmecRegistradora = "Recredenciamento";
                                
                                foreach($dados_registradora->Recredenciamento as $dados_recredenciamento_registradora)
                                {
                                    if($dados_recredenciamento_registradora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecRegistradoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_recredenciamento_registradora->InformacoesTramitacaoEMEC as $dados_tramitacao_recredenciamento_registradora)
                                        {
                                            $dados_representacao->RecredenciamentoRegistradoraNumeroProcesso = (string) $dados_tramitacao_recredenciamento_registradora->NumeroProcesso;
                                            $dados_representacao->RecredenciamentoRegistradoraTipoProcesso = (string) $dados_tramitacao_recredenciamento_registradora->TipoProcesso;
                                            $dados_representacao->RecredenciamentoRegistradoraDataCadastro = (string) TDate::date2br($dados_tramitacao_recredenciamento_registradora->DataCadastro);
                                            $dados_representacao->RecredenciamentoRegistradoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_recredenciamento_registradora->DataProtocolo); 
                                        }
                                    }
                                    else
                                    {
                                        $dados_representacao->EmecRegistradoraAtoRegulatorio = "Ato regulatório";
                                        
                                        $dados_representacao->RecredenciamentoRegistradoraTipo = (string) $dados_recredenciamento_registradora->Tipo;
                                        $dados_representacao->RecredenciamentoRegistradoraNumero = (string) $dados_recredenciamento_registradora->Numero;
                                        $dados_representacao->RecredenciamentoRegistradoraData = (string) TDate::date2br($dados_recredenciamento_registradora->Data);
                                        $dados_representacao->RecredenciamentoRegistradoraVeiculoPublicacao = (string) $dados_recredenciamento_registradora->VeiculoPublicacao;
                                        $dados_representacao->RecredenciamentoRegistradoraDataPublicacao = (string) TDate::date2br($dados_recredenciamento_registradora->DataPublicacao);
                                        $dados_representacao->RecredenciamentoRegistradoraSecaoPublicacao = (string) $dados_recredenciamento_registradora->SecaoPublicacao;
                                        $dados_representacao->RecredenciamentoRegistradoraPaginaPublicacao = (string) $dados_recredenciamento_registradora->PaginaPublicacao;
                                        $dados_representacao->RecredenciamentoRegistradoraNumeroDOU = (string) $dados_recredenciamento_registradora->NumeroDOU;
                                    }
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecRegistradora = "Credenciamento";
                                
                                foreach($dados_registradora->Credenciamento as $dados_credenciamento_registradora)
                                {
                                    if($dados_credenciamento_registradora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecRegistradoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_credenciamento_registradora->InformacoesTramitacaoEMEC as $dados_tramitacao_credenciamento_registradora)
                                        {
                                            $dados_representacao->CredenciamentoRegistradoraNumeroProcesso = (string) $dados_tramitacao_credenciamento_registradora->NumeroProcesso;
                                            $dados_representacao->CredenciamentoRegistradoraTipoProcesso = (string) $dados_tramitacao_credenciamento_registradora>TipoProcesso;
                                            $dados_representacao->CredenciamentoRegistradoraDataCadastro = (string) TDate::date2br($dados_tramitacao_credenciamento_registradora->DataCadastro);
                                            $dados_representacao->CredenciamentoRegistradoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_credenciamento_registradora->DataProtocolo); 
                                        }
                                    }
                                    else
                                    {
                                        $dados_representacao->EmecRegistradoraAtoRegulatorio = "Ato regulatório";
                                        
                                        $dados_representacao->CredenciamentoRegistradoraTipo = (string) $dados_credenciamento_registradora->Tipo;
                                        $dados_representacao->CredenciamentoRegistradoraNumero = (string) $dados_credenciamento_registradora->Numero;
                                        $dados_representacao->CredenciamentoRegistradoraData = (string) TDate::date2br($dados_credenciamento_registradora->Data);
                                        $dados_representacao->CredenciamentoRegistradoraVeiculoPublicacao = (string) $dados_credenciamento_registradora->VeiculoPublicacao;
                                        $dados_representacao->CredenciamentoRegistradoraDataPublicacao = (string) TDate::date2br($dados_credenciamento_registradora->DataPublicacao);
                                        $dados_representacao->CredenciamentoRegistradoraSecaoPublicacao = (string) $dados_credenciamento_registradora->SecaoPublicacao;
                                        $dados_representacao->CredenciamentoRegistradoraPaginaPublicacao = (string) $dados_credenciamento_registradora->PaginaPublicacao;
                                        $dados_representacao->CredenciamentoRegistradoraNumeroDOU = (string) $dados_credenciamento_registradora->NumeroDOU;
                                    }
                                }
                            }
                            
                            //Pega somente as informações obrigatórias
                            if($dados_registradora->AtoRegulatorioAutorizacaoRegistro)
                            {
                                $dados_representacao->AutorizacaoRegistro = "Autorização para registro";
                                
                                foreach($dados_registradora->AtoRegulatorioAutorizacaoRegistro as $dados_autorizacao_registro)
                                {
                                    $dados_representacao->AutorizacaoRegistroTipo = (string) $dados_autorizacao_registro->Tipo;
                                    $dados_representacao->AutorizacaoRegistroNumero = (string) $dados_autorizacao_registro->Numero;
                                    $dados_representacao->AutorizacaoRegistroData = (string) TDate::date2br($dados_autorizacao_registro->Data);
                                }
                            }                    
                        }
                                                
                        //LIVRO DE REGISTRO
                        foreach($tag_dados_registro->LivroRegistro as $dados_livro_registro)
                        {
                            $dados_representacao->LivroRegistroDiplomaRegistradora = (string) $dados_livro_registro->LivroRegistro;
                            
                            if($dados_livro_registro->NumeroRegistro)
                            {
                                $dados_representacao->NumeroRegistroDiplomaRegistradora = (string) $dados_livro_registro->NumeroRegistro;   
                            }
                            else
                            {
                                $dados_representacao->NumeroFolhaDoDiplomaRegistradora = (string) $dados_livro_registro->NumeroFolhaDoDiploma;
                                $dados_representacao->NumeroSequenciaDoDiplomaRegistradora = (string) $dados_livro_registro->NumeroSequenciaDoDiploma;
                            }
                            
                            $dados_representacao->ProcessoDoDiplomaRegistradora = (string) $dados_livro_registro->ProcessoDoDiploma;
                            $dados_representacao->DataColacaoGrauRegistradora = (string) $dados_livro_registro->DataColacaoGrau;
                            $dados_representacao->DataExpedicaoDiplomaRegistradora = (string) $dados_livro_registro->DataExpedicaoDiploma;
                            $dados_representacao->DataRegistroDiplomaRegistradora = (string) $dados_livro_registro->DataRegistroDiploma;
                        
                            foreach($dados_livro_registro->ResponsavelRegistro as $dados_responsavel_registro)
                            {
                                $dados_representacao->NomeResponsavelRegistro = (string) $dados_responsavel_registro->Nome;
                                $dados_representacao->CpfResponsavelRegistro = (string) $dados_responsavel_registro->CPF;
                                $dados_representacao->IdOuMatriculaResponsavelRegistro = (string) $dados_responsavel_registro->IDouNumeroMatricula;    
                            }
                        }
                                                
                        //CÓDIGO DE VALIDAÇÃO DIPLOMA
                        foreach($tag_dados_registro->Seguranca as $dados_seguranca)
                        {
                            $dados_representacao->CodigoValidacaoDiploma = (string) $dados_seguranca->CodigoValidacao;
                        }    
                    }
                }
                else
                {
                    //Percorre a tag DadosRegistroPorDecisaoJudicial
                    foreach($tags_dados_diploma->DadosRegistroPorDecisaoJudicial as $tag_dados_registro)
                    {
                        foreach($tag_dados_registro->IesRegistradora as $dados_registradora)
                        {
                            $dados_representacao->NomeRegistradora = (string) $dados_registradora->Nome;
                            
                            if($dados_registradora->RenovacaoDeRecredenciamento)
                            {
                                $dados_representacao->EmecRegistradora = "Renovação de recredenciamento";
                                
                                foreach($dados_registradora->RenovacaoDeRecredenciamento as $dados_renovacao_registradora)
                                {
                                    if($dados_renovacao_registradora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecRegistradoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_renovacao_registradora->InformacoesTramitacaoEMEC as $dados_tramitacao_renovacao_registradora)
                                        {
                                            $dados_representacao->RenovacaoRegistradoraNumeroProcesso = (string) $dados_tramitacao_renovacao_registradora->NumeroProcesso;
                                            $dados_representacao->RenovacaoRegistradoraTipoProcesso = (string) $dados_tramitacao_renovacao_registradora->TipoProcesso;
                                            $dados_representacao->RenovacaoRegistradoraDataCadastro = (string) TDate::date2br($dados_tramitacao_renovacao_registradora->DataCadastro);
                                            $dados_representacao->RenovacaoRegistradoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_renovacao_registradora->DataProtocolo); 
                                        }
                                    }
                                    else
                                    {
                                        $dados_representacao->EmecRegistradoraAtoRegulatorio = "Ato regulatório";
                                        
                                        $dados_representacao->RenovacaoRegistradoraTipo = (string) $dados_renovacao_registradora->Tipo;
                                        $dados_representacao->RenovacaoRegistradoraNumero = (string) $dados_renovacao_registradora->Numero;
                                        $dados_representacao->RenovacaoRegistradoraData = (string) TDate::date2br($dados_renovacao_registradora->Data);
                                        $dados_representacao->RenovacaoRegistradoraVeiculoPublicacao = (string) $dados_renovacao_registradora->VeiculoPublicacao;
                                        $dados_representacao->RenovacaoRegistradoraDataPublicacao = (string) TDate::date2br($dados_renovacao_registradora->DataPublicacao);
                                        $dados_representacao->RenovacaoRegistradoraSecaoPublicacao = (string) $dados_renovacao_registradora->SecaoPublicacao;
                                        $dados_representacao->RenovacaoRegistradoraPaginaPublicacao = (string) $dados_renovacao_registradora->PaginaPublicacao;
                                        $dados_representacao->RenovacaoRegistradoraNumeroDOU = (string) $dados_renovacao_registradora->NumeroDOU;
                                    }
                                }
                            }
                            elseif($dados_registradora->Recredenciamento)
                            {
                                $dados_representacao->EmecRegistradora = "Recredenciamento";
                                
                                foreach($dados_registradora->Recredenciamento as $dados_recredenciamento_registradora)
                                {
                                    if($dados_recredenciamento_registradora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecRegistradoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_recredenciamento_registradora->InformacoesTramitacaoEMEC as $dados_tramitacao_recredenciamento_registradora)
                                        {
                                            $dados_representacao->RecredenciamentoRegistradoraNumeroProcesso = (string) $dados_tramitacao_recredenciamento_registradora->NumeroProcesso;
                                            $dados_representacao->RecredenciamentoRegistradoraTipoProcesso = (string) $dados_tramitacao_recredenciamento_registradora->TipoProcesso;
                                            $dados_representacao->RecredenciamentoRegistradoraDataCadastro = (string) TDate::date2br($dados_tramitacao_recredenciamento_registradora->DataCadastro);
                                            $dados_representacao->RecredenciamentoRegistradoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_recredenciamento_registradora->DataProtocolo); 
                                        }
                                    }
                                    else
                                    {
                                        $dados_representacao->EmecRegistradoraAtoRegulatorio = "Ato regulatório";
                                        
                                        $dados_representacao->RecredenciamentoRegistradoraTipo = (string) $dados_recredenciamento_registradora->Tipo;
                                        $dados_representacao->RecredenciamentoRegistradoraNumero = (string) $dados_recredenciamento_registradora->Numero;
                                        $dados_representacao->RecredenciamentoRegistradoraData = (string) TDate::date2br($dados_recredenciamento_registradora->Data);
                                        $dados_representacao->RecredenciamentoRegistradoraVeiculoPublicacao = (string) $dados_recredenciamento_registradora->VeiculoPublicacao;
                                        $dados_representacao->RecredenciamentoRegistradoraDataPublicacao = (string) TDate::date2br($dados_recredenciamento_registradora->DataPublicacao);
                                        $dados_representacao->RecredenciamentoRegistradoraSecaoPublicacao = (string) $dados_recredenciamento_registradora->SecaoPublicacao;
                                        $dados_representacao->RecredenciamentoRegistradoraPaginaPublicacao = (string) $dados_recredenciamento_registradora->PaginaPublicacao;
                                        $dados_representacao->RecredenciamentoRegistradoraNumeroDOU = (string) $dados_recredenciamento_registradora->NumeroDOU;
                                    }
                                }
                            }
                            else
                            {
                                $dados_representacao->EmecRegistradora = "Credenciamento";
                                
                                foreach($dados_registradora->Credenciamento as $dados_credenciamento_registradora)
                                {
                                    if($dados_credenciamento_registradora->InformacoesTramitacaoEMEC)
                                    {
                                        $dados_representacao->EmecRegistradoraTramitacao = "Tramitação do processo";
                                        
                                        foreach($dados_credenciamento_registradora->InformacoesTramitacaoEMEC as $dados_tramitacao_credenciamento_registradora)
                                        {
                                            $dados_representacao->CredenciamentoRegistradoraNumeroProcesso = (string) $dados_tramitacao_credenciamento_registradora->NumeroProcesso;
                                            $dados_representacao->CredenciamentoRegistradoraTipoProcesso = (string) $dados_tramitacao_credenciamento_registradora>TipoProcesso;
                                            $dados_representacao->CredenciamentoRegistradoraDataCadastro = (string) TDate::date2br($dados_tramitacao_credenciamento_registradora->DataCadastro);
                                            $dados_representacao->CredenciamentoRegistradoraDataProtocolo = (string) TDate::date2br($dados_tramitacao_credenciamento_registradora->DataProtocolo); 
                                        }
                                    }
                                    else
                                    {
                                        $dados_representacao->EmecRegistradoraAtoRegulatorio = "Ato regulatório";
                                        
                                        $dados_representacao->CredenciamentoRegistradoraTipo = (string) $dados_credenciamento_registradora->Tipo;
                                        $dados_representacao->CredenciamentoRegistradoraNumero = (string) $dados_credenciamento_registradora->Numero;
                                        $dados_representacao->CredenciamentoRegistradoraData = (string) TDate::date2br($dados_credenciamento_registradora->Data);
                                        $dados_representacao->CredenciamentoRegistradoraVeiculoPublicacao = (string) $dados_credenciamento_registradora->VeiculoPublicacao;
                                        $dados_representacao->CredenciamentoRegistradoraDataPublicacao = (string) TDate::date2br($dados_credenciamento_registradora->DataPublicacao);
                                        $dados_representacao->CredenciamentoRegistradoraSecaoPublicacao = (string) $dados_credenciamento_registradora->SecaoPublicacao;
                                        $dados_representacao->CredenciamentoRegistradoraPaginaPublicacao = (string) $dados_credenciamento_registradora->PaginaPublicacao;
                                        $dados_representacao->CredenciamentoRegistradoraNumeroDOU = (string) $dados_credenciamento_registradora->NumeroDOU;
                                    }
                                }
                            }
                            
                            //Pega somente as informações obrigatórias
                            if($dados_registradora->AtoRegulatorioAutorizacaoRegistro)
                            {
                                $dados_representacao->AutorizacaoRegistro = "Autorização para registro";
                                
                                foreach($dados_registradora->AtoRegulatorioAutorizacaoRegistro as $dados_autorizacao_registro)
                                {
                                    $dados_representacao->AutorizacaoRegistroTipo = (string) $dados_autorizacao_registro->Tipo;
                                    $dados_representacao->AutorizacaoRegistroNumero = (string) $dados_autorizacao_registro->Numero;
                                    $dados_representacao->AutorizacaoRegistroData = (string) TDate::date2br($dados_autorizacao_registro->Data);
                                }
                            }                    
                        }
                                                
                        //LIVRO DE REGISTRO
                        foreach($tag_dados_registro->LivroRegistro as $dados_livro_registro)
                        {
                            $dados_representacao->LivroRegistroDiplomaRegistradora = (string) $dados_livro_registro->LivroRegistro;
                            
                            if($dados_livro_registro->NumeroRegistro)
                            {
                                $dados_representacao->NumeroRegistroDiplomaRegistradora = (string) $dados_livro_registro->NumeroRegistro;   
                            }
                            else
                            {
                                $dados_representacao->NumeroFolhaDoDiplomaRegistradora = (string) $dados_livro_registro->NumeroFolhaDoDiploma;
                                $dados_representacao->NumeroSequenciaDoDiplomaRegistradora = (string) $dados_livro_registro->NumeroSequenciaDoDiploma;
                            }
                            
                            $dados_representacao->ProcessoDoDiplomaRegistradora = (string) $dados_livro_registro->ProcessoDoDiploma;
                            $dados_representacao->DataColacaoGrauRegistradora = (string) $dados_livro_registro->DataColacaoGrau;
                            $dados_representacao->DataExpedicaoDiplomaRegistradora = (string) $dados_livro_registro->DataExpedicaoDiploma;
                            $dados_representacao->DataRegistroDiplomaRegistradora = (string) $dados_livro_registro->DataRegistroDiploma;
                        
                            foreach($dados_livro_registro->ResponsavelRegistro as $dados_responsavel_registro)
                            {
                                $dados_representacao->NomeResponsavelRegistro = (string) $dados_responsavel_registro->Nome;
                                $dados_representacao->CpfResponsavelRegistro = (string) $dados_responsavel_registro->CPF;
                                $dados_representacao->IdOuMatriculaResponsavelRegistro = (string) $dados_responsavel_registro->IDouNumeroMatricula;    
                            }
                        }
                                                
                        //CÓDIGO DE VALIDAÇÃO DIPLOMA
                        foreach($tag_dados_registro->Seguranca as $dados_seguranca)
                        {
                            $dados_representacao->CodigoValidacaoDiploma = (string) $dados_seguranca->CodigoValidacao;
                        }    
                    }
                }
            } 

            //Limpa variável para garantir integridade
            TSession::setValue('dados_representacao', NULL);
            TSession::setValue('dados_representacao', $dados_representacao);
            
            TTransaction::close();            

            $this->onFormatarDadosXml($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onFormatarDadosXml($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $id_diploma = $param['id_diploma'];
            $diploma = new DiplomaDigitalDiploma($id_diploma);
            
            $dados_representacao = TSession::getValue('dados_representacao'); 


            //Nome
           /* $nome = mb_strtolower($dados_representacao->NomeDiplomado); //Converte o nome todo para minúsculo sem desconfigurar os acentos

            $nome = explode(" ", $nome); // Separa o nome por espaços

            for ($i=0; $i < count($nome); $i++) 
            { 
                //Tratar cada palavra do nome
                if ($nome[$i] == "de" or $nome[$i] == "da" or $nome[$i] == "e" or $nome[$i] == "dos" or $nome[$i] == "do")
                {
                    $nome_diplomado .= $nome[$i].' '; //Se a palavra estiver dentro das complementares, mostra toda em minúsculo
                }
                else
                {
                    $nome_diplomado .= ucfirst($nome[$i]).' '; //Se for um nome, mostra a primeira letra maiúscula
                }
            }*/
            $nome = mb_strtolower($dados_representacao->NomeDiplomado); // Converte o nome todo para minúsculo sem desconfigurar os acentos

            $nome = preg_split('/\s+/', $nome); // Separa o nome por espaços, mantendo os acentos

            $nome_diplomado = ''; // Inicializa a variável do nome formatado

            foreach ($nome as $palavra) {
                // Tratar cada palavra do nome
                if (in_array($palavra, array("de", "da", "e", "dos", "do"))) {
                    $nome_diplomado .= $palavra . ' '; // Se a palavra estiver dentro das complementares, mostra toda em minúsculo
                } else {
                    $nome_diplomado .= mb_convert_case($palavra, MB_CASE_TITLE, 'UTF-8') . ' '; // Converte a primeira letra de cada palavra para maiúscula
                }
            }

            // Remover o espaço extra no final
            $nome_diplomado = trim($nome_diplomado);
                        
            
            //Nascida/Nascido
            if($dados_representacao->Sexo == "F")
            {
                $nascimento = "nascida";
            }
            else
            {
                $nascimento = "nascido";
            }
            
            
            //Data de nascimento por extenso
            $data_nascimento = $dados_representacao->DataNascimento;
                               setlocale(LC_TIME, 'portuguese'); 
                               date_default_timezone_set('America/Sao_Paulo');
            $data_nascimento_extenso = strftime("%d de %B de %Y", strtotime($data_nascimento));
            $data_nascimento_extenso = utf8_encode($data_nascimento_extenso);


            //Naturalidade
            $nome_cidade = mb_strtolower($dados_representacao->NaturalidadeMunicipio); // Converte o nome todo para minúsculo sem desconfigurar os acentos

            $nome_cidade = str_replace(array("-", "(", ")"), array(" ", " ", " "), $nome_cidade); //Retira caracteres especiais

            $nome_cidade = explode(" ", $nome_cidade); // Separa o nome por espaços

            for ($i=0; $i < count($nome_cidade); $i++) 
            {     
                //Tratar cada palavra do nome
                if ($nome_cidade[$i] == "de" or $nome_cidade[$i] == "da" or $nome_cidade[$i] == "do" or $nome_cidade[$i] == "dos" or $nome_cidade[$i] == "das")
                {
                    $cidade .= $nome_cidade[$i].' '; // Se a palavra estiver dentro das complementares, mostra toda em minúsculo
                }
                elseif($nome_cidade[$i] == "d'oeste")
                {
                    $do_oeste = "D'Oeste";
                    
                    $cidade .= $do_oeste.' '; //Se tiver apóstrofo, mostra as letras envolvidas em maiúsculo
                }
                else
                {
                    $cidade .= ucfirst($nome_cidade[$i]).' '; //Se for um nome, mostra a primeira letra maiúscula
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


            //Nacionalidade
            if($diploma->diploma_digital_emissora->system_unit_id == 2 OR $diploma->diploma_digital_emissora->system_unit_id == 6 OR $diploma->diploma_digital_emissora->system_unit_id == 10) //FFCL, NEAD e FAJOB
            {
                if($dados_representacao->Nacionalidade == "Brasileira" OR $dados_representacao->Nacionalidade == "Brasileiro")
                {
                     $nacionalidade = "brasileira"; //FFCL usa "nacionalidade brasileira"    
                }
                else
                {
                    $nacionalidade = mb_strtolower($dados_representacao->Nacionalidade);
                }
            }
            else //FAFRAM
            {
                $nacionalidade = mb_strtolower($dados_representacao->Nacionalidade);    
            }


            //Documento de identificação
            if($dados_representacao->RgNumero)
            {
                $rg_numero = $dados_representacao->RgNumero;
                $rg_orgao_expedidor = $dados_representacao->RgOrgaoExpedidor;
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
                        if($diploma->diploma_digital_emissora->system_unit_id == 2 OR $diploma->diploma_digital_emissora->system_unit_id == 6 OR $diploma->diploma_digital_emissora->system_unit_id == 10) //FFCL, NEAD e FAJOB
                        {
                            $documento_identificacao_texto = "cédula de identidade nº ";
                            $documento_identificacao_valor = $rg_formatado . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;
                        }
                        else
                        {
                            $documento_identificacao_texto = "RG nº ";
                            $documento_identificacao_valor = $rg_formatado . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;
                        }                    
                    }
                    
                    //Formatação de RG sem dígito verificador
                    else
                    {
                        if($diploma->diploma_digital_emissora->system_unit_id == 2 OR $diploma->diploma_digital_emissora->system_unit_id == 6 OR $diploma->diploma_digital_emissora->system_unit_id == 10) //FFCL, NEAD e FAJOB
                        {
                            $rg_sem_digito = preg_replace('/^([0-9]{1,2})([0-9]{3})([0-9]{3})/', '$1.$2.$3', $rg_numero);
                            
                            $documento_identificacao_texto = "cédula de identidade nº ";
                            $documento_identificacao_valor = $rg_sem_digito . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;
                        }
                        else
                        {
                            $rg_sem_digito = preg_replace('/^([0-9]{1,2})([0-9]{3})([0-9]{3})/', '$1.$2.$3', $rg_numero);
                            
                            $documento_identificacao_texto = "RG nº ";
                            $documento_identificacao_valor = $rg_sem_digito . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;
                        }                   
                    }
                }
                else //Ex: RGs de Minas Gerais (começa com MG)
                {
                    if($diploma->diploma_digital_emissora->system_unit_id == 2 OR $diploma->diploma_digital_emissora->system_unit_id == 6 OR $diploma->diploma_digital_emissora->system_unit_id == 10) //FFCL, NEAD e FAJOB
                    {
                        $rg = preg_replace('/^([A-Za-z]{1,2})([0-9]{1,2})([0-9]{3})([0-9]{3})$/', '$1-$2.$3.$4', $rg_numero);
                        
                        $documento_identificacao_texto = "cédula de identidade nº ";
                        $documento_identificacao_valor = $rg . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;
                    }
                    else
                    {
                        $rg = preg_replace('/^([A-Za-z]{1,2})([0-9]{1,2})([0-9]{3})([0-9]{3})$/', '$1-$2.$3.$4', $rg_numero);
                        
                        $documento_identificacao_texto = "RG nº ";
                        $documento_identificacao_valor = $rg  . ' ' . $rg_orgao_expedidor . '/' . $rg_uf;
                    }
                }
            }
            else
            {
                $documento_identificacao_texto = "documento de identificação ";
                $documento_identificacao_valor = $dados_representacao->DocTipo . ' - ' . $dados_representacao->DocIdentificador;
            }
            
            
            //Data de conclusão por extenso e ano de conclusão
            $data_conclusao = $dados_representacao->DataConclusaoCurso;
                              setlocale(LC_TIME, 'portuguese'); 
                              date_default_timezone_set('America/Sao_Paulo');
            $data_conclusao_extenso = strftime("%d de %B de %Y", strtotime($data_conclusao));
            $data_conclusao_extenso = utf8_encode($data_conclusao_extenso);
                
            $ano_conclusao = trim(substr($data_conclusao, 0, -6));
                                     
              
            //Data da colação por extenso
            $data_colacao = trim($dados_representacao->DataColacaoGrauRegistradora);
                            setlocale(LC_TIME, 'portuguese'); 
                            date_default_timezone_set('America/Sao_Paulo');
            $data_colacao_extenso = strftime("%d de %B de %Y", strtotime($data_colacao));
            $data_colacao_extenso = utf8_encode($data_colacao_extenso);
                
                
            //Data de expedição por extenso
            $data_expedicao = trim($dados_representacao->DataExpedicaoDiplomaRegistradora);
                              setlocale(LC_TIME, 'portuguese'); 
                              date_default_timezone_set('America/Sao_Paulo');
            $data_expedicao_extenso = strftime("%d de %B de %Y", strtotime($data_expedicao));
            $data_expedicao_extenso = utf8_encode($data_expedicao_extenso);
                                   
            
            //Curso - título (Flexiona gênero)
            if($dados_representacao->Sexo == 'F') 
            {
                if($dados_representacao->TituloConferido == "Bacharel")
                {
                    $titulo_conferido = "Bacharela";
                }
                elseif($dados_representacao->TituloConferido == "Licenciado")
                {
                    $titulo_conferido = "Licenciada";
                }
                elseif($dados_representacao->TituloConferido == "Médico")
                {
                    $titulo_conferido = "Médica";
                }
                elseif($dados_representacao->TituloConferido == "Tecnólogo")
                {
                    $titulo_conferido = "Tecnóloga";
                }
                else
                {
                    $titulo_conferido = $dados_representacao->TituloConferido;
                }
            }
            else
            {
                $titulo_conferido = $dados_representacao->TituloConferido;
            }
                        
            
            //CNPJ Mantenedora
            $cnpj_mantenedora = preg_replace('/^([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})$/', '$1.$2.$3/$4-$5', $dados_representacao->CnpjMantenedora);


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

           
            //Emec - Registradora
            if($dados_representacao->EmecRegistradora == "Renovação de recredenciamento") 
            {
                if($dados_representacao->EmecRegistradoraAtoRegulatorio)
                {
                    $texto_emec_registradora = $dados_representacao->EmecRegistradora . " pelo(a) " . 
                                               $dados_representacao->RenovacaoRegistradoraTipo . " nº " .
                                               $dados_representacao->RenovacaoRegistradoraNumero . ", de " .
                                               $dados_representacao->RenovacaoRegistradoraData . ", publicado(a) no " .
                                               $dados_representacao->RenovacaoRegistradoraVeiculoPublicacao . /*" nº " .
                                               $dados_representacao->RenovacaoRegistradoraNumeroDOU . ", seção " . 
                                               $dados_representacao->RenovacaoRegistradoraSecaoPublicacao . ", pág. " .
                                               $dados_representacao->RenovacaoRegistradoraPaginaPublicacao .*/ " em " .
                                               $dados_representacao->RenovacaoRegistradoraDataPublicacao;
                }
                else
                {
                    $texto_emec_registradora = "Processo de " . $dados_representacao->RenovacaoRegistradoraTipoProcesso .
                                               " cadastrado em " . $dados_representacao->RenovacaoRegistradoraDataCadastro .
                                               " e protocolado em " . $dados_representacao->RenovacaoRegistradoraDataProtocolo .
                                               " sob o nº " . $dados_representacao->RenovacaoRegistradoraNumeroProcesso .
                                               " junto ao e-mec";
                    
                                               /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecRegistradora . 
                                               " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->RenovacaoRegistradoraNumeroProcesso .
                                               ", " . $dados_representacao->RenovacaoRegistradoraTipoProcesso . " cadastrado em " .
                                               $dados_representacao->RenovacaoRegistradoraDataCadastro . " e protocolado em " .
                                               $dados_representacao->RenovacaoRegistradoraDataProtocolo;*/
                }
            }          
            elseif($dados_representacao->EmecRegistradora == "Recredenciamento")
            {
                if($dados_representacao->EmecRegistradoraAtoRegulatorio)
                {
                    $texto_emec_registradora = "Recredenciada pelo(a) " . $dados_representacao->RecredenciamentoRegistradoraTipo . 
                                               " nº " . $dados_representacao->RecredenciamentoRegistradoraNumero . ", de " .
                                               $dados_representacao->RecredenciamentoRegistradoraData . ", publicado(a) no " .
                                               $dados_representacao->RecredenciamentoRegistradoraVeiculoPublicacao . /*" nº " .
                                               $dados_representacao->RecredenciamentoRegistradoraNumeroDOU . ", seção " . 
                                               $dados_representacao->RecredenciamentoRegistradoraSecaoPublicacao . ", pág. " .
                                               $dados_representacao->RecredenciamentoRegistradoraPaginaPublicacao .*/ " em " .
                                               $dados_representacao->RecredenciamentoRegistradoraDataPublicacao;
                }
                else
                {
                    $texto_emec_registradora = "Processo de " . $dados_representacao->RecredenciamentoRegistradoraTipoProcesso .
                                               " cadastrado em " . $dados_representacao->RecredenciamentoRegistradoraDataCadastro .
                                               " e protocolado em " . $dados_representacao->RecredenciamentoRegistradoraDataProtocolo .
                                               " sob o nº " . $dados_representacao->RecredenciamentoRegistradoraNumeroProcesso .
                                               " junto ao e-mec";
                    
                                               /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecRegistradora . 
                                               " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->RecredenciamentoRegistradoraNumeroProcesso .
                                               ", " . $dados_representacao->RecredenciamentoRegistradoraTipoProcesso . " cadastrado em " .
                                               $dados_representacao->RecredenciamentoRegistradoraDataCadastro . " e protocolado em " .
                                               $dados_representacao->RecredenciamentoRegistradoraDataProtocolo;*/
                }
            }            
            else
            {
                if($dados_representacao->EmecRegistradoraAtoRegulatorio)
                {
                    $texto_emec_registradora = "Credenciada pelo(a) " . $dados_representacao->CredenciamentoRegistradoraTipo . 
                                               " nº " . $dados_representacao->CredenciamentoRegistradoraNumero . ", de " .
                                               $dados_representacao->CredenciamentoRegistradoraData . ", publicado(a) no " .
                                               $dados_representacao->CredenciamentoRegistradoraVeiculoPublicacao . /*" nº " .
                                               $dados_representacao->CredenciamentoRegistradoraNumeroDOU . ", seção " . 
                                               $dados_representacao->CredenciamentoRegistradoraSecaoPublicacao . ", pág. " .
                                               $dados_representacao->CredenciamentoRegistradoraPaginaPublicacao .*/ " em " .
                                               $dados_representacao->CredenciamentoRegistradoraDataPublicacao;
                }
                else
                {
                    $texto_emec_registradora = "Processo de " . $dados_representacao->CredenciamentoRegistradoraTipoProcesso .
                                               " cadastrado em " . $dados_representacao->CredenciamentoRegistradoraDataCadastro .
                                               " e protocolado em " . $dados_representacao->CredenciamentoRegistradoraDataProtocolo .
                                               " sob o nº " . $dados_representacao->CredenciamentoRegistradoraNumeroProcesso .
                                               " junto ao e-mec";
                    
                                               /*"Informações sobre a tramitação do processo para " . $dados_representacao->EmecRegistradora . 
                                               " da instituição junto ao E-MEC: Processo de nº " . $dados_representacao->CredenciamentoRegistradoraNumeroProcesso .
                                               ", " . $dados_representacao->CredenciamentoRegistradoraTipoProcesso . " cadastrado em " .
                                               $dados_representacao->CredenciamentoRegistradoraDataCadastro . " e protocolado em " .
                                               $dados_representacao->CredenciamentoRegistradoraDataProtocolo;*/
                }
            }
            
            
            //Número do processo do diploma na registradora (não é obrigatório)
            if($dados_representacao->ProcessoDoDiplomaRegistradora)
            {
                $num_processo_dipl_registradora = "Processo nº: " . $dados_representacao->ProcessoDoDiplomaRegistradora;
            }
            
            
            //Dados de registro pode ser ser o nº do registro ou folha e nº sequencial do diploma
            if($dados_representacao->NumeroRegistroDiplomaRegistradora)
            {
                $informacoes_registro = "Diploma registrado sob o nº: " . $dados_representacao->NumeroRegistroDiplomaRegistradora;       
            }
            else
            {
                $informacoes_registro = "Nº da folha de registro: " . $dados_representacao->NumeroFolhaDoDiplomaRegistradora . ' / ' . 
                                        "Nº sequência do diploma: " . $dados_representacao->NumeroSequenciaDoDiplomaRegistradora;           
            }
            
       
            //Dados autorização para registro (não é obrigatório)
            if($dados_representacao->AutorizacaoRegistro)
            {
                $data_autorizacao_registro = trim($dados_representacao->AutorizacaoRegistroData);
                                             setlocale(LC_TIME, 'portuguese'); 
                                             date_default_timezone_set('America/Sao_Paulo');
                $data_autorizacao_registro_extenso = strftime("%d de %B de %Y", strtotime($data_autorizacao_registro));
                $data_autorizacao_registro_extenso = utf8_encode($data_autorizacao_registro_extenso);
            
                $informacoes_autorizacao_registro = "Por delegação de competência do Ministério da Educação, nos termos da Lei n.º 9.394, 
                                                     publicada no Diário Oficial da União em 23 de dezembro de 1996, e " .
                                                     $dados_representacao->AutorizacaoRegistroTipo . " nº " .
                                                     $dados_representacao->AutorizacaoRegistroNumero . ", de " . $data_autorizacao_registro_extenso . ".";
            }
            else
            {
                $informacoes_autorizacao_registro = "Por delegação de competência do Ministério da Educação, nos termos da Lei n.º 9.394, 
                                                     publicada no Diário Oficial da União em 23 de dezembro de 1996.";
            }
            
            
            //Dados do responsável pelo registro
            $cpf_responsavel_registro = preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/', '$1.$2.$3-$4', $dados_representacao->CpfResponsavelRegistro);

            if($dados_representacao->IdOuMatriculaResponsavelRegistro)
            {
                $matricula_responsavel_registro = "ID/Matrícula: " . $dados_representacao->IdOuMatriculaResponsavelRegistro;
            }
            
            
            //Limpa variável para garantir integridade
            TSession::setValue('representacao_diploma', NULL);        
        
            //Passa os dados para a representação do diploma
            TSession::setValue('representacao_diploma', array('NomeDiplomado' => $nome_diplomado,
                                                              'Nascimento' => $nascimento,
                                                              'DataNascimentoExtenso' => $data_nascimento_extenso,
                                                              'Naturalidade' => $naturalidade,
                                                              'Nacionalidade' => $nacionalidade,
                                                              'DocumentoIdentificacaoTexto' => $documento_identificacao_texto,
                                                              'DocumentoIdentificacaoValor' => $documento_identificacao_valor,
                                                              'NomeCurso' => $dados_representacao->NomeCurso,
                                                              'TextoEmecCurso' => $texto_emec_curso,
                                                              'TituloConferido' => $titulo_conferido,                                                                                                                         
                                                              'GrauConferido' => $dados_representacao->GrauConferido,
                                                              'NomeEmissora' => $dados_representacao->NomeEmissora,    
                                                              'TextoEmecEmissora' => $texto_emec_emissora,   
                                                              'NomeMantenedora' => $dados_representacao->NomeMantenedora,
                                                              'CnpjMantenedora' => $cnpj_mantenedora,
                                                              'NomeRegistradora' => mb_strtoupper($dados_representacao->NomeRegistradora),
                                                              'TextoEmecRegistradora' => $texto_emec_registradora,   
                                                              'TextoAutorizacaoRegistro' => $informacoes_autorizacao_registro,   
                                                              'LivroRegistroDiplomaRegistradora' => $dados_representacao->LivroRegistroDiplomaRegistradora,
                                                              'NumProcessoDiploma'=> $num_processo_dipl_registradora,
                                                              'InformacoesRegistro' => $informacoes_registro, 
                                                              'NomeResposavelRegistro' => $dados_representacao->NomeResponsavelRegistro,
                                                              'CpfResponsavelRegistro' => $cpf_responsavel_registro,
                                                              'MatriculaResponsavelRegistro' => $matricula_responsavel_registro,                                                              
                                                              'DataConclusaoExtenso' => $data_conclusao_extenso,
                                                              'AnoConclusao' => $ano_conclusao,
                                                              'DataColacaoAbreviada' => TDate::date2br($data_colacao),
                                                              'DataColacaoExtenso' => $data_colacao_extenso,
                                                              'DataExpedicaoDiplomaAbreviada' => TDate::date2br($data_expedicao),
                                                              'DataExpedicaoDiplomaExtenso' => $data_expedicao_extenso,
                                                              'DataRegistroDiplomaAbreviada' => TDate::date2br($dados_representacao->DataRegistroDiplomaRegistradora),                                                                                                                 
                                                              'CodigoValidacao' => $dados_representacao->CodigoValidacaoDiploma 
                                                             )      
                              );                              
                               
            TTransaction::close();            
            
            $this->onGerarQrCode($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    
    
    public function onGerarQrCode($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $id_diploma = $param['id_diploma'];
            $representacao_diploma = TSession::getValue('representacao_diploma');
            
            $diploma = new DiplomaDigitalDiploma($id_diploma);
            $codigo_validacao_diploma = $representacao_diploma['CodigoValidacao'];

            
            if(($diploma->qrcode == NULL) AND ($diploma->caminho_qrcode == NULL))
            {
                if($codigo_validacao_diploma <> NULL)
                {                                               
                    //Gera URL única do diploma
                    $url = 'https://academico.feituverava.com.br/consultadiploma-codigo=' . $codigo_validacao_diploma;
                    
                    //$url = 'https://192.168.10.107/academico/consultadiploma-codigo=' . $codigo_validacao_diploma;                 
                 
                    //Cria QR Code            
                    $options = new QROptions([
                        'version' => QRCode::VERSION_AUTO,
                        'outputType' => QRCode::OUTPUT_IMAGE_JPG,
                        'eccLevel' => QRCode::ECC_Q, //Q - nível mínimo exigido pelo MEC
                        'addQuietzone' => true,
                        'quietzoneSize' => 3,
                        'scale' => 3
                    ]);
        
        
                    //Cria diretório para salvar QR Code
                    $target_path = 'secretaria/diploma_qrcode';
                    $target_file = $target_path . '/'. 'qrcode_diploma_' . $diploma->id . '_' . $codigo_validacao_diploma . '.jpg';
                                
        
                    if (!file_exists($target_path))
                    {
                        if (!@mkdir($target_path, 0777, true))
                        {
                            throw new Exception(_t('Permission denied'). ': '. $target_path);
                        }
                    }
                    
                    if (file_exists($target_path))
                    {
                        $qrcode = new QRCode($options);
        
                        //Gera e salva a imagem do QR no servidor
                        $qrcode->render($url, $target_file);      
                    }
                    
                    $diploma->url_diploma = $url;
                    $diploma->qrcode = 'qrcode_diploma_' . $diploma->id . '_' . $codigo_validacao_diploma . '.jpg';
                    $diploma->caminho_qrcode = $target_path;
                    
                    $diploma->store();
                    
                    
                    //Adiciona o caminho do qrcode à variável de sessão
                    $representacao_diploma['CaminhoQrCode'] = $target_file;
                    
                    TSession::setValue('representacao_diploma', $representacao_diploma);

                    $this->onVisualizarDiploma($param);
                }
                else
                {
                    $action_voltar = new TAction(array('DiplomaRegistradoList', 'onReload')); 
                    new TMessage('error', 'O código de validação precisa estar inserido no XML para que o diploma possa ser publicado', $action_voltar);
                    die;
                }
            }            
            else
            {
                //Adiciona o caminho do qrcode à variável de sessão
                $representacao_diploma['CaminhoQrCode'] = $diploma->caminho_qrcode . '/' . $diploma->qrcode;
                    
                TSession::setValue('representacao_diploma', $representacao_diploma);
                        
                $this->onVisualizarDiploma($param);
            }
            
            TTransaction::close();    
            
        }    
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }      
    }
    
    
    public function onVisualizarDiploma($param)
    {
        $dados_representacao = TSession::getValue('representacao_diploma');

        $this->html->enableSection('main', $dados_representacao);
    }
    
    
    public static function onBackList()
    {
        TApplication::loadPage('DiplomaRegistradoList', 'onReload');
    }
    
    
    public static function onDownload($param)
    {
        try
        {            
            TTransaction::open('Felabs_DB');
            
            $id_diploma = $param['id_diploma'];
            $dados_representacao = TSession::getValue('representacao_diploma');            
            $codigo_validacao = $dados_representacao['CodigoValidacao'];
            
            $diploma = new DiplomaDigitalDiploma($id_diploma);
            $emissora = new DiplomaDigitalEmissora($diploma->dados_emissora_id);
                       
                                    
            //FFCL E NEAD
            if($emissora->system_unit_id == 2 OR $emissora->system_unit_id == 6) 
            {
                $html_impressao = new THtmlRenderer('app/resources/ImpressaoDiplomaFFCL.html');
                $html_impressao->enableSection('main', $dados_representacao);
            }
            
            //FAFRAM
            if($emissora->system_unit_id == 3)
            {
                $html_impressao = new THtmlRenderer('app/resources/ImpressaoDiplomaFAFRAM.html');
                $html_impressao->enableSection('main', $dados_representacao);
            }

            //FAJOB - falta imagem

            $contents = $html_impressao->getContents();
            
            $options = new \Dompdf\Options();
            $options->setChroot(getcwd());
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
                
            $file = 'app/output/rvdd-'. $codigo_validacao . '.pdf';
                
            file_put_contents($file, $dompdf->output());
                    
            $window = TWindow::create('Representação Visual do Diploma', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
            
            TTransaction::close();           
        }    
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public static function onQuestionPublicarDiploma($param)
    {
        try
        {          
            TTransaction::open('Felabs_DB');
            
            $id_diploma = $param['id_diploma'];

            $diploma = new DiplomaDigitalDiploma($id_diploma);           

            if($diploma->status_publicacao == 0)
            {
                $action_publicar = new TAction(['DiplomaRepresentacaoVisualView', 'onPublicarDiploma'], ['id_diploma' => $diploma->id]);                    
                $action_voltar = new TAction(['DiplomaRegistradoList', 'onReload']);
                   
                new TQuestion('Esta ação não poderá ser desfeita. Clicando em "Sim" você confirma que todos os dados foram conferidos e estão corretos. Deseja realmente publicá-lo ?', $action_publicar, $action_voltar);
            }
            
            //Se diploma já foi publicado e usuário deseja somente reenviar os dados de acesso para o aluno
            if($diploma->status_publicacao == 1)
            {
                self::onNotificarAluno($param);
            }
            
            TTransaction::close(); 
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }   
    }
    
    
    public static function onPublicarDiploma($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $id_diploma = $param['id_diploma'];

            $diploma = new DiplomaDigitalDiploma($id_diploma);

            $diploma->status_publicacao = 1;
            $diploma->data_publicacao = date('Y-m-d H:i:s');
            $diploma->store();
            
            TTransaction::close();
            
            new TMessage('info', 'Diploma publicado com sucesso!');
            
            self::onNotificarAluno($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public static function onNotificarAluno($param)
    {
        try
        {   
            TTransaction::open('Felabs_DB');
               
            $id_diploma = $param['id_diploma']; 
            
            $dados_diploma = new DiplomaDigitalDiploma($id_diploma);
            
            $diplomado = new DiplomaDigitalDiplomado($dados_diploma->dados_diplomado_id);           
                  
            //Exibe formulário
            $form = new BootstrapFormBuilder('input_form');
        
            $email = new TEntry('email');
            
            $form->addFields( [ new TLabel('E-mail para envio das instruções de acesso à página de consulta pública do diploma') ] );
            $form->addFields( [ $email ] );
            
            $form->addAction('Notificar aluno', new TAction([__CLASS__, 'onEnviarNotificacao'], ['id_diploma' => $dados_diploma->id]), 'far:check-circle blue');
            
            new TInputDialog('Notificação', $form);        

            //Seta email cadastrado em dados_diplomado 
            $obj = new StdClass;
            $obj->email = $diplomado->email;
            
            TForm::sendData('input_form', $obj);

            TTransaction::close();            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public static function onEnviarNotificacao($param)
    {
        try
        {
            $id_diploma = $param['id_diploma']; 
            $dados_representacao = TSession::getValue('representacao_diploma');            
            $codigo_validacao = $dados_representacao['CodigoValidacao'];

            if($codigo_validacao)
            {
                TTransaction::open('Felabs_DB');
                
                $prefs = SystemPreference::getAllPreferences();
                $diploma = new DiplomaDigitalDiploma($id_diploma);
                $diplomado = new DiplomaDigitalDiplomado($diploma->dados_diplomado_id);
                
                //Se usuário alterou email, salva alteração e dispara email
                $email = $param['email'];
                
                $diplomado->email = $email;
                $diplomado->store();
                
                TTransaction::close();
                
                $caminho_img = "http://academico.feituverava.com.br/app/images/cabecalho_email_diploma.png";            
                $imgData = base64_encode(file_get_contents($caminho_img));            
                $imgSrc = 'data: ' . mime_content_type($caminho_img) . ';base64,' . $imgData;
            
                $message = "
                    <html>        
                        <body style='margin: 0; padding: 0;'>
                            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                    <td>
                                        <table align='center' border='0' cellpadding='0' cellspacing='0' width='600' style='border-collapse: collapse;'>
                                            <tr>
                                                <td align='center' style='padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px;'>
                                                    <img src='http://academico.feituverava.com.br/app/images/cabecalho_email_diploma.png' alt='Diploma Digital FE' width='600' style='display: block;'/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td bgcolor='#ffffff' style='padding-top: 40px; padding-right: 30px; padding-bottom: 40px; padding-left: 30px;'>
                                                    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                                        <tr>
                                                            <td style='color: #153643; font-family: Arial, sans-serif; font-size: 24px;'><b>OLÁ, $diplomado->nome_social !</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td style='padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 25px; text-align: justify;'>
                                                                Atendendo à normatização do MEC, o diploma passa a ser um documento com existência, emissão e armazenamento integralmente digitais.<br><br> 
                                                                Emitido em formato XML (Extensible Markup Language) e assinado de forma eletrônica (certificado digital e carimbo de tempo - ICP-Brasil), 
                                                                o que torna o processo de emissão e registro muito mais rápido, prático e seguro, além da garantia de validade jurídica.<br><br> 
                                                                Para acessar seu diploma digital, siga estas instruções:<br><br>
                                                                1) Acesse nosso Portal Acadêmico através deste link: <a href='https://academico.feituverava.com.br/consultadiploma'>https://academico.feituverava.com.br/consultadiploma</a><br><br>
                                                                2) Durante o processo, será solicitado que você insira o código de validação do seu diploma. Utilize o seguinte código de validação: <b>$codigo_validacao</b>
                                                                <br><br> 
                                                                Se precisar de ajuda ou tiver alguma dúvida, contate a instituição. =)
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Tire suas dúvidas através do site: <a href='http://portal.mec.gov.br/diplomadigital/'>http://portal.mec.gov.br/diplomadigital/</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>    
                                            </tr>
                                            <tr>
                                                <td align='center' style='padding-top: 5px; padding-right: 0px; padding-bottom: 5px; padding-left: 0px;'>
                                                    <img src='http://academico.feituverava.com.br/app/images/fundo-intranet-logo.png' alt='Diploma Digital FE' width='400' style='display: block;'/>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                           </table>
                        </body>
                    </html>
                ";
            
                //Email para aluno    
                $mail = new TMail;
                $mail->setFrom($prefs['mail_from'], 'Fundação Educacional de Ituverava');
                $mail->setSubject('Diploma emitido');
                $mail->setHtmlBody($message);
                $mail->addAddress(trim($email), $diplomado->nome);
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();                             
                
                new TMessage('info', 'Aluno notificado com sucesso');
            }
            else
            {
                new TMessage('error', 'Falha ao notificar aluno');
            }    
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}