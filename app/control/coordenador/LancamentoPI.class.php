<?php
/**
 * VwAlunosnotasList Listing
 * @author  <your name here>
 */
class LancamentoPI extends TPage
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
        $this->form = new TQuickForm('form_search_LancamentoPI');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('LancamentoPI');
        

        // create the form fields
        //$NomeAluno = new TEntry('NomeAluno');

        // add the fields
        //$this->form->addQuickField('Nome do Aluno', $NomeAluno,  '100%' );        
          
        // keep the form filled during navigation with session data
        //$this->form->setData( TSession::getValue('LancamentoPI_filter_data') );
        
        // add the search form actions
        //$btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$btn->class = 'btn btn-sm btn-primary';

        $this->form->addQuickAction('Listar Turmas',  new TAction(array('CoordenadorPIList','onReload')), 'fa:list orange');

        //até aqui é a busca

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
 
        // creates the datagrid columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Código', 'left');
        $column_Nome = new TDataGridColumn('NomeAluno', 'Nome', 'left'); 
        $column_IdentificacaoMatricula = new TDataGridColumn('IdentificacaoMatricula', 'Turma', 'left');
        $column_CodTurmaetapa = new TDataGridColumn('CodTurmaetapa', 'Turma', 'left');
        $column_TotalAcertosPI = new TDataGridColumn('totalacertosPI_widget', 'Acertos PI', 'left');
        $column_NotaNI = new TDataGridColumn('NotaNI_widget', 'Nota NI', 'left');        
        $column_Situacao = new TDataGridColumn('SituacaoMatricula', 'Matric.', 'center');
        
        
        //Coluna da nota seleciona a nota na tabela notasfaltasfrente
        $column_TotalAcertosPI->setTransformer( function($value, $object, $row) {
   
            $repository = new TRepository('FiMatriculaEtapa');
            $notas = $repository    ->where('CodMatriculaEtapa',  '=', $object->CodMatriculaEtapa)
                                    ->where('CodTurmaetapa', '=', $object->CodTurmaetapa)
                                    ->load();
            foreach ($notas as $nota)
            {
                $TotalAcertosPI = $nota->TotalAcertosPI;
                $id = $nota->CodMatriculaEtapa; //Coluna id da tabela notasfaltasfrente. Este campo foi criado na tabela pois não existia (não é chave primaria na tabela, apenas foi identificado com PK na classe de modelo da tabela)
            };
            $widget = new TEntry('TotalAcertosPI' . '_' . $object->CodMatriculaEtapa.'_'.$object->CodTurmaetapa.'_'.$id);
            $widget->setValue($TotalAcertosPI);
            $widget->setSize(50);
            $widget->setFormName('form_search_LancamentoPI');
            $action = new TAction( [$this, 'onSaveInline'] );
            $action->setParameter('column', 'TotalAcertosPI');
            $widget->setExitAction( $action );
            return $widget;
        });
        
        $column_NotaNI->setTransformer( function($value, $object, $row) {
   
            $repository = new TRepository('FiMatriculaEtapa');
            $notas = $repository    ->where('CodMatriculaEtapa',  '=', $object->CodMatriculaEtapa)
                                    ->where('CodTurmaetapa', '=', $object->CodTurmaetapa)
                                    ->load();
            foreach ($notas as $nota)
            {
                $NotaNI = $nota->NotaNI;
                $id = $nota->CodMatriculaEtapa; //Coluna id da tabela notasfaltasfrente. Este campo foi criado na tabela pois não existia (não é chave primaria na tabela, apenas foi identificado com PK na classe de modelo da tabela)
            };
            $widget = new TEntry('NotaNI' . '_' . $object->CodMatriculaEtapa.'_'.$object->CodTurmaetapa.'_'.$id);
            $widget->setValue($NotaNI);
            $widget->setSize(50);
            $widget->setNumericMask(1, '.','.');
            $widget->setFormName('form_search_LancamentoPI');
            $action = new TAction( [$this, 'onSaveInline'] );
            $action->setParameter('column', 'NotaNI');
            $widget->setExitAction( $action );
            return $widget;
        });
    
        $this->datagrid->addColumn($column_Codaluno); 
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_TotalAcertosPI);
        $this->datagrid->addColumn($column_NotaNI);        
        //$this->datagrid->addColumn($column_CodTurmaetapa);
        $this->datagrid->addColumn($column_IdentificacaoMatricula);
        $this->datagrid->addColumn($column_Situacao);
        
               
    
        // create the datagrid model
        $this->datagrid->createModel();
        
        $this->datagrid->disableDefaultClick();
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CoordenadorPIList'));
        $container->add(TPanelGroup::pack('Lançamento de PI e Núcleo Integrador', $this->form));
        
        $sessao_coordenador = TSession::getValue('sessao_coordenador');
        $Identificacao = $sessao_coordenador["Identificacao"];
        $container->add(TPanelGroup::pack($Identificacao, $this->datagrid, $this->pageNavigation));
        
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
        $CodMatriculaEtapa  = $parts[1];
        $CodTurmaetapa      = $parts[2];
                
        try
        {
            // open transaction
            TTransaction::open('dados_fei');
            
            $object = FiMatriculaEtapa::find($id);
            if ($object)
            {
                $object->$column = $value;
                $object->store();
            }
            
            // close transaction
            TTransaction::close();
        }
        catch (Exception $e)
        {
            // show the exception message
            new TMessage('error', $e->getMessage());
        }
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

            
            $sessao_coordenador = TSession::getValue('sessao_coordenador');

            //var_dump($sessao_coordenador);
            //die();
            
            //$nomediscipina = $sessao_prof["NomeDisciplina"];
            $CodCoordenador = $sessao_coordenador["CodCoordenador"];
            $codprofessor = $sessao_coordenador["Codprofessor"];
            $CodCurso = $sessao_coordenador["CodCurso"];
            $CodTurmaetapa = $sessao_coordenador["key"];

            //echo $codprofessor;
            //die();
            
           // $sessao_bimestre = TSession::getValue('sessao_bimestre');
           // $Bimestre = $sessao_bimestre["Bimestre"];
          
            // creates a repository for VwAlunosnotas

            $repository = new TRepository('VwAlunoMatriculaEtapa');
            $limit = 500;
            // creates a criteria
            $criteria = new TCriteria();
          
            $criteria->add(new TFilter('CodTurmaetapa', '=', $CodTurmaetapa));
          

            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'NomeAluno';
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



    /* public function onPapeleta($param)
    {
        // get the parameter and shows the message
       $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($this->datagrid->getItems());
        //die();
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->CodGradeDisciplinaEtapaFrente)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

                //echo $object->CodGradeDisciplinaEtapaFrente;
                //die();

                TSession::setValue('sessao_papeleta', array('NomeDisciplina' => $object->NomeDisciplina,
                                                            'Codprofessor'   => $object->Codprofessor,
                                                            'key'            => $object->CodGradeDisciplinaEtapaFrente,
                                                            'CodTurmaetapa'  => $object->CodTurmaetapa,
                                                            'Etapa'          => $object->Etapa,
                                                            'Identificacao'  => $object->Identificacao,
                                                            'NomeEntidade'   => $object->NomeEntidade,
                                                            'NomeProfessor'  => $object->NomeProfessor,
                                                            'Periodo'        => $object->Periodo,
                                                            'CodDisciplina'  => $object->CodDisciplina
                                                        )
                                   );
        
            }
        }
        

        

       //var_dump(TSession::getValue('sessao_papeleta'));
       //die();

        TApplication::loadPage('VwPapeletaReport');
    }*/
}

