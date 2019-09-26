<?php 
header('Content-type: text/html; charset=utf-8'); // 문자셋 
require_once 'excel/reader.php'; 
$data = new Spreadsheet_Excel_Reader(); 
$data->setOutputEncoding('UTF-8'); // 문자셋 
$data->read('1.xls'); 
error_reporting(E_ALL ^ E_NOTICE); 

for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) { 
    for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) { 
		if ( $j == 5 or $j == 9) {
			echo "\"".get_words_string($data->sheets[0]['cells'][$i][$j])."\","; 
		} else {
			echo "\"".$data->sheets[0]['cells'][$i][$j]."\","; 
		}
    } 
    echo "\n<br>"; 
} 

function get_words_string($value){
	$return_string = "";
	$acs_code = 0;
	for($k=1; $k<strlen($value); $k++){
		$acs_code = ord(substr($value, $k, 1));
		
		if(($acs_code >= ord("a") and $acs_code <= ord("z")) or 
		   ($acs_code >= ord("A") and $acs_code <= ord("Z"))){
			$return_string .= substr($value, $k, 1);
		}else{
			//$return_string .= "[".ord(substr($value,$k,1))."]";
		}
	}

	return $return_string;
}
?> 
