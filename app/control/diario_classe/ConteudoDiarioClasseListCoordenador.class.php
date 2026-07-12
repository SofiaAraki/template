<?php
/**
 * ConteudoDiarioClasseListCoordenador
 * Controle e Monitoramento de Lançamentos de Diário de Classe (Visão Coordenador)
 * Base de Dados: Felabs_DB (Tabela) / dados_fei (Views)
 */
class ConteudoDiarioClasseListCoordenador extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        // Formulário de pesquisa para o Coordenador
        $this->form = new TQuickForm('form_search_DiarioClasseCoordenador');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table; width:100%'; 
        $this->form->setFormTitle('Monitoramento - Diário de Classe (Coordenação)');

        // Filtros estruturados para o acompanhamento da gestão
        $professor  = new TEntry('nome_professor');
        $disciplina = new TEntry('nome_disciplina');
        $data_aula  = new TDate('data_aula');

        $this->form->addQuickField('Professor(a)', $professor, '100%');
        $this->form->addQuickField('Disciplina', $disciplina, '100%');
        $this->form->addQuickField('Data da Aula', $data_aula, '100%');
        
        $data_aula->setMask('dd/mm/yyyy');
        
        // Mantém os filtros ativos na sessão do usuário
        $this->form->setData(TSession::getValue('DiarioCoordenador_filter_data'));
        
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fas:search blue');
        
        // Criação da Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        // Colunas focadas na auditoria do coordenador
        $column_id          = new TDataGridColumn('id', 'ID', 'right');
        $column_professor   = new TDataGridColumn('nome_professor', 'Professor(a)', 'left');
        $column_disciplina  = new TDataGridColumn('nome_disciplina', 'Disciplina', 'left');
        $column_cod_turma   = new TDataGridColumn('cod_turma_etapa', 'Cód. Turma Etapa', 'center');
        $column_data        = new TDataGridColumn('data_aula', 'Data da Aula', 'center');
        $column_conteudo    = new TDataGridColumn('conteudo', 'Conteúdo Lançado', 'left');

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_professor);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_cod_turma);
        $this->datagrid->addColumn($column_data);
        $this->datagrid->addColumn($column_conteudo);

        $this->datagrid->createModel();
        
        // Paginação da listagem
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // Layout final da página
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TPanelGroup::pack('Filtros de Monitoramento', $this->form));
        $container->add(TPanelGroup::pack('Aulas Lançadas no Diário', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        
        // Limpa estados antigos de busca
        TSession::setValue('DiarioCoordenador_filter_professor', NULL);
        TSession::setValue('DiarioCoordenador_filter_disciplina', NULL);
        TSession::setValue('DiarioCoordenador_filter_data_aula', NULL);

        if (isset($data->nome_professor) AND ($data->nome_professor)) {
            TSession::setValue('DiarioCoordenador_filter_professor', new TFilter('nome_professor', 'like', "%{$data->nome_professor}%"));
        }
        if (isset($data->nome_disciplina) AND ($data->nome_disciplina)) {
            TSession::setValue('DiarioCoordenador_filter_disciplina', new TFilter('nome_disciplina', 'like', "%{$data->nome_disciplina}%"));
        }
        if (isset($data->data_aula) AND ($data->data_aula)) {
            // Como sua tabela salva VARCHAR, filtramos diretamente pelo padrão de texto digitado ou enviado
            TSession::setValue('DiarioCoordenador_filter_data_aula', new TFilter('data_aula', '=', $data->data_aula)); 
        }
       
        $this->form->setData($data);
        TSession::setValue('DiarioCoordenador_filter_data', $data);
        
        $param = array();
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            $anoAtual = date('Y');
            $loggedUnit = TSession::getValue('userunitid');
            $nomeCoordenador = TSession::getValue('username'); 

            $turmasPermitidas = [];

            // -------------------------------------------------------------------------
            // PASSO 1: Buscar na base 'dados_fei' o escopo de turmas do coordenador
            // -------------------------------------------------------------------------
            TTransaction::open('dados_fei');
            
            $criteriaCoord = new TCriteria;
            $criteriaCoord->add(new TFilter('NomeCoordenador', '=', $nomeCoordenador));
            $criteriaCoord->add(new TFilter('Ano', '=', $anoAtual));
            $criteriaCoord->add(new TFilter('CodEntidade', '=', $loggedUnit));
            
            $turmasCoord = VwCoordenadorturmaetapa::getObjects($criteriaCoord);
            
            if (!empty($turmasCoord)) {
                foreach ($turmasCoord as $tc) {
                    if (!empty($tc->CodTurmaetapa)) {
                        $turmasPermitidas[] = $tc->CodTurmaetapa;
                    }
                }
                $turmasPermitidas = array_unique($turmasPermitidas);
            }
            TTransaction::close(); 

            // Bloqueio de segurança: Se o coordenador não possuir turmas este ano, para e limpa a tela
            if (empty($turmasPermitidas)) {
                $this->datagrid->clear();
                $this->pageNavigation->setCount(0);
                return;
            }

            // -------------------------------------------------------------------------
            // PASSO 2: Consultar os diários de classe lançados na base Felabs_DB
            // -------------------------------------------------------------------------
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('ConteudoDiarioClasse');
            $limit = 20;
            $criteria = new TCriteria;

            // Ordenação padrão idêntica à regra original por ID decrescente (mais recentes primeiro)
            if (empty($param['order'])) {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);

            // Restrição de Escopo: Traz somente as turmas validadas no PASSO 1
            $criteria->add(new TFilter('cod_turma_etapa', 'IN', $turmasPermitidas));

            // Filtro Temporal usando o campo nativo cod_curso ou via texto se necessário.
            // Se o campo data_aula é VARCHAR(50) e guarda no formato brasileiro 'dd/mm/yyyy',
            // filtramos o final da string correspondente ao ano atual:
            $criteria->add(new TFilter('data_aula', 'like', "%/{$anoAtual}"));

            // Aplica filtros de busca dinâmicos do formulário
            if (TSession::getValue('DiarioCoordenador_filter_professor')) {
                $criteria->add(TSession::getValue('DiarioCoordenador_filter_professor')); 
            }
            if (TSession::getValue('DiarioCoordenador_filter_disciplina')) {
                $criteria->add(TSession::getValue('DiarioCoordenador_filter_disciplina'));
            }
            if (TSession::getValue('DiarioCoordenador_filter_data_aula')) {
                $criteria->add(TSession::getValue('DiarioCoordenador_filter_data_aula'));
            }

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects) {
                foreach ($objects as $object) {
                    // Como data_aula já é VARCHAR(50), jogamos direto na grid sem converter formato
                    $this->datagrid->addItem($object);
                }
            }
            
            // Recalcula o contador total do repositório respeitando os filtros ativos
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            try { TTransaction::rollback(); } catch (Exception $ex) {}
        }
    }    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload')) {
            $this->onReload(func_num_args() > 0 ? func_get_arg(0) : NULL);
        }
        parent::show();
    }
}