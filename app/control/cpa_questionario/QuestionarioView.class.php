<?php
/**
 * QuestionarioView Form
 * @author  <your name here>
 */
class QuestionarioView extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();


        TTransaction::open('Felabs_DB');

        $periodoInfo = new QuestionarioPeriodo(TSession::getValue('periodoid'));


        ////////////////////////

        if($periodoInfo->mostra_disciplina == "S")
        {

            TTransaction::open('dados_fei');

            $codchave = new VwFiDisciplinasATADDP(TSession::getValue('CodDisciplinaChave'));    

            $criteria = new TCriteria;
            $criteria->add( new TFilter(Ano, '=', $periodoInfo->ano));
            $criteria->add( new TFilter(Semestre, '=', $periodoInfo->semestre));
            $criteria->add( new TFilter(CodTurmaetapa, '=', $codchave->CodTurmaetapa));
            $criteria->add( new TFilter(CodDisciplina, '=', $codchave->CodDisciplina));

            $registro = VwProfessordisciplinassemestre::getObjects($criteria);

            TTransaction::close();
        }

        /////////////////////////////


        TTransaction::close();


        
        // creates the form
        $this->form = new TQuickForm('form_QuestionarioQuestao');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('QuestionarioQuestao');
        


        // create the form fields
        $id = new TEntry('id');
        $questionario_id = new TEntry('questionario_id');
        $tipo = new TEntry('tipo');
        $conteudo = new TText('conteudo');

    

        TTransaction::open('Felabs_DB');

     //   $periodo = new QuestionarioPeriodo($param['key']);

        $criteria = new TCriteria;
        $criteria->add(new TFilter('questionario_id', '=', $periodoInfo->questionario_id)); //PEGA QUESTÕES DO QUESTIONARIO DO PERÍODO

        $questoes = QuestionarioQuestao::getObjects($criteria);

        TTransaction::close();



        if($periodoInfo->descricao)
        {
            $labelDescr = new TLabel($periodoInfo->descricao);
            $this->form->addQuickField('', $labelDescr,  '100%' );
        }


        foreach($questoes as $questao)
        {


            $criteria = new TCriteria;
            $criteria->add(new TFilter('questao_id', '=', $questao->id));

            $alternativa = 'alternativa'.$questao->id;


            $$alternativa  = new TDBRadioGroup("$questao->id", 'Felabs_DB', 'QuestionarioAlternativa', 'id', 'conteudo', 'id', $criteria);

            $enunciado = $questao->num_questao.'. '.$questao->conteudo;
            $label = new TLabel($enunciado);

           //  ($name, $database, $model, $key, $value, [$ordercolumn = NULL], [$criteria = NULL])
            $this->form->addQuickField('', $label,  '100%' );
            $this->form->addQuickField('', $$alternativa,  '100%' );

         
        }




        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/

        if(TSession::getValue('modo') != 'previsualizacao')
        {
            // create the form actions
            $btn = $this->form->addQuickAction('Enviar Respostas', new TAction(array($this, 'onSave'),$param), 'far:save');
            $btn->class = 'btn btn-sm btn-danger';

        }
         
        
 

        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';


        if($periodoInfo->mostra_disciplina == "S")
        {

            $nomeDisc = '| Disciplina: '.$registro[0]->NomeDisciplina;
            $nomeProf = '| Professor(a): '.$registro[0]->NomeProfessor;

            $container->add(TPanelGroup::pack("QUESTIONÁRIO $nomeDisc $nomeProf  ", $this->form));

        }
        else
        {
            $container->add(TPanelGroup::pack("QUESTIONÁRIO", $this->form));
        }
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
      //  var_dump($param['key']);
      //  die;

        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            $loggedUnit = TSession::getValue('userunitid');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new QuestionarioQuestao;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data


            

            $questoes = (array)$data;

            foreach($questoes as $value=>$key) //VERIFICA SE TODAS AS QUESTÕES OBRIGATÓRIAS FORAM RESPONDIDAS
            {

                $questaoInfo = new QuestionarioQuestao($value); 

                if($questaoInfo->obrigatorio == 'S' && $key == 0)
                {
                    throw new Exception("A questão número $questaoInfo->num_questao é obrigatória", 1);
                    
                }

            }

            $periodoInfo = new QuestionarioPeriodo(TSession::getValue('periodoid'));



            if($periodoInfo->mostra_disciplina == 'S') //SE O QUESTIONÁRIO FOR DO TIPO 'DISCIPLINAS'
            {

                TTransaction::open('dados_fei');

                $criteria1 = new TCriteria;
                $criteria1->add( new TFilter(AnoMatricula, '=', $periodoInfo->ano));
                $criteria1->add( new TFilter(SemestreMatricula, '=', $periodoInfo->semestre));
                $criteria1->add( new TFilter(CodAluno, '=', $logged->systemuser_codlegado));

                $matriculaEtapa = VwAlunoMatriculaEtapa::getObjects($criteria1); //OK


                $criteria2 = new TCriteria;
                $criteria2->add( new TFilter(CodDisciplinaChave, '=', TSession::getValue('CodDisciplinaChave')));
                $criteria2->add( new TFilter(CodMatriculaEtapa, '=', $matriculaEtapa[0]->CodMatriculaEtapa));

                $codchave = VwFiDisciplinasATADDP::getObjects($criteria2);
            

                $criteria = new TCriteria;
                $criteria->add( new TFilter(Ano, '=', $periodoInfo->ano));
                $criteria->add( new TFilter(Semestre, '=', $periodoInfo->semestre));
                $criteria->add( new TFilter(Periodo, '=', $matriculaEtapa[0]->Periodo));
                $criteria->add( new TFilter(CodDisciplina, '=', $codchave[0]->CodDisciplina));


                $registro = VwProfessordisciplinassemestre::getObjects($criteria);

                TTransaction::close(); // close the transaction
            }

            

            foreach($questoes as $value=>$key) //VALUE = ID DA QUESTAO   KEY = ID DA ALTERNATIVA
            {
                TTransaction::open('Felabs_DB');
                $questaoInfo = new QuestionarioQuestao($value); 

                $alternativaInfo = new QuestionarioAlternativa($key);

                $resposta = new QuestionarioResposta();
                $resposta->questionario_periodo_id = $periodoInfo->id;
                $resposta->questionario_id = $periodoInfo->questionario_id;
                $resposta->questao_id = $value;
                $resposta->alternativa_id = $key;
                $resposta->system_user_id = $logged->id;
                $resposta->system_unit_id = $loggedUnit;

                if($periodoInfo->mostra_disciplina == 'S') //SE O QUESTIONÁRIO FOR DO TIPO 'DISCIPLINAS'
                {

                    $resposta->cod_disciplina = $codchave[0]->CodDisciplina;
                    $resposta->cod_professor = $registro[0]->Codprofessor;
                    $resposta->cod_curso = $matriculaEtapa[0]->CodCurso;

                }


                $resposta->ano = $periodoInfo->ano;
                $resposta->semestre = $periodoInfo->semestre;
                $resposta->conteudo_alternativa = $alternativaInfo->conteudo;
                $resposta->num_questao = $questaoInfo->num_questao;
                
                $resposta->store();

                TTransaction::close();


            }

            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data

            TTransaction::close(); // close the transaction

            if($periodoInfo->mostra_disciplina == 'S')
            {
                new TMessage('info', 'Suas respostas foram enviadas com sucesso',TApplication::loadPage('QuestionarioDisciplinasAluno'));
            }
            else
            {
                new TMessage('info', 'Suas respostas foram enviadas com sucesso',TApplication::loadPage('QuestionarioPeriodoListAluno'));
            }
            
            
            
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }


    public function mostrar( $param )
    {
       
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new QuestionarioQuestao($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
