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
            $widths = [40, 260, 260, 40]; 
            $tr = new TTableWriterPDF($widths, 'P', 'A4');
            
            $tr->addStyle('title', 'Arial', '11', 'B', '#ffffff', '#4A90E2');
            $tr->addStyle('datap', 'Arial', '9', '',  '#333333', '#ffffff');
            $tr->addStyle('datai', 'Arial', '9', '',  '#333333', '#f9f9f9');
            $tr->addStyle('header', 'Arial', '9', 'B', '#333333', '#e0e0e0');
            $tr->addStyle('footer', 'Arial', '8',  'I', '#666666', '#ffffff');
            $tr->addStyle('footer2', 'Arial', '9', 'B', '#333333', '#ffffff');

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
                $tr->addCell('RELATÓRIO DE EQUIVALÊNCIAS CURRICULARES', 'center', 'title', 4);
                
                $tr->addRow();
                $tr->addCell('Aluno: ' . $alunoId . ' - ' . $nomeAluno, 'left', 'datap', 4);
                $tr->addRow();
                $tr->addCell('Estrutura Curricular: Grade ' . $obj_grade->cod_grade . ' - Curso: ' . $obj_grade->cod_curso, 'left', 'datap', 4);
                
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
                    $tr->addCell($disciplinaEquivalente, 'left', $style);
                    $tr->addCell($notaEquivalente, 'center', $style);
                    
                    $colour = !$colour;
                }

                $tr->addRow();
                $tr->addCell('Documento gerado em: ' . date('d/m/Y H:i:s'), 'center', 'footer', 4);
                $tr->addRow(); $tr->addRow(); $tr->addRow();
                $tr->addCell('____________________________________________________', 'center', 'footer2', 4);
                $tr->addRow();
                $tr->addCell('Coordenador Responsável', 'center', 'footer', 4);

                $output_file = 'tmp/Equivalencias_' . $alunoId . '_' . uniqid() . '.' . $format;
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
        catch (Exception $e) 
        {
            new TMessage('error', '<b>Falha na Emissão:</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}