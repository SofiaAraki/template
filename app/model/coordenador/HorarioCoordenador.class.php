<?php

class HorarioCoordenador extends TRecord
{
    const TABLENAME  = 'horario_coordenador';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nome_horario');
        parent::addAttribute('curso');
        parent::addAttribute('periodo');
        parent::addAttribute('etapa');
        parent::addAttribute('ano_semestre');
        parent::addAttribute('qtd_aulas');
        parent::addAttribute('dia_semana');
        parent::addAttribute('numero_ordem_aula');
        parent::addAttribute('horario_aula');
        parent::addAttribute('disciplina');
        parent::addAttribute('professor');
        parent::addAttribute('eh_intervalo');
        parent::addAttribute('usuario_horario_coordenador');
        parent::addAttribute('data_horario_coordenador');
    }
}