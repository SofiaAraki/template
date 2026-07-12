<?php
/**
 * SolicitacaoAlunoFormListAluno Form List
 * @author  <your name here>
 */
class SolicitacaoAlunoFormListAluno extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_SolicitacaoAluno');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('SolicitacaoAluno');
        


        // create the form fields
        $id_solicitacao = new THidden('id_solicitacao');
        $cod_aluno = new THidden('cod_aluno');
        $matricula_aluno = new THidden('matricula_aluno');
        $unidade = new THidden('unidade');
        $email_aluno = new TEntry('email_aluno');
        $tipo_solicitacao = new TCombo('tipo_solicitacao');
        $obs_solicitacao = new TText('obs_solicitacao');
        $status_solicitacao = new THidden('status_solicitacao');
        $status_pgto = new THidden('status_pgto');
        $quem_abriu = new THidden('quem_abriu');
        $filename = new TMultiFile('filename');
        $ultima_edicao = new THidden('ultima_edicao');
        $data_reg = new THidden('data_reg');
        $nome_aluno = new THidden('nome_aluno');


        TTransaction::open('Felabs_DB');

        $logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $loggedUnit = TSession::getValue('userunitid');
    
        $email_aluno->setValue($logged->email);

        if($loggedUnit == 1)
        {

            $optionsCNSC = [];

            $precosCNSC = SolicitacaoCnsc::getObjects();

            foreach($precosCNSC as $precoCNSC)
            {
                $optionsCNSC[$precoCNSC->id] = $precoCNSC->tipo_doc_cnsc;
            }


            $tipo_solicitacao->addItems($optionsCNSC);
        }

        if($loggedUnit == 2 || $loggedUnit == 6)
        {
        
            $optionsFFCL = [];

            $precosFFCL = SolicitacaoFfcl::getObjects();

            foreach($precosFFCL as $precoFFCL)
            {
                $optionsFFCL[$precoFFCL->id] = $precoFFCL->tipo_doc_ffcl;
            }

            $tipo_solicitacao->addItems($optionsFFCL);
        }



        TTransaction::close();





        // add the fields
        $this->form->addQuickField('Id Solicitacao', $id_solicitacao,  '50%' );
        $this->form->addQuickField('Cod Aluno', $cod_aluno,  '100%' );
        $this->form->addQuickField('Matricula Aluno', $matricula_aluno,  '100%' );
        $this->form->addQuickField('Unidade', $unidade,  '100%' );
        $this->form->addQuickField('Solicitação', $tipo_solicitacao,  '100%',new TRequiredValidator );
        $this->form->addQuickField('Observação', $obs_solicitacao,  '100%' );
        $this->form->addQuickField('Status Solicitacao', $status_solicitacao,  '100%' );
        $this->form->addQuickField('Status Pgto', $status_pgto,  '100%' );
        $this->form->addQuickField('Quem Abriu', $quem_abriu,  '100%' );
        $this->form->addQuickField('Anexar arquivos', $filename,  '100%' );
        $this->form->addQuickField('Email', $email_aluno,  '100%',new TEmailValidator );
        $this->form->addQuickField('Ultima Edicao', $ultima_edicao,  '100%' );
        $this->form->addQuickField('Data Reg', $data_reg,  '100%' );
        $this->form->addQuickField('Nome Aluno', $nome_aluno,  '100%' );




        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addQuickAction('Limpar',  new TAction([$this, 'onClear']), 'fa:eraser red');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id_solicitacao = new TDataGridColumn('id_solicitacao', 'Protocolo', 'left');
        $column_cod_aluno = new TDataGridColumn('cod_aluno', 'Cód. do aluno', 'left');
        $column_matricula_aluno = new TDataGridColumn('matricula_aluno', 'Matrícula', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_email_aluno = new TDataGridColumn('email_aluno', 'Email', 'left');
        $column_tipo_solicitacao = new TDataGridColumn('tipo_solicitacao', 'Tipo de solicitação', 'left');
        $column_obs_solicitacao = new TDataGridColumn('obs_solicitacao', 'Obs Solicitacao', 'left');
        $column_status_solicitacao = new TDataGridColumn('status_solicitacao', 'Situação', 'left');
        $column_status_pgto = new TDataGridColumn('status_pgto', 'Status Pgto', 'left');
        $column_quem_abriu = new TDataGridColumn('quem_abriu', 'Quem abriu', 'left');
        $column_filename = new TDataGridColumn('filename', 'Filename', 'left');
        $column_ultima_edicao = new TDataGridColumn('ultima_edicao', 'Última atualização', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');
        $column_nome_aluno = new TDataGridColumn('nome_aluno', 'Nome do aluno', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id_solicitacao);
        $this->datagrid->addColumn($column_tipo_solicitacao);
      //  $this->datagrid->addColumn($column_cod_aluno);
      //  $this->datagrid->addColumn($column_nome_aluno);
      //  $this->datagrid->addColumn($column_matricula_aluno);
        
      //  $this->datagrid->addColumn($column_email_aluno);
        
       // $this->datagrid->addColumn($column_obs_solicitacao);
        $this->datagrid->addColumn($column_status_solicitacao);
       // $this->datagrid->addColumn($column_status_pgto);
      //  $this->datagrid->addColumn($column_quem_abriu);
      //  $this->datagrid->addColumn($column_filename);
        $this->datagrid->addColumn($column_ultima_edicao);
        $this->datagrid->addColumn($column_data_reg);
        $this->datagrid->addColumn($column_unidade);

        // creates two datagrid actions

        // 
        $action_alunoMensagens = new TDataGridAction(array('SolicitacaoAlunoFormMensagens', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_alunoMensagens->setLabel('Ver atendimento');
        $action_alunoMensagens->setImage('fa:search green fa-lg');
        $action_alunoMensagens->setField('id_solicitacao');
        $this->datagrid->addAction($action_alunoMensagens);
        

        
        
    //    $action1 = new TDataGridAction(array($this, 'onEdit'));
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
      //  $action1->setLabel(_t('Edit'));
      //  $action1->setImage('far:edit blue fa-lg');
     //   $action1->setField('id_solicitacao');
        /*
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id_solicitacao');
        */
        // add the actions to the datagrid
      //  $this->datagrid->addAction($action1);
     //   $this->datagrid->addAction($action2);
        
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
        $container->add(TPanelGroup::pack('Abrir Novo Atendimento', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
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
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            // creates a repository for SolicitacaoAluno
            $repository = new TRepository('SolicitacaoAluno');
            $limit = 10;

          //  var_dump($logged->systemuser_codlegado);
         //   die();
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $logged->systemuser_codlegado));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_reg';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('SolicitacaoAluno_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('SolicitacaoAluno_filter'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
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
                    
                    if($object->status_solicitacao == 'Aberta'){
                        $object->status_solicitacao = '<span class="label label-danger">Aberta</span>';
                    }
                    elseif($object->status_solicitacao == 'Em Progresso'){
                        $object->status_solicitacao = '<span class="label label-warning">Em Progresso</span>';
                    }
                    elseif($object->status_solicitacao == 'Finalizada'){
                        $object->status_solicitacao = '<span class="label label-primary">Finalizada</span>';
                    }


                    if($object->unidade == '1'){
                        $object->unidade = '<span class="label label-success">CNSC</span>';
                        $solicitacaoUnidade = new SolicitacaoCnsc($object->tipo_solicitacao);
                        $object->tipo_solicitacao = $solicitacaoUnidade->tipo_doc_cnsc;
                    }
                    elseif($object->unidade == '2'){
                        $object->unidade = '<span class="label label-warning">FFCL</span>';
                        $solicitacaoUnidade = new SolicitacaoFfcl($object->tipo_solicitacao);
                        $object->tipo_solicitacao = $solicitacaoUnidade->tipo_doc_ffcl;
                    }
                    elseif($object->unidade == '3'){
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    }
                    elseif($object->unidade == '6'){
                        $object->unidade = '<span class="label label-warning">NEAD</span>';
                        $solicitacaoUnidade = new SolicitacaoFfcl($object->tipo_solicitacao);
                        $object->tipo_solicitacao = $solicitacaoUnidade->tipo_doc_ffcl;
                    }

                    $this->datagrid->addItem($object);
                    $this->datagrid->disableHtmlConversion();
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
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
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
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
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
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new SolicitacaoAluno;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->unidade = TSession::getValue('userunitid');
            $object->cod_aluno = $logged->systemuser_codlegado; //NO CASO DO ALUNO TROCAR PELO LOGIN
            $object->nome_aluno = $logged->name;
            $object->quem_abriu = $logged->id; //NO CASO DO ALUNO TROCAR PELO LOGIN
            $object->status_solicitacao = 'Aberta';
            $object->data_reg = date('Y-m-d H:i:s');



            if(isset($data-> filename)){

            $zip = new ZipArchive();
            $usuarioLogado = $logged-> id;
            $today = date("Ymd");
            $nomeArquivo = "arquivo"."_$today_".time().'.zip';
            $nomeCaminho = "files/solicitacao_atendimento/".$nomeArquivo;
            $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
            
            foreach ($data-> filename as $arq)
            {
                $source_file   = 'tmp/'.$arq;
            //    $target_file   = 'images/' . $arq;
                
                if (file_exists($source_file))
                {

                    $zip->addFile(  'tmp/'.$arq , "$arq" );
                    
                }
            }
            $zip->close();

            $object-> filename = $nomeArquivo;
            }



            ///////////////////////////////////////////////////////////// PEGA MATRICULA ALUNO
            TTransaction::open('dados_fei');
            $alunoCurso= new VwAluno($numeroId);

            $anoAtual = $anoHoje;
            $mesAtual = $mesHoje;


            if($mesAtual < 7)
            {
                $semestreM = 1;
            }
            else
            {
                $semestreM = 2;
            }

            if($alunoCurso->CodEntidade == 1)
            {
                $semestreM = 1;
            }

            $criteria = new TCriteria;                        
            $criteria->add(new TFilter('Codaluno', '=', $numeroId));            
            $criteria->add(new TFilter('AnoMatricula', '=', $anoAtual));            
            $criteria->add(new TFilter('SemestreMatricula', '=', $semestreM));


            $alunoSemestre = VwAluno::getObjects($criteria);

            $numeroCiclo = $alunoSemestre[0]->EtapaMatricula;
            $codEntidade = $alunoCurso->CodEntidade;

            if($numeroCiclo){
            $cicloAluno = " - CICLO ".$numeroCiclo;
            }

            if($alunoSemestre[0]->NomeCurso){
                $object-> matricula_aluno = $alunoSemestre[0]->NomeCurso.$cicloAluno;
            }

            


            if(empty($alunoSemestre[0]->NomeCurso)){
            $criteria1 = new TCriteria;                        
            $criteria1->add(new TFilter('Codaluno', '=', $numeroId));            
            $criteria1->add(new TFilter('AnoMatricula', '=', $anoAtual));            
            $criteria1->add(new TFilter('SemestreMatricula', '=', $semestreM));

            $alunoView1= new TRepository('VwAluno');
            $alunoSemestre1 = $alunoView1->load($criteria1);
            $object-> matricula_aluno = $alunoSemestre1[0]->NomeCurso.$cicloAluno;
            }
           
            if(empty($object-> matricula_aluno)){
                $object-> matricula_aluno = 'MATRÍCULA NÃO ENCONTRADA';
            }




            TTransaction::close();

            ///////////////////////////

            
            $object->store(); // save the object
            
            // get the generated id_solicitacao
            $data->id_solicitacao = $object->id_solicitacao;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved')); // success message
            $this->onReload(); // reload the listing
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new SolicitacaoAluno($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
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
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
