<?php
/**
 * ConteudoDiarioClasse Active Record
 * @author  <your-name-here>
 */
class ConteudoDiarioClasse extends TRecord
{
    const TABLENAME = 'conteudo_diario_classe';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod_disciplina');
        parent::addAttribute('cod_turma_etapa');
        parent::addAttribute('cod_professor');
        parent::addAttribute('data_aula');
        parent::addAttribute('conteudo');
        parent::addAttribute('apostila');
        parent::addAttribute('nome_disciplina');
        parent::addAttribute('nome_professor');
        parent::addAttribute('cod_curso');
        parent::addAttribute('cod_ies');
    }


}
