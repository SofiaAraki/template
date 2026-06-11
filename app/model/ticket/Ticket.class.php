<?php
/**
 * Ticket Active Record
 * @author  <your-name-here>
 */
class Ticket extends TRecord
{
    const TABLENAME = 'ticket';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
     //   parent::addAttribute('titulo');
        parent::addAttribute('descricao');
        parent::addAttribute('system_user_id');
        parent::addAttribute('status');
        parent::addAttribute('departamento');
        parent::addAttribute('categoria');
        parent::addAttribute('data_reg');
        parent::addAttribute('quem_abriu');
        parent::addAttribute('ultima_edicao');
        parent::addAttribute('edicao_user_id');
        parent::addAttribute('matricula_aluno');
    }



     /**
     * Method set_system_user
     * Sample of usage: $ticket->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $ticket->system_user->attribute;
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
     * Method set_ticket_categoria
     * Sample of usage: $ticket->ticket_categoria = $object;
     * @param $object Instance of TicketCategoria
     */
    public function set_ticket_categoria(TicketCategoria $object)
    {
        $this->ticket_categoria = $object;
        $this->categoria = $object->id;
    }
    
    /**
     * Method get_ticket_categoria
     * Sample of usage: $ticket->ticket_categoria->attribute;
     * @returns TicketCategoria instance
     */
    public function get_ticket_categoria()
    {
        // loads the associated object
        if (empty($this->ticket_categoria))
            $this->ticket_categoria = new TicketCategoria($this->categoria);
    
        // returns the associated object
        return $this->ticket_categoria;
    }



    /**
     * Method set_system_user
     * Sample of usage: $ticket->system_user = $object;
     * @param $object Instance of SystemUser
     */


    public function set_gestor(SystemUser $object)
    {
        $this->gestor = $object;
        $this->quem_abriu = $object->id;
    }
    
   
    public function get_gestor()
    {
        // loads the associated object
        if (empty($this->gestor))
            $this->gestor = new SystemUser($this->quem_abriu);
    
        // returns the associated object
        return $this->gestor;
    }



    public function set_edicao_user(SystemUser $object)
    {
        $this->edicao_user = $object;
        $this->edicao_user_id = $object->id;
    }
    
   
    public function get_edicao_user()
    {
        // loads the associated object
        if (empty($this->edicao_user))
            $this->edicao_user = new SystemUser($this->edicao_user_id);
    
        // returns the associated object
        return $this->edicao_user;
    }




}
