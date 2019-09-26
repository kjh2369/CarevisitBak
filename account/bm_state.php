<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">

</script>
<div class="title title_border">수지분석 현황 및 집계</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">분류</th>
			<th class="head">No</th>
			<th class="head">지사명</th>
			<th class="head">설립일자</th>
			<th class="head">매출액</th>
			<th class="head">가구수(요양+목욕)</th>
			<th class="head">cost.1 요양보호사급여(매입가)</th>
			<th class="head">차액(매출액 - cost.1)</th>
			<th class="head">비율(%)</th>
			<th class="head">cost.2(정직원급여 + 간저비 + 기타)</th>
			<th class="head">영업이익(차액 - cost.2)</th>
			<th class="head">영업이익율(%)</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<?
	include_once("../inc/_db_close.php");
?>