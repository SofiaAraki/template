<?php

class ReqBolsaAlunoForm extends TPage
{
    protected $form; 
    
    use adianti\base\AdiantiMasterDetailTrait;


    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('list_Requerimento');
        $this->form->setFormTitle('Requerimento de Bolsa de Estudo');
        
        
        // master fields
        $id = new THidden('id');
        $system_user_id = new TEntry('system_user_id');
        $nome = new TEntry('nome');
        $curso = new TCombo('curso');
        $ciclo = new TCombo('ciclo');
        $periodo = new TCombo('periodo');
        $rg = new TEntry('rg');
        $cpf = new TEntry('cpf');
        $data_nascimento = new TEntry('data_nascimento');
        $estado_civil = new TEntry('estado_civil');
        $profissao = new TEntry('profissao');
        $endereco = new TEntry('endereco');
        $endereco_numero = new TEntry('endereco_numero');
        $bairro = new TEntry('bairro');
        $endereco_complemento = new TEntry('endereco_complemento');
        $cidade = new TEntry('cidade');
        $estado = new TCombo('estado');
        $cep = new TEntry('cep');
        $telefone = new TEntry('telefone');
        $celular = new TEntry('celular');
        $telefone_trabalho = new TEntry('telefone_trabalho');
        $email = new TEntry('email');
        $moradia = new TRadioGroup('moradia');
        $moradia_aluno = new TRadioGroup('moradia_aluno');
        $saude_familiar = new TCheckGroup('saude_familiar');
        $saude_aluno = new TCheckGroup('saude_aluno');
        $saude_aluno_neces = new TCheckGroup('saude_aluno_neces');
        $veiculo_aluno = new TRadioGroup('veiculo_aluno');
        $ensino_aluno = new TRadioGroup('ensino_aluno');
        $checar = new TCheckGroup('checar');
        $filename = new TMultiFile('filename');
        $unidade = new THidden('unidade');
        //$cad_unico = new TEntry('cad_unico');
        $outra_graduacao = new TRadioGroup('outra_graduacao');
        $graduacao_anterior = new TEntry('graduacao_anterior');
        

