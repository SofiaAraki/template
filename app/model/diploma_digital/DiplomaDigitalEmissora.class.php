<?php
/**
 * DiplomaDigitalEmissora Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalEmissora extends TRecord
{
    const TABLENAME = 'dados_emissora';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_mantenedora;
    private $system_user;
    private $system_unit;
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dados_mantenedora_id');
        parent::addAttribute('nome');
        parent::addAttribute('codigo_mec');
        parent::addAttribute('cnpj');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('codigo_municipio');
        parent::addAttribute('nome_municipio');
        parent::addAttribute('uf');
        parent::addAttribute('cep');
        parent::addAttribute('opcao_credenciamento_emec');
        parent::addAttribute('credenciamento_tipo');
        parent::addAttribute('credenciamento_numero');
        parent::addAttribute('credenciamento_data');
        parent::addAttribute('credenciamento_veiculo_publicacao');
        parent::addAttribute('credenciamento_data_publicacao');
        parent::addAttribute('credenciamento_secao_publicacao');
        parent::addAttribute('credenciamento_pag_publicacao');
        parent::addAttribute('credenciamento_numero_DOU');
        parent::addAttribute('credenciamento_numero_processo');
        parent::addAttribute('credenciamento_tipo_processo');
        parent::addAttribute('credenciamento_data_cadastro');
        parent::addAttribute('credenciamento_data_protocolo');
        parent::addAttribute('opcao_recredenciamento_emec');
        parent::addAttribute('recredenciamento_tipo');
        parent::addAttribute('recredenciamento_numero');
        parent::addAttribute('recredenciamento_data');
        parent::addAttribute('recredenciamento_veiculo_publicacao');
        parent::addAttribute('recredenciamento_data_publicacao');
        parent::addAttribute('recredenciamento_secao_publicacao');
        parent::addAttribute('recredenciamento_pag_publicacao');
        parent::addAttribute('recredenciamento_numero_DOU');
        parent::addAttribute('recredenciamento_numero_processo');
        parent::addAttribute('recredenciamento_tipo_processo');
        parent::addAttribute('recredenciamento_data_cadastro');
        parent::addAttribute('recredenciamento_data_protocolo');
        parent::addAttribute('opcao_renovacao_emec');
        parent::addAttribute('renovacao_recredenciamento_tipo');
        parent::addAttribute('renovacao_recredenciamento_numero');
        parent::addAttribute('renovacao_recredenciamento_data');
        parent::addAttribute('renovacao_recredenciamento_veic_publ');
        parent::addAttribute('renovacao_recredenciamento_data_publ');
        parent::addAttribute('renovacao_recredenciamento_secao_publ');
        parent::addAttribute('renovacao_recredenciamento_pag_publ');
        parent::addAttribute('renovacao_recredenciamento_numero_DOU');
        parent::addAttribute('renovacao_recredenciamento_numero_processo');
        parent::addAttribute('renovacao_recredenciamento_tipo_processo');
        parent::addAttribute('renovacao_recredenciamento_data_cadastro');
        parent::addAttribute('renovacao_recredenciamento_data_protocolo');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_diploma_digital_mantenedora
     * Sample of usage: $diploma_digital_emissora->diploma_digital_mantenedora = $object;
     * @param $object Instance of DiplomaDigitalMantenedora
     */
    public function set_diploma_digital_mantenedora(DiplomaDigitalMantenedora $object)
    {
        $this->diploma_digital_mantenedora = $object;
        $this->diploma_digital_mantenedora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_mantenedora
     * Sample of usage: $diploma_digital_emissora->diploma_digital_mantenedora->attribute;
     * @returns DiplomaDigitalMantenedora instance
     */
    public function get_diploma_digital_mantenedora()
    {
        // loads the associated object
        if (empty($this->diploma_digital_mantenedora))
            $this->diploma_digital_mantenedora = new DiplomaDigitalMantenedora($this->dados_mantenedora_id);
    
        // returns the associated object
        return $this->diploma_digital_mantenedora;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $diploma_digital_emissora->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diploma_digital_emissora->system_user->attribute;
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
     * Method set_system_unit
     * Sample of usage: $diplomadigital_dados_emissora->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $diplomadigital_dados_emissora->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }


}
