<?php
/**
 * AgendamentoEquipamento Active Record
 * @author  <your-name-here>
 */
class AgendamentoEquipamento extends TRecord
{
    const TABLENAME = 'agendamento_equipamento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_user;

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
        parent::addAttribute('observacoes');
        parent::addAttribute('data_reg');
        parent::addAttribute('local');
        parent::addAttribute('equipamento_id');
        parent::addAttribute('unidade');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $agendamento_equipamento->system_user = $object;
     * @param $object Instance of SystemUser
     */
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




    /**
     * Method set_agendamento_equipamento_item
     * Sample of usage: $agendamento_equipamento->agendamento_equipamento_item = $object;
     * @param $object Instance of AgendamentoEquipamentoItem
     */
    public function set_agendamento_equipamento_item(AgendamentoEquipamentoItem $object)
    {
        $this->agendamento_equipamento_item = $object;
        $this->equipamento_id = $object->id;
    }
    
    /**
     * Method get_agendamento_equipamento_item
     * Sample of usage: $agendamento_equipamento->agendamento_equipamento_item->attribute;
     * @returns AgendamentoEquipamentoItem instance
     */
    public function get_agendamento_equipamento_item()
    {
        // loads the associated object
        if (empty($this->agendamento_equipamento_item))
            $this->agendamento_equipamento_item = new AgendamentoEquipamentoItem($this->equipamento_id);
    
        // returns the associated object
        return $this->agendamento_equipamento_item;
    }

    


}
