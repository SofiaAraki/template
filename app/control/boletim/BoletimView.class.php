<?php
/**
 * BoletimView
 * @author  Felipe S. Teixeira
 * @author  Fernando Stuck
 * @since   22/20/2018
 * @version 1.1
 */
class BoletimView extends TPage
{
    private $datagrid; // listing
    private $loaded;    
    /**
     * Class constructor
     */
    public function __construct($data)
    {
        parent::__construct();

        /*if(TSession::getValue('userunitid') == 1)
        {
            new TMessage('info','Funcionalidade não disponível para esta unidade');
            die;
        }
        */


        //Verifica se existe contrato pendente de assinatura
        TTransaction::open('Felabs_DB');
        
        $user_id = TSession::getValue('userid');
        
        $user = new SystemUser($user_id);
                
        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
        
        $criteria2 = new TCriteria;
        $criteria2->add(new TFilter('StatusContrato', '=', 'Pendente de Validação Pelo Aluno'), TExpression::OR_OPERATOR);
        $criteria2->add(new TFilter('StatusContrato', '=', 'Assinado Pelo Aluno / Envio de Documento Pendente'), TExpression::OR_OPERATOR);
        
        $criteria = new TCriteria;
        $criteria->add($criteria1);
        $criteria->add($criteria2);
                
        $contratos_pendentes = ContratoDadosAluno::getObjects($criteria);
        
        if($contratos_pendentes)
        {
            $action = new TAction(['ContratoDadosAlunoList', 'onReload']);
            
            new TMessage('info', 'Antes de prosseguir, é necessário assinar digitalmente o(s) contrato(s) de prestação de serviços pendente(s)', $action); 
        }
        else
        {
            if(empty($data['curso']))
            {

                $this->onBoletimForm();            

            }
        }
        
        TTransaction::close();

    }
        
        



