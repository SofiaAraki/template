<?php

//use Mpdf\Mpdf;

class DiplomaTermoResponsabilidadeFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        //Para preenchimento do cabeçalho da datagrid
        $dados_documentacao = TSession::getValue('dados_documentacao');
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $documentacao = new DiplomaDigitalDocumentacao($dados_documentacao->id);
            $diplomado = $documentacao->diploma_digital_diplomado->nome;
            $curso = $documentacao->diploma_digital_curso->nome_curso_diploma;
        
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } 
        

        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalTermoResponsabilidade');
        $this->form->setFormTitle('<h4>Termo de Responsabilidade</h4>');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $nome_assinante = new TEntry('nome_assinante');
        $cpf_assinante = new TEntry('cpf_assinante');
        $cargo_assinante = new TEntry('cargo_assinante');
        $ato_designacao = new TFile('ato_designacao');
        $caminho_ato = new THidden('caminho_ato');
        $dados_documentacao_id = new THidden('dados_documentacao_id');
        $status_pdfa = new THidden('status_pdfa');
        $status_assinatura = new THidden('status_assinatura');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        $ato_designacao->setAllowedExtensions( ['pdf'] );
        $ato_designacao->setTip("A inclusão do ato de designação é opcional. Refere-se à procuração feita por superior outorgando poderes à pessoa que assinou o Termo de Responsabilidade. 
                                 <br>O arquivo deve ser anexado à plataforma no formato PDF");


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $caminho_ato ] );
        $this->form->addFields( [ $dados_documentacao_id ] );
        $this->form->addFields( [ $status_pdfa ] );
        $this->form->addFields( [ $status_assinatura ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
                                
        $row = $this->form->addFields( [ new TLabel('Nome completo do assinante <font color="red">*</font>'), $nome_assinante ],
                                       [ new TLabel('CPF<font color="red">*</font>'), $cpf_assinante ],
                                       [ new TLabel('Cargo<font color="red">*</font>'), $cargo_assinante ] );
        $row->layout = ['col-sm-6', 'col-sm-3', 'col-sm-3'];    
        
        $row = $this->form->addFields( [ new TLabel('Inserir ato de designação'), $ato_designacao ] );
        $row->layout = ['col-sm-12'];
                
                
        $nome_assinante->addValidation('Nome assinante', new TRequiredValidator);
        $cpf_assinante->addValidation('CPF assinante', new TCPFValidator);
        $cargo_assinante->addValidation('Cargo', new TRequiredValidator);
        $dados_documentacao_id->addValidation('ID Documentação', new TRequiredValidator);
              

        // set sizes
        $nome_assinante->forceUpperCase();
        $cpf_assinante->setMask('99999999999');
        $cargo_assinante->forceUpperCase();
        $dados_documentacao_id->setValue($documentacao->id);
        $dados_documentacao_id->setEditable(FALSE);
                

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
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_nome_assinante = new TDataGridColumn('nome_assinante', 'Assinante', 'left');
        $column_cpf_assinante = new TDataGridColumn('cpf_assinante', 'CPF', 'center', 180);
        $column_cargo_assinante = new TDataGridColumn('cargo_assinante', 'Cargo', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');
        $column_caminho_ato = new TDataGridColumn('caminho_ato', 'Caminho do ato de designação', 'left');
               

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome_assinante);
        $this->datagrid->addColumn($column_cpf_assinante);
        $this->datagrid->addColumn($column_cargo_assinante);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);
        //$this->datagrid->addColumn($column_caminho_ato);
        
        
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

            $object = new DiplomaDigitalTermoResponsabilidade($id);

            if (strtolower(substr($object->ato_designacao, -4)) == 'html')
            {
                $win = TWindow::create( 'Arquivo', 0.8, 0.8 );
                $win->add( file_get_contents( $object->caminho_ato. '/' . $object->ato_designacao ) );
                $win->show();
            }
            else
            {
                if(($object->caminho_ato == NULL) OR ($object->ato_designacao == NULL))
                {
                    new TMessage('info', 'Não foi anexado nenhum ato de designação');
                }
                else
                {
                    TPage::openFile($object->caminho_ato. '/' . $object->ato_designacao);
                }
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
            
            $repository = new TRepository('DiplomaDigitalTermoResponsabilidade');
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
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $object->cpf_assinante = preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/', '$1.$2.$3-$4', $object->cpf_assinante);

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
    
            $termo = new DiplomaDigitalTermoResponsabilidade($id);
    
            //Não permite alteração caso o diploma relacionado tenha sido registrado
            $documentacao = new DiplomaDigitalDocumentacao($termo->dados_documentacao_id);
            
            //O código que interliga os dois é único, portanto só traz um registro
            $diploma = DiplomaDigitalDiploma::where('codigo_interliga_diploma_documentacao', '=', $documentacao->codigo_interliga_diploma_documentacao)->load();

            if($diploma[0]->arquivo_registrado OR $diploma[0]->status_publicacao == 1) //1 - Publicado
            {
                throw new Exception('Não é possível alterar nenhum dado pertencente a um diploma registrado');  
            }
            
            
            //Verifica se o XML da documentação já foi gerado
            if($documentacao->status_xml <> 0) //0 - Não gerado
            {
                //Verifica se o ato de designação está presente no XML
                $target_file = $documentacao->caminho_arquivo. '/' . $documentacao->arquivo;
                
                $xml_documentacao = simplexml_load_file($target_file);
                
                foreach($xml_documentacao as $tags_principais) //Percorre a tag que está na raiz do XML
                {    
                    foreach($tags_principais->TermoResponsabilidadeEmissora as $tags_termo) //Percorre a tag TermoResponsabilidadeEmissora
                    {
                        if($tags_termo->AtoDesignacao)
                        {
                            $ato = "Ato incluso";
                        }
                        else
                        {
                            $ato = "Ato não incluso";
                        }
                    }    
                }

                //Se o ato de designação a ser excluído estiver no XML, questiona usuário
                if($ato == "Ato incluso")
                {                
                    $action = new TAction([__CLASS__, 'Delete']);
                    $action->setParameters($param);
                    
                    new TQuestion('Os dados do termo de responsabilidade bem como o ato de designação estão inclusos no XML da documentação. Deseja realmente excluir ?', $action);
                }
                else
                {
                    $action = new TAction([__CLASS__, 'Delete']);
                    $action->setParameters($param);
                    
                    new TQuestion('Os dados do termo de responsabilidade estão inclusos no XML da documentação. Deseja realmente excluir ?', $action);
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
            
            $object = new DiplomaDigitalTermoResponsabilidade($key);
            
            //Apaga o arquivo (um diretório para todos os alunos, pois é só um ato por documentação)
            if(file_exists($object->caminho_ato. '/' . $object->ato_designacao))
            {
                unlink($object->caminho_ato. '/' . $object->ato_designacao);
            }

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
                        
            $this->form->validate();
            $data = $this->form->getData();
            
            $object = new DiplomaDigitalTermoResponsabilidade;
            $object->fromArray( (array) $data);

            
            //Não permite alteração caso o diploma relacionado tenha sido registrado
            $documentacao = new DiplomaDigitalDocumentacao($object->dados_documentacao_id);
            
            //O código que interliga os dois é único, portanto só traz um registro
            $diploma = DiplomaDigitalDiploma::where('codigo_interliga_diploma_documentacao', '=', $documentacao->codigo_interliga_diploma_documentacao)->load();

            if($diploma[0]->arquivo_registrado OR $diploma[0]->status_publicacao == 1) //1 - Publicado
            {
                throw new Exception('Não é possível adicionar mais nenhum arquivo, pois o diploma foi registrado');  
            }
            

            //Se está salvando um "novo registro", mas já existe um termo de responsabilidade referente à esta documentação
            if(empty($data->id))
            {
                $registros_bd = DiplomaDigitalTermoResponsabilidade::where('dados_documentacao_id', '=', $data->dados_documentacao_id)->load();
                
                if ($registros_bd)
                {
                    throw new Exception("Já existe um termo de responsabilidade para esta documentação. Caso queira substituí-lo, é necessário excluir o existente primeiro");
                }
            }              
            
            
            if($object->ato_designacao)
            {
                $source_file  = 'tmp/' . $object->ato_designacao;
                $datetime = date("YmdHis");
                $extensao = pathinfo('tmp/' . $object->ato_designacao, PATHINFO_EXTENSION);
            
            
                $filepdf = fopen($source_file, 'r');
                $line_first = fgets($filepdf);               
                $valid = false;
                

                //1º - Verifica se arquivo não está assinado, pois se estiver não é possível fazer conversão para PDF/A posteriormente
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
                    //Renomeia o arquivo na própria pasta tmp para não ter problemas com caracteres especiais na hora de usar o ghostscript
                    $nome_tmp = 'tmp/' . 'AtoDesignacao_documentacao_' . $object->dados_documentacao_id . '_' . $datetime . '.' . $extensao;

                    rename($source_file, $nome_tmp);
                    
                    
                    $target_path  = 'secretaria/documentacao_atos';
                    
                    //Se não existir diretório, cria
                    if (!file_exists($target_path))
                    {
                        if (!@mkdir($target_path, 0777, true))
                        {
                            throw new Exception(_t('Permission denied'). ': '. $target_path);
                        }
                    }
                
                    //Se diretório foi criado, faz a conversão
                    if (file_exists($target_path))
                    {                                
                        //Ghostscript usa o caminho absoluto
                        $caminho_absoluto_tmp = realpath($nome_tmp);                                                
                        $caminho_absoluto_target = realpath($target_path);   
                        $caminho_absoluto_pdf = $caminho_absoluto_target . '/' . 'AtoDesignacao_documentacao_' . $object->dados_documentacao_id . '_' . $datetime . '.' . $extensao;

                        
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
                            
                            
                            $object->ato_designacao = 'AtoDesignacao_documentacao_' . $object->dados_documentacao_id . '_' . $datetime . '_A2b.pdf';
                            $object->caminho_ato = $target_path;   
                            $object->status_pdfa = 1; //0 - Não convertido / 1 - Convertido
                            $object->status_assinatura = 0; //0 - Não assinado / 1 - Assinado                         
                        } 
                        else
                        {
                            throw new Exception("Erro ao converter arquivo");         
                        }    
                    }
                    else
                    {
                        throw new Exception("Erro ao criar diretório para armazenar o arquivo");  
                    }
                }
            }

            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');                                                
            $object->store();
                
               
            $data->id = $object->id;
            $this->form->setData($data);
                            
            TTransaction::close();
                            
                            
            //Verifica se o XML da documentação já foi gerado
            if($documentacao->status_xml <> 0) //0 - Não gerado
            {                
                new TMessage('info', 'O XML desta documentação já foi gerado. Se desejar que este novo termo integre-o, um novo XML precisa ser gerado');
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            }
            else
            {
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            }     
                            
            $this->form->clear();
                          
            //Limpa o formulário depois de salvar, mas mantém o id da documentação preenchido
            $obj = new StdClass;
            $obj->dados_documentacao_id = $object->dados_documentacao_id;
                           
            TForm::sendData('form_DiplomaDigitalTermoResponsabilidade', $obj);
              
            $this->onReload();                   
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
