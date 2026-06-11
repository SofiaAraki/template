<?php
/**
 * DiplomaDigitalDocumentacao Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalDocumentacao extends TRecord
{
    const TABLENAME = 'dados_documentacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_diplomado;
    private $diploma_digital_curso;
    private $diploma_digital_emissora;
    private $system_user;

    private $diploma_digital_documentos;
    private $diploma_digital_termo_responsabilidade;
    private $diploma_digital_diploma;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_documento');
        parent::addAttribute('codigo_interliga_diploma_documentacao');
        parent::addAttribute('status_documentacao');
        parent::addAttribute('opcao_via');
        parent::addAttribute('dados_versao_id');
        parent::addAttribute('dados_diplomado_id');
        parent::addAttribute('dados_curso_id');
        parent::addAttribute('dados_polo_id');
        parent::addAttribute('dados_emissora_id');
        parent::addAttribute('status_xml');
        parent::addAttribute('tipo_assinante_secretaria');
        parent::addAttribute('user_id_assinatura_secretaria');
        parent::addAttribute('cpf_assinatura_secretaria');
        parent::addAttribute('opcao_cargo_secretaria');
        parent::addAttribute('cargo_mec_secretaria');
        parent::addAttribute('outro_cargo_secretaria');
        parent::addAttribute('status_assinatura_secretaria');
        parent::addAttribute('data_exp_certificado_secretaria');
        parent::addAttribute('tipo_assinante_diretor');
        parent::addAttribute('user_id_assinatura_diretor');
        parent::addAttribute('cpf_assinatura_diretor');
        parent::addAttribute('opcao_cargo_diretor');
        parent::addAttribute('cargo_mec_diretor');
        parent::addAttribute('outro_cargo_diretor');
        parent::addAttribute('status_assinatura_diretor');
        parent::addAttribute('data_exp_certificado_diretor');
        parent::addAttribute('tipo_assinante_emissora');
        parent::addAttribute('unit_id_assinatura_emissora');
        parent::addAttribute('cnpj_assinatura_emissora');
        parent::addAttribute('status_assinatura_emissora');
        parent::addAttribute('data_exp_certificado_emissora');
        parent::addAttribute('tipo_assinante_arquivamento');
        parent::addAttribute('unit_id_assinatura_arquivamento');
        parent::addAttribute('cnpj_assinatura_arquivamento');
        parent::addAttribute('status_assinatura_arquivamento');
        parent::addAttribute('data_exp_certificado_arquivamento');
        parent::addAttribute('arquivo');
        parent::addAttribute('caminho_arquivo');
        parent::addAttribute('arquivo_registrado');
        parent::addAttribute('caminho_arquivo_registrado');
        parent::addAttribute('url_documentacao');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_diploma_digital_diplomado
     * Sample of usage: $diploma_digital_documentacao->diploma_digital_diplomado = $object;
     * @param $object Instance of DiplomaDigitalDiplomado
     */
    public function set_diploma_digital_diplomado(DiplomaDigitalDiplomado $object)
    {
        $this->diploma_digital_diplomado = $object;
        $this->diploma_digital_diplomado_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_diplomado
     * Sample of usage: $diploma_digital_documentacao->diploma_digital_diplomado->attribute;
     * @returns DiplomaDigitalDiplomado instance
     */
    public function get_diploma_digital_diplomado()
    {
        // loads the associated object
        if (empty($this->diploma_digital_diplomado))
            $this->diploma_digital_diplomado = new DiplomaDigitalDiplomado($this->dados_diplomado_id);
    
        // returns the associated object
        return $this->diploma_digital_diplomado;
    }
    
    
    /**
     * Method set_diploma_digital_curso
     * Sample of usage: $diploma_digital_documentacao->diploma_digital_curso = $object;
     * @param $object Instance of DiplomaDigitalCurso
     */
    public function set_diploma_digital_curso(DiplomaDigitalCurso $object)
    {
        $this->diploma_digital_curso = $object;
        $this->diploma_digital_curso_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_curso
     * Sample of usage: $diploma_digital_documentacao->diploma_digital_curso->attribute;
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
     * Sample of usage: $diploma_digital_documentacao->diploma_digital_emissora = $object;
     * @param $object Instance of DiplomaDigitalEmissora
     */
    public function set_diploma_digital_emissora(DiplomaDigitalEmissora $object)
    {
        $this->diploma_digital_emissora = $object;
        $this->diploma_digital_emissora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_emissora
     * Sample of usage: $diploma_digital_documentacao->diploma_digital_emissora->attribute;
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
     * Sample of usage: $diploma_digital_documentacao->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diploma_digital_documentacao->system_user->attribute;
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
