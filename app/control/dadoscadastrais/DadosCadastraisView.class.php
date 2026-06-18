<?php
class DadosCadastraisView extends TPage
{
    protected $form; 
    protected $formFields;
    protected $detail_list;

    public function __construct($param)
    {
        parent::__construct();
        
        try
        {
            TTransaction::open('Felabs_DB');
            
            $unitid = TSession::getValue('userunitid');
            $userid = TSession::getValue('userid');
            $user = new SystemUser($userid);
            
            TTransaction::close();
            
            TTransaction::open('dados_fei');
            
            // Para preenchimento da view
            $fiAluno = new FiAluno($user->systemuser_codlegado);
            $object_cidade = new FiCidade($fiAluno->CodCidade);
            
            /////////// Verifica se é da graduação ou colégio //////////////////////
            $ano_atual = date('Y');
                        
            $array_colegio = ['118' => '118', '119' => '119', '120' => '120'];

            $matriculas = new TRepository('VwAlunoMatriculaEtapa');
            
            $criteria_cadastro = new TCriteria;
            $criteria_cadastro->add(new TFilter('Codaluno', '=', $user->systemuser_codlegado));
            $criteria_cadastro->add(new TFilter('AnoMatricula', '=', $ano_atual));
            $criteria_cadastro->add(new TFilter('CodCurso', 'NOT IN', $array_colegio));
                                    
            $aluno = $matriculas->load($criteria_cadastro);
    
            TTransaction::close();            
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
        
        // Creates the form
        $this->form = new BootstrapFormBuilder('form_DadosCadastrais');
        $this->form->setFormTitle('Meu Cadastro');

        // Estilização das tags/badges para dar destaque visual moderno
        $sexoBadge = $fiAluno->Sexo == 'F' ? 'success' : 'info';
        $corRacaVal = $fiAluno->CorRaca ? $fiAluno->CorRaca : 'Não Informada';

        // Customização dos elementos de exibição (Valores)
        $text_nome         = '<b>' . $fiAluno->Nome . '</b>';
        $text_nascimento   = TDate::date2br($fiAluno->Datanascimento);
        $text_sexo         = "<span class='label label-{$sexoBadge}'>" . ($fiAluno->Sexo == 'F' ? 'Feminino' : 'Masculino') . "</span>";
        $text_naturalidade = $fiAluno->Naturalidade;
        $text_uf           = "<span class='label label-default'>{$fiAluno->NaturalidadeUF}</span>";
        $text_nacionalidade= $fiAluno->Nacionalidade;
        $text_pai          = $fiAluno->NomePai ? $fiAluno->NomePai : '<i>Não informado</i>';
        $text_mae          = $fiAluno->NomeMae;
        $text_rg           = $fiAluno->Rg;
        $text_expedidor    = $fiAluno->RgOrgaoExpedidor;
        $text_profissao    = $fiAluno->Profissao ? $fiAluno->Profissao : '<i>Não informada</i>';
        $text_cpf          = $fiAluno->CPF;
        $text_civil        = $fiAluno->EstadoCivil;
        $text_raca         = "<span class='label label-primary'>{$corRacaVal}</span>";        

        // Container principal onde vamos injetar o grid customizado
        $gridContainer = new TElement('div');
        $gridContainer->style = 'padding: 10px 15px;';

        // --- FUNÇÃO AUXILIAR PARA CRIAR OS BLOCOS DE CAMPO ---
        $createFieldBlock = function($label, $value, $colSize) {
            $col = new TElement('div');
            $col->class = $colSize;
            $col->style = 'margin-bottom: 15px;';
            
            $lbl = new TElement('label');
            $lbl->style = 'display: block; font-weight: bold; margin-bottom: 3px; font-size: 12px; text-transform: uppercase;';
            $lbl->add($label);
            
            $val = new TElement('div');
            $val->style = 'font-size: 14px;';
            $val->add($value);
            
            $col->add($lbl);
            $col->add($val);
            return $col;
        };

        // --- SEÇÃO 1: IDENTIFICAÇÃO ---
        // Criamos o separador como um TElement ou passamos o objeto nativo direto na linha do grid
        $sep1 = new TFormSeparator('Identificação & Informações Gerais');
        $gridContainer->add($sep1);
        
        $row1 = new TElement('div');
        $row1->class = 'row';
        $row1->add($createFieldBlock('Nome Completo', $text_nome, 'col-sm-8'));
        $row1->add($createFieldBlock('Data de Nascimento', $text_nascimento, 'col-sm-4'));
        $gridContainer->add($row1);

        $row2 = new TElement('div');
        $row2->class = 'row';
        $row2->add($createFieldBlock('Sexo', $text_sexo, 'col-sm-3'));
        $row2->add($createFieldBlock('Estado Civil', $text_civil, 'col-sm-4'));
        $row2->add($createFieldBlock('Cor / Raça', $text_raca, 'col-sm-5'));
        $gridContainer->add($row2);

        // --- SEÇÃO 2: DOCUMENTAÇÃO ---
        $sep2 = new TFormSeparator('Documentação');
        $gridContainer->add($sep2);

        $row3 = new TElement('div');
        $row3->class = 'row';
        $row3->add($createFieldBlock('CPF', $text_cpf, 'col-sm-4'));
        $row3->add($createFieldBlock('RG', $text_rg, 'col-sm-4'));
        $row3->add($createFieldBlock('Órgão Expedidor', $text_expedidor, 'col-sm-4'));
        $gridContainer->add($row3);

        // --- SEÇÃO 3: ORIGEM E FILIAÇÃO ---
        $sep3 = new TFormSeparator('Origem & Filiação');
        $gridContainer->add($sep3);

        $row4 = new TElement('div');
        $row4->class = 'row';
        $row4->add($createFieldBlock('Naturalidade', $text_naturalidade, 'col-sm-5'));
        $row4->add($createFieldBlock('UF', $text_uf, 'col-sm-2'));
        $row4->add($createFieldBlock('Nacionalidade', $text_nacionalidade, 'col-sm-5'));
        $gridContainer->add($row4);

        $row5 = new TElement('div');
        $row5->class = 'row';
        $row5->add($createFieldBlock('Profissão', $text_profissao, 'col-sm-12'));
        $gridContainer->add($row5);

        $row6 = new TElement('div');
        $row6->class = 'row';
        $row6->add($createFieldBlock('Nome da Mãe', $text_mae, 'col-sm-6'));
        $row6->add($createFieldBlock('Nome do Pai', $text_pai, 'col-sm-6'));
        $gridContainer->add($row6);

        // Agora sim, injetamos todo o conjunto (Separadores + Linhas sincronizadas)
        $this->form->addContent([$gridContainer]);

        // Define as ações de cabeçalho baseadas no tipo de curso
        if($aluno)
        {
            $this->form->addHeaderAction('Atualizar informações de contato', new TAction([$this, 'onSetDadosContato'], ['Codaluno' => $fiAluno->Codaluno]), 'far:edit blue fa-lg');            
        }       
        else
        {
            $this->form->addHeaderAction('Solicitar Alteração', new TAction(['TicketFormListAluno', 'onReload']), 'far:edit blue fa-lg');
        }
        
        // Create the page container
        $container = new TVBox;
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->style = 'width: 100%';
        $container->add($this->form);

        $div = new TElement('div');
        $div->add($container);
        $container->style = 'width:100%;';

        parent::add($div);
    }
  
    public function onSetDadosContato($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
            
            $cod_aluno = $param['Codaluno'];
            $repository = new TRepository('ContatoAluno');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $cod_aluno));
            
            $contato_aluno = $repository->load($criteria);
           
            if($contato_aluno)
            {
                $parametros['key'] = $contato_aluno[0]->id;
                TApplication::loadPage('DadosCadastraisAlunoEditForm', 'onEdit', $parametros);
            }
            else
            {
                $parametros['cod_aluno'] = $cod_aluno;
                TApplication::loadPage('DadosCadastraisAlunoEditForm', 'onLoad', $parametros);
            }
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }    
    }
    
    public function onLoad()
    {
    }
}