<?php
/**
 * FiProfessor Active Record
 * @author  <your-name-here>
 */
class FiProfessor extends TRecord
{
    const TABLENAME = 'FI_Professor';
    const PRIMARYKEY= 'Codprofessor';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Nome');
        parent::addAttribute('Sexo');
        parent::addAttribute('Naturalidade');
        parent::addAttribute('NaturalidadeUf');
        parent::addAttribute('Nacionalidade');
        parent::addAttribute('DataNascimento');
        parent::addAttribute('HabilitacaoProf1');
        parent::addAttribute('HabilitacaoProf2');
        parent::addAttribute('HabilitacaoProf3');
        parent::addAttribute('Rg');
        parent::addAttribute('CPF');
        parent::addAttribute('Endereco');
        parent::addAttribute('Bairro');
        parent::addAttribute('CEP');
        parent::addAttribute('CodOperador');
        parent::addAttribute('CodCidade');
        parent::addAttribute('Telefone1');
        parent::addAttribute('Telefone2');
        parent::addAttribute('Telefone3');
        parent::addAttribute('Email');
        parent::addAttribute('DataCadastro');
        parent::addAttribute('DataAdmissao');
        parent::addAttribute('Senha');
        parent::addAttribute('RD');
    }


}
