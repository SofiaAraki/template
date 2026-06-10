<?php
/**
 * FiDataapontamentobimestral Active Record
 * @author  <your-name-here>
 */
class FiDataapontamentobimestral extends TRecord
{
    const TABLENAME = 'FI_DataApontamentoBimestral';
    const PRIMARYKEY= 'Cod_DataApontamentoBimestral';
    const IDPOLICY =  'serial'; // {max, serial}
    
	use SystemChangeLogTrait;
    
    private $fi_entidade;
    private $fi_operador;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodOperador');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('Bimestre');
        parent::addAttribute('DataInicio');
        parent::addAttribute('DataFim');
    }

    // public function get_avaliacao_bimestre()
    // {
    //     $bimestres = array(1 =>'1º Bimestre', 2 =>'2º Bimestre', 3 => 'Exame');
    //     return $bimestres[$this->Bimestre];
    // }

    public function get_avaliacao_bimestre_colegio()
    {
        $bimestres = array(1 =>'1º Bimestre', 2 =>'2º Bimestre', 3 => '3º Bimestre', 4 => '4º Bimestre');
        return $bimestres[$this->Bimestre];
    }
    /**
     * Method set_fi_entidade
     * Sample of usage: $fi_dataapontamentobimestral->fi_entidade = $object;
     * @param $object Instance of FiEntidade
     */
    public function set_fi_entidade(FiEntidade $object)
    {
        $this->fi_entidade = $object;
        $this->CodEntidade = $object->id;
    }
    
    /**
     * Method get_fi_entidade
     * Sample of usage: $fi_dataapontamentobimestral->fi_entidade->attribute;
     * @returns FiEntidade instance
     */
    public function get_fi_entidade()
    {
        // loads the associated object
        if (empty($this->fi_entidade))
            $this->fi_entidade = new FiEntidade($this->CodEntidade);
    
        // returns the associated object
        return $this->fi_entidade;
    }
    
    
    /**
     * Method set_fi_operador
     * Sample of usage: $fi_dataapontamentobimestral->fi_operador = $object;
     * @param $object Instance of FiOperador
     */
    public function set_fi_operador(FiOperador $object)
    {
        $this->fi_operador = $object;
        $this->CodOperador = $object->id;
    }
    
    /**
     * Method get_fi_operador
     * Sample of usage: $fi_dataapontamentobimestral->fi_operador->attribute;
     * @returns FiOperador instance
     */
    public function get_fi_operador()
    {
        // loads the associated object
        if (empty($this->fi_operador))
            $this->fi_operador = new FiOperador($this->CodOperador);
    
        // returns the associated object
        return $this->fi_operador;
    }
    


}
