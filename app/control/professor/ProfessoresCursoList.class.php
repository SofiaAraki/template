<?php


class ProfessoresCursoList extends TPage
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
        $this->form = new TQuickForm('form_search_VwProfessordisciplinassemestre');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('VwProfessordisciplinassemestre');
        

        // create the form fields
        //$NomeProfessor = new TEntry('NomeProfessor');
        //$NomeCurso = new TDBCombo('NomeCurso','dados_fei_t','VwProfessordisciplinassemestre','NomeCurso','NomeCurso');

        $NomeCurso = new TCombo('NomeCurso');
            
            TTransaction::open('Felabs_DB');
                $loggedUnit = TSession::getValue('userunitid');
            TTransaction::close();

            TTransaction::open('dados_fei');
            
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('CodEntidade', '=', $loggedUnit), TExpression::AND_OPERATOR);
            $criteria1->setProperty('order', 'NomeCurso asc');


            $cursos = VwProfessordisciplinassemestre::getObjects($criteria1);
            $options = [];
            
            if ($cursos)
            {
                foreach ($cursos as $curso)
                {
                    $options[ $curso-> NomeCurso ] = $curso-> NomeCurso;                               
                }            
            }
            
            $NomeCurso->addItems($options);
            
            TTransaction::close();


        // add the fields
        //$this->form->addQuickField('Nomeprofessor', $NomeProfessor, '100%');
        $this->form->addQuickField('Selecione o Curso:', $NomeCurso, '40%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwProfessordisciplinassemestre_filter_data') );
        
        
        // add the search form actions
        $btn = $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction(_t('New'),  new TAction(array('', 'onEdit')), 'bs:plus-sign green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_Codprofessor = new TDataGridColumn('Codprofessor', 'Codprofessor', 'left');
        $column_NomeProfessor = new TDataGridColumn('NomeProfessor', 'Docente', 'left');
        $column_Ano = new TDataGridColumn('Ano', 'Ano', 'center');
        $column_Semestre = new TDataGridColumn('Semestre', 'Semestre', 'center');
        $column_CodCurso = new TDataGridColumn('CodCurso', 'Codcurso', 'right');
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_CodDisciplina = new TDataGridColumn('CodDisciplina', 'Coddisciplina', 'center');
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left');
        $column_EmailProf = new TDataGridColumn('fi_professor->Email', 'Email', 'left');
        $column_NomeEntidade = new TDataGridColumn('NomeEntidade', 'Entidade', 'left');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_Codprofessor);
        $this->datagrid->addColumn($column_NomeProfessor);
        $this->datagrid->addColumn($column_Ano);
        $this->datagrid->addColumn($column_Semestre);
        //$this->datagrid->addColumn($column_CodCurso);
        $this->datagrid->addColumn($column_NomeCurso);
        //$this->datagrid->addColumn($column_CodDisciplina);
        $this->datagrid->addColumn($column_NomeDisciplina);
        $this->datagrid->addColumn($column_EmailProf);
        $this->datagrid->addColumn($column_NomeEntidade);

        
        // create EDIT action
        //$action_edit = new TDataGridAction(array('', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        //$action_edit->setLabel(_t('Edit'));
        //$action_edit->setImage('far:edit blue fa-lg');
        //$action_edit->setField('Codprofessor');
        //$this->datagrid->addAction($action_edit);
        
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the page navigation
        //$this->pageNavigation = new TPageNavigation;
        //$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        //$this->pageNavigation->setWidth($this->datagrid->getWidth());
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Listagem de Professor por Curso', $this->form));
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

    /*public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('dados_fei_t'); // open a transaction with database
            $object = new VwProfessordisciplinassemestre($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }*/
    

    public function onSearch()
    {
        $data = $this->form->getData();
        

        TSession::setValue('ProfessoresCursoList_filter_NomeProfessor', NULL);
        TSession::setValue('ProfessoresCursoList_filter_NomeCurso', NULL);

        if (isset($data->NomeCurso) AND ($data->NomeCurso)) {
            $filter = new TFilter('NomeCurso', 'like', "%{$data->NomeCurso}%");
            TSession::setValue('ProfessoresCursoList_filter_NomeCurso', $filter);
        }


        $this->form->setData($data);
        
        TSession::setValue('VwProfessordisciplinassemestre_filter_data', $data);
        
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
                $loggedUnit = TSession::getValue('userunitid');
            TTransaction::close();

            TTransaction::open('dados_fei');

            $Ano = date('Y');
            $Mes = date('m');
            
            if($Mes <= 7)
            {
                $Semestre = 1;
            }
            elseif($Mes >= 8)
            {
                $Semestre = 2;
            }

            //echo $Semestre;


            $repository = new TRepository('VwProfessordisciplinassemestre');
            $limit = 1000;

            $criteria = new TCriteria;

            $criteria->add(new TFilter('Ano', '=', $Ano), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Semestre', '=', $Semestre), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit), TExpression::AND_OPERATOR);

            //$criteria->add(new TFilter('CodEntidade', '=', '2'));
            //$criteria->add(new TFilter('NomeCurso', 'like', $NomeCurso ));            
            

            //if (empty($param['order']))
            //{
            //    $param['order'] = 'Nomeprofessor';
            //    $param['direction'] = 'asc';
            //}
            
            $criteria->setProperty('order', 'Nomeprofessor asc');
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ProfessoresCursoList_filter_NomeProfessor')) {
                $criteria->add(TSession::getValue('ProfessoresCursoList_filter_NomeProfessor')); 
            }


            if (TSession::getValue('ProfessoresCursoList_filter_NomeCurso')) {
                $criteria->add(TSession::getValue('ProfessoresCursoList_filter_NomeCurso')); 
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
                    $this->datagrid->addItem($object);
                }
            }
            

            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            //$this->pageNavigation->setCount($count); 
            //$this->pageNavigation->setProperties($param); 
            //$this->pageNavigation->setLimit($limit); 
            

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
            
            TTransaction::open('dados_fei'); 
            
            $object = new VwProfessordisciplinassemestre($key, FALSE); 
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
        // check if the datagrid is already loaded
        //if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        //{
        //    if (func_num_args() > 0)
        //    {
        //        $this->onReload( func_get_arg(0) );
        //    }
        //    else
        //    {
       //         $this->onReload();
        //    }
        //  }
        parent::show();
    }
}
