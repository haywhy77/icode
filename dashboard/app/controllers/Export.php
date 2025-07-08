<?php
require('app/inc/functions.php');
class Export extends Controller{
    public function index(\Base $f3, $params){
        $sql=$f3->get('SESSION.query.sql');
        $exclude=$f3->get('SESSION.query.exclude');
        $title=$f3->get('SESSION.caption') || "Adedeji richards";
        $records=$this->db->DBQuery($sql)->all();
        $extension=$params["extention"];
        $records=(Array)$records;

        // var_dump($records[0]);exit;
        if($records){
            $dir=$f3->DOWNLOAD;
            if(!is_dir($dir)){
                mkdir($dir);
                chmod($dir, 0755);
            }
            if(!is_writable($dir)){
                chmod($dir, 0777);
                // $payload=[
                //     "status"=>true,
                //     "message"=>sprintf("Warning:'%s' is not writable!", $dir),
                // ];
                // $this->Response(200, $payload);exit;
            }
            //sanitze records
            
            // var_dump($records);exit;
            $headers=array_keys($records[0]);
            if($exclude)
                $headers = array_diff($headers, $exclude);
            // var_dump($headers);exit;
            $headers=array_flip($headers);
            $headers = array_change_key_case($headers, CASE_UPPER);
            $headers = array_flip($headers);
            
            
            $filename=$dir.$this->code_generator("{$extension}_").".{$extension}";
            // echo $filename;exit;
            switch($extension){
                case'csv':
                    $rows='';
                    $fp = fopen('php://output', 'wb');
                    foreach ( $headers as $data ) {
                        $rows .= $data.",";
                    }
                    $rows.="\n";
                    
                    foreach ( $records as $line ) {
                        
                        $line=sanitizeArray($line, $exclude);
                        
                        foreach ( $line as $data ) {
                            $rows .= $data.",";
                        }
                        $rows.="\n";
                    }
                    if(file_exists($filename)){
                        unlink($filename);
                    }
                    $fp = fopen($filename, 'w+');
                    fwrite($fp, $rows); /** Once the data is written it will be saved in the path given */
                    fclose($fp);
                break;
                case'xls':
                    require_once("app/inc/excelwriter.inc.php");
                    $excel = new ExcelWriter($filename);
                    $excel->open($filename);
                    $caption='<div style="Font-weight:bold; font-size:20px;">'.$f3->get('business').'</div>';
                    $caption.='<div style="Font-weight:bold; font-size:15px;">'.$title.'</div>';
                    $excel->writeHeader($caption, count((Array)$headers),array('text-align'=>'center'));
                    $excel->writeLine($headers, array('text-align'=>'center','font-size'=> '14px'));
                    // var_dump($caption);exit;
                    //$excel->writeRow();
                    // var_dump($excel);exit;
                    $sn=0;
                    foreach($records as $key){
                        $div='';
                        if($sn%2==0){
                            $div='WhiteSmoke';
                        }else{
                            $div='white';
                        }
                        
                        $key=sanitizeArray($key, $exclude);
                       
                        //echo $div;
                        $excel->writeLine($key, array('text-align'=>'left', 'background-color'=>$div, 'font-size'=> '12px'));
                        ++$sn;
                    }
                    
                    $excel->close();
                break;
                case'pdf':
                    
                    $html=array2HTML($headers, $records, $exclude);
                    
                    require('app/inc/html2fpdf/html2fpdf.php');
                    
                    $pdf=new HTML2FPDF('P','A4','fr','true','UTF-8');
                    // $pdf->AddPage();
                    $pdf->WriteHTML("<div style='Font-weight:bold; font-size:20px; color:red;'>".$f3->get('business')."</div>");
                    $pdf->WriteHTML("<div style='Font-weight:bold; font-size:15px;'>".$title."</div>'");
                    // $fp = fopen("app/views/pages/home.htm","r");
                    // $strContent = fread($fp, filesize("app/views/pages/home.htm"));
                    // fclose($fp);
                    //Write table heads
                    $html = ob_get_clean();

                    $pdf->WriteHTML($html);
                    $pdf->SetAuthor("Adedeji");
                    $pdf->SetTitle(mb_strtoupper($title));
                    $pdf->Output($filename, 'F');
                break;
                default:
                    
                    $html=array2HTML($headers, $records, $exclude);
                    // echo $html;exit;
                    $f3->set('content', ["title"=>$title, "data"=>$html]);
                    echo Template::instance()->render('print.htm');die();
            }
            
            
            if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}
            // get the file mime type using the file extension
            // $ext=strtolower(substr(strrchr($filename,'.'), 1));
            $mime="";
            switch($extension) {
                case 'pdf': $mime = 'application/pdf'; break;
                case 'zip': $mime = 'application/zip'; break;
                case 'jpeg':
                case 'jpg': $mime = 'image/jpg'; break;
                case 'csv': $mime = 'text/csv'; break;
                case'xlsx':
                case'xls': $mime='application/vnd.ms-excel';break;
                default: $mime = 'application/force-download';
            }
            
            header('Pragma: public'); 	// required
            header('Expires: 0');		// no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            // header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime($filename)).' GMT');
            header('Cache-Control: private',false);
            header('Content-Type: '. $mime);
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($filename));	// provide file size
            header('Connection: close');
            // echo @exec('hostname -f');
            //echo $filename;exit;
            ob_clean();
            flush();
            //echo $filename;exit;
            readfile($filename);		// push it out
            unlink($filename);
            $payload=[
                "status"=>true,
                "message"=>"Record generated and prepared for download.",
                "payalod"=>[
                    "filename"=>$filename,
                    "extention"=>$extension
                ]
            ];
        }else{
            $payload=[
                "status"=>false,
                "message"=>"Record is empty and cannot be downloaded."
            ];
        }
        $this->Response(200, $payload);exit;
    }
}