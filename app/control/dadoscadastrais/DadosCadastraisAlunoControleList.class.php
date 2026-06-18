<?php
class DadosCadastraisAlunoControleList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_ContatoAluno');
        $this->form->setFormTitle('<h4>Contatos Alunos</h4>');
        

        // create the form fields
        $cod_aluno = new TEntry('cod_aluno');


        // add the fields
        $this->form->addFields( [ new TLabel('Cód. aluno') ], [ $cod_aluno ] );


        // set sizes
        $cod_aluno->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';


        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_cod_aluno = new TDataGridColumn('cod_aluno', 'Cód. aluno', 'center');
        $column_logradouro = new TDataGridColumn('logradouro', 'Logradouro', 'left');
        $column_numero = new TDataGridColumn('numero', 'Nº', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_cidade = new TDataGridColumn('cidade', 'Cidade', 'left');
        $column_uf = new TDataGridColumn('uf', 'UF', 'center');
        $column_telefone_1 = new TDataGridColumn('telefone_1', 'Telefone 1', 'left');
        $column_telefone_2 = new TDataGridColumn('telefone_2', 'Telefone 2', 'left');
        $column_telefone_3 = new TDataGridColumn('telefone_3', 'Telefone 3', 'left');
        $column_contato_whatsapp = new TDataGridColumn('contato_whatsapp', 'Contato Whatsapp', 'left');
        $column_email = new TDataGridColumn('email', 'Email', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data edição', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cod_aluno);
        $this->datagrid->addColumn($column_logradouro);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_bairro);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_telefone_1);
        $this->datagrid->addColumn($column_telefone_2);
        $this->datagrid->addColumn($column_telefone_3);
        $this->datagrid->addColumn($column_contato_whatsapp);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);


        // define the transformer method over image
        $column_data_reg->setTransformer( function($value, $object, $row) {
            if ($value)
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
            return $value;
        });


        //$action1 = new TDataGridAction(['DadosCadastraisAlunoEditForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        //$this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue(__CLASS__.'_filter_cod_aluno', NULL);

        if (isset($data->cod_aluno) AND ($data->cod_aluno)) {
            $filter = new TFilter('cod_aluno', 'like', "%{$data->cod_aluno}%"); 
            TSession::setValue(__CLASS__.'_filter_cod_aluno', $filter); 
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
            
            $repository = new TRepository('ContatoAluno');
            $limit = 100;
 
            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'cod_aluno';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_cod_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cod_aluno')); 
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
    

    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new ContatoAluno($key, FALSE); 
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
