<?php
/**
 * FiCurso Active Record
 * @author  <your-name-here>
 */
class FiCurso extends TRecord
{
    const TABLENAME = 'FI_Curso';
    const PRIMARYKEY= 'CodCurso';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodEntidade');
        parent::addAttribute('CodNivelCurso');
        parent::addAttribute('Nome');
        parent::addAttribute('sigla');
        parent::addAttribute('Codificacao');
        parent::addAttribute('Nomehistorico');
        parent::addAttribute('CodOperador_Coordenador');
    }


}
