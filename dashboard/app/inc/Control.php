<?php
class Control{
	protected $chars=array(' ','/','\\','+');
	function getBase($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
			
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
			
            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
			
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }
		//echo $base_url;exit;
        return $base_url;
	}
	function get_current_url(){
		//var_dump($_SERVER['REQUEST_URI']);exit;
		return "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		//return $_SERVER['HTTP_REFERER']; 
	}
	function createLink($link=''){
		//echo $this->getBase();exit;
		return $this->getBase().$link;
	}
	
	function SendHTTP($url){
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_HEADER, TRUE); 
		//curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		
		$response = curl_exec($ch); //run the whole process and return the response 
		if(curl_errno($ch) ) {
			$result =array('error'=>1,'msg'=>'cURL ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch)) ;
		} else {
			$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			switch($returnCode){
				case 200:
					$resp = explode("\n\r\n", $response);
					$result1 = explode("\n", $resp[0]);
					list($http,$code,$ms) = explode(" ", $result1[0]);
					$result = array('error'=>0,'code'=>$ms,'msg'=>$resp[1]);
					//$result=$ms.":".$resp[1];//$ms;
				break;
				default:
					$result = array('error'=>1,'msg'=>$returnCode);
				break;
			}
		}
		curl_close($ch);
		return $result;	
	}
}
?>