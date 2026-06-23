<?php
/**
 * HorarioAulasList Listing
 * @author   Pamella Scapim
 */
class HorarioAulasList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
               
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left');
        $column_Etapa = new TDataGridColumn('Etapa', 'Ciclo', 'left');

        $column_Etapa->setTransformer(function($value) {
            return $value .'º Ciclo';
        });

        $column_Identificacao = new TDataGridColumn('Identificacao', 'Turma', 'left');
        $column_Periodo = new TDataGridColumn('Periodo', 'Turno', 'left');
        $column_NomeCurso = new TDataGridColumn('NomeCurso',   'Curso', 'left');
        $column_Codprofessor = new TDataGridColumn('Codprofessor', 'Codprofessor', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_NomeDisciplina);
        $this->datagrid->addColumn($column_Etapa);
        $this->datagrid->addColumn($column_Periodo);
        $this->datagrid->addColumn($column_NomeCurso);

        // AÇÕES DA DATAGRID
        $action_faltas    = new TDataGridAction([$this, 'onSelectAula'], ['CodComposto'=>'{CodComposto}']);
        $action_conteudo  = new TDataGridAction([$this, 'onSelectConteudo'], ['CodComposto'=>'{CodComposto}']);
        $action_lista     = new TDataGridAction([$this, 'onSelectLista'], ['CodComposto'=>'{CodComposto}']);
        $action_papeleta  = new TDataGridAction([$this, 'onSelectPapeleta'], ['CodComposto'=>'{CodComposto}']);
       
        $action_faltas->setLabel('Registrar Frequências');
        $action_faltas->setImage('fa:check green');

        $action_conteudo->setLabel('Registrar Conteúdo Diário');
        $action_conteudo->setImage('fas:edit orange');

        // Modificado os Labels para deixar claro que ambos são relatórios unificados no mesmo grupo
        $action_lista->setLabel('Relatório: Lista de Alunos');
        $action_lista->setImage('fas:file-alt blue');

        $action_papeleta->setLabel('Relatório: Papeleta (1º e 2º Bimestre)');
        $action_papeleta->setImage('fas:file-pdf red');

        // Criando o grupo principal de ações
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');
        
        $action_group->addAction($action_faltas);
        $action_group->addAction($action_conteudo);
        
        // Separador visual opcional para organizar os relatórios juntos
        $action_group->addAction($action_lista);
        $action_group->addAction($action_papeleta);

        $this->datagrid->addActionGroup($action_group);
        
        // create the datagrid model
        $this->datagrid->createModel();
      
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('Diário de Classe Online - Lista de Disciplinas', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public function onSelectAula($param)
    {
        try 
        {
            TTransaction::open('Felabs_DB');
                $logged = SystemUser::newFromLogin(TSession::getValue('login'));
                $CodProfLogado = $logged->systemuser_codlegado;
            TTransaction::close();

            TTransaction::open('dados_fei_t');

            $key = $param['key']; //Busca o id da frente da disciplina

            // get the course description
            foreach ($this->datagrid->getItems() as $object)
            {
                if ($key == $object->CodComposto)
                {
                    TSession::setValue('sessao_diarioclasse', array('NomeDisciplina'    => $object->NomeDisciplina,
                                                                    'Codprofessor'      => $object->Codprofessor,
                                                                    'CodCurso'          => $object->CodCurso,
                                                                    'Etapa'             => $object->Etapa,
                                                                    'Ano'               => $object->Ano,
                                                                    'Semestre'          => $object->Semestre,
                                                                    'Periodo'           => $object->Periodo,
                                                                    'CodTurmaetapa'     => $object->CodTurmaetapa,
                                                                    'CodDisciplina'     => $object->CodDisciplina,
                                                                    'Identificacao'     => $object->Identificacao,
                                                                    'NomeProfessor'     => $object->NomeProfessor,
                                                                    'NomeCurso'         => $object->NomeCurso,
                                                                    'CodGradeDisciplinaEtapaFrente'    => $object->CodGradeDisciplinaEtapaFrente
                                                                    )
                                        );
                   
                }
                AdiantiCoreApplication::gotoPage('VwCalendarioacademicoForm');
            }
            TTransaction::close();
        }    
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    
    }


    public function onSelectConteudo($param)
    {
        TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $CodProfLogado = $logged->systemuser_codlegado;
        TTransaction::close();

        TTransaction::open('dados_fei_t');

        $key = $param['key']; //Busca o id da frente da disciplina

        // get the course description
        foreach ($this->datagrid->getItems() as $object)
        {

            if ($key == $object->CodComposto)
            {

                TSession::setValue('sessao_diarioclasse', array('NomeDisciplina'    => $object->NomeDisciplina,
                                                                'Codprofessor'      => $object->Codprofessor,
                                                                'CodCurso'          => $object->CodCurso,
                                                                'Etapa'             => $object->Etapa,
                                                                'Ano'               => $object->Ano,
                                                                'Semestre'          => $object->Semestre,
                                                                'Periodo'           => $object->Periodo,
                                                                'CodTurmaetapa'     => $object->CodTurmaetapa,
                                                                'CodDisciplina'     => $object->CodDisciplina,
                                                                'Identificacao'     => $object->Identificacao,
                                                                'NomeProfessor'     => $object->NomeProfessor,
                                                                'NomeCurso'         => $object->NomeCurso,
                                                                'CodGradeDisciplinaEtapaFrente'    => $object->CodGradeDisciplinaEtapaFrente
                                                        )
                                   );
                
                TTransaction::close();

                 $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse'); 

            }
            AdiantiCoreApplication::gotoPage('ConteudoDiarioClasseForm','onReload');
        }

   
    }
    
    public function onSelectLista($param)
    {
        try 
        {
            $cod_turma_disciplina = $param['CodComposto'];

            TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $CodProfLogado = $logged->systemuser_codlegado;
            TTransaction::close();

            TTransaction::open('dados_fei_t');

            $key = $param['key']; //Busca o id da frente da disciplina

            // get the course description
            foreach ($this->datagrid->getItems() as $object)
            {
                if ($key == $object->CodComposto)
                {
                    TSession::setValue('sessao_diarioclasse', array('NomeDisciplina'    => $object->NomeDisciplina,
                                                                    'Codprofessor'      => $object->Codprofessor,
                                                                    'CodCurso'          => $object->CodCurso,
                                                                    'Etapa'             => $object->Etapa,
                                                                    'Ano'               => $object->Ano,
                                                                    'Semestre'          => $object->Semestre,
                                                                    'Periodo'           => $object->Periodo,
                                                                    'CodTurmaetapa'     => $object->CodTurmaetapa,
                                                                    'CodDisciplina'     => $object->CodDisciplina,
                                                                    'Identificacao'     => $object->Identificacao,
                                                                    'NomeProfessor'     => $object->NomeProfessor,
                                                                    'NomeCurso'         => $object->NomeCurso,
                                                                    'CodGradeDisciplinaEtapaFrente'    => $object->CodGradeDisciplinaEtapaFrente
                                                            )
                                        );
                }

                TApplication::loadPage('ListaAlunosCompletoReport');
                
            }
            TTransaction::close();
            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
    /**
     * Carrega os dados da disciplina selecionada e abre o relatório do 1º e 2º Bimestre
     */
    public function onSelectPapeleta($param)
    {
        try 
        {
            $key = $param['key']; 

            foreach ($this->datagrid->getItems() as $object)
            {
                if ($key == $object->CodComposto)
                {
                    // Alimenta a sessão 'sessao_papeleta' exigida pela classe do relatório
                    TSession::setValue('sessao_papeleta', array(
                        'CodDisciplina'  => $object->CodDisciplina,
                        'CodTurmaetapa'  => $object->CodTurmaetapa,
                        'NomeProfessor'  => $object->NomeProfessor,
                        'Identificacao'  => $object->Identificacao,
                        'NomeEntidade'   => TSession::getValue('userunitname') ?? 'Instituição de Ensino',
                        'Periodo'        => $object->Periodo,
                        'NomeDisciplina' => $object->NomeDisciplina,
                        'NomeCurso'      => $object->NomeCurso,
                        'key'            => $object->CodGradeDisciplinaEtapaFrente
                    ));
                    
                    // Direciona imediatamente para a classe do relatório sem abrir telas intermediárias
                    TApplication::loadPage('VwPapeletaReportAll');
                    break;
                }
            }
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Inline record editing
     */
    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('dados_fei'); 
            $object = new ProfessoresDisciplinasTurmas($key); 
            $object->{$field} = $value;
            $object->store(); 
            TTransaction::close(); 
            
            $this->onReload($param); 
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));     
            $Unidade = $loggedUnit = TSession::getValue('userunitid');                   
            TTransaction::close();

            TTransaction::open('dados_fei');
           
            $repository = new TRepository('ProfessoresDisciplinasTurmas');
            $limit = 20;
            $criteria = new TCriteria;
            $criteria->add(new TFilter('Codprofessor', '=', $logged->systemuser_codlegado), TExpression::AND_OPERATOR); 
            $criteria->add(new TFilter('Ano', '=', 2026), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Semestre', '=', 1), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('CodEntidade', '=', $Unidade), TExpression::AND_OPERATOR);
            
            if (empty($param['order']))
            {
                $param['order'] = 'NomeDisciplina';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Shows the page
     */
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
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