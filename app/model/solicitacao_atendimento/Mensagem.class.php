<?php
/**
 * Mensagem Active Record
 * @author  <your-name-here>
 */
class Mensagem extends TRecord
{
    const TABLENAME = 'mensagem';
    const PRIMARYKEY= 'id_mensagem';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $solicitacao_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('solicitacaoaluno_id');
        parent::addAttribute('usuario');
        parent::addAttribute('conteudo');
        parent::addAttribute('anexo');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_solicitacao_aluno
     * Sample of usage: $mensagem->solicitacao_aluno = $object;
     * @param $object Instance of SolicitacaoAluno
     */
    public function set_solicitacao_aluno(SolicitacaoAluno $object)
    {
        $this->solicitacao_aluno = $object;
        $this->solicitacao_aluno_id = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $mensagem->solicitacao_aluno->attribute;
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
