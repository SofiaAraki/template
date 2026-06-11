<?php
/**
 * QuestionarioAlternativa Active Record
 * @author  <your-name-here>
 */
class QuestionarioAlternativa extends TRecord
{
    const TABLENAME = 'questionario_alternativa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('questao_id');
        parent::addAttribute('conteudo');
    }


}
