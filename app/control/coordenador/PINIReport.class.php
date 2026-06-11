<?php
/**
 * PINIReport Report
 * @author  <your name here>
 */
class PINIReport extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_VwAlunoMatriculaEtapa_report');
        $this->form->setFormTitle('Papeleta Prova Integrada e Núcleo Integrador');
        

        // create the form fields
        //$NomeAluno = new TEntry('NomeAluno');
        //$NomeCurso = new TEntry('NomeCurso');
        $CodTurmaetapa = new TEntry('CodTurmaetapa');
        $Ano = new TEntry('Ano');
        $Semestre = new TEntry('Semestre');
        $CodCurso = new TEntry('CodCurso');
        $output_type = new TRadioGroup('output_type');

        TTransaction::open('dados_fei');
            $sessao_papeletaPI = TSession::getValue('sessao_papeletaPI');

            $semestre = $sessao_papeletaPI["Semestre"];
            $ano = $sessao_papeletaPI["Ano"];
            $turmaetapa = $sessao_papeletaPI["key"];
            $codcurso = $sessao_papeletaPI["CodCurso"];
           
          //var_dump($sessao_papeletaPI);
          //die();


        // add the fields
        //$this->form->addFields( [ new TLabel('Nomealuno') ], [ $NomeAluno ] );
        //$this->form->addFields( [ new TLabel('Nomecurso') ], [ $NomeCurso ] );
        $this->form->addFields( [ new TLabel('Turma') ], [ $CodTurmaetapa ] );
        $this->form->addFields( [ new TLabel('Ano') ], [ $Ano ] );
        $this->form->addFields( [ new TLabel('Semestre') ], [ $Semestre ] );
        $this->form->addFields( [ new TLabel('CodCurso') ], [ $CodCurso ] );
        $this->form->addFields( [ new TLabel('Output') ], [ $output_type ] );

        $output_type->addValidation('Output', new TRequiredValidator);


        // set sizes
       //$NomeAluno->setSize('100%');
       //$NomeCurso->setSize('100%');
        $output_type->setSize('100%');

        $CodTurmaetapa->setValue($turmaetapa);
        $Ano->setValue($ano);
        $Semestre->setValue($semestre);
        $CodCurso->setValue($codcurso);

        $CodTurmaetapa->setEditable(false);
        $Ano->setEditable(false);;
        $Semestre->setEditable(false);;
        $CodCurso->setEditable(false);;


        
        $output_type->addItems(array('pdf'=>'PDF'));
        $output_type->setLayout('horizontal');
        $output_type->setUseButton();
        $output_type->setValue('pdf');
        $output_type->setSize(70);
        
        // add the action button
        $btn = $this->form->addAction(('Imprimir Papeleta PI NI'), new TAction(array($this, 'onGenerate')), 'fa:cog');
        $btn->class = 'btn btn-warning btn-sm';

        $this->form->addAction('Listar Turmas',  new TAction(array('CoordenadorPIList','onReload')), 'fa:list orange');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CoordenadorPIList'));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Generate the report
     */
    function onGenerate()
    {
        try
        {
            // open a transaction with database 'dados_fei_t'
            TTransaction::open('dados_fei');
            
            // get the form data into an active record
            $data = $this->form->getData();
            
            $this->form->validate();
            
            $repository = new TRepository('VwAlunoMatriculaEtapa');
            $criteria   = new TCriteria;
            $criteria->setProperty('order', 'NomeAluno asc');
            
            if ($data->CodTurmaetapa)
            {
                $criteria->add(new TFilter('CodTurmaetapa', 'like', "%{$data->CodTurmaetapa}%"));
            }
            if ($data->Ano)
            {
                $criteria->add(new TFilter('AnoMatricula', 'like', "%{$data->Ano}%"));
            }

            if ($data->Semestre)
            {
                $criteria->add(new TFilter('SemestreMatricula', 'like', "%{$data->Semestre}%"));
            }

            if ($data->CodCurso)
            {
                $criteria->add(new TFilter('CodCurso', 'like', "%{$data->CodCurso}%"));
            }

           //echo $criteria->dump();

            $objects = $repository->load($criteria, FALSE);
            $format  = $data->output_type;
            
            if ($objects)
            {
                $widths = array(40,100,100,100,60,30,50,30,30);
                
                switch ($format)
                {
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                 }
                
                $sessao_papeletaPI = TSession::getValue('sessao_papeletaPI');
                $Identificacao = $sessao_papeletaPI["Identificacao"];

                //var_dump($sessao_papeletaPI);

                // create the document styles
                $tr->addStyle('title', 'Arial', '10', 'B',   '#000000', '#E8E8E8');
                $tr->addStyle('datap', 'Arial', '9', '',    '#000000', '#EEEEEE');
                $tr->addStyle('datai', 'Arial', '9', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Arial', '16', '',   '#ffffff', '#A9A9A9');
                $tr->addStyle('header2', 'Arial', '10', 'B',   '#000000', '#E8E8E8');
                $tr->addStyle('footer', 'Arial', '9', 'I',  '#000000', '#A9A9A9');
                $tr->addStyle('footer2', 'Arial', '24', 'I',  '#ffffff', '#ffffff');
                
                // add a header row
                $tr->addRow();
                $tr->addCell('FUNDAÇÃO EDUCACIONAL DE ITUVERAVA', 'center', 'header', 9);
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('Faculdade de Filosofia, Ciências e Letras de Ituverava', 'center', 'header2', 9);
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('Papeleta - Prova Integrada e Núcleo Integrador', 'center', 'header', 9);
                $tr->addRow();
                $tr->addCell('Turma:', 'left', 'header2');
                $tr->addCell($Identificacao, 'left', 'header2',8);
                
                $tr->addRow();
                
                // add titles row
                $tr->addRow();
                $tr->addCell('Cod.', 'left', 'title');
                $tr->addCell('Nome', 'left', 'title',3);
                $tr->addCell('Turma', 'left', 'title');
                //$tr->addCell('Nomecurso', 'left', 'title');
                $tr->addCell('N.I.', 'left', 'title');
                $tr->addCell('Acertos.', 'left', 'title');
                $tr->addCell('P.I.', 'left', 'title');
                $tr->addCell('% P.I.', 'left', 'title');

                
                // controls the background filling
                $colour= FALSE;
                
                // data rows
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->Codaluno, 'left', $style);
                    $tr->addCell($object->NomeAluno, 'left', $style, 3);
                    $tr->addCell($object->IdentificacaoMatricula, 'left', $style);
                   //$tr->addCell($object->NomeCurso, 'left', $style);
                    $tr->addCell($object->NotaNI, 'left', $style);
                    $tr->addCell($object->TotalAcertosPI, 'left', $style);
                    $tr->addCell($object->MediaPI, 'left', $style);
                    $tr->addCell($object->PercentualPI, 'left', $style);

                    
                    $colour = !$colour;
                }
                
                // footer row
                $tr->addRow();
                $tr->addCell(date('d-m-Y h:i:s'), 'center', 'footer', 9);
                $tr->addRow();
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('_________', 'center', 'footer2', 2);
                $tr->addRow();
                $tr->addCell('Assinatura Coordenador', 'center', 'footer', 2);
                
                // stores the file
                if (!file_exists("app/output/VwAlunoMatriculaEtapa.{$format}") OR is_writable("app/output/VwAlunoMatriculaEtapa.{$format}"))
                {
                    $tr->save("app/output/VwAlunoMatriculaEtapa.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/VwAlunoMatriculaEtapa.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/VwAlunoMatriculaEtapa.{$format}");
                
                // shows the success message
                new TMessage('info', 'Papeleta gerada com sucesso! Por favor, habilite o pop-up do navegador caso não visualize o arquivo.');
            }
            else
            {
                new TMessage('info', 'NÃO EXISTE NOTAS LANÇADAS!');
            }
    
            // fill the form with the active record data
            $this->form->setData($data);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
