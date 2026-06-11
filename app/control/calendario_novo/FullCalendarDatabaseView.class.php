<?php
/**
 * FullCalendarDatabaseView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FullCalendarDatabaseView extends TPage
{
    private $fc;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->fc = new TFullCalendar(date('Y-m-d'), 'month');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));

        TTransaction::open('Felabs_DB');
        $loggedUnit = TSession::getValue('userunitid');
        $unidade = new SystemUnit($loggedUnit);
        TTransaction::close();

        

        $cabecalho = new TElement("section");
        $cabecalho->class = "content-header";
        $cabecalho->style = "padding: 0px 0px 0px 0px";
        $cabecalho->add("<h1>
        Calendário Acadêmico
        <small>Exibindo eventos para $unidade->name</small>
        </h1><br>"); //MOSTRA DE QUAL MANTIDA SÃO OS EVENTOS EXIBIDOS NO CALENDÁRIO

        parent::add( $cabecalho); 

        $this->fc->setDayClickAction(new TAction(array('CalendarEventUser', 'onEventClick')));   //ação ao clicar sobre evento
        $this->fc->setEventClickAction(new TAction(array('CalendarEventUser', 'onEdit')));   //ação ao clicar em data em branco
    //    }

        $container = new TVBox;
        $container->style = 'width: 100%';
     //   $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('', $this->fc));
        $container->add($this->fc);
        
        parent::add($container);

     //   parent::add( $this->fc );
        
    }
    
    /**
     * Output events as an json
     */
    public static function getEvents($param=NULL)
    {
        $return = array();
        try
        {
            TTransaction::open('Felabs_DB');
            $loggedUnit = TSession::getValue('userunitid');

            
            $events = CalendarEvent::where('start_time', '>=', $param['start'])
                                   ->where('end_time',   '<=', $param['end'])->load();
                                
            if ($events)
            {
                foreach ($events as $event)
                {
                    
                    $event_array = $event->toArray();

                    if($loggedUnit == 2 || $loggedUnit == 5 || $loggedUnit == 6) //ALUNOS LOGADOS COM UNIDADE NEAD OU PÓS-FFCL VERÃO EVENTOS DA FFCL
                    {
                        $loggedUnit = 2;
                    }
                    elseif($loggedUnit == 3 || $loggedUnit == 4) //.. E ALUNOS LOGADOS COM UNIDADE PÓS-FAFRAM VERÃO EVENTOS DA FAFRAM
                    {
                        $loggedUnit = 3; 
                    }

                    if(in_array($loggedUnit,unserialize($event_array['unit']))) //TRAZ APENAS EVENTOS RELACIONADOS A ENTIDADE USUÁRIO LOGADO
                    {
                        $event_array['start'] = str_replace( ' ', 'T', $event_array['start_time']);
                        $event_array['end'] = str_replace( ' ', 'T', $event_array['end_time']);
                        $return[] = $event_array;
                    }
                }
            }
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            $this->fc->setCurrentDate($param['date']);
        }
    }
}