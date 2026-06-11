<?php

class HistoricoManualInformacoesForm extends TPage
{
    protected $form;     
    private $dados_diplomado_id;
    private $dados_curso_id;


    public function __construct( $param )
    {
        parent::__construct();        
        
        $unit_id = TSession::getValue('userunitid');
        
        try
        {
            //Filtrar cursos de acordo com a unidade no momento de logar
            TTransaction::open('Felabs_DB');        
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_emissora_id', 'IN', '(SELECT id FROM dados_emissora WHERE system_unit_id = ' . $unit_id . ')'));
            $criteria->setProperty('order', 'id');
    
            //Filtro para evitar pré-carregamento da seek currículo
            $criteria_curriculo = new TCriteria;
            $criteria_curriculo->add(new TFilter('dados_curso_id', '=', $param['dados_curso_id']));
            
            //Filtro para evitar pré-carregamento da check áreas
            $criteria_areas = new TCriteria;
            $criteria_areas->add(new TFilter('dados_curso_id', '=', $param['dados_curso_id']));
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } 
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_HistoricoManualInformacoes');
        $this->form->setFormTitle('<h4>Histórico Digital Manual</h4>');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $tipo_documento = new THidden('tipo_documento');
        $tipo_historico = new TEntry('tipo_historico');
        $historico_gerado = new TEntry('historico_gerado');
        $historico_genesi_id = new THidden('historico_genesi_id');
        $dados_versao_id = new THidden('dados_versao_id');
        $this->dados_diplomado_id = new TDBUniqueSearch('dados_diplomado_id', 'Felabs_DB', 'DiplomaDigitalDiplomado', 'id', 'nome');
        $cod_aluno = new THidden('cod_aluno');
        $this->dados_curso_id = new TDBCombo('dados_curso_id', 'Felabs_DB', 'DiplomaDigitalCurso', 'id', '({codigo_curso_sistema}) {nome_curso_diploma}', 'nome_curso_diploma', $criteria);
        $cod_curso = new THidden('cod_curso');
        $dados_polo_id = new THidden('dados_polo_id'); //Não é requisitado no XML do histórico versão 1.04.1, oculto por enquanto
        $dados_emissora_id = new THidden('dados_emissora_id');
        $data_ingresso = new TDate('data_ingresso');
        $forma_acesso = new TCombo('forma_acesso');
        $ch_total_curso = new TEntry('ch_total_curso');
        $situacao_enade1 = new TCombo('situacao_enade1');
        $situacao_enade1_condicao = new TCombo('situacao_enade1_condicao');
        $situacao_enade1_edicao = new TEntry('situacao_enade1_edicao');
        $situacao_enade1_opcao_motivo = new TCheckGroup('situacao_enade1_opcao_motivo');
        $situacao_enade1_motivo = new TCombo('situacao_enade1_motivo');
        $situacao_enade1_outro_motivo = new TEntry('situacao_enade1_outro_motivo');
        $situacao_enade2 = new TCombo('situacao_enade2');
        $situacao_enade2_condicao = new TCombo('situacao_enade2_condicao');
        $situacao_enade2_edicao = new TEntry('situacao_enade2_edicao');
        $situacao_enade2_opcao_motivo = new TCheckGroup('situacao_enade2_opcao_motivo');
        $situacao_enade2_motivo = new TCombo('situacao_enade2_motivo');
        $situacao_enade2_outro_motivo = new TEntry('situacao_enade2_outro_motivo');
        $data_expedicao_historico = new TDateTime('data_expedicao_historico');
        $data_conclusao_curso = new TDate('data_conclusao_curso');
        $data_colacao_grau = new TDate('data_colacao_grau');
        $data_expedicao_diploma = new TDate('data_expedicao_diploma');
        $status_xml = new THidden('status_xml');
        $status_assinatura_secretaria = new THidden('status_assinatura_secretaria');
        $data_exp_certificado_secretaria = new THidden('data_exp_certificado_secretaria');
        $status_assinatura_diretor = new THidden('status_assinatura_diretor');
        $data_exp_certificado_diretor = new THidden('data_exp_certificado_diretor');
        $status_assinatura_emissora = new THidden('status_assinatura_emissora');
        $data_exp_certificado_emissora = new THidden('data_exp_certificado_emissora');
        $codigo_validacao = new THidden('codigo_validacao');
        $url_historico = new THidden('url_historico');
        $qrcode = new THidden('qrcode');
        $caminho_qrcode = new THidden('caminho_qrcode');
        $arquivo = new THidden('arquivo');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $arquivo_pdf = new THidden('arquivo_pdf');
        $caminho_pdf = new THidden('caminho_pdf');
        $status_assinatura_pdf = new THidden('status_assinatura_pdf');
        $status_publicacao = new THidden('status_publicacao');
        $data_publicacao = new THidden('data_publicacao');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        $curriculo_id = new TDBSeekButton('curriculo_id', 'Felabs_DB', 'form_HistoricoManualInformacoes', 'CurriculoDigital', 'codigo_curriculo');            
        $curriculo_nome = new TEntry('codigo_curriculo'); // Componente auxiliar, não será salvo no banco de dados
        $areas_integralizadas_id = new TDBCheckGroup('areas_integralizadas_id', 'Felabs_DB', 'AreaFormacao', 'id', 'nome', 'nome', $criteria_areas);
        $informacoes_adicionais = new TText('informacoes_adicionais');
        $ano_processo_seletivo = new TEntry('ano_processo_seletivo');
        $mes_processo_seletivo = new TCombo('mes_processo_seletivo');
        

        $this->dados_curso_id->setChangeAction(new TAction(array($this, 'onRecarregaForm')));
        
        $exit_action = new TAction(array($this, 'onChCursoExitAction'));
        $curriculo_id->setExitAction($exit_action);


        //Mês do processo seletivo
        $combo_mes = [];
        $combo_mes['01'] = "01"; 
        $combo_mes['02'] = "02";
        $combo_mes['03'] = "03";
        $combo_mes['04'] = "04";
        $combo_mes['05'] = "05";
        $combo_mes['06'] = "06";
        $combo_mes['07'] = "07";
        $combo_mes['08'] = "08";
        $combo_mes['09'] = "09";
        $combo_mes['10'] = "10";
        $combo_mes['11'] = "11";
        $combo_mes['12'] = "12";
        
        $mes_processo_seletivo->addItems($combo_mes);
        

        //Forma de acesso (definidas pelo MEC)
        $combo_acesso = [];
        $combo_acesso['Avaliação Seriada'] = "Avaliação Seriada"; 
        $combo_acesso['Decisão judicial'] = "Decisão judicial";
        $combo_acesso['Egresso BI/LI'] = "Egresso BI/LI";
        $combo_acesso['Enem'] = "Enem";  
        $combo_acesso['PEC-G'] = "PEC-G";
        $combo_acesso['Seleção para Vagas de Programas Especiais'] = "Seleção para Vagas de Programas Especiais";
        $combo_acesso['Seleção para Vagas Remanescentes'] = "Seleção para Vagas Remanescentes";
        $combo_acesso['Seleção Simplificada'] = "Seleção Simplificada";
        $combo_acesso['Transferência Ex Officio'] = "Transferência Ex Officio";
        $combo_acesso['Vestibular'] = "Vestibular";
              
        $forma_acesso->addItems($combo_acesso);
        

        //Situação ENADE (definidas pelo MEC)
        $combo_situacao_enade = [];
        $combo_situacao_enade['Habilitado'] = "Habilitado";
        $combo_situacao_enade['NaoHabilitado'] = "Não habilitado";
        $combo_situacao_enade['Irregular'] = "Irregular";
        
        $situacao_enade1->addItems($combo_situacao_enade);
        $situacao_enade2->addItems($combo_situacao_enade);
        
        $situacao_enade1->setChangeAction(new TAction(array($this, 'onSituacaoEnade1Change')));
        $situacao_enade2->setChangeAction(new TAction(array($this, 'onSituacaoEnade2Change')));
        
        
        //Condição ENADE (definidas pelo MEC)
        $combo_condicao_enade = [];
        $combo_condicao_enade['Ingressante'] = "Ingressante";
        $combo_condicao_enade['Concluinte'] = "Concluinte";
        
        $situacao_enade1_condicao->addItems($combo_condicao_enade);
        $situacao_enade2_condicao->addItems($combo_condicao_enade);
        
        
        //Motivo Não Habilitado ENADE (definidas pelo MEC)
        $combo_motivo_enade = [];
        $combo_motivo_enade['Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo'] = "Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo";
        $combo_motivo_enade['Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso'] = "Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso";    
        
        $situacao_enade1_motivo->addItems($combo_motivo_enade);
        $situacao_enade2_motivo->addItems($combo_motivo_enade);
        
        $situacao_enade1_motivo->setChangeAction(new TAction(array($this, 'onMotivoEnade1Change')));
        $situacao_enade2_motivo->setChangeAction(new TAction(array($this, 'onMotivoEnade2Change')));
        
        
        //Opção motivo Não Habilitado ENADE 
        $check_motivo = [];
        $check_motivo['Utiliza motivo não listado pelo MEC'] = "Utilizar motivo não listado pelo MEC";

        $situacao_enade1_opcao_motivo->addItems($check_motivo);
        $situacao_enade2_opcao_motivo->addItems($check_motivo);
        
        $situacao_enade1_opcao_motivo->setChangeAction(new TAction(array($this, 'onOpcaoMotivoEnade1Change')));
        $situacao_enade2_opcao_motivo->setChangeAction(new TAction(array($this, 'onOpcaoMotivoEnade2Change')));
        

        // add the fields
        $label_explicacao = '<center><p style="color:red; font-size: 22px;"><b>Atenção</b></p>
                             <p style="font-size: 18px;">Este recurso deve ser utilizado <b>EXCLUSIVAMENTE</b> 
                             nos casos em que não seja possível gerar o histórico de um ex-aluno com base nas 
                             informações advindas do atual sistema acadêmico por se tratar de um registro antigo</p></center>';        
                               
        
        $panel = new TPanelGroup();
        $panel->add($label_explicacao);
        
        $this->form->addContent( [ $panel ] );

        $this->form->addContent( ['<br><h4>Informações Gerais</h4><hr>'] );
        
        $row = $this->form->addFields( [ new TLabel('Aluno'), $this->dados_diplomado_id ],
                                       [ new TLabel('Curso'), $this->dados_curso_id ],
                                       [ new TLabel('Histórico'), $historico_gerado ]);
        $row->layout = ['col-sm-5', 'col-sm-5', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Vincular histórico ao currículo correspondente (obrigatório em caso de 1ª via - parcial, transferência ou final)'), $curriculo_id ],
                                       [ new TLabel('CH total do curso'), $ch_total_curso ] );
        $row->layout = ['col-sm-10', 'col-sm-2'];
        
        $label_explicacao1 = 'Caso o histórico seja vinculado a um currículo, a carga horária total do curso será preenchida com base na soma dos elementos que compõem 
        os critérios de integralização e contribuem para a carga horária total. Caso não seja vinculado a um currículo, a carga horária total do curso deve ser preenchida 
        levando-se em consideração <b>todas as disciplinas que fazem parte da grade + horas mínimas obrigatórias de atividades complementares + horas mínimas obrigatórias de estágio</b>';
        
        $panel1 = new TPanelGroup();
        $panel1->add($label_explicacao1);
        
        $this->form->addContent( [ $panel1 ] );
        
        $row = $this->form->addFields( [ new TLabel('Data de ingresso'), $data_ingresso ],
                                       [ new TLabel('Forma de acesso'), $forma_acesso ],
                                       [ new TLabel('Mês do processo seletivo'), $mes_processo_seletivo ],
                                       [ new TLabel('Ano do processo seletivo'), $ano_processo_seletivo ], 
                                       [ new TLabel('Último histórico expedido'), $tipo_historico ] );
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

                
        $row = $this->form->addFields( [ new TLabel('Data última expedição histórico'), $data_expedicao_historico ],
                                       [ new TLabel('Data conclusão do curso'), $data_conclusao_curso ],
                                       [ new TLabel('Data colação de grau'), $data_colacao_grau ],
                                       [ new TLabel('Data última expedição diploma'), $data_expedicao_diploma ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Informações adicionais (não devem ser inseridas informações neste elemento caso esteja estruturada 
                                                     em outro campo)'), $informacoes_adicionais ] );
        $row->layout = ['col-sm-12'];


        $this->form->addContent( ['<br><br><h4>Informações Enade 1</h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Situação enade'), $situacao_enade1 ],
                                       [ new TLabel('Condição'), $situacao_enade1_condicao ],
                                       [ new TLabel('Edição'), $situacao_enade1_edicao ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Motivo'), $situacao_enade1_motivo ] );
        $row->layout = ['col-sm-12'];
        
        $this->form->addFields( [ new TLabel("<i>Conforme orientações do próprio MEC, <b>SE E SOMENTE SE</b> o motivo da NÃO HABILITAÇÃO do aluno ao
        Enade <b>NÃO</b> estiver listado no item anterior, selecione a opção 'Utilizar motivo não listado pelo MEC' e digite-o no campo abaixo</i>") ] );
        
        $row = $this->form->addFields( [ $situacao_enade1_opcao_motivo ],
                                       [ new TLabel('Outro'), $situacao_enade1_outro_motivo ] );
        $row->layout = ['col-sm-4', 'col-sm-8'];
        
        
        $this->form->addContent( ['<br><br><h4>Informações Enade 2</h4><hr>'] );
        
        $label_explicacao2 = '<center><p style="font-size: 16px;"><b>Preenchimento somente para os casos em que o aluno tenha prestado dois Enades no decorrer do curso</b></p></center>';        
                                    
        $panel2 = new TPanelGroup();
        $panel2->add($label_explicacao2);
        
        $this->form->addContent( [ $panel2 ] );

        $row = $this->form->addFields( [ new TLabel('Situação enade'), $situacao_enade2 ],
                                       [ new TLabel('Condição'), $situacao_enade2_condicao ],
                                       [ new TLabel('Edição'), $situacao_enade2_edicao ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Motivo'), $situacao_enade2_motivo ] );
        $row->layout = ['col-sm-12'];
        
        $this->form->addFields( [ new TLabel("<i>Conforme orientações do próprio MEC, <b>SE E SOMENTE SE</b> o motivo da NÃO HABILITAÇÃO do aluno ao
        Enade <b>NÃO</b> estiver listado no item anterior, selecione a opção 'Utilizar motivo não listado pelo MEC' e digite-o no campo abaixo</i>") ] );
        
        $row = $this->form->addFields( [ $situacao_enade2_opcao_motivo ],
                                       [ new TLabel('Outro'), $situacao_enade2_outro_motivo ] );
        $row->layout = ['col-sm-4', 'col-sm-8'];
        
        //Se o curso selecionado tiver áreas de formação, ao recarregar a página, o texto é exibido
        if ($areas_integralizadas_id->getLabels())
        {        
            $this->form->addContent( ['<br><br><h4>Áreas de Formação integralizadas pelo aluno</h4><hr>'] );
        }
        
        $this->form->addFields( [ $areas_integralizadas_id ] );
        
        
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_documento ] );
        $this->form->addFields( [ $historico_genesi_id ] );
        $this->form->addFields( [ $dados_versao_id ] );
        $this->form->addFields( [ $cod_aluno ] );    
        $this->form->addFields( [ $cod_curso ] );
        $this->form->addFields( [ $dados_polo_id ] );
        $this->form->addFields( [ $dados_emissora_id ] );                
        $this->form->addFields( [ $status_xml ] );
        $this->form->addFields( [ $status_assinatura_secretaria ] );
        $this->form->addFields( [ $data_exp_certificado_secretaria ] );
        $this->form->addFields( [ $status_assinatura_diretor ] );
        $this->form->addFields( [ $data_exp_certificado_diretor ] );
        $this->form->addFields( [ $status_assinatura_emissora ] );
        $this->form->addFields( [ $data_exp_certificado_emissora ] );
        $this->form->addFields( [ $codigo_validacao ] );
        $this->form->addFields( [ $url_historico ] );
        $this->form->addFields( [ $qrcode ] );
        $this->form->addFields( [ $caminho_qrcode ] );
        $this->form->addFields( [ $arquivo ] );
        $this->form->addFields( [ $caminho_arquivo ] );        
        $this->form->addFields( [ $arquivo_pdf ] );
        $this->form->addFields( [ $caminho_pdf ] );
        $this->form->addFields( [ $status_assinatura_pdf ] );        
        $this->form->addFields( [ $status_publicacao ] );
        $this->form->addFields( [ $data_publicacao ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );


        //Retirada a obrigatoriedade de preencher as datas, pois podem haver históricos antigos parciais
        $this->dados_diplomado_id->addValidation('Diplomado ID', new TRequiredValidator);
        $cod_aluno->addValidation('Cód. aluno', new TRequiredValidator);
        $this->dados_curso_id->addValidation('Curso ID', new TRequiredValidator);
        $cod_curso->addValidation('Cód. curso', new TRequiredValidator);    
        $dados_emissora_id->addValidation('Emissora ID', new TRequiredValidator);    
        $historico_gerado->addValidation('Histórico', new TRequiredValidator);
        $ch_total_curso->addValidation('CH total do curso', new TRequiredValidator);
        $data_ingresso->addValidation('Data de ingresso', new TRequiredValidator);
        $forma_acesso->addValidation('Forma de acesso', new TRequiredValidator);
        $tipo_documento->addValidation('Tipo de documento', new TRequiredValidator);    
        //$data_expedicao_historico->addValidation('Data última expedição histórico', new TRequiredValidator);
        //$data_conclusao_curso->addValidation('Data conclusão do curso', new TRequiredValidator); 
        //$data_colacao_grau->addValidation('Data colação de grau', new TRequiredValidator); 
        //$data_expedicao_diploma->addValidation('Data última expedição diploma', new TRequiredValidator);         
                
        
        // set sizes
        $tipo_documento->setValue('XMLHistorico');
        $tipo_historico->setEditable(FALSE);
        $this->dados_diplomado_id->setMask('({cod_aluno}) {nome}');
        $historico_gerado->setEditable(FALSE);
        $historico_gerado->setValue('Manual');
        $ch_total_curso->setMask('9!'); 
        $data_ingresso->setMask('dd/mm/yyyy');
        $data_ingresso->setDatabaseMask('yyyy-mm-dd');
        $ano_processo_seletivo->setMask('9999');
        $ano_processo_seletivo->placeholder = "Ex: 2023"; 
        $situacao_enade1_edicao->setMask('9999');
        $situacao_enade1_edicao->placeholder = "Ex: 2023"; 
        $situacao_enade2_edicao->setMask('9999');
        $situacao_enade2_edicao->placeholder = "Ex: 2023";  
        $data_expedicao_historico->setMask('dd/mm/yyyy hh:ii');
        $data_expedicao_historico->setDatabaseMask('yyyy/mm/dd hh:ii');
        $data_expedicao_historico->setEditable(FALSE);
        $data_conclusao_curso->setMask('dd/mm/yyyy');
        $data_conclusao_curso->setDatabaseMask('yyyy-mm-dd');
        $data_colacao_grau->setMask('dd/mm/yyyy');
        $data_colacao_grau->setDatabaseMask('yyyy-mm-dd');
        $data_expedicao_diploma->setMask('dd/mm/yyyy');
        $data_expedicao_diploma->setDatabaseMask('yyyy-mm-dd');
        $curriculo_id->setSize(130);
        $curriculo_id->setDisplayMask("Cód: {codigo_curriculo} - Curso: {diploma_digital_curso->nome_curso_diploma} - Grade: {cod_grade}");
        $curriculo_id->setDisplayLabel('Currículo');
        $curriculo_id->setAuxiliar($curriculo_nome);
        $curriculo_nome->setSize('calc(100% - 160px)');
        $curriculo_nome->style .= ';margin-left:10px';
        $curriculo_nome->setEditable(FALSE);
        $curriculo_id->setCriteria($criteria_curriculo);
        $areas_integralizadas_id->setLayout('horizontal');
            
            
        if ($areas_integralizadas_id->getLabels())
        {
            foreach ($areas_integralizadas_id->getLabels() as $label)
            {
                $label->setSize(200);
            }
        }
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('HistoricoManualList','onReload')), 'fas:arrow-alt-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onChCursoExitAction($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            if(!empty($param['curriculo_id']))
            {
                TEntry::disableField('form_HistoricoManualInformacoes', 'ch_total_curso');
                
                $curriculo_id = $param['curriculo_id'];

                //Nos critérios do currículo é definida a carga horária do curso
                $criterios = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo_id)
                                                            ->where('participacao_total', '=', 'Sim')
                                                            ->load();
    
                foreach($criterios as $criterio)
                {
                    //Ch mínima e máxima vão ser iguais no currículo
                    $ch_curso += $criterio->ch_minima_hora_relogio;
                }
    
                $obj = new StdClass;
                $obj->ch_total_curso = $ch_curso; 
                
                TForm::sendData('form_HistoricoManualInformacoes', $obj);
            }    
            else
            {
                TEntry::clearField('form_HistoricoManualInformacoes', 'ch_total_curso');
                TEntry::enableField('form_HistoricoManualInformacoes', 'ch_total_curso');
            }    
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public static function onRecarregaForm($param)
    {   
        unset($param['static']);   
        TApplication::postData('form_HistoricoManualInformacoes', 'HistoricoManualInformacoesForm', 'onExibeCampos');
    }
    
    
    public function onExibeCampos($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $data = $this->form->getData();
    
            //Preenche campos ocultos obrigatórios
            $diplomado = new DiplomaDigitalDiplomado($param['dados_diplomado_id']);  
            $data->cod_aluno = $diplomado->cod_aluno;
                    
            $curso = new DiplomaDigitalCurso($param['dados_curso_id']);    
            $data->cod_curso = $curso->codigo_curso_sistema;
            $data->dados_emissora_id = $curso->dados_emissora_id;
            
            
            //CH total do curso
            if($data->curriculo_id)
            {
                $param['curriculo_id'] = $data->curriculo_id;
                $this->onChCursoExitAction($param);
            }    
              
                    
            //Enade 1
            if($data->situacao_enade1 == "Habilitado" OR $data->situacao_enade1 == "Irregular" OR $data->situacao_enade1 == "")
            {    
                $param['situacao_enade1'] = $data->situacao_enade1;
                $this->onSituacaoEnade1Change($param); 
            }
            else
            {
                if($data->situacao_enade1_motivo)
                {
                    $param['situacao_enade1'] = $data->situacao_enade1;
                    $param['situacao_enade1_motivo'] = $data->situacao_enade1_motivo;
                    $this->onMotivoEnade1Change($param);                
                }
                        
                if($data->situacao_enade1_opcao_motivo[0] == "Utiliza motivo não listado pelo MEC")
                {
                    $param['situacao_enade1_opcao_motivo'] = $data->situacao_enade1_opcao_motivo;
                    $this->onOpcaoMotivoEnade1Change($param);                                       
                }
            }
                    
                    
            //Enade 2
            if($data->situacao_enade2 == "Habilitado" OR $data->situacao_enade2 == "Irregular" OR $data->situacao_enade2 == "")
            {    
                $param['situacao_enade2'] = $data->situacao_enade2;
                $this->onSituacaoEnade2Change($param);   
            }
            else
            {
                if($data->situacao_enade2_motivo)
                {
                    $param['situacao_enade2'] = $data->situacao_enade2;
                    $param['situacao_enade2_motivo'] = $data->situacao_enade2_motivo;
                    $this->onMotivoEnade2Change($param);                
                }
                        
                if($data->situacao_enade2_opcao_motivo[0] == "Utiliza motivo não listado pelo MEC")
                {
                    $param['situacao_enade2_opcao_motivo'] = $data->situacao_enade2_opcao_motivo;
                    $this->onOpcaoMotivoEnade2Change($param);                                       
                }
            }
                    
            $this->form->setData($data);
            
            TTransaction::close();
        } 
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }        
    }
    
     
    public static function onSituacaoEnade1Change($param)
    {
        $situacao_enade = $param['situacao_enade1'];

        if($situacao_enade == "NaoHabilitado")
        {
            //HABILITA
            TCombo::enableField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');
            TEntry::enableField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo'); 
            
            //RECARREGA            
            $opcoes_motivo = [];
            $opcoes_motivo['Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo'] = "Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo";
            $opcoes_motivo['Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso'] = "Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso";
            
            TCombo::reload('form_HistoricoManualInformacoes', 'situacao_enade1_motivo', $opcoes_motivo, TRUE);
        }
        elseif($situacao_enade == "Habilitado" OR $situacao_enade == "Irregular")
        {
            //LIMPA
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');
            TCheckGroup::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_opcao_motivo');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');

            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');  
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');     
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_condicao');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_edicao');
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');
            TCheckGroup::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_opcao_motivo');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');

            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo'); 
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Ingressante'] = "Ingressante";
            $opcoes['Concluinte'] = "Concluinte";
            
            TCombo::reload('form_HistoricoManualInformacoes', 'situacao_enade1_condicao', $opcoes, TRUE);
        }
    }
     

    public static function onMotivoEnade1Change($param)
    {
        $situacao_enade = $param['situacao_enade1'];
        $situacao_enade1_motivo = $param['situacao_enade1_motivo'];
        

        if($situacao_enade == "NaoHabilitado" AND $situacao_enade1_motivo)
        {
            //LIMPA
            TCheckGroup::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_opcao_motivo');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');
            
            //DESABILITA
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');   
        }
        elseif($situacao_enade == "NaoHabilitado" AND $situacao_enade1_motivo == NULL)
        {
            //HABILITA
            TEntry::enableField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');        
        }
        else
        {
            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo'); 
        }  
    }


    public static function onOpcaoMotivoEnade1Change($param)
    {        
        $opcao_motivo = $param['situacao_enade1_opcao_motivo'];        
        $check_opcao = implode('', $opcao_motivo);


        if($check_opcao == "Utiliza motivo não listado pelo MEC")
        {
            //LIMPA
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');
            
            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');    
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');
            
            //DESABILITA
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade1_outro_motivo');
            
            //HABILITA 
            TCombo::enableField('form_HistoricoManualInformacoes', 'situacao_enade1_motivo');
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo'] = "Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo";
            $opcoes['Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso'] = "Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso";    
                
            TCombo::reload('form_HistoricoManualInformacoes', 'situacao_enade1_motivo', $opcoes, TRUE);       
        }
    } 
    

    public static function onSituacaoEnade2Change($param)
    {
        $situacao_enade = $param['situacao_enade2'];

        if($situacao_enade == "NaoHabilitado")
        {
            //HABILITA
            TCombo::enableField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');
            TEntry::enableField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo'); 
            
            //RECARREGA            
            $opcoes_motivo = [];
            $opcoes_motivo['Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo'] = "Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo";
            $opcoes_motivo['Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso'] = "Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso";
            
            TCombo::reload('form_HistoricoManualInformacoes', 'situacao_enade2_motivo', $opcoes_motivo, TRUE);
        }
        elseif($situacao_enade == "Habilitado" OR $situacao_enade == "Irregular")
        {
            //LIMPA
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');
            TCheckGroup::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_opcao_motivo');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');

            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');  
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');     
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_condicao');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_edicao');
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');
            TCheckGroup::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_opcao_motivo');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');

            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo'); 
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Ingressante'] = "Ingressante";
            $opcoes['Concluinte'] = "Concluinte";
            
            TCombo::reload('form_HistoricoManualInformacoes', 'situacao_enade2_condicao', $opcoes, TRUE);
        }
    }  
    
    
    public static function onMotivoEnade2Change($param)
    {
        $situacao_enade = $param['situacao_enade2'];
        $situacao_enade2_motivo = $param['situacao_enade2_motivo'];
        

        if($situacao_enade == "NaoHabilitado" AND $situacao_enade2_motivo)
        {
            //LIMPA
            TCheckGroup::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_opcao_motivo');
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');
            
            //DESABILITA
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');   
        }
        elseif($situacao_enade == "NaoHabilitado" AND $situacao_enade2_motivo == NULL)
        {
            //HABILITA
            TEntry::enableField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');        
        }
        else
        {
            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo'); 
        }  
    }
    
    
    public static function onOpcaoMotivoEnade2Change($param)
    {
        $opcao_motivo = $param['situacao_enade2_opcao_motivo'];        
        $check_opcao = implode('', $opcao_motivo);
        
        if($check_opcao == "Utiliza motivo não listado pelo MEC")
        {
            //LIMPA
            TCombo::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');
            
            //DESABILITA
            TCombo::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');    
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');
            
            //DESABILITA
            TEntry::disableField('form_HistoricoManualInformacoes', 'situacao_enade2_outro_motivo');
            
            //HABILITA 
            TCombo::enableField('form_HistoricoManualInformacoes', 'situacao_enade2_motivo');
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo'] = "Estudante não habilitado ao Enade em razão do calendário do ciclo avaliativo";
            $opcoes['Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso'] = "Estudante não habilitado ao Enade em razão da natureza do projeto pedagógico do curso";    
                
            TCombo::reload('form_HistoricoManualInformacoes', 'situacao_enade2_motivo', $opcoes, TRUE);      
        }
    }   


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');    
                         
