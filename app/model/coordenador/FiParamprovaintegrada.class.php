<?php
/**
 * FiParamprovaintegrada Active Record
 * @author  <your-name-here>
 */
class FiParamprovaintegrada extends TRecord
{
    const TABLENAME = 'FI_ParamProvaIntegrada';
    const PRIMARYKEY= 'CodParamProvaIntegrada';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_matriculaetapa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('TotalQuestoes');
        parent::addAttribute('Indice1');
        parent::addAttribute('Nota1');
        parent::addAttribute('Indice2');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('Nota2');
        parent::addAttribute('Indice3');
        parent::addAttribute('Nota3');
        parent::addAttribute('Indice4');
        parent::addAttribute('Nota4');
        parent::addAttribute('Indice5');
        parent::addAttribute('Nota5');
        parent::addAttribute('DataProva');
        parent::addAttribute('Modelo');
        parent::addAttribute('Fx1');
        parent::addAttribute('P1');
        parent::addAttribute('Fx2');
        parent::addAttribute('P2');
        parent::addAttribute('Fx3');
        parent::addAttribute('P3');
        parent::addAttribute('Fx4');
        parent::addAttribute('P4');
        parent::addAttribute('P5');
    }

    
    /**
     * Method set_fi_matriculaetapa
     * Sample of usage: $fi_paramprovaintegrada->fi_matriculaetapa = $object;
     * @param $object Instance of FiMatriculaetapa
     */
    public function set_fi_matriculaetapa(FiMatriculaetapa $object)
    {
        $this->fi_matriculaetapa = $object;
        $this->CodMatriculaEtapa = $object->id;
    }
    
    /**
     * Method get_fi_matriculaetapa
     * Sample of usage: $fi_paramprovaintegrada->fi_matriculaetapa->attribute;
     * @returns FiMatriculaetapa instance
     */
    public function get_fi_matriculaetapa()
    {
        // loads the associated object
        if (empty($this->fi_matriculaetapa))
            $this->fi_matriculaetapa = new FiMatriculaetapa($this->CodMatriculaEtapa);
    
        // returns the associated object
        return $this->fi_matriculaetapa;
    }
    
    public function get_turmaEtapa()
    {
        return new FiTurmaEtapa($this->CodTurmaetapa);
    }

}
