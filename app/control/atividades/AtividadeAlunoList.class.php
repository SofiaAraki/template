<?php

class AtividadeAlunoList extends TPage
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
        
        
        TTransaction::open('Felabs_DB');
        
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);


        $atividadeId = TSession::getValue('atividadeid');
        $atividadeInfo = new Atividade($atividadeId);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_AtividadeAluno');
        $this->form->setFormTitle($atividadeInfo->nome);
        

        $label1 = new TLabel('Descrição:', '#333333', '15px', '');
        
  
        if($atividadeInfo->anexo) //SE TIVER ANEXO, MOSTRA
        {  
            $label2 = new TLabel('Anexo:', '#333333', '15px', '');   
            $button  = new TButton('download_anexo');

            $param = [];
            $param['id'] = TSession::getValue('atividadeid');

            $button->setImage('fas:cloud-download-alt');
            $button->setAction(new TAction(array($this, 'onDownloadMaster'),$param), 'Download');
        }


        if($atividadeInfo->descricao) //SE TIVER DESCRIÇÃO, MOSTRA
        {
            $text1  = new TTextDisplay($atividadeInfo->descricao, '#333333', '15px', '');
            $this->form->addFields([$label1],[$text1]);
        }

        $this->form->addFields([$label2],[$button]);



        if($atividadeInfo->tipo != 1) //SE FOR APENAS UM ANEXO PARA DOWNLOAD, NÃO HABILITA BOTÃO DE ENVIO DE ATIVIDADE PELO ALUNO
        {
            //TSession::setValue('atividadeid',$param['key']);
            $btn1 = $this->form->addAction('Enviar Atividade', new TAction(array('AtividadeAlunoForm', 'mostrar')), 'bs:plus-sign green');
        }

        
        $this->form->addAction('Voltar',new TAction(array('AtividadeList','onReload')),'fa:arrow-circle-left blue');

        if ($user->checkInGroup( new SystemGroup(3)) ) //PROF
        {
            $btn2 = $this->form->addAction('Editar Atividade',  new TAction(array('AtividadeForm', 'onEdit'),$param), 'far:edit blue');
            $btn2 = $this->form->addAction('Excluir Atividade',  new TAction(array($this, 'onDelete'),$param), 'far:trash-alt red fw-lg');
        }

        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_atividade_id = new TDataGridColumn('atividade_id', 'Atividade Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'System User Id', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_anexo = new TDataGridColumn('anexo', 'Anexo', 'left');
        $column_nota = new TDataGridColumn('nota', 'Nota', 'left');
        $column_feedback = new TDataGridColumn('feedback', 'Feedback', 'left');
        $column_data_envio = new TDataGridColumn('data_envio', 'Data Envio', 'left');
        $column_data_ultimaedicao = new TDataGridColumn('data_ultimaedicao', 'Data Ultimaedicao', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_atividade_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_anexo);
        $this->datagrid->addColumn($column_nota);
        $this->datagrid->addColumn($column_feedback);
        $this->datagrid->addColumn($column_data_envio);
        $this->datagrid->addColumn($column_data_ultimaedicao);


        // creates the datagrid column actions
        $order_system_user_id = new TAction(array($this, 'onReload'));
        $order_system_user_id->setParameter('order', 'system_user_id');
        $column_system_user_id->setAction($order_system_user_id);
        
        $order_anexo = new TAction(array($this, 'onReload'));
        $order_anexo->setParameter('order', 'anexo');
        $column_anexo->setAction($order_anexo);
        
        $order_nota = new TAction(array($this, 'onReload'));
        $order_nota->setParameter('order', 'nota');
        $column_nota->setAction($order_nota);
        
        $order_feedback = new TAction(array($this, 'onReload'));
        $order_feedback->setParameter('order', 'feedback');
        $column_feedback->setAction($order_feedback);
        
        $order_data_envio = new TAction(array($this, 'onReload'));
        $order_data_envio->setParameter('order', 'data_envio');
        $column_data_envio->setAction($order_data_envio);
        
        $order_data_ultimaedicao = new TAction(array($this, 'onReload'));
        $order_data_ultimaedicao->setParameter('order', 'data_ultimaedicao');
        $column_data_ultimaedicao->setAction($order_data_ultimaedicao);
        


        // inline editing
        $nota_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        $nota_edit->setField('id');
        $column_nota->setEditAction($nota_edit);
        
        $feedback_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        $feedback_edit->setField('id');
        $column_feedback->setEditAction($feedback_edit);
        

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('AtividadeAlunoForm', 'onEdit'));
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
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);


        if($atividadeInfo->tipo != 1)
        {
            $container->add(TPanelGroup::pack('Envios', $this->datagrid, $this->pageNavigation));
        }     
        
        parent::add($container);
    }


    public function onDownloadMaster($param)
    {
        try
        {
            $id = $param['id'];
        
            TTransaction::open('Felabs_DB');
        
            $object = new Atividade($id);
              
            TTransaction::close();

                

            if(!empty($object->anexo))
            {              
                if (strtolower(substr($object->anexo, -4)) == 'html')
                {
                    $win = TWindow::create( $object->anexo, 0.8, 0.8 );
                    $win->add( file_get_contents( "files/atividades/atividade/".$object->anexo ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile("files/atividades/atividade/".$object->anexo);
                }
                
                $this->form->setData( $this->form->getData() ); 
                
                $parametros = [];
                $parametros['id'] = $param['id'];
                $parametros['key'] = $param['id'];

                TApplication::loadPage('AtividadeAlunoList', 'onReload', $parametros);
                TTransaction::rollback();
            }
            else
            {
                new TMessage('info', 'Esta atividade não possui anexos'); 
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback(); 
        }
    }
    

    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new AtividadeAluno($key); 
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
    
 
    public function onSearch()
    {
        $data = $this->form->getData();
        

        TSession::setValue('AtividadeAlunoList_filter_id', NULL);
        TSession::setValue('AtividadeAlunoList_filter_system_user_id', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%");
            TSession::setValue('AtividadeAlunoList_filter_id',   $filter); 
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', 'like', "%{$data->system_user_id}%");
            TSession::setValue('AtividadeAlunoList_filter_system_user_id',   $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue('AtividadeAluno_filter_data', $data);
        
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
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);


            $repository = new TRepository('AtividadeAluno');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('atividade_id', '=', $param['key']));

           

            if($user->funcao_legado == 'Aluno') //SE FOR ALUNO, MOSTRA APENAS ATIVIDADE DELE (LOGADO)
            {
                $criteria->add(new TFilter('system_user_id', '=', $user->id));
            }


            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AtividadeAlunoList_filter_id')) {
                $criteria->add(TSession::getValue('AtividadeAlunoList_filter_id'));
            }


            if (TSession::getValue('AtividadeAlunoList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('AtividadeAlunoList_filter_system_user_id'));
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
    

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB');
            
            $object = new AtividadeAluno($key, FALSE);
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
