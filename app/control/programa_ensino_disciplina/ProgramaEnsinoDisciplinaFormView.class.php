<?php


class ProgramaEnsinoDisciplinaFormView extends TPage

{
     protected $form; 


    public function __construct( $param )
    {

        parent::__construct();


        TTransaction::open('Felabs_DB');

        $this->form = new BootstrapFormBuilder('ProgramaEnsinoDisciplinaFormView');
        $this->form->setFormTitle('Programa de Ensino da Disciplina');

        
        $label1 = new TLabel('ID:', '#333333', '15px', '');
        $label2 = new TLabel('Professor:', '#333333', '15px', '');
        $label3 = new TLabel('Curso:', '#333333', '15px', '');
        $label4 = new TLabel('Disciplina', '#333333', '15px', '');
        $label5 = new TLabel('Etapa:', '#333333', '15px', '');
        $label6 = new TLabel('Turma:', '#333333', '15px', '');
        //$label7 = new TLabel('', '#333333', '15px', '');
        $label8 = new TLabel('Código:', '#333333', '15px', '');
        $label9 = new TLabel('Obrigatória/Optativa:', '#333333', '15px', '');
        $label10 = new TLabel('Pré-Requisitos:', '#333333', '15px', '');
        $label11 = new TLabel('Có-Requisitos:', '#333333', '15px', '');
        $label12 = new TLabel('Período:', '#333333', '15px', '');
        $label13 = new TLabel('Semestral/Anual:', '#333333', '15px', '');
        $label14 = new TLabel('Crédito:', '#333333', '15px', '');
        $label15 = new TLabel('Total:', '#333333', '15px', '');
        $label16 = new TLabel('Semanal:', '#333333', '15px', '');
        $label17 = new TLabel('Teórica:', '#333333', '15px', '');
        $label18 = new TLabel('Prática:', '#333333', '15px', '');
        $label19 = new TLabel('Teórica/Prática:', '#333333', '15px', '');
        //$label20 = new TLabel('Modalidade:', '#333333', '15px', '');
        $label20 = new TLabel('Metodologia de ensino das atividades mediadas por tecnologia:', '#333333', '15px', '');
        //$label21 = new TLabel('Carga horária presencial:', '#333333', '15px', '');
        $label21 = new TLabel('Critérios de avaliação de aprendizagem:', '#333333', '15px', '');
        //$label22 = new TLabel('Carga horária EAD:', '#333333', '15px', '');
        $label22 = new TLabel('Material suplementar:', '#333333', '15px', '');
        $label23 = new TLabel('Ementa: (Tópicos que caracterizam. Unidades dos programas de ensino.)', '#333333', '15px', '');
        $label24 = new TLabel('Objetivos: (Ao término da disciplina o aluno deverá ser capaz de: )', '#333333', '15px', '');
        $label25 = new TLabel('Conteúdo Programático: (Título e discriminação das unidades )', '#333333', '15px', '');
        $label26 = new TLabel('Metodologia de Ensino:', '#333333', '15px', '');
        $label27 = new TLabel('Critérios de Avaliação de Aprendizagem:', '#333333', '15px', '');
        $label28 = new TLabel('Bibliografia Básica:', '#333333', '15px', '');
        $label29 = new TLabel('Bibliografia Complementar:', '#333333', '15px', '');
        $label30 = new TLabel('Descrição sumária das atividades realizadas:', '#333333', '15px', '');

        $programa_ensino_disciplina = new ProgramaEnsinoDisciplina($param['key']);

        
        $text1  = new TTextDisplay($programa_ensino_disciplina->id, '#333333', '15px', '');          
        //$text2  = new TTextDisplay($programa_ensino_disciplina->system_user_id, '#333333', '15px', '');
        $text3  = new TTextDisplay($programa_ensino_disciplina->curso, '#333333', '15px', '');


        TTransaction::open('dados_fei');

        $criteria2 = new TCriteria;
        $criteria2->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $programa_ensino_disciplina->disciplina));

        $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria2);

        $programa_ensino_disciplina->disciplina = $disciplinaNome[0]->NomeDisciplina;

        TTransaction::close();

        $text2  = new TTextDisplay($programa_ensino_disciplina->system_user->name, '#333333', '15px', '');
        $text4  = new TTextDisplay($programa_ensino_disciplina->disciplina, '#333333', '15px', '');
        $text5  = new TTextDisplay($programa_ensino_disciplina->etapa, '#333333', '15px', '');
        $text6  = new TTextDisplay($programa_ensino_disciplina->turma, '#333333', '15px', '');
        //$text7  = new TTextDisplay($programa_ensino_disciplina->status, '#333333', '15px', '');
        $text8  = new TTextDisplay($programa_ensino_disciplina->codigo, '#333333', '15px', '');
        $text9  = new TTextDisplay($programa_ensino_disciplina->obg_optativa, '#333333', '15px', '');
        $text10  = new TTextDisplay($programa_ensino_disciplina->pre_requisito, '#333333', '15px', '');
        $text11  = new TTextDisplay($programa_ensino_disciplina->co_requisito, '#333333', '15px', '');
        $text12  = new TTextDisplay($programa_ensino_disciplina->periodo, '#333333', '15px', '');
        $text13  = new TTextDisplay($programa_ensino_disciplina->semestral_anual, '#333333', '15px', '');
        $text14  = new TTextDisplay($programa_ensino_disciplina->credito, '#333333', '15px', '');
        $text15  = new TTextDisplay($programa_ensino_disciplina->total, '#333333', '15px', '');
        $text16  = new TTextDisplay($programa_ensino_disciplina->semanal, '#333333', '15px', '');
        $text17  = new TTextDisplay($programa_ensino_disciplina->teorica, '#333333', '15px', '');
        $text18  = new TTextDisplay($programa_ensino_disciplina->pratica, '#333333', '15px', '');
        $text19  = new TTextDisplay($programa_ensino_disciplina->teorica_pratica, '#333333', '15px', '');
        //$text20  = new TTextDisplay($programa_ensino_disciplina->modalidade, '#333333', '15px', '');
        //$text21  = new TTextDisplay($programa_ensino_disciplina->ch_presencial, '#333333', '15px', '');
        //$text22  = new TTextDisplay($programa_ensino_disciplina->ch_ead, '#333333', '15px', '');
        $text23  = new TTextDisplay($programa_ensino_disciplina->ementa, '#333333', '15px', '');
        $text24  = new TTextDisplay($programa_ensino_disciplina->objetivos, '#333333', '15px', '');
        $text25  = new TTextDisplay($programa_ensino_disciplina->conteudo_programatico, '#333333', '15px', '');
        $text26  = new TTextDisplay($programa_ensino_disciplina->metodologia, '#333333', '15px', '');
        $text27  = new TTextDisplay($programa_ensino_disciplina->criterio_avaliacao, '#333333', '15px', '');
        $text28  = new TTextDisplay($programa_ensino_disciplina->bibliografia_basica, '#333333', '15px', '');
        $text29  = new TTextDisplay($programa_ensino_disciplina->bibliografia_complementar, '#333333', '15px', '');
        $text20  = new TTextDisplay($programa_ensino_disciplina->metodologia_ead, '#333333', '15px', '');
        $text21  = new TTextDisplay($programa_ensino_disciplina->criterio_aval, '#333333', '15px', '');
        $text22  = new TTextDisplay($programa_ensino_disciplina->material_supl, '#333333', '15px', '');
        $text30  = new TTextDisplay($programa_ensino_disciplina->desc_atividades, '#333333', '15px', '');

                
        $this->form->addFields( [new TFormSeparator('Dados da Disciplina')] );
        $this->form->addFields([$label1],[$text1]);
        $this->form->addFields([$label2],[$text2]);
        $this->form->addFields([$label3],[$text3],[$label4],[$text4]);
        $this->form->addFields([$label8],[$text8],[$label9],[$text9]);
        $this->form->addFields([$label10],[$text10],[$label11],[$text11]);
        $this->form->addFields([$label12],[$text12],[$label13],[$text13]);

        $this->form->addFields( ['<br>'] );

        $this->form->addFields( [new TFormSeparator('Carga Horária')] );
        $this->form->addFields([$label14],[$text14],[$label15],[$text15],[$label16],[$text16]);

        $this->form->addFields( ['<br>'] );

        $this->form->addFields( [new TFormSeparator('Distribuição Carga Horária Semanal')] );
        $this->form->addFields([$label17],[$text17],[$label18],[$text18],[$label19],[$text19]);

                
        $this->form->addFields( [new TFormSeparator('')] );
        $this->form->addFields([$label23],[$text23]);
        $this->form->addFields([$label24],[$text24]);
        $this->form->addFields([$label25],[$text25]);
        $this->form->addFields([$label28],[$text28]);
        $this->form->addFields([$label29],[$text29]);
        
        //Se o Plano de Ensino NÃO for da FFCL ou FAJOB exibe Metodologia e Critérios de avaliação 
        if($programa_ensino_disciplina->unidade <> 2 AND $programa_ensino_disciplina->unidade <> 10)
        {
            $this->form->addFields([$label26],[$text26]);
            $this->form->addFields([$label27],[$text27]);
        }

         //Se o Plano de Ensino for da FFCL ou FAJOB exibe Adendo 2020
         /*if($programa_ensino_disciplina->unidade == 2 OR $programa_ensino_disciplina->unidade == 10)
         {
             $this->form->addFields( ['<br>'] );
             $this->form->addFields( [new TFormSeparator('Adendo 2020')] );
             $this->form->addFields([$label20],[$text20]);
             $this->form->addFields([$label21],[$text21]);
             $this->form->addFields([$label22],[$text22]);
             $this->form->addFields([$label30],[$text30]);
         }*/

        $this->form->addHeaderAction('Imprimir', new TAction(['ProgramaEnsinoDisciplinaFormView', 'onPrint'],['key'=>$programa_ensino_disciplina->id]), 'far:file-pdf red');

        //$this->form->addHeaderAction('Voltar', new TAction(['ProgramaEnsinoDisciplinaList', 'onReload']), 'far:arrow-alt-circle-left blue');

        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'formView-container'; 
        $container->add(new TXMLBreadCrumb('menu.xml', 'ProgramaEnsinoDisciplinaList'));
        $container->add($this->form);


        TTransaction::close();


        parent::add($container);
    }


    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }


    /**
     * Se for da FFCL ou FAJOB direciona para HTML com Adendo 2020, se for outra unidade, direciona para HTML sem Adendo 2020
    */ 

    public function onPrint($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');            

            $object = ProgramaEnsinoDisciplina::find($param['key']);

            if ($object)
            {
                //Se o plano de ensino não for da FFCL nem da FAJOB
                if($object->unidade <> 2 AND $object->unidade <> 10)
                {                
                    $object->data_reg = TDate::date2br($object->data_reg);

                    $userName = new SystemUser($object->system_user_id);

                    $object->system_user_id = $userName->name;


                    TTransaction::open('dados_fei');

                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));

                    $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria);

                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;

                    TTransaction::close();


                    $html = new AdiantiHTMLDocumentParser('app/documents/ProgramaEnsinoDisciplina.html', 'A4', 'portrait');
                    $html->setMaster($object);
      
                    $html->process();
                    $output = $html->getContents();
              
                    $document = 'tmp/'.uniqid().'.pdf'; 

                    $html = AdiantiHTMLDocumentParser::newFromString($output);
                    $html->saveAsPDF($document);

                    $window = TWindow::create('Programa de Ensino', 0.8, 0.8);


                    $object = new TElement('object');

                    $object->data  = 'download.php?file='.$document;
                    $object->type  = 'application/pdf';
                    $object->style = "width: 100%; height:calc(100% - 10px)";
    
                    $window->add($object);
                    $window->show();
                }
                
                //Se for da FFCL ou FAJOB, oculta Metodologia e Critérios de avaliação
                else
                {
                    $object->data_reg = TDate::date2br($object->data_reg);

                    $userName = new SystemUser($object->system_user_id);

                    $object->system_user_id = $userName->name;


                    TTransaction::open('dados_fei');

                    $criteria = new TCriteria;
                    $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));

                    $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria);

                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;

                    TTransaction::close();


                    $html = new AdiantiHTMLDocumentParser('app/documents/ProgramaEnsinoDisciplinaFFCL.html', 'A4', 'portrait');
                    $html->setMaster($object);
      
                    $html->process();
                    $output = $html->getContents();
              
                    $document = 'tmp/'.uniqid().'.pdf'; 

                    $html = AdiantiHTMLDocumentParser::newFromString($output);
                    $html->saveAsPDF($document);

                    $window = TWindow::create('Programa de Ensino', 0.8, 0.8);


                    $object = new TElement('object');

                    $object->data  = 'download.php?file='.$document;
                    $object->type  = 'application/pdf';
                    $object->style = "width: 100%; height:calc(100% - 10px)";
    
                    $window->add($object);
                    $window->show();    
                }
            }

            TTransaction::close();
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }


    public function onShow()

    {      

    }
}

   
            