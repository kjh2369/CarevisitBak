<?
	//echo iconv("UTF-8","EUC-KR",$_REQUEST['value'])
	echo iconv("EUC-KR","UTF-8",$_REQUEST['value']);
?>