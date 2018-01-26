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
require_once(PATH_CONTROLLER . 'ComentarioController.php');
require_once(PATH_CONTROLLER . 'SessionController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET["action"]) {
        case 'list_questions':
            ComentarioController::getInstance()->listadoPreguntasAction();
            break;
        case 'list_answers':
            ComentarioController::getInstance()->listadoRespuestasAction();
            break;
        case 'get_queries':
            echo ComentarioController::getInstance()->ajax_getQueriesAction();
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
        case 'sent_query':
            echo ComentarioController::getInstance()->ajax_sentQueryAction();
            break;
        case 'send_response':
            echo ComentarioController::getInstance()->ajax_sentResponseAction();
            break;
        default:
            echo ErrorHandlerController::getInstance()->notFoundAction();
            break;
    }
}
