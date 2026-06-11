<?php
/**
 * AtividadeComplementarCadastro Active Record
 * @author  <your-name-here>
 */
class AtividadeComplementarCadastro extends TRecord
{
    const TABLENAME = 'atividade_complementar_cadastro';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $atividade_complementar_categoria;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('categoria_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');
    }


    /**
     * Method set_atividade_complementar_categoria
     * Sample of usage: $atividade_complementar_cadastro->atividade_complementar_categoria = $object;
     * @param $object Instance of AtividadeComplementarCategoria
     */
    public function set_atividade_complementar_categoria(AtividadeComplementarCategoria $object)
    {
        $this->atividade_complementar_categoria = $object;
        $this->atividade_complementar_categoria_id = $object->id;
    }
    
    /**
     * Method get_atividade_complementar_categoria
     * Sample of usage: $atividade_complementar_cadastro->atividade_complementar_categoria->attribute;
     * @returns AtividadeComplementarCategoria instance
     */
    public function get_atividade_complementar_categoria()
    {
        // loads the associated object
        if (empty($this->atividade_complementar_categoria))
            $this->atividade_complementar_categoria = new AtividadeComplementarCategoria($this->categoria_id);
    
        // returns the associated object
        return $this->atividade_complementar_categoria;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $atividade_complementar_cadastro->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $atividade_complementar_cadastro->system_user->attribute;
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
