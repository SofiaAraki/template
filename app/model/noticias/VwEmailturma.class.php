<?php
/**
 * VwEmailturma Active Record
 * @author  <your-name-here>
 */
class VwEmailturma extends TRecord
{
    const TABLENAME = 'VW_EmailTurma';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('Identificacao');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('ConfirmacaoMatricula');
        parent::addAttribute('NomeAluno');
        parent::addAttribute('Email');
    }


}
