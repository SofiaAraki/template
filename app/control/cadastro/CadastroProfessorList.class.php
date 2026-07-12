<?php

class CadastroProfessorList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_search_Professor');
        $this->form->setFormTitle('Listagem de Professores');
        
        $nome = new TEntry('Nome');
        $cpf  = new TEntry('CPF');
        $cpf->setMask('999.999.999-99');

        $this->form->addFields([new TLabel('Nome')], [$nome], [new TLabel('CPF')], [$cpf]);
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Novo Professor', new TAction(['CadastroProfessorForm', 'onEdit']), 'fa:plus green');
        
        // DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id    = new TDataGridColumn('Codprofessor', 'Código', 'center', '10%');
        $col_nome  = new TDataGridColumn('Nome', 'Nome', 'left', '40%');
        $col_cpf   = new TDataGridColumn('CPF', 'CPF', 'left', '20%');
        $col_rd    = new TDataGridColumn('RD', 'RD', 'center', '15%');
        $col_fone  = new TDataGridColumn('Telefone1', 'Telefone', 'left', '15%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_cpf);
        $this->datagrid->addColumn($col_rd);
        $this->datagrid->addColumn($col_fone);
        
        // Ações
        $action_edit = new TDataGridAction(['CadastroProfessorForm', 'onEdit'], ['key' => '{Codprofessor}']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:edit blue');
        $this->datagrid->addAction($action_edit);
        
        $action_del = new TDataGridAction([$this, 'onDelete'], ['key' => '{Codprofessor}']);
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
        $repository = new TRepository('FiProfessor');
        $criteria = new TCriteria;
        
        if (!empty($data->Nome)) $criteria->add(new TFilter('Nome', 'like', "%{$data->Nome}%"));
        if (!empty($data->CPF))  $criteria->add(new TFilter('CPF', '=', $data->CPF));
        
        // CORREÇÃO SQL SERVER: Executa o count antes de injetar o ORDER BY no critério
        $this->pageNavigation->setCount($repository->count($criteria));
        
        $criteria->setProperty('limit', 10);
        $criteria->setProperty('order', 'Nome');
        $criteria->setProperty('direction', 'asc');
        
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
        new TQuestion('Deseja realmente excluir este docente?', $action);
    }
    
    public function Delete($param)
    {
        try {
            TTransaction::open('dados_fei');
            $object = new FiProfessor($param['key']);
            $object->delete();
            TTransaction::close();
            $this->onSearch();
            new TMessage('info', 'Registro excluído com sucesso!');
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