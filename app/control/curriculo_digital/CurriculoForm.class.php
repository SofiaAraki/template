<?php

class CurriculoForm extends TPage
{
    protected $form; 
    private $cod_curso;
    private $cod_grade;
    

    public function __construct( $param )
    {
        parent::__construct();
        
        $unit_id = TSession::getValue('userunitid');
        
        if($unit_id <> 2 AND $unit_id <> 3 AND $unit_id <> 10 AND $unit_id <> 6)
        {
            new TMessage('error', 'Funcionalidade não disponível para esta unidade');
            die;
        }
                

        //Filtra os cursos de acordo com a unidade
        $criteria_curso = new TCriteria;
        $criteria_curso->add(new TFilter('CodEntidade', '=', $unit_id));
            

        //Filtro para evitar pré-carregamento da combo grade
        $criteria_grade = new TCriteria;
        $criteria_grade->add(new TFilter('CodGradecurso', '<', '0'));
        
                
        // creates the form
        $this->form = new BootstrapFormBuilder('form_CurriculoDigital');
        $this->form->setFormTitle('<h4>Currículo Digital</h4>');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new THidden('id');        
        $dados_curso_id = new THidden('dados_curso_id'); 
        $this->cod_curso = new TDBSeekButton('cod_curso', 'dados_fei', 'form_CurriculoDigital', 'FiCurso', 'Nome', 'cod_curso', 'nome_curso', $criteria_curso); 
        $this->cod_grade = new TDBCombo('cod_grade', 'dados_fei', 'FiGradeCurso', 'CodGradecurso', 'Código: ({CodGradecurso}) - Descrição: {Descricao}', 'CodGradecurso', $criteria_grade);
        $tipo_documento = new THidden('tipo_documento');
        $dados_versao_id = new THidden('dados_versao_id'); 
        $codigo_curriculo = new TEntry('codigo_curriculo');
        $data_curriculo = new TDate('data_curriculo');
        $nome_areas = new TEntry('nome_areas');
        //$regime_letivo = new TCombo('regime_letivo');
        //$regime_matricula = new TCombo('regime_matricula');
        //$numero_vagas_anual = new TEntry('numero_vagas_anual');
        //$numero_vagas_turma = new TEntry('numero_vagas_turma');
        $regime_letivo = new THidden('regime_letivo');
        $regime_matricula = new THidden('regime_matricula');
        $numero_vagas_anual = new THidden('numero_vagas_anual');
        $numero_vagas_turma = new THidden('numero_vagas_turma');
        $numero_etapas = new TEntry('numero_etapas');
        $duracao_aula = new TEntry('duracao_aula');
        $informacoes_adicionais = new TText('informacoes_adicionais');
        $status_xml = new THidden('status_xml');
        $status_assinatura_coordenador = new THidden('status_assinatura_coordenador');
        $data_exp_certificado_coordenador = new THidden('data_exp_certificado_coordenador');
        $dados_emissora_id = new THidden('dados_emissora_id');
        $tipo_assinante_emissora = new THidden('tipo_assinante_emissora');
        $status_assinatura_emissora = new THidden('status_assinatura_emissora');
        $data_exp_certificado_emissora = new THidden('data_exp_certificado_emissora');
        $codigo_validacao = new THidden('codigo_validacao');
        $url_curriculo = new THidden('url_curriculo');
        $qrcode = new THidden('qrcode');
        $caminho_qrcode = new THidden('caminho_qrcode');
        $arquivo = new THidden('arquivo');
        $caminho_arquivo = new THidden('caminho_arquivo');
        $arquivo_pdf = new THidden('arquivo_pdf');
        $caminho_pdf = new THidden('caminho_pdf');
        $status_assinatura_pdf = new THidden('status_assinatura_pdf');
        $status_publicacao = new THidden('status_publicacao');
        $data_primeira_publicacao = new THidden('data_primeira_publicacao');
        $data_publicacao = new THidden('data_publicacao');
        $system_user_id = new THidden('system_user_id');
        $data_reg = new THidden('data_reg');
        $nome_curso = new TEntry('nome_curso'); //Componente auxiliar, não será salvo no banco de dados
        
              
        //Opção regime letivo e matrícula
        //$combo_regime = [];
        //$combo_regime['Semestral'] = "Semestral";
        //$combo_regime['Anual'] = "Anual";
        
        //$regime_letivo->addItems($combo_regime);   
        //$regime_matricula->addItems($combo_regime);   
        

        //Carrega as grades de acordo com o curso escolhido
        $this->cod_curso->setExitAction(new TAction(array($this, 'onCursoExit')));
        
        
        //Preenche o código do currículo de acordo com o curso e a grade escolhida
        $this->cod_grade->setChangeAction(new TAction(array($this, 'onGradeChange')));
        

        // add the fields
        $this->form->addFields( [ $id ] );  
        $this->form->addFields( [ $dados_curso_id ] );           
        $this->form->addFields( [ $tipo_documento ] );
        $this->form->addFields( [ $dados_versao_id ] );         
        $this->form->addFields( [ $regime_letivo ] ); 
        $this->form->addFields( [ $regime_matricula ] ); 
        $this->form->addFields( [ $numero_vagas_anual ] ); 
        $this->form->addFields( [ $numero_vagas_turma ] );                      
        $this->form->addFields( [ $status_xml ] );
        $this->form->addFields( [ $status_assinatura_coordenador ] );
        $this->form->addFields( [ $data_exp_certificado_coordenador ] );
        $this->form->addFields( [ $dados_emissora_id ] );
        $this->form->addFields( [ $tipo_assinante_emissora ] );
        $this->form->addFields( [ $status_assinatura_emissora ] );
        $this->form->addFields( [ $data_exp_certificado_emissora ] );
        $this->form->addFields( [ $codigo_validacao ] );
        $this->form->addFields( [ $url_curriculo ] );
        $this->form->addFields( [ $qrcode ] );
        $this->form->addFields( [ $caminho_qrcode ] );
        $this->form->addFields( [ $arquivo ] );
        $this->form->addFields( [ $caminho_arquivo ] );
        $this->form->addFields( [ $arquivo_pdf ] );
        $this->form->addFields( [ $caminho_pdf ] );
        $this->form->addFields( [ $status_assinatura_pdf ] );
        $this->form->addFields( [ $status_publicacao ] );
        $this->form->addFields( [ $data_primeira_publicacao ] );
        $this->form->addFields( [ $data_publicacao ] );
        $this->form->addFields( [ $system_user_id ] );
        $this->form->addFields( [ $data_reg ] );
                
        $row = $this->form->addFields( [ new TLabel('Cód. Curso'), $this->cod_curso ],
                                       [ new TLabel('Curso'), $nome_curso ],
                                       [ new TLabel('Grade'), $this->cod_grade ] );
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-7'];
        
        /*$row = $this->form->addFields( [ new TLabel('Regime letivo'), $regime_letivo ],
                                       [ new TLabel('Regime de matrícula'), $regime_matricula ],
                                       [ new TLabel('Nº de vagas oferecidas'), $numero_vagas_anual ],
                                       [ new TLabel('Nº máximo de alunos por turma'), $numero_vagas_turma ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];*/
        
        $row = $this->form->addFields( [ new TLabel('Cód. Currículo'), $codigo_curriculo ],
                                       [ new TLabel('Data da homologação'), $data_curriculo ],
                                       [ new TLabel('Nº de etapas'), $numero_etapas ],
                                       [ new TLabel('Duração da aula (em minutos)'), $duracao_aula ] );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];                                              

