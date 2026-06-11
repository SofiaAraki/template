<?php
/**
 * AnexosFichaMedica Active Record
 * @author  <your-name-here>
 */
class AnexosFichaMedica extends TRecord
{
    const TABLENAME = 'anexos_ficha_medica';
    const PRIMARYKEY= 'id_anexo';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $fi_aluno;
    private $fi_curso;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ficha_anexo_id');
        parent::addAttribute('data_anexo');
        parent::addAttribute('obs_anexo');
        parent::addAttribute('anexo');
        parent::addAttribute('caminho_anexo');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $anexos_ficha_medica->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->fi_aluno_id = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $anexos_ficha_medica->fi_aluno->attribute;
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
     * Sample of usage: $anexos_ficha_medica->fi_curso = $object;
     * @param $object Instance of FiCurso
     */
    public function set_fi_curso(FiCurso $object)
    {
        $this->fi_curso = $object;
        $this->fi_curso_id = $object->id;
    }
    
    /**
     * Method get_fi_curso
     * Sample of usage: $anexos_ficha_medica->fi_curso->attribute;
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
     * Sample of usage: $anexos_ficha_medica->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $anexos_ficha_medica->system_user->attribute;
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
