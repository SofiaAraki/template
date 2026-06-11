<?php
/**
 * MarketingBriefingFormList Form List
 * @author  <your name here>
 */
class MarketingBriefingFormList_MKT extends TPage
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
        
        
        $this->form = new BootstrapFormBuilder('form_MarketingBriefing_MKT');
        $this->form->setFormTitle('Briefing para Solicitação de Arte - Departamento de Marketing');
        $this->form->generateAria(); // automatic aria-label

        

        // create the form fields
        $id = new THidden('id');
        $solicitante = new THidden('solicitante');
        $departamento = new TEntry('departamento');
        $mantida = new TCombo('mantida');
        $objetivo_campanha = new TText('objetivo_campanha');
        $comunicacao_sugerida = new TEntry('comunicacao_sugerida');
        $titulo_evento = new TRadioGroup('titulo_evento');
        $data_evento = new TEntry('data_evento');
        $local_evento = new TEntry('local_evento');
        $tipo_inscricoes = new TEntry('tipo_inscricoes');
        $descritivo_evento = new TText('descritivo_evento');
        $contato_principal = new TEntry('contato_principal');
        $locais_divulgacao = new TEntry('locais_divulgacao');
        $publico_alvo = new TEntry('publico_alvo');
        $outras_info = new TText('outras_info');
        $status = new TCombo('status');
        $declarar_ciencia = new TCheckGroup('declarar_ciencia');
        $data_reg = new THidden('data_reg');
        $autorizado_por = new TEntry('autorizado_por');
        

        $mantida->addItems( ['FFCL' => 'FFCL', 'FAFRAM' => 'FAFRAM', 'CONNEXT' =>'CONNEXT'] );
        $comunicacao_sugerida->placeholder = 'Ex: Feed, Stories, Panfleto, etc.';
        $titulo_evento->addItems( ['Sim' => 'Sim', 'Não' => 'Não'] );
        $titulo_evento->setLayout('horizontal');
        $titulo_evento->setUseButton();

        $declarar_ciencia->addItems( ['Sim' => 'Sim'] );
        $declarar_ciencia->setLayout('horizontal');
        //$declarar_ciencia->setUseButton();
        //$declarar_ciencia->addValidation('Declarar Ciência', new TRequiredValidator);

        $status->addItems( ['SOLICITADO' => 'SOLICITADO','EM PROGRESSO' => 'EM PROGRESSO', 'CONCLUÍDO' => 'CONCLUÍDO'] );



        // add the fields
        $this->form->addFields( [ new TLabel('') ], [ $id ] );
        //$this->form->addFields( [ new TLabel('Solicitante') ], [ $solicitante ] );
        $this->form->addFields( [ new TLabel('Departamento:') ], [ $departamento ], [ new TLabel('Mantida:') ], [ $mantida ] );
        // $this->form->addFields( [ new TLabel('Mantida') ], [ $mantida ] );
        $this->form->addFields( [ new TLabel('Objetivo da Campanha:') ], [ $objetivo_campanha ] );
        $this->form->addFields( [ new TLabel('Formato da Comunicação:') ], [ $comunicacao_sugerida ] );
        $this->form->addFields( [ new TLabel('É divulgação de um evento com data e hora marcada?') ],[ $titulo_evento ] );
        $this->form->addFields( [ new TLabel('Se sim, preencher o campo abaixo:', '', '', 'b') ] );
        $this->form->addFields( [ new TLabel('Descritivo Evento') ], [ $descritivo_evento ] );
        $descritivo_evento->placeholder = 'Título do Evento:
