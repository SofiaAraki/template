<?php

class DiplomaPoloForm extends TPage
{
    protected $form;
        

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalPolo');
        $this->form->setFormTitle('<h4>Polo</h4>');
        $this->form->setFieldSizes('100%');


        //Para preenchimento do formulário
        $dados_curso_id = TSession::getValue('dados_curso_id');
       
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '=', $dados_curso_id));


        // create the form fields
        $id = new THidden('id');
        $nome_polo = new TEntry('nome_polo');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $codigo_municipio = new TSeekButton('codigo_municipio');
        $nome_municipio = new TEntry('nome_municipio');
        $uf = new TCombo('uf');
        $cep = new TEntry('cep');
        $opcao_codigo_emec = new TRadioGroup('opcao_codigo_emec');
        $codigo_polo_emec = new TEntry('codigo_polo_emec');
        $sem_codigo_emec_numero_processo = new TEntry('sem_codigo_emec_numero_processo');
        $sem_codigo_emec_tipo_processo = new TEntry('sem_codigo_emec_tipo_processo');
        $sem_codigo_emec_data_cadastro = new TDate('sem_codigo_emec_data_cadastro');
        $sem_codigo_emec_data_protocolo = new TDate('sem_codigo_emec_data_protocolo');
        $dados_curso_id = new TDBCombo('dados_curso_id', 'Felabs_DB', 'DiplomaDigitalCurso', 'id', 'nome_curso_diploma', 'nome_curso_diploma', $criteria);
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        //Buscar dados do município
        $codigo_municipio->setAction(new TAction(array('BuscaCidadePolo', 'onReload')));
        
        
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
        
        
        //Opção código EMEC
        $opcao_emec = [];
        $opcao_emec['Possui código EMEC'] = "Polo possui código E-MEC";
        $opcao_emec['Não possui código EMEC'] = "Polo não possui código E-MEC";

        $opcao_codigo_emec->addItems($opcao_emec);
        
        $opcao_codigo_emec->setChangeAction(new TAction(array($this, 'onOpcaoCodigoEmecChange')));
        

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        
        $row = $this->form->addFields( [ new TLabel('Curso <font color="red">*</font>'), $dados_curso_id ]);
        $row->layout = ['col-sm-12'];
                
        $this->form->addFields( [ new TLabel('Nome polo <font color="red">*</font>'), $nome_polo ] );        
        
        $this->form->addFields( [ '<br><br>' ] );
        

        //ENDEREÇO
        $label1 = new TLabel('Endereço', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        
        $row = $this->form->addFields( [ new TLabel('Logradouro <font color="red">*</font>'), $logradouro ],
                                       [ new TLabel('Nº <font color="red">*</font>'), $numero ],
                                       [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Bairro <font color="red">*</font>'), $bairro ] );
        $row->layout = ['col-sm-6', 'col-sm-1', 'col-sm-2', 'col-sm-3' ];

        $row = $this->form->addFields( [ new TLabel('Código município <font color="red">*</font>'), $codigo_municipio ],
                                       [ new TLabel('Nome do município <font color="red">*</font>'), $nome_municipio ],
                                       [ new TLabel('UF <font color="red">*</font>'), $uf ],
                                       [ new TLabel('CEP <font color="red">*</font>'), $cep ] );
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-1', 'col-sm-3' ];         
        
        $this->form->addFields( [ '<br><br>' ] );

        
        //EMEC
        $label2 = new TLabel('E-MEC', '#285097', 12, 'b', '<br>');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );
        
        $row = $this->form->addFields( [], [ $opcao_codigo_emec ], [] );
        $row->layout = ['col-sm-3', 'col-sm-6', 'col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Código EMEC'), $codigo_polo_emec ],
                                       [ new TLabel('Número processo'), $sem_codigo_emec_numero_processo ],
                                       [ new TLabel('Tipo processo'), $sem_codigo_emec_tipo_processo ],
                                       [ new TLabel('Data cadastro'), $sem_codigo_emec_data_cadastro ],
                                       [ new TLabel('Data protocolo'), $sem_codigo_emec_data_protocolo ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-2'];
        
        $this->form->addFields( [ '<br>' ] ); 
        $label3 = new TLabel('<font color="red">*</font> Campos obrigatórios (alguns campos são obrigatórios condicionais, ou seja, dependem da escolha do usuário no momento do preenchimento)', '', 10, 'i');
        $this->form->addContent( [$label3] );


        $dados_curso_id->addValidation('Curso', new TRequiredValidator);
        $nome_polo->addValidation('Nome polo', new TRequiredValidator);
        $logradouro->addValidation('Logradouro', new TRequiredValidator);
        $numero->addValidation('Nº', new TRequiredValidator);
        $bairro->addValidation('Bairro', new TRequiredValidator);
        $codigo_municipio->addValidation('Código município', new TRequiredValidator);
        $nome_municipio->addValidation('Nome do município', new TRequiredValidator);
        $uf->addValidation('UF', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);
        $opcao_codigo_emec->addValidation('Polo possui/não possui código E-MEC', new TRequiredValidator);


        // set sizes
        $dados_curso_id->setDefaultOption(FALSE);        
        $nome_municipio->setEditable(FALSE);
        $uf->setEditable(FALSE);
        $cep->setEditable(FALSE);
        $opcao_codigo_emec->setLayout('horizontal');
        $opcao_codigo_emec->setUseButton();
        $opcao_codigo_emec->setSize('100%');       
        $sem_codigo_emec_data_cadastro->setMask('dd/mm/yyyy');
        $sem_codigo_emec_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $sem_codigo_emec_data_protocolo->setMask('dd/mm/yyyy');
        $sem_codigo_emec_data_protocolo->setDatabaseMask('yyyy-mm-dd');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('DiplomaCursoList','onReload')), 'fas:arrow-alt-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        
        parent::add($container);
    }


    public static function onOpcaoCodigoEmecChange($param)
    {
        $opcao_codigo = $param['opcao_codigo_emec'] ?? [];      
        
        if($opcao_codigo == 'Possui código EMEC')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_numero_processo');
            TEntry::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_tipo_processo'); 
            TDate::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_cadastro'); 
            TDate::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_numero_processo');
            TEntry::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_tipo_processo');
            TDate::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_cadastro');
            TDate::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_protocolo');
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalPolo', 'codigo_polo_emec');      
        }
        elseif($opcao_codigo == 'Não possui código EMEC')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalPolo', 'codigo_polo_emec');

            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalPolo', 'codigo_polo_emec');
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_numero_processo');
            TEntry::enableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_tipo_processo');
            TDate::enableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_cadastro');
            TDate::enableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_protocolo');
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalPolo', 'codigo_polo_emec');
            TEntry::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_numero_processo');
            TEntry::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_tipo_processo'); 
            TDate::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_cadastro'); 
            TDate::clearField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalPolo', 'codigo_polo_emec');
            TEntry::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_numero_processo'); 
            TEntry::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_tipo_processo'); 
            TDate::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_cadastro');
            TDate::disableField('form_DiplomaDigitalPolo', 'sem_codigo_emec_data_protocolo');   
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');            
            
            $data = $this->form->getData();
            
            $object = new DiplomaDigitalPolo;
            $object->fromArray( (array) $data);
            
            $this->form->validate();
            
            //Controle campos condicionais - Código EMEC
            if($object->opcao_codigo_emec == 'Não possui código EMEC')
            {
                if((! $object->sem_codigo_emec_numero_processo) OR (! $object->sem_codigo_emec_tipo_processo)
                  OR (! $object->sem_codigo_emec_data_cadastro) OR (! $object->sem_codigo_emec_data_protocolo))
                {
                    throw new Exception("É necessário preencher todos os dados relacionados ao EMEC");
                }
            }
            
            if($object->opcao_codigo_emec == 'Possui código EMEC')
            {
                if(! $object->codigo_polo_emec)
                {
                    throw new Exception("É necessário preencher o código EMEC");
                }
            }
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store();
            
            
            //Se cadastrar polo, garante que a opcao_polo do cadastro do curso esteja com o valor correto
            $dados_curso = DiplomaDigitalCurso::find($object->dados_curso_id);
            
            if($dados_curso)
            {
                $dados_curso->opcao_polo = "Curso possui polo";
                $dados_curso->store();
            }

            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('DiplomaCursoList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            
            $param['opcao_codigo_emec'] = $object->opcao_codigo_emec;
            $this->onOpcaoCodigoEmecChange($param);
            
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
                
                $object = new DiplomaDigitalPolo($key);

                $param['opcao_codigo_emec'] = $object->opcao_codigo_emec;
                $this->onOpcaoCodigoEmecChange($param);
            
                $this->form->setData($object);
                
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
                
                $this->onOpcaoCodigoEmecChange($param);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
