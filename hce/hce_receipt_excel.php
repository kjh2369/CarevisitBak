<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$orgNm = $_SESSION['userCenterName'];
	$SR = $_POST['sr'];
	$filename = $myF->euckr("사례접수일지");
	$strFrom = str_replace('-', '', $_POST['txtFrom']);
	$strTo = str_replace('-', '', $_POST['txtTo']);
	$strEndYn	= $_POST['cboEndYn'];
	
	//접수방법
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CT\'';

	$gbnRct = $conn->_fetch_array($sql,'code');

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=$filename.xls" );
	header( "Content-Description: $filename" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<div style="text-align:center; font-size:17px; font-weight:bold;"><?=$orgNm;?> 사례접수일지</div>
	<div style="text-align:right;">출력일 : <?=Date('Y.m.d');?></div>
	<table border="1">
		<tr>
			<th style="width:50px; background-color:#EAEAEA;">연번</th>
			<th style="width:50px; background-color:#EAEAEA;">접수<br style="mso-data-placement:same-cell;">방법</th>
			<th style="width:100px; background-color:#EAEAEA;">접수일자</th>
			<th style="width:100px; background-color:#EAEAEA;">대상자명<br style="mso-data-placement:same-cell;">(성별/나이)</th>
			<th style="width:200px; background-color:#EAEAEA;">대상자주소<br style="mso-data-placement:same-cell;">(연락처)</th>
			<th style="width:248px; background-color:#EAEAEA;">상담내용</th>
			<th style="width:70px; background-color:#EAEAEA;">의뢰인<br style="mso-data-placement:same-cell;">(연락처)</th>
			<th style="width:70px; background-color:#EAEAEA;">접수자</th>
			<th style="width:70px; background-color:#EAEAEA;">초기면접<br style="mso-data-placement:same-cell;">필요여부</th>
		</tr><?
		//사례접수리스트
		$sql = 'SELECT	DISTINCT
						rcpt.counsel_type
				,		rcpt.rcpt_dt
				,		m03_jumin AS jumin
				,		m03_name AS name
				,		rcpt.addr
				,		rcpt.addr_dtl
				,		rcpt.phone
				,		rcpt.mobile
				,		rcpt.counsel_text
				,		reqor_nm
				,		reqor_telno
				,		rcver_nm
				FROM	hce_receipt AS rcpt
				INNER	JOIN	m03sugupja AS mst
						ON		mst.m03_ccode = rcpt.org_no
						AND		mst.m03_mkind = \'6\'
						AND		mst.m03_key = rcpt.IPIN
				WHERE	rcpt.org_no		= \''.$orgNo.'\'
				AND		rcpt.org_type	= \''.$SR.'\'
				AND		rcpt.rcpt_seq	= (	SELECT	MAX(rcpt_seq)
											FROM	hce_receipt
											WHERE	org_no	= rcpt.org_no
											AND		org_type= rcpt.org_type
											AND		IPIN	= rcpt.IPIN
											AND		del_flag= \'N\')
				AND		rcpt.del_flag	= \'N\'';

		if ($strFrom && $strTo){
			$sql .= '
				AND		rcpt.rcpt_dt >= \''.$strFrom.'\'
				AND		rcpt.rcpt_dt <= \''.$strTo.'\'';
		}
		
		if ($strEndYn){
			$sql .= '
				AND		end_flag = \''.$strEndYn.'\'';
		}

		$sql .= '	ORDER	BY rcpt_dt';
			
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td style="text-align:center;"><?=$no;?></td>
				<td style="text-align:center;"><?=$gbnRct[$row['counsel_type']]['name'];?></td>
				<td style="text-align:center;"><?=$myF->dateStyle($row['rcpt_dt'],'.');?></td>
				<td style="text-align:center;"><?=$row['name']."<br style='mso-data-placement:same-cell;'>(".$myF->issToGender($row['jumin'])."/".$myF->issToAge($row['jumin'])."세)";?></td>
				<td style="text-align:center;"><?=$row['addr']." ".$row['addr_dtl']."<br style='mso-data-placement:same-cell;'>".$myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');?></td>
				<td style="text-align:left;"><?=$row['counsel_text'];?></td>
				<td style="text-align:center;"><?=$row['reqor_nm']."<br style='mso-data-placement:same-cell;'>".$myF->phoneStyle($row['reqor_telno'],'.');?></td>
				<td style="text-align:center;"><?=$row['rcver_nm'];?></td>
				<td style="text-align:center;"></td>
			</tr><?

			$no ++;
		}

		$conn->row_free();?>
	</table><?
	include_once('../inc/_db_close.php');
?>