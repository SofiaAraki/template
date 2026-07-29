<?php

class RelatorioLancamentoNotas extends TPage
{
    private $form;
    private $datagrid;

    private $curso;
    private $periodo;

    const CURSOS = [
        10  => 'ADMINISTRAÇÃO',
        146 => 'BIOMEDICINA',
        62  => 'CIÊNCIAS CONTÁBEIS',
        16  => 'DIREITO',
        70  => 'ENFERMAGEM',
        15  => 'ENGENHARIA AGRONÔMICA',
        69  => 'ENGENHARIA CIVIL',
        68  => 'ENGENHARIA DE PRODUÇÃO',
        104 => 'ENGENHARIA ELÉTRICA',
        67  => 'ENGENHARIA MECÂNICA',
        20  => 'MEDICINA VETERINÁRIA',
        6   => 'PEDAGOGIA',
        147 => 'PSICOLOGIA',
        21  => 'SISTEMAS DE INFORMAÇÃO'
    ];

    const PERIODOS = [
        'I' => 'INTEGRAL',
        'N' => 'NOTURNO'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_relatorio');
        $this->form->setFormTitle('Relatório - Lançamento de Notas');
        $this->form->generateAria();

        $this->curso = new TCombo('CodCurso');
        $this->curso->setSize('40%');
        $this->curso->addItems(self::CURSOS);
        $this->curso->addValidation('Curso', new TRequiredValidator);

        $this->periodo = new TCombo('Periodo');
        $this->periodo->setSize('40%');
        $this->periodo->addItems(self::PERIODOS);
        $this->periodo->addValidation('Período', new TRequiredValidator);

        $this->form->addFields([new TLabel('Curso')], [$this->curso]);
        $this->form->addFields([new TLabel('Período')], [$this->periodo]);

        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        // DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        $this->datagrid->disableDefaultClick();

        $this->datagrid->setGroupColumn('Etapa', '<b>{Etapa}º Ciclo</b>');

        $discColumn = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left', '35%');

        $profColumn = new TDataGridColumn('NomeProfessor', 'Professor', 'left', '35%');

        $lancamentoNotasColumn = new TDataGridColumn('', 'Notas lançadas', 'center', '30%');

        $lancamentoNotasColumn->setTransformer(function ($value, $object) {

            $badge = function ($valor) {
                return $valor == 'SIM'
                    ? '<span class="label label-success">SIM</span>'
                    : '<span class="label label-danger">NÃO</span>';
            };

            return "1º Bim: {$badge($object->Nota_1_Bimestre)}
                &nbsp; 2º Bim: {$badge($object->Nota_2_Bimestre)}";
        });

        $this->datagrid->addColumn($discColumn);
        $this->datagrid->addColumn($profColumn);
        $this->datagrid->addColumn($lancamentoNotasColumn);

        $this->datagrid->createModel();

        $vbox = new TVBox();
        $vbox->style = 'width:100%';
        $vbox->add($this->form);

        $panel = new TPanelGroup('Disciplinas');

        $pdfButton = new TButton('pdf');
        $pdfButton->setAction(
            new TAction([$this, 'onGeneratePDF']),
            'Exportar'
        );
        $pdfButton->setImage('fas:file-pdf red');

        $this->form->setFields(array_merge(
            $this->form->getFields(),
            [$pdfButton]
        ));

        $panel->addHeaderWidget($pdfButton);
        $panel->add($this->datagrid);
        
        $vbox->add($panel);

        parent::add($vbox);
    }


    public function onReload($param = null)
    {
        try {

            TTransaction::open('dados_fei');

            $this->datagrid->clear();

            $data = $this->form->getData();

            $this->form->setData($data);

            $repository = new TRepository('VwRelatorioLancamentoNotas');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('Ano', '=', date('Y')));
            $criteria->add(new TFilter('Semestre', '=', self::getSemestre()));
            $criteria->add(new TFilter('CodCurso', '=', $data->CodCurso));
            $criteria->add(new TFilter('Periodo', '=', $data->Periodo));

            $criteria->setProperty('order', 'Etapa, NomeDisciplina');

            $objects = $repository->load($criteria);

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }
            else
            {
                new TMessage('warning', 'Nenhum registro encontrado.');
            }

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

   public function onSearch($param = null)
    {
        try {
            $this->form->validate();
            $this->onReload();
        } catch (Exception $e) {
            new TMessage('warning', $e->getMessage());
        }
    }

    public function onClear()
    {
        $this->form->clear(true);
        $this->datagrid->clear();
    }

    private static function getSemestre()
    {
        return date('n') <= 6 ? 1 : 2;
    }

