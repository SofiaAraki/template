<?php

/**
 * ContratoFinanceiroFormList Form List
 * @author  Pamella Scapim
 */
class ContratoFinanceiroFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct($param)
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_ContratoFinanceiro');
        $this->form->setFormTitle('Contrato Financeiro');

        $loggedUnit = TSession::getValue('userunitid');

        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit));

        // Criar campos do formulário
        $id                           = new THidden('id');
        $curso_id                     = new TDBCombo('curso_id', 'Dados_Fei', 'FiCurso', 'CodCurso', 'Nome', 'Nome', $criteria);
        $valor_total                  = new TNumeric('valor_total', '2', ',', '.' );
        $valor_total_extenso          = new TEntry('valor_total_extenso');
        $valor_primeira_parcela       = new TNumeric('valor_primeira_parcela', '2', ',', '.' );
        $varlor_prim_parcela_extenso  = new TEntry('varlor_prim_parcela_extenso');
        $valor_demais_parcelas        = new TNumeric('valor_demais_parcelas', '2', ',', '.' );
        $valor_dms_parcelas_extenso   = new TEntry('valor_dms_parcelas_extenso');
        $ano_vigente                  = new TEntry('ano_vigente');
        $data_reg                     = new THidden('data_reg');
        $user_id                      = new THidden('user_id');
        $nome_curso                   = new TEntry('nome_curso');
        
        $turno = new TCombo('turno');
        $items = ['I' => 'Integral', 'M' => 'Matutino', 'N' => 'Noturno'];
        $turno->addItems($items);

        // Adicionar campos no formulário com Grid Bootstrap
        $row = $this->form->addFields(
            [ new TLabel('Curso'), $curso_id ],
            [ new TLabel('Período'), $turno ],
            [ new TLabel('Ano Vigente'), $ano_vigente ],
        );
        $row->layout = ['col-sm-4', 'col-sm-3', 'col-sm-2'];

        $row = $this->form->addFields(
            [ new TLabel('Valor Anuidade ou Semestralidade'), $valor_total ],
            [ new TLabel('Valor por extenso'), $valor_total_extenso ]
        );
        $row->layout = ['col-sm-4', 'col-sm-5'];

        $row = $this->form->addFields(
            [ new TLabel('Valor 1ª Parcela'), $valor_primeira_parcela ],
            [ new TLabel('Valor por extenso'), $varlor_prim_parcela_extenso ]
        );
        $row->layout = ['col-sm-4', 'col-sm-5'];

        $row = $this->form->addFields(
            [ new TLabel('Valor Demais Parcelas'), $valor_demais_parcelas ],
            [ new TLabel('Valor por extenso'), $valor_dms_parcelas_extenso ]
        );
        $row->layout = ['col-sm-4', 'col-sm-5'];        

        // Definição de tamanhos padrão
        $id->setSize('100%');
        $curso_id->setSize('100%');
        $turno->setSize('100%');
        $valor_total->setSize('100%');
        $valor_total_extenso->setSize('100%');
        $valor_primeira_parcela->setSize('100%');
        $varlor_prim_parcela_extenso->setSize('100%');
        $valor_demais_parcelas->setSize('100%');
        $valor_dms_parcelas_extenso->setSize('100%');
        $ano_vigente->setSize('100%');

        // Validações obrigatórias
        $curso_id->addValidation('Curso', new TRequiredValidator);
        $valor_total->addValidation('Valor da anuidade ou semestralidade', new TRequiredValidator);
        $valor_total_extenso->addValidation('Valor por extenso', new TRequiredValidator);
        $valor_primeira_parcela->addValidation('Valor da 1ª parcela ', new TRequiredValidator);
        $varlor_prim_parcela_extenso->addValidation('Valor por extenso', new TRequiredValidator);
        $valor_demais_parcelas->addValidation('Valor das demais parcelas', new TRequiredValidator);
        $valor_dms_parcelas_extenso->addValidation('Valor por extenso', new TRequiredValidator);
        $ano_vigente->addValidation('Ano vigente', new TRequiredValidator);

        if (!empty($id)) {
            $id->setEditable(FALSE);
        }
        
        // Ações do formulário
        $this->form->addActionLink(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save green');
        
        // Criação da Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // Colunas da Datagrid
        $column_id                          = new TDataGridColumn('id', 'Id', 'left');
        $column_nome_curso                  = new TDataGridColumn('nome_curso', 'Curso', 'left');
        $column_turno                       = new TDataGridColumn('turno', 'Período', 'left');
        $column_valor_total                 = new TDataGridColumn('valor_total', 'Valor Total', 'left');
        $column_valor_total_extenso         = new TDataGridColumn('valor_total_extenso', 'Valor Total Extenso', 'left');
        $column_valor_primeira_parcela      = new TDataGridColumn('valor_primeira_parcela', 'Valor Primeira Parcela', 'left');
        $column_varlor_prim_parcela_extenso = new TDataGridColumn('varlor_prim_parcela_extenso', 'Varlor Prim Parcela Extenso', 'left');
        $column_valor_demais_parcelas       = new TDataGridColumn('valor_demais_parcelas', 'Valor Demais Parcelas', 'left');
        $column_valor_dms_parcelas_extenso  = new TDataGridColumn('valor_dms_parcelas_extenso', 'Valor Dms Parcelas Extenso', 'left');
        $column_ano_vigente                 = new TDataGridColumn('ano_vigente', 'Ano Vigente', 'left');

        // Adicionar colunas
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome_curso);
        $this->datagrid->addColumn($column_turno);
        $this->datagrid->addColumn($column_valor_total);
        $this->datagrid->addColumn($column_valor_total_extenso);
        $this->datagrid->addColumn($column_valor_primeira_parcela);
        $this->datagrid->addColumn($column_varlor_prim_parcela_extenso);
        $this->datagrid->addColumn($column_valor_demais_parcelas);
        $this->datagrid->addColumn($column_valor_dms_parcelas_extenso);
        $this->datagrid->addColumn($column_ano_vigente);

        // Ações da Datagrid
        $action2 = new TDataGridAction([$this, 'onDelete']);
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        $this->datagrid->addAction($action2);

        // Transformação e formatação monetária nas colunas
        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        };
        
        $column_valor_total->setTransformer($format_value);
        $column_valor_primeira_parcela->setTransformer($format_value);
        $column_valor_demais_parcelas->setTransformer($format_value);
        
        $this->datagrid->createModel();
        
        // Paginação
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // Container VBox Geral
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            $loggedUnit = TSession::getValue('userunitid');
            $ids_cursos = []; // Inicialização preventiva para evitar quebras em arrays vazios
            
            TTransaction::open('Dados_Fei');
            $repository_curso = new TRepository('FiCurso');
            
            $criteria_curso = new TCriteria;
            $criteria_curso->add(new TFilter('CodEntidade', '=', $loggedUnit));    
            
            $cursos = $repository_curso->load($criteria_curso);
            foreach($cursos as $curso)
            {
                $ids_cursos[$curso->CodCurso] = $curso->CodCurso;
            }
            TTransaction::close();

            // Filtrar os contratos dos cursos recuperados acima
            TTransaction::open('Felabs_DB');
            
            $repository = new TRepository('ContratoFinanceiro');
            $limit = 10;
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('curso_id', 'IN', $ids_cursos));
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public static function Delete($param)
    {
        try
        {
            $key = $param['key'];
            TTransaction::open('Felabs_DB');
            $object = new ContratoFinanceiro($key, FALSE);
            $object->delete();
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onSave($param)
    {
        try
        {
            $Cod_Curso = $param['curso_id'];
            $NomeCurso = '';
            
            TTransaction::open('Dados_Fei');
            $object_curso = FiCurso::find($Cod_Curso);
            if ($object_curso) 
            { 
                $NomeCurso = $object_curso->Nome;     
            }
            TTransaction::close();

            TTransaction::open('Felabs_DB');
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            
            $this->form->validate();
            $data = $this->form->getData();
            $data->registro = date('Y-m-d H:i:s');

            $object = new ContratoFinanceiro;
            $object->fromArray((array) $data);

            $object->user_id = TSession::getValue('userid');
            $object->nome_curso = $NomeCurso;
           
            $object->store();
            
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            $this->onReload();
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
        $this->form->clear(TRUE);
    }
    
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                TTransaction::open('Felabs_DB');
                $object = new ContratoFinanceiro($key);
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload'))
        {
            $this->onReload(func_get_arg(0));
        }
        parent::show();
    }
}