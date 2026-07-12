<?php
/**
 * SolicitacaoAlunoForm Form
 * @author  <your name here>
 */
class SolicitacaoAlunoForm extends TPage
{
    private $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_SolicitacaoAluno');
     //   $this->form->class = 'tform'; // change CSS class
      //  $this->form = new BootstrapFormWrapper($this->form);
    //    $this->form->style = 'width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Solicitação de Atendimento - Secretaria');


        // create the form fields
        $id_solicitacao = new THidden('id_solicitacao');
    //    $cod_aluno = new TDBSeekButton('cod_aluno', 'dados_fei', 'form_SolicitacaoAluno', 'VwAluno', 'NomeAluno', 'Codaluno', 'nome_aluno');
        $cod_aluno = new TDBSeekButton('cod_aluno', 'dados_fei', 'form_SolicitacaoAluno', 'FiAluno', 'Nome', 'cod_aluno', 'nome_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $matricula_aluno = new TEntry('matricula_aluno');
      //  $matricula_aluno1 = new TEntry('matricula_aluno1');
        $unidade = new TRadioGroup('unidade');
        $email_aluno = new TEntry('email_aluno');
        $email_cc = new TEntry('email_cc');
      //  $tipo_solicitacao = new TDBCombo('tipo_solicitacao','atendimento','PrecosFfcl','id','{tipo_doc_ffcl}','id');
        $tipo_solicitacao = new TCombo('tipo_solicitacao');
        $obs_solicitacao = new TText('obs_solicitacao');
        $status_solicitacao = new THidden('status_solicitacao');
        $status_pgto = new THidden('status_pgto');
        $quem_abriu = new THidden('quem_abriu');
        $quem_realizou = new THidden('quem_realizou');
        $data_reg = new THidden('data_reg');
        $ultima_edicao = new THidden('ultima_edicao');
        $finaliza = new TCheckGroup('finaliza');
        $email = new TCheckGroup('email');
        $filename = new TMultiFile('filename');
        $atribuir = new TDBMultiSearch('atribuir', 'permission', 'SystemUser', 'id', 'name');

        // set exit action for input_exit
        $change_action = new TAction(array($this, 'onChangeAction'));
        $unidade->setChangeAction($change_action);

        $items = array();
        $items['1'] ='CNSC';
        $items['2'] ='FFCL';
        $items['3'] ='FAFRAM';
     //   $items['4'] ='FFCL PÓS'
        $items['6'] ='NEAD';
      //  $items['8'] ='VAN GOGH';
     
        $unidade->addItems($items);
        $unidade->setLayout('horizontal');

        $chek = array();
        $chek['finaliza'] ='Finalizar solicitação';

        $chek1 = array();
        $chek1['Sim'] ='Enviar cópia desta solicitação para email do aluno (inclui anexo(s))';

        $finaliza->addItems($chek);
        $email->addItems($chek1);

        $filename->setCompleteAction(new TAction(array($this, 'onComplete')));
        $filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );

        // add the fields
        $this->form->addFields( [new TLabel('Código do aluno')], [$cod_aluno]);
        $this->form->addFields( [new TLabel('Nome')], [$nome_aluno],[new TLabel('')],[$unidade]);
        $this->form->addFields( [new TLabel('Email do aluno')], [$email_aluno],  [new TLabel('Matrícula atual')], [$matricula_aluno] );
        $this->form->addFields( [new TLabel('Email cópia')], [$email_cc], [new TLabel('Anexar arquivo')], [$filename]);
        $this->form->addFields( [new TLabel('Tipo de solicitação')], [$tipo_solicitacao],[new TLabel('')] );
        $this->form->addFields( [new TLabel('Observação')], [$obs_solicitacao]);
        $this->form->addFields( [new TLabel('')], [$finaliza]);
        $this->form->addFields( [new TLabel('')], [$email]);

        $this->form->addField($status_solicitacao);
        $this->form->addField($status_pgto);
        $this->form->addField($quem_abriu);
        $this->form->addField($quem_realizou);
        $this->form->addField($data_reg);
        $this->form->addField($ultima_edicao);

        $cod_aluno->addValidation('cod_aluno', new TRequiredValidator);
        $tipo_solicitacao->addValidation('tipo_solicitacao', new TRequiredValidator);
        $unidade->addValidation('unidade', new TRequiredValidator);

        $exit_action = new TAction(array($this, 'onExitAction'));
        $cod_aluno->setExitAction($exit_action);
        
        $cod_aluno->setSize(200);
        $nome_aluno->setSize('100%');
        $matricula_aluno->setSize('100%');
     
