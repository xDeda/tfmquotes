<?php

class Database extends mysqli {
    private $servername = "localhost";
    private $username = "NAME";
    private $password = "PASS";
    private $dbname = "quotes";
    private static $instance = null;

    public function __construct() {
        parent::__construct($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
 
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function exec($s) {
        return $this->query($s);
    }

    public function escape_string($s) {
		return parent::real_escape_string($s);
	}
}
