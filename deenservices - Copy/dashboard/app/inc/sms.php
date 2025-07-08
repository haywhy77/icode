<?php
class sms {

    public $message;
    public $recipients;
    public $sender;
    private $provider;

    function __construct(array $account) {
		if(is_null($account) || empty($account)){
			return "Invalid account details";
		}
        $this->provider = $account;
		$response=self::login();
    }
	//function to set parameters
	private static function getProvider() {
		//$account= array('account' => 'adewumiadedeji27@gmail.com','sub_account'=>'RUGIPO', 'password' => '1234uz', 'url' => 'http://www.smslive247.com', 'scriptpath'=>'/http/index.aspx','sender'=>'DAVET Portal');
		//$account['sessionID']=$this->login($account);
        return $account;
    }
	//function to login into webservice
	public function login(){
		$url=$this->provider['url'].$this->provider['scriptpath'].'?cmd=login&owneremail='.$this->provider['account'].'&subacct='.$this->provider['sub_account'].'&subacctpwd='.$this->provider['password'];
		$response=self::SendHTTP($url);
		//var_dump($response);exit;
		if(isset($response['error']) && $response['error'] ==1){
			return false;
		}else{
			$resp=explode(':',$response['msg']);
			//echo trim($resp[1]);exit;
			$this->provider['sessionID']=trim($resp[1]);
			return true;
		}
	}
	//function to send sms
    public function sendMSG($message = '', $recipients = '') {
		if(isset($this->provider['sessionID'])){
			$url = $this->provider['url'].$this->provider['scriptpath'];
			$username = $this->provider['account'];
			$sub_account=$this->provider['sub_account'];
			$userpassword = $this->provider['password'];
			$sendername = $this->provider['sender'];
			$session_id=$this->provider['sessionID'];
			$flash = 1;
			$return_val = '';
			$country_code = '234';
			$arr_recipient = $recipients;
			$count = count((Array)$arr_recipient);
			$msg_ids = array();
			$generated_id = uniqid('int_', false);
			$generated_id = substr($generated_id, 0, 30);
			$recipients = '';
	
			for ($i = 0; $i < $count; $i++) {
				$mobilenumber = trim($arr_recipient[$i]);
				if (substr($mobilenumber, 0, 1) == '0'){
					$mobilenumber = $country_code . substr($mobilenumber, 1);
				}elseif (substr($mobilenumber, 0, 1) == '+'){
					$mobilenumber = substr($mobilenumber, 1);
				}
				$recipients .=  $mobilenumber.",";
				$msg_ids[$mobilenumber] = $generated_id . '_' . $i;
			}
			if($recipients==''){
				return '';
			}
			$recipients=rtrim($recipients,',');
			//var_dump($recipients);exit;
			//http://www.smslive247.com/http/index.aspx?cmd=sendmsg&sessionid=xxx&message=xxx&sender=xxx&sendto=xxx&msgtype=0 
			$url.='?cmd=sendmsg&sessionid='.$session_id.'&message='.$message.'&sender='.$sendername.'&sendto='.$recipients.'&msgtype=0';
			$dispatch_status=self::SendHTTP($url);
			var_dump($dispatch_status);exit;
			if ($dispatch_status['error']==1) {
				echo $dispatch_status['msg'];
			}else{
				return $dispatch_status['msg'];
			}
		}
        
    }
    public function sendQuickMSG($message = '', $recipients = '') {
		if(isset($this->provider['sessionID'])){
			$url = $this->provider['url'].$this->provider['scriptpath'];
			$username = $this->provider['account'];
			$sub_account=$this->provider['sub_account'];
			$userpassword = $this->provider['password'];
			$sendername = $this->provider['sender'];
			$session_id=$this->provider['sessionID'];
			$flash = 1;
			$return_val = '';
			$country_code = '234';
			$arr_recipient = $recipients;
			$count = count((Array)$arr_recipient);
			$msg_ids = array();
			$generated_id = uniqid('int_', false);
			$generated_id = substr($generated_id, 0, 30);
			$recipients = '';
	
			for ($i = 0; $i < $count; $i++) {
				$mobilenumber = trim($arr_recipient[$i]);
				if (substr($mobilenumber, 0, 1) == '0'){
					$mobilenumber = $country_code . substr($mobilenumber, 1);
				}elseif (substr($mobilenumber, 0, 1) == '+'){
					$mobilenumber = substr($mobilenumber, 1);
				}
				$recipients .=  $mobilenumber.",";
				$msg_ids[$mobilenumber] = $generated_id . '_' . $i;
			}
			if($recipients==''){
				return '';
			}
			$recipients=rtrim($recipients,',');
			//var_dump($recipients);exit;
			//http://www.smslive247.com/http/index.aspx?cmd=sendquickmsg&owneremail=xxx&subacct=xxx&subacctpwd=xxx&message=xxx&sender=xxx&sendto=xxx&msgtype=0 
			$url.='?cmd=sendquickmsg&owneremail='.$username.'&subacct='.$sub_account.'&subacctpwd='.$userpassword.'&message='.$message.'&sender='.$sendername.'&sendto='.$recipients.'&msgtype=0';
			//echo $url;exit;
			$dispatch_status=self::SendHTTP($url);
			var_dump($dispatch_status);exit;
			if ($dispatch_status['error']==1) {
				echo $dispatch_status['msg'];
			}else{
				return $dispatch_status['msg'];
			}
		}
        
    }
    public static function getBalance(){
		if(isset($this->provider['sessionID'])){
			$url = $this->provider['url'].$this->provider['scriptpath'];
			$username = $this->provider['account'];
			$sub_account=$this->provider['sub_acount'];
			$userpassword = $this->provider['password'];
			$sendername = $this->provider['sender'];
			$session_id=$this->provider['sessionID'];
			//http://www.smslive247.com/http/index.aspx?cmd=querybalance&sessionid=xxx     }
			$url.="?cmd=querybalance&sessionid=".$session_id;
			$dispatch_status=self::SendHTTP($url);
			if ($dispatch_status['error']==1) {
				echo $dispatch_status['msg'];
			}else{
				return $dispatch_status['msg'];
			}
		}
	}
	public function getMSGCharge($id){
		if(isset($this->provider['sessionID'])){
			//http://www.smslive247.com/http/index.aspx?cmd=querymsgcharge&sessionid=xxx&messageid=xxx 
			$url = $this->provider['url'].$this->provider['scriptpath'];
			$username = $this->provider['account'];
			$sub_account=$this->provider['sub_acount'];
			$userpassword = $this->provider['password'];
			$sendername = $this->provider['sender'];
			$session_id=$this->provider['sessionID'];
			$url.="?cmd=querymsgcharge&sessionid=".$session_id."&messageid=".$id;
			$dispatch_status=self::SendHTTP($url);
			if ($dispatch_status['error']==1) {
				echo $dispatch_status['msg'];
			}else{
				return $dispatch_status['msg'];
			}
		}
	}
    public function getStatusMessage($id) {
		if(isset($this->provider['sessionID'])){
			//http://www.smslive247.com/http/index.aspx?cmd=querymsgstaus&sessionid=xxx&messageid=xxx 
			$url = $this->provider['url'].$this->provider['scriptpath'];
			$username = $this->provider['account'];
			$sub_account=$this->provider['sub_acount'];
			$userpassword = $this->provider['password'];
			$sendername = $this->provider['sender'];
			$session_id=$this->provider['sessionID'];
			$url.="?cmd=querymsgstaus&sessionid=".$session_id."&messageid=".$id;
			$dispatch_status=self::SendHTTP($url);
			if ($dispatch_status['error']==1) {
				echo $dispatch_status['msg'];
			}else{
				return $dispatch_status['msg'];
			}
		}
    }
	public function rechargeSMS($code){
		if(isset($this->provider['sessionID'])){
			//http://www.smslive247.com/http/index.aspx?cmd=recharge&sessionid=xxx&rcode=xxx 
			$url = $this->provider['url'].$this->provider['scriptpath'];
			$username = $this->provider['account'];
			$sub_account=$this->provider['sub_acount'];
			$userpassword = $this->provider['password'];
			$sendername = $this->provider['sender'];
			$session_id=$this->provider['sessionID'];
			$url.="?cmd=recharge&sessionid=".$session_id."&rcode=".$code;
			$dispatch_status=self::SendHTTP($url);
			if ($dispatch_status['error']==1) {
				echo $dispatch_status['msg'];
			}else{
				return $dispatch_status['msg'];
			}
		}
	}
	public function SendHTTP($url){
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_HEADER, TRUE); 
		//curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		
		$response = curl_exec($ch); //run the whole process and return the response 
		if(curl_errno($ch) ) {
			$result = 'cURL ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
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