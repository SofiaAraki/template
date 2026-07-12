<?php
/**
 * @author     Pamella Scapim
 */
class LinksCPAFFCL extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
                       
       try 
       {
        TTransaction::open('Felabs_DB');
        $logged  = SystemUser::newFromLogin(TSession::getValue('login'));
        $loggedUnit = TSession::getValue('userunitid');

        TTransaction::open('dados_fei');
        $criteria_cpa_ffcl = new TCriteria;                        
        $criteria_cpa_ffcl->add(new TFilter('Codaluno', '=', $logged->systemuser_codlegado));            
        $criteria_cpa_ffcl->add(new TFilter('AnoMatricula', '=', 2022)); 
        $criteria_cpa_ffcl->add(new TFilter('SemestreMatricula', '=', 1)); 
        $criteria_cpa_ffcl->add(new TFilter('CodEntidade', '=', 2)); 

        $alunos_ffcl = new TRepository('VwAlunoMatriculaEtapa');
        $alunoSemestre = $alunos_ffcl->load($criteria_cpa_ffcl);

        foreach($alunoSemestre as $alunoCurso)
        {
            $CodCurso           = $alunoCurso->CodCurso;
            $EtapaMatricula     = $alunoCurso->EtapaMatricula;
            $Periodo            = $alunoCurso->Periodo;

            //var_dump($CodCurso);
           
            if ($CodCurso == 62) //Ciências Contábeis
            {
                switch ($EtapaMatricula) {
                    case 1:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/88FSX8WBcn6sWdp77";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 2:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/FQDKbMHwJgW6Vtdh8";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 3:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/fkNhU6zTLRCYNVED7";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 5:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/ewbzTFxHaXCM77CJ6";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 7:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/skeavKeN85M78XS2A";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;                    
                }
        } 

        else if ($CodCurso == 10) //Administração
        {
            switch ($EtapaMatricula)
            {
                case 1:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/kf9SJKjcnwB3oEBB6";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 2:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/aEKgPE5LFuZNG3Nd8";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    
                    break;
                case 3:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/fsASnENpdi1PnUe78";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 5:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/x4roy1z2maTE95V48";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    
                    break;
                case 7:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/87o9r5TgUo1NYpZm9";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                
            }
        }

        else if ($CodCurso == 6) //Pedagogia Presencial
        {
            switch ($EtapaMatricula)
            {
                case 5:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/n4KYVdifr2BdpaTYA";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 7:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/7bbpdhtu4nuSNzdy6";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
            }
        }

        else if ($CodCurso == 69) //Engenharia Civil
        {
            switch ($EtapaMatricula)
            {   
                case 1:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/tPBYGcz4yJAgwrPi9";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 2:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/B5zJuhDKwYS9xZrb9";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 3:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/EuCjo4S8soQncmhV9";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 5:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/NJgH2yd9AGokeG8n8";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";                    
                    break;
                case 7:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/K77EG8SVAnyLWY4Z8";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 9:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/ZgAicDsL9mjkW3io8";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;                
            }
        }

        else if ($CodCurso == 68) //Engenharia de Produção
        {
            switch ($EtapaMatricula)
            {
                case 1:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/pNnpJan3XR4a8oPo7";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 2:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/GSsEWD1vhGDhYt8F7";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 3:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/TkWGLuwHSuM4m35s7";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 5:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/VzXGRkTZfhiHTspH6";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";                    
                    break;
                case 7:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/z6mdfcJNVEAxgmSx5";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 9:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/3UbXmUNgV5vhq6BL8";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break; 
            }
        }

        else if ($CodCurso == 67) //Engenharia Mecânica
            {
                switch ($EtapaMatricula)
                {
                    case 1:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/Yj2jsRk8wcttVs7d7";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 2:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/A9812k1oafY2suHr5";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 3:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/ARhTw7WzoR3MHoYD9";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 5:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/2jEa5QU4fTexCBm7A";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";                    
                        break;
                    case 7:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/msoCsTKvXmrWvkss5";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break;
                    case 9:
                        $iframe = new TElement('iframe');
                        $iframe->id = "iframe_external";
                        $iframe->src = "https://forms.gle/5So6SW4nMUqAtyKn7";
                        $iframe->frameborder = "0";
                        $iframe->scrolling = "yes";
                        $iframe->width = "100%";
                        $iframe->height = "700px";
                        break; 
                }
        }

        else if ($CodCurso == 104) //Engenharia Elétrica
        {
            switch ($EtapaMatricula)
            {
                case 1:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/yunAZ5yb87fGiUrx8";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 2:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/RoBt3fTHK16dpEGg8";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 3:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/pgtDxLasWFs5o1zc7";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";
                    break;
                case 5:
                    $iframe = new TElement('iframe');
                    $iframe->id = "iframe_external";
                    $iframe->src = "https://forms.gle/jRADYcC9kCVfUyFaA";
                    $iframe->frameborder = "0";
                    $iframe->scrolling = "yes";
                    $iframe->width = "100%";
                    $iframe->height = "700px";                    
                    break;
                }
            }
        }

        TTransaction::close();
        TTransaction::close();
    }
    catch (Exception $e)
    {
        new TMessage('error', $e->getMessage());            
        TTransaction::rollback();
    }
       parent::add($iframe);  
    }
}
