<?php

/**
 * ProgramaEnsinoDisciplinaForm
 * Classe ÚNICA responsável pela Criação e Edição unificadas.
 */
class ProgramaEnsinoDisciplinaForm extends TPage
{
    protected $form;    

    public function __construct($param)
    {
        parent::__construct();
        
        // Inicializa o formulário
        $this->form = new BootstrapFormBuilder('form_ProgramaEnsinoDisciplina');
        $this->form->setFormTitle('Programa de Ensino da Disciplina');
        
        // Criação dos campos comuns
        $id                     = new THidden('id');
        $system_user_id         = new TEntry('system_user_id');
        $curso                  = new TEntry('curso');
        $nome                   = new TEntry('nome');
        $turma                  = new TEntry('turma');
        $codigo                 = new TEntry('codigo');
        $obg_optativa           = new TRadioGroup('obg_optativa');
        $pre_requisito          = new TEntry('pre_requisito');
        $co_requisito           = new TEntry('co_requisito');
        $periodo                = new TEntry('periodo');
        $semestral_anual        = new TRadioGroup('semestral_anual');
        $credito                = new TEntry('credito');
        $total                  = new TEntry('total');
        $semanal                = new TEntry('semanal');
        $teorica                = new TEntry('teorica');
        $pratica                = new TEntry('pratica');
        $teorica_pratica        = new TEntry('teorica_pratica');
        $ementa                 = new TText('ementa');
        $objetivos              = new TText('objetivos');
        $conteudo_programatico  = new TText('conteudo_programatico');
        $bibliografia_basica    = new TText('bibliografia_basica');
        $bibliografia_complementar = new TText('bibliografia_complementar');
        $unidade                = new THidden('unidade');
        $metodologia            = new TText('metodologia');
        $criterio_avaliacao     = new TText('criterio_avaliacao');

        // Configurações básicas de campos não editáveis
        $curso->setEditable(FALSE);
        $curso->setSize('100%');
        $turma->setEditable(FALSE);
        $codigo->setEditable(FALSE);
        $periodo->setEditable(FALSE);
        $codigo->setSize('30%');
        
        // Obtém o usuário ativo na sessão
        TTransaction::open('Felabs_DB');
        $user = new SystemUser(TSession::getValue('userid'));
        $loggedUnitProf = TSession::getValue('userunitid');
        TTransaction::close();

        // --- LÓGICA CORRIGIDA: Só considera Edição se houver uma ID/Chave informada ---
        $isEdit = !empty($param['key']) || !empty($param['id']);

        if ($isEdit) {
            // Modo EDIÇÃO: Mostra apenas o nome fixo da disciplina
            $disciplina_display = new TEntry('nome_disciplina');
            $disciplina_display->setEditable(FALSE);
            $disciplina_display->setSize('100%');
            
            $disciplina = new THidden('disciplina');
        } else {
            // Modo INCLUSÃO: Carrega a listagem em ComboBox perfeitamente liberada
            $disciplina = new TCombo('disciplina');
            $disciplina_display = $disciplina;
            
            TTransaction::open('Dados_Fei');
            $repository = new TRepository('VwProfessordisciplinassemestre');
            $criteria = new TCriteria;            
            $criteria->add(new TFilter('CodProfessor', '=', $user->systemuser_codlegado));
            $criteria->add(new TFilter('Ano', '=', date('Y')));
            
            $mes = date('m');
            $semestre = ($mes < 8) ? 1 : 2;
            $criteria->add(new TFilter('Semestre', '=', $semestre));
            $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf));

            $repo = $repository->load($criteria);
            $items = [];
            if ($repo) {
                foreach ($repo as $row) {
                    $items[$row->CodGradeDisciplinaEtapaFrente] = $row->NomeDisciplina;
                }
            }
            TTransaction::close();

