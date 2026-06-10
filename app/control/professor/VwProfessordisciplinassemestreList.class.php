<?php


class VwProfessordisciplinassemestreList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct($param)
    {
        parent::__construct();         


        // creates the form
        $this->form = new TQuickForm('form_search_VwProfessordisciplinassemestre');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('VwProfessordisciplinassemestre');
        
        
        // create the form fields
        $NomeProfessor = new TEntry('NomeProfessor');
        $NomeCurso = new TEntry('NomeCurso');
        $Etapa = new TEntry('Etapa');
        $NomeDisciplina = new TEntry('NomeDisciplina');
        $Identificacao = new TEntry('Identificacao');
        $Ano = new TEntry('Ano');
        $Semestre = new TEntry('Semestre');
        $NomeEntidade = new TEntry('NomeEntidade');


        // add the fields
        //$this->form->addQuickField('Professor', $NomeProfessor, '100%');
        $this->form->addQuickField('Nome do Curso', $NomeCurso, '100%');
        //$this->form->addQuickField('Etapa', $Etapa, '100%');
        $this->form->addQuickField('Nome da Disciplina', $NomeDisciplina, '100%');
        /*$this->form->addQuickField('Identificacao', $Identificacao, '100%');
        $this->form->addQuickField('Ano', $Ano, '100%');
        $this->form->addQuickField('Semestre', $Semestre, '100%');
        $this->form->addQuickField('Mantida', $NomeEntidade, '100%');*/


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwProfessordisciplinassemestre_filter_data') );

        
        // add the search form actions
        $btn = $this->form->addQuickAction(('Buscar'), new TAction(array($this, 'onSearch')), 'fas:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction( 'Show results', new TAction(array($this, 'showResults')), 'far:check-circle green' );        
        //$this->form->addQuickAction(_t('New'),  new TAction(array('', 'onEdit')), 'bs:plus-sign green');
        
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';


        // creates the datagrid columns
        $column_NomeProfessor = new TDataGridColumn('NomeProfessor', 'Professor', 'left');
        $column_NomeCurso = new TDataGridColumn('NomeCurso', 'Curso', 'left');
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left');
        $column_Etapa = new TDataGridColumn('Etapa', 'Etapa', 'center');
        $column_Identificacao = new TDataGridColumn('Identificacao', 'Identificacao', 'center');
        $column_Periodo = new TDataGridColumn('Periodo', 'Período', 'center');
        $column_CodTurmaetapa = new TDataGridColumn('CodTurmaetapa', 'CodTurmaetapa', 'left');
        $column_CodComposto = new TDataGridColumn('CodComposto', 'CodComposto', 'left');
        //$column_Ano = new TDataGridColumn('Ano', 'Ano', 'left');
        //$column_Semestre = new TDataGridColumn('Semestre', 'Semestre', 'left');
        //$column_NomeEntidade = new TDataGridColumn('NomeEntidade', 'Nomeentidade', 'left');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_NomeProfessor);
        $this->datagrid->addColumn($column_NomeCurso);
        $this->datagrid->addColumn($column_NomeDisciplina);
        $this->datagrid->addColumn($column_Etapa);
        $this->datagrid->addColumn($column_Identificacao);
        $this->datagrid->addColumn($column_Periodo);
        //$this->datagrid->addColumn($column_CodTurmaetapa);
        // $this->datagrid->addColumn($column_CodComposto);        
        //$this->datagrid->addColumn($column_Ano);
        //$this->datagrid->addColumn($column_Semestre);
        //$this->datagrid->addColumn($column_NomeEntidade);

        
        // create EDIT action
        /*$action_edit = new TDataGridAction(array('', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('CodProfessor');
        $this->datagrid->addAction($action_edit);*/
        
        
        // create DELETE action
        //$action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        //$action_del->setLabel(_t('Delete'));
        //$action_del->setImage('far:trash-alt red fa-lg');
        //$action_del->setField('CodProfessor');
        //$this->datagrid->addAction($action_del);
        
        
        // creates the datagrid actions
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(FALSE);
        $action_select->setButtonClass('btn btn-default');
        $action_select->setLabel('Selecionar');
        $action_select->setImage('fas:check-circle blue');
        $action_select->setField('CodComposto');
        $this->datagrid->addAction($action_select);


        $action_papeleta = new TDataGridAction(array($this, 'onPapeleta'));
        $action_papeleta->setUseButton(FALSE);
        $action_papeleta->setButtonClass('btn btn-default');
        $action_papeleta->setLabel('Papeleta');
        $action_papeleta->setImage('fas:file-pdf red');
        $action_papeleta->setField('CodComposto');
        $this->datagrid->addAction($action_papeleta);


        // create the datagrid model
        $this->datagrid->createModel();

        
        // creates the page navigation
        //$this->pageNavigation = new TPageNavigation;
        //$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        //$this->pageNavigation->setWidth($this->datagrid->getWidth());
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ApontamentoBimestral'));
        $container->add(TPanelGroup::pack('Buscar Disciplina Por:', $this->form));
        $container->add(TPanelGroup::pack('Minhas Disciplinas', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
  
    public function onSearch()
    {
        $data = $this->form->getData();
        

        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeProfessor', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Etapa', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Identificacao', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Ano', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Semestre', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeEntidade', NULL);

        if (isset($data->NomeProfessor) AND ($data->NomeProfessor)) {
            $filter = new TFilter('NomeProfessor', 'like', "%{$data->NomeProfessor}%"); 
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeProfessor',  $filter); 
        }


        if (isset($data->NomeCurso) AND ($data->NomeCurso)) {
            $filter = new TFilter('NomeCurso', 'like', "%{$data->NomeCurso}%"); 
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso', $filter);
        }


        if (isset($data->NomeDisciplina) AND ($data->NomeDisciplina)) {
            $filter = new TFilter('NomeDisciplina', 'like', "%{$data->NomeDisciplina}%"); 
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina', $filter); 
        }


        $this->form->setData($data);
        
        TSession::setValue('VwProfessordisciplinassemestre_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    

    public function onSelect($param)
    {
       $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($this->datagrid->getItems());
        //die();
        
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->CodComposto)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

                //echo $object->CodGradeDisciplinaEtapaFrente;
                //die();

                TSession::setValue('sessao_prof', array('NomeDisciplina' => $object->NomeDisciplina,
                                                        'CodProfessor'   => $object->CodProfessor,
                                                        'key'            => $object->CodGradeDisciplinaEtapaFrente,
                                                        'Etapa'          => $object->Etapa,
                                                        'Identificacao'  => $object->Identificacao,
                                                        'CodTurmaetapa'  => $object->CodTurmaetapa,
                                                        'CodDisciplina'  => $object->CodDisciplina
                                                        )
                                   );        
            }
        }
        

      //  var_dump(TSession::getValue('sessao_prof'));
    //    die();

        TApplication::loadPage('VwAlunosnotasList');
    }

    
    public function onPapeleta($param)
    {
       $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($this->datagrid->getItems());
        //die();
        
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->CodComposto)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

                //echo $object->CodGradeDisciplinaEtapaFrente;
                //die();

                TSession::setValue('sessao_papeleta', array('NomeDisciplina' => $object->NomeDisciplina,
                                                            'Codprofessor'   => $object->Codprofessor,
                                                            'key'            => $object->CodGradeDisciplinaEtapaFrente,
                                                            'CodTurmaetapa'  => $object->CodTurmaetapa,
                                                            'Etapa'          => $object->Etapa,
                                                            'Identificacao'  => $object->Identificacao,
                                                            'NomeEntidade'   => $object->NomeEntidade,
                                                            'NomeProfessor'  => $object->NomeProfessor,
                                                            'Periodo'        => $object->Periodo,
                                                            'NomeCurso'      => $object->NomeCurso,
                                                            'CodDisciplina'  => $object->CodDisciplina
                                                        )
                                   );
            }
        }     
        

       //var_dump(TSession::getValue('sessao_papeleta'));
       //die();

        TApplication::loadPage('VwPapeletaReport');
    }


    public function onReload($param = NULL)
    {
        try
        {
             /*  
            $Unidade = $loggedUnit = TSession::getValue('userunitid');

            
             TTransaction::open('dados_fei');             
                $conn = TTransaction::get(); 
                $result = $conn->query('SELECT * from FI_DataApontamentoBimestral WHERE (getdate() BETWEEN DataInicio AND DataFim) AND CodEntidade = '.$Unidade.''); 
                //var_dump($result);
                foreach ($result as $row) 
                { 
                   $DataInicio = $row['DataInicio'];
                   $DataFim    = $row['DataFim'];
                   $Bimestre   = $row['Bimestre'];
                   $Semestre   = $row['Semestre']; 
                   $Ano        = $row['Ano'];
                } 
            TTransaction::close(); 

            TSession::setValue('sessao_bimestre', array('DataInicio' => $DataInicio,
                                                        'DataFim'    => $DataFim,
                                                        'Bimestre'   => $Bimestre,
                                                        'Semestre'   => $Semestre,
                                                        'Ano'        => $Ano
                                                        )
                                   );
        
            */

            //if ($Bimestre){

                
                $sessao_bimestre = TSession::getValue('sessao_bimestre');

                $Bimestre = $sessao_bimestre["Bimestre"];
                $Ano = $sessao_bimestre["Ano"];
                $Semestre = $sessao_bimestre["Semestre"];
                $Entidade = $sessao_bimestre["Entidade"];

                //var_dump($sessao_bimestre);


                TTransaction::open('Felabs_DB');
                
                //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
                $userid = TSession::getValue('userid');
                $user = new SystemUser($userid);
            
                $professor = $user->systemuser_codlegado;
                
                TTransaction::close();


                TTransaction::open('dados_fei');
             
                $repository = new TRepository('VwProfessordisciplinassemestre');
                $limit = 50;
                
                $util = new Util();
                $message = $util->semestre."-".$util->ano;


                // creates a criteria
                $criteria = new TCriteria;
                
                $criteria->add(new TFilter('CodProfessor', '=', $user->systemuser_codlegado));
                $criteria->add(new TFilter('Ano', '=', $Ano), TExpression::AND_OPERATOR);
                $criteria->add(new TFilter('Semestre', '=', $Semestre), TExpression::AND_OPERATOR);
                $criteria->add(new TFilter('CodEntidade', '=',$Entidade), TExpression::AND_OPERATOR);
                
                //echo $criteria->dump();
                // default order
                
                if (empty($param['order']))
                {
                    $param['order'] = 'NomeDisciplina';
                    $param['direction'] = 'asc';
                }
                
                $criteria->setProperties($param); 
                $criteria->setProperty('limit', $limit);
                

                if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeProfessor')) {
                    $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeProfessor')); 
                }


                if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')) {
                    $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso'));
                }


                if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')) {
                    $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')); 
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
                

                //$this->form->clear(); 
                TTransaction::close();
                $this->loaded = true;

            //}else{
               // echo "Não retorna";
           // }
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
