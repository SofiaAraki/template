<?php


class MeuCursoView extends TPage
{

    function __construct($param)
    {
        parent::__construct();


        if(empty($param['curso']) && empty($param['key']))
        {
            $this->onCursoForm();
        }
    }


    public function onCursoForm()
    {        
        TTransaction::open('Felabs_DB');
        
        $loggedUnit = TSession::getValue('userunitid');
        //$logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        
        TTransaction::close();
        
        $qform = new TQuickForm('input_form');
        $qform->style = 'padding:20px';
        
        $curso = new TCombo('curso');
        //$ano = new TCombo('ano');
        //$semestre = new TCombo('semestre');

        $curso->enableSearch();

        $qform->addQuickField('Curso', $curso);
        //$qform->addQuickField('Ano', $ano);
        //$qform->addQuickField('Semestre', $semestre);

        $ano = date('Y');
        
        $mes = date('m');

        if($mes < 8)
        {
            $semestreAtual = 1;
        }
        elseif($mes > 7)
        {
            $semestreAtual = 2;
        }


        TTransaction::open('dados_fei');

        $criteria = new TCriteria;

        if($user->funcao_legado == 'Aluno')
        {
            $criteria->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
        }
        else
        {
            //$criteria->add(new TFilter('Codaluno', '=', 999999999999999999999999));  //SE NAO FOR ALUNO, DEVE DAR 0 MATRÍCULAS E MOSTRAR TODOS OS CURSOS
            $criteria->add(new TFilter('Codaluno', '=', 0));
        }


        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit)); 
        $criteria->add(new TFilter('SemestreMatricula', '=', $semestreAtual)); 
        $criteria->add(new TFilter('AnoMatricula', '=', $ano)); 
        //$criteria->add(new TFilter('CodCurso', '<>', 11)); 
           
        $matriculas = VwAluno::getObjects($criteria);

        $numeroMatriculas = count($matriculas);

        $parametro = [];
   

        if($numeroMatriculas == 1) //SE TEM UMA MATRÍCULA
        {
            $parametro['curso'] = $matriculas[0]->CodCurso;
            $this->verPagina($parametro);
        }
        
        if($numeroMatriculas > 1) //SE TEM MAIS DE UMA MATRÍCULA
        {
            $items = [];

            foreach($matriculas as $matricula)
            {                
                $items[$matricula->CodCurso] = $matricula->NomeCurso;
            }

            $curso->addItems($items);


            $qform->addQuickAction('Ver Página do Curso', new TAction(array($this, 'onVerPagina')), 'fa:table');
            new TInputDialog('Por favor selecione o curso', $qform);
            
        }
        
        elseif($numeroMatriculas == 0) //SE NÃO TEM MATRÍCULAS
        {
            $criteria1 = new TCriteria;                        
            $criteria1->add(new TFilter('CodEntidade', '=', $loggedUnit)); 

            $todosCursos = FiCurso::getObjects($criteria1);


            $items = [];

            foreach($todosCursos as $todoCurso)
            {
                if($todoCurso->CodCurso != 11)
                {
                    $items[$todoCurso->CodCurso] = $todoCurso->Nome;
                }
            }

            $curso->addItems($items);

            $qform->addQuickAction('Ver Página do Curso', new TAction(array($this, 'onVerPagina')), 'fa:table');
            new TInputDialog('Por favor selecione o curso', $qform);            
        }

        TTransaction::close();
    }


    public function onVerPagina($param)
    {
        if(empty($param['curso']))
        {
            new TMessage('info', 'Insira qual curso deseja visualizar',TApplication::loadPage('MeuCursoView'));
        }
        else
        {
            $this->verPagina($param);
        }
    }


    public function verPagina($param)
    {
        TTransaction::open('Felabs_DB'); 
        
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        
        TTransaction::close(); 


        TSession::setValue('cursoid', $param['curso']);

        $cabecalho = new TElement("section");
        $cabecalho->class = "content-header";
        $cabecalho->style = "padding: 0px 0px 0px 0px";
        

        TTransaction::open('dados_fei');
        $cursoInfo = new FiCurso($param['curso']);
        TTransaction::close();

        $cabecalho->add("<h1>
        $cursoInfo->Nome
        <small>Página do Curso</small>
        </h1><br>");


        $this->form = new BootstrapFormBuilder('form_meucurso');
        $this->form->setFormTitle('Arquivos para Download');


        $this->form2 = new BootstrapFormBuilder('form_meucurso2');
        $this->form2->setFormTitle('Informativos do Curso');
        

        // creates one datagrid
        $this->datagrid = new TQuickGrid;
        
        // add the columns
        //$this->datagrid->addQuickColumn('', 'id', 'center');
        $this->datagrid->addQuickColumn('', 'nome', 'left', '100%');


        // creates one datagrid
        $this->datagrid2 = new TQuickGrid;
        
        // add the columns
        //$this->datagrid2->addQuickColumn('', 'id', 'center');
        $this->datagrid2->addQuickColumn('Título', 'nome', 'left', '100%');
        $this->datagrid2->addQuickColumn('Data da postagem', 'data_reg', 'left', '100%');
     

        $action1 = new TDataGridAction(array($this,'onDownload'));
        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setImage('fa:download green');
        
        
        $action2 = new TDataGridAction(array($this,'onInputDialog'));
        $action2->setUseButton(TRUE);
        $action2->setButtonClass('btn btn-default');
        $action2->setImage('far:clone green');


        $action3 = new TDataGridAction(array('MeuCursoForm','onEdit'));
        $action3->setUseButton(TRUE);
        $action3->setButtonClass('btn btn-default');
        $action3->setImage('fa:edit blue');        
   
        
        // add the actions
        $this->datagrid->addQuickAction('Download', $action1, 'id', '');
        $this->datagrid2->addQuickAction('Ver Informativo', $action2, 'id', '');


        if($user->funcao_legado != 'Aluno')
        {
            $this->datagrid->addQuickAction('Editar', $action3, 'id', '');
            $this->datagrid2->addQuickAction('Editar', $action3, 'id', ''); 
        }       


        $this->datagrid->setGroupColumn('arquivosTipo', '<b><i>{arquivosTipo}</i></b>');

        //$this->datagrid2->setGroupColumn('arquivosTipo', '<b><i>{arquivosTipo}</i></b>');


        if($user->funcao_legado != 'Aluno')
        {
            $this->form->addHeaderAction('Adicionar Arquivo',new TAction(array('MeuCursoForm','mostrar'),$param),'bs:plus-sign green');

            $this->form2->addHeaderAction('Adicionar Informativo',new TAction(array('MeuCursoForm','mostrarInfo'),$param),'bs:plus-sign green');
        }
        

        // creates the datagrid model
        $this->datagrid->createModel();


        // creates the datagrid model
        $this->datagrid2->createModel();


        $this->form->addContent([new BootstrapDatagridWrapper($this->datagrid)]);
        $this->form2->addContent([new BootstrapDatagridWrapper($this->datagrid2)]);
        
        $panel = new TPanelGroup('Arquivos para Download');
        $panel->add($this->form);


        $panel2 = new TPanelGroup('Informativos');
        $panel2->add($this->datagrid2);
        
        
        // wrap the page content using vertical box
        $vbox = new TVBox;

        $vbox1 = new TVBox;


        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form2);
        $vbox1->add($this->form);
            

        TTransaction::close();


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($vbox);
        $container->add($vbox1);
  
        
        // add the template to the page
        parent::add($cabecalho);
        parent::add($container);
        $this->onReload($param);
    }


    public function onInputDialog( $param )
    {      
        TApplication::loadPage('MeuCursoInformativo','mostrar',$param);        
    }


    function onReload($param)
    {
        $this->datagrid->clear();

        TTransaction::open('Felabs_DB');
        
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
  

        $criteria = new TCriteria;
        $criteria->add( new TFilter(curso_id, '=', $param['curso']));
        $criteria->setProperty('order', 'tipo, id asc');

        $meuCursoObjs = MeuCurso::getObjects($criteria);


        foreach($meuCursoObjs as $meuCursoObj) // CRIA REGISTROS DATAGRID COM INSCRIÇÕES EVENTOS MASTER
        { 
            if($meuCursoObj->tipo != 'I') //ADICIONA INFORMATIVOS NA GRID ARQUIVOS
            {
                $registros1 = new StdClass;
                $registros1->id = $meuCursoObj->id;
                $registros1->nome = $meuCursoObj->nome;
                

                if($meuCursoObj->tipo == 'A')
                {
                    $registros1->arquivosTipo = 'Atividades Complementares';
                }
                if($meuCursoObj->tipo == 'E')
                {
                    $registros1->arquivosTipo = 'Estágio Supervisionado';
                }
                if($meuCursoObj->tipo == 'P')
                {
                    $registros1->arquivosTipo = 'Projeto Pedagógico do Curso';
                }
                if($meuCursoObj->tipo == 'T')
                {
                    $registros1->arquivosTipo = 'Trabalho de Conclusão de Curso (TCC)';
                }
                if($meuCursoObj->tipo == 'G')
                {
                    $registros1->arquivosTipo = 'Grade Curricular';
                }
                if($meuCursoObj->tipo == 'C')
                {
                    $registros1->arquivosTipo = 'Calendários';
                }
                if($meuCursoObj->tipo == 'H')
                {
                    $registros1->arquivosTipo = 'Horários';
                }
                if($meuCursoObj->tipo == 'I')
                {
                    $registros1->arquivosTipo = 'Informativo';
                }
                if($meuCursoObj->tipo == 'O')
                {
                    $registros1->arquivosTipo = 'Outros';
                }

                if($registros1)
                {
                    $this->datagrid->addItem($registros1);
                }
            }

            /////////////////

            if($meuCursoObj->tipo == 'I') //ADICIONA INFORMATIVOS NA GRID INFORMATIVOS
            {
                $registros2 = new StdClass;
                $registros2->id = $meuCursoObj->id;
                $registros2->nome = $meuCursoObj->nome;
                $registros2->data_reg = TDate::date2br($meuCursoObj->data_reg);           
            

                if($registros2)
                {
                    $this->datagrid2->addItem($registros2);
                }
            }
        }

        TTransaction::close();
    }
    

    public function onDownload($param)
    {
        try
        {
            $cursoId = TSession::getValue('cursoid');
            
            $id = $param['id'];  
    
            TTransaction::open('Felabs_DB'); 
            $object = new MeuCurso($id); 
            TTransaction::close(); 

           
            if(!empty($object->filename))
            {              
                if (strtolower(substr($object->filename, -4)) == 'html')
                {
                    $win = TWindow::create( $object->filename, 0.8, 0.8 );
                    $win->add( file_get_contents( "files/meucurso/".$object->filename ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile("files/meucurso/".$object->filename);
                }
                    
                $parametros = [];
                $parametros['id'] = $param['id'];
                $parametros['key'] = $param['id'];
                $parametros['curso'] = $cursoId;

                TApplication::loadPage('MeuCursoView','verPagina',$parametros);
                TTransaction::rollback();
            }
            else
            {
                new TMessage('info', 'Esta atividade não possui anexos'); 
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