        $nome_aluno->setEditable(FALSE);
        $matricula_aluno->setEditable(FALSE);

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Voltar',new TAction(['SolicitacaoAlunoList','onReload']),'far:arrow-left blue');
      //  $btn1 = $this->form->addAction( 'Salvar e Criar Outra', new TAction(array($this, 'onSaveNew')), 'far:plus-square');
       // $btn1->class = 'btn btn-sm btn-primary';
    //    $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
      //  $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);        
    }

    /**
     * Save form data
     * @param $param Request
     */

    public static function onChangeAction($param) //LISTA OS TIPOS DE DOC DA MANTIDA ESCOLHIDA NO COMBO
    {
        TTransaction::open('Felabs_DB');

        if($param['unidade'] == 1){

            $optionsCNSC = [];

            $precosCNSC = SolicitacaoCnsc::getObjects();

            foreach($precosCNSC as $precoCNSC){
                $optionsCNSC[$precoCNSC->id] = $precoCNSC->tipo_doc_cnsc;
            }

            TCombo::reload('form_SolicitacaoAluno', 'tipo_solicitacao', $optionsCNSC);
        }

        if($param['unidade'] == 2 || $param['unidade'] == 6){
        
            $optionsFFCL = [];

            $precosFFCL = SolicitacaoFfcl::getObjects();

            foreach($precosFFCL as $precoFFCL){
                $optionsFFCL[$precoFFCL->id] = $precoFFCL->tipo_doc_ffcl;
            }

            TCombo::reload('form_SolicitacaoAluno', 'tipo_solicitacao', $optionsFFCL);
        }

        TTransaction::close();
    }

    public static function onExitAction($param) //INSERE NOME, EMAIL E DADOS DA MATRÍCULA
    {
 
      //  $object = new StdClass;
     //   $object-> email_aluno = 'teste';
     //   $object->combo_change = 'a';

        $numeroId=$param[cod_aluno];
      //  $alunoId=$param['key'];

        TTransaction::open('dados_fei');

        $anoHoje = date('Y');
        $mesHoje = date('m');

        $object=new StdClass;

        try{

        $aluno= new FiAluno($numeroId);
        $object-> email_aluno = $aluno-> Email;

        try
        {
            $alunoCurso= new VwAluno($numeroId);



            $anoAtual = $anoHoje;

            $mesAtual = $mesHoje;


            if($mesAtual < 7){
                $semestreM = 1;
            }else{
                $semestreM = 2;
            }

            if($alunoCurso->CodEntidade == 1){
                $semestreM = 1;
            }



            $criteria = new TCriteria;                        
            $criteria->add(new TFilter('Codaluno', '=', $numeroId));            
            $criteria->add(new TFilter('AnoMatricula', '=', $anoAtual));            
            $criteria->add(new TFilter('SemestreMatricula', '=', $semestreM));

            $alunoView= new TRepository('VwAluno');
            $alunoSemestre = $alunoView->load($criteria);

            $numeroCiclo = $alunoSemestre[0]->EtapaMatricula;
            $codEntidade = $alunoCurso->CodEntidade;

            if($numeroCiclo){
            $cicloAluno = " - CICLO ".$numeroCiclo;
            }

            if($alunoSemestre[0]->NomeCurso){
                $object-> matricula_aluno = $alunoSemestre[0]->NomeCurso.$cicloAluno;
            }

            


            if(empty($alunoSemestre[0]->NomeCurso)){
            $criteria1 = new TCriteria;                        
            $criteria1->add(new TFilter('Codaluno', '=', $numeroId));            
            $criteria1->add(new TFilter('AnoMatricula', '=', $anoAtual));            
            $criteria1->add(new TFilter('SemestreMatricula', '=', $semestreM));

            $alunoView1= new TRepository('VwAluno');
            $alunoSemestre1 = $alunoView1->load($criteria1);
            $object-> matricula_aluno = $alunoSemestre1[0]->NomeCurso.$cicloAluno;
            }
           
            if(empty($object-> matricula_aluno)){
                $object-> matricula_aluno = 'MATRÍCULA NÃO ENCONTRADA';
            }




            if($codEntidade == 1){ //CNSC
                $campus = 1;
            }elseif($codEntidade == 2 || $codEntidade == 5){ //FFCL
                $campus = 2;
            }elseif($codEntidade == 3){ //FAFRAM
                $campus = 3;
            }elseif($codEntidade == 6){
                $campus = 6;
            }

            if($codEntidade == 3){ //FAFRAM
                new TMessage('info', 'Atenção: Atualmente o sistema de atendimento está ativo apenas para CNSC e FFCL. Favor não registrar solicitações de outras mantidas.');
            }    



            $object-> unidade = $campus;
        }
        catch(Exception $e){
           

            
        }


    }
    catch(Exception $e){
        new TMessage('info', 'Aluno não encontrado');
    }



        TTransaction::close();


        
        TForm::sendData('form_SolicitacaoAluno', $object);
     //   new TMessage('info', 'Message on field exit. <br>You have typed: ' . $param['input_exit']);
        
    }


    public static function onComplete($param)
    {
        new TMessage('info', 'Arquivo enviado com sucesso: '.$param['filename']);
        
        // refresh photo_frame
        TScript::create("$('#filename').html('')");
        TScript::create("$('#filename').append(\"<img style='width:100%' src='tmp/{$param['filename']}'>\");");
    }




    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new SolicitacaoAluno;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object-> quem_abriu = $logged-> id;
         //   $object-> ultima_edicao = date("Y-m-d H:i:s");
            $object-> data_reg = date('Y-m-d H:i:s');

            $object-> email_cc = $data->email_cc;


            $verificaFinaliza=$data-> finaliza;
            $verificaEmail=$data-> email;

            
          	$object-> atribuir = implode(",", $data->atribuir);



            if(isset($data-> filename)){

            $zip = new ZipArchive();
            $usuarioLogado = $logged-> id;
            $today = date("Ymd");
            $nomeArquivo = "arquivo"."_$today_".time().'.zip';
            $nomeCaminho = "files/solicitacao_atendimento/".$nomeArquivo;
            $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
            
            foreach ($data-> filename as $arq)
            {
                $source_file   = 'tmp/'.$arq;
            //    $target_file   = 'images/' . $arq;
                
                if (file_exists($source_file))
                {

                    $zip->addFile(  'tmp/'.$arq , "$arq" );
                    
                }
            }
            $zip->close();

            $object-> filename = $nomeArquivo;
            }


            $object-> status_solicitacao = "Aberta";

            

            if ($verificaFinaliza[0]=="finaliza"){
                $object-> quem_realizou = $logged-> id;
                $object-> status_solicitacao = "Finalizada";
            }

            
            
            // get the generated id_solicitacao
            $data->id_solicitacao = $object->id_solicitacao;

            if($verificaEmail[0]==NULL){
            $object->store(); // save the object
            new TMessage('info', 'Registro salvo');
            }



            if($verificaEmail[0]=='Sim'){     //VERIFICA SE ESCOLHEU OPÇÃO ENVIAR CÓPIA EMAIL PARA ALUNO

            if(!empty($object-> email_aluno)){          //VERIFICA SE INSERIU EMAIL NO CAMPO DESIGNADO
                $emailAluno= $object-> email_aluno;

                $object->store(); // save the object

                $prefs = SystemPreference::getAllPreferences();

                $status_solicitacao=$object-> status_solicitacao;

                $conteudoComentario=$object-> obs_secretaria;

                $tipoSolicitacao=$object-> tipo_solicitacao;


                $pegaTipo= new SolicitacaoFfcl($tipoSolicitacao);
                $nomeTipo= $pegaTipo-> tipo_doc_ffcl;

                

                if(!empty($object-> obs_secretaria)){
                    $conteudo="Comentário: "."$object->obs_secretaria";
                }

                $solicitacaoInfo="Protocolo: $object->id_solicitacao

Solicitação: $nomeTipo 

Conteúdo: $object->obs_solicitacao



Secretaria Online - FE. 
Para mais informações, favor entrar em contato.";

                $mail = new TMail;
                $mail->setFrom($logged-> email, 'Solicitação - Secretaria Online');
                $mail->setSubject('Cópia de solicitação - Secretaria Online FE');
                $mail->setTextBody("Prezado(a) aluno(a),

Esta é uma cópia da sua solicitação aberta em Secretaria Online - FE.

------------------------------

$solicitacaoInfo
");  
            
                $mail->addAddress($emailAluno);

                $prefs['smtp_pass']='FeL@bs2017#';
                if(isset($nomeArquivo)){
                $mail->addAttach($nomeArquivo, $name = 'arquivo'."_$today_".time().'.zip');
                }
  
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();
                
                new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));

            }

            else{
                new TMessage('info', 'Inserir um email válido');
            }


            if(!empty($object-> email_cc))  //ENVIA EMAIL CÓPIA SE ESTIVER PREENCHIDO
                { 

                $mail1 = new TMail;
                $mail1->setFrom($logged-> email, 'Solicitação - Secretaria Online');
                $mail1->setSubject('Cópia de solicitação - Secretaria Online FE');
                $mail1->setTextBody("Prezado(a) aluno(a),

Esta é uma cópia da sua solicitação aberta em Secretaria Online - FE.

------------------------------

$solicitacaoInfo
");  
            
                $mail1->addAddress($object->email_cc);

                $prefs['smtp_pass']='FeL@bs2017#';
                if(isset($nomeArquivo)){
                $mail1->addAttach($nomeArquivo, $name = 'arquivo'."_$today_".time().'.zip');
                }
  
                $mail1->SetUseSmtp();
                $mail1->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail1->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail1->send();

                }






        }

        
        	



            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            TApplication::loadPage('SolicitacaoAlunoList', 'onReload', $param);
        //    new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }

        
        

    }





