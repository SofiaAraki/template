<?php
/**
 * GerenciadorFrequenciaGridComponent
 * Responsabilidade: Montar dinamicamente a grade de chamada e gerenciar a persistência das presenças de forma blindada.
 */
class GerenciadorFrequenciaGridComponent extends TElement
{
    protected $datagridFrequencia;
    protected $formGridFrequencia;

    public function __construct($param)
    {
        parent::__construct('div');

        $painelFrequencia = new TPanelGroup("Controle de Frequência Diária");
        $this->formGridFrequencia = new BootstrapFormBuilder('form_grid_frequencia');
        $this->datagridFrequencia = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagridFrequencia->style = 'width: 100%';
        
        $this->formGridFrequencia->addFields([$this->datagridFrequencia]);
        $painelFrequencia->add($this->formGridFrequencia);
        $this->add($painelFrequencia);

        $btnSalvarFrequencia = $this->formGridFrequencia->addAction('Salvar Frequência da Turma', new TAction([__CLASS__, 'onSaveFrequencia']), 'fa:check-double');
        $btnSalvarFrequencia->class = 'btn btn-sm btn-success';

        $this->renderGrid($param);
    }

    public function renderGrid($param)
    {
        try {
            // Padronização arquitetural proposta: prioriza $param com fallback seguro na sessão
            $codTurmaEtapa = $param['cod_turma']      ?? TSession::getValue('sessao_diarioclasse')["CodTurmaetapa"] ?? null;
            $codDisciplina = $param['cod_disciplina'] ?? TSession::getValue('sessao_diarioclasse')["CodDisciplina"] ?? null;
            $codProfessor  = $param['cod_professor']   ?? TSession::getValue('sessao_diarioclasse')["Codprofessor"] ?? null;
            $anoLetivo     = $param['ano']            ?? TSession::getValue('sessao_diarioclasse')["Ano"] ?? date('Y');

            $data_escolhida  = TSession::getValue('data_escolhida');
            $dataAula        = $data_escolhida['data_escolhida'] ?? date('d/m/Y');

            $dateObj = DateTime::createFromFormat('d/m/Y', $dataAula);
            if (!$dateObj) {
                throw new Exception("Data de aula inválida fornecida para o componente: {$dataAula}");
            }
            $dataBanco = $dateObj->format('Y-m-d');
            $diasemana_numero = $dateObj->format('w') + 1;

            // Limpeza segura sem o uso de Reflection de propriedades privadas
            if (method_exists($this->datagridFrequencia, 'clear')) {
                $this->datagridFrequencia->clear();
            }

            $this->datagridFrequencia->addColumn(new TDataGridColumn('Codaluno', 'Cód. Aluno', 'center'));
            $this->datagridFrequencia->addColumn(new TDataGridColumn('Nome', 'Nome do Aluno', 'left'));

            TTransaction::open('dados_fei'); // Leitura centralizada

            $criteriaDia = new TCriteria;
            $criteriaDia->add(new TFilter('Codprofessor', '=', $codProfessor));
            $criteriaDia->add(new TFilter('CodDisciplina', '=', $codDisciplina));
            $criteriaDia->add(new TFilter('CodTurmaetapa', '=', $codTurmaEtapa));
            $criteriaDia->add(new TFilter('Ano', '=', $anoLetivo));
            $criteriaDia->add(new TFilter('DiaSemana', '=', $diasemana_numero));
            
            $AulasDoDia = (new TRepository('VwHorarioprofessor'))->load($criteriaDia);

            foreach ($AulasDoDia as $aula) {
                $colName = 'aula_' . $aula->NumeroOrdemAula;
                $this->datagridFrequencia->addColumn(new TDataGridColumn($colName, $aula->NumeroOrdemAula . 'ª Aula', 'center', '10%'));
            }
            
            $this->datagridFrequencia->createModel();

            $alunos = VwAlunosCompleto::where('CodTurmaetapa', '=', $codTurmaEtapa)
                                       ->where('CodDisciplina', '=', $codDisciplina)
                                       ->where('ConfirmacaoMatricula', '=', 'S')
                                       ->where('Situacao', '=', 'FR')
                                       ->orderBy('Nome', 'asc')
                                       ->load();

            $inputsGerados = [];

            if ($alunos) {
                foreach ($alunos as $aluno) {
                    foreach ($AulasDoDia as $aula) {
                        $inputName = 'freq_' . $aluno->CodMatriculaEtapa . '_' . $aula->NumeroOrdemAula;
                        $inputsGerados[] = $inputName;
                        
                        $freqExistente = FiFrqdiariaDisciplinas::where('CodMatriculaEtapa', '=', $aluno->CodMatriculaEtapa)
                                                              ->where('DataLancamento', '=', $dataBanco)
                                                              ->where('Aula', '=', $aula->NumeroOrdemAula)
                                                              ->where('CodDisciplina', '=', $codDisciplina)
                                                              ->first();
                        
                        $statusAtual = $freqExistente ? $freqExistente->Freq : 'P';

                        $checkPresenca = new TCheckButton($inputName);
                        $checkPresenca->setIndexValue('P');
                        $checkPresenca->setValue(($statusAtual == 'P') ? 'P' : '');

                        $this->formGridFrequencia->addField($checkPresenca);
                        $aluno->{'aula_' . $aula->NumeroOrdemAula} = $checkPresenca;
                    }
                    $this->datagridFrequencia->addItem($aluno);
                }
            }

            // Injeta a lista oculta de inputs mapeados para rastrear ausências no POST futuramente
            $hiddenMapeamento = new THidden('lista_inputs_frequencia');
            $hiddenMapeamento->setValue(implode(',', $inputsGerados));
            $this->formGridFrequencia->addField($hiddenMapeamento);

            TTransaction::close();
        } catch (Exception $e) {
            if (TTransaction::get() !== null) TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onSaveFrequencia($param)
    {
        try {
            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
            $data_escolhida      = TSession::getValue('data_escolhida');
            $dataAula            = $data_escolhida['data_escolhida'] ?? date('d/m/Y');
            
            $dateObj = DateTime::createFromFormat('d/m/Y', $dataAula);
            if (!$dateObj) {
                throw new Exception("Falha crítica ao parsear data no salvamento: {$dataAula}");
            }
            $dataBanco = $dateObj->format('Y-m-d');

            $codTurmaEtapa = $param['cod_turma'] ?? $sessao_diarioclasse["CodTurmaetapa"];
            $codDisciplina = $param['cod_disciplina'] ?? $sessao_diarioclasse["CodDisciplina"];
            $codGradeDisciplinaEtapa_Frente = $sessao_diarioclasse["CodGradeDisciplinaEtapaFrente"] ?? $sessao_diarioclasse["CodGradeDisciplinaEtapa_Frente"] ?? null;

            if (empty($param['lista_inputs_frequencia'])) {
                throw new Exception("Nenhum dado estrutural foi postado para gravação.");
            }

            TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            TTransaction::close();

            // Abre gravação unificada (certifique-se que aponta corretamente na sua configuração de databases)
            TTransaction::open('dados_fei_t');
            $conn = TTransaction::get();

            $frqdiaria = FiFrqdiaria::where('CodTurmaetapa', '=', $codTurmaEtapa)
                                    ->where('Data', '=', $dataBanco)
                                    ->first();

            if (!$frqdiaria) {
                $datalanc = date('Y-m-d');
                $horalanc = date('H:i:s');
                
                $sqlInsertPai = "INSERT INTO FI_FrqDiaria (CodTurmaetapa, Data, DataLancamento, HoraLancamento) 
                                 VALUES ('{$codTurmaEtapa}', '{$dataBanco}', '{$datalanc}', '{$horalanc}')";
                $conn->query($sqlInsertPai);

                $frqdiaria = FiFrqdiaria::where('CodTurmaetapa', '=', $codTurmaEtapa)
                                        ->where('Data', '=', $dataBanco)
                                        ->first();
            }

            if (!$frqdiaria) {
                throw new Exception("Falha ao gerar o cabeçalho base de frequência (FI_FrqDiaria).");
            }

            $codFrqDiaria = $frqdiaria->CodFrqDiaria;
            $horaAtual = date('H:i:s');
            $codProfLegado = $logged->systemuser_codlegado ?? null;

            // Recarrega a lista de chaves esperadas geradas na renderização
            $inputsEsperados = explode(',', $param['lista_inputs_frequencia']);

            foreach ($inputsEsperados as $name) {
                $parts = explode('_', $name);
                if (count($parts) < 3) continue;

                $codMatriculaEtapa = $parts[1];
                $numeroAula        = $parts[2];
                
                // SOLUÇÃO DO BUG DO POST: Se a chave existe e está marcada como 'P', é Presença. 
                // Se sumiu do POST ($param[$name] não definido), é Falta ('F').
                $statusFreq = (isset($param[$name]) && $param[$name] === 'P') ? 'P' : 'F';

                $registro = FiFrqdiariaDisciplinas::where('CodMatriculaEtapa', '=', $codMatriculaEtapa)
                                                  ->where('CodFrqDiaria', '=', $codFrqDiaria)
                                                  ->where('Aula', '=', $numeroAula)
                                                  ->where('CodDisciplina', '=', $codDisciplina)
                                                  ->first();
                
                if ($registro) {
                    if ($registro->Freq !== $statusFreq) {
                        $registro->Freq = $statusFreq;
                        $registro->DataLancamento = date('Y-m-d');
                        $registro->HoraLancamento = $horaAtual;
                        $registro->CodProfessor = $codProfLegado;
                        $registro->store();
                    }
                } else {
                    $registro = new FiFrqdiariaDisciplinas;
                    $registro->CodGradeDisciplinaEtapa_Frente = $codGradeDisciplinaEtapa_Frente;
                    $registro->CodMatriculaEtapa              = $codMatriculaEtapa;
                    $registro->CodFrqDiaria                   = $codFrqDiaria;
                    $registro->CodDisciplina                  = $codDisciplina;
                    $registro->Aula                           = $numeroAula;
                    $registro->Freq                           = $statusFreq;
                    $registro->DataLancamento                 = date('Y-m-d');
                    $registro->HoraLancamento                 = $horaAtual;
                    $registro->CodProfessor                   = $codProfLegado;
                    $registro->store();
                }
            }
            
            TTransaction::close();
            
            new TMessage('info', 'Frequência gravada com sucesso!');
        } catch (Exception $e) {
            if (TTransaction::get() !== null) TTransaction::rollback();
            new TMessage('error', 'Erro ao salvar: ' . $e->getMessage());
        }
    }
}