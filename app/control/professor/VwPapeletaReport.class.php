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
        $this->form->addQuickAction('Voltar',  new TAction(array('VwProfessordisciplinassemestreList','onReload')), 'fas:arrow-left blue');
        $this->form->addQuickAction('Imprimir Papeleta', new TAction(array($this, 'onGenerate')), 'far:file-pdf red');
        
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

                $widths = array(70, 320, 60, 60, 60, 70, 70, 70); 
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths, 'L', 'A4');
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $tr = new TTableWriterRTF($widths);
                        break;
                }

                if (!empty($tr))
                {
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

                    // Ajuste de Estilos oficiais com a identidade unificada FAFRAM
                    $tr->addStyle('title', 'Arial', '12', 'B', '#ffffff', '#024287');     // Azul Fafram Principal
                    $tr->addStyle('meta_header', 'Arial', '10', '', '#222222', '#f4f6f9'); // Fundo cinza claro corporativo para metadados
                    $tr->addStyle('header', 'Arial', '10', 'B', '#ffffff', '#024287');    // Cabeçalho em Azul Fafram para colunas
                    $tr->addStyle('datap', 'Arial', '10', '', '#333333', '#ffffff');       // Linha de dados comum branca
                    $tr->addStyle('datai', 'Arial', '10', '', '#333333', '#f4f6f9');       // Linha intercalada cinza claro corporativo
                    $tr->addStyle('footer', 'Arial', '10', 'I', '#666666', '#ffffff');
                    $tr->addStyle('footer2', 'Arial', '10', 'B', '#333333', '#ffffff');
                    
                    $tr->addRow();
                    $tr->addCell('FACULDADE DR. FRANCISCO MAEDA - FAFRAM', 'center', 'title', 8);
                    
                    $tr->addRow();
                    $tr->addCell('PAPELETA BIMESTRAL DE LANÇAMENTO DE NOTAS E FALTAS', 'center', 'header', 8);
                    
                    $tr->addRow();
                    $tr->addCell('CURSO:', 'left', 'meta_header', 1);
                    $tr->addCell($NomeCurso, 'left', 'meta_header', 3);
                    $tr->addCell('TURMA: ','center', 'meta_header', 1);
                    $tr->addCell($Identificacao ,'center', 'meta_header', 3);                  
                    
                    $tr->addRow();
                    $tr->addCell('DISCIPLINA:', 'left', 'meta_header', 1);
                    $tr->addCell($NomeDisciplina, 'left', 'meta_header', 3);
                    $tr->addCell('PERIODO: ' . $Periodo, 'center', 'meta_header', 1);
                    $tr->addCell('ETAPA: ' . $Etapa, 'center', 'meta_header', 1);
                    $tr->addCell($Bimestre . 'º Bimestre', 'center', 'meta_header', 2);                    
                    
                    // Colunas
                    $tr->addRow();
                    $tr->addCell('Código', 'center', 'header');
                    $tr->addCell('Nome do Aluno', 'left', 'header');
                    $tr->addCell('Nota', 'center', 'header');
                    $tr->addCell('Faltas', 'center', 'header');
                    $tr->addCell('Freq. %', 'center', 'header');
                    $tr->addCell('Média Final', 'center', 'header');
                    $tr->addCell('Situação', 'center', 'header');
                    $tr->addCell('Mat.', 'center', 'header');
                    
                    $colour = FALSE;
                    
                    foreach ($alunos_filtrados as $object)
                    {
                        $style = $colour ? 'datai' : 'datap';

                        $notaBimestre = '-'; 
                        $faltasBimestre = '0';
                        $mediaFinal = '-';
                        $frequencia = '-';

                        // CORREÇÃO 1: Consultar Notas e Faltas diretamente de FiNotasFaltas baseado no Bimestre atual
                        $NotasFaltas = FiNotasFaltas::where('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa)
                                                    ->where('CodDisciplina', '=', $object->CodDisciplina)
                                                    ->where('Avaliacao', '=', $Bimestre)
                                                    ->load();

                        if ($NotasFaltas) {
                            $notaBimestre = !is_null($NotasFaltas[0]->Nota1) ? $NotasFaltas[0]->Nota1 : '-';
                            $faltasBimestre = !is_null($NotasFaltas[0]->Faltas) ? $NotasFaltas[0]->Faltas : '0';
                        }

                        // CORREÇÃO 2: Buscar Frequência Geral e Média Final Semestral da View VwFiDisciplinasATADDP
                        $repoSpecs = new TRepository('VwFiDisciplinasATADDP');
                        $critSpecs = new TCriteria;
                        $critSpecs->add(new TFilter('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa));
                        $critSpecs->add(new TFilter('CodDisciplina', '=', $object->CodDisciplina));
                        $resSpecs = $repoSpecs->load($critSpecs);
                        
                        if ($resSpecs) {
                            $frequencia = !is_null($resSpecs[0]->Frequencia) ? $resSpecs[0]->Frequencia . '%' : '-';
                            $mediaFinal = !is_null($resSpecs[0]->MediaSem) ? $resSpecs[0]->MediaSem : '-';
                        }

                        // Transformer da Situação
                        $situacaoFormatada = '';
                        $resultadoBruto = $object->resultado ?? ($object->Resultado ?? '');
                        switch (trim(strtoupper($resultadoBruto))) {
                            case 'A': case 'AP': case 'APROVADO': $situacaoFormatada = 'Aprovado'; break;
                            case 'R': case 'RP': case 'REPROVADO': $situacaoFormatada = 'Reprovado'; break;
                            case 'E': case 'EXAME': $situacaoFormatada = 'Exame'; break;
                            case 'DP': case 'DEPENDENCIA': $situacaoFormatada = 'Dependência'; break;
                            case 'RF': case 'REPROVADO POR FALTA': $situacaoFormatada = 'Rep. Falta'; break;
                            case 'TR': case 'TRANCADO': $situacaoFormatada = 'Trancado'; break;
                            case 'MA': case 'MATRICULADO': $situacaoFormatada = 'Matriculado'; break;
                            default: $situacaoFormatada = !empty($resultadoBruto) ? $resultadoBruto : 'Pendente'; break;
                        }

                        // Transformer do Tipo de Disciplina
                        $tipoDisciplinaFormatada = '';
                        $tipoDisBruto = $object->tipodisciplina ?? ($object->TipoDis ?? '');
                        switch (trim(strtoupper($tipoDisBruto))) {
                            case 'A': case 'AT': case 'ATUAL': $tipoDisciplinaFormatada = 'Atual'; break;
                            case 'DP': case 'DEPENDENCIA': $tipoDisciplinaFormatada = 'Dependência'; break;
                            case 'AD': case 'ADAPTADO': $tipoDisciplinaFormatada = 'Adaptado'; break;
                            case 'TR': case 'TRANCADO': $tipoDisciplinaFormatada = 'Trancado'; break;
                            default: $tipoDisciplinaFormatada = !empty($tipoDisBruto) ? $tipoDisBruto : '?'; break;
                        }

                        $tr->addRow();
                        $tr->addCell($object->codaluno, 'center', $style);
                        $tr->addCell($object->Nome, 'left', $style);
                        $tr->addCell($notaBimestre, 'center', $style);
                        $tr->addCell($faltasBimestre, 'center', $style);
                        $tr->addCell($frequencia, 'center', $style);
                        $tr->addCell($mediaFinal, 'center', $style);
                        $tr->addCell($situacaoFormatada, 'center', $style);
                        $tr->addCell($tipoDisciplinaFormatada, 'center', $style);
                        
                        $colour = !$colour;
                    }
                    
                    // Rodapé
                    $tr->addRow();
                    $tr->addCell('Documento gerado em: ' . date('d/m/Y H:i:s'), 'center', 'footer', 8);
                    $tr->addRow(); $tr->addRow(); $tr->addRow(); $tr->addRow(); $tr->addRow();
                    $tr->addCell('____________________________________________________', 'center', 'footer2', 8);
                    $tr->addRow();
                    $tr->addCell('Docente: ' . $NomeProfessor, 'center', 'footer', 8);

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
            }
            else
            {
                new TMessage('warning', 'NÃO EXISTEM NOTAS OU ALUNOS PARA ESTA DISCIPLINA NESTE BIMESTRE!');
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