<?php

class DiplomaEmissoraForm extends TPage
{
    protected $form;    
    private $system_unit_id;


    public function __construct( $param )
    {
        parent::__construct();
        
        
        //$this->system_unit_id para conseguir bloquear campo na edição sem perder o valor ou se cair num Exception
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalEmissora');
        $this->form->setFormTitle('<h4>Emissora</h4>');
        $this->form->setFieldSizes('100%');

    
        //Filtrar unidades (remove as opções 7, 9 e 11 = "Não existe no Genesi" e CNSC e VAN GOGH)
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_unit_codlegado', '<>', 0));
        $criteria->add(new TFilter('id', '<>', 1));
        $criteria->add(new TFilter('id', '<>', 8));
        
        
        // create the form fields
        $id = new THidden('id');
        $dados_mantenedora_id = new TDBCombo('dados_mantenedora_id', 'Felabs_DB', 'DiplomaDigitalMantenedora', 'id', 'razao_social');
        $nome = new TEntry('nome');
        $codigo_mec = new TEntry('codigo_mec');
        $cnpj = new TEntry('cnpj');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $codigo_municipio = new TSeekButton('codigo_municipio');
        $nome_municipio = new TEntry('nome_municipio');
        $uf = new TCombo('uf');
        $cep = new TEntry('cep');
        $opcao_credenciamento_emec = new TRadioGroup('opcao_credenciamento_emec');
        $credenciamento_tipo = new TCombo('credenciamento_tipo');
        $credenciamento_numero = new TEntry('credenciamento_numero');
        $credenciamento_data = new TDate('credenciamento_data');
        $credenciamento_veiculo_publicacao = new TEntry('credenciamento_veiculo_publicacao');
        $credenciamento_data_publicacao = new TDate('credenciamento_data_publicacao');
        $credenciamento_secao_publicacao = new TEntry('credenciamento_secao_publicacao');
        $credenciamento_pag_publicacao = new TEntry('credenciamento_pag_publicacao');
        $credenciamento_numero_DOU = new TEntry('credenciamento_numero_DOU');
        $credenciamento_numero_processo = new TEntry('credenciamento_numero_processo');
        $credenciamento_tipo_processo = new TEntry('credenciamento_tipo_processo');
        $credenciamento_data_cadastro = new TDate('credenciamento_data_cadastro');
        $credenciamento_data_protocolo = new TDate('credenciamento_data_protocolo');
        $opcao_recredenciamento_emec = new TRadioGroup('opcao_recredenciamento_emec');
        $recredenciamento_tipo = new TCombo('recredenciamento_tipo');
        $recredenciamento_numero = new TEntry('recredenciamento_numero');
        $recredenciamento_data = new TDate('recredenciamento_data');
        $recredenciamento_veiculo_publicacao = new TEntry('recredenciamento_veiculo_publicacao');
        $recredenciamento_data_publicacao = new TDate('recredenciamento_data_publicacao');
        $recredenciamento_secao_publicacao = new TEntry('recredenciamento_secao_publicacao');
        $recredenciamento_pag_publicacao = new TEntry('recredenciamento_pag_publicacao');
        $recredenciamento_numero_DOU = new TEntry('recredenciamento_numero_DOU');
        $recredenciamento_numero_processo = new TEntry('recredenciamento_numero_processo');
        $recredenciamento_tipo_processo = new TEntry('recredenciamento_tipo_processo');
        $recredenciamento_data_cadastro = new TDate('recredenciamento_data_cadastro');
        $recredenciamento_data_protocolo = new TDate('recredenciamento_data_protocolo');
        $opcao_renovacao_emec = new TRadioGroup('opcao_renovacao_emec');
        $renovacao_recredenciamento_tipo = new TCombo('renovacao_recredenciamento_tipo');
        $renovacao_recredenciamento_numero = new TEntry('renovacao_recredenciamento_numero');
        $renovacao_recredenciamento_data = new TDate('renovacao_recredenciamento_data');
        $renovacao_recredenciamento_veic_publ = new TEntry('renovacao_recredenciamento_veic_publ');
        $renovacao_recredenciamento_data_publ = new TDate('renovacao_recredenciamento_data_publ');
        $renovacao_recredenciamento_secao_publ = new TEntry('renovacao_recredenciamento_secao_publ');
        $renovacao_recredenciamento_pag_publ = new TEntry('renovacao_recredenciamento_pag_publ');
        $renovacao_recredenciamento_numero_DOU = new TEntry('renovacao_recredenciamento_numero_DOU');
        $renovacao_recredenciamento_numero_processo = new TEntry('renovacao_recredenciamento_numero_processo');
        $renovacao_recredenciamento_tipo_processo = new TEntry('renovacao_recredenciamento_tipo_processo');
        $renovacao_recredenciamento_data_cadastro = new TDate('renovacao_recredenciamento_data_cadastro');
        $renovacao_recredenciamento_data_protocolo = new TDate('renovacao_recredenciamento_data_protocolo');
        $this->system_unit_id = new TDBCombo('system_unit_id', 'Felabs_DB', 'SystemUnit', 'id', 'name', 'name', $criteria);
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        //UF
        $combo_uf = [];
        $combo_uf['AC'] = "AC";
        $combo_uf['AL'] = "AL";
        $combo_uf['AM'] = "AM";
        $combo_uf['AP'] = "AP";
        $combo_uf['BA'] = "BA";
        $combo_uf['CE'] = "CE";
        $combo_uf['DF'] = "DF";
        $combo_uf['ES'] = "ES";
        $combo_uf['GO'] = "GO";
        $combo_uf['MA'] = "MA";
        $combo_uf['MG'] = "MG";
        $combo_uf['MS'] = "MS";
        $combo_uf['MT'] = "MT";
        $combo_uf['PA'] = "PA";
        $combo_uf['PB'] = "PB";
        $combo_uf['PE'] = "PE";
        $combo_uf['PI'] = "PI";
        $combo_uf['PR'] = "PR";
        $combo_uf['RJ'] = "RJ";
        $combo_uf['RN'] = "RN";
        $combo_uf['RO'] = "RO";
        $combo_uf['RR'] = "RR";
        $combo_uf['RS'] = "RS";
        $combo_uf['SC'] = "SC";
        $combo_uf['SE'] = "SE";
        $combo_uf['SP'] = "SP";
        $combo_uf['TO'] = "TO";
        
        $uf->addItems($combo_uf);
        
        
        //Opção (Credenciamento, Recredenciamento e Renovação de Recredenciamento)
        $opcao_emec = [];
        $opcao_emec['Utilizar informações sobre ato regulatório'] = "Utilizar informações sobre ato regulatório";
        $opcao_emec['Utilizar informações sobre tramitação do processo'] = "Utilizar informações sobre tramitação do processo";
        
        $opcao_credenciamento_emec->addItems($opcao_emec);
        $opcao_recredenciamento_emec->addItems($opcao_emec);
        $opcao_renovacao_emec->addItems($opcao_emec);
        
        $opcao_credenciamento_emec->setChangeAction(new TAction(array($this, 'onOpcaoCredenciamentoChange')));
        $opcao_recredenciamento_emec->setChangeAction(new TAction(array($this, 'onOpcaoRecredenciamentoChange')));
        $opcao_renovacao_emec->setChangeAction(new TAction(array($this, 'onOpcaoRenovacaoChange')));
        
        
        //Tipo (Credenciamento, Recredenciamento e Renovação do Recredenciamento)
        $tipo = [];
        $tipo['Ato Próprio'] = "Ato Próprio";
        $tipo['Decreto'] = "Decreto";
        $tipo['Deliberação'] = "Deliberação";
        $tipo['Lei Estadual'] = "Lei Estadual";
        $tipo['Lei Federal'] = "Lei Federal";
        $tipo['Lei Municipal'] = "Lei Municipal";
        $tipo['Parecer'] = "Parecer";
        $tipo['Portaria'] = "Portaria";
        $tipo['Resolução'] = "Resolução";
        
        $credenciamento_tipo->addItems($tipo);
        $recredenciamento_tipo->addItems($tipo);
        $renovacao_recredenciamento_tipo->addItems($tipo);
        
        
        $this->system_unit_id->setChangeAction(new TAction(array($this, 'onChangeUnit')));
        
        
        //Buscar dados do município
        $codigo_municipio->setAction(new TAction(array('BuscaCidadeEmissora', 'onReload')));
        
        
        //INFORMAÇÕES GERAIS
        $this->form->addFields( [ $id ] );
        
        $row = $this->form->addFields( [ new TLabel('Mantenedora <font color="red">*</font>'), $dados_mantenedora_id ] );
        $row->layout = ['col-sm-12'];
                
        $row = $this->form->addFields( [ new TLabel('Unidade <font color="red">*</font>'), $this->system_unit_id ],
                                       [ new TLabel('Nome emissora <font color="red">*</font>'), $nome ],
                                       [ new TLabel('CNPJ <font color="red">*</font>'), $cnpj ],
                                       [ new TLabel('Código MEC <font color="red">*</font>'), $codigo_mec ] );
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-2', 'col-sm-2'];        
        
        $this->form->addFields( [ '<br><br>' ] );        


        //ENDEREÇO
        $label1 = new TLabel('Endereço', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        
        $row = $this->form->addFields( [ new TLabel('Logradouro <font color="red">*</font>'), $logradouro ],
                                       [ new TLabel('Nº <font color="red">*</font>'), $numero ],
                                       [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Bairro <font color="red">*</font>'), $bairro ] );
        $row->layout = ['col-sm-6', 'col-sm-1', 'col-sm-2', 'col-sm-3' ];
        
        $row = $this->form->addFields( [ new TLabel('Código município <font color="red">*</font>'), $codigo_municipio ],
                                       [ new TLabel('Nome do município <font color="red">*</font>'), $nome_municipio ],
                                       [ new TLabel('UF <font color="red">*</font>'), $uf ],
                                       [ new TLabel('CEP <font color="red">*</font>'), $cep ] );
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-1', 'col-sm-3' ];         
        
        $this->form->addFields( [ '<br><br>' ] );
        
        $panel = new TPanelGroup();
        
        $label_explicacao = "<i>Prezado usuário,<br> 
        Os dados relacionados ao Credenciamento, Recredenciamento e a Renovação de Recredenciamento podem ser consultados
        no site do E-MEC <a style='color:#3c8dbc' href= 'https://emec.mec.gov.br/' target='_blank'> (clicando aqui)</a>.<br>
        Os campos relacionados ao Recredenciamento e Renovação de Recredenciamento são considerados obrigatórios desde que 
        conste determinado ato regulatório ou informação sobre a tramitação do processo no sistema E-MEC</i>";

        $panel->add($label_explicacao);
        $this->form->addContent( [ $panel ] );

        $this->form->addFields( [ '<br>' ] );
        
        
        //CREDENCIAMENTO
        $label2 = new TLabel('Credenciamento', '#285097', 12, 'b', '<br>');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );
                
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ $opcao_credenciamento_emec ] );
        $row->layout = ['col-sm-12'];        
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Tipo'), $credenciamento_tipo ],
                                       [ new TLabel('Nº'), $credenciamento_numero ],
                                       [ new TLabel('Data'), $credenciamento_data ],
                                       [ new TLabel('Número DOU'), $credenciamento_numero_DOU ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Veículo de publicação'), $credenciamento_veiculo_publicacao ],
                                       [ new TLabel('Data de publicação'), $credenciamento_data_publicacao ],
                                       [ new TLabel('Seção de publicação'), $credenciamento_secao_publicacao ],
                                       [ new TLabel('Pág. de publicação'), $credenciamento_pag_publicacao ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
        
        $row = $this->form->addFields( [ new TLabel('Número do processo'), $credenciamento_numero_processo ],
                                       [ new TLabel('Tipo de processo'), $credenciamento_tipo_processo ],
                                       [ new TLabel('Data do cadastro'), $credenciamento_data_cadastro ],
                                       [ new TLabel('Data do protocolo'), $credenciamento_data_protocolo ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
        
        
        $this->form->addFields( [ '<br><br>' ] );
        
        
        //RECREDENCIAMENTO
        $label3 = new TLabel('Recredenciamento', '#285097', 12, 'b', '<br>');
        $label3->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label3] );
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ $opcao_recredenciamento_emec ] );
        $row->layout = ['col-sm-12'];        
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Tipo'), $recredenciamento_tipo ],
                                       [ new TLabel('Nº'), $recredenciamento_numero ],
                                       [ new TLabel('Data'), $recredenciamento_data ],
                                       [ new TLabel('Número DOU'), $recredenciamento_numero_DOU ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Veículo de publicação'), $recredenciamento_veiculo_publicacao ],
                                       [ new TLabel('Data de publicação'), $recredenciamento_data_publicacao ],
                                       [ new TLabel('Seção de publicação'), $recredenciamento_secao_publicacao ],
                                       [ new TLabel('Pág. de publicação'), $recredenciamento_pag_publicacao ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
        
        $row = $this->form->addFields( [ new TLabel('Número do processo'), $recredenciamento_numero_processo ],
                                       [ new TLabel('Tipo de processo'), $recredenciamento_tipo_processo ],
                                       [ new TLabel('Data do cadastro'), $recredenciamento_data_cadastro ],
                                       [ new TLabel('Data do protocolo'), $recredenciamento_data_protocolo ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
       
        
        $this->form->addFields( [ '<br><br>' ] );
        
        
        //RENOVAÇÃO DE RECREDENCIAMENTO
        $label4 = new TLabel('Renovação de Recredenciamento', '#285097', 12, 'b', '<br>');
        $label4->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label4] );
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ $opcao_renovacao_emec ] );
        $row->layout = ['col-sm-12'];        
        
        $this->form->addFields( [ '<br>' ] );
        
        $row = $this->form->addFields( [ new TLabel('Tipo'), $renovacao_recredenciamento_tipo ],
                                       [ new TLabel('Nº'), $renovacao_recredenciamento_numero ],
                                       [ new TLabel('Data'), $renovacao_recredenciamento_data ],
                                       [ new TLabel('Número DOU'), $renovacao_recredenciamento_numero_DOU ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Veículo de publicação'), $renovacao_recredenciamento_veic_publ ],
                                       [ new TLabel('Data de publicação'), $renovacao_recredenciamento_data_publ ],
                                       [ new TLabel('Seção de publicação'), $renovacao_recredenciamento_secao_publ ],
                                       [ new TLabel('Pág. de publicação'), $renovacao_recredenciamento_pag_publ ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
        
        $row = $this->form->addFields( [ new TLabel('Número do processo'), $renovacao_recredenciamento_numero_processo ],
                                       [ new TLabel('Tipo de processo'), $renovacao_recredenciamento_tipo_processo ],
                                       [ new TLabel('Data do cadastro'), $renovacao_recredenciamento_data_cadastro ],
                                       [ new TLabel('Data do protocolo'), $renovacao_recredenciamento_data_protocolo ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ];
 
        $this->form->addFields( [ '<br>' ] ); 
        $label5 = new TLabel('<font color="red">*</font> Campos obrigatórios (alguns campos são obrigatórios condicionais, ou seja, dependem da escolha do usuário no momento do preenchimento)', '', 10, 'i');
        $this->form->addContent( [$label5] ); 
 
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        
        $dados_mantenedora_id->addValidation('Mantenedora', new TRequiredValidator);
        $this->system_unit_id->addValidation('Unidade', new TRequiredValidator);
        $nome->addValidation('Nome emissora', new TRequiredValidator);
        $cnpj->addValidation('CNPJ', new TRequiredValidator);
        $cnpj->addValidation('CNPJ', new TCNPJValidator);  
        $codigo_mec->addValidation('Código MEC', new TRequiredValidator);
        $logradouro->addValidation('Logradouro', new TRequiredValidator);
        $numero->addValidation('Nº', new TRequiredValidator);
        $bairro->addValidation('Bairro', new TRequiredValidator);
        $codigo_municipio->addValidation('Código município', new TRequiredValidator);
        $nome_municipio->addValidation('Nome do município', new TRequiredValidator);
        $uf->addValidation('UF', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);
        $opcao_credenciamento_emec->addValidation('Credenciamento - Utilizar informações sobre ato regulatório/tramitação do processo é obrigatório', new TRequiredValidator);
                                     

        // set sizes
        $nome->setMaxLength('255');
        $nome->forceUpperCase();
        $nome->setEditable(false);
        $codigo_mec->setMask('9!');
        $cnpj->setMask('99999999999999');
        $logradouro->setMaxLength('60');
        $numero->setMaxLength('60');
        $complemento->setMaxLength('60');
        $bairro->setMaxLength('60');
        $codigo_municipio->setMask('9999999');
        $nome_municipio->setEditable(FALSE);
        $nome_municipio->setMaxLength('255');
        $nome_municipio->forceUpperCase();
        $uf->setEditable(FALSE);
        $cep->setEditable(FALSE);
        $cep->setMask('99999999');
        $opcao_credenciamento_emec->setLayout('horizontal');
        $opcao_credenciamento_emec->setUseButton();
        $opcao_credenciamento_emec->setSize('100%');
        $credenciamento_numero->setMask('9!');
        $credenciamento_data->setMask('dd/mm/yyyy');
        $credenciamento_data->setDatabaseMask('yyyy-mm-dd');
        $credenciamento_veiculo_publicacao->setMask('S!');
        $credenciamento_veiculo_publicacao->forceUpperCase();
        $credenciamento_data_publicacao->setMask('dd/mm/yyyy');
        $credenciamento_data_publicacao->setDatabaseMask('yyyy-mm-dd');
        $credenciamento_secao_publicacao->setMask('9!');
        $credenciamento_pag_publicacao->setMask('9!');
        $credenciamento_numero_DOU->setMask('9!');
        $credenciamento_numero_processo->setMask('9!');
        $credenciamento_data_cadastro->setMask('dd/mm/yyyy');
        $credenciamento_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $credenciamento_data_protocolo->setMask('dd/mm/yyyy');
        $credenciamento_data_protocolo->setDatabaseMask('yyyy-mm-dd');
        $opcao_recredenciamento_emec->setLayout('horizontal');
        $opcao_recredenciamento_emec->setUseButton();
        $opcao_recredenciamento_emec->setSize('100%');
        $recredenciamento_numero->setMask('9!');
        $recredenciamento_data->setMask('dd/mm/yyyy');
        $recredenciamento_data->setDatabaseMask('yyyy-mm-dd');
        $recredenciamento_veiculo_publicacao->setMask('S!');
        $recredenciamento_veiculo_publicacao->forceUpperCase();
        $recredenciamento_data_publicacao->setMask('dd/mm/yyyy');
        $recredenciamento_data_publicacao->setDatabaseMask('yyyy-mm-dd');
        $recredenciamento_secao_publicacao->setMask('9!');
        $recredenciamento_pag_publicacao->setMask('9!');
        $recredenciamento_numero_DOU->setMask('9!');
        $recredenciamento_numero_processo->setMask('9!');
        $recredenciamento_data_cadastro->setMask('dd/mm/yyyy');
        $recredenciamento_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $recredenciamento_data_protocolo->setMask('dd/mm/yyyy');
        $recredenciamento_data_protocolo->setDatabaseMask('yyyy-mm-dd');
        $opcao_renovacao_emec->setLayout('horizontal');
        $opcao_renovacao_emec->setUseButton();
        $opcao_renovacao_emec->setSize('100%');
        $renovacao_recredenciamento_numero->setMask('9!');
        $renovacao_recredenciamento_data->setMask('dd/mm/yyyy');
        $renovacao_recredenciamento_data->setDatabaseMask('yyyy-mm-dd');
        $renovacao_recredenciamento_veic_publ->setMask('S!');
        $renovacao_recredenciamento_veic_publ->forceUpperCase();
        $renovacao_recredenciamento_data_publ->setMask('dd/mm/yyyy');
        $renovacao_recredenciamento_data_publ->setDatabaseMask('yyyy-mm-dd');
        $renovacao_recredenciamento_secao_publ->setMask('9!');
        $renovacao_recredenciamento_pag_publ->setMask('9!');
        $renovacao_recredenciamento_numero_DOU->setMask('9!');
        $renovacao_recredenciamento_numero_processo->setMask('9!');
        $renovacao_recredenciamento_data_cadastro->setMask('dd/mm/yyyy');
        $renovacao_recredenciamento_data_cadastro->setDatabaseMask('yyyy-mm-dd');
        $renovacao_recredenciamento_data_protocolo->setMask('dd/mm/yyyy');
        $renovacao_recredenciamento_data_protocolo->setDatabaseMask('yyyy-mm-dd');
                

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction( _t('Back'), new TAction(array('DiplomaEmissoraList','onReload')), 'fas:arrow-alt-circle-left blue');
    
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onChangeUnit($param)
    {   
        try
        {
            TTransaction::open('dados_fei');
        
            $unidade_id = $param['system_unit_id'];
            
            $unidade = new FiEntidade($unidade_id);

            $obj = new StdClass;
            $obj->nome = $unidade->NomeFantasia;
                
            TForm::sendData('form_DiplomaDigitalEmissora', $obj);    

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }
    
    
    public static function onOpcaoCredenciamentoChange($param)
    {
        $opcao_credenciamento_emec = $param['opcao_credenciamento_emec'] ?? null;

        if($opcao_credenciamento_emec == "Utilizar informações sobre ato regulatório")
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data_protocolo');
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalEmissora', 'credenciamento_tipo');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'credenciamento_numero'); 
            TDate::enableField('form_DiplomaDigitalEmissora', 'credenciamento_data'); 
            TEntry::enableField('form_DiplomaDigitalEmissora', 'credenciamento_veiculo_publicacao');
            TDate::enableField('form_DiplomaDigitalEmissora', 'credenciamento_data_publicacao');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'credenciamento_secao_publicacao');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'credenciamento_pag_publicacao');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'credenciamento_numero_DOU');
            
            //RECARREGA
            $tipo = [];
            $tipo['Ato Próprio'] = "Ato Próprio";
            $tipo['Decreto'] = "Decreto";
            $tipo['Deliberação'] = "Deliberação";
            $tipo['Lei Estadual'] = "Lei Estadual";
            $tipo['Lei Federal'] = "Lei Federal";
            $tipo['Lei Municipal'] = "Lei Municipal";
            $tipo['Parecer'] = "Parecer";
            $tipo['Portaria'] = "Portaria";
            $tipo['Resolução'] = "Resolução"; 
        
            TCombo::reload('form_DiplomaDigitalEmissora', 'credenciamento_tipo', $tipo, TRUE);
        }
        elseif($opcao_credenciamento_emec == "Utilizar informações sobre tramitação do processo")
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalEmissora', 'credenciamento_tipo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_numero'); 
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data'); 
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_numero_DOU');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalEmissora', 'credenciamento_tipo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_numero'); 
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data'); 
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_numero_DOU');
            
            //HABILITA 
            TEntry::enableField('form_DiplomaDigitalEmissora', 'credenciamento_numero_processo');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'credenciamento_tipo_processo');
            TDate::enableField('form_DiplomaDigitalEmissora', 'credenciamento_data_cadastro');
            TDate::enableField('form_DiplomaDigitalEmissora', 'credenciamento_data_protocolo');
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalEmissora', 'credenciamento_tipo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_numero'); 
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data'); 
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_numero_DOU');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'credenciamento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalEmissora', 'credenciamento_data_protocolo');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalEmissora', 'credenciamento_tipo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_numero'); 
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data'); 
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_numero_DOU');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'credenciamento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalEmissora', 'credenciamento_data_protocolo');
        }
    }
    
    
    public static function onOpcaoRecredenciamentoChange($param)
    {
        $opcao_recredenciamento_emec = $param['opcao_recredenciamento_emec'] ?? null;
        
        if($opcao_recredenciamento_emec == "Utilizar informações sobre ato regulatório")
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_protocolo');
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero'); 
            TDate::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_data'); 
            TEntry::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_veiculo_publicacao');
            TDate::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_publicacao');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_secao_publicacao');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_pag_publicacao');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_DOU'); 
            
            //RECARREGA
            $tipo = [];
            $tipo['Ato Próprio'] = "Ato Próprio";
            $tipo['Decreto'] = "Decreto";
            $tipo['Deliberação'] = "Deliberação";
            $tipo['Lei Estadual'] = "Lei Estadual";
            $tipo['Lei Federal'] = "Lei Federal";
            $tipo['Lei Municipal'] = "Lei Municipal";
            $tipo['Parecer'] = "Parecer";
            $tipo['Portaria'] = "Portaria";
            $tipo['Resolução'] = "Resolução"; 
        
            TCombo::reload('form_DiplomaDigitalEmissora', 'recredenciamento_tipo', $tipo, TRUE);
        }
        elseif($opcao_recredenciamento_emec == "Utilizar informações sobre tramitação do processo")
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_numero'); 
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data'); 
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_DOU');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero'); 
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data'); 
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_DOU');
            
            //HABILITA 
            TEntry::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_processo');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo_processo');
            TDate::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_cadastro');
            TDate::enableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_protocolo');
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_numero'); 
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data'); 
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_veiculo_publicacao');
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_secao_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_pag_publicacao');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_DOU');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalEmissora', 'recredenciamento_data_protocolo');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero'); 
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data'); 
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_veiculo_publicacao');
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_secao_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_pag_publicacao');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_DOU');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalEmissora', 'recredenciamento_data_protocolo');
        }
    }
    
    
    public static function onOpcaoRenovacaoChange($param)
    {
        $opcao_renovacao_emec = $param['opcao_renovacao_emec'] ?? null;
        
        if($opcao_renovacao_emec == "Utilizar informações sobre ato regulatório")
        {
            //LIMPA
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_protocolo');
            
            //DESABILITA
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_protocolo');
            
            //HABILITA
            TCombo::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero'); 
            TDate::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data'); 
            TEntry::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_veic_publ');
            TDate::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_publ');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_secao_publ');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_pag_publ');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_DOU');
            
            //RECARREGA
            $tipo = [];
            $tipo['Ato Próprio'] = "Ato Próprio";
            $tipo['Decreto'] = "Decreto";
            $tipo['Deliberação'] = "Deliberação";
            $tipo['Lei Estadual'] = "Lei Estadual";
            $tipo['Lei Federal'] = "Lei Federal";
            $tipo['Lei Municipal'] = "Lei Municipal";
            $tipo['Parecer'] = "Parecer";
            $tipo['Portaria'] = "Portaria";
            $tipo['Resolução'] = "Resolução"; 
        
            TCombo::reload('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo', $tipo, TRUE); 
        }
        elseif($opcao_renovacao_emec == "Utilizar informações sobre tramitação do processo")
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero'); 
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data'); 
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_veic_publ');
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_publ');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_secao_publ');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_pag_publ');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_DOU');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero'); 
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data'); 
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_veic_publ');
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_publ');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_secao_publ');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_pag_publ');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_DOU');
            
            //HABILITA 
            TEntry::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_processo');
            TEntry::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo_processo');
            TDate::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_cadastro');
            TDate::enableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_protocolo');
        }
        else
        {
            //LIMPA
            TCombo::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero'); 
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data'); 
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_veic_publ');
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_publ');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_secao_publ');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_pag_publ');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_DOU');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_processo');
            TEntry::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo_processo');
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_cadastro');
            TDate::clearField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_protocolo');
            
            //DESABILITA
            TCombo::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero'); 
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data'); 
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_veic_publ');
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_publ');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_secao_publ');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_pag_publ');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_DOU');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_numero_processo');
            TEntry::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_tipo_processo');
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_cadastro');
            TDate::disableField('form_DiplomaDigitalEmissora', 'renovacao_recredenciamento_data_protocolo');
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $data = $this->form->getData();
            
            $object = new DiplomaDigitalEmissora;
            $object->fromArray( (array) $data);
           
            $this->form->validate();
            
            
            //Se está salvando um "novo registro", mas já existe registro desta mesma unidade
            if(empty($data->id))
            {
                $registros_bd = DiplomaDigitalEmissora::where('system_unit_id', '=', $data->system_unit_id)->load();
                
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro desta mesma emissora");
                }
            }
             
 
            //Credenciamento - verifica campos condicionais
            if($object->opcao_credenciamento_emec == "Utilizar informações sobre ato regulatório")
            {
                if (! $object->credenciamento_tipo)
                {
                    throw new Exception("O campo Credenciamento - Tipo é obrigatório");
                }
                if (! $object->credenciamento_numero)
                {
                    throw new Exception("O campo Credenciamento - Nº é obrigatório");
                }
                if (! $object->credenciamento_data)
                {
                    throw new Exception("O campo Credenciamento - Data é obrigatório");
                }
                if (! $object->credenciamento_veiculo_publicacao)
                {
                    throw new Exception("O campo Credenciamento - Veículo de publicação é obrigatório");
                }
                if (! $object->credenciamento_data_publicacao)
                {
                    throw new Exception("O campo Credenciamento - Data de publicação é obrigatório");
                }
                if (! $object->credenciamento_secao_publicacao)
                {
                    throw new Exception("O campo Credenciamento - Seção de publicação é obrigatório");
                }
                if (! $object->credenciamento_pag_publicacao)
                {
                    throw new Exception("O campo Credenciamento - Página de publicação é obrigatório");
                }
                if (! $object->credenciamento_numero_DOU)
                {
                    throw new Exception("O campo Credenciamento - Número DOU é obrigatório");
                }                
            }
            
            if($object->opcao_credenciamento_emec == "Utilizar informações sobre tramitação do processo")
            {
                if (! $object->credenciamento_numero_processo)
                {
                    throw new Exception("O campo Credenciamento - Nº do processo é obrigatório");
                }
                if (! $object->credenciamento_tipo_processo)
                {
                    throw new Exception("O campo Credenciamento - Tipo de processo é obrigatório");
                }
                if (! $object->credenciamento_data_cadastro)
                {
                    throw new Exception("O campo Credenciamento - Data do cadastro é obrigatório");
                }
                if (! $object->credenciamento_data_protocolo)
                {
                    throw new Exception("O campo Credenciamento - Data do protocolo é obrigatório");
                }
            }
            
            
            //Recredenciamento - verifica campos condicionais
            if((($object->recredenciamento_tipo) OR ($object->recredenciamento_numero) OR ($object->recredenciamento_data)
               OR ($object->recredenciamento_numero_DOU) OR ($object->recredenciamento_veiculo_publicacao) 
               OR ($object->recredenciamento_data_publicacao) OR ($object->recredenciamento_secao_publicacao) 
               OR ($object->recredenciamento_pag_publicacao)OR ($object->recredenciamento_numero_processo) 
               OR ($object->recredenciamento_tipo_processo) OR ($object->recredenciamento_data_cadastro) 
               OR ($object->recredenciamento_data_protocolo)) AND (! $object->opcao_recredenciamento_emec))
            {
                throw new Exception("O campo Recredenciamento - Utilizar informações sobre ato regulatório/tramitação do processo é obrigatório");  
            }
          
            //Faz o inverso, verifica cada campo obrigatório do Recredenciamento de acordo com a opção marcada
            if($object->opcao_recredenciamento_emec == "Utilizar informações sobre ato regulatório")
            {
                if (! $object->recredenciamento_tipo)
                {
                    throw new Exception("O campo Recredenciamento - Tipo é obrigatório");
                }
                if (! $object->recredenciamento_numero)
                {
                    throw new Exception("O campo Recredenciamento - Nº é obrigatório");
                }
                if (! $object->recredenciamento_data)
                {
                    throw new Exception("O campo Recredenciamento - Data é obrigatório");
                }
                if (! $object->recredenciamento_veiculo_publicacao)
                {
                    throw new Exception("O campo Recredenciamento - Veículo de publicação é obrigatório");
                }
                if (! $object->recredenciamento_data_publicacao)
                {
                    throw new Exception("O campo Recredenciamento - Data de publicação é obrigatório");
                }
                if (! $object->recredenciamento_secao_publicacao)
                {
                    throw new Exception("O campo Recredenciamento - Seção de publicação é obrigatório");
                }
                if (! $object->recredenciamento_pag_publicacao)
                {
                    throw new Exception("O campo Recredenciamento - Página de publicação é obrigatório");
                }
                if (! $object->recredenciamento_numero_DOU)
                {
                    throw new Exception("O campo Recredenciamento - Número DOU é obrigatório");
                }                
            }
            
            if($object->opcao_recredenciamento_emec == "Utilizar informações sobre tramitação do processo")
            {
                if (! $object->recredenciamento_numero_processo)
                {
                    throw new Exception("O campo Recredenciamento - Nº do processo é obrigatório");
                }
                if (! $object->recredenciamento_tipo_processo)
                {
                    throw new Exception("O campo Recredenciamento - Tipo de processo é obrigatório");
                }
                if (! $object->recredenciamento_data_cadastro)
                {
                    throw new Exception("O campo Recredenciamento - Data do cadastro é obrigatório");
                }
                if (! $object->recredenciamento_data_protocolo)
                {
                    throw new Exception("O campo Recredenciamento - Data do protocolo é obrigatório");
                }
            }
            
            
            //Renovação - verifica campos condicionais
            if((($object->renovacao_recredenciamento_tipo) OR ($object->renovacao_recredenciamento_numero)
               OR ($object->renovacao_recredenciamento_data) OR ($object->renovacao_recredenciamento_numero_DOU)
               OR ($object->renovacao_recredenciamento_veic_publ) OR ($object->renovacao_recredenciamento_data_publ) 
               OR ($object->renovacao_recredenciamento_secao_publ) OR ($object->renovacao_recredenciamento_pag_publ) 
               OR ($object->renovacao_recredenciamento_numero_processo) OR ($object->renovacao_recredenciamento_tipo_processo) 
               OR ($object->renovacao_recredenciamento_data_cadastro) OR ($object->renovacao_recredenciamento_data_protocolo)) 
               AND (! $object->opcao_renovacao_emec))
            {
                throw new Exception("O campo Renovação de Recredenciamento - Utilizar informações sobre ato regulatório/tramitação do processo é obrigatório");  
            }
          
            //Faz o inverso, verifica cada campo obrigatório do Recredenciamento de acordo com a opção marcada
            if($object->opcao_renovacao_emec == "Utilizar informações sobre ato regulatório")
            {
                if (! $object->renovacao_recredenciamento_tipo)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Tipo é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_numero)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Nº é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_data)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Data é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_veic_publ)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Veículo de publicação é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_data_publ)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Data de publicação é obrigatório");
                }
                if (!$object->renovacao_recredenciamento_secao_publ)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Seção de publicação é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_pag_publ)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Página de publicação é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_numero_DOU)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Número DOU é obrigatório");
                }                
            }
            
            if($object->opcao_renovacao_emec == "Utilizar informações sobre tramitação do processo")
            {
                if (! $object->renovacao_recredenciamento_numero_processo)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Nº do processo é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_tipo_processo)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Tipo de processo é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_data_cadastro)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Data do cadastro é obrigatório");
                }
                if (! $object->renovacao_recredenciamento_data_protocolo)
                {
                    throw new Exception("O campo Renovação de Recredenciamento - Data do protocolo é obrigatório");
                }
            }           
           
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store();
            

            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('DiplomaEmissoraList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            
            //Se estiver editando registro e cair na exceção, mantém campo bloqueado. Se estiver salvando novo registro, mantém desbloqueado
            if(!empty($param['id']))
            {
                $this->system_unit_id->setEditable(FALSE);  
            }
                               
            $param['opcao_credenciamento_emec'] = $object->opcao_credenciamento_emec;
            $this->onOpcaoCredenciamentoChange($param);
            
            $param['opcao_recredenciamento_emec'] = $object->opcao_recredenciamento_emec;
            $this->onOpcaoRecredenciamentoChange($param);
            
            $param['opcao_renovacao_emec'] = $object->opcao_renovacao_emec;
            $this->onOpcaoRenovacaoChange($param); 
            
            $this->form->setData( $this->form->getData() );
            
            $obj = new StdClass;
            $obj->credenciamento_tipo = $object->credenciamento_tipo;
            $obj->recredenciamento_tipo = $object->recredenciamento_tipo;
            $obj->renovacao_recredenciamento_tipo = $object->renovacao_recredenciamento_tipo;
            
            TForm::sendData('form_DiplomaDigitalEmissora', $obj);
            
            TTransaction::rollback();
        }
    }


    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {                
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new DiplomaDigitalEmissora($key);
                
                $this->system_unit_id->setEditable(FALSE);
                
                $param['opcao_credenciamento_emec'] = $object->opcao_credenciamento_emec;
                $this->onOpcaoCredenciamentoChange($param);
                
                $param['opcao_recredenciamento_emec'] = $object->opcao_recredenciamento_emec;
                $this->onOpcaoRecredenciamentoChange($param);
                
                $param['opcao_renovacao_emec'] = $object->opcao_renovacao_emec;
                $this->onOpcaoRenovacaoChange($param); 
                
                $this->form->setData( $this->form->getData() );
                
                $obj = new StdClass;
                $obj->credenciamento_tipo = $object->credenciamento_tipo;
                $obj->recredenciamento_tipo = $object->recredenciamento_tipo;
                $obj->renovacao_recredenciamento_tipo = $object->renovacao_recredenciamento_tipo;
                
                TForm::sendData('form_DiplomaDigitalEmissora', $obj);
                
                $this->form->setData($object);
                
                TTransaction::close();    
            }
            else
            {
                $this->form->clear(TRUE);

                $this->onOpcaoCredenciamentoChange($param);
                $this->onOpcaoRecredenciamentoChange($param);
                $this->onOpcaoRenovacaoChange($param);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
