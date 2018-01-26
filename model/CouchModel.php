<?php

require_once (PATH_MODEL . 'PDORepository.php');

class CouchModel extends PDORepository {

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
    public function newCouch($parameters) {
        return $this->insert("couch", $parameters);
    }

    /**
     * 
     */
    public function count() {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "WHERE couch.bajaLogica = 0");
        return count($result);
    }

    /**
     * 
     */
    public function getCouchById($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.idCouch = :idCouch AND couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0", $parameters);
        if (count($result) > 0) {
            $couch = reset($result); //retorna el primer elemento del arreglo;
            $couch['foto'] = base64_encode($couch["foto"]);
            return $couch;
        } else {
            return false;
        }
    }

    /**
     * 
     */
    public function getCouchs($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    /**
     * Se obtienen todos los tipos de Couch activos (no borrados)
     */
    public function getTipos() {
        return $this->select("SELECT * FROM tipocouch WHERE bajaLogica = 0 ORDER BY nombre ASC");
    }

    /**
     * Se obtienen todas las provinicas activas (no borradas)
     */
    public function getProvincias() {
        return $this->select("SELECT * FROM provincia WHERE bajaLogica = 0 ORDER BY nombre ASC");
    }

    /**
     * Se obtienen todas las ciudades activas (no borradas) que perteneces a una provincia que se especifica como parámetro 
     */
    public function getCiudades($parameters) {
        return $this->select("SELECT * FROM provincia_ciudad INNER JOIN ciudad ON ciudad.idCiudad = provincia_ciudad.id_ciudad WHERE ciudad.bajaLogica = 0 AND provincia_ciudad.id_provincia = :id_provincia ORDER BY ciudad.nombre ASC", $parameters);
    }

    /**
     * Retorna todos los couchs publicados y despublicados de un usuario.
     * @param unknown $parameters
     */
    public function getUserCouchs($parameters) {
        return $result = $this->select(""
                . "SELECT couch.idCouch, couch.idUsuario, couch.bajaLogica, couch.titulo, couch.capacidad, tc.nombre AS tipo, p.id AS idProvincia, p.nombre AS provincia, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.idUsuario = :idUsuario AND p.bajaLogica = 0 AND tc.bajaLogica = 0 "
                . "ORDER BY couch.idCouch DESC ", $parameters);
    }

    /**
     * Actualiza el couch con los nuevos parametros
     * @param unknown $table
     * @param unknown $data
     * @param unknown $id
     */
    private function updateCouch($table, $data, $id) {
        ksort($data);
        $columnas = array_keys($data);
        foreach ($columnas as $key => $value) {
            $columnas[$key] = $value . ' = :' . $value;
        }
        $str = implode(' , ', array_values($columnas));
        $connection = $this->getConnection();
        $sth = $connection->prepare("UPDATE $table SET $str WHERE idCouch = :idCouch");
        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->bindValue(':idCouch', $id);
        return $sth->execute();
    }

    /**
     * Actualiza el couch con todos sus parametros
     */
    public function changeCouch($parameters, $id) {
        return $this->updateCouch("couch", $parameters, $id);
    }

    /**
     * Retorna el tipo buscado por id
     * @param unknown $parameters
     * @return mixed
     */
    public function getNombreTipoCouch($parameters) {

        $result = $this->select("SELECT nombre FROM tipocouch WHERE idTipoCouch = :idTipoCouch", $parameters);
        return reset($result);
    }

    /**
     * Retorna la provincia buscada por id
     * @param unknown $parameters
     * @return mixed
     */
    public function getNombreProvincia($parameters) {
        $result = $this->select("SELECT nombre FROM provincia WHERE id = :idProvincia", $parameters);
        return reset($result);
    }

    /**
     * Retorna la ciudad buscada por id
     * @param unknown $parameters
     * @return mixed
     */
    public function getNombreCiudad($parameters) {
        $result = $this->select("SELECT nombre FROM ciudad WHERE idCiudad = :idCiudad", $parameters);
        return reset($result);
    }

    /**
     * Retorna todos los couchs por un tipo de couch en especial
     * @param unknown $data
     */
    public function searchCouchsByTipo($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND couch.idTipoCouch = :idTipoCouch "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    /**
     * Retorna la cantidad de todos los couchs por un tipo de couch en especial
     * @param unknown $parameters
     * @return number
     */
    public function countCouchsByTipo($parameters) {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.idTipoCouch = :idTipoCouch AND couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 ", $parameters);

        return count($result);
    }

    /**
     * Retorna todos los couchs que esten en dicha provincia pasada por parametro
     * @param unknown $parameters
     * @return string[]
     */
    public function searchCouchsByProvincia($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND p_c.id_provincia = :idProvincia "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    /**
     * Retorna la cantidad de todos los couchs que esten en dicha provincia pasada por parametro
     * @param unknown $parameters
     * @return number
     */
    public function countCouchsByProvincia($parameters) {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE p.id = :idProvincia AND couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND p_c.id_provincia = :idProvincia ", $parameters);

        return count($result);
    }

    /**
     * Retorna todos los couchs que esten en dicha provincia y ciudad pasados por parametros
     * @param unknown $parameters
     * @return string[]
     */
    public function searchCouchsByProvinciaCiudad($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND p_c.id_ciudad = :idCiudad AND p_c.id_provincia = :idProvincia "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    /**
     * Retorna la cantidad de todos los couchs que esten en dicha provincia y ciudad pasados por parametros
     * @param unknown $parameters
     * @return number
     */
    public function countCouchsByProvinciaCiudad($parameters) {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE p.id = :idProvincia AND couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND p_c.id_provincia = :idProvincia AND couch.idCiudad = :idCiudad ", $parameters);

        return count($result);
    }

    /**
     * Retorna todos los couchs que contengan con la descripcion pasada
     * @param unknown $parameters
     * @return string[]
     */
    public function searchCouchsByDescripcion($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 AND tc.bajaLogica = 0 "
                . "AND couch.descripcion LIKE :descripcion "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    /**
     * Retorna l cantidad de todos los couchs que contengan con la descripcion pasada
     * @param unknown $parameters
     * @return number
     */
    public function countCouchsByDescripcion($parameters) {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 AND p.bajaLogica = 0 "
                . "AND tc.bajaLogica = 0 AND couch.descripcion LIKE :descripcion ", $parameters);

        return count($result);
    }

    /**
     * Retorna todos los couchs que coincidan con los parámetros pasados
     * @param unknown $parameters
     * @return string[]
     */
    public function searchCouchsByAll($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 "
                . "AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND couch.idTipoCouch = :idTipoCouch "
                . "AND p_c.id_provincia = :idProvincia AND p_c.id_ciudad = :idCiudad "
                . "AND couch.descripcion LIKE :descripcion "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    /**
     * Retorna cantidad de couchs que coincidan con los parametros pasados
     * @param unknown $parameters
     * @return number
     */
    public function countCouchsByAll($parameters) {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 "
                . "AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND couch.idTipoCouch = :idTipoCouch "
                . "AND p_c.id_provincia = :idProvincia AND p_c.id_ciudad = :idCiudad "
                . "AND couch.descripcion LIKE :descripcion ", $parameters);

        return count($result);
    }

    /**
     * 
     * @param unknown $parameters
     * @return string[]
     */
    public function searchCouchsByTipoLugar($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 "
                . "AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND couch.idTipoCouch = :idTipoCouch "
                . "AND p_c.id_provincia = :idProvincia AND p_c.id_ciudad = :idCiudad "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    /**
     * Retorna la cantidad 
     * @param unknown $parameters
     * @return number
     */
    public function countCouchsByTipoLugar($parameters) {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 "
                . "AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND couch.idTipoCouch = :idTipoCouch "
                . "AND p_c.id_provincia = :idProvincia AND p_c.id_ciudad = :idCiudad ", $parameters);

        return count($result);
    }

    public function searchCouchsByTipoProvincia($parameters) {
        $result = $this->select(""
                . "SELECT couch.idCouch, couch.titulo, couch.descripcion, couch.capacidad, couch.idTipoCouch AS idTipoCouch, couch.foto, couch.idUsuario, tc.nombre AS tipo, p.nombre AS provincia, p.id AS idProvincia, c.idCiudad AS idCiudad, c.nombre AS ciudad "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 "
                . "AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND couch.idTipoCouch = :idTipoCouch "
                . "AND p_c.id_provincia = :idProvincia "
                . "ORDER BY couch.idCouch DESC "
                . "LIMIT :limit "
                . "OFFSET :offset", $parameters);

        $resultProcess = array();
        foreach ($result as $couch) {
            $couch['foto'] = base64_encode($couch["foto"]);
            $resultProcess[] = $couch;
        }

        return $resultProcess;
    }

    public function countCouchsByTipoProvincia($parameters) {
        $result = $this->select(""
                . "SELECT * "
                . "FROM couch "
                . "INNER JOIN tipocouch AS tc ON tc.idTipoCouch = couch.idTipoCouch "
                . "INNER JOIN provincia_ciudad AS p_c ON  p_c.id_ciudad = couch.idCiudad "
                . "INNER JOIN provincia AS p ON p.id = p_c.id_provincia "
                . "INNER JOIN ciudad AS c ON c.idCiudad = p_c.id_ciudad "
                . "WHERE couch.bajaLogica = 0 AND c.bajaLogica = 0 "
                . "AND p.bajaLogica = 0 AND tc.bajaLogica = 0 AND couch.idTipoCouch = :idTipoCouch "
                . "AND p_c.id_provincia = :idProvincia ", $parameters);

        return count($result);
    }

}