        $filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );
        $filename->enableFileHandling();


        $cpf->setMask('999.999.999-99');
        $data_nascimento->setMask('99/99/9999');
        $cep->setMask('99.999-999');
        $telefone->setMask('(99)9999-9999');
        $celular->setMask('(99)99999-9999');
        $telefone_trabalho->setMask('(99)99999-9999');
        

        $item_checar = array();
        $item_checar['Sim'] = 'Sim';

        $checar->addItems($item_checar);
               

        $itens_uf = array(); 
        $itens_uf['AC'] = 'Acre'; 
        $itens_uf['AL'] = 'Alagoas'; 
        $itens_uf['AP'] = 'Amapá'; 
        $itens_uf['AM'] = 'Amazonas'; 
        $itens_uf['BA'] = 'Bahia'; 
        $itens_uf['CE'] = 'Ceará'; 
        $itens_uf['DF'] = 'Distrito Federal'; 
        $itens_uf['ES'] = 'Espirito Santo'; 
        $itens_uf['GO'] = 'Goiás'; 
        $itens_uf['MA'] = 'Maranhão'; 
        $itens_uf['MT'] = 'Mato Grosso'; 
        $itens_uf['MS'] = 'Mato Grosso do Sul'; 
        $itens_uf['MG'] = 'Minas Gerais'; 
        $itens_uf['PA'] = 'Pará'; 
        $itens_uf['PB'] = 'Paraiba'; 
        $itens_uf['PR'] = 'Paraná'; 
        $itens_uf['PE'] = 'Pernambuco'; 
        $itens_uf['PI'] = 'Piauí'; 
        $itens_uf['RJ'] = 'Rio de Janeiro'; 
        $itens_uf['RN'] = 'Rio Grande do Norte'; 
        $itens_uf['RS'] = 'Rio Grande do Sul'; 
        $itens_uf['RO'] = 'Rondônia'; 
        $itens_uf['RR'] = 'Roraima'; 
        $itens_uf['SC'] = 'Santa Catarina'; 
        $itens_uf['SP'] = 'São Paulo'; 
        $itens_uf['SE'] = 'Sergipe'; 
        $itens_uf['TO'] = 'Tocantins'; 

        $estado->addItems($itens_uf); 
        $estado->enableSearch();


        $itens_periodo = array();
        $itens_periodo['Diurno'] ='Diurno';
        $itens_periodo['Integral'] = 'Integral';
        $itens_periodo['Noturno'] ='Noturno';
        //$itens_periodo['Matutino'] = 'Matutino';
        

        $periodo->addItems($itens_periodo);


        $itens_ciclo = array();
        $itens_ciclo['berçário'] ='Berçário';
        $itens_ciclo['maternal I'] ='Maternal I';
        $itens_ciclo['maternal II'] ='Maternal II';
        $itens_ciclo['1º estágio'] ='1º estágio';
        $itens_ciclo['2º estágio'] ='2º estágio';
        $itens_ciclo['1º ano'] ='1º ano';
        $itens_ciclo['2º ano'] ='2º ano';
        $itens_ciclo['3º ano'] ='3º ano';
        $itens_ciclo['4º ano'] ='4º ano';
        $itens_ciclo['5º ano'] ='5º ano';
        $itens_ciclo['6º ano'] ='6º ano';
        $itens_ciclo['7º ano'] ='7º ano';
        $itens_ciclo['8º ano'] ='8º ano';
        $itens_ciclo['9º ano'] ='9º ano';
        $itens_ciclo['1ª série'] ='1ª série EM';
        $itens_ciclo['2ª série'] ='2ª série EM';
        $itens_ciclo['3ª série'] ='3ª série EM';
        $itens_ciclo['1º ciclo'] ='1º ciclo';
        $itens_ciclo['2º ciclo'] ='2º ciclo';
        $itens_ciclo['3º ciclo'] ='3º ciclo';
        $itens_ciclo['4º ciclo'] ='4º ciclo';
        $itens_ciclo['5º ciclo'] ='5º ciclo';
        $itens_ciclo['6º ciclo'] ='6º ciclo';
        $itens_ciclo['7º ciclo'] ='7º ciclo';
        $itens_ciclo['8º ciclo'] ='8º ciclo';
        $itens_ciclo['9º ciclo'] ='9º ciclo';
        $itens_ciclo['10º ciclo'] ='10º ciclo';        

        $ciclo->addItems($itens_ciclo);


        $curso_nome = array();
        $curso_nome['Técnico em Enfermagem'] ='Técnico em Enfermagem';
        $curso_nome['Educação Infantil'] ='Educação Infantil';
        $curso_nome['Ensino Fundamental I'] ='Ensino Fundamental I';
        $curso_nome['Ensino Fundamental II'] ='Ensino Fundamental II';
        $curso_nome['Ensino Médio'] ='Ensino Médio';
        $curso_nome['Administração'] ='Administração';
        $curso_nome['Agronomia'] ='Agronomia';
        $curso_nome['Ciências Biológicas'] ='Ciências Biológicas';
        $curso_nome['Ciências Contábeis'] ='Ciências Contábeis';
        $curso_nome['Direito'] ='Direito';
        $curso_nome['Enfermagem'] ='Enfermagem';
        $curso_nome['Engenharia Civil'] ='Engenharia Civil';
        $curso_nome['Engenharia de Produção'] ='Engenharia de Produção';
        $curso_nome['Engenharia Elétrica'] ='Engenharia Elétrica';
        $curso_nome['Engenharia Mecânica'] ='Engenharia Mecânica';
        $curso_nome['História'] ='História';
        $curso_nome['Letras'] ='Letras';
        $curso_nome['Matemática'] ='Matemática';
        $curso_nome['Medicina Veterinária'] ='Medicina Veterinária';
        $curso_nome['Pedagogia'] ='Pedagogia';
        $curso_nome['Sistemas de Informação'] ='Sistemas de Informação';             

        $curso->addItems($curso_nome);
        $curso->enableSearch();


        // detail fields
        $item_aluno_id = new THidden('item_aluno_id');
        $item_aluno_item_membro = new TCombo('item_aluno_item_membro');
        $item_aluno_nome = new TEntry('item_aluno_nome');
        $item_aluno_idade = new TEntry('item_aluno_idade');
        $item_aluno_rg = new TEntry('item_aluno_rg');
        $item_aluno_cpf = new TEntry('item_aluno_cpf');
        $item_aluno_profissao = new TEntry('item_aluno_profissao');
        $item_aluno_salario = new TNumeric('item_aluno_salario', '2', ',', '.' );
        $item_aluno_local_trabalho = new TEntry('item_aluno_local_trabalho');


        $item_aluno_cpf->setMask('999.999.999-99');


        $combo1 = array();
        $combo1['Aluno(a)'] ='Aluno(a)';
        $combo1['Esposo(a)'] ='Esposo(a)';
        $combo1['Pai'] ='Pai';
        $combo1['Mãe'] ='Mãe';
        $combo1['Irmão(ã)'] ='Irmão(ã)';
        $combo1['Filho(a)'] ='Filho(a)';
        $combo1['Outro(a)'] ='Outro(a)';
        
        $item_aluno_item_membro->addItems($combo1);
        $item_aluno_item_membro->enableSearch();


        $radio1 = array();
        $radio1['Própria'] ='Própria';
        $radio1['Alugada'] ='Alugada';
        $radio1['Financiada'] ='Financiada';
        $radio1['Cedida'] ='Cedida';
        
        $moradia->setLayout('horizontal');
        $moradia->addItems($radio1);
        
        
        $radio2 = array();
        $radio2['Família'] ='Família';
        $radio2['República'] ='República';
        
        $moradia_aluno->setLayout('horizontal');
        $moradia_aluno->addItems($radio2);
        
        
        $radio3 = array();
        $radio3['Doença crônica'] ='Doença crônica';
        $radio3['Incapacidade física permanente'] ='Incapacidade física permanente';
        
        $saude_familiar->setLayout('horizontal');
        $saude_familiar->addItems($radio3);
        
        
        $radio4 = array();
        $radio4['Doença crônica'] ='Doença crônica';
        $radio4['Incapacidade física permanente'] ='Incapacidade física permanente';
        
        $saude_aluno->setLayout('horizontal');
        $saude_aluno->addItems($radio4);


        $radio5 = array();
        $radio5['Visual'] ='Visual';
        $radio5['Auditiva'] ='Auditiva';
        $radio5['Outra'] ='Outra';
        
        $saude_aluno_neces->setLayout('horizontal');
        $saude_aluno_neces->addItems($radio5);
        
        
        $radio6 = array();
        $radio6['Sim'] ='Sim';
        $radio6['Não'] ='Não';
        
        $veiculo_aluno->setLayout('horizontal');
        $veiculo_aluno->addItems($radio6);


        $radio7 = array();
        $radio7['Pública'] ='Pública';
        $radio7['Particular'] ='Particular';
        
        $ensino_aluno->setLayout('horizontal');
        $ensino_aluno->addItems($radio7);
        
        
        $radio8 = array();
        $radio8['Sim'] = 'Sim';
        $radio8['Não'] = 'Não';
        
        $outra_graduacao->setLayout('horizontal');
        $outra_graduacao->addItems($radio8);
        
        TQuickForm::hideField('list_Requerimento', "graduacao_anterior");

        
        $change_graduacao = new TAction(array($this, 'onChangeGraduacao'));       
        $outra_graduacao->setChangeAction($change_graduacao);


        /* detail fields despesa - Retirado do formulário a partir de 13/04/2022
        $item_despesa_id = new THidden('item_despesa_id');
        $item_despesa_item_tipo = new TCombo('item_despesa_item_tipo');
        $item_despesa_valor = new TNumeric('item_despesa_valor', '2', ',', '.' );

        $combo2 = array();
        $combo2['Energia Elétrica'] ='Energia Elétrica';
        $combo2['Água e Esgoto'] ='Água e Esgoto';
        $combo2['Telefone'] ='Telefone';
        $combo2['Celular'] ='Celular';
        $combo2['Supermercado'] ='Supermercado';
        $combo2['Aluguel'] ='Aluguel';
        $combo2['Financiamento'] ='Financiamento';
        $combo2['Empréstimos bancários'] ='Empréstimos bancários';
        $combo2['Convênio Médico'] ='Convênio Médico';
        $combo2['Outros'] ='Outros';
        
        $item_despesa_item_tipo->addItems($combo2);        
        $item_despesa_item_tipo->enableSearch();*/


        $nome->addValidation('"Nome do Aluno(a)"', new TRequiredValidator());
        $curso->addValidation('"Curso"', new TRequiredValidator());
        $ciclo->addValidation('"Ciclo"', new TRequiredValidator());
        $periodo->addValidation('"Período"', new TRequiredValidator());
        $rg->addValidation('"RG"', new TRequiredValidator());
        $cpf->addValidation('"CPF"', new TRequiredValidator());
        $data_nascimento->addValidation('"Data de nascimento"', new TRequiredValidator());
        $estado_civil->addValidation('"Estado civil"', new TRequiredValidator());
        $profissao->addValidation('"Profissão"', new TRequiredValidator());
        $endereco->addValidation('"Endereço"', new TRequiredValidator());
        $endereco_numero->addValidation('"Nº"', new TRequiredValidator());
        $bairro->addValidation('"Bairro"', new TRequiredValidator());
        $cidade->addValidation('"Cidade"', new TRequiredValidator());
        $estado->addValidation('"Estado"', new TRequiredValidator());
        $cep->addValidation('"CEP"', new TRequiredValidator());
        $telefone->addValidation('"Telefone"', new TRequiredValidator());
        $celular->addValidation('"Celular/WhatsApp"', new TRequiredValidator());
        //$cad_unico->addValidation('"CadÚnico"', new TRequiredValidator());
        $filename->addValidation('"Anexar arquivos"', new TRequiredValidator());
        $outra_graduacao->addValidation('"Possui outra graduação superior"', new TRequiredValidator());
        $moradia->addValidation('"A família reside em moradia"', new TRequiredValidator());
        $moradia_aluno->addValidation('"O aluno reside em:"', new TRequiredValidator());
        $ensino_aluno->addValidation('"O aluno estuda em Escola (Para Alunos do Colégio)"', new TRequiredValidator());
        $veiculo_aluno->addValidation('"A família possui veículo"', new TRequiredValidator());
        $checar->addValidation('"Li e concordo..."', new TRequiredValidator());


        $id->setEditable(false);
        $id->setSize(100);
        $nome->setSize('100%');
        $data_nascimento->setSize('100%');
        $rg->setSize('100%');
        $cpf->setSize('100%');
        $email->setSize('250');
        $estado_civil->setSize('100%');
        $profissao->setSize('100%');
        $endereco->setSize('100%');
        $curso->setSize('250');
        $cidade->setSize('250');
        $endereco_numero->setSize('50%');
        $endereco_complemento->setSize('50%');
        $item_aluno_nome->setSize('100%');
        $item_aluno_idade->setSize('100%');
        $item_aluno_profissao->setSize('100%');
        $item_aluno_salario->setSize('100%');
        $item_aluno_profissao->setSize(220);
        //$item_despesa_valor->setSize('50%');
        $filename->setSize('38%');
        $telefone_trabalho->setSize('100%');
        //$cad_unico->setSize(300);


        //Para preecher os dados pessoais do aluno
        TTransaction::open('dados_fei');

            TTransaction::open('Felabs_DB');

            $logado = SystemUser::newFromLogin(TSession::getValue('login'));
            TTransaction::close();

        $object = new FiAluno($logado->systemuser_codlegado); 
        
        $nome->setValue($object->Nome);
        $rg->setValue($object->Rg);
        $cpf->setValue($object->CPF);
        $data_nascimento->setValue(TDate::date2br($object->Datanascimento));
        $estado_civil->setValue($object->EstadoCivil);
        $profissao->setValue($object->Profissao);
        $endereco->setValue($object->Endereco);
        $endereco_numero->setValue($object->EnderecoNumero);
        $bairro->setValue($object->Bairro);
        $endereco_complemento->setValue($object->EnderecoComplemeto);
        $cep->setValue($object->Cep);
        //$telefone->setValue($object->Telefone);
        $email->setValue($object->Email);

        $object_cidade = new FiCidade($object->CodCidade);
        
        $cidade->setValue($object_cidade->Nome);
        $estado->setValue($object_cidade->Uf);
        
        TTransaction::close();


        //DADOS PESSOAIS DO ALUNO

        // master fields
        $label1 = new TLabel('Dados pessoais do(a) aluno(a)', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        
        $this->form->addFields( [ new TLabel('ID:') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Nome do Aluno(a):', '#ff0000') ], [ $nome ], [ new TLabel('Data de nascimento:', '#ff0000') ], [ $data_nascimento ] );
        $this->form->addFields( [ new TLabel('RG:', '#ff0000') ],[ $rg ], [ new TLabel('CPF:', '#ff0000') ], [ $cpf ] );
        $this->form->addFields( [ new TLabel('Estado civil:', '#ff0000') ], [ $estado_civil ], [ new TLabel('Profissão:', '#ff0000') ], [ $profissao ] );
        $this->form->addFields( [ new TLabel('Endereço:', '#ff0000') ], [ $endereco ], [ new TLabel('Nº:', '#ff0000') ], [ $endereco_numero ] );
        $this->form->addFields( [ new TLabel('Bairro:', '#ff0000') ], [ $bairro ], [ new TLabel('Complemento:')], [ $endereco_complemento ] );
        $this->form->addFields( [ new TLabel('Cidade:', '#ff0000') ], [ $cidade ], [ new TLabel('Estado:', '#ff0000') ], [ $estado ], [ new TLabel('CEP:', '#ff0000') ], [ $cep ] );
        $this->form->addFields( [ new TLabel('Telefone:', '#ff0000') ], [ $telefone ], [ new TLabel('Celular/WhatsApp:', '#ff0000') ], [ $celular ], [ new TLabel('Telefone (trabalho):') ], [ $telefone_trabalho ] );
        $this->form->addFields( [ new TLabel('Email:', '#ff0000') ], [ $email ], [] );
        $this->form->addFields( [ new TLabel('Curso:', '#ff0000') ], [ $curso ], [ new TLabel('Série (Para 2026):', '#ff0000') ], [ $ciclo ], [ new TLabel('Período:', '#ff0000') ], [ $periodo ] );
        //$this->form->addFields([new TLabel('CadÚnico:', '#ff0000')],[$cad_unico], [new TLabel('')]);
        
        
        //DESCRIÇÃO DO GRUPO FAMILIAR
        
        // detail fields
        $label2 = new TLabel('<br>Descrição do grupo familiar', '#285097', 12, 'b');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $label2 ] );
        
        $this->form->addFields( [ new TLabel('- Preencha os campos abaixo e adicione na tabela as informações de cada pessoa que mora na residência <b><font color="red">(incluindo menores e o próprio aluno)</font></b>') ] );


        // add button
        $add_info2 = new TButton('add_info2');
        $add_info2->setAction(new TAction(array($this, 'onInfo2')), 'Orientações');
        $add_info2->setImage('fa:question-circle');
        $this->form->addFields([new TLabel('')],[$add_info2]);


        $this->form->addFields( [ new TLabel('Membro:', '#ff0000') ], [ $item_aluno_item_membro ], [ new TLabel('Nome:', '#ff0000') ], [ $item_aluno_nome ] );
        $this->form->addFields( [ new TLabel('RG:', '#ff0000') ], [ $item_aluno_rg ], [ new TLabel('CPF:', '#ff0000') ], [ $item_aluno_cpf ] );
        $this->form->addFields( [ new TLabel('Idade:', '#ff0000') ], [ $item_aluno_idade ], [ new TLabel('Profissão:', '#ff0000') ], [ $item_aluno_profissao ], [ new TLabel('Salário(R$):', '#ff0000') ], [ $item_aluno_salario ] );
        $this->form->addFields( [ new TLabel('Local de trabalho:') ], [ $item_aluno_local_trabalho ], [] );
        $this->form->addFields( [ $item_aluno_id ] );


        // add button
        $add_item_aluno = new TButton('add_item_aluno');
        $add_item_aluno->setAction(new TAction(array($this, 'onAddItemAluno')), 'Adicionar');
        $add_item_aluno->setImage('fa:plus #51c249');
        $this->form->addFields( [ $add_item_aluno ] );


        // detail datagrid
        $this->item_aluno_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_aluno_list->style = 'width:100%';
        $this->item_aluno_list->class .= ' table-bordered';
        $this->item_aluno_list->disableDefaultClick();
        $this->item_aluno_list->addQuickColumn('', 'edit', 'left', 50);
        $this->item_aluno_list->addQuickColumn('', 'delete', 'left', 50);


        $col_item_membro    = $this->item_aluno_list->addQuickColumn('Membro', 'item_aluno_item_membro', 'left');
        $col_nome           = $this->item_aluno_list->addQuickColumn('Nome', 'item_aluno_nome', 'left');
        $col_rg             = $this->item_aluno_list->addQuickColumn('RG', 'item_aluno_rg', 'left');
        $col_cpf            = $this->item_aluno_list->addQuickColumn('CPF', 'item_aluno_cpf', 'left');
        $col_idade          = $this->item_aluno_list->addQuickColumn('Idade', 'item_aluno_idade', 'left');
        $col_profissao      = $this->item_aluno_list->addQuickColumn('Profissão', 'item_aluno_profissao', 'left');
        $col_salario        = $this->item_aluno_list->addQuickColumn('Salário', 'item_aluno_salario', 'left');
        $col_local_trabalho = $this->item_aluno_list->addQuickColumn('Local de trabalho', 'item_aluno_local_trabalho', 'left');


        $col_salario->setTotalFunction( function($values) { 
            return array_sum((array) $values);
        }); 
        
        
        //$this->item_aluno_list->createModel();


        $col_salario->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return "R$ " . number_format($value, 2, ",", ".");
        }); 
        
        
        $this->item_aluno_list->createModel();
        
        
        $this->form->addContent( [ $this->item_aluno_list ] );
