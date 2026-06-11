<?php
/**
 * FiMatriculaetapa Active Record
 * @author  Felipe S. Teixeira
 * @author  Fernando Stuck
 */
class FiMatriculaetapa extends TRecord
{
    const TABLENAME = 'FI_MatriculaEtapa';
    const PRIMARYKEY= 'CodMatriculaEtapa';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodMatriculaInicial');
        parent::addAttribute('CodOperador');
        parent::addAttribute('Codaluno');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('Ingresso');
        parent::addAttribute('Situacao');
        parent::addAttribute('SituacaoData');
        parent::addAttribute('QtdeDisciplinaEtapa');
        parent::addAttribute('QtdeDependenciaEtapa');
        parent::addAttribute('QtdeAdaptacaoEtapa');
        parent::addAttribute('ResultadoFinal');
        parent::addAttribute('ResultadoQtdeDependencia');
        parent::addAttribute('DataMatricula');
        parent::addAttribute('ConfirmacaoMatricula');
        parent::addAttribute('DataAtualizacao');
        parent::addAttribute('TotalAcertosPI');
        parent::addAttribute('MediaPI');
        parent::addAttribute('CodContrato');
        parent::addAttribute('SituacaoTesouraria');
        parent::addAttribute('NReg');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('NumeroSeq');
        parent::addAttribute('MediaFreq');
        parent::addAttribute('Observacao1');
        parent::addAttribute('Observacao2');
        parent::addAttribute('Observacao3');
        parent::addAttribute('PercentualPI');
        parent::addAttribute('Observacao');
        parent::addAttribute('CodTipoResultado');
        parent::addAttribute('NotaNI');
        parent::addAttribute('SituacaoOutros');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $fi_matriculaetapa->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $fi_matriculaetapa->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_fi_aluno()
    {
        // loads the associated object
        if (empty($this->fi_aluno))
            $this->fi_aluno = new FiAluno($this->Codaluno);
    
        // returns the associated object
        return $this->fi_aluno;
    }
    


}
