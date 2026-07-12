<?php

class CadastroProfessorForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FIProfessor');
        $this->form->setFormTitle('Cadastro de Professor');
        
        // ---- INSTANCIAÇÃO DOS CAMPOS ----
        $codprofessor = new TEntry('Codprofessor'); $codprofessor->setEditable(FALSE);
        $nome         = new TEntry('Nome');
        $cpf          = new TEntry('CPF'); $cpf->setMask('999.999.999-99');
        $rg           = new TEntry('Rg');
        $nascimento   = new TDate('DataNascimento'); $nascimento->setMask('dd/mm/yyyy');
        $sexo         = new TRadioGroup('Sexo'); $sexo->addItems(['M' => 'Masculino', 'F' => 'Feminino']); $sexo->setLayout('horizontal');
        $nacionalidade= new TEntry('Nacionalidade');
        
        $cep        = new TEntry('CEP'); $cep->setMask('99999-999');
        $endereco   = new TEntry('Endereco');
        $bairro     = new TEntry('Bairro');
        $cidade     = new TDBCombo('CodCidade', 'dados_fei', 'FiCidade', 'CodCidade', 'Nome');
        $telefone1  = new TEntry('Telefone1'); $telefone1->setMask('(99)99999-9999');
        $telefone2  = new TEntry('Telefone2'); $telefone2->setMask('(99)99999-9999');
        $email      = new TEntry('Email');
        
        $rd         = new TEntry('RD');
        $admissao   = new TDate('DataAdmissao'); $admissao->setMask('dd/mm/yyyy');
        $senha      = new TPassword('Senha'); $senha->setMaxLength(4);
        $hab1       = new TEntry('HabilitacaoProf1');
        $hab2       = new TEntry('HabilitacaoProf2');
        $hab3       = new TEntry('HabilitacaoProf3');

        // ---- DISTRIBUIÇÃO DOS CAMPOS NAS ABAS SEQUENCIAIS ----
        
        // 1. Aba: Dados Pessoais
        $this->form->appendPage('Dados Pessoais');
        $this->form->addFields([new TLabel('Código:')], [$codprofessor]);
        $this->form->addFields([new TLabel('Nome Completo:')], [$nome]);
        $this->form->addFields([new TLabel('CPF:')], [$cpf], [new TLabel('RG:')], [$rg]);
        $this->form->addFields([new TLabel('Nascimento:')], [$nascimento], [new TLabel('Sexo:')], [$sexo]);
        $this->form->addFields([new TLabel('Nacionalidade:')], [$nacionalidade]);

        // 2. Aba: Endereço e Contato
        $this->form->appendPage('Endereço e Contato');
        $this->form->addFields([new TLabel('CEP:')], [$cep], [new TLabel('Cidade:')], [$cidade]);
        $this->form->addFields([new TLabel('Logradouro:')], [$endereco], [new TLabel('Bairro:')], [$bairro]);
        $this->form->addFields([new TLabel('Telefone 1:')], [$telefone1], [new TLabel('Telefone 2:')], [$telefone2]);
        $this->form->addFields([new TLabel('E-mail:')], [$email]);

        // 3. Aba: Profissional e Habilitações
        $this->form->appendPage('Profissional e Habilitações');
        $this->form->addFields([new TLabel('Registro Docente (RD):')], [$rd], [new TLabel('Data de Admissão:')], [$admissao]);
        $this->form->addFields([new TLabel('Senha do Portal (Máx 4 dig.):')], [$senha]);
        $this->form->addFields([new TLabel('Habilitação Principal:')], [$hab1]);
        $this->form->addFields([new TLabel('Habilitação Secundária:')], [$hab2]);
        $this->form->addFields([new TLabel('Outras Informações/Habilitação:')], [$hab3]);

        // Mapeamento de campos para processamento interno
        $this->form->setFields([
            $codprofessor, $nome, $cpf, $rg, $nascimento, $sexo, $nacionalidade,
            $cep, $endereco, $bairro, $cidade, $telefone1, $telefone2, $email,
            $rd, $admissao, $senha, $hab1, $hab2, $hab3
        ]);

        // Botões de Ação
        $this->form->addAction('Voltar', new TAction(['CadastroProfessorList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $professor = new FiProfessor;
            $professor->fromArray((array) $data);
            
            if (empty($professor->Codprofessor)) {
                $professor->DataCadastro = date('Y-m-d H:i:s');
            }
            $professor->CodOperador = TSession::getValue('userid');
            
            if (!empty($professor->DataNascimento)) {
                $professor->DataNascimento = TDate::convertToMask($professor->DataNascimento, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            if (!empty($professor->DataAdmissao)) {
                $professor->DataAdmissao = TDate::convertToMask($professor->DataAdmissao, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
            $professor->store();
            
            $data->Codprofessor = $professor->Codprofessor;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Cadastro de professor salvo com sucesso!');
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
                $object = new FiProfessor($param['key']);
                
                if (!empty($object->DataNascimento)) {
                    $object->DataNascimento = TDate::convertToMask($object->DataNascimento, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                if (!empty($object->DataAdmissao)) {
                    $object->DataAdmissao = TDate::convertToMask($object->DataAdmissao, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}