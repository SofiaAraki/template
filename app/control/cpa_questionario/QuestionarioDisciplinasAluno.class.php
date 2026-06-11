<?php
/**
 * BoletimView
 * @author  Felipe S. Teixeira
 * @author  Fernando Stuck
 * @since   22/20/2018
 * @version 1.1
 */
class QuestionarioDisciplinasAluno extends TPage
{
    private $datagrid; // listing
    private $loaded;    
    /**
     * Class constructor
     */
    public function __construct($data)
    {
        parent::__construct();

        if(TSession::getValue('userunitid') == 1)
        {
            new TMessage('info','Funcionalidade não disponível para esta unidade');
            die;
        }

        if(empty(TSession::getValue('periodoid')) && $data['periodoid'])
        {
            TSession::setValue('periodoid',$data['periodoid']);
        }



        if(empty($data['curso'])){
            $this->onBoletimForm();
            
        }

    }

    public static function onQuestionarioView($param)
    {

        $parametros = [];
        $parametros['id'] = TSession::getValue('periodoid');
        $parametros['key'] = TSession::getValue('periodoid');

        TSession::setValue('CodDisciplinaChave',$param['CodDisciplinaChave']);
        
        //var_dump($parametros);
        TApplication::loadPage('QuestionarioView','mostrar',$parametros);
    }
        
        



