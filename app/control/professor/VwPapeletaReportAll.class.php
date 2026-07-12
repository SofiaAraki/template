<?php
/**
 * VwPapeletaReportAll
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

        $this->form->addQuickAction('Voltar', new TAction(array('HorarioAulasList','onReload')), 'fa:arrow-left blue');
        $this->form->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGenerate')), 'far:file-pdf red');

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
            $Etapa          = $sessao['Etapa'] ?? '';
            $NomeDisciplina = $sessao_papeleta["NomeDisciplina"];

            // Carrega os alunos matriculados na disciplina/turma através de VwAlunosnotas
            $repository = new TRepository('VwAlunosnotas');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodDisciplina', '=', $CodDisciplina));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $codturmaetapa));
            $criteria->setProperty('order', 'Ordem, Nome');
            
            $objects = $repository->load($criteria, FALSE);
            
            if ($objects)
            {
                $widths = array(70, 220, 60, 60, 60, 60, 60, 70, 70, 70);
                
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
                    // Ajuste de Estilos oficiais com a identidade unificada FAFRAM
                    $tr->addStyle('title', 'Arial', '12', 'B', '#ffffff', '#024287');     // Azul Fafram Principal
                    $tr->addStyle('meta_header', 'Arial', '10', '', '#222222', '#f4f6f9'); // Fundo cinza claro corporativo para metadados
                    $tr->addStyle('header', 'Arial', '10', 'B', '#ffffff', '#024287');    // Cabeçalho em Azul Fafram para colunas
                    $tr->addStyle('datap', 'Arial', '10', '', '#333333', '#ffffff');       // Linha de dados comum branca
                    $tr->addStyle('datai', 'Arial', '10', '', '#333333', '#f4f6f9');       // Linha intercalada cinza claro corporativo
                    $tr->addStyle('footer', 'Arial', '10', 'I', '#666666', '#ffffff');
                    $tr->addStyle('footer2', 'Arial', '10', 'B', '#333333', '#ffffff');

                    // Cabeçalho da Papeleta
                    $tr->addRow();
                    $tr->addCell('FACULDADE DR. FRANCISCO MAEDA - FAFRAM', 'center', 'title', 10);
                    $tr->addRow();
                    $tr->addCell('RELATÓRIO DE LANÇAMENTO DE NOTAS E FALTAS', 'center', 'header', 10);
                    
                    $tr->addRow();
                    $tr->addCell('CURSO:', 'left', 'meta_header', 1);
                    $tr->addCell($sessao_papeleta["NomeCurso"] ?? '', 'left', 'meta_header', 5);
                    $tr->addCell('TURMA:', 'left', 'meta_header', 1);
                    $tr->addCell($Identificacao, 'left', 'meta_header', 3);
                    
                    $tr->addRow();
                    $tr->addCell('DISCIPLINA:', 'left', 'meta_header', 1);
                    $tr->addCell($NomeDisciplina, 'left', 'meta_header', 5);
                    $tr->addCell('PERÍODO:', 'left', 'meta_header', 1);
                    $tr->addCell($Periodo, 'center', 'meta_header', 1);
                    $tr->addCell('ETAPA: ', 'center', 'meta_header', 1);
                    $tr->addCell($Etapa, 'center', 'meta_header', 1);
                    
                    // Títulos das Colunas
                    $tr->addRow();
                    $tr->addCell('Código', 'center', 'header');
                    $tr->addCell('Nome do Aluno', 'left', 'header');
                    $tr->addCell('Nota 1ºB', 'center', 'header');
                    $tr->addCell('Faltas 1ºB', 'center', 'header');
                    $tr->addCell('Nota 2ºB', 'center', 'header');
                    $tr->addCell('Faltas 2ºB', 'center', 'header');
                    $tr->addCell('Freq. %', 'center', 'header');
                    $tr->addCell('Média Final', 'center', 'header');
                    $tr->addCell('Situação', 'center', 'header');
                    $tr->addCell('Mat.', 'center', 'header');
                    
                    $colour = FALSE;
                    
                    foreach ($objects as $object)
                    {
                        $style = $colour ? 'datai' : 'datap';
                        
                        $nota1 = '-'; $faltas1 = '0';
                        $nota2 = '-'; $faltas2 = '0';
                        $mediaFinal = '-';
                        $frequencia = '-';
                        
                        // CORREÇÃO 1: Consultar Notas e Faltas diretamente de FiNotasFaltas (Igual ao Boletim)
                        $NotasFaltas = FiNotasFaltas::where('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa)
                                                    ->where('CodDisciplina', '=', $object->CodDisciplina)
                                                    ->load();
                        
                        if ($NotasFaltas) {
                            foreach ($NotasFaltas as $NotaFalta) {
                                if ($NotaFalta->Avaliacao == 1) {
                                    $nota1 = !is_null($NotaFalta->Nota1) ? $NotaFalta->Nota1 : '-';
                                    $faltas1 = !is_null($NotaFalta->Faltas) ? $NotaFalta->Faltas : '0';
                                }
                                if ($NotaFalta->Avaliacao == 2) {
                                    $nota2 = !is_null($NotaFalta->Nota1) ? $NotaFalta->Nota1 : '-';
                                    $faltas2 = !is_null($NotaFalta->Faltas) ? $NotaFalta->Faltas : '0';
                                }
                            }
                        }
                        
                        // CORREÇÃO 2: Buscar Frequência e Média Final da View VwFiDisciplinasATADDP
                        $repoSpecs = new TRepository('VwFiDisciplinasATADDP');
                        $critSpecs = new TCriteria;
                        $critSpecs->add(new TFilter('CodMatriculaEtapa', '=', $object->CodMatriculaEtapa));
                        $critSpecs->add(new TFilter('CodDisciplina', '=', $object->CodDisciplina));
                        $resSpecs = $repoSpecs->load($critSpecs);
                        
                        if ($resSpecs) {
                            $frequencia = !is_null($resSpecs[0]->Frequencia) ? $resSpecs[0]->Frequencia . '%' : '-';
                            $mediaFinal = !is_null($resSpecs[0]->MediaSem) ? $resSpecs[0]->MediaSem : '-';
                        }
                        
                        // Transformers com definição de cores dinâmicas
                        $situacaoFormatada = '';
                        $sufixoCor = ''; // Vai ajudar a mapear o estilo de cor

                        switch (trim(strtoupper($object->Resultado))) {
                            case 'A': case 'AP': case 'APROVADO': 
                                $situacaoFormatada = 'Aprovado'; 
                                $sufixoCor = '_aprovado'; // Verde
                                break;
                            case 'R': case 'RP': case 'REPROVADO': 
                                $situacaoFormatada = 'Reprovado'; 
                                $sufixoCor = '_reprovado'; // Vermelho
                                break;
                            case 'E': case 'EXAME': 
                                $situacaoFormatada = 'Exame'; 
                                $sufixoCor = '_exame'; // Amarelo/Laranja
                                break;
                            case 'DP': case 'DEPENDENCIA': 
                                $situacaoFormatada = 'Dependência'; 
                                $sufixoCor = '_exame';
                                break;
                            case 'RF': case 'REPROVADO POR FALTA': 
                                $situacaoFormatada = 'Rep. Falta'; 
                                $sufixoCor = '_reprovado';
                                break;
                            case 'TR': case 'TRANCADO': 
                                $situacaoFormatada = 'Trancado'; 
                                break;
                            case 'MA': case 'MATRICULADO': 
                                $situacaoFormatada = 'Matriculado'; 
                                break;
                            default: 
                                $situacaoFormatada = !empty($object->Resultado) ? $object->Resultado : 'Pendente'; 
                                break;
                        }

                        // Cria os estilos de cores dinamicamente para a célula da situação
                        // Mantém o fundo original da linha ($style) para o relatório não perder o zebrado
                        $bg_atual = ($style == 'datai') ? '#f4f6f9' : '#ffffff';
                        $estiloSituacao = $style; // Padrão se não tiver cor específica

                        if (!empty($sufixoCor)) {
                            $estiloSituacao = $style . $sufixoCor;
                            
                            if ($sufixoCor == '_aprovado') {
                                $tr->addStyle($estiloSituacao, 'Arial', '10', '', '#008000', $bg_atual);
                            } elseif ($sufixoCor == '_reprovado') {
                                $tr->addStyle($estiloSituacao, 'Arial', '10', '', '#ff0000', $bg_atual);
                            } elseif ($sufixoCor == '_exame') {
                                $tr->addStyle($estiloSituacao, 'Arial', '10', '', '#ffde21', $bg_atual);
                            }
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
                        $tr->addCell($frequencia, 'center', $style); 
                        $tr->addCell($mediaFinal, 'center', $style); 

                        // Passamos o estilo customizado e colorido para a situação
                        $tr->addCell($situacaoFormatada, 'center', $estiloSituacao);

                        $tr->addCell($tipoDisciplinaFormatada, 'center', $style); 

                        $colour = !$colour;
                    }
                    
                    // Rodapé
                    $tr->addRow();
                    $tr->addCell('Documento gerado em: ' . date('d/m/Y H:i:s'), 'center', 'footer', 10);
                    $tr->addRow(); $tr->addRow(); $tr->addRow();
                    $tr->addCell('____________________________________________________', 'center', 'footer2', 10);
                    $tr->addRow();
                    $tr->addCell('Docente: ' . $NomeProfessor, 'center', 'footer', 10);
                    
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
                new TMessage('warning', 'Não existe lançamento registrado!');
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