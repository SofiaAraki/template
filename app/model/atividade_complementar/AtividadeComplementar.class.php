<?php
/**
 * AtividadeComplementar Active Record
 * @author  <your-name-here>
 */
class AtividadeComplementar extends TRecord
{
    const TABLENAME = 'atividade_complementar';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $diploma_digital_diplomado;
    private $diploma_digital_curso;
    private $historico_digital;
    private $system_user;
    private $CalcularHorasAprovadas;
    private $CalcularHorasPendentes;
       
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_entrada');
        parent::addAttribute('cod_aluno');
        parent::addAttribute('nome_aluno');
        parent::addAttribute('cod_curso');
        parent::addAttribute('nome_curso');
        parent::addAttribute('ano');
        parent::addAttribute('semestre');
        parent::addAttribute('etapa');
        parent::addAttribute('tipo_atividade');
        parent::addAttribute('data_inicio');
        parent::addAttribute('data_termino');
        parent::addAttribute('carga_horaria');
        parent::addAttribute('descricao');
        parent::addAttribute('cod_prof_responsavel');
        parent::addAttribute('titulacao_prof_responsavel');
        parent::addAttribute('arquivo');
        parent::addAttribute('caminho_arquivo');
        parent::addAttribute('status_atividade');
        parent::addAttribute('observacao');
        parent::addAttribute('status_pdfa');
        parent::addAttribute('status_assinatura');
        parent::addAttribute('system_user_id');
        parent::addAttribute('data_reg');        
        parent::addAttribute('categoria_atividade_id');
        parent::addAttribute('cadastro_atividade_id');
        parent::addAttribute('cod_atividade_historico');
    }

    
    /**
     * Method set_diploma_digital_diplomado
     * Sample of usage: $atividade_complementar->diploma_digital_diplomado = $object;
     * @param $object Instance of DiplomaDigitalDiplomado
     */
    public function set_diploma_digital_diplomado(DiplomaDigitalDiplomado $object)
    {
        $this->diploma_digital_diplomado = $object;
        $this->diploma_digital_diplomado_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_diplomado
     * Sample of usage: $atividade_complementar->diploma_digital_diplomado->attribute;
     * @returns DiplomaDigitalDiplomado instance
     */
    public function get_diploma_digital_diplomado()
    {
        // loads the associated object
        if (empty($this->diploma_digital_diplomado))
            $this->diploma_digital_diplomado = new DiplomaDigitalDiplomado($this->diploma_digital_diplomado_id);
    
        // returns the associated object
        return $this->diploma_digital_diplomado;
    }
    
    
    /**
     * Method set_diploma_digital_curso
     * Sample of usage: $atividade_complementar->diploma_digital_curso = $object;
     * @param $object Instance of DiplomaDigitalCurso
     */
    public function set_diploma_digital_curso(DiplomaDigitalCurso $object)
    {
        $this->diploma_digital_curso = $object;
        $this->diploma_digital_curso_id = $object->id;
    }
    
    /**
     * Method get_diploma_digital_curso
     * Sample of usage: $atividade_complementar->diploma_digital_curso->attribute;
     * @returns DiplomaDigitalCurso instance
     */
    public function get_diploma_digital_curso()
    {
        // loads the associated object
        if (empty($this->diploma_digital_curso))
            $this->diploma_digital_curso = new DiplomaDigitalCurso($this->diploma_digital_curso_id);
    
        // returns the associated object
        return $this->diploma_digital_curso;
    }
    
    
    /**
     * Method set_historico_digital
     * Sample of usage: $atividade_complementar->historico_digital = $object;
     * @param $object Instance of HistoricoDigital
     */
    public function set_historico_digital(HistoricoDigital $object)
    {
        $this->historico_digital = $object;
        $this->historico_digital_id = $object->id;
    }
    
    /**
     * Method get_historico_digital
     * Sample of usage: $atividade_complementar->historico_digital->attribute;
     * @returns HistoricoDigital instance
     */
    public function get_historico_digital()
    {
        // loads the associated object
        if (empty($this->historico_digital))
            $this->historico_digital = new HistoricoDigital($this->historico_digital_id);
    
        // returns the associated object
        return $this->historico_digital;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $atividade_complementar->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $atividade_complementar->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    
    
    public function get_CalcularHorasAprovadas()
    {
        $repository = new TRepository('AtividadeComplementar');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cod_aluno', '=', $this->cod_aluno));
        $criteria->add(new TFilter('status_atividade', '=', 'Aprovado'));
        
        $atividades = $repository->load($criteria); 
        
        // CORREÇÃO: Inicializa como array vazio para evitar erro no array_sum
        $horas = []; 
        
        foreach($atividades as $atividade)
        {
            $horas[] = $atividade->carga_horaria;
        }
        
        $this->CalcularHorasAprovadas = array_sum($horas);
        
        if($this->CalcularHorasAprovadas == 0)
        {
            return "0";
        }
        else
        {
            return $this->CalcularHorasAprovadas;          
        }
    } 
    
    
    public function get_CalcularHorasPendentes()
    {
        $repository = new TRepository('AtividadeComplementar');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cod_aluno', '=', $this->cod_aluno));
        $criteria->add(new TFilter('status_atividade', '=', 'Aguardando aprovação'));
        
        $atividades = $repository->load($criteria); 
        
        // CORREÇÃO: Inicializa como array vazio para evitar erro no array_sum
        $horas = []; 
        
        foreach($atividades as $atividade)
        {
            $horas[] = $atividade->carga_horaria;
        }
        
        $this->CalcularHorasPendentes = array_sum($horas);
        
        if($this->CalcularHorasPendentes == 0)
        {
            return "0";
        }
        else
        {
            return $this->CalcularHorasPendentes;
        }
    }   
}