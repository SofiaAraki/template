<?php
/**
 * QuestionarioPeriodoForm Form
 * @author  <your name here>
 */
class QuestionarioPeriodoForm extends TPage
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
        $this->form = new TQuickForm('form_QuestionarioPeriodo');
        $this->form->class = 'tform'; // change CSS class
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('QuestionarioPeriodo');
        


        // create the form fields
        $id = new TEntry('id');
       // public function __construct($name, $database, $model, $key, $value, $ordercolumn = NULL, TCriteria $criteria = NULL)
        $questionario_id = new TDBCombo('questionario_id','Felabs_DB','Questionario','id','titulo');
        $titulo = new TEntry('titulo');
        $ano = new TEntry('ano');
        $semestre = new TEntry('semestre');
        $inicio = new TDateTime('inicio');
        $termino = new TDateTime('termino');
        $system_unit_id = new THidden('system_unit_id');
        $system_user_id = new THidden('system_user_id');
        $descricao = new TText('descricao');
        $mostra_disciplina = new TRadioGroup('mostra_disciplina');
        $publico = new TEntry('publico');

        $options = [];
        $options['S'] = 'Sim';
        $options['N'] = 'Não';

        $mostra_disciplina->addItems($options);

        
        $ano->setMask('9999');
        $semestre->setMask('9');

        $ano->placeholder = 'ex.: 2018';
        $semestre->placeholder = 'ex.: 2';
    


        // add the fields
        $this->form->addQuickField('Id', $id,  '50%' );
        $this->form->addQuickField('Questionário', $questionario_id,  '50%' );
        $this->form->addQuickField('Descrição', $descricao,  '50%' );
        $this->form->addQuickField('Título', $titulo,  '50%' );
        $this->form->addQuickField('Ano', $ano,  '50%' );
        $this->form->addQuickField('Semestre', $semestre,  '50%' );
        $this->form->addQuickField('Início', $inicio,  '50%' );
        $this->form->addQuickField('Término', $termino,  '50%' );
        $this->form->addQuickField('System Unit Id', $system_unit_id,  '50%' );
        $this->form->addQuickField('System User Id', $system_user_id,  '50%' );
        $this->form->addQuickField('Mostrar questionário por disciplina atual?', $mostra_disciplina,  '50%' );
        $this->form->addQuickField('Público (Cod. Cursos. Ex: 20;21;15)', $publico,  '50%' );




        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Período', $this->form));
        
        parent::add($container);
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
            $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
            $loggedUnit = TSession::getValue('userunitid');
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new QuestionarioPeriodo;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data


            
            $object->system_user_id = $logged->id;
            $object->system_unit_id = $loggedUnit;

            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'),TApplication::loadPage('QuestionarioPeriodoList'));
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
                TTransaction::open('Felabs_DB'); // open a transaction
                $object = new QuestionarioPeriodo($key); // instantiates the Active Record
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
