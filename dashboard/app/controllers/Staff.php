<?php

// use Dompdf\Dompdf;

class Staff extends Controller{
    public function index(\Base $f3){
        // var_dump($action);exit;
        parse_str($f3->get("QUERY"), $params); 
        //var_dump($params);exit;
        
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 20;
        $sql="select staff.*, (select clients.names from clients where clients.id=staff.current_client) as client from staff where 1=1";
        if(array_key_exists('type', $params)){
            $sql.=" and status='{$params['type']}'";
        }else{
            $sql.=" and status!='FIRED'";
        }
        
        if(array_key_exists('name', $params)){
            $sql.=" and names like '%{$params['name']}%'";
        }
        // echo $sql;exit;
        $clients=$this->db->DBQuery($sql)->paginates($page, $limit);
      
        
        $f3->set('staff',$clients);
        $f3->set('page',['title'=>'Staff', 'desc'=>'']);
        $f3->set('template','pages/staff/manage.htm');
    }

    public function detail(\Base $f3, $params){
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 20;
        $id=$params['id'];
        $clients=$this->db->DBQuery("select clients.* from clients;")->all();
        // var_dump($clients);exit;
        $f3->set('clients',$clients);
        $staff=$this->db->DBQuery("select staff.*, (select clients.names from clients where clients.id=staff.current_client) as client from staff where id='$id';")->first();
        //var_dump($staff);exit;
        $cand_passport=$this->striPath($staff->passport);
        $documents=$this->readFilesFromDirectory("ui/uploads/".$staff->upload_dir);
        $documents=$this->removeFromArray($documents, $cand_passport);
        // var_dump($documents);exit;
        if($documents){
            $files=[];
            foreach($documents as $file){
                $files[]="ui/uploads/{$staff->upload_dir}/{$file}";
            }
            // $staff->documents=array_merge($staff->documents, $files);
            $staff->documents=array_unique($files);
        }else{
            $staff->documents=[];
        }
        // var_dump($staff->documents);exit;
        
        
        if(!empty($staff->skills)){
            $staff->skills=explode(", ", $staff->skills);
        }else{
            $staff->skills=[];
        }
        
        $f3->set('staff',$staff);
        
        $companies=$this->db->DBQuery("select clients.*, clients_user.status as c_status from clients, clients_user where clients.id=clients_user.client_id and clients_user.user_id='$id' order by clients_user.id desc")->paginates($page, $limit);
        // var_dump($companies);exit;
        $f3->set("companies", $companies);
        $f3->set("roles", $this->db->DBQuery("select * from job_fields;")->all());
        $status=explode(",", "ACTIVE,DEACTIVATED,SUSPENDED,DELETED,FIRED");
        $status=array_diff($status, [$staff->status]);
        // var_dump($status);exit;
        $f3->set('statuses', $status);
        $f3->set('action', 'default');
        $f3->set('page',['title'=>'Staff', 'desc'=>'']);
        $f3->set('template','pages/staff/detail.htm');
    }

    public function performActions(\Base $f3){
        // var_dump($f3->get('POST'));exit;

        $client_user_id=stripslashes(strip_tags($this->f3->get('POST.client_user_id')));
        $todo=stripslashes(strip_tags($this->f3->get('POST.client_action')));
        $row=$this->db->DBUpdate("staff", array("status"=>$todo), array("id"=>$client_user_id), array());
        // var_dump($row);exit;
        $f3->reroute("/staff/{$client_user_id}");
    }

