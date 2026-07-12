<?php
/**
 * ViewHorarioCoordenador Active Record (Apenas Leitura)
 */
class ViewHorarioCoordenador extends TRecord
{
    const TABLENAME  = 'view_horario_coordenador';
    const PRIMARYKEY = 'id_virtual';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('id_virtual');
        parent::addAttribute('nome_horario');
        parent::addAttribute('curso');
        parent::addAttribute('periodo');
        parent::addAttribute('etapa');
        parent::addAttribute('ano_semestre');
        parent::addAttribute('qtd_aulas');
        parent::addAttribute('total_registros_celulas');
        parent::addAttribute('ultimo_usuario');
        parent::addAttribute('ultima_alteracao');
    }
}