    public function mostraBoletim($data)
    {
        TTransaction::open('Felabs_DB');
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $loggedUnidade = TSession::getValue('userunitid');
        TTransaction::close();
        try 
        { 

        	TTransaction::open('dados_fei');
            //Transação para verificar se tem matrícula no semestre (falta parametrizar dinamicamente)
        	$criteria_matricula_facul = new TCriteria;
        	$criteria_matricula_facul->add( new TFilter(CodAluno, '=', $logged->systemuser_codlegado));
        	$criteria_matricula_facul->add( new TFilter(AnoMatricula, '=', $data['ano']));
        	$criteria_matricula_facul->add( new TFilter(CodCurso, '=', $data['curso']));
        	$criteria_matricula_facul->add( new TFilter(SemestreMatricula, '=', $data['semestre']));
        	$criteria_matricula_facul->add( new TFilter(ConfirmacaoMatricula, '=', 'S'));
            $criteria_matricula_facul->add( new TFilter(SituacaoMatricula, '=', 'FR'));
            
            $criteria_matricula_colegio = new TCriteria;
        	$criteria_matricula_colegio->add( new TFilter(CodAluno, '=', $logged->systemuser_codlegado));
        	$criteria_matricula_colegio->add( new TFilter(CodEntidade, '=', 12));
            $criteria_matricula_colegio->add( new TFilter(AnoMatricula, '=', $data['ano']));
        	$criteria_matricula_colegio->add( new TFilter(CodCurso, '=', $data['curso']));
        	//$criteria_matricula_colegio->add( new TFilter(SemestreMatricula, '=', $data['semestre']));
        	//$criteria_matricula_colegio->add( new TFilter(ConfirmacaoMatricula, '=', 'S'));
            $criteria_matricula_colegio->add( new TFilter(SituacaoMatricula, '=', 'FR'));  
            
          /*  $criteria_matricula_anglo = new TCriteria;
        	$criteria_matricula_anglo->add( new TFilter(CodAluno, '=', $logged->systemuser_codlegado));
        	$criteria_matricula_anglo->add( new TFilter(CodEntidade, '=', 8));
            $criteria_matricula_anglo->add( new TFilter(AnoMatricula, '=', $data['ano']));
        	$criteria_matricula_anglo->add( new TFilter(CodCurso, '=', $data['curso']));
            $criteria_matricula_anglo->add( new TFilter(SituacaoMatricula, '=', 'FR'));
            //$criteria_matricula_anglo->add( new TFilter(ConfirmacaoMatricula, '=', 'S'));*/
  
            

            $criteria_matricula = new TCriteria;
            $criteria_matricula->add($criteria_matricula_facul, TExpression::OR_OPERATOR);
            $criteria_matricula->add($criteria_matricula_colegio, TExpression::OR_OPERATOR);
            //$criteria_matricula->add($criteria_matricula_anglo, TExpression::OR_OPERATOR);

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
        TTransaction::close();
        $qform = new TQuickForm('input_form');
        $qform->style = 'padding:20px';
        
        $curso = new TCombo('curso');
        $ano = new TCombo('ano');
        $semestre = new TCombo('semestre');

        $qform->addQuickField('Curso', $curso);
        $qform->addQuickField('Ano', $ano);
        $qform->addQuickField('Semestre', $semestre);

        $anos = date('Y');

        $mes = date('m');

        if($mes < 7)
        {
            $semestreAtual = 1;
        }
        elseif($mes > 6)
        {
            $semestreAtual = 2;
        }

        $items=[];
        $items[$anos] = $anos;
        $items[$anos-1] = $anos-1;
        $items[$anos-2] = $anos-2;
        $items[$anos-3] = $anos-3;
        $items[$anos-4] = $anos-4;

        $sem=[];
        $sem[1] = '1º Semestre';
        $sem[2] = '2º Semestre';


        $ano->addItems($items);
        $semestre->addItems($sem);

        $ano->setValue($anos);
        $semestre->setValue($semestreAtual);

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



        $qform->addQuickAction('Ver Boletim', new TAction(array($this, 'mostraBoletim')), 'fa:table');
        
        // show the input dialog
        new TInputDialog('Preencha os campos abaixo', $qform);
    }





    //Função de carregamento da página
	public function onReload($param)
	{
	    if($param)//Se a consulta acima retornar algo, é direcionado ao boletim, caso contrário mostra mensagem de boletim não disponível.
	    {
        	$this->onBoletimView($param);
         //   var_dump($param);
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
        $this->datagrid->disableHtmlConversion();
        
        // creates columns of DataGrid
		$column_CodDisciplina  = new TDataGridColumn('CodDisciplina', 'Cód.', 'center');
        $column_NomeDisciplina = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'left');
        $column_Nota1Bim       = new TDataGridColumn('Nota1Bim', '1º Bim', 'center');
        $column_Falta1Bim      = new TDataGridColumn('Falta1Bim', 'F', 'center');
        $column_Nota2Bim       = new TDataGridColumn('Nota2Bim', '2º Bim', 'center');
        $column_Falta2Bim      = new TDataGridColumn('Falta2Bim', 'F', 'center');
        $column_Nota3Bim       = new TDataGridColumn('Nota3Bim', '3º Bim', 'center');
        $column_Falta3Bim      = new TDataGridColumn('Falta3Bim', 'F', 'center');
        $column_Nota4Bim       = new TDataGridColumn('Nota4Bim', '4º Bim', 'center');
        $column_Falta4Bim      = new TDataGridColumn('Falta4Bim', 'F', 'center');
        $column_Pi             = new TDataGridColumn('Pi', 'PI', 'center');
        $column_Ni             = new TDataGridColumn('Ni', 'NI', 'center');
        $column_Frequencia     = new TDataGridColumn('Frequencia', '% Freq', 'center');
        $column_Media          = new TDataGridColumn('Media', 'Média', 'center');
        $column_NotaExame      = new TDataGridColumn('NotaExame', 'Exame', 'center');
        $column_MediaSem       = new TDataGridColumn('MediaSem', 'Média Final', 'center');
        $column_Resultado      = new TDataGridColumn('Resultado', 'Resultado', 'center');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_CodDisciplina);
        $this->datagrid->addColumn($column_NomeDisciplina);
        $this->datagrid->addColumn($column_Nota1Bim);
        $this->datagrid->addColumn($column_Falta1Bim);
        $this->datagrid->addColumn($column_Nota2Bim);
        $this->datagrid->addColumn($column_Falta2Bim);

        TTransaction::open('Felabs_DB');
        $loggedUnit = TSession::getValue('userunitid'); //UNIDADE ESCOLHIDA NO MOMENTO DO LOGIN
        TTransaction::close();

        if($loggedUnit == 2)
        {
            $this->datagrid->addColumn($column_Pi);
            $this->datagrid->addColumn($column_Ni);
        }

        if($loggedUnit == 10)
        {
            $this->datagrid->addColumn($column_Pi);
            $this->datagrid->addColumn($column_Ni);
        }
        
        //NEAD
        if($loggedUnit == 6)
        {
            $this->datagrid->addColumn($column_Pi);
        }

        //CONNEXT
        if($loggedUnit == 12)
        {
            $this->datagrid->addColumn($column_Nota3Bim);
            $this->datagrid->addColumn($column_Falta3Bim);
            $this->datagrid->addColumn($column_Nota4Bim);
            $this->datagrid->addColumn($column_Falta4Bim);
        }

       /* if($loggedUnit == 8)
        {
            $this->datagrid->addColumn($column_Nota3Bim);
            $this->datagrid->addColumn($column_Falta3Bim);
            $this->datagrid->addColumn($column_Nota4Bim);
            $this->datagrid->addColumn($column_Falta4Bim);
        }*/

        $this->datagrid->addColumn($column_Frequencia);
        $this->datagrid->addColumn($column_Media);
        $this->datagrid->addColumn($column_NotaExame);
        $this->datagrid->addColumn($column_MediaSem);
        $this->datagrid->addColumn($column_Resultado);  
        // create the datagrid model
        $this->datagrid->createModel();
       
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';

        $anoSemBread = $NomeAluno.' - BOLETIM ESCOLAR '.$param[0]->AnoMatricula.' / '.$param[0]->SemestreMatricula;
        $avisoBoletim = new TLabel("<i>Caro (a) aluno (a), os dados constantes neste boletim não são oficiais, portanto, estão sujeitos à alteração. <br>No caso de dados irregulares (faltantes ou divergentes), procure a Secretaria, com uma cópia do mesmo, para a devida regularização. <br>Ao imprimir, escolha o formato paisagem (horizontal).</i>");
        

        $panel = TPanelGroup::pack($anoSemBread, $this->datagrid);
        $panel->getBody()->{'style'} = "overflow-x:auto;";


        $container->add($panel);
      //  $container->getBody()->{'style'} = "overflow-x:auto;";
      //  $container->add(TPanelGroup::pack('', $this->datagrid)); 
 
        parent::add($container);
        parent::add($avisoBoletim);

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

        //FFCL
        if ($loggedUnit == 2)
        {
        
            foreach ($disciplinasATADDP as $disciplina)
            {   //Campos retornados da view VwFiDisciplinasATADDP. Utilizados para parametrizar a consulta na tabela FI_NotasFaltas
                $CodMatriculaEtapa  = $disciplina->CodMatriculaEtapa;
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
                //Montagem do Objeto do Datagrid. Campos selecionados da View VwFiDisciplinasATADDP
                $obj->CodDisciplina  = $disciplina->CodDisciplina;
                $obj->NomeDisciplina = $disciplina->NomeDisciplina;
                $obj->Frequencia     = $disciplina->Frequencia;
                
                //Montagem do Objeto do Datagrid. Campos selecionados da Tabela FI_NotasFaltas
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
                $this->datagrid->disableHtmlConversion();

            }
        }
        
        //NEAD
        if ($loggedUnit == 6)
        {
        
            foreach ($disciplinasATADDP as $disciplina)
            {   //Campos retornados da view VwFiDisciplinasATADDP. Utilizados para parametrizar a consulta na tabela FI_NotasFaltas
                $CodMatriculaEtapa  = $disciplina->CodMatriculaEtapa;
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
                //Montagem do Objeto do Datagrid. Campos selecionados da View VwFiDisciplinasATADDP
                $obj->CodDisciplina  = $disciplina->CodDisciplina;
                $obj->NomeDisciplina = $disciplina->NomeDisciplina;
                $obj->Frequencia     = $disciplina->Frequencia;
                
                //Montagem do Objeto do Datagrid. Campos selecionados da Tabela FI_NotasFaltas
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
                $this->datagrid->disableHtmlConversion();

            }
        }

        //FAFRAM
        if ($loggedUnit == 3)
        {

            foreach ($disciplinasATADDP as $disciplina)
            {   //Campos retornados da view VwFiDisciplinasATADDP. Utilizados para parametrizar a consulta na tabela FI_NotasFaltas
                $CodMatriculaEtapa  = $disciplina->CodMatriculaEtapa;
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
                //Montagem do Objeto do Datagrid. Campos selecionados da View VwFiDisciplinasATADDP
                $obj->CodDisciplina  = $disciplina->CodDisciplina;
                $obj->NomeDisciplina = $disciplina->NomeDisciplina;
                $obj->Frequencia     = $disciplina->Frequencia;
                
                //Montagem do Objeto do Datagrid. Campos selecionados da Tabela FI_NotasFaltas
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
                $this->datagrid->disableHtmlConversion();

            }
        }

        //FAJOB
        if ($loggedUnit == 10)
        {

            foreach ($disciplinasATADDP as $disciplina)
            {   //Campos retornados da view VwFiDisciplinasATADDP. Utilizados para parametrizar a consulta na tabela FI_NotasFaltas
                $CodMatriculaEtapa  = $disciplina->CodMatriculaEtapa;
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
                //Montagem do Objeto do Datagrid. Campos selecionados da View VwFiDisciplinasATADDP
                $obj->CodDisciplina  = $disciplina->CodDisciplina;
                $obj->NomeDisciplina = $disciplina->NomeDisciplina;
                $obj->Frequencia     = $disciplina->Frequencia;
                
                //Montagem do Objeto do Datagrid. Campos selecionados da Tabela FI_NotasFaltas
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
                $this->datagrid->disableHtmlConversion();

            }
        }

        //CONNEXT
        if ($loggedUnit == 12)
        {

            foreach ($disciplinasATADDP as $disciplina)
                {   //Campos retornados da view VwFiDisciplinasATADDP. Utilizados para parametrizar a consulta na tabela FI_NotasFaltas
                    $CodMatriculaEtapa  = $disciplina->CodMatriculaEtapa;
                    $CodDisciplina      = $disciplina->CodDisciplina;          
                    
                    //Consulta seleciona as notas em FI_NotasFaltas da disciplina atual do laço.
                    $NotasFaltas = FiNotasFaltas::where('CodMatriculaEtapa', '=', $CodMatriculaEtapa)->where('CodDisciplina', '=', $CodDisciplina)->load();
                    
                    //Limpa Variáveis
                    $Nota1Bim  = '';
                    $Falta1Bim = '';
                    $Nota2Bim  = '';
                    $Falta2Bim = '';
                    $Nota3Bim  = '';
                    $Falta3Bim = '';
                    $Nota4Bim  = '';
                    $Falta4Bim = '';
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
                            $Nota3Bim  = $NotaFalta->Nota1;
                            $Falta3Bim = $NotaFalta->Faltas;
                        }
                        if ($Avaliacao == 4){
                            $Nota4Bim  = $NotaFalta->Nota1;
                            $Falta4Bim = $NotaFalta->Faltas;
                        }
                        if ($Avaliacao == 5){
                            $NotaExame = $NotaFalta->Nota1;
                        }
                    }
                    //Montagem do Objeto do Datagrid. Campos selecionados da View VwFiDisciplinasATADDP
                    $obj->CodDisciplina  = $disciplina->CodDisciplina;
                    $obj->NomeDisciplina = $disciplina->NomeDisciplina;
                    $obj->Frequencia     = $disciplina->Frequencia;
                    
                    //Montagem do Objeto do Datagrid. Campos selecionados da Tabela FI_NotasFaltas
                    $obj->Nota1Bim  = $Nota1Bim;
                    $obj->Nota2Bim  = $Nota2Bim;
                    $obj->Nota3Bim  = $Nota3Bim;
                    $obj->Nota4Bim  = $Nota4Bim;
                    $obj->NotaExame = $NotaExame;
                    $obj->Falta1Bim = $Falta1Bim;
                    $obj->Falta2Bim = $Falta2Bim;
                    $obj->Falta3Bim = $Falta3Bim;
                    $obj->Falta4Bim = $Falta4Bim;
                    //$obj->Pi        = $MediaPI;
                    //$obj->Ni        = $NotaNI;
                    //$obj->Media     = $disciplina->MediaSem;
                    //$obj->MediaSem  = $disciplina->MediaSem;
                    //$obj->Resultado = $disciplina->Resultado;
                
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
                    $this->datagrid->disableHtmlConversion();

               }
        }

        /*//ANGLO
        if ($loggedUnit == 8)
        {

            foreach ($disciplinasATADDP as $disciplina)
            {   //Campos retornados da view VwFiDisciplinasATADDP. Utilizados para parametrizar a consulta na tabela FI_NotasFaltas
                    $CodMatriculaEtapa  = $disciplina->CodMatriculaEtapa;
                    $CodDisciplina      = $disciplina->CodDisciplina;          
                    
                    //Consulta seleciona as notas em FI_NotasFaltas da disciplina atual do laço.
                    $NotasFaltas = FiNotasFaltas::where('CodMatriculaEtapa', '=', $CodMatriculaEtapa)->where('CodDisciplina', '=', $CodDisciplina)->load();
                    
                    
                    //Limpa Variáveis
                    $Nota1Bim  = '';
                    $Falta1Bim = '';
                    $Nota2Bim  = '';
                    $Falta2Bim = '';
                    $Nota3Bim  = '';
                    $Falta3Bim = '';
                    $Nota4Bim  = '';
                    $Falta4Bim = '';
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
                            $Nota3Bim  = $NotaFalta->Nota1;
                            $Falta3Bim = $NotaFalta->Faltas;
                        }
                        if ($Avaliacao == 4){
                            $Nota4Bim  = $NotaFalta->Nota1;
                            $Falta4Bim = $NotaFalta->Faltas;
                        }
                        if ($Avaliacao == 5){
                            $NotaExame = $NotaFalta->Nota1;
                        }
                    }
                    //Montagem do Objeto do Datagrid. Campos selecionados da View VwFiDisciplinasATADDP
                    $obj->CodDisciplina  = $disciplina->CodDisciplina;
                    $obj->NomeDisciplina = $disciplina->NomeDisciplina;
                    $obj->Frequencia     = $disciplina->Frequencia;
                    
                    //Montagem do Objeto do Datagrid. Campos selecionados da Tabela FI_NotasFaltas
                    $obj->Nota1Bim  = $Nota1Bim;
                    $obj->Nota2Bim  = $Nota2Bim;
                    $obj->Nota3Bim  = $Nota3Bim;
                    $obj->Nota4Bim  = $Nota4Bim;
                    $obj->NotaExame = $NotaExame;
                    $obj->Falta1Bim = $Falta1Bim;
                    $obj->Falta2Bim = $Falta2Bim;
                    $obj->Falta3Bim = $Falta3Bim;
                    $obj->Falta4Bim = $Falta4Bim;
                    //$obj->Pi        = $MediaPI;
                    //$obj->Ni        = $NotaNI;
                    //$obj->Media     = $disciplina->MediaSem;
                    //$obj->MediaSem  = $disciplina->MediaSem;
                    //$obj->Resultado = $disciplina->Resultado;


                
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
                    $this->datagrid->disableHtmlConversion();

            }
        }*/



	}

}