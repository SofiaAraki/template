<?php

class AgendamentoEquipamentoItemFormView extends TPage
{

    public function onEdit( $param )
    {
        try
        {
            $data = (object) $param;
            
            $html = new THtmlRenderer('app/resources/agendamentoequipamentoitemformview.html');
            
            parent::include_css('app/resources/styles.css');
            
            TTransaction::open('Felabs_DB');
            
            if (isset($data->id))
            {
                $object = AgendamentoEquipamentoItem::find( $data->id );
                
                if ($object)
                {
                    $array_object = $object->toArray();
                    
                    $html->enableSection('main',  $array_object);
                }
                else
                {
                    throw new Exception('AgendamentoEquipamentoItem not found');
                }
            }
            
            TTransaction::close();
            
            
            // vertical box container
            $container = new TVBox;
            // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            parent::add($container);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
