<?php

class MatriculaAlunoForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FiMatriculaEtapa');
        $this->form->setFormTitle('Movimentação / Formulário de Matrícula');
        
        // ---- INSTANCIAÇÃO DOS CAMPOS ----
        
        // Aba 1
        $cod_mat_etapa   = new TEntry('CodMatriculaEtapa'); $cod_mat_etapa->setEditable(FALSE);
        $cod_mat_inicial = new TEntry('CodMatriculaInicial');
        $aluno           = new TDBUniqueSearch('Codaluno', 'dados_fei', 'FiAluno', 'Codaluno', 'Nome');
        $aluno->setMinLength(3); $aluno->setMask('{Nome} ({Codaluno})');
        $turma           = new TDBCombo('CodTurmaetapa', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $grade_curso     = new TDBCombo('CodGradecurso', 'dados_fei', 'FiGradeCurso', 'CodGradecurso', 'CodGradecurso');
        $dt_matricula    = new TDate('DataMatricula'); $dt_matricula->setMask('dd/mm/yyyy');
        $ingresso        = new TCombo('Ingresso'); $ingresso->addItems(['01'=>'Regular', '02'=>'Transferência', '03'=>'Retorno']);
        $situacao        = new TCombo('Situacao'); $situacao->addItems(['MA'=>'Matriculado', 'TR'=>'Trancado', 'CA'=>'Cancelado', 'TE'=>'Transferido']);
        $dt_situacao     = new TDate('SituacaoData'); $dt_situacao->setMask('dd/mm/yyyy');
        $confirmada      = new TRadioGroup('ConfirmacaoMatricula'); $confirmada->addItems(['S'=>'Sim', 'N'=>'Não']); $confirmada->setLayout('horizontal');
        
        // Aba 2
        $qtd_disc    = new TEntry('QtdeDisciplinaEtapa'); $qtd_disc->setNumericMask(0, '', '');
        $qtd_dep     = new TEntry('QtdeDependenciaEtapa'); $qtd_dep->setNumericMask(0, '', '');
        $qtd_adapt   = new TEntry('QtdeAdaptacaoEtapa'); $qtd_adapt->setNumericMask(0, '', '');
        $res_final   = new TCombo('ResultadoFinal'); $res_final->addItems(['AP'=>'Aprovado', 'RE'=>'Reprovado', 'DP'=>'Dependência']);
        $res_qtd_dep = new TEntry('ResultadoQtdeDependencia');
        $media_freq  = new TEntry('MediaFreq');
        $total_ac_pi = new TEntry('TotalAcertosPI');
        $media_pi    = new TEntry('MediaPI');
        $percent_pi  = new TEntry('PercentualPI');
        $nota_ni     = new TEntry('NotaNI');
        
        // Aba 3
        $num_seq        = new TEntry('NumeroSeq'); $num_seq->setEditable(FALSE);
        $n_reg          = new TEntry('NReg');
        $cod_contrato   = new TEntry('CodContrato');
        $sit_tesouraria = new TEntry('SituacaoTesouraria');
        $sit_outros     = new TEntry('SituacaoOutros');
        $dt_update      = new TEntry('DataAtualizacao'); $dt_update->setEditable(FALSE);
        $obs1           = new TText('Observacao1'); $obs1->setSize('100%', 50);
        $obs2           = new TText('Observacao2'); $obs2->setSize('100%', 50);
        $obs3           = new TText('Observacao3'); $obs3->setSize('100%', 50);
        $obs_geral      = new TEntry('Observacao');

        // ---- DISTRIBUIÇÃO DOS CAMPOS NAS ABAS SEQUENCIAIS ----
        
        // 1. Aba: Vínculo e Ingresso
        $this->form->appendPage('Vínculo e Ingresso');
        $this->form->addFields([new TLabel('Nº Matrícula Etapa:')], [$cod_mat_etapa], [new TLabel('Nº Matrícula Inicial (Pai): *')], [$cod_mat_inicial]);
        $this->form->addFields([new TLabel('Aluno:')], [$aluno]);
        $this->form->addFields([new TLabel('Turma Alocada:')], [$turma], [new TLabel('Grade do Curso:')], [$grade_curso]);
        $this->form->addFields([new TLabel('Data da Matrícula:')], [$dt_matricula], [new TLabel('Forma Ingresso:')], [$ingresso]);
        $this->form->addFields([new TLabel('Situação Atual:')], [$situacao], [new TLabel('Data da Situação:')], [$dt_situacao]);
        $this->form->addFields([new TLabel('Matrícula Confirmada?')], [$confirmada]);

        // 2. Aba: Desempenho e Estatísticas
        $this->form->appendPage('Desempenho e Estatísticas');
        $this->form->addFields([new TLabel('Qtd Disciplinas Etapa:')], [$qtd_disc], [new TLabel('Qtd Dependências:')], [$qtd_dep], [new TLabel('Qtd Adaptações:')], [$qtd_adapt]);
        $this->form->addFields([new TLabel('Média Frequência (%):')], [$media_freq], [new TLabel('Resultado Final:')], [$res_final]);
        $this->form->addFields([new TLabel('Qtd DPs Resultantes:')], [$res_qtd_dep]);
        $this->form->addFields([new TLabel('Acertos Prova Integrada:')], [$total_ac_pi], [new TLabel('Média P.I.:')], [$media_pi], [new TLabel('% P.I.:')], [$percent_pi]);
        $this->form->addFields([new TLabel('Nota N.I.:')], [$nota_ni]);

        // 3. Aba: Observações e Auditoria
        $this->form->appendPage('Observações e Auditoria');
        $this->form->addFields([new TLabel('Nº Sequencial:')], [$num_seq], [new TLabel('Nº Registro (NReg):')], [$n_reg]);
        $this->form->addFields([new TLabel('Código Contrato:')], [$cod_contrato], [new TLabel('Situação Tesouraria:')], [$sit_tesouraria]);
        $this->form->addFields([new TLabel('Última Atualização:')], [$dt_update], [new TLabel('Situação Outros:')], [$sit_outros]);
        $this->form->addFields([new TLabel('Observação Resumida:')], [$obs_geral]);
        $this->form->addFields([new TLabel('Observação Histórico 1:')], [$obs1]);
        $this->form->addFields([new TLabel('Observação Histórico 2:')], [$obs2]);
        $this->form->addFields([new TLabel('Observação Histórico 3:')], [$obs3]);

        // Mapeamento para requisições globais do formulário
        $this->form->setFields([
            $cod_mat_etapa, $cod_mat_inicial, $aluno, $turma, $grade_curso, $dt_matricula, $ingresso, $situacao, $dt_situacao, $confirmada,
            $qtd_disc, $qtd_dep, $qtd_adapt, $res_final, $res_qtd_dep, $media_freq, $total_ac_pi, $media_pi, $percent_pi, $nota_ni,
            $num_seq, $n_reg, $cod_contrato, $sit_tesouraria, $sit_outros, $dt_update, $obs1, $obs2, $obs3, $obs_geral
        ]);

        // Ações globais do rodapé
        $this->form->addAction('Voltar', new TAction(['MatriculaAlunoList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Matrícula', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $matricula = new FiMatriculaEtapa;
            $matricula->fromArray((array) $data);
            
            $matricula->CodOperador    = TSession::getValue('userid');
            $matricula->DataAtualizacao = date('Y-m-d H:i:s');
            
            if (!empty($matricula->DataMatricula)) {
                $matricula->DataMatricula = TDate::convertToMask($matricula->DataMatricula, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            if (!empty($matricula->SituacaoData)) {
                $matricula->SituacaoData = TDate::convertToMask($matricula->SituacaoData, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
            $matricula->store();
            
            $data->CodMatriculaEtapa = $matricula->CodMatriculaEtapa;
            $data->DataAtualizacao   = $matricula->DataAtualizacao;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Matrícula atualizada com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onEdit($param)
    {
        if (isset($param['key'])) {
            try {
                TTransaction::open('dados_fei');
                $object = new FiMatriculaEtapa($param['key']);
                
                if (!empty($object->DataMatricula)) {
                    $object->DataMatricula = TDate::convertToMask($object->DataMatricula, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                if (!empty($object->SituacaoData)) {
                    $object->SituacaoData = TDate::convertToMask($object->SituacaoData, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}