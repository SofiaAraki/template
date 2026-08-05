<?php
/**
 * ConteudoProgramatico Active Record
 * @author  <your-name-here>
 */
class ConteudoProgramatico extends TRecord
{
    const TABLENAME = 'conteudo_programatico';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_user;
    private $conteudo_programatico_item;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('curso');
        parent::addAttribute('disciplina');
        parent::addAttribute('etapa');
        parent::addAttribute('turma');
        parent::addAttribute('status');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $conteudo_programatico->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $conteudo_programatico->system_user->attribute;
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
     * Method set_req_bolsa_aluno_despesa
     * Sample of usage: $req_bolsa_aluno->req_bolsa_aluno_despesa = $object;
     * @param $object Instance of ReqBolsaAlunoDespesa
     */
    public function set_conteudo_programatico_item(ConteudoProgramaticoItem $object)
    {
        $this->conteudo_programatico_item = $object;
        $this->conteudo_programatico_id = $object->id;
    }
    
    /**
     * Method get_req_bolsa_aluno_despesa
     * Sample of usage: $req_bolsa_aluno->req_bolsa_aluno_despesa->attribute;
     * @returns ReqBolsaAlunoDespesa instance
     */
    public function get_conteudo_programatico_item()
    {
        // loads the associated object
        if (empty($this->conteudo_programatico_item))
            $this->conteudo_programatico_item = new ConteudoProgramaticoItem($this->conteudo_programatico_id);
    
        // returns the associated object
        return $this->conteudo_programatico_item;
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related DespesaItem objects
        $repository = new TRepository('ConteudoProgramaticoItem');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('conteudo_programatico_id', '=', $id));
        $repository->delete($criteria);
    
        // delete the object itself
        parent::delete($id);
    }
    

}
