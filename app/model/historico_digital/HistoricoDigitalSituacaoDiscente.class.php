<?php
/**
 * HistoricoSituacaoDiscente Active Record
 * @author  <your-name-here>
 */
class HistoricoDigitalSituacaoDiscente extends TRecord
{
    const TABLENAME = 'historico_situacao_discente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $historico_digital;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('historico_digital_id');
        parent::addAttribute('tipo_entrada');
        parent::addAttribute('situacao_discente');
        parent::addAttribute('situacao_ano');
        parent::addAttribute('situacao_semestre');
        parent::addAttribute('situacao_etapa');
        parent::addAttribute('situacao_intercambio_instituicao');
        parent::addAttribute('situacao_intercambio_pais');
        parent::addAttribute('situacao_intercambio_programa');
        parent::addAttribute('situacao_outra');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }

    
    /**
     * Method set_historico_digital
     * Sample of usage: $historico_situacao_discente->historico_digital = $object;
     * @param $object Instance of HistoricoDigital
     */
    public function set_historico_digital(HistoricoDigital $object)
    {
        $this->historico_digital = $object;
        $this->historico_digital_id = $object->id;
    }
    
    /**
     * Method get_historico_digital
     * Sample of usage: $historico_situacao_discente->historico_digital->attribute;
     * @returns HistoricoDigital instance
     */
    public function get_historico_digital()
    {
        // loads the associated object
        if (empty($this->historico_digital))
            $this->historico_digital = new HistoricoDigital($this->historico_digital_id);
    
        // returns the associated object
        return $this->historico_digital;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $historico_situacao_discente->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $historico_situacao_discente->system_user->attribute;
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
