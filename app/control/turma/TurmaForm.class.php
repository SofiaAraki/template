<?php

class TurmaForm extends TPage
{
    protected $form;
    protected $grid_disciplinas;
    protected $grid_avaliacoes;
    protected $grid_migracao;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FiTurmaEtapa');
        $this->form->setFormTitle('Cadastro de Turmas');
        
        // ---- 1. CAMPOS DO CABEÇALHO ----
        $cod_curso  = new TDBUniqueSearch('CodCurso', 'dados_fei', 'FiCurso', 'CodCurso', 'Nome');
        $cod_curso->setMinLength(1);
        $ano        = new TEntry('Ano');
        $semestre   = new TCombo('Semestre'); $semestre->addItems(['1'=>'1º', '2'=>'2º']);
        //$semestre   = new TCombo('Semestre'); $semestre->addItems(['1'=>'1º Semestre', '2'=>'2º Semestre', '3'=>'3º Semestre', '4'=>'4º Semestre', '5'=>'5º Semestre', '6'=>'6º Semestre', '7'=>'7º Semestre', '8'=>'8º Semestre', '9'=>'9º Semestre', '10'=>'10º Semestre']);
        
        // ---- 2. PAINEL DE GRADE CURRICULAR ----
        $grade             = new TDBUniqueSearch('CodGradeEtapa', 'dados_fei', 'FiGradeEtapa', 'CodGradeEtapa', 'Descricao');
        $grade->setMinLength(1);
        $ano_inicial       = new TEntry('AnoInicial'); $ano_inicial->setEditable(FALSE);
        $q_etapas          = new TEntry('QEtapas'); $q_etapas->setEditable(FALSE);
        $duracao_etapa     = new TEntry('DuracaoEtapa'); $duracao_etapa->setEditable(FALSE);
        $turma_da_etapa    = new TSpinner('TurmaDaEtapa'); $turma_da_etapa->setRange(1, 10, 1);
        $acesso_moodle     = new TCheckButton('AcessoMoodle');
        
        // ---- 3. IDENTIFICADORES E LOCALIZAÇÃO ----
        $codigo_da_turma   = new TEntry('CodTurmaetapa'); $codigo_da_turma->setEditable(FALSE);
        $grade_id          = new TEntry('GradeId'); $grade_id->setEditable(FALSE);
        $etapa_id          = new TEntry('EtapaId'); $etapa_id->setEditable(FALSE);
        $sistema_av        = new TDBCombo('CodSistemaAvaliacao', 'dados_fei', 'FiSistemaAvaliacao', 'CodSistemaAvaliacao', 'Descricao');
        
        $identificacao     = new TEntry('Identificacao');
        $ano_turma         = new TEntry('AnoTurma');
        $semestre_radio    = new TRadioGroup('SemestreRadio');
        $semestre_radio->addItems(['1'=>'1º', '2'=>'2º']); $semestre_radio->setLayout('horizontal');
        
        $sala              = new TEntry('Sala');
        $bloco             = new TEntry('Bloco');
        $campus            = new TEntry('Campus');
        $dt_inicial        = new TDate('DataInicial'); $dt_inicial->setMask('dd/mm/yyyy');
        $dt_final          = new TDate('DataFinal'); $dt_final->setMask('dd/mm/yyyy');
        $validade_cartao   = new TDate('Validade_Cartao'); $validade_cartao->setMask('mm/yyyy');

        // ---- 4. CAMPOS COMPLEMENTARES ----
        $periodo = new TRadioGroup('Periodo');
        $periodo->addItems(['M'=>'Manhã', 'T'=>'Tarde', 'N'=>'Noite', 'I'=>'Integral']);
        $periodo->setLayout('horizontal');

        $tcc       = new TCheckButton('TCC');
        $ativ_comp = new TCheckButton('AtivComp');
        $estagio   = new TCheckButton('Estagio');

        $apont_faltas = new TRadioGroup('ApontamentoFaltas');
        $apont_faltas->addItems(['B'=>'Bimestral', 'D'=>'Diário']); $apont_faltas->setLayout('horizontal');
        
