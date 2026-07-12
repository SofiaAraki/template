<?php

class FiDependencia extends TRecord
{
    const TABLENAME  = 'FI_Dependencia';
    const PRIMARYKEY = 'CodDependencia';
    const IDPOLICY   = 'serial'; // Altere para 'manual' se a chave não for auto-incremento

    private $disciplina;
    private $matriculaEtapa;
    private $matriculaInicial;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        // Mapeamento dos atributos da tabela
        parent::addAttribute('CodMatriculaInicial');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Ano');
        parent::addAttribute('Sem');
        parent::addAttribute('Etapa');
        parent::addAttribute('Tipo');
        parent::addAttribute('Previsao_N');
        parent::addAttribute('Previsao_F');
        parent::addAttribute('Freq_Etapa');
        parent::addAttribute('Nota_Etapa');
        parent::addAttribute('Situacao');
        parent::addAttribute('Ano_Dep');
        parent::addAttribute('Sem_Dep');
        parent::addAttribute('Freq_Dep');
        parent::addAttribute('Nota_Dep');
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

    /**
     * Relacionamento com a Matrícula por Etapa (FI_MatriculaEtapa)
     */
    public function get_matriculaEtapa()
    {
        if (empty($this->matriculaEtapa) && !empty($this->CodMatriculaEtapa)) {
            $this->matriculaEtapa = new FiMatriculaEtapa($this->CodMatriculaEtapa); // Ajuste o nome da classe conforme seu projeto
        }
        return $this->matriculaEtapa;
    }

    /**
     * Relacionamento com a Matrícula Inicial (FI_MatriculaInicial)
     */
    public function get_matriculaInicial()
    {
        if (empty($this->matriculaInicial)) {
            $this->matriculaInicial = new FiMatriculaInicial($this->CodMatriculaInicial); // Ajuste o nome da classe conforme seu projeto
        }
        return $this->matriculaInicial;
    }
}