<?php

class ReqBolsaUpdateAlunoForm extends TPage
{
    protected $form;
    
    use adianti\base\AdiantiMasterDetailTrait;


    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('list_Requerimento');
        $this->form->setFormTitle('Requerimento de Bolsa de Estudo');
        
        
        // master fields
        $id = new TEntry('id');
        $system_user_id = new THidden('system_user_id');
        $nome = new TEntry('nome');
        $curso = new TEntry('curso');
        $ciclo = new TEntry('ciclo');
        $periodo = new TEntry('periodo');
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
        $checar = new TRadioGroup('checar');
        //$filename = new TMultiFile('filename');
        $obs = new TText('obs');
        $obs_ass_social = new TText('obs_ass_social');
        $data_final = new TDate('data_final');
        $situacao = new TCombo('situacao');
        $filename = new TButton('filename');
        //$cad_unico = new TEntry('cad_unico');
        $outra_graduacao = new TRadioGroup('outra_graduacao');
        $graduacao_anterior = new TEntry('graduacao_anterior');
        $renda_familiar_apurada = new TNumeric('renda_familiar_apurada', '2', ',', '.' );
        $n_pessoas_apurado = new TEntry('n_pessoas_apurado');
        

        $filename->setImage('fas:cloud-download-alt');
        $filename->setAction(new TAction(array($this, 'onDownloadMaster')), 'Download');


        //$filename->setCompleteAction(new TAction(array($this, 'onComplete')));
        //$filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );


        $cpf->setMask('999.999.999-99');
        $data_nascimento->setMask('99/99/9999');
        $cep->setMask('99.999-999');
        $telefone->setMask('(99)9999-9999');
        $celular->setMask('(99)99999-9999');
        $telefone_trabalho->setMask('(99)9999-9999');


        $item_checar = array();
        $item_checar['Sim'] ='Sim';

        $checar->addItems($item_checar);


        $itens_situacao = array();
        $itens_situacao['Aberto'] = 'Aberto';
        $itens_situacao['Em análise'] = 'Em análise';
        $itens_situacao['Aguardando assinaturas'] = 'Aguardando assinaturas';
        $itens_situacao['Solicitar alteração'] = 'Solicitar alteração';
        $itens_situacao['Deferido'] = 'Deferido';
        $itens_situacao['Indeferido'] = 'Indeferido';
        $itens_situacao['Indevido'] = 'Indevido';
        $itens_situacao['Desclassificado'] = 'Desclassificado';
        
        $situacao->addItems($itens_situacao);


        $radio1 = array();
        $radio1['Própria'] ='Própria';
        $radio1['Alugada'] ='Alugada';
        $radio1['Financiada'] ='Financiada';
        $radio1['Cedida'] ='Cedida';
        
        $moradia->setLayout('horizontal');
        $moradia->addItems($radio1);
        
        
        $radio2 = array();
        $radio2['Família'] ='Com a família';
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
       

        /* detail fields despesa - Retirado do formulário a partir de 13/04/2022
        $item_despesa_id = new THidden('item_despesa_id');
        $item_despesa_item_tipo = new TCombo('item_despesa_item_tipo');
        $item_despesa_valor = new TNumeric('item_despesa_valor', '2', ',', '.' );*/


        $id->setEditable(false);
        $id->setSize(100);
        $nome->setSize('100%');
        $data_nascimento->setSize('100%');
        $rg->setSize('100%');
        $cpf->setSize('100%');
        $estado_civil->setSize('100%');
        $profissao->setSize('100%');
        $endereco->setSize('100%');
        $endereco_numero->setSize('50%');
        $bairro->setSize('100%');
        $endereco_complemento->setSize('50%');
        $cidade->setSize('250');
        $estado->setSize('100%');
        $cep->setSize('100%');
        $telefone->setSize('100%');
        $celular->setSize('100%');
        $telefone_trabalho->setSize('100%');
        $email->setSize('250');
        $curso->setSize('250');
        $ciclo->setSize('100%');     
        $periodo->setSize('100%');
        //$cad_unico->setSize('250');
        //$item_despesa_valor->setSize('50%');
        $filename->setSize('38%');
        $obs->setSize('100%', 170);
        $situacao->setSize('80%');
        $data_final->setSize(120);
        $data_final->setMask('dd/mm/yyyy');
        $data_final->setDatabaseMask('yyyy-mm-dd');
        $renda_familiar_apurada->setSize('100%');
        $n_pessoas_apurado->setMask('9!');
        $n_pessoas_apurado->setSize('100%');


