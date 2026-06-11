<?php

class HistoricoAutomaticoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_FiHistorico');
        $this->form->setFormTitle('Buscar Histórico');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $CPF = new TEntry('CPF');


        // add the fields
        $this->form->addFields( [ new TLabel('Cód. Aluno:') ], [ $Codaluno ] );
        $this->form->addFields( [ new TLabel('Nome:') ], [ $Nome ] );
        $this->form->addFields( [ new TLabel('CPF:') ], [ $CPF ] );
        

        // set sizes
        $Codaluno->setSize('80%');
        $Nome->setSize('80%');
        $CPF->setSize('80%');
        $CPF->setMask('999.999.999-99');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(('Buscar Histórico'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
                
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Cód.', 'center');
        $column_Nome = new TDataGridColumn('fi_aluno->Nome', 'Nome', 'left');
        $column_CPF = new TDataGridColumn('fi_aluno->CPF', 'CPF', 'left', 120);
        $column_Nomehistorico = new TDataGridColumn('fi_curso->Nomehistorico', 'Curso', 'left');
        $column_tipo_historico = new TDataGridColumn('tipo_historico', 'Última emissão', 'center');
        $column_status_assinatura_secretaria = new TDataGridColumn('status_assinatura_secretaria', 'Assinatura secretária', 'center');
        $column_status_assinatura_emissora = new TDataGridColumn('status_assinatura_emissora', 'Assinatura emissora', 'center');
        $column_status_publicacao = new TDataGridColumn('status_publicacao', 'Status Publicação', 'center');
        $column_data_publicacao = new TDataGridColumn('data_publicacao', 'Data Publicação', 'center');


        $column_tipo_historico->setTransformer( array($this, 'setTipoHistorico') );
        $column_status_assinatura_secretaria->setTransformer( array($this, 'setStatusAssinaturaSecretaria') );
        $column_status_assinatura_emissora->setTransformer( array($this, 'setStatusAssinaturaEmissora') );
        $column_status_publicacao->setTransformer( array($this, 'setStatusPublicacao') );
        $column_data_publicacao->setTransformer( array($this, 'setDataPublicacao') );


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_CPF);
        $this->datagrid->addColumn($column_Nomehistorico); 
        $this->datagrid->addColumn($column_tipo_historico); 
        $this->datagrid->addColumn($column_status_assinatura_secretaria); 
        $this->datagrid->addColumn($column_status_assinatura_emissora); 
        $this->datagrid->addColumn($column_status_publicacao); 
        $this->datagrid->addColumn($column_data_publicacao);
        

        $action_gerais = new TDataGridAction([$this, 'onSetDadosHistorico'], ['codhistorico'=>'{codhistorico}']);
        $action_componentes = new TDataGridAction([$this, 'onSetDadosComponentes'], ['codhistorico'=>'{codhistorico}']);
        $action_gerar = new TDataGridAction([$this, 'onSetDadosGerarXmlHistorico'], ['codhistorico'=>'{codhistorico}']);
        $action_assinar = new TDataGridAction([$this, 'onSetDadosAssinarXml'], ['codhistorico'=>'{codhistorico}']);
        $action_xml = new TDataGridAction([$this, 'onDownloadXml'], ['codhistorico'=>'{codhistorico}']);
        $action_publicar = new TDataGridAction([$this, 'onSetDadosPublicarHistorico'], ['codhistorico'=>'{codhistorico}']);
        
        
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
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
                
        parent::add($container);
    }
    
    
    public function setTipoHistorico($column_tipo_historico, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
                    
            $historico_digital = HistoricoDigital::where('historico_genesi_id', '=', $object->codhistorico)->load();
            
            if($historico_digital)
            {
                //Se tem registro em historico_digital e o xml foi gerado (o tipo é registrado ao gerar o xml)
                if($historico_digital[0]->tipo_historico <> NULL)
                {
                    $tipo_historico = $historico_digital[0]->tipo_historico;
                }
                else
                {
                    $tipo_historico = "Físico";  
                } 
            }
            else
            {
                $tipo_historico = "Físico";     
            }
                   
            TTransaction::close();
      
                        
            TTransaction::open('dados_fei');           
            
            $object->tipo_historico = $tipo_historico;
            return $object->tipo_historico;

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
                    
            $historico_digital = HistoricoDigital::where('historico_genesi_id', '=', $object->codhistorico)->load();

            if($historico_digital)
            {
                //Não gerado
                if($historico_digital[0]->status_xml == 0)
                {
                    $status_assinatura_secretaria = "";
                } 
                else
                {
                    //Verifica o tipo de histórico (secretária assina apenas transferência/final/2ª vias)
                    if($historico_digital[0]->tipo_historico <> "Parcial" AND $historico_digital[0]->tipo_historico <> "2ª via parcial")
                    {
                        if($historico_digital[0]->status_assinatura_secretaria == 0)
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
            }
            else
            {
                $status_assinatura_secretaria = "";     
            }
                   
            TTransaction::close();


            TTransaction::open('dados_fei');           
            
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
                    
            $historico_digital = HistoricoDigital::where('historico_genesi_id', '=', $object->codhistorico)->load();

            if($historico_digital)
            {
                //Não gerado
                if($historico_digital[0]->status_xml == 0)
                {
                    $status_assinatura_emissora = "";
                } 
                else
                {
                    //Emissora assina qualquer tipo de histórico
                    if($historico_digital[0]->status_assinatura_emissora == 0)
                    {
                        $status_assinatura_emissora = "<span class='label label-danger'>Não preenchida</span>";
                    }
                    else
                    {
                        $status_assinatura_emissora = "<span class='label label-success'>Preenchida</span>";
                    }
                }    
            }
            else
            {
                $status_assinatura_emissora = "";     
            }
                   
            TTransaction::close();
      
                        
            TTransaction::open('dados_fei');           
            
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
                    
            $historico_digital = HistoricoDigital::where('historico_genesi_id', '=', $object->codhistorico)->load();

            if($historico_digital)
            {
                //Não gerado
                if($historico_digital[0]->status_xml == 0)
                {
                    $status_publicacao = "";
                } 
                else
                {
                    if($historico_digital[0]->status_publicacao == 0)
                    {
                        $status_publicacao = "<span class='label label-danger'>Não publicado</span>";
                    } 
                    else
                    {
                        $status_publicacao = "<span class='label label-success'>Publicado</span>";
                    }
                }       
            }
            else
            {
                $status_publicacao = "";     
            }
                   
            TTransaction::close();
      
                        
            TTransaction::open('dados_fei');           
            
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
    
    
    public function setDataPublicacao($column_data_publicacao, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
                    
            $historico_digital = HistoricoDigital::where('historico_genesi_id', '=', $object->codhistorico)->load();

            if($historico_digital)
            {
                if($historico_digital[0]->data_publicacao <> NULL)
                {
                    $data_publicacao = TDate::date2br($historico_digital[0]->data_publicacao);
                } 
                else
                {
                    $data_publicacao = "";
                }   
            }
            else
            {
                $data_publicacao = "";     
            }
                   
            TTransaction::close();
      
                        
            TTransaction::open('dados_fei');           
            
            $object->data_publicacao = $data_publicacao;
            return $object->data_publicacao;

            TTransaction::close();    
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onSetDadosHistorico($param)
    {
        try
        {
            $cod_historico = $param['codhistorico'];
            
            TTransaction::open('dados_fei');
            
            $historico_genesi = new FiHistorico($cod_historico);
            
            $cod_aluno = $historico_genesi->Codaluno;
            $cod_curso = $historico_genesi->CodCurso;
            
            TTransaction::close();
            
            
            TTransaction::open('Felabs_DB');
            
            //Verifica se existe registro na tabela dados_curso, pois os campos serão usados para o histórico
            $verifica_curso = DiplomaDigitalCurso::where('codigo_curso_sistema', '=', $cod_curso)->load();
            

            //Verifica se existe registro na tabela dados_diplomado, pois os campos serão usados para o histórico
            $verifica_diplomado = DiplomaDigitalDiplomado::where('cod_aluno', '=', $cod_aluno)->load();


            if($verifica_curso AND $verifica_diplomado)
            {
                //Verifica se existe histórico
                $count = HistoricoDigital::where('historico_genesi_id', '=', $historico_genesi->codhistorico)->count();

                //Se existir, direciona para a edição 
                if($count == 1)
                {
                    $verifica_historico = HistoricoDigital::where('historico_genesi_id', '=', $historico_genesi->codhistorico)->load();
                    
                    $historico_digital = HistoricoDigital::find($verifica_historico[0]->id);
                    
                    //Passa ID do curso como variável de sessão para não perder valor ao executar o construct
                    TSession::setValue('curso_id', NULL);
                    TSession::setValue('curso_id', $historico_digital->dados_curso_id);
                    
                    unset($param['codhistorico']);
                    $param['key'] = $historico_digital->id;     
                                 
                    TApplication::loadPage('HistoricoAutomaticoInformacoesForm', 'onEdit', $param);
                } 
                
                //Se ainda não foi criado o histórico, preenche campos obrigatórios                
                elseif($count == 0)
                {
                    $curso = DiplomaDigitalCurso::find($verifica_curso[0]->id);
                    $diplomado = DiplomaDigitalDiplomado::find($verifica_diplomado[0]->id);
                
                    //Passa ID do curso como variável de sessão para não perder valor ao executar o construct
                    TSession::setValue('curso_id', NULL);
                    TSession::setValue('curso_id', $curso->id);
                
                    unset($param['key']);
                    unset($param['codhistorico']);
                
                    $param['id_curso'] = $curso->id;
                    $param['id_diplomado'] = $diplomado->id;
                    $param['id_historico_genesi'] = $historico_genesi->codhistorico;
                     
                    TApplication::loadPage('HistoricoAutomaticoInformacoesForm', 'onLoadDadosHistorico', $param);
                }
                
                //Se trouxer mais que um registro
                else
                {
                    new TMessage('error', 'Há mais de um registro de histórico do mesmo aluno e curso. Por favor, contate o setor de TI');
                }
            }
            else
            {
                $action = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                 
                new TMessage('error', 'Verifique se o curso e o aluno foram cadastrados antes de prosseguir', $action);     
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
    
    
    public function onSetDadosComponentes($param)
    {
        try
        {
            $cod_historico = $param['codhistorico'];
            
            TTransaction::open('dados_fei');
            
            $historico_genesi = new FiHistorico($cod_historico);

            TTransaction::close();

            
            TTransaction::open('Felabs_DB');

            //Verifica se existe registro na tabela historico_digital, pois o ID da tabela será salvo como chave estrangeira
            $count = HistoricoDigital::where('historico_genesi_id', '=', $historico_genesi->codhistorico)
                                     ->where('cod_aluno', '=', $historico_genesi->Codaluno)
                                     ->where('cod_curso', '=', $historico_genesi->CodCurso)
                                     ->count();

            if($count == 1)
            {
                $verifica_historico = HistoricoDigital::where('historico_genesi_id', '=', $historico_genesi->codhistorico)
                                                      ->where('cod_aluno', '=', $historico_genesi->Codaluno)
                                                      ->where('cod_curso', '=', $historico_genesi->CodCurso)
                                                      ->load();
                                     
                $historico_digital = HistoricoDigital::find($verifica_historico[0]->id);
                
                //Limpa variável para garantir integridade
                TSession::setValue('dados_historico_genesi', NULL);
                TSession::setValue('dados_historico_digital', NULL);
                                    
                //Passa os dados dos históricos
                TSession::setValue('dados_historico_genesi', $historico_genesi); 
                TSession::setValue('dados_historico_digital', $historico_digital);                           


                TApplication::loadPage('HistoricoAutomaticoComponentesForm', 'onShow');    
            }
            
            //Se ainda não foi criado o histórico
            elseif($count == 0)
            {
                $action = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                    
                new TMessage('error', 'É necessário atualizar as informações gerais do histórico antes de prosseguir', $action);     
                die;
            }
            
            //Se trouxer mais que um registro
            else
            {
                new TMessage('error', 'Há mais de um registro de histórico do mesmo aluno e curso. Por favor, contate o setor de TI');
            }
                        
            TTransaction::close();
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
            $cod_historico_genesi = $param['codhistorico'];
            
            TTransaction::open('dados_fei');
            
            $historico_genesi = new FiHistorico($cod_historico_genesi);
                             
            $verifica_situacoes = VwAlunoMatriculaEtapa::where('Codaluno', '=', $historico_genesi->Codaluno)
                                                       ->where('CodCurso', '=', $historico_genesi->CodCurso)
                                                       ->load();
                                                                           
            TTransaction::close();

            
            TTransaction::open('Felabs_DB');
            
            //Verifica se existem registros para compor o histórico              
            $verifica_historico = HistoricoDigital::where('historico_genesi_id', '=', $historico_genesi->codhistorico)
                                                  ->where('cod_aluno', '=', $historico_genesi->Codaluno)
                                                  ->where('cod_curso', '=', $historico_genesi->CodCurso)
                                                  ->load();
            
            $verifica_disciplinas = HistoricoDigitalDisciplinas::where('historico_digital_id', '=', $verifica_historico[0]->id)->load();
            
            //Atividades e estágios não podem ser requisitos para gerar o xml, pois o aluno pode não ter nenhum registro
            /*$verifica_atividades = AtividadeComplementar::where('cod_aluno', '=', $historico_genesi->Codaluno)
                                                          ->where('cod_curso', '=', $historico_genesi->CodCurso)
                                                          ->where('status_atividade', '=', 'Aprovado')
                                                          ->load();
            
            $verifica_estagios = Estagio::where('cod_aluno', '=', $historico_genesi->Codaluno)
                                        ->where('cod_curso', '=', $historico_genesi->CodCurso)
                                        ->where('status_estagio', '=', 'Aprovado')
                                        ->load();*/
                                                     

            if($verifica_historico AND $verifica_disciplinas AND $verifica_situacoes)
            {
                $historico_digital = HistoricoDigital::find($verifica_historico[0]->id);
                  
                $form = new BootstrapFormBuilder('form_TipoHistoricoAutomatico');
            
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
                $action_cancelar = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                     
                new TMessage('error', 'Verifique se os dados do histórico bem como os componentes curriculares foram lançados antes de prosseguir', $action_cancelar);    
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
            $parametros['formulario_origem'] = "HistoricoAutomaticoList";
            
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
            $cod_historico_genesi = $param['codhistorico'];
            
            TTransaction::open('Felabs_DB');
            
            //Verifica se existe histórico (a contagem garante que é o histórico correto)              
            $count = HistoricoDigital::where('historico_genesi_id', '=', $cod_historico_genesi)->count();
            
            if($count == 1)
            {
                $historico_digital = HistoricoDigital::where('historico_genesi_id', '=', $cod_historico_genesi)->load();

                //Se por acaso o histórico foi publicado, não permite assinatura posterior
                if($historico_digital[0]->status_publicacao == 1)
                {
                    $action1 = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                       
                    new TMessage('error', 'Não é possível inserir assinatura em histórico já publicado', $action1);    
                    die;
                }
                
                
                //Verifica se o XML foi gerado
                if($historico_digital[0]->status_xml == 1)
                {
                    //Verifica se as assinaturas já não foram inseridas
                    if($historico_digital[0]->tipo_historico == "Parcial" OR $historico_digital[0]->tipo_historico == "2ª via parcial")
                    {
                        if($historico_digital[0]->status_assinatura_emissora == 1)
                        {
                            $action2 = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                       
                            new TMessage('error', 'Histórico já assinado', $action2);    
                            die;
                        }      
                    }
                    else
                    {
                        if($historico_digital[0]->status_assinatura_secretaria == 1 AND $historico_digital[0]->status_assinatura_emissora == 1)
                        {
                            $action3 = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                       
                            new TMessage('error', 'Histórico já assinado', $action3);    
                            die;
                        }     
                    }                    
                    
                    $form = new BootstrapFormBuilder('form_InstrucoesAssinaturaHistoricoAutomatico');

                    $tipo_historico = new TEntry('tipo_historico');
                    $status_assinatura_secretaria = new TEntry('status_assinatura_secretaria');
                    $status_assinatura_emissora = new TEntry('status_assinatura_emissora');
                    
                    $tipo_historico->setValue($historico_digital[0]->tipo_historico);
                    $tipo_historico->setEditable(FALSE);
                    $tipo_historico->setSize('100%');
                    $status_assinatura_secretaria->setEditable(FALSE);
                    $status_assinatura_secretaria->setSize('100%');
                    $status_assinatura_emissora->setEditable(FALSE);
                    $status_assinatura_emissora->setSize('100%');
                    
                    //Secretária
                    if($historico_digital[0]->status_assinatura_secretaria == 0)
                    {
                        $status_assinatura_secretaria->setValue('Não preenchida');
                    }
                    else
                    {
                        $status_assinatura_secretaria->setValue('Preenchida');
                    }
                    
                    //Emissora
                    if($historico_digital[0]->status_assinatura_emissora == 0)
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
                   
                    if($historico_digital[0]->tipo_historico == "Parcial" OR $historico_digital[0]->tipo_historico == "2ª via parcial")
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
                    
                    $form->addAction('Assinar XML', new TAction([__CLASS__, 'onAssinarXML'], ['historico_id' => $historico_digital[0]->id]), 'fas: fa-signature');
                    
                    new TInputDialog('Emissão de Histórico', $form);                         
                }
                else
                {
                    $action_cancelar = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                       
                    new TMessage('error', 'É necessário gerar o XML para assiná-lo', $action_cancelar);    
                    die;
                }
            }
            else
            {
                $action_cancelar = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                       
                new TMessage('error', 'É necessário gerar o histórico antes de prosseguir', $action_cancelar);    
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
                                     window.location.href = 'index.php?class=HistoricoAutomaticoList';                                  
                                 }                                        
                             }, 1000);   
                           ");*/
                           
                           
            //PRODUÇÃO
            TScript::create("var popUp = window.open('http://academico.feituverava.com.br:8082/bry/index?id=".$id_historico."&type_doc=XMLHistoricoEscolar','_blank','height=700,width=600,left='+(window.innerWidth-600)/2+',top='+(window.innerHeight-700)/2);                                 
                             setInterval(function() {
                                 if (popUp.closed) {
                                     window.location.href = 'index.php?class=HistoricoAutomaticoList';                                  
                                 }                                        
                             }, 1000);   
                          ");                              
        } 
    }
    
    
    public function onDownloadXml($param)
    {
        try
        {
            $cod_historico = $param['codhistorico'];
               
            TTransaction::open('Felabs_DB');
                
            $historico = HistoricoDigital::where('historico_genesi_id', '=', $cod_historico)->load(); 
            
            $object = new HistoricoDigital($historico[0]->id);

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
            $cod_historico_genesi = $param['codhistorico'];            
            
            TTransaction::open('Felabs_DB');
            
            //Verifica se existe histórico (a contagem garante que é o histórico correto)              
            $count = HistoricoDigital::where('historico_genesi_id', '=', $cod_historico_genesi)->count();
            
            if($count == 1)
            {
                $historico = HistoricoDigital::where('historico_genesi_id', '=', $cod_historico_genesi)->load();

                $historico_digital = new HistoricoDigital($historico[0]->id);
                
                
                //Verifica se o XML foi gerado
                if($historico_digital->status_xml == 0)
                {
                    $action1 = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                       
                    new TMessage('error', 'É necessário gerar o xml antes de prosseguir', $action1);    
                    die;
                } 
                else
                {
                    //Retorna para HistoricoAutomaticoList ou HistoricoManualList dependendo de qual deles chamou a função
                    $formulario_origem = "HistoricoAutomaticoList";
                            
                    TSession::setValue('formulario_origem', NULL);
                    TSession::setValue('formulario_origem', $formulario_origem);
                            
                    $parametros['id_historico_digital'] = $historico_digital->id;

                             
                    TApplication::loadPage('HistoricoRepresentacaoVisualView', 'onLerDadosXml', $parametros); 
    
                }   
            }
            else
            {
                $action2 = new TAction(array('HistoricoAutomaticoList', 'onReload'));                                       
                new TMessage('error', 'Verifique se todas as informações referentes ao histórico foram preenchidas e se o XML foi devidamente gerado antes de prosseguir', $action2);    
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
    
    
    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue(__CLASS__.'_filter_Codaluno', NULL);
        TSession::setValue(__CLASS__.'_filter_Nome', NULL);
        TSession::setValue(__CLASS__.'_filter_CPF', NULL);


        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', '=', "$data->Codaluno");
            TSession::setValue(__CLASS__.'_filter_Codaluno', $filter); 
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('(SELECT Nome from FI_Aluno WHERE Codaluno=FI_Historico.Codaluno)', 'like', "%{$data->Nome}%");
            TSession::setValue(__CLASS__.'_filter_Nome', $filter); 
        }
        
        if (isset($data->CPF) AND ($data->CPF)) {
            $filter = new TFilter('(SELECT CPF from FI_Aluno WHERE Codaluno=FI_Historico.Codaluno)', 'like', "%{$data->CPF}%"); 
            TSession::setValue(__CLASS__.'_filter_CPF', $filter); 
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
            
            TTransaction::open('dados_fei');
            
            //Filtra os cursos da unidade logada
            $repository_curso = new TRepository('FiCurso');
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $cursos = $repository_curso->load($criteria_curso);

            foreach($cursos as $curso)
            {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            
            
            //Exibe só os históricos da unidade logada
            $repository = new TRepository('FiHistorico');
            $limit = 10;
           
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodCurso', 'IN', $items));
      
            if (empty($param['order']))
            {
                $param['order'] = '(SELECT Nome from FI_Aluno WHERE Codaluno=FI_Historico.Codaluno)';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_Codaluno')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Codaluno')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_Nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Nome')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_CPF')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_CPF')); 
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
