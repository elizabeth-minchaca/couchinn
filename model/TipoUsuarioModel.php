<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TipoUsuarioModel
 *
 * @author kibunke
 */
require_once (PATH_MODEL . 'PDORepository.php');

class TipoUsuarioModel extends PDORepository {

    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Inserta un couch en la Base de Datos
     */
    public function getByNombre($tipoNombre) {
        $result = $this->select(""
                . "SELECT  idTipoUsuario, nombreTipo "
                . "FROM tipousuario "
                . "WHERE nombreTipo = :nombreTipo AND bajaLogica = 0", array("nombreTipo"=>$tipoNombre));
        if (count($result) > 0) {
            return reset($result);
        } else {
            return FALSE;
        }
    }

}
