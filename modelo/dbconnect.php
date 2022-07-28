<?php
  date_default_timezone_set("America/Bogota");

  class dbconnect
  {

    private $host = 'localhost';
    private $dbname = 'db_deca';
    private $user = 'root';
    private $password = '';
    private $db;

    function __construct()
    {
      try {
        $this->db = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname,$this->user,$this->password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
          die($e->getMessage());//Imprime el mensaje de error en pantalla
      }
    }
    public function con()
    {
      return $this->db;
    }
    public function update_condition($tabla, $data, $condicion)
    {
      return $this->db->query("UPDATE $tabla SET $data WHERE $condicion");
    }
    public function select_condition($tabla, $condicion)
    {
      return $this->db->query("SELECT * FROM $tabla WHERE $condicion");
    }
    public function sql($sql)
    {
      return $this->db->query($sql);
    }
    public function select($tabla){

      return $this->db->query("SELECT * FROM $tabla");
    }
    public function AllConsult($val)
    {
      return $val->fetchAll(PDO::FETCH_OBJ);
    }
    public function Consult($val)
    {
      return $val->fetch(PDO::FETCH_OBJ);
    }
  }

?>
