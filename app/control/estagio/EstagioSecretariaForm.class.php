<?php

class EstagioSecretariaForm extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();              

        
        $loggedUnit = TSession::getValue('userunitid');
        
        if($loggedUnit <> 2 AND $loggedUnit <> 3 AND $loggedUnit <> 10 AND $loggedUnit <> 6)
        {
            new TMessage('error', 'Funcionalidade não disponível para esta unidade');
            die;
        }


        //Critério para filtrar os cursos de acordo com a unidade no momento de logar
        $criteria_curso = new TCriteria;
        $criteria_curso->add(new TFilter('CodEntidade', '=', $loggedUnit));
            
            
        //Critério para carregar combo do professor responsável pela aprovação
        if($loggedUnit == 3)
        {
            $array_fafram = [];
            $array_fafram['19'] = '19'; //Agronomia - Livia Cordaro                
            $array_fafram['6015'] = '6015'; //Direito - Bruno
            $array_fafram['2890'] = '2890'; //Sistemas - Murilo Scapim
            $array_fafram['1937'] = '1937'; //Enfermagem - Andreza Maeda
            $array_fafram['18'] = "18"; //Veterinária - ELZYLENE LÉGA
                
            $criteria_prof = new TCriteria;
            $criteria_prof->add(new TFilter('id', 'IN', $array_fafram));
        }
        else
        {
            $array_ffcl_fajob = [];
            $array_ffcl_fajob['29'] = '29'; //Administração, Gestão de RH e Contábeis - Lidiane Kanesiro
            $array_ffcl_fajob['2401'] = '2401'; //Pedagogia - Fátima Gonini 
            $array_ffcl_fajob['5622'] = '5622'; //Engenharia Civil, Mecânica, Produção e Elétrica - Amanda Paula Caretta                       
            $array_ffcl_fajob['27'] = '27'; //Luciana 2ªvia  
            $array_ffcl_fajob['1852'] = '1852'; //Lisangela - Letras
            $array_ffcl_fajob['59'] = '59'; //Wesley - Estudos Sociais                     
               
            $criteria_prof = new TCriteria;
            $criteria_prof->add(new TFilter('id', 'IN', $array_ffcl_fajob));
        }    
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_EstagioSecretaria');
        $this->form->setFormTitle('<h4>Lançamento de Estágio</h4>');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $tipo_entrada = new THidden('tipo_entrada');
        $cod_aluno = new TDBSeekButton('cod_aluno', 'dados_fei', 'form_EstagioSecretaria', 'FiAluno', 'Nome', 'cod_aluno', 'nome_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $cod_curso = new TDBSeekButton('cod_curso', 'dados_fei', 'form_EstagioSecretaria', 'FiCurso', 'Nome', 'cod_curso', 'nome_curso', $criteria_curso);
        $nome_curso = new TEntry('nome_curso');
        $ano = new TEntry('ano');
        $semestre = new TCombo('semestre');
        $etapa = new TCombo('etapa');
        $data_inicio = new TDate('data_inicio');
        $data_termino = new TDate('data_termino');
        $carga_horaria = new TEntry('carga_horaria');
        $razao_social_empresa = new TEntry('razao_social_empresa');
        $cnpj_empresa = new TEntry('cnpj_empresa');
        $descricao = new TEntry('descricao');
        $cod_prof_responsavel = new TDBCombo('cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria_prof);
        $titulacao_prof_responsavel = new TCombo('titulacao_prof_responsavel');
        $arquivo = new TFile('arquivo');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $status_estagio = new THidden('status_estagio');
        $observacao = new THidden('observacao');
        $status_pdfa = new THidden('status_pdfa');    
        $status_assinatura = new THidden('status_assinatura');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        $opcao_estagio = new TRadioGroup('opcao_estagio');
        $nome_pessoa_fisica = new TEntry('nome_pessoa_fisica');
        $cpf_pessoa_fisica = new TEntry('cpf_pessoa_fisica');
        $cod_estagio_historico = new THidden('cod_estagio_historico');
        

        //Semestre
        $combo_semestre = [];
        $combo_semestre[1] = '1º semestre';
        $combo_semestre[2] = '2º semestre';
        
        $semestre->addItems($combo_semestre);
        
        //Preenche a etapa em que o aluno estava matriculado de acordo com o ano e semestre do estágio
        $semestre->setChangeAction(new TAction(array($this, 'onEtapaChange')));
        
 
        //Etapa
        $combo_etapa = [];
        $combo_etapa[1] = '1º ciclo';
        $combo_etapa[2] = '2º ciclo';
        $combo_etapa[3] = '3º ciclo';
        $combo_etapa[4] = '4º ciclo';
        $combo_etapa[5] = '5º ciclo';
        $combo_etapa[6] = '6º ciclo';
        $combo_etapa[7] = '7º ciclo';
        $combo_etapa[8] = '8º ciclo';
        $combo_etapa[9] = '9º ciclo';
        $combo_etapa[10] = '10º ciclo';

        $etapa->addItems($combo_etapa);
        
        
        //Opção estágio
        $radio_opcao_estagio = [];
        $radio_opcao_estagio['Pessoa física'] = 'Pessoa física';
        $radio_opcao_estagio['Pessoa jurídica'] = 'Pessoa jurídica';
        
        $opcao_estagio->addItems($radio_opcao_estagio);
        
        $opcao_estagio->setChangeAction(new TAction(array($this, 'onOpcaoEstagioChange')));
        
        
        //Titulação
        $combo_titulacao = [];
        $combo_titulacao['Tecnólogo'] = "Tecnólogo";
        $combo_titulacao['Graduação'] = "Graduação";
        $combo_titulacao['Especialização'] = "Especialização";
        $combo_titulacao['Mestrado'] = "Mestrado";
        $combo_titulacao['Doutorado'] = "Doutorado";
         
        $titulacao_prof_responsavel->addItems($combo_titulacao);
        
        
        $arquivo->setAllowedExtensions(['pdf']);   
        

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_entrada ] );
        $this->form->addFields( [ $caminho_arquivo ] );
        $this->form->addFields( [ $status_estagio ] );
        $this->form->addFields( [ $observacao ] );
        $this->form->addFields( [ $status_pdfa ] );
        $this->form->addFields( [ $status_assinatura ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        $this->form->addFields( [ $cod_estagio_historico ] );
        
        $row = $this->form->addFields( [ new TLabel('Cód. aluno <font color="red">*</font>'), $cod_aluno ],
                                       [ new TLabel('Aluno <font color="red">*</font>'), $nome_aluno ],
                                       [ new TLabel('Cód. curso <font color="red">*</font>'), $cod_curso ],
                                       [ new TLabel('Curso <font color="red">*</font>'), $nome_curso ] );
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Ano (em que realizou o estágio) <font color="red">*</font>'), $ano ],
                                       [ new TLabel('Semestre <font color="red">*</font>'), $semestre ],
                                       [ new TLabel('Etapa <font color="red">*</font>'), $etapa ],
                                       [ new TLabel('Responsável pela aprovação <font color="red">*</font>'), $cod_prof_responsavel ],
                                       [ new TLabel('Titulação <font color="red">*</font>'), $titulacao_prof_responsavel ] );
        $row->layout = ['col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-3', 'col-sm-2'];
        
        $row = $this->form->addFields( [ $opcao_estagio ],
                                       [ new TLabel('Razão social da empresa'), $razao_social_empresa ],
                                       [ new TLabel('CNPJ da empresa'), $cnpj_empresa ],
                                       [ new TLabel('Nome do concedente'), $nome_pessoa_fisica ],
                                       [ new TLabel('CPF do concedente'), $cpf_pessoa_fisica ] );
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-3', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Data de início <font color="red">*</font>'), $data_inicio ],
                                       [ new TLabel('Data de término <font color="red">*</font>'), $data_termino ],
                                       [ new TLabel('Horas <font color="red">*</font>'), $carga_horaria ],
                                       [ new TLabel('Anexar comprovante em PDF <font color="red">*</font>'), $arquivo ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Breve descrição do estágio <font color="red">*</font>'), $descricao ] );
        $row->layout = ['col-sm-12'];

        $this->form->addFields( [ '<br>' ] ); 
        $label1 = new TLabel('<font color="red">*</font> Campos obrigatórios', '', 10, 'i');
        $this->form->addContent( [$label1] ); 
        

        $cod_aluno->addValidation('Cód. aluno', new TRequiredValidator);
        $nome_aluno->addValidation('Aluno', new TRequiredValidator);
        $cod_curso->addValidation('Cód. curso', new TRequiredValidator);
        $nome_curso->addValidation('Curso', new TRequiredValidator);
        $ano->addValidation('Ano (em que realizou o estágio)', new TRequiredValidator);
        $semestre->addValidation('Semestre', new TRequiredValidator);
        $etapa->addValidation('Etapa', new TRequiredValidator);
        $cod_prof_responsavel->addValidation('Responsável pela aprovação', new TRequiredValidator);
        $titulacao_prof_responsavel->addValidation('Titulação', new TRequiredValidator);        
        $opcao_estagio->addValidation('Pessoa física/Pessoa jurídica', new TRequiredValidator);    
        $data_inicio->addValidation('Data de início', new TRequiredValidator);
        $data_termino->addValidation('Data de término', new TRequiredValidator);
        $carga_horaria->addValidation('Horas', new TRequiredValidator);      
        $descricao->addValidation('Breve descrição do estágio', new TRequiredValidator);              
        $arquivo->addValidation('Anexar comprovante em PDF', new TRequiredValidator);


        // set sizes
        $nome_aluno->setEditable(FALSE);
        $nome_curso->setEditable(FALSE);
        $ano->setMask('9999');
        $ano->placeholder = "Ex: 2023";
        $data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_termino->setMask('dd/mm/yyyy');
        $data_termino->setDatabaseMask('yyyy-mm-dd');
        $carga_horaria->setMask('9!');
        $opcao_estagio->setLayout('horizontal');
        $cnpj_empresa->setMask('99.999.999/9999-99');
        $cpf_pessoa_fisica->setMask('999.999.999-99');
        

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

         
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Voltar', new TAction(['EstagioSecretariaList', 'onReload']), 'fa:arrow-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onEtapaChange($param)
    {
        try
        {
            TTransaction::open('dados_fei');
           
            if((!empty($param['cod_aluno'])) AND (!empty($param['cod_curso'])) AND (!empty($param['ano'])) AND (!empty($param['semestre'])))
            {
                //Pega a etapa em que o aluno estava matriculado quando realizou o estágio
                $criteria = new TCriteria;
                $criteria->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
                $criteria->add(new TFilter('CodCurso', '=', $param['cod_curso']));
                $criteria->add(new TFilter('AnoMatricula', '=', $param['ano']));
                $criteria->add(new TFilter('SemestreMatricula', '=', $param['semestre']));
            
                $matricula = VwAlunoMatriculaEtapa::getObjects($criteria);
                
                if($matricula)
                {
                    $obj = new StdClass;
                    $obj->etapa = $matricula[0]->EtapaMatricula;

                    TForm::sendData('form_EstagioSecretaria', $obj);
                }
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }
    }
    
    
    public static function onOpcaoEstagioChange($param)
    {
        $opcao_estagio = $param['opcao_estagio'];

        if($opcao_estagio == 'Pessoa física')
        {
            //LIMPA
            TEntry::clearField('form_EstagioSecretaria', 'razao_social_empresa');
            TEntry::clearField('form_EstagioSecretaria', 'cnpj_empresa');
            
            //DESABILITA
            TEntry::disableField('form_EstagioSecretaria', 'razao_social_empresa');
            TEntry::disableField('form_EstagioSecretaria', 'cnpj_empresa');  
            
            //HABILITA
            TEntry::enableField('form_EstagioSecretaria', 'nome_pessoa_fisica');
            TEntry::enableField('form_EstagioSecretaria', 'cpf_pessoa_fisica'); 
        }
        elseif($opcao_estagio == 'Pessoa jurídica')
        {
            //LIMPA
            TEntry::clearField('form_EstagioSecretaria', 'nome_pessoa_fisica');
            TEntry::clearField('form_EstagioSecretaria', 'cpf_pessoa_fisica'); 
            
            //DESABILITA
            TEntry::disableField('form_EstagioSecretaria', 'nome_pessoa_fisica');
            TEntry::disableField('form_EstagioSecretaria', 'cpf_pessoa_fisica'); 
            
            //HABILITA
            TEntry::enableField('form_EstagioSecretaria', 'razao_social_empresa');
            TEntry::enableField('form_EstagioSecretaria', 'cnpj_empresa');   
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_EstagioSecretaria', 'razao_social_empresa');
            TEntry::clearField('form_EstagioSecretaria', 'cnpj_empresa'); 
            TEntry::clearField('form_EstagioSecretaria', 'nome_pessoa_fisica');
            TEntry::clearField('form_EstagioSecretaria', 'cpf_pessoa_fisica'); 
            
            //DESABILITA
            TEntry::disableField('form_EstagioSecretaria', 'razao_social_empresa');
            TEntry::disableField('form_EstagioSecretaria', 'cnpj_empresa'); 
            TEntry::disableField('form_EstagioSecretaria', 'nome_pessoa_fisica');
            TEntry::disableField('form_EstagioSecretaria', 'cpf_pessoa_fisica'); 
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');                        

            $data = $this->form->getData(); 
            
            $object = new Estagio;  
            $object->fromArray( (array) $data); 
            
            $this->form->validate(); 
            
            
            TTransaction::open('dados_fei');
            
            //Confirma se aluno tem matrícula no curso selecionado
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
            $criteria_curso->add(new TFilter('CodCurso', '=', $param['cod_curso']));
            
            $matricula_curso = VwAlunoMatriculaEtapa::getObjects($criteria_curso);
            
            if(empty($matricula_curso))
            {
                throw new Exception("Não consta matrícula deste aluno no curso selecionado");
            }
            
            
            //Confirma se a etapa em que o aluno estava matriculado quando realizou o estágio está correta
            $criteria_matricula = new TCriteria;
            $criteria_matricula->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
            $criteria_matricula->add(new TFilter('CodCurso', '=', $param['cod_curso']));
            $criteria_matricula->add(new TFilter('AnoMatricula', '=', $param['ano']));
            $criteria_matricula->add(new TFilter('SemestreMatricula', '=', $param['semestre']));
            
            $matricula = VwAlunoMatriculaEtapa::getObjects($criteria_matricula);
            
            //Se tiver matrícula e no formulário estiver diferente do que consta no BD, barra. Caso contrário, deixa salvar (ex: casos de transferência)
            if((!empty($matricula)) AND ($matricula[0]->EtapaMatricula <> $param['etapa']))
            {
                throw new Exception("Verifique a etapa em que o aluno estava matriculado quando realizou o estágio");
            }
            
            TTransaction::close();
            
            
            //Verifica se o ano contém 4 dígitos
            $count = strlen($object->ano);
            
            if($count <> 4)
            {
                throw new Exception("O campo Ano (em que realizou o estágio) precisa ter 4 dígitos");
            }
            
            
            //Controle campos condicionais - Concedente do estágio
            if($object->opcao_estagio == 'Pessoa física')
            {
                $object->razao_social_empresa = '';
                $object->cnpj_empresa = '';  
                
                if(! $object->nome_pessoa_fisica)
                {
                    throw new Exception('É necessário informar a pessoa física concedente do estágio. Caso conste o CPF do concedente, este também deve ser informado');
                }
                
                //Se o CPF foi preenchido
                if($object->cpf_pessoa_fisica)
                {
                    $valida_cpf = new TCPFValidator;
                    $valida_cpf->validate('CPF do concedente', $object->cpf_pessoa_fisica);
                }
            }
            elseif($object->opcao_estagio == 'Pessoa jurídica')
            {
                $object->nome_pessoa_fisica = '';
                $object->cpf_pessoa_fisica = '';
                
                if(! $object->razao_social_empresa)
                {
                    throw new Exception('É necessário informar a pessoa jurídica concedente do estágio. Caso conste o CNPJ do concedente, este também deve ser informado');
                }
                
                //Se o CNPJ foi preenchido
                if($object->cnpj_empresa)
                {
                    $valida_cnpj = new TCNPJValidator;
                    $valida_cnpj->validate('CNPJ da empresa', $object->cnpj_empresa);
                }
            }
            else
            {
                $object->opcao_estagio = '';
                $object->razao_social_empresa = '';
                $object->cnpj_empresa = ''; 
                $object->nome_pessoa_fisica = '';
                $object->cpf_pessoa_fisica = '';  
            }
            
            
            //Data de término não pode ser anterior à de início
            if($object->data_termino < $object->data_inicio)
            {
                throw new Exception("A data de término não pode ser anterior à data de início");
            }
            
            
            //Verifica se as datas são válidas
            $data_inicio = explode('/', $param['data_inicio']);
            $data_termino = explode('/', $param['data_termino']);
            
            if (!checkdate($data_inicio[1], $data_inicio[0], $data_inicio[2])) //mês, dia e ano
            {
                throw new Exception("A data de início não é uma data válida");
            } 
            
            if (!checkdate($data_termino[1], $data_termino[0], $data_termino[2])) //mês, dia e ano 
            {
                throw new Exception("A data de término não é uma data válida");
            }


            //Verifica se o ano de início do estágio é o mesmo ano em que ele foi realizado (o ano de término pode ser diferente)
            if ($data_inicio[2] <> $object->ano) 
            {
                throw new Exception("O ano de realização do estágio e o ano de início estão diferentes");
            }
            
            
            //Verifica se o semestre de início do estágio é o mesmo semestre em que ele foi realizado (o semestre de término pode ser diferente)
            if($object->semestre == '1' AND $data_inicio[1] > 6) 
            {
                throw new Exception("O semestre de realização do estágio e o semestre de início estão diferentes");
            }
            
            if($object->semestre == '2' AND $data_inicio[1] <= 6)
            {
                throw new Exception("O semestre de realização do estágio e o semestre de início estão diferentes");
            }
            
            
            //Verifica se o responsável selecionado é o designado para aprovar as atividades do curso
            $curso = $param['cod_curso'];

            switch ($curso) 
            {
              //Agronomia
              case "15":
                if($object->cod_prof_responsavel <> '136')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Direito  
              case "16":
                if($object->cod_prof_responsavel <> '841')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Veterinária  
              case "20":
                if($object->cod_prof_responsavel <> '235')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Sistemas  
              case "21":
                if($object->cod_prof_responsavel <> '774')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Enfermagem  
              case "70":
                if($object->cod_prof_responsavel <> '717')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;              
              
              //Administração
              case "10":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Administração EAD  
              case "115":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Ciências Biológicas  
              case "13":
                if($object->cod_prof_responsavel <> '50')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Contábeis  
              case "62":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Contábeis EAD  
              case "114":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Eng. Civil  
              case "69":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break; 
                  
              //Eng. Produção  
              case "68":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Eng. Elétrica  
              case "104":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Eng. Mecânica  
              case "67":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Recursos Humanos  
              case "116":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //História  
              case "8":
                if($object->cod_prof_responsavel <> '72')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //História EAD  
              case "88":
                if($object->cod_prof_responsavel <> '27')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Letras  
              case "5":
                if($object->cod_prof_responsavel <> '184')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Matemática  
              case "1":
                if($object->cod_prof_responsavel <> '371')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Pedagogia  
              case "6":
                if($object->cod_prof_responsavel <> '338')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Pedagogia EAD  
              case "39":
                if($object->cod_prof_responsavel <> '338')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Geografia EAD  
              case "89":
                if($object->cod_prof_responsavel <> '72')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;

                              //Geografia EAD  
              case "7":
                if($object->cod_prof_responsavel <> '72')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                     
              default:
                throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
            }
            
            
            $source_file  = 'tmp/' . $object->arquivo;

            if (file_exists($source_file))
            {
                $filepdf = fopen($source_file, 'r');
                $line_first = fgets($filepdf);               
                $valid = false;
                

                //Verifica se arquivo não está assinado, pois se estiver não é possível fazer conversão para PDF/A posteriormente
                while (($buffer = fgets($filepdf)) !== false) 
                {
                    if (strpos($buffer, 'adbe.pkcs7.detached') !== false)  
                    {
                        $valid = TRUE;
                        break; 
                    }      
                }
                
                fclose($filepdf);

                if($valid === true)
                {
                    unlink($source_file);
                    
                    throw new Exception("O arquivo a ser anexado não pode estar assinado com certificado digital");
                }
                else
                {                                   
                    $target_path  = 'secretaria/estagios/aluno_' . $object->cod_aluno;
                    
                    //Se não existir diretório, cria
                    if (!file_exists($target_path))
                    {
                        if (!@mkdir($target_path, 0777, true))
                        {
                            throw new Exception(_t('Permission denied'). ': '. $target_path);
                        }
                    }
                
                    //Se diretório foi criado, salva arquivo já renomeado
                    if (file_exists($target_path))
                    {                     
                        $datetime = date("YmdHis");
                        $extensao = pathinfo('tmp/' . $object->arquivo, PATHINFO_EXTENSION);
                                                   
                                                   
                        //Renomeia o arquivo na própria pasta tmp para não ter problemas com caracteres especiais na hora de usar o ghostscript                        
                        $nome_tmp = 'tmp/' . 'comprovante_estagio_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;                        
                        rename($source_file, $nome_tmp);
                                                
                                                
                        //Ghostscript usa o caminho absoluto
                        $caminho_absoluto_tmp = realpath($nome_tmp);                                                
                        $caminho_absoluto_target = realpath($target_path);   
                        $caminho_absoluto_pdf = $caminho_absoluto_target . '/comprovante_estagio_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;
                        
                        
                        //Sobe arquivo independentemente de versão (sem ghostscript, apresenta erro em versões maiores que 1.4)
                        shell_exec('gswin32c -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=' . $caminho_absoluto_pdf . ' ' . $caminho_absoluto_tmp);                         
                    }
                }
                
                //Se arquivo foi salvo                                              
                if(file_exists($caminho_absoluto_pdf))
                {
                    //Apaga arquivo da pasta tmp
                    if($nome_tmp)
                    {
                        unlink($nome_tmp);
                    }
                                        
                    if($object->status_estagio == NULL)
                    {
                        $object->status_estagio = "Aguardando aprovação";
                    }                        
                           
                    $object->tipo_entrada = "Estágio";
                    $object->cnpj_empresa = str_replace(array(".", "/", "-"), "", $object->cnpj_empresa);
                    $object->cpf_pessoa_fisica = str_replace(array(".", "-"), "", $object->cpf_pessoa_fisica);
                    $object->arquivo = 'comprovante_estagio_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;
                    $object->caminho_arquivo = $target_path;
                    $object->status_pdfa = 0;
                    $object->status_assinatura = 0;
                    $object->system_user_id = TSession::getValue('userid');
                    $object->data_reg = date('Y-m-d H:i:s');  
                
                    $object->store();
                            
                           
                    //Dispara e-mail para o professor (se FFCL, FAJOB, NEAD)
                    $loggedUnit = TSession::getValue('userunitid');
                    
                    if($loggedUnit == 2 OR $loggedUnit == 6 OR $loggedUnit == 10)
                    {                    
                        TTransaction::open('dados_fei');
                        
                        $professor = new FiProfessor($object->cod_prof_responsavel);
                        
                        TTransaction::close();
                
                
                        $corpoEmail = 'Prezado(a), 
                    
Um novo estágio foi enviado e aguarda sua análise.
    
Aluno(a): ' . $object->nome_aluno . '  
    
Curso: ' . $object->nome_curso;
    
                
                        if($professor->Email AND $corpoEmail)
                        {
                            $prefs = SystemPreference::getAllPreferences();
                            
                            $mail = new TMail;
                            $mail->setFrom($prefs['mail_from'], "Mensagem - Secretaria Acadêmica");
                            $mail->setSubject('Avaliação de Estágio');
                            $mail->setTextBody($corpoEmail);  
                            
                            $mail->addAddress($professor->Email);  
                              
                            $mail->SetUseSmtp();
                            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                            $mail->send();
                        }
                    }       
                                                          
                    $data->id = $object->id;
            
                    $this->form->setData($data); 
                    TTransaction::close(); 
                    
                    new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
                    
                    TApplication::loadPage('EstagioSecretariaList', 'onReload');
                }
                else
                {
                    throw new Exception("Erro ao fazer upload do arquivo. Por favor, reinicie o processo");
                } 
            }
            else
            {
                throw new Exception("É necessário anexar o comprovante em PDF");
            } 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            
            $param['opcao_estagio'] = $param['opcao_estagio'];
            $this->onOpcaoEstagioChange($param);
            
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
                
                $object = new Estagio($key); 
                
                $param['opcao_estagio'] = $object->opcao_estagio;
                $this->onOpcaoEstagioChange($param);
                
                $cod_responsavel = $object->cod_prof_responsavel;
                
                $this->form->setData($object); 
                
                TTransaction::close(); 
                
                
                //Preenche nome do responsável
                TTransaction::open('dados_fei');
                
                $responsavel = new FiProfessor($cod_responsavel);
                
                $object->nome_prof_responsavel = $responsavel->Nome;
                
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