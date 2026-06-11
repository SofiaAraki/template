<?php
/**
 * CurriculoDisciplinaEtiqueta Active Record
 * @author  <your-name-here>
 */
class CurriculoDisciplinaEtiqueta extends TRecord
{
    const TABLENAME = 'curriculo_disciplina_etiqueta';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $curriculo_disciplina;
    private $etiqueta;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curriculo_disciplina_id');
        parent::addAttribute('dados_etiqueta_id');
        parent::addAttribute('ch_hora_aula');
        parent::addAttribute('ch_hora_relogio');
    }

    
    /**
     * Method set_curriculo_disciplina
     * Sample of usage: $curriculo_disciplina_etiqueta->curriculo_disciplina = $object;
     * @param $object Instance of CurriculoDisciplina
     */
    public function set_curriculo_disciplina(CurriculoDisciplina $object)
    {
        $this->curriculo_disciplina = $object;
        $this->curriculo_disciplina_id = $object->id;
    }
    
    /**
     * Method get_curriculo_disciplina
     * Sample of usage: $curriculo_disciplina_etiqueta->curriculo_disciplina->attribute;
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
     * Method set_etiqueta
     * Sample of usage: $curriculo_disciplina_etiqueta->etiqueta = $object;
     * @param $object Instance of Etiqueta
     */
    public function set_etiqueta(Etiqueta $object)
    {
        $this->etiqueta = $object;
        $this->etiqueta_id = $object->id;
    }
    
    /**
     * Method get_etiqueta
     * Sample of usage: $curriculo_disciplina_etiqueta->etiqueta->attribute;
     * @returns Etiqueta instance
     */
    public function get_etiqueta()
    {
        // loads the associated object
        if (empty($this->etiqueta))
            $this->etiqueta = new Etiqueta($this->dados_etiqueta_id);
    
        // returns the associated object
        return $this->etiqueta;
    }
    


}
