<?php
/**
 * FiFrqdiaria Active Record
 * @author  <your-name-here>
 */
class FiFrqdiaria extends TRecord
{
    const TABLENAME = 'FI_FrqDiaria';
    const PRIMARYKEY= 'CodFrqDiaria';
    const IDPOLICY =  'serial'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('Data');
        parent::addAttribute('CodOperador');
        parent::addAttribute('DataLancamento');
        parent::addAttribute('HoraLancamento');
    }
}