            $data = $this->form->getData();
            
            $object = new HistoricoDigital;
            $object->fromArray( (array) $data);
            
            $this->form->validate();


            //Verifica se o ano do processo seletivo contém 4 dígitos (caso tenha sido preenchido)
            if($object->ano_processo_seletivo)
            {
                $count = strlen($object->ano_processo_seletivo);
                
                if($count <> 4)
                {
                    throw new Exception("O ano do processo seletivo precisa ter 4 dígitos");
                }
            }
            

            //Verifica se o ano de edição do enade 1 contém 4 dígitos (caso tenha sido preenchido)
            if($object->situacao_enade1_edicao)
            {
                $count = strlen($object->situacao_enade1_edicao);
                
                if($count <> 4)
                {
                    throw new Exception("O ano de edição do enade precisa ter 4 dígitos");
                }
            }
            
            
            //Verifica se o ano de edição do enade 2 contém 4 dígitos (caso tenha sido preenchido)
            if($object->situacao_enade2_edicao)
            {
                $count = strlen($object->situacao_enade2_edicao);
                
                if($count <> 4)
                {
                    throw new Exception("O ano de edição do enade precisa ter 4 dígitos");
                }
            }
            

            //Caso o usuário preencha os campos do Enade 1 e 2, verifica se não se trata de registros iguais (mesmo ano ou condição)
            if ((($object->situacao_enade1_condicao <> NULL) AND ($object->situacao_enade2_condicao <> NULL) AND ($object->situacao_enade1_condicao == $object->situacao_enade2_condicao)) 
            OR (($object->situacao_enade1_edicao <> NULL) AND ($object->situacao_enade2_edicao <> NULL) AND ($object->situacao_enade1_edicao == $object->situacao_enade2_edicao)))
            {
                throw new Exception("Caso o aluno tenha prestado somente um Enade no decorrer do curso, preencha somente os campos relacionados ao Enade 1");  
            }
            

