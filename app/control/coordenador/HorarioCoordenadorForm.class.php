<?php

class HorarioCoordenadorForm extends TPage
{
    protected $form;

    public function __construct($param)
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_HorarioCoordenador');
        $this->form->setFormTitle('Montagem do Horário Semestral');

        // 1. Definição dos Parâmetros Iniciais (Cabeçalho)
        $id               = new THidden('id');
        $nome_horario     = new TEntry('nome_horario');
        $curso            = new TCombo('curso');
        $periodo          = new TCombo('periodo');
        $etapa            = new TCombo('etapa');
        $ano_semestre     = new TEntry('ano_semestre');
        $qtd_aulas        = new TCombo('qtd_aulas');

        // Configurações e Opções
        $nome_horario->placeholder = 'Ex: Agronomia Noturno 1º Semestre 2026/2';

        $curso->addItems([
            'Administração' => 'Administração',
            'Agrocomputação' => 'Agrocomputação',
            'Agronomia' => 'Agronomia',
            'Biomedicina' => 'Biomedicina',
            'Ciências Contábeis' => 'Ciências Contábeis',
            'Direito' => 'Direito',
            'Enfermagem' => 'Enfermagem',
            'Engenharia Civil' => 'Engenharia Civil',
            'Engenharia Elétrica' => 'Engenharia Elétrica',
            'Engenharia Mecânica' => 'Engenharia Mecânica',
            'Engenharia de Produção' => 'Engenharia de Produção',
            'Medicina Veterinária' => 'Medicina Veterinária',
            'Pedagogia' => 'Pedagogia',
            'Psicologia' => 'Psicologia',
            'Sistemas de Informação' => 'Sistemas de Informação',
        ]);

        $periodo->addItems([
            'Matutino' => 'Matutino', 'Vespertino' => 'Vespertino', 
            'Noturno' => 'Noturno', 'Integral' => 'Integral'
        ]);

        $etapa->addItems([
            '1' => '1º semestre', '2' => '2º semestre',
            '3' => '3º semestre', '4' => '4º semestre',
            '5' => '5º semestre', '6' => '6º semestre',
            '7' => '7º semestre', '8' => '8º semestre',
            '9' => '9º semestre', '10' => '10º semestre',
        ]);

        $ano_semestre->placeholder = 'Ex: 2026/2';
        $ano_semestre->setmask('9999/9');
        
        $qtd_aulas->addItems([
            '2' => '2 Slots/Aulas', '3' => '3 Slots/Aulas',
            '4' => '4 Slots/Aulas', '5' => '5 Slots/Aulas', 
            '6' => '6 Slots/Aulas', '7' => '7 Slots/Aulas',
            '8' => '8 Slots/Aulas', '9' => '9 Slots/Aulas',
            '10' => '10 Slots/Aulas', '11' => '11 Slots/Aulas',
            '12' => '12 Slots/Aulas', '13' => '13 Slots/Aulas',
        ]);

        $change_action = new TAction([$this, 'onChangeConfiguracao']);
        $qtd_aulas->setChangeAction($change_action);

        $this->form->addFields([$id]);
        $this->form->addFields(
            [new TLabel('Nome do Horário:')], [$nome_horario],
            [new TLabel('Curso:')], [$curso]
        );
        $this->form->addFields(
            [new TLabel('Período / Turno:')], [$periodo],
            [new TLabel('Etapa / Ciclo:')], [$etapa]
        );
        $this->form->addFields(
            [new TLabel('Ano / Semestre:')], [$ano_semestre],
            [new TLabel('Qtd. Linhas Totais (Aulas/Pausas):')], [$qtd_aulas]
        );

        // 1. Define o padrão inicial de segurança
        $linhas_aulas = 3;

