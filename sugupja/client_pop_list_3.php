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
			<col width="40px">
			<col width="70px">
			<col width="50px">
			<col width="50px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody><?

	$sql = 'select m91_code as cd
			,      m91_kupyeo as pay
			,      m91_sdate as f_dt
			,      m91_edate as t_dt
			  from m91maxkupyeo';

	$arrLimitPay = $conn->_fetch_array($sql);

	$sql = 'select level
			,      from_dt
			,      to_dt
			  from client_his_lvl
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \'0\'';

	$arrLvl = $conn->_fetch_array($sql);

	$sql = 'select seq
			,	   from_dt
			,      to_dt
			,      kind as expense_kind
			,	   case kind when \'3\' then \'기초\'
							 when \'2\' then \'의료\'
							 when \'4\' then \'경감\' else \'일반\' end as kind_nm
			,      rate as expense_rate
			  from client_his_kind as kind
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);

		if (is_array($arrLvl)){
			foreach($arrLvl as $lvl){
				if ($lvl['from_dt'] <= $dt && $lvl['to_dt'] >= $dt){
					$lvlCd = $lvl['level'];
					break;
				}
			}
		}

		if (is_array($arrLimitPay)){
			$dt  = str_replace('-','',$dt);

			foreach($arrLimitPay as $pay){
				if ($pay['cd'] == $lvlCd && $pay['f_dt'] <= $dt && $pay['t_dt'] >= $dt){
					$limitPay = $pay['pay'];
					break;
				}
			}
		}

		$expenseAmt = $myF->cutOff($limitPay * $row['expense_rate'] * 0.01);?>
		<tr>
			<td class="center"><span id=""><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
			<td class="center"><span id=""><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
			<td class="center"><span id=""><?=$myF->_lvlNm($lvlCd);?></span></td>
			<td class="center"><div id="" class="right"><?=number_format($limitPay);?></div></td>
			<td class="center"><span id=""><?=$row['kind_nm'];?></span></td>
			<td class="center"><div id="" class="right"><?=number_format($row['expense_rate'],1);?></div></td>
			<td class="center"><div id="" class="right"><?=number_format($expenseAmt);?></div></td>
			<td class="center">
				<div class="left"><?
					if ($rowCount > 0 && $i == 0){?>
						<span class="btn_pack m"><button type="button" onclick="doDel('<?=$i;?>');">삭제</button></span><?
					}?>
					<span id="seq_<?=$i;?>" style="display:none;"><?=$row['seq'];?></span>
				</div>
			</td>
		</tr><?
	}

	$conn->row_free();


	unset($arrLimitPay);?>

	</tbody>
	</table><?

	include_once('../inc/_db_close.php');
?>