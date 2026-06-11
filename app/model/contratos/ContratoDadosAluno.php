<?php
/**
 * ContratoDadosAluno Active Record
 * @author  Pamella Scapim
 */
class ContratoDadosAluno extends TRecord
{
    const TABLENAME = 'contrato_dados_aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $contrato_financeiro;
    private $vw_aluno_matricula_etapa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Codaluno');
        parent::addAttribute('NomeAluno');
        parent::addAttribute('Datanascimento');
        parent::addAttribute('CPF');
        parent::addAttribute('Rg');
        parent::addAttribute('RgOrgaoExpedidor');
        parent::addAttribute('Naturalidade');
        parent::addAttribute('Endereco');
        parent::addAttribute('EnderecoNumero');
        parent::addAttribute('Bairro');
        parent::addAttribute('CodCidade');
        parent::addAttribute('Nacionalidade');
        parent::addAttribute('Cep');
        parent::addAttribute('Telefone');
        parent::addAttribute('CodResponsavel');
        parent::addAttribute('NomeResponsavel');
        parent::addAttribute('CPFResponsavel');
        parent::addAttribute('CodCurso');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('AnoMatricula');
        parent::addAttribute('SemestreMatricula');
        parent::addAttribute('Periodo');
        parent::addAttribute('EtapaMatricula');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('ValorAnoSem');
        parent::addAttribute('ValorAnoSemExt');
        parent::addAttribute('ValorParc1');
        parent::addAttribute('ValorParc1Ext');
        parent::addAttribute('ValorDmsParc');
        parent::addAttribute('ValorDmsParcExt');
        parent::addAttribute('DescontoComercial');
        parent::addAttribute('DataRegistro');
        parent::addAttribute('system_user_id');
        parent::addAttribute('StatusContrato');
        parent::addAttribute('EstadoCivil');
        parent::addAttribute('Profissao');
        parent::addAttribute('Uf');
        parent::addAttribute('RgResponsavel');
        parent::addAttribute('RuaResponsavel');
        parent::addAttribute('NumResponsavel');
        parent::addAttribute('BairroResponsavel');
        parent::addAttribute('EmailResponsavel');
        parent::addAttribute('CidadeResponsavel');
        parent::addAttribute('CEPResponsavel');
        parent::addAttribute('TelResponsavel');
        parent::addAttribute('AssinaturaAluno');
        parent::addAttribute('IPAluno');
        parent::addAttribute('LatitudeAluno');
        parent::addAttribute('LongitudeAluno');
        parent::addAttribute('DataHoraAceiteAluno');
        parent::addAttribute('DocAluno');
        parent::addAttribute('RazaoSocial');
        parent::addAttribute('NomeFantasia');
        parent::addAttribute('InicioPrestServico');
        parent::addAttribute('TerminoPrestServico');
        parent::addAttribute('DataPrimeiraParcela');
        parent::addAttribute('UfResponsavel');
        parent::addAttribute('DataFinalContrato');
    }

    
    /**
     * Method set_contrato_financeiro
     * Sample of usage: $contrato_dados_aluno->contrato_financeiro = $object;
     * @param $object Instance of ContratoFinanceiro
     */
    public function set_contrato_financeiro(ContratoFinanceiro $object)
    {
        $this->contrato_financeiro = $object;
        $this->contrato_financeiro_id = $object->id;
    }
    
    /**
     * Method get_contrato_financeiro
     * Sample of usage: $contrato_dados_aluno->contrato_financeiro->attribute;
     * @returns ContratoFinanceiro instance
     */
    public function get_contrato_financeiro()
    {
        // loads the associated object
        if (empty($this->contrato_financeiro))
            $this->contrato_financeiro = new ContratoFinanceiro($this->contrato_financeiro_id);
    
        // returns the associated object
        return $this->contrato_financeiro;
    }
    
    
    /**
     * Method set_vw_aluno_matricula_etapa
     * Sample of usage: $contrato_dados_aluno->vw_aluno_matricula_etapa = $object;
     * @param $object Instance of VwAlunoMatriculaEtapa
     */
    public function set_vw_aluno_matricula_etapa(VwAlunoMatriculaEtapa $object)
    {
        $this->vw_aluno_matricula_etapa = $object;
        $this->Codaluno = $object->id;
    }
    
    /**
     * Method get_vw_aluno_matricula_etapa
     * Sample of usage: $contrato_dados_aluno->vw_aluno_matricula_etapa->attribute;
     * @returns VwAlunoMatriculaEtapa instance
     */
    public function get_vw_aluno_matricula_etapa()
    {
        // loads the associated object
        if (empty($this->vw_aluno_matricula_etapa))
            $this->vw_aluno_matricula_etapa = new VwAlunoMatriculaEtapa($this->Codaluno);
    
        // returns the associated object
        return $this->vw_aluno_matricula_etapa;
    }
    
    
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;

        $repository = new TRepository('ContratoDadosAlunoDoc');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('contrato_aluno_id', '=', $id));
        $repository->delete($criteria);
            
        parent::delete($id);
    }


}