/*

        //DESPESAS DO GRUPO FAMILIAR

        // detail fields despesa
        $label3 = new TLabel('<br>Despesas do grupo familiar', '#285097', 12, 'b');
        $label3->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $label3 ] );
        
        $this->form->addFields( [ new TLabel('- Preencha os campos abaixo e adicione na tabela todas as despesas que compõem a renda familiar (Energia elétrica, Água e Esgoto, Telefone, Supermercado, Convênio Médico)') ] );

        $this->form->addFields( [ new TLabel('Descrição:', '#ff0000') ], [ $item_despesa_item_tipo ], [ new TLabel('Valor:', '#ff0000') ], [ $item_despesa_valor ] );
        $this->form->addFields( [ $item_despesa_id ] );


        // add button
        $add_item_despesa = new TButton('add_item_despesa');
        $add_item_despesa->setAction(new TAction(array($this, 'onAddItemDespesa')), 'Adicionar');
        $add_item_despesa->setImage('fa:plus #51c249');
        $this->form->addFields( [ $add_item_despesa ] );


        // detail datagrid
        $this->item_despesa_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_despesa_list->style = 'width:100%';
        $this->item_despesa_list->class .= ' table-bordered';
        $this->item_despesa_list->disableDefaultClick();
        $this->item_despesa_list->addQuickColumn('', 'edit', 'left', 50);
        $this->item_despesa_list->addQuickColumn('', 'delete', 'left', 50);


        $col_item_tipo = $this->item_despesa_list->addQuickColumn('Descrição', 'item_despesa_item_tipo', 'left');
        $col_valor     = $this->item_despesa_list->addQuickColumn('Valor', 'item_despesa_valor', 'left');


        $col_valor->setTotalFunction( function($values) { 
            return array_sum((array) $values);
        }); 
        
        
        $this->item_despesa_list->createModel();


        $col_valor->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return "R$ " . number_format($value, 2, ",", ".");
        });
        
        
        $this->form->addContent([$this->item_despesa_list]);
*/

        //ESPECIFICAÇÕES                
        $label4 = new TLabel('<br>Especificações', '#285097', 12, 'b');
        $label4->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $label4 ] );


        $this->form->addFields( [ new TLabel('A família reside em moradia:', '#ff0000') ], [ $moradia ], [ new TLabel('O aluno reside em:', '#ff0000') ], [ $moradia_aluno ] );
        $this->form->addFields( [ new TLabel('Saúde das pessoas do grupo familiar que residem juntas:') ], [ $saude_familiar ], [ new TLabel('Saúde do candidato:') ], [ $saude_aluno ] );
        $this->form->addFields( [ new TLabel('Candidato portador de necessidade especial:') ], [ $saude_aluno_neces ], [ new TLabel('A família possui veículo:', '#ff0000') ], [ $veiculo_aluno ] );
        $this->form->addFields( [ new TLabel('(Se aluno da Educação Básica) No ano anterior estudou em escola:', '#ff0000') ], [ $ensino_aluno ], [ new TLabel('O aluno possui outra graduação em Ensino Superior:', '#ff0000') ], [ $outra_graduacao ] );
        $this->form->addFields( [ ], [ ], [ new TLabel('Se sim, qual:') ], [ $graduacao_anterior ] );


        $this->form->addFields( [ new TFormSeparator('', '#333333', '18') ] );

        // add button
        $add_info1 = new TButton('add_info1');
        $add_info1->setAction(new TAction(array($this, 'onInfo1')), 'Termos e condições');
        $add_info1->setImage('fa:info-circle');
     
        $this->form->addFields( [ $add_info1 ] );
 
  
        $this->form->addFields( [ new TLabel('Li e concordo com os Termos e condições', '#ff0000') ], [ $checar ] );


        //ANEXAR DOCUMENTOS           
        $label5 = new TLabel('<br>Anexar documentos', '#285097', 12, 'b');
        $label5->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $label5 ] );
        
        
        $this->form->addFields( [new TLabel("
        <b>- Documentos pessoais de todos que residam na casa, incluindo o(a) aluno(a):</b> cópia do RG e CPF; CNH; certidão de nascimento (para menores de idade), carteira de trabalho, carteira de entidade de classe<br>
    <br><b>- Carteira de trabalho (física ou digital) de cada membro do grupo familiar maior de idade:</b> cópia da página de identificação (com nº de série e foto); da página que indica a qualificação civil; 
             da página com o último registro de contrato de trabalho e da página seguinte em branco (para comprovação do 'não' registro (no caso de pessoas que declaram não possuir renda) ou para comprovação do registro 
             atual de trabalho) <b>ou em caso de impossibilidade, apresentar relatório do Cadastro Nacional de Informações Sociais - CNIS</b><br>
    <br><b>- Comprovantes de renda de todos que residam na casa, incluindo o(a) aluno(a):</b> <b>para trabalhadores assalariados</b>: cópia dos 03 últimos holerites; <b>para aposentados ou pensionistas</b>: extrato recente do pagamento do benefício do INSS; 
             <b>para trabalhadores autônomos</b>: declaração de autônomo ou atividade informal constando atividade exercida, rendimento mensal e mês de referência acompanhado das guias de recolhimento ao INSS dos últimos meses (quando houver);
             <b>para proprietários/sócios e microempreendedores</b>: declaração de pró-labore firmado pelo contabilista com identificação do CRC acompanhado da declaração do Imposto de Renda de Pessoa Jurídica; <b>se o aluno receber pensão alimentícia ou ajuda financeira</b>, 
             anexar o comprovante de pagamento atualizado constando nome e CPF do pagante da pensão, mês de referência e valor recebido e cópia de decisão judicial/acordo homologado judicialmente determinando o pagamento de pensão alimentícia<br>
    <br><b>- Comprovantes de residência:</b> cópia de comprovante de residência atualizado de cada membro do grupo familiar maior de 18 anos (serão aceitos um dos seguintes documentos: conta de água, luz, telefone, carnê do IPTU ou IPVA, demonstrativos bancários 
             de financiamentos, empréstimos; declaração do proprietário do imóvel quando for cedido ou alugado acompanhado de um comprovante em nome do proprietário do imóvel (IPTU/energia/água)<br>
    <br><b>- Documentos acessórios para comprovação de vínculo:</b> se o aluno ou pais do aluno forem casados, certidão de casamento; se o aluno ou pais do aluno forem divorciados, certidão de casamento com a averbação do divórcio; certidão de óbito (em caso de falecimentos dos pais, responsáveis ou viuvez)<br>
    <br><b>- Para alunos do curso superior:</b> declaração que o aluno não concluiu ou que não é possuidor de diploma de curso superior e que não está sendo beneficiado por nenhum programa de custeio educacional oferecido pelo governo, seja municipal, estadual ou federal")]);


        $this->form->addFields( [ '<br>' ] );
        $this->form->addFields( [ new TLabel('Anexar documentos') ], [ $filename ] );
        $this->form->addFields( [ '<br>' ] );

        //$label6 = new TLabel('Caso o aluno precise anexar mais algum documento posteriormente, o mesmo deverá <u>anexar todos os outros documentos novamente.</u>', '#FF0000', 12, 'b');
        //$this->form->addContent( [ $label6 ] );
        
        
        // create the form actions
        $this->form->addAction(('Finalizar'), new TAction(array($this, 'onSave')), 'far:save red');


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        //$container->add(new TXMLBreadCrumb('menu.xml',  __CLASS__));
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReqBolsaAlunoDialogQuestionView'));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onInfo1()
    {
        $data = $this->form->getData();

        $this->form->setData($data);
        
        $win = TWindow::create('Termos e condições', 0.7, 0.7);
        $win->add("
	<br><b>DECLARO</b>, sob as penas da lei que preencho os requisitos, aceitando-os em todos os seus termos e que:<br>
    <br>- Não sou beneficiário(a) de nenhum outro tipo de bolsa de estudo e/ou de qualquer outra forma de benefício com a mesma finalidade;
    <br>- Não apresento pendência financeira com a instituição (no caso de bolsistas parciais (50%) ou referente a material didático para bolsistas de 100%);
    <br>- Estou ciente de que poderei receber visitas domiciliares para comprovar a veracidade das informações e dos documentos por mim apresentados;
    <br>- Autorizo a Fundação Educacional de Ituverava a efetuar sindicância para apurar as informações ora prestadas, comprometendo-me a apresentar, a qualquer momento, todos os documentos que me forem solicitados, sob pena de perda da bolsa.
    <br>- Responsabilizo-me cível, criminal e administrativamente pelas informações prestadas.
    <br>- As informações constantes neste requerimento são verdadeiras e que qualquer alteração nos dados fornecidos será comunicada imediatamente sob pena de cancelamento da bolsa, ciente
    que a prestação de informações falsas e indutivas constitui crime previsto na legislação;
    <br>- Não fui retido(a) no ciclo que estou requerendo a bolsa;
    <br>- Concedo autorização para que a Comissão de Bolsa de Estudos confirme os dados constantes neste Requerimento, por intermédio de Assistente Social contratada por esta Fundação.<br>
	<b> - Estou ciente dos requisitos para o deferimento da bolsa de estudos e do cancelamento da mesma. </b><br>
	<b> - Estou ciente de que terei que refazer este requerimento de bolsa periodicamente, para reavaliação da situação socioeconômica.</b>
	<br>- Ratifico serem verdadeiras as informações prestadas, e, também, declaro que as pessoas mencionadas no presente formulário, ainda que ausente o respectivo comprovante de residência das mesmas, residem comigo, estando ciente de que a informação falsa incorrerá nas penas do crime do art. 299 do Código Penal (falsidade ideológica), além de, caso configurada a prestação de informação falsa, apurada posteriormente à inserção do estudante no referido Programa ou auxílio, ensejará o desligamento imediato deste, sem prejuízo das sanções penais cabíveis. <br>
    <b> - Por fim, assumo exclusivamente a responsabilidade por acompanhar qualquer comunicado referente à bolsa visualizando o sistema acadêmico, sob pena de nada poder reclamar futuramente.<br>
    <br> <b>* Não será analisado o requerimento de pedido de bolsa de estudos se o aluno não apresentar toda a documentação exigida.</b>");
          $win->show();
    }


    public function onInfo2()
    {
        $data = $this->form->getData();

        $this->form->setData($data);
        
        $win = TWindow::create('Orientações para preenchimento', 0.6, null);
        $win->add("Entende-se por grupo familar, o conjunto de pessoas ou parentes que compartilhem rendimentos comuns, incluindo o candidato, pai, padrasto, mãe, madrasta, cônjuge, companheiro(a), filho(a), enteado(a), irmão(a), avô(ó) ou demais pessoas que residam em comum.<br>
        	<br>Por rendimentos poderão ser considerados os valores brutos dos: salários, vencimentos, pró-labore, pensões, proventos, benefícios previdenciários, aluguéis, honorários profissionais e, no caso de autônomo ou empresário, aqueles	contantes da declaração de rendimento mensal fornecido por Contador (contendo o número de registro no C.R.C). Poderão também ser considerados os rendimentos não comprovados da realização de pequenos ou eventuais serviços prestados.<br>
        	<br>Não se entedem os benefícios à título de Bolsa - Auxílios recebidos em programas de estágio, que devem se somar ao total dos rendimentos do(a) candidato(a).");
        $win->show();
    }
    
    
    public static function onChangeGraduacao($param)
    {
        try
        {       
            if($param['outra_graduacao'] == 'Não')
            {
                TQuickForm::hideField('list_Requerimento', 'graduacao_anterior');
            }
            elseif($param['outra_graduacao'] == 'Sim')
            {
                TQuickForm::showField('list_Requerimento', 'graduacao_anterior');
            }
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }


    public function onAddItemAluno( $param )
    {
        try
        {
            $data = $this->form->getData();

            if(!$data->item_aluno_item_membro)
            {
                throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', 'Membro'));
            }

            if(!$data->item_aluno_nome)
            {
                throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', 'Nome'));
            }
            
            if(!$data->item_aluno_rg)
            {
                throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', 'RG'));
            }
            
            if(!$data->item_aluno_cpf)
            {
                throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', 'CPF'));
            }

            if (!$data->item_aluno_idade)
            {
                throw new Exception('O campo Idade é obrigatório');
            }
            
            if (! $data->item_aluno_profissao)
            {
                throw new Exception('O campo Profissão é obrigatório');
            }
            
            if (! $data->item_aluno_salario)
            {
                throw new Exception('O campo Salário é obrigatório');
            }
            
            $item_aluno_items = TSession::getValue('item_aluno_items');
            $key = !empty($data->item_aluno_id) ? $data->item_aluno_id : uniqid();
            
            $fields = []; 
            $fields['item_aluno_item_membro'] = $data->item_aluno_item_membro;
            $fields['item_aluno_nome'] = $data->item_aluno_nome;
            $fields['item_aluno_rg'] = $data->item_aluno_rg;
            $fields['item_aluno_cpf'] = $data->item_aluno_cpf;
            $fields['item_aluno_idade'] = $data->item_aluno_idade;
            $fields['item_aluno_profissao'] = $data->item_aluno_profissao;
            $fields['item_aluno_salario'] = $data->item_aluno_salario;
            $fields['item_aluno_local_trabalho'] = $data->item_aluno_local_trabalho;

            $item_aluno_items[ $key ] = $fields;
            
            TSession::setValue('item_aluno_items', $item_aluno_items);

            // limpa os campos do item do pedido
            $data->item_aluno_item_membro = '';
            $data->item_aluno_nome = '';
            $data->item_aluno_rg = '';
            $data->item_aluno_cpf = '';
            $data->item_aluno_idade = '';
            $data->item_aluno_profissao = '';
            $data->item_aluno_salario = '';
            $data->item_aluno_local_trabalho = '';
            $data->item_aluno_id = '';
            
            $this->form->setData($data);
            $this->onReload( $param );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }


    public function onReload($params = null)
    {    
        $this->loaded = TRUE;
        $this->onReloadAlunoItemAluno($params);
        //$this->onReloadAlunoDespesaAluno($params);
        
        if($params['outra_graduacao'] == 'Sim')
        {
            TQuickForm::showField('list_Requerimento', 'graduacao_anterior');
        }
    }
    

    public function onReloadAlunoItemAluno( $param )
    {
        $items = TSession::getValue('item_aluno_items'); 

        $this->item_aluno_list->clear(); 

        if($items) 
        { 
            $cont = 1; 
            foreach ($items as $key => $item) 
            {
                $rowItem = new StdClass;

                $action_del = new TAction(array($this, 'onDeleteItemAluno')); 
                $action_del->setParameter('item_aluno_id_row_id', $key);   

                $action_edi = new TAction(array($this, 'onEditItemAluno'));  
                $action_edi->setParameter('item_aluno_id_row_id', $key);  

                $button_del = new TButton('delete_item_aluno'.$cont);
                $button_del->class = 'btn btn-default btn-sm';
                $button_del->setAction($action_del, '');
                $button_del->setImage('far:trash-alt'); 
                $button_del->setFormName($this->form->getName());

                $button_edi = new TButton('edit_item_aluno'.$cont);
                $button_edi->class = 'btn btn-default btn-sm';
                $button_edi->setAction($action_edi, '');
                $button_edi->setImage('far:edit');
                $button_edi->setFormName($this->form->getName());

                $rowItem->edit   = $button_edi;
                $rowItem->delete = $button_del;
                
                $rowItem->item_aluno_item_membro = isset($item['item_aluno_item_membro']) ? $item['item_aluno_item_membro'] : '';
                $rowItem->item_aluno_nome = isset($item['item_aluno_nome']) ? $item['item_aluno_nome'] : '';
                $rowItem->item_aluno_rg = isset($item['item_aluno_rg']) ? $item['item_aluno_rg'] : '';
                $rowItem->item_aluno_cpf = isset($item['item_aluno_cpf']) ? $item['item_aluno_cpf'] : '';
                $rowItem->item_aluno_idade = isset($item['item_aluno_idade']) ? $item['item_aluno_idade'] : '';                
                $rowItem->item_aluno_profissao = isset($item['item_aluno_profissao']) ? $item['item_aluno_profissao'] : '';
                $rowItem->item_aluno_salario = isset($item['item_aluno_salario']) ? $item['item_aluno_salario'] : '';
                $rowItem->item_aluno_local_trabalho = isset($item['item_aluno_local_trabalho']) ? $item['item_aluno_local_trabalho'] : '';

                $this->item_aluno_list->addItem($rowItem);
                $cont ++;
            } 
        } 
    }


    public function onEditItemAluno( $param )
    {
        $data = $this->form->getData();

        // read session items
        $items = TSession::getValue('item_aluno_items');

        // get the session item
        $item = $items[$param['item_aluno_id_row_id']];

        $data->item_aluno_item_membro = $item['item_aluno_item_membro'];
        $data->item_aluno_nome = $item['item_aluno_nome'];
        $data->item_aluno_rg = $item['item_aluno_rg'];
        $data->item_aluno_cpf = $item['item_aluno_cpf'];
        $data->item_aluno_idade = $item['item_aluno_idade'];
        $data->item_aluno_profissao = $item['item_aluno_profissao'];
        $data->item_aluno_salario = $item['item_aluno_salario'];
        $data->item_aluno_local_trabalho = $item['item_aluno_local_trabalho'];
        $data->item_aluno_id = $param['item_aluno_id_row_id'];
        
        // fill product fields
        $this->form->setData( $data );

        $this->onReload( $param );
    }


    public function onDeleteItemAluno( $param )
    {
        $data = $this->form->getData();

        $data->item_aluno_item_membro = '';
        $data->item_aluno_nome = '';
        $data->item_aluno_rg = '';
        $data->item_aluno_cpf = '';
        $data->item_aluno_idade = '';
        $data->item_aluno_profissao = '';
        $data->item_aluno_salario = '';
        $data->item_aluno_local_trabalho = '';
        $this->form->setData( $data );

        // read session items
        $items = TSession::getValue('item_aluno_items');

        // delete the item from session
        unset($items[$param['item_aluno_id_row_id']]);
        TSession::setValue('item_aluno_items', $items);
        
        $this->onReload( $param );
    }


    /*public function onAddItemDespesa( $param )
    {
        try
        {
            $data = $this->form->getData();

            if(!$data->item_despesa_item_tipo)
            {
                throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', 'Descrição'));
            }

            if(!$data->item_despesa_valor)
            {
                throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', 'Valor'));
            }
            
            $item_despesa_items = TSession::getValue('item_despesa_items');
            $key = !empty($data->item_despesa_id) ? $data->item_despesa_id : uniqid();
            
            $fields = []; 
            $fields['item_despesa_item_tipo'] = $data->item_despesa_item_tipo;
            $fields['item_despesa_valor'] = $data->item_despesa_valor;
            
            $item_despesa_items[ $key ]        = $fields;
            
            TSession::setValue('item_despesa_items', $item_despesa_items);

            // limpa os campos do item do pedido
            $data->item_despesa_item_tipo = '';
            $data->item_despesa_valor = '';
            $data->item_despesa_id = '';
            
            $this->form->setData($data);
            $this->onReload( $param );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }*/


    /*public function onReloadAlunoDespesaAluno( $param )
    {
        $items = TSession::getValue('item_despesa_items'); 

        $this->item_despesa_list->clear(); 

        if($items) 
        { 
            $cont = 1; 
            foreach ($items as $key => $item) 
            {
                $rowDespesa = new StdClass;

                $action_del_despesa = new TAction(array($this, 'onDeleteDespesaAluno')); 
                $action_del_despesa->setParameter('item_despesa_id_row_id', $key);   

                $action_edi_despesa = new TAction(array($this, 'onEditDespesaAluno'));  
                $action_edi_despesa->setParameter('item_despesa_id_row_id', $key);  

                $button_del_despesa = new TButton('delete_despesa_aluno'.$cont);
                $button_del_despesa->class = 'btn btn-default btn-sm';
                $button_del_despesa->setAction($action_del_despesa, '');
                $button_del_despesa->setImage('far:trash-alt'); 
                $button_del_despesa->setFormName($this->form->getName());

                $button_edi_despesa = new TButton('edit_despesa_aluno'.$cont);
                $button_edi_despesa->class = 'btn btn-default btn-sm';
                $button_edi_despesa->setAction($action_edi_despesa, '');
                $button_edi_despesa->setImage('far:edit');
                $button_edi_despesa->setFormName($this->form->getName());

                $rowDespesa->edit   = $button_edi_despesa;
                $rowDespesa->delete = $button_del_despesa;
                
                $rowDespesa->item_despesa_item_tipo = isset($item['item_despesa_item_tipo']) ? $item['item_despesa_item_tipo'] : '';
                $rowDespesa->item_despesa_valor = isset($item['item_despesa_valor']) ? $item['item_despesa_valor'] : '';
               
                $this->item_despesa_list->addItem($rowDespesa);
                $cont ++;
            } 
        } 
    }*/


    /*public function onEditDespesaAluno( $param )
    {
        $data = $this->form->getData();

        // read session items
        $items = TSession::getValue('item_despesa_items');

        // get the session item
        $item = $items[$param['item_despesa_id_row_id']];

        $data->item_despesa_item_tipo = $item['item_despesa_item_tipo'];
        $data->item_despesa_valor = $item['item_despesa_valor'];
        $data->item_despesa_id = $param['item_despesa_id_row_id'];
        
        // fill product fields
        $this->form->setData( $data );

        $this->onReload( $param );
    }*/


    /*public function onDeleteDespesaAluno( $param )
    {
        $data = $this->form->getData();

        $data->item_despesa_item_tipo = '';
        $data->item_despesa_valor = '';
        $this->form->setData( $data );

        // read session items
        $items = TSession::getValue('item_despesa_items');

        // delete the item from session
        unset($items[$param['item_despesa_id_row_id']]);
        TSession::setValue('item_despesa_items', $items);
        
        $this->onReload( $param );
    }*/


    public function onClear( $param )
    {
        $this->form->clear();
        TSession::setValue('item_aluno_items', null);
        //TSession::setValue('item_despesa_items', null);
        $this->onReload();
    }


    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
			$prefs  = SystemPreference::getAllPreferences();
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));

            $this->form->validate();
            $data = $this->form->getData();
            
            $object = new ReqBolsaAluno; 
            $object->fromArray( (array) $data);
            $object->system_user_id = TSession::getValue('userid');
            $object->situacao = "Aberto";
            $object->unidade = TSession::getValue('userunitid');

            //check
            $object->saude_familiar = implode(',', $object->saude_familiar);
            $object->saude_aluno = implode(',', $object->saude_aluno);
            $object->saude_aluno_neces = implode(',', $object->saude_aluno_neces);
            $object->checar = implode(',', $object->checar);


            //Se aluno marcou que possui outra graduação, forçar preenchimento do curso
            if($object->outra_graduacao == 'Sim' AND empty($object->graduacao_anterior))
            {
                throw new Exception('Ao informar que já possui outra graduação no Ensino Superior, é necessário preencher o nome do curso');
            }
                                        
            // Limpa o campo com o nome do curso, caso o aluno tenha preenchido e alterado a opção de outra graduação para Não
            if($object->outra_graduacao == 'Não' AND !empty($object->graduacao_anterior))
            {
                $object->graduacao_anterior = '';
            }
             

            if($data->id)
            {                           
                if($data->filename)
                {                        
                    /*Código Matheus
                    $rb = new ReqBolsaAluno($data->id);
                    $arqBanco = $rb->filename;

                    $contador = count($data->filename);
                    $i = $contador-1;

                    $teste = $data->filename[$i];

                    if($teste != $arqBanco)
                    {                    
                        $zip = new ZipArchive();
                        $today = date("Ymd");
                        $nomeArquivo = "arquivos/"."req_bolsa"."_$today_".time().'.zip';
                        $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                    
                        foreach ($data->filename as $arq)
                        {
                            $source_file   = 'tmp/'.$arq;
                            
                            if (file_exists($source_file))
                            {    
                                $zip->addFile(  'tmp/'.$arq , "$arq" );                            
                            }
                        }
                    
                        $zip->close();

                        $object->filename = $nomeArquivo;
                    }Termina aqui*/
                    
                    //Teste - só acrescentar arquivo faltante, sem precisar adicionar tudo de novo
                    $rb = new ReqBolsaAluno($data->id);
                    $arqBanco = $rb->filename;
                    
                    $zip = new ZipArchive;
                    
                    if($zip->open($arqBanco) === TRUE)
                    {
                        $qtde = $zip->numFiles;
                        
                        $i = $qtde + 1;

                        foreach($data->filename as $arq)
                        {
                            //Desconsidera o zip e possível índice em branco/nulo
                            if($arq <> $arqBanco AND (!empty($arq)) AND $arq <> NULL)
                            {   
                                //Retira caracteres especiais que a ativação do "enableFileHandling" faz gerar
                                $arquivo = substr((json_decode(urldecode($arq))->fileName), 4);
                                
                                $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
                                //$data->filename = $nomeArquivo;
                                                        
                                $zip->addFile('tmp/'.$arquivo, "Documento_$i.$extensao");                                  
                            }
                            
                            $i++;                                                            
                        }
    
                        $zip->close();
                    } 
                    else
                    {
                        throw new Exception("Falha ao anexar arquivos, tente novamente");
                    }
                }
            }
            else //quando é um novo registro
            {
                /*Código Matheus
                $zip = new ZipArchive();
                $today = date("Ymd");
                $nomeArquivo = "arquivos/"."req_bolsa"."_$today_".time().'.zip';
                $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                
                foreach($data->filename as $arq)
                {
                    $source_file   = 'tmp/'.$arq;
                    
                    if (file_exists($source_file))
                    {
                        $zip->addFile(  'tmp/'.$arq , "$arq" );                        
                    }
                }
                
                $zip->close();

                $object->filename = $nomeArquivo;
                Termina aqui*/
                
                
                //Teste - renomeia arquivo para tentar corrigir problema de gerar arquivo em branco
                $zip = new ZipArchive;
                    
                $today = date("Ymd");
                $nomeArquivo = "arquivos/"."req_bolsa"."_$today_".time().'.zip';
                $i = 1;
                   
                    
                if ($zip->open("$nomeArquivo", ZipArchive::CREATE) === TRUE)
                {
                    foreach($data->filename as $arq)
                    {
                        $arquivo = substr((json_decode(urldecode($arq))->fileName), 4);
                        $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
                        $data->filename = $nomeArquivo;
                                            
                        $zip->addFile('tmp/'.$arquivo, "Documento_$i.$extensao"); 
                        
                        $i++;                       
                    }

                    $zip->close();
                    $object->filename = $nomeArquivo;
                } 
                else 
                {
                    throw new Exception("Falha ao anexar arquivos, tente novamente");
                }   
            }


            $object->store(); 
            
            
            $this->storeItems('ReqBolsaAlunoItem', 'req_bolsa_aluno_id', $object, 'item_aluno',
                function($masterObject, $detailObject) { 
                	$masterObject->renda_familiar += ($detailObject->salario);
                	$masterObject->n_pessoa += count($detailObject->idade);                
            });

            /*$this->storeItems('ReqBolsaAlunoDespesa', 'req_bolsa_aluno_id', $object, 'item_despesa',
                function($masterObject, $detailObject) { 
                    
            });*/


            $object->renda_percapita += ($object->renda_familiar / $object->n_pessoa);
            $object->rf_salario_minimo += round($object->renda_familiar / 1100, 2);
            $object->rp_salario_minimo += round($object->renda_percapita / 1100, 2);

            $object->store();
            
            
            $data->id = $object->id; 
            $this->form->setData($data);
            TTransaction::close();


            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('ReqBolsaAlunoList', 'onReload');


            //email aluno
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Aluno - FEAcadêmico');
            $mail->setSubject('Requerimento de Bolsa');
            $mail->setTextBody('Prezado(a) aluno(a), seu Requerimento de Bolsa foi enviado para avaliação! Acompanhe a situação através da Área do Aluno - FEAcadêmico.'."\n". 'Esta é uma mensagem automática. Solicitamos, por favor, não responder este e-mail.');  

            $emails = explode(',', $logged-> email);
            
            if ($emails)
            {
                foreach ($emails as $email)
                {
                    if ($email)
                    {
                        $mail->addAddress(trim($email), $logged->name);
                    }
                }
            }            
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();
      }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }   


    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                TTransaction::open('Felabs_DB');
                
                //$object = new Aluno($key); 
                $object = new stdClass; 

                $logado = SystemUser::newFromLogin(TSession::getValue('login'));
                $object->system_user_id = $logado->id;
               
                $testaid = $logado->id;

                $object = new ReqBolsaAluno($key);
                $system_user_id = new stdClass;
                $system_user_id->system_user_id = $object->system_user_id;
                
                $this->onClear( $param );                
              
                TTransaction::close();
                
                $verificaid = $system_user_id->system_user_id;
                
                if($verificaid == $testaid)
                {
                    TTransaction::open('Felabs_DB');

                    $object = new ReqBolsaAluno($key);

                    //teste
                    $this->loadItems('ReqBolsaAlunoItem', 'req_bolsa_aluno_id', $object, 'item_aluno');
                    //$this->loadItems('ReqBolsaAlunoDespesa', 'req_bolsa_aluno_id', $object, 'item_despesa');
          
                    //check
                    $object->saude_familiar = explode(',', $object->saude_familiar);
                    $object->saude_aluno = explode(',', $object->saude_aluno);
                    $object->saude_aluno_neces = explode(',', $object->saude_aluno_neces);
                    $object->checar = explode(',', $object->checar);

                    
                    if($object->outra_graduacao == 'Sim')
                    {
                        TQuickForm::showField('list_Requerimento', 'graduacao_anterior');
                    }
                    elseif($object->outra_graduacao == 'Não')
                    {
                        TQuickForm::hideField('list_Requerimento', 'graduacao_anterior');    
                    }
                    
                    $this->form->setData($object); 
                    $this->onReload();
          
                    TTransaction::close(); 
                }
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function show() 
    { 
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') ) 
        { 
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }

    public function onShow( $param )
    {     
        TSession::setValue('item_aluno_items', null);
        //TSession::setValue('item_despesa_items', null);
        $this->onReload();
    }
} 


