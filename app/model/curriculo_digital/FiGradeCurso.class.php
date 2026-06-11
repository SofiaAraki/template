<?php
/**
 * FiGradeCurso Active Record
 * @author  <your-name-here>
 */
class FiGradeCurso extends TRecord
{
    const TABLENAME = 'FI_GradeCurso';
    const PRIMARYKEY= 'CodGradecurso';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $fi_curso;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodOperador');
        parent::addAttribute('CodCoordenador');
        parent::addAttribute('CodCurso');
        parent::addAttribute('Descricao');
        parent::addAttribute('QtdeEtapas');
        parent::addAttribute('DuracaoEtapa');
        parent::addAttribute('AnoInicial');
        parent::addAttribute('DataCadastro');
        parent::addAttribute('Habilitacao1');
        parent::addAttribute('Habilitacao2');
        parent::addAttribute('Reconhecimento');
        parent::addAttribute('CargaHorariaTotal');
        parent::addAttribute('ValorTotalCurso');
        parent::addAttribute('ValorMensalidade');
        parent::addAttribute('DataUltimoReajuste');
        parent::addAttribute('IndicePercentual');
        parent::addAttribute('TCC');
        parent::addAttribute('TCC_Nota');
        parent::addAttribute('AtivComp');
        parent::addAttribute('AtivCom_CH');
        parent::addAttribute('Estagio');
        parent::addAttribute('Estagio_CH');
        parent::addAttribute('homologacao_codoperador');
        parent::addAttribute('homologacao_data');
        parent::addAttribute('homologacao_hora');
        parent::addAttribute('homologacao_obs');
    }


    /**
     * Method set_fi_curso
     * Sample of usage: $fi_gradecurso->fi_curso = $object;
     * @param $object Instance of FiCurso
     */
    public function set_fi_curso(FiCurso $object)
    {
        $this->fi_curso = $object;
        $this->CodCurso = $object->id;
    }
    
    /**
     * Method get_fi_curso
     * Sample of usage: $fi_gradecurso->fi_curso->attribute;
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

}
