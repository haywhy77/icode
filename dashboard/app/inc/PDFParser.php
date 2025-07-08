<?php


class PDFParser extends \Prefab {

    /**
    * check environment for requirements
    * @return array
    */
    public function preflight($pdf) {
        /** @var \Base $f3 */
        $f3 = \Base::instance();
       
        if (!file_exists($pdf)) throw "File: $pdf does not exist";
        $parser = new \Smalot\PdfParser\Parser(); 
        // var_dump($parser);exit;
        // Source PDF file to extract text 
    
        // $rawdata = file_get_contents($file);
        // if ($rawdata === false) {
        //     die('Unable to get the content of the file: ' . $file);
        // }
        // Parse pdf file using Parser library 
        $file = $parser->parseFile($pdf); 
        
        // Extract text from PDF 
        // $textContent = $pdf->getText();
        return $file;
    }
}