<?php
/**
 * @author     Pamella Scapim
 */
class CompletaDisciplinaHistorico extends TStandardList
{
    protected $datagrid; // listing
    protected $pageNavigation;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('dados_fei');
        parent::setActiveRecord('VwHistoricodisciplina');
        parent::setDefaultOrder('Etapa', 'asc');
        parent::setLimit(150); //(50) é quantidade de registros que mostrará no grid

        // add the filter (filter field, operator, form field)
        $sessao_historico = TSession::getValue('sessao_historico');
        $Historico = $sessao_historico["key"];
        $Aluno = $sessao_historico["Codaluno"];
        $NomeAluno = $sessao_historico["Nome"];
        
        $criteria = new TCriteria();
        $criteria->add(new TFilter('codhistorico','=',$Historico));
        $criteria->add(new TFilter('Codaluno','=',$Aluno));
        //$criteria->add(new TFilter('PrefixoDisciplina','like',"*"));
        //$criteria->add(new TFilter('Edita','=','S'));

        //echo $criteria->dump();
        parent::setCriteria($criteria); 
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_VwHistoricodisciplina');
        $this->form->setFormTitle(('Professores Pendentes'));
        
        // create the form fields
        $Codaluno = new THidden('Codaluno');
        $this->form->addFields( [new TLabel('Aluno:')], [$NomeAluno] );
        
        //$this->form->addAction((''), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addAction('Voltar', new TAction(['DadoshistoricoalunoList', 'onReload']), 'fa:arrow-left blue');
        //$this->form->clear(); 
        
        // keep the form filled with session data
        $this->form->setData( TSession::getValue('VwHistoricodisciplina_filter_data') );
        
        // creates the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        // create the datagrid columns
        //$column_codhistorico    = new TDataGridColumn('codhistorico', 'Codhistorico', 'left');
        //$column_Codaluno        = new TDataGridColumn('Codaluno', 'Codaluno', 'left');
        $column_Etapa           = new TDataGridColumn('Etapa', 'Etapa', 'center');
        $column_Ano             = new TDataGridColumn('Ano', 'Ano', 'center');
        $column_Sem             = new TDataGridColumn('Sem', 'Sem', 'center');
        $column_NomeDisciplina  = new TDataGridColumn('NomeDisciplina', 'Disciplina', 'center');
        $column_nome            = new TDataGridColumn('nome', 'Prof.', 'center');
        $column_Habilitacao     = new TDataGridColumn('HabilitacaoProf3', 'Tit.', 'center');
        //$column_NotaFinal       = new TDataGridColumn('NotaFinal', 'Nota', 'center');
        //$column_CH              = new TDataGridColumn('CH', 'C.H.', 'center');
        $column_Sit             = new TDataGridColumn('Sit', 'Sit.', 'center');
        $column_Edita           = new TDataGridColumn('Edita', 'Edita', 'center');
        $column_PrefixoDisciplina = new TDataGridColumn('PrefixoDisciplina', 'Pfx', 'center');

         ///////////////////////////////////////Insere CH da Disciplina///////////////////////////////////////////

         $column_CHParcial        = new TDataGridColumn('CHParcial_widget', 'CHParcial', 'center');
         $column_CHParcial->setTransformer( function($value, $object, $row) {
         
             $sessao_historico = TSession::getValue('sessao_historico');
             $CodHistorico = $sessao_historico["key"];
             TTransaction::open('dados_fei');
             //TTransaction::setLogger(new TLoggerSTD); // standard output
             //TTransaction::setLogger(new TLoggerTXT('log2.txt')); // file
             $repository = new TRepository('FiHistoricodisciplinas');
             $disciplinas = $repository  ->where('CodHistoricoDisciplinas',  '=', $object->CodHistoricoDisciplinas)
                                         ->load();
                 foreach ($disciplinas as $disciplina)
                 {
                     $CHParcial = $disciplina->CHParcial;
                     $id = $disciplina->CodHistoricoDisciplinas; 
                 };
             $widget = new TEntry('CHParcial' .'_'.$CodHistorico.'_'.$id);
             
             if ($CHParcial == NULL) {
                 $widget->setValue( $object->CH );
             }
             else {
                 $widget->setValue( $object->CHParcial );
             }
             
             
             $widget->setSize(50);
             $widget->setFormName('form_search_VwHistoricodisciplina');
                 
             $action = new TAction( [$this, 'onSaveInline'] );
             $action->setParameter('column', 'CHParcial');
             $widget->setExitAction( $action );
             return $widget;
         });
        
