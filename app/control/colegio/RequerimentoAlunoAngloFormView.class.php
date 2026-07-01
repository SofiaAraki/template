<?php
/**
 * DespesaFormView Form
 * @author  <your name here>
 */
class RequerimentoAlunoAngloFormView extends TPage
{
     protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        TTransaction::open('dados_fei');
        
        $this->form = new BootstrapFormBuilder('RequerimentoAlunoAngloFormView');
        $this->form->setFormTitle('Requerimento de Matrícula - Anglo');
        
        $label1 = new TLabel('COD:', '#333333', '15px', '');
        $label2 = new TLabel('ALUNO(a):', '#333333', '15px', '');
        $label3 = new TLabel('NACIONALIDADE:', '#333333', '15px', '');
        $label4 = new TLabel('DATA DE NASC.:', '#333333', '15px', '');
        $label5 = new TLabel('RG:', '#333333', '15px', '');
        $label6 = new TLabel('CPF:', '#333333', '15px', '');
        //$label7 = new TLabel('', '#333333', '15px', '');
        $label8 = new TLabel('ENDEREÇO:', '#333333', '15px', '');
        $label9 = new TLabel('Nº:', '#333333', '15px', '');
        $label10 = new TLabel('Compl.:', '#333333', '15px', '');
        $label11 = new TLabel('BAIRRO:', '#333333', '15px', '');
        $label12 = new TLabel('CIDADE:', '#333333', '15px', '');
        $label30 = new TLabel('UF:', '#333333', '15px', '');
        $label13 = new TLabel('CEP:', '#333333', '15px', '');
        $label14 = new TLabel('TELEFONE:', '#333333', '15px', '');
        $label15 = new TLabel('RESPONSÁVEL:', '#333333', '15px', '');
        $label16 = new TLabel('RG:', '#333333', '15px', '');
        $label17 = new TLabel('CPF:', '#333333', '15px', '');
        $label18 = new TLabel('ENDEREÇO RESP.:', '#333333', '15px', '');
        $label19 = new TLabel('Nº RESP.:', '#333333', '15px', '');
        $label20 = new TLabel('BAIRRO.:', '#333333', '15px', '');
        $label21 = new TLabel('E-MAIL:', '#333333', '15px', '');
        $label22 = new TLabel('CIDADE:', '#333333', '15px', '');
        $label31 = new TLabel('UF:', '#333333', '15px', '');
        $label23 = new TLabel('CEP:', '#333333', '15px', '');
        $label24 = new TLabel('TELEFONE RESP.:', '#333333', '15px', '');
        $label25 = new TLabel('CURSO:', '#333333', '15px', '');
        $label26 = new TLabel('PERÍODO:', '#333333', '15px', '');
        $label27 = new TLabel('ETAPA:', '#333333', '15px', '');
        $label28 = new TLabel('ANO:', '#333333', '15px', '');
        $label29 = new TLabel('COD. RESP.:', '#333333', '15px', '');

            $curso = $param['Curso'];
            $periodo = $param['Periodo'];
            $etapa = $param['Etapa'];
            $ano = $param['Ano'];

        $requerimento_aluno = new FiAluno($param['key']);

        //var_dump($requerimento_aluno);
        
