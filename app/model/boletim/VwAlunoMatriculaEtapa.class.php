<?php
/**
 * VwAlunomatriculaetapa Active Record
 * @author  <your-name-here>
 */
class VwAlunoMatriculaEtapa extends TRecord
{
    const TABLENAME = 'VW_AlunoMatriculaEtapa';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Datanascimento');
        parent::addAttribute('NomeAluno');
        parent::addAttribute('CodMatriculaEtapa');
        parent::addAttribute('MediaPI');
        parent::addAttribute('NotaNI');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('AnoMatricula');
        parent::addAttribute('SemestreMatricula');
        parent::addAttribute('IdentificacaoMatricula');
        parent::addAttribute('Campus');
        parent::addAttribute('Periodo');
        parent::addAttribute('EtapaMatricula');
        parent::addAttribute('HabilitacaoCurso');
        parent::addAttribute('CodCurso');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('SituacaoMatricula');
        parent::addAttribute('ConfirmacaoMatricula');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('PercentualPI');
        parent::addAttribute('TotalAcertosPI');
        parent::addAttribute('CPF');
        parent::addAttribute('EstadoCivil');
        parent::addAttribute('Profissao');
        parent::addAttribute('Rg');
        parent::addAttribute('RgOrgaoExpedidor');
        parent::addAttribute('Endereco');
        parent::addAttribute('EnderecoNumero');
        parent::addAttribute('Bairro');
        parent::addAttribute('CodCidade');
        parent::addAttribute('Cep');
        parent::addAttribute('Email');
        parent::addAttribute('TipoEscolaEnsinoMedio');
        parent::addAttribute('Telefone');
        parent::addAttribute('Nacionalidade');
        parent::addAttribute('Naturalidade');
        parent::addAttribute('NaturalidadeUF');
        parent::addAttribute('NomePai');
        parent::addAttribute('NomeMae');
        parent::addAttribute('Telefone2');
        parent::addAttribute('Telefone3');
        parent::addAttribute('NomeIdentificacaoCivil');
        parent::addAttribute('CodResponsavel');
        parent::addAttribute('Uf');
        parent::addAttribute('RazaoSocial');
        parent::addAttribute('NomeFantasia');

    }

    /**
     * Method set_fi_aluno
     * Sample of usage: $fi_matriculaetapa->fi_aluno = $object;
     * @param $object Instance of FiAluno
     */
    public function set_fi_matriculaetapa(FiMatriculaetapa $object)
    {
        $this->fi_matriculaetapa = $object;
        $this->CodMatriculaEtapa = $object->id;
    }
    
    /**
     * Method get_fi_aluno
     * Sample of usage: $fi_matriculaetapa->fi_aluno->attribute;
     * @returns FiAluno instance
     */
    public function get_fi_aluno()
    {
        // loads the associated object
        if (empty($this->fi_matriculaetapa))
            $this->fi_matriculaetapa = new FiMatriculaetapa($this->CodMatriculaEtapa);
    
        // returns the associated object
        return $this->fi_matriculaetapa->Nome;
    }

    public function set_cidadealuno(FiCidade $object)
    {
        $this->cidadealuno = $object;
        $this->CodCidade = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $fi_aluno->solicitacao_aluno->attribute;
     * @returns SolicitacaoAluno instance
     */
    public function get_cidadealuno()
    {
        // loads the associated object
        if (empty($this->cidadealuno))
            $this->cidadealuno = new FiCidade($this->CodCidade);
    
        // returns the associated object
        return $this->cidadealuno->Nome;
    }

    public function set_responsavel(FiResponsavel $object)
    {
        $this->responsavel = $object;
        $this->codresponsavel = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $fi_aluno->solicitacao_aluno->attribute;
     * @returns SolicitacaoAluno instance
     */
    public function get_responsavel()
    {
        // loads the associated object
        if (empty($this->responsavel))
            $this->responsavel = new FiResponsavel($this->CodResponsavel);
    
        // returns the associated object
        return $this->responsavel;
        
    }


}
