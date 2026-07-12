<?php

class FiTurmaPeriodoAvaliacoes extends TRecord
{
    const TABLENAME  = 'FI_Turma_PeriodoAvaliacoes';
    const PRIMARYKEY = 'CodTurmaPeriodoAvaliacoes';
    const IDPOLICY   = 'serial'; // mude para 'manual' se o banco não utilizar auto-incremento

    private $turma_etapa;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        // Mapeamento dos atributos da tabela
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('Avaliacao');
        parent::addAttribute('DataInicial');
        parent::addAttribute('DataFinal');
    }

    /**
     * Relacionamento com a Etapa da Turma (FI_Turma_etapa)
     * Retorna o objeto FITurmaEtapa associado a este período
     */
    public function get_turma_etapa()
    {
        if (empty($this->turma_etapa)) {
            // Lembre-se de criar a classe model 'FITurmaEtapa' correspondente à tabela 'FI_Turma_etapa'
            $this->turma_etapa = new FiTurmaEtapa($this->CodTurmaetapa);
        }
        return $this->turma_etapa;
    }
}