<?php

require_once(PATH_VIEW . 'TwigView.php');

class IndexView extends TwigView {

    /**
     * 
     */
    public function renderIndex($parameters = array()) {
        echo self::getTwig()->render('index.html.twig', $parameters);
    }

    /**
     * 
     */
    public function renderCouchinns($parameters = array()) {
        return self::getTwig()->render('indexcouchinns.html.twig', $parameters);
    }

    /**
     * 
     */
    public function renderPaginador($parameters = array()) {
        return self::getTwig()->render('indexpaginador.html.twig', $parameters);
    }

    public function renderPaginadorSearch($parameters = array()) {
        return self::getTwig()->render('indexpaginadorsearch.html.twig', $parameters);
    }

}
