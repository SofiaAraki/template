<?php

class BuscaDisciplinaHistorico extends TWindow
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    

    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Buscar disciplina');
        parent::setSize(0.95, null);
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_VwHistoricodisciplina');
                

        // create the form fields
        $Etapa = new TEntry('Etapa');
        $NomeDisciplina = new TEntry('NomeDisciplina');


        // add the fields
        $this->form->addFields( [ new TLabel('Etapa') ], [ $Etapa ] );
        $this->form->addFields( [ new TLabel('Disciplina') ], [ $NomeDisciplina ] );


        // set sizes
        $Etapa->setSize('80%');
        $NomeDisciplina->setSize('80%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwHistoricodisciplina_filter_data') );
        
        
        // add the search form actions
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
                
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_Etapa = new TDataGridColumn('Etapa', 'Etapa', 'center');
        $column_Ano = new TDataGridColumn('Ano', 'Ano', 'center');
        $column_Sem = new TDataGridColumn('Sem', 'Sem', 'center');
        $column_CodDisciplina = new TDataGridColumn('CodDisciplina', 'Cód.', 'right');
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left');
        $column_NotaFinal = new TDataGridColumn('NotaFinal', 'Nota', 'center');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'CH', 'center');
        $column_Sit = new TDataGridColumn('Sit', 'Situação', 'center'); 
        $column_codigo_professor = new TDataGridColumn('codigo_professor', 'Cód.', 'right');
        $column_nome_professor = new TDataGridColumn('nome_professor', 'Professor', 'left');
        $column_titulacao_professor = new TDataGridColumn('titulacao_professor', 'Titulação', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_Etapa);
        $this->datagrid->addColumn($column_Ano);
        $this->datagrid->addColumn($column_Sem);
        $this->datagrid->addColumn($column_CodDisciplina);
        $this->datagrid->addColumn($column_NomeDisciplina);
        $this->datagrid->addColumn($column_NotaFinal);
        $this->datagrid->addColumn($column_carga_horaria);
        $this->datagrid->addColumn($column_Sit);        
        $this->datagrid->addColumn($column_codigo_professor);
        $this->datagrid->addColumn($column_nome_professor);
        $this->datagrid->addColumn($column_titulacao_professor);
        
        
        // create SELECT action
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(TRUE);
        $action_select->setButtonClass('nopadding');
        $action_select->setLabel('');
        $action_select->setImage('fa:hand-pointer green');
        $action_select->setFields(['codhistorico' , 'CodDisciplina', 'CodGradeDisciplinaEtapa_Frente']);
        $this->datagrid->addAction($action_select);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $dados_historico_digital = TSession::getValue('dados_historico_digital');
            $nome_aluno = $dados_historico_digital->diploma_digital_diplomado->nome;
            $nome_curso = $dados_historico_digital->diploma_digital_curso->nome_curso_diploma;
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }  
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%;margin-bottom:0;border-radius:0';
        $container->add($this->form);
        $container->add(TPanelGroup::pack("<b>$nome_aluno :</b> $nome_curso", $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
 
    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue(__CLASS__.'_filter_Etapa', NULL);
        TSession::setValue(__CLASS__.'_filter_NomeDisciplina', NULL);

        if (isset($data->Etapa) AND ($data->Etapa)) {
            $filter = new TFilter('Etapa', '=', $data->Etapa); 
            TSession::setValue(__CLASS__.'_filter_Etapa',   $filter); 
        }


        if (isset($data->NomeDisciplina) AND ($data->NomeDisciplina)) {
            $filter = new TFilter('NomeDisciplina', 'like', "%{$data->NomeDisciplina}%");
            TSession::setValue(__CLASS__.'_filter_NomeDisciplina',   $filter); 
        }


        $this->form->setData($data);
        
        TSession::setValue('VwHistoricodisciplina_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('dados_fei');
             
            $dados_historico_digital = TSession::getValue('dados_historico_digital');
                        
            $repository = new TRepository('VwHistoricodisciplina');
            $limit = 10;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('codhistorico', '=', $dados_historico_digital->historico_genesi_id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'Etapa';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_Etapa')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_Etapa')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_NomeDisciplina')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_NomeDisciplina')); 
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
                    //Exibe a carga horária na coluna carga_horaria independentemente da coluna que ela esteja na view 
                    if($object->CHParcial)
                    {
                        $object->carga_horaria = $object->CHParcial;
                    }
                    elseif($object->CH)
                    {
                        $object->carga_horaria = $object->CH;
                    }
                    else
                    {
                        $object->carga_horaria = '';
                    }
                    
                
                    //Exibe o código do professor na coluna codigo_professor independentemente da coluna que ele esteja na view
                    if($object->Codprofessor)
                    {
                        $object->codigo_professor = $object->Codprofessor;
                    }
                    elseif($object->CodProf)
                    {
                        $object->codigo_professor = $object->CodProf;
                    }
                    else
                    {
                        $object->codigo_professor = '';
                    }
                    
                    
                    //Exibe o nome do professor na coluna nome_professor independentemente da coluna que ele esteja na view
                    if($object->nome)
                    {
                        $object->nome_professor = mb_strtoupper($object->nome);
                    }
                    elseif($object->NomeProf)
                    {
                        $object->nome_professor = mb_strtoupper($object->NomeProf);
                    }
                    else
                    {
                        $object->nome_professor = '';
                    }
                    
                    
                    //Exibe a titulação do professor na coluna titulacao_professor independentemente da coluna que ela esteja na view
                    if($object->HabilitacaoProf3)
                    {
                        $object->titulacao_professor = $object->HabilitacaoProf3;
                    }
                    elseif($object->TituloProf)
                    {
                        $object->titulacao_professor = $object->TituloProf;
                    }
                    else
                    {
                        $object->titulacao_professor = '';
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
    

    public static function onSelect($param)
    {
        try
        {
            $cod_historico = $param['codhistorico'];
            $cod_disciplina = $param['CodDisciplina'];
            $cod_grade_disciplina_etapa_frente = $param['CodGradeDisciplinaEtapa_Frente'];
            
            TTransaction::open('dados_fei');
            
            $object = VwHistoricodisciplina::where('codhistorico', '=', $cod_historico)
                                           ->where('CodDisciplina', '=', $cod_disciplina)
                                           ->where('CodGradeDisciplinaEtapa_Frente', '=', $cod_grade_disciplina_etapa_frente)
                                           ->load(); 
                        
            TTransaction::close();
            
            //Formata os campos para serem adicionados ao formulário
            if($object)
            {
                foreach($object as $item_object)
                {               
                    $send = new StdClass;
                    $send->cod_disciplina = $item_object->CodDisciplina;
                    $send->nome_disciplina = $item_object->NomeDisciplina;
                    $send->ano = $item_object->Ano;
                    $send->semestre = $item_object->Sem;
                    $send->etapa = $item_object->Etapa;
                    $send->frequencia = $item_object->Freq;
                    
                    
                    //Código do professor
                    if($item_object->Codprofessor)
                    {
                        $send->cod_professor = $item_object->Codprofessor;
                    }
                    elseif($item_object->CodProf)
                    {
                        $send->cod_professor = $item_object->CodProf;
                    }
                    else
                    {
                        $send->cod_professor = '';
                    }
                    
                    
                    //Nome do professor
                    if($item_object->nome)
                    {
                        $send->nome_professor = mb_strtoupper($item_object->nome);
                    }
                    elseif($item_object->NomeProf)
                    {
                        $send->nome_professor = mb_strtoupper($item_object->NomeProf);   
                    }
                    else
                    {
                        $send->nome_professor = '';
                    }
                    
                    
                    //Titulação do professor
                    if($item_object->HabilitacaoProf3)
                    {
                        if($item_object->HabilitacaoProf3 == "TECNÓLOGO")
                        {
                            $send->titulacao_professor = 'Tecnólogo';
                        }
                        elseif($item_object->HabilitacaoProf3 == "GRADUAÇÃO")
                        {
                            $send->titulacao_professor = 'Graduação';
                        }
                        elseif($item_object->HabilitacaoProf3 == "ESPECIALIZAÇÃO")
                        {
                            $send->titulacao_professor = 'Especialização';
                        }
                        elseif($item_object->HabilitacaoProf3 == "MESTRADO")
                        {
                            $send->titulacao_professor = 'Mestrado';
                        }
                        elseif($item_object->HabilitacaoProf3 == "DOUTORADO")
                        {
                            $send->titulacao_professor = 'Doutorado';
                        }
                        else
                        {
                            $send->titulacao_professor = '';
                        }
                    }                
                    elseif($item_object->TituloProf)
                    {
                        if($item_object->TituloProf == "TECNÓLOGO")
                        {
                            $send->titulacao_professor = 'Tecnólogo';
                        }
                        elseif($item_object->TituloProf == "GRADUAÇÃO")
                        {
                            $send->titulacao_professor = 'Graduação';
                        }
                        elseif($item_object->TituloProf == "ESPECIALIZAÇÃO")
                        {
                            $send->titulacao_professor = 'Especialização';
                        }
                        elseif($item_object->TituloProf == "MESTRADO")
                        {
                            $send->titulacao_professor = 'Mestrado';
                        }
                        elseif($item_object->TituloProf == "DOUTORADO")
                        {
                            $send->titulacao_professor = 'Doutorado';
                        }
                        else
                        {
                            $send->titulacao_professor = '';
                        }
                    }
                    else
                    {
                        $send->titulacao_professor = '';
                    }


                    //Carga horária
                    if($item_object->CHParcial)
                    {
                        //CH em que a disciplina tem divisão de frente (ex:33,5) precisou de formatação, pois no banco está como varchar
                        if(!is_numeric($item_object->CHParcial))
                        {
                            $item_object->CHParcial = str_replace(',', '.', $item_object->CHParcial);
                            $item_object->CHParcial = floatval($item_object->CHParcial);                             
                            $item_object->CHParcial = number_format($item_object->CHParcial, 2, '.', '');
                            $item_object->CHParcial = $item_object->CHParcial;  
                            
                            $send->carga_horaria = $item_object->CHParcial;  
                        } 
                        else
                        {
                            $send->carga_horaria = $item_object->CHParcial;
                        }                        
                    }
                    elseif($item_object->CH)
                    {
                        $send->carga_horaria = $item_object->CH;    
                    }
                    else
                    {
                        $send->carga_horaria = '';
                    }
                        

                    //Nota
                    $item_object->NotaFinal = str_replace(',', '.', $item_object->NotaFinal);                    
                    $item_object->NotaFinal = floatval($item_object->NotaFinal);        
                    $item_object->NotaFinal = number_format($item_object->NotaFinal, 2, '.', '');                                        
                    $item_object->NotaFinal = $item_object->NotaFinal;
                    
                    $send->nota = $item_object->NotaFinal;
                    
                    
                    //Situação
                    if($item_object->Sit == "APROVADO(A)" OR $item_object->Sit == "APROV. ESTUDO")
                    {
                        $send->situacao = "Aprovado";
                    }
                    elseif($item_object->Sit == "REPROVADO(A)")
                    {
                        $send->situacao = "Reprovado";
                    }
                    else
                    {
                        $send->situacao = "Pendente";
                    }
                    
                    
                    //Forma de integralização
                    $unidade_logada = TSession::getValue('userunitid');
                    
                    if($unidade_logada == 3) //FAFRAM padroniza a forma de integralização
                    {
                        if($item_object->Sit == "APROVADO(A)")
                        {
                            $send->forma_integralizacao = "Cursado";
                        }
                        elseif($item_object->Sit == "APROV. ESTUDO")
                        {
                            $send->forma_integralizacao = "Aproveitado";
                        }
                        else
                        {
                            $send->forma_integralizacao = '';
                        }
                    }
                    else
                    {
                        $send->forma_integralizacao = '';
                    }                    
                }    
            
                TForm::sendData('form_HistoricoAutomaticoDisciplinas', $send);
            }
            
            parent::closeWindow(); 
        }
        catch (Exception $e)
        {
            $send = new StdClass;
            $send->cod_disciplina = '';
            $send->nome_disciplina = '';
            $send->ano = '';
            $send->semestre = '';
            $send->etapa = '';
            $send->frequencia = '';
            $send->cod_professor = '';
            $send->nome_professor = '';
            $send->titulacao_professor = '';
            $send->carga_horaria = '';            
            $send->nota = '';
            $send->situacao = '';
            $send->forma_integralizacao = '';
            
            TForm::sendData('form_HistoricoAutomaticoDisciplinas', $send);
            
            TTransaction::rollback();
        }
    }

    //-------------SOFIA----------------------//
    public static function onSelectAll($param)
    {
        try
        {
            // 1. Coleta de dados da Origem (Genesi)
            TTransaction::open('dados_fei');
            $dados_historico_digital = TSession::getValue('dados_historico_digital');
            
            if (empty($dados_historico_digital->historico_genesi_id)) {
                throw new Exception("ID do histórico Genesi não encontrado na sessão.");
            }

            $repository = new TRepository('VwHistoricodisciplina');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('codhistorico', '=', $dados_historico_digital->historico_genesi_id));
            $objects = $repository->load($criteria);
            TTransaction::close();

            if ($objects)
            {
                TTransaction::open('Felabs_DB'); // Abre transação no banco Destino
                
                foreach ($objects as $item_object)
                {
                    // 2. Lógica de Verificação (Existe ou Novo?)
                    // Buscamos se já existe essa disciplina para este aluno no mesmo período
                    $existente = HistoricoDigitalDisciplinas::where('historico_digital_id', '=', $dados_historico_digital->id)
                                                            ->where('cod_disciplina', '=', $item_object->CodDisciplina)
                                                            ->where('ano', '=', $item_object->Ano)
                                                            ->where('semestre', '=', $item_object->Sem)
                                                            ->first();

                    // Se existir, carregamos o objeto; se não, instanciamos um novo
                    $historico_digital_disciplinas = $existente ?? new HistoricoDigitalDisciplinas;
                    
                    // 3. Mapeamento de Cabeçalho e Auditoria
                    $historico_digital_disciplinas->historico_digital_id = $dados_historico_digital->id;
                    $historico_digital_disciplinas->system_user_id       = TSession::getValue('userid');
                    $historico_digital_disciplinas->data_reg             = date('Y-m-d H:i:s');
                    $historico_digital_disciplinas->tipo_entrada         = 'Disciplina';

                    // 4. Mapeamento de Dados Educacionais
                    $historico_digital_disciplinas->cod_disciplina    = $item_object->CodDisciplina;
                    $historico_digital_disciplinas->nome_disciplina   = $item_object->NomeDisciplina;
                    $historico_digital_disciplinas->ano               = $item_object->Ano;
                    $historico_digital_disciplinas->semestre          = $item_object->Sem;
                    $historico_digital_disciplinas->etapa             = $item_object->Etapa;
                    $historico_digital_disciplinas->frequencia        = $item_object->Freq;

                    // 5. Processamento de Professor e Titulação
                    $historico_digital_disciplinas->cod_professor     = $item_object->Codprofessor ?? $item_object->CodProf;
                    $nome_prof = $item_object->nome ?? $item_object->NomeProf;
                    $historico_digital_disciplinas->nome_professor    = mb_strtoupper($nome_prof);
                    
                    $titulacao_bruta = $item_object->HabilitacaoProf3 ?? $item_object->TituloProf;
                    $historico_digital_disciplinas->titulacao_professor = self::formatarTitulacao($titulacao_bruta);

                    // 6. Carga Horária e Nota
                    $ch_final = $item_object->CHParcial ?? $item_object->CH;
                    $historico_digital_disciplinas->carga_horaria     = str_replace(',', '.', $ch_final);
                    
                    $nota_formatada = str_replace(',', '.', $item_object->NotaFinal);
                    $historico_digital_disciplinas->nota              = number_format(floatval($nota_formatada), 2, '.', '');

                    // 7. Situação e Integralização (Regra FAFRAM baseada no código original)
                    if (in_array($item_object->Sit, ["APROVADO(A)", "APROV. ESTUDO"])) {
                        $historico_digital_disciplinas->situacao = "Aprovado";
                        $historico_digital_disciplinas->forma_integralizacao = ($item_object->Sit == "APROVADO(A)") ? "Cursado" : "Aproveitado";
                    } else {
                        $historico_digital_disciplinas->situacao = "Reprovado";
                        $historico_digital_disciplinas->forma_integralizacao = '';
                    }

                    $historico_digital_disciplinas->store(); 
                }
                
                TTransaction::close();
                TToast::show('success', 'Sincronização realizada com sucesso!', 'bottom right', 'fa-sync');
                
                // Atualiza o formulário principal
                AdiantiCoreApplication::loadPage('HistoricoAutomaticoComponentesForm', 'onReloadDisciplinas', ['id' => $dados_historico_digital->id]);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', 'Erro ao sincronizar histórico: ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    private static function formatarTitulacao($titulacao)
    {
        $map = [
            "TECNÓLOGO"      => 'Tecnólogo',
            "GRADUAÇÃO"      => 'Graduação',
            "ESPECIALIZAÇÃO" => 'Especialização',
            "MESTRADO"       => 'Mestrado',
            "DOUTORADO"      => 'Doutorado'
        ];
        return $map[$titulacao] ?? '';
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
