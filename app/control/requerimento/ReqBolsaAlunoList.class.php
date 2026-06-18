<?php
/**
 * AlunoList Listing
 * @author  <your name here>
 */
class ReqBolsaAlunoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_Aluno');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Aluno');
        
        // create the form fields
        //$data_reg = new TDate('data_reg');
        $situacao = new TEntry('situacao');

        // add the fields
        //$this->form->addQuickField('Data do Lançamento', $data_reg,  '25%' );
        $this->form->addQuickField('Situação', $situacao,  '25%' );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Aluno_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(('Novo Requerimento'), new TAction(array('ReqBolsaAlunoDialogQuestionView', 'onTeste')), 'bs:plus-sign green');
        
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
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_ciclo = new TDataGridColumn('ciclo', 'Ciclo', 'left');
        //$column_periodo = new TDataGridColumn('periodo', 'Período', 'left');
        //$column_cidade = new TDataGridColumn('cidade', 'Cidade', 'left');
        $column_situacao = new TDataGridColumn('situacao', 'Situação', 'left');
        $column_situacao->setTransformer( array($this, 'setStatusColor') );

        $column_data_reg->setTransformer(array($this, 'formatDate'));


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_ciclo);
        //$this->datagrid->addColumn($column_periodo);
        //$this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_situacao);

        
        $action_edit = new TDataGridAction(array('ReqBolsaAlunoFormView', 'onShow'));
        $action_edit->setButtonClass('btn btn-default btn-sm');
        $action_edit->setLabel('Visualizar');
        $action_edit->setImage('fa:search #478fca');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);

        $action_download = new TDataGridAction(array($this, 'downloadArquivo'));
        //$action_edit->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel(_t('Download'));
        $action_download->setImage('fas:cloud-download-alt green fa-lg');
        $action_download->setField('id');
        $action_download->setDisplayCondition( array($this, 'displayColumnDownload') );
        $this->datagrid->addAction($action_download);

        // create EDIT action
        $action_edit = new TDataGridAction(array('ReqBolsaAlunoForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:edit blue fa-lg');
        $action_edit->setField('id');
        $action_edit->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
    /** $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $action_del->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_del);*/
        
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
        $container->add(TPanelGroup::pack('Realizados', $this->form));
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
	else if ($color == "Em análise")
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
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }

    }

    public function displayColumn( $object )
    {

        TTransaction::open('Felabs_DB');
        
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));

        if ($object->situacao == 'Aberto' OR $object->situacao == 'Solicitar correção')
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
    

    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new ReqBolsaAluno($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('AlunoList_filter_data_reg',   NULL);
        TSession::setValue('AlunoList_filter_nome',   NULL);
        TSession::setValue('AlunoList_filter_curso',   NULL);
        TSession::setValue('AlunoList_filter_ciclo',   NULL);
        TSession::setValue('AlunoList_filter_periodo',   NULL);
        TSession::setValue('AlunoList_filter_cidade',   NULL);
        TSession::setValue('AlunoList_filter_situacao',   NULL);

        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); // create the filter
            TSession::setValue('AlunoList_filter_data_reg',   $filter); // stores the filter in the session
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


        if (isset($data->periodo) AND ($data->periodo)) {
            $filter = new TFilter('periodo', 'like', "%{$data->periodo}%"); // create the filter
            TSession::setValue('AlunoList_filter_periodo',   $filter); // stores the filter in the session
        }


        if (isset($data->cidade) AND ($data->cidade)) {
            $filter = new TFilter('cidade', 'like', "%{$data->cidade}%"); // create the filter
            TSession::setValue('AlunoList_filter_cidade',   $filter); // stores the filter in the session
        }


        if (isset($data->situacao) AND ($data->situacao)) {
            $filter = new TFilter('situacao', 'like', "%{$data->situacao}%"); // create the filter
            TSession::setValue('AlunoList_filter_situacao',   $filter); // stores the filter in the session
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
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'intranet_ad'
            TTransaction::open('Felabs_DB');

            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            
            // creates a repository for Aluno
            $repository = new TRepository('ReqBolsaAluno');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;

            $criteria->add(new TFilter('system_user_id', '=', $logged->id));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AlunoList_filter_data_reg')) {
                $criteria->add(TSession::getValue('AlunoList_filter_data_reg')); // add the session filter
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


            if (TSession::getValue('AlunoList_filter_periodo')) {
                $criteria->add(TSession::getValue('AlunoList_filter_periodo')); // add the session filter
            }


            if (TSession::getValue('AlunoList_filter_cidade')) {
                $criteria->add(TSession::getValue('AlunoList_filter_cidade')); // add the session filter
            }


            if (TSession::getValue('AlunoList_filter_situacao')) {
                $criteria->add(TSession::getValue('AlunoList_filter_situacao')); // add the session filter
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
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }


    /*public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }*/
    

    /*public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new ReqBolsaAluno($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $this->onReload( $param ); // reload the listing
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }*/
    

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
