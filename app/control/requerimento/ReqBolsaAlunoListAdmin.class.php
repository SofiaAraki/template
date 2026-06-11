<?php

class ReqBolsaAlunoListAdmin extends TPage
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
        $this->form = new TQuickForm('form_search_Aluno');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%';
        $this->form->setFormTitle('Aluno');
        
        // create the form fields
        $nome = new TEntry('nome');

        // add the fields
        $this->form->addQuickField('Aluno', $nome,  '80%' );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Aluno_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fa:search');
        
                
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        //$this->datagrid->enablePopover(('Resumo'), '<b>'.('Parecer Técnico do(a) Assistente Social').'</b><br>' . '{obs}');
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');
        $column_nome = new TDataGridColumn('nome', 'Aluno', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_situacao = new TDataGridColumn('situacao', 'Situação', 'left');
        
        $column_situacao->setTransformer( array($this, 'setStatusColor') );
        $column_data_reg->setTransformer(array($this, 'formatDate'));


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_situacao);

        
        $action_visualizar = new TDataGridAction(array('ReqBolsaGestorFormView', 'onShow'));
        $action_visualizar->setLabel('Visualizar');
        $action_visualizar->setImage('fa:search green');
        $action_visualizar->setField('id');
        $this->datagrid->addAction($action_visualizar);


        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ReqBolsaUpdateAlunoForm', 'onEdit'));
        $action_edit->setLabel('Editar');
        $action_edit->setImage('far:edit blue');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Analisar', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public function setStatusColor($column_situacao, $object, $row)
    {
        $color = $object->situacao;
        
        if ($color == "Aberto")
        {
            return '<span class="label label-default">' . $column_situacao . '</span>';
        }
        else if ($color == "Em análise")
        {
            return '<span class="label label-warning">' . $column_situacao . '</span>';
        }
        else if ($color == "Aguardando assinaturas")
        {
            return '<span class="label label-info">' . $column_situacao . '</span>';
        }
        else if ($color == "Solicitar alteração")
        {
            return '<span class="label label-primary">' . $column_situacao . '</span>';
        }
         else if ($color == "Deferido")
        {
            return '<span class="label label-success">' . $column_situacao . '</span>';
        }       
        else if ($color == "Indeferido")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }
        else if ($color == "Indevido")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }
        else if ($color == "Desclassificado")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }
        else
        {
            return $column_situacao;
        }   
    }


    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }

    
    public function onSearch()
    {
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('AlunoList_filter_nome', NULL);


        if($data->nome)
        {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%");
            TSession::setValue('AlunoList_filter_nome', $filter);
        }

        
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Aluno_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');


            $repository = new TRepository('ReqBolsaAluno');
            $limit = 10;

            $criteria1 = new TCriteria;

            //$criteria1->add(new TFilter('system_user_id', '=', $logged->id));
            $criteria1->add(new TFilter('situacao', '=', 'Aberto'), TExpression::OR_OPERATOR);
            $criteria1->add(new TFilter('situacao', '=', 'Em análise'), TExpression::OR_OPERATOR);
            $criteria1->add(new TFilter('situacao', '=', 'Solicitar alteração'), TExpression::OR_OPERATOR);
            $criteria1->add(new TFilter('situacao', '=', 'Indevido'), TExpression::OR_OPERATOR);
            
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'situacao';
                $param['direction'] = 'asc';
            }
            
            
            $criteria = new TCriteria;
            $criteria->add($criteria1);                    
            
                        
            if(TSession::getValue('AlunoList_filter_nome'))
            {         
                $criteria2 = new TCriteria;       
                $criteria2->add(TSession::getValue('AlunoList_filter_nome'));
                $criteria->add($criteria2); 
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
    

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    //Apaga o registro no banco e exclui o arquivo do diretório
    public function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new ReqBolsaAluno($key, FALSE);
            
            if($object)
            {
                $arquivo = $object->filename;
                
                //Apaga zip requerimento
                if(file_exists($arquivo))
                {
                    unlink($arquivo);
                }
            }
            
            $object->delete();
            
            TTransaction::close();
            
            $this->onReload( $param );
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
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
