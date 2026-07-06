CREATE OR ALTER VIEW dbo.VW_DiarioClasseProfessor
AS
SELECT DISTINCT
       c.Data,
       c.Ano,
       c.CodCurso,
       h.AnoTurma,
       h.CodProfessor,
       h.NomeProfessor,
       h.CodTurmaEtapa,
       h.CodGradeDisciplinaEtapa_Frente AS CodDisciplina,
       h.NomeCurso,
       h.NomeDisciplina,
       h.DiaSemana,
       dc.id,
       dc.conteudo,
       CASE
           WHEN EXISTS (
               SELECT 1
               FROM Dados_Fei.dbo.Vw_FrequenciaDiaria f
               WHERE f.DataAula = c.Data
                 AND f.CodTurmaEtapa = h.CodTurmaEtapa
                 AND f.CodDisciplina = h.CodDisciplina
           )
           THEN 'SIM'
           ELSE 'NÃO'
       END AS FrequenciaLancada
FROM Dados_Fei.dbo.VW_HorarioProfessor h
INNER JOIN Dados_Fei.dbo.Vw_CalendarioAcademico c
    ON c.CodCurso = h.CodCurso
   AND c.Ano = h.AnoTurma
   AND c.Letivo = 'S'
   AND c.Data BETWEEN h.DataInicial AND h.DataFinal
LEFT JOIN Felabs_DB.dbo.conteudo_diario_classe dc
    ON dc.cod_turma_etapa = h.CodTurmaEtapa
   AND dc.cod_professor = h.CodProfessor
   AND dc.cod_disciplina = h.CodGradeDisciplinaEtapa_Frente
   AND CONVERT(DATETIME, dc.data_aula, 103) = c.Data
WHERE DATEPART(WEEKDAY, c.Data) = h.DiaSemana;