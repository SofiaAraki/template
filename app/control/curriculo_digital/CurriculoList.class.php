<?php

class CurriculoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_CurriculoDigital');
        $this->form->setFormTitle('<h4>Currículos Digitais</h4>');
        

        // create the form fields
        $dados_curso_id = new TEntry('dados_curso_id');
        $cod_grade = new TEntry('cod_grade');
        $status_publicacao = new TCombo('status_publicacao');
        
        
        //Status publicado
        $combo_status_publicacao = [];
        $combo_status_publicacao[0] = "Não publicado";
        $combo_status_publicacao[1] = "Publicado";
                        
        $status_publicacao->addItems($combo_status_publicacao);


        // add the fields
        $this->form->addFields( [ new TLabel('Curso') ], [ $dados_curso_id ] );
        $this->form->addFields( [ new TLabel('Grade') ], [ $cod_grade ] );
        $this->form->addFields( [ new TLabel('Status Publicação') ], [ $status_publicacao ] );


        // set sizes
        $dados_curso_id->setSize('80%');
        $cod_grade->setSize('80%');
        $status_publicacao->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $btn = $this->form->addActionLink('Instruções', new TAction([$this, 'onShowInstrucoes']), 'fas:align-justify');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addActionLink('Cadastrar currículo', new TAction(['CurriculoForm', 'onEdit']), 'fa:plus green');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_dados_curso_id = new TDataGridColumn('diploma_digital_curso->nome_curso_diploma', 'Curso', 'left');
        $column_cod_grade = new TDataGridColumn('cod_grade', 'Cód. Grade', 'center');
        $column_codigo_curriculo = new TDataGridColumn('codigo_curriculo', 'Cód. Currículo', 'center');
        $column_data_curriculo = new TDataGridColumn('data_curriculo', 'Vigência', 'center');
        $column_status_publicacao = new TDataGridColumn('status_publicacao', 'Status Publicação', 'center');
        $column_data_publicacao = new TDataGridColumn('data_publicacao', 'Data Publicação', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dados_curso_id);
        $this->datagrid->addColumn($column_cod_grade);
        $this->datagrid->addColumn($column_codigo_curriculo);
        $this->datagrid->addColumn($column_data_curriculo);
        $this->datagrid->addColumn($column_status_publicacao);
        $this->datagrid->addColumn($column_data_publicacao);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);


        $column_dados_curso_id->setTransformer( function($value, $object, $row) {
            return mb_strtoupper($value);
        });
    
    
        $column_data_curriculo->setTransformer( function($value, $object, $row) {
            if ($value)
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
            return $value;
        });

       
        $column_data_publicacao->setTransformer( function($value, $object, $row) {
            if ($value)
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
            return $value;
        });


        $action_excluir = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        $action_gerais = new TDataGridAction(['CurriculoForm', 'onEdit'], ['id'=>'{id}']);      
        $action_ementario = new TDataGridAction([$this, 'onSetDadosEmentario'], ['id'=>'{id}']);
        $action_optativas = new TDataGridAction([$this, 'onSetDadosOptativas'], ['id'=>'{id}']);
        $action_atividades = new TDataGridAction([$this, 'onSetDadosAtividadeComplementar'], ['id'=>'{id}']);
        $action_estrutura = new TDataGridAction([$this, 'onSetDadosEstruturaCurricular'], ['id'=>'{id}']);
        $action_integralizacao = new TDataGridAction([$this, 'onSetDadosCriteriosIntegralizacao'], ['id'=>'{id}']);        
        $action_gerar = new TDataGridAction([$this, 'onSetDadosGerarXmlCurriculo'], ['id'=>'{id}']);
        $action_assinar = new TDataGridAction([$this, 'onSetDadosAssinarXml'], ['id'=>'{id}']);
        $action_xml = new TDataGridAction([$this, 'onDownloadXml'], ['id'=>'{id}']);
        $action_publicar = new TDataGridAction([$this, 'onSetDadosPublicarCurriculo'], ['id'=>'{id}']);
        
        
        $action_excluir->setLabel('Excluir');
        $action_excluir->setImage('far:trash-alt red');
        $action_excluir->setDisplayCondition( array($this, 'displayColumnDelete') );
        
        $action_gerais->setLabel('Editar informações principais');
        $action_gerais->setImage('fas:pencil-alt orange');

        $action_ementario->setLabel('Lançar disciplinas da grade');
        $action_ementario->setImage('fas: fa-list-ul #008080');
        
        $action_optativas->setLabel('Lançar disciplinas optativas');
        $action_optativas->setImage('fas: fa-list-ul #008080');
        
        $action_atividades->setLabel('Lançar atividades complementares');
        $action_atividades->setImage('fas: fa-list-ul #008080');
        
        $action_estrutura->setLabel('Estrutura Curricular');
        $action_estrutura->setImage('fas:align-left #191970');

        $action_integralizacao->setLabel('Definir Critérios de Integralização');
        $action_integralizacao->setImage('fas: fa-tags red');
        
        $action_gerar->setLabel('Gerar XML');
        $action_gerar->setImage('fa:sync green');
        
        $action_assinar->setLabel('Assinar com certificado');
        $action_assinar->setImage('fas: fa-signature');
        
        $action_xml->setLabel('Download XML');
        $action_xml->setImage('fas:cloud-download-alt blue');
        
        $action_publicar->setLabel('Conferir e publicar currículo');
        $action_publicar->setImage('fa:check green');
        
        
        $action_group = new TDataGridActionGroup('Ações ', 'fa:th');
        $action_group->addAction($action_excluir);
        $action_group->addHeader('Currículo');        
        $action_group->addAction($action_gerais);
        $action_group->addAction($action_ementario);
        $action_group->addAction($action_optativas);
        $action_group->addAction($action_atividades);
        $action_group->addAction($action_estrutura);
        $action_group->addAction($action_integralizacao);
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


    public static function onShowInstrucoes($param)
    {
        $win = TWindow::create('Instruções sobre o Currículo Escolar Digital', 0.8, 0.8);
        $win->add("
    <br>- Deve ser emitido sempre que houver uma mudança curricular no curso, <b>devendo ser disponibilizado assim que entrar em vigor</b>. <br> 
    
    <br>- Deve ser <b>reemitido</b> sempre que a versão com a qual foi emitido for descontinuada devido a política de atualização dos arquivos digitais. <br>
    
    <br>- O código do currículo deve ser <b>único</b> e utilizado pela IES para referenciá-lo internamente. Cada histórico escolar (1ª via) deverá ser 
    <b>obrigatoriamente</b> associado a um currículo. <br>
    
    <br>- Considera-se para todos os efeitos que, <b>uma vez publicado</b>, o mesmo <b>não pode mais sofrer alterações</b>, com exceção da inclusão de novas
    Equivalências em Unidades Curriculares. <b>Quaisquer outras alterações exigem a criação de um novo Currículo Escolar, 
    com um novo código único</b>, a ser publicado pela IES. <br>
    
    <br>- A data do currículo é um elemento de inclusão obrigatória e indica a data em que o mesmo foi aprovado e passou a ser aplicado. <br>
    
    <br>- <b>Para fins de cômputo da carga horária total do curso será considerada a carga horária em horas-relógio de cada unidade curricular presente 
    no currículo</b>. <br>
    
    <br>- <b>Áreas:</b> deve ser usada caso o Projeto Pedagógico do Curso (PPC) preveja a noção de Áreas de Concentração, Competências, Linhas de Formação ou 
    equivalentes. Neste caso, cada unidade curricular será associada à(s) área(s) que ela 'atende'. <br> 
    
    <br>- <b>Etiquetas:</b> classificam as unidades dentro do currículo para fins de cômputo da integralização curricular. Uma unidade pode receber mais de uma 
    etiqueta. Todo currículo possui um conjunto mínimo de Etiquetas. São elas: Obrigatória <b>(código ob)</b> que caracteriza unidades curriculares que devem ser 
    obrigatoriamente cursadas pelos alunos para integralizar o currículo e Extensão <b>(código ext)</b> que caracteriza unidades curriculares cuja carga horária, 
    total ou parcial, deve ser usada para cômputo da carga de extensão do mesmo. <br>
    
    <br>- <b>Pré-Requisito(s):</b> elemento de inclusão opcional usado para declarar unidade(s) curricular(es) que deve(m) ser <b>obrigatoriamente</b> cursadas
    pelo aluno <b>antes</b> de cursar determinada unidade. <br> 
         
    <br>- <b>Informações Adicionais:</b> é um campo textual opcional que pode ser usado para inserir informações adicionais ao currículo. Não devem ser inseridas informações 
    neste elemento caso a informação seja estruturada em outro campo. <br>     
        ");
        
        $win->show();
    }


    public function onSetDadosEmentario($param)
    {
        //Retorna para CurriculoList ou EstruturaCurricularList dependendo de qual deles chamou a função
        $datagrid_origem = "CurriculoList";
                            
        TSession::setValue('datagrid_origem', NULL);
        TSession::setValue('datagrid_origem', $datagrid_origem);
         
        //Passa ID do currículo como parâmetro 
        $id = $param['id'];
                
        unset($param['id']);
        unset($param['key']);
            
        $param['curriculo_id'] = $id;
    
        TApplication::loadPage('DisciplinaGradeCurriculoForm', 'onLoad', $param);  
    }
    
    
    public function onSetDadosOptativas($param)
    {
        //Retorna para CurriculoList ou EstruturaCurricularList dependendo de qual deles chamou a função
        $datagrid_origem = "CurriculoList";
                            
        TSession::setValue('datagrid_origem', NULL);
        TSession::setValue('datagrid_origem', $datagrid_origem);
         
        //Passa ID do currículo como parâmetro 
        $id = $param['id'];
                
        unset($param['id']);
        unset($param['key']);
            
        $param['curriculo_id'] = $id;
    
        TApplication::loadPage('DisciplinaOptativaCurriculoForm', 'onLoad', $param); 
    }
    
    
    public function onSetDadosAtividadeComplementar($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $curriculo_id = $param['id'];
            
            $curriculo = new CurriculoDigital($curriculo_id);
            $curso = new DiplomaDigitalCurso($curriculo->dados_curso_id);
            
            //Verifica se foram cadastradas categorias e atividades para o curso
            $categorias = AtividadeComplementarCategoria::where('dados_curso_id', '=', $curso->id)->load();
            
            foreach($categorias as $categoria)
            {
                $ids_categorias[$categoria->id] = $categoria->id;
            }
            
            if($categorias)
            {
                $atividades = AtividadeComplementarCadastro::where('categoria_id', 'IN', $ids_categorias)->load();
            }
            
            
            if($categorias AND $atividades)
            {
                //Limpa variável para garantir integridade
                TSession::setValue('dados_curriculo', NULL);
                TSession::setValue('dados_curso', NULL);
                                        
                //Passa os dados do currículo e curso
                TSession::setValue('dados_curriculo', $curriculo); 
                TSession::setValue('dados_curso', $curso);                            
    
                TApplication::loadPage('AtividadeComplementarCurriculoForm', 'onShow', $param);     
            }
            else
            {
                new TMessage('error', 'É necessário cadastrar as atividades complementares e suas respectivas categorias antes de prosseguir');
                return false;
            }
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onSetDadosEstruturaCurricular($param)
    {
        //Passa ID do currículo como parâmetro 
        $id = $param['id'];
                
        unset($param['id']);
        unset($param['key']);
            
        $param['curriculo_id'] = $id;

        TApplication::loadPage('EstruturaCurricularList', 'onShow', $param);
    }


    public function onSetDadosCriteriosIntegralizacao($param)
    {
        try
        {
            $curriculo_id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $curriculo = new CurriculoDigital($curriculo_id);
            $curso = new DiplomaDigitalCurso($curriculo->dados_curso_id);
            
            //1º Verifica o ano inicial da grade (2023 se tornou obrigatório informar a extensão)
            TTransaction::open('dados_fei');
            
            $grade = new FiGradeCurso($curriculo->cod_grade);
            $ano_inicial = $grade->AnoInicial;
            
            TTransaction::close();
            
            
            //2º Verifica se as unidades foram lançadas
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('curriculo_id', '=', $curriculo->id));
            $criteria1->add(new TFilter('opcao_disciplina', '=', 'Grade'));
            
            $unidades = CurriculoDisciplina::getObjects($criteria1);
            
            
            //3º Verifica se as atividades complementares foram estruturadas
            $criteria2 = new TCriteria;
            $criteria2->add(new TFilter('curriculo_id', '=', $curriculo->id));
            
            $categorias = CurriculoAtividadeCategoria::getObjects($criteria2);
            
            if($categorias)
            {
                foreach($categorias as $categoria)
                {
                    $ids_curriculo_categorias[] = $categoria->id;    
                }
                    
                $atividades = CurriculoAtividadeCadastro::where('curriculo_atividade_categoria_id', 'IN', $ids_curriculo_categorias)->load();
            }
            
            
            if($unidades AND $categorias AND $atividades)
            {
                //Se o ano inicial é anterior a 2023, não precisa verificar a carga horária de extensão (não era obrigatório discriminar)
                if($ano_inicial < 2023)
                {
                    //Limpa variável para garantir integridade
                    TSession::setValue('ch_total_curso_ha', NULL);
                    TSession::setValue('ch_total_curso_hr', NULL);
                    TSession::setValue('dados_curriculo', NULL);
                    TSession::setValue('dados_curso', NULL);
                                                    
                    //Passa os dados do currículo e curso
                    TSession::setValue('dados_curriculo', $curriculo); 
                    TSession::setValue('dados_curso', $curso);                           
                
                    unset($param['id']);
                    unset($param['key']);
                
                    TApplication::loadPage('CriterioIntegralizacaoForm', 'onShow', $param);
                }
                
                //Se o ano inicial for igual ou superior a 2023, verifica se a carga horária total possui os 10% de extensão antes de redirecionar a página
                else
                {
                    foreach($unidades as $unidade)
                    {
                        $ch_aula_curso += $unidade->ch_hora_aula;
                        $ch_relogio_curso += $unidade->ch_hora_relogio;
                    }

    
                    //A etiqueta de extensão é a única que recebe carga horária
                    $criteria3 = new TCriteria;
                    $criteria3->add(new TFilter('curriculo_digital_id', '=', $curriculo->id));
                    $criteria3->add(new TFilter('opcao_disciplina', '=', 'Grade'));
                    $criteria3->add(new TFilter('ch_hora_aula_etiqueta', 'IS NOT', NULL));
                    $criteria3->add(new TFilter('ch_hora_relogio_etiqueta', 'IS NOT', NULL));
                                    
                    $verifica_extensao = VwCriterioIntegralizacaoCurriculo::getObjects($criteria3);
                    
                    if($verifica_extensao)
                    {
                        foreach($verifica_extensao as $extensao)
                        {
                            $ch_aula_extensao += $extensao->ch_hora_aula_etiqueta;
                            $ch_relogio_extensao += $extensao->ch_hora_relogio_etiqueta;
                        }
                                            
                        $dez_por_cento_curso_ha = ($ch_aula_curso/10);
                        $dez_por_cento_curso_hr = ($ch_relogio_curso/10);

                        if(($dez_por_cento_curso_ha == $ch_aula_extensao) AND ($dez_por_cento_curso_hr == $ch_relogio_extensao))
                        {
                            //Limpa variável para garantir integridade
                            TSession::setValue('ch_total_curso_ha', NULL);
                            TSession::setValue('ch_total_curso_hr', NULL);
                            TSession::setValue('dados_curriculo', NULL);
                            TSession::setValue('dados_curso', NULL);
                                                    
                            //Passa os dados do currículo e curso
                            TSession::setValue('dados_curriculo', $curriculo); 
                            TSession::setValue('dados_curso', $curso);                           
                
                            unset($param['id']);
                            unset($param['key']);
                
                            TApplication::loadPage('CriterioIntegralizacaoForm', 'onShow', $param);    
                        }
                        else
                        {
                            $action_cancelar1 = new TAction(array('CurriculoList', 'onReload'));                                     
                            new TMessage('error', 'Verifique se a carga de extensão do currículo corresponde a 10% da carga horária do curso', $action_cancelar1);    
                            die; 
                        }
                    }
                    else
                    {
                        $action_cancelar2 = new TAction(array('CurriculoList', 'onReload'));                                     
                        new TMessage('error', 'Verifique se a carga de extensão do currículo foi lançada antes de prosseguir', $action_cancelar2);    
                        die; 
                    }
                }        
            }
            else
            {
                $action_cancelar3 = new TAction(array('CurriculoList', 'onReload'));                                     
                new TMessage('error', 'Verifique se as unidades curriculares foram lançadas e as atividades complementares estruturadas antes de prosseguir', $action_cancelar3);    
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
    
    
    public function onSetDadosGerarXmlCurriculo($param)
    {
        try
        {
            $id_curriculo = $param['id'];                        

            TTransaction::open('Felabs_DB');
            
            $curriculo_digital = new CurriculoDigital($id_curriculo);            
           
            //Verifica se existem registros para compor o currículo           
            $verifica_disciplinas = CurriculoDisciplina::where('curriculo_id', '=', $curriculo_digital->id)->load();
            $verifica_atividades = CurriculoAtividadeCategoria::where('curriculo_id', '=', $curriculo_digital->id)->load();
            $verifica_criterios = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo_digital->id)->load();
    
            if($verifica_disciplinas AND $verifica_atividades AND $verifica_criterios)
            {   
                $parametros['curriculo_id'] = $curriculo_digital->id;
                
                TApplication::loadPage('XMLCurriculoForm', 'onVerificarXMLCurriculo', $parametros);                            
            }                
            else
            {
                $action_cancelar = new TAction(array('CurriculoList', 'onReload'));                                     
                new TMessage('error', 'Verifique se as unidades curriculares foram lançadas, as atividades complementares estruturadas e os critérios de integralização definidos antes de prosseguir', $action_cancelar);    
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
    
    
    public function onSetDadosAssinarXml($param)
    {
        try
        {
            $id_curriculo = $param['id'];
            
            TTransaction::open('Felabs_DB');            

            $curriculo_digital = new CurriculoDigital($id_curriculo);


            //Se o currículo foi publicado, não permite assinatura posterior
            if($curriculo_digital->status_publicacao == 1)
            {
                $action1 = new TAction(array('CurriculoList', 'onReload'));                                       
                new TMessage('error', 'Não é possível inserir assinatura em currículo já publicado', $action1);    
                die;
            }
                
                
            //Verifica se o XML foi gerado
            if($curriculo_digital->status_xml == 1)
            {
                //Verifica se as assinaturas já não foram inseridas (Inicialmente, só a emissora vai assinar)
                if($curriculo_digital->status_assinatura_emissora == 1)
                {
                    $action2 = new TAction(array('CurriculoList', 'onReload'));                                       
                    new TMessage('error', 'Currículo já assinado', $action2);    
                    die;
                }      
                else
                {
                    //HOMOLOGAÇÃO - versão 1.05 (acrescentou /index)               
                    // TScript::create("var popUp = window.open('http://localhost:8082/bry/index?id=".$id_curriculo."&type_doc=XMLCurriculoEscolar','_blank','height=700,width=600,left='+(window.innerWidth-600)/2+',top='+(window.innerHeight-700)/2);                                 
                    //                  setInterval(function() {
                    //                      if (popUp.closed) {
                    //                          window.location.href = 'index.php?class=CurriculoList';                                  
                    //                      }                                        
                    //                  }, 1000);   
                    //                ");
                                   
                    
                    //PRODUÇÃO
                    TScript::create("var popUp = window.open('http://academico.feituverava.com.br:8082/bry/index?id=".$id_curriculo."&type_doc=XMLCurriculoEscolar','_blank','height=700,width=600,left='+(window.innerWidth-600)/2+',top='+(window.innerHeight-700)/2);                                 
                                     setInterval(function() {
                                         if (popUp.closed) {
                                             window.location.href = 'index.php?class=CurriculoList';                                  
                                         }                                        
                                     }, 1000);   
                                   ");               
                }                                   
            }
            else
            {
                $action_cancelar = new TAction(array('CurriculoList', 'onReload'));                                       
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
    
    
    public function onDownloadXml($param)
    {
        try
        {
            $curriculo_id = $param['id'];
               
            TTransaction::open('Felabs_DB');
                
            $object = new CurriculoDigital($curriculo_id);

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
    
    
    public function onSetDadosPublicarCurriculo($param)
    {
        try
        {
            $id_curriculo = $param['id'];            
            
            TTransaction::open('Felabs_DB');
            
            $curriculo_digital = new CurriculoDigital($id_curriculo);                
                
            //Verifica se o XML foi gerado
            if($curriculo_digital->status_xml == 0)
            {
                $action1 = new TAction(array('CurriculoList', 'onReload'));                                       
                new TMessage('error', 'É necessário gerar o currículo antes de prosseguir', $action1);    
                die;
            } 
            else
            {         
                $parametros['id_curriculo_digital'] = $curriculo_digital->id;                             
                TApplication::loadPage('CurriculoRepresentacaoVisualView', 'onLerDadosXml', $parametros);         
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
        
        TSession::setValue(__CLASS__.'_filter_dados_curso_id', NULL);
        TSession::setValue(__CLASS__.'_filter_cod_grade', NULL);
        TSession::setValue(__CLASS__.'_filter_status_publicacao', NULL);

        if (isset($data->dados_curso_id) AND ($data->dados_curso_id)) {
            $filter = new TFilter('(SELECT nome_curso_diploma FROM dados_curso WHERE id=curriculo_digital.dados_curso_id)', 'like', "%{$data->dados_curso_id}%");
            TSession::setValue(__CLASS__.'_filter_dados_curso_id', $filter); 
        }


        if (isset($data->cod_grade) AND ($data->cod_grade)) {
            $filter = new TFilter('cod_grade', '=', $data->cod_grade); 
            TSession::setValue(__CLASS__.'_filter_cod_grade', $filter); 
        }


        if ($data->status_publicacao <> NULL) {
            $filter = new TFilter('status_publicacao', '=', $data->status_publicacao);
            TSession::setValue(__CLASS__.'_filter_status_publicacao', $filter);
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
            
            TTransaction::close();
            
            
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('CurriculoDigital');
            $limit = 30;
  
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_curso', 'IN', $items));
            
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_dados_curso_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_dados_curso_id')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_cod_grade')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cod_grade')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_status_publicacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status_publicacao')); 
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
                    //STATUS PUBLICAÇÃO                  
                    if($object->status_publicacao == 0)
                    {
                        $object->status_publicacao = "<span class='label label-danger'>Não publicado</span>";
                    }
                    elseif($object->status_publicacao == 1)
                    {
                        $object->status_publicacao = "<span class='label label-success'>Publicado</span>";
                    }
                    else
                    {
                        $object->status_publicacao = $object->status_publicacao;
                    }
                    
                    $hr = substr($object->data_reg, 11, 19);
                    $dt = TDate::date2br($object->data_reg);
                    $object->data_reg = "$dt" . " " . substr($hr,0,-7);
                    
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
    public function displayColumnDelete( $object )
    {
        $grupo_admin = 1;
        $user_groups = TSession::getValue('usergroupids');
                
        if(( in_array($grupo_admin, $user_groups)))
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
            $curriculo = new CurriculoDigital($key);
           
            /*Não permite a exclusão se houver registros vinculados ou se já foi publicado*/
            if((CurriculoAtividadeCategoria::where('curriculo_id', '=', $curriculo->id)->count() > 0) OR
               (CurriculoAtividadeCadastro::where('curriculo_id', '=', $curriculo->id)->count() > 0) OR 
               (CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo->id)->count() > 0) OR
               (CurriculoDisciplina::where('curriculo_id', '=', $curriculo->id)->count() > 0) OR
               (HistoricoDigital::where('curriculo_id', '=', $curriculo->id)->count() > 0) OR
               ($curriculo->data_primeira_publicacao <> NULL))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) ao currículo');
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
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new CurriculoDigital($key, FALSE); 
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
