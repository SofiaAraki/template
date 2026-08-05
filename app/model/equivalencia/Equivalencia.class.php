<?php
/**
 * Equivalencia Active Record
 */
class Equivalencia extends TRecord
{
    const TABLENAME = 'equivalencia';
    const PRIMARYKEY= 'id';
    const IDPOLICY  = 'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_aluno');
        parent::addAttribute('grade_id');
        parent::addAttribute('disciplina_grade_id');
        parent::addAttribute('disciplina_equivalente');
        parent::addAttribute('nota_equivalente');
        parent::addAttribute('data_lancamento');
        parent::addAttribute('system_user_id');
    }
}
