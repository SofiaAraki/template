<?php
/**
 * AvisoModalView
 * Renderiza o carrossel de avisos
 */
class AvisoModalView extends TWindow
{
    public function __construct($param = null)
    {
        parent::__construct();
        
        // Configurações padrão da Janela Modal do Adianti
        parent::setSize(0.6, null); 
        parent::removePadding();
        parent::removeTitleBar();
        parent::disableEscape();

        try
        {
            TTransaction::open('Felabs_DB');

            $loggedUserId   = TSession::getValue('userid');
            $loggedUnit     = TSession::getValue('userunitid');
            $hoje           = date('Y-m-d');

            $criteriaNoticias = new TCriteria;
            $criteriaNoticias->add(new TFilter('data_expira', '>=', $hoje));
            
            if ($loggedUnit) {
                $criteriaNoticias->add(new TFilter('unidade', '=', $loggedUnit));
            }

            $usuarioLogado = SystemUser::find($loggedUserId);

            if ($usuarioLogado->funcao_legado == 'Aluno') {
                $criteriaNoticias->add(new TFilter('publico', '<>', '2'));
            } elseif ($usuarioLogado->funcao_legado == 'Professor') {
                $criteriaNoticias->add(new TFilter('publico', '<>', '1'));
            }
            
            $criteriaNoticias->setProperty('order', 'id');
            $criteriaNoticias->setProperty('direction', 'desc');

            $noticias = Noticias::getObjects($criteriaNoticias);

            if (!empty($noticias))
            {
                // Container principal usando TPanelGroup (Adapta-se ao Card do Bootstrap 4)
                $panelGroup = new TPanelGroup('<i class="fa fa-bullhorn red"></i> Comunicados Importantes!');
                $panelGroup->style = 'margin: 0; border: none; box-shadow: none;';

                // Instanciação estruturada do Carrossel Bootstrap 4+
                $carousel = new TElement('div');
                $carousel->id = 'carousel-modal-avisos';
                $carousel->class = 'carousel slide';
                $carousel->{'data-ride'} = 'carousel';
                $carousel->{'data-interval'} = 'false'; 

                // Estilos unificados para fixar tamanho das imagens e dar suporte ao layout
                $cssGlobal = "<style>
                    #carousel-modal-avisos .noticia-conteudo-restrito img {
                        max-width: 100% !important;
                        height: auto !important;
                        max-height: 320px !important;
                        display: block !important;
                        margin: 15px auto !important;
                        border-radius: 4px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
                    }
                    /* Força visibilidade e estilização das setas no Bootstrap 4 */
                    #carousel-modal-avisos .carousel-control-prev-icon,
                    #carousel-modal-avisos .carousel-control-next-icon {
                        background-color: rgba(0, 0, 0, 0.3);
                        padding: 15px;
                        border-radius: 50%;
                    }
                </style>";
                $carousel->add($cssGlobal);

                // 1. Criação das Bolinhas Indicadoras (Bootstrap 4+)
                $indicators = new TElement('ol');
                $indicators->class = 'carousel-indicators';
                $indicators->style = 'bottom: 5px; list-style: none; padding-left: 0; margin-bottom: 0;';

                // 2. Container dos Slides - Classe nativa .carousel-inner do Bootstrap 4
                $carouselInner = new TElement('div');
                $carouselInner->class = 'carousel-inner';
                $carouselInner->style = 'position: relative; width: 100%;';

                $count = 0;
                foreach ($noticias as $noticia)
                {
                    $activeClass = ($count === 0) ? 'active' : '';

                    // Injeta o indicador individual
                    $li = new TElement('li');
                    $li->{'data-target'} = '#carousel-modal-avisos';
                    $li->{'data-slide-to'} = (string)$count;
                    $li->class = $activeClass;
                    $indicators->add($li);

                    // Mudar de 'item' para 'carousel-item' padrão do Bootstrap 4
                    $item = new TElement('div');
                    $item->class = "carousel-item {$activeClass}";
                    $item->style = 'padding: 25px 60px 45px 60px;';

                    $item->add("<small class='text-muted'><i class='fa fa-calendar'></i> Publicado em: " . TDate::date2br($noticia->data_postagem) . "</small>");
                    $item->add("<h3 style='margin-top: 5px; font-weight: bold; border-bottom: 1px solid rgba(128,128,128,0.2); padding-bottom: 8px;'>{$noticia->titulo}</h3>");
                    
                    // Box de rolagem interna do texto
                    $bodyNoticia = new TElement('div');
                    $bodyNoticia->style = 'margin-top: 15px; font-size: 14px; line-height: 1.6; max-height: 380px; overflow-y: auto; padding-right: 5px;';
                    
                    $bodyNoticia->add("<div class='noticia-conteudo-restrito'>{$noticia->conteudo}</div>"); 
                    
                    $item->add($bodyNoticia);
                    $carouselInner->add($item);

                    $count++;
                }

                $carousel->add($indicators);
                $carousel->add($carouselInner);

                /**
                 * Scripts puros de navegação que gerenciam as classes '.active' diretamente no DOM.
                 * Evita o congelamento de estado interno das animações nativas do Bootstrap.
                 */
                $jsPrev = "event.preventDefault(); 
                           var \$carousel = $('#carousel-modal-avisos');
                           var \$activeItem = \$carousel.find('.carousel-item.active');
                           var \$nextItem = \$activeItem.prev('.carousel-item');
                           if (!\$nextItem.length) { \$nextItem = \$carousel.find('.carousel-item').last(); }
                           var index = \$nextItem.index();
                           \$activeItem.removeClass('active');
                           \$nextItem.addClass('active');
                           \$carousel.find('.carousel-indicators li').removeClass('active').eq(index).addClass('active');
                           return false;";

                $jsNext = "event.preventDefault(); 
                           var \$carousel = $('#carousel-modal-avisos');
                           var \$activeItem = \$carousel.find('.carousel-item.active');
                           var \$nextItem = \$activeItem.next('.carousel-item');
                           if (!\$nextItem.length) { \$nextItem = \$carousel.find('.carousel-item').first(); }
                           var index = \$nextItem.index();
                           \$activeItem.removeClass('active');
                           \$nextItem.addClass('active');
                           \$carousel.find('.carousel-indicators li').removeClass('active').eq(index).addClass('active');
                           return false;";

                // 3. Setas de Direção usando a navegação direta via DOM
                $leftControl = new TElement('a');
                $leftControl->class = 'carousel-control-prev';
                $leftControl->{'data-target'} = '#carousel-modal-avisos'; 
                $leftControl->style = 'width: 8%; opacity: 0.7; z-index: 999; cursor: pointer;';
                $leftControl->add('<span class="carousel-control-prev-icon" aria-hidden="true"></span>');
                $leftControl->onclick = $jsPrev;

                $rightControl = new TElement('a');
                $rightControl->class = 'carousel-control-next';
                $rightControl->{'data-target'} = '#carousel-modal-avisos'; 
                $rightControl->style = 'width: 8%; opacity: 0.7; z-index: 999; cursor: pointer;';
                $rightControl->add('<span class="carousel-control-next-icon" aria-hidden="true"></span>');
                $rightControl->onclick = $jsNext;

                $carousel->add($leftControl);
                $carousel->add($rightControl);

                $panelGroup->add($carousel);

                // 4. Botão Fechar Nativo
                $btnClose = new TButton('close_modal');
                $btnClose->class = 'btn btn-sm btn-danger';
                $btnClose->setLabel('Fechar');
                $btnClose->setImage('fa:times');
                $btnClose->addFunction("$(this).closest('[widget=\'TWindow\']').dialog('close');");

                $panelGroup->addFooter(TElement::tag('div', $btnClose, ['style' => 'text-align:right;']));

                parent::add($panelGroup);
            }
            else
            {
                parent::closeWindow();
            }

            TTransaction::close();
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', 'Erro ao carregar avisos: ' . $e->getMessage());
        }
    }
}