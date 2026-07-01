<?php
/**
 * VwAlunosnotasList Listing
 * @author  <your name here>
 */
class VwAlunosnotasList extends TPage
{
    private $form; 
    private $datagrid; 
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();

        $sessao_bimestre = TSession::getValue('sessao_bimestre');
        $Bimestre = $sessao_bimestre["Bimestre"] ?? '2';

        // Creates the form
        $this->form = new TQuickForm('form_search_VwAlunosnotas');
        $this->form->class = 'tform'; 
        $this->form = new BootstrapFormWrapper($this->form);
        $this->form->style = 'display: table;width:100%'; 
        $this->form->setFormTitle('VwAlunosnotas');
        
        // Fields
        $Codaluno = new TEntry('Codaluno');
        $Nome = new TEntry('Nome');

        // Add fields
        $this->form->addQuickField('Nome:', $Nome,  '50%' );        
        $this->form->addQuickField('Cód. Aluno:', $Codaluno,  '50%' );

        $this->form->setData( TSession::getValue('VwAlunosnotas_filter_data') );
        
        // Actions do topo
        $this->form->addQuickAction('Voltar',  new TAction(array('VwProfessordisciplinassemestreList','onReload')), 'fas:arrow-left orange');
        $this->form->addQuickAction('Buscar', new TAction(array($this, 'onSearch')), 'fas:search blue');

        // NOVO: Botão de fechamento/confirmação oficial para o Professor
        $this->form->addQuickAction('Finalizar Lançamento', new TAction(array($this, 'onFinalizarLançamento')), 'fas:check green');

        // Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // Columns
        $column_Codaluno = new TDataGridColumn('Codaluno', 'Código', 'center');
        $column_Nome = new TDataGridColumn('Nome', 'Nome', 'left');
        $column_nota1 = new TDataGridColumn('nota1_widget', 'Nota', 'left');
        $column_Resultado = new TDataGridColumn('Resultado', 'Result.', 'center');
        $column_TipoDis = new TDataGridColumn('TipoDis', 'Tipo Disc.', 'center');
        
        // Transformer da Nota - ATUALIZADO PARA EVITAR CONFLICTS DE TRANSACTION DO DRIVER
        $column_nota1->setTransformer( function($value, $object, $row)
        {
            $sessao = TSession::getValue('sessao_papeleta_unificada');
            $CodGradeDisciplinaEtapa_Frente = $sessao["CodGradeDisciplinaEtapa_Frente"] ?? '';
            
            // Abre uma transação apenas se já não houver uma ativa para evitar conflitos
            $fechar_transacao = false;
            if (!TTransaction::get()) {
                TTransaction::open('dados_fei');
                $fechar_transacao = true;
            }
            
            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"] ?? '2';

            $repository = new TRepository('FiNotasfaltasFrente');
            $notas = $repository    ->where('CodMatriculaEtapa',  '=', $object->CodMatriculaEtapa)
                                    ->where('CodDisciplina', '=', $object->CodDisciplina)
                                    ->where('CodGradeDisciplinaEtapa_Frente', '=', $CodGradeDisciplinaEtapa_Frente)
                                    ->where('Avaliacao','=', $Bimestre)
                                    ->load();
            
            $Nota1 = '';
            $id = '';
            foreach ($notas as $nota)
            {
                $Nota1 = $nota->Nota1;
                $id = $nota->ID;
            }

            if ($fechar_transacao) {
                TTransaction::close();
            }

            $widget = new TEntry('Nota1' . '_' . $object->CodDisciplina . '_'.$object->CodMatriculaEtapa.'_'.$object->TipoDis.'_'.$object->CodTurmaetapa.'_'.$CodGradeDisciplinaEtapa_Frente.'_'.$id);
            $widget->setValue($Nota1);
            $widget->setSize(60);
            $widget->setNumericMask(2, '.','.');
            $widget->setFormName('form_search_VwAlunosnotas');
            
            $action = new TAction( [$this, 'onSaveInline'] );
            $action->setParameter('column', 'Nota1');
            $widget->setExitAction( $action );
            
            return $widget;
        });

        // Transformer para legendar e colorir o Resultado
        $column_Resultado->setTransformer( function($value, $object, $row) {
            $value = trim(strtoupper($value ?? ''));
            switch ($value) {
                case 'A': return '<span class="label label-success">Aprovado</span>';
                case 'R': return '<span class="label label-danger">Reprovado</span>';
                case 'E': return '<span class="label label-warning">Exame</span>';
                case 'RF': return '<span class="label label-default">Rep. Falta</span>';
                case '':  return '<span class="label label-info">Pendente</span>';
                default:  return $value;
            }
        });

