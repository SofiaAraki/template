<?php

class EtiquetaFormList extends TPage
{
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    protected $loaded;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        $unit_id = TSession::getValue('userunitid');
        
        if($unit_id <> 2 AND $unit_id <> 3 AND $unit_id <> 10 AND $unit_id <> 6)
        {
            new TMessage('error', 'Funcionalidade não disponível para esta unidade');
            die;
        }    
        
        
        $this->form = new BootstrapFormBuilder('form_Etiqueta');
        $this->form->setFormTitle('<h4>Etiqueta</h4>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $codigo = new TEntry('codigo');
        $nome = new TEntry('nome');
        $aplicada_automaticamente = new TRadioGroup('aplicada_automaticamente');
        $color = new TColor('color');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');


        $radio_aplicacao = [];
        $radio_aplicacao['Não'] = "Não";
        $radio_aplicacao['Sim'] = "Sim";
        
        $aplicada_automaticamente->addItems($radio_aplicacao);
         

        // add the fields
        $label_explicacao = '<p style="font-size: 15px;">Todo currículo possui um conjunto mínimo de Etiquetas. São elas: Obrigatória
        <b>(código ob)</b> que caracteriza unidades curriculares que devem ser obrigatoriamente cursadas pelos alunos para integralizar o 
        currículo e Extensão <b>(código ext)</b> que caracteriza unidades curriculares cuja carga horária, total ou parcial, deve ser usada para
        cômputo da carga de extensão do mesmo. O atributo "Aplicar automaticamente" deve ser usado para identificar qual etiqueta aplicar sobre
        unidades curriculares constantes no histórico escolar de um aluno e que não fazem parte do presente currículo, ou seja, disciplinas 
        extracurriculares. Apenas uma etiqueta pode possuir este atributo com valor Sim.</p>';        
                                    
        $panel = new TPanelGroup();
        $panel->add($label_explicacao);
        
        $this->form->addContent( [ $panel ] );
        
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Nome da etiqueta <font color="red">*</font>'), $nome ],
                                       [ new TLabel('Identificação / Sigla <font color="red">*</font>'), $codigo ] );
        $row->layout = ['col-sm-9', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Aplicar automaticamente sobre os componentes curriculares presentes no histórico e que não fazem parte do currículo <font color="red">*</font>'), $aplicada_automaticamente ],
                                       [ new TLabel('Cor da etiqueta <font color="red">*</font>'), $color ]);
        $row->layout = ['col-sm-9', 'col-sm-3'];


        $codigo->addValidation('Identificação / Sigla', new TRequiredValidator);
        $nome->addValidation('Nome da etiqueta', new TRequiredValidator);
        $aplicada_automaticamente->addValidation('Aplicar automaticamente', new TRequiredValidator);
        $color->addValidation('Cor da etiqueta', new TRequiredValidator);


        // set sizes
        $codigo->forceLowerCase();
        $aplicada_automaticamente->setSize(50);
        $aplicada_automaticamente->setLayout('horizontal');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Limpar campos', new TAction(array($this, 'onClear')), 'fa:eraser red');
        
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_codigo = new TDataGridColumn('codigo', 'Identificação / Sigla', 'center');
        $column_nome = new TDataGridColumn('nome', 'Nome da etiqueta', 'left');
        $column_aplicada_automaticamente = new TDataGridColumn('aplicada_automaticamente', 'Aplicada Automaticamente', 'center');
        $column_color = new TDataGridColumn('color', 'Cor da etiqueta', 'center');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Última edição', 'center');
        $column_data_reg = new TDataGridColumn('data_reg', 'Data do registro', 'center');

    
        $column_color->setTransformer(array($this, 'formatColor'));
        

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_codigo);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_aplicada_automaticamente);
        $this->datagrid->addColumn($column_color);
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
    
    
    public function formatColor($column_color, $object, $row)
    {
        return "<span style='padding: 5px; border-radius: 5px; color: white; background-color:$object->color'> $object->nome </span>";
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('Etiqueta');
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
            $etiqueta = new Etiqueta($key);
            
            //Opção 1: Verifica se há disciplina ou critério de integralização lançados no currículo vinculados à etiqueta e, se houver, não permite a exclusão
            $curriculos = CurriculoCriterioIntegralizacao::where('dados_etiqueta_id', 'IS NOT', NULL)->load();

            foreach($curriculos as $curriculo)
            {
                $etiquetas_id = explode(',', $curriculo->dados_etiqueta_id);
                
                foreach($etiquetas_id as $etiqueta_id)
                {
                    $etiquetas[$etiqueta_id] = $etiqueta_id;
                }
            }
            
            if((CurriculoDisciplinaEtiqueta::where('dados_etiqueta_id', '=', $etiqueta->id)->count() > 0) OR (in_array($etiqueta->id, $etiquetas)))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) à etiqueta');
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
            
            $object = new Etiqueta($key, FALSE); 
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
            
            $object = new Etiqueta;  
            $object->fromArray( (array) $data); 
            
            
            //Se está editando registro, verifica a opção "Aplicada automaticamente" comparando com os registros já existentes no BD
            if($data->id)
            {
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('id', '<>', $data->id)); 
                $criteria1->add(new TFilter('aplicada_automaticamente', '=', "Sim")); 

                $repository = new TRepository('Etiqueta'); 
                $registros_bd = $repository->load($criteria1);
                
                if ($registros_bd AND $data->aplicada_automaticamente == "Sim")
                {
                    throw new Exception("Apenas uma etiqueta pode possuir o atributo 'Aplicada automaticamente' com valor Sim");
                }
            }
            
            
            //Se está salvando um novo registro, verifica a opção "Aplicada automaticamente" comparando com os registros já existentes no BD
            if(empty($data->id))
            {
                $criteria2 = new TCriteria;
                $criteria2->add(new TFilter('aplicada_automaticamente', '=', "Sim")); 

                $repository = new TRepository('Etiqueta'); 
                $registros_bd = $repository->load($criteria2);
            
                if ($registros_bd AND $data->aplicada_automaticamente == "Sim")
                {
                    throw new Exception("Apenas uma etiqueta pode possuir o atributo 'Aplicada automaticamente' com valor Sim");
                }
                
                
                //Se já existe uma etiqueta com mesmo código, não deixa salvar
                $criteria3 = new TCriteria;
                $criteria3->add(new TFilter('codigo', '=', trim($data->codigo)));
                
                $repository = new TRepository('Etiqueta'); 
                $registros_bd = $repository->load($criteria3);
                
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro de etiqueta com este mesmo código");
                }
            }
    
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); 
            
            $this->form->clear();
            
            $this->onReload();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData( $this->form->getData() ); 
            
            TEntry::disableField('form_Etiqueta', 'codigo');
            
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
                
                $object = new Etiqueta($key); 
                
                //O código não pode ser alterado
                TEntry::disableField('form_Etiqueta', 'codigo');
                
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
