<?php

class ApontamentoBimestral extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        //$this->form = new TQuickForm('form_search_FiDataapontamentobimestral');
        //$this->form->class = 'tform'; // change CSS class
        //$this->form = new BootstrapFormWrapper($this->form);
        //$this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('FiDataapontamentobimestral');
        
        
        // keep the form filled during navigation with session data
        //$this->form->setData( TSession::getValue('FiDataapontamentobimestral_filter_data') );
        
        
        // add the search form actions
        //$btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction(_t('New'),  new TAction(array('FiDataapontamentobimestralForm', 'onEdit')), 'bs:plus-sign green');
        $Unidade = $loggedUnit = TSession::getValue('userunitid');

        //echo $Unidade;
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        //$this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_Ano = new TDataGridColumn('Ano', 'Ano', 'center','10%');
        $column_Semestre = new TDataGridColumn('Semestre', 'Semestre', 'center','10%');
        //$column_Bimestre = new TDataGridColumn('avaliacao_bimestre', 'Avaliação', 'center','10%');
        $column_Bimestre_Colegio = new TDataGridColumn('avaliacao_bimestre_colegio', 'Avaliação', 'center','10%');
        $column_DataInicio = new TDataGridColumn('DataInicio', 'De', 'center','30%');
        $column_DataFim = new TDataGridColumn('DataFim', 'Até', 'left','30%');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Ano);
        $this->datagrid->addColumn($column_Semestre);

        $this->datagrid->addColumn($column_Bimestre_Colegio);



        // if ($Unidade == 12)
        // {
        //     $this->datagrid->addColumn($column_Bimestre_Colegio);
        // }
        // else {
        //     $this->datagrid->addColumn($column_Bimestre);
        // }



        
        
        $this->datagrid->addColumn($column_DataInicio);
        $this->datagrid->addColumn($column_DataFim);

        
        // create EDIT action
        //$action_edit = new TDataGridAction(array('FiDataapontamentobimestralForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        //$action_edit->setLabel(_t('Edit'));
        //$action_edit->setImage('far:edit blue fa-lg');
        //$action_edit->setField('Cod_DataApontamentoBimestral');
        //$this->datagrid->addAction($action_edit);
        
        
        // creates the datagrid actions
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(FALSE);
        $action_select->setButtonClass('btn btn-default');
        $action_select->setLabel(AdiantiCoreTranslator::translate('Select'));
        $action_select->setImage('far:check-circle green');
        $action_select->setField('Cod_DataApontamentoBimestral');
        $this->datagrid->addAction($action_select);


        // create the datagrid model
        $this->datagrid->createModel();

        
        // creates the page navigation
        //$this->pageNavigation = new TPageNavigation;
        //$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        //$this->pageNavigation->setWidth($this->datagrid->getWidth());
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Períodos de Apontamentos Bimestral Abertos', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

       
    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('dados_fei'); 
            
            $object = new FiDataapontamentobimestral($key); 
            $object->{$field} = $value;
            $object->store(); 
            
            TTransaction::close(); 
            
            $this->onReload($param); 
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    

    public function onSearch()
    {
        $data = $this->form->getData();
        
        $this->form->setData($data);
        
        TSession::setValue('FiDataapontamentobimestral_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {            
            $Unidade = $loggedUnit = TSession::getValue('userunitid');
            $dataAtual = date('Y-m-d');


            TTransaction::open('dados_fei');


            $repository = new TRepository('FiDataapontamentobimestral');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodEntidade',  '=',  $Unidade), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('DataInicio',  '<=',  ''.$dataAtual.''), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('DataFim',     '>=',  ''.$dataAtual.''), TExpression::AND_OPERATOR);


            if (empty($param['order']))
            {
                $param['order'] = 'Cod_DataApontamentoBimestral';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

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
                   /*
                    $object->Bimestre;

                    switch ($object->Bimestre) {
                        case '1':
                            $object->Bimestre = "1º Bimestre";
                            break;
                        case '2':
                            $object->Bimestre = "2º Bimestre";
                            break;
                        case '3':
                            $object->Bimestre = "Exame";
                            break;
                        default:
                            
                            break;
                    }*/


                    $object->DataInicio = TDate::date2br($object->DataInicio);
                    $object->DataFim = TDate::date2br($object->DataFim);


                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            //$this->pageNavigation->setCount($count); 
            //$this->pageNavigation->setProperties($param); 
            //$this->pageNavigation->setLimit($limit); 
            

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }


    public function onSelect($param)
    {
        $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($this->datagrid->getItems());
        //die();
        
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->Cod_DataApontamentoBimestral)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

                //echo $object->CodGradeDisciplinaEtapaFrente;
                //die();

                TSession::setValue('sessao_bimestre', array('DataInicio' => $object->DataInicio,
                                                        'DataFim'    => $object->DataFim,
                                                        'Bimestre'   => $object->Bimestre,
                                                        'Semestre'   => $object->Semestre,
                                                        'Ano'        => $object->Ano,
                                                        'Entidade'   => $object->CodEntidade
                                                        )
                                   );        
            }
        }     
        

       //var_dump(TSession::getValue('sessao_prof'));
       //die();

        TApplication::loadPage('VwProfessordisciplinassemestreList');        
    }


    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('dados_fei'); 
            
            $object = new FiDataapontamentobimestral($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            
            $this->onReload( $param ); 
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
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