        $prova_integrada   = new TCheckButton('ProvaIntegrada');
        $rec_bim_ant       = new TCheckButton('Recupera_Bim_Anterior');

        // ---- 5. CAMPOS DE ATRIBUIÇÃO DA LINHA SELECIONADA (ABA 3) ----
        $atribuicao_id = new THidden('AtribuicaoId'); 
        $prof_add      = new TDBUniqueSearch('ProfAdd', 'dados_fei', 'FiProfessor', 'CodProfessor', 'Nome'); $prof_add->setMinLength(1);
        $modalidade    = new TCombo('ModalidadeAdd'); $modalidade->addItems(['P'=>'Presencial', 'E'=>'EAD']);
        
        // ---- CAMPOS DE ATRIBUIÇÃO DE MIGRAÇÃO (ABA 4) ----
        $migracao_id      = new THidden('MigracaoId'); // ID físico de FiAtribuicaoGradeTurma para edição de migração
        $id_migracao_input = new TEntry('IdMigracaoInput');
        
        // ---- CAMPOS DE ATRIBUIÇÃO DE AVALIAÇÕES (ABA 5) ----
        $avaliacao_id = new THidden('AvaliacaoId'); // ID físico de FiTurmaPeriodoAvaliacoes
        $av_label     = new TEntry('AvLabel'); $av_label->setEditable(FALSE); // Apenas mostra qual avaliação está editando (Ex: B1)
        $av_dt_ini    = new TDate('AvDtIni'); $av_dt_ini->setMask('dd/mm/yyyy');
        $av_dt_fim    = new TDate('AvDtFim'); $av_dt_fim->setMask('dd/mm/yyyy');

        // ---- 6. GRIDS DE LANÇAMENTO ----
        
        // Grid 1: Disciplinas/Atribuição
        $this->grid_disciplinas = new TQuickGrid; $this->grid_disciplinas->setHeight(180);
        $this->grid_disciplinas->addQuickColumn('Et/Mód', 'Etapa', 'center', 50);
        $this->grid_disciplinas->addQuickColumn('Disciplina', 'NomeDisciplina', 'left', 200);
        $this->grid_disciplinas->addQuickColumn('Professor', 'NomeProfessor', 'left', 150);
        $this->grid_disciplinas->addQuickColumn('C.Hr.', 'Ch', 'center', 50);
        
        $action_editar_prof = new TDataGridAction([$this, 'onSelectAtribuicao']);
        $action_editar_prof->setField('CodAtribuicaoGradeTurma');
        $action_editar_prof->setProperty('target', 'ajax'); 
        $this->grid_disciplinas->addQuickAction('Atribuir/Alterar Professor', $action_editar_prof, 'CodAtribuicaoGradeTurma', 'fa:edit blue');
        $this->grid_disciplinas->createModel();
        
        // Grid 2: Dados para Migração
        $this->grid_migracao = new TQuickGrid; $this->grid_migracao->setHeight(180);
        $this->grid_migracao->addQuickColumn('Disciplina', 'NomeDisciplina', 'left', 200);
        $this->grid_migracao->addQuickColumn('ID p/Migração', 'IdMigracao', 'left', 120);
        
        $action_editar_mig = new TDataGridAction([$this, 'onSelectMigracao']);
        $action_editar_mig->setField('CodAtribuicaoGradeTurma');
        $action_editar_mig->setProperty('target', 'ajax');
        $this->grid_migracao->addQuickAction('Editar Mapeamento', $action_editar_mig, 'CodAtribuicaoGradeTurma', 'fa:edit blue');
        $this->grid_migracao->createModel();
        
        // Grid 3: Período das Avaliações
        $this->grid_avaliacoes = new TQuickGrid; $this->grid_avaliacoes->setHeight(180);
        $this->grid_avaliacoes->addQuickColumn('Avaliação', 'Avaliacao', 'center', 70);
        $this->grid_avaliacoes->addQuickColumn('Data Inicial', 'DataInicial', 'center', 90);
        $this->grid_avaliacoes->addQuickColumn('Data Final', 'DataFinal', 'center', 90);
        
