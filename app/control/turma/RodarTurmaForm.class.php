<?php

class RodarTurmaForm extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_rodar_turmas');
        $this->form->setFormTitle('Processamento em Lote: Rodar Turmas (Genesi)');

        // ---- PARAMETROS GERAIS ----
        $opcao_mat_inicial = new TCheckButton('matricula_inicial');
        $opcao_reg_mat     = new TCheckButton('registrar_matriculas');
        $opcao_reg_mat->setValue('S');

        $restringir_docs   = new TCheckButton('restringir_docs');
        $restringir_docs->setValue('S');

        // ---- DEFINIÇÃO DAS OPÇÕES DOS COMBOS ----
        $opcoes_semestre = [
            '1' => '1º Semestre',
            '2' => '2º Semestre'
        ];

        $opcoes_etapas = [
            '1'  => '1ª Etapa',
            '2'  => '2ª Etapa',
            '3'  => '3ª Etapa',
            '4'  => '4ª Etapa',
            '5'  => '5ª Etapa',
            '6'  => '6ª Etapa',
            '7'  => '7ª Etapa',
            '8'  => '8ª Etapa',
            '9'  => '9ª Etapa',
            '10' => '10ª Etapa'
        ];

        // ---- BLOCO 1: TURMA DE ORIGEM (MATRÍCULAS ATUAIS) ----
        // Adicionado o Filtro de Curso/Grade de Origem
        $origem_curso = new TDBCombo('origem_curso', 'dados_fei', 'FiGradeCurso', 'CodGradecurso', 'Descricao');
        $origem_curso->enableSearch();

        $origem_ano = new TEntry('origem_ano');
        $origem_ano->setValue(date('Y'));
        
        $origem_semestre = new TCombo('origem_semestre'); 
        $origem_semestre->addItems($opcoes_semestre);
        $origem_semestre->setValue('1');
        
        $origem_etapa = new TCombo('origem_etapa'); 
        $origem_etapa->addItems($opcoes_etapas);
        $origem_etapa->setValue('1');

        // Seu TDBCombo de volta! Começa vazio para o AJAX preencher de acordo com o Curso, Ano, Semestre e Etapa
        $origem_turma_id = new TDBCombo('origem_turma_id', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $origem_turma_id->enableSearch();

        // ---- BLOCO 2: EFETUAR MATRÍCULAS (TURMA DE DESTINO) ----
        // Adicionado o Filtro de Curso/Grade de Destino
        $destino_curso = new TDBCombo('destino_curso', 'dados_fei', 'FiGradeCurso', 'CodGradecurso', 'Descricao');
        $destino_curso->enableSearch();

        $destino_ano = new TEntry('destino_ano');
        $destino_ano->setValue(date('Y'));
        
        $destino_semestre = new TCombo('destino_semestre'); 
        $destino_semestre->addItems($opcoes_semestre);
        $destino_semestre->setValue('1');
        
        $destino_etapa = new TCombo('destino_etapa'); 
        $destino_etapa->addItems($opcoes_etapas);
        $destino_etapa->setValue('1');

        // Seu TDBCombo de Destino de volta!
        $destino_turma_id = new TDBCombo('destino_turma_id', 'dados_fei', 'FiTurmaEtapa', 'CodTurmaetapa', 'Identificacao');
        $destino_turma_id->enableSearch();

        // ---- CONFIGURAÇÃO DAS AÇÕES DE MUDANÇA DINÂMICA (AJAX) ----
        $change_origem = new TAction([$this, 'onChangeOrigem']);
        $change_destino = new TAction([$this, 'onChangeDestino']);

        // Disparar o AJAX também ao mudar o Curso selecionado!
        $origem_curso->setChangeAction($change_origem);
        $origem_ano->setExitAction($change_origem);
        $origem_semestre->setChangeAction($change_origem);
        $origem_etapa->setChangeAction($change_origem);

        $destino_curso->setChangeAction($change_destino);
        $destino_ano->setExitAction($change_destino);
        $destino_semestre->setChangeAction($change_destino);
        $destino_etapa->setChangeAction($change_destino);

        // ---- ADICIONANDO OS CAMPOS AO LAYOUT ----
        $this->form->addContent([new TFormSeparator('Opções de Processamento')]);

        $this->form->addFields(
            [new TLabel('Matrícula Inicial:')], [$opcao_mat_inicial],
            [new TLabel('Registrar Matrículas:')], [$opcao_reg_mat],
            [new TLabel('Restringir p/ Falta Docs:')], [$restringir_docs]
        );

        $this->form->addContent(['<div class="alert alert-info" style="margin-top:15px; font-weight:bold;">Requerimento por Turma (Matrículas Atuais)</div>']);
        
        // Incluindo o campo de Curso no Layout de Origem
        $this->form->addFields([new TLabel('Curso Origem:')], [$origem_curso]);
        $this->form->addFields(
            [new TLabel('Ano:')], [$origem_ano],
            [new TLabel('Semestre:')], [$origem_semestre],
            [new TLabel('Etapa:')], [$origem_etapa]
        );
        $this->form->addFields([new TLabel('Turma Origem:')], [$origem_turma_id]);

        $this->form->addContent(['<div class="alert alert-warning" style="margin-top:15px; font-weight:bold;">Efetuar Matrículas (Destino Novo)</div>']);
        
        // Incluindo o campo de Curso no Layout de Destino
        $this->form->addFields([new TLabel('Curso Destino:')], [$destino_curso]);
        $this->form->addFields(
            [new TLabel('Ano:')], [$destino_ano],
            [new TLabel('Semestre:')], [$destino_semestre],
            [new TLabel('Etapa:')], [$destino_etapa]
        );
        $this->form->addFields([new TLabel('Turma Destino:')], [$destino_turma_id]);

        // Validações Obrigatórias
        $origem_curso->addValidation('Curso Origem', new TRequiredValidator);
        $destino_curso->addValidation('Curso Destino', new TRequiredValidator);
        $origem_turma_id->addValidation('Turma Origem', new TRequiredValidator);
        $destino_turma_id->addValidation('Turma Destino', new TRequiredValidator);

        $this->form->addAction('Rodar Turma / Gerar Requerimentos', new TAction([$this, 'onProcessar']), 'fa:cog green');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        parent::add($container);
    }

    /**
     * Altera dinamicamente o combo de Turma de Origem
     */
    public static function onChangeOrigem($param)
    {
        try 
        {
            TTransaction::open('dados_fei');
            
            $curso    = !empty($param['origem_curso']) ? $param['origem_curso'] : null;
            $ano      = !empty($param['origem_ano']) ? $param['origem_ano'] : null;
            $semestre = !empty($param['origem_semestre']) ? $param['origem_semestre'] : null;
            $etapa    = !empty($param['origem_etapa']) ? $param['origem_etapa'] : null;

            $options = [];
            // O filtro agora exige que o Curso, Ano e Semestre estejam preenchidos para buscar as turmas corretas
            if ($curso && $ano && $semestre) 
            {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('Ano', '=', $ano));
                $criteria->add(new TFilter('Semestre', '=', $semestre));
                
                // Se sua tabela FI_Turma_etapa tiver CodGradecurso vinculada:
                $criteria->add(new TFilter('CodGradecurso', '=', $curso)); 
                
                if ($etapa) {
                    $criteria->add(new TFilter('CodGradeEtapa', '=', $etapa));
                }
                
                $repository = new TRepository('FiTurmaEtapa');
                $turmas = $repository->load($criteria);
                
                if ($turmas) 
                {
                    foreach ($turmas as $turma) 
                    {
                        $options[$turma->CodTurmaetapa] = "{$turma->Identificacao} ({$turma->Periodo})";
                    }
                }
            }
            
            // O reload funciona perfeitamente atualizando o TDBCombo!
            TCombo::reload('form_rodar_turmas', 'origem_turma_id', $options);
            TTransaction::close();
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Altera dinamicamente o combo de Turma de Destino
     */
    public static function onChangeDestino($param)
    {
        try 
        {
            TTransaction::open('dados_fei');
            
            $curso    = !empty($param['destino_curso']) ? $param['destino_curso'] : null;
            $ano      = !empty($param['destino_ano']) ? $param['destino_ano'] : null;
            $semestre = !empty($param['destino_semestre']) ? $param['destino_semestre'] : null;
            $etapa    = !empty($param['destino_etapa']) ? $param['destino_etapa'] : null;

            $options = [];
            if ($curso && $ano && $semestre) 
            {
                $criteria = new TCriteria;
                $criteria->add(new TFilter('Ano', '=', $ano));
                $criteria->add(new TFilter('Semestre', '=', $semestre));
                
                // Se sua tabela FI_Turma_etapa tiver CodGradecurso vinculada:
                $criteria->add(new TFilter('CodGradecurso', '=', $curso)); 

                if ($etapa) {
                    $criteria->add(new TFilter('CodGradeEtapa', '=', $etapa));
                }
                
                $repository = new TRepository('FiTurmaEtapa');
                $turmas = $repository->load($criteria);
                
                if ($turmas) 
                {
                    foreach ($turmas as $turma) 
                    {
                        $options[$turma->CodTurmaetapa] = "{$turma->Identificacao} ({$turma->Periodo})";
                    }
                }
            }
            
            TCombo::reload('form_rodar_turmas', 'destino_turma_id', $options);
            TTransaction::close();
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onProcessar($param)
    {
        try
        {
            $this->form->validate();
            $dados = $this->form->getData();

            TTransaction::open('dados_fei');
            $resultado = RodarTurmaService::executarTransicao((array)$dados);
            TTransaction::close();

            $mensagem = "<b>Processamento Concluído com Sucesso!</b><br><br>";
            $mensagem .= "• Alunos promovidos/matriculados: <span class='label label-success'>{$resultado['sucessos']}</span><br>";
            $mensagem .= "• Alunos retidos ou com inconsistência: <span class='label label-danger'>" . count($resultado['erros']) . "</span>";

            if (!empty($resultado['erros']))
            {
                TSession::setValue('log_erros_rodar_turma', $resultado['erros']);
                new TMessage('warning', $mensagem, new TAction(['RelatorioErrosTurmaWindow', 'onLoad']));
            }
            else
            {
                new TMessage('info', $mensagem);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}