<?php
/**
 * DiplomaDigitalDadosMantenedora Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalMantenedora extends TRecord
{
    const TABLENAME = 'dados_mantenedora';
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
        parent::addAttribute('razao_social');
        parent::addAttribute('cnpj');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('codigo_municipio');
        parent::addAttribute('nome_municipio');
        parent::addAttribute('uf');
        parent::addAttribute('cep');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $diplomadigital_dados_mantenedora->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diplomadigital_dados_mantenedora->system_user->attribute;
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
