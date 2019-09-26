<?
	include_once('../inc/_header.php');

	$code	= $_REQUEST['code'];
	$gbn	= $_REQUEST['gbn'];

	if ($gbn == 'ok'){
		$sql = "update closing_result
				   set closing_read_yn = 'Y'
				 where org_no          = '$code'
				   and closing_read_yn = 'N'";
		echo $sql;
		$conn->execute($sql);?>
		<script language='javascript'>
			window.close();
		</script><?
	}
?>
<script language='javascript'>
function result_ok(){
	document.f.action = 'result_show_message.php?gbn=ok';
	document.f.submit();
}
</script>
<div class="title title_border">작업결과 리포트</div>
<div class="title_border" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:230;">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="18%">
		<col width="32%">
		<col width="18%">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head last" colspan="4">수급자 실적 일괄확정 처리내역</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select closing_yymm
				,      closing_dt_f
				,      closing_dt_t
				,      closing_rst
				,      closing_msg
				  from closing_result
				 where org_no          = '$code'
				   and closing_read_yn = 'N'";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<th style="border-top:2px solid #a6c0f3;">처리년월</th>
				<td class="left last" style="border-top:2px solid #a6c0f3;" colspan="3"><?=substr($row['closing_yymm'], 0, 4);?>년 <?=substr($row['closing_yymm'], 4, 2);?>월</td>
			</tr>
			<tr>
				<th>시작일시</th>
				<td class="left"><?=$row['closing_dt_t'];?></td>
				<th>종료일시</th>
				<td class="left last"><?=$row['closing_dt_f'];?></td>
			</tr>
			<tr>
				<th class="bottom">작업결과</th>
				<td class="left last bottom" colspan="3"><?=$row['closing_msg'];?></td>
			</tr><?
		}
	?>
	</tbody>
</table>
</div>
<div style="text-align:right; padding-top:3px; padding-right:5px;">
	<span class="btn_pack m"><button type="button" onclick="result_ok();">확인</button></span>
</div>
<form name="f" method="post">
<input type="hidden" name="code" value="<?=$code;?>">
</form>
<script language='javascript'>
	window.self.focus();
</script>
<?
	include_once('../inc/_footer.php');
?>