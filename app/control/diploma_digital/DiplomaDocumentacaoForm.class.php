<?php

class DiplomaDocumentacaoForm extends TPage
{
    protected $form;    
    private $dados_diplomado_id;
    private $dados_curso_id;


    public function __construct( $param )
    {
        parent::__construct();


        //$this->dados_diplomado_id e $this->dados_curso_id para conseguir bloquear campo na edição sem perder o valor ou se cair num Exception


        //Filtra os cursos de acordo com a unidade logada
        $unit_id = TSession::getValue('userunitid');
                
        TTransaction::open('Felabs_DB');        
        
        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
        
        
        //Filtro para evitar pré-carregamento da combo polo
        $criteria2 = new TCriteria;
        $criteria2->add(new TFilter('id', '<', '0'));
        
        TTransaction::close();    
        
        
        //Criteria para filtrar os assinantes pelo id (assinatura secretária)
        $array_assinantes_secretaria = [];
        $array_assinantes_secretaria = ['1806' => '1806', '1816' => '1816'];
        //1806 - D. Vilma, 1816 - Tânia

        $criteria3 = new TCriteria;
        $criteria3->add(new TFilter('id', 'IN', $array_assinantes_secretaria));
        
        
        //Criteria para filtrar os assinantes pelo id (assinatura diretor)
        $array_assinantes_diretor = [];
        $array_assinantes_diretor = ['27' => '27', '11' => '11', '17' => '17'];
        //27 - Luciana, 11 - Márcio, 17 - Betô

        $criteria4 = new TCriteria;
        $criteria4->add(new TFilter('id', 'IN', $array_assinantes_diretor));
        
               
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalDocumentacao');
        $this->form->setFormTitle('<h4>Documentação Acadêmica para Registro de Diploma</h4>');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $tipo_documento = new THidden('tipo_documento');
        $codigo_interliga_diploma_documentacao = new THidden('codigo_interliga_diploma_documentacao');
        $status_documentacao = new THidden('status_documentacao');
        $opcao_via = new TRadioGroup('opcao_via');
        $dados_versao_id = new THidden('dados_versao_id');
        $this->dados_diplomado_id = new TDBUniqueSearch('dados_diplomado_id', 'Felabs_DB', 'DiplomaDigitalDiplomado', 'id', 'nome');
        $this->dados_curso_id = new TDBCombo('dados_curso_id', 'Felabs_DB', 'DiplomaDigitalCurso', 'id', '({codigo_curso_sistema}) {nome_curso_diploma}', 'nome_curso_diploma', $criteria1);
        $dados_polo_id = new TCombo('dados_polo_id', 'Felabs_DB', 'DiplomaDigitalPolo', 'id', 'nome_polo', 'nome_polo', $criteria2);
        $dados_emissora_id = new THidden('dados_emissora_id');
        $status_xml = new THidden('status_xml');
        $tipo_assinante_secretaria = new THidden('tipo_assinante_secretaria');    
        $user_id_assinatura_secretaria = new TDBCombo('user_id_assinatura_secretaria', 'Felabs_DB', 'SystemUser', 'id', 'name', 'name', $criteria3);
        $cpf_assinatura_secretaria = new TEntry('cpf_assinatura_secretaria');
        $opcao_cargo_secretaria = new TRadioGroup('opcao_cargo_secretaria');
        $cargo_mec_secretaria = new TCombo('cargo_mec_secretaria');
        $outro_cargo_secretaria = new TEntry('outro_cargo_secretaria');
        $status_assinatura_secretaria = new THidden('status_assinatura_secretaria');
        $data_exp_certificado_secretaria = new THidden('data_exp_certificado_secretaria');
        $tipo_assinante_diretor = new THidden('tipo_assinante_diretor');
        $user_id_assinatura_diretor = new TDBCombo('user_id_assinatura_diretor', 'Felabs_DB', 'SystemUser', 'id', 'name', 'name', $criteria4);
        $cpf_assinatura_diretor = new TEntry('cpf_assinatura_diretor');
        $opcao_cargo_diretor = new TRadioGroup('opcao_cargo_diretor');
        $cargo_mec_diretor = new TCombo('cargo_mec_diretor');
        $outro_cargo_diretor = new TEntry('outro_cargo_diretor');
        $status_assinatura_diretor = new THidden('status_assinatura_diretor');
        $data_exp_certificado_diretor = new THidden('data_exp_certificado_diretor');
        $tipo_assinante_emissora = new THidden('tipo_assinante_emissora');
        $unit_id_assinatura_emissora = new THidden('unit_id_assinatura_emissora');
        $cnpj_assinatura_emissora = new THidden('cnpj_assinatura_emissora');
        $status_assinatura_emissora = new THidden('status_assinatura_emissora');
        $data_exp_certificado_emissora = new THidden('data_exp_certificado_emissora');
        $tipo_assinante_arquivamento = new THidden('tipo_assinante_arquivamento');
        $unit_id_assinatura_arquivamento = new THidden('unit_id_assinatura_arquivamento');
        $cnpj_assinatura_arquivamento = new THidden('cnpj_assinatura_arquivamento');
        $status_assinatura_arquivamento = new THidden('status_assinatura_arquivamento');
        $data_exp_certificado_arquivamento = new THidden('data_exp_certificado_arquivamento');
        $arquivo = new THidden('arquivo');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $arquivo_registrado = new THidden('arquivo_registrado');
        $caminho_arquivo_registrado = new THidden('caminho_arquivo_registrado');
        $url_documentacao = new THidden('url_documentacao');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        //Se curso possuir polo, carrega combo
        $this->dados_curso_id->setChangeAction(new TAction(array($this, 'onCursoChange')));
        
        
        //Preenche cpf da secretária e diretor escolhidos
        $user_id_assinatura_secretaria->setChangeAction(new TAction(array($this, 'onSecretariaChange')));
        $user_id_assinatura_diretor->setChangeAction(new TAction(array($this, 'onDiretorChange')));
                
        
        //Habilita/desabilita campo condicional
        $opcao_cargo_secretaria->setChangeAction(new TAction(array($this, 'onCargoSecretariaChange')));
        
        
        //Habilita/desabilita campo condicional
        $opcao_cargo_diretor->setChangeAction(new TAction(array($this, 'onCargoDiretorChange')));
        

        $radio_via = [];
        $radio_via['1ª via'] = "1ª via do diploma";
        $radio_via['2ª via'] = "2ª via do diploma";
        $radio_via['Decisão judicial'] = "Decisão judicial";
        
        $opcao_via->addItems($radio_via);

        
        $radio_cargo = [];
        $radio_cargo['Utilizar cargo listado pelo MEC'] = "Utilizar cargo listado pelo MEC";
        $radio_cargo['Utilizar cargo não listado pelo MEC'] = "Utilizar cargo não listado pelo MEC";
        
        $opcao_cargo_secretaria->addItems($radio_cargo);
        $opcao_cargo_diretor->addItems($radio_cargo);
        
        
        $combo_cargo = [];
        $combo_cargo['Reitor'] = "Reitor";
        $combo_cargo['Reitor em Exercício'] = "Reitor em Exercício";
        $combo_cargo['Responsável pelo registro'] = "Responsável pelo registro";
        $combo_cargo['Coordenador de Curso'] = "Coordenador de Curso";
        $combo_cargo['Subcoordenador de Curso'] = "Subcoordenador de Curso";
        $combo_cargo['Coordenador de Curso em exercício'] = "Coordenador de Curso em exercício";
        $combo_cargo['Chefe da área de registro de diplomas'] = "Chefe da área de registro de diplomas";
        $combo_cargo['Chefe em exercício da área de registro de diplomas'] = "Chefe em exercício da área de registro de diplomas";
        
        $cargo_mec_secretaria->addItems($combo_cargo);
        $cargo_mec_diretor->addItems($combo_cargo);
        
        
        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_documento ] );
        $this->form->addFields( [ $codigo_interliga_diploma_documentacao ] ); 
        $this->form->addFields( [ $status_documentacao ] );       
        $this->form->addFields( [ $dados_versao_id ] );        
        $this->form->addFields( [ $dados_emissora_id ] );
        $this->form->addFields( [ $status_xml ] );        
        $this->form->addFields( [ $tipo_assinante_secretaria ] );        
        $this->form->addFields( [ $status_assinatura_secretaria ] );
        $this->form->addFields( [ $data_exp_certificado_secretaria ] );        
        $this->form->addFields( [ $tipo_assinante_diretor ] );        
        $this->form->addFields( [ $status_assinatura_diretor ] ); 
        $this->form->addFields( [ $data_exp_certificado_diretor ] );       
        $this->form->addFields( [ $tipo_assinante_emissora ] ); 
        $this->form->addFields( [ $unit_id_assinatura_emissora ] ); 
        $this->form->addFields( [ $cnpj_assinatura_emissora ] );        
        $this->form->addFields( [ $status_assinatura_emissora ] );
        $this->form->addFields( [ $data_exp_certificado_emissora ] );        
        $this->form->addFields( [ $tipo_assinante_arquivamento ] ); 
        $this->form->addFields( [ $unit_id_assinatura_arquivamento ] ); 
        $this->form->addFields( [ $cnpj_assinatura_arquivamento ] );         
        $this->form->addFields( [ $status_assinatura_arquivamento ] ); 
        $this->form->addFields( [ $data_exp_certificado_arquivamento ] );        
        $this->form->addFields( [ $arquivo ] );
        $this->form->addFields( [ $caminho_arquivo ] );
        $this->form->addFields( [ $arquivo_registrado ] );
        $this->form->addFields( [ $caminho_arquivo_registrado ] );
        $this->form->addFields( [ $url_documentacao ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        
        $row = $this->form->addFields( [ new TLabel('Diplomado <font color="red">*</font>'), $this->dados_diplomado_id ] );
        $row->layout = ['col-sm-12'];  
        
        $row = $this->form->addFields( [ new TLabel('Curso <font color="red">*</font>'), $this->dados_curso_id ],
                                       [ new TLabel('Polo'), $dados_polo_id ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];  
        
        $this->form->addFields( [ '<br>' ] );

        $label_explicacao = '<p style="font-size: 16px;"><b>1ª via:</b> Usado para requisição de registro de diploma digital</p>
                             <p style="font-size: 16px;"><b>2ª via:</b> Usado <b>exclusivamente</b> para requisição de registro de diploma digital cuja primeira via foi emitida em meio físico</p>
                             <p style="font-size: 16px;"><b>Decisão judicial:</b> Usado <b>exclusivamente</b> para requisição de registro de diploma digital por força de decisão judicial</p>';        
                               
        
        $panel = new TPanelGroup();
        $panel->add($label_explicacao);
        
        $this->form->addContent( [ $panel ] );

        $this->form->addFields( [ $opcao_via ] );
        
        $this->form->addFields( [ '<br><h5>Definição das assinaturas da documentação</h5><hr>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Assinatura Secretária <font color="red">*</font>'), $user_id_assinatura_secretaria ],
                                       [ new TLabel('CPF <font color="red">*</font>'), $cpf_assinatura_secretaria ],
                                       [ $opcao_cargo_secretaria ] );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Cargo listado pelo MEC'), $cargo_mec_secretaria ],
                                       [ new TLabel('Outro cargo'), $outro_cargo_secretaria ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $this->form->addFields( [ '<hr>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Assinatura Diretor <font color="red">*</font>'), $user_id_assinatura_diretor ],
                                       [ new TLabel('CPF <font color="red">*</font>'), $cpf_assinatura_diretor ],
                                       [ $opcao_cargo_diretor ] );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Cargo listado pelo MEC'), $cargo_mec_diretor ],
                                       [ new TLabel('Outro cargo'), $outro_cargo_diretor ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];

        $this->form->addFields( [ '<hr>' ] );
        

        $this->dados_diplomado_id->addValidation('Diplomado', new TRequiredValidator);
        $this->dados_curso_id->addValidation('Curso', new TRequiredValidator);
        $opcao_via->addValidation('Via', new TRequiredValidator);
        $user_id_assinatura_secretaria->addValidation('Assinatura Secretária', new TRequiredValidator);
        $cpf_assinatura_secretaria->addValidation('CPF Secretária', new TCPFValidator);
        $opcao_cargo_secretaria->addValidation('Utilizar cargo listado/não listado pelo MEC', new TRequiredValidator);
        $user_id_assinatura_diretor->addValidation('Assinatura Diretor', new TRequiredValidator);
        $cpf_assinatura_diretor->addValidation('CPF Diretor', new TCPFValidator);
        $opcao_cargo_diretor->addValidation('Utilizar cargo listado/não listado pelo MEC', new TRequiredValidator);
        $unit_id_assinatura_emissora->addValidation('Assinatura IES', new TRequiredValidator);
        $cnpj_assinatura_emissora->addValidation('CNPJ IES', new TCNPJValidator);
        $unit_id_assinatura_arquivamento->addValidation('Assinatura Arquivamento', new TRequiredValidator);
        $cnpj_assinatura_arquivamento->addValidation('CNPJ Arquivamento', new TCNPJValidator);
        

        // set sizes
        $opcao_via->setSize('100%');
        $opcao_via->setLayout('horizontal');
        $opcao_via->setUseButton();
        $this->dados_diplomado_id->setMask('<b><font color="black">({cod_aluno}) {nome}</font></b>');
        $user_id_assinatura_secretaria->style = 'text-transform: uppercase';
        $opcao_cargo_secretaria->setLayout('horizontal');
        $opcao_cargo_secretaria->setSize(220);
        $user_id_assinatura_diretor->style = 'text-transform: uppercase';
        $opcao_cargo_diretor->setLayout('horizontal');
        $opcao_cargo_diretor->setSize(220);
        $cpf_assinatura_secretaria->setMask('9!');
        $cpf_assinatura_diretor->setMask('9!');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('DiplomaDocumentacaoList','onReload')), 'fas:arrow-alt-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onCursoChange($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //Critério para preencher ou não a combo polo
            if (!empty($param['dados_curso_id']))
            {
                $criteria = TCriteria::create( ['dados_curso_id' => $param['dados_curso_id'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_DiplomaDigitalDocumentacao', 'dados_polo_id', 'Felabs_DB', 'DiplomaDigitalPolo', 'id', 'nome_polo', 'nome_polo', $criteria, TRUE);
            
            
                //Preenche os campos ocultos referentes às assinaturas da emissora e de arquivamento
                $id_curso = $param['dados_curso_id'];
                
                $curso = new DiplomaDigitalCurso($id_curso);
                
                $obj = new StdClass;
                $obj->dados_emissora_id = $curso->diploma_digital_emissora->id;
                $obj->unit_id_assinatura_emissora = $curso->diploma_digital_emissora->system_unit_id;
                $obj->cnpj_assinatura_emissora = $curso->diploma_digital_emissora->cnpj;
                $obj->unit_id_assinatura_arquivamento = $curso->diploma_digital_emissora->system_unit_id;
                $obj->cnpj_assinatura_arquivamento = $curso->diploma_digital_emissora->cnpj;
                
                TForm::sendData('form_DiplomaDigitalDocumentacao', $obj);
            }
            else
            {
                //Se curso não tiver sido selecionado, combo fica vazia
                TCombo::clearField('form_DiplomaDigitalDocumentacao', 'dados_polo_id');
  
                //Campos ocultos obrigatórios recebem valor nulo 
                $obj = new StdClass;
                $obj->dados_emissora_id = '';
                $obj->unit_id_assinatura_emissora = '';
                $obj->cnpj_assinatura_emissora = '';
                $obj->unit_id_assinatura_arquivamento = '';
                $obj->cnpj_assinatura_arquivamento = ''; 
                
                TForm::sendData('form_DiplomaDigitalDocumentacao', $obj);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }
    }
    
    
    public static function onSecretariaChange($param)
    {
        $user_id_assinatura_secretaria = $param['user_id_assinatura_secretaria'];

        if($user_id_assinatura_secretaria == '1806') //Vilma
        {
            $obj = new StdClass;
            $obj->cpf_assinatura_secretaria = '01993035842';
        }
        elseif($user_id_assinatura_secretaria == '1816') //Tânia
        {
            $obj = new StdClass;
            $obj->cpf_assinatura_secretaria = '07143218855';
        }
        else
        {
            $obj = new StdClass;
            $obj->cpf_assinatura_secretaria = '';
        }
        
        TForm::sendData('form_DiplomaDigitalDocumentacao', $obj);
    }
    
    
    public static function onDiretorChange($param)
    {
        $user_id_assinatura_diretor = $param['user_id_assinatura_diretor'];
        
        if($user_id_assinatura_diretor == '27') //Luciana
        {
            $obj = new StdClass;
            $obj->cpf_assinatura_diretor = '00294623639';
        }
        elseif($user_id_assinatura_diretor == '11') //Márcio
        {
            $obj = new StdClass;
            $obj->cpf_assinatura_diretor = '08324735100';
        }
        elseif($user_id_assinatura_diretor == '17') //Betô
        {
            $obj = new StdClass;
            $obj->cpf_assinatura_diretor = '29224016855';
        }
        else
        {
            $obj = new StdClass;
            $obj->cpf_assinatura_diretor = '';
        }
        
        TForm::sendData('form_DiplomaDigitalDocumentacao', $obj);
    }
    
    
    public static function onCargoSecretariaChange($param)
    {
        $opcao_cargo_secretaria = $param['opcao_cargo_secretaria'];
        
        if($opcao_cargo_secretaria == 'Utilizar cargo listado pelo MEC')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');  
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria');
            
            //RECARREGA
            $combo_cargo = [];
            $combo_cargo['Reitor'] = "Reitor";
            $combo_cargo['Reitor em Exercício'] = "Reitor em Exercício";
            $combo_cargo['Responsável pelo registro'] = "Responsável pelo registro";
            $combo_cargo['Coordenador de Curso'] = "Coordenador de Curso";
            $combo_cargo['Subcoordenador de Curso'] = "Subcoordenador de Curso";
            $combo_cargo['Coordenador de Curso em exercício'] = "Coordenador de Curso em exercício";
            $combo_cargo['Chefe da área de registro de diplomas'] = "Chefe da área de registro de diplomas";
            $combo_cargo['Chefe em exercício da área de registro de diplomas'] = "Chefe em exercício da área de registro de diplomas";
            
            TCombo::reload('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria', $combo_cargo, TRUE);
        }
        elseif($opcao_cargo_secretaria == 'Utilizar cargo não listado pelo MEC')
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria');
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');  
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
            TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
            
            //DESABILITA 
            TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
            TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');  
        }    
    }
    
    
    public static function onCargoDiretorChange($param)
    {
        $opcao_cargo_diretor = $param['opcao_cargo_diretor'];
        
        if($opcao_cargo_diretor == 'Utilizar cargo listado pelo MEC')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');  
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor');
            
            //RECARREGA
            $combo_cargo = [];
            $combo_cargo['Reitor'] = "Reitor";
            $combo_cargo['Reitor em Exercício'] = "Reitor em Exercício";
            $combo_cargo['Responsável pelo registro'] = "Responsável pelo registro";
            $combo_cargo['Coordenador de Curso'] = "Coordenador de Curso";
            $combo_cargo['Subcoordenador de Curso'] = "Subcoordenador de Curso";
            $combo_cargo['Coordenador de Curso em exercício'] = "Coordenador de Curso em exercício";
            $combo_cargo['Chefe da área de registro de diplomas'] = "Chefe da área de registro de diplomas";
            $combo_cargo['Chefe em exercício da área de registro de diplomas'] = "Chefe em exercício da área de registro de diplomas";
            
            TCombo::reload('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor', $combo_cargo, TRUE);
        }
        elseif($opcao_cargo_diretor == 'Utilizar cargo não listado pelo MEC')
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor'); 
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor');
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');  
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor'); 
            TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
            
            //DESABILITA 
            TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor'); 
            TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');  
        }  
    }
        

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');            
            
            $data = $this->form->getData(); 

            $object = new DiplomaDigitalDocumentacao; 
            $object->fromArray( (array) $data);
            
            $this->form->validate();
            
            
            //Não permite que outra documentação seja criada para o mesmo aluno e curso caso já exista uma ATIVA
            if(empty($data->id))
            {
                $registros_bd = DiplomaDigitalDocumentacao::where('dados_diplomado_id', '=', $data->dados_diplomado_id)
                                                          ->where('dados_curso_id', '=', $data->dados_curso_id)
                                                          ->where('status_documentacao', '=', 1) //Ativa  
                                                          ->load();
                
                if ($registros_bd)
                {
                    throw new Exception("Já existe uma documentação ativa deste mesmo aluno e curso", 1);
                }
            }
            
            
            //Controle campos condicionais - Cargo secretária
            if($object->opcao_cargo_secretaria == 'Utilizar cargo listado pelo MEC')
            {
                if(! $object->cargo_mec_secretaria)
                {
                    throw new Exception("É necessário preencher o cargo da secretária", 1);
                }
            }
            else
            {
                if(! $object->outro_cargo_secretaria)
                {
                    throw new Exception("É necessário preencher o cargo da secretária", 1);
                }
            }
            
            
            //Controle campos condicionais - Cargo diretor
            if($object->opcao_cargo_diretor == 'Utilizar cargo listado pelo MEC')
            {
                if(! $object->cargo_mec_diretor)
                {
                    throw new Exception("É necessário preencher o cargo do diretor", 1);
                }
            }
            else
            {
                if(! $object->outro_cargo_diretor)
                {
                    throw new Exception("É necessário preencher o cargo do diretor", 1);
                }
            }
            
            
            /*Importante, pois estando ativa não permite a criação de outra documentação. Caso o diploma seja anulado, a documentação deverá ser inativada e uma nova gerada*/
            if($object->status_documentacao == NULL)
            {
                $object->status_documentacao = 1; //0 Inativa / 1 Ativa
            }
            
            if($object->status_xml == NULL)
            {
                $object->status_xml = 0; //0 Não gerado / 1 Gerado
            }
            
            if($object->status_assinatura_secretaria == NULL)
            {
                $object->status_assinatura_secretaria = 0; //0 Não assinado / 1 Assinado
            }
            
            if($object->status_assinatura_diretor == NULL)
            {
                $object->status_assinatura_diretor = 0; //0 Não assinado / 1 Assinado
            }
            
            if($object->status_assinatura_emissora == NULL)
            {
                $object->status_assinatura_emissora = 0; //0 Não assinado / 1 Assinado
            }

            if($object->status_assinatura_arquivamento == NULL)
            {
                $object->status_assinatura_arquivamento = 0; //0 Não assinado / 1 Assinado
            }
            
            
            //Gera código nonce de 44 dígitos
            if((empty($object->codigo_interliga_diploma_documentacao)) OR ($object->codigo_interliga_diploma_documentacao == NULL))
            {
                $last = DiplomaDigitalDocumentacao::last();
                $last_id = $last->id + 1;
                $id_diplomado = $object->dados_diplomado_id;
                $codigo_aluno = $object->diploma_digital_diplomado->cod_aluno;
                $uniqid1 = hexdec(uniqid()); //gera sequência aleatória
                $uniqid2 = hexdec(uniqid()); //gera sequência aleatória
                $datetime = date('YmdHis');
                $codigo = substr("$last_id" . "$id_diplomado" . "$codigo_aluno" . "$uniqid1" . "$datetime" . "$uniqid2", 0, 44);  
                    
                $object->codigo_interliga_diploma_documentacao = $codigo;                    
            }
            
            
            $object->tipo_documento = "XMLDocumentacao";
            $object->tipo_assinante_secretaria = "IESRepresentante";
            $object->tipo_assinante_diretor = "IESRepresentante";
            $object->tipo_assinante_emissora = "IESEmissora";
            $object->tipo_assinante_arquivamento = "IESEmissora";
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
            $code = $e->getCode();

            //Não preencheu todos os campos obrigatórios
            if($code == 1 or $code == 0)
            {
                new TMessage('error', $e->getMessage());

                $data = $this->form->getData();                                   
                $this->form->setData($data);  
                
                //Se estiver editando registro e cair na exceção, mantém campos bloqueados. Se estiver salvando novo registro, mantém desbloqueado
                if(!empty($param['id']))
                {
                    $this->dados_diplomado_id->setEditable(FALSE);
                    $this->dados_curso_id->setEditable(FALSE);
                }
                
                
                //Campos dependentes - secretária
                if($data->opcao_cargo_secretaria == "Utilizar cargo listado pelo MEC")
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');      
                }
                elseif($data->opcao_cargo_secretaria == "Utilizar cargo não listado pelo MEC")
                {
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria');
                }
                else
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
                }
                
                
                //Campos dependentes - diretor
                if($data->opcao_cargo_diretor == "Utilizar cargo listado pelo MEC")
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');     
                }
                elseif($data->opcao_cargo_secretaria == "Utilizar cargo não listado pelo MEC")
                {
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor');
                }  
                else
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor');
                }  
                
                TTransaction::rollback();
            }
            
            
            //Se já existir o mesmo código na tabela
            if($code == 23000)
            {                
                $codigo_gerado = $object->codigo_interliga_diploma_documentacao;
                
                $conn = TTransaction::get();
                
                $sth = $conn->prepare("SELECT codigo_interliga_diploma_documentacao FROM dados_documentacao");
                $sth->execute();
                
                $result = $sth->fetchAll(PDO::FETCH_COLUMN, 0);                        

                while($result)
                {
                   if(in_array($codigo_gerado, $result))
                   {
                      $last = DiplomaDigitalDocumentacao::last();
                      $last_id = $last->id + 1;
                      $id_diplomado = $object->dados_diplomado_id;
                      $codigo_aluno = $object->diploma_digital_diplomado->cod_aluno;
                      $uniqid1 = hexdec(uniqid()); //gera sequência aleatória
                      $uniqid2 = hexdec(uniqid()); //gera sequência aleatória
                      $datetime = date('YmdHis');
                      $codigo = substr("$last_id" . "$id_diplomado" . "$codigo_aluno" . "$uniqid1" . "$datetime" . "$uniqid2", 0, 44);
                            
                      $codigo_gerado = $codigo;
    
                      reset($result); 
                   }
                   else
                   {
                       $codigo_final = $codigo_gerado;   
                        break;
                   }
                }

                $verifica_codigo = strlen($codigo_final);

                //Código deve ser composto por 44 dígitos
                if($verifica_codigo == 44)
                {
                    $object->codigo_interliga_diploma_documentacao = $codigo_final;
                    $object->store();
                }
                else
                {
                    new TMessage('error', $e->getMessage());
                    TTransaction::rollback();
                    
                    TApplication::loadPage('DiplomaDocumentacaoList', 'onReload');
                } 
                
                TTransaction::close();
                
                TApplication::loadPage('DiplomaDocumentacaoList', 'onReload');
            }    
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
                
                $object = new DiplomaDigitalDocumentacao($key);

                $this->form->setData($object);
                
                $this->dados_diplomado_id->setEditable(FALSE);
                $this->dados_curso_id->setEditable(FALSE);
                
                
                //Campos dependentes - secretária
                if($object->opcao_cargo_secretaria == "Utilizar cargo listado pelo MEC")
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');      
                }
                elseif($object->opcao_cargo_secretaria == "Utilizar cargo não listado pelo MEC")
                {
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria');
                }
                else
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_secretaria');
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_secretaria'); 
                }
                
                
                //Campos dependentes - diretor
                if($object->opcao_cargo_diretor == "Utilizar cargo listado pelo MEC")
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');     
                }
                elseif($object->opcao_cargo_secretaria == "Utilizar cargo não listado pelo MEC")
                {
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor');
                }  
                else
                {
                    TEntry::clearField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
                    TEntry::disableField('form_DiplomaDigitalDocumentacao', 'outro_cargo_diretor');
                    TCombo::clearField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor'); 
                    TCombo::disableField('form_DiplomaDigitalDocumentacao', 'cargo_mec_diretor');
                }  
                
                TTransaction::close();                                 
            }
            else
            {
                $this->form->clear(TRUE);
                
                //Campos dependentes são inicialmente desabilitados
                $this->onCargoSecretariaChange($param);
                $this->onCargoDiretorChange($param);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
