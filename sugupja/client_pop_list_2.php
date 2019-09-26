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
			<col width="90px">
			<col width="70px">
			<col width="70px">
			<col width="60px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody><?

	$sql = 'select m91_code as cd
			,      m91_kupyeo as pay
			,      m91_sdate as f_dt
			,      m91_edate as t_dt
			  from m91maxkupyeo';

	$arrLimitPay = $conn->_fetch_array($sql);

	$sql = 'select seq
			,      app_no
			,      date_format(from_dt,\'%Y%m%d\') as from_dt
			,      date_format(to_dt,\'%Y%m%d\') as to_dt
			,      level as lvl_cd
			  from client_his_lvl
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \'0\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$dt  = $myF->_getDt($row['from_dt'],$row['to_dt']);
		$dt  = str_replace('-','',$dt);

		/*if ($dt >= '2014-07-01'){
			if ($row['lvl_cd'] >= '1' && $row['lvl_cd'] <= '5'){
				$row['lvl_nm'] = $row['lvl_cd'].'등급';
			}else{
				$row['lvl_nm'] = '일반';
			}
		}*/

		$row['lvl_nm'] = $myF->_lvlNm($row['lvl_cd']);

		foreach($arrLimitPay as $pay){
			if ($pay['cd'] == $row['lvl_cd'] &&
				$pay['f_dt'] <= $dt &&
				$pay['t_dt'] >= $dt){
				$limitPay = $pay['pay'];
				break;
			}
		}?>
		<tr>
			<td class="center"><span id="mgmtNo"><?=$row['app_no'];?></span></td>
			<td class="center"><span id="mgmtFrom"><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
			<td class="center"><span id="mgmtTo"><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
			<td class="center"><span id="mgmtLvl" class="nowrap" style="width:55px" lvl="<?=$row['lvl_cd'];?>"><?=$row['lvl_nm'];?></span></td>
			<td class="center"><div id="mgmtPay" class="right"><?=number_format($limitPay);?></div></td>
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