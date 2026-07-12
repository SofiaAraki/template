<?php

class RelatorioErrosTurmaWindow extends TWindow
{
    protected $datagrid;

    public function __construct()
    {
        parent::__construct();
        parent::setSize(0.75, 480);
        parent::setTitle('Inconsistências Encontradas no Processamento');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%; padding: 15px;';

        $alerta = new TElement('div');
        $alerta->class = 'alert alert-warning';
        $alerta->add('<i class="fa fa-exclamation-triangle"></i> Atenção: Os alunos abaixo <b>NÃO</b> foram promovidos/movimentados de turma.');
        $vbox->add($alerta);

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // Colunas alinhadas com as propriedades do array gerado no Service
        $col_codigo = new TDataGridColumn('codigo', 'Cód. Aluno', 'center', '15%');
        $col_nome   = new TDataGridColumn('nome', 'Nome do Aluno', 'left', '45%');
        $col_motivo = new TDataGridColumn('motivo', 'Motivo da Ocorrência / Erro', 'left', '40%');

        $this->datagrid->addColumn($col_codigo);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_motivo);

        $vbox->add($this->datagrid);
        parent::add($vbox);
    }

    public function onLoad($param)
    {
        $erros = TSession::getValue('log_erros_rodar_turma');

        if (!empty($erros))
        {
            $this->datagrid->clear();
            foreach ($erros as $erro)
            {
                // Transforma o item de log em um StdClass para renderização limpa do componente
                $this->datagrid->addItem((object) $erro);
            }
        }
    }
}