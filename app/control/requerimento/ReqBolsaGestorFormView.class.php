<?php

class ReqBolsaGestorFormView extends TPage
{
    protected $form;
    protected $aluno_item_list;
    protected $subnotebook;

    public function __construct($param)
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');

        // Configuração do Formulário
        $this->form = new BootstrapFormBuilder('form_Aluno');
        $this->form->setFormTitle('Requerimento de Bolsa de Estudo');

        // Configurações nulas de cor para herdar os estilos do tema do sistema
        $textColor  = null; 
        $fontSize   = '15px';

        // -------------------------------------------------------------------------
        // LABELS (RÓTULOS)
        // -------------------------------------------------------------------------
        $label_id        = new TLabel('ID:', $textColor, $fontSize, '');
        $label_nome      = new TLabel('Nome:', $textColor, $fontSize, '');
        $label_rg        = new TLabel('RG:', $textColor, $fontSize, '');
        $label_cpf       = new TLabel('CPF:', $textColor, $fontSize, '');
        $label_nasc      = new TLabel('Data de nascimento:', $textColor, $fontSize, '');
        $label_est_civil = new TLabel('Estado civil:', $textColor, $fontSize, '');
        $label_profissao = new TLabel('Profissão:', $textColor, $fontSize, '');
        $label_endereco  = new TLabel('Endereço:', $textColor, $fontSize, '');
        $label_num       = new TLabel('Nº:', $textColor, $fontSize, '');
        $label_bairro    = new TLabel('Bairro:', $textColor, $fontSize, '');
        $label_complem   = new TLabel('Complemento:', $textColor, $fontSize, '');
        $label_cidade    = new TLabel('Cidade:', $textColor, $fontSize, '');
        $label_estado    = new TLabel('Estado:', $textColor, $fontSize, '');
        $label_cep       = new TLabel('CEP:', $textColor, $fontSize, '');
        $label_telefone  = new TLabel('Telefone:', $textColor, $fontSize, '');
        $label_celular   = new TLabel('Celular:', $textColor, $fontSize, '');
        $label_tel_trab  = new TLabel('Telefone (trabalho):', $textColor, $fontSize, '');
        $label_email     = new TLabel('Email:', $textColor, $fontSize, '');
        $label_curso     = new TLabel('Curso:', $textColor, $fontSize, '');
        $label_ciclo     = new TLabel('Ciclo:', $textColor, $fontSize, '');
        $label_periodo   = new TLabel('Período:', $textColor, $fontSize, '');
        
        $label_moradia   = new TLabel('A família reside em moradia:', $textColor, $fontSize, '');
        $label_reside_em = new TLabel('O aluno reside em:', $textColor, $fontSize, '');
        $label_veiculo   = new TLabel('A família possui veículo:', $textColor, $fontSize, '');
        $label_escola    = new TLabel('(Se aluno da Educação Básica) No ano anterior estudou em escola:<br><br>(Se aluno da Educação Superior) Concluiu o ensino médio em escola:', $textColor, $fontSize, '');
        $label_grad_sup  = new TLabel('O aluno possui outra graduação em Ensino Superior:', $textColor, $fontSize, '');
        $label_qual_grad = new TLabel('Se sim, qual:', $textColor, $fontSize, '');

        // Carrega os dados do Aluno
        $aluno = new ReqBolsaAluno($param['key']);

        // Seção: Dados Pessoais (Utilizando classe nativa Bootstrap para o título se adaptar)
        $titulo_pessoais = new TLabel('Dados pessoais do aluno', null, 14, 'b');
        $titulo_pessoais->class = 'text-primary'; 
        $this->form->addContent([$titulo_pessoais]);
        
