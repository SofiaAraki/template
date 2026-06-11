<?php
/**
 * FichaMedica Active Record
 * @author  <your-name-here>
 */
class FichaMedica extends TRecord
{
    const TABLENAME = 'ficha_medica';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $fi_aluno;
    private $fi_curso;
    private $system_user;
    private $fi_cidade;
    private $fi_responsavel;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod_aluno');
        parent::addAttribute('nome');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
        parent::addAttribute('data_nasc');
        parent::addAttribute('local_nasc');
        parent::addAttribute('endereco');
        parent::addAttribute('cidade');
        parent::addAttribute('cep');
        parent::addAttribute('bairro');
        parent::addAttribute('responsavel');
        parent::addAttribute('aluno_mora');
        parent::addAttribute('telefone');
        parent::addAttribute('tipo_sang');
        parent::addAttribute('alergico_s_n');
        parent::addAttribute('alergico');
        parent::addAttribute('medicamento');
        parent::addAttribute('alergico_alimento_s_n');
        parent::addAttribute('alergico_alimento');
        parent::addAttribute('observacao');
        parent::addAttribute('nome_pai');
        parent::addAttribute('empresa_pai');
        parent::addAttribute('telefone_pai');
        parent::addAttribute('nome_mae');
        parent::addAttribute('empresa_mae');
        parent::addAttribute('telefone_mae');
        parent::addAttribute('nome_outros');
        parent::addAttribute('empresa_outros');
        parent::addAttribute('telefone_outros');
        parent::addAttribute('plano_de_saude_s_n');
        parent::addAttribute('plano_de_saude');
        parent::addAttribute('alergico_medicamento_s_n');
        parent::addAttribute('alergico_medicamento');
        parent::addAttribute('medico_aluno');
        parent::addAttribute('nome_medico');
        parent::addAttribute('endereco_medico');
        parent::addAttribute('telefone_medico');
        parent::addAttribute('observacao_febre');
        parent::addAttribute('doenca_congenita_s_n');
        parent::addAttribute('doenca_congenita');
        parent::addAttribute('hipertensao_s_n');
        parent::addAttribute('hipertensao');
        parent::addAttribute('doencas_contraidas_infancia');
        parent::addAttribute('epiletico_s_n');
        parent::addAttribute('epiletico_tratamento_s_n');
        parent::addAttribute('hemofilico_s_n');
        parent::addAttribute('deficiente_visual_s_n');
        parent::addAttribute('deficiente_fisico_s_n');
        parent::addAttribute('deficiente_auditivo_s_n');
        parent::addAttribute('deficiente_intelectual_s_n');
        parent::addAttribute('tea_s_n');
        parent::addAttribute('diabetico_s_n');
        parent::addAttribute('diabetico_insulina');
        parent::addAttribute('asmatico_s_n');
        parent::addAttribute('tratamento_medico_s_n');
        parent::addAttribute('tratamento_medico');
        parent::addAttribute('necessidade_s_n');
        parent::addAttribute('necessidade');
        parent::addAttribute('dificuldades_s_n');
        parent::addAttribute('ingere_medicamentos_s_n');
        parent::addAttribute('ingere_medicamentos');
        parent::addAttribute('aluno_hospital');
        parent::addAttribute('filename');
        parent::addAttribute('transtorno_s_n');
        parent::addAttribute('transtorno');
        parent::addAttribute('acp_psicologico_s_n');
        parent::addAttribute('acp_psicologico');
        parent::addAttribute('termo');
    }
    
    /**
     * Method set_fi_aluno
     * Sample of usage: $ficha_medica->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->fi_aluno_id = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $ficha_medica->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_fi_aluno()
    {
        // loads the associated object
        if (empty($this->fi_aluno))
            $this->fi_aluno = new FiAluno($this->fi_aluno_id);
    
        // returns the associated object
        return $this->fi_aluno;
    }
    
    
    /**
     * Method set_fi_curso
     * Sample of usage: $ficha_medica->fi_curso = $object;
     * @param $object Instance of FiCurso
     */
    public function set_fi_curso(FiCurso $object)
    {
        $this->fi_curso = $object;
        $this->fi_curso_id = $object->id;
    }
    
    /**
     * Method get_fi_curso
     * Sample of usage: $ficha_medica->fi_curso->attribute;
     * @returns FiCurso instance
     */
    public function get_fi_curso()
    {
        // loads the associated object
        if (empty($this->fi_curso))
            $this->fi_curso = new FiCurso($this->fi_curso_id);
    
        // returns the associated object
        return $this->fi_curso;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $ficha_medica->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $ficha_medica->system_user->attribute;
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
     * Method set_fi_cidade
     * Sample of usage: $ficha_medica->fi_cidade = $object;
     * @param $object Instance of FiCidade
     */
    public function set_fi_cidade(FiCidade $object)
    {
        $this->fi_cidade = $object;
        $this->fi_cidade_id = $object->id;
    }
    
    /**
     * Method get_fi_cidade
     * Sample of usage: $ficha_medica->fi_cidade->attribute;
     * @returns FiCidade instance
     */
    public function get_fi_cidade()
    {
        // loads the associated object
        if (empty($this->fi_cidade))
            $this->fi_cidade = new FiCidade($this->fi_cidade_id);
    
        // returns the associated object
        return $this->fi_cidade;
    }
    
    
    /**
     * Method set_fi_responsavel
     * Sample of usage: $ficha_medica->fi_responsavel = $object;
     * @param $object Instance of FiResponsavel
     */
    public function set_fi_responsavel(FiResponsavel $object)
    {
        $this->fi_responsavel = $object;
        $this->fi_responsavel_id = $object->id;
    }
    
    /**
     * Method get_fi_responsavel
     * Sample of usage: $ficha_medica->fi_responsavel->attribute;
     * @returns FiResponsavel instance
     */
    public function get_fi_responsavel()
    {
        // loads the associated object
        if (empty($this->fi_responsavel))
            $this->fi_responsavel = new FiResponsavel($this->fi_responsavel_id);
    
        // returns the associated object
        return $this->fi_responsavel;
    }
}
