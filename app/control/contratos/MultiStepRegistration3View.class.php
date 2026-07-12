<?php
/**
 * @author     Pamella Scapim
 */
class MultiStepRegistration3View extends TPage 
{
    protected $form;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;
    
    function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_EnvioDocumentos');
        $this->form->setFormTitle(('Envio de Documento de Identificação'));
        $this->form->setClientValidation(true);
        
        
       $sessao_contrato = TSession::getValue('sessao_contrato');
       $idContrato = $sessao_contrato["key"];
       $nome_aluno = $sessao_contrato["NomeAluno"];


        // create the form fields
        $id          = new TEntry('id');
        $NomeAluno   = new TEntry('NomeAluno');
        $filename = new TMultiFile('image');
        
        
        // allow just these extensions
        $filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf'] );
        
        
        // enable progress bar, preview, and gallery mode
        //$photo_path->enableFileHandling();
        $filename->enablePopover();
        
        
        $id->setEditable( FALSE );
        $id->setValue( $idContrato );
        $NomeAluno->setValue( $nome_aluno );
        $NomeAluno->setEditable( FALSE );
               
               
        // add the form fields
        $this->form->addFields( [new TLabel('ID'), $id] );
        $this->form->addFields( [new TLabel('Aluno'), $NomeAluno] );
        $this->form->addFields( [new TLabel('')] );
        $this->form->addFields( [new TLabel('Anexar Documentos','red', 11, 'b'), $filename] );
        /*$this->form->addFields( [new TLabel('<br>Atenção!<br>
                                             <br> - Anexar documento de identificação com foto (Ex: RG, CNH)
                                             <br> - Anexar selfie segurando a frente do documento de identificação próximo ao rosto. Certifique-se de tirá-la em ambiente com iluminação adequada, com boa resolução e de modo que seu rosto não seja tampado pelo documento e que os dados constantes no mesmo estejam visíveis','red', 12, 'b')]);
        $this->form->addFields( [new TLabel('Os dados e fotos enviados passarão por análise e, caso necessário, o processo poderá ser refeito','red', 10, 'b')]);
        */        
        
        $id->setSize('100%');
        $NomeAluno->setSize('100%');
        $filename->setSize('100%');
        $filename->addValidation('"Anexar arquivos"', new TRequiredValidator());


        //Imagem exemplo selfie
        $table = new TTable;
        
        $instrucoes = new TLabel('<br><font color="red"><b>Atenção! Deverão ser enviados no mínimo 2 arquivos:<br><br>
                                  - 1) Documento de identificação com foto (Ex: RG, CNH)<br><br>
                                  - 2) Selfie segurando a frente do documento de identificação próximo ao rosto</font></b><br><br>
                                  - Certifique-se de tirá-la em ambiente com iluminação adequada<br><br>
                                  - Selecione a melhor qualidade de imagem do dispositivo para tirar a foto<br><br>
                                  - Segure o documento próximo ao rosto, conforme a imagem ao lado<br><br>
                                  - Certifique-se de que o seu rosto e o documento estão nítidos na foto<br><br>
                                  <font color="red"><b>- Se a foto não estiver nítida o suficiente, nós solicitaremos um novo envio</font></b>');
        

        //Certifique-se de tirá-la em ambiente com iluminação adequada, boa resolução, de modo que seu rosto não seja tampado pelo documento e que os dados constantes no mesmo estejam visíveis
        
        $row = $table->addRow();
        $instrucoes = $row->addCell($instrucoes);
        $instrucoes->colspan = 1;
        
        $imagem_exemplo = new TImage('app/images/selfie_documento.jpg');
        
        $imagem_exemplo = $row->addCell($imagem_exemplo);
        $imagem_exemplo->colspan = 1;
                
        $this->form->addContent( [ $table ] );

        $pagestep = new TPageStep;
        $pagestep->addItem('Selecionar');
        $pagestep->addItem('Assinatura Digital');
        $pagestep->addItem('Enviar Documento de Indentificação');
        $pagestep->select('Enviar Documento de Indentificação');
        
        
         // add the actions
        $this->form->addAction( 'Enviar Documento', new TAction([$this, 'onSave']), 'fa:save green');
        //$this->form->addActionLink( 'Clear', new TAction([$this, 'onEdit']), 'fa:eraser red');
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
       // $vbox->add(new TXMLBreadCrumb('menu.xml', 'ProductList'));
        $vbox->add( $pagestep );
        $vbox->add( '<br>' );
        $vbox->add($this->form);
        parent::add($vbox);
    }
    
    /**
     * Overloaded method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try
        { 
            TTransaction::open('Felabs_DB');

            $sessao_contrato = TSession::getValue('sessao_contrato');
            $idContrato = $sessao_contrato["key"];
            $cod_aluno = $sessao_contrato["Codaluno"];

            $hoje = date('Y-m-d H:i:s');

            $this->form->validate();
            
            // get form data
            $data   = $this->form->getData();

            // store product
            $object = new ContratoDadosAlunoDoc;

            $object->contrato_aluno_id = $idContrato;
            $object->contrato_aluno_codaluno = $cod_aluno;
            $object->data_reg = date('Y-m-d H:i:s');

            $object->fromArray( (array) $data);

                $zip = new ZipArchive();
                $nomeArquivo = "contratos/"."doc"."_".$idContrato.'_'.$cod_aluno.'.zip';
                $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                
                foreach ($data-> image as $arq)
                {
                    $source_file   = 'tmp/'.$arq;
                    
                    if (file_exists($source_file))
                    {
                        $zip->addFile(  'tmp/'.$arq , "$arq" );                        
                    }
                }

                $zip->close();

            $object->image = $nomeArquivo;
            $object->store();
                        
            // send id back to the form
            $data->id = $object->id;
            $this->form->setData($data);

            $object->store();
            TTransaction::close();

            TTransaction::open('Felabs_DB');
            //TTransaction::setLogger(new TLoggerSTD); // standard output

            $sessao_contrato = TSession::getValue('sessao_contrato');
            $idContrato = $sessao_contrato["key"];
            $cod_aluno = $sessao_contrato["Codaluno"];
            $hoje = date('Y-m-d H:i:s');

            $object2 = ContratoDadosAluno::find($idContrato);

            if ($object2) 
            { 
                $object2->DataHoraAceiteAluno = $hoje;
                $object2->DocAluno = 'Enviado';
                $object2->StatusContrato = ("Finalizado pelo aluno / Pendente de Assinatura Eletrônica da IES");
            }

            $object2->store();
            
            TTransaction::close();

            new TMessage('info', ('Documento enviado com sucesso! Aguardar assinatura eletrônica da IES.'));
            AdiantiCoreApplication::loadPage('ContratoDadosAlunoList');
        }
        catch (Exception $e)
        {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
   
}


            