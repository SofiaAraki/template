<?php
/**
 * ReqBolsaAlunoParecer Active Record
 * @author  <your-name-here>
 */
class ReqBolsaAlunoParecer extends TRecord
{
    const TABLENAME = 'req_bolsa_aluno_parecer';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_req');
        //parent::addAttribute('nome_aluno');
        //parent::addAttribute('cod_aluno');
        //parent::addAttribute('data_reg');
        //parent::addAttribute('user_id');
        parent::addAttribute('anexo');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $req_bolsa_aluno_parecer->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $req_bolsa_aluno_parecer->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    


}
