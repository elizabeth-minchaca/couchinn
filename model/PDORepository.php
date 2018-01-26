<?php

abstract class PDORepository {

    protected function getConnection() {
        $u = DB_USER;
        $p = DB_PASS;
        $db = DB_NAME;
        $host = DB_HOST;
        $connection = new PDO("mysql:dbname=$db;host=$host", $u, $p, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHAR));
        return $connection;
    }

    protected function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC) {
        $connection = $this->getConnection();
        $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $sth = $connection->prepare($sql);
        foreach ($array as $key => $value) {
            $sth->bindValue("$key", $value);
        }
        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }

    protected function insert($table, $data) {
        ksort($data);
        $fieldNames = implode('`, `', array_keys($data));
        $filedValues = ':' . implode(', :', array_keys($data));
        $connection = $this->getConnection();
        $sth = $connection->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($filedValues)");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        if ($sth->execute()) {
            return $connection->lastInsertId();
        } else {
            return 0;
        }
    }

    protected function update($table, $data, $id) {
        ksort($data);
        $columnas = array_keys($data);
        foreach ($columnas as $key => $value) {
            $columnas[$key] = $value . ' = :' . $value;
        }
        $str = implode(' , ', array_values($columnas));
        $connection = $this->getConnection();
        $sth = $connection->prepare("UPDATE $table SET $str WHERE id = :id");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->bindValue(':id', $id);        
        return $sth->execute();
    }

    protected function delete($table, $id) {
        $connection = $this->getConnection();
        $sth = $connection->prepare("DELETE FROM $table WHERE id = :id");
        $sth->bindValue(':id', $id);
        return $sth->execute();
    }

}
