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
        102 => 'ENGENHARIA CIVIL',
        68  => 'ENGENHARIA DE PRODUÇÃO',
        104 => 'ENGENHARIA ELÉTRICA',
        67 => 'ENGENHARIA MECÂNICA',
        20  => 'MEDICINA VETERINÁRIA',
        6   => 'PEDAGOGIA',
        147 => 'PSICOLOGIA',
        21  => 'SISTEMAS DE INFORMAÇÃO',
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
        $this->curso->setChangeAction(new TAction([$this, 'onChangeCurso']));

        $this->professor = new TCombo('CodProfessor');
        $this->professor->setSize('40%');
        $this->professor->setChangeAction(new TAction([$this, 'onChangeProfessor']));

        $this->disciplina = new TCombo('disc');
        $this->disciplina->setSize('40%');

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

        $vbox->add(
            TPanelGroup::pack(
                'Disciplina',
                $this->datagrid,
                $this->pageNavigation
            )
        );
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
        try{
            TTransaction::open('dados_fei');
            
            $conn = TTransaction::get();

            $sql = "
                SELECT DISTINCT
                    CodProfessor,
                    NomeProfessor
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

            if (!$data)
            {
                TTransaction::close();
                return;
            }

            // Restaura combos dependentes
            $this->restaurarCombos($data);

            if (empty($data->CodCurso) || empty($data->CodProfessor)
                || empty($data->disc))
            {
                TTransaction::close();
                return;
            }

            // Separa disciplina e turma
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
            $criteria->setProperty('limit', 10);

            if ($param)
            {
                $criteria->setProperties($param);
            }

            $objects = $repository->load($criteria);

            if ($objects)
            {
                foreach($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }

            $countCriteria = clone $criteria;
            $countCriteria->resetProperties();
            $count = $repository->count($countCriteria);

            // Paginação
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit(10);

            // Manter dados no formulário
            $this->form->setData($data);

            TTransaction::close();
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
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
}