<?php
/**
 * MarketingBriefingFormList Form List
 * @author  <your name here>
 */
class MarketingBriefingFormList extends TPage
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
        
        $this->form = new BootstrapFormBuilder('form_MarketingBriefing');
        $this->form->setFormTitle('Briefing para Solicitação de Arte - Departamento de Marketing');
        $this->form->generateAria(); // automatic aria-label

        // create the form fields
        $id                   = new THidden('id');
        $solicitante          = new THidden('solicitante');
        $departamento         = new TEntry('departamento');
        $mantida              = new TCombo('mantida');
        $objetivo_campanha    = new TText('objetivo_campanha');
        $comunicacao_sugerida = new TEntry('comunicacao_sugerida');
        $titulo_evento        = new TRadioGroup('titulo_evento');
        $data_evento          = new TEntry('data_evento');
        $local_evento         = new TEntry('local_evento');
        $tipo_inscricoes      = new TEntry('tipo_inscricoes');
        $descritivo_evento    = new TText('descritivo_evento');
        $contato_principal    = new TEntry('contato_principal');
        $locais_divulgacao    = new TEntry('locais_divulgacao');
        $publico_alvo         = new TEntry('publico_alvo');
        $outras_info          = new TText('outras_info');
        $status               = new THidden('status');
        $declarar_ciencia     = new TCheckGroup('declarar_ciencia');
        $data_reg             = new THidden('data_reg');
        $autorizado_por       = new TEntry('autorizado_por');

        // field configurations
        $mantida->addItems( ['FFCL' => 'FFCL', 'FAFRAM' => 'FAFRAM', 'CONNEXT' => 'CONNEXT'] );
        $comunicacao_sugerida->placeholder = 'Ex: Feed, Stories, Panfleto, etc.';
        
        $titulo_evento->addItems( ['Sim' => 'Sim', 'Não' => 'Não'] );
        $titulo_evento->setLayout('horizontal');
        $titulo_evento->setUseButton();

        $declarar_ciencia->addItems( ['Sim' => 'Sim'] );
        $declarar_ciencia->setLayout('horizontal');
        $declarar_ciencia->addValidation('Declarar Ciência', new TRequiredValidator);

        $descritivo_evento->placeholder = "Título do Evento:\nDatas e Horários:\nLocal do Evento:\nLink para as inscrições:\nTelefone para informações:\nOutras informações relevantes:";

        // add the fields
        $this->form->addFields( [ new TLabel('') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Departamento:') ], [ $departamento ], [ new TLabel('Mantida:') ], [ $mantida ] );
        $this->form->addFields( [ new TLabel('Objetivo da Campanha:') ], [ $objetivo_campanha ] );
        $this->form->addFields( [ new TLabel('Formato da Comunicação:') ], [ $comunicacao_sugerida ] );
        $this->form->addFields( [ new TLabel('É divulgação de um evento com data e hora marcada?') ], [ $titulo_evento ] );
        $this->form->addFields( [ new TLabel('Se sim, preencher o campo abaixo:', '', '', 'b') ] );
        $this->form->addFields( [ new TLabel('Descritivo Evento') ], [ $descritivo_evento ] );
        $this->form->addFields( [ new TLabel('Contato Principal:') ], [ $contato_principal ] );
        $this->form->addFields( [ new TLabel('Locais de Divulgação:') ], [ $locais_divulgacao ] );
        $this->form->addFields( [ new TLabel('Público Alvo:') ], [ $publico_alvo ] );
        $this->form->addFields( [ new TLabel('Ocasião para Material Impresso (banner, faixa): Informações breves que precisam conter no material:', '', '', 'b') ] );
        $this->form->addFields( [ new TLabel('Outras Informações para material impresso:') ], [ $outras_info ] );
        $this->form->addFields( [ new TLabel('Caso haja parceiros: Enviar logo em alta resolução para marketing@feituverava.com.br', '', 12, 'b') ] );
        $this->form->addFields( [ new TLabel('A produção de material físico (banners, camiseta, material impresso) exige autorização para production (incluir nome de quem autorizou): ', '', 12, 'b') ] );
        $this->form->addFields( [ new TLabel('Autorizado por: ') ], [$autorizado_por] );
        $this->form->addFields( [ new TLabel('') ], [ $status ] );
        $this->form->addFields( [ new TLabel('Declaro estar ciente em relação aos prazos estabelecidos pela tabela acima, e também de que o prazo pode se estender caso ocorram atrasos na entrega de conteúdo, alterações ou outro motivo por parte do solicitante', 'red', 12, 'b') ] );
        $this->form->addFields( [ new TLabel('*','red') ], [ $declarar_ciencia ] );

        // set sizes
        $id->setSize('100%');
        $solicitante->setSize('100%');
        $departamento->setSize('100%');
        $mantida->setSize('100%');
        $objetivo_campanha->setSize('100%');
        $comunicacao_sugerida->setSize('100%');
        $data_evento->setSize('100%');
        $local_evento->setSize('100%');
        $tipo_inscricoes->setSize('100%');
        $descritivo_evento->setSize('100%');
        $contato_principal->setSize('100%');
        $locais_divulgacao->setSize('100%');
        $publico_alvo->setSize('100%');
        $outras_info->setSize('100%');
        $status->setSize('100%');
        $declarar_ciencia->setSize('100%');
        $data_reg->setSize('100%');

        if (!empty($id)) {
            $id->setEditable(FALSE);
        }
        
        // create the form actions
        $this->form->addActionLink('Limpar', new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_solicitante = new TDataGridColumn('system_user->name', 'Solicitante', 'left');
        $column_comunicacao_sugerida = new TDataGridColumn('comunicacao_sugerida', 'Formato Comunic.', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Realizado em:', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_solicitante);
        $this->datagrid->addColumn($column_comunicacao_sugerida);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_data_reg);

        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TAlert('warning', 'PRAZOS DE ENTREGA<br>
        Tabela de Prazos de Entrega de Campanhas que contenham: Impressos de Gráfica, Brindes, Camisetas e Conteúdo Online.<br>
        <li>Cobertura de Eventos/Fotografia: pelo menos 1 dia de antecedência</li>
        <li>Conteúdo Online: 5 dias úteis</li>
        <li>Impressos: 15 dias úteis</li>
        <li>Brindes/Camisetas: 30 dias úteis</li>'));
        
        $container->add($this->form);
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
            TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            
            $repository = new TRepository('MarketingBriefing');
            $limit = 10;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('solicitante', '=', $logged->id));
            
            if (empty($param['order'])) {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects) {
                foreach ($objects as $object) {
                    if ($object->status == 'EM ANÁLISE') {
                        $object->status = '<span class="label label-warning">EM ANÁLISE</span>';
                    } elseif ($object->status == 'EM PROGRESSO') {
                        $object->status = '<span class="label label-primary">EM PROGRESSO</span>';
                    } elseif ($object->status == 'CONCLUÍDO') {
                        $object->status = '<span class="label label-success">CONCLUÍDO</span>';
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
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try 
        {
            $key = $param['key'];
            TTransaction::open('Felabs_DB');
            $object = new MarketingBriefing($key, FALSE);
            $object->delete();
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
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
            TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $object = new MarketingBriefing;
            $object->fromArray( (array) $data);
            $object->solicitante = $logged->id;
            $object->status = "EM ANÁLISE";
            $object->declarar_ciencia = "SIM";
            $object->data_reg = date('Y-m-d');
            $object->store();

            $members = SystemUser::getInGroups( [new SystemGroup(12)] );
            $options = [];
            $notifications = [];
            
            if ($members) {
                foreach ($members as $member) {
                    $options[] = $member->email;
                    $notifications[] = $member->id;                               
                }            
            }
    
            $prefs = SystemPreference::getAllPreferences();
       
            foreach ($options as $option) {
                $emailfunc = $option;
                $mail = new TMail;
                $mail->setFrom($prefs['mail_from'], 'Briefing - Depto de Marketing');
                $mail->setSubject('Novo Briefing em Acadêmico FE');
                $mail->setTextBody('Novo Briefing recebido! Entre no Acadêmico FE para analisar.');  
                $mail->addAddress($emailfunc);
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();
            }

            foreach ($notifications as $notification) {
                $id_notif = $notification;
                SystemNotification::register(
                    $id_notif,
                    'Novo briefing recebido',
                    'Um novo briefing recebido e aguarda sua análise.',
                    'class=MarketingBriefingList',
                    'Ver briefing',
                    'fa fa-list-alt green'
                );
            }
            
            $data->id = $object->id;
            $this->form->setData($data);
            
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            $this->onReload();
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
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
            if (isset($param['key'])) {
                $key = $param['key'];
                TTransaction::open('Felabs_DB');
                $object = new MarketingBriefing($key);
                $this->form->setData($object);
                TTransaction::close();
            } else {
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
     * method show()
     * Shows the page
     */
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') ) {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}