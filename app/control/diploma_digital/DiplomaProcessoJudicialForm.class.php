<?php

class DiplomaProcessoJudicialForm extends TWindow
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Dados do Processo Judicial');
        
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($param['id_documentacao']);
            $curso = $documentacao->diploma_digital_curso->nome_curso_diploma;
            $aluno = $documentacao->diploma_digital_diplomado->nome;
        
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }  
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalProcessoJudicial');
        $this->form->setFieldSizes('100%');
        $this->setSize(0.8, null);


        // create the form fields
        $id = new THidden('id');
        $dados_documentacao_id = new THidden('dados_documentacao_id');
        $numero_processo = new TEntry('numero_processo');
        $nome_juiz = new TEntry('nome_juiz');
        $decisao_judicial = new TText('decisao_judicial');
        $declaracao_emissora = new TText('declaracao_emissora');
        $declaracao_registradora = new TText('declaracao_registradora');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $dados_documentacao_id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $this->form->addFields( [ new TLabel("<b>Documentação:</b> $documentacao->codigo_interliga_diploma_documentacao") ] );
        $this->form->addFields( [ new TLabel("<b>Curso:</b> $curso") ] );
        $this->form->addFields( [ new TLabel("<b>Aluno:</b> $aluno") ] );
        
        $this->form->addContent( [ "<hr>" ] );
        
        $row = $this->form->addFields( [ new TLabel('Nº do processo <font color="red">*</font>'), $numero_processo ], 
                                       [ new TLabel('Nome do juiz <font color="red">*</font>'), $nome_juiz ] );
        $row->layout = ['col-sm-4', 'col-sm-8'];
                
        $this->form->addFields( [ new TLabel('Decisão judicial (reprodução da decisão emitida pelo juiz) <font color="red">*</font>'), $decisao_judicial ] );
        
        $this->form->addFields( [ new TLabel('Declaração emissora (acerca do processo judicial, das circunstâncias de emissão, ausência de 
        informações ou qualquer outra declaração que julgar pertinente constar no diploma) <font color="red">*</font>'), $declaracao_emissora ] );
        
        $this->form->addFields( [ new TLabel('Declaração registradora (acerca do processo judicial, das circunstâncias de registro, ausência de 
        informações ou qualquer outra declaração que julgar pertinente constar no diploma)'), $declaracao_registradora ] );
        
        $this->form->addFields( [ '<br>' ] ); 
        $label1 = new TLabel('<font color="red">*</font> Campos obrigatórios', '', 10, 'i');
        $this->form->addContent( [$label1] );


        $dados_documentacao_id->addValidation('ID Documentação', new TRequiredValidator);
        $numero_processo->addValidation('Nº do processo', new TRequiredValidator);
        $nome_juiz->addValidation('Nome do juiz', new TRequiredValidator);
        $decisao_judicial->addValidation('Decisão judicial', new TRequiredValidator);
        $declaracao_emissora->addValidation('Declaração emissora', new TRequiredValidator);
        

        // set sizes
        $dados_documentacao_id->setValue($documentacao->id);
        $dados_documentacao_id->setEditable(FALSE);
        $numero_processo->setMask('9999999-99.9999.9.99.9999');
        $declaracao_registradora->setEditable(FALSE);
        

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('DiplomaDocumentacaoList','onReload')), 'fas:arrow-alt-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 

            $this->form->validate(); 
            $data = $this->form->getData(); 
            
            $object = new DiplomaDigitalProcessoJudicial;  
            $object->fromArray( (array) $data); 
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s'); 
                            
            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('DiplomaDocumentacaoList', 'onReload');
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData( $this->form->getData() ); 
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
                
                $object = new DiplomaDigitalProcessoJudicial($key); 
                $this->form->setData($object); 
                
                TTransaction::close(); 
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
}
