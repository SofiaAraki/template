<?php
class CalendarEventUser extends TPage
{
    
    public static function onEventClick($param)   //Ao clicar em área em branco do calendário
    {
	new TMessage('info',"Não há informações disponíveis.<br/>Clique em um evento para visualizar suas informações.");
    }
    
    public static function onEdit($param)   //Ao clicar em sobre evento
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                $title= new stdClass;
                $description= new stdClass;
                $start_time= new stdClass;
                $end_time= new stdClass;
              //  $system_user_id= new stdClass;
                $calendar_local=new stdClass;
                
                // open a transaction with database 'samples'
                TTransaction::open('Felabs_DB');
                $object = new CalendarEvent($key);
                $title->title = $object->title;
                $description->description = $object->description;
                $start_time->start_time = $object->start_time;
                $end_time->end_time = $object->end_time;
            //    $system_user_id->system_user_id=$object->system_user_id;
                $calendar_local->calendar_local=$object->calendar_local;
           //    $system_user_name->system_user_name = $object->system_user_name;
           //    TTransaction::close();
                
                

                $desc=array();
                $desc[0]=$title->title;
                $desc[1]=$description->description;
                $desc[2]=$start_time->start_time;
                $desc[3]=$end_time->end_time;
            //    $desc[4]=$system_user_name->system_user_name;

                $datainicio = TDate::date2br($desc[2]);
                $datatermino = TDate::date2br($desc[3]);


                $horainicio=substr($desc[2],11,17);
                $horainicio1=substr($horainicio,0,5);

                $horatermino=substr($desc[3],11,17);
                $horatermino1=substr($horatermino,0,5);

                
                

                $desc[6]=new SystemUser($desc[5]);
                $desc[7]=$desc[6]->name;
              
                $desc[8]=$calendar_local->calendar_local;
              
                TTransaction::close();

              
                new TMessage('info',"<b>Título:</b> $desc[0]<br/><b>Início:</b> $datainicio às $horainicio1<br/><b>Término:</b> $datatermino às $horatermino1<br/><b>Descrição:</b> $desc[1]<br/><b>Local:</b> $desc[8]");
                
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
    
}