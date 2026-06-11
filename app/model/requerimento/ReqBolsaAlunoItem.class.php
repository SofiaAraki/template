<?php
/**
 * ReqBolsaAlunoItem Active Record
 * @author  <your-name-here>
 */
class ReqBolsaAlunoItem extends TRecord
{
    const TABLENAME = 'req_bolsa_aluno_item';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('item_membro');
        parent::addAttribute('nome');
        parent::addAttribute('idade');
        parent::addAttribute('profissao');
        parent::addAttribute('salario');
        parent::addAttribute('local_trabalho');
        parent::addAttribute('req_bolsa_aluno_id');
        parent::addAttribute('data_reg');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
    }


}
