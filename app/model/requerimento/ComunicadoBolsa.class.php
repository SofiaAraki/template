<?php
/**
 * ComunicadoBolsa Active Record
 * @author  <your-name-here>
 */
class ComunicadoBolsa extends TRecord
{
    const TABLENAME = 'comunicado_bolsa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_unit;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('opcao_bolsa');
        parent::addAttribute('ano_referencia');
        parent::addAttribute('titulo');
        parent::addAttribute('conteudo');
        parent::addAttribute('data_postagem');
        parent::addAttribute('data_expiracao');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $comunicado_bolsa->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $comunicado_bolsa->system_unit->attribute;
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
     * Method set_system_user
     * Sample of usage: $comunicado_bolsa->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $comunicado_bolsa->system_user->attribute;
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
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('ComunicadoBolsaParticipante');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('comunicado_id', '=', $id));
        $repository->delete($criteria);
        
        $repository = new TRepository('ComunicadoBolsaAceite');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('comunicado_id', '=', $id));
        $repository->delete($criteria);
    
        // delete the object itself
        parent::delete($id);
    }


}
