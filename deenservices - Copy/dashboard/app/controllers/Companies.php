<?php
class Companies extends Controller{
    public function index(\Base $f3, $params){
        parse_str($f3->get("QUERY"), $params); 
        // var_dump($params);exit;
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 20;
        $clients=$this->db->DBSelect("clients", array())->paginates($page, $limit);
        $f3->set('clients',$clients);
        $f3->set('page',['title'=>'Clients', 'desc'=>'']);
        $f3->set('template','pages/company/manage.htm');
    }

    public function details(\Base $f3, $params) {
        $id=0;
        $id=!array_key_exists('id', $params) ? $f3->get("SESSION.user_id") : $params["id"];
        $clients=$this->db->DBSelect("clients", array('id'=>$id))->first();
        // var_dump($clients);exit;
        $f3->set('client',$clients);

        //get staff
        $page=array_key_exists('page', $params)?$params["page"] : 1;
        $limit=array_key_exists('limit', $params)?$params["limit"] : 50;
        
        $response=$this->db->DBQuery("select clients_user.id as clt, staff.id, staff.names, staff.email, staff.gender, staff.phone, staff.employment_type, clients_user.status from staff, clients_user where clients_user.user_id= staff.id and clients_user.client_id='{$id}'")->paginates($page, $limit);
        // var_dump($response);exit;
        $f3->set('staff', $response);

        $f3->set("roles", $this->db->DBQuery("select * from job_fields;")->all());
       
        $f3->set('staffs', $this->db->DBQuery("select * from staff where status='ACTIVE' and id not in (select user_id as id from clients_user where client_id='{$clients->id}')")->all());
        $f3->set('page',['title'=>'Client', 'desc'=>'']);
        $f3->set('template','pages/company/detail.htm');
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
            \Flash::instance()->addMessage('Wrong Username/Password', 'danger');
        }
        $f3->set('form.action', '');
        $f3->set('page',['title'=>'Clients', 'desc'=>'']);
        $f3->set('template','forms/client.html');
    }

    public function postStaff(\Base $f3,$params){
        // var_dump($f3->get('POST'));exit;
        $records=json_decode($f3->get('POST.record'), true);
        $roles=$f3->exists("POST.role")?$f3->get('POST.role'):"";
        $location=$f3->exists("POST.location")?$f3->get('POST.location'):"";
        $date=$f3->exists("POST.date")?$f3->get('POST.date'):"";
        $time=$f3->exists("POST.time")?$f3->get('POST.time'):"";
        foreach($records as $record){
            $data=[
                "client_id"=>$record["client"],
                "user_id"=>$record["staff"],
                "role"=>$roles,
                "location"=>$location,
                "resumption_date"=>"$date $time"
            ];
            $row = $this->db->DBSelect("clients_user", array("client_id"=>$record["client"], "user_id"=>$record["staff"]))->first();
            // $row=$this->db->DBSelect()->first();
            if($row){
                continue;
            }
            $user = $this->db->DBInsert("clients_user", $data, array("client_id", "user_id"));
            // var_dump($user);exit;
            if($user->resp){
                $this->db->DBUpdate("staff", array("current_client"=>$record["client"]), array("id"=>$record["staff"]), array("current_client"));
            }
            $profile=$this->db->DBSelect("staff", array("id"=>$record["staff"]))->first();
            $emp=$this->db->DBSelect("clients", array("id"=>$record["client"]))->first();
            $f3->set("data", array(
                "name"=>$profile->names, 
                "client"=>$emp->names,
                "role"=>$roles,
                "location"=>$location,
                "date"=>"$date $time"
            ));
            
            
            $contents = Template::instance()->render( "email/staff-notify.htm" );
            // var_dump(["email"=>$profile->email, "name"=>$profile->name]);exit;
            $staffMail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->names], "New posting information", $contents);
            // var_dump($staffMail);exit;
            $f3->set("data", array(
                "name"=>$emp->names, 
                "staff"=>$profile->names,
                "role"=>$roles,
                "location"=>$location,
                "date"=>"$date $time"
            ));
            // var_dump(["email"=>$emp->email, "name"=>$emp->names]);exit;
            $content = Template::instance()->render( "email/employer-notify.htm" );
            $clientMail=SendMail::instance()->send("No-reply", ["email"=>$emp->email, "name"=>$emp->names], "Staff Posting", $content, null, ["email"=>"hr@deenservices.com", "name"=>"HR"]);
        }
        
        $f3->reroute("/employers/{$records[0]['client']}");
    }

    public function removeStaff(\Base $f3){
        // var_dump($f3->get('POST'));exit;
        $client_user_id=stripslashes(strip_tags($this->f3->get('POST.client_user_id')));
        $client=$this->db->DBSelect("clients_user", array("id"=>$client_user_id))->first();
        // var_dump($client);exit;
        $user = $this->db->DBDelete("clients_user", array("id"=>$client_user_id));
        // var_dump($user);exit;
        if($user->resp){
            $this->db->DBUpdate("staff", array("current_client"=>""), array("id"=>$client_user_id), array());
        }
        $profile=$this->db->DBSelect("candidates", array("id"=>$client->user_id))->first();
        $emp=$this->db->DBSelect("clients", array("id"=>$client->client_id))->first();
        $f3->set("data", array(
            "name"=>$profile->name, 
            "client"=>$emp->names,
            "date"=>"",
        ));
        
        $contents = Template::instance()->render( "email/remove-staff-notify.htm" );
        $staffMail=SendMail::instance()->send("No-reply", ["email"=>$profile->email, "name"=>$profile->name], "Detachment information", $contents);
        // var_dump($staffMail);exit;
        $f3->set("data", array(
            "name"=>$emp->names, 
            "staff"=>$profile->name,
            "date"=>"",
        ));
        $content = Template::instance()->render( "email/remove-employer-notify.htm" );
        $clientMail=SendMail::instance()->send("No-reply", ["email"=>$emp->email, "name"=>$emp->names], "Staff Removal", $content);
        $f3->reroute("/employers/{$client->client_id}");
    }

    public function performActions(\Base $f3){
        // var_dump($f3->get('POST'));exit;
        $client_user_id=stripslashes(strip_tags($this->f3->get('POST.client_user_id')));
        $todo=stripslashes(strip_tags($this->f3->get('POST.client_action')));
        $row=$this->db->DBUpdate("clients", array("status"=>$todo), array("id"=>$client_user_id), array());
        // var_dump($row);exit;
        $client=$this->db->DBSelect("clients", array("id"=>$client_user_id))->first();
        $f3->set("data", array(
            "name"=>$client->names
        ));
        $content = Template::instance()->render( "email/activate-employer.htm" );
        $mail=SendMail::instance()->send("No-reply", ["email"=>$client->email, "name"=>$client->names], "Account Activated", $content);
        $f3->reroute("/employers/{$client_user_id}");
    }

    public function myStaff(\Base $f3){

    }
}