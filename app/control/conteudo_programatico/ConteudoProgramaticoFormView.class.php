<?php

class ConteudoProgramaticoFormView extends TPage
{
    protected $form; 

    public function __construct( $param )
    {
        parent::__construct();

        TTransaction::open('Felabs_DB');
        
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        
        $this->form = new BootstrapFormBuilder('form_ConteudoProgramatico');
        $this->form->setFormTitle('Conteúdo Programático');
        
        // CORREÇÃO: Removida a cor fixa '#333333' e adicionada classe Bootstrap para compatibilidade com tema Dark
        $label1 = new TLabel('ID:', null, '15px', '');
        $label1->class = 'text-muted';
        $label2 = new TLabel('Professor:', null, '15px', '');
        $label2->class = 'text-muted';
        $label3 = new TLabel('Curso:', null, '15px', '');
        $label3->class = 'text-muted';
        $label4 = new TLabel('Disciplina:', null, '15px', '');
        $label4->class = 'text-muted';
        $label5 = new TLabel('Etapa:', null, '15px', '');
        $label5->class = 'text-muted';
        $label6 = new TLabel('Turma:', null, '15px', '');
        $label6->class = 'text-muted';
        $label7 = new TLabel('', null, '15px', '');
        
        $conteudo = new ConteudoProgramatico($param['key']);
        
        // Busca o nome por extenso da disciplina na base dados_fei
        $nomeDisciplinaPorExtenso = $conteudo->disciplina;
        try {
            TTransaction::open('dados_fei');
            $criteriaDisc = new TCriteria;
            $criteriaDisc->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $conteudo->disciplina));
            
            $disciplinaNomeObj = VwProfessordisciplinassemestre::getObjects($criteriaDisc);
            if (!empty($disciplinaNomeObj)) {
                $nomeDisciplinaPorExtenso = $disciplinaNomeObj[0]->NomeDisciplina;
            }
            TTransaction::close();
        } catch (Exception $e) {
            // Mantém o código se falhar
        }
        
        // CORREÇÃO: Removida a cor fixa '#333333' dos valores exibidos para herdar a cor do tema (Light/Dark)
        $text1  = new TTextDisplay($conteudo->id, null, '15px', '');          
        $text2  = new TTextDisplay($conteudo->system_user_id, null, '15px', '');
        $text3  = new TTextDisplay($conteudo->curso, null, '15px', '');
        $text4  = new TTextDisplay($nomeDisciplinaPorExtenso, null, '15px', ''); 
        $text5  = new TTextDisplay($conteudo->etapa, null, '15px', '');
        $text6  = new TTextDisplay($conteudo->turma, null, '15px', '');
        $text7  = new TTextDisplay($conteudo->status, null, '15px', '');
        
        $this->form->addFields([$label1],[$text1],[$label7],[$text7]);
        $this->form->addFields([$label3],[$text3],[$label4],[$text4]);
        $this->form->addFields([$label5],[$text5],[$label6],[$text6]);
        
        $this->conteudo_programatico_item_list = new TQuickGrid;
        $this->conteudo_programatico_item_list->style = 'width:100%';
        $this->conteudo_programatico_item_list->disableDefaultClick();
        
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
        
        // CORREÇÃO: Removido o background fixo '#f5f5f5' que gerava um bloco branco no painel do tema escuro
        $panel = new TPanelGroup('Itens');
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
                
                $disciplinaNome = VwProfessordisciplinassemestre::getObjects($criteria);
                if (!empty($disciplinaNome)) {
                    $object->disciplina = $disciplinaNome[0]->NomeDisciplina;
                }

                TTransaction::close();

                // --- CORREÇÃO CONTRA DEPRECATED (PHP 8+) ---
                // Transforma qualquer propriedade nula do objeto principal em string vazia
                $arrObject = (array) $object;
                foreach ($arrObject as $prop => $value) {
                    if (is_null($value)) {
                        $object->$prop = '';
                    }
                }
                // --------------------------------------------
             
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
                    
                    // --- CORREÇÃO PARA OS ITENS DA LISTA ---
                    // Evita o erro caso o conteúdo da aula venha nulo por algum motivo
                    if (is_null($objectss->conteudo)) {
                        $objectss->conteudo = '';
                    }
                }                
                
                $html->setDetail('ConteudoProgramaticoItem', $objects);    
                $html->process();
                
                $output = $html->getContents();
                
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
            
                $window = TWindow::create('Conteúdo Programático', 0.8, 0.8);
                $objectElement = new TElement('object'); // Alterado nome da variável para não conflitar
                $objectElement->data  = 'download.php?file='.$document;
                $objectElement->type  = 'application/pdf';
                $objectElement->style = "width: 100%; height:calc(100% - 10px)";
                $window->add($objectElement);
                $window->show();
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    public function onShow()
    {
    }
}