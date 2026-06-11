<?php

class EstagioPendenteProfessorList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_Estagio');
        $this->form->setFormTitle('<h4>Estágios para Análise</h4>');
        

        // create the form fields
        $nome_aluno = new TEntry('nome_aluno');
        $nome_curso = new TEntry('nome_curso');
        $status_estagio = new TEntry('status_estagio');


        // add the fields
        $this->form->addFields( [ new TLabel('Aluno') ], [ $nome_aluno ] );
        $this->form->addFields( [ new TLabel('Curso') ], [ $nome_curso ] );
        $this->form->addFields( [ new TLabel('Status') ], [ $status_estagio ] );


        // set sizes
        $nome_aluno->setSize('80%');
        $nome_curso->setSize('80%');
        $status_estagio->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        $this->datagrid->setGroupColumn('nome_aluno', '<b>{nome_aluno} - HORAS PENDENTES: {CalcularHorasPendentes} horas</b>');
        

        // creates the datagrid columns
        $column_nome_aluno = new TDataGridColumn('nome_aluno', 'Aluno', 'left');
        $column_nome_curso = new TDataGridColumn('nome_curso', 'Curso', 'left');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'CH', 'center');
        $column_status_estagio = new TDataGridColumn('status_estagio', 'Status', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Registrado em', 'center');


        $column_status_estagio->setTransformer( array($this, 'setStatusColor') );


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_nome_aluno);
        $this->datagrid->addColumn($column_nome_curso);
        $this->datagrid->addColumn($column_carga_horaria);
        $this->datagrid->addColumn($column_status_estagio);
        $this->datagrid->addColumn($column_data_reg);


        $action_download = new TDataGridAction([$this, 'onDownload']);
        $action_download->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel('Download comprovante');
        $action_download->setImage('fas:cloud-download-alt blue');
        $action_download->setField('id');
        
        
        $action_analisar = new TDataGridAction(['EstagioAnaliseProfessorForm', 'onEdit']);
        $action_analisar->setUseButton(TRUE);
        $action_analisar->setButtonClass('btn btn-default');
        $action_analisar->setLabel('Analisar estágio');
        $action_analisar->setImage('fas:pencil-alt orange');
        $action_analisar->setField('id');
        
        
        $this->datagrid->addAction($action_download);
        $this->datagrid->addAction($action_analisar);
        
        
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


    public function setStatusColor($column_status_estagio, $object, $row)
    {
        $color = $object->status_estagio;
        
        if($color == "Aguardando aprovação")
        {
            return '<span class="label label-warning">' . $column_status_estagio . '</span>';
        }
        elseif($color == "Aprovado")
        {
            return '<span class="label label-success">' . $column_status_estagio . '</span>';
        }
        elseif($color == "Reprovado")
        {
            return '<span class="label label-danger">' . $column_status_estagio . '</span>';
        }
        else
        {
            return $column_status_estagio;
        }    
    }
    
    
    public static function onDownload($param)
    {
        try
        {
            $id = $param['id'];
                
            TTransaction::open('Felabs_DB');

            $object = new Estagio($id);

            if (strtolower(substr($object->arquivo, -4)) == 'html')
            {
                $win = TWindow::create( 'Arquivo', 0.8, 0.8 );
                $win->add( file_get_contents( $object->caminho_arquivo . '/' . $object->arquivo ) );
                $win->show();
            }
            else
            {
                TPage::openFile($object->caminho_arquivo . '/' . $object->arquivo);
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
        
        TSession::setValue(__CLASS__.'_filter_nome_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome_curso', NULL);
        TSession::setValue(__CLASS__.'_filter_status_estagio', NULL);

        if (isset($data->nome_aluno) AND ($data->nome_aluno)) {
            $filter = new TFilter('nome_aluno', 'like', "%{$data->nome_aluno}%"); 
            TSession::setValue(__CLASS__.'_filter_nome_aluno', $filter);
        }


        if (isset($data->nome_curso) AND ($data->nome_curso)) {
            $filter = new TFilter('nome_curso', 'like', "%{$data->nome_curso}%");
            TSession::setValue(__CLASS__.'_filter_nome_curso', $filter); 
        }


        if (isset($data->status_estagio) AND ($data->status_estagio)) {
            $filter = new TFilter('status_estagio', 'like', "%{$data->status_estagio}%");
            TSession::setValue(__CLASS__.'_filter_status_estagio', $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
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
            
            $unit_id = TSession::getValue('userunitid');            
            $user_id = TSession::getValue('userid');                    
            $user = new SystemUser($user_id);
                
            TTransaction::close();
            
            
            //Filtra os cursos da unidade logada
            TTransaction::open('dados_fei');
            
            $repository_curso = new TRepository('FiCurso');
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $cursos = $repository_curso->load($criteria_curso);

            foreach($cursos as $curso)
            {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            
            $professor = new FiProfessor($user->systemuser_codlegado);
            
            TTransaction::close();
            
            
            //Exibe só os estágios do professor logado na unidade correspondente
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('Estagio');
            $limit = 20;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_prof_responsavel', '=', $professor->Codprofessor));
            $criteria->add(new TFilter('cod_curso', 'IN', $items)); 
            $criteria->add(new TFilter('status_estagio', '=', "Aguardando aprovação"));
            
            
            if (empty($param['order']))
            {
                $param['order'] = 'nome_aluno';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_nome_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_aluno'));
            }


            if (TSession::getValue(__CLASS__.'_filter_nome_curso')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_curso')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_status_estagio')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status_estagio'));
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
                    $object->carga_horaria = (int) $object->carga_horaria;
                    
                    /*$row =*/ $this->datagrid->addItem($object);
                    
                    /*$data_inicio = TDate::date2br($object->data_inicio);
                    $data_termino = TDate::date2br($object->data_termino);
                    $horas = (int) $object->carga_horaria;
                    
                    if($object->razao_social_empresa <> NULL)
                    {
                        $concedente = $object->razao_social_empresa;
                    }
                    elseif($object->nome_pessoa_fisica <> NULL)
                    {
                        $concedente = $object->nome_pessoa_fisica;
                    }
                    else
                    {
                        $concedente = '';
                    }
                    
                    $row->popover = 'true';
                    $row->popside = 'top';
                    $row->popcontent = "<table class='popover-table'>
                                            <tr><td><b>Concedente</b></td><td>{$concedente}</td></tr>
                                            <tr><td><b>Descrição</b></td><td>{$object->descricao}</td></tr>
                                            <tr><td><b>Data de início</b></td><td>{$data_inicio}</td></tr>
                                            <tr><td><b>Data de término</b></td><td>{$data_termino}</td></tr>
                                            <tr><td><b>Etapa</b></td><td>{$object->etapa}</td></tr>
                                            <tr><td><b>Horas</b></td><td>{$horas}</td></tr>
                                        </table>";
                    $row->poptitle = 'Detalhes';*/
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