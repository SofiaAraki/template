<?php

class AgendamentoProvaListProfessor extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        // cria o formulario de busca
        $this->form = new TQuickForm('form_search_AgendamentoProva');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table; width:100%'; 
        $this->form->setFormTitle('Agendamento de Provas');

        // campos de busca
        $id = new TEntry('id');
        $data_prova = new TDate('data_prova');
        $data_prova->setMask('dd/mm/yyyy');

        $this->form->addQuickField('Id', $id);
        $this->form->addQuickField('Data da prova', $data_prova);
        
        $this->form->setData(TSession::getValue('AgendamentoProva_filter_data'));
        
        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction('Novo Agendamento', new TAction(array('AgendamentoProvaFormList', 'onEdit')), 'fas:plus green');
        
        // Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // colunas
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Professor', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_data_prova = new TDataGridColumn('data_prova', 'Data da prova', 'left');
        $column_observacao = new TDataGridColumn('observacao', 'Observação', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_unidade = new TDataGridColumn('unidade', 'Unidade', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do envio', 'left');

        // Transformers para exibição limpa sem alterar o dado real do objeto
        $column_data_prova->setTransformer(function($value) {
            return !empty($value) ? date('d/m/Y H:i', strtotime($value)) : '';
        });

        $column_data_reg->setTransformer(function($value) {
            return !empty($value) ? date('d/m/Y H:i', strtotime($value)) : '';
        });

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_data_prova);
        $this->datagrid->addColumn($column_observacao);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_data_reg);

        // Ações da Grid
        
        // CORREÇÃO CENTRAL: Adicionado botão Editar apontando para o Formulário externo
        $action_edit = new TDataGridAction(array('AgendamentoProvaFormList', 'onEdit'));
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        //$this->datagrid->addAction($action_edit);

        $action_download = new TDataGridAction(array($this, 'onDownload'));
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel(_t('Download'));
        $action_download->setImage('fas:cloud-download-alt green fa-lg');
        $action_download->setField('id');
        $action_download->setDisplayCondition(array($this, 'displayColumn'));
        $this->datagrid->addAction($action_download);

        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $container = new TVBox;
        $container->style = 'width: 100%;';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Agendamento de Provas', $this->form));
        $container->add(TPanelGroup::pack('Meus Agendamentos', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function displayColumn($object)
    {
        return !empty($object->filename);
    }

    public function onDownload($param)
    {
        try
        {
            if (isset($param['id']))
            {
                TTransaction::open('Felabs_DB');
                $object = new AgendamentoProva($param['id']); 
                TTransaction::close();

                $filename = basename($object->filename);
                $file_path = "arquivos/provas/" . $filename;

                if (file_exists($file_path)) {
                    TPage::openFile($file_path);
                } else {
                    throw new Exception('Arquivo não encontrado no servidor.');
                }
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('AgendamentoProvaList_filter_id', null);
        TSession::setValue('AgendamentoProvaList_filter_data_prova', null);

        if (!empty($data->id)) {
            TSession::setValue('AgendamentoProvaList_filter_id', new TFilter('id', 'like', "%{$data->id}%")); 
        }

        if (!empty($data->data_prova)) {
            TSession::setValue('AgendamentoProvaList_filter_data_prova', new TFilter('data_prova', 'like', "%{$data->data_prova}%")); 
        }

        $this->form->setData($data);
        TSession::setValue('AgendamentoProva_filter_data', $data);
        
        $this->onReload(array('offset' => 0, 'first_page' => 1));
    }
    
    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $repository = new TRepository('AgendamentoProva');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));

            if (empty($param['order'])) {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('AgendamentoProvaList_filter_id')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_id')); 
            }
            if (TSession::getValue('AgendamentoProvaList_filter_data_prova')) {
                $criteria->add(TSession::getValue('AgendamentoProvaList_filter_data_prova')); 
            }

            $objects = $repository->load($criteria, false);
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {                    
                    TTransaction::open('dados_fei');
                    $criteria1 = new TCriteria;
                    $criteria1->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
                    $disciplinaInfo = VwProfessordisciplinassemestre::getObjects($criteria1);
                    TTransaction::close();

                    $object->disciplina = !empty($disciplinaInfo) ? $disciplinaInfo[0]->NomeDisciplina : 'Disciplina não encontrada'; 
                    
                    try {
                        TTransaction::open('Felabs_DB');
                        $unidadeInfo = new SystemUnit($object->unidade);
                        $object->unidade = $unidadeInfo->name;
                    } catch (Exception $e) {
                        $object->unidade = 'Unidade não encontrada';
                    }

                    $this->datagrid->addItem($object);
                }
            }
            
            TTransaction::open('Felabs_DB');
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
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param);
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            TTransaction::open('Felabs_DB'); 
            
            $object = new AgendamentoProva($key, false); 
            $object->delete(); 
            
            TTransaction::close(); 
            $this->onReload($param); 
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    public function show()
    {
        if (!$this->loaded and (!isset($_GET['method']) or !(in_array($_GET['method'], array('onReload', 'onSearch')))) ) {
            $this->onReload(func_num_args() > 0 ? func_get_arg(0) : null);
        }
        parent::show();
    }
}
