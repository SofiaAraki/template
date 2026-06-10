<?php
/**
 * FiCoordenador Active Record
 * @author  <your-name-here>
 */
class FiCoordenador extends TRecord
{
    const TABLENAME = 'FI_Coordenador';
    const PRIMARYKEY= 'CodCoordenador';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('QtdeAvaliacoesIntegradas');
        parent::addAttribute('DataInicial');
    }


}
