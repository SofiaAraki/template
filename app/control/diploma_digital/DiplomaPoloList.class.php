<?php

class DiplomaPoloList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct($param)
    {
        parent::__construct();


        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_DiplomaDigitalPolo');
        $this->form->setFormTitle('<h4>Polos</h4>');
        

        // create the form fields
        $nome_polo = new TEntry('nome_polo');
        $nome_municipio = new TEntry('nome_municipio');


        // add the fields
        $this->form->addFields( [ new TLabel('Polo') ], [ $nome_polo ] );
        $this->form->addFields( [ new TLabel('Município') ], [ $nome_municipio ] );


        // set sizes
        $nome_polo->setSize('80%');
        $nome_municipio->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        
        $this->form->addAction('Voltar', new TAction(array('DiplomaCursoList','onReload')), 'fas:arrow-alt-circle-left blue');
        
                
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center', 50);
        $column_dados_curso_id = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'left', 200);
        $column_nome_polo = new TDataGridColumn('nome_polo', 'Polo', 'left', 150);
        $column_nome_municipio = new TDataGridColumn('nome_municipio', 'Município', 'left');
        $column_uf = new TDataGridColumn('uf', 'UF', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dados_curso_id);
        $this->datagrid->addColumn($column_nome_polo);
        $this->datagrid->addColumn($column_nome_municipio);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);


        $action1 = new TDataGridAction(['DiplomaPoloForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);        
        $action2->setDisplayCondition( array($this, 'displayColumnDelete') );
        
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue fa-lg');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red fa-lg');
        
        
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
    

    public function onSearch()
    {
        $data = $this->form->getData();        
        
        TSession::setValue(__CLASS__.'_filter_nome_polo', NULL);
        TSession::setValue(__CLASS__.'_filter_nome_municipio', NULL);

        if (isset($data->nome_polo) AND ($data->nome_polo)) {
            $filter = new TFilter('nome_polo', 'like', "%{$data->nome_polo}%");
            TSession::setValue(__CLASS__.'_filter_nome_polo', $filter);
        }


        if (isset($data->nome_municipio) AND ($data->nome_municipio)) {
            $filter = new TFilter('nome_municipio', 'like', "%{$data->nome_municipio}%");
            TSession::setValue(__CLASS__.'_filter_nome_municipio', $filter);
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

            $dados_curso_id = TSession::getValue('dados_curso_id');
            
            $repository = new TRepository('DiplomaDigitalPolo');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_curso_id', '=', $dados_curso_id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_nome_polo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_polo'));
            }


            if (TSession::getValue(__CLASS__.'_filter_nome_municipio')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_municipio'));
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
            $polo = new DiplomaDigitalPolo($key);
            
            //Opção 1: Verifica se há histórico/documentação/diploma vinculada ao polo e, se houver, não permite a exclusão
            if((HistoricoDigital::where('dados_polo_id', '=', $polo->id)->count() > 0) OR
               (DiplomaDigitalDocumentacao::where('dados_polo_id', '=', $polo->id)->count() > 0) OR
               (DiplomaDigitalDiploma::where('dados_polo_id', '=', $polo->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) ao polo');
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
            
            $object = new DiplomaDigitalPolo($key, FALSE);
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
