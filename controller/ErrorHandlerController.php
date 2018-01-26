<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorHandlerController
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'ErrorHandlerView.php');

class ErrorHandlerController {

    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Pagina de error
     */
    public function notFoundAction() {
        $view = new ErrorHandlerView();
        return $view->renderNotFound();
    }

    /**
     * Pagina de acceso denegado
     */
    public function accessDeniedPageAction() {
        $view = new ErrorHandlerView();
        return $view->renderAcccesDenied();
    }

}