            //Controle campos condicionais - Situação Enade 1
            if(($object->situacao_enade1_condicao OR $object->situacao_enade1_edicao OR $object->situacao_enade1_opcao_motivo OR 
                $object->situacao_enade1_motivo OR $object->situacao_enade1_outro_motivo) AND (! $object->situacao_enade1))
            {
                throw new Exception("É necessário preencher a situação do aluno em relação ao enade");    
            }
            
            if($object->situacao_enade1 == "NaoHabilitado")
            {
                if((! $object->situacao_enade1_condicao) OR (! $object->situacao_enade1_edicao))
                {
                    throw new Exception("É necessário preencher a condição do aluno e o ano de edição do enade");
                }
                
                $check_opcao_motivo = implode('', $object->situacao_enade1_opcao_motivo);
            
                if($check_opcao_motivo == 'Utiliza motivo não listado pelo MEC')
                {   
                    $object->situacao_enade1_opcao_motivo = $check_opcao_motivo;
                    
                    if(! $object->situacao_enade1_outro_motivo)
                    {                   
                        throw new Exception("É necessário preencher o motivo pelo qual o(a) aluno(a) não está habilitado(a) ao Enade");
                    }    
                }
                else
                {
                    $object->situacao_enade1_opcao_motivo = "Utiliza motivo listado pelo MEC";
                    
                    if(! $object->situacao_enade1_motivo)
                    {
                        throw new Exception("É necessário selecionar o motivo pelo qual o(a) aluno(a) não está habilitado(a) ao enade");
                    }
                }
            }                     
                      
