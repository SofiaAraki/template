<?php

class DiplomaDiplomadoForm extends TPage
{
    protected $form;
    private $cod_aluno;

    public function __construct( $param )
    {
        parent::__construct();

        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalDiplomado');
        $this->form->setFormTitle('<h4>Aluno</h4>');
        $this->form->setFieldSizes('100%');

        //$this->cod_aluno para conseguir bloquear tseek na edição sem perder o valor do campo ou se cair num Exception

        // create the form fields
        $id = new THidden('id');
        $this->cod_aluno = new TSeekButton('cod_aluno'); 
        $nome = new TEntry('nome');
        $nome_social = new TEntry('nome_social');
        $sexo = new TCombo('sexo');
        $data_nascimento = new TDate('data_nascimento');
        $cpf = new TEntry('cpf');
        $documento_identificacao = new TCombo('documento_identificacao');
        $rg_numero = new TEntry('rg_numero');
        $rg_orgao_expedidor = new TEntry('rg_orgao_expedidor');
        $rg_uf = new TCombo('rg_uf');
        $outro_doc_tipo = new TEntry('outro_doc_tipo');
        $outro_doc_identificador = new TEntry('outro_doc_identificador');
        $opcao_nacionalidade = new TCombo('opcao_nacionalidade');
        $nacionalidade = new TEntry('nacionalidade');
        $naturalidade_cod_municipio = new TSeekButton('naturalidade_cod_municipio');
        $naturalidade_nome_municipio = new TEntry('naturalidade_nome_municipio');
        $naturalidade_uf = new TEntry('naturalidade_uf');
        $nome_pai = new TEntry('nome_pai');
        $nome_social_pai = new TEntry('nome_social_pai');
        $sexo_pai = new TCombo('sexo_pai');
        $nome_mae = new TEntry('nome_mae');
        $nome_social_mae = new TEntry('nome_social_mae');
        $sexo_mae = new TCombo('sexo_mae');              
        $email = new TEntry('email');                         
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        

        $this->cod_aluno->setAction(new TAction(array('BuscaDiplomado', 'onReload'))); 


        //Buscar dados do município
        $naturalidade_cod_municipio->setAction(new TAction(array('BuscaCidadeDiplomado', 'onReload'))); 


        //Se RG, habilita nº, órgão expedidor e uf. Se Outro, habilita tipo e identificador
        $documento_identificacao->setChangeAction(new TAction(array($this, 'onDocumentoChange')));
        
        
        //Se brasileiro(a), habilita código município, nome município e uf. Se outra, habilita somente o nome do município         
        $opcao_nacionalidade->setChangeAction(new TAction(array($this, 'onOpcaoNacionalidadeChange')));


        //Sexo
        $combo_sexo = [];
        $combo_sexo['M'] = "Masculino";
        $combo_sexo['F'] = "Feminino";
                
        $sexo->addItems($combo_sexo);
        $sexo_pai->addItems($combo_sexo);
        $sexo_mae->addItems($combo_sexo);


        //Documento identificação
        $combo_documento = [];
        $combo_documento['RG'] = "Utilizar RG";
        $combo_documento['Outro documento pessoal'] = "Utilizar outro documento pessoal";
        
        $documento_identificacao->addItems($combo_documento);

    
        //UF
        $combo_uf = [];
        $combo_uf['AC'] = "AC";
        $combo_uf['AL'] = "AL";
        $combo_uf['AM'] = "AM";
        $combo_uf['AP'] = "AP";
        $combo_uf['BA'] = "BA";
        $combo_uf['CE'] = "CE";
        $combo_uf['DF'] = "DF";
        $combo_uf['ES'] = "ES";
        $combo_uf['GO'] = "GO";
        $combo_uf['MA'] = "MA";
        $combo_uf['MG'] = "MG";
        $combo_uf['MS'] = "MS";
        $combo_uf['MT'] = "MT";
        $combo_uf['PA'] = "PA";
        $combo_uf['PB'] = "PB";
        $combo_uf['PE'] = "PE";
        $combo_uf['PI'] = "PI";
        $combo_uf['PR'] = "PR";
        $combo_uf['RJ'] = "RJ";
        $combo_uf['RN'] = "RN";
        $combo_uf['RO'] = "RO";
        $combo_uf['RR'] = "RR";
        $combo_uf['RS'] = "RS";
        $combo_uf['SC'] = "SC";
        $combo_uf['SE'] = "SE";
        $combo_uf['SP'] = "SP";
        $combo_uf['TO'] = "TO";
        
        $rg_uf->addItems($combo_uf);
        
        
        //Opção nacionalidade        
        $combo_opcao = [];
        $combo_opcao['Brasileiro'] = "BRASILEIRO (caso sexo seja masculino)";
        $combo_opcao['Brasileira'] = "BRASILEIRA (caso sexo seja feminino)";
        $combo_opcao['Outra nacionalidade'] = "Outra nacionalidade (caso aluno(a) estrangeiro(a))";
        
        $opcao_nacionalidade->addItems($combo_opcao);


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        
        $label1 = new TLabel('Dados Pessoais', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        
        $row = $this->form->addFields( [ new TLabel('Código aluno <font color="red">*</font>'), $this->cod_aluno ],
                                       [ new TLabel('Nome <font color="red">*</font>'), $nome ],
                                       [ new TLabel('Nome Social'), $nome_social ] );
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-5'];
                        
        $row = $this->form->addFields( [ new TLabel('Sexo <font color="red">*</font>'), $sexo ],
                                       [ new TLabel('Data de nascimento <font color="red">*</font>'), $data_nascimento ],
                                       [ new TLabel('CPF <font color="red">*</font>'), $cpf ],
                                       [ new TLabel('Documento de identificação do aluno <font color="red">*</font>'), $documento_identificacao ]);
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-3', 'col-sm-4'];
                
        $row = $this->form->addFields( [ new TLabel('RG (nº)'), $rg_numero ],
                                       [ new TLabel('RG (Órgão exp.)'), $rg_orgao_expedidor ],
                                       [ new TLabel('RG (UF)'), $rg_uf ],
                                       [ new TLabel('Tipo de documento'), $outro_doc_tipo ],
                                       [ new TLabel('Identificador'), $outro_doc_identificador ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-3', 'col-sm-3'];                               

        $row = $this->form->addFields( [ new TLabel('Nacionalidade <font color="red">*</font>'), $opcao_nacionalidade ],
                                       [ new TLabel('Nacionalidade (no diploma) <font color="red">*</font>'), $nacionalidade ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];                               

        $row = $this->form->addFields( [ new TLabel('Naturalidade'), $naturalidade_cod_municipio ],
                                       [ new TLabel('Nome do município <font color="red">*</font>'), $naturalidade_nome_municipio ],
                                       [ new TLabel('UF'), $naturalidade_uf ] );
        $row->layout = ['col-sm-3', 'col-sm-7', 'col-sm-2'];
        

        $this->form->addContent( ['<br>'] );


        $label2 = new TLabel('Filiação', '#285097', 12, 'b', '<br>');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );
        
        $row = $this->form->addFields( [ new TLabel('Nome pai'), $nome_pai ],
                                       [ new TLabel('Nome social pai'), $nome_social_pai ],
                                       [ new TLabel('Sexo pai'), $sexo_pai ] );
        $row->layout = ['col-sm-5', 'col-sm-5', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Nome mãe'), $nome_mae ],
                                       [ new TLabel('Nome social mãe'), $nome_social_mae ],
                                       [ new TLabel('Sexo mãe'), $sexo_mae ] );
        $row->layout = ['col-sm-5', 'col-sm-5', 'col-sm-2'];
        
        $this->form->addContent( ['<br><hr>'] );
        
        $row = $this->form->addFields( [ new TLabel('E-mail atualizado para envio das instruções de acesso à arquivos digitais (histórico, diploma e outros) <font color="red">*</font>'), $email ] );
        $row->layout = ['col-sm-12'];


        // set sizes       
        $nome->forceUpperCase();       
        $nome_social->forceUpperCase(); 
        $cpf->setMask('99999999999');
        $data_nascimento->setMask('dd/mm/yyyy');
        $data_nascimento->setDatabaseMask('yyyy-mm-dd');
        $rg_numero->setMask('A!');
        $rg_numero->forceUpperCase();
        $rg_orgao_expedidor->forceUpperCase();
        $rg_orgao_expedidor->setTip("Prezado usuário, neste campo insira <b>somente a sigla</b> da instituição responsável pela emissão do documento. (Ex: SSP; PC; DPF, entre outras)");
        $rg_orgao_expedidor->placeholder = "Ex: SSP; PC; DPF";        
        $naturalidade_nome_municipio->forceUpperCase();
        $naturalidade_uf->forceUpperCase();
        $naturalidade_uf->setMask('AA');
        $nome_pai->forceUpperCase();
        $nome_social_pai->forceUpperCase();
        $nome_mae->forceUpperCase();
        $nome_social_mae->forceUpperCase();                       
          

        $this->cod_aluno->addValidation('Código aluno', new TRequiredValidator);
        $nome->addValidation('Nome', new TRequiredValidator);
        //$nome_social->addValidation('Nome social', new TRequiredValidator); -- A obrigatoriedade foi retirada a pedido da UFScar (Pamella 25/11/25)
        $sexo->addValidation('Sexo', new TRequiredValidator);
        $data_nascimento->addValidation('Data de nascimento', new TRequiredValidator);
        $cpf->addValidation('CPF', new TRequiredValidator);
        $cpf->addValidation('CPF', new TCPFValidator);
        $documento_identificacao->addValidation('Documento de identificação do aluno', new TRequiredValidator);
        $opcao_nacionalidade->addValidation('Nacionalidade', new TRequiredValidator);
        $nacionalidade->addValidation('Nacionalidade (no diploma)', new TRequiredValidator);
        $naturalidade_nome_municipio->addValidation('Nome do município', new TRequiredValidator);
        $email->addValidation('E-mail', new TRequiredValidator);
        $email->addValidation('E-mail', new TEmailValidator);


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
         
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('DiplomaDiplomadoList','onReload')), 'fas:arrow-alt-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onDocumentoChange($param)
    {
        $documento = $param['documento_identificacao'] ?? null;

        if($documento == 'RG')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'outro_doc_tipo');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'outro_doc_identificador');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'outro_doc_tipo');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'outro_doc_identificador');  
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalDiplomado', 'rg_numero');
            TEntry::enableField('form_DiplomaDigitalDiplomado', 'rg_orgao_expedidor'); 
            TCombo::enableField('form_DiplomaDigitalDiplomado', 'rg_uf');     
            
