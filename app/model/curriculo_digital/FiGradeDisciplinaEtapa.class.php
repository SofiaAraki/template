<?php
/**
 * FiGradeDisciplinaEtapa Active Record
 * @author  <your-name-here>
 */
class FiGradeDisciplinaEtapa extends TRecord
{
    const TABLENAME = 'FI_GradeDisciplinaEtapa';
    const PRIMARYKEY= 'CodGradeDisciplinaEtapa';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $fi_disciplina;
    private $fi_grade_etapa;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('CodGradeEtapa');
        parent::addAttribute('NotaRequerida');
        parent::addAttribute('PreRequisito');
        parent::addAttribute('CargaHorariaTotal');
        parent::addAttribute('CargaHorariaSemanal');
        parent::addAttribute('ValorDisciplina');
        parent::addAttribute('Categoria');
        parent::addAttribute('Display');
    }


    /**
     * Method set_fi_disciplina
     * Sample of usage: $fi_gradedisciplinaetapa->fi_disciplina = $object;
     * @param $object Instance of FiDisciplina
     */
    public function set_fi_disciplina(FiDisciplina $object)
    {
        $this->fi_disciplina = $object;
        $this->fi_disciplina_id = $object->id;
    }
    
    /**
     * Method get_fi_disciplina
     * Sample of usage: $fi_gradedisciplinaetapa->fi_disciplina->attribute;
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


    /**
     * Method set_fi_grade_etapa
     * Sample of usage: $fi_gradedisciplinaetapa->fi_grade_etapa = $object;
     * @param $object Instance of FiGradeEtapa
     */
    public function set_fi_grade_etapa(FiGradeEtapa $object)
    {
        $this->fi_grade_etapa = $object;
        $this->fi_grade_etapa_id = $object->id;
    }
    
    /**
     * Method get_fi_grade_etapa
     * Sample of usage: $fi_gradedisciplinaetapa->fi_grade_etapa->attribute;
     * @returns FiGradeEtapa instance
     */
    public function get_fi_grade_etapa()
    {
        // loads the associated object
        if (empty($this->fi_grade_etapa))
            $this->fi_grade_etapa = new FiGradeEtapa($this->CodGradeEtapa);
    
        // returns the associated object
        return $this->fi_grade_etapa;
    }
    
}
