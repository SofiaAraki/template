<?php
/**
 * CurriculoDisciplinaRequisitada Active Record
 * @author  <your-name-here>
 */
class CurriculoDisciplinaRequisitada extends TRecord
{
    const TABLENAME = 'curriculo_disciplina_requisitada';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $curriculo_disciplina_dependente;
    private $curriculo_disciplina_requisitada;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curriculo_disciplina_dependente_id');
        parent::addAttribute('curriculo_disciplina_requisitada_id');
    }

    

    public function set_curriculo_disciplina_dependente(CurriculoDisciplina $object)
    {
        $this->curriculo_disciplina_dependente = $object;
        $this->curriculo_disciplina_dependente_id = $object->id;
    }
    

    public function get_curriculo_disciplina_dependente()
    {
        if (empty($this->curriculo_disciplina_dependente))
            $this->curriculo_disciplina_dependente = new CurriculoDisciplina($this->curriculo_disciplina_dependente_id);
    
        return $this->curriculo_disciplina_dependente;
    }
    
    
    public function set_curriculo_disciplina_requisitada(CurriculoDisciplina $object)
    {
        $this->curriculo_disciplina_requisitada = $object;
        $this->curriculo_disciplina_requisitada_id = $object->id;
    }
    

    public function get_curriculo_disciplina_requisitada()
    {
        if (empty($this->curriculo_disciplina_requisitada))
            $this->curriculo_disciplina_requisitada = new CurriculoDisciplina($this->curriculo_disciplina_requisitada_id);
    
        return $this->curriculo_disciplina_requisitada;
    }


}
