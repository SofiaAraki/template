<?php
/**
 * VwPapeleta Active Record
 * @author  <your-name-here>
 */
class VwPapeleta extends TRecord
{
    const TABLENAME = 'VW_Papeleta';
    const PRIMARYKEY= 'CodGradeDisciplinaEtapa_Frente';
    const IDPOLICY =  'max'; // {max, serial}
    
    //use SystemChangeLogTrait;

    private $fi_turma_etapa;
    private $fi_notasfaltas_frente;
    private $fi_matriculaetapa;
    private $fi_gradedisciplinaetapa_frente;
    private $fi_disciplina;
    private $fi_aluno;
    private $fi_professor;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('frequencia');
        parent::addAttribute('mediasem');
        parent::addAttribute('resultado');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('Nome');
        parent::addAttribute('codaluno');
        parent::addAttribute('Nota1');
        parent::addAttribute('Faltas');
        parent::addAttribute('Avaliacao');
        parent::addAttribute('tipodisciplina');
        parent::addAttribute('FrequenciaRequerida');
        parent::addAttribute('Dispensa');
        parent::addAttribute('Ordem');
    }

    
    /**
     * Method set_fi_turma_etapa
     * Sample of usage: $vw_papeleta->fi_turma_etapa = $object;
     * @param $object Instance of FiTurmaEtapa
     */
    public function set_fi_turma_etapa(FiTurmaEtapa $object)
    {
        $this->fi_turma_etapa = $object;
        $this->CodTurmaetapa = $object->id;
    }
    
    /**
     * Method get_fi_turma_etapa
     * Sample of usage: $vw_papeleta->fi_turma_etapa->attribute;
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
     * Method set_fi_notasfaltas_frente
     * Sample of usage: $vw_papeleta->fi_notasfaltas_frente = $object;
     * @param $object Instance of FiNotasfaltasFrente
     */
    public function set_fi_notasfaltas_frente(FiNotasfaltasFrente $object)
    {
        $this->fi_notasfaltas_frente = $object;
        $this->ID = $object->id;
    }
    
    /**
     * Method get_fi_notasfaltas_frente
     * Sample of usage: $vw_papeleta->fi_notasfaltas_frente->attribute;
     * @returns FiNotasfaltasFrente instance
     */
    public function get_fi_notasfaltas_frente()
    {
        // loads the associated object
        if (empty($this->fi_notasfaltas_frente))
            $this->fi_notasfaltas_frente = new FiNotasfaltasFrente($this->ID);
    
        // returns the associated object
        return $this->fi_notasfaltas_frente;
    }
    
    
    /**
     * Method set_fi_matriculaetapa
     * Sample of usage: $vw_papeleta->fi_matriculaetapa = $object;
     * @param $object Instance of FiMatriculaetapa
     */
    public function set_fi_matriculaetapa(FiMatriculaetapa $object)
    {
        $this->fi_matriculaetapa = $object;
        $this->CodMatriculaEtapa = $object->id;
    }
    
    /**
     * Method get_fi_matriculaetapa
     * Sample of usage: $vw_papeleta->fi_matriculaetapa->attribute;
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
     * Method set_fi_gradedisciplinaetapa_frente
     * Sample of usage: $vw_papeleta->fi_gradedisciplinaetapa_frente = $object;
     * @param $object Instance of FiGradedisciplinaetapaFrente
     */
    public function set_fi_gradedisciplinaetapa_frente(FiGradedisciplinaetapaFrente $object)
    {
        $this->fi_gradedisciplinaetapa_frente = $object;
        $this->CodGradeDisciplinaEtapa_Frente = $object->id;
    }
    
    /**
     * Method get_fi_gradedisciplinaetapa_frente
     * Sample of usage: $vw_papeleta->fi_gradedisciplinaetapa_frente->attribute;
     * @returns FiGradedisciplinaetapaFrente instance
     */
    public function get_fi_gradedisciplinaetapa_frente()
    {
        // loads the associated object
        if (empty($this->fi_gradedisciplinaetapa_frente))
            $this->fi_gradedisciplinaetapa_frente = new FiGradedisciplinaetapaFrente($this->CodGradeDisciplinaEtapa_Frente);
    
        // returns the associated object
        return $this->fi_gradedisciplinaetapa_frente;
    }
    
    
    /**
     * Method set_fi_disciplina
     * Sample of usage: $vw_papeleta->fi_disciplina = $object;
     * @param $object Instance of FiDisciplina
     */
    public function set_fi_disciplina(FiDisciplina $object)
    {
        $this->fi_disciplina = $object;
        $this->CodDisciplina = $object->id;
    }
    
    /**
     * Method get_fi_disciplina
     * Sample of usage: $vw_papeleta->fi_disciplina->attribute;
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
     * Sample of usage: $vw_papeleta->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $vw_papeleta->fi_aluno->attribute;
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
     * Method set_fi_professor
     * Sample of usage: $vw_papeleta->fi_professor = $object;
     * @param $object Instance of FiProfessor
     */
    public function set_fi_professor(FiProfessor $object)
    {
        $this->fi_professor = $object;
        $this->Codprofessor = $object->id;
    }
    
    /**
     * Method get_fi_professor
     * Sample of usage: $vw_papeleta->fi_professor->attribute;
     * @returns FiProfessor instance
     */
    public function get_fi_professor()
    {
        // loads the associated object
        if (empty($this->fi_professor))
            $this->fi_professor = new FiProfessor($this->Codprofessor);
    
        // returns the associated object
        return $this->fi_professor;
    }
    


}
