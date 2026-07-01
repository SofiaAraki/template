<?php
/**
 * FiAlunoForm Form
 * @author  <your name here>
 */
class RequerimentoMatriculaAnglo extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

             
        // creates the form
        $this->form = new BootstrapFormBuilder('form_RequerimentoMatriculaAnglo');
        $this->form->setFormTitle('Requerimento de Matrícula - Anglo');

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $Datanascimento = new TEntry('Datanascimento');
        $Naturalidade = new TEntry('Naturalidade');
        $NaturalidadeUF = new TEntry('NaturalidadeUF');
        $Nacionalidade = new TEntry('Nacionalidade');
        $Rg = new TEntry('Rg');
        $RgOrgaoExpedidor = new TEntry('RgOrgaoExpedidor');
        $CPF = new TEntry('CPF');
        $Endereco = new TEntry('Endereco');
        $EnderecoNumero = new TEntry('EnderecoNumero');
        $EnderecoComplemeto = new TEntry('EnderecoComplemeto');
        $Bairro = new TEntry('Bairro');
        $CodCidade = new TEntry('cidade_aluno');
        $Cep = new TEntry('Cep');
        $Telefone = new TEntry('Telefone');
        $Email = new TEntry('Email');
        $Telefone2 = new TEntry('Telefone2');
        $Curso = new TCombo('Curso');
        $Periodo = new TCombo('Periodo');
        $Etapa = new TCombo('Etapa');
        $Ano = new TEntry('Ano');
        $CodResponsavel = new TEntry('CodResponsavel'); //ele ja traz da tabela FiAluno
        $NomeResponsavel = new TEntry('NomeResponsavel');
        $RgResponsavel = new TEntry('RgResponsavel');
        $CPFResponsavel = new TEntry('CPFResponsavel');
        $RuaResponsavel = new TEntry('RuaResponsavel');
        $NumResponsavel = new TEntry('NumResponsavel');
        $BairroResponsavel = new TEntry('BairroResponsavel');
        $EmailResponsavel = new TEntry('EmailResponsavel');
        $CidadeResponsavel = new TEntry('CidadeResponsavel');
        $CEPResponsavel = new TEntry('CEPResponsavel');
        $TelResponsavel = new TEntry('TelResponsavel');

       //  $output_type = new TRadioGroup('output_type');
        

        // add the fields
        $this->form->addFields( [new TFormSeparator('Dados do Aluno(a)')] );
        $this->form->addFields( [ new TLabel('Cod. Aluno') ], [ $Codaluno ]);
        $this->form->addFields( [ new TLabel('Nome') ], [ $Nome ] );
        $this->form->addFields( [ new TLabel('Data Nasc.') ], [ $Datanascimento ] );
        $this->form->addFields( [ new TLabel('Naturalidade') ], [ $Naturalidade ],[ new TLabel('UF') ], [ $NaturalidadeUF ],[ new TLabel('Nacionalidade') ], [ $Nacionalidade ]  );
        $this->form->addFields( [ new TLabel('CPF') ], [ $CPF ],[ new TLabel('RG') ], [ $Rg ],[ new TLabel('Orgão Expedidor') ], [ $RgOrgaoExpedidor ]  );
        $this->form->addFields( [ new TLabel('Endereço') ], [ $Endereco ],[ new TLabel('Nº') ], [ $EnderecoNumero ],[ new TLabel('Complemento') ], [ $EnderecoComplemeto ]  );
        $this->form->addFields( );
        $this->form->addFields( [ new TLabel('Bairro') ], [ $Bairro ],[ new TLabel('Cidade') ], [ $CodCidade ],[ new TLabel('CEP') ], [ $Cep ]  );
        $this->form->addFields( [ new TLabel('Email') ], [ $Email ], [ new TLabel('Telefone') ], [ $Telefone ],[ new TLabel('Telefone') ], [ $Telefone2 ]);
        $this->form->addFields( [new TFormSeparator('Dados do Responsável')] );
        $this->form->addFields( [ new TLabel('Cod. Resp.') ], [ $CodResponsavel ] );

        $this->form->addFields([ new TLabel('Nome') ], [ $NomeResponsavel ],[ new TLabel('Rg') ], [ $RgResponsavel ],[ new TLabel('CPF') ], [ $CPFResponsavel ]);
        $this->form->addFields([ new TLabel('Endereço') ], [ $RuaResponsavel ],[ new TLabel('Nº') ], [ $NumResponsavel ],[ new TLabel('Bairro') ], [ $BairroResponsavel ]);
        $this->form->addFields([ new TLabel('Email') ], [ $EmailResponsavel ]);
        $this->form->addFields([ new TLabel('Cidade') ], [ $CidadeResponsavel ],[ new TLabel('CEP') ], [ $CEPResponsavel ],[ new TLabel('Telefone') ], [ $TelResponsavel ]);
        
        $this->form->addFields( [new TFormSeparator('Dados da Matrícula')] );

        $this->form->addFields( [ new TLabel('Curso') ], [ $Curso ],[ new TLabel('Ano Letivo') ], [ $Ano ]);
        $this->form->addFields([ new TLabel('Período') ], [ $Periodo ],[ new TLabel('Etapa') ], [ $Etapa ] );

       //$this->form->addFields( [ new TLabel('Output') ], [ $output_type ] );

       // $output_type->addValidation('Output', new TRequiredValidator);
       


        $Curso->addItems( [ 'Ensino Fundamental I' => 'Ensino Fundamental I',
                            'Ensino Fundamental II' => 'Ensino Fundamental II',
                            'Ensino Médio' => 'Ensino Médio', ] );

        $Periodo->addItems( [ 'Diurno' => 'Diurno', 
                              'Manhã' => 'Manhã',
                              'Tarde' => 'Tarde' ] );

        $Etapa->addItems( [ '1º Ano' => '1º Ano',
                            '2º Ano' => '2º Ano',
                            '3º Ano' => '3º Ano',
                            '4º Ano' => '4º Ano',
                            '5º Ano' => '5º Ano',
                            '6º Ano' => '6º Ano',
                            '7º Ano' => '7º Ano',
                            '8º Ano' => '8º Ano',
                            '9º Ano' => '9º Ano',
                            '1ª Série EM' => '1ª Série EM',
                            '2ª Série EM' => '2ª Série EM',
                            '3ª Série EM' => '3ª Série EM'] );


     /*   $output_type->addItems(['html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF', 'xls' => 'XLS']);
        $output_type->setLayout('horizontal');
        $output_type->setUseButton();
        $output_type->setValue('pdf');
        $output_type->setSize(70);
*/
        
        // set sizes
        $Codaluno->setSize('20%');
        $Nome->setSize('100%');
        $Datanascimento->setSize('30%');
        $Naturalidade->setSize('100%');
        $NaturalidadeUF->setSize('100%');
        $Nacionalidade->setSize('100%');
        $Rg->setSize('100%');
        $RgOrgaoExpedidor->setSize('100%');
        $CPF->setSize('100%');
        $Endereco->setSize('200%');
        $EnderecoNumero->setSize('100%');
        $EnderecoComplemeto->setSize('100%');
        $Bairro->setSize('100%');
        $CodCidade->setSize('100%');
        $Cep->setSize('100%');
        $Telefone->setSize('50%');
        $Email->setSize('150%');
        $Telefone2->setSize('50%');

        $CodResponsavel->setSize('30%');
        $NomeResponsavel->setSize('200%');
        $RuaResponsavel->setSize('200%');
        $NumResponsavel->setSize('30%');
        $BairroResponsavel->setSize('70%');
        

        $Ano->setValue(2021);

        TTransaction::open('dados_fei');
        
        $object = new FiAluno($param['key']);

        $responsavel = $object->CodResponsavel;

        $object_resp = new FiResponsavel($responsavel);

        $nomeresponsavel = $object_resp->Nome; 
        $rgresponsavel = $object_resp->Rg;
        $CPFresponsavel = $object_resp->CPF;
        $ruaresponsavel = $object_resp->Endereco;
        $numresponsavel = $object_resp->EnderecoNumero;
        $bairroresponsavel = $object_resp->Bairro;
        $emailresponsavel = $object_resp->email;
        $cidaderesponsavel = $object_resp->cidade_responsavel;
        $cepresponsavel = $object_resp->Cep;
        $telresponsavel = $object_resp->Telefone1;

        $NomeResponsavel->setValue($nomeresponsavel);
        $RgResponsavel->setValue($rgresponsavel);
        $CPFResponsavel->setValue($CPFresponsavel);
        $RuaResponsavel->setValue($ruaresponsavel);
        $NumResponsavel->setValue($numresponsavel);
        $BairroResponsavel->setValue($bairroresponsavel);
        $EmailResponsavel->setValue($emailresponsavel);
        $CidadeResponsavel->setValue($cidaderesponsavel);
        $CEPResponsavel->setValue($cepresponsavel);
        $TelResponsavel->setValue($telresponsavel);
        
        $Codaluno->setEditable(FALSE);
        $Nome->setEditable(FALSE);
        $Datanascimento->setEditable(FALSE);
        $Naturalidade->setEditable(FALSE);
        $NaturalidadeUF->setEditable(FALSE);
        $Nacionalidade->setEditable(FALSE);
        $Rg->setEditable(FALSE);
        $RgOrgaoExpedidor->setEditable(FALSE);
        $CPF->setEditable(FALSE);
        $Endereco->setEditable(FALSE);
        $EnderecoNumero->setEditable(FALSE);
        $EnderecoComplemeto->setEditable(FALSE);
        $Bairro->setEditable(FALSE);
        $CodCidade->setEditable(FALSE);
        $Cep->setEditable(FALSE);
        $Telefone->setEditable(FALSE);
        $Email->setEditable(FALSE);
        $Telefone2->setEditable(FALSE);
        $CodResponsavel->setEditable(FALSE);
        $NomeResponsavel->setEditable(FALSE);
        $Ano->setEditable(FALSE);
        $RgResponsavel->setEditable(FALSE);
        $CPFResponsavel->setEditable(FALSE);
        $RuaResponsavel->setEditable(FALSE);
        $NumResponsavel->setEditable(FALSE);
        $BairroResponsavel->setEditable(FALSE);
        $EmailResponsavel->setEditable(FALSE);
        $CidadeResponsavel->setEditable(FALSE);
        $CEPResponsavel->setEditable(FALSE);
        $TelResponsavel->setEditable(FALSE);

        TTransaction::close();



        if (!empty($Codaluno))
        {
            $Codaluno->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        //$btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        //$btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(['ReqMatriculaAlunoListAnglo', 'onReload']), 'fas:arrow-left blue');


        
        
        // add the action button
        $this->form->addAction('Gerar Requerimento',  new TAction(array('RequerimentoAlunoAngloFormView','onPrint'), $param), 'fa:check-circle green');


        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReqMatriculaAlunoListAnglo'));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
  /*  public function onSave( $param )
    {
        try
        {
            TTransaction::open('dados_fei_t'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
       /*     $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new FiAluno;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated Codaluno
            $data->Codaluno = $object->Codaluno;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }*/
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('dados_fei'); // open a transaction
                $object = new FiAluno($key); // instantiates the Active Record

                $object->Datanascimento = TDate::date2br($object->Datanascimento);  


                $this->form->setData($object); // fill the form

                $data = $this->form->getData();

            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

 
        

    


}
