<?php
/**
 * EmailTurmaFormList Form List
 * @author  <your name here>
 */
class EmailTurmaFormList extends TPage
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
        $this->form = new TQuickForm('form_EmailTurma');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('EmailTurma');
        
        // create the form fields
        $id = new THidden('id');
        $system_user_id = new THidden('system_user_id');
        $assunto = new TEntry('assunto');
        $conteudo = new TText('conteudo');
        $turma = new TCombo('turma');
        $data_reg = new THidden('data_reg');
        $unidade = new THidden('unidade');
        $anexo = new TFile('anexo');

        $conteudo->setSize('100%',140);

        $logged  = TSession::getValue('login');
        $loggedUnit = TSession::getValue('userunitid');

        TTransaction::open('dados_fei');

        $anoAtual = date('Y');
        $mesAtual = date('m');

        if($mesAtual < 7)
        {
            $semestreAtual = 1;
        }
        else
        {
            $semestreAtual = 2;
        }

        $criteria_nometurma = new TCriteria;
        $criteria_nometurma->add( new TFilter('Ano', '=', $anoAtual));
        $criteria_nometurma->add( new TFilter('Semestre', '=', $semestreAtual));
        $criteria_nometurma->add( new TFilter('CodEntidade', '=', $loggedUnit));
        $criteria_nometurma->setProperty('order', 'NomeCurso');
        $criteria_nometurma->setProperty('direction','ASC');
   
        $nomeTurmas = VwEtapanomecurso::getObjects($criteria_nometurma);

        $items = [];
        foreach($nomeTurmas as $nomeTurma)
        {
            $codigoTurmaEtapa = $nomeTurma->CodTurmaetapa;

            $criteria_alunos = new TCriteria;
            $criteria_alunos->add( new TFilter('CodTurmaetapa', '=', $codigoTurmaEtapa));
            $criteria_alunos->add( new TFilter('ConfirmacaoMatricula', '=', 'S'));
            $alunoTurmas = VWEmailTurma::getObjects($criteria_alunos);

            $items["$codigoTurmaEtapa"] = "$nomeTurma->NomeCurso".' - '."$nomeTurma->Identificacao".' - '.count($alunoTurmas).' alunos';
        }
        $turma->addItems($items);

        TTransaction::close();

        // add the fields
        $this->form->addQuickField('Id', $id,  '100%' );
        $this->form->addQuickField('System User Id', $system_user_id,  '100%' );
        $this->form->addQuickField('Assunto', $assunto,  '70%' , new TRequiredValidator);
        $this->form->addQuickField('Conteúdo do email', $conteudo,  '70%' , new TRequiredValidator);
        $this->form->addQuickField('Turma', $turma,  '70%' , new TRequiredValidator);
        $this->form->addQuickField('Unidade', $unidade,  '100%' );
        $this->form->addQuickField('Data do registro', $data_reg,  '100%' );
        $this->form->addQuickField('Anexo', $anexo,  '70%' );
         
        // create the form actions
        $this->form->addQuickAction('Limpar',  new TAction(array($this, 'onClear')), 'fas:eraser red');
        $this->form->addQuickAction('Salvar', new TAction(array($this, 'Question')), 'fas:save green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Usuário', 'left');
        $column_assunto = new TDataGridColumn('assunto', 'Assunto', 'left');
        $column_conteudo = new TDataGridColumn('conteudo', 'Conteúdo do email', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_assunto);
        $this->datagrid->addColumn($column_conteudo);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_data_reg);

        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
       
        // add the actions to the datagrid
        $this->datagrid->addAction($action2);
        
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
        $container->add(TPanelGroup::pack('Envio de Email para Turmas', $this->form));
        //$container->add(TPanelGroup::pack('Histórico de Envios', $this->datagrid, $this->pageNavigation));

        parent::add($container);
    }

    public function Question($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'onSave'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion("Atenção: O disparo de emails pode levar alguns minutos. Não feche esta janela até que o processo esteja concluído.
            Somente alunos que tiverem email cadastrado no GENESI receberão a mensagem. Alunos cadastrados receberão também uma mensagem na área do aluno.
            <br><br>Clique em 'Sim' para iniciar o disparo.",
            $action
        );
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
            
            // CORREÇÃO: Capturando o valor da unidade logada diretamente da sessão para uso local neste método
            $loggedUnit = TSession::getValue('userunitid');
            
            // creates a repository for EmailTurma
            $repository = new TRepository('EmailTurma');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('EmailTurma_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('EmailTurma_filter'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    if ($object->unidade == $loggedUnit)
                    {
                        // add the object inside the datagrid
                        $horario=substr($object-> data_reg,11,19);
                        $dataUs=TDate::date2br($object->data_reg);
                        $object-> data_reg = "$dataUs"." "."$horario";

                        if($object->unidade == 1){
                            $object->unidade = '<span class="label label-success">CNSC</span>';
                        }
                        elseif($object->unidade == 2 || $object->unidade == 5 || $object->unidade == 6){
                            $object->unidade = '<span class="label label-warning">FFCL</span>';
                        }
                        elseif($object->unidade == 3 || $object->unidade == 4){
                            $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                        }
                        elseif($object->unidade == 8){
                            $object->unidade = '<span class="label label-primary">VAN GOGH</span>';
                        }

                        $this->datagrid->addItem($object);
                    }
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
            $object = new EmailTurma($key, FALSE); // instantiates the Active Record
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
            $logged = TSession::getValue('login');
            $loggedUnit = TSession::getValue('userunitid');
            $prefs = SystemPreference::getAllPreferences();
            
            $object = new EmailTurma;  // create an empty object
            $data = new stdClass;
            $data->assunto = $param['assunto'];
            $data->conteudo = $param['conteudo'];
            $data->turma = $param['turma'];
            $data->data_reg = date('Y-m-d H:i:s');
            $data->system_user_id = $logged->id;
            $data->unidade = $loggedUnit;

            if(isset($param['anexo']) && $param['anexo'])
            {
                $data->anexo = $param['anexo'];
            }

            TTransaction::open('dados_fei'); // open a transaction

            $criteria_turma = new TCriteria;
            // CORREÇÃO: Adicionadas aspas na string literal 'CodTurmaetapa'
            $criteria_turma->add( new TFilter('CodTurmaetapa', '=', $data->turma)); //PEGA ALUNOS ATIVOS DA TURMA E DA ETAPA SELECIONADA NO FORM

            $turmaEmails = VwEmailturma::getObjects($criteria_turma);

            TTransaction::close();

            $emailsAluno = [];
            $codigoAlunos = [];
            foreach($turmaEmails as $turmaEmail)
            {
                if($turmaEmail->Email)
                {
                    $emailsAluno[] = $turmaEmail->Email; //ARRAY COM EMAILS DE TODOS OS ALUNOS ATIVOS DA TURMA
                }
               $codigoAlunos[] = $turmaEmail->Codaluno;
            }

            $alunosAcademico = [];
            foreach($codigoAlunos as $codAluno)
            {
                $criteria_usermsg = new TCriteria;
                $criteria_usermsg->add( new TFilter('systemuser_codlegado', '=', $codAluno));
                $usersAcademico = SystemUser::getObjects($criteria_usermsg); //VERIFICA SE EXISTE USUARIO NO ACADÊMICO

                if($usersAcademico)
                {
                    $alunosAcademico[] = $usersAcademico[0]->id; //ARRAY COM CÓDIGO (LEGADO) DOS ALUNOS QUE EXISTEM NO ACADÊMICO
                }
            }

            if (isset($param['anexo']) && $param['anexo'])   //SE USUÁRIO CARREGA FOTO
            {
                $source_file   = 'tmp/'.$data->anexo;
            }
        
            foreach($emailsAluno as $emailAluno) //ENVIA EMAIL PARA TODOS OS ALUNOS DA TURMA QUE TEM EMAIL NO GENESI
            {
                $mail = new TMail;
                $mail->setFrom($prefs['mail_from'], 'FE Ituverava - Contato');
                $mail->setSubject($data->assunto);
                $mail->setTextBody($data->conteudo);

                if(isset($param['anexo']) && $param['anexo'])
                {
                    $mail->addAttach($source_file); 
                }            
                $mail->addAddress($emailAluno);
              
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->setReplyTo($logged->email);
                $mail->send();               
            }
    
            if(!empty($alunosAcademico)) //ENVIA MENSAGEM NO SISTEMA ACADÊMICO PARA OS ALUNOS QUE TÊM USUÁRIO (QUE JÁ LOGARAM ALGUMA VEZ)
            {
                foreach($alunosAcademico as $alunoAcademico)
                {
                    $messageSystem = new SystemMessage;
                    $messageSystem->system_user_id = $logged->id;
                    $messageSystem->system_user_to_id = $alunoAcademico;
                    $messageSystem->subject = $data->assunto;
                    $messageSystem->message = $data->conteudo;
                    $messageSystem->dt_message = $data->data_reg;
                    $messageSystem->checked = 'N';
                    $messageSystem->store();
                }
            }
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', 'Emails enviados com sucesso'); // success message
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
                $object = new EmailTurma($key); // instantiates the Active Record
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