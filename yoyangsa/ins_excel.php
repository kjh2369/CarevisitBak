<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');

	//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	/*
	header( "Content-type: application/vnd.ms-word" ); 
	header( "Content-Disposition: attachment; filename=test.doc" );
	header( "Content-Transfer-Encoding: binary" ); 
	header( "Content-Description: PHP4 Generated Data" ); 
	*/
	header( "Content-type: application/vnd.ms-excel" ); 
	header( "Content-Disposition: attachment; filename=test.xls" ); 
	header( "Content-Transfer-Encoding: binary" ); 
	header( "Content-Description: PHP4 Generated Data" ); 
?>
<table width="600" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td style="text-align:center; font-size:20pt; font-weight:bold; padding-bottom:10px; border-bottom:2px solid #000;" colspan="6">현대해상보험(주)</td>
</tr>
</table>
<?
	include('../inc/_db_close.php');
?>