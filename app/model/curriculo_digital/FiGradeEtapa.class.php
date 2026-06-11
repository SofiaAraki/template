<?php
/**
 * FiGradeEtapa Active Record
 * @author  <your-name-here>
 */
class FiGradeEtapa extends TRecord
{
    const TABLENAME = 'FI_GradeEtapa';
    const PRIMARYKEY= 'CodGradeEtapa';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $fi_grade_curso;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodSistemaAvaliacao');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('Etapa');
        parent::addAttribute('CargaHorariaTotal');
        parent::addAttribute('Descricao');
        parent::addAttribute('QuantidadeAvaliacoes');
        parent::addAttribute('QuantidadeDisciplina');
    }

    
    /**
     * Method set_fi_grade_curso
     * Sample of usage: $fi_gradeetapa->fi_grade_curso = $object;
     * @param $object Instance of FiGradeCurso
     */
    public function set_fi_grade_curso(FiGradeCurso $object)
    {
        $this->fi_grade_curso = $object;
        $this->fi_grade_curso_id = $object->id;
    }
    
    /**
     * Method get_fi_grade_curso
     * Sample of usage: $fi_gradeetapa->fi_grade_curso->attribute;
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
}
