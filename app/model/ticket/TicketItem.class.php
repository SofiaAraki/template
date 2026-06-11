<?php
/**
 * TicketItem Active Record
 * @author  <your-name-here>
 */
class TicketItem extends TRecord
{
    const TABLENAME = 'ticket_item';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ticket_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('destino_user_id');
        parent::addAttribute('descricao');
        parent::addAttribute('anexo');
        parent::addAttribute('data_reg');
    }


}
