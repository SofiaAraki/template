<?php

class ComunicadoBolsaView extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        
        $this->setSize(0.9, 0.8);
        $this->style = 'display: table;width:100%';
        parent::setProperty('class', 'window_modal');
        
        
        //Bloqueia o botão close
        $close_action = new TAction([$this, 'onClose']);
        $close_action->setParameter('id', parent::getId());
        parent::setCloseAction($close_action);

        $comunicado = TSession::getValue('dados_comunicado');        


        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/comunicado_bolsa.html');
        
        $replace = array();
        $replace['conteudo'] = $comunicado->conteudo;
        
        $this->html->disableHtmlConversion();
        
        // replace the main section variables
        $this->html->enableSection('main', $replace);
        
        parent::add($this->html);
    }
    
    
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $usuario_id = TSession::getValue('userid');
            $comunicado = TSession::getValue('dados_comunicado');        
                     
                        
            $object = new ComunicadoBolsaAceite;
            
            $object->comunicado_id = $comunicado->id;
            $object->system_user_id = TSession::getValue('userid');
            $object->status_aceite = "Notificação recebida";
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store();

            TTransaction::close();
            
            //new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TWindow::closeWindow();
            
            TApplication::loadPage('WelcomeView');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }
    
    public static function onClose($param)
    {
        //parent::closeWindow($param['id']);
        //AdiantiCoreApplication::loadPage('SinglePageView');
    }
}