            elseif($object->situacao_enade1 == "Habilitado" OR $object->situacao_enade1 == "Irregular")
            {
                $object->situacao_enade1_motivo = '';
                $object->situacao_enade1_opcao_motivo = '';
                $object->situacao_enade1_outro_motivo = '';
                
                if((! $object->situacao_enade1_condicao) OR (! $object->situacao_enade1_edicao))
                {
                    throw new Exception("É necessário preencher a condição do aluno e o ano de edição do enade");
                }
            }
            
            else
            {
                $object->situacao_enade1 = '';   
                $object->situacao_enade1_condicao = '';
                $object->situacao_enade1_edicao = '';
                $object->situacao_enade1_motivo = '';
                $object->situacao_enade1_opcao_motivo = '';
                $object->situacao_enade1_outro_motivo = '';
            }
            
            
            //Controle campos condicionais - Situação Enade 2
            if(($object->situacao_enade2_condicao OR $object->situacao_enade2_edicao OR $object->situacao_enade2_opcao_motivo OR 
                $object->situacao_enade2_motivo OR $object->situacao_enade2_outro_motivo) AND (! $object->situacao_enade2))
            {
                throw new Exception("É necessário preencher a situação do aluno em relação ao enade");    
            }
                        
            if($object->situacao_enade2 == "NaoHabilitado")
            {
                if((! $object->situacao_enade2_condicao) OR (! $object->situacao_enade2_edicao))
                {
                    throw new Exception("É necessário preencher a condição do aluno e o ano de edição do enade");
                }
                
                $check_opcao_motivo = implode('', $object->situacao_enade2_opcao_motivo);
            
                if($check_opcao_motivo == 'Utiliza motivo não listado pelo MEC')
                {   
                    $object->situacao_enade2_opcao_motivo = $check_opcao_motivo;
                    
                    if(! $object->situacao_enade2_outro_motivo)
                    {                   
                        throw new Exception("É necessário preencher o motivo pelo qual o(a) aluno(a) não está habilitado(a) ao enade");
                    }    
                }
                else
                {
                    $object->situacao_enade2_opcao_motivo = "Utiliza motivo listado pelo MEC";
                    
                    if(! $object->situacao_enade2_motivo)
                    {
                        throw new Exception("É necessário selecionar o motivo pelo qual o(a) aluno(a) não está habilitado(a) ao enade");
                    }
                }
            }                    
                      
