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
			<col width="130px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody><?
	//서비스 단가
	$sql = 'select service_code as cd
			,      concat(service_gbn,\'[\',service_conf_time,\'일]\') as nm
			,      service_conf_time as cnt
			,      service_cost as cost
			,      service_from_dt as from_dt
			,      service_to_dt as to_dt
			  from suga_service
			 where org_no       = \'goodeos\'
			   and service_kind = \'3\'';

	$laSugaList = $conn->_fetch_array($sql, 'cd');

	$sql = 'select seq
			,      from_dt
			,      to_dt
			,      svc_val
			  from client_his_baby
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		$cd  = 'VM'.$row['svc_val'].'01';

		foreach($laSugaList as $val){
			if ($val['cd'] == $cd && $val['from_dt'] <= $dt && $val['to_dt'] >= $dt){
				$suga = $val['nm'];
				$cost = $val['cost'];
				$cnt  = $val['cnt'];
				break;
			}
		}

		$amt = $myF->cutOff($cost * $cnt);?>
		<tr>
			<td class="center"><div id=""><?=$myF->dateStyle($row['from_dt'],'.');?></div></td>
			<td class="center"><div id=""><?=$myF->dateStyle($row['to_dt'],'.');?></div></td>
			<td class="center"><div id=""><?=$suga;?></div></td>
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