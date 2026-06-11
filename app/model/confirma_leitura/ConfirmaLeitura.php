<?php
/**
 * ConfirmaLeitura Active Record
 * @author  <your-name-here>
 */
class ConfirmaLeitura extends TRecord
{
    const TABLENAME = 'confirma_leitura';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod_aluno');
        parent::addAttribute('confirma_leit');
        parent::addAttribute('data_confirma');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_user_id');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $confirma_leitura->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $confirma_leitura->system_user->attribute;
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
