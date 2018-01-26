<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TipoCouchController
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'TipoCouchView.php');
require_once(PATH_MODEL . 'TipoCouchModel.php');
require_once(PATH_VIEW . 'ErrorHandlerView.php');
require_once(PATH_CONTROLLER . 'SessionController.php');

class TipoCouchController {

    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 
     */
    public function indexAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );
        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
            if (!$dataSession['tipo'] == "ADMINISTRADOR") {
                $view = new ErrorHandlerView();
                return $view->renderAcccesDenied();
            }
            $tipos = TipoCouchModel::getInstance()->getTipos();
            $view = new TipoCouchView();
            return $view->renderIndex(array(
                        "session" => $dataSession,
                        "tipos" => $tipos
            ));
        } else {
            $view = new ErrorHandlerView();
            return $view->renderAcccesDenied();
        }
    }

    /**
     * 
     */
    public function ajax_newAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );

        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
            if (!$dataSession['tipo'] == "ADMINISTRADOR") {
                return json_encode(array(
                    "error" => true,
                    "msj" => "Error de acceso."
                ));
            }
            $result = TipoCouchModel::getInstance()->exist($_POST['nuevoTipo']);
            if (!$result) {
                TipoCouchModel::getInstance()->newTipo(array(
                    "nombre" => strtoupper($_POST['nuevoTipo'])
                ));
                return json_encode(array(
                    "error" => false,
                ));
            } else {
                return json_encode(array(
                    "error" => true,
                    "msj" => "Error de acceso."
                ));
            }
        } else {
            return json_encode(array(
                "error" => true,
                "msj" => "Error de acceso."
            ));
        }
    }

    /**
     * 
     */
    public function ajax_editAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );

        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
            if (!$dataSession['tipo'] == "ADMINISTRADOR") {
                return json_encode(array(
                    "error" => true,
                    "msj" => "Error de acceso."
                ));
            }
            $result = TipoCouchModel::getInstance()->exist($_POST['nuevoTipo']);
            if (!$result) {
                TipoCouchModel::getInstance()->changeTipo(array("nombre" => $_POST['nuevoTipo']), $_POST['idTipo']);
                return json_encode(array(
                    "error" => false,
                ));
            } else {
                return json_encode(array(
                    "error" => true,
                    "msj" => "Error de acceso."
                ));
            }
        } else {
            return json_encode(array(
                "error" => true,
                "msj" => "Error de acceso."
            ));
        }
    }

    /**
     * 
     */
    public function ajax_deleteAction() {
        $session = SessionController::getInstance();
        $dataSession = array(
            "logueado" => NULL
        );

        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
            if (!$dataSession['tipo'] == "ADMINISTRADOR") {
                return json_encode(array(
                    "error" => true,
                    "msj" => "Error de acceso."
                ));
            }


            $result = TipoCouchModel::getInstance()->deleteTest($_POST['idTipo']);


            if ($result) {
                TipoCouchModel::getInstance()->changeTipo(array("bajaLogica" => 1), $_POST['idTipo']);
                return json_encode(array(
                    "error" => false,
                ));
            } else {
                return json_encode(array(
                    "error" => true,
                    "msj" => "El tipo ya esta siendo usado."
                ));
            }
        } else {
            return json_encode(array(
                "error" => true,
                "msj" => "Error de acceso."
            ));
        }
    }

}