            //RECARREGA
            $combo_uf = [];
            $combo_uf['AC'] = "AC";
            $combo_uf['AL'] = "AL";
            $combo_uf['AM'] = "AM";
            $combo_uf['AP'] = "AP";
            $combo_uf['BA'] = "BA";
            $combo_uf['CE'] = "CE";
            $combo_uf['DF'] = "DF";
            $combo_uf['ES'] = "ES";
            $combo_uf['GO'] = "GO";
            $combo_uf['MA'] = "MA";
            $combo_uf['MG'] = "MG";
            $combo_uf['MS'] = "MS";
            $combo_uf['MT'] = "MT";
            $combo_uf['PA'] = "PA";
            $combo_uf['PB'] = "PB";
            $combo_uf['PE'] = "PE";
            $combo_uf['PI'] = "PI";
            $combo_uf['PR'] = "PR";
            $combo_uf['RJ'] = "RJ";
            $combo_uf['RN'] = "RN";
            $combo_uf['RO'] = "RO";
            $combo_uf['RR'] = "RR";
            $combo_uf['RS'] = "RS";
            $combo_uf['SC'] = "SC";
            $combo_uf['SE'] = "SE";
            $combo_uf['SP'] = "SP";
            $combo_uf['TO'] = "TO"; 
            
