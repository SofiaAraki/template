<?php
/**
 * SolicitacaoAlunoList Listing
 * @author  <your name here>
 */
class SolicitacaoAlunoList extends TPage
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
        $this->form = new TQuickForm('form_search_SolicitacaoAluno');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('SolicitacaoAluno');
        

        // create the form fields
        $id_solicitacao = new TEntry('id_solicitacao');
        $cod_aluno = new TEntry('cod_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $matricula_aluno = new TEntry('matricula_aluno');
        $unidade = new TEntry('unidade');
        $email_aluno = new TEntry('email_aluno');
        $tipo_solicitacao = new TEntry('tipo_solicitacao');
        $obs_solicitacao = new TEntry('obs_solicitacao');
        $obs_secretaria = new TEntry('obs_secretaria');
        $status_solicitacao = new TEntry('status_solicitacao');
        $status_pgto = new TEntry('status_pgto');
        $quem_abriu = new TEntry('quem_abriu');
        $quem_realizou = new TEntry('quem_realizou');
        $filename = new TEntry('filename');
        $filename_secretaria = new TEntry('filename_secretaria');
        $ultima_edicao = new TEntry('ultima_edicao');
        $data_reg = new TEntry('data_reg');


        // add the fields
        $this->form->addQuickField('Protocolo', $id_solicitacao,  '100%' );
        $this->form->addQuickField('Código do aluno', $cod_aluno,  '100%' );
        $this->form->addQuickField('Nome do aluno', $nome_aluno,  '100%' );
        $this->form->addQuickField('Matrícula', $matricula_aluno,  '100%' );
     //   $this->form->addQuickField('Unidade', $unidade,  '100%' );
     //   $this->form->addQuickField('Email Aluno', $email_aluno,  '100%' );
     //   $this->form->addQuickField('Tipo Solicitacao', $tipo_solicitacao,  '100%' );
     //   $this->form->addQuickField('Obs Solicitacao', $obs_solicitacao,  '100%' );
     //   $this->form->addQuickField('Obs Secretaria', $obs_secretaria,  '100%' );
     //   $this->form->addQuickField('Status Solicitacao', $status_solicitacao,  '100%' );
     //   $this->form->addQuickField('Status Pgto', $status_pgto,  '100%' );
     //   $this->form->addQuickField('Quem Abriu', $quem_abriu,  '100%' );
     //   $this->form->addQuickField('Quem Realizou', $quem_realizou,  '100%' );
     //   $this->form->addQuickField('Filename', $filename,  '100%' );
     //   $this->form->addQuickField('Filename Secretaria', $filename_secretaria,  '100%' );
      //  $this->form->addQuickField('Ultima Edicao', $ultima_edicao,  '100%' );
      //  $this->form->addQuickField('Data Reg', $data_reg,  '100%' );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('SolicitacaoAluno_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addQuickAction('Novo',  new TAction(['SolicitacaoAlunoForm', 'onEdit']), 'fas:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id_solicitacao = new TDataGridColumn('id_solicitacao', 'Protocolo', 'right');
        $column_cod_aluno = new TDataGridColumn('cod_aluno', 'Código do Aluno', 'left');
        $column_nome_aluno = new TDataGridColumn('nome_aluno', 'Nome do aluno', 'left');
        $column_matricula_aluno = new TDataGridColumn('matricula_aluno', 'Matrícula', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_email_aluno = new TDataGridColumn('email_aluno', 'Email Aluno', 'left');
        $column_tipo_solicitacao = new TDataGridColumn('tipo_solicitacao', 'Solicitação', 'left');
        $column_obs_solicitacao = new TDataGridColumn('obs_solicitacao', 'Obs Solicitacao', 'left');
        $column_obs_secretaria = new TDataGridColumn('obs_secretaria', 'Obs Secretaria', 'left');
        $column_status_solicitacao = new TDataGridColumn('status_solicitacao', 'Situação', 'left');
        $column_status_pgto = new TDataGridColumn('status_pgto', 'Status Pgto', 'left');
        $column_quem_abriu = new TDataGridColumn('system_user->name', 'Quem abriu', 'left');
        $column_quem_realizou = new TDataGridColumn('realizou->name', 'Quem realizou', 'left');
        $column_filename = new TDataGridColumn('filename', 'Filename', 'left');
        $column_filename_secretaria = new TDataGridColumn('filename_secretaria', 'Filename Secretaria', 'left');
        $column_ultima_edicao = new TDataGridColumn('ultima_edicao', 'Última edição', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id_solicitacao);
        $this->datagrid->addColumn($column_cod_aluno);
        $this->datagrid->addColumn($column_nome_aluno);
        $this->datagrid->addColumn($column_matricula_aluno);
        $this->datagrid->addColumn($column_unidade);
     //   $this->datagrid->addColumn($column_email_aluno);
        $this->datagrid->addColumn($column_tipo_solicitacao);
     //   $this->datagrid->addColumn($column_obs_solicitacao);
     //   $this->datagrid->addColumn($column_obs_secretaria);
        $this->datagrid->addColumn($column_status_solicitacao);
     //   $this->datagrid->addColumn($column_status_pgto);
        $this->datagrid->addColumn($column_quem_abriu);
        $this->datagrid->addColumn($column_quem_realizou);
      //  $this->datagrid->addColumn($column_filename);
     //   $this->datagrid->addColumn($column_filename_secretaria);
        $this->datagrid->addColumn($column_ultima_edicao);
        $this->datagrid->addColumn($column_data_reg);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('SolicitacaoAlunoFormMensagens', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id_solicitacao');
        $this->datagrid->addAction($action_edit);
        

        
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
        $container->add(TPanelGroup::pack('Listagem - Solicitações de Atendimento', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new SolicitacaoAluno($key); // instantiates the Active Record
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
        TSession::setValue('SolicitacaoAlunoList_filter_id_solicitacao',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_cod_aluno',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_nome_aluno',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_matricula_aluno',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_unidade',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_email_aluno',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_tipo_solicitacao',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_obs_solicitacao',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_obs_secretaria',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_status_solicitacao',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_status_pgto',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_quem_abriu',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_quem_realizou',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_filename',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_filename_secretaria',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_ultima_edicao',   NULL);
        TSession::setValue('SolicitacaoAlunoList_filter_data_reg',   NULL);

        if (isset($data->id_solicitacao) AND ($data->id_solicitacao)) {
            $filter = new TFilter('id_solicitacao', 'like', "%{$data->id_solicitacao}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_id_solicitacao',   $filter); // stores the filter in the session
        }


        if (isset($data->cod_aluno) AND ($data->cod_aluno)) {
            $filter = new TFilter('cod_aluno', 'like', "%{$data->cod_aluno}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_cod_aluno',   $filter); // stores the filter in the session
        }


        if (isset($data->nome_aluno) AND ($data->nome_aluno)) {
            $filter = new TFilter('nome_aluno', 'like', "%{$data->nome_aluno}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_nome_aluno',   $filter); // stores the filter in the session
        }


        if (isset($data->matricula_aluno) AND ($data->matricula_aluno)) {
            $filter = new TFilter('matricula_aluno', 'like', "%{$data->matricula_aluno}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_matricula_aluno',   $filter); // stores the filter in the session
        }


        if (isset($data->unidade) AND ($data->unidade)) {
            $filter = new TFilter('unidade', 'like', "%{$data->unidade}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_unidade',   $filter); // stores the filter in the session
        }


        if (isset($data->email_aluno) AND ($data->email_aluno)) {
            $filter = new TFilter('email_aluno', 'like', "%{$data->email_aluno}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_email_aluno',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo_solicitacao) AND ($data->tipo_solicitacao)) {
            $filter = new TFilter('tipo_solicitacao', 'like', "%{$data->tipo_solicitacao}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_tipo_solicitacao',   $filter); // stores the filter in the session
        }


        if (isset($data->obs_solicitacao) AND ($data->obs_solicitacao)) {
            $filter = new TFilter('obs_solicitacao', 'like', "%{$data->obs_solicitacao}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_obs_solicitacao',   $filter); // stores the filter in the session
        }


        if (isset($data->obs_secretaria) AND ($data->obs_secretaria)) {
            $filter = new TFilter('obs_secretaria', 'like', "%{$data->obs_secretaria}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_obs_secretaria',   $filter); // stores the filter in the session
        }


        if (isset($data->status_solicitacao) AND ($data->status_solicitacao)) {
            $filter = new TFilter('status_solicitacao', 'like', "%{$data->status_solicitacao}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_status_solicitacao',   $filter); // stores the filter in the session
        }


        if (isset($data->status_pgto) AND ($data->status_pgto)) {
            $filter = new TFilter('status_pgto', 'like', "%{$data->status_pgto}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_status_pgto',   $filter); // stores the filter in the session
        }


        if (isset($data->quem_abriu) AND ($data->quem_abriu)) {
            $filter = new TFilter('quem_abriu', 'like', "%{$data->quem_abriu}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_quem_abriu',   $filter); // stores the filter in the session
        }


        if (isset($data->quem_realizou) AND ($data->quem_realizou)) {
            $filter = new TFilter('quem_realizou', 'like', "%{$data->quem_realizou}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_quem_realizou',   $filter); // stores the filter in the session
        }


        if (isset($data->filename) AND ($data->filename)) {
            $filter = new TFilter('filename', 'like', "%{$data->filename}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_filename',   $filter); // stores the filter in the session
        }


        if (isset($data->filename_secretaria) AND ($data->filename_secretaria)) {
            $filter = new TFilter('filename_secretaria', 'like', "%{$data->filename_secretaria}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_filename_secretaria',   $filter); // stores the filter in the session
        }


        if (isset($data->ultima_edicao) AND ($data->ultima_edicao)) {
            $filter = new TFilter('ultima_edicao', 'like', "%{$data->ultima_edicao}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_ultima_edicao',   $filter); // stores the filter in the session
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); // create the filter
            TSession::setValue('SolicitacaoAlunoList_filter_data_reg',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('SolicitacaoAluno_filter_data', $data);
        
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
            // open a transaction with database 'Felabs_DB'
            TTransaction::open('Felabs_DB');
            
            // creates a repository for SolicitacaoAluno
            $repository = new TRepository('SolicitacaoAluno');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_reg';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('SolicitacaoAlunoList_filter_id_solicitacao')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_id_solicitacao')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_cod_aluno')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_cod_aluno')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_nome_aluno')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_nome_aluno')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_matricula_aluno')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_matricula_aluno')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_unidade')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_unidade')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_email_aluno')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_email_aluno')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_tipo_solicitacao')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_tipo_solicitacao')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_obs_solicitacao')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_obs_solicitacao')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_obs_secretaria')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_obs_secretaria')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_status_solicitacao')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_status_solicitacao')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_status_pgto')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_status_pgto')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_quem_abriu')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_quem_abriu')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_quem_realizou')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_quem_realizou')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_filename')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_filename')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_filename_secretaria')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_filename_secretaria')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_ultima_edicao')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_ultima_edicao')); // add the session filter
            }


            if (TSession::getValue('SolicitacaoAlunoList_filter_data_reg')) {
                $criteria->add(TSession::getValue('SolicitacaoAlunoList_filter_data_reg')); // add the session filter
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

                    $horario=substr($object-> data_reg,11,19);
                    $dataUs=TDate::date2br($object->data_reg);
                    $dataHora = "$dataUs"." "."$horario";
                    $object-> data_reg = substr($dataHora, 0, -7);

                    $horario2=substr($object-> ultima_edicao,11,19);
                    $dataUs2=TDate::date2br($object-> ultima_edicao);
                    $dataHora1 = "$dataUs2"." "."$horario2";
                    $object-> ultima_edicao = substr($dataHora1, 0, -7);

                    TTransaction::open('Felabs_DB');
                    
                    if($object->unidade == 1){

                        $solicitacaoUnidade = new SolicitacaoCnsc($object->tipo_solicitacao);
                        $object->tipo_solicitacao = $solicitacaoUnidade->tipo_doc_cnsc;
                    }
                    if($object->unidade == 2){

                        $solicitacaoUnidade = new SolicitacaoFfcl($object->tipo_solicitacao);
                        $object->tipo_solicitacao = $solicitacaoUnidade->tipo_doc_ffcl;
                    }

                    TTransaction::close();


                    if($object->unidade == 1){
                        $object->unidade = '<span class="label label-success">CNSC</span>';
                    }
                    elseif($object->unidade == 2){
                        $object->unidade = '<span class="label label-warning">FFCL</span>';
                    }
                    elseif($object->unidade == 3){
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    }
                    elseif($object->unidade == 6){
                        $object->unidade = '<span class="label label-warning">NEAD</span>';
                    }
                    elseif($object->unidade == 8){
                        $object->unidade = '<span class="label label-primary">VAN GOGH</span>';
                    }



                    if($object->status_solicitacao == 'Aberta'){
                        $object->status_solicitacao = '<span class="label label-danger">Aberta</span>';
                    }
                    elseif($object->status_solicitacao == 'Em Progresso'){
                        $object->status_solicitacao = '<span class="label label-warning">Em Progresso</span>';
                    }
                    elseif($object->status_solicitacao == 'Finalizada'){
                        $object->status_solicitacao = '<span class="label label-primary">Finalizada</span>';
                    }

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
            $object = new SolicitacaoAluno($key, FALSE); // instantiates the Active Record
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
