<?php
/**
 * ViewEquivalencia Active Record (Apenas Leitura)
 */
class ViewEquivalencia extends TRecord
{
    const TABLENAME = 'view_equivalencia';
    const PRIMARYKEY= 'id_virtual';
    const IDPOLICY  = 'max'; 

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id_virtual');
        parent::addAttribute('nome_aluno');
        parent::addAttribute('grade_id');
        parent::addAttribute('cod_curso');
        parent::addAttribute('nome_curso');
        parent::addAttribute('total_disciplinas_aproveitadas');
        parent::addAttribute('ultima_atualizacao');
        parent::addAttribute('ultimo_system_user_id');
        parent::addAttribute('nome_ultimo_usuario');
    }
}