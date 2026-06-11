<?php
/**
 * ProcessoJudicial Active Record
 * @author  <your-name-here>
 */
class DiplomaDigitalProcessoJudicial extends TRecord
{
    const TABLENAME = 'dados_processo_judicial';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_documentacao;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dados_documentacao_id');
        parent::addAttribute('numero_processo');
        parent::addAttribute('nome_juiz');
        parent::addAttribute('decisao_judicial');
        parent::addAttribute('declaracao_emissora');
        parent::addAttribute('declaracao_registradora');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_diploma_digital_documentacao
     * Sample of usage: $processo_judicial->diploma_digital_documentacao = $object;
     * @param $object Instance of DiplomaDigitalDocumentacao
     */
    public function set_diploma_digital_documentacao(DiplomaDigitalDocumentacao $object)
    {
        $this->diploma_digital_documentacao = $object;
        $this->diploma_digital_dados_documentacao_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_documentacao
     * Sample of usage: $processo_judicial->diploma_digital_documentacao->attribute;
     * @returns DiplomaDigitalDocumentacao instance
     */
    public function get_diploma_digital_documentacao()
    {
        // loads the associated object
        if (empty($this->diploma_digital_documentacao))
            $this->diploma_digital_documentacao = new DiplomaDigitalDocumentacao($this->dados_documentacao_id);
    
        // returns the associated object
        return $this->diploma_digital_documentacao;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $processo_judicial->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $processo_judicial->system_user->attribute;
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
