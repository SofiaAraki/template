<?php
/**
 * CurriculoDigital Active Record
 * @author  <your-name-here>
 */
class CurriculoDigital extends TRecord
{
    const TABLENAME = 'curriculo_digital';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_emissora;
    private $diploma_digital_curso;
    private $fi_curso;
    private $fi_grade_curso;
    private $system_user;
    private $atividades_categorias;
    private $atividades_cadastro;
    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);        
        parent::addAttribute('dados_curso_id');
        parent::addAttribute('cod_curso');
        parent::addAttribute('cod_grade');
        parent::addAttribute('tipo_documento');
        parent::addAttribute('dados_versao_id');
        parent::addAttribute('codigo_curriculo');
        parent::addAttribute('data_curriculo');
        parent::addAttribute('nome_areas');        
        parent::addAttribute('regime_letivo');
        parent::addAttribute('regime_matricula');
        parent::addAttribute('numero_vagas_anual');
        parent::addAttribute('numero_vagas_turma');        
        parent::addAttribute('numero_etapas');
        parent::addAttribute('duracao_aula');
        parent::addAttribute('informacoes_adicionais');
        parent::addAttribute('status_xml');
        parent::addAttribute('status_assinatura_coordenador');
        parent::addAttribute('data_exp_certificado_coordenador');
        parent::addAttribute('dados_emissora_id');
        parent::addAttribute('tipo_assinante_emissora');
        parent::addAttribute('status_assinatura_emissora');
        parent::addAttribute('data_exp_certificado_emissora');
        parent::addAttribute('codigo_validacao');
        parent::addAttribute('url_curriculo');
        parent::addAttribute('qrcode');
        parent::addAttribute('caminho_qrcode');
        parent::addAttribute('arquivo');
        parent::addAttribute('caminho_arquivo');
        parent::addAttribute('arquivo_pdf');
        parent::addAttribute('caminho_pdf');
        parent::addAttribute('status_assinatura_pdf');
        parent::addAttribute('status_publicacao');
        parent::addAttribute('data_primeira_publicacao');
        parent::addAttribute('data_publicacao');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }


    /**
     * Method set_diploma_digital_emissora
     * Sample of usage: $curriculo_digital->diploma_digital_emissora = $object;
     * @param $object Instance of DiplomaDigitalEmissora
     */
    public function set_diploma_digital_emissora(DiplomaDigitalEmissora $object)
    {
        $this->diploma_digital_emissora = $object;
        $this->diploma_digital_emissora_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_emissora
     * Sample of usage: $curriculo_digital->diploma_digital_emissora->attribute;
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
     * Sample of usage: $curriculo_digital->diploma_digital_curso = $object;
     * @param $object Instance of DiplomaDigitalCurso
     */
    public function set_diploma_digital_curso(DiplomaDigitalCurso $object)
    {
        $this->diploma_digital_curso = $object;
        $this->diploma_digital_curso_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_curso
     * Sample of usage: $curriculo_digital->diploma_digital_curso->attribute;
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
     * Method set_fi_curso
     * Sample of usage: $curriculo_digital->fi_curso = $object;
     * @param $object Instance of FiCurso
     */
    public function set_fi_curso(FiCurso $object)
    {
        $this->fi_curso = $object;
        $this->fi_curso_id = $object->id;
    }
    
    /**
     * Method get_fi_curso
     * Sample of usage: $curriculo_digital->fi_curso->attribute;
     * @returns FiCurso instance
     */
    public function get_fi_curso()
    {
        // loads the associated object
        if (empty($this->fi_curso))
            $this->fi_curso = new FiCurso($this->cod_curso);
    
        // returns the associated object
        return $this->fi_curso;
    }
    
    
    /**
     * Method set_fi_grade_curso
     * Sample of usage: $curriculo_digital->fi_grade_curso = $object;
     * @param $object Instance of FiGradeCurso
     */
    public function set_fi_grade_curso(FiGradeCurso $object)
    {
        $this->fi_grade_curso = $object;
        $this->fi_grade_curso_id = $object->id;
    }
    
    /**
     * Method get_fi_grade_curso
     * Sample of usage: $curriculo_digital->fi_grade_curso->attribute;
     * @returns FiGradeCurso instance
     */
    public function get_fi_grade_curso()
    {
        // loads the associated object
        if (empty($this->fi_grade_curso))
            $this->fi_grade_curso = new FiGradeCurso($this->cod_grade);
    
        // returns the associated object
        return $this->fi_grade_curso;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $curriculo_digital->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $curriculo_digital->system_user->attribute;
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
    
    
    public function getCurriculoAtividadeCategorias()
    {
        return CurriculoAtividadeCategoria::where('curriculo_id', '=', $this->id)->load();
    }


    public function getCurriculoAtividadeCadastros()
    {
        return CurriculoAtividadeCadastro::where('curriculo_id', '=', $this->id)->load();
    }

    /**
     * Method get_fi_grade_curso_descricao
     * Busca a descricao da grade de forma dinâmica utilizando o código de negócio ao invés da PK física
     * @returns FiGradeCurso instance ou NULL
     */
    public function get_fi_grade_curso_descricao()
    {
        if (empty($this->fi_grade_curso)) {
            if (!empty($this->cod_grade)) {
                try {
                    TTransaction::open('dados_fei');
                    
                    $this->fi_grade_curso = FiGradeCurso::where('CodGradecurso', '=', $this->cod_grade)->first();
                    
                    TTransaction::close();
                } catch (Exception $e) {
                    TTransaction::rollback();
                    $this->fi_grade_curso = new FiGradeCurso;
                }
            }
        }
        return $this->fi_grade_curso ?? new FiGradeCurso;
    }
}
