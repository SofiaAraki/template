<?php

class HistoricoManualComponentesForm extends TPage
{
    protected $formDisciplinas; 
    protected $datagrid_disciplinas; 
    protected $pageNavigationDisciplinas;
    protected $loadedDisciplinas;
    
    private $formSituacoes; 
    private $datagrid_situacoes; 
    private $pageNavigationSituacoes;   
    private $loadedSituacoes;
    
    //Só para exibição, não são manipulados
    private $formMestre;
    private $notebook;
    private $datagrid_atividades; 
    private $loadedAtividades;
    private $datagrid_estagios;
    private $loadedEstagios;
    
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
        $this->formMestre = new BootstrapFormBuilder('form_mestre_HistoricoManual');
        $this->formMestre->setFormTitle('<h4>Componentes Curriculares - Histórico Digital</h4>');

        $this->formMestre->addFields( [new TLabel('Aluno:')], [$nome_aluno] );
        $this->formMestre->addFields( [new TLabel('Curso:')], [$nome_curso] );
        
        
        //ABA 1 - DISCIPLINAS
        
        $this->formDisciplinas = new BootstrapFormBuilder('form_HistoricoManualDisciplinas');
        $this->formDisciplinas->setFormTitle('');
        $this->formDisciplinas->setFieldSizes('100%');               


        // create the form fields
        $id = new THidden('id');
        $historico_digital_id = new THidden('historico_digital_id');
        $ano = new TEntry('ano');
        $semestre = new TCombo('semestre');
        $etapa = new TCombo('etapa');
        $tipo_entrada = new TCombo('tipo_entrada');
        $this->cod_disciplina = new TDBUniqueSearch('cod_disciplina', 'dados_fei', 'FiDisciplina', 'CodDisciplina', 'Nomeusual');
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


