<?php
/**
 * ListaAlunosCompletoReport Report
 * @author  <your name here>
 */
class ListaAlunosCompletoReport extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        TTransaction::open('dados_fei');
            
            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
            $CodTurma       = $sessao_diarioclasse["CodTurmaetapa"] ?? null;
            $CodDisc        = $sessao_diarioclasse["CodDisciplina"] ?? null;
            $Turno          = $sessao_diarioclasse["Periodo"] ?? null;
            $NomeDisciplina = $sessao_diarioclasse["NomeDisciplina"] ?? null;
            $Etapa          = $sessao_diarioclasse["Etapa"] ?? null;

             // Conversão do turno
             switch ($Turno) 
             {
                 case 'I':
                      $TurnoCompleto = 'Integral';
                     break;
                 case 'M':
                     $TurnoCompleto = 'Manhã';
                     break;
                 case 'N':
                     $TurnoCompleto = 'Noturno';
                     break;
                 default:
                     $TurnoCompleto = 'Turno inválido';
             }

        TTransaction::close();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_VwAlunosCompleto_report');
        $this->form->setFormTitle('Relatórios - Diário de Classe');
        

        // create the form fields
        $CodTurmaetapa = new THidden('CodTurmaetapa');
        $CodDisciplina = new THidden('CodDisciplina');
        $CodCurso = new THidden('CodCurso');
        $output_type = new TRadioGroup('output_type');

        // add the fields
        $this->form->addFields( [ new TLabel('Disciplina: ') ],  [ '<b>'.$NomeDisciplina ],[ $CodTurmaetapa ] );
        $this->form->addFields( [ new TLabel('Turma:') ], [ $Etapa .'º Ciclo' ] , [ $CodDisciplina ] );
        $this->form->addFields( [ new TLabel('') ], [ $CodCurso ] );
        $this->form->addFields( [ new TLabel('Tipo de Lista:') ], [ $output_type ] );

        $output_type->addValidation('Output', new TRequiredValidator);

        // set sizes
        $CodTurmaetapa->setSize('100%');
        $CodDisciplina->setSize('100%');
        $CodCurso->setSize('100%');
        $output_type->setSize('100%');

        $CodTurmaetapa->setValue($CodTurma);
        $CodTurmaetapa->setEditable(false);
        $CodDisciplina->setValue($CodDisc);
        $CodDisciplina->setEditable(false);
        
        $output_type->addItems(array('pdf'=>'PDF', 'xls' => 'XLS'));
        $output_type->setLayout('horizontal');
        $output_type->setUseButton();
        $output_type->setValue('pdf');
        $output_type->setSize(70);
        
        // add the action button
        $this->form->addActionLink('Voltar',  new TAction(['HorarioAulasList', 'onReload']), 'fa:arrow-left blue');
        $this->form->addAction('Imprimir Lista de Alunos', new TAction(array($this, 'onGenerateListaAlunos')), 'fa:users orange');
        $this->form->addAction('Imprimir Conteúdo Programático', new TAction(array($this, 'onGenerateConteudo')), 'fa:list-ul red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Generate the report
     */
    function onGenerateListaAlunos()
    {
        try
        {
            TTransaction::open('dados_fei_t');
            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
            $Etapa          = $sessao_diarioclasse["Etapa"] ?? '';
            $Turno          = $sessao_diarioclasse["Periodo"] ?? '';
            
            switch ($Turno) 
            {
                 case 'I': $TurnoCompleto = 'Integral'; break;
                 case 'M': $TurnoCompleto = 'Manhã'; break;
                 case 'N': $TurnoCompleto = 'Noturno'; break;
                 default:  $TurnoCompleto = 'Turno inválido';
            }
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $repository = new TRepository('VwAlunosCompleto');
            $criteria   = new TCriteria;
            
            if ($data->CodTurmaetapa) {
                $criteria->add(new TFilter('CodTurmaetapa', 'like', "%{$data->CodTurmaetapa}%"));
            }
            if ($data->CodDisciplina) {
                $criteria->add(new TFilter('CodDisciplina', 'like', "%{$data->CodDisciplina}%"));
            }
            if ($data->CodCurso) {
                $criteria->add(new TFilter('CodCurso', 'like', "%{$data->CodCurso}%"));
            }

            if (empty($param['order'])) {
                $param['order'] = 'Nome';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param);
            $objects = $repository->load($criteria, FALSE);
            $format  = $data->output_type;
            
            if ($objects)
                {
                $NomeDisciplina = '';
                $Curso = '';
                foreach ($objects as $obj) {
                    $NomeDisciplina = $obj->Nomeusual;
                    $Curso = $obj->NomeCurso;
                }
           
                $widths = array(40,250,50,30,200);
                
                switch ($format)
                {
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        $pdf = $tr->getNativeWriter();
                        $pdf->SetAutoPageBreak(true, 10);
                        $pdf->Image('C:\xampp\htdocs\academico\app\images\diario\fafram.jpg');
                        $pdf->Ln(10);
                        break;
                    case 'xls':
                        $tr = new TTableWriterXLS($widths);
                        break;
                }
                
                $tr->addStyle('title', 'Courier', '9', '',   '#000000', '#A3A3A3');
                $tr->addStyle('datap', 'Courier', '9', '',    '#000000', '#EEEEEE', 'LR');
                $tr->addStyle('datai', 'Courier', '9', '',    '#000000', '#ffffff','LR');
                $tr->addStyle('header', 'Courier', '11', '',   '#000000', '#EEEEEE');
                $tr->addStyle('footer', 'Times', '10', 'I',  '#000000', '#A3A3A3');
                
                $tr->addRow();
                $tr->addCell('Disciplina:  '.$NomeDisciplina.'         Curso: '.$Curso, 'left', 'header', 5);
                $tr->addRow();
                $tr->addCell('Turma: ' .$Etapa.'º Ciclo         Turno: ' .$TurnoCompleto, 'left', 'header',5);
                $tr->addRow();
                $tr->addCell('LISTA DE ALUNOS', 'CENTER', 'header',5);

                $tr->addRow();
                $tr->addCell('Cod.', 'right', 'title');
                $tr->addCell('Nome', 'left', 'title');
                $tr->addCell('Matr.', 'left', 'title');
                $tr->addCell('Sit.', 'left', 'title');
                $tr->addCell('Assinatura', 'left', 'title');

                $colour = FALSE;
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->Codaluno, 'right', $style);
                    $tr->addCell($object->Nome, 'left', $style);
                    $tr->addCell($object->TipoDis, 'left', $style);
                    $tr->addCell($object->Situacao, 'left', $style);
                    $tr->addCell('', 'left', $style);
                    
                    $colour = !$colour;
                }
                
                $tr->addRow();
                $tr->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 5); // Corrigido de 13 para 5
                
                if (!file_exists("app/output/VwAlunosCompleto.{$format}") OR is_writable("app/output/VwAlunosCompleto.{$format}")) {
                    $tr->save("app/output/VwAlunosCompleto.{$format}");
                } else {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/VwAlunosCompleto.{$format}");
                }
                
                parent::openFile("app/output/VwAlunosCompleto.{$format}");
                new TMessage('info', 'Relatório gerado. Por favor, habilite os pop-ups.');
            }
            else
            {
                new TMessage('error', 'Nenhum registro encontrado');
            }
    
            $this->form->setData($data);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    function onGenerateConteudo()
    {
        try
        {
            // Pegamos as informações da sessão antes de lidar com banco
            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
            $Etapa          = $sessao_diarioclasse["Etapa"] ?? '';
            $Turno          = $sessao_diarioclasse["Periodo"] ?? '';
            $NomeCurso      = $sessao_diarioclasse["NomeCurso"] ?? '';
            $CodGradeDisciplinaEtapaFrente = $sessao_diarioclasse["CodGradeDisciplinaEtapaFrente"] ?? null;
            
            // CORREÇÃO: Abre e trabalha apenas com o Felabs_DB se a TRepository vem dele
            TTransaction::open('Felabs_DB');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $repository_conteudo = new TRepository('ConteudoDiarioClasse');
            $criteria_conteudo   = new TCriteria;
            
            if ($data->CodTurmaetapa) {
                $criteria_conteudo->add(new TFilter('cod_turma_etapa', '=', "{$data->CodTurmaetapa}"));
            }
            if ($data->CodDisciplina) {
                $criteria_conteudo->add(new TFilter('cod_disciplina', '=', $CodGradeDisciplinaEtapaFrente));
            }

            if (empty($param['order'])) {
                $param['order'] = 'data_aula';
                $param['direction'] = 'asc';
            }
            $criteria_conteudo->setProperties($param);
            $objects = $repository_conteudo->load($criteria_conteudo, FALSE);
            $format  = $data->output_type;
            
            if ($objects)
            {
                $NomeDisciplina = '';
                $Prof = 'Professor Não Informado'; // Fallback estratégico para evitar quebra de variável
                
                foreach ($objects as $obj) {
                    $NomeDisciplina = $obj->nome_disciplina;
                    $Prof = $obj->nome_professor;
                }
           
                $widths = array(40,100,400); // 3 colunas totais
                
                switch ($format)
                {
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        $pdf = $tr->getNativeWriter();
                        $pdf->SetAutoPageBreak(true, 10);
                        $pdf->Image('C:\xampp\htdocs\academico\app\images\diario\fafram.jpg');
                        $pdf->Ln(10);
                        break;
                    case 'xls':
                        $tr = new TTableWriterXLS($widths);
                        break;
                }
                
                $tr->addStyle('title', 'Courier', '11', '',   '#000000', '#EEEEEE');
                $tr->addStyle('datap', 'Courier', '9', '',    '#000000', '#EEEEEE');
                $tr->addStyle('datai', 'Courier', '9', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Courier', '14', '',   '#000000', '#ffffff');
                $tr->addStyle('footer', 'Times', '10', 'I',  '#000000', '#EEEEEE','LR');
                
                $tr->addRow();
                $tr->addCell('Disciplina:  '.$NomeDisciplina.'      Curso: '.$NomeCurso, 'left', 'header', 3);
                $tr->addRow();
                $tr->addCell('Turma: ' .$Etapa.'º Ciclo      Turno: ' .$Turno, 'left', 'header', 3);
                $tr->addRow();
                $tr->addCell('CONTEÚDO PROGRAMÁTICO', 'CENTER', 'header', 3);

                $tr->addRow();
                $tr->addCell('Cod.', 'right', 'title');
                $tr->addCell('Data Aula', 'left', 'title');
                $tr->addCell('Conteúdo.', 'left', 'title');
                
                $colour = FALSE;
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->id, 'right', $style);
                    $tr->addCell($object->data_aula, 'left', $style);
                    $tr->addCell($object->conteudo, 'left', $style);
                    
                    $colour = !$colour;
                }
                
                // CORREÇÃO: Alinhado todos os colspans de 13 para 3
                $tr->addRow();
                $tr->addCell('', 'center', 'footer', 3);
                $tr->addRow();
                $tr->addCell('', 'center', 'footer', 3);
                $tr->addRow();
                $tr->addCell('______________________________________', 'center', 'footer', 3);
                $tr->addRow();
                $tr->addCell($Prof, 'center', 'footer', 3);
                $tr->addRow();
                $tr->addCell(date('d-m-Y h:i:s'), 'center', 'footer', 3);

                if (!file_exists("app/output/VwAlunosCompleto.{$format}") OR is_writable("app/output/VwAlunosCompleto.{$format}")) {
                    $tr->save("app/output/VwAlunosCompleto.{$format}");
                } else {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/VwAlunosCompleto.{$format}");
                }
                
                parent::openFile("app/output/VwAlunosCompleto.{$format}");
                new TMessage('info', 'Relatório gerado. Por favor, habilite os pop-ups.');
            }
            else
            {
                new TMessage('error', 'Nenhum registro encontrado');
            }
    
            $this->form->setData($data);
            TTransaction::close(); // Fecha apenas a conexão correta ativa
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}