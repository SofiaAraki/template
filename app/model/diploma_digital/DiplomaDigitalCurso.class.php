<?php
/**
 * DiplomaDigitalCurso Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalCurso extends TRecord
{
    const TABLENAME = 'dados_curso';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_emissora;
    private $diploma_digital_mantenedora;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dados_emissora_id');
        parent::addAttribute('codigo_curso_sistema');
        parent::addAttribute('nome_curso_sistema');
        parent::addAttribute('nome_curso_diploma');
        parent::addAttribute('opcao_codigo_emec');
        parent::addAttribute('codigo_curso_emec');
        parent::addAttribute('sem_codigo_emec_numero_processo');
        parent::addAttribute('sem_codigo_emec_tipo_processo');
        parent::addAttribute('sem_codigo_emec_data_cadastro');
        parent::addAttribute('sem_codigo_emec_data_protocolo');
        parent::addAttribute('opcao_polo');
        parent::addAttribute('nome_habilitacao');
        parent::addAttribute('data_habilitacao');
        parent::addAttribute('modalidade');
        parent::addAttribute('opcao_titulo');
        parent::addAttribute('titulo_conferido');
        parent::addAttribute('outro_titulo_conferido');
        parent::addAttribute('enfase');
        parent::addAttribute('grau_conferido');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('codigo_municipio');
        parent::addAttribute('nome_municipio');
        parent::addAttribute('uf');
        parent::addAttribute('cep');
        parent::addAttribute('opcao_autorizacao_emec');
        parent::addAttribute('autorizacao_tipo');
        parent::addAttribute('autorizacao_numero');
        parent::addAttribute('autorizacao_data');
        parent::addAttribute('autorizacao_veiculo_publicacao');
        parent::addAttribute('autorizacao_data_publicacao');
        parent::addAttribute('autorizacao_secao_publicacao');
        parent::addAttribute('autorizacao_pag_publicacao');
        parent::addAttribute('autorizacao_numero_DOU');
        parent::addAttribute('autorizacao_numero_processo');
        parent::addAttribute('autorizacao_tipo_processo');
        parent::addAttribute('autorizacao_data_cadastro');
        parent::addAttribute('autorizacao_data_protocolo');
        parent::addAttribute('opcao_reconhecimento_emec');
        parent::addAttribute('reconhecimento_tipo');
        parent::addAttribute('reconhecimento_numero');
        parent::addAttribute('reconhecimento_data');
        parent::addAttribute('reconhecimento_veiculo_publicacao');
        parent::addAttribute('reconhecimento_data_publicacao');
        parent::addAttribute('reconhecimento_secao_publicacao');
        parent::addAttribute('reconhecimento_pag_publicacao');
        parent::addAttribute('reconhecimento_numero_DOU');
        parent::addAttribute('reconhecimento_numero_processo');
        parent::addAttribute('reconhecimento_tipo_processo');
        parent::addAttribute('reconhecimento_data_cadastro');
        parent::addAttribute('reconhecimento_data_protocolo');
        parent::addAttribute('opcao_renovacao_emec');
        parent::addAttribute('renovacao_reconhecimento_tipo');
        parent::addAttribute('renovacao_reconhecimento_numero');
        parent::addAttribute('renovacao_reconhecimento_data');
        parent::addAttribute('renovacao_reconhecimento_veic_publ');
        parent::addAttribute('renovacao_reconhecimento_data_publ');
        parent::addAttribute('renovacao_reconhecimento_secao_publ');
        parent::addAttribute('renovacao_reconhecimento_pag_publ');
        parent::addAttribute('renovacao_reconhecimento_numero_DOU');
        parent::addAttribute('renovacao_reconhecimento_numero_processo');
        parent::addAttribute('renovacao_reconhecimento_tipo_processo');
        parent::addAttribute('renovacao_reconhecimento_data_cadastro');
        parent::addAttribute('renovacao_reconhecimento_data_protocolo');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
        parent::addAttribute('opcao_area');
        parent::addAttribute('termo_referencia_area');
    }

    
    /**
     * Method set_diploma_digital_emissora
     * Sample of usage: $diploma_digital_curso->diploma_digital_emissora = $object;
     * @param $object Instance of DiplomaDigitalEmissora
     */
    public function set_diploma_digital_emissora(DiplomaDigitalEmissora $object)
    {
        $this->diploma_digital_emissora = $object;
        $this->diploma_digital_emissora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_emissora
     * Sample of usage: $diploma_digital_curso->diploma_digital_emissora->attribute;
     * @returns DiplomaDigitalEmissora instance
     */
    public function get_diploma_digital_emissora()
    {
        // loads the associated object
        if (empty($this->diploma_digital_emissora))
            $this->diploma_digital_emissora = new DiplomaDigitalEmissora($this->dados_emissora_id);
    
        // returns the associated object
        return $this->diploma_digital_emissora;
    }
    
    
    /**
     * Method set_diploma_digital_mantenedora
     * Sample of usage: $diploma_digital_curso->diploma_digital_mantenedora = $object;
     * @param $object Instance of DiplomaDigitalMantenedora
     */
    public function set_diploma_digital_mantenedora(DiplomaDigitalMantenedora $object)
    {
        $this->diploma_digital_mantenedora = $object;
        $this->diploma_digital_mantenedora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_mantenedora
     * Sample of usage: $diploma_digital_curso->diploma_digital_mantenedora->attribute;
     * @returns DiplomaDigitalMantenedora instance
     */
    public function get_diploma_digital_mantenedora()
    {
        // loads the associated object
        if (empty($this->diploma_digital_mantenedora))
            $this->diploma_digital_mantenedora = new DiplomaDigitalMantenedora($this->diploma_digital_mantenedora_id);
    
        // returns the associated object
        return $this->diploma_digital_mantenedora;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $diploma_digital_curso->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diploma_digital_curso->system_user->attribute;
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
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;

        $repository = new TRepository('DiplomaDigitalPolo');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('dados_curso_id', '=', $id));
        $repository->delete($criteria);
        
        // delete the object itself
        parent::delete($id);
    }

}
