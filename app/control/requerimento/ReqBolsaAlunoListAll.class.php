<?php

class ReqBolsaAlunoListAll extends TPage
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
        $this->form->addQuickField('Aluno', $nome, '80%' );
        $this->form->addQuickField('Unidade', $unidade, '80%' );
        $this->form->addQuickField('Curso', $curso, '80%' );
        $this->form->addQuickField('Ciclo', $ciclo, '80%');
        $this->form->addQuickField('Situação', $situacao, '80%' );        
        
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
        $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$this->form->addQuickAction('Exportar Requerimentos',  new TAction(array($this, 'onExportCSV')), 'fa:table' );
        
        
        //Se admin, exporta CSV com dados do socioeconômico, se não, exporta CSV só com dados básicos
        $this->form->addQuickAction('Exportar Requerimentos', new TAction(array($this, 'onVerificaPermissao')), 'fa:table' );
        
        
        // creates a Datagrid
        /**$this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';*/


        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->width = '100%';
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


        $column_data_reg->setTransformer(array($this, 'formatDate'));


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_ciclo);
        $this->datagrid->addColumn($column_situacao);

        
        $action_visualizar = new TDataGridAction(array('ReqBolsaGestorFormView', 'onShow'));
        $action_visualizar->setButtonClass('btn btn-default btn-sm');
        $action_visualizar->setLabel('Visualizar');
        $action_visualizar->setImage('fa:search #478fca');
        $action_visualizar->setField('id');
        $this->datagrid->addAction($action_visualizar);


        $action_download = new TDataGridAction(array($this, 'downloadArquivo'));
        //$action_download->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel(_t('Download'));
        $action_download->setImage('fas:cloud-download-alt green fa-lg');
        $action_download->setField('id');
        $action_download->setDisplayCondition( array($this, 'displayColumnDownload') );
        $this->datagrid->addAction($action_download);

        
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
        $container->add(TPanelGroup::pack('Todos Requerimentos', $this->form));
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
        else if ($color == "Em análise")
        {
            return '<span class="label label-warning">' . $column_situacao . '</span>';
        }
        else if ($color == "Aguardando assinaturas")
        {
            return '<span class="label label-info">' . $column_situacao . '</span>';
        }
        else if ($color == "Solicitar alteração")
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
                $id = $param['id'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new ReqBolsaAluno($id);
                
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
        TSession::setValue('AlunoList_filter_nome',   NULL);
        TSession::setValue('AlunoList_filter_curso',   NULL);
        TSession::setValue('AlunoList_filter_ciclo',   NULL);
        TSession::setValue('AlunoList_filter_situacao',   NULL);
        TSession::setValue('AlunoList_filter_unidade',   NULL);


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


        /*if (isset($data->situacao) AND ($data->situacao)) {
            $filter = new TFilter('situacao', 'like', "%{$data->situacao}%"); // create the filter
            TSession::setValue('AlunoList_filter_situacao',   $filter); // stores the filter in the session
        }*/
        
        if (isset($data->situacao) AND ($data->situacao)) {
            $filter = new TFilter('situacao', '=', $data->situacao); // create the filter
            TSession::setValue('AlunoList_filter_situacao',   $filter); // stores the filter in the session
        }

        
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
            
            // creates a repository for Aluno
            $repository = new TRepository('ReqBolsaAluno');
            $limit = 10;
            
            // creates a criteria
            $criteria = new TCriteria;

            //$criteria->add(new TFilter('system_user_id', '=', $logged->id));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            
            if (TSession::getValue('AlunoList_filter_data_inicial')) {
                $criteria->add(TSession::getValue('AlunoList_filter_data_inicial'));
            }
            
            
            if (TSession::getValue('AlunoList_filter_data_final')) {
                $criteria->add(TSession::getValue('AlunoList_filter_data_final'));
            }
            

            if (TSession::getValue('AlunoList_filter_nome')) {
                $criteria->add(TSession::getValue('AlunoList_filter_nome')); // add the session filter
            }


            if (TSession::getValue('AlunoList_filter_curso')) {
                $criteria->add(TSession::getValue('AlunoList_filter_curso')); // add the session filter
            }


            if (TSession::getValue('AlunoList_filter_ciclo')) {
                $criteria->add(TSession::getValue('AlunoList_filter_ciclo')); // add the session filter
            }


            if (TSession::getValue('AlunoList_filter_situacao')) {
                $criteria->add(TSession::getValue('AlunoList_filter_situacao')); // add the session filter
            }
            
            if (TSession::getValue('AlunoList_filter_unidade')) {
                $criteria->add(TSession::getValue('AlunoList_filter_unidade')); // add the session filter
            }

            
            // load the objects according to criteria
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
    
    
    public function onVerificaPermissao()
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');
        $user_id = TSession::getValue('userid');
        
        
        if( in_array($grupo_admin, $user_groups))
        {
            $this->onExportSocioeconomicoCSV();
        }
        else
        {
            $this->onExportCSV();
        }
    }  
    
    
    //Administrador
    function onExportSocioeconomicoCSV()
    {
        $this->onSearch();

        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('ReqBolsaAluno');
            
            $criteria = new TCriteria;
            
            if (TSession::getValue('AlunoList_filter_data_inicial'))
            {
                $criteria->add(TSession::getValue('AlunoList_filter_data_inicial'));
            }
            
            if (TSession::getValue('AlunoList_filter_data_final'))
            {
                $criteria->add(TSession::getValue('AlunoList_filter_data_final'));
            }
            
            if (TSession::getValue('AlunoList_filter_unidade'))
            {
                $criteria->add(TSession::getValue('AlunoList_filter_unidade'));
            }
            
            if (TSession::getValue('AlunoList_filter_nome'))
            {
                $criteria->add(TSession::getValue('AlunoList_filter_nome'));
            }
            
            if (TSession::getValue('AlunoList_filter_curso'))
            {
                $criteria->add(TSession::getValue('AlunoList_filter_curso'));
            }
            
            if (TSession::getValue('AlunoList_filter_ciclo'))
            {
                $criteria->add(TSession::getValue('AlunoList_filter_ciclo'));
            }
            
            if (TSession::getValue('AlunoList_filter_situacao'))
            {
                $criteria->add(TSession::getValue('AlunoList_filter_situacao'));
            }
            
            $csv = '';
            
            $alunos = $repository->load($criteria);
            
            if ($alunos)
            {
                $csv .= utf8_decode('ID;CURSO;CICLO;ALUNO;RENDA FAMILIAR APURADA;Nº PESSOAS;RENDA PERCAPITA APURADA; ÍNDICE RENDA FAMILIAR; ÍNDICE RENDA PERCAPITA; DATA REGISTRO; SITUAÇÃO')."\n";
                
                foreach ($alunos as $aluno)
                {
                    //Formata data do registro
                    $hr = substr($aluno->data_reg, 11, 19);
                    $dt = TDate::date2br($aluno->data_reg);
                    $data_reg = "$dt" . " " . substr($hr,0,-7);
                    
                    $csv .= utf8_decode($aluno->id).';'.
                            utf8_decode($aluno->curso).';'.
                            utf8_decode($aluno->ciclo).';'.
                            utf8_decode($aluno->nome).';'.
                            utf8_decode(number_format($aluno->renda_familiar_apurada, 2, ',', '.')).';'.
                            utf8_decode($aluno->n_pessoas_apurado).';'.
                            utf8_decode(number_format($aluno->renda_percapita_apurada, 2, ',', '.')).';'.
                            utf8_decode(number_format($aluno->rf_salario_minimo_apurada, 2, ',', '.')).';'.
                            utf8_decode(number_format($aluno->rp_salario_minimo_apurada, 2, ',', '.')).';'.
                            utf8_decode($data_reg).';'.                                                     
                            utf8_decode($aluno->situacao)."\n";
                }                
                
                file_put_contents('app/output/requerimentos_bolsa.csv', $csv);
                TPage::openFile('app/output/requerimentos_bolsa.csv');
            }

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            
            TTransaction::rollback();
        }
    }
        
    
    function onExportCSV()
    {
        $this->onSearch();

        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('Felabs_DB');
            
            // creates a repository for Customer
            $repository = new TRepository('ReqBolsaAluno');
            
            // creates a criteria
            $criteria = new TCriteria;
            
            if (TSession::getValue('AlunoList_filter_data_inicial'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AlunoList_filter_data_inicial'));
            }
            
            if (TSession::getValue('AlunoList_filter_data_final'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AlunoList_filter_data_final'));
            }
            
            if (TSession::getValue('AlunoList_filter_unidade'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AlunoList_filter_unidade'));
            }
            
            if (TSession::getValue('AlunoList_filter_nome'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AlunoList_filter_nome'));
            }
            
            if (TSession::getValue('AlunoList_filter_curso'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AlunoList_filter_curso'));
            }
            
            if (TSession::getValue('AlunoList_filter_ciclo'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AlunoList_filter_ciclo'));
            }
            
            if (TSession::getValue('AlunoList_filter_situacao'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('AlunoList_filter_situacao'));
            }
            
            $csv = '';
            
            // load the objects according to criteria
            $alunos = $repository->load($criteria);
            
            if ($alunos)
            {
                $csv .= utf8_decode('ID;CURSO;CICLO;ALUNO;DATA REGISTRO;SITUAÇÃO')."\n";

                foreach ($alunos as $aluno)
                {
                    $csv .= utf8_decode($aluno->id).';'.
                            utf8_decode($aluno->curso).';'.
                            utf8_decode($aluno->ciclo).';'.
                            utf8_decode($aluno->nome).';'.
                            utf8_decode($aluno->data_reg).';'.                                                    
                            utf8_decode($aluno->situacao)."\n";
                }
                
                file_put_contents('app/output/requerimentos_bolsa.csv', $csv);
                TPage::openFile('app/output/requerimentos_bolsa.csv');
            }

            TTransaction::close();
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
