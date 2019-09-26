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
			<col width="110px">
			<col width="70px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody><?
	//서비스 지원금액 및 시간
	$sql = 'select service_code as cd
			,      service_lvl as nm
			,      service_conf_time as cnt
			,      service_conf_amt as amt
			,      service_from_dt as from_dt
			,      service_to_dt as to_dt
			  from suga_service
			 where org_no       = \'goodeos\'
			   and service_kind = \'4\'';

	$laSugaList = $conn->_fetch_array($sql);

	$sql = 'select seq
			,      from_dt
			,      to_dt
			,      svc_val
			,      svc_lvl
			  from client_his_dis
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_val != \'3\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		$cd  = 'VA'.($row['svc_val'] == '1' ? 'A' : 'C').$row['svc_lvl'].'0';

		foreach($laSugaList as $val){
			if ($val['cd'] == $cd && $val['from_dt'] <= $dt && $val['to_dt'] >= $dt){
				$suga = $val['nm'];
				$amt  = $val['amt'];
				$time = $val['cnt'];
				break;
			}
		}?>
		<tr>
			<td class="center"><div id=""><?=$myF->dateStyle($row['from_dt'],'.');?></div></td>
			<td class="center"><div id=""><?=$myF->dateStyle($row['to_dt'],'.');?></div></td>
			<td class="center"><div id=""><?=$suga;?></div></td>
			<td class="center"><div id="val_<?=$i;?>" value="<?=$row['svc_val'];?>" class="right"><?=number_format($amt);?></div></td>
			<td class="center"><div id="lvl_<?=$i;?>" value="<?=$row['svc_lvl'];?>" class="right"><?=$time;?>시간</div></td>
			<td class="center">
				<div class="left">
					<span class="btn_pack m"><button type="button" onclick="doDel('<?=$i;?>');">삭제</button></span>
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