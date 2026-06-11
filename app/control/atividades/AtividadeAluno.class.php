<?php
/**
 * AtividadeAluno Active Record
 * @author  <your-name-here>
 */
class AtividadeAluno extends TRecord
{
    const TABLENAME = 'atividade_aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $system_user;
    private $atividade;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('atividade_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('descricao');
        parent::addAttribute('anexo');
        parent::addAttribute('nota');
        parent::addAttribute('feedback');
        parent::addAttribute('data_envio');
        parent::addAttribute('data_ultimaedicao');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $atividade_aluno->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $atividade_aluno->system_user->attribute;
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
    
    
    /**
     * Method set_atividade
     * Sample of usage: $atividade_aluno->atividade = $object;
     * @param $object Instance of Atividade
     */
    public function set_atividade(Atividade $object)
    {
        $this->atividade = $object;
        $this->atividade_id = $object->id;
    }
    
    /**
     * Method get_atividade
     * Sample of usage: $atividade_aluno->atividade->attribute;
     * @returns Atividade instance
     */
    public function get_atividade()
    {
        // loads the associated object
        if (empty($this->atividade))
            $this->atividade = new Atividade($this->atividade_id);
    
        // returns the associated object
        return $this->atividade;
    }
    


}
