<?php

class DataApontamentoBimestralList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_search_DataApontamento');
        $this->form->setFormTitle('Prazos de Lançamentos Bimestrais');
        
        $ano      = new TEntry('Ano'); $ano->setMaxLength(4);
        $semestre = new TCombo('Semestre'); $semestre->addItems(['1' => '1º Semestre', '2' => '2º Semestre']);
        $bimestre = new TCombo('Bimestre'); $bimestre->addItems(['1'=>'1º Bimestre', '2'=>'2º Bimestre', '3'=>'3º Bimestre', '4'=>'4º Bimestre']);

        $this->form->addFields(
            [new TLabel('Ano')], [$ano], 
            [new TLabel('Semestre')], [$semestre], 
            [new TLabel('Bimestre')], [$bimestre]
        );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Novo Prazo', new TAction(['DataApontamentoBimestralForm', 'onEdit']), 'fa:plus green');
        
        // DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id     = new TDataGridColumn('Cod_DataApontamentoBimestral', 'Código', 'center', '5%');
        $col_ent    = new TDataGridColumn('{fi_entidade->NomeFantasia}', 'Entidade / Escola', 'left', '25%');
        $col_ref    = new TDataGridColumn('Ano', 'Ano/Ref', 'center', '15%');
        $col_inicio = new TDataGridColumn('DataInicio', 'Abertura', 'center', '20%');
        $col_fim    = new TDataGridColumn('DataFim', 'Encerramento', 'center', '20%');
        $col_operador = new TDataGridColumn('{fi_operador->Nome}', 'Operador', 'left', '15%');
        
        // Transformadores de exibição para referências combinadas e datas
        $col_ref->setTransformer(function($value, $object, $row) {
            return "{$object->Ano} - {$object->Bimestre}º Bim / {$object->Semestre}º Sem";
        });
        
        $col_inicio->setTransformer(function($value) {
            return !empty($value) ? date('d/m/Y H:i', strtotime($value)) : '';
        });
        
        $col_fim->setTransformer(function($value) {
            return !empty($value) ? date('d/m/Y H:i', strtotime($value)) : '';
        });
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_ent);
        $this->datagrid->addColumn($col_ref);
        $this->datagrid->addColumn($col_inicio);
        $this->datagrid->addColumn($col_fim);
        $this->datagrid->addColumn($col_operador);

        $action_edit = new TDataGridAction(['DataApontamentoBimestralForm', 'onEdit'], ['key' => '{Cod_DataApontamentoBimestral}']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:edit blue');
        $this->datagrid->addAction($action_edit);
        
        $action_del = new TDataGridAction([$this, 'onDelete'], ['key' => '{Cod_DataApontamentoBimestral}']);
        $action_del->setLabel('Excluir');
        $action_del->setImage('fa:trash red');
        $this->datagrid->addAction($action_del);
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onSearch']));
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        TSession::setValue(__CLASS__.'_filter_data', $data);
        
        TTransaction::open('dados_fei');
        $repository = new TRepository('FiDataapontamentobimestral');
        $criteria = new TCriteria;
        
        if (!empty($data->Ano))      $criteria->add(new TFilter('Ano', '=', $data->Ano));
        if (!empty($data->Semestre)) $criteria->add(new TFilter('Semestre', '=', $data->Semestre));
        if (!empty($data->Bimestre)) $criteria->add(new TFilter('Bimestre', '=', $data->Bimestre));
        
        $this->pageNavigation->setCount($repository->count($criteria));
        
        $criteria->setProperty('limit', 10);
        $criteria->setProperty('order', 'Ano DESC, Semestre, Bimestre');
        $criteria->setProperty('direction', 'desc');
        
        $objects = $repository->load($criteria);
        $this->datagrid->clear();
        
        if ($objects) {
            foreach ($objects as $object) {
                $this->datagrid->addItem($object);
            }
        }
        
        TTransaction::close();
    }
    
    public function onDelete($param)
    {
        $action = new TAction([$this, 'Delete']);
        $action->setParameters($param);
        new TQuestion('Deseja realmente remover o cronograma de apontamentos selecionado?', $action);
    }
    
    public function Delete($param)
    {
        try {
            TTransaction::open('dados_fei');
            $object = new FiDataapontamentobimestral($param['key']);
            $object->delete();
            TTransaction::close();
            $this->onSearch();
            new TMessage('info', 'Cronograma removido com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function show()
    {
        if (!$this->loaded) {
            $this->onSearch();
        }
        parent::show();
    }
}