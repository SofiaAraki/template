<?php

class AtividadeComplementarRelatorio extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;    

    public function __construct()
    {
        parent::__construct();
        
        // 1. Formulário de Filtro do Relatório
        $this->form = new BootstrapFormBuilder('form_relatorio_AtividadeComplementar');
        $this->form->setFormTitle('<h4>Relatório de Atividades Complementares por Aluno</h4>');
        
        // Campos para consulta
        $cod_aluno        = new TEntry('cod_aluno');
        $nome_aluno       = new TEntry('nome_aluno');
        $nome_curso       = new TEntry('nome_curso');
        $status_atividade = new TCombo('status_atividade');

        $status_atividade->addItems([
            'Aguardando aprovação' => 'Aguardando aprovação',
            'Aprovado'             => 'Aprovado',
            'Reprovado'            => 'Reprovado'
        ]);

        $this->form->addFields( [ new TLabel('Cód. Aluno') ], [ $cod_aluno ] );
        $this->form->addFields( [ new TLabel('Nome do Aluno') ], [ $nome_aluno ] );
        $this->form->addFields( [ new TLabel('Curso') ], [ $nome_curso ] );
        $this->form->addFields( [ new TLabel('Status') ], [ $status_atividade ] );

        $cod_aluno->setSize('30%');
        $nome_aluno->setSize('80%');
        $nome_curso->setSize('80%');
        $status_atividade->setSize('50%');
        
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
                
        // Ações do formulário
        $this->form->addAction('Gerar Relatório', new TAction([$this, 'onSearch']), 'fa:search blue');        
        $this->form->addAction('Imprimir Relatório', new TAction([$this, 'onPrint']), 'fa:print green');
        $this->form->addAction('Limpar Filtros', new TAction([$this, 'onClear']), 'fa:eraser red');

        // 2. DataGrid (Somente Leitura)
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->disableDefaultClick();

        // Agrupamento com resumo de horas postadas/analisadas
        $this->datagrid->setGroupColumn('nome_aluno', '<b>Aluno: {nome_aluno} ({cod_aluno}) | Cursando: {nome_curso} | PENDENTES: {CalcularHorasPendentes}h | APROVADAS: {CalcularHorasAprovadas}h</b>');

        // Colunas essenciais para o Coordenador identificar duplicidade
        $column_data_reg        = new TDataGridColumn('data_reg', 'Data Registro', 'center', '12%');
        $column_tipo_atividade  = new TDataGridColumn('tipo_atividade', 'Tipo de Atividade', 'left', '18%');
        $column_descricao       = new TDataGridColumn('descricao', 'Descrição / Detalhes da Postagem', 'left', '35%');
        $column_periodo         = new TDataGridColumn('periodo', 'Período Realizado', 'center', '15%');
        $column_carga_horaria   = new TDataGridColumn('carga_horaria', 'CH (h)', 'center', '8%');
        $column_status_atividade = new TDataGridColumn('status_atividade', 'Status', 'center', '12%');

        // Formatação de datas e status
        $column_data_reg->setTransformer(function($value) {
            return !empty($value) ? date('d/m/Y H:i', strtotime($value)) : '';
        });

        $column_status_atividade->setTransformer(array($this, 'setStatusColor'));

        // Adiciona colunas
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_tipo_atividade);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_periodo);
        $this->datagrid->addColumn($column_carga_horaria);
        $this->datagrid->addColumn($column_status_atividade);

        // Ação para baixar/visualizar o comprovante
        $action_download = new TDataGridAction([$this, 'onDownload']);
        $action_download->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default btn-sm');
        $action_download->setLabel('Comprovante');
        $action_download->setImage('fas:cloud-download-alt blue');
        $action_download->setField('id');

        $this->datagrid->addAction($action_download);

        // Modelo e Navegação
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    /**
     * Método para Imprimir o Relatório em uma Nova Aba com Visualização Aprimorada
     */
    public function onPrint($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $unit_id = TSession::getValue('userunitid');            
            $user_id = TSession::getValue('userid');                    
            $user    = new SystemUser($user_id);
            TTransaction::close();

            TTransaction::open('dados_fei');
            $repository_curso = new TRepository('FiCurso');
            $criteria_curso   = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            $cursos = $repository_curso->load($criteria_curso);
            $items = [];
            foreach($cursos as $curso) {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            $professor = new FiProfessor($user->systemuser_codlegado);
            TTransaction::close();

            // Busca os dados filtrados
            TTransaction::open('Felabs_DB');
            $repository = new TRepository('AtividadeComplementar');
            $criteria   = new TCriteria;
            $criteria->add(new TFilter('cod_prof_responsavel', '=', $professor->Codprofessor));
            
            if (!empty($items)) {
                $criteria->add(new TFilter('cod_curso', 'IN', $items)); 
            }

            // Aplica os filtros ativos na sessão
            if (TSession::getValue(__CLASS__.'_filter_cod_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cod_aluno')); 
            }
            if (TSession::getValue(__CLASS__.'_filter_nome_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_aluno')); 
            }
            if (TSession::getValue(__CLASS__.'_filter_nome_curso')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_curso')); 
            }
            if (TSession::getValue(__CLASS__.'_filter_status_atividade')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status_atividade')); 
            }

            $criteria->setProperty('order', 'nome_aluno, data_inicio');
            $criteria->setProperty('direction', 'desc');

            $objects = $repository->load($criteria, FALSE);

            // Montagem do HTML Modernizado
            $html  = "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Relatório de Atividades Complementares</title>";
            $html .= "<style>
                        @page { size: A4 portrait; margin: 12mm; }
                        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                        body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 11px; margin: 0; color: #2c3e50; background-color: #f8f9fa; }
                        .report-container { background: #fff; padding: 20px; border-radius: 4px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
                        h2 { text-align: center; margin-bottom: 4px; text-transform: uppercase; font-size: 18px; color: #1a252f; letter-spacing: 0.5px; }
                        p.subtitle { text-align: center; font-size: 11px; color: #7f8c8d; margin-top: 0; margin-bottom: 25px; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; page-break-inside: auto; background-color: #fff; }
                        tr { page-break-inside: avoid; page-break-after: auto; }
                        tr:nth-child(even) { background-color: #fcfcfc; }
                        tr:hover { background-color: #f1f5f9; }
                        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; vertical-align: middle; }
                        th { background-color: #f1f5f9; font-weight: 600; font-size: 10px; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; }
                        
                        /* Estilo do Grupo do Aluno */
                        .group-header { background: #e0f2fe; color: #0369a1; font-weight: bold; padding: 10px 12px; font-size: 12px; border: 1px solid #bae6fd; border-bottom: none; border-radius: 4px 4px 0 0; }
                        
                        /* Badges de Status */
                        .badge { display: inline-block; padding: 4px 10px; font-size: 10px; font-weight: 600; border-radius: 12px; text-align: center; white-space: nowrap; }
                        .badge-aprovado { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
                        .badge-pendente { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
                        .badge-reprovado { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

                        .center { text-align: center; }
                        .no-print { position: fixed; top: 15px; right: 20px; z-index: 999; }
                        .btn-print { padding: 9px 18px; cursor: pointer; background: #10b981; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: background 0.2s; }
                        .btn-print:hover { background: #059669; }

                        @media print {
                            body { background-color: #fff; }
                            .report-container { padding: 0; box-shadow: none; }
                            .no-print { display: none; }
                        }
                      </style>";
            $html .= "</head><body>";
            
            $html .= "<div class='no-print'>
                        <button class='btn-print' onclick='window.print()'>Imprimir Documento</button>
                      </div>";

            $html .= "<div class='report-container'>";
            $html .= "<h2>Relatório de Atividades Complementares</h2>";
            $html .= "<p class='subtitle'>Emissão: " . date('d/m/Y H:i') . "</p>";

            if ($objects)
            {
                $alunoAtual = null;

                foreach ($objects as $obj)
                {
                    if ($alunoAtual !== $obj->nome_aluno)
                    {
                        if ($alunoAtual !== null) {
                            $html .= "</tbody></table>";
                        }

                        $alunoAtual = $obj->nome_aluno;
                        $pendentes  = $obj->get_CalcularHorasPendentes();
                        $aprovadas  = $obj->get_CalcularHorasAprovadas();

                        $html .= "<div class='group-header'>";
                        $html .= "Aluno: {$obj->nome_aluno} ({$obj->cod_aluno}) | Curso: {$obj->nome_curso} | Horas Pendentes: <b>{$pendentes}h</b> | Horas Aprovadas: <b>{$aprovadas}h</b>";
                        $html .= "</div>";

                        $html .= "<table>
                                    <thead>
                                        <tr>
                                            <th width='12%' class='center'>Registrado em</th>
                                            <th width='20%'>Tipo de Atividade</th>
                                            <th width='38%'>Descrição</th>
                                            <th width='15%' class='center'>Período</th>
                                            <th width='5%' class='center'>CH</th>
                                            <th width='10%' class='center'>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                    }

                    $dt_reg    = !empty($obj->data_reg) ? date('d/m/Y H:i', strtotime($obj->data_reg)) : '-';
                    $dt_inicio = !empty($obj->data_inicio) ? TDate::date2br($obj->data_inicio) : '-';
                    $dt_fim    = !empty($obj->data_termino) ? TDate::date2br($obj->data_termino) : '-';
                    $ch        = (int) $obj->carga_horaria;

                    // Definição de Cores do Status (Badges)
                    $statusStr = $obj->status_atividade;
                    $badgeClass = 'badge-pendente';
                    
                    if ($statusStr == 'Aprovado') {
                        $badgeClass = 'badge-aprovado';
                    } elseif ($statusStr == 'Reprovado') {
                        $badgeClass = 'badge-reprovado';
                    }

                    $statusHtml = "<span class='badge {$badgeClass}'>{$statusStr}</span>";

                    $html .= "<tr>";
                    $html .= "<td class='center' style='color:#64748b;'>{$dt_reg}</td>";
                    $html .= "<td>{$obj->tipo_atividade}</td>";
                    $html .= "<td>{$obj->descricao}</td>";
                    $html .= "<td class='center' style='color:#64748b;'>{$dt_inicio} a {$dt_fim}</td>";
                    $html .= "<td class='center'><b>{$ch}h</b></td>";
                    $html .= "<td class='center'>{$statusHtml}</td>";
                    $html .= "</tr>";
                }

                $html .= "</tbody></table>";
            }
            else
            {
                $html .= "<p style='text-align:center;'>Nenhum registro encontrado para os filtros informados.</p>";
            }

            $html .= "</div>"; // fecha report-container

            $html .= "</body></html>";

            TTransaction::close();

            // Salva em arquivo temporário na pasta app/output
            $file = 'app/output/relatorio_atividades_' . uniqid() . '.html';
            file_put_contents($file, $html);

            // Abre o arquivo gerado numa nova aba usando JS
            TScript::create("window.open('{$file}', '_blank');");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function setStatusColor($column_status_atividade, $object, $row)
    {
        $status = $object->status_atividade;
        
        if($status == "Aguardando aprovação") {
            return '<span class="label label-warning" style="font-size:90%;">' . $status . '</span>';
        } elseif($status == "Aprovado") {
            return '<span class="label label-success" style="font-size:90%;">' . $status . '</span>';
        } elseif($status == "Reprovado") {
            return '<span class="label label-danger" style="font-size:90%;">' . $status . '</span>';
        }
        
        return $status;
    }

    public static function onDownload($param)
    {
        try
        {
            $id = $param['id'];
            TTransaction::open('Felabs_DB');

            $object = new AtividadeComplementar($id);

            if (strtolower(substr($object->arquivo, -4)) == 'html') {
                $win = TWindow::create('Comprovante', 0.8, 0.8);
                $win->add(file_get_contents($object->caminho_arquivo . '/' . $object->arquivo));
                $win->show();
            } else {
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

    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue(__CLASS__.'_filter_cod_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome_curso', NULL);
        TSession::setValue(__CLASS__.'_filter_status_atividade', NULL);

        if (isset($data->cod_aluno) AND ($data->cod_aluno)) {
            $filter = new TFilter('cod_aluno', '=', $data->cod_aluno);
            TSession::setValue(__CLASS__.'_filter_cod_aluno', $filter); 
        }

        if (isset($data->nome_aluno) AND ($data->nome_aluno)) {
            $filter = new TFilter('nome_aluno', 'like', "%{$data->nome_aluno}%");
            TSession::setValue(__CLASS__.'_filter_nome_aluno', $filter); 
        }

        if (isset($data->nome_curso) AND ($data->nome_curso)) {
            $filter = new TFilter('nome_curso', 'like', "%{$data->nome_curso}%"); 
            TSession::setValue(__CLASS__.'_filter_nome_curso', $filter); 
        }

        if (isset($data->status_atividade) AND ($data->status_atividade)) {
            $filter = new TFilter('status_atividade', '=', $data->status_atividade);
            TSession::setValue(__CLASS__.'_filter_status_atividade', $filter); 
        }

        $this->form->setData($data);
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }

    public function onClear()
    {
        $this->form->clear();
        TSession::setValue(__CLASS__.'_filter_cod_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome_curso', NULL);
        TSession::setValue(__CLASS__.'_filter_status_atividade', NULL);
        TSession::setValue(__CLASS__ . '_filter_data', NULL);
        
        $this->onReload();
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');  
            $unit_id = TSession::getValue('userunitid');            
            $user_id = TSession::getValue('userid');                    
            $user    = new SystemUser($user_id);
            TTransaction::close();
            
            TTransaction::open('dados_fei');
            $repository_curso = new TRepository('FiCurso');
            $criteria_curso   = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $cursos = $repository_curso->load($criteria_curso);
            $items = [];
            foreach($cursos as $curso) {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            
            $professor = new FiProfessor($user->systemuser_codlegado);
            TTransaction::close();
            
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('AtividadeComplementar');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_prof_responsavel', '=', $professor->Codprofessor));
            if (!empty($items)) {
                $criteria->add(new TFilter('cod_curso', 'IN', $items)); 
            }

            if (empty($param['order'])) {
                $param['order']     = 'nome_aluno, data_inicio';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue(__CLASS__.'_filter_cod_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cod_aluno')); 
            }
            if (TSession::getValue(__CLASS__.'_filter_nome_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_aluno')); 
            }
            if (TSession::getValue(__CLASS__.'_filter_nome_curso')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_curso')); 
            }
            if (TSession::getValue(__CLASS__.'_filter_status_atividade')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status_atividade')); 
            }

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->carga_horaria = (int) $object->carga_horaria;
                    
                    $dt_inicio = !empty($object->data_inicio) ? TDate::date2br($object->data_inicio) : '-';
                    $dt_fim    = !empty($object->data_termino) ? TDate::date2br($object->data_termino) : '-';
                    $object->periodo = "{$dt_inicio} a {$dt_fim}";

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
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'], array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0) {
                $this->onReload( func_get_arg(0) );
            } else {
                $this->onReload();
            }
        }
        
        parent::show();
    }
}