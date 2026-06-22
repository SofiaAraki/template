<?php

class AtividadeComplementarSecretariaForm extends TPage
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
            
            
        //Critério para evitar pré-carregamento da combo categoria
        $criteria_categoria = new TCriteria;
        $criteria_categoria->add(new TFilter('id', '<', '0'));
            
            
        //Critério para evitar pré-carregamento da combo atividade
        $criteria_atividade = new TCriteria;
        $criteria_atividade->add(new TFilter('id', '<', '0'));
        
            
        //Critério para carregar combo do professor responsável pela aprovação
        if($loggedUnit == 3)
        {
            $array_fafram = [];
            $array_fafram['19'] = '19'; //Agronomia - Livia Cordaro
            $array_fafram['6015'] = '6015'; //Direito - Bruno
            $array_fafram['2890'] = '2890'; //Sistemas - Murilo Scapim
            $array_fafram['1937'] = '1937'; //Enfermagem - Andreza Maeda
            $array_fafram['18'] = '18'; //Veterinária - ELZYLENE LÉGA
               
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
        $this->form = new BootstrapFormBuilder('form_AtividadeComplementarSecretaria');
        $this->form->setFormTitle('<h4>Lançamento de Atividade Complementar</h4>');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $tipo_entrada = new THidden('tipo_entrada');
        $cod_aluno = new TDBSeekButton('cod_aluno', 'dados_fei', 'form_AtividadeComplementarSecretaria', 'FiAluno', 'Nome', 'cod_aluno', 'nome_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $cod_curso = new TDBSeekButton('cod_curso', 'dados_fei', 'form_AtividadeComplementarSecretaria', 'FiCurso', 'Nome', 'cod_curso', 'nome_curso', $criteria_curso);
        $nome_curso = new TEntry('nome_curso');
        $ano = new TEntry('ano');
        $semestre = new TCombo('semestre');
        $etapa = new TCombo('etapa');
        $tipo_atividade = new THidden('tipo_atividade');
        $data_inicio = new TDate('data_inicio');
        $data_termino = new TDate('data_termino');
        $carga_horaria = new TEntry('carga_horaria');
        $descricao = new TEntry('descricao');
        $cod_prof_responsavel = new TDBCombo('cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria_prof);
        $titulacao_prof_responsavel = new TCombo('titulacao_prof_responsavel');
        $arquivo = new TFile('arquivo');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $status_atividade = new THidden('status_atividade');
        $observacao = new THidden('observacao');
        $status_pdfa = new THidden('status_pdfa');    
        $status_assinatura = new THidden('status_assinatura');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        $categoria_atividade_id = new TDBCombo('categoria_atividade_id', 'Felabs_DB', 'AtividadeComplementarCategoria', 'id', 'nome', 'id', $criteria_categoria);
        $cadastro_atividade_id = new TDBCombo('cadastro_atividade_id', 'Felabs_DB', 'AtividadeComplementarCadastro', 'id', 'nome', 'id', $criteria_atividade);
        $cod_atividade_historico = new THidden('cod_atividade_historico');
        
        
        //Recarrega campos dependentes de acordo com o curso escolhido
        $cod_curso->setExitAction(new TAction(array($this, 'onCursoExit')));
        
        
        //Carrega as atividades de acordo com a categoria escolhida
        $categoria_atividade_id->setChangeAction(new TAction(array($this, 'onCategoriaExit')));
        
        
        //Preenche o tipo de atividade
        $cadastro_atividade_id->setChangeAction(new TAction(array($this, 'onAtividadeExit')));
        

        //Semestre
        $combo_semestre = [];
        $combo_semestre[1] = '1º semestre';
        $combo_semestre[2] = '2º semestre';
        
        $semestre->addItems($combo_semestre);
 
        //Preenche a etapa em que o aluno estava matriculado de acordo com o ano e semestre da atividade 
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
        $this->form->addFields( [ $tipo_atividade ] );
        $this->form->addFields( [ $caminho_arquivo ] );
        $this->form->addFields( [ $status_atividade ] );
        $this->form->addFields( [ $observacao ] );
        $this->form->addFields( [ $status_pdfa ] );
        $this->form->addFields( [ $status_assinatura ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        $this->form->addFields( [ $cod_atividade_historico ] );
        
        $row = $this->form->addFields( [ new TLabel('Cód. aluno <font color="red">*</font>'), $cod_aluno ],
                                       [ new TLabel('Aluno <font color="red">*</font>'), $nome_aluno ],
                                       [ new TLabel('Cód. curso <font color="red">*</font>'), $cod_curso ],
                                       [ new TLabel('Curso <font color="red">*</font>'), $nome_curso ] );
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Ano (em que realizou a atividade) <font color="red">*</font>'), $ano ],
                                       [ new TLabel('Semestre (em que realizou a atividade) <font color="red">*</font>'), $semestre ],
                                       [ new TLabel('Etapa (em que realizou a atividade) <font color="red">*</font>'), $etapa ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Categoria <font color="red">*</font>'), $categoria_atividade_id ],
                                       [ new TLabel('Atividade <font color="red">*</font>'), $cadastro_atividade_id ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Breve descrição a ser exibida no histórico <font color="red">*</font>'), $descricao ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('Anexar comprovante em PDF <font color="red">*</font>'), $arquivo ] );
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Data de início <font color="red">*</font>'), $data_inicio ],
                                       [ new TLabel('Data de término <font color="red">*</font>'), $data_termino ],
                                       [ new TLabel('Horas <font color="red">*</font>'), $carga_horaria ],
                                       [ new TLabel('Responsável pela aprovação <font color="red">*</font>'), $cod_prof_responsavel ],
                                       [ new TLabel('Titulação do responsável <font color="red">*</font>'), $titulacao_prof_responsavel ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-1', 'col-sm-4', 'col-sm-3'];
        
        $this->form->addFields( [ '<br>' ] ); 
        $label1 = new TLabel('<font color="red">*</font> Campos obrigatórios', '', 10, 'i');
        $this->form->addContent( [$label1] );  
        
        $label_explicacao = '<p style="font-size: 16px;"><b>* NA DESCRIÇÃO DA ATIVIDADE COLOCAR <u>APENAS O NOME DO EVENTO E SEM ABREVIAÇÕES</u></b></p>
                             <p style="font-size: 16px;"><b>* SELECIONAR A CATEGORIA QUE <u>MELHOR SE ENCAIXA NA ATIVIDADE DESENVOLVIDA PELO ALUNO</u> COM BASE NO QUE DIZ O TEXTO DO CERTIFICADO</b></p>';        
                                       
        $panel = new TPanelGroup();
        $panel->add($label_explicacao);
        
        $this->form->addContent( [ $panel ] );
        

        $cod_aluno->addValidation('Cód. aluno', new TRequiredValidator);
        $nome_aluno->addValidation('Aluno', new TRequiredValidator);
        $cod_curso->addValidation('Cód. curso', new TRequiredValidator);
        $nome_curso->addValidation('Curso', new TRequiredValidator);
        $ano->addValidation('Ano (em que realizou a atividade)', new TRequiredValidator);
        $semestre->addValidation('Semestre (em que realizou a atividade)', new TRequiredValidator);
        $etapa->addValidation('Etapa (em que realizou a atividade)', new TRequiredValidator);
        $categoria_atividade_id->addValidation('Categoria', new TRequiredValidator);
        $cadastro_atividade_id->addValidation('Atividade', new TRequiredValidator);  
        $tipo_atividade->addValidation('Tipo', new TRequiredValidator);
        $arquivo->addValidation('Anexar comprovante em PDF', new TRequiredValidator);
        $descricao->addValidation('Breve descrição a ser exibida no histórico', new TRequiredValidator);
        $data_inicio->addValidation('Data de início', new TRequiredValidator);
        $data_termino->addValidation('Data de término', new TRequiredValidator);
        $carga_horaria->addValidation('Horas', new TRequiredValidator);
        $cod_prof_responsavel->addValidation('Responsável pela aprovação', new TRequiredValidator);
        $titulacao_prof_responsavel->addValidation('Titulação prof. responsável', new TRequiredValidator);
        

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


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('AtividadeComplementarSecretariaList','onReload')), 'fas:arrow-alt-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
                
        parent::add($container);
    }


    public static function onCursoExit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
           
            if(!empty($param['cod_curso']))
            {
                $cod_curso = $param['cod_curso'];
                
                //Verifica se o curso foi cadastrado em dados_curso e filtra as categorias pertencentes
                $criteria_dados_curso = new TCriteria;
                $criteria_dados_curso->add(new TFilter('codigo_curso_sistema', '=', $cod_curso));
            
                $dados_curso = DiplomaDigitalCurso::getObjects($criteria_dados_curso);
                
                if($dados_curso)
                {
                    $criteria_categorias = new TCriteria;
                    $criteria_categorias->add(new TFilter('dados_curso_id', '=', $dados_curso[0]->id));
                
                    $categorias = AtividadeComplementarCategoria::getObjects($criteria_categorias);
                    
                    if($categorias)
                    {
                        $criteria = TCriteria::create( ['dados_curso_id' => $dados_curso[0]->id ] );
                    
                        // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                        TDBCombo::reloadFromModel('form_AtividadeComplementarSecretaria', 'categoria_atividade_id', 'Felabs_DB', 'AtividadeComplementarCategoria', 'id', 'nome', 'nome', $criteria, TRUE);
                    }
                    else
                    {
                        new TMessage('error', 'Verifique se as categorias de atividades complementares para este curso foram cadastradas antes de prosseguir');
                    }
                    
                    
                    //Na troca de curso, recarrega as combos "Responsável pela aprovação" e "Titulação do responsável"
                    $loggedUnit = TSession::getValue('userunitid');

                    if($loggedUnit == 3)
                    {
                        $array_responsavel = [];
                        $array_responsavel['19'] = '19'; //Agronomia - Livia Cordaro                
                        $array_responsavel['6015'] = '6015'; //Direito - Bruno
                        $array_responsavel['2890'] = '2890'; //Sistemas - Murilo Scapim
                        $array_responsavel['1937'] = '1937'; //Enfermagem - Andreza Maeda
                        $array_responsavel['18'] = '18'; //Veterinária - ELZYLENE LÉGA
                    }
                    else
                    {
                        $array_responsavel = [];
                        $array_responsavel['29'] = '29'; //Administração, Gestão de RH e Contábeis - Lidiane Kanesiro
                        $array_responsavel['2401'] = '2401'; //Pedagogia - Fátima Gonini 
                        $array_responsavel['5622'] = '5622';//Engenharia Civil, Mecânica, Produção e Elétrica - Amanda Paula Caretta
                        $array_responsavel['27'] = '27'; //Luciana 2ª via
                        $array_responsavel['1852'] = '1852'; //Lisangela - Letras
                        $array_responsavel['59'] = '59'; //Wesley - Estudos Sociais
                    }
                    
                    $criteria_responsavel = new TCriteria;
                    $criteria_responsavel->add(new TFilter('id', 'IN', $array_responsavel));
                    
                    // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                    TDBCombo::reloadFromModel('form_AtividadeComplementarSecretaria', 'cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria_responsavel, TRUE);
                
                
                    $array_titulacao = [];
                    $array_titulacao['Tecnólogo'] = "Tecnólogo";
                    $array_titulacao['Graduação'] = "Graduação";
                    $array_titulacao['Especialização'] = "Especialização";
                    $array_titulacao['Mestrado'] = "Mestrado";
                    $array_titulacao['Doutorado'] = "Doutorado";
                    
                    TCombo::reload('form_AtividadeComplementarSecretaria', 'titulacao_prof_responsavel', $array_titulacao, TRUE);
                }
                else
                {
                    new TMessage('error', 'Verifique se o curso foi cadastrado antes de prosseguir');
                }
            }
            else
            {
                TCombo::clearField('form_AtividadeComplementarSecretaria', 'categoria_atividade_id'); 
                TCombo::clearField('form_AtividadeComplementarSecretaria', 'cadastro_atividade_id'); 

                $obj = new StdClass;
                $obj->tipo_atividade = '';
                $obj->cod_prof_responsavel = '';
                $obj->titulacao_prof_responsavel = '';
                
                TForm::sendData('form_AtividadeComplementarSecretaria', $obj);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }
    }
    
    
    public static function onCategoriaExit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
           
            if((!empty($param['cod_curso'])) AND (!empty($param['categoria_atividade_id'])))
            {
                $criteria_atividades = new TCriteria;
                $criteria_atividades->add(new TFilter('categoria_id', '=', $param['categoria_atividade_id']));
            
                $atividades = AtividadeComplementarCadastro::getObjects($criteria_atividades);
                
                if($atividades)
                {
                    $criteria = TCriteria::create( ['categoria_id' => $param['categoria_atividade_id'] ] );
                
                    // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                    TDBCombo::reloadFromModel('form_AtividadeComplementarSecretaria', 'cadastro_atividade_id', 'Felabs_DB', 'AtividadeComplementarCadastro', 'id', 'nome', 'nome', $criteria, TRUE);
                }
                else
                {
                    new TMessage('error', 'Verifique se as atividades desta categoria foram cadastradas antes de prosseguir');
                }
            }
            else
            { 
                TCombo::clearField('form_AtividadeComplementarSecretaria', 'cadastro_atividade_id'); 

                $obj = new StdClass;
                $obj->tipo_atividade = '';
                
                TForm::sendData('form_AtividadeComplementarSecretaria', $obj);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }
    }
    
    
    public static function onAtividadeExit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $atividade_id = $param['cadastro_atividade_id'] ?? null;
            
            $cadastro_atividade  = new AtividadeComplementarCadastro($atividade_id);
            
            $obj = new StdClass;
            $obj->tipo_atividade = $cadastro_atividade->nome;
                
            TForm::sendData('form_AtividadeComplementarSecretaria', $obj);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }            
    }
    

    public static function onEtapaChange($param)
    {
        try
        {
            TTransaction::open('dados_fei');
           
            if((!empty($param['cod_aluno'])) AND (!empty($param['cod_curso'])) AND (!empty($param['ano'])) AND (!empty($param['semestre'])))
            {
                //Pega a etapa em que o aluno estava matriculado quando realizou a atividade
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

                    TForm::sendData('form_AtividadeComplementarSecretaria', $obj);
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
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');             
                        
            $data = $this->form->getData(); 
            
            $object = new AtividadeComplementar;  
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
                
            
            //Confirma se a etapa em que o aluno estava matriculado quando realizou a atividade está correta
            $criteria_matricula = new TCriteria;
            $criteria_matricula->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
            $criteria_matricula->add(new TFilter('CodCurso', '=', $param['cod_curso']));
            $criteria_matricula->add(new TFilter('AnoMatricula', '=', $param['ano']));
            $criteria_matricula->add(new TFilter('SemestreMatricula', '=', $param['semestre']));
            
            $matricula = VwAlunoMatriculaEtapa::getObjects($criteria_matricula);
            
            //Se tiver matrícula e no formulário estiver diferente do que consta no BD, barra. Caso contrário, deixa salvar (ex: casos de transferência)
            if((!empty($matricula)) AND ($matricula[0]->EtapaMatricula <> $param['etapa']))
            {
                throw new Exception("Verifique a etapa em que o aluno estava matriculado quando realizou a atividade");
            }
            
            TTransaction::close();
                
            
            //Verifica se o ano contém 4 dígitos
            $count = strlen($object->ano);
            
            if($count <> 4)
            {
                throw new Exception("O campo Ano (em que realizou a atividade) precisa ter 4 dígitos");
            }
            
            
            //Verifica se a categoria pertence ao curso
            $categoria = new AtividadeComplementarCategoria($param['categoria_atividade_id']);
            $curso = DiplomaDigitalCurso::where('codigo_curso_sistema', '=', $param['cod_curso'])->load();
            
            if($categoria->dados_curso_id <> $curso[0]->id)
            {
                throw new Exception("A categoria escolhida não pertence ao curso selecionado");    
            }
            
            
            //Data de término não pode ser anterior à de início
            if($object->data_termino < $object->data_inicio){

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
            
            
            //Verifica se o ano de início do evento é o mesmo ano em que a atividade foi realizada (o ano de término pode ser diferente)
            if ($data_inicio[2] <> $object->ano) 
            {
                throw new Exception("O ano de realização da atividade e o ano de início estão diferentes");
            } 
            
            
            //Verifica se o semestre de início da atividade é o mesmo semestre em que a atividade foi realizada (o semestre de término pode ser diferente)
            if($object->semestre == '1' AND $data_inicio[1] > 6) 
            {
                throw new Exception("O semestre de realização da atividade e o semestre de início estão diferentes");
            }
            
            if($object->semestre == '2' AND $data_inicio[1] <= 6)
            {
                throw new Exception("O semestre de realização da atividade e o semestre de início estão diferentes");
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

              //Estudos Sociais  
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
                    $target_path  = 'secretaria/atividades_complementares/aluno_' . $object->cod_aluno;
                    
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
                        $nome_tmp = 'tmp/' . 'comprovante_atividade_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;
                        rename($source_file, $nome_tmp);
                                         
                                                
                        //Ghostscript usa o caminho absoluto
                        $caminho_absoluto_tmp = realpath($nome_tmp);                                                
                        $caminho_absoluto_target = realpath($target_path);   
                        $caminho_absoluto_pdf = $caminho_absoluto_target . '/comprovante_atividade_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;
                        
                        
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
                    
                    if($object->status_atividade == NULL)
                    {
                        $object->status_atividade = "Aguardando aprovação";
                    }                        
                           
                    $object->tipo_entrada = "Atividade Complementar";
                    $object->arquivo = 'comprovante_atividade_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;
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
                    
Uma nova atividade complementar foi enviada e aguarda sua análise.
    
Aluno(a): ' . $object->nome_aluno . '  
    
Curso: ' . $object->nome_curso;
    
                
                        if($professor->Email AND $corpoEmail)
                        {
                            $prefs = SystemPreference::getAllPreferences();
                            
                            $mail = new TMail;
                            $mail->setFrom($prefs['mail_from'], "Mensagem - Secretaria Acadêmica");
                            $mail->setSubject('Avaliação de Atividade Complementar');
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
                    
                    TApplication::loadPage('AtividadeComplementarSecretariaList', 'onReload');
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
            $this->fireEvents($object); 
            TTransaction::rollback(); 
        }
    }
    
    
    public function fireEvents( $object )
    {
        $obj = new stdClass;
        $obj->cod_curso = $object->cod_curso;
        $obj->categoria_atividade_id = $object->categoria_atividade_id;
        $obj->cadastro_atividade_id = $object->cadastro_atividade_id;
        $obj->cod_prof_responsavel = $object->cod_prof_responsavel;
        $obj->titulacao_prof_responsavel = $object->titulacao_prof_responsavel;
                        
        TForm::sendData('form_AtividadeComplementarSecretaria', $obj);    
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
                
                $object = new AtividadeComplementar($key); 
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