<?php

class Vw_DiarioClasseProfessor extends TRecord
{
    const TABLENAME  = 'VW_DiarioClasseProfessor';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('Data');
        parent::addAttribute('CodCurso');
        parent::addAttribute('AnoTurma');
        parent::addAttribute('CodProfessor');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('CodTurmaEtapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('DiaSemana');
        parent::addAttribute('id');
        parent::addAttribute('conteudo');
        parent::addAttribute('FrequenciaLancada');
    }
}