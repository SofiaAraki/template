<?php

class AgendamentoEquipamentoItemFormList extends TPage
{
    protected $form;
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new TQuickForm('form_AgendamentoEquipamentoItem');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%';
        $this->form->setFormTitle('AgendamentoEquipamentoItem');
        

        // create the form fields
        $id = new THidden('id');
        $equipamento = new TEntry('equipamento');
        $unidade = new TCombo('unidade');
        $status = new THidden('status');
        $imagem = new TFile('imagem');
        $marca = new TEntry('marca');
        $modelo = new TEntry('modelo');
        $numero_serie = new TEntry('numero_serie');
        $patrimonio = new TEntry('patrimonio');
        $identificador = new TEntry('identificador');
        $data_aquisicao = new TDate('data_aquisicao');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        $items = array();
        $items['0'] = 'FE';
        $items['1'] = 'CNSC';
        $items['2'] = 'FFCL';
        $items['3'] = 'FAFRAM';
        //$items['4'] = 'FFCL PÓS'
        //$items['6'] = 'NEAD';
        $items['8'] = 'VAN GOGH';
        $items['12'] = 'CONNEXT';

        $unidade->addItems($items);


        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Equipamento', $equipamento, '100%');
        $this->form->addQuickField('Unidade', $unidade, '100%');
        $this->form->addQuickField('Status', $status, '100%');
        $this->form->addQuickField('Marca', $marca, '100%');
        $this->form->addQuickField('Modelo', $modelo, '100%');
        $this->form->addQuickField('Número de série', $numero_serie, '100%');
        $this->form->addQuickField('Patrimônio FE', $patrimonio, '100%');
        $this->form->addQuickField('Identificador', $identificador, '100%');
        $this->form->addQuickField('Imagem', $imagem, '100%');
        $this->form->addQuickField('Data de aquisição', $data_aquisicao, '50%');
        $this->form->addQuickField('System User Id', $system_user_id, '100%');
        $this->form->addQuickField('Data Reg', $data_reg, '100%');


        // create the form actions
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_equipamento = new TDataGridColumn('equipamento', 'Equipamento', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_status = new TDataGridColumn('status', 'Ativo', 'left');
        $column_imagem = new TDataGridColumn('imagem', 'Imagem', 'left');
        $column_marca = new TDataGridColumn('marca', 'Marca', 'left');
        $column_modelo = new TDataGridColumn('modelo', 'Modelo', 'left');
        $column_numero_serie = new TDataGridColumn('numero_serie', 'Número de série', 'left');
        $column_patrimonio = new TDataGridColumn('patrimonio', 'Patrimônio FE', 'left');
        $column_identificador = new TDataGridColumn('identificador', 'Identificador', 'left');
        $column_data_aquisicao = new TDataGridColumn('data_aquisicao', 'Data de aquisição', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'Cadastrado por', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data Reg', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_equipamento);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_status);
        //$this->datagrid->addColumn($column_imagem);
        $this->datagrid->addColumn($column_marca);
        $this->datagrid->addColumn($column_modelo);
        //$this->datagrid->addColumn($column_numero_serie);
        //$this->datagrid->addColumn($column_patrimonio);
        $this->datagrid->addColumn($column_identificador);
        //$this->datagrid->addColumn($column_data_aquisicao);
        //$this->datagrid->addColumn($column_system_user_id);
        //$this->datagrid->addColumn($column_data_reg);

        
        // creates two datagrid actions
        $action = new TDataGridAction(array('AgendamentoEquipamentoItemFormView', 'onEdit'));
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action->setLabel(_t('View'));
        $action->setImage('fa:search green fa-lg');
        $action->setField('id');


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


        $action_onoff = new TDataGridAction(array($this, 'onTurnOnOff'));
        $action_onoff->setButtonClass('btn btn-default');
        $action_onoff->setLabel(_t('Activate/Deactivate'));
        $action_onoff->setImage('fa:power-off fa-lg orange');
        $action_onoff->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action);
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action_onoff);
        
        
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
        $container->add(TPanelGroup::pack('Cadastro de Equipamentos', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        $alerta = new TAlert('warning', "Atenção: Usuários só poderão visualizar e agendar equipamentos da unidade que escolheram no momento do login. Escolha a unidade FE para permitir agendamentos a partir de outras unidades.");
        parent::add($alerta);
        parent::add($container);

    }


    public function onTurnOnOff($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $equip = AgendamentoEquipamentoItem::find($param['id']);

            if ($equip->status == 'N')
            {
                $equip->status = 'S';
            }
            elseif ($equip->status == 'S')
            {
                $equip->status = 'N';
            }
            
            $equip->store();
            
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('AgendamentoEquipamentoItem');
            $limit = 10;

            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('AgendamentoEquipamentoItem_filter'))
            {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoItem_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    if($object->unidade == '0')
                    {
                        $object->unidade = '<span class="label label-primary">FE</span>';                        
                    }
                    elseif($object->unidade == '12')
                    {
                        $object->unidade = '<span class="label label-success">CONNEXT</span>';                        
                    }
                    elseif($object->unidade == '2')
                    {
                        $object->unidade = '<span class="label label-warning">FFCL</span>';                        
                    }
                    elseif($object->unidade == '3')
                    {
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    }

                    if($object->status == 'N')
                    {
                        $object->status = '<span class="label label-danger">Não</span>';
                    }
                    elseif($object->status == 'S')
                    {
                        $object->status = '<span class="label label-success">Sim</span>';
                    }
                    elseif($object->status == 'E')
                    {
                        $object->status = '<span class="label label-warning">Excluir</span>';
                    }

                    $object->data_reg = TDate::date2br($object->data_reg);
                    $object->data_aquisicao = TDate::date2br($object->data_aquisicao);
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
            
            $object = new AgendamentoEquipamentoItem($key, FALSE);
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

            $this->form->validate();

            $object = new AgendamentoEquipamentoItem;
            $data = $this->form->getData();

            $data->system_user_id = $user->id;
            $data->status = 'S';
            $data->data_reg = date('Y-m-d H:i:s');

            $object->fromArray( (array) $data);
            

            if($object->imagem)   //SE USUÁRIO CARREGA FOTO
            {
                $userid = $user->id;
                $better_token = md5(uniqid(rand(), true));
                
                $partes = explode(".",$object->imagem);
                $extensaoPonto = '.'.$partes[1];
    
                $source_file   = 'tmp/'.$object->imagem;
                $nomeFoto = $better_token . '_' . $userid . $extensaoPonto;
                $target_file   = 'app/images/equipamentos/' . $nomeFoto;
                $finfo         = new finfo(FILEINFO_MIME_TYPE);
            
                //if the user uploaded a source file

                //move to the target directory
                //var_dump($source_file);
                //die();
                
                rename($source_file, $target_file);
                
                try
                {
                    TTransaction::open('Felabs_DB');
                    
                    $object->imagem = $nomeFoto;
                    //$user->store();

                    TTransaction::close();
                }
                catch (Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                    TTransaction::rollback();
                }
            
                $image = new TImage($nomeFoto);   
            }


            $object->store();
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
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
                
                $object = new AgendamentoEquipamentoItem($key);
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
