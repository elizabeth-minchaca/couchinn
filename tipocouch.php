<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('./config/config.php');
require_once(PATH_CONTROLLER . 'TipoCouchController.php');
require_once(PATH_CONTROLLER . 'SessionController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    TipoCouchController::getInstance()->indexAction();
}

//Se chequea si el requerimiento http es via POST
//$isPaginador = ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET["action"] == "paginador") ? true : false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_GET["action"]) {
        case 'new':
            echo TipoCouchController::getInstance()->ajax_newAction();
            break;
        case 'edit':
            echo TipoCouchController::getInstance()->ajax_editAction();
            break;
        case 'delete':
            echo TipoCouchController::getInstance()->ajax_deleteAction();
            break;
        default:
            //IndexController::getInstance()->errorPage();
            break;
    }
}
