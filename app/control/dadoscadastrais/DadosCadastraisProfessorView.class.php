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
        
        
        TTransaction::open('Felabs_DB');
        //$logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        TTransaction::close();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DadosCadastrais');
        $this->form->class = 'tform'; // CSS class
        //$this->form->style = 'display: table;width:100%'; // style
        parent::include_css('app/resources/custom-frame.css');


        TTransaction::open('dados_fei');
        $fiProfessor = new FiProfessor($user->systemuser_codlegado);
        
        $this->form->setFormTitle('Meu Cadastro');

        $text2  = new TTextDisplay($fiProfessor->Nome, '#333333', '14px', '');
        $text3  = new TTextDisplay(TDate::date2br($fiProfessor->DataNascimento), '#333333', '14px', '');
        $text4  = new TTextDisplay($fiProfessor->Sexo, '#333333', '14px', '');
        $text5  = new TTextDisplay($fiProfessor->Naturalidade, '#333333', '14px', '');
        $text6  = new TTextDisplay($fiProfessor->NaturalidadeUf, '#333333', '14px', '');
        $text7  = new TTextDisplay($fiProfessor->Nacionalidade, '#333333', '14px', '');
        $text8  = new TTextDisplay($fiProfessor->HabilitacaoProf1, '#333333', '14px', '');
        $text9  = new TTextDisplay($fiProfessor->HabilitacaoProf2, '#333333', '14px', '');
        $text10  = new TTextDisplay($fiProfessor->HabilitacaoProf3, '#333333', '14px', '');
        $text11  = new TTextDisplay($fiProfessor->Rg, '#333333', '14px', '');
        $text12  = new TTextDisplay($fiProfessor->CPF, '#333333', '14px', '');
        $text13  = new TTextDisplay($fiProfessor->Endereco, '#333333', '14px', '');
        $text14  = new TTextDisplay($fiProfessor->Bairro, '#333333', '14px', '');
        $text15  = new TTextDisplay($fiProfessor->CEP, '#333333', '14px', '');
        $text16  = new TTextDisplay($fiProfessor->Telefone1, '#333333', '14px', '');
        $text17  = new TTextDisplay($fiProfessor->Telefone2, '#333333', '14px', '');
        $text18  = new TTextDisplay($fiProfessor->Telefone3, '#333333', '14px', '');
        $text19  = new TTextDisplay($fiProfessor->Email, '#333333', '14px', '');
        $object_cidade = new FiCidade($fiProfessor->CodCidade);
        $text20  = new TTextDisplay($object_cidade->Nome, '#333333', '14px', '');

        
        // master
        $this->form->addFields( [new TFormSeparator('Informações gerais')] );
        $this->form->addFields( [new TLabel('Nome')], [$text2],[new TLabel('Data de nascimento')], [$text3] );
        $this->form->addFields( [new TLabel('Sexo')], [$text4], [new TLabel('Naturalidade')], [$text5]);
        $this->form->addFields( [new TLabel('Naturalidade UF')], [$text6], [new TLabel('Nacionalidade')], [$text7]);
        $this->form->addFields( [new TLabel('RG')], [$text11], [new TLabel('CPF')], [$text12]);
        $this->form->addFields( [new TLabel('Habilitação ')], [$text8], [new TLabel('Habilitação 2')], [$text9]);
        $this->form->addFields( [new TLabel('Habilitação 3')], [$text10], [new TLabel('')]);
        $this->form->addFields( [new TFormSeparator('Contato')] );
        $this->form->addFields( [new TLabel('Endereço')], [$text13], [new TLabel('Bairro')], [$text14]);
        $this->form->addFields( [new TLabel('Cidade')], [$text20], [new TLabel('CEP')], [$text15]);
        $this->form->addFields( [new TLabel('Telefone')], [$text16], [new TLabel('Telefone 2')], [$text17]);
        $this->form->addFields( [new TLabel('Telefone 3')], [$text18], [new TLabel('Email ')], [$text19]);
        
        
        // create the page container
        $container = new TVBox;
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->style = 'width: 100%';
        $container->add($this->form);
        $div = new TElement('div');
        $div->add($a = $container);

        $a->style = 'width:90%;';
        
        parent::add($div);
    }  
}