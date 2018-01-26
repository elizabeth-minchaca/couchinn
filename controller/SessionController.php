<?php

require_once(PATH_VIEW . 'ErrorHandlerView.php');
require_once(PATH_MODEL . 'UsuarioModel.php');

class SessionController {

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
    public function initAction($idUsuario) {
        @session_start();
        $session = SessionController::getInstance();
        $usuarioModel = UsuarioModel::getInstance();
        $usuario = $usuarioModel->getById($idUsuario);
        $session->setValueAction("LOGUEADO", TRUE);
        $session->setValueAction("USER_ID", $usuario['idUsuario']);
        $session->setValueAction("ROL", $usuario['tipo']);
        $session->setValueAction("NOMBRE", $usuario['nombre']);
        $session->setValueAction("APELLIDO", $usuario['apellido']);
        $session->setValueAction("EMAIL", $usuario['email']);
        return $usuarioModel->updateUsuario($usuario['idUsuario'], array(
                    "identificador" => $session->couchinnHash($usuario['email'])
        ));
    }

    /**
     * 
     */
    public function couchinnHash($str) {
        return hash('sha512', $str);
    }

    /**
     * 
     */
    private function generateRandomToken() {
        return $this->couchinnHash((string) rand(1, 99999));
    }

    /**
     * 
     */
    private function renew() {
        $usuarioModel = UsuarioModel::getInstance();
        $usuario = $usuarioModel->getByIdToken(array(
            "identificador" => $_COOKIE["COUCHINN_ID"],
            "token" => $_COOKIE["COUCHINN_TOKEN"]
        ));
        if ($usuario) {
            @session_start();
            $session = SessionController::getInstance();
            $session->setValueAction("LOGUEADO", TRUE);
            $session->setValueAction("USER_ID", $usuario['idUsuario']);
            $session->setValueAction("ROL", $usuario['tipo']);
            $session->setValueAction("NOMBRE", $usuario['nombre']);
            $session->setValueAction("APELLIDO", $usuario['apellido']);
            $session->setValueAction("EMAIL", $usuario['email']);
            $newToken = $session->generateRandomToken();
            setcookie("COUCHINN_TOKEN", $newToken, time() + 60 * 60 * 24 * 30);
            setcookie("COUCHINN_ID", $usuario['identificador'], time() + 60 * 60 * 24 * 30);
            $usuarioModel->updateUsuario($usuario["idUsuario"], array(
                "token" => $newToken,
                "identificador" => $_COOKIE["COUCHINN_ID"]
            ));
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     */
    public function refresh() {
        @session_start();
        $session = SessionController::getInstance();
        $usuarioModel = UsuarioModel::getInstance();
        $usuario = $usuarioModel->getById($session->getValueAction("USER_ID"));
        if ($usuario) {
            $session->setValueAction("LOGUEADO", TRUE);
            $session->setValueAction("USER_ID", $usuario['idUsuario']);
            $session->setValueAction("ROL", $usuario['tipo']);
            $session->setValueAction("NOMBRE", $usuario['nombre']);
            $session->setValueAction("APELLIDO", $usuario['apellido']);
            $session->setValueAction("EMAIL", $usuario['email']);
            $newToken = $session->generateRandomToken();
            $usuarioModel->updateUsuario($usuario["idUsuario"], array(
                "token" => $newToken
            ));
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     */
    public function loginAction($email, $password, $rememberme = FALSE) {
        $session = SessionController::getInstance();
        $usuarioModel = UsuarioModel::getInstance();
        $usuario = $usuarioModel->getUsuario(array(
            "email" => $email,
            "password" => $session->couchinnHash($password) //encripta la contraseña para su cotejamiento en la BD
        ));
        if ($usuario) {//Se encontro el usuario registrado en el sistema
            @session_start();
            if ($rememberme) {
                $token = $session->generateRandomToken();
                setcookie("COUCHINN_TOKEN", $token, time() + 60 * 60 * 24 * 30);
                setcookie("COUCHINN_ID", $usuario['identificador'], time() + 60 * 60 * 24 * 30);
                $usuarioModel->updateUsuario($usuario["idUsuario"], array(
                    "token" => $token
                ));
            }
            $session->initAction($usuario["idUsuario"]);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     */
    public function logoutAction() {
        @session_start();
        @session_destroy();
        UsuarioModel::getInstance()->updateUsuario($_SESSION['USER_ID'], array(
            "token" => "NO_TOKEN"
        ));
        return TRUE;
    }

    /**
     * 
     */
    private function getValueAction($var) {
        return ($_SESSION[$var]);
    }

    /**
     * 
     */
    private function setValueAction($var, $val) {
        $_SESSION[$var] = $val;
    }

    /**
     * 
     */
    private function getRolAction() {
        if (sizeof($_SESSION) > 0 && $_SESSION['ROL'] != '') {
            return $_SESSION['ROL'];
        } else {
            return false;
        }
    }

    /**
     * 
     */
    public function getData() {
        @session_start();
        $session = SessionController::getInstance();
        return array(
            "logueado" => $session->getValueAction("LOGUEADO"),
            "id" => $session->getValueAction("USER_ID"),
            "nombre" => $session->getValueAction("NOMBRE"),
            "apellido" => $session->getValueAction("APELLIDO"),
            "email" => $session->getValueAction("EMAIL"),
            "tipo" => $session->getValueAction("ROL")
        );
    }

    /**
     * 
     */
    public function isLogginAction() {
        @session_start();
        if (isset($_SESSION["LOGUEADO"]) && $_SESSION["LOGUEADO"] == TRUE) {
            return TRUE;
        } elseif (isset($_COOKIE["COUCHINN_ID"]) && isset($_COOKIE["COUCHINN_TOKEN"])) {//Se verifica si existe las cookies del sistema  
            return $this->renew(); //Crea una session recordando datos de acceso
        } else {
            return FALSE;
        }
    }

}
