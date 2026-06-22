<?php

class VwCalendarioacademicoForm extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->buildDatagrid();
        $this->buildPageNavigation();
        $this->buildContainer();
    }

    /**
     * Normaliza diferentes formatos de data para o padrão Y-m-d do banco de dados
     */
    private function normalizeDate($date)
    {
        if (empty($date)) {
            return null;
        }

        if ($date instanceof DateTime) {
            return $date->format('Y-m-d');
        }

        $dt = DateTime::createFromFormat('d/m/Y', $date);
        if ($dt) {
            return $dt->format('Y-m-d');
        }

        $ts = strtotime($date);
        if ($ts) {
            return date('Y-m-d', $ts);
        }

        return null;
    }

    /**
     * Constrói e configura a estrutura do DataGrid
     */
    private function buildDatagrid()
    {
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%';
        $this->datagrid->disableDefaultClick();

        // Definição das Colunas
        $col_data      = new TDataGridColumn('data_formatada', 'Data', 'center', '15%');
        $col_conteudo  = new TDataGridColumn('conteudo_exibicao', 'Conteúdo', 'left', '60%');
        $col_frequencia = new TDataGridColumn('botao', 'Frequências', 'center', '25%');

        // Configuração da Ação de Edição Inline (Conteúdo)
        $actionEdit = new TDataGridAction([$this, 'onEdit']);
        $actionEdit->setField('id'); // Passa o ID do registro de conteúdo como a 'key'
        
        // IMPORTANTÍSSIMO: Captura a propriedade 'Data' bruta da View e passa para o onEdit
        $actionEdit->setParameter('Data', '{Data}'); 
        
        $col_conteudo->setEditAction($actionEdit);

        // Transformador da Coluna de Frequência (Geração dinâmica do Botão)
        $col_frequencia->setTransformer(function ($value, $object, $row) {
            $action = new TAction([$this, 'onFrequencias']);
            $action->setParameter('Data', $object->Data);

            $url = $action->serialize();

            $btn = new TElement('button');
            $btn->type    = 'button';
            $btn->class   = 'btn btn-success btn-sm';
            $btn->add('Frequências');
            $btn->onclick = "__adianti_load_page('{$url}'); return false;";

            return $btn;
        });

        // Adiciona colunas ao Grid e cria o modelo
        $this->datagrid->addColumn($col_data);
        $this->datagrid->addColumn($col_conteudo);
        $this->datagrid->addColumn($col_frequencia);

        $this->datagrid->createModel();
    }

    /**
     * Cria o componente de paginação
     */
    private function buildPageNavigation()
    {
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setLimit(10);
    }

    /**
     * Monta o container visual da página
     */
    private function buildContainer()
    {
        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add(
            TPanelGroup::pack(
                'Diário de Classe - Conteúdo e Frequência',
                $this->datagrid,
                $this->pageNavigation
            )
        );

        parent::add($vbox);
    }

    /**
     * Carrega a DataGrid com os dados filtrados vindos da View
     */
    public function onReload($param = null)
    {
        try 
        {
            TTransaction::open('dados_fei');

            $sessao = TSession::getValue('sessao_diarioclasse');
            $criteria = new TCriteria;

            // Aplicação dos Filtros com base na Sessão
            if (!empty($sessao['Codprofessor'])) {
                $criteria->add(new TFilter('Codprofessor', '=', $sessao['Codprofessor']));
            }
            if (!empty($sessao['NomeDisciplina'])) {
                $criteria->add(new TFilter('NomeDisciplina', '=', $sessao['NomeDisciplina']));
            }
            if (!empty($sessao['CodCurso'])) {
                $criteria->add(new TFilter('CodCurso', '=', $sessao['CodCurso']));
            }

            // Filtro de ano corrente estático
            $criteria->add(new TFilter('AnoTurma', '=', date('Y')));

            // Ordenação padrão
            $criteria->setProperty('order', 'Data');
            $criteria->setProperty('direction', 'DESC');
            $criteria->setProperty('limit', 10);

            if ($param) {
                $criteria->setProperties($param);
            }

            // Carrega os objetos a partir da View de Leitura
            $repository = new TRepository('VW_DiarioProfessorCompleto');
            $objects = $repository->load($criteria);

            $this->datagrid->clear();

            if ($objects) 
            {
                foreach ($objects as $object) 
                {
                    // Formata a data para exibição amigável em tela (dd/mm/aaaa)
                    $dataNorm = $this->normalizeDate($object->Data);
                    $object->data_formatada = $dataNorm ? date('d/m/Y', strtotime($dataNorm)) : '';
                    
                    // Texto padrão caso ainda não haja conteúdo inserido
                    $object->conteudo_exibicao = !empty($object->conteudo) 
                        ? $object->conteudo 
                        : 'Clique para registrar o conteúdo';

                    $this->datagrid->addItem($object);
                }
            }

            // Paginação
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count);

            if ($param) {
                $this->pageNavigation->setProperties($param);
            }

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) 
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Executa a gravação do Conteúdo Inline mantendo a Data imutável associada
     */
    public function onEdit($param)
    {
        try 
        {
            $conteudo = trim($param['value'] ?? '');
            $id       = $param['key'] ?? null; // ID da tabela física de conteúdos
            
            // Recupera a data imutável passada pelo setParameter da linha clicada
            $data_original = $param['Data'] ?? null;

            if (empty($data_original)) {
                throw new Exception("Não foi possível identificar a data de referência para esta linha.");
            }

            // Normaliza para o formato padrão do banco (aaaa-mm-dd)
            $data_banco = $this->normalizeDate($data_original);

            TTransaction::open('dados_fei');

            // 💡 IMPORTANTE: Substitua 'DiarioClasse' pelo nome real do seu Model Active Record da tabela física!
            if (empty($id)) 
            {
                // CASO NOVO (INSERT): Se o registro não existia na tabela física, cria um novo
                $diario = new DiarioClasse();
                $diario->data_registro = $data_banco; // Salva a data imutável vinda da View
                $diario->conteudo      = $conteudo;   // Salva o conteúdo digitado inline
                
                // Exemplo de recuperação de outros dados da sessão para popular o novo registro:
                $sessao = TSession::getValue('sessao_diarioclasse');
                $diario->codprofessor   = $sessao['Codprofessor'] ?? null;
                $diario->codturmaetapa  = $sessao['CodTurmaetapa'] ?? null;
                
                $diario->store();
            } 
            else 
            {
                // CASO EXISTENTE (UPDATE): Se já existia um ID físico, apenas atualiza o conteúdo
                $diario = new DiarioClasse($id);
                $diario->conteudo = $conteudo;
                $diario->store();
            }

            TTransaction::close();

            // Recarrega a listagem para refletir a atualização instantaneamente
            $this->onReload();
            new TMessage('info', 'Conteúdo atualizado com sucesso!');
        }
        catch (Exception $e) 
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Redireciona para o controle de Frequências guardando os estados na sessão
     */
    public function onFrequencias($param)
    {
        $sessao = TSession::getValue('sessao_diarioclasse');

        TSession::setValue('data_escolhida', $param['Data']);

        AdiantiCoreApplication::gotoPage(
            'ControleFrequencia',
            'onReload',
            [
                'data'           => $param['Data'],
                'CodTurmaetapa'  => $sessao['CodTurmaetapa'] ?? null,
                'NomeDisciplina' => $sessao['NomeDisciplina'] ?? null
            ]
        );
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }
        parent::show();
    }
}