        // Transformer para legendar e colorir o Tipo Disc.
        $column_TipoDis->setTransformer( function($value, $object, $row) {
            $value = trim(strtoupper($value ?? ''));
            switch ($value) {
                case 'AT': return '<span class="label label-success">Atual</span>';
                case 'DP': return '<span class="label label-danger">Dependencia</span>';
                case 'AD': return '<span class="label label-warning">Adaptado</span>';
                case '':  return '<span class="label label-info">Pendente</span>';
                default:  return $value;
            }
        });

        $this->datagrid->addColumn($column_Codaluno); 
        $this->datagrid->addColumn($column_Nome);
        $this->datagrid->addColumn($column_nota1);
        
        $Unidade = TSession::getValue('userunitid');
        if ($Unidade <> 12)
        {
            $this->datagrid->addColumn($column_Resultado);
            $this->datagrid->addColumn($column_TipoDis);
        }
        
        $this->datagrid->createModel();
        $this->datagrid->disableDefaultClick();
        
        // Container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ApontamentoBimestral'));
        $container->add(TPanelGroup::pack('Buscar Aluno Por:', $this->form));
        
        // ATUALIZADO PARA USAR A SESSÃO UNIFICADA
        $sessao = TSession::getValue('sessao_papeleta_unificada');
        $nomediscipina = $sessao["NomeDisciplina"] ?? '';
        $identificacao = $sessao["Identificacao"] ?? '';

