<?php

class TicketCategoriaFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new TQuickForm('form_TicketCategoria');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('TicketCategoria');
        

        $id = new TEntry('id');
        $departamento_id = new TCombo('departamento_id');
        $nome = new TEntry('nome');
        $exemplo_msg = new TText('exemplo_msg');
        

        $deptoItems = [];
        //$deptoItems[1] = 'Secretaria CNSC';
        $deptoItems[12] = 'Secretaria CONNEXT';
        $deptoItems[3] = 'Secretaria FAFRAM';
        $deptoItems[2] = 'Secretaria FFCL';        
        $deptoItems[6] = 'Secretaria NEAD';
        $deptoItems[13] = 'Atendimento - Professor';
        $deptoItems[14] = 'Departamento Financeiro';
        $deptoItems[15] = 'Departamento Comercial';
        

        $departamento_id->addItems($deptoItems);
        
        
        $id->setEditable(FALSE);
        $departamento_id->setValue(1);
        $exemplo_msg->setSize('100%',100);


        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Departamento', $departamento_id, '50%', new TRequiredValidator);
        $this->form->addQuickField('Nome', $nome, '100%', new TRequiredValidator);
        $this->form->addQuickField('Mensagem pré-definida', $exemplo_msg, '100%');


        // create the form actions
        $btn = $this->form->addQuickAction(('Salvar'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        $btn = $this->form->addQuickAction('Voltar', new TAction(array('TicketList', 'onReload')), 'far:arrow-alt-circle-left blue');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_departamento_id = new TDataGridColumn('departamento_id', 'Departamento', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_exemplo_msg = new TDataGridColumn('exemplo_msg', 'Mensagem pré-definida', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_departamento_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_exemplo_msg);

        
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
       // $this->datagrid->addAction($action2);
        
        
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
        $container->add(TPanelGroup::pack('Configurar Categorias', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('TicketCategoria');
            $limit = 10;

            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('TicketCategoria_filter'))
            {
                $criteria->add(TSession::getValue('TicketCategoria_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                  
                    // if($object->departamento_id == 1)
                    // {
                    //     $object->departamento_id = 'Secretaria CNSC';
                    // }
                    if($object->departamento_id == 2)
                    {
                        $object->departamento_id = 'Secretaria FFCL';
                    }
                    if($object->departamento_id == 3)
                    {
                        $object->departamento_id = 'Secretaria FAFRAM';
                    }
                    // if($object->departamento_id == 10)
                    // {
                    //     $object->departamento_id = 'Secretaria FAJOB';
                    // }
                    if($object->departamento_id == 12)
                    {
                        $object->departamento_id = 'Secretaria CONNEXT';
                    }
                    if($object->departamento_id == 13)
                    {
                        $object->departamento_id = 'Atendimento -  Professor';
                    }
                    if($object->departamento_id == 14)
                    {
                        $object->departamento_id = 'Departamento Financeiro';
                    }
                    if($object->departamento_id == 15)
                    {
                        $object->departamento_id = 'Departamento Comercial';
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
            
            $object = new TicketCategoria($key, FALSE);
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
            
            $this->form->validate();
            
            $object = new TicketCategoria;
            $data = $this->form->getData(); 
            $object->fromArray( (array) $data);
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
                
                $object = new TicketCategoria($key);
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
