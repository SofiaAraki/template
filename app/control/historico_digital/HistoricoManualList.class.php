<?php

class HistoricoManualList extends TPage
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
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_HistoricoDigital');
        $this->form->setFormTitle('Buscar Histórico');
        

        // create the form fields
        $cod_aluno = new TEntry('cod_aluno');
        $nome = new TEntry('nome');
        $cpf = new TEntry('cpf');


        // add the fields
        $this->form->addFields( [ new TLabel('Cód. Aluno:') ], [ $cod_aluno ] );
        $this->form->addFields( [ new TLabel('Nome:') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('CPF:') ], [ $cpf ] );


        // set sizes
        $cod_aluno->setSize('80%');
        $nome->setSize('80%');
        $cpf->setSize('80%');
        $cpf->setMask('9!');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Criar histórico manual', new TAction(['HistoricoManualInformacoesForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        
        
        // creates the datagrid columns
        $column_cod_aluno = new TDataGridColumn('cod_aluno', 'Cód.', 'center');
        $column_nome = new TDataGridColumn('diploma_digital_diplomado->nome', 'Nome', 'left');
        $column_cpf = new TDataGridColumn('diploma_digital_diplomado->cpf', 'CPF', 'left');
        $column_curso = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'left');
        $column_tipo_historico = new TDataGridColumn('tipo_historico', 'Última emissão', 'center');
        $column_status_assinatura_secretaria = new TDataGridColumn('status_assinatura_secretaria', 'Assinatura secretária', 'center');
        $column_status_assinatura_emissora = new TDataGridColumn('status_assinatura_emissora', 'Assinatura emissora', 'center');
        $column_status_publicacao = new TDataGridColumn('status_publicacao', 'Status Publicação', 'center');
        $column_data_publicacao = new TDataGridColumn('data_publicacao', 'Data Publicação', 'center');
        

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_cod_aluno);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_tipo_historico); 
        $this->datagrid->addColumn($column_status_assinatura_secretaria); 
        $this->datagrid->addColumn($column_status_assinatura_emissora); 
        $this->datagrid->addColumn($column_status_publicacao); 
        $this->datagrid->addColumn($column_data_publicacao);      

        
        $column_cpf->setTransformer(array($this, 'formatCPF'));
        $column_tipo_historico->setTransformer( array($this, 'setTipoHistorico') );        
        $column_status_assinatura_secretaria->setTransformer( array($this, 'setStatusAssinaturaSecretaria') );
        $column_status_assinatura_emissora->setTransformer( array($this, 'setStatusAssinaturaEmissora') );
        $column_status_publicacao->setTransformer( array($this, 'setStatusPublicacao') );
        

        $action_gerais = new TDataGridAction(['HistoricoManualInformacoesForm', 'onEdit'], ['id'=>'{id}', 'dados_curso_id' =>'{dados_curso_id}']);
        $action_componentes = new TDataGridAction([$this, 'onSetDadosComponentes'], ['id'=>'{id}']);
        $action_gerar = new TDataGridAction([$this, 'onSetDadosGerarXmlHistorico'], ['id'=>'{id}']);
        $action_assinar = new TDataGridAction([$this, 'onSetDadosAssinarXml'], ['id'=>'{id}']);
        $action_xml = new TDataGridAction([$this, 'onDownloadXml'], ['id'=>'{id}']);
        $action_publicar = new TDataGridAction([$this, 'onSetDadosPublicarHistorico'], ['id'=>'{id}']);
        
        
        $action_gerais->setLabel('Editar informações gerais');
        $action_gerais->setImage('fas:pencil-alt orange');
        
        $action_componentes->setLabel('Editar componentes curriculares');
        $action_componentes->setImage('fas:pencil-alt orange');
        
        $action_gerar->setLabel('Gerar XML');
        $action_gerar->setImage('fa:sync green');
        
        $action_assinar->setLabel('Assinar com certificado');
        $action_assinar->setImage('fas: fa-signature');
        
        $action_xml->setLabel('Download XML');
        $action_xml->setImage('fas:cloud-download-alt blue');
        
        $action_publicar->setLabel('Conferir e publicar histórico');
        $action_publicar->setImage('fa:check green');
        
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');
        
        $action_group->addAction($action_gerais);
        $action_group->addAction($action_componentes);
        $action_group->addAction($action_gerar);
        $action_group->addAction($action_assinar);
        $action_group->addAction($action_xml);
        $action_group->addAction($action_publicar);
        
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
    
    
    public function formatCPF($column_cpf, $object, $row)
    {
        return preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/', '$1.$2.$3-$4', $column_cpf);
    }
    
    
    public function setTipoHistorico($column_tipo_historico, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //Se o xml foi gerado (o tipo é registrado ao gerar o xml)
            if($object->tipo_historico <> NULL)
            {
                return $object->tipo_historico; 
            }
            else
            {
                return $tipo_historico = "Físico";  
            } 
                   
            TTransaction::close();              
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function setStatusAssinaturaSecretaria($column_status_assinatura_secretaria, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
     
            //Não gerado
            if($object->status_xml == 0)
            {
                $status_assinatura_secretaria = "";
            } 
            else
            {
                //Verifica o tipo de histórico (secretária assina apenas transferência/final/2ª vias)
                if($object->tipo_historico <> "Parcial" AND $object->tipo_historico <> "2ª via parcial")
                {
                    if($object->status_assinatura_secretaria == 0)
                    {
                        $status_assinatura_secretaria = "<span class='label label-danger'>Não preenchida</span>";
                    }
                    else
                    {
                        $status_assinatura_secretaria = "<span class='label label-success'>Preenchida</span>";
                    }
                }
                else
                {
                    $status_assinatura_secretaria = "";
                }    
            }   

            $object->status_assinatura_secretaria = $status_assinatura_secretaria;
            return $object->status_assinatura_secretaria; 
                   
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function setStatusAssinaturaEmissora($column_status_assinatura_emissora, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
                    
            //Não gerado
            if($object->status_xml == 0)
            {
                $status_assinatura_emissora = "";
            } 
            else
            {
                //Emissora assina qualquer tipo de histórico
                if($object->status_assinatura_emissora == 0)
                {
                    $status_assinatura_emissora = "<span class='label label-danger'>Não preenchida</span>";
                }
                else
                {
                    $status_assinatura_emissora = "<span class='label label-success'>Preenchida</span>";
                }
            }    
         
            $object->status_assinatura_emissora = $status_assinatura_emissora;
            return $object->status_assinatura_emissora;
          
            TTransaction::close();    
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function setStatusPublicacao($column_status_publicacao, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
                    
            //Não gerado
            if($object->status_xml == 0)
            {
                $status_publicacao = "";
            } 
            else
            {
                if($object->status_publicacao == 0)
                {
                    $status_publicacao = "<span class='label label-danger'>Não publicado</span>";
                } 
                else
                {
                    $status_publicacao = "<span class='label label-success'>Publicado</span>";
                }
            }       
     
            $object->status_publicacao = $status_publicacao;
            return $object->status_publicacao;
            
            TTransaction::close();   
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onSetDadosComponentes($param)
    {
        try
        {
            $id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $historico_digital = new HistoricoDigital($id);            
            
            TTransaction::close();
            
            //Limpa variável para garantir integridade
            TSession::setValue('dados_historico_digital', NULL);
            TSession::setValue('dados_historico_digital', $historico_digital);


            TApplication::loadPage('HistoricoManualComponentesForm', 'onShow');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onSetDadosGerarXmlHistorico($param)
    {
        try
        {
            $id_historico = $param['id'];                        

            TTransaction::open('Felabs_DB');
            
            $historico_digital = new HistoricoDigital($id_historico);            
            
            //Verifica se existem registros para compor o histórico              
            $verifica_disciplinas = HistoricoDigitalDisciplinas::where('historico_digital_id', '=', $historico_digital->id)->load();
            
            
            //Atividades e estágios não podem ser requisitos para gerar o xml, pois o aluno pode não ter nenhum registro (caso histórico parcial)
            /*$verifica_atividades = AtividadeComplementar::where('cod_aluno', '=', $historico_digital->cod_aluno)
                                                        ->where('cod_curso', '=', $historico_digital->cod_curso)
                                                        ->where('status_atividade', '=', 'Aprovado')
                                                        ->load();
            
            $verifica_estagios = Estagio::where('cod_aluno', '=', $historico_digital->cod_aluno)
                                        ->where('cod_curso', '=', $historico_digital->cod_curso)
                                        ->where('status_estagio', '=', 'Aprovado')
                                        ->load();*/
                                        
            $verifica_situacoes = HistoricoDigitalSituacaoDiscente::where('historico_digital_id', '=', $historico_digital->id)->load();                            


            if($verifica_disciplinas AND $verifica_situacoes)
            {               
                $form = new BootstrapFormBuilder('form_TipoHistoricoManual');
            
                $tipo_historico = new TRadioGroup('tipo_historico');
                
                $tipos = [];
                $tipos['Parcial'] = "Parcial";
                $tipos['Transferência'] = "Transferência";
                $tipos['Final'] = "Final";
                //$tipos['2ª via parcial'] = "2ª via parcial";
                //$tipos['2ª via transferência'] = "2ª via transferência";
                $tipos['2ª via final'] = "2ª via final";
                
                
                $tipo_historico->addItems($tipos);                        
                //$tipo_historico->setLayout('horizontal');
                //$tipo_historico->setBreakItems(3);                
                //$tipo_historico->setSize(150);


                $label_explicacao = '<p style="font-size: 15px;">Prezado usuário, <br>Selecione o tipo de histórico que deseja emitir: </p>';
                
                $panel = new TPanelGroup();
                $panel->add($label_explicacao);
                $panel->add($tipo_historico);

            
                $form->addContent( [ $panel ] );
                
                $form->addAction('Gerar XML', new TAction([$this, 'onRedirecionaFormularioXML'], ['id_historico_digital' => $historico_digital->id]), 'fa:sync green');
                
                new TInputDialog('Emissão de Histórico', $form);
            }                
            else
            {
                $action_cancelar = new TAction(array('HistoricoManualList', 'onReload'));                                     
                new TMessage('error', 'Verifique se os componentes curriculares foram lançados antes de prosseguir', $action_cancelar);    
                die; 
            }

            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onRedirecionaFormularioXML($param)
    {
        $id_historico_digital = $param['id_historico_digital'];
        $opcao = $param['tipo_historico'];
       
        if(!empty($opcao))
        {
            $parametros['id_historico_digital'] = $id_historico_digital;
            $parametros['tipo_historico'] = $opcao;
            
            //Para redirecionar à listagem que chamou a ação em caso de exceção
            $parametros['formulario_origem'] = "HistoricoManualList";
            
            TApplication::loadPage('XMLHistoricoForm', 'onVerificarXMLHistorico', $parametros);
        }
        else
        {
            new TMessage('error', 'Por favor, selecione uma opção');
        } 
    }
    
    
    public function onSetDadosAssinarXml($param)
    {
        try
        {
            $id_historico = $param['id'];
            
            TTransaction::open('Felabs_DB');            

            $historico_digital = new HistoricoDigital($id_historico);

            //Se por acaso o histórico foi publicado, não permite assinatura posterior
            if($historico_digital->status_publicacao == 1)
            {
                $action1 = new TAction(array('HistoricoManualList', 'onReload'));                                       
                new TMessage('error', 'Não é possível inserir assinatura em histórico já publicado', $action1);    
                die;
            }
                
                
            //Verifica se o XML foi gerado
            if($historico_digital->status_xml == 1)
            {
                //Verifica se as assinaturas já não foram inseridas
                if($historico_digital->tipo_historico == "Parcial" OR $historico_digital->tipo_historico == "2ª via parcial")
                {
                    if($historico_digital->status_assinatura_emissora == 1)
                    {
                        $action2 = new TAction(array('HistoricoManualList', 'onReload'));                                       
                        new TMessage('error', 'Histórico já assinado', $action2);    
                        die;
                    }      
                }
                else
                {
                    if($historico_digital->status_assinatura_secretaria == 1 AND $historico_digital->status_assinatura_emissora == 1)
                    {
                        $action3 = new TAction(array('HistoricoManualList', 'onReload'));                                       
                        new TMessage('error', 'Histórico já assinado', $action3);    
                        die;
                    }     
                }                         
                   
                $form = new BootstrapFormBuilder('form_InstrucoesAssinaturaHistoricoManual');

                $tipo_historico = new TEntry('tipo_historico');
                $status_assinatura_secretaria = new TEntry('status_assinatura_secretaria');
                $status_assinatura_emissora = new TEntry('status_assinatura_emissora');
                    
                $tipo_historico->setValue($historico_digital->tipo_historico);
                $tipo_historico->setEditable(FALSE);
                $tipo_historico->setSize('100%');
                $status_assinatura_secretaria->setEditable(FALSE);
                $status_assinatura_secretaria->setSize('100%');
                $status_assinatura_emissora->setEditable(FALSE);
                $status_assinatura_emissora->setSize('100%');
                    
                //Secretária
                if($historico_digital->status_assinatura_secretaria == 0)
                {
                    $status_assinatura_secretaria->setValue('Não preenchida');
                }
                else
                {
                    $status_assinatura_secretaria->setValue('Preenchida');
                }
                    
                //Emissora
                if($historico_digital->status_assinatura_emissora == 0)
                {
                    $status_assinatura_emissora->setValue('Não preenchida');
                }
                else
                {
                    $status_assinatura_emissora->setValue('Preenchida');
                }                       
           
           
                $label_explicacao = '<p style="font-size: 15px;">Prezado usuário, <br>As regras para assinatura variam de acordo com o tipo de histórico gerado:</p>
                                     <p style="font-size: 15px;"><b>Histórico de Transferência ou Final</b> - (incluindo 2ª via) deve conter uma assinatura e-CPF da 
                                     secretária acadêmica <b>seguida</b> de uma assinatura e-CNPJ da instituição</p>
                                     <p style="font-size: 15px;"><b>Histórico Parcial</b> - deve conter <b>apenas</b> uma assinatura e-CNPJ da instituição</p>';                                       
                
                $panel = new TPanelGroup();
                $panel->add($label_explicacao);


                $form->addContent( [ $panel ] );
                $row = $form->addFields( [ new TLabel('Tipo de histórico a ser assinado:'), $tipo_historico ] );
                $row->layout = ['col-sm-12'];
                   
                if($historico_digital->tipo_historico == "Parcial" OR $historico_digital->tipo_historico == "2ª via parcial")
                {
                    $row = $form->addFields( [ new TLabel('Assinatura emissora:'), $status_assinatura_emissora ] );
                    $row->layout = ['col-sm-12'];    
                }
                else
                {
                    $row = $form->addFields( [ new TLabel('Assinatura secretária:'), $status_assinatura_secretaria ],
                                             [ new TLabel('Assinatura emissora:'), $status_assinatura_emissora ] );
                    $row->layout = ['col-sm-6', 'col-sm-6'];
                }
                    
                $form->addAction('Assinar XML', new TAction([__CLASS__, 'onAssinarXML'], ['historico_id' => $historico_digital->id]), 'fas: fa-signature');
                    
                new TInputDialog('Emissão de Histórico', $form);                         
            }
            else
            {
                $action_cancelar = new TAction(array('HistoricoManualList', 'onReload'));                                       
                new TMessage('error', 'É necessário gerar o XML para assiná-lo', $action_cancelar);    
                die;
            }

            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onAssinarXML($param)
    {
        $id_historico = $param['historico_id'];
        
        if($id_historico)
        {
            //HOMOLOGAÇÃO - versão 1.05 (acrescentou /index)              
            /*TScript::create("var popUp = window.open('http://localhost:8082/bry/index?id=".$id_historico."&type_doc=XMLHistoricoEscolar','_blank','height=700,width=600,left='+(window.innerWidth-600)/2+',top='+(window.innerHeight-700)/2);                                 
                             setInterval(function() {
                                 if (popUp.closed) {
                                     window.location.href = 'index.php?class=HistoricoManualList';                                  
                                 }                                        
                             }, 1000);   
                           ");*/                    
        
        
            //PRODUÇÃO
            TScript::create("var popUp = window.open('http://academico.feituverava.com.br:8082/bry/index?id=".$id_historico."&type_doc=XMLHistoricoEscolar','_blank','height=700,width=600,left='+(window.innerWidth-600)/2+',top='+(window.innerHeight-700)/2);                                 
                             setInterval(function() {
                                 if (popUp.closed) {
                                     window.location.href = 'index.php?class=HistoricoManualList';                                  
                                 }                                        
                             }, 1000);   
                          ");
        }
    }
    
    
    public function onDownloadXml($param)
    {
        try
        {
            $id_historico = $param['id'];
               
            TTransaction::open('Felabs_DB');
                
            $object = new HistoricoDigital($id_historico);
            
            if($object->caminho_arquivo <> NULL AND $object->arquivo <> NULL)
            {
                $caminho_arquivo = $object->caminho_arquivo . '/' . $object->arquivo;

                if (file_exists($caminho_arquivo))
                {
                    TPage::openFile($caminho_arquivo);
                }
            }
            else
            {
                new TMessage('error', 'É necessário gerar o arquivo para depois fazer o download');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onSetDadosPublicarHistorico($param)
    {
        try
        {
            $id_historico = $param['id'];            
            
            TTransaction::open('Felabs_DB');
            
            $historico_digital = new HistoricoDigital($id_historico);                
                
            //Verifica se o XML foi gerado
            if($historico_digital->status_xml == 0)
            {
                $action1 = new TAction(array('HistoricoManualList', 'onReload'));                                       
                new TMessage('error', 'É necessário gerar o histórico antes de prosseguir', $action1);    
                die;
            } 
            else
            {
                //Retorna para HistoricoAutomaticoList ou HistoricoManualList dependendo de qual deles chamou a função
                $formulario_origem = "HistoricoManualList";
                            
                TSession::setValue('formulario_origem', NULL);
                TSession::setValue('formulario_origem', $formulario_origem);
                            
                $parametros['id_historico_digital'] = $historico_digital->id;
                             
                TApplication::loadPage('HistoricoRepresentacaoVisualView', 'onLerDadosXml', $parametros);         
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
        
        TSession::setValue(__CLASS__.'_filter_cod_aluno', NULL);
        TSession::setValue(__CLASS__.'_filter_nome', NULL);
        TSession::setValue(__CLASS__.'_filter_cpf', NULL);

        if (isset($data->cod_aluno) AND ($data->cod_aluno)) {
            $filter = new TFilter('cod_aluno', 'like', "%{$data->cod_aluno}%"); 
            TSession::setValue(__CLASS__.'_filter_cod_aluno', $filter); 
        }
        
        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('(SELECT nome from dados_diplomado WHERE id=historico_digital.dados_diplomado_id)', 'like', "%{$data->nome}%");
            TSession::setValue(__CLASS__.'_filter_nome', $filter); 
        }
        
        if (isset($data->cpf) AND ($data->cpf)) {
            $filter = new TFilter('(SELECT cpf from dados_diplomado WHERE id=historico_digital.dados_diplomado_id)', 'like', "%{$data->cpf}%"); 
            TSession::setValue(__CLASS__.'_filter_cod_aluno', $filter); 
        }


        $this->form->setData($data);
        
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
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

            $repository = new TRepository('HistoricoDigital');
            $limit = 10;

            $unit_id = TSession::getValue('userunitid');
            
            //Carrega apenas históricos gerados manualmente e da unidade logada
            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
            $criteria->add(new TFilter('historico_gerado', '=', 'Manual'));

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_cod_aluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cod_aluno')); 
            }
            
            if (TSession::getValue(__CLASS__.'_filter_nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome')); 
            }
            
            if (TSession::getValue(__CLASS__.'_filter_cpf')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cpf')); 
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
                    $object->data_publicacao = TDate::date2br($object->data_publicacao);
                    
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
