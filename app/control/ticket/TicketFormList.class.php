<?php

class TicketFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new TQuickForm('form_Ticket');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('Ticket');


        TTransaction::open('Felabs_DB');

        $loggedUnit = TSession::getValue('userunitid');

        $criteria = new TCriteria;
        $criteria->add( new TFilter(departamento_id, '=', $loggedUnit));

        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('funcao_legado', '<>', 'Aluno'), TExpression::OR_OPERATOR);
        $criteria1->add(new TFilter('funcao_legado', 'is', NULL), TExpression::OR_OPERATOR);
        
        //$categorias = TicketCategoria::getObjects($criteria);
        $colaboradores = SystemUser::getObjects($criteria1);

        TTransaction::close();        


        // create the form fields
        $id = new THidden('id');
        //$titulo = new THidden('titulo');
        $descricao = new TText('descricao');
        $matricula_aluno = new TEntry('matricula_aluno');
        //public function __construct($name, $database, $model, $key, $value, $ordercolumn = NULL, TCriteria $criteria = NULL)
        //$system_user_id = new TDBUniqueSearch('system_user_id','dados_fei_t','FiAluno','Codaluno','NomeSemAcento');        
        $system_user_id = new TDBSeekButton('system_user_id', 'dados_fei', 'form_Ticket', 'FiAluno', 'Nome', 'system_user_id', 'nome_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $destino_user_id = new TMultiSearch('destino_user_id'); //CRIADO MANUAL
        $status = new THidden('status');
        $departamento = new TCombo('departamento');
        $categoria = new TDBCombo('categoria','Felabs_DB','TicketCategoria','id','nome','nome',$criteria);
        $data_reg = new THidden('data_reg');
        $anexo = new TFile('anexo');

        
        $nome_aluno->setEditable(FALSE);
        //$system_user_id->setMinLength(5);
        $descricao->setSize('100%',100);
        $id->setEditable(FALSE);
        $matricula_aluno->setEditable(FALSE);
        

        $exit_action = new TAction(array($this, 'onExitAction'));
        $system_user_id->setExitAction($exit_action);


        $deptoItems = [];
        //$deptoItems[1] = 'Secretaria CNSC';
	    $deptoItems[12] = 'Secretaria CONNEXT';
        $deptoItems[3] = 'Secretaria FAFRAM';
	    $deptoItems[2] = 'Secretaria FFCL';
        $deptoItems[6] = 'Secretaria NEAD';



        $departamento->addItems($deptoItems);
       

        $items = [];

        foreach($colaboradores as $colaborador)
        {
            $items[$colaborador->id] = $colaborador->name;
        }

        $destino_user_id->addItems($items);        
     

        // add the fields
        $this->form->addQuickField('Id', $id, '50%');        
        $this->form->addQuickField('Cód. do aluno', $system_user_id, '50%');
        $this->form->addQuickField('Nome do aluno', $nome_aluno, '50%');
        $this->form->addQuickField('Matrícula atual', $matricula_aluno, '50%');
        $this->form->addQuickField('Categoria', $categoria, '50%', new TRequiredValidator);
        //$this->form->addQuickField('Título', $titulo, '100%');
        $this->form->addQuickField('Descrição', $descricao, '100%');
        $this->form->addQuickField('Departamento', $departamento, '50%', new TRequiredValidator);
        $this->form->addQuickField('Anexar arquivo(s)', $anexo, '50%');
        $this->form->addQuickField('Adicionar participante', $destino_user_id, '50%');        
        $this->form->addQuickField('Status', $status, '100%');        
        $this->form->addQuickField('Data Reg', $data_reg, '100%');


        // set exit action for input_exit
        $change_action = new TAction(array($this, 'onChangeAction'));
        $categoria->setChangeAction($change_action);


        // create the form actions
        $btn = $this->form->addQuickAction(('Salvar'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $btn = $this->form->addQuickAction('Voltar', new TAction(array('TicketList', 'onReload')), 'far:arrow-alt-circle-left blue');
        //$this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        //$btn = $this->form->addQuickAction('Voltar', new TAction(array('TicketList', 'onReload')), 'far:arrow-alt-circle-left blue');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_titulo = new TDataGridColumn('titulo', 'Título', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Solicitante', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_departamento = new TDataGridColumn('departamento', 'Unidade', 'left');
        $column_categoria = new TDataGridColumn('ticket_categoria->nome', 'Categoria', 'left');
        $column_quem_abriu = new TDataGridColumn('gestor->name', 'Quem abriu', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Criado em', 'left');
        $column_ultima_edicao = new TDataGridColumn('ultima_edicao', 'Última Edição', 'left');
        $column_edicao_user_id = new TDataGridColumn('edicao_user->name', 'Última Edição', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_titulo);
        //$this->datagrid->addColumn($column_descricao); MUITO GRANDE
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_departamento);        
        $this->datagrid->addColumn($column_edicao_user_id);
        $this->datagrid->addColumn($column_quem_abriu);
        $this->datagrid->addColumn($column_ultima_edicao);
        $this->datagrid->addColumn($column_data_reg);


        // create abrir action
        $action_abrir = new TDataGridAction(array($this, 'goTicketView'),$param);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_abrir->setLabel('Abrir Ticket');
        $action_abrir->setImage('fas:ticket-alt green fa-lg');
        $action_abrir->setField('id');
        $this->datagrid->addAction($action_abrir);

        
        // creates two datagrid actions
        /*$action1 = new TDataGridAction(array($this, 'onEdit'));
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);*/
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'TicketList'));
        $container->add(TPanelGroup::pack('Novo Ticket', $this->form));
        //$container->add(TPanelGroup::pack('Meus Tickets', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }


    public static function onExitAction($param) //INSERE NOME, EMAIL E DADOS DA MATRÍCULA
    {
        $numeroId = $param['key'];


        TTransaction::open('dados_fei');
    
        $object = new StdClass;       

        $aluno = new FiAluno($numeroId);  
     
        //$alunoCurso = new VwAluno($numeroId);


        //Teste para correção de entidade errada ($alunoCurso puxava primeira matrícula do aluno)
        $criteria = new TCriteria;
        $criteria->add(new TFilter('Codaluno', '=', $numeroId));
 
        $ultima_matricula = VwAluno::getObjects($criteria);
       
        $alunoCurso = end($ultima_matricula);


        $anoAtual = date('Y');

        $mesAtual = date('m');


        if($mesAtual < 8)
        {
            $semestreM = 1;
        }
        else
        {
            $semestreM = 2;
        }

        if($alunoCurso->CodEntidade == 1 OR $alunoCurso->CodEntidade == 12)
        {
            $semestreM = 1;
        }
      

        $criteria1 = new TCriteria;                        
        $criteria1->add(new TFilter('Codaluno', '=', $numeroId));            
        $criteria1->add(new TFilter('AnoMatricula', '=', $anoAtual));            
        $criteria1->add(new TFilter('SemestreMatricula', '=', $semestreM));

        $alunoView = new TRepository('VwAluno');
        $alunoSemestre = $alunoView->load($criteria1);

        $numeroCiclo = $alunoSemestre[0]->EtapaMatricula;
        $codEntidade = $alunoCurso->CodEntidade;

        if($numeroCiclo)
        {
            $cicloAluno = " - CICLO ".$numeroCiclo;
        }

        if($alunoSemestre[0]->NomeCurso)
        {
            $object->matricula_aluno = $alunoSemestre[0]->NomeCurso.$cicloAluno;
            $object->departamento = $alunoSemestre[0]->CodEntidade;
        }
            

        if(empty($alunoSemestre[0]->NomeCurso))
        {
            $criteria2 = new TCriteria;                        
            $criteria2->add(new TFilter('Codaluno', '=', $numeroId));            
            $criteria2->add(new TFilter('AnoMatricula', '=', $anoAtual));            
            $criteria2->add(new TFilter('SemestreMatricula', '=', $semestreM));
    
            $alunoView1 = new TRepository('VwAluno');
            $alunoSemestre1 = $alunoView1->load($criteria2);
            $object->matricula_aluno = $alunoSemestre1[0]->NomeCurso.$cicloAluno;
        }
           
        if(empty($object-> matricula_aluno))
        {
            $object->matricula_aluno = 'MATRÍCULA NÃO ENCONTRADA';
        }        
    
        TTransaction::close();
        
        TForm::sendData('form_Ticket', $object);
    }
       

    public static function onChangeAction($param)
    {
        TTransaction::open('Felabs_DB');

        $categoriaInfo = new TicketCategoria($param['categoria']);

        TTransaction::close();
        
        
        if($categoriaInfo->nome == 'CONTRATO')
        {
            $obj = new StdClass;
            $obj->descricao = "Por favor, anexar junto ao contrato assinado as seguintes documentações:
            
* Certificado de conclusão do Ensino Médio ou equivalente
* Histórico Escolar do Ensino Médio ou equivalente
* Certidão de nascimento ou casamento
* RG (não é válida a CNH)
* CPF
* Título de eleitor com o último comprovante de voto
* Certificado de reservista (homens maiores de 18 anos)
* Comprovante de residência (atual)";
                               
            TForm::sendData('form_Ticket', $obj);                   
        }

        //elseif($categoriaInfo->exemplo_msg)
        elseif($categoriaInfo->nome != 'CONTRATO' AND $categoriaInfo->exemplo_msg)
        {
            $obj = new StdClass;
            $obj->descricao = $categoriaInfo->exemplo_msg;
        
            TForm::sendData('form_Ticket', $obj);
        }
    }


    public function goTicketView($param)
    {       
        $parametros = [];
        $parametros['key'] = $param['key'];
        $parametros['id'] = $param['key'];

        TSession::setValue('ticketid',$param['key']); //FAZER FILTROS/BUSCA FUNCIONAR NA OUTRA CLASSE

        TApplication::loadPage('TicketView','onReload', $parametros);        
    }


    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);


            $repository = new TRepository('Ticket');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('quem_abriu', '=', $user->id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'status';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('Ticket_filter'))
            {
                $criteria->add(TSession::getValue('Ticket_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $horario = substr($object-> data_reg,11,19);
                    $dataUs = TDate::date2br($object->data_reg);
                    $object->data_reg = "$dataUs"." "."$horario"; 

                    $horario = substr($object-> ultima_edicao,11,19);
                    $dataUs = TDate::date2br($object->ultima_edicao);
                    $object->ultima_edicao = "$dataUs"." "."$horario";

                    if($object->status == 'A')
                    {
                        $object->status = '<span class="label label-danger">Aberto</span>';
                    }
                    elseif($object->status == 'E')
                    {
                        $object->status = '<span class="label label-warning">Em Progresso</span>';
                    }
                    elseif($object->status == 'F')
                    {
                        $object->status = '<span class="label label-primary">Finalizado</span>';
                    }

                    $unidade = new SystemUnit($object->departamento);
                    $object->departamento = $unidade->name;

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
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new Ticket($key, FALSE);
            $object->delete();
            
            TTransaction::close();
            $this->onReload( $param );
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); 
            TTransaction::rollback();
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $loggedUnit = TSession::getValue('userunitid');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);  
                        
            $this->form->validate(); 
            
            $object = new Ticket;  
            $data = $this->form->getData(); 
            $object->fromArray( (array) $data); 


            if($data->anexo)
            {
                $today = date("YmdHis");
                $source_file   = 'tmp/'.$data->anexo;
                $target_file   = 'arquivos/' . 'anexo_'. $today . '_' . $data->anexo;
                $finfo         = new finfo(FILEINFO_MIME_TYPE);
                
                if (file_exists($source_file))
                {
                    // move to the target directory
                    rename($source_file, $target_file);
                }

                $nomeArquivo = $target_file;
            }


            /*Para preencher INICIALMENTE o $system_user_id, a seek faz uma busca na tabela FiAluno. Então, para trazer um registro único na 
            variável $usuários abaixo, já que o código legado do professor pode ser igual do aluno, acrescenta-se o critério de funcao_legado*/
            $criteria = new TCriteria;
            $criteria->add( new TFilter('systemuser_codlegado', '=', $object->system_user_id));
            $criteria->add( new TFilter('funcao_legado', '=', "Aluno"));

            $usuarios = SystemUser::getObjects($criteria); //BUSCA SE JÁ EXISTE USUARIO COM CODLEGADO DO ALUNO (COD GENESI)


            //Aqui já preenche o system_user_id com o id da tabela correta (Usuários)
            if(count($usuarios) == 1) //SE JÁ EXISTE
            {
                $object->system_user_id = $usuarios[0]->id;
            }
            else //SE NAO EXISTE, CRIA
            {
                TTransaction::open('dados_fei');

                $alunoInfo = new FiAluno($object->system_user_id);

                TTransaction::close();


                $senhaData = substr($alunoInfo->Datanascimento, 8, 2).substr($alunoInfo->Datanascimento, 5, 2);


                $novoAluno = new SystemUser;
                $novoAluno->login = $object->system_user_id;
                $novoAluno->password = md5($senhaData);
                $novoAluno->name = $alunoInfo->NomeSemAcento;
                $novoAluno->email = $alunoInfo->Email;
                $novoAluno->systemuser_codlegado = $object->system_user_id;
                $novoAluno->funcao_legado = 'Aluno';
                $novoAluno->frontpage_id = 10;
                $novoAluno->active = Y;
                $novoAluno->store();

                $novoAluno->addSystemUserGroup( new SystemGroup(4) );

                /////////////////////////////////////////

                $criteria = new TCriteria;
                $criteria->add( new TFilter('CodAluno', '=', $object->system_user_id));

         
                TTransaction::open('dados_fei');
                
                $repos = VwAlunoMatriculaEtapa::getObjects($criteria);
                
                TTransaction::close();


                if($repos) //SE EXISTIREM MATRÍCULAS DESTE USUÁRIO NO SISTEMA
                {
                    $items = [];

                    foreach($repos as $repo)
                    {
                        $items[] = $repo->CodEntidade; //PEGA O CÓDIGO DA ENTIDADE DE TODAS AS MATRÍCULAS
                    }

                    $codEntidades = array_unique($items); //AGRUPA OS CÓDIGOS DE ENTIDADE IGUAIS
      
                    foreach($codEntidades as $codEntidade) //CADASTRA O ALUNO NAS ENTIDADES DAS QUAIS ELE POSSUI MATRÍCULA
                    {
                        $criteriaT = new TCriteria;
                        $criteriaT->add( new TFilter(system_user_id, '=', $novoAluno->id));
                        $criteriaT->add( new TFilter(system_unit_id, '=', $codEntidade));
                   
                        $unitTests = SystemUserUnit::getObjects($criteriaT);
     
                        if(empty($unitTests))
                        {
                            $unitNovo = new SystemUserUnit;
                            $unitNovo->system_user_id = $novoAluno->id;
                            $unitNovo->system_unit_id = $codEntidade;
                            $unitNovo->store();
                        }                        
                    }
                }

                    ///////////////////////////////////////////

                $object->system_user_id = $novoAluno->id;
            }


            $object->data_reg = date('Y-m-d H:i:s');
            $object->status = 'A';
            //$object->titulo = 'A';
            //$object->departamento = $loggedUnit;
            $object->quem_abriu = $user->id;


            $object->store(); 


            $ticketItem = new TicketItem; //CRIA O PRIMEIRO ITEM COM INFORMAÇÕES DO TICKET
            $ticketItem->ticket_id = $object->id;
            $ticketItem->system_user_id = $user->id; //A PESSOA QUE ABRIU O TICKET
            $ticketItem->descricao = $object->descricao;

            if($data->destino_user_id)
            {
                // Pega só o primeiro para salvar no ticketItem
                $destino = $data->destino_user_id[0];
                $ticketItem->destino_user_id = $destino;

            }

            if($data->anexo)
            {
                $ticketItem->anexo = $nomeArquivo;
            }
            
            $ticketItem->data_reg = $object->data_reg;
            $ticketItem->store();

            $ticketPart = new TicketParticipante; //ADICIONA ALUNO SOLICITANTE DO TICKET COMO PARTICIPANTE
            $ticketPart->ticket_id = $object->id;
            $ticketPart->system_user_id = $object->system_user_id;
            $ticketPart->store();

            if ($data->destino_user_id) 
            {
                foreach ($data->destino_user_id as $user_id) 
                {
                    $ticketPart = new TicketParticipante;
                    $ticketPart->ticket_id = $object->id;
                    $ticketPart->system_user_id = $user_id;
                    $ticketPart->store();

                }

           
            
            

            $categoriaInfo = new TicketCategoria($object->categoria);

            
            $unidade = new SystemUnit($object->departamento);


            foreach ($data->destino_user_id as $user_id)
            {
                SystemNotification::register(
                    $user_id,
                    'Atualização em ticket',
                    "Foi solicitada sua participação no ticket nº{$object->id} em Acadêmico FE, unidade {$unidade->name}.",
                    "class=TicketList&method=goTicketView&key={$object->id}&id={$object->id}",
                    'Ver Ticket',
                    'far fa-list-alt green'
                );
            

                $userDestino = new SystemUser($user_id);

                TTransaction::open('permission');
            
                $prefs = SystemPreference::getAllPreferences();

                TTransaction::close();

                $corpoEmail = "Prezado(a),

                                Foi solicitada sua participação no ticket nº{$object->id} no sistema acadêmico da FE, unidade {$unidade->name}.

                                Categoria: ".$categoriaInfo->nome.'

                                Mensagem: '.$object->descricao."

                                http://academico.feituverava.com.br/

                                Att,

                                FE Acadêmico
                                Fundação Educacional de Ituverava

                                ";

                $mail = new TMail;
                $mail->setFrom($prefs['mail_from'], "Mensagem - Atendimento FE Acadêmico");
                $mail->setSubject('Atualização em Atendimento FE Acadêmico');
                $mail->setTextBody($corpoEmail);  
            
                $mail->addAddress($userDestino->email);
              
  
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();

            }


            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved')); 
            $this->onReload();
        }
    }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); 
            TTransaction::rollback();
        }
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new Ticket($key);
                $this->form->setData($object);
                
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback();
        }
    }
    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
