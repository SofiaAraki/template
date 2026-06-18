<?php
class ApontamentoBimestral extends TPage
{
    private $datagrid; 
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        // creates the datagrid columns - AJUSTADO: Alinhamento centralizado para todas as colunas
        $column_Ano = new TDataGridColumn('Ano', 'Ano', 'center', '10%');
        $column_Semestre = new TDataGridColumn('Semestre', 'Semestre', 'center', '10%');
        $column_Bimestre_Colegio = new TDataGridColumn('avaliacao_bimestre_colegio', 'Avaliação', 'center', '20%');
        $column_DataInicio = new TDataGridColumn('DataInicio', 'De', 'center', '30%');
        $column_DataFim = new TDataGridColumn('DataFim', 'Até', 'center', '30%');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Ano);
        $this->datagrid->addColumn($column_Semestre);
        $this->datagrid->addColumn($column_Bimestre_Colegio);
        $this->datagrid->addColumn($column_DataInicio);
        $this->datagrid->addColumn($column_DataFim);
        
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

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        
        // AJUSTADO: Removido o empacotamento do $this->form inexistente e simplificado o painel da grid
        $container->add(TPanelGroup::pack('Períodos de Apontamentos Bimestral Abertos', $this->datagrid));
        
        parent::add($container);
    }
    
    public function onReload($param = NULL)
    {
        try
        {            
            $Unidade = TSession::getValue('userunitid');
            $dataAtual = date('Y-m-d');

            TTransaction::open('dados_fei');

            $repository = new TRepository('FiDataapontamentobimestral');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodEntidade', '=', $Unidade), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('DataInicio', '<=', $dataAtual), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('DataFim', '>=', $dataAtual), TExpression::AND_OPERATOR);

            if (empty($param['order']))
            {
                $param['order'] = 'Cod_DataApontamentoBimestral';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->DataInicio = TDate::date2br($object->DataInicio);
                    $object->DataFim = TDate::date2br($object->DataFim);
                    
                    // Fallback de segurança para popular a propriedade se "Bimestre" for lido nulo no onSelect
                    if (empty($object->Bimestre) && !empty($object->avaliacao_bimestre_colegio)) {
                        $object->Bimestre = $object->avaliacao_bimestre_colegio;
                    }
                    
                    $this->datagrid->addItem($object);
                }
            }
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSelect($param)
    {
        $key = $param['key'];
       
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->Cod_DataApontamentoBimestral)
            {
                TSession::setValue('sessao_bimestre', array(
                    'DataInicio' => $object->DataInicio,
                    'DataFim'    => $object->DataFim,
                    'Bimestre'   => !empty($object->Bimestre) ? $object->Bimestre : $object->avaliacao_bimestre_colegio,
                    'Semestre'   => $object->Semestre,
                    'Ano'        => $object->Ano,
                    'Entidade'   => $object->CodEntidade
                ));        
            }
        }     
        TApplication::loadPage('VwProfessordisciplinassemestreList');        
    }

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'], array('onReload', 'onSearch')))) )
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