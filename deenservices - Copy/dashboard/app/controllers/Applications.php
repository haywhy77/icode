<?php
require_once("app/inc/findDate.php");
class Applications extends Controller{
    public function index(\Base $f3, $params){
        parse_str($f3->get("QUERY"), $param); 
        // var_dump($params);exit;
        $page=array_key_exists('page', $param)?$param["page"] : 1;
        $limit=array_key_exists('limit', $param)?$param["limit"] : 20;
        $status=array_key_exists('status', $param)?$param["status"] : "";
        $duration=array_key_exists('duration', $param)?$param["duration"] : "";
        $sql="select candidates.id as cand_id, candidates.name as candidate, candidates.email, applications.id, jobs.id as job_id, jobs.job_title, jobs.employer_name, job_locations.name as location, job_fields.name as field, applications.created_at, applications.status from applications, jobs, job_locations, candidates, job_fields where candidates.id=applications.candidate_id and applications.job_id=jobs.id and jobs.location=job_locations.id and job_fields.id=jobs.job_field";
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
        
        $sql.= " order by applications.id desc";
        //echo $sql;exit;
        
        $f3->set('SESSION.query', ["sql"=>$sql, "exclude"=>["cand_id", "id", "job_id"]]);
        $jobs=$this->db->DBQuery($sql)->paginates($page, $limit);
        
        $f3->set('applications',$jobs);
        $f3->set('page',['title'=>'Applications', 'desc'=>'']);
        $f3->set('template','pages/applications/manage.htm');
    }

    public function getApplicants(\Base $f3, $params){
        $id=$params["id"];
        //get applicant details from db
        
        $application=$this->db->DBSelect("applications", array("id"=>$id))->first();
        if($application->other_documents){
            $application->other_documents=explode(", ", $application->other_documents);
        }else{
            $application->other_documents=[];
        }
        // var_dump($application);exit;
        $profile=$this->db->DBSelect("candidates", array("id"=>$application->candidate_id))->first();
        $track=$this->db->DBSelect("application_tracker", array("application_id"=>$id, 'candidate_id'=>$profile->id))->all();
        $skills=$profile->skills ? explode(",", $profile->skills):[];
        $profile->skills=$skills;
        if(empty($profile->passport)){
            $profile->passport="ui/img/passport.png";
        }
        
        // $documents=$this->readFilesFromDirectory("ui/uploads/".$profile->id);
        // $documents=$this->removeFromArray($documents, $profile->passport);
        // // var_dump($documents);exit;
        // if($documents){
        //     $files=[];
        //     foreach($documents as $file){
        //         $files[]="ui/uploads/{$staff->upload_dir}/{$file}";
        //     }
        //     // $staff->documents=array_merge($staff->documents, $files);
        //     $staff->documents=array_unique($files);
        // }else{
        //     $staff->documents=[];
        // }
        $job=$this->db->DBQuery("select jobs.*, job_fields.name as field, job_locations.name as location from jobs, job_fields, job_locations where jobs.job_field=job_fields.id and jobs.location=job_locations.id and jobs.id='{$application->job_id}'")->first();

        $f3->set('tracks',$track);
        $f3->set('application',$application);
        $f3->set('profile',$profile);
        $f3->set('job',$job);
        // echo $f3->get('BASE')."/".$profile->resume;
        // var_dump($application);exit;
        // if (!file_exists($application->resume)) throw "File: $application->resume does not exist";
        
        $f3->set('page',['title'=>$profile->name, 'desc'=>'']);
        $f3->set('template','pages/applications/detail.htm');
    }

    public function openApplication(\Base $f3, $params){
        $f3->set("extra", [
            "css"=>["plugins/live-edit/liveedit.css"],
            "js"=>["plugins/live-edit/liveedit.js"]
        ]);
        $f3->set('template','forms/application.html');
    }

