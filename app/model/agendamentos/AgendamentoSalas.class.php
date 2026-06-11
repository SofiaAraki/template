<?php
/**
 * AgendaSalaonobre Active Record
 * @author  <your-name-here>
 */
class AgendamentoSalas extends TRecord
{
    const TABLENAME = 'agendamento_salas';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('usuario');
        parent::addAttribute('data_evento');
        parent::addAttribute('inicio');
        parent::addAttribute('termino');
        parent::addAttribute('descricao');
        parent::addAttribute('data_reg');
        parent::addAttribute('sala_id');
    }






    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->usuario = $object->id;
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
            $this->system_user = new SystemUser($this->usuario);
    
        // returns the associated object
        return $this->system_user;

    }


}