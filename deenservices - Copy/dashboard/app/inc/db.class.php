<?php
Class DB{
	var $dbh;
	var $trans=FALSE;
	//! Number of rows affected by query
	var $rows=0;
	function __construct($db,$user,$pwd,$host){
		try {
			$this->dbh = new PDO('mysql:host='.$host.';dbname='.$db, $user, $pwd,array(PDO::ATTR_AUTOCOMMIT=>FALSE));
			$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbh->exec("SET CHARACTER SET utf8");
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	function FetchQuery($sql){
		if($sql==''){
			return '';
		}
		try{
			$stm = $this->dbh->prepare($sql);
    		$stm->execute();
			$result =$stm->fetchAll();
			return $result;
		}catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		
	}

	function UpdateQuery($sql){
		if($sql==''){
			return '';
		}
		try{
			$stm = $this->dbh->prepare($sql);
    		$stm->execute();
			return 1;
		}catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		
	}
	function DeleteQuery($table,$condition=''){
		if($table=='') return null;
		$sql="delete from $table $condition";
		try{
			$stm = $this->dbh->prepare($sql);
    		$stm->execute();
			return 1;
		}catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	function InsertQuery($table,$array,$getlastid=false){
		
		if(!is_array($array) || count((Array)$array)<1 || $table==''){
			return 'Error. Parameters are empty;';
		}
		$count=count((Array)$array);
		$keys='';
		$value=array();
		$str='';
		while (list($key, $val) = each($array)) {
			//var_dump($key);exit;
			$keys .="{$key},";
			$value[]=$val;
			$str .='?,';
		}
		$keys =rtrim($keys,',');
		
		$str=rtrim($str,',');
		$sql="insert into $table($keys) ";
		$sql .=" VALUES ($str)";
		if($getlastid){
			//$sql .="; SELECT LAST_INSERT_ID() ";
		}
		$this->begin();
		$sth = $this->dbh->prepare($sql);
		$sth->execute((array)$value);
		//$temp = $sth->fetchAll(PDO::FETCH_ASSOC);
		$this->commit();
		$rows = $sth->fetchAll();
		//$this->rollback();
		if($getlastid){
			return $this->dbh->lastInsertId();
		}else{
			return 0;
		}
	}
	function begin() {
		$out=$this->dbh->beginTransaction();
		$this->trans=TRUE;
		return $out;
	}

	/**
	*	Rollback SQL transaction
	*	@return bool
	**/
	function rollback() {
		$out=$this->dbh->rollback();
		$this->trans=FALSE;
		return $out;
	}

	/**
	*	Commit SQL transaction
	*	@return bool
	**/
	function commit() {
		$out=$this->dbh->commit();
		$this->trans=FALSE;
		return $out;
	}
	function NumRows($result){
		return count((Array)$result);
	}
	
	function FetchData($result){
		foreach ($result->fetchAll as $row) {
			return $row;
		}
	}
	function LastInsertID($sql){
		$result=$this->FetchQuery($sql);
		if (is_array($result) && !empty($result)){
			list($id)=$result[0];
			return $id;
		}
		return 0;
	}
	function in_object($value,$object,$empty=false) {
		if($empty && (empty($object) || is_null($object)) ) return true;
		if (is_array($object)) {
		  foreach($object as $key => $item) {
			  //echo "$value==$item,";return;exit;
			if ($value==$item) return true;
		  }
		}
		return false;
	 }
}
?>