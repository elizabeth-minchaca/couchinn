<?php

require_once(PATH_VIEW . 'TwigView.php');

class RegisterLoginView extends TwigView {

    /**
     * Muestra la página para crear un nuevo couch
     */
    public function renderPage($parameters = array()) {
        echo self::getTwig()->render('registerlogin.html.twig', $parameters);
    }

}