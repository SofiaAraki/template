<?php

class ProgramaEnsinoDisciplinaList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // Colunas da Datagrid
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Professor', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_reg);

        $action_pdf = new TDataGridAction(array('ProgramaEnsinoDisciplinaList', 'onPrint'));
        $action_pdf->setButtonClass('btn btn-default btn-sm');
        $action_pdf->setLabel('Exportar PDF');
        $action_pdf->setImage('far:file-pdf red');
        $action_pdf->setField('id');
        $this->datagrid->addAction($action_pdf);

        $action_edit = new TDataGridAction(array('ProgramaEnsinoDisciplinaForm', 'onEdit'));
        $action_edit->setLabel('Editar');
        $action_edit->setImage('far:edit blue');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        
        $panel = new TPanelGroup('Listagem - Plano de Ensino');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        $panel->addHeaderActionLink('Cadastrar Novo', new TAction(array('ProgramaEnsinoDisciplinaForm', 'onClear')), 'fa:plus green');
        
        $container->add($panel);
        
        parent::add($container);
    }

    public static function onPrint($param)
    {
        try
        {
            if (empty($param['key'])) return;

            TTransaction::open('Felabs_DB');        
            $object = ProgramaEnsinoDisciplina::find($param['key']);

            if (!$object)
            {
                throw new Exception('Registro não encontrado.');
            }

            $object->data_reg = TDate::date2br($object->data_reg);
            $userName = new SystemUser($object->system_user_id);
            $object->system_user_id = $userName->name;

            // Busca o nome legível da disciplina na base legada
            TTransaction::open('dados_fei');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
            $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria);
            if (!empty($disciplinaNome)) {
                $object->disciplina = $disciplinaNome[0]->NomeDisciplina;
            }
            TTransaction::close();

            $widths = [230, 270];
            $tr = new TTableWriterHTML($widths);

            $tr->addStyle('title', 'Arial', '18', 'B', '#000000', '#f0f0f0'); 
            $tr->addStyle('header', 'Arial', '14', '', '#222222', '#f0f0f0'); 
            $tr->addStyle('info_label', 'Arial', '10', 'B', '#333333', '#ffffff'); 
            $tr->addStyle('info_value', 'Arial', '10', '', '#333333', '#ffffff'); 
            $tr->addStyle('th_header', 'Arial', '12', 'B', '#000000', '#f0f0f0');
            $tr->addStyle('data_cell', 'Arial', '11', '', '#333333', '#ffffff');
            $tr->addStyle('footer_cell', 'Arial', '10', '', '#222222', '#ffffff');

            $pathLogo = $_SERVER['DOCUMENT_ROOT'] . '/template/app/images/logo-fafram.png';
            $logoHtml = '';

            if (file_exists($pathLogo)) {
                $base64Image = 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogo));
                $logoHtml = "<img src='{$base64Image}' style='max-height: 55px; height: auto;' />";
            }

            $tr->addRow();
            $logoHtmlWithPadding = str_replace('<img ', '<img style="padding-left: 10px; max-height: 55px; height: auto;" ', $logoHtml);

            $headerHtml = '<table style="width: 100%; border: none !important; background: transparent !important; margin: 0; padding: 0; table-layout: fixed;">
                <tr>
                    <td style="width: 20%; border: none !important; text-align: left; vertical-align: middle; padding: 0;">' . $logoHtmlWithPadding . '</td>
                    <td style="width: 80%; border: none !important; text-align: center; vertical-align: middle; padding: 0;">
                        <span style="font-family: Arial; font-size: 18px; font-weight: bold; color: #000000;">FACULDADE DR. FRANCISCO MAEDA - FAFRAM</span><br>
                        <span style="font-family: Arial; font-size: 16px; font-weight: bold; color: #333333;">PLANO DE ENSINO</span>
                    </td>
                    <td style="width: 20%; border: none !important; padding: 0;"></td>
                </tr>
            </table>';

            $tr->addCell($headerHtml, 'left', 'title', 2);

            $tr->addRow();
            $tr->addCell("<b>Curso:</b> " . ($object->curso ?? ''), 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell("<b>Disciplina:</b> " . ($object->disciplina ?? ''), 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell("<b>Professor Responsável:</b> " . ($object->system_user_id ?? ''), 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell("<b>Código:</b> " . ($object->codigo ?? ''), 'left', 'info_value', 1);
            $tr->addCell("<b>Obrigatória/Optativa:</b> " . ($object->obg_optativa ?? ''), 'left', 'info_value', 1);

            $tr->addRow();
            $tr->addCell("<b>Pré-Requisitos:</b> " . ($object->pre_requisito ?? ''), 'left', 'info_value', 1);
            $tr->addCell("<b>Correquisitos:</b> " . ($object->co_requisito ?? ''), 'left', 'info_value', 1);

            $periodosMap = [
                'N' => 'Noturno',
                'I' => 'Integral',
                'M' => 'Manhã'
            ];
            
            $periodoExtenso = $periodosMap[strtoupper($object->periodo ?? '')] ?? ($object->periodo ?? '');

            $tr->addRow();
            $tr->addCell("<b>Período:</b> " . $periodoExtenso, 'left', 'info_value', 1);
            $tr->addCell("<b>Semestral/Anual:</b> " . ($object->semestral_anual ?? ''), 'left', 'info_value', 1);


            // Carga Horária
            $tr->addRow();
            $tr->addCell("Carga Horária", 'center', 'th_header', 2);
            
            $tr->addRow();
            $tr->addCell("Crédito: " . ($object->credito ?? '') . ' | Total: ' . ($object->total ?? '') . ' | Semanal: ' . ($object->semanal ?? '') .
                ' | Extensão: ' . ($object->extensao ?? ''), 'center', 'info_value', 2);
            
            $tr->addRow();
            $tr->addCell("Distribuição Carga Horária Semanal", 'center', 'th_header', 2);
            
            $tr->addRow();
            $tr->addCell("Teórica: " . ($object->teorica ?? '') . ' | Prática: ' . ($object->pratica ?? '') . ' | Teórica/Prática: ' . ($object->teorica_pratica ?? ''), 'center', 'info_value', 2);
            

            // Blocos Textuais Dinâmicos
            $blocosTextuais = [
                'Ementa:' => $object->ementa ?? '',
                'Objetivos:' => $object->objetivos ?? '',
                'Conteúdo Programático: ' => $object->conteudo_programatico ?? '',
                'Metodologia de Ensino:' => $object->metodologia ?? '',
                'Critérios de Avaliação da Aprendizagem:' => $object->criterio_avaliacao ?? '',
                'Recursos de Apoio:' => $object->recusos ?? '',
                'Bibliografia Básica:' => $object->bibliografia_basica ?? '',
                'Bibliografia Complementar:' => $object->bibliografia_complementar ?? ''
            ];

            foreach ($blocosTextuais as $titulo => $conteudo) {
                $tr->addRow();
                $tr->addCell($titulo, 'left', 'th_header', 2);

                $textoLimpo = !empty(trim((string)$conteudo)) 
                    ? nl2br(htmlspecialchars(strip_tags(html_entity_decode($conteudo))))
                    : '<span style="color: #888888; font-style: italic;">Não informado</span>';

                $tr->addRow();
                $tr->addCell($textoLimpo, 'left', 'data_cell', 2);
            }

            // Assinatura
            $tr->addRow();
            $assinaturaHtml = "<br>_________________________________________<br>" . "PROF. " .($object->system_user_id ?? '') . "</b>";
            $tr->addCell($assinaturaHtml, 'center', 'data_cell', 2);

            $tr->addRow();
            $tr->addCell("<i>Plano de Ensino gerado em: " . date('d/m/Y H:i:s') . "</i>", 'center', 'footer_cell', 2);


            $htmlPath = "app/output/programa_ensino_{$param['key']}.html";
            $pdfPath  = "app/output/programa_ensino_{$param['key']}.pdf";
            
            $tr->save($htmlPath);

            if (!file_exists($htmlPath))
            {
                throw new Exception('Não foi possível gerar o arquivo temporário do plano de ensino.');
            }

            $content = file_get_contents($htmlPath);
            
            $wrapStyle = '<style>
                            body { font-family: Arial, sans-serif; margin: 10px; }
                            table { border-collapse: collapse !important; table-layout: fixed !important; }
                            th, td { border: 1px solid #000000 !important; padding: 6px !important;
                                     word-wrap: break-word !important; overflow: hidden !important; }
                            .footer_cell, .footer_cell td, .footer_cell th { border: none !important; }
                           </style>';
            
            if (strpos($content, '</head>') !== false) {
                $content = str_replace('</head>', $wrapStyle . '</head>', $content);
            } else {
                $content = $wrapStyle . $content;
            }

            $colGroupTag = '<colgroup>
                                <col style="width: 50%;">
                                <col style="width: 50%;">
                            </colgroup>';
            
            $content = str_replace('<table>', '<table style="width: 100%; table-layout: fixed;">' . $colGroupTag, $content);

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($content);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            file_put_contents($pdfPath, $dompdf->output());

            $window = TWindow::create('Plano de Ensino', 0.8, 0.8);
            $element = new TElement('object');
            $element->data  = 'download.php?file=' . $pdfPath;
            $element->type  = 'application/pdf';
            $element->style = "width: 100%; height:calc(100% - 10px)";

            $window->add($element);
            $window->show();

            TTransaction::close();
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', 'Erro ao gerar o arquivo: ' . $e->getMessage());
        }
    }


    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_curso', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_disciplina', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_periodo', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_data_reg', NULL);

        if (isset($data->curso) && !empty($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_curso', $filter);
        }

        if (isset($data->disciplina) && !empty($data->disciplina)) {
            $filter = new TFilter('disciplina', 'like', "%{$data->disciplina}%");
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_disciplina', $filter);
        }

        if (isset($data->periodo) && !empty($data->periodo)) {
            $filter = new TFilter('periodo', 'like', "%{$data->periodo}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_periodo', $filter);
        }

        if (isset($data->data_reg) && !empty($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_data_reg', $filter);
        }

        $this->form->setData($data);
        TSession::setValue('ProgramaEnsinoDisciplina_filter_data', $data);
        
        $param = array();
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);    
            $loggedUnit = TSession::getValue('userunitid');
            
            $repository = new TRepository('ProgramaEnsinoDisciplina');
            $limit = 10;
           
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));

            // Traz apenas planos de ensino atuais
            $criteria->add(new TFilter('YEAR(data_reg)', '=', date('Y')));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            // Aplicação dos Filtros de Sessão
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_curso')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_curso')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_disciplina')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_disciplina')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_periodo')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_periodo')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_data_reg')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_data_reg')); 
            }

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                $codsFrente = array_filter(array_map(function($o) { return $o->disciplina; }, $objects));
                $nomesDisciplinas = [];

                if (!empty($codsFrente)) {
                    TTransaction::open('dados_fei');
                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', 'IN', $codsFrente));
                    $disciplinaNomesObjs = VwProfessordisciplinassemestre::getObjects($criteria2);
                    
                    if ($disciplinaNomesObjs) {
                        foreach ($disciplinaNomesObjs as $disObj) {
                            $nomesDisciplinas[$disObj->CodGradeDisciplinaEtapaFrente] = $disObj->NomeDisciplina;
                        }
                    }
                    
                    TTransaction::close();
                }

                // População final da grid
                foreach ($objects as $object)
                {
                    $object->disciplina = isset($nomesDisciplinas[$object->disciplina]) ? $nomesDisciplinas[$object->disciplina] : $object->disciplina;
                    $object->data_reg = TDate::date2br($object->data_reg);
                    
                    $this->datagrid->addItem($object);
                }
            }
            
            // Contagem para a paginação retornar dados precisos
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
                $this->onReload(func_get_arg(0));
            } else {
                $this->onReload();
            }
        }
        parent::show();
    }
}
