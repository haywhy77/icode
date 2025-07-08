<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class SendMail extends \Prefab{
    protected $mail;
    protected $user;
    protected $f3;
    public function __construct() {
        //Create an instance; passing `true` enables exceptions
        $this->mail = new PHPMailer(true);

        $this->f3=\Base::instance();
        //Server settings
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF; //Enable verbose debug output
        $this->mail->isSMTP();//Send using SMTP
        $this->mail->Host       = $this->f3->get("smtp_host");//Set the SMTP server to send through
        $this->mail->SMTPAuth   = true;   //Enable SMTP authentication
        $this->mail->Username   = $this->f3->get("smtp_username");  //SMTP username
        $this->mail->Password   = $this->f3->get("smtp_pw"); //SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $this->mail->Port  = $this->f3->get("smtp_port"); //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $this->user=$this->f3->get("smtp_user");
    }
    
    public function send($name, $to=array(), $subject, $template, array $addCC=null, array $addBCC=null, array $attachment=null){
        
        $name="Deen Services";
        try{
            $this->mail->setFrom($this->user, $name);
            if($this->is_multi_array($to)){
                foreach($to as $rec){
                    if(empty($rec['email'])) continue;
                    $this->mail->addAddress($rec['email'], array_key_exists("name", $rec)?$rec['name']:"");//Add a recipient
                }
            }else{
                $this->mail->addAddress($to['email'], array_key_exists("name", $to)? $to['name']:"");//Add a recipient
            }
            
            if($addCC && count($addCC)>0){
                if($this->is_multi_array($addCC)){
                    foreach($addCC as $rec){
                        if(empty($rec['email'])) continue;
                        $this->mail->addCC($rec['email'], array_key_exists("name", $rec)?$rec['name']:"");
                    }
                }else{
                    $this->mail->addCC($addCC['email'], array_key_exists("name", $addCC)? $addCC['name']:"");//Add a recipient
                }
            }
            
            if($addBCC && count($addBCC)>0){
                if($this->is_multi_array($addBCC)){
                    foreach($addBCC as $rec){
                        if(empty($rec['email'])) continue;
                        $this->mail->addBCC($rec['email'], array_key_exists("name", $rec)?$rec['name']:"");
                    }
                }else{
                    $this->mail->addBCC($addBCC['email'], array_key_exists("name", $addBCC)? $addBCC['name']:"");//Add a recipient
                }
            }
            
            $this->mail->addReplyTo('no-reply@deenservices.com', $name);
            // var_dump($addCC);exit;
            // if($addCC) $this->mail->addCC($addCC);
            
            // if($addBCC) $this->mail->addBCC($addBCC);
        
            //Attachments
            if($attachment){
                $this->mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                $this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
            }

            
            $this->mail->Subject = $subject;
            // $this->mail->msgHTML(file_get_contents($template), __DIR__);
            
            $this->mail->msgHTML($template);
            // $tpl = new Template();
            // $this->mail->msgHTML($tpl->resolve($tpl->parse($template)));
            $return=$this->mail->send();
            $this->mail->clearAddresses();
            return $return;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$e}";
            return false;
        }
    }

    private function is_multi_array( $arr ) {
        rsort( $arr );
        return isset( $arr[0] ) && is_array( $arr[0] );
    }
}




    