        $nome->setEditable(FALSE);
        $rg->setEditable(FALSE);
        $cpf->setEditable(FALSE);
        $data_nascimento->setEditable(FALSE);
        $estado_civil->setEditable(FALSE);
        $profissao->setEditable(FALSE);
        $endereco->setEditable(FALSE);
        $endereco_numero->setEditable(FALSE);
        $bairro->setEditable(FALSE);
        $endereco_complemento->setEditable(FALSE);
        $cidade->setEditable(FALSE);
        $estado->setEditable(FALSE);
        $cep->setEditable(FALSE);
        $telefone->setEditable(FALSE);
        $celular->setEditable(FALSE);
        $telefone_trabalho->setEditable(FALSE);
        $email->setEditable(FALSE);
        $curso->setEditable(FALSE);
        $ciclo->setEditable(FALSE);
        $periodo->setEditable(FALSE);
        //$cad_unico->setEditable(FALSE);
        //$item_despesa_item_tipo->setEditable(FALSE);
        //$item_despesa_valor->setEditable(FALSE);
        $moradia->setEditable(FALSE);
        $moradia_aluno->setEditable(FALSE);
        $veiculo_aluno->setEditable(FALSE);
        $ensino_aluno->setEditable(FALSE);
        $saude_familiar->setEditable(FALSE);
        $saude_aluno->setEditable(FALSE);
        $saude_aluno_neces->setEditable(FALSE);
        $outra_graduacao->setEditable(FALSE);
        $graduacao_anterior->setEditable(FALSE);


