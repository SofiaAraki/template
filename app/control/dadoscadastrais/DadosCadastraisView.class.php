<?php


class DadosCadastraisView extends TPage
{
    protected $form; 
    protected $formFields;
    protected $detail_list;


    public function __construct($param)
    {
        parent::__construct();
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $unitid = TSession::getValue('userunitid');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            TTransaction::close();
            
                        
            TTransaction::open('dados_fei');
            
            //Para preenchimendo da view
            $fiAluno = new FiAluno($user->systemuser_codlegado);
            $object_cidade = new FiCidade($fiAluno->CodCidade);
            
            
            ///////////Verifica se é da graduação ou colégio//////////////////////(Amanda)
            $ano_atual = date('Y');
                        
            $array_colegio = [];
            $array_colegio = ['118' => '118', '119' => '119', '120' => '120'];

            $matriculas = new TRepository('VwAlunoMatriculaEtapa');
            
            $criteria_cadastro = new TCriteria;
            $criteria_cadastro->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
            $criteria_cadastro->add(new TFilter('AnoMatricula', '=', $ano_atual)); //Tira as matrículas antigas do NSC e ANGLO
            $criteria_cadastro->add(new TFilter('CodCurso', 'NOT IN', $array_colegio));
                                    
            $aluno = $matriculas->load($criteria_cadastro);
    
            TTransaction::close();            
            ///////////Encerra Verifica se é da graduação ou colégio//////////////////////(Amanda)
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DadosCadastrais');
        $this->form->setFormTitle('Meu Cadastro');

       
        $text1  = new TTextDisplay($fiAluno->Nome, '#333333', '14px', '');
        $text2  = new TTextDisplay(TDate::date2br($fiAluno->Datanascimento), '#333333', '14px', '');
        $text3  = new TTextDisplay($fiAluno->Sexo, '#333333', '14px', '');
        $text4  = new TTextDisplay($fiAluno->Naturalidade, '#333333', '14px', '');
        $text5  = new TTextDisplay($fiAluno->NaturalidadeUF, '#333333', '14px', '');
        $text6  = new TTextDisplay($fiAluno->Nacionalidade, '#333333', '14px', '');
        $text7  = new TTextDisplay($fiAluno->NomePai, '#333333', '14px', '');
        $text8  = new TTextDisplay($fiAluno->NomeMae, '#333333', '14px', '');
        $text9  = new TTextDisplay($fiAluno->Rg, '#333333', '14px', '');
        $text10  = new TTextDisplay($fiAluno->RgOrgaoExpedidor, '#333333', '14px', '');
        $text11  = new TTextDisplay($fiAluno->Profissao, '#333333', '14px', '');
        $text12  = new TTextDisplay($fiAluno->CPF, '#333333', '14px', '');
        $text13  = new TTextDisplay($fiAluno->Endereco, '#333333', '14px', '');
        $text14  = new TTextDisplay($fiAluno->EnderecoNumero, '#333333', '14px', '');
        $text15  = new TTextDisplay($fiAluno->EnderecoComplemeto, '#333333', '14px', '');
        $text16  = new TTextDisplay($fiAluno->Bairro, '#333333', '14px', '');
        $text17  = new TTextDisplay($fiAluno->Cep, '#333333', '14px', '');
        $text18  = new TTextDisplay($fiAluno->Telefone, '#333333', '14px', '');
        $text19  = new TTextDisplay($fiAluno->Telefone2, '#333333', '14px', '');
        $text20  = new TTextDisplay($fiAluno->Telefone3, '#333333', '14px', '');
        $text21  = new TTextDisplay($fiAluno->Email, '#333333', '14px', '');
        $text22  = new TTextDisplay($fiAluno->EstadoCivil, '#333333', '14px', '');
        $text23  = new TTextDisplay($fiAluno->CorRaca, '#333333', '14px', '');        
        $text24  = new TTextDisplay($object_cidade->Nome, '#333333', '14px', '');  
        $text25  = new TTextDisplay($fiAluno->ContatoWhatsapp, '#333333', '14px', '');        


        $this->form->addFields( [ new TFormSeparator('Informações gerais') ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $text1 ],[ new TLabel('Data de nascimento') ], [ $text2 ] );
        $this->form->addFields( [ new TLabel('Sexo') ], [ $text3 ], [ new TLabel('Naturalidade') ], [ $text4 ] );
        $this->form->addFields( [ new TLabel('Naturalidade UF') ], [ $text5 ], [ new TLabel('Nacionalidade') ], [ $text6 ] );
        $this->form->addFields( [ new TLabel('Nome do pai') ], [ $text7 ], [ new TLabel('Nome da mãe') ], [ $text8 ] );
        $this->form->addFields( [ new TLabel('RG') ], [ $text9 ], [ new TLabel('Órgão expedidor') ], [ $text10 ] );
        $this->form->addFields( [ new TLabel('Profissão') ], [ $text11 ], [ new TLabel('CPF') ], [ $text12 ] );
        $this->form->addFields( [ new TLabel('Cor/raça') ], [ $text23 ],[ new TLabel('Estado civil') ], [ $text22 ] );
        
        /*Ocultado para não confundir o aluno depois que ele atualizar as informações na tabela contato_aluno,
        visto que aqui traz o registro do Genesi*/
        
        /*$this->form->addFields( [new TFormSeparator('Contato')] );
        $this->form->addFields( [new TLabel('Endereço')], [$text13], [new TLabel('Número')], [$text14]);
        $this->form->addFields( [new TLabel('Complemento')], [$text15], [new TLabel('Bairro')], [$text16]);
        $this->form->addFields( [new TLabel('Cidade')], [$text24], [new TLabel('CEP')], [$text17]);
        $this->form->addFields( [new TLabel('Telefone')], [$text18], [new TLabel('Telefone 2')], [$text19]);
        $this->form->addFields( [new TLabel('Telefone 3')], [$text20], [new TLabel('Email')], [$text21]);
        $this->form->addFields( [new TLabel('Contato whatsapp')], [$text25]);*/
      
        
        //Se for do colégio, não permite alteração por parte do aluno
        if($aluno)
        {
            $this->form->addHeaderAction('Atualizar informações de contato', new TAction([$this, 'onSetDadosContato'], ['Codaluno' => $fiAluno->Codaluno]), 'far:edit blue fa-lg');            
        }       
        else
        {
            $this->form->addHeaderAction('Solicitar Alteração', new TAction(['TicketFormListAluno', 'onReload']), 'far:edit blue fa-lg');
        }
        
    
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
  
    
    public function onSetDadosContato($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $cod_aluno = $param['Codaluno'];
            
            $repository = new TRepository('ContatoAluno');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $cod_aluno));
            
            $contato_aluno = $repository->load($criteria);
           
            if($contato_aluno)
            {
                $parametros['key'] = $contato_aluno[0]->id;
                
                TApplication::loadPage('DadosCadastraisAlunoEditForm', 'onEdit', $parametros);
            }
            else
            {
                $parametros['cod_aluno'] = $cod_aluno;
                
                TApplication::loadPage('DadosCadastraisAlunoEditForm', 'onLoad', $parametros);
            }
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }    
    }
    
    
    public function onLoad()
    {
    }
}