<?php

class DiplomaAlterarStatusForm extends TPage
{
    protected $form;
        

    public function __construct( $param )
    {
        parent::__construct();
          
                
        //ANULA O DIPLOMA PERMANENTEMENTE
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_AlterarStatusDiploma');
        $this->form->setFormTitle('<h4>Anulação do diploma</h4>');
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
        $status_diploma = new TRadioGroup('status_diploma');
        $data_anulacao = new TDate('data_anulacao');
        $motivo_anulacao = new TRadioGroup('motivo_anulacao');
        $anotacao_anulacao = new TText('anotacao_anulacao');
        $anulacao_system_user_id = new THidden('anulacao_system_user_id');
        $anulacao_data_reg = new THidden('anulacao_data_reg');
        $status_xml = new THidden('status_xml');
        $arquivo_registrado = new THidden('arquivo_registrado');
        $caminho_arquivo_registrado = new THidden('caminho_arquivo_registrado');
        $qrcode = new THidden('qrcode');
        $caminho_qrcode = new THidden('caminho_qrcode');
        $codigo_validacao_diploma = new TEntry('codigo_validacao_diploma');
        $url_diploma = new THidden('url_diploma');
        $status_publicacao = new THidden('status_publicacao');
        $data_publicacao = new THidden('data_publicacao');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
               

        //Apenas para exibição, não será salvo no banco
        $nome_diplomado = new TEntry('nome_diplomado'); 
        $nome_curso = new TEntry('nome_curso'); 


        $radio_status = [];
        $radio_status[0] = "Inativo";
        $radio_status[1] = "Ativo";
                
        $status_diploma->addItems($radio_status);
        
        
        //Definidos pelo MEC
        $radio_motivo = [];
        $radio_motivo['Erro de Fato'] = "Erro de Fato";
        $radio_motivo['Erro de Direito'] = "Erro de Direito";
        $radio_motivo['Decisão Judicial'] = "Decisão Judicial";
        $radio_motivo['Reemissão para Complemento de Informação'] = "Reemissão para Complemento de Informação";
        $radio_motivo['Reemissão para Inclusão de Habilitação'] = "Reemissão para Inclusão de Habilitação";
        $radio_motivo['Reemissão para Anotaçao de Registro'] = "Reemissão para Anotaçao de Registro";
        
        $motivo_anulacao->addItems($radio_motivo);

                        
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
        $this->form->addFields( [ $anulacao_system_user_id ] );
        $this->form->addFields( [ $anulacao_data_reg ] );
        $this->form->addFields( [ $status_xml ] );
        $this->form->addFields( [ $arquivo_registrado ] ); 
        $this->form->addFields( [ $caminho_arquivo_registrado ] );
        $this->form->addFields( [ $qrcode ] );
        $this->form->addFields( [ $caminho_qrcode ] );
        $this->form->addFields( [ $url_diploma ] );
        $this->form->addFields( [ $status_publicacao ] );
        $this->form->addFields( [ $data_publicacao ] );
        $this->form->addFields( [ $system_user_id ] );        
        $this->form->addFields( [ $data_reg ] );
        
        
        $label_explicacao = '<center><b><p style="color:red; font-size: 20px;">Atenção</p></b></center>                            
                             <p style="font-size: 16px;">O processo para anulação do diploma deverá observar cumulativamente o seguinte:</p>
                             <p style="font-size: 16px;"><b>- Estar devidamente motivado, amparado em sólidos fundamentos e ocorrer de forma a respeitar as normas vigentes</b></p>
                             <p style="font-size: 16px;"><b>- Realizar todas as ações necessárias para invalidar todos os efeitos do correspondente diploma</b></p>
                             <p style="font-size: 16px;">- A IES que anular um diploma digital <b>DEVE PERMITIR</b> a consulta ao código inválido deixando claro seu status de inativo,
                             porém, o mesmo <b>NÃO DEVE</b> trazer dados acerca do diploma em si, a fim de preservar a privacidade do diplomado. A não disponibilização das informações
                             visa atender aos requisitos de privacidade da <b>Lei Geral de Proteção de Dados</b>.</p>';                                
        
        $panel = new TPanelGroup();
        $panel->add($label_explicacao);
        
        
        $this->form->addContent( [ $panel ] );
        $this->form->addContent( [ '<hr>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Diplomado'), $nome_diplomado ],
                                       [ new TLabel('Curso'), $nome_curso ], 
                                       [ new TLabel('Código de validação do diploma'), $codigo_validacao_diploma ] );
        $row->layout = ['col-sm-5', 'col-sm-4', 'col-sm-3'];
        
        $this->form->addContent( [ '<br>' ] );
        
        $this->form->addFields( [ new TLabel('Alterar status atual do diploma para'), $status_diploma ] ); 
        
        $this->form->addContent( [ '<br>' ] );
        
        $this->form->addFields( [ new TLabel('Inativar diploma por'), $motivo_anulacao ] );
        
        $this->form->addContent( [ '<br>' ] );       
        