/*



    public function onSaveNew( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));


            $this->form->validate(); // validate form data
            
            $object = new SolicitacaoAluno;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object-> quem_abriu = $logged-> id;
            $object-> ultima_edicao = date('Y-m-d H:i:s');
            $object-> data_reg = date('Y-m-d H:i:s');


            $verificaFinaliza=$data-> finaliza;
            $verificaEmail=$data-> email;

          //  $dataCriacao= date("d/m/Y");



            if(isset($data-> filename))
            {

            $zip = new ZipArchive();
            $usuarioLogado = $logged-> id;
            $today = date("Ymd");
            $nomeArquivo = "arquivo"."_$today_".time().'.zip';
            $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
            
            foreach ($data-> filename as $arq)
            {
                $source_file   = 'tmp/'.$arq;
            //    $target_file   = 'images/' . $arq;
                
                if (file_exists($source_file))
                {

                    $zip->addFile(  'tmp/'.$arq , "$arq" );
                    
                }
            }
            $zip->close();

            $object-> filename = $nomeArquivo;
            }

            var_dump($object);
            die();


            $object-> status_solicitacao = "Aberta";

            

            if ($verificaFinaliza[0]=="finaliza"){
                $object-> quem_realizou = $logged-> id;
                $object-> status_solicitacao = "Finalizada";
            }

          //  var_dump($object);
          //  die();

            
            // get the generated id_solicitacao
            $data->id_solicitacao = $object->id_solicitacao;

            if($verificaEmail[0]==NULL){
            $object->store(); // save the object
            new TMessage('info', 'Registro salvo');
            }



            if($verificaEmail[0]=='Sim'){     //VERIFICA SE ESCOLHEU OPÇÃO ENVIAR CÓPIA EMAIL PARA ALUNO

            if(!empty($object-> email_aluno)){          //VERIFICA SE INSERIU EMAIL NO CAMPO DESIGNADO
                $emailAluno= $object-> email_aluno;

                $object->store(); // save the object

                $prefs = SystemPreference::getAllPreferences();

                $status_solicitacao=$object-> status_solicitacao;

                $conteudoComentario=$object-> obs_secretaria;

                $tipoSolicitacao=$object-> tipo_solicitacao;

                $pegaTipo= new SolicitacaoFfcl($tipoSolicitacao);
                $nomeTipo= $pegaTipo-> tipo_doc_ffcl;

                

                if(!empty($object-> obs_secretaria)){
                    $conteudo="Comentário: "."$object->obs_secretaria";
                }

                $solicitacaoInfo="Protocolo: $object->id_solicitacao

Solicitação: $nomeTipo 

Conteúdo: $object->obs_solicitacao



Secretaria Online - FE. 
Para mais informações, favor entrar em contato.";

                $mail = new TMail;
                $mail->setFrom($logged-> email, 'Solicitação - Secretaria Online');
                $mail->setSubject('Cópia de solicitação - Secretaria Online FE');
                $mail->setTextBody("Prezado(a) aluno(a),

Esta é uma cópia da sua solicitação aberta em Secretaria Online - FE.

------------------------------

$solicitacaoInfo
");  
            
                $mail->addAddress($emailAluno);

                $prefs['smtp_pass']='FeL@bs2017#';
                if(isset($nomeArquivo)){
                $mail->addAttach($nomeArquivo, $name = 'arquivo'."_$today_".time().'.zip');
                }
  
                $mail->SetUseSmtp();
                $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                $mail->send();
                
                new TMessage('info', 'Registro salvo e email enviado com sucesso');

            }

            else{
                new TMessage('info', 'Inserir um email válido');
            }
      //      $object->store(); // save the object
        }



            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            TApplication::loadPage('SolicitacaoAlunoForm', 'onClear');
        //    new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }

        
        

    }


*/





    
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
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new SolicitacaoAluno($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
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
