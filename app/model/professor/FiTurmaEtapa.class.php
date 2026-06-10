<?php
/**
 * FiTurmaEtapa Active Record
 * @author  <your-name-here>
 */
class FiTurmaEtapa extends TRecord
{
    const TABLENAME = 'FI_Turma_etapa';
    const PRIMARYKEY= 'CodTurmaetapa';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_aluno;
    private $fi_matriculaetapa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodOperador');
        parent::addAttribute('CodGradeEtapa');
        parent::addAttribute('Identificacao');
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('Sala');
        parent::addAttribute('Bloco');
        parent::addAttribute('Campus');
        parent::addAttribute('Periodo');
        parent::addAttribute('DataInicial');
        parent::addAttribute('DataFinal');
        parent::addAttribute('ProvaIntegrada');
        parent::addAttribute('ApontamentoFaltas');
        parent::addAttribute('Marca');
        parent::addAttribute('CodProfessor');
        parent::addAttribute('CodSistemaAvaliacao');
        parent::addAttribute('TCC');
        parent::addAttribute('AtivComp');
        parent::addAttribute('Estagio');
        parent::addAttribute('Recupera_Bim_Anterior');
        parent::addAttribute('AcessoMoodle');
        parent::addAttribute('Validade_Cartao');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $fi_turma_etapa->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $fi_turma_etapa->fi_aluno->attribute;
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
     * Method set_fi_matriculaetapa
     * Sample of usage: $fi_turma_etapa->fi_matriculaetapa = $object;
     * @param $object Instance of FiMatriculaetapa
     */
    public function set_fi_matriculaetapa(FiMatriculaetapa $object)
    {
        $this->fi_matriculaetapa = $object;
        $this->CodMatricualEtapa = $object->id;
    }
    
    /**
     * Method get_fi_matriculaetapa
     * Sample of usage: $fi_turma_etapa->fi_matriculaetapa->attribute;
     * @returns FiMatriculaetapa instance
     */
    public function get_fi_matriculaetapa()
    {
        // loads the associated object
        if (empty($this->fi_matriculaetapa))
            $this->fi_matriculaetapa = new FiMatriculaetapa($this->CodMatricualEtapa);
    
        // returns the associated object
        return $this->fi_matriculaetapa;
    }
    


}