        $action_editar_av = new TDataGridAction([$this, 'onSelectAvaliacao']);
        $action_editar_av->setField('CodTurmaPeriodoAvaliacoes'); // Ajuste o nome da PK da sua tabela de período de avaliações se necessário
        $action_editar_av->setProperty('target', 'ajax');
        $this->grid_avaliacoes->addQuickAction('Editar Datas', $action_editar_av, 'CodTurmaPeriodoAvaliacoes', 'fa:edit blue');
        $this->grid_avaliacoes->createModel();

        // ---- 7. RODAPÉ DO BLOCO DE DADOS ----
        $chk_atribuicao    = new TCheckButton('ChkAtribuicao');
        $professor_titular = new TDBUniqueSearch('CodProfessor', 'dados_fei', 'FiProfessor', 'CodProfessor', 'Nome'); $professor_titular->setMinLength(1);
        $operador          = new TEntry('NomeOperador'); $operador->setEditable(FALSE);

        // ==================== ORGANIZAÇÃO DAS ABAS (LAYOUT) ====================
        
        // ---- ABA 1: Identificação e Localização ----
        $this->form->appendPage('Estrutura e Identificação');
        $this->form->addFields([new TLabel('Curso:')], [$cod_curso], [new TLabel('Ano:')], [$ano], [new TLabel('Semestre:')], [$semestre]);
        $this->form->addFields([new TLabel('Grade Curricular:')], [$grade], [new TLabel('Ano Inicial:')], [$ano_inicial]);
        $this->form->addFields([new TLabel('Qtd. Etapas:')], [$q_etapas], [new TLabel('Duração:')], [$duracao_etapa], [new TLabel('Turma da Etapa:')], [$turma_da_etapa]);
        $this->form->addFields([new TLabel('Código Turma:')], [$codigo_da_turma], [new TLabel('Grade ID:')], [$grade_id], [new TLabel('Etapa ID:')], [$etapa_id], [new TLabel('Sistema Avaliação:')], [$sistema_av]);
        $this->form->addFields([new TLabel('Identificação:')], [$identificacao], [new TLabel('Ano Turma:')], [$ano_turma], [new TLabel('Período Semestre:')], [$semestre_radio]);
        $this->form->addFields([new TLabel('Campus:')], [$campus], [new TLabel('Sala:')], [$sala], [new TLabel('Bloco:')], [$bloco]);
        $this->form->addFields([new TLabel('Vigência Início:')], [$dt_inicial], [new TLabel('Vigência Fim:')], [$dt_final], [new TLabel('Validade Cartão:')], [$validade_cartao]);

        // ---- ABA 2: Parâmetros e Configurações ----
        $this->form->appendPage('Parâmetros Acadêmicos');
        $this->form->addFields([new TLabel('Turno/Período:')], [$periodo]);
        $this->form->addFields([new TLabel('Apontamento de Faltas:')], [$apont_faltas]);
        $this->form->addFields([new TLabel('Acesso Moodle:')], [$acesso_moodle], [new TLabel('Prova Integrada:')], [$prova_integrada], [new TLabel('Recuperação Bimestral:')], [$rec_bim_ant]);
        $this->form->addFields([new TLabel('Complementações:')], [THBox::pack(new TLabel('TCC'), $tcc, new TLabel('Atividades'), $ativ_comp, new TLabel('Estágio'), $estagio)]);
        $this->form->addFields([new TLabel('Verificações:')], [$chk_atribuicao]);
        $this->form->addFields([new TLabel('Professor Titular:')], [$professor_titular]);
        $this->form->addFields([new TLabel('Operador do Sistema:')], [$operador]);

        // ---- ABA 3: Atribuição de Professores ----
        $this->form->appendPage('Disciplinas / Atribuição');
        $frame_disc = new TFrame; $frame_disc->setLegend('Gerenciar Atribuição de Professores por Disciplina');
        $btn_gravar_prof = TButton::create('save_prof', [$this, 'onSaveProfessorAtribuído'], 'Gravar Professor', 'fa:check green');
        
