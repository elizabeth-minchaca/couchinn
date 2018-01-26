<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TipoCouchModel
 *
 * @author kibunke
 */
require_once (PATH_MODEL . 'PDORepository.php');

class TipoCouchModel extends PDORepository {

    private static $instance;

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function exist($tipo) {
        $result = $this->select("SELECT * "
                . "FROM tipocouch "
                . "WHERE bajaLogica = 0 AND nombre = :nombre", array("nombre" => $tipo));
        if (count($result) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function updateTipo($table, $data, $id) {
        ksort($data);
        $columnas = array_keys($data);
        foreach ($columnas as $key => $value) {
            $columnas[$key] = $value . ' = :' . $value;
        }
        $str = implode(' , ', array_values($columnas));
        $connection = $this->getConnection();
        $sth = $connection->prepare("UPDATE $table SET $str WHERE idTipoCouch = :idTipoCouch");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->bindValue(':idTipoCouch', $id);
        return $sth->execute();
    }

    /**
     * 
     */
    public function changeTipo($parameters, $id) {
        return $this->updateTipo("tipocouch", $parameters, $id);
    }

    /**
     * 
     */
    public function deleteTest($idTipo) {
        $result = $this->select("SELECT * "
                . "FROM couch "
                . "WHERE bajaLogica = 0 AND idTipoCouch = :idTipoCouch", array("idTipoCouch" => $idTipo));
        if (count($result) > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * 
     */
    public function newTipo($parameters) {
        return $this->insert("tipocouch", $parameters);
    }

    /**
     * 
     */
    public function getTipos() {
        return $this->select("SELECT idTipoCouch, nombre, bajaLogica "
                        . "FROM tipocouch "
                        . "WHERE bajaLogica = 0 "
                        . "ORDER BY nombre ASC");
    }

}
