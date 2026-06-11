<?php

class CriterioIntegralizacaoForm extends TPage
{
    protected $datagrid; 
    protected $formGrid;
        

    public function __construct()
    {
        parent::__construct();
        
        
        $curriculo = TSession::getValue('dados_curriculo'); 
        $curso = TSession::getValue('dados_curso'); 

        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableDefaultClick();
        
        
        $panel = new TPanelGroup("Critérios de Integralização : $curso->nome_curso_diploma - Currículo: $curriculo->codigo_curriculo");
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        
        
        // form grid
        $this->formGrid = new TForm('form_CriteriosIntegralizacaoCurriculo');
        
        
        //Exibe CH total do curso
        //$label_ch_curso_ha = new TLabel('CH TOTAL DO CURSO EM HORA/AULA: ');
        //$label_ch_curso_ha->setFontStyle('b');
        //$ch_curso_ha = new TEntry('ch_curso_ha');
        //$ch_curso_ha->setEditable(FALSE);
        
        $label_ch_curso_hr = new TLabel('CH TOTAL DO CURSO: ');
        $label_ch_curso_hr->setFontStyle('b');
        $ch_curso_hr = new TEntry('ch_curso_hr');
        $ch_curso_hr->setEditable(FALSE);
        
        //$this->formGrid->addField($ch_curso_ha);
        $this->formGrid->addField($ch_curso_hr);
        
        $table = new TTable;        
        $table->style = 'width: 32%; margin-bottom: 15px; border-spacing: 0 8px';
        //$table->addRowSet($label_ch_curso_ha, $ch_curso_ha);
        $table->addRowSet($label_ch_curso_hr, $ch_curso_hr);
        $this->formGrid->add($table);
        
         
        //Insere painel com datagrid no formulário para manipulação dos campos                
        $this->formGrid->add($panel);
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'center');
        $column_codigo = new TDataGridColumn('codigo', 'Critério', 'center');
        $column_tipo_unidade = new TDataGridColumn('tipo_unidade', 'Tipo', 'left');
        $column_dados_etiqueta_id = new TDataGridColumn('dados_etiqueta_id', 'Classificação', 'left');
        $column_ch_computada_ha = new TDataGridColumn('ch_computada_ha', 'H/A Computada', 'center', 40);
        $column_ch_computada_hr = new TDataGridColumn('ch_computada_hr', 'CH Computada', 'center');
        $column_ch_minima_ha = new TDataGridColumn('ch_minima_ha', 'H/A Mínima', 'center');
        $column_ch_minima_hr = new TDataGridColumn('ch_minima_hr', 'CH Mínima', 'center');
        $column_ch_maxima_ha = new TDataGridColumn('ch_maxima_ha', 'H/A Máxima', 'center');
        $column_ch_maxima_hr = new TDataGridColumn('ch_maxima_hr', 'CH Máxima', 'center');
        $column_participacao_total = new TDataGridColumn('participacao_total', 'Participação CH Total', 'center');


