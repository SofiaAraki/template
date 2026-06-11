<?php

class ReqBolsaGestorFormView extends TPage
{
     protected $form;
    

    public function __construct( $param )
    {
        parent::__construct();


        TTransaction::open('Felabs_DB');

        $this->form = new BootstrapFormBuilder('form_Aluno');
        $this->form->setFormTitle('Requerimento de Bolsa de Estudo');

        
        $label1 = new TLabel('ID:', '#333333', '15px', '');
        $label2 = new TLabel('Nome:', '#333333', '15px', '');
        $label3 = new TLabel('RG:', '#333333', '15px', '');
        $label4 = new TLabel('CPF:', '#333333', '15px', '');
        $label5 = new TLabel('Data de nascimento:', '#333333', '15px', '');
        $label6 = new TLabel('Estado civil:', '#333333', '15px', '');
        $label7 = new TLabel('Profissão:', '#333333', '15px', '');
        $label8 = new TLabel('Endereço:', '#333333', '15px', '');
        $label9 = new TLabel('Nº:', '#333333', '15px', '');
        $label10 = new TLabel('Bairro:', '#333333', '15px', '');
        $label11 = new TLabel('Complemento:', '#333333', '15px', '');
        $label12 = new TLabel('Cidade:', '#333333', '15px', '');
        $label13 = new TLabel('Estado:', '#333333', '15px', '');
        $label14 = new TLabel('CEP:', '#333333', '15px', '');
        $label15 = new TLabel('Telefone:', '#333333', '15px', '');
        $label16 = new TLabel('Celular:', '#333333', '15px', '');
        $label17 = new TLabel('Telefone (trabalho):', '#333333', '15px', '');
        $label18 = new TLabel('Email:', '#333333', '15px', '');
        $label19 = new TLabel('Curso:', '#333333', '15px', '');
        $label20 = new TLabel('Ciclo:', '#333333', '15px', '');
        $label21 = new TLabel('Período:', '#333333', '15px', '');
        $label22 = new TLabel('A família reside em moradia:', '#333333', '15px', '');
        $label23 = new TLabel('O aluno reside em:', '#333333', '15px', '');
        $label24 = new TLabel('A família possui veículo:', '#333333', '15px', '');
        $label25 = new TLabel('(Se aluno da Educação Básica) No ano anterior estudou em escola:<br><br>(Se aluno da Educação Superior) Concluiu o ensino médio em escola:', '#333333', '15px', '');
        //$label27 = new TLabel('CadÚnico:', '#333333', '15px', '');
        $label28 = new TLabel('O aluno possui outra graduação em Ensino Superior:', '#333333', '15px', '');
        $label29 = new TLabel('Se sim, qual:', '#333333', '15px', '');


        $aluno = new ReqBolsaAluno($param['key']);

        $label1 = new TLabel('Dados pessoais do aluno', '#285097', 12, 'b');
        $this->form->addContent( [$label1] );
        
        $text1  = new TTextDisplay($aluno->id, '#333333', '15px', '');          
        $text2  = new TTextDisplay($aluno->nome, '#333333', '15px', '');
        $text3  = new TTextDisplay($aluno->rg, '#333333', '15px', '');
        $text4  = new TTextDisplay($aluno->cpf, '#333333', '15px', '');
        $text5  = new TTextDisplay($aluno->data_nascimento, '#333333', '15px', '');
        $text6  = new TTextDisplay($aluno->estado_civil, '#333333', '15px', '');
        $text7  = new TTextDisplay($aluno->profissao, '#333333', '15px', '');
        $text8  = new TTextDisplay($aluno->endereco, '#333333', '15px', '');
        $text9  = new TTextDisplay($aluno->endereco_numero, '#333333', '15px', '');
        $text10  = new TTextDisplay($aluno->bairro, '#333333', '15px', '');
        $text11  = new TTextDisplay($aluno->endereco_complemento, '#333333', '15px', '');
        $text12  = new TTextDisplay($aluno->cidade, '#333333', '15px', '');
        $text13  = new TTextDisplay($aluno->estado, '#333333', '15px', '');
        $text14  = new TTextDisplay($aluno->cep, '#333333', '15px', '');
        $text15  = new TTextDisplay($aluno->telefone, '#333333', '15px', '');
        $text16  = new TTextDisplay($aluno->celular, '#333333', '15px', '');
        $text17  = new TTextDisplay($aluno->telefone_trabalho, '#333333', '15px', '');
        $text18  = new TTextDisplay($aluno->email, '#333333', '15px', '');
        $text19  = new TTextDisplay($aluno->curso, '#333333', '15px', '');
        $text20  = new TTextDisplay($aluno->ciclo, '#333333', '15px', '');
        $text21  = new TTextDisplay($aluno->periodo, '#333333', '15px', '');
        //$text27  = new TTextDisplay($aluno->cad_unico, '#333333', '15px', '');
        $text28  = new TTextDisplay($aluno->outra_graduacao, '#333333', '15px', '');
        $text29  = new TTextDisplay($aluno->graduacao_anterior, '#333333', '15px', '');


        $label26 = new TLabel('<br>Especificações', '#285097', 12, 'b');

        $text22  = new TTextDisplay($aluno->moradia, '#333333', '15px', '');
        $text23  = new TTextDisplay($aluno->moradia_aluno, '#333333', '15px', '');
        $text24  = new TTextDisplay($aluno->veiculo_aluno, '#333333', '15px', '');
        $text25  = new TTextDisplay($aluno->ensino_aluno, '#333333', '15px', '');


        $this->form->addFields([$label2],[$text2],[$label3],[$text3],[$label4],[$text4]);
        $this->form->addFields([$label5],[$text5],[$label6],[$text6],[$label7],[$text7]);
        $this->form->addFields([$label8],[$text8],[$label9],[$text9],[$label10],[$text10]);
        $this->form->addFields([$label11],[$text11],[$label12],[$text12],[$label13],[$text13]);
        $this->form->addFields([$label14],[$text14],[$label15],[$text15],[$label16],[$text16]);
        $this->form->addFields([$label17],[$text17],[$label18],[$text18],[''],['']);
        $this->form->addFields([$label19],[$text19],[$label20],[$text20],[$label21],[$text21]);
        //$this->form->addFields([$label27],[$text27]);
        $this->form->addContent( [$label26] );
        $this->form->addFields([$label22],[$text22],[$label23],[$text23],[$label24],[$text24]);
        $this->form->addFields([$label25],[$text25], [$label28],[$text28],[$label29],[$text29]);
        $this->form->addFields(['<br>']);


        $this->aluno_item_list = new TQuickGrid;
        $this->aluno_item_list->style = 'width:100%';
        $this->aluno_item_list->disableDefaultClick();
        
        $this->aluno_item_list->addQuickColumn('Membro', 'item_membro', 'left');
        $this->aluno_item_list->addQuickColumn('Nome', 'nome', 'left');
        $this->aluno_item_list->addQuickColumn('RG', 'rg', 'left');
        $this->aluno_item_list->addQuickColumn('CPF', 'cpf', 'left');
        $this->aluno_item_list->addQuickColumn('Idade', 'idade', 'left');
        $this->aluno_item_list->addQuickColumn('Profissão', 'profissao', 'left');
        $this->aluno_item_list->addQuickColumn('Salário', 'salario', 'left');
        $this->aluno_item_list->addQuickColumn('Local de trabalho', 'local_trabalho', 'left');


        $column_total = $this->aluno_item_list->addQuickColumn('Total', '=( {salario} )', 'right');
        
        
        $column_total->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 


        $column_total->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return "R$ " . number_format($value, 2, ",", ".");
        });
        
        
        $this->aluno_item_list->createModel();
        
        
        $items = ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $aluno->id)->load();
        $this->aluno_item_list->addItems($items);
        
        
        $panel = new TPanelGroup('Descrição do grupo familiar', '#f5f5f5');
        $panel->add(new BootstrapDatagridWrapper($this->aluno_item_list));
        
        
        $this->form->addContent([$panel]);


        /*Retirado do formulário a partir de 13/04/2022
        $this->aluno_despesa_list = new TQuickGrid;
        $this->aluno_despesa_list->style = 'width:100%';
        $this->aluno_despesa_list->disableDefaultClick();
        
        
        $this->aluno_despesa_list->addQuickColumn('Descrição', 'item_tipo', 'left');


        $column_total_despesa = $this->aluno_despesa_list->addQuickColumn('Total', '=({valor})', 'right');
        
        
        $column_total_despesa->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 


        $column_total_despesa->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return "R$ " . number_format($value, 2, ",", ".");
        });
        
        
        $this->aluno_despesa_list->createModel();


        $items = ReqBolsaAlunoDespesa::where('req_bolsa_aluno_id', '=', $aluno->id)->load();
        $this->aluno_despesa_list->addItems($items);
        
        
        $panel = new TPanelGroup('Despesas do grupo familiar', '#f5f5f5');
        $panel->add(new BootstrapDatagridWrapper($this->aluno_despesa_list));
        
        
        $this->form->addContent([$panel]);*/
        
        
        //$this->form->addHeaderAction('Imprimir', new TAction(['ReqBolsaGestorFormView', 'onPrint'],['key'=>$aluno->id]), 'far:file-pdf red');
        $this->form->addHeaderAction(('Voltar'),new TAction(array('ReqBolsaAlunoListGestor','onReload')),'far:arrow-alt-circle-left blue');
        
        
        $this->subnotebook = new BootstrapNotebookWrapper(new TNotebook);
        $this->form->addContent( [$this->subnotebook] );


        $obs = new TTextDisplay($aluno->obs);
        $obs->style = 'margin:10px;display:block';
        $this->subnotebook->appendPage(('Parecer Técnico Assistente Social'), $obs);


        $obs_ass_social = new TTextDisplay($aluno->obs_ass_social);
        $obs_ass_social->style = 'margin:10px;display:block';
        $this->subnotebook->appendPage(('Parecer Técnico (não visível para alunos) '), $obs_ass_social);


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'formView-container'; 
        $container->add(new TXMLBreadCrumb('menu.xml', 'ReqBolsaAlunoListGestor'));
        $container->add($this->form);


        TTransaction::close();


        parent::add($container);
    }
    
 
    public function onPrint($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $object = ReqBolsaAluno::find($param['key']);
             
            if ($object)
            {

                $html = new AdiantiHTMLDocumentParser('app/documents/GestorDocument.html', 'A4', 'portrait');
                $html->setMaster($object);
                $object->data_final = TDate::date2br($object->data_final);
                $object->rp_salario_minimo = number_format($object->rp_salario_minimo, 2, ",", ".");
                $object->rf_salario_minimo = number_format($object->rf_salario_minimo, 2, ",", ".");
    
                $objects = ReqBolsaAlunoItem::where('req_bolsa_aluno_id', '=', $object->id)->load();
                //$objects_despesa = ReqBolsaAlunoDespesa::where('req_bolsa_aluno_id', '=', $object->id)->load();

                $html->setDetail('ReqBolsaAlunoItem', $objects);
                //$html->setDetail('ReqBolsaAlunoDespesa', $objects_despesa);
       
                $html->process();
                $output = $html->getContents();
                
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
                
                parent::openFile($document);
                new TMessage('info', ('Documento gerado com sucesso. Por favor, habilite os popups no navegador.'));
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

   
            