    public function mostraBoletim($data)
    {
        TTransaction::open('Felabs_DB');
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        TTransaction::close();
        try 
        { 

            TTransaction::open('dados_fei');
            //Trasação para verificar se tem matrícula no semestre (falta parametrizar dinamicamente)
            $criteria_matricula = new TCriteria;
            $criteria_matricula->add( new TFilter(CodAluno, '=', $logged->systemuser_codlegado));
            $criteria_matricula->add( new TFilter(AnoMatricula, '=', $data['ano']));
            $criteria_matricula->add( new TFilter(CodCurso, '=', $data['curso']));
            $criteria_matricula->add( new TFilter(SemestreMatricula, '=', $data['semestre']));
            $criteria_matricula->add( new TFilter(ConfirmacaoMatricula, '=', 'S'));
            $criteria_matricula->add( new TFilter(SituacaoMatricula, '=', 'FR'));                                     

            $repository = new TRepository('VwAlunoMatriculaEtapa');
            $matricula = $repository->load($criteria_matricula);

            if($data['curso'] && $data['ano'] && $data['semestre'])
            {
               $this->onReload($matricula);
            }
            
            TTransaction::close(); // close transaction
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    
    }
    

    public function onBoletimForm()
    {
        TTransaction::open('Felabs_DB');
        $loggedAluno = TSession::getValue('userunitid');
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        
        $periodoInfo = new QuestionarioPeriodo(TSession::getValue('periodoid'));




        TTransaction::close();
        $qform = new TQuickForm('input_form');
        $qform->style = 'padding:20px';
        
        $curso = new TCombo('curso');
        $ano = new THidden('ano');
        $semestre = new THidden('semestre');

        $qform->addQuickField('Curso', $curso);
        $qform->addQuickField('Ano', $ano);
        $qform->addQuickField('Semestre', $semestre);



       

        $ano->setValue($periodoInfo->ano);
        $semestre->setValue($periodoInfo->semestre);

      //  $ano->setValue($anos);
      //  $semestre->setValue($semestreAtual);

        TTransaction::open('dados_fei');

        $criteria = new TCriteria;                        
        $criteria->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
        $criteria->add(new TFilter('CodEntidade', '=', $loggedAluno));
        $criteria->setProperty('order', 'AnoMatricula');
        $criteria->setProperty('direction','DESC');            
 

        $alunoView= new TRepository('VwAluno');
        $alunoSemestre = $alunoView->load($criteria);

        $cursos = [];
        $codCursos = [];

        foreach($alunoSemestre as $alunoCurso){

            $cursos[$alunoCurso->CodCurso] = $alunoCurso->NomeCurso; 
            $codCursos[] = $alunoCurso->CodCurso;
        }


        $curso->addItems($cursos);

        if($codCursos)
        {
            $curso->setValue($codCursos[0]);
        }


        TTransaction::close();



        $qform->addQuickAction('Próximo', new TAction(array($this, 'mostraBoletim')), 'fa:chevron-circle-right blue');
        
        // show the input dialog
        new TInputDialog('Selecione seu curso', $qform);
    }





    //Função de carregamento da página
    public function onReload($param)
    {
        if($param)//Se a consulta acima retornar algo, é direcionado ao boletim, caso contrário mostra mensagem de boletim não disponível.
        {
            $this->onBoletimView($param);
            //var_dump($param);
        }
        else
        {
            parent::add(new TAlert('warning', 'Aluno Não Matriculado'));
        }
    }
    //Função para mostrar o Boletim
    public function onBoletimView($param)
    {
        foreach ($param as $matricula) {
            $CodCurso           = $matricula->CodCurso;
            $NomeAluno          = $matricula->NomeAluno;
            $CodMatriculaEtapa  = $matricula->CodMatriculaEtapa;
            $MediaPI            = $matricula->MediaPI;  
            $NotaNI             = $matricula->NotaNI;
        }
        // creates a Datagrid
        $this->datagrid            = new TDataGrid;
        $this->datagrid            = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style     = 'width: 100%';
      //  $this->datagrid->getBody()->{'style'} = "overflow-x:auto;";
    //    $this->datagrid->datatable = 'true';
        $this->datagrid->setGroupColumn('disciplinasTipo', '<b><i>{disciplinasTipo}</i></b>');
        
        // creates columns of DataGrid
        $column_CodDisciplinaChave  = new TDataGridColumn('CodDisciplinaChave', 'Cód Disciplina chave', 'center');
        $column_CodDisciplina  = new TDataGridColumn('CodDisciplina', 'Cód Disciplina', 'center');
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', '', 'left');
        $column_NomeProfessor = new TDataGridColumn('NomeProfessor', 'Nome do Professor', 'left');
        $column_Nota1Bim       = new TDataGridColumn('Nota1Bim', '1 Bi', 'center');
        $column_Falta1Bim      = new TDataGridColumn('Falta1Bim', 'Faltas', 'center');
        $column_Nota2Bim       = new TDataGridColumn('Nota2Bim', '2 Bi', 'center');
        $column_Falta2Bim      = new TDataGridColumn('Falta2Bim', 'Faltas', 'center');
        $column_Pi             = new TDataGridColumn('Pi', 'PI', 'center');
        $column_Ni             = new TDataGridColumn('Ni', 'NI', 'center');
        $column_Frequencia     = new TDataGridColumn('Frequencia', '% Freq', 'center');
        $column_Media          = new TDataGridColumn('Media', 'Média', 'center');
        $column_NotaExame      = new TDataGridColumn('NotaExame', 'Exame', 'center');
        $column_MediaSem       = new TDataGridColumn('MediaSem', 'Média Final', 'center');
        $column_Resultado      = new TDataGridColumn('Resultado', 'Resultado', 'center');

        // add the columns to the DataGridCodDisciplinaChave
   //     $this->datagrid->addColumn($column_CodDisciplinaChave);
    //    $this->datagrid->addColumn($column_CodDisciplina);
        $this->datagrid->addColumn($column_NomeDisciplina);
      //  $this->datagrid->addColumn($column_NomeProfessor);
      //  $this->datagrid->addColumn($column_Falta1Bim);
      //  $this->datagrid->addColumn($column_Nota2Bim);
     //   $this->datagrid->addColumn($column_Falta2Bim);

        TTransaction::open('Felabs_DB');
        $loggedUnit = TSession::getValue('userunitid'); //UNIDADE ESCOLHIDA NO MOMENTO DO LOGIN
        TTransaction::close();

        if($loggedUnit != 3)
        {
      //      $this->datagrid->addColumn($column_Pi);
     //       $this->datagrid->addColumn($column_Ni);
        }



     //   $this->datagrid->addColumn($column_Frequencia);
      //  $this->datagrid->addColumn($column_Media);
     //   $this->datagrid->addColumn($column_NotaExame);
     //   $this->datagrid->addColumn($column_MediaSem);
    //    $this->datagrid->addColumn($column_Resultado);  


        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onQuestionarioView'));
        $action1->setLabel('Responder');
        $action1->setImage('far:check-circle green');
        $action1->setField('CodDisciplinaChave');
        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action1);









        // create the datagrid model
        $this->datagrid->createModel();
       
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';

        //$anoSemBread = $NomeAluno.' - BOLETIM ESCOLAR '.$param[0]->AnoMatricula.' / '.$param[0]->SemestreMatricula;
      //  $avisoBoletim = new TLabel("<i>Caro (a) aluno (a), os dados constantes neste boletim não são oficiais, portanto, estão sujeitos à alteração. <br>No caso de dados irregulares (faltantes ou divergentes), procure a Secretaria, com uma cópia do mesmo, para a devida regularização. <br>Ao imprimir, escolha o formato paisagem (horizontal).</i>");
        

        $panel = TPanelGroup::pack('Atenção, pois deverão ser respondidos os questionários de todas as disciplinas do semestre cursado.', $this->datagrid);
        $panel->getBody()->{'style'} = "overflow-x:auto;";


        $container->add($panel);
      //  $container->getBody()->{'style'} = "overflow-x:auto;";
      //  $container->add(TPanelGroup::pack('', $this->datagrid)); 
 
        parent::add($container);
       // parent::add($avisoBoletim);

        //Boletim logic starts here
        
        TTransaction::open('dados_fei');
        //Trasação para verificar se tem matrícula no semestre (falta parametrizar dinamicamente)
        $criteria1 = new TCriteria;
        $criteria1->add( new TFilter(CodMatriculaEtapa, '=', $CodMatriculaEtapa));

        $criteria2 = new TCriteria;
        $criteria2->add( new TFilter(Dispensa, '<>', 'S'), TExpression::OR_OPERATOR);
        $criteria2->add( new TFilter(Dispensa, 'is', NULL), TExpression::OR_OPERATOR);
        
        $criteria_matricula = new TCriteria;
        $criteria_matricula->add($criteria1, TExpression::AND_OPERATOR);
        $criteria_matricula->add($criteria2, TExpression::AND_OPERATOR);
        $criteria_matricula->setProperty('order', 'Ordem');
        
        $repository = new TRepository('VwFiDisciplinasATADDP');
        $disciplinasATADDP = $repository->load($criteria_matricula);
      
        TTransaction::close(); // close transaction

        //Consulta seleciona as disciplinas AT(atuais), AD(adaptação) e (DP) que o aluno está matriculado no semestre. A view VwFiDisciplinasATADDP une as 3 tabelas.
        $obj = new stdClass;//Objeto para armazenar linhas de resultado do datagrid

        foreach ($disciplinasATADDP as $disciplina)
        {   //Campos retornados da view VwFiDisciplinasATADDP. Utilizados para parametrizar a consulta na tabela FI_NotasFaltas
   /*         $CodMatriculaEtapa  = $disciplina->CodMatriculaEtapa;
            $CodDisciplina      = $disciplina->CodDisciplina;          
            
            //Consulta seleciona as notas em FI_NotasFaltas da disciplina atual do laço.
            $NotasFaltas = FiNotasFaltas::where('CodMatriculaEtapa', '=', $CodMatriculaEtapa)->where('CodDisciplina', '=', $CodDisciplina)->load();
            
            //Limpa Variáveis
            $Nota1Bim  = '';
            $Falta1Bim = '';
            $Nota2Bim  = '';
            $Falta2Bim = '';
            $NotaExame = '';

            foreach ($NotasFaltas as $NotaFalta)
            {
                //Para Verificar qual nota é
                $Avaliacao  = $NotaFalta->Avaliacao;

                //Condicional separa as notas do 1Bim(1), 2Bim(2) e Exame(3)
                if ($Avaliacao == 1){
                    $Nota1Bim  = $NotaFalta->Nota1;
                    $Falta1Bim = $NotaFalta->Faltas;
                }
                if ($Avaliacao == 2){
                    $Nota2Bim  = $NotaFalta->Nota1;
                    $Falta2Bim = $NotaFalta->Faltas;
                }
                if ($Avaliacao == 3){
                    $NotaExame = $NotaFalta->Nota1;
                }
            }
            */
            //Montagem do Objeto do Datagrid. Campos selecionados da View VwFiDisciplinasATADDP CodDisciplinaChave
            $obj->CodDisciplinaChave  = $disciplina->CodDisciplinaChave;
            $obj->CodDisciplina  = $disciplina->CodDisciplina;
            $obj->NomeDisciplina = $disciplina->NomeDisciplina;
          //  $obj->NomeProfessor     = $disciplina->CodProfessor;
            
     /*       //Montagem do Objeto do Datagrid. Campos selecionados da Tabela FI_NotasFaltas
            $obj->Nota1Bim  = $Nota1Bim;
            $obj->Nota2Bim  = $Nota2Bim;
            $obj->NotaExame = $NotaExame;
            $obj->Falta1Bim = $Falta1Bim;
            $obj->Falta2Bim = $Falta2Bim;
            $obj->Pi        = $MediaPI;
            $obj->Ni        = $NotaNI;
            $obj->Media     = $disciplina->MediaSem;
            $obj->MediaSem  = $disciplina->MediaSem;
            $obj->Resultado = $disciplina->Resultado;
*/
            if($obj->Resultado == 'A'){
            $obj->Resultado = '<span class="label label-success">APROVADO</span>';
            }
            if($obj->Resultado == 'R'){
            $obj->Resultado = '<span class="label label-danger">REPROVADO</span>';
            }
            if($obj->Resultado == 'E'){
            $obj->Resultado = '<span class="label label-warning">EXAME</span>';
            }


            if($disciplina->Tipo == 'AT'){
                $obj->disciplinasTipo = 'Atuais';
            }
            if($disciplina->Tipo == 'AD'){
                $obj->disciplinasTipo = 'Adaptações';
            }
            if($disciplina->Tipo == 'DP'){
                $obj->disciplinasTipo = 'Dependências';
            }
            
            // adiciona objeto ao datagrid
            $this->datagrid->addItem($obj);

        }
    }



    public function displayColumn( $object ) //VERIFICA CONDIÇÕES PARA HABILITAR BOTÃO DE RESPONDER QUESTIONÁRIO
    {
        

        TTransaction::open('Felabs_DB');
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $periodoInfo = new QuestionarioPeriodo(TSession::getValue('periodoid'));

        $hoje = date('Y-m-d H:i:s');


        if($hoje < $periodoInfo->termino && $hoje > $periodoInfo->inicio)
        {
            $validador = 'OK';
        }


        $criteria = new TCriteria;                        
        $criteria->add(new TFilter('cod_disciplina', '=', $object->CodDisciplina)); 
        $criteria->add(new TFilter('system_user_id', '=', $logged->id)); 


        $questionarioRespostas = QuestionarioResposta::getObjects($criteria);

        TTransaction::close();

        if(empty($questionarioRespostas) && $validador)
        {
            return TRUE;
        }

        return FALSE;

        
    }


    public function mostrar()
    {
        
    }

}

