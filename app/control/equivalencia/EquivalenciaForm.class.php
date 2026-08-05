<?php
/**
 * EquivalenciaForm - Tela de Atribuição com Entradas Inline Dinâmicas
 */
class EquivalenciaForm extends TPage
{
    protected $form;
    protected $datagrid;

    public function __construct($param)
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_Equivalencia');
        $this->form->setFormTitle('<h4>Lançamento de Equivalências</h4>');
        $this->form->setFieldSizes('100%');

        $exibe_aluno = new TEntry('exibe_aluno');
        $exibe_aluno->setEditable(FALSE);
        
        $exibe_grade = new TEntry('exibe_grade');
        $exibe_grade->setEditable(FALSE);

        // Campos ocultos
        $nome_aluno = new THidden('nome_aluno');
        $grade_id   = new THidden('grade_id');

        $this->form->addFields( [ $nome_aluno ], [ $grade_id ] );
        $this->form->addFields( [ new TLabel('Aluno:') ], [ $exibe_aluno ] );
        $this->form->addFields( [ new TLabel('Grade:') ], [ $exibe_grade ] );

        $this->form->addActionLink('Voltar', new TAction(['EquivalenciaList', 'onReload']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Equivalências', new TAction([$this, 'onSaveVisual']), 'fa:check green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->disableDefaultClick();

        $this->datagrid->addColumn(new TDataGridColumn('etapa', 'Etapa', 'center', '5%'));
        $this->datagrid->addColumn(new TDataGridColumn('cod_disciplina', 'Cód. Origem', 'center', '10%'));
        $this->datagrid->addColumn(new TDataGridColumn('nome', 'Disciplina de Origem', 'left', '35%'));
        $this->datagrid->addColumn(new TDataGridColumn('ch_hora_relogio', 'Carga Horária', 'center', '5%'));
        $this->datagrid->addColumn(new TDataGridColumn('disciplina_equivalente_widget', 'Disciplina Equivalente', 'center', '30%'));
        $this->datagrid->addColumn(new TDataGridColumn('nota_widget', 'Nota', 'center', '10%'));

        $this->datagrid->createModel();

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('Disciplinas do Currículo Encontradas', $this->datagrid));

        parent::add($container);
    }

