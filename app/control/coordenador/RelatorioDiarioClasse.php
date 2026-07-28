<?php

class RelatorioDiarioClasse extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    private $curso;
    private $professor;
    private $disciplina;

    const CURSOS = [
        10  => 'ADMINISTRAÇÃO',
        146 => 'BIOMEDICINA',
        62  => 'CIÊNCIAS CONTÁBEIS',
        16  => 'DIREITO',
        70  => 'ENFERMAGEM',
        15  => 'ENGENHARIA AGRONÔMICA',
        69 => 'ENGENHARIA CIVIL',
        68  => 'ENGENHARIA DE PRODUÇÃO',
        104 => 'ENGENHARIA ELÉTRICA',
        67 => 'ENGENHARIA MECÂNICA',
        20  => 'MEDICINA VETERINÁRIA',
        6  => 'PEDAGOGIA',
        147 => 'PSICOLOGIA',
        21  => 'SISTEMAS DE INFORMAÇÃO'
    ];


    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_relatorio');
        $this->form->setFormTitle('Relatório - Diário de Classe');
        $this->form->generateAria();

        $this->curso = new TCombo('CodCurso');
        $this->curso->setSize('40%');
        $this->curso->addItems(self::CURSOS);
        $this->curso->addValidation('Curso', new TRequiredValidator);
        $this->curso->setChangeAction(new TAction([$this, 'onChangeCurso']));


        $this->professor = new TCombo('CodProfessor');
        $this->professor->setSize('40%');
        $this->professor->addValidation('Professor', new TRequiredValidator);
        $this->professor->setChangeAction(new TAction([$this, 'onChangeProfessor']));

        $this->disciplina = new TCombo('disc');
        $this->disciplina->setSize('40%');
        $this->disciplina->addValidation('Disciplina', new TRequiredValidator);

        $this->form->addFields([new TLabel('Curso')], [$this->curso]);
        $this->form->addFields([new TLabel('Professor')], [$this->professor]);
        $this->form->addFields([new TLabel('Disciplina')], [$this->disciplina]);

        $this->form->addAction('Buscar', new TAction([$this,'onSearch']), 'fa:search blue');
        $this->form->addAction('Limpar', new TAction([$this,'onClear']), 'fa:eraser red');

        // Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        $this->datagrid->disableDefaultClick();


        $dataColumn = new TDataGridColumn('Data','Data da aula','center','15%');

        $dataColumn->setTransformer(function($value)
        {
            return !empty($value) ? TDate::date2br($value) : '';
        });

        $this->datagrid->addColumn($dataColumn);


        $conteudoColumn = new TDataGridColumn('conteudo','Conteúdo','left','60%');

        $conteudoColumn->setTransformer(function($value)
        {
            if (empty(trim($value)))
            {
                return '<span class="label label-danger">
                            Conteúdo não registrado
                        </span>';
            }
            return nl2br($value);
        });

        $this->datagrid->addColumn($conteudoColumn);


        $frequenciaColumn = new TDataGridColumn('FrequenciaLancada','Frequência registrada','center','25%');

        $frequenciaColumn->setTransformer(function($value)
        {
            if ($value == 'SIM')
            {
                return '<span class="label label-success">SIM</span>';
            }
            return '<span class="label label-danger">NÃO</span>';
        });

        $this->datagrid->addColumn($frequenciaColumn);

        $this->datagrid->createModel();

        // Paginação
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this,'onReload']));


        $vbox = new TVBox();
        $vbox->style = 'width:100%';
        $vbox->add($this->form);

        $panel = new TPanelGroup('Disciplina');

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
        $panel->add($this->pageNavigation);
        
        $vbox->add($panel);

        parent::add($vbox);
    }

    public static function onChangeCurso($param)
    {
        $codCurso = $param['CodCurso'] ?? null;

        if (empty($codCurso))
        {
            TCombo::reload('form_relatorio','CodProfessor',[]);
            TCombo::reload('form_relatorio','disc',[]);
            return;
        }

        $items = self::getProfessores($codCurso);

        TCombo::reload('form_relatorio', 'CodProfessor', $items, true);
        TCombo::reload('form_relatorio', 'disc', [], true);
    }

    public static function onChangeProfessor($param)
    {
        $codCurso = $param['CodCurso'] ?? null;
        $codProfessor = $param['CodProfessor'] ?? null;

        if (empty($codCurso) || empty($codProfessor))
        {
            TCombo::reload('form_relatorio','disc',[]);
            return;
        }

        $items = self::getDisciplinas($codProfessor, $codCurso);

        TCombo::reload('form_relatorio', 'disc', $items, true);
    }
    
    private static function getProfessores($codCurso)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $conn = TTransaction::get();

            $sql = "
                SELECT DISTINCT
                    CodProfessor,
                    UPPER(NomeProfessor) AS NomeProfessor
                FROM VW_ProfessorDisciplinasSemestre
                WHERE Ano = :ano
                AND CodCurso = :codCurso
                ORDER BY NomeProfessor
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':ano' => date('Y'),
                ':codCurso' => $codCurso
            ]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $items = [];

            foreach ($result as $row)
            {
                $items[$row['CodProfessor']] = $row['NomeProfessor'];
            }

            return $items;
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private static function getDisciplinas($codProfessor, $codCurso)
    {
        try
        {
            TTransaction::open('dados_fei');

            $repository = new TRepository('ProfessoresDisciplinasTurmas');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodProfessor', '=', $codProfessor));
            $criteria->add(new TFilter('CodCurso', '=', $codCurso));
            $criteria->add(new TFilter('Ano', '=', date('Y')));
            $criteria->add(new TFilter('Semestre', '=', self::getSemestre()));
            $criteria->setProperty('order','NomeDisciplina');

            $objects = $repository->load($criteria);

            $items = [];

            if ($objects)
            {
                $periodos = [
                    'I' => 'INTEGRAL',
                    'N' => 'NOTURNO'
                ];

                foreach ($objects as $obj)
                {
                    $key = $obj->CodGradeDisciplinaEtapaFrente . '_' . $obj->CodTurmaetapa;

                    $periodo = $periodos[$obj->Periodo] ?? $obj->Periodo;

                    $items[$key] = $obj->NomeDisciplina . ' (' . $obj->Etapa .
                        '° CICLO - ' . $periodo . ')';
                }
            }

            TTransaction::close();

            return $items;
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    private static function getSemestre()
    {
       return date('n') <= 6 ? 1 : 2;
    }

    public function show()
    {
        if (TSession::getValue('RelatorioDisciplinas_filter'))
        {
            $this->onReload();
        }
        parent::show();
    }

    public function onSearch()
    {
        try
        {
            $this->form->validate();

            $data = $this->form->getData();

            TSession::setValue('RelatorioDisciplinas_filter', $data);

            $this->form->setData($data);

            $this->onReload(
                [
                    'offset' => 0,
                    'first_page' => 1
                ]
            );
        }
        catch (Exception $e) {
            $data = $this->form->getData();

            $this->restaurarCombos($data);
            $this->form->setData($data);

            new TMessage('warning', $e->getMessage());
        }
    }

    private function restaurarCombos($data)
    {
        if (!$data)
        {
            return;
        }

        if (!empty($data->CodCurso))
        {
            $this->professor->clear();
            $this->professor->addItems(self::getProfessores($data->CodCurso));
        }

        // Recarrega disciplinas pelo professor
        if (!empty($data->CodProfessor) && !empty($data->CodCurso))
        {
            $this->disciplina->clear();
            $this->disciplina->addItems(
                self::getDisciplinas(
                    $data->CodProfessor, $data->CodCurso
                ));
        }
            
        $this->form->setData($data);
    }


    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('dados_fei');

            $this->datagrid->clear();

            $data = TSession::getValue('RelatorioDisciplinas_filter');

            if (empty($data) || empty($data->CodCurso) || empty($data->CodProfessor) || empty($data->disc))
            {
                return;
            }

            // Restaura combos dependentes
            $this->restaurarCombos($data);

            $parts = explode('_', $data->disc);
        
            $repository = new TRepository('Vw_DiarioClasseProfessor');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodProfessor', '=', $data->CodProfessor));
            $criteria->add(new TFilter('CodDisciplina', '=', $parts[0]));
            $criteria->add(new TFilter('CodCurso', '=', $data->CodCurso));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $parts[1]));
            $criteria->add(new TFilter('AnoTurma', '=', date('Y')));

            $limit = 10;
            $criteria->setProperty('order', 'Data');
            $criteria->setProperty('direction', 'DESC');
            $criteria->setProperty('limit', $limit);

            if ($param)
            {
                $criteria->setProperties($param);
            }

            $objects = $repository->load($criteria);

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            else
            {
                TSession::delValue('RelatorioDisciplinas_filter');
                
                new TMessage('warning', 'Nenhum registro encontrado para a disciplina.');
            }

            $countCriteria = clone $criteria;
            $countCriteria->resetProperties();
            $count = $repository->count($countCriteria);

            // Paginação
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            // Manter dados no formulário
            $this->form->setData($data);
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
        finally
        {
            TTransaction::close();
        }
    }

    public function onClear()
    {
        try
        {
            TSession::delValue('RelatorioDisciplinas_filter');

            TCombo::reload('form_relatorio', 'CodProfessor', []);
            TCombo::reload('form_relatorio', 'disc', []);

            $data = new stdClass();
            $data->CodCurso = '';
            $data->CodProfessor = '';
            $data->disc = '';

            $this->form->setData($data);

            $this->datagrid->clear();
            $this->pageNavigation->setCount(0);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onGeneratePDF($param)
    {
        try
        {
            TTransaction::open('dados_fei');

            $data = TSession::getValue('RelatorioDisciplinas_filter');

            if (empty($data) || empty($data->CodCurso) || empty($data->CodProfessor) || empty($data->disc))
            {
                throw new Exception('Selecione o curso, o professor e a disciplina antes de gerar o PDF.');
            }

            $parts = explode('_', $data->disc);

            $repository = new TRepository('Vw_DiarioClasseProfessor');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodProfessor', '=', $data->CodProfessor));
            $criteria->add(new TFilter('CodDisciplina', '=', $parts[0]));
            $criteria->add(new TFilter('CodCurso', '=', $data->CodCurso));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $parts[1]));
            $criteria->add(new TFilter('AnoTurma', '=', date('Y')));
            $criteria->setProperty('order', 'Data');
            $criteria->setProperty('direction', 'DESC');

            $objects = $repository->load($criteria);

            if (!$objects)
            {
                new TMessage('warning', 'Nenhum registro encontrado para a disciplina.');
                return;
            }

            $widths = [70, 350, 70];
            $tr = new TTableWriterHTML($widths);

            $tr->addStyle('title', 'Arial', '14', 'B', '#000000', '#f0f0f0'); 
            $tr->addStyle('header', 'Arial', '12', '', '#222222', '#f0f0f0'); 
            $tr->addStyle('info_label', 'Arial', '10', 'B', '#333333', '#ffffff'); 
            $tr->addStyle('info_value', 'Arial', '10', '', '#333333', '#ffffff'); 
            $tr->addStyle('th_header', 'Arial', '12', 'B', '#000000', '#f0f0f0');
            $tr->addStyle('data_cell', 'Arial', '12', '', '#333333', '#ffffff');
            $tr->addStyle('footer_cell', 'Arial', '10', '', '#222222', '#ffffff');

            $pathLogo = $_SERVER['DOCUMENT_ROOT'] . '/template/app/images/logo-fafram.png';
            $logoHtml = '';

            if (file_exists($pathLogo)) {
                $base64Image = 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogo));
                $logoHtml = "<img src='{$base64Image}' style='max-height: 55px; height: auto;' />";
            }

            $tr->addRow();
            $tr->addCell($logoHtml, 'center', 'header', 1);
            $tr->addCell("RELATÓRIO - DIÁRIO DE CLASSE", 'center', 'title', 2);

            $object = $objects[0];

            $tr->addRow();
            $tr->addCell("Curso:", 'right', 'info_label', 1);
            $tr->addCell($object->NomeCurso, 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell("Disciplina:", 'right', 'info_label', 1);
            $tr->addCell($object->NomeDisciplina, 'left', 'info_value', 2);

             $tr->addRow();
            $tr->addCell("Professor:", 'right', 'info_label', 1);
            $tr->addCell(mb_strtoupper($object->NomeProfessor, 'UTF-8'), 'left', 'info_value', 2);

            $tr->addRow();
            $tr->addCell('Data da aula', 'center', 'th_header', 1);
            $tr->addCell('Conteúdo', 'center', 'th_header', 1);
            $tr->addCell('Frequência registrada', 'center', 'th_header', 1);

            foreach ($objects as $item)
            {
                $dataAula = !empty($item->Data) ? TDate::date2br($item->Data) : '';
                
                $conteudoTexto = !empty(trim((string)($item->conteudo ?? ''))) 
                    ? nl2br(htmlspecialchars($item->conteudo)) 
                    : '<span style="color: #FF0000; font-weight: bold;">Conteúdo não registrado</span>';
                
                $frequencia = ($item->FrequenciaLancada == 'SIM') 
                    ? 'SIM' 
                    : '<span style="color: #FF0000; font-weight: bold;">NÃO</span>';

                $tr->addRow();
                $tr->addCell($dataAula, 'center', 'data_cell');
                $tr->addCell($conteudoTexto, 'left', 'data_cell');
                $tr->addCell($frequencia, 'center', 'data_cell');
            }

            $tr->addRow();
            $tr->addCell("Relatório gerado em: " . date('d/m/Y H:i:s'), 'center', 'footer_cell', 3);

            $htmlPath = "app/output/relatorio_diario_classe.html";
            $pdfPath  = "app/output/relatorio_diario_classe.pdf";
            
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

            $colGroupTag = '<colgroup>
                                <col style="width: 17%;">
                                <col style="width: 68%;">
                                <col style="width: 15%;">
                            </colgroup>';
            
            $content = str_replace('<table>', '<table style="width: 100%; table-layout: fixed;">' . $colGroupTag, $content);

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