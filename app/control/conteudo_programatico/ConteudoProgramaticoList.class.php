<?php

class ConteudoProgramaticoList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct()
    {
        parent::__construct();        
        
        // creates the form
        $this->form = new TQuickForm('form_search_ConteudoProgramatico');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('Conteúdo Programático');
        
        // create the form fields
        $usuario = new TEntry('usuario');
        $curso = new TEntry('curso');
        $disciplina = new TEntry('disciplina');
        $etapa = new TEntry('etapa');
        $turma = new TEntry('turma');
        $data_reg = new TEntry('data_reg');
        $id = new TEntry('id');
        $status = new TEntry('status');

        // add the fields
        $this->form->addQuickField('Curso', $curso, '100%');
        $this->form->setData(TSession::getValue('ConteudoProgramatico_filter_data'));
        
        // keep the search data in the session
        
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search blue');
        $this->form->addQuickAction('Cadastrar Novo', new TAction(array('ConteudoProgramaticoForm', 'onEdit')), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        

        // add the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_curso = new TDataGridColumn('curso', 'Curso', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'left');
        $column_turma = new TDataGridColumn('turma', 'Turma', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_curso);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_turma);
        $this->datagrid->addColumn($column_status);


        // action onEdit
        $action_edit = new TAction(array('ConteudoProgramaticoFormView', '__construct'));
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:eye blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        

        // action onDelete
        $action_del = new TAction(array($this, 'onDelete'));
        $action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
    
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('ConteudoProgramaticoList_filter_curso',   NULL);
        
        if (isset($data->curso) AND ($data->curso)) {
            $filter = new TFilter('curso', 'like', "%{$data->curso}%"); // create the filter
            TSession::setValue('ConteudoProgramaticoList_filter_curso',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ConteudoProgramatico_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('ConteudoProgramatico');
            $limit = 10;
            
            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ConteudoProgramaticoList_filter_curso')) {
                $criteria->add(TSession::getValue('ConteudoProgramaticoList_filter_curso'));
            }
            
            $logged = TSession::getValue('userid');
            $criteria->add(new TFilter('system_user_id', '=', $logged));
            
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // Estrutura unificada baseada no Programa de Ensino para tradução de códigos
                $disciplinasIds = array();
                foreach ($objects as $obj) {
                    if (!empty($obj->disciplina)) {
                        $disciplinasIds[$obj->disciplina] = $obj->disciplina;
                    }
                }

                $nomesDisciplinas = array();
                if (!empty($disciplinasIds)) {
                    TTransaction::open('dados_fei');
                    $criteria2 = new TCriteria;
                    $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', 'IN', $disciplinasIds));
                    $discObjects = VwProfessordisciplinassemestre::getObjects($criteria2);
                    if ($discObjects) {
                        foreach ($discObjects as $dObj) {
                            $nomesDisciplinas[$dObj->CodGradeDisciplinaEtapaFrente] = $dObj->NomeDisciplina;
                        }
                    }
                    TTransaction::close();
                }

                foreach ($objects as $object)
                {
                    $object->disciplina = isset($nomesDisciplinas[$object->disciplina]) ? $nomesDisciplinas[$object->disciplina] : $object->disciplina;
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
    
    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 

            $criteria = new TCriteria;
            $criteria->add(new TFilter('conteudo_programatico_id', '=', $param['key']));

            $cpitens = ConteudoProgramaticoItem::getObjects($criteria);

            if(!empty($cpitens))
            {
                foreach($cpitens as $cpitem)
                {
                    $cpitem->delete();
                }
            }

            $object = new ConteudoProgramatico($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            $this->onReload( $param ); 
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}