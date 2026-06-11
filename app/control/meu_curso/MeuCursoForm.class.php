<?php


class MeuCursoForm extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();

        //var_dump($param);
        //die;        
        
        // creates the form
        $this->form = new TQuickForm('form_MeuCurso');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        
        
        // define the form title
        $this->form->setFormTitle('MeuCurso');
        

        // create the form fields
        $id = new THidden('id');
        $curso_id = new THidden('curso_id');
        $filename = new TFile('filename');
        $nome = new TEntry('nome');
        $descricao = new THtmlEditor('descricao');
        $tipo = new TCombo('tipo');
        $data_reg = new THidden('data_reg');
        
        
        $tipoItems = [];
        $tipoItems['A'] = 'Atividades Complementares';
        $tipoItems['C'] = 'Calendários';
        $tipoItems['E'] = 'Estágio Supervisionado';
        $tipoItems['G'] = 'Grade Curricular';
        $tipoItems['H'] = 'Horário de Aulas';
        $tipoItems['I'] = 'Informativo';
        $tipoItems['O'] = 'Outros';
        $tipoItems['P'] = 'Projeto Pedagógico do Curso';
        $tipoItems['T'] = 'Trabalho de Conclusão de Curso (TCC)';
        
        $tipo->addItems($tipoItems);
        
        $descricao->setSize('100%',100);     

        $curso_id->setValue((int)$param['curso']);

        if($param['method'] == 'mostrarInfo')
        {
            $tipo->setValue('I');
        }

       
        // add the fields
        $this->form->addQuickField('Id', $id, '50%');
        $this->form->addQuickField('Curso Id', $curso_id, '50%');
        $this->form->addQuickField('Nome', $nome, '90%', new TRequiredValidator);
        $this->form->addQuickField('Tipo', $tipo, '90%', new TRequiredValidator);
        $this->form->addQuickField('Descrição', $descricao, '90%');
        $this->form->addQuickField('Arquivo anexo', $filename, '90%');
        $this->form->addQuickField('Data Reg', $data_reg, '90%');


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction('Voltar',  new TAction(array($this, 'onBack')), 'fa:arrow-circle-left blue');
      

        if($param['method'] == 'onEdit')
        {
            $this->form->addQuickAction('Excluir',  new TAction(array($this, 'onDelete'),$param), 'far:trash-alt red');
        }
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'MeuCursoView'));


        if($param['method'] == 'mostrarInfo')
        {
            $container->add(TPanelGroup::pack('Adicionar Informativo', $this->form));
        }
        else
        {
            $container->add(TPanelGroup::pack('Adicionar Arquivo', $this->form));
        }
        
        
        parent::add($container);
    }


    public function onBack()
    {       
        $parametro = [];
        $parametro['curso'] = TSession::getValue('cursoid');
        
        TApplication::loadPage('MeuCursoView','verPagina',$parametro);
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $this->form->validate(); 
            
            $object = new MeuCurso;  
            $data = $this->form->getData(); 

            if($data->tipo != 'I' && empty($data->filename)) //SE FOR ARQUIVO E NÃO ANEXAR, EXIBE ERRO
            {
                throw new Exception('O campo Anexo está vazio');
            }

            $data->data_reg = date('Y-m-d H:i:s');


            if($data->id) //VERIFICA SE É UM REGISTRO EXISTENTE SENDO EDITADO
            {
                $testa = new MeuCurso($data->id);
            }
     

            if ($data->filename)
            {
                if($data->id)
                {
                    if($data->filename != $testa->filename) //VERIFICA SE O ANEXO COLADO É DIFERENTE DO ANEXO ANTERIOR
                    {  
                        $today = date("YmdHis");
                        $source_file   = 'tmp/'.$data->filename;
                        $nomeArquivo = 'meucurso_'.$today.'_'. $logged->id.'_'.$data->filename;
                        $target_file   = 'files/meucurso/'.$nomeArquivo;
                        $finfo         = new finfo(FILEINFO_MIME_TYPE);
            
                        // move to the target directory
                        rename($source_file, $target_file);
            
                        // update the photo_path
                        $data->filename = $nomeArquivo;
                    }
                }
                else
                {
                    $today = date("YmdHis");
                    $source_file   = 'tmp/'.$data->filename;
                    $nomeArquivo = 'meucurso_'.$today.'_'. $logged->id.'_'.$data->filename;
                    $target_file   = 'files/meucurso/'.$nomeArquivo;
                    $finfo         = new finfo(FILEINFO_MIME_TYPE);
            
                    // move to the target directory
                    rename($source_file, $target_file);
            
                    // update the photo_path
                    $data->filename = $nomeArquivo;
                }
            }


            $object->fromArray( (array) $data); 
            $object->store(); 
            

            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            $parametro = [];
            $parametro['curso'] = TSession::getValue('cursoid');

            new TMessage('info', 'Registro salvo',TApplication::loadPage('MeuCursoView','verPagina',$parametro)); 
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
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new MeuCurso($key); 
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


    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    

    public function Delete($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new MeuCurso($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close();
            //$this->onReload( $param ); 
            
            $parametro = [];
            $parametro['curso'] = TSession::getValue('cursoid');

            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'),TApplication::loadPage('MeuCursoView','verPagina',$parametro));
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }


    public function mostrar( $param )
    {
      
    }


    public function mostrarInfo( $param )
    {
 
    }
}
