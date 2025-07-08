<?php
define('TOTAL_BACKUP_FILES_TO_RETAIN',1);
define ('CHARSET',"utf8");
define('NEWLINE',"\n");
define('DEBUG', 0);//set to 0 when done testing
define('LOCATION', dirname(__FILE__) ."/ui/");
if (DEBUG){
  error_reporting(E_ALL);
}else {
  error_reporting(0);
}

include("backitup/extras.php");
define('DBDRIVER', 'mysql');
define('DBPORT', '3306');
// No more changes required below here
// ---------------------------------------------------------
define('DBHOST', "localhost");
class Backup{
  protected $newline= "\r\n" ;
  protected $from_emailaddress = "mictexplorers@gmail.com";
  protected $report_emailaddress = "adewumiadedeji27@gmail.com";
  protected $to_emailaddress = "adewumiadedeji27@gmail.com";
  protected $send_email_backup=1;
  protected $send_email_report=1;
  protected $log_reports_max=6;
  protected $dropDB=0;
  protected $time_interval=3600;
  protected $table_select=array();
  protected $table_exclude=array();
  protected $version='1.0';
  protected $recordBackup;
  protected $save_backup_zip_file_to_server=1;
  protected $ftp_username="";
  protected $ftp_password="";
  protected $ftp_server="";
  protected $ftp_path="/public_html/";
  protected $dbc;
  protected $limit_to=10000000; //total rows to export - IF YOU ARE NOT SURE LEAVE AS IS
  protected $limit_from=0; //record number to start from - IF YOU ARE NOT SURE LEAVE AS IS

