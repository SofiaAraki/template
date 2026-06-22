<?php

class AtividadeComplementarAdminForm extends TWindow
{
    protected $form;
    

    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Atividade Complementar');
        
        
        //Critério para filtrar os cursos de acordo com a unidade no momento de logar
        $loggedUnit = TSession::getValue('userunitid');
                
        $criteria_curso = new TCriteria;
        $criteria_curso->add(new TFilter('CodEntidade', '=', $loggedUnit));
                   
        
        //Critério para evitar pré-carregamento da combo atividade
        $criteria_atividade = new TCriteria;
        $criteria_atividade->add(new TFilter('id', '<', '0'));
        
        
        //Critério para carregar combo do professor responsável pela aprovação
        if($loggedUnit == 3)
        {
            $array_fafram = [];
            $array_fafram['19'] = '19'; //Agronomia - Livia Galdiano
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
            $array_ffcl_fajob['27'] = '27'; //Luciana para 2ªvia
            $array_ffcl_fajob['1852'] = '1852'; //Lisangela - Letras
            $array_ffcl_fajob['59'] = '59'; //Wesley - Estudos Sociais

           
            $criteria_prof = new TCriteria;
            $criteria_prof->add(new TFilter('id', 'IN', $array_ffcl_fajob));
        }
                 
                           
        try
        {
            TTransaction::open('Felabs_DB');
            
            $atividade_id = $param['id'];
            
            $atividade_complementar = new AtividadeComplementar($atividade_id);
            
            $criteria_dados_curso = new TCriteria;
            $criteria_dados_curso->add(new TFilter('codigo_curso_sistema', '=', $atividade_complementar->cod_curso));
            
            $dados_curso = DiplomaDigitalCurso::getObjects($criteria_dados_curso);
            
            //Critério para carregar combo categoria
            if($dados_curso)
            {
                $criteria_categoria = new TCriteria;
                $criteria_categoria->add(new TFilter('dados_curso_id', '=', $dados_curso[0]->id));
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }
              

        // creates the form
        $this->form = new BootstrapFormBuilder('form_AtividadeComplementar');
        $this->form->setFieldSizes('100%');
        $this->setSize(0.8, null);


        // create the form fields
        $id = new THidden('id');
        $tipo_entrada = new THidden('tipo_entrada');
        $cod_aluno = new THidden('cod_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $cod_curso = new TDBSeekButton('cod_curso', 'dados_fei', 'form_AtividadeComplementar', 'FiCurso', 'Nome', 'cod_curso', 'nome_curso', $criteria_curso);
        $nome_curso = new TEntry('nome_curso');
        $ano = new TEntry('ano');
        $semestre = new TCombo('semestre');
        $etapa = new TCombo('etapa');
        $tipo_atividade = new THidden('tipo_atividade');
        $atividade_historico = new TEntry('atividade_historico'); //Componente auxiliar, não será salvo no banco
        $data_inicio = new TDate('data_inicio');
        $data_termino = new TDate('data_termino');
        $carga_horaria = new TNumeric('carga_horaria', 2, '.', '', true);
        $descricao = new TEntry('descricao');
        $cod_prof_responsavel = new TDBCombo('cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria_prof);
        $titulacao_prof_responsavel = new TCombo('titulacao_prof_responsavel');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $arquivo = new THidden('arquivo');
        $status_atividade = new TRadioGroup('status_atividade');
        $observacao = new TText('observacao');
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
            

        //Status atividade
        $radio_status['Aprovado'] = "Aprovar";
        $radio_status['Reprovado'] = "Reprovar";
        
        $status_atividade->addItems($radio_status);

        $status_atividade->setChangeAction(new TAction(array($this, 'onStatusChange')));


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_entrada ] );
        $this->form->addFields( [ $cod_aluno ] );
        $this->form->addFields( [ $tipo_atividade ] );
        $this->form->addFields( [ $caminho_arquivo ] );
        $this->form->addFields( [ $arquivo ] ); 
        $this->form->addFields( [ $status_pdfa ] );
        $this->form->addFields( [ $status_assinatura ] );       
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        $this->form->addFields( [ $cod_atividade_historico ] );

        
        $row = $this->form->addFields( [ new TLabel('Aluno'), $nome_aluno ],
                                       [ new TLabel('Cód. curso'), $cod_curso ],
                                       [ new TLabel('Curso'), $nome_curso ] );
        $row->layout = ['col-sm-6', 'col-sm-2', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Ano (em que realizou a atividade)'), $ano ],
                                       [ new TLabel('Semestre (em que realizou a atividade)'), $semestre ],
                                       [ new TLabel('Etapa (em que realizou a atividade)'), $etapa ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Categoria'), $categoria_atividade_id ],
                                       [ new TLabel('Atividade'), $cadastro_atividade_id ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];              
        
        $row = $this->form->addFields( [ new TLabel('Exibição no histórico'), $atividade_historico ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('Descrição'), $descricao ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('Data de início'), $data_inicio ],
                                       [ new TLabel('Data de término'), $data_termino ],
                                       [ new TLabel('Horas'), $carga_horaria ],
                                       [ new TLabel('Responsável pela aprovação'), $cod_prof_responsavel ],
                                       [ new TLabel('Titulação do responsável'), $titulacao_prof_responsavel ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-1', 'col-sm-4', 'col-sm-3'];
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ $status_atividade ] );
        $row->layout = ['col-sm-12'];
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Observação (caso a atividade seja reprovada, deve-se informar o motivo no campo abaixo - <b>este, ficará visível para o aluno</b>)'), $observacao ] );
        $row->layout = ['col-sm-12'];


        $cod_curso->addValidation('Cód. curso', new TRequiredValidator);
        $ano->addValidation('Ano (em que realizou a atividade)', new TRequiredValidator);
        $semestre->addValidation('Semestre (em que realizou a atividade)', new TRequiredValidator);
        $etapa->addValidation('Etapa (em que realizou a atividade)', new TRequiredValidator);
        $categoria_atividade_id->addValidation('Categoria', new TRequiredValidator);
        $cadastro_atividade_id->addValidation('Atividade', new TRequiredValidator); 
        $tipo_atividade->addValidation('Exibição no histórico', new TRequiredValidator);
        $descricao->addValidation('Descrição', new TRequiredValidator);
        $data_inicio->addValidation('Data de início', new TRequiredValidator);
        $data_termino->addValidation('Data de término', new TRequiredValidator);
        $carga_horaria->addValidation('Horas', new TRequiredValidator);
        $cod_prof_responsavel->addValidation('Responsável pela aprovação', new TRequiredValidator);
        $titulacao_prof_responsavel->addValidation('Titulação do responsável', new TRequiredValidator);


        // set sizes
        $nome_aluno->setEditable(FALSE);
        $nome_curso->setEditable(FALSE);
        $ano->setMask('9999');
        $atividade_historico->setEditable(FALSE);
        $atividade_historico->setValue($atividade_complementar->tipo_atividade);
        $data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_termino->setMask('dd/mm/yyyy');
        $data_termino->setDatabaseMask('yyyy-mm-dd');
        $status_atividade->setSize('100%');
        $status_atividade->setLayout('horizontal');
        $status_atividade->setUseButton();
        $observacao->setSize('100%', 50);


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }


        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        
        
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
                        TDBCombo::reloadFromModel('form_AtividadeComplementar', 'categoria_atividade_id', 'Felabs_DB', 'AtividadeComplementarCategoria', 'id', 'nome', 'nome', $criteria, TRUE);
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
                        $array_responsavel['19'] = '19'; //Agronomia - Livia Galdiano
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
                        $array_responsavel['5622'] = '5622';
                        $array_responsavel['27'] = '27'; //Luciana 2ªvia 
                        $array_responsavel['1852'] = '1852'; //Lisangela - Letras
                        $array_responsavel['59'] = '59'; //Wesley - Estudos Sociais
                    }
                    
                    $criteria_responsavel = new TCriteria;
                    $criteria_responsavel->add(new TFilter('id', 'IN', $array_responsavel));
                    
                    // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                    TDBCombo::reloadFromModel('form_AtividadeComplementar', 'cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria_responsavel, TRUE);
                
                
                    $array_titulacao = [];
                    $array_titulacao['Tecnólogo'] = "Tecnólogo";
                    $array_titulacao['Graduação'] = "Graduação";
                    $array_titulacao['Especialização'] = "Especialização";
                    $array_titulacao['Mestrado'] = "Mestrado";
                    $array_titulacao['Doutorado'] = "Doutorado";
                    
                    TCombo::reload('form_AtividadeComplementar', 'titulacao_prof_responsavel', $array_titulacao, TRUE);
                }
                else
                {
                    new TMessage('error', 'Verifique se o curso foi cadastrado antes de prosseguir');
                }
            }
            else
            {
                TCombo::clearField('form_AtividadeComplementar', 'categoria_atividade_id'); 
                TCombo::clearField('form_AtividadeComplementar', 'cadastro_atividade_id'); 
                
                $obj = new StdClass;
                $obj->tipo_atividade = '';
                $obj->cod_prof_responsavel = '';
                $obj->titulacao_prof_responsavel = '';
                
                TForm::sendData('form_AtividadeComplementar', $obj);
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
                    TDBCombo::reloadFromModel('form_AtividadeComplementar', 'cadastro_atividade_id', 'Felabs_DB', 'AtividadeComplementarCadastro', 'id', 'nome', 'nome', $criteria, TRUE);
                }
                else
                {
                    new TMessage('error', 'Verifique se as atividades desta categoria foram cadastradas antes de prosseguir');
                }
            }
            else
            { 
                TCombo::clearField('form_AtividadeComplementar', 'cadastro_atividade_id'); 

                $obj = new StdClass;
                $obj->tipo_atividade = '';
                                
                TForm::sendData('form_AtividadeComplementar', $obj);
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
            
            $atividade_id = $param['cadastro_atividade_id'];
            
            $cadastro_atividade  = new AtividadeComplementarCadastro($atividade_id);
            
            $obj = new StdClass;
            $obj->tipo_atividade = $cadastro_atividade->nome;
                
            TForm::sendData('form_AtividadeComplementar', $obj);
            
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

                    TForm::sendData('form_AtividadeComplementar', $obj);
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
    

    public static function onStatusChange($param)
    {
        $status_atividade = $param['status_atividade'];
            
        if($status_atividade == 'Reprovado')
        {
            TText::enableField('form_AtividadeComplementar', 'observacao');     
        }
        else
        {
            TText::clearField('form_AtividadeComplementar', 'observacao'); 
            TText::disableField('form_AtividadeComplementar', 'observacao'); 
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
            
            
            //Atividade ID vai "substituir" o tipo_atividade ao ser exibida no histórico, então os dois campos precisam referenciar a mesma atividade
            $atividade_id = $param['cadastro_atividade_id'] ?? nul;
            $cadastro_atividade = new AtividadeComplementarCadastro($atividade_id);
            
            
            //O campo atividade_historico recebeu o valor salvo em tipo_atividade
            if($cadastro_atividade->nome <> $param['atividade_historico'])
            {
                throw new Exception("A atividade deve referenciar a mesma salva anteriormente e exibida no campo 'Exibição no histórico'");
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
                        
            
            //Se status for Reprovado, o campo observação é obrigatório
            if($object->status_atividade == 'Reprovado')
            {
                if(! $object->observacao)
                {
                    throw new Exception("O motivo da reprovação da atividade deve ser especificado");    
                }
            }

            if($object->status_atividade <> 'Aprovado' AND $object->status_atividade <> 'Reprovado')
            {
                $object->status_atividade = 'Aguardando aprovação';
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
            
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s'); 
            
            $object->store();
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('AtividadeComplementarSecretariaList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());    
                                
            //Mantém campo dependente habilitado ou desabilitado dependendo da escolha do usuário
            $status_atividade = $param['status_atividade'];
                    
            if($status_atividade == 'Reprovado')
            {
                TText::enableField('form_AtividadeComplementar', 'observacao');     
            }
            else
            {
                TText::clearField('form_AtividadeComplementar', 'observacao'); 
                TText::disableField('form_AtividadeComplementar', 'observacao'); 
            }
            
            $this->fireEvents($object);
            
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
                
                $object = new AtividadeComplementar($key);                
                
                if($object->status_atividade <> 'Reprovado')
                {
                    TText::disableField('form_AtividadeComplementar', 'observacao');     
                }
                
                $cod_responsavel = $object->cod_prof_responsavel;
                
                $this->form->setData($object);
                $this->fireEvents( $object ); 
                
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
    
    
    public function fireEvents( $object )
    {
        $obj = new stdClass;
        $obj->cod_curso = $object->cod_curso;
        $obj->categoria_atividade_id = $object->categoria_atividade_id;
        $obj->cadastro_atividade_id = $object->cadastro_atividade_id;
        $obj->cod_prof_responsavel = $object->cod_prof_responsavel;
        $obj->titulacao_prof_responsavel = $object->titulacao_prof_responsavel;
                        
        TForm::sendData('form_AtividadeComplementarAdmin', $obj);    
    }
}