        $frame_disc->add(THBox::pack($atribuicao_id, new TLabel('Selecione uma linha abaixo para alterar o Professor:'), $prof_add, new TLabel('Modalidade:'), $modalidade, $btn_gravar_prof));
        $frame_disc->add($this->grid_disciplinas);
        $this->form->addContent([$frame_disc]);

        // ---- ABA 4: Dados para Migração ----
        $this->form->appendPage('Dados p/ Migração');
        $frame_mig = new TFrame; $frame_mig->setLegend('Mapeamento e Identificadores de Integração externa');
        $btn_gravar_mig = TButton::create('save_mig', [$this, 'onSaveMigracao'], 'Gravar ID Migração', 'fa:check green');
        
        $frame_mig->add(THBox::pack($migracao_id, new TLabel('Selecione uma linha abaixo e altere o Código de ID p/ Migração:'), $id_migracao_input, $btn_gravar_mig));
        $frame_mig->add($this->grid_migracao);
        $this->form->addContent([$frame_mig]);

        // ---- ABA 5: Datas e Período das Avaliações ----
        $this->form->appendPage('Período das Avaliações');
        $frame_av = new TFrame; $frame_av->setLegend('Cronograma de Avaliações Institucionais');
        $btn_gravar_av = TButton::create('save_av', [$this, 'onSaveAvaliacao'], 'Gravar Cronograma', 'fa:check green');
        
        $frame_av->add(THBox::pack($avaliacao_id, new TLabel('Avaliação:'), $av_label, new TLabel('Data Início:'), $av_dt_ini, new TLabel('Data Fim:'), $av_dt_fim, $btn_gravar_av));
        $frame_av->add($this->grid_avaliacoes);
        $this->form->addContent([$frame_av]);

        // ---- 8. MAPEAMENTO GLOBAL DE CAMPOS ----
        $this->form->setFields([
            $cod_curso, $ano, $semestre, $grade, $ano_inicial, $q_etapas, $duracao_etapa, $turma_da_etapa, $acesso_moodle,
            $codigo_da_turma, $grade_id, $etapa_id, $sistema_av, $identificacao, $ano_turma, $semestre_radio, $sala, $bloco,
            $campus, $dt_inicial, $dt_final, $validade_cartao, $periodo, $tcc, $ativ_comp, $estagio, $apont_faltas, 
            $prova_integrada, $rec_bim_ant, $chk_atribuicao, $professor_titular, $operador,
            $atribuicao_id, $prof_add, $modalidade, $btn_gravar_prof,
            $migracao_id, $id_migracao_input, $btn_gravar_mig,
            $avaliacao_id, $av_label, $av_dt_ini, $av_dt_fim, $btn_gravar_av
        ]);

