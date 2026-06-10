<?php
/**
 * VwHorarioprofessor Active Record
 * @author  <your-name-here>
 */
class VwHorarioprofessor extends TRecord
{
    const TABLENAME = 'VW_HorarioProfessor';
    const PRIMARYKEY= 'Codprofessor';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;

    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
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
        parent::addAttribute('QuantidadeAvaliacoes');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('Habilitacao1');
        parent::addAttribute('DiaSemana');
        parent::addAttribute('HoraAula');
        parent::addAttribute('InicioAula');
        parent::addAttribute('NumeroOrdemAula');
        parent::addAttribute('DuracaoAula');
        parent::addAttribute('IntervalorAula');
        parent::addAttribute('DuracaoIntervalo');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('NomeFantasia');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $vw_horarioprofessor->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $vw_horarioprofessor->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    


}
