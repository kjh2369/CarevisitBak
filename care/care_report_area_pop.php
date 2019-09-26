<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$year	= $_POST['year'];
	$month	= $_POST['month	'];
	$area	= $_POST['area'];
?>
<script type="text/javascript">

</script>

<form id="f" name="f" method="post">
<div id="lsTitle" class="title title_border">지역별보고서(<?=($sr == 'S' ? '재가지원' : '자원연계');?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="70px">
		<col width="90px">
		<col width="200px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">연락처</th>
			<th class="head">주소</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
</form>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	include_once('../inc/_footer.php');
?>