    public function updateApplications(\Base $f3, $params){
        // var_dump($f3->get('POST'));exit;
        $applicant = stripslashes(strip_tags($f3->get('POST.applicant')));
        $appId = stripslashes(strip_tags($f3->get('POST.appId')));
        $remark = stripslashes(strip_tags($f3->get('POST.remark')));
        $status = stripslashes(strip_tags($f3->get('POST.status'))); 
        $location = $f3->exists('POST.location') ? stripslashes(strip_tags($f3->get('POST.location'))): 'PHYSICAL'; 
        $interviewDate = $f3->exists('POST.date') ? stripslashes(strip_tags($f3->get('POST.date'))): ''; 
        $interviewtime = $f3->exists('POST.time') ? stripslashes(strip_tags($f3->get('POST.time'))): ''; 
        $meeting_url = $f3->exists('POST.meeting_url') ? stripslashes(strip_tags($f3->get('POST.meeting_url'))): '';
        $meeting_address = $f3->exists('POST.meeting_url') ? stripslashes(strip_tags($f3->get('POST.meeting_address'))): '';
        $employment_type = $f3->exists('POST.employment_type') ? stripslashes(strip_tags($f3->get('POST.employment_type'))): '';
        $application=$this->db->DBSelect("applications", array("id"=>$appId))->first();
        if(!$application){
            $payload=[
                "status"=>false,
                "message"=>"Application doesnt exists."
            ];
            $this->Response(200, $payload);exit;
        }
        $meeting=$location=='VIRTUAL'?$meeting_url: $meeting_address;
        $row=$this->db->DBInsert("application_tracker", array('candidate_id'=>$applicant, 'application_id'=>$appId, 'remark'=>$remark, 'level'=>$status=='CANCEL'?'APPROVE':($status=='REINTERVIEW'?'INTERVIEW': $status), 'interview_date'=>"{$interviewDate} {$interviewtime}", 'type'=>$location, 'interview_url'=>$meeting, 'type'=>$location), array('application_id', 'candidate_id', 'remark', 'level'));
        
        $profile=$this->db->DBSelect("candidates", array("id"=>$applicant))->first();
        $job=$this->db->DBQuery("select jobs.*, job_locations.name as location, job_fields.name as role from jobs, applications, job_fields, job_locations where jobs.id=applications.job_id and jobs.location=job_locations.id and jobs.job_field=job_fields.id and applications.id='$appId'")->first();
        
        
        if(!$row->resp) {
            $payload=[
                "status"=>false,
                "message"=>$row->message
            ];
            $this->Response(200, $payload);exit;
            // \Flash::instance()->addMessage($row->message, 'danger');
        }else{
            switch($status){
                case'APPROVE':
                    $job=$this->db->DBQuery("select distinct applications.*, jobs.job_title, job_fields.name as role, jobs.job_field from applications, jobs, job_fields where job_fields.id=jobs.job_field and applications.job_id=jobs.id and applications.id='$appId'")->first();
                    $this->db->DBUpdate("applications", array("status"=>'APPROVED'), array('id'=>$appId), array('status'));
                    //$profileLink=$f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE').'/application/complete-profile/'.md5($profile->id);
                    $date = new DateTime();
                    $f3->set("data", array(
                        "name"=>$profile->name, 
                        "date"=>$date->add(new DateInterval('P1W'))->format('D d, F, Y'),
                        "role"=>$job->role,
                        // "link"=>$profileLink,
                        "title"=>$job->job_title
                    ));
                    $content = Template::instance()->render( "email/approve.htm" );
                    $mail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->name], "Follow up on your application", $content);

                break;
                case'REJECT':
                    $this->db->DBUpdate("applications", array("status"=>'REJECTED'), array('id'=>$appId), array('status'));
                    $f3->set("data", array("name"=>$profile->name));
                    $content = Template::instance()->render( "email/rejection.htm" );
                    
                    $mail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->name], "Follow up on your application", $content);
                break;
                case'CANCEL':
                    $row=$this->db->DBUpdate("applications", array("status"=>'APPROVED'), array('id'=>$appId), array('status'));
                    
                    $f3->set("data", array("name"=>$profile->name));
                    $content = Template::instance()->render( "email/cancel.htm" );
                    
                    $mail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->name], "Cancellation of Interview", $content);
                break;
                case'REINTERVIEW':
                case'INTERVIEW':
                    $this->db->DBUpdate("application_track", array('interview_date'=>'', 'meeting_url'=>$meeting, 'resumption_date'=>"{$interviewDate} {$interviewtime}"), array('id'=>$row->code), array());
                    $this->db->DBUpdate("applications", array("status"=>'INREVIEW'), array('id'=>$appId), array('status'));
                    $f3->set("data", array(
                        "name"=>$profile->name, 
                        "date"=> date("D d, F, Y", strtotime($interviewDate)),
                        "time"=>date("H:i:s", strtotime($interviewtime)),
                        "location"=>$location,
                        "link"=>$meeting,
                    ));
                    if($status=='REINTERVIEW'){
                        $subject="Reschedule of Interview";
                        $content = Template::instance()->render( "email/reschedule-virtual.htm" );
                        $mail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->name], $subject, $content);
                    }else{
                        $subject="Invitation for Interview";
                        $content = Template::instance()->render( "email/interview-virtual.htm" );
                        $mail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->name], $subject, $content);
                    }
                    
                break;

                case'INTERVIEWED':
                    $this->db->DBUpdate("application_track", array('level'=>'INTERVIEWED'), array('id'=>$row->code), array());
                    $this->db->DBUpdate("applications", array("status"=>'INTERVIEWED'), array('id'=>$appId), array('status'));
                    $f3->set("data", array("name"=>$profile->name));
                    break;


                case'EMPLOYED':
                
                    $password='';
                    $hash_engine = $f3->get('password_hash_engine');
                    $pwd=$this->code_generator();
                    if($hash_engine == 'bcrypt') {
                        $password = \Bcrypt::instance()->hash($pwd, $f3->get('password_md5_salt'));
                    } elseif($hash_engine == 'md5') {
                        $password = md5($pwd.$f3->get('password_md5_salt'));
                    }
                    $this->db->DBUpdate("application_track", array('interview_date'=>'', 'type'=>$employment_type, 'meeting_url'=>$meeting, 'resumption_date'=>"{$interviewDate} {$interviewtime}"), array('id'=>$row->code), array());

                    
                    $staff=$this->db->DBInsert("staff", array("passport"=>$profile->passport, "names"=>$profile->name, "email"=>$profile->email, "phone"=>$profile->phone, "gender"=>$profile->gender, "resume"=>$application->resume, "password"=>$password, "employment_type"=>$f3->get('POST.employment_type'), 'position'=>$job->job_title, 'linkedIn_url'=>$profile->linkedIn_url, 'portfolio_url'=>$profile->portfolio_url, 'skills'=>$profile->skills, 'upload_dir'=>$profile->id, 'documents'=>$application->other_documents), array("names", "email", "gender", "resume", "password"));
                    if($staff){
                        
                    
                        $this->db->DBUpdate("applications", array("status"=>'EMPLOYED'), array('id'=>$appId), array('status'));
                        $profileLink=$f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE').'/application/complete-profile/'.md5($staff->code);
                        $f3->set("data", array(
                            "name"=>$profile->name, 
                            "title"=>$job->job_title,
                            "role"=>$job->role,
                            "startDate"=>"{$interviewDate} {$interviewtime}",
                            "salary"=>$job->salary_range,
                            "location"=>$employment_type,
                            "link"=>$profileLink
                        ));
                        $content = Template::instance()->render( "email/employment.htm" );
                        // echo $content;exit;
                        // var_dump(["email"=>$profile->email, "name"=>$profile->name]);exit;
                        $mail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->name], "Congratulations! You have been employed.", $content);
                        // var_dump($mail);exit;
                    }else{
                        $payload=[
                            "status"=>false,
                            "message"=>"Error ocured while profiling staff."
                        ];
                        $this->Response(200, $payload);exit;
                    }
                    
                break;
            }
            $payload=[
                "status"=>true,
                "message"=>"Application processed successfully"
            ];
            $this->Response(200, $payload);exit;
            
            // \Flash::instance()->addMessage($row->message, 'success');
        }
        // $f3->reroute("application/{$applicant}");
        }
}