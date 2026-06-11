<?php
/**
 * QuestionarioPeriodo Active Record
 * @author  <your-name-here>
 */
class QuestionarioPeriodo extends TRecord
{
    const TABLENAME = 'questionario_periodo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_user;
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('questionario_id');
        parent::addAttribute('titulo');
        parent::addAttribute('ano');
        parent::addAttribute('semestre');
        parent::addAttribute('inicio');
        parent::addAttribute('termino');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('descricao');
        parent::addAttribute('mostra_disciplina');
        parent::addAttribute('publico');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $questionario_periodo->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $questionario_periodo->system_user->attribute;
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
     * Method set_system_unit
     * Sample of usage: $questionario_periodo->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $questionario_periodo->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }






      /**
     * Method set_questionario
     * Sample of usage: $questionario_periodo->questionario = $object;
     * @param $object Instance of Questionario
     */
    public function set_questionario(Questionario $object)
    {
        $this->questionario = $object;
        $this->questionario_id = $object->id;
    }
    
    /**
     * Method get_questionario
     * Sample of usage: $questionario_periodo->questionario->attribute;
     * @returns Questionario instance
     */
    public function get_questionario()
    {
        // loads the associated object
        if (empty($this->questionario))
            $this->questionario = new Questionario($this->questionario_id);
    
        // returns the associated object
        return $this->questionario;
    }
    


}
