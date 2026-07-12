<?php

class FISistemaAvaliacao extends TRecord
{
    const TABLENAME  = 'FI_SistemaAvaliacao';
    const PRIMARYKEY = 'CodSistemaAvaliacao';
    const IDPOLICY   = 'serial'; // Mude para 'manual' se o banco não for auto-incremento

    private $operador;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        
        // Mapeamento de todos os atributos da tabela
        parent::addAttribute('Descricao');
        parent::addAttribute('ProvaIntegrada');
        parent::addAttribute('TipoNota');
        parent::addAttribute('PromocaoFreqMinima');
        parent::addAttribute('NotaMaxima');
        parent::addAttribute('NotaMinima');
        parent::addAttribute('PromocaoMedia');
        parent::addAttribute('Variacao');
        parent::addAttribute('ArredCasasDecimais');
        parent::addAttribute('ArredIndice');
        parent::addAttribute('DataCadastro');
        parent::addAttribute('FormulaMediaEtapa');
        
        // Descrições de Recuperação
        parent::addAttribute('DescrRecuperacao1');
        parent::addAttribute('DescrRecuperacao2');
        parent::addAttribute('DescrRecuperacao3');
        parent::addAttribute('DescrRecuperacao4');
        parent::addAttribute('DescrRecuperacao5');
        
        // Fórmulas de Recuperação
        parent::addAttribute('FormulaRecuperacao1');
        parent::addAttribute('FormulaRecuperacao2');
        parent::addAttribute('FormulaRecuperacao3');
        parent::addAttribute('FormulaRecuperacao4');
        parent::addAttribute('FormulaRecuperacao5');
        
        // Médias de Recuperação
        parent::addAttribute('MediaRecuperacao1');
        parent::addAttribute('MediaRecuperacao2');
        parent::addAttribute('MediaRecuperacao3');
        parent::addAttribute('MediaRecuperacao4');
        parent::addAttribute('MediaRecuperacao5');
        
        // Parâmetros Gerais de Recuperação e Cálculo
        parent::addAttribute('RecuperacaoFreqMinima');
        parent::addAttribute('RecuperacaoMedia');
        parent::addAttribute('LimiteDisciplinaRecuperacao');
        parent::addAttribute('CodOperador');
        parent::addAttribute('QuantidadeRecuperacao');
        parent::addAttribute('VariacaoArrend');
        parent::addAttribute('TipoCalcFreq');
        parent::addAttribute('Recuperacao');
        parent::addAttribute('MediaGeralFreq');
    }

    /**
     * Relacionamento com o Operador (FI_Operador)
     * Retorna o objeto FIOperador associado a este sistema de avaliação
     */
    public function get_operador()
    {
        if (empty($this->operador)) {
            $this->operador = new FIOperador($this->CodOperador);
        }
        return $this->operador;
    }
}