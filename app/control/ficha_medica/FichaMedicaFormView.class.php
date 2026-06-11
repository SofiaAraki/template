<?php
/**
 * FichaMedicaFormView Form
 * @author  <your name here>
 */
class FichaMedicaFormView extends TPage
{
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_FichaMedica_View');
        
        $this->form->setFormTitle('Ficha Médica do Aluno');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        $this->form->addHeaderActionLink( _t('Print'), new TAction([$this, 'onPrint'], ['key'=>$param['key'], 'static' => '1']), 'far:file-pdf red');
        $this->form->addHeaderActionLink( _t('Edit'), new TAction(['FichaMedicaForm', 'onEdit'], ['key'=>$param['key'], 'register_state'=>'true']), 'far:edit blue');
        $this->form->addHeaderActionLink(('Voltar para Lista'),  new TAction(['FichaMedicaList', 'onReload']), 'fa:arrow-left');
        
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
        
            $object = new FichaMedica($param['key']);
            
            $label_id = new TLabel('ID do aluno:', '#000000', '', 'B');
            $label_cod_aluno = new TLabel('Código do aluno:', '#000000', '', 'B');
            $label_nome = new TLabel('Nome do aluno:', '#000000', '', 'B');
            $label_rg = new TLabel('RG:', '#000000', '', 'B');
            $label_cpf = new TLabel('CPF:', '#000000', '', 'B');
            $label_data_nasc = new TLabel('Data de nascimento:', '#000000', '', 'B');
            //$label_local_nasc = new TLabel('Local de nascimento:', '#000000', '', 'B');
            $label_endereco = new TLabel('Endereço:', '#000000', '', 'B');
            $label_cidade = new TLabel('Cidade:', '#000000', '', 'B');
            $label_cep = new TLabel('CEP:', '#000000', '', 'B');
            $label_bairro = new TLabel('Bairro:', '#000000', '', 'B');
            $label_responsavel = new TLabel('Responsável pelo aluno(a):', '#000000', '', 'B');
            $label_aluno_mora = new TLabel('Com quem mora o(a) aluno(a)?', '#000000', '', 'B');
            $label_telefone = new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp:', '#000000', '', 'B');
            $label_tipo_sang = new TLabel('<b>Tipo Sanguíneo (em atendimento CITEM/DEINF/CGAB019/2021):</b>', '#ff0000', '', 'B');
            $label_alergico_s_n = new TLabel('O(a) aluno(a) é alérgico(a)? (Sim/Não)', '#000000', '', 'B');
            $label_alergico = new TLabel('Sim. Qual(is)?', '#000000', '', 'B');
            //$label_medicamento = new TLabel('Medicamento:', '#333333', '', 'B');
            $label_alergico_alimento_s_n = new TLabel('O(a) aluno(a) tem alergia a algum tipo de alimento?', '#000000', '', 'B');
            $label_alergico_alimento = new TLabel('Sim. Qual(is)?', '#000000', '', 'B');
            $label_observacao = new TLabel('Observacao:', '##000000', '', 'B');
            $label_nome_pai = new TLabel('Nome do pai:', '#000000', '', 'B');
            $label_empresa_pai = new TLabel('Empresa em que o pai trabalha:', '#000000', '', 'B');
            $label_telefone_pai = new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Pai):', '#000000', '', 'B');
            $label_nome_mae = new TLabel('Nome da mãe:', '#000000', '', 'B');
            $label_empresa_mae = new TLabel('Empresa em que a mãe trabalha:', '#000000', '', 'B');
            $label_telefone_mae = new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Mãe):', '#000000', '', 'B');
            $label_nome_outros = new TLabel('Nome de outros:', '#000000', '', 'B');
            $label_empresa_outros = new TLabel('Empresa em que outros trabalha:', '#000000', '', 'B');
            $label_telefone_outros = new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Outros):', '#000000', '', 'B');
            $label_plano_de_saude_s_n = new TLabel('1 - O(a) aluno(a) possui plano de saúde?', '#000000', '', 'B');
            $label_plano_de_saude = new TLabel('Sim. Qual? (Número da Carteirinha)', '#000000', '', 'B');
            $label_alergico_medicamento_s_n = new TLabel('<b>2 - O(a) aluno(a) é alérgico a algum medicamento tópico, oral ou injetável?</b>','#ff0000', '', 'B');
            $label_alergico_medicamento = new TLabel('Sim. Qual(is)?', '#000000', '', 'B');
            $label_medico_aluno = new TLabel('3 - O médico do(a) aluno(a) é:', '#000000', '', 'B');
            $label_nome_medico = new TLabel('4 - Nome do médico:', '#000000', '', 'B');
            $label_endereco_medico = new TLabel('Endereço do médico:', '#000000', '', 'B');
            $label_telefone_medico = new TLabel('Telefones para contato do médico (inclusive celular):', '#000000', '', 'B');
            $label_observacao_febre = new TLabel('5 - Em caso de febre alta, não sendo localizado os pais ou responsáveis pelo(a) aluno(a) com qual medicamento ele deverá ser medicado e a quantidade, por indicação médica:', '#000000', '', 'B');
            //$label_doenca_congenita_s_n = new TLabel('Doenca Congenita S N:', '#333333', '', 'B');
            //$label_doenca_congenita = new TLabel('Doenca Congenita:', '#333333', '', 'B');
            $label_hipertensao_s_n = new TLabel('6 - Tem hipertensão? (Sim/Não)', '#000000', '', 'B');
            //$label_hipertensao = new TLabel('Hipertensao:', '#333333', '', 'B');
            //$label_doencas_contraidas_infancia = new TLabel('Doencas Contraidas Infancia:', '#333333', '', 'B');
            $label_epiletico_s_n = new TLabel('7 - É epilético? (Sim/Não)', '#000000', '', 'B');
            $label_epiletico_tratamento_s_n = new TLabel('Em caso de afirmativo, está em tratamento? (Sim/Não)', '#000000', '', 'B');
            $label_hemofilico_s_n = new TLabel('8 - É hemofílico? (Sim/Não)', '#000000', '', 'B');
            $label_deficiente_visual_s_n = new TLabel('9 - É deficiente visual? (Sim/Não)', '#000000', '', 'B');
            $label_deficiente_fisico_s_n = new TLabel('10 - É deficiente físico? (Sim/Não)', '#000000', '', 'B');
            $label_deficiente_auditivo_s_n = new TLabel('11 - É deficiente auditivo? (Sim/Não)', '#000000', '', 'B');
            //$label_deficiente_intelectual_s_n = new TLabel('Deficiente Intelectual S N:', '#333333', '', 'B');
            //$label_tea_s_n = new TLabel('Tea S N:', '#333333', '', 'B');
            $label_diabetico_s_n = new TLabel('12 - É diabético? (Sim/Não)', '#000000', '', 'B');
            $label_diabetico_insulina = new TLabel('Em caso de afirmativo: é dependente de insulina? (Sim/Não)', '#000000', '', 'B');
            $label_asmatico_s_n = new TLabel('13 - É asmático? (Sim/Não)', '#000000', '', 'B');
            $label_transtorno_s_n = new TLabel('14 - Apresenta algum tipo de transtorno; diagnosticado? (Sim/Não)', '#000000', '', 'B');
            $label_transtorno = new TLabel('Sim. Qual é?', '#000000', '', 'B');
            $label_tratamento_medico_s_n = new TLabel('15 - Está fazendo algum tipo de tratamento médico psicológico? (Sim/Não)', '#000000', '', 'B');
            $label_tratamento_medico = new TLabel('Sim. Qual?', '#000000', '', 'B');
            $label_necessidade_s_n = new TLabel('16 - O(a) aluno(a) possui alguma necessidade específica? (Sim/Não)', '#000000', '', 'B');
            $label_necessidade = new TLabel('Sim. Qual?', '#000000', '', 'B');
            //$label_dificuldades_s_n = new TLabel('Dificuldades S N:', '#333333', '', 'B');
            $label_ingere_medicamentos_s_n = new TLabel('17 - Está ingerindo medicação específica? (Sim/Não)', '#000000', '', 'B');
            $label_ingere_medicamentos = new TLabel('Sim. Qual(is)?', '#000000', '', 'B');
            $label_aluno_hospital = new TLabel('18 - Em caso de necessidade, o(a) aluno(a) deverá ser removido para qual hospital ou clínica?', '#000000', '', 'B');
            //$label_filename = new TLabel('Filename:', '#333333', '', 'B');
            $label_acp_psicologico_s_n = new TLabel('19 - Faz acompanhamento psicológico e/ou psiquiátrico? (Sim/Não)', '#000000', '', 'B');
            $label_acp_psicologico = new TLabel('Sim. Qual é?', '#000000', '', 'B');

            $text_id  = new TTextDisplay($object->id, '#333333', '', '');
            $text_cod_aluno  = new TTextDisplay($object->cod_aluno, '#333333', '', '');
            $text_nome  = new TTextDisplay($object->nome, '#333333', '', '');
            $text_rg  = new TTextDisplay($object->rg, '#333333', '', '');
            $text_cpf  = new TTextDisplay($object->cpf, '#333333', '', '');
            $text_data_nasc  = new TTextDisplay($object->data_nasc, '#333333', '', '');
            //$text_local_nasc  = new TTextDisplay($object->local_nasc, '#333333', '', '');
            $text_endereco  = new TTextDisplay($object->endereco, '#333333', '', '');
            $text_cidade  = new TTextDisplay($object->cidade, '#333333', '', '');
            $text_cep  = new TTextDisplay($object->cep, '#333333', '', '');
            $text_bairro  = new TTextDisplay($object->bairro, '#333333', '', '');
            $text_responsavel  = new TTextDisplay($object->responsavel, '#333333', '', '');
            $text_aluno_mora  = new TTextDisplay($object->aluno_mora, '#333333', '', '');
            $text_telefone  = new TTextDisplay($object->telefone, '#333333', '', '');
            $text_tipo_sang  = new TTextDisplay($object->tipo_sang, '#ff0000', '', '');
            $text_alergico_s_n  = new TTextDisplay($object->alergico_s_n, '#333333', '', '');
            $text_alergico  = new TTextDisplay($object->alergico, '#333333', '', '');
            //$text_medicamento  = new TTextDisplay($object->medicamento, '#333333', '', '');
            $text_alergico_alimento_s_n  = new TTextDisplay($object->alergico_alimento_s_n, '#333333', '', '');
            $text_alergico_alimento  = new TTextDisplay($object->alergico_alimento, '#333333', '', '');
            $text_observacao  = new TTextDisplay($object->observacao, '#333333', '', '');
            $text_nome_pai  = new TTextDisplay($object->nome_pai, '#333333', '', '');
            $text_empresa_pai  = new TTextDisplay($object->empresa_pai, '#333333', '', '');
            $text_telefone_pai  = new TTextDisplay($object->telefone_pai, '#333333', '', '');
            $text_nome_mae  = new TTextDisplay($object->nome_mae, '#333333', '', '');
            $text_empresa_mae  = new TTextDisplay($object->empresa_mae, '#333333', '', '');
            $text_telefone_mae  = new TTextDisplay($object->telefone_mae, '#333333', '', '');
            $text_nome_outros  = new TTextDisplay($object->nome_outros, '#333333', '', '');
            $text_empresa_outros  = new TTextDisplay($object->empresa_outros, '#333333', '', '');
            $text_telefone_outros  = new TTextDisplay($object->telefone_outros, '#333333', '', '');
            $text_plano_de_saude_s_n  = new TTextDisplay($object->plano_de_saude_s_n, '#333333', '', '');
            $text_plano_de_saude  = new TTextDisplay($object->plano_de_saude, '#333333', '', '');
            $text_alergico_medicamento_s_n  = new TTextDisplay($object->alergico_medicamento_s_n, '#ff0000', '', '');
            $text_alergico_medicamento  = new TTextDisplay($object->alergico_medicamento, '#333333', '', '');
            $text_medico_aluno  = new TTextDisplay($object->medico_aluno, '#333333', '', '');
            $text_nome_medico  = new TTextDisplay($object->nome_medico, '#333333', '', '');
            $text_endereco_medico  = new TTextDisplay($object->endereco_medico, '#333333', '', '');
            $text_telefone_medico  = new TTextDisplay($object->telefone_medico, '#333333', '', '');
            $text_observacao_febre  = new TTextDisplay($object->observacao_febre, '#333333', '', '');
            //$text_doenca_congenita_s_n  = new TTextDisplay($object->doenca_congenita_s_n, '#333333', '', '');
            //$text_doenca_congenita  = new TTextDisplay($object->doenca_congenita, '#333333', '', '');
            $text_hipertensao_s_n  = new TTextDisplay($object->hipertensao_s_n, '#333333', '', '');
            //$text_hipertensao  = new TTextDisplay($object->hipertensao, '#333333', '', '');
            //$text_doencas_contraidas_infancia  = new TTextDisplay($object->doencas_contraidas_infancia, '#333333', '', '');
            $text_epiletico_s_n  = new TTextDisplay($object->epiletico_s_n, '#333333', '', '');
            $text_epiletico_tratamento_s_n  = new TTextDisplay($object->epiletico_tratamento_s_n, '#333333', '', '');
            $text_hemofilico_s_n  = new TTextDisplay($object->hemofilico_s_n, '#333333', '', '');
            $text_deficiente_visual_s_n  = new TTextDisplay($object->deficiente_visual_s_n, '#333333', '', '');
            $text_deficiente_fisico_s_n  = new TTextDisplay($object->deficiente_fisico_s_n, '#333333', '', '');
            $text_deficiente_auditivo_s_n  = new TTextDisplay($object->deficiente_auditivo_s_n, '#333333', '', '');
            //$text_deficiente_intelectual_s_n  = new TTextDisplay($object->deficiente_intelectual_s_n, '#333333', '', '');
            //$text_tea_s_n  = new TTextDisplay($object->tea_s_n, '#333333', '', '');
            $text_diabetico_s_n  = new TTextDisplay($object->diabetico_s_n, '#333333', '', '');
            $text_diabetico_insulina  = new TTextDisplay($object->diabetico_insulina, '#333333', '', '');
            $text_asmatico_s_n  = new TTextDisplay($object->asmatico_s_n, '#333333', '', '');
            $text_transtorno_s_n  = new TTextDisplay($object->transtorno_s_n, '#333333', '', '');
            $text_transtorno  = new TTextDisplay($object->transtorno, '#333333', '', '');
            $text_tratamento_medico_s_n  = new TTextDisplay($object->tratamento_medico_s_n, '#333333', '', '');
            $text_tratamento_medico  = new TTextDisplay($object->tratamento_medico, '#333333', '', '');
            $text_necessidade_s_n  = new TTextDisplay($object->necessidade_s_n, '#333333', '', '');
            $text_necessidade  = new TTextDisplay($object->necessidade, '#333333', '', '');
            //$text_dificuldades_s_n  = new TTextDisplay($object->dificuldades_s_n, '#333333', '', '');
            $text_ingere_medicamentos_s_n  = new TTextDisplay($object->ingere_medicamentos_s_n, '#333333', '', '');
            $text_ingere_medicamentos  = new TTextDisplay($object->ingere_medicamentos, '#333333', '', '');
            $text_aluno_hospital  = new TTextDisplay($object->aluno_hospital, '#333333', '', '');
            //$text_filename  = new TTextDisplay($object->filename, '#333333', '', '');
            $text_acp_psicologico_s_n  = new TTextDisplay($object->acp_psicologico_s_n, '#333333', '', '');
            $text_acp_psicologico  = new TTextDisplay($object->acp_psicologico, '#333333', '', '');

            $this->form->addFields([$label_id],[$text_id]);
            $this->form->addFields([$label_cod_aluno],[$text_cod_aluno]);
            $this->form->addFields([$label_nome],[$text_nome]);
            $this->form->addFields([$label_rg],[$text_rg]);
            $this->form->addFields([$label_cpf],[$text_cpf]);
            $this->form->addFields([$label_data_nasc],[$text_data_nasc]);
            //$this->form->addFields([$label_local_nasc],[$text_local_nasc]);
            $this->form->addFields([$label_endereco],[$text_endereco]);
            $this->form->addFields([$label_cidade],[$text_cidade]);
            $this->form->addFields([$label_cep],[$text_cep]);
            $this->form->addFields([$label_bairro],[$text_bairro]);
            $this->form->addFields([$label_responsavel],[$text_responsavel]);
            $this->form->addFields([$label_aluno_mora],[$text_aluno_mora]);
            $this->form->addFields([$label_telefone],[$text_telefone]);
            $this->form->addFields([$label_tipo_sang],[$text_tipo_sang]);
            $this->form->addFields([$label_alergico_s_n],[$text_alergico_s_n]);
            $this->form->addFields([$label_alergico],[$text_alergico]);
            //$this->form->addFields([$label_medicamento],[$text_medicamento]);
            $this->form->addFields([$label_alergico_alimento_s_n],[$text_alergico_alimento_s_n]);
            $this->form->addFields([$label_alergico_alimento],[$text_alergico_alimento]);
            $this->form->addFields([$label_observacao],[$text_observacao]);
            $this->form->addFields([$label_nome_pai],[$text_nome_pai]);
            $this->form->addFields([$label_empresa_pai],[$text_empresa_pai]);
            $this->form->addFields([$label_telefone_pai],[$text_telefone_pai]);
            $this->form->addFields([$label_nome_mae],[$text_nome_mae]);
            $this->form->addFields([$label_empresa_mae],[$text_empresa_mae]);
            $this->form->addFields([$label_telefone_mae],[$text_telefone_mae]);
            $this->form->addFields([$label_nome_outros],[$text_nome_outros]);
            $this->form->addFields([$label_empresa_outros],[$text_empresa_outros]);
            $this->form->addFields([$label_telefone_outros],[$text_telefone_outros]);
            $this->form->addFields([$label_plano_de_saude_s_n],[$text_plano_de_saude_s_n]);
            $this->form->addFields([$label_plano_de_saude],[$text_plano_de_saude]);
            $this->form->addFields([$label_alergico_medicamento_s_n],[$text_alergico_medicamento_s_n]);
            $this->form->addFields([$label_alergico_medicamento],[$text_alergico_medicamento]);
            $this->form->addFields([$label_medico_aluno],[$text_medico_aluno]);
            $this->form->addFields([$label_nome_medico],[$text_nome_medico]);
            $this->form->addFields([$label_endereco_medico],[$text_endereco_medico]);
            $this->form->addFields([$label_telefone_medico],[$text_telefone_medico]);
            $this->form->addFields([$label_observacao_febre],[$text_observacao_febre]);
            //$this->form->addFields([$label_doenca_congenita_s_n],[$text_doenca_congenita_s_n]);
            //$this->form->addFields([$label_doenca_congenita],[$text_doenca_congenita]);
            $this->form->addFields([$label_hipertensao_s_n],[$text_hipertensao_s_n]);
            //$this->form->addFields([$label_hipertensao],[$text_hipertensao]);
            //$this->form->addFields([$label_doencas_contraidas_infancia],[$text_doencas_contraidas_infancia]);
            $this->form->addFields([$label_epiletico_s_n],[$text_epiletico_s_n]);
            $this->form->addFields([$label_epiletico_tratamento_s_n],[$text_epiletico_tratamento_s_n]);
            $this->form->addFields([$label_hemofilico_s_n],[$text_hemofilico_s_n]);
            $this->form->addFields([$label_deficiente_visual_s_n],[$text_deficiente_visual_s_n]);
            $this->form->addFields([$label_deficiente_fisico_s_n],[$text_deficiente_fisico_s_n]);
            $this->form->addFields([$label_deficiente_auditivo_s_n],[$text_deficiente_auditivo_s_n]);
            //$this->form->addFields([$label_deficiente_intelectual_s_n],[$text_deficiente_intelectual_s_n]);
            //$this->form->addFields([$label_tea_s_n],[$text_tea_s_n]);
            $this->form->addFields([$label_diabetico_s_n],[$text_diabetico_s_n]);
            $this->form->addFields([$label_diabetico_insulina],[$text_diabetico_insulina]);
            $this->form->addFields([$label_asmatico_s_n],[$text_asmatico_s_n]);
            $this->form->addFields([$label_transtorno_s_n],[$text_transtorno_s_n]);
            $this->form->addFields([$label_transtorno],[$text_transtorno]);
            $this->form->addFields([$label_tratamento_medico_s_n],[$text_tratamento_medico_s_n]);
            $this->form->addFields([$label_tratamento_medico],[$text_tratamento_medico]);
            $this->form->addFields([$label_necessidade_s_n],[$text_necessidade_s_n]);
            $this->form->addFields([$label_necessidade],[$text_necessidade]);
            //$this->form->addFields([$label_dificuldades_s_n],[$text_dificuldades_s_n]);
            $this->form->addFields([$label_ingere_medicamentos_s_n],[$text_ingere_medicamentos_s_n]);
            $this->form->addFields([$label_ingere_medicamentos],[$text_ingere_medicamentos]);
            $this->form->addFields([$label_aluno_hospital],[$text_aluno_hospital]);
            //$this->form->addFields([$label_filename],[$text_filename]);
            $this->form->addFields([$label_acp_psicologico_s_n],[$text_acp_psicologico_s_n]);
            $this->form->addFields([$label_acp_psicologico],[$text_acp_psicologico]);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    

    public function onPrint($param)
    {
        try
        {
            $this->onEdit($param);
            
            TTransaction::open('Felabs_DB');
            
            $key = $param['key'];            
            
            $ficha_medica = new FichaMedica($key);

            $html = new AdiantiHTMLDocumentParser('app/documents/FichaMedicaImpressao.html', 'A4', 'portrait');
            $html->setMaster($ficha_medica);
            
            $html->process();
                
            $output = $html->getContents();
                
            $document = 'tmp/'.uniqid().'.pdf'; 
            $html = AdiantiHTMLDocumentParser::newFromString($output);
            $html->saveAsPDF($document);
                

            $window = TWindow::create('Ficha Médica', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = 'download.php?file='.$document;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        
            TTransaction::close();
            
/*            // string with HTML contents
            $html = clone $this->form;
            $contents = file_get_contents('app/documents/FichaMedicaImpressao.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/FichaMedica-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();*/
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
