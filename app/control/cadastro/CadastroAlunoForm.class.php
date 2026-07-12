<?php

class CadastroAlunoForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_CadastroAluno');
        $this->form->setFormTitle('Cadastro de Aluno');
        
        // ---- INSTANCIAÇÃO DOS CAMPOS (Precisam ser criados antes) ----
        $codaluno   = new TEntry('Codaluno'); $codaluno->setEditable(FALSE);
        $ra         = new TEntry('Ra');
        $nome       = new TEntry('Nome');
        $cpf        = new TEntry('CPF'); $cpf->setMask('999.999.999-99');
        $rg         = new TEntry('Rg');
        $nascimento = new TDate('Datanascimento'); $nascimento->setMask('dd/mm/yyyy');
        $sexo       = new TRadioGroup('Sexo'); $sexo->addItems(['M' => 'Masculino', 'F' => 'Feminino']); $sexo->setLayout('horizontal');
        $est_civil  = new TCombo('EstadoCivil'); $est_civil->addItems(['Solteiro(a)'=>'Solteiro(a)', 'Casado(a)'=>'Casado(a)']);
        $pai        = new TEntry('NomePai');
        $mae        = new TEntry('NomeMae');
        
        $cep        = new TEntry('Cep'); $cep->setMask('99999-999');
        $endereco   = new TEntry('Endereco');
        $numero     = new TEntry('EnderecoNumero');
        $bairro     = new TEntry('Bairro');
        $cidade     = new TDBCombo('CodCidade', 'dados_fei', 'FiCidade', 'CodCidade', 'Nome');
        $telefone   = new TEntry('Telefone'); $telefone->setMask('(99)99999-9999');
        $email      = new TEntry('Email');
        
        $p_especial  = new TCombo('NecessidadesEspecias'); $p_especial->addItems(['S'=>'Sim', 'N'=>'Não']);
        $cigueira    = new TCombo('Cegueira'); $cigueira->addItems(['S'=>'Sim', 'N'=>'Não']);
        $surdez      = new TCombo('Surdez'); $surdez->addItems(['S'=>'Sim', 'N'=>'Não']);
        $fisi        = new TCombo('Fisica'); $fisi->addItems(['S'=>'Sim', 'N'=>'Não']);
        $intel       = new TCombo('Mental'); $intel->addItems(['S'=>'Sim', 'N'=>'Não']);
        $obs_saude   = new TText('ObsEducespec1');

        // ---- DISTRIBUIÇÃO DOS CAMPOS NAS ABAS EM ORDEM SEQUENCIAL ----
        
        // 1. Cria a primeira aba e já joga os campos nela
        $this->form->appendPage('Dados Pessoais');
        $this->form->addFields([new TLabel('Código:')], [$codaluno], [new TLabel('RA:')], [$ra]);
        $this->form->addFields([new TLabel('Nome Completo:')], [$nome]);
        $this->form->addFields([new TLabel('CPF:')], [$cpf], [new TLabel('RG:')], [$rg]);
        $this->form->addFields([new TLabel('Nascimento:')], [$nascimento], [new TLabel('Sexo:')], [$sexo]);
        $this->form->addFields([new TLabel('Estado Civil:')], [$est_civil]);
        $this->form->addFields([new TLabel('Nome do Pai:')], [$pai]);
        $this->form->addFields([new TLabel('Nome da Mãe:')], [$mae]);

        // 2. Cria a segunda aba e joga os campos
        $this->form->appendPage('Endereço e Contato');
        $this->form->addFields([new TLabel('CEP:')], [$cep], [new TLabel('Cidade:')], [$cidade]);
        $this->form->addFields([new TLabel('Logradouro:')], [$endereco], [new TLabel('Número:')], [$numero]);
        $this->form->addFields([new TLabel('Bairro:')], [$bairro]);
        $this->form->addFields([new TLabel('Telefone Principal:')], [$telefone], [new TLabel('E-mail:')], [$email]);

        // 3. Cria a terceira aba e joga os campos
        $this->form->appendPage('Necessidades Especiais / Saúde');
        $this->form->addFields([new TLabel('Possui Deficiência/PNE?:')], [$p_especial]);
        $this->form->addFields([new TLabel('Visual (Cegueira):')], [$cigueira], [new TLabel('Auditiva (Surdez):')], [$surdez]);
        $this->form->addFields([new TLabel('Física:')], [$fisi], [new TLabel('Mental/Intelectual:')], [$intel]);
        $this->form->addFields([new TLabel('Observações Médicas:')], [$obs_saude]);

        // Mapeamento global dos campos para as requisições do Adianti
        $this->form->setFields([
            $codaluno, $ra, $nome, $cpf, $rg, $nascimento, $sexo, $est_civil, $pai, $mae,
            $cep, $endereco, $numero, $bairro, $cidade, $telefone, $email,
            $p_especial, $cigueira, $surdez, $fisi, $intel, $obs_saude
        ]);

        // Ações globais do formulário
        $this->form->addAction('Voltar', new TAction(['CadastroAlunoList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $aluno = new FiAluno;
            $aluno->fromArray((array) $data);
            
            if (empty($aluno->Codaluno)) {
                $aluno->DataCadastro = date('Y-m-d H:i:s');
            }
            $aluno->DataAtualizacao = date('Y-m-d H:i:s');
            $aluno->CodOperador = TSession::getValue('userid');
            
            $aluno->store();
            
            $data->Codaluno = $aluno->Codaluno;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Cadastro salvo com sucesso!');
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
                $object = new FiAluno($param['key']);
                
                if(!empty($object->Datanascimento)) {
                    $object->Datanascimento = TDate::convertToMask($object->Datanascimento, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}