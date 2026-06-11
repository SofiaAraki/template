<?php
/**
 * SolicitacaoAluno Active Record
 * @author  <your-name-here>
 */
class SolicitacaoAluno extends TRecord
{
    const TABLENAME = 'solicitacao_aluno';
    const PRIMARYKEY= 'id_solicitacao';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_aluno;
    private $cursos_fei;
    private $precos_ffcl;
    private $system_user;
    private $vw_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod_aluno');
        parent::addAttribute('matricula_aluno');
        parent::addAttribute('unidade');
        parent::addAttribute('email_aluno');
        parent::addAttribute('tipo_solicitacao');
        parent::addAttribute('obs_solicitacao');
        parent::addAttribute('status_solicitacao');
        parent::addAttribute('status_pgto');
        parent::addAttribute('quem_abriu');
        parent::addAttribute('quem_realizou');
        parent::addAttribute('filename');
        parent::addAttribute('filename_secretaria');
        parent::addAttribute('ultima_edicao');
        parent::addAttribute('obs_secretaria');
        parent::addAttribute('data_reg');
        parent::addAttribute('nome_aluno');
        parent::addAttribute('atribuir');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $solicitacao_aluno->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->fi_aluno_id = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $solicitacao_aluno->fi_aluno->attribute;
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
     * Method set_cursos_fei
     * Sample of usage: $solicitacao_aluno->cursos_fei = $object;
     * @param $object Instance of CursosFei
     */
    public function set_cursos_fei(CursosFei $object)
    {
        $this->cursos_fei = $object;
        $this->cursos_fei_id = $object->id;
    }
    
    /**
     * Method get_cursos_fei
     * Sample of usage: $solicitacao_aluno->cursos_fei->attribute;
     * @returns CursosFei instance
     */
    public function get_cursos_fei()
    {
        // loads the associated object
        if (empty($this->cursos_fei))
            $this->cursos_fei = new CursosFei($this->cursos_fei_id);
    
        // returns the associated object
        return $this->cursos_fei;
    }
    
    
    /**
     * Method set_precos_ffcl
     * Sample of usage: $solicitacao_aluno->precos_ffcl = $object;
     * @param $object Instance of PrecosFfcl
     */
    public function set_precos_ffcl(PrecosFfcl $object)
    {
        $this->precos_ffcl = $object;
        $this->tipo_solicitacao = $object->id_preco_ffcl;
    }
    
    /**
     * Method get_precos_ffcl
     * Sample of usage: $solicitacao_aluno->precos_ffcl->attribute;
     * @returns PrecosFfcl instance
     */
    public function get_precos_ffcl()
    {
        // loads the associated object
        if (empty($this->precos_ffcl))
            $this->precos_ffcl = new PrecosFfcl($this->tipo_solicitacao);
    
        // returns the associated object
        return $this->precos_ffcl;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $solicitacao_aluno->system_user = $object;
     * @param $object Instance of SystemUser
     */
    
    
    
    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->quem_abriu = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $solicitacao_aluno->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->quem_abriu);
    
        // returns the associated object
        return $this->system_user;
    }
    
    
    
    
       public function set_realizou(SystemUser $object)
    {
        $this->realizou = $object;
        $this->quem_realizou = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $rh_ausencia->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_realizou()
    {
        // loads the associated object
        if (empty($this->realizou))
            $this->realizou = new SystemUser($this->quem_realizou);
    
        // returns the associated object
        return $this->realizou;
    }
    
    
    /**
     * Method set_cursos_ffcl
     * Sample of usage: $solicitacao_aluno->cursos_ffcl = $object;
     * @param $object Instance of CursosFfcl
     */
    public function set_cursos_ffcl(CursosFfcl $object)
    {
        $this->cursos_ffcl = $object;
        $this->matricula_aluno = $object->id;
    }
    
    /**
     * Method get_cursos_ffcl
     * Sample of usage: $solicitacao_aluno->cursos_ffcl->attribute;
     * @returns CursosFfcl instance
     */
    public function get_cursos_ffcl()
    {
        // loads the associated object
        if (empty($this->cursos_ffcl))
            $this->cursos_ffcl = new CursosFfcl($this->matricula_aluno);
    
        // returns the associated object
        return $this->cursos_ffcl;
    }
    
    /**
     * Method set_vw_aluno
     * Sample of usage: $solicitacao_aluno->vw_aluno = $object;
     * @param $object Instance of VwAluno
     */
    public function set_vw_aluno(VwAluno $object)
    {
        $this->vw_aluno = $object;
        $this->vw_aluno_id = $object->id;
    }
    
    /**
     * Method get_vw_aluno
     * Sample of usage: $solicitacao_aluno->vw_aluno->attribute;
     * @returns VwAluno instance
     */
    public function get_vw_aluno()
    {
        // loads the associated object
        if (empty($this->vw_aluno))
            $this->vw_aluno = new VwAluno($this->vw_aluno_id);
    
        // returns the associated object
        return $this->vw_aluno;
    }
    
    
    /**
     * Method set_mensagem
     * Sample of usage: $solicitacao_aluno->mensagem = $object;
     * @param $object Instance of Mensagem
     */
    public function set_mensagem(Mensagem $object)
    {
        $this->mensagem = $object;
        $this->mensagem_id = $object->id;
    }
    
    /**
     * Method get_mensagem
     * Sample of usage: $solicitacao_aluno->mensagem->attribute;
     * @returns Mensagem instance
     */
    public function get_mensagem()
    {
        // loads the associated object
        if (empty($this->mensagem))
            $this->mensagem = new Mensagem($this->mensagem_id);
    
        // returns the associated object
        return $this->mensagem;
    }
    


}
