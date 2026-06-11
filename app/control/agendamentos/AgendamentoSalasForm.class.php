<?php
/**
 * AgendamentoSalasForm Form
 * @author  <your name here>
 */
class AgendamentoSalasForm extends TWindow
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(670, null);
        parent::setTitle('Novo Agendamento');
        
        // creates the form
        $this->form = new TForm('form_AgendamentoSalas');
        $this->form->class = 'tform'; // change CSS class
    //    $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
      //  $this->form->setFormTitle('AgendamentoSalas');

        // add a table inside form
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);

        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Detalhes') )->colspan = 2;
        


        // create the form fields
        $id = new THidden('id');
        $usuario = new THidden('usuario');
        $data_evento = new TDate('data_evento');
        $inicio_hora = new TCombo('inicio_hora');
        $inicio_minutos = new TCombo('inicio_minutos');
        $termino_hora = new TCombo('termino_hora');
        $termino_minutos = new TCombo('termino_minutos');
        $descricao = new TEntry('descricao');
        $data_reg = new TEntry('data_reg');
        $sala_id = new TCombo('sala_id');
        $unidade = new TCombo('unidade');
        $calendario_options = new TCheckGroup('calendario_options');

        $items=[];
        $items[1] = 'Salão Nobre';
        $items[2] = 'Poliesportivo';
        $items[3] = 'Quadra';

        $sala_id->addItems($items);

        $calendario_items=[];
        $calendario_items[0] = 'FE (Intranet - Visível para colaboradores apenas)';
        $calendario_items[12] = 'CONNEXT (Visível para alunos)';
        $calendario_items[2] = 'FFCL (Visível para alunos)';
        $calendario_items[3] = 'FAFRAM (Visível para alunos)';

        $calendario_options->addItems($calendario_items);

        $unidade_items=[];
        $unidade_items['#3a87ad'] = 'FE Administrativo';
        $unidade_items['#33cc33'] = 'CONNEXT';
        $unidade_items['#f19800'] = 'FFCL';
        $unidade_items['#ff0000'] = 'FAFRAM';

        $unidade->addItems($unidade_items);


        //AÇÕES PARA COLOCAR 00 NO CAMPO MINUTOS QUANDO ALGUMA HORA É COLOCADA

        $change_action_inicio = new TAction(array($this, 'onChangeActionInicio'));
        $inicio_hora->setChangeAction($change_action_inicio);

        $change_action_termino = new TAction(array($this, 'onChangeActionTermino'));
        $termino_hora->setChangeAction($change_action_termino);




        $hours = array();
        $minutes = array();
        for ($n=0; $n<24; $n++)
        {
            $hours[str_pad($n, 2, '0', STR_PAD_LEFT)] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        for ($n=0; $n<=55; $n+=5)
        {
            $minutes[str_pad($n, 2, '0', STR_PAD_LEFT)] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }

        $inicio_hora->addItems($hours);
        $inicio_minutos->addItems($minutes);
        $termino_hora->addItems($hours);
        $termino_minutos->addItems($minutes);

        $inicio_hora->addValidation('Hora', new TRequiredValidator);
        $inicio_minutos->addValidation('Minutos', new TRequiredValidator);
        $termino_hora->addValidation('Hora', new TRequiredValidator);
        $termino_minutos->addValidation('Minutos', new TRequiredValidator);
        $descricao->addValidation('Descrição', new TRequiredValidator);
        $data_evento->addValidation('Data', new TRequiredValidator);

        $inicio_hora->setSize(70);
        $inicio_minutos->setSize(70);
        $termino_hora->setSize(70);
        $termino_minutos->setSize(70);
        
        $descricao->setSize('70%');
        $data_evento->setSize('70%');
        $sala_id->setSize('70%');
        $unidade->setSize('70%');


        // add the fields
        $table->addRowSet($id);
        $table->addRowSet($usuario);
        $table->addRowSet(new TLabel('Local/Sala'), $sala_id);
        $table->addRowSet(new TLabel('Data do evento'), $data_evento);
        $table->addRowSet(new TLabel('Início'), array($inicio_hora, ':', $inicio_minutos));
        $table->addRowSet(new TLabel('Término'), array($termino_hora, ':', $termino_minutos));
        $table->addRowSet(new TLabel('Descrição breve'), $descricao);
        $table->addRowSet(new TLabel('Vinculado a unidade'), $unidade);
        $table->addRowSet(new TLabel('Mostrar em calendário para:'), $calendario_options);

        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:save green');

        $this->form->setFields(array($id, $usuario, $sala_id, $data_evento, $inicio_hora, $inicio_minutos,$termino_hora,$termino_minutos,$descricao,$calendario_options,$unidade,$data_reg,$save_button));
        
        $buttons_box = new THBox;
        $buttons_box->add($save_button);

        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;




        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        parent::add($this->form);
    }

    /**
     * Save form data
     * @param $param Request
     */


    public static function onChangeActionInicio($param) //LISTA OS TIPOS DE DOC DA MANTIDA ESCOLHIDA NO COMBO
    {
     //   var_dump($param);
     //   die();
        if($param['inicio_hora']){
            if(empty($param['inicio_minutos'])){
            $obj = new StdClass;
            $obj->inicio_minutos = '00';
            TForm::sendData('form_AgendamentoSalas', $obj);
            }
        }

    }

    public static function onChangeActionTermino($param) //LISTA OS TIPOS DE DOC DA MANTIDA ESCOLHIDA NO COMBO
    {

        if($param['termino_hora']){
            if(empty($param['termino_minutos'])){
            $obj1 = new StdClass;
            $obj1->termino_minutos = '00';
            TForm::sendData('form_AgendamentoSalas', $obj1);
            }

        }

    }








    public function onSave( $param )
    {
        try
        {
           
			TTransaction::open('Felabs_DB');
				$logged  = SystemUser::newFromLogin(TSession::getValue('login'));
			TTransaction::close();
			
			TTransaction::open('Felabs_DB'); // open a transaction
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            //var_dump($logged);
			
			//die();
			
            $this->form->validate(); // validate form data
            
            $object = new AgendamentoSalas;  // create an empty object
            $data = $this->form->getData(); // get form data as array

            $data->usuario = $logged->id;
            $data->data_reg = date('Y-m-d H:i:s');

            $data->inicio = $data->data_evento.' '.$data->inicio_hora.':'.$data->inicio_minutos.':00';
            $data->termino = $data->data_evento.' '.$data->termino_hora.':'.$data->termino_minutos.':00';




            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('inicio', 'BETWEEN', $data->inicio, $data->termino), TExpression::OR_OPERATOR);
            $criteria1->add(new TFilter('termino', 'BETWEEN', $data->inicio, $data->termino), TExpression::OR_OPERATOR);
            
            $agendamentos1 = AgendamentoSalas::getObjects($criteria1);


            foreach($agendamentos1 as $agendamentos){

                if($agendamentos->sala_id == $param['sala_id']){

                    $ag[] = $agendamentos;

                }
            }


            if($data->inicio > $data->termino){

                new TMessage('error','O horário de início não pode ser maior que o horário de término');
              //  die();

            }

            elseif($data->inicio == $data->termino){

                new TMessage('error','O horário de início não pode ser igual ao horário de término');
              //  die();

            }




            elseif($ag == NULL){


            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction



            TTransaction::open('Felabs_DB'); 
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));

            $novoEvento = new CalendarEvent; //CRIA O EVENTO NO CALENDÁRIO
            $novoEvento->title = $data->descricao;
            $novoEvento->description = $data->descricao;
            $novoEvento->start_time = $data->inicio;
            $novoEvento->end_time = $data->termino;
            $novoEvento->color = $data->unidade;
            $novoEvento->system_user_id = $logged->id;
            $novoEvento->system_user_name = $logged->name;
            $novoEvento->data_reg = date('Y-m-d H:i:s');
            $novoEvento->unit = serialize($data->calendario_options); //GRAVA AS UNIDADES PARA AS QUAIS O EVENTO DEVE SER MOSTRADO

            if($data->sala_id == 1){ //GRAVA NOME NO CAMPO CALENDAR_LOCAL PARA MOSTRAR NA TMESSAGE DO CALENDÁRIO

                $novoEvento->calendar_local = 'Salão Nobre - Campus I';
            }
            elseif($data->sala_id == 2){

                $novoEvento->calendar_local = 'Poliesportivo';
            }
            elseif($data->sala_id == 3){

                $novoEvento->calendar_local = 'Quadra';
            }


            $novoEvento->store();

            TTransaction::close();


            
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), TApplication::loadPage('AgendamentoSalasList'));


            }else{

                new TMessage('info', 'Já existe outro evento marcado neste horário');
            }


       



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
                $object = new AgendamentoSalas($key); // instantiates the Active Record
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
