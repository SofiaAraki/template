<?php

class VwRelatorioLacamentoNotas extends TRecord
{
    const TABLENAME = 'VW_RelatorioLacamentoNotas';
    const PRIMARYKEY = 'CodDisciplina';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('CodCurso');
        parent::addAttribute('Periodo');
        parent::addAttribute('Etapa');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('Nota_1_Bimestre');
        parent::addAttribute('Nota_2_Bimestre');
    }
}