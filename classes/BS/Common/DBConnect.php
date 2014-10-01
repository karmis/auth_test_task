<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 4:02
 */

namespace BS\Common;

use BS\Common\Config;

class DBConnect {
    private $db;

    public function connect()
    {
        $config = Config::get('db');
        try {
            $this->db = new \pdo("mysql:host=$config[host];dbname=$config[name]", $config['user'], $config['pass']);
            $this->db->query('set names utf8');
        } catch (\pdoexception $e) {
            die( "Error connection: " . $e->getMessage());
        }

        return $this->db;
    }

    public function __destruct()
    {
        $this->db = null;
    }
} 