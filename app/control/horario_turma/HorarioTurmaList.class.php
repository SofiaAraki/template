<?php
class HorarioTurmaList extends TPage
{
    protected $form;     
    protected $datagrid; 
    protected $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('dados_fei');                           
        $this->setActiveRecord('FiHorario');                      
        $this->addFilterField('CodTurmaetapa', '=', 'CodTurmaetapa'); 
        $this->setDefaultOrder('Codhorario', 'desc');              
        
        $this->form = new BootstrapFormBuilder('form_search_HorarioTurma');
        $this->form->setFormTitle('Painel de Controle de Horários');
        
        $cod_turma_etapa = new TDBCombo('CodTurmaetapa', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        
        $this->form->addFields( [new TLabel('Filtrar por Turma:')], [$cod_turma_etapa] );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Limpar Filtros',  new TAction([$this, 'clear']), 'fa:eraser red');
        $this->form->addActionLink('Novo Horário',  new TAction(['HorarioTurmaForm', 'onGerarMatriz']), 'fa:calendar-plus green');
        
        $this->form->setData( TSession::getValue('HorarioTurmaList_filter_data') );
        
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = "100%";
        
        $col_id        = new TDataGridColumn('Codhorario', 'Cód.', 'center', '5%');
        $col_turma     = new TDataGridColumn('{turma_etapa->Identificacao}', 'Turma / Etapa', 'left', '30%');
        $col_curso     = new TDataGridColumn('{turma_etapa->Identificacao}', 'Curso', 'left', '30%');
        $col_inicio    = new TDataGridColumn('InicioAula', 'Início', 'center', '10%');
        $col_max_aulas = new TDataGridColumn('QtdeMaximaAulasPorDia', 'Aulas', 'center', '5%');
        $col_operador  = new TDataGridColumn('{Operador->Nome}', 'Operador', 'center', '20%');
        
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_turma);
        $this->datagrid->addColumn($col_curso);
        $this->datagrid->addColumn($col_inicio);
        $this->datagrid->addColumn($col_max_aulas);
        $this->datagrid->addColumn($col_operador);
        
        // Ações de Linha
        $action_edit   = new TDataGridAction(['HorarioTurmaForm', 'onEdit'], ['key' => '{Codhorario}']);
        $action_delete = new TDataGridAction([$this, 'onDelete'], ['key' => '{Codhorario}']);
        $action_print  = new TDataGridAction(['HorarioTurmaReport', 'onGenerate'], ['key' => '{Codhorario}']);
        
        $this->datagrid->addAction($action_edit, 'Editar Horário', 'far:edit blue');
        $this->datagrid->addAction($action_print, 'Imprimir Horário Oficial (PDF)', 'fa:print purple');
        $this->datagrid->addAction($action_delete, 'Excluir Horário', 'far:trash-alt red');
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($vbox);
    }
    
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}