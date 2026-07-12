<?php

class ComunicadoBolsaFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;

    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_ComunicadoBolsa');
        $this->form->setFormTitle('<h4>Comunicado Bolsa</h4>');
        
        
        $unit_id = TSession::getValue('userunitid');
        $funcao_legado = "Aluno";
        
        //Filtra baseado na unidade logada
        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('id', '=', $unit_id));

        
        //Filtra os alunos por unidade
        $criteria2 = new TCriteria;
        $criteria2->add(new TFilter('id', 'IN', '(SELECT system_user_id FROM system_user_unit WHERE system_unit_id = ' . $unit_id . ')'));
        $criteria2->add(new TFilter('funcao_legado', '=', $funcao_legado));


        // create the form fields
        $id = new THidden('id');
        $opcao_bolsa = new TCombo('opcao_bolsa');
        $ano_referencia = new TEntry('ano_referencia');
        $titulo = new TEntry('titulo');
        $conteudo = new THtmlEditor('conteudo');
        $data_postagem = new TDate('data_postagem');
        $data_expiracao = new TDate('data_expiracao');
        $system_unit_id = new TDBCombo('system_unit_id', 'Felabs_DB', 'SystemUnit', 'id', 'name', 'name', $criteria1);
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        
        //Criado manualmente
        $alunos_list = new TDBMultiSearch('alunos_list', 'Felabs_DB', 'SystemUser', 'id', 'name', 'name', $criteria2);


        $bolsa = [];
        $bolsa['Concessão'] = "Concessão";
        $bolsa['Renovação'] = "Renovação";
        
        $opcao_bolsa->addItems($bolsa);
        

        // add the fields
        $this->form->addFields( [ $id ] );
        
        $row = $this->form->addFields( [ new TLabel('Unidade'), $system_unit_id ],
                                       [ new TLabel('Opção'), $opcao_bolsa ],
                                       [ new TLabel('Ano referência'), $ano_referencia ], 
                                       [ new TLabel('Data postagem'), $data_postagem ],
                                       [ new TLabel('Data expiração'), $data_expiracao ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Título'), $titulo ] );
        $row->layout = ['col-sm-12'];        
        
        $row = $this->form->addFields( [ new TLabel('Conteúdo'), $conteudo ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('Alunos'), $alunos_list ] );
        $row->layout = ['col-sm-12'];

        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );


        $titulo->addValidation('Título', new TRequiredValidator);
        $conteudo->addValidation('Conteúdo', new TRequiredValidator);
        $alunos_list->addValidation('Alunos', new TRequiredValidator);

        // set sizes
        $system_unit_id->setSize('100%');
        $opcao_bolsa->setSize('100%');
        $ano_referencia->setSize('100%');
        $ano_referencia->setMask('9999');
        $data_postagem->setMask('dd/mm/yyyy');
        $data_postagem->setDatabaseMask('yyyy-mm-dd');
        $data_expiracao->setMask('dd/mm/yyyy');
        $data_expiracao->setDatabaseMask('yyyy-mm-dd');
        $titulo->setSize('100%');
        $conteudo->setSize('100%',250);
        $alunos_list->setSize('100%', 300);
    

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_opcao_bolsa = new TDataGridColumn('opcao_bolsa', 'Opção', 'center');
        $column_ano_referencia = new TDataGridColumn('ano_referencia', 'Ano', 'center');
        $column_titulo = new TDataGridColumn('titulo', 'Título', 'left');
        $column_conteudo = new TDataGridColumn('conteudo', 'Conteúdo', 'left');
        $column_system_unit_id = new TDataGridColumn('system_unit->name', 'Unidade', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do Registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_opcao_bolsa);
        $this->datagrid->addColumn($column_ano_referencia);
        $this->datagrid->addColumn($column_titulo);
        $this->datagrid->addColumn($column_conteudo);
        $this->datagrid->addColumn($column_system_unit_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        
        $action3 = new TDataGridAction([$this, 'onExport']);
        //$action3->setUseButton(TRUE);
        //$action3->setButtonClass('btn btn-default');
        $action3->setLabel('Relatório aceites');
        $action3->setImage('fas:list green');
        $action3->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action3);
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        
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
    
    public function onExport($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $comunicado = new ComunicadoBolsa($param['id']);
                      
            $repository_aceites = new TRepository('ComunicadoBolsaAceite');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('comunicado_id', '=', $comunicado->id));             

            $alunos = $repository_aceites->load($criteria);
       
            $csv = '';               
            

            if ($alunos)
            {
                $filename = 'relatorio_comunicado_bolsa.csv';
                $filepath = 'app/output/' . $filename;

                $csv = "sep=;\n"; 
                
                // Substituindo os utf8_decode por mb_convert_encoding
                $csv .= mb_convert_encoding(mb_strtoupper($comunicado->titulo), 'ISO-8859-1', 'UTF-8') . "\n";
                $csv .= mb_convert_encoding('ALUNO;STATUS;DATA DO REGISTRO', 'ISO-8859-1', 'UTF-8') . "\n";
                
                foreach ($alunos as $aluno)
                {   
                    $hr = substr($aluno->data_reg, 11, 19);
                    $dt = TDate::date2br($aluno->data_reg);

                    $aluno->data_reg = "$dt " . substr($hr, 0, -4); 
                
                    // Substituindo nas colunas dos alunos
                    $csv .= mb_convert_encoding($aluno->system_user->name, 'ISO-8859-1', 'UTF-8') . ';'.
                            mb_convert_encoding($aluno->status_aceite, 'ISO-8859-1', 'UTF-8') . ';'.
                            mb_convert_encoding($aluno->data_reg, 'ISO-8859-1', 'UTF-8') . "\n";   
                }
            }
            else
            {
                new TMessage('error', 'Não há registros');
                die;
            }
                file_put_contents('app/output/relatorio_comunicado_bolsa.csv', $csv);
                TPage::openFile('app/output/relatorio_comunicado_bolsa.csv');
           
            
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
            
            $repository = new TRepository('ComunicadoBolsa');
            $limit = 1;

            $unit_id = TSession::getValue('userunitid');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_unit_id', '=', $unit_id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
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
                    $hr = substr($object->data_reg, 11, 19);
                    $dt = TDate::date2br($object->data_reg);
                    $object->data_reg = "$dt" . " " . substr($hr,0,-7);
                    
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
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
            
            $object = new ComunicadoBolsa($key, FALSE);
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
            
            $object = new ComunicadoBolsa;
            $object->fromArray( (array) $data);
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');

            $object->store();
            
            //Verifica se aluno já foi incluído
            if ($data->alunos_list)
            { 
                foreach ($data->alunos_list as $cada_aluno)
                {
                    $list[$cada_aluno] = $cada_aluno;
                    
                    $criteria1 = new TCriteria;
                    $criteria1->add(new TFilter('comunicado_id', '=', $object->id)); 
                    $criteria1->add(new TFilter('system_user_id', '=', $cada_aluno)); 

                    $aluno_participante = ComunicadoBolsaParticipante::getObjects($criteria1);

                    if(empty($aluno_participante))
                    {
                        $aluno = new ComunicadoBolsaParticipante;
                        $aluno->comunicado_id = $object->id;
                        $aluno->system_user_id = $cada_aluno;
                    
                        $aluno->store();
                    }
                }                    
            }
            
            //Exclui registro do aluno que foi retirado da lista
            $criteria2 = new TCriteria;
            $criteria2->add(new TFilter('comunicado_id', '=', $object->id));
                    
            $participantes = ComunicadoBolsaParticipante::getObjects($criteria2);
            
            foreach($participantes as $participante)
            {
                if(! in_array($participante->system_user_id, $list))
                {
                    $aluno_nao_participante = ComunicadoBolsaParticipante::find($participante->id);
                    $aluno_nao_participante->delete();       
                }
            }
            
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            $this->onReload();
            $this->form->clear();
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
                
                $object = new ComunicadoBolsa($key);
                
                $criteria = new TCriteria;
                $criteria->add(new TFilter('comunicado_id', '=', $object->id));
                
                $alunos = ComunicadoBolsaParticipante::getObjects($criteria);
                
                $alunos_list = array();
                
                if ($alunos)
                {
                    foreach ($alunos as $aluno)
                    {
                        $aluno_list[$aluno->id] = $aluno->system_user_id;
                    }
                }
                
                $object->alunos_list = $aluno_list;                
                
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
