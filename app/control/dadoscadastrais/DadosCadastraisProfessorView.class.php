<?php

class DadosCadastraisProfessorView extends TPage
{
    protected $form; 
    protected $formFields;
    protected $detail_list;

    use adianti\base\AdiantiMasterDetailTrait;

    public function __construct($param)
    {
        parent::__construct();
        
        try
        {
            TTransaction::open('Felabs_DB');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            TTransaction::close();
            
            TTransaction::open('dados_fei');
            $fiProfessor = new FiProfessor($user->systemuser_codlegado);
            $object_cidade = new FiCidade($fiProfessor->CodCidade);
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
        
        // Creates the form
        $this->form = new BootstrapFormBuilder('form_DadosCadastrais');
        $this->form->setFormTitle('Meu Cadastro');
        parent::include_css('app/resources/custom-frame.css');

        // Estilização das tags/badges para dar destaque visual moderno
        $sexoBadge = $fiProfessor->Sexo == 'F' ? 'success' : 'info';

        // Customização dos elementos de exibição (Valores)
        $text_nome         = '<b>' . $fiProfessor->Nome . '</b>';
        $text_nascimento   = TDate::date2br($fiProfessor->DataNascimento);
        $text_sexo         = "<span class='label label-{$sexoBadge}'>" . ($fiProfessor->Sexo == 'F' ? 'Feminino' : 'Masculino') . "</span>";
        $text_naturalidade = $fiProfessor->Naturalidade;
        $text_uf           = "<span class='label label-default'>{$fiProfessor->NaturalidadeUf}</span>";
        $text_nacionalidade= $fiProfessor->Nacionalidade;
        $text_rg           = $fiProfessor->Rg;
        $text_cpf          = $fiProfessor->CPF;
        
        $text_hab1         = $fiProfessor->HabilitacaoProf1 ? $fiProfessor->HabilitacaoProf1 : '<i>Não informada</i>';
        $text_hab2         = $fiProfessor->HabilitacaoProf2 ? $fiProfessor->HabilitacaoProf2 : '<i>Não informada</i>';
        $text_hab3         = $fiProfessor->HabilitacaoProf3 ? $fiProfessor->HabilitacaoProf3 : '<i>Não informada</i>';
        
        $text_endereco     = $fiProfessor->Endereco;
        $text_bairro       = $fiProfessor->Bairro;
        $text_cidade       = $object_cidade->Nome;
        $text_cep          = $fiProfessor->CEP;
        $text_tel1         = $fiProfessor->Telefone1 ? $fiProfessor->Telefone1 : '<i>Não informado</i>';
        $text_tel2         = $fiProfessor->Telefone2 ? $fiProfessor->Telefone2 : '<i>Não informado</i>';
        $text_tel3         = $fiProfessor->Telefone3 ? $fiProfessor->Telefone3 : '<i>Não informado</i>';
        $text_email        = $fiProfessor->Email;

        // Container principal onde vamos injetar o grid customizado
        $gridContainer = new TElement('div');
        $gridContainer->style = 'padding: 10px 15px;';

        // --- FUNÇÃO AUXILIAR PARA CRIAR OS BLOCOS DE CAMPO ---
        $createFieldBlock = function($label, $value, $colSize) {
            $col = new TElement('div');
            $col->class = $colSize;
            $col->style = 'margin-bottom: 15px;';
            
            $lbl = new TElement('label');
            $lbl->style = 'display: block; font-weight: bold; margin-bottom: 3px; font-size: 12px; text-transform: uppercase;';
            $lbl->add($label);
            
            $val = new TElement('div');
            $val->style = 'font-size: 14px;';
            $val->add($value);
            
            $col->add($lbl);
            $col->add($val);
            return $col;
        };

        // --- SEÇÃO 1: IDENTIFICAÇÃO ---
        $sep1 = new TFormSeparator('Identificação & Informações Gerais');
        $gridContainer->add($sep1);
        
        $row1 = new TElement('div');
        $row1->class = 'row';
        $row1->add($createFieldBlock('Nome Completo', $text_nome, 'col-sm-8'));
        $row1->add($createFieldBlock('Data de Nascimento', $text_nascimento, 'col-sm-4'));
        $gridContainer->add($row1);

        $row2 = new TElement('div');
        $row2->class = 'row';
        $row2->add($createFieldBlock('Sexo', $text_sexo, 'col-sm-3'));
        $row2->add($createFieldBlock('Naturalidade', $text_naturalidade, 'col-sm-4'));
        $row2->add($createFieldBlock('UF', $text_uf, 'col-sm-1'));
        $row2->add($createFieldBlock('Nacionalidade', $text_nacionalidade, 'col-sm-4'));
        $gridContainer->add($row2);

        // --- SEÇÃO 2: DOCUMENTAÇÃO ---
        $sep2 = new TFormSeparator('Documentação');
        $gridContainer->add($sep2);

        $row3 = new TElement('div');
        $row3->class = 'row';
        $row3->add($createFieldBlock('CPF', $text_cpf, 'col-sm-6'));
        $row3->add($createFieldBlock('RG', $text_rg, 'col-sm-6'));
        $gridContainer->add($row3);

        // --- SEÇÃO 3: HABILITAÇÕES ---
        $sep3 = new TFormSeparator('Habilitações Acadêmicas / Profissionais');
        $gridContainer->add($sep3);

        $row4 = new TElement('div');
        $row4->class = 'row';
        $row4->add($createFieldBlock('Habilitação 1', $text_hab1, 'col-sm-4'));
        $row4->add($createFieldBlock('Habilitação 2', $text_hab2, 'col-sm-4'));
        $row4->add($createFieldBlock('Habilitação 3', $text_hab3, 'col-sm-4'));
        $gridContainer->add($row4);

        // --- SEÇÃO 4: CONTATO & ENDEREÇO ---
        $sep4 = new TFormSeparator('Contato & Endereço');
        $gridContainer->add($sep4);

        $row5 = new TElement('div');
        $row5->class = 'row';
        $row5->add($createFieldBlock('Endereço', $text_endereco, 'col-sm-8'));
        $row5->add($createFieldBlock('Bairro', $text_bairro, 'col-sm-4'));
        $gridContainer->add($row5);

        $row6 = new TElement('div');
        $row6->class = 'row';
        $row6->add($createFieldBlock('Cidade', $text_cidade, 'col-sm-6'));
        $row6->add($createFieldBlock('CEP', $text_cep, 'col-sm-6'));
        $gridContainer->add($row6);

        $row7 = new TElement('div');
        $row7->class = 'row';
        $row7->add($createFieldBlock('Telefone 1', $text_tel1, 'col-sm-3'));
        $row7->add($createFieldBlock('Telefone 2', $text_tel2, 'col-sm-3'));
        $row7->add($createFieldBlock('Telefone 3', $text_tel3, 'col-sm-3'));
        $row7->add($createFieldBlock('E-mail', $text_email, 'col-sm-3'));
        $gridContainer->add($row7);

        // Injeta o conjunto completo estruturado no formulário
        $this->form->addContent([$gridContainer]);

        // Create the page container
        $container = new TVBox;
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->style = 'width: 100%';
        $container->add($this->form);

        $div = new TElement('div');
        $div->add($container);
        $container->style = 'width:100%;';

        parent::add($div);
    }  
}