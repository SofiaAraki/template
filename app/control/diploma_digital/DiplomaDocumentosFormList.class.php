<?php

//use Mpdf\Mpdf;
//use setasign\Fpdi\Fpdi;

//use Jeidison\JSignPDF\JSignPDF;
//use Jeidison\JSignPDF\Sign\JSignParam;


class DiplomaDocumentosFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        //Para preenchimento do cabeçalho da datagrid e formulário
        $dados_documentacao = TSession::getValue('dados_documentacao');
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($dados_documentacao->id);
            $diplomado = $dados_documentacao->diploma_digital_diplomado->nome;
            $curso = $dados_documentacao->diploma_digital_curso->nome_curso_diploma;
        
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }        
                        
        
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalDocumentos');
        $this->form->setFormTitle('<h4>Documentos para Registro Acadêmico<h4>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $tipo_arquivo = new TCombo('tipo_arquivo');
        $arquivo = new TFile('arquivo');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $observacoes = new TText('observacoes');
        $dados_documentacao_id = new TEntry('dados_documentacao_id');
        $status_pdfa = new THidden('status_pdfa');
        $status_assinatura = new THidden('status_assinatura');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        
        $nome_aluno = new TEntry('nome_aluno'); //Apenas para exibição, não será salvo no banco
        $nome_curso = new TEntry('nome_curso'); //Apenas para exibição, não será salvo no banco


        //Tipos aceitos pelo MEC
        $combo_tipo = [];
        $combo_tipo['AtoNaturalizacao'] = "Ato de naturalização";
        $combo_tipo['CertidaoCasamento'] = "Certidão de casamento";
        $combo_tipo['CertidaoNascimento'] = "Certidão de nascimento";
        $combo_tipo['ComprovacaoEstagioCurricular'] = "Comprovante de estágio curricular";
        $combo_tipo['DocumentoIdentidadeDoAluno'] = "Documento de identidade";
        $combo_tipo['ProvaColacao'] = "Prova de colação de grau";
        $combo_tipo['ProvaConclusaoEnsinoMedio'] = "Prova de conclusão do Ensino Médio (histórico E.M)";
        $combo_tipo['TituloEleitor'] = "Título de eleitor";
        $combo_tipo['Outros'] = "Outros";
        
        $tipo_arquivo->addItems($combo_tipo);
        
        
        $arquivo->setAllowedExtensions( ['pdf'] );
                

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $caminho_arquivo ] ); 
        $this->form->addFields( [ $status_pdfa ] );
        $this->form->addFields( [ $status_assinatura ] );       
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('ID Documentação'), $dados_documentacao_id ],
                                       [ new TLabel('Nome'), $nome_aluno ] );
        $row->layout = ['col-sm-3', 'col-sm-9'];
        
        $row = $this->form->addFields( [ new TLabel('Curso'), $nome_curso ] );
        $row->layout = ['col-sm-12'];

        $this->form->addFields( [ '<br><hr>' ] );        
        
        $row = $this->form->addFields(  [ new TLabel('Anexar arquivo em PDF'), $arquivo ],
                                        [ new TLabel('Tipo de arquivo'), $tipo_arquivo ] );
        $row->layout = ['col-sm-5', 'col-sm-7'];
        
        $this->form->addFields( [ new TLabel('Observações'), $observacoes ] );
        

        $tipo_arquivo->addValidation('Tipo de arquivo', new TRequiredValidator);
        $arquivo->addValidation('Arquivo', new TRequiredValidator);
        $dados_documentacao_id->addValidation('ID Documentação', new TRequiredValidator);


        // set sizes
        $dados_documentacao_id->setValue($documentacao->id);
        $dados_documentacao_id->setEditable(FALSE);        
        $nome_aluno->setValue($diplomado);
        $nome_curso->setValue($curso);
        $nome_aluno->setEditable(FALSE);
        $nome_curso->setEditable(FALSE);
        
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('DiplomaDocumentacaoList','onReload')), 'fas:arrow-alt-circle-left blue');
        

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
                

        // creates the datagrid columns
        $column_tipo_arquivo = new TDataGridColumn('tipo_arquivo', 'Tipo', 'left', 250);
        $column_arquivo = new TDataGridColumn('arquivo', 'Arquivo', 'left');
        $column_caminho_arquivo = new TDataGridColumn('caminho_arquivo', 'Caminho', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Anexado por', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data de registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_tipo_arquivo);
        $this->datagrid->addColumn($column_arquivo);
        //$this->datagrid->addColumn($column_caminho_arquivo);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);

        
        $action1 = new TDataGridAction([$this, 'onDownload'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
                
        
        $this->datagrid->addAction($action1, 'Download', 'fas:cloud-download-alt blue fa-lg');
        $this->datagrid->addAction($action2, 'Excluir', 'far:trash-alt red fa-lg');
             
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack("<b>$diplomado :</b> $curso", $this->datagrid, $this->pageNavigation));
        
        
        parent::add($container);
    }
    
    
    public static function onDownload($param)
    {
        try
        {
            $id = $param['id'];
                
            TTransaction::open('Felabs_DB');

            $object = new DiplomaDigitalDocumentos($id);

            if($object->caminho_arquivo <> NULL AND $object->arquivo <> NULL)
            {
                $caminho_arquivo = $object->caminho_arquivo . '/' . $object->arquivo;
    
                if (file_exists($caminho_arquivo))
                {
                    TPage::openFile($caminho_arquivo);
                }
            }
            else
            {
                new TMessage('error', 'Não foi possível fazer o download');
            }
                
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onReload($param = NULL)
    {
        try
        {            
            TTransaction::open('Felabs_DB');

            $repository = new TRepository('DiplomaDigitalDocumentos');
            $limit = 10;

            $dados_documentacao = TSession::getValue('dados_documentacao');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('dados_documentacao_id', '=', $dados_documentacao->id));

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_tipo_arquivo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_tipo_arquivo'));
            }


            $objects = $repository->load($criteria, FALSE);
           
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    if($object->tipo_arquivo == "AtoNaturalizacao")
                    {
                        $object->tipo_arquivo = "Ato de naturalização";
                    }
                    elseif($object->tipo_arquivo == "CertidaoCasamento")
                    {
                        $object->tipo_arquivo = "Certidão de casamento";
                    }
                    elseif($object->tipo_arquivo == "CertidaoNascimento")
                    {
                        $object->tipo_arquivo = "Certidão de nascimento";
                    }
                    elseif($object->tipo_arquivo == "ComprovacaoEstagioCurricular")
                    {
                        $object->tipo_arquivo = "Comprovante de estágio curricular";
                    }
                    elseif($object->tipo_arquivo == "DocumentoIdentidadeDoAluno")
                    {
                        $object->tipo_arquivo = "Documento de identidade";
                    }
                    elseif($object->tipo_arquivo == "ProvaColacao")
                    {
                        $object->tipo_arquivo = "Prova de colação de grau";
                    }
                    elseif($object->tipo_arquivo == "ProvaConclusaoEnsinoMedio")
                    {
                        $object->tipo_arquivo = "Prova de conclusão do Ensino Médio";
                    }
                    elseif($object->tipo_arquivo == "TituloEleitor")
                    {
                        $object->tipo_arquivo = "Título de eleitor";
                    }
                    elseif($object->tipo_arquivo == "Outros")
                    {
                        $object->tipo_arquivo = "Outros";
                    }
                    else
                    {
                        $object->tipo_arquivo = $object->tipo_arquivo;
                    }
                    
                    $hr = substr($object->data_reg, 11, 19);
                    $dt = TDate::date2br($object->data_reg);
                    $object->data_reg = "$dt" . " " . substr($hr,0,-7);
                    
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public static function onDelete($param)
    {
        try
        {
            $id = $param['id'];
    
            TTransaction::open('Felabs_DB');
    
            $documento = new DiplomaDigitalDocumentos($id);
    
            //Não permite alteração caso o diploma relacionado tenha sido registrado
            $documentacao = new DiplomaDigitalDocumentacao($documento->dados_documentacao_id);
            
            //O código que interliga os dois é único, portanto só traz um registro
            $diploma = DiplomaDigitalDiploma::where('codigo_interliga_diploma_documentacao', '=', $documentacao->codigo_interliga_diploma_documentacao)->load();

            if($diploma[0]->arquivo_registrado OR $diploma[0]->status_publicacao == 1) //1 - Publicado
            {
                throw new Exception('Não é possível alterar nenhum dado pertencente a um diploma registrado');  
            }
            
            
            //Verifica se o XML da documentação já foi gerado
            if($documentacao->status_xml <> 0) //0 - Não gerado
            {
                //Verifica se o arquivo está presente no XML
                $target_file = $documentacao->caminho_arquivo. '/' . $documentacao->arquivo;
                
                $xml_documentacao = simplexml_load_file($target_file);
                
                foreach($xml_documentacao as $tags_principais) //Percorre a tag que está na raiz do XML
                {    
                    foreach($tags_principais->DocumentacaoComprobatoria as $tags_documentos) //Percorre a tag DocumentacaoComprobatoria
                    {
                        foreach($tags_documentos->Documento as $tag_documento)
                        {
                            (array) $tipo[] = $tag_documento->attributes()['tipo']; //Pega o tipo de documento especificado no atributo
                        }   
                    }    
                }
                
                //Se o documento a ser excluído estiver no XML, questiona usuário
                if(in_array($documento->tipo_arquivo, $tipo))
                {                
                    $action = new TAction([__CLASS__, 'Delete']);
                    $action->setParameters($param);
                    
                    new TQuestion('O documento está anexado ao XML da documentação. Deseja realmente excluir ?', $action);
                }
                else
                {
                    $action = new TAction([__CLASS__, 'Delete']);
                    $action->setParameters($param);
                    
                    new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
                }
            }
            else
            {
                $action = new TAction([__CLASS__, 'Delete']);
                $action->setParameters($param);
                
                new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }      
    }
    

    //Apaga o registro no banco e o arquivo PDF/A
    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];            
            
            TTransaction::open('Felabs_DB');
            
            $object = new DiplomaDigitalDocumentos($key);                         
                         
            //Apaga o arquivo
            if(file_exists($object->caminho_arquivo. '/' . $object->arquivo))
            {
                unlink($object->caminho_arquivo. '/' . $object->arquivo);
            }

            //Se diretório estiver vazio, apaga diretório (um diretório para cada aluno)
            $files = ((count(glob("$object->caminho_arquivo/*")) === 0) ? true : false);
            
            if($files == true)
            {
                rmdir($object->caminho_arquivo);
            }

            //Apaga o registro no banco
            $object->delete();
            
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);            
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
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
            
            //TTransaction::setLogger(new TLoggerSTD);
            
            $this->form->validate();
            $data = $this->form->getData();

            $object = new DiplomaDigitalDocumentos;
            $object->fromArray( (array) $data);
                                   
            
            //Não permite alteração caso o diploma relacionado tenha sido registrado
            $documentacao = new DiplomaDigitalDocumentacao($object->dados_documentacao_id);
            
            //O código que interliga os dois é único, portanto só traz um registro
/*            $diploma = DiplomaDigitalDiploma::where('codigo_interliga_diploma_documentacao', '=', $documentacao->codigo_interliga_diploma_documentacao)->load();

            if($diploma[0]->arquivo_registrado OR $diploma[0]->status_publicacao == 1) //1 - Publicado
            {
                throw new Exception('Não é possível adicionar mais nenhum arquivo, pois o diploma foi registrado');  
            }
*/            
            
            //Se está salvando um novo registro, verifica se o tipo de arquivo já existe para esta documentação (Só permite que "Outros" tenha mais de um)
            if(empty($data->id))
            {
                $registros_bd = DiplomaDigitalDocumentos::where('dados_documentacao_id', '=', $data->dados_documentacao_id)->load();
                
                foreach($registros_bd as $registro_bd)
                {
                    if ($registro_bd->tipo_arquivo == $data->tipo_arquivo AND $data->tipo_arquivo <> "Outros")
                    {
                        throw new Exception("Já existe um arquivo deste tipo adicionado à documentação. Caso queira substituí-lo, é necessário excluir o existente primeiro");
                    }   
                }
            } 


            $source_file  = 'tmp/' . $object->arquivo;
            
            if (file_exists($source_file))
            {
                $filepdf = fopen($source_file, 'r');
                $line_first = fgets($filepdf);               
                $valid = false;
                

                //Verifica se arquivo não está assinado, pois se estiver não é possível fazer conversão para PDF/A posteriormente
                while (($buffer = fgets($filepdf)) !== false) 
                {
                    if (strpos($buffer, 'adbe.pkcs7.detached') !== false)  
                    {
                        $valid = TRUE;
                        break; 
                    }      
                }
                
                fclose($filepdf);

                if($valid === true)
                {
                    unlink($source_file);
                    
                    throw new Exception("O arquivo a ser anexado não pode estar assinado com certificado digital");
                }
                else
                {
                    $target_path  = 'secretaria/documentacao_arquivos/documentacao_' . $object->dados_documentacao_id;
                    
                    //Se não existir diretório, cria
                    if (!file_exists($target_path))
                    {
                        if (!@mkdir($target_path, 0777, true))
                        {
                            throw new Exception(_t('Permission denied'). ': '. $target_path);
                        }
                    }
                    
                    //Se diretório foi criado, salva arquivo já renomeado
                    if (file_exists($target_path))
                    {
                        $datetime = date("YmdHis");
                        $extensao = pathinfo('tmp/' . $object->arquivo, PATHINFO_EXTENSION);
            
            
                        //Renomeia o arquivo na própria pasta tmp para não ter problemas com caracteres especiais na hora de usar o ghostscript    
                        $nome_tmp = 'tmp/' . $object->tipo_arquivo . '_documentacao_' . $documentacao->id . '_' . $datetime . '.' . $extensao; 
                        rename($source_file, $nome_tmp);
                    
                            
                        //Ghostscript usa o caminho absoluto
                        $caminho_absoluto_tmp = realpath($nome_tmp);                                                
                        $caminho_absoluto_target = realpath($target_path);   
                        $caminho_absoluto_pdf = $caminho_absoluto_target . '/' . $object->tipo_arquivo . '_documentacao_' . $documentacao->id . '_' . $datetime . '.' . $extensao;

                        
                        //Sobe arquivo independentemente de versão (sem ghostscript, apresenta erro em versões maiores que 1.4)
                        shell_exec('gswin32c -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=' . $caminho_absoluto_pdf . ' ' . $caminho_absoluto_tmp);                         
                    
                        
                        //1º Converte para PDF/A-1b
                        $caminho_pdfa1 = realpath($caminho_absoluto_pdf);
                        $caminho_pdfa1 = substr($caminho_pdfa1, 0, -4) . '_A1b.pdf';
            
                        shell_exec('gswin32c -dPDFA -dOverrideICC=true -dEmbedAllFonts=true -dBATCH -dNOPAUSE -dPDFSETTINGS=/printer -sProcessColorModel=DeviceRGB -sColorConversionStrategy=UseDeviceIndependentColor -sDEVICE=pdfwrite -dPDFACompatibilityPolicy=1 -sOutputFile=' . $caminho_pdfa1 . ' ' . $caminho_absoluto_pdf);
                    
                                
                        //2º Converte para PDFA-2b (a conversão direta causa erro na validação de conformidade)
                        $caminho_pdfa2 = realpath($caminho_absoluto_pdf);
                        $caminho_pdfa2 = substr($caminho_pdfa2, 0, -4) . '_A2b.pdf';
                        
                        shell_exec('gswin32c -dPDFA=2 -dBATCH -dNOPAUSE -dPDFSETTINGS=/printer -sProcessColorModel=DeviceRGB -sColorConversionStrategy=UseDeviceIndependentColor -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFACompatibilityPolicy=1 -sOutputFile='. $caminho_pdfa2 . ' ' . $caminho_pdfa1);        
                    }
                }        
                
                //Se arquivo foi convertido                                              
                if(file_exists($caminho_pdfa2))
                {
                    //Apaga arquivo da pasta tmp, o 'original' e o A1-b na pasta de destino gerados para permitirem a conversão
                    if($nome_tmp)
                    {
                        unlink($nome_tmp);    
                    }
                    
                    if($caminho_absoluto_pdf)
                    {
                        unlink($caminho_absoluto_pdf);
                    }
                           
                    if($caminho_pdfa1)
                    {
                        unlink($caminho_pdfa1);
                    }
                            
                            
                    $object->arquivo = $object->tipo_arquivo . '_documentacao_' . $documentacao->id . '_' . $datetime . '_A2b.pdf';
                    $object->caminho_arquivo = $target_path;
                    $object->status_pdfa = 1; //0 - Não convertido / 1 - Convertido
                    $object->status_assinatura = 0; //0 - Não assinado / 1 - Assinado
                    $object->system_user_id = TSession::getValue('userid');
                    $object->data_reg = date('Y-m-d H:i:s');                                                
                    $object->store();
                
                
                    $data->id = $object->id;
                    
                    $this->form->setData($data);
                    TTransaction::close();
                            
                            
                    //Verifica se o XML da documentação já foi gerado
                    if($documentacao->status_xml <> 0) //0 - Não gerado
                    {
                        new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
                        new TMessage('info', 'O XML desta documentação já foi gerado. Se desejar que este documento integre-o, um novo XML precisa ser gerado');
                    }
                    else
                    {
                        new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
                    }
                            
                    TApplication::loadPage('DiplomaDocumentosFormList', 'onReload');
                } 
                else
                {
                   throw new Exception("Erro ao converter arquivo");         
                }    
            }
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
        

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        
        parent::show();
    }
}
