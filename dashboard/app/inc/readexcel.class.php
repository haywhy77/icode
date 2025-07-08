<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("reader.php");
class ReadExcel
{
	var $allow_url_override = 1;
	var $file_to_include;
	var $max_rows = 0;
	var $max_cols = 0;
	var $debug = 0;
	var $force_nobr = 1;
	var $no_of_rows = 0;
	public function ReadExcel($file){
		$this->SetParam($file);
		
	}
	private function SetParam($file){
		if(!$this->allow_url_override || !isset($this->file_to_include)){
			$this->file_to_include=$file;
		}
		if(!$this->allow_url_override || !isset($this->max_rows)){
			$this->max_rows = 0; //USE 0 for no max
		}
		if(!$this->allow_url_override || !isset($this->max_cols)){
			$this->max_cols = 5; //USE 0 for no max
		}
		if(!$this->allow_url_override || !isset($this->debug)){
			$this->debug = 0;  //1 for on 0 for off
		}
		if(!$this->allow_url_override || !isset($this->force_nobr)){
			$this->force_nobr = 1;  //Force the info in cells not to wrap unless stated explicitly (newline)
		}	
	}

	private function make_alpha_from_numbers($number){
		$numeric = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if($number<strlen($numeric))
		{
			return $numeric[$number];
		}
		else
		{
			$dev_by = floor($number/strlen($numeric));
			return "" . $this->make_alpha_from_numbers($dev_by-1) . $this->make_alpha_from_numbers($number-($dev_by*strlen($numeric)));
		}
	}
	public function WriteFileWithOption(){
		$data=new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CPa25a');
		$data->read($this->file_to_include);
		$table_output='';
		//var_dump($data->sheets[0]['numCols']);exit;
			$table_output .= '<table  class="table table-striped table-bordered table-hover"><tr><td>&nbsp;</td>';
			$_SESSION['excel']=$data->sheets[0];
			for($i=0;($i<$data->sheets[0]['numCols']&&($i<=$this->max_cols||$this->max_cols==0));$i++)
			{
				$table_output .= '<th ALIGN=CENTER><input type="checkbox" name="cols[]" value="'.$i.'">'
				.'('.$this->make_alpha_from_numbers($i).')'.nl2br(htmlentities(isset($data->sheets[0]['cells'][1][$i+1])?$data->sheets[0]['cells'][1][$i+1]:'')).
				'</th>';
			}
			$table_output .="</tr>";
			//var_dump($data->sheets[0]['cellsInfo']);exit;
			
				for($row=2;($row<=$data->sheets[0]['numRows']&&($row<=$this->max_rows||$this->max_rows==0));$row++)
				{
					$this->no_of_rows +=1;
					try{
						//throw new Exception('');
						$table_output .= '<tr><td><input type="checkbox" name="rows[]" value="'.$row.'">' . $row . '</td>';
						for($col=1;($col<=$data->sheets[0]['numCols']&&($col<=$this->max_cols||$this->max_cols==0));$col++)
						{
							try{
								//throw new Exception('');
	
								$table_output .= '<td>';
								
								$table_output .= nl2br(htmlentities(isset($data->sheets[0]['cells'][$row][$col])?$data->sheets[0]['cells'][$row][$col]:''));
								
								$table_output .= '</td>';
							
							}catch(Exception $e) {
								continue;
								//echo 'Caught exception: '.  $e->getMessage(). "\n<br>";
							}
						}
						$table_output .= '</tr>';
					}catch(Exception $e) {
						continue;
						//echo 'Caught exception: '.  $e->getMessage(). "\n<br>";
					}
				}
			
				
			$table_output .='</table>';
			$table_output = str_replace("\n","",$table_output);
			$table_output= str_replace("\r","",$table_output);
			$table_output= str_replace("\t"," ",$table_output);
			$table_output="<div>$table_output</div>";
			return $table_output;
	}
}
?>