<? 
$filename = iconv("UTF-8","EUCKR",$_REQUEST['fileName']);
$file = '../userFiles/'.$filename;

global $HTTP_USER_AGENT; 
$check_result = file_exists($file); 

if ($check_result != 0 && $filename != ""){ // 파일이 실제로 존재하면 
    if(eregi("(MSIE 5.5|MSIE 6.0)", $HTTP_USER_AGENT)){ 
		$fp = fopen($file,"r");     
		$buffer = fread($fp,filesize($file)); 
		fclose($fp); 
		Header("Content-type: application/octet-stream"); 
		Header("Content-Length: ".filesize("$file"));
		Header("Content-Disposition: attachment; filename=$filename"); 
		Header("Content-Transfer-Encoding: binary"); 
		Header("Pragma: no-cache"); 
		Header("Expires: 0"); 
    }else{ 
		$fp = fopen($file,"r"); 
		$buffer = fread($fp,filesize($file)); 
		fclose($fp); 
		Header("Content-type: application/octet-stream"); 
		Header("Content-Length: ".filesize("$file"));
		header("Content-Disposition: attachment; filename=$filename"); 
		Header("Content-Transfer-incoding: euc_kr");  
		Header("Content-Transfer-Encoding: binary"); 
		Header("Content-Description: down"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
	} 
    echo $buffer; 
    exit; 
}else{ //파일이 존재하지 않으면 
    $errmsg = "파일이 삭제 되었거나 존재하지 않습니다. ".$file; 
    echo $errmsg; 
    exit; 
}
?>