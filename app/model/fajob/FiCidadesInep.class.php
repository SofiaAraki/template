<?php
/**
 * FiCidadesInep Active Record
 * @author  <your-name-here>
 */
class FiCidadesInep extends TRecord
{
    const TABLENAME = 'FI_Cidades_INEP';
    const PRIMARYKEY= 'CODCIDADE_INEP';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_vestibular_inscricao;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODESTADO_INEP');
        parent::addAttribute('NOME');
        parent::addAttribute('CEPINICIAL');
        parent::addAttribute('CEPFINAL');
    }

    
    /**
     * Method set_fi_vestibular_inscricao
     * Sample of usage: $fi_cidades_inep->fi_vestibular_inscricao = $object;
     * @param $object Instance of FiVestibularInscricao
     */
    public function set_fi_vestibular_inscricao(FiVestibularInscricao $object)
    {
        $this->fi_vestibular_inscricao = $object;
        $this->fi_vestibular_inscricao_MUDAR = $object->id;
    }
    
    /**
     * Method get_fi_vestibular_inscricao
     * Sample of usage: $fi_cidades_inep->fi_vestibular_inscricao->attribute;
     * @returns FiVestibularInscricao instance
     */
    public function get_fi_vestibular_inscricao()
    {
        // loads the associated object
        if (empty($this->fi_vestibular_inscricao))
            $this->fi_vestibular_inscricao = new FiVestibularInscricao($this->fi_vestibular_inscricao_MUDAR);
    
        // returns the associated object
        return $this->fi_vestibular_inscricao;
    }
    


}
