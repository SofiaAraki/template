<?php
/**
 * DiplomaDigitalDiploma Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalDiploma extends TRecord
{
    const TABLENAME = 'dados_diploma';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_diplomado;
    private $diploma_digital_curso;
    private $diploma_digital_polo;
    private $diploma_digital_emissora;
    private $diploma_digital_documentacao;
    private $diploma_digital_processo_judicial;
    private $system_user;
    private $system_user_anulacao;


    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_documento');
        parent::addAttribute('codigo_interliga_diploma_documentacao');
        parent::addAttribute('versao_xsd_diploma');
        parent::addAttribute('dados_diplomado_id');
        parent::addAttribute('dados_curso_id');
        parent::addAttribute('dados_polo_id');
        parent::addAttribute('dados_emissora_id');
        parent::addAttribute('dados_documentacao_id');
        parent::addAttribute('dados_processo_judicial_id');
        parent::addAttribute('user_id_assinatura_secretaria');
        parent::addAttribute('cpf_assinatura_secretaria');
        parent::addAttribute('status_assinatura_secretaria');
        parent::addAttribute('user_id_assinatura_diretor');
        parent::addAttribute('cpf_assinatura_diretor');
        parent::addAttribute('status_assinatura_diretor');
        parent::addAttribute('unit_id_assinatura_emissora');
        parent::addAttribute('cnpj_assinatura_emissora');
        parent::addAttribute('status_assinatura_emissora');
        parent::addAttribute('livro_registro_dipl_emissora');
        parent::addAttribute('num_registro_dipl_emissora');
        parent::addAttribute('folha_registro_dipl_emissora');
        parent::addAttribute('obs_registro_emissora');
        parent::addAttribute('nome_registradora');
        parent::addAttribute('codigo_mec_registradora');
        parent::addAttribute('cnpj_registradora');
        parent::addAttribute('livro_registro_dipl_registradora');
        parent::addAttribute('num_registro_dipl_registradora');
        parent::addAttribute('folha_registro_dipl_registradora');
        parent::addAttribute('num_sequencia_dipl_registradora');
        parent::addAttribute('num_processo_dipl_registradora');
        parent::addAttribute('data_conclusao_curso');
        parent::addAttribute('data_colacao_grau');
        parent::addAttribute('data_expedicao_diploma');
        parent::addAttribute('data_registro_diploma');
        parent::addAttribute('informacoes_adicionais_registradora');
        parent::addAttribute('nome_responsavel_registro');
        parent::addAttribute('cpf_responsavel_registro');
        parent::addAttribute('status_assinatura_responsavel_registro');
        parent::addAttribute('status_assinatura_arquivamento');
        parent::addAttribute('status_diploma');
        parent::addAttribute('data_anulacao');
        parent::addAttribute('motivo_anulacao');
        parent::addAttribute('anotacao_anulacao');
        parent::addAttribute('anulacao_system_user_id');
        parent::addAttribute('anulacao_data_reg');
        parent::addAttribute('status_xml');
        parent::addAttribute('arquivo_registrado');
        parent::addAttribute('caminho_arquivo_registrado');
        parent::addAttribute('qrcode');
        parent::addAttribute('caminho_qrcode');
        parent::addAttribute('codigo_validacao_diploma');
        parent::addAttribute('url_diploma');
        parent::addAttribute('status_publicacao');
        parent::addAttribute('data_publicacao');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_diploma_digital_diplomado
     * Sample of usage: $diploma_digital_diploma->diploma_digital_diplomado = $object;
     * @param $object Instance of DiplomaDigitalDiplomado
     */
    public function set_diploma_digital_diplomado(DiplomaDigitalDiplomado $object)
    {
        $this->diploma_digital_diplomado = $object;
        $this->dados_diplomado_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_diplomado
     * Sample of usage: $diploma_digital_diploma->diploma_digital_diplomado->attribute;
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
     * Sample of usage: $diploma_digital_diploma->diploma_digital_curso = $object;
     * @param $object Instance of DiplomaDigitalCurso
     */
    public function set_diploma_digital_curso(DiplomaDigitalCurso $object)
    {
        $this->diploma_digital_curso = $object;
        $this->dados_curso_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_curso
     * Sample of usage: $diploma_digital_diploma->diploma_digital_curso->attribute;
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
     * Method set_diploma_digital_polo
     * Sample of usage: $dados_diploma->diploma_digital_polo = $object;
     * @param $object Instance of DiplomaDigitalPolo
     */
    public function set_diploma_digital_polo(DiplomaDigitalPolo $object)
    {
        $this->diploma_digital_polo = $object;
        $this->dados_polo_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_polo
     * Sample of usage: $dados_diploma->diploma_digital_polo->attribute;
     * @returns DiplomaDigitalPolo instance
     */
    public function get_diploma_digital_polo()
    {
        // loads the associated object
        if (empty($this->diploma_digital_polo))
            $this->diploma_digital_polo = new DiplomaDigitalPolo($this->dados_polo_id);
    
        // returns the associated object
        return $this->diploma_digital_polo;
    }
    
    
    /**
     * Method set_diploma_digital_emissora
     * Sample of usage: $diploma_digital_diploma->diploma_digital_emissora = $object;
     * @param $object Instance of DiplomaDigitalEmissora
     */
    public function set_diploma_digital_emissora(DiplomaDigitalEmissora $object)
    {
        $this->diploma_digital_emissora = $object;
        $this->dados_emissora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_emissora
     * Sample of usage: $diploma_digital_diploma->diploma_digital_emissora->attribute;
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
     * Method set_diploma_digital_documentacao
     * Sample of usage: $diploma_digital_diploma->diploma_digital_documentacao = $object;
     * @param $object Instance of DiplomaDigitalDocumentacao
     */
    public function set_diploma_digital_documentacao(DiplomaDigitalDocumentacao $object)
    {
        $this->diploma_digital_documentacao = $object;
        $this->dados_documentacao_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_documentacao
     * Sample of usage: $diploma_digital_diploma->diploma_digital_documentacao->attribute;
     * @returns DiplomaDigitalDocumentacao instance
     */
    public function get_diploma_digital_documentacao()
    {
        // loads the associated object
        if (empty($this->diploma_digital_documentacao))
            $this->diploma_digital_documentacao = new DiplomaDigitalDocumentacao($this->dados_documentacao_id);
    
        // returns the associated object
        return $this->diploma_digital_documentacao;
    }
    
    
    /**
     * Method set_diploma_digital_processo_judicial
     * Sample of usage: $dados_diploma->diploma_digital_processo_judicial = $object;
     * @param $object Instance of DiplomaDigitalProcessoJudicial
     */
    public function set_diploma_digital_processo_judicial(DiplomaDigitalProcessoJudicial $object)
    {
        $this->diploma_digital_processo_judicial = $object;
        $this->dados_processo_judicial_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_processo_judicial
     * Sample of usage: $dados_diploma->diploma_digital_processo_judicial->attribute;
     * @returns DiplomaDigitalProcessoJudicial instance
     */
    public function get_diploma_digital_processo_judicial()
    {
        // loads the associated object
        if (empty($this->diploma_digital_processo_judicial))
            $this->diploma_digital_processo_judicial = new DiplomaDigitalProcessoJudicial($this->dados_processo_judicial_id);
    
        // returns the associated object
        return $this->diploma_digital_processo_judicial;
    }   
    
    /**
     * Method set_system_user
     * Sample of usage: $diploma_digital_diploma->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diploma_digital_diploma->system_user->attribute;
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
    
    
    public function get_system_user_anulacao()
    {
        // loads the associated object
        if (empty($this->system_user_anulacao))
            $this->system_user_anulacao = new SystemUser($this->anulacao_system_user_id);
    
        // returns the associated object
        return $this->system_user_anulacao;
    }

}
