<?php
/**
 * VwProfessordisciplinassemestre Active Record
 * @author  Pamella Scapim
 */
class ProfessoresDisciplinasTurmas extends TRecord
{
    const TABLENAME = 'VW_ProfessorDisciplinasSemestre';
    const PRIMARYKEY= 'CodGradeDisciplinaEtapaFrente';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('Identificacao');
        parent::addAttribute('Periodo');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('CodCurso');
        parent::addAttribute('Etapa');
        parent::addAttribute('QuantidadeAvaliacaoes');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('CodGradeDisciplinaEtapa');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Habilitacao');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('NomeEntidade');
        parent::addAttribute('CodComposto');
    }


}
