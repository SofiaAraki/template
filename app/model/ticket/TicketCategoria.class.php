<?php
/**
 * TicketCategoria Active Record
 * @author  <your-name-here>
 */
class TicketCategoria extends TRecord
{
    const TABLENAME = 'ticket_categoria';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('departamento_id');
        parent::addAttribute('nome');
        parent::addAttribute('exemplo_msg');
    }


}
