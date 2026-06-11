<?php
/**
 * FiCidade Active Record
 * @author  <your-name-here>
 */
class FiCidade extends TRecord
{
    const TABLENAME = 'FI_Cidade';
    const PRIMARYKEY= 'CodCidade';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_cidades_inep;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Nome');
        parent::addAttribute('Uf');
        parent::addAttribute('distancia');
        parent::addAttribute('CODCIDADE_INEP');
    }
    
    /**
     * Method set_fi_cidades_inep
     * Sample of usage: $fi_cidade->fi_cidades_inep = $object;
     * @param $object Instance of FiCidadesInep
     */
    public function set_fi_cidades_inep(FiCidadesInep $object)
    {
        $this->fi_cidades_inep = $object;
        $this->fi_cidades_inep_id = $object->id;
    }
    
    /**
     * Method get_fi_cidades_inep
     * Sample of usage: $fi_cidade->fi_cidades_inep->attribute;
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
