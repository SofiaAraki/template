<?php


class ProgramaEnsinoDisciplinaForm extends TPage
{
    protected $form;     


    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ProgramaEnsinoDisciplina');
        $this->form->setFormTitle('Programa de Ensino da Disciplina');
        
        
        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TEntry('system_user_id');
        $curso = new TEntry('curso');
        $nome = new TEntry('nome');
        //$curso = new TEntry('curso');
        $disciplina = new TCombo('disciplina');
        $turma = new TEntry('turma');
        $codigo = new TEntry('codigo');
        $obg_optativa = new TRadioGroup('obg_optativa');
        $pre_requisito = new TEntry('pre_requisito');
        $co_requisito = new TEntry('co_requisito');
        $periodo = new TEntry('periodo');
        $semestral_anual = new TRadioGroup('semestral_anual');
        $credito = new TEntry('credito');
        $total = new TEntry('total');
        $semanal = new TEntry('semanal');
        $teorica = new TEntry('teorica');
        $pratica = new TEntry('pratica');
        $teorica_pratica = new TEntry('teorica_pratica');
        //$modalidade = new TCombo('modalidade');
        //$ch_presencial = new TEntry('ch_presencial');
        //$ch_ead = new TEntry('ch_ead');
        $ementa = new TText('ementa');
        $objetivos = new TText('objetivos');
        $conteudo_programatico = new TText('conteudo_programatico');
        $bibliografia_basica = new TText('bibliografia_basica');
        $bibliografia_complementar = new TText('bibliografia_complementar');
        $unidade = new THidden('unidade');
        $metodologia = new TText('metodologia');
        $criterio_avaliacao = new TText('criterio_avaliacao');

        //$metodologia_ead = new TText('metodologia_ead');
        //$criterio_aval = new TText('criterio_aval');
        //$material_supl = new TText('material_supl');
        //$desc_atividades = new TText('desc_atividades');


        $curso->setEditable(FALSE);
        $curso->setSize('100%');
        $turma->setEditable(FALSE);
        $codigo->setEditable(FALSE);
        $periodo->setEditable(FALSE);
        
        
        //$metodologia_ead->setEditable(FALSE);
        //$metodologia_ead->setValue("A disciplina acontece na plataforma Moodle (atividades assíncronas) e as aulas síncronas semanais através do Big Blue. São realizadas aulas expositivas e reflexivas nos dias e horários  das aulas presenciais, ficando gravadas para posterior consulta.");
        //$criterio_aval->setEditable(FALSE);
        //$criterio_aval->setValue("São enviadas atividades visando avaliar o processo de ensino-aprendizagem de modo contínuo. Ao final do bimestre, os discentes desenvolvem uma atividade de maior valor, que corresponde à avaliação bimestral.");
        //$material_supl->setEditable(FALSE);
        //$material_supl->setValue("-");
        //$desc_atividades->setEditable(FALSE);
        //$desc_atividades->setValue("- Aulas síncronas semanais, voltadas à reflexão, discussão e esclarecimentos de dúvidas acerca do conteúdo trabalhado.- Atividades enviadas através do AVA Moodle.");
                

        //Carregar disciplinas de acordo com a unidade selecionada pelo professor e semestre atual
        TTransaction::open('Felabs_DB');
        
        //$loggedProf = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        $loggedUnitProf = TSession::getValue('userunitid');

        TTransaction::close();


        TTransaction::open('Dados_Fei');

        $repository = new TRepository('VwProfessordisciplinassemestre');

        //$ano = date('Y');

        $mes = date('m');

        if($mes < 8)
        {
            $semestre = 1;
        }
        elseif($mes > 7)
        {
            $semestre = 2;
        }

        /*if($loggedUnitProf == 2 OR $loggedUnitProf == 10)
        {
            $ano = 2021;
            $semestre = 2;
        }
        else
        {*/
            $ano = date('Y');
        //}


