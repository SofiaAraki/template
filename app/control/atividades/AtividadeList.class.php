<?php
class AtividadeList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_Atividade');      
        $this->form->setFormTitle('Página da Disciplina');

        TTransaction::open('dados_fei');

        $teste = TSession::getValue('sessao_prof');
        $disciplina = new FiDisciplina($teste['coddisciplina']);

        TTransaction::close();
       
        $label1 = new TLabel('Disciplina:', '', '15px', '');
        $text1  = new TTextDisplay($disciplina->Nomeusual, '', '15px', '');          

        $this->form->addFields([$label1],[$text1]);

        // create the form fields
        $id = new TEntry('id');
        $coddisciplina = new TEntry('coddisciplina');
        $codturmaetapa = new TEntry('codturmaetapa');
        $tipo = new TEntry('tipo');
        $nome = new TEntry('nome');
        $descricao = new TEntry('descricao');
        $anexo = new TEntry('anexo');
        $valor_nota = new TEntry('valor_nota');
        $data_prazo = new TEntry('data_prazo');
        $data_reg = new TEntry('data_reg');
        $system_user_id = new TEntry('system_user_id');
        $ordem = new TEntry('ordem');

        TTransaction::open('Felabs_DB');

        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);

        if($user->funcao_legado == "Professor") //PROF
        {
            $this->form->addAction('Voltar', new TAction(['AtividadeProfessorDisciplinasList', 'onReload']), 'fa:arrow-left blue');
            $this->form->addAction('Adicionar Conteudo', new TAction(['AtividadeForm', 'mostrar']), 'fa:plus green');
        }
        elseif($user->funcao_legado == "Aluno") //ALUNO
        {
            $this->form->addAction('Voltar', new TAction(['AtividadeAlunoDisciplinasList', 'onReload']), 'fa:arrow-left blue');
        }
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_coddisciplina = new TDataGridColumn('coddisciplina', 'Coddisciplina', 'left');
        $column_codturmaetapa = new TDataGridColumn('codturmaetapa', 'Codturmaetapa', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_anexo = new TDataGridColumn('anexo', 'Anexo', 'left');
        $column_valor_nota = new TDataGridColumn('valor_nota', 'Valor Nota', 'left');
        $column_data_prazo = new TDataGridColumn('data_prazo', 'Data Prazo', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do Registro', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'System User Id', 'left');
        $column_ordem = new TDataGridColumn('ordem', 'Ordem', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_coddisciplina);
        //$this->datagrid->addColumn($column_codturmaetapa);        
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_tipo);
        //$this->datagrid->addColumn($column_descricao);
        //$this->datagrid->addColumn($column_anexo);
        //$this->datagrid->addColumn($column_valor_nota);
        //$this->datagrid->addColumn($column_data_prazo);
        $this->datagrid->addColumn($column_data_reg);
        //$this->datagrid->addColumn($column_system_user_id);
        //$this->datagrid->addColumn($column_ordem);

        // create VER ATIVIDADE action
        $action_verativ = new TDataGridAction(array($this, 'goAtividadeAlunoList'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_verativ->setLabel('Ver Atividade');
        $action_verativ->setImage('far:folder-open green fa-lg');
        $action_verativ->setField('id');
        $this->datagrid->addAction($action_verativ);


        if($user->funcao_legado == "Professor") //PROF
        {        
            // create EDIT action
            $action_edit = new TDataGridAction(array('AtividadeForm', 'onEdit'));
            //$action_edit->setUseButton(TRUE);
            //$action_edit->setButtonClass('btn btn-default');
            $action_edit->setLabel(_t('Edit'));
            $action_edit->setImage('far:edit blue fa-lg');
            $action_edit->setField('id');
            $this->datagrid->addAction($action_edit);
    
            // create DELETE action
            $action_del = new TDataGridAction(array($this, 'onDelete'));
            //$action_del->setUseButton(TRUE);
            //$action_del->setButtonClass('btn btn-default');
            $action_del->setLabel(_t('Delete'));
            $action_del->setImage('far:trash-alt red fa-lg');
            $action_del->setField('id');
            $this->datagrid->addAction($action_del);
        }


        TTransaction::close();


        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        

        $container = new TVBox;
        $container->style = 'width: 100%';

        if ($user->funcao_legado == 'Professor' ) //PROF
        {
            $container->add(new TXMLBreadCrumb('menu.xml', 'AtividadeProfessorDisciplinasList'));
           
        }
        elseif ($user->funcao_legado == 'Aluno' ) //ALUNO
        {
            $container->add(new TXMLBreadCrumb('menu.xml', 'AtividadeAlunoDisciplinasList'));
        }

        $container->add($this->form);
        $container->add(TPanelGroup::pack('Conteúdo da Disciplina', $this->datagrid));
        
        parent::add($container);
    }


    public function goAtividadeAlunoList($param)
    {
        TSession::setValue('atividadeid',$param['key']);


        TTransaction::open('Felabs_DB');
      
        $ativInfo = new Atividade($param['key']);

        if($ativInfo->tipo != 3)
        {
            TApplication::loadPage('AtividadeAlunoList','onReload',$param);
        }
        else
        {
            TApplication::loadPage('AtividadeAlunoForum','onReload');
        }

      TTransaction::close();
    }
    

    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new Atividade($key); 
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
        

        TSession::setValue('AtividadeList_filter_id', NULL);
        TSession::setValue('AtividadeList_filter_coddisciplina', NULL);
        TSession::setValue('AtividadeList_filter_codturmaetapa', NULL);
        TSession::setValue('AtividadeList_filter_tipo', NULL);
        TSession::setValue('AtividadeList_filter_nome', NULL);
        TSession::setValue('AtividadeList_filter_descricao', NULL);
        TSession::setValue('AtividadeList_filter_anexo', NULL);
        TSession::setValue('AtividadeList_filter_valor_nota', NULL);
        TSession::setValue('AtividadeList_filter_data_prazo', NULL);
        TSession::setValue('AtividadeList_filter_data_reg', NULL);
        TSession::setValue('AtividadeList_filter_system_user_id', NULL);
        TSession::setValue('AtividadeList_filter_ordem', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); 
            TSession::setValue('AtividadeList_filter_id',   $filter); 
        }


        if (isset($data->coddisciplina) AND ($data->coddisciplina)) {
            $filter = new TFilter('coddisciplina', 'like', "%{$data->coddisciplina}%"); 
            TSession::setValue('AtividadeList_filter_coddisciplina',   $filter); 
        }


        if (isset($data->codturmaetapa) AND ($data->codturmaetapa)) {
            $filter = new TFilter('codturmaetapa', 'like', "%{$data->codturmaetapa}%"); 
            TSession::setValue('AtividadeList_filter_codturmaetapa',   $filter); 
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', 'like', "%{$data->tipo}%"); 
            TSession::setValue('AtividadeList_filter_tipo',   $filter); 
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); 
            TSession::setValue('AtividadeList_filter_nome',   $filter); 
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); 
            TSession::setValue('AtividadeList_filter_descricao',   $filter); 
        }


        if (isset($data->anexo) AND ($data->anexo)) {
            $filter = new TFilter('anexo', 'like', "%{$data->anexo}%"); 
            TSession::setValue('AtividadeList_filter_anexo',   $filter); 
        }


        if (isset($data->valor_nota) AND ($data->valor_nota)) {
            $filter = new TFilter('valor_nota', 'like', "%{$data->valor_nota}%"); 
            TSession::setValue('AtividadeList_filter_valor_nota',   $filter); 
        }


        if (isset($data->data_prazo) AND ($data->data_prazo)) {
            $filter = new TFilter('data_prazo', 'like', "%{$data->data_prazo}%"); 
            TSession::setValue('AtividadeList_filter_data_prazo',   $filter); 
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('AtividadeList_filter_data_reg',   $filter); 
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', 'like', "%{$data->system_user_id}%"); 
            TSession::setValue('AtividadeList_filter_system_user_id',   $filter); 
        }


        if (isset($data->ordem) AND ($data->ordem)) {
            $filter = new TFilter('ordem', 'like', "%{$data->ordem}%"); 
            TSession::setValue('AtividadeList_filter_ordem',   $filter); 
        }

        
        $this->form->setData($data);
        
        TSession::setValue('Atividade_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $sessaoProf = TSession::getValue('sessao_prof');
            $repository = new TRepository('Atividade');
            $limit = 1000;

            $criteria = new TCriteria;
            $criteria->add( new TFilter('coddisciplina', '=', $sessaoProf['coddisciplina'])); //CODIGO DA DISCIPLINA
            $criteria->add( new TFilter('codturmaetapa', '=', $sessaoProf['codturmaetapa'])); //CÓDIGO DA MATRICULA DA ETAPA ATUAL 

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AtividadeList_filter_id')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_id')); 
            }


            if (TSession::getValue('AtividadeList_filter_coddisciplina')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_coddisciplina')); 
            }


            if (TSession::getValue('AtividadeList_filter_codturmaetapa')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_codturmaetapa')); 
            }


            if (TSession::getValue('AtividadeList_filter_tipo')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_tipo')); 
            }


            if (TSession::getValue('AtividadeList_filter_nome')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_nome')); 
            }


            if (TSession::getValue('AtividadeList_filter_descricao')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_descricao')); 
            }


            if (TSession::getValue('AtividadeList_filter_anexo')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_anexo')); 
            }


            if (TSession::getValue('AtividadeList_filter_valor_nota')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_valor_nota')); 
            }


            if (TSession::getValue('AtividadeList_filter_data_prazo')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_data_prazo')); 
            }


            if (TSession::getValue('AtividadeList_filter_data_reg')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_data_reg')); 
            }


            if (TSession::getValue('AtividadeList_filter_system_user_id')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_system_user_id')); 
            }


            if (TSession::getValue('AtividadeList_filter_ordem')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_ordem')); 
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
                    if($object->tipo == 1)
                    {
                        $object->tipo = 'Conteúdo';
                    }

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
            
            $object = new Atividade($key, FALSE); 
            $object->delete();
            
            TTransaction::close(); 
            
            $this->onReload( $param ); 
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
