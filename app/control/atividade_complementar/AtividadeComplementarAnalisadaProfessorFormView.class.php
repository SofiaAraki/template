<?php

class AtividadeComplementarAnalisadaProfessorFormView extends TWindow
{

    public function __construct( $param )
    {    
        parent::__construct();
        parent::setTitle('Atividade Complementar');
        
        $this->form = new BootstrapFormBuilder('form_AtividadeComplementar_View');
        $this->setSize(0.8, null);
        $this->form->setFieldSizes('100%');
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    

    public function onEdit( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
        
            $object = new AtividadeComplementar($param['key']);
            
            $label_nome_aluno = new TLabel('Aluno', '#333333', '', 'B');
            $label_nome_curso = new TLabel('Curso', '#333333', '', 'B');
            $label_ano = new TLabel('Ano', '#333333', '', 'B');
            $label_semestre = new TLabel('Semestre', '#333333', '', 'B');
            $label_etapa = new TLabel('Etapa', '#333333', '', 'B');
            $label_tipo_atividade = new TLabel('Atividade', '#333333', '', 'B');
            $label_descricao = new TLabel('Descrição', '#333333', '', 'B');
            $label_data_inicio = new TLabel('Data de início', '#333333', '', 'B');
            $label_data_termino = new TLabel('Data de término', '#333333', '', 'B');
            $label_carga_horaria = new TLabel('Horas', '#333333', '', 'B');            
                        
            
            $text_nome_aluno = new TTextDisplay($object->nome_aluno, '#333333', '', '');
            $text_nome_curso = new TTextDisplay($object->nome_curso, '#333333', '', '');
            $text_ano = new TTextDisplay($object->ano, '#333333', '', '');
            $text_semestre = new TTextDisplay($object->semestre, '#333333', '', '');
            $text_etapa = new TTextDisplay($object->etapa, '#333333', '', '');
            $text_tipo_atividade = new TTextDisplay($object->tipo_atividade, '#333333', '', '');
            $text_descricao = new TTextDisplay($object->descricao, '#333333', '', '');
            $text_data_inicio = new TTextDisplay(TDate::date2br($object->data_inicio), '#333333', '', '');
            $text_data_termino = new TTextDisplay(TDate::date2br($object->data_termino), '#333333', '', '');
            $text_carga_horaria = new TTextDisplay((int)$object->carga_horaria, '#333333', '', '');
                                   
            
            $row = $this->form->addFields( [ $label_nome_aluno, $text_nome_aluno ],
                                           [ $label_nome_curso, $text_nome_curso ] );
            $row->layout = ['col-sm-4', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_ano, $text_ano ],
                                           [ $label_semestre, $text_semestre ],
                                           [ $label_etapa, $text_etapa ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
            $row = $this->form->addFields( [ $label_tipo_atividade, $text_tipo_atividade ],
                                           [ $label_descricao, $text_descricao ] );
            $row->layout = ['col-sm-8', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_data_inicio, $text_data_inicio ],
                                           [ $label_data_termino, $text_data_termino ],
                                           [ $label_carga_horaria, $text_carga_horaria ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

            TTransaction::close();
            
            
            //Pega o nome do professor responsável
            TTransaction::open('dados_fei');            
            
            $responsavel = new FiProfessor($object->cod_prof_responsavel);            
            
            TTransaction::close();
            
            
            $label_prof_responsavel = new TLabel('Responsável pela aprovação', '#333333', '', 'B');
            $label_titulacao_responsavel = new TLabel('Titulação', '#333333', '', 'B');
            $label_status_atividade = new TLabel('Status', '#333333', '', 'B');
            $label_observacao = new TLabel('Observação', '#333333', '', 'B');
            
            
            $text_prof_responsavel = new TTextDisplay($responsavel->Nome, '#333333', '', '');
            $text_titulacao_responsavel = new TTextDisplay($object->titulacao_prof_responsavel, '#333333', '', '');
            $text_status_atividade = new TTextDisplay($object->status_atividade, '#333333', '', '');
            $text_observacao = new TTextDisplay($object->observacao, '#333333', '', '');


            $row = $this->form->addFields( [ $label_prof_responsavel, $text_prof_responsavel ],
                                           [ $label_titulacao_responsavel, $text_titulacao_responsavel ],
                                           [ $label_status_atividade, $text_status_atividade ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_observacao, $text_observacao ] );
            $row->layout = ['col-sm-12'];
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}