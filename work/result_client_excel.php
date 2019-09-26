<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=".$myF->euckr("수급자별 실적내역").".xls" );
	header( "Content-Description: ".$myF->euckr("수급자별 실적내역"));
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div style="font-size:17px; text-align:center; font-weight:bold;"><?=$year;?>년 <?=IntVal($month);?>월 수급자별 실적내역</div>
<table border="0">
	<tr>
		<td colspan="9" style="border:none; text-align:right;">출력일 : <?=Date('Y.m.d');?></td>
	</tr>
	<thead>
		<tr>
			<th style="width:50px; background-color:#EAEAEA; border:0.5pt solid BLACK;">No</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">수급자</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">요양보호사</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">제공서비스</th>
			<th style="width:50px; background-color:#EAEAEA; border:0.5pt solid BLACK;">횟수</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">총금액</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">공단청구액</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">본인부담금</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">근무시간</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">시급</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">급여</th>
			<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">처우개선비</th>
			<th style="width:120px; background-color:#EAEAEA; border:0.5pt solid BLACK;">급여+처우개선비</th><?
			if ($gDomain == 'dolvoin.net'){?>
				<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">이익</th>
				<th style="width:100px; background-color:#EAEAEA; border:0.5pt solid BLACK;">이익율(%)</th><?
			}?>
		</tr>
	</thead><?
	$IsExcel = true;
	include_once('./result_client_search.php');?>
</table>
<div style="font-size:17px; text-align:center; font-weight:bold;"><?=$orgNm;?></div>