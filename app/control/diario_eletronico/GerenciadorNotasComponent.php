<?php
/**
 * Subcomponente dedicado ao Lançamento de Notas Inline - Totalmente baseado na regra de FiNotasfaltasFrente
 */
class GerenciadorNotasComponent extends TElement
{
    private $datagrid;
    private $form;

    public function __construct($param = null)
    {
        parent::__construct('div');
        $this->style = 'width: 100%;';

        // 1. Captura os parâmetros essenciais
        $codTurma       = $param['cod_turma']      ?? TSession::getValue('sessao_diarioclasse')["CodTurmaetapa"] ?? null;
        $codDisciplina  = $param['cod_disciplina'] ?? TSession::getValue('sessao_diarioclasse')["CodDisciplina"] ?? null;
        $bimestre       = $param['bimestre']       ?? TSession::getValue('sessao_bimestre')["Bimestre"] ?? '1';
        $unidade        = TSession::getValue('userunitid');

        if (empty($codTurma) || empty($codDisciplina)) {
            parent::add(new TAlert('danger', 'Não foi possível carregar as notas: parâmetros ausentes de turma/disciplina.'));
            return;
        }

        // Instancia o formulário básico
        $this->form = new BootstrapFormBuilder('form_componente_notas');

        // Instanciamos a TDataGrid nativa
        $tDataGrid = new TDataGrid;
        $tDataGrid->style = 'width: 100%';

        // Configuração das Colunas da Grade de Notas
        $col_nota_cod     = new TDataGridColumn('Codaluno', 'Código', 'center');
        $col_nota_nome    = new TDataGridColumn('Nome', 'Nome do Aluno', 'left');
        $col_nota_input   = new TDataGridColumn('input_nota', "Nota do {$bimestre}º Bimestre", 'center');
        $col_nota_res     = new TDataGridColumn('Resultado', 'Result.', 'center');
        $col_nota_tipo_di = new TDataGridColumn('TipoDis', 'Tipo Disc.', 'center');

        // Adiciona as colunas base à Grid
        $tDataGrid->addColumn($col_nota_cod);
        $tDataGrid->addColumn($col_nota_nome);
        $tDataGrid->addColumn($col_nota_input);

        $tDataGrid->createModel();

        // Array compartilhado para registrar as instâncias dos campos no formulário posteriormente
        $camposDinamicos = [];

        // 2. Transformer da Nota (Consulta à tabela real física FiNotasfaltasFrente)
        $col_nota_input->setTransformer(function($value, $object, $row) use ($bimestre, &$camposDinamicos) {
            $dataArray = is_object($object) ? (method_exists($object, 'toArray') ? $object->toArray() : (array) $object) : $object;
            $valorNotaAtual = '';

            try {
                $openedLocal = false;
                if (TTransaction::get() === null) {
                    TTransaction::open('dados_fei');
                    $openedLocal = true;
                }

                $notas = FiNotasfaltasFrente::where('CodMatriculaEtapa', '=', $dataArray['CodMatriculaEtapa'] ?? '')
                                            ->where('CodDisciplina', '=', $dataArray['CodDisciplina'] ?? '')
                                            ->where('Avaliacao', '=', $bimestre)
                                            ->load();

                if ($notas) {
                    foreach ($notas as $nota) {
                        $valorNotaAtual = $nota->Nota1;
                        break;
                    }
                }

                if ($openedLocal) {
                    TTransaction::close();
                }
            } catch (Exception $e) {
                $valorNotaAtual = '';
            }

            $inputName = 'nota_' . ($dataArray['CodMatriculaEtapa'] ?? $dataArray['Codaluno']);
            $inputNota = new TEntry($inputName);
            $inputNota->setValue($valorNotaAtual);
            $inputNota->setSize('80px');
            $inputNota->style = 'text-align: center; font-weight: bold;';
            $inputNota->setEditable(TRUE);

            $actionSalvar = new TAction([__CLASS__, 'onSalvarNotaInline']);
            $actionSalvar->setParameters([
                'CodMatriculaEtapa'              => $dataArray['CodMatriculaEtapa'] ?? '',
                'CodDisciplina'                  => $dataArray['CodDisciplina'] ?? '',
                'CodGradeDisciplinaEtapa_Frente' => $dataArray['CodGradeDisciplinaEtapa_Frente'] ?? '',
                'TipoDis'                        => $dataArray['TipoDis'] ?? '',
                'CodTurmaetapa'                  => $dataArray['CodTurmaetapa'] ?? '',
                'bimestre'                       => $bimestre
            ]);
            
            $inputNota->setExitAction($actionSalvar);
            $camposDinamicos[] = $inputNota;

            return $inputNota;
        });

        // 3. Transformer para legendar e colorir o Resultado
        $col_nota_res->setTransformer(function($value, $object, $row) {
            $value = trim(strtoupper($value ?? ''));
            switch ($value) {
                case 'A': return '<span class="label label-success" style="display:block; padding:4px;">Aprovado</span>';
                case 'R': return '<span class="label label-danger" style="display:block; padding:4px;">Reprovado</span>';
                case 'E': return '<span class="label label-warning" style="display:block; padding:4px;">Exame</span>';
                case 'F': return '<span class="label label-default" style="display:block; padding:4px;">Falta</span>';
                case '':  return '<span class="label label-info" style="display:block; padding:4px;">Pendente</span>';
                default:  return '<span class="label label-primary" style="display:block; padding:4px;">'.$value.'</span>';
            }
        });

        // 4. Transformer para legendar e colorir o Tipo de Disciplina
        $col_nota_tipo_di->setTransformer(function($value, $object, $row) {
            $value = trim(strtoupper($value ?? ''));
            switch ($value) {
                case 'AT': return '<span class="label label-success" style="display:block; padding:4px;">Atual</span>';
                case 'DP': return '<span class="label label-danger" style="display:block; padding:4px;">Dependência</span>';
                case 'AD': return '<span class="label label-warning" style="display:block; padding:4px;">Adaptado</span>';
                case 'F':  return '<span class="label label-default" style="display:block; padding:4px;">Falta</span>';
                case '':  return '<span class="label label-info" style="display:block; padding:4px;">Pendente</span>';
                default:  return '<span class="label label-primary" style="display:block; padding:4px;">'.$value.'</span>';
            }
        });

        // 5. Carregamento dos Alunos vindos da View
        try {
            TTransaction::open('dados_fei');
            
            $repository = new TRepository('VwAlunosnotas');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodTurmaetapa', '=', $codTurma));
            $criteria->add(new TFilter('CodDisciplina', '=', $codDisciplina));
            
            // Regra do onReload antigo: se for o 3º Bimestre, filtra apenas quem está em Exame ('E')
            if ($bimestre == 3) {
                $criteria->add(new TFilter('Resultado', '=', 'E'));
            }
            
            $criteria->setProperty('order', 'Ordem, Nome');

            $alunosDaView = $repository->load($criteria);

            if ($alunosDaView) {
                foreach ($alunosDaView as $aluno) {
                    $tDataGrid->addItem(clone $aluno);
                }
            } else {
                parent::add(new TAlert('warning', 'Nenhum aluno localizado para os critérios informados nesta disciplina.'));
            }

            TTransaction::close();
        } catch (Exception $e) {
            if (TTransaction::get() !== null) TTransaction::rollback();
            parent::add(new TAlert('danger', 'Erro ao consultar banco de dados: ' . $e->getMessage()));
        }

        // Amarra os inputs gerados ao formulário
        if (!empty($camposDinamicos)) {
            $this->form->setFields($camposDinamicos);
        }

        // Transforma em Bootstrap Datagrid e adiciona ao formulário do componente
        $this->datagrid = new BootstrapDatagridWrapper($tDataGrid);
        $this->form->addFields([$this->datagrid]);
        
        parent::add($this->form);
    }

