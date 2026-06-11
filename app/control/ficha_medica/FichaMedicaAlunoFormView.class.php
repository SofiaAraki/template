<?php


class FichaMedicaAlunoFormView extends TPage
{
    protected $form; 
    protected $formFields;
    protected $detail_list;


    public function __construct($param)
    {
        parent::__construct();
        
        
        $unitid = TSession::getValue('userunitid');
        $userid = TSession::getValue('userid');
                
        if($unitid <> 12)
        {
            new TMessage('error', 'Funcionalidade não disponível para esta unidade');
            die;
        }
            
            
        try
        {
            TTransaction::open('Felabs_DB');

            $user = new SystemUser($userid);


            $repository = new TRepository('FichaMedica');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $user->systemuser_codlegado));
            
            $object = FichaMedica::getObjects($criteria);

            TTransaction::close();
            
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_FichaMedicaAluno');
        $this->form->setFormTitle('Ficha Médica');

       
        $text1  = new TTextDisplay($object[0]->cod_aluno, '#333333', '14px', '');
        $text2  = new TTextDisplay($object[0]->nome, '#333333', '14px', '');
        $text3  = new TTextDisplay($object[0]->rg, '#333333', '14px', '');
        $text4  = new TTextDisplay($object[0]->cpf, '#333333', '14px', '');
        $text5  = new TTextDisplay(TDate::date2br($object[0]->data_nasc), '#333333', '14px', '');
        $text6  = new TTextDisplay($object[0]->bairro, '#333333', '14px', '');
        $text7  = new TTextDisplay($object[0]->endereco, '#333333', '14px', '');
        $text8  = new TTextDisplay($object[0]->cidade, '#333333', '14px', '');
        $text9  = new TTextDisplay($object[0]->cep, '#333333', '14px', '');
        $text10  = new TTextDisplay($object[0]->responsavel, '#333333', '14px', '');
        $text11  = new TTextDisplay($object[0]->aluno_mora, '#333333', '14px', '');
        $text12  = new TTextDisplay($object[0]->telefone, '#333333', '14px', '');
        $text13  = new TTextDisplay($object[0]->tipo_sang, '#333333', '14px', '');
        $text14  = new TTextDisplay($object[0]->alergico_s_n, '#333333', '14px', '');
        $text15  = new TTextDisplay($object[0]->alergico, '#333333', '14px', '');
        $text16  = new TTextDisplay($object[0]->alergico_alimento_s_n, '#333333', '14px', '');
        $text17  = new TTextDisplay($object[0]->alergico_alimento, '#333333', '14px', '');
        $text18  = new TTextDisplay($object[0]->nome_pai, '#333333', '14px', '');
        $text19  = new TTextDisplay($object[0]->empresa_pai, '#333333', '14px', '');
        $text20  = new TTextDisplay($object[0]->telefone_pai, '#333333', '14px', '');
        $text21  = new TTextDisplay($object[0]->nome_mae, '#333333', '14px', '');
        $text22  = new TTextDisplay($object[0]->empresa_mae, '#333333', '14px', '');
        $text23  = new TTextDisplay($object[0]->telefone_mae, '#333333', '14px', '');
        $text24  = new TTextDisplay($object[0]->nome_outros, '#333333', '14px', '');
        $text25  = new TTextDisplay($object[0]->empresa_outros, '#333333', '14px', '');
        $text26  = new TTextDisplay($object[0]->telefone_outros, '#333333', '14px', '');
        $text27  = new TTextDisplay($object[0]->plano_de_saude_s_n, '#333333', '14px', '');
        $text28  = new TTextDisplay($object[0]->plano_de_saude, '#333333', '14px', '');
        $text29  = new TTextDisplay($object[0]->alergico_medicamento_s_n, '#333333', '14px', '');
        $text30  = new TTextDisplay($object[0]->alergico_medicamento, '#333333', '14px', '');
        $text31  = new TTextDisplay($object[0]->medico_aluno, '#333333', '14px', '');
        $text32  = new TTextDisplay($object[0]->nome_medico, '#333333', '14px', '');
        $text33  = new TTextDisplay($object[0]->endereco_medico, '#333333', '14px', '');
        $text34  = new TTextDisplay($object[0]->telefone_medico, '#333333', '14px', '');
        $text35  = new TTextDisplay($object[0]->observacao_febre, '#333333', '14px', '');
        $text36  = new TTextDisplay($object[0]->hipertensao_s_n, '#333333', '14px', '');
        $text37  = new TTextDisplay($object[0]->epiletico_s_n, '#333333', '14px', '');
        $text38  = new TTextDisplay($object[0]->epiletico_tratamento_s_n, '#333333', '14px', '');
        $text39  = new TTextDisplay($object[0]->hemofilico_s_n, '#333333', '14px', '');
        $text40  = new TTextDisplay($object[0]->deficiente_visual_s_n, '#333333', '14px', '');
        $text41  = new TTextDisplay($object[0]->deficiente_fisico_s_n, '#333333', '14px', '');
        $text42  = new TTextDisplay($object[0]->deficiente_auditivo_s_n, '#333333', '14px', '');
        $text43  = new TTextDisplay($object[0]->diabetico_s_n, '#333333', '14px', '');
        $text44  = new TTextDisplay($object[0]->diabetico_insulina, '#333333', '14px', '');
        $text45  = new TTextDisplay($object[0]->asmatico_s_n, '#333333', '14px', '');
        $text46  = new TTextDisplay($object[0]->transtorno_s_n, '#333333', '14px', '');
        $text47  = new TTextDisplay($object[0]->transtorno, '#333333', '14px', '');
        $text48  = new TTextDisplay($object[0]->tratamento_medico_s_n, '#333333', '14px', '');
        $text49  = new TTextDisplay($object[0]->tratamento_medico, '#333333', '14px', '');
        $text50  = new TTextDisplay($object[0]->necessidade_s_n, '#333333', '14px', '');
        $text51  = new TTextDisplay($object[0]->necessidade, '#333333', '14px', '');
        $text52  = new TTextDisplay($object[0]->ingere_medicamentos_s_n, '#333333', '14px', '');
        $text53  = new TTextDisplay($object[0]->ingere_medicamentos, '#333333', '14px', '');
        $text54  = new TTextDisplay($object[0]->aluno_hospital, '#333333', '14px', '');
        $text55  = new TTextDisplay($object[0]->acp_psicologico_s_n, '#333333', '14px', '');
        $text56  = new TTextDisplay($object[0]->acp_psicologico, '#333333', '14px', '');
        //$text57  = new TTextDisplay($object[0]->termo, '#333333', '14px', '');
        

        
                

