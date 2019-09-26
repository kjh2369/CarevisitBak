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
			<col width="80px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody><?
	//서비스 리스트
	$sql = 'select person_code as cd
			,      person_id as val
			,      person_conf_time as time
			,      person_from_dt as from_dt
			,      person_to_dt as to_dt
			  from suga_person
			 where org_no              = \'goodeos\'
			   and left(person_code,3) = \'VH0\'';

	$laSvcList = $conn->_fetch_array($sql);

	//서비스단가
	$sql = 'select service_cost as cost
			,      service_from_dt as from_dt
			,      service_to_dt as to_dt
			  from suga_service
			 where org_no       = \'goodeos\'
			   and service_kind = \'1\'
			   and service_code = \'VH001\'';

	$laSvcCost = $conn->_fetch_array($sql);

	$sql = 'select seq
			,      from_dt
			,      to_dt
			,      svc_val
			  from client_his_nurse
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);

		$time = 0;
		$cost = 0;
		$amt  = 0;

		foreach($laSvcList as $svc){
			if ($svc['val'] == $row['svc_val'] && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
				$time = $svc['time'];
				break;
			}
		}

		foreach($laSvcCost as $svc){
			if ($svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
				$cost = $svc['cost'];
				break;
			}
		}

		$amt = $cost * $time;
		
		
		if($time == 40){
			$amt -= 5000;
		}

		?>
		<tr>
			<td class="center"><span id=""><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
			<td class="center"><span id=""><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
			<td class="center"><span id="" class="center"><?=$time;?>시간</span></td>
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