<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_GET['SR'];
	$code	= $_GET['jumin'];
	$today	= Date('Y-m-d');

	if ($code == 'ALL'){
		$sql = 'SELECT	DISTINCT
						a.jumin
				,		m03_name AS name
				FROM	client_his_svc AS a
				INNER	JOIN	m03sugupja
						ON		m03_ccode = a.org_no
						AND		m03_mkind = \'6\'
						AND		m03_jumin = a.jumin
				WHERE	a.org_no = \''.$orgNo.'\'
				AND		a.svc_cd = \''.$SR.'\'
				ORDER	BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$codeList[] = $row['jumin'];
		}

		$conn->row_free();
	}else{
		$codeList[] = $ed->de($code);
	}

	//if (!$code) exit;
	if (!is_array($codeList)) exit;

	$IsExcel = true;

	header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=carevisit_excel_".date('Ymd').".xls" );

	$IsFirst = true;

	foreach($codeList as $code){
		$sql = 'SELECT	m03_name AS name
				,		m03_juso1 AS addr
				,		m03_juso2 AS addr_dtl
				,		m03_tel AS phone
				,		m03_hp AS mobile
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_mkind = \'6\'
				AND		m03_jumin = \''.$code.'\'';

		$row = $conn->get_array($sql);

		$name	= $row['name'];
		$addr	= $row['addr'].' '.$row['addr_dtl'];

		$telno	= Trim($myF->phoneStyle($row['phone'],'.').' / '.$myF->phoneStyle($row['mobile'],'.'));

		if (SubStr($telno,0,1) == '/'){
			$telno = SubStr($telno,1);
		}else if (SubStr($telno,StrLen($telno) - 1, 1) == '/'){
			$telno = SubStr($telno,0,StrLen($telno) - 1);
		}

		Unset($row);

		$sql = 'SELECT	jumin
				FROM	mst_jumin
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \'1\'
				AND		code	= \''.$code.'\'';

		$jumin	= SubStr($conn->get_data($sql).'0000000',0,13);
		$birth	= $myF->issToBirthDay($jumin,'.');
		$gender	= $myF->issToGender($jumin);

		if ($gender == '남'){
			$gender = '<span style="color:BLUE;">남</span>';
		}else if ($gender == '여'){
			$gender = '<span style="color:RED;">여</span>';
		}

		include_once('../care/care_client_find_period.php');

		if ($fromDt <= $today && $toDt >= $today){
			$gbn = '(진행중)';
		}else{
			$gbn = '(중지)';
		}

		if ($IsFirst){?>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<div style="clear:both; text-align:center; font-weight:bold; font-size:17px;">대상자조회</div><?
		}else{?>
			<div style="height:50px;">&nbsp;</div><?
		}?>
		<div style="clear:both;">성명/성별 : <?=$name;?> / <?=$gender;?></div>
		<div style="clear:both;">생년월일 : <?=$birth;?></div>
		<div style="clear:both;">주소 : <?=$addr;?></div>
		<div style="clear:both;">연락처 : <?=$telno;?></div>
		<div style="clear:both;">계약기간 : <?=$myF->dateStyle($fromDt,'.');?> ~ <?=$myF->dateStyle($toDt,'.');?> <?=$gbn;?></div><?
		if ($debug && $SR == 'R'){
			//자원연계 출력시 기관명과 등급을 출력한다.
			$sql = 'SELECT	seq
					FROM	client_his_svc
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		jumin	 = \''.$code.'\'
					AND		svc_cd	 = \''.$SR.'\'
					AND		from_dt <= NOW()
					AND		to_dt	>= NOW()';

			$tmpSeq = $conn->get_data($sql);

			$sql = 'SELECT	care_org_nm
					,		care_lvl
					FROM	client_his_care
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$code.'\'
					AND		svc_cd	= \''.$SR.'\'
					AND		seq		= \''.$tmpSeq.'\'';

			$row = $conn->get_array($sql);?>
			<div style="clear:both;">기관 / 등급 : <?=$row['care_org_nm'].' / '.($row['care_lvl'] > '1' && $row['care_lvl'] <= '5' ? $row['care_lvl'].'등급' : '일반');?></div><?
			Unset($row);
		}?>
		<table border="1">
			<tr>
				<th style="width:70px; background-color:#EAEAEA;">년</th>
				<th style="width:50px; background-color:#EAEAEA;">월</th>
				<th style="width:50px; background-color:#EAEAEA;">일</th>
				<th style="width:150px; background-color:#EAEAEA;">서비스</th>
				<th style="width:150px; background-color:#EAEAEA;">자원</th>
				<th style="width:100px; background-color:#EAEAEA;">담당자</th>
				<th style="width:200px; background-color:#EAEAEA;">비고</th>
			</tr><?
			include('../care/care_client_find_iljung.php');?>
		</table>
		<div style="clear:both; text-align:center; font-size:17px; font-weight:bold;"><?=$_SESSION['userCenterName'];?></div><?

		$IsFirst = false;
	}
	include_once('../inc/_db_close.php');
?>