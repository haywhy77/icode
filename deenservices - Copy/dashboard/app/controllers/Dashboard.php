<?php
class Dashboard extends Controller{
    public function index(\Base $f3){
        $account=$f3->get('SESSION.account');
        $id=$f3->get('SESSION.user_id');
        // echo $account;exit;
        switch($account){
            case'ADMIN':
                $user=$this->db->DBSelect("users", array("id"=>$f3->get('SESSION.user_id')))->first();
                $f3->set('USER',$user);
                //update `candidates` set created_at=DATE_FORMAT(created_at,'2022-%m-%d %T') where id>15 and id<12;
                $datas=$this->db->DBQuery("SELECT YEAR(c.created_at)as year, COUNT(DISTINCT id) as nos FROM candidates AS c  GROUP BY YEAR(c.created_at);")->all();
                $barchart=[];
                $total=0;
                if($datas){
                    foreach($datas as $data){
                        $barchart[$data['year']]=$data['nos'];
                        $total +=$data['nos'];
                    };    
                }
                
                // var_dump($barchart);exit;
                $f3->set('barchartYear',rtrim(implode(",", array_keys($barchart)), ","));
                $f3->set('barchartValue', rtrim(implode(",", array_values($barchart)), ","));
                $f3->set('barchart', $barchart);
                $f3->set("extra", [
                    "js"=>[
                        "js/vendors/chartjs/Chart.bundle.min.js",
                        "js/vendors/chartjs/utils.js"
                    ]
                ]);
                $f3->set('template','pages/dashboard/admin.htm');
            break;
            case'CLIENT':
                $counters=$this->db->DBQuery("SELECT count(id) as total, sum(IF(isApprove = 'NO', 1, 0)) as pending, sum(IF(isApprove = 'YES', 1, 0)) as success, sum(IF(isApprove = 'REJECTED', 1, 0)) as rejected FROM jobs where employer_name='$id'")->first();
                $f3->set('summary',$counters);

                $counter=$this->db->DBQuery("SELECT count(id) as total, sum(IF(status = 'DEACTIVATED', 1, 0)) as deactivated, sum(IF(status = 'ACTIVE', 1, 0)) as active, sum(IF(status = 'WITHDRAWN', 1, 0)) as withdrawn, sum(IF(status = 'TRANSFERRED', 1, 0)) as transfered FROM clients_user where client_id='$id'")->first();
                $f3->set('staff',$counter);


                $user=$this->db->DBSelect("clients", array("id"=>$f3->get('SESSION.user_id')))->first();
                $f3->set('USER',$user);
                $f3->set('template','pages/dashboard/client.htm');
            break;
            default:
                
                $user=$this->db->DBSelect("candidates", array("id"=>$f3->get('SESSION.user_id')))->first();
                $f3->set('USER',$user);
                if($user->isVerify=='NOTVERIFIED'){
                    $f3->reroute("/account/".$user->id);
                }else{
                    
                    $counters=$this->db->DBQuery("SELECT count(id) as total, sum(IF(status = 'PENDING', 1, 0)) as pending, sum(IF(status = 'APPROVED', 1, 0)) as success, sum(IF(status = 'REJECTED', 1, 0)) as rejected, sum(IF(status = 'INTERVIEW', 1, 0)) as interview FROM applications where candidate_id='$id'")->first();
                    // var_dump($counters);exit;
                    $f3->set('summary',$counters);

                    
                    $track=$this->db->DBQuery("select application_tracker.*, jobs.job_title, jobs.salary_range, job_locations.name as location from jobs, applications, application_tracker, job_locations where application_tracker.application_id=applications.id and applications.job_id=jobs.id and jobs.location=job_locations.id and application_tracker.candidate_id='$id'")->paginates(1, 20);
                    // var_dump($track);exit;
                    $f3->set('tracks',$track);

                    $skills=explode(",", $user->skills);
                    $skillSet='';
                    foreach($skills as $skill){
                        $skillSet.="'".$skill."',";
                    }
                    $skills=rtrim($skillSet, ',');
                    
                    $jobs=$this->db->DBQuery("select jobs.*, job_locations.name as location, job_fields.name as role from jobs, job_locations, job_fields where jobs.location=job_locations.id and jobs.job_field=job_fields.id and job_fields.name in ($skills)")->paginates(1, 20);
                    $f3->set('jobs',$jobs);
                    $f3->set('template','pages/dashboard/candidate.htm');
                }
                
        }
    }

