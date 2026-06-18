<?php

/**
 * ProgramaEnsinoDisciplinaList Listing
 * Organizado e otimizado para o Adianti Framework v8.5+
 */
class ProgramaEnsinoDisciplinaList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;    

    public function __construct()
    {
        parent::__construct();
        
        // --- CONFIGURAÇÃO DO FORMULÁRIO DE BUSCA ---
        $this->form = new TQuickForm('form_search_ProgramaEnsinoDisciplina');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table; width:100%'; 
        $this->form->setFormTitle('Programa de Ensino da Disciplina');
        
        // Campos de busca mantidos em sessão (descomente caso vá reativar os inputs em tela)
        $curso = new TEntry('curso');
        $disciplina = new TEntry('disciplina');
        
        $this->form->setData(TSession::getValue('ProgramaEnsinoDisciplina_filter_data'));
        
        // Ações do Formulário
        $this->form->addQuickAction('Cadastrar Novo Plano', new TAction(array('ProgramaEnsinoDisciplinaForm', 'onClear')), 'fa:plus green');
        
        // --- CONFIGURAÇÃO DA DATAGRID ---
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // Colunas da Datagrid
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Professor', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_reg);

        // Altere a ação para apontar para o onPrint na própria Listagem
        $action_pdf = new TDataGridAction(array('ProgramaEnsinoDisciplinaList', 'onPrint'));
        $action_pdf->setButtonClass('btn btn-default btn-sm');
        $action_pdf->setLabel('Imprimir PDF');
        $action_pdf->setImage('far:file-pdf red'); // Ícone mais intuitivo para PDF
        $action_pdf->setField('id');
        $this->datagrid->addAction($action_pdf);

        // Ação: Editar Registro
        $action_edit = new TDataGridAction(array('ProgramaEnsinoDisciplinaForm', 'onEdit'));
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        $this->datagrid->createModel();
        
        // --- CONFIGURAÇÃO DA PAGINAÇÃO ---
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // --- MONTAGEM DO LAYOUT CONTAINER ---
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Listagem - Programa de Ensino da Disciplina', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public static function onPrint($param)
    {
        try
        {
            if (empty($param['key'])) return;

            TTransaction::open('Felabs_DB');            
            $object = ProgramaEnsinoDisciplina::find($param['key']);

            if ($object)
            {
                $object->data_reg = TDate::date2br($object->data_reg);
                $userName = new SystemUser($object->system_user_id);
                $object->system_user_id = $userName->name;

                // Busca o nome legível da disciplina na base legada
                TTransaction::open('dados_fei');
                $criteria = new TCriteria;
                $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
                $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria);
                if (!empty($disciplinaNome)) {
                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;
                }
                TTransaction::close();

                // --- CORREÇÃO: Fallback seguro para evitar caminhos nulos se a unidade for nula ---
                $unidadeId = isset($object->unidade) ? (int)$object->unidade : 0;

                $template = ($unidadeId !== 2 && $unidadeId !== 10) 
                    ? 'app/documents/ProgramaEnsinoDisciplina.html' 
                    : 'app/documents/ProgramaEnsinoDisciplinaFFCL.html';

                // Validação extra para garantir que o arquivo fisicamente existe antes de passar para o Adianti Parser
                if (!file_exists($template)) {
                    throw new Exception("O arquivo de template obrigatório não foi localizado em: {$template}");
                }

                $html = new AdiantiHTMLDocumentParser($template, 'A4', 'portrait');
                $html->setMaster($object);
                $html->process();
                $output = $html->getContents();
            
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);

                // Abre o PDF em popup diretamente na tela do usuário
                $window = TWindow::create('Programa de Ensino', 0.8, 0.8);
                $element = new TElement('object');
                $element->data  = 'download.php?file='.$document;
                $element->type  = 'application/pdf';
                $element->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($element);
                $window->show();
            }

            TTransaction::close();
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
        
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_curso', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_disciplina', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_periodo', NULL);
        TSession::setValue('ProgramaEnsinoDisciplinaList_filter_data_reg', NULL);

        if (isset($data->curso) && !empty($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_curso', $filter);
        }

        if (isset($data->disciplina) && !empty($data->disciplina)) {
            $filter = new TFilter('disciplina', 'like', "%{$data->disciplina}%");
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_disciplina', $filter);
        }

        if (isset($data->periodo) && !empty($data->periodo)) {
            $filter = new TFilter('periodo', 'like', "%{$data->periodo}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_periodo', $filter);
        }

        if (isset($data->data_reg) && !empty($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_data_reg', $filter);
        }

        $this->form->setData($data);
        TSession::setValue('ProgramaEnsinoDisciplina_filter_data', $data);
        
        $param = array();
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);    
            $loggedUnit = TSession::getValue('userunitid');
            
            $repository = new TRepository('ProgramaEnsinoDisciplina');
            $limit = 10;
           
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', $user->id));
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            // Aplicação dos Filtros de Sessão
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_curso')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_curso')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_disciplina')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_disciplina')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_obg_optativa')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_periodo')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_periodo')); 
            }
            if (TSession::getValue('ProgramaEnsinoDisciplinaList_filter_data_reg')) {
                $criteria->add(TSession::getValue('ProgramaEnsinoDisciplinaList_filter_data_reg')); 
            }

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                // Para evitar abrir e fechar conexões repetidamente dentro do loop (Conexão Aninhada Crítica)
                // Coletamos os IDs de disciplina para buscar os nomes de uma só vez
                $codsFrente = array_filter(array_map(function($o) { return $o->disciplina; }, $objects));
                $nomesDisciplinas = [];

                if (!empty($codsFrente)) {
                    TTransaction::open('dados_fei');
                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', 'IN', $codsFrente));
                    $disciplinaNomesObjs = VwProfessordisciplinassemestre::getObjects($criteria2);
                    if ($disciplinaNomesObjs) {
                        foreach ($disciplinaNomesObjs as $disObj) {
                            $nomesDisciplinas[$disObj->CodGradeDisciplinaEtapaFrente] = $disObj->NomeDisciplina;
                        }
                    }
                    TTransaction::close();
                }

                // População final da grid
                foreach ($objects as $object)
                {
                    $object->disciplina = isset($nomesDisciplinas[$object->disciplina]) ? $nomesDisciplinas[$object->disciplina] : $object->disciplina;
                    $object->data_reg = TDate::date2br($object->data_reg);
                    
                    $this->datagrid->addItem($object);
                }
            }
            
            // Contagem para a paginação retornar dados precisos
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

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'], array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0) {
                $this->onReload(func_get_arg(0));
            } else {
                $this->onReload();
            }
        }
        parent::show();
    }
}