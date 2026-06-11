<?php

//use Jeidison\JSignPDF\JSignPDF;
//use Jeidison\JSignPDF\Sign\JSignParam;

class AtividadeComplementarSecretariaList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct()
    {
        parent::__construct();
        
        
        $loggedUnit = TSession::getValue('userunitid');
        
        if($loggedUnit <> 2 AND $loggedUnit <> 3 AND $loggedUnit <> 10 AND $loggedUnit <> 6)
        {
            new TMessage('error', 'Funcionalidade não disponível para esta unidade');
            die;
        }
                
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_AtividadeComplementar');
        $this->form->setFormTitle('<h4>Atividades Complementares</h4>');
        

        // create the form fields
        $nome_aluno = new TEntry('nome_aluno');
        $nome_curso = new TEntry('nome_curso');
        $cod_prof_responsavel = new TEntry('cod_prof_responsavel');        
        $status_atividade = new TEntry('status_atividade');


        // add the fields
        $this->form->addFields( [ new TLabel('Aluno') ], [ $nome_aluno ] );
        $this->form->addFields( [ new TLabel('Curso') ], [ $nome_curso ] );
        $this->form->addFields( [ new TLabel('Responsável pela aprovação') ], [ $cod_prof_responsavel ] );
        $this->form->addFields( [ new TLabel('Status') ], [ $status_atividade ] );


        // set sizes
        $nome_aluno->setSize('80%');
        $nome_curso->setSize('80%');
        $cod_prof_responsavel->setSize('80%');
        $status_atividade->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Adicionar atividade', new TAction(['AtividadeComplementarSecretariaForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        $this->datagrid->setGroupColumn('nome_aluno', '<b>{nome_aluno} - HORAS PENDENTES: {CalcularHorasPendentes} horas - HORAS INTEGRALIZADAS: {CalcularHorasAprovadas} horas</b>');
        

        // creates the datagrid columns
        $column_nome_aluno = new TDataGridColumn('nome_aluno', 'Aluno', 'left');
        $column_nome_curso = new TDataGridColumn('nome_curso', 'Curso', 'left');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'center');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'CH', 'center');
        $column_cod_prof_responsavel = new TDataGridColumn('cod_prof_responsavel', 'Responsável pela aprovação', 'left');
        $column_status_atividade = new TDataGridColumn('status_atividade', 'Status', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Registrado em', 'center');


        $column_cod_prof_responsavel->setTransformer( array($this, 'setNomeResponsavel') );
        $column_status_atividade->setTransformer( array($this, 'setStatusColor') );    
    

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_nome_aluno);
        $this->datagrid->addColumn($column_nome_curso);
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_carga_horaria);
        $this->datagrid->addColumn($column_cod_prof_responsavel);
        $this->datagrid->addColumn($column_status_atividade);
        $this->datagrid->addColumn($column_data_reg);

            
        $action_admin = new TDataGridAction(['AtividadeComplementarAdminForm', 'onEdit'], ['id'=>'{id}']);
        $action_visualizar = new TDataGridAction(['AtividadeComplementarAnalisadaProfessorFormView', 'onEdit'], ['id'=>'{id}']);        
        $action_download = new TDataGridAction([$this, 'onDownload'], ['id'=>'{id}']);
        //$action_pdfa = new TDataGridAction([$this, 'onQuestionConverterPDF'], ['id'=>'{id}']);
        //$action_assinar = new TDataGridAction([$this, 'onAssinar'], ['id'=>'{id}']);
        $action_delete = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        
        $action_admin->setLabel('Editar atividade');
        $action_admin->setImage('fas: fa-user-lock red');
        $action_admin->setDisplayCondition( array($this, 'displayColumnEditarAtividadeAdmin') );
        
        $action_visualizar->setLabel('Visualizar');
        $action_visualizar->setImage('fa:search green');
        
        $action_download->setLabel('Download');
        $action_download->setImage('fas:cloud-download-alt blue');
        
        //$action_pdfa->setLabel('Converter em PDF/A');
        //$action_pdfa->setImage('far: fa-file-pdf');
        
        //$action_assinar->setLabel('Assinar com certificado');
        //$action_assinar->setImage('fas: fa-signature #000080');
        //$action_assinar->setDisplayCondition( array($this, 'displayColumnAssinar') );
        
        $action_delete->setLabel(_t('Delete'));
        $action_delete->setImage('far:trash-alt red');
        $action_delete->setDisplayCondition( array($this, 'displayColumnDelete') );
        
        
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');        
                
        $action_group->addAction($action_admin);
        $action_group->addAction($action_visualizar);
        $action_group->addAction($action_download);
        //$action_group->addAction($action_pdfa);
        //$action_group->addAction($action_assinar);
        $action_group->addAction($action_delete); 
                     
        $this->datagrid->addActionGroup($action_group);
  
  
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    
    //Se o usuário logado é do grupo Admin, exibe opção
    public function displayColumnEditarAtividadeAdmin($object)
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');
                
        if(( in_array($grupo_admin, $user_groups)))
        {
            return TRUE;
        }
            return FALSE;
    }
    
    
    public function setNomeResponsavel($column_cod_prof_responsavel, $object, $row)
    {
        try
        {
            $cod_prof = $object->cod_prof_responsavel;            
            
            TTransaction::open('dados_fei');           
            
            $responsavel = new FiProfessor($cod_prof);

            TTransaction::close();


            return $responsavel->Nome;            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function setStatusColor($column_status_atividade, $object, $row)
    {
        $color = $object->status_atividade;
        
        if($color == "Aguardando aprovação")
        {
            return '<span class="label label-warning">' . $column_status_atividade . '</span>';
        }
        elseif($color == "Aprovado")
        {
            return '<span class="label label-success">' . $column_status_atividade . '</span>';
        }
        elseif($color == "Reprovado")
        {
            return '<span class="label label-danger">' . $column_status_atividade . '</span>';
        }
        else
        {
            return $column_status_atividade;
        }    
    }
    
    
    public static function onDownload($param)
    {
        try
        {
            $id = $param['id'];
                
            TTransaction::open('Felabs_DB');

            $object = new AtividadeComplementar($id);

            if (strtolower(substr($object->arquivo, -4)) == 'html')
            {
                $win = TWindow::create( 'Arquivo', 0.8, 0.8 );
                $win->add( file_get_contents( $object->caminho_arquivo . '/' . $object->arquivo ) );
                $win->show();
            }
            else
            {
                TPage::openFile($object->caminho_arquivo . '/' . $object->arquivo);
            }
                
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    /*public static function onQuestionConverterPDF($param)
    {
        $action = new TAction([__CLASS__, 'onConverterPDF']);
        $action->setParameters($param); 
        
        new TQuestion('Deseja realmente converter o arquivo para o formato PDF/A ?', $action);
    }*/
    
    
    /*public static function onConverterPDF($param)
    {
        try
        {
            $atividade_id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $atividade = new AtividadeComplementar($atividade_id);
            
            if($atividade->status_pdfa == 0)
            {            
                //Ghostscript usa o caminho absoluto
                $caminho_pdf = $atividade->caminho_arquivo . '/' . $atividade->arquivo;
                $caminho_absoluto_pdf = realpath($caminho_pdf);
                
                
                //1º Converte para PDF/A-1b
                $caminho_pdfa1 = realpath($caminho_absoluto_pdf);
                $caminho_pdfa1 = substr($caminho_pdfa1, 0, -4) . '_A1b.pdf';
    
                shell_exec('gswin32c -dPDFA -dOverrideICC=true -dEmbedAllFonts=true -dBATCH -dNOPAUSE -dPDFSETTINGS=/printer -sProcessColorModel=DeviceRGB -sColorConversionStrategy=UseDeviceIndependentColor -sDEVICE=pdfwrite -dPDFACompatibilityPolicy=1 -sOutputFile=' . $caminho_pdfa1 . ' ' . $caminho_absoluto_pdf);
                        
                        
                //2º Converte para PDF/A-2b (a conversão direta causa erro na validação de conformidade)
                $caminho_pdfa2 = realpath($caminho_absoluto_pdf);
                $caminho_pdfa2 = substr($caminho_pdfa2, 0, -4) . '_A2b.pdf';
                
                shell_exec('gswin32c -dPDFA=2 -dBATCH -dNOPAUSE -dPDFSETTINGS=/printer -sProcessColorModel=DeviceRGB -sColorConversionStrategy=UseDeviceIndependentColor -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFACompatibilityPolicy=1 -sOutputFile='. $caminho_pdfa2 . ' ' . $caminho_pdfa1);
                
                
                //Se arquivo foi convertido                                              
                if(file_exists($caminho_pdfa2))
                {
                    //Apaga arquivo 'original' e o A1-b gerado para permitir a conversão
                    if($caminho_pdf)
                    {
                        unlink($caminho_pdf);
                    }
                    
                    if($caminho_pdfa1)
                    {
                        unlink($caminho_pdfa1);
                    }
                                           
                    $atividade->arquivo = substr($atividade->arquivo, 0, -4) . '_A2b.pdf';
                    $atividade->status_pdfa = 1;
                    $atividade->system_user_id = TSession::getValue('userid');
                    $atividade->data_reg = date('Y-m-d H:i:s');  
                    
                    $atividade->store();                
                    
                    TTransaction::close();
                    
                    new TMessage('info', 'Arquivo convertido com sucesso');                                
                }
                else
                {
                    throw new Exception("Erro ao converter arquivo");         
                } 
            }
            else
            {
                new TMessage('error', 'Este arquivo já foi convertido para o formato PDF/A');         
            }                                     
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }*/
    
    
    /*public function displayColumnAssinar($object)
    {
        if($object->status_atividade == 'Aprovado')
        {            
            return TRUE;
        }            
            return FALSE;  
    }*/
    
        
    /*public function onAssinar($param)
    {
        try
        {
            //FUNÇÃO NÃO CONCLUÍDA
            
            $atividade_id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $atividade = new AtividadeComplementar($atividade_id);
            
            $caminho_absoluto_atividade = realpath($atividade->caminho_arquivo . '/' . $atividade->arquivo);  
            $caminho_absoluto_diretorio = dirname(realpath($atividade->caminho_arquivo . '/' . $atividade->arquivo)) . "\\"; 
            
            
            //Esta função faz assinatura de forma "anexada" e não no formato Pades (embutida), como pede o MEC
            if($atividade->status_atividade == "Aprovado" AND $atividade->status_pdfa == 1)
            {
                $param = JSignParam::instance();
                $param->setIsUseJavaInstalled(true);
                //Comenta a linha abaixo se quiser assinar usando A3
                $param->setCertificate(file_get_contents('C:/Users/TI/Desktop/A1_FEI_2022.pfx'));
                $param->setPdf(file_get_contents($caminho_absoluto_atividade));
                $param->setPassword('--------');
                $param->setIsOutputTypeBase64(true);
                $param->setPathPdfSigned($caminho_absoluto_diretorio);
                
                $jSignPdf   = new JSignPDF($param);
                $fileSigned = $jSignPdf->sign();
                
                $pdf_assinado = $param->getTempPdfSignedPath();
                                
                file_put_contents($caminho_absoluto_atividade, $fileSigned);
                
                //TERMINAR
            }
            else
            {
                new TMessage('error', 'A atividade precisa ter sido aprovada pelo professor responsável e convertida em PDF/A para ser assinada');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }*/
 
 
    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue(__CLASS__.'_filter_nome_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome_curso', NULL);
        TSession::setValue(__CLASS__.'_filter_cod_prof_responsavel', NULL);
        TSession::setValue(__CLASS__.'_filter_status_atividade', NULL);


        if (isset($data->nome_aluno) AND ($data->nome_aluno)) {
            $filter = new TFilter('nome_aluno', 'like', "%{$data->nome_aluno}%"); 
            TSession::setValue(__CLASS__.'_filter_nome_aluno', $filter); 
        }


        if (isset($data->nome_curso) AND ($data->nome_curso)) {
            $filter = new TFilter('nome_curso', 'like', "%{$data->nome_curso}%"); 
            TSession::setValue(__CLASS__.'_filter_nome_curso', $filter); 
        }


        /*if ($data->cod_prof_responsavel) {
            $filter = new TFilter('(SELECT name from system_users WHERE systemuser_codlegado=atividade_complementar.cod_prof_responsavel)', 'like', "%{$data->cod_prof_responsavel}%");
            TSession::setValue(__CLASS__.'_filter_cod_prof_responsavel', $filter); 
        }*/
        
        
        if ($data->cod_prof_responsavel) {
            $filter = new TFilter('cod_prof_responsavel','in',"(SELECT su.systemuser_codlegado
                                    from system_users su
                                    join atividade_complementar ac
                                    on su.systemuser_codlegado = ac.cod_prof_responsavel 
                                    WHERE su.name like '%{$data->cod_prof_responsavel}%')");
                                    
                TSession::setValue(__CLASS__.'_filter_cod_prof_responsavel', $filter); 
        }

        if (isset($data->status_atividade) AND ($data->status_atividade)) {
            $filter = new TFilter('status_atividade', 'like', "%{$data->status_atividade}%"); 
            TSession::setValue(__CLASS__.'_filter_status_atividade', $filter); 
        }
        
        $this->form->setData($data);
        
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            $unit_id = TSession::getValue('userunitid');            
            
            //Filtra os cursos da unidade logada            
            TTransaction::open('dados_fei');
            
            $repository_curso = new TRepository('FiCurso');
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $cursos = $repository_curso->load($criteria_curso);

            foreach($cursos as $curso)
            {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            
            TTransaction::close();
            
            
            //Exibe só as atividades complementares da unidade logada
            TTransaction::open('Felabs_DB');          

            $repository = new TRepository('AtividadeComplementar');
            $limit = 20;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_curso', 'IN', $items));
            

            if (empty($param['order']))
            {
                $param['order'] = 'nome_aluno';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_nome_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_aluno')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_nome_curso')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome_curso')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_cod_prof_responsavel')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cod_prof_responsavel')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_status_atividade')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status_atividade')); 
            }


            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->carga_horaria = substr($object->carga_horaria,0,-3);
                    
                    /*$row =*/ $this->datagrid->addItem($object);
                    
                    /*$data_inicio = TDate::date2br($object->data_inicio);
                    $data_termino = TDate::date2br($object->data_termino);
                    
                    $row->popover = 'true';
                    $row->popside = 'top';
                    $row->popcontent = "<table class='popover-table'>
                                            <tr><td><b>Atividade</b></td><td>{$object->tipo_atividade}</td></tr>
                                            <tr><td><b>Descrição</b></td><td>{$object->descricao}</td></tr>
                                            <tr><td><b>Data de início</b></td><td>{$data_inicio}</td></tr>
                                            <tr><td><b>Data de término</b></td><td>{$data_termino}</td></tr>
                                        </table>";
                    $row->poptitle = 'Detalhes';*/
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
    
    
    public function displayColumnDelete($object)
    {
        try
        {
            $grupo_admin = 1;
            $user_groups = TSession::getValue('usergroupids');
            $user_id = TSession::getValue('userid');
            
            //if(($object->status_atividade == "Aguardando aprovação" AND $object->system_user_id == $user_id) OR (($object->status_atividade == "Aguardando aprovação") AND (in_array($grupo_admin, $user_groups))))
            if($object->status_atividade == "Aguardando aprovação")
            {
                return TRUE;
            }
                return FALSE;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    

    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new AtividadeComplementar($key, FALSE);
                        
            //Apaga o arquivo
            if(file_exists($object->caminho_arquivo . '/' . $object->arquivo))
            {
                unlink($object->caminho_arquivo . '/' . $object->arquivo);
            }
            
            //Se diretório estiver vazio, apaga diretório (um diretório para cada aluno)
            $files = ((count(glob("$object->caminho_arquivo/*")) === 0) ? true : false);
            
            if($files == true)
            {
                rmdir($object->caminho_arquivo);
            }
            
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);  
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