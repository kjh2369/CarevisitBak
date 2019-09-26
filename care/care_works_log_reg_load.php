<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$date	= $_POST['date'];
	$jumin	= $ed->de($_POST['jumin']);
	$sugaCd	= $_POST['suga'];
	$resCd	= $_POST['res'];
	$memCd	= $ed->de($_POST['mem']);
	
	

	//대상자 정보
	//성명
	$sql = 'SELECT	m03_name
			,		m03_key
			,		m03_tel
			,		m03_hp
			,		m03_yboho_phone
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'6\'
			AND		m03_jumin = \''.$jumin.'\'';

	$row = $conn->get_array($sql);

	$data = 'name='.$row['m03_name'];
	$IPIN = $row['m03_key'];
	
	$data  .= '&tel='.($row['m03_hp']!=''? $myF->phoneStyle($row['m03_hp'],'.').' / ' : $myF->phoneStyle($row['m03_tel'],'.').' / ').($row['m03_yboho_phone']!=''? $myF->phoneStyle($row['m03_tel'],'.') : ''); 

	Unset($row);
	
	$sql = 'select jumin 
			from   mst_jumin
			where  org_no = \''.$orgNo.'\'
			and    code = \''.$jumin.'\''; 
	$jumin = $conn -> get_data($sql); 
	
	//성별
	if (SubStr($jumin,6,1)){
		if ((SubStr($jumin,6,1) % 2) == 1){
			$data .= '&gender=남';
		}else{
			$data .= '&gender=여';
		}
	}else{
		$data .= '&gender=';
	}

	//생년월일
	$data .= '&birthday='.$myF->issToBirthday($jumin,'.');

	//서비스
	$sql = 'SELECT	suga_nm
			FROM	care_suga
			WHERE	org_no	= \''.$orgNo.'\'
			AND		suga_sr = \''.$SR.'\'
			AND		CONCAT(suga_cd, suga_sub) = \''.$sugaCd.'\'
			AND		REPLACE(from_dt,\'-\',\'\') <= \''.$date.'\'
			AND		REPLACE(to_dt,\'-\',\'\')	>= \''.$date.'\'';

	$data .= '&service='.$conn->get_data($sql);

	//자원
	$sql = 'SELECT	cust_nm
			FROM	care_cust
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cust_cd = \''.$resCd.'\'';

	$data .= '&resource='.$conn->get_data($sql);

	//당당자
	$sql = 'SELECT	DISTINCT m02_yname
			FROM	m02yoyangsa
			WHERE	m02_ccode	= \''.$orgNo.'\'
			AND		m02_yjumin	= \''.$memCd.'\'';

	$data .= '&mem='.$conn->get_data($sql);

	echo $data;

	include_once('../inc/_db_close.php');
?>