    public function updateProfile(\Base $f3, $params){
        // var_dump($f3->get('POST'));exit;
        $post=$f3->get('POST');
        $st=$this->db->DBUpdate("staff", array("profile_card"=>json_encode($post)), array("id"=>$post['id']), array());
        if($st->resp){
            $f3->reroute("/staff/profile-card/profile/{$post['id']}");
        }else{
            \Flash::instance()->addMessage($st->message, 'danger');
            $f3->reroute("/staff/profile-card/edit/{$post['id']}");
        }
    }
    public function profile(\Base $f3, $params){
        $candidate=$this->db->DBSelect("staff", array('id'=>$params['id']))->first();
        
        $skills=$candidate->skills ? explode(",", $candidate->skills):[];
        $candidate->skills=$skills;
        if(empty($candidate->passport)){
            $candidate->passport="ui/img/passport.png";
        }

        $profile=(Array) $candidate;
        if(array_key_exists('profile_card', $profile) && !empty($profile['profile_card'])){
            $profile['profile_card']=json_decode($profile['profile_card'], true);
            $candidate->profile_card=$profile['profile_card'];
        }else{
            $candidate->profile_card=[];
        }
        
        $documents=$this->readFilesFromDirectory("ui/uploads/".$candidate->upload_dir);
        // var_dump($staff->documents);exit;
        
        if($candidate->documents){
            $candidate->documents=explode(", ", $candidate->documents);
        }else{
            $candidate->documents=[];
        }
        if($documents){
            $candidate->documents=array_merge($candidate->documents, $documents);
        }
        
        
        $f3->set('profile',$candidate);
        $f3->set('staff',$candidate);

        $f3->set("employment", $this->db->DBSelect("staff_employment_history", array("staff_id"=>$params['id']))->all());
        $f3->set("qualification", $this->db->DBSelect("staff_qualification_history", array("staff_id"=>$params['id']))->all());
        $f3->set("profession", $this->db->DBSelect("staff_profession_history", array("staff_id"=>$params['id']))->all());
        $f3->set("gaps", $this->db->DBSelect("staff_gaps_employment", array("staff_id"=>$params['id']))->all());
        $f3->set("police", $this->db->DBSelect("staff_police_check", array("staff_id"=>$params['id']))->all());
        //get staff
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 50;
        
        
        $card=$candidate->profile_card;
        // var_dump($card);exit;
        // echo $candidate->profile_card['training_16'];exit;
        $f3->set("trainings", [
            ["name"=>'NVQ 2 or equivalent', "value"=>array_key_exists('training_1', $card)?'Yes✓':'No', "date"=>array_key_exists('training_1_date', $card)?$card['training_1_date']:''],
            ["name"=>'NVQ 3 or equivalent ', "value"=>array_key_exists('training_2', $card)?'Yes✓':'No', "date"=>array_key_exists('training_2_date', $card)?$card['training_2_date']:''],
            ["name"=>'Manual Handling Theory', "value"=>array_key_exists('training_3', $card)?'Yes✓':'No', "date"=>array_key_exists('training_3_date', $card)?$card['training_3_date']:''],
            ["name"=>'Food Hygiene', "value"=>array_key_exists('training_4', $card)?'Yes✓':'No', "date"=>array_key_exists('training_4_date', $card)?$card['training_4_date']:''],
            ["name"=>'Health & Safety', "value"=>array_key_exists('training_5', $card)?'Yes✓':'No', "date"=>array_key_exists('training_5_date', $card)?$card['training_5_date']:''],
            ["name"=>'Fire Safety', "value"=>array_key_exists('training_6', $card)?'Yes✓':'No', "date"=>array_key_exists('training_6_date', $card)?$card['training_6_date']:''],
            ["name"=>'Compassion and Care', "value"=>array_key_exists('training_7', $card)?'Yes✓':'No', "date"=>array_key_exists('training_7_date', $card)?$card['training_7_date']:''],
            ["name"=>'Infection Control', "value"=>array_key_exists('training_8', $card)?'Yes✓':'No', "date"=>array_key_exists('training_8_date', $card)?$card['training_8_date']:''],
            ["name"=>'GDPR', "value"=>array_key_exists('training_9', $card)?'Yes✓':'No', "date"=>array_key_exists('training_9_date', $card)?$card['training_9_date']:''],
            ["name"=>'Equality, Diversity and Human Rights', "value"=>array_key_exists('training_10', $card)?'Yes✓':'No', "date"=>array_key_exists('training_10_date', $card)?$card['training_10_date']:''],
            ["name"=>'Epilepsy awareness', "value"=>array_key_exists('training_11', $card)?'Yes✓':'No', "date"=>array_key_exists('training_11_date', $card)?$card['training_11_date']:''],
            ["name"=>'Tier 1 Training - Induction to Care and 1st Med', "value"=>array_key_exists('training_12', $card)?'Yes✓':'No', "date"=>array_key_exists('training_12_date', $card)?$card['training_12_date']:''],
            ["name"=>'Tier 2 - e learning and Mulberry Training', "value"=>array_key_exists('training_13', $card)?'Yes✓':'No', "date"=>array_key_exists('training_13_date', $card)?$card['training_13_date']:''],
            ["name"=>'Tier 3 - Practical - Training Works (practical)', "value"=>array_key_exists('training_14', $card)?'Yes✓':'No', "date"=>array_key_exists('training_14_date', $card)?$card['training_14_date']:''],
            ["name"=>'Safeguarding of Vulnerable Adults Training (SOVA) and annual refresher mandatory', "value"=>array_key_exists('training_15', $card)?'Yes✓':'No', "date"=>array_key_exists('training_15_date', $card)?$card['training_15_date']:''],
            ["name"=>'Medication Administration Training', "value"=>array_key_exists('training_16', $card)?'Yes✓':'No', "date"=>array_key_exists('training_16_date', $card)?$card['training_16_date']:'']
        ]);

        $f3->set('maskString',function($str){
            return $this->stringToSecret($str);
        });
        
        if($params['action']=='edit'){
            
            $f3->set('action', 'profile');
            $f3->set('page',['title'=>$candidate->names, 'desc'=>'']);
            $f3->set('template','pages/staff/detail.htm');
        }else{
            echo Template::instance()->render('profile.htm');die();
        }
        
        
    }

