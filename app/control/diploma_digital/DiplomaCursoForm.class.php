<?php

class DiplomaCursoForm extends TPage
{
    protected $form;   
    private $codigo_curso_sistema;
     

    public function __construct( $param )
    {
        parent::__construct();
        
        
        //$this->codigo_curso_sistema para conseguir bloquear campo na edição sem perder o valor ou se cair num Exception
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalCurso');
        $this->form->setFormTitle('<h4>Curso</h4>');
        $this->form->setFieldSizes('100%');

        
        $unit_id = TSession::getValue('userunitid');
        
        //Filtrar emissora de acordo com a unidade no momento de logar
        $criteria_emissora = new TCriteria;
        $criteria_emissora->add(new TFilter('system_unit_id', '=', $unit_id));

        //Filtrar cursos de acordo com a unidade no momento de logar
        $criteria_curso = new TCriteria;
        $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));


        // create the form fields
        $id = new THidden('id');
        $dados_emissora_id = new TDBCombo('dados_emissora_id', 'Felabs_DB', 'DiplomaDigitalEmissora', 'id', '{system_unit->name} - {nome}', 'nome', $criteria_emissora);
        $this->codigo_curso_sistema = new TDBSeekButton('codigo_curso_sistema', 'dados_fei', 'form_DiplomaDigitalCurso', 'FiCurso', 'Nome', 'codigo_curso_sistema', 'nome_curso_sistema', $criteria_curso);
        $nome_curso_sistema = new TEntry('nome_curso_sistema');
        $nome_curso_diploma = new TEntry('nome_curso_diploma');
        $opcao_codigo_emec = new TRadioGroup('opcao_codigo_emec');
        $codigo_curso_emec = new TEntry('codigo_curso_emec');
        $sem_codigo_emec_numero_processo = new TEntry('sem_codigo_emec_numero_processo');
        $sem_codigo_emec_tipo_processo = new TEntry('sem_codigo_emec_tipo_processo');
        $sem_codigo_emec_data_cadastro = new TDate('sem_codigo_emec_data_cadastro');
        $sem_codigo_emec_data_protocolo = new TDate('sem_codigo_emec_data_protocolo');
        $opcao_polo = new TRadioGroup('opcao_polo');
        $nome_habilitacao = new TEntry('nome_habilitacao');
        $data_habilitacao = new THidden('data_habilitacao');
        $enfase = new TEntry('enfase');
        $modalidade = new TCombo('modalidade');
        $opcao_titulo = new TCheckGroup('opcao_titulo');
        $titulo_conferido = new TCombo('titulo_conferido');
        $outro_titulo_conferido = new TEntry('outro_titulo_conferido');
        $grau_conferido = new TCombo('grau_conferido');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $codigo_municipio = new TSeekButton('codigo_municipio');
        $nome_municipio = new TEntry('nome_municipio');
        $uf = new TCombo('uf');
        $cep = new TEntry('cep');  
        $opcao_autorizacao_emec = new TRadioGroup('opcao_autorizacao_emec');      
        $autorizacao_tipo = new TCombo('autorizacao_tipo');
        $autorizacao_numero = new TEntry('autorizacao_numero');
        $autorizacao_data = new TDate('autorizacao_data');
        $autorizacao_veiculo_publicacao = new TEntry('autorizacao_veiculo_publicacao');
        $autorizacao_data_publicacao = new TDate('autorizacao_data_publicacao');
        $autorizacao_secao_publicacao = new TEntry('autorizacao_secao_publicacao');
        $autorizacao_pag_publicacao = new TEntry('autorizacao_pag_publicacao');
        $autorizacao_numero_DOU = new TEntry('autorizacao_numero_DOU');
        $autorizacao_numero_processo = new TEntry('autorizacao_numero_processo');
        $autorizacao_tipo_processo = new TEntry('autorizacao_tipo_processo');
        $autorizacao_data_cadastro = new TDate('autorizacao_data_cadastro');
        $autorizacao_data_protocolo = new TDate('autorizacao_data_protocolo');
        $opcao_reconhecimento_emec = new TRadioGroup('opcao_reconhecimento_emec');
        $reconhecimento_tipo = new TCombo('reconhecimento_tipo');
        $reconhecimento_numero = new TEntry('reconhecimento_numero');
        $reconhecimento_data = new TDate('reconhecimento_data');
        $reconhecimento_veiculo_publicacao = new TEntry('reconhecimento_veiculo_publicacao');
        $reconhecimento_data_publicacao = new TDate('reconhecimento_data_publicacao');
        $reconhecimento_secao_publicacao = new TEntry('reconhecimento_secao_publicacao');
        $reconhecimento_pag_publicacao = new TEntry('reconhecimento_pag_publicacao');
        $reconhecimento_numero_DOU = new TEntry('reconhecimento_numero_DOU');
        $reconhecimento_numero_processo = new TEntry('reconhecimento_numero_processo');
        $reconhecimento_tipo_processo = new TEntry('reconhecimento_tipo_processo');
        $reconhecimento_data_cadastro = new TDate('reconhecimento_data_cadastro');
        $reconhecimento_data_protocolo = new TDate('reconhecimento_data_protocolo');        
        $opcao_renovacao_emec = new TRadioGroup('opcao_renovacao_emec');
        $renovacao_reconhecimento_tipo = new TCombo('renovacao_reconhecimento_tipo');
        $renovacao_reconhecimento_numero = new TEntry('renovacao_reconhecimento_numero');
        $renovacao_reconhecimento_data = new TDate('renovacao_reconhecimento_data');
        $renovacao_reconhecimento_veic_publ = new TEntry('renovacao_reconhecimento_veic_publ');
        $renovacao_reconhecimento_data_publ = new TDate('renovacao_reconhecimento_data_publ');
        $renovacao_reconhecimento_secao_publ = new TEntry('renovacao_reconhecimento_secao_publ');
        $renovacao_reconhecimento_pag_publ = new TEntry('renovacao_reconhecimento_pag_publ');
        $renovacao_reconhecimento_numero_DOU = new TEntry('renovacao_reconhecimento_numero_DOU');
        $renovacao_reconhecimento_numero_processo = new TEntry('renovacao_reconhecimento_numero_processo');
        $renovacao_reconhecimento_tipo_processo = new TEntry('renovacao_reconhecimento_tipo_processo');
        $renovacao_reconhecimento_data_cadastro = new TDate('renovacao_reconhecimento_data_cadastro');
        $renovacao_reconhecimento_data_protocolo = new TDate('renovacao_reconhecimento_data_protocolo'); 
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');        
        $opcao_area = new TRadioGroup('opcao_area');
        $termo_referencia_area = new TEntry('termo_referencia_area');
        
            
        //Buscar dados do município
        $codigo_municipio->setAction(new TAction(array('BuscaCidadeCurso', 'onReload')));
        
        
        //Opção Área
        $radio_opcao_area = [];
        $radio_opcao_area['Curso possui formação por áreas'] = "Curso possui formação por áreas";
        $radio_opcao_area['Curso não possui formação por áreas'] = "Curso não possui formação por áreas";

        $opcao_area->addItems($radio_opcao_area);
        
        $opcao_area->setChangeAction(new TAction(array($this, 'onOpcaoAreaChange')));
         
         
        //Opção (Autorização, Reconhecimento e Renovação de Reconhecimento)
        $opcao_emec = [];
        $opcao_emec['Utilizar informações sobre ato regulatório'] = "Utilizar informações sobre ato regulatório";
        $opcao_emec['Utilizar informações sobre tramitação do processo'] = "Utilizar informações sobre tramitação do processo";
        
        $opcao_autorizacao_emec->addItems($opcao_emec);
        $opcao_reconhecimento_emec->addItems($opcao_emec);
        $opcao_renovacao_emec->addItems($opcao_emec);
        
        $opcao_autorizacao_emec->setChangeAction(new TAction(array($this, 'onOpcaoAutorizacaoChange')));
        $opcao_reconhecimento_emec->setChangeAction(new TAction(array($this, 'onOpcaoReconhecimentoChange')));
        $opcao_renovacao_emec->setChangeAction(new TAction(array($this, 'onOpcaoRenovacaoChange')));
                        

        //Modalidade
        $tipos_modalidade = [];
        $tipos_modalidade['EAD'] = "EAD";
        $tipos_modalidade['Presencial'] = "Presencial";

        $modalidade->addItems($tipos_modalidade);
        
        
        //Grau
        $tipos_grau = [];
        $tipos_grau['Bacharelado'] = "Bacharelado";
        $tipos_grau['Curso sequencial'] = "Curso sequencial";
        $tipos_grau['Licenciatura'] = "Licenciatura";
        $tipos_grau['Tecnólogo'] = "Tecnólogo";

        $grau_conferido->addItems($tipos_grau);
        
        
        //Título
        $tipos_titulo = [];
        $tipos_titulo['Bacharel'] = "Bacharel";
        $tipos_titulo['Licenciado'] = "Licenciado";
        $tipos_titulo['Médico'] = "Médico";
        $tipos_titulo['Tecnólogo'] = "Tecnólogo";

        $titulo_conferido->addItems($tipos_titulo);
        

        //Opção título 
        $check_opcao_titulo = [];
        $check_opcao_titulo['Utiliza título não listado pelo MEC'] = "Utilizar título não listado pelo MEC";

        $opcao_titulo->addItems($check_opcao_titulo);
        
        $opcao_titulo->setChangeAction(new TAction(array($this, 'onOpcaoTituloChange')));


        //Opção Polo
        $radio_opcao_polo = [];
        $radio_opcao_polo['Curso possui polo'] = "Curso possui polo";
        $radio_opcao_polo['Curso não possui polo'] = "Curso não possui polo";

        $opcao_polo->addItems($radio_opcao_polo);
        
        
        //Opção código EMEC
        $opcao_emec = [];
        $opcao_emec['Possui código EMEC'] = "Curso possui código E-MEC";
        $opcao_emec['Não possui código EMEC'] = "Curso não possui código E-MEC";

        $opcao_codigo_emec->addItems($opcao_emec);
        
        $opcao_codigo_emec->setChangeAction(new TAction(array($this, 'onOpcaoCodigoEmecChange')));
        
        
        //UF
        $combo_uf = [];
        $combo_uf['AC'] = "AC";
        $combo_uf['AL'] = "AL";
        $combo_uf['AM'] = "AM";
        $combo_uf['AP'] = "AP";
        $combo_uf['BA'] = "BA";
        $combo_uf['CE'] = "CE";
        $combo_uf['DF'] = "DF";
        $combo_uf['ES'] = "ES";
        $combo_uf['GO'] = "GO";
        $combo_uf['MA'] = "MA";
        $combo_uf['MG'] = "MG";
        $combo_uf['MS'] = "MS";
        $combo_uf['MT'] = "MT";
        $combo_uf['PA'] = "PA";
        $combo_uf['PB'] = "PB";
        $combo_uf['PE'] = "PE";
        $combo_uf['PI'] = "PI";
        $combo_uf['PR'] = "PR";
        $combo_uf['RJ'] = "RJ";
        $combo_uf['RN'] = "RN";
        $combo_uf['RO'] = "RO";
        $combo_uf['RR'] = "RR";
        $combo_uf['RS'] = "RS";
        $combo_uf['SC'] = "SC";
        $combo_uf['SE'] = "SE";
        $combo_uf['SP'] = "SP";
        $combo_uf['TO'] = "TO";
        
        $uf->addItems($combo_uf);
        
        
        //Tipo (Credenciamento, Reconhecimento e Renovação do Reconhecimento)
        $tipo = [];
        $tipo['Decreto'] = "Decreto";
        $tipo['Deliberação'] = "Deliberação";
        $tipo['Lei Estadual'] = "Lei Estadual";
        $tipo['Lei Federal'] = "Lei Federal";
        $tipo['Lei Municipal'] = "Lei Municipal";
        $tipo['Parecer'] = "Parecer";
        $tipo['Portaria'] = "Portaria";
        $tipo['Resolução'] = "Resolução";
        $tipo['Ato Próprio'] = "Ato Próprio";
        
        $autorizacao_tipo->addItems($tipo);
        $reconhecimento_tipo->addItems($tipo);
        $renovacao_reconhecimento_tipo->addItems($tipo);
        

        //INFORMAÇÕES GERAIS
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $data_habilitacao ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Emissora <font color="red">*</font>'), $dados_emissora_id ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('Código curso <font color="red">*</font>'), $this->codigo_curso_sistema ],
                                       [ new TLabel('Nome curso (Genesi) <font color="red">*</font>'), $nome_curso_sistema ],
                                       [ new TLabel('Nome curso (Diploma Digital) <font color="red">*</font>'), $nome_curso_diploma ] );
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-5'];

        $row = $this->form->addFields( [ new TLabel('Ênfase'), $enfase ] );
        $row->layout = ['col-sm-12'];  
        
        $row = $this->form->addFields( [ new TLabel('Habilitação'), $nome_habilitacao ],
                                       [ new TLabel('Modalidade <font color="red">*</font>'), $modalidade ],
                                       [ new TLabel('Grau <font color="red">*</font>'), $grau_conferido ],
                                       [ new TLabel('Título'), $titulo_conferido ] );
        $row->layout = ['col-sm-6', 'col-sm-2', 'col-sm-2', 'col-sm-2'];     
        
        $this->form->addFields( [ '<br>' ] );         
         
        $this->form->addFields( [ new TLabel("<i>Conforme orientações do próprio MEC, <b>SE E SOMENTE SE</b> o título conferido 
        pelo curso <b>NÃO</b> estiver listado no item anterior, selecione a opção 'Utilizar título não listado pelo MEC' e digite-o 
        no campo abaixo</i>") ] );
        
        $row = $this->form->addFields( [ $opcao_titulo ],
                                       [ $outro_titulo_conferido ] );
        $row->layout = ['col-sm-4', 'col-sm-8'];                       
        
        $this->form->addFields( [ '<br><br>' ] );
        
        
        //ÁREAS
        $label1 = new TLabel('Áreas', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );

        $row = $this->form->addFields( [ $opcao_area ],
                                       [ new TLabel('Termo usado pelo curso para referenciar o conceito de áreas'), $termo_referencia_area ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $this->form->addFields( [ '<br><br>' ] );
                

        //POLO
        $label2 = new TLabel('Polo', '#285097', 12, 'b', '<br>');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );

        $row = $this->form->addFields( [ $opcao_polo ] );
        $row->layout = ['col-sm-12'];        
        
        $this->form->addFields( [ '<br><br>' ] );
        
        
        //EMEC
        $label3 = new TLabel('E-MEC', '#285097', 12, 'b', '<br>');
        $label3->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label3] );
        
        $row = $this->form->addFields( [], [ $opcao_codigo_emec ], [] );
        $row->layout = ['col-sm-3', 'col-sm-6', 'col-sm-3'];        

        $row = $this->form->addFields( [ new TLabel('Código EMEC'), $codigo_curso_emec ],
                                       [ new TLabel('Número processo'), $sem_codigo_emec_numero_processo ],
                                       [ new TLabel('Tipo processo'), $sem_codigo_emec_tipo_processo ],
                                       [ new TLabel('Data cadastro'), $sem_codigo_emec_data_cadastro ],
                                       [ new TLabel('Data protocolo'), $sem_codigo_emec_data_protocolo ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-2'];
        
        
        $this->form->addFields( [ '<br><br>' ] );
            
        
        //ENDEREÇO
        $label4 = new TLabel('Endereço', '#285097', 12, 'b', '<br>');
        $label4->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label4] );
        
        $row = $this->form->addFields( [ new TLabel('Logradouro <font color="red">*</font>'), $logradouro ],
                                       [ new TLabel('Nº <font color="red">*</font>'), $numero ],
                                       [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Bairro <font color="red">*</font>'), $bairro ] );
        $row->layout = ['col-sm-6', 'col-sm-1', 'col-sm-2', 'col-sm-3' ];

        $row = $this->form->addFields( [ new TLabel('Código município <font color="red">*</font>'), $codigo_municipio ],
                                       [ new TLabel('Nome do município <font color="red">*</font>'), $nome_municipio ],
                                       [ new TLabel('UF <font color="red">*</font>'), $uf ],
                                       [ new TLabel('CEP <font color="red">*</font>'), $cep ] );
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-1', 'col-sm-3' ];     
    
        $this->form->addFields( [ '<br><br>' ] );
        
        
        $panel = new TPanelGroup();
        
        $label_explicacao = "<i>Prezado usuário,<br> 
        Os dados relacionados à Autorização, Reconhecimento e Renovação de Reconhecimento podem ser consultados
        no site do E-MEC <a style='color:#3c8dbc' href= 'https://emec.mec.gov.br/' target='_blank'> (clicando aqui)</a>.<br>
        Os campos relacionados à Renovação de Reconhecimento são considerados obrigatórios desde que conste determinado ato 
        regulatório ou informação sobre a tramitação do processo no sistema E-MEC</i>";

        $panel->add($label_explicacao);
        $this->form->addContent( [ $panel ] );

        $this->form->addFields( [ '<br>' ] );

        
        //AUTORIZAÇÃO
        $label5 = new TLabel('Autorização', '#285097', 12, 'b', '<br>');
        $label5->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label5] );
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ $opcao_autorizacao_emec ] );
        $row->layout = ['col-sm-12'];        
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Tipo'), $autorizacao_tipo ],
                                       [ new TLabel('Nº'), $autorizacao_numero ],
                                       [ new TLabel('Data'), $autorizacao_data ],
                                       [ new TLabel('Número DOU'), $autorizacao_numero_DOU ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Veículo de publicação'), $autorizacao_veiculo_publicacao ],
                                       [ new TLabel('Data de publicação'), $autorizacao_data_publicacao ],
                                       [ new TLabel('Seção de publicação'), $autorizacao_secao_publicacao ],
                                       [ new TLabel('Pág. de publicação'), $autorizacao_pag_publicacao ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
        
        $row = $this->form->addFields( [ new TLabel('Número processo'), $autorizacao_numero_processo ],
                                       [ new TLabel('Tipo processo'), $autorizacao_tipo_processo ],
                                       [ new TLabel('Data cadastro'), $autorizacao_data_cadastro ],
                                       [ new TLabel('Data protocolo'), $autorizacao_data_protocolo ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
        
        
        $this->form->addFields( [ '<br><br>' ] );
        
        
        //RECONHECIMENTO
        $label6 = new TLabel('Reconhecimento', '#285097', 12, 'b', '<br>');
        $label6->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label6] );
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ $opcao_reconhecimento_emec ] );
        $row->layout = ['col-sm-12'];        
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Tipo'), $reconhecimento_tipo ],
                                       [ new TLabel('Nº'), $reconhecimento_numero ],
                                       [ new TLabel('Data'), $reconhecimento_data ],
                                       [ new TLabel('Número DOU'), $reconhecimento_numero_DOU ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Veículo de publicação'), $reconhecimento_veiculo_publicacao ],
                                       [ new TLabel('Data de publicação'), $reconhecimento_data_publicacao ],
                                       [ new TLabel('Seção de publicação'), $reconhecimento_secao_publicacao ],
                                       [ new TLabel('Pág. de publicação'), $reconhecimento_pag_publicacao ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
        
        $row = $this->form->addFields( [ new TLabel('Número processo'), $reconhecimento_numero_processo ],
                                       [ new TLabel('Tipo processo'), $reconhecimento_tipo_processo ],
                                       [ new TLabel('Data cadastro'), $reconhecimento_data_cadastro ],
                                       [ new TLabel('Data protocolo'), $reconhecimento_data_protocolo ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];


        $this->form->addFields( [ '<br><br>' ] );
       
        
        //RENOVAÇÃO RECONHECIMENTO
        $label7 = new TLabel('Renovação de Reconhecimento', '#285097', 12, 'b', '<br>');
        $label7->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label7] );
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ $opcao_renovacao_emec ] );
        $row->layout = ['col-sm-12'];        
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Tipo'), $renovacao_reconhecimento_tipo ],
                                       [ new TLabel('Nº'), $renovacao_reconhecimento_numero ],
                                       [ new TLabel('Data'), $renovacao_reconhecimento_data ],
                                       [ new TLabel('Número DOU'), $renovacao_reconhecimento_numero_DOU ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Veículo de publicação'), $renovacao_reconhecimento_veic_publ ],
                                       [ new TLabel('Data de publicação'), $renovacao_reconhecimento_data_publ ],
                                       [ new TLabel('Seção de publicação'), $renovacao_reconhecimento_secao_publ ],
                                       [ new TLabel('Pág. de publicação'), $renovacao_reconhecimento_pag_publ ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ]; 
        
        $row = $this->form->addFields( [ new TLabel('Número processo'), $renovacao_reconhecimento_numero_processo ],
                                       [ new TLabel('Tipo processo'), $renovacao_reconhecimento_tipo_processo ],
                                       [ new TLabel('Data cadastro'), $renovacao_reconhecimento_data_cadastro ],
                                       [ new TLabel('Data protocolo'), $renovacao_reconhecimento_data_protocolo ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];       
        
        $this->form->addFields( [ '<br>' ] ); 
        $label8 = new TLabel('<font color="red">*</font> Campos obrigatórios (alguns campos são obrigatórios condicionais, ou seja, dependem da escolha do usuário no momento do preenchimento)', '', 10, 'i');
        $this->form->addContent( [$label8] );
                         

        $dados_emissora_id->addValidation('Emissora', new TRequiredValidator);
        $this->codigo_curso_sistema->addValidation('Código curso', new TRequiredValidator);
        $nome_curso_sistema->addValidation('Nome curso (Genesi)', new TRequiredValidator);
        $nome_curso_diploma->addValidation('Nome curso (Diploma Digital)', new TRequiredValidator);
        //$nome_habilitacao->addValidation('Habilitação', new TRequiredValidator);
        $modalidade->addValidation('Modalidade', new TRequiredValidator);
        $grau_conferido->addValidation('Grau', new TRequiredValidator);
        $opcao_area->addValidation('Curso possui/não possui formação por áreas', new TRequiredValidator);
        $opcao_polo->addValidation('Polo', new TRequiredValidator);
        $opcao_codigo_emec->addValidation('Curso possui/não possui código E-MEC', new TRequiredValidator);      
        $logradouro->addValidation('Logradouro', new TRequiredValidator);
        $numero->addValidation('Nº', new TRequiredValidator);
        $bairro->addValidation('Bairro', new TRequiredValidator);
        $codigo_municipio->addValidation('Código município', new TRequiredValidator);
        $nome_municipio->addValidation('Nome do município', new TRequiredValidator);
        $uf->addValidation('UF', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);
        $opcao_autorizacao_emec->addValidation('Autorização - utilizar informações sobre ato regulatório/tramitação do processo é obrigatório', new TRequiredValidator);
        $opcao_reconhecimento_emec->addValidation('Reconhecimento - utilizar informações sobre ato regulatório/tramitação do processo é obrigatório', new TRequiredValidator);
        
      
        // set sizes
        $nome_curso_sistema->setEditable(FALSE);
        $nome_curso_diploma->placeholder = "Ex: Engenharia Civil, Pedagogia, Medicina Veterinária";
        $nome_habilitacao->placeholder = "Bacharelado em Direito, Licenciatura em Pedagogia";
        $opcao_area->setLayout('horizontal');
        $opcao_area->setSize(260);
        $opcao_polo->setLayout('horizontal');
        $opcao_polo->setSize(260);
        $opcao_codigo_emec->setLayout('horizontal');
        $opcao_codigo_emec->setUseButton();
        $opcao_codigo_emec->setSize('100%');
        $codigo_curso_emec->setMask('9!');
        $sem_codigo_emec_numero_processo->setMask('9!');
        $sem_codigo_emec_data_cadastro->setMask('dd/mm/yyyy');
        $sem_codigo_emec_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $sem_codigo_emec_data_protocolo->setMask('dd/mm/yyyy');
        $sem_codigo_emec_data_protocolo->setDatabaseMask('yyyy-mm-dd');
        $logradouro->setMaxLength('60');
        $numero->setMaxLength('60');
        $complemento->setMaxLength('60');
        $bairro->setMaxLength('60');
        $codigo_municipio->setMask('9999999');
        $nome_municipio->setMaxLength('255');
        $nome_municipio->setEditable(FALSE);
        $uf->setEditable(FALSE);
        $cep->setMask('99999999');
        $cep->setEditable(FALSE);
        $opcao_autorizacao_emec->setLayout('horizontal');
        $opcao_autorizacao_emec->setUseButton();
        $opcao_autorizacao_emec->setSize('100%');
        $autorizacao_numero->setMask('9!');
        $autorizacao_data->setMask('dd/mm/yyyy');
        $autorizacao_data->setDatabaseMask('yyyy-mm-dd');
        $autorizacao_numero_DOU->setMask('9!');
        $autorizacao_veiculo_publicacao->setMask('S!');
        $autorizacao_veiculo_publicacao->forceUpperCase();
        $autorizacao_data_publicacao->setMask('dd/mm/yyyy');
        $autorizacao_data_publicacao->setDatabaseMask('yyyy-mm-dd');
        $autorizacao_secao_publicacao->setMask('9!');
        $autorizacao_pag_publicacao->setMask('9!');
        $autorizacao_numero_processo->setMask('9!');
        $autorizacao_data_cadastro->setMask('dd/mm/yyyy');
        $autorizacao_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $autorizacao_data_protocolo->setMask('dd/mm/yyyy');
        $autorizacao_data_protocolo->setDatabaseMask('yyyy-mm-dd');
        $opcao_reconhecimento_emec->setLayout('horizontal');
        $opcao_reconhecimento_emec->setUseButton();
        $opcao_reconhecimento_emec->setSize('100%');
        $reconhecimento_numero->setMask('9!');
        $reconhecimento_data->setMask('dd/mm/yyyy');
        $reconhecimento_data->setDatabaseMask('yyyy-mm-dd');
        $reconhecimento_numero_DOU->setMask('9!');
        $reconhecimento_veiculo_publicacao->setMask('S!');        
        $reconhecimento_veiculo_publicacao->forceUpperCase();
        $reconhecimento_data_publicacao->setMask('dd/mm/yyyy');
        $reconhecimento_data_publicacao->setDatabaseMask('yyyy-mm-dd');
        $reconhecimento_secao_publicacao->setMask('9!');
        $reconhecimento_pag_publicacao->setMask('9!');
        $reconhecimento_numero_processo->setMask('9!');
        $reconhecimento_data_cadastro->setMask('dd/mm/yyyy');
        $reconhecimento_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $reconhecimento_data_protocolo->setMask('dd/mm/yyyy');
        $reconhecimento_data_protocolo->setDatabaseMask('yyyy-mm-dd');
        $opcao_renovacao_emec->setLayout('horizontal');
        $opcao_renovacao_emec->setUseButton();
        $opcao_renovacao_emec->setSize('100%');
        $renovacao_reconhecimento_numero->setMask('9!');
        $renovacao_reconhecimento_data->setMask('dd/mm/yyyy');
        $renovacao_reconhecimento_data->setDatabaseMask('yyyy-mm-dd');
        $renovacao_reconhecimento_numero_DOU->setMask('9!');
        $renovacao_reconhecimento_veic_publ->setMask('S!');
        $renovacao_reconhecimento_veic_publ->forceUpperCase();
        $renovacao_reconhecimento_data_publ->setMask('dd/mm/yyyy');
        $renovacao_reconhecimento_data_publ->setDatabaseMask('yyyy-mm-dd');
        $renovacao_reconhecimento_secao_publ->setMask('9!');
        $renovacao_reconhecimento_pag_publ->setMask('9!');
        $renovacao_reconhecimento_numero_processo->setMask('9!');
        $renovacao_reconhecimento_data_cadastro->setMask('dd/mm/yyyy');
        $renovacao_reconhecimento_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $renovacao_reconhecimento_data_protocolo->setMask('dd/mm/yyyy');
        $renovacao_reconhecimento_data_protocolo->setDatabaseMask('yyyy-mm-dd');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Voltar', new TAction(array('DiplomaCursoList','onReload')), 'fas:arrow-alt-circle-left blue');

        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    
    //Se selecionou a opção utilizar título não listado pelo MEC
    public static function onOpcaoTituloChange($param)
    {
        $opcao_titulo = $param['opcao_titulo'] ?? [];

        $check_opcao_titulo = implode('', $opcao_titulo);

        if($check_opcao_titulo == 'Utiliza título não listado pelo MEC')
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalCurso', 'titulo_conferido');
                            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalCurso', 'outro_titulo_conferido');         
        }
        else
        {
            //LIMPA
            TCheckGroup::clearField('form_DiplomaDigitalCurso', 'opcao_titulo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'outro_titulo_conferido');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'outro_titulo_conferido');
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Bacharel'] = "Bacharel";
            $opcoes['Licenciado'] = "Licenciado";
            $opcoes['Médico'] = "Médico";
            $opcoes['Tecnólogo'] = "Tecnólogo";
            
            TCombo::reload('form_DiplomaDigitalCurso', 'titulo_conferido', $opcoes, TRUE);
        }                  
    }
        
    
    public static function onOpcaoAreaChange($param)
    {
        $opcao_area = $param['opcao_area'] ?? [];      
        
        if($opcao_area == "Curso possui formação por áreas")
        {
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalCurso', 'termo_referencia_area');      
        }
        elseif($opcao_area == "Curso não possui formação por áreas")
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'termo_referencia_area');

            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'termo_referencia_area');
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'termo_referencia_area');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'termo_referencia_area');  
        }
    }
    
    
    public static function onOpcaoCodigoEmecChange($param)
    {
        $opcao_codigo = $param['opcao_codigo_emec'] ?? [];      
        
        if($opcao_codigo == "Possui código EMEC")
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_tipo_processo'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_cadastro'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_numero_processo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_tipo_processo');
            TDate::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_protocolo');
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalCurso', 'codigo_curso_emec');      
        }
        elseif($opcao_codigo == "Não possui código EMEC")
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'codigo_curso_emec');

            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'codigo_curso_emec');
            
            //HABILITA
            TEntry::enableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_numero_processo');
            TEntry::enableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_tipo_processo');
            TDate::enableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_cadastro');
            TDate::enableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_protocolo');
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'codigo_curso_emec');
            TEntry::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_tipo_processo'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_cadastro'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'codigo_curso_emec');
            TEntry::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_numero_processo'); 
            TEntry::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_tipo_processo'); 
            TDate::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'sem_codigo_emec_data_protocolo');   
        }
    }
    
    
    public static function onOpcaoAutorizacaoChange($param)
    {
        $opcao_autorizacao_emec = $param['opcao_autorizacao_emec'] ?? [];
        
        if($opcao_autorizacao_emec == 'Utilizar informações sobre ato regulatório')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_tipo_processo');
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data_cadastro');
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_numero_processo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_tipo_processo');
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data_protocolo');
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalCurso', 'autorizacao_tipo');
            TEntry::enableField('form_DiplomaDigitalCurso', 'autorizacao_numero'); 
            TDate::enableField('form_DiplomaDigitalCurso', 'autorizacao_data'); 
            TEntry::enableField('form_DiplomaDigitalCurso', 'autorizacao_veiculo_publicacao');
            TDate::enableField('form_DiplomaDigitalCurso', 'autorizacao_data_publicacao');
            TEntry::enableField('form_DiplomaDigitalCurso', 'autorizacao_secao_publicacao');
            TEntry::enableField('form_DiplomaDigitalCurso', 'autorizacao_pag_publicacao');
            TEntry::enableField('form_DiplomaDigitalCurso', 'autorizacao_numero_DOU'); 
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Ato Próprio'] = "Ato Próprio";
            $opcoes['Decreto'] = "Decreto";
            $opcoes['Deliberação'] = "Deliberação";
            $opcoes['Lei Estadual'] = "Lei Estadual";
            $opcoes['Lei Federal'] = "Lei Federal";
            $opcoes['Lei Municipal'] = "Lei Municipal";
            $opcoes['Parecer'] = "Parecer";
            $opcoes['Portaria'] = "Portaria";
            $opcoes['Resolução'] = "Resolução";
            
            TCombo::reload('form_DiplomaDigitalCurso', 'autorizacao_tipo', $opcoes, TRUE);
        }
        elseif($opcao_autorizacao_emec == 'Utilizar informações sobre tramitação do processo')
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalCurso', 'autorizacao_tipo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_numero'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data'); 
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_numero_DOU');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalCurso', 'autorizacao_tipo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_numero'); 
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data'); 
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_numero_DOU');
            
            //HABILITA 
            TEntry::enableField('form_DiplomaDigitalCurso', 'autorizacao_numero_processo');
            TEntry::enableField('form_DiplomaDigitalCurso', 'autorizacao_tipo_processo');
            TDate::enableField('form_DiplomaDigitalCurso', 'autorizacao_data_cadastro');
            TDate::enableField('form_DiplomaDigitalCurso', 'autorizacao_data_protocolo');
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalCurso', 'autorizacao_tipo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_numero'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data'); 
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_numero_DOU');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'autorizacao_tipo_processo');
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data_cadastro');
            TDate::clearField('form_DiplomaDigitalCurso', 'autorizacao_data_protocolo');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalCurso', 'autorizacao_tipo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_numero'); 
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data'); 
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_numero_DOU');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_numero_processo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'autorizacao_tipo_processo');
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'autorizacao_data_protocolo');
        }
    }
    
    
    public static function onOpcaoReconhecimentoChange($param)
    {
        $opcao_reconhecimento_emec = $param['opcao_reconhecimento_emec'] ?? [];
        
        if($opcao_reconhecimento_emec == 'Utilizar informações sobre ato regulatório')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data_protocolo');
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalCurso', 'reconhecimento_tipo');
            TEntry::enableField('form_DiplomaDigitalCurso', 'reconhecimento_numero'); 
            TDate::enableField('form_DiplomaDigitalCurso', 'reconhecimento_data'); 
            TEntry::enableField('form_DiplomaDigitalCurso', 'reconhecimento_veiculo_publicacao');
            TDate::enableField('form_DiplomaDigitalCurso', 'reconhecimento_data_publicacao');
            TEntry::enableField('form_DiplomaDigitalCurso', 'reconhecimento_secao_publicacao');
            TEntry::enableField('form_DiplomaDigitalCurso', 'reconhecimento_pag_publicacao');
            TEntry::enableField('form_DiplomaDigitalCurso', 'reconhecimento_numero_DOU'); 
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Ato Próprio'] = "Ato Próprio";
            $opcoes['Decreto'] = "Decreto";
            $opcoes['Deliberação'] = "Deliberação";
            $opcoes['Lei Estadual'] = "Lei Estadual";
            $opcoes['Lei Federal'] = "Lei Federal";
            $opcoes['Lei Municipal'] = "Lei Municipal";
            $opcoes['Parecer'] = "Parecer";
            $opcoes['Portaria'] = "Portaria";
            $opcoes['Resolução'] = "Resolução";
            
            TCombo::reload('form_DiplomaDigitalCurso', 'reconhecimento_tipo', $opcoes, TRUE);
        }
        elseif($opcao_reconhecimento_emec == 'Utilizar informações sobre tramitação do processo')
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalCurso', 'reconhecimento_tipo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_numero'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data'); 
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_numero_DOU');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalCurso', 'reconhecimento_tipo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_numero'); 
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data'); 
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_numero_DOU');
            
            //HABILITA 
            TEntry::enableField('form_DiplomaDigitalCurso', 'reconhecimento_numero_processo');
            TEntry::enableField('form_DiplomaDigitalCurso', 'reconhecimento_tipo_processo');
            TDate::enableField('form_DiplomaDigitalCurso', 'reconhecimento_data_cadastro');
            TDate::enableField('form_DiplomaDigitalCurso', 'reconhecimento_data_protocolo');
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalCurso', 'reconhecimento_tipo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_numero'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data'); 
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_numero_DOU');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'reconhecimento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalCurso', 'reconhecimento_data_protocolo');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalCurso', 'reconhecimento_tipo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_numero'); 
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data'); 
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_numero_DOU');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'reconhecimento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'reconhecimento_data_protocolo');
        }
    }
    
    
    public static function onOpcaoRenovacaoChange($param)
    {
        $opcao_renovacao_emec = $param['opcao_renovacao_emec'] ?? [];
        
        if($opcao_renovacao_emec == 'Utilizar informações sobre ato regulatório')
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_protocolo');
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo');
            TEntry::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero'); 
            TDate::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data'); 
            TEntry::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_veic_publ');
            TDate::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_publ');
            TEntry::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_secao_publ');
            TEntry::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_pag_publ');
            TEntry::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_DOU'); 
            
            //RECARREGA
            $opcoes = [];
            $opcoes['Ato Próprio'] = "Ato Próprio";
            $opcoes['Decreto'] = "Decreto";
            $opcoes['Deliberação'] = "Deliberação";
            $opcoes['Lei Estadual'] = "Lei Estadual";
            $opcoes['Lei Federal'] = "Lei Federal";
            $opcoes['Lei Municipal'] = "Lei Municipal";
            $opcoes['Parecer'] = "Parecer";
            $opcoes['Portaria'] = "Portaria";
            $opcoes['Resolução'] = "Resolução";
            
            TCombo::reload('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo', $opcoes, TRUE);
        }
        elseif($opcao_renovacao_emec == 'Utilizar informações sobre tramitação do processo')
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data'); 
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_veic_publ');
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_publ');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_secao_publ');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_pag_publ');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_DOU');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero'); 
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data'); 
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_veic_publ');
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_publ');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_secao_publ');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_pag_publ');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_DOU');
            
            //HABILITA 
            TEntry::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_processo');
            TEntry::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo_processo');
            TDate::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_cadastro');
            TDate::enableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_protocolo');
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero'); 
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data'); 
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_veic_publ');
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_publ');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_secao_publ');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_pag_publ');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_DOU');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_protocolo');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero'); 
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data'); 
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_veic_publ');
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_publ');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_secao_publ');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_pag_publ');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_DOU');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalCurso', 'renovacao_reconhecimento_data_protocolo');
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
                      
            $data = $this->form->getData();
            
            $object = new DiplomaDigitalCurso;
            $object->fromArray( (array) $data);
            
            $this->form->validate();
            
            
            //Se está salvando um "novo registro", mas já existe registro deste mesmo curso
            if(empty($data->id))
            {
                $registros_bd = DiplomaDigitalCurso::where('codigo_curso_sistema', '=', $data->codigo_curso_sistema)->load();
                
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro deste mesmo curso");
                }
            }
            
            
            //Controle campos condicionais - Áreas
            if($object->opcao_area == "Curso possui formação por áreas")
            {
                if(! $object->termo_referencia_area)
                {                   
                    throw new Exception("É necessário preencher o termo usado pelo curso para referenciar o conceito de áreas");
                }
            }
            
            if($data->id)
            {
                if((AreaFormacao::where('dados_curso_id', '=', $object->id)->count() > 0) AND ($object->opcao_area <> "Curso possui formação por áreas"))
                {
                    throw new Exception("Há registro(s) de Áreas de formação vinculadas ao curso. Por favor, verifique a opção selecionada em 'Áreas'");    
                }
            }
            
            
            //Controle campos condicionais - Título
            $check_opcao_titulo = implode('', $object->opcao_titulo);
            
            if($check_opcao_titulo == 'Utiliza título não listado pelo MEC')
            {   
                $object->titulo_conferido = '';
                $object->opcao_titulo = $check_opcao_titulo;
                                
                if(! $object->outro_titulo_conferido)
                {                   
                    throw new Exception("É necessário preencher o título conferido pelo curso");
                }    
            }
            else
            {
                $object->outro_titulo_conferido = '';
                $object->opcao_titulo = "Utiliza título listado pelo MEC";
                
                if(! $object->titulo_conferido)
                {
                    throw new Exception("É necessário selecionar o título conferido pelo curso");
                }
            }            
            
            
            //Controle campos condicionais - Código EMEC
            if($object->opcao_codigo_emec == 'Não possui código EMEC')
            {
                if((! $object->sem_codigo_emec_numero_processo) OR (! $object->sem_codigo_emec_tipo_processo) OR (! $object->sem_codigo_emec_data_cadastro) OR (! $object->sem_codigo_emec_data_protocolo))
                {                                
                    throw new Exception("É necessário preencher todos os dados relacionados ao EMEC");
                }
            }
            else
            {
                if(! $object->codigo_curso_emec)
                {
                    throw new Exception("É necessário preencher o código EMEC");
                }
            }
            
            
            //Controle de campos condicionais - Autorização
            if($object->opcao_autorizacao_emec == "Utilizar informações sobre ato regulatório")
            {
                if (! $object->autorizacao_tipo)
                {
                    throw new Exception("O campo Autorização - Tipo é obrigatório");
                }
                if (! $object->autorizacao_numero)
                {
                    throw new Exception("O campo Autorização - Nº é obrigatório");
                }
                if (! $object->autorizacao_data)
                {
                    throw new Exception("O campo Autorização - Data é obrigatório");
                }
                if (! $object->autorizacao_veiculo_publicacao)
                {
                    throw new Exception("O campo Autorização - Veículo de publicação é obrigatório");
                }
                if (! $object->autorizacao_data_publicacao)
                {
                    throw new Exception("O campo Autorização - Data de publicação é obrigatório");
                }
                if (! $object->autorizacao_secao_publicacao)
                {
                    throw new Exception("O campo Autorização - Seção de publicação é obrigatório");
                }
                if (! $object->autorizacao_pag_publicacao)
                {
                    throw new Exception("O campo Autorização - Página de publicação é obrigatório");
                } 
                if (! $object->autorizacao_numero_DOU)
                {
                    throw new Exception("O campo Autorização - Número DOU é obrigatório");
                }   
            }
            
            if($object->opcao_autorizacao_emec == "Utilizar informações sobre tramitação do processo")
            {
                if (! $object->autorizacao_numero_processo)
                {
                    throw new Exception("O campo Autorização - Nº do processo é obrigatório");
                }
                if (! $object->autorizacao_tipo_processo)
                {
                    throw new Exception("O campo Autorização - Tipo de processo é obrigatório");
                }
                if (! $object->autorizacao_data_cadastro)
                {
                    throw new Exception("O campo Autorização - Data do cadastro é obrigatório");
                }
                if (! $object->autorizacao_data_protocolo)
                {
                    throw new Exception("O campo Autorização - Data do protocolo é obrigatório");
                }
            }  
            
            
            //Controle de campos condicionais - Reconhecimento
            if($object->opcao_reconhecimento_emec == "Utilizar informações sobre ato regulatório")
            {
                if (! $object->reconhecimento_tipo)
                {
                    throw new Exception("O campo Reconhecimento - Tipo é obrigatório");
                }
                if (! $object->reconhecimento_numero)
                {
                    throw new Exception("O campo Reconhecimento - Nº é obrigatório");
                }
                if (! $object->reconhecimento_data)
                {
                    throw new Exception("O campo Reconhecimento - Data é obrigatório");
                }
                if (! $object->reconhecimento_veiculo_publicacao)
                {
                    throw new Exception("O campo Reconhecimento - Veículo de publicação é obrigatório");
                }
                if (! $object->reconhecimento_data_publicacao)
                {
                    throw new Exception("O campo Reconhecimento - Data de publicação é obrigatório");
                }
                if (! $object->reconhecimento_secao_publicacao)
                {
                    throw new Exception("O campo Reconhecimento - Seção de publicação é obrigatório");
                }
                if (! $object->reconhecimento_pag_publicacao)
                {
                    throw new Exception("O campo Reconhecimento - Página de publicação é obrigatório");
                }
                if (! $object->reconhecimento_numero_DOU)
                {
                    throw new Exception("O campo Reconhecimento - Número DOU é obrigatório");
                }                 
            }
            
            if($object->opcao_reconhecimento_emec == "Utilizar informações sobre tramitação do processo")
            {
                if (! $object->reconhecimento_numero_processo)
                {
                    throw new Exception("O campo Reconhecimento - Nº do processo é obrigatório");
                }
                if (! $object->reconhecimento_tipo_processo)
                {
                    throw new Exception("O campo Reconhecimento - Tipo de processo é obrigatório");
                }
                if (! $object->reconhecimento_data_cadastro)
                {
                    throw new Exception("O campo Reconhecimento - Data do cadastro é obrigatório");
                }
                if (! $object->reconhecimento_data_protocolo)
                {
                    throw new Exception("O campo Reconhecimento - Data do protocolo é obrigatório");
                }
            }            
            
            
            //Controle de campos condicionais - Renovação de Reconhecimento
            if((($object->renovacao_reconhecimento_tipo) OR ($object->renovacao_reconhecimento_numero) OR 
               ($object->renovacao_reconhecimento_data) OR ($object->renovacao_reconhecimento_veic_publ) OR
               ($object->renovacao_reconhecimento_data_publ) OR ($object->renovacao_reconhecimento_secao_publ) OR 
               ($object->renovacao_reconhecimento_pag_publ) OR ($object->renovacao_reconhecimento_numero_DOU) OR 
               ($object->renovacao_reconhecimento_numero_processo) OR ($object->renovacao_reconhecimento_tipo_processo) OR 
               ($object->renovacao_reconhecimento_data_cadastro) OR ($object->renovacao_reconhecimento_data_protocolo)) AND 
               (! $object->opcao_renovacao_emec))
            {
                throw new Exception("O campo Renovação de Reconhecimento - Utilizar informações sobre ato regulatório/tramitação do processo é obrigatório");  
            }
            
            
            //Faz o inverso, verifica cada campo obrigatório de Renovação de Reconhecimento de acordo com a opção marcada
            if($object->opcao_renovacao_emec == "Utilizar informações sobre ato regulatório")
            {
                if (! $object->renovacao_reconhecimento_tipo)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Tipo é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_numero)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Nº é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_data)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Data é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_veic_publ)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Veículo de publicação é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_data_publ)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Data de publicação é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_secao_publ)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Seção de publicação é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_pag_publ)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Página de publicação é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_numero_DOU)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Número DOU é obrigatório");
                }                
            }
            
            if($object->opcao_renovacao_emec == "Utilizar informações sobre tramitação do processo")
            {
                if (! $object->renovacao_reconhecimento_numero_processo)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Nº do processo é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_tipo_processo)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Tipo de processo é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_data_cadastro)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Data do cadastro é obrigatório");
                }
                if (! $object->renovacao_reconhecimento_data_protocolo)
                {
                    throw new Exception("O campo Renovação de Reconhecimento - Data do protocolo é obrigatório");
                }
            }
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store();
            

            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('DiplomaCursoList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            
            //Se estiver editando registro e cair na exceção, mantém campo bloqueado. Se estiver salvando novo registro, mantém desbloqueado
            if(!empty($param['id']))
            {
                $this->codigo_curso_sistema->setEditable(FALSE);
            }            

            $param['opcao_titulo'] = $object->opcao_titulo;
            $this->onOpcaoTituloChange($param);
            
            $param['opcao_area'] = $object->opcao_area;
            $this->onOpcaoAreaChange($param);
            
            $param['opcao_codigo_emec'] = $object->opcao_codigo_emec;
            $this->onOpcaoCodigoEmecChange($param);
    
            $param['opcao_autorizacao_emec'] = $object->opcao_autorizacao_emec;
            $this->onOpcaoAutorizacaoChange($param);
            
            $param['opcao_reconhecimento_emec'] = $object->opcao_reconhecimento_emec;
            $this->onOpcaoReconhecimentoChange($param);
            
            $param['opcao_renovacao_emec'] = $object->opcao_renovacao_emec;
            $this->onOpcaoRenovacaoChange($param); 
            
            $this->form->setData( $this->form->getData() );
        
            $obj = new StdClass;
            $obj->titulo_conferido = $object->titulo_conferido;
            $obj->autorizacao_tipo = $object->autorizacao_tipo;
            $obj->reconhecimento_tipo = $object->reconhecimento_tipo;
            $obj->renovacao_reconhecimento_tipo = $object->renovacao_reconhecimento_tipo;
            
            TForm::sendData('form_DiplomaDigitalCurso', $obj); 
            
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
                
                $object = new DiplomaDigitalCurso($key);
                
                $this->codigo_curso_sistema->setEditable(FALSE);                
                
                $object->opcao_titulo = explode(',', $object->opcao_titulo);
                
                $check_opcao_titulo = implode('', $object->opcao_titulo);

                if($check_opcao_titulo == 'Utiliza título não listado pelo MEC')
                {
                    //LIMPA
                    TCombo::clearField('form_DiplomaDigitalCurso', 'titulo_conferido');                        
            
                    //HABILITA
                    TEntry::enableField('form_DiplomaDigitalCurso', 'outro_titulo_conferido');
                }
                else
                {
                    //LIMPA
                    TCheckGroup::clearField('form_DiplomaDigitalCurso', 'opcao_titulo');
                    TEntry::clearField('form_DiplomaDigitalCurso', 'outro_titulo_conferido');
                    
                    //DESABILITA
                    TEntry::disableField('form_DiplomaDigitalCurso', 'outro_titulo_conferido');
                }
      
                $param['opcao_titulo'] = $object->opcao_titulo;
                $this->onOpcaoTituloChange($param);
                
                $param['opcao_area'] = $object->opcao_area;
                $this->onOpcaoAreaChange($param);
                
                $param['opcao_codigo_emec'] = $object->opcao_codigo_emec;
                $this->onOpcaoCodigoEmecChange($param);
        
                $param['opcao_autorizacao_emec'] = $object->opcao_autorizacao_emec;
                $this->onOpcaoAutorizacaoChange($param);
                
                $param['opcao_reconhecimento_emec'] = $object->opcao_reconhecimento_emec;
                $this->onOpcaoReconhecimentoChange($param);
                
                $param['opcao_renovacao_emec'] = $object->opcao_renovacao_emec;
                $this->onOpcaoRenovacaoChange($param);
              
                $this->form->setData($object);
                
                $obj = new StdClass;
                $obj->titulo_conferido = $object->titulo_conferido;
                $obj->autorizacao_tipo = $object->autorizacao_tipo;
                $obj->reconhecimento_tipo = $object->reconhecimento_tipo;
                $obj->renovacao_reconhecimento_tipo = $object->renovacao_reconhecimento_tipo;
                
                TForm::sendData('form_DiplomaDigitalCurso', $obj);
            
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
                
                $this->onOpcaoTituloChange($param);
                $this->onOpcaoAreaChange($param);
                $this->onOpcaoCodigoEmecChange($param);
                $this->onOpcaoAutorizacaoChange($param);
                $this->onOpcaoReconhecimentoChange($param);
                $this->onOpcaoRenovacaoChange($param);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
