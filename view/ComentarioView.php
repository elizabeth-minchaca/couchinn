<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComentarioView
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'TwigView.php');

class ComentarioView extends TwigView {

    /**
     * 
     */
    public function renderShow($parameters = array()) {
        echo self::getTwig()->render('comentarios.html.twig', $parameters);
    }

    /**
     * 
     */
    public function renderListRespuestas($parameters = array()) {
        echo self::getTwig()->render('comentariosListadoRespuestas.html.twig', $parameters);
    }

    /**
     * 
     */
    public function renderListPreguntas($parameters = array()) {
        echo self::getTwig()->render('comentariosListadoPreguntas.html.twig', $parameters);
    }

}
