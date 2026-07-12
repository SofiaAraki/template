<?php

class HorarioTurmaForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_HorarioMontagem');
        $this->form->setFormTitle('Montagem de Horário Semestral - Sistema Acadêmico');
        
        // ---- 1. CAPTURA DINÂMICA DA QUANTIDADE DE AULAS ----
        // 1º busca do POST/Ação atual (mudar na tela), 2º busca do banco (edição), 3º padrão inicial
        $qtd_aulas_grid = 3; 
        
        if (!empty($_REQUEST['QtdeMaximaAulasPorDia'])) {
            $qtd_aulas_grid = (int)$_REQUEST['QtdeMaximaAulasPorDia'];
        } else {
            $param_key = $_REQUEST['key'] ?? $_REQUEST['Codhorario'] ?? null;
            if (!empty($param_key)) {
                try {
                    TTransaction::open('dados_fei');
                    $horario_existente = new FiHorario($param_key);
                    if (!empty($horario_existente->QtdeMaximaAulasPorDia)) {
                        $qtd_aulas_grid = (int)$horario_existente->QtdeMaximaAulasPorDia;
                    }
                    TTransaction::close();
                } catch (Exception $e) {
                    $qtd_aulas_grid = 3;
                }
            }
        }
        
        // ---- 2. CAMPOS DO CABEÇALHO ----
        $cod_horario        = new TEntry('Codhorario'); $cod_horario->setEditable(false);
        $cod_turma          = new TDBCombo('CodTurmaetapa', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        
        $qtde_aulas         = new TEntry('QtdeMaximaAulasPorDia'); $qtde_aulas->setValue($qtd_aulas_grid); 
        $duracao_aula       = new TEntry('DuracaoAula'); $duracao_aula->setValue('50');
        $inicio_aula        = new TEntry('InicioAula'); $inicio_aula->setValue('19:10');
        $intervalo_apos_aula= new TEntry('IntervalorAula'); $intervalo_apos_aula->setValue('2'); 
        $duracao_intervalo  = new TEntry('DuracaoIntervalo'); $duracao_intervalo->setValue('15'); 
        $bimestre           = new TCombo('Bimestre'); $bimestre->addItems(['1'=>'1º Bimestre', '2'=>'2º Bimestre', '3'=>'3º Bimestre', '4'=>'4º Bimestre']); $bimestre->setValue('1');

        // Evento para reconstruir a tela imediatamente se o usuário mudar a quantidade de aulas na mão
        $change_action = new TAction([$this, 'onMudarQuantidadeAulas']);
        $qtde_aulas->setExitAction($change_action);

        // Layout do Cabeçalho
        $this->form->addFields([new TLabel('Código Horário:')], [$cod_horario], [new TLabel('Turma:')], [$cod_turma]);
        $this->form->addFields([new TLabel('Bimestre Corrente:')], [$bimestre]);
        
        $this->form->addContent(['<div class="alert alert-info" style="margin-bottom:0px; padding:8px;"><b>Definições Iniciais do Horário</b></div>']);
        
        $this->form->addFields(
            [new TLabel('Qt. Máx aulas por dia:')], [$qtde_aulas],
            [new TLabel('Duração das aulas (min):')], [$duracao_aula],
            [new TLabel('Horário inicial:')], [$inicio_aula]
        );
        $this->form->addFields(
            [new TLabel('Intervalo após a (aula):')], [$intervalo_apos_aula],
            [new TLabel('Duração do intervalo (min):')], [$duracao_intervalo]
        );

        // ---- 3. CRIAÇÃO DINÂMICA DAS ABAS POR DIA DA SEMANA ----
        $dias = [2 => 'Segunda-Feira', 3 => 'Terça-Feira', 4 => 'Quarta-Feira', 5 => 'Quinta-Feira', 6 => 'Sexta-Feira', 7 => 'Sábado'];
        $todos_combos = [];
        $todas_horas = [];

        foreach ($dias as $num_dia => $nome_dia) {
            $this->form->appendPage($nome_dia);
            
            for ($ordem = 1; $ordem <= $qtd_aulas_grid; $ordem++) {
                $campo_combo = "grade_{$ordem}_{$num_dia}";
                $campo_hora  = "hora_{$ordem}_{$num_dia}";
                
                $combo_disciplina = new TCombo($campo_combo);
                $combo_disciplina->enableSearch();
                $todos_combos[] = $combo_disciplina;
                
                $hora_inicio_aula = new TEntry($campo_hora);
                $hora_inicio_aula->setSize('80px');
                $hora_inicio_aula->setEditable(false);
                $todas_horas[] = $hora_inicio_aula;
                
                $this->form->addFields(
                    [new TLabel("<b>{$ordem}ª Aula</b> — Atribuição:")], [$combo_disciplina],
                    [new TLabel('Hr Início:')], [$hora_inicio_aula]
                );
            }
        }

        $campos_globais = array_merge(
            [$cod_horario, $cod_turma, $qtde_aulas, $duracao_aula, $inicio_aula, $intervalo_apos_aula, $duracao_intervalo, $bimestre],
            $todos_combos,
            $todas_horas
        );
        $this->form->setFields($campos_globais);

        $this->form->addAction('Voltar', new TAction(['HorarioTurmaList', 'onReload']), 'fa:arrow-left blue');
        $this->form->addAction('Gerar/Carregar Matriz', new TAction([$this, 'onGerarMatriz']), 'fa:magic orange');
        $this->form->addAction('Salvar Horário', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    /**
     * Recarrega a página preservando os dados digitados ao alterar a quantidade de aulas
     */
    public static function onMudarQuantidadeAulas($param)
    {
        AdiantiCoreApplication::loadPage('HorarioTurmaForm', 'onRebuild', $param);
    }

    public function onRebuild($param)
    {
        // Devolve os dados digitados para a tela após a reconstrução dinâmica do construtor
        $this->form->setData((object)$param);
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            $data = $this->form->getData();
            $this->form->validate();
            
            $horario = !empty($data->Codhorario) ? new FiHorario($data->Codhorario) : new FiHorario;
            
            $horario->CodTurmaetapa          = $data->CodTurmaetapa;
            $horario->InicioAula             = $data->InicioAula;
            $horario->DuracaoAula            = $data->DuracaoAula;
            $horario->QtdeMaximaAulasPorDia  = $data->QtdeMaximaAulasPorDia;
            $horario->IntervalorAula         = $data->IntervalorAula; 
            $horario->DuracaoIntervalo       = $data->DuracaoIntervalo;
            $horario->Bimestre               = $data->Bimestre;
            $horario->CodOperador            = TSession::getValue('userid') ?? 1;
            $horario->store();
            
            FiHorarioAulasDiarias::where('Codhorario', '=', $horario->Codhorario)->delete();
            
            $limite_aulas = (int)$data->QtdeMaximaAulasPorDia;
            
            for ($dia = 2; $dia <= 7; $dia++) {
                for ($ordem = 1; $ordem <= $limite_aulas; $ordem++) {
                    $campo_post = "grade_{$ordem}_{$dia}";
                    $campo_hora = "hora_{$ordem}_{$dia}";
                    
                    if (!empty($param[$campo_post])) {
                        $id_atribuicao = $param[$campo_post];
                        $atrib = new FiAtribuicaoGradeTurma($id_atribuicao);
                        
                        $aula_diaria = new FiHorarioAulasDiarias;
                        $aula_diaria->Codhorario = $horario->Codhorario;
                        $aula_diaria->CodAtribuicaoGradeTurma = $id_atribuicao;
                        $aula_diaria->CodGradeDisciplinaEtapa_Frente = $atrib->CodGradeDisciplinaEtapa_Frente;
                        $aula_diaria->DiaSemana = $dia;
                        $aula_diaria->NumeroOrdemAula = $ordem;
                        $aula_diaria->HoraAula = $param[$campo_hora] ?? null;
                        $aula_diaria->Compartilhada = 'N';
                        $aula_diaria->store();
                    }
                }
            }
            
            $data->Codhorario = $horario->Codhorario;
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', 'Grade de horários consolidada com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
            $this->form->setData($this->form->getData());
        }
    }

    public function onGerarMatriz($param)
    {
        try {
            $data = $this->form->getData();
            if (empty($data->CodTurmaetapa)) {
                throw new Exception('Por favor, selecione uma Turma antes de carregar a matriz.');
            }
            
            TTransaction::open('dados_fei');
            $atribuicoes = FiAtribuicaoGradeTurma::where('CodTurmaetapa', '=', $data->CodTurmaetapa)->load();
            
            $items_combo = [];
            if ($atribuicoes) {
                foreach ($atribuicoes as $atrib) {
                    $items_combo[$atrib->CodAtribuicaoGradeTurma] = "{$atrib->grade_frente->NomeFrente} ({$atrib->professor->Nome})";
                }
            }
            TTransaction::close();
            
            $inicio = $data->InicioAula ?? '19:10';
            $duracao = (int)($data->DuracaoAula ?? 50);
            $int_apos = (int)($data->IntervalorAula ?? 2);
            $int_duracao = (int)($data->DuracaoIntervalo ?? 15);
            $limite_aulas = (int)($data->QtdeMaximaAulasPorDia ?? 3);
            
            $obj_horas = new stdClass();
            $obj_horas->Codhorario = $data->Codhorario;
            $obj_horas->CodTurmaetapa = $data->CodTurmaetapa;
            $obj_horas->InicioAula = $data->InicioAula;
            $obj_horas->DuracaoAula = $data->DuracaoAula;
            $obj_horas->QtdeMaximaAulasPorDia = $data->QtdeMaximaAulasPorDia;
            $obj_horas->IntervalorAula = $data->IntervalorAula;
            $obj_horas->DuracaoIntervalo = $data->DuracaoIntervalo;
            $obj_horas->Bimestre = $data->Bimestre;
            
            for ($dia = 2; $dia <= 7; $dia++) {
                $timestamp_atual = strtotime($inicio);
                
                for ($ordem = 1; $ordem <= $limite_aulas; $ordem++) {
                    TCombo::reload('form_HorarioMontagem', "grade_{$ordem}_{$dia}", $items_combo);
                    
                    $nome_campo_hora = "hora_{$ordem}_{$dia}";
                    $nome_campo_grade = "grade_{$ordem}_{$dia}";
                    
                    $obj_horas->$nome_campo_hora = date('H:i', $timestamp_atual);
                    $obj_horas->$nome_campo_grade = $param[$nome_campo_grade] ?? null;
                    
                    $timestamp_atual += ($duracao * 60);
                    if ($ordem == $int_apos) {
                        $timestamp_atual += ($int_duracao * 60);
                    }
                }
            }
            
            TForm::sendData('form_HorarioMontagem', $obj_horas);
            
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param)
    {
        if (isset($param['key'])) {
            try {
                TTransaction::open('dados_fei');
                $horario = new FiHorario($param['key']);
                
                $obj = new stdClass();
                $obj->Codhorario             = $horario->Codhorario;
                $obj->CodTurmaetapa          = $horario->CodTurmaetapa;
                $obj->InicioAula             = $horario->InicioAula;
                $obj->DuracaoAula            = $horario->DuracaoAula;
                $obj->QtdeMaximaAulasPorDia  = $horario->QtdeMaximaAulasPorDia;
                $obj->IntervalorAula         = $horario->IntervalorAula;
                $obj->DuracaoIntervalo       = $horario->DuracaoIntervalo;
                $obj->Bimestre               = $horario->Bimestre;
                
                $atribuicoes = FiAtribuicaoGradeTurma::where('CodTurmaetapa', '=', $horario->CodTurmaetapa)->load();
                $items = [];
                if ($atribuicoes) {
                    foreach ($atribuicoes as $atrib) {
                        $items[$atrib->CodAtribuicaoGradeTurma] = "{$atrib->grade_frente->NomeFrente} ({$atrib->professor->Nome})";
                    }
                }
                
                $limite_aulas = (int)$horario->QtdeMaximaAulasPorDia;
                for ($dia = 2; $dia <= 7; $dia++) {
                    for ($ordem = 1; $ordem <= $limite_aulas; $ordem++) {
                        TCombo::reload('form_HorarioMontagem', "grade_{$ordem}_{$dia}", $items);
                    }
                }
                
                $aulas_salvas = FiHorarioAulasDiarias::where('Codhorario', '=', $horario->Codhorario)->load();
                if ($aulas_salvas) {
                    foreach ($aulas_salvas as $aula) {
                        $prop_grade = "grade_{$aula->NumeroOrdemAula}_{$aula->DiaSemana}";
                        $prop_hora  = "hora_{$aula->NumeroOrdemAula}_{$aula->DiaSemana}";
                        
                        $obj->$prop_grade = $aula->CodAtribuicaoGradeTurma;
                        $obj->$prop_hora  = $aula->HoraAula;
                    }
                }
                
                TTransaction::close();
                TForm::sendData('form_HorarioMontagem', $obj);
                
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
    }
}