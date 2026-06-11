<?php

class EstagioAnalisadoProfessorFormView extends TWindow
{
    public function __construct( $param )
    {
    
        parent::__construct();
        parent::setTitle('Estágio');        
        
        $this->form = new BootstrapFormBuilder('form_Estagio_View');
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
        
            $object = new Estagio($param['key']);
            
            $cnpj_concedente = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $object->cnpj_empresa);
            $cpf_concedente = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $object->cpf_pessoa_fisica);
            
            
            $label_nome_aluno = new TLabel('Aluno', '#333333', '', 'B');
            $label_nome_curso = new TLabel('Curso', '#333333', '', 'B');
            $label_ano = new TLabel('Ano', '#333333', '', 'B');
            $label_semestre = new TLabel('Semestre', '#333333', '', 'B');
            $label_etapa = new TLabel('Etapa', '#333333', '', 'B');
            $label_razao_social_empresa = new TLabel('Concedente PJ', '#333333', '', 'B');
            $label_cnpj_empresa = new TLabel('CNPJ', '#333333', '', 'B');
            $label_nome_pessoa_fisica = new TLabel('Concedente PF', '#333333', '', 'B');
            $label_cpf_pessoa_fisica = new TLabel('CPF', '#333333', '', 'B');
            $label_data_inicio = new TLabel('Início', '#333333', '', 'B');
            $label_data_termino = new TLabel('Término', '#333333', '', 'B');
            $label_carga_horaria = new TLabel('Horas', '#333333', '', 'B');
            $label_descricao = new TLabel('Descrição', '#333333', '', 'B');


            $text_nome_aluno = new TTextDisplay($object->nome_aluno, '#333333', '', '');
            $text_nome_curso = new TTextDisplay($object->nome_curso, '#333333', '', '');
            $text_ano = new TTextDisplay($object->ano, '#333333', '', '');
            $text_semestre = new TTextDisplay($object->semestre, '#333333', '', '');
            $text_etapa = new TTextDisplay($object->etapa, '#333333', '', '');
            $text_razao_social_empresa = new TTextDisplay($object->razao_social_empresa, '#333333', '', '');
            $text_cnpj_empresa = new TTextDisplay($cnpj_concedente, '#333333', '', '');
            $text_nome_pessoa_fisica = new TTextDisplay($object->nome_pessoa_fisica, '#333333', '', '');
            $text_cpf_pessoa_fisica = new TTextDisplay($cpf_concedente, '#333333', '', '');
            $text_data_inicio = new TTextDisplay(TDate::date2br($object->data_inicio), '#333333', '', '');
            $text_data_termino = new TTextDisplay(TDate::date2br($object->data_termino), '#333333', '', '');
            $text_carga_horaria = new TTextDisplay((int)$object->carga_horaria, '#333333', '', '');
            $text_descricao = new TTextDisplay($object->descricao, '#333333', '', '');


            $row = $this->form->addFields( [ $label_nome_aluno, $text_nome_aluno ],
                                           [ $label_nome_curso, $text_nome_curso ] );
            $row->layout = ['col-sm-4', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_ano, $text_ano ],
                                           [ $label_semestre, $text_semestre ],
                                           [ $label_etapa, $text_etapa ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_razao_social_empresa, $text_razao_social_empresa ],
                                           [ $label_cnpj_empresa, $text_cnpj_empresa ] );
            $row->layout = ['col-sm-4', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_nome_pessoa_fisica, $text_nome_pessoa_fisica ],
                                           [ $label_cpf_pessoa_fisica, $text_cpf_pessoa_fisica ] );
            $row->layout = ['col-sm-4', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_descricao, $text_descricao ] );
            $row->layout = ['col-sm-12'];
   
            $row = $this->form->addFields( [ $label_data_inicio, $text_data_inicio ],
                                           [ $label_data_termino, $text_data_termino ],
                                           [ $label_carga_horaria, $text_carga_horaria ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

            TTransaction::close();
            
            
            TTransaction::open('dados_fei');        
                
            $responsavel = new FiProfessor($object->cod_prof_responsavel);          
              
            TTransaction::close();
            
            
            $label_prof_responsavel = new TLabel('Responsável pela aprovação', '#333333', '', 'B');
            $label_titulacao_responsavel = new TLabel('Titulação', '#333333', '', 'B');
            $label_status_estagio = new TLabel('Status', '#333333', '', 'B');
            $label_observacao = new TLabel('Observação', '#333333', '', 'B');
            
            
            $text_prof_responsavel  = new TTextDisplay($responsavel->Nome, '#333333', '', '');
            $text_titulacao_responsavel  = new TTextDisplay($object->titulacao_prof_responsavel, '#333333', '', '');
            $text_status_estagio  = new TTextDisplay($object->status_estagio, '#333333', '', '');
            $text_observacao = new TTextDisplay($object->observacao, '#333333', '', '');


            $row = $this->form->addFields( [ $label_prof_responsavel, $text_prof_responsavel ],
                                           [ $label_titulacao_responsavel, $text_titulacao_responsavel ], 
                                           [ $label_status_estagio, $text_status_estagio ] );
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
