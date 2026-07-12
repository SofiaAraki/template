<?php

class DataApontamentoBimestralForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FiDataApontamentoBimestral');
        $this->form->setFormTitle('Definição de Período para Apontamento');
        
        $cod_prazo = new TEntry('Cod_DataApontamentoBimestral'); $cod_prazo->setEditable(FALSE);
        $entidade  = new TDBCombo('CodEntidade', 'dados_fei', 'FiEntidade', 'CodEntidade', 'NomeFantasia');
        
        $ano       = new TEntry('Ano'); $ano->setMaxLength(4);
        $semestre  = new TCombo('Semestre'); $semestre->addItems(['1'=>'1º Semestre', '2'=>'2º Semestre']);
        $bimestre  = new TCombo('Bimestre'); $bimestre->addItems(['1'=>'1º Bimestre', '2'=>'2º Bimestre', '3'=>'3º Bimestre', '4'=>'4º Bimestre']);
        
        // Configuração de inputs com máscara de hora (Data e Hora)
        $dt_inicio = new TDateTime('DataInicio'); $dt_inicio->setMask('dd/mm/yyyy hh:ii');
        $dt_fim    = new TDateTime('DataFim'); $dt_fim->setMask('dd/mm/yyyy hh:ii');
        
        // Definição de Obrigatoriedades
        $entidade->addValidation('Entidade / Escola', new TRequiredValidator);
        $ano->addValidation('Ano', new TRequiredValidator);
        $semestre->addValidation('Semestre', new TRequiredValidator);
        $bimestre->addValidation('Bimestre', new TRequiredValidator);
        $dt_inicio->addValidation('Data de Abertura', new TRequiredValidator);
        $dt_fim->addValidation('Data de Encerramento', new TRequiredValidator);
        
        // Construção do Grid do Formulário
        $this->form->addFields([new TLabel('Código Registro:')], [$cod_prazo]);
        $this->form->addFields([new TLabel('Instituição / Entidade: ')], [$entidade]);
        
        $this->form->addFields(
            [new TLabel('Ano de Ref.: ')], [$ano], 
            [new TLabel('Semestre: ')], [$semestre], 
            [new TLabel('Bimestre: ')], [$bimestre]
        );
        
        $this->form->addFields(
            [new TLabel('Data/Hora Abertura: ')], [$dt_inicio], 
            [new TLabel('Data/Hora Fechamento: ')], [$dt_fim]
        );

        $this->form->setFields([$cod_prazo, $entidade, $ano, $semestre, $bimestre, $dt_inicio, $dt_fim]);

        $this->form->addAction('Voltar', new TAction(['DataApontamentoBimestralList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Cronograma', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $cronograma = new FiDataapontamentobimestral;
            $cronograma->fromArray((array) $data);
            
            // Injeção de auditoria operacional obrigatória da DDL
            $cronograma->CodOperador = TSession::getValue('userid');
            
            // Sanitização e formatação de datas combinadas para persistência no banco (Y-m-d H:i:s)
            if (!empty($cronograma->DataInicio)) {
                $cronograma->DataInicio = TDateTime::convertToMask($cronograma->DataInicio, 'dd/mm/yyyy hh:ii', 'yyyy-mm-dd hh:ii:ss');
            }
            if (!empty($cronograma->DataFim)) {
                $cronograma->DataFim = TDateTime::convertToMask($cronograma->DataFim, 'dd/mm/yyyy hh:ii', 'yyyy-mm-dd hh:ii:ss');
            }
            
            $cronograma->store();
            
            $data->Cod_DataApontamentoBimestral = $cronograma->Cod_DataApontamentoBimestral;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Configuração de prazos armazenada com sucesso!');
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
                $object = new FiDataapontamentobimestral($param['key']);
                
                // Conversão do padrão ISO para o formato de exibição local das views
                if (!empty($object->DataInicio)) {
                    $object->DataInicio = TDateTime::convertToMask($object->DataInicio, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii');
                }
                if (!empty($object->DataFim)) {
                    $object->DataFim = TDateTime::convertToMask($object->DataFim, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}