    public function remove(\Base $f3, $params){
        
        if($params['delete']=='delete'){
            $name=str_replace("___","/", $params['name']).".{$params['ext']}";
            if(file_exists($name)){
                @unlink($name);
            }
            $app=$this->db->DBSelect("staff", array("id"=>$params["id"]), array())->first();
            $documents=explode(', ', $app->documents);
            $newDocument=array();
            $counter=0;
            for($i=0; $i<count($documents); $i++){
                if($documents[$i]==$name) continue;
                $newDocument[$counter] = $documents[$i];
                ++$counter;
            }

            // var_dump($newDocument);exit;
            $otherDocs=implode(', ', $newDocument); //str_replace("$name, ", '', $app->documents);
            // echo $otherDocs;exit;
            $row=$this->db->DBUpdate("staff", array("documents"=>rtrim(ltrim($otherDocs, ', '), ', ')), array("id"=>$params["id"]), array());
            // if(!$row->resp) {
            //     \Flash::instance()->addMessage($row->message, 'danger');
            // }else{
            //     unlink($name);
            //     \Flash::instance()->addMessage('File removed successfully.', 'success');
            // } 
            \Flash::instance()->addMessage('File removed successfully.', 'success');
        }
        $f3->reroute("/staff/{$params["id"]}/upload");
    }

    public function attach(\Base $f3, $params){
        $app=$this->db->DBSelect("staff", array("id"=>$params["id"]), array())->first();
        $dir=$app->upload_dir;
        if ($f3->exists('POST') && !empty($f3->get('POST')) ) {
            
            $web=\UploadFile::instance();
		    $files=$web->upload('', true, true);
            // var_dump($files);exit;
            if(count((Array)$files) >0 && is_array($files)){
                $newFiles=array();
                foreach($files as $k=>$v){
                    foreach($v as $i=>$j){
                        $newFiles[]=$j['url'];
                    }
                }
                // var_dump($newFiles);exit;
                $files=$this->relocateFiles($dir, $newFiles);
            }
            
            $names=implode(", ", $files);
            
            // echo $names;exit;
            $otherDocs=$app->documents;
            $otherDocs="{$otherDocs}, {$names}";
            $row=$this->db->DBUpdate("staff", array("documents"=>rtrim(ltrim($otherDocs, ', '), ', ')), array("id"=>$params["id"]), array());
            if(!$row->resp) {
                \Flash::instance()->addMessage($row->message, 'danger');
            }else{
                \Flash::instance()->addMessage('Your documents uploaded successfully.', 'success');
            } 
            
        }
        $cand_passport=$this->striPath($app->passport);
        $documents=$this->readFilesFromDirectory("ui/uploads/".$dir);
        $documents=$this->removeFromArray($documents, $cand_passport);
        
        if($documents){
            $files=[];
            foreach($documents as $file){
                $files[]="ui/uploads/{$app->upload_dir}/{$file}";
            }
            // $staff->documents=array_merge($staff->documents, $files);
            $app->other_documents=array_unique($files);
        }else{
            $app->other_documents=[];
        }
        // var_dump($app);exit;
        
        $f3->set('action', "staff/{$params['id']}/upload");
        $f3->set('actionDelete', "staff/{$params['id']}/upload/delete");
        $f3->set('job', $app);
        $f3->set('template','forms/upload.html');
    }

