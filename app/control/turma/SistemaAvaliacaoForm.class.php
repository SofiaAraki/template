<?php

class SistemaAvaliacaoForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FiSistemaAvaliacao');
        $this->form->setFormTitle('Configuração do Sistema de Avaliação');
        
        $opcao_sn = ['S' => 'Sim', 'N' => 'Não'];
        
        // ---- INSTANCIAÇÃO DOS CAMPOS ----
        
        // Aba 1: Regras de Promoção e Notas
        $cod_sistema    = new TEntry('CodSistemaAvaliacao'); $cod_sistema->setEditable(FALSE);
        $descricao      = new TEntry('Descricao'); $descricao->setMaxLength(20);
        $tipo_nota      = new TCombo('TipoNota'); $tipo_nota->addItems(['N'=>'Numérica', 'C'=>'Conceitual']);
        $prova_int      = new TRadioGroup('ProvaIntegrada'); $prova_int->addItems($opcao_sn); $prova_int->setLayout('horizontal');
        
        $nota_maxima    = new TEntry('NotaMaxima'); $nota_maxima->setNumericMask(2, ',', '.', true);
        $nota_minima    = new TEntry('NotaMinima'); $nota_minima->setNumericMask(2, ',', '.', true);
        $promocao_media = new TEntry('PromocaoMedia'); $promocao_media->setNumericMask(2, ',', '.', true);
        $promocao_freq  = new TEntry('PromocaoFreqMinima'); $promocao_freq->setNumericMask(2, ',', '.', true);
        
        $variacao       = new TEntry('Variacao'); $variacao->setNumericMask(2, ',', '.', true);
        $arred_casas    = new TEntry('ArredCasasDecimais'); $arred_casas->setNumericMask(0, '', '');
        $arred_indice   = new TEntry('ArredIndice'); $arred_indice->setNumericMask(2, ',', '.', true);
        $variacao_arr   = new TEntry('VariacaoArrend'); $variacao_arr->setNumericMask(2, ',', '.', true);
        
        $tipo_calc_freq = new TCombo('TipoCalcFreq'); $tipo_calc_freq->addItems(['H'=>'Por Horas', 'D'=>'Por Dias', 'P'=>'Percentual']);
        $media_ger_freq = new TRadioGroup('MediaGeralFreq'); $media_ger_freq->addItems($opcao_sn); $media_ger_freq->setLayout('horizontal');
        
        $formula_etapa  = new TText('FormulaMediaEtapa'); $formula_etapa->setSize('100%', 60);
        
        // Aba 2: Parâmetros da Recuperação (Gerais)
        $tem_rec        = new TRadioGroup('Recuperacao'); $tem_rec->addItems($opcao_sn); $tem_rec->setLayout('horizontal');
        $qtd_rec        = new TEntry('QuantidadeRecuperacao'); $qtd_rec->setNumericMask(0, '', '');
        $rec_media_alvo = new TEntry('RecuperacaoMedia'); $rec_media_alvo->setNumericMask(2, ',', '.', true);
        $rec_freq_min   = new TEntry('RecuperacaoFreqMinima'); $rec_freq_min->setNumericMask(2, ',', '.', true);
        $limite_disc    = new TEntry('LimiteDisciplinaRecuperacao'); $limite_disc->setNumericMask(0, '', '');

        // Aba 3: Fórmulas das Recuperações (Mapeamento em Array)
        $fields_rec = [];
        for ($i = 1; $i <= 5; $i++) {
            $descr_rec   = new TEntry("DescrRecuperacao{$i}");
            $formula_rec = new TEntry("FormulaRecuperacao{$i}");
            $media_rec   = new TEntry("MediaRecuperacao{$i}"); $media_rec->setNumericMask(2, ',', '.', true);
            
            $fields_rec["DescrRecuperacao{$i}"] = $descr_rec;
            $fields_rec["FormulaRecuperacao{$i}"] = $formula_rec;
            $fields_rec["MediaRecuperacao{$i}"] = $media_rec;
        }

        // ---- DISTRIBUIÇÃO DOS CAMPOS NAS ABAS NATIVAS DO BOOTSTRAP ----
        
        // 1. Aba: Regras de Promoção e Notas
        $this->form->appendPage('Regras de Promoção e Notas');
        $this->form->addFields([new TLabel('Código:')], [$cod_sistema], [new TLabel('Descrição do Sistema: *')], [$descricao]);
        $this->form->addFields([new TLabel('Tipo de Nota:')], [$tipo_nota], [new TLabel('Exige Prova Integrada?')], [$prova_int]);
        $this->form->addFields([new TLabel('Nota Máxima:')], [$nota_maxima], [new TLabel('Nota Mínima p/ Aprovação:')], [$nota_minima]);
        $this->form->addFields([new TLabel('Média p/ Promoção:')], [$promocao_media], [new TLabel('Frequência Mínima (%):')], [$promocao_freq]);
        $this->form->addFields([new TLabel('Variação de Nota:')], [$variacao], [new TLabel('Casas Decimais Arred.:')], [$arred_casas]);
        $this->form->addFields([new TLabel('Índice Arredondamento:')], [$arred_indice], [new TLabel('Variação Arredond.:')], [$variacao_arr]);
        $this->form->addFields([new TLabel('Tipo Cálculo Frequência:')], [$tipo_calc_freq], [new TLabel('Média Geral Freq.?')], [$media_ger_freq]);
        $this->form->addFields([new TLabel('Fórmula da Média da Etapa:')], [$formula_etapa]);

        // 2. Aba: Parâmetros da Recuperação
        $this->form->appendPage('Parâmetros da Recuperação');
        $this->form->addFields([new TLabel('Possui Recuperação?')], [$tem_rec], [new TLabel('Quantidade Máx. de Recuperações:')], [$qtd_rec]);
        $this->form->addFields([new TLabel('Média Exigida na Recuperação:')], [$rec_media_alvo], [new TLabel('Frequência Mínima p/ Direito à Rec.:')], [$rec_freq_min]);
        $this->form->addFields([new TLabel('Limite de Disciplinas em Rec.:')], [$limite_disc]);

        // 3. Aba: Fórmulas das Recuperações
        $this->form->appendPage('Fórmulas das Recuperações');
        for ($i = 1; $i <= 5; $i++) {
            // Linha da descrição
            $this->form->addFields([new TLabel("<b>[Recuperação {$i}]</b> Descrição:")], [$fields_rec["DescrRecuperacao{$i}"]]);
            // Linha da Fórmula e Média de Corte alinhadas lado a lado
            $this->form->addFields(
                [new TLabel("Fórmula Rec. {$i}:")], [$fields_rec["FormulaRecuperacao{$i}"]], 
                [new TLabel("Média Corte Rec. {$i}:")], [$fields_rec["MediaRecuperacao{$i}"]]
            );
        }

        // Registro Global de Componentes no Form
        $campos_base = [
            $cod_sistema, $descricao, $tipo_nota, $prova_int, $nota_maxima, $nota_minima, $promocao_media, $promocao_freq,
            $variacao, $arred_casas, $arred_indice, $variacao_arr, $tipo_calc_freq, $media_ger_freq, $formula_etapa,
            $tem_rec, $qtd_rec, $rec_media_alvo, $rec_freq_min, $limite_disc
        ];
        
        $this->form->setFields(array_merge($campos_base, array_values($fields_rec)));

        // Botões operacionais
        $this->form->addAction('Voltar', new TAction(['SistemaAvaliacaoList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Configuração', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $sistema = new FiSistemaAvaliacao;
            $sistema->fromArray((array) $data);
            
            // Injeção de metadados obrigatórios do banco de dados
            if (empty($sistema->CodSistemaAvaliacao)) {
                $sistema->DataCadastro = date('Y-m-d H:i:s');
            }
            $sistema->CodOperador = TSession::getValue('userid');
            
            $sistema->store();
            
            $data->CodSistemaAvaliacao = $sistema->CodSistemaAvaliacao;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Parâmetros de avaliação salvos com sucesso!');
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
                $object = new FiSistemaAvaliacao($param['key']);
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}