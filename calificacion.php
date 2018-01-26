<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
  error_reporting(-1);
 */
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


require_once('./config/config.php');
require_once(PATH_CONTROLLER . 'ReservaController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET["action"]) {
        case 'list_calificar':
            ReservaController::getInstance()->listadoParaCalificarAction();
            break;
        case 'list_calificaciones':
            ReservaController::getInstance()->listadoMisCalificacionesAction();
            break;
        default:
            //Se lanza una pagina de error
            ErrorHandlerController::getInstance()->notFoundAction();
            break;
    }
}

//Se chequea si el requerimiento http es via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_GET["action"]) {
        case 'sent_calificacion':
            echo ReservaController::getInstance()->ajax_sentCalificacionAction();
            break;
        default:
            echo ErrorHandlerController::getInstance()->notFoundAction();
            break;
    }
}

