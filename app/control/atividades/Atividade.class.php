<?php
/**
 * Atividade Active Record
 * @author  <your-name-here>
 */
class Atividade extends TRecord
{
    const TABLENAME = 'atividade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo');
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('anexo');
        parent::addAttribute('valor_nota');
        parent::addAttribute('data_prazo');
        parent::addAttribute('data_reg');
        parent::addAttribute('system_user_id');
        parent::addAttribute('coddisciplina');
        parent::addAttribute('codturmaetapa');
        parent::addAttribute('ordem');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $atividade->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $atividade->system_user->attribute;
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
