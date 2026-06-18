<?php

class TutorialRequerimento extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $cards = new TCardView;
        $cards->setUseButton();
        $items = [];
        $items[] = (object) [ 'id' => 1, 'title' => 'Tutorial', 'source' => '4tnqOw8hHcY'];

        foreach ($items as $key => $item)
        {
            $cards->addItem($item);
        }
        
        $cards->setTitleAttribute('title');
        
        $cards->setItemTemplate('<iframe width="100%" height="100%" src="https://www.youtube.com/embed/{source}""></iframe>');
        
        $action = new TAction([$this, 'onGotoVideo'], ['source'=>'{source}']);
        $cards->addAction($action, 'Assista no Youtube', 'far:play-circle red');
        
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($cards);
        parent::add($vbox);
    }
    

    public static function onGotoVideo($param = NULL)
    {
        $source = $param['source'];
        TScript::create("window.open('https://www.youtube.com/watch?v={$source}')");
    }
}
