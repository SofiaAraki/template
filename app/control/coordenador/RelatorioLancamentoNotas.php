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

        $lancamentoNotasColumn = new TDataGridColumn('', 'Notas Lançadas', 'center', '30%');

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

        $vbox->add(
            TPanelGroup::pack(
                'Disciplinas',
                $this->datagrid
            )
        );

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
}