<?php
class ManualProfPDFView extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('MANUAL DE UTILIZAÇÃO');
        parent::setSize(0.8, 0.8);
        
        $object = new TElement('object');
        $object->data  = 'app/documents/MANUAL DE UTILIZAÇÃO_atualizado.pdf';
        $object->type  = 'application/pdf';
        $object->style = "width: 100%; height:calc(100% - 10px)";
        
        parent::add($object);
    }

    public function onShow()
    {     
        
    }
}