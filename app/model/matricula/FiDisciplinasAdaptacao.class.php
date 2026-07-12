<?php

class FiDisciplinasAdaptacao extends TRecord
{
    const TABLENAME  = 'FI_Disciplinas_Adaptacao';
    const PRIMARYKEY = 'CodDisciplina_Adaptacao';
    const IDPOLICY   = 'serial'; // Altere para 'manual' se a chave primária não for auto-incremento

    private $turmaEtapa;
    private $matriculaEtapa;
    private $disciplina;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        // Mapeamento dos atributos da tabela
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Media');
        parent::addAttribute('Frequencia');
        parent::addAttribute('Resultado');
        parent::addAttribute('MediaProf');
        parent::addAttribute('MediaSem');
        parent::addAttribute('NotaExame');
    }

    /**
     * Relacionamento com a Turma / Etapa (FI_Turma_etapa)
     */
    public function get_turmaEtapa()
    {
        if (empty($this->turmaEtapa)) {
            $this->turmaEtapa = new FiTurmaEtapa($this->CodTurmaetapa);
        }
        return $this->turmaEtapa;
    }

    /**
     * Relacionamento com a Matrícula / Etapa (FI_MatriculaEtapa)
     */
    public function get_matriculaEtapa()
    {
        if (empty($this->matriculaEtapa)) {
            $this->matriculaEtapa = new FiMatriculaEtapa($this->CodMatriculaEtapa); // Ajuste o nome da classe de matrícula conforme seu projeto
        }
        return $this->matriculaEtapa;
    }

    /**
     * Relacionamento com a Disciplina (FI_Disciplina)
     */
    public function get_disciplina()
    {
        if (empty($this->disciplina)) {
            $this->disciplina = new FiDisciplina($this->CodDisciplina);
        }
        return $this->disciplina;
    }
}