            elseif($object->situacao_enade2 == "Habilitado" OR $object->situacao_enade2 == "Irregular")
            {
                $object->situacao_enade2_motivo = '';
                $object->situacao_enade2_opcao_motivo = '';
                $object->situacao_enade2_outro_motivo = '';
                
                if((! $object->situacao_enade2_condicao) OR (! $object->situacao_enade2_edicao))
                {
                    throw new Exception("É necessário preencher a condição do aluno e o ano de edição do enade");
                }
            }
            
            else
            {
                $object->situacao_enade2 = '';   
                $object->situacao_enade2_condicao = '';
                $object->situacao_enade2_edicao = '';
                $object->situacao_enade2_motivo = '';
                $object->situacao_enade2_opcao_motivo = '';
                $object->situacao_enade2_outro_motivo = '';
            }
            
            
            //Verifica se o aluno realmente não tem histórico gerado no Genesi           
            $criteria = new TCriteria;
            $criteria->add(new TFilter('Codaluno', '=', $data->cod_aluno));
            $criteria->add(new TFilter('CodCurso', '=', $data->cod_curso));
            
            
            TTransaction::open('dados_fei');
            
            $aluno = FiHistorico::getObjects($criteria);

            TTransaction::close();
            
            if($aluno)
            {
                throw new Exception('Este aluno possui histórico no Genesi');
            }
            
            
            //Se está salvando um "novo registro", mas já existe histórico digital com mesmo aluno e curso
            if(empty($data->id))
            {
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('cod_aluno', '=', $data->cod_aluno)); 
                $criteria1->add(new TFilter('cod_curso', '=', $data->cod_curso));
                
