<?php
/**
 * VwFiVestibularRequerimento Active Record
 * @author  <your-name-here>
 */
class VwFiVestibularRequerimento extends TRecord
{
    const TABLENAME = 'VW_FI_Vestibular_Requerimento';
    const PRIMARYKEY= 'COD_INSCRICAO_VESTTIBULAR';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('COD_VESTIBULAR');
        parent::addAttribute('Nome');
        parent::addAttribute('RG');
        parent::addAttribute('Filiacao_Pai');
        parent::addAttribute('Filiacao_Mae');
        parent::addAttribute('RG_Origem');
        parent::addAttribute('Endereco');
        parent::addAttribute('Numero');
        parent::addAttribute('Bairro');
        parent::addAttribute('Complemento');
        parent::addAttribute('Cep');
        parent::addAttribute('TEL_CELULAR');
        parent::addAttribute('TEL_RESIDENCIAL');
        parent::addAttribute('TEL_COMERCIAL');
        parent::addAttribute('Email');
        parent::addAttribute('Data_Nascimento');
        parent::addAttribute('CPF');
        parent::addAttribute('CIDADE_NASC');
        parent::addAttribute('UF_NASC');
        parent::addAttribute('UF_RES');
        parent::addAttribute('CIDADE_RES');
        parent::addAttribute('CURSO');
    }


}
