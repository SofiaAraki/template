<?php
/**
 * HistoricoFinalFormView Master/Detail
 * @author  <your name here>
 */
class HistoricoFinalFormView extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct($param)
    {
        parent::__construct();

        TTransaction::open('dados_fei');
        $this->form = new BootstrapFormBuilder('form_VwDadoshistoricoaluno');
        $this->form->setFormTitle('VwDadoshistoricoaluno');
        
        $master_object = new VwDadoshistoricoaluno($param['key']);

        //var_dump($master_object);
        //die();
        
        $label_codhistorico             = new TLabel('Cod. Historico:', '#333333', '12px', '');
        $label_Codaluno                 = new TLabel('Cod. Aluno:', '#333333', '12px', '');
        $label_Nome                     = new TLabel('Nome:', '#333333', '12px', '');
        $label_NomeIdentificacaoCivil   = new TLabel('Nome Social:', '#333333', '12px', '');
        $label_Datanascimento           = new TLabel('Data Nasc.:', '#333333', '12px', '');
        $label_Naturalidade             = new TLabel('Naturalidade:', '#333333', '12px', '');
        $label_NaturalidadeUF           = new TLabel('UF:', '#333333', '12px', '');
        $label_Nacionalidade            = new TLabel('Nacionalidade:', '#333333', '12px', '');
        $label_Rg                       = new TLabel('RG:', '#333333', '12px', '');
        $label_RgOrgaoExpedidor         = new TLabel('Org. Exp.:', '#333333', '12px', '');
        $label_CPF                      = new TLabel('CPF:', '#333333', '12px', '');
        $label_EscolaEnsinoMedio        = new TLabel('Escola E. M.:', '#333333', '12px', '');
        $label_EscolaEnsinoMedioLocal   = new TLabel('Local E. M.:', '#333333', '12px', '');
        $label_VestibularAno            = new TLabel('Ano Vestibular:', '#333333', '12px', '');
        $label_TipoIngresso             = new TLabel('Tipo Ingresso:', '#333333', '12px', '');
        $label_TipoIngresso2            = new TLabel('Tipo Ingresso:', '#333333', '12px', '');
        $label_DataIngresso             = new TLabel('Data Ingresso:', '#333333', '12px', '');
        $label_DataIngresso2            = new TLabel('Data Ingresso:', '#333333', '12px', '');
        $label_DataConclusaoCurso       = new TLabel('Data Concl. Curso:', '#333333', '12px', '');
        $label_DataColacaoGrau          = new TLabel('Data Colação Grau:', '#333333', '12px', '');
        $label_DataExpedicaoDiploma     = new TLabel('Data Exp. Diploma:', '#333333', '12px', '');
        $label_DataVestibular           = new TLabel('Data Vestibular:', '#333333', '12px', '');
        $label_DataVestibExt            = new TLabel('Data Vestib. Manual:', '#333333', '12px', '');
        $label_DataConclEMExt           = new TLabel('Data Concl. E. M. Manual:', '#333333', '12px', '');
        $label_ObservacaoFinais1        = new TLabel('Obs. Finais 1:', '#333333', '12px', '');
        $label_ObservacaoFinais2        = new TLabel('Obs. Finais 2:', '#333333', '12px', '');
        $label_ObservacaoFinais3        = new TLabel('Obs. Finais 3:', '#333333', '12px', '');
        $label_ObservacaoFinais4        = new TLabel('Obs. Finais 4:', '#333333', '12px', '');
        $label_ObservacaoFinais5        = new TLabel('Obs. Finais 5:', '#333333', '12px', '');
        $label_ObservacaoCadastral1     = new TLabel('Obs. Cadastral 1:', '#333333', '12px', '');
        $label_ObservacaoCadastral2     = new TLabel('Obs. Cadastral 2:', '#333333', '12px', '');
        $label_ObservacaoCadastral3     = new TLabel('Obs. Cadastral 3:', '#333333', '12px', '');
        $label_ObservacaoCadastral4     = new TLabel('Obs. Cadastral 4:', '#333333', '12px', '');
        $label_ObservacaoCadastral5     = new TLabel('Obs. Cadastral 5:', '#333333', '12px', '');
        $label_CodGradecurso            = new TLabel('Cod. Grade Curso:', '#333333', '12px', '');
        $label_Habilitacao1             = new TLabel('Habilitação 1:', '#333333', '12px', '');
        $label_Habilitacao2             = new TLabel('Habilitação 2:', '#333333', '12px', '');
        $label_Reconhecimento           = new TLabel('Reconhecimento:', '#333333', '12px', '');
        $label_CargaHorariaTotal        = new TLabel('Carga Horaria Total:', '#333333', '12px', '');
        $label_Descricao                = new TLabel('Descrição:', '#333333', '12px', '');
        $label_QtdeEtapas               = new TLabel('Qtde. Etapas:', '#333333', '12px', '');
        $label_CodEntidade              = new TLabel('Entidade:', '#333333', '12px', '');
        $label_Nomehistorico            = new TLabel('Nome Historico:', '#333333', '12px', '');
        $label_dataexphistorico         = new TLabel('Data Exp. Histórico:', '#333333', '12px', '');
        $label_SituacaoEnade            = new TLabel('Situação ENADE:', '#333333', '12px', '');
        $label_CodCurso                 = new TLabel('Cod. Curso:', '#333333', '12px', '');
        $label_NomeFantasia             = new TLabel('Nome :', '#333333', '12px', '');
        $label_HISTORICO_CAB1           = new TLabel('Cabeçalho Histórico 1:', '#333333', '12px', '');
        $label_HISTORICO_CAB2           = new TLabel('Cabeçalho Histórico 2:', '#333333', '12px', '');
        $label_HISTORICO_CAB3           = new TLabel('Cabeçalho Histórico 3:', '#333333', '12px', '');
        $label_HISTORICO_CAB4           = new TLabel('Cabeçalho Histórico 4:', '#333333', '12px', '');
        $label_NomeCoordenador          = new TLabel('Nome Coordenador:', '#333333', '12px', '');
        $label_HabilitacaoProf2         = new TLabel('Habilitação:', '#333333', '12px', '');
        $label_SECRETARIO_DADOS1        = new TLabel('Secretario Dados1:', '#333333', '12px', '');
        $label_SECRETARIO_DADOS2        = new TLabel('Secretario Dados2:', '#333333', '12px', '');
        $label_SECRETARIO_DADOS3        = new TLabel('Secretario Dados3:', '#333333', '12px', '');
        $label_DIRETOR_DADOS1           = new TLabel('Diretor Dados1:', '#333333', '12px', '');
        $label_DIRETOR_DADOS2           = new TLabel('Diretor Dados2:', '#333333', '12px', '');
        $label_DIRETOR_DADOS3           = new TLabel('Diretor Dados3:', '#333333', '12px', '');

        $text_codhistorico              = new TTextDisplay($master_object->codhistorico, '#333333', '12px', '');
        $text_Codaluno                  = new TTextDisplay($master_object->Codaluno, '#333333', '12px', '');
        $text_Nome                      = new TTextDisplay($master_object->Nome, '#333333', '12px', '');
        $text_NomeIdentificacaoCivil    = new TTextDisplay($master_object->NomeIdentificacaoCivil, '#333333', '12px', '');
        $text_Datanascimento            = new TTextDisplay($master_object->Datanascimento, '#333333', '12px', '');
        $text_Naturalidade              = new TTextDisplay($master_object->Naturalidade, '#333333', '12px', '');
        $text_NaturalidadeUF            = new TTextDisplay($master_object->NaturalidadeUF, '#333333', '12px', '');
        $text_Nacionalidade             = new TTextDisplay($master_object->Nacionalidade, '#333333', '12px', '');
        $text_Rg                        = new TTextDisplay($master_object->Rg, '#333333', '12px', '');
        $text_RgOrgaoExpedidor          = new TTextDisplay($master_object->RgOrgaoExpedidor, '#333333', '12px', '');
        $text_CPF                       = new TTextDisplay($master_object->CPF, '#333333', '12px', '');
        $text_EscolaEnsinoMedio         = new TTextDisplay($master_object->EscolaEnsinoMedio, '#333333', '12px', '');
        $text_EscolaEnsinoMedioLocal    = new TTextDisplay($master_object->EscolaEnsinoMedioLocal, '#333333', '12px', '');
        $text_VestibularAno             = new TTextDisplay($master_object->VestibularAno, '#333333', '12px', '');
        $text_TipoIngresso              = new TTextDisplay($master_object->TipoIngresso, '#333333', '12px', '');
        $text_DataIngresso              = new TTextDisplay($master_object->DataIngresso, '#333333', '12px', '');         
        //$text_formaingressohistorico1  = new TTextDisplay($formaingressohistorico1, '#333333', '12px', '');
        //$text_formaingressohistorico2  = new TTextDisplay($formaingressohistorico2, '#333333', '12px', '');  
        $text_DataConclusaoCurso        = new TTextDisplay($master_object->DataConclusaoCurso, '#333333', '12px', '');
        $text_DataColacaoGrau           = new TTextDisplay($master_object->DataColacaoGrau, '#333333', '12px', '');
        $text_DataExpedicaoDiploma      = new TTextDisplay($master_object->DataExpedicaoDiploma, '#333333', '12px', '');
        $text_DataVestibular            = new TTextDisplay($master_object->DataVestibular, '#333333', '12px', '');
        $text_DataVestibExt             = new TTextDisplay($master_object->DataVestibExt, '#333333', '12px', '');
        $text_DataConclEMExt            = new TTextDisplay($master_object->DataConclEMExt, '#333333', '12px', '');
        $text_ObservacaoFinais1         = new TTextDisplay($master_object->ObservacaoFinais1, '#333333', '12px', '');
        $text_ObservacaoFinais2         = new TTextDisplay($master_object->ObservacaoFinais2, '#333333', '12px', '');
        $text_ObservacaoFinais3         = new TTextDisplay($master_object->ObservacaoFinais3, '#333333', '12px', '');
        $text_ObservacaoFinais4         = new TTextDisplay($master_object->ObservacaoFinais4, '#333333', '12px', '');
        $text_ObservacaoFinais5         = new TTextDisplay($master_object->ObservacaoFinais5, '#333333', '12px', '');
        $text_ObservacaoCadastral1      = new TTextDisplay($master_object->ObservacaoCadastral1, '#333333', '12px', '');
        $text_ObservacaoCadastral2      = new TTextDisplay($master_object->ObservacaoCadastral2, '#333333', '12px', '');
        $text_ObservacaoCadastral3      = new TTextDisplay($master_object->ObservacaoCadastral3, '#333333', '12px', '');
        $text_ObservacaoCadastral4      = new TTextDisplay($master_object->ObservacaoCadastral4, '#333333', '12px', '');
        $text_ObservacaoCadastral5      = new TTextDisplay($master_object->ObservacaoCadastral5, '#333333', '12px', '');
        $text_CodGradecurso             = new TTextDisplay($master_object->CodGradecurso, '#333333', '12px', '');
        $text_Habilitacao1              = new TTextDisplay($master_object->Habilitacao1, '#333333', '12px', '');
        $text_Habilitacao2              = new TTextDisplay($master_object->Habilitacao2, '#333333', '12px', '');
        $text_Reconhecimento            = new TTextDisplay($master_object->Reconhecimento, '#333333', '12px', '');
        $text_CargaHorariaTotal         = new TTextDisplay($master_object->CargaHorariaTotal, '#333333', '12px', '');
        $text_Descricao                 = new TTextDisplay($master_object->Descricao, '#333333', '12px', '');
        $text_QtdeEtapas                = new TTextDisplay($master_object->QtdeEtapas, '#333333', '12px', '');
        $text_CodEntidade               = new TTextDisplay($master_object->CodEntidade, '#333333', '12px', '');
        $text_Nomehistorico             = new TTextDisplay($master_object->Nomehistorico, '#333333', '12px', '');
        $text_dataexphistorico          = new TTextDisplay($master_object->dataexphistorico, '#333333', '12px', '');
        $text_SituacaoEnade             = new TTextDisplay($master_object->SituacaoEnade, '#333333', '12px', '');
        $text_CodCurso                  = new TTextDisplay($master_object->CodCurso, '#333333', '12px', '');
        $text_NomeFantasia              = new TTextDisplay($master_object->NomeFantasia, '#333333', '12px', '');
        $text_HISTORICO_CAB1            = new TTextDisplay($master_object->HISTORICO_CAB1, '#333333', '12px', '');
        $text_HISTORICO_CAB2            = new TTextDisplay($master_object->HISTORICO_CAB2, '#333333', '12px', '');
        $text_HISTORICO_CAB3            = new TTextDisplay($master_object->HISTORICO_CAB3, '#333333', '12px', '');
        $text_HISTORICO_CAB4            = new TTextDisplay($master_object->HISTORICO_CAB4, '#333333', '12px', '');
        $text_NomeCoordenador           = new TTextDisplay($master_object->NomeCoordenador, '#333333', '12px', '');
        $text_HabilitacaoProf2          = new TTextDisplay($master_object->HabilitacaoProf2, '#333333', '12px', '');
        $text_SECRETARIO_DADOS1         = new TTextDisplay($master_object->SECRETARIO_DADOS1, '#333333', '12px', '');
        $text_SECRETARIO_DADOS2         = new TTextDisplay($master_object->SECRETARIO_DADOS2, '#333333', '12px', '');
        $text_SECRETARIO_DADOS3         = new TTextDisplay($master_object->SECRETARIO_DADOS3, '#333333', '12px', '');
        $text_DIRETOR_DADOS1            = new TTextDisplay($master_object->DIRETOR_DADOS1, '#333333', '12px', '');
        $text_DIRETOR_DADOS2            = new TTextDisplay($master_object->DIRETOR_DADOS2, '#333333', '12px', '');
        $text_DIRETOR_DADOS3            = new TTextDisplay($master_object->DIRETOR_DADOS3, '#333333', '12px', '');

        $this->form->addFields([$label_codhistorico],[$text_codhistorico]);
        $this->form->addFields([$label_Codaluno],[$text_Codaluno]);
        $this->form->addFields([$label_Nome],[$text_Nome]);
        $this->form->addFields([$label_NomeIdentificacaoCivil],[$text_NomeIdentificacaoCivil]);
        $this->form->addFields([$label_Datanascimento],[$text_Datanascimento]);
        $this->form->addFields([$label_Naturalidade],[$text_Naturalidade]);
        $this->form->addFields([$label_NaturalidadeUF],[$text_NaturalidadeUF]);
        $this->form->addFields([$label_Nacionalidade],[$text_Nacionalidade]);
        $this->form->addFields([$label_Rg],[$text_Rg]);
        $this->form->addFields([$label_RgOrgaoExpedidor],[$text_RgOrgaoExpedidor]);
        $this->form->addFields([$label_CPF],[$text_CPF]);
        $this->form->addFields([$label_EscolaEnsinoMedio],[$text_EscolaEnsinoMedio]);
        $this->form->addFields([$label_EscolaEnsinoMedioLocal],[$text_EscolaEnsinoMedioLocal]);
        $this->form->addFields([$label_VestibularAno],[$text_VestibularAno]);
        
        $this->form->addFields([$label_DataConclusaoCurso],[$text_DataConclusaoCurso]);
        $this->form->addFields([$label_DataColacaoGrau],[$text_DataColacaoGrau]);
        $this->form->addFields([$label_DataExpedicaoDiploma],[$text_DataExpedicaoDiploma]);
        $this->form->addFields([$label_DataVestibular],[$text_DataVestibular]);
        $this->form->addFields([$label_DataVestibExt],[$text_DataVestibExt]);
        $this->form->addFields([$label_DataConclEMExt],[$text_DataConclEMExt]);
        $this->form->addFields([$label_ObservacaoFinais1],[$text_ObservacaoFinais1]);
        $this->form->addFields([$label_ObservacaoFinais2],[$text_ObservacaoFinais2]);
        $this->form->addFields([$label_ObservacaoFinais3],[$text_ObservacaoFinais3]);
        $this->form->addFields([$label_ObservacaoFinais4],[$text_ObservacaoFinais4]);
        $this->form->addFields([$label_ObservacaoFinais5],[$text_ObservacaoFinais5]);
        $this->form->addFields([$label_ObservacaoCadastral1],[$text_ObservacaoCadastral1]);
        $this->form->addFields([$label_ObservacaoCadastral2],[$text_ObservacaoCadastral2]);
        $this->form->addFields([$label_ObservacaoCadastral3],[$text_ObservacaoCadastral3]);
        $this->form->addFields([$label_ObservacaoCadastral4],[$text_ObservacaoCadastral4]);
        $this->form->addFields([$label_ObservacaoCadastral5],[$text_ObservacaoCadastral5]);
        $this->form->addFields([$label_CodGradecurso],[$text_CodGradecurso]);
        $this->form->addFields([$label_Habilitacao1],[$text_Habilitacao1]);
        $this->form->addFields([$label_Habilitacao2],[$text_Habilitacao2]);
        $this->form->addFields([$label_Reconhecimento],[$text_Reconhecimento]);
        $this->form->addFields([$label_CargaHorariaTotal],[$text_CargaHorariaTotal]);
        $this->form->addFields([$label_Descricao],[$text_Descricao]);
        $this->form->addFields([$label_QtdeEtapas],[$text_QtdeEtapas]);
        $this->form->addFields([$label_CodEntidade],[$text_CodEntidade]);
        $this->form->addFields([$label_Nomehistorico],[$text_Nomehistorico]);
        $this->form->addFields([$label_dataexphistorico],[$text_dataexphistorico]);
        $this->form->addFields([$label_SituacaoEnade],[$text_SituacaoEnade]);
        $this->form->addFields([$label_CodCurso],[$text_CodCurso]);
        $this->form->addFields([$label_NomeFantasia],[$text_NomeFantasia]);
        $this->form->addFields([$label_HISTORICO_CAB1],[$text_HISTORICO_CAB1]);
        $this->form->addFields([$label_HISTORICO_CAB2],[$text_HISTORICO_CAB2]);
        $this->form->addFields([$label_HISTORICO_CAB3],[$text_HISTORICO_CAB3]);
        $this->form->addFields([$label_HISTORICO_CAB4],[$text_HISTORICO_CAB4]);
        $this->form->addFields([$label_NomeCoordenador],[$text_NomeCoordenador]);
        $this->form->addFields([$label_HabilitacaoProf2],[$text_HabilitacaoProf2]);
        $this->form->addFields([$label_SECRETARIO_DADOS1],[$text_SECRETARIO_DADOS1]);
        $this->form->addFields([$label_SECRETARIO_DADOS2],[$text_SECRETARIO_DADOS2]);
        $this->form->addFields([$label_SECRETARIO_DADOS3],[$text_SECRETARIO_DADOS3]);
        $this->form->addFields([$label_DIRETOR_DADOS1],[$text_DIRETOR_DADOS1]);
        $this->form->addFields([$label_DIRETOR_DADOS2],[$text_DIRETOR_DADOS2]);
        $this->form->addFields([$label_DIRETOR_DADOS3],[$text_DIRETOR_DADOS3]);
        
       
        
        $this->detail_list = new TQuickGrid;
        $this->detail_list->style = 'width:100%';
        $this->detail_list->disableDefaultClick();
        
        //$this->detail_list->addQuickColumn('Codhistorico', 'codhistorico', 'left');
        //$this->detail_list->addQuickColumn('Codcurso', 'CodCurso', 'left');
        //$this->detail_list->addQuickColumn('Nomecurso', 'NomeCurso', 'left');
        $this->detail_list->addQuickColumn('Etapa', 'Etapa', 'left');
        $this->detail_list->addQuickColumn('Ano', 'Ano', 'left');
        $this->detail_list->addQuickColumn('Sem', 'Sem', 'left');
        $this->detail_list->addQuickColumn('Disciplina', 'NomeDisciplina', 'left');
        $this->detail_list->addQuickColumn('Prof (M)', 'NomeProf', 'left');
        $this->detail_list->addQuickColumn('Titulo (M)', 'TituloProf', 'left');
        $this->detail_list->addQuickColumn('Nome', 'nome', 'left');
        $this->detail_list->addQuickColumn('Habilitacao', 'HabilitacaoProf3', 'left');
        $this->detail_list->addQuickColumn('Nota Final', 'NotaFinal', 'left');
        $this->detail_list->addQuickColumn('Ch', 'CHParcial', 'left');
        $this->detail_list->addQuickColumn('Sit', 'Sit', 'left');
        $this->detail_list->addQuickColumn('Prefixodisciplina', 'PrefixoDisciplina', 'left');
        $this->detail_list->addQuickColumn('Sufixodisciplina', 'SufixoDisciplina', 'left');
        $this->detail_list->addQuickColumn('Edita', 'Edita', 'left');
        $this->detail_list->addQuickColumn('Notafinalbck', 'notafinalbck', 'left');
        

        /*
        $column_totalCH = $this->detail_list->addQuickColumn('TotalCH', '=( {CHParcial} )', 'left');
        
        $column_totalCH->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        */


        $this->detail_list->createModel();

        
        
        $criteria1 = new TCriteria;
        //$criteria1->add(new TFilter('Codentidade', '=', $master_object->codhistorico));
        $criteria1->add(new TFilter('Codaluno', '=', $master_object->Codaluno));
        $criteria1->add(new TFilter('CodCurso', '=', $master_object->CodCurso));  
        $criteria1->setProperty('order', 'Etapa');
        $criteria1->setProperty('direction','ASC');
        //echo $criteria1->dump();
        
        $items = VwHistoricodisciplina::getObjects($criteria1);

        
        $this->detail_list->addItems($items);
        
        $panel = new TPanelGroup('Itens', '#f5f5f5');
        $panel->add(new BootstrapDatagridWrapper($this->detail_list));
        
        $this->form->addContent([$panel]);
        
        $this->form->addHeaderAction('Voltar', new TAction(['DadoshistoricoalunoList', 'onReload']), 'fa:arrow-left blue');
        $this->form->addHeaderAction('Imprimir', new TAction(['HistoricoFinalFormView', 'onPrint'],['key'=>$master_object->codhistorico]), 'far:file-pdf red');
        

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%'; 
        // $container->add(new TXMLBreadCrumb('menu.xml', 'VwDadoshistoricoalunoList'));
        $container->add($this->form);

        TTransaction::close();

        parent::add($container);


    }
        public function onPrint($param)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $object = VwDadoshistoricoaluno::find($param['key']);

            $entidade = $object->CodEntidade;
                        
            if ($object)
            {
                $object->Datanascimento = TDate::date2br($object->Datanascimento);
                $object->DataIngresso = TDate::date2br($object->DataIngresso);
                $object->DataConclusaoCurso = TDate::date2br($object->DataConclusaoCurso);
                $object->DataColacaoGrau = TDate::date2br($object->DataColacaoGrau);
                $object->DataExpedicaoDiploma = TDate::date2br($object->DataExpedicaoDiploma);

             
                $criteria = new TCriteria;
                $criteria->add(new TFilter('Codaluno', '=', $object->Codaluno));
                $criteria->add(new TFilter('CodCurso', '=', $object->CodCurso));
                //$criteria->add(new TFilter('CodEntidade', '=', $object->CodEntidade));
                //$criteria->add(new TFilter('codhistorico', '=', $object->codhistorico));
                $criteria->setProperty('order', 'Etapa');
				$criteria->setProperties('direction','ASC');
                //echo $criteria->dump();
                //exit;a

                $items = VwHistoricodisciplina::getObjects($criteria);
                $linha = 0;
                
                
                foreach ($items as $item){ 

                    if ($item->Etapa % 2 == 0){
                        if($items[$Etapa] !=$results[$Etapa-1])
                        echo $item->Etapa;
                   // die();
                        $item->Etapa = '<span style="font-weight: bold">'. $item->Etapa . '</span>';
                        //echo $item->Etapa.'<br/>';
                        //die();

                    }
                    $linha = $item->Etapa;
                    $chtotal = $CHTotal;
                }


                $object->Codaluno = $aluno[0]->Codaluno;

                //var_dump($items);
                //die();
             
                if ($entidade == 3) {
                    
                    $html = new AdiantiHTMLDocumentParser('app/documents/vwdadoshistoricoalunoformviewfafram.html', 'A4', 'portrait');
                    $html->setMaster($object);
                    $html->setDetail('VwHistoricodisciplina', $items);
                    $html->process();
                    $output = $html->getContents();       
                }
                 
                if ($entidade == 2) {
                   
                    $html = new AdiantiHTMLDocumentParser('app/documents/vwdadoshistoricoalunoformview.html', 'A4', 'portrait');
                    $html->setMaster($object);
                    $html->setDetail('VwHistoricodisciplina', $items);
                    $html->process();
                    $output = $html->getContents();    
                }
    
                if ($entidade == 6) {
                   
                    $html = new AdiantiHTMLDocumentParser('app/documents/vwdadoshistoricoalunoformview.html', 'A4', 'portrait');
                    $html->setMaster($object);
                    $html->setDetail('VwHistoricodisciplina', $items);
                    $html->process();
                    $output = $html->getContents();    
                }


                if ($entidade == 10) {
                   
                    $html = new AdiantiHTMLDocumentParser('app/documents/vwdadoshistoricoalunoformview.html', 'A4', 'portrait');
                    $html->setMaster($object);
                    $html->setDetail('VwHistoricodisciplina', $items);
                    $html->process();
                    $output = $html->getContents();
                }
                         
                $document = 'tmp/'.uniqid().'.pdf'; 
                $html = AdiantiHTMLDocumentParser::newFromString($output);
                $html->saveAsPDF($document);
                
                //parent::openFile($document);
    
                $window = TWindow::create('Histórico Escolar', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = 'download.php?file='.$document;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";
                $window->add($object);
                $window->show();
                /*// converts the HTML template into PDF
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($output);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                    
                // write and open file
                file_put_contents('app/output/document.pdf', $dompdf->output());
                    
                // open window to show pdf
                $window = TWindow::create(('Histórico Escolar'), 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = 'app/output/document.pdf';
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";
                $window->add($object);
                $window->show();*/
            }
            
            //new TMessage('info', 'Documento PDF gerado com sucesso. Caso não tenha conseguido visualizá-lo, habilite pop-ups em seu navegador e tente gerá-lo novamente.');
            
            
            TTransaction::close();    
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    public function mostrar()
    {
        
    }
    
}
