<?php
/**
 * FiNotasfaltas Active Record
 * @author  <your-name-here>
 */
class FiNotasfaltas extends TRecord
{
    const TABLENAME = 'FI_NotasFaltas';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('Avaliacao');
        parent::addAttribute('TipoDisciplina');
        parent::addAttribute('TipoNota');
        parent::addAttribute('Peso1');
        parent::addAttribute('Adicional1');
        parent::addAttribute('Credito1');
        parent::addAttribute('Nota1');
        parent::addAttribute('Peso2');
        parent::addAttribute('Adicional2');
        parent::addAttribute('Credito2');
        parent::addAttribute('Nota2');
        parent::addAttribute('Peso3');
        parent::addAttribute('Adicional3');
        parent::addAttribute('Credito3');
        parent::addAttribute('Nota3');
        parent::addAttribute('Peso4');
        parent::addAttribute('Adicional4');
        parent::addAttribute('Credito4');
        parent::addAttribute('Nota4');
        parent::addAttribute('Peso5');
        parent::addAttribute('Adicional5');
        parent::addAttribute('Credito5');
        parent::addAttribute('Nota5');
        parent::addAttribute('Media');
        parent::addAttribute('Faltas');
        parent::addAttribute('FaltasComp');
        parent::addAttribute('Nota1_Ant_Recup');
        parent::addAttribute('Nota2_Ant_Recup');
        parent::addAttribute('Nota3_Ant_Recup');
        parent::addAttribute('Nota4_Ant_Recup');
        parent::addAttribute('Nota1_Recup');
        parent::addAttribute('Nota2_Recup');
        parent::addAttribute('Nota3_Recup');
        parent::addAttribute('Nota4_Recup');
        parent::addAttribute('CodOperador');
        parent::addAttribute('DataLancamento');
        parent::addAttribute('HoraLancamento');
    }


}
