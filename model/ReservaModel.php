<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReservaModel
 *
 * @author kibunke
 */
require_once (PATH_MODEL . 'PDORepository.php');

class ReservaModel extends PDORepository {

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
    public function newReserva($parameters) {
        return $this->insert("reserva", $parameters);
    }

    /**
     * 
     */
    public function getEstado($estado) {
        $resultado = $this->select("SELECT * "
                . "FROM reservaestado "
                . "WHERE bajaLogica = 0 AND nombre = :nombre ", array("nombre" => $estado));
        return (count($resultado) > 0) ? reset($resultado) : null;
    }

    /**
     * 
     */
    public function getListadoRealizados($idUsuario) {
        return $this->select("SELECT c.foto, c.titulo, c.idUsuario AS propietario, r.idReserva, r.idCouch, r.idUsuarioHospedado, r.idReservaEstado, r.fechaInicio, r.fechaFin, r.comentarioUsuario, r.comentarioReserva, r.comentarioCouch, r.puntajeCouch, r.puntajeUsuario, r.fechaAlta, r.bajaLogica, re.nombre AS estado "
                        . "FROM reserva AS r "
                        . "INNER JOIN reservaestado AS re ON r.idReservaEstado = re.id "
                        . "INNER JOIN couch AS c ON c.idCouch = r.idCouch "
                        . "WHERE c.bajaLogica = 0 AND r.bajaLogica = 0 AND r.idUsuarioHospedado = :idUsuarioHospedado "
                        . "ORDER BY r.fechaAlta DESC ", array(
                    "idUsuarioHospedado" => $idUsuario
        ));
    }

    /**
     * 
     */
    public function getListadoRecibidosPendientes($idUsuario) {
        $estado = $this->getEstado("PENDIENTE");
        return $this->select("SELECT p.nombre, p.apellido, c.titulo, r.idReserva, r.idCouch, r.idUsuarioHospedado, r.idReservaEstado, r.fechaInicio, r.fechaFin, r.comentarioUsuario, r.comentarioReserva, r.comentarioCouch, r.puntajeCouch, r.puntajeUsuario, r.fechaAlta, r.bajaLogica, re.nombre AS estado "
                        . "FROM reserva AS r "
                        . "INNER JOIN reservaestado AS re ON r.idReservaEstado = re.id "
                        . "INNER JOIN couch AS c ON c.idCouch = r.idCouch "
                        . "INNER JOIN usuario AS u ON u.idUsuario = r.idUsuarioHospedado "
                        . "INNER JOIN persona AS p ON u.idPersona = p.idPersona "
                        . "WHERE c.bajaLogica = 0 AND r.bajaLogica = 0 AND c.idUsuario = :idUsuario AND r.idReservaEstado = :idReservaEstado "
                        . "ORDER BY r.fechaAlta DESC ", array(
                    "idUsuario" => $idUsuario,
                    "idReservaEstado" => $estado['id']
        ));
    }

    /**
     * 
     */
    public function getListadoRecibidosNoPendientes($idUsuario) {
        $estado = $this->getEstado("PENDIENTE");
        return $this->select("SELECT p.nombre, p.apellido, c.titulo, c.idUsuario AS propietario, r.idReserva, r.idCouch, r.idUsuarioHospedado, r.idReservaEstado, r.fechaInicio, r.fechaFin, r.comentarioUsuario, r.comentarioReserva, r.comentarioCouch, r.puntajeCouch, r.puntajeUsuario, r.fechaAlta, r.bajaLogica, re.nombre AS estado "
                        . "FROM reserva AS r "
                        . "INNER JOIN reservaestado AS re ON r.idReservaEstado = re.id "
                        . "INNER JOIN couch AS c ON c.idCouch = r.idCouch "
                        . "INNER JOIN usuario AS u ON u.idUsuario = r.idUsuarioHospedado "
                        . "INNER JOIN persona AS p ON u.idPersona = p.idPersona "
                        . "WHERE c.bajaLogica = 0 AND r.bajaLogica = 0 AND c.idUsuario = :idUsuario AND r.idReservaEstado != :idReservaEstado "
                        . "ORDER BY r.fechaAlta DESC ", array(
                    "idUsuario" => $idUsuario,
                    "idReservaEstado" => $estado['id']
        ));
    }

    /**
     * 
     */
    public function reservasEnRangoFecha($idCouch, $fechaInicio, $fechaFin) {
        return $this->select("SELECT p.nombre, p.apellido, c.titulo, r.idReserva, r.idCouch, r.idUsuarioHospedado, r.idReservaEstado, r.fechaInicio, r.fechaFin, r.comentarioUsuario, r.comentarioReserva, r.comentarioCouch, r.puntajeCouch, r.puntajeUsuario, r.fechaAlta, r.bajaLogica, re.nombre AS estado "
                        . "FROM reserva AS r "
                        . "INNER JOIN reservaestado AS re ON r.idReservaEstado = re.id "
                        . "INNER JOIN couch AS c ON c.idCouch = r.idCouch "
                        . "INNER JOIN usuario AS u ON u.idUsuario = r.idUsuarioHospedado "
                        . "INNER JOIN persona AS p ON u.idPersona = p.idPersona "
                        . "WHERE c.bajaLogica = 0 AND r.bajaLogica = 0 AND r.idCouch = :idCouch AND  ( "
                        . "( "
                        . "(r.fechaInicio >= CAST(:fechaInicio1 AS DATE) AND r.fechaInicio <= CAST(:fechaFin1 AS DATE))) "
                        . " OR (r.fechaFin >= CAST(:fechaInicio2 AS DATE) AND r.fechaFin <= CAST(:fechaFin2 AS DATE)) "
                        . " OR (r.fechaInicio <= CAST(:fechaInicio3 AS DATE) AND r.fechaFin >= CAST(:fechaFin3 AS DATE)) "
                        . ") "
                        . "ORDER BY r.fechaAlta DESC ", array(
                    "idCouch" => $idCouch,
                    "fechaInicio1" => $fechaInicio,
                    "fechaFin1" => $fechaFin,
                    "fechaInicio2" => $fechaInicio,
                    "fechaFin2" => $fechaFin,
                    "fechaInicio3" => $fechaInicio,
                    "fechaFin3" => $fechaFin
        ));
    }

