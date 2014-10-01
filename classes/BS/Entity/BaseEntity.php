<?php
/**
 * Created by PhpStorm.
 * User: karmis
 * Date: 01.10.14
 * Time: 8:05
 */

namespace BS\Entity;
use BS\Common\DBConnect;

class BaseEntity {
    protected  $db;
    public function __construct()
    {
        $this->db = new DBConnect();
        $this->db = $this->db->connect();
    }
} 