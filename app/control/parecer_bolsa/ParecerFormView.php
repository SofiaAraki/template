<?php
/**
 * ReqBolsaAlunoFormView Form
 * @author  <your name here>
 */
class ParecerFormView extends TPage
{
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Parecer_View');
        
        $this->form->setFormTitle('Requerimento de Bolsa - Análise Assistente Social');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        $this->form->addHeaderActionLink( _t('Print'), new TAction([$this, 'onPrint'], ['key'=>$param['key'], 'static' => '1']), 'far:file-pdf red');
        $this->form->addHeaderActionLink( _t('Edit'), new TAction(['AnaliseRequerimentoBolsa', 'onEdit'], ['key'=>$param['key'], 'register_state'=>'true']), 'far:edit blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    /**
     * Show data
     */
    public function onEdit( $param )
    {
        try
        {
            TTransaction::open('Felabs_DB');
        
            $object = new ReqBolsaAluno($param['key']);
            
            $label_id = new TLabel('Id:', '', '', 'B');
            $label_system_user_id = new TLabel('System User Id:', '', '', 'B');
            $label_nome = new TLabel('Nome:', '', '', 'B');
            $label_curso = new TLabel('Curso:', '', '', 'B');
            $label_ciclo = new TLabel('Ciclo:', '', '', 'B');
            $label_periodo = new TLabel('Periodo:', '', '', 'B');
            $label_rg = new TLabel('Rg:', '', '', 'B');
            $label_cpf = new TLabel('Cpf:', '', '', 'B');
            $label_data_nascimento = new TLabel('Data Nascimento:', '', '', 'B');
            $label_estado_civil = new TLabel('Estado Civil:', '', '', 'B');
            $label_profissao = new TLabel('Profissao:', '', '', 'B');
            $label_endereco = new TLabel('Endereco:', '', '', 'B');
            $label_endereco_numero = new TLabel('Endereco Numero:', '', '', 'B');
            $label_endereco_complemento = new TLabel('Endereco Complemento:', '', '', 'B');
            $label_cidade = new TLabel('Cidade:', '', '', 'B');
            $label_estado = new TLabel('Estado:', '', '', 'B');
            $label_cep = new TLabel('Cep:', '', '', 'B');
            $label_telefone = new TLabel('Telefone:', '', '', 'B');
            $label_celular = new TLabel('Celular:', '', '', 'B');
            $label_telefone_trabalho = new TLabel('Telefone Trabalho:', '', '', 'B');
            $label_email = new TLabel('Email:', '', '', 'B');
            $label_data_reg = new TLabel('Data Reg:', '', '', 'B');
            $label_situacao = new TLabel('Situacao:', '', '', 'B');
            $label_moradia = new TLabel('Moradia:', '', '', 'B');
            $label_moradia_aluno = new TLabel('Moradia Aluno:', '', '', 'B');
            $label_saude_familiar = new TLabel('Saude Familiar:', '', '', 'B');
            $label_saude_aluno = new TLabel('Saude Aluno:', '', '', 'B');
            $label_saude_aluno_neces = new TLabel('Saude Aluno Neces:', '', '', 'B');
            $label_veiculo_aluno = new TLabel('Veiculo Aluno:', '', '', 'B');
            $label_ensino_aluno = new TLabel('Ensino Aluno:', '', '', 'B');
            $label_checar = new TLabel('Checar:', '', '', 'B');
            $label_filename = new TLabel('Filename:', '', '', 'B');
            $label_obs = new TLabel('Obs:', '', '', 'B');
            $label_renda_familiar = new TLabel('Renda Familiar:', '', '', 'B');
            $label_n_pessoa = new TLabel('N Pessoa:', '', '', 'B');
            $label_renda_percapita = new TLabel('Renda Percapita:', '', '', 'B');
            $label_rf_salario_minimo = new TLabel('Rf Salario Minimo:', '', '', 'B');
            $label_rp_salario_minimo = new TLabel('Rp Salario Minimo:', '', '', 'B');
            $label_data_final = new TLabel('Data Final:', '', '', 'B');
            $label_bairro = new TLabel('Bairro:', '', '', 'B');
            $label_unidade = new TLabel('Unidade:', '', '', 'B');
            $label_cad_unico = new TLabel('Cad Unico:', '', '', 'B');
            $label_obs_ass_social = new TLabel('Obs Ass Social:', '', '', 'B');
            $label_outra_graduacao = new TLabel('Outra Graduacao:', '', '', 'B');
            $label_graduacao_anterior = new TLabel('Graduacao Anterior:', '', '', 'B');
            $label_renda_familiar_apurada = new TLabel('Renda Familiar Apurada:', '', '', 'B');
            $label_n_pessoas_apurado = new TLabel('N Pessoas Apurado:', '', '', 'B');
            $label_renda_percapita_apurada = new TLabel('Renda Percapita Apurada:', '', '', 'B');
            $label_rf_salario_minimo_apurada = new TLabel('Rf Salario Minimo Apurada:', '', '', 'B');
            $label_rp_salario_minimo_apurada = new TLabel('Rp Salario Minimo Apurada:', '', '', 'B');
            $label_salario_minimo_atual = new TLabel('Salario Minimo Atual:', '', '', 'B');
            $label_documentos_check = new TLabel('Documentos Check:', '', '', 'B');
            $label_analise_check = new TLabel('Analise Check:', '', '', 'B');
            $label_percentual_bolsa = new TLabel('Percentual Bolsa:', '', '', 'B');
            $label_parecer_assist_social = new TLabel('Parecer Assist Social:', '', '', 'B');
            $label_aluno_retido = new TLabel('Aluno Retido:', '', '', 'B');
            $label_aluno_pendencia_finc = new TLabel('Aluno Pendencia Finc:', '', '', 'B');
            $label_parecer_comissao = new TLabel('Parecer Comissao:', '', '', 'B');
            $label_situacaofinal_bolsa = new TLabel('Situacaofinal Bolsa:', '', '', 'B');
            $label_obs_final_assistente = new TLabel('Obs Final Assistente:', '', '', 'B');

            $text_id  = new TTextDisplay($object->id, '', '', '');
            $text_system_user_id  = new TTextDisplay($object->system_user_id, '', '', '');
            $text_nome  = new TTextDisplay($object->nome, '', '', '');
            $text_curso  = new TTextDisplay($object->curso, '', '', '');
            $text_ciclo  = new TTextDisplay($object->ciclo, '', '', '');
            $text_periodo  = new TTextDisplay($object->periodo, '', '', '');
            $text_rg  = new TTextDisplay($object->rg, '', '', '');
            $text_cpf  = new TTextDisplay($object->cpf, '', '', '');
            $text_data_nascimento  = new TTextDisplay($object->data_nascimento, '', '', '');
            $text_estado_civil  = new TTextDisplay($object->estado_civil, '', '', '');
            $text_profissao  = new TTextDisplay($object->profissao, '', '', '');
            $text_endereco  = new TTextDisplay($object->endereco, '', '', '');
            $text_endereco_numero  = new TTextDisplay($object->endereco_numero, '', '', '');
            $text_endereco_complemento  = new TTextDisplay($object->endereco_complemento, '', '', '');
            $text_cidade  = new TTextDisplay($object->cidade, '', '', '');
            $text_estado  = new TTextDisplay($object->estado, '', '', '');
            $text_cep  = new TTextDisplay($object->cep, '', '', '');
            $text_telefone  = new TTextDisplay($object->telefone, '', '', '');
            $text_celular  = new TTextDisplay($object->celular, '', '', '');
            $text_telefone_trabalho  = new TTextDisplay($object->telefone_trabalho, '', '', '');
            $text_email  = new TTextDisplay($object->email, '', '', '');
            $text_data_reg  = new TTextDisplay($object->data_reg, '', '', '');
            $text_situacao  = new TTextDisplay($object->situacao, '', '', '');
            $text_moradia  = new TTextDisplay($object->moradia, '', '', '');
            $text_moradia_aluno  = new TTextDisplay($object->moradia_aluno, '', '', '');
            $text_saude_familiar  = new TTextDisplay($object->saude_familiar, '', '', '');
            $text_saude_aluno  = new TTextDisplay($object->saude_aluno, '', '', '');
            $text_saude_aluno_neces  = new TTextDisplay($object->saude_aluno_neces, '', '', '');
            $text_veiculo_aluno  = new TTextDisplay($object->veiculo_aluno, '', '', '');
            $text_ensino_aluno  = new TTextDisplay($object->ensino_aluno, '', '', '');
            $text_checar  = new TTextDisplay($object->checar, '', '', '');
            $text_filename  = new TTextDisplay($object->filename, '', '', '');
            $text_obs  = new TTextDisplay($object->obs, '', '', '');
            $text_renda_familiar  = new TTextDisplay($object->renda_familiar, '', '', '');
            $text_n_pessoa  = new TTextDisplay($object->n_pessoa, '', '', '');
            $text_renda_percapita  = new TTextDisplay($object->renda_percapita, '', '', '');
            $text_rf_salario_minimo  = new TTextDisplay($object->rf_salario_minimo, '', '', '');
            $text_rp_salario_minimo  = new TTextDisplay($object->rp_salario_minimo, '', '', '');
            $text_data_final  = new TTextDisplay($object->data_final, '', '', '');
            $text_bairro  = new TTextDisplay($object->bairro, '', '', '');
            $text_unidade  = new TTextDisplay($object->unidade, '', '', '');
            $text_cad_unico  = new TTextDisplay($object->cad_unico, '', '', '');
            $text_obs_ass_social  = new TTextDisplay($object->obs_ass_social, '', '', '');
            $text_outra_graduacao  = new TTextDisplay($object->outra_graduacao, '', '', '');
            $text_graduacao_anterior  = new TTextDisplay($object->graduacao_anterior, '', '', '');
            $text_renda_familiar_apurada  = new TTextDisplay($object->renda_familiar_apurada, '', '', '');
            $text_n_pessoas_apurado  = new TTextDisplay($object->n_pessoas_apurado, '', '', '');
            $text_renda_percapita_apurada  = new TTextDisplay($object->renda_percapita_apurada, '', '', '');
            $text_rf_salario_minimo_apurada  = new TTextDisplay($object->rf_salario_minimo_apurada, '', '', '');
            $text_rp_salario_minimo_apurada  = new TTextDisplay($object->rp_salario_minimo_apurada, '', '', '');
            $text_salario_minimo_atual  = new TTextDisplay($object->salario_minimo_atual, '', '', '');
            $text_documentos_check  = new TTextDisplay($object->documentos_check, '', '', '');
            $text_analise_check  = new TTextDisplay($object->analise_check, '', '', '');
            $text_percentual_bolsa  = new TTextDisplay($object->percentual_bolsa, '', '', '');
            $text_parecer_assist_social  = new TTextDisplay($object->parecer_assist_social, '', '', '');
            $text_aluno_retido  = new TTextDisplay($object->aluno_retido, '', '', '');
            $text_aluno_pendencia_finc  = new TTextDisplay($object->aluno_pendencia_finc, '', '', '');
            $text_parecer_comissao  = new TTextDisplay($object->parecer_comissao, '', '', '');
            $text_situacaofinal_bolsa  = new TTextDisplay($object->situacaofinal_bolsa, '', '', '');
            $text_obs_final_assistente  = new TTextDisplay($object->obs_final_assistente, '', '', '');

            $this->form->addFields([$label_id],[$text_id]);
            $this->form->addFields([$label_system_user_id],[$text_system_user_id]);
            $this->form->addFields([$label_nome],[$text_nome]);
            $this->form->addFields([$label_curso],[$text_curso]);
            $this->form->addFields([$label_ciclo],[$text_ciclo]);
            $this->form->addFields([$label_periodo],[$text_periodo]);
            $this->form->addFields([$label_rg],[$text_rg]);
            $this->form->addFields([$label_cpf],[$text_cpf]);
            $this->form->addFields([$label_data_nascimento],[$text_data_nascimento]);
            $this->form->addFields([$label_estado_civil],[$text_estado_civil]);
            $this->form->addFields([$label_profissao],[$text_profissao]);
            $this->form->addFields([$label_endereco],[$text_endereco]);
            $this->form->addFields([$label_endereco_numero],[$text_endereco_numero]);
            $this->form->addFields([$label_endereco_complemento],[$text_endereco_complemento]);
            $this->form->addFields([$label_cidade],[$text_cidade]);
            $this->form->addFields([$label_estado],[$text_estado]);
            $this->form->addFields([$label_cep],[$text_cep]);
            $this->form->addFields([$label_telefone],[$text_telefone]);
            $this->form->addFields([$label_celular],[$text_celular]);
            $this->form->addFields([$label_telefone_trabalho],[$text_telefone_trabalho]);
            $this->form->addFields([$label_email],[$text_email]);
            $this->form->addFields([$label_data_reg],[$text_data_reg]);
            $this->form->addFields([$label_situacao],[$text_situacao]);
            $this->form->addFields([$label_moradia],[$text_moradia]);
            $this->form->addFields([$label_moradia_aluno],[$text_moradia_aluno]);
            $this->form->addFields([$label_saude_familiar],[$text_saude_familiar]);
            $this->form->addFields([$label_saude_aluno],[$text_saude_aluno]);
            $this->form->addFields([$label_saude_aluno_neces],[$text_saude_aluno_neces]);
            $this->form->addFields([$label_veiculo_aluno],[$text_veiculo_aluno]);
            $this->form->addFields([$label_ensino_aluno],[$text_ensino_aluno]);
            $this->form->addFields([$label_checar],[$text_checar]);
            $this->form->addFields([$label_filename],[$text_filename]);
            $this->form->addFields([$label_obs],[$text_obs]);
            $this->form->addFields([$label_renda_familiar],[$text_renda_familiar]);
            $this->form->addFields([$label_n_pessoa],[$text_n_pessoa]);
            $this->form->addFields([$label_renda_percapita],[$text_renda_percapita]);
            $this->form->addFields([$label_rf_salario_minimo],[$text_rf_salario_minimo]);
            $this->form->addFields([$label_rp_salario_minimo],[$text_rp_salario_minimo]);
            $this->form->addFields([$label_data_final],[$text_data_final]);
            $this->form->addFields([$label_bairro],[$text_bairro]);
            $this->form->addFields([$label_unidade],[$text_unidade]);
            $this->form->addFields([$label_cad_unico],[$text_cad_unico]);
            $this->form->addFields([$label_obs_ass_social],[$text_obs_ass_social]);
            $this->form->addFields([$label_outra_graduacao],[$text_outra_graduacao]);
            $this->form->addFields([$label_graduacao_anterior],[$text_graduacao_anterior]);
            $this->form->addFields([$label_renda_familiar_apurada],[$text_renda_familiar_apurada]);
            $this->form->addFields([$label_n_pessoas_apurado],[$text_n_pessoas_apurado]);
            $this->form->addFields([$label_renda_percapita_apurada],[$text_renda_percapita_apurada]);
            $this->form->addFields([$label_rf_salario_minimo_apurada],[$text_rf_salario_minimo_apurada]);
            $this->form->addFields([$label_rp_salario_minimo_apurada],[$text_rp_salario_minimo_apurada]);
            $this->form->addFields([$label_salario_minimo_atual],[$text_salario_minimo_atual]);
            $this->form->addFields([$label_documentos_check],[$text_documentos_check]);
            $this->form->addFields([$label_analise_check],[$text_analise_check]);
            $this->form->addFields([$label_percentual_bolsa],[$text_percentual_bolsa]);
            $this->form->addFields([$label_parecer_assist_social],[$text_parecer_assist_social]);
            $this->form->addFields([$label_aluno_retido],[$text_aluno_retido]);
            $this->form->addFields([$label_aluno_pendencia_finc],[$text_aluno_pendencia_finc]);
            $this->form->addFields([$label_parecer_comissao],[$text_parecer_comissao]);
            $this->form->addFields([$label_situacaofinal_bolsa],[$text_situacaofinal_bolsa]);
            $this->form->addFields([$label_obs_final_assistente],[$text_obs_final_assistente]);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Print view
     */
    public function onPrint($param)
    {
        try
        {
            $this->onEdit($param);
            
            // string with HTML contents
            $html = clone $this->form;
            $contents = file_get_contents('app/documents/Parecer_Bolsa_FE.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/ReqBolsaAluno-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
