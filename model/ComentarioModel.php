<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComentarioModel
 *
 * @author kibunke
 */
require_once (PATH_MODEL . 'PDORepository.php');

class ComentarioModel extends PDORepository {

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
    public function newComentario($parameters) {
        return $this->insert("comentario", $parameters);
    }

    /**
     * 
     */
    public function getComentarios($idCouch) {
        return $this->select("SELECT c.idComentario, c.idUsuario, c.idCouch, c.pregunta, c.respuesta, c.fecha, c.bajaLogica, p.nombre, p.apellido "
                        . "FROM comentario AS c INNER JOIN usuario AS u ON c.idUsuario = u.idUsuario INNER JOIN persona AS p ON p.idPersona = u.idPersona "
                        . "WHERE c.bajaLogica = 0 AND c.idCouch = :idCouch "
                        . "ORDER BY c.fecha DESC", array("idCouch" => $idCouch));
    }

    /**
     * 
     */
    public function getConsultasPendientes($idUsuario) {
        return $this->select("SELECT c.idComentario, co.titulo, c.idUsuario, c.idCouch, c.pregunta, c.respuesta, c.fecha, c.bajaLogica, p.nombre, p.apellido "
                        . "FROM comentario AS c INNER JOIN usuario AS u ON c.idUsuario = u.idUsuario "
                        . "INNER JOIN persona AS p ON p.idPersona = u.idPersona "
                        . "INNER JOIN couch AS co ON co.idCouch = c.idCouch "
                        . "WHERE c.bajaLogica = 0 AND p.bajaLogica = 0  AND co.bajaLogica = 0 AND co.idUsuario = :idUsuario AND c.respuesta IS NULL "
                        . "ORDER BY c.fecha DESC", array("idUsuario" => $idUsuario));
    }

    /**
     * 
     */
    public function getConsultasNoPendientes($idUsuario) {
        return $this->select("SELECT c.idComentario, co.titulo, c.idUsuario, c.idCouch, c.pregunta, c.respuesta, c.fecha, c.bajaLogica, p.nombre, p.apellido "
                        . "FROM comentario AS c INNER JOIN usuario AS u ON c.idUsuario = u.idUsuario "
                        . "INNER JOIN persona AS p ON p.idPersona = u.idPersona "
                        . "INNER JOIN couch AS co ON co.idCouch = c.idCouch "
                        . "WHERE c.bajaLogica = 0 AND p.bajaLogica = 0  AND co.bajaLogica = 0 AND co.idUsuario = :idUsuario AND c.respuesta IS NOT NULL "
                        . "ORDER BY c.fecha DESC", array("idUsuario" => $idUsuario));
    }

    /*
     * 
     */

    public function getPreguntasRealizadas($idUsuario) {
        return $this->select("SELECT c.idComentario, co.titulo, c.idUsuario, c.idCouch, c.pregunta, c.respuesta, c.fecha, c.bajaLogica, p.nombre, p.apellido "
                        . "FROM comentario AS c INNER JOIN usuario AS u ON c.idUsuario = u.idUsuario "
                        . "INNER JOIN persona AS p ON p.idPersona = u.idPersona "
                        . "INNER JOIN couch AS co ON co.idCouch = c.idCouch "
                        . "WHERE c.bajaLogica = 0 AND p.bajaLogica = 0  AND co.bajaLogica = 0 AND c.idUsuario = :idUsuario "
                        . "ORDER BY c.fecha DESC", array("idUsuario" => $idUsuario));
    }

    /**
     * Esta funcion es creada solamente porque las BD no tienen como nombre del id de la tabla 'id'
     */
    private function updateC($table, $data, $id) {
        ksort($data);
        $columnas = array_keys($data);
        foreach ($columnas as $key => $value) {
            $columnas[$key] = $value . ' = :' . $value;
        }
        $str = implode(' , ', array_values($columnas));
        $connection = $this->getConnection();
        $sth = $connection->prepare("UPDATE $table SET $str WHERE idComentario = :id");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->bindValue(':id', $id);
        return $sth->execute();
    }

    /**
     * 
     */
    public function updateComentario($id, $data) {
        return $this->updateC("comentario", $data, $id);
    }

}
