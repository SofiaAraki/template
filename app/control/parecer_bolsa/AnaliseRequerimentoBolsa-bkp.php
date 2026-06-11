<?php
/**
 * AnaliseRequerimentoBolsa Master/Detail
 * @author  <your name here>
 */
class AnaliseRequerimentoBolsa extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_AnaliseBolsa');
        $this->form->setFormTitle('Requerimento de Bolsa - Análise Assistente Social');
        $this->form->setClientValidation(true);
        //$this->html = new THtmlRenderer('app/documents/Parecer_Bolsa_FE.html');
        
        
        //$this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $curso = new TEntry('curso');
        $ciclo = new TEntry('ciclo');
        $periodo = new TEntry('periodo');
        $unidade = new TEntry('system_unit');
        $rg = new TEntry('rg');
        $cpf = new TEntry('cpf');
        $data_nascimento = new TEntry('data_nascimento');
        $estado_civil = new TEntry('estado_civil');
        $profissao = new TEntry('profissao');
        $endereco = new TEntry('endereco');
        $endereco_numero = new TEntry('endereco_numero');
        $endereco_complemento = new TEntry('endereco_complemento');
        $cidade = new TEntry('cidade');
        $estado = new TEntry('estado');
        $cep = new TEntry('cep');
        $telefone = new TEntry('telefone');
        $celular = new TEntry('celular');
        $telefone_trabalho = new TEntry('telefone_trabalho');
        $email = new TEntry('email');
        $data_reg = new TEntry('data_reg');
        $situacao = new TCombo('situacao');
            $items_situacao = [ 'Aberto'=>'Aberto',
                                'Aguardando assinaturas'=> 'Aguardando assinaturas',
                                'Deferido'=>'Deferido',
                                'Desclassificado'=>'Desclassificado', 
                                'Em Análise'=>'Em Análise', 
                                'Indeferido'=>'Indeferido',
                                'Indevido'=>'Indevido',                                 
                                'Solicitar correção'=>'Solicitar correção',
                                'Reprovado'=>'Reprovado'
                                ];
            $situacao->addItems($items_situacao);
        $moradia = new TEntry('moradia');
        $moradia_aluno = new TEntry('moradia_aluno');
        $veiculo_aluno = new TEntry('veiculo_aluno');
        $ensino_aluno = new TEntry('ensino_aluno');
        $obs = new TText('obs');
        $renda_familiar = new TNumeric('renda_familiar', '2', ',', '.' );
        $n_pessoa = new TEntry('n_pessoa');
        $renda_percapita = new TNumeric('renda_percapita', '2', ',', '.' );
        $rf_salario_minimo = new TNumeric('rf_salario_minimo', '2', ',', '.' );
        $rp_salario_minimo = new TNumeric('rp_salario_minimo', '2', ',', '.' );
        $data_final = new TEntry('data_final');
        $bairro = new TEntry('bairro');
        $cad_unico = new TEntry('cad_unico');
        $obs_ass_social = new TText('obs_ass_social');
        $outra_graduacao = new TEntry('outra_graduacao');
        $graduacao_anterior = new TEntry('graduacao_anterior');
        $renda_familiar_apurada = new TNumeric('renda_familiar_apurada', '2', ',', '.' );
        $n_pessoas_apurado = new TEntry('n_pessoas_apurado');
        $renda_percapita_apurada = new TNumeric('renda_percapita_apurada','2', ',', '.');
        $rf_salario_minimo_apurada = new TNumeric('rf_salario_minimo_apurada','2', ',', '.');
        $rp_salario_minimo_apurada = new TNumeric('rp_salario_minimo_apurada','2', ',', '.');
        $salario_minimo_atual = new TNumeric('salario_minimo_atual','2', ',', '.');
        
        $filename = new TButton('filename');
        $filename->class = 'btn btn-info btn-lg';
        $filename->setImage('fa:cloud-download-alt fa-lg green');
        $filename->setAction(new TAction(array($this, 'onDownloadArquivo')),'Baixar Documentos'  );
       

        
        $analise_check    = new TEntry('analise_check');
        $percentual_bolsa    = new TEntry('percentual_bolsa');
        
        $situacaofinal_bolsa = new TCombo('situacaofinal_bolsa');
        $items_deferimento = [  'DEFERIDO para 50%'=>'DEFERIDO para 50%', 
                                'DEFERIDO para 100%'=>'DEFERIDO para 100%', 
                                'INDEFERIDO'=> 'INDEFERIDO'];
        $situacaofinal_bolsa->addItems($items_deferimento);

        $tipo_req  = new TRadioGroup('tipo_req');
        $tipo_req->setLayout('horizontal');
        $tipo_req->setUseButton();
        $itemsReq = [   'Aluno Ingressante'=>'Aluno Ingressante &nbsp &nbsp  ', 
                        'Renovação de Bolsa'=>'Renovação de Bolsa'];
        $tipo_req->addItems($itemsReq);

        $documentos_check    = new TRadioGroup('documentos_check');
        $documentos_check->setLayout('horizontal');
        $documentos_check->setUseButton();
        $items2 = [ 'Sim'=>'Sim ', 
                    'Não'=>'Não', 
                    'SC'=>'Solicitar Correção'];
        $documentos_check->addItems($items2);
        $documentos_check->setChangeAction(new TAction(array($this, 'onChangeRadio')));
        $documentos_check->addValidation( 'Situação dos Documentos', new TRequiredValidator);

        $obs_final_assistente = new TText('obs_final_assistente');
        $data_parecer_assist_social = new TEntry('data_parecer_assist_social');

        TTransaction::open('Felabs_DB');
        
        ///VERIFICA SE É ALUNO INGRESSANTE OU RENOVAÇÃO DE BOLSA///
        $object = new ReqBolsaAluno($param['key']);
        $etapa_curso = $object->ciclo;

            if ($etapa_curso == "1º ciclo") 
            {
                $tipo_req->setValue('Aluno Ingressante');
            }
            else 
            {
                $tipo_req->setValue('Renovação de Bolsa');
            }
        
        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_item_membro = new TText('detail_item_membro');
        $detail_nome = new TText('detail_nome');
        $detail_idade = new TEntry('detail_idade');
        $detail_profissao = new TText('detail_profissao');
        $detail_salario = new TEntry('detail_salario');
        $detail_local_trabalho = new TText('detail_local_trabalho');
        $detail_data_reg = new TEntry('detail_data_reg');
        $detail_rg = new TEntry('detail_rg');
        $detail_cpf = new TEntry('detail_cpf');
       

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        //$system_user_id->setEditable(FALSE);
        $nome ->setEditable(FALSE);       
        $curso ->setEditable(FALSE);        
        $ciclo ->setEditable(FALSE);        
        $periodo ->setEditable(FALSE);
        $unidade ->setEditable(FALSE);
        $rg ->setEditable(FALSE);     
        $cpf ->setEditable(FALSE);      
        $data_nascimento ->setEditable(FALSE);
        $estado_civil ->setEditable(FALSE);
        $profissao->setEditable(FALSE);
        $endereco ->setEditable(FALSE);
        $endereco_numero ->setEditable(FALSE);
        $endereco_complemento ->setEditable(FALSE);
        $cidade ->setEditable(FALSE);
        $estado ->setEditable(FALSE);        
        $cep ->setEditable(FALSE);
        $telefone ->setEditable(FALSE);
        $celular ->setEditable(FALSE);
        $telefone_trabalho ->setEditable(FALSE);
        $email ->setEditable(FALSE);
        $data_reg ->setEditable(FALSE);
        $moradia->setEditable(FALSE);
        $moradia_aluno ->setEditable(FALSE);
        $veiculo_aluno ->setEditable(FALSE);
        $ensino_aluno ->setEditable(FALSE);
        $renda_familiar ->setEditable(FALSE);
        $n_pessoa ->setEditable(FALSE);
        $renda_percapita ->setEditable(FALSE);
        $data_final ->setEditable(FALSE);
        $bairro ->setEditable(FALSE);        
        $cad_unico ->setEditable(FALSE);
        $outra_graduacao ->setEditable(FALSE);
        $graduacao_anterior ->setEditable(FALSE);
        $rf_salario_minimo->setEditable(FALSE);
        $rp_salario_minimo->setEditable(FALSE);

        $salario_minimo_atual->setValue(1212);
        
        $id->setSize('18%');
        $rf_salario_minimo_apurada->setEditable(FALSE);
        $n_pessoas_apurado->setMask('999');
        $renda_percapita_apurada->setEditable(FALSE);
        $rp_salario_minimo_apurada->setEditable(FALSE);

        $nome->style = 'font-size: 17pt';
        $curso->style = 'font-size: 17pt';
        $curso->setSize('100%');
        $ciclo->style = 'font-size: 17pt';
        $periodo->style = 'font-size: 17pt';
        $unidade->style = 'font-size: 17pt';
        $n_pessoas_apurado->style = 'text-align:right';
        $renda_percapita_apurada->style = 'text-align:right';
        $percentual_bolsa->style = 'min-width:272px; color: red; font-size: 12pt;font-weight: bold';
        $analise_check->style = 'min-width:80px; color: red; font-size: 12pt;font-weight: bold';
        $situacao->style = 'min-width:250px; color: red; font-size: 14pt;font-weight: bold';
        $situacaofinal_bolsa->style = 'min-width:250px; color: red; font-size: 14pt;font-weight: bold';
        $percentual_bolsa->setEditable(FALSE);
        $analise_check->setEditable(FALSE);
        


        $n_pessoas_apurado->setExitAction(new TAction(array($this, 'onUpdateRenda')));
        
        
        // master fields
        $this->form->addContent( ['<h4>DADOS PESSOAIS </h4><hr>'] );

        $this->form->addFields( [new TLabel('ID:')], [$id]);
        $this->form->addFields( [new TLabel('Nome:')], [$nome]  );

        $this->form->addFields( [new TLabel('RG:')], [$rg],
                                [new TLabel('CPF:')], [$cpf],
                                [new TLabel('Data de Nascimento:')], [$data_nascimento] );

        $this->form->addFields( [new TLabel('Estado Civil:')], [ $estado_civil],
                                [new TLabel('Profissão:')], [$profissao],
                                [''] );
        
        $this->form->addFields( [new TLabel('Endereço:')], [$endereco]);
        
        $this->form->addFields( [new TLabel('Número:')], [$endereco_numero],
                                [new TLabel('Complemento:')], [$endereco_complemento],
                                [new TLabel('CEP:')], [$cep]
                             );

        $this->form->addFields( [new TLabel('Bairro:')], [$bairro],
                                [new TLabel('Cidade:')], [$cidade],
                                [new TLabel('Estado:')], [ $estado] );


        $this->form->addFields( [new TLabel('Telefone:')], [$telefone],
                                [new TLabel('Celular:')], [ $celular] ,
                                [new TLabel('Telefone Trabalho:')], [$telefone_trabalho] );
        
        $this->form->addFields( [new TLabel('Email:')], [$email]);
        
                   // detail fields
                   $this->form->addContent( ['<br><h4> - Núcleo Familiar</h4><hr>'] );
                   $this->form->addFields( [$detail_uniqid] );
                   $this->form->addFields( [$detail_id] );
           
                   $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
                   $this->detail_list->setId('ReqBolsaAlunoItem_list');
                   $this->detail_list->generateHiddenFields();
                   $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
                   
                   // items
                   $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
                   $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
                   $this->detail_list->addColumn( new TDataGridColumn('item_membro', 'Membro', 'left', 100) );
                   $this->detail_list->addColumn( new TDataGridColumn('nome', 'Nome', 'left', 100) );
                   $this->detail_list->addColumn( new TDataGridColumn('rg', 'RG', 'left', 100) );
                   $this->detail_list->addColumn( new TDataGridColumn('cpf', 'CPF', 'left', 100) );
                   $this->detail_list->addColumn( new TDataGridColumn('idade', 'Idade', 'left', 100) );
                   $this->detail_list->addColumn( new TDataGridColumn('profissao', 'Profissão', 'left', 100) );
                   $salario = $this->detail_list->addColumn( new TDataGridColumn('salario', 'Salário', 'left', 100) );
                   $this->detail_list->addColumn( new TDataGridColumn('local_trabalho', 'Local Trabalho', 'left', 100) );
                   //$this->detail_list->addColumn( new TDataGridColumn('data_reg', 'Data Reg', 'left', 100) );
   
                   $salario->setTotalFunction( function($values) 
                   { 
                       return array_sum((array) $values);
                   }); 
           
                   $salario->setTransformer(function($value, $object, $row) 
                   {
                       if (!$value)
                       {
                           $value = 0;
                       }
                       return "R$ " . number_format($value, 2, ",", ".");
                   });
                     
           $this->detail_list->createModel();
   
           $panel = new TPanelGroup;
           $panel->add($this->detail_list);
           $panel->getBody()->style = 'overflow-x:auto';
           $this->form->addContent( [$panel] );

        $this->form->addContent( ['<br><h4>INFORMAÇÕES</h4><hr>'] );

        $this->form->addFields( [new TLabel('A família reside em moradia:')], [$moradia],
                                [new TLabel('O aluno reside em:')], [$moradia_aluno],
                                [new TLabel('A família possui veículo:')], [$veiculo_aluno] );
      
        $row = $this->form->addFields( [new TLabel('(Se aluno da Educação Básica) No ano anterior estudou em escola:<br>(Se aluno da Educação Superior) Concluiu o ensino médio em escola:')],[$ensino_aluno]);       
        $row->layout = ['col-sm-6 control-label', 'col-sm-6' ];

        $this->form->addFields( [new TLabel('Cad. Unico:')], [$cad_unico],
                                [new TLabel('Data Final:')], [$data_final],
                                [new TLabel('Data Parecer:')], [$data_parecer_assist_social] );
       

        $this->form->addContent( ['<br><br><h4>CURSO </h4><hr>'] );

        $this->form->addFields( [new TLabel('Curso:', '', 12, 'b')], [$curso]);
        $this->form->addFields( [new TLabel('Ciclo:', '', 12, 'b')], [$ciclo],
                                [new TLabel('Período:', '', 12, 'b')], [$periodo],
                                [new TLabel('Unidade:', '', 12, 'b')], [$unidade]
                            );

        $this->form->addContent( ['<br><br><h4>ANÁLISE SOCIOECONÔMICA DO REQUERIMENTO </h4><hr>'] ); 

        $this->form->addFields( [new TLabel('Tipo de Requerimento:', '', 12, 'b')],  [$tipo_req]);
        
        $this->form->addFields( [new TLabel('O aluno possui outra graduação em Ensino Superior:')], [$outra_graduacao],
                                [new TLabel('Se sim, qual:')], [$graduacao_anterior]  );


        
        $this->form->addContent( ['<br><br><h4>PARECER TÉCNICO - ASSISTENTE SOCIAL </h4><hr>'] );     

        $this->form->addFields( [new TLabel('Documentos Enviados pelo Aluno:')], [$filename] );
        $this->form->addFields( [new TLabel('O aluno entregou/anexou toda a documentação necessária para a comprovação da condição socioeconômica exigida para obtenção/manutenção da condição de bolsista integral ou parcial?', 'red', 11, 'b')]);
        $this->form->addFields( [new TLabel('Selecione a situação *', 'red')],[$documentos_check]);

        $this->form->addFields( [new TLabel('Observações internas')], [$obs_ass_social] );
        $this->form->addFields( [new TLabel('Observações enviadas para o Aluno')], [$obs] );

        //$this->form->addFields( [new TLabel('Data Reg')], [$data_reg] );
                 
        $this->form->addContent( ['<br><h4>INFORMAÇÕES FINANCEIRAS - Citadas pelo aluno </h4><hr>'] );
                
        $this->form->addFields( [new TLabel('Renda Familiar informada pelo aluno (ou responsável) em R$:')], [$renda_familiar],
                                [new TLabel('Quantidade de pessoas do Grupo Familiar:')], [$n_pessoa],
                                [new TLabel('Renda Per Capita em R$:')], [$renda_percapita] );

        $this->form->addFields( [new TLabel('Renda Total informada em quantidade de salários mínimos:')], [$rf_salario_minimo],
                                [new TLabel('Renda Per Capita em quantidade de salários mínimos:')], [$rp_salario_minimo],
                                ['']  );
        

        
        $this->form->addContent( ['<br><br><h4>MEMÓRIA DE CÁLCULO CONFORME LEGISLAÇÃO EM VIGOR </h4><hr>'] ); 

        $this->form->addFields( [new TLabel('(A) Salário Mínimo na Data da Análise','#FF0000', 11, 'b')]);
        $this->form->addFields( [ $salario_minimo_atual],['']);
        $this->form->addFields( [new TLabel('(B) Renda Familiar Apurada pela Assistente Social:','#FF0000', 11, 'b')]);
        $this->form->addFields( [$renda_familiar_apurada],[''] );
        $this->form->addFields( [new TLabel('(D) Quantidade de Pessoas Grupo Familiar Apurado pela Assistente Social:','#FF0000', 11, 'b')]);
        $this->form->addFields( [ $n_pessoas_apurado],[''] );
        $this->form->addFields( [new TLabel('(C) Renda Total informada em quantidade de salários mínimos:','#FF0000', 11, 'b')]);
        $this->form->addFields( [$rf_salario_minimo_apurada],['']);
        $this->form->addFields( [new TLabel('(E) Renda Percapita em R$ calculado pela Assistente Social:','#FF0000', 11, 'b')]);
        $this->form->addFields( [$renda_percapita_apurada],['']);
        $this->form->addFields( [new TLabel('(F) Renda Per Capita em quantidade de salários mínimos:','#FF0000', 11, 'b')]);
        $this->form->addFields( [$rp_salario_minimo_apurada],['']);

        $this->form->addContent( ['<br><br><h4>RESULTADO DA ANÁLISE </h4><hr>'] ); 
        
        $row = $this->form->addFields( [new TLabel('Conforme análise o aluno:')], [$analise_check], 
                                [new TLabel('os requisitos para obtenção de Bolsa de Estudo Filantrópica')]);
        $row->layout = ['col-sm-3 control-label', 'col-sm-4', 'col-sm-5' ];

        $row =$this->form->addFields( [new TLabel(' no percentual de:')], [$percentual_bolsa],
                                [new TLabel('(Fundamento legal no verso)')] );
        $row->layout = ['col-sm-3 control-label', 'col-sm-4', 'col-sm-5' ];

        $row =$this->form->addFields( [new TLabel('<span style="font-size: 16px;color: red"><b>Parecer da Assistente Social:')], [$situacaofinal_bolsa]);
        $row->layout = ['col-sm-3 control-label', 'col-sm-4', 'col-sm-5' ];
        
        $this->form->addFields( [new TLabel('<span style="font-size: 16px;color: red"><br><b>Observações da Assistente Social:')]);
        $this->form->addFields( [$obs_final_assistente]);

        $this->form->addFields( [new TLabel('<p style="font-size: 16px;"><b><br>Situação: ')], [$situacao ] );
                  
        $btn = $this->form->addAction( 'Finalizar Parecer',  new TAction([$this, 'onSave']), 'fa:check fa-lg');
        $btn->class = 'btn btn-success btn-lg';      
        $btn2 = $this->form->addAction( 'Imprimir Parecer',  new TAction([$this, 'onPrint']), 'fa:file-pdf fa-lg');
        $btn2->class = 'btn btn-info btn-lg';  


        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        //$container->add($this->html);
        parent::add($container);
    }

        
    public static function onChangeRadio($param)
    {
            $format  = $param['documentos_check'];

            $obj2 = new StdClass;

            switch ($format)
                {
                    case 'Sim':
                        $obj2->situacao = "Em Análise";
                        $obj2->analise_check = "";
                        $obj2->percentual_bolsa = "";
                        $obj2->situacaofinal_bolsa = '';
                        break;
                    case 'Não':
                        $obj2->situacao = "Reprovado";
                        $obj2->analise_check = "NÃO CUMPRE";
                        $obj2->percentual_bolsa = "NÃO PREENCHE OS REQUISITOS";
                        $obj2->situacaofinal_bolsa = 'INDEFERIDO';
                        break;
                    case 'SC':
                        $obj2->situacao = "Solicita Correção";
                        $obj2->analise_check = "";
                        $obj2->percentual_bolsa = "";
                        $obj2->situacaofinal_bolsa = '';
                        break;
                   
                }
                TForm::sendData('form_AnaliseBolsa', $obj2);
        
    }

    public static function onUpdateRenda ($param)
    {
        
        $n_pessoas_apurado = str_replace(['.', ','], ['', '.'], $param['n_pessoas_apurado']);
        $renda_familiar_apurada = str_replace(['.', ','], ['', '.'], $param['renda_familiar_apurada']);
        $rp_salario_minimo_apurada = str_replace(['.', ','], ['', '.'], $param['rp_salario_minimo_apurada']);
        $renda_percapita_apurada = str_replace(['.', ','], ['', '.'], $param['renda_percapita_apurada']);
        $salario_minimo_atual = str_replace(['.', ','], ['', '.'], $param['salario_minimo_atual']);
        
        $obj = new StdClass;
        $obj->rf_salario_minimo_apurada = number_format( ($renda_familiar_apurada/$salario_minimo_atual), 2, ',', '.');//CALCULA A RENDA TOTAL EM QNTD DE SALÁRIO MÍNIMO
        $obj->renda_percapita_apurada = number_format( ($renda_familiar_apurada/$n_pessoas_apurado), 2, ',', '.'); //CALCULA A RENDA PERCAPITA DA FAMILIA
        $obj->rp_salario_minimo_apurada = number_format( ( ($renda_familiar_apurada/$n_pessoas_apurado)/$salario_minimo_atual), 2, ',', '.'); // CALCULA A RENDA PERCAPITA EM QNTD DE SALARIO MINIMO


        //Substitui a vírgula pelo ponto para fazer comparação
        $obj->rp_salario_minimo_apurada = str_replace(',', '.', $obj->rp_salario_minimo_apurada);


        if ($obj->rp_salario_minimo_apurada <= 1.50) {
            $obj->analise_check = "CUMPRE";
            $obj->percentual_bolsa = "100%";    
        }
        elseif ($obj->rp_salario_minimo_apurada <= 3.00) {
            $obj->analise_check = "CUMPRE";
            $obj->percentual_bolsa = "50%";            
        }
        elseif ($obj->rp_salario_minimo_apurada > 3.00) {
            $obj->analise_check = "NÃO CUMPRE";
            $obj->percentual_bolsa = "NÃO PREENCHE OS REQUISITOS";
        }     


        //Volta a vírgula para exibição no formulário
        $obj->rp_salario_minimo_apurada = str_replace('.', ',', $obj->rp_salario_minimo_apurada);

        TForm::sendData('form_AnaliseBolsa', $obj);

    }

       
    public function onDownloadArquivo($param)
    {
        try
        {                      
                $id = $param['id'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new ReqBolsaAluno($id);

                $arquivo_aluno = $object->filename;

                if(!empty($arquivo_aluno))
                {              
                    parent::openFile($arquivo_aluno);

                    $this->form->setData( $this->form->getData() );
                        
                        $items  = ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $id)->load();
                
                        foreach( $items as $item )
                        {
                            $item->uniqid = uniqid();
                            $row = $this->detail_list->addItem( $item );
                            $row->id = $item->uniqid;
                        }
                
                        $this->form->setData($object);
                    TTransaction::rollback();
                }
                else
                {
                    new TMessage('info', 'Este requerimento não possui anexos');
                }
                
                
                TTransaction::close();
           
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    /**
     * Load Master/Detail data from database to form
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new ReqBolsaAluno($key);

                $object->data_final = TDate::date2br($object->data_final);
                $object->data_reg = TDate::date2br($object->data_reg);
                $object->data_parecer_assist_social = TDate::date2br($object->data_parecer_assist_social);
                

                $items  = ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('Felabs_DB');
            $prefs  = SystemPreference::getAllPreferences();
            $hoje = date('Y-m-d H:i:s');

    

            //TTransaction::setLogger(new TLoggerSTD);            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new ReqBolsaAluno;
            $master->fromArray( (array) $param);

            $email = $param['email'];

             
            $master->renda_familiar_apurada = str_replace(['.', ','], ['', '.'], $param['renda_familiar_apurada']);
            $master->rp_salario_minimo_apurada = str_replace(['.', ','], ['', '.'], $param['rp_salario_minimo_apurada']);
            $master->renda_percapita_apurada = str_replace(['.', ','], ['', '.'], $param['renda_percapita_apurada']);
            $master->salario_minimo_atual = str_replace(['.', ','], ['', '.'], $param['salario_minimo_atual']);

            $master->renda_familiar = str_replace(['.', ','], ['', '.'], $param['renda_familiar']);
            $master->renda_percapita = str_replace(['.', ','], ['', '.'], $param['renda_percapita']);
            $master->rf_salario_minimo = str_replace(['.', ','], ['', '.'], $param['rf_salario_minimo']);
            $master->rp_salario_minimo = str_replace(['.', ','], ['', '.'], $param['rp_salario_minimo']);
            $master->rf_salario_minimo_apurada = str_replace(['.', ','], ['', '.'], $param['rf_salario_minimo_apurada']);
            $master->rp_salario_minimo_apurada = str_replace(['.', ','], ['', '.'], $param['rp_salario_minimo_apurada']);
            $master->data_parecer_assist_social = $hoje;
            $master->data_final = date('Y-m-d H:i:s');

            

            $master->store();


            
            // ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $master->id)->delete();
            
            // if( $param['ReqBolsaAlunoItem_list_item_membro'] )
            // {
            //     foreach( $param['ReqBolsaAlunoItem_list_item_membro'] as $key => $item_id )
            //     {
            //         $detail = new ReqBolsaAlunoItem;
            //         $detail->item_membro  = $param['ReqBolsaAlunoItem_list_item_membro'][$key];
            //         $detail->nome  = $param['ReqBolsaAlunoItem_list_nome'][$key];
            //         $detail->idade  = $param['ReqBolsaAlunoItem_list_idade'][$key];
            //         $detail->profissao  = $param['ReqBolsaAlunoItem_list_profissao'][$key];
            //         $detail->salario  = $param['ReqBolsaAlunoItem_list_salario'][$key];
            //         $detail->local_trabalho  = $param['ReqBolsaAlunoItem_list_local_trabalho'][$key];
            //         $detail->data_reg  = $param['ReqBolsaAlunoItem_list_data_reg'][$key];
            //         $detail->rg  = $param['ReqBolsaAlunoItem_list_rg'][$key];
            //         $detail->cpf  = $param['ReqBolsaAlunoItem_list_cpf'][$key];
            //         $detail->req_bolsa_aluno_id = $master->id;
            //         $detail->store();
            //     }
            // }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_ReqBolsaAluno', (object) ['id' => $master->id]);

            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));

            //email aluno
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Aluno - FEAcadêmico');
            $mail->setSubject('Requerimento de Bolsa');
            $mail->setTextBody('Prezado(a) aluno(a), o status do seu Requerimento de Bolsa foi atualizado, por favor verifique o sistema Acadêmico.' ."\n". 'Acompanhe a situação através da Área do Aluno - FEAcadêmico.'."\n". 'Esta é uma mensagem automática. Solicitamos, por favor, não responder este e-mail.');  

            $mail->addAddress($email);          
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();

            $id_notif = $usuario;

            SystemNotification::register(
                                        $id_notif,
                                        'Novo status de Requerimento de Bolsa definido',
                                        'O status do seu Requerimento de Bolsa foi atualizado, verifique.',
                                        'class=ReqBolsaAlunoList',
                                        'Ver Requerimento',
                                        'far fa-list-alt green'
                                        );

        
            //TApplication::loadPage('ReqBolsaAlunoListGestor', 'onReload');

            $this->form->setData( $this->form->getData() );

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }

    
    public function onPrint($param)
    {
       
        try
        {   
            TTransaction::open('Felabs_DB');
            
            $id = $param['id'];
            $object = new ReqBolsaAluno($id);

            $this->html = new THtmlRenderer('app/documents/Parecer_Bolsa_FE.html');
            //echo '<pre>' , var_dump($param) , '</pre>';
           
            //die();
            $object = new stdClass;
            $object->nome       = $param['nome'];
            $object->curso      = $param['curso'];
            $object->ciclo      = $param['ciclo'];
            $object->periodo    = $param['periodo'];
            $object->cidade     = $param['cidade'];
            $object->unidade     = $param['system_unit'];
            $object->salario_minimo_atual       = $param['salario_minimo_atual'];
            $object->renda_familiar_apurada     = $param['renda_familiar_apurada'];
            $object->rf_salario_minimo_apurada  = $param['rf_salario_minimo_apurada'];
            $object->renda_percapita_apurada    = $param['renda_percapita_apurada'];
            $object->rp_salario_minimo_apurada  = $param['rp_salario_minimo_apurada'];
            $object->n_pessoas_apurado          = $param['n_pessoas_apurado'];
            $object->tipo_req           = $param['tipo_req'];
            $object->outra_graduacao    = $param['outra_graduacao'];
            $object->documentos_check   = $param['documentos_check'];
            $object->analise_check      = $param['analise_check'];
            $object->percentual_bolsa   = $param['percentual_bolsa'];
            $object->situacaofinal_bolsa= $param['situacaofinal_bolsa'];
            $object->analise_check      = $param['analise_check'];
            $object->analise_check      = $param['analise_check'];
            $object->obs_final_assistente       = $param['obs_final_assistente'];
            $object->data_parecer_assist_social = $param['data_parecer_assist_social'];

            // var_dump($object->data_parecer_assist_social);
            // die();
            
            $replace = array();
            $replace['object']  = $object;
            $this->html->enableSection('main', $replace);

            $contents = "<img src='http://localhost/xsd/cabecalho-fe.jpg'>";
            $contents = $this->html->getContents();
            
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf(['enable_remote' => true]);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/ReqBolsaAluno-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();

            $this->form->setData($this->form->getData());
                        
            $items  = ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $id)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                
                $this->form->setData($object);
            TTransaction::rollback();

            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function show()
    {
        $this->onEdit($param);
        parent::show();
    }

    
}
