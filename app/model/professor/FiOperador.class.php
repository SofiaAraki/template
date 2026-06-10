<?php
/**
 * FiOperador Active Record
 * @author  <your-name-here>
 */
class FiOperador extends TRecord
{
    const TABLENAME = 'FI_Operador';
    const PRIMARYKEY= 'CodOperador';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Nome');
        parent::addAttribute('Login');
        parent::addAttribute('Senha');
        parent::addAttribute('Nivel');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('genesi');
        parent::addAttribute('requerimentobolsa');
        parent::addAttribute('sicob');
        parent::addAttribute('senhadelphi6');
        parent::addAttribute('DataValidadeSenha');
    }


}
