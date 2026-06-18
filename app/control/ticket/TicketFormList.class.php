<?php
class TicketFormList extends TPage
{
    protected $form; 
    protected $loaded;

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_Ticket');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->setFormTitle('Ticket');

        TTransaction::open('Felabs_DB');

        $loggedUnit = TSession::getValue('userunitid');
        $criteria = new TCriteria;
        $criteria->add( new TFilter('departamento_id', '=', $loggedUnit));

        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('funcao_legado', '<>', 'Aluno'), TExpression::OR_OPERATOR);
        $criteria1->add(new TFilter('funcao_legado', 'is', NULL), TExpression::OR_OPERATOR);
        
        TTransaction::close();        

        // create the form fields
        $descricao = new TText('descricao');
        $matricula_aluno = new TEntry('matricula_aluno');
        $system_user_id = new TDBSeekButton('system_user_id', 'dados_fei', 'form_Ticket', 'FiAluno', 'Nome', null, 'nome_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $destino_user_id = new TDBMultiSearch('destino_user_id', 'Felabs_DB', 'SystemUser', 'id', 'name');
        $departamento = new TCombo('departamento');
        $categoria = new TDBCombo('categoria','Felabs_DB','TicketCategoria','id','nome','nome',$criteria);
        $anexo = new TFile('anexo');
        
        $nome_aluno->setEditable(FALSE);
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
     
        // add the fields
        $this->form->addQuickField('Cód. do aluno', $system_user_id, '80%');
        $this->form->addQuickField('Nome do aluno', $nome_aluno, '80%');
        $this->form->addQuickField('Matrícula atual', $matricula_aluno, '80%');
        $this->form->addQuickField('Categoria', $categoria, '80%', new TRequiredValidator);
        $this->form->addQuickField('Descrição', $descricao, '80%');
        $this->form->addQuickField('Departamento', $departamento, '80%', new TRequiredValidator);
        $this->form->addQuickField('Anexar arquivo(s)', $anexo, '80%');
        $this->form->addQuickField('Adicionar participantes', $destino_user_id, '80%');        

        // set exit action for input_exit
        $change_action = new TAction(array($this, 'onChangeAction'));
        $categoria->setChangeAction($change_action);

        // create the form actions
        $btn = $this->form->addQuickAction(('Salvar'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $btn = $this->form->addQuickAction('Voltar', new TAction(array('TicketList', 'onReload')), 'far:arrow-alt-circle-left blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'TicketList'));
        $container->add(TPanelGroup::pack('Novo Ticket', $this->form));
        
        parent::add($container);
    }


    public static function onExitAction($param) //INSERE NOME, EMAIL E DADOS DA MATRÍCULA
    {
        $numeroId = $param['key'];
        TTransaction::open('dados_fei');
    
        $object = new StdClass;       
        $aluno = new FiAluno($numeroId);

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
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
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
                $novoAluno->active = 'Y';
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
                $object->system_user_id = $novoAluno->id;
            }

            $object->data_reg = date('Y-m-d H:i:s');
            $object->status = 'A';
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
            
            //new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            new TMessage('info', "Menssagem enviada!");
            AdiantiCoreApplication::loadPage('TicketList'); //REDIRECIONA PARA A LISTA DE TICKETS
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); 
            TTransaction::rollback();
        }
    }

    public function onShow()
    {
        parent::show();
    }
}
