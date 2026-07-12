<?php
class HorarioTurmaReport extends TPage
{
    public static function onGenerate($param)
    {
        try {
            if (!isset($param['key'])) {
                return;
            }
            
            $id_horario = $param['key'];
            
            TTransaction::open('dados_fei');
            
            $horario = new FiHorario($id_horario);
            $turma_nome = !empty($horario->turma_etapa->Identificacao) ? $horario->turma_etapa->Identificacao : 'Turma não identificada';
            
            // Coleta dados estruturados das aulas
            $aulas_salvas = FiHorarioAulasDiarias::where('Codhorario', '=', $id_horario)->load();
            
            // Inicializa matriz indexada de dados para o relatório [Ordem][DiaSemana]
            $grade_matriz = [];
            if ($aulas_salvas) {
                foreach ($aulas_salvas as $aula) {
                    $atrib = new FiAtribuicaoGradeTurma($aula->CodAtribuicaoGradeTurma);
                    $disc = !empty($atrib->grade_frente->NomeFrente) ? $atrib->grade_frente->NomeFrente : 'Disciplina';
                    $prof = !empty($atrib->professor->Nome) ? $atrib->professor->Nome: 'Sem Professor';
                    
                    $grade_matriz[$aula->NumeroOrdemAula][$aula->DiaSemana] = "<b>{$disc}</b><br><span style='font-size:10px; color:#555;'>{$prof}</span>";
                }
            }
            
            // Define as larguras das colunas (Horário + 6 dias da semana)
            $widths = array(90, 130, 130, 130, 130, 130, 130);
            
            $tr = new TTableWriterHTML($widths);
            
            if (!empty($tr)) {
                $tr->addStyle('title', 'Arial', '14', 'B', '#ffffff', '#3b5998');
                $tr->addStyle('header', 'Arial', '11', 'B', '#555555', '#e3e3e3');
                $tr->addStyle('dia_semana', 'Arial', '10', 'B', '#ffffff', '#5c7dc1');
                $tr->addStyle('datai', 'Arial', '10', '', '#000000', '#ffffff');
                $tr->addStyle('recreio', 'Arial', '10', 'B', '#856404', '#fff3cd');
                $tr->addStyle('footer', 'Arial', '9', 'I', '#555555', '#fafafa');
                
                $tr->addRow();
                $tr->addCell("QUADRO DE HORÁRIO DA TURMA", 'center', 'title', 7);
                
                $tr->addRow();
                $tr->addCell("<b>Turma / Curso:</b> {$turma_nome}", 'center', 'header', 7);
                
                $tr->addRow();
                $tr->addCell('Horário', 'center', 'header');
                $tr->addCell('Segunda', 'center', 'dia_semana');
                $tr->addCell('Terça', 'center', 'dia_semana');
                $tr->addCell('Quarta', 'center', 'dia_semana');
                $tr->addCell('Quinta', 'center', 'dia_semana');
                $tr->addCell('Sexta', 'center', 'dia_semana');
                $tr->addCell('Sábado', 'center', 'dia_semana');
                
                $hora_base = strtotime($horario->InicioAula);
                $duracao = (int)$horario->DuracaoAula;
                $pos_intervalo = (int)$horario->IntervalorAula;
                $dur_intervalo = (int)$horario->DuracaoIntervalo;
                
                // Montagem das Linhas com as Aulas Dinâmicas
                for ($ordem = 1; $ordem <= $horario->QtdeMaximaAulasPorDia; $ordem++) {
                    $hora_fim = strtotime("+{$duracao} minutes", $hora_base);
                    $faixa = date('H:i', $hora_base) . ' às ' . date('H:i', $hora_fim);
                    
                    $tr->addRow();
                    $tr->addCell("<b>{$ordem}ª Aula</b><br><small>{$faixa}</small>", 'center', 'header');
                    
                    for ($dia = 2; $dia <= 7; $dia++) {
                        $conteudo_celula = !empty($grade_matriz[$ordem][$dia]) ? $grade_matriz[$ordem][$dia] : "<span style='color:#fffff;'>-</span>";
                        $tr->addCell($conteudo_celula, 'center', 'datai');
                    }
                    
                    $hora_base = $hora_fim;
                    
                    // Injeção automática da linha de Intervalo acadêmico
                    if ($ordem == $pos_intervalo && $dur_intervalo > 0) {
                        $hora_fim_int = strtotime("+{$dur_intervalo} minutes", $hora_base);
                        $faixa_int = date('H:i', $hora_base) . ' às ' . date('H:i', $hora_fim_int);
                        
                        $tr->addRow();
                        $tr->addCell("<b>INTERVALO</b><br><small>{$faixa_int}</small>", 'center', 'recreio');
                        $tr->addCell("INTERVALO ACADÊMICO", 'center', 'recreio', 6);
                        
                        $hora_base = $hora_fim_int;
                    }
                }
                
                // Rodapé de Emissão
                $tr->addRow();
                $tr->addCell("Documento emitido via Sistema Acadêmico em " . date('d/m/Y H:i:s'), 'center', 'footer', 7);
                
                $file_path = "app/output/HorarioTurma{$id_horario}.html";
                
                if (!file_exists("app/output") or is_writable("app/output")) {
                    $tr->save($file_path);
                } else {
                    throw new Exception("Permissão negada para gravar o arquivo em: app/output");
                }
                
                TTransaction::close();
                
                parent::openFile($file_path);
            }
            
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}