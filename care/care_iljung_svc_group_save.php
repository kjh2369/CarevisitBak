<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$grpCd	= $_POST['grpCd'];
	$sugaCd = $_POST['suga'];
	$seq	= $_POST['seq'];
	$resCd	= $_POST['res'];
	$memCd	= $ed->de($_POST['memCd']);
	$tmp	= Explode('?',$_POST['target']);
	$ym		= $_POST['year'].$_POST['month'];

	if ($grpCd){
		$newGrp = false;
	}else{
		$newGrp = true;
	}

	//자원명
	$sql = 'SELECT	cust_nm
			FROM	care_cust
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cust_cd	= \''.$resCd.'\'';

	$resNm = $conn->get_data($sql);

	foreach($tmp as $t){
		if ($t) $target[] = $t;
	}

	$tgCnt = SizeOf($target);

	if ($grpCd){
		$sql = 'SELECT	tg_info
				FROM	care_svc_iljung
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		grp_cd	= \''.$grpCd.'\'';

		$tmp = Explode('?',$conn->get_data($sql));
		foreach($tmp as $t)if ($t) $tg[] = $t;

		//기존삭제
		foreach($tg as $row){
			parse_str($row,$val);
			
			//$jumin	= $ed->de($val['jumin']);
			
			$sql = 'SELECT m03_jumin
					FROM   m03sugupja
					WHERE  m03_ccode = \''.$orgNo.'\'
					AND    m03_mkind = \'6\'
					AND    m03_key   = \''.$val['key'].'\'';
			$jumin = $conn->get_data($sql); 		
			

			$sql = 'DELETE
					FROM	t01iljung
					WHERE	t01_ccode		= \''.$orgNo.'\'
					AND		t01_mkind		= \''.$SR.'\'
					AND		t01_jumin		= \''.$jumin.'\'
					AND		t01_request		= \''.$grpCd.'\'';

			$query[] = $sql;

			$conn->begin();

			foreach($query as $sql){
				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 9;
					 exit;
				}
			}

			$conn->commit();
		}

		unset($query);

	}else{
		//다음키
		$sql = 'SELECT	IFNULL(MAX(grp_cd),\'\')
				FROM	care_svc_iljung
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		grp_cd	LIKE \''.$ym.'%\'';

		$grpCd = $conn->get_data($sql);

		if ($grpCd){
			$grpCd ++;
		}else{
			$grpCd = $ym.'0001';
		}
	}

	//그룹저장
	if ($newGrp){
		$sql = 'INSERT INTO care_svc_iljung (org_no,org_type,grp_cd,suga_cd,seq,mem_cd,date,time,conf_yn,tg_info,tg_cnt,insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$grpCd.'\'
				,\''.$sugaCd.'\'
				,\''.$seq.'\'
				,\''.$memCd.'\'
				,\''.$_POST['date'].'\'
				,\''.$_POST['time'].'\'
				,\''.$_POST['conf'].'\'
				,\''.$_POST['target'].'\'
				,\''.$tgCnt.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}else{
		$sql = 'UPDATE	care_svc_iljung
				SET		mem_cd		= \'\'
				,		date		= \''.$_POST['date'].'\'
				,		time		= \''.$_POST['time'].'\'
				,		conf_yn		= \''.$_POST['conf'].'\'
				,		tg_info		= \''.$_POST['target'].'\'
				,		tg_cnt		= \''.$tgCnt.'\'
				,		mem_cd		= \''.$memCd.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		grp_cd	= \''.$grpCd.'\'';
	}

	$query[] = $sql;

	$sl	 = 'INSERT INTO t01iljung (
			 t01_ccode			/*기관코드*/
			,t01_mkind			/*서비스구분*/
			,t01_jumin			/*주민번호*/
			,t01_sugup_date		/*일자*/
			,t01_sugup_fmtime	/*시작시간*/
			,t01_sugup_seq		/*순번*/
			,t01_sugup_yoil		/*요일*/
			,t01_svc_subcode	/*서비스종류*/
			,t01_status_gbn		/*상태*/

			,t01_yoyangsa_id1	/*실행 주요양보호사*/
			,t01_yoyangsa_id2	/*실행 부요양보호사*/
			,t01_yname1			/*요양보호사명*/
			,t01_yname2			/*요양보호사명*/

			,t01_mem_cd1		/*계획 주요양보호사*/
			,t01_mem_cd2		/*계획 부요양보호사*/
			,t01_mem_nm1		/*요양보호사명*/
			,t01_mem_nm2		/*요양보호사명*/

			,t01_suga_code1		/*수가코드*/
			,t01_suga			/*수가*/
			,t01_suga_tot		/*수가총액*/

			,t01_svc_subcd		/*S:재가지원 R:자원연계*/
			,t01_request

			) values (';

	foreach($target as $row){
		parse_str($row,$val);

		$jumin	= $ed->de($val['jumin']);
		$date	= $val['date'];
		$time	= $val['time'];

		if (!$jumin){
			$sql = 'SELECT	m03_jumin
					FROM	m03sugupja
					WHERE	m03_ccode	= \''.$orgNo.'\'
					AND		m03_mkind	= \'6\'
					AND		m03_key		= \''.$val['key'].'\'';

			$jumin = $conn->get_data($sql);
		}

		if ($time == '1'){
			$time = '1000';
		}else{
			$time = '1300';
		}

		$statGbn = $val['conf'];

		if ($statGbn == 'Y'){
			$statGbn = '1';
		}else{
			$statGbn = '9';
		}

		//재가지원은 계획과 실적을 같이 저장한다.
		if ($SR == 'S') $statGbn = '1';

		$weekly = Date('w',StrToTime($date));
		$subCd = '26';

		$mCd = $ed->de($val['memCd']);
		$mNm = $val['memNm'];

		//순번
		if ($iljungSeq[$jumin][$date] > 0){
			$iljungSeq[$jumin][$date] ++;
		}else{
			$sql = 'SELECT	IFNULL(MAX(t01_sugup_seq),0)+1
					FROM	t01iljung
					WHERE	t01_ccode		= \''.$orgNo.'\'
					AND		t01_mkind		= \''.$SR.'\'
					AND		t01_jumin		= \''.$jumin.'\'
					AND		t01_sugup_date	= \''.$date.'\'';

			$iljungSeq[$jumin][$date] = $conn->get_data($sql);
		}

		$sql = $sl.'
			 \''.$orgNo.'\'
			,\''.$SR.'\'
			,\''.$jumin.'\'
			,\''.$date.'\'
			,\''.$time.'\'
			,\''.$iljungSeq[$jumin][$date].'\'
			,\''.$weekly.'\'
			,\''.$subCd.'\'
			,\''.$statGbn.'\'

			,\''.$resCd.'\'
			,\''.$mCd.'\'
			,\''.$resNm.'\'
			,\''.$mNm.'\'

			,\''.$resCd.'\'
			,\''.$mCd.'\'
			,\''.$resNm.'\'
			,\''.$mNm.'\'

			,\''.$sugaCd.'\'
			,\'0\'
			,\'0\'

			,\''.$SR.'\'
			,\''.$grpCd.'\'
			)';

		$query[] = $sql;
	}

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();
	//echo $grpCd;
	echo 1;

	include_once('../inc/_db_close.php');
?>