<?php

class CadastroProfessorForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FIProfessor');
        $this->form->setFormTitle('Cadastro de Professor');
        
        // ---- 1. INSTANCIAÇÃO DOS CAMPOS ----
        $codprofessor = new TEntry('Codprofessor'); $codprofessor->setEditable(FALSE);
        $nome         = new TEntry('Nome');
        $admissao     = new TDate('DataAdmissao'); $admissao->setMask('dd/mm/yyyy');
        $sexo         = new TRadioGroup('Sexo'); $sexo->addItems(['M' => 'Masculino', 'F' => 'Feminino']); $sexo->setLayout('horizontal');
        
        $endereco     = new TEntry('Endereco');
        $bairro       = new TEntry('Bairro');
        $cep          = new TEntry('CEP'); $cep->setMask('99999-999');
        $cidade       = new TDBCombo('CodCidade', 'dados_fei', 'FiCidade', 'CodCidade', 'Nome');
        
        $cpf          = new TEntry('CPF'); $cpf->setMask('999.999.999-99');
        $rg           = new TEntry('Rg');
        $rd           = new TEntry('RD');
        $nascimento   = new TDate('DataNascimento'); $nascimento->setMask('dd/mm/yyyy');
        $nacionalidade= new TEntry('Nacionalidade');
        
        $naturalidade = new TEntry('Naturalidade');
        $naturalidade_uf = new TEntry('NaturalidadeUf');
        $telefone1    = new TEntry('Telefone1'); $telefone1->setMask('(99)9999-9999');
        $telefone2    = new TEntry('Telefone2'); $telefone2->setMask('(99)9999-9999');
        $telefone3    = new TEntry('Telefone3'); // Adicionado conforme a model e imagem
        
        $hab1         = new TEntry('HabilitacaoProf1');
        $hab2         = new TEntry('HabilitacaoProf2');
        $hab3         = new TEntry('HabilitacaoProf3');
        $email        = new TEntry('Email');
        $senha        = new TPassword('Senha'); $senha->setMaxLength(4);

        // ---- 2. DISTRIBUIÇÃO DOS CAMPOS NA TELA (Sem Abas - Conforme a imagem) ----
        
        $this->form->addFields([new TLabel('Código:')], [$codprofessor], [new TLabel('Nome Completo:')], [$nome], [new TLabel('Data Admissão:')], [$admissao]);
        $this->form->addFields([new TLabel('Endereço:')], [$endereco], [new TLabel('Sexo:')], [$sexo]);
        $this->form->addFields([new TLabel('Bairro:')], [$bairro], [new TLabel('CEP:')], [$cep]);
        $this->form->addFields([new TLabel('Cidade:')], [$cidade]);
        
        // Linha de Documentos fracionada para evitar erro de índice no BootstrapFormBuilder
        $this->form->addFields([new TLabel('CPF:')], [$cpf], [new TLabel('RG:')], [$rg], [new TLabel('RD (Reg. Docente):')], [$rd]);
        $this->form->addFields([new TLabel('Data Nascimento:')], [$nascimento], [new TLabel('Nacionalidade:')], [$nacionalidade]);
        
        // Linha de Naturalidade e Telefones
        $this->form->addFields([new TLabel('Naturalidade:')], [$naturalidade], [new TLabel('UF:')], [$naturalidade_uf]);
        $this->form->addFields([new TLabel('Telefone 1:')], [$telefone1], [new TLabel('Telefone 2:')], [$telefone2], [new TLabel('Telefone 3:')], [$telefone3]);
        
        // Habilitações, E-mail e Senha
        $this->form->addFields([new TLabel('1ª Habilitação:')], [$hab1], [new TLabel('E-mail:')], [$email]);
        $this->form->addFields([new TLabel('2ª Habilitação:')], [$hab2], [new TLabel('Senha do Portal:')], [$senha]);
        $this->form->addFields([new TLabel('3ª Habilitação:')], [$hab3]);

        // ---- 3. MAPEAMENTO GLOBAL DOS CAMPOS ----
        $this->form->setFields([
            $codprofessor, $nome, $admissao, $sexo, $endereco, $bairro, $cep, $cidade,
            $cpf, $rg, $rd, $nascimento, $nacionalidade, $naturalidade, $naturalidade_uf,
            $telefone1, $telefone2, $telefone3, $hab1, $hab2, $hab3, $email, $senha
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
            
            // Tratamento das datas para o Banco de Dados
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
                
                // Conversão das datas vinda do Banco de Dados para a Interface
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