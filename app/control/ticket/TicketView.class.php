<?php

class TicketView extends TPage
{
    private $html;


    public function __construct()
    {
        parent::__construct();
        

        // creates the form
        $this->form = new TQuickForm('form_Ticket');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        
        // define the form title
        $this->form->setFormTitle('Ticket');


        TTransaction::open('Felabs_DB');

        $ticket = new Ticket(TSession::getValue('ticketid'));
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
                    
        TTransaction::close();


        // create the form fields
        $id = new THidden('id');
        $ticket_id = new THidden('ticket_id');
        $system_user_id = new THidden('system_user_id');
        $destino_user_id = new TMultiSearch('destino_user_id');
        $descricao = new TText('descricao');
        $anexo = new TFile('anexo');
        $data_reg = new THidden('data_reg');
      

        TTransaction::open('Felabs_DB');

        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('funcao_legado', '<>', 'Aluno'), TExpression::OR_OPERATOR);
        $criteria1->add(new TFilter('funcao_legado', 'is', NULL), TExpression::OR_OPERATOR);

        $colaboradores = SystemUser::getObjects($criteria1);

        TTransaction::close();
        

        $items = [];

        foreach($colaboradores as $colaborador)
        {
            $items[$colaborador->id] = $colaborador->name;
        }

        $destino_user_id->addItems($items);
        

        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Ticket Id', $ticket_id, '50%');
        $this->form->addQuickField('System User Id', $system_user_id, '50%');        
        $this->form->addQuickField('Descrição', $descricao, '100%');
        
        if($user->funcao_legado != 'Aluno')
        {
            $this->form->addQuickField('Incluir participante (opcional)', $destino_user_id, '50%');
        }
        
        $this->form->addQuickField('Anexar arquivo(s)', $anexo, '50%');
        $this->form->addQuickField('Data Reg', $data_reg, '100%');


        // create the form actions
        $btn = $this->form->addQuickAction(('Salvar Alterações'), new TAction(array($this, 'onSave')), 'fas:save');
        $btn->class = 'btn btn-sm btn-primary';
        

        if($user->funcao_legado == '') //SECRETARIA E ADMINS
        {
            $btn1 = $this->form->addQuickAction('Voltar', new TAction(array('TicketFormList', 'onReload')), 'fas:arrow-alt-circle-left blue');

            $btn2 = $this->form->addQuickAction('Fechar Ticket', new TAction(array($this, 'onFechaTicket')), 'fas:times');
            $btn2->class = 'btn btn-sm btn-danger';

            //$btn3 = $this->form->addQuickAction('Dar Andamento', new TAction(array($this, 'onProgressoTicket')), 'fas:arrow-alt-circle-right');
            //$btn3->class = 'btn btn-sm btn-warning';

            $btn4 = $this->form->addQuickAction('Imprimir Atendimento', new TAction(array($this, 'onImprimeAtendimento')), 'fas:print');
        }
        
        elseif($user->funcao_legado == 'Professor')
        {
            $btn1 = $this->form->addQuickAction('Voltar', new TAction(array('TicketListProf', 'onReload')), 'fas:arrow-alt-circle-left blue');

            $btn2 = $this->form->addQuickAction('Fechar Ticket', new TAction(array($this, 'onFechaTicket')), 'fas:times');
            $btn2->class = 'btn btn-sm btn-danger';

           // $btn3 = $this->form->addQuickAction('Dar Andamento', new TAction(array($this, 'onProgressoTicket')), 'fas:arrow-alt-circle-right');
           // $btn3->class = 'btn btn-sm btn-warning';

            $btn4 = $this->form->addQuickAction('Imprimir Atendimento', new TAction(array($this, 'onImprimeAtendimento')), 'fas:print');
        }

        
        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/ticket.html');
              

        // define replacements for the main section
        $replace = array();
        $replace['id'] = $ticket->id;
        //$replace['titulo'] = $ticket->titulo;
        //$replace['descricao'] = $ticket->descricao;
       

        if($ticket->status == 'A')
        {
            $replace['status'] = 'Aberto';
        }
        elseif($ticket->status == 'E')
        {
            $replace['status'] = 'Em Progresso';
        }
        elseif($ticket->status == 'F')
        {
            $replace['status'] = 'Finalizado';
        }


        TTransaction::open('Felabs_DB');

        $solicitanteInfo = new SystemUser($ticket->system_user_id);
        $categoriaInfo = new TicketCategoria($ticket->categoria);

