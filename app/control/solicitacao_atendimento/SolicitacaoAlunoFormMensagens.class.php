<?php
/**
 * SolicitacaoAlunoFormMensagens Master/Detail
 * @author  <your name here>
 */
class SolicitacaoAlunoFormMensagens extends TPage
{
    protected $form; // form
    protected $formFields;
    protected $detail_list;

    use adianti\base\AdiantiMasterDetailTrait;
    
    /**
     * Page constructor
     */
    public function __construct($param)
    {
        parent::__construct();
        TTransaction::open('Felabs_DB');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_SolicitacaoAluno');
        $this->form->class = 'tform'; // CSS class
     //   $this->form->style = 'display: table;width:100%'; // style
        parent::include_css('app/resources/custom-frame.css');

        $solicitacao = new SolicitacaoAluno($param['key']);


            if($solicitacao->unidade == 1) //CNSC
                {
                    $tipoSolicitacao = new SolicitacaoCnsc($solicitacao->tipo_solicitacao);
                    $solicitacao->tipo_solicitacao1 = $tipoSolicitacao->tipo_doc_cnsc;
                }
                elseif($solicitacao->unidade == 2 || $solicitacao->unidade == 4 || $solicitacao->unidade == 6) //FFCL, PÓS E NEAD
                {
                    $tipoSolicitacao = new SolicitacaoFfcl($solicitacao->tipo_solicitacao);
                    $solicitacao->tipo_solicitacao1 = $tipoSolicitacao->tipo_doc_ffcl;
                }

        
        $this->form->setFormTitle('Solicitação do Atendimento - Mensagens');

     //   $text1  = new TTextDisplay($solicitacao->id_solicitacao, '#333333', '14px', '');
        $text2  = new TTextDisplay($solicitacao->cod_aluno, '#333333', '14px', '');
        $text3  = new TTextDisplay($solicitacao->nome_aluno, '#333333', '14px', '');
        $text4  = new TTextDisplay($solicitacao->obs_solicitacao, '#333333', '14px', '');
        $text5  = new TTextDisplay("<b>$solicitacao->tipo_solicitacao1</b>", '#333333', '14px', '');
        $text6  = new TTextDisplay($solicitacao->matricula_aluno, '#333333', '14px', '');

        // master fields
        $id_solicitacao = new THidden('id_solicitacao');
        $cod_aluno = new TEntry('cod_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $matricula_aluno = new TEntry('matricula_aluno');
        $unidade = new TEntry('unidade');
        $email_aluno = new TEntry('email_aluno');
        $atribuir = new TDBMultiSearch('atribuir', 'permission', 'SystemUser', 'id', 'name');
        $tipo_solicitacao1 = new TEntry('tipo_solicitacao1');
        $obs_solicitacao = new TText('obs_solicitacao');
        $obs_secretaria = new THidden('obs_secretaria');
        $status_solicitacao = new THidden('status_solicitacao');
        $status_pgto = new THidden('status_pgto');
        $quem_realizou = new THidden('quem_realizou');
        $filename = new TButton('filename');
        $filename_secretaria = new THidden('filename_secretaria');
        $ultima_edicao = new THidden('ultima_edicao');
        
        if (!empty($id_solicitacao))
        {
            $id_solicitacao->setEditable(FALSE);
        }

    //    $filename->setLabel('Download');
        $filename->setImage('fas:cloud-download-alt');
        $filename->setAction(new TAction(array($this, 'onDownloadMaster')), 'Download');
        
        // detail fields
        $detail_id_mensagem = new THidden('detail_id_mensagem');
        $detail_usuario = new THidden('detail_usuario');
        $detail_conteudo = new TText('detail_conteudo');
        $detail_anexo = new TMultiFile('detail_anexo');
        $detail_botao = new TButton('enviar_mensagem');
        $detail_data_reg = new THidden('detail_data_reg');

        
        // master
        $this->form->addFields( [new TFormSeparator('Detalhes da solicitação')] );
        $this->form->addFields( [$id_solicitacao] );
        $this->form->addFields( [new TLabel('Código do aluno')], [$text2],[new TLabel('')] );
        $this->form->addFields( [new TLabel('Nome')], [$text3], [new TLabel('Matrícula atual')], [$text6]);
        $this->form->addFields( [new TLabel('Tipo de solicitação')], [$text5], [new TLabel('Email para notificações')], [$email_aluno]);
        $this->form->addFields( [new TLabel('Observação')], [$text4], [new TLabel('Arquivo(s) anexo(s)')], [$filename]);
        

        
        
        $btn_save_detail = new TButton('btn_save_detail');
        $btn_save_detail->setAction(new TAction(array($this, 'onSaveDetail')), 'Salvar Informações');
      //  $btn_save_detail->setImage('far:hand-point-down');

        $this->form->addFields( [new TFormSeparator('Envio de mensagens')] );

        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->detail_list->setHeight( 300 );
        $this->detail_list->disableDefaultClick();
        
        // items
        $this->detail_list->addQuickColumn('Usuário', 'usuario', 'left', 200);
        $this->detail_list->addQuickColumn('Mensagem', 'conteudo', 'left', 1200);
        $this->detail_list->addQuickColumn('Data', 'data_reg', 'left', 100);
        $this->detail_list->createModel();
        $this->form->addContent([$this->detail_list]);

        TTransaction::open('Felabs_DB');
        $logged = SystemUser::newFromLogin(TSession::getValue('login'));


        // detail
        $this->form->addFields( [new TFormSeparator('')] );

        if($logged->funcao_legado != 'Aluno')
        {
            $this->form->addFields( [new TLabel('Mensagem')], [$detail_conteudo],[new TLabel('Atribuições')],[$atribuir]);
        }
        else
        {
            $this->form->addFields( [new TLabel('Mensagem')], [$detail_conteudo], [new TLabel('')]);
        }
        $this->form->addFields( [new TLabel('Anexo')], [$detail_anexo], [new TLabel('')]);
      
        TTransaction::close();

        $detail_conteudo->setSize('100%');
        $detail_anexo->setSize('100%');

        $detail_conteudo->addValidation('Mensagem', new TRequiredValidator);

        $this->form->addFields( [new TLabel('')], [$btn_save_detail]);
        

        
        // define form fields
        $this->formFields   = array($id_solicitacao,$cod_aluno,$matricula_aluno,$nome_aluno,$email_aluno,$tipo_solicitacao1,$obs_solicitacao,$obs_secretaria,$status_solicitacao,$status_pgto,$quem_realizou,$filename,$filename_secretaria,$ultima_edicao,$detail_usuario,$detail_conteudo,$detail_anexo,$detail_data_reg,$atribuir);
        $this->formFields[] = $btn_save_detail;
        $this->formFields[] = $detail_id_mensagem;
        $this->form->setFields( $this->formFields );
        
    //    $this->form->addAction('Voltar',new TAction(array('SolicitacaoAlunoList','onReload')),'far:arrow-alt-circle-left blue');
        $this->form->addAction('Finalizar Atendimento', new TAction([$this, 'finaliza']), 'far:window-close')->addStyleClass('btn-primary');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';

     //   $container1 = new TVBox;
      //  $container1->style = 'width: 100%';

     //   $formulario = new BootstrapFormBuilder('form_formulario');
      //  $formulario->class = 'tform'; // CSS class
      //  $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
    //    $container->add(new TAlert('info', 'Atenção: Os alunos ainda não possuem acesso a esta página. Portanto, todas as mensagens e anexos inseridos aqui serão enviados para o email do aluno.'));
      //  $this->form->style = 'width:50%;float:left;padding:10px';
        $container->add($this->form);
      //  $container1->add($formulario);

        $div = new TElement('div');
        $div->add($a = $container);
      //  $div->add($b = $container1);

        $a->style = 'width:100%;';
     //   $b->style = 'width:30%;';
       // $div->add( $b=new PedidosEstadoChartView(false) );

        
        parent::add($div);
    }



    public function finaliza($param) //PERGUNTA SE TEM CERTEZA QUE QUER FINALIZAR A SOLICITAÇÃO
    {
        // define the delete action
        $action = new TAction(array($this, 'onFinaliza'));
        $action->setParameters($param); // pass the key parameter ahead
        
        $action2 = new TAction(array($this, 'onEdit'));
        
        $parametros = [];
        $parametros['id_solicitacao'] = $param['id_solicitacao'];
        $parametros['key'] = $param['id_solicitacao'];

        $action2->setParameters($parametros); // pass the key parameter ahead


        TTransaction::open('Felabs_DB'); // open a transaction with database
        $object = new SolicitacaoAluno($param['id_solicitacao']);
        $statusAtual = $object->status_solicitacao;
        TTransaction::close();

        if($statusAtual != 'Finalizada'){
            new TQuestion(("Tem certeza que deseja finalizar esta solicitação? <br>Uma vez finalizada não será mais possível dar continuidade a este atendimento."), $action, $action2);
        }
        else{
         //   new TMessage('error','Esta solicitação já está finalizada.');
            new TMessage('error', 'Esta solicitação já está finalizada.');
            TApplication::loadPage('SolicitacaoAlunoFormMensagens','onEdit',$parametros);

        }

    }





    public function onDownloadMaster($param)
    {
        try
        {
            
            
                $id = $param['id_solicitacao'];  // get the parameter $key
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new SolicitacaoAluno($id); // instantiates the Active Record
                TTransaction::close(); // close the transaction

                if(!empty($object-> filename))
                {              
                    if (strtolower(substr($object->filename, -4)) == 'html')
                    {
                        $win = TWindow::create( $object->filename, 0.8, 0.8 );
                        $win->add( file_get_contents( "files/solicitacao_atendimento/".$object->filename ) );
                        $win->show();

                    }
                    else
                    {
                        TPage::openFile($object->filename);
                    }
                    $this->form->setData( $this->form->getData() ); // keep form data
                    $parametros = [];
                    $parametros['id_solicitacao'] = $param['id_solicitacao'];
                    $parametros['key'] = $param['id_solicitacao'];

                    TApplication::loadPage('SolicitacaoAlunoFormMensagens','onEdit',$parametros);
                    TTransaction::rollback();
                }
                else
                {
                    new TMessage('info', 'Esta solicitação não possui anexos'); 
                }
            
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }



    public function onFinaliza($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('Felabs_DB');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            
            $data = $this->form->getData();
            $master = new SolicitacaoAluno($param['id_solicitacao']);
          //  $master->fromArray( (array) $data);

       //     $this->form->validate(); // form validation

            $master-> quem_realizou = $logged-> id;
            $master-> ultima_edicao = date('Y-m-d H:i:s');
            $master-> status_solicitacao = "Finalizada";

            $master->store(); // save master object
            
            // delete details
            $old_items = Mensagem::where('solicitacaoaluno_id', '=', $master->id_solicitacao)->load();
            
            $keep_items = array();
            
            // get session items
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id_mensagem'],0,1) == 'X' ) // new record
                    {
                        $detail = new Mensagem;
                    }
                    else
                    {
                        $detail = Mensagem::find($item['id_mensagem']);
                    }

                    $detail->usuario  = $item['usuario'];
                    $detail->conteudo  = $item['conteudo'];
                    $detail->anexo  = $item['anexo'];
                    $detail->data_reg  = $item['data_reg'];
                    $detail->solicitacaoaluno_id = $master->id_solicitacao;
                    $detail->store();
                    
                    $keep_items[] = $detail->id_mensagem;
                }
            }


            
            if ($old_items)
            {
                foreach ($old_items as $old_item)
                {
                    if (!in_array( $old_item->id_mensagem, $keep_items))
                    {
                        $old_item->delete();
                    }
                }
            }

        

            TTransaction::close(); // close the transaction
            
            // reload form and session items
            $this->onEdit(array('key'=>$master->id_solicitacao));
            
            new TMessage('info', 'Solicitação finalizada com sucesso');

            $parametros = [];
            $parametros['id_solicitacao'] = $param['id_solicitacao'];
            $parametros['key'] = $param['id_solicitacao'];

            TApplication::loadPage('SolicitacaoAlunoFormMensagens','onEdit',$parametros);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    /**
     * Save an item from form to session list
     * @param $param URL parameters
     */
    public function onSaveDetail( $param )    //ENVIO DE MENSAGENS
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            $this->form->validate(); // validate form data
            $data = $this->form->getData();

            
            $items = TSession::getValue(__CLASS__.'_items');
            $key = empty($data->detail_id_mensagem) ? 'X'.mt_rand(1000000000, 1999999999) : $data->detail_id_mensagem;

          //  $primeiroNome = explode(" ", $logged->name);
            
            if(isset($data-> detail_anexo))
            {

                $zip = new ZipArchive();
                $usuarioLogado = $logged-> id;
                $today = date("Ymd");
                $nomeArquivo = "arquivo"."_$today_".time().'.zip';
                $nomeCaminho = "files/solicitacao_atendimento/".$nomeArquivo;
                $zip->open( "$nomeCaminho" , ZipArchive::CREATE);
            
                foreach ($data-> detail_anexo as $arq)
                {
                    $source_file   = 'tmp/'.$arq;

                
                    if (file_exists($source_file))
                    {

                        $zip->addFile(  'tmp/'.$arq , "$arq" );
                    
                    }
                }
                $zip->close();

                $data-> detail_anexo = "$nomeArquivo";
            }



            $solicitacao = new SolicitacaoAluno($param['id_solicitacao']); // PEGA O TIPO DE SOLICITAÇÃO


            if($solicitacao->unidade == 1)
                {
                    $tipoSolicitacao = new SolicitacaoCnsc($solicitacao->tipo_solicitacao);
                    $solicitacao->tipo_solicitacao1 = $tipoSolicitacao->tipo_doc_cnsc;
                }
                elseif($solicitacao->unidade == 2 || $solicitacao->unidade == 4 || $solicitacao->unidade == 6)
                {
                    $tipoSolicitacao = new SolicitacaoFfcl($solicitacao->tipo_solicitacao);
                    $solicitacao->tipo_solicitacao1 = $tipoSolicitacao->tipo_doc_ffcl;
                }



            if($data->email_aluno)  //VERIFICA SE INSERIU EMAIL NO CAMPO DESIGNADO
            {

                if($param['email_aluno'] != $logged->email) //EVITA QUE O ALUNO/USUÁRIO RECEBA EMAIL DE NOTIFICAÇÃO QUANDO ELE MESMO ENVIA MSG
                {         
                
                $prefs = SystemPreference::getAllPreferences();


                if(!empty($data-> obs_secretaria))
                {
                    $conteudo="Comentário: "."$data->obs_secretaria";
                }

                $solicitacaoInfo="Protocolo: $data->id_solicitacao

Solicitação: $solicitacao->tipo_solicitacao1 

Mensagem: $data->detail_conteudo


Att.

Secretaria Online - FE. 
Para mais informações, favor entrar em contato.";

                $mail = new TMail;
                $mail->setFrom($logged-> email, 'Solicitação - Secretaria Online');
                $mail->setSubject('Cópia de solicitação - Secretaria Online FE');
                $mail->setTextBody("Prezado(a) aluno(a),

Você recebeu uma mensagem da Secretaria Online - FE.

------------------------------

$solicitacaoInfo
");  
            
                $mail->addAddress($data-> email_aluno);
              //  $prefs['smtp_pass']='FeL@bs2017#';
                
                if(isset($nomeArquivo))
                {
                    $mail->addAttach($nomeCaminho, $name = 'arquivo'."_$today_".time().'.zip');
                }
  
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();
                
                
                
                }

                new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));   

                $items[ $key ] = array();
                $items[ $key ]['id_mensagem'] = $key;
                $items[ $key ]['usuario'] = $logged->id;
                $items[ $key ]['anexo'] = $data->detail_anexo;
                $items[ $key ]['conteudo'] = $data->detail_conteudo;

                if(!empty($items[ $key ]['anexo']))
                {
                    $items[ $key ]['conteudo'] = $data->detail_conteudo."<br><b>Anexo:</b> <a href='http://localhost/academico/files/solicitacao_atendimento/{$items[ $key ]['anexo']}'>Clique aqui para fazer o download</a>";
                }
                else
                {
                    $items[ $key ]['conteudo'] = $data->detail_conteudo;
                }

          //  $items[ $key ]['data_reg'] = $data->detail_data_reg;
                $items[ $key ]['data_reg'] = date('Y-m-d H:i:s');


            
                TSession::setValue(__CLASS__.'_items', $items);
            
                // clear detail form fields
                $data->detail_id_mensagem = '';
                $data->detail_usuario = '';
                $data->detail_conteudo = '';
                $data->detail_anexo = '';
                $data->detail_data_reg = '';
            
                TTransaction::close();
                $this->onSave();
                $this->form->setData($data);

                $this->onReload( $param ); // reload the items      

                $parametros = [];
                $parametros['id_solicitacao'] = $param['id_solicitacao'];
                $parametros['key'] = $param['id_solicitacao'];

                TApplication::loadPage('SolicitacaoAlunoFormMensagens','onEdit',$parametros);


            }
            else
            {
                new TMessage('info', 'O campo endereço de email do aluno deve estar preenchido');
                $this->form->setData($data);
                $this->onReload( $param ); // reload the items
            }


        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Load an item from session list to detail form
     * @param $param URL parameters
     */
    public function onEditDetail( $param )
    {
        $data = $this->form->getData();
        
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get the session item
        $item = $items[ $param['item_key'] ];
        
        $data->detail_id_mensagem = $item['id_mensagem'];
        $data->detail_solicitacaoaluno_id = $item['solicitacaoaluno_id'];
        $data->detail_usuario = $item['usuario'];
        $data->detail_conteudo = $item['conteudo'];
        $data->detail_anexo = $item['anexo'];
        $data->detail_data_reg = $item['data_reg'];
        
        // fill detail fields
        $this->form->setData( $data );
    
        $this->onReload( $param );
    }
    
