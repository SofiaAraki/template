<?php
/**
 * FiAlunoForm Form
 * @author  Pamella Scapim 
 * Formulário de atualização de cadastro de CEP, WhatsApp e email dos alunos.
 */
class AtualizaDadosForm extends TWindow
{
    protected $form;
    private $codAluno;

    public function __construct($param)
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::removePadding();
        parent::removeTitleBar();
        parent::disableEscape();
        
        // with: 500, height: automatic
        parent::setSize(0.6, null); // use 0.6, 0.4 (for relative sizes 60%, 40%)

        // busca usuário logado
        TTransaction::open('Felabs_DB');
        $user_id = TSession::getValue('userid');
        $user = new SystemUser($user_id);
        $this->codAluno = $user->systemuser_codlegado;
        $nomeAluno = $user->name;
        TTransaction::close();

        // cria formulário
        $this->form = new BootstrapFormBuilder('form_FiAluno');
        $this->form->setFormTitle('Atualização de Dados Obrigatória');

        // campos
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $Endereco = new TEntry('Endereco');
        $EnderecoNumero = new TEntry('EnderecoNumero');
        $Bairro = new TEntry('Bairro');
        $CodCidade = new TDBCombo(
            'CodCidade',
            'dados_fei',
            'FiCidade',
            'CodCidade',
            'Nome'
        );
        $Cep = new TEntry('Cep');
        $Telefone = new TEntry('Telefone');
        $Email = new TEntry('Email');

        // busca CEP
        $Cep->setExitAction(new TAction([$this,'onBuscaCep']));
        $Cep->setMask('99999-999');

        // campos no formulário
        $this->form->addFields([new TLabel('Código do Aluno:')], [$Codaluno]);
        $this->form->addFields([new TLabel('Nome:')], [$Nome]);
        $this->form->addFields([new TLabel('<span style="color:red; font-weight:bold;">NOVO CEP:*</span>')], [$Cep]);
        $this->form->addFields([new TLabel('Endereço')], [$Endereco],[new TLabel('Número')], [$EnderecoNumero]);
        $this->form->addFields([new TLabel('Bairro:')], [$Bairro],[new TLabel('Cidade')], [$CodCidade]);
        $this->form->addFields([new TLabel('<span style="color:red; font-weight:bold;">WhatsApp:*</span>')], [$Telefone]);
        $this->form->addFields([new TLabel('<span style="color:red; font-weight:bold;">Email:*</span>')], [$Email]);

        // validações
        $Endereco->addValidation('Endereco', new TRequiredValidator);
        $EnderecoNumero->addValidation('Numero', new TRequiredValidator);
        $CodCidade->addValidation('Cidade', new TRequiredValidator);
        $Cep->addValidation('Cep', new TRequiredValidator);
        $Telefone->addValidation('Telefone', new TRequiredValidator);
        $Email->addValidation('Email', new TRequiredValidator);

        // tamanho
        $Codaluno->setSize('25%');
        $Nome->setSize('100%');
        $Endereco->setSize('100%');
        $EnderecoNumero->setSize('25%');
        $Bairro->setSize('100%');
        $CodCidade->setSize('100%');
        $Cep->setSize('50%');
        $Telefone->setSize('50%');
        $Email->setSize('100%');

        // bloqueia campos
        $Codaluno->setEditable(FALSE);
        $Nome->setEditable(FALSE);
        $Cep->placeholder = 'Digite novamente seu CEP para validar o endereço';
        $Cep->style = 'font-weight:bold; background-color:#e7f3ff; border:1px solid #b3d7ff;';
        $Telefone->style = 'font-weight:bold; background-color:#e7f3ff; border:1px solid #b3d7ff;';
        $Email->style = 'font-weight:bold; background-color:#e7f3ff; border:1px solid #b3d7ff;';
        // ação salvar
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');

        // container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TAlert('warning', '⚠<b> ATUALIZAÇÃO CADASTRAL OBRIGATÓRIA</b><br>
        Seu cadastro está desatualizado há mais de 180 dias.<br>
        Para continuar utilizando o sistema, é necessário atualizar seus dados abaixo.'));
        $container->add($this->form);

        parent::add($container);

        // abre automaticamente o cadastro do aluno
        $this->onEdit(['key' => $this->codAluno]);
    }

public function onSave($param)
{
    try
    {
        TTransaction::open('dados_fei');

        $this->form->validate();

        $data = $this->form->getData();

        // carrega o registro original
        $object = new FiAluno($this->codAluno);

        // campos permitidos
        $fields = [
            'Cep',
            'Endereco',
            'EnderecoNumero',
            'Bairro',
            'CodCidade',
            'Telefone',
            'Email'
        ];

        $set = [];
        $alterou = false;

        foreach ($fields as $field)
        {
            $valorBanco = trim((string) $object->$field);
            $valorForm  = trim((string) $data->$field);

            if ($valorBanco !== $valorForm)
            {
                $valor = addslashes($valorForm);
                $set[] = "{$field} = '{$valor}'";
                $alterou = true;
            }
        }

        if ($alterou)
        {
            $set[] = "DataAtualizacao = '".date('Y-m-d H:i:s')."'";

            $sql = "UPDATE FI_Aluno SET " . implode(', ', $set) . 
                   " WHERE Codaluno = '{$this->codAluno}'";

            TTransaction::get()->exec($sql);
        }

        // recarrega dados atualizados no form
        $this->form->setData($data);

        TTransaction::close();

        new TMessage('info','Dados atualizados com sucesso');
        TWindow::closeWindow();
    }
    catch(Exception $e)
    {
        new TMessage('error',$e->getMessage());
        TTransaction::rollback();
    }
}


    public function onEdit($param)
    {
        try
        {
            TTransaction::open('dados_fei');

            $object = new FiAluno($param['key']);

            if ($object->Codaluno)
            {
                // limpa o CEP para forçar atualização
                $object->Cep = null;

                $this->form->setData($object);
            }
            else
            {
                $data = new stdClass;
                $data->Codaluno = $param['key'];
                $this->form->setData($data);
            }

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onBuscaCep($param)
    {
        try
        {
            $cep = preg_replace('/[^0-9]/','',$param['Cep']);

            if ($cep)
            {
                $url = "https://viacep.com.br/ws/{$cep}/json/";
                $json = file_get_contents($url);
                $dados = json_decode($json);

                if (empty($dados->erro))
                {
                    $obj = new stdClass;
                    $obj->Endereco = $dados->logradouro;
                    $obj->Bairro   = $dados->bairro;

                    if (!empty($dados->ibge))
                    {
                        TTransaction::open('dados_fei');

                        $criteria = new TCriteria;
                        $criteria->add(new TFilter('CODCIDADE_INEP','=',$dados->ibge));

                        $cidade = FiCidade::getObjects($criteria);

                        if ($cidade)
                        {
                            $obj->CodCidade = $cidade[0]->CodCidade;
                        }

                        TTransaction::close();
                    }

                    TForm::sendData('form_FiAluno', $obj);
                }
            }
        }
        catch(Exception $e)
        {
            new TMessage('error',$e->getMessage());
        }
    }

    private function sanitizeData($data)
    {
        foreach ($data as $campo => $valor)
        {
            if (is_string($valor))
            {
                $valor = mb_convert_encoding($valor, 'UTF-8', 'UTF-8');
                $valor = preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}]/u', '', $valor);
                $valor = str_replace("\xC2\xA0", ' ', $valor);
                $valor = trim($valor);

                $data->$campo = $valor;
            }
        }

        return $data;
    }


}