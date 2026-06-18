<?php

class CadastroVeiculoAnaliseForm extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_CadastroVeiculo');
        $this->form->setFormTitle('Cadastro de Veículos');
        

        // create the form fields
        //$id = new THidden('id');
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        //$nome = new THidden('nome');
        $curso = new TEntry('curso');
        //$ciclo = new TEntry('ciclo');
        $ciclo = new TEntry('ciclo');
        $proprietario = new TEntry('proprietario');
        $placa = new TEntry('placa');
        $modelo = new TEntry('modelo');
        $ano = new TEntry('ano');
        $cor = new TEntry('cor');
        $unidade = new THidden('unidade');
        //$filename = new TFile('filename');
        $obs = new TText('obs');
        //$validade = new TDate('validade');
        $validade = new THidden('validade');
        $grupo = new TEntry('grupo');
        $status = new TCombo('status');
        $filename = new TButton('filename');
        $system_user_id = new THidden('system_user_id');
        
        
        /*$itens_ciclo = array();
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
        $ciclo->enableSearch();*/


        $filename->setImage('fas:cloud-download-alt');
        $filename->setAction(new TAction(array($this, 'onDownloadMaster')), 'Download');


        $itens_situacao = array();
        $itens_situacao['Em Análise'] ='Em Análise';
        //$itens_situacao['Solicitar alteração'] ='Solicitar alteração';
        $itens_situacao['Deferido'] ='Deferido';
        $itens_situacao['Indeferido'] ='Indeferido';
        
        $status->addItems($itens_situacao);

        $curso->forceUpperCase();
        $proprietario->forceUpperCase();
        $placa->forceUpperCase();
        $modelo->forceUpperCase();
        $cor->forceUpperCase();
        $placa->setMask('SSS-9999');
        $nome->setEditable(FALSE);
        $curso->setEditable(FALSE);
        $proprietario->setEditable(FALSE);
        $placa->setEditable(FALSE);
        $modelo->setEditable(FALSE);
        $cor->setEditable(FALSE);
        $placa->setEditable(FALSE);
        $ciclo->setEditable(FALSE);
        $ano->setEditable(FALSE);
        $grupo->setEditable(FALSE);

        
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


        //$curso->addValidation('"Curso"', new TRequiredValidator());
        //$ciclo->addValidation('"Ciclo"', new TRequiredValidator());
        //$proprietario->addValidation('"Nome do Proprietário"', new TRequiredValidator());
        //$placa->addValidation('"Placa"', new TRequiredValidator());
        //$modelo->addValidation('"Modelo"', new TRequiredValidator());
        //$ano->addValidation('"Ano"', new TRequiredValidator());
        //$cor->addValidation('"Cor"', new TRequiredValidator());
        //$validade->addValidation('"Validade"', new TRequiredValidator());
        $status->addValidation('"Situação"', new TRequiredValidator());


        // add the fields
        $this->form->addFields( [new TFormSeparator('<i>Detalhes do cadastro</i>')] );
        $this->form->addFields( [ new TLabel('ID:') ],[ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        //$this->form->addFields( [new TLabel('<b>Análise dos dados</b>')]);
        //$this->form->addFields( [new TLabel('<i>Após o preenchimento, aguarde a análise para impressão da carteirinha do veículo.</i>')]);
        $this->form->addFields( [ new TLabel('Nome do Condutor:') ], [ $nome ], [ new TLabel('Perfil:') ], [$grupo] );
        $this->form->addFields( [ new TLabel('Curso:') ], [ $curso ], [ new TLabel('Ciclo:') ], [ $ciclo ] );
        //$this->form->addFields( [ new TLabel('Ciclo:') ], [ $ciclo ] );
        $this->form->addFields( [ new TLabel('Nome do Proprietário: (nome no documento do veículo)') ], [ $proprietario ], [ new TLabel('Placa:') ], [ $placa ] );
        $this->form->addFields(  [ new TLabel('Modelo:') ], [ $modelo ], [ new TLabel('Ano:') ], [ $ano ], [ new TLabel('Cor:') ], [ $cor ] );
        $this->form->addFields(  [ new TLabel('Documento(s) anexo(s):') ], [ $filename ], [ new TLabel('') ] );
        //$this->form->addFields( [ new TLabel('Modelo:') ], [ $modelo ] );
        //$this->form->addFields( [ new TLabel('Ano:') ], [ $ano ], [ new TLabel('Cor:') ], [ $cor ] );
        //$this->form->addFields( [ new TLabel('Cor:') ], [ $cor ] );
        //$this->form->addFields( [ new TLabel('Unidade:') ], [ $unidade ], [ new TLabel('Documento (veículo):') ], [ $filename ] );
        //$this->form->addFields( [ new TLabel('Documento: (anexar foto ou PDF do documento do veículo)') ], [ $filename ], [ new TLabel('') ] );


        $this->form->addFields( [new TFormSeparator('<i>Análise dos dados</i>')] );        
        //$this->form->addFields([new TLabel('Situação:')], [$status], [new TLabel('Validade:')], [$validade]);
        $this->form->addFields([new TLabel('Situação:')], [$status], [new TLabel('')]);
        $this->form->addFields([new TLabel('Observação:')], [$obs], [new TLabel('')]);


        // set sizes
        $nome->setSize('100%');
        $curso->setSize('100%');
        $ciclo->setSize('40%');
        $grupo->setSize('40%');
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
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CadastroVeiculoList'));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onDownloadMaster($param)
    {
        try
        {         
            $id = $param['id'];  
        
            TTransaction::open('Felabs_DB'); 
            
            $object = new CadastroVeiculo($id); 
                
            TTransaction::close(); 

            if(!empty($object-> filename))
            {              
                if (strtolower(substr($object->filename, -4)) == 'html')
                {
                    $win = TWindow::create( $object->filename, 0.8, 0.8 );
                    $win->add( file_get_contents( "arquivos/".$object->filename ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile($object->filename);
                }

                $this->form->setData( $this->form->getData() ); 

                TTransaction::rollback();
            }
            else
            {
                new TMessage('info', 'Este cadastro não possui anexos'); 
            }           
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            $prefs  = SystemPreference::getAllPreferences();            

            $this->form->validate(); 
            $data = $this->form->getData(); 
            
            $object = new CadastroVeiculo;  
            $object->fromArray( (array) $data); 

            $data_atual = date('Y');
            $object->validade = "31/12/".$data_atual;


            //email
            $usuario = $object->system_user_id;
            $usuarios = new SystemUser($usuario, FALSE);
            $emailusuario = $usuarios->email;
            //var_dump($usuarios);
            //die;

            /**
            $object->nome = $logged->name;
            $object->system_user_id = $logged->id;
            $object->unidade = TSession::getValue('userunitid');
            $object->status = "Em Análise";

            if ($logged->checkInGroup( new SystemGroup(4)) ){

                $object->grupo = 'Aluno';

            }

            elseif ($logged->checkInGroup( new SystemGroup(3)) ){

                $object->grupo = 'Professor';

            }

            elseif ($logged->checkInGroup( new SystemGroup(5)) ){

                $object->grupo = 'Colaborador';

            }

            //verificamos se ira existir troca de arquivo ou somente alteração de dados
            if($data->id){

            $obj = new CadastroVeiculo($data->id);

            if($object->filename != $obj->filename){

            if(file_exists("arquivos/carteirinha_veiculos/" . $obj->filename)){
            unlink("arquivos/carteirinha_veiculos/" . $obj->filename);
            }

            //$object->filename = md5($object->filename . date('Ymd')). $this->ver_extensao($object->filename);
            $today = date("Ymd");
            $user_id = $object->system_user_id;
            $reg_id = $object->id;
            $source_file = 'tmp/'.$param['filename'];
            $target_file = 'arquivos/' .'carteirinha_veiculos/' .$user_id . '_'. $reg_id . '_'. $today. $object->filename;

            // move to the target directory
            rename($source_file, $target_file);

            $object->filename = $target_file;

            }
            //quando é um novo registro

            }
            else{
            //$object->data_cadastro = date('Y-m-d H:i:s');
            //$object->filename = md5($object->filename . date('Ymd')). $this->ver_extensao($object->filename);
            $today = date("Ymd");
            $user_id = $object->system_user_id;		
            $source_file = 'tmp/'.$param['filename'];
            $target_file = 'arquivos/carteirinha_veiculos/' .$user_id . '_'. $today. $object->filename;

            // move to the target directory
            rename($source_file, $target_file);

            $object->filename = $target_file;

            } **/

            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('CadastroVeiculoAnaliseList', 'onReload');


            //email aluno/professor
/*            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Aluno - FEAcadêmico');
            $mail->setSubject('Carteirinha de Veículos');
            $mail->setTextBody("Prezado(a) $usuarios->name, sua solicitação de cadastro de Carteirinha de Veículo foi avaliada e a situação foi alterada. Acompanhe a situação através da Área do Aluno - FEAcadêmico."."\n". 'Esta é uma mensagem automática. Solicitamos, por favor, não responder este e-mail.');  

            $mail->addAddress($emailusuario);          
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();

            $id_notif = $usuario;

            //$notif = $object-> id_user;
            SystemNotification::register(
                                        $id_notif,
                                        'Novo status de Carteirinha de veículo definido',
                                        'Um novo estado foi definido para sua Carteirinha de veículo, verifique.',
                                        'class=CadastroVeiculoList',
                                        'Ver Cadastro',
                                        'far fa-list-alt green'
                                        );*/
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
