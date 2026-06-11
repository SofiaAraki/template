<?php
/**
 * ReqBolsaAlunoDespesa Active Record
 * @author  <your-name-here>
 */
class ReqBolsaAlunoDespesa extends TRecord
{
    const TABLENAME = 'req_bolsa_aluno_despesa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('item_tipo');
        parent::addAttribute('valor');
        parent::addAttribute('req_bolsa_aluno_id');
        parent::addAttribute('data_reg');
    }


}
