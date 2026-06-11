<?php
/**
 * VwCoordenadorturmaetapa Active Record
 * @author  <your-name-here>
 */
class VwCoordenadorturmaetapa extends TRecord
{
    const TABLENAME = 'VW_CoordenadorTurmaEtapa';
    const PRIMARYKEY= 'CodTurmaetapa';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodGradeEtapa');
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('NomeCoordenador');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('CodGradeCurso');
        parent::addAttribute('CodCoordenador');
        parent::addAttribute('CodCurso');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('Identificacao');
        parent::addAttribute('CodEntidade');
    }

    


}
