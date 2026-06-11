<?php
/**
 * DiplomadigitalDadosPolo Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalPolo extends TRecord
{
    const TABLENAME = 'dados_polo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_curso;
    private $system_user;
       
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_polo');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('codigo_municipio');
        parent::addAttribute('nome_municipio');
        parent::addAttribute('uf');
        parent::addAttribute('cep');
        parent::addAttribute('opcao_codigo_emec');
        parent::addAttribute('codigo_polo_emec');
        parent::addAttribute('sem_codigo_emec_numero_processo');
        parent::addAttribute('sem_codigo_emec_tipo_processo');
        parent::addAttribute('sem_codigo_emec_data_cadastro');
        parent::addAttribute('sem_codigo_emec_data_protocolo');
        parent::addAttribute('dados_curso_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    /**
     * Method set_diploma_digital_curso
     * Sample of usage: $diplomadigital_dados_polo->diploma_digital_curso = $object;
     * @param $object Instance of DiplomaDigitalCurso
     */
    public function set_diploma_digital_curso(DiplomaDigitalCurso $object)
    {
        $this->diploma_digital_curso = $object;
        $this->dados_curso_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_curso
     * Sample of usage: $diplomadigital_dados_polo->diploma_digital_curso->attribute;
     * @returns DiplomaDigitalCurso instance
     */
    public function get_diploma_digital_curso()
    {
        // loads the associated object
        if (empty($this->diploma_digital_curso))
            $this->diploma_digital_curso = new DiplomaDigitalCurso($this->dados_curso_id);
    
        // returns the associated object
        return $this->diploma_digital_curso;
    }
    

    /**
     * Method set_system_user
     * Sample of usage: $diplomadigital_dados_polo->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diplomadigital_dados_polo->system_user->attribute;
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
