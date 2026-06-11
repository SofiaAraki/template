<?php

class DiplomaCursoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct()
    {
        parent::__construct();
                
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalCurso');
        $this->form->setFormTitle('<h4>Cursos</h4>');
        

        // create the form fields
        $codigo_curso_sistema = new TEntry('codigo_curso_sistema');
        $nome_curso_diploma = new TEntry('nome_curso_diploma');


        // add the fields
        $this->form->addFields( [ new TLabel('Código') ], [ $codigo_curso_sistema ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome_curso_diploma ] );


        // set sizes
        $codigo_curso_sistema->setSize('80%');
        $nome_curso_diploma->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('DiplomaDigitalCurso_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Adicionar curso', new TAction(['DiplomaCursoForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_codigo_curso_sistema = new TDataGridColumn('codigo_curso_sistema', 'Código curso', 'center');
        $column_nome_curso_diploma = new TDataGridColumn('nome_curso_diploma', 'Nome', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_codigo_curso_sistema);
        $this->datagrid->addColumn($column_nome_curso_diploma);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);
                
        
        $action1 = new TDataGridAction(['DiplomaCursoForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        $action3 = new TDataGridAction([$this, 'onSetDadosCursoPoloForm'], ['id'=>'{id}']);
        $action4 = new TDataGridAction([$this, 'onSetDadosCursoPoloList'], ['id'=>'{id}']);
        $action5 = new TDataGridAction([$this, 'onSetDadosCursoAreaFormacao'], ['id'=>'{id}']);
         

        $action1->setLabel('Editar');
        $action1->setImage('far:edit blue');
        
        $action2->setLabel('Excluir');
        $action2->setImage('far:trash-alt red');
        $action2->setDisplayCondition( array($this, 'displayColumnDelete') );
        
        $action3->setLabel('Cadastrar polo');
        $action3->setImage('fas:map-marker-alt green');
                
        $action4->setLabel('Visualizar polos');
        $action4->setImage('fa:search orange');
        
        $action5->setLabel('Áreas de formação');
        $action5->setImage('fas:graduation-cap');
        
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');
        
        $action_group->addHeader('Curso');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addHeader('Polo');
        $action_group->addAction($action3);
        $action_group->addAction($action4);
        $action_group->addHeader('Áreas');
        $action_group->addAction($action5);
        $action_group->addSeparator();
                       
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
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
  
  
    public function onSetDadosCursoPoloForm($param)
    {
        $curso_id = $param['id'];
        
        //Limpa variável para garantir integridade e passa o valor
        TSession::setValue('dados_curso_id', NULL);
        TSession::setValue('dados_curso_id', $curso_id);
        
        TApplication::loadPage('DiplomaPoloForm', 'onEdit');
    }
      

    public function onSetDadosCursoPoloList($param)
    {
        $curso_id = $param['id'];
        
        //Limpa variável para garantir integridade e passa o valor
        TSession::setValue('dados_curso_id', NULL);
        TSession::setValue('dados_curso_id', $curso_id);

        TApplication::loadPage('DiplomaPoloList', 'onReload');
    }
    
    
    public function onSetDadosCursoAreaFormacao($param)
    {
        $curso_id = $param['id'];
        
        //Limpa variável para garantir integridade e passa o valor
        TSession::setValue('dados_curso_id', NULL);
        TSession::setValue('dados_curso_id', $curso_id);

        TApplication::loadPage('AreaFormacaoFormList', 'onReload');
    }
    
    
    public function onSearch()
    {
        $data = $this->form->getData();        
        
        TSession::setValue('DiplomaDigitalCursoList_filter_codigo_curso_sistema', NULL);
        TSession::setValue('DiplomaDigitalCursoList_filter_nome_curso_diploma', NULL);

        if (isset($data->codigo_curso_sistema) AND ($data->codigo_curso_sistema)) {
            $filter = new TFilter('codigo_curso_sistema', 'like', "%{$data->codigo_curso_sistema}%");
            TSession::setValue('DiplomaDigitalCursoList_filter_codigo_curso_sistema', $filter);
        }


        if (isset($data->nome_curso_diploma) AND ($data->nome_curso_diploma)) {
            $filter = new TFilter('nome_curso_diploma', 'like', "%{$data->nome_curso_diploma}%");
            TSession::setValue('DiplomaDigitalCursoList_filter_nome_curso_diploma', $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue('DiplomaDigitalCurso_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('DiplomaDigitalCurso');
            $limit = 10;
            
            $unit_id = TSession::getValue('userunitid');
            
            //Filtra os cursos de acordo com a unidade logada
            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('DiplomaDigitalCursoList_filter_codigo_curso_sistema')) {
                $criteria->add(TSession::getValue('DiplomaDigitalCursoList_filter_codigo_curso_sistema'));
            }


            if (TSession::getValue('DiplomaDigitalCursoList_filter_nome_curso_diploma')) {
                $criteria->add(TSession::getValue('DiplomaDigitalCursoList_filter_nome_curso_diploma'));
            }


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
                    $hr = substr($object->data_reg, 11, 19);
                    $dt = TDate::date2br($object->data_reg);
                    $object->data_reg = "$dt" . " " . substr($hr,0,-7);
                    
                    $this->datagrid->addItem($object);
                }
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
            TTransaction::rollback();
        }
    }
    
    
    //Se o usuário logado é do grupo Admin, exibe opção
    public function displayColumnDelete( $object )
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');
                
        if(( in_array($grupo_admin, $user_groups)))
        {
            return TRUE;
        }
            return FALSE;
    }
    

    public static function onDelete($param)
    {
        try
        {
            $action = new TAction([__CLASS__, 'Delete']);
            $action->setParameters($param);            
            
            TTransaction::open('Felabs_DB');
            
            $key = $param['key'];        
            $curso = new DiplomaDigitalCurso($key);
            
            //Opção 1: Verifica se há polo/currículo/área de formação/histórico/categoria de atividade complementar/documentação/diploma vinculado ao curso e, se houver, não permite a exclusão
            if((DiplomaDigitalPolo::where('dados_curso_id', '=', $curso->id)->count() > 0) OR
               (CurriculoDigital::where('dados_curso_id', '=', $curso->id)->count() > 0) OR
               (AreaFormacao::where('dados_curso_id', '=', $curso->id)->count() > 0) OR
               (HistoricoDigital::where('dados_curso_id', '=', $curso->id)->count() > 0) OR 
               (AtividadeComplementarCategoria::where('dados_curso_id', '=', $curso->id)->count() > 0) OR
               (DiplomaDigitalDocumentacao::where('dados_curso_id', '=', $curso->id)->count() > 0) OR
               (DiplomaDigitalDiploma::where('dados_curso_id', '=', $curso->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) ao curso');
                return false;
            }
            
            //Opção 2: Se não houver, só confirma se o usuário deseja realmente excluir
            else
            {    
                new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new DiplomaDigitalCurso($key, FALSE);
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

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

