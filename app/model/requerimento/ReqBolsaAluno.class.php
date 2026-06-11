<?php
/**
 * ReqBolsaAluno Active Record
 * @author  <your-name-here>
 */
class ReqBolsaAluno extends TRecord
{
    const TABLENAME = 'req_bolsa_aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    private $system_user;
    private $system_unit;
    private $req_bolsa_aluno_despesa;
    private $req_bolsa_aluno_item;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('nome');
        parent::addAttribute('curso');
        parent::addAttribute('ciclo');
        parent::addAttribute('periodo');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
        parent::addAttribute('data_nascimento');
        parent::addAttribute('estado_civil');
        parent::addAttribute('profissao');
        parent::addAttribute('endereco');
        parent::addAttribute('endereco_numero');
        parent::addAttribute('bairro');
        parent::addAttribute('endereco_complemento');
        parent::addAttribute('cidade');
        parent::addAttribute('estado');
        parent::addAttribute('cep');
        parent::addAttribute('telefone');
        parent::addAttribute('celular');
        parent::addAttribute('telefone_trabalho');
        parent::addAttribute('email');
        parent::addAttribute('data_reg');
        parent::addAttribute('situacao');
        parent::addAttribute('moradia');
        parent::addAttribute('moradia_aluno');
        parent::addAttribute('saude_familiar');
        parent::addAttribute('saude_aluno');
        parent::addAttribute('saude_aluno_neces');
        parent::addAttribute('veiculo_aluno');
        parent::addAttribute('ensino_aluno');
        parent::addAttribute('checar');
        parent::addAttribute('filename');
        parent::addAttribute('obs');
        parent::addAttribute('renda_familiar');
        parent::addAttribute('n_pessoa');
        parent::addAttribute('renda_percapita');
        parent::addAttribute('rf_salario_minimo');
        parent::addAttribute('rp_salario_minimo');
        parent::addAttribute('data_final');
        parent::addAttribute('unidade');
        parent::addAttribute('cad_unico');
        parent::addAttribute('obs_ass_social');
        //parent::addAttribute('Codaluno');
        parent::addAttribute('outra_graduacao');
        parent::addAttribute('graduacao_anterior');
        parent::addAttribute('renda_familiar_apurada');
        parent::addAttribute('n_pessoas_apurado');
        parent::addAttribute('renda_percapita_apurada');
        parent::addAttribute('rf_salario_minimo_apurada');
        parent::addAttribute('rp_salario_minimo_apurada');
        //campos novos - Pamella
        parent::addAttribute('salario_minimo_atual');
        parent::addAttribute('documentos_check');
        parent::addAttribute('analise_check');
        parent::addAttribute('percentual_bolsa');
        parent::addAttribute('data_parecer_assist_social');
        parent::addAttribute('aluno_retido');
        parent::addAttribute('aluno_pendencia_finc');
        parent::addAttribute('parecer_comissao');
        parent::addAttribute('situacaofinal_bolsa');
        parent::addAttribute('obs_final_assistente');
        


    }

    
    /**
     * Method set_system_user
     * Sample of usage: $aluno->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $aluno->system_user->attribute;
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
    
    /**
     * Method set_system_unit
     * Sample of usage: $req_bolsa_aluno->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $req_bolsa_aluno->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unidade);
    
        // returns the associated object
        return $this->system_unit->name;
    }
    
    
    /**
     * Method set_req_bolsa_aluno_item
     * Sample of usage: $req_bolsa_aluno->req_bolsa_aluno_item = $object;
     * @param $object Instance of ReqBolsaAlunoItem
     */
    public function set_req_bolsa_aluno_item(ReqBolsaAlunoItem $object)
    {
        $this->req_bolsa_aluno_item = $object;
        $this->req_bolsa_aluno_id = $object->id;
    }
    
    /**
     * Method get_req_bolsa_aluno_item
     * Sample of usage: $req_bolsa_aluno->req_bolsa_aluno_item->attribute;
     * @returns ReqBolsaAlunoItem instance
     */
    public function get_req_bolsa_aluno_item()
    {
        // loads the associated object
        if (empty($this->req_bolsa_aluno_item))
            $this->req_bolsa_aluno_item = new ReqBolsaAlunoItem($this->req_bolsa_aluno_id);
    
        // returns the associated object
        return $this->req_bolsa_aluno_item;
    }
    
    //here
    /**
     * Method set_req_bolsa_aluno_despesa
     * Sample of usage: $req_bolsa_aluno->req_bolsa_aluno_despesa = $object;
     * @param $object Instance of ReqBolsaAlunoDespesa
     */
    public function set_req_bolsa_aluno_despesa(ReqBolsaAlunoDespesa $object)
    {
        $this->req_bolsa_aluno_despesa = $object;
        $this->req_bolsa_aluno_id = $object->id;
    }
    
    /**
     * Method get_req_bolsa_aluno_despesa
     * Sample of usage: $req_bolsa_aluno->req_bolsa_aluno_despesa->attribute;
     * @returns ReqBolsaAlunoDespesa instance
     */
    public function get_req_bolsa_aluno_despesa()
    {
        // loads the associated object
        if (empty($this->req_bolsa_aluno_despesa))
            $this->req_bolsa_aluno_despesa = new ReqBolsaAlunoDespesa($this->req_bolsa_aluno_id);
    
        // returns the associated object
        return $this->req_bolsa_aluno_despesa;
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related DespesaItem objects
        $repository = new TRepository('ReqBolsaAlunoItem');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('req_bolsa_aluno_id', '=', $id));
        $repository->delete($criteria);
        
        $repository = new TRepository('ReqBolsaAlunoDespesa');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('req_bolsa_aluno_id', '=', $id));
        $repository->delete($criteria);
    
        // delete the object itself
        parent::delete($id);
    }


}
