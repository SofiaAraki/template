<?php
/**
 * ComunicadoBolsaAceite Active Record
 * @author  <your-name-here>
 */
class ComunicadoBolsaAceite extends TRecord
{
    const TABLENAME = 'comunicado_bolsa_aceite';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $comunicado_bolsa;
    private $comunicado_bolsa_participante;
    private $system_unit;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('comunicado_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('status_aceite');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_comunicado_bolsa
     * Sample of usage: $comunicado_bolsa_aceite->comunicado_bolsa = $object;
     * @param $object Instance of ComunicadoBolsa
     */
    public function set_comunicado_bolsa(ComunicadoBolsa $object)
    {
        $this->comunicado_bolsa = $object;
        $this->comunicado_bolsa_id = $object->id;
    }
    
    /**
     * Method get_comunicado_bolsa
     * Sample of usage: $comunicado_bolsa_aceite->comunicado_bolsa->attribute;
     * @returns ComunicadoBolsa instance
     */
    public function get_comunicado_bolsa()
    {
        // loads the associated object
        if (empty($this->comunicado_bolsa))
            $this->comunicado_bolsa = new ComunicadoBolsa($this->comunicado_bolsa_id);
    
        // returns the associated object
        return $this->comunicado_bolsa;
    }
    
    
    /**
     * Method set_comunicado_bolsa_participante
     * Sample of usage: $comunicado_bolsa_aceite->comunicado_bolsa_participante = $object;
     * @param $object Instance of ComunicadoBolsaParticipante
     */
    public function set_comunicado_bolsa_participante(ComunicadoBolsaParticipante $object)
    {
        $this->comunicado_bolsa_participante = $object;
        $this->comunicado_bolsa_participante_id = $object->id;
    }
    
    /**
     * Method get_comunicado_bolsa_participante
     * Sample of usage: $comunicado_bolsa_aceite->comunicado_bolsa_participante->attribute;
     * @returns ComunicadoBolsaParticipante instance
     */
    public function get_comunicado_bolsa_participante()
    {
        // loads the associated object
        if (empty($this->comunicado_bolsa_participante))
            $this->comunicado_bolsa_participante = new ComunicadoBolsaParticipante($this->comunicado_bolsa_participante_id);
    
        // returns the associated object
        return $this->comunicado_bolsa_participante;
    }
    
    
    /**
     * Method set_system_unit
     * Sample of usage: $comunicado_bolsa_aceite->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $comunicado_bolsa_aceite->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $comunicado_bolsa_aceite->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $comunicado_bolsa_aceite->system_user->attribute;
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