Datas e Horários:
Local do Evento:
Link para as inscrições:
Telefone para informações:
Outras informações relevantes:';

        //$this->form->addFields( [ new TLabel('Data Evento') ], [ $data_evento ] );
        //$this->form->addFields( [ new TLabel('Local Evento') ], [ $local_evento ] );
        //$this->form->addFields( [ new TLabel('Tipo Inscricoes') ], [ $tipo_inscricoes ] );
        $this->form->addFields( [ new TLabel('Contato Principal:') ], [ $contato_principal ] );
        $this->form->addFields( [ new TLabel('Locais de Divulgação:') ], [ $locais_divulgacao ] );
        $this->form->addFields( [ new TLabel('Público Alvo:') ], [ $publico_alvo ] );
        $this->form->addFields( [ new TLabel('Ocasião para Material Impresso (banner, faixa): Informações breves que precisam conter no material:', '', '', 'b') ] );
        $this->form->addFields( [ new TLabel('Outras Informações para material impresso:') ], [ $outras_info ] );
        $this->form->addFields( [ new TLabel('Caso haja parceiros: Enviar logo em alta resolução para marketing@feituverava.com.br', '', 12, 'b') ] );
        $this->form->addFields( [ new TLabel('A produção de material físico (banners, camiseta, material impresso) exige autorização para produção (incluir nome de quem autorizou): ', '', 12, 'b') ] );
        $this->form->addFields( [ new TLabel('Autorizado por: ') ], [$autorizado_por] );

        
        
        $this->form->addFields( [ new TLabel('Declaro estar ciente em relação aos prazos estabelecidos pela tabela acima, e também de que o prazo pode se estender caso ocorram atrasos na entrega de conteúdo, alterações ou outro motivo por parte do solicitante', 'red', 12, 'b') ] );
        $this->form->addFields( [ new TLabel('*','red') ],[ $declarar_ciencia ] );
        //$this->form->addFields( [ new TLabel('Data Reg') ], [ $data_reg ] );
        $this->form->addFields( [ new TLabel('Status do Briefing') ], [ $status ] );


        // set sizes
        $id->setSize('100%');
        $solicitante->setSize('100%');
        $departamento->setSize('100%');
        $mantida->setSize('100%');
        $objetivo_campanha->setSize('100%');
        $comunicacao_sugerida->setSize('100%');
        //$titulo_evento->setSize('100%');
        $data_evento->setSize('100%');
        $local_evento->setSize('100%');
        $tipo_inscricoes->setSize('100%');
        $descritivo_evento->setSize('100%');
        $contato_principal->setSize('100%');
        $locais_divulgacao->setSize('100%');
        $publico_alvo->setSize('100%');
        $outras_info->setSize('100%');
        $status->setSize('25%');
        $declarar_ciencia->setSize('100%');
        $data_reg->setSize('100%');
        //$titulo_evento->style = 'font-size: 17pt';
        



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_solicitante = new TDataGridColumn('system_user->name', 'Solicitante', 'left');
        $column_departamento = new TDataGridColumn('departamento', 'Departamento', 'left');
        $column_mantida = new TDataGridColumn('mantida', 'Mantida', 'left');
        $column_objetivo_campanha = new TDataGridColumn('objetivo_campanha', 'Objetivo Campanha', 'left');
        $column_comunicacao_sugerida = new TDataGridColumn('comunicacao_sugerida', 'Formato Comunic.', 'left');
        $column_titulo_evento = new TDataGridColumn('titulo_evento', 'Titulo Evento', 'left');
        $column_data_evento = new TDataGridColumn('data_evento', 'Data Evento', 'left');
        $column_local_evento = new TDataGridColumn('local_evento', 'Local Evento', 'left');
        $column_tipo_inscricoes = new TDataGridColumn('tipo_inscricoes', 'Tipo Inscricoes', 'left');
        $column_descritivo_evento = new TDataGridColumn('descritivo_evento', 'Descritivo Evento', 'left');
        $column_contato_principal = new TDataGridColumn('contato_principal', 'Contato Principal', 'left');
        $column_locais_divulgacao = new TDataGridColumn('locais_divulgacao', 'Locais Divulgacao', 'left');
        $column_publico_alvo = new TDataGridColumn('publico_alvo', 'Publico Alvo', 'left');
        $column_outras_info = new TDataGridColumn('outras_info', 'Outras Info', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_declarar_ciencia = new TDataGridColumn('declarar_ciencia', 'Declarar Ciencia', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Realizado em:', 'left');

        //$column_status->setTransformer( array($this, 'setStatusColor') );


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_solicitante);
        //$this->datagrid->addColumn($column_departamento);
       // $this->datagrid->addColumn($column_mantida);
        //$this->datagrid->addColumn($column_objetivo_campanha);
        $this->datagrid->addColumn($column_comunicacao_sugerida);
        //$this->datagrid->addColumn($column_titulo_evento);
        //$this->datagrid->addColumn($column_data_evento);
        //$this->datagrid->addColumn($column_local_evento);
        //$this->datagrid->addColumn($column_tipo_inscricoes);
        //$this->datagrid->addColumn($column_descritivo_evento);
        //$this->datagrid->addColumn($column_contato_principal);
        //$this->datagrid->addColumn($column_locais_divulgacao);
        //$this->datagrid->addColumn($column_publico_alvo);
        //$this->datagrid->addColumn($column_outras_info);
        $this->datagrid->addColumn($column_status);
       // $this->datagrid->addColumn($column_declarar_ciencia);
        $this->datagrid->addColumn($column_data_reg);



        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
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
        
        <li>Conteúdo Online 5 dias úteis</li>
        <li>Impressos 15 dias úteis</li>
        <li>Brindes/Camisetas 30 dias úteis</li>'));
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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
            // open a transaction with database 'Felabs_DB'
            TTransaction::open('Felabs_DB');
           // $logged = SystemUser::newFromLogin(TSession::getValue('login'));

            
            // creates a repository for MarketingBriefing
            $repository = new TRepository('MarketingBriefing');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
          //  $criteria->add(new TFilter('solicitante', '=', $logged->id));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    if($object->status == "SOLICITADO")
                    {
                        $object->status = '<span class="label label-primary">SOLICITADO</span>';
                    }
                    if($object->status == "EM ANÁLISE")
                    {
                        $object->status = '<span class="label label-warning">EM ANÁLISE</span>';
                    }
                    elseif($object->status == "EM PROGRESSO")
                    {
                        $object->status = '<span class="label label-primary">EM PROGRESSO</span>';
                    }
                    elseif($object->status == "CONCLUÍDO")
                    {
                        $object->status = '<span class="label label-success">CONCLUÍDO</span>';
                    }
                    // add the object inside the datagrid
                    $object->data_reg = TDate::date2br($object->data_reg);
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
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new MarketingBriefing($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
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
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new MarketingBriefing;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object-> solicitante = $logged-> id;
            $object-> status  = $data->status;
            $object-> declarar_ciencia  = "SIM";
            $object-> data_reg  = date('Y-m-d');;
            $object->store(); // save the object

            $members = SystemUser::getInGroups( [new SystemGroup(12)] );
            $options = [];
            $notifications=[];
            if ($members)
            {
                foreach ($members as $member)
                {
                    $options[] = $member-> email;
                    $notifications[]=$member-> id;                               
                }            
            }
    
            $prefs = SystemPreference::getAllPreferences();
       
            foreach($options as $option){

                $emailfunc=$option;

                $mail = new TMail;
                $mail->setFrom($prefs['mail_from'], 'Briefing - Depto de Marketing');
                $mail->setSubject('Alteração de Status do Briefing - Acadêmico FE');
                $mail->setTextBody('O Departamento de Marketing realizou uma altração no Status do Briefing! Entre no Acadêmico FE para verificação.');  
            
                $mail->addAddress($emailfunc);
              
  
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();
            }

            foreach($notifications as $notification){

            $id_notif=$notification;

            SystemNotification::register(
                                        $id_notif,
                                        'Alteração no briefing',
                                        'Alteração de Status do Briefing.',
                                        'class=MarketingBriefingList',
                                        'Ver briefing',
                                        'fa fa-list-alt green'
                                        );
            }
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); // success message
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
                $object = new MarketingBriefing($key); // instantiates the Active Record
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
