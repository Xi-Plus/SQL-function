<?php
class query {
	//ALL
	public $host = null;
	public $dbname = null;
	public $table = null;
	public $username = null;
	public $password = null;
	public $charset = null;
	public $blindlist = array();
	// SELECT WHERE UPDATE DELETE
	public $where = null;
	public $limit = null;
	// SELECT
	public $column = "*";
	public $order = null;
	public $group = null;
	// INSERT UPDATE
	public $value = null;
	// SQL
	public $query = "";
	function __construct(){
		require("db.php");
		$this->host = $db->host;
		$this->dbname = $db->dbname;
		$this->table = $db->table;
		$this->username = $db->username;
		$this->password = $db->password;
		$this->type = $db->type;
		$this->charset = $db->charset;
	}
	function connect() {
		try {
			$link = new PDO($this->type.":host=".$this->host.";dbname=".$this->dbname.";charset=".$this->charset, $this->username, $this->password);
		} catch (PDOException $e) {
			exit("SQL connect error: ".$e->getMessage());
		}
		return $link;
	}
	function randomkey($length) {
		$pattern = "abcdefghijklmnopqrstuvwxyz";
		$key = "";
		for($i=0; $i < $length; $i++){
			$key .= $pattern{rand(0, 25)};
		}
		return $key;
	}
	function createbind($value) {
		$bindvalue = $this->randomkey(8);
		$this->blindlist[$bindvalue] = $value;
		return ":".$bindvalue;
	}
	function bind($sth) {
		try {
			foreach($this->blindlist as $index => $value){
				$sth->bindValue($index, $value);
			}
		} catch (PDOExsception $e) {
			exit("SQL bind error: ".$e->getMessage());
		}
		return $sth;
	}
	function fetch($link, $query) {
		try {
			$sth = $link->prepare($query);
			$sth = $this->bind($sth);
			$success = $sth->execute();
			$this->blindlist=array();
			return (object)["success"=>$success, "sth"=>$sth];
		} catch(PDOExsception $e) {
			exit("SQL fetch error: ".$e->getMessage());
		}
		return $result;
	}
	function WHERE(){
		$where = $this->where;
		if ($where == null) return "";
		if (!is_array($where)) {
			exit("WHERE isn't a array");
		} else if (!is_array($where[0])) {
			$where = array($where);
		}
		$query = "WHERE ";
		foreach($where as $index => $value) {
			if (!isset($value[0]) || is_null($value[0])) {
				
			} else if (isset($value[2]) && !is_null($value[2])) {
				if ($value[2] === "REGEXP") {
					$query .= "`".$value[0]."` REGEXP ".str_replace("+","[+]",$this->createbind($value[1]))." ";
				} else if (!is_null($value[1])) {
					$query .= "`".$value[0]."` ".$value[2].$this->createbind($value[1])." ";
				} else {
					$query .= "`".$value[0]."` ".$value[2]." ";
				}
			} else {
				$query .= "`".$value[0]."`=".$this->createbind($value[1])." ";
			}
			if ($index < count($where)-1) {
				if (isset($value[3])) {
					$query .= $value[3]." ";
				} else {
					$query .= "AND ";
				}
			}
		}
		return $query;
	}
	function LIMIT($limit) {
		if ($limit == null || $limit == "all") {
			return "";
		} else if (is_array($limit)) {
			if (!isset($limit[1])) {
				exit("LIMIT offset 2 undefined");
			} else if(!is_numeric($limit[0]) || !is_numeric($limit[1])) {
				exit("LIMIT wrong type");
			} else {
				return "LIMIT ".$limit[0].",".$limit[1]." ";
			}
		} else if (is_numeric($limit)) {
			return "LIMIT ".$limit." ";
		} else {
			exit("LIMIT wrong type");
		}
	}
	function SELECT($oneline = false) {
		$link = $this->connect();
		$query = "SELECT ";
		if (is_string($this->column)) {
			$query .= $this->column." ";
		} else if (is_array($this->column)) {
			foreach ($this->column as $index => $value) {
				if ($index!=0) $query .= ",";
				$query .= $value;
			}
			$query.=" ";
		}
		$query .= "FROM `".$this->table."` ";
		$query .= $this->WHERE();
		if ($this->group !== null) {
			if (!is_array($this->group)) {
				$this->group = array($this->group);
			}
			$query .= "GROUP BY ";
			foreach ($this->group as $index => $value) {
				if ($index != 0) $query .= ",";
				$query .= "`".$value."`";
			}
			$query .= " ";
		}
		if ($this->order !== null) {
			if (!is_array($this->order)) {
				exit("WHERE isn't a array");
			} else if(!is_array($this->order[0])) {
				$this->order = array($this->order);
			}
			$query .= "ORDER BY ";
			foreach ($this->order as $index => $value) {
				if ($index != 0) $query .= ",";
				if (isset($value[1])) $query .= "`".$value[0]."` ".$value[1];
				else $query .= "`".$value[0]."` ASC";
			}
			$query .= " ";
		}
		if ($this->limit !== null) $query .= LIMIT($this->limit);
		$sth = $this->fetch($link, $query)->sth;
		$sth->setFetchMode(PDO::FETCH_ASSOC);
		$result=$sth->fetchAll();
		if ($oneline) return $result[0];
		else return $result;
	}
	function INSERT() {
		$link = $this->connect();
		$query = "INSERT INTO `".$this->table."` (";
		if (!is_array($this->value[0])) {
			$this->value = array($this->value);
		}
		foreach ($this->value as $index => $temp) {
			if ($index != 0) $query.=",";
			$query .= "`".$temp[0]."`";
		}
		$query .= ")VALUES(";
		foreach ($this->value as $index => $temp) {
			if ($index != 0) $query .= ",";
			$query .= $this->createbind($temp[1]);
		}
		$query .= ")";
		$success = $this->fetch($link, $query)->success;
		return $success;
	}
	function UPDATE() {
		$link = $this->connect();
		$query = "UPDATE `".$this->table."` SET ";
		if (!is_array($this->value[0])) {
			$this->value = array($this->value);
		}
		foreach ($this->value as $index => $temp) {
			if ($index != 0)$query .= ",";
			$query .= "`".$temp[0]."`=".$this->createbind($temp[1]);
		}
		$query .= " ".$this->WHERE().$this->LIMIT($this->limit);
		$sth = $this->fetch($link, $query)->sth;
		return $sth->rowCount();
	}
	function DELETE() {
		$link = $this->connect();
		$query = "DELETE FROM `".$this->table."` ".$this->WHERE().$this->LIMIT($this->limit);
		$sth = $this->fetch($link, $query)->sth;
		return $sth->rowCount();
	}
	function SQL() {
		$link = $this->connect();
		try {
			$result = $link->query($this->query);
			return $result;
		} catch (PDOException $e) {
			exit("SQL query error: ".$e->getMessage());
		}
	}
}
?>
