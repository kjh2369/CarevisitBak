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
	switch($svcCd){
		case 1:
			$sql = 'select seq
					,      from_dt
					,      to_dt
					,      svc_val
					  from client_his_nurse
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'';
			break;

		case 2:
			$sql = 'select seq
					,      from_dt
					,      to_dt
					,      svc_val
					,      svc_tm
					  from client_his_old
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'';
			break;

		case 3:
			$sql = 'select seq
					,      from_dt
					,      to_dt
					,      svc_val
					  from client_his_baby
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'';
			break;

		case 4:
			$sql = 'select seq
					,      from_dt
					,      to_dt
					,      svc_val
					,      svc_lvl
					  from client_his_dis
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'';
			
			break;
	}

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
			   and svc_cd = \''.$svcCd.'\'';
	
	if($svcCd == '4'){
		$sql .= ' and (app_no = \'1\' or app_no is null)';
	}	
	
	$sql .= ' order by from_dt desc, to_dt desc';
	
	
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	
	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		
		if($laSvcList != ''){
			foreach($laSvcList as $svc){
				if ($svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
					switch($svcCd){
						case 1:
							$val  = $svc['svc_val'];
							$suga = 'VH0';
							break;

						case 2:
							$val  = $svc['svc_tm'];
							//$suga = 'VO'.($val == '1' ? 'V' : 'D');

							if ($svc['svc_val'] == '1'){
								$suga = 'VOV';
							}else if ($svc['svc_val'] == '2'){
								$suga = 'VOD';
							}else if ($svc['svc_val'] == '3'){
								$suga = 'VOS';
							}

							break;

						case 3:
							$val  = $svc['svc_val'];
							$suga = 'VM0';
							break;

						case 4:
							$val  = $svc['svc_lvl'];
							$suga = 'VA0';
							break;
					}
					break;
				}
			}
		}

		foreach($laExpense as $svc){
			
			if($svcCd == '4'){
				if ($svc['val'] == '1'){
					$svc['val'] = '4';
				}else if ($svc['val'] == '2'){
					$svc['val'] = '3';
				}else if ($svc['val'] == '3'){
					$svc['val'] = '2';
				}else if ($svc['val'] == '4'){
					$svc['val'] = '1';
				}
			}
			
			if ($svc['cd'] == $suga && $svc['val'] == $val && $svc['from_dt'] <= $dt && $svc['to_dt'] >= $dt){
				
				$amt = $svc['amt'.$row['level']];
				break;
			}
		}

		$lvlNm = $myF->_lvlNm($row['level'],$svcCd);?>
		<tr>
			<td class="center"><span id=""><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
			<td class="center"><span id=""><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
			<td class="center"><div id="lvl_<?=$i?>" value="<?=$row['level'];?>" class="left"><?=$lvlNm;?></div></td>
			<td class="center"><div id="" class="right"><?=number_format($amt);?></div></td>
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