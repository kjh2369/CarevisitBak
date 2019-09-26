<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode']; //$_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);


	if ($svcCd == '0'){
		$reason = array(
				'01'=>'계약해지'
			,	'02'=>'보류'
			,	'03'=>'사망'
			,	'04'=>'타업체이동'
			,	'05'=>'등외판정'
			,	'06'=>'입원'
			,	'07'=>'무리한요구'
			,	'08'=>'단순서비스종료'
			,	'09'=>'근무자미투입'
			,	'10'=>'거주지이전'
			,	'11'=>'건강호전'
			,	'12'=>'부담금미납'
			,	'13'=>'지점이동'
			,	'14'=>'요양입소'
			,	'15'=>'주야간보호이용'
			,	'16'=>'서비스거부'
			,	'99'=>'기타'
		);
	}else if ($svcCd >= '1' && $svcCd <= '4'){
		$reason = array(
				'01'=>'본인포기'
			,	'02'=>'사망'
			,	'03'=>'말소'
			,	'04'=>'전출'
			,	'05'=>'미사용'
			,	'06'=>'본인부담금미납'
			,	'07'=>'사업종료'
			,	'08'=>'자격종료'
			,	'09'=>'판정결과반영'
			,	'10'=>'자격정지'
			,	'99'=>'기타'
		);
	}?>

	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="60px">
			<col width="215px">
			<col>
		</colgroup>
		<tbody><?

	$sql = 'select seq
			,      from_dt
			,      to_dt
			,      svc_stat
			,      svc_reason
			,		mp_gbn
			  from client_his_svc
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \''.$svcCd.'\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($row['svc_stat'] == '1')
			$stat = '이용';
		else
			$stat = '중지';?>
		<tr mp="<?=$row['mp_gbn'];?>">
			<td class="center"><span id="from_<?=$i;?>"><?=$myF->dateStyle($row['from_dt'],'.');?></span></td>
			<td class="center"><span id="to_<?=$i;?>"><?=$myF->dateStyle($row['to_dt'],'.');?></span></td>
			<td class="center"><?=$stat;?></td>
			<td class="center"><div class="left"><?=$reason[$row['svc_reason']];?></div></td>
			<td class="center"><?
				if (($gDomain == 'dolvoin.net') || ($rowCount > 1 && $i == 0)){?>
					<span class="btn_pack m"><button type="button" onclick="doDel('<?=$i;?>');">삭제</button></span><?
				}?>
				<span id="seq_<?=$i;?>" style="display:none;"><?=$row['seq'];?></span>
			</td>
		</tr><?
	}

	$conn->row_free();?>
		</tbody>
	</table><?

	include_once('../inc/_db_close.php');
?>