<?php
/**
 * ConteudoProgramaticoItem Active Record
 * @author  <your-name-here>
 */
class ConteudoProgramaticoItem extends TRecord
{
    const TABLENAME = 'conteudo_programatico_item';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('conteudo_programatico_id');
        parent::addAttribute('data_aula');
        parent::addAttribute('conteudo');
    }


}
