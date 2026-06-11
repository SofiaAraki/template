<?php
class TutorialRequerimentoConnextPDFView extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        $object = new TElement('iframe');
        $object->src   = 'arquivos/requerimento_bolsa/TUTORIAL.pdf';
        $object->type  = 'application/pdf';
        $object->style = "width: 100%; height:600px";
        
        parent::add($object);
    }
}
