<?php
/**

 * @author     Pamella Scapim

 */
class ContratoAlunoViewSecretaria extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
             
        try
        {
            // create the HTML Renderer
            $this->html = new THtmlRenderer('app/resources/ContratoFinanceiroSecretaria.html');
            $confirmation_data = array_merge(TSession::getValue('sessao_contrato'));

            $this->html->enableSection('main', $confirmation_data);
            
            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->style = 'padding: 25px';
            //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $panel = new TPanelGroup('Contrato Financeiro - Secretaria');
            $panel->addHeaderActionLink('Voltar para Lista', new TAction([$this, 'onBackForm']), 'fa:arrow-left black' );
            $panel->addHeaderActionLink('Download Doc. Indentificação', new TAction([$this, 'downloadArquivo']), 'fa:download green' );
            $panel->addHeaderActionLink('Salvar Contrato', new TAction([$this, 'onExportPDF']), 'far:file-pdf red' );
            $panel->addHeaderActionLink('Enviar Contrato Assinado pela IES', new TAction([$this, 'onEnvioContratoIES']), 'fa:upload blue' );
            
            //$panel->class = 'btn btn-success btn-lg';
            $panel->add($this->html);
            $vbox->add($panel );
            parent::add($vbox);

            



        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onBackForm()
    {
        // Load another page
        AdiantiCoreApplication::loadPage('ContratoAlunoListSecretaria');
    }

    public function onEnvioContratoIES()
    {
        // Load another page
        AdiantiCoreApplication::loadPage('EnvioContratoIES');
    }

    public function downloadArquivo()
    {


        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
                
            $sessao_contrato = TSession::getValue('sessao_contrato');
            $id = $sessao_contrato["key"];
            $CodigoAluno = $sessao_contrato["Codaluno"];
            $CPFAluno = $sessao_contrato["CPF"];
            
            $object = ContratoDadosAlunoDoc::find($id);

            if ($object) 
            { 
                if (strtolower(substr($object->image, -4)) == 'html')
                {
                    $win = TWindow::create( $object->image, 0.8, 0.8 );
                    $win->add( file_get_contents( "contratos/".$object->image ) );
                    $win->show();

                }
                else
                {
                    TPage::openFile($object->image);
                        
                }
            }
            
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }

    }

    public function onExportPDF($param)
    {
        try
        {
            $sessao_contrato = TSession::getValue('sessao_contrato');
            $idContrato = $sessao_contrato["key"];
            $cod_aluno = $sessao_contrato["Codaluno"];
            $nome_aluno = $sessao_contrato["NomeAluno"];

            // string with HTML contents
            $html = clone $this->html;
            $contents = $contents = $this->html->getContents();
            
            $options = new \Dompdf\Options();
            $options->setChroot(getcwd());
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            
            //Se já tiver anexado o contrato assinado pela IES, faz download do contrato assinado pela IES
            TTransaction::open('Felabs_DB');
            
            $object = new ContratoDadosAlunoDoc($idContrato);
            
            TTransaction::close();
            
            if($object->contrato_assinado_ies <> NULL)
            {
                if (strtolower(substr($object->contrato_assinado_ies, -4)) == 'html')
                {
                    $win = TWindow::create( $object->contrato_assinado_ies, 0.8, 0.8 );
                    $win->add( file_get_contents( "contratos/".$object->contrato_assinado_ies ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile($object->contrato_assinado_ies);                        
                }
            }
            else
            {
                //Se não tiver anexado o contrato assinado pela IES, faz download somente do contrato assinado pelo aluno
                $file = 'app/output/ContratoFinanceiroSecretaria'.'_'.$idContrato.'_'.$cod_aluno.'_'.$nome_aluno.'.pdf';
            
                // write and open file
                file_put_contents($file, $dompdf->output());
                
                $window = TWindow::create('ContratoFinanceiroSecretaria', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $file.'?rndval='.uniqid();
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";
                $window->add($object);
                $window->show();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    /*
    function onAction1()
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **//*
            $sessao_contrato = TSession::getValue('sessao_contrato');
            $id = $sessao_contrato["key"];
            $CodigoAluno = $sessao_contrato["Codaluno"];
            $CPFAluno = $sessao_contrato["CPF"];
            

            $object = ContratoDadosAluno::find($id);

            if ($object) 
            { 
               
                $object->StatusContrato = ('Assinado Pelo Aluno / Envio de Documento Pendente');
                $object->AssinaturaAluno = ('Cod'.$CodigoAluno .'_' .'CPF'. $CPFAluno.'_' .'Assinado');
                $object->IPAluno = $_SERVER['REMOTE_ADDR'];      
                                
                $object->store();             
                TTransaction::close();
            }
            
            new TMessage('info', ('Requerimento e Contrato assinados com sucesso!'));
            AdiantiCoreApplication::loadPage('MultiStepRegistration3View');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            //$this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }*/

    
    function loadPage()
    {}
}