        // creates a criteria
        $criteria = new TCriteria;            
        $criteria->add(new TFilter('CodProfessor', '=', $user->systemuser_codlegado));
        $criteria->add(new TFilter('Ano', '=', 2025), TExpression::AND_OPERATOR);//$ano
/**/    $criteria->add(new TFilter('Semestre', '=', 2), TExpression::AND_OPERATOR);//$semestre
        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf), TExpression::AND_OPERATOR);

        //echo $criteria->dump();

        $repo = $repository->load($criteria);

        $items = [];
        $i = 0;

        foreach($repo as $row)
        {
            $stringCodDisciplina = $repo[$i]->CodGradeDisciplinaEtapaFrente;

            $items["$stringCodDisciplina"] = $repo[$i]->NomeDisciplina;
            $i++;
        }

        $disciplina->addItems($items);

        $change_action = new TAction(array($this, 'onChangeAction'));
        $disciplina->setChangeAction($change_action);


        //Obrigatória ou Optativa
        $obg_opt = [];
        $obg_opt['Obrigatória'] = 'Obrigatória';
        $obg_opt['Optativa'] = 'Optativa';
        
        $obg_optativa->addItems($obg_opt);
        $obg_optativa->setLayout('vertical');
        $obg_optativa->setSize('100%');
        
        
        //Semestral ou anual
        $sem_anual = [];
        $sem_anual['Semestral'] = 'Semestral';
        $sem_anual['Anual'] = 'Anual';
        
        $semestral_anual->addItems($sem_anual);
        $semestral_anual->setLayout('vertical');
        $semestral_anual->setSize('100%');
        
        
        //Modalidade
        /*$modalidades = [];
        $modalidades['Presencial'] = 'Presencial';
        $modalidades['EAD'] = 'EAD';
        $modalidades['Presencial e EAD'] = 'Ambos';
        
        $modalidade->addItems($modalidades);*/
        

        $disciplina->addValidation( ('Disciplina'), new TRequiredValidator );
        $obg_optativa->addValidation( ('Obrigatória/Optativa'), new TRequiredValidator );
        $periodo->addValidation( ('Perí­odo'), new TRequiredValidator );
        $semestral_anual->addValidation( ('Semestral/Anual'), new TRequiredValidator );
        //$modalidade->addValidation( ('Modalidade'), new TRequiredValidator );
        $ementa->addValidation( ('Ementa'), new TRequiredValidator );
        $objetivos->addValidation( ('Objetivos'), new TRequiredValidator );
        $conteudo_programatico->addValidation( ('Conteúdo Programático'), new TRequiredValidator );
        $bibliografia_basica->addValidation( ('Bibliografia Básica'), new TRequiredValidator );
        $bibliografia_complementar->addValidation( ('Bibliografia Complementar'), new TRequiredValidator );


        // add the fields
        $this->form->addFields( [$id] );
                
        $this->form->addContent( [ '<h4>Dados da disciplina</h4><hr>' ] );
        $this->form->addFields( [ new TLabel('Disciplina') ], [ $disciplina ] );
        $this->form->addFields( [ new TLabel('Curso') ], [ $curso ], [ new TLabel('Turma') ], [ $turma ] );
        $this->form->addFields( [ new TLabel('Código') ], [ $codigo ], [ new TLabel('Período') ], [ $periodo ]);
        $this->form->addFields( [ new TLabel('Pré-Requisitos') ], [ $pre_requisito ], [ new TLabel('Correquisitos') ], [ $co_requisito ] );
        $this->form->addFields( [ new TLabel('Periodicidade') ], [ $semestral_anual ], [ new TLabel('Classificação') ], [ $obg_optativa ] );

        $this->form->addContent( ['<br><h4>Carga Horária</h4><hr>'] );
        $this->form->addFields( [new TLabel('Crédito')], [$credito], [new TLabel('Total')], [$total], [new TLabel('Semanal')], [$semanal] );

        $this->form->addContent( ['<br><h4>Distribuição Carga Horária Semanal</h4><hr>'] );
        $this->form->addFields( [new TLabel('Teórica')], [$teorica], [new TLabel('Prática')], [$pratica], [new TLabel('Teórica/Prática')], [$teorica_pratica]);
                        
        $this->form->addContent( ['<br><h4>Plano de Ensino</h4><hr>'] );        
        $this->form->addFields( [new TLabel('Ementa (Tópicos que caracterizam. Unidades dos programas de ensino.)')], [$ementa]);
        $this->form->addFields( [new TLabel('Objetivos (Ao término da disciplina o aluno deverá ser capaz de: )')], [$objetivos]);
        $this->form->addFields( [new TLabel('Conteúdo Programático: (Tí­tulo e discriminação das unidades )')], [$conteudo_programatico]);
        
        
        //Se o professor NÃO logou na FFCL(2) nem na FAJOB(10), exibe os campos Metodologia e Critérios de avaliação
        if($loggedUnitProf <> 2 AND $loggedUnitProf <> 10)
        {            
            $this->form->addFields( [ new TLabel('Metodologia de Ensino') ], [ $metodologia ] );
            $this->form->addFields( [ new TLabel('Critérios de Avaliação') ], [ $criterio_avaliacao ] );
        }
        
        
        $this->form->addFields( [new TLabel('Bibliografia Básica:')], [$bibliografia_basica]);
        $this->form->addFields( [new TLabel('Bibliografia Complementar:')], [$bibliografia_complementar]);

