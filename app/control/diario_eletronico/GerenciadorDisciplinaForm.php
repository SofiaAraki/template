<?php
/**
 * GerenciadorDisciplinaForm - Centralizador de Diário, Frequência e Notas
 * Responsabilidade: Orquestrar os sub-componentes dentro de um layout de abas (TNotebook).
 */
class GerenciadorDisciplinaForm extends TPage
{
    protected $notebook;
    protected $painelDiario;
    protected $painelFrequencia;
    protected $painelNotas;
    protected $loaded;

    public function __construct($param)
    {
        parent::__construct();

        // 1. Resgata e valida parâmetros essenciais
        $codTurmaEtapa = $param['cod_turma']      ?? TSession::getValue('sessao_diarioclasse')["CodTurmaetapa"] ?? null;
        $codDisciplina = $param['cod_disciplina'] ?? TSession::getValue('sessao_diarioclasse')["CodDisciplina"] ?? null;
        $bimestre      = $param['bimestre']       ?? TSession::getValue('sessao_bimestre')["Bimestre"] ?? '1';
        $ano           = $param['ano']            ?? TSession::getValue('sessao_bimestre')["Ano"] ?? date('Y');

        if (empty($codTurmaEtapa) || empty($codDisciplina)) {
            new TMessage('error', 'Parâmetros da disciplina inválidos ou sessão expirada. Selecione novamente.');
            TApplication::loadPage('VwProfessordisciplinassemestreList');
            return;
        }

        $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse') ?? [];
        $nomeDisciplina = $sessao_diarioclasse["NomeDisciplina"] ?? 'Disciplina Selecionada';
        $identificacao  = $sessao_diarioclasse["Identificacao"] ?? 'Turma';
        
        $sessao_bimestre = TSession::getValue('sessao_bimestre') ?? [];
        $dataInicioRaw   = $sessao_bimestre["DataInicio"] ?? '';
        $dataFimRaw      = $sessao_bimestre["DataFim"] ?? '';
        $dataInicio      = (strpos($dataInicioRaw, '-') !== false) ? TDate::date2br($dataInicioRaw) : (!empty($dataInicioRaw) ? $dataInicioRaw : 'Não informada');
        $dataFim         = (strpos($dataFimRaw, '-') !== false)    ? TDate::date2br($dataFimRaw)    : (!empty($dataFimRaw) ? $dataFimRaw : 'Não informada');
        
        // 2. Estrutura Visual Base (VBox + Painel Informativo Superior)
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';

        $infoPanel = new TPanelGroup("<i class='fas fa-graduation-cap'></i> Painel de Gestão da Disciplina");
        $infoPanel->add("<div class='row' style='padding: 5px 15px;'>
                            <div class='col-md-5'>
                                <strong>Disciplina:</strong> {$nomeDisciplina} <br>
                                <strong>Turma:</strong> {$identificacao} (Cód: {$codTurmaEtapa})
                            </div>
                            <div class='col-md-4'>
                                <strong><i class='far fa-calendar-alt text-primary'></i> Período:</strong> {$bimestre}º Bimestre / {$ano}<br>
                                <strong><i class='far fa-clock text-warning'></i> Janela de Notas:</strong> de {$dataInicio} até {$dataFim}
                            </div>
                            <div class='col-md-3 text-right'>
                                <span class='label label-success' style='padding: 8px; font-size:12px; display:inline-block; border-radius:4px;'>Período de Lançamento Aberto</span>
                            </div>
                         </div>");
        $vbox->add($infoPanel);

        // 3. Inicialização das Abas
        $this->notebook = new TNotebook;
        $vbox->add($this->notebook);

        // Instancia os containers vazios que receberão os componentes
        $this->painelDiario     = new TVBox(['style' => 'width:100%; padding:15px;']);
        $this->painelNotas      = new TVBox(['style' => 'width:100%; padding:15px;']);

        $this->notebook->appendPage('Diário de Classe & Frequência', $this->painelDiario);
        $this->notebook->appendPage('Notas Avaliativas', $this->painelNotas);

        parent::add($vbox);
    }

    public function onReload($param = NULL)
    {
        try {
            // Força a limpeza estrutural lógica dos elementos filhos das abas
            $this->painelDiario->elements = [];
            $this->painelNotas->elements = [];

            // Instancia componentes limpos com base nos novos estados de sessão/parâmetros
            $diarioComponente = new GerenciadorDiarioFrequenciaComponent($param);
            $this->painelDiario->add($diarioComponente);

            $notasComponente = new GerenciadorNotasComponent($param);
            $this->painelNotas->add($notasComponente);

            $this->loaded = true;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload($_GET);
        }
        parent::show();
    }
}