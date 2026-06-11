<?php
/**
 * FiDisciplinasATADDP Active Record
 * @author  Felipe S. Teixeira
 * @author  Fernando Stuck
 */
class VwFiDisciplinasATADDP extends TRecord
{
    const TABLENAME = 'VW_DisciplinasATADDP';
    const PRIMARYKEY= 'CodDisciplinaChave';
    const IDPOLICY =  'max'; // {max, serial}

	use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Media');
        parent::addAttribute('Frequencia');
        parent::addAttribute('Resultado');
        parent::addAttribute('MediaProf');
        parent::addAttribute('MediaSem');
        parent::addAttribute('NotaExame');
        parent::addAttribute('CodDisciplinaChave');
        parent::addAttribute('Dispensa');
        parent::addAttribute('Opcao');
        parent::addAttribute('TipoDisciplina');
        parent::addAttribute('Etapa');
        parent::addAttribute('FrequenciaRequerida');
        parent::addAttribute('Cursando');
        parent::addAttribute('Tipo');
        parent::addAttribute('Ordem');
        parent::addAttribute('NomeDisciplina');
    }
}
