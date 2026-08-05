<?php

class RequerimentoFinanceiroAlunoView extends TPage
{
    private $datagridServicos;
    private $datagridSolicitados;

    public function __construct()
    {
        parent::__construct();

        $vbox = new TVBox;
        $vbox->style = 'width: 100%; padding: 20px;';

        // =========================================================================
        // 1. PRIMEIRO: CATÁLOGO DE SERVIÇOS DISPONÍVEIS PARA SOLICITAÇÃO
        // =========================================================================
        $panelServicos = new TPanelGroup('Serviços Disponíveis');
        $panelServicos->panel_color = 'default';
        
        $this->datagridServicos = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagridServicos->width = '100%';

        $col_nome  = new TDataGridColumn('nome', 'Requerimento', 'left');
        $col_preco = new TDataGridColumn('valor', 'Valor', 'right');

        $this->datagridServicos->addColumn($col_nome);
        $this->datagridServicos->addColumn($col_preco);

        // Ação para Requisitar um serviço
        $action_requisitar = new TDataGridAction([$this, 'onAbrirModalOrientacao']);
        $action_requisitar->setFields(['id', 'nome', 'valor', 'prazo', 'orientacao']);
        $action_requisitar->setLabel('Requisitar');
        $action_requisitar->setImage('fa:plus-circle #337ab7');
        $this->datagridServicos->addAction($action_requisitar);

        $this->datagridServicos->createModel();
        $panelServicos->add($this->datagridServicos);

        // =========================================================================
        // 2. DEPOIS: HISTÓRICO DE REQUERIMENTOS SOLICITADOS
        // =========================================================================
        $panelSolicitados = new TPanelGroup('Requerimentos solicitados');
        $panelSolicitados->panel_color = 'primary';
        
        $this->datagridSolicitados = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagridSolicitados->width = '100%';

        $col_pagamento = new TDataGridColumn('status_pagamento', 'Pagamento', 'center');
        $col_servico   = new TDataGridColumn('requerimento', 'Requerimento', 'left');
        $col_protocolo = new TDataGridColumn('protocolo', 'Protocolo', 'center');
        $col_data      = new TDataGridColumn('data_solicitacao', 'Data solicitação', 'center');
        $col_valor     = new TDataGridColumn('valor', 'Valor', 'right');
        $col_doc       = new TDataGridColumn('arquivo_virtual', 'Documento virtual', 'center');

        // Formatação visual do Badge de Pagamento
        $col_pagamento->setTransformer(function($val) {
            if ($val === 'Pago') {
                return '<span class="label label-success" style="background-color: #5cb85c; color: white; padding: 4px 8px; border-radius: 4px;">Pago</span>';
            }
            return '<span class="label label-warning" style="background-color: #f0ad4e; color: white; padding: 4px 8px; border-radius: 4px;">Pendente</span>';
        });

        // Formatação da coluna do Documento Virtual
        $col_doc->setTransformer(function($val, $object) {
            if ($object->status_pagamento === 'Pago' && !empty($val)) {
                $action = new TAction([__CLASS__, 'onSimularDownload'], ['arquivo' => $val]);
                return new TActionLink('Fazer o Download', $action, '#337ab7', null, null, 'fa:download');
            } elseif ($object->status_pagamento === 'Pago') {
                return '<span style="color: #777;">O arquivo ainda não foi postado pela secretaria.</span>';
            }
            return '<span style="color: #d9534f;">Aguardando Pagamento</span>';
        });

        $this->datagridSolicitados->addColumn($col_pagamento);
        $this->datagridSolicitados->addColumn($col_servico);
        $this->datagridSolicitados->addColumn($col_protocolo);
        $this->datagridSolicitados->addColumn($col_data);
        $this->datagridSolicitados->addColumn($col_valor);
        $this->datagridSolicitados->addColumn($col_doc);

        // Ação para Pagar requerimentos pendentes
        $action_pagar = new TDataGridAction([$this, 'onAbrirModalPagamento'], ['id' => '{id}', 'requerimento' => '{requerimento}']);
        $action_pagar->setLabel('Pagar');
        $action_pagar->setImage('fa:credit-card #5cb85c');
        $this->datagridSolicitados->addAction($action_pagar);

        $this->datagridSolicitados->createModel();
        $panelSolicitados->add($this->datagridSolicitados);

        // Adiciona ao container principal na ordem correta
        $vbox->add($panelServicos);
        $vbox->add($panelSolicitados);

        parent::add($vbox);
    }

