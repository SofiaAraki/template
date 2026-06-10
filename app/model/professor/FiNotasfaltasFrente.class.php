<?php
/**
 * FiNotasfaltasFrente Active Record
 * @author  <your-name-here>
 */
class FiNotasfaltasFrente extends TRecord
{
    const TABLENAME = 'FI_NotasFaltas_Frente';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_aluno;
    private $fi_disciplinas_atuais;
    private $fi_matriculaetapa;
    private $fi_dependencia;
    private $fi_disciplinas_adaptacao;
    private $vwalunosnotas;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('TipoDisciplina');
        parent::addAttribute('TipoNota');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('Avaliacao');
        parent::addAttribute('Peso4');
        parent::addAttribute('Credito2');
        parent::addAttribute('Peso3');
        parent::addAttribute('Nota5');
        parent::addAttribute('Adicional1');
        parent::addAttribute('Adicional4');
        parent::addAttribute('Credito1');
        parent::addAttribute('Credito4');
        parent::addAttribute('Nota3');
        parent::addAttribute('Nota1');
        parent::addAttribute('Peso1');
        parent::addAttribute('Adicional2');
        parent::addAttribute('Credito3');
        parent::addAttribute('Peso2');
        parent::addAttribute('Adicional3');
        parent::addAttribute('Credito5');
        parent::addAttribute('Adicional5');
        parent::addAttribute('Nota2');
        parent::addAttribute('Media');
        parent::addAttribute('Peso5');
        parent::addAttribute('Nota4');
        parent::addAttribute('Faltas');
        parent::addAttribute('FaltasComp');
        parent::addAttribute('Recupera_Bim_Anterior');
        parent::addAttribute('Nota_Ant_Recup');
        parent::addAttribute('Nota1_Recup');
        parent::addAttribute('Nota2_Recup');
        parent::addAttribute('Nota3_Recup');
        parent::addAttribute('Nota4_Recup');
        parent::addAttribute('Nota1_Ant_Recup');
        parent::addAttribute('Nota2_Ant_Recup');
        parent::addAttribute('Nota3_Ant_Recup');
        parent::addAttribute('Nota4_Ant_Recup');
        parent::addAttribute('CodOperador');
        parent::addAttribute('DataLancamento');
        parent::addAttribute('HoraLancamento');
        parent::addAttribute('ID');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $fi_notasfaltas_frente->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $fi_notasfaltas_frente->fi_aluno->attribute;
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
     * Method set_fi_disciplinas_atuais
     * Sample of usage: $fi_notasfaltas_frente->fi_disciplinas_atuais = $object;
     * @param $object Instance of FiDisciplinasAtuais
     */
    public function set_fi_disciplinas_atuais(FiDisciplinasAtuais $object)
    {
        $this->fi_disciplinas_atuais = $object;
        $this->CodDisciplina_atuais = $object->id;
    }
    
    /**
     * Method get_fi_disciplinas_atuais
     * Sample of usage: $fi_notasfaltas_frente->fi_disciplinas_atuais->attribute;
     * @returns FiDisciplinasAtuais instance
     */
    public function get_fi_disciplinas_atuais()
    {
        // loads the associated object
        if (empty($this->fi_disciplinas_atuais))
            $this->fi_disciplinas_atuais = new FiDisciplinasAtuais($this->CodDisciplina_atuais);
    
        // returns the associated object
        return $this->fi_disciplinas_atuais;
    }
    
    
    /**
     * Method set_fi_matriculaetapa
     * Sample of usage: $fi_notasfaltas_frente->fi_matriculaetapa = $object;
     * @param $object Instance of FiMatriculaetapa
     */
    public function set_fi_matriculaetapa(FiMatriculaetapa $object)
    {
        $this->fi_matriculaetapa = $object;
        $this->CodMatriculaEtapa = $object->id;
    }
    
