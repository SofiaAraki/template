<?php
/**
 * DespesaProfessor Active Record
 * @author  <your-name-here>
 */
class DespesaProfessor extends TRecord
{
    const TABLENAME = 'despesa_professor';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $trecho_professor;
    private $system_user;
    private $despesa_professor_item;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('system_user_admin');
        parent::addAttribute('nome');
        parent::addAttribute('curso');
        parent::addAttribute('viagem_dobro');
        parent::addAttribute('trecho_id');
        parent::addAttribute('qtd_aulas');
        parent::addAttribute('qtd_dias');
        parent::addAttribute('obs');
        parent::addAttribute('data_reg');
        parent::addAttribute('situacao');
        parent::addAttribute('valor_total');
        parent::addAttribute('unidade');
        parent::addAttribute('filename');
        parent::addAttribute('total_dia');
        parent::addAttribute('total_percorrido');
        parent::addAttribute('custo_medio');
        parent::addAttribute('data_final');
        parent::addAttribute('id_analise');
        parent::addAttribute('id_parecer');
        parent::addAttribute('obs_analise');
        parent::addAttribute('obs_parecer');
    }

    
    /**
     * Method set_trecho_professor
     * Sample of usage: $despesa_professor->trecho_professor = $object;
     * @param $object Instance of TrechoProfessor
     */
    public function set_trecho_professor(TrechoProfessor $object)
    {
        $this->trecho_professor = $object;
        $this->trecho_id = $object->id;
    }
    
    /**
     * Method get_trecho_professor
     * Sample of usage: $despesa_professor->trecho_professor->attribute;
     * @returns TrechoProfessor instance
     */
    public function get_trecho_professor()
    {
        // loads the associated object
        if (empty($this->trecho_professor))
            $this->trecho_professor = new TrechoProfessor($this->trecho_id);
    
        // returns the associated object
        return $this->trecho_professor;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $despesa_professor->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $despesa_professor->system_user->attribute;
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

    public function set_analise(SystemUser $object)
    {
        $this->analise = $object;
        $this->id_analise = $object->id;
    }

    public function get_analise()
    {
        if (empty($this->analise))
            $this->analise = new SystemUser($this->id_analise);

        return $this->analise;
    }

    public function set_parecer(SystemUser $object)
    {
        $this->parecer = $object;
        $this->id_parecer = $object->id;
    }

    public function get_parecer()
    {
        if (empty($this->parecer))
            $this->parecer = new SystemUser($this->id_parecer);

        return $this->parecer;
    }
    
    
    /**
     * Method set_despesa_professor_item
     * Sample of usage: $despesa_professor->despesa_professor_item = $object;
     * @param $object Instance of DespesaProfessorItem
     */
    public function set_despesa_professor_item(DespesaProfessorItem $object)
    {
        $this->despesa_professor_item = $object;
        $this->despesa_professor_item_id = $object->id;
    }
    
    /**
     * Method get_despesa_professor_item
     * Sample of usage: $despesa_professor->despesa_professor_item->attribute;
     * @returns DespesaProfessorItem instance
     */
    public function get_despesa_professor_item()
    {
        // loads the associated object
        if (empty($this->despesa_professor_item))
            $this->despesa_professor_item = new DespesaProfessorItem($this->despesa_professor_item_id);
    
        // returns the associated object
        return $this->despesa_professor_item;
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related DespesaItem objects
        $repository = new TRepository('DespesaProfessorItem');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('despesa_id', '=', $id));
        $repository->delete($criteria);
            
        // delete the object itself
        parent::delete($id);
    }
    


}
