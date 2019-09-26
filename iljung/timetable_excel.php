<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
?>
<table class="my_table" border="1" style="width:100%; border-top:1px solid #0e69b0;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col width="150px">
		<col width="90px">
		<col width="150px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">시간</th>
			<th class="head">서비스</th>
			<th class="head">고객명</th>
			<th class="head">직원명</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"><?
	include_once('./timetable_list.php');?>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>