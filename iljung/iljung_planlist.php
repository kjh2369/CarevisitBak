<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->set_name('euckr');

	$code		= $_SESSION['userCenterCode'];	//�����ȣ
	$jumin		= $ed->de($_REQUEST['jumin']);	//������
	$yymm		= $_REQUEST['yymm'];			//���
	$svcKind	= $_REQUEST['svcKind'];		//�����ڵ�
	$lastday	= $myF->lastDay(substr($yymm, 0, 4), substr($yymm, 4, 2));
	$uploadYN	= $_REQUEST['uploadYN'];
	$lgPara		= Explode('?',$_REQUEST['lgPara']);

	if (is_array($lgPara)){
		foreach($lgPara as $idx => $R){
			parse_str($R,$row);
			$lgVal[$row['day'].'_'.$row['subCd'].'_'.$row['from']] = 'Y';
		}
	}

	if ($svcKind == '004'){
		$kind = '5';
	}else{
		$kind = '0'; //�������
	}

	$debug = false;

	//if ($code == '24273000050') $debug = true;

	//����
	$chgSayu	= $_REQUEST['chgSayu'];
	$chgSayuEtc	= $_REQUEST['chgSayuEtc'];

	//���̵�
	$winID = $_REQUEST['id'];

	$nextYn = 'Y';

	if ($debug){
		$nextYn = 'N';
	}else{
		//if ($code == '31153000162') $nextYn = 'N';
	}

	$lbAdmin = false;

	if ($debug){
		//���ٴ� ���� Ajax ���ε� �׽�Ʈ
		//$lbAdmin = true;
	}


	/*********************************************************
	 * ������ ������ȣ
	 *********************************************************/
		$sql = 'select app_no
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$kind.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$yymm.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$yymm.'\'
				 order by seq desc
				 limit 1';

		$mgmtNo = $conn->get_data($sql);
		$mgmtNo = trim($mgmtNo);

	/*********************************************************
	 * ����
	 *********************************************************/
	$sql = 'select cast(date_format(mdate, \'%d\') as signed) as dt
			,      holiday_name as nm
			  from tbl_holiday
			 where left(mdate, 6) = \''.$yymm.'\'';

	$arrHoliday = $conn->_fetch_array($sql, 'dt');


	switch($svcKind){
		case '001':
			$svcSubKind = '200';
			break;

		case '002':
			$svcSubKind = '500';
			break;

		case '003':
			$svcSubKind = '800';
			break;

		case '004':
			$svcSubKind = 'DAY_AND_NIGHT';
			break;
	}

	//�����ȣ
	$lsGiho = $_SESSION['userCenterGiho'];

	/*
	if ($debug){
		if ($svcSubKind == '500' || $svcSubKind == '800'){
			$sql = 'SELECT sub_code
					  FROM center_comm
					 WHERE org_no = \''.$code.'\'';

			$lsSubCD = $conn->get_data($sql);

			if (!Empty($lsSubCD)){
				$lsGiho = $lsSubCD;
			}
		}
	}
	*/

	//�� �����ȸ
	$sql = 'SELECT	MIN(level)
			FROM	client_his_lvl
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		svc_cd	= \'0\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
			AND		DATE_FORMAT(to_dt,	\'%Y%m\') >= \''.$yymm.'\'';
	$lvl = $conn->get_data($sql);


	/*********************************************************

		������纸ȣ�� ��ȸ

		*****************************************************/
		$sql = 'select cf_mem_cd as cd
				,      cf_mem_nm as nm
				,      cf_kind as kind
				  from client_family
				 where org_no   = \''.$code.'\'
				   and cf_jumin = \''.$jumin.'\'';

		$arrClientFamily = $conn->_fetch_array($sql, 'cd');
	/********************************************************/


	if (strlen($mgmtNo) == 11){
		$mgmtYn = 'Y';
		$paraNo = $mgmtNo;
	}else{
		$mgmtYn = 'N';
		$paraNo = $jumin;
	}


	$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
				<head>
					<title>::�湮���񽺽ý���::</title>
					<meta http-equiv="Content-Type" content="text/html; charset=EUC-KR" />
					<meta http-equiv="imagetoolbar" content="no">

					<link href="../css/style.css" rel="stylesheet" type="text/css">
					<link href="../css/head.css" rel="stylesheet" type="text/css">
					<link href="../css/main_contents.css" rel="stylesheet" type="text/css">
					<link href="../css/left_menu.css" rel="stylesheet" type="text/css">
					<link rel="stylesheet" type="text/css" href="../css/jqueryslidemenu.css" />
					<style>
						.lcDiv{
							float:left;
							width:auto;
							height:25px;
							line-height:25px;
							cursor:default;
							text-align:center;
							border:1px solid #cccccc;
							border-top:none;
							border-left:none;
						}
					</style>
				</head>
				<body>';

	$colgrp = '<col width=\'90px\'>
			   <col width=\'130px\'>
			   <col width=\'80px\'>
			   <col width=\'40px\'>
			   <col>';
	$html .= '<div id=\'carevisitData\' style=\'display:'.($nextYn == 'Y' && !$winID ? 'none' : '').';\'>';
	$html .= '<table class=\'my_table my_green\' style=\'width:100%;\'>
				<colgroup>'.$colgrp.'</colgroup>
				<thead>
					<tr>
						<th class=\'center\'>��纸ȣ��</th>
						<th class=\'center\'>��������</th>
						<th class=\'center\'>�����ð�</th>
						<th class=\'center\'>Ƚ��</th>
						<th class=\'center last\'>
							<div style=\'float:right; width:auto; cursor:default; margin-right:5px;\' onclick=\'$("#iljung_planlist").hide();\'>[�ݱ�]</div>
							<div style=\'float:right; width:auto; cursor:default; margin-right:5px;\' onclick=\'_iljungGetLongTermMgmtNo("'.$svcKind.'","'.$uploadYN.'","'.$paraNo.'","'.$mgmtYn.'","'.$jumin.'","'.$lbAdmin.'","'.$chgSayu.'","'.$chgSayuEtc.'","'.$winID.'");\'>[���ε�]</div>
							<div style=\'float:center; width:auto;\'>������</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class=\'top center last\' colspan=\'5\'>
							<div style=\'overflow-x:hidden; overflow-y:scroll; width:100%; height:150px;\'>';

	if ($svcSubKind == 'DAY_AND_NIGHT'){
		$sql = 'SELECT	CAST(DATE_FORMAT(t01_sugup_date,\'%d\') AS signed) AS dt
				,		t01_sugup_date AS tmp_dt
				,		t01_sugup_fmtime AS f_tm
				,		t01_sugup_totime AS t_tm
				,		t01_sugup_soyotime AS p_tm
				,		t01_suga_tot AS suga_pay
				,		t01_suga_code1 AS suga_cd
				,		suga.name AS suga_nm
				FROM	t01iljung
				INNER	JOIN  suga_dan AS suga
						ON    suga.code		= t01_suga_code1
						AND   suga.lv_gbn	= \''.$lvl.'\'
						AND   DATE_FORMAT(from_dt,	\'%Y%m%d\') <= t01_sugup_date
						AND   DATE_FORMAT(to_dt,	\'%Y%m%d\') >= t01_sugup_date
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$kind.'\'
				AND		t01_jumin = \''.$jumin.'\'
				AND		t01_sugup_date BETWEEN \''.$yymm.'01\' AND \''.$yymm.'31\'
				AND		t01_del_yn = \'N\'
				AND		IFNULL(t01_bipay_umu,\'N\') != \'Y\'';
	}else{
		$sql = 'select	cast(date_format(t01_sugup_date, \'%d\') as signed) as dt
				,		t01_sugup_date as tmp_dt
				,		t01_sugup_fmtime as f_tm
				,		t01_sugup_totime as t_tm
				,		t01_sugup_soyotime as p_tm
				,		t01_mem_cd1 as mem_cd1
				,		t01_mem_cd2 as mem_cd2
				,		t01_mem_nm1 as mem_nm1
				,		t01_mem_nm2 as mem_nm2
				,		t01_toge_umu as family_yn
				,		t01_suga_tot as suga_pay
				,		t01_time_doub AS together_yn
				,		suga.cd as suga_cd
				,		suga.nm as suga_nm
				,		suga.scd as long_cd
				from	t01iljung
				inner	join (
							select	m01_mcode as code
							,		m01_mcode2 as cd
							,		m01_scode as scd
							,		m01_suga_value as val
							,		m01_suga_cont as nm
							,		m01_sdate as f_dt
							,		m01_edate as e_dt
							from	m01suga
							where	m01_mcode = \'goodeos\'
							union	all
							select	m11_mcode as code
							,		m11_mcode2 as suga_cd
							,		m11_scode as scd
							,		m11_suga_value as val
							,		m11_suga_cont as suga_nm
							,		m11_sdate as f_dt
							,		m11_edate as e_dt
							from	m11suga
							where	m11_mcode = \'goodeos\'
						) as suga
						on		t01_suga_code1  = suga.cd
						and		t01_sugup_date >= suga.f_dt
						and		t01_sugup_date <= suga.e_dt
				where	t01_ccode       = \''.$code.'\'
				and		t01_mkind       = \''.$kind.'\'
				and		t01_jumin       = \''.$jumin.'\'
				and		t01_svc_subcode = \''.$svcSubKind.'\'
				and		t01_del_yn      = \'N\'
				and		ifnull(t01_bipay_umu, \'N\') != \'Y\'
				and		t01_sugup_date >= \''.$yymm.'01\'
				and		t01_sugup_date <= \''.$yymm.'31\'
				order	by mem_nm1, mem_nm2, f_tm, t_tm, dt';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	$html .= '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>'.$colgrp.'</colgroup>
				<tbody>';

	$seq = 0;

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($lgVal[$row['dt'].'_'.$svcSubKind.'_'.$row['f_tm']] == 'Y'){
			continue;
		}

		$tmpData = $row['mem_cd1'].'_'.$row['mem_nm1'].'_'.$row['mem_nm2'].'_'.$row['f_tm'].'_'.$row['t_tm'].'_'.$row['suga_cd'];

		//�־߰���ȣ
		if ($svcSubKind == 'DAY_AND_NIGHT'){
			$tmpData .= '_'.$row['suga_pay'];
		}

		if ($tempData != $tmpData){
			$tempData  = $tmpData;

			/*********************************************************
				�湮��� ����
				EX - B1209010
				- B1  : ���
				- 2   : ����(�Ϲ��� 0)
				- 090 : ���������ð�(��)
				- 1   : ??
				- 0   : ??
			*********************************************************/

			/*********************************************************
				�湮��� ����
				B2300000 71290	�湮��� �����̿�(������ ���) 60���̻�
				B2380000 57030	�湮��� �����̿�(������ ���) 40���̻� 60�й̸�
				B2400000 64160	�湮��� �����̿�(������ ���) 60���̻�
				B2480000 51330	�湮��� �����̿� (������ ���) 40���̻� 60�й̸�
				B2500000 39590	�湮��� ���� ���̿� 60���̻�
				B2580000 31670	�湮��� ���� ���̿� 40���̻� 60�� �̸�
			*********************************************************/

			/*********************************************************
				�湮��ȣ ����
				B3000000 29220 �湮��ȣ, 30�й̸� (�湮��)
				B3000100 37990 �湮��ȣ, 30�й̸�-�ɾ�.���� (�湮��)
				B3003000 37310 �湮��ȣ, 30���̻� 60�й̸� (�湮��)
				B3003100 48500 �湮��ȣ, 30���̻� 60�й̸�-�ɾ�.���� (�湮��)
				B3006000 45400 �湮��ȣ, 60���̻�(�湮��)
				B3006100 59020 �湮��ȣ, 60���̻�-�ɾ�.����(�湮��)
			*********************************************************/

			$longCD = 'B';

			#������ ����
			if ($svcSubKind == '200')
				$longCD .= '1';
			else if ($svcSubKind == '500')
				$longCD .= '2';
			else if ($svcSubKind == '800')
				$longCD .= '3';
			else if ($svcSubKind == 'DAY_AND_NIGHT')
				$longCD .= '4';

			#�����ð�
			$procTime = intval($row['p_tm']);

			//20130221
			//�ҿ�ð��� 30�� ������ ������ ������ �����Ѵ�.
			$procTime = $myF->cutOff($procTime,30);

			if ($svcSubKind == '200'){
				#���� ����
				if ($lvl == '5'){
					$longCD .= '3';
				}else{
					if ($row['family_yn'] == 'Y'){
						$longCD .= '2';
					}else{
						$longCD .= '0';
					}
				}

				#$procTime = intval($row['p_tm']);

				if ($procTime == 270) $procTime = 240;
				if ($procTime  > 270) $procTime = 270;
				if (strlen($procTime) < 3) $procTime = '0'.$procTime;

				$longCD .= $procTime;

				if (intval($procTime) == 90){
					if ($row['family_yn'] == 'Y'){
						$longCD .= '10';
					}else{
						$longCD .= '00';
					}
				}else{
					$longCD .= '00';
				}

			}else if ($svcSubKind == '500'){
				#$procTime = intval($row['p_tm']);

				if ($row['suga_cd'] == 'CBFD1'){ //���/������(�Կ�)
					$longCD .= '5';
				}else if ($row['suga_cd'] == 'CBKD1'){ //���/����(�Կ�)
					$longCD .= '3';
				}else if ($row['suga_cd'] == 'CBKD2'){ //���/����(�������Կ�)
					$longCD .= '4';
				}


				if (intval($procTime) >= 40 && intval($procTime) < 60)
					$longCD .= '80000'; //40���̻� 60�й̸�
				else
					$longCD .= '00000'; //60���̻�

			}else if ($svcSubKind == '800'){
				$longCD .= '00';

				if ($procTime < 30)
					$longCD .= '0';
				else if ($procTime < 60)
					$longCD .= '3';
				else
					$longCD .= '6';

				if (!empty($arrHoliday[$row['dt']]) || date('w', strtotime($yymm.($row['dt'] < 10 ? '0' : '').$row['dt'])) == 0)
					$longCD .= '1';
				else
					$longCD .= '0';

				$longCD .= '00';

			}else if ($svcSubKind == 'DAY_AND_NIGHT'){
				//4����� D�ڵ�� �׿ܴ� ����� ����Ѵ�.
				if ($lvl == '4'){
					$longCD .= 'D';
				}else{
					$longCD .= $lvl;
				}

				$tmpFrom= $myF->time2min($row['f_tm']);
				$tmpTo	= $myF->time2min($row['t_tm']);

				if ($tmpFrom > $tmpTo) $tmpTo += 24 * 60;

				$tmpHour = $tmpTo - $tmpFrom;
				$tmpHour = Floor($tmpHour / 60);

				if ($tmpHour >= 3 && $tmpHour < 6){
					$tmpHour = 3;
				}else if ($tmpHour >= 6 && $tmpHour < 8){
					$tmpHour = 6;
				}else if ($tmpHour >= 8 && $tmpHour < 10){
					$tmpHour = 8;
				}else if ($tmpHour >= 10 && $tmpHour < 12){
					$tmpHour = 10;
				}else if ($tmpHour >= 12){
					$tmpHour = 12;
				}

				$tmpHour = ($tmpHour < 10 ? '0' : '').$tmpHour;

				$longCD .= $tmpHour;

				if (!empty($arrHoliday[$row['dt']]) || date('w', strtotime($yymm.($row['dt'] < 10 ? '0' : '').$row['dt'])) == 0)
					$longCD .= '1';
				else
					$longCD .= '0';

				$longCD .= '00';
			}

			$data[$seq]['order']     = $row['f_tm'].'_'.$row['t_tm'].'_'.$row['mem_nm1'].'_'.$row['mem_nm2'];
			$data[$seq]['f_time']    = $row['f_tm'];
			$data[$seq]['t_time']    = $row['t_tm'];
			$data[$seq]['suga_nm']   = $row['suga_nm'];
			$data[$seq]['suga_pay']  = $row['suga_pay'];
			$data[$seq]['mem_nm1']   = $row['mem_nm1'];
			$data[$seq]['mem_nm2']   = $row['mem_nm2'];
			$data[$seq]['mem_cd1']   = $row['mem_cd1'];
			$data[$seq]['mem_cd2']   = $row['mem_cd2'];
			$data[$seq]['family_yn'] = $row['family_yn'];
			$data[$seq]['long_cd']   = $longCD; //$row['long_cd'];
			$data[$seq]['count']     = 0;
			$seq ++;
		}
		$data[$seq-1]['count'] ++;
		$data[$seq-1]['day_'.$row['dt']] .= $row['dt'];
	}

	$data = $myF->sortArray($data, 'order', 1);


	/*********************************************************
		�Ǻ� ���� ����

		S031	ó
		S032	����
		S033	��
		S034	�ں�
		S035	����
		S036	�����ڸ�
		S037	��
		S038	������������ڸ�
		S039	�ܼ�
		S040	�θ�
		S041	��Ÿ
	*********************************************************/


	if (is_array($data)){
		foreach($data as $i => $svc){
			$html .= '<tr class=\'planList\' memNM1=\''.$svc['mem_nm1'].'\' memNM2=\''.$svc['mem_nm2'].'\' memCD1=\''.$ed->en($svc['mem_cd1']).'\' memCD2=\''.$ed->en($svc['mem_cd2']).'\' svcKind=\''.$svcKind.'\'>
						<td class=\'center\'><div class=\'left\'>'.$svc['mem_nm1'].'('.$arrClientFamily[$svc['mem_cd1']]['kind'].')'.(!empty($svc['mem_nm2']) ? '/'.$svc['mem_nm2'].'('.$arrClientFamily[$svc['mem_cd2']]['kind'].')' : '').'</div></td>
						<td class=\'center\'><div class=\'left\'>'.$svc['suga_nm'].'</div></td>
						<td class=\'center\'>'.$myF->timeStyle($svc['f_time']).'~'.$myF->timeStyle($svc['t_time']).'</td>
						<td class=\'center\'>'.$svc['count'].'</td>
						<td class=\'center\'>
							<div class=\'nowrap\' style=\'\'>';

			for($day=1; $day<=$lastday; $day++){
				if ($lgVal[$day.'_'.$svcSubKind.'_'.$svc['f_time']] == 'Y'){
					continue;
				}

				if (!empty($svc['day_'.$day])){
					$style  = 'color:#000000;';
					$planIs = 'clsPlan';
				}else{
					$style  = 'color:#cccccc;';
					$planIs = '';
				}

				$html .= '<div class=\''.$planIs.'\' date=\''.$yymm.($day<10?'0':'').$day.'\' style=\'float:left; width:auto; margin:right:3px; padding:0 3px 0 3px; font-weight:bold; '.$style.'\'';

				/*********************************************************
					str ����
					- ��纸ȣ�� �ֹι�ȣ
					- YYYYMMDD
					- ��纸ȣ�� ����
					- ��纸ȣ�� qlfNo
					- ��纸ȣ�� qlfKind
					- 2�ο��� ?
					- ���۽ð�
					- ����ð�
					- �Ǻ� �����ڵ�
					- �ݾ�
					- ���
					- ����(����/�Ƿ�/�氨/�Ϲ�)
					- ��������
					- �����ڵ�
					- Ȯ�忩��(90�а���)
				*********************************************************/

				if ($svc['family_yn'] == 'Y')
					$familyYN = 'Y';
				else
					$familyYN = 'N';

				if (!empty($planIs)){
					/*
						$html .= ' str=\''.$svc['mem_cd1'].' ='.$yymm.($day<10?'0':'').$day.' =#careNm =#qlfNo =#qlfKind =N ='.$svc['f_time'].' ='.$svc['t_time'].' ='.$svc['long_cd'].' ='.$svc['suga_pay']; //B1209010
						$html .= ' =#srcAmdtGradeCd =#srcTgtPrsnCd'; #������ ���, ����
						$html .= ' =#juminNo2 =#careNm2 =#qlfNo2 =#qlfKind2'; #��纸ȣ��2

						//��ȣ��1�� �������� ��������
						$familyKind = $arrClientFamily[$svc['mem_cd1']]['kind'];

						if (!empty($familyKind)){
							//��������, �����ڵ�
							$html .= ' =Y ='.$familyKind;
						}else{
							$html .= ' =N =00';
						}


						if ($svcSubKind == '500'){
							//��ȣ��2�� �������� ��������
							$familyKind = $arrClientFamily[$svc['mem_cd2']]['kind'];

							if (!empty($familyKind)){
								//��������, �����ڵ�, 2�ο���
								$html .= ' =Y ='.$familyKind.' =Y';
							}else{
								$html .= ' =N =00 =N'; #��� ���2���а�.
							}
						}else{
							$html .= ' =N';
						}

						$html .= '\'';
					 */

					//0 : careJuminNo
					$html .= ' str=\''.$svc['mem_cd1'];

					//1 : ����
					$html .= ' ='.$yymm.($day<10?'0':'').$day;

					//2 : careNm
					$html .= ' =#careNm';

					//3 : qlfNo
					$html .= ' =#qlfNo';

					//4 : qlfKind
					$html .= ' =#qlfKind';

					//5 : togatherYn
					$html .= ' =N';

					//6 : ���۽ð�
					$html .= ' ='.$svc['f_time'];

					//7 : ����ð�
					$html .= ' ='.$svc['t_time'];

					//8 : �����ڵ�
					$html .= ' ='.$svc['long_cd'];

					//9 : �ݾ�
					$html .= ' ='.$svc['suga_pay'];

					//10 : ���
					$html .= ' =#srcAmdtGradeCd';

					//11 : ����
					$html .= ' =#srcTgtPrsnCd';

					if ($svcSubKind == '500'){//��常����
						//12 : careJuminNo2
						$html .= ' =#juminNo2';

						//13 : careNm2
						$html .= ' =#careNm2';

						//14 : qlfNo2
						$html .= ' =#qlfNo2';

						//15 : qlfKind2
						$html .= ' =#qlfKind2';

						//��ȣ��1�� �������� ��������
						$familyKind1 = $arrClientFamily[$svc['mem_cd1']]['kind'];

						//��ȣ��2�� �������� ��������
						$familyKind2 = $arrClientFamily[$svc['mem_cd2']]['kind'];

						//16 : familyYn1
						if ($familyKind1){
							$html .= ' =Y';
						}else{
							$html .= ' =N';
						}

						//17 : ��������
						if ($familyKind1){
							$html .= ' ='.$familyKind1;
						}else{
							$html .= ' =00';
						}

						//18 : familyYn2
						if ($familyKind2){
							$html .= ' =Y';
						}else{
							$html .= ' =N';
						}

						//19 : ��������
						if ($familyKind2){
							$html .= ' ='.$familyKind2;
						}else{
							$html .= ' =00';
						}

						//20 : togatherYn2
						$html .= ' =Y';

						//21 : pgmMngrName
						$html .= ' =N';

						//22 : excsDd15Yn1
						$html .= ' =N';

						//23 : fmlyHldayYn1
						$html .= ' =N';

						//24 : hltMgmtSvcYn1
						$html .= ' =N';

					//}else if( document.form1.serviceKind.value == "001" && document.form1.admtGradeCd.value == "E" && document.form1.payMm.value >= 201407 ){
					}else if ($svcSubKind == '200' && $lvl == '5' && $yymm >= '201407'){
						//12 : careJuminNo2
						$html .= ' =#juminNo2';

						//13 : careNm2
						$html .= ' =#careNm2';

						//14 : qlfNo2
						$html .= ' =#qlfNo2';

						//15 : qlfKind2
						$html .= ' =#qlfKind2';

						//��ȣ��1�� �������� ��������
						$familyKind1 = $arrClientFamily[$svc['mem_cd1']]['kind'];

						//��ȣ��2�� �������� ��������
						$familyKind2 = $arrClientFamily[$svc['mem_cd2']]['kind'];

						//16 : familyYn1
						if ($familyKind1){
							$html .= ' =Y';
						}else{
							$html .= ' =N';
						}

						//17 : ��������
						if ($familyKind1){
							$html .= ' ='.$familyKind1;
						}else{
							$html .= ' =00';
						}

						//18 : p100ResultString=="Y" ||togatherExtString == "Y" ||chk_age == "Y"
						$html .= ' =#srcResultString';

						//19 : pgmMngrName
						$html .= ' =Y';

						//20 : excsDd15Yn1
						$html .= ' =N';

						//21 : fmlyHldayYn1
						$html .= ' =N';

						//22 : hltMgmtSvcYn1
						$html .= ' =N';

					}else{
						//��ȣ��1�� �������� ��������
						$familyKind1 = $arrClientFamily[$svc['mem_cd1']]['kind'];

						//12 : familyYn1
						if ($familyKind1){
							$html .= ' =Y';
						}else{
							$html .= ' =N';
						}

						//13 : familyRel1
						if ($familyKind1){
							$html .= ' ='.$familyKind1;
						}else{
							$html .= ' =00';
						}

						//14 : p100ResultString=="Y" ||togatherExtString == "Y" ||chk_age == "Y"
						$html .= ' =#srcResultString';

						//15 : pgmMngrName
						$html .= ' =N';

						//16 : excsDd15Yn1
						$html .= ' =N';

						//17 : fmlyHldayYn1
						$html .= ' =N';

						//18 : hltMgmtSvcYn1
						$html .= ' =N';
					}

					$html .= '\'';
				}else{
					$html .= ' str=\'\'';
				}

				$html .= '>'.$day.'</div>';
			}

			$html .= '		</div>
						</td>
					  </tr>';
		}
	}

	$html .= '	</tbody>
			  </table>
			  </body>
			  </html>';

	$conn->row_free();

	$html .= '				</div>
						</td>
					</tr>
				</tbody>
			  </table>';

	$html .= '</div>
			  <div id=\'longcareData\' style=\'width:100%;\'></div>';

	$html .= '<input id=\'code\' name=\'code\' type=\'hidden\' value=\''.$code.'\'>
			  <input id=\'giho\' name=\'giho\' type=\'hidden\' value=\''.$lsGiho.'\'>
			  <input id=\'year\' name=\'year\' type=\'hidden\' value=\''.substr($yymm,0,4).'\'>
			  <input id=\'month\' name=\'month\' type=\'hidden\' value=\''.substr($yymm,4,2).'\'>
			  <input id=\'size\' name=\'size\' type=\'hidden\' value=\''.$lastday.'\'>
			  <div id=\'strCenterName\' style=\'display:none;\'>'.$conn->center_name($code).'</div>';


	$html .= '<script type=\'text/javascript\' src=\'../js/prototype.js\'></script>
			  <script type=\'text/javascript\' src=\'../js/jquery.js\'></script>
			  <script type=\'text/javascript\' src=\'../js/xmlHTTP.js\'></script>
			  <script type=\'text/javascript\' src=\'./iljung.longcare.js\' charset=\'euc-kr\'></script>
			  <script type=\'text/javascript\' src=\'./iljung.longcare.result.js\' charset=\'euc-kr\'></script>';

	if ($nextYn == 'Y'){
		//if ($code == '31138000058'){
		//	$lbAdmin = true;
		//}

		$html .= '<script type=\'text/javascript\'>
					function fn_refresh(){

					}

					window.onload = function(){
						_iljungGetLongTermMgmtNo(\''.$svcKind.'\',\''.$uploadYN.'\',\''.$paraNo.'\',\''.$mgmtYn.'\',\''.$jumin.'\',\''.$lbAdmin.'\',\''.$chgSayu.'\',\''.$chgSayuEtc.'\',\''.$winID.'\');
					}';

		if ($winID){
			$html .= '
					window.onunload = function(){
						_iljungWinClose("'.$winID.'");
					}';
		}

		$html .= '</script>';
		//setTimeout("_iljungSetHis(\''.$jumin.'\',\''.$svcKind.'\',\''.$yymm.'\')",10);
	}else{
		/*
		$html .= '<script type=\'text/javascript\'>
					window.onload = function(){
					}';

		if ($debug){
			if ($winID){
				$html .= '
						window.onunload = function(){
							_iljungWinClose("'.$winID.'");
						}';
			}
		}

		$html .= '</script>';
		*/

		//setTimeout("_iljungSetHis(\''.$jumin.'\',\''.$svcKind.'\',\''.$yymm.'\')",10);
		//_iljungLongcareAjax(\''.$yymm.'\',\'1234567890\',\'L1111111111\',\''.$svcKind.'\',\'�����ٶ� ABCD �����ٶ�\');
	}

	//echo $myF->_gabSplitHtml($html);
	echo $html;

	/*
	$(document).ready(function(){
					_iljungGetLongTermMgmtNo("'.$svcKind.'");
				});

				function fn_refresh(){ }
	*/

	include_once('../inc/_db_close.php');
?>