        $this->form->addFields( [ new TLabel('Anotação'), $anotacao_anulacao ] );
        

        // set sizes
        $nome_diplomado->setEditable(FALSE);
        $nome_curso->setEditable(FALSE);
        $codigo_validacao_diploma->setEditable(FALSE);
        $status_diploma->setLayout('horizontal');
        $status_diploma->setSize(150);


        $status_diploma->addValidation("'Alterar status atual do diploma para'", new TRequiredValidator);
        $motivo_anulacao->addValidation("'Inativar diploma por'", new TRequiredValidator);
        $anotacao_anulacao->addValidation('Anotação', new TRequiredValidator);
        

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }


        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        $this->form->addAction('Voltar', new TAction(array('DiplomaRegistradoList','onReload')), 'fas:arrow-alt-circle-left blue');

        
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


            //Só é possível anular um diploma registrado
            if($object->arquivo_registrado == NULL)
            {
                throw new Exception("Só é possível anular um diploma registrado");
            }


            //Se usuário marcou por engano admin pode alterar desde que satisfaça algumas condições 
            $grupo_admin = 1;
            $user_groups = TSession::getValue('usergroupids');
            
            //Pega o status do diploma registrado no banco, pois o do formulário pode ter sido alterado
            $diploma = new DiplomaDigitalDiploma($object->id);
            
            //Verifica se há outro registro de documentação/diploma do mesmo aluno e curso ATIVO
            $verifica_documentacao = DiplomaDigitalDocumentacao::where('dados_diplomado_id', '=', $object->dados_diplomado_id)
                                                               ->where('dados_curso_id', '=', $object->dados_curso_id)
                                                               ->where('status_documentacao', '=', 1)
                                                               ->load();    
                                          
            $verifica_diploma = DiplomaDigitalDiploma::where('dados_diplomado_id', '=', $object->dados_diplomado_id)
                                                     ->where('dados_curso_id', '=', $object->dados_curso_id)
                                                     ->where('status_diploma', '=', 1)
                                                     ->load();
                                                
            if(($diploma->status_diploma == 0) AND (in_array($grupo_admin, $user_groups)) AND ($data->status_diploma == 1))
            {
                if((! $verifica_documentacao) AND (! $verifica_diploma))
                {
                    $object->status_diploma = 1; //Ativo
                    $object->data_anulacao = '';
                    $object->motivo_anulacao = '';                    
                    $object->anotacao_anulacao = '';
                    $object->anulacao_system_user_id = '';
                    $object->anulacao_data_reg = '';
                    $object->system_user_id = TSession::getValue('userid');
                    $object->data_reg = date('Y-m-d H:i:s');
    
                    $object->store();
                    
                    //Se o diploma volta a ser ativo, o status da documentação correspondente volta a ser ativo
                    $documentacao = new DiplomaDigitalDocumentacao($object->dados_documentacao_id);
        
                    $documentacao->status_documentacao = 1; //Ativa
                    $documentacao->store();
                    
                    new TMessage('info', 'Diploma reativado com sucesso');
                }
                else
                {
                    throw new Exception("Não é possível alterar os dados de anulação, pois um outro registro de documentação/diploma com este mesmo aluno e curso está ativo");
                    die;
                }        
            }
            
            //Se não for o caso, segue o fluxo normalmente 
            else
            {
                $object->status_diploma = 0; //Inativo                
                $object->data_anulacao = date('Y-m-d');
                $object->anulacao_system_user_id = TSession::getValue('userid');
                
                if($object->anulacao_data_reg == NULL)
                {
                    $object->anulacao_data_reg = date('Y-m-d H:i:s');
                }
                
                $object->system_user_id = TSession::getValue('userid');
                $object->data_reg = date('Y-m-d H:i:s');
                
                $object->store();
                
                //Se o diploma foi inativado, o status da documentação correspondente passa a ser inativo, o que bloqueia algumas ações
                $documentacao = new DiplomaDigitalDocumentacao($object->dados_documentacao_id);
    
                $documentacao->status_documentacao = 0; //Inativa
                $documentacao->store();  
                
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            }


            $data->id = $object->id;
            
            $this->form->setData($data);
            
            TTransaction::close();
            
            TApplication::loadPage('DiplomaRegistradoList', 'onReload');
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
                
                
                //Não permite nem a exibição do formulário caso o diploma tenha sido anulado permanentemente e o usuário não seja admin
                $grupo_admin = 1;
                $user_groups = TSession::getValue('usergroupids');
        
                if(($object->status_diploma == 0) AND (!in_array($grupo_admin, $user_groups)))
                {
                    $action_cancelar = new TAction(['DiplomaRegistradoList', 'onReload']);
                    new TMessage('error', 'Não é possível alterar nenhum dado pertencente a um diploma registrado e anulado permanentemente', $action_cancelar);
                    die;
                }
                          
                              
                $object->nome_diplomado = $object->diploma_digital_diplomado->nome;
                $object->nome_curso = $object->diploma_digital_curso->nome_curso_diploma;
                                
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
