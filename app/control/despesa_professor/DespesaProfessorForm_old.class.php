
<?php
/**
 * DespesaProfessorForm Form
 * @author  <your name here>
 */
class DespesaProfessorForm extends TPage
{
    protected $form; // form
    
    use adianti\base\AdiantiMasterDetailTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_DespesaProfessor');
        $this->form->setFormTitle('Relatório Indivual de Despesas');

        // master fields
        $id = new THidden('id');
        $curso = new TEntry('curso');
        $viagem_dobro = new TRadioGroup('viagem_dobro');
        $trecho_id = new TDBCombo('trecho_id', 'Felabs_DB', 'TrechoProfessor', 'id', 'nome_trecho');
        $qtd_aulas = new TEntry('qtd_aulas');
        $qtd_dias = new TEntry('qtd_dias');
        //$qtd_dias   = new TNumeric('qtd_dias', 2, ',', '.');
        $obs = new TText('obs');
        $filename = new TMultiFile('filename');
        $unidade = new THidden('unidade');

        $radio1 = array();
        $radio1['Sim'] ='Sim';
        $radio1['Não'] ='Não';

        $viagem_dobro->setLayout('horizontal');
        $viagem_dobro->addItems($radio1);

        $trecho_id->enableSearch();

        // detail fields
        $item_despesa_id = new THidden('item_despesa_id');
        $item_despesa_item_tipo = new TCombo('item_despesa_item_tipo');
        $item_despesa_data_despesa = new TDate('item_despesa_data_despesa');
        $item_despesa_valor = new TNumeric('item_despesa_valor', '3', ',', '.' );
        $item_despesa_quantidade = new TNumeric('item_despesa_quantidade', '2', ',', '.' );

		$combo1 = array();
        $combo1['Alimentação'] ='Alimentação';
        $combo1['Combustível - Gasolina'] ='Combustível - Gasolina';
        $combo1['Combustível - Etanol'] ='Combustível - Etanol';
        $combo1['Combustível - Diesel'] ='Combustível - Diesel';
        $combo1['Pedágio'] ='Pedágio';
        
        $item_despesa_item_tipo->addItems($combo1);
        
        $item_despesa_item_tipo->enableSearch();

