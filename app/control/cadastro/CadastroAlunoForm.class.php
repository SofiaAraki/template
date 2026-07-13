<?php

class CadastroAlunoForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_CadastroAluno');
        $this->form->setFormTitle('Cadastro de Aluno');
        
        // ---- 1. INSTANCIAÇÃO DOS CAMPOS ----
        
        // Dados Principais / Identificadores
        $codaluno       = new TEntry('Codaluno'); $codaluno->setEditable(FALSE);
        $num_identific  = new TEntry('NumeroIdentificacao');
        $nome_social    = new TEntry('Nome'); // Campo principal associado ao Nome
        $nome_civil     = new TEntry('NomeIdentificacaoCivil');
        $sexo           = new TRadioGroup('Sexo'); $sexo->addItems(['M' => 'Masculino', 'F' => 'Feminino']); $sexo->setLayout('horizontal');
        $nascimento     = new TDate('Datanascimento'); $nascimento->setMask('dd/mm/yyyy');
        $naturalidade   = new TEntry('Naturalidade');
        $naturalidade_uf= new TEntry('NaturalidadeUF');
        $nacionalidade  = new TEntry('Nacionalidade');
        $cod_resp       = new TEntry('CodResponsavel');
        
        // Filiação
        $pai            = new TEntry('NomePai');
        $mae            = new TEntry('NomeMae');
        
        // Certidão de Nascimento
        $cert_num       = new TEntry('NumeroCertidaoNascimento');
        $cert_livro     = new TEntry('CertNascLivro');
        $cert_folha     = new TEntry('CertNascFolha');
        
        // Documentos
        $regmat         = new TEntry('regmat'); // RM (Última Matrícula)
        $ra             = new TEntry('Ra');
        $rg             = new TEntry('Rg');
        $rg_orgao       = new TEntry('RgOrgaoExpedidor');
        $cpf            = new TEntry('CPF'); $cpf->setMask('999.999.999-99');
        $titulo_eleitor = new TEntry('TituloEleitorNumero');
        $titulo_zona    = new TEntry('TituloEleitorZona');
        $titulo_secao   = new TEntry('TituloEleitorSecao');
        
        // Serviço Militar
        $serv_militar   = new TEntry('ServicoMilitar');
        $serv_orgao     = new TEntry('ServicoMilitarOrgaoExpedidor');
        $serv_data_exp  = new TDate('ServicoMilitarDataExpedicao'); $serv_data_exp->setMask('dd/mm/yyyy');
        
        // Endereço
        $endereco       = new TEntry('Endereco');
        $numero         = new TEntry('EnderecoNumero');
        $complemento    = new TEntry('EnderecoComplemeto');
        $bairro         = new TEntry('Bairro');
        $cidade         = new TDBCombo('CodCidade', 'dados_fei', 'FiCidade', 'CodCidade', 'Nome');
        $cep            = new TEntry('Cep'); $cep->setMask('99999-999');
        
        // Informações Sociais / Escolares
        $est_civil      = new TCombo('EstadoCivil'); $est_civil->addItems(['SOLTEIRO'=>'SOLTEIRO', 'CASADO'=>'CASADO', 'VIÚVO'=>'VIÚVO', 'DIVORCIADO'=>'DIVORCIADO']);
        $profissao      = new TEntry('Profissao');
        $tipo_escola_em = new TCombo('TipoEscolaEnsinoMedio'); $tipo_escola_em->addItems(['Pública'=>'Pública', 'Privada'=>'Privada']);
        $senha_moodle   = new TEntry('SenhaMoodle');
        
        // Contatos e E-mail
        $telefone1      = new TEntry('Telefone'); $telefone1->setMask('(99)99999-9999');
        $telefone2      = new TEntry('Telefone2'); $telefone2->setMask('(99)99999-9999');
        $telefone3      = new TEntry('Telefone3'); 
        $telefone_fax   = new TEntry('TelefoneFax');
        $email          = new TEntry('Email');
        
        // Observações Gerais
        $observacao1    = new TText('Observacao1');
        
        // Campos das Demais Abas (Necessidades / Cor / Raça)
        $cor_raca       = new TCombo('CorRaca'); $cor_raca->addItems(['Branca'=>'Branca', 'Preta'=>'Preta', 'Parda'=>'Parda', 'Amarela'=>'Amarela', 'Indígena'=>'Indígena']);
        $obs_cor_raca1  = new TText('ObsCorRaca1');
        
        $p_especial     = new TCombo('NecessidadesEspecias'); $p_especial->addItems(['S'=>'Sim', 'N'=>'Não']);
        $cigueira       = new TCombo('Cegueira'); $cigueira->addItems(['S'=>'Sim', 'N'=>'Não']);
        $baixa_visao    = new TCombo('Baixavisao'); $baixa_visao->addItems(['S'=>'Sim', 'N'=>'Não']);
        $surdez         = new TCombo('Surdez'); $surdez->addItems(['S'=>'Sim', 'N'=>'Não']);
        $auditivo       = new TCombo('Auditivo'); $auditivo->addItems(['S'=>'Sim', 'N'=>'Não']);
        $fisi           = new TCombo('Fisica'); $fisi->addItems(['S'=>'Sim', 'N'=>'Não']);
        $intel          = new TCombo('Mental'); $intel->addItems(['S'=>'Sim', 'N'=>'Não']);
        $multiplo       = new TCombo('Multiplo'); $multiplo->addItems(['S'=>'Sim', 'N'=>'Não']);
        $superdotado    = new TCombo('Superdotado'); $superdotado->addItems(['S'=>'Sim', 'N'=>'Não']);
        $obs_saude      = new TText('ObsEducespec1');

        // ---- 2. DISTRIBUIÇÃO DOS CAMPOS NAS ABAS EM ORDEM SEQUENCIAL ----
        
        // --- ABA 1: DADOS CADASTRAIS ---
        $this->form->appendPage('Dados cadastrais');
        
        $this->form->addFields([new TLabel('Código:')], [$codaluno], [new TLabel('Nº Identificação:')], [$num_identific]);
        $this->form->addFields([new TLabel('Nome Social:')], [$nome_social]);
        $this->form->addFields([new TLabel('Nome de Identificação Civil:')], [$nome_civil]);
        
        // Quebrando os dados de nascimento e naturalidade em duas linhas menores
        $this->form->addFields([new TLabel('Sexo:')], [$sexo], [new TLabel('Data Nascimento:')], [$nascimento]);
        $this->form->addFields([new TLabel('Naturalidade:')], [$naturalidade], [new TLabel('UF:')], [$naturalidade_uf], [new TLabel('Nacionalidade:')], [$nacionalidade]);
        $this->form->addFields([new TLabel('Cód. Responsável:')], [$cod_resp]);
        
        $this->form->addFields([new TLabel('Pai:')], [$pai]);
        $this->form->addFields([new TLabel('Mãe:')], [$mae]);
        
        $this->form->addFields([new TLabel('Nº Certidão de Nascimento:')], [$cert_num], [new TLabel('Livro:')], [$cert_livro], [new TLabel('Folha:')], [$cert_folha]);
        
        // Organizando os Documentos (RG, CPF, RA, RM) de forma fracionada para não estourar o grid
        $this->form->addFields([new TLabel('RM (Última Mat.):')], [$regmat], [new TLabel('RA:')], [$ra]);
        $this->form->addFields([new TLabel('RG:')], [$rg], [new TLabel('Órgão Exp:')], [$rg_orgao], [new TLabel('CPF:')], [$cpf]);
        $this->form->addFields([new TLabel('Título Eleitor:')], [$titulo_eleitor], [new TLabel('Zona:')], [$titulo_zona], [new TLabel('Seção:')], [$titulo_secao]);
        
        $this->form->addFields([new TLabel('Serviço Militar:')], [$serv_militar], [new TLabel('Órgão Expedidor:')], [$serv_orgao], [new TLabel('Data Expedição:')], [$serv_data_exp]);
        
        $this->form->addFields([new TLabel('Logradouro:')], [$endereco], [new TLabel('Nº:')], [$numero], [new TLabel('Complemento:')], [$complemento]);
        $this->form->addFields([new TLabel('Bairro:')], [$bairro], [new TLabel('Cidade:')], [$cidade], [new TLabel('CEP:')], [$cep]);
        
        $this->form->addFields([new TLabel('Estado Civil:')], [$est_civil], [new TLabel('Profissão:')], [$profissao]);
        $this->form->addFields([new TLabel('Tipo de Escola Ensino Médio:')], [$tipo_escola_em], [new TLabel('Senha (Moodle):')], [$senha_moodle]);
        
        // Organizando os telefones
        $this->form->addFields([new TLabel('Telefone - 1:')], [$telefone1], [new TLabel('Telefone - 2:')], [$telefone2]);
        $this->form->addFields([new TLabel('Telefone - 3:')], [$telefone3], [new TLabel('Telefone - Fax:')], [$telefone_fax], [new TLabel('E-mail:')], [$email]);
        
        $this->form->addFields([new TLabel('Observações:')], [$observacao1]);

        // --- ABA 2: DECLARAÇÃO SOBRE COR / RAÇA ---
        $this->form->appendPage('Declaração sobre Cor/Raça');
        $this->form->addFields([new TLabel('Cor/Raça:')], [$cor_raca]);
        $this->form->addFields([new TLabel('Observações Cor/Raça:')], [$obs_cor_raca1]);

        // --- ABA 3: NECESSIDADES EDUCACIONAIS ESPECIAIS ---
        $this->form->appendPage('Necessidades Educacionais Especiais');
        $this->form->addFields([new TLabel('Possui Necessidades Especiais?:')], [$p_especial]);
        $this->form->addFields(
            [new TLabel('Cegueira:')], [$cigueira], 
            [new TLabel('Baixa Visão:')], [$baixa_visao], 
            [new TLabel('Surdez:')], [$surdez], 
            [new TLabel('Deficiência Auditiva:')], [$auditivo]
        );
        $this->form->addFields(
            [new TLabel('Física:')], [$fisi], 
            [new TLabel('Mental/Intelectual:')], [$intel], 
            [new TLabel('Múltipla:')], [$multiplo], 
            [new TLabel('Superdotação:')], [$superdotado]
        );
        $this->form->addFields([new TLabel('Observações Educação Especial:')], [$obs_saude]);

        // ---- 3. MAPEAMENTO GLOBAL DOS CAMPOS PARA O ADIANTI ----
        $this->form->setFields([
            $codaluno, $num_identific, $nome_social, $nome_civil, $sexo, $nascimento, $naturalidade, $naturalidade_uf, $nacionalidade, $cod_resp,
            $pai, $mae, $cert_num, $cert_livro, $cert_folha, $regmat, $ra, $rg, $rg_orgao, $cpf, $titulo_eleitor, $titulo_zona, $titulo_secao,
            $serv_militar, $serv_orgao, $serv_data_exp, $endereco, $numero, $complemento, $bairro, $cidade, $cep,
            $est_civil, $profissao, $tipo_escola_em, $senha_moodle, $telefone1, $telefone2, $telefone3, $telefone_fax, $email, $observacao1,
            $cor_raca, $obs_cor_raca1, $p_especial, $cigueira, $baixa_visao, $surdez, $auditivo, $fisi, $intel, $multiplo, $superdotado, $obs_saude
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
            
            // Tratamento reverso de máscara de data para persistência no BD (yyyy-mm-dd)
            if (!empty($aluno->Datanascimento)) {
                $aluno->Datanascimento = TDate::convertToMask($aluno->Datanascimento, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            if (!empty($aluno->ServicoMilitarDataExpedicao)) {
                $aluno->ServicoMilitarDataExpedicao = TDate::convertToMask($aluno->ServicoMilitarDataExpedicao, 'dd/mm/yyyy', 'yyyy-mm-dd');
            }
            
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
                
                // Conversão de datas do BD para a interface gráfica
                if(!empty($object->Datanascimento)) {
                    $object->Datanascimento = TDate::convertToMask($object->Datanascimento, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                if(!empty($object->ServicoMilitarDataExpedicao)) {
                    $object->ServicoMilitarDataExpedicao = TDate::convertToMask($object->ServicoMilitarDataExpedicao, 'yyyy-mm-dd', 'dd/mm/yyyy');
                }
                
                $this->form->setData($object);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
            }
        }
    }
}