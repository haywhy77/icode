<?php
class Database extends DB\SQL\Mapper {
	protected $response;

	public function __construct(DB\SQL $db,$table='app_desc') {
       	parent::__construct($db,$table);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
	public function DBSelect($table, array $where=null,array $fields=null,$order='',$selector='and'){
		//var_dump($fields);
		$response=array();
        try{
            $a = array();
            $w = "";
            foreach ($where as $key => $value) {
                $w .= " $selector " .$key. " like :".$key;
                $a[":".$key] = $value;
            }
			$sql="";
			if(is_null($fields) || empty($fields)){
				$sql="select * from ";
			}else{
				$sql="select ".implode(',',$fields)." from ";
			}
			$order_stm='';
			if($order!=''){
				$w.=" $order";
			}
			$sql=$sql.$table." where 1=1 ". $w;//. $order_stm;
			//echo "$sql<br>";//exit;
            $stmt = $this->db->prepare($sql);
            $stmt->execute($a);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($rows)<=0){
				$response["code"] = -1;
				$response["resp"] = false;
                $response["status"] = "warning";
                $response["message"] = "No data found.";
            }else{
				$response["code"] = 1;
				$response["resp"] = true;
                $response["status"] = "success";
                $response["message"] = "Data selected from database";
            }
                $response["data"] = $rows;
        }catch(PDOException $e){
			$response["code"] = -2;
			$response["resp"] = false;
            $response["status"] = "error";
            $response["message"] = 'Select Failed: ' .$e->getMessage();
            $response["data"] = null;
        }
        
        $this->response=$response;
        return $this;
    }
	public function DBInsert($table, $columnsArray, $requiredColumnsArray) {
        $this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
        
        try{
            $a = array();
            $c = "";
            $v = "";
            foreach ($columnsArray as $key => $value) {
                $c .= $key. ", ";
                $v .= ":".$key. ", ";
                $a[":".$key] = $value;
            }
            $c = rtrim($c,', ');
            $v = rtrim($v,', ');
			//var_dump($a);
			//echo "INSERT INTO $table($c) VALUES($v)";
            $stmt =  $this->db->prepare("INSERT INTO $table($c) VALUES($v)");
			$return=$stmt->execute($a);
            if($return){
				$affected_rows = $stmt->rowCount();
				$response["code"] = $this->db->lastInsertId('id');
				$response["resp"] = true;
				$response["status"] = "success";
				$response["message"] = $affected_rows." row inserted into database";
			}else{
				$response["code"] = -2;
				$response["resp"] = false;
				$response["status"] = "Unable to save record";
				$response["message"] = 'Insert Failed: Unable to save details';	
			}
        }catch(PDOException $e){
			$response["code"] = -2;
			$response["resp"] = false;
            $response["status"] = "error";
            $response["message"] = 'Insert Failed: ' .$e->getMessage();
        }
        return (object)$response;
    }
	public function DBUpdate($table, $columnsArray, $where, $requiredColumnsArray){
        $this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
        try{
            $a = array();
            $w = "";
            $c = "";
            foreach ($where as $key => $value) {
                $w .= " and " .$key. " = :".$key;
                $a[":".$key] = $value;
            }
            foreach ($columnsArray as $key => $value) {
                $c .= $key. " = :".$key.", ";
                $a[":".$key] = $value;
            }
                $c = rtrim($c,", ");
                //echo "UPDATE $table SET $c WHERE 1=1 ".$w;exit;
            $stmt =  $this->db->prepare("UPDATE $table SET $c WHERE 1=1 ".$w);
            $stmt->execute($a);
            $affected_rows = $stmt->rowCount();
            if($affected_rows<=0){
				$response["code"] = -1;
				$response["resp"] = false;
                $response["status"] = "warning";
                $response["message"] = "No updated/change committed";
            }else{
				$response["code"] = 1;
				$response["resp"] = true;
                $response["status"] = "success";
                $response["message"] = $affected_rows." row(s) updated in database";
            }
        }catch(PDOException $e){
			$response["code"] = -2;
			$response["resp"] = false;
            $response["status"] = "error";
            $response["message"] = "Update Failed: " .$e->getMessage();
        }
        return (object)$response;
    }
	public function DBDelete($table, $where){
        if(count($where)<=0){
			$response["code"] = -1;
			$response["resp"] = false;
            $response["status"] = "warning";
            $response["message"] = "Delete Failed: At least one condition is required";
        }else{
            try{
                $a = array();
                $w = "";
                foreach ($where as $key => $value) {
                    $w .= " and " .$key. " = :".$key;
                    $a[":".$key] = $value;
                }
                $stmt =  $this->db->prepare("DELETE FROM $table WHERE 1=1 ".$w);
                $stmt->execute($a);
                $affected_rows = $stmt->rowCount();
                if($affected_rows<=0){
					$response["code"] = -1;
					$response["resp"] = false;
                    $response["status"] = "warning";
                    $response["message"] = "No row deleted";
                }else{
					$response["code"] = 1;
					$response["resp"] = true;
                    $response["status"] = "success";
                    $response["message"] = $affected_rows." row(s) deleted from database";
                }
            }catch(PDOException $e){
				$response["code"] = -2;
				$response["resp"] = false;
                $response["status"] = "error";
                $response["message"] = 'Delete Failed: ' .$e->getMessage();
            }
        }
        return (object)$response;
    }
	public function DBQuery($sql){
		//echo "$sql<br>";//exit;
		try{
			if($sql !=''){
				$rows=$this->db->exec($sql);
				if(is_array($rows) && !empty($rows) && !is_null($rows) && count($rows)>0){
					$response["code"] = 1;
					$response["resp"] = true;
					$response["status"] = "Success";
					$response["message"] = '';
					$response['data']=$rows;	
				}else{
					$response["code"] = -2;
					$response["resp"] = false;
					$response["status"] = "error";
					$response["message"] = 'Query string is empty';
					$response['data']=null;
				}
				
			}else{
				$response["code"] = -2;
				$response["resp"] = false;
				$response["status"] = "error";
				$response["message"] = 'Query string is empty';
				$response['data']=null;
			}	
			
		}catch(PDOException $e){
			$response["code"] = -2;
			$response["resp"] = false;
			$response["status"] = "error";
			$response["message"] = 'Operation Failed: ' .$e->getMessage();
			$response['data']=null;
		}
		$this->response= $response;
		return $this;
	}
	public function BreakArray($array){
		//var_dump($array);exit;
		$a = array();
		$c = "";
		$v = "";
		foreach ($array as $key => $value) {
			if(is_array($value)){
				$keys=array_keys($value);
				$c=implode(',',$keys);
				for($i=0;$i<count($keys);$i++){
					$keys[$i].=":".$keys[$i];
				}
				$v= implode(',',$keys);
				$this->BreakArray($value);
			}else{
				//$c .= $key. ", ";
				//$v .= ":".$key. ", ";
				
				$a[":".$key] = $value;
			}
		}
		//var_dump($a);
		$c = rtrim($c,', ');
		$v = rtrim($v,', ');
		
		return array($c,$v,$a);
	}
	public function verifyRequiredParams($inArray, $requiredColumns) {
        $error = false;
        $errorColumns = "";
		foreach ($requiredColumns as $field) {
			if (!isset($inArray[$field]) || strlen(trim($inArray[$field])) <= 0) {
				$error = true;
				$errorColumns .= $field . ', ';
			}
		}
		foreach ($inArray as $keys) {
			//var_dump($keys);continue;
			
		}
        
 		//exit;
        if ($error) {
            $response = array();
			$response["code"] = -2;
			$response["resp"] = false;
            $response["status"] = "error";
            $response["value"] = $inArray;
            $response["message"] = 'Required field(s) ' . rtrim($errorColumns, ', ') . ' is missing or empty';
            print_r($response);
            exit;
        }
    }
    public function response(){
    	return (is_array($this->response['data']) && count($this->response['data'])>0)?(object)$this->response['data']:false;
	}
	public function all(){
    	return (is_array($this->response['data']) && count($this->response['data'])>0)?$this->response['data']:false;
    }
    public function first(){
    	return (is_array($this->response['data']) && count($this->response['data'])>0)?(object)array_shift($this->response['data']):false;
	}
	public function last(){
    	return (is_array($this->response['data']) && count($this->response['data'])>0)?(object)end($this->response['data']):false;
    }
	public function filter(array $search, array $allowed=null){
		$filtered = array_filter(
			$this->response['data'],
			function ($val, $key) use ($search) { // N.b. $val, $key not $key, $val
				return isset($search[$key]) && (
					$allowed[$key] === $val
				);
			},
			ARRAY_FILTER_USE_BOTH
		);
		return $filtered;
	}
	public function paginates($page, $limit){
		if(is_array($this->response['data']) && count($this->response['data'])>0){
			$total = count( $this->response['data'] ); //total items in array  
			$totalPages = ceil( $total/ $limit ); //calculate total pages
			$page = max($page, 1); //get 1 page when $_GET['page'] <= 0
			$page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
			$offset = ($page - 1) * $limit;
			if( $offset < 0 ) $offset = 0;
			return [
				"data"=>array_slice( $this->response['data'], $offset, $limit ), 
				"totalPages"=>$totalPages,
				"currentPage"=>$page,
				"limit"=>$limit
			];
		}else{
			return [
				"data"=>[], 
				"totalPages"=>0,
				"currentPage"=>1,
				"limit"=>$limit
			];
		}
	}
	/*
	$rows = $db->select("customers_php",array());
	$rows = $db->select("customers_php",array('id'=>171));
	$rows = $db->insert("customers_php",array('name' => 'Ipsita Sahoo', 'email'=>'ipi@angularcode.com'), array('name', 'email'));
	$rows = $db->update("customers_php",array('name' => 'Ipsita Sahoo', 'email'=>'email'),array('id'=>'170'), array('name', 'email'));
	$rows = $db->delete("customers_php", array('name' => 'Ipsita Sahoo', 'id'=>'227'));
	
	select(table name, where clause as associative array)
	insert(table name, data as associative array, mandatory column names as array)
	update(table name, column names as associative array, where clause as associative array, mandatory columns as array)
	delete(table name, where clause as array)
*/	
}
?>
