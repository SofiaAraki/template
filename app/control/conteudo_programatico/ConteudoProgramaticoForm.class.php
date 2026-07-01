<?php

class ConteudoProgramaticoForm extends TPage
{
    protected $form; 
    protected $detail_list;
    

    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ConteudoProgramatico');
        $this->form->setFormTitle('Conteúdo Programático');
        
        // master fields
        $id = new TEntry('id');
        $curso = new TEntry('curso');
        $disciplina = new TCombo('disciplina');
        $etapa = new THidden('etapa');
        $turma = new TEntry('turma');
        $status = new THidden('status');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');

        $copia_disciplina = new TCombo('copia_disciplina');

        $curso->setEditable(FALSE);
        $turma->setEditable(FALSE);


        TTransaction::open('Felabs_DB');
        
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        $loggedUnitProf = TSession::getValue('userunitid');
        
        TTransaction::open('dados_fei');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf));
        $criteria->add(new TFilter('IdProfessor', '=', $user->id_legado));
        
        $disciplinas = VwProfessordisciplinassemestre::getObjects($criteria);
        
        $combo_items = array();
        if($disciplinas)
        {
            foreach($disciplinas as $objDisc)
            {
                $combo_items[$objDisc->CodGradeDisciplinaEtapaFrente] = $objDisc->NomeDisciplina.' - '.$objDisc->Turma;
            }
        }
        $disciplina->addItems($combo_items);
        
        
        $criteriaCopia = new TCriteria;
        $criteriaCopia->add(new TFilter('system_user_id', '=', $userid));
        $conteudos_copia = ConteudoProgramatico::getObjects($criteriaCopia);
        
        $combo_copia = array();
        if($conteudos_copia)
        {
            foreach($conteudos_copia as $objCopia)
            {
                TTransaction::open('dados_fei');
                $criteriaC = new TCriteria;
                $criteriaC->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $objCopia->disciplina));
                $discNome = VwProfessordisciplinassemestre::getObjects($criteriaC);
                
                $nomeD = !empty($discNome) ? $discNome[0]->NomeDisciplina : $objCopia->disciplina;
                TTransaction::close();
                
                $combo_copia[$objCopia->id] = 'ID: '.$objCopia->id.' - '.$nomeD.' - '.$objCopia->turma;
            }
        }
        $copia_disciplina->addItems($combo_copia);
        
        TTransaction::close();
        
        $id->setEditable(FALSE);
        
        // add the fields
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Cópia')], [$copia_disciplina] );
        $this->form->addFields( [new TLabel('Curso')], [$curso] );
        $this->form->addFields( [new TLabel('Disciplina')], [$disciplina] );
        $this->form->addFields( [$etapa] );
        $this->form->addFields( [new TLabel('Turma')], [$turma] );
        $this->form->addFields( [$status] );
        $this->form->addFields( [$system_user_id] );
        $this->form->addFields( [$data_reg] );

        $id->setSize('100%');
        $curso->setSize('100%');
        $disciplina->setSize('100%');
        $turma->setSize('100%');
        $copia_disciplina->setSize('100%');
        
        // detail fields
        $this->detail_list = new元素('div');
        $this->detail_list->id = 'detail_list';
        
        $detail_id = new THidden('detail_id[]');
        $detail_data_aula = new TDate('detail_data_aula[]');
        $detail_conteudo = new TText('detail_conteudo[]');
        
        $detail_data_aula->setMask('dd/mm/yyyy');
        
        $this->form->addContent( [ TElement::tag('h5', 'Itens do Conteúdo Programático', array('style'=>'background: #f5f5f5; padding: 5px; margin-bottom: 5px; font-weight: bold')) ] );
        
        $table_id = 'table_ConteudoProgramaticoItem';
        $this->form->addContent( [ TElement::tag('table', 
            TElement::tag('tr', 
                TElement::tag('th', 'Data da Aula', array('width'=>'20%')).
                TElement::tag('th', 'Conteúdo ministrado', array('width'=>'75%')).
                TElement::tag('th', '&nbsp;', array('width'=>'5%'))
            ).
            TElement::tag('tr', 
                TElement::tag('td', $detail_data_aula).
                TElement::tag('td', $detail_conteudo).
                TElement::tag('td', TElement::tag('button', '<i class="fa fa-plus green"></i>', array('class'=>'btn btn-default', 'onclick'=>"ttable_add_row('{$table_id}')")))
            , array('class'=>'ttable_actions_row')), array('id'=>$table_id, 'class'=>'ttable', 'style'=>'width:100%')) ] );
            
        $this->form->addContent( [$this->detail_list] );
        
        $copia_disciplina->setChangeAction(new TAction(array($this, 'onCopia')));
        $disciplina->setChangeAction(new TAction(array($this, 'onChangeDisciplina')));
        
        // create the form actions
        $this->form->addHeaderAction('Salvar', new TAction(array($this, 'onSave')), 'fa:floppy-o bridge blue');
        
        if($user->funcao_legado == 'Professor')
        {
            $this->form->addHeaderAction('Voltar', new TAction(['ConteudoProgramaticoList','onReload']), 'far:arrow-left blue');
        }
        else
        {
            $this->form->addHeaderAction('Voltar', new TAction(['ConteudoProgramaticoListAll','onReload']), 'far:arrow-left blue');
        }
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        
        // AJUSTADO: Usa ConteudoProgramaticoListAll para evitar quebras dinâmicas de rota no menu.xml
        $container->add(new TXMLBreadCrumb('menu.xml', 'ConteudoProgramaticoListAll'));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    public static function onChangeDisciplina($param)
    {
        try
        {
            TTransaction::open('dados_fei');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $param['disciplina']));
            
            $disciplinas = VwProfessordisciplinassemestre::getObjects($criteria);
            
            if($disciplinas)
            {
                $obj = new stdClass;
                $obj->curso = $disciplinas[0]->NomeCurso;
                $obj->turma = $disciplinas[0]->Turma;
                $obj->etapa = $disciplinas[0]->CodEtapa;
                $obj->status = 'Pendente';
                
                TForm::sendData('form_ConteudoProgramatico', $obj);
            }
            TTransaction::close();
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function onCopia($param)
    {
        try
        {
            if(!empty($param['copia_disciplina']))
            {
                TTransaction::open('Felabs_DB');
                
                $criteria = new TCriteria;
                $criteria->add(new TFilter('conteudo_programatico_id', '=', $param['copia_disciplina']));
                $criteria->setProperty('order', 'data_aula');
                $criteria->setProperty('direction','ASC');
                $itens_copia = ConteudoProgramaticoItem::getObjects($criteria);
                
                TTransaction::close();
                
                $table_id = 'table_ConteudoProgramaticoItem';
                TScript::create("ttable_clear('{$table_id}')");
                
                if($itens_copia)
                {
                    foreach($itens_copia as $item)
                    {
                        $data_aula = TDate::date2br($item->data_aula);
                        $conteudo = $item->conteudo;
                        
                        $row =  "<td><input type='text' name='detail_data_aula[]' class='form-control tdate-field' value='{$data_aula}'></td>".
                                "<td><textarea name='detail_conteudo[]' class='form-control' rows='3'>{$conteudo}</textarea></td>".
                                "<td><button type='button' class='btn btn-default' onclick='ttable_remove_row(this)'><i class='fa fa-trash red'></i></button></td>";
                                
                        TScript::create("ttable_add_custom_row('{$table_id}', \"{$row}\")");
                    }
                }
            }
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key']; 
                TTransaction::open('Felabs_DB'); 
                $object = new ConteudoProgramatico($key); 
                
                $mes = date("m", strtotime($object->data_reg));
                $ano = date("Y", strtotime($object->data_reg));
                $semestre = ($mes < 8) ? 1 : 2;
                $loggedUnitProf = TSession::getValue('userunitid');

                TTransaction::open('dados_fei');
                $criteria = new TCriteria;
                $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina));
                $criteria->add(new TFilter('Ano', '=', $ano));
                $criteria->add(new TFilter('Semestre', '=', $semestre));
                $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf));

                $nomesdisc = VwProfessordisciplinassemestre::getObjects($criteria);
                
                if (!empty($nomesdisc) && isset($nomesdisc[0])) {
                    $object->nome_disciplina = $nomesdisc[0]->NomeDisciplina;
                } else {
                    $object->nome_disciplina = "Disciplina Cód: " . $object->disciplina;
                }
                TTransaction::close();
                
                $this->form->setData($object); 
                
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('conteudo_programatico_id', '=', $object->id));
                $criteria1->setProperty('order', 'data_aula');
                $criteria1->setProperty('direction','ASC');
                $items = ConteudoProgramaticoItem::getObjects($criteria1);
                
                $table_id = 'table_ConteudoProgramaticoItem';
                if($items)
                {
                    foreach($items as $item)
                    {
                        $data_aula = TDate::date2br($item->data_aula);
                        $conteudo = addslashes($item->conteudo);
                        $conteudo = str_replace(array("\r", "\n"), array("", " "), $conteudo);
                        
                        $row =  "<td><input type='hidden' name='detail_id[]' value='{$item->id}'><input type='text' name='detail_data_aula[]' class='form-control tdate-field' value='{$data_aula}'></td>".
                                "<td><textarea name='detail_conteudo[]' class='form-control' rows='3'>{$conteudo}</textarea></td>".
                                "<td><button type='button' class='btn btn-default' onclick='ttable_remove_row(this)'><i class='fa fa-trash red'></i></button></td>";
                                
                        TScript::create("ttable_add_custom_row('{$table_id}', \"{$row}\")");
                    }
                }
                
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
    
    public function onSave($param)
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $id = $param['id'];
            $master = new ConteudoProgramatico($id);
            $master->id = $param['id'];
            $master->curso = $param['curso'];
            $master->disciplina = $param['disciplina'];
            $master->etapa = $param['etapa'];
            $master->turma = $param['turma'];
            $master->status = !empty($param['status']) ? $param['status'] : 'Pendente';
            $master->system_user_id = !empty($param['system_user_id']) ? $param['system_user_id'] : TSession::getValue('userid');
            $master->data_reg = !empty($param['data_reg']) ? $param['data_reg'] : date('Y-m-d');
            
            $master->store(); 
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('conteudo_programatico_id', '=', $master->id));
            $old_items = ConteudoProgramaticoItem::getObjects($criteria);
            
            $keep_items = array();
            
            if(!empty($param['detail_data_aula']))
            {
                foreach($param['detail_data_aula'] as $key => $data_aula)
                {
                    if(empty($param['detail_id'][$key]))
                    {
                        $detail = new ConteudoProgramaticoItem;
                    }
                    else
                    {
                        $detail = ConteudoProgramaticoItem::find($param['detail_id'][$key]);
                    }
                    $detail->data_aula  = TDate::date2us($data_aula);
                    $detail->conteudo  = $param['detail_conteudo'][$key];
                    $detail->conteudo_programatico_id = $master->id;
                    $detail->store();
                    
                    $keep_items[] = $detail->id;
                }
            }
            
            if ($old_items)
            {
                foreach ($old_items as $old_item)
                {
                    if (!in_array( $old_item->id, $keep_items))
                    {
                        $old_item->delete();
                    }
                }
            }
            
            TTransaction::close(); 
            
            $this->onEdit(array('key'=>$master->id));
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            if (func_num_args() > 0) {
                $this->onReload(func_get_arg(0));
            }
        }
        parent::show();
    }
}