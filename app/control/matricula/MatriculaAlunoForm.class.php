<?php

class MatriculaAlunoForm extends TPage
{
    protected $form;
    protected $grid_normais;
    protected $grid_dependencias;
    protected $grid_adaptacoes;
    
    // Campos de controle para inserção inline
    protected $dep_turma;
    protected $dep_disciplina;
    protected $adp_turma;
    protected $adp_disciplina;

    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FiMatriculaEtapa');
        $this->form->setFormTitle('Movimentação / Formulário de Matrícula');
        
        // ---- 1. INSTANCIAÇÃO DOS CAMPOS CABEÇALHO ----
        $cod_mat_etapa   = new TEntry('CodMatriculaEtapa'); $cod_mat_etapa->setEditable(FALSE);
        $cod_mat_inicial = new TEntry('CodMatriculaInicial');
        $aluno           = new TDBUniqueSearch('Codaluno', 'dados_fei', 'FiAluno', 'Codaluno', 'Nome');
        $aluno->setMinLength(3); $aluno->setMask('{Nome} ({Codaluno})');
        $turma           = new TDBCombo('CodTurmaetapa', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $grade_curso     = new TDBCombo('CodGradecurso', 'dados_fei', 'FiGradeCurso', 'CodGradecurso', 'CodGradecurso');
        $dt_matricula    = new TDate('DataMatricula'); $dt_matricula->setMask('dd/mm/yyyy');
        $ingresso        = new TCombo('Ingresso'); $ingresso->addItems(['01'=>'Regular', '02'=>'Vestibular', '03'=>'Histórico', '04'=>'Transferência']);
        $situacao        = new TCombo('Situacao'); $situacao->addItems(['MA'=>'Matriculado', 'TR'=>'Trancado', 'CA'=>'Cancelado', 'TE'=>'Transferido']);
        $dt_situacao     = new TDate('SituacaoData'); $dt_situacao->setMask('dd/mm/yyyy');
        $confirmada      = new TRadioGroup('ConfirmacaoMatricula'); $confirmada->addItems(['S'=>'Sim', 'N'=>'Não']); $confirmada->setLayout('horizontal');
        
        $qtd_disc    = new TEntry('QtdeDisciplinaEtapa'); $qtd_disc->setNumericMask(0, '', '');
        $qtd_dep     = new TEntry('QtdeDependenciaEtapa'); $qtd_dep->setNumericMask(0, '', '');
        $qtd_adapt   = new TEntry('QtdeAdaptacaoEtapa'); $qtd_adapt->setNumericMask(0, '', '');
        $res_final   = new TCombo('ResultadoFinal'); $res_final->addItems(['AP'=>'Aprovado', 'RE'=>'Reprovado', 'DP'=>'Dependência']);
        
        $num_seq        = new TEntry('NumeroSeq'); $num_seq->setEditable(FALSE);
        $n_reg          = new TEntry('NReg');
        $sit_tesouraria = new TEntry('SituacaoTesouraria');
        $sit_outros     = new TEntry('SituacaoOutros');
        $obs_geral      = new TEntry('Observacao');

        // ---- 2. INSTANCIAÇÃO DOS CAMPOS DE ADIÇÃO INTERNA ----
        
        // Dependências Inline
        $this->dep_turma = new TDBUniqueSearch('dep_turma', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $this->dep_turma->setMinLength(1); $this->dep_turma->setSize('100%');
        $this->dep_turma->setMask('{Identificacao} ({Ano}/{Semestre})');
        
        $this->dep_disciplina = new TCombo('dep_disciplina');
        $this->dep_turma->setChangeAction(new TAction([$this, 'onChangeTurmaDep']));

        // Adaptações Inline
        $this->adp_turma = new TDBUniqueSearch('adp_turma', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $this->adp_turma->setMinLength(1); $this->adp_turma->setSize('100%');
        $this->adp_turma->setMask('{Identificacao} ({Ano}/{Semestre})');
        
        $this->adp_disciplina = new TCombo('adp_disciplina');
        $this->adp_turma->setChangeAction(new TAction([$this, 'onChangeTurmaAdp']));

        // ---- 3. CONSTRUÇÃO DAS GRIDS ----
        $this->grid_normais = new TQuickGrid; $this->grid_normais->setHeight(180);
        $this->grid_normais->addQuickColumn('Etapa', 'Etapa', 'center', 60);
        $this->grid_normais->addQuickColumn('Disciplina', 'NomeDisciplina', 'left', 350);
        $this->grid_normais->addQuickColumn('Turma', 'CodTurmaetapa', 'center', 80);
        $this->grid_normais->addQuickColumn('Média', 'Media', 'right', 60);
        $this->grid_normais->addQuickColumn('% Freq', 'Frequencia', 'right', 60);
        $this->grid_normais->addQuickColumn('Resultado', 'Resultado', 'center', 100);
        $this->grid_normais->createModel();

        // GRID - DEPENDÊNCIAS (Com coluna de exclusão)
        $this->grid_dependencias = new TQuickGrid; $this->grid_dependencias->setHeight(180);
        $this->grid_dependencias->addQuickColumn('Etapa', 'Etapa', 'center', 60);
        $this->grid_dependencias->addQuickColumn('Disciplina', 'NomeDisciplina', 'left', 350);
        $this->grid_dependencias->addQuickColumn('Turma', 'CodTurmaetapa', 'center', 80);
        $this->grid_dependencias->addQuickColumn('Média', 'Media', 'right', 60);
        $this->grid_dependencias->addQuickColumn('Resultado', 'Resultado', 'center', 100);
        
        // Adiciona Ação de Exclusão com confirmação na grid de DPs
        $this->grid_dependencias->addQuickAction('Deletar DP', new TDataGridAction([$this, 'onDeleteDependencia']), 'CodDisciplinaChave', 'fa:trash red');
        $this->grid_dependencias->createModel();

        // GRID - ADAPTAÇÕES (Com coluna de exclusão)
        $this->grid_adaptacoes = new TQuickGrid; $this->grid_adaptacoes->setHeight(180);
        $this->grid_adaptacoes->addQuickColumn('Etapa', 'Etapa', 'center', 60);
        $this->grid_adaptacoes->addQuickColumn('Disciplina', 'NomeDisciplina', 'left', 350);
        $this->grid_adaptacoes->addQuickColumn('Turma', 'CodTurmaetapa', 'center', 80);
        $this->grid_adaptacoes->addQuickColumn('Média', 'Media', 'right', 60);
        $this->grid_adaptacoes->addQuickColumn('Resultado', 'Resultado', 'center', 100);
        
        // Adiciona Ação de Exclusão com confirmação na grid de Adaptações
        $this->grid_adaptacoes->addQuickAction('Deletar Adaptação', new TDataGridAction([$this, 'onDeleteAdaptacao']), 'CodDisciplinaChave', 'fa:trash red');
        $this->grid_adaptacoes->createModel();

        // ---- 4. DISTRIBUIÇÃO DOS CAMPOS NO FORMULÁRIO ----
        $this->form->addFields([new TLabel('Código da Matrícula:')], [$cod_mat_etapa], [new TLabel('ALUNO:')], [$aluno]);
        $this->form->addFields([new TLabel('Reg. Matrícula:')], [$cod_mat_inicial], [new TLabel('Grade:')], [$grade_curso]);
        $this->form->addFields([new TLabel('Curso:')], [$turma], [new TLabel('Data Ingresso:')], [$dt_matricula]);
        $this->form->addFields([new TLabel('Tipo de Ingresso:')], [$ingresso]);

        // Aba 1: Normais
        $this->form->appendPage('Disciplinas da Etapa');
        $this->form->addFields([new TLabel('Confirmação da Matrícula:')], [$confirmada]);
        $this->form->addFields([new TLabel('Situação da Matrícula:')], [$situacao], [new TLabel('Data da Situação:')], [$dt_situacao]);
        $this->form->addFields([new TLabel('Situação Tesouraria:')], [$sit_tesouraria]);
        $this->form->addContent([$this->grid_normais]);

        // Aba 2: Dependências
        $this->form->appendPage('Dependências');
        $btn_add_dep = TButton::create('btn_add_dep', [$this, 'onAddDependencia'], 'Adicionar DP', 'fa:plus green');
        $this->form->addFields(
            [new TLabel('Selecionar Turma/Curso Ofertado:')], [$this->dep_turma],
            [new TLabel('Disciplina:')], [$this->dep_disciplina],
            [], [$btn_add_dep]
        );
        $this->form->addContent([$this->grid_dependencias]);

        // Aba 3: Adaptações
        $this->form->appendPage('Adaptações');
        $btn_add_adp = TButton::create('btn_add_adp', [$this, 'onAddAdaptacao'], 'Adicionar Adaptação', 'fa:plus green');
        $this->form->addFields(
            [new TLabel('Selecionar Turma/Curso Ofertado:')], [$this->adp_turma],
            [new TLabel('Disciplina:')], [$this->adp_disciplina],
            [], [$btn_add_adp]
        );
        $this->form->addContent([$this->grid_adaptacoes]);

        // Aba 4: Observações
        $this->form->appendPage('Tesouraria / Observações');
        $this->form->addFields([new TLabel('Nº Registro (NReg):')], [$n_reg], [new TLabel('Nº Seq:')], [$num_seq]);
        $this->form->addFields([new TLabel('Situação Outros:')], [$sit_outros]);
        $this->form->addFields([new TLabel('Observações do Operador:')], [$obs_geral]);

        // Rodapé
        $this->form->addFields(
            [new TLabel('Quantidade de disciplinas - Et:')], [$qtd_disc], 
            [new TLabel('Dp:')], [$qtd_dep], 
            [new TLabel('Adp:')], [$qtd_adapt],
            [new TLabel('Resultado Final:')], [$res_final]
        );

        $this->form->setFields([
            $cod_mat_etapa, $cod_mat_inicial, $aluno, $turma, $grade_curso, $dt_matricula, $ingresso, $situacao, $dt_situacao, $confirmada,
            $qtd_disc, $qtd_dep, $qtd_adapt, $res_final, $num_seq, $n_reg, $sit_tesouraria, $sit_outros, $obs_geral,
            $this->dep_turma, $this->dep_disciplina, $this->adp_turma, $this->adp_disciplina
        ]);

        $this->form->addAction('Voltar', new TAction(['MatriculaAlunoList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }

    // ---- LÓGICA DE ATUALIZAÇÃO DOS COMBOS DE DISCIPLINAS POR TURMA ----
    
    public static function onChangeTurmaDep($param)
    {
        if (!empty($param['dep_turma'])) {
            try {
                TTransaction::open('dados_fei');
                $items = FiDisciplinasAtuais::where('CodTurmaetapa', '=', $param['dep_turma'])->load();
                $options = [];
                foreach ($items as $item) {
                    $options[$item->CodDisciplina] = $item->CodDisciplina . ' - ' . ($item->get_disciplina()->Nomeusual ?? 'Disciplina');
                }
                TCombo::reload('form_FiMatriculaEtapa', 'dep_disciplina', $options);
                TTransaction::close();
            } catch (Exception $e) { /**/ }
        }
    }

    public static function onChangeTurmaAdp($param)
    {
        if (!empty($param['adp_turma'])) {
            try {
                TTransaction::open('dados_fei');
                $items = FiDisciplinasAtuais::where('CodTurmaetapa', '=', $param['adp_turma'])->load();
                $options = [];
                foreach ($items as $item) {
                    $options[$item->CodDisciplina] = $item->CodDisciplina . ' - ' . ($item->get_disciplina()->Nomeusual ?? 'Disciplina');
                }
                TCombo::reload('form_FiMatriculaEtapa', 'adp_disciplina', $options);
                TTransaction::close();
            } catch (Exception $e) { /**/ }
        }
    }

    // ---- MÉTODOS DE INCLUSÃO ----

    public function onAddDependencia($param)
    {
        try {
            if (empty($param['CodMatriculaEtapa'])) {
                throw new Exception('Carregue uma matrícula ativa antes de adicionar dependências.');
            }
            if (empty($param['dep_turma']) || empty($param['dep_disciplina'])) {
                throw new Exception('Selecione a Turma e a Disciplina para a inclusão da DP.');
            }

            TTransaction::open('dados_fei');
            
            $dp = new FiDisciplinasDP;
            $dp->CodMatriculaEtapa = $param['CodMatriculaEtapa'];
            $dp->CodTurmaetapa     = $param['dep_turma'];
            $dp->CodDisciplina     = $param['dep_disciplina'];
            $dp->Cursando          = 'S';
            $dp->store();

            TTransaction::close();
            
            new TMessage('info', 'Dependência incluída com sucesso!');
            $this->onEdit(['key' => $param['CodMatriculaEtapa']]);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onAddAdaptacao($param)
    {
        try {
            if (empty($param['CodMatriculaEtapa'])) {
                throw new Exception('Carregue uma matrícula ativa antes de adicionar adaptações.');
            }
            if (empty($param['adp_turma']) || empty($param['adp_disciplina'])) {
                throw new Exception('Selecione a Turma e a Disciplina para a inclusão da Adaptação.');
            }

            TTransaction::open('dados_fei');
            
            $adp = new FiDisciplinasAdaptacao;
            $adp->CodMatriculaEtapa = $param['CodMatriculaEtapa'];
            $adp->CodTurmaetapa     = $param['adp_turma'];
            $adp->CodDisciplina     = $param['adp_disciplina'];
            $adp->store();

            TTransaction::close();
            
            new TMessage('info', 'Adaptação incluída com sucesso!');
            $this->onEdit(['key' => $param['CodMatriculaEtapa']]);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    // ---- MÉTODOS DE EXCLUSÃO COM CONFIRMAÇÃO VISUAL ----

    public function onDeleteDependencia($param)
    {
        if (isset($param['key'])) {
            $action = new TAction([$this, 'DeleteDependencia']);
            $action->setParameters($param);
            
            // Pergunta de confirmação visual
            new TQuestion('Deseja realmente excluir esta Dependência?', $action);
        }
    }

    public function DeleteDependencia($param)
    {
        try {
            if (empty($param['key'])) {
                throw new Exception('Identificador do registro não encontrado.');
            }

            TTransaction::open('dados_fei');
            
            // Instancia direto pela chave física recuperada do "CodDisciplinaChave"
            $object = new FiDisciplinasDP($param['key']);
            $codMatriculaEtapa = $object->CodMatriculaEtapa;
            
            $object->delete();
            TTransaction::close();
            
            new TMessage('info', 'Dependência excluída com sucesso!');
            $this->onEdit(['key' => $codMatriculaEtapa]); // Recarrega usando a matrícula
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onDeleteAdaptacao($param)
    {
        if (isset($param['key'])) {
            $action = new TAction([$this, 'DeleteAdaptacao']);
            $action->setParameters($param);
            
            // Pergunta de confirmação visual
            new TQuestion('Deseja realmente excluir esta Adaptação?', $action);
        }
    }

    public function DeleteAdaptacao($param)
    {
        try {
            if (empty($param['key'])) {
                throw new Exception('Identificador do registro não encontrado.');
            }

            TTransaction::open('dados_fei');
            
            // Instancia direto pela chave física recuperada do "CodDisciplinaChave"
            $object = new FiDisciplinasAdaptacao($param['key']);
            $codMatriculaEtapa = $object->CodMatriculaEtapa;
            
            $object->delete();
            TTransaction::close();
            
            new TMessage('info', 'Adaptação excluída com sucesso!');
            $this->onEdit(['key' => $codMatriculaEtapa]); // Recarrega usando a matrícula
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    // ---- FLUXOS PADRÕES DO SISTEMA (ONEDIT / ONSAVE) ----

    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            $this->form->validate();
            $data = $this->form->getData();
            
            $matricula = new FiMatriculaetapa;
            $matricula->fromArray((array) $data);
            $matricula->CodOperador     = TSession::getValue('userid');
            $matricula->DataAtualizacao = date('Y-m-d H:i:s');
            
            if (!empty($matricula->DataMatricula)) {
                $matricula->DataMatricula = TDate::convertToMask($matricula->DataMatricula, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            if (!empty($matricula->SituacaoData)) {
                $matricula->SituacaoData = TDate::convertToMask($matricula->SituacaoData, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
            $matricula->store();
            TTransaction::close();
            
            new TMessage('info', 'Matrícula salva com sucesso!');
            $this->onEdit(['key' => $matricula->CodMatriculaEtapa]);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onEdit($param)
    {
        if (isset($param['key'])) {
            try {
                TTransaction::open('dados_fei');
                
                $object = new FiMatriculaetapa($param['key']);
                if (!empty($object->DataMatricula)) {
                    $object->DataMatricula = TDate::convertToMask($object->DataMatricula, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                if (!empty($object->SituacaoData)) {
                    $object->SituacaoData = TDate::convertToMask($object->SituacaoData, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);

                $this->grid_normais->clear();
                $this->grid_dependencias->clear();
                $this->grid_adaptacoes->clear();

                $disciplinas = VwFiDisciplinasATADDP::where('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa)->load();

                if ($disciplinas) {
                    foreach ($disciplinas as $disc) {
                        $tipoSigla = trim(strtoupper((string)($disc->Tipo ?? '')));

                        if ($tipoSigla === 'DP') {
                            $this->grid_dependencias->addItem($disc);
                        } else if ($tipoSigla === 'AD') {
                            $this->grid_adaptacoes->addItem($disc);
                        } else if ($tipoSigla === 'AT' || empty($tipoSigla)) {
                            $this->grid_normais->addItem($disc);
                        }
                    }
                }
                
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
    }
}