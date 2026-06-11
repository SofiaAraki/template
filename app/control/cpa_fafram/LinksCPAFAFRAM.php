<?php
/**
 * SinglePageView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class LinksCPAFAFRAM extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

                
       try 
       {

        TTransaction::open('Felabs_DB');
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $loggedUnit = TSession::getValue('userunitid');
        

        TTransaction::open('dados_fei');
        $criteria_cpa_fafram = new TCriteria;                        
        $criteria_cpa_fafram->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
        $criteria_cpa_fafram->add(new TFilter('AnoMatricula', '=', 2023)); 
        $criteria_cpa_fafram->add(new TFilter('SemestreMatricula', '=', 1)); 
        $criteria_cpa_fafram->add(new TFilter('CodEntidade', '=', 3)); 

        $alunos_fafram = new TRepository('VwAlunoMatriculaEtapa');
        $alunoSemestre = $alunos_fafram->load($criteria_cpa_fafram);

        foreach($alunoSemestre as $alunoCurso)
        {
            $CodCurso           = $alunoCurso->CodCurso;
            $EtapaMatricula     = $alunoCurso->EtapaMatricula;
            $Periodo            = $alunoCurso->Periodo;

            //var_dump($CodCurso);
           
            if (($CodCurso == 15) AND ($EtapaMatricula == 1) AND ($Periodo = "I") )//AGRONOMIA INTEGRAL
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 1º Ciclo - Agronomia Integral 2023.1', 'https://forms.gle/NMw7R33f8xMoERHG7', 'blue', 14, 'biu');
            }
            else if (($CodCurso == 15) AND ($EtapaMatricula == 3) AND ($Periodo = "I"))
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 3º Ciclo - Agronomia Integral 2023.1', 'https://forms.gle/5oSFq3LCxjgEWABv5', 'blue', 14, 'biu');
            }
            else if (($CodCurso == 15) AND ($EtapaMatricula == 5) AND ($Periodo = "I"))
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 5º Ciclo - Agronomia Integral 2023.1', 'https://forms.gle/1ZAtX4SfrKV4dBFA7', 'blue', 14, 'biu');
            }
            else if (($CodCurso == 15) AND ($EtapaMatricula == 6) AND ($Periodo = "I"))
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 6º Ciclo - Agronomia Integral 2023.1', 'https://forms.gle/3MmnJR5pTWmfBR4GA', 'blue', 14, 'biu');
            }
            else if (($CodCurso == 15) AND ($EtapaMatricula == 7) AND ($Periodo = "I"))
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 7º Ciclo - Agronomia Integral 2023.1', 'https://forms.gle/GRcCtiM1RERn8CLG7', 'blue', 14, 'biu');
            }
            else if (($CodCurso == 15) AND ($EtapaMatricula == 8) AND ($Periodo = "I"))
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 8º Ciclo - Agronomia Integral 2023.1', 'https://forms.gle/73Z6xRysbpa6RAwT9', 'blue', 14, 'biu');
            }

            else if (($CodCurso == 15) AND ($EtapaMatricula == 1) AND ($Periodo = "N")) //AGRONOMIA NOTURNO
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 1º Ciclo - Agronomia Noturno 2023.1', 'https://forms.gle/1R7YNcyufKySswxaA', 'blue', 14, 'biu');
            }
            else if (($CodCurso == 15) AND ($EtapaMatricula == 3) AND ($Periodo = "N"))
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 3º Ciclo - Agronomia Noturno 2023.1', 'https://forms.gle/4Gu1oRTVhBwffaSS7', 'blue', 14, 'biu');
            }
            else if (($CodCurso == 15) AND ($EtapaMatricula == 4) AND ($Periodo = "N"))
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 4º Ciclo - Agronomia Noturno 2023.1', 'https://forms.gle/E2HAydWH4yuL6jpf7', 'blue', 14, 'biu');
            }
           

        
        else if (($CodCurso == 70) AND ($EtapaMatricula == 1)) //ENFERMAGEM
        {
           $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 1º Ciclo - Enfermagem 2023.1', 'https://forms.gle/myPC7zR1DJ5x4eE8A', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 70) AND ($EtapaMatricula == 3))
        {
           $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 3º Ciclo - Enfermagem 2023.1', 'https://forms.gle/ebMRcBkCVhuYPNfZ7', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 70) AND ($EtapaMatricula == 5))
        {
           $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 5º Ciclo - Enfermagem 2023.1', 'https://forms.gle/mkxZ1hMqrt2nMntx8', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 70) AND ($EtapaMatricula == 7))
        {
           $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 7º Ciclo - Enfermagem 2023.1', 'https://forms.gle/MXJaJ6Axhb9Hu6VL8', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 70) AND ($EtapaMatricula == 9))
        {
           $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 9º Ciclo - Enfermagem 2023.1', 'https://forms.gle/hfWN3fxYEwUDuzkh9', 'blue', 14, 'biu');
        }

        else if (($CodCurso == 16) AND ($EtapaMatricula == 1)) //DIREITO
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 1º Ciclo - Direito 2023.1', 'https://forms.gle/ZzR4sH3ySEUeghYE7', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 16) AND ($EtapaMatricula == 3))
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 3º Ciclo - Direito 2023.1', 'https://forms.gle/2Pf67UwU3aPPdv5f7', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 16) AND ($EtapaMatricula == 5))
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 5º Ciclo - Direito 2023.1', 'https://forms.gle/737EABtgFwvt4AXp7', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 16) AND ($EtapaMatricula == 7))
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 7º Ciclo - Direito 2023.1', 'https://forms.gle/NWhewm2Tsjjoh4x27', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 16) AND ($EtapaMatricula == 9))
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 9º Ciclo - Direito 2023.1', 'https://forms.gle/kbXquPqrT2DhVBj5A', 'blue', 14, 'biu');
        }

        
        else if (($CodCurso == 20) AND ($EtapaMatricula == 1)) //MEDICINA VETERINÁRIA
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 1º Ciclo - Medicina Veterinária 2023.1', 'https://forms.gle/uHen3Q23gDKhmSnZ7', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 20) AND ($EtapaMatricula == 3) )
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 3º Ciclo - Medicina Veterinária 2023.1', 'https://forms.gle/WZHrYfCJ8y6M8u1z6', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 20) AND ($EtapaMatricula == 5)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 5º Ciclo - Medicina Veterinária 2023.1', 'https://forms.gle/215XRMG94Fj4Yc567', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 20) AND ($EtapaMatricula == 6)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 6º Ciclo - Medicina Veterinária 2023.1', 'https://forms.gle/qCdHvcqw7ePhtBka7', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 20) AND ($EtapaMatricula == 7)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 7º Ciclo - Medicina Veterinária 2023.1', 'https://forms.gle/uPgSVNXJ4yUME1JL6', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 20) AND ($EtapaMatricula == 8)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 8º Ciclo - Medicina Veterinária 2023.1', 'https://forms.gle/3yuc2tmHHqTQieSMA', 'blue', 14, 'biu');
        }
        else if (($CodCurso == 20) AND ($EtapaMatricula == 9)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 9º Ciclo - Medicina Veterinária 2023.1', 'https://forms.gle/MrsBERSBGM6maSPw5', 'blue', 14, 'biu');
        }
        

        else if (($CodCurso == 21) AND ($EtapaMatricula == 1)) //SISTEMAS DE INFORMAÇÃO
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 1º Ciclo - Sistemas de Informação 2023.1', 'https://forms.gle/K2e5zZxmeJuB6MHR6', 'blue', 14, 'biu');
        }     
        else if (($CodCurso == 21) AND ($EtapaMatricula == 2)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 2º Ciclo - Sistemas de Informação 2023.1', 'https://forms.gle/K2e5zZxmeJuB6MHR6', 'blue', 14, 'biu');
        }  
        else if (($CodCurso == 21) AND ($EtapaMatricula == 3)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 3º Ciclo - Sistemas de Informação 2023.1', 'https://forms.gle/uBxVrPqoExNLGxVA8', 'blue', 14, 'biu');
        }   
        else if (($CodCurso == 21) AND ($EtapaMatricula == 4)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 4º Ciclo - Sistemas de Informação 2023.1', 'https://forms.gle/TdQxjVwy2zkP3pHh9', 'blue', 14, 'biu');
        }     
        else if (($CodCurso == 21) AND ($EtapaMatricula == 5)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 5º Ciclo - Sistemas de Informação 2023.1', 'https://forms.gle/TdQxjVwy2zkP3pHh9', 'blue', 14, 'biu');
        }           
        else if (($CodCurso == 21) AND ($EtapaMatricula == 7)) 
        {
            $c2 = new THyperLink('CPA FAFRAM - Questionário Avaliação Disciplinas - 7º Ciclo - Sistemas de Informação 2023.1', 'https://forms.gle/VYhRDQmPaiWis8Q57', 'blue', 14, 'biu');
        }                          
            

    } 
    
    TTransaction::close();
    TTransaction::close();

    $texto = new TLabel('<br>2023.1 - A CPA (Comissão Própria de Avaliação), atendendo a regulamentação do Sistema Nacional de Avaliação do Ensino Superior (SINAES), solicita a você, nosso aluno, preencher com bastante atenção e critério, de forma individual e espontânea, o questionário disponibilizado abaixo.
    <br><br>
    Sua participação é muito importante, pois essas informações subsidiarão futuras ações na instituição.
    <br><br>
    
    ÓTIMO - Acima das expectativas <br>
    BOM - Atingiu as expectativas <br>
    INSUFICIENTE - Abaixo das expectativas <br>
    RUIM - Muito abaixo das expectativas <br>
    <br><br>
    ***Todas as respostas serão coletadas de forma anônima.*** <br><br>', '#285097', 12, 'b'); 
}
catch (Exception $e)
{
    new TMessage('error', $e->getMessage());            
    TTransaction::rollback();
} 
       
       $vbox = new TVBox;
       $vbox->add($texto);
       $vbox->add($c2);
       $vbox->add($c3);
       $vbox->add($c4);
       parent::add( $vbox );
    }
}