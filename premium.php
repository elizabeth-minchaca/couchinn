<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('./config/config.php');
require_once(PATH_CONTROLLER . 'PremiumController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET["action"]) {
        case 'join':
            PremiumController::getInstance()->indexAction();
            break;
        case 'beneficios':
            PremiumController::getInstance()->beneficiosAction();
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
        case 'join':
            echo PremiumController::getInstance()->ajax_joinSubmitAction();
            break;
    }
}