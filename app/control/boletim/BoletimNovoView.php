<?php
/**

 * @author     Pamella Scapim

 */
class BoletimNovoView extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        $link1 = new TActionLink('Voltar', new TAction(array('BoletimNovoList','onReload')), 'far:list blue');
        $link1->class = 'btn btn-success';
        $hbox_actions = THBox::pack($link1);    

        try
        {
            $sessao_boletim = TSession::getValue('sessao_boletim');




            $CodMatriculaEtapa = $sessao_boletim["CodMatriculaEtapa"];
            $IdentificacaoMatricula = $sessao_boletim["IdentificacaoMatricula"];
            $MediaPI = $sessao_boletim["MediaPI"];
            $NotaNI = $sessao_boletim["NotaNI"]; 
            $ConfirmacaoMatricula = $sessao_boletim["ConfirmacaoMatricula"];
            $SituacaoMatricula = $sessao_boletim["SituacaoMatricula"];

            TTransaction::open('Felabs_DB');
            $loggedUnit = TSession::getValue('userunitid'); //UNIDADE ESCOLHIDA NO MOMENTO DO LOGIN
            TTransaction::close();

            if (($loggedUnit == 3) AND ($ConfirmacaoMatricula == 'N') AND ($SituacaoMatricula == 'FR' )) 
            {
                new TMessage('warning', 'Não foi possível localizar seu Boletim, por favor procure o Departamento Financeiro da Instituição!');
                die();
      
            }
            if (($loggedUnit == 2) AND ($ConfirmacaoMatricula == 'N') AND ($SituacaoMatricula == 'FR' )) 
            {
                new TMessage('warning', 'Não foi possível localizar seu Boletim, por favor procure o Departamento Financeiro da Instituição!');
                die();
      
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

                //CNSC - Técnico
                if($loggedUnit == 1)
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
        
                $anoSemBread = 'BOLETIM ESCOLAR - '.$IdentificacaoMatricula;
                $avisoBoletim = new TLabel("<i>Caro (a) aluno (a), os dados constantes neste boletim não são oficiais, portanto, estão sujeitos à alteração. No caso de
                dados irregulares (faltantes ou divergentes), procure a Secretaria, com uma cópia do mesmo, para a devida regularização. Ao imprimir, escolha o formato 
                paisagem (horizontal).
                <br>O boletim apresenta todas as médias bimestrais e o registro de algumas frequências. A totalidade da frequência bimestral será registrada/atualizada
                posteriormente, considerando a migração do novo sistema acadêmico em implementação.</i>");
                
        
                $panel = TPanelGroup::pack($anoSemBread, $this->datagrid);
                $panel->getBody()->{'style'} = "overflow-x:auto;";
        
        
                $container->add($panel);
                $container->add($hbox_actions);
                
              //  $container->getBody()->{'style'} = "overflow-x:auto;";
              //  $container->add(TPanelGroup::pack('', $this->datagrid)); 
         
                parent::add($container);
                parent::add($avisoBoletim);
            
                //Boletim logic starts here
                
                TTransaction::open('dados_fei');
                //Trasação para verificar se tem matrícula no semestre (falta parametrizar dinamicamente)
                $criteria1 = new TCriteria;
                $criteria1->add( new TFilter('CodMatriculaEtapa', '=', $CodMatriculaEtapa));
                //$criteria1->add( new TFilter('CodTurmaetapa', '=', $CodTurmaetapa));
        
                $criteria2 = new TCriteria;
                $criteria2->add( new TFilter('Dispensa', '<>', 'S'), TExpression::OR_OPERATOR);
                $criteria2->add( new TFilter('Dispensa', 'is', NULL), TExpression::OR_OPERATOR);
                
                $criteria_matricula = new TCriteria;
                $criteria_matricula->add($criteria1, TExpression::AND_OPERATOR);
                $criteria_matricula->add($criteria2, TExpression::AND_OPERATOR);
                $criteria_matricula->setProperty('order', 'Ordem');
        
                //echo $criteria_matricula->dump();
                
                $repository = new TRepository('VwFiDisciplinasATADDP');
                $disciplinasATADDP = $repository->load($criteria_matricula);
        
                     
        
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
                            $NotasFaltasFrente = FiNotasfaltasFrente::where('CodMatriculaEtapa', '=', $CodMatriculaEtapa)->where('CodDisciplina', '=', $CodDisciplina)->load();
                            $NotasFaltas = FiNotasfaltas::where('CodMatriculaEtapa', '=', $CodMatriculaEtapa)->where('CodDisciplina', '=', $CodDisciplina)->load();

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
        
                            foreach ($NotasFaltasFrente as $NotaFaltaFrente)
                            {
                                //Para Verificar qual nota é
                                $Avaliacao  = $NotaFaltaFrente->Avaliacao;
                            
                                //Condicional separa as notas do 1Bim(1), 2Bim(2) e Exame(3)
                                if ($Avaliacao == 1){
                                    $Nota1Bim  = $NotaFaltaFrente->Nota1;
                                    $Falta1Bim = $NotaFaltaFrente->Faltas;
                                }
                                if ($Avaliacao == 2){
                                    $Nota2Bim  = $NotaFaltaFrente->Nota1;
                                    $Falta2Bim = $NotaFaltaFrente->Faltas;
                                }
                                
                            }

                            foreach ($NotasFaltas as $NotaFalta)
                            {
                                //Para Verificar qual nota é
                                $Avaliacao  = $NotaFalta->Avaliacao;
                            
                                //Condicional separa as notas do 1Bim(1), 2Bim(2) e Exame(3)

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

                //CNSC - Técnico
                if ($loggedUnit == 1)
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
        
       
        
            }
               catch (Exception $e)
               {
                   new TMessage('error', $e->getMessage());
               }

}
}