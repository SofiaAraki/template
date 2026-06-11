<?php
/**
 * CurriculoDisciplina Active Record
 * @author  <your-name-here>
 */
class CurriculoDisciplina extends TRecord
{
    const TABLENAME = 'curriculo_disciplina';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $curriculo_digital;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curriculo_id');
        parent::addAttribute('tipo');
        parent::addAttribute('opcao_disciplina');
        parent::addAttribute('cod_disciplina');
        parent::addAttribute('cod_disciplina_grade_etapa');
        parent::addAttribute('cod_disciplina_curriculo');
        parent::addAttribute('nome');
        parent::addAttribute('etapa');
        parent::addAttribute('opcao_carga_horaria');
        parent::addAttribute('ch_hora_aula');
        parent::addAttribute('ch_hora_relogio');
        parent::addAttribute('ementa');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_curriculo_digital
     * Sample of usage: $curriculo_disciplina->curriculo_digital = $object;
     * @param $object Instance of CurriculoDigital
     */
    public function set_curriculo_digital(CurriculoDigital $object)
    {
        $this->curriculo_digital = $object;
        $this->curriculo_digital_id = $object->id;
    }
    
    /**
     * Method get_curriculo_digital
     * Sample of usage: $curriculo_disciplina->curriculo_digital->attribute;
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
    

    public function getCurriculoDisciplinaRequisitada()
    {
        return CurriculoDisciplinaRequisitada::where('curriculo_disciplina_dependente_id', '=', $this->id)->load();
    }
    
    
    public function getCurriculoDisciplinaEtiqueta()
    {
        return CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $this->id)->load();
    }
    
    
    public function getCurriculoDisciplinaArea()
    {
        return CurriculoDisciplinaArea::where('curriculo_disciplina_id', '=', $this->id)->load();
    }
}
