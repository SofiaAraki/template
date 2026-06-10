<?php
/**
 * VwHorariocalendario Active Record
 * @author  <your-name-here>
 */
class VwHorariocalendario extends TRecord
{
    const TABLENAME = 'Vw_HorarioCalendario';
    const PRIMARYKEY= 'CodCalendarioCurso';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $vw_horarioprofessor;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Data');
        parent::addAttribute('DiaSemana');
        parent::addAttribute('NomeDiaSemana');
        parent::addAttribute('HoraAula');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('CodCurso');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodGradeDisciplinaEtapa');
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('Bimestre');
        parent::addAttribute('Identificacao');
        parent::addAttribute('Periodo');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('Etapa');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('NumeroOrdemAula');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('NomeFantasia');
        parent::addAttribute('Letivo');
        parent::addAttribute('Expr1');
        parent::addAttribute('Expr2');
        parent::addAttribute('DataInicio1Bim');
        parent::addAttribute('DataInicio2Bim');
        parent::addAttribute('DataInicio3Bim');
        parent::addAttribute('DataInicio4Bim');
        parent::addAttribute('Codhorario');
        parent::addAttribute('TotalDiasLetivos');
    }

    
    /**
     * Method set_vw_horarioprofessor
     * Sample of usage: $vw_horariocalendario->vw_horarioprofessor = $object;
     * @param $object Instance of VwHorarioprofessor
     */
    public function set_vw_horarioprofessor(VwHorarioprofessor $object)
    {
        $this->vw_horarioprofessor = $object;
        $this->Codprofessor = $object->id;
    }
    
    /**
     * Method get_vw_horarioprofessor
     * Sample of usage: $vw_horariocalendario->vw_horarioprofessor->attribute;
     * @returns VwHorarioprofessor instance
     */
    public function get_vw_horarioprofessor()
    {
        // loads the associated object
        if (empty($this->vw_horarioprofessor))
            $this->vw_horarioprofessor = new VwHorarioprofessor($this->Codprofessor);
    
        // returns the associated object
        return $this->vw_horarioprofessor;
    }
    


}
