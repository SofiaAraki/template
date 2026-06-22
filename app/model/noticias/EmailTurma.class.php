<?php
/**
 * EmailTurma Active Record
 * @author  <your-name-here>
 */
class EmailTurma extends TRecord
{
    const TABLENAME = 'email_turma';
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
        parent::addAttribute('system_user_id');
        parent::addAttribute('conteudo');
        parent::addAttribute('turma');
        parent::addAttribute('data_reg');
        parent::addAttribute('assunto');
        parent::addAttribute('unidade');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $email_turma->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $email_turma->system_user->attribute;
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
