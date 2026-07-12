<?php

class HorarioCoordenadorReport extends TPage
{
    public static function onGenerate($param)
    {
        try {
            $horario_nome = !empty($param['nome_horario']) ? $param['nome_horario'] : (!empty($param['key']) ? $param['key'] : null);

            if (empty($horario_nome)) {
                throw new Exception("Nenhum identificador de grade válido foi recebido.");
            }

            TTransaction::open('Felabs_DB');

            $aulas = HorarioCoordenador::where('nome_horario', '=', $horario_nome)->load();

            $grade_matriz     = [];
            $horarios_faixas  = [];
            $linhas_intervalo = []; // Guarda quais ordens são intervalos
            
            $curso          = '-';
            $periodo        = '-';
            $etapa          = '-';
            $ano_semestre   = '-';
            $qtd_aulas      = 3;

            if ($aulas) {
                foreach ($aulas as $aula) {
                    $grade_matriz[$aula->numero_ordem_aula][$aula->dia_semana] = [
                        'disciplina' => $aula->disciplina,
                        'professor'  => $aula->professor
                    ];
                    
                    $horarios_faixas[$aula->numero_ordem_aula]  = $aula->horario_aula;
                    $linhas_intervalo[$aula->numero_ordem_aula] = (int)$aula->eh_intervalo;

                    $curso        = $aula->curso;
                    $periodo      = $aula->periodo;
                    $etapa        = $aula->etapa;
                    $ano_semestre = $aula->ano_semestre;
                    $qtd_aulas    = (int)$aula->qtd_aulas;
                }
            }

            $widths = array(125, 125, 125, 125, 125, 125, 125);
            $tr = new TTableWriterHTML($widths);

            if (!empty($tr)) {
                // Ajuste de Estilos com a paleta oficial da FAFRAM
                $tr->addStyle('title', 'Arial', '14', 'B', '#ffffff', '#024287'); // Azul Fafram Principal
                $tr->addStyle('header', 'Arial', '10', '', '#222222', '#f4f6f9'); // Fundo cinza claro corporativo para metadados
                $tr->addStyle('dia_semana', 'Arial', '9', 'B', '#ffffff', '#024287'); // Cabeçalho dos dias em Azul Fafram
                $tr->addStyle('label_aula', 'Arial', '9', 'B', '#024287', '#f4f6f9'); // Faixas laterais de horários
                $tr->addStyle('data_cell', 'Arial', '9', '', '#333333', '#ffffff');
                $tr->addStyle('intervalo', 'Arial', '10', 'B', '#ffffff', '#f07d00'); // Laranja Fafram para destacar intervalos

                $tr->addRow();
                $tr->addCell("FAFRAM - FACULDADE DR FRANCISCO MAEDA", 'center', 'title', 7);
                
                $tr->addRow();
                $tr->addCell("<b>CURSO:</b> {$curso}", 'center', 'header', 2);
                $tr->addCell("<b>CICLO/ETAPA:</b> {$etapa}", 'center', 'header', 2);
                $tr->addCell("<b>ANO:</b> {$ano_semestre}", 'center', 'header', 2);
                $tr->addCell("<b>TURNO / PERÍODO:</b> {$periodo}", 'center', 'header', 1);

                $tr->addRow();
                $tr->addCell('HORÁRIO', 'center', 'dia_semana');
                $tr->addCell('2ª FEIRA', 'center', 'dia_semana');
                $tr->addCell('3ª FEIRA', 'center', 'dia_semana');
                $tr->addCell('4ª FEIRA', 'center', 'dia_semana');
                $tr->addCell('5ª FEIRA', 'center', 'dia_semana');
                $tr->addCell('6ª FEIRA', 'center', 'dia_semana');
                $tr->addCell('SÁBADO', 'center', 'dia_semana');

                // Impressão baseada no tipo de linha dinâmica
                for ($ordem = 1; $ordem <= $qtd_aulas; $ordem++) 
                {
                    $faixa_texto = !empty($horarios_faixas[$ordem]) ? $horarios_faixas[$ordem] : "{$ordem}ª Aula";
                    
                    if (isset($linhas_intervalo[$ordem]) && $linhas_intervalo[$ordem] === 1) 
                    {
                        // Linha especial de intervalo com o Laranja Fafram
                        $tr->addRow();
                        $tr->addCell("<b>INTERVALO</b>", 'center', 'intervalo', 1);
                        $tr->addCell("<b>Horário: {$faixa_texto}</b>", 'center', 'intervalo', 6);
                    } 
                    else 
                    {
                        // Linha comum de aula normal
                        $tr->addRow();
                        $tr->addCell($faixa_texto, 'center', 'label_aula');

                        for ($dia = 2; $dia <= 7; $dia++) {
                            if (!empty($grade_matriz[$ordem][$dia]['disciplina'])) {
                                $cel = $grade_matriz[$ordem][$dia];
                                $conteudo = "<span style='color:#024287;'><b>" . $cel['disciplina'] . "</b></span><br><span style='color:#666666; font-size:11px; font-style:italic;'>" . $cel['professor'] . "</span>";
                            } else {
                                $conteudo = "<span style='color:#ccc;'>-</span>";
                            }
                            $tr->addCell($conteudo, 'center', 'data_cell');
                        }
                    }
                }

                $nome_arquivo_limpo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $horario_nome);
                $file_path = "app/output/Horario_Semestral_{$nome_arquivo_limpo}.html";
                
                $tr->save($file_path);
                TTransaction::close();
                
                parent::openFile($file_path);
                TApplication::loadPage('HorarioCoordenadorList', 'onReload');
            }
        } catch (Exception $e) {
            new TMessage('error', 'Erro ao emitir o relatório técnico: ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}