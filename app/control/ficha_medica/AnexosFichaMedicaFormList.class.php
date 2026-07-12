<?php

class AnexosFichaMedicaFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_AnexosFichaMedica');
        $this->form->setFormTitle('Anexos Ficha Médica');
        

        // create the form fields
        $id_anexo = new THidden('id_anexo');
        $ficha_anexo_id = new THidden('ficha_anexo_id');
        $data_anexo = new TDate('data_anexo');
        $obs_anexo = new TText('obs_anexo');
        $anexo = new TFile('anexo');
        $caminho_anexo = new THidden('caminho_anexo');


        // add the fields
        $this->form->addFields( [ $id_anexo ] );
        $this->form->addFields( [ $ficha_anexo_id ] );
        $this->form->addFields( [ new TLabel('Data do anexo') ], [ $data_anexo ] );
        $this->form->addFields( [ new TLabel('Observação do anexo') ], [ $obs_anexo ] );
        $this->form->addFields( [ new TLabel('Anexar arquivos') ], [ $anexo ] );
        $this->form->addFields( [ $caminho_anexo ] );
        
        
        // set sizes
        $id_anexo->setSize('100%');
        $ficha_anexo_id->setSize('100%');
        $data_anexo->setSize('10%');
        $data_anexo->setMask('dd/mm/yyyy');
        $data_anexo->setDatabaseMask('yyyy-mm-dd');
        $obs_anexo->setSize('100%');
        $anexo->setSize('100%');        
        $anexo->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );


        if (!empty($id_anexo))
        {
            $id_anexo->setEditable(FALSE);
        }
        
        
        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addActionLink(('Voltar para Lista'),  new TAction(['FichaMedicaList', 'onReload']), 'fa:arrow-left');
       
       
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        //$this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id_anexo = new TDataGridColumn('id_anexo', 'ID do anexo', 'left');
        $column_ficha_anexo_id = new TDataGridColumn('ficha_anexo_id', 'ID da ficha médica relacionada', 'left');
        $column_data_anexo = new TDataGridColumn('data_anexo', 'Data do anexo', 'left');
        $column_obs_anexo = new TDataGridColumn('obs_anexo', 'Observação do anexo', 'left');
        //$column_anexo = new TDataGridColumn('anexo', 'Anexar arquivos', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id_anexo);
        $this->datagrid->addColumn($column_ficha_anexo_id);
        $this->datagrid->addColumn($column_data_anexo);
        $this->datagrid->addColumn($column_obs_anexo);
        //$this->datagrid->addColumn($column_anexo);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id_anexo');
        
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id_anexo');
        
        
        // create DOWNLOAD action
        $action_download = new TDataGridAction(array($this, 'onDownload'));
        //$action_edit->setUseButton(TRUE);
        $action_download->setButtonClass('btn btn-default');
        $action_download->setLabel(_t('Download'));
        $action_download->setImage('fas:cloud-download-alt green fa-lg');
        $action_download->setField('id_anexo');
        //$action_download->setDisplayCondition( array($this, 'displayColumn') );
        
        
        
        // add the actions to the datagrid
        //$this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action_download);
        
        
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
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('AnexosFichaMedica');
            $limit = 10;

            $criteria = new TCriteria;
            
            $ficha_medica = TSession::getValue('dados_ficha_medica');
            $criteria->add(new TFilter('ficha_anexo_id', '=', $ficha_medica->id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id_anexo';
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
                    $object->data_anexo = TDate::date2br($object->data_anexo);               

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
            
            $object = new AnexosFichaMedica($key, FALSE); 
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
                
                $ficha_medica = TSession::getValue('dados_ficha_medica');
              
                TTransaction::open('Felabs_DB');            
            
                $this->form->validate();
                $data = $this->form->getData();
    
                $object = new AnexosFichaMedica;
                $object->fromArray( (array) $data);
                $object->ficha_anexo_id = $ficha_medica->id;
                $object->store();
                
                
                 //Salvar arquivo na pasta files/ficha_medica
                 $extensao = pathinfo($object->anexo, PATHINFO_EXTENSION);

                 $today = date("YmdHis");
 
                 $source_file   = 'tmp/' . $object->anexo;
                 $target_path   = 'files/ficha_medica/';
                 $target_file   =  $target_path . 'anexo_ficha_medica_id_' . $ficha_medica->id . "_$today" . '.' . $extensao;

                
                 if (file_exists($source_file))
                 {
                     if (!file_exists($target_path))
                     {
                         if (!@mkdir($target_path, 0777, true))
                         {
                             throw new Exception(_t('Permission denied'). ': '. $target_path);
                         }
                     }

                     if (file_exists($target_path))
                     {
                         rename($source_file, $target_file);                
                     }
                 }

                 $object->anexo = 'anexo_ficha_medica_id_' . $ficha_medica->id . "_$today" . '.' . $extensao;
                 $object->caminho_anexo = $target_path;
                 $object->store();     


                  $data->id = $object->id;
                         
                  $this->form->setData($data);
                  TTransaction::close();
                        
                  new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
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
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                
                TTransaction::open('Felabs_DB'); 
                $object = new AnexosFichaMedica($key); 
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
    
    public function onDownload($param)
    {
        try
        {
            $id = $param['id_anexo'];
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new AnexosFichaMedica($id); 
            
            $caminho_arquivo = $object->caminho_anexo . $object->anexo;

            TPage::openFile($caminho_arquivo);      
               
            TTransaction::close(); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
}