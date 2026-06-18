<?php
class AtividadeComplementarAlunoFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;
    
    public function __construct( $param )
    {
        parent::__construct();
        try
        {
            TTransaction::open('Felabs_DB');
            
            $criteria1 = null;

            $loggedUnit = TSession::getValue('userunitid');
            $loggedId = TSession::getValue('userid');        
            $user = new SystemUser($loggedId);   
            
            //Critério para carregar combo professor responsável pela aprovação na FFCL, FAJOB e NEAD
            if($loggedUnit == 2 OR $loggedUnit == 6 OR $loggedUnit == 10)
            {          
                $array_ffcl_fajob = [];
                $array_ffcl_fajob['29'] = '29'; //Administração, Gestão de RH e Contábeis - Lidiane Kanesiro
                $array_ffcl_fajob['2401'] = '2401'; //Pedagogia - Fátima Gonini 
                $array_ffcl_fajob['5622'] = '5622';//Engenharia Civil, Mecânica, Produção e Elétrica - Amanda Paula Caretta
                $array_ffcl_fajob['27'] = '27'; // Luciana 2ª Via
                $array_ffcl_fajob['1852'] = '1852'; //Lisangela - Letras
                $array_ffcl_fajob['59'] = '59'; //Wesley - Estudos Sociais
                
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('id', 'IN', $array_ffcl_fajob));
            }
                  
            //Critério para carregar combo professor responsável pela aprovação na FAFRAM
            if($loggedUnit == 3)
            {
                $array_fafram = [];
                $array_fafram['19'] = '19'; //Agronomia - Livia Cordaro
                $array_fafram['6015'] = '6015'; //Direito - Bruno
                $array_fafram['2890'] = '2890'; //Sistemas - Murilo Scapim
                $array_fafram['1937'] = '1937'; //Enfermagem - Andreza Maeda
                $array_fafram['18'] = '18'; //Veterinária - ELZYLENE LÉGA
                
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('id', 'IN', $array_fafram));
            }
        
            if($loggedUnit == 1 OR $loggedUnit == 8 OR $loggedUnit == 12)
            {
                new TMessage('info', 'Funcionalidade não disponível para esta unidade');
                die;
            }
     
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
        
        if (is_null($criteria1)) {
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('id', '<', '0'));
        }
        
        //Filtro para evitar pré-carregamento da combo categoria
        $criteria_categoria = new TCriteria;
        $criteria_categoria->add(new TFilter('id', '<', '0'));
        
        
        //Filtro para evitar pré-carregamento da combo atividade
        $criteria_atividade = new TCriteria;
        $criteria_atividade->add(new TFilter('id', '<', '0'));
         

        $this->form = new BootstrapFormBuilder('form_AtividadeComplementar');
        $this->form->setFormTitle('<h4>Minhas atividades complementares</h4>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $tipo_entrada = new THidden('tipo_entrada');
        $cod_aluno = new THidden('cod_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $cod_curso = new TCombo('cod_curso');
        $nome_curso = new THidden('nome_curso');
        $ano = new TEntry('ano');
        $semestre = new TCombo('semestre');
        $etapa = new TCombo('etapa');
        $tipo_atividade = new THidden('tipo_atividade');
        $data_inicio = new TDate('data_inicio');
        $data_termino = new TDate('data_termino');
        $carga_horaria = new TEntry('carga_horaria');
        $descricao = new TEntry('descricao');
        $cod_prof_responsavel = new TDBCombo('cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria1);
        $titulacao_prof_responsavel = new THidden('titulacao_prof_responsavel');
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


        //Critério para carregar combo curso
        try
        {
            TTransaction::open('dados_fei');   
            
            $aluno = new FiAluno($user->systemuser_codlegado); //Preenche nome do aluno  
    
            $criteria2 = new TCriteria;
            $criteria2->add( new TFilter('Codaluno', '=', $aluno->Codaluno));
            $criteria2->add( new TFilter('CodEntidade', '=', $loggedUnit));         
            
            $dados_aluno = VwAlunoMatriculaEtapa::getObjects($criteria2);
            
            //Preenche combo
            if($dados_aluno)
            {
                $cursos = [];
                foreach($dados_aluno as $dado_aluno)
                {
                    $cursos[$dado_aluno->CodCurso] = $dado_aluno->NomeCurso;
                }
                $codCursos = array_unique($cursos); //Agrupa códigos iguais
                $cod_curso->addItems($codCursos);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } 
        
        
        //Carrega as categorias de acordo com o curso escolhido
        $cod_curso->setChangeAction(new TAction(array($this, 'onCursoExit')));
        
        
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
                
        
        $arquivo->setAllowedExtensions(['pdf']);       
        
               
        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_entrada ] );
        $this->form->addFields( [ $cod_aluno ] );
        $this->form->addFields( [ $nome_curso ] );
        $this->form->addFields( [ $tipo_atividade ] );
        $this->form->addFields( [ $titulacao_prof_responsavel ] );
        $this->form->addFields( [ $caminho_arquivo ] );
        $this->form->addFields( [ $status_atividade ] );
        $this->form->addFields( [ $observacao ] );
        $this->form->addFields( [ $status_pdfa ] );
        $this->form->addFields( [ $status_assinatura ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        $this->form->addFields( [ $cod_atividade_historico ] );
        
        $row = $this->form->addFields( [ new TLabel('Aluno <font color="red">*</font>'), $nome_aluno ],
                                       [ new TLabel('Curso <font color="red">*</font>'), $cod_curso ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Ano (em que realizou a atividade) <font color="red">*</font>'), $ano ],
                                       [ new TLabel('Semestre (em que realizou a atividade) <font color="red">*</font>'), $semestre ],
                                       [ new TLabel('Etapa (em que realizou a atividade) <font color="red">*</font>'), $etapa ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Categoria <font color="red">*</font>'), $categoria_atividade_id ],
                                       [ new TLabel('Atividade <font color="red">*</font>'), $cadastro_atividade_id ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Breve descrição a ser exibida no histórico (Ex: nome do evento) <font color="red">*</font>'), $descricao ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('Anexar comprovante em PDF <font color="red">*</font>'), $arquivo ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('Data de início <font color="red">*</font>'), $data_inicio ],
                                       [ new TLabel('Data de término <font color="red">*</font>'), $data_termino ],
                                       [ new TLabel('Horas <font color="red">*</font>'), $carga_horaria ],
                                       [ new TLabel('Responsável pela aprovação <font color="red">*</font>'), $cod_prof_responsavel ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $this->form->addFields( [ '<br>' ] ); 
        $label1 = new TLabel('<font color="red">*</font> Campos obrigatórios', '', 10, 'i');
        $this->form->addContent( [$label1] );        


        $cod_aluno->addValidation('Cód. aluno', new TRequiredValidator);
        $nome_aluno->addValidation('Aluno', new TRequiredValidator);
        $cod_curso->addValidation('Curso', new TRequiredValidator);
        $ano->addValidation('Ano (em que realizou a atividade)', new TRequiredValidator);
        $semestre->addValidation('Semestre (em que realizou a atividade)', new TRequiredValidator);
        $etapa->addValidation('Etapa (em que realizou a atividade)', new TRequiredValidator);        
        $tipo_atividade->addValidation('Tipo', new TRequiredValidator);
        $data_inicio->addValidation('Data de início', new TRequiredValidator);
        $data_termino->addValidation('Data de término', new TRequiredValidator);
        $carga_horaria->addValidation('Horas', new TRequiredValidator);
        $descricao->addValidation('Breve descrição a ser exibida no histórico (Ex: nome do evento)', new TRequiredValidator);
        $cod_prof_responsavel->addValidation('Responsável pela aprovação', new TRequiredValidator);
        $arquivo->addValidation('Anexar comprovante em PDF', new TRequiredValidator);
        $categoria_atividade_id->addValidation('Categoria', new TRequiredValidator);
        $cadastro_atividade_id->addValidation('Atividade', new TRequiredValidator);        


        // set sizes
        $cod_aluno->setEditable(FALSE);
        $cod_aluno->setValue($aluno->Codaluno);
        $nome_aluno->setEditable(FALSE);
        $nome_aluno->setValue($aluno->Nome);
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
                
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'center');
        $column_tipo_atividade = new TDataGridColumn('tipo_atividade', 'Atividade', 'left');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Data de início', 'center');
        $column_data_termino = new TDataGridColumn('data_termino', 'Data de término', 'center');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'Carga Horária', 'center');
        $column_cod_prof_responsavel = new TDataGridColumn('responsavel->name', 'Responsável pela aprovação', 'center');
        $column_status_atividade = new TDataGridColumn('status_atividade', 'Status', 'center');


        $column_cod_prof_responsavel->setTransformer( array($this, 'setNomeResponsavel') );
        $column_status_atividade->setTransformer( array($this, 'setStatusColor') );


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_tipo_atividade);
        $this->datagrid->addColumn($column_data_inicio);
        $this->datagrid->addColumn($column_data_termino);
        $this->datagrid->addColumn($column_carga_horaria);
        $this->datagrid->addColumn($column_cod_prof_responsavel);
        $this->datagrid->addColumn($column_status_atividade);


        $action1 = new TDataGridAction([$this, 'onDelete']);
        $action1->setLabel(_t('Delete'));
        $action1->setImage('far:trash-alt red fa-lg');
        $action1->setField('id');
        $action1->setDisplayCondition( array($this, 'displayColumn') );
        

        $action2 = new TDataGridAction([$this, 'onDownload']);
        $action2->setLabel('Download');
        $action2->setImage('fas:cloud-download-alt blue fa-lg');
        $action2->setField('id');
                

        //Só exibe as informações da atividade, sem ação nenhuma, então não tem problema exibir formview do professor
        $action3 = new TDataGridAction(['AtividadeComplementarAnalisadaProfessorFormView', 'onEdit'], ['id'=>'{id}']);
        $action3->setLabel('Ver detalhes');
        $action3->setImage('fa:search green');
        $action3->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action3);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
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
                        new TMessage('error', 'Verifique com a secretaria acadêmica se as categorias de atividades complementares para este curso foram cadastradas antes de prosseguir');
                    }
                    
                    
                    //Na troca de curso, recarrega a combo "Responsável pela aprovação"
                    $loggedUnit = TSession::getValue('userunitid');

                    if($loggedUnit == 3)
                    {
                        $array_responsavel = [];
                        $array_responsavel['19'] = '19'; //Agronomia - Livia Cordaro
                        $array_responsavel['6015'] = '6015'; //Direito - Bruno
                        $array_responsavel['2890'] = '2890'; //Sistemas - Murilo Scapim
                        $array_responsavel['1937'] = '1937'; //Enfermagem - Andreza Maeda
                        $array_responsavel['18'] = "18"; //Veterinária - Aline Gomes
                    }
                    else
                    {
                        $array_responsavel = [];
                        $array_responsavel['29'] = '29'; //Administração, Gestão de RH e Contábeis - Lidiane Kanesiro
                        $array_responsavel['2401'] = '2401'; //Pedagogia - Fátima Gonini 
                        $array_responsavel['27'] = '27'; //Luciana 2ªvia
                        $array_responsavel['5622'] = '5622'; //Engenharia Civil, Mecânica, Produção e Elétrica - Amanda Paula Caretta
                        $array_responsavel['1852'] = '1852'; //Lisangela - Letras
                        $array_responsavel['59'] = '59'; //Wesley - Estudos Sociais
                    }
                    
                    $criteria_responsavel = new TCriteria;
                    $criteria_responsavel->add(new TFilter('id', 'IN', $array_responsavel));
                    
                    // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                    TDBCombo::reloadFromModel('form_AtividadeComplementar', 'cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria_responsavel, TRUE);
                }
                else
                {
                    new TMessage('error', 'Verifique com a secretaria acadêmica se o curso foi cadastrado antes de prosseguir');
                }
            }
            else
            {
                TCombo::clearField('form_AtividadeComplementar', 'categoria_atividade_id'); 
                TCombo::clearField('form_AtividadeComplementar', 'cadastro_atividade_id'); 

                $obj = new StdClass;
                $obj->tipo_atividade = '';
                $obj->cod_prof_responsavel = '';
                
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
                    new TMessage('error', 'Verifique com a secretaria acadêmica se as atividades desta categoria foram cadastradas antes de prosseguir');
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
    
    
    public function setNomeResponsavel($column_cod_prof_responsavel, $object, $row)
    {
        try
        {
            TTransaction::open('dados_fei');           
            
            $cod_professor = $object->cod_prof_responsavel;
            
            $responsavel = new FiProfessor($cod_professor);

            TTransaction::close();


            return $responsavel->Nome;            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function setStatusColor($column_status_atividade, $object, $row)
    {
        $color = $object->status_atividade;
        
        if($color == "Aguardando aprovação")
        {
            return '<span class="label label-warning">' . $column_status_atividade . '</span>';
        }
        elseif($color == "Aprovado")
        {
            return '<span class="label label-success">' . $column_status_atividade . '</span>';
        }
        elseif($color == "Reprovado")
        {
            return '<span class="label label-danger">' . $column_status_atividade . '</span>';
        }
        else
        {
            return $column_status_atividade;
        }    
    }


    public static function onDownload($param)
    {
        try
        {
            $id = $param['id'];
                
            TTransaction::open('Felabs_DB');

            $object = new AtividadeComplementar($id);

            if (strtolower(substr($object->arquivo, -4)) == 'html')
            {
                $win = TWindow::create( 'Arquivo', 0.8, 0.8 );
                $win->add( file_get_contents( $object->caminho_arquivo . '/' . $object->arquivo ) );
                $win->show();
            }
            else
            {
                TPage::openFile($object->caminho_arquivo . '/' . $object->arquivo);
            }
              
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');  
            
            $unit_id = TSession::getValue('userunitid');            
            $user_id = TSession::getValue('userid');                    
            $user = new SystemUser($user_id);
                
            TTransaction::close();
            
            
            //Filtra os cursos da unidade logada            
            TTransaction::open('dados_fei');
            
            $repository_curso = new TRepository('FiCurso');
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $cursos = $repository_curso->load($criteria_curso);

            foreach($cursos as $curso)
            {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            
            $aluno = new FiAluno($user->systemuser_codlegado);
            
            TTransaction::close();

            
            //Exibe só as atividades complementares do aluno logado na unidade correspondente
            TTransaction::open('Felabs_DB');            

            $repository = new TRepository('AtividadeComplementar');
            $limit = 20;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $aluno->Codaluno));
            $criteria->add(new TFilter('cod_curso', 'IN', $items));


            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->data_inicio = TDate::date2br($object->data_inicio);
                    $object->data_termino = TDate::date2br($object->data_termino);
                    $object->carga_horaria = substr($object->carga_horaria,0,-3);
                    
                    $this->datagrid->addItem($object);
                }
            }


            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 
            

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function displayColumn($object)
    {
        if($object->status_atividade == "Aguardando aprovação")
        {
            return TRUE;
        }
            return FALSE;
    }
    

    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);         

        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new AtividadeComplementar($key, FALSE);
                        
            //Apaga o arquivo
            if(file_exists($object->caminho_arquivo . '/' . $object->arquivo))
            {
                unlink($object->caminho_arquivo . '/' . $object->arquivo);
            }
            
            //Se diretório estiver vazio, apaga diretório (um diretório para cada aluno)
            $files = ((count(glob("$object->caminho_arquivo/*")) === 0) ? true : false);
            
            if($files == true)
            {
                rmdir($object->caminho_arquivo);
            }
            
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); 
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
            
            $cod_curso = $param['cod_curso'];            
            $fi_curso = new FiCurso($cod_curso);
            
            //Confirma se aluno tem matrícula no curso selecionado
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
            $criteria_curso->add(new TFilter('CodCurso', '=', $param['cod_curso']));
            
            $matricula_curso = VwAlunoMatriculaEtapa::getObjects($criteria_curso);
            
            if(empty($matricula_curso))
            {
                throw new Exception("Não consta matrícula do(a) aluno(a) no curso selecionado");
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
                throw new Exception("Verifique a etapa em que estava matriculado(a) quando realizou a atividade");
            }
            
            TTransaction::close();
                
            
            //Verifica se o ano contém 4 dígitos
            $count = strlen($object->ano);
            
            if($count <> 4)
            {
                throw new Exception("O campo 'Ano (em que realizou a atividade)' precisa ter 4 dígitos");
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
                    $object->nome_curso = $fi_curso->Nome;
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
                    
                    //Limpa o formulário depois de salvar, mas mantém o código e nome do aluno preenchido
                    $this->form->clear();
                                
                    $obj = new StdClass;
                    $obj->cod_aluno = $object->cod_aluno;
                    $obj->nome_aluno = $param['nome_aluno'];
                    
                    TForm::sendData('form_AtividadeComplementar', $obj);
                    
                    $this->onReload();
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
                                
        TForm::sendData('form_AtividadeComplementar', $obj);    
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        
        parent::show();
    }
}
