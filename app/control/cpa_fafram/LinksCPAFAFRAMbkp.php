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
        $criteria_cpa_fafram->add(new TFilter('AnoMatricula', '=', 2022)); 
        $criteria_cpa_fafram->add(new TFilter('SemestreMatricula', '=', 2)); 
        $criteria_cpa_fafram->add(new TFilter('CodEntidade', '=', 3)); 

        $alunos_fafram = new TRepository('VwAlunoMatriculaEtapa');
        $alunoSemestre = $alunos_fafram->load($criteria_cpa_fafram);

        foreach($alunoSemestre as $alunoCurso)
        {
            $CodCurso           = $alunoCurso->CodCurso;
            $EtapaMatricula     = $alunoCurso->EtapaMatricula;
            $Periodo            = $alunoCurso->Periodo;

            //var_dump($CodCurso);
           
            if ($CodCurso == 15)
            {
                $c2 = new THyperLink('CPA FAFRAM - Questionário Institucional Discentes 2022.2', 'https://forms.gle/DSgEBEj2TzDzTVc88', 'blue', 14, 'biu');
                $c3 = new THyperLink('CPA FAFRAM - Questionário Fazenda Experimental 2022.2', 'https://forms.gle/2e9kPAreAGqRxWiJ9', 'blue', 14, 'biu');
                        
            }

        
        else if ($CodCurso == 70) 
        {
            
                $c2 = new THyperLink('CPA FAFRAM - Questionário Institucional Discentes 2022.2', 'https://forms.gle/DSgEBEj2TzDzTVc88', 'blue', 14, 'biu');
                                        
           
        }

        else if ($CodCurso == 16) 
        {
            
                $c2 = new THyperLink('CPA FAFRAM - Questionário Institucional Discentes 2022.2', 'https://forms.gle/DSgEBEj2TzDzTVc88', 'blue', 14, 'biu');
                                        
            
        }

        else if ($CodCurso == 20) 
        {
            
                $c2 = new THyperLink('CPA FAFRAM - Questionário Institucional Discentes 2022.2', 'https://forms.gle/DSgEBEj2TzDzTVc88', 'blue', 14, 'biu');
                $c3 = new THyperLink('CPA FAFRAM - Questionário Instalações zootécnicas 2022.2', 'https://forms.gle/RnCFEqfmyxwDrefW9', 'blue', 14, 'biu');
                $c4 = new THyperLink('CPA FAFRAM - Questionário Hospital Veterinário 2022.2 ', 'https://forms.gle/G9EdXvrwgpvvkR786', 'blue', 14, 'biu');
                        
            
        }

        else if ($CodCurso == 21) 
        {
            
                $c2 = new THyperLink('CPA FAFRAM - Questionário Institucional Discentes 2022.2', 'https://forms.gle/DSgEBEj2TzDzTVc88', 'blue', 14, 'biu');
        }                          
            

    } 
    
    TTransaction::close();
    TTransaction::close();
}
catch (Exception $e)
{
    new TMessage('error', $e->getMessage());            
    TTransaction::rollback();
} 
       
       $vbox = new TVBox;
       $vbox->add($c2);
       $vbox->add($c3);
       $vbox->add($c4);
       parent::add( $vbox );
    }
}