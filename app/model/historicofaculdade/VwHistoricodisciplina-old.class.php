<?php
/**
 * VwHistoricodisciplina Active Record
 * @author  <your-name-here>
 */
class VwHistoricodisciplina extends TRecord
{
    const TABLENAME = 'VW_HistoricoDisciplina';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $fi_entidade;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        //parent::addAttribute('Codaluno');
        parent::addAttribute('codhistorico');
        parent::addAttribute('CodCurso');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('Etapa');
        parent::addAttribute('Ano');
        parent::addAttribute('Sem');
        parent::addAttribute('NotaFinal');
        parent::addAttribute('CodHistoricoDisciplinas');
        parent::addAttribute('CH');
        parent::addAttribute('Freq');
        parent::addAttribute('Sit');
        parent::addAttribute('Edita');
        parent::addAttribute('PrefixoDisciplina');
        parent::addAttribute('SufixoDisciplina');
        parent::addAttribute('CodProf');
        parent::addAttribute('NomeProf');
        parent::addAttribute('TituloProf');
        parent::addAttribute('notafinalbck');
        parent::addAttribute('Codprofessor');
        parent::addAttribute('nome');
        parent::addAttribute('HabilitacaoProf3');
        parent::addAttribute('CHParcial');
    }

    
    /**
     * Method set_fi_entidade
     * Sample of usage: $vw_historicodisciplina->fi_entidade = $object;
     * @param $object Instance of FiEntidade
     */
    public function set_fi_entidade(FiEntidade $object)
    {
        $this->fi_entidade = $object;
        $this->CodEntidade = $object->id;
    }
    
    /**
     * Method get_fi_entidade
     * Sample of usage: $vw_historicodisciplina->fi_entidade->attribute;
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
    


}
