<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$memCd = $_POST['memCd'];
	$year  = $_POST['year'];
	$month = $_POST['month'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	if (!is_numeric($memCd)) $memCd = $ed->de($memCd);

	/*
		$sql = 'select cast(substring(t01_sugup_date,7) as unsigned) as dt
				,      t01_sugup_fmtime as f_time
				,      t01_sugup_totime as t_time
				  from t01iljung
				 where t01_ccode   = \''.$code.'\'
				   and t01_jumin  != \''.$jumin.'\'
				   and t01_mem_cd1 = \''.$memCd.'\'
				   and t01_del_yn  = \'N\'
				   and left(t01_sugup_date,6) = \''.$year.$month.'\'
				 union
				select cast(substring(t01_sugup_date,7) as unsigned)
				,      t01_sugup_fmtime
				,      t01_sugup_totime
				  from t01iljung
				 where t01_ccode   = \''.$code.'\'
				   and t01_jumin  != \''.$jumin.'\'
				   and t01_mem_cd2 = \''.$memCd.'\'
				   and t01_del_yn  = \'N\'
				   and left(t01_sugup_date,6) = \''.$year.$month.'\'';
	 */
	$svcList	= $conn->svcKindSort($code,$gHostSvc['voucher']);
	$sql		= '';

	foreach($svcList as $svcGbn => $arrSvc){
		foreach($arrSvc as $idx => $svc){
			if ($sql){
				$sql .= '	UNION	ALL';
			}
			$sql .= '	SELECT	CAST(SUBSTRING(t01_sugup_date,7) AS unsigned) AS dt
						,		t01_sugup_fmtime AS f_time
						,		t01_sugup_totime AS t_time
						,		t01_svc_subcode AS sub_cd
						,		\'1\' AS mem_pos
						FROM	t01iljung
						WHERE	t01_ccode				 = \''.$code.'\'
						AND		t01_mkind				 = \''.$svc['code'].'\'
						AND		t01_jumin				!= \''.$jumin.'\'
						AND		t01_mem_cd1				 = \''.$memCd.'\'
						AND		t01_del_yn				 = \'N\'
						AND		left(t01_sugup_date,6)	 = \''.$year.$month.'\'';

			if ($svc['code'] == '0' || $svc['code'] == '4'){
				/*
					$sql .= '	UNION	ALL
								SELECT	CAST(SUBSTRING(t01_sugup_date,7) AS unsigned)
								,		t01_sugup_fmtime
								,		t01_sugup_totime
								,		t01_svc_subcode
								,		\'2\'
								FROM	t01iljung
								WHERE	t01_ccode				 = \''.$code.'\'
								AND		t01_mkind				 = \''.$svc['code'].'\'
								AND		t01_jumin				!= \''.$jumin.'\'
								AND		t01_mem_cd2				 = \''.$memCd.'\'
								AND		t01_del_yn				 = \'N\'
								AND		left(t01_sugup_date,6)	 = \''.$year.$month.'\'';
				 */
				$sql .= '	UNION	ALL
							SELECT	a.date, a.f_time, a.t_time, a.sub_cd, a.mem_pos
							FROM	(
									SELECT	CAST(SUBSTRING(t01_sugup_date,7) AS unsigned) AS date
									,		t01_sugup_fmtime AS f_time
									,		t01_sugup_totime AS t_time
									,		t01_svc_subcode AS sub_cd
									,		\'2\' AS mem_pos
									,		IFNULL(a.level, \'9\') AS lvl
									FROM	t01iljung
									LEFT	JOIN client_his_lvl AS a
											ON a.org_no	 = t01_ccode
											AND a.svc_cd = t01_mkind
											AND a.jumin	 = t01_jumin
											AND REPLACE(a.from_dt, \'-\', \'\') <= t01_sugup_date
											AND REPLACE(a.to_dt, \'-\', \'\') >= t01_sugup_date
									WHERE	t01_ccode				 = \''.$code.'\'
									AND		t01_mkind				 = \''.$svc['code'].'\'
									AND		t01_jumin				!= \''.$jumin.'\'
									AND		t01_mem_cd2				 = \''.$memCd.'\'
									AND		t01_del_yn				 = \'N\'
									AND		left(t01_sugup_date,6)	 = \''.$year.$month.'\'
									) AS a
							WHERE	a.lvl != \'5\'';
			}
		}
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$loArr[$row['dt']] .= $row['f_time'].'/'.$row['t_time'].'/'.$row['sub_cd'].'/'.$row['mem_pos'].';';
	}

	$conn->row_free();

	if (is_array($loArr)){
		foreach($loArr as $i => $row){
			$str .= (!empty($str) ? '&' : '').$i.'='.$row;
		}
	}

	echo $str;

	include_once('../inc/_db_close.php');
?>