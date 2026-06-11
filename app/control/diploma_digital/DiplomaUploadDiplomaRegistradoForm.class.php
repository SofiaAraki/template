<?php

class DiplomaUploadDiplomaRegistradoForm extends TWindow
{
    protected $form;
        

    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Upload Diploma Registrado');
        parent::setSize(0.6, null);
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalDiploma');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $tipo_documento = new THidden('tipo_documento');
        $codigo_interliga_diploma_documentacao = new THidden('codigo_interliga_diploma_documentacao');
        $versao_xsd_diploma = new THidden('versao_xsd_diploma');
        $dados_diplomado_id = new THidden('dados_diplomado_id');
        $dados_curso_id = new THidden('dados_curso_id');
        $dados_polo_id = new THidden('dados_polo_id');
        $dados_emissora_id = new THidden('dados_emissora_id');
        $dados_documentacao_id = new THidden('dados_documentacao_id');
        $dados_processo_judicial_id = new THidden('dados_processo_judicial_id');
        $user_id_assinatura_secretaria = new THidden('user_id_assinatura_secretaria');
        $cpf_assinatura_secretaria = new THidden('cpf_assinatura_secretaria');
        $status_assinatura_secretaria = new THidden('status_assinatura_secretaria');
        $user_id_assinatura_diretor = new THidden('user_id_assinatura_diretor');
        $cpf_assinatura_diretor = new THidden('cpf_assinatura_diretor');
        $status_assinatura_diretor = new THidden('status_assinatura_diretor');
        $unit_id_assinatura_emissora = new THidden('unit_id_assinatura_emissora');
        $cnpj_assinatura_emissora = new THidden('cnpj_assinatura_emissora');
        $status_assinatura_emissora = new THidden('status_assinatura_emissora');
        $livro_registro_dipl_emissora = new THidden('livro_registro_dipl_emissora');
        $num_registro_dipl_emissora = new THidden('num_registro_dipl_emissora');
        $folha_registro_dipl_emissora = new THidden('folha_registro_dipl_emissora');
        $obs_registro_emissora = new THidden('obs_registro_emissora');
        $nome_registradora = new THidden('nome_registradora');
        $codigo_mec_registradora = new THidden('codigo_mec_registradora');
        $cnpj_registradora = new THidden('cnpj_registradora');
        $livro_registro_dipl_registradora = new THidden('livro_registro_dipl_registradora');
        $num_registro_dipl_registradora = new THidden('num_registro_dipl_registradora');
        $folha_registro_dipl_registradora = new THidden('folha_registro_dipl_registradora');
        $num_sequencia_dipl_registradora = new THidden('num_sequencia_dipl_registradora');
        $num_processo_dipl_registradora = new THidden('num_processo_dipl_registradora');
        $data_conclusao_curso = new THidden('data_conclusao_curso');
        $data_colacao_grau = new THidden('data_colacao_grau');
        $data_expedicao_diploma = new THidden('data_expedicao_diploma');
        $data_registro_diploma = new THidden('data_registro_diploma');
        $informacoes_adicionais_registradora = new THidden('informacoes_adicionais_registradora');
        $nome_responsavel_registro = new THidden('nome_responsavel_registro');
        $cpf_responsavel_registro = new THidden('cpf_responsavel_registro');
        $status_assinatura_responsavel_registro = new THidden('status_assinatura_responsavel_registro');
        $status_assinatura_arquivamento = new THidden('status_assinatura_arquivamento');
        $status_diploma = new THidden('status_diploma');
        $data_anulacao = new THidden('data_anulacao');
        $motivo_anulacao = new THidden('motivo_anulacao');
        $anotacao_anulacao = new THidden('anotacao_anulacao');
        $anulacao_system_user_id = new THidden('anulacao_system_user_id');
        $anulacao_data_reg = new THidden('anulacao_data_reg');
        $status_xml = new THidden('status_xml');
        $arquivo_registrado = new TFile('arquivo_registrado');
        $caminho_arquivo_registrado = new THidden('caminho_arquivo_registrado');
        $qrcode = new THidden('qrcode');
        $caminho_qrcode = new THidden('caminho_qrcode');
        $codigo_validacao_diploma = new THidden('codigo_validacao_diploma');
        $url_diploma = new THidden('url_diploma');
        $status_publicacao = new THidden('status_publicacao');
        $data_publicacao = new THidden('data_publicacao');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        $arquivo_registrado->setAllowedExtensions( ['xml'] );
        

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_documento ] );
        $this->form->addFields( [ $codigo_interliga_diploma_documentacao ] );
        $this->form->addFields( [ $versao_xsd_diploma ] );
        $this->form->addFields( [ $dados_diplomado_id ] );
        $this->form->addFields( [ $dados_curso_id ] );
        $this->form->addFields( [ $dados_polo_id ] );
        $this->form->addFields( [ $dados_emissora_id ] );
        $this->form->addFields( [ $dados_documentacao_id ] );
        $this->form->addFields( [ $dados_processo_judicial_id ] );
        $this->form->addFields( [ $user_id_assinatura_secretaria ] );
        $this->form->addFields( [ $cpf_assinatura_secretaria ] );
        $this->form->addFields( [ $status_assinatura_secretaria ] );
        $this->form->addFields( [ $user_id_assinatura_diretor ] );
        $this->form->addFields( [ $cpf_assinatura_diretor ] );
        $this->form->addFields( [ $status_assinatura_diretor ] );
        $this->form->addFields( [ $unit_id_assinatura_emissora ] );
        $this->form->addFields( [ $cnpj_assinatura_emissora ] );
        $this->form->addFields( [ $status_assinatura_emissora ] );
        $this->form->addFields( [ $livro_registro_dipl_emissora ] );
        $this->form->addFields( [ $num_registro_dipl_emissora ] );
        $this->form->addFields( [ $folha_registro_dipl_emissora ] );
        $this->form->addFields( [ $obs_registro_emissora ] );
        $this->form->addFields( [ $nome_registradora ] );
        $this->form->addFields( [ $codigo_mec_registradora ] );
        $this->form->addFields( [ $cnpj_registradora ] );
        $this->form->addFields( [ $livro_registro_dipl_registradora ] );
        $this->form->addFields( [ $num_registro_dipl_registradora ] );
        $this->form->addFields( [ $folha_registro_dipl_registradora ] );
        $this->form->addFields( [ $num_sequencia_dipl_registradora ] );
        $this->form->addFields( [ $num_processo_dipl_registradora ] );
        $this->form->addFields( [ $data_conclusao_curso ] );
        $this->form->addFields( [ $data_colacao_grau ] );
        $this->form->addFields( [ $data_expedicao_diploma ] );
        $this->form->addFields( [ $data_registro_diploma ] );
        $this->form->addFields( [ $informacoes_adicionais_registradora ] );
        $this->form->addFields( [ $nome_responsavel_registro ] );
        $this->form->addFields( [ $cpf_responsavel_registro ] );
        $this->form->addFields( [ $status_assinatura_responsavel_registro ] );
        $this->form->addFields( [ $status_assinatura_arquivamento ] );         
        $this->form->addFields( [ $status_diploma ] );
        $this->form->addFields( [ $data_anulacao ] );
        $this->form->addFields( [ $motivo_anulacao ] );
        $this->form->addFields( [ $anotacao_anulacao ] );
        $this->form->addFields( [ $anulacao_system_user_id ] );
        $this->form->addFields( [ $anulacao_data_reg ] );
        $this->form->addFields( [ $status_xml ] );
        $this->form->addFields( [ $caminho_arquivo_registrado ] );
        $this->form->addFields( [ $qrcode ] );
        $this->form->addFields( [ $caminho_qrcode ] );
        $this->form->addFields( [ $codigo_validacao_diploma ] );
        $this->form->addFields( [ $url_diploma ] );
        $this->form->addFields( [ $status_publicacao ] );
        $this->form->addFields( [ $data_publicacao ] );
        $this->form->addFields( [ $system_user_id ] );        
        $this->form->addFields( [ $data_reg ] );
        $this->form->addFields( [ new TLabel('Arquivo'), $arquivo_registrado ] );        


        $arquivo_registrado->addValidation('Arquivo', new TRequiredValidator);


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onBeforeSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onBeforeSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            
            if(!$param['arquivo_registrado'])
            {
                new TMessage('error', 'É necessário anexar um arquivo');
                die;
            }


            if($param['id'])    
            {
                $diploma_id = $param['id'];
                $diploma = new DiplomaDigitalDiploma($diploma_id);
            
            
                //1 - Verifica se é o XML do diploma
                $caminho = 'tmp/' . $param['arquivo_registrado']; 
                $arquivo = file_get_contents($caminho); //Traz arquivo inteiro em uma única string
                       
                $dom = new DOMDocument;
                $dom->loadXML($arquivo);       
                                          
                $tagDiploma = 'Diploma'; //Tag procurada    
                $nodoDiploma = $dom->getElementsByTagName($tagDiploma)->item(0); 
    
                if(!$nodoDiploma)
                {
                   $action1 = new TAction(['DiplomaRegistradoList', 'onReload']);                      
                   new TMessage('error', "O XML anexado não é do tipo 'Diploma'", $action1);
                   die;
                }
    
    
                //2 - Verifica se o XML pertence realmente ao diploma (compara o código de 44 dígitos)
                //$item = $dom->getElementsByTagName('DadosDiploma')->item(0);
                //$cod_unico = $item->attributes->getNamedItem("id")->nodeValue;
                //$cod_unico_diploma = substr($cod_unico, 3, 44); //Retira o "Dip" e deixa só a numeração para comparação 
                
                $item = $dom->getElementsByTagName('IdDocumentacaoAcademica')->item(0);                
                $cod_unico = $item->nodeValue;
                $cod_unico_diploma = substr($cod_unico, 6, 44); //Retira o "ReqDip" e deixa só a numeração para comparação            
      
                if($cod_unico_diploma <> $diploma->codigo_interliga_diploma_documentacao)
                {
                   $action2 = new TAction(['DiplomaRegistradoList', 'onReload']);                      
                   new TMessage('error', 'O xml anexado não pertence a este registro', $action2);
                   die;          
                }
    
                     
                //3 - Verifica se o XML contém os dados de registro 
                $tagRegistro = 'DadosRegistro'; //Tag procurada    
                $nodoRegistro = $dom->getElementsByTagName($tagRegistro)->item(0);
                
                $tagRegistroDecisaoJudicial = 'DadosRegistroPorDecisaoJudicial'; //Tag procurada    
                $nodoRegistroDecisaoJudicial = $dom->getElementsByTagName($tagRegistroDecisaoJudicial)->item(0);
                
                if((!$nodoRegistro) AND (!$nodoRegistroDecisaoJudicial))
                {
                   $action3 = new TAction(['DiplomaRegistradoList', 'onReload']);                      
                   new TMessage('error', 'O XML anexado não contém informações sobre o registro do diploma', $action3);
                   die;
                }
            
            
                //4 - Se já existir um XML, questiona usuário
                if(($diploma->arquivo_registrado <> NULL) OR ($diploma->caminho_arquivo_registrado <> NULL))
                {
                    $action_salvar = new TAction(['DiplomaUploadDiplomaRegistradoForm', 'onSave']);
                    $action_salvar->setParameters(['id' => $diploma->id, 'caminho' => $caminho]);
                            
                    $action_voltar = new TAction(['DiplomaRegistradoList', 'onReload']);
                            
                    new TQuestion('Já existe um arquivo XML referente a este diploma. Deseja realmente substituí-lo?', $action_salvar, $action_voltar);
                }
                else
                {
                    $param['id'] = $diploma->id;
                    $param['caminho'] = $caminho;
                    self::onSave($param);    
                }
            }
            else
            {
                $action4 = new TAction(['DiplomaRegistradoList', 'onReload']);                      
                new TMessage('error', "Recarregue a página e tente novamente", $action4);
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
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $key = $param['id'];
            $caminho_tmp = $param['caminho'];
            
            $object = new DiplomaDigitalDiploma($key);


            //1º - Lê o XML
            if(file_exists($caminho_tmp))
            {
                $xml = simplexml_load_file($caminho_tmp);
                $campos = $xml->getNamespaces(true);
        
                $registro = new StdClass();
                
                
                //Percorre a tag que está na raiz do XML
                foreach($xml->infDiploma as $tags_diploma) 
                {   
                    if($tags_diploma->DadosDiploma)
                    { 
                        //Percorre a tag DadosDiploma
                        foreach($tags_diploma->DadosDiploma as $tag_dados_diploma) 
                        {
                            $registro->data_conclusao_curso = (string) $tag_dados_diploma->DataConclusao;
                            
                            //Verifica se consta a assinatura da secretária, diretor e emissora
                            foreach($tag_dados_diploma->children($campos['ds']) as $assinaturas_emissao) 
                            {
                                //Nodo onde fica salvo o nome do proprietário do certificado
                                $assinante = (string) $assinaturas_emissao->KeyInfo->X509Data->X509SubjectName;
    
    
                                if((strpos($assinante, 'VILMA') !== false) OR (strpos($assinante, 'TANIA') !== false))
                                {
                                    $registro->status_assinatura_secretaria = 1;
                                }
     
                                if((strpos($assinante, 'LUCIANA') !== false) OR (strpos($assinante, 'MARCIO') !== false) OR (strpos($assinante, 'ROBERTO') !== false))
                                {
                                    $registro->status_assinatura_diretor = 1;
                                }
                                 
                                if(strpos($assinante, 'FUNDACAO') !== false)
                                {
                                    $registro->status_assinatura_emissora = 1;
                                }
                            }
                        }
                    }
                    else
                    {
                        //Percorre a tag DadosDiplomaPorDecisaoJudicial
                        foreach($tags_diploma->DadosDiplomaPorDecisaoJudicial as $tag_dados_diploma) 
                        {
                            $registro->data_conclusao_curso = (string) $tag_dados_diploma->DataConclusao;
                            
                            //Verifica se consta a assinatura da secretária, diretor e emissora
                            foreach($tag_dados_diploma->children($campos['ds']) as $assinaturas_emissao) 
                            {
                                //Nodo onde fica salvo o nome do proprietário do certificado
                                $assinante = (string) $assinaturas_emissao->KeyInfo->X509Data->X509SubjectName;
    
    
                                if((strpos($assinante, 'VILMA') !== false) OR (strpos($assinante, 'TANIA') !== false))
                                {
                                    $registro->status_assinatura_secretaria = 1;
                                }
     
                                if((strpos($assinante, 'LUCIANA') !== false) OR (strpos($assinante, 'MARCIO') !== false) OR (strpos($assinante, 'ROBERTO') !== false))
                                {
                                    $registro->status_assinatura_diretor = 1;
                                }
                                 
                                if(strpos($assinante, 'FUNDACAO') !== false)
                                {
                                    $registro->status_assinatura_emissora = 1;
                                }
                            }
                        }
                    }    
 
                     
                    if($tags_diploma->DadosRegistro)
                    {
                        //Percorre a tag DadosRegistro
                        foreach($tags_diploma->DadosRegistro as $tag_dados_registro) 
                        {
                            //Verifica se consta a assinatura do responsável pelo registro em DadosRegistro
                            foreach($tag_dados_registro->children($campos['ds']) as $assinatura_responsavel) 
                            {
                                if($assinatura_responsavel)
                                {
                                    $registro->status_assinatura_responsavel_registro = 1;
                                }
                            }                        
                            
                            //TAG REGISTRADORA
                            foreach($tag_dados_registro->IesRegistradora as $tag_dados_registradora)
                            {                            
                                $registro->nome_registradora = (string) $tag_dados_registradora->Nome;
                                $registro->codigo_mec_registradora = (string) $tag_dados_registradora->CodigoMEC;
                                $registro->cnpj_registradora = (string) $tag_dados_registradora->CNPJ;                            
                            }
                                                
                            //TAG LIVRO REGISTRO
                            foreach($tag_dados_registro->LivroRegistro as $dados_livro_registro)
                            {
                                $registro->livro_registro_dipl_registradora = (string) $dados_livro_registro->LivroRegistro;
                                $registro->num_registro_dipl_registradora = (string) $dados_livro_registro->NumeroRegistro;
                                $registro->folha_registro_dipl_registradora = (string) $dados_livro_registro->NumeroFolhaDoDiploma;
                                $registro->num_sequencia_dipl_registradora = (string) $dados_livro_registro->NumeroSequenciaDoDiploma;
                                $registro->num_processo_dipl_registradora = (string) $dados_livro_registro->ProcessoDoDiploma;
                                $registro->data_colacao_grau = (string) $dados_livro_registro->DataColacaoGrau;
                                $registro->data_expedicao_diploma = (string) $dados_livro_registro->DataExpedicaoDiploma;
                                $registro->data_registro_diploma = (string) $dados_livro_registro->DataRegistroDiploma;
                                
                                foreach($dados_livro_registro->ResponsavelRegistro as $dados_responsavel)
                                {
                                    $registro->nome_responsavel_registro = (string) $dados_responsavel->Nome;
                                    $registro->cpf_responsavel_registro = (string) $dados_responsavel->CPF;
                                }
                            }
                                                                                                
                            //TAG SEGURANÇA
                            foreach($tag_dados_registro->Seguranca as $dados_seguranca)
                            {
                                $registro->codigo_validacao_diploma = (string) $dados_seguranca->CodigoValidacao;
                            }
                                                    
                            //TAG INFORMAÇÕES ADICIONAIS
                            foreach($tag_dados_registro->InformacoesAdicionais as $dados_info_adicionais)
                            {
                                $registro->informacoes_adicionais_registradora = (string) $dados_info_adicionais;
                            }                       
                        }
                    }
                    else
                    {
                        //Percorre a tag DadosRegistroPorDecisaoJudicial
                        foreach($tags_diploma->DadosRegistroPorDecisaoJudicial as $tag_dados_registro) 
                        {
                            //Verifica se consta a assinatura do responsável pelo registro em DadosRegistro
                            foreach($tag_dados_registro->children($campos['ds']) as $assinatura_responsavel) 
                            {
                                if($assinatura_responsavel)
                                {
                                    $registro->status_assinatura_responsavel_registro = 1;
                                }
                            }                        
                            
                            //TAG REGISTRADORA
                            foreach($tag_dados_registro->IesRegistradora as $tag_dados_registradora)
                            {                            
                                $registro->nome_registradora = (string) $tag_dados_registradora->Nome;
                                $registro->codigo_mec_registradora = (string) $tag_dados_registradora->CodigoMEC;
                                $registro->cnpj_registradora = (string) $tag_dados_registradora->CNPJ;                            
                            }
                                                
                            //TAG LIVRO REGISTRO
                            foreach($tag_dados_registro->LivroRegistro as $dados_livro_registro)
                            {
                                $registro->livro_registro_dipl_registradora = (string) $dados_livro_registro->LivroRegistro;
                                $registro->num_registro_dipl_registradora = (string) $dados_livro_registro->NumeroRegistro;
                                $registro->folha_registro_dipl_registradora = (string) $dados_livro_registro->NumeroFolhaDoDiploma;
                                $registro->num_sequencia_dipl_registradora = (string) $dados_livro_registro->NumeroSequenciaDoDiploma;
                                $registro->num_processo_dipl_registradora = (string) $dados_livro_registro->ProcessoDoDiploma;
                                $registro->data_colacao_grau = (string) $dados_livro_registro->DataColacaoGrau;
                                $registro->data_expedicao_diploma = (string) $dados_livro_registro->DataExpedicaoDiploma;
                                $registro->data_registro_diploma = (string) $dados_livro_registro->DataRegistroDiploma;
                                
                                foreach($dados_livro_registro->ResponsavelRegistro as $dados_responsavel)
                                {
                                    $registro->nome_responsavel_registro = (string) $dados_responsavel->Nome;
                                    $registro->cpf_responsavel_registro = (string) $dados_responsavel->CPF;
                                }
                            }
                                                                                                
                            //TAG SEGURANÇA
                            foreach($tag_dados_registro->Seguranca as $dados_seguranca)
                            {
                                $registro->codigo_validacao_diploma = (string) $dados_seguranca->CodigoValidacao;
                            }
                            
                            //TAG DECLARAÇÃO REGISTRADORA PROCESSO JUDICIAL
                            foreach($tag_dados_registro->DeclaracoesRegistradoraAcercaProcesso as $dados_declaracao_registradora)
                            {
                                $registro->declaracao_registradora_processo = (string) $dados_declaracao_registradora->Declaracao;
                            }
                                                    
                            //TAG INFORMAÇÕES ADICIONAIS
                            foreach($tag_dados_registro->InformacoesAdicionais as $dados_info_adicionais)
                            {
                                $registro->informacoes_adicionais_registradora = (string) $dados_info_adicionais;
                            }                       
                        }
                    }
                }
                                
                //Verifica se consta a assinatura de arquivamento da registradora
                foreach($xml->children($campos['ds']) as $assinatura_registradora) 
                {
                    if($assinatura_registradora)
                    {
                        $registro->status_assinatura_arquivamento = 1;
                    }
                }

                
                //2º - Salva o arquivo no diretório com o nome recomendado pelo MEC
                if(!empty($registro->codigo_validacao_diploma))
                {
                    $source_file = $caminho_tmp;
                    $target_path = 'secretaria/diploma_xmls'; 
                    $target_file = $target_path . '/'. 'diploma-' . $registro->codigo_validacao_diploma . '.xml';
            
                    if (file_exists($source_file))
                    {
                        if (!file_exists($target_path))
                        {
                            if (!@mkdir($target_path, 0777, true))
                            {
                                throw new Exception(_t('Permission denied'). ': '. $target_path);
                            }
                        }
                            
                        if (file_exists($target_path))
                        {
                            rename($source_file, $target_file);
                        }
                    }
                }    
                else
                {
                    new TMessage('error', 'Falha ao gravar as informações do diploma. Tente novamente');
                    die;
                }                
                
                
                //3º - Se arquivo foi salvo, salva as informações em dados_diploma                                               
                if(file_exists($target_file))
                {    
                    //Assinatura secretária              
                    if($registro->status_assinatura_secretaria == '1')
                    {
                        $object->status_assinatura_secretaria = $registro->status_assinatura_secretaria;
                    }
                    else
                    {
                        $object->status_assinatura_secretaria = '0';
                    }
                
                    //Assinatura diretor
                    if($registro->status_assinatura_diretor == '1')
                    {
                        $object->status_assinatura_diretor = $registro->status_assinatura_diretor;
                    }
                    else
                    {
                        $object->status_assinatura_diretor = '0';
                    }
                
                    //Assinatura emissora
                    if($registro->status_assinatura_emissora == '1')
                    {
                        $object->status_assinatura_emissora = $registro->status_assinatura_emissora;
                    }
                    else
                    {
                        $object->status_assinatura_emissora = '0';
                    }
                    
                    //Assinatura responsável registro
                    if($registro->status_assinatura_responsavel_registro == '1')
                    {
                        $object->status_assinatura_responsavel_registro = $registro->status_assinatura_responsavel_registro;
                    }
                    else
                    {
                        $object->status_assinatura_responsavel_registro = '0';
                    }
                    
                    //Assinatura arquivamento registradora
                    if($registro->status_assinatura_arquivamento == '1')
                    {
                        $object->status_assinatura_arquivamento = $registro->status_assinatura_arquivamento;
                    }
                    else
                    {
                        $object->status_assinatura_arquivamento = '0';
                    }
                
                    $object->versao_xsd_diploma = (string) $xml->infDiploma['versao'];
                    $object->nome_registradora = $registro->nome_registradora;
                    $object->codigo_mec_registradora = $registro->codigo_mec_registradora;
                    $object->cnpj_registradora = $registro->cnpj_registradora;    
                    $object->livro_registro_dipl_registradora = $registro->livro_registro_dipl_registradora;
                    $object->num_registro_dipl_registradora = $registro->num_registro_dipl_registradora;
                    $object->folha_registro_dipl_registradora = $registro->folha_registro_dipl_registradora;
                    $object->num_sequencia_dipl_registradora = $registro->num_sequencia_dipl_registradora;
                    $object->num_processo_dipl_registradora = $registro->num_processo_dipl_registradora;
                    $object->data_conclusao_curso = $registro->data_conclusao_curso;
                    $object->data_colacao_grau = $registro->data_colacao_grau;
                    $object->data_expedicao_diploma = $registro->data_expedicao_diploma;
                    $object->data_registro_diploma = $registro->data_registro_diploma;
                    $object->informacoes_adicionais_registradora = $registro->informacoes_adicionais_registradora;
                    $object->nome_responsavel_registro = $registro->nome_responsavel_registro; 
                    $object->cpf_responsavel_registro = $registro->cpf_responsavel_registro;
                    $object->codigo_validacao_diploma = $registro->codigo_validacao_diploma;
                    $object->status_xml = 1; //1- Gerado                
                    $object->arquivo_registrado = 'diploma-' . $registro->codigo_validacao_diploma . '.xml';
                    $object->caminho_arquivo_registrado = $target_path;              
                    $object->system_user_id = TSession::getValue('userid');
                    $object->data_reg = date('Y-m-d H:i:s');
   
                    $object->store();  
                    
                    
                    //Se o diploma foi emitido por decisão judicial, salva a declaração da registradora
                    $processo = DiplomaDigitalProcessoJudicial::where('dados_documentacao_id', '=', $object->diploma_digital_documentacao->id)->load();
                                                            
                    if($processo)
                    {
                        $processo_judicial = new DiplomaDigitalProcessoJudicial($processo[0]->id);
                        
                        $processo_judicial->declaracao_registradora = $registro->declaracao_registradora_processo;
                        $processo_judicial->system_user_id = TSession::getValue('userid');
                        $processo_judicial->data_reg = date('Y-m-d H:i:s');
                        $processo_judicial->store();                          
                    }
                    
                    TTransaction::close(); 
                    
                    new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
                    TApplication::loadPage('DiplomaRegistradoList', 'onReload');
                }
                else
                {
                    new TMessage('error', 'Falha ao gravar as informações do diploma. Tente novamente');
                    die;
                }                                      
            } 
            else
            {
                new TMessage('error', 'Falha ao gravar as informações do diploma. Tente novamente');
                die;
            } 
            
            TTransaction::close();                                                         
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new DiplomaDigitalDiploma($key);
                
                if($object->status_diploma == 0)
                {
                    $action_cancelar = new TAction(['DiplomaRegistradoList', 'onReload']);
                    new TMessage('error', 'Não é possível alterar nenhum dado pertencente a um diploma registrado e anulado permanentemente', $action_cancelar);
                    die;
                }
                else
                {
                    $object->arquivo_registrado = "";
                }
                
                $this->form->setData($object);

                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