    public function onCarregaDisciplinas($param)
    {
        $str_nome_aluno = !empty($param['nome_aluno']) ? $param['nome_aluno'] : '';
        $cod_grade      = !empty($param['grade_id']) ? $param['grade_id'] : 0;

        try
        {
            if (!empty($str_nome_aluno) && !empty($cod_grade))
            {
                TTransaction::open('Felabs_DB');

                $obj_grade = CurriculoDigital::where('cod_grade', '=', $cod_grade)->first();
                
                $data = new StdClass;
                $data->exibe_aluno = $str_nome_aluno;

                $descricaoGrade = $obj_grade->fi_grade_curso_descricao->Descricao ?? '';
                $data->exibe_grade = $obj_grade ? "Grade: ({$obj_grade->cod_grade}) - {$descricaoGrade}" : "Grade não encontrada";
                
                $data->nome_aluno  = $str_nome_aluno;
                $data->grade_id    = $cod_grade;

                $this->form->setData($data);
                $this->datagrid->clear();

                if ($obj_grade)
                {
                    $repository = new TRepository('CurriculoDisciplina');
                    $criteria = new TCriteria;
                    
                    $criteria->add(new TFilter('curriculo_id', '=', $obj_grade->id)); 
                    $criteria->setProperty('order', 'etapa, nome');
                    $criteria->setProperty('direction', 'asc');

                    $objects = $repository->load($criteria, FALSE);

                    if ($objects)
                    {
                        foreach ($objects as $object)
                        {
                            $item = new StdClass;
                            $item->etapa            = $object->etapa;
                            $item->cod_disciplina   = $object->cod_disciplina;
                            $item->nome             = $object->nome;
                            $item->ch_hora_relogio  = $object->ch_hora_relogio;

                            $val_equivalente = '';
                            $val_nota        = '';
                            
                            // Busca a equivalência pelo nome_aluno
                            $equivalencia = Equivalencia::where('nome_aluno', '=', $str_nome_aluno)
                                                        ->where('grade_id', '=', $cod_grade)
                                                        ->where('disciplina_grade_id', '=', $object->id)
                                                        ->first();
                                                        
                            if ($equivalencia) {
                                $val_equivalente = $equivalencia->disciplina_equivalente;
                                $val_nota        = $equivalencia->nota_equivalente;
                            }

                            // INPUT DISCIPLINA EQUIVALENTE
                            $name_eq = 'equivalente_' . $object->id;
                            $widget_eq = new TEntry($name_eq);
                            $widget_eq->setValue($val_equivalente);
                            $widget_eq->setSize('100%');
                            $widget_eq->setFormName('form_Equivalencia');

                            $action_eq = new TAction([__CLASS__, 'onSaveInline']);
                            $action_eq->setParameter('column', 'disciplina_equivalente');
                            $action_eq->setParameter('nome_aluno', $str_nome_aluno);
                            $action_eq->setParameter('grade_id', $cod_grade);
                            $action_eq->setParameter('disciplina_grade_id', $object->id);
                            $widget_eq->setExitAction($action_eq);

                            // INPUT NOTA
                            $nota_eq = 'nota_' . $object->id;
                            $widget_nota = new TEntry($nota_eq);
                            $widget_nota->setNumericMask(2,',','.', true);
                            $widget_nota->setMask('19,9');
                            $widget_nota->setValue($val_nota);
                            $widget_nota->setSize('100%');
                            $widget_nota->setFormName('form_Equivalencia');

                            $action_nota = new TAction([__CLASS__, 'onSaveInline']);
                            $action_nota->setParameter('column', 'nota_equivalente');
                            $action_nota->setParameter('nome_aluno', $str_nome_aluno);
                            $action_nota->setParameter('grade_id', $cod_grade);
                            $action_nota->setParameter('disciplina_grade_id', $object->id);
                            $widget_nota->setExitAction($action_nota);

                            $this->form->addField($widget_eq);
                            $this->form->addField($widget_nota);

                            $item->disciplina_equivalente_widget = $widget_eq;
                            $item->nota_widget = $widget_nota;

                            $this->datagrid->addItem($item);
                        }
                    }
                }
                TTransaction::close();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', 'Erro ao carregar matérias: ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onSaveInline($param)
    {
        $value  = (isset($param['_field_value']) && $param['_field_value'] !== '') ? $param['_field_value'] : ''; 
        $column = $param['column'];       
        
        $nome_aluno           = $param['nome_aluno'];
        $grade_id             = $param['grade_id'];
        $disciplina_grade_id = $param['disciplina_grade_id'];

        try
        {
            TTransaction::open('Felabs_DB');

            $object = Equivalencia::where('nome_aluno', '=', $nome_aluno)
                                   ->where('grade_id', '=', $grade_id)
                                   ->where('disciplina_grade_id', '=', $disciplina_grade_id)
                                   ->first();

            if (!$object)
            {
                $object = new Equivalencia;
                $object->nome_aluno           = $nome_aluno;
                $object->grade_id             = $grade_id;
                $object->disciplina_grade_id = $disciplina_grade_id;
                $object->data_lancamento      = date('Y-m-d H:i:s');
                $object->system_user_id       = TSession::getValue('userid') ?? 1;
                
                $object->disciplina_equivalente = '';
                $object->nota_equivalente       = '';
            }

            $object->$column = $value;
            
            $object->store();
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSaveVisual($param)
    {
        try {
            $action = new TAction(array('EquivalenciaList', 'onReload'));
            new TMessage('info', 'Equivalências salvas com sucesso!', $action);
        } 
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}