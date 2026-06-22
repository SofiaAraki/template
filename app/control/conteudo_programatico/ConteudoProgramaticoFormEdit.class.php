<?php
class ConteudoProgramaticoFormEdit extends TPage
{
    protected $form; 
    protected $detail_list;

    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ConteudoProgramatico');
        $this->form->setFormTitle('Conteudo Programático');
        
        // master fields
        $id = new TEntry('id');
        $curso = new TEntry('curso');
        $disciplina = new THidden('disciplina');
        $nome_disciplina = new TEntry('nome_disciplina');
        $etapa = new THidden('etapa');
        $turma = new TEntry('turma');
        $status = new THidden('status');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');

        $copia_disciplina = new TCombo('copia_disciplina');

        $curso->setEditable(FALSE);
        $turma->setEditable(FALSE);
        $nome_disciplina->setEditable(FALSE);

        TTransaction::open('Felabs_DB');
        
        $loggedProf = SystemUser::newFromLogin(TSession::getValue('login'));
        $userid = TSession::getValue('userid');
        $user = new SystemUser($userid);
        $loggedUnitProf = TSession::getValue('userunitid');
        
        TTransaction::close();


        TTransaction::open('dados_fei');

        $repository = new TRepository('VwProfessordisciplinassemestre');

        $ano = date('Y');
        $mes = date('m');

        if($mes < 8)
        {
            $semestre = 1;
        }
        elseif($mes > 7)
        {
            $semestre = 2;
        }
            
        // creates a criteria
        $criteria = new TCriteria;
            
