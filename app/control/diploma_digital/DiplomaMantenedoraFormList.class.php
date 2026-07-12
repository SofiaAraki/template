<?php

class DiplomaMantenedoraFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
                
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalMantenedora');
        $this->form->setFormTitle('<h4>Mantenedora</h4>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $razao_social = new TEntry('razao_social');
        $cnpj = new TEntry('cnpj');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $codigo_municipio = new TSeekButton('codigo_municipio');
        $nome_municipio = new TEntry('nome_municipio');
        $uf = new TCombo('uf');
        $cep = new TEntry('cep');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        $combo_uf = [];
        $combo_uf['AC'] = "AC";
        $combo_uf['AL'] = "AL";
        $combo_uf['AM'] = "AM";
        $combo_uf['AP'] = "AP";
        $combo_uf['BA'] = "BA";
        $combo_uf['CE'] = "CE";
        $combo_uf['DF'] = "DF";
        $combo_uf['ES'] = "ES";
        $combo_uf['GO'] = "GO";
        $combo_uf['MA'] = "MA";
        $combo_uf['MG'] = "MG";
        $combo_uf['MS'] = "MS";
        $combo_uf['MT'] = "MT";
        $combo_uf['PA'] = "PA";
        $combo_uf['PB'] = "PB";
        $combo_uf['PE'] = "PE";
        $combo_uf['PI'] = "PI";
        $combo_uf['PR'] = "PR";
        $combo_uf['RJ'] = "RJ";
        $combo_uf['RN'] = "RN";
        $combo_uf['RO'] = "RO";
        $combo_uf['RR'] = "RR";
        $combo_uf['RS'] = "RS";
        $combo_uf['SC'] = "SC";
        $combo_uf['SE'] = "SE";
        $combo_uf['SP'] = "SP";
        $combo_uf['TO'] = "TO";
        
        $uf->addItems($combo_uf);
        
        
        //Buscar dados do município
        $codigo_municipio->setAction(new TAction(['BuscaCidadeMantenedora', 'onReload']));
        

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Razão Social <font color="red">*</font>'), $razao_social ],
                                       [ new TLabel('CNPJ <font color="red">*</font>'), $cnpj ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];                                
                                              
        $this->form->addFields( [ '<br>' ] );
        
        $label1 = new TLabel('Endereço', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );

        $row = $this->form->addFields( [ new TLabel('Logradouro <font color="red">*</font>'), $logradouro ],
                                       [ new TLabel('Nº <font color="red">*</font>'), $numero ],
                                       [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Bairro <font color="red">*</font>'), $bairro ] );
        $row->layout = ['col-sm-6', 'col-sm-1', 'col-sm-2', 'col-sm-3' ];
        
        $row = $this->form->addFields( [ new TLabel('Código município <font color="red">*</font>'), $codigo_municipio ],
                                       [ new TLabel('Nome do município <font color="red">*</font>'), $nome_municipio ],
                                       [ new TLabel('UF <font color="red">*</font>'), $uf ],
                                       [ new TLabel('CEP <font color="red">*</font>'), $cep ] );
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-1', 'col-sm-3' ];        
        
        $this->form->addFields( [ '<br>' ] ); 
        $label_explicacao = new TLabel('<font color="red">*</font> Campos obrigatórios', '', 10, 'i');
        $this->form->addContent( [$label_explicacao] );
                               

        $razao_social->addValidation('Razão Social', new TRequiredValidator);
        $cnpj->addValidation('CNPJ', new TRequiredValidator);
        $cnpj->addValidation('CNPJ', new TCNPJValidator);   
        $logradouro->addValidation('Logradouro', new TRequiredValidator);
        $numero->addValidation('Nº', new TRequiredValidator);
        $bairro->addValidation('Bairro', new TRequiredValidator);
        $codigo_municipio->addValidation('Código município', new TRequiredValidator);
        $nome_municipio->addValidation('Nome do município', new TRequiredValidator);
        $uf->addValidation('UF', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);


        // set sizes
        $razao_social->setMaxLength('255');
        $razao_social->forceUpperCase();
        $cnpj->setMask('99.999.999/9999-99');
        $logradouro->setMaxLength('60');
        $numero->setMaxLength('60');
        $complemento->setMaxLength('60');
        $bairro->setMaxLength('60');
        $codigo_municipio->setMask('9999999');
        $nome_municipio->setMaxLength('255');
        $nome_municipio->forceUpperCase();
        $nome_municipio->setEditable(FALSE);
        $uf->setEditable(FALSE);
        $cep->setMask('99.999-999');
        $cep->setEditable(FALSE);


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        

        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center', 100);
        $column_razao_social = new TDataGridColumn('razao_social', 'Razão Social', 'left');
        $column_cnpj = new TDataGridColumn('cnpj', 'CNPJ', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_cnpj);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);


        $column_cnpj->setTransformer(array($this, 'formatCNPJ'));
        
        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        $action2->setDisplayCondition(array($this, 'displayColumnDelete'));
        
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        
        parent::add($container);
    }
    
    
    public function formatCNPJ($column_cnpj, $object, $row)
    {
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $column_cnpj);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('DiplomaDigitalMantenedora');
            $limit = 10;

            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $hr = substr($object->data_reg, 11, 19);
                    $dt = TDate::date2br($object->data_reg);
                    $object->data_reg = "$dt" . " " . substr($hr,0,-7);
                    
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
    
    
    //Se o usuário logado é do grupo Admin, exibe opção
    public function displayColumnDelete( $object )
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');
                
        if(( in_array($grupo_admin, $user_groups)))
        {
            return TRUE;
        }
            return FALSE;
    }
    

    public static function onDelete($param)
    {
        try
        {
            $action = new TAction([__CLASS__, 'Delete']);
            $action->setParameters($param);            
            
            TTransaction::open('Felabs_DB');
            
            $key = $param['key'];        
            $mantenedora = new DiplomaDigitalMantenedora($key);
            
            //Opção 1: Verifica se há emissora vinculada à mantenedora e, se houver, não permite a exclusão
            if(DiplomaDigitalEmissora::where('dados_mantenedora_id', '=', $mantenedora->id)->count() > 0)
            {
                new TMessage('error','O registro não pode ser excluído, pois há emissora(s) vinculada(s) à mantenedora');
                return false;
            }
            
            //Opção 2: Se não houver, só confirma se o usuário deseja realmente excluir
            else
            {    
                new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new DiplomaDigitalMantenedora($key, FALSE);
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $object = new DiplomaDigitalMantenedora;
            $object->fromArray( (array) $data);            
            
            $object->cnpj = str_replace(array(".", "/", "-"), "", $object->cnpj);
            
            //Se está salvando um "novo registro", mas já existe registro com mesmo cnpj
            if(empty($data->id))
            {
                $registros_bd = DiplomaDigitalMantenedora::where('cnpj', '=', $object->cnpj)->load();
                
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro desta mesma mantenedora");
                }
            }            
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store();
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            $this->onReload();
            $this->form->clear();
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
                
                $object = new DiplomaDigitalMantenedora($key);
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
