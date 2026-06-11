<?php
/**
 * TrechoProfessor Active Record
 * @author  <your-name-here>
 */
class TrechoProfessor extends TRecord
{
    const TABLENAME = 'trecho_professor';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_trecho');
        parent::addAttribute('distancia');
        parent::addAttribute('qtd_litro_diesel');
        parent::addAttribute('qtd_litro_etanol');
        parent::addAttribute('qtd_litro_gasolina');
        parent::addAttribute('data_reg');
    }


}
