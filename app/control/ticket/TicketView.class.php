<?php

use Adianti\Widget\Util\TTimeline;

class TicketView extends TPage
{
    private $form;
    private $timeline;
    private $timelineContainer;
    private $destino_user_id;

    public function __construct()
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');

        $ticket = new Ticket(TSession::getValue('ticketid'));

        $userid = TSession::getValue('userid');
        $user   = new SystemUser($userid);

        $solicitante = new SystemUser($ticket->system_user_id);
        $categoria   = new TicketCategoria($ticket->categoria);

        $criteria = new TCriteria;
        $criteria->add(new TFilter('ticket_id', '=', $ticket->id));

        $ticketParticipantes = TicketParticipante::getObjects($criteria);

        $participantes = [];

        foreach ($ticketParticipantes as $part)
        {
            $participantes[] = (new SystemUser($part->system_user_id))->name;
        }

        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('funcao_legado', '<>', 'Aluno'), TExpression::OR_OPERATOR);
        $criteria1->add(new TFilter('funcao_legado', 'is', NULL), TExpression::OR_OPERATOR);
        $colaboradores = SystemUser::getObjects($criteria1);

        TTransaction::close();

        $items = [];
        foreach($colaboradores as $colaborador)
        {
            $items[$colaborador->id] = $colaborador->name ?: '';
        }

        $this->buildForm($user);
        
        $this->destino_user_id->addItems($items);

        $dadosTicket = $this->buildTicketInfo(
            $ticket,
            $categoria,
            $solicitante,
            $participantes
        );

        $row = new TElement('div');
        $row->class = 'row';

        $left = new TElement('div');
        $left->class = 'col-md-3';
        $left->add($dadosTicket);

        $right = new TElement('div');
        $right->class = 'col-md-9';

        $row->add($left);
        $row->add($right);
        
        $containerForm = new TVBox;
        $containerForm->style = 'width:100%';
        $containerForm->add(TPanelGroup::pack('Nova Postagem', $this->form));

        $this->timelineContainer = new TVBox;
        $this->timelineContainer->style = 'width:100%;display:block;';
        $this->timeline = new TTimeline;
        $this->timelineContainer->add($this->timeline);
        $right->add($this->timelineContainer);

        parent::add($row);
        parent::add($containerForm);
    }

    private function buildForm($user)
    {
        $this->form = new TQuickForm('form_Ticket');
        $this->form->class = 'tform';

        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%';

        $this->form->setFormTitle('Ticket');

        $id = new THidden('id');
        $ticket_id = new THidden('ticket_id');
        $system_user_id = new THidden('system_user_id');
        $this->destino_user_id = new TMultiSearch('destino_user_id');
        $descricao = new TText('descricao');
        $anexo = new TFile('anexo');
        $data_reg = new THidden('data_reg');
        
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Ticket Id', $ticket_id, '50%');
        $this->form->addQuickField('System User Id', $system_user_id, '50%');        
        $this->form->addQuickField('Descrição', $descricao, '80%');
        if($user->funcao_legado != 'Aluno')
        {
            $this->form->addQuickField('Incluir participante (opcional)', $this->destino_user_id, '80%');
        }
        $this->form->addQuickField('Anexar arquivo(s)', $anexo, '80%');
        $this->form->addQuickField('Data Reg', $data_reg, '100%');

        // create the form actions
        $btn = $this->form->addQuickAction(('Salvar Alterações'), new TAction(array($this, 'onSave')), 'fas:save');
        $btn->class = 'btn btn-sm btn-primary';
        
        if($user->funcao_legado == '') //SECRETARIA E ADMINS
        {
            $btn1 = $this->form->addQuickAction('Voltar', new TAction(array('TicketFormList', 'onReload')), 'fas:arrow-alt-circle-left blue');
            $btn2 = $this->form->addQuickAction('Fechar Ticket', new TAction(array($this, 'onFechaTicket')), 'fas:times');
            $btn2->class = 'btn btn-sm btn-danger';
            $btn3 = $this->form->addQuickAction('Imprimir Atendimento', new TAction(array($this, 'onImprimeAtendimento')), 'fas:print');
        }
        
        elseif($user->funcao_legado == 'Professor')
        {
            $btn1 = $this->form->addQuickAction('Voltar', new TAction(array('TicketListProf', 'onReload')), 'fas:arrow-alt-circle-left blue');
            $btn2 = $this->form->addQuickAction('Fechar Ticket', new TAction(array($this, 'onFechaTicket')), 'fas:times');
            $btn2->class = 'btn btn-sm btn-danger';
            $btn3 = $this->form->addQuickAction('Imprimir Atendimento', new TAction(array($this, 'onImprimeAtendimento')), 'fas:print');
        }
    }

    private function buildTicketInfo(
        Ticket $ticket,
        TicketCategoria $categoria,
        SystemUser $solicitante,
        array $participantes
    )
    {
        $status = match($ticket->status)
        {
            'A' => 'Aberto',
            'E' => 'Em Progresso',
            'F' => 'Finalizado',
            default => '-'
        };

        $panel = new TPanelGroup('Dados do Ticket');

        $html = "
        <strong><i class='fas fa-ticket-alt'></i> Ticket</strong><br>
        {$ticket->id}<hr>

        <strong><i class='fas fa-bars'></i> Categoria</strong><br>
        {$categoria->nome}<hr>

        <strong><i class='fas fa-user'></i> Solicitante</strong><br>
        {$solicitante->name}<hr>

        <strong><i class='fas fa-graduation-cap'></i> Matrícula</strong><br>
        {$ticket->matricula_aluno}<hr>

        <strong><i class='fas fa-clock'></i> Status</strong><br>
        {$status}<hr>

        <strong><i class='fas fa-calendar'></i> Abertura</strong><br>
        ".TDate::date2br($ticket->data_reg)."<hr>

        <strong><i class='fas fa-users'></i> Participantes</strong><br>
        ".implode('<br>', $participantes);

        $panel->add($html);

        return $panel;
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

                $horario = substr($object-> data_reg,11,8);
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
    
                    $horario = substr($ticketItem-> data_reg,11,8);
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

    public function onSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $this->form->validate();

            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);

            $data = $this->form->getData();

            /**
             * PROCESSA ANEXO
             */
            if (!empty($data->anexo))
            {
                $today = date('YmdHis');
                $source_file = 'tmp/' . $data->anexo;
                $target_file = 'arquivos/anexo_' . $today . '_' . $data->anexo;

                if (file_exists($source_file))
                {
                    rename($source_file, $target_file);
                }

                $data->anexo = $target_file;
            }

            /**
             * MONTA OBJETO DA POSTAGEM
             */
            $object = new TicketItem;
            $object->fromArray((array) $data);

            $object->ticket_id      = TSession::getValue('ticketid');
            $object->system_user_id = $user->id;
            $object->data_reg       = date('Y-m-d H:i:s');

            /**
             * ATUALIZA STATUS DO TICKET
             */
            $ticketMaster = new Ticket($object->ticket_id);

            if ($ticketMaster->edicao_user_id)
            {
                $userAndamento = new SystemUser($ticketMaster->edicao_user_id);
            }

            if (
                $ticketMaster->status != 'E' &&
                (!isset($userAndamento) || $userAndamento->funcao_legado != 'Aluno')
            )
            {
                $ticketMaster->status = 'E';
                $ticketMaster->ultima_edicao = date('Y-m-d H:i:s');
                $ticketMaster->edicao_user_id = $user->id;
                $ticketMaster->store();
            }

            /**
             * GARANTE QUE O USUÁRIO LOGADO É PARTICIPANTE
             */
            $criteria = new TCriteria;
            $criteria->add(new TFilter('ticket_id', '=', $object->ticket_id));
            $criteria->add(new TFilter('system_user_id', '=', $user->id));

            $ticketParticipantes = TicketParticipante::getObjects($criteria);

            if (empty($ticketParticipantes))
            {
                $ticketPart = new TicketParticipante;
                $ticketPart->ticket_id = $object->ticket_id;
                $ticketPart->system_user_id = $user->id;
                $ticketPart->store();
            }

            /**
             * ADICIONA NOVOS PARTICIPANTES
             */
            if (!empty($object->destino_user_id))
            {
                foreach ($object->destino_user_id as $user_id)
                {
                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('ticket_id', '=', $object->ticket_id));
                    $criteria->add(new TFilter('system_user_id', '=', $user_id));

                    $participante = TicketParticipante::getObjects($criteria);

                    if (empty($participante))
                    {
                        $ticketPart = new TicketParticipante;
                        $ticketPart->ticket_id = $object->ticket_id;
                        $ticketPart->system_user_id = $user_id;
                        $ticketPart->store();

                        // Histórico da inclusão
                        $hist = new TicketItem;
                        $hist->ticket_id = $object->ticket_id;
                        $hist->system_user_id = $user->id;
                        $hist->destino_user_id = $user_id;
                        $hist->descricao = 'adicionou participante';
                        $hist->data_reg = date('Y-m-d H:i:s');
                        $hist->store();
                    }
                }
            }

            /**
             * SALVA A POSTAGEM SOMENTE SE HOUVER CONTEÚDO
             */
            $temMensagem =
                !empty(trim($object->descricao ?? '')) ||
                !empty($object->anexo);

            if ($temMensagem)
            {
                $object->store();
            }

            /**
             * BUSCA TODOS OS PARTICIPANTES
             */
            $criteria = new TCriteria;
            $criteria->add(new TFilter('ticket_id', '=', $object->ticket_id));

            $ticketParticipantes = TicketParticipante::getObjects($criteria);

            $categoriaInfo = new TicketCategoria($ticketMaster->categoria);

            TTransaction::open('permission');
            $prefs = SystemPreference::getAllPreferences();
            TTransaction::close();

            $unidade = new SystemUnit($ticketMaster->departamento);

            /**
             * NOTIFICAÇÕES
             */
            foreach ($ticketParticipantes as $ticketParticipante)
            {
                $participanteTicket = new SystemUser($ticketParticipante->system_user_id);

                if ($participanteTicket->funcao_legado == 'Professor')
                {
                    $classeNotif = 'class=TicketListProf';
                }
                elseif ($participanteTicket->funcao_legado == 'Aluno')
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
                    'Houve uma nova postagem em um ticket de atendimento que você está participando. Por favor clique no botão abaixo para visualizá-lo.',
                    $classeNotif,
                    'Ver Ticket',
                    'far fa-list-alt green'
                );

                /**
                 * ENVIA E-MAIL SOMENTE SE EXISTIR UMA MENSAGEM
                 */
                if ($temMensagem)
                {
                    $userDestino = new SystemUser($ticketParticipante->system_user_id);

                    if (!empty($userDestino->email))
                    {
                        $mensagem = $object->descricao;

                        $corpoEmail = "Prezado(a),

Houve uma atualização no ticket de atendimento nº{$object->ticket_id} no sistema acadêmico da FE, unidade {$unidade->name}, do qual você é participante.

Categoria: {$categoriaInfo->nome}

Mensagem: {$mensagem}

http://academico.feituverava.com.br/

Att,

FE Acadêmico
Fundação Educacional de Ituverava
                        ";

                        $mail = new TMail;
                        $mail->setFrom(
                            $prefs['mail_from'],
                            'Mensagem - Atendimento FE Acadêmico'
                        );

                        $mail->setSubject('Atualização em Atendimento FE Acadêmico');
                        $mail->setTextBody($corpoEmail);
                        $mail->addAddress($userDestino->email);

                        $mail->SetUseSmtp();
                        $mail->SetSmtpHost(
                            $prefs['smtp_host'],
                            $prefs['smtp_port']
                        );

                        $mail->SetSmtpUser(
                            $prefs['smtp_user'],
                            $prefs['smtp_pass']
                        );

                        $mail->send();
                    }
                }
            }

            $data->id = $object->id ?? null;

            $this->form->setData($data);

            TTransaction::close();

            new TMessage(
                'info',
                TAdiantiCoreTranslator::translate('Record saved'),
                TApplication::loadPage('TicketView', 'onReload', $param)
            );
        }
        catch (Exception $e)
        {
            TTransaction::rollback();

            $this->form->setData($this->form->getData());

            new TMessage('error', $e->getMessage());

            TApplication::loadPage('TicketView', 'onReload', $param);
        }
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('ticket_id', '=', TSession::getValue('ticketid')));
            $criteria->setProperty('order', 'data_reg');
            $criteria->setProperty('direction', 'ASC');

            $this->timelineContainer->clearChildren();
            $this->timeline = new TTimeline;
            $this->timelineContainer->add($this->timeline);

            $items = TicketItem::getObjects($criteria);

            foreach ($items as $item)
            {
                $autor = new SystemUser($item->system_user_id);

                if (!empty($item->destino_user_id))
                {
                    $destino = new SystemUser($item->destino_user_id);

                    $titulo = "{$autor->name} adicionou {$destino->name}";
                    $icone  = 'fa:user-plus bg-aqua';
                }
                else
                {
                    $titulo = $autor->name;
                    $icone  = 'fa:comments bg-yellow';
                }

                $descricao = nl2br($item->descricao ?? '');

                if (!empty($item->anexo))
                {
                    $link = "index.php?class=TicketView&method=onDownloadMaster&id={$item->id}&key={$item->id}";

                    $descricao .= "
                        <br><br>
                        <a href='{$link}' class='btn btn-success btn-sm'>
                            <i class='fa fa-paperclip'></i> Anexo
                        </a>
                    ";
                }

                $data = TDate::date2br(substr($item->data_reg, 0, 10));
                $hora = substr($item->data_reg, 11, 5);

                $this->timeline->addItem(
                    $item->id,
                    $titulo,
                    $descricao,
                    $data,
                    $icone,
                    'left',
                    $item,
                    $hora
                );
            }

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}