        $this->form->addAction('Voltar', new TAction(['TurmaList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Turma', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            $this->form->validate();
            $data = $this->form->getData();
            
            $turma = new FiTurmaEtapa;
            $turma->fromArray((array) $data);
            $turma->CodOperador = TSession::getValue('userid');
            
            if (!empty($turma->DataInicial)) {
                $turma->DataInicial = TDate::convertToMask($turma->DataInicial, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            if (!empty($turma->DataFinal)) {
                $turma->DataFinal = TDate::convertToMask($turma->DataFinal, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
            $turma->store();
            TTransaction::close();
            new TMessage('info', 'Turma salva com sucesso!');
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
                $object = new FiTurmaEtapa($param['key']);
                
                if (!empty($object->DataInicial)) {
                    $object->DataInicial = TDate::convertToMask($object->DataInicial, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                if (!empty($object->DataFinal)) {
                    $object->DataFinal = TDate::convertToMask($object->DataFinal, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                if (isset($object->operador)) {
                    $object->NomeOperador = $object->operador->Nome ?? $object->operador->Login ?? '';
                }
                
                $this->form->setData($object);

                $this->reloadGrids($object->CodTurmaetapa);
                
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
    }

    /**
     * ABA 3: Seleciona Professor para Edição Inline
     */
    public function onSelectAtribuicao($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('dados_fei');
                
                $atribuicao = new FiAtribuicaoGradeTurma($param['key']);
                $codTurma = $atribuicao->CodTurmaetapa;
                
                $obj = new stdClass;
                $obj->AtribuicaoId  = $atribuicao->CodAtribuicaoGradeTurma;
                $obj->ModalidadeAdd = $atribuicao->Modalidade ?? 'P';
                
                if (!empty($atribuicao->CodProfessor) && isset($atribuicao->professor)) {
                    $obj->ProfAdd = [$atribuicao->CodProfessor => $atribuicao->professor->Nome];
                } else {
                    $obj->ProfAdd = [];
                }
                
                TForm::sendData('form_FiTurmaEtapa', $obj);
                $this->reloadGrids($codTurma);
                
                TTransaction::close();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * ABA 3: Grava alteração do Professor via AJAX
     */
    public function onSaveProfessorAtribuído($param)
    {
        try {
            if (empty($param['AtribuicaoId'])) {
                throw new Exception('Selecione uma disciplina na lista abaixo antes de gravar.');
            }

            TTransaction::open('dados_fei');
            
            $atribuicao = new FiAtribuicaoGradeTurma($param['AtribuicaoId']);
            $atribuicao->CodProfessor = !empty($param['ProfAdd']) ? $param['ProfAdd'] : null;
            $atribuicao->Modalidade   = $param['ModalidadeAdd'] ?? 'P';
            $atribuicao->store();
            
            $codTurma = $atribuicao->CodTurmaetapa;
            
            $limpa = new stdClass;
            $limpa->AtribuicaoId  = '';
            $limpa->ProfAdd       = [];
            $limpa->ModalidadeAdd = 'P';
            TForm::sendData('form_FiTurmaEtapa', $limpa);

            $this->reloadGrids($codTurma);
            TTransaction::close();
            
            new TMessage('info', 'Professor atribuído com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * ABA 4: Seleciona linha para edição do ID de Migração
     */
    public function onSelectMigracao($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('dados_fei');
                
                $atribuicao = new FiAtribuicaoGradeTurma($param['key']);
                $codTurma = $atribuicao->CodTurmaetapa;
                
                $obj = new stdClass;
                $obj->MigracaoId        = $atribuicao->CodAtribuicaoGradeTurma;
                $obj->IdMigracaoInput   = $atribuicao->Migracao_DisciplinaID;
                
                TForm::sendData('form_FiTurmaEtapa', $obj);
                $this->reloadGrids($codTurma);
                
                TTransaction::close();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * ABA 4: Salva ID de migração isoladamente via AJAX
     */
    public function onSaveMigracao($param)
    {
        try {
            if (empty($param['MigracaoId'])) {
                throw new Exception('Selecione uma linha da tabela de migração antes de gravar.');
            }

            TTransaction::open('dados_fei');
            
            $atribuicao = new FiAtribuicaoGradeTurma($param['MigracaoId']);
            $atribuicao->Migracao_DisciplinaID = $param['IdMigracaoInput'] ?? null;
            $atribuicao->store();
            
            $codTurma = $atribuicao->CodTurmaetapa;
            
            $limpa = new stdClass;
            $limpa->MigracaoId      = '';
            $limpa->IdMigracaoInput = '';
            TForm::sendData('form_FiTurmaEtapa', $limpa);

            $this->reloadGrids($codTurma);
            TTransaction::close();
            
            new TMessage('info', 'ID de Migração atualizado com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * ABA 5: Seleciona linha para edição do Cronograma de Avaliações
     */
    public function onSelectAvaliacao($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('dados_fei');
                
                $av = new FiTurmaPeriodoAvaliacoes($param['key']);
                $codTurma = $av->CodTurmaetapa;
                
                $obj = new stdClass;
                $obj->AvaliacaoId = $av->CodTurmaPeriodoAvaliacoes;
                $obj->AvLabel     = $av->Avaliacao;
                $obj->AvDtIni     = !empty($av->DataInicial) ? TDate::convertToMask($av->DataInicial, 'yyyy-mm-dd', 'dd/mm/yyyy') : '';
                $obj->AvDtFim     = !empty($av->DataFinal) ? TDate::convertToMask($av->DataFinal, 'yyyy-mm-dd', 'dd/mm/yyyy') : '';
                
                TForm::sendData('form_FiTurmaEtapa', $obj);
                $this->reloadGrids($codTurma);
                
                TTransaction::close();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * ABA 5: Salva datas das avaliações via AJAX
     */
    public function onSaveAvaliacao($param)
    {
        try {
            if (empty($param['AvaliacaoId'])) {
                throw new Exception('Selecione um período de avaliação abaixo antes de gravar.');
            }

            TTransaction::open('dados_fei');
            
            $av = new FiTurmaPeriodoAvaliacoes($param['AvaliacaoId']);
            $av->DataInicial = !empty($param['AvDtIni']) ? TDate::convertToMask($param['AvDtIni'], 'dd/mm/yyyy', 'yyyy-mm-dd') : null;
            $av->DataFinal   = !empty($param['AvDtFim']) ? TDate::convertToMask($param['AvDtFim'], 'dd/mm/yyyy', 'yyyy-mm-dd') : null;
            $av->store();
            
            $codTurma = $av->CodTurmaetapa;
            
            $limpa = new stdClass;
            $limpa->AvaliacaoId = '';
            $limpa->AvLabel     = '';
            $limpa->AvDtIni     = '';
            $limpa->AvDtFim     = '';
            TForm::sendData('form_FiTurmaEtapa', $limpa);

            $this->reloadGrids($codTurma);
            TTransaction::close();
            
            new TMessage('info', 'Cronograma de avaliação atualizado!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Centralizador de renderização estática de Grids
     */
    private function reloadGrids($codTurmaetapa)
    {
        $this->grid_disciplinas->clear();
        $this->grid_migracao->clear();
        $this->grid_avaliacoes->clear();

        if (empty($codTurmaetapa)) return;

        // Recarrega disciplinas e migração
        $atribuicoes = FiAtribuicaoGradeTurma::where('CodTurmaetapa', '=', $codTurmaetapa)->load();
        if ($atribuicoes) {
            foreach ($atribuicoes as $atribuicao) {
                $itemGrid = new stdClass;
                $itemGrid->CodAtribuicaoGradeTurma = $atribuicao->CodAtribuicaoGradeTurma;
                $itemGrid->Etapa                   = $atribuicao->turma->TurmaDaEtapa ?? '1';
                $itemGrid->NomeDisciplina          = $atribuicao->grade_frente->NomeDisciplina ?? 'Não informada';
                $itemGrid->NomeProfessor           = $atribuicao->professor->Nome ?? 'Sem Professor';
                $itemGrid->Ch                      = $atribuicao->grade_frente->CargaHoraria ?? '-';
                $itemGrid->IdMigracao              = $atribuicao->Migracao_DisciplinaID;

                $this->grid_disciplinas->addItem($itemGrid);
                $this->grid_migracao->addItem($itemGrid);
            }
        }

        // Recarrega avaliações
        $avaliacoes = FiTurmaPeriodoAvaliacoes::where('CodTurmaetapa', '=', $codTurmaetapa)->load();
        if ($avaliacoes) {
            foreach ($avaliacoes as $av) {
                $itemAv = new stdClass;
                $itemAv->CodTurmaPeriodoAvaliacoes = $av->CodTurmaPeriodoAvaliacoes; // Injeta a PK para controle do botão editar
                $itemAv->Avaliacao                 = $av->Avaliacao;
                $itemAv->DataInicial               = !empty($av->DataInicial) ? TDate::convertToMask($av->DataInicial, 'yyyy-mm-dd', 'dd/mm/yyyy') : '';
                $itemAv->DataFinal                 = !empty($av->DataFinal) ? TDate::convertToMask($av->DataFinal, 'yyyy-mm-dd', 'dd/mm/yyyy') : '';
                
                $this->grid_avaliacoes->addItem($itemAv);
            }
        }
    }
}