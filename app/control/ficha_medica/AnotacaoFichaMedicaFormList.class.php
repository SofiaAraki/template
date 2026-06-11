<?php

class AnotacaoFichaMedicaFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
                
        
        $this->form = new BootstrapFormBuilder('form_AnotacaoFichaMedica');
        $this->form->setFormTitle('AnotacaoFichaMedica');
        

        // create the form fields
        $id_anot = new TEntry('id_anot');
        $ficha_id = new TEntry('ficha_id', 'Felabs_DB', 'FichaMedica', 'id', 'cod_aluno');
        $cod_aluno = new TEntry('cod_aluno');
        $data_anot = new TDate('data_anot');
        $obs_anot = new TText('obs_anot');


        // add the fields
        $this->form->addFields( [ new TLabel('ID') ,  $id_anot ], [ new TLabel('ID Ficha') ,  $ficha_id ]);
        $this->form->addFields(  );
        $this->form->addFields( [ new TLabel('Cod. Aluno') ,  $cod_aluno ] );
        $this->form->addFields( [ new TLabel('Data Anot.') ,  $data_anot ],[ new TLabel('Observação') ,  $obs_anot ] );
        $this->form->addFields(  );



        // set sizes
        $id_anot->setSize('100%');
        $ficha_id->setSize('100%');
        $cod_aluno->setSize('100%');
        $data_anot->setSize('100%');
        $obs_anot->setSize('100%');
        $ficha_id->setEditable(FALSE);
        $cod_aluno->setEditable(FALSE);
        $data_anot->setMask('dd/mm/yyyy');
        $data_anot->setDatabaseMask('yyyy-mm-dd');

        /*TTransaction::open('Felabs_DB');

        $object = new FichaMedica($param['key']);
        $aluno      = $object->nome;
        $codaluno   = $object->cod_aluno;
        $id_ficha   = $object->id;
   
        TTransaction::close();
        $ficha_id->setValue($id_ficha);
        $cod_aluno->setValue($codaluno. ' - ' .$aluno);*/
        
        $ficha_medica = TSession::getValue('dados_ficha_medica');

        $ficha_id->setValue($ficha_medica->id);
        $cod_aluno->setValue($ficha_medica->cod_aluno . ' - ' . $ficha_medica->nome);
        

        if (!empty($id_anot))
        {
            $id_anot->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(('Voltar para Lista'),  new TAction(['FichaMedicaList', 'onReload']), 'fa:arrow-left');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id_anot = new TDataGridColumn('id_anot', 'ID', 'left');
        $column_ficha_id = new TDataGridColumn('ficha_id', 'ID Ficha', 'left');
        $column_cod_aluno = new TDataGridColumn('cod_aluno', 'Cod. Aluno', 'left');
        $column_data_anot = new TDataGridColumn('data_anot', 'Data Anot.', 'left');
        $column_obs_anot = new TDataGridColumn('obs_anot', 'Observação', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id_anot);
        $this->datagrid->addColumn($column_ficha_id);
        $this->datagrid->addColumn($column_cod_aluno);
        $this->datagrid->addColumn($column_data_anot);
        $this->datagrid->addColumn($column_obs_anot);


        $column_data_anot->setTransformer( function($value, $object, $row) {
            $data_anot = new DateTime($value);
            return $data_anot->format('d/m/Y');
        });

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id_anot');
        
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id_anot');
        
        
        // add the actions to the datagrid
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
    

    public function onReload($param = NULL)
    {
        try
        {

            TTransaction::open('Felabs_DB');
            
            $ficha_medica = TSession::getValue('dados_ficha_medica');

            $repository = new TRepository('AnotacaoFichaMedica');
            $limit = 10;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('ficha_id', '=', $ficha_medica->id));
            
            if (empty($param['order']))
            {
                $param['order'] = 'data_anot';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
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
            
            $object = new AnotacaoFichaMedica($key, FALSE); 
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
            $ficha_medica = TSession::getValue('dados_ficha_medica');
            
            TTransaction::open('Felabs_DB');             

            $this->form->validate(); 
            $data = $this->form->getData(); 
                
            $object = new AnotacaoFichaMedica;  
            $object->fromArray( (array) $data); 
            $object->ficha_id = $ficha_medica->id;

            $object->store(); 
            
            $data->id_anot = $object->id_anot;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); 
            
            //Limpa o formulário depois de salvar, mas mantém o código e nome do aluno preenchido
            $this->form->clear();
                        
            $obj = new StdClass;
            $obj->ficha_id = $object->ficha_id;
            $obj->cod_aluno = $object->cod_aluno;

            TForm::sendData('form_AnotacaoFichaMedica', $obj);
            
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
                 
                $object = new AnotacaoFichaMedica($key); 
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
