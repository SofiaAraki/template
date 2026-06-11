<?php

class DadosCadastraisAlunoEditForm extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        /*try
        {
            TTransaction::open('Felabs_DB');
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            TTransaction::close();
            
            
            TTransaction::open('dados_fei');
            
            $aluno = new FiAluno($user->systemuser_codlegado);
            $cidade = new FiCidade($aluno->CodCidade);
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }*/
            
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContatoAluno');
        $this->form->setFormTitle('Meu Cadastro');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $cod_aluno = new THidden('cod_aluno');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $cidade = new TEntry('cidade');
        $uf = new TCombo('uf');
        $cep = new TEntry('cep');
        $telefone_1 = new TEntry('telefone_1');
        $telefone_2 = new TEntry('telefone_2');
        $telefone_3 = new TEntry('telefone_3');
        $contato_whatsapp = new TEntry('contato_whatsapp');
        $email = new TEntry('email');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


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
        
        $uf->addItems($combo_uf);
        

        //Campos apenas para exibição, não serão salvos no banco
        /*$Nome = new TEntry('Nome');
        $Datanascimento = new TEntry('Datanascimento');
        $Sexo = new TEntry('Sexo');
        $Naturalidade = new TEntry('Naturalidade');
        $NaturalidadeUF = new TEntry('NaturalidadeUF');
        $Nacionalidade = new TEntry('Nacionalidade');
        $NomePai = new TEntry('NomePai');
        $NomeMae = new TEntry('NomeMae');
        $Rg = new TEntry('Rg');
        $RgOrgaoExpedidor = new TEntry('RgOrgaoExpedidor');
        $CPF = new TEntry('CPF');
        $Profissao = new TEntry('Profissao');
        $CorRaca = new TEntry('CorRaca');
        $EstadoCivil = new TEntry('EstadoCivil');


        $text1  = new TTextDisplay($aluno->Nome, '#333333', '14px', '');
        $text2  = new TTextDisplay(TDate::date2br($aluno->Datanascimento), '#333333', '14px', '');
        $text3  = new TTextDisplay($aluno->Sexo, '#333333', '14px', '');
        $text4  = new TTextDisplay($aluno->Nacionalidade, '#333333', '14px', '');
        $text5  = new TTextDisplay($aluno->Naturalidade, '#333333', '14px', '');
        $text6  = new TTextDisplay($aluno->NaturalidadeUF, '#333333', '14px', '');
        $text7  = new TTextDisplay($aluno->Rg, '#333333', '14px', '');
        $text8  = new TTextDisplay($aluno->RgOrgaoExpedidor, '#333333', '14px', '');
        $text9  = new TTextDisplay($aluno->CPF, '#333333', '14px', '');
        $text10  = new TTextDisplay($aluno->Profissao, '#333333', '14px', '');
        $text11  = new TTextDisplay($aluno->EstadoCivil, '#333333', '14px', '');
        $text12  = new TTextDisplay($aluno->CorRaca, '#333333', '14px', '');        
        $text13  = new TTextDisplay($aluno->NomePai, '#333333', '14px', '');
        $text14  = new TTextDisplay($aluno->NomeMae, '#333333', '14px', '');
        
        
        $this->form->addFields( [new TFormSeparator('Informações gerais')] );
        
        $row = $this->form->addFields( [ new TLabel('Nome'), $text1 ],
                                       [ new TLabel('Data de nascimento'), $text2 ],
                                       [ new TLabel('Sexo'), $text3 ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $this->form->addContent( ['<br>'] );
        
        $row = $this->form->addFields( [ new TLabel('Nacionalidade'), $text4 ],
                                       [ new TLabel('Naturalidade'), $text5 ],
                                       [ new TLabel('UF'), $text6 ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $this->form->addContent( ['<br>'] );
        
        $row = $this->form->addFields( [ new TLabel('RG'), $text7 ],
                                       [ new TLabel('Órgão expeditor'), $text8 ],
                                       [ new TLabel('CPF'), $text9 ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $this->form->addContent( ['<br>'] );
        
        $row = $this->form->addFields( [ new TLabel('Profissão'), $text10 ],
                                       [ new TLabel('Estado civil'), $text11 ], 
                                       [ new TLabel('Cor/raça'), $text12 ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $this->form->addContent( ['<br>'] );
        
        $row = $this->form->addFields( [ new TLabel('Nome do pai'), $text13 ],
                                       [ new TLabel('Nome da mãe'), $text14 ] );
        $row->layout = ['col-sm-4', 'col-sm-4'];
      
        $this->form->addContent( ['<br>'] );*/  
      
        ///////////////////////////////////////////////////////////////////////////////
      
        // add the fields
        $label_explicacao = '<center><p style="font-size: 18px; vertical-align: middle;">Prezado(a) aluno(a), manter seus dados sempre atualizados em nosso cadastro é
                            muito importante, pois através deles nosso contato será mais ágil!</p></center>';        
                               
        
        $panel = new TPanelGroup();
        $panel->add($label_explicacao);
        
        $this->form->addContent( [ $panel ] );
        
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $cod_aluno ] );
                  
        $this->form->addFields( [new TFormSeparator('Informações para Contato')] );
        
        $row = $this->form->addFields( [ new TLabel('<font color="red">Endereço</font>'), $logradouro ],
                                       [ new TLabel('<font color="red">Nº</font>'), $numero ], 
                                       [ new TLabel('Complem.'), $complemento ],
                                       [ new TLabel('<font color="red">Bairro</font>'), $bairro ] );
        $row->layout = ['col-sm-5', 'col-sm-1', 'col-sm-2', 'col-sm-4'];
 
        $row = $this->form->addFields( [ new TLabel('<font color="red">Cidade</font>'), $cidade ],
                                       [ new TLabel('<font color="red">UF</font>'), $uf ], 
                                       [ new TLabel('<font color="red">CEP</font>'), $cep ] );
        $row->layout = ['col-sm-7', 'col-sm-2', 'col-sm-3'];
 
        $row = $this->form->addFields( [ new TLabel('<font color="red">Telefone 1</font>'), $telefone_1 ],
                                       [ new TLabel('Telefone 2'), $telefone_2 ], 
                                       [ new TLabel('Telefone 3'), $telefone_3 ],
                                       [ new TLabel('<font color="red">Contato Whatsapp</font>'), $contato_whatsapp ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('<font color="red">E-mail</font>'), $email ] );
        $row->layout = ['col-sm-12'];

        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        
        $this->form->addFields( [ '<br>' ] ); 
        $label1 = new TLabel('<font color="red">*</font> Campos obrigatórios', '', 10, 'i');
        $this->form->addContent( [$label1] );
        
        
        $logradouro->addValidation('Endereço', new TRequiredValidator);
        $numero->addValidation('Nº', new TRequiredValidator);
        $bairro->addValidation('Bairro', new TRequiredValidator);
        $cidade->addValidation('Cidade', new TRequiredValidator);
        $uf->addValidation('UF', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);
        $telefone_1->addValidation('Telefone1', new TRequiredValidator);
        $contato_whatsapp->addValidation('Contato Whatsapp', new TRequiredValidator);
        $email->addValidation('E-mail', new TRequiredValidator);                      
        $email->addValidation('E-mail', new TEmailValidator);
        
        
        // set sizes
        $logradouro->forceUpperCase();
        $numero->setMask('9!');
        $complemento->forceUpperCase();
        $bairro->forceUpperCase();
        $cidade->forceUpperCase();
        $cep->setMask('99.999-000');
        $telefone_1->setMask('(99)999999999');
        $telefone_2->setMask('(99)999999999');
        $telefone_3->setMask('(99)999999999');
        $contato_whatsapp->setMask('(99)999999999');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';

        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onSave( $param )
    {
        try
        {
            $userid = TSession::getValue('userid');
                        
            TTransaction::open('Felabs_DB'); 
                                   
            $this->form->validate();           
            $data = $this->form->getData(); 
                                     
            $object = new ContatoAluno;  
            $object->fromArray( (array) $data); 

            //Garante que o código do aluno vai ser salvo corretamente
            $user = new SystemUser($userid);
            
            $object->cod_aluno = $user->systemuser_codlegado;
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s'); 

            $object->store(); 
            
            $data->id = $object->id; 
            
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('DadosCadastraisView', 'onLoad');
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
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
                
                $object = new ContatoAluno($key); 
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
    
    
    public function onLoad( $param )
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $cod_aluno = $param['cod_aluno'];
            
            $aluno = new FiAluno($cod_aluno);
            $cidade = new FiCidade($aluno->CodCidade);
            
            $obj = new StdClass;
            $obj->cod_aluno   = $aluno->Codaluno;
            $obj->logradouro  = $aluno->Endereco;
            $obj->numero      = $aluno->EnderecoNumero;
            $obj->complemento = $aluno->EnderecoComplemeto;
            $obj->bairro      = $aluno->Bairro;
            $obj->cidade      = $cidade->Nome;
            $obj->uf          = $cidade->Uf;
            $obj->cep         = $aluno->Cep;
            $obj->telefone_1  = $aluno->Telefone;
            $obj->telefone_2  = $aluno->Telefone2;
            $obj->telefone_3  = $aluno->Telefone3;
            $obj->email       = $aluno->Email;

            TTransaction::close();
            
            $this->form->setData($obj);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
