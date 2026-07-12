<?php

class RodarTurmaService
{
    /**
     * Executa a transição em lote dos alunos de uma turma para outra
     */
    public static function executarTransicao($turmaOrigemId, $turmaDestinoId)
    {
        // Abre transação com o banco com base nos seus Active Records
        TTransaction::open('dados_fei');

        // 1. Busca matrículas ativas na turma de origem
        $repositorio = new TRepository('FiMatriculaetapa');
        $criterio = new TCriteria;
        $criterio->add(new TFilter('CodTurmaetapa', '=', $turmaOrigemId));
        $criterio->add(new TFilter('Situacao', '=', 'Ativo')); // Ou o código de ativo que seu sistema usa
        
        $matriculasAtivas = $repositorio->load($criterio);

        if (empty($matriculasAtivas))
        {
            throw new Exception('Nenhum aluno com matrícula ativa foi encontrado na turma de origem.');
        }

        $erros = [];
        $sucessos = 0;

        foreach ($matriculasAtivas as $matriculaAntiga)
        {
            // Instancia o aluno usando o relacionamento mapeado no seu model
            $aluno = $matriculaAntiga->get_fi_aluno();

            try
            {
                // Regra 1: Validação de Retenção (Exemplo usando seu campo real de dependências)
                // Se o seu sistema mapeia reprovações em outro lugar, pode trocar pela contagem adequada
                $reprovacoes = (int) $matriculaAntiga->QtdeDependenciaEtapa; 
                if ($reprovacoes > 3)
                {
                    throw new Exception("Aluno retido! Apresenta {$reprovacoes} dependências/reprovações cadastradas nesta etapa.");
                }

                // Regra 2: Impedir duplicidade na turma de destino
                $jaMatriculado = FiMatriculaetapa::where('Codaluno', '=', $aluno->Codaluno)
                                                ->where('CodTurmaetapa', '=', $turmaDestinoId)
                                                ->where('Situacao', '=', 'Ativo')
                                                ->first();
                if ($jaMatriculado)
                {
                    throw new Exception("O aluno já possui uma matrícula ativa na turma de destino.");
                }

                // Regra 3: Gerar nova matrícula na turma destino
                $novaMatricula = new FiMatriculaetapa;
                $novaMatricula->Codaluno = $aluno->Codaluno;
                $novaMatricula->CodTurmaetapa = $turmaDestinoId;
                $novaMatricula->DataMatricula = date('Y-m-d');
                $novaMatricula->Situacao = 'Ativo';
                $novaMatricula->ConfirmacaoMatricula = 'S';
                $novaMatricula->CodOperador = TSession::getValue('userid'); // Vincula o operador logado do Adianti
                $novaMatricula->store();

                // Regra 4: Atualizar status da matrícula antiga para histórico correto
                $matriculaAntiga->Situacao = 'Concluído'; 
                $matriculaAntiga->SituacaoData = date('Y-m-d');
                $matriculaAntiga->store();

                $sucessos++;
            }
            catch (Exception $e)
            {
                // Guarda o log de erro mapeando o id correto: Codaluno
                $erros[] = [
                    'codigo' => $aluno->Codaluno,
                    'nome'   => $aluno->Nome,
                    'motivo' => $e->getMessage()
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