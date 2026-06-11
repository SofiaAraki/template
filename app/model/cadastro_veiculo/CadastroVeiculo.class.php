<?php
/**
 * CadastroVeiculo Active Record
 * @author  <your-name-here>
 */
class CadastroVeiculo extends TRecord
{
    const TABLENAME = 'cadastro_veiculo';
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
        parent::addAttribute('grupo');
        parent::addAttribute('nome');
        parent::addAttribute('curso');
        parent::addAttribute('ciclo');
        parent::addAttribute('proprietario');
        parent::addAttribute('placa');
        parent::addAttribute('modelo');
        parent::addAttribute('ano');
        parent::addAttribute('cor');
        parent::addAttribute('validade');
        parent::addAttribute('data_reg');
        parent::addAttribute('status');
        parent::addAttribute('unidade');
        parent::addAttribute('filename');
        parent::addAttribute('obs');
        //parent::addAttribute('data_final');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $cadastro_veiculo->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $cadastro_veiculo->system_user->attribute;
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
