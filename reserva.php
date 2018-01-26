<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
  error_reporting(-1);
 */

require_once('./config/config.php');
require_once(PATH_CONTROLLER . 'ReservaController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET["action"]) {
        case 'list_realizadas':
            ReservaController::getInstance()->listadoRealizadasAction();
            break;
        case 'list_recibidas':
            ReservaController::getInstance()->listadoRecibidasAction();
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
        case 'sent_reserva':
            echo ReservaController::getInstance()->ajax_sentReservaAction();
            break;
        case 'aceptar_reserva':
            echo ReservaController::getInstance()->ajax_aceptarReservaAction();
            break;
        case 'verificar_conflicto':
            echo ReservaController::getInstance()->ajax_hayconflitofechaAction();
            break;
        case 'rechazar_reserva':
            echo ReservaController::getInstance()->ajax_rechazarReservaAction();
            break;
        case 'get_calificacion':
            echo ReservaController::getInstance()->ajax_getCalificacionAction();
            break;
        default:
            echo ErrorHandlerController::getInstance()->notFoundAction();
            break;
    }
}
