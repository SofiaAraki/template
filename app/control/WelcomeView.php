<?php

use template\Widget\TStory;

/**
 * WelcomeView
 * @version    8.5
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 */
class WelcomeView extends TPage
{
    private $fc;

    public function __construct()
    {
        parent::__construct();

        try
        {
            // ==========================================
            // TRANSAÇÃO DE BANCO E VALIDAÇÕES INTEGRADAS
            // ==========================================
            TTransaction::open('Felabs_DB');

            $logged          = SystemUser::newFromLogin(TSession::getValue('login'));
            $loggedUnit      = TSession::getValue('userunitid');
            $system_user_id  = TSession::getValue('userid');
            $hoje            = date('Y-m-d');

            $isProfessor     = ($logged->funcao_legado == 'Professor');

            // BLOQUEIO - ATUALIZAÇÃO CADASTRAL (180 DIAS)
            if ($logged->funcao_legado == 'Aluno')
            {
                TTransaction::close();
                TTransaction::open('dados_fei');

                $aluno = new FiAluno($logged->systemuser_codlegado);
                $limite = '2026-03-17';
                $dataAtualizacao = !empty($aluno->DataAtualizacao) ? date('Y-m-d', strtotime($aluno->DataAtualizacao)) : null;

                if (empty($dataAtualizacao) || $dataAtualizacao < $limite)
                {
                    TTransaction::close();
                    TApplication::loadPage('AtualizaDadosForm');
                    exit;
                }

                TTransaction::close();
                TTransaction::open('Felabs_DB');
            }

            // VERIFICAÇÃO DE COMUNICADOS DE BOLSA IMPEDIENTES
            $criteriaBolsa = new TCriteria;
            $criteriaBolsa->add(new TFilter('data_postagem', '<=', $hoje));
            $criteriaBolsa->add(new TFilter('data_expiracao', '>=', $hoje));
            $criteriaBolsa->add(new TFilter('system_unit_id', '=', $loggedUnit));

            $comunicados = ComunicadoBolsa::getObjects($criteriaBolsa);
            foreach ($comunicados as $comunicado)
            {
                $participante = ComunicadoBolsaParticipante::where('system_user_id', '=', $system_user_id)->where('comunicado_id', '=', $comunicado->id)->load();
                $aceite       = ComunicadoBolsaAceite::where('system_user_id', '=', $system_user_id)->where('comunicado_id', '=', $comunicado->id)->load();

                if ($participante && empty($aceite))
                {
                    TSession::setValue('dados_comunicado', $comunicado);
                    TTransaction::close();
                    TApplication::loadPage('ComunicadoBolsaView');
                    return;
                }
            }

            // BUSCA DE INFORMAÇÕES DE NOTAS APENAS SE FOR PROFESSOR
            $periodoAtivoHtml = '';
            if ($isProfessor) {
                TTransaction::close(); 
                TTransaction::open('dados_fei');
                
                $criteriaApontamento = new TCriteria;
                $criteriaApontamento->add(new TFilter('CodEntidade', '=', $loggedUnit));
                $criteriaApontamento->add(new TFilter('DataInicio', '<=', $hoje));
                $criteriaApontamento->add(new TFilter('DataFim', '>=', $hoje));
                
                $prazosAbertos = FiDataapontamentobimestral::getObjects($criteriaApontamento);
                if (!empty($prazosAbertos)) {
                    $periodoAtivoHtml .= '<div style="margin-top:15px; text-align:left;">';
                    $periodoAtivoHtml .= '  <span class="text-muted" style="font-size:12px; font-weight:bold; display:block; margin-bottom:5px;"><i class="fa fa-clock-o text-warning"></i> PRAZOS ATIVOS DE LANÇAMENTO:</span>';
                    foreach ($prazosAbertos as $prazo) {
                        $dataFimBr = TDate::date2br($prazo->DataFim);
                        $nomeAvaliacao = !empty($prazo->avaliacao_bimestre_colegio) ? $prazo->avaliacao_bimestre_colegio : "{$prazo->Bimestre}º Bimestre";
                        $periodoAtivoHtml .= "  <div class='alert alert-warning' style='padding:8px; font-size:12px; margin-bottom:6px; border-left:4px solid #f39c12;'>
                                                    <strong>{$nomeAvaliacao} ({$prazo->Ano})</strong><br>Disponível até: <span class='label label-danger'>{$dataFimBr}</span>
                                                </div>";
                    }
                    $periodoAtivoHtml .= '</div>';
                } else {
                    $periodoAtivoHtml = '<div class="alert alert-info" style="margin-top:15px; font-size:12px; text-align:left; border-left:4px solid #3498db;"><i class="fa fa-info-circle"></i> Nenhum período de digitação aberto hoje.</div>';
                }

                TTransaction::close();
                TTransaction::open('Felabs_DB');
            }

            // ==========================================
            // CONSTRUÇÃO DA INTERFACE GRÁFICA (LAYOUT)
            // ==========================================
            $container = new TElement('div');
            $container->class = 'container-fluid';

            // --- PRIMEIRA LINHA: CARDS DE ACESSO RÁPIDO ---
            $row1 = new TElement('div');
            $row1->class = 'row';

            $linkMoodle     = 'https://moodlefe.com.br/feituverava/eva4/my/courses.php'; 
            $linkBiblioteca = 'https://biblioteca.feituverava.com.br/';

            $labelDiario = $isProfessor ? 'Diário Eletrônico' : 'Boletim';
            $linkDiario  = $isProfessor ? 'index.php?class=HorarioAulasList' : 'index.php?class=BoletimNovoList';
            $linkETicket = $isProfessor ? 'index.php?class=TicketFormListProf' : 'index.php?class=TicketFormListAluno';

            $row1->add($this->column($this->card('Moodle', 'fa-graduation-cap', $linkMoodle, true)));
            $row1->add($this->column($this->card($labelDiario, 'fa-book', $linkDiario)));
            $row1->add($this->column($this->card('E-Ticket', 'fa-ticket-alt', $linkETicket)));
            $row1->add($this->column($this->card('Biblioteca', 'fa-university', $linkBiblioteca, true)));
            $container->add($row1);

            // --- SEGUNDA LINHA: CALENDÁRIO DINÂMICO ADAPTATIVO ---
            $row2 = new TElement('div');
            $row2->class = 'row';

            $colAulas = new TElement('div');
            $colAulas->class = $isProfessor ? 'col-sm-8' : 'col-sm-12';
            
            $aulasPanel = new TPanelGroup('Calendário de Aulas e Horários Letivos');
            
            $this->fc = new TFullCalendar(date('Y-m-d'), 'month');
            $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
            $this->fc->enableFullHeight();
            
            $aulasPanel->add($this->fc);
            $colAulas->add($aulasPanel);
            $row2->add($colAulas);

            if ($isProfessor) {
                $colNotas = new TElement('div');
                $colNotas->class = 'col-sm-4';

                $notas = new TPanelGroup('Lançamento de Notas');                
                $notas->add($periodoAtivoHtml);
                
                $btn = new TElement('a');
                $btn->class = 'btn btn-primary btn-block';
                $btn->href = 'index.php?class=ApontamentoBimestral&method=onReload';
                $btn->add('<i class="fa fa-arrow-circle-right"></i> Abrir Painel de Lançamentos');
                
                $notas->add($btn);
                $colNotas->add($notas);
                $row2->add($colNotas);
            }

            $container->add($row2);

            // --- TERCEIRA LINHA: MURAL VIRTUAL ---
            if (class_exists('Noticias')) 
            {
                $criteriaNoticias = new TCriteria;
                $criteriaNoticias->add(new TFilter('data_expira', '>', $hoje));
                $criteriaNoticias->add(new TFilter('unidade', '=', $loggedUnit));

                if ($logged->funcao_legado == 'Aluno') {
                    $criteriaNoticias->add(new TFilter('publico', '<>', '2'));
                } elseif ($logged->funcao_legado == 'Professor') {
                    $criteriaNoticias->add(new TFilter('publico', '<>', '1'));
                }

                $noticias = Noticias::getObjects($criteriaNoticias);
                if (!empty($noticias)) 
                {
                    $noticias = array_reverse($noticias);

                    foreach ($noticias as $noticia)
                    {
                        if (class_exists('template\Widget\TStory') || class_exists('TStory')) {
                            $story = new TStory(
                                "app/images/atendente.jpg",
                                "Informativo <strong>FE</strong>",
                                'Postado em ' . TDate::date2br($noticia->data_postagem),
                                $noticia->conteudo
                            );
                            $container->add($story);
                        }
                    }
                }
            }

            parent::add($container);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function getEvents($param=NULL)
    {
        $return = array();
        try
        {
            TTransaction::open('Felabs_DB'); 
            
            $logged = SystemUser::newFromLogin(TSession::getValue('login'));
            $codLegado = $logged->systemuser_codlegado;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('Data', '>=', substr($param['start'], 0, 10)));
            $criteria->add(new TFilter('Data', '<=', substr($param['end'], 0, 10)));

            if ($logged->funcao_legado == 'Professor') {
                $criteria->add(new TFilter('Codprofessor', '=', $codLegado));
                $modelClass = 'VwHorariocalendario';
            } else {
                $criteria->add(new TFilter('Codaluno', '=', $codLegado));
                $modelClass = 'VwTurmacalendario';
            }

            if (class_exists($modelClass))
            {
                $aulas = $modelClass::getObjects($criteria);
                foreach ($aulas as $aula)
                {
                    $evento = array();
                    $evento['id']    = isset($aula->CodCalendarioCurso) ? $aula->CodCalendarioCurso : $aula->CodGradeDisciplinaEtapa_Frente;
                    
                    $nomeDiscip  = isset($aula->NomeDisciplina) ? $aula->NomeDisciplina : (isset($aula->NomeFrente) ? $aula->NomeFrente : 'Aula');
                    $turmaIdent  = isset($aula->Identificacao) ? $aula->Identificacao : '';
                    
                    $evento['title'] = $nomeDiscip . " (" . $turmaIdent . ")";
                    
                    $horaFixa = !empty($aula->HoraAula) ? $aula->HoraAula : '19:00:00';
                    $dataAula = isset($aula->Data) ? $aula->Data : date('Y-m-d');
                    
                    $evento['start'] = $dataAula . 'T' . $horaFixa;
                    $evento['end']   = $dataAula . 'T' . date('H:i:s', strtotime($horaFixa . ' + 50 minutes'));
                    
                    $evento['color'] = ($logged->funcao_legado == 'Professor') ? '#337ab7' : '#5cb85c';

                    $popover_content = "<b>Turma:</b> {$turmaIdent}<br>".
                                       "<b>Curso:</b> {$aula->NomeCurso}<br>".
                                       "<b>Ciclo/Etapa:</b> {$aula->Etapa}º Período";
                                       
                    $evento['title'] = TFullCalendar::renderPopover($evento['title'], 'Informações da Disciplina', $popover_content);
                    $return[] = $evento;
                }
            }
            
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            echo json_encode(array());
        }
    }

    public function onReload($param = null)
    {
        if (isset($param['view'])) {
            $this->fc->setCurrentView($param['view']);
        }
        if (isset($param['date'])) {
            $this->fc->setCurrentDate($param['date']);
        }
    }

    private function column($content)
    {
        $col = new TElement('div');
        $col->class = 'col-xs-12 col-sm-3';
        $col->add($content);
        return $col;
    }

    private function card($titulo, $icone, $url, $targetBlank = false)
    {
        $panel = new TPanelGroup($titulo);
        
        $center = new TElement('center');
        
        $link = new TElement('a');
        $link->href = $url;
        if ($targetBlank) {
            $link->target = '_blank';
        }
        
        $icon = new TElement('i');
        $icon->class = "fa {$icone} fa-4x text-primary";
        
        $link->add($icon);
        $center->add($link);
        $panel->add($center);
        
        return $panel;
    }

    /**
     * Ciclo final de exibição da página no Adianti
     */
    public function show()
    {
        parent::show();
        
        if (!TSession::getValue('aviso_exibido'))
        {
            // CORREÇÃO AQUI: Remove o 'onShow' para o construtor assumir o controle perfeito do Ajax
            TApplication::loadPage('AvisoModalView');
            
            TSession::setValue('aviso_exibido', true);
        }
    }
}