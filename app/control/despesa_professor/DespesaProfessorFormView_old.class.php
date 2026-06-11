<?php
/**
 * DespesaFormView Form
 * @author  <your name here>
 */
class DespesaProfessorFormView extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Despesa');
        $this->form->setFormTitle('Relatório Indivual de Despesas');
        
        $label1 = new TLabel('ID:', '#333333', '15px', '');
        $label2 = new TLabel('Professor:', '#333333', '15px', '');
        $label3 = new TLabel('Curso:', '#333333', '15px', '');
        $label4 = new TLabel('Vem duas vezes por dia a Ituverava?', '#333333', '15px', '');
        $label5 = new TLabel('Trecho:', '#333333', '15px', '');
        $label6 = new TLabel('Dias que ministrou aulas:', '#333333', '15px', '');
        $label7 = new TLabel('Quantidade de dias:', '#333333', '15px', '');
        $label8 = new TLabel('Observação:', '#333333', '15px', '');
        //$label9 = new TLabel('Operador:', '#333333', '15px', '');
        $label10 = new TLabel('Data do registro:', '#333333', '15px', '');
        $label11 = new TLabel('Total por dia:', '#333333', '15px', '');
        $label12 = new TLabel('Total percorrido (Km):', '#333333', '15px', '');
        $label13 = new TLabel('Custo médio Km rodado: (R$)', '#333333', '15px', '');
        $label14 = new TLabel('Situação:', '#333333', '15px', '');
        $label15 = new TLabel('Total de despesas: (R$)', '#333333', '15px', '');
        $label16 = new TLabel('Distância (Km): ', '#333333', '15px', '');
        
        $despesa = new DespesaProfessor($param['key']);
        
        $text1  = new TTextDisplay($despesa->id, '#333333', '15px', '');          
        $text2  = new TTextDisplay($despesa->nome, '#333333', '15px', '');
        $text3  = new TTextDisplay($despesa->curso, '#333333', '15px', '');
        $text4  = new TTextDisplay($despesa->viagem_dobro, '#333333', '15px', '');
        $text5  = new TTextDisplay($despesa->trecho_professor->nome_trecho, '#333333', '15px', '');
        $text6  = new TTextDisplay($despesa->qtd_aulas, '#333333', '15px', '');
        $text7  = new TTextDisplay($despesa->qtd_dias, '#333333', '15px', '');
        $text8  = new TTextDisplay($despesa->obs, '#333333', '15px', '');
        //$text9  = new TTextDisplay($despesa->system_user->name, '#333333', '15px', '');
        //$text9  = new TTextDisplay($despesa->data_reg, '#333333', '12px', '');
        $text10  = new TTextDisplay(TDate::convertToMask($despesa->data_reg, 'yyyy-mm-dd', 'dd/mm/yyyy'), '#333333', '15px', '');
        
        //$text11  = new TTextDisplay($despesa->valor_total, '#333333', '12px', '');
        
        //$text12 = new TTextDisplay(($despesa->viagem_dobro == 'Sim') ? $despesa->trecho->distancia * 4 : $despesa->trecho->distancia * 2, '#333333', '12px', ''); 
        $text11  = new TTextDisplay(number_format($despesa->total_dia, '2', ',', '.'), '#333333', '15px', '');
        $text12  = new TTextDisplay(number_format($despesa->total_percorrido, '2', ',', '.'), '#333333', '15px', '');
        $text13  = new TTextDisplay(number_format($despesa->custo_medio, '2', ',', '.'), '#333333', '15px', '');
        $text14  = new TTextDisplay($despesa->situacao, '#333333', '15px', ''); 
        $text15  = new TTextDisplay(number_format($despesa->valor_total, '2', ',', '.'), '#333333', '15px', '');
        $text16  = new TTextDisplay(number_format($despesa->trecho_professor->distancia, '2', ',', '.'), '#333333', '15px', '');
        /**
        if ($despesa->viagem_dobro == 'Não')
        {
            //echo $text12 = $despesa->trecho->distancia * 2;
            echo $text12  = new TTextDisplay($despesa->trecho->distancia * 2, '#333333', '12px', '');
        }
        else {
            echo $text12  = new TTextDisplay($despesa->trecho->distancia * 4, '#333333', '12px', '');
        }
        //$text12  = new TTextDisplay($despesa->trecho->distancia, '#333333', '12px', '');
        */
        $this->form->addFields([$label1],[$text1],[$label2],[$text2]);
        //$this->form->addFields([$label2],[$text2]);
        $this->form->addFields([$label3],[$text3],[$label4],[$text4]);
        //$this->form->addFields([$label4],[$text4]);
        $this->form->addFields([$label5],[$text5],[$label16],[$text16]);
        $this->form->addFields([$label6],[$text6],[$label7],[$text7]);
        $this->form->addFields([$label8],[$text8],[$label10],[$text10]);
        //$this->form->addFields([$label8],[$text8]);
        //$this->form->addFields([$label9],[$text9],[$label10],[$text10]);
        $this->form->addFields([$label11],[$text11],[$label12],[$text12]);
        //$this->form->addFields([$label10],[$text10]);
        //$this->form->addFields([$label11],[$text11],[$label12],[$text12]);
        $this->form->addFields([$label13],[$text13],[$label15],[$text15]);
        //$this->form->addFields([$label12],[$text12]);
        $this->form->addFields([$label14],[$text14]);
        //$this->form->addFields([$label16],[$text16]);
        //$this->form->addFields([$label14],[$text14]);
        //$this->form->addFields([$label15],[$text15]);

        
        $this->despesa_item_list = new TQuickGrid;
        $this->despesa_item_list->style = 'width:100%';
        $this->despesa_item_list->disableDefaultClick();
        
        //$this->despesa_item_list->addQuickColumn('Código', 'id', 'left');
        $this->despesa_item_list->addQuickColumn('Categoria', 'item_tipo', 'left');
        $column_data = $this->despesa_item_list->addQuickColumn('Data', 'data_despesa', 'left');
        $column_valor = $this->despesa_item_list->addQuickColumn('Valor', 'valor', 'left');
        $column_quantidade = $this->despesa_item_list->addQuickColumn('Quantidade', 'quantidade', 'left');
        //$this->despesa_item_list->addQuickColumn('Descrição', 'descricao', 'left');


        $column_data->setTransformer(array($this, 'formatDate'));

        $column_total = $this->despesa_item_list->addQuickColumn('Total', '=( {quantidade} * {valor}  )', 'right');

        $column_valor->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return "R$ " . number_format($value, 3, ",", ".");
        });

        $column_quantidade->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return number_format($value, 2, ",", ".");
        });
        
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
        
        $this->despesa_item_list->createModel();
        
        $items = DespesaProfessorItem::where('despesa_id', '=', $despesa->id)->load();
        $this->despesa_item_list->addItems($items);
        
        $panel = new TPanelGroup('Itens', '#f5f5f5');
        $panel->add(new BootstrapDatagridWrapper($this->despesa_item_list));
        
        $this->form->addContent([$panel]);
        
        $this->form->addHeaderAction('Imprimir', new TAction(['DespesaProfessorFormView', 'onPrint'],['key'=>$despesa->id]), 'far:file-pdf red');
        //$this->form->addHeaderAction('Editar', new TAction(['DespesaForm', 'onEdit'],['key'=>$despesa->id]), 'far:edit blue');
        //$this->form->addAction('Voltar', new TAction(array('DespesaList','onReload')), 'far:arrow-alt-circle-left blue');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'formView-container'; 
        $container->add(new TXMLBreadCrumb('menu.xml', 'DespesaProfessorList'));
        $container->add($this->form);

        TTransaction::close();

        parent::add($container);
    }


    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    

    /**
     * Imprime a despesa
    */ 
    public function onPrint($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $object = DespesaProfessor::find($param['key']);
            //var_dump($object);
            //die;
             
            if ($object)
            {
                //$total += 

                $object->data_reg = TDate::date2br($object->data_reg);
                $object->valor_total = number_format($object->valor_total, 2, ",", ".");
                $object->custo_medio = number_format($object->custo_medio, 2, ",", ".");
                $object->total_dia = number_format($object->total_dia, 2, ",", ".");
                $object->total_percorrido = number_format($object->total_percorrido, 2, ",", ".");
                //return "R$ " . number_format($value, 2, ",", ".");

                $html = new AdiantiHTMLDocumentParser('app/documents/DespesaDocument.html', 'A4', 'portrait');
                $html->setMaster($object);
    
                $objects = DespesaProfessorItem::where('despesa_id', '=', $object->id)->load();

                //$repository = new TRepository('Despesa');
                //$objects = $repository->load($criteria);

                foreach ($objects as $object)
                {
                	$total+= ($object->quantidade * $object->valor);
                    $object->data_despesa = TDate::date2br($object->data_despesa);
                    $object->valor = number_format($object->valor, 2, ",", ".");
                    $object->quantidade = number_format($object->quantidade, 2, ",", ".");
                	//$total1 = $total;
                }
                //$objects->total = $total;
                //$objects->$total;
                //var_dump($total);
                //die;
                $html->setDetail('DespesaProfessorItem', $objects, $total);
    
                //$masterObject->valor_total += ($detailObject->quantidade * $detailObject->valor);
                //var_dump($total);
                //die;
    
                $html->process();
                $output = $html->getContents();
                
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
                
                parent::openFile($document);
                new TMessage('info', _t('Document successfully generated'));
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

   
            