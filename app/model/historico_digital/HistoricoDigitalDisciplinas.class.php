<?php
/**
 * HistoricoDigitalDisciplinas Active Record
 * @author  <your-name-here>
 */
class HistoricoDigitalDisciplinas extends TRecord
{
    const TABLENAME = 'historico_digital_disciplinas';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    //use SystemChangeLogTrait;
    
    private $historico_digital;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('historico_digital_id');
        parent::addAttribute('ano');
        parent::addAttribute('semestre');
        parent::addAttribute('etapa');
        parent::addAttribute('tipo_entrada');
        parent::addAttribute('cod_disciplina');
        parent::addAttribute('nome_disciplina');
        parent::addAttribute('carga_horaria');
        parent::addAttribute('frequencia');
        parent::addAttribute('nota');
        parent::addAttribute('situacao');
        parent::addAttribute('forma_integralizacao');
        parent::addAttribute('cod_professor');
        parent::addAttribute('nome_professor');
        parent::addAttribute('titulacao_professor');
        parent::addAttribute('cod_disciplina_historico');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');                      
    }

    
    /**
     * Method set_historico_digital
     * Sample of usage: $historico_digital_disciplinas->historico_digital = $object;
     * @param $object Instance of HistoricoDigital
     */
    public function set_historico_digital(HistoricoDigital $object)
    {
        $this->historico_digital = $object;
        $this->historico_digital_id = $object->id;
    }
    
    /**
     * Method get_historico_digital
     * Sample of usage: $historico_digital_disciplinas->historico_digital->attribute;
     * @returns HistoricoDigital instance
     */
    public function get_historico_digital()
    {
        // loads the associated object
        if (empty($this->historico_digital))
            $this->historico_digital = new HistoricoDigital($this->historico_digital_id);
    
        // returns the associated object
        return $this->historico_digital;
    }
    


}