            TCombo::reload('form_DiplomaDigitalDiplomado', 'rg_uf', $combo_uf, TRUE);   
        }
        elseif($documento == 'Outro documento pessoal')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'rg_numero');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'rg_orgao_expedidor'); 
            TCombo::clearField('form_DiplomaDigitalDiplomado', 'rg_uf'); 
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'rg_numero');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'rg_orgao_expedidor'); 
            TCombo::disableField('form_DiplomaDigitalDiplomado', 'rg_uf');
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalDiplomado', 'outro_doc_tipo');
            TEntry::enableField('form_DiplomaDigitalDiplomado', 'outro_doc_identificador');   
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'rg_numero');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'rg_orgao_expedidor'); 
            TCombo::clearField('form_DiplomaDigitalDiplomado', 'rg_uf'); 
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'outro_doc_tipo');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'outro_doc_identificador');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'rg_numero');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'rg_orgao_expedidor'); 
            TCombo::disableField('form_DiplomaDigitalDiplomado', 'rg_uf'); 
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'outro_doc_tipo');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'outro_doc_identificador');   
        }
    }
        
    
    public static function onOpcaoNacionalidadeChange($param)
    {
        $nacionalidade = $param['opcao_nacionalidade'] ?? null;

        if($nacionalidade == 'Brasileira' OR $nacionalidade == 'Brasileiro')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'nacionalidade');
            TSeekButton::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_cod_municipio');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_nome_municipio');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_uf');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'nacionalidade');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'naturalidade_nome_municipio');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'naturalidade_uf');
            
            //HABILITA            
            TSeekButton::enableField('form_DiplomaDigitalDiplomado', 'naturalidade_cod_municipio');
            
                                                 
            $obj = new StdClass;
            $obj->nacionalidade = $nacionalidade;
            
            TForm::sendData('form_DiplomaDigitalDiplomado', $obj);
        }
        elseif($nacionalidade == 'Outra nacionalidade')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'nacionalidade');
            TSeekButton::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_cod_municipio');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_nome_municipio');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_uf');
            
            //DESABILITA
            TSeekButton::disableField('form_DiplomaDigitalDiplomado', 'naturalidade_cod_municipio');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'naturalidade_uf');  
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalDiplomado', 'nacionalidade');
            TEntry::enableField('form_DiplomaDigitalDiplomado', 'naturalidade_nome_municipio');   
        }                
        else
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'nacionalidade');
            TSeekButton::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_cod_municipio');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_nome_municipio');
            TEntry::clearField('form_DiplomaDigitalDiplomado', 'naturalidade_uf');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'nacionalidade');
            TSeekButton::disableField('form_DiplomaDigitalDiplomado', 'naturalidade_cod_municipio');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'naturalidade_nome_municipio');
            TEntry::disableField('form_DiplomaDigitalDiplomado', 'naturalidade_uf');
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');            
                        
            $data = $this->form->getData();

            $object = new DiplomaDigitalDiplomado;
            $object->fromArray( (array) $data);

            $this->form->validate();
            
            
            //Controle campos condicionais - Documento de identificação
            if($object->documento_identificacao == 'RG')
            {
                if((! $object->rg_numero) OR (! $object->rg_orgao_expedidor) OR (! $object->rg_uf))
                {
                    throw new Exception('É necessário preencher todos os dados relacionados ao RG');
                }
            }
            else
            {
                if((! $object->outro_doc_tipo) OR (! $object->outro_doc_identificador))
                {
                    throw new Exception('É necessário preencher todos os dados relacionados ao documento pessoal');
                }
            }


            //Controle campos condicionais - Nacionalidade
            if($object->opcao_nacionalidade == 'Brasileiro' OR $object->opcao_nacionalidade == 'Brasileira')
            {
                if((! $object->naturalidade_cod_municipio) OR (! $object->naturalidade_nome_municipio) OR (! $object->naturalidade_uf))
                {                                       
                    throw new Exception('É necessário preencher todos os dados relacionados a naturalidade');
                }
            }
            else
            {
                if((! $object->nacionalidade) OR (! $object->naturalidade_nome_municipio))
                {
                    throw new Exception('É necessário preencher todos os dados relacionados a naturalidade');
                }
            }
            
            
            //Controle campos condicionais - Filiação
            if(((! $object->nome_pai) OR (! $object->nome_social_pai) OR (! $object->sexo_pai)) AND ((! $object->nome_mae) OR (! $object->nome_social_mae) OR (! $object->sexo_mae)))
            {
                throw new Exception('É necessário preencher os dados de pelo menos um genitor');
            }

            
            //Se está salvando um "novo registro", mas já existe registro com mesmo diplomado
            if(empty($data->id))
            {
                $cpf_sem_caracteres = str_replace(array(".", "-", " "), "", $data->cpf); 
                
                $criteria = new TCriteria;
                $criteria->add(new TFilter('cod_aluno', '=', $data->cod_aluno), TExpression::OR_OPERATOR); 
                $criteria->add(new TFilter('cpf', '=', $cpf_sem_caracteres), TExpression::OR_OPERATOR); 

                $repository = new TRepository('DiplomaDigitalDiplomado'); 
                $registros_bd = $repository->load($criteria);
            
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro deste mesmo aluno");
                }
            }
            

            $object->cpf = str_replace(array(".", "-", " "), "", $object->cpf);            
            $object->rg_numero = str_replace(array(".", ",", "-", "/", " "), "", $object->rg_numero);
            $object->naturalidade_nome_municipio = str_replace(array("-", "(", ")"), array(" ", " ", " "), $object->naturalidade_nome_municipio);     
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');


            $object->store();
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('DiplomaDiplomadoList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            
            //Se estiver editando registro e cair na exceção, mantém campo bloqueado. Se estiver salvando novo registro, mantém desbloqueado
            if(!empty($param['id']))
            {
                $this->cod_aluno->setEditable(FALSE);  
            }
            
            $this->fireEvents($object);         
                     
            TTransaction::rollback();
        }
    }


    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    
    public function fireEvents( $object )
    {
        $obj = new StdClass;
        $obj->id = $object->id;
        
        if(!empty($object->id))
        {
            $this->cod_aluno->setEditable(FALSE);  
        }
        
        $obj->nome = $object->nome;
        $obj->nome_social = $object->nome_social;
        $obj->sexo = $object->sexo;
        $obj->opcao_nacionalidade = $object->opcao_nacionalidade;
        $obj->nacionalidade = $object->nacionalidade;
        $obj->naturalidade_cod_municipio = $object->naturalidade_cod_municipio;
        $obj->naturalidade_nome_municipio = $object->naturalidade_nome_municipio;
        $obj->naturalidade_uf = $object->naturalidade_uf;
        $obj->cpf = $object->cpf;
        $obj->documento_identificacao = $object->documento_identificacao;
        $obj->rg_numero = $object->rg_numero;
        $obj->rg_orgao_expedidor = $object->rg_orgao_expedidor;
        $obj->rg_uf = $object->rg_uf;
        $obj->outro_doc_tipo = $object->outro_doc_tipo;
        $obj->outro_doc_identificador = $object->outro_doc_identificador;
        $obj->data_nascimento = TDate::date2br($object->data_nascimento);
        $obj->nome_pai = $object->nome_pai;
        $obj->nome_social_pai = $object->nome_social_pai;
        $obj->sexo_pai = $object->sexo_pai;
        $obj->nome_mae = $object->nome_mae;
        $obj->nome_social_mae = $object->nome_social_mae;
        $obj->sexo_mae = $object->sexo_mae;
        $obj->email = $object->email;        
                
        TForm::sendData('form_DiplomaDigitalDiplomado', $obj);
        
        $param['documento_identificacao'] = $obj->documento_identificacao;
        $this->onDocumentoChange($param);
        
        $param['opcao_nacionalidade'] = $obj->opcao_nacionalidade;
        $this->onOpcaoNacionalidadeChange($param);
    }
    
  
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new DiplomaDigitalDiplomado($key);
                                
                $this->form->setData($object);
                
                $this->cod_aluno->setEditable(FALSE);
                
                TTransaction::close();
                
                $this->fireEvents($object);                
            }
            else
            {
                $this->form->clear(TRUE);
                
                //Campos dependentes iniciam desabilitados
                $this->onDocumentoChange($param);
                $this->onOpcaoNacionalidadeChange($param);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}


