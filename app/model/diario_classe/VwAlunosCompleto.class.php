<?php
/**
 * VwAlunosCompleto Active Record
 * @author  <your-name-here>
 */
class VwAlunosCompleto extends TRecord
{
    const TABLENAME = 'Vw_Alunos_NotasCompleto';
    const PRIMARYKEY= 'CodTurmaetapa';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Nome');
        parent::addAttribute('Codaluno');
        parent::addAttribute('Nomeusual');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Identificacao');
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('CodCurso');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('Resultado');
        parent::addAttribute('TipoDis');
        parent::addAttribute('Ordem');
        parent::addAttribute('Situacao');
        parent::addAttribute('ConfirmacaoMatricula');
    }


}
