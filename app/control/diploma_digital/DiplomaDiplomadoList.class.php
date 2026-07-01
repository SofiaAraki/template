<?php

class DiplomaDiplomadoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_DiplomaDigitalDiplomado');
        $this->form->setFormTitle('<h4>Alunos</h4>');
        

        // create the form fields
        $cod_aluno = new TEntry('cod_aluno');
        $nome = new TEntry('nome');
        $cpf = new TEntry('cpf');


        // add the fields
        $this->form->addFields( [ new TLabel('Código aluno') ], [ $cod_aluno ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('CPF') ], [ $cpf ] );


        // set sizes
        $cod_aluno->setSize('80%');
        $nome->setSize('80%');
        $cpf->setSize('80%');
        $cpf->setMask('99999999999');
        
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Adicionar aluno', new TAction(['DiplomaDiplomadoForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_cod_aluno = new TDataGridColumn('cod_aluno', 'Código aluno', 'center');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_cpf = new TDataGridColumn('cpf', 'CPF', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        $column_cpf->setTransformer( array($this, 'setCpfAluno') );
        

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cod_aluno);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);


        // create EDIT action
        $action1 = new TDataGridAction(['DiplomaDiplomadoForm', 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel('Editar');
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        $this->datagrid->addAction($action1);
        

        // create DELETE action
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel('Excluir');
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        $action2->setDisplayCondition(array($this, 'displayColumn'));
        $this->datagrid->addAction($action2);
        
        
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
    
    
    public function setCpfAluno($column_cpf, $object, $row)
    {
        $object->cpf = preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/', '$1.$2.$3-$4', $object->cpf);
        
        return $object->cpf;
    }
    

    public function onSearch()
    {
        $data = $this->form->getData();        

        TSession::setValue(__CLASS__.'_filter_cod_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome', NULL);
        TSession::setValue(__CLASS__.'_filter_cpf', NULL);

        if (isset($data->cod_aluno) AND ($data->cod_aluno)) {
            $filter = new TFilter('cod_aluno', '=', $data->cod_aluno);
            TSession::setValue(__CLASS__.'_filter_cod_aluno', $filter);
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%");
            TSession::setValue(__CLASS__.'_filter_nome', $filter);
        }


        if (isset($data->cpf) AND ($data->cpf)) {
            $filter = new TFilter('cpf', 'like', "%{$data->cpf}%");            
            TSession::setValue(__CLASS__.'_filter_cpf', $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
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
            
            $repository = new TRepository('DiplomaDigitalDiplomado');
            $limit = 10;

            //Traz todos os alunos sem filtro, pois o vínculo entre aluno e curso só será feito ao criar a documentação ou o histórico
            //Não poderia filtrar com base na matrícula, pois alunos "pré-Genesi" não tem este registro
            $criteria = new TCriteria;
                        
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_cod_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cod_aluno'));
            }


            if (TSession::getValue(__CLASS__.'_filter_nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome'));
            }


            if (TSession::getValue(__CLASS__.'_filter_cpf')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cpf'));
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
    public function displayColumn( $object )
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
            $diplomado = new DiplomaDigitalDiplomado($key);
            
            //Opção 1: Verifica se há histórico/documentação/diploma vinculado ao diplomado e, se houver, não permite a exclusão
            if((HistoricoDigital::where('dados_diplomado_id', '=', $diplomado->id)->count() > 0) OR
               (DiplomaDigitalDocumentacao::where('dados_diplomado_id', '=', $diplomado->id)->count() > 0) OR
               (DiplomaDigitalDiploma::where('dados_diplomado_id', '=', $diplomado->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) ao aluno');
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
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }    
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new DiplomaDigitalDiplomado($key, FALSE);
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
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
