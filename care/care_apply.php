<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$userCd	= $_SESSION['userCode'];

	if ($type == '1_POP'){
		$sugaCd = $_POST['cd'];
		$name	= $_POST['nm'];
		$seq	= $_POST['seq'];
		$cost	= $_POST['cost'];
		$from	= $_POST['from'];
		$to		= $_POST['to'];
		$reYn	= $_POST['reYn'];
		$modFlag= $_POST['modifyName'];
		$reason = 0;

		$from = str_replace('-', '', $myF->dateStyle($from));
		$to = str_replace('-', '', $myF->dateStyle($to));

		$subCd	= SubStr($sugaCd,5,2);
		$sugaCd	= SubStr($sugaCd,0,5);

		//중복여부
		if ($reYn == 'Y'){
			$sql = 'SELECT	suga_seq
					,		replace(min(from_dt),\'-\',\'\') as from_dt
					,		replace(max(to_dt),\'-\',\'\') as to_dt
					FROM	care_suga
					WHERE	org_no = \''.$code.'\'
					AND		suga_sr = \''.$sr.'\'
					AND		suga_cd = \''.$sugaCd.'\'
					AND		suga_sub= \''.$subCd.'\'
					ORDER	BY suga_seq DESC
					LIMIT	1';
		}else {

			$sql = 'SELECT	suga_seq
					,		from_dt
					,		to_dt
					FROM	care_suga
					WHERE	org_no = \''.$code.'\'
					AND		suga_sr = \''.$sr.'\'
					AND		suga_cd = \''.$sugaCd.'\'
					AND		suga_sub= \''.$subCd.'\'
					AND		suga_seq < \''.$seq.'\'
					ORDER	BY suga_seq DESC
					LIMIT	1';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($from < $row['from_dt'] || $to < $row['from_dt']){
				$reason = 91; //계약이전
				break;
			}else if ($from >= $row['from_dt'] && $from <= $row['to_dt']){
				$reason = 92; //계약중복
				break;
			}else if ($to >= $row['from_dt'] && $to <= $row['to_dt']){
				$reason = 92; //계약중복
				break;
			}
		}

		$conn->row_free();

		if ($reason > 0){
			if ($reason == 91){
				echo '이전 등록일자가 존재합니다.';
			}else if ($reason == 92){
				echo '등록일자가 중복됩니다.';
			}else{
				echo '9';
			}
			exit;
		}

		if (!$subCd){
			$sql = 'SELECT	MAX(suga_sub)
					FROM	care_suga
					WHERE	org_no = \''.$code.'\'
					AND		suga_sr = \''.$sr.'\'
					AND		suga_cd = \''.$sugaCd.'\'';

			$subCd = '0'.(IntVal($conn->get_data($sql)) + 1);
			$subCd = SubStr($subCd,StrLen($subCd)-2,StrLen($subCd));
		}

		if ($reYn == 'Y'){
			$sql = 'SELECT	IFNULL(MAX(suga_seq),0)+1
					FROM	care_suga
					WHERE	org_no = \''.$code.'\'
					AND		suga_sr = \''.$sr.'\'
					AND		suga_cd = \''.$sugaCd.'\'
					AND		suga_sub= \''.$subCd.'\'';

			$seq = $conn->get_data($sql);
			$new = true;
		}else{
			$sql = 'SELECT	MAX(suga_seq)
					FROM	care_suga
					WHERE	org_no = \''.$code.'\'
					AND		suga_sr = \''.$sr.'\'
					AND		suga_cd = \''.$sugaCd.'\'
					AND		suga_sub= \''.$subCd.'\'';

			$seq = $conn->get_data($sql);

			if (Empty($seq)){
				$seq = 1;
				$new = true;
			}else{
				$new = false;
			}
		}

		$conn->begin();

		if ($new){
			$sql = 'INSERT INTO care_suga (
					 org_no
					,suga_sr
					,suga_cd
					,suga_sub
					,suga_seq
					,suga_nm
					,suga_cost
					,from_dt
					,to_dt
					,insert_dt
					,insert_id) VALUES (
					 \''.$code.'\'
					,\''.$sr.'\'
					,\''.$sugaCd.'\'
					,\''.$subCd.'\'
					,\''.$seq.'\'
					,\''.$name.'\'
					,\''.$cost.'\'
					,\''.$from.'\'
					,\''.$to.'\'
					,NOW()
					,\''.$_SESSION['userCode'].'\'
					)';
		}else{
			$sql = 'UPDATE	care_suga
					SET		suga_cost	= \''.$cost.'\'
					,		from_dt		= \''.$from.'\'
					,		to_dt		= \''.$to.'\'
					WHERE	org_no		= \''.$code.'\'
					AND		suga_sr		= \''.$sr.'\'
					AND		suga_cd		= \''.$sugaCd.'\'
					AND		suga_sub	= \''.$subCd.'\'
					AND		suga_seq	= \''.$seq.'\'';
		}

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		if ($modFlag == 'Y'){
			$sql = 'UPDATE	care_suga
					SET		suga_nm = \''.$name.'\'
					WHERE	org_no	= \''.$code.'\'
					AND		suga_sr	= \''.$sr.'\'
					AND		suga_cd	= \''.$sugaCd.'\'
					AND		suga_sub= \''.$subCd.'\'';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo '9';
				 exit;
			}
		}

		$conn->commit();

	}else if ($type == '2'){
		//수가단위
		$year = $_POST['year'];
		$sugaCd = $_POST['cd'];
		$unitGbn = $_POST['gbn'];

		$sql = 'SELECT	COUNT(*)
				FROM	care_suga_unit
				WHERE	year = \''.$year.'\'
				AND		suga_cd = \''.$sugaCd.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	care_suga_unit
					SET		unit_gbn = \''.$unitGbn.'\'
					WHERE	year = \''.$year.'\'
					AND		suga_cd = \''.$sugaCd.'\'';
		}else{
			$sql = 'INSERT INTO care_suga_unit(
					 year
					,suga_cd
					,unit_gbn) VALUES (
					 \''.$year.'\'
					,\''.$sugaCd.'\'
					,\''.$unitGbn.'\'
					)';
		}

		$conn->execute($sql);

	}else if ($type == '1_POP_DELETE'){
		//삭제전 일정등록 내역을 확인한다.
		$sql = 'SELECT	COUNT(*)
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$code.'\'
				AND		t01_mkind		= \''.$_POST['sr'].'\'
				AND		t01_suga_code1	= \''.$_POST['cd'].'\'
				AND     t01_sugup_date  >= \''.str_replace('-','', $_POST['fmDt']).'\'
				AND     t01_sugup_date  <= \''.str_replace('-','', $_POST['toDt']).'\'
				AND		t01_del_yn		= \'N\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$conn->close();
			echo $cnt;
			exit;
		}

		//상세서비스 삭제
		$sql = 'DELETE
				FROM	care_suga
				WHERE	org_no	= \''.$code.'\'
				AND		suga_sr	= \''.$_POST['sr'].'\'
				AND		suga_seq= \''.$_POST['seq'].'\'
				AND		CONCAT(suga_cd,suga_sub) = \''.$_POST['cd'].'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'ERROR';
			 exit;
		}

		$conn->commit();


	}else if ($type == '11_POP'){
		/*********************************************************
		 *	자원등록
		 *********************************************************/
		$sr		= $_POST['sr'];		//구분
		$svc	= $_POST['svc'];	//수가
		$cd		= $_POST['cd'];		//자원코드
		$cust	= $_POST['cust'];	//거래처
		$cost	= Str_Replace(',','',$_POST['cost']);	//단가
		$from	= $_POST['fromDt'];	//적용일
		$to		= $_POST['toDt'];	//종료일

		$sub= SubStr($svc,5,2);
		$svc= SubStr($svc,0,5);

		$sql = 'SELECT	COUNT(*)
				FROM	care_resource
				WHERE	org_no		= \''.$code.'\'
				AND		care_sr		= \''.$sr.'\'
				AND		care_svc	= \''.$svc.'\'
				AND		care_cd		= \''.$cd.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){//수정
			$sql = 'UPDATE	care_resource
					SET		care_cust	= \''.$cust.'\'
					,		from_dt		= \''.$from.'\'
					,		to_dt		= \''.$to.'\'
					WHERE	org_no		= \''.$code.'\'
					AND		care_sr		= \''.$sr.'\'
					AND		care_svc	= \''.$svc.'\'
					AND		care_cd		= \''.$cd.'\'';
		}else{//신규
			$sql = 'SELECT	MAX(care_cd)
					FROM	care_resource
					WHERE	org_no	= \''.$code.'\'
					AND		care_sr	= \''.$sr.'\'
					AND		care_svc= \''.$svc.'\'
					';

			$cd = '000'.(IntVal($conn->get_data($sql))+1);
			$cd = SubStr($cd,StrLen($cd)-4,StrLen($cd));

			$sql = 'INSERT INTO care_resource (
					 org_no
					,care_sr
					,care_svc
					,care_cd
					,care_cust
					,from_dt
					,to_dt) VALUES (
					 \''.$code.'\'
					,\''.$sr.'\'
					,\''.$svc.'\'
					,\''.$cd.'\'
					,\''.$cust.'\'
					,\''.$from.'\'
					,\''.$to.'\'
					)';
		}

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		$conn->commit();

		echo '1';

	}else if ($type == '11_POP_DEL'){
		//자원삭제
		$svc= $_POST['svc'];
		$no	= $_POST['no'];
		$seq= $_POST['seq'];

		$sql = 'DELETE
				FROM	care_resource
				WHERE	org_no		= \''.$code.'\'
				AND		care_svc	= \''.$svc.'\'
				AND		care_no		= \''.$no.'\'
				AND		care_seq	= \''.$seq.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		$conn->commit();

		echo '1';

	}else if ($type == '11_DELETE'){
		/*********************************************************
		 *	자원삭제
		 *********************************************************/
		$svc = $_POST['svc'];
		$cd  = $_POST['cd'];

		$sql = 'UPDATE	care_resource
				SET		del_flag= \'Y\'
				WHERE	org_no	= \''.$code.'\'
				AND		care_sr	= \''.$sr.'\'
				AND		care_cd	= \''.$cd.'\'
				AND		CONCAT(care_svc,care_sub)= \''.$svc.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		$conn->commit();

		echo '1';


	}else if ($type == '21_POP'){
		//사업계획서
		$year	= $_POST['year'];
		$suga	= $_POST['code'];
		$target = $_POST['target'];
		$gbn	= $_POST['targetGbn'];
		$budget = $_POST['budget'];
		$cnt	= $_POST['cnt'];
		$cont	= AddSlashes($_POST['cont']);
		$effect = AddSlashes($_POST['effect']);
		$eval	= AddSlashes($_POST['eval']);

		$sql = 'SELECT	COUNT(*)
				FROm	care_year_plan
				WHERE	org_no		= \''.$code.'\'
				AND		plan_year	= \''.$year.'\'
				AND		plan_cd		= \''.$suga.'\'
				AND		plan_sr		= \''.$sr.'\'';

		$rowCnt = $conn->get_data($sql);

		if ($rowCnt > 0){
			$sql = 'UPDATE	care_year_plan
					SET		plan_target = \''.$target.'\'
					,		plan_target_gbn = \''.$gbn.'\'
					,		plan_budget = \''.$budget.'\'
					,		plan_cnt	= \''.$cnt.'\'
					,		plan_cont	= \''.$cont.'\'
					,		plan_effect = \''.$effect.'\'
					,		plan_eval	= \''.$eval.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$code.'\'
					AND		plan_year	= \''.$year.'\'
					AND		plan_cd		= \''.$suga.'\'
					AND		plan_sr		= \''.$sr.'\'';
		}else{
			$sql = 'INSERT INTO care_year_plan(
					 org_no
					,plan_year
					,plan_sr
					,plan_cd
					,plan_target
					,plan_target_gbn
					,plan_budget
					,plan_cnt
					,plan_cont
					,plan_effect
					,plan_eval
					,insert_id
					,insert_dt) VALUES (
					 \''.$code.'\'
					,\''.$year.'\'
					,\''.$sr.'\'
					,\''.$suga.'\'
					,\''.$target.'\'
					,\''.$gbn.'\'
					,\''.$budget.'\'
					,\''.$cnt.'\'
					,\''.$cont.'\'
					,\''.$effect.'\'
					,\''.$eval.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}

		if (!$conn->execute($sql)){
			echo '9';
			exit;
		}

		echo '1';

	}else if ($type == '41'){
		//실적등록(재가지원)
		$sr		= $_POST['sr'];
		$year	= $_POST['year'];
		$month	= IntVal($_POST['month']);
		$month	= ($month < 10 ? '0' : '').$month;
		$data	= Explode(chr(11),$_POST['data']);

		if (Is_Array($data)){
			$conn->begin();

			foreach($data as $row){
				parse_str($row,$col);

				$sql = 'UPDATE	t01iljung
						SET		t01_status_gbn	= \''.$col['stat'].'\'
						WHERE	t01_ccode		= \''.$code.'\'
						AND		t01_mkind		= \''.$sr.'\'
						AND		t01_jumin		= \''.$ed->de($col['jumin']).'\'
						AND		t01_sugup_date	= \''.$col['dt'].'\'
						AND		t01_sugup_fmtime = \''.$col['from'].'\'
						AND		t01_sugup_seq	= \''.$col['seq'].'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo '9';
					 exit;
				}
			}

			$conn->commit();

			Unset($col);

			echo '1';
		}

	}else if ($type == '42'){
		//실적등록(상담지원)
		$sr		= $_POST['sr'];
		$year	= $_POST['year'];
		$month	= IntVal($_POST['month']);
		$month	= ($month < 10 ? '0' : '').$month;
		$data	= Explode(chr(11),$_POST['data']);

		if (Is_Array($data)){
			$conn->begin();

			foreach($data as $row){
				parse_str($row,$col);

				$sql = 'UPDATE	care_counsel_iljung
						SET		iljung_stat = \''.$col['stat'].'\'
						WHERE	org_no = \''.$code.'\'
						AND		jumin = \''.$ed->de($col['jumin']).'\'
						AND		iljung_sr = \''.$sr.'\'
						AND		iljung_dt = \''.$year.$month.$col['date'].'\'
						AND		iljung_seq = \''.$col['seq'].'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo '9';
					 exit;
				}
			}

			$conn->commit();

			Unset($col);

			echo '1';
		}

	}else if ($type == '43'){
		//실적마감
		$sr		= $_POST['sr'];
		$year	= $_POST['year'];
		$month	= $myF->monthStr($_POST['month']);

		//대사장
		$sql = 'SELECT	m03_jumin AS jumin
				,		m03_name AS name
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$code.'\'
				AND		m03_mkind = \'6\'';

		$client = $conn->_fetch_array($sql, 'jumin');

		//수가정보
		$sql = 'SELECT	CONCAT(cd1,cd2,cd3) AS suga
				FROM	suga_care';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$tmpSugaStr .= '/'.$row['suga'];
		}

		$conn->row_free();

		if ($IsCareYoyAddon){
			//공통수가
			$sql = 'SELECT	code
					FROM	care_suga_comm';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);
				$tmpSugaStr .= '/'.$row['code'];
			}

			$conn->row_free();
		}

		//인원수
		$sql = 'SELECT	t01_sugup_date AS date
				,		t01_jumin AS jumin
				,		LEFT(t01_suga_code1,5) AS suga_cd
				,		CASE WHEN t01_yoyangsa_id2 != \'\'THEN t01_yoyangsa_id2 ELSE \'EMPTY\' END AS mem_cd
				,		COUNT(DISTINCT t01_jumin) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				AND		t01_status_gbn = \'1\'
				AND		t01_del_yn = \'N\'
				GROUP	BY LEFT(t01_suga_code1,5), t01_jumin, t01_yoyangsa_id2, t01_sugup_date
				UNION	ALL
				SELECT	reg_dt, \'\', LEFT(suga_cd, 5), \'EMPTY\', SUM(att_cnt)
				FROM	care_rpt
				WHERE	org_no	 = \''.$code.'\'
				AND		org_sr	 = \''.$sr.'\'
				AND		del_flag = \'N\'
				AND		LEFT(reg_dt, 6) = \''.$year.$month.'\'
				GROUP	BY LEFT(suga_cd, 5), reg_dt
				ORDER	BY suga_cd, mem_cd, date';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$jumin = $row['jumin'];
			$suagCd = $row['suga_cd'];
			$memCd = $row['mem_cd'];
			$date = $row['date'];
			$yymm = Date('Ym',StrToTime($date));

			//if ($client[$jumin]['name']){
			if ($suagCd){
				if (is_numeric(StrPos($tmpSugaStr, '/'.$sugaCd))){
					$perCnt[$suagCd][$date] += $row['cnt'];
					$perCnt[$memCd][$date] += $row['cnt'];
					$perCnt[$jumin][$date] += $row['cnt'];
					$perCnt[$suagCd][$yymm] += $row['cnt'];
					$perCnt[$memCd][$yymm] += $row['cnt'];
					$perCnt[$jumin][$yymm] += $row['cnt'];
				}
			}
		}

		$conn->row_free();

		//재가지원
		/*
		$sql = 'SELECT	\'1\' AS idx
				,		t01_sugup_date AS dt
				,		t01_suga_code1 AS suga_cd
				,		t01_yoyangsa_id1 AS res_cd
				,		SUM(t01_suga_tot) AS pay
				,		COUNT(*) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \'6\'
				AND		t01_svc_subcd = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				AND		t01_status_gbn = \'1\'
				AND		t01_del_yn = \'N\'
				GROUP	BY t01_sugup_date,t01_suga_code1,t01_yoyangsa_id1
				UNION	ALL
				SELECT	\'2\' AS idx
				,		iljung_dt AS dt
				,		\'CUNSL\' AS suga_cd
				,		jumin AS res_cd
				,		0 AS pay
				,		COUNT(*) AS cnt
				FROM	care_counsel_iljung
				WHERE	org_no = \''.$code.'\'
				AND		iljung_sr = \''.$sr.'\'
				AND		LEFT(iljung_dt,6) = \''.$year.$month.'\'
				AND		iljung_stat = \'1\'
				AND		del_flag = \'N\'
				GROUP	BY iljung_dt, jumin';
		*/

		/*
		$sql = 'SELECT	\'1\' AS idx
				,		t01_sugup_date AS dt
				,		LEFT(t01_suga_code1,5) AS suga_cd
				,		t01_yoyangsa_id1 AS res_cd
				,		SUM(t01_suga_tot) AS pay
				,		COUNT(*) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				AND		t01_status_gbn = \'1\'
				AND		t01_del_yn = \'N\'
				GROUP	BY t01_sugup_date,LEFT(t01_suga_code1,5),t01_yoyangsa_id1
				UNION	ALL
				SELECT	\'2\' AS idx
				,		t01_sugup_date AS dt
				,		LEFT(t01_suga_code1,5) AS suga_cd
				,		t01_yoyangsa_id2 AS res_cd
				,		SUM(t01_suga_tot) AS pay
				,		COUNT(*) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				AND		t01_status_gbn = \'1\'
				AND		t01_del_yn = \'N\'
				AND		t01_yoyangsa_id2 != \'\'
				GROUP	BY t01_sugup_date,LEFT(t01_suga_code1,5),t01_yoyangsa_id2';
		 */

		$sql = 'SELECT	\'1\' AS idx
				,		t01_sugup_date AS dt
				,		t01_jumin AS jumin
				,		LEFT(t01_suga_code1,5) AS suga_cd
				,		t01_yoyangsa_id1 AS res_cd
				,		SUM(t01_suga_tot) AS pay
				,		COUNT(*) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				AND		t01_status_gbn = \'1\'
				AND		t01_del_yn = \'N\'
				GROUP	BY t01_sugup_date,t01_jumin,LEFT(t01_suga_code1,5),t01_yoyangsa_id1
				UNION	ALL
				SELECT	\'2\' AS idx
				,		t01_sugup_date AS dt
				,		t01_jumin AS jumin
				,		LEFT(t01_suga_code1,5) AS suga_cd
				,		t01_yoyangsa_id2 AS res_cd
				,		SUM(t01_suga_tot) AS pay
				,		COUNT(*) AS cnt
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				AND		t01_status_gbn = \'1\'
				AND		t01_del_yn = \'N\'
				AND		t01_yoyangsa_id2 != \'\'
				GROUP	BY t01_sugup_date,t01_jumin,LEFT(t01_suga_code1,5),t01_yoyangsa_id2
				UNION	ALL
				SELECT	\'1\', reg_dt, \'\', LEFT(suga_cd, 5), \'\', 0, COUNT(*)
				FROM	care_rpt
				WHERE	org_no	 = \''.$code.'\'
				AND		org_sr	 = \''.$sr.'\'
				AND		del_flag = \'N\'
				AND		LEFT(reg_dt, 6) = \''.$year.$month.'\'
				GROUP	BY LEFT(suga_cd, 5), reg_dt';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$idx = $row['idx'];
			$date = $row['dt'];
			$yymm = SubStr($date,0,6);
			$sugaCd = $row['suga_cd'];
			$resCd = $row['res_cd'];
			$jumin = $row['jumin'];

			//if ($client[$jumin]['name']){
			if ($sugaCd){
				if (is_numeric(StrPos($tmpSugaStr, '/'.$sugaCd))){
					//수가별
					$day[$idx.'1'][$sugaCd][$date]['cnt'] += $row['cnt'];
					$day[$idx.'1'][$sugaCd][$date]['pay'] += $row['pay'];
					$mon[$idx.'1'][$sugaCd][$yymm]['cnt'] += $row['cnt'];
					$mon[$idx.'1'][$sugaCd][$yymm]['pay'] += $row['pay'];


					//자원별
					if ($resCd){
						$day[$idx.'2'][$resCd][$date]['cnt'] += $row['cnt'];
						$day[$idx.'2'][$resCd][$date]['pay'] += $row['pay'];
						$mon[$idx.'2'][$resCd][$yymm]['cnt'] += $row['cnt'];
						$mon[$idx.'2'][$resCd][$yymm]['pay'] += $row['pay'];
					}


					//대상자별
					if ($jumin){
						$day[$idx.'3'][$jumin][$date]['cnt'] += $row['cnt'];
						$day[$idx.'3'][$jumin][$date]['pay'] += $row['pay'];
						$mon[$idx.'3'][$jumin][$yymm]['cnt'] += $row['cnt'];
						$mon[$idx.'3'][$jumin][$yymm]['pay'] += $row['pay'];
					}
				}
			}
		}

		$conn->row_free();

		$conn->begin();

		//일별삭제
		$sql = 'DELETE
				FROM	care_close_day
				WHERE	org_no = \''.$code.'\'
				AND		close_sr = \''.$sr.'\'
				AND		LEFT(close_date,6) = \''.$year.$month.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		//월별삭제
		$sql = 'DELETE
				FROM	care_close_month
				WHERE	org_no = \''.$code.'\'
				AND		close_sr = \''.$sr.'\'
				AND		close_yymm = \''.$year.$month.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		//일별
		if (Is_Array($day)){
			foreach($day as $gbn => $arr1){
				foreach($arr1 as $cd => $arr2){
					foreach($arr2 as $dt => $row){
						$sql = 'INSERT INTO care_close_day(
								 org_no
								,close_sr
								,close_gbn
								,close_cd
								,close_date
								,close_cnt
								,close_pay
								,close_per) VALUES (
								 \''.$code.'\'
								,\''.$sr.'\'
								,\''.$gbn.'\'
								,\''.$cd.'\'
								,\''.$dt.'\'
								,\''.$row['cnt'].'\'
								,\''.$row['pay'].'\'
								,\''.$perCnt[$cd][$dt].'\'
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 $conn->close();
							 if ($debug) echo $conn->error_query;
							 echo 9;
							 exit;
						}
					}
				}
			}
		}

		//월별
		if (Is_Array($mon)){
			foreach($mon as $gbn => $arr1){
				foreach($arr1 as $cd => $arr2){
					foreach($arr2 as $yymm => $row){
						$sql = 'INSERT INTO care_close_month(
								 org_no
								,close_sr
								,close_gbn
								,close_cd
								,close_yymm
								,close_cnt
								,close_pay
								,close_per) VALUES (
								 \''.$code.'\'
								,\''.$sr.'\'
								,\''.$gbn.'\'
								,\''.$cd.'\'
								,\''.$yymm.'\'
								,\''.$row['cnt'].'\'
								,\''.$row['pay'].'\'
								,\''.$perCnt[$cd][$yymm].'\'
								)';

						if (!$conn->execute($sql)){
							 $conn->rollback();
							 $conn->close();
							 if ($debug) echo $conn->error_query;
							 echo 9;
							 exit;
						}
					}
				}
			}
		}


		//개별통계
		$sql = 'DELETE
				FROM	care_close_person
				WHERE	org_no		= \''.$code.'\'
				AND		close_sr	= \''.$sr.'\'
				AND		close_yymm	= \''.$year.$month.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 if ($debug) echo $conn->error_query;
			 echo 9;
			 exit;
		}

		$sql = 'SELECT	t01_sugup_date AS date
				,		t01_jumin AS jumin
				,		LEFT(t01_suga_code1,5) AS suga_cd
				,		t01_yoyangsa_id1 AS resource_cd
				,		t01_yoyangsa_id2 AS mem_cd
				,		t01_suga_tot AS pay
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$code.'\'
				AND		t01_mkind		= \''.$sr.'\'
				AND		t01_sugup_date >= \''.$year.$month.'01\'
				AND		t01_sugup_date <= \''.$year.$month.'31\'
				AND		t01_status_gbn	= \'1\'
				AND		t01_del_yn		= \'N\'
				ORDER	BY date, jumin, suga_cd, resource_cd, mem_cd';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$jumin = $row['jumin'];

			if ($client[$jumin]['name']){
				if (is_numeric(StrPos($tmpSugaStr, '/'.$row['suga_cd']))){
					if ($tmpKey != $jumin.'_'.$row['suga_cd'].'_'.$row['resource_cd'].'_'.$row['mem_cd']){
						$tmpKey  = $jumin.'_'.$row['suga_cd'].'_'.$row['resource_cd'].'_'.$row['mem_cd'];
						$IsAdd = true;
					}else{
						$IsAdd = false;
					}

					if ($IsAdd){
						$idx = SizeOf($data[$jumin]);

						$data[$jumin][$idx]['suga'] = $row['suga_cd'];
						$data[$jumin][$idx]['resource'] = $row['resource_cd'];
						$data[$jumin][$idx]['mem'] = $row['mem_cd'];
					}

					$data[$jumin][$idx]['cnt'] += 1;
					$data[$jumin][$idx]['pay'] += $row['pay'];

					if (!is_numeric(StrPos($data[$jumin][$idx]['date'],'/'.$row['date']))){
						$data[$jumin][$idx]['per'] += 1;
						$data[$jumin][$idx]['date'] .= '/'.$row['date'];
					}
				}else{
				}
			}
		}

		$conn->row_free();

		if (is_array($data)){
			foreach($data as $jumin => $arr){
				foreach($arr as $idx => $row){
					$sql = 'INSERT INTO care_close_person (
							 org_no
							,close_sr
							,close_yymm
							,close_jumin
							,close_idx
							,close_suga
							,close_resource
							,close_mem_cd
							,close_cnt
							,close_pay
							,close_per) VALUES (
							 \''.$code.'\'
							,\''.$sr.'\'
							,\''.$year.$month.'\'
							,\''.$jumin.'\'
							,\''.$idx.'\'
							,\''.$row['suga'].'\'
							,\''.$row['resource'].'\'
							,\''.$row['mem'].'\'
							,\''.$row['cnt'].'\'
							,\''.$row['pay'].'\'
							,\''.$row['per'].'\'
							)';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->close();
						 if ($debug) echo $conn->error_query;
						 echo 9;
						 exit;
					}
				}
			}
		}


		$conn->commit();


		echo 1;


	}else if ($type == '71_APPLY'){
		//거래처 적용
		$cd			= $_POST['cd'];
		$nm			= $_POST['nm'];
		$gbn		= $_POST['gbn'];
		$kindS		= $_POST['kindS'];
		$kindW		= $_POST['kindW'];
		$date		= $_POST['date'];
		$bizno		= Str_Replace('-','',$_POST['bizno']);
		$manager	= $_POST['manager'];
		$stat		= $_POST['stat'];
		$item		= $_POST['item'];
		$phone		= Str_Replace('-','',$_POST['phone']);
		$fax		= Str_Replace('-','',$_POST['fax']);
		$postno		= $_POST['postno'];
		$addr		= $_POST['addr'];
		$addrDtl	= $_POST['addrDtl'];
		$pernm		= $_POST['pernm'];
		$pertel		= Str_Replace('-','',$_POST['pertel']);
		$support	= 'Y'; //$_POST['support'];
		$resource	= 'N'; //$_POST['resource'];
		$userCd		= $_SESSION['userCode'];
		$today		= Date('Ymd');

		$sql = 'SELECT	COUNT(*)
				FROM	care_cust
				WHERE	org_no	= \''.$code.'\'
				AND		cust_cd	= \''.$cd.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	care_cust
					SET		cust_nm		= \''.$nm.'\'
					,		cust_gbn	= \''.$gbn.'\'
					,		supporter_yn= \''.$kindS.'\'
					,		worker_yn	= \''.$kindW.'\'
					,		reg_dt		= \''.$date.'\'
					,		biz_no		= \''.$bizno.'\'
					,		manager		= \''.$manager.'\'
					,		status		= \''.$stat.'\'
					,		item		= \''.$item.'\'
					,		phone		= \''.$phone.'\'
					,		fax			= \''.$fax.'\'
					,		postno		= \''.$postno.'\'
					,		addr		= \''.$addr.'\'
					,		addr_dtl	= \''.$addrDtl.'\'
					,		per_nm		= \''.$pernm.'\'
					,		per_phone	= \''.$pertel.'\'
					,		support_yn	= \''.$support.'\'
					,		resource_yn	= \''.$resource.'\'
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= \''.$today.'\'
					WHERE	org_no		= \''.$code.'\'
					AND		cust_cd		= \''.$cd.'\'';
		}else{
			$sql = 'INSERT INTO care_cust (
					 org_no
					,cust_cd
					,cust_nm
					,cust_gbn
					,supporter_yn
					,worker_yn
					,reg_dt
					,biz_no
					,manager
					,status
					,item
					,phone
					,fax
					,postno
					,addr
					,addr_dtl
					,per_nm
					,per_phone
					,support_yn
					,resource_yn
					,insert_id
					,insert_dt) VALUES (
					 \''.$code.'\'
					,\''.$cd.'\'
					,\''.$nm.'\'
					,\''.$gbn.'\'
					,\''.$kindS.'\'
					,\''.$kindW.'\'
					,\''.$date.'\'
					,\''.$bizno.'\'
					,\''.$manager.'\'
					,\''.$stat.'\'
					,\''.$item.'\'
					,\''.$phone.'\'
					,\''.$fax.'\'
					,\''.$postno.'\'
					,\''.$addr.'\'
					,\''.$addrDtl.'\'
					,\''.$pernm.'\'
					,\''.$pertel.'\'
					,\''.$support.'\'
					,\''.$resource.'\'
					,\''.$userCd.'\'
					,\''.$today.'\'
					)';
		}

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		$conn->commit();


	}else if ($type == '71_DELETE'){
		/*********************************************************
		 *	거래처삭제
		 *********************************************************/
		$cd	= $_POST['cd'];

		$sql = 'UPDATE	care_cust
				SET		del_flag= \'Y\'
				WHERE	org_no	= \''.$code.'\'
				AND		cust_cd	= \''.$cd.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		$conn->commit();

		echo '1';


	}else if ($type == 'REG_CLIENT'){
		/*********************************************************
		 *	재가지원, 자원연계 고객등록
		 *********************************************************/
		$sr		= $_POST['sr'];
		$jumin	= $_POST['jumin'];
		$name	= $_POST['txtName'];

		if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

		$sql = 'SELECT	code
				,		jumin
				FROM	mst_jumin
				WHERE	org_no	= \''.$code.'\'
				AND		gbn		= \'1\'
				AND		code	= \''.$jumin.'\'';

		$row = $conn->get_array($sql);

		$mstCd = $row['code'];
		$mstJm = $row['jumin'];

		Unset($row);

		if (!$jumin) $jumin	= $_POST['txtJumin1'].$_POST['txtJumin2'];

		$juminNo = $_POST['txtJumin1'].$_POST['txtJumin2'];

		/*
			if ($juminNo){
				$sql = 'SELECT	COUNT(*)
						FROM	m03sugupja
						WHERE	m03_ccode = \''.$code.'\'
						AND		m03_jumin = \''.$juminNo.'\'
						AND		m03_mkind = \'6\'';

				$liCnt = $conn->get_data($sql);

				if ($liCnt > 0){
					echo 9;
					exit;
				}
			}else{
				$juminNo = $jumin;
			}
		 */

		if ($mstCd){
			if ($mstJm != $juminNo && $juminNo){
				$sql = 'UPDATE	mst_jumin
						SET		jumin		= \''.$juminNo.'\'
						,		update_id	= \''.$userCd.'\'
						,		update_dt	= NOW()
						WHERE	org_no	= \''.$code.'\'
						AND		gbn		= \'1\'
						AND		code	= \''.$mstCd.'\'';

				$query[SizeOf($query)] = $sql;
			}

			$juminNo	= $mstCd;
			$mstSaveYn	= 'N';
		}else{
			//마스터 저장
			$mstJumin	= $juminNo;
			$mstSaveYn	= 'Y';

			//주민번호의 7자리까지의 코드를 생성한다.
			$juminNo = SubStr($juminNo,0,7);
			$sql = 'SELECT	CAST(IFNULL(RIGHT(MAX(code),6),0) + 1 AS unsigned)
					FROM	mst_jumin
					WHERE	org_no		= \''.$code.'\'
					AND		gbn			= \'1\'
					AND		LEFT(code,7)= \''.$juminNo.'\'';

			$juminSeq = '00000'.$conn->get_data($sql);
			$juminSeq = SubStr($juminSeq,StrLen($juminSeq)-6,6);
			$juminNo .= $juminSeq;
		}

		$jumin = $juminNo;

		$from	= $_POST['txtFrom_'.$sr];	//적용일
		$to		= $_POST['txtTo_'.$sr];		//종료일
		$stat	= $_POST['txtStat_'.$sr];	//이용상태
		$seq	= $_POST['txtSeq_'.$sr];	//계약순번
		$cost	= Str_Replace(',','',$_POST['txtSvcCost']);	//단가

		$phone	= Str_Replace('-','',$_POST['txtPhone']);	//연락처
		$mobile	= Str_Replace('-','',$_POST['txtMobile']);	//모바일

		$postno	= $_POST['txtPostno1'].$_POST['txtPostno2'];	//우편번호
		$addr	= $_POST['txtAddr'];	//주소
		$addrDtl= $_POST['txtAddrDtl'];	//상세주소

		$marry	= ($_POST['cboMarry'] ? $_POST['cboMarry'] : '-');		//결혼구분
		$cohabit= ($_POST['cboCohabit'] ? $_POST['cboCohabit'] : '-');	//동거구분
		$edu	= ($_POST['cboEdu'] ? $_POST['cboEdu'] : '--');			//학력
		$rel	= ($_POST['cboRel'] ? $_POST['cboRel'] : '-');			//종교

		$grdNm	= $_POST['txtGuardNm'];		//보호자명
		$grdAddr= $_POST['txtGuardAddr'];	//보호자 ㅈ소
		$grdTel	= Str_Replace('-','',$_POST['txtGuardTel']);	//보호자 연락처

		$orgNo	= $_POST['txtOrgNo'];	//서비스 기관
		$orgNm	= $_POST['txtOrgNm'];	//기관명
		$appNo	= $_POST['txtAppNo'];	//인정번호
		$lvl	= $_POST['cboLvl'];		//등급
		$gbn	= $_POST['cboGbn'];		//구분
		$pernm	= $_POST['txtPerNm'];	//담당자명
		$pertel	= Str_Replace('-','',$_POST['txtPerTel']);	//담당자연락처

		$linkSeq = $_POST['linkSeq'];


		//기존데이타 존재여부
		$sql = 'SELECT	m03_key
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$code.'\'
				AND		m03_jumin = \''.$jumin.'\'';

		$mstKey = $conn->get_data($sql);

		//서비스 개인정보 데이타 존재여부
		$sql = 'SELECT	COUNT(*)
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$code.'\'
				AND		m03_jumin = \''.$jumin.'\'
				AND		m03_mkind = \'6\'';

		$svcCnt = $conn->get_data($sql);

		if ($svcCnt <= 0){
			if ($mstKey){
				$key = $mstKey;
			}else{
				$sql = 'SELECT	IFNULL(MAX(m03_key),0)+1
						FROM	m03sugupja
						WHERE	m03_ccode = \''.$code.'\'';
				$key = $conn->get_data($sql);
			}

			$sql = 'INSERT INTO m03sugupja (
					 m03_ccode
					,m03_mkind
					,m03_jumin
					,m03_key) VALUES (
					 \''.$code.'\'
					,\'6\'
					,\''.$jumin.'\'
					,\''.$key.'\'
					)';

			$query[SizeOf($query)] = $sql;
		}

		//마스터저장
		if ($mstSaveYn == 'Y'){
			$sql = 'INSERT INTO mst_jumin(
					 org_no
					,gbn
					,code
					,jumin
					,name
					,cd_key
					,insert_id
					,insert_dt) VALUES (
					 \''.$code.'\'
					,\'1\'
					,\''.$juminNo.'\'
					,\''.$mstJumin.'\'
					,\''.$name.'\'
					,\''.$key.'\'
					,\''.$userCd.'\'
					,NOW())';

			$query[SizeOf($query)] = $sql;
		}

		$sql = 'UPDATE	m03sugupja
				SET		m03_name			= \''.$name.'\'
				,		m03_tel				= \''.$phone.'\'
				,		m03_hp				= \''.$mobile.'\'
				,		m03_post_no			= \''.$postno.'\'
				,		m03_juso1			= \''.$addr.'\'
				,		m03_juso2			= \''.$addrDtl.'\'
				,		m03_yboho_name		= \''.$grdNm.'\'
				,		m03_yoyangsa4_nm	= \''.$grdAddr.'\'
				,		m03_yboho_phone		= \''.$grdTel.'\'
				,		m03_yoyangsa5_nm	= \''.$marry.$cohabit.$edu.$rel.'\'';

		if ($jumin != $juminNo){
			$sql .= '
				,		m03_jumin			= \''.$juminNo.'\'';
		}

		$sql .= '
				WHERE	m03_ccode			= \''.$code.'\'
				AND		m03_jumin			= \''.$jumin.'\'';

		$query[SizeOf($query)] = $sql;

		//서비스 계약기간
		if (!Empty($seq)){
			/*
			$sql = 'UPDATE	client_his_svc
					SET		from_dt		= \''.$myF->dateStyle($from).'\'
					,		to_dt		= \''.$myF->dateStyle($to).'\'
					,		svc_stat	= \''.$stat.'\'';

			if ($jumin != $juminNo){
				$sql .= '
					,		jumin		= \''.$juminNo.'\'';
			}

			$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$code.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		svc_cd	= \''.$sr.'\'
					AND		seq		= \''.$seq.'\'';

			$query[SizeOf($query)] = $sql;
			*/
		}else{
			$sql = 'INSERT INTO client_his_svc (
					 org_no
					,jumin
					,svc_cd
					,seq
					,from_dt
					,to_dt
					,svc_stat
					,insert_id
					,insert_dt) VALUES (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\''.$sr.'\'
					,\'1\'
					,\''.$myF->dateStyle($from).'\'
					,\''.$myF->dateStyle($to).'\'
					,\''.$stat.'\'
					,\''.$userCd.'\'
					,NOW()
					)';

			$query[SizeOf($query)] = $sql;
		}

		//서비스 상세
		$sql = 'SELECT	COUNT(*)
				FROM	client_his_care
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		svc_cd	= \''.$sr.'\'
				AND		seq		= \'1\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	client_his_care
					SET		care_cost	= \''.$cost.'\'
					,		care_org_no	= \''.$orgNo.'\'
					,		care_org_nm	= \''.$orgNm.'\'
					,		care_no		= \''.$appNo.'\'
					,		care_lvl	= \''.$lvl.'\'
					,		care_gbn	= \''.$gbn.'\'
					,		care_pic_nm	= \''.$pernm.'\'
					,		care_telno	= \''.$pertel.'\'';

			if ($jumin != $juminNo){
				$sql .= '
					,		jumin		= \''.$juminNo.'\'';
			}

			$sql .= '
					,		update_id	= \''.$userCd.'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$code.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		svc_cd	= \''.$sr.'\'
					AND		seq		= \'1\'';

			$query[SizeOf($query)] = $sql;
		}else{
			$sql = 'INSERT INTO client_his_care (
					 org_no
					,jumin
					,svc_cd
					,seq
					,care_cost
					,care_org_no
					,care_org_nm
					,care_no
					,care_lvl
					,care_gbn
					,care_pic_nm
					,care_telno
					,insert_id
					,insert_dt) VALUES (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\''.$sr.'\'
					,\'1\'
					,\''.$cost.'\'
					,\''.$orgNo.'\'
					,\''.$orgNm.'\'
					,\''.$appNo.'\'
					,\''.$lvl.'\'
					,\''.$gbn.'\'
					,\''.$pernm.'\'
					,\''.$pertel.'\'
					,\''.$userCd.'\'
					,NOW()
					)';

			$query[SizeOf($query)] = $sql;
		}

		if ($linkSeq){
			$sql = 'UPDATE	care_client_normal
					SET		link_IPIN	= \''.$key.'\'
					WHERE	org_no		= \''.$code.'\'
					AND		normal_sr	= \''.$sr.'\'
					AND		normal_seq	= \''.$linkSeq.'\'';

			$query[SizeOf($query)] = $sql;
		}

		//서비스정보 삭제
		$sql = 'DELETE
				FROM	care_svc_his
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'';

		$query[SizeOf($query)] = $sql;

		//서비스정보
		$his = Explode('?',$_POST['history']);

		if (is_array($his)){
			$seq = 1;

			foreach($his as $idx => $row){
				if ($row){
					parse_str($row,$col);

					$sql = 'INSERT INTO care_svc_his (
							 org_no
							,jumin
							,seq
							,org_nm
							,svc_cd
							,from_dt
							,to_dt
							,person_nm
							,telno) VALUES (
							 \''.$code.'\'
							,\''.$jumin.'\'
							,\''.$seq.'\'
							,\''.$_SESSION['userCenterName'].'\'
							,\''.$col['svcCd'].'\'
							,\''.$col['from'].'\'
							,\''.$col['to'].'\'
							,\''.$col['person'].'\'
							,\''.$col['telno'].'\'
							)';

					$query[SizeOf($query)] = $sql;
					$seq ++;
				}
			}
		}

		if (Is_Array($query)){
			$conn->begin();

			foreach($query as $sql){
				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 #echo $conn->error_msg;
					 #echo $conn->error_query;
					 echo 9;
					 exit;
				}
			}

			$conn->commit();
		}

		echo 'OK_'.$ed->en($jumin);

	}else{
		echo '9';
		exit;
	}

	include_once('../inc/_db_close.php');
?>