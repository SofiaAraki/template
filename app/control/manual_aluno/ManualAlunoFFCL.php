<?php
/**
 * ConfirmaLeituraForm Form
 * @author  <Pamella Scapim>
 */
class ManualAlunoFFCL extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');

        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $loggedUnit = TSession::getValue('userunitid');
       
        TTransaction::close();
        TTransaction::open('dados_fei');

        
        $criteria = new TCriteria;                        
        $criteria->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
        $criteria->add(new TFilter('AnoMatricula', '=', 2022)); 
        $criteria->add(new TFilter('SemestreMatricula', '=', 1)); 
        $criteria->add(new TFilter('EtapaMatricula', '=', 1)); 
       // $criteria->add(new TFilter('CodEntidade', '=', $loggedUnit)); 
       //echo $criteria->dump();

    
        $alunoView = new TRepository('VwAluno');
        $alunoSemestre = $alunoView->load($criteria);

        //var_dump($alunoSemestre);

        if(TSession::getValue('userunitid') == 2)
        {
        
        $object = new TElement('iframe');
        $object->src   = 'https://ffcl.com.br/phocadownload/secretaria/secretaria/manualaluno-FFCL.pdf';
        $object->type  = 'application/pdf';
        $object->style = "width: 80%; height:600px";
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ConfirmaLeitura');
        //$this->form->setFormTitle('ConfirmaLeitura');
        

        // create the form fields
        $id = new THidden('id');
        $cod_aluno = new TEntry('cod_aluno');
        $confirma_leit = new TEntry('confirma_leit');
        $data_confirma = new TEntry('data_confirma');






        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(('CONFIRMAR RECEBIMENTO'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($object);
        parent::add($container);
        TTransaction::close();
    }
}

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            
            //$this->form->validate(); // validate form data
            //$data = $this->form->getData(); // get form data as array
            
            $object = new ConfirmaLeitura;  // create an empty object
            //var_dump($logged);
            //die();
            $object->cod_aluno = $logged->systemuser_codlegado;
            $object->confirma_leit = " ";
            $object->data_confirma = date("d/m/Y");
            //$object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', ('Confirmação gravada com sucesso!'));
            AdiantiCoreApplication::loadPage('WelcomeView');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    

}
