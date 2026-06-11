<?php
/**
 * PrecosFfcl Active Record
 * @author  <your-name-here>
 */
class SolicitacaoFfcl extends TRecord
{
    const TABLENAME = 'solicitacao_ffcl';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $solicitacao_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_doc_ffcl');
        parent::addAttribute('preco_doc_ffcl');
    }

    
    /**
     * Method set_solicitacao_aluno
     * Sample of usage: $precos_ffcl->solicitacao_aluno = $object;
     * @param $object Instance of SolicitacaoAluno
     */
    public function set_solicitacao_aluno(SolicitacaoAluno $object)
    {
        $this->solicitacao_aluno = $object;
        $this->solicitacao_aluno_id = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $precos_ffcl->solicitacao_aluno->attribute;
     * @returns SolicitacaoAluno instance
     */
    public function get_solicitacao_aluno()
    {
        // loads the associated object
        if (empty($this->solicitacao_aluno))
            $this->solicitacao_aluno = new SolicitacaoAluno($this->solicitacao_aluno_id);
    
        // returns the associated object
        return $this->solicitacao_aluno;
    }
    


}
