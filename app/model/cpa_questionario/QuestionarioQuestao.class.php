<?php
/**
 * QuestionarioQuestao Active Record
 * @author  <your-name-here>
 */
class QuestionarioQuestao extends TRecord
{
    const TABLENAME = 'questionario_questao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('questionario_id');
        parent::addAttribute('tipo');
        parent::addAttribute('conteudo');
        parent::addAttribute('obrigatorio');
        parent::addAttribute('num_questao');
    }


}
