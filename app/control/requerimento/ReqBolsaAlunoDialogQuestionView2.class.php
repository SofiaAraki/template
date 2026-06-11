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
class ReqBolsaAlunoDialogQuestionView2 extends TPage
{
    public function __construct()
    {
        parent::__construct();

        TPage::include_css('app/resources/styles.css');
        
        // create two actions
        //$action1 = new TAction(array($this, 'onAction1'));
        //$action2 = new TAction(array($this, 'onAction2'));
        //$action1 = new TAction(array($this, 'onAction1'));
        //$action1 = new TAction(array('Teste', 'onShow'));
        $action1 = new TAction(array('ReqBolsaAlunoForm', 'onShow'));
        $action2 = new TAction(array('ReqBolsaAlunoQuestion2', 'onShow'));
        //$action3 = new TAction(array($this, 'onAction3'));

        // define os parâmetros de cada ação
        //$action1->setParameter('parameter', 1);
        //$action2->setParameter('parameter', 2);
        
        // shows the question dialog
        new TQuestion('Você já possui Cadastro no CadÚnico?', $action1, $action2);
        
        //parent::add(new TXMLBreadCrumb('menu.xml', __CLASS__));
    }
    
    //function onAction1()
    //{
    //   new TMessage('info', "Você não pode solicitar a bolsa.");

        //TApplication::loadPage('DespesaForm', 'onReload');
    //}
    
    //function onAction2($param)
    //{
    //    new TMessage('info', "Você tem cadastro no Cadúnico? Parameter value {$param['parameter']}");
    //}

    public function onTeste()
    {      
    }
}
?>