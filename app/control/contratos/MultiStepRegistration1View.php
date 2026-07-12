<?php
/**
 * @author     Pamella Scapim
 */
class MultiStepRegistration1View extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        $link1 = new TActionLink('ACEITAR E ASSINAR DIGITALMENTE', new TAction(array($this, 'onAction1')), 'fa:check');
        $link1->class = 'btn btn-success btn-lg';
        $hbox_actions = THBox::pack($link1);        
       
        try
        {
            // create the HTML Renderer
            $this->html = new THtmlRenderer('app/resources/ContratoFinanceiro.html');
            $confirmation_data = array_merge(TSession::getValue('sessao_contrato'));
            $this->html->enableSection('main', $confirmation_data);

            $pagestep = new TPageStep;
            $pagestep->addItem('Selecionar');
            $pagestep->addItem('Assinatura Digital');
            $pagestep->addItem('Enviar Documento de Indentificação');
            $pagestep->select('Assinatura Digital');

            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add( $pagestep );
            $vbox->add('<br>');
            $vbox->add( $this->html );
            $vbox->add($hbox_actions);
            parent::add($vbox);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    function onAction1()
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            $sessao_contrato = TSession::getValue('sessao_contrato');
            $id = $sessao_contrato["key"];
            $CodigoAluno = $sessao_contrato["Codaluno"];
            $CPFAluno = $sessao_contrato["CPF"];

            $MD5AssinaturaAluno = md5('Cod'.$CodigoAluno .'_' .'CPF'. $CPFAluno.'_' .'Assinado');

            //echo $MD5AssinaturaAluno;

            

            $object = ContratoDadosAluno::find($id);

            if ($object) 
            { 
               
                $object->StatusContrato = ('Assinado Pelo Aluno / Envio de Documento Pendente');
                $object->AssinaturaAluno = $MD5AssinaturaAluno;
                $object->IPAluno = $_SERVER['REMOTE_ADDR'];      
                                
                $object->store();             
                TTransaction::close();
            }
            
            new TMessage('info', ('Requerimento e Contrato assinados com sucesso!'));
            AdiantiCoreApplication::loadPage('MultiStepRegistration3View');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            //$this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    
    function loadPage()
    {}
}
