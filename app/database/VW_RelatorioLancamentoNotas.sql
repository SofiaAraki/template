CREATE OR ALTER VIEW dbo.VW_RelatorioLancamentoNotas
AS
SELECT
    p.Ano,
    p.Semestre,
    p.CodCurso,
    p.Periodo,
	p.Etapa,
    p.CodTurmaetapa,
    p.CodDisciplina,
    p.NomeDisciplina,
	p.NomeProfessor,

    CASE
        WHEN EXISTS (
            SELECT 1
            FROM Dados_Fei.dbo.Vw_Notas_Faltas n
            WHERE n.Ano = p.Ano
              AND n.Semestre = p.Semestre
              AND n.CodTurmaetapa = p.CodTurmaetapa
              AND n.CodDisciplina = p.CodDisciplina
              AND n.Avaliacao = 1
              AND n.Nota1 IS NOT NULL
        ) THEN 'SIM'
        ELSE 'NÃO'
    END AS Nota_1_Bimestre,

    CASE
        WHEN EXISTS (
            SELECT 1
            FROM Dados_Fei.dbo.Vw_Notas_Faltas n
            WHERE n.Ano = p.Ano
              AND n.Semestre = p.Semestre
              AND n.CodTurmaetapa = p.CodTurmaetapa
              AND n.CodDisciplina = p.CodDisciplina
              AND n.Avaliacao = 2
              AND n.Nota1 IS NOT NULL
        ) THEN 'SIM'
        ELSE 'NÃO'
    END AS Nota_2_Bimestre

FROM Dados_Fei.dbo.VW_ProfessorDisciplinasSemestre p;


SELECT *
FROM dbo.VW_RelatorioLancamentoNotas
WHERE Ano = 2026
  AND Semestre = 1
  AND CodCurso = 21
  AND Periodo = 'N'
ORDER BY Etapa, NomeDisciplina;