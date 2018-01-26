<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PremiumController
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'PremiumView.php');
require_once(PATH_VIEW . 'ErrorHandlerView.php');
require_once(PATH_MODEL . 'PrecioPremiumModel.php');
require_once(PATH_MODEL . 'PagoModel.php');
require_once(PATH_MODEL . 'UsuarioModel.php');
require_once(PATH_MODEL . 'TipoUsuarioModel.php');
require_once(PATH_CONTROLLER . 'SessionController.php');

class PremiumController {

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
        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
            $view = new PremiumView();
            return $view->renderIndex(array(
                        "session" => $dataSession,
                        "membresia" => 150
            ));
        } else {
            $view = new ErrorHandlerView();
            return $view->renderAcccesDenied();
        }
    }

    /**
     * 
     */
    public function beneficiosAction() {
        $session = SessionController::getInstance();
        if ($session->isLogginAction()) {
            $dataSession = $session->getData();
            $view = new PremiumView();
            return $view->renderBeneficios(array(
                        "session" => $dataSession
            ));
        } else {
            $view = new ErrorHandlerView();
            return $view->renderAcccesDenied();
        }
    }

    /**
     * 
     */
    public function ajax_joinSubmitAction() {
        $session = SessionController::getInstance();
        $monto = PrecioPremiumModel::getInstance()->getPrecio();
        $dataSession = $session->getData();
        $tipoPremium = TipoUsuarioModel::getInstance()->getByNombre("PREMIUM");
        if ($session->isLogginAction()) {

            $result = PagoModel::getInstance()->newPago(array(
                "idUsuario" => $dataSession['id'],
                "monto" => $monto
            ));
            $result2 = UsuarioModel::getInstance()->updateUsuario($dataSession['id'], array(
                "idTipoUsuario" => $tipoPremium['idTipoUsuario']
            ));
            if ($result) {
                $session->refresh(); //se refresaca la Session
                return json_encode(array(
                    "error" => false
                ));
            } else {
                return json_encode(array(
                    "error" => true,
                    "msj" => "Error al almacenar el Pago"
                ));
            }
        } else {
            return json_encode(array(
                "error" => true,
                "msj" => "Error de session"
            ));
        }
    }

}
