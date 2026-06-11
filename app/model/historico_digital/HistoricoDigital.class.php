<?php
/**
 * HistoricoDigital Active Record
 * @author  <your-name-here>
 */
class HistoricoDigital extends TRecord
{
    const TABLENAME = 'historico_digital';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_emissora;
    private $diploma_digital_curso;
    private $diploma_digital_diplomado;
    private $curriculo_digital;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_documento');
        parent::addAttribute('tipo_historico');
        parent::addAttribute('historico_gerado');
        parent::addAttribute('historico_genesi_id');
        parent::addAttribute('dados_versao_id');
        parent::addAttribute('dados_diplomado_id');
        parent::addAttribute('cod_aluno');
        parent::addAttribute('dados_curso_id');
        parent::addAttribute('cod_curso');
        parent::addAttribute('dados_polo_id');
        parent::addAttribute('dados_emissora_id');
        parent::addAttribute('data_ingresso');
        parent::addAttribute('forma_acesso');
        parent::addAttribute('ch_total_curso');
        parent::addAttribute('situacao_enade1');
        parent::addAttribute('situacao_enade1_condicao');
        parent::addAttribute('situacao_enade1_edicao');
        parent::addAttribute('situacao_enade1_opcao_motivo');
        parent::addAttribute('situacao_enade1_motivo');
        parent::addAttribute('situacao_enade1_outro_motivo');
        parent::addAttribute('situacao_enade2');
        parent::addAttribute('situacao_enade2_condicao');
        parent::addAttribute('situacao_enade2_edicao');
        parent::addAttribute('situacao_enade2_opcao_motivo');
        parent::addAttribute('situacao_enade2_motivo');
        parent::addAttribute('situacao_enade2_outro_motivo');
        parent::addAttribute('data_expedicao_historico');
        parent::addAttribute('data_conclusao_curso');
        parent::addAttribute('data_colacao_grau');
        parent::addAttribute('data_expedicao_diploma');
        parent::addAttribute('status_xml');
        parent::addAttribute('status_assinatura_secretaria');
        parent::addAttribute('data_exp_certificado_secretaria');
        parent::addAttribute('status_assinatura_diretor');
        parent::addAttribute('data_exp_certificado_diretor');
        parent::addAttribute('status_assinatura_emissora');
        parent::addAttribute('data_exp_certificado_emissora');
        parent::addAttribute('codigo_validacao');
        parent::addAttribute('url_historico');
        parent::addAttribute('qrcode');
        parent::addAttribute('caminho_qrcode');
        parent::addAttribute('arquivo');
        parent::addAttribute('caminho_arquivo');
        parent::addAttribute('arquivo_pdf');
        parent::addAttribute('caminho_pdf');
        parent::addAttribute('status_assinatura_pdf');
        parent::addAttribute('status_publicacao');
        parent::addAttribute('data_publicacao');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');          
        parent::addAttribute('informacoes_adicionais');
        parent::addAttribute('curriculo_id');  
        parent::addAttribute('areas_integralizadas_id'); 
        parent::addAttribute('ano_processo_seletivo');
        parent::addAttribute('mes_processo_seletivo');   
    }


    /**
     * Method set_diploma_digital_emissora
     * Sample of usage: $historico_digital->diploma_digital_emissora = $object;
     * @param $object Instance of DiplomaDigitalEmissora
     */
    public function set_diploma_digital_emissora(DiplomaDigitalEmissora $object)
    {
        $this->diploma_digital_emissora = $object;
        $this->dados_emissora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_emissora
     * Sample of usage: $historico_digital->diploma_digital_emissora->attribute;
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
     * Method set_diploma_digital_curso
     * Sample of usage: $historico_digital->diploma_digital_curso = $object;
     * @param $object Instance of DiplomaDigitalCurso
     */
    public function set_diploma_digital_curso(DiplomaDigitalCurso $object)
    {
        $this->diploma_digital_curso = $object;
        $this->dados_curso_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_curso
     * Sample of usage: $historico_digital->diploma_digital_curso->attribute;
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
     * Method set_diploma_digital_diplomado
     * Sample of usage: $historico_digital->diploma_digital_diplomado = $object;
     * @param $object Instance of DiplomaDigitalDiplomado
     */
    public function set_diploma_digital_diplomado(DiplomaDigitalDiplomado $object)
    {
        $this->diploma_digital_diplomado = $object;
        $this->dados_diplomado_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_diplomado
     * Sample of usage: $historico_digital->diploma_digital_diplomado->attribute;
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
     * Method set_curriculo_digital
     * Sample of usage: $historico_digital->curriculo_digital = $object;
     * @param $object Instance of CurriculoDigital
     */
    public function set_curriculo_digital(CurriculoDigital $object)
    {
        $this->curriculo_digital = $object;
        $this->curriculo_id = $object->id;
    }
    
    /**
     * Method get_curriculo_digital
     * Sample of usage: $historico_digital->curriculo_digital->attribute;
     * @returns CurriculoDigital instance
     */
    public function get_curriculo_digital()
    {
        // loads the associated object
        if (empty($this->curriculo_digital))
            $this->curriculo_digital = new CurriculoDigital($this->curriculo_id);
    
        // returns the associated object
        return $this->curriculo_digital;
    }

}
