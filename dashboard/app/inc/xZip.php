<?php
class xZip {
    public function __construct() {}
    private function _rglobRead($source, &$array = array()) {
        if (!$source || trim($source) == "") {
            $source = ".";
        }
        foreach ((array) glob($source . "/*/") as $key => $value) {
            $this->_rglobRead(str_replace("//", "/", $value), $array);
        }
    
        foreach ((array) glob($source . "*.*") as $key => $value) {
            $array[] = str_replace("//", "/", $value);
        }
    }
    private function _all($source, $destination){
        $zip = new ZipArchive();
        if ($zip->open("ui/documents.zip", ZIPARCHIVE::CREATE)!==TRUE) {
            return false;
        }
        if(is_dir()){

        }

        if ($handle = opendir($source)){
            
            while (false !== ($file = readdir($handle))) {
                $zip->addFile($dest.'/'.$file);
            }
            $zip->addFile($filename);
            closedir($handle);
            $zip->close();
        }
    }
    private function _zip($array, $part, $destination) {
        $zip = new ZipArchive;
        @mkdir($destination, 0777, true);
    
        if ($zip->open(str_replace("//", "/", "{$destination}/partz{$part}.zip"), ZipArchive::CREATE)) {
            foreach ((array) $array as $key => $value) {
                $zip->addFile($value, str_replace(array("../", "./"), NULL, $value));
            }
            $zip->close();
        }
    }
    public function zip($limit = 500, $source = NULL, $destination = "./") {
        if (!$destination || trim($destination) == "") {
            $destination = "./";
        }
    
        $this->_rglobRead($source, $input);
        $maxinput = count($input);
        $splitinto = (($maxinput / $limit) > round($maxinput / $limit, 0)) ? round($maxinput / $limit, 0) + 1 : round($maxinput / $limit, 0);
    
        for($i = 0; $i < $splitinto; $i ++) {
            $this->_zip(array_slice($input, ($i * $limit), $limit, true), $i, $destination);
        }
        
        unset($input);
        return;
    }
    public function unzip($source, $destination) {
        @mkdir($destination, 0777, true);
    
        foreach ((array) glob($source . "/*.zip") as $key => $value) {
            $zip = new ZipArchive;
            if ($zip->open(str_replace("//", "/", $value)) === true) {
                $zip->extractTo($destination);
                $zip->close();
            }
        }
    }
    
    public function __destruct() {}
}