<?php

$version="1.0";
// ---------------------------------------------------------
function has_data($value)
{
 if (is_array($value)) return (sizeof($value) > 0)? true : false;
 else return (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) ? true : false;
}

function xmail ($to_emailaddress,$from_emailaddress, $subject, $content, $file_name, $backup_type, $ver)
{
 $mail_attached = "";
 $boundary = "----=_NextPart_000_01FB_010".md5($to_emailaddress);
 $mail_attached.="--".$boundary.NEWLINE
                       ."Content-Type: application/octet-stream;".NEWLINE." name=\"$file_name\"".NEWLINE
                       ."Content-Transfer-Encoding: base64".NEWLINE
                       ."Content-Disposition: attachment;".NEWLINE." filename=\"$file_name\"".NEWLINE.NEWLINE
                       .chunk_split(base64_encode($content)).NEWLINE;
 $mail_attached .= "--".$boundary."--".NEWLINE;
 $add_header ="MIME-Version: 1.0".NEWLINE."Content-Type: multipart/mixed;".NEWLINE." boundary=\"$boundary\" ".NEWLINE;
 $mail_content="--".$boundary.NEWLINE."Content-Type: text/plain; ".NEWLINE." charset=\"iso-8859-1\"".NEWLINE."Content-Transfer-Encoding: 7bit".NEWLINE.NEWLINE."BACKUP Successful...".NEWLINE.NEWLINE."Please see attached for your zipped Backup file; $backup_type ".NEWLINE."If this is the first backup then you should test it restores correctly to a test server.".NEWLINE.NEWLINE." AutoBackup (version $ver) is developed by http://www.mictexplorers.com/ ".NEWLINE.NEWLINE." Have a good day now you have a backup of your MySQL db  :-) ".NEWLINE.NEWLINE." Please consider making a donation at: ".NEWLINE." http://www.mictexplorers/ ".NEWLINE." (every penny or cent helps)".NEWLINE.$mail_attached;
 return mail($to_emailaddress, $subject, $mail_content, "From: $from_emailaddress".NEWLINE."Reply-To:$from_emailaddress".NEWLINE.$add_header);
}

function write_backup($gzdata, $backup_file_name)
{
 $dir="ui/backups/";
 if(!file_exists($dir)) {
   mkdir($dir,0777);
 }
 $fp = fopen($dir.$backup_file_name, "w");
 fwrite($fp, $gzdata);
 fclose($fp);
 //check folder is protected - stop HTTP access
 if (!file_exists("$dir.htaccess"))
 {
  $fp = fopen("$dir.htaccess", "w");
  fwrite($fp, "deny from all");
  fclose($fp);
 }

}

function delete_old_backups(){
  $files=$keep=array();
  $prefix = 'mysql_'.DBNAME;
  $suffix = ".sql.gz";
  $dir = "ui/backups/";
  if ($handle = opendir($dir)){
     while (false !== ($file = readdir($handle))){
       if ((filetype($dir.$file) == "file") && (substr($file,0,strlen($prefix)) == $prefix) && (substr($file,-strlen($suffix)) == $suffix) && (filesize($dir.$file)>0))
        {
         $files[filemtime($dir.$file)]= $file;
        }
      }
      closedir($handle);
      krsort($files);

      $slice = min(TOTAL_BACKUP_FILES_TO_RETAIN,sizeof($files));
      if ($slice)
       {
        $erase = array_slice($files,TOTAL_BACKUP_FILES_TO_RETAIN);
        foreach ($erase as $key=>$thisOne)
         {
          unlink($dir.$thisOne);
         }
       }
    }
}



class transfer_backup
{
      public $error = "";
      public function transfer_data($ftp_username,$ftp_password,$ftp_server,$ftp_path,$filename)
      {
       if (function_exists('curl_exec'))
       {
        $file=LOCATION."../backups/".$filename;
        $fp = fopen($file, "r");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "ftp://$ftp_username:$ftp_password@$ftp_server.$ftp_path".$filename);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
        curl_setopt($ch, CURLOPT_TRANSFERTEXT, 0);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']." - via AutoBackup");
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        if (empty($info['http_code'])) $this->error = NEWLINE."FTP ERROR - Failed to transfer backup file to remote ftp server";
        else
        {
         $http_codes = parse_ini_file(LOCATION."http_codes.ini");
         if ($info['http_code']!=226) $this->error = NEWLINE.NEWLINE."FTP ERROR - server response: ".NEWLINE.$info['http_code']
                                            ." " . $http_codes[$info['http_code']]
                                            .NEWLINE."for more detail please refer to: http://www.w3.org/Protocols/rfc959/4_FileTransfer.html"                                            ;
        }
        curl_close($ch);

       }
       else $this->error = NEWLINE.NEWLINE."WARNING: FTP will not function as PHP CURL does not exist on your hosting account - try a new host:  http://www.seiretto.com";
       return $this->error;
      }
}


