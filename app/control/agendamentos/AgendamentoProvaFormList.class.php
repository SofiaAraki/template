<?php

class AgendamentoProvaFormList extends TPage
{
    protected $form; 
    protected $loaded;

    public function __construct($param)
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');
        $user = new SystemUser(TSession::getValue('userid'));
        $loggedUnit = TSession::getValue('userunitid'); 
        TTransaction::close();
        
        $this->form = new TQuickForm('form_AgendamentoProva');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table; width:100%'; 
        $this->form->setFormTitle('Agendamento de Prova');

        // Campos do Formulário
        $id = new THidden('id');
        $filename = new TFile('filename');
        $disciplina = new TCombo('disciplina');
        $turma = new TEntry('turma');
        $data_prova = new TDateTime('data_prova');
        $observacao = new TText('observacao');
        $status = new THidden('status');
        //$unidade = new TDBCombo('unidade', 'Felabs_DB', 'SystemUnit', 'id', 'name');
        $unidade = new TCombo('unidade');
        $unidade->addItems(['2' => 'Campus 1', '3' => 'Campus 2']);
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');

        $data_prova->setMask('dd/mm/yyyy hh:ii');
        $data_prova->setDatabaseMask('yyyy-mm-dd hh:ii');
        $turma->setEditable(false);

        // Carrega disciplinas semestrais
        TTransaction::open('dados_fei');
        $ano = date('Y');
        $mes = date('m');
        $semestre = ($mes < 7) ? 1 : 2;
            
        $criteria = new TCriteria;
        if ($user->funcao_legado == 'Professor') {
            $criteria->add(new TFilter('CodProfessor', '=', $user->systemuser_codlegado));
        } else {
            $disciplina->enableSearch();
        }
        
        $criteria->add(new TFilter('Ano', '=', $ano), TExpression::AND_OPERATOR);
        $criteria->add(new TFilter('Semestre', '=', $semestre), TExpression::AND_OPERATOR);
        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit), TExpression::AND_OPERATOR);

        $repos = VwProfessordisciplinassemestre::getObjects($criteria);
        $items = [];
        if ($repos) {
            foreach ($repos as $repo) {
                $items[$repo->CodGradeDisciplinaEtapaFrente] = $repo->NomeDisciplina;
            }
        }
        $disciplina->addItems($items);
        $disciplina->setChangeAction(new TAction(array($this, 'onChangeAction')));
        TTransaction::close();

        // Adiciona campos ao Formulário
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Disciplina', $disciplina, '50%', new TRequiredValidator);
        $this->form->addQuickField('Turma', $turma, '50%', new TRequiredValidator);
        $this->form->addQuickField('Data e horário da prova', $data_prova, '50%', new TRequiredValidator);
        $this->form->addQuickField('Observação', $observacao, '50%');
        $this->form->addQuickField('Status', $status, '50%');
        $this->form->addQuickField('Unidade', $unidade, '50%');
        $this->form->addQuickField('Professor', $system_user_id, '50%');
        $this->form->addQuickField('Data do registro', $data_reg, '50%');
        $this->form->addQuickField('Anexar arquivo', $filename, '50%');        
        
        $this->form->addQuickAction('Voltar', new TAction(['AgendamentoProvaListProfessor', 'onReload']), 'fa:arrow-left blue');
        $this->form->addQuickAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addQuickAction('Salvar', new TAction([$this, 'onSave']), 'far:save green');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TPanelGroup::pack('Agendamento de Provas', $this->form));
        
        parent::add($container);
    }

    public static function onChangeAction($param)
    {
        if (empty($param['disciplina'])) return;

        TTransaction::open('dados_fei');
        $ano = date('Y');
        $mes = date('m');
        $semestre = ($mes < 8) ? 1 : 2;

        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $param['disciplina']));
        $criteria->add(new TFilter('Ano', '=', $ano));
        $criteria->add(new TFilter('Semestre', '=', $semestre));

        $repo = VwProfessordisciplinassemestre::getObjects($criteria);

        if (!empty($repo)) {
            $obj = new StdClass;
            $obj->turma = $repo[0]->Identificacao;
            TForm::sendData('form_AgendamentoProva', $obj);
        }

        TTransaction::close();
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $user = new SystemUser(TSession::getValue('userid'));
                                
            $this->form->validate(); 
            
            $object = new AgendamentoProva;  
            $data = $this->form->getData(); 

            $data->system_user_id = $user->id;
            $data->data_reg = date('Y-m-d H:i:s');
            $data->status = 'Pendente';

            $object->fromArray((array) $data); 

            if ($object->filename)
            {
                $today = date("Ymd");
                $source_file = 'tmp/'.$object->filename;
                $arquivo = TSession::getValue('login') . '_' . $today . '_' . basename($object->filename);
                $target_file = 'arquivos/provas/' . $arquivo;
                
                if (file_exists($source_file)) {
                    rename($source_file, $target_file);
                }
                $object->filename = $arquivo;
            }

            $object->store(); 
            TTransaction::close(); 
            
            // Redireciona com sucesso de volta para a tela de listagem centralizada
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), TApplication::loadPage('AgendamentoProvaListProfessor', 'onReload')); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData($this->form->getData()); 
            TTransaction::rollback(); 
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                TTransaction::open('Felabs_DB');
                $object = new AgendamentoProva($param['key']);
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(true);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