        $this->form->addFields( [ new TFormSeparator('Informações da Ficha Médica') ] );
        $this->form->addFields( [ new TLabel('*Código do aluno:') ], [ $text1 ],[ new TLabel('*Aluno(a):') ], [ $text2 ] );
        $this->form->addFields( [ new TLabel('*RG:') ], [ $text3 ], [ new TLabel('*CPF:') ], [ $text4 ] );
        $this->form->addFields( [ new TLabel('*Data de nascimento:') ], [ $text5 ], [ new TLabel('*Bairro:') ], [ $text6 ] );
        $this->form->addFields( [ new TLabel('*Endereço:') ], [ $text7 ], [ new TLabel('*Cidade:') ], [ $text8 ] );
        $this->form->addFields( [ new TLabel('*CEP:') ], [ $text9 ], [ new TLabel('*Responsável pelo aluno(a):') ], [ $text10 ] );
        $this->form->addFields( [ new TLabel('*Com quem mora o(a) aluno(a)?') ], [ $text11 ]);        
        $this->form->addFields( [ new TLabel('*Telefone(s) / Comercial / Residencial / Celular/WhatsApp:') ], [ $text12 ] );
        $this->form->addFields( [ new TLabel('*<b>Tipo Sanguíneo?</b>', '#ff0000')], [ $text13 ] );
        $this->form->addFields( [ new TLabel('*O(a) aluno(a) é alérgico(a)? (Sim/Não)')], [ $text14 ] );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [ $text15 ] );
        //$this->form->addFields( [ new TLabel('<b>O(a) aluno(a) é alérgico a algum medicamento tópico, oral ou injetável?</b>','#ff0000'), $medicamento ] );
        $this->form->addFields( [ new TLabel('*O(a) aluno(a) tem alergia a algum tipo de alimento?')], [ $text16 ] );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [ $text17 ] );
        $this->form->addFields( [ new TLabel('Nome do pai:')], [ $text18 ] );
        $this->form->addFields( [ new TLabel('Empresa em que o pai trabalha:')],[ $text19 ],[ new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Pai):')], [ $text20 ] );
        $this->form->addFields(  );
        $this->form->addFields( [ new TLabel('Nome da mãe:')], [ $text21 ] );
        $this->form->addFields( [ new TLabel('Empresa em que a mãe trabalha:')], [ $text22 ], [ new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Mãe):')],[ $text23 ] );
        $this->form->addFields( );
        $this->form->addFields( [ new TLabel('Nome de outros:')], [ $text24 ] );
        $this->form->addFields( [ new TLabel('Empresa em que outros trabalha:')], [ $text25 ],[ new TLabel('Telefone(s) / Comercial / Residencial / Celular/WhatsApp (Outros):')], [ $text26 ] );
        $this->form->addFields(  );
        $this->form->addFields( [ new TLabel('*1 - O(a) aluno(a) possui plano de saúde?')], [ $text27 ] );
        $this->form->addFields( [ new TLabel('Sim. Qual? (Número da Carteirinha)')], [ $text28 ]);
        $this->form->addFields( [ new TLabel('<b>*2 - O(a) aluno(a) é alérgico a algum medicamento tópico, oral ou injetável?</b>','#ff0000')], [ $text29 ] );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [ $text30 ]);
        $this->form->addFields( [ new TLabel('3 - O médico do(a) aluno(a) é:')], [ $text31 ] );
        $this->form->addFields( [ new TLabel('4 - Nome do médico:')], [ $text32 ] );
        $this->form->addFields( [ new TLabel('Endereço do médico:')], [ $text33 ] );
        $this->form->addFields( [ new TLabel('Telefones para contato do médico (inclusive celular):')], [ $text34 ]);
        $this->form->addFields( [ new TLabel('5 - Em caso de febre alta, não sendo localizado os pais ou responsáveis pelo(a) aluno(a) com qual medicamento ele deverá ser medicado e a quantidade, por indicação médica:')],[ $text35 ] );
        //$this->form->addFields( [ new TLabel('6 - A criança tem doença congênita? (Sim/Não)'), $doenca_congenita_s_n ] );
        //$this->form->addFields( [ new TLabel('Sim. Qual?'), $doenca_congenita ] );
        $this->form->addFields( [ new TLabel('*6 - Tem hipertensão? (Sim/Não)')], [ $text36 ] );
        //$this->form->addFields( [ new TLabel('8 - Quais as doenças contagiosas da infância já contraídas? '), $doencas_contraidas_infancia ] );
        $this->form->addFields( [ new TLabel('*7 - É epilético? (Sim/Não)')], [ $text37 ]  );
        $this->form->addFields( [ new TLabel('Em caso de afirmativo, está em tratamento? (Sim/Não)')], [ $text38 ]);
        $this->form->addFields( [ new TLabel('*8 - É hemofílico? (Sim/Não)')], [ $text39 ] );
        $this->form->addFields( [ new TLabel('*9 - É deficiente visual? (Sim/Não)')], [ $text40 ] );
        $this->form->addFields( [ new TLabel('*10 - É deficiente físico? (Sim/Não)')], [ $text41 ] );
        $this->form->addFields( [ new TLabel('*11 - É deficiente auditivo? (Sim/Não)')], [ $text42 ] );
        //$this->form->addFields( [ new TLabel('14 - É deficiente intelectual? (Sim/Não)'), $deficiente_intelectual_s_n ] );
        //$this->form->addFields( [ new TLabel('15 - É TEA? (Sim/Não)'), $tea_s_n ] );
        $this->form->addFields( [ new TLabel('*12 - É diabético? (Sim/Não)')],[ $text43 ] );
        $this->form->addFields( [ new TLabel('Em caso de afirmativo: é dependente de insulina? (Sim/Não)')], [ $text44 ] );
        $this->form->addFields( [ new TLabel('*13 - É asmático? (Sim/Não)')], [ $text45 ] );
        $this->form->addFields( [ new TLabel('*14 - Apresenta algum tipo de transtorno; diagnosticado? (Sim/Não)')],[ $text46 ] );
        $this->form->addFields( [ new TLabel('Sim. Qual é?')], [ $text47 ]);
        $this->form->addFields( [ new TLabel('*15 - Está fazendo algum tipo de tratamento médico psicológico? (Sim/Não)')], [ $text48 ]);
        $this->form->addFields( [ new TLabel('Sim. Qual?')], [ $text49 ]  );
        $this->form->addFields( [ new TLabel('*16 - O(a) aluno(a) possui alguma necessidade específica? (Sim/Não)')], [ $text50 ]  );
        $this->form->addFields( [ new TLabel('Sim. Qual?')], [ $text51 ]);
        //$this->form->addFields( [ new TLabel('21 - O(a) aluno(a) tem dificuldades e/ou transtornos de aprendizagem, diagnosticados? (Sim/Não)'), $dificuldades_s_n ] );
        //$this->form->addFields( [ new TLabel('Sim. Qual(is)?'), $dificuldades ] );
        $this->form->addFields( [ new TLabel('*17 - Está ingerindo medicação específica? (Sim/Não)')], [ $text52 ]  );
        $this->form->addFields( [ new TLabel('Sim. Qual(is)?')], [ $text53 ]);
        $this->form->addFields( [ new TLabel('18 - Em caso de necessidade, o(a) aluno(a) deverá ser removido para qual hospital ou clínica?')], [ $text54 ] );
        $this->form->addFields( [ new TLabel('*19 - Faz acompanhamento psicológico e/ou psiquiátrico? (Sim/Não)')], [ $text55 ] );
        $this->form->addFields( [ new TLabel('Sim. Qual é?')], [ $text56 ] );
        //$this->form->addFields( [ new TLabel('Teste')], [ $text57 ] );
        

        //Após salvar, não permite alteração por parte do aluno
        if(!$object)
        {
            $this->form->addHeaderAction('Atualizar ficha médica', new TAction([$this, 'onSetDadosFichaMedica'], ['cod_aluno' => $user->systemuser_codlegado]), 'far:edit blue fa-lg');            
        }       
        
    
        // create the page container
        $container = new TVBox;
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->style = 'width: 100%';
        $container->add($this->form);

        $div = new TElement('div');
        $div->add($a = $container);
        $a->style = 'width:90%;';

        parent::add($div);
    }
  
    
    public function onSetDadosFichaMedica($param)
    {
        try
        {
            TTransaction::open('Felabs_DB');
      
            $cod_aluno = $param['cod_aluno'];
            
            $repository = new TRepository('FichaMedica');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cod_aluno', '=', $cod_aluno));
            
            $aluno = $repository->load($criteria);
           
            if($aluno)
            {
                $parametros['key'] = $aluno[0]->id;
                
                TApplication::loadPage('FichaMedicaAlunoForm', 'onEdit', $parametros);
            }
            else
            {
                $parametros['cod_aluno'] = $cod_aluno;
                
                TApplication::loadPage('FichaMedicaAlunoForm', 'onLoad', $parametros);
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