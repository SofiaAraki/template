<?php
/**
 * DespesaList Listing
 * @author  <your name here>
 */
class DespesaProfessorList extends TPage
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
        $this->form = new TQuickForm('form_search_Despesa');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Despesa');
        

        // create the form fields
        $data_reg = new TDate('data_reg');
        $nome = new TEntry('nome');
        //$situacao = new TEntry('situacao');


        // add the fields
        $this->form->addQuickField('Data do registro', $data_reg,  '25%' );
        //$this->form->addQuickField('Professor', $nome,  '25%' );
        //$this->form->addQuickField('Situação', $situacao,  '25%' );

        $data_reg->setMask('dd/mm/yyyy');
        $data_reg->setDatabaseMask('yyyy-mm-dd');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Despesa_filter_data') );
        
        // add the search form actions
        //$btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$this->form->addQuickAction(('Nova Despesa'), new TAction(array('DespesaProfessorForm', 'onClear')), 'fa:plus #69aa46');
        $this->form->addQuickAction(('Nova Despesa'), new TAction(array($this, 'VerificaRegistro')), 'fa:plus #69aa46');
        $this->form->addQuickAction(('Manual de instruções'), new TAction(array('ManualProfPDFView', 'onShow' )), 'fa:book red');
        //$this->form->addQuickAction(('Despesa'), new TAction(array('DespesaProfessorVerifica', 'onTeste')), 'bs:plus-sign green');
        //$this->form->addQuickAction(('Despesa'), new TAction(array($this, 'VerificaRegistro')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Mês/Ano', 'left');
        $column_nome = new TDataGridColumn('nome', 'Professor', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        //$column_viagem_dobro = new TDataGridColumn('viagem_dobro', 'Viagem em dobro', 'left');
        $column_unidade = new TDataGridColumn('system_unit->name', 'Unidade', 'left');
        $column_trecho_id = new TDataGridColumn('trecho_professor->nome_trecho', 'Trecho', 'left');
        //$column_qtd_aulas = new TDataGridColumn('qtd_aulas', 'Aulas Ministradas', 'left');
        //$column_system_user_id = new TDataGridColumn('system_user->name', 'Operador', 'right');
        $column_situacao = new TDataGridColumn('situacao', 'Situação', 'left');
		$column_situacao->setTransformer( array($this, 'setStatusColor') );

        $column_data_reg->setTransformer(array($this, 'formatDate'));

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_curso);
        //$this->datagrid->addColumn($column_viagem_dobro);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_trecho_id);
        //$this->datagrid->addColumn($column_qtd_aulas);
        //$this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_situacao);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('DespesaProfessorForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $action_edit->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_edit);

        // create DELETE action
    /**    $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        //$action_del->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_del);*/
        
        //$action_edit = new TDataGridAction(array('DespesaProfessorFormView', 'onShow'));
        $action_edit = new TDataGridAction(array($this, 'OnDespesaProfessorFormView'));
        $action_edit->setButtonClass('btn btn-default btn-sm');
        $action_edit->setLabel('Visualizar');
        $action_edit->setImage('fa:search #478fca');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        /**
        $action_download = new TDataGridAction(array($this, 'downloadArquivo'));
        //$action_edit->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel(_t('Download'));
        $action_download->setImage('fas:cloud-download-alt green fa-lg');
        $action_download->setField('id');
        $this->datagrid->addAction($action_download);*/

        //$filename->setAction(new TAction(array('SolicitacaoAlunoFormEdit','downloadArquivo')));
        

        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(new TAlert('warning', 'Atenção: A realização do lançamento das despensas deverá ser realizado até o ÚLTIMO DIA do mês corrente. Não será mais possível realizar lançamentos após essa data. Por exemplo: despesas do mês de Abril deverão ser lançadas até o dia 30 de Abril. '));
        $container->add(TPanelGroup::pack('Relatório Indivual de Despesas', $this->form));
        $container->add(TPanelGroup::pack('Minhas despesas', $this->datagrid, $this->pageNavigation));
        //$container->add(TPanelGroup::pack('', $this->datagrid));
        //$container->add($this->pageNavigation);
        
        parent::add($container);
    }

    public function OnDespesaProfessorFormView($param)
    {
        $idDoMestre = $param['key'];

        $parametros = [];
        $parametros['key'] = $param['key'];
        $parametros['id'] = $param['key'];

        TSession::setValue('mestre',$idDoMestre); //FAZER FILTROS/BUSCA FUNCIONAR NA OUTRA CLASSE

        TApplication::loadPage('DespesaProfessorFormView','onShow', $parametros);
        
    }

    public function VerificaRegistro()
    {
        
        TTransaction::open('Felabs_DB');
        $logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $loggedUnitProf = TSession::getValue('userunitid');

        $hoje = date('m');

        $criteria = new TCriteria;
        $criteria->add(new TFilter("MONTH(data_reg)", '>=', $hoje));
        $criteria->add(new TFilter('system_user_id', '=', $logged->id));
        $criteria->add(new TFilter('unidade', '=', $loggedUnitProf), TExpression::AND_OPERATOR);

        $repository = new TRepository('DespesaProfessor');
        $count = $repository->count($criteria);

        //$verifica_registro = DespesaProfessor::getObjects($criteria);

        TTransaction::close();

        //if (! $verifica_registro){
        if ($count < 2){
            TApplication::loadPage('DespesaProfessorForm', 'onShow');
        }

        else{     

        new TMessage('info', 'Você já iniciou o preenchimento das despesas deste mês. Edite o registro atual, para incluir novas despesas.');
            }

    }


     public function downloadArquivo($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $id = $param['id'];  // get the parameter $key
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new DespesaProfessor($id); // instantiates the Active Record
                
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
      
        //$testando=$param['id_solicitacao'];
      //  $this->form->reload();
   //     $reload="SolicitacaoAlunoFormEdit&method=onEdit&key=$testando&id_solicitacao=$testando";
      //         SolicitacaoAlunoFormEdit&method=onEdit&key=147&id_solicitacao=147

      //  $url=urlencode($reload);
   //     var_dump($url);
    //    die();

        //TApplication::loadPage($reload);
    }

    public function displayColumn( $object )
    {

        TTransaction::open('Felabs_DB'); // open a transaction
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));

        //TTransaction::close();
        
        //TTransaction::open('Felabs_DB');

       // var_dump($object->usuario);
       // die();
        //if ($object->situacao != 'Enviado' AND $object->situacao != 'Em análise' AND $object->situacao != 'Deferido' AND $object->situacao != 'Indeferido')
        if ($object->situacao != 'Em análise' AND $object->situacao != 'Deferido' AND $object->situacao != 'Indeferido' AND $object->situacao != 'Aguardando aprovação')
        {
         //   var_dump(strlen($object->filename));
            return TRUE;
        }
        return FALSE;
        TTransaction::close();
    }

     public function formatDate($date, $object)
        {
            $dt = new DateTime($date);
            return $dt->format('m/Y');
        }  
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
	 
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
        else if ($color == "Aguardando aprovação")
        {
            return '<span class="label label-success">' . $column_situacao . '</span>';
        }
                
        else if ($color == "Deferido")
        {
            return '<span class="label label-primary">' . $column_situacao . '</span>';
        }

        else if ($color == "Indeferido")
        {
            return '<span class="label label-danger">' . $column_situacao . '</span>';
        }

        else
        {
            return $column_situacao;
        }
    
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
            $object = new DespesaProfessor($key); // instantiates the Active Record
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
        TSession::setValue('DespesaList_filter_data_reg',   NULL);
        TSession::setValue('DespesaList_filter_nome',   NULL);
        //TSession::setValue('DespesaList_filter_situacao',   NULL);
        /**
        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); // create the filter
            TSession::setValue('DespesaList_filter_data_reg',   $filter); // stores the filter in the session
        }*/

        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('cast(data_reg as date)', 'like', "{$data->data_reg}%"); // create the filter
            TSession::setValue('DespesaList_filter_data_reg',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue('DespesaList_filter_nome',   $filter); // stores the filter in the session
        }

        /**
        if (isset($data->situacao) AND ($data->situacao)) {
            $filter = new TFilter('situacao', 'like', "%{$data->situacao}%"); // create the filter
            TSession::setValue('DespesaList_filter_situacao',   $filter); // stores the filter in the session
        }
        */
        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Despesa_filter_data', $data);
        
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
            $loggedUnit = TSession::getValue('userunitid');															
            // creates a repository for Despesa
            $repository = new TRepository('DespesaProfessor');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            //criterio para exibir somente os que ainda estão em aberto
            //$criteria->add(new TFilter('situacao', '=', 'EM ANÁLISE'));
            $criteria->add(new TFilter('system_user_id', '=', $logged->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_reg';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('DespesaList_filter_data_reg')) {
                $criteria->add(TSession::getValue('DespesaList_filter_data_reg')); // add the session filter
            }


            if (TSession::getValue('DespesaList_filter_nome')) {
                $criteria->add(TSession::getValue('DespesaList_filter_nome')); // add the session filter
            }

			/**
            if (TSession::getValue('DespesaList_filter_situacao')) {
                $criteria->add(TSession::getValue('DespesaList_filter_situacao')); // add the session filter
            }
			*/
            
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
    
    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new DespesaProfessor($key, FALSE); // instantiates the Active Record
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
    }
    



    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
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
