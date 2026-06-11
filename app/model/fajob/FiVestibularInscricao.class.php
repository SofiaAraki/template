<?php
/**
 * FiVestibularInscricao Active Record
 * @author  <your-name-here>
 */
class FiVestibularInscricao extends TRecord
{
    const TABLENAME = 'FI_Vestibular_Inscricao';
    const PRIMARYKEY= 'CodInscricaoVestibular';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $fi_curso;
    private $fi_entidade;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODVESTIBULAR');
        parent::addAttribute('CODVESTIBULAR_SALA');
        parent::addAttribute('Nome');
        parent::addAttribute('RG');
        parent::addAttribute('RG_Origem');
        parent::addAttribute('Filiacao_Pai');
        parent::addAttribute('Filiacao_Mae');
        parent::addAttribute('Cor_Raca');
        parent::addAttribute('Sexo');
        parent::addAttribute('Necessidades_Especiais');
        parent::addAttribute('NS_Baixa_Visao');
        parent::addAttribute('NS_Cegueira');
        parent::addAttribute('NS_Surdez');
        parent::addAttribute('NS_Deficiente_Auditivo');
        parent::addAttribute('NS_Fisica');
        parent::addAttribute('NS_Surdocegueira');
        parent::addAttribute('NS_Intelectual');
        parent::addAttribute('NS_Autismo');
        parent::addAttribute('NS_Sindrome_Asperger');
        parent::addAttribute('NS_Sindrome_RETT');
        parent::addAttribute('NS_Transtorno_Desintegrativo');
        parent::addAttribute('NS_Multipla');
        parent::addAttribute('NS_Superdotado');
        parent::addAttribute('Endereco');
        parent::addAttribute('Numero');
        parent::addAttribute('Bairro');
        parent::addAttribute('Complemento');
        parent::addAttribute('Cep');
        parent::addAttribute('Telefone_Residencial');
        parent::addAttribute('Telefone_Comercial');
        parent::addAttribute('Email');
        parent::addAttribute('Data_Prova');
        parent::addAttribute('Pagamento');
        parent::addAttribute('Data_Inscricao');
        parent::addAttribute('Frequencia');
        parent::addAttribute('Senha');
        parent::addAttribute('CodCidade_INEP_RES');
        parent::addAttribute('CodCidade_INEP_NASC');
        parent::addAttribute('Data_Nascimento');
        parent::addAttribute('Telefone_Celular');
        parent::addAttribute('Hora_Prova');
        parent::addAttribute('Perfil_Candidato');
        parent::addAttribute('Escola_Ensino_Medio');
        parent::addAttribute('Ano_Conclusao_Ensino_Medio');
        parent::addAttribute('CodOperador');
        parent::addAttribute('CPF');
        parent::addAttribute('NumeroCarteira');
        parent::addAttribute('Opcao_Curso1');
        parent::addAttribute('Opcao_Curso2');
        parent::addAttribute('Opcao_Curso3');
        parent::addAttribute('Inscricao_Tipo');
        parent::addAttribute('Obs');
        parent::addAttribute('Obs_Pagamento');
        parent::addAttribute('Classificacao');
        parent::addAttribute('MATRICULA');
        parent::addAttribute('NotaFinal');
        parent::addAttribute('RestricaoGeral');
        parent::addAttribute('RestricaoCurso');
        parent::addAttribute('ClassificacaoCurso');
        parent::addAttribute('data_pagamento');
        parent::addAttribute('LocalProva');
    }

    
    /**
     * Method set_fi_curso
     * Sample of usage: $fi_vestibular_inscricao->fi_curso = $object;
     * @param $object Instance of FiCurso
     */
    public function set_fi_curso(FiCurso $object)
    {
        $this->fi_curso = $object;
        $this->CodCurso = $object->id;
    }
    
    /**
     * Method get_fi_curso
     * Sample of usage: $fi_vestibular_inscricao->fi_curso->attribute;
     * @returns FiCurso instance
     */
    public function get_fi_curso()
    {
        // loads the associated object
        if (empty($this->fi_curso))
            $this->fi_curso = new FiCurso($this->CodCurso);
    
        // returns the associated object
        return $this->fi_curso;
    }
    
    
    /**
     * Method set_fi_entidade
     * Sample of usage: $fi_vestibular_inscricao->fi_entidade = $object;
     * @param $object Instance of FiEntidade
     */
    public function set_fi_entidade(FiEntidade $object)
    {
        $this->fi_entidade = $object;
        $this->CodEntidade = $object->id;
    }
    
    /**
     * Method get_fi_entidade
     * Sample of usage: $fi_vestibular_inscricao->fi_entidade->attribute;
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
    
     public function set_fi_cidades_inep(FiCidadesInep $object)
    {
        $this->fi_cidades_inep = $object;
        $this->CODCIDADE_INEP = $object->id;
    }
    
    /**
     * Method get_fi_cidades_inep
     * Sample of usage: $fi_estados_inep->fi_cidades_inep->attribute;
     * @returns FiCidadesInep instance
     */
    public function get_fi_cidades_inep()
    {
        // loads the associated object
        if (empty($this->fi_cidades_inep))
            $this->fi_cidades_inep = new FiCidadesInep($this->CODCIDADE_INEP);
    
        // returns the associated object
        return $this->fi_cidades_inep->NOME;
    }

     public function set_fi_estados_inep(FiEstadosInep $object)
    {
        $this->fi_estados_inep = $object;
        $this->CODESTADO_INEP = $object->id;
    }
    
    /**
     * Method get_fi_cidades_inep
     * Sample of usage: $fi_estados_inep->fi_cidades_inep->attribute;
     * @returns FiCidadesInep instance
     */
    public function get_fi_estados_inep()
    {
        // loads the associated object
        if (empty($this->fi_estados_inep))
            $this->fi_estados_inep = new FiEstadosInep($this->CODESTADO_INEP);
    
        // returns the associated object
        return $this->fi_estados_inep;
    }


    //model
    public function get_cidade()
    {
          return $this->get_fi_cidades_inep()->NOME;
    }


}
