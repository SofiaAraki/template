<?php

//Este formulário vai salvar as disciplinas optativas oferecidas pelo curso no currículo

class DisciplinaOptativaCurriculoForm extends TPage
{
    protected $form; 
    private $cod_disciplina;
    
    
    public function __construct( $param )
    {
        parent::__construct();


        //Salva a dagrid que chamou o formulário em uma variável de sessão para retornar à página correta.
        $datagrid_origem = TSession::getValue('datagrid_origem');
 
 
        try
        {
            $id_curriculo = $param['curriculo_id'];
            
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
        

        // creates the form
        $this->form = new BootstrapFormBuilder('form_CurriculoDisciplinaOptativa');
        $this->form->setFormTitle("Unidade Curricular : $curso->nome_curso_diploma - Currículo: $curriculo->codigo_curriculo");
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');
        $curriculo_id = new THidden('curriculo_id');
        $tipo = new TCombo('tipo');
        $opcao_disciplina = new TEntry('opcao_disciplina');
        $this->cod_disciplina = new TDBSeekButton('cod_disciplina', 'dados_fei', 'form_CurriculoDisciplinaOptativa', 'FiDisciplina', 'NomeOficial');
        $cod_disciplina_grade_etapa = new THidden('cod_disciplina_grade_etapa');                    
        $cod_disciplina_curriculo = new TEntry('cod_disciplina_curriculo');        
        $nome = new TEntry('nome');  
        $etapa = new THidden('etapa');
        $opcao_carga_horaria = new TRadioGroup('opcao_carga_horaria');
        $ch_hora_aula = new TEntry('ch_hora_aula');
        $ch_hora_relogio = new TEntry('ch_hora_relogio');
        $ementa = new TText('ementa');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');     


        //Componentes auxiliares para exibição, não serão salvos no banco
        $nome_curso = new TEntry('nome_curso');
        $cod_grade = new TEntry('cod_grade');
        $cod_curriculo = new TEntry('cod_curriculo');
        

        $this->cod_disciplina->setExitAction(new TAction(array($this, 'onExitDisciplina')));        


        //Tipos (definidos pelo MEC)
        $combo_tipo = [];
        $combo_tipo['Disciplina'] = "Disciplina";
        $combo_tipo['Módulo'] = "Módulo";
        $combo_tipo['Atividade'] = "Atividade";
        $combo_tipo['Estágio'] = "Estágio";
        $combo_tipo['Trabalho de Conclusão de Curso'] = "Trabalho de Conclusão de Curso";
        $combo_tipo['Monografia'] = "Monografia";
        $combo_tipo['Artigo'] = "Artigo";
        $combo_tipo['Projeto'] = "Projeto";
        $combo_tipo['Produto'] = "Produto";
        $combo_tipo['Atividade Complementar'] = "Atividade Complementar";
        $combo_tipo['Atividade de Extensão'] = "Atividade de Extensão";
                
        $tipo->addItems($combo_tipo);
        
        
        //Opção CH
        $radio_opcao_ch = [];
        $radio_opcao_ch['Hora/Aula'] = "Hora/Aula";
        $radio_opcao_ch['Hora/Relógio'] = "Hora/Relógio";
                
        $opcao_carga_horaria->addItems($radio_opcao_ch);


        //Verifica a opção marcada, replica a carga horária no campo correspondente e calcula a conversão no outro         
        $opcao_carga_horaria->setChangeAction(new TAction(array($this, 'onChangeCargaHoraria')));
        

        //Se a unidade estiver sendo lançada em hora/aula e o campo for preenchido, ao sair, faz a conversão da hora/relógio
        $ch_hora_aula->setExitAction( new TAction( array($this, 'onCalculaHoraRelogio')));


        //Se a unidade estiver sendo lançada em hora/relógio e o campo for preenchido, ao sair, faz a conversão da hora/aula
        $ch_hora_relogio->setExitAction( new TAction( array($this, 'onCalculaHoraAula')));


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $curriculo_id ] );
        $this->form->addFields( [ $etapa ] );
        $this->form->addFields( [ $cod_disciplina_grade_etapa ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
        
        $row = $this->form->addFields( [ new TLabel('Curso'), $nome_curso ],
                                       [ new TLabel('Grade'), $cod_grade ],
                                       [ new TLabel('Currículo'), $cod_curriculo ]);
        $row->layout = ['col-sm-8', 'col-sm-2', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('Cód.'), $this->cod_disciplina ],
                                       [ new TLabel('Nome'), $nome ]);
        $row->layout = ['col-sm-3', 'col-sm-2', 'col-sm-7'];
        
        $row = $this->form->addFields( [ new TLabel('Lançamento da CH'), $opcao_carga_horaria ],
                                       [ new TLabel('CH em Hora/Aula'), $ch_hora_aula ],
                                       [ new TLabel('CH em Hora/Relógio'), $ch_hora_relogio ],
                                       [ new TLabel('Grade ou Optativa'), $opcao_disciplina ],
                                       [ new TLabel('Cód. da unidade no currículo'), $cod_disciplina_curriculo ]);
        $row->layout = ['col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Ementa'), $ementa ] );
        $row->layout = ['col-sm-12'];
        
        $this->form->addFields( [ '<br>' ] );
              
        
        //ETIQUETAS
        $label1 = new TLabel('Etiquetas', '#285097', 12, 'b', '<br>');
        $label1->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent( [$label1] );
        
        
        $btn_ex = new TButton('btn_ex');
        //$btn_ex->setLabel('EXEMPLO');
        $btn_ex->class = 'btn btn-primary';
        $btn_ex->style = 'color: white';
        $btn_ex->setAction(new TAction(array($this, 'onExemploEtiqueta')), 'EXEMPLO');
        /*$btn_ex->popover = 'true';
        $btn_ex->popside = 'right';
        $btn_ex->popcontent = "<p style='font-size: 14px'><b>Exemplo 1</b> - Disciplina obrigatória: aplica-se a etiqueta Obrigatória <br><br>
                               <b>Exemplo 2</b> - Disciplina optativa: aplica-se a etiqueta Optativa <br><br>
                               <b>Exemplo 3</b> - Disciplina obrigatória ou optativa cuja carga horária, total ou parcial, seja dedicada à extensão:
                               aplica-se a etiqueta Obrigatória <b>ou</b> a etiqueta Optativa e <b>aplica-se também</b> a etiqueta Extensão, neste
                               caso, informando as horas devidas para fins de cômputo da carga de extensão.</p>";*/
                   
                                               
        $this->form->addFields( [ new TLabel("Classificam as unidades no currículo. Uma unidade pode receber mais de uma etiqueta. Caso a carga 
        horária, total ou parcial, da unidade seja utilizada para cômputo da carga de Extensão do currículo, a mesma deve ser informada"), $btn_ex ] );        


        $etiqueta = new TDBCheckGroup('etiqueta', 'Felabs_DB', 'Etiqueta', 'id', 'nome');
        $ch_hora_aula_extensao = new TEntry('ch_hora_aula_extensao');
        $ch_hora_relogio_extensao = new TEntry('ch_hora_relogio_extensao');
        
        $ch_hora_aula_extensao->setMask('9!');
        $ch_hora_aula_extensao->setTip("Após o preenchimento, pressione a tecla TAB para que a carga horária em hora/relógio seja calculada");
        $ch_hora_relogio_extensao->setTip("Após o preenchimento, pressione a tecla TAB para que a carga horária em hora/aula seja calculada");
        
        
        //Se a etiqueta de extensão for marcada, habilita campo para preenchimento da carga horária        
        $etiqueta->setChangeAction(new TAction(array($this, 'onChangeEtiqueta')));
        
        
        //Se a unidade estiver sendo lançada em hora/aula e o campo for preenchido, ao sair, faz a conversão da hora/relógio
        $ch_hora_aula_extensao->setExitAction( new TAction( array($this, 'onCalculaExtensaoHoraRelogio')));
        
        
        //Se a unidade estiver sendo lançada em hora/relógio e o campo for preenchido, ao sair, faz a conversão da hora/aula
        $ch_hora_relogio_extensao->setExitAction( new TAction( array($this, 'onCalculaExtensaoHoraAula')));
        
        
        $this->form->addFields( [  $etiqueta ] );

        $this->form->addFields( [ '<br>' ] );

        $row = $this->form->addFields( [ new TLabel("Quanto da carga horária da unidade é destinada à Extensão?") ] );
        $row->layout = ['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('CH em Hora/Aula'), $ch_hora_aula_extensao ],
                                       [ new TLabel('CH em Hora/Relógio'), $ch_hora_relogio_extensao ] );
        $row->layout = ['col-sm-2', 'col-sm-2'];

        $this->form->addFields( [ '<br>' ] );
        

        //ÁREAS        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $curso = new DiplomaDigitalCurso($curriculo->dados_curso_id);
    
            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('dados_curso_id', '=', $curso->id));
            
            $area_formacao = AreaFormacao::getObjects($criteria1);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        
        //Só acrescenta o escrito ao formulário se existirem áreas de formação no curso. O componente também só vai aparecer se o critério for satisfeito
        if($area_formacao)
        {
            $label2 = new TLabel('Áreas', '#285097', 12, 'b', '<br>');
            $label2->style='text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [$label2] );
        }
            
        $areas = new TDBCheckGroup('areas', 'Felabs_DB', 'AreaFormacao', 'id', 'nome', 'nome', $criteria1);
               
        $this->form->addFields( [$areas] );
        

        $curriculo_id->addValidation('Currículo ID', new TRequiredValidator);
        $tipo->addValidation('Tipo', new TRequiredValidator);        
        $this->cod_disciplina->addValidation('Cód.', new TRequiredValidator);
        $nome->addValidation('Nome', new TRequiredValidator);
        $opcao_carga_horaria->addValidation('Lançamento da CH', new TRequiredValidator);
        $ch_hora_aula->addValidation('CH em Hora/Aula', new TRequiredValidator);
        $ch_hora_relogio->addValidation('CH em Hora/Relógio', new TRequiredValidator);
        $opcao_disciplina->addValidation('Grade ou Optativa', new TRequiredValidator);
        $cod_disciplina_curriculo->addValidation('Cód. da unidade no currículo', new TRequiredValidator);
        $ementa->addValidation('Ementa', new TRequiredValidator);
        

        // set sizes
        $nome_curso->setValue($curso->nome_curso_diploma);
        $nome_curso->setEditable(FALSE);
        $cod_grade->setValue($curriculo->cod_grade);
        $cod_grade->setEditable(FALSE);
        $cod_curriculo->setValue($curriculo->codigo_curriculo);
        $cod_curriculo->setEditable(FALSE);
        $this->cod_disciplina->setDisplayLabel('Disciplina');
        $nome->setEditable(FALSE);
        $opcao_carga_horaria->setLayout('horizontal');
        $opcao_carga_horaria->setSize(85);
        $ch_hora_aula->setMask('9!');        
        $ch_hora_aula->setTip("Após o preenchimento, pressione a tecla TAB para que a carga horária em hora/relógio seja calculada");       
        $ch_hora_relogio->setTip("Após o preenchimento, pressione a tecla TAB para que a carga horária em hora/aula seja calculada");
        $opcao_disciplina->setValue('Optativa');
        $opcao_disciplina->setEditable(FALSE);        
        $cod_disciplina_curriculo->setEditable(FALSE);
        $ementa->setSize('100%', 200);            
        $etiqueta->setLayout('horizontal');
        $etiqueta->setSize(140);
        $areas->setLayout('horizontal');

            
        if ($areas->getLabels())
        {
            foreach ($areas->getLabels() as $label)
            {
                $label->setSize(200);
            }
        }
        

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Back'), new TAction([$this, 'onBackList']), 'fa:arrow-left');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
                
        parent::add($container);
    }


    public static function onExemploEtiqueta()
    {
        $win = TWindow::create('Orientações', 0.6, null);
        $win->add("<p style='font-size: 14px'><b>Exemplo 1</b> - Disciplina obrigatória: aplica-se a etiqueta Obrigatória <br><br>
                   <b>Exemplo 2</b> - Disciplina optativa: aplica-se a etiqueta Optativa <br><br>
                   <b>Exemplo 3</b> - Disciplina obrigatória ou optativa cuja carga horária, total ou parcial, seja dedicada à extensão:
                   aplica-se a etiqueta Obrigatória <b>ou</b> a etiqueta Optativa e <b>aplica-se também</b> a etiqueta Extensão, neste
                   caso, informando as horas devidas para fins de cômputo da carga de extensão.</p>");
        $win->show();
    }
    
    
    public function onBackList($param)
    {
        $datagrid_origem = TSession::getValue('datagrid_origem');
  
        if($datagrid_origem == "CurriculoList")
        {
            TApplication::loadPage($datagrid_origem, 'onReload');    
        }
        else
        {
            $id_curriculo = $param['curriculo_id'];  
                  
            $param['curriculo_id'] = $id_curriculo;
                    
            TApplication::loadPage($datagrid_origem, 'onShow', $param);   
        } 
    }


    public static function onExitDisciplina($param)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $cod_disciplina = $param['cod_disciplina']; 
            $cod_curriculo = $param['cod_curriculo'];
            
            if($cod_disciplina AND $cod_curriculo)
            {
                $disciplina = new FiDisciplina($cod_disciplina);
            
                $obj = new StdClass;
                $obj->cod_disciplina = $disciplina->CodDisciplina;
                $obj->nome = $disciplina->NomeOficial;               
                                 
                //O código da unidade no currículo é formado pelo próprio código do currículo (cód.curso + "." + cód. grade) + "-" + código da disciplina
                $obj->cod_disciplina_curriculo = $cod_curriculo . '-' . $disciplina->CodDisciplina;                                 
                        
                TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);    
            }       
                        
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();    
        }
    }
    
    
    public static function onChangeCargaHoraria($param)
    {
        $cod_disciplina = $param['cod_disciplina'];
        $opcao_carga_horaria = $param['opcao_carga_horaria'];
        
        if($opcao_carga_horaria)
        {    
            if((!empty($cod_disciplina)) AND (!empty($opcao_carga_horaria)))
            {    
                $obj = new StdClass;
                   
                if($opcao_carga_horaria == "Hora/Aula")
                {
                    TEntry::enableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula');  
                    TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio');                                              
                                        
                    $obj->ch_hora_aula = '';    
                    $obj->ch_hora_relogio = '';
                    $obj->ch_hora_aula_extensao = '';
                    $obj->ch_hora_relogio_extensao = '';
                    
                    TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);
                    
                    //Recarrega a função das etiquetas com base no tipo de carga horária escolhida 
                    self::onChangeEtiqueta($param);
                }            
                elseif($opcao_carga_horaria == "Hora/Relógio")
                {                                               
                    TEntry::enableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio');
                    TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula');    
                       
                    $obj->ch_hora_aula = '';     
                    $obj->ch_hora_relogio = '';                                   
                    $obj->ch_hora_aula_extensao = '';
                    $obj->ch_hora_relogio_extensao = '';
                    
                    TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);
                    
                    //Recarrega a função das etiquetas com base no tipo de carga horária escolhida 
                    self::onChangeEtiqueta($param);
                }  
                else
                {     
                    TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula');
                    TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio');
                            
                    TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula');
                    TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio'); 
                    
                    $obj->ch_hora_aula = '';     
                    $obj->ch_hora_relogio = '';                                   
                    $obj->ch_hora_aula_extensao = '';
                    $obj->ch_hora_relogio_extensao = '';
                    
                    TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);
                }   
            }
            else
            {
                TScript::create("$('input[type=radio][name=opcao_carga_horaria]').prop('checked', false)");
                new TMessage('error', 'Por favor, selecione uma disciplina');
            }
        }    
        else
        {
            TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula');
            TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio');
                        
            TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula');
            TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio');  
        }    
    }
    
    
    public static function onCalculaHoraRelogio($param)
    {
        $ch_aula = $param['ch_hora_aula'];
        
        $obj = new StdClass;
                
        if(!empty($ch_aula))
        {
            $obj->ch_hora_relogio = ($ch_aula * 50)/60;   
            $obj->ch_hora_relogio = (int) round($obj->ch_hora_relogio);
        }
        else
        {
            $obj->ch_hora_relogio = '';
        }
        
        TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);
    }
    
    
    public static function onCalculaHoraAula($param)
    {
        $ch_relogio = $param['ch_hora_relogio'];
        
        $obj = new StdClass;
               
        if(!empty($ch_relogio))
        {
            $obj->ch_hora_aula = ($ch_relogio * 60)/50;   
            $obj->ch_hora_aula = (int) $obj->ch_hora_aula; 
        }
        else
        {
            $obj->ch_hora_aula = '';
        }
        
        TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);       
    }
    
    
    public static function onChangeEtiqueta($param)
    {
        try
        {
            $opcao_carga_horaria = $param['opcao_carga_horaria'];
            $etiquetas_selecionadas = $param['etiqueta'];
            
            TTransaction::open('Felabs_DB'); 
            

            //Por determinação do MEC, o código para a etiqueta extensão deverá ser única e, obrigatoriamente, "ext"
            $criteria1 = new TCriteria;            
            $criteria1->add(new TFilter('codigo', '=', 'ext')); 
    
            $extensao = Etiqueta::getObjects($criteria1); 
                
            $id_etiqueta_extensao = $extensao[0]->id;


            //Se a etiqueta de extensão foi selecionada
            if(in_array($id_etiqueta_extensao, $etiquetas_selecionadas))
            {
                if(!empty($opcao_carga_horaria))
                {
                    //Verifica se a carga horária da unidade está sendo lançada em hora/aula ou hora/relógio e habilita o campo correto
                    if($opcao_carga_horaria == "Hora/Aula")
                    {      
                        TEntry::enableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula_extensao');                                                
                        TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio_extensao');
                    }
                    elseif($opcao_carga_horaria == "Hora/Relógio")
                    {
                        TEntry::enableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio_extensao');
                        TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula_extensao');
                    }
                    else
                    {
                        TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula_extensao');
                        TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio_extensao');
                        
                        TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula_extensao');
                        TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio_extensao');
                    }
                }
                else
                {
                    TCheckGroup::clearField('form_CurriculoDisciplinaOptativa', 'etiqueta');
                    new TMessage('error', 'É necessário informar se a unidade está sendo lançada em hora/aula ou hora/relógio');
                }    
            }
            else
            {       
                TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula_extensao');
                TEntry::clearField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio_extensao');
                
                TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_aula_extensao');
                TEntry::disableField('form_CurriculoDisciplinaOptativa', 'ch_hora_relogio_extensao');    
            }
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback(); 
        }
    }


    public static function onCalculaExtensaoHoraRelogio($param)
    {
        $ch_aula_extensao = $param['ch_hora_aula_extensao'];

        $obj = new StdClass;
               
        if(!empty($ch_aula_extensao))
        {
            $obj->ch_hora_relogio_extensao = ($ch_aula_extensao * 50)/60; 
            $obj->ch_hora_relogio_extensao = (int) round($obj->ch_hora_relogio_extensao);   
        }
        else
        {
            $obj->ch_hora_relogio_extensao = '';
        }
        
        TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);
    }
    
    
    public static function onCalculaExtensaoHoraAula($param)
    {
        $ch_relogio_extensao = $param['ch_hora_relogio_extensao'];

        $obj = new StdClass;

        if(!empty($ch_relogio_extensao))
        {
            $obj->ch_hora_aula_extensao = ($ch_relogio_extensao * 60)/50;
            $obj->ch_hora_aula_extensao = (int) $obj->ch_hora_aula_extensao;
        }
        else
        {
            $obj->ch_hora_aula_extensao = '';
        }
        
        TForm::sendData('form_CurriculoDisciplinaOptativa', $obj, FALSE, FALSE);
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $this->form->validate(); 
            $data = $this->form->getData(); 
            
            $object = new CurriculoDisciplina;  
            $object->fromArray( (array) $data); 
            
            
            //Se o currículo já foi publicado uma vez, não permite alteração
            if($curriculo->data_primeira_publicacao <> NULL)
            {
                new TMessage('error','O registro não pode ser alterado, pois o currículo já foi publicado');
                return false;
            }
            
            
            //Se está salvando um "novo registro", mas já existe registro da mesma disciplina no currículo
            if(empty($data->id))
            {
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('curriculo_id', '=', $object->curriculo_id)); 
                $criteria1->add(new TFilter('cod_disciplina', '=', $object->cod_disciplina));
                
                $criteria2 = new TCriteria;
                $criteria2->add(new TFilter('curriculo_id', '=', $object->curriculo_id)); 
                $criteria2->add(new TFilter('cod_disciplina_curriculo', '=', $object->cod_disciplina_curriculo)); 
                
                $criteria3 = new TCriteria;     
                $criteria3->add($criteria1, TExpression::OR_OPERATOR); 
                $criteria3->add($criteria2, TExpression::OR_OPERATOR); 

                $repository = new TRepository('CurriculoDisciplina'); 
                $registros_bd = $repository->load($criteria3);
            
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro desta disciplina no currículo");
                }
            }
            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
            
            $object->store();
            
            
            //Controle campos condicionais - Etiquetas (ao menos uma etiqueta deve ser selecionada)
            $old_etiquetas = CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $object->id)->load();
            
            if($data->etiqueta)
            {
                foreach($data->etiqueta as $etiqueta_selecionada)
                {
                    //Verifica se é a etiqueta de Extensão
                    $etiqueta = new Etiqueta($etiqueta_selecionada);
                    
                    //Se for, verifica se as cargas horárias foram preenchidas
                    if($etiqueta->codigo == "ext")
                    {
                        $data->ch_hora_aula_extensao = $param['ch_hora_aula_extensao'];
                        $data->ch_hora_relogio_extensao = $param['ch_hora_relogio_extensao'];
                        
                        if((!$data->ch_hora_aula_extensao) OR (!$data->ch_hora_relogio_extensao))
                        {
                            throw new Exception("É necessário informar o quanto da carga horária da unidade curricular é destinada à Extensão");    
                        }

                        //Verifica se a carga de extensão não é superior a carga horária da unidade
                        if(($data->ch_hora_relogio_extensao > $object->ch_hora_relogio) OR ($data->ch_hora_aula_extensao > $object->ch_hora_aula))
                        {
                            throw new Exception("A carga horária destinada à Extensão não pode ser superior à carga horária da unidade");     
                        }
                    }
                    
                    //Se não for, não atribui CH nenhuma
                    else
                    {
                        $data->ch_hora_relogio_extensao = '';
                        $data->ch_hora_aula_extensao = '';
                                
                    }
       
                    $verifica_etiqueta = CurriculoDisciplinaEtiqueta::where('curriculo_disciplina_id', '=', $object->id)
                                                                    ->where('dados_etiqueta_id', '=', $etiqueta_selecionada)
                                                                    ->load();
                    
                    //Se não encontrou registro, é novo                                                   
                    if(empty($verifica_etiqueta))
                    {
                        $curriculo_disciplina_etiqueta = new CurriculoDisciplinaEtiqueta;
                    }
                    
                    //Se encontrou registro, é porque a etiqueta já havia sido selecionada anteriormente
                    else
                    {
                        $curriculo_disciplina_etiqueta = CurriculoDisciplinaEtiqueta::find($verifica_etiqueta[0]->id);
                    }
                    
                    $curriculo_disciplina_etiqueta->curriculo_disciplina_id = $object->id;
                    $curriculo_disciplina_etiqueta->dados_etiqueta_id = $etiqueta_selecionada;
                    $curriculo_disciplina_etiqueta->ch_hora_relogio = $data->ch_hora_relogio_extensao;
                    $curriculo_disciplina_etiqueta->ch_hora_aula = $data->ch_hora_aula_extensao;
                    $curriculo_disciplina_etiqueta->store();

                    $disciplinas_etiquetas[] = $curriculo_disciplina_etiqueta->id;
                }
            }
            else
            {
                throw new Exception("É necessário aplicar ao menos uma etiqueta à unidade curricular");
            }                       
            
            //Se preencheu a carga horária de extensão, verifica se a etiqueta foi marcada
            $etiqueta_extensao = Etiqueta::where('codigo', '=', 'ext')->load();

            if((($data->ch_hora_aula_extensao) OR ($data->ch_hora_relogio_extensao)) AND (!in_array($etiqueta_extensao[0]->id, $data->etiqueta)))
            {
                throw new Exception("É necessário aplicar a etiqueta de Extensão");    
            }
            
            if($old_etiquetas)
            {
                foreach($old_etiquetas as $old_etiqueta)
                {
                    if (!in_array($old_etiqueta->id, $disciplinas_etiquetas))
                    {
                        $old_etiqueta->delete();
                    }
                }
            }
            
            
            //Controle campos condicionais - Áreas
            $old_areas = CurriculoDisciplinaArea::where('curriculo_disciplina_id', '=', $object->id)->load();
            
            if($data->areas)
            {
                foreach($data->areas as $area_selecionada)
                {
                    $verifica_area = CurriculoDisciplinaArea::where('curriculo_disciplina_id', '=', $object->id)
                                                            ->where('dados_area_formacao_id', '=', $area_selecionada)
                                                            ->load();
                    
                    //Se não encontrou registro, é novo                                                    
                    if(empty($verifica_area))
                    {                    
                        $curriculo_disciplina_area = new CurriculoDisciplinaArea;
                    }
                    
                    //Se encontrou registro, é porque a área já havia sido selecionada anteriormente
                    else
                    {
                        $curriculo_disciplina_area = CurriculoDisciplinaArea::find($verifica_area[0]->id);
                    }
                    
                    $curriculo_disciplina_area->curriculo_disciplina_id = $object->id;
                    $curriculo_disciplina_area->dados_area_formacao_id = $area_selecionada;
                    $curriculo_disciplina_area->store();    
                    
                    $disciplinas_areas[] = $curriculo_disciplina_area->id;
                }    
            }
            
            if($old_areas)
            {
                foreach($old_areas as $old_area)
                {
                    if (!in_array($old_area->id, $disciplinas_areas))
                    {
                        $old_area->delete();
                    }
                }
            }
            
                        
            $data->id = $object->id;
            
            $this->form->setData($data); 
            
            TTransaction::close(); 

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            

            //Verifica qual datagrid chamou o formulário, adição (CurriculoList) ou edição (EstruturaCurricularList)
            $datagrid_origem = TSession::getValue('datagrid_origem');
    
            if($datagrid_origem == "CurriculoList")
            {
                //Se está cadastrando, limpa o formulário depois de salvar, mas mantém os campos referentes à mesma grade preenchidos                                      
                TApplication::loadPage('DisciplinaOptativaCurriculoForm', 'onLoad', ['curriculo_id' => $object->curriculo_id]);
            }
            else
            {
                //Se está editando, volta para a estrutura curricular após salvar
                $param['curriculo_id'] = $object->curriculo_id;                    
                TApplication::loadPage($datagrid_origem, 'onShow', $param);  
            }            
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData( $this->form->getData() ); 
            
            //Se estiver editando registro e cair na edição, mantém o mesmo código de disciplina
            if(!empty($param['id']))
            {
                $this->cod_disciplina->setEditable(FALSE);
            }
            
            $this->onChangeCargaHoraria($param);
            $this->onChangeEtiqueta($param);
            $this->onCalculaHoraAula($param);
            $this->onCalculaHoraRelogio($param);
            $this->onCalculaExtensaoHoraAula($param);
            $this->onCalculaExtensaoHoraRelogio($param);
            
            TTransaction::rollback(); 
        }
    }
    

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }


    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                
                TTransaction::open('Felabs_DB'); 
                
                $object = new CurriculoDisciplina($key); 
                
                $this->cod_disciplina->setEditable(FALSE);
                
                
                //Executa todas as funções com campos dependentes que devem estar habilitados ou não
                $param['cod_disciplina'] = $object->cod_disciplina;
                $param['opcao_carga_horaria'] = $object->opcao_carga_horaria;
                $this->onChangeCargaHoraria($param);
                
                                
                $param['ch_hora_aula'] = (int) $object->ch_hora_aula;
                $this->onCalculaHoraRelogio($param);
                
                
                $param['ch_hora_relogio'] = (int) $object->ch_hora_relogio;
                $this->onCalculaHoraAula($param);
                

                //Etiquetas
                $etiquetas = $object->getCurriculoDisciplinaEtiqueta();
                
                if($etiquetas)
                {
                    foreach($etiquetas as $etiqueta)
                    {
                        $etiqueta_list[] = $etiqueta->dados_etiqueta_id;
                        
                        if(($etiqueta->ch_hora_relogio <> NULL) AND ($etiqueta->ch_hora_aula <> NULL))
                        {                            
                            $ch_hora_relogio_extensao = $etiqueta->ch_hora_relogio;
                            $ch_hora_aula_extensao = $etiqueta->ch_hora_aula;
                        }
                    }
                }

                $object->etiqueta = $etiqueta_list;                
                
                //Habilita ou desabilita os campos da ch de extensão de acordo com os parâmetros passados
                $param['opcao_carga_horaria'] = $object->opcao_carga_horaria;
                $param['etiqueta'] = $object->etiqueta;
                $this->onChangeEtiqueta($param); 
                
                
                $param['ch_hora_aula_extensao'] = (int) $ch_hora_aula_extensao;
                $this->onCalculaExtensaoHoraRelogio($param);
                
                
                $param['ch_hora_relogio_extensao'] = (int) $ch_hora_relogio_extensao;
                $this->onCalculaExtensaoHoraAula($param);
                                 
                                
                //Áreas
                $areas = $object->getCurriculoDisciplinaArea();
                
                if($areas)
                {
                    foreach($areas as $area)
                    {
                        $area_list[] = $area->dados_area_formacao_id;
                    }
                }
                
                $object->areas = $area_list;

                
                $this->form->setData($object); 
                
                $obj = new StdClass;
                $obj->nome_curso = $object->curriculo_digital->diploma_digital_curso->nome_curso_diploma;
                $obj->cod_grade = $object->curriculo_digital->cod_grade;
                $obj->cod_curriculo = $object->curriculo_digital->codigo_curriculo;
                            
                $this->form->setData($obj); 
                
                TTransaction::close(); 
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    
    public function onLoad( $param )
    {
        try
        {
            //Preenche os campos dependentes
            $id_curriculo = $param['curriculo_id'];


            TTransaction::open('Felabs_DB');
            
            $curriculo = new CurriculoDigital($id_curriculo);  

            $obj = new StdClass;
            $obj->curriculo_id = $curriculo->id;    
            $obj->nome_curso = $curriculo->diploma_digital_curso->nome_curso_diploma;
            $obj->cod_grade = $curriculo->cod_grade;
            $obj->cod_curriculo = $curriculo->codigo_curriculo;
            $obj->opcao_disciplina = "Optativa"; 
                        
            $this->form->setData($obj);
    
            TTransaction::close();
            
            $param['cod_disciplina'] = '';
            $this->onExitDisciplina($param);            
            
            //Campos dependentes iniciam desabilitados
            $this->onChangeCargaHoraria($param);
            $this->onChangeEtiqueta($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        } 
    }
}