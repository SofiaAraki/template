<?php
/**
 * VwAlunosnotasList Listing
 * @author  <your name here>
 */
class VwAlunosnotasList extends TPage
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

        $sessao_bimestre = TSession::getValue('sessao_bimestre');
        $Bimestre = $sessao_bimestre["Bimestre"];

        //echo $Bimestre;

        // creates the form
        $this->form = new TQuickForm('form_search_VwAlunosnotas');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('VwAlunosnotas');
        

        // create the form fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $TipoDis = new TEntry('TipoDis');
        $Resultado = new TEntry('Resultado');


        // add the fields
        
        $this->form->addQuickField('Nome', $Nome,  '100%' );        
        $this->form->addQuickField('Resultado', $Resultado,  '100%' );
        $this->form->addQuickField('Tipo da Disciplina', $TipoDis,  '100%' );
        $this->form->addQuickField('Código', $Codaluno,  '100%' );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('VwAlunosnotas_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addQuickAction('Voltar para a Lista de Disciplinas',  new TAction(array('VwProfessordisciplinassemestreList','onReload')), 'fa:list blue');
        // $this->form->addQuickAction(_t('New'),  new TAction(array('', 'onEdit')), 'bs:plus-sign green');
        


        //até aqui é a busca



        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Código', 'center');
        $CodMatriculaEtapa = new TDataGridColumn('CodMatriculaEtapa', 'CodMatriculaEtapa', 'center'); 
        $CodDisciplina = new TDataGridColumn('CodDisciplina', 'CodDisciplina', 'center');
        
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
        // $column_CodTurmaetapa = new TDataGridColumn('fi_turma_etapa->Identificacao', 'Turma', 'center');
        // $column_CodDisciplina = new TDataGridColumn('fi_disciplina->Nomeusual', 'Disciplina', 'center');
        $column_TipoDis = new TDataGridColumn('TipoDis', 'Tipo Disc.', 'center');
        $column_faltas = new TDataGridColumn('faltas_widget', 'Faltas', 'center');
        $column_nota1 = new TDataGridColumn('nota1_widget', 'Nota', 'center');
        $column_Resultado = new TDataGridColumn('Resultado', 'Result.', 'center');
        
        
        //Coluna da nota seleciona a nota na tabela notasfaltasfrente
        $column_faltas->setTransformer( function($value, $object, $row) {
            $sessao_prof = TSession::getValue('sessao_prof');
            $CodGradeDisciplinaEtapa_Frente = $sessao_prof["key"];
            TTransaction::open('dados_fei');
            //TTransaction::setLogger(new TLoggerSTD); // standard output
            //TTransaction::setLogger(new TLoggerTXT('log1.txt')); // file

            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"];


            $repository = new TRepository('FiNotasfaltasFrente');
            $notas = $repository    ->where('CodMatriculaEtapa',  '=', $object->CodMatriculaEtapa)
                                    ->where('CodDisciplina', '=', $object->CodDisciplina)
                                    ->where('Avaliacao',     '=', $Bimestre)//Valor Tipo da Avaliação deve ser dinâmico, ou seja, mudar de acordo com o bimestre.
                                    ->load();
            foreach ($notas as $nota)
            {
                $Faltas = $nota->Faltas;
                $id = $nota->ID; //Coluna id da tabela notasfaltasfrente. Este campo foi criado na tabela pois não existia (não é chave primaria na tabela, apenas foi identificado com PK na classe de modelo da tabela)
            };
            $widget = new TEntry('Faltas' . '_' . $object->CodDisciplina . '_'.$object->CodMatriculaEtapa.'_'.$object->TipoDis.'_'.$object->CodTurmaetapa.'_'.$CodGradeDisciplinaEtapa_Frente.'_'.$id);
            $widget->setValue($Faltas);
            $widget->setSize(40);
            $widget->setMask('99');
            //$widget->addValidation('widget', new TMaxValueValidator, array(10)); 
            $widget->setFormName('form_search_VwAlunosnotas');
            $action = new TAction( [$this, 'onSaveInline'] );
            $action->setParameter('column', 'Faltas');
            $widget->setExitAction( $action );
            return $widget;
        });

        //Coluna da nota seleciona a nota na tabela notasfaltasfrente
        $column_nota1->setTransformer( function($value, $object, $row) {
            $sessao_prof = TSession::getValue('sessao_prof');
            $CodGradeDisciplinaEtapa_Frente = $sessao_prof["key"];
            TTransaction::open('dados_fei');
            //TTransaction::setLogger(new TLoggerSTD); // standard output
            //TTransaction::setLogger(new TLoggerTXT('log2.txt')); // file

            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"];


            $repository = new TRepository('FiNotasfaltasFrente');
            $notas = $repository    ->where('CodMatriculaEtapa',  '=', $object->CodMatriculaEtapa)
                                    ->where('CodDisciplina', '=', $object->CodDisciplina)
                                    ->where('Avaliacao',     '=', $Bimestre)//Valor Tipo da Avaliação deve ser dinâmico, ou seja, mudar de acordo com o bimestre.
                                    ->load();
            foreach ($notas as $nota)
            {
                $Nota1 = $nota->Nota1;
                $id = $nota->ID; //Coluna id da tabela notasfaltasfrente. Este campo foi criado na tabela pois não existia (não é chave primaria na tabela, apenas foi identificado com PK na classe de modelo da tabela)
            };
            $widget = new TEntry('Nota1' . '_' . $object->CodDisciplina . '_'.$object->CodMatriculaEtapa.'_'.$object->TipoDis.'_'.$object->CodTurmaetapa.'_'.$CodGradeDisciplinaEtapa_Frente.'_'.$id);
            $widget->setValue($Nota1);
            //$widget->setMask('99.9');
            $widget->setSize(50);
            $widget->setNumericMask(1, '.','.');
            //$widget = new TSpinner('widget');
            //$widget->setRange(0, 10, 1);
            //$widget->maxlength = 3;
           //$widget->addValidation('widget', new TMaxValueValidator, array(10)); 
            $widget->setFormName('form_search_VwAlunosnotas');
            $action = new TAction( [$this, 'onSaveInline'] );
            $action->setParameter('column', 'Nota1');
            $widget->setExitAction( $action );
            return $widget;
        });

        // add the columns to the DataGrid
        
        //$this->datagrid->addColumn($CodMatriculaEtapa); 
        //$this->datagrid->addColumn($CodDisciplina);

        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_nota1);

        $Unidade = $loggedUnit = TSession::getValue('userunitid');

        //TTransaction::open('Felabs_DB'); 
                //$logged  = SystemUser::newFromLogin(TSession::getValue('login'));          
                //$Unidade = $logged-> system_unit_id;
        //TTransaction::close();
        
        //echo "Unidade:  ".$Unidade."<br>";
        //echo "Bimestre: ".$Bimestre."<br>";

        if ($Unidade <> 2){
            if ($Bimestre <> 3){
                $this->datagrid->addColumn($column_faltas);
            }
        }
        $this->datagrid->addColumn($column_Resultado);
        //$this->datagrid->addColumn($column_CodTurmaetapa);
        //$this->datagrid->addColumn($column_CodDisciplina);
        $this->datagrid->addColumn($column_TipoDis);
        $this->datagrid->addColumn($column_Codaluno); 
        
       
        
        

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        //$this->pageNavigation = new TPageNavigation;
        //$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        //$this->pageNavigation->setWidth($this->datagrid->getWidth());

        $this->datagrid->disableDefaultClick();
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ApontamentoBimestral'));
        $container->add(TPanelGroup::pack('Buscar Aluno Por:', $this->form));
        
        $sessao_prof = TSession::getValue('sessao_prof');
        $nomediscipina = $sessao_prof["NomeDisciplina"];
        //var_dump($sessao_prof);
        //  $container->add($this->datagrid);
        $container->add(TPanelGroup::pack($nomediscipina, $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    

/**
     * Save the datagrid objects
     */
    public static function onSaveInline($param)
    {
        $name               = $param['_field_name'];
        $value              = $param['_field_value'];
        $column             = $param['column'];
        $parts              = explode('_', $name);
        $id                 = end($parts);
        $CodDisciplina      = $parts[1];
        $CodMatriculaEtapa  = $parts[2];
        $TipoDis            = $parts[3];
        $CodTurmaetapa      = $parts[4];
        $CodGradeDisciplinaEtapa_Frente = $parts[5];

        try
        {
            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"];

            TTransaction::open('dados_fei'); 
            //TTransaction::setLogger(new TLoggerSTD); // standard output
            //TTransaction::setLogger(new TLoggerTXT('log4.txt')); // file
            

            $object = FiNotasfaltasFrente::find($id);

           

            if ($object) //verifica se já existe registro na tabela notasfaltasfrente
            { 
                //echo "1<br>";
                $object->$column = $value;                
                $object->store(); //update na tabela notasfaltasfrente                
                TTransaction::close();
                
            }else{
                //echo "2<br>";
                $conn = TTransaction::get();
                $result = $conn->query('SELECT * FROM FI_NotasFaltas_Frente WHERE (CodMatriculaEtapa = '.$CodMatriculaEtapa.' AND CodDisciplina = '.$CodDisciplina.' AND Avaliacao = '.$Bimestre.')');
                //var_dump($result);
                //die();
                foreach ($result as $row) 
                { 
                    $ID = $row['ID']; 
                } 
                if ($ID <> NULL){ //necessário para verificar se um dos campos (nota ou faltas) ja foi preenchido em notasfaltasfrente, se já existe faz update
                    //echo "3<br>";
                    $object = FiNotasfaltasFrente::find($ID);                    
                    $object->$column = $value;                
                    $object->store(); //update na tabela notasfaltasfrente                
                    TTransaction::close();
                    
                }else{
                    //echo "4<br>";
                    // monta objeto para gravar em notas faltas
                    $notasfalta = new FiNotasfaltas;                    
                    $notasfalta->CodDisciplina = $CodDisciplina;
                    $notasfalta->TipoDisciplina =  $TipoDis;
                    $notasfalta->TipoNota = 'N';
                    $notasfalta->CodMatriculaEtapa = $CodMatriculaEtapa;
                    $notasfalta->Avaliacao = $Bimestre;
                    $notasfalta->$column = $value;

                    // monta objeto para gravar em notas faltasfrente
                    $notasfaltafrente = new FiNotasfaltasFrente;                    
                    $notasfaltafrente->CodDisciplina = $CodDisciplina;
                    $notasfaltafrente->TipoDisciplina =  $TipoDis;
                    $notasfaltafrente->TipoNota = 'N';
                    $notasfaltafrente->CodMatriculaEtapa = $CodMatriculaEtapa;
                    $notasfaltafrente->Avaliacao = $Bimestre;
                    $notasfaltafrente->$column = $value;
                    $notasfaltafrente->CodGradeDisciplinaEtapa_Frente = $CodGradeDisciplinaEtapa_Frente; // existe somente em notasfaltasfrente

                    $conn = TTransaction::get();
                    $result = $conn->query('SELECT * FROM FI_NotasFaltas WHERE (CodMatriculaEtapa = '.$CodMatriculaEtapa.' AND CodDisciplina = '.$CodDisciplina.' AND Avaliacao = '.$Bimestre.')');
                    $contador = $result->rowCount();
                    //echo "contador: ".$contador;
                    //var_dump($result);

                    if ($contador == 0){
                        $notasfalta->store();       //insert na tabela notasfaltas
                    }
                    $notasfaltafrente->store(); //insert na tabela notasfaltasfrente
                    TTransaction::close();//

                }
            } 
        }
        catch (Exception $e)
        {
            // show the exception message
            new TMessage('error', $e->getMessage());
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
        TSession::setValue('VwAlunosnotasList_filter_Codaluno',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_Nome',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_CodTurmaetapa',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_CodDisciplina',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_TipoDis',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_Resultado',   NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', 'like', "%{$data->Codaluno}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_Codaluno',   $filter); // stores the filter in the session
        }


        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_Nome',   $filter); // stores the filter in the session
        }

        if (isset($data->Nome) AND ($data->TipoDis)) {
            $filter = new TFilter('TipoDis', 'like', "%{$data->TipoDis}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_TipoDis',   $filter); // stores the filter in the session
        }


        if (isset($data->Resultado) AND ($data->Resultado)) {
            $filter = new TFilter('Resultado', 'like', "%{$data->Resultado}%"); // create the filter
            TSession::setValue('VwAlunosnotasList_filter_Resultado',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('VwAlunosnotas_filter_data', $data);
        
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
            
            // open a transaction with database 'dados_fei'
            TTransaction::open('dados_fei');
            //TTransaction::setLogger(new TLoggerSTD); // standard output
            //TTransaction::setLogger(new TLoggerTXT('log3.txt')); // file

            
            $sessao_prof = TSession::getValue('sessao_prof');

           // var_dump($sessao_prof);
            
            $nomediscipina = $sessao_prof["NomeDisciplina"];
            $coddiscipina = $sessao_prof["CodDisciplina"];
            $codprofessor = $sessao_prof["CodProfessor"];
            $codturmaetapa = $sessao_prof["CodTurmaetapa"];
            $CodGradeDisciplinaEtapa_Frente = $sessao_prof["key"];

            //echo $CodGradeDisciplinaEtapa_Frente;
            //die();
            
            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"];
          
            // creates a repository for VwAlunosnotas

            $repository = new TRepository('VwAlunosnotas');
            $limit = 100;
            // creates a criteria
            
            $criteria = new TCriteria();

            $criteria->add(new TFilter('CodDisciplina', '=', $coddiscipina));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $codturmaetapa));

            if ($Bimestre == 3){
                $criteria->add(new TFilter('Resultado', '=','E'));
            }
            //echo $criteria->dump();
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'Ordem, Nome';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('VwAlunosnotasList_filter_Codaluno')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Codaluno')); // add the session filter
            }

            if (TSession::getValue('VwAlunosnotasList_filter_Nome')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Nome')); // add the session filter
            }

            if (TSession::getValue('VwAlunosnotasList_filter_TipoDis')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_TipoDis')); // add the session filter
            }

            if (TSession::getValue('VwAlunosnotasList_filter_Resultado')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Resultado')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
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
            
           // $this->pageNavigation->setCount($count); // count of records
           // $this->pageNavigation->setProperties($param); // order, page
           // $this->pageNavigation->setLimit($limit); // limit
            
            //$this->form->clear(); 

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