        $text1  = new TTextDisplay($requerimento_aluno->Codaluno, '#333333', '15px', '');          
        $text2  = new TTextDisplay($requerimento_aluno->Nome, '#333333', '15px', '');
        $text3  = new TTextDisplay($requerimento_aluno->Nacionalidade, '#333333', '15px', '');
        $text4  = new TTextDisplay(TDate::date2br($requerimento_aluno->Datanascimento, '#333333', '15px', ''));
        $text5  = new TTextDisplay($requerimento_aluno->Rg, '#333333', '15px', '');
        $text6  = new TTextDisplay($requerimento_aluno->RgOrgaoExpedidor, '#333333', '15px', '');
        $text7  = new TTextDisplay($requerimento_aluno->CPF, '#333333', '15px', '');
        //$text7  = new TTextDisplay($programa_ensino_disciplina->status, '#333333', '15px', '');
        $text8  = new TTextDisplay($requerimento_aluno->Endereco, '#333333', '15px', '');
        $text9  = new TTextDisplay($requerimento_aluno->EnderecoNumero, '#333333', '15px', '');
        $text10  = new TTextDisplay($requerimento_aluno->EnderecoComplemeto, '#333333', '15px', '');
        $text11  = new TTextDisplay($requerimento_aluno->Bairro, '#333333', '15px', '');
        $text12  = new TTextDisplay($requerimento_aluno->cidade_aluno, '#333333', '15px', '');
        $text13  = new TTextDisplay($requerimento_aluno->Cep, '#333333', '15px', '');
        $text14  = new TTextDisplay($requerimento_aluno->Telefone, '#333333', '15px', '');
        $text15  = new TTextDisplay($requerimento_aluno->Email, '#333333', '15px', '');
        $text30  = new TTextDisplay($requerimento_aluno->cidade_aluno->Uf, '#333333', '15px', '');

        TTransaction::open('dados_fei');
        
        $object = new FiAluno($param['key']);
        $responsavel = $object->CodResponsavel;
        

        

        $requerimento_responsavel = new FiResponsavel($responsavel);



        $text16  = new TTextDisplay($requerimento_responsavel->codresponsavel, '#333333', '15px', '');
        $text17  = new TTextDisplay($requerimento_responsavel->Nome, '#333333', '15px', '');
        $text18  = new TTextDisplay($requerimento_responsavel->Rg, '#333333', '15px', '');
        $text19  = new TTextDisplay($requerimento_responsavel->CPF, '#333333', '15px', '');
        $text20  = new TTextDisplay($requerimento_responsavel->Endereco, '#333333', '15px', '');
        $text21  = new TTextDisplay($requerimento_responsavel->EnderecoNumero, '#333333', '15px', '');
        $text32  = new TTextDisplay($requerimento_responsavel->Bairro, '#333333', '15px', '');
        $text22  = new TTextDisplay($requerimento_responsavel->email, '#333333', '15px', '');
        $text23  = new TTextDisplay($requerimento_responsavel->cidade_responsavel, '#333333', '15px', '');
        $text24  = new TTextDisplay($requerimento_responsavel->Cep, '#333333', '15px', '');
        $text25  = new TTextDisplay($requerimento_responsavel->Telefone1, '#333333', '15px', '');
        $text31  = new TTextDisplay($requerimento_responsavel->cidade_responsavel->Uf, '#333333', '15px', '');
        $text26  = new TTextDisplay($curso, '#333333', '15px', '');
        $text27  = new TTextDisplay($periodo, '#333333', '15px', '');
        $text28  = new TTextDisplay($etapa, '#333333', '15px', '');
        $text29  = new TTextDisplay($ano, '#333333', '15px', '');
        
        $this->form->addFields( [new TFormSeparator('Dados do Aluno(a)')] );
        $this->form->addFields([$label1],[$text1]);
        $this->form->addFields([$label2],[$text2]);
        $this->form->addFields([$label3],[$text3]);
        $this->form->addFields([$label4],[$text4]);
        $this->form->addFields([$label5],[$text5]);
        //$this->form->addFields([$label3],[$text3]);
        //$this->form->addFields([$label4],[$text4]);
        $this->form->addFields([$label6],[$text7]);
        $this->form->addFields([$label8],[$text8],[$label9],[$text9],[$label10],[$text10]);
        $this->form->addFields([$label11],[$text11]);
        $this->form->addFields([$label12],[$text12],[$label30],[$text30],[$label13],[$text13]);
        $this->form->addFields([$label14],[$text14]);
        
        $this->form->addFields( [new TFormSeparator('DADOS DO RESPONSÁVEL')] );
        $this->form->addFields([$label29],[$text16]);
        $this->form->addFields([$label15],[$text17]);
        $this->form->addFields([$label16],[$text18],[$label17],[$text19]);
        $this->form->addFields([$label18],[$text20],[$label19],[$text21],[$label31],[$text31]);
        $this->form->addFields([$label20],[$text32]);
        $this->form->addFields([$label21],[$text22]);
        $this->form->addFields([$label23],[$text24]);
        $this->form->addFields([$label24],[$text25]);
        $this->form->addFields([$label25],[$text26]);
        $this->form->addFields([$label26],[$text27]);
        $this->form->addFields([$label27],[$text28]);
        $this->form->addFields([$label28],[$text29]);
        //$this->form->addFields();

