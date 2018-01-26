<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComentarioController
 *
 * @author kibunke
 */
require_once(PATH_MODEL . 'ComentarioModel.php');
require_once(PATH_CONTROLLER . 'SessionController.php');
require_once(PATH_VIEW . 'ComentarioView.php');

class ComentarioController {

    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*
     * 
     */

    public function listadoPreguntasAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            $view = new ErrorHandlerView();
            return $view->renderAcccesDenied();
        }
        $dataSession = $session->getData();
        $view = new ComentarioView();
        $realizadas = ComentarioModel::getInstance()->getPreguntasRealizadas($dataSession['id']);
        return $view->renderListPreguntas(array(
                    "session" => $dataSession,
                    "realizadas" => $realizadas
        ));
    }

    /**
     * 
     */
    public function listadoRespuestasAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            $view = new ErrorHandlerView();
            return $view->renderAcccesDenied();
        }
        $dataSession = $session->getData();
        $view = new ComentarioView();
        $pendientes = ComentarioModel::getInstance()->getConsultasPendientes($dataSession['id']);
        $respondidas = ComentarioModel::getInstance()->getConsultasNoPendientes($dataSession['id']);
        return $view->renderListRespuestas(array(
                    "session" => $dataSession,
                    "pendientes" => $pendientes,
                    "respondidas" => $respondidas
        ));
    }

    /**
     * 
     */
    public function ajax_sentQueryAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            return json_encode(array(
                "error" => true,
                "msj" => "Acceso denegado"
            ));
        }
        $sessionData = $session->getData();
        $comentario = ComentarioModel::getInstance()->newComentario(array(
            "idUsuario" => $sessionData['id'],
            "idCouch" => $_POST['idCouch'],
            "pregunta" => $_POST['comentario']
        ));
        return json_encode(array(
            "error" => false
        ));
    }

    /**
     * 
     */
    public function ajax_getQueriesAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            $view = new ErrorHandlerView();
            return $view->renderAcccesDenied();
        }
        $comentarios = ComentarioModel::getInstance()->getComentarios($_GET['id']);
        $view = new ComentarioView();
        return $view->renderShow(array(
                    "comentarios" => $comentarios,
        ));
    }

    /**
     * 
     */
    public function ajax_sentResponseAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            return json_encode(array(
                "error" => true,
                "msj" => "Acceso denegado"
            ));
        }



//verificar que el id de session sea propietario del id del couch en donde pertenece el comentario


        $sessionData = $session->getData();
        $comentario = ComentarioModel::getInstance()->updateComentario($_POST['id'], array(
            "respuesta" => $_POST['comentario']
        ));
        return json_encode(array(
            "error" => false
        ));
    }

}
