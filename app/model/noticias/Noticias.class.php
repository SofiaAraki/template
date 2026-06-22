<?php
/**
 * Noticias Active Record
 * @author  <your-name-here>
 */
class Noticias extends TRecord
{
    const TABLENAME = 'noticias';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_postagem');
        parent::addAttribute('data_expira');
        parent::addAttribute('data_edicao');
        parent::addAttribute('conteudo');
        parent::addAttribute('autor');
        parent::addAttribute('titulo');
        parent::addAttribute('unidade');
        parent::addAttribute('publico');
    }


    /**
     * Method set_system_user
     * Sample of usage: $noticias->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->autor->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $solicitacao_aluno->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->autor);
    
        // returns the associated object
        return $this->system_user;
    }


}
