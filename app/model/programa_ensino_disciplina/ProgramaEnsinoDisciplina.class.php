<?php
/**
 * ProgramaEnsinoDisciplina Active Record
 * @author  <your-name-here>
 */
class ProgramaEnsinoDisciplina extends TRecord
{
    const TABLENAME = 'programa_ensino_disciplina';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_user;
    private $fi_coordenador;
    //private $fi_disciplina;
    private $programa_ensino_disciplina_bibliografia;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('nome');
        parent::addAttribute('curso');
        parent::addAttribute('disciplina');
        parent::addAttribute('codigo');
        parent::addAttribute('obg_optativa');
        parent::addAttribute('pre_requisito');
        parent::addAttribute('co_requisito');
        parent::addAttribute('periodo');
        parent::addAttribute('semestral_anual');
        parent::addAttribute('credito');
        parent::addAttribute('total');
        parent::addAttribute('semanal');
        parent::addAttribute('teorica');
        parent::addAttribute('pratica');
        parent::addAttribute('teorica_pratica');
        parent::addAttribute('modalidade');
        parent::addAttribute('ch_presencial');
        parent::addAttribute('ch_ead');
        parent::addAttribute('ementa');
        parent::addAttribute('objetivos');
        parent::addAttribute('conteudo_programatico');
        parent::addAttribute('bibliografia_basica');
        parent::addAttribute('bibliografia_complementar');
        parent::addAttribute('data_reg');
        parent::addAttribute('turma');
        parent::addAttribute('unidade');
        parent::addAttribute('metodologia');
        parent::addAttribute('criterio_avaliacao');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('CodCurso');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('metodologia_ead');
        parent::addAttribute('criterio_aval');
        parent::addAttribute('material_supl');
        parent::addAttribute('desc_atividades');

    }

    
    /**
     * Method set_system_user
     * Sample of usage: $programa_ensino_disciplina->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $programa_ensino_disciplina->system_user->attribute;
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
     * Method set_fi_coordenador
     * Sample of usage: $programa_ensino_disciplina->fi_coordenador = $object;
     * @param $object Instance of FiCoordenador
     */
    public function set_fi_coordenador(FiCoordenador $object)
    {
        $this->fi_coordenador = $object;
        $this->fi_coordenador_id = $object->id;
    }
    
    /**
     * Method get_fi_coordenador
     * Sample of usage: $programa_ensino_disciplina->fi_coordenador->attribute;
     * @returns FiCoordenador instance
     */
    public function get_fi_coordenador()
    {
        // loads the associated object
        if (empty($this->fi_coordenador))
            $this->fi_coordenador = new FiCoordenador($this->fi_coordenador_id);
    
        // returns the associated object
        return $this->fi_coordenador;
    }
    
    public function set_programa_ensino_disciplina_bibliografia(ProgramaEnsinoDisciplinaBibliografia $object)
    {
        $this->programa_ensino_disciplina_bibliografia = $object;
        $this->programa_ensino_disciplina_bibliografia_id = $object->id;
    }
    
    public function get_programa_ensino_disciplina_bibliografia()
    {
        // loads the associated object
        if (empty($this->programa_ensino_disciplina_bibliografia))
            $this->programa_ensino_disciplina_bibliografia = new ProgramaEnsinoDisciplinaBibliografia($this->programa_ensino_disciplina_bibliografia_id);
    
        // returns the associated object
        return $this->programa_ensino_disciplina_bibliografia;
    }
    

    /**
     * Method set_vw_professordisciplinassemestre
     * Sample of usage: $programa_ensino_disciplina->vw_professordisciplinassemestre = $object;
     * @param $object Instance of VwProfessordisciplinassemestre
     
    public function set_fi_disciplina(FiDisciplina $object)
    {
        $this->fi_disciplina = $object;
        $this->CodDisciplina = $object->id;
    }*/
    
    /**
     * Method get_vw_professordisciplinassemestre
     * Sample of usage: $programa_ensino_disciplina->vw_professordisciplinassemestre->attribute;
     * @returns VwProfessordisciplinassemestre instance
     
    public function get_fi_disciplina()
    {
        // loads the associated object
        if (empty($this->fi_disciplina))
            $this->fi_disciplina = new FiDisciplina($this->CodDisciplina);
    
        // returns the associated object
        return $this->fi_disciplina;
    }*/
    


}