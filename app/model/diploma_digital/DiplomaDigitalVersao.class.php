<?php
/**
 * DiplomadigitalDadosVersao Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalVersao extends TRecord
{
    const TABLENAME = 'dados_versao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_user;
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('versao_diploma');
        parent::addAttribute('versao_diploma_inicio');
        parent::addAttribute('versao_diploma_termino');
        parent::addAttribute('versao_documentacao');
        parent::addAttribute('versao_documentacao_inicio');
        parent::addAttribute('versao_documentacao_termino');
        parent::addAttribute('versao_historico');
        parent::addAttribute('versao_historico_inicio');
        parent::addAttribute('versao_historico_termino');
        parent::addAttribute('versao_fiscalizacao');
        parent::addAttribute('versao_fiscalizacao_inicio');
        parent::addAttribute('versao_fiscalizacao_termino');
        parent::addAttribute('versao_curriculo');
        parent::addAttribute('versao_curriculo_inicio');
        parent::addAttribute('versao_curriculo_termino');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }


    /**
     * Method set_system_user
     * Sample of usage: $diplomadigital_dados_versao->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $diplomadigital_dados_versao->system_user->attribute;
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
