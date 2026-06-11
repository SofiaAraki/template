<?php
/**
 * CurriculoDisciplinaArea Active Record
 * @author  <your-name-here>
 */
class CurriculoDisciplinaArea extends TRecord
{
    const TABLENAME = 'curriculo_disciplina_area';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $curriculo_disciplina;
    private $area_formacao;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curriculo_disciplina_id');
        parent::addAttribute('dados_area_formacao_id');
    }

    
    /**
     * Method set_curriculo_disciplina
     * Sample of usage: $curriculo_disciplina_area->curriculo_disciplina = $object;
     * @param $object Instance of CurriculoDisciplina
     */
    public function set_curriculo_disciplina(CurriculoDisciplina $object)
    {
        $this->curriculo_disciplina = $object;
        $this->curriculo_disciplina_id = $object->id;
    }
    
    /**
     * Method get_curriculo_disciplina
     * Sample of usage: $curriculo_disciplina_area->curriculo_disciplina->attribute;
     * @returns CurriculoDisciplina instance
     */
    public function get_curriculo_disciplina()
    {
        // loads the associated object
        if (empty($this->curriculo_disciplina))
            $this->curriculo_disciplina = new CurriculoDisciplina($this->curriculo_disciplina_id);
    
        // returns the associated object
        return $this->curriculo_disciplina;
    }
    
    
    /**
     * Method set_area_formacao
     * Sample of usage: $curriculo_disciplina_area->area_formacao = $object;
     * @param $object Instance of AreaFormacao
     */
    public function set_area_formacao(AreaFormacao $object)
    {
        $this->area_formacao = $object;
        $this->area_formacao_id = $object->id;
    }
    
    /**
     * Method get_area_formacao
     * Sample of usage: $curriculo_disciplina_area->area_formacao->attribute;
     * @returns AreaFormacao instance
     */
    public function get_area_formacao()
    {
        // loads the associated object
        if (empty($this->area_formacao))
            $this->area_formacao = new AreaFormacao($this->dados_area_formacao_id);
    
        // returns the associated object
        return $this->area_formacao;
    }
    


}
