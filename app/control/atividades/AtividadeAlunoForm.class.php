<?php

class AtividadeAlunoForm extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new TQuickForm('form_AtividadeAluno');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        
        // define the form title
        $this->form->setFormTitle('Atividade Aluno');        


        // create the form fields
        $id = new TEntry('id');
        $atividade_id = new TEntry('atividade_id');
        $system_user_id = new THidden('system_user_id');
        $descricao = new TText('descricao');
        $anexo = new TFile('anexo');
        $nota = new THidden('nota');
        $feedback = new THidden('feedback');
        $data_envio = new THidden('data_envio');
        $data_ultimaedicao = new THidden('data_ultimaedicao');

        $atividade_id->setValue(TSession::getValue('atividadeid'));


        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Atividade Id', $atividade_id, '100%');
        $this->form->addQuickField('System User Id', $system_user_id, '100%');
        $this->form->addQuickField('Descricao', $descricao, '100%');
        $this->form->addQuickField('Anexo', $anexo, '100%');
        $this->form->addQuickField('Nota', $nota, '100%');
        $this->form->addQuickField('Feedback', $feedback, '100%');
        $this->form->addQuickField('Data Envio', $data_envio, '100%');
        $this->form->addQuickField('Data Ultimaedicao', $data_ultimaedicao, '100%');

        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
       //$this->form->addQuickAction('Voltar',new TAction(array('AtividadeList','onReload')),'fa:arrow-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Envio de Atividade', $this->form));
        
        parent::add($container);
    }


    public function mostrar()
    {

    }


/*
    public function setaAtividadeId( $param )
    {
        try
        {
            $obj = new StdClass;
            $obj->atividade_id = $param['key'];

            $this->form->setData($obj);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
*/

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);   

            
            $this->form->validate(); 
            
            $object = new AtividadeAluno;  
            $data = $this->form->getData(); 

            $data->system_user_id = $user->id;

            if(empty($data->data_envio))
            {
                $data->data_envio = date('Y-m-d H:i:s');
            }
            
            $data->data_ultimaedicao = date('Y-m-d H:i:s');         


            $object->fromArray( (array) $data); 
            $object->store(); 
            
            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            $param = [];
            $param['key'] = TSession::getValue('atividadeid');
            $param['id'] = TSession::getValue('atividadeid');

            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'),TApplication::loadPage('AtividadeAlunoList','onReload',$param));
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback(); 
        }
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);

        $obj = new StdClass;
        $obj->atividade_id = TSession::getValue('atividadeid');

        $this->form->setData($obj);
    }
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new AtividadeAluno($key); 
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
}
