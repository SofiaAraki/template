<?php

class AgendamentoProvaFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();


        TTransaction::open('Felabs_DB');
        
        //$logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        $loggedUnit = TSession::getValue('userunitid'); //UNIDADE ESCOLHIDA NO MOMENTO DO LOGIN
        
        TTransaction::close();
        
        
        // creates the form
        $this->form = new TQuickForm('form_AgendamentoProva');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('AgendamentoProva');
        

        // create the form fields
        $id = new THidden('id');
        $filename = new TFile('filename');
        $disciplina = new TCombo('disciplina');
        $turma = new TEntry('turma');
        $data_prova = new TDateTime('data_prova');
        $observacao = new TEntry('observacao');
        $status = new THidden('status');
        $unidade = new THidden('unidade');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        $data_prova->setMask('dd/mm/yyyy hh:ii');
        $data_prova->setDatabaseMask('yyyy-mm-dd hh:ii');
        $turma->setEditable(FALSE);


        TTransaction::open('dados_fei');

        $ano = date('Y');

        $mes = date('m');

        if($mes < 8)
        {
            $semestre = 1;
        }
        elseif($mes > 7)
        {
            $semestre = 2;
        }            
            
        // creates a criteria
        $criteria = new TCriteria;
        
        if($user->funcao_legado == 'Professor')
        {
            $criteria->add(new TFilter('CodProfessor', '=', $user->systemuser_codlegado));
        }
        else
        {
            $disciplina->enableSearch();
        }
        
        $criteria->add(new TFilter('Ano', '=', $ano), TExpression::AND_OPERATOR);
        $criteria->add(new TFilter('Semestre', '=', $semestre), TExpression::AND_OPERATOR);
        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit), TExpression::AND_OPERATOR);

        $repos = VwProfessordisciplinassemestre::getObjects($criteria);
        

        $items = [];
      

        foreach($repos as $repo)
        {
            $items[$repo->CodGradeDisciplinaEtapaFrente] = $repo->NomeDisciplina;
        }

        $disciplina->addItems($items);
        
        $change_action = new TAction(array($this, 'onChangeAction'));
        $disciplina->setChangeAction($change_action);

        TTransaction::close();


        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        //$this->form->addQuickField('', $label, '50%');        
        $this->form->addQuickField('Disciplina', $disciplina, '100%', new TRequiredValidator);
        $this->form->addQuickField('Turma', $turma, '100%', new TRequiredValidator);
        $this->form->addQuickField('Data e horário da prova', $data_prova, '50%', new TRequiredValidator);
        $this->form->addQuickField('Observação', $observacao, '100%');
        $this->form->addQuickField('Status', $status, '100%');
        $this->form->addQuickField('Unidade', $unidade, '50%');
        $this->form->addQuickField('Professor', $system_user_id, '50%');
        $this->form->addQuickField('Data do registro', $data_reg, '100%');
        
if($loggedUnit == 2)
        {
            $this->form->addQuickField('Anexar arquivo', $filename, '100%');
        }        
else if($loggedUnit == 3)
        {
            $this->form->addQuickField('Anexar arquivo', $filename, '100%');
        }
        else if($loggedUnit == 10)
        {
            $this->form->addQuickField('Anexar arquivo', $filename, '100%');
        }
        elseif($loggedUnit == 6)
        {
            $this->form->addQuickField('Anexar arquivo', $filename, '100%');
        }

         
        // create the form actions
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        if($user->funcao_legado != 'Professor')
        {
            $this->form->addQuickAction('Voltar',  new TAction(array('AgendamentoProvaList', 'onReload')), 'far:arrow-alt-circle-left blue');
        }
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        //$this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_filename = new TDataGridColumn('filename', 'Filename', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_prova = new TDataGridColumn('data_prova', 'Data e horário da prova', 'left');
        $column_observacao = new TDataGridColumn('observacao', 'Observação', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Professor', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
       // $this->datagrid->addColumn($column_filename);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_prova);
        $this->datagrid->addColumn($column_observacao);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onEdit'));
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'AgendamentoProvaListProfessor'));
        $container->add(TPanelGroup::pack('Agendamento de Provas', $this->form));
        //$container->add(TPanelGroup::pack('Meus Agendamentos', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public static function onChangeAction($param)
    {
        TTransaction::open('dados_fei');
         
        //$repository = new TRepository('VwProfessordisciplinassemestre');

        $ano = date('Y');
        $mes = date('m');

        if($mes < 8)
        {
            $semestre = 1;
        }
        elseif($mes > 7)
        {
            $semestre = 2;
        }

        $codDisciplina = $param['disciplina'];

        // creates a criteria
        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $codDisciplina));
        $criteria->add(new TFilter('Ano', '=', $ano));//$ano
        $criteria->add(new TFilter('Semestre', '=', $semestre));//$semestre

        //$repo = $repository->load($criteria);

        $repo = VwProfessordisciplinassemestre::getObjects($criteria);

        //var_dump($repo[0]);


        $obj = new StdClass;
        //$obj->curso = $repo[0]->NomeCurso;
        $obj->turma = $repo[0]->Identificacao;
        TForm::sendData('form_AgendamentoProva', $obj);

        TTransaction::close();
    }


    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $loggedUnit = TSession::getValue('userunitid');
            

            $repository = new TRepository('AgendamentoProva');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));  
            

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('AgendamentoProva_filter'))
            {
                $criteria->add(TSession::getValue('AgendamentoProva_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    TTransaction::open('dados_fei');

                    $criteria1 = new TCriteria;
                    $criteria1->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));

                    $disciplinaInfo = VwProfessordisciplinassemestre::getObjects($criteria1);

                    //var_dump($disciplinaInfo);
                    //die;                    

                    TTransaction::close();

                    $object->disciplina = $disciplinaInfo[0]->NomeDisciplina;
               
                    $object->data_reg = date('d/m/y H:i:s',strtotime($object->data_reg));
                  
                    $object->data_prova = date('d/m/y H:i:s',strtotime($object->data_prova));

                    $unidadeInfo = new SystemUnit($object->unidade);

                    $object->unidade = $unidadeInfo->name;


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
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
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
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); 
            TTransaction::rollback();
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $loggedUnit = TSession::getValue('userunitid');
                        
            $this->form->validate(); 
            
            $object = new AgendamentoProva;  
            $data = $this->form->getData(); 

            //$dataProva = $data->data_prova;
            //$dataProva->setDatabaseMask('yyyy-mm-dd hh:ii:ss');

            //var_dump($dataProva);
            //die;           

            $data->system_user_id = $user->id;
            $data->data_reg = date('Y-m-d H:i:s');
            //$data->data_prova = date('Y-m-d H:i:s',strtotime($data->data_prova));
            $data->status = 'Pendente';
            $data->unidade = $loggedUnit;

            $object->fromArray( (array) $data); 


            if ($object->filename)
            {
                $today = date("Ymd");
                $source_file = 'tmp/'.$object->filename;

                $arquivo = TSession::getValue('login') . '_' . $today . '_' . $object->filename;


                $target_file = 'arquivos/provas/' . $arquivo;
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                
                if (file_exists($source_file))
                {
                    // move to the target directory
                    rename($source_file, $target_file);
                }

                $object->filename = $arquivo;
            }


            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), TApplication::loadPage('AgendamentoProvaListProfessor',NULL)); // success message
            $this->onReload(); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData( $this->form->getData() ); 
            TTransaction::rollback(); 
        }
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                $object = new AgendamentoProva($key);

                //$object->data_prova = '';

                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
