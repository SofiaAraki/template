<?php


class ConteudoProgramaticoFormView extends TPage
{
     protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();


        TTransaction::open('Felabs_DB');
        
        //$logged = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        
        
        $this->form = new BootstrapFormBuilder('form_ConteudoProgramatico');
        $this->form->setFormTitle('Conteúdo Programático');
        
        $label1 = new TLabel('ID:', '#333333', '15px', '');
        $label2 = new TLabel('Professor:', '#333333', '15px', '');
        $label3 = new TLabel('Curso:', '#333333', '15px', '');
        $label4 = new TLabel('Disciplina', '#333333', '15px', '');
        $label5 = new TLabel('Etapa:', '#333333', '15px', '');
        $label6 = new TLabel('Turma:', '#333333', '15px', '');
        $label7 = new TLabel('', '#333333', '15px', '');
        
        $conteudo = new ConteudoProgramatico($param['key']);
        
        $text1  = new TTextDisplay($conteudo->id, '#333333', '15px', '');          
        $text2  = new TTextDisplay($conteudo->system_user_id, '#333333', '15px', '');
        $text3  = new TTextDisplay($conteudo->curso, '#333333', '15px', '');
        $text4  = new TTextDisplay($conteudo->disciplina, '#333333', '15px', '');
        $text5  = new TTextDisplay($conteudo->etapa, '#333333', '15px', '');
        $text6  = new TTextDisplay($conteudo->turma, '#333333', '15px', '');
        $text7  = new TTextDisplay($conteudo->status, '#333333', '15px', '');
        
        
        $this->form->addFields([$label1],[$text1],[$label7],[$text7]);
        //$this->form->addFields([$label2],[$text2]);
        $this->form->addFields([$label3],[$text3],[$label4],[$text4]);
        //$this->form->addFields([$label4],[$text4]);
        $this->form->addFields([$label5],[$text5],[$label6],[$text6]);
        //$this->form->addFields([$label6],[$text6]);
        //$this->form->addFields();

        
        $this->conteudo_programatico_item_list = new TQuickGrid;
        $this->conteudo_programatico_item_list->style = 'width:100%';
        $this->conteudo_programatico_item_list->disableDefaultClick();
        
        //$this->conteudo_programatico_item_list->addQuickColumn('id', 'id', 'left');
        $this->conteudo_programatico_item_list->addQuickColumn('Data da aula', 'data_aula', 'left');
        $this->conteudo_programatico_item_list->addQuickColumn('Conteúdo', 'conteudo', 'left', '85%');
     
        $this->conteudo_programatico_item_list->createModel();


        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('conteudo_programatico_id', '=', $conteudo->id));
        $criteria1->setProperty('order', 'data_aula');
		$criteria1->setProperty('direction','ASC');
        
        $items = ConteudoProgramaticoItem::getObjects($criteria1);

        foreach($items as $item)
        {
        	$item->data_aula = TDate::date2br($item->data_aula);
        }

        $this->conteudo_programatico_item_list->addItems($items);
        
        $panel = new TPanelGroup('Itens', '#f5f5f5');
        $panel->add(new BootstrapDatagridWrapper($this->conteudo_programatico_item_list));
        
        $this->form->addContent([$panel]);
        
        if($user->funcao_legado == 'Professor')
        {
            $this->form->addHeaderAction('Voltar', new TAction(['ConteudoProgramaticoList','onReload']), 'far:arrow-alt-circle-left blue');
        }
        else
        {
            $this->form->addHeaderAction('Voltar', new TAction(['ConteudoProgramaticoListAll','onReload']), 'far:arrow-alt-circle-left blue');
        }
        
        $this->form->addHeaderAction('Editar', new TAction(['ConteudoProgramaticoForm', 'onEdit'],['key'=>$conteudo->id]), 'far:edit blue fa-lg');
        $this->form->addHeaderAction('Imprimir', new TAction(['ConteudoProgramaticoFormView', 'onPrint'],['key'=>$conteudo->id]), 'far:file-pdf red');
              
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'formView-container'; 
        //$container->add(new TXMLBreadCrumb('menu.xml', 'ConteudoProgramaticoFormView'));
        $container->add($this->form);

        TTransaction::close();

        parent::add($container);
    }


    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    

    public function onPrint($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $object = ConteudoProgramatico::find($param['key']);      
             
            if ($object)
            {
                $object->data_reg = TDate::date2br($object->data_reg);

                $userName = new SystemUser($object->system_user_id);
                $object->system_user_id = $userName->name;


                TTransaction::open('dados_fei');

                $criteria = new TCriteria;
                $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
                //$criteria->setProperty('order', 'data_aula');
				//$criteria->setProperties('direction','ASC');
                
                $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria);
                $object->disciplina = $disciplinaNome[0]->NomeDisciplina;

                TTransaction::close();
             

                $html = new AdiantiHTMLDocumentParser('app/documents/ConteudoProgramaticoDocument.html', 'A4', 'portrait');
                $html->setMaster($object);


                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('conteudo_programatico_id', '=', $object->id));
                $criteria1->setProperty('order', 'data_aula');
				$criteria1->setProperty('direction','ASC');
    

                $objects = ConteudoProgramaticoItem::getObjects($criteria1);

                foreach($objects as $objectss)
                {
                    $objectss->data_aula = TDate::date2br($objectss->data_aula);
                }                
                
                $html->setDetail('ConteudoProgramaticoItem', $objects);    
                $html->process();
                
                $output = $html->getContents();
                
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
                
                //parent::openFile($document);
            

                $window = TWindow::create('Conteúdo Programático', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = 'download.php?file='.$document;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";
                $window->add($object);
                $window->show();

                //new TMessage('info', 'Documento PDF gerado com sucesso. Caso não tenha conseguido visualizá-lo, habilite pop-ups em seu navegador e tente gerá-lo novamente.');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }


    public function mostrar()
    {
        
    }
}

   
            