    /**
     * 
     */
    public function getById($idReserva) {
        $resultado = $this->select("SELECT * "
                . "FROM reserva "
                . "WHERE idReserva = :idReserva ", array(
            "idReserva" => $idReserva,
        ));
        return (count($resultado) > 0) ? reset($resultado) : null;
    }

    /**
     * 
     */
    public function getCalificacionesPendientes($idUsuario) {
        $estado = $this->getEstado("ACEPTADA");
        return $this->select("SELECT p.nombre, p.apellido, c.titulo, r.idReserva, r.idCouch, r.idUsuarioHospedado, r.idReservaEstado, r.fechaInicio, r.fechaFin, r.comentarioUsuario, r.comentarioReserva, r.comentarioCouch, r.puntajeCouch, r.puntajeUsuario, r.fechaAlta, r.bajaLogica, re.nombre AS estado "
                        . "FROM reserva AS r "
                        . "INNER JOIN reservaestado AS re ON r.idReservaEstado = re.id "
                        . "INNER JOIN couch AS c ON c.idCouch = r.idCouch "
                        . "INNER JOIN usuario AS u ON u.idUsuario = r.idUsuarioHospedado "
                        . "INNER JOIN persona AS p ON u.idPersona = p.idPersona "
                        . "WHERE c.bajaLogica = 0 AND r.bajaLogica = 0 AND c.idUsuario = :idUsuario AND r.idReservaEstado = :idReservaEstado AND CURDATE() > r.fechaFin "
                        . "ORDER BY r.fechaAlta DESC ", array(
                    "idUsuario" => $idUsuario,
                    "idReservaEstado" => $estado['id']
        ));
    }

    /**
     * 
     */
    public function getCalificacionesRealizadas($idUsuario) {
        $estado = $this->getEstado("CALIFICADA");
        return $this->select("SELECT p.nombre, p.apellido, c.titulo, r.idReserva, r.idCouch, r.idUsuarioHospedado, r.idReservaEstado, r.fechaInicio, r.fechaFin, r.comentarioUsuario, r.comentarioReserva, r.comentarioCouch, r.puntajeCouch, r.puntajeUsuario, r.fechaAlta, r.bajaLogica, re.nombre AS estado "
                        . "FROM reserva AS r "
                        . "INNER JOIN reservaestado AS re ON r.idReservaEstado = re.id "
                        . "INNER JOIN couch AS c ON c.idCouch = r.idCouch "
                        . "INNER JOIN usuario AS u ON u.idUsuario = r.idUsuarioHospedado "
                        . "INNER JOIN persona AS p ON u.idPersona = p.idPersona "
                        . "WHERE c.bajaLogica = 0 AND r.bajaLogica = 0 AND c.idUsuario = :idUsuario AND r.idReservaEstado = :idReservaEstado AND CURDATE() > r.fechaFin "
                        . "ORDER BY r.fechaAlta DESC ", array(
                    "idUsuario" => $idUsuario,
                    "idReservaEstado" => $estado['id']
        ));
    }

    /**
     * 
     */
    public function getMisCalificaciones($idUsuario) {
        $estado = $this->getEstado("CALIFICADA");
        return $this->select("SELECT c.idUsuario AS idPropietario, p.nombre, p.apellido, c.titulo, r.idReserva, r.idCouch, r.idUsuarioHospedado, r.idReservaEstado, r.fechaInicio, r.fechaFin, r.comentarioUsuario, r.comentarioReserva, r.comentarioCouch, r.puntajeCouch, r.puntajeUsuario, r.fechaAlta, r.bajaLogica, re.nombre AS estado "
                        . "FROM reserva AS r "
                        . "INNER JOIN reservaestado AS re ON r.idReservaEstado = re.id "
                        . "INNER JOIN couch AS c ON c.idCouch = r.idCouch "
                        . "INNER JOIN usuario AS u ON u.idUsuario = c.idUsuario "
                        . "INNER JOIN persona AS p ON u.idPersona = p.idPersona "
                        . "WHERE c.bajaLogica = 0 AND r.bajaLogica = 0 AND r.idUsuarioHospedado = :idUsuarioHospedado AND r.idReservaEstado = :idReservaEstado  "
                        . "ORDER BY r.fechaAlta DESC ", array(
                    "idUsuarioHospedado" => $idUsuario,
                    "idReservaEstado" => $estado['id']
        ));
    }
    
    
    /**
     * Esta funcion es creada solamente porque las BD no tienen como nombre del id de la tabla 'id'
     */
    private function updateR($table, $data, $id) {
        ksort($data);
        $columnas = array_keys($data);
        foreach ($columnas as $key => $value) {
            $columnas[$key] = $value . ' = :' . $value;
        }
        $str = implode(' , ', array_values($columnas));
        $connection = $this->getConnection();
        $sth = $connection->prepare("UPDATE $table SET $str WHERE idReserva = :id");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->bindValue(':id', $id);
        return $sth->execute();
    }

    /**
     * 
     */
    public function updateReserva($id, $data) {
        return $this->updateR("reserva", $data, $id);
    }

}
