<?php

class ConsultaDiplomaDigitalForm extends TPage
{
    public function __construct($param)
    { 

        parent::__construct();


        $this->html = new THtmlRenderer('app/resources/RecaptchaDiploma.html');      

        $replace = array();
        $replace['url_amigavel'] = $param['url_amigavel'];

        $this->html->enableSection('main', $replace);
 
        parent::add($this->html);
    }
    
    
    public function onLoad()
    {
    }
}    