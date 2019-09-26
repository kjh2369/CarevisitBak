<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$month = (intval($month) < 10 ? '0' : '').intval($month);
	$gbn   = $_POST['gbn'];
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function list(){
	var f = document.f;

	f.action = 'result_status.php';
	f.submit();
}

-->
</script>

<div class="title">일괄확정 및 급여계산 실행 기록</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">마감년월</th>
			<td class="left"><?=$year;?>년 <?=$month;?>월</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="list();">이전</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="150px">
		<col width="80px">
		<col width="70px">
		<col width="80px">
		<col width="70px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">순번</th>
			<th class="head">실행내용</th>
			<th class="head">작업일자</th>
			<th class="head">시작시간</th>
			<th class="head">종료일자</th>
			<th class="head">종료시간</th>
			<th class="head">작업결과</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select job_type
				,      job_start_dt
				,      job_start_tm
				,      job_end_dt
				,      job_end_tm
				,      err_cd
				,      err_msg
				  from batch_job_log
				 where org_no    = '$code'
				   and stnd_yymm = '$year$month'";

		if ($gbn != 'all'){
			if ($gbn == '1'){
				$sql .= " and job_type in ('1', '3')";
			}else{
				$sql .= " and job_type in ('2')";
			}
		}

		$sql .= "
				 order by job_start_dt desc, job_start_tm desc";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($row['err_cd'] == 0){
				$result = 'OK';
				$result_msg = '';
				$font_color = '';
			}else{
				$result = 'ERROR';
				$result_msg = 'CODE : '.$row['err_cd'];
				$font_color = '#ff0000';
			}?>
			<tr>
				<td class="center"><?=$i+1;?></td>
				<td class="left"><?=$conn->get_job_name($row['job_type']);?></td>
				<td class="center"><?=$row['job_start_dt'];?></td>
				<td class="center"><?=$row['job_start_tm'];?></td>
				<td class="center"><?=$row['job_end_dt'];?></td>
				<td class="center"><?=$row['job_end_tm'];?></td>
				<td class="center" style="color:<?=$font_color;?>;"><?=$result;?></td>
				<td class="left last"><?=$result_msg;?></td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="8"><?=$myF->message($row_count, 'N');?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="year"  value="<?=$year;?>">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>