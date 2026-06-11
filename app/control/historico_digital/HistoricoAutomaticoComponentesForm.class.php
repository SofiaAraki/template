<?php

class HistoricoAutomaticoComponentesForm extends TPage
{
    protected static $formDisciplinas; 
    protected $datagrid_disciplinas; 
    protected $pageNavigationDisciplinas;
    protected $loadedDisciplinas;
    
    //Só para exibição, não são manipulados
    private $formMestre;
    private $notebook;
    private $datagrid_atividades; 
    private $loadedAtividades;
    private $datagrid_estagios;
    private $loadedEstagios;
    private $datagrid_situacoes;   
    private $loadedSituacoes;
    
    private $cod_disciplina;    

    public function __construct( $param )
    {
        parent::__construct();        
        
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $dados_historico_digital = TSession::getValue('dados_historico_digital');
            $nome_aluno = $dados_historico_digital->diploma_digital_diplomado->nome;
            $nome_curso = $dados_historico_digital->diploma_digital_curso->nome_curso_diploma;
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }    
        
        
        // creates the form
        $this->formMestre = new BootstrapFormBuilder('form_mestre_HistoricoAutomatico');
        $this->formMestre->setFormTitle('<h4>Componentes Curriculares - Histórico Digital</h4>');

        $this->formMestre->addFields( [new TLabel('Aluno:')], [$nome_aluno] );
        $this->formMestre->addFields( [new TLabel('Curso:')], [$nome_curso] );
        
        
        //ABA 1 - DISCIPLINAS
        
        $this->formDisciplinas = new BootstrapFormBuilder('form_HistoricoAutomaticoDisciplinas');
        $this->formDisciplinas->setFormTitle('');
        $this->formDisciplinas->setFieldSizes('100%');               


        // create the form fields
        $id = new THidden('id');
        $historico_digital_id = new THidden('historico_digital_id');
        $ano = new TEntry('ano');
        $semestre = new TCombo('semestre');
        $etapa = new TCombo('etapa');
        $tipo_entrada = new TCombo('tipo_entrada');
        $this->cod_disciplina = new TSeekButton('cod_disciplina');
        $nome_disciplina = new TEntry('nome_disciplina');
        $carga_horaria = new TNumeric('carga_horaria', 2, '.', '', true);
        $frequencia = new TNumeric('frequencia', 2, '.', '', true);
        $nota = new TNumeric('nota', 2, '.', '', true);
        $situacao = new TCombo('situacao');
        $forma_integralizacao = new TCombo('forma_integralizacao');
        $cod_professor = new TEntry('cod_professor');
        $nome_professor = new TEntry('nome_professor');
        $titulacao_professor = new TCombo('titulacao_professor');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        $cod_disciplina_historico = new THidden('cod_disciplina_historico');

 
        $this->cod_disciplina->setAction(new TAction(['BuscaDisciplinaHistorico', 'onReload']));

        
        
        $situacao->setChangeAction(new TAction(array($this, 'onChangeSituacaoDisciplina')));
        
        
        //Tipo entrada
        $combo_tipo = [];
        $combo_tipo['Disciplina'] = "Disciplina";
        $combo_tipo['Atividade Complementar'] = "Atividade Compl.";
        $combo_tipo['Estágio'] = "Estágio";
            
        $tipo_entrada->addItems($combo_tipo);    
        
        
        //Semestre
        $combo_semestre = [];
        $combo_semestre['1'] = "1º";
        $combo_semestre['2'] = "2º";
        
        $semestre->addItems($combo_semestre);
        
        
        //Etapa
        $combo_etapa = [];
        $combo_etapa['1'] = "1";
        $combo_etapa['2'] = "2";
        $combo_etapa['3'] = "3";
        $combo_etapa['4'] = "4";
        $combo_etapa['5'] = "5";
        $combo_etapa['6'] = "6";
        $combo_etapa['7'] = "7";
        $combo_etapa['8'] = "8";
        $combo_etapa['9'] = "9";
        $combo_etapa['10'] = "10";
        $combo_etapa['11'] = "11";
        $combo_etapa['12'] = "12";
        $combo_etapa['13'] = "13";
        $combo_etapa['14'] = "14";
        $combo_etapa['15'] = "15";
        
        $etapa->addItems($combo_etapa); 
        
        
        //Situação
        $combo_situacao = [];
        $combo_situacao['Aprovado'] = "Aprovado";
        $combo_situacao['Pendente'] = "Pendente";
        $combo_situacao['Reprovado'] = "Reprovado";
        
        $situacao->addItems($combo_situacao);    
        

        //Forma de integralização
        $combo_integralizacao = [];
        $combo_integralizacao['Cursado'] = "Cursado";
        $combo_integralizacao['Validado'] = "Validado";
        $combo_integralizacao['Aproveitado'] = "Aproveitado";
        
        $forma_integralizacao->addItems($combo_integralizacao);    


        //Titulação
        $combo_titulacao = [];
        $combo_titulacao['Tecnólogo'] = "Tecnólogo";
        $combo_titulacao['Graduação'] = "Graduação";
        $combo_titulacao['Especialização'] = "Especialização";
        $combo_titulacao['Mestrado'] = "Mestrado";
        $combo_titulacao['Doutorado'] = "Doutorado";
            
        $titulacao_professor->addItems($combo_titulacao);
            
            
        // add the fields
        $this->formDisciplinas->addFields( [ $id ] );
        $this->formDisciplinas->addFields( [ $historico_digital_id ] );
        $this->formDisciplinas->addFields( [ $system_user_id ] );
        $this->formDisciplinas->addFields( [ $data_reg ] );
        $this->formDisciplinas->addFields( [ $cod_disciplina_historico ] );       
        
        $row = $this->formDisciplinas->addFields( [ new TLabel('Tipo de entrada'), $tipo_entrada ],
                                                  [ new TLabel('Cód. disciplina (busca pelo nome)'), $this->cod_disciplina ],
                                                  [ new TLabel('Nome disciplina no histórico'), $nome_disciplina ]);
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-7'];
        
