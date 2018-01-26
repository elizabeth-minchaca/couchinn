<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
  error_reporting(-1);
 */
/*ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
*/

require_once('./config/config.php');
require_once(PATH_CONTROLLER . 'UsuarioController.php');
require_once(PATH_CONTROLLER . 'SessionController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET["action"]) {
        case 'edit':
            UsuarioController::getInstance()->editAction();
            break;
        case 'show':
            UsuarioController::getInstance()->showAction();
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
        case 'password':
            echo UsuarioController::getInstance()->ajax_changePassAction();
            break;
        case 'submit':
            echo UsuarioController::getInstance()->ajax_saveAction();
            break;
    }
}
