<?php
/**
 * FiGradeDisciplina Active Record
 * @author  <your-name-here>
 */
class FiGradeDisciplina extends TRecord
{
    const TABLENAME = 'FI_GradeDisciplina';
    const PRIMARYKEY= 'CoddisciplinaGrade';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $fi_grade_curso;
    private $fi_disciplina;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('CargaHorariaTotal');
    }

    
    /**
     * Method set_fi_grade_curso
     * Sample of usage: $fi_grade_disciplina->fi_grade_curso = $object;
     * @param $object Instance of FiGradeCurso
     */
    public function set_fi_grade_curso(FiGradeCurso $object)
    {
        $this->fi_grade_curso = $object;
        $this->CodGradecurso = $object->id;
    }
    
    /**
     * Method get_fi_grade_curso
     * Sample of usage: $fi_grade_disciplina->fi_grade_curso->attribute;
     * @returns FiGradeCurso instance
     */
    public function get_fi_grade_curso()
    {
        // loads the associated object
        if (empty($this->fi_grade_curso))
            $this->fi_grade_curso = new FiGradeCurso($this->CodGradecurso);
    
        // returns the associated object
        return $this->fi_grade_curso;
    }
    
    
    /**
     * Method set_fi_disciplina
     * Sample of usage: $fi_grade_disciplina->fi_disciplina = $object;
     * @param $object Instance of FiDisciplina
     */
    public function set_fi_disciplina(FiDisciplina $object)
    {
        $this->fi_disciplina = $object;
        $this->CodDisciplina = $object->id;
    }
    
    /**
     * Method get_fi_disciplina
     * Sample of usage: $fi_grade_disciplina->fi_disciplina->attribute;
     * @returns FiDisciplina instance
     */
    public function get_fi_disciplina()
    {
        // loads the associated object
        if (empty($this->fi_disciplina))
            $this->fi_disciplina = new FiDisciplina($this->CodDisciplina);
    
        // returns the associated object
        return $this->fi_disciplina;
    }
    


}
