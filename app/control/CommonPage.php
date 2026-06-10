<?php
class CommonPage extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/jumbotron.html');
        
        $ini = AdiantiApplicationConfig::get();
        
        $replaces = ['title' => _t('Welcome'),
                     'content' => $ini['general']['welcome_message'] ?? ''];
        
        // replace the main section variables
        $this->html->enableSection('main', $replaces);
        
        parent::add( $this->html );
    }
}