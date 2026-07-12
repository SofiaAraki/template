<?php

class CadastroResponsavelForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_FIResponsavel');
        $this->form->setFormTitle('Cadastro de Responsável Legal');
        
        // ---- INSTANCIAÇÃO DOS CAMPOS ----
        $codresponsavel = new TEntry('codresponsavel'); $codresponsavel->setEditable(FALSE);
        $nome           = new TEntry('Nome');
        $cpf            = new TEntry('CPF'); $cpf->setMask('999.999.999-99');
        $rg             = new TEntry('Rg');
        $nascimento     = new TDate('DataNascimento'); $nascimento->setMask('dd/mm/yyyy');
        $sexo           = new TRadioGroup('Sexo'); $sexo->addItems(['M' => 'Masculino', 'F' => 'Feminino']); $sexo->setLayout('horizontal');
        $estado_civil   = new TCombo('EstadoCivil'); $estado_civil->addItems(['Solteiro(a)'=>'Solteiro(a)', 'Casado(a)'=>'Casado(a)', 'Divorciado(a)'=>'Divorciado(a)', 'Viúvo(a)'=>'Viúvo(a)']);
        
        $cep            = new TEntry('Cep'); $cep->setMask('99999-999');
        $endereco       = new TEntry('Endereco');
        $numero         = new TEntry('EnderecoNumero');
        $bairro         = new TEntry('Bairro');
        $cidade         = new TDBCombo('CodCidade', 'dados_fei', 'FICidade', 'CodCidade', 'Nome');
        $telefone1      = new TEntry('Telefone1'); $telefone1->setMask('(99)99999-9999');
        $telefone2      = new TEntry('Telefone2'); $telefone2->setMask('(99)99999-9999');
        $email          = new TEntry('email');
        $profissao      = new TEntry('Profissao');
        $local_trabalho = new TEntry('LocalTrabalho');

        // ---- DISTRIBUIÇÃO DOS CAMPOS NAS ABAS SEQUENCIAIS ----
        
        // 1. Aba: Dados Pessoais
        $this->form->appendPage('Dados Pessoais');
        $this->form->addFields([new TLabel('Código:')], [$codresponsavel]);
        $this->form->addFields([new TLabel('Nome do Responsável:')], [$nome]);
        $this->form->addFields([new TLabel('CPF:')], [$cpf], [new TLabel('RG:')], [$rg]);
        $this->form->addFields([new TLabel('Nascimento:')], [$nascimento], [new TLabel('Sexo:')], [$sexo]);
        $this->form->addFields([new TLabel('Estado Civil:')], [$estado_civil]);

        // 2. Aba: Contato, Endereço e Trabalho
        $this->form->appendPage('Contato, Endereço e Trabalho');
        $this->form->addFields([new TLabel('CEP:')], [$cep], [new TLabel('Cidade:')], [$cidade]);
        $this->form->addFields([new TLabel('Logradouro:')], [$endereco], [new TLabel('Número:')], [$numero]);
        $this->form->addFields([new TLabel('Bairro:')], [$bairro]);
        $this->form->addFields([new TLabel('Telefone Principal:')], [$telefone1], [new TLabel('Telefone Recado:')], [$telefone2]);
        $this->form->addFields([new TLabel('E-mail:')], [$email]);
        $this->form->addFields([new TLabel('Profissão:')], [$profissao], [new TLabel('Local de Trabalho:')], [$local_trabalho]);

        // Mapeamento global de campos
        $this->form->setFields([
            $codresponsavel, $nome, $cpf, $rg, $nascimento, $sexo, $estado_civil,
            $cep, $endereco, $numero, $bairro, $cidade, $telefone1, $telefone2, $email, $profissao, $local_trabalho
        ]);

        // Botões de Ação
        $this->form->addAction('Voltar', new TAction(['CadastroResponsavelList', 'onSearch']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        parent::add($this->form);
    }
    
    public function onSave($param)
    {
        try {
            TTransaction::open('dados_fei');
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $responsavel = new FiResponsavel;
            $responsavel->fromArray((array) $data);
            
            $responsavel->CodOperador = TSession::getValue('userid');
            
            if (!empty($responsavel->DataNascimento)) {
                $responsavel->DataNascimento = TDate::convertToMask($responsavel->DataNascimento, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
            $responsavel->store();
            
            $data->codresponsavel = $responsavel->codresponsavel;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', 'Dados salvos com sucesso!');
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
                $object = new FiResponsavel($param['key']);
                
                if (!empty($object->DataNascimento)) {
                    $object->DataNascimento = TDate::convertToMask($object->DataNascimento, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}