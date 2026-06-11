<?php

class AtividadeAlunoForum extends TPage
{
    private $html;
    private $pageNavigation;
    

    public function __construct()
    {
        parent::__construct();
        
        // load the styles
        //TPage::include_css('app/resources/catalog.css');
        TTransaction::open('Felabs_DB');
        $ativInfo = new Atividade(TSession::getValue('atividadeid'));
        $profInfo = new SystemUser($ativInfo->system_user_id);
        

        $criteria = new TCriteria;
        $criteria->add(new TFilter('atividade_id', '=', TSession::getValue('atividadeid')));

        $count = AtividadeAluno::countObjects($criteria);
            
        TTransaction::close();
      
        
        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/forum.html');


        // define replacements for the main section
        $replace = array(); 
        $replace['prof_name'] = $profInfo->name;
        $replace['ativ_name'] = $ativInfo->nome;
        $replace['descricao'] = $ativInfo->descricao;
        $replace['data'] = $ativInfo->data_reg;
        $replace['num_respostas'] = $count;
        
        
        // replace the main section variables
        $this->html->enableSection('main', $replace);
        
        //$this->enableManagement();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->html);
        $vbox->add($this->pageNavigation);

        parent::add($vbox);
    }
  

    /*
    public function onBuyClick( $param )
    {
        $cart_items = TSession::getValue('cart_items');
        if (isset($cart_items[ $param['product_id'] ]))
        {
            $cart_items[ $param['product_id'] ] ++;
        }
        else
        {
            $cart_items[ $param['product_id'] ] = 1;
        }
        
        TSession::setValue('cart_items', $cart_items);
        
        $this->enableManagement();
        
        $posAction = new TAction( array('CartManagementView', 'onReload') );
        new TMessage('info', 'You have chosen the product: ' . $param['product_id'], $posAction);
    }
    */


    public function onReload( $param )
    {
        try
        {
            $limit = 6;
            
            TTransaction::open('Felabs_DB');
            
            $criteria = new TCriteria;
            //$criteria->add(new TFilter('system_user_id', '=', 5));
            //$criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            $products = AtividadeAluno::getObjects($criteria);
            
            $criteria->resetProperties(); // reset the criteria for record count
            $count = AtividadeAluno::countObjects($criteria);

             //var_dump($products);
             //die;


            $replace_detail = array();
            
            if ($products)
            {
                foreach ($products as $product)
                {
                    $userInfo = new SystemUser($product->system_user_id);
                    $product->system_user_id = $userInfo->name;
                    $replace_detail[] = $product->toArray(); 
                }
            }
            
            TTransaction::close();
            
            // enable products section as repeatable
            $this->html->enableSection('respostas', $replace_detail, TRUE);
            
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    
    public function show()
    {
        $this->onReload( func_get_arg(0) );
        parent::show();
    }
}