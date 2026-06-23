<?php
/**
 * VwPapeletaReportSemestre1 Report
 * @author  FEI Team
 */
class VwPapeletaReportAll extends TPage
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
        $this->form = new TQuickForm('form_VwPapeleta_report_all');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        
        $this->form->setFormTitle('Papeleta - 1º e 2º Bimestre');

        // create the form fields
        $CodTurmaetapa = new TEntry('CodTurmaetapa');
        $CodDisciplina = new TEntry('CodDisciplina');
        $CodGradeDisciplinaEtapa_Frente = new TEntry('CodGradeDisciplinaEtapa_Frente');
        $NomeDisciplina = new TEntry('NomeDisciplina');
        $Periodo = new TEntry('Periodoturma');
        $NomeCurso = new TEntry('NomeCurso');
        $output_type = new TRadioGroup('output_type');
         
        $this->form->addQuickField('CodTurmaetapa', $CodTurmaetapa, '100%');
        $this->form->addQuickField('CodDisciplina', $CodDisciplina, '100%');
        $this->form->addQuickField('CodGradeDisciplinaEtapa_Frente', $CodGradeDisciplinaEtapa_Frente, '100%');
        $this->form->addQuickField('NomeDisciplina', $NomeDisciplina, '100%');
        $this->form->addQuickField('Periodo', $Periodo, '100%');
        $this->form->addQuickField('NomeCurso', $NomeCurso, '100%');
        $this->form->addQuickField('Formato', $output_type, '100%');

        $output_type->addItems(array('html' => 'HTML', 'pdf' => 'PDF'));
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');
        
        $CodTurmaetapa->setEditable(FALSE);
        $CodDisciplina->setEditable(FALSE);
        $CodGradeDisciplinaEtapa_Frente->setEditable(FALSE);
        $NomeDisciplina->setEditable(FALSE);
        $Periodo->setEditable(FALSE);
        $NomeCurso->setEditable(FALSE);

        $this->form->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGenerate')), 'far:file-pdf red');
        $this->form->addQuickAction('Voltar para a Listagem', new TAction(array('VwProfessordisciplinassemestreList','onReload')), 'fas:list blue');

        $sessao_papeleta = TSession::getValue('sessao_papeleta');
        
        $this->form->setData((object)$sessao_papeleta);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TPanelGroup::pack('Relatório do 1º e 2º Bimestre', $this->form));
        
        parent::add($container);
    }

    /**
     * Generate the report
     */
    function onGenerate($param = NULL)
    {
        try
        {
            $formdata = $this->form->getData();
            $format = $formdata->output_type;
            
            TTransaction::open('dados_fei');
            
            $sessao_papeleta = TSession::getValue('sessao_papeleta');
            $CodDisciplina  = $sessao_papeleta["CodDisciplina"];
            $codturmaetapa  = $sessao_papeleta["CodTurmaetapa"];
            $NomeProfessor  = $sessao_papeleta["NomeProfessor"];
            $Identificacao  = $sessao_papeleta["Identificacao"];
            $NomeEntidade   = $sessao_papeleta["NomeEntidade"];
            $Periodo        = $sessao_papeleta["Periodo"] ?? ($sessao_papeleta["Periodoturma"] ?? '');
            $NomeDisciplina = $sessao_papeleta["NomeDisciplina"];
            $CodGradeDisciplinaEtapa_Frente = $sessao_papeleta["key"] ?? ($sessao_papeleta["CodGradeDisciplinaEtapa_Frente"] ?? '');

            // Carrega os alunos matriculados na disciplina/turma através de VwAlunosnotas
            $repository = new TRepository('VwAlunosnotas');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodDisciplina', '=', $CodDisciplina));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $codturmaetapa));
            $criteria->setProperty('order', 'Ordem, Nome');
            
            $objects = $repository->load($criteria, FALSE);
            
            if ($objects)
            {
                // Ajustado para 9 colunas
                $widths = array(70, 270, 65, 65, 65, 65, 80, 80, 80);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths, 'L', 'A4');
                        break;
                }
                
                if (!empty($tr))
                {
                    // Relatório Estilos
                    $tr->addStyle('title', 'Arial', '12', 'B',   '#ffffff', '#3b5998');
                    $tr->addStyle('header', 'Arial', '10', 'B',  '#555555', '#e3e3e3');
                    $tr->addStyle('datai', 'Arial', '10', '',    '#000000', '#ffffff');
                    $tr->addStyle('datae', 'Arial', '10', '',    '#000000', '#eeeeee');
                    $tr->addStyle('footer', 'Arial', '10', 'I',  '#000000', '#e3e3e3');
                    $tr->addStyle('footer2', 'Arial', '10', 'B', '#000000', '#ffffff');

                    // Cabeçalho da Papeleta
                    $tr->addRow();
                    $tr->addCell($NomeEntidade, 'center', 'title', 9);
                    $tr->addRow();
                    $tr->addCell('PAPELETA DE LANÇAMENTO DE NOTAS E FALTAS', 'center', 'header', 9);
                    
                    $tr->addRow();
                    $tr->addCell('CURSO:', 'left', 'datae', 1);
                    $tr->addCell($sessao_papeleta["NomeCurso"] ?? '', 'left', 'datai', 5);
                    $tr->addCell('TURMA:', 'left', 'datae', 1);
                    $tr->addCell($Identificacao, 'left', 'datai', 2);
                    
                    $tr->addRow();
                    $tr->addCell('DISCIPLINA:', 'left', 'datae', 1);
                    $tr->addCell($NomeDisciplina, 'left', 'datai', 5);
                    $tr->addCell('PERÍODO:', 'left', 'datae', 1);
                    $tr->addCell($Periodo, 'left', 'datai', 2);
                    
                    // Títulos das Colunas
                    $tr->addRow();
                    $tr->addCell('Código', 'center', 'header');
                    $tr->addCell('Nome do Aluno', 'left', 'header');
                    $tr->addCell('Nota 1ºB', 'center', 'header');
                    $tr->addCell('Faltas 1ºB', 'center', 'header');
                    $tr->addCell('Nota 2ºB', 'center', 'header');
                    $tr->addCell('Faltas 2ºB', 'center', 'header');
                    $tr->addCell('Média Final', 'center', 'header');
                    $tr->addCell('Situação', 'center', 'header');
                    $tr->addCell('Disc.', 'center', 'header');
                    
                    $colour = FALSE;
                    
                    foreach ($objects as $object)
                    {
                        $style = $colour ? 'datae' : 'datai';
                        
                        $nota1 = '-'; $faltas1 = '0';
                        $nota2 = '-'; $faltas2 = '0';
                        $mediaFinal = '-';
                        
                        // 1. Busca dados específicos do 1º Bimestre (Frente)
                        $repoNotas = new TRepository('FiNotasfaltasFrente');
                        $crit1 = new TCriteria;
                        $crit1->add(new TFilter('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa));
                        $crit1->add(new TFilter('CodDisciplina', '=', $object->CodDisciplina));
                        $crit1->add(new TFilter('CodGradeDisciplinaEtapa_Frente', '=', $CodGradeDisciplinaEtapa_Frente));
                        $crit1->add(new TFilter('Avaliacao', '=', '1')); 
                        $res1 = $repoNotas->load($crit1);
                        if ($res1) {
                            $nota1 = !is_null($res1[0]->Nota1) ? $res1[0]->Nota1 : '-';
                            $faltas1 = !is_null($res1[0]->Faltas) ? $res1[0]->Faltas : '0';
                        }
                        
                        // 2. Busca dados específicos do 2º Bimestre (Frente)
                        $crit2 = new TCriteria;
                        $crit2->add(new TFilter('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa));
                        $crit2->add(new TFilter('CodDisciplina', '=', $object->CodDisciplina));
                        $crit2->add(new TFilter('CodGradeDisciplinaEtapa_Frente', '=', $CodGradeDisciplinaEtapa_Frente));
                        $crit2->add(new TFilter('Avaliacao', '=', '2')); 
                        $res2 = $repoNotas->load($crit2);
                        if ($res2) {
                            $nota2 = !is_null($res2[0]->Nota1) ? $res2[0]->Nota1 : '-';
                            $faltas2 = !is_null($res2[0]->Faltas) ? $res2[0]->Faltas : '0';
                        }
                        
                        // CORREÇÃO DA MÉDIA FINAL: 
                        // Como a média global do período letivo fica salva na tabela FI_NotasFaltas (geral)
                        $repoMedia = new TRepository('FiNotasfaltas');
                        $critMedia = new TCriteria;
                        $critMedia->add(new TFilter('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa));
                        $critMedia->add(new TFilter('CodDisciplina', '=', $object->CodDisciplina));
                        $resMedia = $repoMedia->load($critMedia);
                        if ($resMedia && !is_null($resMedia[0]->Media)) {
                            $mediaFinal = $resMedia[0]->Media;
                        }
                        
                        // Transformer da Situação
                        $situacaoFormatada = '';
                        switch (trim(strtoupper($object->Resultado))) {
                            case 'A': case 'AP': case 'APROVADO': $situacaoFormatada = 'Aprovado'; break;
                            case 'R': case 'RE': case 'REPROVADO': $situacaoFormatada = 'Reprovado'; break;
                            case 'E': case 'EXAME': $situacaoFormatada = 'Exame'; break;
                            case 'DP': case 'DEPENDENCIA': $situacaoFormatada = 'Dependência'; break;
                            case 'RF': case 'REPROVADO POR FALTA': $situacaoFormatada = 'Rep. Falta'; break;
                            case 'TR': case 'TRANCADO': $situacaoFormatada = 'Trancado'; break;
                            case 'MA': case 'MATRICULADO': $situacaoFormatada = 'Matriculado'; break;
                            default: $situacaoFormatada = !empty($object->Resultado) ? $object->Resultado : 'Pendente'; break;
                        }

                        $tipoDisciplinaFormatada = '';
                        switch (trim(strtoupper($object->TipoDis))) {
                            case 'A': case 'AT': case 'ATUAL': $tipoDisciplinaFormatada = 'Atual'; break;
                            case 'DP': case 'DEPENDENCIA': $tipoDisciplinaFormatada = 'Dependência'; break;
                            case 'AD': case 'ADAPTADO': $tipoDisciplinaFormatada = 'Adaptado'; break;
                            case 'TR': case 'TRANCADO': $tipoDisciplinaFormatada = 'Trancado'; break;
                            default: $tipoDisciplinaFormatada = !empty($object->TipoDis) ? $object->TipoDis : '?'; break;
                        }

                        // Imprime a linha do aluno
                        $tr->addRow();
                        $tr->addCell($object->Codaluno, 'center', $style);
                        $tr->addCell($object->Nome, 'left', $style);
                        $tr->addCell($nota1, 'center', $style);
                        $tr->addCell($faltas1, 'center', $style);
                        $tr->addCell($nota2, 'center', $style);
                        $tr->addCell($faltas2, 'center', $style);
                        $tr->addCell($mediaFinal, 'center', $style); // Inserida a Média correta obtida via repositório
                        $tr->addCell($situacaoFormatada, 'center', $style);
                        $tr->addCell($tipoDisciplinaFormatada, 'center', $style); // Ajustado para ler 'TipoDis'
                        
                        $colour = !$colour;
                    }
                    
                    // Rodapé
                    $tr->addRow();
                    $tr->addCell(date('d-m-Y h:i:s'), 'center', 'footer', 9);
                    $tr->addRow(); $tr->addRow(); $tr->addRow();
                    $tr->addRow();
                    $tr->addCell('______________', 'center', 'footer2', 9);
                    $tr->addRow();
                    $tr->addCell($NomeProfessor, 'center', 'footer', 9);
                    
                    if (!file_exists("app/output/VwPapeletaAll.{$format}") OR is_writable("app/output/VwPapeletaAll.{$format}"))
                    {
                        $tr->save("app/output/VwPapeletaAll.{$format}");
                    }
                    else
                    {
                        throw new Exception(_t('Permission denied') . ': ' . "app/output/VwPapeletaAll.{$format}");
                    }
                    
                    parent::openFile("app/output/VwPapeletaAll.{$format}");
                    new TMessage('info', 'Papeleta do 1º e 2º Bimestre gerada com sucesso!');
                }
            }
            else
            {
                new TMessage('warning', 'NÃO EXISTEM NOTAS OU ALUNOS PARA ESTA DISCIPLINA!');
            }
            
            $this->form->setData($formdata);
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}