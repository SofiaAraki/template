<?php

class CadastroVeiculoFormProf extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_CadastroVeiculo');
        $this->form->setFormTitle('Cadastro de Veículos');
        

        // create the form fields
        $id = new THidden('id');
        //$nome = new TEntry('nome');
        $nome = new THidden('nome');
        $curso = new TEntry('curso');
        //$ciclo = new TEntry('ciclo');
        $ciclo = new TCombo('ciclo');
        $proprietario = new TEntry('proprietario');
        $placa = new TEntry('placa');
        $modelo = new TEntry('modelo');
        $ano = new TEntry('ano');
        $cor = new TEntry('cor');
        $unidade = new THidden('unidade');
        $filename = new TMultiFile('filename');


        $itens_ciclo = array();
        $itens_ciclo['1º ciclo'] ='1º ciclo';
        $itens_ciclo['2º ciclo'] ='2º ciclo';
        $itens_ciclo['3º ciclo'] ='3º ciclo';
        $itens_ciclo['4º ciclo'] ='4º ciclo';
        $itens_ciclo['5º ciclo'] ='5º ciclo';
        $itens_ciclo['6º ciclo'] ='6º ciclo';
        $itens_ciclo['7º ciclo'] ='7º ciclo';
        $itens_ciclo['8º ciclo'] ='8º ciclo';
        $itens_ciclo['9º ciclo'] ='9º ciclo';
        $itens_ciclo['10º ciclo'] ='10º ciclo';

        $ciclo->addItems($itens_ciclo);
        $ciclo->enableSearch();


        $curso->forceUpperCase();
        $proprietario->forceUpperCase();
        $placa->forceUpperCase();
        $modelo->forceUpperCase();
        $cor->forceUpperCase();
        //$placa->setMask('SSS-9999');

        
        /*TTransaction::open('Felabs_DB');
        $loggedProfUnit = TSession::getValue('userunitid'); //PEGA A ID DA UNIDADE DO USUARIO LOGADO
        $unitName = new SystemUnit($loggedProfUnit);

        $logado = SystemUser::newFromLogin(TSession::getValue('login'));
        //$nome->setValue($logado->nome);
        $nome->setValue($logado->name);

        TTransaction::close();

        //$unidade->setValue($unitName->name);
        //$unidade->setEditable(FALSE);
        $nome->setEditable(FALSE); */


        $curso->addValidation('"Curso"', new TRequiredValidator());
        $ciclo->addValidation('"Ciclo"', new TRequiredValidator());
        $proprietario->addValidation('"Nome do Proprietário"', new TRequiredValidator());
        $placa->addValidation('"Placa"', new TRequiredValidator());
        $modelo->addValidation('"Modelo"', new TRequiredValidator());
        $ano->addValidation('"Ano"', new TRequiredValidator());
        $cor->addValidation('"Cor"', new TRequiredValidator());
        $filename->addValidation('"Documento"', new TRequiredValidator());


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [new TLabel('<i><b>Preencha corretamente os dados abaixo:</i></b>')]);
        $this->form->addFields( [new TLabel('<i>Após o preenchimento, aguarde a análise para impressão da carteirinha do veículo.</i>')]);
        //$this->form->addFields( [ new TLabel('Nome do Condutor:') ], [ $nome ], [ new TLabel('') ] );
        $this->form->addFields( [ new TLabel('Curso:') ], [ $curso ], [ new TLabel('Ciclo:') ], [ $ciclo ] );
        //$this->form->addFields( [ new TLabel('Ciclo:') ], [ $ciclo ] );
        $this->form->addFields( [ new TLabel('Nome do Proprietário: (nome no documento do veículo)') ], [ $proprietario ], [ new TLabel('Placa:') ], [ $placa ] );
        $this->form->addFields(  [ new TLabel('Modelo:') ], [ $modelo ], [ new TLabel('Ano:') ], [ $ano ], [ new TLabel('Cor:') ], [ $cor ] );
        //$this->form->addFields( [ new TLabel('Modelo:') ], [ $modelo ] );
        //$this->form->addFields( [ new TLabel('Ano:') ], [ $ano ], [ new TLabel('Cor:') ], [ $cor ] );
        //$this->form->addFields( [ new TLabel('Cor:') ], [ $cor ] );
        //$this->form->addFields( [ new TLabel('Unidade:') ], [ $unidade ], [ new TLabel('Documento (veículo):') ], [ $filename ] );
        $this->form->addFields( [ new TLabel('Documento: (anexar foto ou PDF do documento do veículo)') ], [ $filename ], [ new TLabel('') ] );


        // set sizes
        $nome->setSize('100%');
        $curso->setSize('100%');
        $ciclo->setSize('40%');
        $proprietario->setSize('100%');
        $placa->setSize('40%');
        $modelo->setSize('100%');
        $ano->setSize('100%');
        $cor->setSize('100%');
        $unidade->setSize('100%');
        $filename->setSize('100%');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // create the form actions
        $btn = $this->form->addAction(('Salvar'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CadastroVeiculoListProf'));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            $this->form->validate(); 
            $data = $this->form->getData(); 
            
            $object = new CadastroVeiculo;  
            $object->fromArray( (array) $data);
            
            if($data->id)
            {	            	    	
            	if($data->filename )
            	{           			
            		$cv = new CadastroVeiculo($data->id);
            		$arqBanco = $cv->filename;

            		$contador = count($data->filename);
            		$i = $contador-1;

            		$teste = $data->filename[$i];

            		if($teste != $arqBanco)
            		{            		
                		$zip = new ZipArchive();
    		            $today = date("Ymd");
    		            $nomeArquivo = "arquivos/"."cadastro_veiculo"."_$today_".time().'.zip';
    		            $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
    		            
    		            foreach ($data->filename as $arq)
    		            {
    		                $source_file   = 'tmp/'.$arq;
    		                
    		                if (file_exists($source_file))
    		                {
    		                    $zip->addFile(  'tmp/'.$arq , "$arq" );		                    
    		                }
    		            }
    		            $zip->close();
    
    		            $object->filename = $nomeArquivo;
            		}
            	}
            }
            else //quando é um novo registro
            {
            	$zip = new ZipArchive();
	            $today = date("Ymd");
	            $nomeArquivo = "arquivos/"."cadastro_veiculo"."_$today_".time().'.zip';
	            $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
	            
	            foreach ($data-> filename as $arq)
	            {
	                $source_file   = 'tmp/'.$arq;
	                
	                if (file_exists($source_file))
	                {
	                    $zip->addFile(  'tmp/'.$arq , "$arq" );	                    
	                }
	            }
	            
	            $zip->close();

	            $object->filename = $nomeArquivo;
            }            
            
            $object->nome = $user->name;
            $object->system_user_id = $user->id;
            $object->unidade = TSession::getValue('userunitid');
            $object->status = "Em Análise";

            if($user->checkInGroup( new SystemGroup(4)))
            {
                $object->grupo = 'Aluno';
            }

            elseif($user->checkInGroup( new SystemGroup(3)))
            {
                $object->grupo = 'Professor';
            }

            elseif($user->checkInGroup( new SystemGroup(5)))
            {
                $object->grupo = 'Colaborador';
            }            

            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('CadastroVeiculoListProf', 'onReload');
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
                
                $object = new CadastroVeiculo($key); 
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