        $criteria = new TCriteria;
        $criteria->add(new TFilter('ticket_id', '=', $ticket->id));
 
        $ticketParticipantes = TicketParticipante::getObjects($criteria);

                   
        $participantes = "";

        foreach($ticketParticipantes as $ticketParticipante)
        {
            $partNome = new SystemUser($ticketParticipante->system_user_id);
            $participantes .= $partNome->name.'<br>';
        }

        TTransaction::close();

        
        $replace['solicitante'] = $solicitanteInfo->name;
        $replace['abertura'] = TDate::date2br($ticket->data_reg);
        $replace['matricula_aluno'] = $ticket->matricula_aluno;
        $replace['categoria'] = $categoriaInfo->nome;
        $replace['participantes'] = $participantes;
        
        
        $this->html->enableSection('main', $replace);

        
        //$this->enableManagement();
        
        // creates the page navigation
        //$this->pageNavigation = new TPageNavigation;
        //$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        //$vbox->add();
        $vbox->add(TPanelGroup::pack('Detalhes do Ticket', $this->html));


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Nova Postagem', $this->form));


        parent::add($vbox);
        parent::add('</div>');
        parent::add($container);
    }


    public function onImprimeAtendimento()
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $ticketId = TSession::getValue('ticketid');

            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('ticket_id', '=', $ticketId));
            $criteria->setProperty('order', 'id');
            $criteria->setProperty('direction','ASC');
            
            $ticketItems = TicketItem::getObjects($criteria);


            if(!empty($ticketItems))
            {
                $html = new AdiantiHTMLDocumentParser('app/documents/ticket_impressao.html', 'A4', 'portrait');

                $object = new Ticket($ticketId);

                $solicitanteInfo = new SystemUser($object->system_user_id);
                $quemAbriu = new SystemUser($object->quem_abriu);
                $categoriaInfo = new TicketCategoria($object->categoria);
                $unidade = new SystemUnit($object->departamento);

                $horario = substr($object-> data_reg,11,19);
                $dataUs = TDate::date2br($object->data_reg);
                
                $object->data_reg = "$dataUs"." ".substr($horario,0,-7);
                $object->system_user_id = $solicitanteInfo->name;
                $object->quem_abriu = $quemAbriu->name;
                $object->departamento = $unidade->name;
                $object->categoria = $categoriaInfo->nome;


                $html->setMaster($object);

                $obj = [];

                foreach($ticketItems as $ticketItem)
                {
                    $solicitanteInfo = new SystemUser($ticketItem->system_user_id);
    
                    $horario = substr($ticketItem-> data_reg,11,19);
                    $dataUs = TDate::date2br($ticketItem->data_reg);
                    
                    $ticketItem->data_reg = "$dataUs"." ".substr($horario,0,-7);
                    $ticketItem->system_user_id = $solicitanteInfo->name;
    
                    $obj[] = $ticketItem;
                }


                $html->setDetail('TicketItem', $ticketItems);
                

                $html->process();
                $output = $html->getContents();
                
                
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                
                $html->saveAsPDF($document);
                
                parent::openFile($document);
            }
           

            TTransaction::close();

            $param = [];
            $param['key'] = $ticketId;
            $param['id'] = $ticketId;

            new TMessage('info', "Documento para impressão gerado com sucesso", TApplication::loadPage('TicketView','onReload',$param));  

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function onDownloadMaster($param)
    {
        try
        {
            $id = $param['id'];
               
            TTransaction::open('Felabs_DB');
        
            $object = new TicketItem($id);

            TTransaction::close();

            if(!empty($object->anexo))
            {              
                if (strtolower(substr($object->anexo, -4)) == 'html')
                {
                    $win = TWindow::create( $object->anexo, 0.8, 0.8 );
                    $win->add( file_get_contents( "arquivos/".$object->anexo ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile($object->anexo);
                }

                $this->form->setData( $this->form->getData() );
                
                TTransaction::rollback();

                $param = [];
                $param['key'] = $object->ticket_id;
                $param['id'] = $object->ticket_id;

                new TMessage('info', 'Caso não consiga fazer o download, habilite pop-ups em seu navegador'); 

                TApplication::loadPage('TicketView','onReload',$param);
            }
            else
            {
                new TMessage('info', 'Esta solicitação não possui anexos'); 
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback();
        }
    }


    public function onFechaTicket( $param )
    {
        TTransaction::open('Felabs_DB');
        
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);     

        $ticket = new Ticket(TSession::getValue('ticketid'));
        $ticket->status = 'F';
        $ticket->ultima_edicao = date('Y-m-d H:i:s');
        $ticket->edicao_user_id = $user->id;
        $ticket->store();

        TTransaction::close();

        new TMessage('info', "O status deste ticket foi atualizado para 'Finalizado'.", TApplication::loadPage('TicketView','onReload',$param));
    }


 /*   public function onProgressoTicket( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid); 

            $ticket = new Ticket(TSession::getValue('ticketid'));

            if($ticket->edicao_user_id)
            {
                $userAndamento = new SystemUser($ticket->edicao_user_id);
            }

            
            if($ticket->status != 'E' && $userAndamento->funcao_legado != 'Aluno')
            {
                $ticket->status = 'E';
                $ticket->ultima_edicao = date('Y-m-d H:i:s');
                $ticket->edicao_user_id = $user->id;
                $ticket->store();

                TTransaction::close();

                new TMessage('info', "O status deste ticket foi atualizado para 'Em Progresso'.", TApplication::loadPage('TicketView','onReload',$param));
            }
            else
            {
                throw new Exception("Já tem alguém dando andamento neste ticket!");
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();

            TApplication::loadPage('TicketView', 'onReload', $param);            
        }
    }
    */
    
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid); 
            
            
            $this->form->validate(); 
            
            $object = new TicketItem;  
            $data = $this->form->getData(); 

 
            if ($data->anexo)
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
                $data->anexo = $nomeArquivo;
            }


            $object->fromArray( (array) $data); 

            $object->ticket_id = TSession::getValue('ticketid');
            $object->system_user_id = $user->id;
            $object->data_reg = date('Y-m-d H:i:s');
            

            $ticketMaster = new Ticket(TSession::getValue('ticketid'));

            // Verifica se já tem alguém editando
            if ($ticketMaster->edicao_user_id)
            {
                $userAndamento = new SystemUser($ticketMaster->edicao_user_id);
            }
            
            if ($ticketMaster->status != 'E' && (!isset($userAndamento) || $userAndamento->funcao_legado != 'Aluno'))
            {
                $ticketMaster->status = 'E';
                $ticketMaster->ultima_edicao = date('Y-m-d H:i:s');
                $ticketMaster->edicao_user_id = $user->id;
                $ticketMaster->store();
            }


            //VERIFICA SE USUÁRIO LOGADO JÁ NÃO FOI ADD NESTE TICKET

            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('ticket_id', '=', $object->ticket_id)); 
            $criteria->add(new TFilter('system_user_id', '=', $user->id)); 

            $ticketParticipantes = TicketParticipante::getObjects($criteria); //TODOS OS PARTICIPANTES DESTE TICKET

            if(empty($ticketParticipantes))
            {
                $ticketPart = new TicketParticipante;
                $ticketPart->ticket_id = $object->ticket_id;
                $ticketPart->system_user_id = $user->id;
                $ticketPart->store();
            }
                
            ///////////////////////////////

            if (!empty($object->destino_user_id))
            {
                foreach ($object->destino_user_id as $user_id)
                {
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('ticket_id', '=', $object->ticket_id));
                    $criteria->add(new TFilter('system_user_id', '=', $user_id));

                    $ticketParticipantes = TicketParticipante::getObjects($criteria);

                    if (empty($ticketParticipantes))
                    {
                        // Salva participante
                        $ticketPart = new TicketParticipante;
                        $ticketPart->ticket_id = $object->ticket_id;
                        $ticketPart->system_user_id = $user_id;
                        $ticketPart->store();

                        // Salva o registro no histórico (TicketItem)
                        $ticketItem = new TicketItem;
                        $ticketItem->ticket_id = $object->ticket_id;
                        $ticketItem->system_user_id = TSession::getValue('userid'); // quem adicionou
                        $ticketItem->destino_user_id = $user_id; // quem foi adicionado
                        $ticketItem->descricao = 'adicionou participante'; // pode ser customizado depois
                        $ticketItem->data_reg = date('Y-m-d H:i:s');
                        $ticketItem->store();
                    }
                    else
                    {
                        throw new Exception("O participante ID {$user_id} já está adicionado neste ticket");
                    }
                }
            }

            $object->store();


            //////////////////////////////////////////////////////////////


            $categoriaInfo = new TicketCategoria($ticketMaster->categoria);


            $criteria = new TCriteria;
            $criteria->add(new TFilter('ticket_id', '=', $object->ticket_id)); 
 
            $ticketParticipantes = TicketParticipante::getObjects($criteria); //TODOS OS PARTICIPANTES DESTE TICKET

            
            foreach($ticketParticipantes as $ticketParticipante)
            {
                $participanteTicket = new SystemUser($ticketParticipante->system_user_id);

                if($participanteTicket->funcao_legado == 'Professor')
                {
                	$classeNotif = 'class=TicketListProf';
                }
                elseif($participanteTicket->funcao_legado == 'Aluno')
                {
                	$classeNotif = 'class=TicketFormListAluno';
                }
                else
                {
                	$classeNotif = "class=TicketList&method=goTicketView&key={$object->ticket_id}&id={$object->ticket_id}";
                }
            

                SystemNotification::register(
                                            $ticketParticipante->system_user_id,
                                            'Atualização em ticket de atendimento',
                                            "Houve uma nova postagem em um ticket de atendimento que você está participando. Por favor clique no botão abaixo para visualizá-lo.",
                                            $classeNotif,
                                            'Ver Ticket',
                                            'far fa-list-alt green'
                                            );



                $userDestino = new SystemUser($ticketParticipante->system_user_id);

                if($userDestino->email) //SÓ ENVIA EMAIL SE PARTICIPANTE TIVER EMAIL                
                {

                TTransaction::open('permission');
            
                $prefs = SystemPreference::getAllPreferences();

                TTransaction::close();

                $unidade = new SystemUnit($ticketMaster->departamento);

                $corpoEmail = "Prezado(a),

                Houve uma atualização no ticket de atendimento nº{$object->ticket_id} no sistema acadêmico da FE, unidade {$unidade->name}, do qual você é participante.

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
            }
                
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), TApplication::loadPage('TicketView','onReload',$param));

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData( $this->form->getData() ); 
            TTransaction::rollback();

            TApplication::loadPage('TicketView','onReload',$param);           
        }
    }


    public function onReload( $param )
    {       
        try
        {
           // $limit = 6;

            TTransaction::open('Felabs_DB');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('ticket_id', '=', TSession::getValue('ticketid')));
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('order', 'data_reg');
            $criteria->setProperty('direction','asc');
            
            $products = TicketItem::getObjects($criteria);
            
            $criteria->resetProperties();
            //$count = TicketItem::countObjects($criteria);
            
            $replace_detail = array();
            
            if ($products)
            {
                // var_dump($products);
                // die();
                $i = 0;

                foreach ($products as $product)
                {
                    $replace_detail[] = $product->toArray(); 

                    $userInfo = new SystemUser((int)$replace_detail[$i]['system_user_id']);
                    $userDestInfo = new SystemUser((int)$replace_detail[$i]['destino_user_id']);

                    $replace_detail[$i]['system_user_id'] = $userInfo->name;
                    $replace_detail[$i]['destino_user_id'] = $userDestInfo->name;

                    if($replace_detail[$i]['destino_user_id'])
                    {
                        $replace_detail[$i]['msg_destinatario'] = 'adicionou '.$replace_detail[$i]['destino_user_id'].' a esta conversa';

                        
                        $replace_detail[$i]['icon'] = '<i class="fa fa-user-plus bg-aqua"></i>';
                    }
                    else
                    {
                        $replace_detail[$i]['msg_destinatario'] = '';
                        $replace_detail[$i]['icon'] = '<i class="fa fa-comments bg-yellow"></i>';
                    }

                    if($replace_detail[$i]['anexo'])
                    {
                        $link = "http://academico.feituverava.com.br/index.php?class=TicketView&method=onDownloadMaster&key={$replace_detail[$i]['id']}&id={$replace_detail[$i]['id']}";

                        $replace_detail[$i]['anexo'] = '<div class="timeline-footer">
                        <a href="'.$link.'" class="btn btn-success btn-xs"><i class="fa fa-paperclip"></i> '.'Anexo(s)'.'</a>
                      </div>';
                    }


                    $horario = substr($replace_detail[$i]['data_reg'],11,19);
                    $dataUs = TDate::date2br($replace_detail[$i]['data_reg']);
                    $replace_detail[$i]['data_reg'] = "$dataUs"." "."$horario";

                    $i++;
                }
            }


            $this->html->enableSection('products', $replace_detail, TRUE);
            $this->html->disableHtmlConversion();
            
            //$this->pageNavigation->setCount($count); // count of records
            //$this->pageNavigation->setProperties($param); // order, page
            //$this->pageNavigation->setLimit($limit); // limit
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}