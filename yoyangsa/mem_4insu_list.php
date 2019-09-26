<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$today = Date('Ymd');

	$sql = 'SELECT	DISTINCT
					jumin
			,		m02_yname AS name
			,		seq
			,		annuity_yn
			,		health_yn
			,		employ_yn
			,		sanje_yn
			,		paye_yn
			,		from_dt
			,		to_dt
			FROM	mem_insu
			INNER	JOIN	m02yoyangsa
					ON		m02_ccode = org_no
					AND		m02_yjumin = jumin
					AND		m02_del_yn = \'N\'
			WHERE	org_no	 = \''.$code.'\'
			AND		from_dt <= \''.$today.'\'
			AND		to_dt	>= \''.$today.'\'
			ORDER	BY name,jumin,seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		#국민연금 월보수액
		$sql = 'SELECT	monthly
				FROM	mem_insu_monthly
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$row['jumin'].'\'
				AND     yymm    <= \''.substr($today, 0, 8).'\'
				ORDER	BY yymm DESC
				LIMIT	1';
		$monthly = $conn -> get_data($sql); 

		$data .= 'name='.$row['name'];
		$data .= '&jumin='.$ed->en($row['jumin']);
		$data .= '&seq='.$row['seq'];
		$data .= '&a='.$row['annuity_yn'];
		$data .= '&h='.$row['health_yn'];
		$data .= '&e='.$row['employ_yn'];
		$data .= '&s='.$row['sanje_yn'];
		$data .= '&p='.$row['paye_yn'];
		$data .= '&f='.$row['from_dt'];
		$data .= '&t='.$row['to_dt'];
		$data .= '&pay='.$monthly;
		$data .= chr(11);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>