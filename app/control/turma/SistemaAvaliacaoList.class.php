<?php

class SistemaAvaliacaoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        // Formulário de Busca
        $this->form = new BootstrapFormBuilder('form_search_SistemaAvaliacao');
        $this->form->setFormTitle('Sistemas de Avaliação');
        
        $descricao = new TEntry('Descricao');
        $this->form->addFields([new TLabel('Descrição')], [$descricao]);
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Novo Sistema', new TAction(['SistemaAvaliacaoForm', 'onEdit']), 'fa:plus green');
        
        // Configuração da DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id        = new TDataGridColumn('CodSistemaAvaliacao', 'Código', 'center', '10%');
        $col_desc      = new TDataGridColumn('Descricao', 'Descrição', 'left', '35%');
        $col_media_pr  = new TDataGridColumn('PromocaoMedia', 'Média Promoção', 'center', '15%');
        $col_freq_pr   = new TDataGridColumn('PromocaoFreqMinima', 'Freq. Mínima (%)', 'center', '15%');
        $col_tipo_nota = new TDataGridColumn('TipoNota', 'Tipo de Nota', 'center', '10%');
        $col_rec       = new TDataGridColumn('Recuperacao', 'Possui Rec.?', 'center', '15%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_desc);
        $this->datagrid->addColumn($col_media_pr);
        $this->datagrid->addColumn($col_freq_pr);
        $this->datagrid->addColumn($col_tipo_nota);
        $this->datagrid->addColumn($col_rec);
        
        // Ações da Linha
        $action_edit = new TDataGridAction(['SistemaAvaliacaoForm', 'onEdit'], ['key' => '{CodSistemaAvaliacao}']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:edit blue');
        $this->datagrid->addAction($action_edit);
        
        $action_del = new TDataGridAction([$this, 'onDelete'], ['key' => '{CodSistemaAvaliacao}']);
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
        $data = $this->form->getData();
        TSession::setValue(__CLASS__.'_filter_data', $data);
        
        TTransaction::open('dados_fei');
        $repository = new TRepository('FiSistemaAvaliacao');
        $criteria = new TCriteria;
        
        if (!empty($data->Descricao)) {
            $criteria->add(new TFilter('Descricao', 'like', "%{$data->Descricao}%"));
        }
        
        $this->pageNavigation->setCount($repository->count($criteria));
        
        $criteria->setProperty('limit', 10);
        $criteria->setProperty('order', 'Descricao');
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
        new TQuestion('Deseja realmente remover esta regra de avaliação?', $action);
    }
    
    public function Delete($param)
    {
        try {
            TTransaction::open('dados_fei');
            $object = new FiSistemaAvaliacao($param['key']);
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