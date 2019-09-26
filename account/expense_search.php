<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$type = $_POST['type'];
	$year = $_POST['year'];
	$month = $_POST['month'];

	if ($type == 'YEAR'){
		$sql = 'SELECT	SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'01\' THEN 1 ElSE 0 END) AS m01
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'02\' THEN 1 ElSE 0 END) AS m02
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'03\' THEN 1 ElSE 0 END) AS m03
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'04\' THEN 1 ElSE 0 END) AS m04
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'05\' THEN 1 ElSE 0 END) AS m05
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'06\' THEN 1 ElSE 0 END) AS m06
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'07\' THEN 1 ElSE 0 END) AS m07
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'08\' THEN 1 ElSE 0 END) AS m08
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'09\' THEN 1 ElSE 0 END) AS m09
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'10\' THEN 1 ElSE 0 END) AS m10
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'11\' THEN 1 ElSE 0 END) AS m11
				,		SUM(CASE SUBSTR(t13_pay_date,5) WHEN \'12\' THEN 1 ElSE 0 END) AS m12

				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'01\' THEN 1 ELSE 0 END) AS i01
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'02\' THEN 1 ELSE 0 END) AS i02
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'03\' THEN 1 ELSE 0 END) AS i03
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'04\' THEN 1 ELSE 0 END) AS i04
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'05\' THEN 1 ELSE 0 END) AS i05
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'06\' THEN 1 ELSE 0 END) AS i06
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'07\' THEN 1 ELSE 0 END) AS i07
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'08\' THEN 1 ELSE 0 END) AS i08
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'09\' THEN 1 ELSE 0 END) AS i09
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'10\' THEN 1 ELSE 0 END) AS i10
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'11\' THEN 1 ELSE 0 END) AS i11
				,		SUM(CASE WHEN SUBSTR(unpaid_yymm,5) = \'12\' THEN 1 ELSE 0 END) AS i12
				FROM	client_family
				INNER	JOIN	t13sugupja
						ON		t13_ccode = client_family.org_no
						AND		t13_mkind = \'0\'
						AND		t13_jumin = client_family.cf_jumin
						AND		t13_type  = \'2\'
						AND		LEFT(t13_pay_date,4) = \''.$year.'\'
						AND		t13_bonbu_tot4 > 0
				INNER	JOIN	salary_basic
						ON		salary_basic.org_no			= client_family.org_no
						AND		salary_basic.salary_yymm	= t13_pay_date
						AND		salary_basic.salary_jumin	= client_family.cf_mem_cd
				LEFT	JOIN	unpaid_auto_list
						ON		unpaid_auto_list.org_no			= client_family.org_no
						AND		unpaid_auto_list.unpaid_yymm	= t13_pay_date
						AND		unpaid_auto_list.unpaid_jumin	= client_family.cf_mem_cd
				WHERE	client_family.org_no = \''.$code.'\'';

		$mon = $conn->get_array($sql);

		for($i=1; $i<=12; $i++){
			if ($data){
				$data .= '&';
			}

			if ($mon['i'.($i<10?'0':'').$i] > 0){
				$gbn = '1';
			}else if ($mon['m'.($i<10?'0':'').$i] > 0){
				$gbn = '0';
			}else{
				$gbn = '';
			}

			$data	.= $i.'='.$gbn;
		}

		echo $data;

	}else if ($type == 'LIST'){
		$sql = 'SELECT	CONCAT(deposit_jumin,\'_\',deposit_mem) AS cd
				,		deposit_ent_dt AS ent_dt
				,		deposit_seq AS ent_seq
				FROM	unpaid_deposit
				WHERE	org_no		 = \''.$code.'\'
				AND		deposit_yymm = \''.$year.$month.'\'
				AND		deposit_auto = \'Y\'
				AND		del_flag	 = \'N\'';
		$list = $conn->_fetch_array($sql,'cd');

		$sql = 'SELECT	DISTINCT
						cf_mem_cd AS mem_cd
				,		m02_yname AS mem_nm
				,		m02_family_pay_yn AS pay_yn
				,		cf_jumin AS clt_cd
				,		m03_name AS clt_nm
				/*
				,		t13_bonin_amt4 AS expense_pay
				,		t13_over_amt4 AS over_pay
				,		t13_bipay4 AS bipay
				,		t13_bonbu_tot4 AS tot_pay
				*/
				,		a.expense_pay
				,		a.over_pay
				,		a.bipay
				,		a.tot_pay
				,		IFNULL(unpaid_amt,0) AS amt
				FROM	client_family
				INNER	JOIN	m02yoyangsa
						ON		m02_ccode	= client_family.org_no
						AND		m02_mkind	= \'0\'
						AND		m02_yjumin	= client_family.cf_mem_cd
				INNER	JOIN	m03sugupja
						ON		m03_ccode	= client_family.org_no
						AND		m03_mkind	= \'0\'
						AND		m03_jumin	= client_family.cf_jumin
				/*
				INNER	JOIN	t13sugupja
						ON		t13_ccode		= client_family.org_no
						AND		t13_mkind		= \'0\'
						AND		t13_jumin		= client_family.cf_jumin
						AND		t13_type		= \'2\'
						AND		t13_pay_date	= \''.$year.$month.'\'
						AND		t13_bonbu_tot4	> 0
				*/
				INNER	JOIN	(
						SELECT	t13_jumin AS jumin
						,		SUM(t13_bonin_amt4) AS expense_pay
						,		SUM(t13_over_amt4) AS over_pay
						,		SUM(t13_bipay4) AS bipay
						,		SUM(t13_bonbu_tot4) AS tot_pay
						FROM	t13sugupja
						WHERE	t13_ccode	= \''.$code.'\'
						AND		t13_mkind	= \'0\'
						AND		t13_pay_date= \''.$year.$month.'\'
						AND		t13_type	= \'2\'
						AND		t13_bonbu_tot4 > 0
						GROUP	BY t13_jumin
						) AS a
						ON		a.jumin = client_family.cf_jumin
				INNER	JOIN	salary_basic
						ON		salary_basic.org_no			= client_family.org_no
						AND		salary_basic.salary_yymm	= \''.$year.$month.'\'
						AND		salary_basic.salary_jumin	= client_family.cf_mem_cd
				INNER	JOIN	t01iljung
						ON		t01_ccode				= client_family.org_no
						AND		t01_mkind				= \'0\'
						AND		t01_jumin				= client_family.cf_jumin
						/*AND		t01_yoyangsa_id1		= client_family.cf_mem_cd*/
						AND		t01_toge_umu			= \'Y\'
						AND		LEFT(t01_sugup_date,6)	= \''.$year.$month.'\'
				LEFT	JOIN	unpaid_auto_list
						ON		unpaid_auto_list.org_no			= client_family.org_no
						AND		unpaid_auto_list.unpaid_yymm	= \''.$year.$month.'\'
						AND		unpaid_auto_list.unpaid_jumin	= client_family.cf_mem_cd
				WHERE	client_family.org_no = \''.$code.'\'
				ORDER	BY mem_nm, mem_cd, clt_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['amt'] > $row['tot_pay']){
				$row['amt'] = $row['tot_pay'];
			}

			$data	.= 'memCd='.$ed->en64($row['mem_cd']);
			$data	.= '&memNm='.$row['mem_nm'];
			$data	.= '&payYn='.$row['pay_yn'];
			$data	.= '&cltCd='.$ed->en64($row['clt_cd']);
			$data	.= '&cltNm='.$row['clt_nm'];
			$data	.= '&expensePay='.$row['expense_pay'];
			$data	.= '&overPay='.$row['over_pay'];
			$data	.= '&bipay='.$row['bipay'];
			$data	.= '&totPay='.$row['tot_pay'];
			$data	.= '&amt='.$row['amt'];
			$data	.= '&entDt='.$list[$row['clt_cd'].'_'.$row['mem_cd']]['ent_dt'];
			$data	.= '&entSeq='.$list[$row['clt_cd'].'_'.$row['mem_cd']]['ent_seq'];
			$data	.= chr(13);
		}

		$conn->row_free();

		echo $data;

	}else if ($type == 'CLOSE'){
		echo $conn->_isCloseSalary($code,$year.$month);

	}else{

	}

	include_once('../inc/_db_close.php');
?>