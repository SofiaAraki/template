<?php
/**
 * VwEtapanomecurso Active Record
 * @author  <your-name-here>
 */
class VwEtapanomecurso extends TRecord
{
    const TABLENAME = 'VW_EtapaNomeCurso';
    const PRIMARYKEY= 'CodTurmaetapa';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Semestre');
        parent::addAttribute('Ano');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('Identificacao');
    }


}
