<?php

class DiplomaVersaoFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_DiplomaDigitalVersao');
        $this->form->setFormTitle('<h4>Versão</h4>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $versao_diploma = new TEntry('versao_diploma');
        $versao_diploma_inicio = new TDate('versao_diploma_inicio');
        $versao_diploma_termino = new TDate('versao_diploma_termino');
        $versao_documentacao = new TEntry('versao_documentacao');
        $versao_documentacao_inicio = new TDate('versao_documentacao_inicio');
        $versao_documentacao_termino = new TDate('versao_documentacao_termino');
        $versao_historico = new TEntry('versao_historico');
        $versao_historico_inicio = new TDate('versao_historico_inicio');
        $versao_historico_termino = new TDate('versao_historico_termino');
        $versao_fiscalizacao = new TEntry('versao_fiscalizacao');
        $versao_fiscalizacao_inicio = new TDate('versao_fiscalizacao_inicio');
        $versao_fiscalizacao_termino = new TDate('versao_fiscalizacao_termino');
        $versao_curriculo = new TEntry('versao_curriculo');
        $versao_curriculo_inicio = new TDate('versao_curriculo_inicio');
        $versao_curriculo_termino = new TDate('versao_curriculo_termino');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        // add the fields
        $this->form->addFields( [ $id] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $label_explicacao = '<p style="font-size: 16px;">Os XMLs vão sempre se basear no <b>último registro lançado na tabela</b>. 
                             Portanto, se apenas um dos arquivos XSDs foi atualizado e não o pacote todo, deve-se lançar os dados 
                             de atualização no XSD correspondente e repetir os dados dos demais verificando sempre a data de início 
                             e término de cada versão.</p>';        
                                       
        $panel = new TPanelGroup();
        $panel->add($label_explicacao);
        
        $this->form->addContent( [ $panel ] );
        
        $label1 = new TLabel('<br>XSD Currículo', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        
        $row = $this->form->addFields( [ new TLabel('Versão'), $versao_curriculo ],
                                       [ new TLabel('Início'), $versao_curriculo_inicio ],
                                       [ new TLabel('Término'), $versao_curriculo_termino ] );
        $row->layout = [ 'col-sm-4', 'col-sm-4', 'col-sm-4' ];
        
        $label2 = new TLabel('<br>XSD Histórico', '#285097', 12, 'b', '<br>');
        $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label2] );
        
        $row = $this->form->addFields( [ new TLabel('Versão'), $versao_historico ],
                                       [ new TLabel('Início'), $versao_historico_inicio ],
                                       [ new TLabel('Término'), $versao_historico_termino ] );
        $row->layout = [ 'col-sm-4', 'col-sm-4', 'col-sm-4' ];
                       
        $label3 = new TLabel('<br>XSD Documentação', '#285097', 12, 'b', '<br>');
        $label3->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label3] );
        
        $row = $this->form->addFields( [ new TLabel('Versão'), $versao_documentacao ],
                                       [ new TLabel('Início'), $versao_documentacao_inicio ],
                                       [ new TLabel('Término'), $versao_documentacao_termino ] );
        $row->layout = [ 'col-sm-4', 'col-sm-4', 'col-sm-4' ];
        
        $label4 = new TLabel('XSD Diploma', '#285097', 12, 'b', '<br>');
        $label4->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label4] );
        
        $row = $this->form->addFields( [ new TLabel('Versão'), $versao_diploma ],
                                       [ new TLabel('Início'), $versao_diploma_inicio ],
                                       [ new TLabel('Término'), $versao_diploma_termino ] );
        $row->layout = [ 'col-sm-4', 'col-sm-4', 'col-sm-4' ];
        
        $label5 = new TLabel('<br>XSD Fiscalização', '#285097', 12, 'b', '<br>');
        $label5->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label5] );
        
        $row = $this->form->addFields( [ new TLabel('Versão'), $versao_fiscalizacao ],
                                       [ new TLabel('Início'), $versao_fiscalizacao_inicio ],
                                       [ new TLabel('Término'), $versao_fiscalizacao_termino ] );
        $row->layout = [ 'col-sm-4', 'col-sm-4', 'col-sm-4' ];
        
        
        //Diploma e Documentação estão presentes desde a primeira versão
        $versao_diploma->addValidation('Versão diploma', new TRequiredValidator);
        $versao_diploma_inicio->addValidation('Versão diploma - Início', new TRequiredValidator);
        $versao_diploma_termino->addValidation('Versão diploma - Término', new TRequiredValidator);        
        $versao_documentacao->addValidation('Versão documentação', new TRequiredValidator);
        $versao_documentacao_inicio->addValidation('Versão documentação - Início', new TRequiredValidator);
        $versao_documentacao_termino->addValidation('Versão documentação - Término', new TRequiredValidator);     


        // set sizes
        $versao_diploma->setTip("Ex: 1.00 ou 1.04.1");
        $versao_diploma_inicio->setMask('dd/mm/yyyy');
        $versao_diploma_inicio->setDatabaseMask('yyyy-mm-dd');
        $versao_diploma_termino->setMask('dd/mm/yyyy');
        $versao_diploma_termino->setDatabaseMask('yyyy-mm-dd');        
        $versao_documentacao->setTip("Ex: 1.00 ou 1.04.1");
        $versao_documentacao_inicio->setMask('dd/mm/yyyy');
        $versao_documentacao_inicio->setDatabaseMask('yyyy-mm-dd');
        $versao_documentacao_termino->setMask('dd/mm/yyyy');
        $versao_documentacao_termino->setDatabaseMask('yyyy-mm-dd');        
        $versao_historico->setTip("Ex: 1.00 ou 1.04.1");
        $versao_historico_inicio->setMask('dd/mm/yyyy');
        $versao_historico_inicio->setDatabaseMask('yyyy-mm-dd');
        $versao_historico_termino->setMask('dd/mm/yyyy');
        $versao_historico_termino->setDatabaseMask('yyyy-mm-dd');        
        $versao_curriculo->setTip("Ex: 1.00 ou 1.04.1");
        $versao_curriculo_inicio->setMask('dd/mm/yyyy');
        $versao_curriculo_inicio->setDatabaseMask('yyyy-mm-dd');
        $versao_curriculo_termino->setMask('dd/mm/yyyy');
        $versao_curriculo_termino->setDatabaseMask('yyyy-mm-dd');                
        $versao_fiscalizacao->setTip("Ex: 1.00 ou 1.04.1");
        $versao_fiscalizacao_inicio->setMask('dd/mm/yyyy');
        $versao_fiscalizacao_inicio->setDatabaseMask('yyyy-mm-dd');
        $versao_fiscalizacao_termino->setMask('dd/mm/yyyy');
        $versao_fiscalizacao_termino->setDatabaseMask('yyyy-mm-dd');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'min-width: 1900px';
        $this->datagrid->disableDefaultClick();
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_versao_curriculo = new TDataGridColumn('versao_curriculo', 'Versão currículo', 'center');
        $column_versao_curriculo_inicio = new TDataGridColumn('versao_curriculo_inicio', 'Início', 'center');
        $column_versao_curriculo_termino = new TDataGridColumn('versao_curriculo_termino', 'Término', 'center');
        $column_versao_historico = new TDataGridColumn('versao_historico', 'Versão histórico', 'center');
        $column_versao_historico_inicio = new TDataGridColumn('versao_historico_inicio', 'Início', 'center');
        $column_versao_historico_termino = new TDataGridColumn('versao_historico_termino', 'Término', 'center');
        $column_versao_documentacao = new TDataGridColumn('versao_documentacao', 'Versão documentação', 'center');
        $column_versao_documentacao_inicio = new TDataGridColumn('versao_documentacao_inicio', 'Início', 'center');
        $column_versao_documentacao_termino = new TDataGridColumn('versao_documentacao_termino', 'Término', 'center');
        $column_versao_diploma = new TDataGridColumn('versao_diploma', 'Versão diploma', 'center');
        $column_versao_diploma_inicio = new TDataGridColumn('versao_diploma_inicio', 'Início', 'center');
        $column_versao_diploma_termino = new TDataGridColumn('versao_diploma_termino', 'Término', 'center');                         
        $column_versao_fiscalizacao = new TDataGridColumn('versao_fiscalizacao', 'Versão fiscalização', 'center');
        $column_versao_fiscalizacao_inicio = new TDataGridColumn('versao_fiscalizacao_inicio', 'Início', 'center');
        $column_versao_fiscalizacao_termino = new TDataGridColumn('versao_fiscalizacao_termino', 'Término', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Registrado por', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data registro', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_versao_curriculo);
        $this->datagrid->addColumn($column_versao_curriculo_inicio);
        $this->datagrid->addColumn($column_versao_curriculo_termino);
        $this->datagrid->addColumn($column_versao_historico);
        $this->datagrid->addColumn($column_versao_historico_inicio);
        $this->datagrid->addColumn($column_versao_historico_termino);
        $this->datagrid->addColumn($column_versao_documentacao);
        $this->datagrid->addColumn($column_versao_documentacao_inicio);
        $this->datagrid->addColumn($column_versao_documentacao_termino);
        $this->datagrid->addColumn($column_versao_diploma);
        $this->datagrid->addColumn($column_versao_diploma_inicio);
        $this->datagrid->addColumn($column_versao_diploma_termino);
        $this->datagrid->addColumn($column_versao_fiscalizacao);
        $this->datagrid->addColumn($column_versao_fiscalizacao_inicio);
        $this->datagrid->addColumn($column_versao_fiscalizacao_termino);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_reg);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        
        $panel = new TPanelGroup('Versões XSDs');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // turn on horizontal scrolling inside panel body
        $panel->getBody()->style = "overflow-x:auto;";
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        //$container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        $container->add($panel);
        
        parent::add($container);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('DiplomaDigitalVersao');
            $limit = 10;

            $criteria = new TCriteria;
            
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
                    $object->versao_curriculo_inicio = TDate::date2br($object->versao_curriculo_inicio);
                    $object->versao_curriculo_termino = TDate::date2br($object->versao_curriculo_termino);
                    
                    $object->versao_historico_inicio = TDate::date2br($object->versao_historico_inicio);
                    $object->versao_historico_termino = TDate::date2br($object->versao_historico_termino);
                    
                    $object->versao_documentacao_inicio = TDate::date2br($object->versao_documentacao_inicio);
                    $object->versao_documentacao_termino = TDate::date2br($object->versao_documentacao_termino);
                    
                    $object->versao_diploma_inicio = TDate::date2br($object->versao_diploma_inicio);
                    $object->versao_diploma_termino = TDate::date2br($object->versao_diploma_termino);

                    $object->versao_fiscalizacao_inicio = TDate::date2br($object->versao_fiscalizacao_inicio);
                    $object->versao_fiscalizacao_termino = TDate::date2br($object->versao_fiscalizacao_termino);
                    
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
            $action = new TAction([__CLASS__, 'Delete']);
            $action->setParameters($param);    
    
            TTransaction::open('Felabs_DB');
            
            $key = $param['key'];        
            $versao = new DiplomaDigitalVersao($key);
            
            //Opção 1: Verifica se há historico/documentação/diploma/currículo/fiscalização usando a versão e, se houver, não permite a exclusão
            if((HistoricoDigital::where('dados_versao_id', '=', $versao->id)->count() > 0) OR 
               (DiplomaDigitalDocumentacao::where('dados_versao_id', '=', $versao->id)->count() > 0) OR
               (DiplomaDigitalDiploma::where('dados_versao_id', '=', $versao->id)->count() > 0) OR
               (CurriculoDigital::where('dados_versao_id', '=', $versao->id)->count() > 0) /*OR
               (DiplomaDigitalFiscalizacao::where('dados_versao_id', '=', $versao->id)->count() > 0)*/)
            {
                new TMessage('error','O registro não pode ser excluído, pois há arquivos gerados com esta versão');
                return false;
            }
            
            //Opção 2: Se não houver, só confirma se o usuário deseja realmente excluir
            else
            {    
                new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            
            TTransaction::open('Felabs_DB');
            
            $object = new DiplomaDigitalVersao($key, FALSE);
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
                        
            $object = new DiplomaDigitalVersao;
            $object->fromArray( (array) $data);
              
                        
            //Controle campos condicionais - XSD Currículo
            if($object->versao_curriculo)
            {
                if((! $object->versao_curriculo_inicio) OR (! $object->versao_curriculo_termino))
                {
                    throw new Exception('É necessário preencher as datas de início e término da versão XSD Currículo');
                }
            }
            else
            {
                if((($object->versao_curriculo_inicio) OR ($object->versao_curriculo_termino)) AND(! $object->versao_curriculo))
                {
                    throw new Exception('É necessário preencher a versão XSD Currículo');
                }
            }


            //Controle campos condicionais - XSD Histórico
            if($object->versao_historico)
            {
                if((! $object->versao_historico_inicio) OR (! $object->versao_historico_termino))
                {
                    throw new Exception('É necessário preencher as datas de início e término da versão XSD Histórico');
                }
            }
            else
            {
                if((($object->versao_historico_inicio) OR ($object->versao_historico_termino)) AND(! $object->versao_historico))
                {
                    throw new Exception('É necessário preencher a versão XSD Histórico');
                }
            }
            
            
            //Controle campos condicionais - XSD Fiscalização
            if($object->versao_fiscalizacao)
            {
                if((! $object->versao_fiscalizacao_inicio) OR (! $object->versao_fiscalizacao_termino))
                {
                    throw new Exception('É necessário preencher as datas de início e término da versão XSD Fiscalização');
                }
            }
            else
            {
                if((($object->versao_fiscalizacao_inicio) OR ($object->versao_fiscalizacao_termino)) AND (! $object->versao_fiscalizacao))
                {
                    throw new Exception('É necessário preencher a versão XSD Fiscalização');
                }
            }
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store();
                        
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
                
                $object = new DiplomaDigitalVersao($key);
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
