<?php
/**
 * FiAluno Active Record
 * @author  <your-name-here>
 */
class FiAluno extends TRecord
{
    const TABLENAME = 'FI_Aluno';
    const PRIMARYKEY= 'Codaluno';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $solicitacao_aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('NumeroIdentificacao');
        parent::addAttribute('Nome');
        parent::addAttribute('NomeSemAcento');
        parent::addAttribute('Datanascimento');
        parent::addAttribute('Foto');
        parent::addAttribute('Sexo');
        parent::addAttribute('Naturalidade');
        parent::addAttribute('NaturalidadeUF');
        parent::addAttribute('Nacionalidade');
        parent::addAttribute('NomePai');
        parent::addAttribute('NomeMae');
        parent::addAttribute('NumeroCertidaoNascimento');
        parent::addAttribute('Rg');
        parent::addAttribute('RgOrgaoExpedidor');
        parent::addAttribute('Ra');
        parent::addAttribute('CPF');
        parent::addAttribute('TituloEleitorNumero');
        parent::addAttribute('TituloEleitorZona');
        parent::addAttribute('TituloEleitorSecao');
        parent::addAttribute('ServicoMilitar');
        parent::addAttribute('ServicoMilitarOrgaoExpedidor');
        parent::addAttribute('ServicoMilitarDataExpedicao');
        parent::addAttribute('Endereco');
        parent::addAttribute('EnderecoNumero');
        parent::addAttribute('EnderecoComplemeto');
        parent::addAttribute('Bairro');
        parent::addAttribute('CodOperador');
        parent::addAttribute('CodCidade');
        parent::addAttribute('Cep');
        parent::addAttribute('Telefone');
        parent::addAttribute('Email');
        parent::addAttribute('DataCadastro');
        parent::addAttribute('DataAtualizacao');
        parent::addAttribute('Observacao1');
        parent::addAttribute('Observacao2');
        parent::addAttribute('Observacao3');
        parent::addAttribute('EstadoCivil');
        parent::addAttribute('Profissao');
        parent::addAttribute('regmat');
        parent::addAttribute('CorRaca');
        parent::addAttribute('ObsCorRaca1');
        parent::addAttribute('ObsCorRaca2');
        parent::addAttribute('NecessidadesEspecias');
        parent::addAttribute('Baixavisao');
        parent::addAttribute('Mental');
        parent::addAttribute('Cegueira');
        parent::addAttribute('Surdez');
        parent::addAttribute('Auditivo');
        parent::addAttribute('Fisica');
        parent::addAttribute('Multiplo');
        parent::addAttribute('Superdotado');
        parent::addAttribute('Condutas');
        parent::addAttribute('ObsEducespec1');
        parent::addAttribute('ObsEducespec2');
        parent::addAttribute('ObsEducespec3');
        parent::addAttribute('NIS');
        parent::addAttribute('BolsaFamilia');
        parent::addAttribute('DataBolsaFamilia');
        parent::addAttribute('RespBolsaFamilia');
        parent::addAttribute('SENHA_INTERNET');
        parent::addAttribute('SENHA_INTERNET');
        parent::addAttribute('BLOQUEADO_INTERNET');
        parent::addAttribute('TipoEscolaEnsinoMedio');
        parent::addAttribute('Telefone2');
        parent::addAttribute('Telefone3');
        parent::addAttribute('TelefoneFax');
        parent::addAttribute('FotoImagem');
        parent::addAttribute('CertNascLivro');
        parent::addAttribute('CertNascFolha');
        parent::addAttribute('SenhaMoodle');
        parent::addAttribute('NomeIdentificacaoCivil');
        parent::addAttribute('CodResponsavel');
        parent::addAttribute('CartaoImagem');
    }

    
    /**
     * Method set_solicitacao_aluno
     * Sample of usage: $fi_aluno->solicitacao_aluno = $object;
     * @param $object Instance of SolicitacaoAluno
     */
    public function set_solicitacao_aluno(SolicitacaoAluno $object)
    {
        $this->solicitacao_aluno = $object;
        $this->solicitacao_aluno_id = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $fi_aluno->solicitacao_aluno->attribute;
     * @returns SolicitacaoAluno instance
     */
    public function get_solicitacao_aluno()
    {
        // loads the associated object
        if (empty($this->solicitacao_aluno))
            $this->solicitacao_aluno = new SolicitacaoAluno($this->solicitacao_aluno_id);
    
        // returns the associated object
        return $this->solicitacao_aluno;
    }



    /**
     * Method set_solicitacao_aluno
     * Sample of usage: $fi_aluno->solicitacao_aluno = $object;
     * @param $object Instance of SolicitacaoAluno
     */
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
    

     public function set_cidade_aluno(FiCidade $object)
    {
        $this->cidade_aluno = $object;
        $this->CodCidade = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $fi_aluno->solicitacao_aluno->attribute;
     * @returns SolicitacaoAluno instance
     */
    public function get_cidade_aluno()
    {
        // loads the associated object
        if (empty($this->cidade_aluno))
            $this->cidade_aluno = new FiCidade($this->CodCidade);
    
        // returns the associated object
        return $this->cidade_aluno->Nome;
    }


}
