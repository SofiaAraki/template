<?php

class AgendamentoEquipamentoItemFormList extends TPage
{
    protected $form;
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        // Cria o formulário usando BootstrapFormBuilder (padrão atual e limpo)
        $this->form = new BootstrapFormBuilder('form_AgendamentoEquipamentoItem');
        $this->form->setFormTitle('Cadastro de Equipamentos');

        // Cria os campos do formulário
        $id = new THidden('id');
        $equipamento = new TEntry('equipamento');
        $unidade = new TCombo('unidade');
        $status = new THidden('status');
        $imagem = new TFile('imagem');
        $marca = new TEntry('marca');
        $modelo = new TEntry('modelo');
        $numero_serie = new TEntry('numero_serie');
        $patrimonio = new TEntry('patrimonio');
        $identificador = new TEntry('identificador');
        $data_aquisicao = new TDate('data_aquisicao');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');

        // Configuração do componente de data
        $data_aquisicao->setMask('dd/mm/yyyy');

        // Itens de Unidade
        $items = array();
        $items['0'] = 'FE';
        $items['1'] = 'CNSC';
        $items['2'] = 'FFCL';
        $items['3'] = 'FAFRAM';
        $items['8'] = 'VAN GOGH';
        $items['12'] = 'CONNEXT';

        $unidade->addItems($items);

        // Validações obrigatórias básicas
        $equipamento->addValidation('Equipamento', new TRequiredValidator);
        $unidade->addValidation('Unidade', new TRequiredValidator);

        // Adiciona os campos ao formulário usando linhas organizadas
        $this->form->addFields([$id], [$status], [$system_user_id], [$data_reg]);
        
        $this->form->addFields([new TLabel('Equipamento')], [$equipamento], [new TLabel('Unidade')], [$unidade]);
        $this->form->addFields([new TLabel('Marca')], [$marca], [new TLabel('Modelo')], [$modelo]);
        $this->form->addFields([new TLabel('Número de Série')], [$numero_serie], [new TLabel('Patrimônio FE')], [$patrimonio]);
        $this->form->addFields([new TLabel('Identificador')], [$identificador], [new TLabel('Data de Aquisição')], [$data_aquisicao]);
        $this->form->addFields([new TLabel('Imagem')], [$imagem]);

        // Definição de tamanhos padrão
        $id->setSize('100%');
        $equipamento->setSize('100%');
        $unidade->setSize('100%');
        $marca->setSize('100%');
        $modelo->setSize('100%');
        $numero_serie->setSize('100%');
        $patrimonio->setSize('100%');
        $identificador->setSize('100%');
        $data_aquisicao->setSize('100%');
        $imagem->setSize('100%');

        // Ações do formulário
        $this->form->addAction(_t('Clear'), new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save green');
        
        // Cria a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // Colunas da datagrid
        $column_id = new TDataGridColumn('id', 'Id', 'center', '50px');
        $column_equipamento = new TDataGridColumn('equipamento', 'Equipamento', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_status = new TDataGridColumn('status', 'Ativo', 'center');
        $column_marca = new TDataGridColumn('marca', 'Marca', 'left');
        $column_modelo = new TDataGridColumn('modelo', 'Modelo', 'left');
        $column_identificador = new TDataGridColumn('identificador', 'Identificador', 'left');

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_equipamento);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_marca);
        $this->datagrid->addColumn($column_modelo);
        $this->datagrid->addColumn($column_identificador);

        // Ações da datagrid
        $action = new TDataGridAction(array('AgendamentoEquipamentoItemFormView', 'onEdit'));
        $action->setLabel(_t('View'));
        $action->setImage('fa:search green fa-lg');
        $action->setField('id');

        $action1 = new TDataGridAction(array($this, 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');

        $action_onoff = new TDataGridAction(array($this, 'onTurnOnOff'));
        $action_onoff->setLabel(_t('Activate/Deactivate'));
        $action_onoff->setImage('fa:power-off fa-lg orange');
        $action_onoff->setField('id');
        
        $this->datagrid->addAction($action);
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action_onoff);
        
        $this->datagrid->createModel();
        
        // Paginação
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // Construção do Container da Página
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        
        $alerta = new TAlert('warning', "Atenção: Usuários só poderão visualizar e agendar equipamentos da unidade que escolheram no momento do login. Escolha a unidade FE para permitir agendamentos a partir de outras unidades.");
        $container->add($alerta);
        
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        parent::add($container);
    }

