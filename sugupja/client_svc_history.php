<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->fetch_type = 'assoc';

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$laReason[1] = array(
			'01'=>'계약해지'
		,	'02'=>'보류'
		,	'03'=>'사망'
		,	'04'=>'타업체이동'
		,	'05'=>'등외판정'
		,	'06'=>'입원'
		,	'07'=>'무리한서비스요구'
		,	'08'=>'단순서비스종료'
		,	'09'=>'근무자미투입'
		,	'10'=>'거주지이전'
		,	'11'=>'건강호전'
		,	'12'=>'부담금미납'
		,	'13'=>'지점이동'
		,	'14'=>'요양입소'
		,	'99'=>'기타'
	);

	$laReason[2] = array(
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
?>
<div id="loSvcHistory" style="padding:0 10px 0 10px;">
	<table class="my_table my_border_blue" style="width:100%; border-top:none;">
		<colgroup>
			<col width="50px">
			<col width="100px">
			<col width="140px">
			<col width="60px">
			<col width="100px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">서비스명</th>
				<th class="head">계약기간</th>
				<th class="head">상태</th>
				<th class="head">중지사유</th>
				<th class="head">
					<div style="float:right; width:auto; margin-right:3px; cursor:pointer;"><img src="../image/btn_close.gif" onclick="$('#loSvcHistory').hide();"></div>
					<div style="float:center; width:auto;">비고</div>
				</th>
			</tr>
		</thead>
		<tbody><?
			$liNo = 1;

			$sql = 'select svc_cd as cd
					,	   from_dt
					,      to_dt
					,      svc_stat
					,      svc_reason
					  from client_his_svc
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					 order by from_dt desc';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0;$i<$rowCount;$i++){
				$row = $conn->select_row($i);

				if ($row['svc_stat'] == '1'){
					$lsStat   = '이용';
					$lsReason = '';
				}else{
					if($debug) echo $row['cd'];
					if ($row['cd'] == '0')
						$liReason = 1;
					else
						$liReason = 2;

					$lsStat   = '중지';
					$lsReason = $laReason[$liReason][$row['svc_reason']];
				}?>
				<tr>
					<td class="center"><?=$liNo;?></td>
					<td class="left"><?=$conn->_svcNm($row['cd']);?></td>
					<td class="center"><?=$myF->dateStyle($row['from_dt'],'.').'~'.$myF->dateStyle($row['to_dt'],'.');?></td>
					<td class="center"><?=$lsStat;?></td>
					<td class="left"><?=$lsReason;?></td>
					<td class="left"></td>
				</tr><?
				$liNo ++;
			}

			$conn->row_free();

			if ($rowCount == 0){?>
				<tr>
					<td class="center" colspan="6">::검색된 데이타가 없습니다.::</td>
				</tr><?
			}?>
		</tbody>
	</table>
</div>
<?
	include_once('../inc/_db_close.php');
?>