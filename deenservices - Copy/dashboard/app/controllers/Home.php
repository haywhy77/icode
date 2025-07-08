<?php

use Dompdf\Dompdf;


class Home extends Controller{
    static public function isLoggedIn() {
        /** @var Base $f3 */
        $f3 = \Base::instance();
        $self = new self();
        $JwtController = new Jwt($f3->get("SECRET_KEY"), $f3->get("JWT_TTL"));
        // echo time();exit;
        $token=$f3->get('SESSION.token');
        
        if($token){
            $signature =$JwtController->decode($token);
            
            // echo $signature;exit;
            // if(!$signature){
            //     @$f3->clear('SESSION.token');
            //     @$f3->clear('SESSION.user_id');
            //     @$f3->clear('SESSION.USER');
            //     return false;
            // }
            // echo time() - strtotime($f3->get('SESSION.LAST_ACTIVITY')) ."\t". 3 * 3600;exit;
            
            // if (!$f3->exists('SESSION.LAST_ACTIVITY')  || (time() - strtotime($f3->get('SESSION.LAST_ACTIVITY')) < 3 * 3600)) {
            //     // last request was more than 30 minutes ago
            //     @$f3->clear('SESSION.token');
            //     @$f3->clear('SESSION.user_id');
            //     @$f3->clear('SESSION.USER');
            //     session_unset();     // unset $_SESSION variable for the run-time 
            //     session_destroy();   // destroy session data in storage
            //     return false;
            // }
            // session_regenerate_id(true);
            $f3->set('SESSION.LAST_ACTIVITY', time());
            $JwtController->setTTL($f3->get("JWT_TTL"));
            // var_dump($f3->get('SESSION.user_id'));exit;
            if ($f3->exists('SESSION.user_id') && $f3->get('SESSION.account')=='USER') {
                $user = $self->db->DBSelect("candidates", array('id'=>$f3->get('SESSION.user_id')))->first();
                if($user && $user->isDefault=='NO') {
                    $token =$JwtController->refresh($token);
                    if(!$token) return false;
                    $f3->set('USER',$user);
                    $f3->set('SESSION.USER', (array)$user);
                    $f3->set('SESSION.token', $token);
                    return true;
                }
                
            }

            if ($f3->exists('SESSION.user_id') && $f3->get('SESSION.account')=='CLIENT') {
                $user = $self->db->DBSelect("clients", array('id'=>$f3->get('SESSION.user_id')))->first();
                if($user) {
                    $token =$JwtController->refresh($token);
                    if(!$token) return false;
                    $f3->set('USER',$user);
                    $f3->set('SESSION.token', $token);
                    return true;
                }
            }

            if ($f3->exists('SESSION.user_id') && $f3->get('SESSION.account')=='ADMIN') {
                $user = $self->db->DBSelect("users", array('id'=>$f3->get('SESSION.user_id')))->first();
                if($user) {
                    $token =$JwtController->refresh($token);
                    if(!$token) return false;
                    $f3->set('USER',$user);
                    $f3->set('SESSION.token', $token);
                    return true;
                }
            }
        }
        
        return false;
    }

