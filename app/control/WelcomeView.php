<?php
/**
 * WelcomeView
 *
 * @version    8.5
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class WelcomeView extends TPage
{
    public function __construct()
    {
        parent::__construct();

        $container = new TElement('div');

        // Primeira linha
        $row1 = new TElement('div');
        $row1->class = 'row';

        $row1->add($this->column($this->card('Portal')));
        $row1->add($this->column($this->card('Diário Eletrônico')));
        $row1->add($this->column($this->card('Questione')));
        $row1->add($this->column($this->card('Biblioteca')));

        // Segunda linha
        $row2 = new TElement('div');
        $row2->class = 'row';

        $colAulas = new TElement('div');
        $colAulas->class = 'col-sm-8';

        $aulas = new TPanelGroup('Aulas do Dia');
        $aulas->add('<center><br><br>Não há aulas hoje.</center>');
        $colAulas->add($aulas);

        $colNotas = new TElement('div');
        $colNotas->class = 'col-sm-4';

        $notas = new TPanelGroup('Lançamento de Notas');
        $notas->add('<center><br><i class="fa fa-file-text fa-5x"></i><br><br></center>');

        $btn = new TButton('acessar');
        $btn->setLabel('Acessar');

        $notas->add($btn);

        $colNotas->add($notas);

        $row2->add($colAulas);
        $row2->add($colNotas);

        $container->add($row1);
        $container->add($row2);

        parent::add($container);
    }

    private function column($content)
    {
        $col = new TElement('div');
        $col->class = 'col-sm-3';

        $col->add($content);

        return $col;
    }

    private function card($titulo)
    {
        $panel = new TPanelGroup($titulo);

        $panel->add('
            <center>
                <br>
                <i class="fa fa-graduation-cap fa-4x"></i>
                <br><br>
            </center>
        ');

        return $panel;
    }   
}