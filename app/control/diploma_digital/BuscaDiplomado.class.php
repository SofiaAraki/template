<?php

class BuscaDiplomado extends TWindow
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    

    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Buscar aluno');
        parent::setSize(0.9, null);
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_FiAluno');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');


        // add the fields
        $this->form->addFields( [ new TLabel('Código aluno') ], [ $Codaluno ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $Nome ] );


        // set sizes
        $Codaluno->setSize('80%');
        $Nome->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('FiAluno_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Código aluno', 'center', '7%');
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
        $column_Sexo = new TDataGridColumn('Sexo', 'Sexo', 'center');
        $column_Datanascimento = new TDataGridColumn('Datanascimento', 'Data de nascimento', 'center', '10%');
        $column_CPF = new TDataGridColumn('CPF', 'CPF', 'center', '10%');
        $column_Rg = new TDataGridColumn('Rg', 'nº RG', 'left');
        $column_RgOrgaoExpedidor = new TDataGridColumn('RgOrgaoExpedidor', 'Órg. exp.', 'center');
        $column_Naturalidade = new TDataGridColumn('Naturalidade', 'Naturalidade', 'left');
        $column_NaturalidadeUF = new TDataGridColumn('NaturalidadeUF', 'UF', 'center', '7%');
        $column_Nacionalidade = new TDataGridColumn('Nacionalidade', 'Nacionalidade', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_Sexo);
        $this->datagrid->addColumn($column_Datanascimento);
        $this->datagrid->addColumn($column_CPF);
        $this->datagrid->addColumn($column_Rg);
        $this->datagrid->addColumn($column_RgOrgaoExpedidor);
        $this->datagrid->addColumn($column_Naturalidade);
        $this->datagrid->addColumn($column_NaturalidadeUF);
        $this->datagrid->addColumn($column_Nacionalidade);

        
        // create SELECT action
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(TRUE);
        $action_select->setButtonClass('nopadding');
        $action_select->setLabel('');
        $action_select->setImage('fa:hand-pointer green');
        $action_select->setField('Codaluno');
        $this->datagrid->addAction($action_select);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%;margin-bottom:0;border-radius:0';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

    public function onSearch()
    {
        $data = $this->form->getData();        
        
        TSession::setValue(__CLASS__.'_filter_Codaluno', NULL);
        TSession::setValue(__CLASS__.'_filter_Nome', NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', '=', $data->Codaluno);
            TSession::setValue(__CLASS__.'_filter_Codaluno', $filter);
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%");
            TSession::setValue(__CLASS__.'_filter_Nome', $filter);
        }

        
        $this->form->setData($data);
        
        TSession::setValue('FiAluno_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $repository = new TRepository('FiAluno');
            $limit = 10;

            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'Codaluno';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_Codaluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Codaluno'));
            }


            if (TSession::getValue(__CLASS__.'_filter_Nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Nome'));
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
                    $dt = TDate::date2br($object->Datanascimento);
                    $object->Datanascimento = substr($dt, 0, 11);
                    
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
    

    public static function onSelect($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('dados_fei');
            
            $object = FiAluno::find($key);
            
            TTransaction::close();
            
            /*RG e naturalidade não são enviados, pois precisam ser "desmembrados" para se adequarem ao padrão do MEC
            Ficam apenas visíveis para facilitar o preenchimento por parte do usuário*/ 
            $send = new StdClass;
            $send->cod_aluno = $object->Codaluno;
            $send->nome = $object->Nome;
            $send->nome_social = $object->NomeIdentificacaoCivil;
            $send->sexo = $object->Sexo;
            $send->data_nascimento = TDateTime::convertToMask($object->Datanascimento, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy');
            $send->cpf = $object->CPF;
            $send->nome_pai = $object->NomePai;
            $send->nome_social_pai = $object->NomePai;
            $send->nome_mae = $object->NomeMae;
            $send->nome_social_mae = $object->NomeMae;
            
            TForm::sendData('form_DiplomaDigitalDiplomado', $send);
            
            parent::closeWindow();
        }
        catch (Exception $e)
        {
            $send = new StdClass;
            $send->cod_aluno = '';
            $send->nome = '';
            $send->nome_social = '';
            $send->sexo = '';
            $send->data_nascimento = '';
            $send->cpf = '';
            $send->nome_pai = '';
            $send->nome_social_pai = '';
            $send->nome_mae = '';
            $send->nome_social_mae = '';
            
            TForm::sendData('form_DiplomaDigitalDiplomado', $send);
            
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