    /**
     * Salva a nota alterada diretamente na tabela real (FiNotasfaltasFrente)
     */
    public static function onSalvarNotaInline($param)
    {
        try {
            $codMatriculaEtapa = $param['CodMatriculaEtapa'] ?? null;
            $codDisciplina     = $param['CodDisciplina'] ?? null;
            $bimestre          = $param['bimestre'] ?? '1';
            
            if (!$codMatriculaEtapa || !$codDisciplina) {
                throw new Exception('Parâmetros de identificação do aluno ausentes.');
            }

            $nomeCampoInput = 'nota_' . $codMatriculaEtapa;
            $valorNota      = $param[$nomeCampoInput] ?? '';
            $valorNota      = str_replace(',', '.', $valorNota);
            
            TTransaction::open('dados_fei');
            
            $notaObj = FiNotasfaltasFrente::where('CodMatriculaEtapa', '=', $codMatriculaEtapa)
                                          ->where('CodDisciplina', '=', $codDisciplina)
                                          ->where('Avaliacao', '=', $bimestre)
                                          ->first();
                                            
            if (!$notaObj) {
                $notaObj = new FiNotasfaltasFrente;
                $notaObj->CodMatriculaEtapa              = $codMatriculaEtapa;
                $notaObj->CodDisciplina                  = $codDisciplina;
                $notaObj->Avaliacao                      = $bimestre;
                $notaObj->CodGradeDisciplinaEtapa_Frente = $param['CodGradeDisciplinaEtapa_Frente'] ?? null;
                $notaObj->TipoDisciplina                 = $param['TipoDis'] ?? null;
                $notaObj->CodOperador                    = TSession::getValue('userid') ?? null;
                $notaObj->DataLancamento                 = date('Y-m-d');
                $notaObj->HoraLancamento                 = date('H:i:s');
            }
            
            $notaObj->Nota1 = $valorNota !== '' ? (float)$valorNota : null;
            $notaObj->store();
            
            TTransaction::close();
            TToast::show('success', 'Nota atualizada com sucesso!', 'bottom right', 'far:check-circle');
            
        } catch (Exception $e) {
            if (TTransaction::get() !== null) TTransaction::rollback();
            new TMessage('error', 'Erro ao salvar nota: ' . $e->getMessage());
        }
    }
}