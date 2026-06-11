<?php
/**
 * CalendarEventForm
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class CalendarEventForm extends TWindow
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    public function __construct()
    {
        parent::__construct();
        parent::setSize(640, null);
        parent::setTitle('Editar Evento');
        
        // creates the form
        $this->form = new TForm('form_event');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 600px';
        
        // add a table inside form
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Evento') )->colspan = 2;
        
        $hours = array();
        $minutes = array();
        for ($n=0; $n<24; $n++)
        {
            $hours[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        for ($n=0; $n<=55; $n+=5)
        {
            $minutes[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        // create the form fields
        $view           = new THidden('view');
        $id             = new TEntry('id');
        $color          = new TCombo('color');
        $start_date     = new TDate('start_date');
        $start_hour     = new TCombo('start_hour');
        $start_minute   = new TCombo('start_minute');
        $end_date       = new TDate('end_date');
        $end_hour       = new TCombo('end_hour');
        $local          = new TEntry('calendar_local');
        $end_minute     = new TCombo('end_minute');
        $title          = new TEntry('title');
        $description    = new TText('description');
        $calendario_options = new TCheckGroup('calendario_options');
  

        $calendario_items=[];
        $calendario_items[0] = 'FE (Intranet - Visível para colaboradores apenas)';
        $calendario_items[1] = 'CNSC (Visível para alunos)';
        $calendario_items[2] = 'FFCL (Visível para alunos)';
        $calendario_items[3] = 'FAFRAM (Visível para alunos)';

        $calendario_options->addItems($calendario_items);

        $optionsColor = (['#3a87ad' => 'FE','#33cc33'=> 'CNSC', '#f19800' => 'FFCL','#ff0000' => 'FAFRAM','#3333ff'=>'ANGLO']);

        $color->addItems($optionsColor);
        
        $start_hour->addItems($hours);
        $start_minute->addItems($minutes);
        $end_hour->addItems($hours);
        $end_minute->addItems($minutes);
        
        $id->setEditable(FALSE);
        // define the sizes
        $id->setSize(40);
        $color->setSize(100);
        $start_date->setSize(100);
        $end_date->setSize(100);
        $start_hour->setSize(50);
        $end_hour->setSize(50);
        $local->setSize(400);
        $start_minute->setSize(50);
        $end_minute->setSize(50);
        $title->setSize(400);
        $description->setSize(400, 50);

        $start_hour->setChangeAction(new TAction(array($this, 'onChangeStartHour')));
        $end_hour->setChangeAction(new TAction(array($this, 'onChangeEndHour')));
        $start_date->setExitAction(new TAction(array($this, 'onChangeStartDate')));
        $end_date->setExitAction(new TAction(array($this, 'onChangeEndDate')));

        // add one row for each form field
        $table->addRowSet( $view );
        $table->addRowSet( new TLabel('ID:'), $id );
        $table->addRowSet( new TLabel('Unidade (cor):'), $color );
        $table->addRowSet( new TLabel('Início:'), array($start_date, $start_hour, ':', $start_minute) );
        $table->addRowSet( new TLabel('Término:'), array($end_date, $end_hour, ':', $end_minute));
        $table->addRowSet( new TLabel('Título:'), $title );
        $table->addRowSet( new TLabel('Descrição:'), $description );
        $table->addRowSet( new TLabel('Local:'), $local );
        $table->addRowSet( new TLabel('Mostrar para:'), $calendario_options );
        
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:save green');

 
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')),'Limpar');
        $new_button->setImage('fa:eraser orange');

        // create an del button (edit with no parameters)
        $del_button=new TButton('del');
        $del_button->setAction(new TAction(array($this, 'onDelete')), _t('Delete'));
        $del_button->setImage('far:trash-alt red');
        
        $this->form->setFields(array($id, $view, $color, $title, $description, $start_date, $start_hour, $start_minute, $end_date, $end_hour, $end_minute, $local,$calendario_options, $save_button,$new_button,$del_button));
        
        $buttons_box = new THBox;
        $buttons_box->add($save_button);
        $buttons_box->add($new_button);
        $buttons_box->add($del_button);
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        parent::add($this->form);
    }

    /**
     * Executed when user leaves start hour field
     */
    public static function onChangeStartHour($param=NULL)
    {
        $obj = new stdClass;
        if (empty($param['start_minute']))
        {
            $obj->start_minute = '0';
            TForm::sendData('form_event', $obj);
        }
        
        if (empty($param['end_hour']) AND empty($param['end_minute']))
        {
            $obj->end_hour = $param['start_hour'] +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end hour field
     */
    public static function onChangeEndHour($param=NULL)
    {
        if (empty($param['end_minute']))
        {
            $obj = new stdClass;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves start date field
     */
    public static function onChangeStartDate($param=NULL)
    {
        if (empty($param['end_date']) AND !empty($param['start_date']))
        {
            $obj = new stdClass;
            $obj->end_date = $param['start_date'];
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end date field
     */
    public static function onChangeEndDate($param=NULL)
    {
        if (empty($param['end_hour']) AND empty($param['end_minute']) AND !empty($param['start_hour']))
        {
            $obj = new stdClass;
            $obj->end_hour = min($param['start_hour'],22) +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('Felabs_DB');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            $this->form->validate(); // form validation
            
            // get the form data into an active record Entry
            $data = $this->form->getData();
            
            $object = new CalendarEvent;
            $object->color = $data->color;
            $object->id = $data->id;
            $object->title = $data->title;
            $object->description = $data->description;
            $object->start_time = $data->start_date . ' ' . str_pad($data->start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->start_minute, 2, '0', STR_PAD_LEFT) . ':00';
            $object->end_time = $data->end_date . ' ' . str_pad($data->end_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->end_minute, 2, '0', STR_PAD_LEFT) . ':00';
        
            $object-> system_user_id = (int)$logged-> id;
            $object-> system_user_name = $logged-> name;
            $object-> calendar_local=$data->calendar_local;
            $object-> unit = serialize($data->calendario_options);
            $object-> data_reg = date('Y-m-d H:i:s');
            
            
            $object->store(); // stores the object
            
            $data->id = $object->id;
            $this->form->setData($data); // keep form data
            
            TTransaction::close(); // close the transaction
            $posAction = new TAction(array('FullCalendarDatabaseView', 'onReload'));
            //$posAction->setParameter('view', $data->view);
            $posAction->setParameter('date', $data->start_date);
            
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $posAction);
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            $this->form->setData( $this->form->getData() ); // keep form data
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                $object= new stdClass;
                
                
                TTransaction::open('Felabs_DB');
                
                $logado = SystemUser::newFromLogin(TSession::getValue('login'));
                $object-> system_user_id = $logado-> id;
               
                $testaid=$logado->id;
                
                
                $object = new CalendarEvent($key);
                $system_user_id=new stdClass;
                $calendar_local_id=new stdClass;
                $system_user_id->system_user_id = $object->system_user_id;
                $calendar_local_id->calendar_local_id = $object->calendar_local_id;
                
              
                TTransaction::close();
                
                $verificaid=$system_user_id->system_user_id;  //id do usuario que criou o evento
                
                
                if($verificaid==$testaid){     //verifica se id do usuário logado é o mesmo que o autor do evento
                
                
                // open a transaction with database 'samples'
                TTransaction::open('Felabs_DB');
                
                // instantiates object CalendarEvent
                $object = new CalendarEvent($key);

              //  var_dump($object);
              //  die();
                
                $data = new stdClass;
                $data->id = $object->id;
                $data->color = $object->color;
                $data->title = $object->title;
                $data->description = $object->description;
                $data->start_date = substr($object->start_time,0,10);
                $data->start_hour = substr($object->start_time,11,2);
                $data->start_minute = substr($object->start_time,14,2);
                $data->end_date = substr($object->end_time,0,10);
                $data->end_hour = substr($object->end_time,11,2);
                $data->end_minute = substr($object->end_time,14,2);
                $data->calendar_local=$object->calendar_local;
                $data->calendario_options=unserialize($object->unit);

                $data->view = $param['view'];

                
                // fill the form with the active record data
                $this->form->setData($data);
                
                // close the transaction
                TTransaction::close();
                }

                else
                {
                
                $title= new stdClass;
                $description= new stdClass;
                $start_time= new stdClass;
                $end_time= new stdClass;
                $system_user_id= new stdClass;
                $calendar_local=new stdClass;
                
                // open a transaction with database 'samples'
                TTransaction::open('Felabs_DB');
                $object = new CalendarEvent($key);
                $title->title = $object->title;
                $description->description = $object->description;
                $start_time->start_time = $object->start_time;
                $end_time->end_time = $object->end_time;
                $system_user_id->system_user_id=$object->system_user_id;
                $calendar_local->calendar_local=$object->calendar_local;
           //     $system_user_name->system_user_name = $object->system_user_name;
          

                $desc=array();
                $desc[0]=$title->title;
                $desc[1]=$description->description;
                $desc[2]=$start_time->start_time;
                $desc[3]=$end_time->end_time;
            //    $desc[4]=$system_user_name->system_user_name;

                $datainicio=substr($desc[2],0,10);

                $ano=substr($datainicio,0,4);
                $mes=substr($datainicio,5,7);
                $mes1=substr($mes,0,2);
                $dia=substr($datainicio,8,10);

                $horainicio=substr($desc[2],11,17);
                $horainicio1=substr($horainicio,0,5);


                $datatermino=substr($desc[3],0,10);

                $ano1=substr($datatermino,0,4);
                $mes2=substr($datatermino,5,7);
                $mes3=substr($mes2,0,2);
                $dia1=substr($datatermino,8,10);

                $horatermino=substr($desc[3],11,17);
                $horatermino1=substr($horatermino,0,5);
                
                
                $desc[5]=(int)$system_user_id->system_user_id;
                $desc[6]=new SystemUser($desc[5]);
                $desc[7]=$desc[6]->name;
              
                $desc[8]=$calendar_local->calendar_local;
              
                TTransaction::close();
               // $desc[2]=setMask('dd-mm-yyyy ##:##:##');
              
                new TMessage('info',"<b>Título:</b> $desc[0]<br/><b>Início:</b> $dia/$mes1/$ano às $horainicio1<br/><b>Término:</b> $dia1/$mes3/$ano1 às $horatermino1<br/><b>Descrição:</b> $desc[1]<br/><b>Local:</b> $desc[8]<br/><b>Última Edição:</b> $desc[7]");
                
                //new TMessage('info',"Você não pode editar este evento. Mas você pode criar um novo evento!");
               // setEventClickAction(new TAction(array('CalendarEventUser2', 'onEdit')));
                }
            }
            else
            {
                $this->form->clear();
            }
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
     * Delete event
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array('CalendarEventForm', 'Delete'));
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
            // get the parameter $key
            $key = $param['id'];
            // open a transaction with database
            TTransaction::open('Felabs_DB');
            
            // instantiates object
            $object = new CalendarEvent($key, FALSE);
            
            // deletes the object from the database
            $object->delete();
            
            // close the transaction
            TTransaction::close();
            
            $posAction = new TAction(array('FullCalendarDatabaseView', 'onReload'));
            $posAction->setParameter('view', $param['view']);
            $posAction->setParameter('date', $param['start_date']);
            
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $posAction);
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
     * Fill form from the user selected time
     */
    public function onStartEdit($param)
    {
        $this->form->clear();
        $data = new stdClass;
        $data->view = $param['view']; // calendar view
        $data->color = '#3a87ad';
        
        if ($param['date'])
        {
            if (strlen($param['date']) == 10)
            {
                $data->start_date = $param['date'];
                $data->end_date = $param['date'];
            }
            if (strlen($param['date']) == 19)
            {
                $data->start_date   = substr($param['date'],0,10);
                $data->start_hour   = substr($param['date'],11,2);
                $data->start_minute = substr($param['date'],14,2);
                
                $data->end_date   = substr($param['date'],0,10);
                $data->end_hour   = substr($param['date'],11,2) +1;
                $data->end_minute = substr($param['date'],14,2);
            }
            $this->form->setData( $data );
        }
    }
    
    /**
     * Update event. Result of the drag and drop or resize.
     */
    public static function onUpdateEvent($param)
    {
        try
        {
            if (isset($param['id']))
            {
                // get the parameter $key
                $key=$param['id'];
                
                // open a transaction with database 'samples'
                TTransaction::open('Felabs_DB');
                
                // instantiates object CalendarEvent
                $object = new CalendarEvent($key);
                $object->start_time = str_replace('T', ' ', $param['start_time']);
                $object->end_time   = str_replace('T', ' ', $param['end_time']);
                
                //$logged = SystemUser::newFormLogin(TSession::getValue('login'));
                //$object->id_author = $logged->id;
                
                //var_dump($object);
                //die();
                
                $object->store();
                                
                // close the transaction
                TTransaction::close();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
