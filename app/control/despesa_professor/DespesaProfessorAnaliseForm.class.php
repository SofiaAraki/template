
<?php
/**
 * DespesaProfessorForm Form
 * @author  <your name here>
 */
class DespesaProfessorAnaliseForm extends TPage
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
        $id = new TEntry('id');
        $curso = new TEntry('curso');
        //$viagem_dobro = new TRadioGroup('viagem_dobro');
        $trecho_id = new TDBCombo('trecho_id', 'Felabs_DB', 'TrechoProfessor', 'id', 'nome_trecho');
        //$trecho_id = new TEntry('trecho_id');
        $qtd_aulas = new TEntry('qtd_aulas');
        //$qtd_aulas   = new TCheckGroup('qtd_aulas');
        $qtd_dias = new TEntry('qtd_dias');
        //$qtd_dias   = new TNumeric('qtd_dias', 2, ',', '.');
        $obs = new TText('obs');
        //$filename = new TMultiFile('filename');
        $unidade = new THidden('unidade');
        $nome = new TEntry('nome');
        $data_reg = new TDateTime('data_reg');
        $total_dia = new TNumeric('total_dia', '2', ',', '.');
        $total_percorrido = new TNumeric('total_percorrido', '2', ',', '.');
        $custo_medio = new TNumeric('custo_medio', '2', ',', '.');
        $valor_total = new TNumeric('valor_total', '2', ',', '.');
        $situacao = new TCombo('situacao');
        $filename = new TButton('filename');
        $obs_analise = new TText('obs_analise');
        $system_user_id = new THidden('system_user_id');

        $filename->setImage('fas:cloud-download-alt');
        $filename->setAction(new TAction(array($this, 'onDownloadMaster')), 'Download');

        $itens_situacao = array();
        $itens_situacao['Aberto'] ='Aberto';
        $itens_situacao['Em análise'] ='Em análise';
        $itens_situacao['Aguardando aprovação'] ='Verificação OK';
        $itens_situacao['Indeferido'] ='Indeferido';
        
        $situacao->addItems($itens_situacao);
        /*
        $radio1 = array();
        $radio1['Sim'] ='Sim';
        $radio1['Não'] ='Não';

        $viagem_dobro->setLayout('horizontal');
        $viagem_dobro->addItems($radio1);*/

        //$trecho_id->enableSearch();
        /*
        $qtd_aulas->setLayout('horizontal');
        $qtd_aulas->setBreakItems(7);

        $items = array();
        for ($n=1; $n<=31; $n++)
        {
            $items[$n] = $n;
        }

        $qtd_aulas->addItems($items);

        foreach ($qtd_aulas->getLabels() as $key => $label)
        {
            $label->setTip("Dia $key");
            $label->setSize(40);
        }*/

        // detail fields
        $item_despesa_id = new THidden('item_despesa_id');
        $item_despesa_item_tipo = new TCombo('item_despesa_item_tipo');
        $item_despesa_data_despesa = new TDate('item_despesa_data_despesa');
        $item_despesa_valor = new TNumeric('item_despesa_valor', '3', ',', '.' );
        $item_despesa_quantidade = new TNumeric('item_despesa_quantidade', '2', ',', '.' );
        $item_despesa_anexo = new TFile('item_despesa_anexo');

		$combo1 = array();
        $combo1['Alimentação'] ='Alimentação';
        $combo1['Combustível - Gasolina'] ='Combustível - Gasolina';
        $combo1['Combustível - Etanol'] ='Combustível - Etanol';
        $combo1['Combustível - Diesel'] ='Combustível - Diesel';
        $combo1['Pedágio'] ='Pedágio';
        
        $item_despesa_item_tipo->addItems($combo1);
        
        $item_despesa_item_tipo->enableSearch();

        //$filename->setCompleteAction(new TAction(array($this, 'onComplete')));
        //$filename->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'txt'] );

        //$qtd_aulas->placeholder = ('Ex: 04/02, 06/02, 08/02 ');
        //$qtd_dias->placeholder = ('Ex: 3 ');
        
        $data_reg->setMask('dd/mm/yyyy hh:ii');
        $data_reg->setDatabaseMask('yyyy-mm-dd hh:ii');

        //$data_reg->setMask('mm/yyyy');
        //$data_reg->setDatabaseMask('yyyy-mm-dd');


        $qtd_dias->setMask('999');

        $nome->setEditable(FALSE);
        $curso->setEditable(FALSE);
        //$viagem_dobro->setEditable(FALSE);
        $trecho_id->setEditable(FALSE);
        $qtd_aulas->setEditable(FALSE);
        $qtd_dias->setEditable(FALSE);
        $obs->setEditable(FALSE);
        $data_reg->setEditable(FALSE);
        $total_dia->setEditable(FALSE);
        $total_percorrido->setEditable(FALSE);
        $custo_medio->setEditable(FALSE);
        $valor_total->setEditable(FALSE);

		//$id_user->addValidation('Professor', new TRequiredValidator());
        $curso->addValidation('"Curso"', new TRequiredValidator());
        //$viagem_dobro->addValidation('"Vem duas vezes por dia a Ituverava"', new TRequiredValidator());
        $trecho_id->addValidation('"Trecho"', new TRequiredValidator());
        $qtd_aulas->addValidation('"Dias que ministrou aulas"', new TRequiredValidator());
        $qtd_dias->addValidation('"Quantidade de dias"', new TRequiredValidator());
        //$filename->addValidation('"Anexar comprovantes"', new TRequiredValidator());

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

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        $nome->setSize('100%');
        $curso->setSize('100%');
        //$viagem_dobro->setSize('100%');
        $trecho_id->setSize('100%');
        $qtd_aulas->setSize('100%');
        $qtd_dias->setSize('85%');

        // master fields
        //$this->form->addFields([new TLabel('Id:')],[$id],[new TLabel('Professor:', '#ff0000')],[$id_user]);
        $this->form->addFields( [new TFormSeparator('<i>Detalhes da despesa</i>')] );
        $this->form->addFields( [$system_user_id] );
        $this->form->addFields( [ new TLabel('ID:') ],[ $id ],[new TLabel('Professor:')], [$nome] );
        //$this->form->addFields( [new TLabel('<i><b>Preencha corretamente os dados abaixo:</i></b>')]);
        //$this->form->addFields([new TLabel('Professor:')],[$id_user],[new TLabel('Nome:')], [$nome]);
        //$this->form->addFields([new TLabel('Professor:')], [$nome], [new TLabel('')]);
        //$this->form->addFields([new TLabel('Curso:')],[$curso],[new TLabel('Vem duas vezes por dia a Ituverava?')],[$viagem_dobro]);
        $this->form->addFields([new TLabel('Curso(s):')],[$curso]);
        $this->form->addFields([new TLabel('Trecho:')],[$trecho_id],
        [new TLabel('Dias que ministrou aulas:')],[$qtd_aulas]);
        //,[new TLabel('Quantidade de dias:')], [$qtd_dias]);
        $this->form->addFields([new TLabel('Quantidade de dias:')], [$qtd_dias], [new TLabel('Total por dia (Km):')], [$total_dia],[new TLabel('Total percorrido (Km):')], [$total_percorrido]);
        $this->form->addFields([new TLabel('Custo médio Km rodado (R$):')], [$custo_medio], [new TLabel('Mês/Ano:')],[$data_reg], [new TLabel('Total de despesas (R$):')], [$valor_total]);
        $this->form->addFields([new TLabel('Observação:')], [$obs],[new TLabel('')]);
        //$this->form->addFields([new TLabel('teste')], [$city_id]);
        //$this->form->addFields([new TLabel('Quantidade')], [$qtd_dias]);
        //$this->form->addFields([new TLabel('Arquivos')], [$arquivos]);
        //$this->form->addFields([new TLabel('Usuário')], [$system_user_id]);

        // detail fields
        //$this->form->addContent( ['<h4><b>Itens</b></h4><hr>'] );
        $this->form->addFields( [new TFormSeparator('<i>Itens</i>')] );
        //$this->form->addContent([new TFormSeparator('Itens', '#333333', '18', '#eeeeee')]);
    /**    $this->form->addFields([new TLabel('Categoria:')],[$item_despesa_item_tipo],[new TLabel('')]);
        $this->form->addFields([new TLabel('Data:')],[$item_despesa_data_despesa], [new TLabel('')]);
        $this->form->addFields([new TLabel('Valor:')],[$item_despesa_valor],[new TLabel('')]);
        $this->form->addFields([new TLabel('Quantidade:')],[$item_despesa_quantidade],[new TLabel('')]);
        $this->form->addFields([$item_despesa_id]);

        // add button
        $add_item_despesa = new TButton('add_item_despesa');
        $add_item_despesa->setAction(new TAction(array($this, 'onAddItemDespesa')), 'Adicionar');
        $add_item_despesa->setImage('fa:plus #51c249');
        $this->form->addFields([$add_item_despesa]);*/

        // detail datagrid
        $this->item_despesa_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->item_despesa_list->style = 'width:100%';
        $this->item_despesa_list->class .= ' table-bordered';
        $this->item_despesa_list->disableDefaultClick();
        //$this->item_despesa_list->addQuickColumn('', 'edit', 'left', 50);
        //$this->item_despesa_list->addQuickColumn('', 'delete', 'left', 50);
        $this->item_despesa_list->addQuickColumn('', 'download', 'left', 50);

        $this->item_despesa_list->enablePopover('Comprovante', "<embed style='width: 100%; height:calc(100% - 10px)' src='{item_despesa_anexo}'>");

        $col_item_tipo       = $this->item_despesa_list->addQuickColumn('Categoria', 'item_despesa_item_tipo', 'left');
        $col_data        = $this->item_despesa_list->addQuickColumn('Data', 'item_despesa_data_despesa', 'left');
        $col_valor             = $this->item_despesa_list->addQuickColumn('Valor', 'item_despesa_valor', 'left');
        $col_quantidade             = $this->item_despesa_list->addQuickColumn('Quantidade', 'item_despesa_quantidade', 'left');
        $col_anexo             = $this->item_despesa_list->addQuickColumn('Comprovante', 'item_despesa_anexo', 'left');
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

        $this->form->addFields([new TLabel('Comprovante(s) anexo(s):')], [$filename]);

        $this->form->addFields( [new TFormSeparator('<i>Análise dos dados</i>')] );

        $this->form->addFields([new TLabel('Situação:')], [$situacao], [new TLabel('')]);
        $this->form->addFields([new TLabel('Observação:')], [$obs_analise], [new TLabel('')]);
        
        // create the form actions
        //$this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'far:save')->addStyleClass('btn-primary');
        //$this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save red');
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        
        //$this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        //$this->form->addAction(('Nova Despesa'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        //$this->form->addAction(('Listar Despesas Realizadas'),  new TAction(array('DespesaList','onReload')), 'fa:list blue');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(new TXMLBreadCrumb('menu.xml',  'DespesaProfessorList'));
        //$container->add(new TXMLBreadCrumb('menu.xml', 'DespesaList'));
        $container->add($this->form);
        
        parent::add($container);

    }

    public function onDownloadMaster($param)
    {
        //try
        //{
        $data = $this->form->getData();
            
        $id = $param['id'];  // get the parameter $key

        TTransaction::open('Felabs_DB'); // open a transaction

        $object = new DespesaProfessor($id); // instantiates the Active Record

        TTransaction::close(); // close the transaction

        if(!empty($object->filename)){              
                    if (strtolower(substr($object->filename, -4)) == 'html')
                    {
                        $win = TWindow::create( $object->filename, 0.8, 0.8 );
                        $win->add( file_get_contents( "arquivos/".$object->filename ) );
                        $win->show();

                    }
                    else
                    {
                        TPage::openFile($object->filename);
                    }
                    $this->form->setData( $this->form->getData() ); // keep form data
                    TTransaction::rollback();

                    new TMessage('info', 'Caso não consiga fazer o download, habilite pop-ups em seu navegador');
                }
                else
                {
                    new TMessage('info', 'Esta despesa não possui anexos'); 
                }
        $this->form->setData( $data );

        $this->onReload( $param );
                
            
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

                $action_download = new TAction(array($this, 'onDownLoadItemDespesa'));  
                $action_download->setParameter('item_despesa_id_row_id', $key);  

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

                $button_download = new TButton('download_item_despesa'.$cont);
                $button_download->class = 'btn btn-default btn-sm';
                $button_download->setAction($action_download, '');
                $button_download->setImage('fa:download');
                $button_download->setFormName($this->form->getName());

                $rowItem->edit   = $button_edi;
                $rowItem->delete = $button_del;
                $rowItem->download = $button_download;
                
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
                $rowItem->item_despesa_anexo = isset($item['item_despesa_anexo']) ? $item['item_despesa_anexo'] : '';
                

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
     * Exclui item da despesa
     * @param $param Request
     */
    public function onDownLoadItemDespesa( $param )
    {
        $data = $this->form->getData();

        // read session items
        $items = TSession::getValue('item_despesa_items');

        // get the session item
        $item = $items[$param['item_despesa_id_row_id']];

        TTransaction::open('Felabs_DB');

        $object = new DespesaProfessorItem($item['item_despesa_id']);

        TTransaction::close();

        if(!empty($object->anexo))
                {              
                    if (strtolower(substr($object->anexo, -4)) == 'html')
                    {
                        $win = TWindow::create( $object->anexo, 0.8, 0.8 );
                        $win->add( file_get_contents( "arquivos/".$object->anexo ) );
                        $win->show();

                    }
                    else
                    {
                        TPage::openFile($object->anexo);
                    }
                    $this->form->setData( $this->form->getData() ); // keep form data
                    TTransaction::rollback();

                    new TMessage('info', 'Caso não consiga fazer o download, habilite pop-ups em seu navegador'); 

                }
                else
                {
                    new TMessage('info', 'Esta despesa não possui anexos'); 
                }

       
        
        // fill product fields
        $this->form->setData( $data );

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
            $prefs  = SystemPreference::getAllPreferences();
            
            $this->form->validate();
            $data = $this->form->getData();
            
            $object = new DespesaProfessor; 
            $object->fromArray( (array) $data);
            //$object->data_reg = TDate::date2us($object->data_reg);
            //$object->system_user_id = TSession:: getValue ('userid');//pega o usuário que esta logado
            //$object->situacao = "EM ANÁLISE";
            //$object->unidade = TSession::getValue('userunitid');
            //$object->nome = $logged->name;

            $object->id_analise = $logged->id;

            if($object->situacao == 'Indeferido')
            {
                $object->data_final = date('Y-m-d H:i:s');
            }

            else
            {
                $object->data_final = '';
            }

            //email
            $usuario = $object->system_user_id;
            $usuarios = new SystemUser($usuario, FALSE);
            $emailusuario = $usuarios->email;

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
            }*/

            $object->store(); 
            /**
            $this->storeItems('DespesaProfessorItem', 'despesa_id', $object, 'item_despesa',
                function($masterObject, $detailObject) { 
                    $masterObject->valor_total += ($detailObject->quantidade * $detailObject->valor);
                    
                    //$masterObject->situacao = "EM ANÁLISE";
            });*/
            /**
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
            $object->custo_medio += ($object->valor_total / $object->total_percorrido);*/
            

            $object->store();
            $data->id = $object->id; 
            $this->form->setData($data);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('DespesaProfessorAnaliseList', 'onReload', $param);

            //email aluno/professor
            $mail = new TMail;
            $mail->setFrom($prefs['mail_from'], 'Área do Professor - FEAcadêmico');
            $mail->setSubject('Despesas - Viagem');
            $mail->setTextBody("Prezado(a) $usuarios->name, sua solicitação de Despesas de viagem foi avaliada e a situação foi alterada. Acompanhe a situação através da Área do Professor - FEAcadêmico."."\n". 'Esta é uma mensagem automática. Solicitamos, por favor, não responder este e-mail.');  

            $mail->addAddress($emailusuario);          
  
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($prefs['smtp_host'], $prefs['smtp_port']);
            $mail->SetSmtpUser($prefs['smtp_user'], $prefs['smtp_pass']);
            $mail->send();

            $id_notif=$usuario;

            //$notif = $object-> id_user;
            SystemNotification::register(
                                        $id_notif,
                                        'Novo status de Despesas de viagem definida',
                                        'Um novo estado foi definido para sua Despesa de viagem, verifique.',
                                        'class=DespesaProfessorList',
                                        'Ver Cadastro',
                                        'far fa-list-alt green'
                                        );
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
                //$object->qtd_aulas = explode(',', $object->qtd_aulas);
                
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


