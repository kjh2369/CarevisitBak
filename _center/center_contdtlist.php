<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];

	$sql = 'SELECT	cont_dt, from_dt, to_dt
			FROM	cv_reg_info
			WHERE	org_no = \''.$orgNo.'\'
			ORDER	BY cont_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<div><label><input name="optContDt" type="radio" class="radio" value="<?=$row['cont_dt'];?>" onclick="$('#txtContDt').val(__getDate($(this).val()));" <?=$i == 0 ? 'checked' : '';?>><?=!$row['cont_dt'] ? '계약일자없음' : '';?><?=$myF->dateStyle($row['cont_dt'],'.');?>[계약기간 : <?=$myF->dateStyle($row['from_dt'],'.');?>~<?=$myF->dateStyle($row['to_dt'],'.');?>]</label></div><?
	}

	$conn->row_free();?>

	<script type="text/javascript">
		$('#txtContDt').val(__getDate($('input:radio[name="optContDt"]:checked').val()));
	</script><?

	include_once('../inc/_db_close.php');
?>