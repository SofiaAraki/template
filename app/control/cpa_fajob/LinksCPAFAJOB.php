<?php
/**

 * @author     Pamella Scapim

 */
class LinksCPAFAJOB extends TPage
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
            $criteria_cpa_fajob = new TCriteria;                        
            $criteria_cpa_fajob->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
            $criteria_cpa_fajob->add(new TFilter('AnoMatricula', '=', 2022)); 
            $criteria_cpa_fajob->add(new TFilter('SemestreMatricula', '=', 1)); 
            $criteria_cpa_fajob->add(new TFilter('CodEntidade', '=', 10)); 

            $alunos_fajob = new TRepository('VwAlunoMatriculaEtapa');
            $alunoSemestre = $alunos_fajob->load($criteria_cpa_fajob);

            foreach($alunoSemestre as $alunoCurso)
            {
                $CodCurso           = $alunoCurso->CodCurso;
                $EtapaMatricula     = $alunoCurso->EtapaMatricula;
                $Periodo            = $alunoCurso->Periodo;

                //var_dump($EtapaMatricula);
            
                if ($CodCurso == 101) //Pedagogia - FAJOB
                {
                    switch ($EtapaMatricula) {
                        
                        case 7:
                            $iframe = new TElement('iframe');
                            $iframe->id = "iframe_external";
                            $iframe->src = "https://forms.gle/qFrZd6k6p6uccLXE8";
                            $iframe->frameborder = "0";
                            $iframe->scrolling = "yes";
                            $iframe->width = "100%";
                            $iframe->height = "700px";
                            break;                    
                    }

                } 

                else if ($CodCurso == 102) //Engenharia Civil - FAJOB
                {
                    switch ($EtapaMatricula)
                    {
                        case 5:
                            $iframe = new TElement('iframe');
                            $iframe->id = "iframe_external";
                            $iframe->src = "https://forms.gle/BETDeZLqTyirNkrf9";
                            $iframe->frameborder = "0";
                            $iframe->scrolling = "yes";
                            $iframe->width = "100%";
                            $iframe->height = "700px";
                            
                            break;
                        case 7:
                            $iframe = new TElement('iframe');
                            $iframe->id = "iframe_external";
                            $iframe->src = "https://forms.gle/sRwWZu9gXsQHSJFx5";
                            $iframe->frameborder = "0";
                            $iframe->scrolling = "yes";
                            $iframe->width = "100%";
                            $iframe->height = "700px";
                            break;
                        
                    }
                }

                else if ($CodCurso == 103) //Engenharia Mecânica - FAJOB
                {
                    switch ($EtapaMatricula)
                    {
                        case 5:
                            $iframe = new TElement('iframe');
                            $iframe->id = "iframe_external";
                            $iframe->src = "https://forms.gle/q3y8kSMR2KqcgcZs5";
                            $iframe->frameborder = "0";
                            $iframe->scrolling = "yes";
                            $iframe->width = "100%";
                            $iframe->height = "700px";
                            break;
                        case 7:
                            $iframe = new TElement('iframe');
                            $iframe->id = "iframe_external";
                            $iframe->src = "https://forms.gle/qprcfK9WA3ziM9c98";
                            $iframe->frameborder = "0";
                            $iframe->scrolling = "yes";
                            $iframe->width = "100%";
                            $iframe->height = "700px";
                            
                            break;
                        
                    }
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
       
       parent::add($iframe);
       
    }
}