        $this->form->addFields( [ new TLabel('Termo usado pelo curso para referenciar Áreas de Concentração, Competências, Linhas de formação ou equivalentes'), $nome_areas ] );
        
        $row = $this->form->addFields( [ new TLabel('Informações adicionais'), $informacoes_adicionais ] );
        $row->layout = ['col-sm-12'];
        
        $this->form->addFields( [ new TLabel("<i>Nesta área pode-se inserir uma breve explicação sobre a estruturação das Atividades Complementares, Estágios e TCC, bem como outra 
        informação considerada pertinente e que componha o currículo. Não devem ser inseridas informações que estejam estruturadas em outro campo. O texto será acrescentado como uma observação ao final do PDF.</i>") ] );
        
        $this->form->addFields( [ '<br>' ] );                 
                        

        $dados_emissora_id->addValidation('Dados Emissora ID', new TRequiredValidator);   
        $dados_curso_id->addValidation('Dados Curso ID', new TRequiredValidator);
        $this->cod_curso->addValidation('Curso', new TRequiredValidator);
        $this->cod_grade->addValidation('Grade', new TRequiredValidator);
        //$regime_letivo->addValidation('Regime letivo', new TRequiredValidator);
        //$regime_matricula->addValidation('Regime de matrícula', new TRequiredValidator);
        //$numero_vagas_anual->addValidation('Nº de vagas oferecidas', new TRequiredValidator);
        //$numero_vagas_turma->addValidation('Nº máximo de alunos por turma', new TRequiredValidator);
        $codigo_curriculo->addValidation('Cód. Currículo', new TRequiredValidator);
        $data_curriculo->addValidation('Data da homologação', new TRequiredValidator);
        $numero_etapas->addValidation('Nº de etapas', new TRequiredValidator);
        $duracao_aula->addValidation('Duração da aula (em minutos)', new TRequiredValidator);
        

        // set sizes
        $this->cod_curso->setSize(80);
        $this->cod_curso->setDisplayLabel('Curso');
        $nome_curso->setEditable(FALSE);
        $codigo_curriculo->setEditable(FALSE);
        $data_curriculo->setMask('dd/mm/yyyy');
        $data_curriculo->setDatabaseMask('yyyy-mm-dd');
        //$numero_vagas_anual->setMask('9!');
        //$numero_vagas_turma->setMask('9!');
        $numero_etapas->setEditable(FALSE);
        $nome_areas->setEditable(FALSE);
        $duracao_aula->setMask('99');        
        $informacoes_adicionais->setSize('100%', 300);
                       

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        // create the form actions
        $this->form->addAction('Voltar', new TAction(['CurriculoList', 'onReload']), 'fa:arrow-left blue');
        $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save green');

        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }


    public static function onCursoExit($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
           
            if (!empty($param['cod_curso']))
            {
                $cod_curso = $param['cod_curso'];
                
                //Verifica se o curso foi cadastrado em dados_curso e filtra as grades pertencentes
                $criteria_dados_curso = new TCriteria;
                $criteria_dados_curso->add(new TFilter('codigo_curso_sistema', '=', $cod_curso));
            
                $dados_curso = DiplomaDigitalCurso::getObjects($criteria_dados_curso);
                
                if($dados_curso)
                {
                    TTransaction::open('dados_fei');
                        
                    $curso = new FiCurso($cod_curso);
                
                    $criteria = TCriteria::create( ['CodCurso' => $param['cod_curso'] ] );
                
                    // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                    TDBCombo::reloadFromModel('form_CurriculoDigital', 'cod_grade', 'dados_fei', 'FiGradeCurso', 'CodGradecurso', 'Cód. grade: ({CodGradecurso}) - Descrição: {Descricao}', 'CodGradecurso', $criteria, TRUE);
                
                
                    //Preenche os campos correspondentes
                    $obj = new StdClass;
                    $obj->dados_curso_id = $dados_curso[0]->id;
                    $obj->dados_emissora_id = $dados_curso[0]->dados_emissora_id;
                                        
                    if($dados_curso[0]->termo_referencia_area)
                    {
                        $obj->nome_areas = $dados_curso[0]->termo_referencia_area;
                    }                    
                    
                    TForm::sendData('form_CurriculoDigital', $obj);
                    
                    TTransaction::close();
                }
                else
                {
                    new TMessage('error', 'Verifique se o curso foi cadastrado em Secretaria > Secretaria Digital > Cadastros > Curso antes de prosseguir');
                    
                    TApplication::loadPage('CurriculoList', 'onReload');
                }
            }
            else
            {
                TCombo::clearField('form_CurriculoDigital', 'cod_grade'); 

                $obj = new StdClass;
                $obj->dados_curso_id = '';
                $obj->dados_emissora_id = '';
                $obj->nome_areas = '';
                
                TForm::sendData('form_CurriculoDigital', $obj);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }
    }
    
    
    public static function onGradeChange($param)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            if ((!empty($param['cod_curso'])) AND (!empty($param['cod_grade'])))
            {
                $cod_grade = $param['cod_grade'];
                
                $grade = new FiGradeCurso($cod_grade);
                
                $obj = new StdClass;
                $obj->codigo_curriculo = $param['cod_curso'] . '.' . $param['cod_grade'];
                $obj->data_curriculo = $param['data_curriculo'];
                $obj->numero_etapas = $grade->QtdeEtapas;
                
                /*switch ($grade->DuracaoEtapa) {
                    case 'A':
                        $obj->regime_letivo = "Anual";
                        $obj->regime_matricula = "Anual";
                    break;
                        
                    case 'S':
                        $obj->regime_letivo = "Semestral";
                        $obj->regime_matricula = "Semestral";
                    break;
                   
                    case 'M':
                        $obj->regime_letivo = ""; 
                        $obj->regime_matricula = "";   
                    break; 
                }*/
                    
                TForm::sendData('form_CurriculoDigital', $obj);
            }
            else
            {
                $obj = new StdClass;
                $obj->codigo_curriculo = '';
                $obj->data_curriculo = '';
                $obj->numero_etapas = '';
                //$obj->regime_letivo = '';  
                //$obj->regime_matricula = '';    
                
                TForm::sendData('form_CurriculoDigital', $obj);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());  
            TTransaction::rollback();
        }
    }
    

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $this->form->validate(); 
            $data = $this->form->getData();            
            
