<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PrecioPremiumModel
 *
 * @author kibunke
 */
require_once (PATH_MODEL . 'PDORepository.php');

class PrecioPremiumModel extends PDORepository {

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
    public function getPrecio() {
        $result = $this->select("SELECT precio "
                . "FROM preciopremium "
                . "WHERE bajaLogica = 0");
        if (count($result) > 0) {
            $precio = reset($result);
            return $precio['precio'];
        } else {
            return 0;
        }
    }

}