        // master fields
        $label1 = new TLabel('Dados pessoais do aluno', '#285097', 12, 'b');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [ $label1 ] );
        $this->form->addFields( [ '<br>' ] );
        $this->form->addFields( [ new TLabel('ID:') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Nome do Aluno(a):') ], [ $nome ], [ new TLabel('Data de nascimento:') ], [ $data_nascimento ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ new TLabel('RG:') ],[ $rg ], [ new TLabel('CPF:') ], [ $cpf ] );
        $this->form->addFields( [ new TLabel('Estado civil:') ], [ $estado_civil ], [ new TLabel('Profissão:') ], [ $profissao ] );
        $this->form->addFields( [ new TLabel('Endereço:') ], [ $endereco ], [ new TLabel('Nº:') ], [ $endereco_numero ] );
        $this->form->addFields( [ new TLabel('Bairro:') ], [ $bairro ], [ new TLabel('Complemento:')], [ $endereco_complemento ] );
        $this->form->addFields( [ new TLabel('Cidade:') ], [ $cidade ], [ new TLabel('Estado:') ], [ $estado ], [ new TLabel('CEP:') ], [ $cep ] );
        $this->form->addFields( [ new TLabel('Telefone:') ], [ $telefone ], [ new TLabel('Celular:') ], [ $celular ], [ new TLabel('Telefone (trabalho):') ], [ $telefone_trabalho ] );
        $this->form->addFields( [ new TLabel('Email:') ], [ $email ], [] );
        $this->form->addFields( [ new TLabel('Curso:') ], [ $curso ], [ new TLabel('Ciclo:') ], [ $ciclo ], [ new TLabel('Período:') ], [ $periodo ] );        
        //$this->form->addFields([new TLabel('CadÚnico:')],[$cad_unico], [new TLabel('')]);
        $this->form->addFields( [ '<br>' ] );
  

        // detail fields
        $label2 = new TLabel('Descrição do grupo familiar', '#285097', 12, 'b');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );
        

        // detail datagrid
        $this->item_aluno_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_aluno_list->style = 'width:100%';
        $this->item_aluno_list->class .= ' table-bordered';
        $this->item_aluno_list->disableDefaultClick();
        //$this->item_aluno_list->addQuickColumn('', 'edit', 'left', 50);
        //$this->item_aluno_list->addQuickColumn('', 'delete', 'left', 50);

        $col_item_membro = $this->item_aluno_list->addQuickColumn('Membro', 'item_aluno_item_membro', 'left');
        $col_nome = $this->item_aluno_list->addQuickColumn('Nome', 'item_aluno_nome', 'left');
        $col_rg = $this->item_aluno_list->addQuickColumn('RG', 'item_aluno_rg', 'left');
        $col_cpf = $this->item_aluno_list->addQuickColumn('CPF', 'item_aluno_cpf', 'left');
        $col_idade = $this->item_aluno_list->addQuickColumn('Idade', 'item_aluno_idade', 'left');
        $col_profissao = $this->item_aluno_list->addQuickColumn('Profissão', 'item_aluno_profissao', 'left');
        $col_salario = $this->item_aluno_list->addQuickColumn('Salário', 'item_aluno_salario', 'left');
        $col_local_trabalho = $this->item_aluno_list->addQuickColumn('Local de trabalho', 'item_aluno_local_trabalho', 'left');


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


        /* detail fields despesa - Retirado do formulário a partir de 13/04/2022
        $label3 = new TLabel('Despesas do grupo familiar', '#285097', 12, 'b');
        $label3->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addFields( [ '<br>' ] );
        $this->form->addContent( [$label3] );
        
        
        // detail datagrid
        $this->item_despesa_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_despesa_list->style = 'width:100%';
        $this->item_despesa_list->class .= ' table-bordered';
        $this->item_despesa_list->disableDefaultClick();
        //$this->item_despesa_list->addQuickColumn('', 'edit', 'left', 50);
        //$this->item_despesa_list->addQuickColumn('', 'delete', 'left', 50);


        $col_item_tipo = $this->item_despesa_list->addQuickColumn('Descrição', 'item_despesa_item_tipo', 'left');
        $col_valor = $this->item_despesa_list->addQuickColumn('Valor', 'item_despesa_valor', 'left');


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

        
        $this->form->addContent([$this->item_despesa_list]);*/


        $label4 = new TLabel('Especificações', '#285097', 12, 'b');
        $label4->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addFields( [ '<br>' ] );
        $this->form->addContent( [$label4] );


        $this->form->addFields([new TLabel('A família reside em moradia:')], [$moradia], [new TLabel('O aluno reside em:')], [$moradia_aluno]);
        $this->form->addFields([new TLabel('Saúde das pessoas do grupo familiar que residem juntas:')], [$saude_familiar], [new TLabel('Saúde do candidato:')], [$saude_aluno]);
        $this->form->addFields([new TLabel('Candidato portador de necessidade especial:')], [$saude_aluno_neces],[new TLabel('A família possui veículo:')], [$veiculo_aluno]);        
        $this->form->addFields( [ new TLabel('O aluno concluiu o Ensino Médio em Escola:') ], [ $ensino_aluno ], [ new TLabel('O aluno possui outra graduação em Ensino Superior:') ], [ $outra_graduacao ] );
        $this->form->addFields( [ ], [ ], [ new TLabel('Se sim, qual:') ], [ $graduacao_anterior ] );

        $this->form->addFields( [ '<hr>' ] );
        $this->form->addFields(  [ new TLabel('Documento(s) anexo(s):') ], [ $filename ], [ new TLabel('') ] );
        $this->form->addFields( [ '<hr>' ] );
        $this->form->addFields( [ '<br>' ] );
        
        
        $label5 = new TLabel('Parecer Técnico do(a) Assistente Social', '#ff0000', 12, 'b');
        $label5->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label5] );
        
        $row = $this->form->addFields( [],
                                       [ new TLabel('Situação:', '#ff0000'), $situacao ],
                                       [ new TLabel('Renda familiar apurada:', '#ff0000'), $renda_familiar_apurada ],
                                       [ new TLabel('Nº de pessoas apurado:', '#ff0000'), $n_pessoas_apurado ],
                                       [ new TLabel('Data Final:', '#ff0000'), $data_final] );
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-3', 'col-sm-2', 'col-sm-2'];

        
        $this->form->addFields([new TLabel('Observações:', '#ff0000')], [$obs]);
        $this->form->addFields([new TLabel('Parecer: (visível apenas para a Assistente Social)', '#ff0000')], [$obs_ass_social]);
        
        
        // create the form actions
        $this->form->addAction(('Salvar'), new TAction(array($this, 'onSave')), 'far:save red');
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        //$container->add(new TXMLBreadCrumb('menu.xml',  __CLASS__));
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReqBolsaAlunoListGestor'));
        $container->add($this->form);
        
        parent::add($container);
        parent::add( TVBox::pack($panel2) );

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

            if (! $data->item_aluno_idade)
                throw new Exception('O campo Idade é obrigatório.');

            if (! $data->item_aluno_profissao)
                throw new Exception('O campo Profissão é obrigatório.');

            if (! $data->item_aluno_salario)
                throw new Exception('O campo Salário é obrigatório.');
            
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
                $button_edi->setImage('bs:edit');
                $button_edi->setFormName($this->form->getName());

                $rowItem->edit   = $button_edi;
                $rowItem->delete = $button_del;
                
                $rowItem->item_aluno_item_membro = isset($item['item_aluno_item_membro']) ? $item['item_aluno_item_membro'] : '';
                $rowItem->item_aluno_nome = isset($item['item_aluno_nome']) ? $item['item_aluno_nome'] : '';
                $rowItem->item_aluno_rg = isset($item['item_aluno_rg']) ? $item['item_aluno_rg'] : '';
                $rowItem->item_aluno_cpf = isset($item['item_aluno_cpf']) ? $item['item_aluno_cpf'] : '';
                $rowItem->item_aluno_idade = isset($item['item_aluno_idade']) ? $item['item_aluno_idade'] : '';                
                $rowItem->item_aluno_profissao      = isset($item['item_aluno_profissao']) ? $item['item_aluno_profissao'] : '';
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
    

    public function onDownloadMaster($param)
    {
        try
        {                      
                $id = $param['id'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new ReqBolsaAluno($id);
                
                TTransaction::close();

                if(!empty($object-> filename))
                {              
                    if (strtolower(substr($object->filename, -4)) == 'html')
                    {
                        $win = TWindow::create( $object->filename, 0.8, 0.8 );
                        $win->add( file_get_contents( "arquivos/".$object->filename ) );
                        $win->show();

                    }
                    else
                    {
                        TPage::openFile($object->filename);
                    }
                    
                    $this->form->setData( $this->form->getData() );
                    TTransaction::rollback();
                }
                else
                {
                    new TMessage('info', 'Este requerimento não possui anexos'); 
                }
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $prefs  = SystemPreference::getAllPreferences();

            $this->form->validate();
            $data = $this->form->getData();
            
            $object = new ReqBolsaAluno; 
            $object->fromArray( (array) $data);

            //teste email
            $usuario = $object->system_user_id;
            $usuarios = new SystemUser($usuario, FALSE);
            $emailusuario = $usuarios->email;

            $object->saude_familiar = implode(',', $object->saude_familiar);
            $object->saude_aluno = implode(',', $object->saude_aluno);
            $object->saude_aluno_neces = implode(',', $object->saude_aluno_neces);


            $object->store(); 
            
            $this->storeItems('ReqBolsaAlunoItem', 'req_bolsa_aluno_id', $object, 'item_aluno',
                function($masterObject, $detailObject) { 
                    
            });

            /*$this->storeItems('ReqBolsaAlunoDespesa', 'req_bolsa_aluno_id', $object, 'item_despesa',
                function($masterObject, $detailObject) { 
                    
            });*/

            
            $object->renda_percapita_apurada += ($object->renda_familiar_apurada / $object->n_pessoas_apurado);
            $object->rf_salario_minimo_apurada += round($object->renda_familiar_apurada / 1212, 2);
            $object->rp_salario_minimo_apurada += round($object->renda_percapita_apurada / 1212, 2);
            
            
            $object->store();
            
            $data->id = $object->id; 
            $this->form->setData($data);
            
            TTransaction::close();

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            
            //email aluno
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Aluno - FEAcadêmico');
            $mail->setSubject('Requerimento de Bolsa');
            $mail->setTextBody('Prezado(a) aluno(a), seu Requerimento de Bolsa foi avaliado e a situação foi alterada. Acompanhe a situação através da Área do Aluno - FEAcadêmico.'."\n". 'Esta é uma mensagem automática. Solicitamos, por favor, não responder este e-mail.');  

            $mail->addAddress($emailusuario);          
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();

            $id_notif = $usuario;

            SystemNotification::register(
                                        $id_notif,
                                        'Novo status de Requerimento de Bolsa definido',
                                        'Um novo estado foi definido para seu requerimento de bolsa, verifique.',
                                        'class=ReqBolsaAlunoList',
                                        'Ver Requerimento',
                                        'far fa-list-alt green'
                                        );

        
            TApplication::loadPage('ReqBolsaAlunoListGestor', 'onReload');
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
                
                $object = new ReqBolsaAluno($key); 
                
                $this->loadItems('ReqBolsaAlunoItem', 'req_bolsa_aluno_id', $object, 'item_aluno');
                //$this->loadItems('ReqBolsaAlunoDespesa', 'req_bolsa_aluno_id', $object, 'item_despesa');
                
                $object->saude_familiar = explode(',', $object->saude_familiar);
                $object->saude_aluno = explode(',', $object->saude_aluno);
                $object->saude_aluno_neces = explode(',', $object->saude_aluno_neces);
                
                $this->form->setData($object); 
                $this->onReload();
                
                TTransaction::close(); 
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
} 