    public function onTurnOnOff($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $equip = AgendamentoEquipamentoItem::find($param['id']);

            if ($equip->status == 'N')
            {
                $equip->status = 'S';
            }
            else
            {
                $equip->status = 'N';
            }
            
            $equip->store();
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('AgendamentoEquipamentoItem');
            $limit = 10;
            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('AgendamentoEquipamentoItem_filter'))
            {
                $criteria->add(TSession::getValue('AgendamentoEquipamentoItem_filter'));
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                // Mapeamento exato de badges para exibição na grid
                $unidades_map = [
                    '0'  => '<span class="label label-primary">FE</span>',
                    '1'  => '<span class="label label-info">CNSC</span>',
                    '2'  => '<span class="label label-warning">FFCL</span>',
                    '3'  => '<span class="label label-danger">FAFRAM</span>',
                    '8'  => '<span class="label label-default">VAN GOGH</span>',
                    '12' => '<span class="label label-success">CONNEXT</span>'
                ];

                foreach ($objects as $object)
                {
                    $object->unidade = isset($unidades_map[$object->unidade]) ? $unidades_map[$object->unidade] : $object->unidade;

                    if($object->status == 'N')
                    {
                        $object->status = '<span class="label label-danger">Não</span>';
                    }
                    elseif($object->status == 'S')
                    {
                        $object->status = '<span class="label label-success">Sim</span>';
                    }
                    elseif($object->status == 'E')
                    {
                        $object->status = '<span class="label label-warning">Excluir</span>';
                    }

                    $object->data_reg = TDate::date2br($object->data_reg);
                    $object->data_aquisicao = TDate::date2br($object->data_aquisicao);
                    
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
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param);
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }

    public function Delete($param)
    {
        try
        {
            $key = $param['key'];
            TTransaction::open('Felabs_DB');
            
            $object = new AgendamentoEquipamentoItem($key, FALSE);
            $object->delete();
            
            TTransaction::close(); 
            
            $this->onReload($param);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
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

            $this->form->validate();

            $data = $this->form->getData();
            $object = new AgendamentoEquipamentoItem;
            
            if (!empty($data->id))
            {
                // Se for edição, carrega o objeto existente para preservar status e histórico
                $object->load($data->id);
            }
            else
            {
                // Se for novo registro, atribui os dados de auditoria iniciais
                $object->status = 'S';
                $object->data_reg = date('Y-m-d H:i:s');
                $object->system_user_id = $user->id;
            }

            // Clona e move os campos vindos do formulário para o Active Record
            $object->fromArray((array) $data);
            $object->data_aquisicao = TDate::date2us($data->data_aquisicao);

            // Tratamento e renomeação do arquivo de imagem carregado
            if(!empty($object->imagem))
            {
                $source_file = 'tmp/'.$object->imagem;
                if (file_exists($source_file))
                {
                    $better_token = md5(uniqid(rand(), true));
                    $partes = explode(".", $object->imagem);
                    $extensaoPonto = '.' . end($partes);
        
                    $nomeFoto = $better_token . '_' . $user->id . $extensaoPonto;
                    $target_dir = 'app/images/equipamentos';
                    
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
                    
                    $target_file = $target_dir . '/' . $nomeFoto;
                    rename($source_file, $target_file);
                    
                    $object->imagem = $nomeFoto;
                }
            }
            else if (!empty($data->id))
            {
                // Se o campo imagem veio vazio numa edição, mantém a imagem que já estava no banco
                $objeto_antigo = new AgendamentoEquipamentoItem($data->id);
                $object->imagem = $objeto_antigo->imagem;
            }

            $object->store();
            
            $data->id = $object->id;
            $this->form->setData($data);
            
            TTransaction::close();
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            $this->onReload();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
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
                
                $object = new AgendamentoEquipamentoItem($key);
                $object->data_aquisicao = TDate::date2br($object->data_aquisicao);
                
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

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload(func_get_arg(0));
        }
        parent::show();
    }
}