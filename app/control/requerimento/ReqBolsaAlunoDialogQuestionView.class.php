<?php

class ReqBolsaAlunoDialogQuestionView extends TPage
{
    public function __construct()
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');
        
        $loggedUnit = TSession::getValue('userunitid');
            
            //var_dump($loggedUnit);

        $dataAtual = date('Y-m-d');

        $periodo = new ReqBolsaAlunoPeriodo(17);

          
        if ($dataAtual >= $periodo->data_inicio AND $dataAtual <= $periodo->data_fim )
        {
            //if ($loggedUnit = '2' OR $loggedUnit = '3')
            if($loggedUnit  = '12') //Requerimentos a partir de 2025 utilizando o sistema Proesc
            {
                
                
                TTransaction::open('dados_fei');

                TTransaction::open('Felabs_DB');
                    $logado = SystemUser::newFromLogin(TSession::getValue('login'));
                TTransaction::close();
                
                /*
            //---------------- Não usou para verificação do Colégio - Sistema Academico Proesc ----------//

                $criteriaC = new TCriteria;
                $criteriaC->add(new TFilter('AnoMatricula', '=', 2025));
                $criteriaC->add(new TFilter('SemestreMatricula', '=', 2));
                $criteriaC->add(new TFilter('Codaluno', '=', $logado->systemuser_codlegado));
                
            
                $matriculaColegio = VwAlunoMatriculaEtapa::getObjects($criteriaC);//($logado->systemuser_codlegado);


                                            
                        if($matriculaColegio)//verifca se alguma matricula
                        {

                            // var_dump($matriculaColegio);
                            // die();
                        //     new TMessage('error','Preenchimento do Colégio.');
                        //     //new TMessage('info','Preenchimento do Requerimento apenas para Renovação de Bolsa de Estudo FE.
                        //     //                     Alunos Ingressantes e Concluintes de 2019, NÂO precisam realizar o preenchimento.
                        //     //                     O PERÍODO NÃO CONTEMPLARÁ');
                            
                        // }
                        
                        // //elseif($matricula[0]->EtapaMatricula = 1 )//verifica se a matricula eh maior que etapa 1
                        // //{
                        // //    new TMessage('error', 'Preenchimento do Requerimento de Bolsa disponível apenas para alunos do 1º ciclo. ');
                    
                        // else// caso contrario exibe o formulario*/
                        // {  */
                
            //---------------- Não usou para verificação do Colégio - Sistema Academico Proesc ----------//

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

        else
        {
            new TMessage('warning', 'Período encerrado para preenchimento do Requerimento de Bolsa.');
        } 
        
    }


    
    public function onTeste()
    {      
    } 
     
}