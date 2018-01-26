<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PersonaModel
 *
 * @author kibunke
 */
require_once (PATH_MODEL . 'PDORepository.php');

class PersonaModel extends PDORepository {

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
    public function newPersona($parameters) {
        return $this->insert("persona", $parameters);
    }

    /**
     * Esta funcion es creada solamente porque las BD no tienen como nombre del id de la tabla 'id'
     */
    private function updateP($table, $data, $id) {
        ksort($data);
        $columnas = array_keys($data);
        foreach ($columnas as $key => $value) {
            $columnas[$key] = $value . ' = :' . $value;
        }
        $str = implode(' , ', array_values($columnas));
        $connection = $this->getConnection();
        $sth = $connection->prepare("UPDATE $table SET $str WHERE idPersona = :id");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->bindValue(':id', $id);
        return $sth->execute();
    }

    /**
     * 
     */
    public function editPersona($id, $data) {
        return $this->updateP("persona", $data, $id);
    }
}
