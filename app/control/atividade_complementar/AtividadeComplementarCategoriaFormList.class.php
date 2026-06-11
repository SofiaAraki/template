<?php

class AtividadeComplementarCategoriaFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    
    protected $formDatagrid;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        $unit_id = TSession::getValue('userunitid');
        
        try
        {
            //Filtrar cursos de acordo com a unidade no momento de logar
            TTransaction::open('Felabs_DB');        
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
            $criteria_curso->setProperty('order', 'id');

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
            
        
        $this->form = new BootstrapFormBuilder('form_AtividadeComplementarCategoria');
        $this->form->setFormTitle('<h4>Categorias de Atividade Complementar</h4>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $codigo = new TEntry('codigo');
        $nome = new TCombo('nome');
        $dados_curso_id = new TDBCombo('dados_curso_id', 'Felabs_DB', 'DiplomaDigitalCurso', 'id', 'nome_curso_diploma', 'nome_curso_diploma', $criteria_curso);
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        $combo_nome = [];
        $combo_nome['Atividades culturais'] = "Atividades culturais";
        $combo_nome['Atividades de ensino'] = "Atividades de ensino";
        $combo_nome['Atividades de extensão'] = "Atividades de extensão";
        $combo_nome['Atividades de pesquisa'] = "Atividades de pesquisa";
        
        $nome->addItems($combo_nome);
        
        
        //Preenche o código da categoria de acordo com o nome escolhido
        $nome->setChangeAction(new TAction(array($this, 'onNomeCategoriaChange')));
                

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Curso <font color="red">*</font>'), $dados_curso_id ],
                                       [ new TLabel('Nome da categoria <font color="red">*</font>'), $nome ],
                                       [ new TLabel('Identificação <font color="red">*</font>'), $codigo ]);
        $row->layout = ['col-sm-4', 'col-sm-6', 'col-sm-2'];


        $dados_curso_id->addValidation('Curso', new TRequiredValidator);
        $codigo->addValidation('Identificação', new TRequiredValidator);
        $nome->addValidation('Nome da categoria', new TRequiredValidator);


        // set sizes
        $codigo->forceUpperCase();
        $codigo->setEditable(FALSE);


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Limpar campos', new TAction([$this, 'onClear']), 'fa:eraser red');

        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();            
        $this->datagrid->setGroupColumn('dados_curso_id', '<b>{diploma_digital_curso->nome_curso_diploma}</b>');


        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_dados_curso_id = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome da categoria', 'left');
        $column_codigo = new TDataGridColumn('codigo', 'Identificação', 'center');
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
        //$action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        //$action1->setLabel(_t('Edit'));
        //$action1->setImage('far:edit blue');
        //$action1->setField('id');
        
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        
        $action3 = new TDataGridAction([$this, 'onAtividadesRelacionadas']);
        //$action3->setUseButton(TRUE);
        //$action3->setButtonClass('btn btn-default');
        $action3->setLabel('Atividades associadas');
        $action3->setImage('fas: fa-list #008080');
        $action3->setField('id');
        
        
        // add the actions to the datagrid
        //$this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action3);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        $panel = new TPanelGroup('');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        
        $btn = $panel->addHeaderActionLink('Filtrar', new TAction([$this, 'onShowWindowFilters']), 'fa:filter');
        $btn->class = 'btn btn-primary';
        $panel->addHeaderActionLink('Limpar filtros', new TAction([$this, 'onClearFilters']), 'fa:eraser red');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }


    public static function onNomeCategoriaChange($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $curso = new DiplomaDigitalCurso($param['dados_curso_id']);
            
            $categorias = AtividadeComplementarCategoria::where('dados_curso_id', '=', $curso->id)->load();            
            
            if($categorias)
            {
                //Verifica se é novo registro ou edição
                if(empty($param['id']))
                {
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('dados_curso_id', '=', $curso->id));    
                    $criteria->setProperty('order', 'codigo', 'desc');
                    
                    $categoria_atividades = AtividadeComplementarCategoria::getObjects($criteria); 
                       
                    $ultimo = end($categoria_atividades);
                    $parts = explode('.', $ultimo->codigo);
                    $total = end($parts);
                        
                    $contador = $total + 1; //Último item + 1
                     
                    //A identificação (código) da categoria é formada pelo código do curso no Genesi + Nº dela na contagem de categorias do curso  
                    $obj->codigo = trim($curso->codigo_curso_sistema . "." . $contador); 
                }
                else
                {
                    $atividade_categoria = new AtividadeComplementarCategoria($param['id']);
                        
                    $obj->codigo = $atividade_categoria->codigo;    
                } 
            }
            else
            {
                $contador = '1'; //Inicia contador ao adicionar primeira categoria do curso
                
                //A identificação (código) da categoria é formada pelo código do curso no Genesi + Nº dela na contagem de categorias do curso
                $obj->codigo = trim($curso->codigo_curso_sistema . "." . $contador);
            }            
                         
            TForm::sendData('form_AtividadeComplementarCategoria', $obj);
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    
    
    public function onShowWindowFilters($param)
    {
        $search_form = new BootstrapFormBuilder('form_search_AtividadeComplementarCategoria');


        $dados_curso_id = new TEntry('dados_curso_id');
        $nome = new TEntry('nome');
        $codigo = new TEntry('codigo');

        
        $search_form->addFields( [ new TLabel('Curso') ], [ $dados_curso_id ] );
        $search_form->addFields( [ new TLabel('Categoria') ], [ $nome ] );
        $search_form->addFields( [ new TLabel('Identificação') ], [ $codigo ] );
        
        
        $dados_curso_id->setSize('100%');
        $nome->setSize('100%');
        $codigo->setSize('100%');


        $search_form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        

        $btn = $search_form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        
        $page = TWindow::create('Filtros', 600, null);
        $page->removePadding();            

        $embed = new AtividadeComplementarCategoria;            

        $page->add($search_form);
        $page->setIsWrapped(true);
        $page->show();
    }
    
    
    public static function closeWindow($param = null)
    {
        TWindow::closeWindow();
    }
    
    
    public static function onAtividadesRelacionadas($param)
    {
        try
        {
            $id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $atividade_categoria = new AtividadeComplementarCategoria($id);        
            
            TTransaction::close();
            
                            
            //Limpa variável para garantir integridade
            TSession::setValue('atividade_categoria', NULL);
            TSession::setValue('atividade_categoria', $atividade_categoria);            

            TApplication::loadPage('AtividadeComplementarCadastroFormList', 'onReload');
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

        TSession::setValue(__CLASS__.'_filter_dados_curso_id', NULL);
        TSession::setValue(__CLASS__.'_filter_codigo', NULL);
        TSession::setValue(__CLASS__.'_filter_nome', NULL);


        if (isset($data->dados_curso_id) AND ($data->dados_curso_id)) {
            $filter = new TFilter('(SELECT nome_curso_diploma FROM dados_curso WHERE id=atividade_complementar_categoria.dados_curso_id)', 'like', "%{$data->dados_curso_id}%");
            TSession::setValue(__CLASS__.'_filter_dados_curso_id',   $filter); 
        }


        if (isset($data->codigo) AND ($data->codigo)) {
            $filter = new TFilter('codigo', 'like', "%{$data->codigo}%"); 
            TSession::setValue(__CLASS__.'_filter_codigo',   $filter); 
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); 
            TSession::setValue(__CLASS__.'_filter_nome',   $filter); 
        }

        
        $this->form->setData($data);

        TSession::setValue(__CLASS__ . '_filter_data', $data);
        

        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
        
        $data->dados_curso_id = '';
        $data->codigo = '';
        $data->nome = '';
        
        TForm::sendData('form_search_AtividadeComplementarCategoria', $data);
    }
    
    
    public function onClearFilters($param)
    {
        TSession::setValue(__CLASS__.'_filter_dados_curso_id', NULL);
        TSession::setValue(__CLASS__.'_filter_codigo', NULL);
        TSession::setValue(__CLASS__.'_filter_nome', NULL);
        
        $this->onReload();
    }


    public function onReload($param = NULL)
    {
        try
        {
            $unit_id = TSession::getValue('userunitid');
            
            TTransaction::open('Felabs_DB');         

            //1º Filtra o ID dos cursos da unidade logada
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
            
            $repository_curso = new TRepository('DiplomaDigitalCurso');
            $cursos = $repository_curso->load($criteria_curso);
            
            foreach($cursos as $curso)
            {
                $ids_cursos[] = $curso->id;
            }
            

            //2º Filtra as categorias de atividade complementar dos cursos referentes à unidade logada 
            $repository = new TRepository('AtividadeComplementarCategoria');
            $limit = 50;
    
            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_curso_id', 'IN', $ids_cursos));
               
            if (empty($param['order']))
            {
                $param['order'] = 'dados_curso_id, codigo';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue(__CLASS__.'_filter_dados_curso_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_dados_curso_id')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_codigo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_codigo')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome')); 
            }
            
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
    

    public static function onDelete($param)
    {
        try
        {
            $action = new TAction([__CLASS__, 'Delete']);
            $action->setParameters($param); 
            
            TTransaction::open('Felabs_DB');
            
            $key = $param['key'];        
            $categoria = new AtividadeComplementarCategoria($key);
            
            //Opção 1: Verifica se há categorias lançadas e, se houver, não permite a exclusão
            if((AtividadeComplementar::where('categoria_atividade_id', '=', $atividade->id)->count() > 0) OR
               (AtividadeComplementarCadastro::where('categoria_id', '=', $categoria->id)->count() > 0) OR
               (CurriculoAtividadeCategoria::where('atividade_complementar_categoria_id', '=', $categoria->id)->count() > 0) OR
               (CurriculoAtividadeCadastro::where('atividade_complementar_categoria_id', '=', $categoria->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) a categoria');
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
            
            $object = new AtividadeComplementarCategoria($key, FALSE); 
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
            
            $object = new AtividadeComplementarCategoria;  
            $object->fromArray( (array) $data); 
            
            //Se está salvando um novo registro
            if(empty($data->id))
            {
                //E já existe a mesma categoria no curso, não deixa salvar
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('dados_curso_id', '=', $data->dados_curso_id)); //importante!
                $criteria1->add(new TFilter('codigo', '=', trim($data->codigo)));
                
                $criteria2 = new TCriteria;
                $criteria2->add(new TFilter('dados_curso_id', '=', $data->dados_curso_id)); //importante!
                $criteria2->add(new TFilter('nome', '=', trim($data->nome)));
                
                $criteria3 = new TCriteria;
                $criteria3->add($criteria1, TExpression::OR_OPERATOR);
                $criteria3->add($criteria2, TExpression::OR_OPERATOR);
                    
                $repository = new TRepository('AtividadeComplementarCategoria'); 
                $registros_bd = $repository->load($criteria3);
                    
                if ($registros_bd)
                {
                    throw new Exception("Esta categoria já foi cadastrada neste curso");
                }
            }
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); 
            
            //Limpa o formulário depois de salvar
            $this->form->clear();
            
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
    

    /*public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new AtividadeComplementarCategoria($key);
                
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
    }*/
    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
