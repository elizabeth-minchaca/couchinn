<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PremiumView
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'TwigView.php');

class PremiumView extends TwigView {

    /**
     * 
     */
    public function renderIndex($parameters = array()) {
        echo self::getTwig()->render('premium.html.twig', $parameters);
    }
    
    /**
     * 
     */
    public function renderBeneficios($parameters = array()) {
        echo self::getTwig()->render('premiumbeneficios.html.twig', $parameters);
    }
}
