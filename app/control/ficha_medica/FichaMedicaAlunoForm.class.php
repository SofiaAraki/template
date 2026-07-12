<?php

class FichaMedicaAlunoForm extends TPage
{
    protected $form;
    protected $product_list;
    

    public function __construct( $param )
    {
        parent::__construct();

        if(TSession::getValue('userunitid') <> 12)
        {
            new TMessage('error','Funcionalidade não disponível para esta Unidade');
            die;
        }
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle(_t('Bootstrap form grid'));
////////////////////////////////////////////////////////CADASTRO FICHA MÉDICA   
        // creates the form
        $this->form = new BootstrapFormBuilder('form_FichaMedica');
        $this->form->setFormTitle('Ficha Médica');
        $this->form->setFieldSizes('100%');
        $this->form->appendPage('Cadastro Ficha Médica');

        // create the form fields
        $id = new THidden('id');
        //$cod_aluno = new TDBSeekButton('cod_aluno', 'dados_fei', 'form_FichaMedica', 'FiAluno', 'Nome', 'Codaluno', 'nome');
        $cod_aluno = new TDBSeekButton('cod_aluno', 'dados_fei', 'form_FichaMedica', 'FiAluno', 'Nome');
        $nome = new TEntry('nome');
        $rg = new TEntry('rg', 'dados_fei', 'form_FichaMedica', 'FiAluno', 'Rg');
        $cpf = new TEntry('cpf');
        $data_nasc = new TDate('data_nasc');
        //$local_nasc = new TEntry('local_nasc');
        $endereco = new TEntry('endereco');
        $cidade = new TEntry('cidade', 'dados_fei', 'form_FichaMedica', 'FiAluno', 'CodCidade');
        $cep = new TEntry('cep');
        $bairro = new TEntry('bairro');
        $responsavel = new TEntry('responsavel');
        $aluno_mora = new TRadioGroup('aluno_mora');
        $telefone = new TEntry('telefone');
        $tipo_sang = new TRadioGroup('tipo_sang');
        $alergico_s_n = new TRadioGroup('alergico_s_n');
        $alergico = new TEntry('alergico');
        $medicamento = new TRadioGroup('medicamento');
        $alergico_alimento_s_n = new TRadioGroup('alergico_alimento_s_n');
        $alergico_alimento = new TEntry('alergico_alimento');
        $nome_pai = new TEntry('nome_pai');
        $empresa_pai = new TEntry('empresa_pai');
        $telefone_pai = new TEntry('telefone_pai');
        $nome_mae = new TEntry('nome_mae');
        $empresa_mae = new TEntry('empresa_mae');
        $telefone_mae = new TEntry('telefone_mae');
        $nome_outros = new TEntry('nome_outros');
        $empresa_outros = new TEntry('empresa_outros');
        $telefone_outros = new TEntry('telefone_outros');
        $plano_de_saude_s_n = new TRadioGroup('plano_de_saude_s_n');
        $plano_de_saude = new TEntry('plano_de_saude');
        $alergico_medicamento_s_n = new TRadioGroup('alergico_medicamento_s_n');
        $alergico_medicamento = new TEntry('alergico_medicamento');
        $medico_aluno = new TRadioGroup('medico_aluno');
        $nome_medico = new TEntry('nome_medico');
        $endereco_medico = new TEntry('endereco_medico');
        $telefone_medico = new TEntry('telefone_medico');
        $observacao_febre = new TText('observacao_febre');
        $doenca_congenita_s_n = new TRadioGroup('doenca_congenita_s_n');
        $doenca_congenita = new TEntry('doenca_congenita');
        $hipertensao_s_n = new TRadioGroup('hipertensao_s_n');
        $doencas_contraidas_infancia = new TCheckGroup('doencas_contraidas_infancia');
        $epiletico_s_n = new TRadioGroup('epiletico_s_n');
        $epiletico_tratamento_s_n = new TEntry('epiletico_tratamento_s_n');
        $hemofilico_s_n = new TRadioGroup('hemofilico_s_n');
        $deficiente_visual_s_n = new TRadioGroup('deficiente_visual_s_n');
        $deficiente_fisico_s_n = new TRadioGroup('deficiente_fisico_s_n');
        $deficiente_auditivo_s_n = new TRadioGroup('deficiente_auditivo_s_n');
        $deficiente_intelectual_s_n = new TRadioGroup('deficiente_intelectual_s_n');
        $tea_s_n = new TRadioGroup('tea_s_n');
        $diabetico_s_n = new TRadioGroup('diabetico_s_n');
        $diabetico_insulina = new TRadioGroup('diabetico_insulina');
        $asmatico_s_n = new TRadioGroup('asmatico_s_n');
        $tratamento_medico_s_n = new TRadioGroup('tratamento_medico_s_n');
        $tratamento_medico = new TEntry('tratamento_medico');
        $necessidade_s_n = new TRadioGroup('necessidade_s_n');
        $necessidade = new TEntry('necessidade');
        $dificuldades_s_n = new TRadioGroup('dificuldades_s_n');
        $dificuldades = new TEntry('dificuldades');
        $ingere_medicamentos_s_n = new TRadioGroup('ingere_medicamentos_s_n');
        $ingere_medicamentos = new TEntry('ingere_medicamentos');
        $aluno_hospital = new TText('aluno_hospital');
        $transtorno_s_n = new TRadioGroup('transtorno_s_n');
        $transtorno = new TEntry('transtorno');
        $acp_psicologico_s_n = new TRadioGroup('acp_psicologico_s_n');
        $acp_psicologico = new TEntry('acp_psicologico');
        $termo = new TCheckGroup('termo');


        $cod_aluno->setAuxiliar($nome);

        $opcao_resp = array(); 
        $opcao_resp['Sim'] = 'Sim &nbsp'; 
        $opcao_resp['Nao'] = 'Não &nbsp';
        
        $alergico_s_n->addItems($opcao_resp);
        $medicamento->addItems($opcao_resp);
        $alergico_alimento_s_n->addItems($opcao_resp);
        $plano_de_saude_s_n->addItems($opcao_resp);
        $alergico_medicamento_s_n->addItems($opcao_resp);
        $doenca_congenita_s_n->addItems($opcao_resp);
        $hipertensao_s_n->addItems($opcao_resp);
        $epiletico_s_n->addItems($opcao_resp);
        $hemofilico_s_n->addItems($opcao_resp);
        $deficiente_visual_s_n->addItems($opcao_resp);
        $deficiente_fisico_s_n->addItems($opcao_resp);
        $deficiente_auditivo_s_n->addItems($opcao_resp);
        $deficiente_intelectual_s_n->addItems($opcao_resp);
        $tea_s_n->addItems($opcao_resp);
        $diabetico_s_n->addItems($opcao_resp);
        $diabetico_insulina->addItems($opcao_resp);
        $asmatico_s_n->addItems($opcao_resp);
        $tratamento_medico_s_n->addItems($opcao_resp);
        $necessidade_s_n->addItems($opcao_resp);
        $dificuldades_s_n->addItems($opcao_resp);
        $ingere_medicamentos_s_n->addItems($opcao_resp);
        $transtorno_s_n->addItems($opcao_resp);
        $acp_psicologico_s_n->addItems($opcao_resp);
        
        $opcao_tipo_sangue = array();
        $opcao_tipo_sangue['A+'] = 'A+ &nbsp';
        $opcao_tipo_sangue['A-'] = 'A- &nbsp';
        $opcao_tipo_sangue['B+'] = 'B+ &nbsp';
        $opcao_tipo_sangue['B-'] = 'B- &nbsp';
        $opcao_tipo_sangue['AB+'] = 'AB+ &nbsp';
        $opcao_tipo_sangue['AB-'] = 'AB- &nbsp';
        $opcao_tipo_sangue['O+'] = 'O+ &nbsp';
        $opcao_tipo_sangue['O-'] = 'O- &nbsp';
        
        $tipo_sang->addItems($opcao_tipo_sangue);
        
        $moradia = array(); 
        $moradia['Pais'] = 'Pais &nbsp'; 
        $moradia['Pai'] = 'Pai &nbsp';
        $moradia['Mãe'] = 'Mãe &nbsp';
        $moradia['Outros'] = 'Outros &nbsp';
        
        $aluno_mora->addItems($moradia);
        
        $doencas_contraidas = array();
        $doencas_contraidas['caxumba'] = 'caxumba';
        $doencas_contraidas['sarampo'] = 'sarampo';
        $doencas_contraidas['rubéola'] = 'rubéola';
        $doencas_contraidas['catapora'] = 'catapora';
        $doencas_contraidas['escarlatina'] = 'escarlatina';
        $doencas_contraidas['coqueluche'] = 'coqueluche';
        $doencas_contraidas['nenhuma'] = 'nenhuma';
        
        $doencas_contraidas_infancia->addItems($doencas_contraidas);
        
        $cpf->setMask('999.999.999-99');
        $data_nasc->setMask('dd/mm/yyyy');
        $data_nasc->setDatabaseMask('yyyy-mm-dd');
        $telefone->setMask('(99)99999-9999');
        $telefone_pai->setMask('(99)99999-9999');
        $telefone_mae->setMask('(99)99999-9999');
        $telefone_outros->setMask('(99)99999-9999');
        $telefone_medico->setMask('(99)99999-9999');
        
        $tipo_sang->setLayout('horizontal');
        $tipo_sang->addItems($opcao_tipo_sangue);
        
        $opcao_medico = array(); 
        $opcao_medico['alopata'] = 'alopata &nbsp'; 
        $opcao_medico['homeopata'] = 'homeopata';
        
        $medico_aluno->addItems($opcao_medico);
        
        $alergico_s_n->setLayout('horizontal');
        $alergico_alimento_s_n->setLayout('horizontal');
        $plano_de_saude_s_n->setLayout('horizontal');
        $alergico_medicamento_s_n->setLayout('horizontal');
        $doenca_congenita_s_n->setLayout('horizontal');
        $hipertensao_s_n->setLayout('horizontal');
        $epiletico_s_n->setLayout('horizontal');
        $hemofilico_s_n->setLayout('horizontal');
        $deficiente_visual_s_n->setLayout('horizontal');
        $deficiente_fisico_s_n->setLayout('horizontal');
        $deficiente_auditivo_s_n->setLayout('horizontal');
        $deficiente_intelectual_s_n->setLayout('horizontal');
        $tea_s_n->setLayout('horizontal');
        $diabetico_s_n->setLayout('horizontal');
        $diabetico_insulina->setLayout('horizontal');
        $asmatico_s_n->setLayout('horizontal');
        $tratamento_medico_s_n->setLayout('horizontal');
        $necessidade_s_n->setLayout('horizontal');
        $dificuldades_s_n->setLayout('horizontal');
        $ingere_medicamentos_s_n->setLayout('horizontal');
        $medico_aluno->setLayout('horizontal');
        $medicamento->setLayout('horizontal');
        $transtorno_s_n->setLayout('horizontal');
        $acp_psicologico_s_n->setLayout('horizontal');
        $alergico_alimento_s_n->addItems($opcao_resp);
        $plano_de_saude_s_n->addItems($opcao_resp);
        $alergico_medicamento_s_n->addItems($opcao_resp);
        $doenca_congenita_s_n->addItems($opcao_resp);
        $hipertensao_s_n->addItems($opcao_resp);
        $epiletico_s_n->addItems($opcao_resp);
        $hemofilico_s_n->addItems($opcao_resp);
        $deficiente_visual_s_n->addItems($opcao_resp);
        $deficiente_fisico_s_n->addItems($opcao_resp);
        $deficiente_auditivo_s_n->addItems($opcao_resp);
        $deficiente_intelectual_s_n->addItems($opcao_resp);
        $tea_s_n->addItems($opcao_resp);
        $diabetico_s_n->addItems($opcao_resp);
        $diabetico_insulina->addItems($opcao_resp);
        $asmatico_s_n->addItems($opcao_resp);
        $tratamento_medico_s_n->addItems($opcao_resp);
        $necessidade_s_n->addItems($opcao_resp);
        $dificuldades_s_n->addItems($opcao_resp);
        $ingere_medicamentos_s_n->addItems($opcao_resp);
        $medico_aluno->addItems($opcao_resp);
        $medicamento->addItems($opcao_resp);
        $transtorno_s_n->addItems($opcao_resp);
        $acp_psicologico_s_n->addItems($opcao_resp);
        
        $doencas_contraidas_infancia->setLayout('horizontal');
        $doencas_contraidas_infancia->addItems($doencas_contraidas);
        
        $medico_aluno->setLayout('horizontal');
        $medico_aluno->addItems($opcao_medico);
        
        $aluno_mora->setLayout('horizontal');
        $aluno_mora->addItems($moradia);

        //$cod_aluno->setSize(100);
        //$cod_aluno->setAuxiliar($nome);        
        //$nome->setSize('100%');
        
        $exit_action = new TAction(array($this, 'onExitAction'));
        $cod_aluno->setExitAction($exit_action);


        $label_aluno = new TLabel('Aluno(a):');
        

        // add the fields
        $this->form->addFields( [$id]);
        //$this->form->addFields( [$label_aluno], [$cod_aluno] );
        
        $this->form->addFields( [new TLabel('*Código do aluno:')],  [$cod_aluno]);
        $this->form->addFields( [new TLabel('*Aluno(a):')],  [$nome]);
        $this->form->addFields( [ new TLabel('RG:')], [$rg ],[ new TLabel('*CPF:')], [$cpf ] );
        $this->form->addFields( [ new TLabel('*Data de nascimento:')], [$data_nasc ], [ new TLabel('*Bairro:')], [$bairro ] /*[ new TLabel('*Local de nascimento:')], [$local_nasc ]*/ );
        $this->form->addFields( [ new TLabel('*Endereço:')], [$endereco ]);
        $this->form->addFields( [ new TLabel('*Cidade:')], [$cidade ],[ new TLabel('*CEP:')], [$cep ]  );
        $this->form->addFields( [ new TLabel('*Responsável pelo aluno(a):')], [$responsavel ] );
        $this->form->addFields( [ new TLabel('*Com quem mora o(a) aluno(a)?')],[$aluno_mora ] );
        $this->form->addFields( [ new TLabel('*Telefone(s) / Comercial / Residencial / Celular/WhatsApp:')], [$telefone ] );
        $this->form->addFields( [ new TLabel('*<b>Tipo Sanguíneo?</b>', '#ff0000')], [$tipo_sang ] );
        $this->form->addFields( [ new TLabel('*O(a) aluno(a) é alérgico(a)? (Sim/Não)')], [$alergico_s_n ] );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [$alergico ] );
        //$this->form->addFields( [ new TLabel('<b>O(a) aluno(a) é alérgico a algum medicamento tópico, oral ou injetável?</b>','#ff0000'), $medicamento ] );
        $this->form->addFields( [ new TLabel('<b>*O(a) aluno(a) tem alergia a algum tipo de alimento?</b>','#ff0000')], [$alergico_alimento_s_n ] );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [$alergico_alimento ] );
        $this->form->addFields( [ new TLabel('Nome do pai:')], [$nome_pai ] );
        $this->form->addFields( [ new TLabel('Empresa em que o pai trabalha:')],[$empresa_pai ],[ new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Pai):')], [$telefone_pai ] );
        $this->form->addFields(  );
        $this->form->addFields( [ new TLabel('Nome da mãe:')], [$nome_mae ] );
        $this->form->addFields( [ new TLabel('Empresa em que a mãe trabalha:')], [$empresa_mae ], [ new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Mãe):')],[$telefone_mae ] );
        $this->form->addFields( );
        $this->form->addFields( [ new TLabel('Nome de outros:')], [$nome_outros ] );
        $this->form->addFields( [ new TLabel('Empresa em que outros trabalha:')], [$empresa_outros ],[ new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Outros):')], [$telefone_outros ] );
        $this->form->addFields(  );
        $this->form->addFields( [ new TLabel('*1 - O(a) aluno(a) possui plano de saúde?')], [$plano_de_saude_s_n ] );
        $this->form->addFields( [ new TLabel('Nº do Cartão Nacional de Saúde (SUS)')], [$plano_de_saude ]);
        $this->form->addFields( [ new TLabel('<b>*2 - O(a) aluno(a) é alérgico a algum medicamento tópico, oral ou injetável?</b>','#ff0000')], [$alergico_medicamento_s_n ] );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [$alergico_medicamento ]);
        $this->form->addFields( [ new TLabel('3 - O médico do(a) aluno(a) é:')], [$medico_aluno ] );
        $this->form->addFields( [ new TLabel('4 - Nome do médico:')], [$nome_medico ] );
        $this->form->addFields( [ new TLabel('Endereço do médico:')], [$endereco_medico ] );
        $this->form->addFields( [ new TLabel('Telefones para contato do médico (inclusive celular):')], [$telefone_medico ]);
        $this->form->addFields( [ new TLabel('5 - Em caso de febre alta, não sendo localizado os pais ou responsáveis pelo(a) aluno(a) com qual medicamento ele deverá ser medicado e a quantidade, por indicação médica:')],[$observacao_febre ] );
        //$this->form->addFields( [ new TLabel('6 - A criança tem doença congênita? (Sim/Não)'), $doenca_congenita_s_n ] );
        //$this->form->addFields( [ new TLabel('Sim. Qual?'), $doenca_congenita ] );
        $this->form->addFields( [ new TLabel('*6 - Tem hipertensão? (Sim/Não)')], [$hipertensao_s_n ] );
        //$this->form->addFields( [ new TLabel('8 - Quais as doenças contagiosas da infância já contraídas? '), $doencas_contraidas_infancia ] );
        $this->form->addFields( [ new TLabel('*7 - É epilético? (Sim/Não)')], [$epiletico_s_n ]  );
        $this->form->addFields( [ new TLabel('Em caso de afirmativo, está em tratamento? (Sim/Não)')], [$epiletico_tratamento_s_n ]);
        $this->form->addFields( [ new TLabel('*8 - É hemofílico? (Sim/Não)')], [$hemofilico_s_n ] );
        $this->form->addFields( [ new TLabel('*9 - É deficiente visual? (Sim/Não)')], [$deficiente_visual_s_n ] );
        $this->form->addFields( [ new TLabel('*10 - É deficiente físico? (Sim/Não)')], [$deficiente_fisico_s_n ] );
        $this->form->addFields( [ new TLabel('*11 - É deficiente auditivo? (Sim/Não)')], [$deficiente_auditivo_s_n ] );
        //$this->form->addFields( [ new TLabel('14 - É deficiente intelectual? (Sim/Não)'), $deficiente_intelectual_s_n ] );
        //$this->form->addFields( [ new TLabel('15 - É TEA? (Sim/Não)'), $tea_s_n ] );
        $this->form->addFields( [ new TLabel('*12 - É diabético? (Sim/Não)')],[$diabetico_s_n ] );
        $this->form->addFields( [ new TLabel('Em caso de afirmativo: é dependente de insulina? (Sim/Não)')], [$diabetico_insulina ] );
        $this->form->addFields( [ new TLabel('*13 - É asmático? (Sim/Não)')], [$asmatico_s_n ] );
        $this->form->addFields( [ new TLabel('*14 - Apresenta algum tipo de transtorno diagnosticado? (Sim/Não)')],[$transtorno_s_n ] );
        $this->form->addFields( [ new TLabel('Sim. Qual é?')], [$transtorno ]);
        $this->form->addFields( [ new TLabel('*15 - Está fazendo algum tipo de tratamento médico psicológico? (Sim/Não)')], [$tratamento_medico_s_n ]);
        $this->form->addFields( [ new TLabel('Sim. Qual?')], [$tratamento_medico ]  );
        $this->form->addFields( [ new TLabel('*16 - O(a) aluno(a) possui alguma necessidade específica? (Sim/Não)')], [$necessidade_s_n ]  );
        $this->form->addFields( [ new TLabel('Sim. Qual?')], [$necessidade ]);
        //$this->form->addFields( [ new TLabel('21 - O(a) aluno(a) tem dificuldades e/ou transtornos de aprendizagem, diagnosticados? (Sim/Não)'), $dificuldades_s_n ] );
        //$this->form->addFields( [ new TLabel('Sim. Qual(is)?'), $dificuldades ] );
        $this->form->addFields( [ new TLabel('*17 - Está ingerindo medicação específica? (Sim/Não)')], [$ingere_medicamentos_s_n ]  );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [$ingere_medicamentos ]);
        $this->form->addFields( [ new TLabel('18 - Em caso de necessidade, o(a) aluno(a) deverá ser removido para qual hospital ou clínica?')], [$aluno_hospital ] );
        $this->form->addFields( [ new TLabel('*19 - Faz acompanhamento psicológico e/ou psiquiátrico? (Sim/Não)')], [$acp_psicologico_s_n ] );
        $this->form->addFields( [ new TLabel('Sim. Qual é?')], [$acp_psicologico ] );
        $this->form->addFields( [ new TLabel('Afirmo que os dados inseridos acima são verdadeiros')], [$termo] );


        $cod_aluno->addValidation('Código do aluno', new TRequiredValidator);
        $nome->addValidation('Aluno(a)', new TRequiredValidator);
        $data_nasc->addValidation('Data de nascimento', new TRequiredValidator);
        $endereco->addValidation('Endereço', new TRequiredValidator);
        $bairro->addValidation('Bairro', new TRequiredValidator);
        $cidade->addValidation('Cidade', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);
        $responsavel->addValidation('Responsável pelo aluno(a)', new TRequiredValidator);
        $aluno_mora->addValidation('Com quem mora o(a) aluno(a)?', new TRequiredValidator);
        $alergico_s_n->addValidation('O(a) aluno(a) é alérgico(a)? (Sim/Não)', new TRequiredValidator);
        $alergico_alimento_s_n->addValidation('O(a) aluno(a) tem alergia a algum tipo de alimento?', new TRequiredValidator);
        $plano_de_saude_s_n->addValidation('O(a) aluno(a) possui plano de saúde?', new TRequiredValidator);
        $plano_de_saude->addValidation('Nº do Cartão Nacional de Saúde (SUS)', new TRequiredValidator);
        $alergico_medicamento_s_n->addValidation('O(a) aluno(a) é alérgico a algum medicamento tópico, oral ou injetável?', new TRequiredValidator);
        $hipertensao_s_n->addValidation('Tem hipertensão? (Sim/Não)', new TRequiredValidator);
        $epiletico_s_n->addValidation('É epilético? (Sim/Não)', new TRequiredValidator);
        $hemofilico_s_n->addValidation('É hemofílico? (Sim/Não)', new TRequiredValidator);
        $deficiente_visual_s_n->addValidation('É deficiente visual? (Sim/Não)', new TRequiredValidator);
        $deficiente_fisico_s_n->addValidation('É deficiente físico? (Sim/Não)', new TRequiredValidator);
        $deficiente_auditivo_s_n->addValidation('É deficiente auditivo? (Sim/Não)', new TRequiredValidator);
        $diabetico_s_n->addValidation('É diabético? (Sim/Não)', new TRequiredValidator);
        $asmatico_s_n->addValidation('É asmático? (Sim/Não)', new TRequiredValidator);
        $tratamento_medico_s_n->addValidation('Está fazendo algum tipo de tratamento médico psicológico? (Sim/Não)', new TRequiredValidator);
        $necessidade_s_n->addValidation('O(a) aluno(a) possui alguma necessidade específica? (Sim/Não)', new TRequiredValidator);
        $ingere_medicamentos_s_n->addValidation('Está ingerindo medicação específica? (Sim/Não)', new TRequiredValidator);
        $transtorno_s_n->addValidation('Apresenta algum tipo de transtorno; diagnosticado? (Sim/Não)', new TRequiredValidator);
        $acp_psicologico_s_n->addValidation('Faz acompanhamento psicológico e/ou psiquiátrico? (Sim/Não)', new TRequiredValidator);
        $termo->addValidation('Afirmo que os dados inseridos acima são verdadeiros', new TRequiredValidator);
       

        // set sizes
        $id->setSize('100%');
        $cod_aluno->setSize('100%');
        $cod_aluno->setEditable(false);
        $nome->setSize('100%');
        $nome->setEditable(false);
        $rg->setSize('100%');
        $rg->setEditable(false);
        $cpf->setSize('100%');
        $cpf->setEditable(false);
        $data_nasc->setSize('100%');
        $data_nasc->setEditable(false);
        //$local_nasc->setSize('100%');
        $endereco->setSize('100%');
        $endereco->setEditable(false);
        $cidade->setSize('100%');
        $cidade->setEditable(false);
        $cep->setSize('100%');
        $cep->setEditable(false);
        $bairro->setSize('100%');
        $bairro->setEditable(false);
        $responsavel->setSize('100%');
        $responsavel->setEditable(false);
        $aluno_mora->setSize('100%');
        $telefone->setSize('100%');
        $tipo_sang->setSize('100%');
        $alergico_s_n->setSize('100%');
        $alergico->setSize('100%');
        $medicamento->setSize('100%');
        $alergico_alimento_s_n->setSize('100%');
        $alergico_alimento->setSize('100%');
        $nome_pai->setSize('100%');
        $empresa_pai->setSize('100%');
        $telefone_pai->setSize('100%');
        $nome_mae->setSize('100%');
        $empresa_mae->setSize('100%');
        $telefone_mae->setSize('100%');
        $nome_outros->setSize('100%');
        $empresa_outros->setSize('100%');
        $telefone_outros->setSize('100%');
        $plano_de_saude_s_n->setSize('100%');
        $plano_de_saude->setSize('100%');
        $alergico_medicamento_s_n->setSize('100%');
        $alergico_medicamento->setSize('100%');
        $medico_aluno->setSize('100%');
        $nome_medico->setSize('100%');
        $endereco_medico->setSize('100%');
        $telefone_medico->setSize('100%');
        $observacao_febre->setSize('100%');
        $doenca_congenita_s_n->setSize('100%');
        $doenca_congenita->setSize('100%');
        $hipertensao_s_n->setSize('100%');
        $doencas_contraidas_infancia->setSize('100%');
        $epiletico_s_n->setSize('100%');
        $epiletico_tratamento_s_n->setSize('100%');
        $hemofilico_s_n->setSize('100%');
        $deficiente_visual_s_n->setSize('100%');
        $deficiente_fisico_s_n->setSize('100%');
        $deficiente_auditivo_s_n->setSize('100%');
        $deficiente_intelectual_s_n->setSize('100%');
        $tea_s_n->setSize('100%');
        $diabetico_s_n->setSize('100%');
        $diabetico_insulina->setSize('100%');
        $asmatico_s_n->setSize('100%');
        $transtorno_s_n->setSize('100%');
        $transtorno->setSize('100%');
        $tratamento_medico_s_n->setSize('100%');
        $tratamento_medico->setSize('100%');
        $necessidade_s_n->setSize('100%');
        $necessidade->setSize('100%');
        $dificuldades_s_n->setSize('100%');
        $dificuldades->setSize('100%');
        $ingere_medicamentos_s_n->setSize('100%');
        $ingere_medicamentos->setSize('100%');
        $aluno_hospital->setSize('100%');
        $dificuldades_s_n->setSize('100%');
        $ingere_medicamentos_s_n->setSize('100%');
        $acp_psicologico_s_n->setSize('100%');
        $acp_psicologico->setSize('100%');
        
        
        $termo_array = array();
        $termo_array ['Sim'] = 'Sim';
        
        $termo->addItems($termo_array);
                
        
        //Para preecher os dados pessoais do aluno
        $userid = TSession::getValue('userid');
        
        TTransaction::open('Felabs_DB');

        $user = new SystemUser($userid);
        
        TTransaction::close();
        
        
        TTransaction::open('dados_fei');

        $object = new FiAluno($user->systemuser_codlegado);
        
        $cod_aluno->setValue($object->Codaluno);
        $nome->setValue($object->Nome);
        $rg->setValue($object->Rg);
        $cpf->setValue($object->CPF);
        $data_nasc->setValue(TDate::date2br($object->Datanascimento));
        $endereco->setValue($object->Endereco);
        $bairro->setValue($object->Bairro);
        $cep->setValue($object->Cep);
        $telefone->setValue($object->Telefone);

        $object_cidade = new FiCidade($object->CodCidade);
        $cidade->setValue($object_cidade->Nome);
        
        $object_resp = new FiResponsavel($object->CodResponsavel);
        $responsavel->setValue($object_resp->Nome);

        TTransaction::close();
        
        
        
////////////////////////////////////////////////////////ANOTAÇÕES
        //$this->form->appendPage('Anotações');
        //$observacao = new TEntry('observacao[]');
        //$observacao->setSize('100%');


        
////////////////////////////////////////////////////////ANEXOS
        //$this->form->appendPage('Anexos');
        //$filename = new TFile('filename');
        //$filename = new TMultiFile('multifile');
        //$filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );
        //$this->form->addFields( [ new TLabel('Anexar arquivos e documentos'), $filename ] );
        //$filename->setSize('100%');
        //fazer o tmultifile



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        //$this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addActionLink('Voltar',  new TAction(['FichaMedicaList', 'onReload']), 'fa:arrow-left blue');
        

        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $data = $this->form->getData();
            
            $object = new FichaMedica; 
            
            $object->fromArray( (array) $data); 
            
            $this->form->validate();           

            //Se está salvando um "novo registro", mas já existe registro com mesmo aluno
            if(empty($data->id))
            {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('cod_aluno', '=', $data->cod_aluno));                 

                $repository = new TRepository('FichaMedica'); 
                
                $registros_bd = $repository->load($criteria);
            
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro deste mesmo aluno");
                }
            } 

            $object->termo = implode(',', $object->termo);
            
            $object->nome =  $param["nome"];

            $object->store(); 

            $data->id = $object->id;
           
            $this->form->setData($data); 

            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('FichaMedicaAlunoFormView', 'onLoad');
        }
        
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
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
                
                $object = new FichaMedica($key); 
                
                $object->termo = explode(',', $object->termo);
                
                $this->form->setData($object); 
                
                TTransaction::close(); 
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) 
        {            
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public static function onExitAction($param)
    {
    
        $id = $param['cod_aluno'];
    
        TTransaction::open('dados_fei');
        
        $object_ficha = new FiAluno($id);
        
        $responsavel = $object_ficha->CodResponsavel; 
        $object_resp = new FiResponsavel($responsavel);
        
        $cidade = $object_ficha->CodCidade;  
        $object_cidade = new FiCidade($cidade);

        $obj = new stdClass;
        $obj->rg          = $object_ficha->Rg;
        $obj->cpf         = $object_ficha->CPF;
        $obj->data_nasc   = TDate::date2br($object_ficha->Datanascimento);
        $obj->bairro      = $object_ficha->Bairro;
        $obj->endereco    = $object_ficha->Endereco;
        $obj->cidade      = $object_cidade->Nome;
        $obj->cep         = $object_ficha->Cep;
        $obj->responsavel = $object_resp->Nome;
        
        TTransaction::close();


        TForm::sendData('form_FichaMedica', $obj);        
    }
    
    
    public function onLoad()
    {
    }
}