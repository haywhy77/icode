<?php
class UploadFile extends Web{
	function upload($filename=NULL,$overwrite=FALSE,$slug=TRUE) {
		$fw=Base::instance();
		$dir=$fw->get('UPLOADS');
		if (!is_dir($dir))
			mkdir($dir,Base::MODE,TRUE);
		if ($fw->get('VERB')=='PUT') {
			$fw->write($dir.basename($fw->get('URI')),$fw->get('BODY'));
			return TRUE;
		}
		
		$out=array();
		$count=0;
		// var_dump($_FILES);exit;
		$files=[];
		foreach ($_FILES as $key=>$file) {
			// echo $key;exit;
			
			
			
			if (empty($file['name']))
				continue;
			// var_dump($file);exit;
			
			if(is_array($file['name']) && count($file['name'])>0){
				$keys=array_keys($file);
				$newFiles=[];
				for($i=0; $i<count($file['name']); $i++){
					
					foreach($keys as $k){
						$files[$k]=$file[$k][$i];
					}
					$newFiles[]=$files;
				}
				$files=$newFiles;
			}else{
				$files[0]=$file;
			}

            // var_dump($files);exit;
			
			foreach($files as $k=>$fl){
				$base=basename($fl['name']);
				
				if($filename){
					$fl['name']=$filename.'_'.$count.".".strtolower(substr(strrchr($base, "."), 1));  
				}else{
					$fl['name']=($slug && preg_match('/(.+?)(\.\w+)?$/',$base,$parts)?($this->slug($parts[1]).(isset($parts[2])?$parts[2]:'')):$base);
				}
				$fl['name']=$dir.$fl['name'];
				$type=explode('/',$fl['type']);
				
				$ext=ltrim(strrchr($fl['name'],'.'),'.');
				$fullPath=$dir.explode('.',$fl['name'])[0].'.'.$ext;
				//echo $fullPath;exit;
				
				//var_dump( $file);exit;
				
				if($file !==false){
					$uploaded_file= (!$fl['error'] && is_uploaded_file($fl['tmp_name']) && (!file_exists($fl['name']) || $overwrite) && move_uploaded_file($fl['tmp_name'],$fl['name']))?$fl['name']:$fl['error'];
						
					if($uploaded_file){
						$out[$key][$count]=array('ext'=>$ext,'type'=>$type[0],'url'=>$fl['name'], 'name'=>$key, 'full_path'=>$fullPath);
					}else{$out[$key][$count]=null;
						$out[$key][$count]=null;
						@unlink($fl['tmp_name'][0]);
					}
				}else{
					$out[$key][$count]=null;
				}
				++$count;
			}
		}
		
		return $out;
	}
	function resize($source_image,$destination,$tn_w = 100,$tn_h = 100,$quality = 80,$wmsource = false) {
		//echo $destination;exit;
		#find out what type of image this is
		$info = getimagesize($source_image);
		$imgtype = image_type_to_mime_type($info[2]);
		#assuming the mime type is correct
		switch ($imgtype) {
			case 'image/jpeg':
				$source = imagecreatefromjpeg($source_image);
			break;
			case 'image/gif':
				$source = imagecreatefromgif($source_image);
			break;
			case 'image/png':
				$source = imagecreatefrompng($source_image);
			break;
			default:
				die('Invalid image type.');
		}
		#Figure out the dimensions of the image and the dimensions of the desired thumbnail
		$src_w = imagesx($source);
		$src_h = imagesy($source);
		$src_ratio = $src_w/$src_h;
		#Do some math to figure out which way we will need to crop the image
		#to get it proportional to the new size, then crop or adjust as needed
		if ($tn_w/$tn_h > $src_ratio) {
			$new_h = $tn_w/$src_ratio;
			$new_w = $tn_w;
		} else {
			$new_w = $tn_h*$src_ratio;
			$new_h = $tn_h;
		}
		$x_mid = $new_w/2;
		$y_mid = $new_h/2;
		$newpic = imagecreatetruecolor(round($new_w), round($new_h));
		imagecopyresampled($newpic, $source, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
		$final = imagecreatetruecolor($tn_w, $tn_h);
		imagecopyresampled($final, $newpic, 0, 0, ($x_mid-($tn_w/2)), ($y_mid-($tn_h/2)), $tn_w, $tn_h, $tn_w, $tn_h);
		#if we need to add a watermark
		if($wmsource) {
			#find out what type of image the watermark is
			$info = getimagesize($wmsource);
			$imgtype = image_type_to_mime_type($info[2]);
			#assuming the mime type is correct
			switch ($imgtype) {
				case 'image/jpeg':
					$watermark = imagecreatefromjpeg($wmsource);
				break;
				case 'image/gif':
					$watermark = imagecreatefromgif($wmsource);
				break;
				case 'image/png':
					$watermark = imagecreatefrompng($wmsource);
				break;
				default:
					die('Invalid watermark type.');
			}
			#if we are adding a watermark, figure out the size of the watermark
			#and then place the watermark image on the bottom right of the image
			$wm_w = imagesx($watermark);
			$wm_h = imagesy($watermark);
		   	//imagecopy($final, $watermark, $x_pos, $y_pos, 0, 0, $tn_w, $tn_h);
			imagecopy($final, $watermark, imagesx($final) - $wm_w - 70, imagesy($final) - $wm_h - 40, 0, 0, imagesx($watermark), imagesy($watermark));
		}
		if(Imagejpeg($final,$destination,$quality)) {
			return true;
		}
		return false;
	}
}
?>