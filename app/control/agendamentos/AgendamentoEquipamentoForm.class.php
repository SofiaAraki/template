<?php

class AgendamentoEquipamentoForm extends TPage
{
    protected $form;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_AgendamentoEquipamento');
        $this->form->setFormTitle('Agendamento de Equipamento');
        

        // create the form fields
        $id = new THidden('id');
        $usuario = new THidden('usuario');
        $data_evento = new TDate('data_evento');
        $inicio = new TCombo('inicio');
        $termino = new TCombo('termino');
        $equipamento_id = new TCombo('equipamento_id');
        $observacoes = new TEntry('observacoes');
        $local = new TEntry('local');
        $unidade = new TEntry('unidade');
        $data_reg = new THidden('data_reg');


        TTransaction::open('Felabs_DB');

        $loggedProfUnit = TSession::getValue('userunitid'); //PEGA A ID DA UNIDADE DO USUARIO LOGADO
        $unitName = new SystemUnit($loggedProfUnit);

        TTransaction::close();


        $hours = [];
        $hours['00:00'] = '00:00';
        $hours['07:00'] = '07:00';
        $hours['08:00'] = '08:00';
        $hours['09:00'] = '09:00';
        $hours['10:00'] = '10:00';
        $hours['11:00'] = '11:00';
        $hours['12:00'] = '12:00';
        $hours['13:00'] = '13:00';
        $hours['14:00'] = '14:00';
        $hours['15:00'] = '15:00';
        $hours['16:00'] = '16:00';
        $hours['17:00'] = '17:00';
        $hours['18:00'] = '18:00';
        $hours['19:00'] = '19:00';
        $hours['20:00'] = '20:00';
        $hours['21:00'] = '21:00';
        $hours['22:00'] = '22:00';
        $hours['23:00'] = '23:00';

        $inicio->addItems($hours);
        $termino->addItems($hours);
        

        $data_evento->addValidation('Data', new TRequiredValidator);
        $inicio->addValidation('Início', new TRequiredValidator);
        $termino->addValidation('Término', new TRequiredValidator);
        $equipamento_id->addValidation('Equipamento', new TRequiredValidator);
        $local->addValidation('Local', new TRequiredValidator);


        $local->placeholder = 'Ex. Sala 1, Salão Nobre, Laboratório 1, etc.';
        $unidade->setValue($unitName->name);
        $unidade->setEditable(FALSE);
        $data_evento->setMask('dd/mm/yyyy'); 
        $data_evento->setSize('60%');
        $inicio->setSize('60%');
        $termino->setSize('60%');
        $equipamento_id->setSize('60%');
        $local->setSize('60%');
        $observacoes->setSize('60%');
        $unidade->setSize('30%');


        $inicio->setChangeAction(new TAction(array($this, 'onChangeActionZera')));
        
        $data_evento->setExitAction(new TAction(array($this, 'onChangeActionZeraData')));

        $termino->setChangeAction(new TAction(array($this, 'onChangeType')));


