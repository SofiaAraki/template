<?php

class AtividadeAlunoDisciplinasList extends TPage
{

    function __construct()
    {
        parent::__construct();

        $cabecalho = new TElement("section");
        $cabecalho->class = "content-header";
        $cabecalho->style = "padding: 0px 0px 0px 0px";
        $cabecalho->add('<h1>
        Minhas Disciplinas Atuais
        <small>Meu curso</small>
        </h1><br>');
        

        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        
        
        // add the columns
        $this->datagrid->addQuickColumn('Cód. Disciplina',    'id',    'center', '20%');
        $this->datagrid->addQuickColumn('Nome',    'nome',    'left', '60%');
        $this->datagrid->addQuickColumn('Cód. Turma',    'codturmaetapa',    'left', '20%');


        $action1 = new TDataGridAction(array($this,'onCarregaDados'));
        $action1->setField('id');
        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setImage('far:folder-open red');

        
        // add the actions
        $this->datagrid->addQuickAction('Abrir Disciplina', $action1, 'codturmaetapa', '');


        // creates the datagrid model
        $this->datagrid->createModel();
        
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

        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);

        

        TTransaction::close();
        
        //add the template to the page
        parent::add($vbox);
        $this->onReload();
    }



    public function onReload()
    {
        $this->datagrid->clear();

        TTransaction::open('Felabs_DB');
        
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $Unidade = $loggedUnit = TSession::getValue('userunitid');
        
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);        
        
        TTransaction::close();


        TTransaction::open('dados_fei');

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


        $criteria_matricula = new TCriteria;
        //$criteria_matricula->add( new TFilter('CodAluno', '=', $logged->systemuser_codlegado));
        $criteria_matricula->add( new TFilter('CodAluno', '=', $user->systemuser_codlegado));
        $criteria_matricula->add( new TFilter('AnoMatricula', '=', $ano));
        $criteria_matricula->add( new TFilter('CodEntidade', '=', $Unidade)); //SISTEMAS 21
        $criteria_matricula->add( new TFilter('SemestreMatricula', '=', $semestre));
      //$criteria_matricula->add( new TFilter('ConfirmacaoMatricula', '=', 'S'));
      //$criteria_matricula->add( new TFilter('SituacaoMatricula', '=', 'FR'));                                     



        $alunoMatriculas = VwAlunoMatriculaEtapa::getObjects($criteria_matricula); //PEGA MATRÍCULAS DO ALUNO NO ANO E SEMESTRE ATUAL

        ////////////////////////////////////////



        if(empty($alunoMatriculas))
        {
            new TMessage('error', 'Indisponível. Por favor procure o atendimento.');
        }
        else
        {

          $criteria2 = new TCriteria;
          $criteria2->add( new TFilter(CodMatriculaEtapa, '=', $alunoMatriculas[0]->CodMatriculaEtapa));

          $alunoDisciplinas = VwAlunosnotas::getObjects($criteria2); //PEGA DISCIPLINAS QUE O ALUNO ESTÁ CURSANDO NO SEMESTRE ATUAL

          $disciplinas = [];

          foreach($alunoDisciplinas as $alunoDisciplina)
          {
              $nomeDisciplina = new FiDisciplina($alunoDisciplina->CodDisciplina); //PEGA O NOME DA DISCIPLINA PELO CÓDIGO

              $disc = new StdClass;
              $disc->id     = $alunoDisciplina->CodDisciplina;
              $disc->nome     = $nomeDisciplina->NomeOficial;
              $disc->codturmaetapa = $alunoDisciplina->CodTurmaetapa;
  
              $this->datagrid->addItem($disc);
          }

          TTransaction::close();
        }
    }


    public function mostrar()
    {
     
    }


    public function onCarregaDados($param)
    {
        TSession::setValue('sessao_prof', array('coddisciplina' => $param['id'],'codturmaetapa'  => $param['key']));
        
          TApplication::loadPage('AtividadeList','onReload'); //PÁGINA DA DISCIPLINA
    }
}
