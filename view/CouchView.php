<?php

require_once(PATH_VIEW . 'TwigView.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CouchView
 *
 * @author kibunke
 */
class CouchView extends TwigView {

    /**
     * Muestra la página para crear un nuevo couch
     */
    public function renderNew($parameters = array()) {
        echo self::getTwig()->render('newcouch.html.twig', $parameters);
    }

    /**
     * Muestra la página del detalle de una couch
     */
    public function renderShow($parameters = array()) {
        echo self::getTwig()->render('showcouch.html.twig', $parameters);
    }

    /**
     * Muestra las opciones de un campo select de ciudades de una provincia
     */
    public function renderCiudades($parameters = array()) {
        echo self::getTwig()->render('ciudades.html.twig', $parameters);
    }

    /**
     * Muestra listado de todos los couchs activos del usuario dueño.
     * @param array $parameters
     */
    public function renderShowUserCouchs($parameters = array()) {
        echo self::getTwig()->render('listadousercouchs.html.twig', $parameters);
    }

    /**
     * 
     */
    public function renderEdit($parameters = array()) {
        echo self::getTwig()->render('modificarcouch.html.twig', $parameters);
    }

}