        $criteria->add(new TFilter('CodProfessor', '=', $loggedProf-> systemuser_codlegado));
        $criteria->add(new TFilter('Ano', '=', $ano), TExpression::AND_OPERATOR);
        $criteria->add(new TFilter('Semestre', '=', $semestre), TExpression::AND_OPERATOR);
        $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf), TExpression::AND_OPERATOR);
       
        $repo = $repository->load($criteria);
      
        $items = [];
        $i = 0;
        foreach($repo as $row)
        {
            $stringCodDisciplina = $repo[$i]->CodGradeDisciplinaEtapaFrente;

            $items["$stringCodDisciplina"] = $repo[$i]->NomeDisciplina. ' - ' .$repo[$i]->Identificacao.' - '.$repo[$i]->CodGradeDisciplinaEtapaFrente;
            $i++;
        }

        // detail fields
        $detail_id = new THidden('detail_id');
        $detail_data_aula = new TDate('detail_data_aula');
        $detail_conteudo = new TText('detail_conteudo');

        $detail_data_aula->setMask('dd/mm/yyyy');
        $detail_data_aula->setDatabaseMask('yyyy-mm-dd');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        // master fields
        $this->form->addFields( [new TLabel('Id')], [$id] );       
        $this->form->addFields( [new TLabel('Disciplina')], [$nome_disciplina] );
        $this->form->addFields( [new TLabel('Curso')], [$curso],[new TLabel('Turma')], [$turma] );
        $this->form->addFields([$disciplina] );
        $this->form->addFields([$etapa] );
        $this->form->addFields([$status] );
        $this->form->addFields([$system_user_id] );
        $this->form->addFields([$data_reg] );

        $curso->setSize('100%');
        $disciplina->setSize('100%');
        $turma->setSize('100%');
        
        // detail fields
        $this->form->addContent( ['<h4>Conteúdo por Data</h4><hr>'] );
        $this->form->addFields( [$detail_id] );
        $this->form->addFields( [new TLabel('Data Aula')], [$detail_data_aula] );
        $this->form->addFields( [new TLabel('Conteudo')], [$detail_conteudo] );

        $add = TButton::create('add', [$this, 'onSaveDetail'], 'Adicionar', 'fa:plus');
        $this->form->addFields( [], [$add] )->style = 'background: whitesmoke; padding: 5px; margin: 1px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->detail_list->setId('ConteudoProgramatico_list');

        // items
        $this->detail_list->addQuickColumn('Data Aula', 'data_aula', 'left', 50);
        $this->detail_list->addQuickColumn('Conteudo', 'conteudo', 'left', 100);

        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction([$this, 'onEditDetail']),   'id', 'fa:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'id', 'fas:trash-alt red');
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );

        //$btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        //$btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction( _t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Voltar',new TAction(['ConteudoProgramaticoList','onReload']),'far:arrow-alt-circle-left blue');

        $copia_disciplina->addItems($items);
        $this->form->addFields( [new TLabel('Copiar Conteúdo para Disciplina:','#FF0000')], [$copia_disciplina] );

        $change_action_copia = new TAction(array($this, 'onChangeActionCopia'));
        $copia_disciplina->setChangeAction($change_action_copia);
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ConteudoProgramaticoList'));
        $container->add($this->form);
        parent::add($container);
    }

    public static function onChangeActionCopia($param)
    {
        // define the delete action
        $action = new TAction(array(__CLASS__, 'ChangeActionCopia'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(('Deseja copiar o conteúdo lançado para a disciplina escolhida?'), $action);
    }

    public static function ChangeActionCopia($param)
    {
        try
        {
            TTransaction::open('dados_fei');
        
            $repository = new TRepository('VwProfessordisciplinassemestre');
            $disciplinas = $repository  ->where('CodGradeDisciplinaEtapaFrente',  '=', $param['key'])
                                        ->load();

            foreach ($disciplinas as $disciplina)
            {
                $Curso =            $disciplina->NomeCurso;
                $DisciplinaCopia =  $disciplina->CodGradeDisciplinaEtapaFrente;
                $Etapa =            $disciplina->Etapa;
                $Turma =            $disciplina->Identificacao;
            }

            TTransaction::open('Felabs_DB');
                $copia = new ConteudoProgramatico;
                $copia->system_user_id = TSession::getValue('userid');
                $copia->data_reg = date('Y-m-d');;
                $copia->curso =  $Curso;
                $copia->disciplina = $DisciplinaCopia;
                $copia->etapa = $Etapa;
                $copia->turma = $Turma;
                $copia->store();

                $ID_copia = $copia->id;
            
                $copia_detalhe = ConteudoProgramaticoItem::where('conteudo_programatico_id', '=', $param["id"])->load(); 

                foreach($copia_detalhe as $std)
                {          
                    $copia_item = new ConteudoProgramaticoItem;
                    $copia_item->data_aula = $std->data_aula;
                    $copia_item->conteudo = $std->conteudo;
                    $copia_item->conteudo_programatico_id = $ID_copia;
                    $copia_item->store();
                }

                new TMessage('info', "Conteúdo copiado com sucesso! <br> Verifique se todas as informações foram copiadas corretamente na disciplina de destino.");
                
            TTransaction::close();
            TTransaction::close();
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onChangeAction($param)
    {
        TTransaction::open('dados_fei');
     
        $repository = new TRepository('VwProfessordisciplinassemestre');

        $ano = date('Y');
        $mes = date('m');

        if($mes < 8)
        {
            $semestre = 1;
        }
        elseif($mes > 7)
        {
            $semestre = 2;
        }
            
        // creates a criteria
        $criteria = new TCriteria;
        $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $param['disciplina']));
        $criteria->add(new TFilter('Ano', '=', $ano), TExpression::AND_OPERATOR);//$ano
        $criteria->add(new TFilter('Semestre', '=', $semestre), TExpression::AND_OPERATOR);//$semestre

        $repo = $repository->load($criteria);

        $obj = new StdClass;
        $obj->curso = $repo[0]->NomeCurso;
        $obj->turma = $repo[0]->Identificacao;
        TForm::sendData('form_ConteudoProgramatico', $obj);

        TTransaction::close();
    }
    

    public function onClear($param)
    {
        $this->form->clear(TRUE);
        TSession::setValue(__CLASS__.'_items', array());
        $this->onReload( $param );
    }


    public function onSaveDetail( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            $data = $this->form->getData();
            
            $items = TSession::getValue(__CLASS__.'_items');
            $key = empty($data->detail_id) ? 'X'.mt_rand(1000000000, 1999999999) : $data->detail_id;
            
            $items[ $key ] = array();
            $items[ $key ]['id'] = $key;
            $items[ $key ]['data_aula'] = $data->detail_data_aula;
            $items[ $key ]['conteudo'] = $data->detail_conteudo;
            
            TSession::setValue(__CLASS__.'_items', $items);
            
            // clear detail form fields
            $data->detail_id = '';
            $data->detail_data_aula = '';
            $data->detail_conteudo = '';
            
            TTransaction::close();
            $this->form->setData($data);

            $this->onSave();
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onEditDetail( $param )
    {
        $items = TSession::getValue(__CLASS__.'_items');
        
        $item = $items[ $param['key'] ];
        
        $data = new stdClass;
        $data->detail_id = $item['id'];
        $data->detail_data_aula = $item['data_aula'];
        $data->detail_conteudo = $item['conteudo'];
        
        TForm::sendData( 'form_ConteudoProgramatico', $data );
    }

    public function onDeleteDetail( $param )
    {
        TTransaction::open('Felabs_DB'); 
        
        $object = new ConteudoProgramaticoItem($param['key']);

        $parametro = [];
        $parametro['key'] = $object->conteudo_programatico_id;

        $object->delete(); 
        TTransaction::close(); 
        
        new TMessage('info','Conteúdo removido');

        TApplication::loadPage('ConteudoProgramaticoForm', 'onEdit', $parametro);
    }

    public function onReload($param)
    {
        $items = TSession::getValue(__CLASS__.'_items');
        
        $this->detail_list->clear();
        
        if ($items)
        {
            foreach ($items as $list_item)
            {
                $item = (object) $list_item;
                
                $row = $this->detail_list->addItem( $item );
                $row->id = $list_item['id'];
            }
        }
        
        $this->loaded = TRUE;
    }   

    public function onEdit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
         
            $loggedUnitProf = TSession::getValue('userunitid');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new ConteudoProgramatico($key);

                $mes = date("m", strtotime($object->data_reg));
                $ano = date("Y", strtotime($object->data_reg));

                if($mes < 8)
                {
                    $semestre = 1;
                }
                elseif($mes > 7)
                {
                    $semestre = 2;
                }

                TTransaction::close();

                TTransaction::open('dados_fei');

                $criteria = new TCriteria;
            
                $criteria->add(new TFilter('CodGradeDisciplinaEtapaFrente', '=', $object->disciplina),TExpression::AND_OPERATOR);
                $criteria->add(new TFilter('Ano', '=', $ano), TExpression::AND_OPERATOR);
                $criteria->add(new TFilter('Semestre', '=', $semestre), TExpression::AND_OPERATOR);
                $criteria->add(new TFilter('CodEntidade', '=', $loggedUnitProf), TExpression::AND_OPERATOR);

                $nomesdisc = VwProfessordisciplinassemestre::getObjects($criteria);

                $object->nome_disciplina = $nomesdisc[0]->NomeDisciplina;   

                TTransaction::close();
                

                TTransaction::open('Felabs_DB');

                $items  = ConteudoProgramaticoItem::where('conteudo_programatico_id', '=', $key)->load();
                
                $session_items = array();
                
                foreach( $items as $item )
                {
                    $item_key = $item->id;
                    $session_items[$item_key] = $item->toArray();
                    $session_items[$item_key]['id'] = $item->id;
                    $session_items[$item_key]['data_aula'] = $item->data_aula;
                    $session_items[$item_key]['conteudo'] = $item->conteudo;
                
                }
                TSession::setValue(__CLASS__.'_items', $session_items);
                
                $this->form->setData($object); 
                $this->onReload( $param ); 
                TTransaction::close(); 
            }
            else
            {
                $this->form->clear(TRUE);
                TSession::setValue(__CLASS__.'_items', null);
                $this->onReload( $param );
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    } 

    public function onSave()
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $data = $this->form->getData();
            $master = new ConteudoProgramatico;

            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);    

            if(date('m') < 8)
            {
                $data->etapa = 1;
            }
            elseif(date('m') > 8)
            {
                $data->etapa = 2;
            }

            $data->system_user_id = $user->id; 
            $data->data_reg = date('Y-m-d');


            $master->fromArray( (array) $data);
            $this->form->validate(); 
            
            $master->store(); 

            $old_items = ConteudoProgramaticoItem::where('conteudo_programatico_id', '=', $master->id)->load();
            
            $keep_items = array();
            
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id'],0,1) == 'X' ) // new record
                    {
                        $detail = new ConteudoProgramaticoItem;
                    }
                    else
                    {
                        $detail = ConteudoProgramaticoItem::find($item['id']);
                    }
                    $detail->data_aula  = $item['data_aula'];
                    $detail->conteudo  = $item['conteudo'];
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
            $this->form->setData( $this->form->getData() ); 
            TTransaction::rollback();
        }
    }

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
