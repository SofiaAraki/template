<?php

class CadastroAlunoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        // Cria o formulário de buscas
        $this->form = new BootstrapFormBuilder('form_search_Aluno');
        $this->form->setFormTitle('Listagem de Alunos');
        
        $ra   = new TEntry('Ra');
        $nome = new TEntry('Nome');
        $cpf  = new TEntry('CPF');
        $cpf->setMask('999.999.999-99');

        $this->form->addFields([new TLabel('RA')], [$ra], [new TLabel('Nome')], [$nome], [new TLabel('CPF')], [$cpf]);
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Novo Aluno', new TAction(['CadastroAlunoForm', 'onEdit']), 'fa:plus green');
        
        // Cria a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id    = new TDataGridColumn('Codaluno', 'Código', 'center', '10%');
        $col_ra    = new TDataGridColumn('Ra', 'RA', 'left', '15%');
        $col_nome  = new TDataGridColumn('Nome', 'Nome', 'left', '40%');
        $col_cpf   = new TDataGridColumn('CPF', 'CPF', 'left', '20%');
        $col_fone  = new TDataGridColumn('Telefone', 'Telefone', 'left', '15%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_ra);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_cpf);
        $this->datagrid->addColumn($col_fone);
        
        // Ações da Datagrid
        $action_edit = new TDataGridAction(['CadastroAlunoForm', 'onEdit'], ['key' => '{Codaluno}']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:edit blue');
        $this->datagrid->addAction($action_edit);
        
        $action_del = new TDataGridAction([$this, 'onDelete'], ['key' => '{Codaluno}']);
        $action_del->setLabel('Excluir');
        $action_del->setImage('fa:trash red');
        $this->datagrid->addAction($action_del);
        
        $this->datagrid->createModel();
        
        // Paginação
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
        // 1. Obtém os dados do formulário
        $data = $this->form->getData();
        
        // 2. Se o usuário clicou em "Buscar" (o formulário enviou dados novos), guardamos na sessão.
        // Caso contrário (veio por paginação ou refresh), recuperamos o que já estava salvo.
        if (isset($param['method']) AND $param['method'] == 'onSearch') {
            TSession::setValue(__CLASS__.'_filter_data', $data);
        } else {
            $data = TSession::getValue(__CLASS__.'_filter_data');
            // Devolve os dados salvos de volta para o formulário para mantê-lo preenchido na tela
            $this->form->setData($data);
        }
        
        TTransaction::open('dados_fei');
        $repository = new TRepository('FiAluno');
        $criteria = new TCriteria;
        
        // Se houver limite/offset na paginação, passamos ao critério
        if (isset($param['offset'])) {
            $criteria->setProperty('offset', $param['offset']);
        }
        
        // 3. Aplica os filtros baseando-se no objeto de dados ($data)
        if (isset($data->Ra) AND ($data->Ra != '')) {
            $criteria->add(new TFilter('Ra', 'like', "%{$data->Ra}%"));
        }
        if (isset($data->Nome) AND ($data->Nome != '')) {
            $criteria->add(new TFilter('Nome', 'like', "%{$data->Nome}%"));
        }
        if (isset($data->CPF) AND ($data->CPF != '')) {
            $criteria->add(new TFilter('CPF', '=', $data->CPF));
        }

        // Define a contagem de registros para alimentar a paginação
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
        $this->loaded = true;
    }
    
    public function onDelete($param)
    {
        $action = new TAction([$this, 'Delete']);
        $action->setParameters($param);
        new TQuestion('Deseja realmente excluir este registro?', $action);
    }
    
    public function Delete($param)
    {
        try {
            TTransaction::open('dados_fei');
            $object = new FiAluno($param['key']);
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