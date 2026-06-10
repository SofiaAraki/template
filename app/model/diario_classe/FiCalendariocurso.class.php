<?php
/**
 * FiCalendariocurso Active Record
 * @author  <your-name-here>
 */
class FiCalendariocurso extends TRecord
{
    const TABLENAME = 'FI_CalendarioCurso';
    const PRIMARYKEY= 'CodCalendarioCurso';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodCurso');
        parent::addAttribute('CodOperador');
        parent::addAttribute('Ano');
        parent::addAttribute('DataInicio1Bim');
        parent::addAttribute('DataFim1Bim');
        parent::addAttribute('DiasLetivos1Bim');
        parent::addAttribute('DataInicio2Bim');
        parent::addAttribute('DataFim2Bim');
        parent::addAttribute('DiasLetivos2Bim');
        parent::addAttribute('DataInicio3Bim');
        parent::addAttribute('DataFim3Bim');
        parent::addAttribute('DiasLetivos3Bim');
        parent::addAttribute('DataInicio4Bim');
        parent::addAttribute('DataFim4Bim');
        parent::addAttribute('DiasLetivos4Bim');
        parent::addAttribute('TotalDiasLetivos');
        parent::addAttribute('SabadoLetivo');
    }


}
