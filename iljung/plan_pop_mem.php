<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code   = $_POST['code'];
	$jumin  = $_POST['jumin'];
	$svcCd	= $_POST['svcCd'];
	$date   = $_POST['date'];
	$memCd1 = $_POST['memCd1'];
	$memNm1 = $_POST['memNm1'];
	$memCd2 = $_POST['memCd2'];
	$memNm2 = $_POST['memNm2'];
	$from   = $_POST['from'];
	$to     = $_POST['to'];
	$tmpSvcCd = $_POST['tmpSvcCd'];

	$lsMsg = '';

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	if (!is_numeric($memCd1)) $memCd1 = $ed->de($memCd1);
	if (!is_numeric($memCd2)) $memCd2 = $ed->de($memCd2);

	if ($tmpSvcCd == '0' || $tmpSvcCd == '5'){
		//주야간보호 일정을 확인한다.
		$sql = 'SELECT	COUNT(*)
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$code.'\'
				AND		t01_mkind		= \''.($tmpSvcCd == '0' ? '5' : '0').'\'
				AND		t01_jumin		= \''.$jumin.'\'
				AND		t01_sugup_date	= \''.$date.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$lsMsg = '<span style="font-weight:bold; color:RED;">주야간보호 일정중복</span><br>';
		}
	}

	$liFrom = $myF->time2min($from);
	$liTo   = $myF->time2min($to);

	if ($liTo < $liFrom)
		$liTo = $liTo + 24 * 60;

	//수급자리스트
	$sql = 'select distinct m03_jumin as jumin
			,      m03_name as name
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'';
	$laClient = $conn->_fetch_array($sql,'jumin');

	for($m=1; $m<=2; $m++){
		if ($m == 1){
			$lsMemCd = $memCd1;
			$lsMemNm = $memNm1;
		}else{
			$lsMemCd = $memCd2;
			$lsMemNm = $memNm2;
		}

		if (!empty($lsMemCd)){
			$sql = 'select t01_jumin as jumin
					,      t01_mkind as kind
					,      t01_sugup_fmtime as f_time
					,      t01_sugup_totime as t_time
					  from t01iljung
					 where t01_ccode      = \''.$code.'\'
					   and t01_jumin     != \''.$jumin.'\'
					   and t01_mem_cd1    = \''.$lsMemCd.'\'
					   and t01_sugup_date = \''.$date.'\'
					   and t01_del_yn     = \'N\'
					 union all
					select t01_jumin
					,      t01_mkind
					,      t01_sugup_fmtime
					,      t01_sugup_totime
					  from t01iljung
					 where t01_ccode      = \''.$code.'\'
					   and t01_jumin     != \''.$jumin.'\'
					   and t01_mem_cd2    = \''.$lsMemCd.'\'
					   and t01_sugup_date = \''.$date.'\'
					   and t01_del_yn     = \'N\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				$liChkF = $myF->time2min($row['f_time']);
				$liChkT = $myF->time2min($row['t_time']);

				if ($lsMemNm){
					if ($liChkF + $liChkT > 0){
						if (($liFrom <= $liChkF && $liTo > $liChkF) ||
							($liFrom < $liChkT && $liTo >= $liChkT) ||
							($liFrom > $liChkF && $liTo < $liChkT)){
							$lsMsg .= '<span style="line-height:20px; color:#ff0000; font-weight:bold; padding-right:5px;">일정중복</span><span style="line-height:20px; font-weight:bold;">'.$lsMemNm.'</span><br>';
							$lsMsg .= '<span style="line-height:20px; padding-left:13px;">수급자 : </span><span style="font-weight:bold; padding-right:10px;">'.$laClient[$row['jumin']]['name'].'</span>|';
							$lsMsg .= '<span style="line-height:20px; padding-left:10px;">서비스 : </span><span style="font-weight:bold; padding-right:10px;">'.$conn->_svcNm($row['kind']).'</span>|';
							$lsMsg .= '<span style="line-height:20px; padding-left:10px;">시간 : </span><span style="font-weight:bold;">'.$myF->timeStyle($row['f_time']).'~'.$myF->timeStyle($row['t_time']).'</span><br>';
						}
					}
				}
			}

			$conn->row_free();
		}
	}

	echo $lsMsg;

	include_once('../inc/_db_close.php');
?>