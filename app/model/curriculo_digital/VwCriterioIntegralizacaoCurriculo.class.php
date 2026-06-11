<?php
/**
 * VwCriterioIntegralizacaoCurriculo Active Record
 * @author  <your-name-here>
 */
class VwCriterioIntegralizacaoCurriculo extends TRecord
{
    const TABLENAME = 'Vw_CriterioIntegralizacaoCurriculo';
    const PRIMARYKEY= 'curriculo_disciplina_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('curriculo_digital_id');
        parent::addAttribute('opcao_disciplina');
        parent::addAttribute('tipo_unidade');
        parent::addAttribute('ch_hora_aula_disciplina');
        parent::addAttribute('ch_hora_relogio_disciplina');
        parent::addAttribute('dados_etiqueta_id');
        parent::addAttribute('nome_etiqueta');
        parent::addAttribute('ch_hora_aula_etiqueta');
        parent::addAttribute('ch_hora_relogio_etiqueta');
    }


}
