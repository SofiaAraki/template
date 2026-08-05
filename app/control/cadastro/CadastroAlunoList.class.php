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
        
        $id   = new TEntry('Codaluno'); // fazer uma validação para não permitir letras
        $id->setMask('99999');
        $nome = new TEntry('Nome');

        $this->form->addFields([new TLabel('Cod.Aluno')], [$id], [new TLabel('Nome')], [$nome]);
        
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Novo Aluno', new TAction(['CadastroAlunoForm', 'onEdit']), 'fa:plus green');
        
        // Cria a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id   = new TDataGridColumn('Codaluno', 'Código', 'center', '10%');
        $col_nome = new TDataGridColumn('Nome', 'Nome', 'left', '60%');
        $col_operador = new TDataGridColumn('{Operador->Nome}', 'Operador', 'left', '30%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_operador);
        
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
        try {
            // 1. Obtém os dados do formulário ou da sessão
            $data = $this->form->getData();
            
            if (isset($param['method']) && $param['method'] == 'onSearch') {
                TSession::setValue(__CLASS__.'_filter_data', $data);
            } else {
                $data = TSession::getValue(__CLASS__.'_filter_data');
                $this->form->setData($data);
            }
            
            TTransaction::open('dados_fei');
            $repository = new TRepository('FiAluno');
            $criteria = new TCriteria;
            
            // 2. Aplica os filtros
            if (!empty($data->Codaluno)) {
                $criteria->add(new TFilter('Codaluno', '=', $data->Codaluno));
            }
            if (!empty($data->Nome)) {
                $criteria->add(new TFilter('Nome', 'like', "%{$data->Nome}%"));
            }

            // 3. Contagem para paginação (feita antes do offset/limit)
            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count);
            
            // 4. Configuração da consulta paginada
            $limit = 10;
            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('order', 'Codaluno');
            $criteria->setProperty('direction', 'desc');
            
            if (isset($param['offset'])) {
                $criteria->setProperty('offset', $param['offset']);
            }

            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            // 5. Carrega os objetos
            $objects = $repository->load($criteria);
            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }
            
            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
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
            TTransaction::rollback();
        }
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open('teste');
            
            $repository = new TRepository('FiAluno');
            $limit = 10;
            
            $criteria = new TCriteria;
            
            $criteria->setProperties($param); // Aplica direction, offset e limit
            $criteria->setProperty('limit', $limit);

            // Carrega os filtros mantidos em sessão
            if (TSession::getValue(__CLASS__.'_filter_Codaluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Codaluno'));
            }
            if (TSession::getValue(__CLASS__.'_filter_Nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Nome'));
            }

            // 1. Contagem total de registros filtrados (antes do limit)
            $count = $repository->count($criteria);
            
            // 2. Carrega os objetos
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }
            
            // 3. Configura o componente de paginação
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            
            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear()
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filter_Codaluno', NULL);
        TSession::setValue(__CLASS__.'_filter_Nome', NULL);
        
        $this->form->clear();
        $this->onReload();
    }
    
    public function show()
    {
        if (!$this->loaded) {
            $this->onSearch();
        }
        parent::show();
    }
}