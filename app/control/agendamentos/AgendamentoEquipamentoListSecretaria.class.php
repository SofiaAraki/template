<?php

/**
 * AgendamentoEquipamentoListSecretaria
 * @author  <your-name-here>
 */
class AgendamentoEquipamentoListSecretaria extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        // Creates the form
        $this->form = new TQuickForm('form_search_AgendamentoEquipamento');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table; width:100%'; 
        $this->form->setFormTitle('AgendamentoEquipamento');

        // Create the form fields
        $data_evento = new TDate('data_evento');
        $data_evento->setSize('40%');
        $data_evento->setMask('dd/mm/yyyy'); 

        // Add fields to form
        $this->form->addQuickField('Data', $data_evento, '50%');

        // Keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue('AgendamentoEquipamento_filter_data'));
        
        // Add the search form actions
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search blue');
        $this->form->addQuickAction('Novo', new TAction(array('AgendamentoEquipamentoForm', 'onEdit')), 'fa:plus green');
        $this->form->addQuickAction('Imprimir', new TAction(array($this, 'onPrint')), 'fa:print orange');
        
        // Creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        
        // Creates the datagrid columns
        $column_local = new TDataGridColumn('local', 'Local', 'left');
        $column_equipamento_id = new TDataGridColumn('agendamento_equipamento_item->equipamento', 'Equipamento', 'left');
        $column_data_evento = new TDataGridColumn('data_evento', 'Data', 'left');
        $column_inicio = new TDataGridColumn('inicio', 'Início', 'left');
        $column_termino = new TDataGridColumn('termino', 'Término', 'left');
        $column_observacoes = new TDataGridColumn('observacoes', 'Observações', 'left');
        $column_usuario = new TDataGridColumn('system_user->name', 'Usuário', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');

        // Add the columns to the DataGrid
        $this->datagrid->addColumn($column_local);
        $this->datagrid->addColumn($column_equipamento_id);
        $this->datagrid->addColumn($column_inicio);
        $this->datagrid->addColumn($column_termino);
        $this->datagrid->addColumn($column_observacoes);
        $this->datagrid->addColumn($column_data_evento);
        $this->datagrid->addColumn($column_usuario);
        $this->datagrid->addColumn($column_unidade);

        // Creates the datagrid column actions
        $order_data_evento = new TAction(array($this, 'onReload'));
        $order_data_evento->setParameter('order', 'data_evento');
        $column_data_evento->setAction($order_data_evento);
        
        $order_unidade = new TAction(array($this, 'onReload'));
        $order_unidade->setParameter('order', 'unidade');
        $column_unidade->setAction($order_unidade);
        
        // Create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // Create the datagrid model
        $this->datagrid->createModel();
        
        // Creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // Vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Lista de Agendamentos - Equipamentos', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function onPrint($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
        
            $loggedUnit = TSession::getValue('userunitid');
            $data = $this->form->getData();

            if (!$data->data_evento) {
                throw new Exception("Selecione uma data", 1);
            }
    
            $data->data_evento = TDate::date2us($data->data_evento);
    
            // Creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('data_evento', 'like', "%{$data->data_evento}%"));
    
            if ($loggedUnit == 2) {
                $criteria->add(new TFilter('unidade', 'IN', array(1, 2, 12)));
            } else {
                $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            }
    
            $agendamentos = AgendamentoEquipamento::getObjects($criteria);
        
            if (!empty($agendamentos)) {
                $html = new AdiantiHTMLDocumentParser('app/documents/AgendamentoEquip.html', 'A4', 'landscape');
    
                $object = new AgendamentoEquipamento;
                $object->data_evento = TDate::date2br($data->data_evento);
    
                $html->setMaster($object);
                $obj = [];
    
                foreach ($agendamentos as $agendamento) {
                    $equipamentoInfo = new AgendamentoEquipamentoItem($agendamento->equipamento_id);
                    $userInfo = new SystemUser($agendamento->usuario);
    
                    $agendamento->equipamento_id = $equipamentoInfo->equipamento;
                    $agendamento->usuario = $userInfo->name;
                    $agendamento->inicio = substr($agendamento->inicio, 11, 5);
                    $agendamento->termino = substr($agendamento->termino, 11, 5);
    
                    $obj[] = $agendamento;
                }

                $html->setDetail('AgendamentoEquipamento', $agendamentos);    
                $html->process();   
                
                $contents = $html->getContents();        
                $options = new \Dompdf\Options();
                $options->setChroot(getcwd());
                
                // Converts the HTML template into PDF
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
            
                file_put_contents('tmp/document.pdf', $dompdf->output());
    
                $window = TWindow::create('Certificado', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = 'download.php?file=tmp/document.pdf';
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";
                $window->add($object);
                $window->show();
            } else {
                throw new Exception("Não há agendamentos na data selecionada", 1);
            }        
    
            TTransaction::close();
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
        $data->data_evento = TDate::date2us($data->data_evento);
        
        // Clear session filters
        TSession::setValue('AgendamentoEquipamentoList_filter_id', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_local', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_equipamento_id', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_data_evento', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_inicio', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_termino', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_observacoes', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_usuario', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_unidade', NULL);
        TSession::setValue('AgendamentoEquipamentoList_filter_data_reg', NULL);

        if (isset($data->id) && $data->id) {
            TSession::setValue('AgendamentoEquipamentoList_filter_id', new TFilter('id', 'like', "%{$data->id}%")); 
        }

        if (isset($data->local) && $data->local) {
            TSession::setValue('AgendamentoEquipamentoList_filter_local', new TFilter('local', 'like', "%{$data->local}%")); 
        }

        if (isset($data->equipamento_id) && $data->equipamento_id) {
            TSession::setValue('AgendamentoEquipamentoList_filter_equipamento_id', new TFilter('equipamento_id', 'like', "%{$data->equipamento_id}%")); 
        }

        if (isset($data->data_evento) && $data->data_evento) {
            TSession::setValue('AgendamentoEquipamentoList_filter_data_evento', new TFilter('data_evento', 'like', "%{$data->data_evento}%")); 
        }

        if (isset($data->inicio) && $data->inicio) {
            TSession::setValue('AgendamentoEquipamentoList_filter_inicio', new TFilter('inicio', 'like', "%{$data->inicio}%")); 
        }

        if (isset($data->termino) && $data->termino) {
            TSession::setValue('AgendamentoEquipamentoList_filter_termino', new TFilter('termino', 'like', "%{$data->termino}%")); 
        }

        if (isset($data->observacoes) && $data->observacoes) {
            TSession::setValue('AgendamentoEquipamentoList_filter_observacoes', new TFilter('observacoes', 'like', "%{$data->observacoes}%")); 
        }

        if (isset($data->usuario) && $data->usuario) {
            TSession::setValue('AgendamentoEquipamentoList_filter_usuario', new TFilter('usuario', 'like', "%{$data->usuario}%"));
        }

        if (isset($data->unidade) && $data->unidade) {
            TSession::setValue('AgendamentoEquipamentoList_filter_unidade', new TFilter('unidade', 'like', "%{$data->unidade}%")); 
        }

        if (isset($data->data_reg) && $data->data_reg) {
            TSession::setValue('AgendamentoEquipamentoList_filter_data_reg', new TFilter('data_reg', 'like', "%{$data->data_reg}%"));
        }

        $data->data_evento = TDate::date2br($data->data_evento);
        $this->form->setData($data);
        
        TSession::setValue('AgendamentoEquipamento_filter_data', $data);
        
        $param = [];
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $loggedUnit = TSession::getValue('userunitid');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);

            $repository = new TRepository('AgendamentoEquipamento');
            $limit = 10;
            $criteria = new TCriteria;

            if ($loggedUnit == 2) {
                $criteria->add(new TFilter('unidade', 'IN', array(1, 2, 12))); 
            } else {
                $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            }

            if (empty($param['order'])) {
                $param['order'] = 'data_evento';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);

            // Reapply Filters
            if (TSession::getValue('AgendamentoEquipamentoList_filter_id')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_id')); 
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_local')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_local')); 
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_equipamento_id')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_equipamento_id')); 
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_data_evento')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_data_evento'));
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_inicio')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_inicio'));
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_termino')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_termino'));
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_observacoes')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_observacoes'));
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_usuario')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_usuario')); 
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_unidade')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_unidade'));
            }
            if (TSession::getValue('AgendamentoEquipamentoList_filter_data_reg')) {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoList_filter_data_reg')); 
            }

            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback)) {
                call_user_func($this->transformCallback, $objects, $param);
            }

            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects) {
                foreach ($objects as $object) {
                    if ($object->unidade == '0') {
                        $object->unidade = '<span class="label label-primary">FE</span>';                        
                    } elseif ($object->unidade == '12') {
                        $object->unidade = '<span class="label label-success">CONNEXT</span>';                        
                    } elseif ($object->unidade == '2') {
                        $object->unidade = '<span class="label label-warning">FFCL</span>';                        
                    } elseif ($object->unidade == '3') {
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    }

                    $object->inicio = substr($object->inicio, 11, -7);
                    $object->termino = substr($object->termino, 11, -7);

                    $object->data_evento = TDate::date2br($object->data_evento);
                    $object->data_reg = TDate::date2br($object->data_reg);

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
            
            $object = new AgendamentoEquipamento($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            $this->onReload($param); 
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
        if (!$this->loaded && (!isset($_GET['method']) || !(in_array($_GET['method'], array('onReload', 'onSearch'))))) {
            if (func_num_args() > 0) {
                $this->onReload(func_get_arg(0));
            } else {
                $this->onReload();
            }
        }
        parent::show();
    }
}