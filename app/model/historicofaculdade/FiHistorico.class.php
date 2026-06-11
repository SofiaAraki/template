<?php
/**
 * FiHistorico Active Record
 * @author  <your-name-here>
 */
class FiHistorico extends TRecord
{
    const TABLENAME = 'FI_Historico';
    const PRIMARYKEY= 'codhistorico';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_coordenador;
    private $fi_aluno;
    private $fi_curso;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodCurso');
        parent::addAttribute('Codaluno');
        parent::addAttribute('CodOperador');
        parent::addAttribute('ObservacaoCadastral1');
        parent::addAttribute('ObservacaoCadastral2');
        parent::addAttribute('ObservacaoCadastral3');
        parent::addAttribute('ObservacaoCadastral4');
        parent::addAttribute('ObservacaoCadastral5');
        parent::addAttribute('ObservacaoFinais1');
        parent::addAttribute('ObservacaoFinais2');
        parent::addAttribute('ObservacaoFinais3');
        parent::addAttribute('ObservacaoFinais4');
        parent::addAttribute('ObservacaoFinais5');
        parent::addAttribute('DataConclusaoCurso');
        parent::addAttribute('DataColacaoGrau');
        parent::addAttribute('DataExpedicaoDiploma');
        parent::addAttribute('DataAtualizacao');
        parent::addAttribute('Atualizacao');
        parent::addAttribute('DataVestibular');
        parent::addAttribute('DataVestibExt');
        parent::addAttribute('DataConclEMExt');
        parent::addAttribute('CodHistoricoLayOut');
        parent::addAttribute('TRFAno');
        parent::addAttribute('TRFData');
        parent::addAttribute('TRFBim');
        parent::addAttribute('TRFObs');
        parent::addAttribute('TransfNovaPag');
        parent::addAttribute('dataexphistorico');
        parent::addAttribute('SituacaoEnade');
    }

    
    /**
     * Method set_fi_coordenador
     * Sample of usage: $fi_historico->fi_coordenador = $object;
     * @param $object Instance of FiCoordenador
     */
    public function set_fi_coordenador(FiCoordenador $object)
    {
        $this->fi_coordenador = $object;
        $this->fi_coordenador_MUDAr = $object->id;
    }
    
    /**
     * Method get_fi_coordenador
     * Sample of usage: $fi_historico->fi_coordenador->attribute;
     * @returns FiCoordenador instance
     */
    public function get_fi_coordenador()
    {
        // loads the associated object
        if (empty($this->fi_coordenador))
            $this->fi_coordenador = new FiCoordenador($this->fi_coordenador_MUDAr);
    
        // returns the associated object
        return $this->fi_coordenador;
    }

     /**
     * Method set_fi_aluno
     * Sample of usage: $vw_alunosnotas->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_aluno(FiAluno $object)
    {
        $this->aluno = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $vw_alunosnotas->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_aluno()
    {
        // loads the associated object
        if (empty($this->aluno))
            $this->aluno = new FiAluno($this->Codaluno);
    
        // returns the associated object
        return $this->aluno;
    }

    public function set_curso(FiCurso $object)
    {
        $this->curso = $object;
        $this->CodCurso = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $vw_alunosnotas->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_curso()
    {
        // loads the associated object
        if (empty($this->curso))
            $this->curso = new FiCurso($this->CodCurso);
    
        // returns the associated object
        return $this->aluno;
    }
    
    
    /*Associação para histórico digital*/
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->Codaluno = $object->Codaluno;
    }
    
    
    public function get_fi_aluno()
    {
        if (empty($this->fi_aluno))
            $this->fi_aluno = new FiAluno($this->Codaluno);
    
        return $this->fi_aluno;
    }
    
    
    public function set_fi_curso(FiCurso $object)
    {
        $this->fi_curso = $object;
        $this->CodCurso = $object->CodCurso;
    }
    
    
    public function get_fi_curso()
    {
        if (empty($this->fi_curso))
            $this->fi_curso = new FiCurso($this->CodCurso);
    
        return $this->fi_curso;
    }
    
}
