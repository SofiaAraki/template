<?php
/**
 * VwAlunosnotas Active Record
 * @author  <your-name-here>
 */
class VwAlunosnotas extends TRecord
{
    const TABLENAME = 'VW_AlunosNotas';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;

    private $fi_turma_etapa;
    private $fi_matriculaetapa;
    private $fi_disciplina;
    private $fi_aluno;
    private $fi_notasfaltas_frente;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Nome');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Resultado');
        parent::addAttribute('TipoDis');
        parent::addAttribute('Ordem');
    }

    
    /**
     * Method set_fi_turma_etapa
     * Sample of usage: $vw_alunosnotas->fi_turma_etapa = $object;
     * @param $object Instance of FiTurmaEtapa
     */
    public function set_fi_turma_etapa(FiTurmaEtapa $object)
    {
        $this->fi_turma_etapa = $object;
        $this->CodTurmaetapa = $object->id;
    }
    
    /**
     * Method get_fi_turma_etapa
     * Sample of usage: $vw_alunosnotas->fi_turma_etapa->attribute;
     * @returns FiTurmaEtapa instance
     */
    public function get_fi_turma_etapa()
    {
        // loads the associated object
        if (empty($this->fi_turma_etapa))
            $this->fi_turma_etapa = new FiTurmaEtapa($this->CodTurmaetapa);
    
        // returns the associated object
        return $this->fi_turma_etapa;
    }
    
    
    /**
     * Method set_fi_matriculaetapa
     * Sample of usage: $vw_alunosnotas->fi_matriculaetapa = $object;
     * @param $object Instance of FiMatriculaetapa
     */
    public function set_fi_matriculaetapa(FiMatriculaetapa $object)
    {
        $this->fi_matriculaetapa = $object;
        $this->CodMatriculaEtapa = $object->id;
    }
    
    /**
     * Method get_fi_matriculaetapa
     * Sample of usage: $vw_alunosnotas->fi_matriculaetapa->attribute;
     * @returns FiMatriculaetapa instance
     */
    public function get_fi_matriculaetapa()
    {
        // loads the associated object
        if (empty($this->fi_matriculaetapa))
            $this->fi_matriculaetapa = new FiMatriculaetapa($this->CodMatriculaEtapa);
    
        // returns the associated object
        return $this->fi_matriculaetapa;
    }
    
    
    /**
     * Method set_fi_disciplina
     * Sample of usage: $vw_alunosnotas->fi_disciplina = $object;
     * @param $object Instance of FiDisciplina
     */
    public function set_fi_disciplina(FiDisciplina $object)
    {
        $this->fi_disciplina = $object;
        $this->CodDisciplina = $object->id;
    }
    
    /**
     * Method get_fi_disciplina
     * Sample of usage: $vw_alunosnotas->fi_disciplina->attribute;
     * @returns FiDisciplina instance
     */
    public function get_fi_disciplina()
    {
        // loads the associated object
        if (empty($this->fi_disciplina))
            $this->fi_disciplina = new FiDisciplina($this->CodDisciplina);
    
        // returns the associated object
        return $this->fi_disciplina;
    }
    
    
    /**
     * Method set_fi_aluno
     * Sample of usage: $vw_alunosnotas->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $vw_alunosnotas->fi_aluno->attribute;
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
    
    
    /**
     * Method set_fi_notasfaltas_frente
     * Sample of usage: $vw_alunosnotas->fi_notasfaltas_frente = $object;
     * @param $object Instance of FiNotasfaltasFrente
     */
    public function set_fi_notasfaltas_frente(FiNotasfaltasFrente $object)
    {
        $this->fi_notasfaltas_frente = $object;
        $this->CodDisciplina = $object->id;
    }
    
    /**
     * Method get_fi_notasfaltas_frente
     * Sample of usage: $vw_alunosnotas->fi_notasfaltas_frente->attribute;
     * @returns FiNotasfaltasFrente instance
     */
    public function get_fi_notasfaltas_frente()
    {
        // loads the associated object
        if (empty($this->fi_notasfaltas_frente))
            $this->fi_notasfaltas_frente = new FiNotasfaltasFrente($this->CodDisciplina);
    
        // returns the associated object
        return $this->fi_notasfaltas_frente;
    }
    


}
