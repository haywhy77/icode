<?php
class Messaging extends Controller{
    public function index(\Base $f3, $params){
        parse_str($f3->get("QUERY"), $param); 
        // var_dump($params);exit;
        $page=array_key_exists('page', $param)?$param["page"] : 1;
        $limit=array_key_exists('limit', $param)?$param["limit"] : 20;
        $status=array_key_exists('status', $param)?$param["status"] : "";
        $duration=array_key_exists('duration', $param)?$param["duration"] : "";
        $sql="select scouts.*, candidates.name as cand, clients.names as client from scouts, clients, candidates where candidates.id=scouts.cand_id and clients.id=scouts.client_id";
        if(!empty($status)){
            $sql.=" and scouts.status='$status'";
        }
        if(!empty($duration)){
            if($duration==1){
                $sql.=" and MONTH(scouts.created_at)=MONTH(CURRENT_DATE())";
            }else if(2){
                $sql.=" and MOTH(scouts.created_at)=MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }else{
                $sql.=" and MONTH(scouts.created_at) < MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
            }
        }
        $sql.=" order by id desc";
        //echo $sql;exit;
        $f3->set('SESSION.query', ["sql"=>$sql, "exclude"=>["cand_id", "client_id", "id"]]);
        $hunts=$this->db->DBQuery($sql)->paginates($page, $limit);
        
        $f3->set('hunts',$hunts);
        $f3->set('page',['title'=>'Applicant hunts', 'desc'=>'']);
        $f3->set('template','pages/message/manage.htm');
    }

    public function compose(\Base $f3){
        if ($f3->exists('POST.to') && $f3->exists('POST.subject') && $f3->exists('POST.message')) {
            // var_dump($f3->get('POST'));exit;
            sleep(3); // login should take a while to kick-ass brute force attacks
            $to=$f3->get('POST.to');
            $to=explode(",", $to);
            $recipients=[];
            foreach($to as $rec){
                $recipients[]=["email"=>$rec, "name"=>""];
            }
            
            $carbonCopy=[];
            if($f3->exists('POST.cc')){
                $cc=$f3->get('POST.cc');
                $cc=explode(",", $cc);
                foreach($cc as $rec){
                    $carbonCopy[]=["email"=>$rec, "name"=>""];
                }
            }
            
            $bcarbonCopy=[];
            if($f3->exists('POST.bcc')){
                $bcc=$f3->get('POST.bcc');
                $bcc=explode(",", $bcc);
                foreach($bcc as $rec){
                    $bcarbonCopy[]=["email"=>$rec, "name"=>""];
                }
            }
            
            
            $subject=$f3->get('POST.subject');
            $body=$f3->get('POST.message');

            $f3->set("data", array(
                "content"=>$body
            ));
            $content = Template::instance()->render( "email/open-content.htm" );
            $mail=SendMail::instance()->send("Deen Services", $recipients, $subject, $content, $carbonCopy, $bcarbonCopy);
            // var_dump($mail);exit;
            \Flash::instance()->addMessage("Mail sent", 'success');
        }
        $f3->set('page',['title'=>'Mails', 'desc'=>'']);
        $f3->set('template','pages/message/compose.htm');
    }

    public function scout(\Base $f3, $params){
        if ($f3->exists('POST.client_id') && $f3->exists('POST.cand_id') && $f3->exists('POST.remark')) {
            // var_dump($f3->get('POST'));exit;
            sleep(3); // login should take a while to kick-ass brute force attacks
            $cand=$f3->get('POST.cand_id');
            $client=$f3->get('POST.client_id');
            $remark=$f3->get('POST.remark');
            $scout=$this->db->DBSelect("scouts", array("client_id"=>$client, "cand_id"=>$cand))->first();
            if($scout){
                \Flash::instance()->addMessage("You have already requested for this applicant before.", 'warning');
            }else{
                $payload=[
                    "client_id"=>$client,
                    "cand_id"=>$cand,
                    "remark"=>$remark
                ];
                $row=$this->db->DBInsert("scouts", $payload, array("client_id", "cand_id"));
                if($row->resp){
                    $cand=$this->db->DBSelect("candidates", array("id"=>$cand))->first();
                    $client=$this->db->DBSelect("clients", array("id"=>$client))->first();
                    $f3->set("data", array(
                        "name"=>"Admin",
                        "staff"=>$cand->name,
                        "client"=>$client->names
                    ));
                    $content = Template::instance()->render( "email/hunt.htm" );
                    $mail=SendMail::instance()->send("No-reply", ["email"=>"hr@deenservices.com", "name"=>"Admin"], "Applicant Hunt", $content);
                    \Flash::instance()->addMessage("Hunt request sent to the Administrator.", 'success');
                }else{
                    \Flash::instance()->addMessage($row->message, 'danger');
                }    
            }
        }
        $f3->reroute("candidates/".$f3->get('POST.cand_id'));
    }

    public function performs(\Base $f3){
        $action = stripslashes(strip_tags($f3->get('POST.hunt_action')));
        $huntId = stripslashes(strip_tags($f3->get('POST.hunt_id')));
        $hunt=$this->db->DBSelect("scouts", array("id"=>$huntId))->first();
        if(!$hunt){
            $payload=[
                "status"=>false,
                "message"=>"No hunt attached to this ID is found."
            ];
            $this->Response(200, $payload);exit;
        }

        $row=[];
        if($action=='DELETE'){
            $row=$this->db->DBDelete("scouts", array('id'=>$huntId));
        }else{
            $row=$this->db->DBUpdate("scouts", array("status"=>$action), array('id'=>$huntId), array('status'));
        }
        
        if($row->resp){ 
            $payload=[
                "status"=>true,
                "message"=>"Scout processed successfully"
            ];
            $this->Response(200, $payload);exit;
        }
        $payload=[
            "status"=>false,
            "message"=>$row->message
        ];
        $this->Response(200, $payload);exit;
    }
}