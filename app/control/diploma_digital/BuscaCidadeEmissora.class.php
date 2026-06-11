<?php

class BuscaCidadeEmissora extends TWindow
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    

    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Buscar cidade');
        parent::setSize(0.6, 0.95);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_BuscaCidadeEmissora');
        

        // create the form fields
        $Nome = new TEntry('Nome');
        $Uf = new TEntry('Uf');


        // add the fields
        $this->form->addFields( [ new TLabel('Nome do município') ], [ $Nome ] );
        $this->form->addFields( [ new TLabel('UF') ], [ $Uf ] );


        // set sizes
        $Nome->setSize('80%');
        $Uf->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('FiCidade_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_CODCIDADE_INEP = new TDataGridColumn('CODCIDADE_INEP', 'Código IBGE', 'center');
        $column_Nome = new TDataGridColumn('Nome', 'Nome do município', 'left');
        $column_Uf = new TDataGridColumn('Uf', 'UF', 'center');
        $column_CEPINICIAL = new TDataGridColumn('fi_cidades_inep->CEPINICIAL', 'CEP', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_CODCIDADE_INEP);
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_Uf);
        $this->datagrid->addColumn( $column_CEPINICIAL);
        
        
        // create SELECT action
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        //$action_select->setUseButton(TRUE);
        //$action_select->setButtonClass('nopadding');
        $action_select->setLabel('Selecionar');
        $action_select->setImage('far: fa-hand-pointer green');
        $action_select->setField('CodCidade');
        $this->datagrid->addAction($action_select);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%;margin-bottom:0;border-radius:0';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('BuscaCidade_filter_Nome', NULL);
        TSession::setValue('BuscaCidade_filter_Uf', NULL);


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%");
            TSession::setValue('BuscaCidade_filter_Nome', $filter);
        }


        if (isset($data->Uf) AND ($data->Uf)) {
            $filter = new TFilter('Uf', 'like', "%{$data->Uf}%");
            TSession::setValue('BuscaCidade_filter_Uf', $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue('FiCidade_filter_data', $data);
        
        $param = array();
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $repository = new TRepository('FiCidade');
            $limit = 10;

            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'Nome';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('BuscaCidade_filter_Nome')) {
                $criteria->add(TSession::getValue('BuscaCidade_filter_Nome'));
            }


            if (TSession::getValue('BuscaCidade_filter_Uf')) {
                $criteria->add(TSession::getValue('BuscaCidade_filter_Uf'));
            }

            
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            
            if ($objects)
            {            
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public static function onSelect($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('dados_fei');
            
            $object = FiCidade::find($key);

            $obj = new FiCidadesInep($object->CODCIDADE_INEP);
            
            TTransaction::close();
            
            $send = new StdClass;
            $send->codigo_municipio = $object->CODCIDADE_INEP;
            $send->nome_municipio = $object->Nome;
            $send->uf = $object->Uf;
            $send->cep = $obj->CEPINICIAL;
            TForm::sendData('form_DiplomaDigitalEmissora', $send);
            
            parent::closeWindow();
        }
        catch (Exception $e)
        {
            $send = new StdClass;
            $send->codigo_municipio = '';
            $send->nome_municipio = '';
            $send->uf = '';
            $send->cep = '';
            TForm::sendData('form_DiplomaDigitalEmissora', $send);
            
            TTransaction::rollback();
        }
    }
    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        
        parent::show();
    }
}


