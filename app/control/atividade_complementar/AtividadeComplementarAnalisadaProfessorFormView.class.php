<?php
class AtividadeComplementarAnalisadaProfessorFormView extends TWindow
{
    protected $form;

    public function __construct( $param )
    {    
        parent::__construct();
        parent::setTitle('Atividade Complementar');
        
        // Remove as bordas brutas da janela deixando o visual mais moderno
        $this->setSize(0.8, null);

        $this->form = new BootstrapFormBuilder('form_AtividadeComplementar_View');
        $this->form->setFieldSizes('100%');

        // Cria um container com espaçamento interno confortável
        $container = new TVBox;
        $container->style = 'width: 100%; padding: 15px;';
        $container->add($this->form);
        
        parent::add($container);
    }
    

    public function onEdit( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
        
            $object = new AtividadeComplementar($param['key']);
            
            // CORREÇÃO: Cores fixas (#333333) removidas para herdar o tema do sistema automaticamente.
            // Passamos os labels e valores de forma verticalizada para melhorar a leitura.
            $label_nome_aluno = new TLabel('Aluno:', '', '', 'B');
            $label_nome_curso = new TLabel('Curso:', '', '', 'B');
            $label_ano = new TLabel('Ano:', '', '', 'B');
            $label_semestre = new TLabel('Semestre:', '', '', 'B');
            $label_etapa = new TLabel('Etapa:', '', '', 'B');
            $label_tipo_atividade = new TLabel('Atividade:', '', '', 'B');
            $label_descricao = new TLabel('Descrição:', '', '', 'B');
            $label_data_inicio = new TLabel('Data de início:', '', '', 'B');
            $label_data_termino = new TLabel('Data de término:', '', '', 'B');
            $label_carga_horaria = new TLabel('Horas:', '', '', 'B');            
                                
            $text_nome_aluno = new TTextDisplay($object->nome_aluno, '', '', '');
            $text_nome_curso = new TTextDisplay($object->nome_curso, '', '', '');
            $text_ano = new TTextDisplay($object->ano, '', '', '');
            $text_semestre = new TTextDisplay($object->semestre, '', '', '');
            $text_etapa = new TTextDisplay($object->etapa, '', '', '');
            $text_tipo_atividade = new TTextDisplay($object->tipo_atividade, '', '', '');
            $text_descricao = new TTextDisplay($object->descricao, '', '', '');
            $text_data_inicio = new TTextDisplay(TDate::date2br($object->data_inicio), '', '', '');
            $text_data_termino = new TTextDisplay(TDate::date2br($object->data_termino), '', '', '');
            $text_carga_horaria = new TTextDisplay((int)$object->carga_horaria, '', '', '');
                                
            // Organizando em linhas fluidas com Bootstrap (Label acima do valor)
            $row = $this->form->addFields( [ $label_nome_aluno, $text_nome_aluno ],
                                           [ $label_nome_curso, $text_nome_curso ] );
            $row->layout = ['col-sm-6', 'col-sm-6'];
            
            $row = $this->form->addFields( [ $label_ano, $text_ano ],
                                           [ $label_semestre, $text_semestre ],
                                           [ $label_etapa, $text_etapa ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
            $row = $this->form->addFields( [ $label_tipo_atividade, $text_tipo_atividade ],
                                           [ $label_descricao, $text_descricao ] );
            $row->layout = ['col-sm-6', 'col-sm-6'];
            
            $row = $this->form->addFields( [ $label_data_inicio, $text_data_inicio ],
                                           [ $label_data_termino, $text_data_termino ],
                                           [ $label_carga_horaria, $text_carga_horaria ] );
            $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

            TTransaction::close();
            
            // Pega o nome do professor responsável
            TTransaction::open('dados_fei');            
            $responsavel = new FiProfessor($object->cod_prof_responsavel);            
            TTransaction::close();
            
            $label_prof_responsavel = new TLabel('Responsável pela aprovação:', '', '', 'B');
            $label_titulacao_responsavel = new TLabel('Titulação:', '', '', 'B');
            $label_status_atividade = new TLabel('Status:', '', '', 'B');
            $label_observacao = new TLabel('Observação:', '', '', 'B');
            
            $text_prof_responsavel = new TTextDisplay($responsavel->Nome, '', '', '');
            $text_titulacao_responsavel = new TTextDisplay($object->titulacao_prof_responsavel, '', '', '');
            
            // Adiciona um destaque visual ao Status da atividade
            $text_status_atividade = new TTextDisplay($object->status_atividade, '', '', '');
            if(trim($object->status_atividade) == 'Aprovado') {
                $text_status_atividade->style = 'color: #00ffaa; font-weight: bold;';
            }
            else if(trim($object->status_atividade) == 'Reprovado') {
                $text_status_atividade->style = 'color: #ff0000; font-weight: bold;';
            }
            else if(trim($object->status_atividade) == 'Aguardando aprovação') {
                $text_status_atividade->style = 'color: #ffaa00; font-weight: bold;';
            }
            
            $text_observacao = new TTextDisplay($object->observacao, '', '', '');

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