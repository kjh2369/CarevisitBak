<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="100px">
		<col width="70px">
		<col width="375px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="6">상담이력</th>
		</tr>
		<tr>
			<th class="head">No</th>
			<th class="head">상담일자</th>
			<th class="head">상담자</th>
			<th class="head">상담유형</th>
			<th class="head">처리결과</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>