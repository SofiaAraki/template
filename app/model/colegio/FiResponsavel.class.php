<?php
/**
 * FiResponsavel Active Record
 * @author  <your-name-here>
 */
class FiResponsavel extends TRecord
{
    const TABLENAME = 'FI_Responsavel';
    const PRIMARYKEY= 'codresponsavel';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $cidade_responsavel;
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Nome');
        parent::addAttribute('Rg');
        parent::addAttribute('CPF');
        parent::addAttribute('DataNascimento');
        parent::addAttribute('Sexo');
        parent::addAttribute('Endereco');
        parent::addAttribute('EnderecoNumero');
        parent::addAttribute('Bairro');
        parent::addAttribute('Cep');
        parent::addAttribute('Profissao');
        parent::addAttribute('Telefone1');
        parent::addAttribute('Telefone2');
        parent::addAttribute('LocalTrabalho');
        parent::addAttribute('CodOperador');
        parent::addAttribute('CodCidade');
        parent::addAttribute('EstadoCivil');
        parent::addAttribute('email');
        parent::addAttribute('codresponsavel');
    }


    public function set_cidade_responsavel(FiCidade $object)
    {
        $this->cidade_responsavel = $object;
        $this->CodCidade = $object->id;
    }
    
    /**
     * Method get_solicitacao_aluno
     * Sample of usage: $fi_aluno->solicitacao_aluno->attribute;
     * @returns SolicitacaoAluno instance
     */
    public function get_cidade_responsavel()
    {
        // loads the associated object
        if (empty($this->cidade_responsavel))
            $this->cidade_responsavel = new FiCidade($this->CodCidade);
    
        // returns the associated object
        return $this->cidade_responsavel->Nome;
    }


}
