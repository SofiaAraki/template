<?php
/**
 * FiEstadosInep Active Record
 * @author  <your-name-here>
 */
class FiEstadosInep extends TRecord
{
    const TABLENAME = 'FI_Estados_INEP';
    const PRIMARYKEY= 'CODESTADO_INEP';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_cidades_inep;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('NOME');
        parent::addAttribute('UF');
    }

    
    /**
     * Method set_fi_cidades_inep
     * Sample of usage: $fi_estados_inep->fi_cidades_inep = $object;
     * @param $object Instance of FiCidadesInep
     */
    public function set_fi_cidades_inep(FiCidadesInep $object)
    {
        $this->fi_cidades_inep = $object;
        $this->CODCIDADE_INEP = $object->id;
    }
    
    /**
     * Method get_fi_cidades_inep
     * Sample of usage: $fi_estados_inep->fi_cidades_inep->attribute;
     * @returns FiCidadesInep instance
     */
    public function get_fi_cidades_inep()
    {
        // loads the associated object
        if (empty($this->fi_cidades_inep))
            $this->fi_cidades_inep = new FiCidadesInep($this->CODCIDADE_INEP);
    
        // returns the associated object
        return $this->fi_cidades_inep;
    }
    


}
