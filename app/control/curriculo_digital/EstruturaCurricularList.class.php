<?php

class EstruturaCurricularList extends TPage
{
    private $datagridEstruturaCurricular; 
    private $pageNavigationEstruturaCurricular;
    private $loadedEstruturaCurricular;

    private $datagridOptativas; 
    private $pageNavigationOptativas;
    private $loadedOptativas;

    public function __construct($param)
    {
        parent::__construct();


        //Para preenchimento do cabeçalho da datagrid
        $id_curriculo = $param['curriculo_id'];

        try
        {
            TTransaction::open('Felabs_DB');
            
            $curriculo = new CurriculoDigital($id_curriculo);
            $curso = new DiplomaDigitalCurso($curriculo->dados_curso_id);
                    
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }  
        
        
        // creates a Datagrid
        $this->datagridEstruturaCurricular = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagridEstruturaCurricular->style = 'width: 100%';
        //$this->datagridEstruturaCurricular->datatable = 'true';  
        $this->datagridEstruturaCurricular->disableDefaultClick();            
        $this->datagridEstruturaCurricular->setGroupColumn('etapa', '<b>{etapa}ª ETAPA</b>');


        // creates the datagrid columns
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'center');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'center');
        $column_cod_disciplina_curriculo = new TDataGridColumn('cod_disciplina_curriculo', 'Cód. no Currículo', 'right');
        $column_cod_disciplina_grade_etapa = new TDataGridColumn('nome', 'Unidade Curricular', 'left');
        $column_ch_hora_aula = new TDataGridColumn('ch_hora_aula', 'CH Hora/Aula', 'center');
        $column_ch_hora_relogio = new TDataGridColumn('ch_hora_relogio', 'CH', 'center');
        $column_ch_hora_aula_extensao = new TDataGridColumn('ch_hora_aula_extensao', 'CH Hora/Aula Extensão', 'center');
        $column_ch_hora_relogio_extensao = new TDataGridColumn('ch_hora_relogio_extensao', 'CH Extensão', 'center');
        $column_disciplina_pre_requisitada = new TDataGridColumn('disciplina_pre_requisitada', 'Pré-Requisito(s)', 'center');
        $column_etiqueta = new TDataGridColumn('etiqueta', 'Etiqueta(s)', 'left', '16%');
        $column_area_formacao = new TDataGridColumn('area_formacao', 'Área(s)', 'center');
        

        $column_disciplina_pre_requisitada->setTransformer( array($this, 'setPreRequisitoDisciplina') );
        $column_etiqueta->setTransformer( array($this, 'setEtiquetaDisciplina') );
        $column_area_formacao->setTransformer( array($this, 'setAreaDisciplina') );


        $column_ch_hora_aula->setTotalFunction( function($values) {
            $total = new TElement('span');
            $total->id = 'total_hora_relogio';
            $total->style = 'float:center; font-weight:bold; color: black; font-size: 12pt;';
            $total->add(array_sum((array) $values));
        
            return $total;
        });        
                
        $column_ch_hora_relogio->setTotalFunction( function($values) {
            $total = new TElement('span');
            $total->id = 'total_hora_relogio';
            $total->style = 'float:center; font-weight:bold; color: black; font-size: 12pt;';
            $total->add(array_sum((array) $values));
        
            return $total;
        });
        
        $column_ch_hora_aula_extensao->setTotalFunction( function($values) {
            $total = new TElement('span');
            $total->id = 'total_extensao';
            $total->style = 'float:center; font-weight:bold; color: black; font-size: 12pt;';
            $total->add(array_sum((array) $values));
        
            return $total;
        }); 
        
        $column_ch_hora_relogio_extensao->setTotalFunction( function($values) {
            $total = new TElement('span');
            $total->id = 'total_extensao';
            $total->style = 'float:center; font-weight:bold; color: black; font-size: 12pt;';
            $total->add(array_sum((array) $values));
        
            return $total;
        });          


        // add the columns to the DataGrid
        $this->datagridEstruturaCurricular->addColumn($column_etapa);
        $this->datagridEstruturaCurricular->addColumn($column_tipo);
        $this->datagridEstruturaCurricular->addColumn($column_cod_disciplina_curriculo);
        $this->datagridEstruturaCurricular->addColumn($column_cod_disciplina_grade_etapa);
        //$this->datagridEstruturaCurricular->addColumn($column_ch_hora_aula);
        $this->datagridEstruturaCurricular->addColumn($column_ch_hora_relogio);
        //$this->datagridEstruturaCurricular->addColumn($column_ch_hora_aula_extensao);
        $this->datagridEstruturaCurricular->addColumn($column_ch_hora_relogio_extensao);
        $this->datagridEstruturaCurricular->addColumn($column_disciplina_pre_requisitada);
        $this->datagridEstruturaCurricular->addColumn($column_etiqueta);
        $this->datagridEstruturaCurricular->addColumn($column_area_formacao);


        $action_editar_grade = new TDataGridAction([$this, 'onSetDadosEditGrade'], ['id'=>'{id}']);        
        $this->datagridEstruturaCurricular->addAction($action_editar_grade, 'Editar', 'fas:pencil-alt orange');
        
        
        $action_excluir_grade = new TDataGridAction([$this, 'onDeleteGrade'], ['id'=>'{id}']);              
        $action_excluir_grade->setDisplayCondition( array($this, 'displayColumnDeleteGrade') );
        $this->datagridEstruturaCurricular->addAction($action_excluir_grade, 'Excluir', 'far:trash-alt red');
        

        // create the datagrid model
        $this->datagridEstruturaCurricular->createModel();
        
        
        // creates the page navigation
        $this->pageNavigationEstruturaCurricular = new TPageNavigation;
        $this->pageNavigationEstruturaCurricular->setAction(new TAction([$this, 'onReloadDisciplinasGrade']));
        $this->pageNavigationEstruturaCurricular->setWidth($this->datagridEstruturaCurricular->getWidth());
        

        $panel1 = new TPanelGroup("Estrutura Curricular : $curso->nome_curso_diploma - Currículo: $curriculo->codigo_curriculo");
        $panel1->add($this->datagridEstruturaCurricular);
        $panel1->addFooter($this->pageNavigationEstruturaCurricular);
        
        $btn = $panel1->addHeaderActionLink('Voltar', new TAction(['CurriculoList', 'onReload']), 'fa:arrow-left white');
        $btn->class = 'btn btn-primary';


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
          

        //DATAGRID OPTATIVAS

         // creates a Datagrid
        $this->datagridOptativas = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagridOptativas->style = 'width: 100%';
        //$this->datagridOptativas->datatable = 'true';  
        $this->datagridOptativas->disableDefaultClick();            


        // creates the datagrid columns
        $column_etapa = new TDataGridColumn('', '', 'center');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'center');
        $column_cod_disciplina_curriculo = new TDataGridColumn('cod_disciplina_curriculo', 'Cód. no Currículo', 'right');
        $column_nome = new TDataGridColumn('nome', 'Unidade Curricular', 'left');
        $column_ch_hora_aula = new TDataGridColumn('ch_hora_aula', 'CH Hora/Aula', 'center');
        $column_ch_hora_relogio = new TDataGridColumn('ch_hora_relogio', 'CH', 'center');
        $column_ch_hora_aula_extensao = new TDataGridColumn('ch_hora_aula_extensao', 'CH Hora/Aula Extensão', 'center');
        $column_ch_hora_relogio_extensao = new TDataGridColumn('ch_hora_relogio_extensao', 'CH Extensão', 'center');
        $column_etiqueta = new TDataGridColumn('etiqueta', 'Etiqueta(s)', 'left', '16%');
        $column_area_formacao = new TDataGridColumn('area_formacao', 'Área(s)', 'center');
        
        
        $column_etiqueta->setTransformer( array($this, 'setEtiquetaDisciplina') );        
        $column_area_formacao->setTransformer( array($this, 'setAreaDisciplina') );        
                        
        
        // add the columns to the DataGrid
        $this->datagridOptativas->addColumn($column_etapa);
        $this->datagridOptativas->addColumn($column_tipo);
        $this->datagridOptativas->addColumn($column_cod_disciplina_curriculo);
        $this->datagridOptativas->addColumn($column_nome);
        //$this->datagridOptativas->addColumn($column_ch_hora_aula);
        $this->datagridOptativas->addColumn($column_ch_hora_relogio);
        //$this->datagridOptativas->addColumn($column_ch_hora_aula_extensao);
        $this->datagridOptativas->addColumn($column_ch_hora_relogio_extensao);
        $this->datagridOptativas->addColumn($column_etiqueta);
        $this->datagridOptativas->addColumn($column_area_formacao);
        
        
        $action_editar_optativa = new TDataGridAction([$this, 'onSetDadosEditOptativa'], ['id'=>'{id}']);        
        $this->datagridOptativas->addAction($action_editar_optativa, 'Editar', 'fas:pencil-alt orange');
        
        
        $action_excluir_optativa = new TDataGridAction([$this, 'onDeleteOptativa'], ['id'=>'{id}']);              
        $action_excluir_optativa->setDisplayCondition( array($this, 'displayColumnDeleteOptativa') );
        $this->datagridOptativas->addAction($action_excluir_optativa, 'Excluir', 'far:trash-alt red');
                        
       
        // create the datagrid model
        $this->datagridOptativas->createModel();
        
        
        // creates the page navigation
        $this->pageNavigationOptativas = new TPageNavigation;
        $this->pageNavigationOptativas->setAction(new TAction([$this, 'onReloadOptativas']));
        $this->pageNavigationOptativas->setWidth($this->datagridOptativas->getWidth());
        

        $panel2 = new TPanelGroup('Opções de Optativas');
        $panel2->add($this->datagridOptativas);
        $panel2->addFooter($this->pageNavigationOptativas);
        
        $btn = $panel2->addHeaderActionLink('Voltar', new TAction(['CurriculoList', 'onReload']), 'fa:arrow-left white');
        $btn->class = 'btn btn-primary';
        
                
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add(TPanelGroup::pack('', $panel1, $panel2));
        
        parent::add($container);
    }


    public static function onSetDadosEditGrade($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //Retorna para CurriculoList ou EstruturaCurricularList dependendo de qual deles chamou a função
            $datagrid_origem = "EstruturaCurricularList";
                                
            TSession::setValue('datagrid_origem', NULL);
            TSession::setValue('datagrid_origem', $datagrid_origem);
            
            $curriculo_disciplina_id = $param['id'];
            
            $curriculo_disciplina = new CurriculoDisciplina($curriculo_disciplina_id);
                                
            $parametros['key'] = $curriculo_disciplina_id;
            $parametros['curriculo_id'] = $curriculo_disciplina->curriculo_id;
                                 
            TApplication::loadPage('DisciplinaGradeCurriculoForm', 'onEdit', $parametros);
        
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    
    
    /*Se a disciplina foi inserida no XML, não permite exclusão, mesmo que o status de publicação do currículo seja = 0,
    pois pode se tratar de uma reemissão (situação em que o status de publicação pode voltar a 0 e a disciplina não pode ser retirada)*/
    public function displayColumnDeleteGrade( $object )
    {
        try
        {    
            TTransaction::open('Felabs_DB');
            
            //Se o usuário logado é do grupo Admin, exibe opção
            $grupo_admin = 1;
            $user_groups = TSession::getValue('usergroupids');
        
            if(in_array($grupo_admin, $user_groups))
            {
                $curriculo = new CurriculoDigital($object->curriculo_id);
                
                if($curriculo->status_xml <> 0)
                {
                    //Verifica se a disciplina está presente no XML
                    $target_file = $curriculo->caminho_arquivo. '/' . $curriculo->arquivo;
                    
                    $xml_curriculo = simplexml_load_file($target_file);
                    
                    foreach($xml_curriculo as $tags_curriculo) 
                    { 
                        foreach($tags_curriculo->infEstruturaCurricular as $tags_unidades) 
                        {   
                            foreach($tags_unidades as $tag_unidade)
                            {
                                $array_codigos[] = (string) $tag_unidade->Codigo;        
                            }
                        }    
                    }    
    
    
                    if(!in_array($object->cod_disciplina_curriculo, $array_codigos))
                    {
                        return TRUE;
                    }
                    else
                    {
                        return FALSE;
                    }
                }
                else
                {
                    return TRUE;
                }
            }
            
            return FALSE;
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public static function onDeleteGrade($param)
    {
        try
        {
            $action = new TAction(array(__CLASS__, 'DeleteGrade'));
            $action->setParameters($param); 
            
            TTransaction::open('Felabs_DB');
                
            $key = $param['key'];        
            $disciplina = new CurriculoDisciplina($key);
               
            //Não permite a exclusão se houver registros vinculados
            if((CurriculoDisciplinaArea::where('curriculo_disciplina_id', '=', $disciplina->id)->count() > 0) OR
               (CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $disciplina->id)->count() > 0) OR 
               (CurriculoDisciplinaRequisitada::where('curriculo_disciplina_dependente_id', '=', $disciplina->id)->count() > 0) OR
               (CurriculoDisciplinaRequisitada::where('curriculo_disciplina_requisitada_id', '=', $disciplina->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) à disciplina');
                return false;
            }                
            else
            {    
                new TQuestion('Caso os critérios de integralização já tenham sido definidos, precisarão ser refeitos após esta exclusão
                para garantir que o cômputo da carga horária do critério em que a unidade se enquadra fique correto. Deseja realmente excluir ?', $action);  
            } 
                
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    

    public static function DeleteGrade($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new CurriculoDisciplina($key, FALSE); 
            
            $curriculo_id = $object->curriculo_id; 
            
            //Apaga registros dependentes, depois a unidade
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('curriculo_disciplina_id', '=', $object->id));

            $etiquetas = CurriculoDisciplinaEtiqueta::getObjects($criteria1);
          
            if(!empty($etiquetas))
            {
                foreach($etiquetas as $etiqueta)
                {
                    $etiqueta->delete();
                }
            }
                        
            $areas = CurriculoDisciplinaArea::getObjects($criteria1);
            
            if(!empty($areas))
            {
                foreach($areas as $area)
                {
                    $area->delete();
                }
            }
            
            $criteria2 = new TCriteria;
            $criteria2->add(new TFilter('curriculo_disciplina_dependente_id', '=', $object->id), TExpression::OR_OPERATOR);
            $criteria2->add(new TFilter('curriculo_disciplina_requisitada_id', '=', $object->id), TExpression::OR_OPERATOR);
            
            $disciplinas_requisitadas = CurriculoDisciplinaRequisitada::getObjects($criteria2);
            
            if(!empty($disciplinas_requisitadas))
            {
                foreach($disciplinas_requisitadas as $disciplina_requisitada)
                {
                    $disciplina_requisitada->delete();
                }
            }
            
            $object->delete(); 
            
            TTransaction::close(); 
            
            $pos_action = new TAction([__CLASS__, 'onShow'], ['curriculo_id' => $curriculo_id]);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }

    
    public static function onSetDadosEditOptativa($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            //Retorna para CurriculoList ou EstruturaCurricularList dependendo de qual deles chamou a função
            $datagrid_origem = "EstruturaCurricularList";
                                
            TSession::setValue('datagrid_origem', NULL);
            TSession::setValue('datagrid_origem', $datagrid_origem);
            
            $curriculo_disciplina_id = $param['id'];
            
            $curriculo_disciplina = new CurriculoDisciplina($curriculo_disciplina_id);
                                
            $parametros['key'] = $curriculo_disciplina_id;
            $parametros['curriculo_id'] = $curriculo_disciplina->curriculo_id;
                                 
            TApplication::loadPage('DisciplinaOptativaCurriculoForm', 'onEdit', $parametros);
        
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }


    /*Se a disciplina foi inserida no XML, não permite exclusão, mesmo que o status de publicação do currículo seja = 0,
    pois pode se tratar de uma reemissão (situação em que o status de publicação pode voltar a 0 e a disciplina não pode ser retirada)*/
    public function displayColumnDeleteOptativa( $object )
    {
        try
        {    
            TTransaction::open('Felabs_DB');
            
            $curriculo = new CurriculoDigital($object->curriculo_id);
            
            if($curriculo->status_xml <> 0)
            {
                //Verifica se a disciplina está presente no XML
                $target_file = $curriculo->caminho_arquivo. '/' . $curriculo->arquivo;
                
                $xml_curriculo = simplexml_load_file($target_file);
                
                foreach($xml_curriculo as $tags_curriculo) 
                { 
                    foreach($tags_curriculo->infEstruturaCurricular as $tags_unidades) 
                    {
                        foreach($tags_unidades as $tag_unidade)
                        {
                            $array_codigos[] = (string) $tag_unidade->Codigo;        
                        }
                    }    
                }    


                if(!in_array($object->cod_disciplina_curriculo, $array_codigos))
                {
                    return TRUE;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                return TRUE;
            }      
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public static function onDeleteOptativa($param)
    {
        try
        {
            $action = new TAction(array(__CLASS__, 'DeleteOptativa'));
            $action->setParameters($param); 
            
            TTransaction::open('Felabs_DB');
                
            $key = $param['key'];        
            $disciplina = new CurriculoDisciplina($key);
               
            //Não permite a exclusão se houver registros vinculados
            if((CurriculoDisciplinaArea::where('curriculo_disciplina_id', '=', $disciplina->id)->count() > 0) OR
               (CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $disciplina->id)->count() > 0) OR 
               (CurriculoDisciplinaRequisitada::where('curriculo_disciplina_dependente_id', '=', $disciplina->id)->count() > 0) OR
               (CurriculoDisciplinaRequisitada::where('curriculo_disciplina_requisitada_id', '=', $disciplina->id)->count() > 0))
            {
                new TMessage('error','O registro não pode ser excluído, pois há dado(s) vinculado(s) à disciplina');
                return false;
            }                
            else
            {    
                new TQuestion('Caso os critérios de integralização já tenham sido definidos, precisarão ser refeitos após esta exclusão
                para garantir que o cômputo da carga horária do critério em que a unidade se enquadra fique correto. Deseja realmente excluir ?', $action);  
            } 
                
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }    
    }
    

    public static function DeleteOptativa($param)
    {
        try
        {
            $key = $param['key']; 
            
            TTransaction::open('Felabs_DB'); 
            
            $object = new CurriculoDisciplina($key, FALSE); 
            
            $curriculo_id = $object->curriculo_id; 
            
            //Apaga registros dependentes, depois a unidade
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('curriculo_disciplina_id', '=', $object->id));

            $etiquetas = CurriculoDisciplinaEtiqueta::getObjects($criteria1);
          
            if(!empty($etiquetas))
            {
                foreach($etiquetas as $etiqueta)
                {
                    $etiqueta->delete();
                }
            }
                        
            $areas = CurriculoDisciplinaArea::getObjects($criteria1);
            
            if(!empty($areas))
            {
                foreach($areas as $area)
                {
                    $area->delete();
                }
            }
            
            $criteria2 = new TCriteria;
            $criteria2->add(new TFilter('curriculo_disciplina_dependente_id', '=', $object->id), TExpression::OR_OPERATOR);
            $criteria2->add(new TFilter('curriculo_disciplina_requisitada_id', '=', $object->id), TExpression::OR_OPERATOR);
            
            $disciplinas_requisitadas = CurriculoDisciplinaRequisitada::getObjects($criteria2);
            
            if(!empty($disciplinas_requisitadas))
            {
                foreach($disciplinas_requisitadas as $disciplina_requisitada)
                {
                    $disciplina_requisitada->delete();
                }
            }
            
            $object->delete(); 
            
            TTransaction::close(); 
            
            $pos_action = new TAction([__CLASS__, 'onShow'], ['curriculo_id' => $curriculo_id]);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }


    public function setPreRequisitoDisciplina($column_disciplina_pre_requisitada, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $pre_requisito = CurriculoDisciplinaRequisitada::where('curriculo_disciplina_dependente_id', '=', $object->id)->load();
                                                
            if($pre_requisito)
            {
                return '<span class="label label-success">SIM</span>';
            }
            else
            {
                return '<span class="label label-danger">NÃO</span>';
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function setEtiquetaDisciplina($column_etiqueta, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $objs = CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $object->id)
                                                ->orderBy('dados_etiqueta_id')
                                                ->load();
            
            $etiquetas = [];
            
            if($objs)
            {
                foreach($objs as $obj)
                {
                    $div = new TElement('span');
                    $div->style="padding: 5px; border-radius: 5px; color: white; background-color:" . $obj->etiqueta->color;
                    $div->add($obj->etiqueta->nome);
                               
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
    
    
    public function setAreaDisciplina($column_area_formacao, $object, $row)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $objs = CurriculoDisciplinaArea::where('curriculo_disciplina_id', '=', $object->id)
                                           ->orderBy('dados_area_formacao_id')
                                           ->load();
            
            $areas = [];
            
            if($objs)
            {
                foreach($objs as $obj)
                {
                    $areas[] = $obj->area_formacao->codigo;    
                }
                
                return implode(', ', $areas);
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
                    

    public function onReloadDisciplinasGrade($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $repository = new TRepository('CurriculoDisciplina');
            $limit = 500;
       
            $criteria = new TCriteria;            
            $criteria->add(new TFilter('curriculo_id', '=', $param['curriculo_id']));
            $criteria->add(new TFilter('opcao_disciplina', 'like', 'Grade'));
    
            if (empty($param['order']))
            {
                $param['order'] = 'etapa';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);


            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagridEstruturaCurricular->clear();
            $this->datagridEstruturaCurricular->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {   
                    $hr_relogio = substr($object->ch_hora_relogio, 0, -3);

                    $object->ch_hora_relogio = $hr_relogio;
                                        
                    //Coluna CH Extensão - a única etiqueta que receberá CH é a de extensão
                    $objs = CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $object->id)
                                                       ->where('ch_hora_relogio', 'IS NOT', NULL)
                                                       ->load();
        
                    if($objs)
                    {
                        foreach($objs as $obj)
                        {
                            $ha_extensao = $obj->ch_hora_aula;
                            $hr_extensao = (int) $obj->ch_hora_relogio;     
                        }
        
                        $object->ch_hora_aula_extensao = $ha_extensao;
                        $object->ch_hora_relogio_extensao = $hr_extensao;
                    }
                    else
                    {
                        $object->ch_hora_aula_extensao = ''; 
                        $object->ch_hora_relogio_extensao = '';   
                    }
                                        
                    $this->datagridEstruturaCurricular->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigationEstruturaCurricular->setCount($count); 
            $this->pageNavigationEstruturaCurricular->setProperties($param); 
            $this->pageNavigationEstruturaCurricular->setLimit($limit); 
            
            TTransaction::close();
            $this->loadedEstruturaCurricular = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
    public function onReloadOptativas($param = NULL)
    {
        try
        {
            TTransaction::open('Felabs_DB');

            $repository = new TRepository('CurriculoDisciplina');
            $limit = 500;       

            $criteria = new TCriteria;            
            $criteria->add(new TFilter('curriculo_id', '=', $param['curriculo_id']));
            $criteria->add(new TFilter('opcao_disciplina', '=', 'Optativa'));
    
            if (empty($param['order']))
            {
                $param['order'] = 'ch_hora_relogio';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);


            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagridOptativas->clear();
            $this->datagridOptativas->disableHtmlConversion();
            
            if ($objects)
            {
                foreach ($objects as $object)
                {   
                    $hr_relogio = substr($object->ch_hora_relogio, 0, -3);

                    $object->ch_hora_relogio = $hr_relogio;
                                        
                    //Coluna CH Extensão - a única etiqueta que receberá CH é a de extensão
                    $objs = CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $object->id)
                                                       ->where('ch_hora_relogio', 'IS NOT', NULL)
                                                       ->load();
        
                    if($objs)
                    {
                        foreach($objs as $obj)
                        {
                            $ha_extensao = $obj->ch_hora_aula;
                            $hr_extensao = (int) $obj->ch_hora_relogio;     
                        }
        
                        $object->ch_hora_aula_extensao = $ha_extensao;
                        $object->ch_hora_relogio_extensao = $hr_extensao;
                    }
                    else
                    {
                        $object->ch_hora_aula_extensao = ''; 
                        $object->ch_hora_relogio_extensao = '';   
                    }
                                        
                    $this->datagridOptativas->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigationOptativas->setCount($count); 
            $this->pageNavigationOptativas->setProperties($param); 
            $this->pageNavigationOptativas->setLimit($limit); 
            
            TTransaction::close();
            $this->loadedOptativas = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function onShow( $param )
    {   

        $this->onReloadDisciplinasGrade($param);
        $this->onReloadOptativas($param);  
    }
    

    public function show()
    {
        parent::show();
    }
}