    public function onGeneratePDF($param)
    {
        try
        {
            TTransaction::open('dados_fei');

            $data = $this->form->getData();

            if (empty($data->CodCurso) || empty($data->Periodo))
            {
                throw new Exception('Selecione o curso e o período antes de gerar o PDF.');
            }

            $repository = new TRepository('VwRelatorioLancamentoNotas');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('Ano', '=', date('Y')));
            $criteria->add(new TFilter('Semestre', '=', self::getSemestre()));
            $criteria->add(new TFilter('CodCurso', '=', $data->CodCurso));
            $criteria->add(new TFilter('Periodo', '=', $data->Periodo));
            $criteria->setProperty('order', 'Etapa, NomeDisciplina');

            $objects = $repository->load($criteria);

            if (!$objects)
            {
                new TMessage('warning', 'Nenhum registro encontrado.');
                return;
            }

            $widths = [160, 240, 90];
            $tr = new TTableWriterHTML($widths);

            $tr->addStyle('title', 'Arial', '14', 'B', '#000000', '#f0f0f0'); 
            $tr->addStyle('header', 'Arial', '12', '', '#222222', '#f0f0f0'); 
            $tr->addStyle('info_label', 'Arial', '10', 'B', '#333333', '#ffffff'); 
            $tr->addStyle('info_value', 'Arial', '10', '', '#333333', '#ffffff'); 
            $tr->addStyle('group_header', 'Arial', '11', 'B', '#000000', '#e0e0e0');
            $tr->addStyle('th_header', 'Arial', '11', 'B', '#000000', '#f0f0f0');
            $tr->addStyle('data_cell', 'Arial', '10', '', '#333333', '#ffffff');
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
                    <td style="width: 25%; border: none !important; text-align: left; vertical-align: middle; padding: 0;">' . $logoHtmlWithPadding . '</td>
                    <td style="width: 50%; border: none !important; text-align: center; vertical-align: middle; padding: 0;">
                        <span style="font-family: Arial; font-size: 16px; font-weight: bold; color: #000000;">RELATÓRIO - LANÇAMENTO DE NOTAS</span>
                    </td>
                    <td style="width: 25%; border: none !important; padding: 0;"></td>
                </tr>
            </table>';

            $tr->addCell($headerHtml, 'left', 'title', 3);

            $object = $objects[0];
            $nomeCurso = self::CURSOS[$data->CodCurso] ?? $object->NomeCurso;
            $nomePeriodo = self::PERIODOS[$data->Periodo] ?? $object->Periodo;

            $tr->addRow();
            $tr->addCell("Curso:", 'right', 'info_label', 1);
            $tr->addCell($nomeCurso, 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell("Período:", 'right', 'info_label', 1);
            $tr->addCell($nomePeriodo, 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell("Ano / Semestre:", 'right', 'info_label', 1);
            $tr->addCell(date('Y') . ' / ' . self::getSemestre() . 'º Semestre', 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell('Disciplina', 'center', 'th_header', 1);
            $tr->addCell('Professor', 'center', 'th_header', 1);
            $tr->addCell('Notas lançadas', 'center', 'th_header', 1);

            $currentEtapa = null;

            foreach ($objects as $item)
            {
                if ($currentEtapa !== $item->Etapa) {
                    $currentEtapa = $item->Etapa;
                    $tr->addRow();
                    $tr->addCell("<b>{$currentEtapa}º Ciclo</b>", 'left', 'group_header', 3);
                }

                $disciplina = htmlspecialchars($item->NomeDisciplina ?? '');
                $professor = mb_strtoupper($item->NomeProfessor ?? '', 'UTF-8');

                $formatStatus = function ($valor) {
                    if ($valor == 'SIM') {
                        return 'Sim';
                    }
                    return '<span style="background-color: #e74c3c; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 12px;">Não</span>';
                };

                $nota1 = $formatStatus($item->Nota_1_Bimestre ?? '');
                $nota2 = $formatStatus($item->Nota_2_Bimestre ?? '');

                $frequenciaTexto = "1º Bim: {$nota1} <br> 2º Bim: {$nota2}";
                
                $tr->addRow();
                $tr->addCell($disciplina, 'left', 'data_cell');
                $tr->addCell($professor, 'left', 'data_cell');
                $tr->addCell($frequenciaTexto, 'center', 'data_cell');
            }

            $tr->addRow();
            $tr->addCell("Relatório gerado em: " . date('d/m/Y H:i:s'), 'center', 'footer_cell', 3);

            $htmlPath = "app/output/relatorio_lancamento_notas.html";
            $pdfPath  = "app/output/relatorio_lancamento_notas.pdf";
            
            $tr->save($htmlPath);

            if (!file_exists($htmlPath))
            {
                throw new Exception('Não foi possível gerar o arquivo temporário do relatório.');
            }

            $content = file_get_contents($htmlPath);
            
            $wrapStyle = '<style>
                            body { font-family: Arial, sans-serif; margin: 10px; }
                            table { border-collapse: collapse !important; table-layout: fixed !important; }
                            th, td { border: 1px solid #cccccc !important; padding: 6px !important;
                                        word-wrap: break-word !important; overflow: hidden !important; }
                         </style>';
            
            if (strpos($content, '</head>') !== false) {
                $content = str_replace('</head>', $wrapStyle . '</head>', $content);
            } else {
                $content = $wrapStyle . $content;
            }

            $content = str_replace('<table>', '<table style="width: 100%; table-layout: fixed;">', $content);

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($content);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            file_put_contents($pdfPath, $dompdf->output());

            parent::openFile($pdfPath);
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', 'Erro ao gerar o relatório: ' . $e->getMessage());
        }
        finally
        {
            TTransaction::close();
        }
    }
}