class record
{
  public function save($date, $bytes, $lines)
  {
   $dbc = dbc::instance();
   $result = $dbc->prepare("SHOW TABLES LIKE 'AutoBackup_log' ");
   $rows = $dbc->executeGetRows($result);
   if(count($rows)<1)
   {
    $q="CREATE TABLE IF NOT EXISTS `AutoBackup_log` (
        `date` int(11) NOT NULL,
        `bytes` int(11) NOT NULL,
        `lines` int(11) NOT NULL,
         PRIMARY KEY (`date`) )";
    $result = $dbc->prepare($q);
    $result = $dbc->execute($result);
   }
   $query="INSERT INTO `AutoBackup_log` (`date`, `bytes`, `lines`)
             VALUES ('$date', '$bytes', '$lines')";
   $result = $dbc->prepare($query);
   $result = $dbc->execute($result);
   $query="SELECT date FROM `AutoBackup_log` ORDER BY `date` DESC LIMIT 0 , ".LOG_REPORTS_MAX;
   $result = $dbc->prepare($query);
   $rows = $dbc->executeGetRows($result);

   $search_date=$rows[count($rows)-1]['date'];
   $query="delete FROM `AutoBackup_log` where date<'$search_date' ";
   $result = $dbc->prepare($query);
   $result = $dbc->execute($result);
  }

  public function get()
  {
   $dbc = dbc::instance();
   $result = $dbc->prepare("SELECT * FROM `AutoBackup_log` ORDER BY `date` DESC ");
   $rows = $dbc->executeGetRows($result);
   $report=NEWLINE."Below are the records of the last ".LOG_REPORTS_MAX." backups.".NEWLINE."DATE and TIME (total bytes, Total lines exported)";
   foreach ($rows as $row)
   {
    $report.= NEWLINE.strftime("%d %b %Y - %H:%M:%S",$row['date'])." (";
    $report.= number_format(($row['bytes']/1000), 2, '.', '') ." KB, ";
    $report.= number_format($row['lines'])." lines)";
   }
   return $report;
  }
	public function dropDB(){
		$dbc = dbc::instance();

    $result=$dbc->prepare("SHOW TABLES ");//"DROP DATABASE ".DBNAME
		$rows = $dbc->executeGetRows($result);
		//var_dump($rows);die;
		 if(count($rows)>0)
		 {
			for($i=0;$i<count($rows);$i++){
        //echo $rows[$i]['Tables_in_'.DBNAME];exit;
				if($rows[$i]['Tables_in_'.DBNAME]=='autobackup' || $rows[$i]['Tables_in_'.DBNAME]=='autobackup_log'){
					continue;
				}
				$query = "TRUNCATE TABLE ".$rows[$i]['Tables_in_'.DBNAME];
				$result = $dbc->prepare($query);
				$result = $dbc->execute($result);
			}
		}
	}
}


class dbc extends PDO{
 protected static $instance;
 protected $dbconn;

 public function __construct()
 {
   $options = array(PDO::ATTR_PERSISTENT => true,
   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
   PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".CHARSET."';" // this command will be executed during every connection to server - suggested by: vit.bares@gmail.com
   );
   try {
        $this->dbconn = new PDO(DBDRIVER.":host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME,DBUSER,DBPASS,$options);
        return $this->dbconn;
       }
    catch (PDOException $e){ $this->reportDBError($e->getMessage()); }
 }

 public function reportDBError($msg)
 {
  if (DEBUG) print_r('<div style="padding:10%;"><h3>'.nl2br($msg).'</h3>(debug ref. 3.9d)</div>');
  else
  {
   if(!session_id()) session_start();
   $_SESSION['mysql_errors'] = "\n\nDb error: ".$msg."\n";
  }
 }

 public static function instance()
 {
  if (!isset(self::$instance)) self::$instance = new self();
  return self::$instance;
 }

 public function prepare($query, $options = NULL) {
  try { return $this->dbconn->prepare($query); }
   catch (PDOException $e){ $this->reportDBError($e->getMessage()); }
 }

 public function bindParam($query) {
  try { return $this->dbconn->bindParam($query); }
   catch (PDOException $e){ $this->reportDBError($e->getMessage()); }
 }

 public function query($query) {
  try {
       if ($this->query($query)) return $this->fetchAll();
       else return 0;
      }
   catch (PDOException $e){ $this->reportDBError($e->getMessage()."<hr>".$e->getTraceAsString()); } }

 public function execute($result) {//use for insert/update/delete
  try { if ($result->execute()) return $result; }
   catch (PDOException $e){ $this->reportDBError($e->getMessage()."<hr>".$e->getTraceAsString()); }
 }
 public function executeGetRows($result) {//use to retrieve rows of data
  try {
       if ($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC);
       else return 0;
      }
    catch (PDOException $e){ $this->reportDBError($e->getMessage()."<hr>".$e->getTraceAsString()); }
 }

 public function __clone()
 {  //not allowed
 }
 public function __destruct()
 {
  $this->dbconn = null;
 }
}
