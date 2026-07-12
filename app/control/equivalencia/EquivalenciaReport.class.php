<?php
class EquivalenciaReport extends TPage
{
    protected $form;

    function __construct($param)
    {
        parent::__construct();
        
        if (!empty($param['aluno_id']) && !empty($param['grade_id']))
        {
            TSession::setValue('relatorio_aluno_id',   $param['aluno_id']);
            TSession::setValue('relatorio_nome_aluno', $param['nome_aluno'] ?? TSession::getValue('relatorio_nome_aluno'));
            TSession::setValue('relatorio_grade_id',   $param['grade_id']);
        }
        
        $this->form = new BootstrapFormBuilder('form_Equivalencia_report');
    }
    
    public function onGenerate($param)
    {
        try 
        {
            TTransaction::open('Felabs_DB');
            
            $alunoId   = !empty($param['aluno_id'])   ? $param['aluno_id']   : TSession::getValue('relatorio_aluno_id');
            $nomeAluno = !empty($param['nome_aluno']) ? $param['nome_aluno'] : TSession::getValue('relatorio_nome_aluno');
            $gradeId   = !empty($param['grade_id'])   ? $param['grade_id']   : TSession::getValue('relatorio_grade_id');
            
            if (empty($alunoId) || empty($gradeId)) {
                throw new Exception("Parâmetros de Aluno ou Grade não identificados na sessão.");
            }

            $format = 'pdf';
            $widths = [50, 400, 400, 50]; 
            $tr = new TTableWriterPDF($widths, 'L', 'A4');
            
            if (!empty($tr)) {
                // Ajuste de Estilos unificados com a paleta oficial da FAFRAM
                $tr->addStyle('title', 'Arial', '12', 'B', '#ffffff', '#024287');     // Azul Fafram Principal
                $tr->addStyle('meta_header', 'Arial', '10', '', '#222222', '#f4f6f9'); // Fundo cinza claro corporativo para metadados
                $tr->addStyle('header', 'Arial', '10', 'B', '#ffffff', '#024287');    // Cabeçalho das colunas em Azul Fafram
                $tr->addStyle('datap', 'Arial', '10', '', '#333333', '#ffffff');       // Linha normal branca
                $tr->addStyle('datai', 'Arial', '10', '', '#333333', '#f4f6f9');       // Linha intercalada cinza claro corporativo
                $tr->addStyle('footer', 'Arial', '9', 'I', '#666666', '#ffffff');
                $tr->addStyle('footer2', 'Arial', '10', 'B', '#333333', '#ffffff');

                $obj_grade = CurriculoDigital::where('cod_grade', '=', $gradeId)->first();
                
                if (!$obj_grade) {
                    throw new Exception("Estrutura curricular (Grade {$gradeId}) não encontrada.");
                }

                $repositoryGrade = new TRepository('CurriculoDisciplina');
                $criteriaGrade = new TCriteria;
                $criteriaGrade->add(new TFilter('curriculo_id', '=', $obj_grade->id)); 
                $criteriaGrade->setProperty('order', 'etapa, nome');
                $criteriaGrade->setProperty('direction', 'asc');
                
                $disciplinasGrade = $repositoryGrade->load($criteriaGrade, FALSE);

                if ($disciplinasGrade) 
                {
                    $tr->addRow();
                    $tr->addCell('FACULDADE DR. FRANCISCO MAEDA - FAFRAM - RELATÓRIO DE EQUIVALÊNCIAS CURRICULARES', 'center', 'title', 4);
                    
                    $tr->addRow();
                    $tr->addCell('Aluno: ' . $alunoId . ' - ' . $nomeAluno, 'left', 'meta_header', 4);
                    $tr->addRow();
                    $tr->addCell('Estrutura Curricular: Grade ' . $obj_grade->cod_grade . ' - Curso: ' . $obj_grade->cod_curso, 'left', 'meta_header', 4);
                    
                    $tr->addRow();
                    $tr->addCell('Etapa', 'center', 'header');
                    $tr->addCell('Disciplina Original da Grade', 'left', 'header');
                    $tr->addCell('Disciplina Equivalente Aproveitada', 'left', 'header');
                    $tr->addCell('Nota', 'center', 'header');

                    $colour = FALSE;
                    
                    foreach ($disciplinasGrade as $disc) 
                    {
                        $style = $colour ? 'datai' : 'datap';
                        $disciplinaEquivalente = '';
                        $notaEquivalente = '';

                        $equivalencia = Equivalencia::where('aluno_id', '=', $alunoId)
                                                    ->where('grade_id', '=', $gradeId)
                                                    ->where('disciplina_grade_id', '=', $disc->id)
                                                    ->first();
                                                    
                        if ($equivalencia) {
                            $disciplinaEquivalente = $equivalencia->disciplina_equivalente;
                            $notaEquivalente       = $equivalencia->nota_equivalente;
                        }

                        $tr->addRow();
                        $tr->addCell($disc->etapa ?? '-', 'center', $style);
                        $tr->addCell($disc->nome ?? 'Não Encontrada', 'left', $style);
                        
                        if (!empty($disciplinaEquivalente)) {
                            $tr->addCell($disciplinaEquivalente, 'left', $style);
                        } else {
                            $tr->addCell('-', 'center', $style);
                        }
                        
                        $tr->addCell(!empty($notaEquivalente) ? $notaEquivalente : '-', 'center', $style);
                        
                        $colour = !$colour;
                    }

                    $vw_equivalencia = ViewEquivalencia::where('aluno_id', '=', $alunoId)
                                                    ->where('grade_id', '=', $gradeId)
                                                    ->first();

                    $nome_coordenador = !empty($vw_equivalencia->nome_ultimo_usuario) ? $vw_equivalencia->nome_ultimo_usuario : 'Não Identificado';

                    $tr->addRow();
                    $tr->addCell('Documento gerado em: ' . date('d/m/Y H:i:s'), 'center', 'footer', 4);
                    $tr->addRow(); $tr->addRow(); $tr->addRow();
                    $tr->addCell('____________________________________________________', 'center', 'footer2', 4);
                    $tr->addRow();
                    $tr->addCell('Coordenador Responsável: ' . $nome_coordenador, 'center', 'footer', 4);

                    $output_file = 'app/output/Equivalencias_' . $alunoId . '_' . uniqid() . '.' . $format;
                    $tr->save($output_file);

                    TTransaction::close();
                    
                    parent::openFile($output_file);

                    TApplication::loadPage('EquivalenciaList', 'onReload');
                } 
                else 
                {
                    new TMessage('warning', 'Não existem disciplinas cadastradas para esta Grade Curricular.');
                    TTransaction::close();
                }
            }
        } 
        catch (Exception $e) 
        {
            new TMessage('error', 'Falha na Emissão: ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}