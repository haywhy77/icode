<?php

setlocale(LC_MONETARY, 'en_US');

class Controller extends \Prefab{
 
    public $f3;
    public $db;
 
    function beforeRoute(\Base $f3) {
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
			$ip = $_SERVER['HTTP_CLIENT_IP'];  
		} 
		//whether ip is from the proxy  
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
		}
		  //whether ip is from the remote address  
		else{  
			$ip = $_SERVER['REMOTE_ADDR'];  
		}  
		// $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
		// var_dump($headers); echo "\n\n\n";
		
		// var_dump($_SERVER);exit;
		$url=$_SERVER["QUERY_STRING"];
		
		// var_dump($f3->get('POST.session_csrf'), $f3->get('SESSION.csrf'));exit;
		if ($f3->VERB=='POST' && strpos($url, "/candidate/jobs/") && $f3->get('POST.session_csrf')!=$f3->get('SESSION.csrf')) {
			$f3->clear('POST');
			$f3->VERB='GET';
			// var_dump($f3->get('POST'));exit;
			$f3->error(403);
		}
		$f3->CSRF=$f3->session->csrf();
		$f3->copy('CSRF','SESSION.csrf');
		$this->cors();
		// echo $this->isAjax($_SERVER);exit;
		if(!$this->isAjax($_SERVER)){
			die();
		}
	}
 	
    function afterRoute() {
    	echo Template::instance()->render('body.html');
    }
 
    function __construct() {
 
        $f3=Base::instance();
 
        
 		
		$this->f3=$f3;
		$this->dbConnect();
    }

	function dbConnect(){
		$db_con=new DB\SQL(
            $this->f3->get('db_dns').$this->f3->get('db_name'),
            $this->f3->get('db_user'),
            $this->f3->get('db_pass')
        );
		$this->db= new Database($db_con,'users');
		
	}

	public function Response($status_code, $response) {
		$this->cors();//exit;
		header('Content-Type: application/json');
		echo json_encode($response,JSON_NUMERIC_CHECK);
	}

	function cors() {
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}
	
		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				// may also be using PUT, PATCH, HEAD etc
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	
		}
	}
	private function pathToRegex($path) {
		$escapedPath = preg_quote($path, '/');
		
		$pattern = preg_replace('/12345678/', '(\d+)', $escapedPath);
		
		return "/^" . $pattern . "$/";
	}
	private function isAjax($server) {
		if (!isset($server['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') return true;

		$static=["/candidate/validate", "/candidate/jobs/", "/employers/staff/remove", "/users/update", "/users/new", "/jobs/fields", "/jobs/locations", "/jobs/skills", "/messaging/update/hunts","/jobs/misc-trash", "/applications/", "/employers/"];
		// var_dump($server);exit;
		$found=false;
		$parsedUrl = parse_url($server['REQUEST_URI']);
		$pathOnly = $parsedUrl['path'];
		foreach($static as $k=>$v){
			if(strpos($pathOnly, $v)){
				$found=true;
				break;
			}
		}

		// if(!$found){
		// 	$permitted=["/applications/12345678/update", "/employers/12345678/post-staff"];
		// 	foreach($permitted as $k=>$v){
		// 		$pattern=$this->pathToRegex($v);
		// 		if(preg_match($pattern, $pathOnly, $matches)){
		// 			$found=true;
		// 			break;
		// 		}
		// 		echo "$pattern \n\n";
		// 	}
		// }
		
		// echo "Found: $found _2";exit;
		return $found;
	}
	public function code_generator($prefix='',$id='',$chars=20) {
		$return='';
		$posible=$id.substr(number_format(time() * mt_rand(),0,'',''),0,10);
		$code='';
		$i=0;
		while($i<$chars){
			$code.=substr($posible, mt_rand(0,strlen($posible)-1),1);
			if($i<$chars) ++$i;
		}
		
		$return=$prefix.$code;
	
		return $return;
	}

	function in_object($value,$object,$empty=false) {
		if($empty) return true;
		if (is_array($object)) {
		  foreach($object as $key => $item) {
			  //echo "$value==$item,";
			if ($value==$item) return true;
		  }
		}
		return false;
	 }
	function createFileName(){
		 return str_replace('-','',$this->uuid());
	}
	function uuid($serverID=1){
		$t=explode(" ",microtime());
		return sprintf( '%04x-%08s-%08s-%04s-%04x%04x',
			$serverID,
			$this->clientIPToHex(),
			substr("00000000".dechex($t[1]),-8),   // get 8HEX of unixtime
			substr("0000".dechex(round($t[0]*65536)),-4), // get 4HEX of microtime
			mt_rand(0,0xffff), mt_rand(0,0xffff));
	}
	function clientIPToHex($ip="") {
		$hex="";
		if($ip=="") $ip=getEnv("REMOTE_ADDR");
		$part=explode('.', $ip);
		for ($i=0; $i<=count((Array)$part)-1; $i++) {
			$hex.=substr("0".dechex($part[$i]),-2);
		}
		return $hex;
	}
	function getArrayDiff($a1,$a2) {
		$r = array(); 
		foreach ($a2 as $key => $second) { 
			foreach ($a1 as $keys => $first) { 
				if (isset($a2[$keys])) { 
					foreach ($first as $first_value) { 
						foreach ($second as $second_value) { 
							if ($first_value == $second_value){ 
								$true = true; 
								break;    
							} 
						} 
						if (!isset($true)) { 
							
							$r[$key][] = $first_value; 
						} 
						unset($true); 
					} 
				} else { 
					$r[$key] = $first; 
				} 
			}
		} 
		return $r; 
	} 
	
	function writeFile($uri,$content) {
		$dir=mb_substr($uri,0,-mb_strlen(strrchr($uri,"/")));
		if(!file_exists($dir)) {
			mkdir($dir,0777);
		}
		$fp = fopen($uri, 'w+');
		fwrite($fp, $content);
		fclose($fp);
		return true;
	}
	function createIMG($base64_string, $output_file) {
		$uri = 'ui/uploads/download/';
		if(!file_exists($uri)) {
			mkdir($uri);
		}
		$uri .=$output_file;
		//echo $uri;exit;
		$ifp = fopen($uri, "wb"); 
		$data = explode(',', $base64_string);
		fwrite($ifp, base64_decode($data[1])); 
		fclose($ifp); 
		return $uri; 
	}
	function verifyOP($op='select'){
		$privilege=$_SESSION['login_cand_privilege'][0];
		$error="";
		$found=false;
		switch($op){
			case'select':
				if($privilege['allow_select'] ==0){
					$error="Permission denied. You cannot perform select operation.";
					$found=true;
				}
			break;
			case'insert':
				if($privilege['allow_insert'] ==0){
					$error="Permission denied. You cannot perform insert operation.";
					$found=true;
				}
			break;
			case'update':
				if($privilege['allow_update'] ==0){
					$error="Permission denied. You cannot perform update operation.";
					$found=true;
				}
			break;
			case'delete':
				if($privilege['allow_delete'] ==0){
					$error="Permission denied. You cannot perform delete operation.";
					$found=true;
				}
			break;
		}
		if ($found){
			$errors=array('type'=>'2','title'=>"Error code: ",'msg'=>$error,'url'=>'/');
			$this->f3->set('SESSION.errorMSG',$errors);
			$this->f3->reroute('/info');
		}
	}
	public function stringToSecret(string $string = NULL){
		if (!$string) {
			return NULL;
		}
		$length = strlen($string);
		$visibleCount = (int) round($length / 4);
		$hiddenCount = $length - ($visibleCount * 2);
		return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
	}

	public function relocateFiles($dir, array $files){
		if(!is_array($files) || is_null($files)) return [];
		$dir="ui/uploads/{$dir}";
// 		var_dump($files);exit;
		
		if (!is_dir($dir)){
			mkdir($dir, 0777, true);
			// chmod($dir, 0755);
		}
		$newFileNames=Array();
		foreach($files as $file){
			$fileNames=explode("/", $file);
			$fileName=$fileNames[count($fileNames)-1];
			$newName="{$dir}/$fileName";
			rename($file, $newName);
			$newFileNames[]=$newName;
		}
		return $newFileNames;
	}
	
	public function readFilesFromDirectory($directory){
	    $arrFiles = array();
	    if(is_dir($directory)){
	        return array_diff(scandir($directory), array('..', '.'));
	    }else{
	        return [];
	    }
	    
	}
	public function removeFromArray(array $items, $item){
         $findKey = array_search($item, $items);
         unset($items[$findKey]);
         return $items;
    }
    
    public function striPath($file){
        $fileName=explode('/', $file);
        $file=$fileName[count($fileName)-1];
        return $file;
    }
}
