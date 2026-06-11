<?php
/**
 * ContratoFinanceiroFormList Form List
 * @author  Pamella Scapim
 */
class ContratoFinanceiroFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_ContratoFinanceiro');
        $this->form->setFormTitle('Contrato Financeiro');

        $loggedUnit = TSession::getValue('userunitid');

        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit));

        // create the form fields
        $id = new THidden('id');
        $curso_id = new TDBCombo('curso_id', 'Dados_Fei', 'FiCurso', 'CodCurso', 'Nome', 'Nome', $criteria);
        $valor_total = new TNumeric('valor_total', '2', ',', '.' );
        $valor_total_extenso = new TEntry('valor_total_extenso');
        $valor_primeira_parcela = new TNumeric('valor_primeira_parcela', '2', ',', '.' );
        $varlor_prim_parcela_extenso = new TEntry('varlor_prim_parcela_extenso');
        $valor_demais_parcelas = new TNumeric('valor_demais_parcelas', '2', ',', '.' );
        $valor_dms_parcelas_extenso = new TEntry('valor_dms_parcelas_extenso');
        $ano_vigente = new TEntry('ano_vigente');
        $data_reg = new THidden('data_reg');
        $user_id = new THidden('user_id');
        $nome_curso = new TEntry('nome_curso');
        
        $turno = new TCombo('turno');
        $items = ['I'=>'Integral','M'=>'Matutino', 'N'=>'Noturno'];


        // add the fields
         //$this->form->addFields( [ new TLabel('Id'), $id ] );
         $row = $this->form->addFields( [ new TLabel('Curso'), $curso_id ],
                                        [ new TLabel('Período'), $turno ],
                                        [ new TLabel('Ano Vigente'), $ano_vigente ],[ new TLabel('') ]  );
        $row->layout = ['col-sm-4', 'col-sm-3', 'col-sm-2'];
        $row = $this->form->addFields(  [ new TLabel('Valor Anuidade ou Semestralidade'), $valor_total ],[ new TLabel('Valor por extenso'), $valor_total_extenso ]);
        $row->layout = ['col-sm-4', 'col-sm-5'];
        $row = $this->form->addFields(  [ new TLabel('Valor 1ª Parcela'), $valor_primeira_parcela ],[ new TLabel('Valor por extenso'), $varlor_prim_parcela_extenso ]);
        $row->layout = ['col-sm-4', 'col-sm-5'];
        $row = $this->form->addFields( [ new TLabel('Valor Demais Parcelas'), $valor_demais_parcelas ],[ new TLabel('Valor por extenso'), $valor_dms_parcelas_extenso ]);
        $row->layout = ['col-sm-4', 'col-sm-5'];        
                                       
           //$this->form->addFields( [ new TLabel('Data Reg'), $data_reg ] );



        // set sizes
        $id->setSize('100%');
        $curso_id->setSize('100%');
        $turno->setSize('100%');
        $turno->addItems($items);
        //$nome_curso->setSize('100%');
        $valor_total->setSize('100%');
        $valor_total_extenso->setSize('100%');
        $valor_primeira_parcela->setSize('100%');
        $varlor_prim_parcela_extenso->setSize('100%');
        $valor_demais_parcelas->setSize('100%');
        $valor_dms_parcelas_extenso->setSize('100%');
        $ano_vigente->setSize('100%');
        //$data_reg->setSize('100%');

        $curso_id->addValidation( 'Curso', new TRequiredValidator );
        $valor_total->addValidation( 'Valor da anuidade ou semestralidade', new TRequiredValidator );
        $valor_total_extenso->addValidation( 'Valor por extenso', new TRequiredValidator );
        $valor_primeira_parcela->addValidation( 'Valor da 1ª parcela ', new TRequiredValidator );
        $varlor_prim_parcela_extenso->addValidation( 'Valor por extenso', new TRequiredValidator );
        $valor_demais_parcelas->addValidation( 'Valor das demais parcelas', new TRequiredValidator );
        $valor_dms_parcelas_extenso->addValidation( 'Valor por extenso', new TRequiredValidator );
        $ano_vigente->addValidation( 'Ano vigente', new TRequiredValidator );



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        //$column_curso_id = new TDataGridColumn('curso_id', 'Cod. Curso', 'left');
        $column_nome_curso = new TDataGridColumn('nome_curso', 'Curso', 'left');
        $column_turno = new TDataGridColumn('turno', 'Período', 'left');
        $column_valor_total = new TDataGridColumn('valor_total', 'Valor Total', 'left');
        $column_valor_total_extenso = new TDataGridColumn('valor_total_extenso', 'Valor Total Extenso', 'left');
        $column_valor_primeira_parcela = new TDataGridColumn('valor_primeira_parcela', 'Valor Primeira Parcela', 'left');
        $column_varlor_prim_parcela_extenso = new TDataGridColumn('varlor_prim_parcela_extenso', 'Varlor Prim Parcela Extenso', 'left');
        $column_valor_demais_parcelas = new TDataGridColumn('valor_demais_parcelas', 'Valor Demais Parcelas', 'left');
        $column_valor_dms_parcelas_extenso = new TDataGridColumn('valor_dms_parcelas_extenso', 'Valor Dms Parcelas Extenso', 'left');
        $column_ano_vigente = new TDataGridColumn('ano_vigente', 'Ano Vigente', 'left');
        //$column_user_id = new TDataGridColumn('system_user->name', 'Data Reg', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_curso_id);
        $this->datagrid->addColumn($column_nome_curso);
        $this->datagrid->addColumn($column_turno);
        $this->datagrid->addColumn($column_valor_total);
        $this->datagrid->addColumn($column_valor_total_extenso);
        $this->datagrid->addColumn($column_valor_primeira_parcela);
        $this->datagrid->addColumn($column_varlor_prim_parcela_extenso);
        $this->datagrid->addColumn($column_valor_demais_parcelas);
        $this->datagrid->addColumn($column_valor_dms_parcelas_extenso);
        $this->datagrid->addColumn($column_ano_vigente);
        //$this->datagrid->addColumn($column_user_id);

        
        // creates two datagrid actions
        //$action1 = new TDataGridAction([$this, 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        //$action1->setLabel(_t('Edit'));
        //$action1->setImage('far:edit blue');
        //$action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        // add the actions to the datagrid
        //$this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);

        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };
        
        $column_valor_total->setTransformer($format_value);
        $column_valor_primeira_parcela->setTransformer($format_value);
        $column_valor_demais_parcelas->setTransformer($format_value);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

    public function onReload($param = NULL)
    {
        try
        {
            //Filtra os cursos da unidade logada
            $loggedUnit = TSession::getValue('userunitid');
            
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

            
            //Filtra os contratos dos cursos da unidade logada
            
            // open a transaction with database 'Felabs_DB'
            TTransaction::open('Felabs_DB');
                        

            
            // creates a repository for ContratoFinanceiro
            $repository = new TRepository('ContratoFinanceiro');
            $limit = 10;
            
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('curso_id', 'IN', $ids_cursos));
            
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    

    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new ContratoFinanceiro($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    

    public function onSave( $param )
    {


        try
        {
            $Cod_Curso = $param['curso_id'];
            
            TTransaction::open('Dados_Fei');
            $object_curso = FiCurso::find($Cod_Curso);
                if ($object_curso) 
                { 
                $NomeCurso = $object_curso->Nome;     
                }
            TTransaction::close();

            TTransaction::open('Felabs_DB'); // open a transaction
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            $data->registro = date('Y-m-d H:i:s');

            $object = new ContratoFinanceiro;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data

            $object->user_id = TSession::getValue('userid');
            $object->nome_curso = $NomeCurso;
           
           
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); // success message
            $this->onReload(); // reload the listing
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new ContratoFinanceiro($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    

    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
