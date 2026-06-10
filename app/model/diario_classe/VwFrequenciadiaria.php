<?php
/**
 * VwFrequenciadiaria Active Record
 * @author  <your-name-here>
 */
class VwFrequenciadiaria extends TRecord
{
    const TABLENAME = 'Vw_FrequenciaDiaria';
    const PRIMARYKEY= 'CodTurmaetapa';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodFrqDiaria');
        parent::addAttribute('DataAula');
        parent::addAttribute('CodFrqDiaria_Disciplinas');
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('Aula');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('Freq');
        //parent::addAttribute('Identificacao');
        parent::addAttribute('Periodo');
        parent::addAttribute('Nome');
        parent::addAttribute('Codaluno');
    }


}
