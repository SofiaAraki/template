<?php
/**
 * Page center
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FolhaPgtoProfessoresRH extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/page_center.html');
        
        try
        {
            // enable main section
            $this->html->enableSection('main');
            
            $panel = new TPanelGroup('Download Programas / Utilitários');
            $panel->add($this->html);
            
            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($panel);
            
            parent::add($vbox);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Load page by action
     */

    /**
     * Load window by action
     */
    public static function onLoadWindow($param)
    {
        AdiantiCoreApplication::loadPage('FolhaPgtoProfessores', null, ['register_state' => 'false']);
    }
    
  
       /**
     * Create an ondemand window
     */
    public static function onCreateWindow($param)
    {
        $window = TWindow::create('Download Programas / Utilitários', 0.3, 0.3);
        
        $iframe = new TElement('iframe');
        $iframe->id = "iframe_external";
        $iframe->src = "public/rh/folha.zip";
        $iframe->frameborder = "0";
        //$iframe->scrolling = "yes";
        $iframe->width = "50%";
        $iframe->height = "50px";
        
        
        $window->add($iframe);
        $window->show();
    }
}
