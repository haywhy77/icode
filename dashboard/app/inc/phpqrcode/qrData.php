<?php
include_once"qrlib.php";
class qr_generate extends QRcode
{
	
	public $data=array();
	
	public function __construct($qrInfo)
	{
	   if(!is_array($qrInfo) || count((Array)$qrInfo)<1) die("Invalide content supply for encryption");
	   $this->data=$qrInfo;
	   //application number, names, admission, program, department,amount,transaction, passport,school
	}
	
	public function GenQr()
	{
		//var_dump($this->data);exit;
		// how to build raw content - QRCode with Business Card (VCard) + photo
		
		
		//echo $avatarJpegFileName;exit;
		//$tempImgDir = $this->f3->get('UPLOADS');
		//$tempQrDir = "app/passport/qrinfo/";
		
		// WARNING! here jpeg file is only 40x40, grayscale, 50% quality!
		// with bigger images it will simply be TOO MUCH DATA for QR Code to handle!
		//$avatarJpegFileName = $this->data['Passport'];;
		//var_dump($this->data);exit;
		//$codeContents  =$this->data['content'];
		//echo $codeContents;exit;
		// we building raw data
		
		//if($avatarJpegFileName !=''){
			 //$codeContents .= 'PHOTO;JPEG;ENCODING=BASE64:'.base64_encode(file_get_contents($avatarJpegFileName))."\n"; 
		//}
		//var_dump($codeContents);exit;
	
		
		// generating
		parent::png($this->data['content'], $this->data['uri'],  QR_ECLEVEL_L, 4);
		
		if(file_exists($this->data['uri'])){
			return array("status"=>true,'data'=>$this->data['uri']);
		}else{
			return array("status"=>false,'data'=>$this->data['uri']);
		}
	}
}
?>