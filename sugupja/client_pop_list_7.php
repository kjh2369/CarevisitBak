<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);?>

	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="90px">
			<col width="80px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody><?
	//서비스 단가
	$sql = 'select service_code as cd
			,      service_cost as cost
			,      service_from_dt as from_dt
			,      service_to_dt as to_dt
			  from suga_service
			 where org_no       = \'goodeos\'
			   and service_kind = \'2\'';

	$laSugaList = $conn->_fetch_array($sql, 'cd');

	$sql = 'select seq
			,      from_dt
			,      to_dt
			,      svc_val
			,      svc_tm
			  from client_his_old
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);

		if ($row['svc_val'] == '1'){
			$cd  = 'VOV01';
		}else if ($row['svc_val'] == '2'){
			$cd  = 'VOD01';
		}else if ($row['svc_val'] == '3'){
			$cd  = 'VOS01';
		}


		foreach($laSugaList as $val){
			if ($val['cd'] == $cd && $val['from_dt'] <= $dt && $val['to_dt'] >= $dt){
				$cost = $val['cost'];
				break;
			}
		}

		if ($row['svc_val'] == '1'){
			$svcVal = '방문';
		}else if ($row['svc_val'] == '2'){
			$svcVal = '주간보호';
		}else if ($row['svc_val'] == '3'){
			$svcVal = '단기가사';
		}else{
			$svcVal = $row['svc_val'];
		}

		if ($row['svc_tm'] == '1'){
			#$svcTm   = ($row['svc_val'] == '1' ? '27시간' : '9일');
			#$svcTime = ($row['svc_val'] == '1' ? 27 : 9);
			if ($row['svc_val'] == '1'){
				$svcTm = '27시간';
				$svcTime = 27;
			}else if ($row['svc_val'] == '2'){
				$svcTm = '9일';
				$svcTime = 9;
			}else if ($row['svc_val'] == '3'){
				$svcTm = '1개월';
				$svcTime = 24;
			}
		}else{
			#$svcTm = ($row['svc_val'] == '1' ? '36시간' : '12일');
			#$svcTime = ($row['svc_val'] == '1' ? 36 : 12);
			if ($row['svc_val'] == '1'){
				$svcTm = '36시간';
				$svcTime = 36;
			}else if ($row['svc_val'] == '2'){
				$svcTm = '12일';
				$svcTime = 12;
			}else if ($row['svc_val'] == '3'){
				$svcTm = '2개월';
				$svcTime = 48;
			}
		}

		$amt = $cost * $svcTime;?>
		<tr>
			<td class="center"><div id=""><?=$myF->dateStyle($row['from_dt'],'.');?></div></td>
			<td class="center"><div id=""><?=$myF->dateStyle($row['to_dt'],'.');?></div></td>
			<td class="center"><div id=""><?=$svcVal;?></div></td>
			<td class="center"><div id=""><?=$svcTm;?></div></td>
			<td class="center"><div id="" class="right"><?=number_format($amt);?></div></td>
			<td class="center">
				<div class="left"><?
					if ($rowCount > 1 && $i == 0){?>
						<span class="btn_pack m"><button type="button" onclick="doDel('<?=$i;?>');">삭제</button></span><?
					}?>
					<span id="seq_<?=$i;?>" style="display:none;"><?=$row['seq'];?></span>
				</div>
			</td>
		</tr><?
	}

	$conn->row_free();?>

	</tbody>
	</table><?

	include_once('../inc/_db_close.php');
?>