        // 2. Se veio de uma mudança de combo (onChange), captura diretamente
        if (isset($param['qtd_aulas'])) 
        {
            $linhas_aulas = (int)$param['qtd_aulas'];
        }
        // 3. Se veio da listagem (Edição), vai ao banco ANTES de montar o HTML para saber o tamanho real
        else if (!empty($param['nome_horario']) || !empty($param['key'])) 
        {
            $horario_busca = $param['nome_horario'] ?? $param['key'];
            try {
                TTransaction::open('Felabs_DB');
                $registro_aux = HorarioCoordenador::where('nome_horario', '=', $horario_busca)->first();
                if ($registro_aux) {
                    $linhas_aulas = (int)$registro_aux->qtd_aulas;
                }
                TTransaction::close();
            } catch (Exception $e) {
                try { TTransaction::rollback(); } catch(Exception $ex){}
            }
        }

        // 2. Renderização da Matriz Dinâmica
        $tabela_grade = new TTable;
        $tabela_grade->style = 'width: 100%; border-collapse: collapse; margin-top: 20px;';
        $tabela_grade->class = 'table table-bordered text-center';

        $linha_cabecalho = $tabela_grade->addRow();
        $linha_cabecalho->addCell('<b>Tipo / Horário</b>', 'center', 'active');
        $linha_cabecalho->addCell('<b>2ª Feira</b>', 'center', 'active');
        $linha_cabecalho->addCell('<b>3ª Feira</b>', 'center', 'active');
        $linha_cabecalho->addCell('<b>4ª Feira</b>', 'center', 'active');
        $linha_cabecalho->addCell('<b>5ª Feira</b>', 'center', 'active');
        $linha_cabecalho->addCell('<b>6ª Feira</b>', 'center', 'active');
        $linha_cabecalho->addCell('<b>Sábado</b>', 'center', 'active');

        for ($ordem = 1; $ordem <= $linhas_aulas; $ordem++) 
        {
            $nova_linha = $tabela_grade->addRow();
            
            $eh_int = new TCombo("eh_intervalo_{$ordem}");
            $eh_int->addItems(['0' => 'Aula', '1' => 'Intervalo']);
            $eh_int->setSize('100%');
            $eh_int->setDefaultOption(false);
            
            $campo_horario = new TEntry("horario_aula_{$ordem}");
            $campo_horario->placeholder = "Ex: 07:30 às 08:20";
            $campo_horario->setSize('100%');
            
            $this->form->addField($eh_int);
            $this->form->addField($campo_horario);
            
            $vbox_controle = new TVBox;
            $vbox_controle->style = 'width: 100%; padding: 2px;';
            $vbox_controle->add($eh_int);
            $vbox_controle->add($campo_horario);
            
            $cell_controle = $nova_linha->addCell($vbox_controle);
            $cell_controle->style = "vertical-align: middle; min-width: 130px;";

            for ($dia = 2; $dia <= 7; $dia++) 
            {
                $campo_disc = new TEntry("grade_disc_{$ordem}_{$dia}");
                $campo_disc->placeholder = 'Disciplina';
                $campo_disc->setSize('100%');
                
                $campo_prof = new TEntry("grade_prof_{$ordem}_{$dia}");
                $campo_prof->placeholder = 'Professor';
                $campo_prof->setSize('100%');
                
                $this->form->addField($campo_disc);
                $this->form->addField($campo_prof);

                $vbox_celula = new TVBox;
                $vbox_celula->style = 'width: 100%; margin:0; padding: 2px;';
                $vbox_celula->add($campo_disc);
                $vbox_celula->add($campo_prof);

                $nova_linha->addCell($vbox_celula);
            }
        }

        $this->form->addContent([$tabela_grade]);

