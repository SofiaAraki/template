<?php

class DiplomaEmissoraList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalEmissora');
        $this->form->setFormTitle('<h4>Emissora</h4>');
        

        // create the form fields
        $nome = new TEntry('nome');


        // add the fields
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );


        // set sizes
        $nome->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('DiplomaDigitalEmissora_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Adicionar Emissora', new TAction(['DiplomaEmissoraForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_system_unit_id = new TDataGridColumn('system_unit->name', 'Unidade', 'center');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_cnpj = new TDataGridColumn('cnpj', 'CNPJ', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_unit_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cnpj);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);


        $column_cnpj->setTransformer(array($this, 'formatCNPJ'));
        
        
        // create EDIT action
        $action_edit = new TDataGridAction(['DiplomaEmissoraForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $action_del->setDisplayCondition(array($this, 'displayColumnDelete'));
        $this->datagrid->addAction($action_del);
        
        
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
    
    
    public function formatCNPJ($column_cnpj, $object, $row)
    {
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $column_cnpj);
    }
    

    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('DiplomaDigitalEmissoraList_filter_nome', NULL);

        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%");
            TSession::setValue('DiplomaDigitalEmissoraList_filter_nome', $filter);
        }
        
        $this->form->setData($data);
        
        TSession::setValue('DiplomaDigitalEmissora_filter_data', $data);
        
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
            
            $repository = new TRepository('DiplomaDigitalEmissora');
            $limit = 10;

            $unit_id = TSession::getValue('userunitid');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_unit_id', '=', $unit_id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('DiplomaDigitalEmissoraList_filter_nome')) {
                $criteria->add(TSession::getValue('DiplomaDigitalEmissoraList_filter_nome'));
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
    
    
    //Se o usuário logado é do grupo Admin, exibe a opção
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
            $emissora = new DiplomaDigitalEmissora($key);
            
            //Opção 1: Verifica se há cursos/currículos/documentação/diploma/histórico vinculados à emissora e, se houver, não permite a exclusão
            if((DiplomaDigitalCurso::where('dados_emissora_id', '=', $emissora->id)->count() > 0) OR 
               (CurriculoDigital::where('dados_emissora_id', '=', $emissora->id)->count() > 0) OR
               (DiplomaDigitalDocumentacao::where('dados_emissora_id', '=', $emissora->id)->count() > 0) OR
               (DiplomaDigitalDiploma::where('dados_emissora_id', '=', $emissora->id)->count() > 0) OR
               (HistoricoDigital::where('dados_emissora_id', '=', $emissora->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) à emissora');
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
            
            $object = new DiplomaDigitalEmissora($key, FALSE);
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
