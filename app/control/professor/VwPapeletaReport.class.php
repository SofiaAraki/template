<?php
/**
 * VwPapeletaReport Report
 * @author  <your name here>
 */
class VwPapeletaReport extends TPage
{
    protected $form; // form
    protected $notebook;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_VwPapeleta_report');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('VwPapeleta Report');
        


        // create the form fields
        $CodTurmaetapa = new TEntry('CodTurmaetapa');
        $CodDisciplina = new TEntry('CodDisciplina');
        $Avaliacao = new TEntry('Avaliacao');
        $CodGradeDisciplinaEtapa_Frente = new TEntry('CodGradeDisciplinaEtapa_Frente');
        $NomeDisciplina = new TEntry('NomeDisciplina');
        $Periodo = new TEntry('Periodoturma');
        $NomeCurso = new TEntry('NomeCurso');
        $output_type = new TRadioGroup('output_type');

         
        TTransaction::open('dados_fei');
            $sessao_papeleta = TSession::getValue('sessao_papeleta');

            $nomedisciplina = $sessao_papeleta["NomeDisciplina"];
            $coddiscipina = $sessao_papeleta["CodDisciplina"];
            $codprofessor = $sessao_papeleta["CodProfessor"];
            $codturmaetapa = $sessao_papeleta["CodTurmaetapa"];
            $periodoturma = $sessao_papeleta["Periodo"];
            $nomecurso = $sessao_papeleta["NomeCurso"];
            $codgradedisciplinaetapafrente = $sessao_papeleta["key"];
            
            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"];

              
            $CodTurmaetapa->setValue($codturmaetapa);
            $CodDisciplina->setValue($coddiscipina);
            $Avaliacao->setValue($Bimestre);
            $CodGradeDisciplinaEtapa_Frente->setValue($codgradedisciplinaetapafrente);
            $NomeDisciplina->setValue($nomedisciplina);
            $Periodo->setValue($periodoturma);
            $NomeCurso->setValue($nomecurso);
        
            $CodTurmaetapa->setEditable(false);
            $CodDisciplina->setEditable(false);
            $Avaliacao->setEditable(false);
            $CodGradeDisciplinaEtapa_Frente->setEditable(false);
            $NomeDisciplina->setEditable(false);
            $Periodo->setEditable(false);
            $NomeCurso->setEditable(false);

        TTransaction::close();

        // add the fields
        $this->form->addQuickField('Turma:', $CodTurmaetapa,  '50%' );
        $this->form->addQuickField('Cod.:', $CodDisciplina,  '50%' );
        $this->form->addQuickField('Avaliação:', $Avaliacao,  '50%' );
        $this->form->addQuickField('Cod. Disc. Frente:', $CodGradeDisciplinaEtapa_Frente,  '50%' );
        $this->form->addQuickField('Disciplina:', $NomeDisciplina,  '50%' );
        $this->form->addQuickField('Período:', $Periodo,  '50%' );
        $this->form->addQuickField('Curso:', $NomeCurso,  '50%' );
        $this->form->addQuickField('Output', $output_type,  '100%' , new TRequiredValidator);



        
        $output_type->addItems(array('pdf'=>'PDF'));;
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');
        
        // add the action button
        $btn = $this->form->addQuickAction(('Imprimir Papeleta'), new TAction(array($this, 'onGenerate')), 'fa:cog');
        $btn->class = 'btn btn-warning btn-sm';

