<?php
class FiAtribuicaoGradeTurma extends TRecord
{
    const TABLENAME  = 'FI_AtribuicaoGradeTurma';
    const PRIMARYKEY = 'CodAtribuicaoGradeTurma'; // Chave primária base
    const IDPOLICY   = 'serial'; 

    private $professor;
    private $grade_frente;

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        // Atributos correspondentes às colunas reais do banco de dados
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('CodAtribuicaoGradeTurma');
        parent::addAttribute('CodModalidadeProfessor');
        parent::addAttribute('Migracao_DisciplinaID');
    }

    /**
     * Relacionamento com o Professor
     * Permite usar $atribuicao->professor->NomeProfessor
     */
    public function get_professor()
    {
        if (empty($this->professor))
        {
            if (!empty($this->Codprofessor)) {
                $this->professor = new FiProfessor($this->Codprofessor);
            } else {
                $this->professor = new stdClass;
                $this->professor->Nome = "Sem Professor"; // Fallback caso seja nulo
            }
        }
        return $this->professor;
    }

    /**
     * Relacionamento com a Disciplina / Frente da Etapa
     * Permite usar $atribuicao->grade_frente->NomeDisciplina
     */
    public function get_grade_frente()
    {
        if (empty($this->grade_frente))
        {
            $this->grade_frente = new FiGradeDisciplinaEtapaFrente($this->CodGradeDisciplinaEtapa_Frente);
        }
        return $this->grade_frente;
    }
}