    /**
     * Delete an item from session list
     * @param $param URL parameters
     */

    /**
     * Load the items list from session
     * @param $param URL parameters
     */
    public function onReload($param)
    {
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        $this->detail_list->clear(); // clear detail list
        $data = $this->form->getData();
        
        if ($items)
        {
            $cont = 1;
            foreach ($items as $list_item_key => $list_item)
            {
                $item_name = 'prod_' . $cont++;
                $item = new StdClass;

                
                // items
                $item->id_mensagem = $list_item['id_mensagem'];

                TTransaction::open('Felabs_DB');

                $user = new SystemUser($list_item['usuario']);

                TTransaction::close();


                $item->usuario = $user->name;
                $item->conteudo = $list_item['conteudo'];
                $item->anexo = $buttonA;
              //  $item->data_reg = TDate::date2br($list_item['data_reg']);

                $horario=substr($list_item['data_reg'],11,8);
                $dataUs=TDate::date2br($list_item['data_reg']);
                $item->data_reg = "$dataUs"." "."$horario";
                
                $row = $this->detail_list->addItem( $item );
                $row->onmouseover='';
                $row->onmouseout='';
            }

            $this->form->setFields( $this->formFields );
        }
        
        $this->loaded = TRUE;
    }
    
    /**
     * Load Master/Detail data from database to form/session
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new SolicitacaoAluno($key);
                $items  = Mensagem::where('solicitacaoaluno_id', '=', $key)->load();

                $atribuir_ids = explode(",", $object->atribuir);

              //  var_dump($object->atribuir);
            //    die();
                $object->atribuir = $atribuir_ids;

                /*
                if($object->unidade == 1 )
                {
                    $tipoSolicitacao = new SolicitacaoCnsc($object->tipo_solicitacao);
                    $object->tipo_solicitacao1 = $tipoSolicitacao->tipo_doc_cnsc;
                }
                elseif($object->unidade == 2 || $object->unidade == 4 || $object->unidade == 6)
                {
                    $tipoSolicitacao = new SolicitacaoFfcl($object->tipo_solicitacao);
                    $object->tipo_solicitacao1 = $tipoSolicitacao->tipo_doc_ffcl;
                }
    */
                if($object->status_solicitacao == 'Finalizada')
                {
                    TText::disableField('form_SolicitacaoAluno', 'detail_conteudo');
                    TMultiFile::disableField('form_SolicitacaoAluno', 'detail_anexo');
                    TButton::disableField('form_SolicitacaoAluno', 'btn_save_detail');
                    TDBMultiSearch::disableField('form_SolicitacaoAluno', 'atribuir');
                }

