<?php
/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(1);*/
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('./config/config.php');
require_once(PATH_CONTROLLER . 'RegisterLoginController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET["action"]) {
        case 'logout':
            RegisterLoginController::getInstance()->logout();
            break;
        case 'show':
            RegisterLoginController::getInstance()->showAction();
            break;
        default:
            ErrorHandlerController::getInstance()->notFoundAction();
            break;
    }
}

//Se chequea si el requerimiento http es via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_GET["action"]) {
        case 'login':
            echo RegisterLoginController::getInstance()->ajax_loginSubmitAction();
            break;
        case 'recovery':
            echo RegisterLoginController::getInstance()->ajax_recoveryAction();
            break;
        case 'register':
            echo RegisterLoginController::getInstance()->ajax_registerSubmitAction();
            break;
    }
}