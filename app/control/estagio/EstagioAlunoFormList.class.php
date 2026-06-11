<?php

class EstagioAlunoFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        try
        {
            TTransaction::open('Felabs_DB');
                    
            $loggedUnit = TSession::getValue('userunitid');
            $loggedId = TSession::getValue('userid');        
            $user = new SystemUser($loggedId);   
            
            //Critério para carregar combo professor responsável pela aprovação na FFCL, FAJOB e NEAD
            if($loggedUnit == 2 OR $loggedUnit == 6 OR $loggedUnit == 10)
            {
                $array_ffcl_fajob = [];
                $array_ffcl_fajob['29'] = '29'; //Administração, Gestão de RH e Contábeis - Lidiane Kanesiro
                $array_ffcl_fajob['2401'] = '2401'; //Pedagogia - Fátima Gonini 
                $array_ffcl_fajob['5622'] = '5622'; //Engenharia Civil, Mecânica, Produção e Elétrica - Amanda Paula Caretta
                $array_ffcl_fajob['27'] = '27'; //LUCIANA 2ªVIA
                $array_ffcl_fajob['1852'] = '1852'; //Lisangela - Letras
                $array_ffcl_fajob['59'] = '59'; //Wesley - Estudos Sociais
                
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('id', 'IN', $array_ffcl_fajob));
                
                //new TMessage('info', 'Lançamento dos estágios realizado pela secretaria acadêmica');
                //die;
            }
                  
            //Critério para carregar combo professor responsável pela aprovação na FAFRAM (quem vai lançar os estágios é a secretaria) 
            if($loggedUnit == 3)
            {
                $array_fafram = [];
                $array_fafram['19'] = '19'; //Agronomia - Livia Cordaro
                $array_fafram['6015'] = '6015'; //Direito - Bruno
                $array_fafram['2890'] = '2890'; //Sistemas - Murilo Scapim
                $array_fafram['1937'] = '1937'; //Enfermagem - Andreza Maeda
                $array_fafram['18'] = "18"; //Veterinária - ELZYLENE LÉGA
                
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('id', 'IN', $array_fafram));
                
                //new TMessage('info', 'Lançamento dos estágios realizado pela secretaria acadêmica');
                //die;
            }
            
    
            if($loggedUnit == 1 OR $loggedUnit == 8 OR $loggedUnit == 12)
            {
                new TMessage('info', 'Funcionalidade não disponível para esta unidade');
                die;
            }
     
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        
        
        $this->form = new BootstrapFormBuilder('form_Estagio');
        $this->form->setFormTitle('<h4>Meus estágios</h4>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $tipo_entrada = new THidden('tipo_entrada');
        $cod_aluno = new THidden('cod_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $cod_curso = new TCombo('cod_curso');
        $nome_curso = new THidden('nome_curso');
        $ano = new TEntry('ano');
        $semestre = new TCombo('semestre');
        $etapa = new TCombo('etapa');
        $data_inicio = new TDate('data_inicio');
        $data_termino = new TDate('data_termino');
        $carga_horaria = new TEntry('carga_horaria');
        $razao_social_empresa = new TEntry('razao_social_empresa');
        $cnpj_empresa = new TEntry('cnpj_empresa');
        $descricao = new TEntry('descricao');
        $cod_prof_responsavel = new TDBCombo('cod_prof_responsavel', 'Felabs_DB', 'SystemUser', 'systemuser_codlegado', 'name', 'name', $criteria1);
        $titulacao_prof_responsavel = new THidden('titulacao_prof_responsavel');
        $arquivo = new TFile('arquivo');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $status_estagio = new THidden('status_estagio');
        $observacao = new THidden('observacao');
        $status_pdfa = new THidden('status_pdfa');    
        $status_assinatura = new THidden('status_assinatura');           
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        $opcao_estagio = new TRadioGroup('opcao_estagio');
        $nome_pessoa_fisica = new TEntry('nome_pessoa_fisica');
        $cpf_pessoa_fisica = new TEntry('cpf_pessoa_fisica');
        $cod_estagio_historico = new THidden('cod_estagio_historico');
        

        //Critério para carregar combo curso
        try
        {
            TTransaction::open('dados_fei');   
            
            $aluno = new FiAluno($user->systemuser_codlegado); //Preenche nome do aluno  
    
            $criteria2 = new TCriteria;
            $criteria2->add( new TFilter('Codaluno', '=', $aluno->Codaluno));
            $criteria2->add( new TFilter('CodEntidade', '=', $loggedUnit));         
            
            $dados_aluno = VwAlunoMatriculaEtapa::getObjects($criteria2);
            
            //Preenche combo
            if($dados_aluno)
            {
                $cursos = [];
    
                foreach($dados_aluno as $dado_aluno)
                {
                    $cursos[$dado_aluno->CodCurso] = $dado_aluno->NomeCurso;
                }
    
                $codCursos = array_unique($cursos); //Agrupa códigos iguais
    
                $cod_curso->addItems($codCursos);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
        
        
        //Semestre
        $combo_semestre = [];
        $combo_semestre[1] = '1º semestre';
        $combo_semestre[2] = '2º semestre';
        
        $semestre->addItems($combo_semestre);
 
        //Preenche a etapa em que o aluno estava matriculado de acordo com o ano e semestre do estágio
        $semestre->setChangeAction(new TAction(array($this, 'onEtapaChange')));
         
 
        //Etapa
        $combo_etapa = [];
        $combo_etapa[1] = '1º ciclo';
        $combo_etapa[2] = '2º ciclo';
        $combo_etapa[3] = '3º ciclo';
        $combo_etapa[4] = '4º ciclo';
        $combo_etapa[5] = '5º ciclo';
        $combo_etapa[6] = '6º ciclo';
        $combo_etapa[7] = '7º ciclo';
        $combo_etapa[8] = '8º ciclo';
        $combo_etapa[9] = '9º ciclo';
        $combo_etapa[10] = '10º ciclo';

        $etapa->addItems($combo_etapa);


        //Opção estágio
        $radio_opcao_estagio = [];
        $radio_opcao_estagio['Pessoa física'] = 'Pessoa física';
        $radio_opcao_estagio['Pessoa jurídica'] = 'Pessoa jurídica';
        
        $opcao_estagio->addItems($radio_opcao_estagio);
        
        $opcao_estagio->setChangeAction(new TAction(array($this, 'onOpcaoEstagioChange')));
        
        
        $arquivo->setAllowedExtensions(['pdf']);
        

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $tipo_entrada ] );
        $this->form->addFields( [ $cod_aluno ] );
        $this->form->addFields( [ $nome_curso ] );
        $this->form->addFields( [ $titulacao_prof_responsavel ] );
        $this->form->addFields( [ $caminho_arquivo ] );
        $this->form->addFields( [ $status_estagio ] );
        $this->form->addFields( [ $observacao ] );
        $this->form->addFields( [ $status_pdfa ] );
        $this->form->addFields( [ $status_assinatura ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        $this->form->addFields( [ $cod_estagio_historico ] );
                
        $row = $this->form->addFields( [ new TLabel('Aluno <font color="red">*</font>'), $nome_aluno ],
                                       [ new TLabel('Curso <font color="red">*</font>'), $cod_curso ] );
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Ano (em que realizou o estágio) <font color="red">*</font>'), $ano ],
                                       [ new TLabel('Semestre (em que realizou o estágio) <font color="red">*</font>'), $semestre ],
                                       [ new TLabel('Etapa (em que realizou o estágio) <font color="red">*</font>'), $etapa ],
                                       [ new TLabel('Responsável pela aprovação <font color="red">*</font>'), $cod_prof_responsavel ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
                
        $row = $this->form->addFields( [ $opcao_estagio ],
                                       [ new TLabel('Razão social da empresa'), $razao_social_empresa ],
                                       [ new TLabel('CNPJ da empresa'), $cnpj_empresa ],
                                       [ new TLabel('Nome do concedente'), $nome_pessoa_fisica ],
                                       [ new TLabel('CPF do concedente'), $cpf_pessoa_fisica ] );
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-3', 'col-sm-2'];
                
        $row = $this->form->addFields( [ new TLabel('Data de início <font color="red">*</font>'), $data_inicio ],
                                       [ new TLabel('Data de término <font color="red">*</font>'), $data_termino ],
                                       [ new TLabel('Horas <font color="red">*</font>'), $carga_horaria ],
                                       [ new TLabel('Anexar comprovante em PDF <font color="red">*</font>'), $arquivo ] );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-6'];      
                
        $row = $this->form->addFields( [ new TLabel('Breve descrição do estágio <font color="red">*</font>'), $descricao ] );
        $row->layout = ['col-sm-12'];

        $this->form->addFields( [ '<br>' ] ); 
        $label1 = new TLabel('<font color="red">*</font> Campos obrigatórios', '', 10, 'i');
        $this->form->addContent( [$label1] );
        

        $cod_aluno->addValidation('Cód. aluno', new TRequiredValidator);
        $nome_aluno->addValidation('Aluno', new TRequiredValidator);
        $cod_curso->addValidation('Curso', new TRequiredValidator);
        $ano->addValidation('Ano (em que realizou o estágio)', new TRequiredValidator);
        $semestre->addValidation('Semestre (em que realizou o estágio)', new TRequiredValidator);
        $etapa->addValidation('Etapa (em que realizou o estágio)', new TRequiredValidator);
        $cod_prof_responsavel->addValidation('Responsável pela aprovação', new TRequiredValidator);        
        $opcao_estagio->addValidation('Pessoa física/Pessoa jurídica', new TRequiredValidator);     
        $data_inicio->addValidation('Data de início', new TRequiredValidator);
        $data_termino->addValidation('Data de término', new TRequiredValidator);
        $carga_horaria->addValidation('Horas', new TRequiredValidator);
        $arquivo->addValidation('Anexar comprovante em PDF', new TRequiredValidator);  
        $descricao->addValidation('Breve descrição do estágio', new TRequiredValidator);
                       

        // set sizes
        $cod_aluno->setEditable(FALSE);
        $cod_aluno->setValue($aluno->Codaluno);
        $nome_aluno->setEditable(FALSE);
        $nome_aluno->setValue($aluno->Nome);
        $ano->setMask('9999');
        $ano->placeholder = "Ex: 2023";
        $data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_termino->setMask('dd/mm/yyyy');
        $data_termino->setDatabaseMask('yyyy-mm-dd');
        $carga_horaria->setMask('9!');
        $opcao_estagio->setLayout('horizontal');
        $cnpj_empresa->setMask('99.999.999/9999-99');
        $cpf_pessoa_fisica->setMask('999.999.999-99');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'center');
        $column_concendente = new TDataGridColumn('concedente', 'Concedente', 'left');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Data de início', 'center');
        $column_data_termino = new TDataGridColumn('data_termino', 'Data de término', 'center');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'Carga Horária', 'center');        
        $column_cod_prof_responsavel = new TDataGridColumn('responsavel->name', 'Responsável pela aprovação', 'center');
        $column_status_estagio = new TDataGridColumn('status_estagio', 'Status', 'center');


        $column_cod_prof_responsavel->setTransformer( array($this, 'setNomeResponsavel') );
        $column_status_estagio->setTransformer( array($this, 'setStatusColor') );


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_concendente);
        $this->datagrid->addColumn($column_data_inicio);
        $this->datagrid->addColumn($column_data_termino);
        $this->datagrid->addColumn($column_carga_horaria);        
        $this->datagrid->addColumn($column_cod_prof_responsavel);
        $this->datagrid->addColumn($column_status_estagio);

                
        $action1 = new TDataGridAction([$this, 'onDelete']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Delete'));
        $action1->setImage('far:trash-alt red fa-lg');
        $action1->setField('id');
        $action1->setDisplayCondition( array($this, 'displayColumn') );
        

        $action2 = new TDataGridAction([$this, 'onDownload']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel('Download');
        $action2->setImage('fas:cloud-download-alt blue fa-lg');
        $action2->setField('id');
        
        
        //Só exibe as informações do estágio, sem ação nenhuma, então não tem problema exibir formview do professor
        $action3 = new TDataGridAction(['EstagioAnalisadoProfessorFormView', 'onEdit'], ['id'=>'{id}']);
        //$action3->setUseButton(TRUE);
        //$action3->setButtonClass('btn btn-default');
        $action3->setLabel('Ver detalhes');
        $action3->setImage('fa:search green');
        $action3->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action3);
        
        
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
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    
    public static function onEtapaChange($param)
    {
        try
        {
            TTransaction::open('dados_fei');
           
            if((!empty($param['cod_aluno'])) AND (!empty($param['cod_curso'])) AND (!empty($param['ano'])) AND (!empty($param['semestre'])))
            {
                //Pega a etapa em que o aluno estava matriculado quando realizou o estágio
                $criteria = new TCriteria;
                $criteria->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
                $criteria->add(new TFilter('CodCurso', '=', $param['cod_curso']));
                $criteria->add(new TFilter('AnoMatricula', '=', $param['ano']));
                $criteria->add(new TFilter('SemestreMatricula', '=', $param['semestre']));
            
                $matricula = VwAlunoMatriculaEtapa::getObjects($criteria);
                
                if($matricula)
                {
                    $obj = new StdClass;
                    $obj->etapa = $matricula[0]->EtapaMatricula;

                    TForm::sendData('form_Estagio', $obj);
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
    
    
    public static function onOpcaoEstagioChange($param)
    {
        $opcao_estagio = $param['opcao_estagio'];

        if($opcao_estagio == 'Pessoa física')
        {
            //LIMPA
            TEntry::clearField('form_Estagio', 'razao_social_empresa');
            TEntry::clearField('form_Estagio', 'cnpj_empresa');
            
            //DESABILITA
            TEntry::disableField('form_Estagio', 'razao_social_empresa');
            TEntry::disableField('form_Estagio', 'cnpj_empresa');  
            
            //HABILITA
            TEntry::enableField('form_Estagio', 'nome_pessoa_fisica');
            TEntry::enableField('form_Estagio', 'cpf_pessoa_fisica'); 
        }
        elseif($opcao_estagio == 'Pessoa jurídica')
        {
            //LIMPA
            TEntry::clearField('form_Estagio', 'nome_pessoa_fisica');
            TEntry::clearField('form_Estagio', 'cpf_pessoa_fisica'); 
            
            //DESABILITA
            TEntry::disableField('form_Estagio', 'nome_pessoa_fisica');
            TEntry::disableField('form_Estagio', 'cpf_pessoa_fisica'); 
            
            //HABILITA
            TEntry::enableField('form_Estagio', 'razao_social_empresa');
            TEntry::enableField('form_Estagio', 'cnpj_empresa');   
        }
        else
        {
            //LIMPA
            TEntry::clearField('form_Estagio', 'razao_social_empresa');
            TEntry::clearField('form_Estagio', 'cnpj_empresa'); 
            TEntry::clearField('form_Estagio', 'nome_pessoa_fisica');
            TEntry::clearField('form_Estagio', 'cpf_pessoa_fisica'); 
            
            //DESABILITA
            TEntry::disableField('form_Estagio', 'razao_social_empresa');
            TEntry::disableField('form_Estagio', 'cnpj_empresa'); 
            TEntry::disableField('form_Estagio', 'nome_pessoa_fisica');
            TEntry::disableField('form_Estagio', 'cpf_pessoa_fisica'); 
        }
    }
    
    
    public function setNomeResponsavel($column_cod_prof_responsavel, $object, $row)
    {
        try
        {
            $cod_professor = $object->cod_prof_responsavel;            
            
            TTransaction::open('dados_fei');           
            
            $responsavel = new FiProfessor($cod_professor);

            TTransaction::close();


            return $responsavel->Nome;            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function setStatusColor($column_status_estagio, $object, $row)
    {
        $color = $object->status_estagio;
        
        if($color == "Aguardando aprovação")
        {
            return '<span class="label label-warning">' . $column_status_estagio . '</span>';
        }
        elseif($color == "Aprovado")
        {
            return '<span class="label label-success">' . $column_status_estagio . '</span>';
        }
        elseif($color == "Reprovado")
        {
            return '<span class="label label-danger">' . $column_status_estagio . '</span>';
        }
        else
        {
            return $column_status_estagio;
        }    
    }


    public static function onDownload($param)
    {
        try
        {
            $id = $param['id'];
                
            TTransaction::open('Felabs_DB');

            $object = new Estagio($id);

            if (strtolower(substr($object->arquivo, -4)) == 'html')
            {
                $win = TWindow::create( 'Arquivo', 0.8, 0.8 );
                $win->add( file_get_contents( $object->caminho_arquivo . '/' . $object->arquivo ) );
                $win->show();
            }
            else
            {
                TPage::openFile($object->caminho_arquivo . '/' . $object->arquivo);
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
            
            $unit_id = TSession::getValue('userunitid');            
            $user_id = TSession::getValue('userid');                    
            $user = new SystemUser($user_id);
                
            TTransaction::close();
            
            
            //Filtra os cursos da unidade logada
            TTransaction::open('dados_fei');
            
            $repository_curso = new TRepository('FiCurso');
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            
            $cursos = $repository_curso->load($criteria_curso);

            foreach($cursos as $curso)
            {
                $items[$curso->CodCurso] = $curso->CodCurso;
            }
            
            $aluno = new FiAluno($user->systemuser_codlegado);
            
            TTransaction::close();


            //Exibe só os estágios do aluno logado na unidade correspondente
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('Estagio');
            $limit = 20;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $aluno->Codaluno));
            $criteria->add(new TFilter('cod_curso', 'IN', $items));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            $this->datagrid->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    if($object->razao_social_empresa <> NULL)
                    {
                        $object->concedente = $object->razao_social_empresa;
                    }
                    elseif($object->nome_pessoa_fisica <> NULL)
                    {
                        $object->concedente = $object->nome_pessoa_fisica;
                    }
                    else
                    {
                        $object->concedente = '';
                    }
                    
                    $object->data_inicio = TDate::date2br($object->data_inicio);
                    $object->data_termino = TDate::date2br($object->data_termino);
                    $object->carga_horaria = substr($object->carga_horaria,0,-3);
                    
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
            
            $param['opcao_estagio'] = '';
            $this->onOpcaoEstagioChange($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function displayColumn($object)
    {
        if($object->status_estagio == "Aguardando aprovação")
        {
            return TRUE;
        }
            return FALSE;
    }
    

    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new Estagio($key, FALSE);
            
            //Apaga o arquivo
            if(file_exists($object->caminho_arquivo . '/' . $object->arquivo))
            {
                unlink($object->caminho_arquivo . '/' . $object->arquivo);
            }
            
            //Se diretório estiver vazio, apaga diretório (um diretório para cada aluno)
            $files = ((count(glob("$object->caminho_arquivo/*")) === 0) ? true : false);
            
            if($files == true)
            {
                rmdir($object->caminho_arquivo);
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
            
            $data = $this->form->getData(); 
            
            $object = new Estagio; 
            $object->fromArray( (array) $data); 

            $this->form->validate();
            
            
            TTransaction::open('dados_fei');
            
            $cod_curso = $param['cod_curso'];
            $fi_curso = new FiCurso($cod_curso); 
            
            //Confirma se aluno tem matrícula no curso selecionado
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
            $criteria_curso->add(new TFilter('CodCurso', '=', $param['cod_curso']));
            
            $matricula_curso = VwAlunoMatriculaEtapa::getObjects($criteria_curso);
            
            if(empty($matricula_curso))
            {
                throw new Exception("Não consta matrícula do(a) aluno(a) no curso selecionado");
            }
            
            
            //Confirma se a etapa em que o aluno estava matriculado quando realizou o estágio está correta
            $criteria_matricula = new TCriteria;
            $criteria_matricula->add(new TFilter('Codaluno', '=', $param['cod_aluno']));
            $criteria_matricula->add(new TFilter('CodCurso', '=', $param['cod_curso']));
            $criteria_matricula->add(new TFilter('AnoMatricula', '=', $param['ano']));
            $criteria_matricula->add(new TFilter('SemestreMatricula', '=', $param['semestre']));
            
            $matricula = VwAlunoMatriculaEtapa::getObjects($criteria_matricula);
            
            //Se tiver matrícula e no formulário estiver diferente do que consta no BD, barra. Caso contrário, deixa salvar (ex: casos de transferência)
            if((!empty($matricula)) AND ($matricula[0]->EtapaMatricula <> $param['etapa']))
            {
                throw new Exception("Verifique a etapa em que o aluno estava matriculado quando realizou o estágio");
            }
            
            TTransaction::close();
            

            //Verifica se o ano contém 4 dígitos
            $count = strlen($object->ano);
            
            if($count <> 4)
            {
                throw new Exception("O campo Ano (em que realizou o estágio) precisa ter 4 dígitos");
            }
            
            
            //Controle campos condicionais - Concedente do estágio
            if($object->opcao_estagio == 'Pessoa física')
            {
                $object->razao_social_empresa = '';
                $object->cnpj_empresa = '';  
                
                if(! $object->nome_pessoa_fisica)
                {
                    throw new Exception('É necessário informar a pessoa física concedente do estágio. Caso conste o CPF do concedente, este também deve ser informado');
                }
                
                //Se o CPF foi preenchido
                if($object->cpf_pessoa_fisica)
                {
                    $valida_cpf = new TCPFValidator;
                    $valida_cpf->validate('CPF do concedente', $object->cpf_pessoa_fisica);
                }
            }
            elseif($object->opcao_estagio == 'Pessoa jurídica')
            {
                $object->nome_pessoa_fisica = '';
                $object->cpf_pessoa_fisica = '';
                
                if(! $object->razao_social_empresa)
                {
                    throw new Exception('É necessário informar a pessoa jurídica concedente do estágio. Caso conste o CNPJ do concedente, este também deve ser informado');
                }
                
                //Se o CNPJ foi preenchido
                if($object->cnpj_empresa)
                {
                    $valida_cnpj = new TCNPJValidator;
                    $valida_cnpj->validate('CNPJ da empresa', $object->cnpj_empresa);
                }
            }
            else
            {
                $object->opcao_estagio = '';
                $object->razao_social_empresa = '';
                $object->cnpj_empresa = ''; 
                $object->nome_pessoa_fisica = '';
                $object->cpf_pessoa_fisica = '';  
            }
            
            
            //Data de término não pode ser anterior à de início
            if($object->data_termino < $object->data_inicio){

                throw new Exception("A data de término não pode ser anterior à data de início");
            }
            
            
            //Verifica se as datas são válidas
            $data_inicio = explode('/', $param['data_inicio']);
            $data_termino = explode('/', $param['data_termino']);
            
            if (!checkdate($data_inicio[1], $data_inicio[0], $data_inicio[2])) //mês, dia e ano 
            {
                throw new Exception("A data de início não é uma data válida");
            } 
            
            if (!checkdate($data_termino[1], $data_termino[0], $data_termino[2])) //mês, dia e ano 
            {
                throw new Exception("A data de término não é uma data válida");
            }
            
            
            //Verifica se o ano de início do estágio é o mesmo ano em que ele foi realizado (o ano de término pode ser diferente)
            if ($data_inicio[2] <> $object->ano) 
            {
                throw new Exception("O ano de realização do estágio e o ano de início estão diferentes");
            } 
            
            
            //Verifica se o semestre de início do estágio é o mesmo semestre em que ele foi realizado (o semestre de término pode ser diferente)
            if($object->semestre == '1' AND $data_inicio[1] > 6) 
            {
                throw new Exception("O semestre de realização do estágio e o semestre de início estão diferentes");
            }
            
            if($object->semestre == '2' AND $data_inicio[1] <= 6)
            {
                throw new Exception("O semestre de realização do estágio e o semestre de início estão diferentes");
            }


            //Verifica se o responsável selecionado é o designado para aprovar as atividades do curso
            $curso = $param['cod_curso'];

            switch ($curso) 
            {
              //Agronomia
              case "15":
                if($object->cod_prof_responsavel <> '136')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Direito  
              case "16":
                if($object->cod_prof_responsavel <> '841')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Veterinária  
              case "20":
                if($object->cod_prof_responsavel <> '235')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Sistemas  
              case "21":
                if($object->cod_prof_responsavel <> '774')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Enfermagem  
              case "70":
                if($object->cod_prof_responsavel <> '717')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;              
              
              //Administração
              case "10":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Administração EAD  
              case "115":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Ciências Biológicas  
              case "13":
                if($object->cod_prof_responsavel <> '50')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Contábeis  
              case "62":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Contábeis EAD  
              case "114":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Eng. Civil  
              case "69":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break; 
                  
              //Eng. Produção  
              case "68":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Eng. Elétrica  
              case "104":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                
              //Eng. Mecânica  
              case "67":
                if($object->cod_prof_responsavel <> '836')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Recursos Humanos  
              case "116":
                if($object->cod_prof_responsavel <> '13')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //História  
              case "8":
                if($object->cod_prof_responsavel <> '72')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //História EAD  
              case "88":
                if($object->cod_prof_responsavel <> '27')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Letras  
              case "5":
                if($object->cod_prof_responsavel <> '184')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Matemática  
              case "1":
                if($object->cod_prof_responsavel <> '371')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Pedagogia  
              case "6":
                if($object->cod_prof_responsavel <> '338')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;  
              
              //Pedagogia EAD  
              case "39":
                if($object->cod_prof_responsavel <> '338')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
              
              //Geografia EAD  
              case "89":
                if($object->cod_prof_responsavel <> '72')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;

              //Estudos Sociais  
              case "7":
                if($object->cod_prof_responsavel <> '72')
                {
                    throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
                }
                break;
                     
              default:
                throw new Exception("O professor selecionado não é o responsável pela aprovação das atividades do curso escolhido");
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
                    $target_path  = 'secretaria/estagios/aluno_' . $object->cod_aluno;
                    
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
                        $nome_tmp = 'tmp/' . 'comprovante_estagio_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;                        
                        rename($source_file, $nome_tmp);
                                   
                                                
                        //Ghostscript usa o caminho absoluto
                        $caminho_absoluto_tmp = realpath($nome_tmp);                                                
                        $caminho_absoluto_target = realpath($target_path);   
                        $caminho_absoluto_pdf = $caminho_absoluto_target . '/comprovante_estagio_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;
                        
                        
                        //Sobe arquivo independentemente de versão (sem ghostscript, estava apresentando erro em versões maiores que 1.4)
                        shell_exec('gswin32c -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=' . $caminho_absoluto_pdf . ' ' . $caminho_absoluto_tmp);                         
                    }
                }
                
                //Se arquivo foi salvo                                              
                if(file_exists($caminho_absoluto_pdf))
                {
                    //Apaga arquivo da pasta tmp
                    if($nome_tmp)
                    {
                        unlink($nome_tmp);    
                    }
                    
                    if($object->status_estagio == NULL)
                    {
                        $object->status_estagio = "Aguardando aprovação";
                    }                        
                           
                    $object->tipo_entrada = "Estágio";
                    $object->nome_curso = $fi_curso->Nome;
                    $object->cnpj_empresa = str_replace(array(".", "/", "-"), "", $object->cnpj_empresa);
                    $object->cpf_pessoa_fisica = str_replace(array(".", "-"), "", $object->cpf_pessoa_fisica);
                    $object->arquivo = 'comprovante_estagio_' . $object->cod_aluno . '_' . $object->cod_curso . '_' . $datetime . '.' . $extensao;
                    $object->caminho_arquivo = $target_path;                    
                    $object->status_pdfa = 0;
                    $object->status_assinatura = 0;
                    $object->system_user_id = TSession::getValue('userid');
                    $object->data_reg = date('Y-m-d H:i:s');  
                
                    $object->store();
                            
                           
                    //Dispara e-mail para o professor (se FFCL, FAJOB, NEAD)
                    $loggedUnit = TSession::getValue('userunitid');
                    
                    if($loggedUnit == 2 OR $loggedUnit == 6 OR $loggedUnit == 10)
                    {                    
                        TTransaction::open('dados_fei');
                        
                        $professor = new FiProfessor($object->cod_prof_responsavel);
                        
                        TTransaction::close();
                
                
                        $corpoEmail = 'Prezado(a), 
                    
Um novo estágio foi enviado e aguarda sua análise.
    
Aluno(a): ' . $object->nome_aluno . '  
    
Curso: ' . $object->nome_curso;
    
                
                        if($professor->Email AND $corpoEmail)
                        {
                            $prefs = SystemPreference::getAllPreferences();
                            
                            $mail = new TMail;
                            $mail->setFrom($prefs['mail_from'], "Mensagem - Secretaria Acadêmica");
                            $mail->setSubject('Avaliação de Estágio');
                            $mail->setTextBody($corpoEmail);  
                            
                            $mail->addAddress($professor->Email);  
                              
                            $mail->SetUseSmtp();
                            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
                            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
                            $mail->send();
                        }
                    }                          
                    
                    $data->id = $object->id;
            
                    $this->form->setData($data);
                    TTransaction::close();
                    
                    new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
                    
                    
                    //Limpa o formulário depois de salvar, mas mantém o código e nome do aluno preenchido
                    $this->form->clear();
                                
                    $obj = new StdClass;
                    $obj->cod_aluno = $object->cod_aluno;
                    $obj->nome_aluno = $param['nome_aluno'];
                    
                    TForm::sendData('form_Estagio', $obj);
                    
                    $this->onReload();
                }
                else
                {
                    throw new Exception("Erro ao fazer upload do arquivo. Por favor, reinicie o processo");
                }
            }
            else
            {
                throw new Exception("É necessário anexar o comprovante em PDF");
            }    
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            
            $param['opcao_estagio'] = $object->opcao_estagio;
            $this->onOpcaoEstagioChange($param);
            
            $obj = new StdClass;
            $obj->razao_social_empresa = $object->razao_social_empresa;
            $obj->cnpj_empresa = $object->cnpj_empresa;
            $obj->nome_pessoa_fisica = $object->nome_pessoa_fisica;
            $obj->cpf_pessoa_fisica = $object->cpf_pessoa_fisica;
                    
            TForm::sendData('form_Estagio', $obj);
            
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
                
                $object = new Estagio($key);
                
                $param['opcao_estagio'] = $object->opcao_estagio;
                $this->onOpcaoEstagioChange($param);
                
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
    

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        
        parent::show();
    }
}
