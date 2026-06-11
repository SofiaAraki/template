<?php
/**
 * AtendimentoPsicopedagogicoDatas Active Record
 * @author  <your-name-here>
 */
class AtendimentoPsicopedagogicoDatas extends TRecord
{
    const TABLENAME = 'atendimento_psicopedagogico_datas';
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
        parent::addAttribute('data_evento');
        parent::addAttribute('unidade');
        parent::addAttribute('status');
        parent::addAttribute('data_reg');
        parent::addAttribute('entrada_hora');
        parent::addAttribute('saida_hora');
        parent::addAttribute('email');
        parent::addAttribute('curso');
        parent::addAttribute('system_user_reg');
        parent::addAttribute('id_psico');

    }

    
    /**
     * Method set_system_user
     * Sample of usage: $atendimento_psicopedagogico_datas->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
        //$this->system_user_reg = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $atendimento_psicopedagogico_datas->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
            //$this->system_user = new SystemUser($this->system_user_reg);
    
        // returns the associated object
        return $this->system_user;
    }

    /**
     * Method set_system_user
     * Sample of usage: $atendimento_psicopedagogico_datas->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user_operador(SystemUser $object)
    {
        $this->system_user_operador = $object;
        $this->system_user_reg = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $atendimento_psicopedagogico_datas->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user_operador()
    {
        // loads the associated object
        if (empty($this->system_user_operador))
            $this->system_user_operador = new SystemUser($this->system_user_reg);
    
        // returns the associated object
        return $this->system_user_operador;
    }

    /**
     * Method set_system_user
     * Sample of usage: $atendimento_psicopedagogico_datas->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user_psico(SystemUser $object)
    {
        $this->system_user_psico = $object;
        $this->id_psico = $object->id;
        //$this->system_user_reg = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $atendimento_psicopedagogico_datas->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user_psico()
    {
        // loads the associated object
        if (empty($this->system_user_psico))
            $this->system_user_psico = new SystemUser($this->id_psico);
            //$this->system_user = new SystemUser($this->system_user_reg);
    
        // returns the associated object
        return $this->system_user_psico;
    } 


}
