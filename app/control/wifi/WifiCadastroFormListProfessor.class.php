<?php
class WifiCadastroFormListProfessor extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_WifiCadastro');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('WifiCadastro');
        
        // create the form fields
        $id = new THidden('id');
        $nome = new THidden('nome');
        $system_user_id = new THidden('system_user_id');
        $mac = new TEntry('mac');
        $unidade = new THidden('unidade');
        $tipo = new THidden('tipo');
        $data_reg = new THidden('data_reg');
        $status = new THidden('status');
     
        $mac->setMask('AA:AA:AA:AA:AA:AA');
        $mac->forceUpperCase();

        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Nome', $nome, '100%');
        $this->form->addQuickField('System user id', $system_user_id, '100%');
        $this->form->addQuickField('Mac do dispositivo', $mac, '100%');
        $this->form->addQuickField('Unidade', $unidade, '100%');
        $this->form->addQuickField('Tipo', $tipo, '100%');
        $this->form->addQuickField('Data_reg', $data_reg, '100%');
        $this->form->addQuickField('Status', $status, '100%');

        // create the form actions
        $this->form->addQuickAction('Salvar', new TAction(array($this, 'onSave')), 'far:save green');
        $this->form->addQuickAction('Ajuda',  new TAction(array($this, 'onHelp')), 'fa:info-circle');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_mac = new TDataGridColumn('mac', 'Mac', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');
        $column_status = new TDataGridColumn('status', 'Ativo', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'Systemuserid', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_mac);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_data_reg);
        //$this->datagrid->addColumn($column_system_user_id);

        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onEdit'));
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(('Editar'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(('Excluir'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');

        $action_onoff = new TDataGridAction(array($this, 'Question'));
        $action_onoff->setButtonClass('btn btn-default');
        $action_onoff->setLabel('Solicitar exclusão');
        $action_onoff->setImage('far:trash-alt red fa-lg');
        $action_onoff->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        //$this->datagrid->addAction($action2);
        $this->datagrid->addAction($action_onoff);
        
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
        $container->add(TPanelGroup::pack('Cadastro de Dispositivo - Autenticação Wi-Fi', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function Question($param)
    {
        TTransaction::open('Felabs_DB');
        
        $wifi = WifiCadastro::find($param['id']);
        
        TTransaction::close();
            
        if($wifi->status == 'E')
        {
            new TMessage('info','Você já solicitou a exclusão deste registro');
        }
        else
        {    
            $action1 = new TAction(array($this, 'onTurnOnOff'));
            $action1->setParameter('id', $param['key']);
            new TQuestion('Você realmente deseja solicitar a exclusão deste endereço MAC?', $action1);
        }
    }

    public function onTurnOnOff($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $wifi = WifiCadastro::find($param['id']);

            if($wifi->status != 'E')
            {
                $wifi->status = 'E';
                $wifi->data_reg = date('Y-m-d H:i:s');
                $wifi->store();
                new TMessage('info','Este registro foi marcado para exclusão e será removido em breve');
            }
            else
            {
                new TMessage('info','Você já solicitou a exclusão deste registro');
            }
            
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function onHelp()
    {
        new TMessage('info', 'Somente alunos e professores podem ter acesso a Wi-Fi. Exemplo de endereço mac: E4:58:E7:D5:23:8E (letras maiúsculas apenas). Como encontrar: 
<li><b>Android:</b> Nas configurações do aparelho, abra "Sobre o telefone" e depois "Status".</li> 
<li><b>Iphone:</b> No menu principal, abra "Ajustes". Vá em "Geral" e depois em "Sobre".</li> 
<li><b>Notebook:</b> Abra o "Prompt de Comando", digite "ipconfig /all" (sem aspas) e tecle Enter (Enviar endereço MAC referente ao Wi-Fi).</li>');
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
         
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            $repository = new TRepository('WifiCadastro');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('WifiCadastro_filter'))
            {
                $criteria->add(TSession::getValue('WifiCadastro_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    if($object->unidade == '1')
                    {
                        $object->unidade = '<span class="label label-success">CNSC</span>';                        
                    }
                    elseif($object->unidade == '2')
                    {
                        $object->unidade = '<span class="label label-warning">FFCL</span>';                        
                    }
                    elseif($object->unidade == '3')
                    {
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    }
                    elseif($object->unidade == '12')
                    {
                        $object->unidade = '<span class="label label-primary">CONNEXT</span>';
                    }


                    if($object->status == 'N')
                    {
                        $object->status = '<span class="label label-danger">Não</span>';
                    }
                    elseif($object->status == 'S')
                    {
                        $object->status = '<span class="label label-success">Sim</span>';
                    }
                    elseif($object->status == 'E')
                    {
                        $object->status = '<span class="label label-warning">Excluir</span>';
                    }

                    $object->data_reg = TDate::date2br($object->data_reg);

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
            
            $object = new WifiCadastro($key, FALSE); 
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
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            $prefs  = SystemPreference::getAllPreferences();

            $this->form->validate(); 
            
            $object = new WifiCadastro;  
            $data = $this->form->getData(); 


            $data->nome = $user->name;
            $data->unidade = TSession::getValue('userunitid');
            $data->data_reg = date('Y-m-d H:i:s');
            $data->status = 'N';
            $data->system_user_id = $user->id;


            if($user->checkInGroup( new SystemGroup(4)))
            {
                $data->tipo = 'Aluno';
            }

            elseif($user->checkInGroup( new SystemGroup(3)))
            {
                $data->tipo = 'Professor';
            }

            elseif($user->checkInGroup( new SystemGroup(5)))
            {
                $data->tipo = 'Colaborador';
            }

            $object->fromArray( (array) $data); 
            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', 'Os dados foram enviados com sucesso. Suas informações serão verificadas e, se estiverem corretas, o acesso do seu dispositivo a Wi-Fi será liberado em até 48 horas'); // success message
            $this->onReload(); 
            

            //email gestor
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Professor - FEAcadêmico');
            $mail->setSubject('Cadastro de Dispositivo - Autenticação Wi-Fi');
            $mail->setTextBody('Prezado(a), existe um novo dispositivo para Cadastro/Autenticação ao Wi-Fi!'."\n". 'Esta é uma mensagem automática. Solicitamos, por favor, não responder este e-mail.'); 

            /*if ($data->unidade == '3' OR $data->unidade == '4')
            {
                $mail->addAddress('douglas@feituverava.com.br');
            }

            else
            {*/
                $mail->addAddress('max@feituverava.com.br');
            //}              
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();


            //email professor
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Professor - FEAcadêmico');
            $mail->setSubject('Cadastro de Dispositivo - Autenticação Wi-Fi');
            $mail->setTextBody('Prezado(a) professor(a), seu Cadastro de Dispositivo - Autenticação Wi-Fi foi enviado para avaliação! Acompanhe a situação através da Área do Aluno - FEAcadêmico.'."\n". 'Esta é uma mensagem automática. Solicitamos, por favor, não responder este e-mail.');  

            $emails = explode(',', $logged-> email);
            
            if ($emails)
            {
                foreach ($emails as $email)
                {
                    if ($email)
                    {
                        $mail->addAddress(trim($email), $logged-> name);
                    }
                }
            }            
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();            
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
                
                $object = new WifiCadastro($key); 
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
