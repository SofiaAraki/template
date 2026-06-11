<?php


class ContratoFinanceiroListMatricula extends TPage
{
    protected $form;


    public function __construct( $param )
    {
    
        parent::__construct();


        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContratoFinanceiroListMatricula');
        $this->form->setFormTitle('Contrato Financeiro - Lançamento de Desconto');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $Codaluno = new TDBSeekButton('Codaluno', 'dados_fei', 'form_ContratoFinanceiroListMatricula', 'VwAlunoMatriculaEtapa', 'NomeAluno', 'Codaluno', 'NomeAluno');
        $NomeAluno = new TEntry('NomeAluno');
        $NomeIdentificacaoCivil = new TEntry('NomeIdentificacaoCivil');
        $Datanascimento = new TEntry('Datanascimento');
        $CPF = new TEntry('CPF');
        $Rg = new TEntry('Rg');
        $RgOrgaoExpedidor = new TEntry('RgOrgaoExpedidor');
        $Naturalidade = new TEntry('Naturalidade');
        $NaturalidadeUF = new TEntry('NaturalidadeUF');
        $Endereco = new TEntry('Endereco');
        $EnderecoNumero = new TEntry('EnderecoNumero');
        $Bairro = new TEntry('Bairro');
        $CodCidade = new TEntry('CodCidade');
        $Uf = new TEntry('Uf');
        $Cep = new TEntry('Cep');
        $Email = new TEntry('Email');
        $Nacionalidade = new TEntry('Nacionalidade');
        $EstadoCivil = new TEntry('EstadoCivil');
        $Profissao = new TEntry('Profissao');
        $TipoEscolaEnsinoMedio = new TEntry('TipoEscolaEnsinoMedio');
        $Telefone = new TEntry('Telefone');
        $Telefone2 = new TEntry('Telefone2');
        $Telefone3 = new TEntry('Telefone3');
        $CodCurso = new TEntry('CodCurso');
        $NomeCurso = new TEntry('NomeCurso');
        $EtapaMatricula = new TEntry('EtapaMatricula');
        $AnoMatricula = new TEntry('AnoMatricula');
        $SemestreMatricula = new TEntry('SemestreMatricula');
        $Periodo = new TEntry('Periodo');
        $SituacaoMatricula = new TEntry('SituacaoMatricula');
        $CodEntidade = new TEntry('CodEntidade');
        $RazaoSocial = new TEntry('RazaoSocial');
        $NomeFantasia = new TEntry('NomeFantasia');

        //dados responsável
        $CodResponsavel = new TEntry('CodResponsavel'); //ele ja traz da tabela FiAluno
        $NomeResponsavel = new TEntry('NomeResponsavel');
        $RgResponsavel = new TEntry('RgResponsavel');
        $CPFResponsavel = new TEntry('CPFResponsavel');
        $RuaResponsavel = new TEntry('RuaResponsavel');
        $NumResponsavel = new TEntry('NumResponsavel');
        $BairroResponsavel = new TEntry('BairroResponsavel');
        $EmailResponsavel = new TEntry('EmailResponsavel');
        $CidadeResponsavel = new TEntry('CidadeResponsavel');
        $UfResponsavel = new TEntry('UfResponsavel');
        $CEPResponsavel = new TEntry('CEPResponsavel');
        $TelResponsavel = new TEntry('TelResponsavel');

        //dados contrato
        $ValorAnoSem = new TEntry('ValorAnoSem');
        $ValorAnoSemExt = new TEntry('ValorAnoSemExt');
        $ValorParc1 = new TEntry('ValorParc1');
        $ValorParc1Ext = new TEntry('ValorParc1Ext');
        $ValorDmsParc = new TEntry('ValorDmsParc');
        $ValorDmsParcExt = new TEntry('ValorDmsParcExt');
        $DescontoComercial = new TEntry('DescontoComercial');
        $StatusContrato = new TEntry('StatusContrato');
        $InicioPrestServico = new TCombo('InicioPrestServico');
        $TerminoPrestServico = new TCombo('TerminoPrestServico');
        $DataPrimeiraParcela = new TDate('DataPrimeiraParcela');
        $DataFinalContrato = new TEntry('DataFinalContrato');

        
        $combo_prazo = [];
        $combo_prazo['Janeiro'] = "Janeiro";
        $combo_prazo['Fevereiro'] = "Fevereiro";
        $combo_prazo['Março'] = "Março";
        $combo_prazo['Abril'] = "Abril";
        $combo_prazo['Maio'] = "Maio";
        $combo_prazo['Junho'] = "Junho";
        $combo_prazo['Julho'] = "Julho";
        $combo_prazo['Agosto'] = "Agosto";
        $combo_prazo['Setembro'] = "Setembro";
        $combo_prazo['Outubro'] = "Outubro";
        $combo_prazo['Novembro'] = "Novembro";
        $combo_prazo['Dezembro'] = "Dezembro";
        
        $InicioPrestServico->addItems($combo_prazo);
        $TerminoPrestServico->addItems($combo_prazo);


        // add the fields
        //$this->form->addFields( [ new TLabel('Id'), $id ] );        
        $this->form->addFields( [ $id ] );
        
        $this->form->addFields( [new TFormSeparator('Dados do Aluno(a)')] );

        
        $row = $this->form->addFields( [ new TLabel('Cod. Aluno'), $Codaluno ]);
        $row->layout = ['col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Nome'), $NomeAluno ],
                                       [ new TLabel('Identificação Civil'), $NomeIdentificacaoCivil ]);
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Data Nasc.'), $Datanascimento ],
                                       [ new TLabel('Naturalidade'), $Naturalidade ],
                                       [ new TLabel('UF'), $NaturalidadeUF ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('CPF'), $CPF ],
                                       [ new TLabel('RG'), $Rg ],
                                       [ new TLabel('Orgão expedidor'), $RgOrgaoExpedidor ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Endereço'), $Endereco ],
                                       [ new TLabel('Nº'), $EnderecoNumero ],
                                       [ new TLabel('Bairro'), $Bairro ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('CEP'), $Cep ],
                                       [ new TLabel('Cidade'), $CodCidade ],
                                       [ new TLabel('UF'), $Uf ] );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Nacionalidade'), $Nacionalidade ],
                                       [ new TLabel('Telefone'), $Telefone ],
                                       [ new TLabel('Estado Civil'), $EstadoCivil ],
                                       [ new TLabel('Profissão'), $Profissao ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        
        $this->form->addFields( [ new TLabel('')]);

        $this->form->addFields( [new TFormSeparator('Dados do Responsável')] );

        
        $row = $this->form->addFields( [ new TLabel('Cod. Responsavel'), $CodResponsavel ],
                                       [ new TLabel('Nome'), $NomeResponsavel ]);
        $row->layout = ['col-sm-2', 'col-sm-10'];
        
        $row = $this->form->addFields( [ new TLabel('RG'), $RgResponsavel ],
                                       [ new TLabel('CPF'), $CPFResponsavel ],
                                       [ new TLabel('Endereço'), $RuaResponsavel ],
                                       [ new TLabel('Nº'), $NumResponsavel ]);
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-4', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Bairro'), $BairroResponsavel ],
                                       [ new TLabel('Cidade'), $CidadeResponsavel ],
                                       [ new TLabel('UF'), $UfResponsavel ],
                                       [ new TLabel('CEP'), $CEPResponsavel ]);
        $row->layout = ['col-sm-3', 'col-sm-5', 'col-sm-2', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Telefone'), $TelResponsavel ],
                                       [ new TLabel('Email'), $EmailResponsavel ]);
        $row->layout = ['col-sm-4', 'col-sm-8'];


        $this->form->addFields( [ new TLabel('')]);

        $this->form->addFields( [new TFormSeparator('Dados Financeiros')] );


        $row = $this->form->addFields( [ new TLabel('Mant.'), $RazaoSocial ],
                                       [ new TLabel('IES'), $NomeFantasia ],
                                       [ new TLabel('Cód. IES'), $CodEntidade ] );
        $row->layout = ['col-sm-5', 'col-sm-5', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Cod. Curso'), $CodCurso ],
                                       [ new TLabel('Nome Curso'), $NomeCurso ]);
        $row->layout = ['col-sm-4', 'col-sm-8'];

        $row = $this->form->addFields( [ new TLabel('Ano Matrícula'), $AnoMatricula ],
                                       [ new TLabel('Semestre Matrícula'), $SemestreMatricula ],
                                       [ new TLabel('Período'), $Periodo ],
                                       [ new TLabel('Etapa Matrícula'), $EtapaMatricula ]);
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];


        $row = $this->form->addFields( [ new TLabel('Início prest. serv.'), $InicioPrestServico ],
                                       [ new TLabel('Término prest. serv.'), $TerminoPrestServico ],
                                       [ new TLabel('Valor Total'), $ValorAnoSem ],
                                       [ new TLabel('Valor por extenso'), $ValorAnoSemExt ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-3', 'col-sm-5'];


        $row = $this->form->addFields( [ new TLabel('Data 1ª Parcela'), $DataPrimeiraParcela ],
                                       [ new TLabel('Valor 1ª Parcela'), $ValorParc1 ],
                                       [ new TLabel('Valor por extenso'), $ValorParc1Ext ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-6'];

        
        $row = $this->form->addFields( [ new TLabel('Valor Demais Parcelas'), $ValorDmsParc ],
                                       [ new TLabel('Valor por extenso'), $ValorDmsParcExt ]);
        $row->layout = ['col-sm-3', 'col-sm-9'];
        
        $row = $this->form->addFields( [ new TLabel('Desconto Comercial'), $DescontoComercial ],
                                       [ new TLabel('Data do Contrato por extenso'), $DataFinalContrato ]);
        $row->layout = ['col-sm-3', 'col-sm-9'];
        
       

        // set sizes
        $id->setSize('50%');
        $Codaluno->setSize('10%');
        $NomeAluno->setSize('100%');
        $Datanascimento->setSize('100%');
        $CPF->setSize('100%');
        $Rg->setSize('100%');
        $RgOrgaoExpedidor->setSize('100%');
        $AnoMatricula->setSize('100%');
        $SemestreMatricula->setSize('100%');
        $Periodo->setSize('100%');
        $EtapaMatricula->setSize('100%');
        $CodCurso->setSize('100%');
        $NomeCurso->setSize('100%');
        $SituacaoMatricula->setSize('100%');
        $CodEntidade->setSize('100%');
        $CodEntidade->setEditable(FALSE);
        $Nacionalidade->setSize('100%');
        $EstadoCivil->setSize('100%');
        $Profissao->setSize('100%');
        $Endereco->setSize('100%');
        $EnderecoNumero->setSize('100%');
        $Bairro->setSize('100%');
        $CodCidade->setSize('100%');
        $Cep->setSize('100%');
        $Uf->setSize('100%');
        $RazaoSocial->setSize('100%');
        $NomeFantasia->setSize('100%');
        $Telefone->setSize('100%');
        $Naturalidade->setSize('100%');
        $NaturalidadeUF->setSize('100%');
        //$Telefone2->setSize('100%');
        //$Telefone3->setSize('100%');
        $NomeIdentificacaoCivil->setSize('100%');
        $CodResponsavel->setSize('100%');
        $DescontoComercial->setSize('100%');
        $CodResponsavel->setSize('100%');
        $NomeResponsavel->setSize('100%');
        $RuaResponsavel->setSize('100%');
        $NumResponsavel->setSize('100%');
        $BairroResponsavel->setSize('100%');
        $RgResponsavel->setSize('100%');
        $CPFResponsavel->setSize('100%');
        $CEPResponsavel->setSize('100%');
        $CidadeResponsavel->setSize('100%');
        $TelResponsavel->setSize('100%');
        $EmailResponsavel->setSize('100%');
        $ValorAnoSem->setSize('100%');
        $ValorAnoSemExt->setSize('100%');
        $ValorParc1->setSize('100%');
        $ValorParc1Ext->setSize('100%');
        $ValorDmsParc->setSize('100%');
        $ValorDmsParcExt->setSize('100%');
        $StatusContrato->setSize('100%');
        $DataFinalContrato->placeholder = "Ex: Ituverava-SP, 01 de janeiro de 2022";
        $DataPrimeiraParcela->setMask('dd/mm/yyyy');
        $DataPrimeiraParcela->setDatabaseMask('yyyy-mm-dd');
        

        $InicioPrestServico->addValidation('Início prest. serv.', new TRequiredValidator);
        $TerminoPrestServico->addValidation('Término prest. serv.', new TRequiredValidator);
        $DataPrimeiraParcela->addValidation('Data 1ª Parcela', new TRequiredValidator);
        $DataFinalContrato ->addValidation('Data do Contrato por extenso', new TRequiredValidator);       
        $DescontoComercial->addValidation('Desconto Comercial', new TRequiredValidator);


        $exit_action = new TAction(array($this, 'onExitAction'));
        $Codaluno->setExitAction($exit_action);


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }


        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(('Listar Contratos'),  new TAction(['ContratoFinanceiroAdm', 'onReload']), 'fa:list black');

       
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
      
        parent::add($container);
    }


    public function onSave( $param )
    {
        //var_dump($param);
       // die();

        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            $object = new ContratoDadosAluno;  // create an empty object

            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));

            $data->system_user_id = $logged->id; 
            $data->DataRegistro = date('Y-m-d');
            $data->StatusContrato = ('Pendente de Validação Pelo Aluno');

            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
         

            // get the generated Codaluno
            $data->id = $object->id;
         
            $this->form->setData($data); // fill form data

            TTransaction::close(); // close the transaction
                       
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));

            $this->form->clear();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }


    public function onClear( $param )
    {
        $this->form->clear();
    }


    function loadPage()
    {}
    

    public static function onExitAction($param) //INSERE NOME, EMAIL E DADOS DA MATRÍCULA
    {
        $Cod_Aluno = $param['Codaluno'];

      
        try
        {
            TTransaction::open('dados_fei');
       
            $criteria = new TCriteria;
            $criteria->add( new TFilter( 'Codaluno', '=', $Cod_Aluno));
 
            $object = VwAlunoMatriculaEtapa::getObjects($criteria);
       
            $ultimo = end($object);
            $obj = new StdClass;
            $obj->Datanascimento            = TDate::date2br($ultimo->Datanascimento);
            $obj->CPF                       = $ultimo->CPF;
            $obj->Rg                        = $ultimo->Rg;
            $obj->RgOrgaoExpedidor          = $ultimo->RgOrgaoExpedidor;
            $obj->AnoMatricula              = $ultimo->AnoMatricula;
            $obj->SemestreMatricula         = $ultimo->SemestreMatricula;
            $obj->Periodo                   = $ultimo->Periodo;
            $obj->EtapaMatricula            = $ultimo->EtapaMatricula;
            $obj->CodCurso                  = $ultimo->CodCurso;
            $obj->NomeCurso                 = $ultimo->NomeCurso;
            $obj->Nacionalidade             = $ultimo->Nacionalidade;
            $obj->EstadoCivil               = $ultimo->EstadoCivil;
            $obj->Endereco                  = $ultimo->Endereco;
            $obj->EnderecoNumero            = $ultimo->EnderecoNumero;
            $obj->Bairro                    = $ultimo->Bairro;
            $obj->CodCidade                 = $ultimo->cidadealuno;
            $obj->Uf                        = $ultimo->Uf;
            $obj->Cep                       = $ultimo->Cep;
            $obj->Telefone                  = $ultimo->Telefone;
            $obj->Naturalidade              = $ultimo->Naturalidade;
            $obj->NaturalidadeUF            = $ultimo->NaturalidadeUF;
            $obj->NomeIdentificacaoCivil    = $ultimo->NomeIdentificacaoCivil;
            $obj->CodResponsavel            = $ultimo->CodResponsavel;
            $obj->CodEntidade               = $ultimo->CodEntidade;
            $obj->Profissao                 = $ultimo->Profissao;
            $obj->RazaoSocial               = $ultimo->RazaoSocial;
            $obj->NomeFantasia              = $ultimo->NomeFantasia;
            

            $VerificaPeriodo = $ultimo->Periodo;

            if($VerificaPeriodo == 'I'){
                $obj->Periodo = "INTEGRAL";
            }

            if($VerificaPeriodo == 'N'){
                $obj->Periodo = "NOTURNO";
            }

            if($VerificaPeriodo == 'M'){
                $obj->Periodo = "MANHÃ";
            }

            if($VerificaPeriodo == 'T'){
                $obj->Periodo = "TARDE";
            }


            $responsavel = $ultimo->CodResponsavel;

            $object_resp = new FiResponsavel($responsavel);


            $obj->NomeResponsavel = $object_resp->Nome; 
            $obj->RgResponsavel = $object_resp->Rg;
            $obj->CPFResponsavel = $object_resp->CPF;
            $obj->RuaResponsavel = $object_resp->Endereco;
            $obj->NumResponsavel = $object_resp->EnderecoNumero;
            $obj->BairroResponsavel = $object_resp->Bairro;
            $obj->EmailResponsavel = $object_resp->email;
            $obj->CidadeResponsavel = $object_resp->cidade_responsavel;
            $obj->CEPResponsavel = $object_resp->Cep;
            $obj->TelResponsavel = $object_resp->Telefone1;
            $obj->UfResponsavel = $object_resp->cidade_responsavel->Uf;   
   
            TTransaction::close();

            TForm::sendData('form_ContratoFinanceiroListMatricula', $obj);

            $curso = $ultimo->CodCurso;
            $turno = $ultimo->Periodo;
            $ano = $ultimo->AnoMatricula;
          

            /*TTransaction::open('Felabs_DB');

            $object_curso = new ContratoFinanceiro($curso);


            $obj_curso = new stdclass();
            $obj_curso->ValorAnoSem     = $object_curso->valor_total; 
            $obj_curso->ValorAnoSemExt  = $object_curso->valor_total_extenso; 
            $obj_curso->ValorParc1      = $object_curso->valor_primeira_parcela; 
            $obj_curso->ValorParc1Ext   = $object_curso->varlor_prim_parcela_extenso; 
            $obj_curso->ValorDmsParc    = $object_curso->valor_demais_parcelas;
            $obj_curso->ValorDmsParcExt = $object_curso->valor_dms_parcelas_extenso;

            TTransaction::close();    

            TForm::sendData('form_ContratoFinanceiroListMatricula', $obj_curso);*/
            
            
            /*Troca - chave primária ContratoFinanceiro de curso_id para id*/
            TTransaction::open('Felabs_DB');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('curso_id', '=', $curso));
            $criteria->add(new TFilter('turno', '=', $turno));
            $criteria->add(new TFilter('ano_vigente', '=', $ano));
           // echo $criteria->dump();
            
            $object_curso = ContratoFinanceiro::getObjects($criteria);
          //  var_dump( $object_curso );
            //die();
            
            $ultimo_contrato = end($object_curso);
            
            $obj_curso = new StdClass();
            $obj_curso->ValorAnoSem     = $ultimo_contrato->valor_total; 
            $obj_curso->ValorAnoSemExt  = $ultimo_contrato->valor_total_extenso;
            $obj_curso->ValorParc1      = $ultimo_contrato->valor_primeira_parcela;
            $obj_curso->ValorParc1Ext   = $ultimo_contrato->varlor_prim_parcela_extenso;
            $obj_curso->ValorDmsParc    = $ultimo_contrato->valor_demais_parcelas;
            $obj_curso->ValorDmsParcExt = $ultimo_contrato->valor_dms_parcelas_extenso;
            
            TTransaction::close();
            
            TForm::sendData('form_ContratoFinanceiroListMatricula', $obj_curso);
        }
        catch (Exception $e)
        {
            // does nothing
        }
    }


   /* public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('dados_fei'); // open a transaction
                $object = new VwAlunoMatriculaEtapa($key); // instantiates the Active Record

                $criteria = new TCriteria;
                $criteria->add( new TFilter( 'Codaluno', '=', ($param['key']) ));
            
                $objects = VwAlunoMatriculaEtapa::getObjects($criteria);
                
                $ultimo = end($objects);

                var_dump($ultimo);

                $ultimo->Datanascimento = TDate::date2br($ultimo->Datanascimento);

                $curso = $ultimo->CodCurso;


                TTransaction::open('Felabs_DB');

                $object_curso = new ContratoFinanceiro($curso);


                $obj_curso = new stdclass();
                $obj_curso->ValorAnoSem     = $object_curso->valor_total; 
                $obj_curso->ValorAnoSemExt  = $object_curso->valor_total_extenso; 
                $obj_curso->ValorParc1      = $object_curso->valor_primeira_parcela; 
                $obj_curso->ValorParc1Ext   = $object_curso->varlor_prim_parcela_extenso; 
                $obj_curso->ValorDmsParc    = $object_curso->valor_demais_parcelas;
                $obj_curso->ValorDmsParcExt = $object_curso->valor_dms_parcelas_extenso;
             

                TForm::sendData('form_ContratoFinanceiroListMatricula',$obj_curso);

                $this->form->setData($ultimo); // fill the form

                TTransaction::close(); // close the transaction

                TTransaction::close();
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
    }*/
}

