<?php

class MatriculaAlunoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded; // Declarado para evitar alertas de propriedades dinâmicas nas versões recentes do PHP
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_search_Matricula');
        $this->form->setFormTitle('Gestão de Matrículas por Etapa');
        
        $cod_aluno = new TDBUniqueSearch('Codaluno', 'dados_fei', 'FiAluno', 'Codaluno', 'Nome');
        $cod_aluno->setMask('{Nome} ({Codaluno})');
        
        $cod_turma = new TDBCombo('CodTurmaetapa', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $situacao  = new TCombo('Situacao');
        $situacao->addItems(['MA'=>'Matriculado', 'TR'=>'Trancado', 'CA'=>'Cancelado', 'TE'=>'Transferido Ext.']);

        $this->form->addFields(
            [new TLabel('Aluno')], [$cod_aluno], 
            [new TLabel('Turma / Etapa')], [$cod_turma], 
            [new TLabel('Situação')], [$situacao]
        );
        
        $this->form->addAction('Filtrar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction('Nova Matrícula', new TAction(['MatriculaAlunoForm', 'onEdit']), 'fa:plus green');
        
        // DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $col_id    = new TDataGridColumn('CodMatriculaEtapa', 'ID', 'center', '8%');
        $col_aluno = new TDataGridColumn('{fi_aluno->Nome}', 'Estudante', 'left', '32%');
        $col_turma = new TDataGridColumn('{turma->Identificacao}', 'Turma', 'center', '15%');
        $col_data  = new TDataGridColumn('DataMatricula', 'Data Matrícula', 'center', '15%');
        $col_sit   = new TDataGridColumn('Situacao', 'Situação', 'center', '15%');
        $col_res   = new TDataGridColumn('{operador->Nome}', 'Responsável', 'center', '15%');
        
        // Formata data do grid
        $col_data->setTransformer(function($value){
            return !empty($value) ? date('d/m/Y', strtotime($value)) : '';
        });
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_aluno);
        $this->datagrid->addColumn($col_turma);
        $this->datagrid->addColumn($col_data);
        $this->datagrid->addColumn($col_sit);
        $this->datagrid->addColumn($col_res);
        
        $action_edit = new TDataGridAction(['MatriculaAlunoForm', 'onEdit'], ['key' => '{CodMatriculaEtapa}']);
        $action_edit->setLabel('Editar Matrícula');
        $action_edit->setImage('fa:edit blue');
        $this->datagrid->addAction($action_edit);
        
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
        // 1. Obtém os dados do formulário
        $data = $this->form->getData();
        
        // 2. Controla o estado dos filtros (Sessão vs Formulário)
        if (isset($param['method']) AND $param['method'] == 'onSearch') {
            TSession::setValue(__CLASS__.'_filter_data', $data);
        } else {
            $data = TSession::getValue(__CLASS__.'_filter_data');
            // Mantém os campos (incluindo o TDBUniqueSearch e TDBCombo) preenchidos
            $this->form->setData($data);
        }
        
        TTransaction::open('dados_fei');
        $repository = new TRepository('FiMatriculaEtapa');
        $criteria = new TCriteria;
        
        // Aplica o offset para paginação correta
        if (isset($param['offset'])) {
            $criteria->setProperty('offset', $param['offset']);
        }
        
        // 3. Aplica os filtros ativos de forma segura
        if (isset($data->Codaluno) AND ($data->Codaluno != '')) {
            $criteria->add(new TFilter('Codaluno', '=', $data->Codaluno));
        }
        if (isset($data->CodTurmaetapa) AND ($data->CodTurmaetapa != '')) {
            $criteria->add(new TFilter('CodTurmaetapa', '=', $data->CodTurmaetapa));
        }
        if (isset($data->Situacao) AND ($data->Situacao != '')) {
            $criteria->add(new TFilter('Situacao', '=', $data->Situacao));
        }
        
        // Conta os registros encontrados para alimentar a paginação antes do limite/ordenação
        $this->pageNavigation->setCount($repository->count($criteria));
        
        $criteria->setProperty('limit', 10);
        $criteria->setProperty('order', 'CodMatriculaEtapa');
        $criteria->setProperty('direction', 'desc');
        
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
    
    public function show()
    {
        if (!$this->loaded) {
            $this->onSearch();
        }
        parent::show();
    }
}