        $container->add(TPanelGroup::pack($nomediscipina . ' - '. $identificacao, $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public static function onSaveInline($param)
    {
        $name                               = $param['_field_name'];
        $value                              = $param['_field_value'];
        $column                             = $param['column'];
        $parts                              = explode('_', $name);
        $id                                 = end($parts);
        $CodDisciplina                      = $parts[1];
        $CodMatriculaEtapa                  = $parts[2];
        $TipoDis                            = $parts[3];
        $CodTurmaetapa                      = $parts[4];
        $CodGradeDisciplinaEtapa_Frente     = $parts[5];

        try
        {
            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"] ?? '2';

            TTransaction::open('dados_fei'); 
            
            // CORREÇÃO: Força o SQL Server a suprimir contagens de registros (Evita o erro IMSSP)
            $conn = TTransaction::get();
            $conn->exec("SET NOCOUNT ON");

            $object = null;
            // Só busca por ID se ele for um valor válido/preenchido numericamente
            if (!empty($id) && is_numeric($id)) {
                $object = FiNotasfaltasFrente::find($id);
            }

            if ($object) 
            { 
                $object->$column = $value;                
                $object->store();               
                TTransaction::close();
            } else {
                $repositoryFrente = new TRepository('FiNotasfaltasFrente');
                $criteriaFrente = new TCriteria;
                $criteriaFrente->add(new TFilter('CodMatriculaEtapa', '=', $CodMatriculaEtapa));
                $criteriaFrente->add(new TFilter('CodDisciplina', '=', $CodDisciplina));
                $criteriaFrente->add(new TFilter('TipoDisciplina', '=', $TipoDis));
                $criteriaFrente->add(new TFilter('Avaliacao', '=', $Bimestre));
                $objectsFrente = $repositoryFrente->load($criteriaFrente);
                
                $ID = NULL;
                if (!empty($objectsFrente)) {
                    $ID = $objectsFrente[0]->ID; 
                }
                
                if ($ID <> NULL){ 
                    $object = FiNotasfaltasFrente::find($ID);                    
                    $object->$column = $value;                
                    $object->store();             
                    TTransaction::close();
                } else {
                    $notasfalta = new FiNotasfaltas;                    
                    $notasfalta->CodDisciplina = $CodDisciplina;
                    $notasfalta->TipoDisciplina =  $TipoDis;
                    $notasfalta->TipoNota = 'N';
                    $notasfalta->CodMatriculaEtapa = $CodMatriculaEtapa;
                    $notasfalta->Avaliacao = $Bimestre;
                    $notasfalta->$column = $value;

                    $notasfaltafrente = new FiNotasfaltasFrente;                    
                    $notasfaltafrente->CodDisciplina = $CodDisciplina;
                    $notasfaltafrente->TipoDisciplina =  $TipoDis;
                    $notasfaltafrente->TipoNota = 'N';
                    $notasfaltafrente->CodMatriculaEtapa = $CodMatriculaEtapa;
                    $notasfaltafrente->Avaliacao = $Bimestre;
                    $notasfaltafrente->$column = $value;
                    $notasfaltafrente->CodGradeDisciplinaEtapa_Frente = $CodGradeDisciplinaEtapa_Frente; 

                    $repositoryNotas = new TRepository('FiNotasfaltas');
                    $criteriaNotas = new TCriteria;
                    $criteriaNotas->add(new TFilter('CodMatriculaEtapa', '=', $CodMatriculaEtapa));
                    $criteriaNotas->add(new TFilter('CodDisciplina', '=', $CodDisciplina));
                    $criteriaNotas->add(new TFilter('TipoDisciplina', '=', $TipoDis));
                    $criteriaNotas->add(new TFilter('Avaliacao', '=', $Bimestre));
                    
                    $existeNota = $repositoryNotas->load($criteriaNotas);

                    if (empty($existeNota)){
                        $notasfalta->store();
                    }
                    $notasfaltafrente->store(); 
                    TTransaction::close();
                }
            }
            
            TToast::show('success', 'Nota salva com sucesso!', 'bottom right', 'far:check-circle');
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', 'ERRO AO SALVAR AUTOMATICAMENTE: ' . $e->getMessage());
        }
    }

    public function onFinalizarLançamento($param)
    {
        try 
        {
            TTransaction::open('dados_fei');
            
            $sessao = TSession::getValue('sessao_papeleta_unificada');
            $coddiscipina = $sessao["CodDisciplina"] ?? '';
            $codturmaetapa = $sessao["CodTurmaetapa"] ?? '';
            
            $repository = new TRepository('VwAlunosnotas');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodDisciplina', '=', $coddiscipina));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $codturmaetapa));
            $criteria->setProperty('limit', 1);
            
            $objects = $repository->load($criteria);
            
            if ($objects) {
                TTransaction::close();
                TApplication::loadPage('VwPapeletaReport');
            } else {
                throw new Exception('Não foi possível gerar a papeleta. Nenhum aluno encontrado nesta turma.');
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        
        TSession::setValue('VwAlunosnotasList_filter_Codaluno',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_Nome',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_CodTurmaetapa',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_CodDisciplina',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_TipoDis',   NULL);
        TSession::setValue('VwAlunosnotasList_filter_Resultado',   NULL);

        if (isset($data->Codaluno) AND ($data->Codaluno)) {
            $filter = new TFilter('Codaluno', 'like', "%{$data->Codaluno}%"); 
            TSession::setValue('VwAlunosnotasList_filter_Codaluno',   $filter); 
        }

        if (isset($data->Nome) AND ($data->Nome)) {
            $filter = new TFilter('Nome', 'like', "%{$data->Nome}%"); 
            TSession::setValue('VwAlunosnotasList_filter_Nome',   $filter); 
        }

        $this->form->setData($data);
        TSession::setValue('VwAlunosnotas_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('dados_fei');
            
            $sessao = TSession::getValue('sessao_papeleta_unificada');
            $coddiscipina = $sessao["CodDisciplina"] ?? '';
            $codturmaetapa = $sessao["CodTurmaetapa"] ?? '';
            
            $sessao_bimestre = TSession::getValue('sessao_bimestre');
            $Bimestre = $sessao_bimestre["Bimestre"] ?? '2';
          
            $repository = new TRepository('VwAlunosnotas');
            $limit = 500;
            
            $criteria = new TCriteria();
            $criteria->add(new TFilter('CodDisciplina', '=', $coddiscipina));
            $criteria->add(new TFilter('CodTurmaEtapa', '=', $codturmaetapa));

            if ($Bimestre == 3){
                $criteria->add(new TFilter('Resultado', '=','E'));
            }
            
            if (empty($param['order']))
            {
                $param['order'] = 'Ordem, Nome';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('VwAlunosnotasList_filter_Codaluno')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Codaluno')); 
            }

            if (TSession::getValue('VwAlunosnotasList_filter_Nome')) {
                $criteria->add(TSession::getValue('VwAlunosnotasList_filter_Nome')); 
            }

            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0) {
                $this->onReload( func_get_arg(0) );
            } else {
                $this->onReload();
            }
        }
        parent::show();
    }
}