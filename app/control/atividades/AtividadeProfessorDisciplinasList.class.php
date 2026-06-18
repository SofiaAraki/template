<?php
class AtividadeProfessorDisciplinasList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;    

    public function __construct()
    {
        parent::__construct();

        $cabecalho = new TElement("section");
        $cabecalho->class = "content-header";
        $cabecalho->style = "padding: 0px 0px 0px 0px";
        $cabecalho->add('<h1>
        Minhas Disciplinas Atuais
        <small>Meu curso</small>
        </h1><br>');
        
        // creates the form
        //$this->form = new TQuickForm('form_search_VwProfessordisciplinassemestre');
        //$this->form->class = 'tform'; // change CSS class
        //$this->form = new BootstrapFormWrapper($this->form);
        //$this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('VwProfessordisciplinassemestre');
        

        // create the form fields
        $CodDisciplina = new TEntry('CodDisciplina');
        $NomeDisciplina = new TEntry('NomeDisciplina');
        $Identificacao = new TEntry('Identificacao');
        $Periodo = new TEntry('Periodo');
        $CodTurmaetapa = new TEntry('CodTurmaetapa');
        $NomeCurso = new TEntry('NomeCurso');


        // add the fields
        //$this->form->addQuickField('Coddisciplina', $CodDisciplina,  '100%' );
        //$this->form->addQuickField('Nomedisciplina', $NomeDisciplina,  '100%' );
        //$this->form->addQuickField('Identificacao', $Identificacao,  '100%' );
        //$this->form->addQuickField('Periodo', $Periodo,  '100%' );
        //$this->form->addQuickField('Codturmaetapa', $CodTurmaetapa,  '100%' );
        //$this->form->addQuickField('Nomecurso', $NomeCurso,  '100%' );

        
        // keep the form filled during navigation with session data
        //$this->form->setData( TSession::getValue('VwProfessordisciplinassemestre_filter_data') );
        
        // add the search form actions
        //$btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction(_t('New'),  new TAction(array('VwProfessordisciplinassemestreForm', 'onEdit')), 'bs:plus-sign green');
        
        
        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);


       	$this->datagrid->addQuickColumn('Cód. Disciplina', 'CodDisciplina', 'center', '20%');
       	$this->datagrid->addQuickColumn('Disciplina', 'NomeDisciplina', 'left', '20%');
       	$this->datagrid->addQuickColumn('Turma', 'Identificacao', 'center', '20%');
       	$this->datagrid->addQuickColumn('Período', 'Periodo', 'center', '20%');
       	$this->datagrid->addQuickColumn('Cód. Turma', 'CodTurmaetapa', 'center', '20%');
       	$this->datagrid->addQuickColumn('Curso', 'NomeCurso', 'center', '20%');
        
        
        // create EDIT action
        /*$action_edit = new TDataGridAction(array('VwProfessordisciplinassemestreForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('Codprofessor');
        $this->datagrid->addAction($action_edit);
       */ 
       
       
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onCarregaDados'));
        $action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
        //$action_del->setLabel('Abrir Disciplina');
        $action_del->setImage('fa:folder red fa-lg');
        $action_del->setField('CodDisciplina');


        // add the actions
        $this->datagrid->addQuickAction('Abrir Disciplina', $action_del, 'CodTurmaetapa', '');
     
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());


        $panel = new TPanelGroup('Disciplinas atuais');
        $panel->add($this->datagrid);

        $ano = date('Y');
        $mes = date('m');
        
        if($mes < 8)
        {
          $semestre = '1º Semestre';
        }
        else
        {
          $semestre = '2º Semestre';
        }


        $panel->addFooter("$semestre de $ano");
        

        // vertical box container
        $container = new TVBox;        
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        //$container->add(TPanelGroup::pack('Title', $this->form));
        //$container->add(TPanelGroup::pack('Conteúdos Disponíveis', $this->datagrid));

        
        //parent::add($cabecalho);
        parent::add($container);
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('VwProfessordisciplinassemestreList_filter_CodDisciplina', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Identificacao', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_Periodo', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_CodTurmaetapa', NULL);
        TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso', NULL);

        if (isset($data->CodDisciplina) AND ($data->CodDisciplina)) {
            $filter = new TFilter('CodDisciplina', 'like', "%{$data->CodDisciplina}%");
            TSession::setValue('VwProfessordisciplinassemestreList_filter_CodDisciplina', $filter); 
        }


        if (isset($data->NomeDisciplina) AND ($data->NomeDisciplina)) {
            $filter = new TFilter('NomeDisciplina', 'like', "%{$data->NomeDisciplina}%"); 
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina', $filter);
        }


        if (isset($data->Identificacao) AND ($data->Identificacao)) {
            $filter = new TFilter('Identificacao', 'like', "%{$data->Identificacao}%");
            TSession::setValue('VwProfessordisciplinassemestreList_filter_Identificacao', $filter); 
        }


        if (isset($data->Periodo) AND ($data->Periodo)) {
            $filter = new TFilter('Periodo', 'like', "%{$data->Periodo}%"); 
            TSession::setValue('VwProfessordisciplinassemestreList_filter_Periodo', $filter);
        }


        if (isset($data->CodTurmaetapa) AND ($data->CodTurmaetapa)) {
            $filter = new TFilter('CodTurmaetapa', 'like', "%{$data->CodTurmaetapa}%");
            TSession::setValue('VwProfessordisciplinassemestreList_filter_CodTurmaetapa', $filter); 
        }


        if (isset($data->NomeCurso) AND ($data->NomeCurso)) {
            $filter = new TFilter('NomeCurso', 'like', "%{$data->NomeCurso}%");
            TSession::setValue('VwProfessordisciplinassemestreList_filter_NomeCurso', $filter);
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
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
            
            //$loggedProf = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            $loggedUnitProf = TSession::getValue('userunitid');

            TTransaction::close();


            $ano = date('Y');
            $mes = date('m');
            
            if($mes < 8)
            {
              $semestre = 1;
            }
            else
            {
              $semestre = 2;
            }


            TTransaction::open('dados_fei');
            

            $repository = new TRepository('VwProfessordisciplinassemestre');
            $limit = 20;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodProfessor', '=', $user->systemuser_codlegado));
            $criteria->add(new TFilter('Ano', '=', $ano), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Semestre', '=', $semestre), TExpression::AND_OPERATOR);   
            $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf), TExpression::AND_OPERATOR);
            

            if (empty($param['order']))
            {
                $param['order'] = 'Codprofessor';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_CodDisciplina')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_CodDisciplina')); 
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeDisciplina')); 
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_Identificacao')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_Identificacao')); 
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_Periodo')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_Periodo')); 
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_CodTurmaetapa')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_CodTurmaetapa')); 
            }


            if (TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')) {
                $criteria->add(TSession::getValue('VwProfessordisciplinassemestreList_filter_NomeCurso')); 
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
                    
                    if($object->Periodo == 'N')
                    {
                        $object->Periodo = 'Noturno';
                    }
                    if($object->Periodo == 'M')
                    {
                        $object->Periodo = 'Matutino';
                    }
                    if($object->Periodo == 'I')
                    {
                        $object->Periodo = 'Integral';
                    }
        
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
    
    public function onCarregaDados($param)
    {
        TSession::setValue('sessao_prof', array('coddisciplina' => $param['CodDisciplina'],'codturmaetapa'  => $param['CodTurmaetapa']));

        TApplication::loadPage('AtividadeList','onReload'); //PÁGINA DA DISCIPLINA
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
