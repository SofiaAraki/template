<?php
/**

 * @author     Pamella Scapim

 */
class EnvioContratoIES extends TPage 
{
    protected $form;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_EnvioContratoIES');
        $this->form->setFormTitle(('Envio do Contrato Final'));
        $this->form->setClientValidation(true);
        
       $sessao_contrato = TSession::getValue('sessao_contrato');
       $idContrato = $sessao_contrato["key"];
       $nome_aluno = $sessao_contrato["NomeAluno"];

        // create the form fields
        $id          = new TEntry('id');
        $NomeAluno   = new TEntry('NomeAluno');
        $filename = new TMultiFile('contrato_assinado_ies');
        
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
        $this->form->addFields( [new TLabel('Anexar Documento com Assinatura Eletrônica da IES','red', 11, 'b'), $filename] );
        //$this->form->addFields( [new TLabel('Atenção! É necessário anexar uma foto do documento de identificação com foto (Ex.: RG, Carteira de Habilitação)','red', 12, 'bi')]);
        //$this->form->addFields( [new TLabel('É permitido anexar mais de 1 imagem.','red', 10, 'bi')]);
        
        
        $id->setSize('100%');
        $NomeAluno->setSize('100%');
        $filename->setSize('100%');
        $filename->addValidation('Anexar arquivos', new TRequiredValidator());

        
         // add the actions
        $this->form->addAction( 'Enviar Documento', new TAction([$this, 'onSave']), 'fa:save green');
        //$this->form->addActionLink( 'Clear', new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
       // $vbox->add(new TXMLBreadCrumb('menu.xml', 'ProductList'));
       
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

            $hoje = date('Y-m-d');

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
                $nomeArquivo = "contratos/"."doc"."_".$idContrato.'_'.$cod_aluno.'_AssinadoIES'.'_'.$hoje.'.zip';
                $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                
                foreach ($data-> contrato_assinado_ies as $arq)
                {
                    $source_file   = 'tmp/'.$arq;
                    
                    if (file_exists($source_file))
                    {
                        $zip->addFile(  'tmp/'.$arq , "$arq" );                        
                    }
                }

                $zip->close();

            $object->contrato_assinado_ies = $nomeArquivo;
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
                $object2->DocAluno = 'Assinado pela IES';
                $object2->StatusContrato = ("Assinado pela IES");
            }

            $object2->store();
            
            TTransaction::close();

            new TMessage('info', ('Documento enviado com sucesso!'));
            AdiantiCoreApplication::loadPage('ContratoAlunoListSecretaria');
        }
        catch (Exception $e)
        {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
   
}


            