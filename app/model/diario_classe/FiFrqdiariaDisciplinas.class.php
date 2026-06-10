<?php
/**
 * FiFrqdiariaDisciplinas Active Record
 * @author  <your-name-here>
 */
class FiFrqdiariaDisciplinas extends TRecord
{
    const TABLENAME = 'FI_FrqDiaria_Disciplinas';
    const PRIMARYKEY= 'CodFrqDiaria_Disciplinas';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $fi_frqdiaria;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('CodFrqDiaria');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Aula');
        parent::addAttribute('Freq');
        parent::addAttribute('CodOperador');
        parent::addAttribute('DataLancamento');
        parent::addAttribute('HoraLancamento');
        parent::addAttribute('obs_aula_colegio');
        parent::addAttribute('CodProfessor');
    }

    
    /**
     * Method set_fi_frqdiaria
     * Sample of usage: $fi_frqdiaria_disciplinas->fi_frqdiaria = $object;
     * @param $object Instance of FiFrqdiaria
     */
    public function set_fi_frqdiaria(FiFrqdiaria $object)
    {
        $this->fi_frqdiaria = $object;
        $this->CodFrqDiaria = $object->CodFrqDiaria;
    }
    
    /**
     * Method get_fi_frqdiaria
     * Sample of usage: $fi_frqdiaria_disciplinas->fi_frqdiaria->attribute;
     * @returns FiFrqdiaria instance
     */
    public function get_fi_frqdiaria()
    {
        // loads the associated object
        if (empty($this->fi_frqdiaria))
            $this->fi_frqdiaria = new FiFrqdiaria($this->CodFrqDiaria);
    
        // returns the associated object
        return $this->fi_frqdiaria;
    }
    


}
