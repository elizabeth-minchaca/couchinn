<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of registerLoginController
 *
 * @author kibunke
 */
require_once(PATH_VIEW . 'RegisterLoginView.php');
require_once(PATH_MODEL . 'UsuarioModel.php');
require_once(PATH_MODEL . 'TipoUsuarioModel.php');
require_once(PATH_MODEL . 'PersonaModel.php');
require_once(PATH_CONTROLLER . 'SessionController.php');
require_once(PATH_CONTROLLER . 'IndexController.php');

Class RegisterLoginController {

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
    public static function fechaValidator($day, $month, $year) {
        return checkdate($month, $day, $year);
    }

    /**
     * 
     */
    public static function passwordValidator($password, $confirm) {
        return ($password == $confirm) ? TRUE : FALSE;
    }

    /**
     * 
     */
    public static function emailFormatValidator($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 
     */
    public static function emailUniqueValidator($email) {
        return (UsuarioModel::getInstance()->existEmail($email)) ? FALSE : TRUE;
    }

    /**
     * 
     */
    public function showAction() {
        $view = new RegisterLoginView();
        return $view->renderPage();
    }

    /**
     * 
     */
    public function logout() {
        SessionController::getInstance()->logoutAction();
        header("Location: " . ROOT_URL . "index.php");
    }

    /**
     * 
     */
    public function ajax_registerSubmitAction() {
        $reister = RegisterLoginController::getInstance();
        if (!$reister->emailUniqueValidator($_POST['email'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "Ya se encuentra registrado el email."
            ));
        }
        if (!$reister->emailFormatValidator($_POST['email'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "El email no cumple con el formato correcto (ej: pedroalvarz@gmail.com."
            ));
        }
        if (!$reister->passwordValidator($_POST['password'], $_POST['confirm'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "Las contraseñas no coinciden."
            ));
        }
        if (!$reister->fechaValidator($_POST['nacimiento_dia'], $_POST['nacimiento_mes'], $_POST['nacimiento_anio'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "La fecha seleccionada es incorrecta."
            ));
        }
        $idPersona = PersonaModel::getInstance()->newPersona(array(
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'fechaNacimiento' => date("Y-m-d", strtotime(strval($_POST['nacimiento_dia']) . '-' . strval($_POST['nacimiento_mes']) . '-' . strval($_POST['nacimiento_anio']))), //strval($_POST['nacimiento_dia']) . '-' . strval($_POST['nacimiento_mes']) . '-' . strval($_POST['nacimiento_anio']),
            'genero' => $_POST['genero']
        ));
        if ($idPersona) {
            $tipoUsuario = TipoUsuarioModel::getInstance()->getByNombre("NORMAL"); //Obetener el id del usuario Usuario de la BD
            $idUsuario = UsuarioModel::getInstance()->newUsuario(array(
                'idPersona' => $idPersona,
                'idTipoUsuario' => $tipoUsuario['idTipoUsuario'],
                'email' => $_POST['email'],
                'password' => SessionController::getInstance()->couchinnHash($_POST['password'])
            ));
            if (($idUsuario) && (SessionController::getInstance()->initAction($idUsuario))) {
                return json_encode(array("error" => false));
            } else {
                return json_encode(array(
                    "error" => true,
                    "msj" => "Error en la Base de Datos."
                ));
            }
        } else {
            return json_encode(array(
                "error" => true,
                "msj" => "Error en la Base de Datos."
            ));
        }
    }

    /**
     * 
     */
    public function ajax_loginSubmitAction() {
        $register = RegisterLoginController::getInstance();
        if (!$register->emailFormatValidator($_POST['email'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "El email no cumple con el formato correcto (ej: pedroalvarz@gmail.com)."
            ));
        }
        if ($register->emailUniqueValidator($_POST['email'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "El correo electrónico no se encuentra registrado en el sistema. Por favor, ingrese uno correcto."
            ));
        }
        $rememberme = (isset($_POST['recordarme'])) ? TRUE : FALSE;
        if (SessionController::getInstance()->loginAction($_POST['email'], $_POST['password'], $rememberme)) {
            return json_encode(array(
                "error" => false
            ));
        } else {
            return json_encode(array(
                "error" => true,
                "msj" => "Contraseña incorrecta. Por favor vuelva a intentarlo."
            ));
        }
    }

    /**
     * 
     */
    public function ajax_recoveryAction() {
        $register = RegisterLoginController::getInstance();
        if (!$register->emailFormatValidator($_POST['email'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "Ingrese un correo electrónico válido"
            ));
        }
        if ($register->emailUniqueValidator($_POST['email'])) {
            return json_encode(array(
                "error" => true,
                "msj" => "El correo electrónico no se encuentra registrado en el sistema."
            ));
        }
        return json_encode(array(
            "error" => false,
        ));
    }

}