  public function __construct($db, $name, $password, $drop=0){
    if($db=='' || $name =='' || $password==''){
      echo "Database connection parameters must be submitted."; exit;
    }
    $this->dropDB=$drop;
    define('DBUSER', $name);
    define('DBPASS', $password);
    define('DBNAME', $db);
  }
  public function startBackup(){
    $this->dbc=dbc::instance();;

    if($this->dbc=="" OR $this->dbc==null)//OR($mysql_password==""))
    {
     echo "Configure your installation BEFORE running, add your details to the file /setup";exit;
    }

    $backup_type="\n\n BACKUP Type: Full database backup (all tables included)\n\n";
    
    if (!empty($this->table_select)){
     $backup_type="\n\n BACKUP Type: partial, includes tables:\n";
     foreach ($this->table_select as $key => $value) $backup_type.= "  $value;\n";
    }

    if (!empty($this->table_exclude)){
     $backup_type="\n\n BACKUP Type: partial, EXCLUDES tables:\n";
     foreach ($this->table_exclude as $key => $value) $backup_type.= "  $value;\n";
    }
    $errors="";

    $this->checkTable();
    $buffer=$this->backItUpNow();
    
    if ($buffer==''){
      echo "Empty database cannot be backed up. Database must have atleat 1/2 tables.";exit;
    }
    $backup_info="\n".$this->version."\n\n";

    $backup_info.=$backup_type;

    $backup_info.= $this->recordBackup->get();

    // zip the backup and email it
    $backup_file_name = 'mysql_'.DBNAME.strftime("_%d_%b_%Y_time_%H_%M_%S.sql",time()).'.gz';
    $dump_buffer = gzencode($buffer);

    write_backup($dump_buffer, $backup_file_name);
    if($this->dropDB==1){
    	$this->recordBackup->dropDB();
    }
    //FTP backup file to remote server

    if (!empty($this->ftp_username))
    {
      $transfer_backup = new transfer_backup();
      $errors.= $transfer_backup->transfer_data($this->ftp_username,$this->ftp_password,$this->ftp_server,$this->ftp_path,$backup_file_name);
     if (!$this->save_backup_zip_file_to_server) unlink(LOCATION."ui/backups/".$backup_file_name);
    }

    if(!session_id()) session_start();
    if(isset($_SESSION['pmab_mysql_errors'])) $errors.=$_SESSION['pmab_mysql_errors'];

    if ($this->send_email_backup) xmail($this->to_emailaddress,$this->from_emailaddress, "AutoBackup: $backup_file_name", $dump_buffer, $backup_file_name, $backup_type, $this->version);
    if ($this->send_email_report)
    {
     $msg_email_backup="";
     $msg_ftp_backup="";
     $msg_local_backup="";

     if ($this->send_email_backup) $msg_email_backup="\nthe email with the backup attached has been sent to: $this->to_emailaddress \n";
     if ($this->ftp_username !='') $msg_ftp_backup="\nthe backup zip file has been transferred via ftp to: $this->ftp_server (user: $this->ftp_username ) - folder: $this->ftp_path \n";
     if ($this->save_backup_zip_file_to_server) $msg_local_backup="\nthe backup zip file has been saved to the same server: ".dirname(__FILE__)."/backups/ \n";
     if ($errors=="") $errors="None captured.";
     mail($this->report_emailaddress,
                                      "REPORT on recent backup using AutoBackup ($backup_file_name)",
                                      "ERRORS: $errors \nSAVE or DELETE THIS MESSAGE - no backup is attached $msg_email_backup $msg_ftp_backup $msg_local_backup \n$backup_info \n AutoBackup (version $this->version) is developed by http://www.mictexplorers.com/ \nPlease consider making a donation at:\n http://mictexplorers.com \n(every penny or cent helps)",
                                      "From: $this->from_emailaddress\nReply-To:$this->from_emailaddress");
    }
    delete_old_backups();
    //if (DEBUG) echo '<H1>WARNING: DEBUG is on! To turn off edit run.php and set DEBUG to 0 (zero)</H1>Report below:<br><textarea cols=150 rows=50>'."\n\nERRORS: ".$errors.$backup_info.'</textarea>';

  }
  private function checkTable(){

    if (empty($this->table_select))
    {
      $result = $this->dbc->prepare("show tables");
      $i=0;
      $table="";
      $tables = $this->dbc->executeGetRows($result);

      foreach ($tables as $table_array)
      {
       list(,$table) = each($table_array);
       $exclude_this_table = !empty($this->table_exclude)? in_array($table, $this->table_exclude) : false;
       if(!$exclude_this_table) $this->table_select[$i]=$table;
       $i++;
       //echo "$table<br>";
      }
    }
  }
  private function backItUpNow(){
    $this->recordBackup = new record();
    
    $thedomain = $_SERVER['HTTP_HOST'];
    if (substr($thedomain,0,4)=="www.") $thedomain=substr($thedomain,4,strlen($thedomain));

    $buffer = '# MySQL backup created by phpautobackup - Version: '.$this->version . NEWLINE .
              '# ' . NEWLINE .
              '# http://www.mictexplorers.com/' . NEWLINE .
              '#' . NEWLINE .
              '# Database: '. $db . NEWLINE .
              '# Domain name: ' . $thedomain . NEWLINE .
              '# (c)' . date('Y') . ' ' . $thedomain . NEWLINE .
              '#' . NEWLINE .
              '# Backup START time: ' . strftime("%H:%M:%S",time()) . NEWLINE.
              '# Backup END time: #autobackup-endtime#' . NEWLINE.
              '# Backup Date: ' . strftime("%d %b %Y",time()) . NEWLINE;

    $i=0;
    $lines_exported=0;
    $alter_tables="";
    foreach ($this->table_select as $table){
      $i++;
      $export = ' '.NEWLINE.'drop table if exists `' . $table . '`; ' . NEWLINE;

      //export the structure
      $query='SHOW CREATE TABLE `' . $table . '`';
      $result = $this->dbc->prepare($query);
      $tables = $this->dbc->executeGetRows($result);
      $this_table=$tables[0]['Create Table'];
      //var_dump($this_table);exit;
      //$export.= print_r($tables) ."; \n";
      $alter_table="";
      if (preg_match('@^[\s]*CONSTRAINT|FOREIGN[\s]+KEY@',$this_table))
      {
       // change line end char to NEWLINE
       if (strpos($this_table, "(\r\n ")) $this_table = str_replace("\r\n", NEWLINE, $this_table);
       elseif (strpos($this_table, "(\n ")) $this_table = str_replace("\n", NEWLINE, $this_table);
       elseif (strpos($this_table, "(\r ")) $this_table = str_replace("\r", NEWLINE, $this_table);

       $sql_lines = explode(NEWLINE, $this_table);
       $sql_count = count($sql_lines);
       // find constraints
       for ($j = 0; $j < $sql_count; $j++)
       {
        if (preg_match('@^[\s]*(CONSTRAINT|FOREIGN[\s]+KEY)@', $sql_lines[$j]) === 1)
           {
           //the following was gratefully received from: vit.bares@gmail.com
           // if more than one constraint in table, we would have ADD CONSTRAINT command ending with ","
           // which is SQL syntax error
           $sql_lines[$j] = str_replace(',', '', $sql_lines[$j]);
           $alter_table.= 'ALTER TABLE `' . $table . '` ADD ' . $sql_lines[$j] . ';' . NEWLINE;

           // if more than one constraint in table, replace rule with comma does not work for at least one constraint
           $needles = array(
               "," . NEWLINE . $sql_lines[$j],
               NEWLINE . $sql_lines[$j]
           );
           //the above was gratefully received from: vit.bares@gmail.com
           $this_table = str_replace($needles, "", $this_table);
        }
       }
       $alter_tables.=NEWLINE.$alter_table;
      }
      $export.= $this_table.';'.NEWLINE;
      //echo $export;exit;
      $table_list = array();
      $result = $this->dbc->prepare('show fields from  `' . $table . '`');
      $fields = $this->dbc->executeGetRows($result);
      foreach ($fields as $field_array) $table_list[] = $field_array['Field'];

      $buffer.=$export;

      // dump the data
      $query='select * from `' . $table . '` LIMIT '. $this->limit_from .', '. $this->limit_to.' ';

      $result = $this->dbc->prepare($query);
      $rows = $this->dbc->executeGetRows($result);
        
      foreach ($rows as $row_array)
      {
        $export = 'insert into `' . $table . '` (`' . implode('`, `', $table_list) . '`) values (';

        $lines_exported++;
        reset($table_list);
        while (list(,$i) = each($table_list))
        {
          if (!isset($row_array[$i])) $export .= 'NULL, ';
          elseif (has_data($row_array[$i]))
          {
            $row = addslashes($row_array[$i]);
            $row = str_replace("\n#", "\n".'\#', $row);
            $export .= '\'' . $row . '\', ';
          }
          else $export .= '\'\', ';
        }
        $export = substr($export,0,-2) . ");".NEWLINE;
        
        $buffer.= $export;
      }
    }


    //echo $buffer;exit;
    $buffer.=$alter_tables;
    $this->recordBackup->save(time(), strlen($buffer), $lines_exported);
    $buffer = str_replace('#autobackup-endtime#', strftime("%H:%M:%S",time()), $buffer);

    return $buffer;
  }
}
?>
