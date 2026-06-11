<?php
/**
 * Tabular report View
 * @author     Pamella Scapim

 */
class RelatorioTickets extends TPage
{
    private $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Customer_report');
        $this->form->setFormTitle( 'Relatório e-Tickets');
        
        // create the form fields
        $status_ticket  = new TCombo('status_ticket');
        $data_ticket    = new TDate('data_ticket');
        $output_type    = new TRadioGroup('output_type');

        $combo_items = array();
        $combo_items['A'] ='Aberto';
        $combo_items['F'] ='Finalizado';
        $combo_items['E'] ='Em Progresso';
        
        $this->form->addFields( [new TLabel('Status e-Ticket:')],     [$status_ticket] );
        $this->form->addFields( [new TLabel('Data:')], [$data_ticket] );
        $this->form->addFields( [new TLabel('Tipo Relatório:')],   [$output_type] );
        
        // define field properties
        $status_ticket->setSize( 150 );
        $status_ticket->addItems($combo_items);
       // $category_id->setSize( '80%' );
        $output_type->setUseButton();
       
        $options = ['pdf' =>'PDF'];
        $output_type->addItems($options);
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');
        $data_ticket->setSize(150);
        $data_ticket->setMask('dd/mm/yyyy');  // Define a máscara para entrada
        $data_ticket->setDatabaseMask('yyyy-mm-dd'); // Define o formato para o banco de dados

        
        $this->form->addAction( 'Gerar Relatório', new TAction(array($this, 'onGenerate')), 'fa:download blue');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        
        parent::add($vbox);
    }

    /**
     * method onGenerate()
     * Executed whenever the user clicks at the generate button
     */
    function onGenerate()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('Felabs_DB');

            $loggedUnit = TSession::getValue('userunitid');

            // get the form data into
            $data = $this->form->getData();

            $repository = new TRepository('Ticket');
            $criteria   = new TCriteria;
            $criteria->add( new TFilter('departamento', '=', $loggedUnit));
            
            if ($data->status_ticket)
            {
                $criteria->add(new TFilter('status', 'like', "%{$data->status_ticket}%"));
                
            }
            if ($data->data_ticket)
            {
                $criteria->add(new TFilter("CONVERT(DATE, data_reg)", "=", $data->data_ticket));                
            }


            if (empty($param['order']))
            {
                $param['order'] = 'data_reg';
                $param['direction'] = 'desc';
            }
            
            $criteria->setProperties($param); 
           
            $customers = $repository->load($criteria);

            foreach ($customers as $categoria)
            {
                $categoria->categoria = $categoria->ticket_categoria->nome; // Pega o nome da categoria

            }
            $format  = $data->output_type;

            // var_dump($customers);
            // die();


            
            if ($customers)
            {
                $widths = array(40, 300, 40, 200);
                
                switch ($format)
                {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }
                
                if (!empty($table))
                {
                    // create the document styles
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#ffffff', '#4B5D8E');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                    $table->addStyle('datap',  'Helvetica', '10', '',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#2B2B2B', '#B4CAFF');
                    
                    $table->setHeaderCallback( function($table) {
                        $table->addRow();
                        $table->addCell('Relatório de e_Tickets', 'center', 'header', 4);
                        
                        $table->addRow();
                        $table->addCell('ID',       'center', 'title');
                        $table->addCell('Descrição','left',   'title');
                        $table->addCell('Status',   'left',   'title');
                        $table->addCell('Criado em','center', 'title');
                        
                    });
                    
                    $table->setFooterCallback( function($table) {
                        $table->addRow();
                        $table->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 6);
                    });
                    
                    // controls the background filling
                    $colour= FALSE;
                    
                    // data rows
                    foreach ($customers as $customer)
                    {
                        $style = $colour ? 'datap' : 'datai';
                        $table->addRow();
                        $table->addCell($customer->id,        'center', $style);
                        $table->addCell($customer->categoria, 'left', $style);
                        $table->addCell($customer->status,    'left',   $style);
                        $table->addCell($customer->data_reg,  'center', $style);
                                                
                        $colour = !$colour;
                    }
                    
                    $output = "app/output/tabular.{$format}";
                    
                    // stores the file
                    if (!file_exists($output) OR is_writable($output))
                    {
                        $table->save($output);
                        parent::openFile($output);
                    }
                    else
                    {
                        throw new Exception(_t('Permission denied') . ': ' . $output);
                    }
                    
                    // shows the success message
                    new TMessage('info', 'Report generated. Please, enable popups in the browser.');
                }
            }
            else
            {
                new TMessage('error', 'No records found');
            }
    
            // fill the form with the active record data
            $this->form->setData($data);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
