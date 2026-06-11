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

        $dataAtual = date('Y-m-d');

        $periodo = new ReqBolsaAlunoPeriodo(1);

        if ($dataAtual >= $periodo->data_inicio AND $dataAtual <= $periodo->data_fim )
        {

            TTransaction::open('dados_fei');

            TTransaction::open('Felabs_DB');
            $logado = SystemUser::newFromLogin(TSession::getValue('login'));
            
            TTransaction::close();

            //$ano = date('Y');
            $ano = ("2019");
            //$mes = date('m');
            $semestre = ('1');
        /**
            if($mes < 8)
            {
                $semestre = 1;
            }
            elseif($mes > 7)
            {
                $semestre = 2;
            }*/

            $criteria = new TCriteria;
            $criteria->add(new TFilter('AnoMatricula', '=', $ano));
            $criteria->add(new TFilter('SemestreMatricula', '=', $semestre));
            $criteria->add(new TFilter('Codaluno', '=', $logado->systemuser_codlegado));

            $matricula = VwAlunoMatriculaEtapa::getObjects($criteria);//($logado->systemuser_codlegado);


            if(empty($matricula))//verifca se alguma matricula
            {
                new TMessage('error','Para o preenchimento do Requerimento de Bolsa é necessário estar matriculado.');
            }
            
            elseif($matricula[0]->EtapaMatricula > 1 )//verifica se a matricula eh maior que etapa 1
            {
                new TMessage('error', 'Preenchimento do Requerimento de Bolsa disponível apenas para alunos do 1º ciclo. ');
            }

            else// caso contrario exibe o formulario
            {
            TPage::include_css('app/resources/styles.css');
            // create two actions
            $action1 = new TAction(array('ReqBolsaAlunoQuestion1', 'onShow'));
            $action2 = new TAction(array('ReqBolsaAlunoDialogQuestionView2', 'onTeste'));

            // define os parâmetros de cada ação
            
            // shows the question dialog
            new TQuestion('Você tem Curso Superior?', $action1, $action2);
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
?>