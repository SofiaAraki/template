<?php
/**
 * VwAlunocolegio Active Record
 * @author  <your-name-here>
 */
class VwAlunocolegio extends TRecord
{
    const TABLENAME = 'Vw_AlunoColegio';
    const PRIMARYKEY= 'CodTurmaetapa';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Nome');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('Codaluno');
        parent::addAttribute('Situacao');
        parent::addAttribute('Identificacao');
        parent::addAttribute('CodCurso');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('Ano');
    }

}