        // -------------------------------------------------------------------------
        // TEXT DISPLAYS (EXIBIÇÃO DOS DADOS)
        // -------------------------------------------------------------------------
        $text_id        = new TTextDisplay($aluno->id, $textColor, $fontSize, '');          
        $text_nome      = new TTextDisplay($aluno->nome, $textColor, $fontSize, '');
        $text_rg        = new TTextDisplay($aluno->rg, $textColor, $fontSize, '');
        $text_cpf       = new TTextDisplay($aluno->cpf, $textColor, $fontSize, '');
        $text_nasc      = new TTextDisplay($aluno->data_nascimento, $textColor, $fontSize, '');
        $text_est_civil = new TTextDisplay($aluno->estado_civil, $textColor, $fontSize, '');
        $text_profissao = new TTextDisplay($aluno->profissao, $textColor, $fontSize, '');
        $text_endereco  = new TTextDisplay($aluno->endereco, $textColor, $fontSize, '');
        $text_num       = new TTextDisplay($aluno->endereco_numero, $textColor, $fontSize, '');
        $text_bairro    = new TTextDisplay($aluno->bairro, $textColor, $fontSize, '');
        $text_complem   = new TTextDisplay($aluno->endereco_complemento, $textColor, $fontSize, '');
        $text_cidade    = new TTextDisplay($aluno->cidade, $textColor, $fontSize, '');
        $text_estado    = new TTextDisplay($aluno->estado, $textColor, $fontSize, '');
        $text_cep       = new TTextDisplay($aluno->cep, $textColor, $fontSize, '');
        $text_telefone  = new TTextDisplay($aluno->telefone, $textColor, $fontSize, '');
        $text_celular   = new TTextDisplay($aluno->celular, $textColor, $fontSize, '');
        $text_tel_trab  = new TTextDisplay($aluno->telefone_trabalho, $textColor, $fontSize, '');
        $text_email     = new TTextDisplay($aluno->email, $textColor, $fontSize, '');
        $text_curso     = new TTextDisplay($aluno->curso, $textColor, $fontSize, '');
        $text_ciclo     = new TTextDisplay($aluno->ciclo, $textColor, $fontSize, '');
        $text_periodo   = new TTextDisplay($aluno->periodo, $textColor, $fontSize, '');
        
        $text_moradia   = new TTextDisplay($aluno->moradia, $textColor, $fontSize, '');
        $text_reside_em = new TTextDisplay($aluno->moradia_aluno, $textColor, $fontSize, '');
        $text_veiculo   = new TTextDisplay($aluno->veiculo_aluno, $textColor, $fontSize, '');
        $text_escola    = new TTextDisplay($aluno->ensino_aluno, $textColor, $fontSize, '');
        $text_grad_sup  = new TTextDisplay($aluno->outra_graduacao, $textColor, $fontSize, '');
        $text_qual_grad = new TTextDisplay($aluno->graduacao_anterior, $textColor, $fontSize, '');

        // Seção: Especificações
        $titulo_especificacoes = new TLabel('<br>Especificações', null, 14, 'b');
        $titulo_especificacoes->class = 'text-primary';

        // Montagem do Grid do Formulário - Dados Pessoais
        $this->form->addFields([$label_nome], [$text_nome], [$label_rg], [$text_rg], [$label_cpf], [$text_cpf]);
        $this->form->addFields([$label_nasc], [$text_nasc], [$label_est_civil], [$text_est_civil], [$label_profissao], [$text_profissao]);
        $this->form->addFields([$label_endereco], [$text_endereco], [$label_num], [$text_num], [$label_bairro], [$text_bairro]);
        $this->form->addFields([$label_complem], [$text_complem], [$label_cidade], [$text_cidade], [$label_estado], [$text_estado]);
        $this->form->addFields([$label_cep], [$text_cep], [$label_telefone], [$text_telefone], [$label_celular], [$text_celular]);
        $this->form->addFields([$label_tel_trab], [$text_tel_trab], [$label_email], [$text_email], [''], ['']);
        $this->form->addFields([$label_curso], [$text_curso], [$label_ciclo], [$text_ciclo], [$label_periodo], [$text_periodo]);
        
        // Inclusão da Seção Especificações
        $this->form->addContent([$titulo_especificacoes]);
        $this->form->addFields([$label_moradia], [$text_moradia], [$label_reside_em], [$text_reside_em], [$label_veiculo], [$text_veiculo]);
        $this->form->addFields([$label_escola], [$text_escola], [$label_grad_sup], [$text_grad_sup], [$label_qual_grad], [$text_qual_grad]);
        $this->form->addFields(['<br>']);

