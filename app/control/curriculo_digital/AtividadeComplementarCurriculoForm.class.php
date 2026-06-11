<?php

class AtividadeComplementarCurriculoForm extends TPage
{    
    protected $datagrid; 
    protected $formGrid;
    

    public function __construct()
    {
        parent::__construct();
        
        
        $curriculo = TSession::getValue('dados_curriculo'); 
        $curso = TSession::getValue('dados_curso');  
            
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->generateHiddenFields();
        $this->datagrid->disableDefaultClick();

        
        $panel = new TPanelGroup("Atividades Complementares : $curso->nome_curso_diploma - Currículo: $curriculo->codigo_curriculo");
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        
        
        $label = '<br><p style="font-size: 15px;">* A inclusão do limite de carga horária para uma categoria é opcional. Quando informado,
        estabelece o máximo de horas a serem consideradas para fins de integralização curricular.</p>
        <p style="font-size: 15px;">* A inclusão do limite de carga horária para uma atividade complementar é obrigatória e estabelece o 
        máximo de horas a serem consideradas para fins de integralização curricular.</p>'; 
        
        $panel->add($label);       
     
     
        // form grid
        $this->formGrid = new TForm('form_AtividadeComplementarCurriculo');
        $this->formGrid->add($panel);
        

        $checkAll = new TElement('input');
        $checkAll->type = 'checkbox';
        $checkAll->title = 'Marcar / Desmarcar todos';
        $checkAll->onclick = "$('input:checkbox').not(this).prop('checked',this.checked); if (! $(this).is(':checked') ){ $('input:text').val('');}";

        
        // creates the datagrid columns
        $column_check = new TDataGridColumn('check', "$checkAll Marcar/Desmarcar Todos", 'center', '70');  
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_cod_curriculo = new TDataGridColumn('cod_curriculo', 'Cód. no Currículo', 'center');
        $column_nome = new TDataGridColumn('nome', '', 'left');
        $column_carga_horaria = new TDataGridColumn('carga_horaria', 'Limite de CH', 'center');
        
        
        $column_nome->setTransformer([ $this, 'formatRow' ] );
        
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_check);
        $this->datagrid->addColumn($column_id)->setVisibility(false);
        $this->datagrid->addColumn($column_cod_curriculo);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_carga_horaria);

            
        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the action button
        $button1 = TButton::create('salvar', [$this, 'onSave'], 'Salvar', 'fa:save');
        $button1->class = 'btn btn-sm btn-primary';
        $button2 = TButton::create('voltar', ['CurriculoList','onReload'], 'Voltar', 'fas:arrow-alt-circle-left blue');
        $this->formGrid->addField($button1);
        $this->formGrid->addField($button2);
        
        
        $hbox = new THBox;
        $hbox->add($button1);
        $hbox->add($button2);
        $panel->addFooter($hbox);
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->formGrid);
        
        parent::add($container);
    }
    
    
    public function formatRow($value, $object, $row)
    {        
        if ($object instanceof AtividadeComplementarCategoria)
        {
            $row->style = "background: #C0C0C0; font-size: 18px; font-weight: bold";
            return $value;
        }    
                
        if ($object instanceof AtividadeComplementarCadastro)
        {
            $row->style = "background: #DCDCDC";
            return $value;
        }
    }
    
    
    public function onReload($param = NULL)
    {
        try
        {                       
            TTransaction::open('Felabs_DB');
          
            $curriculo = TSession::getValue('dados_curriculo');  
            $curso = TSession::getValue('dados_curso');
                
            //Filtra as categorias que tenham atividades cadastradas     
            $repository_categoria = new TRepository('AtividadeComplementarCategoria');
            
            $criteria_categoria = new TCriteria;
            $criteria_categoria->add(new TFilter('dados_curso_id', '=', $curso->id));
            $criteria_categoria->add(new TFilter('id', 'IN', '(SELECT categoria_id FROM atividade_complementar_cadastro WHERE categoria_id IS NOT NULL)'));
            $criteria_categoria->setProperty('order', 'id');
            
            $categorias = $repository_categoria->load($criteria_categoria);
            
            
            //Filtra as atividades complementares de cada categoria
            if($categorias)
            {                
                foreach($categorias as $categoria)
                {
                    $ids_categorias[] = $categoria->id;
                }
    
                $repository_atividade = new TRepository('AtividadeComplementarCadastro');
                
                $criteria_atividade = new TCriteria;
                $criteria_atividade->add(new TFilter('categoria_id', 'IN', $ids_categorias));
                $criteria_atividade->setProperty('order', 'id');
                
                $atividades = $repository_atividade->load($criteria_atividade);
            }

                        
            if ($categorias AND $atividades)
            {
                $this->datagrid->clear();
                
                
                //Ação que vai limpar a CH caso o check seja desmarcado
                $action = new TAction(array($this,'onVerificaCheck'));
                $string_action = $action->serialize(FALSE);

                
                //Categorias
                foreach ($categorias as $categoria)
                {
                    $categoria->check = new TCheckButton('categoria_'.$categoria->id);
                    $categoria->check->setIndexValue($categoria->id);
                    $categoria->check->setProperty('onChange', "__adianti_post_lookup('{$this->formGrid->getName()}', '{$string_action}', this)");
                    
                    $categoria->carga_horaria = new TEntry('ch_categoria_'.$categoria->id);
                    $categoria->carga_horaria->setNumericMask(2, '.', '', true);
                    
                    //O código da categoria no currículo é formado pelo próprio código do currículo + "-" + código da categoria 
                    $cod_categoria_curriculo = $curriculo->codigo_curriculo . "-" . $categoria->codigo;
                    
                    $categoria->cod_curriculo = $cod_categoria_curriculo;
                    
                    //Se tiver registro na tabela curriculo_atividade_categoria, traz preenchido na edição
                    $curriculo_atv_cat = CurriculoAtividadeCategoria::where('curriculo_id', '=', $curriculo->id)
                                                                    ->where('atividade_complementar_categoria_id', '=', $categoria->id)
                                                                    ->where('cod_categoria_curriculo', '=', $cod_categoria_curriculo)
                                                                    ->load();
                                                                    
                    if($curriculo_atv_cat)
                    {
                        $categoria->check->setValue($categoria->id); 
                        $categoria->carga_horaria->setValue($curriculo_atv_cat[0]->ch_categoria_hora_relogio);
                    }
                    
                    
                    $this->datagrid->addItem($categoria);
                    $this->formGrid->addField($categoria->check);
                    $this->formGrid->addField($categoria->carga_horaria);
                
                
                    //Atividades
                    foreach($atividades as $atividade)
                    {
                        //Se a atividade pertencer à categoria
                        if($atividade->categoria_id == $categoria->id)
                        {
                            $atividade->check = new TCheckButton('atividade_'.$atividade->id);
                            $atividade->check->setIndexValue($atividade->id);
                            $atividade->check->setProperty('onChange', "__adianti_post_lookup('{$this->formGrid->getName()}', '{$string_action}', this)");
                            
                            $atividade->carga_horaria = new TEntry('ch_atividade_'.$atividade->id);
                            $atividade->carga_horaria->setNumericMask(2, '.', '', true);
                    
                    
                            //O código da atividade no currículo é formado pelo próprio código do currículo + "-" + código da atividade
                            $cod_atividade_curriculo = $curriculo->codigo_curriculo . "-" . $atividade->codigo;    

                            $atividade->cod_curriculo = $cod_atividade_curriculo;
                    
                            //Se tiver registro na tabela curriculo_atividade_cadastro, traz preenchido na edição
                            $curriculo_atv_cad = CurriculoAtividadeCadastro::where('curriculo_id', '=', $curriculo->id)
                                                                           ->where('atividade_complementar_categoria_id', '=', $categoria->id)
                                                                           ->where('curriculo_atividade_categoria_id', '=', $curriculo_atv_cat[0]->id)
                                                                           ->where('atividade_complementar_cadastro_id', '=', $atividade->id)
                                                                           ->where('cod_atividade_curriculo', '=', $cod_atividade_curriculo)
                                                                           ->load();
                                                                           
                            if($curriculo_atv_cad)
                            {
                                $atividade->check->setValue($atividade->id); 
                                $atividade->carga_horaria->setValue($curriculo_atv_cad[0]->ch_atividade_hora_relogio);
                            }
                    
                    
                            $this->datagrid->addItem($atividade);
                            $this->formGrid->addField($atividade->check);
                            $this->formGrid->addField($atividade->carga_horaria);     
                        }
                    }
                }
            }
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function onVerificaCheck($param)
    {   
        //Nome do campo 
        $name = $param['_field_name'];
        $parts = explode('_', $name);
        
           
        //A parte [0] indica a classe e a parte [1] indica o id (categoria_ID ou atividade_ID)
        $check_classe = $parts[0];
        $check_id = $parts[1];
        
                    
        //Se desmarcar check, limpa o campo da carga horária correspondente caso esteja preenchido
        if(!$param["$name"])
        {
            if($check_classe == "categoria")
            {
                TEntry::clearField('form_AtividadeComplementarCurriculo', 'ch_categoria_'.$check_id);       
            }
            
            if($check_classe == "atividade")
            {
                TEntry::clearField('form_AtividadeComplementarCurriculo', 'ch_atividade_'.$check_id); 
            }
        }
    }
        
    
    public function onSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $data = $this->formGrid->getData();        
            $this->formGrid->setData($data);            

            
            $curriculo = TSession::getValue('dados_curriculo');             
            
            //Se o currículo já foi publicado uma vez, não permite alteração
            if($curriculo->data_primeira_publicacao <> NULL)
            {
                new TMessage('error','O registro não pode ser alterado, pois o currículo já foi publicado');
                return false;
            }
            
            
            //Após salvar, vai deletar as categorias que não persistem
            $old_categorias = CurriculoAtividadeCategoria::where('curriculo_id', '=', $curriculo->id)->load();
            
            
            //Após salvar, vai deletar as atividades que não persistem
            if($old_categorias)
            {
                foreach($old_categorias as $old_categoria)
                {
                    $ids_old_categorias[] = $old_categoria->id;    
                }
                
                $old_atividades = CurriculoAtividadeCadastro::where('curriculo_atividade_categoria_id', 'IN', $ids_old_categorias)->load();
            }
        
            
            $array_categorias = [];
            $array_atividades = [];   
            $i = 0;
                       
            //Percorre os campos do formulário  
            foreach ($this->formGrid->getFields() as $value => $field)
            {         
                if ($field instanceof TCheckButton)
                {
                    $parts = explode('_', $value);
                    
                    //A parte [0] indica a classe e a parte [1] indica o id (categoria_ID ou atividade_ID)
                    $check_classe = $parts[0];
                    $check_id = $parts[1];


                    //Se o check foi marcado, insere no array
                    if ($field->getValue() == $check_id)
                    {
                        if($check_classe == "categoria")
                        {
                            //Verifica se alguma atividade pertencente à categoria foi checada
                            $atividades = AtividadeComplementarCadastro::where('categoria_id', '=', $check_id)->load();
                            
                            foreach($atividades as $atividade)
                            {
                                $nome_campo_atividade = 'atividade_'.$atividade->id;
                                
                                if($data->$nome_campo_atividade)
                                {  
                                    $i++;  
                                }
                            }
                            
                            if($i == 0)
                            {
                                throw new Exception("Se uma determinada categoria foi selecionada, é necessário selecionar pelo menos uma atividade pertencente à ela");
                            }
                            
                            //Zera o contador para a próxima iteração
                            $i = 0;
                            
                            $array_categorias[$check_id]['curriculo_id'] = $curriculo->id; 
                            $array_categorias[$check_id]['atividade_complementar_categoria_id'] = $check_id; 
                            
                            foreach($this->datagrid->getItems() as $object)
                            {
                                if ($object instanceof AtividadeComplementarCategoria)
                                {
                                    if($object->id == $check_id)
                                    {
                                        $array_categorias[$check_id]['cod_categoria_curriculo'] = $object->cod_curriculo;    
                                    }
                                }    
                            }                     	      
                        }
                        
                        
                        if($check_classe == "atividade")
                        {
                            //Verifica se a categoria a qual a atividade pertence foi checada
                            $atividade = new AtividadeComplementarCadastro($check_id);
                            
                            $nome_campo_categoria = 'categoria_'.$atividade->categoria_id;

                            if($data->$nome_campo_categoria)
                            {
                                $array_atividades[$check_id]['curriculo_id'] = $curriculo->id;
                                $array_atividades[$check_id]['atividade_complementar_categoria_id'] = $atividade->categoria_id; 
                                $array_atividades[$check_id]['atividade_complementar_cadastro_id'] = $check_id; 
                                
                                foreach($this->datagrid->getItems() as $object)
                                {
                                    if ($object instanceof AtividadeComplementarCadastro)
                                    {
                                        if($object->id == $check_id)
                                        {
                                            $array_atividades[$check_id]['cod_atividade_curriculo'] = $object->cod_curriculo;    
                                        }
                                    }    
                                }        
                            }
                            else
                            {
                                throw new Exception("Se uma determinada atividade foi selecionada, é necessário selecionar também a categoria a qual ela pertence");
                            }                                                 	      
                        }
                    }    
                }
                   
                                
                if ($field instanceof TEntry)
                {
                    $parts = explode('_', $value);

                    //A parte [1] indica a classe e a parte [2] indica o id (ch_categoria_ID ou ch_atividade_ID)
                    $entry_classe = $parts[1];
                    $entry_id = $parts[2];
                    
                    if($entry_classe == "categoria")
                    {
                        //Verifica se check e entry pertencem a mesma categoria 
                        if($entry_id == $check_id)
                        {
                            //Se a carga horária da categoria foi preenchida, insere no array (CH da categoria não é obrigatória)
                            if($field->getValue())
                            {
                                $array_categorias[$entry_id]['ch_categoria_hora_relogio'] = $field->getValue();
                            }
                        }
                    }
                        
                        
                    if($entry_classe == "atividade")
                    {
                        //Verifica se o check da atividade foi marcado
                        $nome_campo_atividade = 'atividade_'.$check_id;

                        if($data->$nome_campo_atividade)
                        {           
                            //Verifica se check e entry pertencem a mesma atividade                  
                            if($entry_id == $check_id)
                            {
                                if($field->getValue())
                                {
                                    $array_atividades[$entry_id]['ch_atividade_hora_relogio'] = $field->getValue();
                                }
                                else
                                {
                                    throw new Exception("É necessário preencher o limite de carga horária de cada atividade lançada no currículo");
                                }
                            }
                        }    
                    }       
                }                                   
            }

    
            //Salva as categorias
            foreach($array_categorias as $array_categoria)
            {    
                $verifica_categoria = CurriculoAtividadeCategoria::where('curriculo_id', '=', $array_categoria['curriculo_id'])
                                                                 ->where('atividade_complementar_categoria_id', '=', $array_categoria['atividade_complementar_categoria_id'])
                                                                 ->where('cod_categoria_curriculo', '=', $array_categoria['cod_categoria_curriculo'])
                                                                 ->load(); 
                
                //Se não encontrou registro, é novo                                                  
                if(empty($verifica_categoria))
                {
                    $curriculo_atividade_categoria = new CurriculoAtividadeCategoria;
                }                        
                
                //Se encontrou registro, é porque a categoria já havia sido selecionada anteriormente
                else
                {
                    $curriculo_atividade_categoria = CurriculoAtividadeCategoria::find($verifica_categoria[0]->id);
                }
                        
                $curriculo_atividade_categoria->curriculo_id = $array_categoria['curriculo_id'];
                $curriculo_atividade_categoria->atividade_complementar_categoria_id = $array_categoria['atividade_complementar_categoria_id']; 
                $curriculo_atividade_categoria->cod_categoria_curriculo = $array_categoria['cod_categoria_curriculo'];
                $curriculo_atividade_categoria->ch_categoria_hora_relogio = $array_categoria['ch_categoria_hora_relogio'];                                                  
                $curriculo_atividade_categoria->store();                       	


                $categorias[] = $curriculo_atividade_categoria->id;
            
            
            
                //Salva as atividades
                foreach($array_atividades as $array_atividade)
                {   
                    //Se a atividade pertencer à categoria
                    if($array_atividade['atividade_complementar_categoria_id'] == $array_categoria['atividade_complementar_categoria_id'])
                    {             
                        $verifica_atividade = CurriculoAtividadeCadastro::where('curriculo_id', '=', $array_atividade['curriculo_id'])
                                                                        ->where('atividade_complementar_categoria_id', '=', $array_atividade['atividade_complementar_categoria_id'])
                                                                        ->where('curriculo_atividade_categoria_id', '=', $curriculo_atividade_categoria->id)
                                                                        ->where('atividade_complementar_cadastro_id', '=', $array_atividade['atividade_complementar_cadastro_id'])
                                                                        ->where('cod_atividade_curriculo', '=', $array_atividade['cod_atividade_curriculo'])
                                                                        ->load(); 
                        
                        //Se não encontrou registro, é novo                                                  
                        if(empty($verifica_atividade))
                        {
                            $curriculo_atividade_cadastro = new CurriculoAtividadeCadastro;
                        }                        
                        
                        //Se encontrou registro, é porque a categoria já havia sido selecionada anteriormente
                        else
                        {
                            $curriculo_atividade_cadastro = CurriculoAtividadeCadastro::find($verifica_atividade[0]->id);
                        }
                        
                        $curriculo_atividade_cadastro->curriculo_id = $array_atividade['curriculo_id'];        
                        $curriculo_atividade_cadastro->atividade_complementar_categoria_id = $array_atividade['atividade_complementar_categoria_id'];
                        $curriculo_atividade_cadastro->curriculo_atividade_categoria_id = $curriculo_atividade_categoria->id;
                        $curriculo_atividade_cadastro->atividade_complementar_cadastro_id = $array_atividade['atividade_complementar_cadastro_id']; 
                        $curriculo_atividade_cadastro->cod_atividade_curriculo = $array_atividade['cod_atividade_curriculo'];
                        $curriculo_atividade_cadastro->ch_atividade_hora_relogio = $array_atividade['ch_atividade_hora_relogio'];                                                  
                        $curriculo_atividade_cadastro->store();                       	
        
        
                        $atividades[] = $curriculo_atividade_cadastro->id;
                    }    
                }
            }    


            //Deleta as atividades e categorias que não persistem
            if($old_atividades)
            {
                foreach($old_atividades as $old_atividade)
                {
                    if (!in_array($old_atividade->id, $atividades))
                    {
                        $old_atividade->delete();
                    }
                }
            }
    
    
            if($old_categorias)
            {
                foreach($old_categorias as $old_categoria)
                {
                    if (!in_array($old_categoria->id, $categorias))
                    {
                        $old_categoria->delete();
                    }
                }
            }
      
      
            TTransaction::close();
            
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));            
            TApplication::loadPage('CurriculoList', 'onReload');
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->formGrid->setData( $this->formGrid->getData() ); 
            TTransaction::rollback(); 
        }    
    }
    
    
    public function onShow()
    {
    }
    
    
    function show()
    {
        $this->onReload();
        parent::show();
    }    
}
