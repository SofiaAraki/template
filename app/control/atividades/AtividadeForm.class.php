<?php

class AtividadeForm extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new TQuickForm('form_Atividade');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        
        // define the form title
        $this->form->setFormTitle('Atividade');
        

        // create the form fields
        $id = new THidden('id');
        $coddisciplina = new THidden('coddisciplina');
        $codturmaetapa = new THidden('codturmaetapa');
        $tipo = new TCombo('tipo');
        $nome = new TEntry('nome');
        $descricao = new THtmlEditor('descricao');
        $anexo = new TFile('anexo');
        $valor_nota = new THidden('valor_nota');
        $data_prazo = new THidden('data_prazo');
        $data_reg = new THidden('data_reg');
        $system_user_id = new THidden('system_user_id');
        $ordem = new THidden('ordem');


        $tipoItems = [];
        //$tipoItems[0] = 'Selecione';
        $tipoItems[1] = 'Conteúdo';
        //$tipoItems[2] = 'Entrega de atividade';
        //$tipoItems[3] = 'Fórum de discussão';
        
        $tipo->setValue(0);
        
        $tipo->addItems($tipoItems);
        
        $tipo->setChangeAction(new TAction(array($this, 'onChangeType')));

        $descricao->setSize('90%',150);  


        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Coddisciplina', $coddisciplina, '100%');
        $this->form->addQuickField('Codturmaetapa', $codturmaetapa, '100%');
        $this->form->addQuickField('Tipo', $tipo, '100%', new TRequiredValidator);
        $this->form->addQuickField('Nome', $nome, '100%', new TRequiredValidator);
        $this->form->addQuickField('Descricao', $descricao, '100%');
        $this->form->addQuickField('Anexo', $anexo, '100%');
        $this->form->addQuickField('Valor Nota', $valor_nota, '100%');
        $this->form->addQuickField('Data Prazo', $data_prazo, '30%');
        $this->form->addQuickField('Data Reg', $data_reg, '100%');
        $this->form->addQuickField('System User Id', $system_user_id, '100%');
        $this->form->addQuickField('Ordem', $ordem, '100%');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

         
        // create the form actions
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addQuickAction(_t('Back'),  new TAction(array('AtividadeList', 'onReload'),$param), 'fa:arrow-circle-left blue');

        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('Nova Atividade', $this->form));
        
        parent::add($container);
    }


    public static function onChangeType($param)
    {
        if ($param['tipo'] == 1)
        {
            TQuickForm::hideField('form_Atividade', 'valor_nota');
            TQuickForm::hideField('form_Atividade', 'data_prazo');
        }
        
        if ($param['tipo'] == 2)
        {
            TQuickForm::showField('form_Atividade', 'valor_nota');
            TQuickForm::showField('form_Atividade', 'data_prazo');
        }
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);  
 
            $this->form->validate(); 
            
            $object = new Atividade;  
            $data = $this->form->getData(); 

            $data->system_user_id = $user->id;
            $data->data_reg = date('Y-m-d H:i:s');
         

            if($data->id) //VERIFICA SE É UM REGISTRO EXISTENTE SENDO EDITADO
            {
                $testa = new Atividade($data->id);
            }
     

            if ($data->anexo)
            {
                if($data->id)
                {
                    if($data->anexo != $testa->anexo) //VERIFICA SE O ANEXO COLADO É DIFERENTE DO ANEXO ANTERIOR
                    {  
                        $today = date("YmdHis");
                        $source_file   = 'tmp/'.$data->anexo;
                        $nomeArquivo = 'ativ_'.$today.'_'. $user->id.'_'.$data->anexo;
                        $target_file   = 'files/atividades/'.'atividade/'.$nomeArquivo;
                        $finfo         = new finfo(FILEINFO_MIME_TYPE);
            
                        // move to the target directory
                        rename($source_file, $target_file);
            
                        // update the photo_path
                        $data->anexo = $nomeArquivo;
                    }  
                }
                else
                {
                    $today = date("YmdHis");
                    $source_file   = 'tmp/'.$data->anexo;
                    $nomeArquivo = 'ativ_'.$today.'_'. $user->id.'_'.$data->anexo;
                    $target_file   = 'files/atividades/'.'atividade/'.$nomeArquivo;
                    $finfo         = new finfo(FILEINFO_MIME_TYPE);
            
                    // move to the target directory
                    rename($source_file, $target_file);
            
                    // update the photo_path
                    $data->anexo = $nomeArquivo;
                }
            }


            $object->fromArray( (array) $data); 
            $object->store();   

  
            $data->id = $object->id;
            
            $this->form->setData($data); 
            

            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'),TApplication::loadPage('AtividadeList','onReload'));
                    

            TTransaction::close();   
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
    }



    public function mostrar( $param )
    {
        $teste = TSession::getValue('sessao_prof');

        //var_dump($teste['codturmaetapa']);
        //die();

        $obj = new StdClass;
        $obj->codturmaetapa = $teste['codturmaetapa'];
        $obj->coddisciplina = $teste['coddisciplina'];

        TForm::sendData('form_Atividade', $obj);     
    }
    

    public function onEdit( $param )
    {
       // $param['key'] = TSession::getValue('atividadeid');

        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TTransaction::open('Felabs_DB');
                
                $object = new Atividade($key);
                //$object->anexo = NULL;
                $this->form->setData($object); 
                
                TTransaction::close();


                if($object->tipo == 1)
                {
                    TQuickForm::hideField('form_Atividade', 'valor_nota');
                    TQuickForm::hideField('form_Atividade', 'data_prazo');
                }
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
