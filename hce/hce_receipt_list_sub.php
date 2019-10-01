<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************
	 *	사례접수일지
	 *********************************************************/

	$strName	= $_POST['txtName'];
	$strFrom	= str_replace('-', '', $_POST['txtFrom']);
	$strTo		= str_replace('-', '', $_POST['txtTo']);
	$strEndYn	= $_POST['cboEndYn'];

	if (!$strFrom) $strFrom = $strTo;
	if (!$strTo) $strTo = $strFrom;

	$orgNo = $_SESSION['userCenterCode'];
	$sr = $_POST['sr'];

	//접수방법
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CT\'';

	$rctGbn = $conn->_fetch_array($sql,'code');

?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="90px">
		<col width="40px">
		<col width="50px">
		<col width="70px">
		<col width="130px">
		<col width="90px">
		<col width="60px">
		<col width="40px">
		<col>
	</colgroup>
	<tbody><?
		$sql = 'SELECT	DISTINCT
						m03_name AS name
				,		rcpt.IPIN
				,		rcpt.rcpt_seq
				,		rcpt.rcpt_dt
				,		rcpt.phone
				,		rcpt.mobile
				,		rcpt.hce_seq
				,		rcpt.counsel_type AS rcpt_type
				,		rcpt.reqor_nm
				,		rcpt.reqor_telno
				,		rcpt.rcver_nm
				,		rcpt.end_flag
				FROM	hce_receipt AS rcpt
				INNER	JOIN	m03sugupja AS mst
						ON		mst.m03_ccode	= rcpt.org_no
						AND		mst.m03_mkind	= \'6\'
						AND		mst.m03_key		= rcpt.IPIN';

		if ($strName){
			$sql .= '	AND		m03_name >= \''.$strName.'\'';
		}

		$sql .= '
				LEFT	JOIN	client_his_svc AS svc
						ON		svc.org_no	= rcpt.org_no
						AND		svc.jumin	= mst.m03_jumin
						AND		svc.svc_cd	= rcpt.org_type
				WHERE	rcpt.org_no		= \''.$orgNo.'\'
				AND		rcpt.org_type	= \''.$sr.'\'
				AND		rcpt.rcpt_seq	= (SELECT MAX(rcpt_seq) FROM hce_receipt WHERE org_no = rcpt.org_no AND org_type = rcpt.org_type AND IPIN = rcpt.IPIN AND del_flag = \'N\')
				AND		rcpt.del_flag	= \'N\'';

		if ($strFrom && $strTo){
			$sql .= '
				AND		rcpt.rcpt_dt >= \''.$strFrom.'\'
				AND		rcpt.rcpt_dt <= \''.$strTo.'\'';
		}

		if ($strEndYn){
			$sql .= '
				AND		rcpt.end_flag = \''.$strEndYn.'\'';
		}

		$sql .= '
				ORDER	BY name';

		$r = $conn->_fetch_array($sql);
		$rCnt = SizeOf($r);

		for($i=0; $i<$rCnt; $i++){
			$row = $r[$i];

			if ($row['end_flag'] == 'Y'){
				$endStr = '<span style="color:red;">종결</span>';
			}else{
				$endStr = '미결';
			}?>
			<tr style="cursor:default;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="center"><a href="#" onclick="top.frames['frmTop'].lfTarget('<?=$row['IPIN'];?>','<?=$row['rcpt_seq'];?>'); $('#divSeqList').hide(); return false;"><?=$row['name'];?></a></td>
				<td class="center"><?=$myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');?></td><?
				if ($row['hce_seq'] > 1){?>
					<td class="center"><a href="#" onclick="lfSeqList($(this).parent(),'<?=$row['IPIN'];?>');"><span class="bold" style="color:blue;"><?=$row['hce_seq'];?></span></a></td><?
				}else{?>
					<td class="center"><?=$row['hce_seq'];?></td><?
				}?>
				<td class="center"><?=$rctGbn[$row['rcpt_type']]['name'];?></td>
				<td class="center"><?=$myF->dateStyle($row['rcpt_dt'],'.');?></td>
				<td class="center"><div class="left nowrap" style="width:125px;"><?=$row['reqor_nm'];?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['reqor_telno'],'.');?></td>
				<td class="center"><?=$row['rcver_nm'];?></td>
				<td class="center"><?=$endStr;?></td>
				<td class="center last">
					<div class="left"><span class="btn_pack small"><button type="button" onclick="lfDelete('<?=$row['IPIN'];?>','<?=$row['rcpt_seq'];?>');">삭제</button></span></div>
				</td>
			</tr><?

			if ($row['hce_seq'] > 1){
				$sql = 'SELECT	m03_name AS name
						,		rcpt.IPIN
						,		rcpt.rcpt_seq
						,		rcpt.rcpt_dt
						,		rcpt.phone
						,		rcpt.hce_seq
						,		rcpt.counsel_type AS rcpt_type
						,		rcpt.reqor_nm
						,		rcpt.reqor_telno
						,		rcpt.rcver_nm
						,		rcpt.end_flag
						FROM	hce_receipt AS rcpt
						INNER	JOIN	m03sugupja AS mst
						ON		mst.m03_ccode	= rcpt.org_no
						AND		mst.m03_mkind	= \'6\'
						AND		mst.m03_key		= rcpt.IPIN
						WHERE	rcpt.org_no		= \''.$orgNo.'\'
						AND		rcpt.org_type	= \''.$sr.'\'
						AND		rcpt.IPIN		= \''.$row['IPIN'].'\'
						AND		rcpt.del_flag	= \'N\'
						ORDER	BY rcpt_seq DESC
						LIMIT	1,'.($row['hce_seq'] - 1);

				$rs = $conn->_fetch_array($sql);
				$rsCnt = SizeOf($rs);

				for($j=0; $j<$rsCnt; $j++){
					$row = $rs[$j];

					if ($row['end_flag'] == 'Y'){
						$endStr = '<span style="color:red;">종결</span>';
					}else{
						$endStr = '미결';
					}?>
					<tr id="row_<?=$row['IPIN'];?>" style="cursor:default; background-color:#EAEAEA; display:none;" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#EAEAEA';">
						<td class="center <?=($j < $rsCnt - 1 ? 'bottom' : '');?>"></td>
						<td class="center"><a href="#" onclick="top.frames['frmTop'].lfTarget('<?=$row['IPIN'];?>','<?=$row['rcpt_seq'];?>'); $('#divSeqList').hide(); return false;"><?=$row['name'];?></a></td>
						<td class="center"><?=$myF->phoneStyle($row['phone'],'.');?></td>
						<td class="center"><?=$row['hce_seq'];?></td>
						<td class="center"><?=$rctGbn[$row['rcpt_type']]['name'];?></td>
						<td class="center"><?=$myF->dateStyle($row['rcpt_dt'],'.');?></td>
						<td class="center"><div class="left nowrap" style="width:125px;"><?=$row['reqor_nm'];?></div></td>
						<td class="center"><?=$myF->phoneStyle($row['reqor_telno'],'.');?></td>
						<td class="center"><?=$row['rcver_nm'];?></td>
						<td class="center"><?=$endStr;?></td>
						<td class="center last"></td>
					</tr><?
				}

				Unset($rs);
			}
		}

		Unset($r);

		if ($rCnt == 0){?>
			<tr>
				<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}?>
	</tbody>
</table>

<?
	include_once('../inc/_db_close.php');
?>