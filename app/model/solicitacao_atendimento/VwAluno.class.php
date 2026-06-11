<?php
/**
 * VwAluno Active Record
 * @author  <your-name-here>
 */
class VwAluno extends TRecord
{
    const TABLENAME = 'vw_aluno';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $solicitacao_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('DataNascimento');
        parent::addAttribute('NomeAluno');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('MediaPI');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('AnoMatricula'); /////////
        parent::addAttribute('SemestreMatricula'); /////////
        parent::addAttribute('IdentificacaoMatricula');
        parent::addAttribute('Campus');
        parent::addAttribute('Periodo');
        parent::addAttribute('EtapaMatricula');
        parent::addAttribute('HabilitacaoCurso');
        parent::addAttribute('CodCurso');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('CPF');
        parent::addAttribute('Rg');
        parent::addAttribute('RgOrgaoExpedidor');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('Situacao');
        parent::addAttribute('ConfirmacaoMatricula');
        parent::addAttribute('SituacaoTesouraria');
        parent::addAttribute('DataMatricula');
        parent::addAttribute('Ingresso');
        parent::addAttribute('CodOperador');
    }

     /**
     * Method set_solicitacao_aluno
     * Sample of usage: $vw_aluno->solicitacao_aluno = $object;
     * @param $object Instance of SolicitacaoAluno
     */
    public function set_solicitacao_aluno(SolicitacaoAluno $object)
    {
        $this->solicitacao_aluno = $object;
        $this->solicitacao_aluno_id = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $vw_aluno->solicitacao_aluno->attribute;
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
