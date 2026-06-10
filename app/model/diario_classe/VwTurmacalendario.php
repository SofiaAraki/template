<?php
/**
 * VwTurmacalendario Active Record
 * @author  <your-name-here>
 */
class VwTurmacalendario extends TRecord
{
    const TABLENAME = 'Vw_TurmaCalendario';
    const PRIMARYKEY= 'CodGradeDisciplinaEtapa_Frente';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodCurso');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('Identificacao');
        parent::addAttribute('AnoMatricula');
        parent::addAttribute('SemestreMatricula');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('Codaluno');
        parent::addAttribute('SituacaoMatricula');
        parent::addAttribute('NomeAluno');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('NomeFrente');
        parent::addAttribute('Codhorario');
        parent::addAttribute('CodHorario_AulasDiarias');
        parent::addAttribute('DiaSemana');
        parent::addAttribute('NumeroOrdemAula');
        parent::addAttribute('Etapa');
    }


}