                $criteria2 = new TCriteria;
                $criteria2->add(new TFilter('dados_diplomado_id', '=', $data->dados_diplomado_id)); 
                $criteria2->add(new TFilter('dados_curso_id', '=', $data->dados_curso_id));
                
                $criteria3 = new TCriteria;
                $criteria3->add($criteria1, TExpression::OR_OPERATOR);
                $criteria3->add($criteria2, TExpression::OR_OPERATOR);                 

                $repository = new TRepository('HistoricoDigital'); 
                $registros_bd = $repository->load($criteria3);
            
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro deste mesmo histórico");
                }
            }
            
            
            //Check áreas
            $object->areas_integralizadas_id = implode(',', $object->areas_integralizadas_id);


            if($object->status_xml == NULL)
            {
                $object->status_xml = 0; //0 - Não gerado / 1 - Gerado
            } 
            
            if($object->status_assinatura_secretaria == NULL)
            {
                $object->status_assinatura_secretaria = 0; //0 - Não preenchida / 1 - Preenchida
            }
            
            if($object->status_assinatura_diretor == NULL)
            {
                $object->status_assinatura_diretor = 0; //0 - Não preenchida / 1 - Preenchida
            }
            
            if($object->status_assinatura_emissora == NULL)
            {
                $object->status_assinatura_emissora = 0; //0 - Não preenchida / 1 - Preenchida
            }

            if($object->status_assinatura_pdf == NULL)
            {
                $object->status_assinatura_pdf = 0; //0 - Não preenchida / 1 - Preenchida
            }
            
            if($object->status_publicacao == NULL)
            {
                $object->status_publicacao = 0; //0 - Não publicado / 1 - Publicado
            }
            
            $object->tipo_documento = "XMLHistorico";
            $object->historico_gerado = "Manual";
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');

            $object->store();
            
            TTransaction::close();
                                   
            
            $data->id = $object->id;
            
            $this->form->setData($data);                        
            
            
            //Verifica se já existe registro em componentes curriculares. Se não existir, pergunta se quer adicionar
            TTransaction::open('Felabs_DB');
            
            $historico_disciplinas = HistoricoDigitalDisciplinas::where('historico_digital_id', '=', $object->id)->load();
            $historico_situacoes = HistoricoDigitalSituacaoDiscente::where('historico_digital_id', '=', $object->id)->load();
            
            if((! $historico_disciplinas) OR (! $historico_situacoes))
            {
                //Limpa variável para garantir integridade
                TSession::setValue('dados_historico_digital', NULL);
                TSession::setValue('dados_historico_digital', $object);
            
                $action_lancar = new TAction(['HistoricoManualComponentesForm', 'onShow']);                                 
                $action_voltar = new TAction(['HistoricoManualList', 'onReload']);
                   
                new TQuestion("Registro salvo. Deseja lançar os componentes curriculares do histórico?", $action_lancar, $action_voltar);
            }
            else
            {
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
                
                TApplication::loadPage('HistoricoManualList', 'onReload');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            
            $data = $this->form->getData();                                   
            $this->form->setData($data);  
            
            
            //Se estiver editando registro e cair na exceção, mantém campos bloqueados. Se estiver salvando novo registro, mantém desbloqueado
            if(!empty($param['id']))
            {
                $this->dados_diplomado_id->setEditable(FALSE);
                $this->dados_curso_id->setEditable(FALSE);
            }


            //CH total curso
            if($data->curriculo_id)
            {
                $this->onChCursoExitAction($param);
            }            
            
            //Enade 1
            if($data->situacao_enade1 == "Habilitado" OR $data->situacao_enade1 == "Irregular" OR $data->situacao_enade1 == "")
            {    
                $param['situacao_enade1'] = $data->situacao_enade1;
                $this->onSituacaoEnade1Change($param); 
            }
            else
            {
                if($data->situacao_enade1_motivo)
                {
                    $param['situacao_enade1'] = $data->situacao_enade1;
                    $param['situacao_enade1_motivo'] = $data->situacao_enade1_motivo;
                    $this->onMotivoEnade1Change($param);                
                }
                    
                if($data->situacao_enade1_opcao_motivo[0] == "Utiliza motivo não listado pelo MEC")
                {
                    $param['situacao_enade1_opcao_motivo'] = $data->situacao_enade1_opcao_motivo;
                    $this->onOpcaoMotivoEnade1Change($param);                                       
                }
            }
            
            //Enade 2
            if($data->situacao_enade2 == "Habilitado" OR $data->situacao_enade2 == "Irregular" OR $data->situacao_enade2 == "")
            {    
                $param['situacao_enade2'] = $data->situacao_enade2;
                $this->onSituacaoEnade2Change($param);   
            }
            else
            {
                if($data->situacao_enade2_motivo)
                {
                    $param['situacao_enade2'] = $data->situacao_enade2;
                    $param['situacao_enade2_motivo'] = $data->situacao_enade2_motivo;
                    $this->onMotivoEnade2Change($param);                
                }
                    
                if($data->situacao_enade2_opcao_motivo[0] == "Utiliza motivo não listado pelo MEC")
                {
                    $param['situacao_enade2_opcao_motivo'] = $data->situacao_enade2_opcao_motivo;
                    $this->onOpcaoMotivoEnade2Change($param);                                       
                }
            }
            
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
                
                $object = new HistoricoDigital($key);                

                $this->dados_diplomado_id->setEditable(FALSE);
                $this->dados_curso_id->setEditable(FALSE);
                                
                $object->data_expedicao_historico = TDateTime::convertToMask($object->data_expedicao_historico, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii');
                $object->situacao_enade1_opcao_motivo = explode(',', $object->situacao_enade1_opcao_motivo);
                $object->data_expedicao_historico = TDateTime::convertToMask($object->data_expedicao_historico, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii');
                $object->situacao_enade1_opcao_motivo = explode(',', $object->situacao_enade1_opcao_motivo);
                $object->situacao_enade2_opcao_motivo = explode(',', $object->situacao_enade2_opcao_motivo);
                $object->areas_integralizadas_id = explode(',', $object->areas_integralizadas_id);
        
                //CH total do curso
                if($object->curriculo_id)
                {
                    $param['curriculo_id'] = $object->curriculo_id;
                    $this->onChCursoExitAction($param);
                }
        
                //Enade 1
                if($object->situacao_enade1 == "Habilitado" OR $object->situacao_enade1 == "Irregular" OR $object->situacao_enade1 == "")
                {    
                    $param['situacao_enade1'] = $object->situacao_enade1;
                    $this->onSituacaoEnade1Change($param); 
                }
                else
                {
                    if($object->situacao_enade1_motivo)
                    {
                        $param['situacao_enade1'] = $object->situacao_enade1;
                        $param['situacao_enade1_motivo'] = $object->situacao_enade1_motivo;
                        $this->onMotivoEnade1Change($param);                
                    }
                    
                    if($object->situacao_enade1_opcao_motivo[0] == "Utiliza motivo não listado pelo MEC")
                    {
                        $param['situacao_enade1_opcao_motivo'] = $object->situacao_enade1_opcao_motivo;
                        $this->onOpcaoMotivoEnade1Change($param);                                       
                    }
                }
                
                
                //Enade 2
                if($object->situacao_enade2 == "Habilitado" OR $object->situacao_enade2 == "Irregular" OR $object->situacao_enade2 == "")
                {    
                    $param['situacao_enade2'] = $object->situacao_enade2;
                    $this->onSituacaoEnade2Change($param);   
                }
                else
                {
                    if($object->situacao_enade2_motivo)
                    {
                        $param['situacao_enade2'] = $object->situacao_enade2;
                        $param['situacao_enade2_motivo'] = $object->situacao_enade2_motivo;
                        $this->onMotivoEnade2Change($param);                
                    }
                    
                    if($object->situacao_enade2_opcao_motivo[0] == "Utiliza motivo não listado pelo MEC")
                    {
                        $param['situacao_enade2_opcao_motivo'] = $object->situacao_enade2_opcao_motivo;
                        $this->onOpcaoMotivoEnade2Change($param);                                       
                    }
                }                
                
                $this->form->setData($object);
                
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
                
                //Habilita/Desabilita campos dependentes
                $this->onSituacaoEnade1Change($param);
                $this->onMotivoEnade1Change($param);
                $this->onOpcaoMotivoEnade1Change($param);
                $this->onSituacaoEnade2Change($param);
                $this->onMotivoEnade2Change($param);
                $this->onOpcaoMotivoEnade2Change($param);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}

