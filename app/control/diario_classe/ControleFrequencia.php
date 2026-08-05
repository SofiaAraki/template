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
        //$NomeDiaSemana      = $sessao_diarioclasse["NomeDiaSemana"];
        $CodDisciplina      = $sessao_diarioclasse["CodDisciplina"];
        $Ciclo              = $sessao_diarioclasse["Etapa"];
        $Codprofessor       = $sessao_diarioclasse["Codprofessor"];
        $CodGradeDisciplinaEtapaFrente = $sessao_diarioclasse["CodGradeDisciplinaEtapaFrente"];
        
        //$sessao_data_escolhida = TSession::getValue('data_escolhida');
        $data_escolhida = TSession::getValue('data_escolhida');

        // Critério para buscar os alunos
        $criteria = new TCriteria();
        $criteria->add(new TFilter('Situacao','=','FR'), TExpression::AND_OPERATOR);
        // $criteria->add(new TFilter('TipoDis', '=', 'AT'), TExpression::AND_OPERATOR); // Não exibe alunos com DP na disciplina
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
        $this->form->addActionLink( 'Listar Disciplinas',  new TAction(['HorarioAulasList', 'onReload']), 'fa:reply blue');
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
 
        // $php_dia = date('w', strtotime($data_escolhida)); // 0 (dom) a 6 (sab)
        // $diasemana_numero = $php_dia + 1; // 1 (dom) a 7 (sab)

        $data = DateTime::createFromFormat('d/m/Y', $data_escolhida);

        if (!$data) {
            throw new Exception("Data inválida: {$data_escolhida}");
        }

        $php_dia = (int) $data->format('w');
        $diasemana_numero = $php_dia + 1;
        
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
        // $objects é a lista de alunos

        $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse'); 
        $verifica_frente_disciplina = $sessao_diarioclasse["CodGradeDisciplinaEtapaFrente"];
        $verifica_disciplina = $sessao_diarioclasse["CodDisciplina"];
        $verifica_professor = $sessao_diarioclasse["Codprofessor"];
        $verifica_turma = $sessao_diarioclasse["CodTurmaetapa"];
        $verifica_periodo = $sessao_diarioclasse["Periodo"];

        // $sessao_data_escolhida = TSession::getValue('data_escolhida');
        $data_escolhida = TSession::getValue('data_escolhida');
        
        // $php_dia = date('w', strtotime($data_escolhida)); // 0 (dom) a 6 (sab)
        // $diasemana_numero = $php_dia + 1; // 1 (dom) a 7 (sab)

        $data = DateTime::createFromFormat('d/m/Y', $data_escolhida);

        if (!$data) {
            throw new Exception("Data inválida: {$data_escolhida}");
        }

        $php_dia = (int) $data->format('w');
        $diasemana_numero = $php_dia + 1;


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
                
                $data = DateTime::createFromFormat('d/m/Y', $data_escolhida);
                $data_sql = $data->format('Y-m-d');

                // Verifica na tabela FiFrqdiariaDisciplinas se a frequência existe
                $FrqdiariaDisciplinaExistente = VwFrequenciadiaria  ::where('CodGradeDisciplinaEtapa_Frente', '=', $verifica_frente_disciplina)
                                                                    ->where('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa)
                                                                    ->where('CodTurmaetapa', '=', $object->CodTurmaetapa)
                                                                    ->where('CodDisciplina', '=', $verifica_disciplina)
                                                                    ->where('Periodo', 'like', $verifica_periodo)
                                                                    ->where('Aula', '=', $aula->NumeroOrdemAula)
                                                                    ->where('DataAula', '=', $data_sql)
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

    public function onPost($param) {
        try
        {
            TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            TTransaction::close();
            
            TTransaction::open('dados_fei');

            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
            $data = TSession::getValue('data_escolhida');
                
            $CodTurmaEtapa = $sessao_diarioclasse["CodTurmaetapa"];
            $dataAula = DateTime::createFromFormat('d/m/Y', $data)->format('Y-m-d');

            $horaAtual = date('H:i:s');
            $dataAtual = date('Y/m/d');
            
            $dataForm = $this->form->getData();
            $this->form->setData($dataForm);

            $frqdiaria = FiFrqdiaria::where('CodTurmaetapa', '=', $CodTurmaEtapa)
                            -> where('Data', '=', $dataAula)
                            -> first();

            if (is_null($frqdiaria))
            {
                $Frqdiaria = new FiFrqdiaria;
                $Frqdiaria->CodTurmaetapa   = $CodTurmaEtapa;
                $Frqdiaria->Data            = $dataAula;
                $Frqdiaria->CodOperador     = '';
                $Frqdiaria->DataLancamento  = $dataAtual;
                $Frqdiaria->HoraLancamento  = $horaAtual;
                $Frqdiaria->store();

                $CodFrqDiaria = $Frqdiaria->CodFrqDiaria;
            }
            else {
                $CodFrqDiaria = $frqdiaria->CodFrqDiaria;
            }

            foreach ($this->form->getFields() as $name => $field) {

                if ($field instanceof TCheckButton) {

                    $parts = explode('_', $name);

                    $check_CodMatriculaEtapa              = $parts[1];
                    $check_CodTurmaetapa                  = $parts[2];
                    $check_CodGradeDisciplinaEtapa_Frente = $parts[3];
                    $check_CodDisciplina                  = $parts[4];
                    $check_Periodo                        = $parts[5];
                    $check_NumeroOrdemAula                = $parts[6];

                    $Freq = empty($dataForm->$name) ? 'F' : 'P';
                    
                    // verifica se a frequência já foi lançada
                    $FrqdiariaDisciplinaExistente = FiFrqdiariaDisciplinas::where(
                        'CodGradeDisciplinaEtapa_Frente', '=', $check_CodGradeDisciplinaEtapa_Frente)
                        ->where('CodMatriculaEtapa', '=', $check_CodMatriculaEtapa)
                        ->where('CodFrqDiaria', '=', $CodFrqDiaria)
                        ->where('CodDisciplina', '=', $check_CodDisciplina)
                        ->where('Aula', '=', $check_NumeroOrdemAula)
                        ->first();

                    if ($FrqdiariaDisciplinaExistente)
                        {
                                if ($FrqdiariaDisciplinaExistente->Freq != $Freq)
                                    {
                                    $FrqdiariaDisciplinaExistente->Freq            = $Freq;
                                    $FrqdiariaDisciplinaExistente->DataLancamento  = $dataAula;
                                    $FrqdiariaDisciplinaExistente->CodProfessor    = $logged->systemuser_codlegado;
                                    $FrqdiariaDisciplinaExistente->store(); // Update
                                }
                        }
                    else
                        {
                            $FrqdiariaDisciplina = new FiFrqdiariaDisciplinas;
                            $FrqdiariaDisciplina->CodGradeDisciplinaEtapa_Frente = $check_CodGradeDisciplinaEtapa_Frente;
                            $FrqdiariaDisciplina->CodMatriculaEtapa              = $check_CodMatriculaEtapa;
                            $FrqdiariaDisciplina->CodFrqDiaria                   = $CodFrqDiaria;
                            $FrqdiariaDisciplina->CodDisciplina                  = $check_CodDisciplina;
                            $FrqdiariaDisciplina->Aula                           = $check_NumeroOrdemAula;
                            $FrqdiariaDisciplina->Freq                           = $Freq;
                            $FrqdiariaDisciplina->DataLancamento                 = $dataAula;
                            $FrqdiariaDisciplina->HoraLancamento                 = $horaAtual;
                            $FrqdiariaDisciplina->CodProfessor                   = $logged->systemuser_codlegado;    
                            TTransaction::get()->exec("SET NOCOUNT ON");
                            $FrqdiariaDisciplina->store(); // Insert
                        }
                }
            }
        
            TTransaction::close();
            new TMessage('info', 'Frequência salva com sucesso.');
        }
        catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}