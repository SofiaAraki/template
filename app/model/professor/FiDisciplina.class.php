<?php
/**
 * FiDisciplina Active Record
 * @author  <your-name-here>
 */
class FiDisciplina extends TRecord
{
    const TABLENAME = 'FI_Disciplina';
    const PRIMARYKEY= 'CodDisciplina';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_notasfaltas_frente;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('NomeOficial');
        parent::addAttribute('Nomeusual');
        parent::addAttribute('Sigla');
        parent::addAttribute('Codificacao');
        parent::addAttribute('IndiceImpressao');
        parent::addAttribute('CodGrupoDisciplina');
        parent::addAttribute('CodDisciplinaEfetiva');
    }

    
    /**
     * Method set_fi_notasfaltas_frente
     * Sample of usage: $fi_disciplina->fi_notasfaltas_frente = $object;
     * @param $object Instance of FiNotasfaltasFrente
     */
    public function set_fi_notasfaltas_frente(FiNotasfaltasFrente $object)
    {
        $this->fi_notasfaltas_frente = $object;
        $this->CodDisciplina = $object->id;
    }
    
    /**
     * Method get_fi_notasfaltas_frente
     * Sample of usage: $fi_disciplina->fi_notasfaltas_frente->attribute;
     * @returns FiNotasfaltasFrente instance
     */
    public function get_fi_notasfaltas_frente()
    {
        // loads the associated object
        if (empty($this->fi_notasfaltas_frente))
            $this->fi_notasfaltas_frente = new FiNotasfaltasFrente($this->CodDisciplina);
    
        // returns the associated object
        return $this->fi_notasfaltas_frente;
    }
    


}
