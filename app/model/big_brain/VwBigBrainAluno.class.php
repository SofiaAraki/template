<?php
/**
 * VwBigBrainAluno Active Record
 * @author  <your-name-here>
 */
class VwBigBrainAluno extends TRecord
{
    const TABLENAME = 'Vw_BigBrainAluno';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('NomeAluno');
        parent::addAttribute('AnoMatricula');
        parent::addAttribute('SituacaoMatricula');
        parent::addAttribute('CodCurso');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('IdentificacaoMatricula');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('NomeDisciplina');
    }


}
