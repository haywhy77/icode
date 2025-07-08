<?php
class Jobs extends Controller{
    public function index(\Base $f3, $params){
        parse_str($f3->get("QUERY"), $param); 
        // var_dump($params);exit;
        $page=array_key_exists('page', $param)?$param["page"] : 1;
        $limit=array_key_exists('limit', $param)?$param["limit"] : 20;
        $status=array_key_exists('status', $param)?$param["status"] : "";
        $duration=array_key_exists('duration', $param)?$param["duration"] : "";
        $sql="select jobs.id, jobs.job_title, jobs.employer_name, jobs.salary_range, jobs.job_type, jobs.created_at, jobs.status, jobs.skills, jobs.isApprove, (select count(*) from applications where jobs.id=applications.job_id) as applicants, IF(COALESCE(jobs.employer_name, '') != '', (select names from clients where jobs.employer_name=clients.id), 'From Admin') as client from jobs where 1=1";
        if($f3->get('SESSION.account')=='CLIENT'){
            $client=$f3->get('SESSION.user_id');
            $sql.=" and employer_name='$client'";
        }
        if(!empty($status)){
            $sql.=" and status='$status'";
        }
        if(!empty($duration)){
            if($duration==1){
                $sql.=" and MONTH(created_at)=MONTH(CURRENT_DATE())";
            }else if(2){
                $sql.=" and MOTH(created_at)=MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }else{
                $sql.=" and MONTH(created_at) < MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }
        }
        $sql.=" order by id desc;";
        // echo $sql;exit;
        $jobs=$this->db->DBQuery($sql)->paginates($page, $limit);
        
        $f3->set('jobs',$jobs);
        $f3->set('page',['title'=>'Jobs', 'desc'=>'']);
        $f3->set('template','pages/jobs/manage.htm');
    }

    public function editApplicants(\Base $f3, $params){
        $id=$params["id"];
       
        //get applicant details from db
       
        
        $f3->set('application', $this->db->DBSelect("jobs", array("id"=>$id))->first());

        $f3->set("job.locations", $this->db->DBSelect("job_locations", array())->all());
        $f3->set("job.fields", $this->db->DBSelect("job_fields", array())->all());
        $f3->set("job.skills", $this->db->DBSelect("skills", array())->all());
        $f3->set("job.courses", $this->db->DBSelect("course_training", array())->all());
        $f3->set("form.action", "/jobs/save");
        $f3->set("extra", [
            "js"=>[
                "js/vendors/select2/select2.min.js"
            ]
        ]);
        $f3->set('page',['title'=>'New Jobs Posting', 'desc'=>'']);
        $f3->set('template','forms/jobs.html');
    }
    public function getApplicants(\Base $f3, $params){
        $id=$params["id"];
        parse_str($f3->get("QUERY"), $param); 
        $page=array_key_exists('page', $param)?$param["page"] : 1;
        $limit=array_key_exists('limit', $param)?$param["limit"] : 5;
        $status=array_key_exists('status', $param)?$param["status"] : "";
        $duration=array_key_exists('duration', $param)?$param["duration"] : "";
        //get applicant details from db
       
        $job=$this->db->DBQuery("select jobs.*, job_locations.name as location, job_fields.name as field, IF(COALESCE(jobs.employer_name, '') != '', (select names from clients where jobs.employer_name=clients.id), 'From Admin') as client from jobs, job_locations, job_fields where jobs.location=job_locations.id and jobs.job_field=job_fields.id and jobs.id='$id'")->first();
        // var_dump($job);exit;
        $f3->set('job',$job);
        // get applicantions
        $sql="select candidates.id as cand_id, candidates.name as candidate, candidates.email, applications.id, candidates.gender, applications.created_at, applications.status from applications, jobs, candidates where candidates.id=applications.candidate_id and applications.job_id=jobs.id and jobs.id='$id'";
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
        //echo $sql;exit;
        $f3->set('SESSION.query', ["sql"=>$sql, "exclude"=>["cand_id", "id"]]);
        $applications=$this->db->DBQuery($sql)->paginates($page, $limit);
        $f3->set('applications',$applications);

        $f3->set('page',['title'=>'Job Details', 'desc'=>'']);
        $f3->set('template','pages/jobs/detail.htm');
    }

    public function newJob(\Base $f3, $params){
        $f3->set("job.locations", $this->db->DBSelect("job_locations", array())->all());
        $f3->set("job.fields", $this->db->DBSelect("job_fields", array())->all());
        $f3->set("job.skills", $this->db->DBSelect("skills", array())->all());
        $f3->set("job.courses", $this->db->DBSelect("course_training", array())->all());
        $f3->set("form.action", "/jobs/save");
        $f3->set("extra", [
            "js"=>[
                "js/vendors/select2/select2.min.js"
            ]
        ]);
        $f3->set('page',['title'=>'New Jobs Posting', 'desc'=>'']);
        $f3->set('template','forms/jobs.html');
    }

    public function save(\Base $f3, $params){
        
        
        // var_dump($f3->get('POST'));exit;
        $title = stripslashes(strip_tags($f3->get('POST.title')));
        $category = stripslashes(strip_tags($f3->get('POST.category')));  
        $emp_name = $f3->get('SESSION.account')=='CLIENT'?stripslashes(strip_tags($f3->get('POST.emp_name'))):'';   
        $job = stripslashes(strip_tags($f3->get('POST.job')));   
        $location = stripslashes(strip_tags($f3->get('POST.location')));   
        $salary  = stripslashes(strip_tags($f3->get('POST.salary')));    
        $job_type  = stripslashes(strip_tags($f3->get('POST.job_type')));  
        $job_site  = stripslashes(strip_tags($f3->get('POST.job_site')));  
        $job_duration  = stripslashes(strip_tags($f3->get('POST.job_duration')));   
        $qualifications  = $f3->exists('POST.qualifications')?implode(",", $f3->get('POST.qualifications')):"";    
        $abt_comp  = ($f3->get('POST.about_company'));    
        $job_desc  = ($f3->get('POST.job_desc'));    
        $job_req  = ($f3->get('POST.job_requirement'));    
        $job_gender  = stripslashes(strip_tags($f3->get('POST.job_gender'))); 
        $course  = $f3->exists('POST.courses')?implode(",", $f3->get('POST.courses')):""; 
        $other  = $f3->exists('POST.other_requirements')?implode(",", $f3->get('POST.other_requirements')):"";
        $close_date  = stripslashes(strip_tags($f3->get('POST.close_date'))); 
        $job_status  = stripslashes(strip_tags($f3->get('POST.job_status')));
        $skills  = $f3->exists('POST.skills')?implode(",", $f3->get('POST.skills')):""; 
        $isApprove= $f3->get('SESSION.account')=='CLIENT'?'NO':'YES';
        
        $record=[
            "job_title"=>$title,
            "category"=>$category,
            "employer_name"=>$emp_name,
            "job_field"=>$job,
            "location"=>$location,
            "salary_range"=>$salary,
            "job_type"=>$job_type,
            "job_site"=>$job_site,
            "job_duration"=>$job_duration,
            "gender"=>$job_gender,
            "about_company"=>$abt_comp,
            "job_desc"=>$job_desc,
            "job_requirement"=>$job_req,
            "job_close_date"=>$job_status=='Ongoing' ? 'Ongoing' : $close_date,
            "skills"=>$skills,
            "isApprove"=>$isApprove,
            "qualifications"=>$qualifications,
            "other_requirements"=>$other,
            "job_status"=>$job_status,
            "course_training"=>$course
        ];
        // var_dump($record);exit;
        $row='';
        if($f3->get('POST.action')=='save'){
            $row=$this->db->DBInsert("jobs", $record, array("job_title", "job_field", "location", "salary_range", "job_type", "job_site", "job_duration", "gender", "job_status"));
        }else{
            $row=$this->db->DBUpdate("jobs", $record, array("id"=>$f3->get('POST.id')), array("job_title", "job_field", "location", "salary_range", "job_type", "job_site", "job_duration", "gender", "about_company", "job_desc", "job_requirement", "job_status"));
        }
        $msg="Job posted successfully.";
        // var_dump($row);exit;
        if(!$row->resp) {
            \Flash::instance()->addMessage($row->message, 'danger');
        }else{
            $applicants=$this->db->DBSelect("candidates", array("status"=>'ACTIVE'), array("name", "email"))->all();
            // $applicants=[
            //     ["name"=>"Adedeji Richards", "email"=>"adewumiadedeji27@gmail.com"],
            //     ["name"=>"Adeyeun Imoleayo", "email"=>"adeyeunimoleayo@gmail.com"],
            // ];
            if($applicants){
                
                for($i=0; $i<count($applicants); $i++){
                    $f3->set("data", array(
                        "name"=>$applicants[$i]["name"],
                        "title"=>$title
                    ));
                    $content = Template::instance()->render( "email/job-notif.htm" );
                    $mail=SendMail::instance()->send("No-reply", $applicants[$i], "New job for you.", $content);
                    $f3->clear("data");
                    // var_dump($mail);exit;
                }
                // echo"Done";exit;
            }
            $msg.=$f3->get('SESSION.account')=='CLIENT'?" Kindly wait for the approval from the system adminnistrator.":"";
            // echo $msg;exit;
            \Flash::instance()->addMessage($msg, 'success');
        }
        
        if($f3->get('POST.action')=='save'){
            $f3->reroute("/jobs/new");
        }else{
            $f3->reroute("/jobs/edit/".$f3->get('POST.id'));
        }
        //$f3->set('template','forms/jobs.html');
       
    }

    public function locations(\Base $f3){
        if($f3->exists('POST.name')){
            $name = stripslashes(strip_tags($f3->get('POST.name')));
            $row=$this->db->DBInsert("job_locations", array('name'=>$name), array('name'));
            if($row->resp){
                $payload=[
                    "status"=>true,
                    "message"=>"New job role created successfully."
                ];
                $this->Response(200, $payload);exit;
            }else{
                $payload=[
                    "status"=>false,
                    "message"=>$row->message
                ];
                $this->Response(200, $payload);exit;
            }
        }
        parse_str($f3->get("QUERY"), $params); 
        // var_dump($params);exit;
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 20;
        $locations=$this->db->DBSelect("job_locations", array())->paginates($page, $limit);
        // var_dump($locations);exit;
        $f3->set("job.locations", $locations);
        $f3->set('page',['title'=>'Job locations', 'desc'=>'']);
        $f3->set('template','pages/locations.htm');
    }
    public function fields(\Base $f3, $params){
        if($f3->exists('POST.name')){
            $name = stripslashes(strip_tags($f3->get('POST.name')));
            $row=$this->db->DBInsert("job_fields", array('name'=>$name), array('name'));
            if($row->resp){
                $payload=[
                    "status"=>true,
                    "message"=>"New job role created successfully."
                ];
                $this->Response(200, $payload);exit;
            }else{
                $payload=[
                    "status"=>false,
                    "message"=>$row->message
                ];
                $this->Response(200, $payload);exit;
            }
        }
        
        parse_str($f3->get("QUERY"), $params); 
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 20;
        $locations=$this->db->DBSelect("job_fields", array())->paginates($page, $limit);
        // var_dump($locations);exit;
        $f3->set("job.fields", $locations);
        $f3->set('page',['title'=>'Job fields', 'desc'=>'']);
        $f3->set('template','pages/fields.htm');
    }
    public function skills(\Base $f3, $params){
        if($f3->exists('POST.name')){
            
            $names = $f3->get('POST.name');
            // var_dump($name);exit;
            $error=[];
            foreach($names as $name){
                $row=$this->db->DBInsert("skills", array('name'=>$name), array('name'));
                if(!$row->resp){
                    $error[]=$row->message;
                }
            }
            
            if(count($error)<1){
                $payload=[
                    "status"=>true,
                    "message"=>"Skills added successfully."
                ];
                $this->Response(200, $payload);exit;
            }else{
                $payload=[
                    "status"=>false,
                    "message"=>implode("\n", $error)
                ];
                $this->Response(200, $payload);exit;
            }
        }
        
        parse_str($f3->get("QUERY"), $params); 
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 20;
        $locations=$this->db->DBSelect("skills", array())->paginates($page, $limit);
        // var_dump($locations);exit;
        $f3->set("job.skills", $locations);
        $f3->set("extra", [
            "js"=>[
                "js/vendors/select2/select2.min.js"
            ]
        ]);
        $f3->set('page',['title'=>'Skills', 'desc'=>'']);
        $f3->set('template','pages/skills.htm');
    }

    public function misc(\Base $f3, $params){
        $type=$f3->get('POST.action');
        $id=$f3->get('POST.id');
        $row=[];
        switch($type){
            case'SKILLS':
                $row=$this->db->DBDelete('skills', array('id'=>$id));
            break;
            case'FIELDS':
                $row=$this->db->DBDelete('job_fields', array('id'=>$id));
            break;
            case'LOCATIONS':
                $row=$this->db->DBDelete('job_locations', array('id'=>$id));
            break;
            case'JOBS':
                $row=$this->db->DBDelete('jobs', array('id'=>$id));
            break;
            case'ISAPPROVE':
                $row=$this->db->DBUpdate('jobs', array("isApprove"=>$f3->get('POST.value')), array('id'=>$id), array());
                if($row['resp'] && $f3->get('POST.value')=='YES'){
                    $applicants=$this->db->DBSelect("candidates", array("status"=>'ACTIVE'), array("name", "email"))->all();
                    $job=$this->db->DBSelect("jobs", array("id"=>$id), array())->first();
                    if($applicants){
                        
                        for($i=0; $i<count($applicants); $i++){
                            $f3->set("data", array(
                                "name"=>$applicants[$i]["name"],
                                "title"=>$job->job_title
                            ));
                            $content = Template::instance()->render( "email/job-notif.htm" );
                            $mail=SendMail::instance()->send("No-reply", $applicants[$i], "New job for you.", $content);
                            $f3->clear("data");
                        }
                        // echo"Done";exit;
                    }
                }
            break;
            case'ISCLOSE':
                $row=$this->db->DBUpdate('jobs', array("status"=>$f3->get('POST.value')), array('id'=>$id), array());
            break;
            case'CANDIDATE':
                $row=$this->db->DBDelete('candidates', array('id'=>$id));
            break;
            case'CLIENT':
                $row=$this->db->DBDelete('clients', array('id'=>$id));
            break;
            default:
                $payload=[
                    "status"=>false,
                    "message"=>"Invalid action selected."
                ];
                $this->Response(200, $payload);exit;
        }
        $payload=[
            "status"=>$row->resp,
            "message"=>$row->message
        ];
        $this->Response(200, $payload);exit;
    }
}
