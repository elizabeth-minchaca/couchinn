<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReservaView
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'TwigView.php');

class ReservaView extends TwigView {

    /**
     * 
     */
    public function renderListadoRealizadas($parameters = array()) {
        echo self::getTwig()->render('reservas_realizadas.html.twig', $parameters);
    }

    /**
     */
    public function renderListadoRecibidas($parameters = array()) {
        echo self::getTwig()->render('reservas_recibidas.html.twig', $parameters);
    }

    /**
     */
    public function renderCalificaciones($parameters = array()) {
        echo self::getTwig()->render('reservas_calificaciones.html.twig', $parameters);
    }
    
    /**
     */
    public function renderMisCalificaciones($parameters = array()) {
        echo self::getTwig()->render('reservas_miscalificaciones.html.twig', $parameters);
    }

}
