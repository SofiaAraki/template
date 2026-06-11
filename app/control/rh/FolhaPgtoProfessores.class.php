<?php
class FolhaPgtoProfessores extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Folha de Pagamento Professores');
        parent::removePadding();
        parent::setSize(0.3, 0.3);
        
        $iframe = new TElement('iframe');
        $iframe->id = "iframe_external";
        $iframe->src = "public/rh/folha.zip";
        $iframe->frameborder = "0";
        $iframe->scrolling = "yes";
        $iframe->width = "50%";
        $iframe->height = "150px";
        
        parent::add($iframe);
    }
}
