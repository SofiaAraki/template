<?php
/**
 * QuestionarioPeriodoList Listing
 * @author  <your name here>
 */

 /*
 bug para fixar
 
 gerando erro desconhecido em tmessage(cath do onreload) relacionado ao syste_unit_id
 solução provisória:
 - Comentou coluna syste_unit_id da grid
 - Comentou o TMessage('error', $e->getMessage()); linha 463
 */
class QuestionarioPeriodoListAluno extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_QuestionarioPeriodo');

     //   $this->form->class = 'tform'; // change CSS class
    //    $this->form = new BootstrapFormWrapper($this->form);
      //  $this->form->style = 'display: table;width:100%'; // change style
    //    $this->form->setFormTitle('QuestionarioPeriodo');
        

        // create the form fields
        $id = new TEntry('id');
        $questionario_id = new TDBCombo('questionario_id','Felabs_DB','Questionario','id','titulo');
        $titulo = new TEntry('titulo');
        $ano = new TEntry('ano');
        $semestre = new TEntry('semestre');

        $label = '<center>.:: Avaliação Interna ::. 
<p>A CPA (Comissão Própria de Avaliação), atendendo a Regulamentação do Sistema Nacional de Avaliação do Ensino Superior (SINAES), solicita a você, nosso aluno, preencher com bastante atenção e critério, de forma individual e espontânea, os questionários disponibilizados abaixo.</p>
<p>Sua participação é muito importante, pois essas informações subsidiarão futuras ações na instituição.</p></center>
';


        // add the fields
    //    $this->form->addQuickField('Id', $id,  '100%' );
     //   $this->form->addQuickField('Questionário', $questionario_id,  '100%' );
     //   $this->form->addQuickField('Título', $titulo,  '100%' );
     //   $this->form->addQuickField('Ano', $ano,  '100%' );
    //   $this->form->addQuickField('Semestre', $semestre,  '100%' );
      //  $this->form->addQuickField('', $label,  '100%' );
        $this->form->addFields( [new TLabel($label)]);

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('QuestionarioPeriodo_filter_data') );
        
        // add the search form actions
     //   $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
    //   $btn->class = 'btn btn-sm btn-primary';
     //   $this->form->addQuickAction(_t('New'),  new TAction(array('QuestionarioPeriodoForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_questionario_id = new TDataGridColumn('questionario->titulo', 'Questionário', 'left');
        $column_titulo = new TDataGridColumn('titulo', 'Título', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'left');
        $column_semestre = new TDataGridColumn('semestre', 'Semestre', 'left');
        $column_inicio = new TDataGridColumn('inicio', 'Início', 'left');
        $column_termino = new TDataGridColumn('termino', 'Término', 'left');
       // $column_system_unit_id = new TDataGridColumn('system_unit->name', 'Unidade', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'Usuário', 'left');


        // add the columns to the DataGrid
    //    $this->datagrid->addColumn($column_id);
       // $this->datagrid->addColumn($column_questionario_id);
        $this->datagrid->addColumn($column_titulo);
     //   $this->datagrid->addColumn($column_ano);
      //  $this->datagrid->addColumn($column_semestre);
        $this->datagrid->addColumn($column_inicio);
        $this->datagrid->addColumn($column_termino);
      //  $this->datagrid->addColumn($column_system_unit_id);
      //  $this->datagrid->addColumn($column_system_user_id);

