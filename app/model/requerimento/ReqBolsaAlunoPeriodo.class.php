<?php
/**
 * ReqBolsaAlunoPeriodo Active Record
 * @author  <your-name-here>
 */
class ReqBolsaAlunoPeriodo extends TRecord
{
    const TABLENAME = 'req_bolsa_aluno_periodo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_inicio');
        parent::addAttribute('data_fim');
        parent::addAttribute('data_reg');
    }


}
