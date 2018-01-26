<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioController
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'UsuarioView.php');
require_once(PATH_MODEL . 'UsuarioModel.php');
require_once(PATH_MODEL . 'PersonaModel.php');
require_once(PATH_CONTROLLER . 'SessionController.php');
require_once(PATH_CONTROLLER . 'RegisterLoginController.php');

class UsuarioController {

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
    public function showAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            return ErrorHandlerController::getInstance()->accessDeniedPageAction();
        }
        $id = $_GET['id'];
        $usuario = UsuarioModel::getInstance()->getById($id);
        if (!$usuario) {
            return ErrorHandlerController::getInstance()->notFoundAction();
        }
        $dataSession = $session->getData();
        //    if (($dataSession['tipo'] == "ADMINISTRADOR") || ($usuario && $usuario['idUsuario'] == $dataSession['id'])) {
        $view = new UsuarioView();
        return $view->renderShow(array(
                    "usuario" => $usuario,
                    "session" => $dataSession
        ));
        //  } else {
        //      return ErrorHandlerController::getInstance()->accessDeniedPageAction();
        //  }
    }

    /**
     * 
     */
    public function editAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            return ErrorHandlerController::getInstance()->accessDeniedPageAction();
        }
        $id = $_GET['id'];
        $usuario = UsuarioModel::getInstance()->getById($id);
        if (!$usuario) {
            return ErrorHandlerController::getInstance()->notFoundAction();
        }
        $dataSession = $session->getData();
        if (($dataSession['tipo'] == "ADMINISTRADOR") || ($usuario && $usuario['idUsuario'] == $dataSession['id'])) {
            $view = new UsuarioView();
            return $view->renderEdit(array(
                        "usuario" => $usuario,
                        "session" => $dataSession
            ));
        } else {
            return ErrorHandlerController::getInstance()->accessDeniedPageAction();
        }
    }

    /**
     * 
     */
    public function ajax_changePassAction() {
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            return ErrorHandlerController::getInstance()->accessDeniedPageAction();
        }
        //Se verifica que coincidan las nuevas contraseñas
        if ($_POST['passChangeNueva'] != $_POST['passChangeNuevaConfirm']) {
            return json_encode(array(
                "error" => true,
                "msj" => 'La contraseña no coinciden'
            ));
        }
        //Se verifica que la contraseña actual ingresada sea correcta
        $dataSession = $session->getData();
        $usuario = UsuarioModel::getInstance()->getById($dataSession['id']);
        if ($usuario && $session->couchinnHash($_POST['passChangeActual']) != $usuario['password']) {
            return json_encode(array(
                "error" => true,
                "msj" => 'La contraseña actual es incorrecta',
            ));
        }
        UsuarioModel::getInstance()->updateUsuario($dataSession['id'], array(
            "password" => $session->couchinnHash($_POST['passChangeNueva'])
        ));
        return json_encode(array(
            "error" => false
        ));
    }

    /**
     * 
     */
    public function ajax_saveAction() {
        $reister = RegisterLoginController::getInstance();
        $session = SessionController::getInstance();
        if (!$session->isLogginAction()) {
            return json_encode(array(
                "error" => true,
                "msj" => "El usuario no esta logueado en el sistema."
            ));
        }
        //validar la fecha
        if (!$reister->fechaValidator($_POST['usuarioFormNacimiento_dia'], $_POST['usuarioFormNacimiento_mes'], $_POST['usuarioFormNacimiento_anio'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "La fecha seleccionada es incorrecta."
            ));
        }
        //verificar si el usuario logueado tiene permisos de modificar ese id
        $persona = UsuarioModel::getInstance()->getById($_POST['usuarioFormId']);
        if (!$persona) {
            return json_encode(array(
                "error" => true,
                "msj" => "No se encontró el usuario en el sistema."
            ));
        }
        $result = PersonaModel::getInstance()->editPersona($persona['idPersona'], array(
            'nombre' => $_POST['usuarioFormNombre'],
            'apellido' => $_POST['usuarioFormApellido'],
            'fechaNacimiento' => date("Y-m-d", strtotime(strval($_POST['usuarioFormNacimiento_dia']) . '-' . strval($_POST['usuarioFormNacimiento_mes']) . '-' . strval($_POST['usuarioFormNacimiento_anio']))),
            'telefono' => $_POST['usuarioFormTelefono'],
            'direccion' => $_POST['usuarioFormDireccion'],
            'genero' => $_POST['usuarioFormgenero']
        ));
        if (!$result) {
            return json_encode(array(
                "error" => true,
                "msj" => "No se pudo guardar los datos en le Base de Datos."
            ));
        }
        return json_encode(array(
            "error" => false
        ));
    }

}
