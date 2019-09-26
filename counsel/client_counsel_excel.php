<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
?>
<table border="1">
	<tr style="height:30px;">
		<th style="width:50px; text-align:cener; background-color:#BDBDBD;">No</th>
		<th style="width:80px; text-align:cener; background-color:#BDBDBD;">상담일자</th>
		<th style="width:90px; text-align:cener; background-color:#BDBDBD;">고객명</th>
		<th style="width:110px; text-align:cener; background-color:#BDBDBD;">상담구분</th>
		<th style="width:100px; text-align:cener; background-color:#BDBDBD;">연락처</th>
		<th style="width:100px; text-align:cener; background-color:#BDBDBD;">휴대폰</th>
		<th style="width:200px; text-align:cener; background-color:#BDBDBD;">주소</th>
		<th style="width:90px; text-align:cener; background-color:#BDBDBD;">상담자</th>
		<th style="width:200px; text-align:cener; background-color:#BDBDBD;">비고</th>
	</tr><?
	$sql = 'SELECT	counsel.client_dt AS date
			,		counsel.client_ssn AS jumin
			,		counsel.client_nm AS name
			,		counsel.client_counsel AS svc_cd
			,		counsel.client_phone AS phone
			,		counsel.client_mobile AS mobile
			,		counsel.client_addr AS addr
			,		counsel.client_addr_dtl AS addr_dtl

			,		normal.talker_nm AS normal_talker
			,		normal.talker_type AS normal_type

			,		baby.talker_nm AS baby_talker
			,		baby.talker_type AS baby_type
			FROM	counsel_client AS counsel
			LEFT	JOIN	counsel_client_normal AS normal
					ON		counsel.org_no		= normal.org_no
					AND		counsel.client_dt	= normal.client_dt
					AND		counsel.client_seq	= normal.client_seq
			LEFT	JOIN	counsel_client_baby AS baby
					ON		counsel.org_no		= baby.org_no
					AND		counsel.client_dt	= baby.client_dt
					AND		counsel.client_seq	= baby.client_seq
			WHERE	counsel.org_no		= \''.$orgNo.'\'
			AND		counsel.client_ssn != \'\'
			AND		counsel.del_flag	= \'N\'
			ORDER	BY date DESC, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}

		if ($row['svc_cd'] == '3'){
			$talker = $row['baby_talker'];
		}else{
			$talker = $row['normal_talker'];
		}?>
		<tr>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$i+1;?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$myF->dateStyle($row['date'],'.');?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$row['name'];?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$conn->_svcNm($row['svc_cd']);?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$myF->phoneStyle($row['phone'],'.');?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$myF->phoneStyle($row['mobile'],'.');?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$row['addr'].' '.$row['addr_dtl'];?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"><?=$talker;?></td>
			<td style="text-align:center; background-color:#<?=$bgcolor;?>;"></td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>