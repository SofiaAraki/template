<?php

class CarteiraAluno extends TPage
{
    public function __construct()
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');

        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $loggedUnit = TSession::getValue('userunitid');
       
        TTransaction::close();

        TTransaction::open('dados_fei');

        $anoAtual = date('Y');
        $mesAtual = date('m');

        if($mesAtual < 8)
        {
            $semestreAtual = 1;
        }
        else
        {
            $semestreAtual = 2;
        }

            $criteria_tecnico_enf = new TCriteria;                        
            $criteria_tecnico_enf->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
            $criteria_tecnico_enf->add(new TFilter('AnoMatricula', '=', 2021)); 
            $criteria_tecnico_enf->add(new TFilter('SemestreMatricula', '=', 2)); 
            $criteria_tecnico_enf->add(new TFilter('CodEntidade', '=', 1));
            $criteria_tecnico_enf->add(new TFilter('CodCurso', '=', 47));
     
            $criteria_tecnico = new TCriteria;                        
            $criteria_tecnico->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
            $criteria_tecnico->add(new TFilter('AnoMatricula', '=', $anoAtual)); 
            $criteria_tecnico->add(new TFilter('CodEntidade', '=', 1)); 


            //Técnico Connext
            $cod_curso = [121, 130, 131];
            
            $criteria_tec_connext1 = new TCriteria;                        
            $criteria_tec_connext1->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
            $criteria_tec_connext1->add(new TFilter('AnoMatricula', '=', 2022)); 
            $criteria_tec_connext1->add(new TFilter('SemestreMatricula', '=', 2)); 
            $criteria_tec_connext1->add(new TFilter('CodEntidade', '=', 12));
            $criteria_tec_connext1->add(new TFilter('CodCurso', 'IN', $cod_curso)); 
            
            $criteria_tec_connext2 = new TCriteria;                        
            $criteria_tec_connext2->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
            $criteria_tec_connext2->add(new TFilter('AnoMatricula', '=', 2023)); 
            $criteria_tec_connext2->add(new TFilter('SemestreMatricula', '=', 1)); 
            $criteria_tec_connext2->add(new TFilter('CodEntidade', '=', 12));
            $criteria_tec_connext2->add(new TFilter('CodCurso', 'IN', $cod_curso)); 
            //Técnico Connext
            
            $criteria_connext = new TCriteria;                        
            $criteria_connext->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
            $criteria_connext->add(new TFilter('AnoMatricula', '=', 2023)); 
            $criteria_connext->add(new TFilter('CodEntidade', '=', 12)); 
            
            $criteria_faculdade = new TCriteria;                        
            $criteria_faculdade->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
            $criteria_faculdade->add(new TFilter('AnoMatricula', '=', $anoAtual)); 
            $criteria_faculdade->add(new TFilter('SemestreMatricula', '=', $semestreAtual));

            $criteria = new TCriteria;     
            $criteria->add($criteria_tecnico_enf, TExpression::OR_OPERATOR); 
            $criteria->add($criteria_tecnico, TExpression::OR_OPERATOR); 
            $criteria->add($criteria_tec_connext1, TExpression::OR_OPERATOR);
            $criteria->add($criteria_tec_connext2, TExpression::OR_OPERATOR); 
            $criteria->add($criteria_connext, TExpression::OR_OPERATOR); 
            $criteria->add($criteria_faculdade, TExpression::OR_OPERATOR); 
            //echo $criteria->dump();

        $alunoView = new TRepository('VwAluno');
        $alunoSemestre = $alunoView->load($criteria);

       // var_dump($alunoSemestre[0]->CodMatriculaEtapa);
       //die();
    

        $alerta = new TAlert('error', "Prezado(a), procure o atendimento para validar sua carteirinha com a marca d'água da FE. Caso sua carterinha não esteja aparecendo logo abaixo, procure o atendimento. Imprimir no formato paisagem (horizontal).");

        $link = new TElement('a');
        $link->target = 'newwindow';
        $link->class = 'btn btn-default';
        $link->href = "http://www.servicos.feituverava.com.br/aluno/carteirinha.asp?user={$logged->systemuser_codlegado}&amp;matricula={$alunoSemestre[0]->CodMatriculaEtapa}";
        
        $link->add('Gerar carteirinha');


        parent::add($alerta);
        parent::add('<br>');
        parent::add($link);

        TTransaction::close();
    }
}