        $this->cod_disciplina->setChangeAction(new TAction(array($this, 'onChangeDisciplina')));
        
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
                                                  [ new TLabel('Nome disciplina no histórico'), $nome_disciplina ] );
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-5'];
        
        $row = $this->formDisciplinas->addFields( [ new TLabel('Cód. Professor'), $cod_professor ],
                                                  [ new TLabel('Professor'), $nome_professor ],
                                                  [ new TLabel('Titulação'), $titulacao_professor ]);
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-5'];
        
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
        $this->cod_disciplina->setMask('(Cód. <b>{CodDisciplina}</b>) <b>{Nomeusual}</b>');
        $nome_disciplina->setEditable(FALSE);
        $ano->setMask('9999');        
        $cod_professor->setMask('9!');
        $nome_professor->forceUpperCase();


        // create the form actions
        $btn_save_disciplina = $this->formDisciplinas->addAction(_t('Save'), new TAction([$this, 'onSaveDisciplina']), 'fa:save');
        $btn_save_disciplina->class = 'btn btn-sm btn-primary';
        $this->formDisciplinas->addAction('Limpar campos', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->formDisciplinas->addAction('Voltar', new TAction(array('HistoricoManualList','onReload')), 'fas:arrow-alt-circle-left blue');        

        
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
        $column_nome_disciplina = new TDataGridColumn('nome_disciplina', 'Disciplina', 'left');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'CH', 'center');
        //$column_frequencia = new TDataGridColumn('frequencia', 'Frequência', 'center');   
        $column_nota = new TDataGridColumn('nota', 'Nota', 'center');             
        $column_situacao = new TDataGridColumn('situacao', 'Situação', 'center');
        $column_forma_integralizacao = new TDataGridColumn('forma_integralizacao', 'Forma de integralização', 'center');
        //$column_cod_professor = new TDataGridColumn('cod_professor', 'Cód. professor', 'center');
        $column_nome_professor = new TDataGridColumn('nome_professor', 'Professor', 'left');
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
        //$this->datagrid_disciplinas->addColumn($column_cod_disciplina);
        $this->datagrid_disciplinas->addColumn($column_nome_disciplina);
        $this->datagrid_disciplinas->addColumn($column_carga_horaria);
        //$this->datagrid_disciplinas->addColumn($column_frequencia); 
        $this->datagrid_disciplinas->addColumn($column_nota);               
        $this->datagrid_disciplinas->addColumn($column_situacao);
        $this->datagrid_disciplinas->addColumn($column_forma_integralizacao);
        //$this->datagrid_disciplinas->addColumn($column_cod_professor)->setVisibility(false);
        $this->datagrid_disciplinas->addColumn($column_nome_professor);
        $this->datagrid_disciplinas->addColumn($column_titulacao_professor);

        
        // creates two datagrid actions
        $action_edit_disciplina = new TDataGridAction([$this, 'onEditDisciplina']);
        //$action_edit_disciplina->setUseButton(TRUE);
        //$action_edit_disciplina->setButtonClass('btn btn-default');
        $action_edit_disciplina->setLabel(_t('Edit'));
        $action_edit_disciplina->setImage('far:edit blue');
        $action_edit_disciplina->setField('id');
        
        
        $action_del_disciplina = new TDataGridAction([$this, 'onDeleteDisciplina']);
        //$action_del_disciplina->setUseButton(TRUE);
        //$action_del_disciplina->setButtonClass('btn btn-default');
        $action_del_disciplina->setLabel(_t('Delete'));
        $action_del_disciplina->setImage('far:trash-alt red');
        $action_del_disciplina->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid_disciplinas->addAction($action_edit_disciplina);
        $this->datagrid_disciplinas->addAction($action_del_disciplina);
        
        
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
        
        $this->formSituacoes = new BootstrapFormBuilder('form_HistoricoManualSituacaoDiscente');
        $this->formSituacoes->setFormTitle('');
        $this->formSituacoes->setFieldSizes('100%');   
        

        // create the form fields
        $id = new THidden('id');
        $historico_digital_id = new THidden('historico_digital_id');
        $tipo_entrada = new TEntry('tipo_entrada');
        $situacao_discente = new TCombo('situacao_discente');
        $situacao_ano = new TEntry('situacao_ano');
        $situacao_semestre = new TCombo('situacao_semestre');
        $situacao_etapa = new TCombo('situacao_etapa');
        $situacao_intercambio_instituicao = new TEntry('situacao_intercambio_instituicao');
        $situacao_intercambio_pais = new TEntry('situacao_intercambio_pais');
        $situacao_intercambio_programa = new TEntry('situacao_intercambio_programa');
        $situacao_outra = new TEntry('situacao_outra');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        //Semestre
        $combo_semestre = [];
        $combo_semestre['1'] = "1º";
        $combo_semestre['2'] = "2º";
        
        $situacao_semestre->addItems($combo_semestre);
        

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
        
        $situacao_etapa->addItems($combo_etapa);
        

        //Situação atual do discente (definidas pelo MEC)
        $combo_situacao = [];
        $combo_situacao['Trancamento'] = "Trancamento";
        $combo_situacao['MatriculadoEmDisciplina'] = "Matriculado em disciplina";
        $combo_situacao['Licenca'] = "Licença";
        $combo_situacao['IntercambioInternacional'] = "Intercâmbio internacional";
        $combo_situacao['IntercambioNacional'] = "Intercâmbio nacional";
        $combo_situacao['Desistencia'] = "Desistência";
        $combo_situacao['Abandono'] = "Abandono";
        $combo_situacao['Jubilado'] = "Jubilado";
        $combo_situacao['Formado'] = "Formado";
        $combo_situacao['OutraSituacao'] = "Outra";
        
        $situacao_discente->addItems($combo_situacao);
        
        $situacao_discente->setChangeAction(new TAction(array($this, 'onChangeSituacaoDiscente')));


        // add the fields
        $this->formSituacoes->addFields( [ $id ] );
        $this->formSituacoes->addFields( [ $historico_digital_id ] );
        $this->formSituacoes->addFields( [ $system_user_id ] );
        $this->formSituacoes->addFields( [ $data_reg ] );
        
        $row = $this->formSituacoes->addFields( [ new TLabel('Tipo de entrada'), $tipo_entrada ],
                                                [ new TLabel('Situação'), $situacao_discente ],
                                                [ new TLabel('Ano'), $situacao_ano ],
                                                [ new TLabel('Semestre'), $situacao_semestre ],
                                                [ new TLabel('Etapa'), $situacao_etapa ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-2'];
        
        $row = $this->formSituacoes->addFields( [ new TLabel('Intercâmbio (Instituição)'), $situacao_intercambio_instituicao ],
                                                [ new TLabel('Intercâmbio (País)'), $situacao_intercambio_pais ],
                                                [ new TLabel('Intercâmbio (Programa)'), $situacao_intercambio_programa ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $row = $this->formSituacoes->addFields( [ new TLabel('Outra situação'), $situacao_outra ] );
        $row->layout = ['col-sm-12'];
        

        $tipo_entrada->addValidation('Tipo de entrada', new TRequiredValidator);
        $situacao_discente->addValidation('Situação', new TRequiredValidator);
        $situacao_ano->addValidation('Ano', new TRequiredValidator);
        $situacao_semestre->addValidation('Semestre', new TRequiredValidator);
        $situacao_etapa->addValidation('Etapa', new TRequiredValidator);


        // set sizes
        $situacao_ano->setMask('9999');
        $tipo_entrada->setValue('Situação Discente');
        $tipo_entrada->setEditable(FALSE);
        

        // create the form actions
        $btn_save_situacao = $this->formSituacoes->addAction(_t('Save'), new TAction([$this, 'onSaveSituacao']), 'fa:save');
        $btn_save_situacao->class = 'btn btn-sm btn-primary';
        
        
        // creates a Datagrid
        $this->datagrid_situacoes = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_situacoes->style = 'width: 100%';
        $this->datagrid_situacoes->datatable = 'true';
        $this->datagrid_situacoes->disableDefaultClick();
        

        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'ID', 'center');
        //$column_historico_digital_id = new TDataGridColumn('historico_digital_id', 'Histórico Digital ID', 'center');
        $column_tipo_entrada = new TDataGridColumn('tipo_entrada', 'Tipo', 'center');
        $column_situacao_discente = new TDataGridColumn('situacao_discente', 'Situação', 'left', 50);
        $column_situacao_ano = new TDataGridColumn('situacao_ano', 'Ano', 'center');
        $column_situacao_semestre = new TDataGridColumn('situacao_semestre', 'Sem', 'center');
        $column_situacao_etapa = new TDataGridColumn('situacao_etapa', 'Etapa', 'center');
        $column_situacao_intercambio_instituicao = new TDataGridColumn('situacao_intercambio_instituicao', 'Intercâmbio (Instituição)', 'center');
        $column_situacao_intercambio_pais = new TDataGridColumn('situacao_intercambio_pais', 'Intercâmbio (País)', 'center');
        $column_situacao_intercambio_programa = new TDataGridColumn('situacao_intercambio_programa', 'Intercâmbio (Programa)', 'center');
        $column_situacao_outra = new TDataGridColumn('situacao_outra', 'Outra situação', 'center');


        // add the columns to the DataGrid
        //$this->datagrid_situacoes->addColumn($column_id)->setVisibility(false);
        //$this->datagrid_situacoes->addColumn($column_historico_digital_id)->setVisibility(false);
        $this->datagrid_situacoes->addColumn($column_tipo_entrada);
        $this->datagrid_situacoes->addColumn($column_situacao_etapa);
        $this->datagrid_situacoes->addColumn($column_situacao_ano);
        $this->datagrid_situacoes->addColumn($column_situacao_semestre);
        $this->datagrid_situacoes->addColumn($column_situacao_discente);
        $this->datagrid_situacoes->addColumn($column_situacao_intercambio_instituicao);
        $this->datagrid_situacoes->addColumn($column_situacao_intercambio_pais);
        $this->datagrid_situacoes->addColumn($column_situacao_intercambio_programa);
        $this->datagrid_situacoes->addColumn($column_situacao_outra);
        
        
        // creates two datagrid actions
        $action_edit_situacao = new TDataGridAction([$this, 'onEditSituacao']);
        //$action_edit_situacao->setUseButton(TRUE);
        //$action_edit_situacao->setButtonClass('btn btn-default');
        $action_edit_situacao->setLabel(_t('Edit'));
        $action_edit_situacao->setImage('far:edit blue');
        $action_edit_situacao->setField('id');
        
        
        $action_del_situacao = new TDataGridAction([$this, 'onDeleteSituacao']);
        //$action_del_situacao->setUseButton(TRUE);
        //$action_del_situacao->setButtonClass('btn btn-default');
        $action_del_situacao->setLabel(_t('Delete'));
        $action_del_situacao->setImage('far:trash-alt red');
        $action_del_situacao->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid_situacoes->addAction($action_edit_situacao);
        $this->datagrid_situacoes->addAction($action_del_situacao);
        
        
        // create the datagrid model
        $this->datagrid_situacoes->createModel();


        // creates the page navigation
        $this->pageNavigationSituacoes = new TPageNavigation;
        $this->pageNavigationSituacoes->setAction(new TAction([$this, 'onReloadSituacoes']));
        $this->pageNavigationSituacoes->setWidth($this->datagrid_situacoes->getWidth());
        
        
        //Acrescenta formulário e datagrid situações em um container
        $vbox_situacao = new TVBox;
        $vbox_situacao->style = 'width: 100%';
        $vbox_situacao->add($this->formSituacoes);
        $vbox_situacao->add(TPanelGroup::pack('', $this->datagrid_situacoes, $this->pageNavigationSituacoes));
        
        
        //-------------------------------------//------------------------------------- //-------------------------------------//
        
        
        //Acrescenta abas ao notebook
        $this->notebook = new TNotebook('notebook_componentes');        
        $this->notebook->appendPage('<h4>Disciplinas</h4>', $vbox_disciplina);
        $this->notebook->appendPage('<h4>Atividades Complementares</h4>', $this->datagrid_atividades);
        $this->notebook->appendPage('<h4>Estágios</h4>', $this->datagrid_estagios);
        $this->notebook->appendPage('<h4>Situações Discente</h4>', $vbox_situacao);
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->formMestre);
        $container->add($this->notebook);
        
        parent::add($container);
    }
    
    
    public static function onChangeDisciplina($param)
    {
        $cod_disciplina = $param['cod_disciplina'];
        
        try
        {
            TTransaction::open('dados_fei');
            
            $disciplina = new FiDisciplina($cod_disciplina);

            $obj = new StdClass;
            $obj->nome_disciplina = $disciplina->Nomeusual;
            
            TForm::sendData('form_HistoricoManualDisciplinas',$obj);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }   
    } 
    
    
    public static function onChangeSituacaoDisciplina($param)
    {
        $situacao = $param['situacao'];

        if($situacao == "Aprovado")
        {
            //HABILITA
            TCombo::enableField('form_HistoricoManualDisciplinas', 'forma_integralizacao');
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Cursado'] = "Cursado";
            $opcoes['Validado'] = "Validado";
            $opcoes['Aproveitado'] = "Aproveitado";
            
            TCombo::reload('form_HistoricoManualDisciplinas', 'forma_integralizacao', $opcoes, TRUE);
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_HistoricoManualDisciplinas', 'forma_integralizacao');
            
            //DESABILITA
            TCombo::disableField('form_HistoricoManualDisciplinas', 'forma_integralizacao');
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
                
                TForm::sendData('form_HistoricoManualDisciplinas', $object);                                             
                
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
            
            
            //Controle de campos condicionais - Forma de integralização
            if($object->situacao == 'Aprovado')
            {
                if(! $object->forma_integralizacao)
                {
                    throw new Exception('É necessário preencher a forma de integralização');
                }
            }

                           
            $object->historico_digital_id = $dados_historico->id;           
            $object->system_user_id = TSession::getValue('userid'); 
            $object->data_reg = date('Y-m-d H:i:s');

            $object->store(); 

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
            
            $dados_historico = TSession::getValue('dados_historico_digital');
            
            $repository = new TRepository('AtividadeComplementar');
            $limit = 150;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $dados_historico->cod_aluno));
            $criteria->add(new TFilter('cod_curso', '=', $dados_historico->cod_curso));
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
            
            $dados_historico = TSession::getValue('dados_historico_digital');
            
            $repository = new TRepository('Estagio');
            $limit = 150;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $dados_historico->cod_aluno));
            $criteria->add(new TFilter('cod_curso', '=', $dados_historico->cod_curso));
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
    
    
    public static function onChangeSituacaoDiscente($param)
    {
        $opcao_situacao = $param['situacao_discente'];

        if($opcao_situacao == "IntercambioInternacional" OR $opcao_situacao == "IntercambioNacional")
        {    
            //LIMPA
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_outra');
            
            //DESABILITA
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_outra');
                  
            //HABILITA
            TEntry::enableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_instituicao');     
            TEntry::enableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_pais');
            TEntry::enableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_programa');       
        }
        elseif($opcao_situacao == "OutraSituacao")
        {
            //LIMPA
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_instituicao');
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_pais');
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_programa');
            
            //DESABILITA
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_instituicao');
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_pais');
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_programa');
            
            //HABILITA
            TEntry::enableField('form_HistoricoManualSituacaoDiscente', 'situacao_outra'); 
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_instituicao');
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_pais');
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_programa');
            TEntry::clearField('form_HistoricoManualSituacaoDiscente', 'situacao_outra'); 
            
            //DESABILITA
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_instituicao');
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_pais');
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_intercambio_programa');
            TEntry::disableField('form_HistoricoManualSituacaoDiscente', 'situacao_outra');
        }
    }
    
    
    public function onReloadSituacoes($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('HistoricoDigitalSituacaoDiscente');
            $limit = 150;

            $dados_historico = TSession::getValue('dados_historico_digital');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('historico_digital_id', '=', $dados_historico->id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'situacao_etapa';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid_situacoes->clear();
            

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    //Exibe a situação com formatação
                    if($object->situacao_discente == "MatriculadoEmDisciplina")
                    {
                        $object->situacao_discente = "Matriculado em disciplina";
                    }
                    elseif($object->situacao_discente == "Licenca")
                    {
                        $object->situacao_discente = "Licença";
                    }                    
                    elseif($object->situacao_discente == "IntercambioInternacional")
                    {
                        $object->situacao_discente = "Intercâmbio internacional";
                    }                    
                    elseif($object->situacao_discente == "IntercambioNacional")
                    {
                        $object->situacao_discente = "Intercâmbio nacional";
                    }
                    elseif($object->situacao_discente == "Desistencia")
                    {
                        $object->situacao_discente = "Desistência";
                    }
                    elseif($object->situacao_discente == "OutraSituacao")
                    {
                        $object->situacao_discente = "Outra situação";
                    }
                    else
                    {
                        $object->situacao_discente = $object->situacao_discente;
                    }
                     
                    $this->datagrid_situacoes->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigationSituacoes->setCount($count); 
            $this->pageNavigationSituacoes->setProperties($param); 
            $this->pageNavigationSituacoes->setLimit($limit); 
            
            
            TTransaction::close();
            $this->loadedSituacoes = true;
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(3);
            
            //Mantém campo pendente desabilitado após recarregar
            $param['situacao_discente'] = '';
            $this->onChangeSituacaoDiscente($param);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onEditSituacao( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  

                TTransaction::open('Felabs_DB'); 
                
                $object = new HistoricoDigitalSituacaoDiscente($key); 
                $this->formSituacoes->setData($object); 
                
                $param['situacao_discente'] = $object->situacao_discente;
                $this->onChangeSituacaoDiscente($param);
                
                TForm::sendData('form_HistoricoManualSituacaoDiscente', $object);
                                                            
                $this->onReloadDisciplinas();
                $this->onReloadAtividades();
                $this->onReloadEstagios();
                $this->onReloadSituacoes();
        
        
                //Mantém aba atual
                $this->notebook->setCurrentPage(3);
                
                TTransaction::close(); 
            }
            else
            {
                $this->formSituacoes->clear(TRUE);
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public static function onDeleteSituacao($param)
    {
        $action = new TAction([__CLASS__, 'DeleteSituacao']);
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function DeleteSituacao($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new HistoricoDigitalSituacaoDiscente($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            
            //Recarrega todas as datagrids e passa como parâmetro a aba de onde partiu a chamada de função
            $pos_action = new TAction([__CLASS__, 'onShow']);
            $pos_action->setParameter('aba', 3);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public function onSaveSituacao( $param )
    {
        try
        {
            $dados_historico = TSession::getValue('dados_historico_digital');            
            
            TTransaction::open('Felabs_DB');                    
                        
            $this->formSituacoes->validate();
            $data = $this->formSituacoes->getData(); 
            
            $object = new HistoricoDigitalSituacaoDiscente;  
            $object->fromArray( (array) $data);
            
            
            //Controle campos condicionais - Intercâmbio
            if($object->situacao_discente == "IntercambioInternacional" OR $object->situacao_discente == "IntercambioNacional")
            {
                if((! $object->situacao_intercambio_instituicao) OR (! $object->situacao_intercambio_pais) OR (! $object->situacao_intercambio_programa))
                {
                    throw new Exception('É necessário preencher todos os dados relacionados ao intercâmbio');
                }
            }
            
            //Controle campos condicionais - Outra situação
            if($object->situacao_discente == "OutraSituacao")
            {
                if((! $object->situacao_outra))
                {
                    throw new Exception('É necessário preencher a situação atual do discente');
                }
            }            
            
            $object->historico_digital_id = $dados_historico->id;            
            $object->system_user_id = TSession::getValue('userid'); 
            $object->data_reg = date('Y-m-d H:i:s');

            $object->store(); 
            
            TTransaction::close(); 
            
            
            $data->id = $object->id;
            
            $this->formSituacoes->setData($data);             
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); 
            
            
            //Limpa o formulário depois de salvar, mas mantém o tipo de entrada preenchido
            $this->formSituacoes->clear();
                        
            $obj = new StdClass;
            $obj->tipo_entrada = "Situação Discente";
            
            TForm::sendData('form_HistoricoManualSituacaoDiscente', $obj);

            $this->onReloadDisciplinas(); 
            $this->onReloadAtividades();
            $this->onReloadEstagios();
            $this->onReloadSituacoes();


            //Mantém aba atual
            $this->notebook->setCurrentPage(3);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            
            $this->onReloadDisciplinas(); 
            $this->onReloadAtividades();
            $this->onReloadEstagios();
            $this->onReloadSituacoes();
            
            //Mantém aba atual
            $this->notebook->setCurrentPage(3);
            
            //Habilita ou desabilita campo dependente dependendo do que foi selecionado
            $param['situacao_discente'] = $object->situacao_discente;
            $this->onChangeSituacaoDiscente($param);
            
            $this->formSituacoes->setData( $this->formSituacoes->getData() ); 
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
        $this->formSituacoes->clear(TRUE);
        
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
        $this->onChangeSituacaoDiscente($param);
        
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
