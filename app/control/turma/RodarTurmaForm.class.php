<?php

class RodarTurmaForm extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_rodar_turmas');
        $this->form->setFormTitle('Processamento em Lote: Rodar Turmas (Genesi → Adianti)');

        // Instancia os combos vinculados com as chaves e campos corretos do seu modelo FiTurmaEtapa
        // Exibirá no combo: "Identificacao - Ano"
        $turma_origem  = new TDBCombo('turma_origem_id', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', '{Identificacao} - {Ano}');
        $turma_destino = new TDBCombo('turma_destino_id', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', '{Identificacao} - {Ano}');

        $turma_origem->enableSearch();
        $turma_destino->enableSearch();

        $turma_origem->addValidation('Turma de Origem', new TRequiredValidator);
        $turma_destino->addValidation('Turma de Destino', new TRequiredValidator);

        $this->form->addFields( [new TLabel('Turma de Origem (Atual)')], [$turma_origem] );
        $this->form->addFields( [new TLabel('Turma de Destino (Nova)')], [$turma_destino] );

        $this->form->addAction('Rodar Turma', new TAction([$this, 'onProcessar']), 'fa:cog green');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    public function onProcessar($param)
    {
        try
        {
            $this->form->validate();
            $dadosForm = $this->form->getData();

            if ($dadosForm->turma_origem_id == $dadosForm->turma_destino_id)
            {
                throw new Exception('A turma de destino deve ser estritamente diferente da turma de origem.');
            }

            // Invoca o serviço com os IDs correspondentes
            $resultado = RodarTurmaService::executarTransicao($dadosForm->turma_origem_id, $dadosForm->turma_destino_id);

            $mensagem = "<b>Processamento Executado!</b><br><br>";
            $mensagem .= "• Matrículas criadas com sucesso: <span class='label label-success'>{$resultado['sucessos']}</span><br>";
            $mensagem .= "• Erros/Retenções identificadas: <span class='label label-danger'>" . count($resultado['erros']) . "</span>";

            if (!empty($resultado['erros']))
            {
                TSession::setValue('log_erros_rodar_turma', $resultado['erros']);
                
                // Exibe aviso na tela e redireciona direto para a Janela Pop-up de Erros ao fechar
                new TMessage('warning', $mensagem, new TAction(['RelatorioErrosTurmaWindow', 'onLoad']));
            }
            else
            {
                new TMessage('info', $mensagem);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}