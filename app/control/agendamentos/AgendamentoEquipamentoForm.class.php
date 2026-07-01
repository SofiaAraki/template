<?php

class AgendamentoEquipamentoForm extends TPage
{
    protected $form;

    public function __construct( $param )
    {
        parent::__construct();
        
        // cria o formulário
        $this->form = new BootstrapFormBuilder('form_AgendamentoEquipamento');
        $this->form->setFormTitle('Agendamento de Equipamento');

        // cria os campos do formulário
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
        $loggedProfUnit = TSession::getValue('userunitid'); // PEGA A ID DA UNIDADE DO USUARIO LOGADO
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
        
        // Botões de Ação
        $this->form->addAction('Voltar', new TAction(array('AgendamentoEquipamentoList', 'onReload')), 'fa:arrow-left blue');
        $this->form->addAction('Limpar',  new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save green');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
        parent::add($container);
    }

    public static function onChangeActionZera($param) 
    {
        if(empty($param['data_evento']))
        {
            new TMessage('error','Preencha o campo Data antes de prosseguir');
            return;
        }
        
        if(!empty($param['termino']))
        {           
            $obj = new StdClass;
            $obj->termino = '00:00';
            $obj->equipamento_id = '';
            TForm::sendData('form_AgendamentoEquipamento', $obj);            
        }
    }

    public static function onChangeActionZeraData($param) 
    {
        if(!empty($param['inicio']) || !empty($param['termino']))
        {            
            $obj = new StdClass;
            $obj->inicio = '00:00';
            $obj->termino = '00:00';
            $obj->equipamento_id = '';
            TForm::sendData('form_AgendamentoEquipamento', $obj);
        }
    }

    public static function onChangeType($param)
    {
        if (empty($param['data_evento']) || empty($param['inicio']) || empty($param['termino'])) 
        {
            return;
        }

        try 
        {
            TTransaction::open('Felabs_DB');

            $data_us = TDate::date2us($param['data_evento']);
            $inicio1 = $data_us . ' ' . $param['inicio'] . ':01';
            $termino1 = $data_us . ' ' . $param['termino'] . ':00';

            if($termino1 <= $inicio1)
            {
                if($param['termino'] != '00:00')
                {
                    new TMessage('error','O horário de término não pode ser menor ou igual ao horário de início');
                    $obj = new StdClass;
                    $obj->inicio = '00:00';
                    $obj->termino = '00:00';
                    $obj->equipamento_id = '';
                    TForm::sendData('form_AgendamentoEquipamento', $obj);
                }
                TTransaction::close();
                return;
            }

            // Nova lógica anti-conflito (Intersecção de intervalos de tempo)
            $criteria2 = new TCriteria;
            $criteria2->add(new TFilter('inicio', '<', $termino1));
            $criteria2->add(new TFilter('termino', '>', $inicio1));
            
            // Se for uma edição, não deve conflitar com o próprio registro atual
            if (!empty($param['id'])) {
                $criteria2->add(new TFilter('id', '<>', $param['id']));
            }

            $agendamentos2 = AgendamentoEquipamento::getObjects($criteria2);

            $criteria3 = new TCriteria;
            foreach ($agendamentos2 as $linha)
            {
                $criteria3->add(new TFilter('id', '<>', $linha->equipamento_id), TExpression::AND_OPERATOR); 
            }

            $loggedUnit = TSession::getValue('userunitid');
            if($loggedUnit == 12) 
            {
                $loggedUnit = 2; // CNSC usa os mesmos equipamentos da FFCL
            }

            $criteria3->add(new TFilter('unidade', '=', $loggedUnit), TExpression::AND_OPERATOR);
            $criteria3->add(new TFilter('status', '=', 'S'), TExpression::AND_OPERATOR);

            $agendamentosTodosExceto = AgendamentoEquipamentoItem::getObjects($criteria3);

            $arrayEquip = [];
            foreach($agendamentosTodosExceto as $arrayEquipDisp)
            {        
                $arrayEquip[$arrayEquipDisp->id] = $arrayEquipDisp->equipamento;
            }

            TCombo::reload('form_AgendamentoEquipamento', 'equipamento_id', $arrayEquip);        
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $loggedUnitId = TSession::getValue('userunitid'); 

            $this->form->validate(); 
            
            $data = $this->form->getData(); 
            $data_us = TDate::date2us($data->data_evento);

            $object = new AgendamentoEquipamento;  
            if (!empty($data->id)) {
                $object->load($data->id);
            }

            $inicio_full = $data_us.' '.$data->inicio.':00';
            $termino_full = $data_us.' '.$data->termino.':00';

            if($inicio_full >= $termino_full)
            {
                throw new Exception('O horário de início não pode ser maior ou igual ao horário de término');
            }

            // Validação de segurança dupla ao salvar contra concorrência e sobreposição externa
            $criteria3 = new TCriteria;
            $criteria3->add(new TFilter('equipamento_id', '=', $data->equipamento_id));
            $criteria3->add(new TFilter('inicio', '<', $termino_full));
            $criteria3->add(new TFilter('termino', '>', $inicio_full));
            
            if (!empty($data->id)) {
                $criteria3->add(new TFilter('id', '<>', $data->id));
            }
            
            $conflitos = AgendamentoEquipamento::getObjects($criteria3);
            if (!empty($conflitos)) {
                throw new Exception('Este equipamento já foi reservado por outro usuário neste mesmo período.');
            }

            $object->fromArray( (array) $data); 
            $object->data_evento = $data_us;
            $object->usuario = $user->id;
            $object->data_reg = date('Y-m-d H:i:s');
            $object->unidade = $loggedUnitId;
            $object->inicio = $inicio_full;
            $object->termino = $termino_full;

            $object->store(); 
            
            $data->id = $object->id;
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), TApplication::loadPage('AgendamentoEquipamentoList'));
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
                
                // Trata os campos compostos vindos do banco de dados para o formulário
                if (!empty($object->inicio)) {
                    $object->data_evento = TDate::us2date(substr($object->inicio, 0, 10));
                    $object->inicio = substr($object->inicio, 11, 5);
                }
                if (!empty($object->termino)) {
                    $object->termino = substr($object->termino, 11, 5);
                }
                
                $this->form->setData($object);
                
                // Força o gatilho para listar o combo de equipamentos com o valor atual incluso
                $param_fake = [
                    'id' => $object->id,
                    'data_evento' => $object->data_evento,
                    'inicio' => $object->inicio,
                    'termino' => $object->termino
                ];
                self::onChangeType($param_fake);
                
                // Restaura o ID selecionado após o reload do combo
                $obj_equip = new StdClass;
                $obj_equip->equipamento_id = $object->equipamento_id;
                TForm::sendData('form_AgendamentoEquipamento', $obj_equip);

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