        $column_dados_etiqueta_id->setTransformer( array($this, 'setEtiquetas') );
                

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id)->setVisibility(false);
        $this->datagrid->addColumn($column_codigo);
        $this->datagrid->addColumn($column_tipo_unidade);
        $this->datagrid->addColumn($column_dados_etiqueta_id);
        $this->datagrid->addColumn($column_ch_computada_ha)->setVisibility(false);
        $this->datagrid->addColumn($column_ch_computada_hr);
        $this->datagrid->addColumn($column_ch_minima_ha)->setVisibility(false);
        $this->datagrid->addColumn($column_ch_minima_hr);
        $this->datagrid->addColumn($column_ch_maxima_ha)->setVisibility(false);
        $this->datagrid->addColumn($column_ch_maxima_hr);
        $this->datagrid->addColumn($column_participacao_total);


        // create the datagrid model
        $this->datagrid->createModel();
        
        
        // creates the action button
        $button1 = TButton::create('salvar', [$this, 'onSave'], 'Salvar', 'fa:save');
        $button1->class = 'btn btn-sm btn-primary';
        $button2 = TButton::create('voltar', ['CurriculoList','onReload'], 'Voltar', 'fas:arrow-alt-circle-left blue');
        $this->formGrid->addField($button1);
        $this->formGrid->addField($button2);

        $hbox1 = new THBox;
        $hbox1->add($button1);
        $hbox1->add($button2);        
        
        $panel->addFooter($hbox1);


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->formGrid);
        
        parent::add($container);
    }
    
    
    public function setEtiquetas($column_dados_etiqueta_id, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $ids_etiquetas = explode(',', $object->dados_etiqueta_id);

            if($ids_etiquetas)
            {
                foreach($ids_etiquetas as $id_etiqueta)
                {                    
                    $dados_etiqueta = new Etiqueta($id_etiqueta);

                    $div = new TElement('span');
                    $div->style="padding: 5px; border-radius: 5px; color: white; background-color:" . $dados_etiqueta->color;
                    $div->add($dados_etiqueta->nome);
                                   
                    $etiquetas[] = $div;
                }
                
                return implode(' ', $etiquetas);
            }
            
            return '';
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
        

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $conn = TTransaction::get();
            
            
            //Busca todos os itens lançados no currículo
            $curriculo = TSession::getValue('dados_curriculo');
                        
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('curriculo_disciplina_id', 'IN', "(select id from curriculo_disciplina where curriculo_id =" . $curriculo->id . " AND opcao_disciplina = 'Grade')"));
            $criteria1->setProperty('order', 'curriculo_disciplina_id, dados_etiqueta_id');
                                    
            $curriculo_disciplinas_etiquetas = CurriculoDisciplinaEtiqueta::getObjects($criteria1);


            //Cria um array cujo índice é o ID da disciplina e traz como valores o tipo ao qual ela pertence bem como as etiquetas aplicadas 
            foreach($curriculo_disciplinas_etiquetas as $curriculo_disciplina_etiqueta)
            {
                $itens_duplicados[$curriculo_disciplina_etiqueta->curriculo_disciplina_id]['tipo_unidade'] = $curriculo_disciplina_etiqueta->curriculo_disciplina->tipo;
                $itens_duplicados[$curriculo_disciplina_etiqueta->curriculo_disciplina_id]['etiqueta_'.$curriculo_disciplina_etiqueta->dados_etiqueta_id] = $curriculo_disciplina_etiqueta->etiqueta->id;                                    
            }

            
            //Agrupa os elementos que possuem tipo e etiquetas iguais
            $itens_unicos = array_values(array_map("unserialize", array_unique(array_map("serialize", $itens_duplicados))));           


            /*echo '<pre>';
            var_dump($itens_unicos);
            die;
            echo '</pre>';*/


            /*Percorre o array resultante para "desmembrar" os itens que possuem duas etiquetas (obrigatória ou optativa) + (extensão). Eles aparecerão em 2 linhas
            na grid: uma vai trazer a CH total do item e a outra a CH do item que é destinada à extensão*/
            $i = 0;
            
            foreach($itens_unicos as $key => $item_unico)
            {
                //Antes de retirar do array, pega o tipo ao qual a disciplina pertence
                $tipo_unidade = $item_unico['tipo_unidade'];
                
                
                //Em seguida, retira do array para verificar somente as etiquetas
                unset($item_unico['tipo_unidade']);
                
                
                foreach($item_unico as $item)
                {
                    $etiqueta = new Etiqueta($item);
                                                            
                    $etiquetas_ids[] = $etiqueta->id;
                    $etiquetas_codigos[] = $etiqueta->codigo;
                }    
                                
                $verificacao_etiquetas = serialize($etiquetas_codigos);
                
                
                /*Se a disciplina NÃO possui etiqueta de extensão ('ext' é o código de Extensão designado pelo MEC, não pode ser alterado), o item recebe o tipo
                e a etiqueta que a disciplina possui*/
                if(strpos($verificacao_etiquetas,'"ext"') === false)
                {
                    $unidades[$i]['tipo'] = $tipo_unidade;
                    
                    foreach($etiquetas_ids as $etiqueta_id)
                    {
                        $unidades[$i]['etiqueta_'.$etiqueta_id] = $etiqueta_id; 
                    }                    
                
                    $i++;
                }
                
                
                //Se a disciplina POSSUI etiqueta de extensão, é porque, além desta, ela recebeu mais uma etiqueta: 'obrigatória' ou 'optativa' 
                else
                {
                    foreach($etiquetas_ids as $etiqueta_id)
                    {
                        $etiqueta = new Etiqueta($etiqueta_id);
                        
                        //Se for a de extensão, a posição 'etiqueta' vai receber os dois IDs
                        if($etiqueta->codigo == "ext")
                        {
                            $unidades[$i]['tipo'] = $tipo_unidade;
                            
                            foreach($etiquetas_ids as $etiqueta_id)
                            {
                                $unidades[$i]['etiqueta_'.$etiqueta_id] = $etiqueta_id; 
                            }
                        }
                        
                        //Se não, a posição 'etiqueta' vai receber só o ID da etiqueta que acompanha a de extensão
                        else
                        {
                            $unidades[$i]['tipo'] = $tipo_unidade;
                            $unidades[$i]['etiqueta_'.$etiqueta->id] = $etiqueta->id; 
                        }
                        
                        $i++;
                    }  
                }  
            
                //Zera os arrays para a próxima iteração
                $etiquetas_ids = [];
                $etiquetas_codigos = [];
                
            }
            
            
            //Array final que será lançado na datagrid
            $unidades_curriculares = array_values(array_map("unserialize", array_unique(array_map("serialize", $unidades)))); 


            /*echo '<pre>';
            var_dump($unidades_curriculares);
            die;
            echo '</pre>';*/


            if($unidades_curriculares)
            {     
                $combo_participacao = [];
                $combo_participacao['Sim'] = "Sim";
                $combo_participacao['Não'] = "Não";
                    
                $i = 1;                
             
                $this->datagrid->clear();
                $this->datagrid->disableHtmlConversion();                


                foreach($unidades_curriculares as $unidade_curricular)
                {                
                    //Coluna - TIPO (após inserir o tipo na datagrid, retira do array para que sobre somente as chaves relacionadas às etiquetas)
                    $object->tipo_unidade = $unidade_curricular['tipo'];
                    unset($unidade_curricular['tipo']);


                    
                    //Coluna - CLASSIFICAÇÃO
                    foreach($unidade_curricular as $item_unidade)
                    {
                        $etiqueta = new Etiqueta($item_unidade);
                                                
                        $classificacao[] = $etiqueta->id;
                    }   
                    
                    $object->dados_etiqueta_id = implode(',', $classificacao); 
                    
                    
                    
                    //Coluna - CRITÉRIO 
                    $curriculo_criterio_integralizacao = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo->id)
                                                                                        ->where('tipo_unidade', '=', $object->tipo_unidade)
                                                                                        ->where('dados_etiqueta_id', '=', $object->dados_etiqueta_id)
                                                                                        ->orderBy('codigo')
                                                                                        ->load();
                    
                    //Se o critério já foi salvo anteriormente, traz seu código
                    if($curriculo_criterio_integralizacao)
                    {
                        $object->codigo = $curriculo_criterio_integralizacao[0]->codigo;
                    }
                    else
                    {
                        //Verifica se já existe algum critério ou é o primeiro
                        $curriculo_criterio_integralizacao = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo->id)
                                                                                            ->orderBy('codigo')
                                                                                            ->load();
                        
                        //Se já existir algum critério, pega o último e incrementa
                        if($curriculo_criterio_integralizacao)
                        {
                            $ultimo_criterio = end($curriculo_criterio_integralizacao);
                            $parts = explode('-C', $ultimo_criterio->codigo);
                            $total = end($parts);
                            $cont = $total + 1; //Último item + 1
                            
                            $object->codigo = $curriculo->codigo_curriculo . '-C' . $cont;
                        }
                        
                        //Inicia em 1
                        else
                        {
                            $object->codigo = $curriculo->codigo_curriculo . '-C' . $i;
                        }                                                                    
                    }
                   
                   
                   
                    //Início dos cálculos das cargas horárias em hora/aula e hora/relógio com base no tipo de unidade e etiquetas informadas na linha da grid
                    $ids_etiquetas = explode(',', $object->dados_etiqueta_id);
                   
                    if($ids_etiquetas)
                    {
                        foreach($ids_etiquetas as $id_etiqueta)
                        {                    
                            $dados_etiqueta = new Etiqueta($id_etiqueta);
                     
                            $ids[] = $dados_etiqueta->id;
                             
                            $etiquetas[$dados_etiqueta->id]['codigo'] = $dados_etiqueta->codigo;
                        }
                    }        
        
                    $verificacao_etiquetas =  serialize($etiquetas);           
            
            
                    //Se a etiqueta de extensão NÃO estiver presente (pois é a única que recebe CH), será computada a carga horária das unidades 
                    if(strpos($verificacao_etiquetas,'"ext"') === false)
                    { 
                        //Colunas - CARGA HORÁRIA COMPUTADA EM HORA/AULA E HORA/RELÓGIO (CONSIDERA AS DISCIPLINAS DA GRADE E AS OPÇÕES DE OPTATIVAS)
                        $criteria2 = new TCriteria;
                        $criteria2->add(new TFilter('curriculo_digital_id', '=', $curriculo->id));
                        $criteria2->add(new TFilter('tipo_unidade', '=', $object->tipo_unidade));
                        $criteria2->add(new TFilter('dados_etiqueta_id', 'IN', $ids));
                        
                        $criterios = VwCriterioIntegralizacaoCurriculo::getObjects($criteria2); 
                       
                        foreach($criterios as $criterio)
                        {
                            $soma_ch_computada_ha += $criterio->ch_hora_aula_disciplina;
                            $soma_ch_computada_hr += $criterio->ch_hora_relogio_disciplina;
                        }
                        
                        $object->ch_computada_ha = $soma_ch_computada_ha;
                        $object->ch_computada_hr = $soma_ch_computada_hr;
                        
                        
                        
                        //Colunas - CARGA HORÁRIA MÍNIMA E MÁXIMA EM HORA/AULA (CONSIDERA SOMENTE AS DISCIPLINAS DA GRADE)
                        $count_ids = count($ids);
                        $string_ids = implode(",", $ids);
                        $tipo_unidade = "'$object->tipo_unidade'";
                        $opcao_disciplina = "'Grade'"; 
                        
                        $sth_ha = $conn->prepare('(SELECT curriculo_disciplina_id, sum(ch_hora_aula_disciplina) as soma_hora_aula_disciplina
                                                   FROM Vw_CriterioIntegralizacaoCurriculo 
                                                   WHERE curriculo_digital_id = '. $curriculo->id . ' and tipo_unidade = ' . $tipo_unidade . ' and opcao_disciplina = ' . $opcao_disciplina . ' and dados_etiqueta_id IN (' . $string_ids . ')
                                                   GROUP BY curriculo_disciplina_id
                                                   HAVING COUNT(DISTINCT dados_etiqueta_id) = ' . $count_ids . ')');
                        
                        $sth_ha->execute();
                        $result_ha = $sth_ha->fetchAll();
                                           
                        foreach($result_ha as $row)
                        {               
                            $soma_ha += $row['soma_hora_aula_disciplina'];
                        }
                        
                        //Vão ser iguais, pois a matriz curricular não é flexível
                        $object->ch_minima_ha = $soma_ha;
                        $object->ch_maxima_ha = $soma_ha; 
                        
                        
                        
                        //Colunas - CARGA HORÁRIA MÍNIMA E MÁXIMA EM HORA/RELÓGIO (CONSIDERA SOMENTE AS DISCIPLINAS DA GRADE)
                        $sth_hr = $conn->prepare('(SELECT curriculo_disciplina_id, sum(ch_hora_relogio_disciplina) as soma_hora_relogio_disciplina
                                                   FROM Vw_CriterioIntegralizacaoCurriculo 
                                                   WHERE curriculo_digital_id = '. $curriculo->id . ' and tipo_unidade = ' . $tipo_unidade . ' and opcao_disciplina = ' . $opcao_disciplina . ' and dados_etiqueta_id IN (' . $string_ids . ')
                                                   GROUP BY curriculo_disciplina_id
                                                   HAVING COUNT(DISTINCT dados_etiqueta_id) = ' . $count_ids . ')');
                        
                        $sth_hr->execute();
                        $result_hr = $sth_hr->fetchAll();
                                           
                        foreach($result_hr as $row)
                        {               
                            $soma_hr += $row['soma_hora_relogio_disciplina'];
                        }
                        
                        //Vão ser iguais, pois a matriz curricular não é flexível
                        $object->ch_minima_hr = $soma_hr;
                        $object->ch_maxima_hr = $soma_hr;   
                    }
            
            
                    //Se a etiqueta de extensão estiver presente, será computada a sua carga horária
                    else
                    {
                        $count_ids = count($ids);
                        $string_ids = implode(",", $ids);
                        $tipo_unidade = "'$object->tipo_unidade'";
                        $opcao_disciplina = "'Grade'";
                        
                        
                        
                        //Coluna - CARGA HORÁRIA COMPUTADA EM HORA/AULA (CONSIDERA AS DISCIPLINAS DA GRADE E AS OPÇÕES DE OPTATIVAS)
                        $sth_ha = $conn->prepare('(SELECT curriculo_disciplina_id, sum(ch_hora_aula_etiqueta) as soma_ch_computada_ha
                                                   FROM Vw_CriterioIntegralizacaoCurriculo 
                                                   WHERE curriculo_digital_id = '. $curriculo->id . ' and tipo_unidade = ' . $tipo_unidade . ' and dados_etiqueta_id IN (' . $string_ids . ')
                                                   GROUP BY curriculo_disciplina_id
                                                   HAVING COUNT(DISTINCT dados_etiqueta_id) = ' . $count_ids . ')');
                
                        $sth_ha->execute();
                        $result_ha = $sth_ha->fetchAll();
                                           
                        foreach($result_ha as $row)
                        {     
                            $soma_ch_computada_ha += $row['soma_ch_computada_ha'];                   
                        }
                        
                        $object->ch_computada_ha = $soma_ch_computada_ha;
                        
                        
                        
                        //Coluna - CARGA HORÁRIA COMPUTADA EM HORA/RELÓGIO(CONSIDERA AS DISCIPLINAS DA GRADE E AS OPÇÕES DE OPTATIVAS)
                        $sth_hr = $conn->prepare('(SELECT curriculo_disciplina_id, sum(ch_hora_relogio_etiqueta) as soma_ch_computada_hr
                                                   FROM Vw_CriterioIntegralizacaoCurriculo 
                                                   WHERE curriculo_digital_id = '. $curriculo->id . ' and tipo_unidade = ' . $tipo_unidade . ' and dados_etiqueta_id IN (' . $string_ids . ')
                                                   GROUP BY curriculo_disciplina_id
                                                   HAVING COUNT(DISTINCT dados_etiqueta_id) = ' . $count_ids . ')');
                
                        $sth_hr->execute();
                        $result_hr = $sth_hr->fetchAll();
                                           
                        foreach($result_hr as $row)
                        {               
                            $soma_ch_computada_hr += $row['soma_ch_computada_hr'];
                        }
                        
                        $object->ch_computada_hr = $soma_ch_computada_hr;
                        
                        
                        
                        //Colunas - CARGA HORÁRIA MÍNIMA E MÁXIMA EM HORA/AULA (CONSIDERA SOMENTE AS DISCIPLINAS DA GRADE)
                        $sth_ha = $conn->prepare('(SELECT curriculo_disciplina_id, sum(ch_hora_aula_etiqueta) as soma_hora_aula_etiqueta
                                                   FROM Vw_CriterioIntegralizacaoCurriculo 
                                                   WHERE curriculo_digital_id = '. $curriculo->id . ' and tipo_unidade = ' . $tipo_unidade . ' and opcao_disciplina = ' . $opcao_disciplina . ' and dados_etiqueta_id IN (' . $string_ids . ')
                                                   GROUP BY curriculo_disciplina_id
                                                   HAVING COUNT(DISTINCT dados_etiqueta_id) = ' . $count_ids . ')');
                        
                        $sth_ha->execute();
                        $result_ha = $sth_ha->fetchAll();
                                           
                        foreach($result_ha as $row)
                        {                                          
                            $soma_ha += $row['soma_hora_aula_etiqueta'];
                        }
                        
                        //Vão ser iguais, pois a matriz curricular não é flexível
                        $object->ch_minima_ha = $soma_ha;
                        $object->ch_maxima_ha = $soma_ha;
                        
                        
                        
                        //Colunas - CARGA HORÁRIA MÍNIMA E MÁXIMA EM HORA/RELÓGIO (CONSIDERA SOMENTE AS DISCIPLINAS DA GRADE)
                        $sth_hr = $conn->prepare('(SELECT curriculo_disciplina_id, sum(ch_hora_relogio_etiqueta) as soma_hora_relogio_etiqueta
                                                   FROM Vw_CriterioIntegralizacaoCurriculo 
                                                   WHERE curriculo_digital_id = '. $curriculo->id . ' and tipo_unidade = ' . $tipo_unidade . ' and opcao_disciplina = ' . $opcao_disciplina . ' and dados_etiqueta_id IN (' . $string_ids . ')
                                                   GROUP BY curriculo_disciplina_id
                                                   HAVING COUNT(DISTINCT dados_etiqueta_id) = ' . $count_ids . ')');
                        
                        $sth_hr->execute();
                        $result_hr = $sth_hr->fetchAll();
                                           
                        foreach($result_hr as $row)
                        {                                          
                            $soma_hr += $row['soma_hora_relogio_etiqueta'];
                        }
                        
                        //Vão ser iguais, pois a matriz curricular não é flexível
                        $object->ch_minima_hr = $soma_hr;
                        $object->ch_maxima_hr = $soma_hr;        
                    }
                    
                    

                    //Coluna - PARTICIPAÇÃO CH TOTAL
                    $final_cod_criterio = explode('-', $object->codigo);
                    
                    $object->participacao_total = new TCombo('participacao_total_criterio_' . $final_cod_criterio[1]);
                    $object->participacao_total->addItems($combo_participacao);
                    $object->participacao_total->setSize(70);
                    $object->participacao_total->setChangeAction(new TAction([$this, 'onVerificaParticipacaoChTotal'], ['ch_hora_aula' => $soma_ha, 'ch_hora_relogio' => $soma_hr]));
                    
                    //Se tiver registro na tabela curriculo_criterio_integralizacao, traz o valor setado na combo
                    $curriculo_criterio_integralizacao = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo->id)
                                                                                        ->where('codigo', '=', $object->codigo)
                                                                                        ->load();
                                                                         
                    if($curriculo_criterio_integralizacao)
                    {
                        $object->participacao_total->setValue($curriculo_criterio_integralizacao[0]->participacao_total);                            
                    
                        $param['_field_name'] = 'participacao_total_criterio_' . $final_cod_criterio[1];
                        $param['_field_value'] = $curriculo_criterio_integralizacao[0]->participacao_total;
                        $param['ch_hora_aula'] = $soma_ha;
                        $param['ch_hora_relogio'] = $soma_hr;
                        
                        $this->onVerificaParticipacaoChTotal($param);
                    }
                    
                    $this->datagrid->addItem($object);
                    $this->formGrid->addField($object->participacao_total);


                    //Salva o valor das colunas que não são editáveis em um array
                    $array_criterios[$object->codigo]['codigo'] = $object->codigo;
                    $array_criterios[$object->codigo]['tipo_unidade'] = $object->tipo_unidade;
                    $array_criterios[$object->codigo]['dados_etiqueta_id'] = $object->dados_etiqueta_id;
                    $array_criterios[$object->codigo]['ch_computada_ha'] = $object->ch_computada_ha;
                    $array_criterios[$object->codigo]['ch_computada_hr'] = $object->ch_computada_hr;
                    $array_criterios[$object->codigo]['ch_minima_ha'] = $object->ch_minima_ha;
                    $array_criterios[$object->codigo]['ch_minima_hr'] = $object->ch_minima_hr;
                    $array_criterios[$object->codigo]['ch_maxima_ha'] = $object->ch_maxima_ha;
                    $array_criterios[$object->codigo]['ch_maxima_hr'] = $object->ch_maxima_hr;


                    //Limpa os arrays para receberem os dados da próxima iteração
                    $classificacao = [];
                    $ids = [];
                    $etiquetas = [];
                    $soma_ch_computada_ha = '';
                    $soma_ch_computada_hr = '';
                    $soma_ha = '';  
                    $soma_hr = '';  
                                       
                    $i++;   
                }
                
                TSession::setValue('array_criterios', NULL);
                TSession::setValue('array_criterios', $array_criterios);                                                    
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
    
    
    public static function onVerificaParticipacaoChTotal($param)
    {    
        $name = $param['_field_name'];
        $participacao = $param['_field_value'];
        $ch_hora_aula = $param['ch_hora_aula']; 
        $ch_hora_relogio = $param['ch_hora_relogio'];   
        
        
        //Faz o cálculo em hora/aula e hora/relógio
        if($participacao == "Sim")
        {
            $ch_total_curso_ha = TSession::getValue('ch_total_curso_ha'); 
            $ch_total_curso_hr = TSession::getValue('ch_total_curso_hr');  
            
            $ch_total_curso_ha[$name] = $ch_hora_aula; 
            $ch_total_curso_hr[$name] = $ch_hora_relogio;
        
            TSession::setValue('ch_total_curso_ha', $ch_total_curso_ha); 
            TSession::setValue('ch_total_curso_hr', $ch_total_curso_hr);                
        }                   
        else
        {
            $ch_total_curso_ha = TSession::getValue('ch_total_curso_ha'); 
            $ch_total_curso_hr = TSession::getValue('ch_total_curso_hr'); 
            
            unset($ch_total_curso_ha[$name]); 
            unset($ch_total_curso_hr[$name]);
        
            TSession::setValue('ch_total_curso_ha', $ch_total_curso_ha);  
            TSession::setValue('ch_total_curso_hr', $ch_total_curso_hr);                       
        }  
               
               
        $ch_total_curso_ha = TSession::getValue('ch_total_curso_ha');
        $ch_total_curso_hr = TSession::getValue('ch_total_curso_hr');
                
        $ch_total_ha = 0;
        $ch_total_hr = 0;
                
        if($ch_total_curso_ha)
        {
            foreach($ch_total_curso_ha as $ch_item_ha)
            {
                $ch_total_ha += $ch_item_ha;                                                
            }                                        
        } 
        
        if($ch_total_curso_hr)
        {
            foreach($ch_total_curso_hr as $ch_item_hr)
            {
                $ch_total_hr += $ch_item_hr;                                                
            }                                        
        }                              
        
        //Exibe a atualização do total                       
        $obj = new StdClass;
        $obj->ch_curso_ha = $ch_total_ha;
        $obj->ch_curso_hr = $ch_total_hr;
        
        TForm::sendData('form_CriteriosIntegralizacaoCurriculo', $obj, FALSE, FALSE);
    }
    
     
    public function onSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $data = $this->formGrid->getData();        
                       
            $curriculo = TSession::getValue('dados_curriculo'); 
            
            
            //Se o currículo já foi publicado uma vez, não permite alteração
            if($curriculo->data_primeira_publicacao <> NULL)
            {
                new TMessage('error','O registro não pode ser alterado, pois o currículo já foi publicado');
                return false;
            }
            
            
            //Após salvar, vai deletar os critérios que não persistem
            $old_criterios = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo->id)->load();
            
            
            //Pega os valores dos campos do objeto que foi adicionado à datagrid, mas não são editáveis
            $array_criterios = TSession::getValue('array_criterios');
            
            
            //Percorre o formulário para pegar os valores que são campos de input
            foreach ($this->formGrid->getFields() as $value => $field)
            {
                if ($field instanceof TCombo)
                {                   
                    $parts = explode('_', $value);
     
                    //A parte [3] do nome do campo recebe o final do código do critério (C1, C2, C3...)
                    $final_cod_criterio = $parts[3];
                    
                    //Código original do critério é composto pelo código do currículo + "-" + o final do nome do campo
                    $codigo_criterio = $curriculo->codigo_curriculo . '-' . $final_cod_criterio;
                    
                    
                    foreach($array_criterios as $array_criterio)
                    {                        
                        if($codigo_criterio == $array_criterio['codigo'])
                        {
                            $verifica_criterio = CurriculoCriterioIntegralizacao::where('curriculo_id', '=', $curriculo->id)
                                                                                ->where('codigo', '=', $codigo_criterio)
                                                                                ->load();
                                                                                
                            //Se não encontrou registro, é novo                                                     
                            if(empty($verifica_criterio))
                            {
                                $curriculo_criterio_integralizacao = new CurriculoCriterioIntegralizacao;
                            }
                            
                            //Se encontrou registro, é porque o critério já havia sido salvo anteriormente
                            else
                            {
                                $curriculo_criterio_integralizacao = CurriculoCriterioIntegralizacao::find($verifica_criterio[0]->id);
                            }
                            
                            
                            $curriculo_criterio_integralizacao->curriculo_id = $curriculo->id;
                           	$curriculo_criterio_integralizacao->codigo = $array_criterio['codigo'];
                           	$curriculo_criterio_integralizacao->tipo_unidade = $array_criterio['tipo_unidade'];
                           	$curriculo_criterio_integralizacao->dados_etiqueta_id = $array_criterio['dados_etiqueta_id'];
                           	
                           	//Salva o nome das etiquetas
                           	$ids_etiquetas = explode(',', $curriculo_criterio_integralizacao->dados_etiqueta_id);
                            
                            if($ids_etiquetas)
                            {
                                foreach($ids_etiquetas as $id_etiqueta)
                                {                    
                                    $dados_etiqueta = new Etiqueta($id_etiqueta);
                    
                                    $etiquetas[] = $dados_etiqueta->nome;
                                }
                                
                                $curriculo_criterio_integralizacao->etiquetas_nome = implode(',', $etiquetas);
                            }
            
                           	$curriculo_criterio_integralizacao->ch_computada_hora_aula = $array_criterio['ch_computada_ha'];
                           	$curriculo_criterio_integralizacao->ch_computada_hora_relogio = $array_criterio['ch_computada_hr'];
                           	$curriculo_criterio_integralizacao->ch_minima_hora_aula = $array_criterio['ch_minima_ha'];
                           	$curriculo_criterio_integralizacao->ch_minima_hora_relogio = $array_criterio['ch_minima_hr'];
                           	$curriculo_criterio_integralizacao->ch_maxima_hora_aula = $array_criterio['ch_maxima_ha'];
                           	$curriculo_criterio_integralizacao->ch_maxima_hora_relogio = $array_criterio['ch_maxima_hr'];
                           	$curriculo_criterio_integralizacao->participacao_total = $data->$value;
                           	$curriculo_criterio_integralizacao->system_user_id = TSession::getValue('userid');
                           	$curriculo_criterio_integralizacao->data_reg = date('Y-m-d H:i:s');                           	                            
                           	$curriculo_criterio_integralizacao->store();
                           	
                           	
                           	$criterios[] = $curriculo_criterio_integralizacao->id;
                           	
                           	
                            //Limpa o array para receber os dados da próxima iteração
                            $etiquetas = [];                       
                        }
                    }
                }
            }                           


            //Deleta os critérios que não persistem
            if($old_criterios)
            {
                foreach($old_criterios as $old_criterio)
                {
                    if (!in_array($old_criterio->id, $criterios))
                    {
                        $old_criterio->delete();
                    }
                }
            }
            
            
            $this->formGrid->setData($data);          
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            TApplication::loadPage('CurriculoList', 'onReload');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onShow()
    {
        
    }
            

    public function show()
    {
        $this->onReload();
        parent::show();  
    } 
}