            $object = new CurriculoDigital;  
            $object->fromArray( (array) $data); 
            
            
            //Se o currículo já foi publicado uma vez, não permite alteração
            if($object->data_primeira_publicacao <> NULL)
            {
                new TMessage('error','O registro não pode ser alterado, pois o currículo já foi publicado');
                return false;
            }
            

            //Se está salvando um novo registro, verifica se o mesmo já não foi cadastrado
            if(empty($data->id))
            {
                $criteria1 = new TCriteria;
                $criteria1->add(new TFilter('codigo_curriculo', '=', $data->codigo_curriculo));
                    
                $repository = new TRepository('CurriculoDigital'); 
                $registros_bd = $repository->load($criteria1);
                    
                if ($registros_bd)
                {
                    throw new Exception("Já existe um registro de currículo com este mesmo código");
                }
            }
            

            if($object->status_xml == NULL)
            {
                $object->status_xml = 0; //0 Não gerado / 1 Gerado
            }
            
            if($object->status_assinatura_coordenador == NULL)
            {
                $object->status_assinatura_coordenador = 0; //0 Não assinado / 1 Assinado
            }
            
            if($object->status_assinatura_emissora == NULL)
            {
                $object->status_assinatura_emissora = 0; //0 Não assinado / 1 Assinado
            }
            
