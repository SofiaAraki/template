<?php
/**
 * VwBigbrainprofessor Active Record
 * @author  <your-name-here>
 */
class VwBigBrainProfessor extends TRecord
{
    const TABLENAME = 'Vw_BigBrainProfessor';
    const PRIMARYKEY= 'Codprofessor';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Ano');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('CodCurso');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('Identificacao');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('NomeProfessor');
    }


}
