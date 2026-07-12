<?php
/**
 * Multi Step 3
 */
class MultiStepRegistration4View extends TPage 
{
    protected $form;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_EnvioDocumentos');
        $this->form->setFormTitle(('Envio de Documento de Identificação'));
        $this->form->setClientValidation(true);
        
        // add the form fields
        $this->form->addFields( [new TLabel('Atenção! É necessário anexar uma foto do documento de identificação com foto (Ex.: RG, Carteira de Habilitação)','red', 12, 'bi')]);
        $this->form->addFields( [new TLabel('É permitido anexar mais de 1 imagem.','red', 10, 'bi')]);
        
        $pagestep = new TPageStep;
        $pagestep->addItem('Selecionar');
        $pagestep->addItem('Assinatura Digital');
        $pagestep->addItem('Enviar Documento de Indentificação');
        $pagestep->addItem('Confirmação');
        $pagestep->select('Enviar Documento de Indentificação');
        
         // add the actions
        
        //$this->form->addActionLink( 'Clear', new TAction([$this, 'onEdit']), 'fa:eraser red');
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
       // $vbox->add(new TXMLBreadCrumb('menu.xml', 'ProductList'));
        $vbox->add( $pagestep );
        $vbox->add($this->form);
        parent::add($vbox);
    }
    
    /**
     * Overloaded method onSave()
     * Executed whenever the user clicks at the save button
     */
    
    
   
}
