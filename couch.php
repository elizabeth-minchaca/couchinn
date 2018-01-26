<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('./config/config.php');
require_once(PATH_CONTROLLER . 'CouchController.php');
require_once(PATH_CONTROLLER . 'ComentarioController.php');
require_once(PATH_CONTROLLER . 'ErrorHandlerController.php');

//Se chequea si el requerimiento http es via GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET["action"]) {
        case 'new':
            CouchController::getInstance()->newAction();
            break;
        case 'show':
            if (isset($_GET["id"])) {
                CouchController::getInstance()->showAction();
            } else {
                //Se lanza una pagina de error
                ErrorHandlerController::getInstance()->notFoundAction();
            }
            break;
        //Agregado por Elizabeth
        case 'listado_couchs':
            CouchController::getInstance()->showUserCouchs();
            break;
        case 'edit_couch':
            CouchController::getInstance()->editCouchAction();
            break;
        //Agregado por Elizabeth
        default:
            //Se lanza una pagina de error
            ErrorHandlerController::getInstance()->notFoundAction();
            break;
    }
}

//Se chequea si el requerimiento http es via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_GET["action"]) {
        case 'get_ciudades':
            echo CouchController::getInstance()->ajax_getCiudadesAction();
            break;
        case 'couch_submit':
            echo CouchController::getInstance()->ajax_couchSubmitAction();
            break;
        //Agregado por Elizabeth
        case 'despublicar':
            echo CouchController::getInstance()->ajax_despublicarAction();
            break;
        case 'publicar':
            echo CouchController::getInstance()->ajax_publicarAction();
            break;
        case 'edit_submit':
            echo CouchController::getInstance()->ajax_editCouchSubmitAction();
            break;
        //Agregado por Elizabeth
        default:
            echo ErrorHandlerController::getInstance()->notFoundAction();
            break;
    }
}