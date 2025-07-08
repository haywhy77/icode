<?php
class Candidates extends Controller{
    // public function __construct()
    // {
    //     echo "deji in construct";exit;
    // }
    public function index(\Base $f3, $params){
        parse_str($f3->get("QUERY"), $param); 
        $page=array_key_exists('page', $param)?$param["page"] : 1;
        $limit=array_key_exists('limit', $param)?$param["limit"] : 20;
        $status=array_key_exists('status', $param)?$param["status"] : "";
        $duration=array_key_exists('duration', $param)?$param["duration"] : "";
        $sql="select * from candidates where 1=1";
        
        if(!empty($status)){
            $sql.=" and applications.status='$status'";
        }
        if(!empty($duration)){
            if($duration==1){
                $sql.=" and MONTH(applications.created_at)=MONTH(CURRENT_DATE())";
            }else if(2){
                $sql.=" and MOTH(applications.created_at)=MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }else{
                $sql.=" and MONTH(applications.created_at) < MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }
        }
        
        $f3->set('SESSION.query', ["sql"=>$sql, "exclude"=>["id", "about_me", "password"]]);
        $candidates=$this->db->DBQuery($sql)->paginates($page, $limit);
        $f3->set('candidates',$candidates);
        $f3->set('page',['title'=>'Candidates', 'desc'=>'']);
        $f3->set('template','pages/candidates/manage.htm');
    }
    
    public function lists(\Base $f3, $params){
        parse_str($f3->get("QUERY"), $param); 
        
        $page=array_key_exists('page', $param)?$param["page"] : 1;
        $limit=array_key_exists('limit', $param)?$param["limit"] : 20;
        $status=array_key_exists('status', $param)?$param["status"] : "";
        $duration=array_key_exists('duration', $param)?$param["duration"] : "";
        $gender=array_key_exists('gender', $param)?$param["gender"] : "";
        $location=array_key_exists('location', $param)?$param["location"] : "";
        $roles=array_key_exists('roles', $param)?$param["roles"] : "";
        $sql="select * from candidates where 1=1";
        if(array_key_exists('name', $param)){
            $sql.=" and name like '%{$param['name']}%'";
        }
        if(!empty($status)){
            $sql.=" and status='$status'";
        }
        if(!empty($gender)){
            $sql.=" and gender='$gender'";
        }
        if(!empty($location)){
            $sql.=" and location like '%$status%'";
        }
        // if(!empty($role)){
        //     $sql.=" and status='$status'";
        // }
        if(!empty($duration)){
            if($duration==1){
                $sql.=" and MONTH(created_at)=MONTH(CURRENT_DATE())";
            }else if(2){
                $sql.=" and MOTH(created_at)=MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }else{
                $sql.=" and MONTH(created_at) < MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }
        }
        
        $f3->set('SESSION.query', ["sql"=>$sql, "exclude"=>["id", "about_me", "password"]]);
        // echo $sql;exit;
        $candidates=$this->db->DBQuery($sql)->paginates($page, $limit);
        $f3->set('candidates',$candidates);

        $f3->set('SESSION.locations',$this->db->DBSelect("job_locations", array())->all());
        $f3->set('SESSION.roles',$this->db->DBSelect("job_fields", array())->all());
        $f3->set('maskString',function($str){
            return $this->stringToSecret($str);
        });
        $f3->set('page',['title'=>'Candidates', 'desc'=>'']);
        $f3->set('template','pages/candidates/manage.htm');
    }

    public function details(\Base $f3, $params) {
        // echo $params['id'];exit;
        $candidate=$this->db->DBSelect("candidates", array('id'=>$params['id']))->first();
        // var_dump($candidate);exit;
        $skills=$candidate->skills ? explode(",", $candidate->skills):[];
        $candidate->skills=$skills;
        if(empty($candidate->passport)){
            $candidate->passport="ui/img/passport.png";
        }
        
        
        $documents=$this->readFilesFromDirectory("ui/uploads/".$candidate->id);
        $documents=$this->removeFromArray($documents, $candidate->passport);
        // var_dump($documents);exit;
        if($documents){
            $files=[];
            foreach($documents as $file){
                $files[]="ui/uploads/{$candidate->id}/{$file}";
            }
            // $staff->documents=array_merge($staff->documents, $files);
            $candidate->documents=array_unique($files);
        }else{
            $candidate->documents=[];
        }
        // var_dump($candidate->documents);exit;
        $f3->set('profile',$candidate);

        //get staff
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 50;
        
        $jobs=$this->db->DBQuery("select applications.id, jobs.job_title, jobs.salary_range, jobs.gender, job_locations.name as location, job_fields.name as field, applications.resume, applications.cover_letter, applications.created_at, applications.status from applications, jobs, job_locations, job_fields where applications.job_id=jobs.id and jobs.location=job_locations.id and jobs.job_field=job_fields.id and applications.candidate_id='{$params['id']}'")->paginates($page, $limit);
        
        
        // var_dump($jobs);exit;
        $f3->set('applications', $jobs);

        $f3->set('maskString',function($str){
            return $this->stringToSecret($str);
        });
        
        $f3->set('page',['title'=>'Candidates', 'desc'=>'']);
        $f3->set('template','pages/candidates/detail.htm');
    }

    public function create(\Base $f3,$params) {
        if ($f3->exists('POST.email') && $f3->exists('POST.name')) {
            // sleep(3); // login should take a while to kick-ass brute force attacks
            $email=stripslashes(strip_tags($this->f3->get('POST.email')));
		    $password=stripslashes(strip_tags($this->f3->get('POST.password')));
            $user = $this->db->DBSelect("users", array('email'=>$email))->first();
            
            if ($user) {
                // check hash engine
                $hash_engine = $f3->get('password_hash_engine');
                $valid = false;
                if($hash_engine == 'bcrypt') {
                    $valid = \Bcrypt::instance()->verify($password, $user->password);
                } elseif($hash_engine == 'md5') {
                    $valid = (md5($password.$f3->get('password_md5_salt')) == $user->password);
                }
                if($valid) {
                    @$f3->clear('SESSION'); //recreate session id
                    $f3->set('SESSION.user_id',$user->id);
                    $f3->set('SESSION.account', 'USER');
                    if($f3->get('CONFIG.ssl_backend'))
                        $f3->reroute('https://'.$f3->get('HOST').$f3->get('BASE').'/candidate');
                    else $f3->reroute('/candidate');
                }
            }
            \Flash::instance()->addMessage('You have entered a wrong Email or Password', 'danger');
        }
        $f3->set('form.action', '');
        $f3->set('page',['title'=>'Candidates', 'desc'=>'']);
        $f3->set('template','forms/client.html');
    }

    public function postStaff(\Base $f3,$params){
        // var_dump($f3->get('POST'));exit;
        $records=json_decode($f3->get('POST.record'), true);
        foreach($records as $record){
            $data=[
                "client_id"=>$record["client"],
                "user_id"=>$record["staff"]
            ];
            $user = $this->db->DBInsert("clients_user", $data, array("client_id", "user_id"));
            // var_dump($user);exit;
            if($user->resp){
                $this->db->DBUpdate("staff", array("current_client"=>$record["client"]), array("id"=>$record["staff"]), array("current_client"));
            }
        }
       
        $f3->reroute("/employers/{$records[0]['client']}");
    }

    public function removeStaff(\Base $f3){
        $client_user_id=stripslashes(strip_tags($this->f3->get('POST.client_user_id')));
        $client=$this->db->DBSelect("clients_user", array("id"=>$client_user_id))->first();
        
        $user = $this->db->DBDelete("clients_user", array("id"=>$client_user_id));
        // var_dump($user);exit;
        if($user->resp){
            $this->db->DBUpdate("staff", array("current_client"=>""), array("id"=>$client->user_id), array());
        }
        $f3->reroute("/employers/{$client->client_id}");
    }

    public function performActions(\Base $f3){
        // var_dump($f3->get('POST'));exit;
        $client_user_id=stripslashes(strip_tags($this->f3->get('POST.client_user_id')));
        $todo=stripslashes(strip_tags($this->f3->get('POST.client_action')));
        $row=$this->db->DBUpdate("candidates", array("status"=>$todo), array("id"=>$client_user_id), array());
        // var_dump($row);exit;
        $f3->reroute("/employers/{$client_user_id}");
    }

    public function applications(\Base $f3){
        if($f3->get('SESSION.USER.isVerify')=='NOTVERIFIED'){
            $f3->reroute('/account/'.$f3->get('SESSION.USER.id'));
        }
        $f3->reroute("/candidates/".$f3->get('SESSION.user_id'));
    }

    public function jobsListing(\Base $f3){
        if($f3->get('SESSION.USER.isVerify')=='NOTVERIFIED'){
            $f3->reroute('/account/'.$f3->get('SESSION.USER.id'));
        }
        $f3->reroute($f3->get('JOB_URL'));
    }

    public function getJobs(\Base $f3, $params){
        // $category=strtoupper($params["category"]);
        // parse_str($f3->get("QUERY"), $params); 
        // var_dump($f3->get('POST'));exit;
        // var_dump($params);exit;
        $post=$f3->get('POST');
        $page=array_key_exists('page', $post)?$post["page"] : 1;
        $limit=array_key_exists('limit', $post)?$post["limit"] : 20;
        $title=array_key_exists('job_title', $post)?$post["job_title"] : '';
        $category=array_key_exists('category', $post)?$post["category"] : 'ALL';
        $qualifications=array_key_exists('qualifications', $post)?$post["qualifications"] : [];
        $sql="select MD5(jobs.id) as id, jobs.job_title, jobs.employer_name, jobs.salary_range, jobs.job_type, job_close_date, jobs.created_at, jobs.status, jobs.skills, job_locations.name as location, job_fields.name as field, (select count(*) from applications where jobs.id=applications.job_id) as applicants from jobs, job_locations, job_fields where jobs.location=job_locations.id and jobs.job_field=job_fields.id and jobs.isApprove='YES'";
        if($title != ''){
            $sql.=" and jobs.job_title like '%$title%'";
        }
        if($category != 'ALL'){
            $sql.=" and jobs.category='$category'";
        }
        if(is_array($qualifications) && count($qualifications)>0){
            $quals=implode(",", $qualifications);
            $sql.=" and JSON_CONTAINS( jobs.qualifications,'[$quals]')";
        }
        $sql.=" order by id desc;";
        // echo $sql;exit;
        $jobs=$this->db->DBQuery($sql)->paginates($page, $limit);
        if($jobs){
            $payload=[
                "status"=>true,
                "message"=>"Job openings loaded successfullu.",
                "payload"=>$jobs
            ];
            $this->Response(200, $payload);exit;
        }else{
            $payload=[
                "status"=>false,
                "message"=>"No job openings currently."
            ];
            $this->Response(200, $payload);exit;
        }
        
    }

    public function getJobsDetail(\Base $f3, $params){
        $id=base64_decode($params['id']);
        
        $profile=$this->db->DBSelect("candidates", array("id"=>$f3->get('SESSION.user_id')))->first();
        $_SESSION['USER']=(array)$profile;
        // var_dump((array)$profile);exit;
        $jobs=$this->db->DBQuery("select jobs.*, job_locations.name as location, job_fields.name as field from jobs, job_locations, job_fields where jobs.location=job_locations.id and jobs.job_field=job_fields.id and MD5(jobs.id)='{$id}'")->first();
        if($jobs){

            $payload=[
                "status"=>true,
                "message"=>"Job openings loaded successfullu.",
                "payload"=>["job"=>$jobs, "profile"=>(array)$profile]
            ];
            $this->Response(200, $payload);exit;
        }else{
            $payload=[
                "status"=>false,
                "message"=>"No job openings currently."
            ];
            $this->Response(200, $payload);exit;
        }
        
    }

    public function applys(\Base $f3, $params){
        // var_dump($f3->get('POST'));exit;
        $job=stripslashes(strip_tags($f3->get('POST.job')));
        if ($f3->exists('POST.candidate') && $f3->exists('POST.job') && $f3->exists('POST.cover_letter')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
        
            
            $candId=stripslashes(strip_tags($f3->get('POST.candidate')));
            $letter=$f3->get('POST.cover_letter');

            $candApp=$this->db->DBSelect("applications", array("candidate_id"=>$candId, "job_id"=>$job))->first();
            if($candApp){
                $payload=[
                    "status"=>false,
                    "message"=>"You have already submitted an application for this role."
                ];
                $this->Response(200, $payload);exit;
            }
            
            //upload passport
            $overwrite = true; // set to true, to overwrite an existing file; Default: false
            $slug = true; // rename file to filesystem-friendly version
            $web = \Web::instance();
            $pfiles = $web->receive(function($file,$formFieldName){
                    // $file['name'] already contains the slugged name now

                    // maybe you want to check the file size
                    if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
                        return false; // this file is not valid, return false will skip moving it

                    // everything went fine, hurray!
                    return true; // allows the file to be moved from php tmp dir to your defined upload dir
                },
                $overwrite,
                $slug
            );
            // var_dump($pfiles);exit;
            $files=array_keys($pfiles);
            $record=[
                'resume'=>$pfiles[$files[0]]? $files[0]:"",
                "cover_letter"=>$letter,
                "candidate_id"=>$candId,
                'job_id'=>$job,
            ];
            // var_dump($record);exit;
            $row=$this->db->DBInsert("applications", $record, array("resume", "candidate_id", "job_id"));
            // var_dump($row);exit;
            if(!$row->resp) {
                $payload=[
                    "status"=>false,
                    "message"=>$row->message
                ];
                $this->Response(200, $payload);exit;
            }
            $payload=[
                "status"=>true,
                "message"=>"Application submitted successfully."
            ];
            $this->Response(200, $payload);exit;
        }
        $payload=[
            "status"=>false,
            "message"=>"All input fields must be filled correctly."
        ];
        $this->Response(200, $payload);exit;
    }
    public function apply(\Base $f3, $params){
        if($f3->get('SESSION.USER.isVerify')=='NOTVERIFIED'){
            $f3->reroute('/account/'.$f3->get('SESSION.USER.id'));
        }
        // var_dump($f3->get('SESSION.USER'));exit;
        if ($f3->exists('POST.candidate') && $f3->exists('POST.job') && $f3->exists('POST.cover_letter') && $f3->exists('POST.resume_to_use') ) {
            // var_dump($f3->get('POST'));exit;
            sleep(3); // login should take a while to kick-ass brute force attacks
            $job=stripslashes(strip_tags($f3->get('POST.job')));
                
            $candId=stripslashes(strip_tags($f3->get('POST.candidate')));
            $letter=$f3->get('POST.cover_letter');
            $resume_to_use=$f3->get('POST.resume_to_use');

            $candApp=$this->db->DBSelect("applications", array("candidate_id"=>$candId, "job_id"=>$job))->first();
            if($candApp){
                \Flash::instance()->addMessage('You have already submitted an application for this role.', 'danger');
                $f3->set('template','forms/apply.htm');
            }else{
                $resume='';
                if($resume_to_use=='use_default'){
                    $resume=$f3->get('SESSION.USER.default_resume');
                }else{
                    //upload passport
                    $overwrite = true; // set to true, to overwrite an existing file; Default: false
                    $slug = true; // rename file to filesystem-friendly version
                    $web = \Web::instance();
                    $pfiles = $web->receive(function($file,$formFieldName){
                            // $file['name'] already contains the slugged name now

                            // maybe you want to check the file size
                            if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
                                return false; // this file is not valid, return false will skip moving it

                            // everything went fine, hurray!
                            return true; // allows the file to be moved from php tmp dir to your defined upload dir
                        },
                        $overwrite,
                        $slug
                    );
                    // var_dump($pfiles);exit;
                    $files=array_keys($pfiles);
                    $resume=$pfiles[$files[0]]? $files[0]:"";
                }
                
                $record=[
                    'resume'=>$resume,
                    "cover_letter"=>$letter,
                    "candidate_id"=>$candId,
                    'job_id'=>$job,
                ];
                // var_dump($record);exit;
                $row=$this->db->DBInsert("applications", $record, array("resume", "candidate_id", "job_id"));
                // var_dump($row);exit;
                if(!$row->resp) {
                    \Flash::instance()->addMessage($row->message, 'danger');
                }else{
                    \Flash::instance()->addMessage('Application was successfully submitted.', 'success');
                }    
            } 
        }
        // var_dump($f3->get('SESSION.USER'));exit;
        // $profile=$this->db->DBSelect("candidates", array("id"=>$f3->get()))->first();
        $id=$params["id"];
        
        $job=$this->db->DBQuery("select jobs.*, job_locations.name as location, job_fields.name as field from jobs, job_locations, job_fields where jobs.location=job_locations.id and jobs.job_field=job_fields.id and jobs.id='{$id}'")->first();
        // var_dump($job);exit;
        $f3->set('job', $job);
        $f3->set('template','forms/apply.htm');
    }

    public function remove(\Base $f3, $params){
        $params["id"]=$f3->get('SESSION.user_id');
        if($params['delete']=='delete'){
            $name=str_replace("___","/", $params['name']).".{$params['ext']}";
            if(file_exists($name)){
                @unlink($name);
            }
            // echo $name;exit;
            $app=$this->db->DBSelect("candidates", array("id"=>$params["id"]), array())->first();
            $documents=explode(', ', $app->other_documents);
            $newDocument=array();
            $counter=0;
            for($i=0; $i<count($documents); $i++){
                if($documents[$i]==$name) continue;
                $newDocument[$counter] = $documents[$i];
                ++$counter;
            }

            // var_dump($newDocument);exit;
            $otherDocs=implode(', ', $newDocument); //str_replace("$name, ", '', $app->documents);
            $row=$this->db->DBUpdate("candidates", array("other_documents"=>rtrim(ltrim($otherDocs, ', '), ', ')), array("id"=>$params["id"]), array());
            // if(!$row->resp) {
            //     \Flash::instance()->addMessage($row->message, 'danger');
            // }else{
            //     // unlink($name);
                
            // } 
            \Flash::instance()->addMessage('File removed successfully.', 'success');
        }
        $f3->reroute("/candidate/upload-documents");
    }
    public function attach(\Base $f3, $params){
        $params["id"]=$f3->get('SESSION.user_id');
        $app=$this->db->DBSelect("candidates", array("id"=>$params["id"]), array())->first();
        $dir=$app->id;
        if ($f3->exists('POST') && !empty($f3->get('POST')) ) {
            // ge application
            
            $web=\UploadFile::instance();
		    $files=$web->upload('', true, true);
            // var_dump($files);exit;
            $newFiles=[];
            if(count((Array)$files) >0 && is_array($files)){
                foreach($files as $file){
                    foreach($file as $item){
                        $newFiles[]=$item['url'];
                    }
                }
                // var_dump($newFiles);exit;
                $files=$this->relocateFiles($dir, $newFiles);
            }
            // var_dump($files);exit;
            $names=implode(", ", $files);
            // echo $names;exit;
            $otherDocs=$app->other_documents;
            $otherDocs="{$otherDocs}, {$names}";
            $row=$this->db->DBUpdate("candidates", array("other_documents"=>rtrim(ltrim($otherDocs, ', '), ', ')), array("id"=>$params["id"]), array());
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
                $files[]="ui/uploads/{$app->id}/{$file}";
            }
            // $staff->documents=array_merge($staff->documents, $files);
            $app->other_documents=array_unique($files);
        }
        
        // var_dump($applications->other_documents);exit;
        $f3->set('action', 'candidate/upload-documents');
        $f3->set('actionDelete', "candidate/upload-documents/delete");
        $f3->set('job', $app);
        $f3->set('template','forms/upload.html');
    }
    public function fixAttachment(\Base $f3, $params){
        
        // var_dump($params);exit;
        // $params["id"]=$f3->get('SESSION.user_id');
        $profiles=$this->db->DBSelect("candidates", array('isVerify'=>'VERIFIED'), array())->all();
        // var_dump($profiles);exit;
        foreach($profiles as $profile){
            // var_dump($profile); echo "<br>";exit;
            $profile=(object)$profile;
            if($profile){
                $base_dir='ui/uploads';
                $staff_dir="{$base_dir}/{$profile->id}";
                if (!is_dir($staff_dir)){
        			mkdir($staff_dir, 0777, true);
        			// chmod($dir, 0755);
        		}
                
                $staff=$this->db->DBSelect("staff", array("email"=>$profile->email), array())->first();
                
                $cand_passport=$this->striPath($profile->passport);
                $cand_resume=$this->striPath($profile->default_resume);
                $files=$profile->other_documents?explode(", ", $profile->other_documents): false;
                $cand_others=false;
                // var_dump($profile->passport);exit;
                if($files){
                    $cand_others=[];
                    foreach($files as $file){
                        $cand_others[]=$this->striPath($file);
                    }
                }
                
                if($cand_passport){
                    $basefile="{$base_dir}/{$cand_passport}";
                    $stafffile="{$staff_dir}/{$cand_passport}";
                    if(file_exists($basefile)){
                        rename($basefile, $stafffile);
                        $this->db->DBUpdate('candidates', array("passport"=>$stafffile), array("id"=>$profile->id), array());
                    }
                }
                
                
                
                if($cand_resume){
                    $basefile="{$base_dir}/{$cand_resume}";
                    $stafffile="{$staff_dir}/{$cand_resume}";
                    if(file_exists($basefile)){
                        rename($basefile, $stafffile);
                        $this->db->DBUpdate('candidates', array("default_resume"=>$stafffile), array("id"=>$profile->id), array());
                    }
                }
                
                
                // var_dump($cand_others);exit;
                if($cand_others){
                    $fileNames=[];
                    foreach($cand_others as $others){
                        $basefile="{$base_dir}/{$others}";
                        $stafffile="{$staff_dir}/{$others}";
                        if(file_exists($basefile)){
                            rename($basefile, $stafffile);
                            $fileNames[]=$stafffile;
                        }
                    }
                    // var_dump($fileNames);exit;
                    if($fileNames && count($fileNames)>0){
                        $fileNames=implode(", ", $fileNames);
                        $this->db->DBUpdate('candidates', array("other_documents"=>$fileNames), array("id"=>$profile->id), array());
                    }
                }
                
                // var_dump($staff);exit;
                if($staff){
                    $cand_passport=$this->striPath($staff->passport);
                    $staff_resume=$this->striPath($staff->resume);
                    $files=$staff->documents?explode(", ", $staff->documents): false;
                    $staff_others=false;
                    
                    if($files){
                        $staff_others=[];
                        foreach($files as $file){
                            $staff_others[]=$this->striPath($file);
                        }
                    }
                    
                    $files=$profile->other_documents?explode(", ", $staff->documents): false;
                    
                    
                    if($cand_passport){
                        $basefile="{$base_dir}/{$cand_passport}";
                        $stafffile="{$staff_dir}/{$cand_passport}";
                        if(file_exists($basefile)){
                            rename($basefile, $stafffile);
                            // $inDirectory=$this->removeFromArray($inDirectory, $stafffile);
                            $this->db->DBUpdate('staff', array("passport"=>$stafffile), array("id"=>$staff->id), array());
                            $this->db->DBUpdate('candidates', array("passport"=>$stafffile), array("id"=>$profile->id), array());
                        }
                    }
                    
                    
                    if($staff_resume){
                        $basefile="{$base_dir}/{$staff_resume}";
                        $stafffile="{$staff_dir}/{$staff_resume}";
                        if(file_exists($basefile)){
                            rename($basefile, $stafffile);
                            $inDirectory=$this->removeFromArray($inDirectory, $stafffile);
                            $this->db->DBUpdate('staff', array("default_resume"=>$stafffile), array("id"=>$staff->id), array());
                            if($staff_resume == $cand_resume){
                                $this->db->DBUpdate('candidates', array("default_resume"=>$stafffile), array("id"=>$profile->id), array());
                            }
                        }
                    }
                    
                    
                    // var_dump($staff_others);exit;
                    if($staff_others){
                        $fileNames=[];
                        foreach($staff_others as $others){
                            $basefile="{$base_dir}/{$others}";
                            $stafffile="{$staff_dir}/{$others}";
                            if(file_exists($basefile)){
                                rename($basefile, $stafffile);
                                $fileNames[]=$stafffile;
                            }
                        }
                        $inDirectory=$this->readFilesFromDirectory($staff_dir);
                        
                        $inDirectory=$this->removeFromArray($inDirectory, $cand_passport);
                        $inDirectory=$this->removeFromArray($inDirectory, $staff_resume);
                        
                        $files=[];
                        foreach($inDirectory as $file){
                            $files[]="{$staff_dir}/{$file}";
                        }
                        // var_dump($files);exit;
                        $files=array_unique($files);
                        $otherNames=implode(", ", $files);
                        $row=$this->db->DBUpdate('staff', array("documents"=>$otherNames), array("id"=>$staff->id), array());
                        // var_dump($row);exit;
                    }
                }
            }
        }
        
        echo "Done";exit;
    }
    
    
    private function checkFile($dir){
        foreach (glob("../imgs/" . $name . ".*") as $file) { //foreach (glob("../imgs/" . $name . ".*") as $file) {
            if (file_exists($file)) {
                unlink($file);
                break;
            }
        }

    }
}