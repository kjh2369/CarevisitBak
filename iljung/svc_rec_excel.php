<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION["userCenterCode"];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr($year."년 ".$month."월 요양보호사 방문기록지 작성확인").".xls" );
?>
<div style="text-align:center; font-size:20px; font-weight:bold;"><?=$year;?>년 <?=$month;?>월 요양보호사 방문기록지 작성확인</div>
<table>
	<tr>
		<th style="width:70px; border:0.5pt solid BLACK;">직원</th>
		<th style="width:70px; border:0.5pt solid BLACK;">대상자</th>
		<th style="width:50px; border:0.5pt solid BLACK;">구분</th><?
		for($i=1; $i<=31; $i++){?>
			<th style="width:55px; border:0.5pt solid BLACK;"><?=$i;?></th><?
		}?>
		<th style="width:150px; border:0.5pt solid BLACK;">비고</th>
	</tr><?
	$IsExcel = true;
	include_once('./svc_rec_search2.php');?>
</table>
<?
	include_once("../inc/_db_close.php");
?>