<?php

class AgendamentoProvaList extends TPage
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
        $this->form = new TQuickForm('form_search_AgendamentoProva');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('AgendamentoProva');
        

        // create the form fields
        $id = new TEntry('id');
        $system_user_id = new TEntry('system_user_id');
        $turma = new TEntry('turma');
        $disciplina = new TEntry('disciplina');
        $data_prova = new TDate('data_prova');
        $filename = new TEntry('filename');
        $observacao = new TEntry('observacao');
        $status = new TEntry('status');
        $unidade = new TEntry('unidade');
        $data_reg = new TDate('data_reg');


        // add the fields
        $this->form->addQuickField('Id', $id,  '50%' );
        $this->form->addQuickField('Professor', $system_user_id,  '50%' );
        //$this->form->addQuickField('Turma', $turma,  '100%' );
        //$this->form->addQuickField('Disciplina', $disciplina,  '100%' );
        $this->form->addQuickField('Data da prova', $data_prova,  '50%' );
        //$this->form->addQuickField('Filename', $filename,  '100%' );
        //$this->form->addQuickField('Observação', $observacao,  '50%' );
        //$this->form->addQuickField('Status', $status,  '100%' );
        //$this->form->addQuickField('Unidade', $unidade,  '100%' );
        //$this->form->addQuickField('Data Reg', $data_reg,  '100%' );


        $data_prova->setMask('dd/mm/yyyy');
        $data_prova->setDatabaseMask('yyyy-mm-dd');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('AgendamentoProva_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction('Novo Agendamento',  new TAction(array('AgendamentoProvaFormList', 'onEdit')), 'bs:plus-sign green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Professor', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_data_prova = new TDataGridColumn('data_prova', 'Data da prova', 'left');
        $column_ciclo = new TDataGridColumn('ciclo', 'Ciclo', 'left');
        $column_filename = new TDataGridColumn('filename', 'Arquivo', 'left');
        $column_observacao = new TDataGridColumn('observacao', 'Observação', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do envio', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_data_prova);
        $this->datagrid->addColumn($column_ciclo);
        //$this->datagrid->addColumn($column_filename);
        $this->datagrid->addColumn($column_observacao);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_data_reg);
        

        // create EDIT action
        $action_download = new TDataGridAction(array($this, 'onDownload'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_download->setLabel('Download');
        $action_download->setImage('fas:cloud-download-alt purple fa-lg');
        $action_download->setField('id');
        $action_download->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_download);


        $action_onoff = new TDataGridAction(array($this, 'onTurnOnOff'));
        $action_onoff->setButtonClass('btn btn-default');
        $action_onoff->setLabel('Marcar como impresso');
        $action_onoff->setImage('fa:power-off fa-lg green');
        $action_onoff->setField('id');
        $this->datagrid->addAction($action_onoff);
        
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('AgendamentoProvaFormList', 'onEdit'));
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
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Agendamento de Provas', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public function displayColumn( $object )
    {
        if ($object->filename)
        {
            return TRUE;
        }
        
        return FALSE;
    }


    public function onTurnOnOff($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $prova = AgendamentoProva::find($param['id']);

            if ($prova->status == 'Pendente')
            {
                $prova->status = 'Impresso';
            }
            elseif ($prova->status == 'Impresso')
            {
                $prova->status = 'Pendente';
            }
            
            $prova->store();

            
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function onDownload($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $id = $param['id']; 
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new AgendamentoProva($id); 

                TPage::openFile("arquivos/provas/".$object->filename);                 
               
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
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
            
            $object = new AgendamentoProva($key); 
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
        
        
        TSession::setValue('AgendamentoProvaList_filter_id', NULL);
        TSession::setValue('AgendamentoProvaList_filter_system_user_id', NULL);
        TSession::setValue('AgendamentoProvaList_filter_turma', NULL);
        TSession::setValue('AgendamentoProvaList_filter_disciplina', NULL);
        TSession::setValue('AgendamentoProvaList_filter_data_prova', NULL);
        TSession::setValue('AgendamentoProvaList_filter_filename', NULL);
        TSession::setValue('AgendamentoProvaList_filter_observacao', NULL);
        TSession::setValue('AgendamentoProvaList_filter_status', NULL);
        TSession::setValue('AgendamentoProvaList_filter_unidade', NULL);
        TSession::setValue('AgendamentoProvaList_filter_data_reg', NULL);


        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); 
            TSession::setValue('AgendamentoProvaList_filter_id', $filter); 
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('(SELECT name from system_users WHERE id=agendamento_prova.system_user_id)', 'like', "%{$data->system_user_id}%");
            TSession::setValue('AgendamentoProvaList_filter_system_user_id', $filter); 
        }


        if (isset($data->turma) AND ($data->turma)) {
            $filter = new TFilter('turma', 'like', "%{$data->turma}%"); 
            TSession::setValue('AgendamentoProvaList_filter_turma', $filter); 
        }


        if (isset($data->disciplina) AND ($data->disciplina)) {
            $filter = new TFilter('disciplina', 'like', "%{$data->disciplina}%"); 
            TSession::setValue('AgendamentoProvaList_filter_disciplina', $filter); 
        }


        if (isset($data->data_prova) AND ($data->data_prova)) {
            $filter = new TFilter('cast(data_prova as date)', 'like', "{$data->data_prova}%"); 
            TSession::setValue('AgendamentoProvaList_filter_data_prova', $filter); 
        }


        if (isset($data->filename) AND ($data->filename)) {
            $filter = new TFilter('filename', 'like', "%{$data->filename}%"); 
            TSession::setValue('AgendamentoProvaList_filter_filename', $filter); 
        }


        if (isset($data->observacao) AND ($data->observacao)) {
            $filter = new TFilter('observacao', 'like', "%{$data->observacao}%"); 
            TSession::setValue('AgendamentoProvaList_filter_observacao', $filter); 
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); 
            TSession::setValue('AgendamentoProvaList_filter_status', $filter); 
        }


        if (isset($data->unidade) AND ($data->unidade)) {
            $filter = new TFilter('unidade', 'like', "%{$data->unidade}%"); 
            TSession::setValue('AgendamentoProvaList_filter_unidade', $filter); 
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('AgendamentoProvaList_filter_data_reg', $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue('AgendamentoProva_filter_data', $data);
        
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
            $loggedUnit = TSession::getValue('userunitid');

            $repository = new TRepository('AgendamentoProva');
            $limit = 15;


            $criteria = new TCriteria;
            //$criteria->add(new TFilter('system_user_id', '=', $user->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
    

            if (empty($param['order']))
            {
                $param['order'] = 'data_prova';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AgendamentoProvaList_filter_id')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_id')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_system_user_id')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_turma')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_turma')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_disciplina')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_disciplina')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_data_prova')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_data_prova')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_filename')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_filename')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_observacao')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_observacao')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_status')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_status')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_unidade')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_unidade')); 
            }


            if (TSession::getValue('AgendamentoProvaList_filter_data_reg')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_data_reg')); 
            }

            
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {                    
                    TTransaction::open('dados_fei');

                    $ano = date('Y',strtotime($object->data_reg));

                    $mes = date('m',strtotime($object->data_reg));

                    if($mes < 8)
                    {
                        $semestre = 1;
                    }
                    elseif($mes > 7)
                    {
                        $semestre = 2;
                    }

                    $criteria1 = new TCriteria;
                    $criteria1->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
                    $criteria1->add(new TFilter('Ano', '=', $ano));
                    $criteria1->add(new TFilter('Semestre', '=', $semestre));

                    $disciplinaInfo = VwProfessordisciplinassemestre::getObjects($criteria1);
                    

                    $object->ciclo = $disciplinaInfo[0]->Etapa;


                    TTransaction::close();

                    $object->disciplina = $disciplinaInfo[0]->NomeDisciplina;

                    $object->data_reg = date('d/m/Y H:i',strtotime($object->data_reg));  

                    $object->data_prova = date('d/m/Y H:i',strtotime($object->data_prova));

                    $unidadeInfo = new SystemUnit($object->unidade);

                    $object->unidade = $unidadeInfo->name;


                    if($object->status == 'Pendente')
                    {
                        $object->status = '<span class="label label-warning">Pendente</span>';
                    }
                    if($object->status == 'Impresso')
                    {
                        $object->status = '<span class="label label-success">Impresso</span>';
                    }  

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
            
            $object = new AgendamentoProva($key, FALSE); 
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
