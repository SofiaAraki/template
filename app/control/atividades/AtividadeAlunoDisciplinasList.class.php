<?php
class AtividadeAlunoDisciplinasList extends TPage
{
    private $datagrid;

    function __construct()
    {
        parent::__construct();

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
        
        // O SEGREDO AQUI: Força o container principal a espalhar-se por 100% da viewport
        $vbox->style = 'width: 100%; display: block;'; 
        
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);

        //add the template to the page
        parent::add($vbox);
        
        // Carrega os dados pela primeira vez se não for uma ação de postback
        $this->onReload();
    }

    public function onReload()
    {
        $this->datagrid->clear();

        TTransaction::open('Felabs_DB');
        $Unidade = TSession::getValue('userunitid');
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
        $criteria_matricula->add( new TFilter('CodAluno', '=', $user->systemuser_codlegado));
        $criteria_matricula->add( new TFilter('AnoMatricula', '=', $ano));
        $criteria_matricula->add( new TFilter('CodEntidade', '=', $Unidade)); 
        $criteria_matricula->add( new TFilter('SemestreMatricula', '=', $semestre));
      
        $alunoMatriculas = VwAlunoMatriculaEtapa::getObjects($criteria_matricula); 

        if(empty($alunoMatriculas))
        {
            new TMessage('error', 'Indisponível. Por favor procure o atendimento.');
            TTransaction::close();
        }
        else
        {
          $criteria2 = new TCriteria;
          $criteria2->add( new TFilter('CodMatriculaEtapa', '=', $alunoMatriculas[0]->CodMatriculaEtapa));

          $alunoDisciplinas = VwAlunosnotas::getObjects($criteria2); 

          foreach($alunoDisciplinas as $alunoDisciplina)
          {
              $nomeDisciplina = new FiDisciplina($alunoDisciplina->CodDisciplina); 

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
        TApplication::loadPage('AtividadeList','onReload'); 
    }
}