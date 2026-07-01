<?php

class DiplomaLivroRegistroEmissoraForm extends TWindow
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Controle do Livro de Registro da Emissora');
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalLivroRegistroEmissora');
        $this->form->setFieldSizes('100%');
        $this->setSize(0.8, null);


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
        $livro_registro_dipl_emissora = new TEntry('livro_registro_dipl_emissora');
        $num_registro_dipl_emissora = new TEntry('num_registro_dipl_emissora');
        $folha_registro_dipl_emissora = new TEntry('folha_registro_dipl_emissora');
        $obs_registro_emissora = new TEntry('obs_registro_emissora');
        $nome_registradora = new TEntry('nome_registradora');
        $codigo_mec_registradora = new THidden('codigo_mec_registradora');
        $cnpj_registradora = new THidden('cnpj_registradora');
        $livro_registro_dipl_registradora = new TEntry('livro_registro_dipl_registradora');
        $num_registro_dipl_registradora = new TEntry('num_registro_dipl_registradora');
        $folha_registro_dipl_registradora = new TEntry('folha_registro_dipl_registradora');
        $num_sequencia_dipl_registradora = new TEntry('num_sequencia_dipl_registradora');
        $num_processo_dipl_registradora = new TEntry('num_processo_dipl_registradora');
        $data_conclusao_curso = new THidden('data_conclusao_curso');
        $data_colacao_grau = new THidden('data_colacao_grau');
        $data_expedicao_diploma = new TDate('data_expedicao_diploma');
        $data_registro_diploma = new TDate('data_registro_diploma');
        $informacoes_adicionais_registradora = new TEntry('informacoes_adicionais_registradora');
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
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        
        
        //Componentes auxiliares, não serão salvos no banco                
        $nome_emissora = new TEntry('nome_emissora');
        $nome_curso = new TEntry('nome_curso');
        $nome_diplomado = new TEntry('nome_diplomado');
        $ano_conclusao = new TEntry('ano_conclusao');
        $pai_diplomado = new TEntry('pai_diplomado');
        $mae_diplomado = new TEntry('mae_diplomado');
        $data_nascimento_diplomado = new TEntry('data_nascimento_diplomado');
        $municipio_diplomado = new TEntry('municipio_diplomado');
        $uf_diplomado = new TEntry('uf_diplomado');
        

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
        $this->form->addFields( [ $codigo_mec_registradora ] );
        $this->form->addFields( [ $cnpj_registradora ] );
        $this->form->addFields( [ $data_conclusao_curso ] );
        $this->form->addFields( [ $data_colacao_grau ] );
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
        
        $row = $this->form->addFields( [ new TLabel('Emissora'), $nome_emissora ],
                                       [ new TLabel('Registradora'), $nome_registradora ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Curso'), $nome_curso ],
                                       [ new TLabel('Ano de conclusão'), $ano_conclusao ],
                                       [ new TLabel('Data de expedição'), $data_expedicao_diploma ] );
        $row->layout = ['col-sm-6', 'col-sm-3', 'col-sm-3'];
        
        
        $row = $this->form->addFields( [ new TLabel('Nome'), $nome_diplomado ],
                                       [ new TLabel('Data de nascimento'), $data_nascimento_diplomado ],
                                       [ new TLabel('Naturalidade'), $municipio_diplomado ],
                                       [ new TLabel('UF'), $uf_diplomado ] );
        $row->layout = ['col-sm-5', 'col-sm-2', 'col-sm-4', 'col-sm-1'];
        
        
        $row = $this->form->addFields( [ new TLabel('Pai'), $pai_diplomado ],
                                       [ new TLabel('Mãe'), $mae_diplomado ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $this->form->addContent( ['<br>'] );
        
        $label1 = new TLabel('Informações de Registro - IES Registradora', '#285097', 11, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        
        $row = $this->form->addFields( [ new TLabel('Livro de registro'), $livro_registro_dipl_registradora ],
                                       [ new TLabel('Nº registro'), $num_registro_dipl_registradora ],
                                       [ new TLabel('Folha'), $folha_registro_dipl_registradora ],
                                       [ new TLabel('Nº sequência'), $num_sequencia_dipl_registradora ],
                                       [ new TLabel('Nº processo'), $num_processo_dipl_registradora ],
                                       [ new TLabel('Data de registro'), $data_registro_diploma ] );
        $row->layout = ['col-sm-3', 'col-sm-2', 'col-sm-1', 'col-sm-2', 'col-sm-2', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Observações registradora'), $informacoes_adicionais_registradora ] );
        $row->layout = ['col-sm-12'];
        
        $this->form->addContent( ['<br>'] );
        
        $label2 = new TLabel('Informações de Registro - IES Emissora', '#285097', 11, 'b', '<br>');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );
        
        $row = $this->form->addFields( [ new TLabel('Livro de registro'), $livro_registro_dipl_emissora ],
                                       [ new TLabel('Nº registro'), $num_registro_dipl_emissora ],
                                       [ new TLabel('Folha'), $folha_registro_dipl_emissora ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Observações emissora'), $obs_registro_emissora ] );
        $row->layout = ['col-sm-12'];


        $livro_registro_dipl_emissora->addValidation('Livro de registro', new TRequiredValidator);
        $num_registro_dipl_emissora->addValidation('Nº registro', new TRequiredValidator);
        $folha_registro_dipl_emissora->addValidation('Folha', new TRequiredValidator);
        

        // set sizes
        $nome_emissora->setEditable(FALSE);
        $nome_registradora->setEditable(FALSE);
        $nome_registradora->forceUpperCase();
        $nome_curso->setEditable(FALSE);
        $ano_conclusao->setEditable(FALSE);
        $data_expedicao_diploma->setEditable(FALSE);
        $data_expedicao_diploma->setMask('dd/mm/yyyy');
        $data_expedicao_diploma->setDatabaseMask('yyyy-mm-dd');
        $nome_diplomado->setEditable(FALSE);
        $pai_diplomado->setEditable(FALSE);
        $mae_diplomado->setEditable(FALSE);
        $data_nascimento_diplomado->setEditable(FALSE);
        $municipio_diplomado->setEditable(FALSE);
        $uf_diplomado->setEditable(FALSE);
        $livro_registro_dipl_registradora->setEditable(FALSE);
        $num_registro_dipl_registradora->setEditable(FALSE);
        $folha_registro_dipl_registradora->setEditable(FALSE);
        $num_sequencia_dipl_registradora->setEditable(FALSE);
        $num_processo_dipl_registradora->setEditable(FALSE);
        $data_registro_diploma->setEditable(FALSE);
        $data_registro_diploma->setMask('dd/mm/yyyy');
        $data_registro_diploma->setDatabaseMask('yyyy-mm-dd');
        $informacoes_adicionais_registradora->setEditable(FALSE);
        $num_registro_dipl_emissora->setMask('9!');
        $folha_registro_dipl_emissora->setMask('9!');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $btn->class = 'btn btn-sm btn-primary';
        
        
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

            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
                
            $object->store();
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
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
                               
                $this->form->setData($object);


                //Monta o objeto para exibição
                $obj = new StdClass;
                $obj->nome_emissora                    = mb_strtoupper($object->diploma_digital_emissora->nome);                
                $obj->nome_curso                       = mb_strtoupper($object->diploma_digital_curso->nome_curso_diploma);               
                $obj->ano_conclusao                    = substr($object->data_conclusao_curso, 0, -6);
                $obj->nome_diplomado                   = mb_strtoupper($object->diploma_digital_diplomado->nome);
                $obj->pai_diplomado                    = mb_strtoupper($object->diploma_digital_diplomado->nome_pai);
                $obj->mae_diplomado                    = mb_strtoupper($object->diploma_digital_diplomado->nome_mae);
                $obj->data_nascimento_diplomado        = TDate::date2br($object->diploma_digital_diplomado->data_nascimento);
                $obj->municipio_diplomado              = mb_strtoupper($object->diploma_digital_diplomado->naturalidade_nome_municipio);
                $obj->uf_diplomado                     = $object->diploma_digital_diplomado->naturalidade_uf;                
                $obj->nome_registradora                = $object->nome_registradora;
                $obj->livro_registro_dipl_registradora = $object->livro_registro_dipl_registradora;
                $obj->num_registro_dipl_registradora   = $object->num_registro_dipl_registradora;
                $obj->folha_registro_dipl_registradora = $object->folha_registro_dipl_registradora;
                $obj->num_sequencial_dipl_registradora = $object->num_sequencial_dipl_registradora;
                $obj->num_processo_dipl_registradora   = $object->num_processo_dipl_registradora;
                $obj->data_expedicao_diploma           = TDate::date2br($object->data_expedicao_diploma);
                $obj->data_registro_diploma            = TDate::date2br($object->data_registro_diploma);
                                
                TForm::sendData('form_DiplomaDigitalLivroRegistroEmissora', $obj);
                
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
