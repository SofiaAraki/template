<?php
/**
 * VwBigBrainEquipe Active Record
 * @author  <your-name-here>
 */
class VwBigBrainEquipe extends TRecord
{
    const TABLENAME = 'Vw_BigBrainEquipe';
    const PRIMARYKEY= 'CodTurmaetapa';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Ano');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('CodCurso');
        parent::addAttribute('Identificacao');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('NomeDisciplina');
    }


}
