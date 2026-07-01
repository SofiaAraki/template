<?php

class EstagioAnalisadoProfessorFormView extends TWindow
{
    protected $form;

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
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onEdit( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
        
            $object = new Estagio($param['key']);
            
            $cnpj_concedente = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $object->cnpj_empresa ?? '');
            $cpf_concedente = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $object->cpf_pessoa_fisica ?? '');
            
            // Removido o '#333333' para permitir que o tema Dark/Light mude a cor do texto nativamente
            $label_nome_aluno = new TLabel('Aluno', null, '', 'B');
            $label_nome_curso = new TLabel('Curso', null, '', 'B');
            $label_ano = new TLabel('Ano', null, '', 'B');
            $label_semestre = new TLabel('Semestre', null, '', 'B');
            $label_etapa = new TLabel('Etapa', null, '', 'B');
            $label_razao_social_empresa = new TLabel('Concedente PJ', null, '', 'B');
            $label_cnpj_empresa = new TLabel('CNPJ', null, '', 'B');
            $label_nome_pessoa_fisica = new TLabel('Concedente PF', null, '', 'B');
            $label_cpf_pessoa_fisica = new TLabel('CPF', null, '', 'B');
            $label_data_inicio = new TLabel('Início', null, '', 'B');
            $label_data_termino = new TLabel('Término', null, '', 'B');
            $label_carga_horaria = new TLabel('Horas', null, '', 'B');
            $label_descricao = new TLabel('Descrição', null, '', 'B');

            $text_nome_aluno = new TTextDisplay($object->nome_aluno, null, '', '');
            $text_nome_curso = new TTextDisplay($object->nome_curso, null, '', '');
            $text_ano = new TTextDisplay($object->ano, null, '', '');
            $text_semestre = new TTextDisplay($object->semestre, null, '', '');
            $text_etapa = new TTextDisplay($object->etapa, null, '', '');
            $text_razao_social_empresa = new TTextDisplay($object->razao_social_empresa, null, '', '');
            $text_cnpj_empresa = new TTextDisplay($cnpj_concedente, null, '', '');
            $text_nome_pessoa_fisica = new TTextDisplay($object->nome_pessoa_fisica, null, '', '');
            $text_cpf_pessoa_fisica = new TTextDisplay($cpf_concedente, null, '', '');
            $text_data_inicio = new TTextDisplay(TDate::date2br($object->data_inicio), null, '', '');
            $text_data_termino = new TTextDisplay(TDate::date2br($object->data_termino), null, '', '');
            $text_carga_horaria = new TTextDisplay((int)$object->carga_horaria, null, '', '');
            $text_descricao = new TTextDisplay($object->descricao, null, '', '');

            $row = $this->form->addFields( [ $label_nome_aluno, $text_nome_aluno ],
                                           [ $label_nome_curso, $text_nome_curso ] );
            $row->layout = ['col-sm-6', 'col-sm-6'];
            
            $row = $this->form->addFields( [ $label_ano, $text_ano ],
                                           [ $label_semestre, $text_semestre ],
                                           [ $label_etapa, $text_etapa ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
            
            $row = $this->form->addFields( [ $label_razao_social_empresa, $text_razao_social_empresa ],
                                           [ $label_cnpj_empresa, $text_cnpj_empresa ] );
            $row->layout = ['col-sm-6', 'col-sm-6'];
            
            $row = $this->form->addFields( [ $label_nome_pessoa_fisica, $text_nome_pessoa_fisica ],
                                           [ $label_cpf_pessoa_fisica, $text_cpf_pessoa_fisica ] );
            $row->layout = ['col-sm-6', 'col-sm-6'];
            
            $row = $this->form->addFields( [ $label_descricao, $text_descricao ] );
            $row->layout = ['col-sm-12'];
   
            $row = $this->form->addFields( [ $label_data_inicio, $text_data_inicio ],
                                           [ $label_data_termino, $text_data_termino ],
                                           [ $label_carga_horaria, $text_carga_horaria ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

            TTransaction::close();
            
            // Busca o responsável na outra base de dados
            TTransaction::open('dados_fei');        
            $responsavel = new FiProfessor($object->cod_prof_responsavel);          
            $nome_responsavel = $responsavel ? $responsavel->Nome : '';
            TTransaction::close();
            
            $label_prof_responsavel = new TLabel('Responsável pela aprovação', null, '', 'B');
            $label_titulacao_responsavel = new TLabel('Titulação', null, '', 'B');
            $label_status_estagio = new TLabel('Status', null, '', 'B');
            $label_observacao = new TLabel('Observação', null, '', 'B');
            
            $text_prof_responsavel  = new TTextDisplay($nome_responsavel, null, '', '');
            $text_titulacao_responsavel  = new TTextDisplay($object->titulacao_prof_responsavel, null, '', '');
            $text_status_estagio  = new TTextDisplay($object->status_estagio, null, '', '');
            $text_observacao = new TTextDisplay($object->observacao, null, '', '');

            // Adiciona um destaque visual ao Status da atividade
            $text_status_estagio = new TTextDisplay($object->status_estagio, '', '', '');
            if(trim($object->status_estagio) == 'Aprovado') {
                $text_status_estagio->style = 'color: #00ffaa; font-weight: bold;';
            }
            else if(trim($object->status_estagio) == 'Reprovado') {
                $text_status_estagio->style = 'color: #ff0000; font-weight: bold;';
            }

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
            TTransaction::rollback();
        }
    }
}
