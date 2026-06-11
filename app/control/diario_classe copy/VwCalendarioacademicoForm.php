<?php
/**
 * VwCalendarioacademicoForm Form
 * @author  <your name here>
 */
class VwCalendarioacademicoForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        try
        {
            TTransaction::open('dados_fei');
            
            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');
            $CodCurso       = $sessao_diarioclasse["CodCurso"];
            $NomeDisciplina = $sessao_diarioclasse["NomeDisciplina"];
            $Turno          = $sessao_diarioclasse["Periodo"];
            $Etapa          = $sessao_diarioclasse["Etapa"];
            $hoje           = date('Y-m-d');
    
             // Conversão do turno
            switch ($Turno) 
            {
                case 'I':
                    $TurnoCompleto = 'Integral';
                    break;
                case 'M':
                    $TurnoCompleto = 'Manhã';
                    break;
                case 'N':
                    $TurnoCompleto = 'Noturno';
                    break;
                default:
                    $TurnoCompleto = 'Turno inválido';
            }
    
            // Cria o critério
            $criteria = new TCriteria();
            $criteria->add(new TFilter('CodCurso', '=', $CodCurso), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Ano', '=', '2026'), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Letivo', '=', 'S'), TExpression::AND_OPERATOR);
            $criteria->add(new TFilter('Data', '<=', $hoje), TExpression::AND_OPERATOR);

            $repository = new TRepository('VwCalendarioacademico');
            $calendario = $repository->load($criteria);
    
            $options = [];
    
            // Iterar sobre os registros e formatar as datas
            if ($calendario) 
            {
                foreach ($calendario as $registro) 
                {
                    // Converte a data de Y-m-d para d/m/Y
                    $data_formatada = (new DateTime($registro->Data))->format('d/m/Y');
                    $options[$registro->Data] = $data_formatada;
                }
            }
            
            TTransaction::close();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }    
        
   
        // creates the form
        $this->form = new BootstrapFormBuilder('form_VwCalendarioacademico');
        $this->form->setFormTitle('Diário de Classe Online - Data para Lançamento');

        $Data = new TDBCombo('Data', 'dados_fei', 'VwCalendarioacademico', 'Data', 'Data', 'Data', $criteria);
        // Recuperar os valores diretamente da tabela
        krsort($options);
        $Data->addItems($options);


        // add the fields
        $this->form->addFields( [ new TLabel('Disciplina:') ], [ '<b>'.$NomeDisciplina ] );
        $this->form->addFields( [ new TLabel('Turno:') ], [ $TurnoCompleto ] );
        $this->form->addFields( [ new TLabel('Turma:') ], [ $Etapa .'º Ciclo' ] );
        $this->form->addFields( [ new TLabel('Data de Lançamento:') ], [ $Data ] );

        // set sizes
        $Data->setSize('30%');
         
        // create the form actions
        
        $btn = $this->form->addAction(('Lançar Frequências'), new TAction([$this, 'onSave']), 'fa:check');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink( 'Listar Disciplinas',  new TAction(['HorarioAulasList', 'onReload']), 'fa:reply blue');
                
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('dados_fei'); // open a transaction

            $sessao_diarioclasse = TSession::getValue('sessao_diarioclasse');

            $CodTurmaetapa   = $sessao_diarioclasse["CodTurmaetapa"];
            // $Codprofessor   = $sessao_diarioclasse["Codprofessor"];
            // $Ano            = $sessao_diarioclasse["Ano"];
            // $Periodo        = $sessao_diarioclasse["Periodo"];
            // $CodDisciplina  = $sessao_diarioclasse["CodDisciplina"];
            // $CodCurso       = $sessao_diarioclasse["CodCurso"];            
            // $Etapa          = $sessao_diarioclasse["Etapa"];
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new VwCalendarioacademico;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data

            $DiaAula = $data->Data;

            $frqdiarias = FiFrqdiaria::where('CodTurmaetapa', '=', $CodTurmaetapa)
                                    ->where('Data',   '=', $DiaAula)
                                    ->load();

            if(empty($frqdiarias))
            {
                $Frqdiaria = new FiFrqdiaria;

                $Frqdiaria->CodTurmaetapa   = $CodTurmaetapa;
                $Frqdiaria->Data            = $DiaAula;
                $Frqdiaria->CodOperador     = '';
                $Frqdiaria->DataLancamento  = '';
                $Frqdiaria->HoraLancamento  = '';

                $Frqdiaria->store();
            }

            // $diasemana_numero = date('w', strtotime('+1 day', strtotime($DiaAula)));

            // $verifica_aulas = VwHorarioprofessor::where('Codprofessor', '=', $Codprofessor)
            //                                     ->where('Ano','=', $Ano)
            //                                     ->where('Periodo','=', $Periodo)
            //                                     ->where('CodDisciplina','=', $CodDisciplina)
            //                                     ->where('CodCurso','=', $CodCurso)
            //                                     ->where('Etapa','=', $Etapa)
            //                                     ->where('DiaSemana','=', $diasemana_numero)
            //                                     ->load();
            
                 
                //TApplication::loadPage('ControleFrequencia');
                $selected_value = $this->form->getField('Data')->getValue();
                TSession::setValue('data_escolhida', array('data_escolhida' => $selected_value));

                AdiantiCoreApplication::gotoPage('ControleFrequencia');


            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
           
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    


  
}
