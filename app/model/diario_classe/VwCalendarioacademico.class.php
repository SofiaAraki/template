<?php
/**
 * VwCalendarioacademico Active Record
 * @author  <your-name-here>
 */
class VwCalendarioacademico extends TRecord
{
    const TABLENAME = 'Vw_CalendarioAcademico';
    const PRIMARYKEY= 'CodCalendarioCurso';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodCurso');
        parent::addAttribute('Ano');
        parent::addAttribute('DataInicio1Bim');
        parent::addAttribute('DataInicio2Bim');
        parent::addAttribute('DataInicio3Bim');
        parent::addAttribute('DataInicio4Bim');
        parent::addAttribute('Data');
        parent::addAttribute('Letivo');
        parent::addAttribute('Motivo');
    }


}
