<?php
/**
 * MarketingBriefing Active Record
 * @author  <your-name-here>
 */
class MarketingBriefing extends TRecord
{
    const TABLENAME = 'marketing_briefing';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('solicitante');
        parent::addAttribute('departamento');
        parent::addAttribute('mantida');
        parent::addAttribute('objetivo_campanha');
        parent::addAttribute('comunicacao_sugerida');
        parent::addAttribute('titulo_evento');
        parent::addAttribute('data_evento');
        parent::addAttribute('local_evento');
        parent::addAttribute('tipo_inscricoes');
        parent::addAttribute('descritivo_evento');
        parent::addAttribute('contato_principal');
        parent::addAttribute('locais_divulgacao');
        parent::addAttribute('publico_alvo');
        parent::addAttribute('outras_info');
        parent::addAttribute('status');
        parent::addAttribute('declarar_ciencia');
        parent::addAttribute('data_reg');
        parent::addAttribute('autorizado_por');
        
    }
public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->solicitante = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $rh_ausencia->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->solicitante);
    
        // returns the associated object
        return $this->system_user;
    }

}
