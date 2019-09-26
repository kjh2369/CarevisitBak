<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/


	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$excel_yn = 'Y';
	$tableBorderStyle = 'border=\'1\'';
?>
<div style="font-size:14pt;">요양보호사 근무시간 <?=$myF->_styleYYMM($fromDt,'.');?>~<?=$myF->_styleYYMM($toDt,'.');?></div>
<table class="my_table" <?=$tableBorderStyle?> >
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="80px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">입사일</th>
			<th class="head">근무개월</th>
			<th class="head">요양</th>
			<th class="head">목욕</th>
			<th class="head">간호</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
<?
		include_once('./mem_work_time_search.php');
?>
	</tbody>
</table>

<?

	include_once('../inc/_db_close.php');
?>