        $this->form->addHeaderAction('Imprimir', new TAction(['RequerimentoAlunoAngloFormView', 'onPrint'],['key'=>$requerimento_aluno->Codaluno]), 'far:file-pdf red');
        $this->form->addHeaderAction('Voltar', new TAction(['ReqMatriculaAlunoListAnglo', 'onReload']), 'fas:arrow-left blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'formView-container'; 
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReqMatriculaAlunoListAnglo'));
        $container->add($this->form);

        TTransaction::close();

        parent::add($container);
    }


    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    

    /**
     * Imprime a despesa
    */ 
    public function onPrint($param)
    {
        try
        {

           // var_dump($param);

            $curso = $param['Curso'];
            $periodo = $param['Periodo'];
            $etapa = $param['Etapa'];
            $ano = $param['Ano'];

            //var_dump($periodo);


            TTransaction::open('dados_fei');
            
            $req_aluno = new FiAluno($param['key']);
            $responsavel = $req_aluno->CodResponsavel;

            $req_responsavel = new FiResponsavel($responsavel);

             
            if ($req_aluno)
            {
                               
             
                $object = new VwAlunocolegio;

                $object->Curso              = $curso;
                $object->Periodo            = $periodo;
                $object->Etapa              = $etapa;
                $object->Ano                = $ano;
                $object->Codaluno           = $req_aluno->Codaluno;
                $object->Nome               = $req_aluno->Nome;
                $object->Nacionalidade      = $req_aluno->Nacionalidade;
                $object->Datanascimento     = TDate::date2br($req_aluno->Datanascimento);
                $object->Rg                 = $req_aluno->Rg;
                $object->RgOrgaoExpedidor   = $req_aluno->RgOrgaoExpedidor;
                $object->CPF                = $req_aluno->CPF;
                $object->Endereco           = $req_aluno->Endereco;
                $object->EnderecoNumero     = $req_aluno->EnderecoNumero;
                $object->EnderecoComplemeto = $req_aluno->EnderecoComplemeto;
                $object->Bairro             = $req_aluno->Bairro;
                $object->cidade_aluno       = $req_aluno->cidade_aluno;
                $object->uf_aluno           = $req_aluno->cidade_aluno->Uf;
                $object->CEP_aluno          = $req_aluno->Cep;
                $object->Tel_aluno          = $req_aluno->Telefone;

                //dados responsável - FI_Responsavel
                $object->CodResponsavel     = $req_responsavel->codresponsavel;
                $object->NomeResp           = $req_responsavel->Nome;
                $object->RgResp             = $req_responsavel->Rg;
                $object->CPFResp            = $req_responsavel->CPF;
                $object->RuaResp            = $req_responsavel->Endereco;
                $object->NumResp            = $req_responsavel->EnderecoNumero;
                $object->BairroResp         = $req_responsavel->Bairro;
                $object->emailResp          = $req_responsavel->email;
                $object->CidadeResp         = $req_responsavel->cidade_responsavel;
                $object->UFResp             = $req_responsavel->cidade_responsavel->Uf;
                $object->CepResp            = $req_responsavel->Cep;
                $object->TelefoneResp       = $req_responsavel->Telefone1;

                
                $html = new AdiantiHTMLDocumentParser('app/documents/RequerimentoAlunoAnglo.html', 'A4', 'portrait');
                $html->setMaster($object);
      
                $html->process();
                $output = $html->getContents();
                
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
                
                parent::openFile($document);
                new TMessage('info', 'Documento PDF gerado com sucesso. Caso não tenha conseguido visualizá-lo, habilite pop-ups em seu navegador e tente gerá-lo novamente.');

                $this->form->setData($object); // fill the form

                

            
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

   
            
