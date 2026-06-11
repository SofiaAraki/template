<?php
/**
 * ContratoFinanceiro Active Record
 * @author  Pamella Scapim
 */
class ContratoFinanceiro extends TRecord
{
    const TABLENAME = 'contrato_financeiro';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_curso;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('id');
        parent::addAttribute('curso_id');
        parent::addAttribute('valor_total');
        parent::addAttribute('valor_total_extenso');
        parent::addAttribute('valor_primeira_parcela');
        parent::addAttribute('varlor_prim_parcela_extenso');
        parent::addAttribute('valor_demais_parcelas');
        parent::addAttribute('valor_dms_parcelas_extenso');
        parent::addAttribute('ano_vigente');
        parent::addAttribute('data_reg');
        parent::addAttribute('registro');
        parent::addAttribute('user_id');
        parent::addAttribute('nome_curso');
        parent::addAttribute('turno');
    }

    
    /**
     * Method set_fi_curso
     * Sample of usage: $contrato_financeiro->fi_curso = $object;
     * @param $object Instance of FiCurso
     */
    public function set_fi_curso(FiCurso $object)
    {
        $this->fi_curso = $object;
        $this->CodCurso = $object->id;
    }
    
    /**
     * Method get_fi_curso
     * Sample of usage: $contrato_financeiro->fi_curso->attribute;
     * @returns FiCurso instance
     */
    public function get_fi_curso()
    {
        // loads the associated object
        if (empty($this->fi_curso))
            $this->fi_curso = new FiCurso($this->CodCurso);
    
        // returns the associated object
        return $this->fi_curso->Nome;
    }
    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $aluno->system_user->attribute;
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
