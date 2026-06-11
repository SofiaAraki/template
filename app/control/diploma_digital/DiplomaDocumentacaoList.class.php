<?php


class DiplomaDocumentacaoList extends TPage
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
        
        
        $unit_id = TSession::getValue('userunitid');
        
        if($unit_id <> 2 AND $unit_id <> 3 AND $unit_id <> 10 AND $unit_id <> 6)
        {
            new TMessage('error', 'Funcionalidade não disponível para esta unidade');
            die;
        }
        

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_DiplomaDigitalDocumentacao');
        $this->form->setFormTitle('<h4>Documentações Acadêmicas para Registro de Diploma</h4>');
        

        // create the form fields
        $dados_diplomado_id = new TEntry('dados_diplomado_id');
        $dados_curso_id = new TEntry('dados_curso_id');
        $status_documentacao = new TCombo('status_documentacao');
        
        
        //Status Documentação 
        $combo_doc = [];
        $combo_doc[1] = "Ativa";
        $combo_doc[0] = "Inativa";
        
        $status_documentacao->addItems($combo_doc);


        // add the fields
        $this->form->addFields( [ new TLabel('Nome') ], [ $dados_diplomado_id ] );
        $this->form->addFields( [ new TLabel('Curso') ], [ $dados_curso_id ] );
        $this->form->addFields( [ new TLabel('Status documentação') ], [ $status_documentacao ] );


        // set sizes
        $dados_diplomado_id->setSize('80%');
        $dados_curso_id->setSize('80%');
        $status_documentacao->setSize('80%');
        
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Documentacao_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Criar documentação', new TAction(['DiplomaDocumentacaoForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_dados_diplomado_id = new TDataGridColumn('diploma_digital_diplomado->nome', 'Nome', 'left', 200);
        $column_dados_curso_id = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'center', 100);
        $column_status_xml = new TDataGridColumn('status_xml', 'Status XML', 'center', 70);
        $column_status_assinatura_secretaria = new TDataGridColumn('status_assinatura_secretaria', 'Assinatura Secretária', 'left', 90);
        $column_status_assinatura_diretor = new TDataGridColumn('status_assinatura_diretor', 'Assinatura Diretor', 'left', 90);
        $column_status_assinatura_emissora = new TDataGridColumn('status_assinatura_emissora', 'Assinatura Emissora', 'left', 90);
        $column_status_assinatura_arquivamento = new TDataGridColumn('status_assinatura_arquivamento', 'Assinatura Arquivamento', 'left', 90);
        $column_status_documentacao = new TDataGridColumn('status_documentacao', 'Status Documentação', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dados_diplomado_id);
        $this->datagrid->addColumn($column_dados_curso_id);
        $this->datagrid->addColumn($column_status_xml);
        $this->datagrid->addColumn($column_status_assinatura_secretaria);
        $this->datagrid->addColumn($column_status_assinatura_diretor);
        $this->datagrid->addColumn($column_status_assinatura_emissora);
        $this->datagrid->addColumn($column_status_assinatura_arquivamento);
        $this->datagrid->addColumn($column_status_documentacao);


        $action1 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);                
        $action2 = new TDataGridAction(['DiplomaDocumentacaoForm', 'onEdit'], ['id'=>'{id}']);
        $action3 = new TDataGridAction([$this, 'onSetDadosProcessoJudicial'], ['id'=>'{id}']);
        $action4 = new TDataGridAction([$this, 'onSetDadosAnexarDocumentos'], ['id'=>'{id}']);
        $action5 = new TDataGridAction([$this, 'onSetDadosTermoDeResponsabilidade'], ['id'=>'{id}']);
        $action6 = new TDataGridAction([$this, 'onSetDadosAssociarDocumentacaoDiploma'], ['id'=>'{id}']);
        $action7 = new TDataGridAction([$this, 'onSetDadosGerarXmlDocumentacao'], ['id'=>'{id}']);
        $action8 = new TDataGridAction([$this, 'onAssinarXML'], ['id'=>'{id}']);
        $action9 = new TDataGridAction([$this, 'onDownloadEmitido'], ['id'=>'{id}']);        
        //$action10 = new TDataGridAction([$this, 'onValidarArquivo'], ['id'=>'{id}']); 
                        
        
        $action1->setLabel('Excluir arquivo e registro');
        $action1->setImage('far:trash-alt red');
        $action1->setDisplayCondition( array($this, 'displayColumnDelete') );
        
        $action2->setLabel('Editar informações');
        $action2->setImage('fas:pencil-alt orange');
        
        $action3->setLabel('Informações Processo Judicial');
        $action3->setImage('fas: fa-balance-scale black');
        $action3->setDisplayCondition( array($this, 'displayColumnProcesso') );
        
        $action4->setLabel('Anexar documentos');
        $action4->setImage('fas: fa-paperclip red');
        
        $action5->setLabel('Termo de responsabilidade');
        $action5->setImage('fas: fa-file-signature  #000080');
        
        $action6->setLabel('Associar documentação/diploma');
        $action6->setImage('fas: fa-exchange-alt #008080');
        
        $action7->setLabel('Gerar XML');
        $action7->setImage('fa:sync green');
        
        $action8->setLabel('Assinar com certificado');
        $action8->setImage('fas: fa-signature');        
        
        $action9->setLabel('Download XML');
        $action9->setImage('fas:cloud-download-alt blue'); 
        
        //$action10->setLabel('Verificar conformidade do arquivo');
        //$action10->setImage('far: fa-file-code');      
                              
        
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');        
                
        $action_group->addAction($action1);
        $action_group->addHeader('Documentação');
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        $action_group->addAction($action4); 
        $action_group->addAction($action5);  
        $action_group->addAction($action6);
        $action_group->addAction($action7); 
        $action_group->addAction($action8); 
        $action_group->addAction($action9);
        //$action_group->addAction($action10);            
        
        
        // add the actions to the datagrid        
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


    public function displayColumnProcesso($object)
    {
        if($object->opcao_via == "Decisão judicial")
        {
            return TRUE;
        }
        
        return FALSE;
    }
    

    public function onSetDadosProcessoJudicial($param)
    {
        try
        {
            $id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($id);        
            

            //Verifica se existem dados do processo
            $count = DiplomaDigitalProcessoJudicial::where('dados_documentacao_id', '=', $documentacao->id)->count();
            
            //Se existir, direciona para a edição 
            if($count == 1)
            {
                $processo_judicial = DiplomaDigitalProcessoJudicial::where('dados_documentacao_id', '=', $documentacao->id)->load();
 
                unset($param['id']);
                $param['id_documentacao'] = $documentacao->id;
                $param['key'] = $processo_judicial[0]->id; 
                 
                TApplication::loadPage('DiplomaProcessoJudicialForm', 'onEdit', $param);
            } 
                
            //Se ainda não existem dados do processo, retira "key" e "id" dos parâmetros               
            if($count == 0)
            {
                unset($param['key']);
                unset($param['id']);
                
                $param['id_documentacao'] = $documentacao->id;
                     
                TApplication::loadPage('DiplomaProcessoJudicialForm', 'onEdit', $param);
            }
            
            TTransaction::close();           
        } 
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onSetDadosAnexarDocumentos($param)
    {
        try
        {
            $id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($id);        
            
            TTransaction::close();
            
                            
            //Limpa variável para garantir integridade
            TSession::setValue('dados_documentacao', NULL);
            TSession::setValue('dados_documentacao', $documentacao);            

            TApplication::loadPage('DiplomaDocumentosFormList', 'onReload');
        } 
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }   
    }
    

    public function onSetDadosTermoDeResponsabilidade($param)
    {
        try
        {
            $id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($id);

            TTransaction::close();
        
            //Limpa variável para garantir integridade
            TSession::setValue('dados_documentacao', NULL);
            TSession::setValue('dados_documentacao', $documentacao);
    
            TApplication::loadPage('DiplomaTermoResponsabilidadeFormList');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }              
    } 
    
    
    //Cria registro na tabela dados_diploma
    public function onSetDadosAssociarDocumentacaoDiploma($param)
    {
        try
        {
            $id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($id);      
                            
            //O código que interliga os dois deve ser único, portanto, só vai trazer um registro
            $criteria = new TCriteria;
            $criteria->add(new TFilter('codigo_interliga_diploma_documentacao', '=', $documentacao->codigo_interliga_diploma_documentacao));
                                
            $dados_diploma = DiplomaDigitalDiploma::getObjects($criteria);
                
            //Se já existe o registro, direciona para a edição
            if($dados_diploma)
            {
                $diploma = DiplomaDigitalDiploma::find($dados_diploma[0]->id);
             
                $parametros['key'] = $diploma->id; 
                     
                TApplication::loadPage('DiplomaAssociaDocumentacaoDiplomaForm', 'onEdit', $parametros);  
            }
                
            //Se não, preenche campos "herdados" e ocultos 
            else
            {
                $parametros['dados_documentacao_id'] = $documentacao->id; 
                
                TApplication::loadPage('DiplomaAssociaDocumentacaoDiplomaForm', 'onShow', $parametros);
            }    

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    
    
    public function onSetDadosGerarXmlDocumentacao($param)
    {
        try
        {
            $documentacao_id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($documentacao_id);
            
            //O código que interliga os dois deve ser único, portanto, só vai trazer um registro
            $criteria = new TCriteria;
            $criteria->add(new TFilter('codigo_interliga_diploma_documentacao', '=', $documentacao->codigo_interliga_diploma_documentacao));
                                
            $dados_diploma = DiplomaDigitalDiploma::getObjects($criteria);
                                        
            //Se já existe o registro
            if($dados_diploma)
            {
                $count = count($dados_diploma);
                
                if($count == 1)
                {        
                    $diploma = DiplomaDigitalDiploma::find($dados_diploma[0]->id);
    
                    //Não permite alteração nos dados caso o diploma tenha sido registrado/publicado
                    if($diploma->arquivo_registrado OR $diploma->status_publicacao == 1)
                    {
                        throw new Exception('Não é possível alterar nenhum dado vinculado a um diploma registrado');  
                    }
                    else
                    {                
                        $parametros['id'] = $documentacao->id;  
                
                        TApplication::loadPage('XMLDocumentacaoForm', 'onVerificarXMLDocumentacao', $parametros);
                    }
                }    
            }    
            else
            {
                $action_cancelar = new TAction(array('DiplomaDocumentacaoList', 'onReload'));                                   
                new TMessage('error', 'É necessário criar uma associação entre documentação/diploma antes de prosseguir', $action_cancelar);    
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
    
    
    public function onDownloadEmitido($param)
    {
        try
        {
            $id = $param['id'];
               
            TTransaction::open('Felabs_DB');
                
            $object = new DiplomaDigitalDocumentacao($id);
            
            //Pega o nome do aluno para baixar o arquivo com o nome solicitado pela registradora
            $diplomado = new DiplomaDigitalDiplomado($object->dados_diplomado_id);
            $nome_diplomado = str_replace(' ', '_', $diplomado->nome);
            
            if($object->caminho_arquivo <> NULL AND $object->arquivo <> NULL)
            {
                $caminho_arquivo = $object->caminho_arquivo . '/' . $object->arquivo;

                if (file_exists($caminho_arquivo))
                {
                    TPage::openFile($caminho_arquivo, "$nome_diplomado.xml");
                    //TPage::openFile($caminho_arquivo);
                }
            }
            else
            {
                new TMessage('info', 'É necessário gerar o arquivo para depois fazer o download');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    /*public function onValidarArquivo($param)
    {
        TScript::create('window.open("http://validadordiplomadigital.mec.gov.br","_blank")');
    }*/
        

    public function onAssinarXML($param)
    {
        try
        {
            $id_documentacao = $param['id'];                
            
            TTransaction::open('Felabs_DB');          
            
            //Não permite alteração nos dados caso o diploma tenha sido registrado            
            $documentacao = new DiplomaDigitalDocumentacao($id_documentacao);                                        
            
            //O código que interliga os dois é único, portanto só traz um registro
            $diploma = DiplomaDigitalDiploma::where('codigo_interliga_diploma_documentacao', '=', $documentacao->codigo_interliga_diploma_documentacao)->load();

            if($diploma[0]->arquivo_registrado OR $diploma[0]->status_publicacao == 1) //1 - Publicado
            {
                throw new Exception('Não é possível alterar nenhum dado pertencente a um diploma registrado');  
            }


            if($documentacao->status_xml == 1)//1 - Gerado
            {
                if($documentacao->status_assinatura_arquivamento == 1)
                {
                    throw new Exception("Esta documentação já recebeu a assinatura de arquivamento e nenhuma outra assinatura pode ser inserida");
                }
                else
                {
                    //HOMOLOGAÇÃO - versão 1.05 (acrescentou /index)             
                    /*TScript::create("var popUp = window.open('http://localhost:8082/bry/index?id=".$id_documentacao."&type_doc=XMLDocumentacao','_blank','height=700,width=600,left='+(window.innerWidth-600)/2+',top='+(window.innerHeight-700)/2);                                 
                                      setInterval(function() {
                                          if (popUp.closed) {
                                              window.location.href = 'index.php?class=DiplomaDocumentacaoList';                                  
                                          }                                        
                                      }, 1000);   
                                    ");*/
                                    
                                    
                    //PRODUÇÃO
                    TScript::create("var popUp = window.open('http://academico.feituverava.com.br:8082/bry/index?id=".$id_documentacao."&type_doc=XMLDocumentacao','_blank','height=700,width=600,left='+(window.innerWidth-600)/2+',top='+(window.innerHeight-700)/2);                                 
                                     setInterval(function() {
                                         if (popUp.closed) {
                                             window.location.href = 'index.php?class=DiplomaDocumentacaoList';                                  
                                         }                                        
                                     }, 1000);   
                                   ");                   
                }                                                                                                                   
            }
            else
            {
                throw new Exception("É necessário gerar o XML para assiná-lo");
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

        TSession::setValue('Documentacao_filter_dados_diplomado_id', NULL);
        TSession::setValue('Documentacao_filter_dados_curso_id', NULL);
        TSession::setValue('Documentacao_filter_status_documentacao', NULL);
        
        if (isset($data->dados_diplomado_id) AND ($data->dados_diplomado_id)) {
            $filter = new TFilter('(SELECT nome FROM dados_diplomado WHERE id=dados_documentacao.dados_diplomado_id)', 'like', "%{$data->dados_diplomado_id}%");
            TSession::setValue('Documentacao_filter_dados_diplomado_id', $filter);
        }


        if (isset($data->dados_curso_id) AND ($data->dados_curso_id)) {
            $filter = new TFilter('(SELECT nome_curso_diploma FROM dados_curso WHERE id=dados_documentacao.dados_curso_id)', 'like', "%{$data->dados_curso_id}%");
            TSession::setValue('Documentacao_filter_dados_curso_id', $filter);
        }

        if ($data->status_documentacao <> NULL) {
            $filter = new TFilter('status_documentacao', '=', $data->status_documentacao);
            TSession::setValue('Documentacao_filter_status_documentacao', $filter);
        }

        $this->form->setData($data);
        
        TSession::setValue('Documentacao_filter_data', $data);
        

        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('DiplomaDigitalDocumentacao');
            $limit = 10;


            $unit_id = TSession::getValue('userunitid');
            
            //Filtra a documentação por emissora
            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
           

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('Documentacao_filter_dados_diplomado_id')) {
                $criteria->add(TSession::getValue('Documentacao_filter_dados_diplomado_id'));
            }


            if (TSession::getValue('Documentacao_filter_dados_curso_id')) {
                $criteria->add(TSession::getValue('Documentacao_filter_dados_curso_id'));
            }
            
            if (TSession::getValue('Documentacao_filter_status_documentacao')) {
                $criteria->add(TSession::getValue('Documentacao_filter_status_documentacao'));
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
                    //STATUS XML
                    if($object->status_xml == 0)
                    {
                        $object->status_xml = "<span class='label label-danger'>Não gerado</span>";
                    }
                    elseif($object->status_xml == 1)
                    {
                        $object->status_xml = "<span class='label label-success'>Gerado</span>";
                    }
                    else
                    {
                        $object->status_xml = $object->status_xml;
                    }
                    
                    //STATUS ASSINATURA SECRETÁRIA
                    if($object->status_assinatura_secretaria == 0)
                    {
                        $object->status_assinatura_secretaria = "<span class='label label-danger'>Não preenchida</span>";
                    }
                    elseif($object->status_assinatura_secretaria == 1)
                    {
                        $object->status_assinatura_secretaria = "<span class='label label-success'>Preenchida</span>";
                    }
                    else
                    {
                        $object->status_assinatura_secretaria = $object->status_assinatura_secretaria;
                    }
                    
                    //STATUS ASSINATURA DIRETOR
                    if($object->status_assinatura_diretor == 0)
                    {
                        $object->status_assinatura_diretor = "<span class='label label-danger'>Não preenchida</span>";
                    }
                    elseif($object->status_assinatura_diretor == 1)
                    {
                        $object->status_assinatura_diretor = "<span class='label label-success'>Preenchida</span>";
                    }
                    else
                    {
                        $object->status_assinatura_diretor = $object->status_assinatura_diretor;
                    }
                    
                    //STATUS ASSINATURA EMISSORA
                    if($object->status_assinatura_emissora == 0)
                    {
                        $object->status_assinatura_emissora = "<span class='label label-danger'>Não preenchida</span>";
                    }
                    elseif($object->status_assinatura_emissora == 1)
                    {
                        $object->status_assinatura_emissora = "<span class='label label-success'>Preenchida</span>";
                    }
                    else
                    {
                        $object->status_assinatura_emissora = $object->status_assinatura_emissora;
                    }
                    
                    //STATUS ASSINATURA ARQUIVAMENTO
                    if($object->status_assinatura_arquivamento == 0)
                    {
                        $object->status_assinatura_arquivamento = "<span class='label label-danger'>Não preenchida</span>";
                    }
                    elseif($object->status_assinatura_arquivamento == 1)
                    {
                        $object->status_assinatura_arquivamento = "<span class='label label-success'>Preenchida</span>";
                    }
                    else
                    {
                        $object->status_assinatura_arquivamento = $object->status_assinatura_arquivamento;
                    }
                    
                    //STATUS DOCUMENTAÇÃO
                    if($object->status_documentacao == 0)
                    {
                        $object->status_documentacao = "<span class='label label-danger'>Inativa</span>";
                    }
                    elseif($object->status_documentacao == 1)
                    {
                        $object->status_documentacao = "<span class='label label-success'>Ativa</span>";
                    }
                    else
                    {
                        $object->status_documentacao = $object->status_documentacao;
                    }          
                                                        
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
    
    
    //Se o usuário logado é do grupo Admin, exibe opção
    public function displayColumnDelete($param)
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');       
        
        if( in_array($grupo_admin, $user_groups))
        {
            return TRUE;
        }
            return FALSE;
    }
    

    public static function onDelete($param)
    {
        try
        {
            $action = new TAction([__CLASS__, 'Delete']);
            $action->setParameters($param);            
            
            TTransaction::open('Felabs_DB');
            
            $key = $param['key'];        
            $documentacao = new DiplomaDigitalDocumentacao($key);
            

            /*Não permite a exclusão caso o xml tenha sido gerado e todas as assinaturas tenham sido aplicadas, pois nestas condições, 
            o arquivo poderá ter sido enviado à registradora e nem se houver documento/termo/diploma vinculados à documentação*/
            if(($documentacao->status_xml == 1 AND $documentacao->status_assinatura_secretaria == 1 AND 
                $documentacao->status_assinatura_diretor == 1 AND $documentacao->status_assinatura_emissora == 1 AND 
                $documentacao->status_assinatura_arquivamento == 1) OR 
              ((DiplomaDigitalDocumentos::where('dados_documentacao_id', '=', $documentacao->id)->count() > 0) OR
               (DiplomaDigitalTermoResponsabilidade::where('dados_documentacao_id', '=', $documentacao->id)->count() > 0) OR
               (DiplomaDigitalProcessoJudicial::where('dados_documentacao_id', '=', $documentacao->id)->count() > 0) OR
               (DiplomaDigitalDiploma::where('dados_documentacao_id', '=', $documentacao->id)->count() > 0)))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) à documentação');
                return false;
            }                
            else
            {    
                new TQuestion('Deseja realmente excluir ?', $action);
            } 
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    

    //Deleta registro e arquivo
    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new DiplomaDigitalDocumentacao($key);                      

            //Apaga arquivo
            if(file_exists($object->caminho_arquivo. '/' . $object->arquivo))
            {
                unlink($object->caminho_arquivo. '/' . $object->arquivo);
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