        $this->form->addActionLink('Voltar', new TAction(['HorarioCoordenadorList', 'onReload']), 'fa:arrow-left blue');
        $this->form->addAction('Salvar Horário', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Gerar PDF', new TAction([$this, 'onGerarRelatorio']), 'fa:print red');

        if (!empty($param['nome_horario']) || !empty($param['key'])) 
        {
            $horario_busca = $param['nome_horario'] ?? $param['key'];
            try {
                TTransaction::open('Felabs_DB');
                $dados_form = new StdClass;
                
                $aulas_salvas = HorarioCoordenador::where('nome_horario', '=', $horario_busca)->load();
                if ($aulas_salvas) {
                    foreach ($aulas_salvas as $aula) {
                        $dados_form->nome_horario      = $aula->nome_horario;
                        $dados_form->curso             = $aula->curso;
                        $dados_form->periodo           = $aula->periodo;
                        $dados_form->etapa             = $aula->etapa;
                        $dados_form->ano_semestre      = $aula->ano_semestre;
                        $dados_form->qtd_aulas         = $aula->qtd_aulas;
                        
                        $dados_form->{"eh_intervalo_{$aula->numero_ordem_aula}"} = $aula->eh_intervalo;
                        $dados_form->{"horario_aula_{$aula->numero_ordem_aula}"}  = $aula->horario_aula;
                        $dados_form->{"grade_disc_{$aula->numero_ordem_aula}_{$aula->dia_semana}"} = $aula->disciplina;
                        $dados_form->{"grade_prof_{$aula->numero_ordem_aula}_{$aula->dia_semana}"} = $aula->professor;
                    }
                    $this->form->setData($dados_form);
                }
                TTransaction::close();
            } catch (Exception $e) {
                TTransaction::rollback();
            }
        } else {
            $this->form->setData((object)$param);
        }

        parent::add($this->form);
    }

    public function onEdit($param)
    {
        if (isset($param['key']))
        {
            $this->__construct($param);
        }
    }

    public static function onChangeConfiguracao($param)
    {
        TApplication::loadPage('HorarioCoordenadorForm', 'onEdit', [
            'nome_horario'  => $param['nome_horario'] ?? '',
            'curso'         => $param['curso'] ?? '',
            'periodo'       => $param['periodo'] ?? '',
            'etapa'         => $param['etapa'] ?? '',
            'ano_semestre'  => $param['ano_semestre'] ?? '',
            'qtd_aulas'     => $param['qtd_aulas'] ?? '4'
        ]);
    }

    public function onSave($param)
    {
        try {
            if (empty($param['nome_horario'])) {
                throw new Exception("O preenchimento do 'Nome do Horário' é mandatório.");
            }

            TTransaction::open('Felabs_DB');

            HorarioCoordenador::where('nome_horario', '=', $param['nome_horario'])->delete();

            $qtd = (int)($param['qtd_aulas'] ?? 3);
            $usuario_id = TSession::getValue('userid') ?? 1;
            $data_agora = date('Y-m-d H:i:s');

            for ($ordem = 1; $ordem <= $qtd; $ordem++) {
                $is_intervalo = ($param["eh_intervalo_{$ordem}"] == '1') ? 1 : 0;
                $horario_h    = $param["horario_aula_{$ordem}"] ?? '';

                for ($dia = 2; $dia <= 7; $dia++) {
                    $disc = $param["grade_disc_{$ordem}_{$dia}"] ?? '';
                    $prof = $param["grade_prof_{$ordem}_{$dia}"] ?? '';

                    if (!empty($disc) || !empty($prof) || !empty($horario_h) || $is_intervalo == 1) {
                        $model = new HorarioCoordenador;
                        $model->nome_horario                = $param['nome_horario'];
                        $model->curso                       = $param['curso'] ?? '';
                        $model->periodo                     = $param['periodo'] ?? '';
                        $model->etapa                       = $param['etapa'] ?? '';
                        $model->ano_semestre                = $param['ano_semestre'] ?? '';
                        $model->qtd_aulas                   = $qtd;
                        
                        $model->numero_ordem_aula           = $ordem;
                        $model->horario_aula                = $horario_h;
                        $model->dia_semana                  = $dia;
                        $model->disciplina                  = $disc;
                        $model->professor                   = $prof;
                        $model->eh_intervalo                = $is_intervalo;
                        
                        $model->usuario_horario_coordenador = $usuario_id;
                        $model->data_horario_coordenador    = $data_agora;
                        
                        $model->store();
                    }
                }
            }

            TTransaction::close();
            new TMessage('info', 'Horario salvo com sucesso!');
            TApplication::loadPage('HorarioCoordenadorForm', 'onEdit', ['key' => $param['nome_horario']]);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onGerarRelatorio($param)
    {
        if (empty($param['nome_horario'])) {
            new TMessage('error', 'O horário precisa estar nomeado.');
            return;
        }
        
        TApplication::loadPage('HorarioCoordenadorReport', 'onGenerate', ['key' => $param['nome_horario']]);
    }
}