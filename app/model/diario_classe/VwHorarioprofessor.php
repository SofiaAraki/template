<?php
/**
 * VwHorarioprofessor Active Record
 * @author  <your-name-here>
 */
class VwHorarioprofessor extends TRecord
{
    const TABLENAME = 'VW_HorarioProfessor';
    const PRIMARYKEY= 'CodGradeDisciplinaEtapa_Frente';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodGradecurso');
        parent::addAttribute('CodCurso');
        parent::addAttribute('CodTurmaetapa');
        parent::addAttribute('CodGradeDisciplinaEtapa');
        parent::addAttribute('CodGradeDisciplinaEtapa_Frente');
        parent::addAttribute('CodDisciplina');
        parent::addAttribute('Ano');
        parent::addAttribute('Semestre');
        parent::addAttribute('Bimestre');
        parent::addAttribute('Identificacao');
        parent::addAttribute('Periodo');
        parent::addAttribute('NomeCurso');
        parent::addAttribute('Etapa');
        parent::addAttribute('QuantidadeAvaliacoes');
        parent::addAttribute('NomeProfessor');
        parent::addAttribute('NomeDisciplina');
        parent::addAttribute('Habilitacao1');
        parent::addAttribute('DiaSemana');
        parent::addAttribute('HoraAula');
        parent::addAttribute('InicioAula');
        parent::addAttribute('NumeroOrdemAula');
        parent::addAttribute('DuracaoAula');
        parent::addAttribute('IntervalorAula');
        parent::addAttribute('DuracaoIntervalo');
        parent::addAttribute('CodEntidade');
        parent::addAttribute('NomeFantasia');
        parent::addAttribute('CodCalendarioCurso');
        parent::addAttribute('NomeDiaSemana');
        parent::addAttribute('Codprofessor');
        

        


        
    }


}