            $disciplina->addItems($items);
            $disciplina->setChangeAction(new TAction([$this, 'onChangeAction']));
            $disciplina->addValidation('Disciplina', new TRequiredValidator);
        }

        // Opções de seleções estruturadas
        $obg_optativa->addItems(['Obrigatória' => 'Obrigatória', 'Optativa' => 'Optativa']);
        $obg_optativa->setLayout('vertical');
        $obg_optativa->setSize('100%');
        
        $semestral_anual->addItems(['Semestral' => 'Semestral', 'Anual' => 'Anual']);
        $semestral_anual->setLayout('vertical');
        $semestral_anual->setSize('100%');

        // Validações obrigatórias
        $obg_optativa->addValidation('Obrigatória/Optativa', new TRequiredValidator);
        $semestral_anual->addValidation('Semestral/Anual', new TRequiredValidator);
        $ementa->addValidation('Ementa', new TRequiredValidator);
        $objetivos->addValidation('Objetivos', new TRequiredValidator);
        $conteudo_programatico->addValidation('Conteúdo Programático', new TRequiredValidator);
        $bibliografia_basica->addValidation('Bibliografia Básica', new TRequiredValidator);
        $bibliografia_complementar->addValidation('Bibliografia Complementar', new TRequiredValidator);

        // --- MONTAGEM INTERFACE EM TELA ---
        $this->form->addFields([$id]);
        if ($isEdit) {
            $this->form->addFields([$disciplina]);
        }
        $this->form->addFields([$unidade]);
                
        $this->form->addContent(['<h4>Dados da disciplina</h4><hr>']);
        $this->form->addFields([new TLabel('Disciplina')], [$disciplina_display]);
        $this->form->addFields([new TLabel('Curso')], [$curso], [new TLabel('Turma')], [$turma]);
        $this->form->addFields([new TLabel('Código')], [$codigo], [new TLabel('Período')], [$periodo]);
        $this->form->addFields([new TLabel('Pré-Requisitos')], [$pre_requisito], [new TLabel('Correquisitos')], [$co_requisito]);
        $this->form->addFields([new TLabel('Periodicidade')], [$semestral_anual], [new TLabel('Classificação')], [$obg_optativa]);

        $this->form->addContent(['<br><h4>Carga Horária</h4><hr>']);
        $this->form->addFields([new TLabel('Crédito')], [$credito], [new TLabel('Total')], [$total], [new TLabel('Semanal')], [$semanal]);

        $this->form->addContent(['<br><h4>Distribuição Carga Horária Semanal</h4><hr>']);
        $this->form->addFields([new TLabel('Teórica')], [$teorica], [new TLabel('Prática')], [$pratica], [new TLabel('Teórica/Prática')], [$teorica_pratica]);
                        
        $this->form->addContent(['<br><h4>Plano de Ensino</h4><hr>']);        
        $this->form->addFields([new TLabel('Ementa')], [$ementa]);
        $this->form->addFields([new TLabel('Objetivos')], [$objetivos]);
        $this->form->addFields([new TLabel('Conteúdo Programático')], [$conteudo_programatico]);
        
        $this->form->addFields([new TLabel('Metodologia de Ensino')], [$metodologia]);
        $this->form->addFields([new TLabel('Critérios de Avaliação')], [$criterio_avaliacao]);
        
        $this->form->addFields([new TLabel('Bibliografia Básica:')], [$bibliografia_basica]);
        $this->form->addFields([new TLabel('Bibliografia Complementar:')], [$bibliografia_complementar]);

        // Dimensionamento
        $ementa->setSize('100%', 250);
        $objetivos->setSize('100%', 200);
        $conteudo_programatico->setSize('100%', 400);
        $metodologia->setSize('100%', 150);
        $criterio_avaliacao->setSize('100%', 150);
        $bibliografia_basica->setSize('100%', 150);
        $bibliografia_complementar->setSize('100%', 200);
         
        // Ações globais do formulário
        $this->form->addAction('Voltar', new TAction(['ProgramaEnsinoDisciplinaList', 'onReload']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        
        if (!$isEdit) {
            $objValoresIniciais = new StdClass;
            $objValoresIniciais->unidade = TSession::getValue('userunitid');
            $this->form->setData($objValoresIniciais);
        }

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ProgramaEnsinoDisciplinaList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public static function onChangeAction($param)
    {
        try {
            if (empty($param['disciplina'])) return;

            TTransaction::open('Dados_Fei');   
            $repository = new TRepository('VwProfessordisciplinassemestre');
             
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $param['disciplina']));
            $criteria->add(new TFilter('Ano', '=', date('Y')));
            
            $mes = date('m');
            $semestre = ($mes < 8) ? 1 : 2;
            $criteria->add(new TFilter('Semestre', '=', $semestre));

            $repo = $repository->load($criteria);
            if (!empty($repo)) {
                $obj = new StdClass;
                $obj->curso   = $repo[0]->NomeCurso;
                $obj->turma   = $repo[0]->Identificacao;
                $obj->periodo = $repo[0]->Periodo;
                $obj->codigo  = $repo[0]->CodDisciplina;
                TForm::sendData('form_ProgramaEnsinoDisciplina', $obj);
            }
            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
        }
    }

    public function onSave($param)
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            $ano = date('Y');
            $mes = date('m');
            $semestre = ($mes < 8) ? 1 : 2;

            $obj = new StdClass;

            // Busca os dados da View na base Dados_Fei
            TTransaction::open('Dados_Fei');   
            $repository = new TRepository('VwProfessordisciplinassemestre');            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $param['disciplina']));
            $criteria->add(new TFilter('Ano', '=', $ano));
            $criteria->add(new TFilter('Semestre', '=', $semestre));

            $repo = $repository->load($criteria);
            if (!empty($repo)) {
                $obj->nome           = $repo[0]->NomeEntidade;
                $obj->CodCurso       = $repo[0]->CodCurso;
                $obj->CodProfessor   = $repo[0]->CodProfessor;
                $obj->CodGradecurso  = $repo[0]->CodGradecurso;
            }
            TTransaction::close(); 

            // Salva na base principal Felabs_DB
            TTransaction::open('Felabs_DB');            
            
            $object = new ProgramaEnsinoDisciplina($data->id ?? null);
            $object->fromArray((array) $data);
            
            $object->system_user_id = TSession::getValue('userid');
            $object->unidade        = TSession::getValue('userunitid');
            
            if (isset($obj->nome)) {
                $object->nome           = $obj->nome;
                $object->CodCurso       = $obj->CodCurso;
                $object->Codprofessor   = $obj->CodProfessor;
                $object->CodGradecurso  = $obj->CodGradecurso;
            }
            
            $object->store();           
            
            $data->id = $object->id;
            $this->form->setData($data);
            
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('ProgramaEnsinoDisciplinaList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                $object = new ProgramaEnsinoDisciplina($key);
                $loggedUnitProf = TSession::getValue('userunitid');
                TTransaction::close();

                $mes = date("m", strtotime($object->data_reg));
                $ano = date("Y", strtotime($object->data_reg));
                $semestre = ($mes < 8) ? 1 : 2;

                TTransaction::open('Dados_Fei');
                $criteria = new TCriteria;
                $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
                $criteria->add(new TFilter('Ano', '=', $ano));
                $criteria->add(new TFilter('Semestre', '=', $semestre));
                $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf));

                $nomesdisc = VwProfessordisciplinassemestre::getObjects($criteria);
                
                if (!empty($nomesdisc) && isset($nomesdisc[0])) {
                    $object->nome_disciplina = $nomesdisc[0]->NomeDisciplina;
                } else {
                    $object->nome_disciplina = "Disciplina Cód: " . $object->disciplina;
                }
                TTransaction::close();

                $this->form->setData($object); 
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    // Método acionado pelo botão de Cadastrar Novo que limpa os campos com segurança
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
}