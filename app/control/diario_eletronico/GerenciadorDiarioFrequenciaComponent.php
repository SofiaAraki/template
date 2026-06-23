<?php
/**
 * GerenciadorDiarioFrequenciaComponent
 * Responsabilidade: Controlar o input de datas da aula e o formulário de Conteúdo Programático.
 */
class GerenciadorDiarioFrequenciaComponent extends TElement
{
    protected $formConteudo;

    public function __construct($param)
    {
        parent::__construct('div');

        $this->id = 'container_gerenciador_diario_frequencia';
        
        $data_escolhida = TSession::getValue('data_escolhida');
        $dataAula       = $data_escolhida['data_escolhida'] ?? date('d/m/Y');

        // 1. Bloco de Seleção da Data de Aula
        $formDataAula = new BootstrapFormBuilder('form_data_aula');
        $campoData = new TDate('data_aula_controle');
        $campoData->setMask('dd/mm/yyyy'); 
        $campoData->setDatabaseMask('dd/mm/yyyy'); 
        $campoData->setValue($dataAula);
        $campoData->setSize('100%');
        $formDataAula->addFields([new TLabel('Data da Aula letiva:'), $campoData]);
        
        $btnMudarData = $formDataAula->addAction('Carregar', new TAction([$this, 'onMudarData']), 'fa:sync blue');
        $btnMudarData->class = 'btn btn-sm btn-default';
        $this->add($formDataAula);

        // 2. Bloco A: Conteúdo Diário (Formulário)
        $this->formConteudo = new BootstrapFormBuilder('form_conteudo_diario');
        $this->formConteudo->setFormTitle('Registro do Conteúdo Programático Ministrado');

        $idConteudo = new THidden('id');
        $conteudo = new TText('conteudo');
        $conteudo->setSize('100%', 100);
        $conteudo->addValidation('Conteúdo Diário', new TRequiredValidator);

        // GARANTIA 1: Força explicitamente que o campo de texto seja editável
        $conteudo->setEditable(TRUE);

        $this->formConteudo->addFields([$idConteudo]);
        $this->formConteudo->addFields([new TLabel('Conteúdo da Aula (Obrigatório):'), $conteudo]);

        $btnSalvarConteudo = $this->formConteudo->addAction('Salvar Conteúdo Diário', new TAction([$this, 'onSaveConteudo']), 'fa:save');
        $btnSalvarConteudo->class = 'btn btn-sm btn-primary';
        $this->add($this->formConteudo);

        // 3. Regra de Negócio: Busca se já existe conteúdo salvo para liberar ou travar a Frequência
        $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
        $aulaAtiva = $sessao_diarioclasse["NumeroOrdemAula"] ?? 1;
        
        $conteudoExistente = $this->buscarConteudoData($sessao_diarioclasse["CodTurmaetapa"], $sessao_diarioclasse["CodDisciplina"], $dataAula, $aulaAtiva);
        
        if ($conteudoExistente) {
            $this->formConteudo->setData($conteudoExistente);
            
            // GARANTIA 2: Força o formulário a re-adquirir permissão de escrita após o setData()
            $this->formConteudo->getField('conteudo')->setEditable(TRUE);
            
            // CORREÇÃO DA DUPLICAÇÃO: Antes de injetar um componente aninhado em cenários de recarregamento post,
            // certifique-se de que a requisição atual receberá uma árvore limpa do elemento.
            $gridFrequencia = new GerenciadorFrequenciaGridComponent($param);
            $this->add($gridFrequencia);
        } else {
            $this->formConteudo->clear();
            
            // GARANTIA 3: Garante o campo editável mesmo se o form estiver limpo
            $this->formConteudo->getField('conteudo')->setEditable(TRUE);
            
            $this->add(new TAlert('warning', '⚠️ <strong>Acesso Travado:</strong> Deve salvar primeiro o Conteúdo Programático para poder efetuar a chamada desta data.'));
        }
    }

    private function buscarConteudoData($turma, $disciplina, $data)
    {
        try {
            TTransaction::open('Felabs_DB');
            $registro = ConteudoDiarioClasse::where('cod_turma_etapa', '=', $turma)
                                            ->where('cod_disciplina', '=', $disciplina)
                                            ->where('data_aula', '=', $data) 
                                            ->first();
            TTransaction::close();
            return $registro;
        } catch (Exception $e) {
            TTransaction::rollback();
            return null;
        }
    }

    public static function onMudarData($param)
    {
        $data_informada = $param['data_aula_controle'];
        TSession::setValue('data_escolhida', ['data_escolhida' => $data_informada]);
        TApplication::loadPage('GerenciadorDisciplinaForm', 'onReload', $param);
    }

    public static function onSaveConteudo($param)
    {
        try {
            TTransaction::open('Felabs_DB');

            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
            
            // Tratamento da data escolhida na sessão
            $data_escolhida = TSession::getValue('data_escolhida');
            $dataAula = $data_escolhida['data_escolhida'] ?? date('d/m/Y');
            $aulaAtiva = isset($data_escolhida['aula_ativa']) ? $data_escolhida['aula_ativa'] : 1;

            if (empty($param['conteudo'])) {
                throw new Exception('O campo Conteúdo é obrigatório.');
            }

            // Localiza ou instancia o registro do Diário de Classe
            if (!empty($param['id'])) {
                $object = new ConteudoDiarioClasse($param['id']);
            } else {
                $object = new ConteudoDiarioClasse;
                $object->cod_turma_etapa = $sessao_diarioclasse["CodTurmaetapa"];
                $object->cod_disciplina  = $sessao_diarioclasse["CodDisciplina"];
                $object->data_aula       = $dataAula;
                $object->cod_professor   = $sessao_diarioclasse['Codprofessor'] ?? null;
                $object->nome_disciplina = $sessao_diarioclasse['NomeDisciplina'] ?? null;
                $object->nome_professor  = $sessao_diarioclasse['NomeProfessor'] ?? null;
                $object->cod_curso       = $sessao_diarioclasse['CodCurso'] ?? null;
            }

            $object->conteudo = $param['conteudo'];
            $object->store();
            
            // Se for um registro novo, devolvemos o ID gerado para o campo hidden do formulário
            $data = new stdClass;
            $data->id = $object->id;
            TForm::sendData('form_conteudo_diario', $data);
            
            TTransaction::close();

            new TMessage('info', 'Conteúdo diário gravado com sucesso!');
            TApplication::loadPage('GerenciadorDisciplinaForm', 'onReload', $param);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            if (TTransaction::get() !== null) {
                TTransaction::rollback();
            }
        }
    }
}