    public function downloadOther(\Base $f3, $params){

        $user=$this->db->DBSelect("staff", array("id"=>$params["id"]))->first();
        $employment=$this->db->DBSelect("staff_employment_history", array("staff_id"=>$params["id"]))->all();
        $qualification=$this->db->DBSelect("staff_qualification_history", array("staff_id"=>$params["id"]))->all();
        $profession=$this->db->DBSelect("staff_profession_history", array("staff_id"=>$params["id"]))->all();
        $gap=$this->db->DBSelect("staff_gaps_employment", array("staff_id"=>$params["id"]))->all();
        $police=$this->db->DBSelect("staff_police_check", array("staff_id"=>$params["id"]))->all();

        // var_dump($gap);exit;
        $user->employment=$employment;
        $user->qualification=$qualification;
        $user->profession=$profession;
        $user->gap=$gap;
        $user->police=$police;

        $names=explode(' ', $user->names);
        $user->firstName=$names[0];
        $user->lastName=count($names)>1?$names[1]:"";
        $user->middleName=count($names)>2?$names[2]:"";

        // echo $user->passport;exit;
        // echo file_exists($user->passport);exit;
        if(!empty($user->passport) && file_exists($user->passport)){
            $ext=strtolower(substr(strrchr($user->passport,'.'), 1));
            $imgbinary = file_get_contents($user->passport);
			$logo= 'data:image/' . $ext . ';base64,' . base64_encode($imgbinary);
            $user->passport=$logo;
        }else{
            $file='ui/img/passport.png';
            $ext=strtolower(substr(strrchr($file,'.'), 1));
            $imgbinary = file_get_contents($file);
			$logo= 'data:image/' . $ext . ';base64,' . base64_encode($imgbinary);
            $user->passport=$logo;
        }

        $user=(Array)$user;
        // var_dump($user);exit;

        $options = new Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf\Dompdf($options);
        //$dompdf->setBasePath($url);
        $f3->set("data", $user);
       
        $content = Template::instance()->render( "email/staff_doc.html" );
        $dompdf->loadHtml($content);
        $dompdf->setPaper('a4', 'portrait');
        //$dompdf->setPaper( array(0,0,822,848), 'portrait' );
        $dompdf->render();
        $output = $dompdf->output();
        $dir="ui/uploads/{$user['upload_dir']}";
        if (!is_dir($dir)){
			mkdir($dir, 0777, true);
			// chmod($dir, 0755);
		}
        $filename="{$dir}/document.pdf";
        file_put_contents($filename, $output);

        if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}
		// get the file mime type using the file extension
		$ext=strtolower(substr(strrchr($filename,'.'), 1));
		switch($ext) {
			case 'pdf': $mime = 'application/pdf'; break;
			case 'zip': $mime = 'application/zip'; break;
			case 'jpeg':
			case 'jpg': $mime = 'image/jpg'; break;
			case'xlsx':
			case'xls': $mime='application/vnd.ms-excel';break;
			default: $mime = 'application/force-download';
		}
		
		header('Pragma: public'); 	// required
		header('Expires: 0');		// no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($filename)).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: '. $mime);
		header('Content-Disposition: attachment; filename="'.basename($filename).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($filename));	// provide file size
		header('Connection: close');
		//echo @exec('hostname -f');
		//echo $filename;exit;
		ob_clean();
    	flush();
		//echo $filename;exit;
		readfile($filename);		// push it out
		unlink($filename);
        // $f3->reroute("/staff/{$params['id']}");
        echo $filename; exit;
    }
    
    public function status(\Base $f3, $params){
        $staff=$this->db->DBSelect("staff", array('id'=>$params['id']))->first();
        if($staff){
            $this->db->DBUpdate("staff", array("status"=>$params['action']), array('id'=>$params['id']), array());
        }
        $f3->reroute("/staff/{$params['id']}");
    }
}