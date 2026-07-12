<?php

class ParamprovaintegradaForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        // Instanciação correta do formulário utilizando o Builder do Bootstrap
        $this->form = new BootstrapFormBuilder('form_FiParamprovaintegrada');
        $this->form->setFormTitle('Configuração de Prova Integrada');
        
        // ---- INSTANCIAÇÃO DOS CAMPOS ----
        
        // Aba 1: Dados Gerais
        $cod_param = new TEntry('CodParamProvaIntegrada'); $cod_param->setEditable(FALSE);
        $turma     = new TDBCombo('CodTurmaetapa', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $total_q   = new TEntry('TotalQuestoes'); $total_q->setNumericMask(0, '', '');
        $modelo    = new TEntry('Modelo'); $modelo->setNumericMask(0, '', '');
        $dt_prova  = new TDate('DataProva'); $dt_prova->setMask('dd/mm/yyyy');
        
        // Aba 2: Índices e Notas (Arrays dinâmicos para coleta posterior)
        $campos_indices = [];
        for ($i = 1; $i <= 5; $i++) {
            $indice = new TEntry("Indice{$i}"); $indice->setNumericMask(0, '', '');
            $nota   = new TEntry("Nota{$i}");   $nota->setNumericMask(2, ',', '.', true);
            
            $campos_indices["Indice{$i}"] = $indice;
            $campos_indices["Nota{$i}"]   = $nota;
        }

        // Aba 3: Faixas e Pesos
        $campos_faixas = [];
        for ($j = 1; $j <= 5; $j++) {
            $peso = new TEntry("P{$j}"); $peso->setNumericMask(2, ',', '.', true);
            $campos_faixas["P{$j}"] = $peso;

            if ($j < 5) {
                $faixa = new TEntry("Fx{$j}"); $faixa->setNumericMask(2, ',', '.', true);
                $campos_faixas["Fx{$j}"] = $faixa;
            }
        }

        // ---- DISTRIBUIÇÃO DOS CAMPOS NAS ABAS NATIVAS DO BOOTSTRAP ----
        
        // 1. Aba: Dados Gerais
        $this->form->appendPage('Dados Gerais');
        $this->form->addFields([new TLabel('Código Registro:')], [$cod_param], [new TLabel('Turma / Etapa Vinculada:')], [$turma]);
        $this->form->addFields([new TLabel('Total de Questões:')], [$total_q], [new TLabel('Modelo / Caderno:')], [$modelo]);
        $this->form->addFields([new TLabel('Data de Aplicação:')], [$dt_prova]);

        // 2. Aba: Índices e Notas
        $this->form->appendPage('Índices e Notas (Tabela de Conversão)');
        for ($i = 1; $i <= 5; $i++) {
            $this->form->addFields(
                [new TLabel("<b>Nível {$i}</b> - Mínimo Acertos:")], [$campos_indices["Indice{$i}"]], 
                [new TLabel("Nota Atribuída {$i}:")], [$campos_indices["Nota{$i}"]]
            );
        }

        // 3. Aba: Faixas e Pesos Complementares
        $this->form->appendPage('Faixas e Pesos Complementares');
        for ($j = 1; $j <= 5; $j++) {
            if ($j < 5) {
                $this->form->addFields(
                    [new TLabel("Faixa Limite {$j} (Fx):")], [$campos_faixas["Fx{$j}"]], 
                    [new TLabel("Peso / Pontuação {$j} (P):")], [$campos_faixas["P{$j}"]]
                );
            } else {
                // Mantém o layout alinhado na última linha sem o campo Fx5 que não existe no banco
                $this->form->addFields(
                    [], [], 
                    [new TLabel("Peso / Pontuação Final 5 (P):")], [$campos_faixas["P{$j}"]]
                );
            }
        }

        // Mapeamento e Registro Global de Campos
        $campos_base = [$cod_param, $turma, $total_q, $modelo, $dt_prova];
        $this->form->setFields(array_merge($campos_base, array_values($campos_indices), array_values($campos_faixas)));

        // Ações operacionais do rodapé
        $this->form->addAction('Voltar', new TAction(['ParamprovaintegradaList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Parâmetros', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $parametro = new FiParamprovaintegrada;
            $parametro->fromArray((array) $data);
            
            // Tratamento da data para persistência no banco
            if (!empty($parametro->DataProva)) {
                $parametro->DataProva = TDate::convertToMask($parametro->DataProva, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
            $parametro->store();
            
            $data->CodParamProvaIntegrada = $parametro->CodParamProvaIntegrada;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Parâmetros salvos com sucesso!');
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
                $object = new FiParamprovaintegrada($param['key']);
                
                if (!empty($object->DataProva)) {
                    $object->DataProva = TDate::convertToMask($object->DataProva, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}