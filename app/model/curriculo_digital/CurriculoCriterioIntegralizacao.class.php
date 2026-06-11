<?php
/**
 * CurriculoCriterioIntegralizacao Active Record
 * @author  <your-name-here>
 */
class CurriculoCriterioIntegralizacao extends TRecord
{
    const TABLENAME = 'curriculo_criterio_integralizacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $curriculo_digital;
    private $system_user;
    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curriculo_id');
        parent::addAttribute('codigo');
        parent::addAttribute('tipo_unidade');
        parent::addAttribute('dados_etiqueta_id');
        parent::addAttribute('etiquetas_nome');
        parent::addAttribute('ch_computada_hora_aula');
        parent::addAttribute('ch_computada_hora_relogio');
        parent::addAttribute('ch_minima_hora_aula');
        parent::addAttribute('ch_minima_hora_relogio');
        parent::addAttribute('ch_maxima_hora_aula');
        parent::addAttribute('ch_maxima_hora_relogio');
        parent::addAttribute('participacao_total');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_curriculo_digital
     * Sample of usage: $curriculo_criterios_integralizacao->curriculo_digital = $object;
     * @param $object Instance of CurriculoDigital
     */
    public function set_curriculo_digital(CurriculoDigital $object)
    {
        $this->curriculo_digital = $object;
        $this->curriculo_id = $object->id;
    }
    
    /**
     * Method get_curriculo_digital
     * Sample of usage: $curriculo_criterios_integralizacao->curriculo_digital->attribute;
     * @returns CurriculoDigital instance
     */
    public function get_curriculo_digital()
    {
        // loads the associated object
        if (empty($this->curriculo_digital))
            $this->curriculo_digital = new CurriculoDigital($this->curriculo_id);
    
        // returns the associated object
        return $this->curriculo_digital;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $curriculo_criterios_integralizacao->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $curriculo_criterios_integralizacao->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }


}
