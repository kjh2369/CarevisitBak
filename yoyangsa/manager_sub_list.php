<?
	include("../inc/_db_open.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");
	
	$body = $_POST['p_body'];
	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$jumin	= $ed->de($_POST['jumin']);
	$index	= $_POST['index'];
	$value1	= $_POST['value1'];
		
	$width = $index != '33' ? 'width:240px;' : 'width:410px;';
	$col = $index != '33' ? '4' : '6';
?>
<table class="my_table my_green" style='margin-top:2px; <?=$width?>'>
	<colgroup>
		<col width="40px">
		<col width="80px"><?
		if($index == '33'){ ?>
			<col width="150px"><?
		}?>
		<col width="45px">
		<col width="45px"><?
		if($index == '33'){ ?>
			<col width="45px"><?
		}?>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">작성일자</th><?
			if($index == '33'){ ?>
				<th class="head">평가기간</th><?
			}?>
			<th class="head">&nbsp;</th>
			<th class="head">&nbsp;</th><?
			if($index == '33'){ ?>
				<th class="head">&nbsp;</th><?
			}?>
		</tr>
	</thead>
	<tbody>
	<?
		$client_jumin = '';
		$member_jumin = '';

		if ($index == '31'){
			$client_jumin = $ed->en($jumin);
			$sql = "select r200_wrt_date	as date"
				 . "  from r200fsttalk"
				 . " where r200_ccode = '".$code
				 . "'  and r200_mkind = '".$kind
				 . "'  and r200_jumin = '".$jumin
				 . "'"
				 . " order by r200_wrt_date";
		}else if ($index == '37'){
			$client_jumin = $ed->en($jumin);
			$sql = "select r210_date	as date"
				 . ",      r210_seq		as seq"
				 . "  from r210nar"
				 . " where r210_ccode = '".$code
				 . "'  and r210_mkind = '".$kind
				 . "'  and r210_sugup_code = '".$jumin
				 . "'"
				 . " order by r210_date";
		}else if ($index == '33'){
			$member_jumin = $ed->en($jumin);
			$sql = "select count(*)"
				 . "  from r270test"
				 . " where r270_ccode    = '".$code
				 . "'  and r270_mkind	 = '".$kind
				 . "'  and r270_yoy_code = '".$jumin."'";
			$test_cnt = $conn->get_data($sql);

			$sql = "select r270_date	as date"
				 . ",      r270_seq		as seq"
				 . ",      r270_work_from_date as from_date"
				 . ",      r270_work_to_date as to_date"
				 . "  from r270test"
				 . " where r270_ccode    = '".$code
				 . "'  and r270_mkind	 = '".$kind
				 . "'  and r270_yoy_code = '".$jumin
				 . "'"
				 . " order by r270_date";
			
		}else if ($index == '40' || $index == '74' || $index == '75'){
			$client_jumin = $ed->en($jumin);
			$sql = "select r360_date	as date"
				 . ",      r360_seq		as seq"
				 . "  from r360quest"
				 . " where r360_ccode = '".$code
				 . "'  and r360_mkind = '".$kind
				 . "'  and r360_sugupja = '".$jumin
				 . "'  and r360_service_gbn = '".$value1
				 . "'"
				 . " order by r360_date";
		}else if ($index == '41'){
			$client_jumin = $ed->en($jumin);
			$sql = "select r220_date	as date"
				 . ",      r220_seq		as seq"
				 . "  from r220purat"
				 . " where r220_ccode = '".$code
				 . "'  and r220_mkind = '".$kind
				 . "'  and r220_sugupCode = '".$jumin
				 . "'"
				 . " order by r220_date";
		}else if ($index == '47'){
			$member_jumin = $ed->en($jumin);
			$sql = "select r260_date	as date"
				 . ",      r260_seq		as seq"
				 . "  from r260talk"
				 . " where r260_ccode    = '".$code
				 . "'  and r260_mkind    = '".$kind
				 . "'  and r260_yoyangsa = '".$jumin
				 . "'"
				 . " order by r260_date";
		}else if ($index == '81'){
			$client_jumin = $ed->en($jumin);
			$sql = "select r250_date	as date"
				 . ",      r250_seq		as seq"
				 . "  from r250risktoll"
				 . " where r250_ccode = '".$code
				 . "'  and r250_mkind = '".$kind
				 . "'  and r250_sugupja_jumin= '".$jumin
				 . "'"
				 . " order by r250_date";
		}
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i); ?>
			<tr>
				<td class="center"><?=$i+1;?></td>
				<td class="center"><?=$myF->dateStyle($row['date'], '.');?></td><?
				if($index == '33'){ ?>
				<td class="center"><?=$myF->dateStyle($row['from_date'], '.');?>~<?=$myF->dateStyle($row['to_date'], '.');?></td><?
				}?>
				<td class="center">
					<span class="btn_pack m"><button type="button" onclick="_member_report_layer_close(); __my_modal(Array('<?=$kind?>','<?=$row['date'];?>','<?=$ed->en($jumin);?>','<?=$row['seq'];?>','report','input','<?=$index;?>','php','','','<?=$value1;?>'));">수정</button></span>
				</td>
				<td class="center">
					<span class="btn_pack m"><button type="button" onclick="_member_report_layer_close(); showMyReport('<?=$index;?>', '<?=$code;?>', '<?=$kind;?>', '<?=$row['date'];?>', '<?=$client_jumin;?>', '<?=$member_jumin;?>','<?=$row['seq'];?>')">출력</button></span>
				</td><?
				if($index == '33'){ ?>
					<td class="center">
					<span class="btn_pack m"><button type="button" onclick="_member_report_layer_close(); deleteReport('<?=$body?>','','','<?=$index;?>', '<?=$code;?>', '<?=$kind;?>', '','<?=$row['date'];?>','<?=$row['seq'];?>','<?=$member_jumin;?>','<?=$test_cnt?>')">삭제</button></span>
					</td><?
				}?>
			</tr>
			<?	
		}

		$conn->row_free(); ?>
		<tr>
			<td class="right" colspan="<?=$col?>">
				<? 
				if($index != '31'){ ?>
					<span class="btn_pack m"><button type="button" onclick="_member_report_layer_close(); __my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','<?=$index;?>','php','1','2'));">입력</button></span><?
				} ?>
				<span class="btn_pack m"><button type="button" onclick="_member_report_layer_close();">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include("../inc/_db_close.php");
?>