        $row = $this->formDisciplinas->addFields( [ new TLabel('Cód. Professor'), $cod_professor ],
                                                  [ new TLabel('Professor'), $nome_professor ],
                                                  [ new TLabel('Titulação'), $titulacao_professor ]);
        $row->layout = ['col-sm-2', 'col-sm-7', 'col-sm-3'];
        
        $row = $this->formDisciplinas->addFields( [ new TLabel('Ano'), $ano ],
                                                  [ new TLabel('Sem'), $semestre ],
                                                  [ new TLabel('Etapa'), $etapa ],
                                                  [ new TLabel('CH'), $carga_horaria ],
                                                  [ new TLabel('Freq.'), $frequencia ],
                                                  [ new TLabel('Nota'), $nota ],
                                                  [ new TLabel('Situação'), $situacao ],
                                                  [ new TLabel('Forma de integralização'), $forma_integralizacao ] );
        $row->layout = ['col-sm-1', 'col-sm-1', 'col-sm-1', 'col-sm-1', 'col-sm-1',  'col-sm-1', 'col-sm-3', 'col-sm-3'];


        $ano->addValidation('Ano', new TRequiredValidator);
        $semestre->addValidation('Semestre', new TRequiredValidator);
        $etapa->addValidation('Etapa', new TRequiredValidator);     
        $tipo_entrada->addValidation('Tipo de entrada', new TRequiredValidator);
        $this->cod_disciplina->addValidation('Cód. disciplina (busca pelo nome)', new TRequiredValidator);
        $nome_disciplina->addValidation('Nome disciplina no histórico', new TRequiredValidator);
        $carga_horaria->addValidation('CH', new TRequiredValidator);
        $frequencia->addValidation('Frequência', new TRequiredValidator);
        $nota->addValidation('Nota', new TRequiredValidator);
        $situacao->addValidation('Situação', new TRequiredValidator);


        // set sizes
        $nome_disciplina->setEditable(FALSE);
        $ano->setMask('9999');        
        $cod_professor->setMask('9!');
        $nome_professor->forceUpperCase();


