<?php
/**
 * QuestionarioResposta Active Record
 * @author  <your-name-here>
 */
class QuestionarioResposta extends TRecord
{
    const TABLENAME = 'questionario_resposta';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('questionario_periodo_id');
        parent::addAttribute('questionario_id');
        parent::addAttribute('questao_id');
        parent::addAttribute('alternativa_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('cod_disciplina');
        parent::addAttribute('cod_professor');
        parent::addAttribute('ano');
        parent::addAttribute('semestre');
        parent::addAttribute('conteudo_alternativa');
        parent::addAttribute('num_questao');
        parent::addAttribute('cod_curso');
    }


}
