<?php


class MeuCursoInformativo extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    

    public function __construct($param)
    {
        parent::__construct();
        
       
        TTransaction::open('Felabs_DB');
        
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
            
        $meuCursoInfo = new MeuCurso($param['key']);
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_AtividadeAluno');
        //$this->form->class = 'tform'; 
        //$this->form = new BootstrapFormWrapper($this->form);
        //$this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle($meuCursoInfo->nome);
        

        $label1 = new TLabel('Descrição:', '#333333', '15px', '');
        
        //$label3 = new TLabel('CPF:', '#333333', '15px', '');
        //$text2  = new TTextDisplay($atividadeInfo->anexo, '#333333', '15px', '');

        if($meuCursoInfo->filename) //SE TIVER ANEXO, MOSTRA
        {  
            $label2 = new TLabel('Anexo:', '#333333', '15px', '');   
            $button  = new TButton('download_anexo');

            $button->setImage('fas:cloud-download-alt');
            $button->setAction(new TAction(array($this, 'onDownloadMaster'),$param), 'Download');
        }

        if($meuCursoInfo->descricao) //SE FOR APENAS UM ANEXO PARA DOWNLOAD, NÃO HABILITA BOTÃO DE ENVIO DE ATIVIDADE PELO ALUNO
        {
            $text1  = new TTextDisplay($meuCursoInfo->descricao, '#333333', '15px', '');
            $this->form->addFields([$text1]);
        }

        $this->form->addFields([$label2],[$button]);

        $param['curso'] = $meuCursoInfo->curso_id;


        if($user->funcao_legado == 'Aluno')
        {
            $this->form->addAction('Voltar para Meu Curso',new TAction(array('MeuCursoViewAluno','verPagina'),$param),'fa:arrow-circle-left blue');
        }
        elseif($user->funcao_legado == 'Professor')
        {
            $this->form->addAction('Voltar para Meu Curso',new TAction(array('MeuCursoViewProfessor','verPagina'),$param),'fa:arrow-circle-left blue');
        }
        else
        {
            $this->form->addAction('Voltar para Meu Curso',new TAction(array('MeuCursoView','verPagina'),$param),'fa:arrow-circle-left blue');
        }


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onDownloadMaster($param)
    {
        try
        {               
            $cursoId = TSession::getValue('cursoid');
            
            $id = $param['id'];  
    
            TTransaction::open('Felabs_DB'); 
    
            $object = new MeuCurso($id); 
    
            TTransaction::close(); 

           
            if(!empty($object->filename))
            {              
                if (strtolower(substr($object->filename, -4)) == 'html')
                {
                    $win = TWindow::create( $object->filename, 0.8, 0.8 );
                    $win->add( file_get_contents( "files/meucurso/".$object->filename ) );
                    $win->show();
                }
                else
                {
                    TPage::openFile("files/meucurso/".$object->filename);
                }
                  
                //$this->form->setData( $this->form->getData() ); // keep form data
                
                $parametros = [];
                $parametros['id'] = $param['id'];
                $parametros['key'] = $param['id'];
                //$parametros['curso'] = $cursoId;

                TApplication::loadPage('MeuCursoInformativo','mostrar',$parametros);
                TTransaction::rollback();
            }
            else
            {
                new TMessage('info', 'Este tópico não possui anexos'); 
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
            
            $object = new Atividade($key, FALSE); 
            $object->delete(); 
            
            TTransaction::close(); 
            $this->onReload( $param ); 
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'),TApplication::loadPage('AtividadeList','onReload')); 
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }


    public function mostrar($param)
    {

    }
}
