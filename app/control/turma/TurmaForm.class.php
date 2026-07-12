<?php

class TurmaForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FiTurmaEtapa');
        $this->form->setFormTitle('Cadastro de Turma por Etapa');
        
        // ---- INSTANCIAÇÃO DOS CAMPOS ----
        
        // Aba 1: Estrutura e Localização
        $codturmaetapa = new TEntry('CodTurmaetapa'); $codturmaetapa->setEditable(FALSE);
        $identificacao = new TEntry('Identificacao'); $identificacao->setMaxLength(10);
        $ano           = new TEntry('Ano'); $ano->setMaxLength(4);
        $semestre      = new TCombo('Semestre'); $semestre->addItems(['1'=>'1º Semestre', '2'=>'2º Semestre', '0'=>'Anual']);
        $campus        = new TEntry('Campus');
        $bloco         = new TEntry('Bloco');
        $sala          = new TEntry('Sala');
        $periodo       = new TCombo('Periodo'); $periodo->addItems(['M'=>'Matutino', 'V'=>'Vespertino', 'N'=>'Noturno', 'I'=>'Integral']);
        $dt_inicial    = new TDate('DataInicial'); $dt_inicial->setMask('dd/mm/yyyy');
        $dt_final      = new TDate('DataFinal'); $dt_final->setMask('dd/mm/yyyy');
        $grade         = new TDBCombo('CodGradeEtapa', 'dados_fei', 'FiGradeEtapa', 'CodGradeEtapa', 'Descricao');
        $professor     = new TDBCombo('CodProfessor', 'dados_fei', 'FiProfessor', 'Codprofessor', 'Nome');
        $sistema_av    = new TDBCombo('CodSistemaAvaliacao', 'dados_fei', 'FiSistemaAvaliacao', 'CodSistemaAvaliacao', 'Descricao');
        
        // Aba 2: Parâmetros Acadêmicos
        $opcao_sn = ['S' => 'Sim', 'N' => 'Não'];
        $prova_integrada = new TRadioGroup('ProvaIntegrada'); $prova_integrada->addItems($opcao_sn); $prova_integrada->setLayout('horizontal');
        $apont_faltas    = new TRadioGroup('ApontamentoFaltas'); $apont_faltas->addItems($opcao_sn); $apont_faltas->setLayout('horizontal');
        $marca           = new TRadioGroup('Marca'); $marca->addItems($opcao_sn); $marca->setLayout('horizontal');
        $tcc             = new TRadioGroup('TCC'); $tcc->addItems($opcao_sn); $tcc->setLayout('horizontal');
        $ativ_comp       = new TRadioGroup('AtivComp'); $ativ_comp->addItems($opcao_sn); $ativ_comp->setLayout('horizontal');
        $estagio         = new TRadioGroup('Estagio'); $estagio->addItems($opcao_sn); $estagio->setLayout('horizontal');
        $rec_bim_ant     = new TRadioGroup('Recupera_Bim_Anterior'); $rec_bim_ant->addItems($opcao_sn); $rec_bim_ant->setLayout('horizontal');
        $acesso_moodle   = new TCombo('AcessoMoodle'); $acesso_moodle->addItems([1 => 'Liberado', 0 => 'Bloqueado']);
        $validade_cartao = new TEntry('Validade_Cartao');

        // ---- DISTRIBUIÇÃO DOS CAMPOS NAS ABAS SEQUENCIAIS ----
        
        // 1. Aba: Estrutura e Localização
        $this->form->appendPage('Estrutura e Localização');
        $this->form->addFields([new TLabel('Código Interno:')], [$codturmaetapa], [new TLabel('Turma (Identificação): *')], [$identificacao]);
        $this->form->addFields([new TLabel('Ano Corrente:')], [$ano], [new TLabel('Semestre / Ciclo:')], [$semestre]);
        $this->form->addFields([new TLabel('Grade Curricular / Etapa:')], [$grade]);
        $this->form->addFields([new TLabel('Sistema de Avaliação Adotado:')], [$sistema_av]);
        $this->form->addFields([new TLabel('Campus de Ensino:')], [$campus], [new TLabel('Bloco:')], [$bloco], [new TLabel('Sala:')], [$sala]);
        $this->form->addFields([new TLabel('Período/Turno:')], [$periodo]);
        $this->form->addFields([new TLabel('Vigência Inicial:')], [$dt_inicial], [new TLabel('Vigência Final:')], [$dt_final]);

        // 2. Aba: Parâmetros Acadêmicos
        $this->form->appendPage('Parâmetros Acadêmicos');
        $this->form->addFields([new TLabel('Exige Prova Integrada?')], [$prova_integrada]);
        $this->form->addFields([new TLabel('Permite Apontamento de Faltas?')], [$apont_faltas]);
        $this->form->addFields([new TLabel('Turma possui Marcação Especial?')], [$marca]);
        $this->form->addFields([new TLabel('Turma de Conclusão (TCC)?')], [$tcc]);
        $this->form->addFields([new TLabel('Gera Atividades Complementares?')], [$ativ_comp]);
        $this->form->addFields([new TLabel('Possui Estágio Obrigatório?')], [$estagio]);
        $this->form->addFields([new TLabel('Permite Recuperação de Bimestre Anterior?')], [$rec_bim_ant]);
        $this->form->addFields([new TLabel('Integração com Ambiente Moodle:')], [$acesso_moodle]);
        $this->form->addFields([new TLabel('Validade das Carteirinhas da Turma:')], [$validade_cartao]);

        // Mapeamento global de campos para capturar requisições do Form
        $this->form->setFields([
            $codturmaetapa, $identificacao, $ano, $semestre, $grade, $professor, $sistema_av, 
            $campus, $bloco, $sala, $periodo, $dt_inicial, $dt_final,
            $prova_integrada, $apont_faltas, $marca, $tcc, $ativ_comp, $estagio, $rec_bim_ant, $acesso_moodle, $validade_cartao
        ]);

        // Ações de Rodapé
        $this->form->addAction('Voltar', new TAction(['TurmaList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Turma', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $turma = new FiTurmaEtapa;
            $turma->fromArray((array) $data);
            
            $turma->CodOperador = TSession::getValue('userid');
            
            if (!empty($turma->DataInicial)) {
                $turma->DataInicial = TDate::convertToMask($turma->DataInicial, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            if (!empty($turma->DataFinal)) {
                $turma->DataFinal = TDate::convertToMask($turma->DataFinal, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
            $turma->store();
            
            $data->CodTurmaetapa = $turma->CodTurmaetapa;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Turma salva com sucesso!');
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
                $object = new FiTurmaEtapa($param['key']);
                
                if (!empty($object->DataInicial)) {
                    $object->DataInicial = TDate::convertToMask($object->DataInicial, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                if (!empty($object->DataFinal)) {
                    $object->DataFinal = TDate::convertToMask($object->DataFinal, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}