/*
        //Se o professor logou na FFCL(2) ou na FAJOB(10), exibe os campos do Adendo 2020
        if($loggedUnitProf == 2 OR $loggedUnitProf == 10)
        {            
            $this->form->addContent( [ '<hr><br><h4>ADENDO em atendimento à  Portaria nº 343, Portaria nº 345 e Portaria nº 544 que autoriza, em caráter excepcional, a substituição das disciplinas presenciais, em andamento, por aulas que utilizem meios e tecnologias de informação e comunicação devido à Pandemia do COVID-19. <br>
            A ementa, objetivos, conteúdos e bibliografia foram mantidos os inicialmente previstos neste plano de ensino.
            <br> <br>Alterações:</h4>' ] );
            $this->form->addFields( [ new TLabel('METODOLOGIA DE ENSINO DAS ATIVIDADES MEDIADAS POR TECNOLOGIA') ], [ $metodologia_ead ]); 
            $this->form->addFields( [ new TLabel('CRITÉRIOS DE AVALIAÇÃO DA APRENDIZAGEM') ], [ $criterio_aval ]); 
            $this->form->addFields( [ new TLabel('MATERIAL SUPLEMENTAR') ], [ $material_supl ] );
            $this->form->addFields( [ new TLabel('DESCRIÇÃO SUMÁRIA DAS ATIVIDADES REALIZADAS') ], [ $desc_atividades ] );
        }
        
*/  

        $codigo->setSize('30%');
        $ementa->setSize('100%', 250);
        $objetivos->setSize('100%', 200);
        $conteudo_programatico->setSize('100%', 400);
        $metodologia->setSize('100%', 150);
        $criterio_avaliacao->setSize('100%', 150);
        $bibliografia_basica->setSize('100%', 150);
        $bibliografia_complementar->setSize('100%', 200);
         
         
        // create the form actions
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save green');
        //$this->form->addAction(_t('New'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ProgramaEnsinoDisciplinaList'));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onChangeAction($param)
    {
        TTransaction::open('Dados_Fei');   

        $repository = new TRepository('VwProfessordisciplinassemestre');

        
        $mes = date('m');

        if($mes < 8)
        {
            $semestre = 1;
        }
        elseif($mes > 7)
        {
            $semestre = 2;
        }

        /*if($loggedUnitProf == 2 OR $loggedUnitProf == 10)
        {
            $ano = 2021; //alterado de 2020 para 2021
            $semestre = 2;
        }
        else
        {
            $ano = date('Y');
        }*/
         
        $ano = date('Y'); 
            
        // creates a criteria
        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $param['disciplina']));
        $criteria->add(new TFilter('Ano', '=', 2025), TExpression::AND_OPERATOR);
/**/    $criteria->add(new TFilter('Semestre', '=', 2), TExpression::AND_OPERATOR);

        $repo = $repository->load($criteria);

        $obj = new StdClass;
        $obj->curso = $repo[0]->NomeCurso;
        $obj->turma = $repo[0]->Identificacao;
        $obj->periodo = $repo[0]->Periodo;
        $obj->codigo = $repo[0]->CodDisciplina;
        TForm::sendData('form_ProgramaEnsinoDisciplina', $obj);

        TTransaction::close();
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');            
            
            $this->form->validate();
            
            $object = new ProgramaEnsinoDisciplina;
            
            $data = $this->form->getData();
            $object->fromArray( (array) $data);
            
            $object->system_user_id = TSession::getValue('userid');
            $loggedUnit = TSession::getValue('userunitid');
            
            //Adicionado para teste
            $ano = date('Y');
            
            $mes = date('m');

            if($mes < 8)
            {
                $semestre = 1;
            }
            elseif($mes > 7)
            {
                $semestre = 2;
            }
            //Até aqui
            
            
            TTransaction::open('Dados_Fei');   

            $repository = new TRepository('VwProfessordisciplinassemestre');            
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $param['disciplina']));
            
            //Adicionado para teste, pois estava puxando registros de outros anos
            $criteria->add(new TFilter('Ano', '=', $ano ), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Semestre', '=', $semestre), TExpression::AND_OPERATOR);
            //Até aqui

            $repo = $repository->load($criteria);


            $obj = new StdClass;
            $obj->nome = $repo[0]->NomeEntidade;
            $obj->CodCurso = $repo[0]->CodCurso;
            $obj->CodProfessor = $repo[0]->CodProfessor;
            $obj->CodGradecurso = $repo[0]->CodGradecurso;
            TForm::sendData('form_ProgramaEnsinoDisciplina', $obj);

            TTransaction::close();
           
           
            $object->nome = $obj->nome;
            $object->unidade = $loggedUnit;
            $object->CodCurso = $obj->CodCurso;
            $object->Codprofessor = $obj->CodProfessor;
            $object->CodGradecurso = $obj->CodGradecurso;
            
            $object->store();           
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('ProgramaEnsinoDisciplinaList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new ProgramaEnsinoDisciplina($key);
                $this->form->setData($object);
                
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
