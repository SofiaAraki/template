<?php

class AtendimentoPsicopedagogicoAgendamentoForm extends TPage
{
    protected $form; // form
    

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_AtendimentoPsicopedagogicoAgendamento');
        //$this->form->class = 'tform'; // change CSS class
        //$this->form = new BootstrapFormWrapper($this->form);
        //$this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Agendamento Psicopedagógico');
        


        // create the form fields
        $id = new THidden('id');
        $email = new TEntry('email');
        $celular = new TEntry('celular');
        $curso = new TEntry('curso');
        $data_evento = new TCombo('data_evento');
        $unidade = new TEntry('unidade');


        $data_evento->setSize('50%');
        $unidade->setSize('50%');
        $email->setSize('50%');
        $curso->setSize('50%');
        $celular->setSize('50%');
        $celular->placeholder = "DDD + número";
    

        TTransaction::open('Felabs_DB');
        $loggedProfUnit = TSession::getValue('userunitid'); //PEGA A ID DA UNIDADE DO USUARIO LOGADO
        $unitName = new SystemUnit($loggedProfUnit);
        $hoje = date('Y-m-d');


        //$data_evento = AtendimentoPsicopedagogicoAgendamento::getObjects($criteria);
        $criteria = new TCriteria;
        $criteria->add(new TFilter('status', '=', 'Disponível'));
        $criteria->add(new TFilter('unidade', '=', $loggedProfUnit));
        $criteria->add(new TFilter('data_evento', '>=', $hoje));
        $datas_disponiveis = AtendimentoPsicopedagogicoDatas::getObjects($criteria);
        
        $itens = [];
        
        foreach ($datas_disponiveis as $data_disponivel) 
        {
            $horario_entrada=substr($data_disponivel-> entrada_hora,0,5);
            $horario_saida=substr($data_disponivel-> saida_hora,0,5);
            $itens[$data_disponivel->id] = TDate::date2br($data_disponivel->data_evento). " " . $horario_entrada . "hrs " . " - " . $horario_saida . "hrs ";
        }

        $data_evento->addItems($itens);

        $logado = SystemUser::newFromLogin(TSession::getValue('login'));
        $email->setValue($logado->email);


        TTransaction::close();

        $unidade->setValue($unitName->name);
        $unidade->setEditable(FALSE);
        //$data_evento->setValue($datas_disponiveis);

        $email->addValidation('"Email"', new TRequiredValidator());
        $celular->addValidation('"Celular"', new TRequiredValidator());
        $curso->addValidation('"Curso"', new TRequiredValidator());
        $data_evento->addValidation('"Data e horário"', new TRequiredValidator());


        // add the fields
        $this->form->addFields( [ new TLabel('<b>Atendimento Psicopedagógico Individual - NAP</b>') ] );
        $this->form->addFields( [ new TLabel('<i>O Núcleo de Apoio Psicopedagógico (NAP) é um serviço de orientação e acompanhamento de alunos em suas dificuldades pessoais e de aprendizagem, oferecendo uma escuta diferenciada. Agende um horário para atendimento, conforme disponibilidade abaixo:</i>') ] );

        $this->form->addFields( [ new TLabel('Email') ], [ $email ] );
        $this->form->addFields( [ new TLabel('Celular') ], [ $celular ] );
        $this->form->addFields( [ new TLabel('Curso') ], [ $curso ] );
        $this->form->addFields( [ new TLabel('Data e horário') ], [ $data_evento ] );
        $this->form->addFields( [ new TLabel('Unidade') ], [ $unidade ] );
        $this->form->addFields( [ new TLabel('<i>Dúvidas, sugestões e encaminhamentos pelo email:  nap@feituverava.com.br</i>') ] );


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addAction(('Agendar'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'AtendimentoPsicopedagogicoDatasList'));
        $container->add($this->form);
        
        parent::add($container);
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
            $prefs  = SystemPreference::getAllPreferences();
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData();
            $object = new AtendimentoPsicopedagogicoDatas($data->data_evento);  // create an empty object
             // get form data as array
            //$object->fromArray( (array) $data); // load the object with data
            //var_dump($data);
            //die;
            $object->curso = $data->curso;
            $object->email = $data->email;
            $object->celular = $data->celular;
            $object->system_user_id = $logged->id;

            $object->status = "Reservado";
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('AtendimentoPsicopedagogicoDatasList', 'onReload');


            $data_br = TDate::date2br($object->data_evento);

            //email psicólogo
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Aluno - FEAcadêmico');
            $mail->setSubject('NAP');
            $mail->setTextBody("Prezado(a) Psicólogo(a), 

Existe um novo agendamento para você no NAP! 
O(a) aluno(a) $logged->name do curso $object->curso reservou o seguinte horário:
Data: $data_br
Entrada: $object->entrada_hora
Saída: $object->saida_hora
Contato: $object->celular
Entre no Sistema FEAcadêmico para verificar.");

            $psicologo = new SystemUser($object->id_psico); 
            
            $mail->addAddress($psicologo->email);
              
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            
            $mail->send();

            TTransaction::close(); // close the transaction
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
                $object = new AtendimentoPsicopedagogicoDatas($key); // instantiates the Active Record
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
}