        //$filename->setCompleteAction(new TAction(array($this, 'onComplete')));
        $filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );

        $qtd_aulas->placeholder = ('Ex: 04/02, 06/02, 08/02 ');
        $qtd_dias->placeholder = ('Ex: 3 ');

        $qtd_dias->setMask('999');

		//$id_user->addValidation('Professor', new TRequiredValidator());
        $curso->addValidation('"Curso"', new TRequiredValidator());
        $viagem_dobro->addValidation('"Vem duas vezes por dia a Ituverava"', new TRequiredValidator());
        $trecho_id->addValidation('"Trecho"', new TRequiredValidator());
        $qtd_aulas->addValidation('"Dias que ministrou aulas"', new TRequiredValidator());
        $qtd_dias->addValidation('"Quantidade de dias"', new TRequiredValidator());
        $filename->addValidation('"Anexar comprovantes"', new TRequiredValidator());

        //$id->setEditable(false);
        //$nome->setEditable(false);
        //$id->setSize(100);
        //$id_user->setMinLength(2);
        //$id_user->setSize('40%');
        //$nome->setSize('75%');
        //$item_pedido_produto_id->setMinLength(2);
        //$item_despesa_data_despesa->setValue(date('d/m/Y h:i'));
        $item_despesa_data_despesa->setDatabaseMask('yyyy-mm-dd');
        //$id_user->setMask('{Nome}');
        $item_despesa_data_despesa->setMask('dd/mm/yyyy');
        $item_despesa_data_despesa->setSize(287);
        $obs->setSize('100%', 100);
        //$trecho_id->setSize('100%');
        $qtd_aulas->setSize('100%');
        $item_despesa_item_tipo->setSize('71%');
        //$item_pedido_produto_id->setMask('{nome}');
        $item_despesa_valor->setSize('71%');
        $item_despesa_quantidade->setSize('71%');
        //$observacao->setSize('100%');
        $filename->setSize('50%');
        $qtd_dias->setSize('100%');
        //$city_id->setSize('25%');

        // master fields
        //$this->form->addFields([new TLabel('Id:')],[$id],[new TLabel('Professor:', '#ff0000')],[$id_user]);
        $this->form->addFields([$id]);
        $this->form->addFields( [new TLabel('<i><b>Preencha corretamente os dados abaixo:</i></b>')]);
        //$this->form->addFields([new TLabel('Professor:')],[$id_user],[new TLabel('Nome:')], [$nome]);
        //$this->form->addFields([new TLabel('Nome:')], [$nome]);
        $this->form->addFields([new TLabel('Curso:')],[$curso],[new TLabel('Vem duas vezes por dia a Ituverava?')],[$viagem_dobro]);
        $this->form->addFields([new TLabel('Trecho:')],[$trecho_id],
        [new TLabel('Dias que ministrou aulas (dd/mm):')],[$qtd_aulas]);
        //,[new TLabel('Quantidade de dias:')], [$qtd_dias]);
        $this->form->addFields([new TLabel('Quantidade de dias:')],[$qtd_dias],[new TLabel('Observação:')], [$obs]);
        //$this->form->addFields([new TLabel('Observação')], [$observacao], [new TLabel('Anexar arquivos')], [$filename]);
        //$this->form->addFields([new TLabel('Observação')], [$observacao]);
        //$this->form->addFields([new TLabel('teste')], [$city_id]);
        //$this->form->addFields([new TLabel('Quantidade')], [$qtd_dias]);
        //$this->form->addFields([new TLabel('Arquivos')], [$arquivos]);
        //$this->form->addFields([new TLabel('Usuário')], [$system_user_id]);

        // detail fields
        $this->form->addContent( ['<h4><b>Itens</b></h4><hr>'] );
        //$this->form->addContent([new TFormSeparator('Itens', '#333333', '18', '#eeeeee')]);
        $this->form->addFields([new TLabel('Categoria:')],[$item_despesa_item_tipo],[new TLabel('')]);
        $this->form->addFields([new TLabel('Data:')],[$item_despesa_data_despesa], [new TLabel('')]);
        $this->form->addFields([new TLabel('Valor:')],[$item_despesa_valor],[new TLabel('')]);
        $this->form->addFields([new TLabel('Quantidade:')],[$item_despesa_quantidade],[new TLabel('')]);
        $this->form->addFields([$item_despesa_id]);

        $item_despesa_valor->setTip('Para combustível, coloque o valor do litro.');

        // add button
        $add_item_despesa = new TButton('add_item_despesa');
        $add_item_despesa->setAction(new TAction(array($this, 'onAddItemDespesa')), 'Adicionar');
        $add_item_despesa->setImage('fa:plus #51c249');
        $this->form->addFields([$add_item_despesa]);

        // detail datagrid
        $this->item_despesa_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_despesa_list->style = 'width:100%';
        $this->item_despesa_list->class .= ' table-bordered';
        $this->item_despesa_list->disableDefaultClick();
        $this->item_despesa_list->addQuickColumn('', 'edit', 'left', 50);
        $this->item_despesa_list->addQuickColumn('', 'delete', 'left', 50);

        $col_item_tipo       = $this->item_despesa_list->addQuickColumn('Categoria', 'item_despesa_item_tipo', 'left');
        $col_data        = $this->item_despesa_list->addQuickColumn('Data', 'item_despesa_data_despesa', 'left');
        $col_valor             = $this->item_despesa_list->addQuickColumn('Valor', 'item_despesa_valor', 'left');
        $col_quantidade             = $this->item_despesa_list->addQuickColumn('Quantidade', 'item_despesa_quantidade', 'left');
        $col_total             = $this->item_despesa_list->addQuickColumn('Total', '= {item_despesa_quantidade} * {item_despesa_valor}', 'left');

        $col_valor->setTransformer(function($value, $object, $row){
        	if (!$value)
        	{
        		$value = 0;
        	}
        	return "R$ " . number_format($value, 3, ",", ".");
        });
        
        $col_quantidade->setTransformer(function($value, $object, $row){
        	if (!$value)
        	{
        		$value = 0;
        	}
        	return number_format($value, 2, ",", ".");
        });

        $col_data->setTransformer(array($this, 'formatDate'));
        
        $col_total->setTotalFunction( function($values) { 
            return array_sum((array) $values);
        }); 
        
        $this->item_despesa_list->createModel();
        
        $col_total->setTransformer(function($value, $object, $row) {
            if (!$value)
            {
                $value = 0;
            }
            return "R$ " . number_format($value, 2, ",", ".");
        });
        
        $this->form->addContent([$this->item_despesa_list]);

        $this->form->addFields([new TLabel('Anexar comprovantes:')], [$filename]);

        $this->form->addFields( [new TLabel('<i>Caso precise anexar mais algum comprovante posteriormente, o mesmo deverá <u>anexar todos os outros comprovantes novamente.</u></i>')]);

        $this->form->addFields( [new TLabel('<i>Após o preenchimento, deverão ser entregues recibos e notas fiscais para análise e arquivamento. </i>')]);
        
        // create the form actions
        //$this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'far:save')->addStyleClass('btn-primary');
        //$this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save red');
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        //$this->form->addAction(('Nova Despesa'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        //$this->form->addAction(('Listar Despesas Realizadas'),  new TAction(array('DespesaList','onReload')), 'fa:list blue');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(new TXMLBreadCrumb('menu.xml',  'DespesaProfessorList'));
        $container->add(new TAlert('info', 'Atenção: O lançamento das despesas de viagens (do mês atual) deverão ser realizadas até o dia 10 do mês seguinte. Por exemplo: despesas do mês de fevereiro deverão ser lançadas até o dia 10 de março. '));
        //$container->add(new TXMLBreadCrumb('menu.xml', 'DespesaList'));
        $container->add($this->form);
        
        parent::add($container);

    }

    public function formatDate($date, $object)
        {
            $dt = new DateTime($date);
            return $dt->format('d/m/Y');
        }  

    /**
     * Adiciona item ao pedido
     * @param $param Request
     */
    public function onAddItemDespesa( $param )
    {
        try
        {
            $data = $this->form->getData();

            if(!$data->item_despesa_item_tipo)
            {
                throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', 'Categoria'));
            }

            if (! $data->item_despesa_data_despesa)
                throw new Exception('O campo Data é obrigatório.');

            if (! $data->item_despesa_valor)
                throw new Exception('O campo Valor é obrigatório.');

            if (! $data->item_despesa_quantidade)
                throw new Exception('O campo Quantidade é obrigatório.');
            
            $dia_atual = date("d");
            $mes_atual = date("m");

            $mes_despesa = date("m", strtotime($data->item_despesa_data_despesa));
            $mes_anterior = date('m', strtotime('-1 months', strtotime(date('Y-m-d'))));

            if ($mes_despesa == $mes_atual OR $mes_despesa == $mes_anterior ){

            		if($mes_despesa == $mes_anterior && $dia_atual <= 10 ){

            			$item_despesa_items = TSession::getValue('item_despesa_items');
			            $key = !empty($data->item_despesa_id) ? $data->item_despesa_id : uniqid();
			            
			            $fields = []; 
			            $fields['item_despesa_item_tipo'] = $data->item_despesa_item_tipo;
			            $fields['item_despesa_data_despesa'] = $data->item_despesa_data_despesa;
			            $fields['item_despesa_valor']      = $data->item_despesa_valor;
			            $fields['item_despesa_quantidade']      = $data->item_despesa_quantidade;

			            $item_despesa_items[ $key ]        = $fields;
			            
			            TSession::setValue('item_despesa_items', $item_despesa_items);

			            // limpa os campos do item do pedido
			            $data->item_despesa_item_tipo = '';
			            $data->item_despesa_data_despesa = '';
			            $data->item_despesa_valor = '';
			            $data->item_despesa_quantidade = '';
			            $data->item_despesa_id = '';
			            
			            $this->form->setData($data);
			            $this->onReload( $param );
            		}
            		elseif($mes_despesa == $mes_atual){

            			$item_despesa_items = TSession::getValue('item_despesa_items');
			            $key = !empty($data->item_despesa_id) ? $data->item_despesa_id : uniqid();
			            
			            $fields = []; 
			            $fields['item_despesa_item_tipo'] = $data->item_despesa_item_tipo;
			            $fields['item_despesa_data_despesa'] = $data->item_despesa_data_despesa;
			            $fields['item_despesa_valor']      = $data->item_despesa_valor;
			            $fields['item_despesa_quantidade']      = $data->item_despesa_quantidade;

			            $item_despesa_items[ $key ]        = $fields;
			            
			            TSession::setValue('item_despesa_items', $item_despesa_items);

			            // limpa os campos do item do pedido
			            $data->item_despesa_item_tipo = '';
			            $data->item_despesa_data_despesa = '';
			            $data->item_despesa_valor = '';
			            $data->item_despesa_quantidade = '';
			            $data->item_despesa_id = '';
			            
			            $this->form->setData($data);
			            $this->onReload( $param );
            		}
            		else{
            			throw new Exception(('A data da despesa não pode ultrapassar o dia 10 do mês seguinte. Em caso de dúvidas, entre em contato com o departamento contábil. ' ));
            		}
            }
            else{
            	throw new Exception(('Data de despesa não permitida. ' ));
            }
            
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Recarrega tudo
     * @param $param Request
     */
    public function onReload($params = null)
    {
        $this->loaded = TRUE;
        $this->onReloadDespesaItemDespesa($params);
    }
    
    /**
     * Recarrega itens da despesa
     * @param $param Request
     */
    public function onReloadDespesaItemDespesa( $param )
    {
        $items = TSession::getValue('item_despesa_items'); 

        $this->item_despesa_list->clear(); 

        if($items) 
        { 
            $cont = 1; 
            foreach ($items as $key => $item) 
            {
                $rowItem = new StdClass;

                $action_del = new TAction(array($this, 'onDeleteItemDespesa')); 
                $action_del->setParameter('item_despesa_id_row_id', $key);   

                $action_edi = new TAction(array($this, 'onEditItemDespesa'));  
                $action_edi->setParameter('item_despesa_id_row_id', $key);  

                $button_del = new TButton('delete_item_despesa'.$cont);
                $button_del->class = 'btn btn-default btn-sm';
                $button_del->setAction($action_del, '');
                $button_del->setImage('far:trash-alt'); 
                $button_del->setFormName($this->form->getName());

                $button_edi = new TButton('edit_item_despesa'.$cont);
                $button_edi->class = 'btn btn-default btn-sm';
                $button_edi->setAction($action_edi, '');
                $button_edi->setImage('bs:edit');
                $button_edi->setFormName($this->form->getName());

                $rowItem->edit   = $button_edi;
                $rowItem->delete = $button_del;
                
                //$rowItem->item_despesa_item_tipo = '';
                $rowItem->item_despesa_item_tipo = isset($item['item_despesa_item_tipo']) ? $item['item_despesa_item_tipo'] : '';
                $rowItem->item_despesa_data_despesa = isset($item['item_despesa_data_despesa']) ? $item['item_despesa_data_despesa'] : '';
                
                /**if (isset($item['item_despesa_item_tipo']) && $item['item_despesa_item_tipo'])
                {
                    TTransaction::open('intranet_ad');
                    $produto = Produto::find($item['item_pedido_produto_id']);
                    $rowItem->item_pedido_produto_id = $produto->render('{nome}');
                    TTransaction::close();
                }*/
                
                $rowItem->item_despesa_valor      = isset($item['item_despesa_valor']) ? $item['item_despesa_valor'] : '';
                $rowItem->item_despesa_quantidade = isset($item['item_despesa_quantidade']) ? $item['item_despesa_quantidade'] : '';
                

                $this->item_despesa_list->addItem($rowItem);
                $cont ++;
            } 
        } 
    }

    /**
     * Edita item da despesa
     * @param $param Request
     */
    public function onEditItemDespesa( $param )
    {
        $data = $this->form->getData();

        // read session items
        $items = TSession::getValue('item_despesa_items');

        // get the session item
        $item = $items[$param['item_despesa_id_row_id']];

        $data->item_despesa_item_tipo = $item['item_despesa_item_tipo'];
        $data->item_despesa_data_despesa = $item['item_despesa_data_despesa'];
        $data->item_despesa_quantidade = $item['item_despesa_quantidade'];
        $data->item_despesa_valor      = $item['item_despesa_valor'];
        $data->item_despesa_id         = $param['item_despesa_id_row_id'];
        
        // fill product fields
        $this->form->setData( $data );

        $this->onReload( $param );
    }

    /**
     * Exclui item da despesa
     * @param $param Request
     */
    public function onDeleteItemDespesa( $param )
    {
        $data = $this->form->getData();

        $data->item_despesa_item_tipo = '';
        $data->item_despesa_data_despesa = '';
        $data->item_despesa_quantidade = '';
        $data->item_despesa_valor      = '';
        $this->form->setData( $data );

        // read session items
        $items = TSession::getValue('item_despesa_items');

        // delete the item from session
        unset($items[$param['item_despesa_id_row_id']]);
        TSession::setValue('item_despesa_items', $items);
        
        $this->onReload( $param );
    }

    /**
     * Limpa formulário
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear();
        TSession::setValue('item_despesa_items', null);
        $this->onReload();
    }

    public static function onComplete($param)
    {
        new TMessage('info', 'Arquivo enviado com sucesso: '.$param['filename']);
        
        // refresh photo_frame
        TScript::create("$('#filename').html('')");
        TScript::create("$('#filename').append(\"<img style='width:100%' src='tmp/{$param['filename']}'>\");");
    }

    /**
     * Salva despesa
     * @param $param Request
     */
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $object = new DespesaProfessor; 
            $object->fromArray( (array) $data);
            $object->system_user_id = TSession:: getValue ('userid');//pega o usuário que esta logado
            //$object->situacao = "Em análise";
            $object->situacao = "Aberto";
            $object->unidade = TSession::getValue('userunitid');
            $object->nome = $logged->name;

			$verifica = TSession::getValue('item_despesa_items');

            if (! $verifica)
                throw new Exception('É necessário adicionar os itens a sua despesa. Escolha a categoria, data, valor, quantidade e depois clique no botão "Adicionar".');													 
            //$object->system_user_id = $logged->id;
            /**
            if(isset($data->filename)){

            $zip = new ZipArchive();
            //$usuarioLogado = $logged-> id;
            $today = date("Ymd");
            $nomeArquivo = "arquivos/arquivo"."_$today_".time().'.zip';
            $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
            
            foreach ($data-> filename as $arq)
            {
                $source_file   = 'tmp/'.$arq;
            //    $target_file   = 'images/' . $arq;
                
                if (file_exists($source_file))
                {

                    $zip->addFile(  'tmp/'.$arq , "$arq" );
                    
                }
            }
            $zip->close();

            $object->filename = $nomeArquivo;
            }
            */

            if($data->id)
            {   
                        
                if($data->filename )
                {
                        
                    $dp = new DespesaProfessor($data->id);
                    $arqBanco = $dp->filename;

                    $contador = count($data->filename);
                    $i = $contador-1;

                    $teste = $data->filename[$i];

                    if($teste != $arqBanco)
                    {
                    
                    $zip = new ZipArchive();
                    $today = date("Ymd");
                    $nomeArquivo = "arquivos/"."despesa_professor"."_$today_".time().'.zip';
                    $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                    
                    foreach ($data->filename as $arq)
                    {
                        $source_file   = 'tmp/'.$arq;
                        
                        if (file_exists($source_file))
                        {

                            $zip->addFile(  'tmp/'.$arq , "$arq" );
                            
                        }
                    }
                    $zip->close();

                    $object->filename = $nomeArquivo;
                    }

                }
            }
            else //quando é um novo registro
            {
                $zip = new ZipArchive();
                $today = date("Ymd");
                $nomeArquivo = "arquivos/"."despesa_professor"."_$today_".time().'.zip';
                $zip->open( "$nomeArquivo" , ZipArchive::CREATE);
                
                foreach ($data-> filename as $arq)
                {
                    $source_file   = 'tmp/'.$arq;
                    
                    if (file_exists($source_file))
                    {

                        $zip->addFile(  'tmp/'.$arq , "$arq" );
                        
                    }
                }
                $zip->close();

                $object->filename = $nomeArquivo;
            }

            $object->store(); 
            
            $this->storeItems('DespesaProfessorItem', 'despesa_id', $object, 'item_despesa',
                function($masterObject, $detailObject) { 
                    $masterObject->valor_total += ($detailObject->quantidade * $detailObject->valor);
                    
                    //$masterObject->situacao = "EM ANÁLISE";
            });

	        $teste = $object->trecho_id;
            $testes = new TrechoProfessor($teste, FALSE);
            $td = $testes->distancia;

            //$text12 = new TTextDisplay(($despesa->viagem_dobro == 'Sim') ? $despesa->trecho->distancia * 4 : $despesa->trecho->distancia * 2, '#333333', '12px', '');
            $object->total_dia = $object->viagem_dobro == 'Sim' ? $td * 4 : $td * 2;
            //$object->total_dia = ($object->viagem_dobro == 'Sim') ? $td * 4 : $td * 2;
            //$object->total_dia = $td * 2;

            //var_dump($object->total_dia);
            //die;

            $object->total_percorrido += ($object->total_dia * $object->qtd_dias);
            $object->custo_medio += ($object->valor_total / $object->total_percorrido);
            

            $object->store();
            $data->id = $object->id; 
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('DespesaProfessorList', 'onReload', $param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }   

    /**
     * Edita formulário
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                TTransaction::open('Felabs_DB');
                
                $object = new DespesaProfessor($key); 
                $this->loadItems('DespesaProfessorItem', 'despesa_id', $object, 'item_despesa');
                 
                $this->form->setData($object); 
                $this->onReload();
                TTransaction::close(); 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }  
    /**
     * Exibe a página
     * @param $param Request
     */
    public function show() 
    { 
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') ) 
        { 
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
} 


