<?php

/**
 * NoticiasFormList Form List
 * @author  <your name here>
 * @version Adianti Framework
 */
class NoticiasFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct($param)
    {
        parent::__construct();
        
        // --- CONFIGURAÇÃO DO FORMULÁRIO ---
        $this->form = new TQuickForm('form_Noticias');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table; width:100%'; 
        $this->form->setFormTitle('Notícias');
        
        // --- CRIAÇÃO DOS CAMPOS ---
        $id            = new THidden('id');
        $data_postagem = new THidden('data_postagem');
        $data_edicao   = new THidden('data_edicao');
        $autor         = new THidden('autor');
        $unidade       = new THidden('unidade');
        
        $titulo        = new TEntry('titulo');
        $data_expira   = new TDate('data_expira');
        $publico       = new TCombo('publico');
        $conteudo      = new THtmlEditor('conteudo');
        $email         = new TCheckGroup('email');

        // Configuração dos itens do CheckGroup de E-mail
        $chek1 = ['Sim' => 'Enviar informativo para todos o alunos por email (pode levar alguns minutos)'];
        $email->addItems($chek1);

        // Configuração dos itens do Combo Público Alvo
        $itens_publico = [
            '1' => 'Alunos',
            '2' => 'Professores',
            '3' => 'Todos'
        ];
        $publico->addItems($itens_publico);

        // Definição de tamanhos dos campos
        $titulo->setSize('100%');
        $data_expira->setSize('100%');
        $conteudo->setSize('100%', 150);

        // --- ADICIONANDO CAMPOS AO FORMULÁRIO ---
        $this->form->addQuickField('Id', $id, 100);
        $this->form->addQuickField('Data Postagem', $data_postagem, 200);
        $this->form->addQuickField('', $data_edicao);
        $this->form->addQuickField('', $unidade);
        $this->form->addQuickField('Autor', $autor, 200);
        
        $this->form->addQuickField('Título', $titulo, 500, new TRequiredValidator);
        $this->form->addQuickField('Data de Expiração', $data_expira, 200, new TRequiredValidator);
        $this->form->addQuickField('Público Alvo', $publico, 200, new TRequiredValidator);
        $this->form->addQuickField('Conteúdo', $conteudo, 500, new TRequiredValidator);

        // --- AÇÕES DO FORMULÁRIO ---
        $this->form->addQuickAction('Salvar e Publicar', new TAction([$this, 'onSave']), 'fa:upload');
        $this->form->addQuickAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser green');
        
        // --- CONFIGURAÇÃO DA DATAGRID ---
        $this->datagrid = new TDataGrid;
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // Criando as colunas da Datagrid
        $column_data_postagem = new TDataGridColumn('data_postagem', 'Data da postagem', 'left');
        $column_data_expira   = new TDataGridColumn('data_expira', 'Data de expiração', 'left');
        $column_titulo        = new TDataGridColumn('titulo', 'Título', 'left');
        $column_conteudo      = new TDataGridColumn('conteudo', 'Conteúdo', 'left');
        $column_autor         = new TDataGridColumn('system_user->name', 'Autor', 'left');
        $column_data_edicao   = new TDataGridColumn('data_edicao', 'Data da edição', 'left');
        $column_unidade       = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_publico       = new TDataGridColumn('publico', 'Público Alvo', 'left');

        // Adicionando as colunas à Datagrid
        $this->datagrid->addColumn($column_data_postagem);
        $this->datagrid->addColumn($column_data_expira);
        $this->datagrid->addColumn($column_titulo);
        $this->datagrid->addColumn($column_conteudo);
        $this->datagrid->addColumn($column_autor);
        $this->datagrid->addColumn($column_data_edicao);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_publico);
        
        // Ação de Editar
        $action1 = new TDataGridAction([$this, 'onEdit']);
        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        // Ação de Excluir
        $action2 = new TDataGridAction([$this, 'onDelete']);
        $action2->setUseButton(TRUE);
        $action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        $this->datagrid->createModel();
        
        // --- PAGINAÇÃO ---
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // --- MONTAGEM DO LAYOUT (CONTAINER) ---
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Novo Informativo', $this->form));
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $loggedUnit = TSession::getValue('userunitid');
            $repository = new TRepository('Noticias');
            $limit = 10;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('Noticias_filter'))
            {
                $criteria->add(TSession::getValue('Noticias_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->data_postagem = TDate::date2br($object->data_postagem);
                    $object->data_expira   = TDate::date2br($object->data_expira);
                    $object->data_edicao   = TDate::date2br($object->data_edicao);

                    // Formatação visual das labels de Unidade
                    if ($object->unidade == 1) {
                        $object->unidade = '<span class="label label-success">CNSC</span>';
                    } elseif ($object->unidade == 2) {
                        $object->unidade = '<span class="label label-warning">FFCL</span>';
                    } elseif ($object->unidade == 3) {
                        $object->unidade = '<span class="label label-danger">FAFRAM</span>';
                    } elseif ($object->unidade == 8) {
                        $object->unidade = '<span class="label label-primary">VAN GOGH</span>';
                    }

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
    
    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        $action = new TAction([$this, 'Delete']);
        $action->setParameters($param); 
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            TTransaction::open('Felabs_DB'); 
            
            $object = new Noticias($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            $this->onReload($param); 
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); 
        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    /**
     * Save form data
     * @param $param Request
     */
    public function onSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $logged     = SystemUser::newFromLogin(TSession::getValue('login'));
            $loggedUnit = TSession::getValue('userunitid');

            $this->form->validate(); 
            
            $object = new Noticias;  
            $data = $this->form->getData(); 
            $object->fromArray((array) $data); 

            $object->autor   = $logged->id;
            $object->unidade = $loggedUnit;

            if (empty($object->data_postagem)) {
                $object->data_postagem = date('Y-m-d H:i:s');
            }

            $object->data_edicao = date('Y-m-d H:i:s');
            $object->store(); 

            // Se a opção de envio por e-mail estiver marcada
            if ($param['email'] != NULL) 
            {
                $members = SystemUser::getInGroups([new SystemGroup(1)]);
                $options = [];
                $notifications = [];
                
                if ($members) {
                    foreach ($members as $member) {
                        if ($member->active == 'Y') {
                            $options[]       = $member->email;
                            $notifications[] = $member->id;
                        }                                
                    }            
                }

                $prefs = SystemPreference::getAllPreferences();

                // Disparo de E-mails
                foreach ($options as $option) {
                    $emailfunc = $option;

                    $mail = new TMail;
                    $mail->setFrom($prefs['mail_from'], 'Intranet FE - Informativo');
                    $mail->setSubject("Intranet FE: $object->titulo");
                    $mail->setTextBody($object->conteudo);  
                    $mail->addAddress($emailfunc);
                  
                    $mail->SetUseSmtp();
                    $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                    $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                    $mail->send();
                }

                // Geração de Notificações Internas no sistema
                foreach ($notifications as $notification) {
                    $id_notif = $notification;

                    SystemNotification::register(
                        $id_notif,
                        'Novo informativo postado',
                        'Um novo informativo foi postado na página inicial.',
                        'class=WelcomeView',
                        'Ver informativos',
                        'far fa-list-alt green'
                    );
                }
            }
            
            $data->id = $object->id;
            $this->form->setData($data); 
            
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved')); 
            $this->onReload(); 
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData($this->form->getData()); 
            TTransaction::rollback(); 
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                TTransaction::open('Felabs_DB'); 
                
                $object = new Noticias($key); 
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
    
    /**
     * Shows the page
     */
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload'))
        {
            $this->onReload(func_get_arg(0));
        }
        parent::show();
    }
}