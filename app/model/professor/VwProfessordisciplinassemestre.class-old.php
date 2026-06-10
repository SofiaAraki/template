<?php
/**
 * VwProfessordisciplinassemestre Active Record
 * @author  <your-name-here>
 */
class VwProfessordisciplinassemestre extends TRecord
{
    const TABLENAME = 'VW_ProfessorDisciplinasSemestre';
    const PRIMARYKEY= 'CodProfessor';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;

    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('Identificacao');
        parent::addAttribute('Periodo');
        parent::addAttribute('CodTurmaEtapa');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('CodCurso');
        parent::addAttribute('Etapa');
        parent::addAttribute('QuantidadeAvaliacaoes');
        parent::addAttribute('CodGradeCurso');
        parent::addAttribute('CodGradeDisciplinaEtapa');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('CodGradeDisciplinaEtapaFrente');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Habilitacao');
        parent::addAttribute('Expr1');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('NomeEntidade');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $vw_professordisciplinassemestre->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $vw_professordisciplinassemestre->system_user->attribute;
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
