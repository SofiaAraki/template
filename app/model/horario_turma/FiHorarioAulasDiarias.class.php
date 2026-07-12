<?php
class FiHorarioAulasDiarias extends TRecord
{
    const TABLENAME  = 'FI_Horario_AulasDiarias';
    const PRIMARYKEY = 'CodHorario_AulasDiarias';
    const IDPOLICY   = 'serial'; // se for auto-incremento, caso contrário ajuste

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('Codhorario');
        parent::addAttribute('CodAtribuicaoGradeTurma');
        parent::addAttribute('DiaSemana');
        parent::addAttribute('NumeroOrdemAula');
        parent::addAttribute('HoraAula');
        parent::addAttribute('Compartilhada');
        parent::addAttribute('Extras');
    }
}