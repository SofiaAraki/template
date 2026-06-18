<?php

class CadastroVeiculoListAll extends TPage
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
        $nome = new TEntry('nome');
        $proprietario = new TEntry('proprietario');


        // add the fields
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('Proprietário') ], [ $proprietario ] );
        $this->form->addFields( [ new TLabel('Placa') ], [ $placa ] );


        // set sizes
        $placa->setSize('25%');
        $nome->setSize('40%');
        $proprietario->setSize('40%');
        $placa->forceUpperCase();
        //$placa->setMask('SSS-9999');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('CadastroVeiculo_filter_data') );

        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction('Exportar Cadastros',  new TAction(array('RelatorioCadastroVeiculo', 'mostrar')), 'fa:table' );
        //$this->form->addAction('Exportar Requerimentos',  new TAction(array($this, 'onExportCSV')), 'fa:table' );
        $this->form->addAction('Gerar Relatório', new TAction(array($this, 'dialogRelatorio')), 'far:file-pdf red');
        //$this->form->addActionLink(_t('New'), new TAction(['CadastroVeiculoForm', 'onEdit']), 'fa:plus green');
        //$this->form->addAction(('Novo Cadastro'),  new TAction(array('CadastroVeiculoForm', 'onEdit')), 'fa:plus #69aa46');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_ciclo = new TDataGridColumn('ciclo', 'Ciclo', 'left');
        $column_proprietario = new TDataGridColumn('proprietario', 'Proprietário', 'left');
        $column_placa = new TDataGridColumn('placa', 'Placa', 'left');
        $column_modelo = new TDataGridColumn('modelo', 'Modelo', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'left');
        $column_grupo = new TDataGridColumn('grupo', 'Perfil', 'left');
        //$column_cor = new TDataGridColumn('cor', 'Cor', 'left');
        $column_validade = new TDataGridColumn('validade', 'Validade', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do Registro', 'left');
        $column_status = new TDataGridColumn('status', 'Situação', 'left');


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
        $this->datagrid->addColumn($column_grupo);
        //$this->datagrid->addColumn($column_cor);
        $this->datagrid->addColumn($column_validade);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_status);


        $action_download = new TDataGridAction(array($this, 'downloadArquivo'));
        //$action_edit->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel('Download');
        $action_download->setImage('fas:cloud-download-alt green fa-lg');
        $action_download->setField('id');
        $action_download->setDisplayCondition( array($this, 'displayColumnDownload') );
        $this->datagrid->addAction($action_download);

        
        // create EDIT action
        /*$action_edit = new TDataGridAction(['CadastroVeiculoAnaliseForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        //$action_edit->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_edit);

        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        //$action_del->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_del);*/
        
        
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
        $container->add(TPanelGroup::pack('Todos - Cadastros de Veículos', $this->datagrid, $this->pageNavigation));

        
        parent::add($container);
    }


    public function dialogRelatorio($param)
    {
        TTransaction::open('Felabs_DB');

        $qform = new TQuickForm('input_form');
        $qform->style = 'padding:20px';

        $ano = new TEntry('ano');
        $semestre = new TRadioGroup('semestre');

        $opcoes = [];
        $opcoes['1'] = '1º Semestre';
        $opcoes['2'] = '2º Semestre';

        $semestre->addItems($opcoes);
        $semestre->setLayout('horizontal');

        $ano->placeholder = 'Ex: 2018';
        $ano->setMask('9999');
            
        $qform->addQuickField('Digite o ano:', $ano);
        $qform->addQuickField('Escolha o semestre:', $semestre);

        TTransaction::close();

        $qform->addQuickAction('Gerar Relatório', new TAction(array($this, 'onPrint'),$param), 'far:file-pdf red');


        new TInputDialog('Informe:', $qform);
    }


    public function onPrint( $param ) 
    {   
        try
        {
            TTransaction::open('Felabs_DB');  
        
            $loggedUnit = TSession::getValue('userunitid');

            if( empty($param['ano']) )
            {
                throw new Exception('Informe o ano');
            }
    
            if( empty($param['semestre']) )
            {
                throw new Exception('Informe o semestre');
            }

            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('status', '=', 'Deferido'));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
    
            $cadastros = CadastroVeiculo::getObjects($criteria);

            $itens = [];
            
            foreach ($cadastros as $cadastro)
            {   
                $teste_semestre = strtotime($cadastro->data_reg);
                $teste_ano = strtotime($cadastro->data_reg);
    
                $data_semestre = date('m', $teste_semestre);
                $data_ano = date('Y', $teste_ano);
    
                if($param['semestre'] == 1)
                {
                    if($data_semestre < 8 && $param['ano'] == $data_ano)
                    {
                        $itens[] = $cadastro;
                    }
                }
                elseif($param['semestre'] == 2 )
                {
                    if($data_semestre > 7 && $param['ano'] == $data_ano)
                    {
                        $itens[] = $cadastro;
                    }
                }    
            }        

            if(!empty($itens))
            {
                $html = new AdiantiHTMLDocumentParser('app/resources/cadastro_veiculos.html', 'A4', 'portrait');
                
                $object = new CadastroVeiculo;
                $object->data_reg = $param['ano'] . "/" . $param['semestre'];

                $html->setMaster($object);
    
                $html->setDetail('CadastroVeiculo', $itens);
    
                $html->process();
                $output = $html->getContents();
                    
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
                    
                parent::openFile($document);
                
                new TMessage('info', 'Documento gerado com sucesso. Caso não consiga visualizá-lo, habilite pop-ups em seu navegador e tente novamente.');    
            }
            else
            {
                throw new Exception("Não há cadastros de veículos validados neste período.", 1);
            }
        
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
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
        /*else if ($color == "Solicitar alteração")
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


    public function displayColumnDownload( $object )
    {
        if (strlen($object->filename)>1)
        {
            return TRUE;
        }
        
        return FALSE;
    }


    public function downloadArquivo($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $id = $param['id'];  
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new CadastroVeiculo($id); 
                
                if (strtolower(substr($object->filename, -4)) == 'html')
                {
                    $win = TWindow::create( $object->filename, 0.8, 0.8 );
                    $win->add( file_get_contents( "arquivos/".$object->filename ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile($object->filename);                        
                }
            }
            else
            {
                $this->form->clear();
                //new TMessage('info', "Arquivo não localizado");
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
        TSession::setValue('CadastroVeiculoList_filter_nome', NULL);
        TSession::setValue('CadastroVeiculoList_filter_proprietario', NULL);

        if (isset($data->placa) AND ($data->placa)) {
            $filter = new TFilter('placa', 'like', "%{$data->placa}%"); 
            TSession::setValue('CadastroVeiculoList_filter_placa', $filter);
        }

        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); 
            TSession::setValue('CadastroVeiculoList_filter_nome', $filter); 
        }

        if (isset($data->proprietario) AND ($data->proprietario)) {
            $filter = new TFilter('proprietario', 'like', "%{$data->proprietario}%"); 
            TSession::setValue('CadastroVeiculoList_filter_proprietario', $filter);
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

            //$criteria->add(new TFilter('system_user_id', '=', $logged->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('CadastroVeiculoList_filter_placa')) {
                $criteria->add(TSession::getValue('CadastroVeiculoList_filter_placa')); 
            }

            if (TSession::getValue('CadastroVeiculoList_filter_nome')) {
                $criteria->add(TSession::getValue('CadastroVeiculoList_filter_nome')); 
            }

            if (TSession::getValue('CadastroVeiculoList_filter_proprietario')) {
                $criteria->add(TSession::getValue('CadastroVeiculoList_filter_proprietario')); 
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
