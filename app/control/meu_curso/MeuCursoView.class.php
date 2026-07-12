<?php
class MeuCursoView extends TPage
{
    protected $form;
    protected $form2;
    private $datagrid;
    private $datagrid2;

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
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        
        TTransaction::close();
        
        $qform = new TQuickForm('input_form');
        $qform->style = 'padding:20px';
        
        $curso = new TCombo('curso');
        $curso->enableSearch();

        $qform->addQuickField('Curso', $curso);
       
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
            $criteria->add(new TFilter('Codaluno', '=', 0));
        }

        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit)); 
        $criteria->add(new TFilter('SemestreMatricula', '=', $semestreAtual)); 
        $criteria->add(new TFilter('AnoMatricula', '=', $ano)); 
           
        $matriculas = VwAluno::getObjects($criteria);
        $numeroMatriculas = count($matriculas);
        $parametro = [];

        if($numeroMatriculas == 1) 
        {
            $parametro['curso'] = $matriculas[0]->CodCurso;
            $this->verPagina($parametro);
        }
        
        if($numeroMatriculas > 1) 
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
        elseif($numeroMatriculas == 0) 
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
            new TMessage('info', 'Insira qual curso deseja visualizar', TApplication::loadPage('MeuCursoView'));
        }
        else
        {
            $this->verPagina($param);
        }
    }

    public function verPagina($param)
    {
        TTransaction::open('Felabs_DB'); 
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        TTransaction::close(); 

        TSession::setValue('cursoid', $param['curso']);

        TTransaction::open('dados_fei');
        $cursoInfo = new FiCurso($param['curso']);
        TTransaction::close();

        // --- TÍTULO DA PÁGINA ---
        $cabecalho = new TElement('div');
        $cabecalho->style = "margin-bottom: 25px; padding-left: 5px;";
        $cabecalho->add("<h2 style='font-weight: 300; margin-bottom: 5px;'> <i class='fa fa-graduation-cap'></i> {$cursoInfo->Nome}</h2>");

        // --- DATAGRID 1: INFORMATIVOS DO CURSO ---
        $this->datagrid2 = new TQuickGrid;
        $this->datagrid2->style = 'width: 100%; border-collapse: collapse;';
        $this->datagrid2->addQuickColumn('Título', 'nome', 'left', '80%');
        $this->datagrid2->addQuickColumn('Postado em', 'data_reg', 'center', '20%');

        $action2 = new TDataGridAction(array($this, 'onInputDialog'));
        $action2->setUseButton(TRUE);
        $action2->setButtonClass('btn btn-sm btn-primary');
        $action2->setImage('fa:eye white');
        $this->datagrid2->addQuickAction('Visualizar', $action2, 'id', '');

        // --- DATAGRID 2: ARQUIVOS PARA DOWNLOAD ---
        $this->datagrid = new TQuickGrid;
        $this->datagrid->style = 'width: 100%; border-collapse: collapse;';
        $this->datagrid->addQuickColumn('', 'nome', 'left', '100%');

        $action1 = new TDataGridAction(array($this, 'onDownload'));
        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-sm btn-success');
        $action1->setImage('fa:cloud-download-alt white');
        $this->datagrid->addQuickAction('Baixar', $action1, 'id', '');

        // Ações exclusivas de edição da Secretaria
        $action3 = new TDataGridAction(array('MeuCursoForm', 'onEdit'));
        $action3->setUseButton(TRUE);
        $action3->setButtonClass('btn btn-sm btn-warning');
        $action3->setImage('fa:edit white');        
        
        if($user->funcao_legado != 'Aluno')
        {
            $this->datagrid->addQuickAction('Editar', $action3, 'id', '');
            $this->datagrid2->addQuickAction('Editar', $action3, 'id', ''); 
        }       

        $this->datagrid->setGroupColumn('arquivosTipo', "<div><i class='fa fa-folder-open'></i> {arquivosTipo}</div>");

        // Instanciação dos formulários/painéis estruturais mantendo os cabeçalhos de ação da secretaria
        $this->form = new BootstrapFormBuilder('form_meucurso');
        $this->form2 = new BootstrapFormBuilder('form_meucurso2');

        if($user->funcao_legado != 'Aluno')
        {
            $this->form->addHeaderAction('Adicionar Arquivo', new TAction(array('MeuCursoForm', 'mostrar'), $param), 'fa:plus-circle green');
            $this->form2->addHeaderAction('Adicionar Informativo', new TAction(array('MeuCursoForm', 'mostrarInfo'), $param), 'fa:plus-circle green');
        }

        $this->datagrid->createModel();
        $this->datagrid2->createModel();

        // Envolve as datagrids no wrapper Bootstrap
        $this->form->addContent([new BootstrapDatagridWrapper($this->datagrid)]);
        $this->form2->addContent([new BootstrapDatagridWrapper($this->datagrid2)]);

        $panel_informativos = new TPanelGroup('Informativos do Curso');
        $panel_informativos->add($this->form2);
        $panel_informativos->style = 'margin-bottom: 25px; width: 100%;';

        $panel_arquivos = new TPanelGroup('Arquivos para Download');
        $panel_arquivos->add($this->form);
        $panel_arquivos->style = 'width: 100%;';

        // --- CONTAINER VERTICAL SEM DIVISÃO DE COLUNAS ---
        $mainContainer = new TElement('div');
        $mainContainer->style = 'width: 100%;';
        $mainContainer->add($panel_informativos);
        $mainContainer->add($panel_arquivos);

        TTransaction::close();

        parent::add($cabecalho);
        parent::add($mainContainer);
        
        $this->onReload($param);
    }

    public function onInputDialog($param)
    {      
        TApplication::loadPage('MeuCursoInformativo', 'mostrar', $param);        
    }

    public function onReload($param)
    {
        $this->datagrid->clear();
        $this->datagrid2->clear();

        TTransaction::open('Felabs_DB');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('curso_id', '=', $param['curso']));
        $criteria->setProperty('order', 'tipo, id asc');

        $meuCursoObjs = MeuCurso::getObjects($criteria);

        foreach($meuCursoObjs as $meuCursoObj) 
        { 
            if($meuCursoObj->tipo != 'I') 
            {
                $registros1 = new StdClass;
                $registros1->id = $meuCursoObj->id;
                $registros1->nome = $meuCursoObj->nome;
                

                if($meuCursoObj->tipo == 'A')  $registros1->arquivosTipo = 'Atividades Complementares';
                if($meuCursoObj->tipo == 'E')  $registros1->arquivosTipo = 'Estágio Supervisionado';
                if($meuCursoObj->tipo == 'P')  $registros1->arquivosTipo = 'Projeto Pedagógico do Curso';
                if($meuCursoObj->tipo == 'T')  $registros1->arquivosTipo = 'Trabalho de Conclusão de Curso (TCC)';
                if($meuCursoObj->tipo == 'G')  $registros1->arquivosTipo = 'Grade Curricular';
                if($meuCursoObj->tipo == 'C')  $registros1->arquivosTipo = 'Calendários';
                if($meuCursoObj->tipo == 'H')  $registros1->arquivosTipo = 'Horários';
                if($meuCursoObj->tipo == 'O')  $registros1->arquivosTipo = 'Outros';

                if(isset($registros1->arquivosTipo)) 
                {
                    $this->datagrid->addItem($registros1);
                }
            }

            if($meuCursoObj->tipo == 'I') 
            {
                $registros2 = new StdClass;
                $registros2->id = $meuCursoObj->id;
                $registros2->nome = $meuCursoObj->nome;
                $registros2->data_reg = TDate::date2br($meuCursoObj->data_reg);           
            
                $this->datagrid2->addItem($registros2);
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
                    $win = TWindow::create($object->filename, 0.8, 0.8);
                    $win->add(file_get_contents("files/meucurso/".$object->filename));
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

                TApplication::loadPage('MeuCursoView', 'verPagina', $parametros);
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