    public function candidate_login(\Base $f3,$params) {
        if ($f3->exists('POST.email') && $f3->exists('POST.password')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            $email=stripslashes(strip_tags($this->f3->get('POST.email')));
            $password=stripslashes(strip_tags($this->f3->get('POST.password')));
            // echo $password;exit;
            $user = $this->db->DBSelect("candidates", array('email'=>$email))->first();
            // var_dump($password);exit;
            // echo md5($password.$f3->get('password_md5_salt'));exit;
            if ($user) {
                // check hash engine
                $hash_engine = $f3->get('password_hash_engine');
                $valid = false;
                if($hash_engine == 'bcrypt') {
                    $valid = \Bcrypt::instance()->verify($password, $user->password);
                } elseif($hash_engine == 'md5') {
                    $valid = (md5($password.$f3->get('password_md5_salt')) == $user->password);
                }
                // echo $valid;exit;
                if($valid) {
                    
                    @$f3->clear('SESSION'); //recreate session id
                    
                    $JwtController = new Jwt($f3->get("SECRET_KEY"), $f3->get("JWT_TTL"));
                    
                    $payload = [
                        "id" => $user->id,
                        "name" => $user->name,
                        "type" => 'USER'
                    ];
                    $token =$JwtController->encode($payload);
                    // echo $token;exit;
                    // session_regenerate_id(true);
                    $f3->set('SESSION.token', $token);
                    $f3->set('SESSION.user_id',$user->id);
                    $f3->set('SESSION.account', 'USER');
                    $f3->set('SESSION.LAST_ACTIVITY', time());
                    
                    
                    // $payload=[
                    //     "status"=>true,
                    //     "message"=>"Login was successful",
                    //     "payload"=>(array)$user
                    // ];
                    // $this->Response(200, $payload);exit;
                    if($user->isDefault=='YES'){
                        $f3->reroute("/change-password");
                    }
                    if($f3->get('CONFIG.ssl_backend'))
                        $f3->reroute($f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE').'/');
                    else $f3->reroute('/');
                }
            }
            \Flash::instance()->addMessage('Wrong Username/Password', 'danger');
        }
        // $payload=[
        //     "status"=>false,
        //     "message"=>"Invalid email and/or password."
        // ];
        // $this->Response(200, $payload);exit;
        $f3->set('form.action', '');
        echo Template::instance()->render('auth/login.html');die();
    }
    
    public function candidate_signup($f3,$params) {
        // var_dump($f3->get('POST'));exit;
        if ($f3->exists('POST.applicant_name') && $f3->exists('POST.applicant_email') && $f3->exists('POST.applicant_password')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            $email=stripslashes(strip_tags($this->f3->get('POST.applicant_email')));
		    $password=stripslashes(strip_tags($this->f3->get('POST.applicant_password')));
            $name=stripslashes(strip_tags($this->f3->get('POST.applicant_name')));
            $user = $this->db->DBSelect("candidates", array('email'=>$email))->first();
            
            if (!$user) {
                // check hash engine
                $hash_engine = $f3->get('password_hash_engine');
                
                if($hash_engine == 'bcrypt') {
                    $password = \Bcrypt::instance()->hash($password, $f3->get('password_md5_salt'));
                } elseif($hash_engine == 'md5') {
                    $password = md5($password.$f3->get('password_md5_salt'));
                }
                $record=[
                    "name"=>$name,
                    "email"=>$email,
                    "password"=>$password
                ];
                $row=$this->db->DBInsert("candidates", $record, array("name", "email", "password"));
                
                if(!$row->resp) {
                    \Flash::instance()->addMessage($row->message, 'danger');
                    // $payload=[
                    //     "status"=>false,
                    //     "message"=>$row->message
                    // ];
                    // $this->Response(200, $payload);exit;
                }else{
                    $f3->set("data", array(
                        "name"=>$name
                    ));
                    // send email notification to the user account for verification
                    $content = Template::instance()->render( "email/welcome.htm" );
                    SendMail::instance()->send("", ["email"=>$email, "name"=>$name], "Welcome on board to ".$this->f3->get("business"), $content);
                    \Flash::instance()->addMessage("Your signup was successful. You can login now", 'success');
                    // echo Template::instance()->render('auth/login.html');die();
                    // $payload=[
                    //     "status"=>true,
                    //     "message"=>"Registration was successful"
                    // ];
                    // $this->Response(200, $payload);exit;
                    // $f3->reroute("/");
                    echo Template::instance()->render('auth/emailNotif.html');die();
                }
                
            }else{
                \Flash::instance()->addMessage('User account with this email already exist.', 'danger');
                // $payload=[
                //     "status"=>false,
                //     "message"=>"User account with this email already exist."
                // ];
                // $this->Response(200, $payload);exit;
            }
        }
        echo Template::instance()->render('auth/signup.html');die();
        // $payload=[
        //     "status"=>false,
        //     "message"=>"All input fields must be filled correctly."
        // ];
        // $this->Response(200, $payload);exit;
    }

    public function client_login($f3,$params) {
        if ($f3->exists('POST.email') && $f3->exists('POST.password')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            $user = $this->db->DBSelect("clients", array('email'=>$f3->get('POST.email')))->first();
            // var_dump($user);exit;
            if ($user) {
                // check hash engine
                $hash_engine = $f3->get('password_hash_engine');
                $valid = false;
                
                if($hash_engine == 'bcrypt') {
                    $valid = \Bcrypt::instance()->verify($f3->get('POST.password'), $user->password);
                } elseif($hash_engine == 'md5') {
                    // echo sprintf("%s", (md5($f3->get('POST.password').$f3->get('password_md5_salt')) ."==". $user->password));exit;
                    $valid = (md5($f3->get('POST.password').$f3->get('password_md5_salt')) == $user->password);
                }
                // var_dump($user);exit;
                if($valid) {
                    @$f3->clear('SESSION');
                    $f3->set('SESSION.user_id',$user->id);
                    if($user->isDefault=='YES'){
                        $f3->reroute("/client/change-password");
                    }
                    if($user->status=='ACTIVE'){
                        $JwtController = new Jwt($f3->get("SECRET_KEY"), $f3->get("JWT_TTL"));
                    
                        $payload = [
                            "id" => $user->id,
                            "name" => $user->names,
                            "type" => 'CLIENT'
                        ];
                        $token =$JwtController->encode($payload);
                        // session_regenerate_id(true);
                        $f3->set('SESSION.token', $token);
                        $f3->set('SESSION.account', 'CLIENT');
                        
                        $f3->set('SESSION.LAST_ACTIVITY', time());
                        if($f3->get('CONFIG.ssl_backend'))
                            $f3->reroute($f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE').'/');
                        else $f3->reroute('/');
                    }
                    \Flash::instance()->addMessage('Your business account is not activated yet. Kindly contact admin for support.', 'danger');
                }else{
                    \Flash::instance()->addMessage('Invalid Password', 'danger');
                }
            }else{
                \Flash::instance()->addMessage('Invalid email address', 'danger');
            }
           
        }
        $f3->set('form.action', 'client');
        echo Template::instance()->render('auth/client_login.html');die();
        // $this->f3->set('template','auth/login.html');
    }

    public function client_signup($f3,$params) {
        // var_dump($f3->get('POST'));exit;
        if ($f3->exists('POST.company_name') && $f3->exists('POST.company_email') && $f3->exists('POST.company_number') && $f3->exists('POST.company_password')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            $email=stripslashes(strip_tags($this->f3->get('POST.company_email')));
		    $password=stripslashes(strip_tags($this->f3->get('POST.company_password')));
            $name=stripslashes(strip_tags($this->f3->get('POST.company_name')));
            $phone=stripslashes(strip_tags($this->f3->get('POST.company_number')));
            $user = $this->db->DBSelect("clients", array('email'=>$email))->first();
            
            if (!$user) {
                // check hash engine
                $hash_engine = $f3->get('password_hash_engine');
                
                if($hash_engine == 'bcrypt') {
                    $password = \Bcrypt::instance()->hash($password, $f3->get('password_md5_salt'));
                } elseif($hash_engine == 'md5') {
                    $password = md5($password.$f3->get('password_md5_salt'));
                }
                $record=[
                    "names"=>$name,
                    "email"=>$email,
                    "phone"=>"+44".$phone,
                    "password"=>$password
                ];
                $row=$this->db->DBInsert("clients", $record, array("names", "email", "phone", "password"));
                
                if(!$row->resp) {
                    \Flash::instance()->addMessage($row->message, 'danger');
                    $payload=[
                        "status"=>false,
                        "message"=>$row->message
                    ];
                    $this->Response(200, $payload);exit;
                }else{
                    $f3->set("data", array(
                        "name"=>$name
                    ));
                    // send email notification to the user account for verification
                    $content = Template::instance()->render( "email/welcome-employer.htm" );
                    SendMail::instance()->send("", ["email"=>$email, "name"=>$name], "Welcome on board to ".$this->f3->get("business"), $content);
                    \Flash::instance()->addMessage("Signup was successful. Kindly wait for Admin to activate your account.", 'success');
                    // echo Template::instance()->render('auth/login.html');die();
                    // $payload=[
                    //     "status"=>true,
                    //     "message"=>"Registration was successful"
                    // ];
                    // $this->Response(200, $payload);exit;
                    $f3->reroute("/client");

                }
                
            }else{
                \Flash::instance()->addMessage('User account with this email already exist.', 'danger');
                // $payload=[
                //     "status"=>false,
                //     "message"=>"User account with this email already exist."
                // ];
                // $this->Response(200, $payload);exit;
            }
        }
        echo Template::instance()->render('auth/client_signup.html');die();
        // $payload=[
        //     "status"=>false,
        //     "message"=>"All input fields must be filled correctly."
        // ];
        // $this->Response(200, $payload);exit;
    }

    public function admin_login($f3,$params) {
        
        if ($f3->exists('POST.email') && $f3->exists('POST.password')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            $user = $this->db->DBSelect("users", array('email'=>$f3->get('POST.email')))->first();
            // var_dump($user);exit;
            if ($user) {
                // check hash engine
                $hash_engine = $f3->get('password_hash_engine');
                $valid = false;
                if($hash_engine == 'bcrypt') {
                    $valid = \Bcrypt::instance()->verify($f3->get('POST.password'), $user->password);
                } elseif($hash_engine == 'md5') {
                    $valid = (md5($f3->get('POST.password').$f3->get('password_md5_salt')) == $user->password);
                }
                if($valid) {
                    @$f3->clear('SESSION');
                    $f3->set('SESSION.user_id',$user->id);
                    if($user->isDefault=='YES'){
                        $f3->reroute("/admin/change-password");
                    }
                    //recreate session id
                    $JwtController = new Jwt($f3->get("SECRET_KEY"), $f3->get("JWT_TTL"));
                    
                    $payload = [
                        "id" => $user->id,
                        "name" => $user->names,
                        "type" => 'ADMIN'
                    ];
                    $token =$JwtController->encode($payload);
                    // session_regenerate_id(true);
                    $f3->set('SESSION.token', $token);
                    $f3->set('SESSION.account', 'ADMIN');
                    
                    $f3->set('SESSION.LAST_ACTIVITY', time());
                    if($f3->get('CONFIG.ssl_backend'))
                        $f3->reroute($f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE').'/');
                    else $f3->reroute('/');
                }else{
                    \Flash::instance()->addMessage('Invalid Password', 'danger');
                }
            }else{
                \Flash::instance()->addMessage('Invalid Email address', 'danger');
            }
        }
        $f3->set('form.action', 'admin');
        echo Template::instance()->render('auth/client_login.html');die();
        // $this->f3->set('template','auth/login.html');
    }
    public function admin_invitation(\Base $f3, $params){
        $id=$params["id"];
        $f3->set("id", $id);
        $user=$this->db->DBQuery("select * from users where MD5(id)='$id'")->first();
        if(!$user){
            \Flash::instance()->addMessage("Invalid invitation link.", 'danger');
        }else{
            if($user->isVerify==1){
                \Flash::instance()->addMessage("Account already activated.", 'warning');
                $f3->reroute("/admin");
            }
            if ($f3->exists('POST.password')) {
                sleep(3); // login should take a while to kick-ass brute force attacks
                $email=stripslashes(strip_tags($user->email));
                $password=stripslashes(strip_tags($this->f3->get('POST.password')));
                
                $hash_engine = $f3->get('password_hash_engine');
                    
                if($hash_engine == 'bcrypt') {
                    $password = \Bcrypt::instance()->hash($password, $f3->get('password_md5_salt'));
                } elseif($hash_engine == 'md5') {
                    $password = md5($f3->get('POST.password').$f3->get('password_md5_salt'));
                }
                $record=[
                    "password"=>$password,
                    "isVerify"=>true,
                    'status'=>'ACTIVE'
                ];
                $row=$this->db->DBUpdate("users", $record, array("id"=>$user->id), array("password"));
                
                if(!$row->resp) {
                    \Flash::instance()->addMessage($row->message, 'danger');
                }
                \Flash::instance()->addMessage("Signup completed successful", 'success');
                $f3->reroute("/admin");
            }
        }
        
        echo Template::instance()->render('auth/invitation.html');die();
    }
    public function validate(\Base $f3, $params){
        sleep(3);
        if(!$f3->exists('SESSION') || !$f3->exists('SESSION.USER')){

            $payload=[
                "status"=>false,
                "message"=>"User already logged out."
            ];
            $this->Response(200, $payload);exit;
        }else{
            $user=$f3->get('SESSION.USER');

            if($user["isDefault"] == 'YES'){
                $payload=[
                    "status"=>false,
                    "message"=>"Kindly change your password."
                ];
                $this->Response(200, $payload);exit;
            }
            $payload=[
                "status"=>true,
                "message"=>"User still login."
            ];
            $this->Response(200, $payload);exit;
        }
    }
    public function logout($f3,$params) {
        $user=$f3->get('SESSION.account');
        $f3->clear('SESSION');
        if($user=='USER'){
            $f3->reroute($f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE'));
        }else{
            $f3->reroute($f3->get('PROTOCOL').$f3->get('HOST').$f3->get('BASE').'/'.strtolower($user));
        }
        
    }

    public function admin_reset($f3, $params) {
        
        if ($f3->exists('POST.email')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            $user = $this->db->DBSelect("users", array('email'=>$f3->get('POST.email')))->first();
            // var_dump($user);exit;
            if ($user) {
                // check hash engine
                $pwd=$this->code_generator('','',8);
                $password='';
                $hash_engine = $f3->get('password_hash_engine');
                if($hash_engine == 'bcrypt') {
                    $password = \Bcrypt::instance()->hash($pwd, $f3->get('password_md5_salt'));
                } elseif($hash_engine == 'md5') {
                    $password = md5($pwd.$f3->get('password_md5_salt'));
                }
                $row=$this->db->DBUpdate("users", array("password"=>$password, 'isDefault'=>'YES'), array("id"=>$user->id), array("password"));

                
                $f3->set("data", array(
                    "name"=>$user->names,
                    "password"=>$pwd
                ));
                // send email notification to the user account for verification
                $content = Template::instance()->render( "email/reset.htm" );
            
                $mail=SendMail::instance()->send("", ["email"=>$user->email, "name"=>$user->names], "Password Reset",$content);
                \Flash::instance()->addMessage('New password sent to your email.', 'success');
            }else{
                \Flash::instance()->addMessage('User account doesnt exist.', 'danger');
            }
            
        }
        $f3->set('form.action', 'admin');
        echo Template::instance()->render('auth/reset.html');die();
        // $this->f3->set('template','auth/login.html');
    }

    public function client_reset($f3, $params) {
        
        if ($f3->exists('POST.email')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            $user = $this->db->DBSelect("clients", array('email'=>$f3->get('POST.email')))->first();
            // var_dump($user);exit;
            if ($user) {
                // check hash engine
                $pwd=$this->code_generator('','',8);
                $password='';
                $hash_engine = $f3->get('password_hash_engine');
                if($hash_engine == 'bcrypt') {
                    $password = \Bcrypt::instance()->hash($pwd, $f3->get('password_md5_salt'));
                } elseif($hash_engine == 'md5') {
                    $password = md5($pwd.$f3->get('password_md5_salt'));
                }
                $row=$this->db->DBUpdate("clients", array("password"=>$password, 'isDefault'=>'YES'), array("id"=>$user->id), array("password"));

                
                
                $f3->set("data", array(
                    "name"=>$user->names,
                    "password"=>$pwd
                ));
                // send email notification to the user account for verification
                $content = Template::instance()->render( "email/reset.htm" );
            
                $mail=SendMail::instance()->send("", ["email"=>$user->email, "name"=>$user->names], "Password Reset",$content);
                
                \Flash::instance()->addMessage('New password sent to your email.', 'success');
                $f3->reroute("/client");

            }else{
                \Flash::instance()->addMessage('User account doesnt exist.', 'danger');
            }
            
        }
        $f3->set('form.action', 'client');
        echo Template::instance()->render('auth/reset.html');die();
        // $this->f3->set('template','auth/login.html');
    }


    public function candidate_reset($f3, $params) {
        
        if ($f3->exists('POST.email')) {
            // var_dump($f3->get('POST'));exit;
            sleep(3); // login should take a while to kick-ass brute force attacks
            $user = $this->db->DBSelect("candidates", array('email'=>$f3->get('POST.email')))->first();
            // var_dump($user);exit;
            if ($user) {
                // check hash engine
                $pwd=$this->code_generator('','',8);
                $password='';
                $hash_engine = $f3->get('password_hash_engine');
                if($hash_engine == 'bcrypt') {
                    $password = \Bcrypt::instance()->hash($pwd, $f3->get('password_md5_salt'));
                } elseif($hash_engine == 'md5') {
                    $password = md5($pwd.$f3->get('password_md5_salt'));
                }
                $row=$this->db->DBUpdate("candidates", array("password"=>$password, 'isDefault'=>'YES'), array("id"=>$user->id), array("password"));

                
                
                $f3->set("data", array(
                    "name"=>$user->name,
                    "password"=>$pwd
                ));
                // send email notification to the user account for verification
                $content = Template::instance()->render( "email/reset.htm" );
            
                $mail=SendMail::instance()->send("", ["email"=>$user->email, "name"=>$user->name], "Password Reset",$content);
                
                // $payload=[
                //     "status"=>true,
                //     "message"=>"Password changed and temprary password sent to your email."
                // ];
                // $this->Response(200, $payload);exit;
                \Flash::instance()->addMessage('New password sent to your email.', 'success');
                $f3->reroute("/");
            }else{
                \Flash::instance()->addMessage('Email address doesnt exist.', 'danger');
                // $payload=[
                //     "status"=>false,
                //     "message"=>"User account doesnt exist."
                // ];
                // $this->Response(200, $payload);exit;
            }
            
        }
        // $payload=[
        //     "status"=>false,
        //     "message"=>"A valid email address must be provided."
        // ];
        // $this->Response(200, $payload);exit;

        $f3->set('form.action', 'candidate');
        echo Template::instance()->render('auth/cand_reset.html');die();
    }


    public function admin_change($f3, $params) {
        // var_dump($f3->get('SESSION.user_id'));exit;
        if ($f3->exists('POST.password') && $f3->exists('POST.cpassword')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            if($f3->get("POST.password") != $f3->get("POST.cpassword")){
                \Flash::instance()->addMessage('Passwords do match.', 'danger');
            }else{
                $id=$f3->get('SESSION.user_id');
                $user = $this->db->DBSelect("users", array('id'=>$id))->first();
                // var_dump($user);exit;
                if ($user) {
                    // check hash engine
                    
                    $password='';
                    $hash_engine = $f3->get('password_hash_engine');
                    if($hash_engine == 'bcrypt') {
                        $password = \Bcrypt::instance()->hash($f3->get('POST.password'), $f3->get('password_md5_salt'));
                    } elseif($hash_engine == 'md5') {
                        $password = md5($f3->get('POST.password').$f3->get('password_md5_salt'));
                    }
                    $row=$this->db->DBUpdate("users", array("password"=>$password, 'isDefault'=>'NO'), array("id"=>$user->id), array("password"));
                    // var_dump($row);exit;
                    
                    $f3->set("data", array(
                        "name"=>$user->names,
                        "password"=>$f3->get('POST.password')
                    ));
                    
                    // send email notification to the user account for verification
                    $content = Template::instance()->render( "email/reset.htm" );
                
                    $mail=SendMail::instance()->send("", ["email"=>$user->email, "name"=>$user->names], "Password Reset",$content);
                    $f3->clear('SESSION');
                    \Flash::instance()->addMessage('Password changed. Kindly login with the new password..', 'success');
                }else{
                    \Flash::instance()->addMessage('User account doesnt exist.', 'danger');
                }
            }
        }
        $f3->set('form.action', 'admin');
        echo Template::instance()->render('auth/change.html');die();
        // $this->f3->set('template','auth/login.html');
    }

    public function client_change($f3, $params) {
        if ($f3->exists('POST.password') && $f3->exists('POST.cpassword')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            if($f3->get("POST.password") != $f3->get("POST.cpassword")){
                \Flash::instance()->addMessage('Passwords do match.', 'danger');
            }else{
                $id=$f3->get('SESSION.user_id');
                $user = $this->db->DBSelect("clients", array('id'=>$id))->first();
                // var_dump($user);exit;
                if ($user) {
                    // check hash engine
                    
                    $password='';
                    $hash_engine = $f3->get('password_hash_engine');
                    if($hash_engine == 'bcrypt') {
                        $password = \Bcrypt::instance()->hash($f3->get('POST.password'), $f3->get('password_md5_salt'));
                    } elseif($hash_engine == 'md5') {
                        $password = md5($f3->get('POST.password').$f3->get('password_md5_salt'));
                    }
                    $row=$this->db->DBUpdate("clients", array("password"=>$password, 'isDefault'=>'NO'), array("id"=>$user->id), array("password"));
                    // var_dump($row);exit;
                    
                    $f3->set("data", array(
                        "name"=>$user->names,
                        "password"=>$f3->get('POST.password')
                    ));
                    // send email notification to the user account for verification
                    $content = Template::instance()->render( "email/reset.htm" );
                
                    $mail=SendMail::instance()->send("", ["email"=>$user->email, "name"=>$user->names], "Password Reset",$content);
                    $f3->clear('SESSION');
                    \Flash::instance()->addMessage('Password changed. Kindly login with the new password..', 'success');
                    $this->f3->reroute("client");
                }else{
                    \Flash::instance()->addMessage('Employer account doesnt exist.', 'danger');
                }
            }
        }
        $f3->set('form.action', 'client');
        echo Template::instance()->render('auth/change.html');die();
        // $this->f3->set('template','auth/login.html');
    }


    public function candidate_change($f3, $params) {
        if ($f3->exists('POST.password') && $f3->exists('POST.cpassword')) {
            sleep(3); // login should take a while to kick-ass brute force attacks
            if($f3->get("POST.password") != $f3->get("POST.cpassword")){
                \Flash::instance()->addMessage('Passwords do  not match.', 'danger');
                // $payload=[
                //     "status"=>false,
                //     "message"=>"Passwords do  not match."
                // ];
                // $this->Response(200, $payload);exit;
            }else{
                $id=$f3->get('SESSION.user_id');
                $user = $this->db->DBSelect("candidates", array('id'=>$id))->first();
                // var_dump($user);exit;
                if ($user) {
                    // check hash engine
                    
                    $password='';
                    $hash_engine = $f3->get('password_hash_engine');
                    if($hash_engine == 'bcrypt') {
                        $password = \Bcrypt::instance()->hash($f3->get('POST.password'), $f3->get('password_md5_salt'));
                    } elseif($hash_engine == 'md5') {
                        $password = md5($f3->get('POST.password').$f3->get('password_md5_salt'));
                    }
                    $row=$this->db->DBUpdate("candidates", array("password"=>$password, 'isDefault'=>'NO'), array("id"=>$user->id), array("password"));
                    // var_dump($row);exit;
                    
                    $f3->set("data", array(
                        "name"=>$user->name,
                        "password"=>$f3->get('POST.password')
                    ));
                    // send email notification to the user account for verification
                    $content = Template::instance()->render( "email/reset.htm" );
                
                    $mail=SendMail::instance()->send("", ["email"=>$user->email, "name"=>$user->name], "Password Reset",$content);
                    $f3->clear('SESSION');
                    \Flash::instance()->addMessage("Password changed. Kindly login with the new password.", 'success');
                    $f3->reroute("/");
                    // $payload=[
                    //     "status"=>true,
                    //     "message"=>""
                    // ];
                    // $this->Response(200, $payload);exit;
                }else{
                    \Flash::instance()->addMessage("Your account doesn't exist.", 'danger');
                    // $payload=[
                    //     "status"=>true,
                    //     "message"=>""
                    // ];
                    // $this->Response(200, $payload);exit;
                }
            }
            // $payload=[
            //     "status"=>true,
            //     "message"=>"A valid password must be provided."
            // ];
            // $this->Response(200, $payload);exit;
    
            $f3->set('form.action', '');
            echo Template::instance()->render('auth/cand_change.html');die();
        }
        // $payload=[
        //     "status"=>true,
        //     "message"=>"A valid password must be provided."
        // ];
        // $this->Response(200, $payload);exit;

        $f3->set('form.action', '');
        echo Template::instance()->render('auth/cand_change.html');die();
    }

    public function completeProfile(\Base $f3, $params){
        
        $user=$this->db->DBQuery("select * from staff where MD5(id)='{$params["id"]}'")->first();
        if(!$user){
            $f3->error(404);
        }

        if($user->complete=='YES'){
            \Flash::instance()->addMessage("Your application profile was updated and files submitted for review.", 'success');
            $f3->set('action', true);
        }else{
            if ($f3->VERB=='POST') {
                // var_dump($f3->get('POST'));exit;
                $post=$f3->get('POST');
                if($_FILES){
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
                    $dir=$user->upload_dir;
                    $files=$this->relocateFiles($dir, $files);
                    $post["files"]=implode(", ", $files);
                }
                $post["files"]=ltrim(ltrim("{$user->documents}, {$post['files']}", ", "), ", ");
                // echo $post["files"];exit;
                
                $profile=array(
                    "dob"=>$post["dob"],
                    "preffered_positions"=>$post["role_interest"],
                    "trn_number"=>$post["trn_number"],
                    "title"=>$post["title"],
                    "address"=>$post["address"],
                    "eligibility"=>$post["eligibility"],
                    "documents"=>$post["files"],
                    "old_name"=>$post["full_name_history"],
                    "reason_for_name_change"=>$post["reason_for_name_change"],
                    "date_of_change_from"=>$post["from_date_name_change"],
                    "disability"=>$post["disability"],
                    "disability_detail"=>$post["disability_detail"],
                    "adjustments"=>$post["adjustments"],
                    "permission_to_share_info"=>$post["permission_to_share_info"],
                    "criminal_record"=>$post["criminal_record"],
                    "disqualified"=>$post["disqualified"],
                    "criminal_details"=>$post["criminal_details"],
                    "disqualified_children"=>$post["disqualified_children"],
                    "disqualified_children_care"=>$post["disqualified_children_care"],
                    "disqualified_children_prevent"=>$post["disqualified_children_prevent"],
                    "disqualified_children_others"=>$post["disqualified_children_others"],
                );

                // var_dump($profile);exit;

                $st=$this->db->DBUpdate("staff", $profile, array("id"=>$user->id), array());
                
                
                
                // var_dump($post["reason_for_leaving"]);exit;            
                for($i=0; $i<count($post["position"]); $i++){
                    $employmentHistory=array(
                        "staff_id"=>$user->id,
                        "position"=>$post["position"][$i],
                        "reason_for_leaving"=>array_key_exists($i, $post["reason_for_leaving"])?$post["reason_for_leaving"][$i]:"",
                        "employer_name"=>array_key_exists($i, $post["employer_name"])?$post["employer_name"][$i]:"",
                        "employer_address"=>array_key_exists($i, $post["employer_address"])?$post["employer_address"][$i]:"",
                        "from_date_employment"=>array_key_exists($i, $post["from_date_employment"])?$post["from_date_employment"][$i]:"",
                        "to_date_employment"=>array_key_exists($i, $post["to_date_employment"])?$post["to_date_employment"][$i]:"",
                        "referee_details"=>array_key_exists($i, $post["referee_details"])?$post["referee_details"][$i]:"",
                        "referee_email"=>array_key_exists($i, $post["referee_email"])?$post["referee_email"][$i]:"",
                        "contact_referee"=>array_key_exists($i, $post["contact_referee"])?$post["contact_referee"][$i]:""
                    );
                    $row=$this->db->DBInsert("staff_employment_history", $employmentHistory, array());
                    
                }

                

                
                for($i=0; $i<count($post["qualification"]); $i++){
                    $qualification=array(
                        "staff_id"=>$user->id,
                        "qualification"=>$post["qualification"][$i],
                        "subject"=>array_key_exists($i, $post["subject"])?$post["subject"][$i]:"",
                        "institution"=>array_key_exists($i, $post["institution"])?$post["institution"][$i]:"",
                        "grade"=>array_key_exists($i, $post["grade"])?$post["grade"][$i]:"",
                        "from_date_qualification"=>array_key_exists($i, $post["from_date_qualification"])?$post["from_date_qualification"][$i]:"",
                        "to_date_qualification"=>array_key_exists($i, $post["to_date_qualification"])?$post["to_date_qualification"][$i]:""
                    );
                    $row=$this->db->DBInsert("staff_qualification_history", $qualification, array());
                    
                }

                
                for($i=0; $i<count($post["course_name"]); $i++){
                    $professional=array(
                        "staff_id"=>$user->id,
                        "course_name"=>$post["course_name"][$i],
                        "course_subject"=>array_key_exists($i, $post["course_subject"])?$post["course_subject"][$i]:"",
                        "course_institution"=>array_key_exists($i, $post["course_institution"])?$post["course_institution"][$i]:"",
                        "course_grade"=>array_key_exists($i, $post["course_grade"])?$post["course_grade"][$i]:"",
                        "from_date_course"=>array_key_exists($i, $post["from_date_course"])?$post["from_date_course"][$i]:"",
                        "to_date_course"=>array_key_exists($i, $post["to_date_course"])?$post["to_date_course"][$i]:"",
                    );
                    $row=$this->db->DBInsert("staff_profession_history", $professional, array());
                    
                }


                for($i=0; $i<count($post["reason_gap"]); $i++){
                    $gap=array(
                        "staff_id"=>$user->id,
                        "reason_gap"=>$post["reason_gap"][$i],
                        "from_date_gap"=>array_key_exists($i, $post["from_date_gap"])?$post["from_date_gap"][$i]:"",
                        "to_date_gap"=>array_key_exists($i, $post["to_date_gap"])?$post["to_date_gap"][$i]:""
                    );
                    $row=$this->db->DBInsert("staff_gaps_employment", $gap, array());
                    
                }

            
                for($i=0; $i<count($post["overseas_country"]); $i++){
                    $police=array(
                        "staff_id"=>$user->id,
                        "overseas_country"=>$post["overseas_country"][$i],
                        "overseas_job_title"=>array_key_exists($i, $post["overseas_job_title"])?$post["overseas_job_title"][$i]:"",
                        "overseas_from_date"=>array_key_exists($i, $post["overseas_from_date"])?$post["overseas_from_date"][$i]:"",
                        "overseas_to_date"=>array_key_exists($i, $post["overseas_to_date"])?$post["overseas_to_date"][$i]:""
                    );
                    $row=$this->db->DBInsert("staff_police_check", $police, array());
                    
                }

                $this->db->DBUpdate('staff', array('complete'=>'YES'), array('id'=>$user->id), array());
                \Flash::instance()->addMessage("Your application profile was updated and files submitted for review.", 'success');
                
                

            }else{
                $f3->set('action', false);
            }    
        }


        
        $names=explode(' ', $user->names);
        $user->firstName=$names[0];
        $user->lastName=count($names)>1?$names[1]:"";
        $user->middleName=count($names)>2?$names[2]:"";
        
        $f3->set('profile', $user);
        echo Template::instance()->render('auth/staff_doc.html');die();
    }
}
