<?php
/**
 * VwProfessordisciplinassemestreList Listing
 * @author  <your name here>
 */
class CoordenadorPIList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param)
    {
        parent::__construct();         

        // creates the form
        $this->form = new TQuickForm('form_search_CoordenadorPI');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('CoordenadorPIList');
        
        // create the form fields
        $NomeProfessor = new TEntry('NomeProfessor');
        $NomeCurso = new TEntry('NomeCurso');
        $Etapa = new TEntry('Etapa');
        //$NomeDisciplina = new TEntry('NomeDisciplina');
        $Identificacao = new TEntry('Identificacao');
        $Ano = new TEntry('Ano');
        $Semestre = new TEntry('Semestre');
        $NomeEntidade = new TEntry('NomeEntidade');


        // add the fields
        // $this->form->addQuickField('Professor', $NomeProfessor,  '100%' );
        // $this->form->addQuickField('Nome do Curso', $NomeCurso,  '100%' );
        // $this->form->addQuickField('Etapa', $Etapa,  '100%' );
        // $this->form->addQuickField('Nome da Disciplina', $NomeDisciplina,  '100%' );
        // $this->form->addQuickField('Identificacao', $Identificacao,  '100%' );
        // $this->form->addQuickField('Ano', $Ano,  '100%' );
        // $this->form->addQuickField('Semestre', $Semestre,  '100%' );
        // $this->form->addQuickField('Mantida', $NomeEntidade,  '100%' );

        // keep the form filled during navigation with session data
        //$this->form->setData( TSession::getValue('CoordenadorPI_filter_data') );
        
        // add the search form actions
        //$btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$this->form->addQuickAction( 'Show results', new TAction(array($this, 'showResults')), 'far:check-circle green' );
        //$this->form->addQuickAction(_t('New'),  new TAction(array('', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');

        // creates the datagrid columns
        $column_NomeProfessor = new TDataGridColumn('NomeProfessor', 'Professor', 'left');
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left','80%');
        $column_Identificacao = new TDataGridColumn('Identificacao', 'Turma', 'left','40%');
        $column_CodTurmaetapa = new TDataGridColumn('CodTurmaetapa', 'CodTurmaetapa', 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_NomeProfessor);
        $this->datagrid->addColumn($column_Identificacao);
        $this->datagrid->addColumn($column_NomeCurso);
        //$this->datagrid->addColumn($column_CodTurmaetapa);
   
        // creates the datagrid actions
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(FALSE);
        $action_select->setButtonClass('btn btn-default');
        $action_select->setLabel(AdiantiCoreTranslator::translate('Select'));
        $action_select->setImage('far:check-circle blue');
        $action_select->setField('CodTurmaetapa');
        $this->datagrid->addAction($action_select);

        $action_papeletaPI = new TDataGridAction(array($this, 'onPapeletaPI'));
        $action_papeletaPI->setUseButton(FALSE);
        $action_papeletaPI->setButtonClass('btn btn-default');
        $action_papeletaPI->setLabel('Papeleta');
        $action_papeletaPI->setImage('far:file-pdf red');
        $action_papeletaPI->setField('CodTurmaetapa');
        $this->datagrid->addAction($action_papeletaPI);

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        //$this->pageNavigation = new TPageNavigation;
        //$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        //$this->pageNavigation->setWidth($this->datagrid->getWidth());

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        //$container->add(TPanelGroup::pack('Buscar Disciplina Por:', $this->form));
        $container->add(TPanelGroup::pack('Turmas - PI e Núcleo Integrador', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('CoordenadorPIList_filter_NomeCurso',   NULL);
        
        if (isset($data->NomeCurso) AND ($data->NomeCurso)) {
            $filter = new TFilter('NomeCurso', 'like', "%{$data->NomeCurso}%"); // create the filter
            TSession::setValue('CoordenadorPIList_filter_NomeCurso',   $filter); // stores the filter in the session
        }

        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('CoordenadorPI_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onSelect($param)
    {
        // get the parameter and shows the message
        $key = $param['key'];
        
        //die();
        // get the course description
        //var_dump($key);
        //die();
        foreach ($this->datagrid->getItems() as $object)
        {
            //echo $object->CodTurmaetapa;
            if ($key == $object->CodTurmaetapa)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

                //echo $object->CodGradeDisciplinaEtapaFrente;
                //die();

                TSession::setValue('sessao_coordenador', array( 'CodGradeEtapa'  => $object->CodGradeEtapa,
                                                                'Ano'            => $object->Ano,
                                                                'Semestre'       => $object->Semestre,
                                                                'CodCoordenador' => $object->CodCoordenador,
                                                                'Codprofessor'   => $object->Codprofessor,
                                                                'key'            => $object->CodTurmaetapa,
                                                                'Identificacao'  => $object->Identificacao,
                                                                'CodCurso'       => $object->CodCurso
                                                            )
                                   );
            }
        }
       //var_dump(TSession::getValue('sessao_coordenador'));
       //die();

        TApplication::loadPage('LancamentoPI');
    }
    
    public function onPapeletaPI($param)
    {
        // get the parameter and shows the message
       $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($this->datagrid->getItems());
        //die();
        foreach ($this->datagrid->getItems() as $object)

            //echo $object->CodTurmaetapa;
        {
            if ($key == $object->CodTurmaetapa)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

                //echo $object->CodGradeDisciplinaEtapaFrente;
                //die();

                TSession::setValue('sessao_papeletaPI', array( 'CodGradeEtapa'   => $object->CodGradeEtapa,
                                                                'Ano'            => $object->Ano,
                                                                'Semestre'       => $object->Semestre,
                                                                'CodCoordenador' => $object->CodCoordenador,
                                                                'Codprofessor'   => $object->Codprofessor,
                                                                'key'            => $object->CodTurmaetapa,
                                                                'Identificacao'  => $object->Identificacao,
                                                                'CodCurso'       => $object->CodCurso
                                                            )
                                   );
            }
        }
       //var_dump(TSession::getValue('sessao_papeletaPI'));
       //die();

        TApplication::loadPage('PINIReport');
    }

    public function onReload($param = NULL)
    {
        try
        {
                // open a transaction with database 'dados_fei'
                TTransaction::open('Felabs_DB');
                
                $logged = SystemUser::newFromLogin(TSession::getValue('login'));

                $loggedUnit = TSession::getValue('userunitid');
                // creates a repository for VwProfessordisciplinassemestre
                $professor = $logged-> systemuser_codlegado;
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

             
                $repository = new TRepository('VwCoordenadorturmaetapa');
                $limit = 50;
                
                // creates a criteria
                $criteria = new TCriteria;
                
                $criteria->add(new TFilter('Codprofessor', '=', $logged-> systemuser_codlegado));
                $criteria->add(new TFilter('Ano', '=', $Ano), TExpression::AND_OPERATOR);
                $criteria->add(new TFilter('Semestre', '=', $Semestre), TExpression::AND_OPERATOR);
                $criteria->add(new TFilter('CodEntidade', '=', 2 ), TExpression::AND_OPERATOR);
                //echo $criteria->dump();
                // default order
                if (empty($param['order']))
                {
                    $param['order'] = 'CodTurmaetapa';
                    $param['direction'] = 'asc';
                }
                $criteria->setProperties($param); // order, offset
                $criteria->setProperty('limit', $limit);

                if ($loggedUnit <> 2) {
                    
                     new TMessage('error', "Função não disponível para a Entidade");
                }

                if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeProfessor')) {
                    $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeProfessor')); // add the session filter
                }

                if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')) {
                    $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')); // add the session filter
                }

                // load the objects according to criteria
                $objects = $repository->load($criteria, FALSE);
                
                if (is_callable($this->transformCallback))
                {
                    call_user_func($this->transformCallback, $objects, $param);
                }
                
                $this->datagrid->clear();
                if ($objects)
                {
                    // iterate the collection of active records
                    foreach ($objects as $object)
                    {
                        // add the object inside the datagrid
                        $this->datagrid->addItem($object);
                    }
                }
                
                // reset the criteria for record count
                $criteria->resetProperties();
                $count= $repository->count($criteria);
                
                //$this->pageNavigation->setCount($count); // count of records
                //$this->pageNavigation->setProperties($param); // order, page
                //$this->pageNavigation->setLimit($limit); // limit
                
                // close the transaction
                //$this->form->clear(); 
                TTransaction::close();
                $this->loaded = true;

            //}else{
               // echo "Não retorna";
           // }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
   
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
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
