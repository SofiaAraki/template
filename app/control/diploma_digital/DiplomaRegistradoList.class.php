<?php

class DiplomaRegistradoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_DiplomaDigitalDiploma');
        $this->form->setFormTitle('<h4>Diplomas</h4>');
        

        // create the form fields
        $dados_diplomado_id = new TEntry('dados_diplomado_id');
        $dados_curso_id = new TEntry('dados_curso_id');
        $status_diploma = new TCombo('status_diploma');
        $status_publicacao = new TCombo('status_publicacao');
        

        //Status diploma 
        $combo_status_diploma = [];
        $combo_status_diploma[0] = "Inativo";
        $combo_status_diploma[1] = "Ativo";
        
        $status_diploma->addItems($combo_status_diploma);
        
        
        //Status publicado
        $combo_status_publicacao = [];
        $combo_status_publicacao[0] = "Não publicado";
        $combo_status_publicacao[1] = "Publicado";
                        
        $status_publicacao->addItems($combo_status_publicacao);


        // add the fields
        $this->form->addFields( [ new TLabel('Nome') ], [ $dados_diplomado_id ] );
        $this->form->addFields( [ new TLabel('Curso') ], [ $dados_curso_id ] );
        $this->form->addFields( [ new TLabel('Status diploma') ], [ $status_diploma ] );
        $this->form->addFields( [ new TLabel('Status publicação') ], [ $status_publicacao ] );


        // set sizes
        $dados_diplomado_id->setSize('80%');
        $dados_curso_id->setSize('80%');
        $status_diploma->setSize('80%');
        $status_publicacao->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_dados_diplomado_id = new TDataGridColumn('diploma_digital_diplomado->nome', 'Nome', 'left');
        $column_dados_curso_id = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'left');
        $column_status_diploma = new TDataGridColumn('status_diploma', 'Status Diploma', 'center');
        $column_status_publicacao = new TDataGridColumn('status_publicacao', 'Status Publicação', 'center');
        $column_data_publicacao = new TDataGridColumn('data_publicacao', 'Data Publicação', 'center');        
                

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dados_diplomado_id);
        $this->datagrid->addColumn($column_dados_curso_id);
        $this->datagrid->addColumn($column_status_diploma);
        $this->datagrid->addColumn($column_status_publicacao);
        $this->datagrid->addColumn($column_data_publicacao);


        $action1 = new TDataGridAction(['DiplomaAlterarStatusForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction(['DiplomaUploadDiplomaRegistradoForm', 'onEdit'], ['id'=>'{id}']);
        $action3 = new TDataGridAction(['DiplomaLivroRegistroEmissoraForm', 'onEdit'], ['id'=>'{id}']);            
        $action4 = new TDataGridAction([$this, 'onDownloadRegistrado'], ['id'=>'{id}']);        
        $action5 = new TDataGridAction([$this, 'onValidarArquivo'], ['id'=>'{id}']); 
        $action6 = new TDataGridAction([$this, 'onSetDadosPublicarDiploma'], ['id'=>'{id}']);
        
        
        $action1->setLabel('Anular diploma permanentemente');
        $action1->setImage('far: fa-window-close red');
        
        $action2->setLabel('Upload diploma registrado');
        $action2->setImage('fas:cloud-upload-alt blue');
        
        $action3->setLabel('Livro de registro emissora');
        $action3->setImage('fas: fa-book-open orange');

        $action4->setLabel('Download diploma registrado');
        $action4->setImage('fas:cloud-download-alt blue');
        
        $action5->setLabel('Verificar conformidade do arquivo');
        $action5->setImage('far: fa-file-code');
            
        $action6->setLabel('Conferir e publicar diploma');
        $action6->setImage('fas: fa-check-circle green');  

        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');        
        
        $action_group->addAction($action1);
        $action_group->addHeader('<hr>');                
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        $action_group->addAction($action4);
        $action_group->addAction($action5);
        $action_group->addAction($action6);
                        
        // add the actions to the datagrid        
        $this->datagrid->addActionGroup($action_group);
        
        
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
    
    
    public function onDownloadRegistrado($param)
    {
        try
        {  
            TTransaction::open('Felabs_DB');
            
            $id = $param['id'];
                
            $object = new DiplomaDigitalDiploma($id);
                
            if($object->caminho_arquivo_registrado <> NULL AND $object->arquivo_registrado <> NULL)
            {
                $caminho_arquivo = $object->caminho_arquivo_registrado . '/' . $object->arquivo_registrado;

                if (file_exists($caminho_arquivo))
                {
                    TPage::openFile($caminho_arquivo);
                }
            }
            else
            {
                new TMessage('info', 'Não há XML referente a este diploma');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    } 
    
    
    public function onValidarArquivo($param)
    {
        TScript::create('window.open("http://validadordiplomadigital.mec.gov.br","_blank")');
    }
    
    
    public function onSetDadosPublicarDiploma($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $id_diploma = $param['id'];
            
            $diploma = new DiplomaDigitalDiploma($id_diploma);
            
            if($diploma->arquivo_registrado == NULL)
            {
                $action_cancelar = new TAction(array('DiplomaRegistradoList', 'onReload'));                                                             
                new TMessage('error', 'O diploma precisa estar registrado para ser publicado', $action_cancelar);    
                die;  
            }
            else
            {
                $parametros['id_diploma'] = $diploma->id;
                
                TApplication::loadPage('DiplomaRepresentacaoVisualView', 'onLerDadosXml', $parametros);
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

        TSession::setValue(__CLASS__.'_filter_dados_diplomado_id', NULL);
        TSession::setValue(__CLASS__.'_filter_dados_curso_id', NULL);
        TSession::setValue(__CLASS__.'_filter_status_diploma', NULL);
        TSession::setValue(__CLASS__.'_filter_status_publicacao', NULL);


        if (isset($data->dados_diplomado_id) AND ($data->dados_diplomado_id)) {
            $filter = new TFilter('(SELECT nome FROM dados_diplomado WHERE id=dados_diploma.dados_diplomado_id)', 'like', "%{$data->dados_diplomado_id}%");
            TSession::setValue(__CLASS__.'_filter_dados_diplomado_id', $filter);
        }


        if (isset($data->dados_curso_id) AND ($data->dados_curso_id)) {
            $filter = new TFilter('(SELECT nome_curso_diploma FROM dados_curso WHERE id=dados_diploma.dados_curso_id)', 'like', "%{$data->dados_curso_id}%");
            TSession::setValue(__CLASS__.'_filter_dados_curso_id', $filter);
        }
        
        if ($data->status_diploma <> NULL) {
            $filter = new TFilter('status_diploma', '=', $data->status_diploma);
            TSession::setValue(__CLASS__.'_filter_status_diploma', $filter);
        }

        if ($data->status_publicacao <> NULL) {
            $filter = new TFilter('status_publicacao', '=', $data->status_publicacao);
            TSession::setValue(__CLASS__.'_filter_status_publicacao', $filter);
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
            
            //Filtra os diplomas de acordo com a unidade logada
            $unit_id = TSession::getValue('userunitid');

            $repository = new TRepository('DiplomaDigitalDiploma');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
            

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_dados_diplomado_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_dados_diplomado_id'));
            }


            if (TSession::getValue(__CLASS__.'_filter_dados_curso_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_dados_curso_id')); 
            }
            
            if (TSession::getValue(__CLASS__.'_filter_status_diploma')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status_diploma'));
            }


            if (TSession::getValue(__CLASS__.'_filter_status_publicacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status_publicacao'));
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
                    //STATUS DIPLOMA
                    if($object->status_diploma == 0)
                    {
                        $object->status_diploma = "<span class='label label-danger'>Inativo</span>";
                    }
                    elseif($object->status_diploma == 1)
                    {
                        $object->status_diploma = "<span class='label label-success'>Ativo</span>";
                    }
                    else
                    {
                        $object->status_diploma = $object->status_diploma;
                    }
                    
                    
                    //STATUS PUBLICAÇÃO                  
                    if($object->status_publicacao == 0)
                    {
                        $object->status_publicacao = "<span class='label label-danger'>Não publicado</span>";
                    }
                    elseif($object->status_publicacao == 1)
                    {
                        $object->status_publicacao = "<span class='label label-success'>Publicado</span>";
                    }
                    else
                    {
                        $object->status_publicacao = $object->status_publicacao;
                    }
                    
                    $object->data_publicacao = TDate::date2br($object->data_publicacao);
                    
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