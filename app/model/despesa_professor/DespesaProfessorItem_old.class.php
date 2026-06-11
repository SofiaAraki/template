<?php
/**
 * DespesaProfessorItem Active Record
 * @author  <your-name-here>
 */
class DespesaProfessorItem extends TRecord
{
    const TABLENAME = 'despesa_professor_item';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('despesa_id');
        parent::addAttribute('item_tipo');
        parent::addAttribute('data_despesa');
        parent::addAttribute('valor');
        parent::addAttribute('quantidade');
        parent::addAttribute('data_reg');
    }


}
