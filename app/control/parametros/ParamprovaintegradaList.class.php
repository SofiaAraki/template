<?php

class ParamprovaintegradaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_search_ParamProvaIntegrada');
        $this->form->setFormTitle('Parâmetros de Prova Integrada');
        
        $turma = new TDBCombo('CodTurmaetapa', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $this->form->addFields([new TLabel('Filtrar por Turma')], [$turma]);
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Novo Parâmetro', new TAction(['ParamprovaintegradaForm', 'onEdit']), 'fa:plus green');
        
        // DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id      = new TDataGridColumn('CodParamProvaIntegrada', 'Código', 'center', '10%');
        $col_turma   = new TDataGridColumn('turmaEtapa->Identificacao', 'Turma / Etapa', 'left', '35%');
        $col_quest   = new TDataGridColumn('TotalQuestoes', 'Total Questões', 'center', '15%');
        $col_modelo  = new TDataGridColumn('Modelo', 'Modelo', 'center', '15%');
        $col_data    = new TDataGridColumn('DataProva', 'Data da Prova', 'center', '25%');
        
        $col_data->setTransformer(function($value) {
            return !empty($value) ? date('d/m/Y', strtotime($value)) : '';
        });
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_turma);
        $this->datagrid->addColumn($col_quest);
        $this->datagrid->addColumn($col_modelo);
        $this->datagrid->addColumn($col_data);
        
        $action_edit = new TDataGridAction(['ParamprovaintegradaForm', 'onEdit'], ['key' => '{CodParamProvaIntegrada}']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:edit blue');
        $this->datagrid->addAction($action_edit);
        
        $action_del = new TDataGridAction([$this, 'onDelete'], ['key' => '{CodParamProvaIntegrada}']);
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
        $repository = new TRepository('FiParamprovaintegrada');
        $criteria = new TCriteria;
        
        if (!empty($data->CodTurmaetapa)) {
            $criteria->add(new TFilter('CodTurmaetapa', '=', $data->CodTurmaetapa));
        }
        
        $this->pageNavigation->setCount($repository->count($criteria));
        
        $criteria->setProperty('limit', 10);
        $criteria->setProperty('order', 'CodParamProvaIntegrada');
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
        new TQuestion('Deseja realmente excluir este parâmetro de avaliação?', $action);
    }
    
    public function Delete($param)
    {
        try {
            TTransaction::open('dados_fei');
            $object = new FiParamprovaintegrada($param['key']);
            $object->delete();
            TTransaction::close();
            $this->onSearch();
            new TMessage('info', 'Parâmetro removido com sucesso!');
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