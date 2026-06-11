<?php
/**
 * DespesaForm Form
 * @author  <your name here>
 */
class ReqBolsaAlunoForm extends TPage
{
    protected $form; // form
    
    use adianti\base\AdiantiMasterDetailTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('list_Requerimento');
        $this->form->setFormTitle('Requerimento de Bolsa de Estudo');
        
        // master fields
        $id = new TEntry('id');
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
        $estado = new TEntry('estado');
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
        $cad_unico = new TEntry('cad_unico');

        $filename->setCompleteAction(new TAction(array($this, 'onComplete')));
        $filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );

        $cpf->setMask('999.999.999-99');
        $data_nascimento->setMask('99/99/9999');
        $cep->setMask('99.999-999');
        $telefone->setMask('(99)9999-9999');
        $celular->setMask('(99)99999-9999');
        $telefone_trabalho->setMask('(99)9999-9999');

        $item_checar = array();
        $item_checar['Sim'] ='Sim';

        $checar->addItems($item_checar);

        /**
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
        $estado->enableSearch();*/

        $itens_periodo = array();
        $itens_periodo['Diurno'] ='Diurno';
        $itens_periodo['Noturno'] ='Noturno';

        $periodo->addItems($itens_periodo);

        $itens_ciclo = array();
        $itens_ciclo['1º ciclo'] ='1º ciclo';
        $itens_ciclo['2º ciclo'] ='2º ciclo';
        $itens_ciclo['3º ciclo'] ='3º ciclo';
        $itens_ciclo['4º ciclo'] ='4º ciclo';
        $itens_ciclo['5º ciclo'] ='5º ciclo';
        $itens_ciclo['6º ciclo'] ='6º ciclo';
        $itens_ciclo['7º ciclo'] ='7º ciclo';
        $itens_ciclo['8º ciclo'] ='8º ciclo';

        $ciclo->addItems($itens_ciclo);
        $ciclo->enableSearch();

        $curso_nome = array();
        $curso_nome['Administração'] ='Administração';
        $curso_nome['Agronomia'] ='Agronomia';
        $curso_nome['Ciências Biológicas'] ='Ciências Biológicas';
        $curso_nome['Ciências Contábeis'] ='Ciências Contábeis';
        $curso_nome['Direito'] ='Direito';
        $curso_nome['Enfermagem'] ='Enfermagem';
        $curso_nome['Engenharia Civil'] ='Engenharia Civil';
        $curso_nome['Engenharia de Produção'] ='Engenharia de Produção';
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
        $item_aluno_profissao = new TEntry('item_aluno_profissao');
        $item_aluno_salario = new TNumeric('item_aluno_salario', '2', ',', '.' );
        $item_aluno_local_trabalho = new TEntry('item_aluno_local_trabalho');

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

        // detail fields despesa
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
        
        $item_despesa_item_tipo->enableSearch();

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
        $cad_unico->addValidation('"CadÚnico"', new TRequiredValidator());
        $filename->addValidation('"Anexar arquivos"', new TRequiredValidator());

        $moradia->addValidation('"A família reside em moradia"', new TRequiredValidator());
        $moradia_aluno->addValidation('"O aluno reside em:"', new TRequiredValidator());
        $ensino_aluno->addValidation('"O aluno concluiu o Ensino Médio em Escola"', new TRequiredValidator());
        $veiculo_aluno->addValidation('"A família possui veículo"', new TRequiredValidator());
        $checar->addValidation('"Li e concordo..."', new TRequiredValidator());

        $id->setEditable(false);
        $id->setSize(100);
        $nome->setSize('100%');
        $email->setSize('100%');
        $estado_civil->setSize('46%');
        $endereco->setSize(300);
        $curso->setSize(300);
        $endereco_numero->setSize('50%');
        $item_aluno_nome->setSize('100%');
        $item_aluno_idade->setSize('50');
        $item_aluno_idade->setSize('100%');
        $item_aluno_profissao->setSize('100%');
        $item_aluno_salario->setSize('100%');
        $item_aluno_idade->setSize(70);
        $item_aluno_profissao->setSize(220);
        $item_despesa_valor->setSize('50%');
        $filename->setSize('38%');
        $telefone_trabalho->setSize('46%');
        $cad_unico->setSize(300);

        //para preecher os dados pessoais do aluno

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
        $telefone->setValue($object->Telefone);
        $email->setValue($object->Email);

        $object_cidade = new FiCidade($object->CodCidade);
        
        $cidade->setValue($object_cidade->Nome);
        $estado->setValue($object_cidade->Uf);

        TTransaction::close();

        // master fields
        $label1 = new TLabel('Dados pessoais do aluno(a).', '#285097', 12, 'bi');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        $this->form->addFields( [new TLabel('ID:')], [$id],[new TLabel('Nome do Aluno(a):', '#ff0000')], [$nome] );
        $this->form->addFields([new TLabel('RG:', '#ff0000')],[$rg], [new TLabel('CPF:', '#ff0000')],[$cpf], [new TLabel('Data de nascimento:', '#ff0000')], [$data_nascimento]);
        $this->form->addFields([new TLabel('Estado civil:', '#ff0000')],[$estado_civil],[new TLabel('Profissão:', '#ff0000')], [$profissao]);
        $this->form->addFields([new TLabel('Endereço:', '#ff0000')],[$endereco], [new TLabel('Nº:', '#ff0000')], [$endereco_numero], [new TLabel('Bairro:', '#ff0000')], [$bairro]);
        $this->form->addFields([new TLabel('Complemento:')], [$endereco_complemento], [new TLabel('Cidade:', '#ff0000')],[$cidade], [new TLabel('Estado:', '#ff0000')],[$estado]);
        $this->form->addFields([new TLabel('CEP:', '#ff0000')], [$cep], [new TLabel('Telefone:', '#ff0000')],[$telefone], [new TLabel('Celular:')], [$celular]);
        $this->form->addFields([new TLabel('Telefone (trabalho):')], [$telefone_trabalho],[new TLabel('Email:')], [$email]);
        $this->form->addFields([new TLabel('Curso:', '#ff0000')],[$curso], [new TLabel('Ciclo:', '#ff0000')],[$ciclo], [new TLabel('Período:', '#ff0000')], [$periodo]);
        $this->form->addFields([new TLabel('CadÚnico:', '#ff0000')],[$cad_unico], [new TLabel('')]);
        

        // detail fields
        $label2 = new TLabel('Descrição do grupo familiar que reside com o aluno(a).', '#285097', 12, 'bi');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );
        $this->form->addFields( [new TLabel('- Adicione o nome de todas as pessoas que moram na residência (inclusive menores) com suas respectivas profissões e salários.')]);
        //$this->form->addFields( [new TLabel('Entende-se por grupo familar, o conjunto de pessoas ou parentes que compartilhem rendimentos comuns, incluindo o candidato, pai, padrasto, mãe, madrasta, cônjuge, companheiro(a), filho(a), enteado(a), irmão(a), avô(ó) ou demais pessoas que residam em comum.')]);
        //$this->form->addFields( [new TLabel('Por rendimentos poderão ser considerados os valores brutos dos: salários, vencimentos, pró-labore, pensões, proventos, benefícios previdenciários, aluguéis, honorários profissionais e, no caso de autônomo ou empresário, aqueles	contantes da declaração de rendimento mensal fornecido por Contador (contendo o número de registro no C.R.C). Poderão também ser considerados os rendimentos não comprovados da realização de pequenos ou eventuais serviços prestados. ')]);
        //$this->form->addFields( [new TLabel('Não se entedem os benefícios a titulo de Bolsa - Auxilio recebidos em programas de estágio, que devem se somar ao total dos rendimentos do(a) candidato(a).')]);
        //$this->form->addContent([new TFormSeparator('- Cite o nome de todas as pessoas que moram na residência (inclusive menores) com suas respectivas profissões e salários. (TRAZER CÓPIA I.R., HOLERITE OU DECLARAÇÃO DE AUTÔNOMO). Entende-se por grupo familar, o conjunto de pessoas ou parentes que compartilhem rendimentos comuns, incluindo o candidato, pai, padrasto, mãe, madrasta, cônjuge, companheiro(a), filho(a), enteado(a), irmão(a), avô(ó) ou demais pessoas que residam em comum.','text-align:left;')]);

        // add button
        $add_info2 = new TButton('add_info2');
        $add_info2->setAction(new TAction(array($this, 'onInfo2')), 'Orientações');
        $add_info2->setImage('fa:question-circle');
        $this->form->addFields([new TLabel('')],[$add_info2]);

        
        $this->form->addFields([new TLabel('Membro:', '#ff0000')],[$item_aluno_item_membro], [new TLabel('Nome:', '#ff0000')],[$item_aluno_nome] );
        //$this->form->addFields([new TLabel('Nome:')],[$item_aluno_nome], [new TLabel('')] );
        $this->form->addFields([new TLabel('Idade:', '#ff0000')],[$item_aluno_idade], [new TLabel('Profissão:')], [$item_aluno_profissao], [new TLabel('Salário(R$):')],[$item_aluno_salario]);
        $this->form->addFields([new TLabel('Local de trabalho:')],[$item_aluno_local_trabalho],[new TLabel('')] );
        $this->form->addFields([$item_aluno_id]);

        // add button
        $add_item_aluno = new TButton('add_item_aluno');
        $add_item_aluno->setAction(new TAction(array($this, 'onAddItemAluno')), 'Adicionar');
        $add_item_aluno->setImage('fa:plus #51c249');
        $this->form->addFields([$add_item_aluno]);

        //$this->form->addFields([new TLabel('Anexar arquivos')], [$filename]);
        // detail datagrid
        $this->item_aluno_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_aluno_list->style = 'width:100%';
        $this->item_aluno_list->class .= ' table-bordered';
        $this->item_aluno_list->disableDefaultClick();
        $this->item_aluno_list->addQuickColumn('', 'edit', 'left', 50);
        $this->item_aluno_list->addQuickColumn('', 'delete', 'left', 50);

        $col_item_membro      = $this->item_aluno_list->addQuickColumn('Membro', 'item_aluno_item_membro', 'left');
        $col_nome       = $this->item_aluno_list->addQuickColumn('Nome', 'item_aluno_nome', 'left');
        $col_idade        = $this->item_aluno_list->addQuickColumn('Idade', 'item_aluno_idade', 'left');
        $col_profissao             = $this->item_aluno_list->addQuickColumn('Profissão', 'item_aluno_profissao', 'left');
        $col_salario             = $this->item_aluno_list->addQuickColumn('Salário', 'item_aluno_salario', 'left');
        $col_local_trabalho             = $this->item_aluno_list->addQuickColumn('Local de trabalho', 'item_aluno_local_trabalho', 'left');

        $col_salario->setTotalFunction( function($values) { 
            return array_sum((array) $values);
        }); 
        
        $this->item_aluno_list->createModel();

        $col_salario->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return "R$ " . number_format($value, 2, ",", ".");
        }); 
        
        $this->item_aluno_list->createModel();
        
        $this->form->addContent([$this->item_aluno_list]);

        // detail fields despesa
        $label3 = new TLabel('Despesas do grupo familiar.', '#285097', 12, 'bi');
        $label3->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label3] );
        //$this->form->addContent([new TFormSeparator('- Adicione todas as despesas que compõem a renda familiar.  ')]);
        $this->form->addFields( [new TLabel('- Adicione todas as despesas que compõem a renda familiar (Energia elétrica, Água e Esgoto, Telefone, Supermercado...)')]);

        $this->form->addFields([new TLabel('Descrição:', '#ff0000')],[$item_despesa_item_tipo], [new TLabel('Valor:', '#ff0000')],[$item_despesa_valor] );
        //$this->form->addFields([new TLabel('Valor:')],[$item_despesa_valor], [new TLabel('')] );
        $this->form->addFields([$item_despesa_id]);

        // add button
        $add_item_despesa = new TButton('add_item_despesa');
        $add_item_despesa->setAction(new TAction(array($this, 'onAddItemDespesa')), 'Adicionar');
        $add_item_despesa->setImage('fa:plus #51c249');
        $this->form->addFields([$add_item_despesa]);

        //$this->form->addFields([new TLabel('Anexar arquivos')], [$filename]);
        // detail datagrid
        $this->item_despesa_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_despesa_list->style = 'width:100%';
        $this->item_despesa_list->class .= ' table-bordered';
        $this->item_despesa_list->disableDefaultClick();
        $this->item_despesa_list->addQuickColumn('', 'edit', 'left', 50);
        $this->item_despesa_list->addQuickColumn('', 'delete', 'left', 50);

        $col_item_tipo      = $this->item_despesa_list->addQuickColumn('Descrição', 'item_despesa_item_tipo', 'left');
        $col_valor      = $this->item_despesa_list->addQuickColumn('Valor', 'item_despesa_valor', 'left');

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

        //$this->form->addFields( [new TLabel('- Adicione os seus documentos pessoais, comprovantes de renda e residência, entre outros.')]);

        //$this->form->addFields([new TLabel('Anexar arquivos')], [$filename]);
        
        $label4 = new TLabel('Especificações', '#285097', 12, 'bi');
        $label4->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label4] );
        //$this->form->addFields( [new TFormSeparator('Especificações', '#333333', '18')] );

        //$this->form->addFields([new TLabel('<b><u>- Moradia:</u></b>')]);
        $this->form->addFields([new TLabel('A família reside em moradia:', '#ff0000')], [$moradia], [new TLabel('O aluno reside em:', '#ff0000')], [$moradia_aluno]);
        //$this->form->addFields([new TLabel('O aluno reside em:')], [$moradia_aluno]);
        //$this->form->addFields( [new TFormSeparator('', '#333333', '18')] );
        //$this->form->addFields([new TLabel('<b>- Saúde:</b>')]);
        $this->form->addFields([new TLabel('Saúde das pessoas do grupo familiar que residem juntas:')], [$saude_familiar], [new TLabel('Saúde do candidato:')], [$saude_aluno]);
        $this->form->addFields([new TLabel('Candidato portador de necessidade especial:')], [$saude_aluno_neces],[new TLabel('A família possui veículo:', '#ff0000')], [$veiculo_aluno]);
        //$this->form->addFields( [new TFormSeparator('Observação: se o candidato assinalar algum dos itens acima sobre saúde, deverá comprovar com atestado médico recente.', '#333333', '15')] );

        //$this->form->addFields([new TLabel('A família possui veículo:')], [$veiculo_aluno]);
        //$this->form->addFields( [new TFormSeparator('', '#333333', '18')] );
        $this->form->addFields([new TLabel('O aluno concluiu o Ensino Médio em Escola:', '#ff0000')], [$ensino_aluno]);
        //$this->form->addFields( [new TFormSeparator('É obrigatória a apresentação da cópia do certificado de conclusão do Ensino Médio.', '#333333', '15')] );
        
        $this->form->addFields( [new TFormSeparator('', '#333333', '18')] );

        // add button
        $add_info1 = new TButton('add_info1');
        $add_info1->setAction(new TAction(array($this, 'onInfo1')), 'Termos e condições');
        $add_info1->setImage('fa:info-circle');
        $this->form->addFields([$add_info1]);

        $this->form->addFields([new TLabel('Li e concordo com os Termos e condições.', '#ff0000')], [$checar]);

        $label5 = new TLabel('Anexar documentos', '#285097', 12, 'bi');
        $label5->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label5] );
        //$this->form->addFields( [new TLabel('- Adicione os seus documentos pessoais, comprovantes de renda e residência, entre outros.')]);

        $this->form->addFields( [new TLabel("<b>O aluno deverá anexar:</b>
    <br><b>- Documentos pessoais:</b> cópia do RG e CPF; cópia da certidão de nascimento ou casamento do requerente.<br>
    <b>- Comprovantes de Renda:</b> cópia do Imposto de Renda do requerente e do grupo familiar (quem declarar); cópia dos comprovantes de renda de todas as pessoas que residem na casa (se for profissional autônomo - Declaração de trabalhador autônomo).<br>
    <b>- Comprovantes de Residência:</b>    cópia dos Comprovantes de residência (Contas de água, luz, telefone...).<br>
    <b>- Outros:</b> cópia do certificado de conclusão do Ensino Médio; atestados médicos quando algum familar sofrer de enfermidade grave (obrigatório).")]);
        
        // add button
    /**    $add_info = new TButton('add_info');
        $add_info->setAction(new TAction(array($this, 'onInfo')), 'Orientações');
        $add_info->setImage('fa:question-circle');
        $this->form->addFields([new TLabel('')],[$add_info]);*/

        $this->form->addFields([new TLabel('Anexar documentos')], [$filename]);

        $label6 = new TLabel('Caso o aluno precise anexar mais algum documento posteriormente, o mesmo deverá <u>anexar todos os outros documentos novamente.</u>', '#FF0000', 12, 'bi');
        //$label6->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label6] );
        
        // create the form actions
        $this->form->addAction(('Finalizar'), new TAction(array($this, 'onSave')), 'far:save red');
        //$this->form->addAction(('Novo Requerimento'),  new TAction(array($this, 'onShow')), 'bs:plus-sign green');

        /**
        TPage::include_css('app/resources/styles.css');
        //$html1 = new THtmlRenderer('app/resources/requerimento.html');
        $html2 = new THtmlRenderer('app/resources/requerimento_confirmacao.html');

        //$html1->enableSection('main', array());
        $html2->enableSection('main', array());

        //$panel1 = new TPanelGroup('DETALHAMENTO');
        //$panel1->add($html1);       

        $panel2 = new TPanelGroup('INFORMAÇÕES ADICIONAIS AO CANDIDATO BOLSISTA:');
        $panel2->add($html2);*/

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        //$container->add(new TXMLBreadCrumb('menu.xml',  __CLASS__));
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReqBolsaAlunoDialogQuestionView'));
        $container->add($this->form);
        
        parent::add($container);
        //parent::add( TVBox::pack($panel2) );

    }

    /**
     * method onView()
     * Executed when the user clicks at the view button
     */
    function onInfo()
    {
        $data = $this->form->getData();

        $this->form->setData($data);
        
        $win = TWindow::create('Orientações para preenchimento', 0.5, 0.4);
        $win->add("<b>O ALUNO DEVERÁ ANEXAR:</b>
	<br><b>- Documentos pessoais:</b> cópia do RG e CPF; cópia da certidão de nascimento ou casamento do requerente.<br>
	<b>- Comprovantes de Renda:</b> cópia do Imposto de Renda do requerente e do grupo familiar (quem declarar); cópia dos comprovantes de renda de todas as pessoas que residem na casa (se for profissional autônomo - Declaração de trabalhador autônomo).<br>
	<b>- Comprovantes de Residência:</b>	cópia dos Comprovantes de residência (Contas de água, luz, telefone...).<br>
	<b>- Outros:</b> cópia do certificado de conclusão do Ensino Médio; atestados médicos quando algum familar sofrer de enfermidade grave (obrigatório).
	<br><b>Observação:</b> caso o aluno precise anexar mais algum documento posteriormente, o mesmo deverá anexar todos os outros documentos novamente.");
        $win->show();
    }

    /**
     * method onView()
     * Executed when the user clicks at the view button
     */
    function onInfo1()
    {
        $data = $this->form->getData();

        $this->form->setData($data);
        
        $win = TWindow::create('Termos e condições', 0.7, 0.7);
        $win->add("<b>01) A Bolsa de Estudos será CANCELADA quando:</b>
	<br>
	- O aluno ficar de dependência em 02 ou mais matérias;<br>
	- O aluno tiver número excessivo de faltas;<br>
	- O aluno não cumprir o Regimento Interno do Estabelecimento de Ensino;<br>
	- O aluno ficar inadimplemente com o pagamento das mensalidades;<br>
	- Da emissão de cheques sem provisão de fundos à Tesouraria da Fundação;<br>
	- Do trancamento, desistência, cancelamento e abandono de curso.<br>
	<br><b>02) Não será analisado o requerimento de pedido de bolsa de estudos se o aluno não apresentar toda a documentação exigida.<br>
	
	<br>03) O aluno bolsista poderá ser REQUISITADO PARA MONITORIA, ESTÁGIO NÃO REMUNERADO, PARTICIPAÇÃO EM EVENTOS DE DIVULGAÇÃO,
	feiras, e a prestar horas/atividades gratuitamente para a Fundação, seja na biblioteca, Hospital Veterinário, laboratórios e 
	nas atividades de pesquisa, sempre que requerido pelos professores, coordenadores e diretores sob pena de restrição do benefício pleiteado.</b><br>
	
    <br>
	Perante a COMISSÃO SOCIAL, solicito a  CONCESSÃO DE BOLSA DE ESTUDO em conformidade com as Normas vigentes da Fundação. 
	<br>- De acordo com o Plano de Atendimento da Fundação Educacional de Ituverava, <b>DECLARO</b>, sob as penas da lei que preecho os requisitos aceitando-os em todos os seus termos e que:
    <br>- Não sou beneficiário (a) de nenhum outro tipo de bolsa de estudo e/ou de qualquer outra forma de benefício com a mesma finalidade;
    <br>- Estou ciente de que poderei receber visitas domiciliares para comprovar a veracidade das informações e dos documentos por mim apresentados;
    <br>- As informações constantes neste requerimento são verdadeiras e que qualquer alteração nos dados fornecidos será comunicada imediatamente sob pena de cancelamento da bolsa, ciente
    que a prestação de informações falsas e indutivas constitui crime previsto na legislação;
    <br>- Não fui retido (a) no ciclo que estou requerendo a bolsa;
    <br>- Concedo autorização para que a Comissão de Bolsa de Estudos confirme os dados constantes neste Requerimento, por intermédio de Assistente Social contratado por esta Fundação.<br>
	Estou ciente dos requisitos para o deferimento da bolsa de estudos e do cancelamento da mesma. <br>
	<b>Estou ciente de que terei que refazer este requerimento de bolsa periodicamente, para reavaliação da situação socioeconômica.</b>");
        $win->show();
    }

    /**
     * method onView()
     * Executed when the user clicks at the view button
     */
    function onInfo2()
    {
        $data = $this->form->getData();

        $this->form->setData($data);
        
        $win = TWindow::create('Orientações para preenchimento', 0.6, 0.4);
        $win->add("Entende-se por grupo familar, o conjunto de pessoas ou parentes que compartilhem rendimentos comuns, incluindo o candidato, pai, padrasto, mãe, madrasta, cônjuge, companheiro(a), filho(a), enteado(a), irmão(a), avô(ó) ou demais pessoas que residam em comum.<br>
        	<br>Por rendimentos poderão ser considerados os valores brutos dos: salários, vencimentos, pró-labore, pensões, proventos, benefícios previdenciários, aluguéis, honorários profissionais e, no caso de autônomo ou empresário, aqueles	contantes da declaração de rendimento mensal fornecido por Contador (contendo o número de registro no C.R.C). Poderão também ser considerados os rendimentos não comprovados da realização de pequenos ou eventuais serviços prestados.<br>
        	<br>Não se entedem os benefícios a titulo de Bolsa - Auxilio recebidos em programas de estágio, que devem se somar ao total dos rendimentos do(a) candidato(a).");
        $win->show();
    }

    /**
     * Adiciona item ao familiar
     * @param $param Request
     */
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

            if (! $data->item_aluno_idade)
                throw new Exception('O campo Idade é obrigatório.');

            //if (! $data->item_aluno_profissao)
            //    throw new Exception('O campo Profissão é obrigatório.');

            //if (! $data->item_aluno_salario)
            //    throw new Exception('O campo Salário é obrigatório.');
            
            $item_aluno_items = TSession::getValue('item_aluno_items');
            $key = !empty($data->item_aluno_id) ? $data->item_aluno_id : uniqid();
            
            $fields = []; 
            $fields['item_aluno_item_membro'] = $data->item_aluno_item_membro;
            $fields['item_aluno_nome'] = $data->item_aluno_nome;
            $fields['item_aluno_idade'] = $data->item_aluno_idade;
            $fields['item_aluno_profissao']      = $data->item_aluno_profissao;
            $fields['item_aluno_salario']      = $data->item_aluno_salario;
            $fields['item_aluno_local_trabalho']      = $data->item_aluno_local_trabalho;

            $item_aluno_items[ $key ]        = $fields;
            
            TSession::setValue('item_aluno_items', $item_aluno_items);

            // limpa os campos do item do pedido
            $data->item_aluno_item_membro = '';
            $data->item_aluno_nome = '';
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

    /**
     * Recarrega tudo
     * @param $param Request
     */
    public function onReload($params = null)
    {
        $this->loaded = TRUE;
        $this->onReloadAlunoItemAluno($params);
        $this->onReloadAlunoDespesaAluno($params);
    }
    
    /**
     * Recarrega itens do familiar
     * @param $param Request
     */
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
                $button_edi->setImage('bs:edit');
                $button_edi->setFormName($this->form->getName());

                $rowItem->edit   = $button_edi;
                $rowItem->delete = $button_del;
                
                $rowItem->item_aluno_item_membro = isset($item['item_aluno_item_membro']) ? $item['item_aluno_item_membro'] : '';
                $rowItem->item_aluno_nome = isset($item['item_aluno_nome']) ? $item['item_aluno_nome'] : '';
                $rowItem->item_aluno_idade = isset($item['item_aluno_idade']) ? $item['item_aluno_idade'] : '';
                
                $rowItem->item_aluno_profissao      = isset($item['item_aluno_profissao']) ? $item['item_aluno_profissao'] : '';
                $rowItem->item_aluno_salario = isset($item['item_aluno_salario']) ? $item['item_aluno_salario'] : '';
                $rowItem->item_aluno_local_trabalho = isset($item['item_aluno_local_trabalho']) ? $item['item_aluno_local_trabalho'] : '';

                $this->item_aluno_list->addItem($rowItem);
                $cont ++;
            } 
        } 
    }


    /**
     * Edita item do familiar
     * @param $param Request
     */
    public function onEditItemAluno( $param )
    {
        $data = $this->form->getData();

        // read session items
        $items = TSession::getValue('item_aluno_items');

        // get the session item
        $item = $items[$param['item_aluno_id_row_id']];

        $data->item_aluno_item_membro = $item['item_aluno_item_membro'];
        $data->item_aluno_nome = $item['item_aluno_nome'];
        $data->item_aluno_idade = $item['item_aluno_idade'];
        $data->item_aluno_profissao = $item['item_aluno_profissao'];
        $data->item_aluno_salario      = $item['item_aluno_salario'];
        $data->item_aluno_local_trabalho      = $item['item_aluno_local_trabalho'];
        $data->item_aluno_id         = $param['item_aluno_id_row_id'];
        
        // fill product fields
        $this->form->setData( $data );

        $this->onReload( $param );
    }

    /**
     * Exclui item da despesa
     * @param $param Request
     */
    public function onDeleteItemAluno( $param )
    {
        $data = $this->form->getData();

        $data->item_aluno_item_membro = '';
        $data->item_aluno_nome = '';
        $data->item_aluno_idade = '';
        $data->item_aluno_profissao = '';
        $data->item_aluno_salario      = '';
        $data->item_aluno_local_trabalho      = '';
        $this->form->setData( $data );

        // read session items
        $items = TSession::getValue('item_aluno_items');

        // delete the item from session
        unset($items[$param['item_aluno_id_row_id']]);
        TSession::setValue('item_aluno_items', $items);
        
        $this->onReload( $param );
    }

    //here
    /**
     * Adiciona item ao familiar
     * @param $param Request
     */
    public function onAddItemDespesa( $param )
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
    }

    /**
     * Recarrega itens da despesa
     * @param $param Request
     */
    public function onReloadAlunoDespesaAluno( $param )
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
                $button_edi_despesa->setImage('bs:edit');
                $button_edi_despesa->setFormName($this->form->getName());

                $rowDespesa->edit   = $button_edi_despesa;
                $rowDespesa->delete = $button_del_despesa;
                
                $rowDespesa->item_despesa_item_tipo = isset($item['item_despesa_item_tipo']) ? $item['item_despesa_item_tipo'] : '';
                $rowDespesa->item_despesa_valor = isset($item['item_despesa_valor']) ? $item['item_despesa_valor'] : '';
               
                $this->item_despesa_list->addItem($rowDespesa);
                $cont ++;
            } 
        } 
    }

    /**
     * Edita item da despesa
     * @param $param Request
     */
    public function onEditDespesaAluno( $param )
    {
        $data = $this->form->getData();

        // read session items
        $items = TSession::getValue('item_despesa_items');

        // get the session item
        $item = $items[$param['item_despesa_id_row_id']];

        $data->item_despesa_item_tipo = $item['item_despesa_item_tipo'];
        $data->item_despesa_valor = $item['item_despesa_valor'];
        $data->item_despesa_id         = $param['item_despesa_id_row_id'];
        
        // fill product fields
        $this->form->setData( $data );

        $this->onReload( $param );
    }

    /**
     * Exclui item da despesa
     * @param $param Request
     */
    public function onDeleteDespesaAluno( $param )
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
    }


    /**
     * Limpa formulário
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear();
        TSession::setValue('item_aluno_items', null);
        TSession::setValue('item_despesa_items', null);
        $this->onReload();
    }

    public static function onComplete($param)
    {
        new TMessage('info', 'Arquivo enviado com sucesso: '.$param['filename']);
        
        // refresh photo_frame
        TScript::create("$('#filename').html('')");
        TScript::create("$('#filename').append(\"<img style='width:100%' src='tmp/{$param['filename']}'>\");");
    }

    /**
     * Salva aluno
     * @param $param Request
     */
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
            $object->system_user_id = TSession:: getValue ('userid');//pega o usuário que esta logado
            $object->situacao = "Aberto";
            $object->unidade = TSession::getValue('userunitid');

            //check
            //$object-> saude_familiar = serialize($data->saude_familiar);
            $object->saude_familiar = implode(',', $object->saude_familiar);
            $object->saude_aluno = implode(',', $object->saude_aluno);
            $object->saude_aluno_neces = implode(',', $object->saude_aluno_neces);
            $object->checar = implode(',', $object->checar);
            //$object-> saude_aluno = serialize($data->saude_aluno);

            if($data->id)
            {   
                        
                if($data->filename )
                {
                        
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
                    }

                }
            }
            else //quando é um novo registro
            {
                $zip = new ZipArchive();
                $today = date("Ymd");
                $nomeArquivo = "arquivos/"."req_bolsa"."_$today_".time().'.zip';
                $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                
                foreach ($data-> filename as $arq)
                {
                    $source_file   = 'tmp/'.$arq;
                    
                    if (file_exists($source_file))
                    {

                        $zip->addFile(  'tmp/'.$arq , "$arq" );
                        
                    }
                }
                $zip->close();

                $object->filename = $nomeArquivo;
            }
            /**
            if(isset($data->filename)){

            $zip = new ZipArchive();
            $today = date("Ymd");
            $nomeArquivo = "arquivos/"."arquivo"."_$today_".time().'.zip';
            $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
            
            foreach ($data-> filename as $arq)
            {
                $source_file   = 'tmp/'.$arq;
                
                if (file_exists($source_file))
                {

                    $zip->addFile(  'tmp/'.$arq , "$arq" );
                    
                }
            }
            $zip->close();

            $object->filename = $nomeArquivo;
            }*/

            $object->store(); 
            
            $this->storeItems('ReqBolsaAlunoItem', 'req_bolsa_aluno_id', $object, 'item_aluno',
                function($masterObject, $detailObject) { 
                	$masterObject->renda_familiar += ($detailObject->salario);
                	$masterObject->n_pessoa += count($detailObject->idade);                
            });

            $this->storeItems('ReqBolsaAlunoDespesa', 'req_bolsa_aluno_id', $object, 'item_despesa',
                function($masterObject, $detailObject) { 
                    
            });

            $object->renda_percapita += ($object->renda_familiar / $object->n_pessoa);
            $object->rf_salario_minimo += round($object->renda_familiar / 954, 2);
            $object->rp_salario_minimo += round($object->renda_percapita / 954, 2);

            $object->store();
            $data->id = $object->id; 
            $this->form->setData($data);
            TTransaction::close();

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('ReqBolsaAlunoList', 'onReload');

            //email gestor
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Aluno - FEAcadêmico');
            $mail->setSubject('Requerimento de Bolsa');
            $mail->setTextBody('Prezado(a) Assistente Social, existe um novo Requerimento de Bolsa para sua avaliação! Entre no Sistema FEAcadêmico para analisar.');  
            
            $mail->addAddress('elianebarbosa@feituverava.com.br');//email da assitente social 
              
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();

            
            $notif = '24';
            SystemNotification::register(
                                        $notif,
                                        'Novo Requerimento de Bolsa recebido',
                                        'Um novo requerimento de bolsa foi recebido e aguarda sua análise.',
                                        'class=ReqBolsaAlunoListGestor',
                                        'Ver Requerimento',
                                        'far fa-list-alt green'
                                        );

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
                        $mail->addAddress(trim($email), $logged-> name);
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

    /**
     * Edita formulário
     * @param $param Request
     */

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
                $object-> system_user_id = $logado-> id;
               
                $testaid=$logado->id;

                $object = new ReqBolsaAluno($key);
                $system_user_id=new stdClass;
                //$calendar_local_id=new stdClass;
                $system_user_id->system_user_id = $object->system_user_id;
                //$this->form->onClear( $param );
                $this->onClear( $param );
                //$calendar_local_id->calendar_local_id = $object->calendar_local_id;
                
              
                TTransaction::close();
                
                $verificaid=$system_user_id->system_user_id;  //id do usuario que criou o evento
                //if($verificaid==$testaid AND $object->situacao=='Solicitar alteração'){
                if($verificaid==$testaid){

                TTransaction::open('Felabs_DB');

                $object = new ReqBolsaAluno($key);

                //teste
                $this->loadItems('ReqBolsaAlunoItem', 'req_bolsa_aluno_id', $object, 'item_aluno');
                $this->loadItems('ReqBolsaAlunoDespesa', 'req_bolsa_aluno_id', $object, 'item_despesa');
                //check
                //$object->saude_familiar=unserialize($object->saude_familiar);
                $object->saude_familiar = explode(',', $object->saude_familiar);
                $object->saude_aluno = explode(',', $object->saude_aluno);
                $object->saude_aluno_neces = explode(',', $object->saude_aluno_neces);
                $object->checar = explode(',', $object->checar);
                //$object->saude_aluno=unserialize($object->saude_aluno);
                $this->form->setData($object); 
                $this->onReload();
                TTransaction::close(); }
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
    
    /**
     * Exibe a página
     * @param $param Request
     */
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
        TSession::setValue('item_despesa_items', null);
        $this->onReload();
    }
} 


