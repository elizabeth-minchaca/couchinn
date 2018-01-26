<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TipoCouchView
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'TwigView.php');

class TipoCouchView extends TwigView {

    /**
     * 
     */
    public function renderIndex($parameters = array()) {
        echo self::getTwig()->render('tipocouch.html.twig', $parameters);
    }

}
