<?php
/**
 * ContatoDadosAlunoDoc Active Record
 * @author  <your-name-here>
 */
class ContratoDadosAlunoDoc extends TRecord
{
    const TABLENAME = 'contrato_dados_aluno_doc';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $contrato_dados_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('contrato_aluno_id');
        parent::addAttribute('image');
        parent::addAttribute('contrato_aluno_codaluno');
        parent::addAttribute('data_reg');
        parent::addAttribute('contrato_assinado_ies');
    }

    
    /**
     * Method set_contrato_dados_aluno
     * Sample of usage: $contato_dados_aluno_doc->contrato_dados_aluno = $object;
     * @param $object Instance of ContratoDadosAluno
     */
    public function set_contrato_dados_aluno(ContratoDadosAluno $object)
    {
        $this->contrato_dados_aluno = $object;
        $this->id = $object->id;
    }
    
    /**
     * Method get_contrato_dados_aluno
     * Sample of usage: $contato_dados_aluno_doc->contrato_dados_aluno->attribute;
     * @returns ContratoDadosAluno instance
     */
    public function get_contrato_dados_aluno()
    {
        // loads the associated object
        if (empty($this->contrato_dados_aluno))
            $this->contrato_dados_aluno = new ContratoDadosAluno($this->id);
    
        // returns the associated object
        return $this->contrato_dados_aluno;
    }
    


}
