<?php
/**
 * FiHistoricoForm Form
 * @author  <your name here>
 */
class CompletaHistorico extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
                
        // creates the form
        $this->form = new BootstrapFormBuilder('form_FiHistorico');
        $this->form->setFormTitle('Completar Dados - Histórico');

        // create the form fields
        $codhistorico = new TEntry('codhistorico');
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');
        $CodCurso = new TEntry('CodCurso');
        $ObservacaoCadastral1 = new TEntry('ObservacaoCadastral1');
        $ObservacaoCadastral2 = new TEntry('ObservacaoCadastral2');
        $ObservacaoCadastral3 = new TEntry('ObservacaoCadastral3');
        $ObservacaoCadastral4 = new TEntry('ObservacaoCadastral4');
        $ObservacaoCadastral5 = new TEntry('ObservacaoCadastral5');
        $ObservacaoFinais1 = new TEntry('ObservacaoFinais1');
        $ObservacaoFinais2 = new TEntry('ObservacaoFinais2');
        $ObservacaoFinais3 = new TEntry('ObservacaoFinais3');
        $ObservacaoFinais4 = new TEntry('ObservacaoFinais4');
        $ObservacaoFinais5 = new TEntry('ObservacaoFinais5');
        $DataConclusaoCurso = new TEntry('DataConclusaoCurso');
        $DataColacaoGrau = new TEntry('DataColacaoGrau');
        $DataExpedicaoDiploma = new TEntry('DataExpedicaoDiploma');
        $DataVestibExt = new TEntry('DataVestibExt');
        $DataConclEMExt = new TEntry('DataConclEMExt');
        $DataExpHistorico = new TEntry('dataexphistorico');
        $SituacaoEnade = new TEntry('SituacaoEnade');

        

        // $DataConclusaoCurso->setDatabaseMask('yyyy-mm-dd hh:ii:ss');

        // add the fields
        $this->form->addFields( [ new TLabel('Cod. Histórico:') ], [ $codhistorico ] ,  [ new TLabel('Cod. Aluno:') ], [ $Codaluno ],[ new TLabel('Cod Curso:') ], [ $CodCurso ] );
        $this->form->addFields( [ new TLabel('Nome:') ], [ $Nome ] );
        $this->form->addFields( [ new TLabel('Obs. Cadastral 1:') ], [ $ObservacaoCadastral1 ] );
        $this->form->addFields( [ new TLabel('Obs. Cadastral 2:') ], [ $ObservacaoCadastral2 ] );
        $this->form->addFields( [ new TLabel('Obs. Cadastral 3:') ], [ $ObservacaoCadastral3 ] );
        $this->form->addFields( [ new TLabel('Obs. Cadastral 4:') ], [ $ObservacaoCadastral4 ] );
        $this->form->addFields( [ new TLabel('Obs. Cadastral 5:') ], [ $ObservacaoCadastral5 ] );
        $this->form->addFields( [ new TLabel('Obs. Finais 1:') ], [ $ObservacaoFinais1 ] );
        $this->form->addFields( [ new TLabel('Obs. Finais 2:') ], [ $ObservacaoFinais2 ] );
        $this->form->addFields( [ new TLabel('Obs. Finais 3:') ], [ $ObservacaoFinais3 ] );
        $this->form->addFields( [ new TLabel('Obs. Finais 4:') ], [ $ObservacaoFinais4 ] );
        $this->form->addFields( [ new TLabel('Obs. Finais 5:') ], [ $ObservacaoFinais5 ] );
        $this->form->addFields( [ new TLabel('Data Concl. Curso:') ], [ $DataConclusaoCurso ] ,[ new TLabel('Data Colação de Grau:') ], [ $DataColacaoGrau ]);
        $this->form->addFields( [ new TLabel('Data Exp. Diploma:') ], [ $DataExpedicaoDiploma ], [ new TLabel('Data Exp. Histórico:') ], [ $DataExpHistorico ] );
        //$this->form->addFields( [ new TLabel('Data Expedição Diploma:') ], [ $DataExpedicaoDiploma ] );
        $this->form->addFields( [ new TLabel('Data de Ingresso (Ano/Sem):') ], [ $DataVestibExt ] ,[new TLabel('Data Conclusão Ensino Médio:') ], [ $DataConclEMExt ]);
        //$this->form->addFields( [  );
        //$this->form->addFields( );
        $this->form->addFields( [ new TLabel('Situação ENADE:') ], [ $SituacaoEnade ] );

        TTransaction::open('dados_fei');
        
        $object = new FiHistorico($param['key']);
        $aluno = $object->Codaluno;
        $object_aluno = new FiAluno($aluno);
        $nomealuno = $object_aluno->Nome;
        TTransaction::close();

        $DataExpHistorico->setValue(date("d/m/Y")); 
        $Nome->setValue($nomealuno);
        // set sizes
        $codhistorico->setSize('100%');
        $Codaluno->setSize('100%');
        $CodCurso->setSize('100%');
        $ObservacaoCadastral1->setSize('100%');
        $ObservacaoCadastral2->setSize('100%');
        $ObservacaoCadastral3->setSize('100%');
        $ObservacaoCadastral4->setSize('100%');
        $ObservacaoCadastral5->setSize('100%');
        $ObservacaoFinais1->setSize('100%');
        $ObservacaoFinais2->setSize('100%');
        $ObservacaoFinais3->setSize('100%');
        $ObservacaoFinais4->setSize('100%');
        $ObservacaoFinais5->setSize('100%');
        $DataConclusaoCurso->setSize('100%');
        $DataColacaoGrau->setSize('100%');
        $DataExpedicaoDiploma->setSize('100%');
        $DataVestibExt->setSize('100%');
        $DataConclEMExt->setSize('100%');
        $DataExpHistorico->setSize('100%');
        $SituacaoEnade->setSize('100%');

        
        

        //$DataConclusaoCurso->setMask('dd/mm/yyyy');
        $Codaluno->setEditable(FALSE);
        $Nome->setEditable(FALSE);
        $codhistorico->setEditable(FALSE);
        $Codaluno->setEditable(FALSE);
        $CodCurso->setEditable(FALSE);
        /*$ObservacaoCadastral1->setEditable(FALSE);
        $ObservacaoCadastral2->setEditable(FALSE);
        $ObservacaoCadastral3->setEditable(FALSE);
        $ObservacaoCadastral4->setEditable(FALSE);
        $ObservacaoCadastral5->setEditable(FALSE);
        $ObservacaoFinais1->setEditable(FALSE);
        $ObservacaoFinais2->setEditable(FALSE);
        $ObservacaoFinais3->setEditable(FALSE);
        $ObservacaoFinais4->setEditable(FALSE);
        $ObservacaoFinais5->setEditable(FALSE);*/
        $DataConclusaoCurso->setEditable(FALSE);
        $DataColacaoGrau->setEditable(FALSE);
        //$DataExpedicaoDiploma->setEditable(FALSE);
        $DataVestibExt->setSize('100%');
        $DataConclEMExt->setSize('100%');
        $DataExpHistorico->setSize('100%');
        $SituacaoEnade->setSize('100%');

        


        if (!empty($codhistorico))
        {
            $codhistorico->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction('Voltar'),  new TAction(['DadoshistoricoalunoList', 'onReload']), 'fa:arrow_left red');
        $this->form->addAction('Voltar', new TAction(['DadoshistoricoalunoList', 'onReload']), 'fa:arrow-left red');
        //$this->form->addAction('Verificar', new TAction(([$this, 'onSelect']), 'fa:arrow-right green'));
        
        // vertical box container   
        $container = new TVBox;
        $container->style = 'width: 100%';
         $container->add(new TXMLBreadCrumb('menu.xml', 'DadoshistoricoalunoList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onSelect($param)
    {
        // get the parameter and shows the message
       $key = $param['key'];
       
        //die();
        // get the course description
        //var_dump($key);
        //die();
        foreach ($this->datagrid->getItems() as $object)
        {
            if ($key == $object->codhistorico)
            {
               // $CodDisciplina = $object->CodDisciplina;
               // $etapa = $object->Etapa;
               // $NomeDisciplina = $object->NomeDisciplina;

               //var_dump($object);
               //die();

                TSession::setValue('sessao_historico', array('Codaluno' => $object->Codaluno,
                                                             'CodCurso' => $object->CodCurso,
                                                             'key'      => $object->codhistorico,
                                                             'Edita'    => $object->Edita,
                                                             'Nome'     => $object->Nome
                                                        )
                                   );

               //var_dump(TSession::getValue('sessao_historico'));
               //die();
        
            }
        }
        TApplication::loadPage('CompletaDisciplinaHistorico');
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('dados_fei'); // open a transaction
            //$conn = TTransaction::get(); // get PDO connection 
           
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
           
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

                        
            $object = new FiHistorico;  // create an empty object
    
            $object->fromArray( (array) $data); // load the object with data

            if($object->DataConclusaoCurso)
            {
                $object->DataConclusaoCurso = DateTime::createFromFormat('d/m/Y', $object->DataConclusaoCurso)->format( 'Y-m-d H:i:s' );
            }
            
            if($object->DataExpedicaoDiploma)
            {
                $object->DataExpedicaoDiploma = DateTime::createFromFormat('d/m/Y', $object->DataExpedicaoDiploma)->format( 'Y-m-d H:i:s' );
            }
            
            if($object->DataColacaoGrau)
            {
                $object->DataColacaoGrau = DateTime::createFromFormat('d/m/Y', $object->DataColacaoGrau)->format( 'Y-m-d H:i:s' );
            }

            $object->store(); // save the object

            // get the generated codhistorico
            $data->codhistorico = $object->codhistorico;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('dados_fei'); // open a transaction
                $object = new FiHistorico($key); // instantiates the Active Record
                //$object->DataConclusaoCurso = DateTime::createFromFormat('Y-m-d', $object->DataConclusaoCurso)->format( 'd/m/Y' );
                //$object->DataExpedicaoDiploma = DateTime::createFromFormat('Y-m-d', $object->DataExpedicaoDiploma)->format( 'd/m/Y' );
                //$object->DataColacaoGrau = DateTime::createFromFormat('Y-m-d', $object->DataColacaoGrau)->format( 'd/m/Y' );

                $object->DataConclusaoCurso = TDate::date2br($object->DataConclusaoCurso);
                $object->DataExpedicaoDiploma = TDate::date2br($object->DataExpedicaoDiploma);
                $object->DataColacaoGrau = TDate::date2br($object->DataColacaoGrau);
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
}
