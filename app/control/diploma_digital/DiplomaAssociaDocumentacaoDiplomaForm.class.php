<?php

class DiplomaAssociaDocumentacaoDiplomaForm extends TWindow
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Associar Documentação/Diploma');
        parent::setSize(0.7, null);


        //Cria registro na tabela dados_diploma. O diploma é gerado a partir do XML da documentação, então os assinantes serão os mesmos em ambos os arquivos        
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalDiploma');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $tipo_documento = new THidden('tipo_documento');
        $codigo_interliga_diploma_documentacao = new TEntry('codigo_interliga_diploma_documentacao');
        $dados_versao_id = new THidden('dados_versao_id');
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
        $arquivo_registrado = new THidden('arquivo_registrado');
        $caminho_arquivo_registrado = new THidden('caminho_arquivo_registrado');
        $qrcode = new THidden('qrcode');
        $caminho_qrcode = new THidden('caminho_qrcode');
        $codigo_validacao_diploma = new THidden('codigo_validacao_diploma');
        $url_diploma = new THidden('url_diploma');
        $status_publicacao = new THidden('status_publicacao');
        $data_publicacao = new THidden('data_publicacao');
        $system_user_id = new THidden('system_user_id', 'Felabs_DB', 'SystemUser', 'id', 'name');
        $data_reg = new THidden('data_reg');
        $nome_diplomado = new TEntry('nome_diplomado'); //Componente auxiliar, não será salvo no banco
        $nome_curso = new TEntry('nome_curso'); //Componente auxiliar, não será salvo no banco


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_documento ] );
        $this->form->addFields( [ $dados_versao_id ] );
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
        $this->form->addFields( [ $arquivo_registrado ] );
        $this->form->addFields( [ $caminho_arquivo_registrado ] );
        $this->form->addFields( [ $qrcode ] );
        $this->form->addFields( [ $caminho_qrcode ] );
        $this->form->addFields( [ $codigo_validacao_diploma ] );
        $this->form->addFields( [ $url_diploma ] );
        $this->form->addFields( [ $status_publicacao ] );
        $this->form->addFields( [ $data_publicacao ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Aluno'), $nome_diplomado ],
                                       [ new TLabel('Curso'), $nome_curso ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];  

        $row = $this->form->addFields( [ new TLabel('Código único que associa diploma à documentação'), $codigo_interliga_diploma_documentacao ]);
        $row->layout = ['col-sm-12'];
        

        $codigo_interliga_diploma_documentacao->addValidation('Cód. interliga diploma/documentação', new TRequiredValidator);
        $tipo_documento->addValidation('Tipo Documento', new TRequiredValidator);
        $dados_diplomado_id->addValidation('Aluno', new TRequiredValidator);
        $dados_curso_id->addValidation('Curso', new TRequiredValidator);
        $dados_emissora_id->addValidation('Dados Emissora ID', new TRequiredValidator);
        $dados_documentacao_id->addValidation('Dados Documentação ID', new TRequiredValidator);
        $user_id_assinatura_secretaria->addValidation('Secretária', new TRequiredValidator);
        $cpf_assinatura_secretaria->addValidation('CPF secretária', new TRequiredValidator);
        $user_id_assinatura_diretor->addValidation('Diretor', new TRequiredValidator);
        $cpf_assinatura_diretor->addValidation('CPF diretor', new TRequiredValidator);
        $unit_id_assinatura_emissora->addValidation('IES Emissora', new TRequiredValidator);
        $cnpj_assinatura_emissora->addValidation('CNPJ emissora', new TRequiredValidator);


        // set sizes
        $codigo_interliga_diploma_documentacao->setEditable(FALSE);
        $nome_diplomado->setSize('100%');
        $nome_diplomado->setEditable(FALSE);        
        $nome_curso->setSize('100%');
        $nome_curso->setEditable(FALSE); 


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }


        // create the form actions
        $btn = $this->form->addAction('Criar associação', new TAction([$this, 'onSave']), '');
        $btn->class = 'btn btn-sm btn-success';
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $this->form->validate(); 
            $data = $this->form->getData(); 
            
            $object = new DiplomaDigitalDiploma;  
            $object->fromArray( (array) $data); 
            
            
            //Não permite alteração nos dados caso o diploma registrado
            if($object->arquivo_registrado <> NULL OR $object->status_publicacao == 1) //1 - Publicado
            {
                throw new Exception ("Não é possível alterar nenhum dado vinculado a um diploma registrado");
                die;
            }
            
            
            if($object->status_diploma == NULL)
            {
                $object->status_diploma = 1; //0 - Inativo / 1 - Ativo
            }
            

            if($object->status_xml == NULL)
            {
                $object->status_xml = 0; //0 - Não gerado / 1 - Gerado
            }                  
            

            if($object->status_assinatura_secretaria == NULL)
            {
                $object->status_assinatura_secretaria = 0; //0 - Não preenchida / 1 - Preenchida
            }
            

            if($object->status_assinatura_diretor == NULL)
            {
                $object->status_assinatura_diretor = 0; //0 - Não preenchida / 1 - Preenchida
            }
            

            if($object->status_assinatura_emissora == NULL)
            {
                $object->status_assinatura_emissora = 0; //0 - Não preenchida / 1 - Preenchida
            }
            

            if($object->status_publicacao == NULL)
            {
                $object->status_publicacao = 0; //0 - Não publicado / 1 - Publicado
            }


            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');

            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('DiplomaDocumentacaoList', 'onReload');
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
                $this->form->setData($object); 
                                
                $obj = new StdClass;
                $obj->nome_diplomado = $object->diploma_digital_diplomado->nome;
                $obj->nome_curso = $object->diploma_digital_curso->nome_curso_diploma;
                                
                TForm::sendData('form_DiplomaDigitalDiploma', $obj);
                
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
    
    
    //Preenche os campos ocultos obrigatórios
    public function onShow($param)
    {   
        try
        {
            $dados_documentacao_id = $param['dados_documentacao_id'];
    
            TTransaction::open('Felabs_DB');
    
            $documentacao = new DiplomaDigitalDocumentacao($dados_documentacao_id);
            $diplomado = new DiplomaDigitalDiplomado($documentacao->dados_diplomado_id);
            $curso = new DiplomaDigitalCurso($documentacao->dados_curso_id);
            $polo = new DiplomaDigitalPolo($documentacao->dados_polo_id);            
            $emissora = new DiplomaDigitalEmissora($documentacao->dados_emissora_id);
            $processo_judicial = DiplomaDigitalProcessoJudicial::where('dados_documentacao_id', '=', $documentacao->id)->load();
            

            $obj = new StdClass;
            $obj->tipo_documento = "XMLDiplomado";
            $obj->codigo_interliga_diploma_documentacao = $documentacao->codigo_interliga_diploma_documentacao;
            $obj->dados_diplomado_id = $diplomado->id;
            $obj->nome_diplomado = $diplomado->nome;
            $obj->dados_curso_id = $curso->id;
            $obj->nome_curso = $curso->nome_curso_diploma;
            $obj->dados_polo_id = $polo->id;
            $obj->dados_emissora_id = $documentacao->dados_emissora_id;
            $obj->dados_documentacao_id = $documentacao->id;
            
            if($processo_judicial)
            {
                $obj->dados_processo_judicial_id = $processo_judicial[0]->id;    
            }
                        
            $obj->user_id_assinatura_secretaria = $documentacao->user_id_assinatura_secretaria;      
            $obj->cpf_assinatura_secretaria = $documentacao->cpf_assinatura_secretaria;
            $obj->user_id_assinatura_diretor = $documentacao->user_id_assinatura_diretor;
            $obj->cpf_assinatura_diretor = $documentacao->cpf_assinatura_diretor;
            $obj->unit_id_assinatura_emissora = $documentacao->unit_id_assinatura_emissora;
            $obj->cnpj_assinatura_emissora = $documentacao->cnpj_assinatura_emissora;
            
            TForm::sendData('form_DiplomaDigitalDiploma', $obj);
            
            $this->form->setData($obj);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
}
