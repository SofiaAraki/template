<?php
/**
 * PrecosCnsc Active Record
 * @author  <your-name-here>
 */
class SolicitacaoCnsc extends TRecord
{
    const TABLENAME = 'solicitacao_cnsc';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_doc_cnsc');
        parent::addAttribute('preco_doc_cnsc');
        parent::addAttribute('ativo');
    }


}
