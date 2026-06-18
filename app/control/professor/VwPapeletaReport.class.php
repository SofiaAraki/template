<?php
/**
 * VwPapeletaReport Report
 * @author  FEI Team
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
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        
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
            
            $sessao = TSession::getValue('sessao_papeleta_unificada');
            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            
            $Bimestre = $sessao_bimestre["Bimestre"] ?? '2';

            if (!empty($sessao))
            {
                $CodTurmaetapa->setValue($sessao['CodTurmaetapa'] ?? '');
                $CodDisciplina->setValue($sessao['CodDisciplina'] ?? '');
                $Avaliacao->setValue($Bimestre);
                $CodGradeDisciplinaEtapa_Frente->setValue($sessao['CodGradeDisciplinaEtapa_Frente'] ?? '');
                $NomeDisciplina->setValue($sessao['NomeDisciplina'] ?? '');
                $Periodo->setValue($sessao['Periodoturma'] ?? '');
                $NomeCurso->setValue($sessao['NomeCurso'] ?? '');
            }
        
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
        
        $output_type->addItems(array('pdf'=>'PDF'));
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');
        
        // add the action button
        $btn = $this->form->addQuickAction(('Imprimir Papeleta'), new TAction(array($this, 'onGenerate')), 'fa:cog');
        $btn->class = 'btn btn-warning btn-sm';

        $this->form->addQuickAction('Voltar para a Lista de Disciplinas',  new TAction(array('VwProfessordisciplinassemestreList','onReload')), 'fa:list blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
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
            TTransaction::open('dados_fei');
            
            $formdata = $this->form->getData();
            
            $repository = new TRepository('VwPapeleta');
            $criteria   = new TCriteria;
            
            $criteria->setProperty('order', 'Nome');  
            $criteria->setProperty('direction', 'asc');  
            
            if (!empty($formdata->CodTurmaetapa))
            {
                $criteria->add(new TFilter('CodTurmaetapa', '=', $formdata->CodTurmaetapa));
            }
            if (!empty($formdata->CodDisciplina))
            {
                $criteria->add(new TFilter('CodDisciplina', '=', $formdata->CodDisciplina));
            }
            if (!empty($formdata->Avaliacao))
            {
                $criteria->add(new TFilter('Avaliacao', '=', $formdata->Avaliacao));
            }
            if (!empty($formdata->CodGradeDisciplinaEtapa_Frente))
            {
                $criteria->add(new TFilter('CodGradeDisciplinaEtapa_Frente', '=', $formdata->CodGradeDisciplinaEtapa_Frente));
            }
                        
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                // AGRUPAMENTO CONTRA REPETIÇÕES
                $alunos_filtrados = array();
                foreach ($objects as $obj) {
                    $alunos_filtrados[$obj->codaluno] = $obj;
                }

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

                // LÊ A SESSÃO ÚNICA CENTRALIZADA
                $sessao = TSession::getValue('sessao_papeleta_unificada');

                $NomeDisciplina = $formdata->NomeDisciplina ?? ($sessao['NomeDisciplina'] ?? '');
                $Periodo        = $formdata->Periodoturma ?? ($sessao['Periodoturma'] ?? '');
                $NomeCurso      = $formdata->NomeCurso ?? ($sessao['NomeCurso'] ?? '');
                $Bimestre       = $formdata->Avaliacao ?? '';

                $Etapa          = $sessao['Etapa'] ?? '';
                $Identificacao  = $sessao['Identificacao'] ?? '';
                $NomeEntidade   = $sessao['NomeEntidade'] ?? '';
                $NomeProfessor  = $sessao['NomeProfessor'] ?? '';

                if (empty($NomeProfessor) && !empty($objects)) {
                    $NomeProfessor = $objects[0]->NomeProfessor ?? '';
                }
                if (empty($NomeEntidade) && !empty($objects)) {
                    $NomeEntidade = $objects[0]->NomeEntidade ?? '';
                }

                // create the document styles
                $tr->addStyle('title', 'Arial', '10', 'B',   '#000000', '#E8E8E8');
                $tr->addStyle('datap', 'Arial', '9', '',    '#000000', '#EEEEEE');
                $tr->addStyle('datai', 'Arial', '9', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Arial', '16', '',   '#ffffff', '#A9A9A9');
                $tr->addStyle('header2', 'Arial', '10', 'B',   '#000000', '#E8E8E8');
                $tr->addStyle('footer', 'Arial', '9', 'I',  '#000000', '#A9A9A9');
                $tr->addStyle('footer2', 'Arial', '24', 'I',  '#ffffff', '#ffffff');
                
                // Cabeçalho
                $tr->addRow();
                $tr->addCell('FUNDAÇÃO EDUCACIONAL DE ITUVERAVA', 'center', 'header', 8);
                
                $tr->addRow();
                $tr->addCell(!empty($NomeEntidade) ? $NomeEntidade : 'F.E.I.', 'center', 'header2', 8);
                
                $tr->addRow();
                $tr->addCell('Papeleta Bimestral', 'center', 'header', 8);
                
                $tr->addRow();
                $tr->addCell('Bimestre:', 'right', 'header2', 2);
                $tr->addCell($Bimestre . 'º Bimestre', 'left', 'header2', 6);
                
                $tr->addRow();
                $tr->addCell($NomeCurso, 'center', 'header2', 8);
                
                $tr->addRow();
                $tr->addCell('Disciplina:', 'left', 'header2', 1);
                $tr->addCell($NomeDisciplina, 'left', 'header2', 2);
                $tr->addCell('Turno:', 'left', 'header2', 1);
                $tr->addCell($Periodo, 'center', 'header2', 1);
                $tr->addCell('Ciclo:', 'left', 'header2', 1);
                $tr->addCell($Etapa . ' ' . $Identificacao, 'center', 'header2', 2);
                
                // Colunas
                $tr->addRow();
                $tr->addCell('Cod.', 'right', 'title');
                $tr->addCell('Nome', 'left', 'title');
                $tr->addCell('Nota', 'center', 'title');
                $tr->addCell('Faltas', 'center', 'title');
                $tr->addCell('Freq.', 'center', 'title');
                $tr->addCell('Media Final', 'center', 'title');
                $tr->addCell('Result.', 'left', 'title');
                $tr->addCell('Disc.', 'left', 'title');
                
                $colour = FALSE;
                
                // Listagem limpa dos alunos
                foreach ($alunos_filtrados as $object)
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
                
                // Rodapé
                $tr->addRow();
                $tr->addCell(date('d-m-Y h:i:s'), 'center', 'footer', 8);
                $tr->addRow();
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('______________', 'center', 'footer2', 8);
                $tr->addRow();
                $tr->addCell($NomeProfessor, 'center', 'footer', 8);

                if (!file_exists("app/output/VwPapeleta.{$format}") OR is_writable("app/output/VwPapeleta.{$format}"))
                {
                    $tr->save("app/output/VwPapeleta.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/VwPapeleta.{$format}");
                }
                
                parent::openFile("app/output/VwPapeleta.{$format}");
                
                new TMessage('info', 'Papeleta gerada com sucesso!');
            }
            else
            {
                new TMessage('waring', 'NÃO EXISTEM NOTAS OU ALUNOS PARA ESTA DISCIPLINA NESTE BIMESTRE!');
            }
    
            $this->form->setData($formdata);
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}