                if(empty($object->filename))
                {
                    TButton::disableField('form_SolicitacaoAluno', 'filename');
                }

                

                $session_items = array();
                foreach( $items as $item )
                {
                    $item_key = $item->id_mensagem;
                    $session_items[$item_key] = $item->toArray();
                    $session_items[$item_key]['id_mensagem'] = $item->id_mensagem;
                    $session_items[$item_key]['usuario'] = $item->usuario;
                    $session_items[$item_key]['conteudo'] = $item->conteudo;
                    $session_items[$item_key]['anexo'] = $item->anexo;
                    $session_items[$item_key]['data_reg'] = $item->data_reg;
                }
                TSession::setValue(__CLASS__.'_items', $session_items);
                
                $this->form->setData($object); // fill the form with the active record data
                $this->onReload( $param ); // reload items list
                TTransaction::close(); // close transaction
            }
            else
            {
                $this->form->clear(TRUE);
                TSession::setValue(__CLASS__.'_items', null);
                $this->onReload( $param );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form/session to database
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database
            TTransaction::open('Felabs_DB');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            $prefs = SystemPreference::getAllPreferences();
            
            $data = $this->form->getData();
            $master = new SolicitacaoAluno($data->id_solicitacao);

     

            if($data->atribuir) //SE TIVER ATUALIZAÇÃO NAS ATRIBUIÇÕES, ENVIA EMAIL PARA USUÁRIOS NOTIFICANDO-OS
            {


                $array1 = $data->atribuir;
                $array2 = explode(',', $master->atribuir);
                $result2 = array_diff($array1, $array2); //PEGA APENAS NOVOS VALORES DA ARRAY DE USUÁRIOS DAS ATRIBUIÇÕES


                foreach($array1 as $userAtribId)
                {

                $atribuirUser = new SystemUser($userAtribId);

                if($logged->email){

                $mail = new TMail;
                $mail->setFrom($logged-> email, 'Solicitação - Secretaria Online FE');
                $mail->setSubject('Solicitação - Secretaria Online FE');
                $mail->setTextBody("$atribuirUser->name,

Uma solicitação de atendimento atribuída ao seu usuário foi atualizada. Por favor acesse o FE Acadêmico e verifique.

------------------------------

Secretaria Online - Fundação Educacional de Ituverava
http://feituverava.com.br
");  
            
                $mail->addAddress($atribuirUser->email);
   
                
                if($nomeArquivo)
                {
                    $mail->addAttach($nomeCaminho, $name = 'arquivo'."_$today_".time().'.zip');
                }
  
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();
                }

                SystemNotification::register(
                                            $userAtribId,
                                            'Nova solicitação de atendimento',
                                            'Uma solicitação de atendimento atribuída ao seu usuário foi atualizada',
                                            "class=SolicitacaoAlunoFormMensagens&method=onEdit&key=$master->id_solicitacao&id_solicitacao=$master->id_solicitacao",
                                            'Ver Solicitação',
                                            'far fa-list-alt green'
                                            );

                }

            }


            
     

            

            $this->form->validate(); // form validation

            $master->email = $data->email_aluno;
            $master->atribuir = implode(",", $data->atribuir);

            $master-> quem_realizou = $logged-> id;
            $master-> ultima_edicao = date('Y-m-d H:i:s');
            $master-> status_solicitacao = "Em Progresso";


            
            $master->store(); // save master object
            // delete details
            $old_items = Mensagem::where('solicitacaoaluno_id', '=', $master->id_solicitacao)->load();
            
            $keep_items = array();
            
            // get session items
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id_mensagem'],0,1) == 'X' ) // new record
                    {
                        $detail = new Mensagem;
                    }
                    else
                    {
                        $detail = Mensagem::find($item['id_mensagem']);
                    }


                    $detail->usuario  = $item['usuario'];
                    $detail->conteudo  = $item['conteudo'];
                    $detail->anexo  = $item['anexo'];
                    $detail->data_reg  = $item['data_reg'];
                    $detail->solicitacaoaluno_id = $master->id_solicitacao;
                    $detail->store();
                    
                    $keep_items[] = $detail->id_mensagem;
                }
            }


            
            if ($old_items)
            {
                foreach ($old_items as $old_item)
                {
                    if (!in_array( $old_item->id_mensagem, $keep_items))
                    {
                        $old_item->delete();
                    }
                }
            }

        

            TTransaction::close(); // close the transaction
            
            // reload form and session items
            $this->onEdit(array('key'=>$master->id_solicitacao));
        //    $this->form->setData($master);
            
          //  new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    /**
     * Show the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