/*
        // create EDIT action
        $action_exibir = new TDataGridAction(array($this, 'goQuestionarioQuestionarioView'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_exibir->setLabel('Exibir Questionário');
        $action_exibir->setImage('far:list-alt blue fa-lg');
        $action_exibir->setField('id');
        $this->datagrid->addAction($action_exibir);
*/

        // create EDIT action
        $action_disc = new TDataGridAction(array($this, 'goQuestionarioView'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_disc->setLabel('Responder Questionário');
        $action_disc->setImage('far:list-alt green fa-lg');
        $action_disc->setField('id');
        $action_disc->setUseButton(TRUE);
        $action_disc->setButtonClass('btn btn-default');
        $this->datagrid->addAction($action_disc);

     /*   
        // create EDIT action
        $action_edit = new TDataGridAction(array('QuestionarioPeriodoForm', 'onEdit'));
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Editar Período');
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        */
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Comissão Própria de Avaliação - CPA', $this->form));
        $container->add(TPanelGroup::pack('Questionários Aplicados no Semestre', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }




/*
    public function goQuestionarioView($param)
    {

        TSession::setValue('periodoid',$param['key']);
        TApplication::loadPage('QuestionarioView','onEdit',$param);

    }
 */
    public function goQuestionarioView($param)
    {
        TTransaction::open('Felabs_DB');
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));

        $periodoInfo = new QuestionarioPeriodo($param['id']); //CRIAR DEPOIS VERIFICADOR SE É DISCIPLINAS OU QUESTIONÁRIO DIRETO

        if($periodoInfo->mostra_disciplina == 'S')
        {
            TSession::setValue('periodoid',$param['key']);
            TApplication::loadPage('QuestionarioDisciplinasAluno');
        }
        elseif($periodoInfo->mostra_disciplina == 'N')
        {

            $criteria = new TCriteria;
            $criteria->add( new TFilter(questionario_periodo_id, '=', $periodoInfo->id));
            $criteria->add( new TFilter(system_user_id, '=', $logged->id));

            $resps = QuestionarioResposta::getObjects($criteria);

            if(!empty($resps))
            {
                new TMessage('error','Você já respondeu este questionário!');
            }
            else
            {
                TSession::setValue('periodoid',$param['key']);
                TApplication::loadPage('QuestionarioView');
            }

            
        }        



        TTransaction::close();

    }
   
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new QuestionarioPeriodo($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('QuestionarioPeriodoList_filter_id',   NULL);
        TSession::setValue('QuestionarioPeriodoList_filter_questionario_id',   NULL);
        TSession::setValue('QuestionarioPeriodoList_filter_titulo',   NULL);
        TSession::setValue('QuestionarioPeriodoList_filter_ano',   NULL);
        TSession::setValue('QuestionarioPeriodoList_filter_semestre',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', 'like', "%{$data->id}%"); // create the filter
            TSession::setValue('QuestionarioPeriodoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->questionario_id) AND ($data->questionario_id)) {
            $filter = new TFilter('questionario_id', 'like', "%{$data->questionario_id}%"); // create the filter
            TSession::setValue('QuestionarioPeriodoList_filter_questionario_id',   $filter); // stores the filter in the session
        }


        if (isset($data->titulo) AND ($data->titulo)) {
            $filter = new TFilter('titulo', 'like', "%{$data->titulo}%"); // create the filter
            TSession::setValue('QuestionarioPeriodoList_filter_titulo',   $filter); // stores the filter in the session
        }


        if (isset($data->ano) AND ($data->ano)) {
            $filter = new TFilter('ano', 'like', "%{$data->ano}%"); // create the filter
            TSession::setValue('QuestionarioPeriodoList_filter_ano',   $filter); // stores the filter in the session
        }


        if (isset($data->semestre) AND ($data->semestre)) {
            $filter = new TFilter('semestre', 'like', "%{$data->semestre}%"); // create the filter
            TSession::setValue('QuestionarioPeriodoList_filter_semestre',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('QuestionarioPeriodo_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'Felabs_DB'
            TTransaction::open('Felabs_DB');

            $loggedUnit = TSession::getValue('userunitid');
            
            // creates a repository for QuestionarioPeriodo
            $repository = new TRepository('QuestionarioPeriodo');
            $limit = 14;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add( new TFilter('system_unit_id', '=', $loggedUnit));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

           // echo $criteria->dump();
            

            if (TSession::getValue('QuestionarioPeriodoList_filter_id')) {
                $criteria->add(TSession::getValue('QuestionarioPeriodoList_filter_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioPeriodoList_filter_questionario_id')) {
                $criteria->add(TSession::getValue('QuestionarioPeriodoList_filter_questionario_id')); // add the session filter
            }


            if (TSession::getValue('QuestionarioPeriodoList_filter_titulo')) {
                $criteria->add(TSession::getValue('QuestionarioPeriodoList_filter_titulo')); // add the session filter
            }


            if (TSession::getValue('QuestionarioPeriodoList_filter_ano')) {
                $criteria->add(TSession::getValue('QuestionarioPeriodoList_filter_ano')); // add the session filter
            }


            if (TSession::getValue('QuestionarioPeriodoList_filter_semestre')) {
                $criteria->add(TSession::getValue('QuestionarioPeriodoList_filter_semestre')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();

            $hoje = date('Y-m-d H:i:s');



            //////////////////////////VERIFICAR POR CURSO (RESTRIÇÃO CURSO)
            //para verificar se é aplicavel ou não ao curso

            TTransaction::open('Felabs_DB');
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            TTransaction::close();

            //var_dump($logged);
            
            TTransaction::open('dados_fei');
            //Trasação para verificar se tem matrícula no semestre (falta parametrizar dinamicamente)
            $criteria_matricula = new TCriteria;
            $criteria_matricula->add( new TFilter(CodAluno, '=', $logged->systemuser_codlegado));
            $criteria_matricula->add( new TFilter(AnoMatricula, '=', '2021'));
            //$criteria_matricula->add( new TFilter(CodCurso, '=', $data['curso']));
            $criteria_matricula->add( new TFilter(SemestreMatricula, '=', '2'));
            $criteria_matricula->add( new TFilter(ConfirmacaoMatricula, '=', 'S'));
            $criteria_matricula->add( new TFilter(SituacaoMatricula, '=', 'FR'));                                     

            //echo  $criteria_matricula->dump();

            $repository = new TRepository('VwAlunoMatriculaEtapa');
            $matriculas = $repository->load($criteria_matricula);
            //var_dump($objects);
            if($matriculas){

                foreach ($matriculas as $matricula){
                    
                    $codcurso = $matricula->CodCurso;

                    //echo $codcurso;
                    
                    if($objects){
                        
                        foreach ($objects as $object)
                        {
                            $publico =  $object->publico;
                        
                        
							$cursos = (explode(";",$publico));
					
							if(in_array($codcurso,$cursos)){
								 if(($hoje > $object->inicio && $hoje < $object->termino))
								 {
									$object->inicio = TDate::date2br($object->inicio);
									$object->termino = TDate::date2br($object->termino);
									// add the object inside the datagrid
									$this->datagrid->addItem($object);
								}
							//}else{
								//$restricaocurso = "S"; //questionário não aplicavel ao curso
								//echo $restricaocurso;
							}
						}
                    }
                }
            }
            ////////////////////////// FIM VERIFICAR POR CURSO 
            //TTransaction::close(); // close transaction
            
        

         /*   
        echo"<br><br>";
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    if(($hoje > $object->inicio && $hoje < $object->termino))
                    {

                 
                    $object->inicio = TDate::date2br($object->inicio);
                    $object->termino = TDate::date2br($object->termino);
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                    }
                }
            }*/
            
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
            //new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('Felabs_DB'); // open a transaction with database
            $object = new QuestionarioPeriodo($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $this->onReload( $param ); // reload the listing
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    



    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
