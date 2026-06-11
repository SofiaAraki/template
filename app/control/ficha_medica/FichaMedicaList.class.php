<?php

class FichaMedicaList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    

    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('Felabs_DB');
        $this->setActiveRecord('FichaMedica');
        $this->setDefaultOrder('id', 'asc');
        $this->setLimit(10);
        //$this->setCriteria($criteria)

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('cod_aluno', 'like', 'cod_aluno'); // filterField, operator, formField
        $this->addFilterField('nome', 'like', 'nome'); // filterField, operator, formField
        $this->addFilterField('rg', 'like', 'rg'); // filterField, operator, formField
        $this->addFilterField('cpf', 'like', 'cpf'); // filterField, operator, formField
        $this->addFilterField('data_nasc', 'like', 'data_nasc'); // filterField, operator, formField
        $this->addFilterField('local_nasc', 'like', 'local_nasc'); // filterField, operator, formField
        $this->addFilterField('endereco', 'like', 'endereco'); // filterField, operator, formField
        $this->addFilterField('cidade', 'like', 'cidade'); // filterField, operator, formField
        $this->addFilterField('cep', 'like', 'cep'); // filterField, operator, formField
        $this->addFilterField('bairro', 'like', 'bairro'); // filterField, operator, formField
        $this->addFilterField('responsavel', 'like', 'responsavel'); // filterField, operator, formField
        $this->addFilterField('aluno_mora', 'like', 'aluno_mora'); // filterField, operator, formField
        $this->addFilterField('telefone', 'like', 'telefone'); // filterField, operator, formField
        $this->addFilterField('tipo_sang', 'like', 'tipo_sang'); // filterField, operator, formField
        $this->addFilterField('alergico_s_n', 'like', 'alergico_s_n'); // filterField, operator, formField
        $this->addFilterField('alergico', 'like', 'alergico'); // filterField, operator, formField
        $this->addFilterField('medicamento', 'like', 'medicamento'); // filterField, operator, formField
        $this->addFilterField('alergico_alimento_s_n', 'like', 'alergico_alimento_s_n'); // filterField, operator, formField
        $this->addFilterField('alergico_alimento', 'like', 'alergico_alimento'); // filterField, operator, formField
        $this->addFilterField('observacao', 'like', 'observacao'); // filterField, operator, formField
        $this->addFilterField('nome_pai', 'like', 'nome_pai'); // filterField, operator, formField
        $this->addFilterField('empresa_pai', 'like', 'empresa_pai'); // filterField, operator, formField
        $this->addFilterField('telefone_pai', 'like', 'telefone_pai'); // filterField, operator, formField
        $this->addFilterField('nome_mae', 'like', 'nome_mae'); // filterField, operator, formField
        $this->addFilterField('empresa_mae', 'like', 'empresa_mae'); // filterField, operator, formField
        $this->addFilterField('telefone_mae', 'like', 'telefone_mae'); // filterField, operator, formField
        $this->addFilterField('nome_outros', 'like', 'nome_outros'); // filterField, operator, formField
        $this->addFilterField('empresa_outros', 'like', 'empresa_outros'); // filterField, operator, formField
        $this->addFilterField('telefone_outros', 'like', 'telefone_outros'); // filterField, operator, formField
        $this->addFilterField('plano_de_saude_s_n', 'like', 'plano_de_saude_s_n'); // filterField, operator, formField
        $this->addFilterField('plano_de_saude', 'like', 'plano_de_saude'); // filterField, operator, formField
        $this->addFilterField('alergico_medicamento_s_n', 'like', 'alergico_medicamento_s_n'); // filterField, operator, formField
        $this->addFilterField('alergico_medicamento', 'like', 'alergico_medicamento'); // filterField, operator, formField
        $this->addFilterField('medico_aluno', 'like', 'medico_aluno'); // filterField, operator, formField
        $this->addFilterField('nome_medico', 'like', 'nome_medico'); // filterField, operator, formField
        $this->addFilterField('endereco_medico', 'like', 'endereco_medico'); // filterField, operator, formField
        $this->addFilterField('telefone_medico', 'like', 'telefone_medico'); // filterField, operator, formField
        $this->addFilterField('observacao_febre', 'like', 'observacao_febre'); // filterField, operator, formField
        $this->addFilterField('doenca_congenita_s_n', 'like', 'doenca_congenita_s_n'); // filterField, operator, formField
        $this->addFilterField('doenca_congenita', 'like', 'doenca_congenita'); // filterField, operator, formField
        $this->addFilterField('hipertensao_s_n', 'like', 'hipertensao_s_n'); // filterField, operator, formField
        $this->addFilterField('hipertensao', 'like', 'hipertensao'); // filterField, operator, formField
        $this->addFilterField('doencas_contraidas_infancia', 'like', 'doencas_contraidas_infancia'); // filterField, operator, formField
        $this->addFilterField('epiletico_s_n', 'like', 'epiletico_s_n'); // filterField, operator, formField
        $this->addFilterField('epiletico_tratamento_s_n', 'like', 'epiletico_tratamento_s_n'); // filterField, operator, formField
        $this->addFilterField('hemofilico_s_n', 'like', 'hemofilico_s_n'); // filterField, operator, formField
        $this->addFilterField('deficiente_visual_s_n', 'like', 'deficiente_visual_s_n'); // filterField, operator, formField
        $this->addFilterField('deficiente_fisico_s_n', 'like', 'deficiente_fisico_s_n'); // filterField, operator, formField
        $this->addFilterField('deficiente_auditivo_s_n', 'like', 'deficiente_auditivo_s_n'); // filterField, operator, formField
        $this->addFilterField('deficiente_intelectual_s_n', 'like', 'deficiente_intelectual_s_n'); // filterField, operator, formField
        $this->addFilterField('tea_s_n', 'like', 'tea_s_n'); // filterField, operator, formField
        $this->addFilterField('diabetico_s_n', 'like', 'diabetico_s_n'); // filterField, operator, formField
        $this->addFilterField('diabetico_insulina', 'like', 'diabetico_insulina'); // filterField, operator, formField
        $this->addFilterField('asmatico_s_n', 'like', 'asmatico_s_n'); // filterField, operator, formField
        $this->addFilterField('tratamento_medico_s_n', 'like', 'tratamento_medico_s_n'); // filterField, operator, formField
        $this->addFilterField('tratamento_medico', 'like', 'tratamento_medico'); // filterField, operator, formField
        $this->addFilterField('necessidade_s_n', 'like', 'necessidade_s_n'); // filterField, operator, formField
        $this->addFilterField('necessidade', 'like', 'necessidade'); // filterField, operator, formField
        $this->addFilterField('dificuldades_s_n', 'like', 'dificuldades_s_n'); // filterField, operator, formField
        $this->addFilterField('ingere_medicamentos_s_n', 'like', 'ingere_medicamentos_s_n'); // filterField, operator, formField
        $this->addFilterField('ingere_medicamentos', 'like', 'ingere_medicamentos'); // filterField, operator, formField
        $this->addFilterField('aluno_hospital', 'like', 'aluno_hospital'); // filterField, operator, formField
        $this->addFilterField('filename', 'like', 'filename'); // filterField, operator, formField
        
        //creates the form
        $this->form = new BootstrapFormBuilder('form_search_FichaMedica');
        $this->form->setFormTitle('Ficha Médica');
        

        //create the form fields
        $id = new TEntry('id');
        $cod_aluno = new TEntry('cod_aluno');
        $nome = new TEntry('nome');
        $rg = new TEntry('rg');
        $cpf = new TEntry('cpf');
        $data_nasc = new TEntry('data_nasc');
        $local_nasc = new TEntry('local_nasc');
        $endereco = new TEntry('endereco');
        $cidade = new TEntry('cidade');
        $cep = new TEntry('cep');
        $bairro = new TEntry('bairro');
        $responsavel = new TEntry('responsavel');
        $aluno_mora = new TEntry('aluno_mora');
        $telefone = new TEntry('telefone');
        $tipo_sang = new TEntry('tipo_sang');
        $alergico_s_n = new TEntry('alergico_s_n');
        $alergico = new TEntry('alergico');
        $medicamento = new TEntry('medicamento');
        $alergico_alimento_s_n = new TEntry('alergico_alimento_s_n');
        $alergico_alimento = new TEntry('alergico_alimento');
        $observacao = new TEntry('observacao');
        $nome_pai = new TEntry('nome_pai');
        $empresa_pai = new TEntry('empresa_pai');
        $telefone_pai = new TEntry('telefone_pai');
        $nome_mae = new TEntry('nome_mae');
        $empresa_mae = new TEntry('empresa_mae');
        $telefone_mae = new TEntry('telefone_mae');
        $nome_outros = new TEntry('nome_outros');
        $empresa_outros = new TEntry('empresa_outros');
        $telefone_outros = new TEntry('telefone_outros');
        $plano_de_saude_s_n = new TEntry('plano_de_saude_s_n');
        $plano_de_saude = new TEntry('plano_de_saude');
        $alergico_medicamento_s_n = new TEntry('alergico_medicamento_s_n');
        $alergico_medicamento = new TEntry('alergico_medicamento');
        $medico_aluno = new TEntry('medico_aluno');
        $nome_medico = new TEntry('nome_medico');
        $endereco_medico = new TEntry('endereco_medico');
        $telefone_medico = new TEntry('telefone_medico');
        $observacao_febre = new TEntry('observacao_febre');
        $doenca_congenita_s_n = new TEntry('doenca_congenita_s_n');
        $doenca_congenita = new TEntry('doenca_congenita');
        $hipertensao_s_n = new TEntry('hipertensao_s_n');
        $hipertensao = new TEntry('hipertensao');
        $doencas_contraidas_infancia = new TEntry('doencas_contraidas_infancia');
        $epiletico_s_n = new TEntry('epiletico_s_n');
        $epiletico_tratamento_s_n = new TEntry('epiletico_tratamento_s_n');
        $hemofilico_s_n = new TEntry('hemofilico_s_n');
        $deficiente_visual_s_n = new TEntry('deficiente_visual_s_n');
        $deficiente_fisico_s_n = new TEntry('deficiente_fisico_s_n');
        $deficiente_auditivo_s_n = new TEntry('deficiente_auditivo_s_n');
        $deficiente_intelectual_s_n = new TEntry('deficiente_intelectual_s_n');
        $tea_s_n = new TEntry('tea_s_n');
        $diabetico_s_n = new TEntry('diabetico_s_n');
        $diabetico_insulina = new TEntry('diabetico_insulina');
        $asmatico_s_n = new TEntry('asmatico_s_n');
        $tratamento_medico_s_n = new TEntry('tratamento_medico_s_n');
        $tratamento_medico = new TEntry('tratamento_medico');
        $necessidade_s_n = new TEntry('necessidade_s_n');
        $necessidade = new TEntry('necessidade');
        $dificuldades_s_n = new TEntry('dificuldades_s_n');
        $ingere_medicamentos_s_n = new TEntry('ingere_medicamentos_s_n');
        $ingere_medicamentos = new TEntry('ingere_medicamentos');
        $aluno_hospital = new TEntry('aluno_hospital');
        $filename = new TEntry('filename');


        //add the fields
        //$this->form->addFields( [ new TLabel('ID') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Código do aluno:') ], [ $cod_aluno ] );
        $this->form->addFields( [ new TLabel('Nome completo do(a) aluno(a):') ], [ $nome ] );
        //$this->form->addFields( [ new TLabel('RG:') ], [ $rg ] );
        //$this->form->addFields( [ new TLabel('Cpf') ], [ $cpf ] );
        //$this->form->addFields( [ new TLabel('Data Nasc') ], [ $data_nasc ] );
        //$this->form->addFields( [ new TLabel('Local Nasc') ], [ $local_nasc ] );
        //$this->form->addFields( [ new TLabel('Endereco') ], [ $endereco ] );
        //$this->form->addFields( [ new TLabel('Cidade') ], [ $cidade ] );
        //$this->form->addFields( [ new TLabel('Cep') ], [ $cep ] );
        //$this->form->addFields( [ new TLabel('Bairro') ], [ $bairro ] );
        //$this->form->addFields( [ new TLabel('Responsavel') ], [ $responsavel ] );
        //$this->form->addFields( [ new TLabel('Aluno Mora') ], [ $aluno_mora ] );
        //$this->form->addFields( [ new TLabel('Telefone') ], [ $telefone ] );
        //$this->form->addFields( [ new TLabel('Tipo Sang') ], [ $tipo_sang ] );
        //$this->form->addFields( [ new TLabel('Alergico S N') ], [ $alergico_s_n ] );
        //$this->form->addFields( [ new TLabel('Alergico') ], [ $alergico ] );
        //$this->form->addFields( [ new TLabel('Medicamento') ], [ $medicamento ] );
        //$this->form->addFields( [ new TLabel('Alergico Alimento S N') ], [ $alergico_alimento_s_n ] );
        //$this->form->addFields( [ new TLabel('Alergico Alimento') ], [ $alergico_alimento ] );
        //$this->form->addFields( [ new TLabel('Observacao') ], [ $observacao ] );
        //$this->form->addFields( [ new TLabel('Nome Pai') ], [ $nome_pai ] );
        //$this->form->addFields( [ new TLabel('Empresa Pai') ], [ $empresa_pai ] );
        //$this->form->addFields( [ new TLabel('Telefone Pai') ], [ $telefone_pai ] );
        //$this->form->addFields( [ new TLabel('Nome Mae') ], [ $nome_mae ] );
        //$this->form->addFields( [ new TLabel('Empresa Mae') ], [ $empresa_mae ] );
        //$this->form->addFields( [ new TLabel('Telefone Mae') ], [ $telefone_mae ] );
        //$this->form->addFields( [ new TLabel('Nome Outros') ], [ $nome_outros ] );
        //$this->form->addFields( [ new TLabel('Empresa Outros') ], [ $empresa_outros ] );
        //$this->form->addFields( [ new TLabel('Telefone Outros') ], [ $telefone_outros ] );
        //$this->form->addFields( [ new TLabel('Plano De Saude S N') ], [ $plano_de_saude_s_n ] );
        //$this->form->addFields( [ new TLabel('Plano De Saude') ], [ $plano_de_saude ] );
        //$this->form->addFields( [ new TLabel('Alergico Medicamento S N') ], [ $alergico_medicamento_s_n ] );
        //$this->form->addFields( [ new TLabel('Alergico Medicamento') ], [ $alergico_medicamento ] );
        //$this->form->addFields( [ new TLabel('Medico Aluno') ], [ $medico_aluno ] );
        //$this->form->addFields( [ new TLabel('Nome Medico') ], [ $nome_medico ] );
        //$this->form->addFields( [ new TLabel('Endereco Medico') ], [ $endereco_medico ] );
        //$this->form->addFields( [ new TLabel('Telefone Medico') ], [ $telefone_medico ] );
        //$this->form->addFields( [ new TLabel('Observacao Febre') ], [ $observacao_febre ] );
        //$this->form->addFields( [ new TLabel('Doenca Congenita S N') ], [ $doenca_congenita_s_n ] );
        //$this->form->addFields( [ new TLabel('Doenca Congenita') ], [ $doenca_congenita ] );
        //$this->form->addFields( [ new TLabel('Hipertensao S N') ], [ $hipertensao_s_n ] );
        //$this->form->addFields( [ new TLabel('Hipertensao') ], [ $hipertensao ] );
        //$this->form->addFields( [ new TLabel('Doencas Contraidas Infancia') ], [ $doencas_contraidas_infancia ] );
        //$this->form->addFields( [ new TLabel('Epiletico S N') ], [ $epiletico_s_n ] );
        //$this->form->addFields( [ new TLabel('Epiletico Tratamento S N') ], [ $epiletico_tratamento_s_n ] );
        //$this->form->addFields( [ new TLabel('Hemofilico S N') ], [ $hemofilico_s_n ] );
        //$this->form->addFields( [ new TLabel('Deficiente Visual S N') ], [ $deficiente_visual_s_n ] );
        //$this->form->addFields( [ new TLabel('Deficiente Fisico S N') ], [ $deficiente_fisico_s_n ] );
        //$this->form->addFields( [ new TLabel('Deficiente Auditivo S N') ], [ $deficiente_auditivo_s_n ] );
        //$this->form->addFields( [ new TLabel('Deficiente Intelectual S N') ], [ $deficiente_intelectual_s_n ] );
        //$this->form->addFields( [ new TLabel('Tea S N') ], [ $tea_s_n ] );
        //$this->form->addFields( [ new TLabel('Diabetico S N') ], [ $diabetico_s_n ] );
        //$this->form->addFields( [ new TLabel('Diabetico Insulina') ], [ $diabetico_insulina ] );
        //$this->form->addFields( [ new TLabel('Asmatico S N') ], [ $asmatico_s_n ] );
        //$this->form->addFields( [ new TLabel('Tratamento Medico S N') ], [ $tratamento_medico_s_n ] );
        //$this->form->addFields( [ new TLabel('Tratamento Medico') ], [ $tratamento_medico ] );
        //$this->form->addFields( [ new TLabel('Necessidade S N') ], [ $necessidade_s_n ] );
        //$this->form->addFields( [ new TLabel('Necessidade') ], [ $necessidade ] );
        //$this->form->addFields( [ new TLabel('Dificuldades S N') ], [ $dificuldades_s_n ] );
        //$this->form->addFields( [ new TLabel('Ingere Medicamentos S N') ], [ $ingere_medicamentos_s_n ] );
        //$this->form->addFields( [ new TLabel('Ingere Medicamentos') ], [ $ingere_medicamentos ] );
        //$this->form->addFields( [ new TLabel('Aluno Hospital') ], [ $aluno_hospital ] );
        //$this->form->addFields( [ new TLabel('Filename') ], [ $filename ] );


        //set sizes
        $id->setSize('100%');
        $cod_aluno->setSize('100%');
        $nome->setSize('100%');
        $rg->setSize('100%');
        $cpf->setSize('100%');
        $data_nasc->setSize('100%');
        $local_nasc->setSize('100%');
        $endereco->setSize('100%');
        $cidade->setSize('100%');
        $cep->setSize('100%');
        $bairro->setSize('100%');
        $responsavel->setSize('100%');
        $aluno_mora->setSize('100%');
        $telefone->setSize('100%');
        $tipo_sang->setSize('100%');
        $alergico_s_n->setSize('100%');
        $alergico->setSize('100%');
        $medicamento->setSize('100%');
        $alergico_alimento_s_n->setSize('100%');
        $alergico_alimento->setSize('100%');
        $observacao->setSize('100%');
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
        $hipertensao->setSize('100%');
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
        $tratamento_medico_s_n->setSize('100%');
        $tratamento_medico->setSize('100%');
        $necessidade_s_n->setSize('100%');
        $necessidade->setSize('100%');
        $dificuldades_s_n->setSize('100%');
        $ingere_medicamentos_s_n->setSize('100%');
        $ingere_medicamentos->setSize('100%');
        $aluno_hospital->setSize('100%');
        $filename->setSize('100%');

        
        //keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        //add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FichaMedicaForm', 'onEdit']), 'fa:plus green');
        
        //creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        //$this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        //creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_cod_aluno = new TDataGridColumn('cod_aluno', 'Cód. Aluno', 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome:', 'left');
        $column_rg = new TDataGridColumn('rg', 'RG', 'left');
        $column_cpf = new TDataGridColumn('cpf', 'CPF', 'left');
        $column_data_nasc = new TDataGridColumn('data_nasc', 'Data de nasc.', 'left');
        //$column_local_nasc = new TDataGridColumn('local_nasc', 'Local de nascimento:', 'left');
        $column_endereco = new TDataGridColumn('endereco', 'Endereço:', 'left');
        $column_cidade = new TDataGridColumn('cidade', 'Cidade:', 'left');
        //$column_cep = new TDataGridColumn('cep', 'CEP:', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro:', 'left');
        $column_responsavel = new TDataGridColumn('responsavel', 'Responsável pelo aluno(a):', 'left');
        $column_aluno_mora = new TDataGridColumn('aluno_mora', 'Com quem mora o(a) aluno(a)?', 'left');
        $column_telefone = new TDataGridColumn('telefone', 'Telefone(s) ', 'left');
        $column_tipo_sang = new TDataGridColumn('tipo_sang', 'Tipo Sanguíneo (em atendimento CITEM/DEINF/CGAB019/2021)', 'left');
        $column_alergico_s_n = new TDataGridColumn('alergico_s_n', 'O(a) aluno(a) é alérgico(a)? (Sim/Não)', 'left');
        $column_alergico = new TDataGridColumn('alergico', 'Sim. Qual(is)?', 'left');
        //$column_medicamento = new TDataGridColumn('medicamento', 'Medicamento', 'left');
        $column_alergico_alimento_s_n = new TDataGridColumn('alergico_alimento_s_n', 'O(a) aluno(a) tem alergia algum tipo de alimento?', 'left');
        $column_alergico_alimento = new TDataGridColumn('alergico_alimento', 'Sim. Qual(is)?', 'left');
        //$column_observacao = new TDataGridColumn('observacao', 'Observação geral', 'left');
        $column_nome_pai = new TDataGridColumn('nome_pai', 'Nome do pai', 'left');
        $column_empresa_pai = new TDataGridColumn('empresa_pai', 'Empresa em que o pai trabalha', 'left');
        $column_telefone_pai = new TDataGridColumn('telefone_pai', 'Telefone(s) (Pai)', 'left');
        $column_nome_mae = new TDataGridColumn('nome_mae', 'Nome da mãe', 'left');
        $column_empresa_mae = new TDataGridColumn('empresa_mae', 'Empresa em que a mãe trabalha', 'left');
        $column_telefone_mae = new TDataGridColumn('telefone_mae', 'Telefone(s)(Mãe)', 'left');
        $column_nome_outros = new TDataGridColumn('nome_outros', 'Nome de outros', 'left');
        $column_empresa_outros = new TDataGridColumn('empresa_outros', 'Empresa em que outros trabalha', 'left');
        $column_telefone_outros = new TDataGridColumn('telefone_outros', 'Telefone(s) (Outros)', 'left');
        $column_plano_de_saude_s_n = new TDataGridColumn('plano_de_saude_s_n', '1 - O(a) aluno(a) possui plano de saúde?', 'left');
        $column_plano_de_saude = new TDataGridColumn('plano_de_saude', 'Sim. Qual? (Número da Carteirinha)', 'left');
        $column_alergico_medicamento_s_n = new TDataGridColumn('alergico_medicamento_s_n', '<b>2 - O(a) aluno(a) é alérgico a algum medicamento tópico, oral ou injetável?</b>', 'left');
        $column_alergico_medicamento = new TDataGridColumn('alergico_medicamento', 'Sim. Qual(is)?', 'left');
        $column_medico_aluno = new TDataGridColumn('medico_aluno', '3 - O médico do(a) aluno(a) é', 'left');
        $column_nome_medico = new TDataGridColumn('nome_medico', '4 - Nome do médico', 'left');
        $column_endereco_medico = new TDataGridColumn('endereco_medico', 'Endereço do médico', 'left');
        $column_telefone_medico = new TDataGridColumn('telefone_medico', 'Telefones para contato do médico (inclusive celular)', 'left');
        $column_observacao_febre = new TDataGridColumn('observacao_febre', '5 - Em caso de febre alta, não sendo localizado os pais ou responsáveis pelo(a) aluno(a) com qual medicamento ele deverá ser medicado e a quantidade, por indicação médica', 'left');
        $column_doenca_congenita_s_n = new TDataGridColumn('doenca_congenita_s_n', 'Doenca Congenita S N', 'left');
        $column_doenca_congenita = new TDataGridColumn('doenca_congenita', '6 - A criança tem doença congênita? (Sim/Não)', 'left');
        $column_hipertensao_s_n = new TDataGridColumn('hipertensao_s_n', '7 - Tem hipertensão? (Sim/Não)', 'left');
        //$column_hipertensao = new TDataGridColumn('hipertensao', 'Hipertensao', 'left');
        $column_doencas_contraidas_infancia = new TDataGridColumn('doencas_contraidas_infancia', '8 - Quais as doenças contagiosas da infância já contraídas?', 'left');
        $column_epiletico_s_n = new TDataGridColumn('epiletico_s_n', '9 - É epilético? (Sim/Não)', 'left');
        $column_epiletico_tratamento_s_n = new TDataGridColumn('epiletico_tratamento_s_n', 'Em caso de afirmativo, está em tratamento? (Sim/Não)', 'left');
        $column_hemofilico_s_n = new TDataGridColumn('hemofilico_s_n', '10 - É hemofílico? (Sim/Não)', 'left');
        $column_deficiente_visual_s_n = new TDataGridColumn('deficiente_visual_s_n', '11 - É deficiente visual? (Sim/Não)', 'left');
        $column_deficiente_fisico_s_n = new TDataGridColumn('deficiente_fisico_s_n', '12 - É deficiente físico? (Sim/Não)', 'left');
        $column_deficiente_auditivo_s_n = new TDataGridColumn('deficiente_auditivo_s_n', '13 - É deficiente auditivo? (Sim/Não)', 'left');
        $column_deficiente_intelectual_s_n = new TDataGridColumn('deficiente_intelectual_s_n', '14 - É deficiente intelectual? (Sim/Não)', 'left');
        $column_tea_s_n = new TDataGridColumn('tea_s_n', '15 - É TEA? (Sim/Não)', 'left');
        $column_diabetico_s_n = new TDataGridColumn('diabetico_s_n', '16 - É diabético? (Sim/Não)', 'left');
        $column_diabetico_insulina = new TDataGridColumn('diabetico_insulina', 'Em caso de afirmativo: é dependente de insulina? (Sim/Não)', 'left');
        $column_asmatico_s_n = new TDataGridColumn('asmatico_s_n', '18 - É asmático? (Sim/Não)', 'left');
        $column_tratamento_medico_s_n = new TDataGridColumn('tratamento_medico_s_n', '19 - Está fazendo algum tipo de tratamento médico? (Sim/Não)', 'left');
        $column_tratamento_medico = new TDataGridColumn('tratamento_medico', 'Sim. Qual?', 'left');
        $column_necessidade_s_n = new TDataGridColumn('necessidade_s_n', '20 - O(a) aluno(a) possui alguma necessidade específica? (Sim/Não)', 'left');
        $column_necessidade = new TDataGridColumn('necessidade', 'Sim. Qual?', 'left');
        $column_dificuldades_s_n = new TDataGridColumn('dificuldades_s_n', '21 - O(a) aluno(a) tem dificuldades e/ou transtornos de aprendizagem, diagnosticados? (Sim/Não)', 'left');
        $column_ingere_medicamentos_s_n = new TDataGridColumn('ingere_medicamentos_s_n', '22 - Está ingerindo medicação específica? (Sim/Não)', 'left');
        $column_ingere_medicamentos = new TDataGridColumn('ingere_medicamentos', 'Sim. Qual(is)?', 'left');
        $column_aluno_hospital = new TDataGridColumn('aluno_hospital', '23 - Em caso de necessidade, o(a) aluno(a) deverá ser removido para qual hospital ou clínica?', 'left');
        $column_filename = new TDataGridColumn('filename', 'Arquivos e documentos anexados', 'left');


        //add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cod_aluno);
        $this->datagrid->addColumn($column_nome);
        //$this->datagrid->addColumn($column_rg);
        //$this->datagrid->addColumn($column_cpf);
        //$this->datagrid->addColumn($column_data_nasc);
        //$this->datagrid->addColumn($column_local_nasc);
        //$this->datagrid->addColumn($column_endereco);
        //$this->datagrid->addColumn($column_cidade);
        //$this->datagrid->addColumn($column_cep);
        //$this->datagrid->addColumn($column_bairro);
        //$this->datagrid->addColumn($column_responsavel);
        $this->datagrid->addColumn($column_aluno_mora);
        $this->datagrid->addColumn($column_telefone);
        //$this->datagrid->addColumn($column_tipo_sang);
        //$this->datagrid->addColumn($column_alergico_s_n);
        //$this->datagrid->addColumn($column_alergico);
        //$this->datagrid->addColumn($column_medicamento);
        //$this->datagrid->addColumn($column_alergico_alimento_s_n);
        //$this->datagrid->addColumn($column_alergico_alimento);
        //$this->datagrid->addColumn($column_observacao);
        //$this->datagrid->addColumn($column_nome_pai);
        //$this->datagrid->addColumn($column_empresa_pai);
        //$this->datagrid->addColumn($column_telefone_pai);
        //$this->datagrid->addColumn($column_nome_mae);
        //$this->datagrid->addColumn($column_empresa_mae);
        //$this->datagrid->addColumn($column_telefone_mae);
        //$this->datagrid->addColumn($column_nome_outros);
        //$this->datagrid->addColumn($column_empresa_outros);
        //$this->datagrid->addColumn($column_telefone_outros);
        //$this->datagrid->addColumn($column_plano_de_saude_s_n);
        //$this->datagrid->addColumn($column_plano_de_saude);
        //$this->datagrid->addColumn($column_alergico_medicamento_s_n);
        //$this->datagrid->addColumn($column_alergico_medicamento);
        //$this->datagrid->addColumn($column_medico_aluno);
        //$this->datagrid->addColumn($column_nome_medico);
        //$this->datagrid->addColumn($column_endereco_medico);
        //$this->datagrid->addColumn($column_telefone_medico);
        //$this->datagrid->addColumn($column_observacao_febre);
        //$this->datagrid->addColumn($column_doenca_congenita_s_n);
        //$this->datagrid->addColumn($column_doenca_congenita);
        //$this->datagrid->addColumn($column_hipertensao_s_n);
        //$this->datagrid->addColumn($column_hipertensao);
        //$this->datagrid->addColumn($column_doencas_contraidas_infancia);
        //$this->datagrid->addColumn($column_epiletico_s_n);
        //$this->datagrid->addColumn($column_epiletico_tratamento_s_n);
        //$this->datagrid->addColumn($column_hemofilico_s_n);
        //$this->datagrid->addColumn($column_deficiente_visual_s_n);
        //$this->datagrid->addColumn($column_deficiente_fisico_s_n);
        //$this->datagrid->addColumn($column_deficiente_auditivo_s_n);
        //$this->datagrid->addColumn($column_deficiente_intelectual_s_n);
        //$this->datagrid->addColumn($column_tea_s_n);
        //$this->datagrid->addColumn($column_diabetico_s_n);
        //$this->datagrid->addColumn($column_diabetico_insulina);
        //$this->datagrid->addColumn($column_asmatico_s_n);
        //$this->datagrid->addColumn($column_tratamento_medico_s_n);
        //$this->datagrid->addColumn($column_tratamento_medico);
        //$this->datagrid->addColumn($column_necessidade_s_n);
        //$this->datagrid->addColumn($column_necessidade);
        //$this->datagrid->addColumn($column_dificuldades_s_n);
        //$this->datagrid->addColumn($column_ingere_medicamentos_s_n);
        //$this->datagrid->addColumn($column_ingere_medicamentos);
        //$this->datagrid->addColumn($column_aluno_hospital);
        //$this->datagrid->addColumn($column_filename);

        
        $action1 = new TDataGridAction(['FichaMedicaForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        $action3 = new TDataGridAction([$this, 'onSetDadosAnotacaoFichaMedica'], ['id'=>'{id}']);
        $action4 = new TDataGridAction([$this, 'onSetDadosAnexoFichaMedica'], ['id'=>'{id}']);
        $action5 = new TDataGridAction(['FichaMedicaFormView', 'onEdit'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1,_t('Edit'),   'far:edit blue fa-lg');
        $this->datagrid->addAction($action2,_t('Delete'), 'far:trash-alt red fa-lg');
        $this->datagrid->addAction($action3,('Notes'), 'fa:medkit black fa-lg');
        $this->datagrid->addAction($action4,('Upload'), 'fas:cloud-upload-alt blue');
        $this->datagrid->addAction($action5,('Print'), 'fa:print orange');
        
        
        
        //create the datagrid model
        $this->datagrid->createModel();
        
        //creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        //header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        //vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    public function onSetDadosAnexoFichaMedica($param)
    {
        try
        {
            $id = $param['id'];
            
            TTransaction::open('Felabs_DB');
            
            $ficha_medica = new FichaMedica($id);        
            
            TTransaction::close();
            
                            
            //Limpa variável para garantir integridade
            TSession::setValue('dados_ficha_medica', NULL);
        
            //Passa os dados da documentação
            TSession::setValue('dados_ficha_medica', $ficha_medica);


            TApplication::loadPage('AnexosFichaMedicaFormList', 'onReload');
        } 
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }   
    }
    
    public function onSetDadosAnotacaoFichaMedica($param)
    {
        try
        {
            $id = $param['id'];
          
            TTransaction::open('Felabs_DB');
            
            $ficha_medica = new FichaMedica($id);        
            
            TTransaction::close();
            
                            
            //Limpa variável para garantir integridade
            TSession::setValue('dados_ficha_medica', NULL);
        
            //Passa os dados da documentação
            TSession::setValue('dados_ficha_medica', $ficha_medica);


            TApplication::loadPage('AnotacaoFichaMedicaFormList', 'onReload');
        } 
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }   
    }
}