<?php
/**
 * VwHistoricoprofessordisciplina Active Record
 * @author  <your-name-here>
 */
class VwHistoricoprofessordisciplina extends TRecord
{
    const TABLENAME = 'VW_HistoricoProfessorDisciplina';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codhistorico');
        parent::addAttribute('CodCurso');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('Titulacao');
        parent::addAttribute('Etapa');
        parent::addAttribute('Ano');
        parent::addAttribute('Sem');
        parent::addAttribute('NotaFinal');
        parent::addAttribute('CodHistoricoDisciplinas');
        parent::addAttribute('CH');
        parent::addAttribute('Freq');
        parent::addAttribute('Sit');
        parent::addAttribute('Edita');
        parent::addAttribute('PrefixoDisciplina');
        parent::addAttribute('SufixoDisciplina');
        parent::addAttribute('notafinalbck');
        parent::addAttribute('CodAlunoMatriculaEtapa');
    }


}