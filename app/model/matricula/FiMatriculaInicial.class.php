<?php

/**
 * FiMatriculaInicial Active Record
 * @author  Gerei para você espelhando a tabela FI_MatriculaInicial
 */
class FiMatriculaInicial extends TRecord
{
    const TABLENAME = 'FI_MatriculaInicial';
    const PRIMARYKEY= 'CodMatriculaInicial';
    const IDPOLICY =  'max'; // {max, serial} - Geralmente tabelas legadas utilizam max para evitar problemas de concorrência ou serial se for IDENTITY.

    use SystemChangeLogTrait; // Habilita o Log de alterações nativo do Adianti, caso utilize

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        
        // Atributos baseados estritamente nas colunas da tabela do SQL Server
        parent::addAttribute('Codaluno');
        parent::addAttribute('codresponsavel');
        parent::addAttribute('CodOperador');
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('EscolaEnsinoMedio');
        parent::addAttribute('EscolaEnsinoMedioLocal');
        parent::addAttribute('VestibularAno');
        parent::addAttribute('VestibularEstabelecimento');
        parent::addAttribute('VestibularTotalPontos');
        parent::addAttribute('VestibularClassificacao');
        parent::addAttribute('TipoIngresso');
        parent::addAttribute('DataIngresso');
        parent::addAttribute('DataConclusao');
        parent::addAttribute('RM');
        parent::addAttribute('EtapaInicial');
        parent::addAttribute('CodRestricaoMatricula');
        parent::addAttribute('ESTAGIO_DataInicio');
        parent::addAttribute('ESTAGIO_DataFim');
        parent::addAttribute('ESTAGIO_TotalHr');
        parent::addAttribute('ESTAGIO_TotalMin');
        parent::addAttribute('ESTAGIO_Historico');
        parent::addAttribute('ESTAGIO_DataLancamento');
        parent::addAttribute('TCC_nota');
        parent::addAttribute('TCC_Tema');
        parent::addAttribute('TCC_Observacao');
        parent::addAttribute('TCC_BancaExame');
        parent::addAttribute('TCC_DataApresentacao');
        parent::addAttribute('EstagioRemunerado');
        parent::addAttribute('EstagioRemuneradoLocal');
        parent::addAttribute('NProcesso');
        parent::addAttribute('Local');
        parent::addAttribute('Arquivo');
        parent::addAttribute('Gaveta');
    }
}