        $this->form->addQuickAction('Voltar para a Lista de Disciplinas',  new TAction(array('VwProfessordisciplinassemestreList','onReload')), 'fa:list blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Papeleta Bimestral', $this->form));
        
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
            $sessao_papeleta = TSession::getValue('sessao_papeleta');

            $nomedisciplina = $sessao_papeleta["NomeDisciplina"];
            $nomecurso = $sessao_papeleta["NomeCurso"];
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $repository = new TRepository('VwPapeleta');
            $criteria   = new TCriteria;
            $criteria->setProperty('order', 'Ordem, Nome asc');  
            
            if ($formdata->CodTurmaetapa)
            {
                $criteria->add(new TFilter('CodTurmaetapa', 'like', "%{$formdata->CodTurmaetapa}%"));
            }
            if ($formdata->CodDisciplina)
            {
                $criteria->add(new TFilter('CodDisciplina', 'like', "%{$formdata->CodDisciplina}%"));
            }
            if ($formdata->Avaliacao)
            {
                $criteria->add(new TFilter('Avaliacao', 'like', "%{$formdata->Avaliacao}%"));
            }
            if ($formdata->CodGradeDisciplinaEtapa_Frente)
            {
                $criteria->add(new TFilter('CodGradeDisciplinaEtapa_Frente', 'like', "%{$formdata->CodGradeDisciplinaEtapa_Frente}%"));
            }
            if ($formdata->Periodo)
            {
                $criteria->add(new TFilter('Periodo', 'like', "%{$formdata->Periodo}%"));
            }

           

            $criteria->add(new TFilter('resultado', 'is not', NULL));
            
            
            //$criteria->add(new TFilter('mediasem', 'is not', NULL));


            //echo $criteria->dump();

            //die();

                       
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                $widths = array(120,500,80,80,80,120,80,80);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $tr = new TTableWriterRTF($widths);
                        break;
                }


                $sessao_papeleta = TSession::getValue('sessao_papeleta');
                $NomeDisciplina = $sessao_papeleta["NomeDisciplina"];
                $Etapa = $sessao_papeleta["Etapa"];
                $Identificacao = $sessao_papeleta["Identificacao"];
                $NomeEntidade = $sessao_papeleta["NomeEntidade"];
                $NomeProfessor = $sessao_papeleta["NomeProfessor"];
                $Periodo = $sessao_papeleta["Periodo"];
                $NomeCurso = $sessao_papeleta["NomeCurso"];


                $sessao_bimestre = TSession::getValue('sessao_bimestre');
                $Bimestre = $sessao_bimestre["Bimestre"];

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
                $tr->addCell('FUNDAÇÃO EDUCACIONAL DE ITUVERAVA', 'center', 'header', 8);
                $tr->addRow();
                $tr->addRow();
                $tr->addCell($NomeEntidade, 'center', 'header2', 8);
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('Papeleta Bimestral', 'center', 'header', 8);
                $tr->addRow();
                $tr->addCell('Bimestre:', 'right', 'header', 2);
                $tr->addCell($Bimestre, 'left', 'header',6);
                $tr->addRow();
                $tr->addCell($NomeCurso, 'center', 'header2', 8);
                $tr->addRow();
                $tr->addCell('Disciplina:', 'left', 'header2');
                $tr->addCell($NomeDisciplina, 'left', 'header2');
                $tr->addCell('Turno:', 'left', 'header2');
                $tr->addCell($Periodo, 'center', 'header2');
                $tr->addCell('Ciclo:', 'left', 'header2');
                $tr->addCell($Etapa, 'center', 'header2');

                $tr->addCell($Identificacao, 'center', 'header2',5);
                
                $tr->addRow();


                
                // add titles row
                $tr->addRow();
                $tr->addCell('Cod.', 'right', 'title');
                $tr->addCell('Nome', 'left', 'title');
                $tr->addCell('Nota', 'center', 'title');
                $tr->addCell('Faltas', 'center', 'title');
                $tr->addCell('Freq.', 'center', 'title');
                $tr->addCell('Media Final', 'center', 'title');
                $tr->addCell('Result.', 'left', 'title');
                $tr->addCell('Disc.', 'left', 'title');
                
                // controls the background filling
                $colour= FALSE;
                
                // data rows
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->codaluno, 'right', $style);
                    $tr->addCell($object->Nome, 'left', $style);
                    $tr->addCell($object->Nota1, 'center', $style);
                    $tr->addCell($object->Faltas, 'center', $style);
                    $tr->addCell($object->frequencia, 'center', $style);
                    $tr->addCell($object->mediasem, 'center', $style);
                    $tr->addCell($object->resultado, 'left', $style);
                    $tr->addCell($object->tipodisciplina, 'center', $style);
                    
                    $colour = !$colour;
                }
                
                // footer row

                $tr->addRow();
                $tr->addCell(date('d-m-Y h:i:s'), 'center', 'footer', 8);
                $tr->addRow();
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('______________', 'center', 'footer2', 2);
                $tr->addRow();
                $tr->addCell($NomeProfessor, 'center', 'footer', 2);
                // stores the file
                if (!file_exists("app/output/VwPapeleta.{$format}") OR is_writable("app/output/VwPapeleta.{$format}"))
                {
                    $tr->save("app/output/VwPapeleta.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/VwPapeleta.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/VwPapeleta.{$format}");
                
                // shows the success message
                new TMessage('info', 'Papeleta gerada com sucesso! Por favor, habilite o pop-up do navegador caso não visualize o arquivo.');
            }
            else
            {
                new TMessage('info', 'NÃO EXISTE NOTAS LANÇADAS!');
            }
    
            // fill the form with the active record data
            $this->form->setData($formdata);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }

}
