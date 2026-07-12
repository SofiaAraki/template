<?php
class ProgramaEnsinoDisciplinaListAll extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_ProgramaEnsinoDisciplina');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        
        
        //Critério para buscar disciplina pelo nome
        TTransaction::open('Felabs_DB');
        
        $disciplinas_programa_ensino = ProgramaEnsinoDisciplina::getObjects();
        foreach($disciplinas_programa_ensino as $disciplina_programa_ensino)
        {
            $disciplinas[$disciplina_programa_ensino->disciplina] = $disciplina_programa_ensino->disciplina;
        }

        TTransaction::close();


        TTransaction::open('dados_fei');
        
        $unit_id = TSession::getValue('userunitid');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodEntidade', '=', $unit_id));
        $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', 'IN', $disciplinas));

        TTransaction::close();


        // create the form fields
        $curso = new TEntry('curso');
        $disciplina = new TDBCombo('disciplina', 'dados_fei', 'VwProfessordisciplinassemestre', 'CodGradeDisciplinaEtapaFrente', '{NomeDisciplina} - {Identificacao}', 'NomeDisciplina', $criteria);
        $disciplina->enableSearch();

        // add the fields
        $this->form->addQuickField('Curso', $curso, '50%');
        $this->form->addQuickField('Disciplina', $disciplina, '50%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ProgramaEnsinoDisciplina_filter_data') );

        // add the search form actions
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search blue');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Professor', 'left');
        //$column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        //$column_codigo = new TDataGridColumn('codigo', 'Codigo', 'left');
        //$column_obg_optativa = new TDataGridColumn('obg_optativa', 'Obg Optativa', 'left');
        //$column_periodo = new TDataGridColumn('periodo', 'Periodo', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        //$this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        //$this->datagrid->addColumn($column_codigo);
        //$this->datagrid->addColumn($column_obg_optativa);
        //$this->datagrid->addColumn($column_periodo);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_data_reg);

        // Altere a ação para apontar para o onPrint na própria Listagem
        $action_pdf = new TDataGridAction(array('ProgramaEnsinoDisciplinaListAll', 'onPrint'));
        $action_pdf->setLabel('Imprimir PDF');
        $action_pdf->setImage('far:file-pdf red');
        $action_pdf->setField('id');
        $this->datagrid->addAction($action_pdf);

        // Ação: Editar Registro
        $action_edit = new TDataGridAction(array('ProgramaEnsinoDisciplinaForm', 'onEdit'));
        $action_edit->setLabel('Editar');
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
       
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TPanelGroup::pack('Listagem Geral - Programa de Ensino da Disciplina', $this->form));
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

                // Define as larguras das colunas da tabela
                $widths = array(270, 270);
                $designer = new TTableWriterPDF($widths);
                
                // Definição de Estilos (Fontes, Tamanhos, Alinhamentos e Cores)
                $designer->addStyle('header_title', 'Helvetica', 12, 'B', '#FFFFFF', '#3b5998');
                $designer->addStyle('header_sub',   'Helvetica', 9,  '',  '#000000', '#FFFFFF');
                $designer->addStyle('table_head',   'Helvetica', 10, 'B', '#000000', '#C0C0C0');
                $designer->addStyle('label',        'Helvetica', 10, 'B', '#000000', '#FFFFFF');
                $designer->addStyle('value',        'Helvetica', 10, '',  '#000000', '#FFFFFF');
                $designer->addStyle('text_block_b', 'Helvetica', 11, 'B', '#000000', '#FFFFFF');
                $designer->addStyle('text_block_p', 'Helvetica', 10, '',  '#333333', '#FFFFFF', 'LR');

                // --- Helper interno para codificação compatível com PHP 8.2+ ---
                $toIso = function($text) {
                    return mb_convert_encoding($text ?? '', 'ISO-8859-1', 'UTF-8');
                };

                // --- 1. CABEÇALHO INSTITUCIONAL ---
                $designer->addRow();
                $designer->addCell($toIso('FACULDADE DR. FRANCISCO MAEDA - FAFRAM'), 'center', 'header_title', 2);
                
                // --- 2. PRIMEIRA TABELA: DADOS DO CURSO E DISCIPLINA ---
                $designer->addRow();
                $designer->addCell($toIso('PROGRAMA DE ENSINO DA DISCIPLINA'), 'center', 'table_head', 2);
                
                $designer->addRow();
                $designer->addCell($toIso('Curso: ' . $object->curso), 'left', 'value', 2);
                $designer->addRow();
                $designer->addCell($toIso('Disciplina: ' . $object->disciplina), 'left', 'value', 2);
                $designer->addRow();
                $designer->addCell($toIso('Professor Responsável: ' . $object->system_user_id), 'left', 'value', 2);
                
                $designer->addRow();
                $designer->addCell($toIso('Código: ' . ($object->codigo ?? '')), 'left', 'value', 1);
                $designer->addCell($toIso('Obrigatória/Optativa: ' . ($object->obg_optativa ?? '')), 'left', 'value', 1);
                
                $designer->addRow();
                $designer->addCell($toIso('Pré-Requisitos: ' . ($object->pre_requisito ?? '')), 'left', 'value', 1);
                $designer->addCell($toIso('Correquisitos: ' . ($object->co_requisito ?? '')), 'left', 'value', 1);
                
                $designer->addRow();
                $designer->addCell($toIso('Período: ' . ($object->periodo ?? '')), 'left', 'value', 1);
                $designer->addCell($toIso('Semestral/Anual: ' . ($object->semestral_anual ?? '')), 'left', 'value', 1);

                // --- 3. SEGUNDA TABELA: CARGA HORÁRIA ---
                $designer->addRow();
                $designer->addCell($toIso('Carga Horária'), 'center', 'table_head', 2);
                $designer->addRow();
                $designer->addCell($toIso('Crédito: ' . ($object->credito ?? '') . '   |   Total: ' . ($object->total ?? '') . '   |   Semanal: ' . ($object->semanal ?? '')), 'center', 'value', 2);
                
                $designer->addRow();
                $designer->addCell($toIso('Distribuição Carga Horária Semanal'), 'center', 'table_head', 2);
                $designer->addRow();
                $designer->addCell($toIso('Teórica: ' . ($object->teorica ?? '') . '   |   Prática: ' . ($object->pratica ?? '') . '   |   Teórica/Prática: ' . ($object->pratica ?? '')), 'center', 'value', 2);

                // --- 4. BLOCOS TEXTUAIS DINÂMICOS (ESTILO CAIXA INTEGRADA SEM LINHAS INTERNAS) ---
                $blocosTextuais = [
                    'Ementa: (Tópicos que caracterizam. Unidades dos programas de ensino)' => $object->ementa ?? '',
                    'Objetivos: (Ao término da disciplina o aluno deverá ser capaz de: )'   => $object->objetivos ?? '',
                    'Conteúdo Programático: (Título e discriminação das unidades)'          => $object->conteudo_programatico ?? '',
                    'Metodologia de Ensino:'                                               => $object->metodologia ?? '',
                    'Critérios de Avaliação de Aprendizagem:'                              => $object->criterio_avaliacao ?? '',
                    'Bibliografia Básica:'                                                 => $object->bibliografia_basica ?? '',
                    'Bibliografia Complementar:'                                           => $object->bibliografia_complementar ?? ''
                ];

                foreach ($blocosTextuais as $titulo => $conteudo) {
                    // 1. Título da Seção (Gera a linha normal com fundo e bordas)
                    $designer->addRow();
                    $designer->addCell($toIso($titulo), 'left', 'text_block_b', 2);
                    
                    // Limpa tags HTML do banco
                    $textoLimpo = strip_tags(html_entity_decode($conteudo));
                    
                    $textoQuebrado = wordwrap($textoLimpo, 100, "\n", true);
                    $linhasDeTexto = explode("\n", $textoQuebrado);

                    // 2. Renderiza cada linha de conteúdo aplicando apenas as bordas laterais
                    foreach ($linhasDeTexto as $linha) {
                        if (trim($linha) != '') {
                            $designer->addRow();
                            // Usando a variação nativa do addCell: passamos os parâmetros para simular apenas as laterais.
                            // Caso o Adianti force a grade padrão pelo estilo, a forma mais segura é reduzir
                            // o preenchimento ou usar células limpas.
                            $designer->addCell($toIso($linha), 'left', 'text_block_p', 2);
                        }
                    }
                }

                // --- 5. TABELA DE ASSINATURA ---
                $designer->addRow();
                $designer->addCell($toIso('Data / Assinatura'), 'center', 'table_head', 2);
                
                $designer->addRow();
                $designer->addCell("\n\n_________________________________________", 'center', 'value', 2);

                // Gravação e saída do documento
                $document = 'tmp/'.uniqid().'.pdf'; 
                $designer->save($document);

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

        if (isset($data->curso) AND ($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_curso', $filter); 
        }


        if (isset($data->disciplina) AND ($data->disciplina)) {
            $filter = new TFilter('disciplina', 'like', "%{$data->disciplina}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_disciplina', $filter);
        }


        if (isset($data->periodo) AND ($data->periodo)) {
            $filter = new TFilter('periodo', 'like', "%{$data->periodo}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_periodo', $filter); 
        }


        if (isset($data->data_reg) AND ($data->data_reg)) {
            $filter = new TFilter('data_reg', 'like', "%{$data->data_reg}%"); 
            TSession::setValue('ProgramaEnsinoDisciplinaList_filter_data_reg', $filter);
        }

        
        $this->form->setData($data);
        
        TSession::setValue('ProgramaEnsinoDisciplina_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $loggedUnit = TSession::getValue('userunitid');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            $repository = new TRepository('ProgramaEnsinoDisciplina');
            $limit = 10;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unidade', '=', $loggedUnit));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

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
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    TTransaction::open('dados_fei');

                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));

                    $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria2);
                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;

                    TTransaction::close();

                    $object->data_reg = TDate::date2br($object->data_reg);
                    
                    if($disciplinaNome[0]->CodEntidade == $loggedUnit) //SE REGISTRO PERTENCE A UNIDADE DO USUÁRIO LOGADO
                    {
                        $this->datagrid->addItem($object);
                    }
                    
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
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