            if($object->status_assinatura_pdf == NULL)
            {
                $object->status_assinatura_pdf = 0; //0 Não assinado / 1 Assinado
            }
            
            if($object->status_publicacao == NULL)
            {
                $object->status_publicacao = 0; //0 - Não publicado / 1 - Publicado
            }
            
            $object->tipo_documento = "XMLCurriculo";
            $object->tipo_assinante_emissora = "IESEmissora";            
            $object->system_user_id = TSession::getValue('userid');
            $object->data_reg = date('Y-m-d H:i:s');
                            
            $object->store(); 

            $data->id = $object->id;
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            TApplication::loadPage('CurriculoList', 'onReload');
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            
            $data = $this->form->getData();

            $obj = new StdClass;
            $obj->cod_curso = $data->cod_curso;
            $obj->nome_curso = $data->nome_curso;
            $obj->dados_curso_id = $data->dados_curso_id;
            $obj->dados_emissora_id = $data->dados_emissora_id;
            $obj->cod_grade = $data->cod_grade;
            //$obj->regime_letivo = $data->regime_letivo;
            //$obj->regime_matricula = $data->regime_matricula;
            //$obj->numero_vagas_anual = $data->numero_vagas_anual;
            //$obj->numero_vagas_turma = $data->numero_vagas_turma;
            $obj->codigo_curriculo = $data->codigo_curriculo;
            $obj->data_curriculo = TDate::date2br($data->data_curriculo);
            $obj->numero_etapas = $data->numero_etapas;
            $obj->duracao_aula = $data->duracao_aula;
            $obj->nome_areas = $data->nome_areas;
            $obj->informacoes_adicionais = $data->informacoes_adicionais;
            
            TForm::sendData('form_CurriculoDigital', $obj);
            
            //Se estiver editando registro e cair na exceção, mantém campos bloqueados. Se estiver salvando novo registro, mantém desbloqueado
            if(!empty($param['id']))
            {
                $this->cod_curso->setEditable(FALSE);
                $this->cod_grade->setEditable(FALSE);
            }
                        
            $this->form->setData($data);   
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
                
                $object = new CurriculoDigital($key);
                
                $this->cod_curso->setEditable(FALSE);
                $this->cod_grade->setEditable(FALSE);
                 
                $this->form->setData($object); 
                                             
                TTransaction::close(); 
                
                $this->fireEvents( $object ); 
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
    
    
    public function fireEvents( $object )
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $obj = new stdClass;
            $obj->nome_curso = mb_strtoupper($object->diploma_digital_curso->nome_curso_diploma);
            $obj->cod_curso = $object->cod_curso;
            $obj->cod_grade = $object->cod_grade;
                        
            TForm::sendData('form_CurriculoDigital', $obj);
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }    
    }
}