    /**
     * Carrega os dados simulados mantendo o padrão do Adianti
     */
    public function onReload()
    {
        // 1. Tabela de Serviços Disponíveis (Preços da FAFRAM)
        $servicos = [
            (object) ['id' => 101, 'nome' => 'Declarações / Atestados', 'valor' => 'R$ 5,00', 'prazo' => '2 dias úteis', 'orientacao' => 'Declaração padrão de vínculo ou matrícula.'],
            (object) ['id' => 102, 'nome' => '2ª Via Diploma', 'valor' => 'R$ 500,00', 'prazo' => '90 dias úteis', 'orientacao' => 'O requerimento só será processado após confirmação do pagamento e envio do comprovante.'],
            (object) ['id' => 103, 'nome' => 'Apostilamento de Diploma', 'valor' => 'R$ 300,00', 'prazo' => '30 dias úteis', 'orientacao' => 'Alteração ou inclusão de dados no diploma expedido.'],
            (object) ['id' => 104, 'nome' => 'Impressão do Diploma', 'valor' => 'R$ 280,00', 'prazo' => '15 dias úteis', 'orientacao' => 'Segunda via física do documento de conclusão.'],
            (object) ['id' => 105, 'nome' => '2ª Via Histórico / Histórico Parcial', 'valor' => 'R$ 50,00', 'prazo' => '5 dias úteis', 'orientacao' => 'Histórico escolar com as disciplinas concluídas.'],
            (object) ['id' => 106, 'nome' => '2ª Via Certificados em Geral', 'valor' => 'R$ 20,00', 'prazo' => '5 dias úteis', 'orientacao' => 'Certificados de eventos, extensão ou palestras.'],
            (object) ['id' => 107, 'nome' => 'Plano de Ensino por Disciplina', 'valor' => 'R$ 15,00 (por disc.)', 'prazo' => '10 dias úteis', 'orientacao' => 'Conteúdo programático detalhado da disciplina.'],
            (object) ['id' => 108, 'nome' => 'Provas Substitutivas', 'valor' => 'R$ 100,00 (por disc.)', 'prazo' => 'Imediato', 'orientacao' => 'Inscrição para realização de avaliação substitutiva.'],
            (object) ['id' => 109, 'nome' => 'Provas Substitutivas EAD', 'valor' => 'R$ 50,00 (por disc.)', 'prazo' => 'Imediato', 'orientacao' => 'Inscrição para avaliação substitutiva das disciplinas EAD.']
        ];

        $this->datagridServicos->clear();
        foreach ($servicos as $servico) {
            $this->datagridServicos->addItem($servico);
        }

        // 2. Histórico de Solicitações do Aluno
        $solicitados = [
            (object) [
                'id' => 1, 
                'status_pagamento' => 'Pago', 
                'requerimento' => 'Conteúdo programático (virtual)', 
                'protocolo' => '5213034', 
                'data_solicitacao' => '02/02/2026 17:10', 
                'valor' => 'R$ 30,00', 
                'arquivo_virtual' => 'conteudo_programatico_5213034.pdf'
            ],
            (object) [
                'id' => 2, 
                'status_pagamento' => 'Pago', 
                'requerimento' => '2ª Via do Histórico escolar final / Histórico escolar parcial', 
                'protocolo' => '5210741', 
                'data_solicitacao' => '28/01/2026 14:07', 
                'valor' => 'R$ 50,00',
                'arquivo_virtual' => ''
            ],
            (object) [
                'id' => 3, 
                'status_pagamento' => 'Pago', 
                'requerimento' => 'Matriz Curricular', 
                'protocolo' => '5208440', 
                'data_solicitacao' => '23/01/2026 12:17', 
                'valor' => 'Gratuito', 
                'arquivo_virtual' => 'matriz_curricular.pdf'
            ],
            (object) [
                'id' => 4, 
                'status_pagamento' => 'Pendente', 
                'requerimento' => 'Declaração especial (virtual)', 
                'protocolo' => '5192797', 
                'data_solicitacao' => '04/11/2025 21:16', 
                'valor' => 'R$ 5,00',
                'arquivo_virtual' => ''
            ]
        ];

        $this->datagridSolicitados->clear();
        foreach ($solicitados as $item) {
            $this->datagridSolicitados->addItem($item);
        }
    }

