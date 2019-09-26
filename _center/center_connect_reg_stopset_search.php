<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];

	$sql = 'SELECT	*
			FROM	stop_set
			WHERE	org_no	= \''.$orgNo.'\'
			ORDER	BY stop_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['stop_gbn'] == '1'){
			$stopGbn = '중지';
		}else if ($row['stop_gbn'] == '2'){
			$stopGbn = '미납';
		}else{
			$stopGbn = $row['stop_gbn'];
		}

		if ($row['stop_yn'] == 'Y'){
			$stat = '<span style="color:RED;">중지</span>';
		}else if ($row['cls_yn'] == 'Y'){
			$stat = '<span style="color:BLUE;">'.($row['stop_gbn'] == '1' ? '중지' : '미납').'해제</span>';
		}else{
			if ($row['stop_gbn'] == '1'){
				$stat = '중지설정';
			}else if ($row['stop_gbn'] == '2'){
				$stat = '미납설정';
			}else{
				$stat = '-';
			}
		}?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$myF->dateStyle($row['stop_dt'],'.');?></td>
			<td class="center"><?=$stopGbn;?></td>
			<td class=""><div class="left nowrap" style="width:70px;" title="<?=$row['def_txt'];?>"><?=$row['def_txt'];?></div></td>
			<td class=""><div class="right"><?=number_format($row['def_amt']);?></div></td>
			<td class="center"><?=$stat;?></td>
			<td class="center"><?=$myF->dateStyle($row['close_dt'],'.');?></td>
			<td class="center"><?=$row['memo'] ? '<span title="'.$row['memo'].'">▤</span>' : '';?></td>
			<td class="last">
				<span class="btn_pack small" style="margin-left:5px;"><button onclick="$('#txtDate').val('<?=$myF->dateStyle($row['stop_dt']);?>'); $('#txtClsDt').val('<?=$myF->dateStyle($row['close_dt']);?>'); $('#txtTxt').val('<?=$row['def_txt'];?>'); $('#txtAmt').val('<?=number_format($row['def_amt']);?>'); $('#txtSeq').val('<?=$row['seq'];?>'); $('input:radio[name=\'optGbn\'][value=\'<?=$row['stop_gbn'];?>\']').attr('checked',true); $('#txtMemo').val('<?=$row['memo'];?>'); $('#txtOther').val('<?=stripslashes(str_replace(chr(13).chr(10), '\N', $row['other']));?>'.replace('\N',String.fromCharCode(13)+String.fromCharCode(10)));">수정</button></span><?

				if ($row['stop_yn'] == 'N' && $row['cls_yn'] == 'N'){?>
					<span class="btn_pack small"><button onclick="lfStopSet('Y','<?=$row['seq'];?>');">해제</button></span><?
				}else{?>
					<span class="btn_pack small"><button style="color:RED;" onclick="lfStopSet('D','<?=$row['seq'];?>');">삭제</button></span><?
				}?>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>