    /**
     * Method get_fi_matriculaetapa
     * Sample of usage: $fi_notasfaltas_frente->fi_matriculaetapa->attribute;
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
     * Method set_fi_dependencia
     * Sample of usage: $fi_notasfaltas_frente->fi_dependencia = $object;
     * @param $object Instance of FiDependencia
     */
    public function set_fi_dependencia(FiDependencia $object)
    {
        $this->fi_dependencia = $object;
        $this->CodDependencia = $object->id;
    }
    
    /**
     * Method get_fi_dependencia
     * Sample of usage: $fi_notasfaltas_frente->fi_dependencia->attribute;
     * @returns FiDependencia instance
     */
    public function get_fi_dependencia()
    {
        // loads the associated object
        if (empty($this->fi_dependencia))
            $this->fi_dependencia = new FiDependencia($this->CodDependencia);
    
        // returns the associated object
        return $this->fi_dependencia;
    }
    
    
    /**
     * Method set_fi_disciplinas_adaptacao
     * Sample of usage: $fi_notasfaltas_frente->fi_disciplinas_adaptacao = $object;
     * @param $object Instance of FiDisciplinasAdaptacao
     */
    public function set_fi_disciplinas_adaptacao(FiDisciplinasAdaptacao $object)
    {
        $this->fi_disciplinas_adaptacao = $object;
        $this->CodDiscplina_Adaptacao = $object->id;
    }
    
    /**
     * Method get_fi_disciplinas_adaptacao
     * Sample of usage: $fi_notasfaltas_frente->fi_disciplinas_adaptacao->attribute;
     * @returns FiDisciplinasAdaptacao instance
     */
    public function get_fi_disciplinas_adaptacao()
    {
        // loads the associated object
        if (empty($this->fi_disciplinas_adaptacao))
            $this->fi_disciplinas_adaptacao = new FiDisciplinasAdaptacao($this->CodDiscplina_Adaptacao);
    
        // returns the associated object
        return $this->fi_disciplinas_adaptacao;
    }
    

    public function set_fi_gradedisciplnaetapa_frente(FiGradedisciplinaetapaFrente $object)
    {
        $this->fi_gradedisciplnaetapa_frente = $object;
        $this->CodGradeDisciplinaEtapa_Frente = $object->id;
    }
    
    /**
     * Method get_fi_disciplinas_adaptacao
     * Sample of usage: $fi_notasfaltas_frente->fi_disciplinas_adaptacao->attribute;
     * @returns FiDisciplinasAdaptacao instance
     */
    public function get_fi_gradedisciplnaetapa_frente()
    {
        // loads the associated object
        if (empty($this->fi_gradedisciplnaetapa_frente))
            $this->fi_gradedisciplnaetapa_frente = new FiGradedisciplinaetapaFrente($this->CodGradeDisciplinaEtapa_Frente);
    
        // returns the associated object
        return $this->fi_gradedisciplnaetapa_frente;
    }

        public function set_fi_disciplina(FiDisciplina $object)
    {
        $this->fi_disciplina = $object;
        $this->CodDisciplina = $object->id;
    }
    
    /**
     * Method get_fi_notasfaltas_frente
     * Sample of usage: $fi_disciplina->fi_notasfaltas_frente->attribute;
     * @returns FiNotasfaltasFrente instance
     */
    public function get_fi_disciplina()
    {
        // loads the associated object
        if (empty($this->fi_disciplina))
            $this->fi_disciplina = new FiDisciplina($this->CodDisciplina);
    
        // returns the associated object
        return $this->fi_disciplina;
    }


        public function set_vwalunosnotas(VwAlunosnotas $object)
    {
        $this->vwalunosnotas = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_notasfaltas_frente
     * Sample of usage: $fi_disciplina->fi_notasfaltas_frente->attribute;
     * @returns FiNotasfaltasFrente instance
     */
    public function get_vwalunosnotas()
    {
        // loads the associated object
        if (empty($this->vwalunosnotas))
            $this->vwalunosnotas = new VwAlunosnotas($this->Codaluno);
    
        // returns the associated object
        return $this->vwalunosnotas;
    }

}
