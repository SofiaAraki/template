<?php
/**
 * DiplomadigitalDadosDiplomado Active Record
 * @author  <your-name-here>
 */  
 
class DiplomaDigitalDiplomado extends TRecord
{
    const TABLENAME = 'dados_diplomado';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_aluno;
    private $diploma_digital_curso;
    private $diploma_digital_emissora;
    private $system_user;
    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod_aluno');
        parent::addAttribute('nome');
        parent::addAttribute('nome_social');
        parent::addAttribute('sexo');
        parent::addAttribute('opcao_nacionalidade');
        parent::addAttribute('nacionalidade');
        parent::addAttribute('naturalidade_cod_municipio');
        parent::addAttribute('naturalidade_nome_municipio');
        parent::addAttribute('naturalidade_uf');
        parent::addAttribute('cpf');
        parent::addAttribute('documento_identificacao');
        parent::addAttribute('rg_numero');
        parent::addAttribute('rg_orgao_expedidor');
        parent::addAttribute('rg_uf');
        parent::addAttribute('outro_doc_tipo');
        parent::addAttribute('outro_doc_identificador');
        parent::addAttribute('data_nascimento');
        parent::addAttribute('nome_pai');
        parent::addAttribute('nome_social_pai');
        parent::addAttribute('sexo_pai');
        parent::addAttribute('nome_mae');
        parent::addAttribute('nome_social_mae');
        parent::addAttribute('sexo_mae');
        parent::addAttribute('email');          
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $diplomadigital_dados_diplomado->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->fi_aluno_id = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $diplomadigital_dados_diplomado->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_fi_aluno()
    {
        // loads the associated object
        if (empty($this->fi_aluno))
            $this->fi_aluno = new FiAluno($this->cod_aluno);
    
        // returns the associated object
        return $this->fi_aluno;
    }
    
    
    /**
     * Method set_diploma_digital_curso
     * Sample of usage: $diplomadigital_dados_diplomado->diploma_digital_curso = $object;
     * @param $object Instance of DiplomaDigitalCurso
     */
    public function set_diploma_digital_curso(DiplomaDigitalCurso $object)
    {
        $this->diploma_digital_curso = $object;
        $this->diploma_digital_curso_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_curso
     * Sample of usage: $diplomadigital_dados_diplomado->diploma_digital_curso->attribute;
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
     * Method set_diploma_digital_emissora
     * Sample of usage: $diplomadigital_dados_diplomado->diploma_digital_emissora = $object;
     * @param $object Instance of DiplomaDigitalEmissora
     */
    public function set_diploma_digital_emissora(DiplomaDigitalEmissora $object)
    {
        $this->diploma_digital_emissora = $object;
        $this->diploma_digital_emissora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_emissora
     * Sample of usage: $diplomadigital_dados_diplomado->diploma_digital_emissora->attribute;
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
     * Method set_system_user
     * Sample of usage: $diplomadigital_dados_diplomado->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diplomadigital_dados_diplomado->system_user->attribute;
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
