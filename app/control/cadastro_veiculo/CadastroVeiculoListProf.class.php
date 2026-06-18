<?php


class CadastroVeiculoListProf extends TPage
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
        $this->form = new BootstrapFormBuilder('form_CadastroVeiculo');
        $this->form->setFormTitle('Cadastro de Veículos');
        

        // create the form fields
        $placa = new TEntry('placa');


        // add the fields
        $this->form->addFields( [ new TLabel('Placa') ], [ $placa ] );


        // set sizes
        $placa->setSize('25%');
        $placa->forceUpperCase();
        $placa->setMask('SSS-9999');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('CadastroVeiculo_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['CadastroVeiculoForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction(('Novo Cadastro'),  new TAction(array('CadastroVeiculoFormProf', 'onEdit')), 'fa:plus #69aa46');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        //$this->datagrid->enablePopover(('Resumo'), '<b>'.('Análise dos dados').'</b><br>' . '{obs}');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_ciclo = new TDataGridColumn('ciclo', 'Ciclo', 'left');
        $column_proprietario = new TDataGridColumn('proprietario', 'Proprietário', 'left');
        $column_placa = new TDataGridColumn('placa', 'Placa', 'left');
        $column_modelo = new TDataGridColumn('modelo', 'Modelo', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'left');
        //$column_cor = new TDataGridColumn('cor', 'Cor', 'left');
        $column_validade = new TDataGridColumn('validade', 'Validade', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do Registro', 'left');
        $column_status = new TDataGridColumn('status', 'Situação', 'left');


        //$column_validade->setTransformer(array($this, 'formatDate'));
        $column_data_reg->setTransformer(array($this, 'formatDate'));
        $column_status->setTransformer( array($this, 'setStatusColor') );


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_ciclo);
        $this->datagrid->addColumn($column_proprietario);
        $this->datagrid->addColumn($column_placa);
        $this->datagrid->addColumn($column_modelo);
        $this->datagrid->addColumn($column_ano);
        //$this->datagrid->addColumn($column_cor);
        $this->datagrid->addColumn($column_validade);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_status);

        
        // create EDIT action
        /*$action_edit = new TDataGridAction(['CadastroVeiculoForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $action_edit->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_edit);*/
        
        // create EDIT action
        /*$action_pdf = new TDataGridAction(array('CadastroVeiculoFormView', 'onShow'));
        $action_pdf->setButtonClass('btn btn-default btn-sm');
        $action_pdf->setLabel('Visualizar');
        $action_pdf->setImage('fa:search #478fca');
        $action_pdf->setField('id');
        $this->datagrid->addAction($action_pdf);*/


        // create imprime action
        $action_del = new TDataGridAction(array('CadastroVeiculoListProf', 'onGenerate'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel('Imprimir Carteirinha');
        $action_del->setImage('fa:print red fa-lg');
        $action_del->setField('id');
        $action_del->setDisplayCondition( array($this, 'displayColumnPrint') );
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
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('Meus cadastros', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    function onGenerate($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $object = CadastroVeiculo::find($param['key']);

            //$object->validade = TDate::date2br($object->validade);
            $unidadeName = new SystemUnit($object->unidade);
            $object->unidade = $unidadeName->name;
            
            $designer = new TPDFDesigner;
            $designer->fromXml('app/reports/carteirinha_veiculo.pdf.xml');
            $designer->replace('{unidade}', $object->unidade );
            $designer->replace('{proprietario}', utf8_decode($object->proprietario ));
            $designer->replace('{nome}', utf8_decode($object->nome ));
            $designer->replace('{curso}', utf8_decode($object->curso));
            $designer->replace('{placa}', $object->placa );
            $designer->replace('{modelo}', utf8_decode($object->modelo ));
            $designer->replace('{ano}', $object->ano );
            $designer->replace('{cor}', utf8_decode($object->cor ));
            $designer->replace('{validade}', $object->validade );
            $designer->generate();                      
            
            $file = 'app/output/carteirinha_veiculo.pdf';
            
            if (!file_exists($file) OR is_writable($file))
            {
                $designer->save($file);
                parent::openFile($file);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $file);
            }
            
            new TMessage('info', 'Carteirinha de Veículo gerada com sucesso! Por favor, habilite os popups no navegador para visualizar.');
        }
        catch (Exception $e) 
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
        }
    }


    public function displayColumn( $object )
    {

        TTransaction::open('Felabs_DB'); 
        
        //$logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);

        //if ($object->situacao != 'Enviado' AND $object->situacao != 'Em análise' AND $object->situacao != 'Deferido' AND $object->situacao != 'Indeferido')
        if ($object->status != 'Em Análise' AND $object->status != 'Deferido' AND $object->status != 'Indeferido')
        {
            return TRUE;
        }
        
        return FALSE;
        
        TTransaction::close();
    }


    public function displayColumnPrint( $object )
    {

        TTransaction::open('Felabs_DB'); 
        
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
    
        //if ($object->status != 'Em análise' AND $object->status != 'Solicitar alteração' AND $object->status != 'Indeferido' )
        if ($object->status != 'Em Análise' AND $object->status != 'Indeferido' )
        {
            return TRUE;
        }
        
        return FALSE;
        
        TTransaction::close();
    }


    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }


    public function setStatusColor($column_status, $object, $row)
    {
        $color = $object->status;

        if($color == "Em Análise")
        {
            return '<span class="label label-warning">' . $column_status . '</span>';
        }        
        /*elseif($color == "Solicitar alteração")
        {
            return '<span class="label label-danger">' . $column_status . '</span>';
        }*/
         elseif($color == "Deferido")
        {
            return '<span class="label label-success">' . $column_status . '</span>';
        }       
        elseif($color == "Indeferido")
        {
            return '<span class="label label-primary">' . $column_status . '</span>';
        }
        else
        {
            return $column_status;
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
            
            $object = new CadastroVeiculo($key); 
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
        
        
        TSession::setValue('CadastroVeiculoList_filter_placa', NULL);

        if (isset($data->placa) AND ($data->placa)) {
            $filter = new TFilter('placa', 'like', "%{$data->placa}%");
            TSession::setValue('CadastroVeiculoList_filter_placa', $filter);
        }

        
        $this->form->setData($data);
        
        TSession::setValue('CadastroVeiculo_filter_data', $data);
        
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

            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $loggedUnit = TSession::getValue('userunitid');
            

            $repository = new TRepository('CadastroVeiculo');
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
            

            if (TSession::getValue('CadastroVeiculoList_filter_placa')) {
                $criteria->add(TSession::getValue('CadastroVeiculoList_filter_placa'));
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
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new CadastroVeiculo($key, FALSE);
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
