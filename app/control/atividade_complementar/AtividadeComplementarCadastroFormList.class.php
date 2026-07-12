<?php

class AtividadeComplementarCadastroFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    
    protected $formDatagrid;
    private $categoria_id;
    private $nome;


    public function __construct( $param )
    {
        parent::__construct();
        
        
        //Para preenchimento do cabeçalho da datagrid e formulário
        $atividade_categoria = TSession::getValue('atividade_categoria');
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $curso = $atividade_categoria->diploma_digital_curso->nome_curso_diploma;
            $categoria = $atividade_categoria->nome;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('id', '=', $atividade_categoria->id));
                
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } 

        
        $this->form = new BootstrapFormBuilder('form_AtividadeComplementarCadastro');
        $this->form->setFormTitle('<h4>Cadastro de Atividades Complementares</h4>');
        

        // create the form fields
        $id = new THidden('id');
        $codigo = new TEntry('codigo');
        $this->nome = new TCombo('nome');
        $descricao = new TText('descricao');
        $this->categoria_id = new TDBCombo('categoria_id', 'Felabs_DB', 'AtividadeComplementarCategoria', 'id', 'nome', 'nome', $criteria);
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        //Nome de atividades        
        $combo_nome['Acompanhamento de triagem na OAB'] = "Acompanhamento de triagem na OAB";
        $combo_nome['Acompanhamento técnico supervisionado'] = "Acompanhamento técnico supervisionado";
        //$combo_nome['Análise de autos findos - Processo civil'] = "Análise de autos findos - Processo civil";
        //$combo_nome['Análise de autos findos - Processo criminal'] = "Análise de autos findos - Processo criminal";
        //$combo_nome['Análise de autos findos - Processo trabalhista'] = "Análise de autos findos - Processo trabalhista";
        //$combo_nome['Análise de autos findos - Processo previdenciário'] = "Análise de autos findos - Processo previdenciário";        
        $combo_nome['Análise de autos findos'] = "Análise de autos findos"; //(Direito) As análises que ultrapassarem o nº mínimo de horas exigido para ES podem ser convertidas em AC               
        $combo_nome['Apresentação de trabalho em evento'] = "Apresentação de trabalho em evento";        
        $combo_nome['Atividade de extensão acadêmica e comunitária'] = "Atividade de extensão acadêmica e comunitária";
        $combo_nome['Atividade de pesquisa'] = "Atividade de pesquisa";        
        $combo_nome['Atividade extracurricular realizada no Hospital Veterinário'] = "Atividade extracurricular realizada no Hospital Veterinário";    
        $combo_nome['Curso de língua estrangeira'] = "Curso de língua estrangeira";
        $combo_nome['Curso de língua portuguesa'] = "Curso de língua portuguesa";
        $combo_nome['Curso pertinente à área'] = "Curso pertinente à área";
        $combo_nome['Dia da Responsabilidade Social'] = "Dia da Responsabilidade Social";
        $combo_nome['Estágio de prática real'] = "Estágio de prática real";        
        $combo_nome['Exercício de monitoria'] = "Exercício de monitoria";
        $combo_nome['Experiência profissional comprovada'] = "Experiência profissional comprovada";
        $combo_nome['Fichamento de obras'] = "Fichamento de obras";
        $combo_nome['Ministração de palestra'] = "Ministração de palestra"; //(Direito) A ministração de palestra que ultrapassar o nº mínimo de horas exigido para ES pode ser convertida em AC
        $combo_nome['Participação como mesário nas eleições'] = "Participação como mesário nas eleições";        
        //$combo_nome['Participação em audiência civil'] = "Participação em audiência civil";
        //$combo_nome['Participação em audiência criminal'] = "Participação em audiência criminal";
        //$combo_nome['Participação em audiência na Justiça Federal'] = "Participação em audiência na Justiça Federal";
        //$combo_nome['Participação em audiência trabalhista'] = "Participação em audiência trabalhista";         
        $combo_nome['Participação em ATPC'] = "Participação em ATPC";
        $combo_nome['Participação em audiência'] = "Participação em audiência"; //(Direito) As audiências que ultrapassarem o nº mínimo de horas exigido para ES podem ser convertidas em AC                  
        $combo_nome['Participação em curso de extensão ou aperfeiçoamento'] = "Participação em curso de extensão ou aperfeiçoamento";
        $combo_nome['Participação em curso de nível acadêmico em geral'] = "Participação em curso de nível acadêmico em geral";
        $combo_nome['Participação em disciplina de outro curso relacionada à área'] = "Participação em disciplina de outro curso relacionada à área";
        $combo_nome['Participação em evento'] = "Participação em evento";
        $combo_nome['Participação em grupos de pesquisa'] = "Participação em grupos de pesquisa";                
        $combo_nome['Participação em júri real'] = "Participação em júri real"; //(Direito) Os júris que ultrapassarem o nº mínimo de horas exigido para ES podem ser convertidos em AC   
        $combo_nome['Participação em júri simulado'] = "Participação em júri simulado"; //(Direito) Os júris que ultrapassarem o nº mínimo de horas exigido para ES podem ser convertidos em AC
        $combo_nome['Participação em programa de iniciação científica'] = "Participação em programa de iniciação científica";
        $combo_nome['Participação em treinamento pertinente à área'] = "Participação em treinamento pertinente à área";
        $combo_nome['Participação estudantil em colegiado'] = "Participação estudantil em colegiado";       
        $combo_nome['Produção e publicação de artigo científico'] = "Produção e publicação de artigo científico"; //(Direito) A publicação de artigo que ultrapassar o nº mínimo de horas exigido para ES pode ser convertida em AC
        $combo_nome['Participação voluntária em ações comunitárias, campanhas beneficentes ou projetos sociais'] = "Participação voluntária em ações comunitárias, campanhas beneficentes ou projetos sociais";
        $combo_nome['Presença em defesa de Trabalho de Conclusão de Curso'] = "Presença em defesa de Trabalho de Conclusão de Curso";        
        $combo_nome['Resenha de filme/documentário'] = "Resenha de filme/documentário";
        $combo_nome['Visita acompanhada a empresas, instituições ou órgãos ligados à área'] = "Visita acompanhada a empresas, instituições ou órgãos ligados à área";


        //Acrescentados posteriormente para cursos da FFCL
        $combo_nome['Atividade artística, inovação, empreendedorismo, ética, sustentabilidade ambiental'] = "Atividade artística, inovação, empreendedorismo, ética, sustentabilidade ambiental";
        $combo_nome['Atuação na diretoria do Diretório Acadêmico'] = "Atuação na diretoria do Diretório Acadêmico";
        $combo_nome['Curso de informática'] = "Curso de informática";
        $combo_nome['Organização de eventos'] = "Organização de eventos";  
        $combo_nome['Participação em Empresas Junior da FFCL'] = "Participação em Empresas Junior da FFCL";
        $combo_nome['Participação em oficinas e minicursos'] = "Participação em oficinas e minicursos";
        $combo_nome['Participação em semana de estudos do curso'] = "Participação em semana de estudos do curso";
        $combo_nome['Prestação de serviços voluntários ligados à área da educação'] = "Prestação de serviços voluntários ligados à área da educação";
        $combo_nome['Visita técnica na região de Ituverava'] = "Visita técnica na região de Ituverava";
        $combo_nome['Visita técnica fora da região de Ituverava'] = "Visita técnica fora da região de Ituverava"; 
        $combo_nome['Visita técnica fora da região de Ituverava'] = "Visita técnica fora da região de Ituverava";       
        
        $this->nome->addItems($combo_nome);
        

        //Preenche o código da atividade
        $this->nome->setChangeAction(new TAction(array($this, 'onAtividadeChange')));
              

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Categoria <font color="red">*</font>'), $this->categoria_id ],
                                       [ new TLabel('Nome da atividade <font color="red">*</font>'), $this->nome ],
                                       [ new TLabel('Identificação <font color="red">*</font>'), $codigo ]);
        $row->layout = ['col-sm-5', 'col-sm-5', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Descrição'), $descricao ]);
        $row->layout = ['col-sm-12'];
        
          
        $this->categoria_id->addValidation('Categoria', new TRequiredValidator); 
        $this->nome->addValidation('Nome da atividade', new TRequiredValidator);     
        $codigo->addValidation('Identificação', new TRequiredValidator);       
        

        // set sizes
        $this->categoria_id->setSize('100%');
        $this->categoria_id->setValue($atividade_categoria->id);
        $this->nome->setSize('100%');
        $codigo->setSize('100%'); 
        $codigo->setEditable(FALSE);       
        $descricao->setSize('100%');        


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar campos', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(array('AtividadeComplementarCategoriaFormList','onReload')), 'fas:arrow-alt-circle-left blue');
        

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        //$this->datagrid->setGroupColumn('categoria_id', '<b>{atividade_complementar_categoria->nome}</b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_categoria_id = new TDataGridColumn('atividade_complementar_categoria->nome', 'Categoria', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome da atividade', 'left');
        $column_codigo = new TDataGridColumn('codigo', 'Identificação', 'center');        
        //$column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');        
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_categoria_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_codigo);        
        //$this->datagrid->addColumn($column_descricao);        
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
        
        
        $panel = new TPanelGroup("<b>$curso - $categoria</b>");
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


    public static function onAtividadeChange($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');            
            
            if ((!empty($param['categoria_id'])) AND (!empty($param['nome'])))
            {
                $categoria_id = $param['categoria_id'];
                
                $atividade_categoria = new AtividadeComplementarCategoria($categoria_id);
                  
                $atividade_complementares = AtividadeComplementarCadastro::where('categoria_id', '=', $atividade_categoria->id)->load();
                
                //Traz as atividades referentes à categoria
                if($atividade_complementares)
                {
                    //Verifica se é novo registro ou edição
                    if(empty($param['id']))
                    {
                        $criteria = new TCriteria;
                        $criteria->add(new TFilter('categoria_id', '=', $atividade_categoria->id));    
                        $criteria->setProperty('order', 'id', 'desc');
                    
                        $cadastro_atividades = AtividadeComplementarCadastro::getObjects($criteria); 
                        
                        $ultimo = end($cadastro_atividades);
                        $parts = explode('.', $ultimo->codigo);
                        $total = end($parts);
                        
                        $contador = $total + 1; //Último item + 1
                        
                        $obj->codigo = $atividade_categoria->codigo . "." . $contador;
                    }
                    else
                    {
                        $atividade_complementar = new AtividadeComplementarCadastro($param['id']);
                        
                        $obj->codigo = $atividade_complementar->codigo;    
                    }
                }  
                else
                {
                    $contador = '1'; //Inicia contador ao adicionar a primeira atividade da categoria do curso
                    
                    $obj->codigo = $atividade_categoria->codigo . "." . $contador;
                }
                                              
                TForm::sendData('form_AtividadeComplementarCadastro', $obj);
            }
            else
            {
                $obj = new StdClass;
                $obj->codigo = '';
                
                TForm::sendData('form_AtividadeComplementarCadastro', $obj);
            }
            
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
        $search_form = new BootstrapFormBuilder('form_search_AtividadeComplementarCadastro');


        $this->categoria_id = new TEntry('categoria_id');
        $this->nome = new TEntry('nome');
        $codigo = new TEntry('codigo');

        
        $search_form->addFields( [ new TLabel('Categoria') ], [ $this->categoria_id ] );
        $search_form->addFields( [ new TLabel('Atividade') ], [ $this->nome ] );
        $search_form->addFields( [ new TLabel('Identificação') ], [ $codigo ] );
        
        
        $this->categoria_id->setSize('100%');
        $this->nome->setSize('100%');
        $codigo->setSize('100%');


        $search_form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        

        $search_form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        
        
        $page = TWindow::create('Filtros', 600, null);
        $page->removePadding();            

        $embed = new AtividadeComplementarCadastro;            

        $page->add($search_form);
        $page->setIsWrapped(true);
        $page->show();
    }
    
    
    public static function closeWindow($param = null)
    {
        TWindow::closeWindow();
    }
    
    
    public function onSearch()
    {
        $data = $this->form->getData();        

        TSession::setValue(__CLASS__.'_filter_categoria_id', NULL);
        TSession::setValue(__CLASS__.'_filter_codigo', NULL);
        TSession::setValue(__CLASS__.'_filter_nome', NULL);


        if (isset($data->categoria_id) AND ($data->categoria_id)) { 
            $filter = new TFilter('(SELECT nome FROM atividade_complementar_categoria WHERE id=atividade_complementar_cadastro.categoria_id)', 'like', "%{$data->categoria_id}%");
            TSession::setValue(__CLASS__.'_filter_categoria_id',   $filter); 
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
        
        $data->categoria_id = '';
        $data->codigo = '';
        $data->nome = '';
        
        TForm::sendData('form_search_AtividadeComplementarCadastro', $data);
    }
    
    
    public function onClearFilters($param)
    {
        TSession::setValue(__CLASS__.'_filter_categoria_id', NULL);
        TSession::setValue(__CLASS__.'_filter_codigo', NULL);
        TSession::setValue(__CLASS__.'_filter_nome', NULL);
        
        $this->onReload();
    }
       
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('AtividadeComplementarCadastro');
            $limit = 15;
  
            $atividade_categoria = TSession::getValue('atividade_categoria');
                        
            $criteria = new TCriteria;
            $criteria->add(new TFilter('categoria_id', '=', $atividade_categoria->id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_categoria_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_categoria_id')); 
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
            $atividade = new AtividadeComplementarCadastro($key);
            
            //Opção 1: Verifica se há atividades lançadas e, se houver, não permite a exclusão
            if((AtividadeComplementar::where('cadastro_atividade_id', '=', $atividade->id)->count() > 0) OR
               (CurriculoAtividadeCadastro::where('atividade_complementar_cadastro_id', '=', $atividade->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) a atividade');
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
            
            $object = new AtividadeComplementarCadastro($key, FALSE); 
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
            
            $object = new AtividadeComplementarCadastro;  
            $object->fromArray( (array) $data); 
            
            //Se está salvando um novo registro
            if(empty($data->id))
            {
                $categoria = new AtividadeComplementarCategoria($object->categoria_id);
                $dados_curso_id = $categoria->dados_curso_id;
                
                //E já existe uma atividade igual e da mesma categoria no curso
                $criteria1 = new TCriteria; 
                $criteria1->add(new TFilter('nome', '=', $object->nome));
                $criteria1->add(new TFilter('categoria_id', '=', $object->categoria_id));
                $criteria1->add(new TFilter('(SELECT dados_curso_id FROM atividade_complementar_categoria WHERE id=atividade_complementar_cadastro.categoria_id)', '=', $dados_curso_id));
                
                
                //Ou existe uma atividade igual, mas de outra categoria no curso, não deixa salvar
                $criteria2 = new TCriteria; 
                $criteria2->add(new TFilter('nome', '=', $object->nome));
                $criteria2->add(new TFilter('categoria_id', '<>', $object->categoria_id));
                $criteria2->add(new TFilter('(SELECT dados_curso_id FROM atividade_complementar_categoria WHERE id=atividade_complementar_cadastro.categoria_id)', '=', $dados_curso_id));
                
                
                $criteria3 = new TCriteria;
                $criteria3->add($criteria1, TExpression::OR_OPERATOR);
                $criteria3->add($criteria2, TExpression::OR_OPERATOR);
                
                $repository = new TRepository('AtividadeComplementarCadastro'); 
                $registros_bd = $repository->load($criteria3);
                    
                if ($registros_bd)
                {
                    throw new Exception("Já existe uma atividade com este mesmo nome para este curso. Por favor, verifique entre as demais categorias");
                }
            }
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); 
            
            //Limpa o formulário depois de salvar, mas mantém a categoria preenchida
            $this->form->clear();
            
            $obj = new StdClass;
            $obj->categoria_id = $object->categoria_id;
                    
            TForm::sendData('form_AtividadeComplementarCadastro', $obj);
                   
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
                
                $object = new AtividadeComplementarCadastro($key); 
                
                $this->categoria_id->setEditable(FALSE);
                $this->nome->setEditable(FALSE);
                                
                $this->form->setData($object); 
                
                TTransaction::close();
                
                $this->fireEvents( $object ); 
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
    
    
    public function fireEvents( $object )
    {
        $obj = new stdClass;
        $obj->categoria_id = $object->categoria_id;
        $obj->nome = $object->nome;
        
        TForm::sendData('form_AtividadeComplementarCadastro', $obj);
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
