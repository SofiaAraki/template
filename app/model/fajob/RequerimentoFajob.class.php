<?php
/**
 * RequerimentoFajob Active Record
 * @author  <your-name-here>
 */
class RequerimentoFajob extends TRecord
{
    const TABLENAME = 'requerimento_fajob';
    const PRIMARYKEY= 'cod_inscricao_aluno';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_aluno');
        parent::addAttribute('cod_curso');
        parent::addAttribute('periodo_curso');
        parent::addAttribute('entidade');
        parent::addAttribute('cod_vestibular');
        parent::addAttribute('user_req');
        parent::addAttribute('data_reg');
    }


}
