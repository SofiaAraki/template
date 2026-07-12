<?php

class AreaFormacaoFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();


        $this->form = new BootstrapFormBuilder('form_AreaFormacao');
        $this->form->setFormTitle('<h4>Áreas de Formação</h4>');
        $this->form->setFieldSizes('100%');
        

        //Para preenchimento do formulário
        $dados_curso_id = TSession::getValue('dados_curso_id');
       
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '=', $dados_curso_id));
                

        // create the form fields
        $id = new THidden('id');
        $dados_curso_id = new TDBCombo('dados_curso_id', 'Felabs_DB', 'DiplomaDigitalCurso', 'id', 'nome_curso_diploma', 'nome_curso_diploma', $criteria);
        $codigo = new TEntry('codigo');
        $nome = new TEntry('nome');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Curso <font color="red">*</font>'), $dados_curso_id ],
                                       [ new TLabel('Nome da área <font color="red">*</font>'), $nome ],
                                       [ new TLabel('Identificação / Sigla <font color="red">*</font>'), $codigo ]);
        $row->layout = ['col-sm-4', 'col-sm-6', 'col-sm-2'];
        

        $dados_curso_id->addValidation('Curso', new TRequiredValidator);
        $codigo->addValidation('Identificação / Sigla', new TRequiredValidator);
        $nome->addValidation('Nome da área', new TRequiredValidator);


        // set sizes
        $dados_curso_id->setDefaultOption(FALSE);
        $codigo->forceUpperCase();      
        

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        $this->form->addAction('Limpar campos', new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(array('DiplomaCursoList','onReload')), 'fas:arrow-alt-circle-left blue');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';


        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_dados_curso_id = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'center');
        $column_codigo = new TDataGridColumn('codigo', 'Identificação / Sigla', 'center');
        $column_nome = new TDataGridColumn('nome', 'Nome da área', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dados_curso_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_codigo);        
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        
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
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $dados_curso_id = TSession::getValue('dados_curso_id');
            
            $repository = new TRepository('AreaFormacao');
            $limit = 10;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_curso_id', '=', $dados_curso_id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
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
    

    public static function onDelete($param)
    {
        try
        {
            $action = new TAction([__CLASS__, 'Delete']);
            $action->setParameters($param); 
            
            TTransaction::open('Felabs_DB');
            
            $key = $param['key'];        
            $area_formacao = new AreaFormacao($key);
            
            //Opção 1: Verifica se há disciplina lançada no currículo ou histórico vinculado à área de formação e, se houver, não permite a exclusão
            $historicos = HistoricoDigital::where('areas_integralizadas_id', 'IS NOT', NULL)->load();
            
            foreach($historicos as $historico)
            {
                $areas_id = explode(',', $historico->areas_integralizadas_id);
                
                foreach($areas_id as $area_id)
                {
                    $areas[$area_id] = $area_id;
                }
            }
                    
            if((CurriculoDisciplinaArea::where('dados_area_formacao_id', '=', $area_formacao->id)->count() > 0) OR (in_array($area_formacao->id, $areas)))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) à área de formação');
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
            
            $object = new AreaFormacao($key, FALSE); 
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
            
            $object = new AreaFormacao;  
            $object->fromArray( (array) $data); 
                        
            //Verifica se o curso está marcado com a opção "Curso possui formação por áreas", se não estiver, exige a troca antes de salvar a área
            $id = TSession::getValue('dados_curso_id');
            
            $dados_curso = new DiplomaDigitalCurso($id);
            
            if($dados_curso->opcao_area <> "Curso possui formação por áreas")
            {
                throw new Exception("Para cadastrar uma área de formação é necessário alterar primeiro a opção 'Formação por áreas' no cadastro do curso");
            }
            
            
            //Se está salvando um novo registro
            if(empty($data->id))
            {
                //E já existe uma área com mesmo código, não deixa salvar
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('codigo', '=', trim($data->codigo)));
                    
                $repository = new TRepository('AreaFormacao'); 
                $registros_bd = $repository->load($criteria1);
                    
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro de área de formação com este mesmo código");
                }
            }    
                
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s'); 
                    
            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); 
            
            
            //Limpa o formulário depois de salvar, mas mantém o código do curso preenchido
            $this->form->clear();
                                
            $obj = new StdClass;
            $obj->dados_curso_id = $object->dados_curso_id;
                                
            TForm::sendData('form_AreaFormacao', $obj);
                   
            $this->onReload();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData( $this->form->getData() ); 
            
            TEntry::disableField('form_AreaFormacao', 'codigo');
            
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
                
                $object = new AreaFormacao($key);
                
                //O código não pode ser alterado
                TEntry::disableField('form_AreaFormacao', 'codigo');
                 
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
