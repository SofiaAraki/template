<?php
/**
 * FiHistoricodisciplinas Active Record
 * @author  <your-name-here>
 */
class FiHistoricodisciplinas extends TRecord
{
    const TABLENAME = 'FI_HistoricoDisciplinas';
    const PRIMARYKEY= 'CodHistoricoDisciplinas';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_aluno;
    private $fi_historico;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('codhistorico');
        parent::addAttribute('Disciplina');
        parent::addAttribute('Etapa');
        parent::addAttribute('Ano');
        parent::addAttribute('Sem');
        parent::addAttribute('NotaFinal');
        parent::addAttribute('CH');
        parent::addAttribute('Freq');
        parent::addAttribute('Sit');
        parent::addAttribute('Edita');
        parent::addAttribute('PrefixoDisciplina');
        parent::addAttribute('SufixoDisciplina');
        parent::addAttribute('notafinalbck');
        parent::addAttribute('CodProf');
        parent::addAttribute('NomeProf');
        parent::addAttribute('TituloProf');
        parent::addAttribute('CHParcial');
    }

    
    /**
     * Method set_fi_aluno
     * Sample of usage: $fi_historicodisciplinas->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_aluno(FiAluno $object)
    {
        $this->fi_aluno = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $fi_historicodisciplinas->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_fi_aluno()
    {
        // loads the associated object
        if (empty($this->fi_aluno))
            $this->fi_aluno = new FiAluno($this->Codaluno);
    
        // returns the associated object
        return $this->fi_aluno;
    }
    
    
    /**
     * Method set_fi_historico
     * Sample of usage: $fi_historicodisciplinas->fi_historico = $object;
     * @param $object Instance of FiHistorico
     */
    public function set_fi_historico(FiHistorico $object)
    {
        $this->fi_historico = $object;
        $this->codhistorico = $object->id;
    }
    
    /**
     * Method get_fi_historico
     * Sample of usage: $fi_historicodisciplinas->fi_historico->attribute;
     * @returns FiHistorico instance
     */
    public function get_fi_historico()
    {
        // loads the associated object
        if (empty($this->fi_historico))
            $this->fi_historico = new FiHistorico($this->codhistorico);
    
        // returns the associated object
        return $this->fi_historico;
    }
    


}