        $this->form->addFields([$id]);      
        $this->form->addFields([$usuario]);
        $this->form->addFields([new TLabel('Data')], [$data_evento]);
        $this->form->addFields([new TLabel('Início')], [$inicio]);        
        $this->form->addFields([new TLabel('Término')], [$termino]); 
        $this->form->addFields([new TLabel('Equipamentos disponíveis')], [$equipamento_id]);
        $this->form->addFields([new TLabel('Local')], [$local]);
        $this->form->addFields([new TLabel('Observações')], [$observacoes]);
        $this->form->addFields([new TLabel('Unidade')], [$unidade]);        
        $this->form->addFields([$data_reg]);


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        
        // create the form actions
        $btn = $this->form->addAction(('Salvar'), new TAction(array($this, 'onSave')), 'fas:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(('Novo'),  new TAction(array($this, 'onClear')), 'fas:plus green');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onChangeActionZera($param) 
    {
        if($param['data_evento'] == NULL)
        {
            new TMessage('error','Preencha o campo Data antes de prosseguir');
        }
        
        if($param['termino'])
        {           
            $obj = new StdClass;
            $obj->termino = '00:00';
            $obj->equipamento_id = '';
            //$obj->termino_minutos = '00';
            TForm::sendData('form_AgendamentoEquipamento', $obj);            
        }
    }


    public static function onChangeActionZeraData($param) 
    {
        if($param['inicio'] != NULL || $param['termino'] != NULL)
        {            
            $obj = new StdClass;
            $obj->inicio = '00:00';
            $obj->termino = '00:00';

            TForm::sendData('form_AgendamentoEquipamento', $obj);
        }
    }


    public static function onChangeType($param)
    {
        TTransaction::open('Felabs_DB');

        $param['data_evento'] = TDate::date2us($param['data_evento']);
        

        $inicio1 = $param['data_evento'].' '.$param['inicio'].':01';
        $termino1 = $param['data_evento'].' '.$param['termino'].':01';

        if($termino1 < $inicio1)
        {
            if($param['termino'] != '00:00')
            {

                new TMessage('error','O horário de término não pode ser menor que o horário de início');
                $obj = new StdClass;
                $obj->inicio = '00:00';
                
                TForm::sendData('form_AgendamentoEquipamento', $obj);
            }

        }
        else
        {
            if($param['data_evento'] && $param['inicio'])
            {
                $criteria2 = new TCriteria;
                $criteria2->add(new TFilter('inicio', 'BETWEEN', $inicio1, $termino1), TExpression::OR_OPERATOR);
                $criteria2->add(new TFilter('termino', 'BETWEEN', $inicio1, $termino1), TExpression::OR_OPERATOR);
                    
                $agendamentos2 = AgendamentoEquipamento::getObjects($criteria2); //ARRAY COM EQUIPAMENTOS JÁ RESERVADOS NA DATA E HORÁRIO ESCOLHIDO
        
          
                $criteria3 = new TCriteria;
        
                foreach ($agendamentos2 as $linha)
                {
                    $criteria3->add(new TFilter('id', '<>', $linha->equipamento_id), TExpression::AND_OPERATOR); 
                }
        
                $loggedUnit = TSession::getValue('userunitid'); //PEGA A ID DA UNIDADE DO USUARIO LOGADO
        
                if($loggedUnit == 12) //CNSC USA OS MESMOS EQUIP DA FFCL
                {
                    $loggedUnit = 2;
                }
        
                $criteria3->add(new TFilter('unidade', '=', $loggedUnit), TExpression::AND_OPERATOR);
                $criteria3->add(new TFilter('status', '=', 'S'), TExpression::AND_OPERATOR);
        
                $agendamentosTodosExceto = AgendamentoEquipamentoItem::getObjects($criteria3); //ARRAY COM TODOS EQUIPAMENTOS DA UNIDADE DO USUARIO LOGADO 
        
                $arrayEquip = [];
        
                foreach($agendamentosTodosExceto as $arrayEquipDisp)
                {        
                    $arrayEquip[$arrayEquipDisp->id] = $arrayEquipDisp->equipamento;
                }
    
                if($param['inicio'] != $param['termino'] && $param['inicio'] < $param['termino'])
                {        
                    TCombo::reload('form_AgendamentoEquipamento', 'equipamento_id', $arrayEquip);        
                }
    
                TTransaction::close();
            }
            else
            {
                new TMessage('error','Os campos data e início devem estar preenchidos');
            }
        }
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $loggedUnitId = TSession::getValue('userunitid'); //PEGA A ID DA UNIDADE DO USUARIO LOGADO

            
            $this->form->validate(); 
            
            $object = new AgendamentoEquipamento;  
            $data = $this->form->getData(); 

            $data->data_evento = TDate::date2us($data->data_evento);
            $data->usuario = $user->id;
            $data->data_reg = date('Y-m-d H:i:s');
            $data->unidade = $loggedUnitId;
            $data->inicio = $data->data_evento.' '.$data->inicio.':00';
            $data->termino = $data->data_evento.' '.$data->termino.':00';


            //$criteria3 = new TCriteria;
            //$criteria3->add(new TFilter('equipamento_id', '=', $data->equipamento_id), TExpression::AND_OPERATOR);
            //$criteria3->add(new TFilter('inicio', 'BETWEEN', $data->inicio, $data->termino), TExpression::OR_OPERATOR);
            //$criteria3->add(new TFilter('termino', 'BETWEEN', $data->inicio, $data->termino), TExpression::OR_OPERATOR);
            
            //$agendamentos3 = AgendamentoEquipamento::getObjects($criteria3); //ARRAY COM EQUIPAMENTOS QUE ESTÃO RESERVADOS NO MESMO HORÁRIO - SEGURANÇA ADICIONAL


            if($data->inicio > $data->termino)
            {
                new TMessage('error','O horário de início não pode ser maior que o horário de término');
                //die();
            }

            elseif($data->inicio == $data->termino)
            {
                new TMessage('error','O horário de início não pode ser igual ao horário de término');
                //die();
            }
        
            //elseif($agendamentos3 != NULL) //SEGURANÇA ADICIONAL PARA PREVENIR SALVAR O MESMO EQUIPAMENTO NO MESMO HORÁRIO
            
            else
            {
                $object->fromArray( (array) $data); 
                $object->store(); 
                
                $data->id = $object->id;
                
                $this->form->setData($data); 
                TTransaction::close(); 
                
                new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'),TApplication::loadPage('AgendamentoEquipamentoList'));
                }
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
                
                $object = new AgendamentoEquipamento($key);
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
}
