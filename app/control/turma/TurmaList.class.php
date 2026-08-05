<?php

class TurmaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_search_Turma');
        $this->form->setFormTitle('Listagem de Turmas / Etapas');
        
        $ano           = new TEntry('Ano'); $ano->setMaxLength(4);
        $semestre      = new TCombo('Semestre'); $semestre->addItems(['1' => '1º Semestre', '2' => '2º Semestre']);
        $identificacao = new TEntry('Identificacao');

        $this->form->addFields(
            [new TLabel('Ano')], [$ano], 
            [new TLabel('Semestre')], [$semestre], 
            [new TLabel('Turma (Identificação)')], [$identificacao]
        );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Nova Turma', new TAction(['TurmaForm', 'onEdit']), 'fa:plus green');
        
        // Configuração da DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id     = new TDataGridColumn('CodTurmaetapa', 'Código', 'center', '10%');
        $col_ident  = new TDataGridColumn('Identificacao', 'Turma', 'left', '20%');
        $col_ano    = new TDataGridColumn('Ano', 'Ano', 'center', '10%');
        $col_sem    = new TDataGridColumn('Semestre', 'Semestre', 'center', '10%');
        $col_campus = new TDataGridColumn('Campus', 'Campus', 'left', '20%');
        
        // CORREÇÃO: No Adianti, métodos mágicos de AR como get_operador() não devem ser chamados direto no construtor da coluna. 
        // Usamos uma transformer setTransformer() ou chamamos o método mágico se o Active Record já possuir o get_operador()[cite: 2, 6]
        $col_prof   = new TDataGridColumn('operador->Nome', 'Responsável', 'left', '30%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_ident);
        $this->datagrid->addColumn($col_ano);
        $this->datagrid->addColumn($col_sem);
        $this->datagrid->addColumn($col_campus);
        $this->datagrid->addColumn($col_prof);
        
        // Ações da Linha
        $action_edit = new TDataGridAction(['TurmaForm', 'onEdit'], ['key' => '{CodTurmaetapa}']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:edit blue');
        $this->datagrid->addAction($action_edit);
        
        $action_del = new TDataGridAction([$this, 'onDelete'], ['key' => '{CodTurmaetapa}']);
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
        // 1. Obtém os dados passados pelo formulário ou recupera os antigos salvos em sessão
        $data = $this->form->getData();
        
        if (isset($param['method']) AND $param['method'] == 'onSearch') {
            TSession::setValue(__CLASS__.'_filter_data', $data);
        } else {
            $data = TSession::getValue(__CLASS__.'_filter_data');
            $this->form->setData($data);
        }
        
        TTransaction::open('dados_fei');
        $repository = new TRepository('FiTurmaEtapa');
        $criteria = new TCriteria;
        
        // Configura as propriedades básicas do limite e offset de paginação
        $limit = 10;
        $criteria->setProperty('limit', $limit);
        $criteria->setProperty('offset', isset($param['offset']) ? $param['offset'] : 0);
        $criteria->setProperty('order', 'Ano DESC, Identificacao');
        $criteria->setProperty('direction', 'asc');
        
        // 2. Monta os filtros de busca com base nos campos preenchidos
        if (!empty($data->Ano)) {
            $criteria->add(new TFilter('Ano', '=', $data->Ano));
        }
        if (!empty($data->Semestre)) {
            $criteria->add(new TFilter('Semestre', '=', $data->Semestre));
        }
        if (!empty($data->Identificacao)) {
            $criteria->add(new TFilter('Identificacao', 'like', "%{$data->Identificacao}%"));
        }
        
        // 3. Executa a listagem e repassa os parâmetros para a paginação calcular os blocos
        $objects = $repository->load($criteria);
        $this->datagrid->clear();
        
        if ($objects) {
            foreach ($objects as $object) {
                $this->datagrid->addItem($object);
            }
        }
        
        // Reinicia o critério limpando apenas as propriedades de limite para realizar o count total
        $criteria->resetProperties();
        $count = $repository->count($criteria);
        
        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setLimit($limit);
        $this->pageNavigation->setPage(isset($param['page']) ? $param['page'] : 1);
        
        TTransaction::close();
        $this->loaded = true;
    }
    
    public function onDelete($param)
    {
        $action = new TAction([$this, 'Delete']);
        $action->setParameters($param);
        new TQuestion('Deseja realmente excluir esta turma? Isso pode afetar registros vinculados.', $action);
    }
    
    public function Delete($param)
    {
        try {
            TTransaction::open('dados_fei');
            $object = new FiTurmaEtapa($param['key']);
            $object->delete();
            TTransaction::close();
            $this->onSearch();
            new TMessage('info', 'Turma excluída com sucesso!');
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