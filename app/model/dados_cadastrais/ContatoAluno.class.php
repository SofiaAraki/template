<?php
/**
 * ContatoAluno Active Record
 * @author  <your-name-here>
 */
class ContatoAluno extends TRecord
{
    const TABLENAME = 'contato_aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod_aluno');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('cep');
        parent::addAttribute('telefone_1');
        parent::addAttribute('telefone_2');
        parent::addAttribute('telefone_3');
        parent::addAttribute('contato_whatsapp');
        parent::addAttribute('email');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $contato_aluno->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->fi_aluno_id = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $contato_aluno->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_fi_aluno()
    {
        // loads the associated object
        if (empty($this->fi_aluno))
            $this->fi_aluno = new FiAluno($this->fi_aluno_id);
    
        // returns the associated object
        return $this->fi_aluno;
    }
    


}
