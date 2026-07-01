<?php
/**
 * @author     PAMELLA SCAPIM
 */
class ControleFrequencia extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formDatagrid;
    protected $postAction;
    
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('dados_fei_t');                         
        parent::setActiveRecord('VwAlunosCompleto');           
        parent::setDefaultOrder('Nome', 'asc');                
        parent::setTransformer( array($this, 'onBeforeLoad') );
        parent::setLimit(250);

        /////////////****INICIO***** Carrega os alunos conforme o dia da semana e a aula escolhida pelo professor na página anterior *********//////////////////
        $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse'); //Traz as infos da aula escolhida pelo prof.
        $CodTurmaetapa      = $sessao_diarioclasse["CodTurmaetapa"];
        $Identificacao      = $sessao_diarioclasse["Identificacao"];
        $NomeDisciplina     = $sessao_diarioclasse["NomeDisciplina"];
        $NomeProfessor      = $sessao_diarioclasse["NomeProfessor"];
        $NomeDiaSemana      = $sessao_diarioclasse["NomeDiaSemana"];
        $CodDisciplina      = $sessao_diarioclasse["CodDisciplina"];
        $Ciclo              = $sessao_diarioclasse["Etapa"];
        $Codprofessor       = $sessao_diarioclasse["Codprofessor"];
        $CodGradeDisciplinaEtapaFrente = $sessao_diarioclasse["CodGradeDisciplinaEtapaFrente"];
        
        $sessao_data_escolhida = TSession::getValue('data_escolhida');
        $data_escolhida = $sessao_data_escolhida["data_escolhida"];

            $criteria = new TCriteria();
            $criteria->add(new TFilter('Situacao','=','FR'), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('CodTurmaetapa','=', $CodTurmaetapa), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('CodDisciplina','=', $CodDisciplina), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('ConfirmacaoMatricula','=', 'S'), TExpression::AND_OPERATOR);

        parent::setCriteria($criteria);
        /////////////****FIM***** Carrega os alunos conforme o dia da semana e a aula escolhida pelo professor na página anterior *********//////////////////

        // creates the form, with a table inside
        $this->form = new BootstrapFormBuilder('form_search_Diario');
        $this->form->setFormTitle(('Diário de Classe - Controle de Frequência'));
        
        // create the form fields
        //$DiaAula = new TDate('DataAula');
        
        
        // add a row for the filter field
        //$this->form->addFields( [new TLabel($CodTurmaetapa)] );
        $row =  $this->form->addFields( [new TLabel('Disciplina:'),'<b>'.$NomeDisciplina.'</b>'],[new TLabel('Prof:'),$NomeProfessor]  );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4' ];
        $row =$this->form->addFields( [new TLabel('Turma:'), '<b>'.$Ciclo . 'º Ciclo </b>'], [new TLabel('Dia:'),$data_escolhida] );
        $row->layout = ['col-sm-4', 'col-sm-3', 'col-sm-5' ];
 
        $this->form->setData( TSession::getValue('Diario_filter_data') );
        $this->form->addActionLink( 'Listar Disciplinas',  new TAction(['HorarioAulasList', 'onReload']), 'fa:arrow-left blue');
        // create the datagrid form wrapper
        $this->formDatagrid = new TForm('datagrid_form');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '90%';
        $this->formDatagrid->add($this->datagrid);

        $Nome = $this->datagrid->addColumn( new TDataGridColumn('Nome', 'Aluno(a)', 'left') );
        $TipoDis = $this->datagrid->addColumn( new TDataGridColumn('TipoDis', ' ', 'left') );
 
         // Busca as aulas e cria as colunas de checkbox dinamicamente
        TTransaction::open('dados_fei');
        $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse'); 
        $verifica_frente_disciplina = $sessao_diarioclasse["CodGradeDisciplinaEtapaFrente"];
        $verifica_disciplina = $sessao_diarioclasse["CodDisciplina"];
        $verifica_professor = $sessao_diarioclasse["Codprofessor"];
        $verifica_turma = $sessao_diarioclasse["CodTurmaetapa"];
        $verifica_periodo = $sessao_diarioclasse["Periodo"];
 
        $php_dia = date('w', strtotime($data_escolhida)); // 0 (dom) a 6 (sab)
        $diasemana_numero = $php_dia + 1; // 1 (dom) a 7 (sab)

        // Critério para buscar as aulas
        $criteriaDia = new TCriteria;
        $criteriaDia->add(new TFilter('Codprofessor', '=', $verifica_professor));
        $criteriaDia->add(new TFilter('CodDisciplina', '=', $verifica_disciplina));
        $criteriaDia->add(new TFilter('CodTurmaetapa', '=', $verifica_turma));
        $criteriaDia->add(new TFilter('CodGradeDisciplinaEtapa_Frente', '=', $verifica_frente_disciplina));
        $criteriaDia->add(new TFilter('Ano', '=', '2026'));
        $criteriaDia->add(new TFilter('Período', '=', $verifica_periodo));
        $criteriaDia->add(new TFilter('DiaSemana', '=', $diasemana_numero));
        
        $repository = new TRepository('VwHorarioprofessor'); 
        $Aulas = $repository->load($criteriaDia);

        // Adiciona colunas dinamicamente com base na quantidade de NumeroOrdemAula
        foreach ($Aulas as $aula) {
            $colName = 'check_' . $aula->NumeroOrdemAula;
            $checkColumn = new TDataGridColumn($colName, 'Freq. ' . $aula->NumeroOrdemAula . 'ª Aula', 'center');
            $this->datagrid->addColumn($checkColumn);
        }
        TTransaction::close();

        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setLimit(100);
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        
        $this->postAction = new TAction(array($this, 'onPost'));
        $post = new TButton('post');
        $post->setAction($this->postAction);
        $post->setImage('far:check-circle');
        $post->class = 'btn btn-success btn-lg';
        $post->setLabel('Salvar');
                
        $this->formDatagrid->addField($post);
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
       // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel = TPanelGroup::pack('', $this->formDatagrid));
        $panel->getBody()->style = 'overflow-x: auto';
        $container->add($post);
        
        parent::add($container);
    }
    
    public function onReload( $param = NULL )
    {
        // update the post action parameters to pass
        // offset, limit, page and other info in
        // order to preserve the pagination after post
        //$this->postAction->setParameters($param); // important!
       
        return parent::onReload( $param );

        

    }
    

    /**
     * Transform the objects before load them into the datagrid
     */
    public function onBeforeLoad($objects)
    {
        $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse'); 
        $verifica_frente_disciplina = $sessao_diarioclasse["CodGradeDisciplinaEtapaFrente"];
        $verifica_disciplina = $sessao_diarioclasse["CodDisciplina"];
        $verifica_professor = $sessao_diarioclasse["Codprofessor"];
        $verifica_turma = $sessao_diarioclasse["CodTurmaetapa"];
        $verifica_periodo = $sessao_diarioclasse["Periodo"];

        $sessao_data_escolhida = TSession::getValue('data_escolhida');
        $data_escolhida = $sessao_data_escolhida["data_escolhida"];
        
        $php_dia = date('w', strtotime($data_escolhida)); // 0 (dom) a 6 (sab)
        $diasemana_numero = $php_dia + 1; // 1 (dom) a 7 (sab)

        TTransaction::open('dados_fei');

        // Busca as aulas para associar os checkboxes
        $criteriaDia = new TCriteria;
        $criteriaDia->add(new TFilter('Codprofessor', '=', $verifica_professor));
        $criteriaDia->add(new TFilter('CodDisciplina', '=', $verifica_disciplina));
        $criteriaDia->add(new TFilter('CodTurmaetapa', '=', $verifica_turma));
        $criteriaDia->add(new TFilter('CodGradeDisciplinaEtapa_Frente', '=', $verifica_frente_disciplina));
        $criteriaDia->add(new TFilter('Período', '=', $verifica_periodo));
        $criteriaDia->add(new TFilter('Ano', '=', '2026'));
        $criteriaDia->add(new TFilter('DiaSemana', '=', $diasemana_numero));

        //echo $criteriaDia->dump();
        $repository = new TRepository('VwHorarioprofessor'); 
        $Aulas = $repository->load($criteriaDia);

        // Itera sobre os alunos e adiciona um checkbox para cada aula
        foreach ($objects as $object) {
            foreach ($Aulas as $aula) {
                // Cria o checkbox dinamicamente para cada aula e aluno
                $checkName = 'check_' . $object->CodMatriculaEtapa . '_' . $object->CodTurmaetapa . '_' . $verifica_frente_disciplina. '_' . $verifica_disciplina . '_' . $verifica_periodo . '_' . $aula->NumeroOrdemAula;
                
                     // Verifica na tabela FiFrqdiariaDisciplinas se a frequência existe
                $FrqdiariaDisciplinaExistente = VwFrequenciadiaria  ::where('CodGradeDisciplinaEtapa_Frente', '=', $verifica_frente_disciplina)
                                                                    ->where('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa)
                                                                    ->where('CodTurmaetapa', '=', $object->CodTurmaetapa)
                                                                    ->where('CodDisciplina', '=', $verifica_disciplina)
                                                                    ->where('Periodo', 'like', $verifica_periodo)
                                                                    ->where('Aula', '=', $aula->NumeroOrdemAula)
                                                                    ->where('DataAula', '=', $data_escolhida)
                                                                    ->first();

                                                                    $check = new TCheckButton($checkName);
                                                                    $check->setIndexValue('P');
                                                                    
                 // Verifica se a consulta retornou algo
                 if ($FrqdiariaDisciplinaExistente) {
                     // Verifica se a frequência foi marcada como 'P' ou 'F'
                     if ($FrqdiariaDisciplinaExistente->Freq == 'P') {
                         $check->setValue('P'); // Marcado
                         $check->setIndexValue('P');
                     } else {
                         $check->setValue(''); // Desmarcado
                         $check->setIndexValue('F');
                     }
                 } else {
                     // Caso a consulta não retorne nenhum registro, defina o valor padrão do checkbox
                     $check->setValue('P'); // Deixe desmarcado como padrão ou outro valor desejado
                     $check->setIndexValue('P'); // Valor padrão no caso da consulta retornar vazia
                 }
                 
                 // Adiciona o checkbox ao formulário
                 if (!$this->form->getField($checkName)) {
                    $this->form->addField($check);
                }
                 
                 // Armazena o checkbox para ser exibido na datagrid
                 $object->{'check_' . $aula->NumeroOrdemAula} = $check;
        }

        
    }
    TTransaction::close();
}
        


  
    

    
    /**
     * Get post data and redirects to the next screen
     */
    public function onPost( $param )
    {
        try
        {

            TTransaction::open('Felabs_DB');
                    $logged = SystemUser::newFromLogin(TSession::getValue('login'));                        
            TTransaction::close();
            TTransaction::open('dados_fei_t');
            // TTransaction::setLogger(new TLoggerSTD);

            $sessao_diarioclasse            = TSession::getValue('sessao_diarioclasse');
            $CodGradeDisciplinaEtapa_Frente = $sessao_diarioclasse["CodGradeDisciplinaEtapa_Frente"];
            $CodDisciplina                  = $sessao_diarioclasse["CodDisciplina"];
            $NumeroOrdemAula                = $sessao_diarioclasse["NumeroOrdemAula"];
            $CodTurmaetapa                  = $sessao_diarioclasse["CodTurmaetapa"];
            $sessao_data_escolhida          = TSession::getValue('data_escolhida');
            $data_escolhida                 = $sessao_data_escolhida["data_escolhida"];
            $diasemana_numero = date('w', strtotime('+1 day', strtotime($data_escolhida)));

            //$hoje = date('Y-m-d');
            $horaAtual = date('H:i:s');
            $data = $this->form->getData();

            $this->form->setData($data);
            
            $frqdiarias = FiFrqdiaria::where('CodTurmaetapa', '=', $CodTurmaetapa)
                                     ->where('Data',   '=', $data_escolhida)
                                     ->load();
                                    //  var_dump($frqdiarias);
                                    //  die();
  
     
            foreach ($frqdiarias as $frqdiaria)
            {
                $CodFrqDiaria   = $frqdiaria->CodFrqDiaria;
                $CodTurmaetapa  = $frqdiaria->CodTurmaetapa;
                $Data           = $frqdiaria->Data;

                $object_diario = FiFrqdiaria::find($CodFrqDiaria); //busca o registro da aula selecionada

                if ($object_diario)
                {
                    
                    foreach ($this->form->getFields() as $name => $field)
                    {
                    //    echo '<pre>';
                    //     var_dump($name);
                    //     echo '</pre>';
                    //    die();

                        if ($field instanceof TCheckButton)
                        {

                            $parts = explode('_', $name);

                            $check                                  = $parts[0];
                            $check_CodMatriculaEtapa                = $parts[1];
                            $check_CodTurmaetapa                    = $parts[2];
                            $check_CodGradeDisciplinaEtapa_Frente   = $parts[3];
                            $check_CodDisciplina                    = $parts[4];
                            $check_Periodo                          = $parts[5];
                            $check_NumeroOrdemAula                  = $parts[6];

                            // Define 'F' como padrão, para quando o valor estiver vazio ou null
                            $Freq = 'P'; 

                            if ($field->getValue() == '')
                            {
                                $Freq = 'F'; 
                            } 

        // Verificação da frequência existente
        $FrqdiariaDisciplinaExistente = FiFrqdiariaDisciplinas::where('CodGradeDisciplinaEtapa_Frente', '=', $check_CodGradeDisciplinaEtapa_Frente)
            ->where('CodMatriculaEtapa', '=', $check_CodMatriculaEtapa)
            ->where('CodFrqDiaria', '=', $CodFrqDiaria)
            ->where('CodDisciplina', '=', $check_CodDisciplina)
            ->where('Aula', '=', $check_NumeroOrdemAula)
            ->first();

        if ($FrqdiariaDisciplinaExistente)
        {
            // O registro já existe, faça o UPDATE
            if ($FrqdiariaDisciplinaExistente->Freq !== $Freq) {  // Só atualiza se o valor for diferente
                $FrqdiariaDisciplinaExistente->Freq = $Freq;
                $FrqdiariaDisciplinaExistente->DataLancamento = $Data;
                $FrqdiariaDisciplinaExistente->CodProfessor = $logged->systemuser_codlegado;
                $FrqdiariaDisciplinaExistente->store(); //Grava update
            }
        } 
        else
        {
            // O registro não existe, faça o INSERT
            $FrqdiariaDisciplina = new FiFrqdiariaDisciplinas;
            $FrqdiariaDisciplina->CodGradeDisciplinaEtapa_Frente    = $check_CodGradeDisciplinaEtapa_Frente;
            $FrqdiariaDisciplina->CodMatriculaEtapa                 = $check_CodMatriculaEtapa;
            $FrqdiariaDisciplina->CodFrqDiaria                      = $CodFrqDiaria;
            $FrqdiariaDisciplina->CodDisciplina                     = $check_CodDisciplina;
            $FrqdiariaDisciplina->Aula                              = $check_NumeroOrdemAula;
            $FrqdiariaDisciplina->Freq                              = $Freq;
            $FrqdiariaDisciplina->DataLancamento                    = $Data;
            $FrqdiariaDisciplina->HoraLancamento                    = $horaAtual;
            $FrqdiariaDisciplina->CodProfessor                      = $logged->systemuser_codlegado;
            $FrqdiariaDisciplina->store(); //Grava insert
        }
    }   
}
                        }   
                        
                    }
                 
                
  
                
            
        
            TTransaction::close();
            TApplication::loadPage('ConteudoDiarioClasseForm');
        
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
}