    public function candidate(\Base $f3, $params){
        $id=$params["id"];
        
        $user=$this->db->DBSelect("candidates", array("id"=>$id))->first();
        // var_dump($user);exit;
        // echo $user->id;exit;
        if(!$user){
            \Flash::instance()->addMessage("User Account Not Found.", 'danger');
        }else{
            
            if ($f3->exists('POST.name') || $f3->exists('POST.gender') || $f3->exists('POST.phone') || $f3->exists('POST.location') || $f3->exists('POST.about_me') || $f3->exists('POST.linkedin') || $f3->exists('POST.portfolio') && $f3->exists('POST.skills')) {
                sleep(3); // login should take a while to kick-ass brute force attacks
                // var_dump($f3->get('POST'));exit;
                
                $name=stripslashes(strip_tags($f3->get('POST.name')));
                $gender=stripslashes(strip_tags($f3->get('POST.gender')));
                $phone=stripslashes(strip_tags($f3->get('POST.phone')));
                $location=stripslashes(strip_tags($f3->get('POST.location')));
                $about_me=stripslashes(strip_tags($f3->get('POST.about_me')));
                $linkedin=stripslashes(strip_tags($f3->get('POST.linkedin')));
                $portfolio=stripslashes(strip_tags($this->f3->get('POST.portfolio')));
                $skills=$f3->get('POST.skills');
                
                $web=\UploadFile::instance();
    		    $files=$web->upload('', true, true);
                // var_dump($files);exit;
                $newFile=array();
                $dir=$user->id;
                if(count((Array)$files) >0 && is_array($files)){
                    
                    foreach($files as $key=>$file){
                        
                        $fileNames=array();
                        foreach($file as $k=>$v){
                            $fileNames[]=$v['url'];
                        }
                        // var_dump($fileNames);exit;
                        $newFile[$key]=$this->relocateFiles($dir, $fileNames)[0];
                    }
                }
                // var_dump($newFile);exit;
                // //upload passport
                // $overwrite = true; // set to true, to overwrite an existing file; Default: false
                // $slug = true; // rename file to filesystem-friendly version
                // $web = \Web::instance();
                // $pfiles = $web->receive(function($file,$formFieldName){
                //         // $file['name'] already contains the slugged name now

                //         // maybe you want to check the file size
                //         if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
                //             return false; // this file is not valid, return false will skip moving it

                //         // everything went fine, hurray!
                //         return true; // allows the file to be moved from php tmp dir to your defined upload dir
                //     },
                //     $overwrite,
                //     $slug
                // );
                // var_dump($pfiles);exit;
                // $files=array_keys($pfiles);
                // $dir=$user->id;
                // $files=$this->relocateFiles($dir, $files);
                // $files=array_values($files);
                // var_dump($files);exit;
                
                $record=[
                    "name"=>$name,
                    "gender"=>$gender,
                    'phone'=>$phone,
                    "location"=>$location,
                    "about_me"=>$about_me,
                    'linkedin_url'=>$linkedin,
                    "portfolio_url"=>$portfolio,
                    "skills"=>is_array($skills) && count($skills)>0?implode(",", $skills):"",
                    "isVerify"=>'VERIFIED',
                    "status"=>'ACTIVE'
                ];
                if(array_key_exists('passport', $newFile)){
                    $record['passport']=$newFile['passport'];
                }
                if(array_key_exists('resume', $newFile)){
                    $record['default_resume']=$newFile['resume'];
                }
                // var_dump($record);exit;
                $row=$this->db->DBUpdate("candidates", $record, array("id"=>$user->id), array());
                // var_dump($row);exit;
                if(!$row->resp) {
                    \Flash::instance()->addMessage($row->message, 'danger');
                }else{
                    $staff=$this->db->DBSelect("staff", array("email"=>$user->email), array())->first();
                    if($staff && array_key_exists('passport', $newFile)){
                        $this->db->DBUpdate("staff", array("passport"=>$record['passport']), array('id'=>$staff->id), array('passport'));
                    }
                    // \Flash::instance()->addMessage("Profile updated successfully...you can now continue to Overview", 'success');
                }
                // $f3->clear('POST');
                $f3->reroute('/job-listing');
            }
        }
        $skills=$this->db->DBSelect("skills", array())->all();
        $f3->set('skills',$skills);
        $profile=$this->db->DBSelect("candidates", array('id'=>$id))->first();
        // var_dump($profile);exit;
        $f3->set('profile',$profile);
        $f3->set("extra", [
            "js"=>[
                "js/vendors/select2/select2.min.js"
            ]
        ]);
        $f3->set('template','forms/candidate.html');
    }
}