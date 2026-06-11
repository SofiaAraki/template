<?php
/**
 * CurriculoAtividadeCadastro Active Record
 * @author  <your-name-here>
 */
class CurriculoAtividadeCadastro extends TRecord
{
    const TABLENAME = 'curriculo_atividade_cadastro';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $curriculo_digital;
    private $atividade_complementar_categoria;
    private $curriculo_atividade_categoria;
    private $atividade_complementar_cadastro;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curriculo_id');
        parent::addAttribute('atividade_complementar_categoria_id');
        parent::addAttribute('curriculo_atividade_categoria_id');
        parent::addAttribute('atividade_complementar_cadastro_id');
        parent::addAttribute('cod_atividade_curriculo');
        parent::addAttribute('ch_atividade_hora_relogio');
    }

    
    /**
     * Method set_curriculo_digital
     * Sample of usage: $curriculo_atividade_cadastro->curriculo_digital = $object;
     * @param $object Instance of CurriculoDigital
     */
    public function set_curriculo_digital(CurriculoDigital $object)
    {
        $this->curriculo_digital = $object;
        $this->curriculo_id = $object->id;
    }
    
    /**
     * Method get_curriculo_digital
     * Sample of usage: $curriculo_atividade_cadastro->curriculo_digital->attribute;
     * @returns CurriculoDigital instance
     */
    public function get_curriculo_digital()
    {
        // loads the associated object
        if (empty($this->curriculo_digital))
            $this->curriculo_digital = new CurriculoDigital($this->curriculo_id);
    
        // returns the associated object
        return $this->curriculo_digital;
    }
    
    
    /**
     * Method set_atividade_complementar_categoria
     * Sample of usage: $curriculo_atividade_cadastro->atividade_complementar_categoria = $object;
     * @param $object Instance of AtividadeComplementarCategoria
     */
    public function set_atividade_complementar_categoria(AtividadeComplementarCategoria $object)
    {
        $this->atividade_complementar_categoria = $object;
        $this->atividade_complementar_categoria_id = $object->id;
    }
    
    /**
     * Method get_atividade_complementar_categoria
     * Sample of usage: $curriculo_atividade_cadastro->atividade_complementar_categoria->attribute;
     * @returns AtividadeComplementarCategoria instance
     */
    public function get_atividade_complementar_categoria()
    {
        // loads the associated object
        if (empty($this->atividade_complementar_categoria))
            $this->atividade_complementar_categoria = new AtividadeComplementarCategoria($this->atividade_complementar_categoria_id);
    
        // returns the associated object
        return $this->atividade_complementar_categoria;
    }
    
    
    /**
     * Method set_curriculo_atividade_categoria
     * Sample of usage: $curriculo_atividade_cadastro->curriculo_atividade_categoria = $object;
     * @param $object Instance of CurriculoAtividadeCategoria
     */
    public function set_curriculo_atividade_categoria(CurriculoAtividadeCategoria $object)
    {
        $this->curriculo_atividade_categoria = $object;
        $this->curriculo_atividade_categoria_id = $object->id;
    }
    
    /**
     * Method get_curriculo_atividade_categoria
     * Sample of usage: $curriculo_atividade_cadastro->curriculo_atividade_categoria->attribute;
     * @returns CurriculoAtividadeCategoria instance
     */
    public function get_curriculo_atividade_categoria()
    {
        // loads the associated object
        if (empty($this->curriculo_atividade_categoria))
            $this->curriculo_atividade_categoria = new CurriculoAtividadeCategoria($this->curriculo_atividade_categoria_id);
    
        // returns the associated object
        return $this->curriculo_atividade_categoria;
    }
    
    
    /**
     * Method set_atividade_complementar_cadastro
     * Sample of usage: $curriculo_atividade_cadastro->atividade_complementar_cadastro = $object;
     * @param $object Instance of AtividadeComplementarCadastro
     */
    public function set_atividade_complementar_cadastro(AtividadeComplementarCadastro $object)
    {
        $this->atividade_complementar_cadastro = $object;
        $this->atividade_complementar_cadastro_id = $object->id;
    }
    
    /**
     * Method get_atividade_complementar_cadastro
     * Sample of usage: $curriculo_atividade_cadastro->atividade_complementar_cadastro->attribute;
     * @returns AtividadeComplementarCadastro instance
     */
    public function get_atividade_complementar_cadastro()
    {
        // loads the associated object
        if (empty($this->atividade_complementar_cadastro))
            $this->atividade_complementar_cadastro = new AtividadeComplementarCadastro($this->atividade_complementar_cadastro_id);
    
        // returns the associated object
        return $this->atividade_complementar_cadastro;
    }
    


}
