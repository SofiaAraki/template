<?php
/**
 * TarefaColegio Active Record
 * @author  <your-name-here>
 */
class TarefaColegio extends TRecord
{
    const TABLENAME = 'tarefa_colegio';
    const PRIMARYKEY= 'id_tarefa';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('disciplina_tarefa');
        parent::addAttribute('turma_tarefa');
        parent::addAttribute('descricao_tarefa');
        parent::addAttribute('data_tarefa');
        parent::addAttribute('dataentrega_tarefa');
        parent::addAttribute('id_usuario');
        parent::addAttribute('id_entidade');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $tarefa_colegio->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $tarefa_colegio->system_user->attribute;
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
