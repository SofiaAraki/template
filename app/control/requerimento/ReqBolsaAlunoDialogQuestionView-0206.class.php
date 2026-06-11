<?php
/**
 * DialogQuestionView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ReqBolsaAlunoDialogQuestionView extends TPage
{
    public function __construct()
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');
        
        $loggedUnit = TSession::getValue('userunitid');
            
            //var_dump($loggedUnit);

        $dataAtual = date('Y-m-d');

        $periodo = new ReqBolsaAlunoPeriodo(10);

        if ($dataAtual >= $periodo->data_inicio AND $dataAtual <= $periodo->data_fim )
        {
            if ($loggedUnit <> '1' AND $loggedUnit <> '8' AND $loggedUnit <> '12')
            {
                TTransaction::open('dados_fei');

                TTransaction::open('Felabs_DB');
                    $logado = SystemUser::newFromLogin(TSession::getValue('login'));
                TTransaction::close();

                $criteriaF = new TCriteria;
                $criteriaF->add(new TFilter('AnoMatricula', '=', 2022));
                $criteriaF->add(new TFilter('SemestreMatricula', '=', 2));
                $criteriaF->add(new TFilter('Codaluno', '=', $logado->systemuser_codlegado));
                
                //$criteria2 = new TCriteria;
                //$criteria2->add(new TFilter('AnoMatricula', '=', 2021));
                //$criteria2->add(new TFilter('SemestreMatricula', '=', 1));
                //$criteria2->add(new TFilter('Codaluno', '=', $logado->systemuser_codlegado));
                
                //$criteria = new TCriteria;
                //$criteria->add($criteriaF, TExpression::OR_OPERATOR);
                //$criteria->add($criteria2, TExpression::OR_OPERATOR);
                                          
                $matricula = VwAlunoMatriculaEtapa::getObjects($criteriaF);//($logado->systemuser_codlegado);

                //var_dump($matricula);
                //die;
                                            
                        if(empty($matricula))//verifica se alguma matricula
                        {
                            //new TMessage('error','Para o preenchimento do Requerimento de Bolsa é necessário estar matriculado.');
                            new TMessage('error', 'Período encerrado para preenchimento do Requerimento de Bolsa.');
                        }
                        
                        /*elseif($matricula[0]->EtapaMatricula > 1 )//verifica se a matricula eh maior que etapa 1
                        {
                            new TMessage('error', 'Preenchimento do Requerimento de Bolsa disponível apenas para alunos do 1º ciclo. ');
                        }   */              
                        else// caso contrario exibe o formulario*/
                        {
                            TPage::include_css('app/resources/styles.css');
                            // create two actions
                            $action1 = new TAction(array('ReqBolsaAlunoQuestion1', 'onShow'));
                            $action2 = new TAction(array('ReqBolsaAlunoForm', 'onShow'));
                            //$action2 = new TAction(array('ReqBolsaAlunoDialogQuestionView2', 'onTeste'));
                
                            // define os parâmetros de cada ação
                            
                            // shows the question dialog
                            //new TMessage('info','Preenchimento do formulário APENAS para Renovação de Bolsa de Estudo FE.</br>
                            //Serão considerados somente requerimentos de alunos que possuem bolsas de 50% e 100%.</br>
                            //Alunos INGRESSANTES em 2020 NÃO precisam realizar o procedimento.', $action2);
                            new TMessage('info', 'Preenchimento do formulário para Renovação de Bolsas de Estudo FE', $action2);

                        }
            }
            
        else if ($dataAtual >= $periodo->data_inicio AND $dataAtual <= $periodo->data_fim )
        {
            //if ($loggedUnit = '1' AND $loggedUnit = '8')
            if($loggedUnit = '1' OR $loggedUnit = '8' OR $loggedUnit = '12')
            {
                TTransaction::open('dados_fei');

                TTransaction::open('Felabs_DB');
                    $logado = SystemUser::newFromLogin(TSession::getValue('login'));
                TTransaction::close();
                
                
                $criteriaC = new TCriteria;
                $criteriaC->add(new TFilter('AnoMatricula', '=', 2022));
                $criteriaC->add(new TFilter('Codaluno', '=', $logado->systemuser_codlegado));
                
            
                $matriculaColegio = VwAlunoMatriculaEtapa::getObjects($criteriaC);//($logado->systemuser_codlegado);

                //var_dump($matricula);
                
                                            
                        if(empty ($matriculaColegio))//verifca se alguma matricula
                        {
                            new TMessage('error','Preenchimento do Colégio.');
                            //new TMessage('info','Preenchimento do Requerimento apenas para Renovação de Bolsa de Estudo FE.
                            //                     Alunos Ingressantes e Concluintes de 2019, NÂO precisam realizar o preenchimento.
                            //                     O PERÍODO NÃO CONTEMPLARÁ');
                            
                        }
                        
                        //elseif($matricula[0]->EtapaMatricula = 1 )//verifica se a matricula eh maior que etapa 1
                        //{
                        //    new TMessage('error', 'Preenchimento do Requerimento de Bolsa disponível apenas para alunos do 1º ciclo. ');
                    
                        else// caso contrario exibe o formulario*/
                        {
                        TPage::include_css('app/resources/styles.css');
                        // create two actions
                        $action1 = new TAction(array('ReqBolsaAlunoQuestion1', 'onShow'));
                        $action2 = new TAction(array('ReqBolsaAlunoForm', 'onShow'));
                        //$action2 = new TAction(array('ReqBolsaAlunoDialogQuestionView2', 'onTeste'));
            
                        // define os parâmetros de cada ação
                        
                        // shows the question dialog
                        /*new TMessage('info','Preenchimento do formulário APENAS para Renovação de Bolsa de Estudo FE.</br>
                        Serão considerados somente requerimentos de alunos que possuem bolsas de 50% e 100%.</br>
                        Alunos INGRESSANTES em 2020 NÃO precisam realizar o procedimento.', $action2);*/
                        new TMessage('info', 'Preenchimento do formulário para Renovação de Bolsas de Estudo FE.', $action2);
                          
                          //new TMessage('info', 'Preenchimento do formulário para Renovação de Bolsas de Estudo FE apenas para alunos do Ensino Superior.');

                        }
            }
                                        
    
                
                   
        
        
        }
        }
        else{
        new TMessage('info', 'Período encerrado para preenchimento do Requerimento de Bolsa.');

        } 
        
    }
    
    public function onTeste()
    {      
    } 
     
}
/*?>  else{
        new TMessage('info', 'Período encerrado para preenchimento do Requerimento de Bolsa.');

        } 
        
    }
    
    public function onTeste()
    {      
    }
     
}*/
?>