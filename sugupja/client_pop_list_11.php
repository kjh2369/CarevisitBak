<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];

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
	//서비스 리스트
	$sql = 'select seq
			,      from_dt
			,      to_dt
			,      svc_val
			,      svc_lvl
			  from client_his_dis
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_val = \'3\'';
	

	$laSvcList = $conn->_fetch_array($sql);

	//본인부담금 리스트
	$sql = 'select left(person_code,3) as cd
			,      person_id as val
			,      person_amt1 as amt1
			,      person_amt2 as amt2
			,      person_amt3 as amt3
			,      person_amt4 as amt4
			,      person_amt5 as amt5
			,      person_amt6 as amt6
			,      person_from_dt as from_dt
			,      person_to_dt as to_dt
			  from suga_person
			 where org_no = \'goodeos\'';

	$laExpense = $conn->_fetch_array($sql);

	$sql = 'select seq
			,      from_dt
			,      to_dt
			,      level
			  from client_his_lvl
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \''.$svcCd.'\'
			   and app_no = \'3\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		
		if($laSvcList != ''){
			foreach($laSvcList as $svc){
				if ($svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					$val  = $svc['svc_lvl'];
					$suga = 'VB0';
					break;
				}
			}
		}

		foreach($laExpense as $svc){
			if ($svc['cd'] == $suga && $svc['val'] == $val && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
				$amt = $svc['amt'.$row['level']];
				break;
			}
		}

		if($row['level'] == '1'){
			$lvlNm = '생계의료급여수급자';
		}else if($row['level'] == '2'){
			$lvlNm = '차상위계층';
		}else if($row['level'] == '3'){
			$lvlNm = '70%이하';
		}else if($row['level'] == '4'){
			$lvlNm = '120%이하';
		}else if($row['level'] == '5'){
			$lvlNm = '180%이하';
		}else if($row['level'] == '6'){
			$lvlNm = '180%초과';
		}

		?>
		<tr>
			<td class="center"><span id=""><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
			<td class="center"><span id=""><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
			<td class="center"><div id="lvl_<?=$i?>" value="<?=$row['level'];?>" class="left"><?=$lvlNm;?></div></td>
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