    /**
     * Modal de Orientação ao Requisitante
     */
    public static function onAbrirModalOrientacao($param)
    {
        $window = TWindow::create("Orientações - " . $param['nome'], 0.50, null);

        $html = "
            <div style='padding: 10px; font-family: sans-serif;'>
                <h3 style='color: #2c3e50; text-align: center; font-weight: bold;'>{$param['nome']}</h3>
                <hr>
                <p><strong>Prazo:</strong> Pronto em até {$param['prazo']}</p>
                <p><strong>Valor:</strong> {$param['valor']}</p>
                <br>
                <h5 style='font-weight: bold; color: #333;'>Orientação ao requisitante:</h5>
                <p style='color: #555;'>{$param['orientacao']}</p>
                
                <div style='background-color: #f9f9f9; border-left: 4px solid #337ab7; padding: 10px; margin-top: 15px;'>
                    <strong>Para o requerimento ser protocolado siga os passos abaixo:</strong>
                    <ul style='margin-top: 8px; margin-left: 15px;'>
                        <li>Clique no botão <b>'Confirmar Requisição'</b> abaixo.</li>
                        <li>Imprima e pague o boleto ou utilize a chave PIX enviada.</li>
                        <li style='color: #d9534f;'><b>Lembrando:</b> O prazo somente será contado a partir da confirmação do pagamento.</li>
                    </ul>
                </div>
            </div>
        ";

        $btnConfirmar = new TButton('btn_confirmar');
        $btnConfirmar->setLabel('Requisitar');
        $btnConfirmar->setImage('fa:check white');
        $btnConfirmar->style = 'background-color: #337ab7; color: white; width: 100%; padding: 10px; margin-top: 15px; font-size: 16px; border: none; border-radius: 4px;';
        $btnConfirmar->setAction(new TAction([__CLASS__, 'onSimularRequisicao'], ['nome' => $param['nome']]));

        $vbox = new TVBox;
        $vbox->style = 'width: 100%;';
        $vbox->add($html);
        $vbox->add($btnConfirmar);

        $window->add($vbox);
        $window->show();
    }

    /**
     * Modal de Opções de Pagamento
     */
    public static function onAbrirModalPagamento($param)
    {
        $window = TWindow::create('Pagamento do Requerimento', 0.45, null);

        $html = "
            <div style='background-color: #eee; padding: 15px; text-align: center; border-radius: 4px; margin-bottom: 20px;'>
                <h4 style='margin: 0; color: #333;'>Selecione o meio de pagamento para:</h4>
                <strong style='color: #337ab7;'>{$param['requerimento']}</strong>
            </div>
        ";

        $btnBoleto = new TButton('btn_boleto');
        $btnBoleto->setLabel('Gerar Boleto / PIX');
        $btnBoleto->setImage('fa:barcode white');
        $btnBoleto->style = 'background-color: #1b9e77; color: white; width: 100%; padding: 12px; margin-bottom: 10px; border-radius: 4px; border: none; font-size: 14px;';
        $btnBoleto->setAction(new TAction([__CLASS__, 'onSimularAcaoPagamento'], ['tipo' => 'Boleto/PIX']));

        $btnCartao = new TButton('btn_cartao');
        $btnCartao->setLabel('Cartão de Crédito');
        $btnCartao->setImage('fa:credit-card white');
        $btnCartao->style = 'background-color: #11a579; color: white; width: 100%; padding: 12px; border-radius: 4px; border: none; font-size: 14px;';
        $btnCartao->setAction(new TAction([__CLASS__, 'onSimularAcaoPagamento'], ['tipo' => 'Cartão de Crédito']));

        $vbox = new TVBox;
        $vbox->style = 'width: 100%; padding: 10px;';
        $vbox->add($html);
        $vbox->add($btnBoleto);
        $vbox->add($btnCartao);

        $window->add($vbox);
        $window->show();
    }

    // =========================================================================
    // MÉTODOS DE SIMULAÇÃO DE AÇÕES
    // =========================================================================

    public static function onSimularRequisicao($param)
    {
        TWindow::closeWindow();
        new TMessage('info', "O serviço <b>'{$param['nome']}'</b> foi requisitado com sucesso! (Modo de Demonstração Visual)");
    }

    public static function onSimularAcaoPagamento($param)
    {
        TWindow::closeWindow();
        new TToast("Iniciando pagamento via <b>{$param['tipo']}</b>... (Modo Visual)", 'top right', 'info');
    }

    public static function onSimularDownload($param)
    {
        new TToast("Download simulado do arquivo: <b>{$param['arquivo']}</b>", 'bottom right', 'success');
    }

    public function show()
    {
        $this->onReload();
        parent::show();
    }
}