<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$stat = $_POST['stat'];

	$yymm = $year;

	if ($month != 'A'){
		$yymm .= (IntVal($month) < 10 ? '0' : '').IntVal($month);
	}

	$sql = 'SELECT	DISTINCT
					insu.jumin
			,		m02_yname AS name
			,		seq
			,		annuity_yn
			,		health_yn
			,		employ_yn
			,		sanje_yn
			,		from_dt
			,		to_dt
			,		from_stat
			,		to_stat
			,		from_idx
			,		to_idx
			FROM	(';

	if ($stat != '3'){
		$sql .= '	SELECT	org_no
					,		jumin
					,		seq
					,		annuity_yn
					,		health_yn
					,		employ_yn
					,		sanje_yn
					,		from_dt
					,		to_dt
					,		from_stat
					,		to_stat
					,		CASE WHEN LEFT(from_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END AS from_idx
					,		CASE WHEN LEFT(to_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END AS to_idx
					FROM	mem_insu
					WHERE	org_no = \''.$code.'\'
					AND		CASE WHEN LEFT(from_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END + CASE WHEN LEFT(to_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END > 0';

		if ($stat == '1'){
			$sql .= '
					AND		from_stat = \'1\'
					AND		to_stat = \'9\'';
		}else if ($stat == '9'){
			$sql .= '
					AND		from_stat = \'9\'
					AND		to_stat = \'9\'';
		}else{
		}
	}

	if ($stat == 'ALL' || $stat == '9'){
		$sql .= '	UNION	ALL';
	}

	if ($stat != '1'){
		$sql .= '	SELECT	org_no
					,		jumin
					,		seq
					,		annuity_yn
					,		health_yn
					,		employ_yn
					,		sanje_yn
					,		from_dt
					,		to_dt
					,		from_stat
					,		to_stat
					,		CASE WHEN LEFT(from_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END AS from_idx
					,		CASE WHEN LEFT(to_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END AS to_idx
					FROM	mem_insu
					WHERE	org_no = \''.$code.'\'
					AND		CASE WHEN LEFT(from_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END + CASE WHEN LEFT(to_dt,'.StrLen($yymm).') = \''.$yymm.'\' THEN 1 ELSE 0 END > 0';

		if ($stat == '3'){
			$sql .= '
					AND		to_stat = \'1\'';
		}else if ($stat == '9'){
			$sql .= '
					AND		from_stat = \'9\'
					AND		to_stat = \'9\'';
		}else{
			$sql .= '
					AND		to_dt <= \''.$today.'\'';
		}
	}

	$sql .= '		) AS insu
			INNER	JOIN	m02yoyangsa
					ON		m02_ccode	= insu.org_no
					AND		m02_yjumin	= insu.jumin
			ORDER	BY name,jumin,seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$today  = Date('Ymd');

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['to_stat'] == '1' && $row['to_idx'] > 0){
			$gbn = '3';
		}else if ($row['from_stat'] == '1' && $row['from_idx'] > 0){
			$gbn = '1';
		}else{
			$gbn = '9';
		}

		$lbAdd = false;

		$data .= 'name='.$row['name'];
		$data .= '&a='.$row['annuity_yn'];
		$data .= '&h='.$row['health_yn'];
		$data .= '&e='.$row['employ_yn'];
		$data .= '&s='.$row['sanje_yn'];
		$data .= '&f='.$row['from_dt'];

		if ($gbn == '3'){
			$data .= '&t='.$row['to_dt'];
		}else{
			if ($row['to_dt'] <= $today){
				$data .= '&t='.$row['to_dt'];
			}else{
				$data .= '&t=';
			}
		}

		$data .= '&stat='.$gbn;
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>