        // create the form actions
        $btn_save_disciplina = $this->formDisciplinas->addAction(_t('Save'), new TAction([$this, 'onSaveDisciplina']), 'fa:save');
        $btn_save_disciplina->class = 'btn btn-sm btn-primary';
        $this->formDisciplinas->addAction('Limpar campos', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->formDisciplinas->addAction('Voltar', new TAction(array('HistoricoAutomaticoList','onReload')), 'fas:arrow-alt-circle-left blue');        
       
        
        $user_groups = (array) TSession::getValue('usergroupids');

        if (in_array(1, $user_groups))
        {
            $this->formDisciplinas->addAction(
                'Importar do Genesi',
                new TAction(['BuscaDisciplinaHistorico', 'onSelectAll']),
                'fa:cloud-download-alt green'
            );
        }

 
 
        // creates a Datagrid
        $this->datagrid_disciplinas = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_disciplinas->style = 'width: 100%';
        $this->datagrid_disciplinas->datatable = 'true';
        $this->datagrid_disciplinas->disableDefaultClick();
        

        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'ID', 'left');
        //$column_historico_digital_id = new TDataGridColumn('historico_digital_id', 'Histórico Digital ID', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'center', '3%');
        $column_semestre = new TDataGridColumn('semestre', 'Sem', 'center', '3%');        
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'center', '3%');
        //$column_tipo_entrada = new TDataGridColumn('tipo_entrada', 'Tipo de entrada', 'left');     
        //$column_cod_disciplina = new TDataGridColumn('cod_disciplina', 'Cód. disciplina', 'center');
        $column_nome_disciplina = new TDataGridColumn('nome_disciplina', 'Disciplina', 'left', '28%');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'CH', 'center');
        //$column_frequencia = new TDataGridColumn('frequencia', 'Frequência', 'center');   
        $column_nota = new TDataGridColumn('nota', 'Nota', 'center');             
        $column_situacao = new TDataGridColumn('situacao', 'Situação', 'center');
        $column_forma_integralizacao = new TDataGridColumn('forma_integralizacao', 'Forma de integralização', 'center');
        //$column_cod_professor = new TDataGridColumn('cod_professor', 'Cód. professor', 'center');
        $column_nome_professor = new TDataGridColumn('nome_professor', 'Professor', 'left', '20%');
        $column_titulacao_professor = new TDataGridColumn('titulacao_professor', 'Titulação', 'left');


        $column_carga_horaria->setTotalFunction( function($values) {
            $total = new TElement('span');
            $total->id = 'ch_total_disciplinas';
            $total->style = 'float:center; font-weight:bold; color: black; font-size: 12pt;';
            $total->add(array_sum((array) $values));
        
            return "TOTAL: " . $total;
        });
        

        // add the columns to the DataGrid
        //$this->datagrid_disciplinas->addColumn($column_id)->setVisibility(false);
        //$this->datagrid_disciplinas->addColumn($column_historico_digital_id)->setVisibility(false);
        $this->datagrid_disciplinas->addColumn($column_etapa);
        $this->datagrid_disciplinas->addColumn($column_ano);
        $this->datagrid_disciplinas->addColumn($column_semestre);
        //$this->datagrid_disciplinas->addColumn($column_tipo_entrada)->setVisibility(false);
        //$this->datagrid_disciplinas->addColumn($column_cod_disciplina)->setVisibility(false);
        $this->datagrid_disciplinas->addColumn($column_nome_disciplina);
        $this->datagrid_disciplinas->addColumn($column_carga_horaria);
        //$this->datagrid_disciplinas->addColumn($column_frequencia)->setVisibility(false); 
        $this->datagrid_disciplinas->addColumn($column_nota);               
        $this->datagrid_disciplinas->addColumn($column_situacao);
        $this->datagrid_disciplinas->addColumn($column_forma_integralizacao);
        //$this->datagrid_disciplinas->addColumn($column_cod_professor)->setVisibility(false);
        $this->datagrid_disciplinas->addColumn($column_nome_professor);
        $this->datagrid_disciplinas->addColumn($column_titulacao_professor);

                
        $action_edit_disciplina = new TDataGridAction([$this, 'onEditDisciplina']);
        //$action_edit_disciplina->setUseButton(TRUE);
        //$action_edit_disciplina->setButtonClass('btn btn-default');
        $action_edit_disciplina->setLabel(_t('Edit'));
        $action_edit_disciplina->setImage('far:edit blue');
        $action_edit_disciplina->setField('id');
        
        
        $action_atualizar_disciplina = new TDataGridAction([$this, 'onAtualizarDisciplina']);
        //$action_atualizar_disciplina->setUseButton(TRUE);
        //$action_atualizar_disciplina->setButtonClass('btn btn-default');
        $action_atualizar_disciplina->setLabel('Atualizar nota/situação');
        $action_atualizar_disciplina->setImage('fa:sync green');
        $action_atualizar_disciplina->setField('id');
        $action_atualizar_disciplina->setDisplayCondition(array($this, 'displayColumnAtualizar'));
        
        
        $action_del_disciplina = new TDataGridAction([$this, 'onDeleteDisciplina']);
        //$action_del_disciplina->setUseButton(TRUE);
        //$action_del_disciplina->setButtonClass('btn btn-default');
        $action_del_disciplina->setLabel(_t('Delete'));
        $action_del_disciplina->setImage('far:trash-alt red');
        $action_del_disciplina->setField('id');
        $action_del_disciplina->setDisplayCondition(array($this, 'displayColumnDelete'));
        
        
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');        
                
        $action_group->addAction($action_del_disciplina);
        $action_group->addAction($action_edit_disciplina);
        $action_group->addAction($action_atualizar_disciplina);
        
        // add the actions to the datagrid        
        $this->datagrid_disciplinas->addActionGroup($action_group);
       
               
        // create the datagrid model
        $this->datagrid_disciplinas->createModel();
        
        
        // creates the page navigation
        $this->pageNavigationDisciplinas = new TPageNavigation;
        $this->pageNavigationDisciplinas->setAction(new TAction([$this, 'onReloadDisciplinas']));
        $this->pageNavigationDisciplinas->setWidth($this->datagrid_disciplinas->getWidth());
        
                
        //Acrescenta formulário e datagrid disciplinas em um container
        $vbox_disciplina = new TVBox;
        $vbox_disciplina->style = 'width: 100%';
        $vbox_disciplina->add($this->formDisciplinas);
        $vbox_disciplina->add(TPanelGroup::pack('', $this->datagrid_disciplinas, $this->pageNavigationDisciplinas));        
        
        
        //-------------------------------------//------------------------------------- //-------------------------------------//
        
        
        //ABA 2 - ATIVIDADES COMPLEMENTARES
        
        // creates a Datagrid
        $this->datagrid_atividades = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_atividades->style = 'width: 100%';
        $this->datagrid_atividades->datatable = 'true';
        $this->datagrid_atividades->disableDefaultClick();


        // creates the datagrid columns
        $column_tipo_entrada = new TDataGridColumn('tipo_entrada', 'Tipo', 'center', '10%');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'center', '4%');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'center', '4%');
        $column_semestre = new TDataGridColumn('semestre', 'Sem', 'center', '4%');
        $column_tipo_atividade = new TDataGridColumn('tipo_atividade', 'Atividade', 'left', '30%');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Início', 'center', '6%');
        $column_data_termino = new TDataGridColumn('data_termino', 'Término', 'center', '6%');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'CH', 'center', '4%');
        $column_nome_professor = new TDataGridColumn('nome_professor', 'Professor', 'center');
        $column_titulacao_professor = new TDataGridColumn('titulacao_prof_responsavel', 'Titulação', 'left');
        $column_status_atividade = new TDataGridColumn('status_atividade', 'Status atividade', 'center');


        $column_carga_horaria->setTotalFunction( function($values) {
            $total = new TElement('span');
            $total->id = 'ch_total_atividade';
            $total->style = 'float:center; font-weight:bold; color: black; font-size: 12pt;';
            $total->add(array_sum((array) $values));
        
            return "TOTAL: " . $total;
        });
        

        $column_nome_professor->setTransformer( array($this, 'setNomeResponsavel') );
        

        // add the columns to the DataGrid
        $this->datagrid_atividades->addColumn($column_tipo_entrada);
        $this->datagrid_atividades->addColumn($column_etapa);
        $this->datagrid_atividades->addColumn($column_ano);
        $this->datagrid_atividades->addColumn($column_semestre);
        $this->datagrid_atividades->addColumn($column_tipo_atividade);
        $this->datagrid_atividades->addColumn($column_data_inicio);
        $this->datagrid_atividades->addColumn($column_data_termino);
        $this->datagrid_atividades->addColumn($column_carga_horaria);
        $this->datagrid_atividades->addColumn($column_nome_professor);
        $this->datagrid_atividades->addColumn($column_titulacao_professor);
        $this->datagrid_atividades->addColumn($column_status_atividade);


        // create the datagrid model
        $this->datagrid_atividades->createModel();


        //-------------------------------------//------------------------------------- //-------------------------------------//
        
        
        //ABA 3 - ESTÁGIOS
        
        // creates a Datagrid
        $this->datagrid_estagios = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_estagios->style = 'width: 100%';
        $this->datagrid_estagios->datatable = 'true';
        $this->datagrid_estagios->disableDefaultClick();


        // creates the datagrid columns
        $column_tipo_entrada = new TDataGridColumn('tipo_entrada', 'Tipo', 'center');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'center');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'center');
        $column_semestre = new TDataGridColumn('semestre', 'Sem', 'center');
        $column_razao_social_empresa = new TDataGridColumn('razao_social_empresa', 'Empresa', 'left');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Início', 'center');
        $column_data_termino = new TDataGridColumn('data_termino', 'Término', 'center');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'CH', 'center');
        $column_nome_professor = new TDataGridColumn('nome_professor', 'Professor', 'left');
        $column_titulacao_professor = new TDataGridColumn('titulacao_prof_responsavel', 'Titulação', 'center');
        $column_status_estagio = new TDataGridColumn('status_estagio', 'Status estágio', 'center');

    
        $column_carga_horaria->setTotalFunction( function($values) {
            $total = new TElement('span');
            $total->id = 'ch_total_estagio';
            $total->style = 'float:center; font-weight:bold; color: black; font-size: 12pt;';
            $total->add(array_sum((array) $values));
        
            return "TOTAL: " . $total;
        });
        
        
        $column_nome_professor->setTransformer( array($this, 'setNomeResponsavel') );
        

        // add the columns to the DataGrid
        $this->datagrid_estagios->addColumn($column_tipo_entrada);
        $this->datagrid_estagios->addColumn($column_etapa);
        $this->datagrid_estagios->addColumn($column_ano);
        $this->datagrid_estagios->addColumn($column_semestre);
        $this->datagrid_estagios->addColumn($column_razao_social_empresa);
        $this->datagrid_estagios->addColumn($column_data_inicio);
        $this->datagrid_estagios->addColumn($column_data_termino);
        $this->datagrid_estagios->addColumn($column_carga_horaria);
        $this->datagrid_estagios->addColumn($column_nome_professor);
        $this->datagrid_estagios->addColumn($column_titulacao_professor);
        $this->datagrid_estagios->addColumn($column_status_estagio);


        // create the datagrid model
        $this->datagrid_estagios->createModel();
        
        
        //-------------------------------------//------------------------------------- //-------------------------------------//
        
        
        //ABA 4 - SITUAÇÕES DISCENTE
        
        /*Exibe as situações das matrículas registradas no Genesi, mas para gerar os XMLs faz a correspondência das situações aceitas pelo MEC
         (No caso do histórico automático, as situações não são salvas na tabela historico_situacao_discente)*/
         
        // creates a Datagrid
        $this->datagrid_situacoes = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_situacoes->style = 'width: 100%';
        $this->datagrid_situacoes->datatable = 'true';
        $this->datagrid_situacoes->disableDefaultClick();
        

        // creates the datagrid columns
        $column_tipo_entrada = new TDataGridColumn('tipo_entrada', 'Tipo', 'center', 50);
        $column_situacao_etapa = new TDataGridColumn('EtapaMatricula', 'Etapa', 'center');
        $column_situacao_ano = new TDataGridColumn('AnoMatricula', 'Ano', 'center');
        $column_situacao_semestre = new TDataGridColumn('SemestreMatricula', 'Sem', 'center');
        $column_situacao_matricula = new TDataGridColumn('SituacaoMatricula', 'Situação da matrícula no Genesi', 'center');
        $column_situacao_discente = new TDataGridColumn('situacao_discente', 'Situação correspondente aceita pelo MEC', 'center');
        

        // add the columns to the DataGrid
        $this->datagrid_situacoes->addColumn($column_tipo_entrada);
        $this->datagrid_situacoes->addColumn($column_situacao_etapa);
        $this->datagrid_situacoes->addColumn($column_situacao_ano);
        $this->datagrid_situacoes->addColumn($column_situacao_semestre);
        $this->datagrid_situacoes->addColumn($column_situacao_matricula);
        $this->datagrid_situacoes->addColumn($column_situacao_discente);
        
        
        // create the datagrid model
        $this->datagrid_situacoes->createModel();


        //-------------------------------------//------------------------------------- //-------------------------------------//
        
        
        //Acrescenta abas ao notebook
        $this->notebook = new TNotebook('notebook_componentes');        
        $this->notebook->appendPage('<h4>Disciplinas</h4>', $vbox_disciplina);
        $this->notebook->appendPage('<h4>Atividades Complementares</h4>', $this->datagrid_atividades);
        $this->notebook->appendPage('<h4>Estágios</h4>', $this->datagrid_estagios);
        $this->notebook->appendPage('<h4>Situações Discente</h4>', $this->datagrid_situacoes);
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->formMestre);
        $container->add($this->notebook);
        
        parent::add($container);
    }
    

    public static function onChangeSituacaoDisciplina($param)
    {
        $situacao = $param['situacao'];

        if($situacao == "Aprovado")
        {
            //HABILITA
            TCombo::enableField('form_HistoricoAutomaticoDisciplinas', 'forma_integralizacao');
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Cursado'] = "Cursado";
            $opcoes['Validado'] = "Validado";
            $opcoes['Aproveitado'] = "Aproveitado";
            
            TCombo::reload('form_HistoricoAutomaticoDisciplinas', 'forma_integralizacao', $opcoes, TRUE);
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_HistoricoAutomaticoDisciplinas', 'forma_integralizacao');
            
            //DESABILITA
            TCombo::disableField('form_HistoricoAutomaticoDisciplinas', 'forma_integralizacao');
        }
    }
    
    
    public function onReloadDisciplinas($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('HistoricoDigitalDisciplinas');
            $limit = 150;

            $dados_historico = TSession::getValue('dados_historico_digital');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('historico_digital_id', '=', $dados_historico->id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'etapa';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid_disciplinas->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid_disciplinas->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigationDisciplinas->setCount($count); 
            $this->pageNavigationDisciplinas->setProperties($param); 
            $this->pageNavigationDisciplinas->setLimit($limit);             
            
            TTransaction::close();
            $this->loadedDisciplinas = true;
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(0);
            
            //Mantém campo pendente desabilitado após recarregar
            $param['situacao'] = '';
            $this->onChangeSituacaoDisciplina($param);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onEditDisciplina( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new HistoricoDigitalDisciplinas($key);
                
                $this->cod_disciplina->setEditable(FALSE);
                 
                $this->formDisciplinas->setData($object); 
                
                $param['situacao'] = $object->situacao;
                $this->onChangeSituacaoDisciplina($param);
                
                TForm::sendData('form_HistoricoAutomaticoDisciplinas', $object);                                             
                
                $this->onReloadDisciplinas();
                $this->onReloadAtividades();
                $this->onReloadEstagios();
                $this->onReloadSituacoes();
                
                
                //Mantém aba atual
                $this->notebook->setCurrentPage(0);
                
                TTransaction::close(); 
            }
            else
            {
                $this->formDisciplinas->clear(TRUE);
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public function displayColumnAtualizar($object)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $historico_digital = new HistoricoDigital($object->historico_digital_id);
            
            $nota_historico_digital = $object->nota;
            $situacao_historico_digital = $object->situacao;
            
            TTransaction::close();


            TTransaction::open('dados_fei');

            $fi_historico_disciplinas = FiHistoricodisciplinas::where('codhistorico', '=', $historico_digital->historico_genesi_id)
                                                              ->where('CodDisciplina', '=', $object->cod_disciplina)
                                                              ->load();
            
            foreach($fi_historico_disciplinas as $fi_historico_disciplina)
            {
                //Formata para o mesmo padrão antes de comparar
                $fi_historico_disciplina->NotaFinal = str_replace(',', '.', $fi_historico_disciplina->NotaFinal);                    
                $fi_historico_disciplina->NotaFinal = floatval($fi_historico_disciplina->NotaFinal);        
                $fi_historico_disciplina->NotaFinal = number_format($fi_historico_disciplina->NotaFinal, 2, '.', '');                                        
                $fi_historico_disciplina->NotaFinal = $fi_historico_disciplina->NotaFinal;
                
                $nota_historico_original = $fi_historico_disciplina->NotaFinal;
                $situacao_historico_original = $fi_historico_disciplina->Sit;                                
            }
            
            TTransaction::close();
            
            //Se o registro do Genesi estiver diferente do registro da datagrid, significa que ele precisa ser atualizado
            if(($nota_historico_digital <> $nota_historico_original) OR
              (($situacao_historico_digital == 'Aprovado') AND ($situacao_historico_original <> 'A' AND $situacao_historico_original <> 'O')) OR
              (($situacao_historico_digital == 'Reprovado') AND ($situacao_historico_original <> 'R')) OR 
              (($situacao_historico_digital == 'Pendente') AND ($situacao_historico_original <> 'E' AND $situacao_historico_original <> 'D' AND (!empty($situacao_historico_original)))))
            {
                return TRUE;                                
            }
            
                return FALSE;                                                                                
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public static function onAtualizarDisciplina($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $historico_digital_disciplina = new HistoricoDigitalDisciplinas($param['id']);  
            $historico_digital = new HistoricoDigital($historico_digital_disciplina->historico_digital_id);
            
            TTransaction::close();


            TTransaction::open('dados_fei');

            $fi_historico_disciplinas = FiHistoricodisciplinas::where('codhistorico', '=', $historico_digital->historico_genesi_id)
                                                              ->where('CodDisciplina', '=', $historico_digital_disciplina->cod_disciplina)
                                                              ->load();          


            foreach($fi_historico_disciplinas as $fi_historico_disciplina)
            {
                $ano_historico_original = $fi_historico_disciplina->Ano;
                $semestre_historico_original = $fi_historico_disciplina->Sem;
                $frequencia_historico_original = $fi_historico_disciplina->Freq;
                
                $fi_historico_disciplina->NotaFinal = str_replace(',', '.', $fi_historico_disciplina->NotaFinal);                    
                $fi_historico_disciplina->NotaFinal = floatval($fi_historico_disciplina->NotaFinal);        
                $fi_historico_disciplina->NotaFinal = number_format($fi_historico_disciplina->NotaFinal, 2, '.', '');                                        
                $fi_historico_disciplina->NotaFinal = $fi_historico_disciplina->NotaFinal;
                
                $nota_historico_original = $fi_historico_disciplina->NotaFinal;
                
                if($fi_historico_disciplina->Sit == 'A' OR $fi_historico_disciplina->Sit == 'O')
                {
                    $situacao_historico_original = "Aprovado";                    
                }
                elseif($fi_historico_disciplina->Sit == "REPROVADO(A)")
                {
                    $situacao_historico_original = "Reprovado";
                }
                else
                {
                    $situacao_historico_original = "Pendente";
                }
            }
            
            TTransaction::close();
            
            
            //Exibe formulário para confirmação das informações
            $form = new BootstrapFormBuilder('atualizacao_disciplina_form');
            $form->setFieldSizes('100%');                
                
            $ano = new TEntry('ano');
            $semestre  = new TEntry('semestre');
            $frequencia = new TEntry('frequencia');
            $nota  = new TEntry('nota');
            $situacao = new TEntry('situacao');
            $forma_integralizacao = new TCombo('forma_integralizacao');
                
                
            //Forma de integralização
            $combo_integralizacao = [];
            $combo_integralizacao['Cursado'] = "Cursado";
            $combo_integralizacao['Validado'] = "Validado";
            $combo_integralizacao['Aproveitado'] = "Aproveitado";
                
            $forma_integralizacao->addItems($combo_integralizacao);
            
    
            $ano->setValue($ano_historico_original);
            $ano->setEditable(FALSE);
            $semestre->setValue($semestre_historico_original);
            $semestre->setEditable(FALSE);
            $frequencia->setValue($frequencia_historico_original);
            $frequencia->setEditable(FALSE);
            $nota->setValue($nota_historico_original);
            $nota->setEditable(FALSE);
            $situacao->setValue($situacao_historico_original);
            $situacao->setEditable(FALSE);
                                                  
                
            $row = $form->addFields( [ new TLabel('Ano'), $ano ],
                                     [ new TLabel('Semestre'), $semestre ],
                                     [ new TLabel('Frequência'), $frequencia ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
             
            $row = $form->addFields( [ new TLabel('Nota'), $nota ],
                                     [ new TLabel('Situação'), $situacao ],
                                     [ new TLabel('Forma de integralização'), $forma_integralizacao ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
                
            $form->addAction('Confirmar', new TAction([__CLASS__, 'onConfirmaAtualizacaoNotaSituacao'], ['id' => $historico_digital_disciplina->id]), 'fa:save green');
                
            new TInputDialog('Confirmar atualização', $form);    

            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public function onConfirmaAtualizacaoNotaSituacao( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $historico_digital_disciplinas = HistoricoDigitalDisciplinas::find($param['id']);
            
            if ($historico_digital_disciplinas) 
            { 
                $historico_digital_disciplinas->ano = $param['ano'];
                $historico_digital_disciplinas->semestre = $param['semestre'];
                $historico_digital_disciplinas->frequencia = $param['frequencia']; 
                $historico_digital_disciplinas->nota = $param['nota']; 
                $historico_digital_disciplinas->situacao = $param['situacao']; 
                $historico_digital_disciplinas->forma_integralizacao = $param['forma_integralizacao'];
                
                if(($historico_digital_disciplinas->situacao == 'Aprovado') AND (empty($historico_digital_disciplinas->forma_integralizacao)))
                {                
                    $valida_integralizacao = new TRequiredValidator;
                    $valida_integralizacao->validate('Forma de integralização', $param['forma_integralizacao']);  
                    
                }

                $historico_digital_disciplinas->system_user_id = TSession::getValue('userid'); 
                $historico_digital_disciplinas->data_reg = date('Y-m-d H:i:s');
                $historico_digital_disciplinas->store(); 
                
                TTransaction::close();
                
                new TMessage('info', 'Registro atualizado com sucesso');
                           
                $this->onReloadDisciplinas(); 
                $this->onReloadAtividades();
                $this->onReloadEstagios();
                $this->onReloadSituacoes();
                        
                //Mantém aba atual
                $this->notebook->setCurrentPage(0); 
            }             
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
            
            $this->onReloadDisciplinas(); 
            $this->onReloadAtividades();
            $this->onReloadEstagios();
            $this->onReloadSituacoes();
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(0);
        }
    }
    
    //Se o usuário logado é do grupo Admin, exibe opção
    public function displayImportarTudo($object)
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');

        if (in_array($grupo_admin, (array) $user_groups))
        {
            return TRUE;
        }

        return FALSE;
    }

    //Se o usuário logado é do grupo Admin, exibe opção
    public function displayColumnDelete( $object )
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');
                
        if(( in_array($grupo_admin, $user_groups)))
        {
            return TRUE;
        }
            return FALSE;
    }
    
    
    public static function onDeleteDisciplina($param)
    {
        $action = new TAction([__CLASS__, 'DeleteDisciplina']);
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function DeleteDisciplina($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new HistoricoDigitalDisciplinas($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            
            //Recarrega todas as datagrids e passa como parâmetro a aba de onde partiu a chamada de função
            $pos_action = new TAction([__CLASS__, 'onShow']);
            $pos_action->setParameter('aba', 0); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public function onSaveDisciplina( $param )
    {
        try
        {
            $dados_historico = TSession::getValue('dados_historico_digital');

            TTransaction::open('Felabs_DB');                    
                        
            $this->formDisciplinas->validate();
            $data = $this->formDisciplinas->getData(); 
            
            $object = new HistoricoDigitalDisciplinas;  
            $object->fromArray( (array) $data);
            
            $object->historico_digital_id = $dados_historico->id;           
            $object->system_user_id = TSession::getValue('userid'); 
            $object->data_reg = date('Y-m-d H:i:s');
            
 
            //1º - Verifica se os campos estão iguais aos do histórico no Genesi para garantir integridade do registro            
            TTransaction::open('dados_fei');
            
            $fi_historico = new FiHistorico($dados_historico->historico_genesi_id);
            
            $count_vw_historico_disciplinas = VwHistoricodisciplina::where('codhistorico', '=', $fi_historico->codhistorico)
                                                                   ->where('CodDisciplina', '=', $object->cod_disciplina)
                                                                   ->count();
            
            $fi_historico_disciplinas = FiHistoricodisciplinas::where('codhistorico', '=', $fi_historico->codhistorico)
                                                              ->where('CodDisciplina', '=', $object->cod_disciplina)
                                                              ->load();
                                              
            if($fi_historico_disciplinas)
            {
                foreach($fi_historico_disciplinas as $fi_historico_disciplina)
                {
                    //Nome da disciplina
                    if($object->nome_disciplina <> $fi_historico_disciplina->Disciplina)
                    {
                        throw new Exception('O nome da disciplina não corresponde ao lançado no histórico original');    
                    }


                    //Etapa
                    if($object->etapa <> $fi_historico_disciplina->Etapa)
                    {
                        throw new Exception('A etapa em que a disciplina foi integralizada não corresponde à lançada no histórico original');
                    }
                    
                    
                    //Ano
                    if($object->ano <> $fi_historico_disciplina->Ano)
                    {
                        throw new Exception('O ano em que a disciplina foi integralizada não corresponde ao lançado no histórico original');
                    }
                    
                    
                    //Semestre
                    if($object->semestre <> $fi_historico_disciplina->Sem)
                    {
                        throw new Exception('O semestre em que a disciplina foi integralizada não corresponde ao lançado no histórico original');
                    }
                    
                    
                    //Nota (formata a nota lançada no Genesi para o mesmo padrão antes de comparar)
                    $fi_historico_disciplina->NotaFinal = str_replace(',', '.', $fi_historico_disciplina->NotaFinal);                    
                    $fi_historico_disciplina->NotaFinal = floatval($fi_historico_disciplina->NotaFinal);        
                    $fi_historico_disciplina->NotaFinal = number_format($fi_historico_disciplina->NotaFinal, 2, '.', '');                                        
                    $fi_historico_disciplina->NotaFinal = $fi_historico_disciplina->NotaFinal;
                                        
                    if($object->nota <> $fi_historico_disciplina->NotaFinal)
                    {
                        throw new Exception('A nota lançada para esta disciplina não corresponde à lançada no histórico original');
                    }
                    
                    
                    //Carga horária (formata a carga horária lançada no Genesi para o mesmo padrão antes de comparar)
                    if($fi_historico_disciplina->CHParcial)
                    {
                        $fi_historico_disciplina->CHParcial = str_replace(',', '.', $fi_historico_disciplina->CHParcial);
                        $fi_historico_disciplina->CHParcial = floatval($fi_historico_disciplina->CHParcial);                             
                        $fi_historico_disciplina->CHParcial = number_format($fi_historico_disciplina->CHParcial, 2, '.', '');
                        $fi_historico_disciplina->CHParcial = $fi_historico_disciplina->CHParcial; 
                    
                        $carga_horaria_historico_original = $fi_historico_disciplina->CHParcial;
                                                
                        if($object->carga_horaria <> $carga_horaria_historico_original)
                        {
                            throw new Exception('A carga horária lançada para esta disciplina não corresponde à lançada no histórico original');
                        }
                    }
                    else
                    {
                        $fi_historico_disciplina->CH = str_replace(',', '.', $fi_historico_disciplina->CH);
                        $fi_historico_disciplina->CH = floatval($fi_historico_disciplina->CH);                             
                        $fi_historico_disciplina->CH = number_format($fi_historico_disciplina->CH, 2, '.', '');
                        $fi_historico_disciplina->CH = $fi_historico_disciplina->CH; 
                        
                        $carga_horaria_historico_original = $fi_historico_disciplina->CH;
                        
                        if($count_vw_historico_disciplinas == 1)
                        {
                            if($object->carga_horaria <> $carga_horaria_historico_original)
                            {
                                throw new Exception('A carga horária lançada para esta disciplina não corresponde à lançada no histórico original');
                            }
                        }
                        else
                        {
                            //Se houver divisão de frente na disciplina, compara a ch com a do Genesi/nº de vezes que a disciplina aparece. Se não, compara a ch com o valor total
                            $carga_horaria_disciplina = $carga_horaria_historico_original/$count_vw_historico_disciplinas;
                            
                            if($object->carga_horaria <> $carga_horaria_disciplina)
                            {
                                throw new Exception('A carga horária lançada para esta disciplina não corresponde à lançada no histórico original');
                            }
                        }
                    }
                    

                    //Frequência
                    if($object->frequencia <> $fi_historico_disciplina->Freq)
                    {
                        throw new Exception('A frequência lançada para esta disciplina não corresponde à lançada no histórico original');
                    }
                    
                    
                    //Situação
                    if(($object->situacao == 'Aprovado') AND ($fi_historico_disciplina->Sit <> 'A' AND $fi_historico_disciplina->Sit <> 'O'))
                    {
                        throw new Exception('A situação lançada para esta disciplina não corresponde à lançada no histórico original');
                    }
                    if(($object->situacao == 'Reprovado') AND ($fi_historico_disciplina->Sit <> 'R'))
                    {
                        throw new Exception('A situação lançada para esta disciplina não corresponde à lançada no histórico original');
                    }
                    if(($object->situacao == 'Pendente') AND ($fi_historico_disciplina->Sit <> "E" AND $fi_historico_disciplina->Sit <> "D" AND (!empty($fi_historico_disciplina->Sit))))
                    {
                        throw new Exception('A situação lançada para esta disciplina não corresponde à lançada no histórico original');
                    }
                }
            }
            else
            {
                throw new Exception('Verifique se esta disciplina também existe no histórico original');
            }                                                    
            
            TTransaction::close();
            
            
            //2º - Verifica se disciplina já não foi adicionada ao histórico
            if(empty($data->id))
            {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('historico_digital_id', '=', $object->historico_digital_id)); 
                $criteria->add(new TFilter('cod_disciplina', '=', $data->cod_disciplina)); 

                $repository = new TRepository('HistoricoDigitalDisciplinas'); 
                $count_historico_disciplinas = $repository->count($criteria);
            
                if ($count_historico_disciplinas >= $count_vw_historico_disciplinas)
                {
                    throw new Exception("Esta disciplina já foi adicionada ao histórico");
                }
            }
            
            
            //Controle de campos condicionais - Forma de integralização
            if($object->situacao == 'Aprovado')
            {
                if(! $object->forma_integralizacao)
                {
                    throw new Exception('É necessário preencher a forma de integralização');
                }
            }
            
            
            //Controle de campos condicionais - Professor
            if($object->cod_professor)
            {
                if((! $object->nome_professor) OR (! $object->titulacao_professor)) 
                {
                    throw new Exception('É necessário preencher os dados do professor');
                }
            }
            
            if($object->nome_professor)
            {
                if(! $object->titulacao_professor) 
                {
                    throw new Exception('É necessário preencher a titulação do professor');
                }
            }

            if($object->titulacao_professor)
            {
                if(! $object->nome_professor) 
                {
                    throw new Exception('É necessário preencher o nome do professor');
                }
            }

            $object->store(); 

            TTransaction::close();
            
            
            //Faz correspondência para salvar campos alterados no Genesi
            TTransaction::open('dados_fei');
                
            $historico_genesi = FiHistoricoDisciplinas::find($fi_historico_disciplina->CodHistoricoDisciplinas);

            if($historico_genesi)
            {  
                $historico_genesi->CHParcial = $object->carga_horaria;
                
                //Só salvam as informações referentes a professor se a disciplina foi editada
                if($historico_genesi->Edita == "S")
                {      
                    $historico_genesi->NomeProf = mb_strtoupper($object->nome_professor);
                    $historico_genesi->TituloProf = mb_strtoupper($object->titulacao_professor);
                }
                
                $historico_genesi->store();
            }
                        
            TTransaction::close();  
            
            $data->id = $object->id;
            
            $this->formDisciplinas->setData($data);              
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); 
            
            //Limpa o formulário depois de salvar
            $this->formDisciplinas->clear();
                        
                        
            $this->onReloadDisciplinas(); 
            $this->onReloadAtividades();
            $this->onReloadEstagios();
            $this->onReloadSituacoes();
            
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(0);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            
            //Se estiver editando registro e cair na exceção, mantém campos bloqueados. Se estiver salvando novo registro, mantém desbloqueado
            if(!empty($param['id']))
            {
                $this->cod_disciplina->setEditable(FALSE);
            }
                        
            $this->onReloadDisciplinas(); 
            $this->onReloadAtividades();
            $this->onReloadEstagios();
            $this->onReloadSituacoes();
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(0);
            
            //Habilita ou desabilita campo dependente dependendo do que foi selecionado
            $param['situacao'] = $object->situacao;
            $this->onChangeSituacaoDisciplina($param);
                
            $this->formDisciplinas->setData( $this->formDisciplinas->getData() );             
            TTransaction::rollback();    
        }
    }
    

    public function onReloadAtividades($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $dados_historico_genesi = TSession::getValue('dados_historico_genesi');
            
            $repository = new TRepository('AtividadeComplementar');
            $limit = 150;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $dados_historico_genesi->Codaluno));
            $criteria->add(new TFilter('cod_curso', '=', $dados_historico_genesi->CodCurso));
            $criteria->add(new TFilter('status_atividade', '=', "Aprovado"));
            
            if (empty($param['order']))
            {
                $param['order'] = 'etapa';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);          

            $objects = $repository->load($criteria, FALSE);

            $this->datagrid_atividades->clear();
            $this->datagrid_atividades->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->data_inicio = TDate::date2br($object->data_inicio);
                    $object->data_termino = TDate::date2br($object->data_termino);
                    $object->carga_horaria = substr($object->carga_horaria,0,-3);
                    
                    if($object->status_atividade == "Aprovado")
                    {
                        $object->status_atividade = '<span class="label label-success">' . $object->status_atividade . '</span>';
                    }
                    else
                    {
                        $object->status_atividade = $object->status_atividade;
                    }   
                    
                    $this->datagrid_atividades->addItem($object);
                }
            }            

            TTransaction::close();
            $this->loadedAtividades = true;
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(1);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onReloadEstagios($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $dados_historico_genesi = TSession::getValue('dados_historico_genesi');
            
            $repository = new TRepository('Estagio');
            $limit = 150;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $dados_historico_genesi->Codaluno));
            $criteria->add(new TFilter('cod_curso', '=', $dados_historico_genesi->CodCurso));
            $criteria->add(new TFilter('status_estagio', '=', "Aprovado"));
            
            if (empty($param['order']))
            {
                $param['order'] = 'etapa';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);          

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid_estagios->clear();
            $this->datagrid_estagios->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->data_inicio = TDate::date2br($object->data_inicio);
                    $object->data_termino = TDate::date2br($object->data_termino);
                    $object->carga_horaria = substr($object->carga_horaria,0,-3);
                    
                    if($object->status_estagio == "Aprovado")
                    {
                        $object->status_estagio = '<span class="label label-success">' . $object->status_estagio . '</span>';
                    }
                    else
                    {
                        $object->status_estagio = $object->status_estagio;
                    }   
                    
                    $this->datagrid_estagios->addItem($object);
                }
            }            

            TTransaction::close();
            $this->loadedEstagios = true;
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(2);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
  
    public function onReloadSituacoes($param = NULL)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $dados_historico_genesi = TSession::getValue('dados_historico_genesi');            
            
            $repository = new TRepository('VwAlunoMatriculaEtapa');
            $limit = 150;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('Codaluno', '=', $dados_historico_genesi->Codaluno));
            $criteria->add(new TFilter('CodCurso', '=', $dados_historico_genesi->CodCurso));

            
            if (empty($param['order']))
            {
                $param['order'] = 'AnoMatricula, SemestreMatricula, EtapaMatricula';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);          

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid_situacoes->clear();
            $this->datagrid_situacoes->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->tipo_entrada = "Situação Discente";
                    
                    if($object->SituacaoMatricula == "FR")
                    {
                        $object->SituacaoMatricula = "Frequente";
                        $object->situacao_discente = "Matriculado em disciplina";
                    }
                    elseif($object->SituacaoMatricula == "DS")
                    {
                        $object->SituacaoMatricula = "Desistente";
                        $object->situacao_discente = "Desistência";
                    }
                    elseif($object->SituacaoMatricula == "TE")
                    {
                        $object->SituacaoMatricula = "Trsf. Expedida";
                        $object->situacao_discente = "Outra situação";
                    }
                    elseif($object->SituacaoMatricula == "AB")
                    {
                        $object->SituacaoMatricula = "Abandono";
                        $object->situacao_discente = "Abandono";
                    }
                    elseif($object->SituacaoMatricula == "CL")
                    {
                        $object->SituacaoMatricula = "Concluída";
                        $object->situacao_discente = "Formado";
                    }
                    elseif($object->SituacaoMatricula == "TR")
                    {
                        $object->SituacaoMatricula = "Trancada";
                        $object->situacao_discente = "Trancamento";
                    }
                    elseif($object->SituacaoMatricula == "RC")
                    {
                        $object->SituacaoMatricula = "Reclassificado";
                        $object->situacao_discente = "Outra situação";
                    }
                    elseif($object->SituacaoMatricula == "RM")
                    {
                        $object->SituacaoMatricula = "Remanejado";
                        $object->situacao_discente = "Outra situação";
                    }
                    else
                    {
                        $object->SituacaoMatricula = $object->SituacaoMatricula;
                        $object->situacao_discente = "";
                    }
                    
                    $this->datagrid_situacoes->addItem($object);
                }
            }            

            TTransaction::close();
            $this->loadedSituacoes = true;
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(3);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
         
    public function setNomeResponsavel($column_nome_professor, $object, $row)
    {
        try
        {
            $cod_professor = $object->cod_prof_responsavel;            
            
            TTransaction::open('dados_fei');           
            
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
        
    
    public function onClear( $param )
    {
        $this->formDisciplinas->clear(TRUE);
        
        $this->onReloadDisciplinas();
        $this->onReloadAtividades();
        $this->onReloadEstagios();
        $this->onReloadSituacoes();   
                
        //Mantém aba atual
        $this->notebook->setCurrentPage(0);
    }
    
    
    public function onShow( $param )
    {   
        $this->onReloadDisciplinas();
        $this->onReloadAtividades();
        $this->onReloadEstagios();
        $this->onReloadSituacoes();
        $this->onChangeSituacaoDisciplina($param);
        
        $aba = $param['aba'];
        
        //Se executou ação de outras abas, permanece na aba que chamou a função, caso contrário, a aba padrão é a primeira
        if($aba <> 0)
        {            
            $this->notebook->setCurrentPage($aba);
        }
        else
        {
            $this->notebook->setCurrentPage(0);
        }    
    }
    

    public function show()
    {
        parent::show();
    }
}