        ///////////////////////////////////////Insere Nome Professor////////////////////////////////////////////////

        $column_NomeProf        = new TDataGridColumn('NomeProf_widget', 'Prof(Manual)', 'center');
            $column_NomeProf->setTransformer( function($value, $object, $row) {
                
                $sessao_historico = TSession::getValue('sessao_historico');
                $CodHistorico = $sessao_historico["key"];
                TTransaction::open('dados_fei');
                    //TTransaction::setLogger(new TLoggerSTD); // standard output
                    //TTransaction::setLogger(new TLoggerTXT('log2.txt')); // file
                $repository = new TRepository('FiHistoricodisciplinas');
                $disciplinas = $repository  ->where('CodHistoricoDisciplinas',  '=', $object->CodHistoricoDisciplinas)
                                            ->load();
                    foreach ($disciplinas as $disciplina)
                    {
                        $NomeProf = $disciplina->NomeProf;
                        $id = $disciplina->CodHistoricoDisciplinas; 
                    };
                //$combo = new TDBCombo('NomeProf','dados_fei_t','FiProfessor','Codprofessor','Nome','Nome');
                $widget = new TEntry('NomeProf' .'_'.$CodHistorico.'_'.$id);
                $widget->setValue( $object->NomeProf );
                $widget->setSize(300);
                $widget->setFormName('form_search_VwHistoricodisciplina');
                    
                $action = new TAction( [$this, 'onSaveInline'] );
                $action->setParameter('column', 'NomeProf');
                $widget->setExitAction( $action );
                return $widget;
            });

        ///////////////////////////////////////Insere Titulação do Professor///////////////////////////////////////////

        $column_TituloProf        = new TDataGridColumn('TituloProf_widget', 'Tit.(Manual)', 'center');
            $column_TituloProf->setTransformer( function($value, $object, $row) {
            
                $sessao_historico = TSession::getValue('sessao_historico');
                $CodHistorico = $sessao_historico["key"];
                TTransaction::open('dados_fei');
                //TTransaction::setLogger(new TLoggerSTD); // standard output
                //TTransaction::setLogger(new TLoggerTXT('log2.txt')); // file
                $repository = new TRepository('FiHistoricodisciplinas');
                $disciplinas = $repository  ->where('CodHistoricoDisciplinas',  '=', $object->CodHistoricoDisciplinas)
                                            ->load();
                    foreach ($disciplinas as $disciplina)
                    {
                        $TituloProf = $disciplina->TituloProf;
                        $id = $disciplina->CodHistoricoDisciplinas; 
                    };
                $widget = new TEntry('TituloProf' .'_'.$CodHistorico.'_'.$id);
                $widget->setValue( $object->TituloProf );
                $widget->setSize(100);
                $widget->setFormName('form_search_VwHistoricodisciplina');
                    
                $action = new TAction( [$this, 'onSaveInline'] );
                $action->setParameter('column', 'TituloProf');
                $widget->setExitAction( $action );
                return $widget;
            });

           

        //$this->datagrid->addColumn($column_codhistorico);
        //$this->datagrid->addColumn($column_Codaluno);
        $this->datagrid->addColumn($column_Etapa);
        $this->datagrid->addColumn($column_Ano);
        $this->datagrid->addColumn($column_Sem);
        $this->datagrid->addColumn($column_NomeDisciplina);
        //$this->datagrid->addColumn($column_NotaFinal);

        //$this->datagrid->addColumn($column_CH);
        $this->datagrid->addColumn($column_Sit);
        $this->datagrid->addColumn($column_Edita);
        $this->datagrid->addColumn($column_PrefixoDisciplina);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_Habilitacao);
        $this->datagrid->addColumn($column_CHParcial);
        $this->datagrid->addColumn($column_NomeProf);
        $this->datagrid->addColumn($column_TituloProf);
        
        

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the pagination
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $this->datagrid->disableDefaultClick();
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Save the datagrid objects
     */
    public static function onSaveInline($param)
    {
        $name   = $param['_field_name'];
        $value  = $param['_field_value'];
        $column = $param['column'];
        $parts  = explode('_', $name);
        $id     = end($parts);
        
        try
        {
            // open transaction
            TTransaction::open('dados_fei');
            
            $object = FiHistoricodisciplinas::find($id);
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

    public static function onShow()
    {
    }
}