<?
	include("../inc/_db_open.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$code  = $_POST['code'];
	$kind  = $_POST['kind'];
	$jumin = $ed->de($_POST['jumin']);
	$index = $_POST['index'];

?>
<table class="my_table my_green" style="width:240px; margin-top:2px;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="60px">
		<col width="60px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">작성일자</th>
			<th class="head">&nbsp;</th>
			<th class="head">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?
		if ($index == '33'){
			$sql = "select r270_date	as date"
				 . ",      r270_seq		as seq"
				 . "  from r270test"
				 . " where r270_ccode    = '".$code
				 . "'  and r270_mkind	 = '".$kind
				 . "'  and r270_yoy_code = '".$jumin
				 . "'"
				 . " order by r270_date";
		}else if ($index == '47'){
			$sql = "select r260_date	as date"
				 . ",      r260_seq		as seq"
				 . "  from r260talk"
				 . " where r260_ccode    = '".$code
				 . "'  and r260_mkind    = '".$kind
				 . "'  and r260_yoyangsa = '".$jumin
				 . "'"
				 . " order by r260_date";
		}
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i); ?>
			<tr>
				<td class="center"><?=$i+1;?></td>
				<td class="center"><?=$myF->dateStyle($row['date'], '.');?></td>
				<td class="center">
					<a href="#" onclick="_member_report_layer_close(); __my_modal(Array('<?=$kind?>','<?=$row['date'];?>','<?=$ed->en($jumin);?>','<?=$row['seq'];?>','report','input','<?=$index;?>','php','1','2'));">수정</a>
				</td>
				<td class="center">
					<a href="#" onclick="_member_report_layer_close(); showMyReport('<?=$index;?>', '<?=$code;?>', '<?=$kind;?>', '<?=$row['date'];?>', '', '<?=$ed->en($jumin);?>','<?=$row['seq'];?>')">출력</a>
				</td>
			</tr><?
		}

		$conn->row_free(); ?>
		<tr>
			<td class="right" colspan="4">
				<a href="#" onclick="_member_report_layer_close(); __my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','<?=$index;?>','php','1','2'));">입력</a> |
				<a href="#" onclick="_member_report_layer_close();">닫기</a>
			</td>
		</tr>
	</tbody>
</table>
<?
	include("../inc/_db_close.php");
?>