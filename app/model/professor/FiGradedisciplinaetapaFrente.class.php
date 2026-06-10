<?php
/**
 * FiGradedisciplinaetapaFrente Active Record
 * @author  <your-name-here>
 */
class FiGradedisciplinaetapaFrente extends TRecord
{
    const TABLENAME = 'FI_GradeDisciplinaEtapa_Frente';
    const PRIMARYKEY= 'CodGradeDisciplinaEtapa_Frente';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodGradeDisciplinaEtapa');
        parent::addAttribute('NomeFrente');
        parent::addAttribute('CargaHorariaSemanal_Parcial');
    }


}
