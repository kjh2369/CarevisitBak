<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//$debug = false;

	$conn->fetch_type = 'assoc';

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$yymm	= $year.$month;

	$beforeYM = $myF->dateAdd('day', -1, $year.'-'.$month.'-01', 'Ym');

	//기관리스트
	$sql = 'SELECT	DISTINCT
					m00_mcode AS org_no
			FROM	m00center
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode
			WHERE	m00_domain = \''.$gDomain.'\'
			ORDER	BY m00_mcode';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$org[] = $row['org_no'];
	}

	$conn->row_free();

	if (!is_array($org)) exit;

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_close_yn
			WHERE	yymm = \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_close_yn
				SET		acct_dt		= DATE_FORMAT(NOW(),\'%Y%m%d\')
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	yymm		= \''.$yymm.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_close_yn (
				 yymm
				,acct_dt
				,insert_id
				,insert_dt) VALUES (
				 \''.$yymm.'\'
				,DATE_FORMAT(NOW(),\'%Y%m%d\')
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$query[] = $sql;

	Unset($sl);

	foreach($org as $orgNo){
		//대상자수
		$sl[0] .= ($sl[0] ? ' UNION ALL ' : '');
		$sl[0] .= '	SELECT	org_no
					,		COUNT(DISTINCT jumin) AS cnt
					FROM	client_his_svc
					WHERE	org_no = \''.$orgNo.'\'
					AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
					GROUP	BY org_no';


		//등급현황
		$sl[1] .= ($sl[1] ? ' UNION ALL ' : '');
		/*
			$sl[1] .= '	SELECT	org_no
						,		SUM(CASE WHEN level = \'1\' THEN 1 ELSE 0 END) AS lvl1
						,		SUM(CASE WHEN level = \'2\' THEN 1 ELSE 0 END) AS lvl2
						,		SUM(CASE WHEN level = \'3\' THEN 1 ELSE 0 END) AS lvl3
						,		SUM(CASE WHEN level = \'4\' THEN 1 ELSE 0 END) AS lvl4
						,		SUM(CASE WHEN level = \'5\' THEN 1 ELSE 0 END) AS lvl5
						FROM	client_his_lvl
						WHERE	org_no = \''.$orgNo.'\'
						AND		svc_cd = \'0\'
						AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
						AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
						GROUP	BY org_no';
		 */
		$sl[1] .= '	SELECT	org_no
					,		SUM(CASE WHEN lvl = \'1\' THEN 1 ElSE 0 END) AS lvl1
					,		SUM(CASE WHEN lvl = \'2\' THEN 1 ElSE 0 END) AS lvl2
					,		SUM(CASE WHEN lvl = \'3\' THEN 1 ElSE 0 END) AS lvl3
					,		SUM(CASE WHEN lvl = \'4\' THEN 1 ElSE 0 END) AS lvl4
					,		SUM(CASE WHEN lvl = \'5\' THEN 1 ElSE 0 END) AS lvl5
					,		SUM(CASE WHEN lvl = \'9\' THEN 1 ElSE 0 END) AS lvl_other
					FROM	(
							SELECT	a.org_no, IFNULL(b.lvl,\'9\') AS lvl
							FROM	(
									SELECT	DISTINCT org_no, jumin
									FROM	client_his_svc
									WHERE	org_no = \''.$orgNo.'\'
									AND		svc_cd = \'0\'
									AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
									AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
									) AS a
							LEFT	JOIN (
									SELECT	org_no, jumin, RIGHT(GROUP_CONCAT(level),1) AS lvl
									FROM	(
											SELECT	org_no, jumin, level
											FROM	client_his_lvl
											WHERE	org_no = \''.$orgNo.'\'
											AND		svc_cd = \'0\'
											AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
											AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
											ORDER	BY from_dt, to_dt
											) AS a
									GROUP	BY org_no, jumin
									) AS b
									ON		b.org_no = a.org_no
									AND		b.jumin = a.jumin
							) AS a
					GROUP	BY org_no';



		//본인부담구분
		$sl[2] .= ($sl[2] ? ' UNION ALL ' : '');
		/*
			$sl[2] .= '	SELECT	org_no
						,		SUM(CASE WHEN kind = \'3\' THEN 1 ELSE 0 END) AS gbn1
						,		SUM(CASE WHEN kind = \'2\' THEN 1 ELSE 0 END) AS gbn2
						,		SUM(CASE WHEN kind = \'4\' THEN 1 ELSE 0 END) AS gbn3
						,		SUM(CASE WHEN kind = \'1\' THEN 1 ELSE 0 END) AS gbn4
						FROM	client_his_kind
						WHERE	org_no = \''.$orgNo.'\'
						AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
						AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
						GROUP	BY org_no';
		 */
		$sl[2] .= '	SELECT	org_no
					,		SUM(CASE WHEN gbn = \'3\' THEN 1 ELSE 0 END) AS gbn1
					,		SUM(CASE WHEN gbn = \'2\' THEN 1 ELSE 0 END) AS gbn2
					,		SUM(CASE WHEN gbn = \'4\' THEN 1 ELSE 0 END) AS gbn3
					,		SUM(CASE WHEN gbn = \'1\' THEN 1 ELSE 0 END) AS gbn4
					,		SUM(CASE WHEN gbn = \'9\' THEN 1 ELSE 0 END) AS gbn_other
					FROM	(
							SELECT	a.org_no, IFNULL(b.gbn,\'9\') AS gbn
							FROM	(
									SELECT	DISTINCT org_no, jumin
									FROM	client_his_svc
									WHERE	org_no = \''.$orgNo.'\'
									AND		svc_cd = \'0\'
									AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
									AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
									) AS a
							LEFT	JOIN (
									SELECT	org_no, jumin, RIGHT(GROUP_CONCAT(kind),1) AS gbn
									FROM	(
											SELECT	org_no, jumin, kind
											FROM	client_his_kind
											WHERE	org_no = \''.$orgNo.'\'
											AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
											AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
											ORDER	BY from_dt, to_dt
											) AS a
									GROUP	BY org_no, jumin
									) AS b
									ON		b.org_no = a.org_no
									AND		b.jumin = a.jumin
							) AS a
					GROUP	BY org_no';


		//요양분류
		$sl[3] .= ($sl[3] ? ' UNION ALL ' : '');
		$sl[3] .= '	SELECT	org_no
					,		SUM(CASE WHEN mem_cd = \'\' THEN 1 ELSE 0 END) AS n_cnt
					,		SUM(CASE WHEN (mem_cd != \'\') AND (use90_yn = \'N\') AND (partner_yn = \'N\' OR man_age < 65) THEN 1 ELSE 0 END) AS f60_cnt
					,		SUM(CASE WHEN (mem_cd != \'\') AND (use90_yn = \'Y\') OR (partner_yn = \'Y\' AND man_age >= 65) THEN 1 ELSE 0 END) AS f90_cnt
					FROM	(
							SELECT	m03_ccode AS org_no
							,		m03_jumin AS jumin
							,		GROUP_CONCAT(IFNULL(b.cf_mem_cd,\'\')) AS mem_cd
							,		CASE WHEN m03_stat_nogood = \'Y\' THEN \'Y\' ELSE \'N\' END AS use90_yn
							,		CASE WHEN m03_partner = \'Y\' THEN \'Y\' ELSE \'N\' END AS partner_yn
							,		'.$year.' - (CASE WHEN MID(m03_yoyangsa1,7,1) = \'0\' OR MID(m03_yoyangsa1,7,1) = \'9\' THEN 1800
													  WHEN MID(m03_yoyangsa1,7,1) = \'1\' OR MID(m03_yoyangsa1,7,1) = \'2\' THEN 1900
													  WHEN MID(m03_yoyangsa1,7,1) = \'3\' OR MID(m03_yoyangsa1,7,1) = \'4\' THEN 2000 ELSE 0 END
											  +	 CASE WHEN m03_yoyangsa1 != \'\' THEN CAST(LEFT(m03_yoyangsa1,2) AS unsigned) ELSE 0 END) - CASE WHEN CAST(MID(m03_yoyangsa1,3,2) AS unsigned) <= '.IntVal($month).' THEN 1 ELSE 0 END AS man_age
							FROM	m03sugupja
							INNER	JOIN	client_his_svc AS a
									ON		a.org_no = m03_ccode
									AND		a.svc_cd = m03_mkind
									AND		a.jumin = m03_jumin
									AND		LEFT(REPLACE(a.from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
									AND		LEFT(REPLACE(a.to_dt,\'-\',\'\'),6) >= \''.$yymm.'\'
							LEFT	JOIN	client_family AS b
									ON		b.org_no = m03_ccode
									AND		b.cf_jumin = m03_jumin
							WHERE	m03_ccode = \''.$orgNo.'\'
							AND		m03_mkind = \'0\'
							GROUP	BY m03_ccode, m03_jumin
							) AS a
					GROUP	BY org_no';


		//서비스유형
		$sl[4] .= ($sl[4] ? ' UNION ALL ' : '');
		/*
			$sl[4] .= '	SELECT	org_no
						,		SUM(CASE WHEN care_cnt > 0 AND bath_cnt = 0 THEN 1 ELSE 0 END) AS c_cnt
						,		SUM(CASE WHEN care_cnt > 0 AND bath_cnt > 0 THEN 1 ELSE 0 END) AS cb_cnt
						,		SUM(CASE WHEN care_cnt = 0 AND bath_cnt > 0 THEN 1 ELSE 0 END) AS b_cnt
						,		SUM(plan_pay) AS plan_pay
						,		SUM(cont_pay) AS cont_pay
						FROM	(
								SELECT	t01_ccode AS org_no
								,		t01_jumin AS jumin
								,		SUM(CASE WHEN t01_svc_subcode = \'200\' THEN 1 ElSE 0 END) AS care_cnt
								,		SUM(CASE WHEN t01_svc_subcode = \'500\' THEN 1 ELSE 0 END) AS bath_cnt
								,		SUM(t01_suga_tot) AS plan_pay
								,		SUM(t01_conf_suga_value) AS cont_pay
								FROM	t01iljung
								WHERE	t01_ccode	= \''.$orgNo.'\'
								AND		t01_mkind	= \'0\'
								AND		t01_del_yn	= \'N\'
								AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
								GROUP	BY t01_ccode, t01_jumin
								) AS a
						GROUP	BY org_no';
		*/
		$sl[4] .= '	SELECT	org_no
					,		SUM(CASE WHEN care_cnt > 0 AND bath_cnt = 0 THEN 1 ELSE 0 END) AS c_cnt
					,		SUM(CASE WHEN care_cnt > 0 AND bath_cnt > 0 THEN 1 ELSE 0 END) AS cb_cnt
					,		SUM(CASE WHEN care_cnt = 0 AND bath_cnt > 0 THEN 1 ELSE 0 END) AS b_cnt
					,		SUM(not_cnt) AS not_cnt
					FROM	(
							SELECT	a.org_no
							,		IFNULL(b.care_cnt,0) AS care_cnt
							,		IFNULL(b.bath_cnt,0) AS bath_cnt
							,		CASE WHEN IFNULL(b.jumin,\'\') = \'\' THEN 1 ELSE 0 END AS not_cnt
							FROM	(
									SELECT	DISTINCT org_no, jumin
									FROM	client_his_svc
									WHERE	org_no = \''.$orgNo.'\'
									AND		svc_cd = \'0\'
									AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
									AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$yymm.'\'
									) AS a
							LEFT	JOIN (
									SELECT	t01_ccode AS org_no
									,		t01_jumin AS jumin
									,		SUM(CASE WHEN t01_svc_subcode = \'200\' THEN 1 ElSE 0 END) AS care_cnt
									,		SUM(CASE WHEN t01_svc_subcode = \'500\' THEN 1 ELSE 0 END) AS bath_cnt
									,		SUM(t01_suga_tot) AS plan_pay
									,		SUM(t01_conf_suga_value) AS conf_pay
									FROM	t01iljung
									WHERE	t01_ccode	= \''.$orgNo.'\'
									AND		t01_mkind	= \'0\'
									AND		t01_del_yn	= \'N\'
									AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
									GROUP	BY t01_ccode, t01_jumin
									) AS b
									ON		b.org_no = a.org_no
									AND		b.jumin = a.jumin
							) AS a
					GROUP	BY org_no';


		//직원수, 정규직, 60시간이상, 60시간미만 수
		$sl[5] .= ($sl[5] ? ' UNION ALL ' : '');
		$sl[5] .= '	SELECT	a.org_no
					,		COUNT(DISTINCT a.jumin) AS mem_cnt
					,		SUM(CASE WHEN a.employ_type = \'1\' OR a.employ_type = \'2\' THEN 1 ElSE 0 END) AS work_normal_cnt
					,		SUM(CASE WHEN a.employ_type = \'3\' AND b.salary_jumin IS NOT NULL THEN 1 ElSE 0 END) AS work_60up_cnt
					,		SUM(CASE WHEN a.employ_type = \'4\' AND b.salary_jumin IS NOT NULL THEN 1 ElSE 0 END) AS work_60down_cnt
					FROM	mem_his AS a
					LEFT	JOIN	salary_basic AS b
							ON		b.org_no		= a.org_no
							AND		b.salary_jumin	= a.jumin
							AND		b.salary_yymm	= \''.$yymm.'\'
					WHERE	a.org_no = \''.$orgNo.'\'
					AND		LEFT(REPLACE(a.join_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
					AND		LEFT(REPLACE(IFNULL(a.quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$yymm.'\'
					GROUP	BY a.org_no';


		//근무년수
		$sl[6] .= ($sl[6] ? ' UNION ALL ' : '');
		$sl[6] .= 'SELECT	org_no
					,		SUM(CASE WHEN year_cnt < 1 THEN 1 ElSE 0 END) AS down_cnt
					,		SUM(CASE WHEN year_cnt > 0 THEN 1 ELSE 0 END) AS up_cnt
					FROM	(
							SELECT	a.org_no
							,		TIMESTAMPDIFF(YEAR, a.join_dt, IFNULL(a.quit_dt,DATE_FORMAT(NOW(),\'%Y-%m-%d\'))) AS year_cnt
							FROM	mem_his AS a
							LEFT	JOIN	salary_basic AS b
									ON		b.org_no		= a.org_no
									AND		b.salary_jumin	= a.jumin
									AND		b.salary_yymm	= \''.$yymm.'\'
							WHERE	a.org_no = \''.$orgNo.'\'
							AND		a.employ_type != \'1\'
							AND		a.employ_type != \'2\'
							AND		LEFT(REPLACE(a.join_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							AND		LEFT(REPLACE(IFNULL(a.quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$yymm.'\'
							AND		CASE WHEN a.employ_type = \'1\' THEN \'Y\'
										 WHEN a.employ_type = \'2\' THEN \'Y\'
										 WHEN a.employ_type = \'3\' AND b.salary_jumin IS NOT NULL THEN \'Y\'
										 WHEN a.employ_type = \'4\' AND b.salary_jumin IS NOT NULL THEN \'Y\' ELSE \'N\' END = \'Y\'
							) AS a
					GROUP	BY org_no';


		//퇴직충당 임금
		$sl[7].= ($sl[7] ? ' UNION ALL ' : '');
		/*
			$sl[7].= '	SELECT	org_no
						,		SUM(saved_cnt) AS saved_cnt
						,		SUM(saved_amt) AS saved_amt
						,		SUM(not_cnt) AS not_cnt
						,		SUM(not_amt) AS not_amt
						,		SUM(saved_money) AS saved_money
						FROM	(
								SELECT	DISTINCT
										a.org_no
								,		a.jumin
								,		CASE WHEN a.saved_money > 0 THEN 1 ELSE 0 END AS saved_cnt
								,		CASE WHEN a.saved_money > 0 THEN a.work_pay ELSE 0 END AS saved_amt
								,		CASE WHEN a.saved_money > 0 THEN 0 ELSE 1 END AS not_cnt
								,		CASE WHEN a.saved_money > 0 THEN 0 ELSE a.work_pay END AS not_amt
								,		a.saved_money
								FROM	salary_retirement AS a
								INNER	JOIN	mem_his AS b
										ON		b.org_no= a.org_no
										AND		b.jumin	= a.jumin
										AND		LEFT(REPLACE(b.join_dt,\'-\',\'\'),6) <= a.yymm
										AND		LEFT(REPLACE(IFNULL(b.quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= a.yymm
								WHERE	a.org_no= \''.$orgNo.'\'
								AND		a.yymm	= \''.$yymm.'\'
								) AS a
						GROUP	BY org_no';
		 */
		$sl[7].= '	SELECT	a.org_no
					,		SUM(a.saved_cnt) AS saved_cnt
					,		SUM(a.saved_amt) AS saved_amt
					,		SUM(a.not_cnt) AS not_cnt
					,		SUM(a.not_amt) AS not_amt
					,		SUM(CASE WHEN b.employ_type = \'1\' OR b.employ_type = \'2\' OR b.employ_type = \'3\' THEN a.saved_cnt ElSE 0 END) AS re_cnt_60up
					,		SUM(CASE WHEN b.employ_type = \'4\' THEN a.saved_cnt ELSE 0 END) AS re_cnt_60down
					,		SUM(CASE WHEN b.employ_type = \'1\' OR b.employ_type = \'2\' OR b.employ_type = \'3\' THEN a.saved_money ElSE 0 END) AS re_amt_60up
					,		SUM(CASE WHEN b.employ_type = \'4\' THEN a.saved_money ELSE 0 END) AS re_amt_60down
					FROM	(
							SELECT	DISTINCT
									a.org_no
							,		a.jumin
							,		CASE WHEN a.saved_money > 0 THEN 1 ELSE 0 END AS saved_cnt
							,		CASE WHEN a.saved_money > 0 THEN a.work_pay ELSE 0 END AS saved_amt
							,		CASE WHEN a.saved_money > 0 THEN 0 ELSE 1 END AS not_cnt
							,		CASE WHEN a.saved_money > 0 THEN 0 ELSE a.work_pay END AS not_amt
							,		a.saved_money
							FROM	salary_retirement AS a
							INNER	JOIN	mem_his AS b
									ON		b.org_no= a.org_no
									AND		b.jumin	= a.jumin
									AND		LEFT(REPLACE(b.join_dt,\'-\',\'\'),6) <= a.yymm
									AND		LEFT(REPLACE(IFNULL(b.quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= a.yymm
							WHERE	a.org_no= \''.$orgNo.'\'
							AND		a.yymm	= \''.$yymm.'\'
							) AS a
					INNER	JOIN (
							SELECT	org_no
							,		jumin
							,		MIN(employ_type) AS employ_type
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND	LEFT(REPLACE(join_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							AND	LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$yymm.'\'
							GROUP  BY org_no, jumin
							) AS b
							ON		b.jumin = a.jumin
					GROUP	BY org_no';

		//퇴직충당누계
		$sl[8] .= ($sl[8] ? ' UNION ALL ' : '');
		$sl[8] .= '	SELECT	org_no
					,		COUNT(DISTINCT jumin) AS cnt
					,		SUM(saved_money) AS amt
					FROM	salary_retirement
					WHERE	org_no		 = \''.$orgNo.'\'
					AND		yymm		<= \''.$yymm.'\'
					AND		saved_money > 0
					GROUP	BY org_no';


		//이월 직원인원
		$sl[9] .= ($sl[9] ? ' UNION ALL ' : '');
		$sl[9] .= '	SELECT	org_no
					,		COUNT(DISTINCT jumin) AS cnt
					FROM	mem_his
					WHERE	org_no = \''.$orgNo.'\'
					AND		employ_type != \'1\'
					AND		employ_type != \'2\'
					AND		LEFT(REPLACE(join_dt,\'-\',\'\'),6) <= \''.$beforeYM.'\'
					AND		LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$beforeYM.'\'
					AND		LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) != \''.$beforeYM.'\'
					GROUP	BY org_no';


		//당월 직원 입퇴사
		$sl[10] .= ($sl[10] ? ' UNION ALL ' : '');
		$sl[10] .= 'SELECT	org_no
					,		SUM(CASE WHEN join_day >= 1 AND join_day <= 7 THEN 1 ELSE 0 END) AS week1_in
					,		SUM(CASE WHEN join_day >= 8 AND join_day <= 14 THEN 1 ELSE 0 END) AS week2_in
					,		SUM(CASE WHEN join_day >= 15 AND join_day <= 21 THEN 1 ElSE 0 END) AS week3_in
					,		SUM(CASE WHEN join_day >= 22 THEN 1 ElSE 0 END) AS week4_in
					,		SUM(CASE WHEN quit_day >= 1 AND quit_day <= 7 THEN 1 ELSE 0 END) AS week1_out
					,		SUM(CASE WHEN quit_day >= 8 AND quit_day <= 14 THEN 1 ELSE 0 END) AS week2_out
					,		SUM(CASE WHEN quit_day >= 15 AND quit_day <= 21 THEN 1 ElSE 0 END) AS week3_out
					,		SUM(CASE WHEN quit_day >= 22 THEN 1 ElSE 0 END) AS week4_out
					FROM	(
							SELECT	org_no
							,		CAST(DATE_FORMAT(join_dt,\'%d\') AS unsigned) AS join_day
							,		0 AS quit_day
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		LEFT(REPLACE(join_dt,\'-\',\'\'),6) = \''.$yymm.'\'
							UNION	ALL
							SELECT	org_no
							,		0
							,		CAST(DATE_FORMAT(quit_dt,\'%d\') AS unsigned)
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) = \''.$yymm.'\'
							) AS a
					GROUP	BY org_no';


		//이월 대상자수
		$sl[11] .= ($sl[11] ? ' UNION ALL ' : '');
		/*
			$sl[11] .= 'SELECT	org_no
						,		COUNT(DISTINCT jumin) AS cnt
						FROM	client_his_svc
						WHERE	org_no = \''.$orgNo.'\'
						AND		LEFT(REPlACE(from_dt,\'-\',\'\'),6) <= \''.$beforeYM.'\'
						AND		LEFT(REPlACE(to_dt,\'-\',\'\'),6) >= \''.$beforeYM.'\'
						AND		LEFT(REPlACE(to_dt,\'-\',\'\'),6) != \''.$beforeYM.'\'';
		 */
		$sl[11] .= 'SELECT	org_no
					,		jumin
					,		REPlACE(from_dt,\'-\',\'\') AS from_dt
					,		REPlACE(to_dt,\'-\',\'\') AS to_dt
					,		svc_stat
					,		LEFT(REPlACE(from_dt,\'-\',\'\'),6) AS from_ym
					,		LEFT(REPlACE(to_dt,\'-\',\'\'),6) AS to_ym
					FROM	client_his_svc
					WHERE	org_no = \''.$orgNo.'\'';


		//당월 대상자 가입해지
		$sl[12] .= ($sl[12] ? ' UNION ALL ' : '');

		/*
			$sl[12] .= 'SELECT	org_no
						,		SUM(CASE WHEN from_day >= 1 AND from_day <= 7 THEN 1 ELSE 0 END) AS week1_in
						,		SUM(CASE WHEN from_day >= 8 AND from_day <= 14 THEN 1 ELSE 0 END) AS week2_in
						,		SUM(CASE WHEN from_day >= 15 AND from_day <= 21 THEN 1 ElSE 0 END) AS week3_in
						,		SUM(CASE WHEN from_day >= 22 THEN 1 ElSE 0 END) AS week4_in
						,		SUM(CASE WHEN to_day >= 1 AND to_day <= 7 THEN 1 ELSE 0 END) AS week1_out
						,		SUM(CASE WHEN to_day >= 8 AND to_day <= 14 THEN 1 ELSE 0 END) AS week2_out
						,		SUM(CASE WHEN to_day >= 15 AND to_day <= 21 THEN 1 ElSE 0 END) AS week3_out
						,		SUM(CASE WHEN to_day >= 22 THEN 1 ElSE 0 END) AS week4_out
						FROM	(
								SELECT	org_no
								,		CAST(DATE_FORMAT(from_dt,\'%d\') AS unsigned) AS from_day
								,		0 AS to_day
								FROM	client_his_svc
								WHERE	org_no = \''.$orgNo.'\'
								AND		LEFT(REPlACE(from_dt,\'-\',\'\'),6) = \''.$yymm.'\'
								UNION	ALL
								SELECT	org_no
								,		0 AS from_day
								,		CAST(DATE_FORMAT(to_dt,\'%d\') AS unsigned) AS to_day
								FROM	client_his_svc
								WHERE	org_no	= \''.$orgNo.'\'
								AND		svc_stat= \'9\'
								AND		LEFT(REPlACE(to_dt,\'-\',\'\'),6) = \''.$yymm.'\'
								) AS a
						GROUP	BY org_no';
		 */
		$sl[12] .= 'SELECT	org_no, jumin, svc_stat, REPLACE(from_dt,\'-\',\'\') AS from_dt, REPLACE(to_dt,\'-\',\'\') AS to_dt
					FROM	client_his_svc
					WHERE	org_no = \''.$orgNo.'\'';


		//전월 매출
		$sl[13] .= ($sl[13] ? ' UNION ALL ' : '');
		$sl[13] .= 'SELECT	t01_ccode AS org_no
					,		SUM(t01_suga_tot) AS plan_amt
					,		SUM(t01_conf_suga_value) AS conf_amt
					FROM	t01iljung
					WHERE	t01_ccode	= \''.$orgNo.'\'
					AND		t01_del_yn	= \'N\'
					AND		LEFT(t01_sugup_date,6) = \''.$beforeYM.'\'
					GROUP	BY t01_ccode';


		//당월 매출
		$sl[14] .= ($sl[14] ? ' UNION ALL ' : '');
		$sl[14] .= 'SELECT	org_no
					,		SUM(CASE WHEN sales_day >= 1 AND sales_day <= 7 THEN plan_amt ElSE 0 END) AS plan_amt_w1
					,		SUM(CASE WHEN sales_day >= 8 AND sales_day <= 14 THEN plan_amt ElSE 0 END) AS plan_amt_w2
					,		SUM(CASE WHEN sales_day >= 15 AND sales_day <= 21 THEN plan_amt ElSE 0 END) AS plan_amt_w3
					,		SUM(CASE WHEN sales_day >= 22 THEN plan_amt ElSE 0 END) AS plan_amt_w4
					,		SUM(CASE WHEN sales_day >= 1 AND sales_day <= 7 THEN conf_amt ElSE 0 END) AS conf_amt_w1
					,		SUM(CASE WHEN sales_day >= 8 AND sales_day <= 14 THEN conf_amt ElSE 0 END) AS conf_amt_w2
					,		SUM(CASE WHEN sales_day >= 15 AND sales_day <= 21 THEN conf_amt ElSE 0 END) AS conf_amt_w3
					,		SUM(CASE WHEN sales_day >= 22 THEN conf_amt ElSE 0 END) AS conf_amt_w4
					FROM	(
							SELECT	t01_ccode AS org_no
							,		CAST(MID(t01_sugup_date,7) AS unsigned) AS sales_day
							,		t01_suga_tot AS plan_amt
							,		t01_conf_suga_value AS conf_amt
							FROM	t01iljung
							WHERE	t01_ccode	= \''.$orgNo.'\'
							AND		t01_del_yn	= \'N\'
							AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
							) AS a
					GROUP  BY org_no';


		//전월 직원수
		$sl[15] .= ($sl[15] ? ' UNION ALL ' : '');
		$sl[15] .= 'SELECT	org_no
					,		COUNT(DISTINCT jumin) AS cnt
					FROM	mem_his
					WHERE	org_no = \''.$orgNo.'\'
					AND		employ_type != \'1\'
					AND		employ_type != \'2\'
					AND		LEFT(REPLACE(join_dt,\'-\',\'\'),6) <= \''.$beforeYM.'\'
					AND		LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$beforeYM.'\'
					AND		LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) != \''.$beforeYM.'\'
					GROUP	BY org_no';


		//당월 직원 정보
		$sl[16] .= ($sl[16] ? ' UNION ALL ' : '');
		$sl[16] .= 'SELECT	org_no
					,		SUM(week1_join_cnt) AS week1_join_cnt
					,		SUM(week2_join_cnt) AS week2_join_cnt
					,		SUM(week3_join_cnt) AS week3_join_cnt
					,		SUM(week4_join_cnt) AS week4_join_cnt
					,		SUM(week1_quit_cnt) AS week1_quit_cnt
					,		SUM(week2_quit_cnt) AS week2_quit_cnt
					,		SUM(week3_quit_cnt) AS week3_quit_cnt
					,		SUM(week4_quit_cnt) AS week4_quit_cnt
					,		SUM(week_mem_cnt) AS week_mem_cnt
					FROM	(
							SELECT	org_no
							,		COUNT(DISTINCT jumin) AS week_mem_cnt
							,		0 AS week1_join_cnt
							,		0 AS week2_join_cnt
							,		0 AS week3_join_cnt
							,		0 AS week4_join_cnt
							,		0 AS week1_quit_cnt
							,		0 AS week2_quit_cnt
							,		0 AS week3_quit_cnt
							,		0 AS week4_quit_cnt
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		LEFT(REPLACE(join_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							AND		LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$yymm.'\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, COUNT(DISTINCT jumin), 0, 0, 0, 0, 0, 0, 0
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(join_dt,\'-\',\'\') BETWEEN \''.$yymm.'01\' AND \''.$yymm.'07\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0 , 0, COUNT(DISTINCT jumin), 0, 0, 0, 0, 0, 0
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(join_dt,\'-\',\'\') BETWEEN \''.$yymm.'08\' AND \''.$yymm.'14\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, COUNT(DISTINCT jumin), 0, 0, 0, 0, 0
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(join_dt,\'-\',\'\') BETWEEN \''.$yymm.'15\' AND \''.$yymm.'21\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, COUNT(DISTINCT jumin), 0, 0, 0, 0
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(join_dt,\'-\',\'\') BETWEEN \''.$yymm.'22\' AND \''.$yymm.'31\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, COUNT(DISTINCT jumin), 0, 0 ,0
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\') BETWEEN \''.$yymm.'01\' AND \''.$yymm.'07\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, 0, COUNT(DISTINCT jumin), 0 ,0
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\') BETWEEN \''.$yymm.'08\' AND \''.$yymm.'14\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, 0, 0, COUNT(DISTINCT jumin) ,0
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\') BETWEEN \''.$yymm.'15\' AND \''.$yymm.'21\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, 0, 0 ,0, COUNT(DISTINCT jumin)
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND		employ_type != \'1\'
							AND		employ_type != \'2\'
							AND		REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\') BETWEEN \''.$yymm.'22\' AND \''.$yymm.'31\'
							GROUP	BY org_no
							) AS a
					GROUP	BY org_no';


		//전월 대상자수
		$sl[17] .= ($sl[17] ? ' UNION ALL ' : '');
		$sl[17] .= 'SELECT	org_no
					,		COUNT(DISTINCT jumin) AS cnt
					FROM	client_his_svc
					WHERE	org_no = \''.$orgNo.'\'
					AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$beforeYM.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) >= \''.$beforeYM.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) != \''.$beforeYM.'\'
					GROUP	BY org_no';


		//당월 대상자 정보
		$sl[18] .= ($sl[18] ? ' UNION ALL ' : '');
		$sl[18] .= 'SELECT	org_no
					,		SUM(tg_cnt) AS tg_cnt
					,		SUM(week1_in_cnt) AS week1_in_cnt
					,		SUM(week2_in_cnt) AS week2_in_cnt
					,		SUM(week3_in_cnt) AS week3_in_cnt
					,		SUM(week4_in_cnt) AS week4_in_cnt
					,		SUM(week1_out_cnt) AS week1_out_cnt
					,		SUM(week2_out_cnt) AS week2_out_cnt
					,		SUM(week3_out_cnt) AS week3_out_cnt
					,		SUM(week4_out_cnt) AS week4_out_cnt
					FROM	(
							SELECT	org_no
							,		COUNT(DISTINCT jumin) AS tg_cnt
							,		0 AS week1_in_cnt
							,		0 AS week2_in_cnt
							,		0 AS week3_in_cnt
							,		0 AS week4_in_cnt
							,		0 AS week1_out_cnt
							,		0 AS week2_out_cnt
							,		0 AS week3_out_cnt
							,		0 AS week4_out_cnt
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, COUNT(DISTINCT jumin), 0, 0, 0, 0, 0, 0, 0
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(from_dt,\'-\',\'\') BETWEEN \''.$yymm.'01\' AND \''.$yymm.'07\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, COUNT(DISTINCT jumin), 0, 0, 0, 0, 0, 0
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(from_dt,\'-\',\'\') BETWEEN \''.$yymm.'08\' AND \''.$yymm.'14\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, COUNT(DISTINCT jumin), 0, 0, 0, 0, 0
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(from_dt,\'-\',\'\') BETWEEN \''.$yymm.'15\' AND \''.$yymm.'21\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, COUNT(DISTINCT jumin), 0, 0, 0, 0
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(from_dt,\'-\',\'\') BETWEEN \''.$yymm.'22\' AND \''.$yymm.'31\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, COUNT(DISTINCT jumin), 0, 0, 0
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(to_dt,\'-\',\'\') BETWEEN \''.$yymm.'01\' AND \''.$yymm.'07\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, 0, COUNT(DISTINCT jumin), 0, 0
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(to_dt,\'-\',\'\') BETWEEN \''.$yymm.'08\' AND \''.$yymm.'14\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, 0, 0, COUNT(DISTINCT jumin), 0
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(to_dt,\'-\',\'\') BETWEEN \''.$yymm.'15\' AND \''.$yymm.'21\'
							GROUP	BY org_no
							UNION	ALL
							SELECT	org_no, 0, 0, 0, 0, 0, 0, 0, 0, COUNT(DISTINCT jumin)
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		REPLACE(to_dt,\'-\',\'\') BETWEEN \''.$yymm.'22\' AND \''.$yymm.'31\'
							GROUP	BY org_no
							) AS a
					GROUP	BY org_no';

		//4대보험 회사분
		$sl[19] .= ($sl[19] ? ' UNION ALL ' : '');

		/*
			$sl[19] .= 'SELECT	org_no
						,		SUM(pension_amt + health_amt + care_amt + employ_amt + sanje_amt) AS amt
						FROM	salary_center_amt
						WHERE	org_no		= \''.$orgNo.'\'
						AND		salary_yymm = \''.$yymm.'\'
						GROUP	BY org_no';
		 */
		$sl[19] .= 'SELECT	a.org_no
					,		SUM(CASE WHEN a.employ_type = \'1\' OR a.employ_type = \'2\' OR a.employ_type = \'3\' THEN b.amt ElSE 0 END) AS insu_com_60up
					,		SUM(CASE WHEN a.employ_type = \'4\' THEN b.amt ELSE 0 END) AS insu_com_60down
					FROM	(
							SELECT	org_no
							,		jumin
							,		MIN(employ_type) AS employ_type
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND	LEFT(REPLACE(join_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							AND	LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$yymm.'\'
							GROUP  BY org_no, jumin
							) AS a
					INNER	JOIN (
							SELECT	org_no
							,		salary_jumin AS jumin
							,		pension_amt + health_amt + care_amt + employ_amt + sanje_amt AS amt
							FROM	salary_center_amt
							WHERE	org_no		= \''.$orgNo.'\'
							AND		salary_yymm = \''.$yymm.'\'
							) AS b
							ON		b.jumin = a.jumin
					GROUP	BY a.org_no
					';

		//본인부담금 계산 내역
		$sl[20] .= ($sl[20] ? ' UNION ALL ' : '');
		$sl[20] .= 'SELECT	t13_ccode AS org_no
					,		SUM(t13_chung_amt4) AS longterm_amt
					,		SUM(t13_bonbu_tot4) AS expense_amt
					FROM	t13sugupja
					WHERE	t13_ccode	= \''.$orgNo.'\'
					AND		t13_mkind	= \'0\'
					AND		t13_pay_date= \''.$yymm.'\'
					AND		t13_type	= \'2\'
					GROUP	BY t13_ccode';

		//본인부담금 계산 전월 내역
		$sl[21] .= ($sl[21] ? ' UNION ALL ' : '');
		$sl[21] .= 'SELECT	t13_ccode AS org_no
					,		SUM(t13_chung_amt4) AS longterm_amt
					,		SUM(t13_bonbu_tot4) AS expense_amt
					FROM	t13sugupja
					WHERE	t13_ccode	= \''.$orgNo.'\'
					AND		t13_mkind	= \'0\'
					AND		t13_pay_date= \''.$beforeYM.'\'
					AND		t13_type	= \'2\'
					GROUP	BY t13_ccode';

		//직원 근무형태별 급여
		$sl[22] .= ($sl[22] ? ' UNION ALL ' : '');
		$sl[22] .= 'SELECT	a.org_no
					,		SUM(CASE WHEN a.employ_type = \'1\' OR a.employ_type = \'2\' OR a.employ_type = \'3\' THEN 1 ElSE 0 END) AS cnt_60up
					,		SUM(CASE WHEN a.employ_type = \'4\' THEN 1 ELSE 0 END) AS cnt_60down
					,		SUM(CASE WHEN a.employ_type = \'1\' OR a.employ_type = \'2\' OR a.employ_type = \'3\' THEN b.work_pay ELSE 0 END) AS pay_60up
					,		SUM(CASE WHEN a.employ_type = \'4\' THEN b.work_pay ELSE 0 END) AS pay_60down
					FROM	(
							SELECT	org_no
							,		jumin
							,		MIN(employ_type) AS employ_type
							FROM	mem_his
							WHERE	org_no = \''.$orgNo.'\'
							AND	LEFT(REPLACE(join_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
							AND	LEFT(REPLACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$yymm.'\'
							GROUP  BY org_no, jumin
							) AS a
					INNER	JOIN (
							SELECT	jumin
							,		work_time
							,		work_pay
							FROM	salary_retirement
							WHERE	org_no	= \''.$orgNo.'\'
							AND		yymm	= \''.$yymm.'\'
							) AS b
							ON b.jumin = a.jumin
					GROUP	BY a.org_no';

		//대상자 해지사유별
		$sl[23] .= ($sl[23] ? ' UNION ALL ' : '');
		$sl[23] .= 'SELECT	org_no
					,		SUM(CASE WHEN svc_reason = \'01\' THEN 1 ElSE 0 END) AS rsn_01
					,		SUM(CASE WHEN svc_reason = \'02\' THEN 1 ElSE 0 END) AS rsn_02
					,		SUM(CASE WHEN svc_reason = \'03\' THEN 1 ElSE 0 END) AS rsn_03
					,		SUM(CASE WHEN svc_reason = \'04\' THEN 1 ElSE 0 END) AS rsn_04
					,		SUM(CASE WHEN svc_reason = \'05\' THEN 1 ElSE 0 END) AS rsn_05
					,		SUM(CASE WHEN svc_reason = \'06\' THEN 1 ElSE 0 END) AS rsn_06
					,		SUM(CASE WHEN svc_reason = \'07\' THEN 1 ElSE 0 END) AS rsn_07
					,		SUM(CASE WHEN svc_reason = \'08\' THEN 1 ElSE 0 END) AS rsn_08
					,		SUM(CASE WHEN svc_reason = \'09\' THEN 1 ElSE 0 END) AS rsn_09
					,		SUM(CASE WHEN svc_reason = \'10\' THEN 1 ElSE 0 END) AS rsn_10
					,		SUM(CASE WHEN svc_reason = \'11\' THEN 1 ElSE 0 END) AS rsn_11
					,		SUM(CASE WHEN svc_reason = \'12\' THEN 1 ElSE 0 END) AS rsn_12
					,		SUM(CASE WHEN svc_reason = \'13\' THEN 1 ElSE 0 END) AS rsn_13
					,		SUM(CASE WHEN svc_reason = \'14\' THEN 1 ElSE 0 END) AS rsn_14
					,		SUM(CASE WHEN svc_reason = \'99\' THEN 1 ElSE 0 END) AS rsn_99
					FROM	client_his_svc
					WHERE	org_no	= \''.$orgNo.'\'
					AND		svc_cd	= \'0\'
					AND		svc_stat= \'9\'
					AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$yymm.'\'
					AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6) = \''.$yymm.'\'
					GROUP	BY org_no';

		//직원 마감인원
		$sl[24] .= ($sl[24] ? ' UNION ALL ' : '');
		$sl[24] .= 'SELECT	org_no, COUNT(salary_jumin) AS cnt
					FROM	salary_basic
					WHERE	org_no		= \''.$orgNo.'\'
					AND		salary_yymm = \''.$yymm.'\'';

		//고객 맘감인원
		$sl[25] .= ($sl[25] ? ' UNION ALL ' : '');
		$sl[25] .= 'SELECT	t01_ccode AS org_no, COUNT(DISTINCT t01_jumin) AS cnt
					FROM	t01iljung
					WHERE	t01_ccode	= \''.$orgNo.'\'
					AND		t01_mkind	= \'0\'
					AND		t01_del_yn	= \'N\'
					AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'';

		//개인간병인원
		$sl[26] .= ($sl[26] ? ' UNION ALL ' : '');
		$sl[26] .= 'SELECT	org_no, psn_cnt
					FROM	ie_bm_psnurse
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		from_dt <= \''.$yymm.'\'
					AND		to_dt	>= \''.$yymm.'\'';

		//계약유형
		$sl[27] .= ($sl[27] ? ' UNION ALL ' : '');
		$sl[27] .= 'SELECT	a.org_no
					,		SUM(CASE WHEN b.cont_type = \'01\' THEN 1 ElSE 0 END) AS cnt_01 /*전화,인터넷*/
					,		SUM(CASE WHEN b.cont_type = \'02\' THEN 1 ElSE 0 END) AS cnt_02 /*직접발굴*/
					,		SUM(CASE WHEN b.cont_type = \'03\' THEN 1 ElSE 0 END) AS cnt_03 /*지인소개*/
					,		SUM(CASE WHEN b.cont_type = \'04\' THEN 1 ElSE 0 END) AS cnt_04 /*근무자를 통한소개*/
					,		SUM(CASE WHEN b.cont_type = \'05\' THEN 1 ElSE 0 END) AS cnt_05 /*공단자료*/
					,		SUM(CASE WHEN b.cont_type = \'06\' THEN 1 ElSE 0 END) AS cnt_06 /*외부인수*/
					,		SUM(CASE WHEN b.cont_type = \'07\' THEN 1 ElSE 0 END) AS cnt_07 /*간병연계*/
					,		SUM(CASE WHEN b.cont_type = \'08\' THEN 1 ElSE 0 END) AS cnt_08 /*지점연계*/
					FROM	(
							SELECT	org_no, jumin, MIN(REPLACE(from_dt,\'-\',\'\')) AS from_dt
							FROM	client_his_svc
							WHERE	org_no = \''.$orgNo.'\'
							AND		svc_cd = \'0\'
							GROUP	BY org_no, jumin
							) AS a
					INNER	JOIN	client_option AS b
							ON		b.org_no = a.org_no
							AND		b.jumin	 = a.jumin
					WHERE	LEFT(a.from_dt,6) = \''.$yymm.'\'';
	}


	Unset($data);


	//$sl[0] = 대상자 수 org_no, cnt
	$conn->query($sl[0]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['cnt'] = $row['cnt'];
	}

	$conn->row_free();


	//$sl[1] = 등급현황			org_no, lvl1, lvl2, lvl3, lvl4, lvl5
	$conn->query($sl[1]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['lv1_cnt'] = $row['lvl1'];
		$data[$orgNo]['C']['lv2_cnt'] = $row['lvl2'];
		$data[$orgNo]['C']['lv3_cnt'] = $row['lvl3'];
		$data[$orgNo]['C']['lv4_cnt'] = $row['lvl4'];
		$data[$orgNo]['C']['lv5_cnt'] = $row['lvl5'];
		$data[$orgNo]['C']['lv_other']= $row['lvl_other'];
	}

	$conn->row_free();


	//$sl[2] = 본인부담구분		org_no gbn1 gbn2 gbn3 gbn4
	$conn->query($sl[2]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['gbn1_cnt'] = $row['gbn1'];
		$data[$orgNo]['C']['gbn2_cnt'] = $row['gbn2'];
		$data[$orgNo]['C']['gbn3_cnt'] = $row['gbn3'];
		$data[$orgNo]['C']['gbn4_cnt'] = $row['gbn4'];
		$data[$orgNo]['C']['gbn_other']= $row['gbn_other'];
	}

	$conn->row_free();


	//$sl[3] = 요양분류			org_no n_cnt f60_cnt f90_cnt
	$conn->query($sl[3]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['care_nml'] = $row['n_cnt'];
		$data[$orgNo]['C']['care_f60'] = $row['f60_cnt'];
		$data[$orgNo]['C']['care_f90'] = $row['f90_cnt'];
	}

	$conn->row_free();


	//$sl[4] = 서비스유형		org_no c_cnt cb_cnt b_cnt
	$conn->query($sl[4]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['care_cnt']		= $row['c_cnt'];
		$data[$orgNo]['C']['care_bath_cnt'] = $row['cb_cnt'];
		$data[$orgNo]['C']['bath_cnt']		= $row['b_cnt'];
		$data[$orgNo]['C']['care_other']	= $row['not_cnt'];

		$data[$orgNo]['C']['plan_pay'] = $row['plan_pay'];
		$data[$orgNo]['C']['conf_pay'] = $row['cont_pay'];
	}

	$conn->row_free();


	//$sl[5] = 직원수, 정규직, 60시간이상, 60시간미만 수 org_no mem_cnt work_normal_cnt work_60up_cnt work_60down_cnt
	$conn->query($sl[5]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		//$data[$orgNo]['M']['cnt']		= $row['mem_cnt']; //직원수
		$data[$orgNo]['M']['cnt']		= $row['work_normal_cnt'] + $row['work_60up_cnt'] + $row['work_60down_cnt']; //직원수
		$data[$orgNo]['M']['wrk_nml']	= $row['work_normal_cnt']; //정규직
		$data[$orgNo]['M']['wrk_60up']	= $row['work_60up_cnt']; //60시간이상
		$data[$orgNo]['M']['wrk_60down']= $row['work_60down_cnt']; //60시간미만
	}

	$conn->row_free();


	//$sl[6] = 근무년수			org_no down_cnt up_cnt
	$conn->query($sl[6]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['M']['year_down']= $row['down_cnt']; //1년미만
		$data[$orgNo]['M']['year_up']= $row['up_cnt']; //1년이상
	}

	$conn->row_free();


	//$sl[7] = 퇴직충당 임금	org_no saved_cnt saved_amt not_cnt not_amt saved_money
	$conn->query($sl[7]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		//퇴직충당금미발생
		$data[$orgNo]['M']['re_n_cnt']= $row['not_cnt']; //인원수
		$data[$orgNo]['M']['re_n_amt']= $row['not_amt']; //임금

		//퇴직충당금발생
		$data[$orgNo]['M']['re_y_cnt']= $row['saved_cnt']; //인원수
		$data[$orgNo]['M']['re_y_amt']= $row['saved_amt']; //임금

		//인원수
		$data[$orgNo]['M']['re_cnt'] = $row['saved_cnt'];
		$data[$orgNo]['M']['re_cnt_60up'] = $row['re_cnt_60up'];
		$data[$orgNo]['M']['re_cnt_60down'] = $row['re_cnt_60down'];

		//퇴직충당금
		$data[$orgNo]['M']['re_amt'] = $row['re_amt_60up'] + $row['re_amt_60down'];
		$data[$orgNo]['M']['re_amt_60up'] = $row['re_amt_60up'];
		$data[$orgNo]['M']['re_amt_60down'] = $row['re_amt_60down'];
	}

	$conn->row_free();


	//$sl[8] = 퇴직충당누계		org_no cnt amt
	$conn->query($sl[8]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['M']['re_saved_cnt']= $row['cnt']; //인원수
		$data[$orgNo]['M']['re_saved_amt']= $row['amt']; //누적퇴직창당금
	}

	$conn->row_free();


	//$sl[9] = 이월 직원인원	org_no, cnt
	$conn->query($sl[9]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['M']['ahead_cnt']= $row['cnt']; //이월 직원수
	}

	$conn->row_free();


	//$sl[10]= 당월 직원 입퇴사	org_no, week1_in, week2_in, week3_in, week4_in, week1_out, week2_out, week3_out, week4_out
	$conn->query($sl[10]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['M']['wek1_in']	= $row['week1_in']; //1주 입사
		$data[$orgNo]['M']['wek1_out']	= $row['week1_out']; //1주 퇴사
		$data[$orgNo]['M']['wek2_in']	= $row['week2_in']; //2주 입사
		$data[$orgNo]['M']['wek2_out']	= $row['week2_out']; //2주 퇴사
		$data[$orgNo]['M']['wek3_in']	= $row['week3_in']; //3주 입사
		$data[$orgNo]['M']['wek3_out']	= $row['week3_out']; //3주 퇴사
		$data[$orgNo]['M']['wek4_in']	= $row['week4_in']; //4주 입사
		$data[$orgNo]['M']['wek4_out']	= $row['week4_out']; //4주 퇴사
	}

	$conn->row_free();


	//$sl[11]= 이월 대상자수	org_no cnt
	$conn->query($sl[11]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		/*
			$data[$orgNo]['C']['ahead_cnt']	= $row['cnt']; //이월 대상자수
		 */
		$jumin = $row['jumin'];

		if (!$tmpArr[$orgNo][$jumin]){
			$tmpIdx[$orgNo][$jumin] = 0;
			$idx = $tmpIdx[$orgNo][$jumin];
			$tmpArr[$orgNo][$jumin][$idx] = Array('fromYm'=>'','toYm'=>'','stat'=>'');
		}

		if (!$tmpArr[$orgNo][$jumin][$idx]['stat']){
			 $tmpArr[$orgNo][$jumin][$idx]['fromYm'] = $row['from_ym'];
			 $tmpArr[$orgNo][$jumin][$idx]['toYm'] = $row['to_ym'];
			 $tmpArr[$orgNo][$jumin][$idx]['stat'] = $row['svc_stat'];
		}else{
			if ($tmpArr[$orgNo][$jumin][$idx]['stat'] == '1'){
				$tmpArr[$orgNo][$jumin][$idx]['stat'] = $row['svc_stat'];
				if ($tmpArr[$orgNo][$jumin][$idx]['fromYm'] > $row['from_ym']) $tmpArr[$orgNo][$jumin][$idx]['fromYm'] = $row['from_ym'];
				if ($tmpArr[$orgNo][$jumin][$idx]['toYm'] < $row['to_ym']) $tmpArr[$orgNo][$jumin][$idx]['toYm'] = $row['to_ym'];
			}else{
				$tmpIdx[$orgNo][$jumin] ++;
				$idx = $tmpIdx[$orgNo][$jumin];
				$tmpArr[$orgNo][$jumin][$idx]['fromYm'] = $row['from_ym'];
				$tmpArr[$orgNo][$jumin][$idx]['toYm'] = $row['to_ym'];
				$tmpArr[$orgNo][$jumin][$idx]['stat'] = $row['svc_stat'];
			}
		}
	}

	$conn->row_free();

	if (is_array($tmpArr)){
		foreach($tmpArr as $orgNo => $R1){
			foreach($R1 as $jumin => $R2){
				foreach($R2 as $idx => $R){
					if ($R['fromYm'] <= $beforeYM && $R['toYm'] >= $beforeYM && $R['toYm'] != $beforeYM){
						$data[$orgNo]['C']['ahead_cnt'] ++;
					}
				}
			}
		}
	}
	Unset($tmpArr);


	//$sl[12]= 당월 대상자 가입해지	org_no week1_in week2_in week3_in week4_in week1_out week2_out week3_out week4_out
	$conn->query($sl[12]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		/*
			$data[$orgNo]['C']['wek1_in']	= $row['week1_in']; //1주 가입고객
			$data[$orgNo]['C']['wek2_in']	= $row['week2_in']; //2주 가입고객
			$data[$orgNo]['C']['wek3_in']	= $row['week3_in']; //3주 가입고객
			$data[$orgNo]['C']['wek4_in']	= $row['week4_in']; //4주 가입고객
			$data[$orgNo]['C']['wek1_out']	= $row['week1_out']; //1주 해지고객
			$data[$orgNo]['C']['wek2_out']	= $row['week2_out']; //2주 해지고객
			$data[$orgNo]['C']['wek3_out']	= $row['week3_out']; //3주 해지고객
			$data[$orgNo]['C']['wek4_out']	= $row['week4_out']; //4주 해지고객
		 */
		$jumin = $row['jumin'];

		if (!$tmpArr[$orgNo][$jumin]){
			$tmpIdx[$orgNo][$jumin] = 0;
			$idx = $tmpIdx[$orgNo][$jumin];
			$tmpArr[$orgNo][$jumin][$idx] = Array('fromDt'=>'','toDt'=>'','stat'=>'');
		}

		if (!$tmpArr[$orgNo][$jumin][$idx]['stat']){
			 $tmpArr[$orgNo][$jumin][$idx]['fromDt'] = $row['from_dt'];
			 $tmpArr[$orgNo][$jumin][$idx]['toDt'] = $row['to_dt'];
			 $tmpArr[$orgNo][$jumin][$idx]['stat'] = $row['svc_stat'];
		}else{
			if ($tmpArr[$orgNo][$jumin][$idx]['stat'] == '1'){
				$tmpArr[$orgNo][$jumin][$idx]['stat'] = $row['svc_stat'];
				if ($tmpArr[$orgNo][$jumin][$idx]['fromDt'] > $row['from_dt']) $tmpArr[$orgNo][$jumin][$idx]['fromDt'] = $row['from_dt'];
				if ($tmpArr[$orgNo][$jumin][$idx]['toDt'] < $row['to_dt']) $tmpArr[$orgNo][$jumin][$idx]['toDt'] = $row['to_dt'];
			}else{
				$tmpIdx[$orgNo][$jumin] ++;
				$idx = $tmpIdx[$orgNo][$jumin];
				$tmpArr[$orgNo][$jumin][$idx]['fromDt'] = $row['from_dt'];
				$tmpArr[$orgNo][$jumin][$idx]['toDt'] = $row['to_dt'];
				$tmpArr[$orgNo][$jumin][$idx]['stat'] = $row['svc_stat'];
			}
		}
	}

	$conn->row_free();

	if (is_array($tmpArr)){
		foreach($tmpArr as $orgNo => $R1){
			foreach($R1 as $jumin => $R2){
				/*
				//일정이 있는 대상자만 카운트를 실행한다.
				$sql = 'SELECT	COUNT(*)
						FROM	t01iljung
						WHERE	t01_ccode	= \''.$orgNo.'\'
						AND		t01_mkind	= \'0\'
						AND		t01_jumin	= \''.$jumin.'\'
						AND		t01_del_yn	= \'N\'
						AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'';

				$tmpIljungCnt = $conn->get_data($sql);

				if ($tmpIljungCnt < 1) continue;
				*/
				//전월청구마감 여부 확인
				$sql = 'SELECT	COUNT(*)
						FROM	t13sugupja
						WHERE	t13_ccode	= \''.$orgNo.'\'
						AND		t13_mkind	= \'0\'
						AND		t13_jumin	= \''.$jumin.'\'
						AND		t13_pay_date= \''.$beforeYM.'\'
						AND		t13_type	= \'2\'';

				$clsCnt = $conn->get_data($sql);

				foreach($R2 as $idx => $R){
					/*
					if (SubStr($R['fromDt'],0,6) == $yymm ){
						$fromDay = IntVal(SubStr($R['fromDt'],6));

						//신규가입
						if ($fromDay >= 1 && $fromDay <= 7){
							$data[$orgNo]['C']['wek1_in'] ++;
						}else if ($fromDay >= 8 && $fromDay <= 14){
							$data[$orgNo]['C']['wek2_in'] ++;
						}else if ($fromDay >= 15 && $fromDay <= 21){
							$data[$orgNo]['C']['wek3_in'] ++;
						}else if ($fromDay >= 22){
							$data[$orgNo]['C']['wek4_in'] ++;
						}
					}
					*/

					if (SubStr($R['fromDt'],0,6) == $yymm ){
						$fromDay = IntVal(SubStr($R['fromDt'],6));

						if ($tmpClsChk[$orgNo][$jumin]['cnt'] > 0){
							//재계약
							if ($fromDay >= 1 && $fromDay <= 7){
								$data[$orgNo]['C']['wek1_re'] ++;
							}else if ($fromDay >= 8 && $fromDay <= 14){
								$data[$orgNo]['C']['wek2_re'] ++;
							}else if ($fromDay >= 15 && $fromDay <= 21){
								$data[$orgNo]['C']['wek3_re'] ++;
							}else if ($fromDay >= 22){
								$data[$orgNo]['C']['wek4_re'] ++;
							}
							/*
							if ($tmpClsChk[$orgNo][$jumin]['day'] >= 1 && $tmpClsChk[$orgNo][$jumin]['day'] <= 7){
								$data[$orgNo]['C']['wek1_out'] --;
							}else if ($tmpClsChk[$orgNo][$jumin]['day'] >= 8 && $tmpClsChk[$orgNo][$jumin]['day'] <= 14){
								$data[$orgNo]['C']['wek2_out'] --;
							}else if ($tmpClsChk[$orgNo][$jumin]['day'] >= 15 && $tmpClsChk[$orgNo][$jumin]['day'] <= 21){
								$data[$orgNo]['C']['wek3_out'] --;
							}else if ($tmpClsChk[$orgNo][$jumin]['day'] >= 22){
								$data[$orgNo]['C']['wek4_out'] --;
							}
							*/
						}else{
							//신규가입
							if ($fromDay >= 1 && $fromDay <= 7){
								$data[$orgNo]['C']['wek1_in'] ++;
							}else if ($fromDay >= 8 && $fromDay <= 14){
								$data[$orgNo]['C']['wek2_in'] ++;
							}else if ($fromDay >= 15 && $fromDay <= 21){
								$data[$orgNo]['C']['wek3_in'] ++;
							}else if ($fromDay >= 22){
								$data[$orgNo]['C']['wek4_in'] ++;
							}
						}
					}

					if (SubStr($R['toDt'],0,6) == $yymm && $R['stat'] == '9'){
						$toDay = IntVal(SubStr($R['toDt'],6));

						if ($clsCnt > 0){
							$tmpClsChk[$orgNo][$jumin]['cnt'] = $clsCnt;
							$tmpClsChk[$orgNo][$jumin]['day'] = $toDay;
						}

						if ($toDay >= 1 && $toDay <= 7){
							$data[$orgNo]['C']['wek1_out'] ++;
						}else if ($toDay >= 8 && $toDay <= 14){
							$data[$orgNo]['C']['wek2_out'] ++;
						}else if ($toDay >= 15 && $toDay <= 21){
							$data[$orgNo]['C']['wek3_out'] ++;
						}else if ($toDay >= 22){
							$data[$orgNo]['C']['wek4_out'] ++;
						}
					}
				}
			}
		}
		Unset($tmpClsChk);
	}


	//$sl[13]= 전월 매출		org_no plan_amt conf_amt
	$conn->query($sl[13]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['A']['ahead_plan'] = $row['plan_amt']; //전월 계획금액
		$data[$orgNo]['A']['ahead_conf'] = $row['conf_amt']; //전월 실적금액
	}

	$conn->row_free();


	//$sl[14]= 당월 매출		org_no plan_amt_w1 plan_amt_w2 plan_amt_w3 plan_amt_w4 conf_amt_w1 conf_amt_w2 conf_amt_w3 conf_amt_w4
	$conn->query($sl[14]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['A']['wek1_plan'] = $row['plan_amt_w1']; //
		$data[$orgNo]['A']['wek2_plan'] = $row['plan_amt_w2']; //
		$data[$orgNo]['A']['wek3_plan'] = $row['plan_amt_w3']; //
		$data[$orgNo]['A']['wek4_plan'] = $row['plan_amt_w4']; //
		$data[$orgNo]['A']['wek1_conf'] = $row['conf_amt_w1']; //
		$data[$orgNo]['A']['wek2_conf'] = $row['conf_amt_w2']; //
		$data[$orgNo]['A']['wek3_conf'] = $row['conf_amt_w3']; //
		$data[$orgNo]['A']['wek4_conf'] = $row['conf_amt_w4']; //
	}

	$conn->row_free();

	/*
	//$sl[15]= 전월 직원수		org_no cnt
	//$sl[16]= 당월 직원 정보	org_no week1_join_cnt week2_join_cnt week3_join_cnt week4_join_cnt week1_quit_cnt week2_quit_cnt week3_quit_cnt week4_quit_cnt week_mem_cnt
	//$sl[17]= 전월 대상자수	org_no cnt
	//$sl[18]= 당월 대상자 정보	org_no tg_cnt week1_in_cnt week2_in_cnt week3_in_cnt week4_in_cnt week1_out_cnt week2_out_cnt week3_out_cnt week4_out_cnt
	*/

	//$sl[19] = 5대보험 회사분 org_no, amt
	$conn->query($sl[19]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['M']['insu_com_amt'] = $row['insu_com_60up'] + $row['insu_com_60down'];
		$data[$orgNo]['M']['insu_com_60up'] = $row['insu_com_60up'];
		$data[$orgNo]['M']['insu_com_60down'] = $row['insu_com_60down'];
	}

	$conn->row_free();

	//$sl[20] 본인부담금 계산 내역	org_no, longterm_amt, expense_amt
	$conn->query($sl[20]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['A']['longterm_amt']	= $row['longterm_amt']; //
		$data[$orgNo]['A']['expense_amt']	= $row['expense_amt']; //
	}

	$conn->row_free();


	//$sl[21] 본인부담금 계산 전월 내역	org_no, longterm_amt, expense_amt
	$conn->query($sl[21]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['A']['ahead_longterm']= $row['longterm_amt']; //
		$data[$orgNo]['A']['ahead_expense']	= $row['expense_amt']; //
	}

	$conn->row_free();


	//직원 근무형태별 급여
	$conn->query($sl[22]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['M']['cnt_60up']	= $row['cnt_60up']; //
		$data[$orgNo]['M']['cnt_60down']= $row['cnt_60down']; //
		$data[$orgNo]['M']['pay_60up']	= $row['pay_60up']; //
		$data[$orgNo]['M']['pay_60down']= $row['pay_60down']; //
	}

	$conn->row_free();


	//대상자 해지사유별
	$conn->query($sl[23]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['end_rsn_01'] = $row['rsn_01']; //계약해지
		$data[$orgNo]['C']['end_rsn_02'] = $row['rsn_02']; //보류
		$data[$orgNo]['C']['end_rsn_03'] = $row['rsn_03']; //사망
		$data[$orgNo]['C']['end_rsn_04'] = $row['rsn_04']; //타기관이전
		$data[$orgNo]['C']['end_rsn_05'] = $row['rsn_05']; //입원
		$data[$orgNo]['C']['end_rsn_06'] = $row['rsn_06']; //등외판정
		$data[$orgNo]['C']['end_rsn_07'] = $row['rsn_07']; //무리한서비스요규
		$data[$orgNo]['C']['end_rsn_08'] = $row['rsn_08']; //단순서비스종료
		$data[$orgNo]['C']['end_rsn_09'] = $row['rsn_09']; //근무자미투입
		$data[$orgNo]['C']['end_rsn_10'] = $row['rsn_10']; //거주지이전
		$data[$orgNo]['C']['end_rsn_11'] = $row['rsn_11']; //건강호전
		$data[$orgNo]['C']['end_rsn_12'] = $row['rsn_12']; //부담금미납
		$data[$orgNo]['C']['end_rsn_13'] = $row['rsn_13']; //지점이동
		$data[$orgNo]['C']['end_rsn_14'] = $row['rsn_14']; //주간보호이동
		$data[$orgNo]['C']['end_rsn_99'] = $row['rsn_99']; //기타
	}

	$conn->row_free();


	//직원 마감인원
	$conn->query($sl[24]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['M']['cls_cnt'] = $row['cnt']; //마감인원
	}

	$conn->row_free();


	//고객 마감인원
	$conn->query($sl[25]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['cls_cnt'] = $row['cnt']; //마감인원
	}

	$conn->row_free();


	//개인간병 마감인원
	$conn->query($sl[26]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['cls_cnt'] = $row['psn_cnt']; //마감인원
	}

	$conn->row_free();


	//계약유형
	$conn->query($sl[27]);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];

		if (!$orgNo) continue;

		$data[$orgNo]['C']['cont_type_01'] = $row['cnt_01']; //전화,인터넷
		$data[$orgNo]['C']['cont_type_02'] = $row['cnt_02']; //직접발굴
		$data[$orgNo]['C']['cont_type_03'] = $row['cnt_03']; //지인소개
		$data[$orgNo]['C']['cont_type_04'] = $row['cnt_04']; //근무자를 통한소개
		$data[$orgNo]['C']['cont_type_05'] = $row['cnt_05']; //공단자료
		$data[$orgNo]['C']['cont_type_06'] = $row['cnt_06']; //외부인수
		$data[$orgNo]['C']['cont_type_07'] = $row['cnt_07']; //간병연계
		$data[$orgNo]['C']['cont_type_08'] = $row['cnt_08']; //지점연계
	}

	$conn->row_free();


	if (is_array($data)){
		//고객정보 삭제
		$query[] = 'DELETE FROM ie_bm_close_client WHERE yymm = \''.$yymm.'\'';

		//직원정보 삭제
		$query[] = 'DELETE FROM ie_bm_close_member WHERE yymm = \''.$yymm.'\'';

		//매출정보 삭제
		$query[] = 'DELETE FROM ie_bm_close_amt WHERE yymm = \''.$yymm.'\'';

		foreach($data as $orgNo => $row){
			//고객정보
			$sql = 'INSERT INTO ie_bm_close_client VALUES (
					 \''.$orgNo.'\'
					,\''.$yymm.'\'
					,\''.$row['C']['cnt'].'\'
					,\''.$row['C']['lv1_cnt'].'\'
					,\''.$row['C']['lv2_cnt'].'\'
					,\''.$row['C']['lv3_cnt'].'\'
					,\''.$row['C']['lv4_cnt'].'\'
					,\''.$row['C']['lv5_cnt'].'\'
					,\''.$row['C']['lv_other'].'\'
					,\''.$row['C']['gbn1_cnt'].'\'
					,\''.$row['C']['gbn2_cnt'].'\'
					,\''.$row['C']['gbn3_cnt'].'\'
					,\''.$row['C']['gbn4_cnt'].'\'
					,\''.$row['C']['gbn_other'].'\'
					,\''.$row['C']['care_nml'].'\'
					,\''.$row['C']['care_f60'].'\'
					,\''.$row['C']['care_f90'].'\'
					,\''.$row['C']['care_cnt'].'\'
					,\''.$row['C']['care_bath_cnt'].'\'
					,\''.$row['C']['bath_cnt'].'\'
					,\''.$row['C']['care_other'].'\'
					,\''.$row['C']['plan_pay'].'\'
					,\''.$row['C']['conf_pay'].'\'
					,\''.$row['C']['ahead_cnt'].'\'
					,\''.$row['C']['wek1_in'].'\'
					,\''.$row['C']['wek1_out'].'\'
					,\''.$row['C']['wek1_re'].'\'
					,\''.$row['C']['wek2_in'].'\'
					,\''.$row['C']['wek2_out'].'\'
					,\''.$row['C']['wek2_re'].'\'
					,\''.$row['C']['wek3_in'].'\'
					,\''.$row['C']['wek3_out'].'\'
					,\''.$row['C']['wek3_re'].'\'
					,\''.$row['C']['wek4_in'].'\'
					,\''.$row['C']['wek4_out'].'\'
					,\''.$row['C']['wek4_re'].'\'
					,\''.$row['C']['end_rsn_01'].'\'
					,\''.$row['C']['end_rsn_02'].'\'
					,\''.$row['C']['end_rsn_03'].'\'
					,\''.$row['C']['end_rsn_04'].'\'
					,\''.$row['C']['end_rsn_05'].'\'
					,\''.$row['C']['end_rsn_06'].'\'
					,\''.$row['C']['end_rsn_07'].'\'
					,\''.$row['C']['end_rsn_08'].'\'
					,\''.$row['C']['end_rsn_09'].'\'
					,\''.$row['C']['end_rsn_10'].'\'
					,\''.$row['C']['end_rsn_11'].'\'
					,\''.$row['C']['end_rsn_12'].'\'
					,\''.$row['C']['end_rsn_13'].'\'
					,\''.$row['C']['end_rsn_14'].'\'
					,\''.$row['C']['end_rsn_99'].'\'
					,\''.$row['C']['cls_cnt'].'\'
					,\''.$row['C']['cont_type_01'].'\'
					,\''.$row['C']['cont_type_02'].'\'
					,\''.$row['C']['cont_type_03'].'\'
					,\''.$row['C']['cont_type_04'].'\'
					,\''.$row['C']['cont_type_05'].'\'
					,\''.$row['C']['cont_type_06'].'\'
					,\''.$row['C']['cont_type_07'].'\'
					,\''.$row['C']['cont_type_08'].'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
			)';
			$query[] = $sql;

			//직원정보
			$sql = 'INSERT INTO ie_bm_close_member VALUES (
					 \''.$orgNo.'\'
					,\''.$yymm.'\'
					,\''.$row['M']['cnt'].'\'
					,\''.$row['M']['wrk_nml'].'\'
					,\''.$row['M']['wrk_60up'].'\'
					,\''.$row['M']['wrk_60down'].'\'
					,\''.$row['M']['cnt_60up'].'\'
					,\''.$row['M']['cnt_60down'].'\'
					,\''.$row['M']['pay_60up'].'\'
					,\''.$row['M']['pay_60down'].'\'
					,\''.$row['M']['year_down'].'\'
					,\''.$row['M']['year_up'].'\'
					,\''.$row['M']['re_n_cnt'].'\'
					,\''.$row['M']['re_n_amt'].'\'
					,\''.$row['M']['re_y_cnt'].'\'
					,\''.$row['M']['re_y_amt'].'\'
					,\''.$row['M']['re_cnt'].'\'
					,\''.$row['M']['re_cnt_60up'].'\'
					,\''.$row['M']['re_cnt_60down'].'\'
					,\''.$row['M']['re_amt'].'\'
					,\''.$row['M']['re_amt_60up'].'\'
					,\''.$row['M']['re_amt_60down'].'\'
					,\''.$row['M']['re_saved_cnt'].'\'
					,\''.$row['M']['re_saved_amt'].'\'
					,\''.$row['M']['insu_com_amt'].'\'
					,\''.$row['M']['insu_com_60up'].'\'
					,\''.$row['M']['insu_com_60down'].'\'
					,\''.$row['M']['ahead_cnt'].'\'
					,\''.$row['M']['wek1_in'].'\'
					,\''.$row['M']['wek1_out'].'\'
					,\''.$row['M']['wek2_in'].'\'
					,\''.$row['M']['wek2_out'].'\'
					,\''.$row['M']['wek3_in'].'\'
					,\''.$row['M']['wek3_out'].'\'
					,\''.$row['M']['wek4_in'].'\'
					,\''.$row['M']['wek4_out'].'\'
					,\''.$row['M']['cls_cnt'].'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
			$query[] = $sql;

			//매출정보
			$sql = 'INSERT INTO ie_bm_close_amt VALUES (
					 \''.$orgNo.'\'
					,\''.$yymm.'\'
					,\''.$row['A']['ahead_plan'].'\'
					,\''.$row['A']['ahead_conf'].'\'
					,\''.$row['A']['ahead_longterm'].'\'
					,\''.$row['A']['ahead_expense'].'\'
					,\''.$row['A']['wek1_plan'].'\'
					,\''.$row['A']['wek1_conf'].'\'
					,\''.$row['A']['wek2_plan'].'\'
					,\''.$row['A']['wek2_conf'].'\'
					,\''.$row['A']['wek3_plan'].'\'
					,\''.$row['A']['wek3_conf'].'\'
					,\''.$row['A']['wek4_plan'].'\'
					,\''.$row['A']['wek4_conf'].'\'
					,\''.$row['A']['longterm_amt'].'\'
					,\''.$row['A']['expense_amt'].'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
			$query[] = $sql;
		}
	}

	Unset($sl);
	Unset($data);

	if ($debug){
		print_r($query);
	}else{
		if (is_array($query)){
			$conn->begin();

			foreach($query as $sql){
				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 if ($debug) echo $conn->error_msg;
					 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
					 exit;
				}
			}

			$conn->commit();
			//echo 1;
		}
	}

	Unset($query);

	include_once('../inc/_db_close.php');
?>