        // -------------------------------------------------------------------------
        // GRID: GRUPO FAMILIAR
        // -------------------------------------------------------------------------
        $this->aluno_item_list = new TQuickGrid;
        $this->aluno_item_list->style = 'width:100%';
        $this->aluno_item_list->disableDefaultClick();
        
        $this->aluno_item_list->addQuickColumn('Membro', 'item_membro', 'left');
        $this->aluno_item_list->addQuickColumn('Nome', 'nome', 'left');
        $this->aluno_item_list->addQuickColumn('RG', 'rg', 'left');
        $this->aluno_item_list->addQuickColumn('CPF', 'cpf', 'left');
        $this->aluno_item_list->addQuickColumn('Idade', 'idade', 'left');
        $this->aluno_item_list->addQuickColumn('Profissão', 'profissao', 'left');
        $this->aluno_item_list->addQuickColumn('Salário', 'salario', 'left');
        $this->aluno_item_list->addQuickColumn('Local de trabalho', 'local_trabalho', 'left');

        // Coluna de Totalização do Salário
        $column_total = $this->aluno_item_list->addQuickColumn('Total', '=( {salario} )', 'right');
        
        $column_total->setTotalFunction(function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_total->setTransformer(function($value, $object, $row) {
            $value = $value ? $value : 0;
            return "R$ " . number_format($value, 2, ",", ".");
        });
        
        $this->aluno_item_list->createModel();
        
        // Carrega itens do grupo familiar
        $items = ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $aluno->id)->load();
        $this->aluno_item_list->addItems($items);
        
        // Painel para encapsular a Grid (Fundo padrão do painel ajustado)
        $panel = new TPanelGroup('Descrição do grupo familiar', null);
        $panel->add(new BootstrapDatagridWrapper($this->aluno_item_list));
        $this->form->addContent([$panel]);

        // Ações de Cabeçalho do Form
        $this->form->addHeaderAction('Voltar', new TAction(['ReqBolsaAlunoListGestor', 'onReload']), 'far:arrow-alt-circle-left blue');
        
        // -------------------------------------------------------------------------
        // NOTEBOOK (ABAS DE OBSERVAÇÕES)
        // -------------------------------------------------------------------------
        $this->subnotebook = new BootstrapNotebookWrapper(new TNotebook);
        $this->form->addContent([$this->subnotebook]);

        $obs = new TTextDisplay($aluno->obs);
        $obs->style = 'margin:10px; display:block;';
        $this->subnotebook->appendPage('Parecer Técnico Assistente Social', $obs);

        $obs_ass_social = new TTextDisplay($aluno->obs_ass_social);
        $obs_ass_social->style = 'margin:10px; display:block;';
        $this->subnotebook->appendPage('Parecer Técnico (não visível para alunos)', $obs_ass_social);

        // Box Container Geral da Página
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'formView-container'; 
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReqBolsaAlunoListGestor'));
        $container->add($this->form);

        TTransaction::close();

        parent::add($container);
    }
    
    public function onPrint($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $object = ReqBolsaAluno::find($param['key']);
             
            if ($object)
            {
                $html = new AdiantiHTMLDocumentParser('app/documents/GestorDocument.html', 'A4', 'portrait');
                $html->setMaster($object);
                
                $object->data_final = TDate::date2br($object->data_final);
                $object->rp_salario_minimo = number_format($object->rp_salario_minimo, 2, ",", ".");
                $object->rf_salario_minimo = number_format($object->rf_salario_minimo, 2, ",", ".");
    
                $objects = ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $object->id)->load();
                $html->setDetail('ReqBolsaAlunoItem', $objects);
       
                $html->process();
                $output = $html->getContents();
                
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
                
                parent::openFile($document);
                new TMessage('info', 'Documento gerado com sucesso. Por favor, habilite os popups no navegador.');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }
    
    public function onShow()
    {      
    } 
}