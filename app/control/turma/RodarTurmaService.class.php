<?php

class RodarTurmaService
{
    /**
     * Localiza as turmas dinamicamente e realiza a transição estruturada dos alunos
     */
    public static function ejecutarTransicao(array $params)
    {
        TTransaction::open('dados_fei');

        // 1. Identifica a Turma de Origem com base nos filtros informados
        $turmaOrigem = FiTurmaEtapa::where('CodCurso', '=', $params['origem_curso_id'])
                                   ->where('Ano', '=', $params['origem_ano'])
                                   ->where('Semestre', '=', $params['origem_semestre'])
                                   ->where('TurmaDaEtapa', '=', $params['origem_etapa'])
                                   ->first();

        if (!$turmaOrigem) {
            throw new Exception('A turma de Origem especificada não foi encontrada no cadastro de turmas.');
        }

        // 2. Identifica a Turma de Destino com base nos filtros informados
        $turmaDestino = FiTurmaEtapa::where('CodCurso', '=', $params['destino_curso_id'])
                                    ->where('Ano', '=', $params['destino_ano'])
                                    ->where('Semestre', '=', $params['destino_semestre'])
                                    ->where('TurmaDaEtapa', '=', $params['destino_etapa'])
                                    ->first();

        if (!$turmaDestino) {
            throw new Exception('A turma de Destino especificada não foi encontrada no cadastro de turmas.');
        }

        if ($turmaOrigem->CodTurmaetapa == $turmaDestino->CodTurmaetapa) {
            throw new Exception('A turma de destino não pode ser igual à turma de origem.');
        }

        // 3. Busca alunos matriculados ativos na origem
        $repositorio = new TRepository('FiMatriculaetapa');
        $criterio = new TCriteria;
        $criterio->add(new TFilter('CodTurmaetapa', '=', $turmaOrigem->CodTurmaetapa));
        $criterio->add(new TFilter('Situacao', '=', 'Ativo'));
        
        $matriculasAtivas = $repositorio->load($criterio);

        if (empty($matriculasAtivas)) {
            throw new Exception('Nenhum aluno ativo encontrado na turma de origem para ser transferido.');
        }

        $erros = [];
        $sucessos = 0;

        // Se a opção de registrar matrículas não estiver marcada, apenas simula/gera logs
        $deveRegistrar = ($params['registrar_matriculas'] ?? '') === 'S';

        foreach ($matriculasAtivas as $matriculaAntiga)
        {
            $aluno = $matriculaAntiga->get_fi_aluno();[cite: 8]

            try
            {
                // Regra de Restrição por Documentos (Se marcado na interface do Genesi)
                if (($params['restringir_docs'] ?? '') === 'S') {
                    // Exemplo de verificação se o aluno possui pendências documentais impeditivas
                    // Substitua pelo campo real de controle de documentos caso possua no FiAluno
                    if (isset($aluno->RestricaoDocumentos) && $aluno->RestricaoDocumentos == 'S') {
                        throw new Exception('Matrícula barrada: Aluno com falta de documentos obrigatórios na secretaria.');
                    }
                }

                // Regra de Dependências Estritas
                $reprovacoes = (int) $matriculaAntiga->QtdeDependenciaEtapa;[cite: 8]
                if ($reprovacoes > 3) {
                    throw new Exception("Retido por critério técnico: Apresenta {$reprovacoes} dependências ativas.");[cite: 8]
                }

                // Impede Duplicidades
                $jaMatriculado = FiMatriculaetapa::where('Codaluno', '=', $aluno->Codaluno)[cite: 8]
                                                ->where('CodTurmaetapa', '=', $turmaDestino->CodTurmaetapa)
                                                ->where('Situacao', '=', 'Ativo')
                                                ->first();
                if ($jaMatriculado) {
                    throw new Exception("O aluno já consta matriculado e ativo na turma destino.");[cite: 8]
                }

                if ($deveRegistrar) {
                    // Gera nova matrícula na Etapa Destino
                    $novaMatricula = new FiMatriculaetapa;
                    $novaMatricula->Codaluno = $aluno->Codaluno;[cite: 8]
                    $novaMatricula->CodTurmaetapa = $turmaDestino->CodTurmaetapa;[cite: 8]
                    $novaMatricula->DataMatricula = date('Y-m-d');[cite: 8]
                    $novaMatricula->Situacao = 'Ativo';[cite: 8]
                    $novaMatricula->ConfirmacaoMatricula = 'S';[cite: 8]
                    $novaMatricula->Ingresso = (($params['matricula_inicial'] ?? '') === 'S') ? 'Inicial' : 'Regular';
                    $novaMatricula->CodOperador = TSession::getValue('userid');[cite: 8]
                    $novaMatricula->store();[cite: 8]

                    // Encerra ciclo da anterior
                    $matriculaAntiga->Situacao = 'Concluído';[cite: 8]
                    $matriculaAntiga->SituacaoData = date('Y-m-d');[cite: 8]
                    $matriculaAntiga->store();[cite: 8]
                }

                $sucessos++;
            }
            catch (Exception $e)
            {
                $erros[] = [
                    'codigo' => $aluno->Codaluno,[cite: 8]
                    'nome'   => $aluno->Nome,[cite: 8]
                    'motivo' => $e->getMessage()[cite: 8]
                ];
            }
        }

        TTransaction::close();

        return [
            'sucessos' => $sucessos,
            'erros'    => $erros
        ];
    }
}