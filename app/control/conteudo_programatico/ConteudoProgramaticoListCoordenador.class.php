<?php
class ConteudoProgramaticoListCoordenador extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        // cria o formulário de busca
        $this->form = new TQuickForm('form_search_ConteudoProgramatico');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table; width:100%'; 
        $this->form->setFormTitle('Conteúdo Programático - Coordenação');

        // cria os campos do formulário relevantes para o coordenador
        $curso = new TEntry('curso');
        $disciplina = new TEntry('disciplina');
        $etapa = new TEntry('etapa');
        $turma = new TEntry('turma');

        // adiciona os campos que fazem sentido para o coordenador filtrar
        $this->form->addQuickField('Curso', $curso, '100%');
        $this->form->addQuickField('Disciplina', $disciplina, '100%');
        $this->form->addQuickField('Etapa', $etapa, '100%');
        $this->form->addQuickField('Turma', $turma, '100%');
        
        // mantém o formulário preenchido durante a navegação
        $this->form->setData( TSession::getValue('ConteudoProgramatico_filter_data') );
        
        // ação de busca
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fas:search blue');
        
        // cria a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        // colunas da datagrid
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Lançado por', 'left');

        // adiciona as colunas na DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_reg);
        //$this->datagrid->addColumn($column_system_user_id);
        
        // ação de visualização/detalhes para o coordenador acompanhar
        $action_view = new TDataGridAction(array('ConteudoProgramaticoFormView', 'onShow'));
        $action_view->setLabel('Ver Detalhes');
        $action_view->setImage('fa:search blue');
        $action_view->setField('id');
        $this->datagrid->addAction($action_view);

        $this->datagrid->createModel();
        
        // navegação/paginação
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // empacotamento em caixa vertical
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Conteúdo Programático - Visão da Coordenação', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        
        // limpa os filtros anteriores da sessão
        TSession::setValue('ConteudoProgramaticoList_filter_id', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_curso', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_disciplina', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_etapa', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_turma', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_status', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_data_reg', NULL);
        TSession::setValue('ConteudoProgramaticoList_filter_system_user_id', NULL);

        if (isset($data->curso) AND ($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_curso', $filter);
        }

        if (isset($data->disciplina) AND ($data->disciplina)) {
            $filter = new TFilter('disciplina', 'like', "%{$data->disciplina}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_disciplina', $filter); 
        }

        if (isset($data->etapa) AND ($data->etapa)) {
            $filter = new TFilter('etapa', 'like', "%{$data->etapa}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_etapa', $filter); 
        }

        if (isset($data->turma) AND ($data->turma)) {
            $filter = new TFilter('turma', 'like', "%{$data->turma}%"); 
            TSession::setValue('ConteudoProgramaticoList_filter_turma', $filter);
        }
       
        $this->form->setData($data);
        TSession::setValue('ConteudoProgramatico_filter_data', $data);
        
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

            $cursosCoordenados = [];

            // -------------------------------------------------------------------------
            // PASSO 1: Buscar na base 'dados_fei' as turmas/cursos que estão sob responsabilidade do Coordenador logado
            // -------------------------------------------------------------------------
            TTransaction::open('dados_fei');
            
            $criteriaCoord = new TCriteria;
            $criteriaCoord->add(new TFilter('NomeCoordenador', '=', $nomeCoordenador));
            $criteriaCoord->add(new TFilter('Ano', '=', $anoAtual));
            $criteriaCoord->add(new TFilter('CodEntidade', '=', $loggedUnit));
            
            $turmasCoord = VwCoordenadorturmaetapa::getObjects($criteriaCoord);
            
            if (!empty($turmasCoord)) {
                foreach ($turmasCoord as $tc) {
                    if (!empty($tc->CodCurso)) {
                        $cursosCoordenados[] = $tc->CodCurso;
                    }
                    if (!empty($tc->CodGradeCurso)) {
                        $cursosCoordenados[] = $tc->CodGradeCurso;
                    }
                }
                $cursosCoordenados = array_unique($cursosCoordenados);
            }
            TTransaction::close();

            // Bloqueio de segurança preventivo: Se o coordenador não gerenciar nenhuma turma/curso, aborta
            if (empty($cursosCoordenados)) {
                $this->datagrid->clear();
                $this->pageNavigation->setCount(0);
                return;
            }

            // -------------------------------------------------------------------------
            // PASSO 2: Consultar os registros originais do conteúdo programático
            // -------------------------------------------------------------------------
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('ConteudoProgramatico');
            $limit = 10;
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);

            // aplicando os filtros ativos na sessão
            if (TSession::getValue('ConteudoProgramaticoList_filter_curso')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_curso')); 
            }
            if (TSession::getValue('ConteudoProgramaticoList_filter_disciplina')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_disciplina'));
            }
            if (TSession::getValue('ConteudoProgramaticoList_filter_etapa')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_etapa'));
            }
            if (TSession::getValue('ConteudoProgramaticoList_filter_turma')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_turma'));
            }

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                // Abre a conexão externa uma única vez antes do loop de tratamento para otimizar a performance
                TTransaction::open('dados_fei');

                foreach ($objects as $object)
                {
                    $object->data_reg = TDate::date2br($object->data_reg);

                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));

                    $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria2);
                    
                    if (!empty($disciplinaNome) && isset($disciplinaNome[0])) {
                        $object->disciplina = $disciplinaNome[0]->NomeDisciplina;
                        
                        // Validação Cruzada de Segurança:
                        // 1. O registro deve pertencer à mesma Unidade ($loggedUnit) do Coordenador.
                        // 2. O curso/grade da disciplina deve estar dentro do vetor de cursos permitidos ($cursosCoordenados).
                        if ($disciplinaNome[0]->CodEntidade == $loggedUnit) 
                        {
                            $isCoordinated = false;
                            
                            if (isset($disciplinaNome[0]->CodCurso) && in_array($disciplinaNome[0]->CodCurso, $cursosCoordenados)) {
                                $isCoordinated = true;
                            }
                            if (isset($disciplinaNome[0]->CodGradeCurso) && in_array($disciplinaNome[0]->CodGradeCurso, $cursosCoordenados)) {
                                $isCoordinated = true;
                            }

                            if ($isCoordinated) {
                                $this->datagrid->addItem($object);
                            }
                        }
                    }
                }
                
                TTransaction::close();
            }
            
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
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'], array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}