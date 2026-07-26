<?php
/**
 * @author Murilo Scapim
 */
class DiarioClasseConteudoList extends TPage
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

    private function normalizeDate($date)
    {
        if (empty($date))
        {
            return null;
        }

        if ($date instanceof DateTime)
        {
            return $date->format('Y-m-d');
        }

        $dt = DateTime::createFromFormat('d/m/Y', $date);

        if ($dt)
        {
            return $dt->format('Y-m-d');
        }

        $ts = strtotime($date);

        if ($ts)
        {
            return date('Y-m-d', $ts);
        }

        return null;
    }

    private function buildDatagrid()
    {
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%';
        $this->datagrid->disableDefaultClick();

        $col_data = new TDataGridColumn('data_formatada', 'Data', 'center', '15%');
        $col_conteudo = new TDataGridColumn('conteudo_exibicao', 'Conteúdo', 'left', '60%');
        $col_frequencia = new TDataGridColumn('botao', 'Frequências', 'center', '25%');

        $actionEdit = new TDataGridAction([$this, 'onEdit']);
    
        $actionEdit->setField('row_key'); // key

        $col_conteudo->setEditAction($actionEdit);

        $col_frequencia->setTransformer(
            function ($value, $object, $row)
            {
                $btn = new TElement('button');
                $btn->type = 'button';

                $tooltip = '';

                if ($object->FrequenciaLancada == 'SIM') {
                    $tooltip = 'Frequência registrada';
                    $btn->class = 'btn btn-success btn-sm';
                } else {
                    $tooltip = 'Frequência não registrada';
                    $btn->class = 'btn btn-danger btn-sm';
                }
                $btn->add('Frequências');

                if (!empty($object->conteudo)) {
                    $action = new TAction([$this, 'onFrequencias']);
                    $action->setParameter('Data', $object->data_formatada);

                    $url = $action->serialize();

                    $btn->onclick =
                        "__adianti_load_page('{$url}'); return false";
                }
                else {
                    $tooltip = "Conteúdo não registrado";
                    $btn->disabled = 'disabled';
                    $btn->class = 'btn btn-secondary btn-sm';
                }

                $span = new TElement('span');
                $span->title = $tooltip;
                $span->add($btn);

                return $span;
            }
        );

        $this->datagrid->addColumn($col_data);
        $this->datagrid->addColumn($col_conteudo);
        $this->datagrid->addColumn($col_frequencia);

        $this->datagrid->createModel();
    }

    private function buildPageNavigation()
    {
        $this->pageNavigation = new TPageNavigation;

        $this->pageNavigation->setAction(
            new TAction([$this, 'onReload'])
        );
    }

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

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('dados_fei');

            $sessao = TSession::getValue('sessao_diarioclasse');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodProfessor', '=', $sessao['Codprofessor']));
            $criteria->add(new TFilter('CodDisciplina', '=', $sessao['CodGradeDisciplinaEtapaFrente']));
            $criteria->add(new TFilter('CodCurso', '=', $sessao['CodCurso']));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $sessao['CodTurmaetapa']));
            $criteria->add(new TFilter('AnoTurma', '=', $sessao['Ano']));

            $criteria->setProperty('order', 'Data');
            $criteria->setProperty('direction', 'DESC');

            if ($param)
            {
                $criteria->setProperties($param);
            }

            $criteria->setProperty('limit', 10);

            $repository = new TRepository('Vw_DiarioClasseProfessor');

            $objects = $repository->load($criteria);

            $this->datagrid->clear();

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $data = $this->normalizeDate(
                        $object->Data
                    );

                    $object->data_formatada =
                        $data
                        ? date(
                            'd/m/Y',
                            strtotime($data)
                        )
                        : '';

                    $object->conteudo_vazio = empty($object->conteudo);

                    $object->conteudo_exibicao = !empty($object->conteudo)
                        ? $object->conteudo
                        : "Clique para registrar o conteúdo";

                    $object->row_key = $object->id . '_' . $object->data_formatada;
                    
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();

            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);

            if ($param)
            {
                $this->pageNavigation->setProperties($param);
            }

            $this->pageNavigation->setLimit(10);

            TTransaction::close();

            $this->loaded = true;
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $conteudo = trim($param['value'] ?? '');

            list($id, $data) = explode('_', $param['key']);

            $sessao = TSession::getValue('sessao_diarioclasse');
            
            if (!empty($id)) {
                $registro = new ConteudoDiarioClasse($id);
                $registro->conteudo = $conteudo;
                
                $mensagem = 'Conteúdo atualizado com sucesso.';             
            }
            else {
                $registro = new ConteudoDiarioClasse;
                $registro->cod_disciplina = $sessao['CodGradeDisciplinaEtapaFrente'];
                $registro->cod_turma_etapa = $sessao['CodTurmaetapa'];
                $registro->cod_professor = $sessao['Codprofessor'];
                $registro->data_aula = $data;
                $registro->conteudo = $conteudo;
                $registro->nome_disciplina = $sessao['NomeDisciplina'];
                $registro->nome_professor = $sessao['NomeProfessor'];
                $registro->cod_curso = $sessao['CodCurso'];
                // $registro->cod_ies = $sessao['CodIES'];

                $mensagem = 'Conteúdo cadastrado com sucesso.';
            }
            
            $registro->store();
            TTransaction::close();
            
            AdiantiCoreApplication::loadPage(__CLASS__, 'onReload', $param);
            
            new TMessage('info', $mensagem);
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onFrequencias($param)
    {
        $sessao = TSession::getValue('sessao_diarioclasse');
     
        TSession::setValue('data_escolhida', $param['Data']);

        AdiantiCoreApplication::gotoPage(
            'ControleFrequencia',
            'onReload',
            [
                'data' => $param['Data'],

                'CodTurmaetapa' =>
                    $sessao['CodTurmaetapa'] ?? null,

                'NomeDisciplina' =>
                    $sessao['NomeDisciplina'] ?? null
            ]
        );
    }

    public function show()
    {
        if (!$this->loaded)
        {
            $this->onReload();
        }
        
        parent::show();
    }
}