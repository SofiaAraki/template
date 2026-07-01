<?php

class ReqBolsaAlunoListGestor extends TPage
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
        $this->form = new TQuickForm('form_search_Aluno');
        $this->form->class = 'tform';
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%';
        $this->form->setFormTitle('Aluno');
        
        
        // create the form fields
        $data_inicial = new TDate('data_inicial');
        $data_final = new TDate('data_final');
        $nome = new TEntry('nome');
        $unidade = new TEntry('unidade');
        $curso = new TEntry('curso');        
        $ciclo = new TEntry('ciclo');
        $situacao = new TEntry('situacao');
        

        // add the fields
        $this->form->addQuickFields('Data inicial', array($data_inicial, new TLabel('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), $lbl_1 = new TLabel('Data final'), $data_final));
        $this->form->addQuickField('Aluno', $nome,  '80%' );
        $this->form->addQuickField('Unidade', $unidade, '80%' );
        $this->form->addQuickField('Curso', $curso, '80%' );
        $this->form->addQuickField('Ciclo', $ciclo, '80%' );
        $this->form->addQuickField('Situação', $situacao,  '80%' );        
        
        //propriedades
        $data_inicial->setMask('dd/mm/yyyy');
        $data_final->setMask('dd/mm/yyyy');
        $data_inicial->setSize(150);
        $data_final->setSize(150);
        $lbl_1->setSize(90);
        $lbl_1->setFontStyle('b');
        
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Aluno_filter_data') );
        
        
        // add the search form actions
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search blue');
                
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        //$this->datagrid->enablePopover(('Resumo'), '<b>'.('Parecer Técnico do(a) Assistente Social').'</b><br>' . '{obs}');
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');
        $column_nome = new TDataGridColumn('nome', 'Aluno', 'left');
        $column_unidade = new TDataGridColumn('system_unit', 'Unidade', 'center');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_ciclo = new TDataGridColumn('ciclo', 'Ciclo', 'left');
        $column_situacao = new TDataGridColumn('situacao', 'Situação', 'left');
        $column_situacao->setTransformer( array($this, 'setStatusColor') );


        //$column_data_reg->setTransformer(array($this, 'formatDate'));


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_ciclo);
        $this->datagrid->addColumn($column_situacao);

        
        $action_visualizar = new TDataGridAction(array('AnaliseRequerimentoBolsa', 'onEdit'));
        $action_visualizar->setButtonClass('btn btn-default btn-sm');
        $action_visualizar->setLabel('Visualizar');
        $action_visualizar->setImage('fa:id-card fa-lg');
        $action_visualizar->setField('id');
        $this->datagrid->addAction($action_visualizar);
      
        
        //create EDIT action
        // $action_edit = new TDataGridAction(array('ParecerFormView', 'onPrint'));
        // //$action_edit->setUseButton(TRUE);
        // //$action_edit->setButtonClass('btn btn-default');
        // $action_edit->setLabel('Imprimir');
        // $action_edit->setImage('fa:print blue fa-lg');
        // $action_edit->setField('id');
        // $this->datagrid->addAction($action_edit);
        

        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Analisar', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        
        parent::add($container);
    }


    public function setStatusColor($column_situacao, $object, $row)
    {
        $color = $object->situacao;
        
        if ($color == "Aberto")
        {
            return '<span class="label label-default">' . $column_situacao . '</span>';
        }
        else if ($color == "Em Análise")
        {
            return '<span class="label label-warning">' . $column_situacao . '</span>';
        }
        else if ($color == "Aguardando assinaturas")
        {
            return '<span class="label label-info">' . $column_situacao . '</span>';
        }
        else if ($color == "Solicitar correção")
        {
            return '<span class="label label-primary">' . $column_situacao . '</span>';
        }
         else if ($color == "Deferido")
        {
            return '<span class="label label-success">' . $column_situacao . '</span>';
        }       
        else if ($color == "Indeferido")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }
        else if ($color == "Indevido")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }
        else if ($color == "Desclassificado")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }
        else if ($color == "Reprovado")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }
        else
        {
            return $column_situacao;
        }  
    }


    public function displayColumnDownload( $object )
    {
        if (strlen($object->filename)>1)
        {
         //   var_dump(strlen($object->filename));
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
                $id = $param['id'];  // get the parameter $key
                
                TTransaction::open('Felabs_DB'); // open a transaction
                
                $object = new ReqBolsaAluno($id); // instantiates the Active Record
                
               // if ($object->system_user_id == TSession::getValue('userid') OR TSession::getValue('login') === 'admin')
               // {
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


    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    

    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        
        // clear session filters
        TSession::setValue('AlunoList_filter_data_inicial', NULL);
        TSession::setValue('AlunoList_filter_data_final', NULL);
        TSession::setValue('AlunoList_filter_nome', NULL);
        TSession::setValue('AlunoList_filter_curso', NULL);
        TSession::setValue('AlunoList_filter_ciclo', NULL);
        TSession::setValue('AlunoList_filter_periodo', NULL);
        TSession::setValue('AlunoList_filter_cidade', NULL);
        TSession::setValue('AlunoList_filter_situacao', NULL);
        TSession::setValue('AlunoList_filter_unidade', NULL);


        
        if ($data->data_inicial){
            
            $data->data_inicial = TDate::date2us($data->data_inicial);
            
            $filter = new TFilter('data_reg', '>=', $data->data_inicial); // create the filter
            TSession::setValue('AlunoList_filter_data_inicial',   $filter); // stores the filter in the session
            
            $data->data_inicial = TDate::date2br($data->data_inicial);
        }


        if ($data->data_final){
        
            $data->data_final = TDate::date2us($data->data_final);
            
            $filter = new TFilter('data_reg', '<=', $data->data_final); // create the filter
            TSession::setValue('AlunoList_filter_data_final',   $filter); // stores the filter in the session
            
            $data->data_final = TDate::date2br($data->data_final);
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue('AlunoList_filter_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->curso) AND ($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); // create the filter
            TSession::setValue('AlunoList_filter_curso',   $filter); // stores the filter in the session
        }


        if (isset($data->ciclo) AND ($data->ciclo)) {
            $filter = new TFilter('ciclo', 'like', "%{$data->ciclo}%"); // create the filter
            TSession::setValue('AlunoList_filter_ciclo',   $filter); // stores the filter in the session
        }

        if (isset($data->situacao) AND ($data->situacao)) {
            $filter = new TFilter('situacao', 'like', "%{$data->situacao}%"); // create the filter
            TSession::setValue('AlunoList_filter_situacao',   $filter); // stores the filter in the session
        }
        
        /*if (isset($data->situacao) AND ($data->situacao)) {
            $filter = new TFilter('situacao', '=', $data->situacao); // create the filter
            TSession::setValue('AlunoList_filter_situacao',   $filter); // stores the filter in the session
        }*/
        
        if ($data->unidade) {
            $filter = new TFilter('(SELECT name from system_unit WHERE system_unit_codlegado=req_bolsa_aluno.unidade)', 'like', "%{$data->unidade}%"); // create the filter
            TSession::setValue('AlunoList_filter_unidade',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Aluno_filter_data', $data);
        
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

            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            
            $repository = new TRepository('ReqBolsaAluno');
            $limit = 15;

            $criteria = new TCriteria;

            //$criteria->add(new TFilter('system_user_id', '=', $logged->id));
            //$criteria->add(new TFilter('situacao', 'like', 'Aberto%'), TExpression::OR_OPERATOR);
            //$criteria->add(new TFilter('situacao', 'like', 'Em análise%'), TExpression::OR_OPERATOR);
            //$criteria->add(new TFilter('situacao', 'like', 'Solicitar alteração%'), TExpression::OR_OPERATOR);
            
            
            if (empty($param['order']))
            {
                $param['order'] = 'situacao';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AlunoList_filter_data_inicial')) {
                $criteria->add(TSession::getValue('AlunoList_filter_data_inicial'));
            }
            
            
            if (TSession::getValue('AlunoList_filter_data_final')) {
                $criteria->add(TSession::getValue('AlunoList_filter_data_final'));
            }


            if (TSession::getValue('AlunoList_filter_nome')) {
                $criteria->add(TSession::getValue('AlunoList_filter_nome'));
            }


            if (TSession::getValue('AlunoList_filter_curso')) {
                $criteria->add(TSession::getValue('AlunoList_filter_curso'));
            }


            if (TSession::getValue('AlunoList_filter_ciclo')) {
                $criteria->add(TSession::getValue('AlunoList_filter_ciclo'));
            }


            if (TSession::getValue('AlunoList_filter_periodo')) {
                $criteria->add(TSession::getValue('AlunoList_filter_periodo'));
            }


            if (TSession::getValue('AlunoList_filter_cidade')) {
                $criteria->add(TSession::getValue('AlunoList_filter_cidade'));
            }


            if (TSession::getValue('AlunoList_filter_situacao')) {
                $criteria->add(TSession::getValue('AlunoList_filter_situacao'));
            }
            
            if (TSession::getValue('AlunoList_filter_unidade')) {
                $criteria->add(TSession::getValue('AlunoList_filter_unidade'));
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
            $count= $repository->count($criteria);
            
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


