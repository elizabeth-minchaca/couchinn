<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioView
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'TwigView.php');

class UsuarioView extends TwigView {

    /**
     * 
     */
    public function renderShow($parameters = array()) {
        echo self::getTwig()->render('usuario_show.html.twig', $parameters);
    }

    /**
     * 
     */
    public function renderEdit($parameters = array()) {
        echo self::getTwig()->render('usuario_edit.html.twig', $parameters);
    }
}
