<?php
/**
 * MeuCurso Active Record
 * @author  <your-name-here>
 */
class MeuCurso extends TRecord
{
    const TABLENAME = 'meu_curso';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curso_id');
        parent::addAttribute('filename');
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('tipo');
        parent::addAttribute('data_reg');
    }


}
