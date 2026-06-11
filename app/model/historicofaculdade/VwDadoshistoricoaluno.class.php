<?php
/**
 * VwDadoshistoricoaluno Active Record
 * @author  <your-name-here>
 */
class VwDadoshistoricoaluno extends TRecord
{
    const TABLENAME = 'Vw_DadosHistoricoAluno';
    const PRIMARYKEY= 'codhistorico';
    const IDPOLICY =  'max'; // {max, serial}
        
        
    private $vw_historicodisciplina;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Codaluno');
        parent::addAttribute('Nome');
        parent::addAttribute('Datanascimento');
        parent::addAttribute('Naturalidade');
        parent::addAttribute('NaturalidadeUF');
        parent::addAttribute('Nacionalidade');
        parent::addAttribute('Rg');
        parent::addAttribute('RgOrgaoExpedidor');
        parent::addAttribute('CPF');
        parent::addAttribute('EscolaEnsinoMedio');
        parent::addAttribute('EscolaEnsinoMedioLocal');
        parent::addAttribute('VestibularAno');
        parent::addAttribute('TipoIngresso');
        parent::addAttribute('DataIngresso');
        parent::addAttribute('DataConclusaoCurso');
        parent::addAttribute('DataColacaoGrau');
        parent::addAttribute('DataExpedicaoDiploma');
        parent::addAttribute('DataVestibular');
        parent::addAttribute('DataVestibExt');
        parent::addAttribute('DataConclEMExt');
        parent::addAttribute('ObservacaoFinais1');
        parent::addAttribute('ObservacaoFinais2');
        parent::addAttribute('ObservacaoFinais3');
        parent::addAttribute('ObservacaoFinais4');
        parent::addAttribute('ObservacaoFinais5');
        parent::addAttribute('ObservacaoCadastral1');
        parent::addAttribute('ObservacaoCadastral2');
        parent::addAttribute('ObservacaoCadastral3');
        parent::addAttribute('ObservacaoCadastral4');
        parent::addAttribute('ObservacaoCadastral5');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('Habilitacao1');
        parent::addAttribute('Habilitacao2');
        parent::addAttribute('Reconhecimento');
        parent::addAttribute('CargaHorariaTotal');
        parent::addAttribute('Descricao');
        parent::addAttribute('QtdeEtapas');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('Nomehistorico');
        parent::addAttribute('dataexphistorico');
        parent::addAttribute('SituacaoEnade');
        parent::addAttribute('CodCurso');
        parent::addAttribute('codhistorico');
        parent::addAttribute('NomeFantasia');
        parent::addAttribute('HISTORICO_CAB1');
        parent::addAttribute('HISTORICO_CAB2');
        parent::addAttribute('HISTORICO_CAB3');
        parent::addAttribute('HISTORICO_CAB4');
        parent::addAttribute('NomeCoordenador');
        parent::addAttribute('HabilitacaoProf3');
        parent::addAttribute('HabilitacaoProf2');
        parent::addAttribute('HabilitacaoProf1');
        parent::addAttribute('NomeIdentificacaoCivil');
        parent::addAttribute('SECRETARIO_DADOS1');
        parent::addAttribute('SECRETARIO_DADOS2');
        parent::addAttribute('SECRETARIO_DADOS3');
        parent::addAttribute('DIRETOR_DADOS1');
        parent::addAttribute('DIRETOR_DADOS2');
        parent::addAttribute('DIRETOR_DADOS3');
        parent::addAttribute('AtivCom_CH');
        parent::addAttribute('Estagio_CH');
    }

    
    /**
     * Method set_vw_historicodisciplina
     * Sample of usage: $vw_dadoshistoricoaluno->vw_historicodisciplina = $object;
     * @param $object Instance of VwHistoricodisciplina
     */
    public function set_vw_historicodisciplina(VwHistoricodisciplina $object)
    {
        $this->vw_historicodisciplina = $object;
        $this->codhistorico = $object->id;
    }
    
    /**
     * Method get_vw_historicodisciplina
     * Sample of usage: $vw_dadoshistoricoaluno->vw_historicodisciplina->attribute;
     * @returns VwHistoricodisciplina instance
     */
    public function get_vw_historicodisciplina()
    {
        // loads the associated object
        if (empty($this->vw_historicodisciplina))
            $this->vw_historicodisciplina = new VwHistoricodisciplina($this->codhistorico);
    
        // returns the associated object
        return $this->vw_historicodisciplina;
    }
    
        public function set_fi_entidade(FiEntidade $object)
    {
        $this->fi_entidade = $object;
        $this->CodEntidade = $object->id;
    }
    
    /**
     * Method get_fi_professor
     * Sample of usage: $vw_papeleta->fi_professor->attribute;
     * @returns FiProfessor instance
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
